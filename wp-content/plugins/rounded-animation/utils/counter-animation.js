/**
 * ODL Counter Animation — Utilitário reutilizável de contagem
 *
 * Como usar em qualquer shortcode:
 *   1. Adicione nos elementos: data-count="1100" data-suffix="+"
 *   2. Chame: ODLCounter.init('.seu-container');
 *
 * Requer: gsap, gsap-scrolltrigger (registrados via wp_enqueue_script).
 *
 * Registro no plugin principal:
 *   wp_register_script('odl-counter', plugin_dir_url(__FILE__) . 'utils/counter-animation.js',
 *       ['gsap', 'gsap-scrolltrigger'], '1.0.0', true);
 *
 * Dependência em cada shortcode:
 *   wp_enqueue_script('meu-shortcode', ..., ['gsap', 'gsap-scrolltrigger', 'odl-counter'], ...);
 */

window.ODLCounter = ( function () {
    'use strict';

    /**
     * Formata um número inteiro com separadores de milhar (en-US).
     * Ex: 1100 → "1,100"
     *
     * @param {number} value
     * @returns {string}
     */
    function formatNumber( value ) {
        return Math.round( value ).toLocaleString( 'en-US' );
    }

    /**
     * Anima um único elemento com data-count.
     *
     * @param {HTMLElement} el
     */
    function animateCounter( el ) {
        var target = parseInt( el.dataset.count, 10 );
        var suffix = el.dataset.suffix || '';

        if ( isNaN( target ) ) {
            return;
        }

        var obj = { val: 0 };

        gsap.to( obj, {
            val: target,
            duration: 2,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: el,
                start: 'top 85%',
                once: true,
            },
            onUpdate: function () {
                el.textContent = formatNumber( obj.val ) + suffix;
            },
            onComplete: function () {
                // Garante valor final exato (sem arredondamento residual)
                el.textContent = formatNumber( target ) + suffix;
            },
        } );
    }

    /**
     * Inicializa a animação de contagem em todos os [data-count] dentro do scope.
     *
     * @param {string|HTMLElement} scope  Seletor CSS ou elemento DOM.
     *                                   Padrão: document.
     */
    function init( scope ) {
        if ( typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined' ) {
            console.warn( 'ODLCounter: GSAP e ScrollTrigger são necessários.' );
            return;
        }

        gsap.registerPlugin( ScrollTrigger );

        var container =
            typeof scope === 'string'
                ? document.querySelector( scope )
                : scope || document;

        if ( ! container ) {
            return;
        }

        var counters = container.querySelectorAll( '[data-count]' );

        if ( ! counters.length ) {
            return;
        }

        // Respeita preferência de movimento reduzido
        if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
            counters.forEach( function ( el ) {
                var target = parseInt( el.dataset.count, 10 );
                var suffix = el.dataset.suffix || '';
                if ( ! isNaN( target ) ) {
                    el.textContent = formatNumber( target ) + suffix;
                }
            } );
            return;
        }

        counters.forEach( animateCounter );
    }

    return { init: init };
} )();
