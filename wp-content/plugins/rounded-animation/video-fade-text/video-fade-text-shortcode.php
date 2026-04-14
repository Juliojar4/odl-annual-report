<?php
/**
 * Video Fade Text — WordPress Shortcode
 *
 * Usage: [video_fade_text]
 *
 * All attributes are optional and fall back to the defaults below.
 * Attributes that support rich HTML (text_1, text_2) can also be set
 * via the vft_block_config filter.
 *
 * ----------------------------------------------------------------
 * ATTRIBUTES
 * ----------------------------------------------------------------
 *   video_url    URL of the background video (MP4)
 *   video_poster Fallback poster image URL while the video loads
 *   text_1       First text shown on load — supports HTML (bold, em…)
 *   text_2       Second text revealed on scroll — supports HTML
 *   overlay      CSS colour for the semi-transparent overlay
 *                Default: rgba(0,0,0,0.30)
 *   text_color   CSS colour for both text layers. Default: #ffffff
 *
 * ----------------------------------------------------------------
 * FILTER: vft_block_config
 * ----------------------------------------------------------------
 *   Override any default value programmatically:
 *
 *   add_filter( 'vft_block_config', function( $config ) {
 *       $config['text_1'] = 'More than <strong>43 million</strong> people…';
 *       $config['text_2'] = 'Both are happening every day…';
 *       return $config;
 *   } );
 *
 * ----------------------------------------------------------------
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Asset registration ─────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', 'vft_register_assets' );

function vft_register_assets() {
    $base = plugin_dir_url( __FILE__ );

    wp_register_style(
        'video-fade-text',
        $base . 'video-fade-text.css',
        [],
        '1.0.0'
    );

    // GSAP core + ScrollTrigger (shared with rounded-animation if already registered)
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
        'video-fade-text',
        $base . 'video-fade-text.js',
        [ 'gsap', 'gsap-scrolltrigger' ],
        '1.0.0',
        true
    );
}

// ── Shortcode ──────────────────────────────────────────────────────────────

add_shortcode( 'video_fade_text', 'vft_render_shortcode' );

function vft_render_shortcode( $atts ) {

    wp_enqueue_style( 'video-fade-text' );
    wp_enqueue_script( 'video-fade-text' );

    $defaults = [
        'video_url'    => plugin_dir_url( __FILE__ ) . '../img/Hero-video-Annual-Report-placeholder.mp4',
        'video_poster' => '',
        'text_1'       => 'More than <strong>43 million people</strong> in the United States are living with <strong>substance use disorder</strong>, and more than <strong>23 million are in recovery</strong>. Both are happening every day in communities across the country.',
        'text_2'       => 'Across Indiana, Overdose Lifeline works alongside families, schools, healthcare providers, and community partners to make sure support is there when it matters most, because <b>substance use disorder is a chronic disease that deserves the same care, consistency, and response as any other chronic disease.</b>',
        'overlay'      => 'rgba(0,0,0,0.30)',
        'text_color'   => '#ffffff',
    ];

    $defaults = apply_filters( 'vft_block_config', $defaults );

    $atts = shortcode_atts( $defaults, $atts, 'video_fade_text' );

    // Sanitise text fields — allow the same tags WordPress allows in post content
    $allowed        = wp_kses_allowed_html( 'post' );
    $atts['text_1'] = wp_kses( $atts['text_1'], $allowed );
    $atts['text_2'] = wp_kses( $atts['text_2'], $allowed );

    // Sanitise plain-value fields
    $video_url    = esc_url( $atts['video_url'] );
    $video_poster = esc_url( $atts['video_poster'] );
    $overlay      = esc_attr( $atts['overlay'] );
    $text_color   = esc_attr( $atts['text_color'] );

    $block_id = 'vft-' . uniqid();

    ob_start();
    ?>
    <section
        class="vft-block"
        id="<?php echo esc_attr( $block_id ); ?>"
        data-vft="<?php echo esc_attr( $block_id ); ?>"
        aria-label="Video section with animated text"
    >
        <?php /* Per-instance CSS variables */ ?>
        <style>
            #<?php echo esc_attr( $block_id ); ?> {
                --vft-text-color: <?php echo $text_color; ?>;
                --vft-overlay:    <?php echo $overlay; ?>;
            }
        </style>

        <?php /* Background video */ ?>
        <?php if ( $video_url ) : ?>
        <video
            class="vft-video"
            autoplay
            muted
            loop
            playsinline
            <?php echo $video_poster ? 'poster="' . esc_url( $video_poster ) . '"' : ''; ?>
            aria-hidden="true"
        >
            <source src="<?php echo $video_url; ?>" type="video/mp4">
        </video>
        <?php endif; ?>

        <?php /* Semi-transparent overlay */ ?>
        <div class="vft-overlay" aria-hidden="true"></div>

        <?php /* Content wrapper (pinned during scroll) */ ?>
        <div class="vft-container">

            <?php /* Text 1 — visible on load, fades out on scroll */ ?>
            <p
                class="vft-text vft-text-1"
                data-vft-text-1="<?php echo esc_attr( $block_id ); ?>"
            ><?php echo $atts['text_1']; ?></p>

            <?php /* Text 2 — hidden on load, fades in on scroll */ ?>
            <p
                class="vft-text vft-text-2"
                data-vft-text-2="<?php echo esc_attr( $block_id ); ?>"
                aria-hidden="true"
            ><?php echo $atts['text_2']; ?></p>

        </div>

    </section>
    <?php
    return ob_get_clean();
}
