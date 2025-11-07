<?php
/**
 * Analytics Dashboard
 * Track visitor stats, popular posts, traffic sources
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create analytics tables on theme activation
 */
function asad_create_analytics_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Page views table
    $table_name = $wpdb->prefix . 'asad_analytics';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) NOT NULL,
        post_type varchar(50) NOT NULL,
        post_title text NOT NULL,
        visitor_ip varchar(100) NOT NULL,
        user_agent text NOT NULL,
        referrer text,
        country varchar(100),
        device_type varchar(50),
        browser varchar(100),
        os varchar(100),
        view_date datetime NOT NULL,
        PRIMARY KEY (id),
        KEY post_id (post_id),
        KEY view_date (view_date),
        KEY device_type (device_type)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Traffic sources table
    $sources_table = $wpdb->prefix . 'asad_traffic_sources';
    $sql2 = "CREATE TABLE IF NOT EXISTS $sources_table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        source_type varchar(50) NOT NULL,
        source_name varchar(255) NOT NULL,
        visit_count int(11) NOT NULL DEFAULT 0,
        last_visit datetime NOT NULL,
        PRIMARY KEY (id),
        KEY source_type (source_type)
    ) $charset_collate;";

    dbDelta($sql2);
}
add_action('after_switch_theme', 'asad_create_analytics_tables');

/**
 * Track page view
 */
function asad_track_page_view() {
    if (is_admin() || is_user_logged_in()) {
        return; // Don't track admin or logged-in users
    }

    global $wpdb, $post;

    if (!$post) {
        return;
    }

    $table_name = $wpdb->prefix . 'asad_analytics';

    // Get visitor information
    $visitor_ip = asad_get_visitor_ip();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $referrer = $_SERVER['HTTP_REFERER'] ?? 'Direct';

    // Parse user agent
    $device_info = asad_parse_user_agent($user_agent);

    // Insert view record
    $wpdb->insert(
        $table_name,
        array(
            'post_id' => $post->ID,
            'post_type' => $post->post_type,
            'post_title' => $post->post_title,
            'visitor_ip' => $visitor_ip,
            'user_agent' => $user_agent,
            'referrer' => $referrer,
            'device_type' => $device_info['device'],
            'browser' => $device_info['browser'],
            'os' => $device_info['os'],
            'view_date' => current_time('mysql')
        ),
        array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );

    // Track traffic source
    asad_track_traffic_source($referrer);
}
add_action('wp_head', 'asad_track_page_view');

/**
 * Get visitor IP address
 */
function asad_get_visitor_ip() {
    $ip_keys = array(
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    );

    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }
    }

    return 'Unknown';
}

/**
 * Parse user agent to get device, browser, OS info
 */
function asad_parse_user_agent($user_agent) {
    $info = array(
        'device' => 'Desktop',
        'browser' => 'Unknown',
        'os' => 'Unknown'
    );

    // Detect device
    if (preg_match('/mobile|android|iphone|ipad|ipod/i', $user_agent)) {
        $info['device'] = 'Mobile';
    } elseif (preg_match('/tablet|ipad/i', $user_agent)) {
        $info['device'] = 'Tablet';
    }

    // Detect browser
    if (preg_match('/Edge/i', $user_agent)) {
        $info['browser'] = 'Edge';
    } elseif (preg_match('/Chrome/i', $user_agent)) {
        $info['browser'] = 'Chrome';
    } elseif (preg_match('/Safari/i', $user_agent)) {
        $info['browser'] = 'Safari';
    } elseif (preg_match('/Firefox/i', $user_agent)) {
        $info['browser'] = 'Firefox';
    } elseif (preg_match('/MSIE|Trident/i', $user_agent)) {
        $info['browser'] = 'Internet Explorer';
    } elseif (preg_match('/Opera|OPR/i', $user_agent)) {
        $info['browser'] = 'Opera';
    }

    // Detect OS
    if (preg_match('/Windows/i', $user_agent)) {
        $info['os'] = 'Windows';
    } elseif (preg_match('/Macintosh|Mac OS X/i', $user_agent)) {
        $info['os'] = 'Mac OS';
    } elseif (preg_match('/Linux/i', $user_agent)) {
        $info['os'] = 'Linux';
    } elseif (preg_match('/Android/i', $user_agent)) {
        $info['os'] = 'Android';
    } elseif (preg_match('/iOS|iPhone|iPad/i', $user_agent)) {
        $info['os'] = 'iOS';
    }

    return $info;
}

/**
 * Track traffic source
 */
function asad_track_traffic_source($referrer) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_traffic_sources';

    // Determine source type and name
    $source = asad_parse_referrer($referrer);

    // Check if source exists
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE source_name = %s",
        $source['name']
    ));

    if ($existing) {
        // Update visit count
        $wpdb->update(
            $table_name,
            array(
                'visit_count' => $existing->visit_count + 1,
                'last_visit' => current_time('mysql')
            ),
            array('id' => $existing->id),
            array('%d', '%s'),
            array('%d')
        );
    } else {
        // Insert new source
        $wpdb->insert(
            $table_name,
            array(
                'source_type' => $source['type'],
                'source_name' => $source['name'],
                'visit_count' => 1,
                'last_visit' => current_time('mysql')
            ),
            array('%s', '%s', '%d', '%s')
        );
    }
}

