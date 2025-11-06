<?php
/**
 * Theme Editor Functions
 *
 * @package Asad_Portfolio_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get Theme Files
 */
function asad_get_theme_files($theme_slug = null) {
    if (!$theme_slug) {
        $theme_slug = get_stylesheet();
    }

    $theme = wp_get_theme($theme_slug);

    if (!$theme->exists()) {
        return false;
    }

    $theme_root = $theme->get_stylesheet_directory();
    $files = asad_scan_directory($theme_root);

    return $files;
}

/**
 * Recursively Scan Directory
 */
function asad_scan_directory($dir, $base_dir = null) {
    if ($base_dir === null) {
        $base_dir = $dir;
    }

    $files = array();

    if (!is_dir($dir)) {
        return $files;
    }

    $items = scandir($dir);

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $path = $dir . '/' . $item;
        $relative_path = str_replace($base_dir . '/', '', $path);

        if (is_dir($path)) {
            $files[] = array(
                'type' => 'directory',
                'name' => $item,
                'path' => $relative_path,
                'children' => asad_scan_directory($path, $base_dir),
            );
        } else {
            $extension = pathinfo($item, PATHINFO_EXTENSION);
            $editable_extensions = array('php', 'css', 'js', 'html', 'txt', 'json', 'xml', 'md');

            $files[] = array(
                'type' => 'file',
                'name' => $item,
                'path' => $relative_path,
                'extension' => $extension,
                'editable' => in_array($extension, $editable_extensions),
                'size' => filesize($path),
            );
        }
    }

    return $files;
}

/**
 * AJAX: Get File Content
 */
function asad_ajax_get_file_content() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('edit_themes')) {
        wp_send_json_error(array('message' => __('You do not have permission to edit themes.', 'asad-portfolio')));
    }

    $file_path = isset($_POST['file']) ? sanitize_text_field($_POST['file']) : '';
    $theme_slug = isset($_POST['theme']) ? sanitize_text_field($_POST['theme']) : get_stylesheet();

    if (empty($file_path)) {
        wp_send_json_error(array('message' => __('Invalid file path.', 'asad-portfolio')));
    }

    $theme = wp_get_theme($theme_slug);
    if (!$theme->exists()) {
        wp_send_json_error(array('message' => __('Theme does not exist.', 'asad-portfolio')));
    }

    $theme_root = $theme->get_stylesheet_directory();
    $full_path = $theme_root . '/' . $file_path;

    // Security check: ensure file is within theme directory
    if (strpos(realpath($full_path), realpath($theme_root)) !== 0) {
        wp_send_json_error(array('message' => __('Security error: Invalid file path.', 'asad-portfolio')));
    }

    if (!file_exists($full_path)) {
        wp_send_json_error(array('message' => __('File does not exist.', 'asad-portfolio')));
    }

    if (!is_readable($full_path)) {
        wp_send_json_error(array('message' => __('File is not readable.', 'asad-portfolio')));
    }

    $content = file_get_contents($full_path);

    if ($content === false) {
        wp_send_json_error(array('message' => __('Failed to read file.', 'asad-portfolio')));
    }

    wp_send_json_success(array(
        'content' => $content,
        'file' => $file_path,
    ));
}
add_action('wp_ajax_asad_get_file_content', 'asad_ajax_get_file_content');

/**
 * AJAX: Save File Content
 */
function asad_ajax_save_file_content() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('edit_themes')) {
        wp_send_json_error(array('message' => __('You do not have permission to edit themes.', 'asad-portfolio')));
    }

    $file_path = isset($_POST['file']) ? sanitize_text_field($_POST['file']) : '';
    $content = isset($_POST['content']) ? wp_unslash($_POST['content']) : '';
    $theme_slug = isset($_POST['theme']) ? sanitize_text_field($_POST['theme']) : get_stylesheet();

    if (empty($file_path)) {
        wp_send_json_error(array('message' => __('Invalid file path.', 'asad-portfolio')));
    }

    $theme = wp_get_theme($theme_slug);
    if (!$theme->exists()) {
        wp_send_json_error(array('message' => __('Theme does not exist.', 'asad-portfolio')));
    }

    $theme_root = $theme->get_stylesheet_directory();
    $full_path = $theme_root . '/' . $file_path;

    // Security check: ensure file is within theme directory
    if (strpos(realpath(dirname($full_path)), realpath($theme_root)) !== 0) {
        wp_send_json_error(array('message' => __('Security error: Invalid file path.', 'asad-portfolio')));
    }

    if (!file_exists($full_path)) {
        wp_send_json_error(array('message' => __('File does not exist.', 'asad-portfolio')));
    }

    if (!is_writable($full_path)) {
        wp_send_json_error(array('message' => __('File is not writable.', 'asad-portfolio')));
    }

    // Create backup before editing
    $backup_dir = WP_CONTENT_DIR . '/asad-backups';
    if (!file_exists($backup_dir)) {
        wp_mkdir_p($backup_dir);
    }

    $backup_file = $backup_dir . '/' . basename($file_path) . '.' . time() . '.bak';
    copy($full_path, $backup_file);

    // Validate PHP syntax if it's a PHP file
    if (pathinfo($file_path, PATHINFO_EXTENSION) === 'php') {
        $temp_file = tempnam(sys_get_temp_dir(), 'asad');
        file_put_contents($temp_file, $content);

        $output = shell_exec('php -l ' . escapeshellarg($temp_file) . ' 2>&1');
        unlink($temp_file);

        if (strpos($output, 'No syntax errors') === false) {
            wp_send_json_error(array('message' => __('PHP Syntax Error: ', 'asad-portfolio') . $output));
        }
    }

    // Save file
    $result = file_put_contents($full_path, $content);

    if ($result === false) {
        wp_send_json_error(array('message' => __('Failed to save file.', 'asad-portfolio')));
    }

    wp_send_json_success(array(
        'message' => __('File saved successfully.', 'asad-portfolio'),
        'backup' => $backup_file,
    ));
}
add_action('wp_ajax_asad_save_file_content', 'asad_ajax_save_file_content');

