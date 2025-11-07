<?php
/**
 * Performance Optimizer
 *
 * @package Asad_Portfolio_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get Performance Settings
 */
function asad_get_performance_settings() {
    return get_option('asad_performance_settings', array(
        'lazy_loading' => true,
        'minify_css' => false,
        'minify_js' => false,
        'gzip_compression' => true,
        'browser_caching' => true,
        'optimize_database' => false,
        'disable_embeds' => true,
        'disable_emojis' => true,
        'defer_js' => true,
        'remove_query_strings' => true,
    ));
}

/**
 * Save Performance Settings
 */
function asad_save_performance_settings($settings) {
    return update_option('asad_performance_settings', $settings);
}

/**
 * Enable Lazy Loading
 */
function asad_enable_lazy_loading() {
    $settings = asad_get_performance_settings();
    if (isset($settings['lazy_loading']) && $settings['lazy_loading']) {
        add_filter('wp_lazy_loading_enabled', '__return_true');
    }
}
add_action('init', 'asad_enable_lazy_loading');

/**
 * Disable Embeds
 */
function asad_disable_embeds() {
    $settings = asad_get_performance_settings();
    if (isset($settings['disable_embeds']) && $settings['disable_embeds']) {
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
        remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
    }
}
add_action('init', 'asad_disable_embeds');

/**
 * Disable Emojis
 */
function asad_disable_emojis() {
    $settings = asad_get_performance_settings();
    if (isset($settings['disable_emojis']) && $settings['disable_emojis']) {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    }
}
add_action('init', 'asad_disable_emojis');

/**
 * Defer JavaScript Loading
 */
function asad_defer_scripts($tag, $handle, $src) {
    $settings = asad_get_performance_settings();
    if (isset($settings['defer_js']) && $settings['defer_js']) {
        // Don't defer jQuery
        if (strpos($handle, 'jquery') !== false) {
            return $tag;
        }

        // Add defer attribute
        return str_replace(' src', ' defer src', $tag);
    }
    return $tag;
}
add_filter('script_loader_tag', 'asad_defer_scripts', 10, 3);

/**
 * Remove Query Strings from Static Resources
 */
function asad_remove_query_strings($src) {
    $settings = asad_get_performance_settings();
    if (isset($settings['remove_query_strings']) && $settings['remove_query_strings']) {
        if (strpos($src, '?ver=')) {
            $src = remove_query_arg('ver', $src);
        }
    }
    return $src;
}
add_filter('style_loader_src', 'asad_remove_query_strings', 10, 2);
add_filter('script_loader_src', 'asad_remove_query_strings', 10, 2);

/**
 * Enable GZIP Compression
 */
function asad_enable_gzip_compression() {
    $settings = asad_get_performance_settings();
    if (isset($settings['gzip_compression']) && $settings['gzip_compression']) {
        if (!ob_start('ob_gzhandler')) {
            ob_start();
        }
    }
}
add_action('init', 'asad_enable_gzip_compression');

/**
 * Add Browser Caching Headers
 */
function asad_add_caching_headers() {
    $settings = asad_get_performance_settings();
    if (isset($settings['browser_caching']) && $settings['browser_caching']) {
        // This would typically be done via .htaccess
        // But we can set headers for WordPress generated content
        if (!is_admin()) {
            header('Cache-Control: public, max-age=31536000');
        }
    }
}
add_action('send_headers', 'asad_add_caching_headers');

/**
 * Optimize Database
 */
function asad_optimize_database() {
    global $wpdb;

    // Get all tables
    $tables = $wpdb->get_results('SHOW TABLES', ARRAY_N);

    $optimized = 0;
    foreach ($tables as $table) {
        $table_name = $table[0];
        $wpdb->query("OPTIMIZE TABLE `{$table_name}`");
        $optimized++;
    }

    // Clean up
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%_transient_%'");
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_edit_lock'");
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_edit_last'");
    $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_status = 'auto-draft'");
    $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_status = 'trash'");
    $wpdb->query("DELETE FROM {$wpdb->comments} WHERE comment_approved = 'spam'");

    return $optimized;
}

/**
 * Get Database Size
 */
function asad_get_database_size() {
    global $wpdb;
    $size = $wpdb->get_var("
        SELECT SUM(data_length + index_length) / 1024 / 1024
        FROM information_schema.TABLES
        WHERE table_schema = '{$wpdb->dbname}'
    ");
    return round($size, 2);
}

/**
 * Get Performance Stats
 */
function asad_get_performance_stats() {
    global $wpdb;

    return array(
        'database_size' => asad_get_database_size(),
        'total_posts' => wp_count_posts()->publish,
        'total_pages' => wp_count_posts('page')->publish,
        'total_comments' => wp_count_comments()->approved,
        'total_media' => wp_count_posts('attachment')->inherit,
        'transients_count' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE '%_transient_%'"),
        'auto_drafts' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'auto-draft'"),
        'trash_posts' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'trash'"),
        'spam_comments' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = 'spam'"),
    );
}

/**
 * AJAX: Save Performance Settings
 */
function asad_ajax_save_performance_settings() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => __('Permission denied.', 'asad-portfolio')));
    }

    $settings = isset($_POST['settings']) ? json_decode(stripslashes($_POST['settings']), true) : array();

    if (asad_save_performance_settings($settings)) {
        wp_send_json_success(array('message' => __('Settings saved successfully!', 'asad-portfolio')));
    } else {
        wp_send_json_error(array('message' => __('Failed to save settings.', 'asad-portfolio')));
    }
}
add_action('wp_ajax_asad_save_performance_settings', 'asad_ajax_save_performance_settings');

