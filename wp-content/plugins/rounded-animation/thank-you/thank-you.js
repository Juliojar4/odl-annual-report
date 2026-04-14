/**
 * Thank You — GSAP entrance animation
 *
 * A imagem entra com fade + leve crescimento e subida suave.
 *
 * Requires: gsap, gsap-scrolltrigger (enqueued by the shortcode PHP).
 */

( function () {
    'use strict';

    function initThankYou() {
        const wrap = document.querySelector( '.ty-image-wrap' );
        const img  = document.querySelector( '.ty-image' );

        if ( ! wrap || ! img ) {
            return;
        }

        // Register the ScrollTrigger plugin
        gsap.registerPlugin( ScrollTrigger );

        // Respect prefers-reduced-motion
        if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
            return;
        }

        // Estado inicial — autoAlpha garante opacity + visibility sem flash
        gsap.set( img, {
            autoAlpha:       0,
            scale:           0,
            transformOrigin: '50% 50%',
        } );

        // Anima ao entrar na viewport
        gsap.to( img, {
            autoAlpha: 1,
            scale:     1,
            duration:  1.2,
            ease:      'power3.out',
            force3D:   true,
            scrollTrigger: {
                trigger: wrap,
                start:   'top 85%',
                once:    true,
            },
        } );
    }

    // Run after DOM is ready
    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', initThankYou );
    } else {
        initThankYou();
    }
} )();
