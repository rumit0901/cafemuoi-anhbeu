/**
 * ==========================================================================
 * ANIMATION ENGINE & SCROLL OBSERVER MODULE — CÀ PHÊ MUỐI ANH BẾU
 * Quản lý chuyển động cuộn trang (IntersectionObserver), hiệu ứng thu gọn Header,
 * nghiêng thẻ Bento Hero 3D và đếm số nhảy sinh động cho các chỉ số.
 * ==========================================================================
 */

(function () {
  'use strict';

  /**
   * Khởi tạo khi DOM đã sẵn sàng
   */
  $(document).ready(function () {
    initHeaderScrollEffect();
    initScrollRevealObserver();
    initHeroBentoTilt();
    initCountUpCounters();
    observeDynamicDomChanges();
  });

  /**
   * 1. Hiệu ứng thu gọn & phát sáng Header khi cuộn trang xuống
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
   * 2. Quản lý cuộn trang xuất hiện mượt mà (Scroll Reveal Observer)
   * Sử dụng Native IntersectionObserver cho hiệu năng cao nhất
   */
  function initScrollRevealObserver() {
    if (!('IntersectionObserver' in window)) {
      // Fallback nếu trình duyệt cũ không hỗ trợ IntersectionObserver
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
          // Sau khi xuất hiện thành công thì ngừng quan sát để tối ưu bộ nhớ
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    // Gắn observer cho toàn bộ phần tử có class .reveal-on-scroll
    document.querySelectorAll('.reveal-on-scroll').forEach(el => {
      revealObserver.observe(el);
    });

    // Lưu observer vào window object để có thể re-observe khi jsRender nạp xong dữ liệu
    window.AppRevealObserver = revealObserver;
  }

  /**
   * 3. Tự động gắn observer cho các thẻ HTML được chèn động (dynamic rendering) từ jsRender
   */
  function observeDynamicDomChanges() {
    if (!window.AppRevealObserver || !('MutationObserver' in window)) return;

    const mutationObserver = new MutationObserver(mutations => {
      mutations.forEach(mutation => {
        mutation.addedNodes.forEach(node => {
          if (node.nodeType === 1) { // ELEMENT_NODE
            if (node.classList && node.classList.contains('reveal-on-scroll')) {
              window.AppRevealObserver.observe(node);
            }
            // Tìm các phần tử con có class .reveal-on-scroll
            const children = node.querySelectorAll ? node.querySelectorAll('.reveal-on-scroll') : [];
            children.forEach(child => window.AppRevealObserver.observe(child));
          }
        });
      });
    });

    mutationObserver.observe(document.body, { childList: true, subtree: true });
  }

  /**
   * 4. Hiệu ứng nghiêng 3D (3D Tilt Card) nhẹ nhàng cho Hero Bento Card khi di chuột
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

      const rotateX = ((y - centerY) / centerY) * -8; // Tối đa nghiêng 8 deg
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
   * 5. Hiệu ứng nhảy số ấn tượng (Count-Up Counter) cho các chỉ số
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

})();
