/**
 * ODL Image Reveal — Utilitário de animação de entrada para imagens
 *
 * A imagem começa invisível e cresce do centro até 100% quando entra na tela.
 * Usa CSS animation + IntersectionObserver (sem dependência de GSAP).
 *
 * Como usar:
 *   ODLImageReveal.init( '.my-image', '.my-image-wrap' );
 *   ODLImageReveal.init( '.my-image' ); // usa a própria imagem como trigger
 *
 * Requer: odl-image-reveal CSS (image-reveal-animation.css).
 */

window.ODLImageReveal = ( function () {
    'use strict';

    /**
     * @param {string}  imgSelector   Seletor CSS da imagem.
     * @param {string} [wrapSelector] Seletor CSS do elemento trigger (opcional).
     */
    function init( imgSelector, wrapSelector ) {
        var img = document.querySelector( imgSelector );
        if ( ! img ) {
            return;
        }

        var wrap = ( wrapSelector && document.querySelector( wrapSelector ) ) || img;

        if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
            return;
        }

        // Oculta a imagem antes que o observer dispare
        img.classList.add( 'odl-reveal-img' );

        var observer = new IntersectionObserver(
            function ( entries, obs ) {
                entries.forEach( function ( entry ) {
                    if ( entry.isIntersecting ) {
                        img.classList.add( 'odl-reveal-img--visible' );
                        obs.disconnect();
                    }
                } );
            },
            { threshold: 0 }
        );

        observer.observe( wrap );
    }

    return { init: init };
} )();
