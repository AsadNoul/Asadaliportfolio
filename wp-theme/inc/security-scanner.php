<?php
/**
 * Security Scanner
 * Malware scan, firewall, 2FA, security audit
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create security tables
 */
function asad_create_security_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Security logs table
    $table_name = $wpdb->prefix . 'asad_security_logs';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        log_type varchar(50) NOT NULL,
        severity varchar(20) NOT NULL,
        message text NOT NULL,
        ip_address varchar(100),
        user_agent text,
        log_date datetime NOT NULL,
        PRIMARY KEY (id),
        KEY log_type (log_type),
        KEY severity (severity),
        KEY log_date (log_date)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Blocked IPs table
    $blocked_table = $wpdb->prefix . 'asad_blocked_ips';
    $sql2 = "CREATE TABLE IF NOT EXISTS $blocked_table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        ip_address varchar(100) NOT NULL,
        reason text,
        blocked_date datetime NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY ip_address (ip_address)
    ) $charset_collate;";

    dbDelta($sql2);

    // 2FA secrets table
    $twofa_table = $wpdb->prefix . 'asad_2fa_secrets';
    $sql3 = "CREATE TABLE IF NOT EXISTS $twofa_table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        secret_key varchar(255) NOT NULL,
        is_enabled tinyint(1) NOT NULL DEFAULT 0,
        backup_codes text,
        created_date datetime NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY user_id (user_id)
    ) $charset_collate;";

    dbDelta($sql3);
}
add_action('after_switch_theme', 'asad_create_security_tables');

/**
 * Security log function
 */
function asad_log_security_event($type, $severity, $message) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_security_logs';

    $wpdb->insert(
        $table_name,
        array(
            'log_type' => $type,
            'severity' => $severity,
            'message' => $message,
            'ip_address' => asad_get_visitor_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'log_date' => current_time('mysql')
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s')
    );
}

/**
 * Check if IP is blocked
 */
function asad_check_blocked_ip() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_blocked_ips';
    $visitor_ip = asad_get_visitor_ip();

    $blocked = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE ip_address = %s",
        $visitor_ip
    ));

    if ($blocked) {
        asad_log_security_event('blocked_access', 'warning', 'Blocked IP attempted access: ' . $visitor_ip);
        wp_die('Access Denied. Your IP has been blocked.', 'Access Denied', array('response' => 403));
    }
}
add_action('init', 'asad_check_blocked_ip', 1);

/**
 * Malware scan - scan theme and plugin files for suspicious code
 */
function asad_scan_for_malware() {
    $results = array(
        'total_files' => 0,
        'scanned_files' => 0,
        'threats' => array(),
        'warnings' => array(),
        'scan_time' => 0
    );

    $start_time = microtime(true);

    // Suspicious patterns
    $suspicious_patterns = array(
        'eval\s*\(' => 'eval() function detected',
        'base64_decode\s*\(' => 'base64_decode() function detected',
        'system\s*\(' => 'system() function detected',
        'exec\s*\(' => 'exec() function detected',
        'shell_exec\s*\(' => 'shell_exec() function detected',
        'passthru\s*\(' => 'passthru() function detected',
        'assert\s*\(' => 'assert() function detected',
        'preg_replace.*\/e' => 'preg_replace with /e modifier detected',
        'file_get_contents\s*\(\s*["\']http' => 'Remote file inclusion detected',
        '\$_(?:GET|POST|REQUEST|COOKIE)\[.*?\]\s*\(' => 'Variable function call detected',
        'create_function\s*\(' => 'create_function() detected (deprecated)',
    );

    // Scan directories
    $directories = array(
        get_template_directory(),
        WP_PLUGIN_DIR
    );

    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            continue;
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $results['total_files']++;

                $content = file_get_contents($file->getPathname());
                $results['scanned_files']++;

                foreach ($suspicious_patterns as $pattern => $description) {
                    if (preg_match('/' . $pattern . '/i', $content)) {
                        $results['threats'][] = array(
                            'file' => str_replace(ABSPATH, '', $file->getPathname()),
                            'threat' => $description,
                            'severity' => 'high'
                        );
                    }
                }

                // Check file permissions
                if (is_writable($file->getPathname()) && $file->getPerms() & 0002) {
                    $results['warnings'][] = array(
                        'file' => str_replace(ABSPATH, '', $file->getPathname()),
                        'warning' => 'World-writable file permissions',
                        'severity' => 'medium'
                    );
                }
            }
        }
    }

    $results['scan_time'] = round(microtime(true) - $start_time, 2);

    // Log scan
    asad_log_security_event('malware_scan', 'info', 'Malware scan completed. Threats: ' . count($results['threats']));

    return $results;
}

