/**
 * Volunteers — GSAP entrance + counter animation
 *
 * - Imagem: escala de zero ao centro ao entrar na viewport.
 * - Stats: contagem animada de 0 até o valor alvo (via ODLCounter).
 *
 * Requires: gsap, gsap-scrolltrigger, odl-counter (enqueued by the shortcode PHP).
 */

( function () {
    'use strict';

    function initVolunteers() {
        const wrap = document.querySelector( '.vm-image-wrap' );
        const img  = document.querySelector( '.vm-image' );

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

        // Animate image on scroll
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

        // Animate stat counters
        if ( window.ODLCounter ) {
            ODLCounter.init( '.vm-block' );
        }
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', initVolunteers );
    } else {
        initVolunteers();
    }
} )();
