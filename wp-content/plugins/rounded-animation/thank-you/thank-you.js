/**
 * Thank You — GSAP entrance animation
 *
 * A imagem começa escalada a zero do centro e cresce até o tamanho
 * completo quando entra na viewport (via ODLImageReveal).
 *
 * Requires: gsap, gsap-scrolltrigger, odl-image-reveal (enqueued by the shortcode PHP).
 */

( function () {
    'use strict';

    function initThankYou() {
        if ( window.ODLImageReveal ) {
            ODLImageReveal.init( '.ty-image', '.ty-image-wrap' );
        }
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', initThankYou );
    } else {
        initThankYou();
    }
} )();
