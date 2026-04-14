<?php
/**
 * Home Tabs Carousel — WordPress Shortcode
 *
 * Usage: [home_tabs_carousel]
 *
 * Shortcode attributes (simple overrides):
 *   light_mode="true"       — white background variant
 *   auto_rotate="false"     — disable auto tab rotation
 *   active_tab="House"      — which tab is initially active
 *   show_bg_svg="false"     — hide decorative background SVG
 *   block_eyebrow="..."
 *   block_title="..."
 *   block_body="..."
 *   block_btn_label="..."   block_btn_url="..."   block_btn_target="_self"
 *   block_btn2_label="..."  block_btn2_url="..."  block_btn2_target="_self"
 *
 * For slide data (rich content), use the htc_block_config filter:
 *
 *   add_filter( 'htc_block_config', function( $cfg ) {
 *       $cfg['slides'] = [
 *           [
 *               'tabLetter'          => 'Nourish',
 *               'title'              => 'My Title',
 *               'subtitle'           => 'italic subtitle',
 *               'content'            => 'Paragraph text here.',
 *               'topicContentType'   => 'text',      // 'text' or 'list'
 *               'topicContent'       => '',
 *               'listTitle'          => '',
 *               'listContent'        => '<li>Item</li>',
 *               'imageUrl'           => '',
 *               'imageAlt'           => '',
 *               'impactFocusItems'   => ['Youth', 'Education'],
 *               'tabButtonLabel'     => 'Learn More',
 *               'tabButtonUrl'       => '/page',
 *               'tabButtonTarget'    => '_self',
 *               'slideButtonLabel'   => '',
 *               'slideButtonUrl'     => '',
 *               'slideButtonTarget'  => '_self',
 *           ],
 *           // … more slides
 *       ];
 *       return $cfg;
 *   });
 *
 * ----------------------------------------------------------------
 * INSTALLATION
 * ----------------------------------------------------------------
 * Option A — as a standalone plugin:
 *   1. Place the 3 files in wp-content/plugins/home-tabs-carousel/
 *   2. Add this header at the very top of this file:
 *        Plugin Name: Home Tabs Carousel Shortcode
 *   3. Activate in WP Admin → Plugins.
 *
 * Option B — in the active theme:
 *   1. Copy the 3 files to wp-content/themes/your-theme/inc/home-tabs-carousel/
 *   2. Add to functions.php:
 *        require_once get_template_directory() . '/inc/home-tabs-carousel/home-tabs-carousel-shortcode.php';
 *
 * ----------------------------------------------------------------
 * FONTS
 * ----------------------------------------------------------------
 * Original uses "Gotham". Falls back to system-ui automatically.
 * If Gotham is loaded, add: .htc-block, .htc-block * { font-family: "Gotham", sans-serif; }
 * ----------------------------------------------------------------
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ── Asset path helper ────────────────────────────────────────── */

function htc_assets_base_url() {
    // Plugin context
    if ( function_exists( 'plugin_dir_url' ) ) {
        $base = plugin_dir_url( __FILE__ );
        if ( strpos( $base, '/plugins/' ) !== false ) {
            return $base;
        }
    }
    // Theme context — adjust the path to match where you placed the files
    return get_template_directory_uri() . '/inc/home-tabs-carousel/';
}

/* ── Register assets ──────────────────────────────────────────── */

add_action( 'wp_enqueue_scripts', 'htc_register_assets' );

function htc_register_assets() {
    $base = htc_assets_base_url();

    wp_register_style(
        'home-tabs-carousel',
        $base . 'home-tabs-carousel.css',
        [],
        '1.0.0'
    );

    wp_register_script(
        'home-tabs-carousel',
        $base . 'home-tabs-carousel.js',
        [],
        '1.0.0',
        true  // footer
    );
}

/* ── Button helper (replaces x-button Blade component) ────────── */

