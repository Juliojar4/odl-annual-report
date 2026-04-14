<?php
/**
 * Horizontal Scroll Steps — WordPress Shortcode
 *
 * Usage: [horizontal_scroll_steps]
 *
 * Steps are defined in PHP and extensible via the 'hss_steps' filter.
 * To add more steps, hook into the filter and append to the array:
 *
 *   add_filter( 'hss_steps', function( $steps ) {
 *       $steps[] = [
 *           'layout' => 'before-camp', // or any registered layout key
 *           // ...layout-specific fields
 *       ];
 *       return $steps;
 *   } );
 *
 * To add a custom layout, hook into 'hss_render_step_{layout}':
 *
 *   add_filter( 'hss_render_step_my-layout', function( $html, $step, $index ) {
 *       ob_start();
 *       // render your HTML
 *       return ob_get_clean();
 *   }, 10, 3 );
 *
 * ----------------------------------------------------------------
 * REGISTERED LAYOUTS
 * ----------------------------------------------------------------
 *   five-years         — Centered heading + 2 cols (text/button | quote/decoration)
 *   before-camp        — 2 cols (heading + body + stats grid | photo)
 *   after-camp         — 2 cols (photo | heading + body + stats label + stats grid)
 *   family-connection  — 2 cols (heading + body | 2×2 photo grid)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Asset registration ─────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', 'hss_register_assets' );

function hss_register_assets() {
    $base = plugin_dir_url( __FILE__ );

    wp_register_style(
        'horizontal-scroll-steps',
        $base . 'horizontal-scroll-steps.css',
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
        'horizontal-scroll-steps',
        $base . 'horizontal-scroll-steps.js',
        [ 'gsap', 'gsap-scrolltrigger' ],
        '1.0.0',
        true
    );
}

// ── Shortcode ──────────────────────────────────────────────────────────────

add_shortcode( 'horizontal_scroll_steps', 'hss_render_shortcode' );

function hss_render_shortcode( $atts ) {
    wp_enqueue_style( 'horizontal-scroll-steps' );
    wp_enqueue_script( 'horizontal-scroll-steps' );

    $steps    = apply_filters( 'hss_steps', hss_default_steps() );
    $block_id = 'hss-' . uniqid();

    ob_start();
    ?>
    <section
        class="hss-block"
        id="<?php echo esc_attr( $block_id ); ?>"
        data-hss="<?php echo esc_attr( $block_id ); ?>"
        aria-label="Horizontal scroll section"
    >
        <div class="hss-track" aria-live="off">
            <?php foreach ( $steps as $index => $step ) : ?>
                <?php echo hss_render_step( $step, $index ); ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

// ── Step dispatcher ────────────────────────────────────────────────────────

function hss_render_step( $step, $index ) {
    if ( empty( $step['layout'] ) ) {
        return '';
    }

    switch ( $step['layout'] ) {
        case 'five-years':
            return hss_render_step_five_years( $step, $index );
        case 'before-camp':
            return hss_render_step_before_camp( $step, $index );
        case 'after-camp':
            return hss_render_step_after_camp( $step, $index );
        case 'family-connection':
            return hss_render_step_family_connection( $step, $index );
        default:
            return apply_filters( 'hss_render_step_' . $step['layout'], '', $step, $index );
    }
}

// ── Layout: five-years ─────────────────────────────────────────────────────

function hss_render_step_five_years( $step, $index ) {
    $allowed     = wp_kses_allowed_html( 'post' );
    $heading_1   = isset( $step['heading_line_1'] )     ? esc_html( $step['heading_line_1'] )         : '';
    $heading_2   = isset( $step['heading_line_2'] )     ? esc_html( $step['heading_line_2'] )         : '';
    $body        = isset( $step['body'] )               ? wp_kses( $step['body'], $allowed )           : '';
    $btn_text    = isset( $step['button_text'] )        ? esc_html( $step['button_text'] )             : '';
    $btn_url     = isset( $step['button_url'] )         ? esc_url( $step['button_url'] )               : '#';
    $quote       = isset( $step['quote'] )              ? esc_html( $step['quote'] )                   : '';
    $attribution = isset( $step['quote_attribution'] )  ? esc_html( $step['quote_attribution'] )       : '';
    $arrow_img   = isset( $step['arrow_img_url'] )      ? esc_url( $step['arrow_img_url'] )            : '';

    ob_start();
    ?>
    <div
        class="hss-step hss-step--five-years"
        role="group"
        aria-label="<?php echo esc_attr( $heading_1 . ' ' . $heading_2 ); ?>"
        data-hss-step="<?php echo esc_attr( $index ); ?>"
    >
        <div class="hss-content">

            <div class="hss-heading">
                <h2><?php echo $heading_1; ?><br><?php echo $heading_2; ?></h2>
                <p>A place where kids can be kids</p>
            </div>

            <div class="hss-cols">

                <div class="hss-col hss-col--text">
                    <div class="hss-body"><?php echo $body; ?></div>
                    <?php if ( $btn_text ) : ?>
                    <a class="hss-btn" href="<?php echo $btn_url; ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo $btn_text; ?>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8 8" fill="none" aria-hidden="true" focusable="false">
                            <path d="M1 7L7 1M7 1H2M7 1V6" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <?php endif; ?>
                </div>

                <div class="hss-col hss-col--quote">
                    <blockquote class="hss-quote">
                        <p class="hss-quote__text"><?php echo $quote; ?></p>
                        <?php if ( $attribution ) : ?>
                        <cite class="hss-quote__attribution"><?php echo $attribution; ?></cite>
                        <?php endif; ?>
                    </blockquote>
                 
                </div>

            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// ── Layout: before-camp ────────────────────────────────────────────────────

function hss_render_step_before_camp( $step, $index ) {
    $allowed   = wp_kses_allowed_html( 'post' );
    $heading   = isset( $step['heading'] )   ? esc_html( $step['heading'] )             : '';
    $body      = isset( $step['body'] )      ? wp_kses( $step['body'], $allowed )        : '';
    $stats     = isset( $step['stats'] )     ? (array) $step['stats']                   : [];
    $photo_url = isset( $step['photo_url'] ) ? esc_url( $step['photo_url'] )            : '';
    $photo_alt = isset( $step['photo_alt'] ) ? esc_attr( $step['photo_alt'] )           : '';

    ob_start();
    ?>
    <div
        class="hss-step hss-step--before-camp"
        role="group"
        aria-label="<?php echo esc_attr( $heading ); ?>"
        data-hss-step="<?php echo esc_attr( $index ); ?>"
    >
        <div class="hss-content">
            <div class="hss-cols">

                <div class="hss-col hss-col--before-text">
                    <div class="hss-before-inner">
                        <?php if ( $heading ) : ?>
                        <h2><?php echo $heading; ?></h2>
                        <?php endif; ?>
                        <div class="hss-body"><?php echo $body; ?></div>
                    </div>
                    <?php if ( ! empty( $stats ) ) : ?>
                    <div class="hss-stats">
                        <?php foreach ( $stats as $stat ) :
                            $num   = isset( $stat['num'] )   ? esc_html( $stat['num'] )   : '';
                            $label = isset( $stat['label'] ) ? esc_html( $stat['label'] ) : '';
                        ?>
                        <div class="hss-stat">
                            <span class="hss-stat__num"><?php echo $num; ?></span>
                            <span class="hss-stat__label"><?php echo $label; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ( $photo_url ) : ?>
                <div class="hss-col hss-col--photo">
                    <img src="<?php echo $photo_url; ?>" alt="<?php echo $photo_alt; ?>">
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// ── Layout: after-camp ─────────────────────────────────────────────────────

function hss_render_step_after_camp( $step, $index ) {
    $allowed      = wp_kses_allowed_html( 'post' );
    $heading      = isset( $step['heading'] )      ? esc_html( $step['heading'] )           : '';
    $body         = isset( $step['body'] )         ? wp_kses( $step['body'], $allowed )      : '';
    $stats_label  = isset( $step['stats_label'] )  ? esc_html( $step['stats_label'] )        : '';
    $stats        = isset( $step['stats'] )        ? (array) $step['stats']                 : [];
    $photo_url    = isset( $step['photo_url'] )    ? esc_url( $step['photo_url'] )           : '';
    $photo_alt    = isset( $step['photo_alt'] )    ? esc_attr( $step['photo_alt'] )          : '';
    $photo_side   = ( isset( $step['photo_side'] ) && $step['photo_side'] === 'right' ) ? 'right' : 'left';
    $photo_square = ! empty( $step['photo_square'] );

    $photo_classes = 'hss-col hss-col--photo';
    if ( $photo_square ) {
        $photo_classes .= ' hss-col--photo--square';
    }

    ob_start();
    ?>
    <div
        class="hss-step hss-step--after-camp"
        role="group"
        aria-label="<?php echo esc_attr( $heading ); ?>"
        data-hss-step="<?php echo esc_attr( $index ); ?>"
    >
        <div class="hss-content">
            <div class="hss-cols<?php echo $photo_side === 'right' ? ' hss-cols--photo-right' : ''; ?>">

                <?php if ( $photo_url && $photo_side === 'left' ) : ?>
                <div class="<?php echo $photo_classes; ?>">
                    <img src="<?php echo $photo_url; ?>" alt="<?php echo $photo_alt; ?>">
                </div>
                <?php endif; ?>

                <div class="hss-col hss-col--after-text">
                    <div class="hss-after-inner">
                        <?php if ( $heading ) : ?>
                        <h2><?php echo $heading; ?></h2>
                        <?php endif; ?>
                        <div class="hss-body"><?php echo $body; ?></div>
                    </div>
                    <?php if ( ! empty( $stats ) ) : ?>
                    <div class="hss-stats-group">
                        <?php if ( $stats_label ) : ?>
                        <p class="hss-stats-label"><?php echo $stats_label; ?></p>
                        <?php endif; ?>
                        <div class="hss-stats">
                            <?php foreach ( $stats as $stat ) :
                                $num   = isset( $stat['num'] )   ? esc_html( $stat['num'] )   : '';
                                $label = isset( $stat['label'] ) ? esc_html( $stat['label'] ) : '';
                            ?>
                            <div class="hss-stat">
                                <span class="hss-stat__num"><?php echo $num; ?></span>
                                <span class="hss-stat__label"><?php echo $label; ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

               

            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// ── Layout: family-connection ──────────────────────────────────────────────

function hss_render_step_family_connection( $step, $index ) {
    $allowed  = wp_kses_allowed_html( 'post' );
    $heading  = isset( $step['heading'] ) ? esc_html( $step['heading'] )        : '';
    $body     = isset( $step['body'] )    ? wp_kses( $step['body'], $allowed )   : '';
    $photos   = isset( $step['photos'] )  ? (array) $step['photos']              : [];

    ob_start();
    ?>
    <div
        class="hss-step hss-step--family-connection"
        role="group"
        aria-label="<?php echo esc_attr( $heading ); ?>"
        data-hss-step="<?php echo esc_attr( $index ); ?>"
    >
        <div class="hss-content">
            <div class="hss-cols">

                <div class="hss-col hss-col--fc-text">
                    <?php if ( $heading ) : ?>
                    <h2><?php echo $heading; ?></h2>
                    <?php endif; ?>
                    <?php if ( $body ) : ?>
                    <div class="hss-body"><?php echo $body; ?></div>
                    <?php endif; ?>
                </div>

                <div class="hss-col hss-col--fc-grid">
                    <?php foreach ( array_slice( $photos, 0, 4 ) as $photo ) :
                        $url = isset( $photo['url'] ) ? esc_url( $photo['url'] )  : '';
                        $alt = isset( $photo['alt'] ) ? esc_attr( $photo['alt'] ) : '';
                    ?>
                    <div class="hss-fc-photo">
                        <?php if ( $url ) : ?>
                        <img src="<?php echo $url; ?>" alt="<?php echo $alt; ?>">
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// ── Default steps data ─────────────────────────────────────────────────────

function hss_default_steps() {
    return [
        [
            'layout'            => 'five-years',
            'heading_line_1'    => '5 Years of',
            'heading_line_2'    => "Camp Mariposa: Aaron's Place",
            'body'              =>
                '<p>One in four children grow up in a home affected by substance use disorder. Without the right support, those experiences can shape mental health, relationships, and increase the likelihood of substance use challenges later in life.</p>'
                . '<p>Five years ago, when our founder Justin Phillips learned about Camp Mariposa, a national mentoring and prevention program for youth affected by a family member&#39;s substance use, she knew young people in Indiana needed that same support. Since opening, the program has supported 111 young people across Indiana, offering a safe space during some of the most uncertain moments in their lives.</p>'
                . '<p>Camp Mariposa gives youth something many have never had before: a place to process their experiences, connect with others who understand them, and simply be kids.</p>',
            'button_text'       => "Visit Aaron's Place",
            'button_url'        => '#',
            'quote'             => "\u{201C}I guess I have really found my people.\u{201D}",
            'quote_attribution' => 'Camper',
            'arrow_img_url'     => plugin_dir_url( __FILE__ ) . '../img/horizontal-scroll/odl-campers-around-campfire1.webp',
        ],
        [
            'layout'    => 'before-camp',
            'heading'   => 'BEFORE CAMP',
            'body'      =>
                '<p>Camp Mariposa provides a consistent space where young people can build trust with adults who show up for them and connect with peers who understand their experiences.</p>'
                . '<p>Many campers arrive already navigating grief, family separation, and difficult changes at home:</p>',
            'stats'     => [
                [ 'num' => '95%', 'label' => 'have a parent impacted by substance use disorder' ],
                [ 'num' => '76%', 'label' => 'have experienced grief or loss' ],
                [ 'num' => '52%', 'label' => 'have experienced abuse or neglect' ],
                [ 'num' => '47%', 'label' => 'have been involved in foster or kinship care' ],
            ],
            'photo_url' => plugin_dir_url( __FILE__ ) . '../img/horizontal-scroll/odl-campers-around-campfire1.webp',
            'photo_alt' => '',
        ],
        [
            'layout'      => 'after-camp',
            'heading'     => 'AFTER CAMP',
            'body'        =>
                '<p>Despite these challenges, young people keep coming back. <strong>Seventy-five percent return year after year</strong>, building relationships with a community of <strong>55 trained mentors</strong> who show up consistently to listen, support, and walk alongside them.</p>',
            'stats_label' => 'Evaluation data shows:',
            'stats'       => [
                [ 'num' => '99%',     'label' => 'say there is at least one adult at camp they can go to for help' ],
                [ 'num' => '88%',     'label' => 'say they learned a lot from participating' ],
                [ 'num' => '65%',     'label' => 'report using healthy coping strategies to manage stress' ],
                [ 'num' => '96-100%', 'label' => 'report remaining substance-free in the previous six months' ],
            ],
            'photo_url'   => plugin_dir_url( __FILE__ ) . '../img/horizontal-scroll/odl-campers-with-squishmallows3.webp',
            'photo_alt'   => '',
            'photo_side'  => 'right',
        ],
        [
            'layout'  => 'family-connection',
            'heading' => 'FAMILY CONNECTION',
            'body'    =>
                '<p>Over the past year, Camp Mariposa hosted <strong>ten family events</strong> that created space for families to learn together and talk openly about how substance use disorder has affected their lives. For many families, this was the first time they talked openly about it.</p>',
            'photos'  => [
                [ 'url' => plugin_dir_url( __FILE__ ) . '../img/horizontal-scroll/odl-camper-smiling-on-hammock-closeup2.webp', 'alt' => '' ],
                [ 'url' => plugin_dir_url( __FILE__ ) . '../img/horizontal-scroll/odl-campers-with-squishmallows3.webp', 'alt' => '' ],
                [ 'url' => plugin_dir_url( __FILE__ ) . '../img/horizontal-scroll/odl-campers-hiking4.webp', 'alt' => '' ],
                [ 'url' => plugin_dir_url( __FILE__ ) . '../img/horizontal-scroll/odl-campers-halloween-pumpkin-carving4.webp', 'alt' => '' ],
            ],
        ],
        [
            'layout'       => 'after-camp',
            'heading'      => 'APEX: CONTINUING THE CONNECTION',
            'body'         =>
                '<p>As campers grow older, the need for connection and support continues.</p>'
                . '<p>In 2025, participation grew from 5 youth to 17, which represents more than <strong>240% growth</strong> in under a year. <strong>67%</strong> of those participants came from Camp Mariposa: Aaron\'s Place.</p>',
            'stats_label'  => '',
            'stats'        => [],
            'photo_url'    => plugin_dir_url( __FILE__ ) . '../img/horizontal-scroll/odl-campers-by-river5.webp',
            'photo_alt'    => '',
            'photo_side'   => 'left',
            'photo_square' => true,
        ],
    ];
}
