/**
 * Program Stats — counter + image reveal animation
 *
 * Requires: odl-counter, odl-image-reveal (enqueued by the shortcode PHP).
 */

( function () {
    'use strict';

    function init() {
        if ( window.ODLCounter ) {
            ODLCounter.init( '.ps-block' );
        }

        if ( window.ODLImageReveal ) {
            ODLImageReveal.init( '.ps-image', '.ps-image-wrap' );
        }
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }
} )();
