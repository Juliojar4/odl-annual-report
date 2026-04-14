/**
 * Volunteers — GSAP entrance + counter animation
 *
 * - Imagem: escala de zero ao centro ao entrar na viewport (via ODLImageReveal).
 * - Stats: contagem animada de 0 até o valor alvo (via ODLCounter).
 *
 * Requires: gsap, gsap-scrolltrigger, odl-counter, odl-image-reveal (enqueued by the shortcode PHP).
 */

( function () {
    'use strict';

    function initVolunteers() {
        if ( window.ODLImageReveal ) {
            ODLImageReveal.init( '.vm-image', '.vm-image-wrap' );
        }

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
