/**
 * Program Stats — counter animation
 *
 * Requires: odl-counter (enqueued by the shortcode PHP).
 */

( function () {
    'use strict';

    function init() {
        if ( window.ODLCounter ) {
            ODLCounter.init( '.ps-block' );
        }
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }
} )();
