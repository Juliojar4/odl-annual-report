<?php
/**
 * Lottie KATE — WordPress Shortcode
 *
 * Usage: [lottie_kate]
 *
 * Renders a two-column section:
 *  Left  — Lottie animation (kate-animation.json, served from plugin dir)
 *  Right — Heading, body text, and CTA button
 *
 * Customise via the 'lk_block_config' filter:
 *
 *   add_filter( 'lk_block_config', function( $config ) {
 *       $config['heading']  = 'Custom heading';
 *       $config['body']     = '<p>Custom text.</p>';
 *       $config['btn_text'] = 'Click here';
 *       $config['btn_url']  = 'https://example.com';
 *       return $config;
 *   } );
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Asset registration ─────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', 'lk_register_assets' );

function lk_register_assets() {
    $base = plugin_dir_url( __FILE__ );

    wp_register_style(
        'lottie-kate',
        $base . 'lottie-kate.css',
        [],
        '1.0.0'
    );

    // Lottie web-component player (loaded from CDN as required by the project)
    wp_register_script(
        'lottie-player',
        'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js',
        [],
        null,
        false // load in <head> so the custom element is defined before first paint
    );
}

// ── CSP header: upgrade-insecure-requests (avoids mixed-content errors) ────

add_action( 'wp_head', 'lk_csp_meta', 1 );

function lk_csp_meta() {
    static $printed = false;
    if ( $printed ) return;
    $printed = true;
    echo '<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">' . "\n";
}

// ── Shortcode ──────────────────────────────────────────────────────────────

add_shortcode( 'lottie_kate', 'lk_render_shortcode' );

function lk_render_shortcode( $atts ) {
    wp_enqueue_style( 'lottie-kate' );
    wp_enqueue_script( 'lottie-player' );

    $defaults = [
        'heading'    => 'More than 300,000 people asked KATE',
        'body'       =>
            '<p>In 2025, Overdose Lifeline launched KATE, an anonymous overdose prevention chatbot. More than 300,000 people used KATE in its first year, including 250,000 Indiana residents in the first three months alone.</p>'
            . '<p>While KATE uses AI technology to respond in real time, her answers come from a repository built by subject matter experts and grounded in evidence-informed prevention information. KATE received a Shorty Award recognizing its role in expanding access to prevention information through anonymous, stigma-free technology.</p>',
        'btn_text'   => 'Say Hello to KATE',
        'btn_url'    => 'https://hellokate.com/',
        'btn_target' => '_blank',
    ];

    $config = apply_filters( 'lk_block_config', $defaults );

    $allowed    = wp_kses_allowed_html( 'post' );
    $heading    = isset( $config['heading'] )    ? esc_html( $config['heading'] )        : '';
    $body       = isset( $config['body'] )       ? wp_kses( $config['body'], $allowed )  : '';
    $btn_text   = isset( $config['btn_text'] )   ? esc_html( $config['btn_text'] )       : '';
    $btn_url    = isset( $config['btn_url'] )    ? esc_url( $config['btn_url'] )         : '#';
    $btn_target = isset( $config['btn_target'] ) ? esc_attr( $config['btn_target'] )     : '_blank';

    $animation_url = esc_url( plugin_dir_url( __FILE__ ) . 'kate-animation.json' );

    ob_start();
    ?>
    <section class="lk-block" aria-label="<?php echo esc_attr( $heading ); ?>">
        <div class="lk-inner">

            <!-- Left: Lottie animation -->
            <div class="lk-col lk-col--animation" aria-hidden="true">
                <lottie-player
                    src="<?php echo $animation_url; ?>"
                    background="transparent"
                    speed="1"
                    loop
                    autoplay
                    class="lk-player"
                ></lottie-player>
            </div>

            <!-- Right: content -->
            <div class="lk-col lk-col--content">
                <?php if ( $heading ) : ?>
                <h2 class="lk-heading"><?php echo $heading; ?></h2>
                <?php endif; ?>

                <?php if ( $body ) : ?>
                <div class="lk-body"><?php echo $body; ?></div>
                <?php endif; ?>

                <?php if ( $btn_text ) : ?>
                <a
                    class="lk-btn"
                    href="<?php echo $btn_url; ?>"
                    target="<?php echo $btn_target; ?>"
                    rel="noopener noreferrer"
                >
                    <?php echo $btn_text; ?>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8 8" fill="none" aria-hidden="true" focusable="false">
                        <path d="M1 7L7 1M7 1H2M7 1V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <?php endif; ?>
            </div>

        </div>
    </section>
    <?php
    return ob_get_clean();
}
