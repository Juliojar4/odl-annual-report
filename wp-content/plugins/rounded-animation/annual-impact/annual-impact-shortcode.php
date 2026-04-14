<?php
/**
 * Annual Impact — WordPress Shortcode
 *
 * Usage: [annual_impact]
 *
 * Renders:
 *  1. Shape-divider SVG (full-width, curved transition from light → dark)
 *  2. Dark-blue section with centred heading + body text in white
 *
 * Customise via the 'ai_block_config' filter:
 *
 *   add_filter( 'ai_block_config', function( $config ) {
 *       $config['heading'] = 'Custom Heading';
 *       $config['body']    = '<p>Custom text.</p>';
 *       return $config;
 *   } );
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Asset registration ─────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', 'ai_register_assets' );

function ai_register_assets() {
    wp_register_style(
        'annual-impact',
        plugin_dir_url( __FILE__ ) . 'annual-impact.css',
        [],
        '1.0.0'
    );
}

// ── Shortcode ──────────────────────────────────────────────────────────────

add_shortcode( 'annual_impact', 'ai_render_shortcode' );

function ai_render_shortcode( $atts ) {
    wp_enqueue_style( 'annual-impact' );

    $defaults = [
        'heading' => 'Our Annual Impact',
        'body'    =>
            '<p>Across Indiana, Overdose Lifeline is working with communities to expand prevention, '
            . 'increase access to naloxone, and strengthen support for families impacted by substance '
            . 'use disorder. This is what it looks like when solutions are shaped by the people '
            . 'closest to the work.</p>',
    ];

    $config = apply_filters( 'ai_block_config', $defaults );

    $allowed = wp_kses_allowed_html( 'post' );
    $heading = isset( $config['heading'] ) ? esc_html( $config['heading'] )       : '';
    $body    = isset( $config['body'] )    ? wp_kses( $config['body'], $allowed ) : '';

    $divider_url = esc_url( plugin_dir_url( __FILE__ ) . '../img/shape-divider.svg' );

    ob_start();
    ?>
    <div class="ai-wrap">

        <!-- Shape divider: curves from light section above into dark section -->
        <div class="ai-divider" aria-hidden="true">
            <img
                class="ai-divider__img"
                src="<?php echo $divider_url; ?>"
                alt=""
                role="presentation"
                loading="eager"
                decoding="async"
            >
        </div>

        <!-- Dark blue content section -->
        <section class="ai-block" aria-label="<?php echo esc_attr( $heading ); ?>">
            <div class="ai-inner">

                <?php if ( $heading ) : ?>
                <h2 class="ai-heading"><?php echo $heading; ?></h2>
                <?php endif; ?>

                <?php if ( $body ) : ?>
                <div class="ai-body"><?php echo $body; ?></div>
                <?php endif; ?>

            </div>
        </section>

    </div>
    <?php
    return ob_get_clean();
}
