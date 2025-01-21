<?php
/**
 * Plugin Name: Custom URL Redirection
 * Plugin URI: https://cloudfire.co/contact
 * Description: A simple plugin to rewrite URLs and handle query parameters.
 * Version: 1.0.0
 * Author: Odysseus Ambut
 * Author URI: https://web-mech.net/support
 */

// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add custom rewrite rules.
 */
function custom_rewrite_rules() {
    $counties = [
        'Alamance', 'Alexander', 'Alleghany', 'Anson', 'Bladen', 'Brunswick',
        'Cabarrus', 'Caswell', 'Chatham', 'Columbus', 'Davidson', 'Davie', 
        'Durham', 'Forsyth', 'Gaston', 'Guilford', 'Iredell', 'Lincoln', 
        'Mecklenburg', 'Montgomery', 'New Hanover', 'Orange', 'Pender', 'Person', 
        'Randolph', 'Richmond', 'Rockingham', 'Rowan', 'Scotland', 'Stokes',
        'Union', 'Wilkes', 'Yadkin'
    ];

    foreach ($counties as $county) {
        add_rewrite_rule(
            '^shop-2025-plans/' . strtolower($county) . '/?$', // Match "shop-2025-plans/county"
            'index.php?pagename=shop-2025-plans&county=' . strtolower($county), // Map to "shop-2025-plans?county=county"
            'top'
        );
    }

    // Catch-all rule to redirect invalid counties to the main page
    add_rewrite_rule(
        '^shop-2025-plans/([^/]+)/?$',
        'index.php?pagename=shop-2025-plans',
        'top'
    );
}
add_action( 'init', 'custom_rewrite_rules' );

/**
 * Register custom query variables.
 *
 * @param array $vars Existing query variables.
 * @return array Modified query variables.
 */
function add_custom_query_vars( $vars ) {
    $vars[] = 'county';
    return $vars;
}
add_filter( 'query_vars', 'add_custom_query_vars' );

/**
 * Redirect based on county validity.
 */
add_action('template_redirect', function () {
    if (is_page('shop-2025-plans')) {
        $county = get_query_var('county');

        $valid_counties = [
            'alamance', 'alexander', 'alleghany', 'anson', 'bladen', 'brunswick',
            'cabarrus', 'caswell', 'chatham', 'columbus', 'davidson', 'davie', 
            'durham', 'forsyth', 'gaston', 'guilford', 'iredell', 'lincoln', 
            'mecklenburg', 'montgomery', 'new hanover', 'orange', 'pender', 'person', 
            'randolph', 'richmond', 'rockingham', 'rowan', 'scotland', 'stokes',
            'union', 'wilkes', 'yadkin'
        ];

        if ($county) {
            if (in_array(strtolower($county), $valid_counties)) {
                wp_redirect(home_url('/shop-2025-plans/' . $county . '/'));
                exit;
            } else {
                wp_redirect(home_url('/shop-2025-plans/'));
                exit;
            }
        }
    }
});

// add the county on the title to test for dynamic

add_filter('wpseo_enable_xml_sitemap_transient_caching', '__return_false');

add_filter('wpseo_sitemap_page_content', function ($content) {
    $counties = [
        'Alamance', 'Alexander', 'Alleghany', 'Anson', 'Bladen', 'Brunswick',
        'Cabarrus', 'Caswell', 'Chatham', 'Columbus', 'Davidson', 'Davie', 
        'Durham', 'Forsyth', 'Gaston', 'Guilford', 'Iredell', 'Lincoln', 
        'Mecklenburg', 'Montgomery', 'New Hanover', 'Orange', 'Pender', 'Person', 
        'Randolph', 'Richmond', 'Rockingham', 'Rowan', 'Scotland', 'Stokes',
        'Union', 'Wilkes', 'Yadkin'
    ];

    foreach ($counties as $county) {
        $content .= '<url>';
        $content .= '<loc>' . esc_url(home_url('/shop-2025-plans/' . $county . '/')) . '</loc>';
        $content .= '<lastmod>' . esc_html(current_time('Y-m-d\TH:i:s+00:00')) . '</lastmod>';
        $content .= '</url>';
    }

    return $content;
});

/**
 * Flush rewrite rules on plugin activation.
 */
function custom_rewrite_activation() {
    custom_rewrite_rules();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'custom_rewrite_activation' );

/**
 * Flush rewrite rules on plugin deactivation.
 */
function custom_rewrite_deactivation() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'custom_rewrite_deactivation' );

