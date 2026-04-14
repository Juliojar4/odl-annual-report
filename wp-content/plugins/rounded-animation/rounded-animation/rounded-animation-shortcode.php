<?php
/**
 * Rounded Animation — WordPress Shortcode
 *
 * Usage: [rounded_animation]
 *
 * All attributes are optional and fall back to the defaults below.
 * Attributes that support rich HTML (title, subtitle, etc.) can be set
 * via the ra_block_config filter — see the "Customising content" section.
 *
 * ----------------------------------------------------------------
 * INSTALLATION
 * ----------------------------------------------------------------
 * Option A — as a standalone plugin:
 *   1. Place the three files inside a new folder:
 *        wp-content/plugins/rounded-animation/
 *   2. Add the following line at the top of THIS file:
 *        Plugin Name: Rounded Animation Shortcode
 *   3. Activate the plugin in WP Admin > Plugins.
 *
 * Option B — in the active theme:
 *   1. Copy all three files to your theme folder, e.g.:
 *        wp-content/themes/your-theme/inc/rounded-animation/
 *   2. Add to functions.php:
 *        require_once get_template_directory() . '/inc/rounded-animation/rounded-animation-shortcode.php';
 *
 * ----------------------------------------------------------------
 * FONTS
 * ----------------------------------------------------------------
 * The original block uses the "Gotham" typeface. The shortcode falls
 * back to system-ui automatically. If the destination site has Gotham
 * loaded, add the following CSS to override the font family:
 *
 *   .ra-block, .ra-block * { font-family: "Gotham", sans-serif; }
 *
 * ----------------------------------------------------------------
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Asset registration ─────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', 'ra_register_assets' );

function ra_register_assets() {
    $base = plugin_dir_url( __FILE__ );

    // Fallback: if loaded via require_once from a theme, use theme path
    if ( ! function_exists( 'plugin_dir_url' ) || strpos( $base, 'plugins' ) === false ) {
        $base = get_template_directory_uri() . '/inc/rounded-animation/';
    }

    wp_register_style(
        'rounded-animation',
        $base . 'rounded-animation.css',
        [],
        '1.0.0'
    );

    // GSAP core + ScrollTrigger from CDN (no npm required)
    wp_register_script(
        'gsap',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',
        [],
        '3.12.5',
        true
    );

    wp_register_script(
        'gsap-scrolltrigger',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js',
        [ 'gsap' ],
        '3.12.5',
        true
    );

    wp_register_script(
        'rounded-animation',
        $base . 'rounded-animation.js',
        [ 'gsap', 'gsap-scrolltrigger' ],
        '1.0.0',
        true
    );
}

// ── Shortcode ──────────────────────────────────────────────────────────────

add_shortcode( 'rounded_animation', 'ra_render_shortcode' );

function ra_render_shortcode( $atts ) {

    // Enqueue on demand — only when the shortcode is actually used
    wp_enqueue_style( 'rounded-animation' );
    wp_enqueue_script( 'rounded-animation' );

    // ── Default content ───────────────────────────────────────────────────
    // Simple text attributes can be passed directly in the shortcode tag:
    //   [rounded_animation eyebrow="The problem" author_name="Jane Doe"]
    //
    // For HTML-rich content (title, subtitle…) use the ra_block_config filter
    // instead of cramming HTML into shortcode attributes.
    $defaults = [
        // --- Initial panel ---
        'eyebrow'              => 'The problem',
        'title'                => 'On any given night, <br><span style="font-weight:800;font-size:1.2em;">75,000+</span>',
        'subtitle'             => '<b>people</b> experience homelessness in Los Angeles County—<b>enough to fill Dodger Stadium.</b>',

        // --- Secondary panel ---
        'eyebrow_secondary'    => 'St. Joseph Center GOAL',
        'title_secondary'      => '<span style="font-weight:800;">End poverty and homelessness</span><br>in this generation.',
        'subtitle_secondary'   => 'At St. Joseph Center, we know that solving homelessness means <b>more than providing shelter.</b> Families, youth, and <b>individuals</b> each need different kinds of support to thrive.',

        // --- Final quote panel ---
        'final_quote'          => 'If we solve housing here, we can solve it anywhere. If we get it right here, we can solve this in our generation.',
        'author_name'          => 'Dr. Ryan J. Smith',
        'author_title'         => 'President &amp; CEO of St. Joseph Center',

        // --- Scroll-to target for the arrow button ---
        'scroll_target'        => '#home-what-we-do',
    ];

    /**
     * Filter: ra_block_config
     *
     * Override any default value programmatically (supports HTML strings):
     *
     *   add_filter( 'ra_block_config', function( $config ) {
     *       $config['title']  = 'My custom <strong>headline</strong>';
     *       $config['author_name'] = 'Jane Doe';
     *       return $config;
     *   } );
     */
    $defaults = apply_filters( 'ra_block_config', $defaults );

    // Merge with any shortcode attributes (simple string overrides only)
    $atts = shortcode_atts( $defaults, $atts, 'rounded_animation' );

    // Sanitise — allow the same tags WordPress allows in post content
    $allowed = wp_kses_allowed_html( 'post' );
    foreach ( $atts as $key => $value ) {
        $atts[ $key ] = wp_kses( $value, $allowed );
    }

    $block_id = 'rounded-animation-' . uniqid();

    // ── Inline leaf SVG (avoids an external image request) ───────────────
    $leaf_svg = '<svg width="38" height="30" viewBox="0 0 38 30" fill="none" xmlns="http://www.w3.org/2000/svg" class="ra-leaf-icon" aria-hidden="true">'
              . '<path d="M37.6617 1.09713C39.0941 1.75526 35.5636 14.4727 34.7671 16.1733C31.3901 23.3834 21.9384 29.1126 14.1311 30C13.909 24.548 13.8591 18.3232 16.4981 13.342C19.5693 7.54519 31.2648 2.28 37.6617 1.09713Z" fill="#78BE21"/>'
              . '<path d="M2.8161 0C6.60149 1.95099 11.2336 6.19555 12.4112 10.4047C13.4556 14.1375 11.5692 22.6206 9.84955 26.1198C9.61835 26.5903 9.46284 26.8347 8.93642 27.0096C0.134599 19.8278 -2.67915 10.3385 2.8161 0Z" fill="#78BE21"/>'
              . '</svg>';

    // ── Render ────────────────────────────────────────────────────────────
    ob_start();
    ?>
    <section
        class="ra-block"
        id="<?php echo esc_attr( $block_id ); ?>"
        data-rounded-animation="<?php echo esc_attr( $block_id ); ?>"
        aria-label="Animated content section"
    >

        <?php /* Dynamic per-instance styles (sphere sizes, height breakpoints) */ ?>
        <style>
            [data-sphere-secondary="<?php echo esc_attr( $block_id ); ?>"] {
                width: 900px;
                height: 900px;
            }
            @media (min-width: 1024px) {
                [data-sphere-secondary="<?php echo esc_attr( $block_id ); ?>"] {
                    width: 2100px;
                    height: 2100px;
                }
            }
        </style>

        <?php /* Background spheres */ ?>
        <div class="ra-spheres-container" aria-hidden="true">

            <div
                class="ra-sphere ra-sphere-secondary"
                data-sphere-secondary="<?php echo esc_attr( $block_id ); ?>"
            ></div>

            <div
                class="ra-sphere ra-sphere-primary"
                data-sphere-primary="<?php echo esc_attr( $block_id ); ?>"
            ></div>

        </div>

        <?php /* Content wrapper */ ?>
        <div class="ra-container">
            <div class="ra-inner">

                <?php /* Panel 1 — Initial (fades out) */ ?>
                <header
                    class="ra-initial-content"
                    data-initial-content="<?php echo esc_attr( $block_id ); ?>"
                >
                    <?php if ( ! empty( $atts['eyebrow'] ) ) : ?>
                        <span class="ra-eyebrow" role="doc-subtitle">
                            <?php echo $atts['eyebrow']; ?>
                        </span>
                    <?php endif; ?>

                    <?php if ( ! empty( $atts['title'] ) ) : ?>
                        <h2 class="ra-title">
                            <?php echo $atts['title']; ?>
                        </h2>
                    <?php endif; ?>

                    <?php if ( ! empty( $atts['subtitle'] ) ) : ?>
                        <p class="ra-subtitle">
                            <?php echo $atts['subtitle']; ?>
                        </p>
                    <?php endif; ?>
                </header>

                <?php /* Panel 2 — Secondary (fades in) */ ?>
                <div
                    class="ra-secondary-content"
                    data-secondary-content="<?php echo esc_attr( $block_id ); ?>"
                    aria-hidden="true"
                >
                    <?php if ( ! empty( $atts['eyebrow_secondary'] ) ) : ?>
                        <span class="ra-eyebrow" role="doc-subtitle">
                            <?php echo $atts['eyebrow_secondary']; ?>
                        </span>
                    <?php endif; ?>

                    <?php if ( ! empty( $atts['title_secondary'] ) ) : ?>
                        <h2 class="ra-title">
                            <?php echo $atts['title_secondary']; ?>
                        </h2>
                    <?php endif; ?>

                    <?php if ( ! empty( $atts['subtitle_secondary'] ) ) : ?>
                        <p class="ra-subtitle-secondary">
                            <?php echo $atts['subtitle_secondary']; ?>
                        </p>
                    <?php endif; ?>
                </div>

                <?php /* Panel 3 — Final quote (fades in at bottom) */ ?>
                <div
                    class="ra-final-content"
                    data-final-content="<?php echo esc_attr( $block_id ); ?>"
                >
                    <div class="ra-divider"></div>

                    <?php if ( ! empty( $atts['final_quote'] ) ) : ?>
                        <p class="ra-subhead">
                            <?php echo $atts['final_quote']; ?>
                        </p>
                    <?php endif; ?>

                    <div class="ra-author">
                        <?php echo $leaf_svg; ?>

                        <?php if ( ! empty( $atts['author_name'] ) ) : ?>
                            <p class="ra-author-name">
                                <?php echo $atts['author_name']; ?>
                            </p>
                        <?php endif; ?>

                        <?php if ( ! empty( $atts['author_title'] ) ) : ?>
                            <p class="ra-author-title">
                                <?php echo $atts['author_title']; ?>
                            </p>
                        <?php endif; ?>

                        <?php /* Pulsating scroll arrow */ ?>
                        <div class="ra-scroll-arrow">
                            <a
                                href="<?php echo esc_attr( $atts['scroll_target'] ); ?>"
                                class="ra-scroll-link"
                                onclick="event.preventDefault(); var t = document.querySelector('<?php echo esc_js( $atts['scroll_target'] ); ?>'); if(t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });"
                                aria-label="Scroll to next section"
                            >
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle class="ra-pulse-circle" cx="20" cy="20" r="17" fill="#FFFFFF" opacity="0.4"/>
                                    <circle cx="20" cy="20" r="17" fill="#FFFFFF"/>
                                    <path d="M11 20H28.939M22.4938 13L29 19.8833L22.4938 27" stroke="#093E21" stroke-width="3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </section>
    <?php
    return ob_get_clean();
}
