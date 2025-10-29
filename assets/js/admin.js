/**
 * JavaScript del panel administrativo - MTZ Slider
 */

(function($) {
  "use strict";

  const MTZSlider = {
    images: [],
    currentImageId: null,
    currentSliderId: null,

    init: function() {
      const sliderId = $("#mtz-current-slider-id").val();
      this.currentSliderId = sliderId;

      this.bindEvents();

      if (sliderId) {
        this.loadImages(sliderId);
      }
    },

    bindEvents: function() {
      // Eventos de imágenes
      $("#mtz-add-images").on("click", this.openMediaLibrary.bind(this));
      $("#mtz-save-changes").on("click", this.saveChanges.bind(this));
      $("#mtz-image-form").on("submit", this.saveImageForm.bind(this));

      // Eventos de sliders
      $("#mtz-create-slider").on("click", this.openSliderModal.bind(this));
      $("#mtz-slider-form").on("submit", this.saveSlider.bind(this));
      $(".mtz-slider-item").on("click", this.selectSlider.bind(this));
      $(".mtz-slider-item-delete").on("click", this.deleteSlider.bind(this));
      $(document).on(
        "click",
        ".mtz-copy-shortcode",
        this.copyShortcode.bind(this)
      );

      // Eventos modales
      $(".mtz-modal-close, .mtz-modal-cancel").on(
        "click",
        this.closeModal.bind(this)
      );
      $(document).on("click", ".mtz-slider-edit", this.editImage.bind(this));
      $(document).on(
        "click",
        ".mtz-slider-delete",
        this.deleteImage.bind(this)
      );

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

    // ============ MÉTODOS PARA SLIDERS ============

    openSliderModal: function() {
      $("#mtz-modal-title").text("Crear Nuevo Slider");
      $("#mtz-slider-form")[0].reset();
      $("#mtz-slider-id").val("");
      $("#mtz-slider-modal").show();
    },

    saveSlider: function(e) {
      e.preventDefault();

      const sliderId = $("#mtz-slider-id").val();
      const name = $("#mtz-slider-name").val();
      const autoplay = $("#mtz-slider-autoplay").is(":checked") ? 1 : 0;
      const speed = $("#mtz-slider-speed").val();

      console.log("Guardando slider:", { sliderId, name, autoplay, speed });

      if (!name) {
        this.showNotice("Por favor ingresa un nombre para el slider", "error");
        return;
      }

      const url = sliderId
        ? mtzSlider.apiUrl + "sliders/" + sliderId
        : mtzSlider.apiUrl + "sliders";
      const data = {
        name: name,
        autoplay: autoplay,
        speed: speed
      };

      if (sliderId) {
        data.id = sliderId;
      }

      console.log("URL:", url);
      console.log("Data:", data);

      $.ajax({
        url: url,
        method: sliderId ? "PUT" : "POST",
        data: JSON.stringify(data),
        contentType: "application/json",
        beforeSend: function(xhr) {
          xhr.setRequestHeader("X-WP-Nonce", mtzSlider.nonce);
        },
        success: function(response) {
          console.log("Success:", response);
          MTZSlider.showNotice("Slider guardado correctamente", "success");
          $("#mtz-slider-modal").hide();

          setTimeout(function() {
            location.reload();
          }, 1000);
        },
        error: function(xhr, status, error) {
          console.error(
            "Error al guardar slider:",
            xhr.status,
            xhr.responseText
          );
          let errorMsg = "Error al guardar el slider";
          if (xhr.responseText) {
            try {
              const errorData = JSON.parse(xhr.responseText);
              errorMsg = errorData.message || errorMsg;
            } catch (e) {
              errorMsg = xhr.responseText;
            }
          }
          MTZSlider.showNotice(errorMsg, "error");
        }
      });
    },

    selectSlider: function(e) {
      const sliderId = $(e.currentTarget)
        .closest(".mtz-slider-item")
        .data("slider-id");

      if (sliderId) {
        window.location.href =
          window.location.href.split("?")[0] +
          "?page=mtz-slider&slider=" +
          sliderId;
      }
    },

    deleteSlider: function(e) {
      e.stopPropagation();

      if (
        !confirm(
          "¿Estás seguro de eliminar este slider? Esta acción no se puede deshacer."
        )
      ) {
        return;
      }

      const sliderId = $(e.currentTarget).data("id");

      $.ajax({
        url: mtzSlider.apiUrl + "sliders/" + sliderId,
        method: "DELETE",
        beforeSend: function(xhr) {
          xhr.setRequestHeader("X-WP-Nonce", mtzSlider.nonce);
        },
        success: function() {
          MTZSlider.showNotice("Slider eliminado correctamente", "success");
          setTimeout(function() {
            location.reload();
          }, 1000);
        },
        error: function() {
          MTZSlider.showNotice("Error al eliminar el slider", "error");
        }
      });
    },

    copyShortcode: function(e) {
      e.stopPropagation();
      e.preventDefault();

      const shortcode = $(e.currentTarget).data("shortcode");
      const $button = $(e.currentTarget);
      
      // Intentar usar la Clipboard API moderna
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard
          .writeText(shortcode)
          .then(() => {
            // Copiado exitosamente
            const originalHTML = $button.html();
            $button.html('<i data-lucide="check"></i>');
            $button.css("color", "#00a32a");
            
            // Reinicializar Lucide para el nuevo icono
            if (typeof lucide !== 'undefined') {
              lucide.createIcons();
            }

            setTimeout(function() {
              $button.html(originalHTML);
              $button.css("color", "");
              // Reinicializar Lucide para restaurar el icono original
              if (typeof lucide !== 'undefined') {
                lucide.createIcons();
              }
            }, 2000);
          })
          .catch((err) => {
            console.error('Error al copiar:', err);
            this.showNotice("Error al copiar el shortcode", "error");
          });
      } else {
        // Fallback para navegadores antiguos
        const tempInput = $("<input>");
        $("body").append(tempInput);
        tempInput.val(shortcode).select();
        document.execCommand("copy");
        tempInput.remove();

        const originalHTML = $button.html();
        $button.html('<i data-lucide="check"></i>');
        $button.css("color", "#00a32a");
        
        // Reinicializar Lucide para el nuevo icono
        if (typeof lucide !== 'undefined') {
          lucide.createIcons();
        }

        setTimeout(function() {
          $button.html(originalHTML);
          $button.css("color", "");
          // Reinicializar Lucide para restaurar el icono original
          if (typeof lucide !== 'undefined') {
            lucide.createIcons();
          }
        }, 2000);
      }
    },

    // ============ MÉTODOS PARA IMÁGENES ============

    loadImages: function(sliderId) {
      $.ajax({
        url: mtzSlider.apiUrl + "sliders/" + sliderId + "/images",
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
            slider_id: MTZSlider.currentSliderId,
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
          MTZSlider.updateImageCount(); // Actualizar contador en la lista
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
            <img src="${image.image_url}" alt="${image.image_alt || ""}" />
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
          MTZSlider.updateImageCount(); // Actualizar contador
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
      $(".mtz-modal").hide();

      // Reset forms si existen
      const imageForm = $("#mtz-image-form")[0];
      const sliderForm = $("#mtz-slider-form")[0];

      if (imageForm) imageForm.reset();
      if (sliderForm) sliderForm.reset();

      MTZSlider.currentImageId = null;
    },

    showNotice: function(message, type) {
      let $notice = $("#mtz-notice");

      // Si no existe el contenedor, crear uno temporal
      if ($notice.length === 0) {
        $notice = $('<div id="mtz-notice" class="mtz-slider-notice"></div>');
        $("body").append($notice);
      }

      $notice.removeClass("success error").addClass(type);
      $notice.text(message);
      $notice
        .css("display", "block")
        .css("position", "fixed")
        .css("top", "40px")
        .css("right", "20px")
        .css("z-index", "999999");

      setTimeout(function() {
        $notice.removeClass("success error");
        $notice.text("");
        $notice.css("display", "none");
      }, 3000);
    },

    saveChanges: function() {
      MTZSlider.showNotice(mtzSlider.strings.saved, "success");
    },

    toggleHelp: function() {
      $("#mtz-help-modal").show();

      // Reinicializar iconos en la modal
      if (typeof lucide !== "undefined") {
        lucide.createIcons();
      }
    },

    updateImageCount: function() {
      const sliderId = MTZSlider.currentSliderId;
      if (!sliderId) return;

      // Obtener sliders para actualizar el contador
      $.ajax({
        url: mtzSlider.apiUrl + "sliders",
        method: "GET",
        beforeSend: function(xhr) {
          xhr.setRequestHeader("X-WP-Nonce", mtzSlider.nonce);
        },
        success: function(response) {
          // Buscar el slider actual en la lista
          const currentSlider = response.find(s => s.id == sliderId);
          if (currentSlider) {
            // Actualizar el contador en la interfaz
            $(
              '.mtz-slider-item[data-slider-id="' +
                sliderId +
                '"] .mtz-slider-item-stat'
            ).html(
              '<i data-lucide="image"></i> ' +
                (currentSlider.image_count == 1
                  ? currentSlider.image_count + " imagen"
                  : currentSlider.image_count + " imágenes")
            );

            // Reinicializar iconos
            if (typeof lucide !== "undefined") {
              lucide.createIcons();
            }
          }
        }
      });
    }
  };

  // Inicializar cuando el documento esté listo
  $(document).ready(function() {
    MTZSlider.init();

    // Inicializar Lucide Icons en el admin
    if (typeof lucide !== "undefined") {
      lucide.createIcons();
    }
  });
})(jQuery);
