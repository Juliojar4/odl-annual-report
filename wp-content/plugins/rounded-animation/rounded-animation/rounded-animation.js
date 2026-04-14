/**
 * Rounded Animation — Standalone JS
 * Requires: GSAP + ScrollTrigger (loaded via CDN or wp_enqueue_script)
 * No ES modules, no build step, runs in any browser.
 */
(function () {
  'use strict';

  function initRoundedAnimation(block) {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
      return;
    }

    gsap.registerPlugin(ScrollTrigger);

    var spherePrimary   = block.querySelector('[data-sphere-primary]');
    var sphereSecondary = block.querySelector('[data-sphere-secondary]');
    var initialContent  = block.querySelector('[data-initial-content]');
    var secondaryContent = block.querySelector('[data-secondary-content]');
    var finalContent    = block.querySelector('[data-final-content]');

    if (!spherePrimary || !sphereSecondary || !initialContent || !secondaryContent || !finalContent) {
      return;
    }

    // Responsive target size for the primary sphere
    function getSphereSize() {
      return window.innerWidth <= 768 ? '600px' : '1400px';
    }

    var tl = gsap.timeline({
      scrollTrigger: {
        trigger: block,
        start: 'top 6%',
        end: '+=100%',
        pin: true,
        scrub: 1,
        markers: false,
        anticipatePin: 1
      }
    });

    tl
      // 1. Expand primary sphere
      .to(spherePrimary, {
        width: getSphereSize(),
        height: getSphereSize(),
        duration: 1,
        ease: 'power2.inOut'
      }, 0)

      // 2. Fade in + expand secondary sphere
      .to(sphereSecondary, {
        opacity: 1,
        scale: 1,
        duration: 1,
        ease: 'power2.inOut'
      }, 0.3)

      // 3. Fade out initial content
      .to(initialContent, {
        opacity: 0,
        y: -30,
        duration: 0.5,
        ease: 'power2.out'
      }, 0.3)

      // 4. Fade in secondary content
      .to(secondaryContent, {
        opacity: 1,
        y: 0,
        duration: 0.4,
        ease: 'power2.in',
        onStart: function () {
          secondaryContent.setAttribute('aria-hidden', 'false');
          initialContent.setAttribute('aria-hidden', 'true');
        }
      }, 0.5)

      // 5. Fade in final content (quote)
      .to(finalContent, {
        opacity: 1,
        y: 0,
        duration: 0.6,
        ease: 'power2.in'
      }, 0.9);
  }

  function init() {
    var blocks = document.querySelectorAll('[data-rounded-animation]');
    for (var i = 0; i < blocks.length; i++) {
      initRoundedAnimation(blocks[i]);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
