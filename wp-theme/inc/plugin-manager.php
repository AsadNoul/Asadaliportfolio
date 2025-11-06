<?php
/**
 * Plugin Manager Functions
 *
 * @package Asad_Portfolio_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get All Plugins
 */
function asad_get_all_plugins() {
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $all_plugins = get_plugins();
    $active_plugins = get_option('active_plugins', array());

    $plugins_data = array();

    foreach ($all_plugins as $plugin_file => $plugin_data) {
        $is_active = in_array($plugin_file, $active_plugins);

        $plugins_data[] = array(
            'file'        => $plugin_file,
            'name'        => $plugin_data['Name'],
            'description' => $plugin_data['Description'],
            'version'     => $plugin_data['Version'],
            'author'      => $plugin_data['Author'],
            'author_uri'  => $plugin_data['AuthorURI'],
            'plugin_uri'  => $plugin_data['PluginURI'],
            'is_active'   => $is_active,
        );
    }

    return $plugins_data;
}

/**
 * AJAX: Activate Plugin
 */
function asad_ajax_activate_plugin() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('activate_plugins')) {
        wp_send_json_error(array('message' => __('You do not have permission to activate plugins.', 'asad-portfolio')));
    }

    $plugin = isset($_POST['plugin']) ? sanitize_text_field($_POST['plugin']) : '';

    if (empty($plugin)) {
        wp_send_json_error(array('message' => __('Invalid plugin.', 'asad-portfolio')));
    }

    $result = activate_plugin($plugin);

    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()));
    }

    wp_send_json_success(array('message' => __('Plugin activated successfully.', 'asad-portfolio')));
}
add_action('wp_ajax_asad_activate_plugin', 'asad_ajax_activate_plugin');

/**
 * AJAX: Deactivate Plugin
 */
function asad_ajax_deactivate_plugin() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('activate_plugins')) {
        wp_send_json_error(array('message' => __('You do not have permission to deactivate plugins.', 'asad-portfolio')));
    }

    $plugin = isset($_POST['plugin']) ? sanitize_text_field($_POST['plugin']) : '';

    if (empty($plugin)) {
        wp_send_json_error(array('message' => __('Invalid plugin.', 'asad-portfolio')));
    }

    deactivate_plugins($plugin);

    wp_send_json_success(array('message' => __('Plugin deactivated successfully.', 'asad-portfolio')));
}
add_action('wp_ajax_asad_deactivate_plugin', 'asad_ajax_deactivate_plugin');

/**
 * AJAX: Delete Plugin
 */
function asad_ajax_delete_plugin() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('delete_plugins')) {
        wp_send_json_error(array('message' => __('You do not have permission to delete plugins.', 'asad-portfolio')));
    }

    $plugin = isset($_POST['plugin']) ? sanitize_text_field($_POST['plugin']) : '';

    if (empty($plugin)) {
        wp_send_json_error(array('message' => __('Invalid plugin.', 'asad-portfolio')));
    }

    // Deactivate first if active
    if (is_plugin_active($plugin)) {
        deactivate_plugins($plugin);
    }

    // Delete plugin
    $result = delete_plugins(array($plugin));

    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()));
    }

    wp_send_json_success(array('message' => __('Plugin deleted successfully.', 'asad-portfolio')));
}
add_action('wp_ajax_asad_delete_plugin', 'asad_ajax_delete_plugin');

/**
 * AJAX: Install Plugin from WordPress.org
 */
