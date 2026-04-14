<?php
/**
 * Thank You — WordPress Shortcode
 *
 * Usage: [thank_you]
 *
 * Renders a centred heading + body section followed by a full-width
 * image that animates in with GSAP (scale from centre on scroll).
 *
 * Customise via the 'ty_block_config' filter:
 *
 *   add_filter( 'ty_block_config', function( $config ) {
 *       $config['heading']   = 'Custom heading';
 *       $config['body']      = '<p>Custom text.</p>';
 *       $config['image_url'] = get_template_directory_uri() . '/images/custom.webp';
 *       $config['image_alt'] = 'Custom alt text';
 *       return $config;
 *   } );
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Asset registration ─────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', 'ty_register_assets' );

function ty_register_assets() {
    $base = plugin_dir_url( __FILE__ );

    wp_register_style(
        'thank-you',
        $base . 'thank-you.css',
        [],
        '1.0.0'
    );

    if ( ! wp_script_is( 'gsap', 'registered' ) ) {
        wp_register_script(
            'gsap',
            'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',
            [],
            '3.12.5',
            true
        );
    }

    if ( ! wp_script_is( 'gsap-scrolltrigger', 'registered' ) ) {
        wp_register_script(
            'gsap-scrolltrigger',
            'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js',
            [ 'gsap' ],
            '3.12.5',
            true
        );
    }

    wp_register_script(
        'thank-you',
        $base . 'thank-you.js',
        [ 'gsap', 'gsap-scrolltrigger' ],
        '1.0.0',
        true
    );
}

// ── Shortcode ──────────────────────────────────────────────────────────────

add_shortcode( 'thank_you', 'ty_render_shortcode' );

function ty_render_shortcode( $atts ) {
    wp_enqueue_style( 'thank-you' );
    wp_enqueue_script( 'thank-you' );

    $defaults = [
        'heading'   => 'Thank You',
        'body'      => '<p>Across Indiana, families are staying connected, communities are responding earlier, and more people are finding support when they need it most. The work ahead continues to grow, and so does the community making it possible. Thank you for being part of the prevention, support, and recovery happening every day.</p>',
        'image_url' => plugin_dir_url( __FILE__ ) . '../img/odl-indy-fuel-families.webp',
        'image_alt' => '#Worth Saving sign at a golf course',
    ];

    $config = apply_filters( 'ty_block_config', $defaults );

    $allowed   = wp_kses_allowed_html( 'post' );
    $heading   = isset( $config['heading'] )   ? esc_html( $config['heading'] )          : '';
    $body      = isset( $config['body'] )      ? wp_kses( $config['body'], $allowed )    : '';
    $image_url = isset( $config['image_url'] ) ? esc_url( $config['image_url'] )         : '';
    $image_alt = isset( $config['image_alt'] ) ? esc_attr( $config['image_alt'] )        : '';

    ob_start();
    ?>
    <section class="ty-block" aria-label="<?php echo esc_attr( $heading ); ?>">

        <div class="ty-text">
            <?php if ( $heading ) : ?>
            <h2 class="ty-heading"><?php echo $heading; ?></h2>
            <?php endif; ?>

            <?php if ( $body ) : ?>
            <div class="ty-body"><?php echo $body; ?></div>
            <?php endif; ?>
        </div>

        <?php if ( $image_url ) : ?>
        <div class="ty-image-wrap" aria-hidden="true">
            <img
                class="ty-image"
                src="<?php echo $image_url; ?>"
                alt="<?php echo $image_alt; ?>"
                loading="lazy"
                decoding="async"
            >
        </div>
        <?php endif; ?>

    </section>
    <?php
    return ob_get_clean();
}
