/**
 * National Prevention — GSAP entrance animation
 *
 * A imagem começa escalada a zero do centro e cresce até o tamanho
 * completo quando entra na viewport.
 *
 * Requires: gsap, gsap-scrolltrigger (enqueued by the shortcode PHP).
 */

( function () {
    'use strict';

    function initNationalPrevention() {
        const wrap = document.querySelector( '.np-image-wrap' );
        const img  = document.querySelector( '.np-image' );

        if ( ! wrap || ! img ) {
            return;
        }

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

        // Animate on scroll
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

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', initNationalPrevention );
    } else {
        initNationalPrevention();
    }
} )();
