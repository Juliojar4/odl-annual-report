<?php
/**
 * Contributing to National Prevention Efforts — WordPress Shortcode
 *
 * Usage: [national_prevention]
 *
 * Renders:
 *  1. Centred bold heading
 *  2. Centred body paragraph
 *  3. Full-width image (GSAP scale-from-centre entrance animation)
 *
 * Customise via the 'np_block_config' filter:
 *
 *   add_filter( 'np_block_config', function( $config ) {
 *       $config['heading']   = 'Custom heading';
 *       $config['body']      = '<p>Custom body text.</p>';
 *       $config['image_url'] = get_template_directory_uri() . '/images/photo.jpg';
 *       $config['image_alt'] = 'Description of the image';
 *       return $config;
 *   } );
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Asset registration ─────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', 'np_register_assets' );

function np_register_assets() {
    $base = plugin_dir_url( __FILE__ );

    wp_register_style(
        'national-prevention',
        $base . 'national-prevention.css',
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
        'national-prevention',
        $base . 'national-prevention.js',
        [ 'gsap', 'gsap-scrolltrigger' ],
        '1.0.0',
        true
    );
}

// ── Shortcode ──────────────────────────────────────────────────────────────

add_shortcode( 'national_prevention', 'np_render_shortcode' );

function np_render_shortcode( $atts ) {
    wp_enqueue_style( 'national-prevention' );
    wp_enqueue_script( 'national-prevention' );

    $defaults = [
        'heading'   => 'Contributing to National Prevention Efforts',
        'body'      => '<p>In 2025, Overdose Lifeline shared prevention strategies developed with Indiana communities at national conferences, including the Rx Summit, the American Public Health Association Annual Meeting, and the National Youth Prevention, Treatment, and Recovery Roundtable, reaching nearly 200 professionals working across healthcare, public health, and youth services systems.</p>',
        'image_url' => plugin_dir_url( __FILE__ ) . '../img/odl-apha-2025.webp',
        'image_alt' => 'National prevention conference',
    ];

    $config = apply_filters( 'np_block_config', $defaults );

    $allowed   = wp_kses_allowed_html( 'post' );
    $heading   = isset( $config['heading'] )   ? esc_html( $config['heading'] )       : '';
    $body      = isset( $config['body'] )      ? wp_kses( $config['body'], $allowed ) : '';
    $image_url = isset( $config['image_url'] ) ? esc_url( $config['image_url'] )      : '';
    $image_alt = isset( $config['image_alt'] ) ? esc_attr( $config['image_alt'] )     : '';

    ob_start();
    ?>
    <section class="np-block" aria-label="<?php echo esc_attr( $heading ); ?>">

        <div class="np-inner">

            <?php if ( $heading ) : ?>
            <h2 class="np-heading"><?php echo $heading; ?></h2>
            <?php endif; ?>

            <?php if ( $body ) : ?>
            <div class="np-body"><?php echo $body; ?></div>
            <?php endif; ?>

            <?php if ( $image_url ) : ?>
            <div class="np-image-wrap" aria-hidden="true">
                <img
                    class="np-image"
                    src="<?php echo $image_url; ?>"
                    alt="<?php echo $image_alt; ?>"
                    loading="lazy"
                    decoding="async"
                >
            </div>
            <?php endif; ?>

        </div>

    </section>
    <?php
    return ob_get_clean();
}
