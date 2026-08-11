/**
 * ==========================================================================
 * MAIN LANDING PAGE INTERACTION SCRIPT — CÀ PHÊ MUỐI ANH BẾU
 * Tối ưu tốc độ tải 100% Client-side, không phụ thuộc API Backend.
 * ==========================================================================
 */

(function () {
  'use strict';

  $(document).ready(function () {
    initHeaderScrollEffect();
    initMobileNavToggle();
    initSmoothScrollNav();
    initScrollRevealObserver();
    initHeroBentoTilt();
    initCountUpCounters();
    initMenuCarousel();
  });

  /**
   * 1. Hiệu ứng Header thu gọn khi cuộn trang
   */
  function initHeaderScrollEffect() {
    const $header = $('.site-header');

    $(window).on('scroll', function () {
      if ($(this).scrollTop() > 40) {
        $header.addClass('scrolled');
      } else {
        $header.removeClass('scrolled');
      }
    });
  }

  /**
   * 2. Toggle Menu Navigation trên thiết bị di động
   */
  function initMobileNavToggle() {
    const $toggle = $('#mobile-menu-toggle');
    const $nav = $('#main-navigation');

    $toggle.on('click', function () {
      $nav.toggleClass('active');
    });

    // Tự động đóng menu khi click chọn link
    $('.nav-link').on('click', function () {
      $nav.removeClass('active');
    });
  }

  /**
   * 3. Cuộn trang mượt mà (Smooth Scroll) & Active Nav Link khi cuộn
   */
  function initSmoothScrollNav() {
    const $navLinks = $('.nav-link');
    const sections = $('section[id]');

    $(window).on('scroll', function () {
      const currentScroll = $(this).scrollTop() + 120;

      sections.each(function () {
        const top = $(this).offset().top;
        const bottom = top + $(this).outerHeight();
        const id = $(this).attr('id');

        if (currentScroll >= top && currentScroll <= bottom) {
          $navLinks.removeClass('active');
          $(`.nav-link[href="#${id}"]`).addClass('active');
        }
      });
    });
  }

  /**
   * 4. Native Intersection Observer cho hiệu ứng cuộn xuất hiện (Scroll Reveal)
   */
  function initScrollRevealObserver() {
    if (!('IntersectionObserver' in window)) {
      $('.reveal-on-scroll').addClass('revealed');
      return;
    }

    const observerOptions = {
      root: null,
      rootMargin: '0px 0px -50px 0px',
      threshold: 0.15
    };

    const revealObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed');
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    document.querySelectorAll('.reveal-on-scroll').forEach(el => {
      revealObserver.observe(el);
    });
  }

  /**
   * 5. Hiệu ứng nghiêng 3D (Tilt Card) cho Hero Bento Image
   */
  function initHeroBentoTilt() {
    const $card = $('.hero-bento-card');
    if (!$card.length) return;

    $card.on('mousemove', function (e) {
      const rect = this.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;

      const centerX = rect.width / 2;
      const centerY = rect.height / 2;

      const rotateX = ((y - centerY) / centerY) * -8;
      const rotateY = ((x - centerX) / centerX) * 8;

      $(this).css({
        transform: `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.02)`
      });
    });

    $card.on('mouseleave', function () {
      $(this).css({
        transform: 'rotate(2deg) scale(1)'
      });
    });
  }

  /**
   * 6. Hiệu ứng đếm số (Count Up Counter)
   */
  function initCountUpCounters() {
    const $counters = $('.stat-counter');
    if (!$counters.length) return;

    const counterObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const $target = $(entry.target);
          const targetNum = parseInt($target.attr('data-target'), 10);
          if (isNaN(targetNum)) return;

          $({ count: 0 }).animate({ count: targetNum }, {
            duration: 2000,
            easing: 'swing',
            step: function () {
              $target.text(Math.floor(this.count).toLocaleString('vi-VN'));
            },
            complete: function () {
              $target.text(targetNum.toLocaleString('vi-VN'));
            }
          });

          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });

    $counters.each(function () {
      counterObserver.observe(this);
    });
  }

  /**
   * 7. Khởi tạo Carousel hình ảnh thực tế
   */
  function initMenuCarousel() {
    const $track = $('#menu-carousel-track');
    const $slides = $('.carousel-slide');
    const $dots = $('.carousel-dots .dot');
    const $prevBtn = $('#carousel-prev-btn');
    const $nextBtn = $('#carousel-next-btn');

    if (!$track.length || !$slides.length) return;

    let currentIndex = 0;
    const totalSlides = $slides.length;
    let autoPlayTimer = null;

    function goToSlide(index) {
      if (index < 0) {
        currentIndex = totalSlides - 1;
      } else if (index >= totalSlides) {
        currentIndex = 0;
      } else {
        currentIndex = index;
      }

      $track.css('transform', `translateX(-${currentIndex * 100}%)`);
      $slides.removeClass('active').eq(currentIndex).addClass('active');
      $dots.removeClass('active').eq(currentIndex).addClass('active');
    }

    $prevBtn.on('click', function () {
      goToSlide(currentIndex - 1);
      resetAutoPlay();
    });

    $nextBtn.on('click', function () {
      goToSlide(currentIndex + 1);
      resetAutoPlay();
    });

    $dots.on('click', function () {
      const slideIndex = parseInt($(this).attr('data-slide'), 10);
      goToSlide(slideIndex);
      resetAutoPlay();
    });

    function startAutoPlay() {
      autoPlayTimer = setInterval(function () {
        goToSlide(currentIndex + 1);
      }, 4000);
    }

    function stopAutoPlay() {
      if (autoPlayTimer) clearInterval(autoPlayTimer);
    }

    function resetAutoPlay() {
      stopAutoPlay();
      startAutoPlay();
    }

    // Tạm dừng tự động trượt khi rê chuột vào
    $('#menu-carousel-wrapper').on('mouseenter', stopAutoPlay).on('mouseleave', startAutoPlay);

    // Khởi tạo slide đầu tiên
    goToSlide(0);
    startAutoPlay();
  }

})();

