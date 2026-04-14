/**
 * Horizontal Scroll Steps — Standalone JS
 * Requires: GSAP + ScrollTrigger (loaded via CDN or wp_enqueue_script)
 *
 * Desktop (> 900px):
 *   Bloco pinado. Scroll desliza step atual para a esquerda, próximo
 *   entra pela direita.
 *
 * Mobile (≤ 900px):
 *   Bloco pinado. O .hss-content de cada step é transladado para cima
 *   (simula scroll interno). Só quando TODO o conteúdo foi percorrido,
 *   faz fade para o próximo step.
 */
(function () {
  'use strict';

  // ── Desktop: horizontal pin + slide ──────────────────────────────────────

  function initDesktop(block, steps) {
    for (var i = 1; i < steps.length; i++) {
      gsap.set(steps[i], { xPercent: 100, opacity: 1 });
    }

    var tl = gsap.timeline({
      scrollTrigger: {
        trigger: block,
        start: 'top top',
        end: function () {
          return '+=' + (steps.length - 1) * window.innerHeight * 1.5;
        },
        pin: true,
        scrub: 1,
        anticipatePin: 1,
        invalidateOnRefresh: true,
      },
    });

    for (var j = 0; j < steps.length - 1; j++) {
      var segStart = j * 1.0;

      tl
        .to(steps[j], {
          opacity: 0,
          xPercent: -30,
          duration: 0.5,
          ease: 'power2.inOut',
          onStart: (function (outStep, inStep) {
            return function () {
              outStep.setAttribute('aria-hidden', 'true');
              inStep.removeAttribute('aria-hidden');
            };
          }(steps[j], steps[j + 1])),
        }, segStart)

        .to(steps[j + 1], {
          opacity: 1,
          xPercent: 0,
          duration: 0.5,
          ease: 'power2.inOut',
        }, segStart + 0.2);
    }
  }

  // ── Mobile: pin + scroll vertical do conteúdo + slide horizontal ─────────

  function initMobile(block, steps) {
    var VH       = window.innerHeight;
    var STEP_PAD = 96;                          // 48px top + 48px bottom
    var SLIDE_PX = Math.round(VH * 1.5);       // px de scroll para cada slide lateral

    // ─ 1. Medir quanto cada .hss-content ultrapassa a viewport ────────────
    var contents  = [];
    var overflows = [];
    var totalEnd  = 0;

    for (var i = 0; i < steps.length; i++) {
      var el = steps[i].querySelector('.hss-content');
      contents.push(el);

      var ov = el
        ? Math.max(0, el.scrollHeight - (VH - STEP_PAD))
        : 0;
      overflows.push(ov);
      totalEnd += ov;

      if (i < steps.length - 1) {
        totalEnd += SLIDE_PX;
      }
    }

    if (totalEnd < VH) { totalEnd = VH; }

    // ─ 2. Steps 2+ ficam 110% à direita (gap visual de 10%) ───────────────
    for (var k = 1; k < steps.length; k++) {
      gsap.set(steps[k], { xPercent: 110, opacity: 1 });
    }

    // ─ 3. Timeline: 1 unidade de duração = 1 px de scroll ─────────────────
    var tl = gsap.timeline({
      scrollTrigger: {
        trigger: block,
        start: 'top top',
        end: '+=' + totalEnd,
        pin: true,
        scrub: 2,
        invalidateOnRefresh: true,
      },
    });

    var cursor = 0;

    for (var j = 0; j < steps.length; j++) {
      var ov        = overflows[j];
      var contentEl = contents[j];

      // Fase A — rola o conteúdo do step para cima (scroll real do container)
      if (ov > 0 && contentEl) {
        tl.to(contentEl, {
          y:        -ov,
          ease:     'none',
          duration: ov,
        }, cursor);

        cursor += ov;
      }

      // Fase B — slide horizontal para o próximo step
      if (j < steps.length - 1) {
        var slideDur    = SLIDE_PX * 0.6;
        var slideGap    = SLIDE_PX * 0.1;
        var nextContent = contents[j + 1];

        // garante que o conteúdo do próximo step começa exatamente no topo
        if (nextContent) {
          tl.set(nextContent, { y: 0 }, cursor);
        }

        tl
          // step atual sai pela esquerda
          .to(steps[j], {
            xPercent: -110,
            duration: slideDur,
            ease:     'power2.inOut',
            onStart: (function (out, inp) {
              return function () {
                out.setAttribute('aria-hidden', 'true');
                inp.removeAttribute('aria-hidden');
              };
            }(steps[j], steps[j + 1])),
          }, cursor)

          // próximo step entra pela direita
          .to(steps[j + 1], {
            xPercent: 0,
            duration: slideDur,
            ease:     'power2.inOut',
          }, cursor + slideGap);

        cursor += SLIDE_PX;
      }
    }
  }

  // ── Entry point ───────────────────────────────────────────────────────────

  function initHorizontalScrollSteps(block) {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
      return;
    }

    gsap.registerPlugin(ScrollTrigger);

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      return;
    }

    var steps = block.querySelectorAll('.hss-step');
    if (steps.length < 2) return;

    var mm = gsap.matchMedia();

    mm.add('(min-width: 901px)', function () {
      initDesktop(block, steps);
    });

    mm.add('(max-width: 900px)', function () {
      initMobile(block, steps);

      return function () {
        gsap.set(steps, { clearProps: 'xPercent,opacity,transform' });
        var c = block.querySelectorAll('.hss-content');
        gsap.set(c, { clearProps: 'y,transform' });
      };
    });
  }

  function init() {
    var blocks = document.querySelectorAll('[data-hss]');
    for (var i = 0; i < blocks.length; i++) {
      initHorizontalScrollSteps(blocks[i]);
    }

    // Recalcula posições após o browser terminar de pintar o layout final
    // (fontes, admin bar, imagens tardias podem ter causado reflow)
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
