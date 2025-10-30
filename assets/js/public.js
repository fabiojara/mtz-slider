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
      this.autoplayInterval = null;

      this.init();
    }

    init() {
      this.setupSlider();
      this.bindEvents();
      if (this.autoplay) this.startAutoplay();
      this.setupViewportObserver();
    }

    setupSlider() {
      if (this.totalSlides === 0) return;
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
      this.currentSlide = ((index % this.totalSlides) + this.totalSlides) % this.totalSlides;
      const translateX = -this.currentSlide * 100;
      this.trackEl.style.transform = `translateX(${translateX}%)`;
      const dots = Array.from(this.sliderEl.querySelectorAll(".mtz-slider-dot"));
      dots.forEach((d, i) => d.classList.toggle("active", i === this.currentSlide));
    }

    nextSlide() { this.showSlide(this.currentSlide + 1); }
    prevSlide() { this.showSlide(this.currentSlide - 1); }

    goToSlide(index) {
      this.showSlide(index);
      if (this.autoplay) this.restartAutoplay();
    }

    bindEvents() {
      const nextBtn = this.sliderEl.querySelector(".mtz-slider-next");
      const prevBtn = this.sliderEl.querySelector(".mtz-slider-prev");
      nextBtn && nextBtn.addEventListener("click", (e) => { e.stopPropagation(); this.nextSlide(); if (this.autoplay) this.restartAutoplay(); });
      prevBtn && prevBtn.addEventListener("click", (e) => { e.stopPropagation(); this.prevSlide(); if (this.autoplay) this.restartAutoplay(); });

      this.sliderEl.addEventListener("mouseenter", () => { if (this.autoplay) this.stopAutoplay(); });
      this.sliderEl.addEventListener("mouseleave", () => { if (this.autoplay) this.startAutoplay(); });

      let startX = 0, startY = 0;
      this.sliderEl.addEventListener("touchstart", (e) => {
        startX = e.touches[0].pageX;
        startY = e.touches[0].pageY;
      }, { passive: true });
      this.sliderEl.addEventListener("touchend", (e) => {
        const distX = e.changedTouches[0].pageX - startX;
        const distY = e.changedTouches[0].pageY - startY;
        if (Math.abs(distX) > Math.abs(distY)) {
          if (distX > 50) this.prevSlide();
          else if (distX < -50) this.nextSlide();
          if (this.autoplay) this.restartAutoplay();
        }
      }, { passive: true });

      // Accesibilidad con teclado
      document.addEventListener("keydown", (e) => {
        if (!this.sliderEl.contains(document.activeElement)) return;
        if (e.key === "ArrowLeft") { e.preventDefault(); this.prevSlide(); if (this.autoplay) this.restartAutoplay(); }
        if (e.key === "ArrowRight") { e.preventDefault(); this.nextSlide(); if (this.autoplay) this.restartAutoplay(); }
      });
    }

    startAutoplay() {
      if (!this.autoplay || this.totalSlides <= 1) return;
      this.stopAutoplay();
      this.autoplayInterval = setInterval(() => this.nextSlide(), this.speed);
    }
    stopAutoplay() { if (this.autoplayInterval) { clearInterval(this.autoplayInterval); this.autoplayInterval = null; } }
    restartAutoplay() { this.stopAutoplay(); this.startAutoplay(); }

    setupViewportObserver() {
      if (!("IntersectionObserver" in window)) return;
      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (this.autoplay) {
            if (entry.isIntersecting) this.startAutoplay(); else this.stopAutoplay();
          }
        });
      }, { threshold: 0.2 });
      observer.observe(this.sliderEl);
    }
  }

  function ready(fn) {
    if (document.readyState !== "loading") fn();
    else document.addEventListener("DOMContentLoaded", fn);
  }

  ready(function() {
    if (typeof lucide !== "undefined") {
      lucide.createIcons();
    }
    document.querySelectorAll(".mtz-slider-wrapper .mtz-slider").forEach((slider) => {
      new SliderInstance(slider);
    });
  });
})();