/**
 * Security audit - check WordPress security settings
 */
function asad_security_audit() {
    $audit = array(
        'score' => 100,
        'issues' => array(),
        'passed' => array()
    );

    // Check WordPress version
    global $wp_version;
    $latest_version = get_transient('asad_latest_wp_version');
    if ($latest_version === false) {
        $response = wp_remote_get('https://api.wordpress.org/core/version-check/1.7/');
        if (!is_wp_error($response)) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $latest_version = $body['offers'][0]['version'] ?? $wp_version;
            set_transient('asad_latest_wp_version', $latest_version, DAY_IN_SECONDS);
        }
    }

    if (version_compare($wp_version, $latest_version, '<')) {
        $audit['issues'][] = array(
            'severity' => 'high',
            'title' => 'WordPress is Outdated',
            'description' => "You're running WordPress $wp_version. Latest is $latest_version.",
            'fix' => 'Update to the latest version'
        );
        $audit['score'] -= 15;
    } else {
        $audit['passed'][] = 'WordPress is up to date';
    }

    // Check if admin username exists
    $admin_user = get_user_by('login', 'admin');
    if ($admin_user) {
        $audit['issues'][] = array(
            'severity' => 'high',
            'title' => 'Default Admin Username',
            'description' => 'Using "admin" as username makes brute force attacks easier.',
            'fix' => 'Change the admin username'
        );
        $audit['score'] -= 15;
    } else {
        $audit['passed'][] = 'No default admin username';
    }

    // Check file permissions
    $files_to_check = array(
        ABSPATH . 'wp-config.php' => 0600,
        ABSPATH . '.htaccess' => 0644
    );

    foreach ($files_to_check as $file => $recommended_perms) {
        if (file_exists($file)) {
            $current_perms = substr(sprintf('%o', fileperms($file)), -4);
            if (octdec($current_perms) > $recommended_perms) {
                $audit['issues'][] = array(
                    'severity' => 'medium',
                    'title' => 'Insecure File Permissions',
                    'description' => basename($file) . ' has overly permissive permissions.',
                    'fix' => 'Set permissions to ' . decoct($recommended_perms)
                );
                $audit['score'] -= 10;
            }
        }
    }

    // Check if XML-RPC is enabled
    if (!get_option('xmlrpc_enabled')) {
        $audit['passed'][] = 'XML-RPC is disabled';
    } else {
        $audit['issues'][] = array(
            'severity' => 'medium',
            'title' => 'XML-RPC Enabled',
            'description' => 'XML-RPC can be exploited for brute force attacks.',
            'fix' => 'Disable XML-RPC if not needed'
        );
        $audit['score'] -= 10;
    }

    // Check if file editing is disabled
    if (defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT) {
        $audit['passed'][] = 'File editing is disabled';
    } else {
        $audit['issues'][] = array(
            'severity' => 'medium',
            'title' => 'File Editing Enabled',
            'description' => 'WordPress file editor can be exploited if admin account is compromised.',
            'fix' => 'Add define(\'DISALLOW_FILE_EDIT\', true); to wp-config.php'
        );
        $audit['score'] -= 10;
    }

    // Check SSL
    if (is_ssl()) {
        $audit['passed'][] = 'SSL/HTTPS is enabled';
    } else {
        $audit['issues'][] = array(
            'severity' => 'high',
            'title' => 'SSL Not Enabled',
            'description' => 'Your site is not using HTTPS encryption.',
            'fix' => 'Install an SSL certificate and force HTTPS'
        );
        $audit['score'] -= 20;
    }

    // Check database prefix
    global $wpdb;
    if ($wpdb->prefix !== 'wp_') {
        $audit['passed'][] = 'Custom database prefix';
    } else {
        $audit['issues'][] = array(
            'severity' => 'low',
            'title' => 'Default Database Prefix',
            'description' => 'Using the default "wp_" prefix makes SQL injection easier.',
            'fix' => 'Change database prefix (requires manual migration)'
        );
        $audit['score'] -= 5;
    }

    // Check password strength for all users
    $weak_password_users = 0;
    $users = get_users(array('role__in' => array('administrator', 'editor')));
    foreach ($users as $user) {
        // This is a simplified check - in real scenario you'd want more sophisticated checks
        if (strlen($user->user_pass) < 60) { // WordPress hashes are typically 60+ chars
            $weak_password_users++;
        }
    }

    if ($weak_password_users === 0) {
        $audit['passed'][] = 'Strong passwords for admin users';
    }

    $audit['score'] = max(0, $audit['score']);

    return $audit;
}

/**
 * Block IP address
 */
