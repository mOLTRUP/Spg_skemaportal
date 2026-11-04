<?php
/**
 * Plugin Name: ITD Skemaportal – Godkendt GitHub-layout
 * Description: Viser de godkendte GitHub-sider på /skema uden at ændre WordPress-databasen. Prototype til videre SQL-integration.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: ITD
 * Text Domain: itd-skema-github
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Keep the original HTML/CSS/JavaScript intact. Serve the exact GitHub pages
 * from inside WordPress so relative links and JSON imports keep working.
 *
 * This baseline deliberately DOES NOT create or modify SQL tables. Its sample
 * data and edits remain stored in the visitor's browser (localStorage).
 */
function itd_gh_portal_route() {
    $request_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/';
    $request_path = wp_parse_url($request_uri, PHP_URL_PATH);
    $home_path = wp_parse_url(home_url('/'), PHP_URL_PATH);

    if (!is_string($request_path) || !is_string($home_path)) {
        return;
    }

    $prefix = rtrim($home_path, '/') . '/skema';
    if ($request_path !== $prefix && strpos($request_path, $prefix . '/') !== 0) {
        return;
    }

    $relative = substr($request_path, strlen($prefix));
    $routes = array(
        '/' => array('index.html', 'text/html; charset=UTF-8'),
        '/index.html' => array('index.html', 'text/html; charset=UTF-8'),
        '/virksomhed.html' => array('virksomhed.html', 'text/html; charset=UTF-8'),
        '/udgiv.html' => array('udgiv.html', 'text/html; charset=UTF-8'),
        '/skabeloneditor.html' => array('skabeloneditor.html', 'text/html; charset=UTF-8'),
        '/data/kundetilfredshed_v1.json' => array('data/kundetilfredshed_v1.json', 'application/json; charset=UTF-8'),
    );

    if ($relative === '') {
        wp_safe_redirect(home_url('/skema/'), 302);
        exit;
    }

    if (!isset($routes[$relative])) {
        return;
    }

    // Restrict the prototype to existing WP administrators. Permissions for
    // ITD/company/respondent are a separate future implementation step.
    if (!is_user_logged_in()) {
        auth_redirect();
        exit;
    }
    if (!current_user_can('manage_options')) {
        wp_die(
            'Denne testversion af Skemaportalen kræver WordPress-administratoradgang.',
            'Adgang nægtet',
            array('response' => 403)
        );
    }

    $filename = __DIR__ . '/' . $routes[$relative][0];
    if (!is_readable($filename)) {
        status_header(500);
        wp_die('Skemaportalens GitHub-filer mangler i den installerede pluginpakke.');
    }

    status_header(200); // Also corrects WordPress's 404 for virtual subpages.
    nocache_headers();
    header('Content-Type: ' . $routes[$relative][1], true);
    header('X-Content-Type-Options: nosniff', true);
    header('X-Robots-Tag: noindex, nofollow', true);
    readfile($filename);
    exit;
}
add_action('template_redirect', 'itd_gh_portal_route', -1000);

function itd_gh_portal_admin_menu() {
    add_menu_page(
        'ITD Skemaportal – GitHub-layout',
        'ITD Portal (GitHub)',
        'manage_options',
        'itd-gh-portal',
        'itd_gh_portal_admin_page',
        'dashicons-feedback',
        58
    );
}
add_action('admin_menu', 'itd_gh_portal_admin_menu');

function itd_gh_portal_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    $url = esc_url(home_url('/skema/'));
    echo '<div class="wrap"><h1>ITD Skemaportal – godkendt GitHub-layout</h1>';
    echo '<p>De fire godkendte GitHub-sider vises uden ændringer i design og funktionalitet.</p>';
    echo '<p><a class="button button-primary" href="' . $url . '" target="_blank" rel="noopener">Åbn Skemaportalen</a></p>';
    echo '<p><strong>Bemærk:</strong> Dette er layout- og funktionstesten fra GitHub. ';
    echo 'Skabeloner, virksomheder, udsendelser og testsvar gemmes endnu i browserens lokale lager. ';
    echo 'De deles ikke mellem brugere og gemmes ikke i SQL. Brug derfor kun testdata.</p>';
    echo '<p>Pluginet ændrer ikke eksisterende WordPress-tabeller, brugere eller tidligere testdata. ';
    echo 'Den tidligere Skemaportal-test bør deaktiveres for at undgå forvirring.</p></div>';
}

function itd_gh_portal_plugin_links($links) {
    array_unshift($links, '<a href="' . esc_url(home_url('/skema/')) . '">Åbn portalen</a>');
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'itd_gh_portal_plugin_links');