function asad_ajax_install_plugin() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('install_plugins')) {
        wp_send_json_error(array('message' => __('You do not have permission to install plugins.', 'asad-portfolio')));
    }

    $plugin_slug = isset($_POST['plugin_slug']) ? sanitize_text_field($_POST['plugin_slug']) : '';

    if (empty($plugin_slug)) {
        wp_send_json_error(array('message' => __('Invalid plugin slug.', 'asad-portfolio')));
    }

    // Include required files
    if (!class_exists('Plugin_Upgrader')) {
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    }

    if (!class_exists('Plugin_Installer_Skin')) {
        require_once ABSPATH . 'wp-admin/includes/class-plugin-installer-skin.php';
    }

    if (!function_exists('plugins_api')) {
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    }

    // Get plugin info
    $api = plugins_api('plugin_information', array('slug' => $plugin_slug));

    if (is_wp_error($api)) {
        wp_send_json_error(array('message' => $api->get_error_message()));
    }

    // Install plugin
    $skin = new WP_Ajax_Upgrader_Skin();
    $upgrader = new Plugin_Upgrader($skin);
    $result = $upgrader->install($api->download_link);

    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()));
    }

    if ($result === false) {
        wp_send_json_error(array('message' => __('Plugin installation failed.', 'asad-portfolio')));
    }

    wp_send_json_success(array('message' => __('Plugin installed successfully.', 'asad-portfolio')));
}
add_action('wp_ajax_asad_install_plugin', 'asad_ajax_install_plugin');

/**
 * AJAX: Upload Plugin ZIP
 */
function asad_ajax_upload_plugin() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('install_plugins')) {
        wp_send_json_error(array('message' => __('You do not have permission to upload plugins.', 'asad-portfolio')));
    }

    if (!isset($_FILES['plugin_zip'])) {
        wp_send_json_error(array('message' => __('No file uploaded.', 'asad-portfolio')));
    }

    $file = $_FILES['plugin_zip'];

    // Check file type
    $allowed_types = array('application/zip', 'application/x-zip-compressed');
    if (!in_array($file['type'], $allowed_types)) {
        wp_send_json_error(array('message' => __('Only ZIP files are allowed.', 'asad-portfolio')));
    }

    // Include required files
    if (!class_exists('Plugin_Upgrader')) {
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    }

    // Upload plugin
    $skin = new WP_Ajax_Upgrader_Skin();
    $upgrader = new Plugin_Upgrader($skin);
    $result = $upgrader->install($file['tmp_name']);

    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()));
    }

    if ($result === false) {
        wp_send_json_error(array('message' => __('Plugin upload failed.', 'asad-portfolio')));
    }

    wp_send_json_success(array('message' => __('Plugin uploaded successfully.', 'asad-portfolio')));
}
add_action('wp_ajax_asad_upload_plugin', 'asad_ajax_upload_plugin');

/**
 * AJAX: Search WordPress.org Plugins
 */
function asad_ajax_search_plugins() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('install_plugins')) {
        wp_send_json_error(array('message' => __('You do not have permission to search plugins.', 'asad-portfolio')));
    }

    $search_term = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

    if (empty($search_term)) {
        wp_send_json_error(array('message' => __('Please enter a search term.', 'asad-portfolio')));
    }

    if (!function_exists('plugins_api')) {
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    }

    $args = array(
        'search'   => $search_term,
        'per_page' => 20,
    );

    $api = plugins_api('query_plugins', $args);

    if (is_wp_error($api)) {
        wp_send_json_error(array('message' => $api->get_error_message()));
    }

    $plugins = array();
    foreach ($api->plugins as $plugin) {
        $plugins[] = array(
            'name'        => $plugin['name'],
            'slug'        => $plugin['slug'],
            'version'     => $plugin['version'],
            'author'      => strip_tags($plugin['author']),
            'rating'      => $plugin['rating'],
            'num_ratings' => $plugin['num_ratings'],
            'downloaded'  => $plugin['downloaded'],
            'short_description' => $plugin['short_description'],
            'icon'        => !empty($plugin['icons']['2x']) ? $plugin['icons']['2x'] : (!empty($plugin['icons']['1x']) ? $plugin['icons']['1x'] : ''),
        );
    }

    wp_send_json_success(array('plugins' => $plugins));
}
add_action('wp_ajax_asad_search_plugins', 'asad_ajax_search_plugins');
