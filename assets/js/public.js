/**
 * JavaScript público del Slider - MTZ Slider
 */

(function($) {
    'use strict';
    
    const Slider = {
        currentSlide: 0,
        totalSlides: 0,
        autoplay: false,
        autoplayInterval: null,
        speed: 5000,
        
        init: function() {
            this.setupSlider();
            this.bindEvents();
            
            if (this.autoplay) {
                this.startAutoplay();
            }
        },
        
        setupSlider: function() {
            const $slider = $('.mtz-slider');
            
            if ($slider.length === 0) return;
            
            this.totalSlides = $slider.find('.mtz-slide').length;
            this.autoplay = $slider.data('autoplay') === true;
            this.speed = parseInt($slider.data('speed')) || 5000;
            
            // Crear dots
            this.createDots();
            
            // Mostrar primer slide
            this.showSlide(0);
        },
        
        createDots: function() {
            const $dotsContainer = $('.mtz-slider-dots');
            
            for (let i = 0; i < this.totalSlides; i++) {
                const $dot = $('<button class="mtz-slider-dot"></button>');
                if (i === 0) {
                    $dot.addClass('active');
                }
                $dot.on('click', () => this.goToSlide(i));
                $dotsContainer.append($dot);
            }
        },
        
        showSlide: function(index) {
            if (this.totalSlides === 0) return;
            
            this.currentSlide = index % this.totalSlides;
            if (this.currentSlide < 0) {
                this.currentSlide = this.totalSlides - 1;
            }
            
            const translateX = -this.currentSlide * 100;
            $('.mtz-slider-track').css('transform', `translateX(${translateX}%)`);
            
            // Actualizar dots
            $('.mtz-slider-dot')
                .removeClass('active')
                .eq(this.currentSlide)
                .addClass('active');
        },
        
        nextSlide: function() {
            this.showSlide(this.currentSlide + 1);
        },
        
        prevSlide: function() {
            this.showSlide(this.currentSlide - 1);
        },
        
        goToSlide: function(index) {
            this.showSlide(index);
            if (this.autoplay) {
                this.restartAutoplay();
            }
        },
        
        bindEvents: function() {
            const $slider = $('.mtz-slider');
            
            // Botones de navegación
            $('.mtz-slider-next').on('click', () => {
                this.nextSlide();
                if (this.autoplay) {
                    this.restartAutoplay();
                }
            });
            
            $('.mtz-slider-prev').on('click', () => {
                this.prevSlide();
                if (this.autoplay) {
                    this.restartAutoplay();
                }
            });
            
            // Botón de pausa/reproducción
            $('.mtz-slider-pause-play').on('click', () => {
                this.toggleAutoplay();
            });
            
            // Pausa al pasar el mouse
            $slider.on('mouseenter', () => {
                if (this.autoplay) {
                    this.stopAutoplay();
                }
            });
            
            $slider.on('mouseleave', () => {
                if (this.autoplay) {
                    this.startAutoplay();
                }
            });
            
            // Navegación con teclado
            $(document).on('keydown', (e) => {
                if ($slider.length === 0) return;
                
                if (e.key === 'ArrowLeft') {
                    this.prevSlide();
                    if (this.autoplay) this.restartAutoplay();
                } else if (e.key === 'ArrowRight') {
                    this.nextSlide();
                    if (this.autoplay) this.restartAutoplay();
                }
            });
            
            // Swipe para dispositivos móviles
            let startX = 0;
            let startY = 0;
            let distX = 0;
            let distY = 0;
            
            $slider.on('touchstart', (e) => {
                startX = e.originalEvent.touches[0].pageX;
                startY = e.originalEvent.touches[0].pageY;
            });
            
            $slider.on('touchend', (e) => {
                distX = e.originalEvent.changedTouches[0].pageX - startX;
                distY = e.originalEvent.changedTouches[0].pageY - startY;
                
                if (Math.abs(distX) > Math.abs(distY)) {
                    if (distX > 50) {
                        this.prevSlide();
                        if (this.autoplay) this.restartAutoplay();
                    } else if (distX < -50) {
                        this.nextSlide();
                        if (this.autoplay) this.restartAutoplay();
                    }
                }
                
                e.preventDefault();
            });
        },
        
        startAutoplay: function() {
            if (!this.autoplay || this.totalSlides <= 1) return;
            
            this.stopAutoplay();
            
            this.autoplayInterval = setInterval(() => {
                this.nextSlide();
            }, this.speed);
            
            $('.mtz-slider-pause-play .dashicons')
                .removeClass('dashicons-controls-play')
                .addClass('dashicons-controls-pause');
        },
        
        stopAutoplay: function() {
            if (this.autoplayInterval) {
                clearInterval(this.autoplayInterval);
                this.autoplayInterval = null;
            }
        },
        
        restartAutoplay: function() {
            this.stopAutoplay();
            this.startAutoplay();
        },
        
        toggleAutoplay: function() {
            this.autoplay = !this.autoplay;
            
            if (this.autoplay) {
                this.startAutoplay();
            } else {
                this.stopAutoplay();
                $('.mtz-slider-pause-play .dashicons')
                    .removeClass('dashicons-controls-pause')
                    .addClass('dashicons-controls-play');
            }
        }
    };
    
    // Inicializar cuando el documento esté listo
    $(document).ready(function() {
        Slider.init();
    });
    
})(jQuery);

