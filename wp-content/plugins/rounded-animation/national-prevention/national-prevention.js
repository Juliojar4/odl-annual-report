/**
 * National Prevention — GSAP entrance animation
 *
 * A imagem começa escalada a zero do centro e cresce até o tamanho
 * completo quando entra na viewport (via ODLImageReveal).
 *
 * Requires: gsap, gsap-scrolltrigger, odl-image-reveal (enqueued by the shortcode PHP).
 */

( function () {
    'use strict';

    function initNationalPrevention() {
        if ( window.ODLImageReveal ) {
            ODLImageReveal.init( '.np-image', '.np-image-wrap' );
        }
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', initNationalPrevention );
    } else {
        initNationalPrevention();
    }
} )();
