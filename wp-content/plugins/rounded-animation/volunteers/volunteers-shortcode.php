<?php
/**
 * Volunteers Make It Possible — WordPress Shortcode
 *
 * Usage: [volunteers]
 *
 * Renders:
 *  1. Centred bold heading
 *  2. Full-width image (GSAP scale-from-centre entrance animation)
 *  3. Bold intro sentence + body paragraph (centred)
 *  4. Three-column stats row (number in red + label)
 *
 * Customise via the 'vm_block_config' filter:
 *
 *   add_filter( 'vm_block_config', function( $config ) {
 *       $config['heading']   = 'Custom heading';
 *       $config['image_url'] = get_template_directory_uri() . '/images/photo.jpg';
 *       $config['stats']     = [
 *           [ 'number' => '500+', 'label' => 'volunteers' ],
 *       ];
 *       return $config;
 *   } );
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Asset registration ─────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', 'vm_register_assets' );

function vm_register_assets() {
    $base = plugin_dir_url( __FILE__ );

    wp_register_style(
        'volunteers',
        $base . 'volunteers.css',
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

    wp_register_script(
        'volunteers',
        $base . 'volunteers.js',
        [ 'gsap', 'gsap-scrolltrigger', 'odl-counter' ],
        '1.0.0',
        true
    );
}

// ── Shortcode ──────────────────────────────────────────────────────────────

add_shortcode( 'volunteers', 'vm_render_shortcode' );

function vm_render_shortcode( $atts ) {
    wp_enqueue_style( 'volunteers' );
    wp_enqueue_script( 'volunteers' );

    $defaults = [
        'heading'   => 'Volunteers Make It Possible',
        'image_url' => plugin_dir_url( __FILE__ ) . '../img/odl-volunteers-make-it-possible.webp',
        'image_alt' => 'Volunteers at work',
        'intro'     => 'Saving lives takes a community. Volunteers make this work possible.',
        'body'      => '<p>From packing naloxone kits to supporting events and outreach, volunteers help carry this work into communities every day. Their time helps make prevention possible, puts life-saving tools into more hands, and strengthens support for families across Indiana.</p>',
        'stats'     => [
            [
                'number'    => '1,100+',
                'raw_count' => 1100,
                'suffix'    => '+',
                'label'     => 'volunteers supported Overdose Lifeline\'s work',
            ],
            [
                'number'    => '66',
                'raw_count' => 66,
                'suffix'    => '',
                'label'     => 'volunteer events brought people together across Indiana',
            ],
            [
                'number'    => '4,800+',
                'raw_count' => 4800,
                'suffix'    => '+',
                'label'     => 'volunteer hours helped expand prevention, harm reduction, and recovery support',
            ],
        ],
    ];

    $config = apply_filters( 'vm_block_config', $defaults );

    $allowed   = wp_kses_allowed_html( 'post' );
    $heading   = isset( $config['heading'] )   ? esc_html( $config['heading'] )        : '';
    $image_url = isset( $config['image_url'] ) ? esc_url( $config['image_url'] )       : '';
    $image_alt = isset( $config['image_alt'] ) ? esc_attr( $config['image_alt'] )      : '';
    $intro     = isset( $config['intro'] )     ? esc_html( $config['intro'] )          : '';
    $body      = isset( $config['body'] )      ? wp_kses( $config['body'], $allowed )  : '';
    $stats     = isset( $config['stats'] )     ? (array) $config['stats']              : [];

    ob_start();
    ?>
    <section class="vm-block" aria-label="<?php echo esc_attr( $heading ); ?>">

        <div class="vm-inner">

            <?php if ( $heading ) : ?>
            <h2 class="vm-heading"><?php echo $heading; ?></h2>
            <?php endif; ?>

            <?php if ( $image_url ) : ?>
            <div class="vm-image-wrap" aria-hidden="true">
                <img
                    class="vm-image"
                    src="<?php echo $image_url; ?>"
                    alt="<?php echo $image_alt; ?>"
                    loading="lazy"
                    decoding="async"
                >
            </div>
            <?php endif; ?>

            <div class="vm-text">
                <?php if ( $intro ) : ?>
                <p class="vm-intro"><?php echo $intro; ?></p>
                <?php endif; ?>

                <?php if ( $body ) : ?>
                <div class="vm-body"><?php echo $body; ?></div>
                <?php endif; ?>
            </div>

            <?php if ( ! empty( $stats ) ) : ?>
            <div class="vm-stats" aria-label="Key statistics">
                <?php foreach ( $stats as $stat ) :
                    $number    = isset( $stat['number'] )    ? esc_html( $stat['number'] )          : '';
                    $label     = isset( $stat['label'] )     ? esc_html( $stat['label'] )           : '';
                    $raw_count = isset( $stat['raw_count'] ) ? intval( $stat['raw_count'] )         : null;
                    $suffix    = isset( $stat['suffix'] )    ? esc_attr( $stat['suffix'] )          : '';
                ?>
                <div class="vm-stat">
                    <span class="vm-stat__number"
                        <?php if ( ! is_null( $raw_count ) ) : ?>
                            data-count="<?php echo $raw_count; ?>"
                            data-suffix="<?php echo $suffix; ?>"
                        <?php endif; ?>
                    ><?php echo $number; ?></span>
                    <span class="vm-stat__label"><?php echo $label; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div>

    </section>
    <?php
    return ob_get_clean();
}