function htc_render_button( $label, $href, $variant = 'primary-normal', $target = '_self', $text_color = null, $svg_color = null ) {
    if ( empty( $label ) || empty( $href ) ) return '';

    $is_primary   = strpos( $variant, 'primary' ) !== false;
    $is_secondary = strpos( $variant, 'secondary' ) !== false;

    $style = '';
    if ( $text_color ) {
        $style .= 'color:' . esc_attr( $text_color ) . ' !important;';
    }
    $style_attr = $style ? ' style="' . $style . '"' : '';

    // Primary icon: green circle + arrow
    $primary_icon = '<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">'
        . '<circle class="htc-btn-icon-pulse" cx="20" cy="20" r="17" fill="#78BE21" opacity="0.4"/>'
        . '<circle cx="20" cy="20" r="17" fill="#78BE21"/>'
        . '<path d="M11 20H28.939M22.4938 13L29 19.8833L22.4938 27" stroke="#093E21" stroke-width="3"/>'
        . '</svg>';

    // Secondary icon: diagonal arrow
    $stroke = esc_attr( $svg_color ?? '#2E9426' );
    $secondary_icon = '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">'
        . '<path style="stroke:' . $stroke . ';" d="M1.06055 14.499L13.6647 1.89491M4.2119 1.49902L13.6254 1.76993L14.0605 11.3477" stroke="' . $stroke . '" stroke-width="3"/>'
        . '</svg>';

    $icon = $is_primary ? $primary_icon : ( $is_secondary ? $secondary_icon : '' );

    return '<a href="' . esc_url( $href ) . '" target="' . esc_attr( $target ) . '"'
        . ' class="htc-btn htc-btn-' . esc_attr( $variant ) . '"'
        . $style_attr . '>'
        . '<span>' . wp_kses_post( $label ) . '</span>'
        . $icon
        . '</a>';
}

/* ── Background SVG helper ────────────────────────────────────── */