/**
 * AJAX: Optimize Database (Performance)
 */
function asad_ajax_performance_optimize_database() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => __('Permission denied.', 'asad-portfolio')));
    }

    $optimized = asad_optimize_database();
    $new_size = asad_get_database_size();

    wp_send_json_success(array(
        'message' => sprintf(__('Database optimized! %d tables optimized.', 'asad-portfolio'), $optimized),
        'new_size' => $new_size
    ));
}
add_action('wp_ajax_asad_performance_optimize_database', 'asad_ajax_performance_optimize_database');

/**
 * AJAX: Clear All Caches
 */
function asad_ajax_clear_caches() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => __('Permission denied.', 'asad-portfolio')));
    }

    // Clear WordPress transients
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%_transient_%'");

    // Clear object cache
    wp_cache_flush();

    // Clear rewrite rules
    flush_rewrite_rules();

    wp_send_json_success(array('message' => __('All caches cleared successfully!', 'asad-portfolio')));
}
add_action('wp_ajax_asad_clear_caches', 'asad_ajax_clear_caches');

/**
 * AJAX: Get Performance Stats
 */
function asad_ajax_get_performance_stats() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => __('Permission denied.', 'asad-portfolio')));
    }

    $stats = asad_get_performance_stats();
    wp_send_json_success(array('stats' => $stats));
}
add_action('wp_ajax_asad_get_performance_stats', 'asad_ajax_get_performance_stats');

/**
 * Minify CSS
 */
function asad_minify_css($css) {
    $settings = asad_get_performance_settings();
    if (isset($settings['minify_css']) && $settings['minify_css']) {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        // Remove whitespace
        $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
        return $css;
    }
    return $css;
}

/**
 * Minify JavaScript
 */
function asad_minify_js($js) {
    $settings = asad_get_performance_settings();
    if (isset($settings['minify_js']) && $settings['minify_js']) {
        // Basic JS minification
        // Remove comments
        $js = preg_replace('/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/', '', $js);
        // Remove whitespace
        $js = str_replace(array("\r\n", "\r", "\t", "\n", '  ', '    ', '     '), ' ', $js);
        return $js;
    }
    return $js;
}

/**
 * Generate .htaccess rules for performance
 */
function asad_generate_htaccess_rules() {
    $rules = "# BEGIN Asad Performance Optimizer\n";
    $rules .= "<IfModule mod_expires.c>\n";
    $rules .= "ExpiresActive On\n";
    $rules .= "ExpiresByType image/jpg \"access plus 1 year\"\n";
    $rules .= "ExpiresByType image/jpeg \"access plus 1 year\"\n";
    $rules .= "ExpiresByType image/gif \"access plus 1 year\"\n";
    $rules .= "ExpiresByType image/png \"access plus 1 year\"\n";
    $rules .= "ExpiresByType text/css \"access plus 1 month\"\n";
    $rules .= "ExpiresByType application/pdf \"access plus 1 month\"\n";
    $rules .= "ExpiresByType text/javascript \"access plus 1 month\"\n";
    $rules .= "ExpiresByType application/javascript \"access plus 1 month\"\n";
    $rules .= "ExpiresByType application/x-shockwave-flash \"access plus 1 month\"\n";
    $rules .= "ExpiresDefault \"access plus 2 days\"\n";
    $rules .= "</IfModule>\n\n";

    $rules .= "<IfModule mod_deflate.c>\n";
    $rules .= "AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/x-javascript\n";
    $rules .= "</IfModule>\n";
    $rules .= "# END Asad Performance Optimizer\n\n";

    return $rules;
}

/**
 * Image Optimization (Basic lazy loading)
 */
function asad_optimize_images($content) {
    $settings = asad_get_performance_settings();
    if (isset($settings['lazy_loading']) && $settings['lazy_loading']) {
        // Add loading="lazy" to images
        $content = preg_replace('/<img(.*?)>/i', '<img$1 loading="lazy">', $content);
    }
    return $content;
}
add_filter('the_content', 'asad_optimize_images');

/**
 * Preload Critical Resources
 */
function asad_preload_resources() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "\n";
}
add_action('wp_head', 'asad_preload_resources', 0);

/**
 * Disable Heartbeat API
 */
function asad_disable_heartbeat() {
    $settings = asad_get_performance_settings();
    if (isset($settings['disable_heartbeat']) && $settings['disable_heartbeat']) {
        wp_deregister_script('heartbeat');
    }
}
add_action('wp_enqueue_scripts', 'asad_disable_heartbeat', 1);
