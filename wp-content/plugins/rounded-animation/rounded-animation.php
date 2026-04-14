<?php
/**
 * Plugin Name: ODL Shortcodes
 * Description: Animated shortcode library. Includes [rounded_animation], [home_tabs_carousel], [video_fade_text], [horizontal_scroll_steps], [program_stats], [impact_grid], [financial_chart], [lottie_kate], [sticky_letter], [thank_you], [volunteers], [annual_impact] and [national_prevention].
 * Version:     1.2.0
 * Author:      griddl
 * License:     GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Returns a data-URI checkerboard placeholder image.
 * Use wherever a real image URL is not yet available.
 *
 * @return string  A valid value for src="..." attributes.
 */
function shortcode_lib_placeholder() {
    return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600'%3E%3Cdefs%3E%3Cpattern id='c' width='40' height='40' patternUnits='userSpaceOnUse'%3E%3Crect width='20' height='20' fill='%23c8cfd8'/%3E%3Crect x='20' y='20' width='20' height='20' fill='%23c8cfd8'/%3E%3Crect x='20' y='0' width='20' height='20' fill='%23dce2e8'/%3E%3Crect x='0' y='20' width='20' height='20' fill='%23dce2e8'/%3E%3C/pattern%3E%3C/defs%3E%3Crect width='800' height='600' fill='url(%23c)'/%3E%3C/svg%3E";
}

require_once plugin_dir_path( __FILE__ ) . 'rounded-animation/rounded-animation-shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'home-tabs-carousel/home-tabs-carousel-shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'video-fade-text/video-fade-text-shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'horizontal-scroll-steps/horizontal-scroll-steps-shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'program-stats/program-stats-shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'impact-grid/impact-grid-shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'financial-chart/financial-chart-shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'lottie-kate/lottie-kate-shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'sticky-letter/sticky-letter-shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'thank-you/thank-you-shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'volunteers/volunteers-shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'annual-impact/annual-impact-shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'national-prevention/national-prevention-shortcode.php';
