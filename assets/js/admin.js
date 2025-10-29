/**
 * JavaScript del panel administrativo - MTZ Slider
 */

(function($) {
  "use strict";

  const MTZSlider = {
    images: [],
    currentImageId: null,

    init: function() {
      this.bindEvents();
      this.loadImages();
    },

    bindEvents: function() {
      $("#mtz-add-images").on("click", this.openMediaLibrary.bind(this));
      $("#mtz-save-changes").on("click", this.saveChanges.bind(this));
      $(".mtz-modal-close, .mtz-modal-cancel").on(
        "click",
        this.closeModal.bind(this)
      );
      $("#mtz-image-form").on("submit", this.saveImageForm.bind(this));
      $(document).on("click", ".mtz-slider-edit", this.editImage.bind(this));
      $(document).on(
        "click",
        ".mtz-slider-delete",
        this.deleteImage.bind(this)
      );
      $(document).on("click", "#mtz-loading", this.closeLoading.bind(this));

      // Toggle panel de ayuda
      $("#mtz-toggle-help").on("click", this.toggleHelp.bind(this));

      // Sortable
      if ($.fn.sortable) {
        $("#mtz-slider-grid").sortable({
          items: ".mtz-slider-image-item",
          handle: ".mtz-slider-image-overlay",
          opacity: 0.7,
          placeholder: "mtz-slider-placeholder",
          tolerance: "pointer",
          stop: function(event, ui) {
            MTZSlider.updateSortOrder();
          }
        });
      }
    },

    loadImages: function() {
      $.ajax({
        url: mtzSlider.apiUrl + "images",
        method: "GET",
        beforeSend: function(xhr) {
          xhr.setRequestHeader("X-WP-Nonce", mtzSlider.nonce);
        },
        success: function(response) {
          MTZSlider.images = response;
          MTZSlider.renderImages();
        },
        error: function() {
          MTZSlider.showNotice(mtzSlider.strings.error, "error");
        }
      });
    },

    openMediaLibrary: function() {
      const mediaUploader = wp.media({
        title: mtzSlider.strings.selectImages,
        button: {
          text: mtzSlider.strings.addImages
        },
        multiple: true
      });

      mediaUploader.on("select", function() {
        const attachments = mediaUploader.state().get("selection").toJSON();

        attachments.forEach(function(attachment) {
          const imageData = {
            image_id: attachment.id,
            image_url: attachment.url,
            image_title: attachment.title || "",
            image_alt: attachment.alt || "",
            image_description: attachment.caption || "",
            sort_order: MTZSlider.images.length,
            is_active: 1
          };

          MTZSlider.insertImage(imageData);
        });
      });

      mediaUploader.open();
    },

    insertImage: function(imageData) {
      $.ajax({
        url: mtzSlider.apiUrl + "images",
        method: "POST",
        data: JSON.stringify(imageData),
        contentType: "application/json",
        beforeSend: function(xhr) {
          xhr.setRequestHeader("X-WP-Nonce", mtzSlider.nonce);
        },
        success: function(response) {
          imageData.id = response.id;
          MTZSlider.images.push(imageData);
          MTZSlider.renderImages();
        },
        error: function() {
          MTZSlider.showNotice(mtzSlider.strings.error, "error");
        }
      });
    },

    renderImages: function() {
      const $grid = $("#mtz-slider-grid");

      if (MTZSlider.images.length === 0) {
        $grid.html(`
                    <div class="mtz-slider-empty-state">
                        <span class="dashicons dashicons-images-alt2"></span>
                        <p>${mtzSlider.strings.emptyState ||
                          "No hay imágenes en el slider."}</p>
                    </div>
                `);
        return;
      }

      let html = '<div class="mtz-slider-images-list">';

      MTZSlider.images.forEach(function(image, index) {
        html += `
                    <div class="mtz-slider-image-item" data-id="${image.id}" data-index="${index}">
                        <img src="${image.image_url}" alt="${image.image_alt ||
          ""}" />
                        <div class="mtz-slider-image-overlay">
                            <div class="mtz-slider-image-actions">
                                <button class="mtz-slider-edit" data-id="${image.id}">
                                    <span class="dashicons dashicons-edit"></span>
                                </button>
                                <button class="mtz-slider-delete" data-id="${image.id}">
                                    <span class="dashicons dashicons-trash"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
      });

      html += "</div>";
      $grid.html(html);
    },

    editImage: function(e) {
      e.preventDefault();
      const id = $(e.currentTarget).data("id");
      const image = MTZSlider.images.find(img => img.id == id);

      if (!image) return;

      MTZSlider.currentImageId = id;
      $("#mtz-image-id").val(image.id);
      $("#mtz-image-url").val(image.image_url);
      $("#mtz-image-title").val(image.image_title || "");
      $("#mtz-image-alt").val(image.image_alt || "");
      $("#mtz-image-description").val(image.image_description || "");
      $("#mtz-image-active").prop("checked", image.is_active == 1);

      $("#mtz-image-modal").show();
    },

    deleteImage: function(e) {
      e.preventDefault();

      if (!confirm("¿Estás seguro de eliminar esta imagen?")) {
        return;
      }

      const id = $(e.currentTarget).data("id");

      $.ajax({
        url: mtzSlider.apiUrl + "images/" + id,
        method: "DELETE",
        beforeSend: function(xhr) {
          xhr.setRequestHeader("X-WP-Nonce", mtzSlider.nonce);
        },
        success: function() {
          MTZSlider.images = MTZSlider.images.filter(img => img.id != id);
          MTZSlider.renderImages();
          MTZSlider.showNotice(mtzSlider.strings.saved, "success");
        },
        error: function() {
          MTZSlider.showNotice(mtzSlider.strings.error, "error");
        }
      });
    },

    saveImageForm: function(e) {
      e.preventDefault();

      const id = MTZSlider.currentImageId;
      const image = MTZSlider.images.find(img => img.id == id);

      if (!image) return;

      const data = {
        image_title: $("#mtz-image-title").val(),
        image_alt: $("#mtz-image-alt").val(),
        image_description: $("#mtz-image-description").val(),
        is_active: $("#mtz-image-active").is(":checked") ? 1 : 0
      };

      $.ajax({
        url: mtzSlider.apiUrl + "images/" + id,
        method: "PUT",
        data: JSON.stringify(data),
        contentType: "application/json",
        beforeSend: function(xhr) {
          xhr.setRequestHeader("X-WP-Nonce", mtzSlider.nonce);
        },
        success: function() {
          $.extend(image, data);
          MTZSlider.renderImages();
          MTZSlider.closeModal();
          MTZSlider.showNotice(mtzSlider.strings.saved, "success");
        },
        error: function() {
          MTZSlider.showNotice(mtzSlider.strings.error, "error");
        }
      });
    },

    updateSortOrder: function() {
      const ids = $(".mtz-slider-image-item")
        .map(function() {
          return $(this).data("id");
        })
        .get();

      $.ajax({
        url: mtzSlider.apiUrl + "images/order",
        method: "POST",
        data: JSON.stringify({ images: ids }),
        contentType: "application/json",
        beforeSend: function(xhr) {
          xhr.setRequestHeader("X-WP-Nonce", mtzSlider.nonce);
        },
        error: function() {
          MTZSlider.showNotice(mtzSlider.strings.error, "error");
        }
      });
    },

    closeModal: function() {
      $("#mtz-image-modal").hide();
      $("#mtz-image-form")[0].reset();
      MTZSlider.currentImageId = null;
    },

    showLoading: function() {
      $("#mtz-loading").show();
    },

    closeLoading: function() {
      $("#mtz-loading").hide();
    },

    showNotice: function(message, type) {
      const $notice = $("#mtz-notice");
      $notice.removeClass("success error").addClass(type);
      $notice.text(message);

      setTimeout(function() {
        $notice.removeClass("success error");
        $notice.text("");
      }, 3000);
    },

    saveChanges: function() {
      MTZSlider.showNotice(mtzSlider.strings.saved, "success");
    },

    toggleHelp: function() {
      const $helpContent = $(".mtz-help-content");
      const $helpToggle = $(".mtz-help-toggle");

      $helpContent.slideToggle(300);
      $helpToggle.toggleClass("active");
    }
  };

  // Inicializar cuando el documento esté listo
  $(document).ready(function() {
    MTZSlider.init();
  });
})(jQuery);
