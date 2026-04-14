<?php
/**
 * Program Stats — WordPress Shortcode
 *
 * Usage: [program_stats]
 *
 * Block data is extensible via the 'ps_block_config' filter:
 *
 *   add_filter( 'ps_block_config', function( $config ) {
 *       $config['heading']     = 'Custom Heading';
 *       $config['body']        = '<p>Custom body text.</p>';
 *       $config['btn_text']    = 'Learn More';
 *       $config['btn_url']     = 'https://example.com';
 *       $config['stats_label'] = 'Over the past year:';
 *       $config['stats']       = [
 *           [ 'num' => '51', 'label' => 'women supported' ],
 *       ];
 *       $config['photo_url']   = 'https://example.com/photo.jpg';
 *       $config['photo_alt']   = 'Description of photo';
 *       return $config;
 *   } );
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Asset registration ─────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', 'ps_register_assets' );

function ps_register_assets() {
    $base = plugin_dir_url( __FILE__ );

    wp_register_style(
        'program-stats',
        $base . 'program-stats.css',
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

    if ( ! wp_script_is( 'odl-counter', 'registered' ) ) {
        wp_register_script(
            'odl-counter',
            plugin_dir_url( __FILE__ ) . '../utils/counter-animation.js',
            [ 'gsap', 'gsap-scrolltrigger' ],
            '1.0.0',
            true
        );
    }

    if ( ! wp_style_is( 'odl-image-reveal', 'registered' ) ) {
        wp_register_style(
            'odl-image-reveal',
            plugin_dir_url( __FILE__ ) . '../utils/image-reveal-animation.css',
            [],
            '1.0.0'
        );
    }

    if ( ! wp_script_is( 'odl-image-reveal', 'registered' ) ) {
        wp_register_script(
            'odl-image-reveal',
            plugin_dir_url( __FILE__ ) . '../utils/image-reveal-animation.js',
            [],
            '1.0.0',
            true
        );
    }

    wp_register_script(
        'program-stats',
        $base . 'program-stats.js',
        [ 'odl-counter', 'odl-image-reveal' ],
        '1.0.0',
        true
    );
}

/**
 * Extrai o inteiro e o sufixo de uma string de número.
 * Ex: '1,100+' → [ 'raw' => 1100, 'suffix' => '+' ]
 *     '51'     → [ 'raw' => 51,   'suffix' => '' ]
 *
 * @param  string $num_string
 * @return array{ raw: int, suffix: string }
 */
function ps_parse_stat_num( $num_string ) {
    $digits_only = preg_replace( '/[^0-9]/', '', $num_string );
    $suffix      = preg_replace( '/[0-9,\s]/', '', $num_string );

    return [
        'raw'    => intval( $digits_only ),
        'suffix' => trim( $suffix ),
    ];
}

// ── Shortcode ──────────────────────────────────────────────────────────────

add_shortcode( 'program_stats', 'ps_render_shortcode' );

function ps_render_shortcode( $atts ) {
    wp_enqueue_style( 'program-stats' );
    wp_enqueue_style( 'odl-image-reveal' );
    wp_enqueue_script( 'program-stats' );

    $defaults = [
        'heading'     => 'Support That Grows With Families',
        'body'        =>
            '<p>Heart Rock Justus Family Recovery Center provides a place where women and their children can stay together during recovery. Families reconnect, children rebuild trust, and mothers begin rebuilding their lives with their children beside them.</p>',
        'btn_text'    => 'Visit Heart Rock',
        'btn_url'     => 'https://heartrockrecovery.org/',
        'btn_target'  => '_blank',
        'stats_label' => 'Over the past year:',
        'stats'       => [
            [ 'num' => '51', 'label' => 'women and 11 children were supported' ],
            [ 'num' => '10', 'label' => 'women transitioned into independent or semi-independent housing' ],
             [ 'num' => '4',  'label' => 'babies were born into recovery' ],
            [ 'num' => '6',  'label' => 'families reunified' ],
           
        ],
        'photo_url'   => plugin_dir_url( __FILE__ ) . '../img/odl-heart-rock-group-photo-holiday.webp',
        'photo_alt'   => '',
    ];

    $config  = apply_filters( 'ps_block_config', $defaults );
    $allowed = wp_kses_allowed_html( 'post' );

    $heading     = isset( $config['heading'] )     ? esc_html( $config['heading'] )               : '';
    $body        = isset( $config['body'] )        ? wp_kses( $config['body'], $allowed )          : '';
    $btn_text    = isset( $config['btn_text'] )    ? esc_html( $config['btn_text'] )               : '';
    $btn_url     = isset( $config['btn_url'] )     ? esc_url( $config['btn_url'] )                 : '#';
    $btn_target  = isset( $config['btn_target'] )  ? esc_attr( $config['btn_target'] )             : '_blank';
    $stats_label = isset( $config['stats_label'] ) ? esc_html( $config['stats_label'] )            : '';
    $stats       = isset( $config['stats'] )       ? (array) $config['stats']                      : [];
    $photo_url   = isset( $config['photo_url'] )   ? esc_url( $config['photo_url'] )               : '';
    $photo_alt   = isset( $config['photo_alt'] )   ? esc_attr( $config['photo_alt'] )              : '';

    ob_start();
    ?>
    <section class="ps-block" aria-label="<?php echo esc_attr( $heading ); ?>">

        <div class="ps-top">
            <div class="ps-col ps-col--text">
                <?php if ( $heading ) : ?>
                <h2 class="ps-heading"><?php echo $heading; ?></h2>
                <?php endif; ?>

                <?php if ( $body ) : ?>
                <div class="ps-body"><?php echo $body; ?></div>
                <?php endif; ?>

                <?php if ( $btn_text ) : ?>
                <a
                    class="ps-btn"
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

            <div class="ps-col ps-col--stats">
                <?php if ( $stats_label ) : ?>
                <p class="ps-stats-label"><?php echo $stats_label; ?></p>
                <?php endif; ?>

                <?php if ( ! empty( $stats ) ) : ?>
                <div class="ps-stats">
                    <?php foreach ( $stats as $stat ) :
                        $num    = isset( $stat['num'] )   ? $stat['num']              : '';
                        $label  = isset( $stat['label'] ) ? esc_html( $stat['label'] ) : '';
                        $parsed = ps_parse_stat_num( $num );
                    ?>
                    <div class="ps-stat">
                        <span class="ps-stat__num"
                            data-count="<?php echo $parsed['raw']; ?>"
                            data-suffix="<?php echo esc_attr( $parsed['suffix'] ); ?>"
                        ><?php echo esc_html( $num ); ?></span>
                        <span class="ps-stat__label"><?php echo $label; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ( $photo_url ) : ?>
        <div class="ps-top ps-image-wrap">
            <img class="ps-image" src="<?php echo $photo_url; ?>" alt="<?php echo $photo_alt; ?>">
        </div>
        <?php endif; ?>

    </section>
    <?php
    return ob_get_clean();
}
