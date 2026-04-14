<?php
/**
 * Impact Grid — WordPress Shortcode
 *
 * Usage: [impact_grid]
 *
 * Renders a 4-column grid of cards.
 * Default state: background image + title at bottom.
 * Hover state:   dark-blue panel + bullet list.
 *
 * Customise via the 'ig_block_config' filter:
 *
 *   add_filter( 'ig_block_config', function( $cards ) {
 *       // Each card:
 *       // [
 *       //   'title'     => 'CARD TITLE',
 *       //   'image_url' => 'https://example.com/photo.jpg',
 *       //   'image_alt' => 'Description',
 *       //   'items'     => [ 'Bullet one', 'Bullet two', ... ],
 *       // ]
 *       return $cards;
 *   } );
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Asset registration ─────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', 'ig_register_assets' );

function ig_register_assets() {
    wp_register_style(
        'impact-grid',
        plugin_dir_url( __FILE__ ) . 'impact-grid.css',
        [],
        '1.0.0'
    );
}

// ── Shortcode ──────────────────────────────────────────────────────────────

add_shortcode( 'impact_grid', 'ig_render_shortcode' );

function ig_render_shortcode( $atts ) {
    wp_enqueue_style( 'impact-grid' );

    $cards = apply_filters( 'ig_block_config', ig_default_cards() );

    ob_start();
    ?>
    <section class="ig-block" aria-label="Impact areas grid">
        <div class="ig-grid">
            <?php foreach ( $cards as $card ) :
                $title     = isset( $card['title'] )     ? esc_html( $card['title'] )     : '';
                $image_url = isset( $card['image_url'] ) ? esc_url( $card['image_url'] )  : '';
                $image_alt = isset( $card['image_alt'] ) ? esc_attr( $card['image_alt'] ) : '';
                $items     = isset( $card['items'] )     ? (array) $card['items']         : [];
            ?>
            <div class="ig-card" tabindex="0" aria-label="<?php echo esc_attr( $title ); ?>">

                <!-- Default state: image + title -->
                <div class="ig-card__front" aria-hidden="false">
                    <?php if ( $image_url ) : ?>
                    <img class="ig-card__image" src="<?php echo $image_url; ?>" alt="<?php echo $image_alt; ?>">
                    <?php endif; ?>
                    <div class="ig-card__gradient"></div>
                    <h3 class="ig-card__title"><?php echo $title; ?></h3>
                </div>

                <!-- Hover state: bullet list -->
                <div class="ig-card__back" aria-hidden="true">
                     <?php if ( ! empty( $items ) ) : ?>
                    <ul class="ig-card__list">
                        <?php foreach ( $items as $item ) : ?>
                        <li><?php echo esc_html( $item ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>

            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

// ── Default cards data ─────────────────────────────────────────────────────

function ig_default_cards() {
    return [
        [
            'title'     => 'RECOVERING TOGETHER',
            'image_url' => plugin_dir_url( __FILE__ ) . '../img/grid/odl-recovering-together-mother-and-daughter.webp',
            'image_alt' => 'Recovering Together',
            'items'     => [
                '51 women + 11 children supported at Heart Rock Justus Family Recovery Center',
                '4 babies born into recovery',
                '6 families reunified through recovery support',
            ],
        ],
        [
            'title'     => 'PREVENTING OVERDOSE AND SAVING LIVES',
            'image_url' => plugin_dir_url( __FILE__ ) . '../img/grid/odl-volunteers-packing.webp',
            'image_alt' => 'Preventing Overdose and Saving Lives',
            'items'     => [
                '302,885 naloxone kits distributed statewide',
                '166,198 fentanyl test strips distributed',
                '123,117 xylazine test strips distributed',
            ],
        ],
        [
            'title'     => 'EXPANDING PREVENTION THROUGH EDUCATION',
            'image_url' => plugin_dir_url( __FILE__ ) . '../img/grid/odl-education.webp',
            'image_alt' => 'Expanding Prevention Through Education',
            'items'     => [
                '4,400 education program enrollments in 2025',
                '600 partner organizations implementing ODL programs',
                '480 new trainers across 44 states and D.C.',
            ],
        ],
        [
            'title'     => 'TRAINING FACILITATORS NATIONWIDE',
            'image_url' => plugin_dir_url( __FILE__ ) . '../img/grid/odl-facilitator-training-kourtney.webp',
            'image_alt' => 'Training Facilitators Nationwide',
            'items'     => [
                '480 Train-the-Trainer facilitators prepared nationwide',
                '35 new out-of-state CRAFT facilitators',
                '106 new out-of-state TINAD facilitators',
            ],
        ],
        [
            'title'     => 'YOUTH PREVENTION PROGRAMS',
            'image_url' => plugin_dir_url( __FILE__ ) . '../img/grid/odl-tinad-speaker-panel.webp',
            'image_alt' => 'Youth Prevention Programs',
            'items'     => [
                '1,853 students reached through TINAD',
                '80 new PreVenture facilitators trained across 9 states',
                '8 Perspectives facilitators trained across 6 states',
                '2 national Perspectives film screenings',
            ],
        ],
        [
            'title'     => 'FAMILY SUPPORT',
            'image_url' => plugin_dir_url( __FILE__ ) . '../img/grid/odl-family-support.webp',
            'image_alt' => 'Family Support',
            'items'     => [
                '124 individuals completed CRAFT Facilitator Training',
                '16 CRAFT facilitator trainings delivered',
                '14 active CRAFT family support groups across Indiana',
            ],
        ],
        [
            'title'     => 'SUPPORT AFTER LOSS',
            'image_url' => plugin_dir_url( __FILE__ ) . '../img/grid/odl-support-after-loss.webp',
            'image_alt' => 'Support After Loss',
            'items'     => [
                '253 individuals received outreach after a substance-related loss',
                '9 newly bereaved individuals supported by Peer Grief Helper volunteers',
            ],
        ],
        [
            'title'     => 'COMMUNITY SUPPORT THROUGH EVENTS',
            'image_url' => plugin_dir_url( __FILE__ ) . '../img/grid/odl-community-events-family-indy-fuel.webp',
            'image_alt' => 'Community Support Through Events',
            'items'     => [
                'Nearly 800 community members engaged through annual events',
                '46% increase in community support year over year',
                '$343,156 raised through events and community support',
            ],
        ],
    ];
}
