<?php
/**
 * Theme Manager Functions
 *
 * @package Asad_Portfolio_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get All Themes
 */
function asad_get_all_themes() {
    $themes = wp_get_themes();
    $current_theme = wp_get_theme();

    $themes_data = array();

    foreach ($themes as $theme_slug => $theme) {
        $is_active = ($theme->get_stylesheet() === $current_theme->get_stylesheet());

        $themes_data[] = array(
            'slug'        => $theme->get_stylesheet(),
            'name'        => $theme->get('Name'),
            'description' => $theme->get('Description'),
            'version'     => $theme->get('Version'),
            'author'      => $theme->get('Author'),
            'author_uri'  => $theme->get('AuthorURI'),
            'theme_uri'   => $theme->get('ThemeURI'),
            'screenshot'  => $theme->get_screenshot(),
            'is_active'   => $is_active,
            'tags'        => $theme->get('Tags'),
        );
    }

    return $themes_data;
}

/**
 * AJAX: Activate Theme
 */
function asad_ajax_activate_theme() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('switch_themes')) {
        wp_send_json_error(array('message' => __('You do not have permission to switch themes.', 'asad-portfolio')));
    }

    $theme_slug = isset($_POST['theme']) ? sanitize_text_field($_POST['theme']) : '';

    if (empty($theme_slug)) {
        wp_send_json_error(array('message' => __('Invalid theme.', 'asad-portfolio')));
    }

    $theme = wp_get_theme($theme_slug);

    if (!$theme->exists()) {
        wp_send_json_error(array('message' => __('Theme does not exist.', 'asad-portfolio')));
    }

    switch_theme($theme_slug);

    wp_send_json_success(array('message' => __('Theme activated successfully.', 'asad-portfolio')));
}
add_action('wp_ajax_asad_activate_theme', 'asad_ajax_activate_theme');

/**
 * AJAX: Delete Theme
 */
function asad_ajax_delete_theme() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('delete_themes')) {
        wp_send_json_error(array('message' => __('You do not have permission to delete themes.', 'asad-portfolio')));
    }

    $theme_slug = isset($_POST['theme']) ? sanitize_text_field($_POST['theme']) : '';

    if (empty($theme_slug)) {
        wp_send_json_error(array('message' => __('Invalid theme.', 'asad-portfolio')));
    }

    $current_theme = wp_get_theme();
    if ($theme_slug === $current_theme->get_stylesheet()) {
        wp_send_json_error(array('message' => __('Cannot delete the active theme.', 'asad-portfolio')));
    }

    $result = delete_theme($theme_slug);

    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()));
    }

    wp_send_json_success(array('message' => __('Theme deleted successfully.', 'asad-portfolio')));
}
add_action('wp_ajax_asad_delete_theme', 'asad_ajax_delete_theme');

/**
 * AJAX: Install Theme from WordPress.org
 */
function asad_ajax_install_theme() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('install_themes')) {
        wp_send_json_error(array('message' => __('You do not have permission to install themes.', 'asad-portfolio')));
    }

    $theme_slug = isset($_POST['theme_slug']) ? sanitize_text_field($_POST['theme_slug']) : '';

    if (empty($theme_slug)) {
        wp_send_json_error(array('message' => __('Invalid theme slug.', 'asad-portfolio')));
    }

    // Include required files
    if (!class_exists('Theme_Upgrader')) {
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    }

    if (!function_exists('themes_api')) {
        require_once ABSPATH . 'wp-admin/includes/theme.php';
    }

    // Get theme info
    $api = themes_api('theme_information', array('slug' => $theme_slug));

    if (is_wp_error($api)) {
        wp_send_json_error(array('message' => $api->get_error_message()));
    }

    // Install theme
    $skin = new WP_Ajax_Upgrader_Skin();
    $upgrader = new Theme_Upgrader($skin);
    $result = $upgrader->install($api->download_link);

    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()));
    }

    if ($result === false) {
        wp_send_json_error(array('message' => __('Theme installation failed.', 'asad-portfolio')));
    }

    wp_send_json_success(array('message' => __('Theme installed successfully.', 'asad-portfolio')));
}
add_action('wp_ajax_asad_install_theme', 'asad_ajax_install_theme');

/**
 * AJAX: Upload Theme ZIP
 */
function asad_ajax_upload_theme() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('install_themes')) {
        wp_send_json_error(array('message' => __('You do not have permission to upload themes.', 'asad-portfolio')));
    }

    if (!isset($_FILES['theme_zip'])) {
        wp_send_json_error(array('message' => __('No file uploaded.', 'asad-portfolio')));
    }

    $file = $_FILES['theme_zip'];

    // Check file type
    $allowed_types = array('application/zip', 'application/x-zip-compressed');
    if (!in_array($file['type'], $allowed_types)) {
        wp_send_json_error(array('message' => __('Only ZIP files are allowed.', 'asad-portfolio')));
    }

    // Include required files
    if (!class_exists('Theme_Upgrader')) {
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    }

    // Upload theme
    $skin = new WP_Ajax_Upgrader_Skin();
    $upgrader = new Theme_Upgrader($skin);
    $result = $upgrader->install($file['tmp_name']);

    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()));
    }

    if ($result === false) {
        wp_send_json_error(array('message' => __('Theme upload failed.', 'asad-portfolio')));
    }

    wp_send_json_success(array('message' => __('Theme uploaded successfully.', 'asad-portfolio')));
}
add_action('wp_ajax_asad_upload_theme', 'asad_ajax_upload_theme');

/**
 * AJAX: Search WordPress.org Themes
 */
function asad_ajax_search_themes() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('install_themes')) {
        wp_send_json_error(array('message' => __('You do not have permission to search themes.', 'asad-portfolio')));
    }

    $search_term = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

    if (empty($search_term)) {
        wp_send_json_error(array('message' => __('Please enter a search term.', 'asad-portfolio')));
    }

    if (!function_exists('themes_api')) {
        require_once ABSPATH . 'wp-admin/includes/theme.php';
    }

    $args = array(
        'search'   => $search_term,
        'per_page' => 20,
    );

    $api = themes_api('query_themes', $args);

    if (is_wp_error($api)) {
        wp_send_json_error(array('message' => $api->get_error_message()));
    }

    $themes = array();
    foreach ($api->themes as $theme) {
        $themes[] = array(
            'name'        => $theme['name'],
            'slug'        => $theme['slug'],
            'version'     => $theme['version'],
            'author'      => strip_tags($theme['author']),
            'rating'      => $theme['rating'],
            'num_ratings' => $theme['num_ratings'],
            'downloaded'  => $theme['downloaded'],
            'description' => $theme['description'],
            'screenshot'  => !empty($theme['screenshot_url']) ? $theme['screenshot_url'] : '',
        );
    }

    wp_send_json_success(array('themes' => $themes));
}
add_action('wp_ajax_asad_search_themes', 'asad_ajax_search_themes');
