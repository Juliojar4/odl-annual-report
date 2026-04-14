/**
 * Video Fade Text — Standalone JS
 * Requires: GSAP + ScrollTrigger (loaded via CDN or wp_enqueue_script)
 * No ES modules, no build step, runs in any browser.
 */
(function () {
  'use strict';

  function initVideoFadeText(block) {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
      return;
    }

    gsap.registerPlugin(ScrollTrigger);

    // ── Placeholder: esconde quando o vídeo estiver pronto ────────────────
    var placeholder = block.querySelector('.vft-placeholder');

    // ── Animação de texto ─────────────────────────────────────────────────
    var text1 = block.querySelector('[data-vft-text-1]');
    var text2 = block.querySelector('[data-vft-text-2]');

    if (!text1 || !text2) {
      return;
    }

    // Set text-2 starting position slightly below centre.
    // Done in JS so GSAP owns the transform — no CSS transform conflict.
    gsap.set(text2, { y: 28 });

    var tl = gsap.timeline({
      scrollTrigger: {
        trigger: block,
        start: 'top',
        end: '+=80%',
        pin: true,
        scrub: 1,
      }
    });

    tl
      // 1. Fade out + lift text 1
      .to(text1, {
        opacity: 0,
        y: -28,
        duration: 0.5,
        ease: 'power2.out'
      }, 0)

      // 2. Fade in + settle text 2 to centre
      .to(text2, {
        opacity: 1,
        y: 0,
        duration: 0.5,
        ease: 'power2.out',
        onStart: function () {
          text2.removeAttribute('aria-hidden');
          text1.setAttribute('aria-hidden', 'true');
        }
      }, 0.3);
  }

  function init() {
    var blocks = document.querySelectorAll('[data-vft]');
    for (var i = 0; i < blocks.length; i++) {
      initVideoFadeText(blocks[i]);
    }

    // Revela o vft-container só após o layout final estar pronto
    var containers = document.querySelectorAll('.vft-container');
    for (var j = 0; j < containers.length; j++) {
      containers[j].classList.add('is-ready');
    }

    requestAnimationFrame(function () {
      ScrollTrigger.refresh();
    });
  }

  if (document.readyState === 'complete') {
    init();
  } else {
    window.addEventListener('load', init);
  }
})();
