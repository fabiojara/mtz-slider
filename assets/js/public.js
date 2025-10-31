/**
 * JavaScript público del Slider - MTZ Slider (Vanilla JS)
 */
(function() {
  "use strict";

  class SliderInstance {
    constructor(sliderEl) {
      this.sliderEl = sliderEl;
      this.trackEl = sliderEl.querySelector(".mtz-slider-track");
      this.slides = Array.from(sliderEl.querySelectorAll(".mtz-slide"));
      this.currentSlide = 0;
      this.totalSlides = this.slides.length;
      this.autoplay = String(sliderEl.dataset.autoplay) === "true";
      this.speed = parseInt(sliderEl.dataset.speed || "5000", 10);
      this.animationEffect = sliderEl.dataset.animationEffect || "fade";
      this.autoplayInterval = null;

      this.init();
    }

    init() {
      // Asegurar que los iconos de Lucide estén disponibles
      if (typeof lucide !== "undefined") {
        // Inicializar iconos en este slider específico
        setTimeout(() => {
          const icons = this.sliderEl.querySelectorAll('[data-lucide]');
          if (icons.length > 0) {
            lucide.createIcons(icons);
          }
        }, 50);
      }

      this.setupSlider();
      this.bindEvents();
      if (this.autoplay) this.startAutoplay();
      this.setupViewportObserver();
    }

    setupSlider() {
      if (this.totalSlides === 0) return;

      // Asegurar que el track esté visible
      if (this.trackEl) {
        this.trackEl.style.display = 'flex';
        this.trackEl.style.width = '100%';
      }

      // Asegurar que todas las slides sean visibles inicialmente (para verificar que cargan)
      this.slides.forEach((slide, index) => {
        if (slide) {
          slide.style.minWidth = '100%';
          slide.style.width = '100%';
          slide.style.flexShrink = '0';
        }
      });

      this.createDots();
      this.showSlide(0);
    }

    createDots() {
      const dotsContainer = this.sliderEl.querySelector(".mtz-slider-dots");
      if (!dotsContainer) return;
      dotsContainer.innerHTML = "";
      for (let i = 0; i < this.totalSlides; i++) {
        const dot = document.createElement("button");
        dot.className = "mtz-slider-dot";
        if (i === 0) dot.classList.add("active");
        dot.addEventListener("click", () => this.goToSlide(i));
        dotsContainer.appendChild(dot);
      }
    }

    showSlide(index) {
      if (this.totalSlides === 0) return;

      // Validar índice
      if (index < 0) index = this.totalSlides - 1;
      if (index >= this.totalSlides) index = 0;

      const previousSlide = this.currentSlide;
      this.currentSlide =
        (index % this.totalSlides + this.totalSlides) % this.totalSlides;

      // Aplicar efecto de animación
      this.applyAnimation(previousSlide, this.currentSlide);

      const dots = Array.from(
        this.sliderEl.querySelectorAll(".mtz-slider-dot")
      );
      dots.forEach((d, i) =>
        d.classList.toggle("active", i === this.currentSlide)
      );
    }

    applyAnimation(previousIndex, currentIndex) {
      const previousSlide = this.slides[previousIndex];
      const currentSlide = this.slides[currentIndex];

      // Remover todas las clases de animación
      this.slides.forEach(slide => {
        slide.classList.remove(
          "mtz-slide-active",
          "mtz-slide-prev",
          "mtz-animate-fade",
          "mtz-animate-slide-horizontal",
          "mtz-animate-slide-vertical",
          "mtz-animate-zoom-in",
          "mtz-animate-zoom-out",
          "mtz-animate-flip-horizontal",
          "mtz-animate-flip-vertical",
          "mtz-animate-cube"
        );
      });

      // Aplicar clase de efecto
      this.trackEl.className =
        "mtz-slider-track mtz-effect-" + this.animationEffect;

      // Asegurar que el contenido siempre esté centrado
      this.slides.forEach(slide => {
        const content = slide.querySelector(".mtz-slide-content");
        if (content) {
          content.style.transform = "translate(-50%, -50%)";
          content.style.position = "absolute";
          content.style.top = "50%";
          content.style.left = "50%";
        }
      });

      // Configurar slides según el efecto
      switch (this.animationEffect) {
        case "fade":
          // Para fade, todas las slides deben estar visibles pero con opacidad
          this.slides.forEach((slide, i) => {
            if (slide) {
              slide.style.display = "block";
              slide.style.position = i === currentIndex ? "relative" : "absolute";
              slide.style.opacity = i === currentIndex ? "1" : "0";
              slide.style.zIndex = i === currentIndex ? "2" : "1";
              if (i === currentIndex) {
                slide.classList.add("mtz-slide-active", "mtz-animate-fade");
              } else {
                slide.classList.remove("mtz-slide-active", "mtz-animate-fade");
              }
            }
          });
          break;

        case "slide-horizontal":
          const translateX = -this.currentSlide * 100;
          this.trackEl.style.transform = `translateX(${translateX}%)`;
          this.trackEl.style.transition = "transform 0.5s ease-in-out";
          break;

        case "slide-vertical":
          const translateY = -this.currentSlide * 100;
          this.trackEl.style.transform = `translateY(${translateY}%)`;
          this.trackEl.style.transition = "transform 0.5s ease-in-out";
          break;

        case "zoom-in":
          if (previousSlide) {
            previousSlide.style.transform = "scale(1)";
            previousSlide.style.opacity = "0";
          }
          if (currentSlide) {
            currentSlide.style.transform = "scale(1.1)";
            currentSlide.style.opacity = "1";
            currentSlide.style.display = "block";
            currentSlide.classList.add("mtz-animate-zoom-in");
          }
          this.slides.forEach((slide, i) => {
            if (i !== currentIndex) {
              slide.style.display = "none";
            }
          });
          break;

        case "zoom-out":
          if (previousSlide) {
            previousSlide.style.transform = "scale(1)";
            previousSlide.style.opacity = "0";
          }
          if (currentSlide) {
            currentSlide.style.transform = "scale(0.9)";
            currentSlide.style.opacity = "1";
            currentSlide.style.display = "block";
            currentSlide.classList.add("mtz-animate-zoom-out");
          }
          this.slides.forEach((slide, i) => {
            if (i !== currentIndex) {
              slide.style.display = "none";
            }
          });
          break;

        case "flip-horizontal":
          if (previousSlide) previousSlide.style.transform = "rotateY(180deg)";
          if (currentSlide) {
            currentSlide.style.transform = "rotateY(0deg)";
            currentSlide.style.opacity = "1";
            currentSlide.style.display = "block";
            currentSlide.classList.add("mtz-animate-flip-horizontal");
          }
          this.slides.forEach((slide, i) => {
            if (i !== currentIndex) {
              slide.style.display = "none";
            }
          });
          break;

        case "flip-vertical":
          if (previousSlide) previousSlide.style.transform = "rotateX(180deg)";
          if (currentSlide) {
            currentSlide.style.transform = "rotateX(0deg)";
            currentSlide.style.opacity = "1";
            currentSlide.style.display = "block";
            currentSlide.classList.add("mtz-animate-flip-vertical");
          }
          this.slides.forEach((slide, i) => {
            if (i !== currentIndex) {
              slide.style.display = "none";
            }
          });
          break;

        case "cube":
          const cubeTranslateX = -this.currentSlide * 100;
          this.trackEl.style.transform = `translateX(${cubeTranslateX}%) rotateY(-90deg)`;
          this.trackEl.style.transition = "transform 0.6s ease-in-out";
          break;

        default:
          // Default: slide horizontal
          const defaultTranslateX = -this.currentSlide * 100;
          this.trackEl.style.transform = `translateX(${defaultTranslateX}%)`;
          this.trackEl.style.transition = "transform 0.5s ease-in-out";
      }

      // Marcar slide actual como activo
      if (currentSlide) {
        currentSlide.classList.add("mtz-slide-active");
      }

      // Reforzar centrado del contenido después de aplicar animación
      this.slides.forEach(slide => {
        const content = slide.querySelector(".mtz-slide-content");
        if (content) {
          content.style.transform = "translate(-50%, -50%)";
          content.style.position = "absolute";
          content.style.top = "50%";
          content.style.left = "50%";
        }
      });
    }

    nextSlide() {
      this.showSlide(this.currentSlide + 1);
    }
    prevSlide() {
      this.showSlide(this.currentSlide - 1);
    }

    goToSlide(index) {
      this.showSlide(index);
      if (this.autoplay) this.restartAutoplay();
    }

    bindEvents() {
      const nextBtn = this.sliderEl.querySelector(".mtz-slider-next");
      const prevBtn = this.sliderEl.querySelector(".mtz-slider-prev");
      nextBtn &&
        nextBtn.addEventListener("click", e => {
          e.stopPropagation();
          this.nextSlide();
          if (this.autoplay) this.restartAutoplay();
        });
      prevBtn &&
        prevBtn.addEventListener("click", e => {
          e.stopPropagation();
          this.prevSlide();
          if (this.autoplay) this.restartAutoplay();
        });

      this.sliderEl.addEventListener("mouseenter", () => {
        if (this.autoplay) this.stopAutoplay();
      });
      this.sliderEl.addEventListener("mouseleave", () => {
        if (this.autoplay) this.startAutoplay();
      });

      // Gestos táctiles para móviles y tablets (swipe)
      let startX = 0,
        startY = 0,
        isDragging = false;

      this.sliderEl.addEventListener(
        "touchstart",
        e => {
          startX = e.touches[0].pageX;
          startY = e.touches[0].pageY;
          isDragging = true;
          // Detener autoplay durante el arrastre
          if (this.autoplay) this.stopAutoplay();
        },
        { passive: true }
      );

      this.sliderEl.addEventListener(
        "touchmove",
        e => {
          if (!isDragging) return;
          const currentX = e.touches[0].pageX;
          const currentY = e.touches[0].pageY;
          const distX = currentX - startX;
          const distY = currentY - startY;

          // Si el movimiento horizontal es mayor que el vertical, prevenir scroll
          if (Math.abs(distX) > Math.abs(distY)) {
            e.preventDefault();
          }
        },
        { passive: false }
      );

      this.sliderEl.addEventListener(
        "touchend",
        e => {
          if (!isDragging) return;
          isDragging = false;

          const endX = e.changedTouches[0].pageX;
          const endY = e.changedTouches[0].pageY;
          const distX = endX - startX;
          const distY = endY - startY;
          const threshold = 50; // Distancia mínima en píxeles para activar el swipe

          // Solo procesar si el movimiento horizontal es mayor que el vertical
          if (Math.abs(distX) > Math.abs(distY)) {
            // Mover hacia la izquierda (deslizar izquierda) = siguiente slide
            if (distX < -threshold) {
              this.nextSlide();
              if (this.autoplay) this.restartAutoplay();
            } else if (distX > threshold) {
              // Mover hacia la derecha (deslizar derecha) = slide anterior
              this.prevSlide();
              if (this.autoplay) this.restartAutoplay();
            } else if (this.autoplay) {
              // Si no alcanza el umbral, reanudar autoplay
              this.startAutoplay();
            }
          } else if (this.autoplay) {
            // Si fue un movimiento vertical o muy pequeño, reanudar autoplay
            this.startAutoplay();
          }
        },
        { passive: true }
      );

      // Limpiar estado si el toque se cancela (ej: llamada entrante)
      this.sliderEl.addEventListener(
        "touchcancel",
        () => {
          isDragging = false;
          if (this.autoplay) this.startAutoplay();
        },
        { passive: true }
      );

      // Accesibilidad con teclado
      document.addEventListener("keydown", e => {
        if (!this.sliderEl.contains(document.activeElement)) return;
        if (e.key === "ArrowLeft") {
          e.preventDefault();
          this.prevSlide();
          if (this.autoplay) this.restartAutoplay();
        }
        if (e.key === "ArrowRight") {
          e.preventDefault();
          this.nextSlide();
          if (this.autoplay) this.restartAutoplay();
        }
      });
    }

    startAutoplay() {
      if (!this.autoplay || this.totalSlides <= 1) return;
      this.stopAutoplay();
      this.autoplayInterval = setInterval(() => this.nextSlide(), this.speed);
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

    setupViewportObserver() {
      if (!("IntersectionObserver" in window)) return;
      const observer = new IntersectionObserver(
        entries => {
          entries.forEach(entry => {
            if (this.autoplay) {
              if (entry.isIntersecting) this.startAutoplay();
              else this.stopAutoplay();
            }
          });
        },
        { threshold: 0.2 }
      );
      observer.observe(this.sliderEl);
    }
  }

  function ready(fn) {
    if (document.readyState !== "loading") fn();
    else document.addEventListener("DOMContentLoaded", fn);
  }

  function initSliders() {
    // Buscar todos los sliders (pueden haber sido agregados dinámicamente)
    // Buscar por wrapper primero, luego por slider directamente
    const wrappers = document.querySelectorAll(".mtz-slider-wrapper");
    const sliders = document.querySelectorAll(".mtz-slider");

    // Combinar ambos selectores para encontrar todos los sliders
    const allSliders = new Set();
    wrappers.forEach(wrapper => {
      const slider = wrapper.querySelector(".mtz-slider");
      if (slider) allSliders.add(slider);
    });
    sliders.forEach(slider => allSliders.add(slider));

    // Inicializar cada slider encontrado
    allSliders.forEach(slider => {
      // Evitar inicializar dos veces
      if (!slider.dataset.initialized && slider.classList.contains('mtz-slider')) {
        slider.dataset.initialized = "true";
        try {
          new SliderInstance(slider);
        } catch (e) {
          console.error('Error inicializando slider:', e);
        }
      }
    });

    // Inicializar iconos de Lucide después de inicializar sliders
    if (typeof lucide !== "undefined") {
      // Esperar un poco para que los elementos estén en el DOM
      setTimeout(function() {
        try {
          lucide.createIcons();
        } catch (e) {
          console.error('Error inicializando iconos Lucide:', e);
        }
      }, 50);
    }
  }

  // Inicializar cuando el DOM esté listo
  ready(initSliders);

  // Inicializar también cuando Elementor cargue el contenido
  if (typeof window.elementorFrontend !== "undefined") {
    // Hook para cuando Elementor inicializa
    window.elementorFrontend.hooks.addAction("frontend/element_ready/global", function($scope) {
      // Verificar si el elemento contiene un slider
      if ($scope && $scope.find && $scope.find('.mtz-slider').length > 0) {
        setTimeout(initSliders, 100);
      }
      // También buscar sin jQuery
      if ($scope && $scope[0]) {
        const sliderElements = $scope[0].querySelectorAll ? $scope[0].querySelectorAll('.mtz-slider') : [];
        if (sliderElements.length > 0) {
          setTimeout(initSliders, 100);
        }
      }
    });

    // Hook para cuando Elementor termina de renderizar
    window.elementorFrontend.hooks.addAction("frontend/init", function() {
      setTimeout(initSliders, 200);
    });

    // Escuchar cuando se renderiza una sección
    window.elementorFrontend.hooks.addAction("frontend/element_ready/section", function($scope) {
      setTimeout(initSliders, 150);
    });

    // Hook para widgets (donde comúnmente van los shortcodes)
    window.elementorFrontend.hooks.addAction("frontend/element_ready/widget", function($scope) {
      setTimeout(initSliders, 100);
    });

    // Hook para shortcode widget específicamente
    window.elementorFrontend.hooks.addAction("frontend/element_ready/shortcode.default", function($scope) {
      setTimeout(initSliders, 100);
    });

    // También escuchar cuando se actualiza el elemento
    if (typeof jQuery !== "undefined") {
      jQuery(document).on('elementor/popup/show', function() {
        setTimeout(initSliders, 150);
      });
    }

    // Escuchar cuando Elementor renderiza contenido dinámicamente
    if (typeof window.elementorFrontend.on !== "undefined") {
      window.elementorFrontend.on('components:init', function() {
        setTimeout(initSliders, 200);
      });
    }
  }

  // Para el maquetador nativo de WordPress (Gutenberg)
  if (typeof wp !== "undefined" && typeof wp.domReady !== "undefined") {
    wp.domReady(function() {
      setTimeout(initSliders, 100);
    });
  }

  // Inicializar también cuando window.load
  if (window.addEventListener) {
    window.addEventListener('load', function() {
      setTimeout(initSliders, 200);
    });
  }

  // Inicializar cuando se agreguen nuevos elementos al DOM (MutationObserver)
  if (typeof MutationObserver !== "undefined") {
    let initTimeout;
    const observer = new MutationObserver(function(mutations) {
      let shouldInit = false;
      mutations.forEach(function(mutation) {
        if (mutation.addedNodes.length > 0) {
          mutation.addedNodes.forEach(function(node) {
            if (node.nodeType === 1) {
              // Verificar si el nodo agregado es o contiene un slider
              if (node.classList && (
                  node.classList.contains("mtz-slider") ||
                  node.classList.contains("mtz-slider-wrapper") ||
                  node.querySelector(".mtz-slider") ||
                  node.querySelector(".mtz-slider-wrapper")
              )) {
                shouldInit = true;
              }
            }
          });
        }
      });
      if (shouldInit) {
        // Debounce para evitar múltiples inicializaciones
        clearTimeout(initTimeout);
        initTimeout = setTimeout(initSliders, 150);
      }
    });

    ready(function() {
      observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: false
      });
    });
  }
})();