/**
 * Parse referrer to determine source
 */
function asad_parse_referrer($referrer) {
    if (empty($referrer) || $referrer === 'Direct') {
        return array('type' => 'Direct', 'name' => 'Direct Traffic');
    }

    $parsed_url = parse_url($referrer);
    $host = $parsed_url['host'] ?? '';

    // Check for search engines
    $search_engines = array(
        'google' => 'Google',
        'bing' => 'Bing',
        'yahoo' => 'Yahoo',
        'duckduckgo' => 'DuckDuckGo',
        'baidu' => 'Baidu'
    );

    foreach ($search_engines as $key => $name) {
        if (stripos($host, $key) !== false) {
            return array('type' => 'Search Engine', 'name' => $name);
        }
    }

    // Check for social media
    $social_media = array(
        'facebook' => 'Facebook',
        'twitter' => 'Twitter',
        'linkedin' => 'LinkedIn',
        'instagram' => 'Instagram',
        'pinterest' => 'Pinterest',
        'reddit' => 'Reddit',
        'youtube' => 'YouTube'
    );

    foreach ($social_media as $key => $name) {
        if (stripos($host, $key) !== false) {
            return array('type' => 'Social Media', 'name' => $name);
        }
    }

    // Other referral
    return array('type' => 'Referral', 'name' => $host);
}

/**
 * Get analytics stats
 */
function asad_get_analytics_stats($days = 30) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_analytics';
    $date_from = date('Y-m-d', strtotime("-$days days"));

    $stats = array();

    // Total views
    $stats['total_views'] = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE view_date >= %s",
        $date_from
    ));

    // Unique visitors (based on IP)
    $stats['unique_visitors'] = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(DISTINCT visitor_ip) FROM $table_name WHERE view_date >= %s",
        $date_from
    ));

    // Average views per day
    $stats['avg_views_per_day'] = round($stats['total_views'] / $days, 2);

    // Most popular posts
    $stats['popular_posts'] = $wpdb->get_results($wpdb->prepare(
        "SELECT post_id, post_title, COUNT(*) as views
        FROM $table_name
        WHERE view_date >= %s
        GROUP BY post_id, post_title
        ORDER BY views DESC
        LIMIT 10",
        $date_from
    ), ARRAY_A);

    // Device breakdown
    $stats['devices'] = $wpdb->get_results($wpdb->prepare(
        "SELECT device_type, COUNT(*) as count
        FROM $table_name
        WHERE view_date >= %s
        GROUP BY device_type",
        $date_from
    ), ARRAY_A);

    // Browser breakdown
    $stats['browsers'] = $wpdb->get_results($wpdb->prepare(
        "SELECT browser, COUNT(*) as count
        FROM $table_name
        WHERE view_date >= %s
        GROUP BY browser
        ORDER BY count DESC
        LIMIT 10",
        $date_from
    ), ARRAY_A);

    // Daily views for chart
    $stats['daily_views'] = $wpdb->get_results($wpdb->prepare(
        "SELECT DATE(view_date) as date, COUNT(*) as views
        FROM $table_name
        WHERE view_date >= %s
        GROUP BY DATE(view_date)
        ORDER BY date ASC",
        $date_from
    ), ARRAY_A);

    return $stats;
}

/**
 * Get traffic sources
 */
function asad_get_traffic_sources($limit = 10) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_traffic_sources';

    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name ORDER BY visit_count DESC LIMIT %d",
        $limit
    ), ARRAY_A);
}

/**
 * Clear analytics data
 */
function asad_clear_analytics_data() {
    global $wpdb;

    $analytics_table = $wpdb->prefix . 'asad_analytics';
    $sources_table = $wpdb->prefix . 'asad_traffic_sources';

    $wpdb->query("TRUNCATE TABLE $analytics_table");
    $wpdb->query("TRUNCATE TABLE $sources_table");

    return true;
}

/**
 * AJAX handler for analytics data
 */
function asad_ajax_get_analytics() {
    check_ajax_referer('asad_analytics_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $days = isset($_POST['days']) ? intval($_POST['days']) : 30;
    $stats = asad_get_analytics_stats($days);
    $sources = asad_get_traffic_sources(10);

    wp_send_json_success(array(
        'stats' => $stats,
        'sources' => $sources
    ));
}
add_action('wp_ajax_asad_get_analytics', 'asad_ajax_get_analytics');

/**
 * AJAX handler for clearing analytics
 */
function asad_ajax_clear_analytics() {
    check_ajax_referer('asad_analytics_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    asad_clear_analytics_data();
    wp_send_json_success('Analytics data cleared');
}
add_action('wp_ajax_asad_clear_analytics', 'asad_ajax_clear_analytics');
