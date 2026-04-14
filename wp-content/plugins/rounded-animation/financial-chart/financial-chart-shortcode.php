<?php
/**
 * Financial Chart — WordPress Shortcode
 *
 * Usage: [financial_chart]
 *
 * Renders a three-column layout:
 *  Left  — Income heading + chart instructions
 *  Center — Interactive SVG pie chart with clickable pins
 *  Right  — Expenses breakdown list
 *
 * Customise via the 'fc_block_config' filter:
 *
 *   add_filter( 'fc_block_config', function( $config ) {
 *       $config['title']          = 'Financials';
 *       $config['income_heading'] = 'INCOME';
 *       $config['income_note']    = 'Tap the icons to see details.';
 *       $config['slices'][]       = [
 *           'label' => 'CATEGORY',
 *           'color' => '#c82a3c',
 *           'value' => 1000000,     // numeric, used to calculate slice angle
 *           'items' => [ 'Line item: $100.00' ],
 *       ];
 *       return $config;
 *   } );
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Asset registration ─────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', 'fc_register_assets' );

function fc_register_assets() {
    $base = plugin_dir_url( __FILE__ );

    wp_register_style(
        'financial-chart',
        $base . 'financial-chart.css',
        [],
        '1.0.0'
    );

    wp_register_script(
        'financial-chart',
        $base . 'financial-chart.js',
        [],
        '1.0.0',
        true
    );
}

// ── Shortcode ──────────────────────────────────────────────────────────────

add_shortcode( 'financial_chart', 'fc_render_shortcode' );

function fc_render_shortcode( $atts ) {
    wp_enqueue_style( 'financial-chart' );
    wp_enqueue_script( 'financial-chart' );

    $config = apply_filters( 'fc_block_config', fc_default_config() );

    $title          = isset( $config['title'] )          ? esc_html( $config['title'] )                              : 'Financials';
    $income_heading = isset( $config['income_heading'] )  ? esc_html( $config['income_heading'] )                    : 'INCOME';
    $income_note    = isset( $config['income_note'] )     ? esc_html( $config['income_note'] )                       : '';
    $income_items   = isset( $config['income_items'] )    ? array_map( 'esc_html', (array) $config['income_items'] ) : [];
    $slices         = isset( $config['slices'] )          ? (array) $config['slices']                                : [];

    // Sanitise slices and encode for JS
    $safe_slices = array_map( function( $s ) {
        return [
            'label' => isset( $s['label'] ) ? esc_html( $s['label'] ) : '',
            'color' => isset( $s['color'] ) ? esc_attr( $s['color'] ) : '#cccccc',
            'value' => isset( $s['value'] ) ? (float) $s['value']     : 0,
            'items' => isset( $s['items'] ) ? array_map( 'esc_html', (array) $s['items'] ) : [],
        ];
    }, $slices );

    $slices_json = wp_json_encode( $safe_slices );

    ob_start();
    ?>
    <section class="fc-block" aria-label="<?php echo esc_attr( $title ); ?>">

        <h2 class="fc-title"><?php echo $title; ?></h2>

        <div class="fc-layout">

            <!-- Left: Income -->
            <div class="fc-col fc-col--income">
                <div class="fc-income-header">
                    <h3 class="fc-col-heading"><?php echo $income_heading; ?></h3>
                    <?php if ( $income_note ) : ?>
                    <p class="fc-income-note"><?php echo $income_note; ?></p>
                    <?php endif; ?>
                </div>
             
                            <!-- Centre: Pie chart -->
            <div class="fc-col fc-col--chart">
                <div
                    class="fc-chart-wrap"
                    data-slices="<?php echo esc_attr( $slices_json ); ?>"
                    role="img"
                    aria-label="Expenses pie chart"
                >
                    <!-- SVG + pins injected by financial-chart.js -->
                </div>
            </div>
            </div>



            <!-- Right: Expenses breakdown -->
            <div class="fc-col fc-col--expenses">
                <h3 class="fc-col-heading">EXPENSES</h3>
                <ul class="fc-expense-list">
                    <?php foreach ( $safe_slices as $slice ) : ?>
                    <li class="fc-expense-item">
                        <span
                            class="fc-expense-dot"
                            style="background-color: <?php echo esc_attr( $slice['color'] ); ?>;"
                            aria-hidden="true"
                        ></span>
                        <div class="fc-expense-body">
                            <strong class="fc-expense-label"><?php echo $slice['label']; ?></strong>
                            <?php if ( ! empty( $slice['items'] ) ) : ?>
                            <ul class="fc-expense-items">
                                <?php foreach ( $slice['items'] as $item ) :
                                    $item = preg_replace(
                                        '/: (\$[\d,]+\.\d{2})/',
                                        ': <strong>$1</strong>',
                                        $item
                                    );
                                ?>
                                <li><?php echo $item; ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

        </div>
    </section>
    <?php
    return ob_get_clean();
}

// ── Default config ─────────────────────────────────────────────────────────

function fc_default_config() {
    return [
        'title'          => 'Financials',
        'income_heading' => 'INCOME',
        'income_note'    => 'Hover or tap the icons on the pie chart below to see the details.',
        'income_items'   => [
            'Government Contracts - $6,800,990.00',
            'Grant Income (Private/Corporate) - $90,000.00',
            'Fees & Services - $28,200.00',
            'Private Donations - $129,780.00',
            'Interest & Investment Income - $333,208.00',
            'Training and Education - $366,270.00',
            'Events - $120,322.00',
            'Unrestricted Donations - $120,780.00',
            'Other - $56,120.00',
        ],
        'slices'         => [
            [
                'label' => 'EDUCATION',
                'color' => '#3d7fc4',
                'value' => 559930,
                'items' => [
                    'Facilitator Trainings: $190,450.00',
                    'CE Courses: $369,480.00',
                ],
            ],
            [
                'label' => 'YOUTH PREVENTION',
                'color' => '#d4a020',
                'value' => 672314,
                'items' => [
                    'TINAD + PreVenture: $210,763.00',
                    'Community Outreach Education: $139,376.00',
                    'Marketing: $322,175.00',
                ],
            ],
            [
                'label' => 'SUPPORT',
                'color' => '#c82a3c',
                'value' => 5970902,
                'items' => [
                    'Lifeline for Loss: $28,700.00',
                    'CRAFT Family Support: $190,840.00',
                    'Naloxone Kits: $4,527,690.00',
                    'Test Strips: $169,807.00',
                    "Aaron's Place: $350,402.00",
                    'Heart Rock Justus Family Recovery Center: $494,214.00',
                    'MACRO-B: $124,887.00',
                    'Peer Grief Support: $29,051.00',
                    'CHARIOT: $55,311.00',
                ],
            ],
            [
                'label' => 'ADVOCACY',
                'color' => '#4ab8d0',
                'value' => 287877,
                'items' => [
                    'Community Events: $287,877.00',
                ],
            ],
            [
                'label' => 'OPERATIONS',
                'color' => '#d0d8e4',
                'value' => 1018728,
                'items' => [
                    'Administrative / Management / Staff: $878,474.00',
                    'Depreciation: $140,254.00',
                ],
            ],
        ],
    ];
}
