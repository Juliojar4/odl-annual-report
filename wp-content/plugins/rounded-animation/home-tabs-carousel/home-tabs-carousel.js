/**
 * Home Tabs Carousel — Standalone JS
 * Features:
 *  - Desktop: vertical tab switching with auto-advance (IntersectionObserver)
 *  - Desktop: keyboard navigation (arrow keys, Home, End)
 *  - Desktop: scroll-overflow gradient fade on content areas
 *  - Mobile: accordion toggle
 * No dependencies, no build step.
 */
(function () {
  'use strict';

  /* ── Helpers ──────────────────────────────────────────────── */

  function initScrollGradient(panel) {
    if (!panel) return;
    var scrollArea = panel.querySelector('.htc-panel-content--scrollable');
    var bottomGradient = panel.querySelector('.htc-bottom-gradient');
    if (!scrollArea) return;

    function update() {
      var hasOverflow = scrollArea.scrollHeight > scrollArea.clientHeight + 2;
      var atBottom = scrollArea.scrollHeight - scrollArea.scrollTop - scrollArea.clientHeight < 4;
      if (bottomGradient) {
        bottomGradient.style.opacity = (hasOverflow && !atBottom) ? '1' : '0';
      }
    }

    if (!scrollArea.dataset.gradientInit) {
      scrollArea.dataset.gradientInit = 'true';
      scrollArea.addEventListener('scroll', update, { passive: true });
    }
    requestAnimationFrame(update);
  }

  /* ── Desktop Tabs ─────────────────────────────────────────── */

  function initTabs(carousel) {
    var tabs = carousel.querySelectorAll('.htc-tab');
    var panels = carousel.querySelectorAll('.htc-panel');
    if (!tabs.length) return;

    var isLight = carousel.dataset.lightMode === 'true';
    var activeBorderClass = isLight ? 'htc-tab--active-light' : 'htc-tab--active';

    var tabLetters = Array.from(tabs).map(function (t) { return t.dataset.tab; });
    var initialActive = carousel.dataset.activeTab || tabLetters[0];
    var currentIndex = tabLetters.indexOf(initialActive);
    if (currentIndex === -1) currentIndex = 0;

    var autoRotateEnabled = carousel.dataset.autoRotate !== 'false';
    var isUserInteracting = carousel.dataset.isUserInteracting === 'true';
    var isVisible = false;
    var autoAdvanceInterval = null;

    function switchTab(index) {
      tabs.forEach(function (tab, i) {
        var isActive = i === index;
        tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
        if (isActive) {
          tab.classList.remove('htc-tab--inactive');
          tab.classList.add('htc-tab--active');
        } else {
          tab.classList.remove('htc-tab--active');
          tab.classList.add('htc-tab--inactive');
        }
      });

      panels.forEach(function (panel, i) {
        if (i === index) {
          panel.classList.remove('htc-panel--inactive');
          panel.classList.add('htc-panel--active');
          initScrollGradient(panel);
        } else {
          panel.classList.remove('htc-panel--active');
          panel.classList.add('htc-panel--inactive');
        }
      });

      currentIndex = index;
    }

    function stopAutoAdvance() {
      if (autoAdvanceInterval) {
        clearInterval(autoAdvanceInterval);
        autoAdvanceInterval = null;
      }
    }

    function startAutoAdvance() {
      stopAutoAdvance();
      if (!isUserInteracting && isVisible && autoRotateEnabled) {
        autoAdvanceInterval = setInterval(function () {
          switchTab((currentIndex + 1) % tabs.length);
        }, 5000);
      }
    }

    // Tab click
    tabs.forEach(function (tab, index) {
      tab.addEventListener('click', function () {
        stopAutoAdvance();
        isUserInteracting = true;
        carousel.dataset.isUserInteracting = 'true';
        switchTab(index);
      });

      // Keyboard navigation
      tab.addEventListener('keydown', function (e) {
        var newIndex = currentIndex;
        switch (e.key) {
          case 'ArrowRight':
          case 'ArrowDown':
            e.preventDefault();
            newIndex = (currentIndex + 1) % tabs.length;
            break;
          case 'ArrowLeft':
          case 'ArrowUp':
            e.preventDefault();
            newIndex = (currentIndex - 1 + tabs.length) % tabs.length;
            break;
          case 'Home':
            e.preventDefault();
            newIndex = 0;
            break;
          case 'End':
            e.preventDefault();
            newIndex = tabs.length - 1;
            break;
          default:
            return;
        }
        stopAutoAdvance();
        isUserInteracting = true;
        carousel.dataset.isUserInteracting = 'true';
        switchTab(newIndex);
        tabs[newIndex].focus();
      });
    });

    // Initialize
    switchTab(currentIndex);

    // IntersectionObserver for auto-advance
    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          var wasVisible = isVisible;
          isVisible = entry.isIntersecting;
          if (isVisible && !isUserInteracting) {
            if (!wasVisible) startAutoAdvance();
          } else if (!isVisible) {
            stopAutoAdvance();
          }
        });
      }, { threshold: 0.1, rootMargin: '0px' });

      observer.observe(carousel);
    }
  }

  /* ── Mobile Accordion ─────────────────────────────────────── */

  function initAccordion(block) {
    var items = block.querySelectorAll('.htc-accordion-item');
    items.forEach(function (item) {
      var trigger = item.querySelector('.htc-accordion-trigger');
      var content = item.querySelector('.htc-accordion-content');
      var iconVertical = item.querySelector('.htc-accordion-icon-vertical');
      if (!trigger || !content) return;

      trigger.addEventListener('click', function () {
        var isOpen = item.classList.contains('htc-accordion-item--open');

        if (isOpen) {
          // Close
          content.style.maxHeight = content.scrollHeight + 'px';
          requestAnimationFrame(function () {
            content.style.maxHeight = '0';
          });
          item.classList.remove('htc-accordion-item--open');
          trigger.setAttribute('aria-expanded', 'false');
        } else {
          // Open
          item.classList.add('htc-accordion-item--open');
          trigger.setAttribute('aria-expanded', 'true');
          content.style.maxHeight = content.scrollHeight + 'px';
          // After transition, remove explicit max-height so content can resize naturally
          content.addEventListener('transitionend', function onEnd() {
            if (item.classList.contains('htc-accordion-item--open')) {
              content.style.maxHeight = 'none';
            }
            content.removeEventListener('transitionend', onEnd);
          });
        }
      });
    });
  }

  /* ── Init ─────────────────────────────────────────────────── */

  function init() {
    // Desktop carousels
    var carousels = document.querySelectorAll('.htc-block');
    carousels.forEach(function (carousel) {
      initTabs(carousel);
      initAccordion(carousel);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