function htc_render_bg_svg( $block_id ) {
    // Desktop SVG (large, detailed) — truncated leaf paths
    $desktop = '<svg class="htc-bg-svg-desktop" width="484" height="554" viewBox="0 0 484 554" fill="none" xmlns="http://www.w3.org/2000/svg">'
        . '<g opacity="0.1">'
        . '<mask id="htc-mask-a-' . esc_attr( $block_id ) . '" fill="white">'
        . '<path d="M387.06 595.086C387.431 594.201 387.616 593.929 387.644 593.643L387.658 591.743C385.992 576.243 382.074 561.457 373.725 548.085C368.268 539.299 361.145 532.37 351.186 529.227C338.805 525.342 326.382 521.513 313.745 518.599C306.864 517.013 299.526 517.356 291.348 516.756C298.771 540.871 306.692 563.186 326.653 577.529C344.461 590.329 365.419 594.786 387.06 595.086ZM187.188 426.039C188.456 426.625 189.14 427.039 189.881 427.268C205.595 432.339 221.709 434.74 238.178 433.239C248.963 432.254 259.834 430.268 268.34 422.925C287.188 406.653 302.134 387.224 312.933 364.709C314.13 362.209 314.771 359.466 315.725 356.695C314.414 356.266 313.83 355.923 313.246 355.923C298.059 355.895 282.786 354.966 267.684 356.18C252.554 357.395 238.193 362.352 226.339 372.624C213.503 383.753 202.76 396.653 194.411 411.482C191.79 416.139 189.639 421.068 187.188 426.039ZM40.7996 456.94C34.6306 462.969 28.3334 467.997 23.4609 474.141C15.2831 484.412 7.17653 494.912 0.565903 506.213C-10.6608 525.399 -8.2531 546.385 -4.71977 567.129C-2.81071 578.243 0.394958 589.029 5.68065 599.072C6.45 598.915 6.97713 598.944 7.36183 598.715C25.8118 588.072 42.0106 574.9 51.9978 555.642C55.1179 549.599 57.3263 542.556 57.8534 535.799C59.1499 519.684 56.3147 503.77 52.6675 488.098C50.2312 477.583 47.3533 467.169 40.7996 456.94ZM302.447 215.234C304.285 214.677 305.225 214.534 306.037 214.134C326.168 204.034 344.846 191.848 360.717 175.69C371.531 164.676 380.193 152.275 384.325 137.089C389.995 116.332 391.933 95.2024 390.394 73.7733C389.981 68.1589 388.827 62.6016 387.943 56.5729C361.643 64.1731 338.264 74.9448 319.871 94.631C312.733 102.274 307.476 110.96 305.068 121.403C299.583 145.304 297.66 169.476 299.113 193.891C299.526 200.934 301.264 207.905 302.447 215.234ZM477.429 342.594C474.808 342.452 473.868 342.337 472.942 342.352C448.351 342.909 424.288 346.48 401.108 354.952C382.957 361.595 366.43 370.881 354.577 386.753C337.366 409.825 325.086 435.311 318.304 463.354C317.235 467.726 316.623 472.212 315.725 477.012C345.758 482.012 374.622 482.512 402.803 471.669C416.965 466.226 428.476 457.626 436.74 444.511C451.742 420.725 464.051 395.724 471.901 368.666C474.323 360.366 475.534 351.723 477.429 342.594Z"/>'
        . '</mask>'
        . '<path d="M387.06 595.086C387.431 594.201 387.616 593.929 387.644 593.643L387.658 591.743C385.992 576.243 382.074 561.457 373.725 548.085C368.268 539.299 361.145 532.37 351.186 529.227C338.805 525.342 326.382 521.513 313.745 518.599C306.864 517.013 299.526 517.356 291.348 516.756C298.771 540.871 306.692 563.186 326.653 577.529C344.461 590.329 365.419 594.786 387.06 595.086ZM477.429 342.594C474.808 342.452 473.868 342.337 472.942 342.352C448.351 342.909 424.288 346.48 401.108 354.952C382.957 361.595 366.43 370.881 354.577 386.753C337.366 409.825 325.086 435.311 318.304 463.354C317.235 467.726 316.623 472.212 315.725 477.012C345.758 482.012 374.622 482.512 402.803 471.669C416.965 466.226 428.476 457.626 436.74 444.511C451.742 420.725 464.051 395.724 471.901 368.666C474.323 360.366 475.534 351.723 477.429 342.594Z"'
        . ' stroke="url(#htc-grad-a-' . esc_attr( $block_id ) . ')" stroke-width="6"'
        . ' mask="url(#htc-mask-a-' . esc_attr( $block_id ) . ')"/>'
        . '</g>'
        . '<defs>'
        . '<linearGradient id="htc-grad-a-' . esc_attr( $block_id ) . '" x1="209.5" y1="264.119" x2="243.5" y2="98.6187" gradientUnits="userSpaceOnUse">'
        . '<stop stop-color="#78BE21"/>'
        . '<stop offset="1" stop-color="#093E21"/>'
        . '</linearGradient>'
        . '</defs>'
        . '</svg>';

    // Mobile SVG (smaller version)
    $mobile = '<svg class="htc-bg-svg-mobile" width="296" height="288" viewBox="0 0 296 288" fill="none" xmlns="http://www.w3.org/2000/svg">'
        . '<g opacity="0.1">'
        . '<mask id="htc-mask-m-' . esc_attr( $block_id ) . '" fill="white">'
        . '<path d="M348.276 348.186C348.492 347.668 348.601 347.509 348.617 347.342L348.626 346.23C347.65 337.161 345.358 328.509 340.473 320.685C337.28 315.545 333.112 311.491 327.286 309.652C320.042 307.378 312.773 305.138 305.379 303.433C301.352 302.505 297.059 302.706 292.274 302.354C296.618 316.464 301.252 329.521 312.931 337.913C323.351 345.402 335.613 348.01 348.276 348.186ZM401.151 200.452C399.617 200.369 399.067 200.302 398.525 200.31C384.137 200.636 370.058 202.726 356.495 207.683C345.875 211.57 336.205 217.003 329.27 226.289C319.2 239.789 312.014 254.701 308.046 271.109C307.421 273.667 307.062 276.292 306.537 279.1C324.11 282.026 340.998 282.318 357.487 275.974C365.773 272.789 372.508 267.757 377.343 260.084C386.121 246.167 393.323 231.539 397.916 215.707C399.333 210.851 400.042 205.794 401.151 200.452Z"/>'
        . '</mask>'
        . '<path d="M348.276 348.186C348.492 347.668 348.601 347.509 348.617 347.342L348.626 346.23C347.65 337.161 345.358 328.509 340.473 320.685C337.28 315.545 333.112 311.491 327.286 309.652C320.042 307.378 312.773 305.138 305.379 303.433C301.352 302.505 297.059 302.706 292.274 302.354C296.618 316.464 301.252 329.521 312.931 337.913C323.351 345.402 335.613 348.01 348.276 348.186Z"'
        . ' stroke="url(#htc-grad-m-' . esc_attr( $block_id ) . ')" stroke-width="6"'
        . ' mask="url(#htc-mask-m-' . esc_attr( $block_id ) . ')"/>'
        . '</g>'
        . '<defs>'
        . '<linearGradient id="htc-grad-m-' . esc_attr( $block_id ) . '" x1="244.385" y1="154.536" x2="264.278" y2="57.7019" gradientUnits="userSpaceOnUse">'
        . '<stop stop-color="#78BE21"/>'
        . '<stop offset="1" stop-color="#093E21"/>'
        . '</linearGradient>'
        . '</defs>'
        . '</svg>';

    return $desktop . $mobile;
}