/**
 * AJAX: Create New File
 */
function asad_ajax_create_file() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('edit_themes')) {
        wp_send_json_error(array('message' => __('You do not have permission to create files.', 'asad-portfolio')));
    }

    $file_path = isset($_POST['file']) ? sanitize_text_field($_POST['file']) : '';
    $theme_slug = isset($_POST['theme']) ? sanitize_text_field($_POST['theme']) : get_stylesheet();

    if (empty($file_path)) {
        wp_send_json_error(array('message' => __('Invalid file path.', 'asad-portfolio')));
    }

    $theme = wp_get_theme($theme_slug);
    if (!$theme->exists()) {
        wp_send_json_error(array('message' => __('Theme does not exist.', 'asad-portfolio')));
    }

    $theme_root = $theme->get_stylesheet_directory();
    $full_path = $theme_root . '/' . $file_path;

    // Security check
    if (strpos(realpath(dirname($full_path)), realpath($theme_root)) !== 0) {
        wp_send_json_error(array('message' => __('Security error: Invalid file path.', 'asad-portfolio')));
    }

    if (file_exists($full_path)) {
        wp_send_json_error(array('message' => __('File already exists.', 'asad-portfolio')));
    }

    // Create directory if needed
    $dir = dirname($full_path);
    if (!file_exists($dir)) {
        wp_mkdir_p($dir);
    }

    // Create empty file
    $result = file_put_contents($full_path, '');

    if ($result === false) {
        wp_send_json_error(array('message' => __('Failed to create file.', 'asad-portfolio')));
    }

    wp_send_json_success(array('message' => __('File created successfully.', 'asad-portfolio')));
}
add_action('wp_ajax_asad_create_file', 'asad_ajax_create_file');

/**
 * AJAX: Delete File
 */
function asad_ajax_delete_file() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('edit_themes')) {
        wp_send_json_error(array('message' => __('You do not have permission to delete files.', 'asad-portfolio')));
    }

    $file_path = isset($_POST['file']) ? sanitize_text_field($_POST['file']) : '';
    $theme_slug = isset($_POST['theme']) ? sanitize_text_field($_POST['theme']) : get_stylesheet();

    if (empty($file_path)) {
        wp_send_json_error(array('message' => __('Invalid file path.', 'asad-portfolio')));
    }

    $theme = wp_get_theme($theme_slug);
    if (!$theme->exists()) {
        wp_send_json_error(array('message' => __('Theme does not exist.', 'asad-portfolio')));
    }

    $theme_root = $theme->get_stylesheet_directory();
    $full_path = $theme_root . '/' . $file_path;

    // Security check
    if (strpos(realpath($full_path), realpath($theme_root)) !== 0) {
        wp_send_json_error(array('message' => __('Security error: Invalid file path.', 'asad-portfolio')));
    }

    if (!file_exists($full_path)) {
        wp_send_json_error(array('message' => __('File does not exist.', 'asad-portfolio')));
    }

    // Don't allow deleting critical files
    $critical_files = array('style.css', 'functions.php', 'index.php');
    if (in_array(basename($file_path), $critical_files)) {
        wp_send_json_error(array('message' => __('Cannot delete critical theme files.', 'asad-portfolio')));
    }

    $result = unlink($full_path);

    if ($result === false) {
        wp_send_json_error(array('message' => __('Failed to delete file.', 'asad-portfolio')));
    }

    wp_send_json_success(array('message' => __('File deleted successfully.', 'asad-portfolio')));
}
add_action('wp_ajax_asad_delete_file', 'asad_ajax_delete_file');

/**
 * AJAX: Get Backups List
 */
function asad_ajax_get_backups() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('edit_themes')) {
        wp_send_json_error(array('message' => __('You do not have permission to view backups.', 'asad-portfolio')));
    }

    $backup_dir = WP_CONTENT_DIR . '/asad-backups';

    if (!file_exists($backup_dir)) {
        wp_send_json_success(array('backups' => array()));
        return;
    }

    $backups = array();
    $files = scandir($backup_dir, SCANDIR_SORT_DESCENDING);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $full_path = $backup_dir . '/' . $file;
        $backups[] = array(
            'name' => $file,
            'size' => filesize($full_path),
            'date' => filemtime($full_path),
        );
    }

    wp_send_json_success(array('backups' => $backups));
}
add_action('wp_ajax_asad_get_backups', 'asad_ajax_get_backups');
