/**
 * JavaScript público del Slider - MTZ Slider
 */

(function($) {
  "use strict";

  // Clase para manejar cada instancia del slider
  class SliderInstance {
    constructor($slider) {
      this.$slider = $slider;
      this.currentSlide = 0;
      this.totalSlides = $slider.find(".mtz-slide").length;
      this.autoplay =
        $slider.data("autoplay") === "true" ||
        $slider.data("autoplay") === true;
      this.speed = parseInt($slider.data("speed")) || 5000;
      this.autoplayInterval = null;

      this.init();
    }

    init() {
      this.setupSlider();
      this.bindEvents();

      if (this.autoplay) {
        this.startAutoplay();
      }
    }

    setupSlider() {
      if (this.totalSlides === 0) return;

      // Crear dots
      this.createDots();

      // Mostrar primer slide
      this.showSlide(0);
    }

    createDots() {
      const $dotsContainer = this.$slider.find(".mtz-slider-dots");

      for (let i = 0; i < this.totalSlides; i++) {
        const $dot = $('<button class="mtz-slider-dot"></button>');
        if (i === 0) {
          $dot.addClass("active");
        }
        const slideIndex = i;
        $dot.on("click", () => this.goToSlide(slideIndex));
        $dotsContainer.append($dot);
      }
    }

    showSlide(index) {
      if (this.totalSlides === 0) return;

      this.currentSlide = index % this.totalSlides;
      if (this.currentSlide < 0) {
        this.currentSlide = this.totalSlides - 1;
      }

      const translateX = -this.currentSlide * 100;
      this.$slider
        .find(".mtz-slider-track")
        .css("transform", `translateX(${translateX}%)`);

      // Actualizar dots
      this.$slider
        .find(".mtz-slider-dot")
        .removeClass("active")
        .eq(this.currentSlide)
        .addClass("active");
    }

    nextSlide() {
      this.showSlide(this.currentSlide + 1);
    }

    prevSlide() {
      this.showSlide(this.currentSlide - 1);
    }

    goToSlide(index) {
      this.showSlide(index);
      if (this.autoplay) {
        this.restartAutoplay();
      }
    }

    bindEvents() {
      const that = this;

      // Botones de navegación
      this.$slider.find(".mtz-slider-next").on("click", function(e) {
        e.stopPropagation();
        that.nextSlide();
        if (that.autoplay) {
          that.restartAutoplay();
        }
      });

      this.$slider.find(".mtz-slider-prev").on("click", function(e) {
        e.stopPropagation();
        that.prevSlide();
        if (that.autoplay) {
          that.restartAutoplay();
        }
      });

      // Botón de pausa/reproducción
      this.$slider.find(".mtz-slider-pause-play").on("click", function(e) {
        e.stopPropagation();
        that.toggleAutoplay();
      });

      // Pausa al pasar el mouse
      this.$slider.on("mouseenter", function() {
        if (that.autoplay) {
          that.stopAutoplay();
        }
      });

      this.$slider.on("mouseleave", function() {
        if (that.autoplay) {
          that.startAutoplay();
        }
      });

      // Swipe para dispositivos móviles
      let startX = 0;
      let startY = 0;
      let distX = 0;
      let distY = 0;

      this.$slider.on("touchstart", function(e) {
        startX = e.originalEvent.touches[0].pageX;
        startY = e.originalEvent.touches[0].pageY;
      });

      this.$slider.on("touchend", function(e) {
        distX = e.originalEvent.changedTouches[0].pageX - startX;
        distY = e.originalEvent.changedTouches[0].pageY - startY;

        if (Math.abs(distX) > Math.abs(distY)) {
          if (distX > 50) {
            that.prevSlide();
            if (that.autoplay) that.restartAutoplay();
          } else if (distX < -50) {
            that.nextSlide();
            if (that.autoplay) that.restartAutoplay();
          }
        }

        e.preventDefault();
      });
    }

    startAutoplay() {
      if (!this.autoplay || this.totalSlides <= 1) return;

      this.stopAutoplay();

      const that = this;
      this.autoplayInterval = setInterval(function() {
        that.nextSlide();
      }, this.speed);

      this.$slider
        .find(".mtz-slider-pause-play .dashicons")
        .removeClass("dashicons-controls-play")
        .addClass("dashicons-controls-pause");
    }

    stopAutoplay() {
      if (this.autoplayInterval) {
        clearInterval(this.autoplayInterval);
        this.autoplayInterval = null;
      }
    }

    restartAutoplay() {
      this.stopAutoplay();
      this.startAutoplay();
    }

    toggleAutoplay() {
      this.autoplay = !this.autoplay;

      if (this.autoplay) {
        this.startAutoplay();
      } else {
        this.stopAutoplay();
        this.$slider
          .find(".mtz-slider-pause-play .dashicons")
          .removeClass("dashicons-controls-pause")
          .addClass("dashicons-controls-play");
      }
    }
  }

  // Inicializar todos los sliders en la página
  $(document).ready(function() {
    // Navegación con teclado (solo para el slider activo en focus)
    let currentFocusedSlider = null;

    $(document).on("keydown", function(e) {
      if (currentFocusedSlider) {
        if (e.key === "ArrowLeft") {
          e.preventDefault();
          currentFocusedSlider.prevSlide();
          if (currentFocusedSlider.autoplay)
            currentFocusedSlider.restartAutoplay();
        } else if (e.key === "ArrowRight") {
          e.preventDefault();
          currentFocusedSlider.nextSlide();
          if (currentFocusedSlider.autoplay)
            currentFocusedSlider.restartAutoplay();
        }
      }
    });

    $(".mtz-slider-wrapper").on("focusin", function() {
      const instance = $(this).data("sliderInstance");
      if (instance) {
        currentFocusedSlider = instance;
      }
    });

    $(".mtz-slider-wrapper").on("focusout", function() {
      currentFocusedSlider = null;
    });

    // Inicializar cada slider de forma independiente
    $(".mtz-slider-wrapper").each(function() {
      const $sliderWrapper = $(this);
      const $slider = $sliderWrapper.find(".mtz-slider");

      if ($slider.length) {
        const instance = new SliderInstance($slider);
        $sliderWrapper.data("sliderInstance", instance);
      }
    });
  });
})(jQuery);