function asad_block_ip($ip_address, $reason = '') {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_blocked_ips';

    $result = $wpdb->insert(
        $table_name,
        array(
            'ip_address' => $ip_address,
            'reason' => $reason,
            'blocked_date' => current_time('mysql')
        ),
        array('%s', '%s', '%s')
    );

    if ($result) {
        asad_log_security_event('ip_blocked', 'warning', "IP blocked: $ip_address - Reason: $reason");
    }

    return $result;
}

/**
 * Unblock IP address
 */
function asad_unblock_ip($ip_address) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_blocked_ips';

    $result = $wpdb->delete(
        $table_name,
        array('ip_address' => $ip_address),
        array('%s')
    );

    if ($result) {
        asad_log_security_event('ip_unblocked', 'info', "IP unblocked: $ip_address");
    }

    return $result;
}

/**
 * Get blocked IPs
 */
function asad_get_blocked_ips() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_blocked_ips';

    return $wpdb->get_results("SELECT * FROM $table_name ORDER BY blocked_date DESC", ARRAY_A);
}

/**
 * Get security logs
 */
function asad_get_security_logs($limit = 100) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_security_logs';

    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name ORDER BY log_date DESC LIMIT %d",
        $limit
    ), ARRAY_A);
}

/**
 * Generate 2FA secret key
 */
function asad_generate_2fa_secret() {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret = '';
    for ($i = 0; $i < 16; $i++) {
        $secret .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $secret;
}

/**
 * Enable 2FA for user
 */
function asad_enable_2fa($user_id) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_2fa_secrets';
    $secret = asad_generate_2fa_secret();

    // Generate backup codes
    $backup_codes = array();
    for ($i = 0; $i < 10; $i++) {
        $backup_codes[] = wp_generate_password(8, false);
    }

    $wpdb->replace(
        $table_name,
        array(
            'user_id' => $user_id,
            'secret_key' => $secret,
            'is_enabled' => 1,
            'backup_codes' => json_encode($backup_codes),
            'created_date' => current_time('mysql')
        ),
        array('%d', '%s', '%d', '%s', '%s')
    );

    asad_log_security_event('2fa_enabled', 'info', "2FA enabled for user ID: $user_id");

    return array('secret' => $secret, 'backup_codes' => $backup_codes);
}

/**
 * Disable 2FA for user
 */
function asad_disable_2fa($user_id) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_2fa_secrets';

    $wpdb->delete($table_name, array('user_id' => $user_id), array('%d'));

    asad_log_security_event('2fa_disabled', 'info', "2FA disabled for user ID: $user_id");
}

/**
 * AJAX: Run malware scan
 */
function asad_ajax_scan_malware() {
    check_ajax_referer('asad_security_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $results = asad_scan_for_malware();
    wp_send_json_success($results);
}
add_action('wp_ajax_asad_scan_malware', 'asad_ajax_scan_malware');

/**
 * AJAX: Run security audit
 */
function asad_ajax_security_audit() {
    check_ajax_referer('asad_security_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $audit = asad_security_audit();
    wp_send_json_success($audit);
}
add_action('wp_ajax_asad_security_audit', 'asad_ajax_security_audit');

/**
 * AJAX: Block IP
 */
function asad_ajax_block_ip() {
    check_ajax_referer('asad_security_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $ip = sanitize_text_field($_POST['ip'] ?? '');
    $reason = sanitize_text_field($_POST['reason'] ?? '');

    if (empty($ip)) {
        wp_send_json_error('IP address is required');
    }

    $result = asad_block_ip($ip, $reason);

    if ($result) {
        wp_send_json_success('IP blocked successfully');
    } else {
        wp_send_json_error('Failed to block IP');
    }
}
add_action('wp_ajax_asad_block_ip', 'asad_ajax_block_ip');

/**
 * AJAX: Unblock IP
 */
function asad_ajax_unblock_ip() {
    check_ajax_referer('asad_security_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $ip = sanitize_text_field($_POST['ip'] ?? '');

    if (empty($ip)) {
        wp_send_json_error('IP address is required');
    }

    $result = asad_unblock_ip($ip);

    if ($result) {
        wp_send_json_success('IP unblocked successfully');
    } else {
        wp_send_json_error('Failed to unblock IP');
    }
}
add_action('wp_ajax_asad_unblock_ip', 'asad_ajax_unblock_ip');

/**
 * AJAX: Get security logs
 */
function asad_ajax_get_security_logs() {
    check_ajax_referer('asad_security_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $logs = asad_get_security_logs(100);
    wp_send_json_success($logs);
}
add_action('wp_ajax_asad_get_security_logs', 'asad_ajax_get_security_logs');