/* ── Main shortcode ───────────────────────────────────────────── */

add_shortcode( 'home_tabs_carousel', 'htc_render_shortcode' );

function htc_render_shortcode( $atts ) {

    // Enqueue assets on demand
    wp_enqueue_style( 'home-tabs-carousel' );
    wp_enqueue_script( 'home-tabs-carousel' );

    /* ── Default config ──────────────────────────────────── */

    $defaults = [
        // Block-level header
        'block_eyebrow'      => '',
        'block_title'        => 'Prevention that Starts with Community',
        'block_body'         =>
            'Through MACRO-B and CHARIOT, Overdose Lifeline works with local partners to expand prevention education, increase access to naloxone, and share real-time alerts in communities most affected by overdose.'
            . "</br>"
            . "</br>"
            . 'Together, these programs help communities respond earlier and connect people to prevention tools and support where they are needed most.',

        // Block-level CTA buttons
        'block_btn_label'    => '',
        'block_btn_url'      => '',
        'block_btn_target'   => '_self',
        'block_btn2_label'   => '',
        'block_btn2_url'     => '',
        'block_btn2_target'  => '_self',

        // Behaviour flags
        'active_tab'         => 'MACRO-B',
        'auto_rotate'        => 'true',
        'light_mode'         => 'false',
        'show_bg_svg'        => 'false',

        // Slides array — override via filter (see below)
        'slides'             => [
            [
                'tabLetter'         => 'MACRO-B',
                'title'             => 'MACRO-B',
                'subtitle'          => '',
                'content'           => 'MACRO-B brings prevention education, outreach coordination, and local partnerships into neighborhoods most affected by overdose. In four Indianapolis ZIP codes, overdose deaths dropped 45% over three years through the MACRO-B Project.',
                'topicContentType'  => 'list',
                'topicContent'      => '',
                'listTitle'         => 'In 2025:',
                'listContent'       =>
                    '<li><strong>586 people trained</strong> in prevention education across <strong>45 trainings</strong></li>'
                    . '<li><strong>80 community organizations</strong> engaged in coordinated outreach</li>'
                    . '<li><strong>37 community interviews</strong> conducted to guide expansion planning</li>'
                    . '<li><strong>52,397 naloxone kits, 26,449 fentanyl test strips, and 19,852 xylazine test strips</strong> distributed in priority communities</li>',
                'imageUrl'          => plugin_dir_url( __FILE__ ) . '../img/tab/odl-macro-b.webp',
                'imageAlt'          => 'MACRO-B program',
                'impactFocusItems'  => [],
                'tabButtonLabel'    => '',
                'tabButtonUrl'      => '',
                'tabButtonTarget'   => '_self',
                'slideButtonLabel'  => '',
                'slideButtonUrl'    => '',
                'slideButtonTarget' => '_self',
            ],
            [
                'tabLetter'         => 'CHARIOT',
                'title'             => 'CHARIOT',
                'subtitle'          => '',
                'content'           => 'CHARIOT shares real-time alerts about overdose risks and connects people to prevention resources across Marion County through a countywide text alert network and rapid-response outreach.',
                'topicContentType'  => 'list',
                'topicContent'      => '',
                'listTitle'         => 'In 2025:',
                'listContent'       =>
                    '<li><strong>460 residents enrolled</strong> in CHARIOT text alerts</li>'
                    . '<li><strong>1,868 people reached</strong> through outreach events</li>'
                    . '<li><strong>25 rapid-response pop-ups</strong> coordinated in priority areas</li>'
                    . '<li><strong>1,322 naloxone kits distributed</strong> at community events</li>',
                'imageUrl'          => plugin_dir_url( __FILE__ ) . '../img/tab/odl-chariot.webp',
                'imageAlt'          => 'CHARIOT program',
                'impactFocusItems'  => [],
                'tabButtonLabel'    => '',
                'tabButtonUrl'      => '',
                'tabButtonTarget'   => '_self',
                'slideButtonLabel'  => '',
                'slideButtonUrl'    => '',
                'slideButtonTarget' => '_self',
            ],
        ],
    ];

    /**
     * Filter: htc_block_config
     * Override defaults programmatically (including full slides array):
     *
     *   add_filter( 'htc_block_config', function( $cfg ) {
     *       $cfg['block_title'] = 'What We Do';
     *       $cfg['slides'][0]['title'] = 'Nourish the Community';
     *       return $cfg;
     *   });
     */
    $defaults = apply_filters( 'htc_block_config', $defaults );

    // Merge shortcode attributes (simple string values only)
    $atts = shortcode_atts( array_filter( $defaults, 'is_string' ), $atts, 'home_tabs_carousel' );

    // Re-attach slides (not overridable via shortcode tag, only via filter)
    $slides    = $defaults['slides'];
    $allowed   = wp_kses_allowed_html( 'post' );
    $block_id  = 'htc-' . uniqid();

    $light_mode   = filter_var( $atts['light_mode'],  FILTER_VALIDATE_BOOLEAN );
    $auto_rotate  = filter_var( $atts['auto_rotate'], FILTER_VALIDATE_BOOLEAN );
    $show_bg_svg  = filter_var( $atts['show_bg_svg'], FILTER_VALIDATE_BOOLEAN );
    $active_tab   = sanitize_text_field( $atts['active_tab'] );
    $text_color   = $light_mode ? 'black' : 'white';

    ob_start();
    ?>
    <section
        class="htc-block<?php echo $light_mode ? ' htc-block--light' : ''; ?>"
        id="<?php echo esc_attr( $block_id ); ?>"
        data-block-id="<?php echo esc_attr( $block_id ); ?>"
        data-active-tab="<?php echo esc_attr( $active_tab ); ?>"
        data-is-user-interacting="false"
        data-auto-rotate="<?php echo $auto_rotate ? 'true' : 'false'; ?>"
        data-light-mode="<?php echo $light_mode ? 'true' : 'false'; ?>"
    >

        <?php /* Background decorative SVG */ ?>
        <?php if ( $show_bg_svg && ! $light_mode ) : ?>
        <div class="htc-bg-svg">
            <?php echo htc_render_bg_svg( $block_id ); ?>
            <div class="htc-bg-overlay"></div>
        </div>
        <?php endif; ?>

        <?php /* ── Desktop layout ────────────────────────────── */ ?>
        <div class="htc-container">
            <div class="htc-main-grid">

                <?php /* Left column */ ?>
                <div class="htc-left-col">

                    <?php /* Block header */ ?>
                    <?php if ( ! empty( $atts['block_eyebrow'] ) || ! empty( $atts['block_title'] ) || ! empty( $atts['block_body'] ) ) : ?>
                    <div class="htc-header">
                        <?php if ( ! empty( $atts['block_eyebrow'] ) ) : ?>
                            <p class="htc-eyebrow">
                                <?php echo wp_kses( $atts['block_eyebrow'], $allowed ); ?>
                            </p>
                        <?php endif; ?>

                        <?php if ( ! empty( $atts['block_title'] ) ) : ?>
                            <h2 class="htc-block-title">
                                <?php echo wp_kses( $atts['block_title'], $allowed ); ?>
                            </h2>
                        <?php endif; ?>

                        <?php if ( ! empty( $atts['block_body'] ) ) : ?>
                            <p class="htc-block-body">
                                <?php echo wp_kses( $atts['block_body'], $allowed ); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php /* Vertical tab navigation (desktop) */ ?>
                    <nav class="htc-tabs-nav" role="tablist" aria-label="Home carousel navigation">
                        <div class="htc-tabs-list">
                            <?php foreach ( $slides as $index => $slide ) :
                                $is_active = ( $slide['tabLetter'] === $active_tab );
                            ?>
                            <button
                                type="button"
                                role="tab"
                                id="htc-tab-<?php echo esc_attr( $block_id . '-' . $index ); ?>"
                                aria-controls="htc-panel-<?php echo esc_attr( $block_id . '-' . $index ); ?>"
                                aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
                                data-tab="<?php echo esc_attr( $slide['tabLetter'] ); ?>"
                                data-index="<?php echo esc_attr( $index ); ?>"
                                class="htc-tab <?php echo $is_active ? 'htc-tab--active' : 'htc-tab--inactive'; ?>"
                            >
                                <?php echo wp_kses_post( $slide['tabLetter'] ); ?>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </nav>

                    <?php /* Block-level CTA buttons */ ?>
                    <?php
                    $has_btn1 = ! empty( $atts['block_btn_label'] ) && ! empty( $atts['block_btn_url'] );
                    $has_btn2 = ! empty( $atts['block_btn2_label'] ) && ! empty( $atts['block_btn2_url'] );
                    if ( $has_btn1 || $has_btn2 ) :
                    ?>
                    <div class="htc-block-buttons">
                        <?php if ( $has_btn1 ) :
                            echo htc_render_button( $atts['block_btn_label'], $atts['block_btn_url'], 'primary-normal', $atts['block_btn_target'], $text_color );
                        endif; ?>
                        <?php if ( $has_btn2 ) :
                            echo htc_render_button( $atts['block_btn2_label'], $atts['block_btn2_url'], 'primary-normal', $atts['block_btn2_target'], $text_color );
                        endif; ?>
                    </div>
                    <?php endif; ?>

                </div><!-- /.htc-left-col -->

                <?php /* Right column: stacked panels */ ?>
                <div class="htc-panels">
                    <?php foreach ( $slides as $index => $slide ) :
                        $is_active      = ( $slide['tabLetter'] === $active_tab );
                        $panel_class    = $is_active ? 'htc-panel--active' : 'htc-panel--inactive';
                        $has_image      = ! empty( $slide['imageUrl'] );
                        $scrollable     = ! $has_image ? ' htc-panel-content--scrollable mot-scroll-area' : '';
                        $impact_items   = htc_parse_impact_items( $slide['impactFocusItems'] ?? [] );
                    ?>
                    <div
                        role="tabpanel"
                        id="htc-panel-<?php echo esc_attr( $block_id . '-' . $index ); ?>"
                        aria-labelledby="htc-tab-<?php echo esc_attr( $block_id . '-' . $index ); ?>"
                        data-panel="<?php echo esc_attr( $slide['tabLetter'] ); ?>"
                        class="htc-panel <?php echo esc_attr( $panel_class ); ?>"
                    >
                        <?php /* Image */ ?>
                        <?php if ( $has_image ) : ?>
                        <div class="htc-panel-image-wrap">
                            <img
                                src="<?php echo esc_url( $slide['imageUrl'] ); ?>"
                                alt="<?php echo esc_attr( $slide['imageAlt'] ?? '' ); ?>"
                                loading="lazy"
                                decoding="async"
                            >
                            <?php if ( ! empty( $slide['slideButtonLabel'] ) && ! empty( $slide['slideButtonUrl'] ) ) : ?>
                            <div class="htc-panel-image-btn">
                                <?php echo htc_render_button(
                                    $slide['slideButtonLabel'],
                                    $slide['slideButtonUrl'],
                                    'secondary-normal',
                                    $slide['slideButtonTarget'] ?? '_self',
                                    '#ffffff',
                                    '#78BE21'
                                ); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php /* Title */ ?>
                        <?php if ( ! empty( $slide['title'] ) ) : ?>
                        <h3 class="htc-panel-title">
                            <?php echo wp_kses_post( $slide['title'] ); ?>
                        </h3>
                        <?php endif; ?>

                        <?php /* Subtitle */ ?>
                        <?php if ( ! empty( $slide['subtitle'] ) ) : ?>
                        <p class="htc-panel-subtitle">
                            <?php echo wp_kses_post( $slide['subtitle'] ); ?>
                        </p>
                        <?php endif; ?>

                        <?php /* Content (scrollable when no image) */ ?>
                        <?php if ( ! empty( $slide['content'] ) ) : ?>
                        <div class="htc-panel-content-wrap">
                            <p class="htc-panel-content<?php echo $scrollable; ?>">
                                <?php echo wp_kses_post( $slide['content'] ); ?>
                            </p>
                            <div class="htc-bottom-gradient" aria-hidden="true"></div>
                        </div>
                        <?php endif; ?>

                        <?php /* Topic content — list or text */ ?>
                        <?php if ( ( $slide['topicContentType'] ?? 'text' ) === 'list' ) : ?>
                            <?php if ( ! empty( $slide['listTitle'] ) || ! empty( $slide['listContent'] ) ) : ?>
                            <div class="htc-panel-list">
                                <?php if ( ! empty( $slide['listTitle'] ) ) : ?>
                                    <p class="htc-panel-list-title">
                                        <?php echo wp_kses_post( $slide['listTitle'] ); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ( ! empty( $slide['listContent'] ) ) : ?>
                                    <ul><?php echo wp_kses_post( $slide['listContent'] ); ?></ul>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        <?php else : ?>
                            <?php if ( ! empty( $slide['topicContent'] ) ) : ?>
                            <p class="htc-panel-topic">
                                <?php echo wp_kses_post( $slide['topicContent'] ); ?>
                            </p>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php /* Impact Focus pills */ ?>
                        <?php if ( ! empty( $impact_items ) ) : ?>
                        <div class="htc-impact-wrap">
                            <p class="htc-impact-label">Impact Focus</p>
                            <div class="htc-pills">
                                <?php foreach ( $impact_items as $pill ) : ?>
                                <span class="htc-pill"><?php echo esc_html( $pill ); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php /* Tab-level button */ ?>
                        <?php if ( ! empty( $slide['tabButtonLabel'] ) && ! empty( $slide['tabButtonUrl'] ) ) : ?>
                        <div class="htc-tab-action">
                            <?php echo htc_render_button(
                                $slide['tabButtonLabel'],
                                $slide['tabButtonUrl'],
                                'primary-normal',
                                $slide['tabButtonTarget'] ?? '_self',
                                $text_color
                            ); ?>
                        </div>
                        <?php endif; ?>

                    </div><!-- /.htc-panel -->
                    <?php endforeach; ?>
                </div><!-- /.htc-panels -->

            </div><!-- /.htc-main-grid -->
        </div><!-- /.htc-container -->

        <?php /* ── Mobile accordion ─────────────────────────── */ ?>
        <div class="htc-container htc-mobile">
            <div class="htc-accordion-wrapper" role="region" aria-label="Home tabs accordion">
                <?php foreach ( $slides as $index => $slide ) :
                    $accordion_id = esc_attr( $block_id . '-acc-' . $index );
                    $panel_id     = esc_attr( $block_id . '-acc-panel-' . $index );
                    $impact_items = htc_parse_impact_items( $slide['impactFocusItems'] ?? [] );
                ?>
                <div class="htc-accordion-item" data-accordion-item="<?php echo $index; ?>">

                    <h3 class="htc-accordion-header-wrap">
                        <button
                            type="button"
                            class="htc-accordion-trigger"
                            aria-expanded="false"
                            aria-controls="<?php echo $panel_id; ?>"
                            id="<?php echo $accordion_id; ?>"
                            data-accordion-trigger="<?php echo $index; ?>"
                        >
                            <span class="htc-accordion-label">
                                <?php echo wp_kses_post( $slide['tabLetter'] ); ?>
                            </span>
                            <span class="htc-accordion-icon">
                                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <line x1="18" y1="3" x2="18" y2="33" stroke="#78BE21" stroke-width="3"
                                          class="htc-accordion-icon-vertical"
                                          data-accordion-icon-vertical="<?php echo $index; ?>"/>
                                    <line x1="3" y1="18.0713" x2="33" y2="18.0713" stroke="#78BE21" stroke-width="3"/>
                                </svg>
                            </span>
                        </button>
                    </h3>

                    <div
                        id="<?php echo $panel_id; ?>"
                        role="region"
                        aria-labelledby="<?php echo $accordion_id; ?>"
                        class="htc-accordion-content"
                        data-accordion-panel="<?php echo $index; ?>"
                    >
                        <div class="htc-accordion-body">

                            <?php if ( ! empty( $slide['title'] ) ) : ?>
                            <h3 class="htc-panel-title">
                                <?php echo wp_kses_post( $slide['title'] ); ?>
                            </h3>
                            <?php endif; ?>

                            <?php if ( ! empty( $slide['subtitle'] ) ) : ?>
                            <p class="htc-panel-subtitle">
                                <?php echo wp_kses_post( $slide['subtitle'] ); ?>
                            </p>
                            <?php endif; ?>

                            <?php if ( ! empty( $slide['content'] ) ) : ?>
                            <p class="htc-panel-content">
                                <?php echo wp_kses_post( $slide['content'] ); ?>
                            </p>
                            <?php endif; ?>

                            <?php if ( ! empty( $slide['slideButtonLabel'] ) && ! empty( $slide['slideButtonUrl'] ) ) : ?>
                            <div style="margin: 1rem 0;">
                                <?php echo htc_render_button(
                                    $slide['slideButtonLabel'],
                                    $slide['slideButtonUrl'],
                                    'primary-normal',
                                    $slide['slideButtonTarget'] ?? '_self',
                                    $text_color
                                ); ?>
                            </div>
                            <?php endif; ?>

                            <?php /* Topic content */ ?>
                            <?php if ( ( $slide['topicContentType'] ?? 'text' ) === 'list' ) : ?>
                                <?php if ( ! empty( $slide['listTitle'] ) || ! empty( $slide['listContent'] ) ) : ?>
                                <div class="htc-panel-list" style="margin-top: 0.75rem;">
                                    <?php if ( ! empty( $slide['listTitle'] ) ) : ?>
                                        <p class="htc-panel-list-title"><?php echo wp_kses_post( $slide['listTitle'] ); ?></p>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $slide['listContent'] ) ) : ?>
                                        <ul><?php echo wp_kses_post( $slide['listContent'] ); ?></ul>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            <?php else : ?>
                                <?php if ( ! empty( $slide['topicContent'] ) ) : ?>
                                <p class="htc-panel-topic" style="margin-top: 0.75rem;">
                                    <?php echo wp_kses_post( $slide['topicContent'] ); ?>
                                </p>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php /* Impact Focus pills */ ?>
                            <?php if ( ! empty( $impact_items ) ) : ?>
                            <div class="htc-impact-wrap" style="margin-bottom: 1.75rem;">
                                <p class="htc-impact-label">Impact Focus</p>
                                <div class="htc-pills">
                                    <?php foreach ( $impact_items as $pill ) : ?>
                                    <span class="htc-pill"><?php echo esc_html( $pill ); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php /* Tab button */ ?>
                            <?php if ( ! empty( $slide['tabButtonLabel'] ) && ! empty( $slide['tabButtonUrl'] ) ) : ?>
                            <div class="htc-tab-action">
                                <?php echo htc_render_button(
                                    $slide['tabButtonLabel'],
                                    $slide['tabButtonUrl'],
                                    'primary-normal',
                                    $slide['tabButtonTarget'] ?? '_self',
                                    $text_color
                                ); ?>
                            </div>
                            <?php endif; ?>

                        </div>
                    </div>

                </div><!-- /.htc-accordion-item -->
                <?php endforeach; ?>
            </div><!-- /.htc-accordion-wrapper -->

            <?php /* Mobile block-level buttons */ ?>
            <?php
            $has_btn1 = ! empty( $atts['block_btn_label'] ) && ! empty( $atts['block_btn_url'] );
            $has_btn2 = ! empty( $atts['block_btn2_label'] ) && ! empty( $atts['block_btn2_url'] );
            if ( $has_btn1 || $has_btn2 ) :
            ?>
            <div class="htc-mobile-buttons">
                <?php if ( $has_btn1 ) :
                    echo htc_render_button( $atts['block_btn_label'], $atts['block_btn_url'], 'primary-normal', $atts['block_btn_target'], $text_color );
                endif; ?>
                <?php if ( $has_btn2 ) :
                    echo htc_render_button( $atts['block_btn2_label'], $atts['block_btn2_url'], 'primary-normal', $atts['block_btn2_target'], $text_color );
                endif; ?>
            </div>
            <?php endif; ?>

        </div><!-- /.htc-mobile -->

    </section>
    <?php
    return ob_get_clean();
}

/* ── Impact items parser ──────────────────────────────────────── */

function htc_parse_impact_items( $items ) {
    if ( empty( $items ) ) return [];
    if ( ! is_array( $items ) ) {
        $items = [ $items ];
    }
    $result = [];
    foreach ( $items as $item ) {
        if ( ! is_string( $item ) ) continue;
        $parts = preg_split( '/<br\s*\/?>|\r?\n/i', $item );
        foreach ( $parts as $part ) {
            $clean = trim( strip_tags( $part ) );
            if ( $clean !== '' ) {
                $result[] = $clean;
            }
        }
    }
    return $result;
}
