<?php
/**
 * Sticky Letter — WordPress Shortcode
 *
 * Usage: [sticky_letter]
 *
 * Renders a two-column "letter from" section:
 *  Left  — Image, sticky while the right column scrolls
 *  Right — Eyebrow, heading, letter body paragraphs, signature, author
 *
 * Customise via the 'sl_block_config' filter:
 *
 *   add_filter( 'sl_block_config', function( $config ) {
 *       $config['image_url']    = get_template_directory_uri() . '/images/founder.jpg';
 *       $config['eyebrow']      = 'A Letter from Our Founder';
 *       $config['heading']      = 'Five Years of Impact';
 *       $config['body']         = '<p>Dear Friends...</p>';
 *       $config['author_name']  = 'Justin Phillips';
 *       $config['author_title'] = 'Founder & Executive Director';
 *       return $config;
 *   } );
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ── Asset registration ─────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', 'sl_register_assets' );

function sl_register_assets() {
    wp_register_style(
        'sticky-letter',
        plugin_dir_url( __FILE__ ) . 'sticky-letter.css',
        [],
        '1.0.0'
    );
}

// ── Shortcode ──────────────────────────────────────────────────────────────

add_shortcode( 'sticky_letter', 'sl_render_shortcode' );

function sl_render_shortcode( $atts ) {
    wp_enqueue_style( 'sticky-letter' );

    $defaults = [
        'image_url'    => plugin_dir_url( __FILE__ ) . '../img/img-sticky-letter.png',
        'image_alt'    => '',
        'eyebrow'      => 'A Letter from Our Founder',
        'heading'      => 'A message from our founder, Justin Phillips',
        'body'         =>
            '<p>There are no simple solutions to the overdose and substance use crisis. But across Indiana, I see every day that people survive and families recover when the right support is in place.</p>'
            . '<p>Real change happens when people feel safe enough to say they are suffering and supported enough to ask for help before, during, and after a crisis. The stronger the community and support around someone, the more likely recovery becomes possible.</p>'
            . '<p>For more than a decade, Overdose Lifeline has built programs that address the many layers of substance use disorder and the overdose crisis through overdose prevention, family support, and education. In 2025, that work continued to grow in the same way it began, guided by real experiences, real needs, and real voices.</p>'
            . '<p>Across every program, the approach is the same: listen, respond, and build support where it is needed most. From celebrating five years of Camp Mariposa: Aaron’s Place to expanding community response through MACRO-B and CHARIOT and launching new tools like KATE, we continue building the kind of support that helps families stay connected, communities respond faster, and more people have a chance to recover.</p>'
            . '<p>This report shares the stories behind that progress: the people, partnerships, and programs helping communities respond with knowledge, compassion, and practical support.</p>',
        'signature_url'   => plugin_dir_url( __FILE__ ) . 'signature.png',
        'signature_alt'   => 'Signature',
        'author_name'     => 'Justin Phillips',
        'author_title'    => 'Founder & Executive Director, Overdose Lifeline',
    ];

    $config = apply_filters( 'sl_block_config', $defaults );

    $allowed       = wp_kses_allowed_html( 'post' );
    $image_url     = isset( $config['image_url'] )    ? esc_url( $config['image_url'] )           : '';
    $image_alt     = isset( $config['image_alt'] )    ? esc_attr( $config['image_alt'] )          : '';
    $eyebrow       = isset( $config['eyebrow'] )      ? esc_html( $config['eyebrow'] )            : '';
    $heading       = isset( $config['heading'] )      ? esc_html( $config['heading'] )            : '';
    $body          = isset( $config['body'] )         ? wp_kses( $config['body'], $allowed )      : '';
    $sig_url       = isset( $config['signature_url'] ) ? esc_url( $config['signature_url'] )      : '';
    $sig_alt       = isset( $config['signature_alt'] ) ? esc_attr( $config['signature_alt'] )     : '';
    $author_name   = isset( $config['author_name'] )  ? esc_html( $config['author_name'] )        : '';
    $author_title  = isset( $config['author_title'] ) ? esc_html( $config['author_title'] )       : '';

    ob_start();
    ?>
    <section class="sl-block" aria-label="<?php echo esc_attr( $heading ); ?>">
        <div class="sl-wrapper">

            <!-- Left: scrollable letter content -->
            <div class="sl-col sl-col--content">

 

                <?php if ( $heading ) : ?>
                <h2 class="sl-heading"><?php echo $heading; ?></h2>
                <?php endif; ?>

                <?php if ( $body ) : ?>
                <div class="sl-body"><?php echo $body; ?></div>
                <?php endif; ?>

                <?php if ( $sig_url ) : ?>
                <div class="sl-signature">
                    <img
                        src="<?php echo $sig_url; ?>"
                        alt="<?php echo $sig_alt; ?>"
                        class="sl-signature__img"
                    >
                </div>
                <?php endif; ?>

            

            </div><!-- /.sl-col--content -->

            <!-- Right: sticky image -->
            <div class="sl-col sl-col--image">
                <?php if ( $image_url ) : ?>
                <img
                    class="sl-image"
                    src="<?php echo $image_url; ?>"
                    alt="<?php echo $image_alt; ?>"
                    loading="lazy"
                    decoding="async"
                >
                <?php endif; ?>
            </div>

        </div><!-- /.sl-wrapper -->
    </section>
    <?php
    return ob_get_clean();
}
