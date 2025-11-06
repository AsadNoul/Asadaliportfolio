<?php
/**
 * Database Manager
 * Backup, optimize, import/export database
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get database info
 */
function asad_get_database_info() {
    global $wpdb;

    $info = array();

    // Database size
    $result = $wpdb->get_row($wpdb->prepare(
        "SELECT
            SUM(data_length + index_length) as size,
            SUM(data_free) as free
        FROM information_schema.TABLES
        WHERE table_schema = %s",
        DB_NAME
    ));

    $info['total_size'] = $result->size ?? 0;
    $info['free_space'] = $result->free ?? 0;
    $info['total_size_formatted'] = size_format($info['total_size']);

    // Tables info
    $tables = $wpdb->get_results($wpdb->prepare(
        "SELECT
            table_name,
            table_rows,
            data_length + index_length as size,
            data_free as free_space,
            engine
        FROM information_schema.TABLES
        WHERE table_schema = %s
        ORDER BY (data_length + index_length) DESC",
        DB_NAME
    ), ARRAY_A);

    $info['tables'] = array();
    foreach ($tables as $table) {
        $info['tables'][] = array(
            'name' => $table['table_name'],
            'rows' => intval($table['table_rows']),
            'size' => intval($table['size']),
            'size_formatted' => size_format($table['size']),
            'free_space' => intval($table['free_space']),
            'engine' => $table['engine']
        );
    }

    $info['table_count'] = count($info['tables']);

    // Get MySQL version
    $info['mysql_version'] = $wpdb->get_var("SELECT VERSION()");

    return $info;
}

/**
 * Optimize database tables
 */
function asad_optimize_database_tables() {
    global $wpdb;

    $tables = $wpdb->get_col("SHOW TABLES");
    $results = array(
        'optimized' => 0,
        'failed' => 0,
        'tables' => array()
    );

    foreach ($tables as $table) {
        $result = $wpdb->query("OPTIMIZE TABLE `$table`");

        if ($result !== false) {
            $results['optimized']++;
            $results['tables'][] = array(
                'table' => $table,
                'status' => 'success'
            );
        } else {
            $results['failed']++;
            $results['tables'][] = array(
                'table' => $table,
                'status' => 'failed'
            );
        }
    }

    return $results;
}

/**
 * Repair database tables
 */
function asad_repair_database_tables() {
    global $wpdb;

    $tables = $wpdb->get_col("SHOW TABLES");
    $results = array(
        'repaired' => 0,
        'failed' => 0,
        'tables' => array()
    );

    foreach ($tables as $table) {
        $result = $wpdb->query("REPAIR TABLE `$table`");

        if ($result !== false) {
            $results['repaired']++;
            $results['tables'][] = array(
                'table' => $table,
                'status' => 'success'
            );
        } else {
            $results['failed']++;
            $results['tables'][] = array(
                'table' => $table,
                'status' => 'failed'
            );
        }
    }

    return $results;
}

/**
 * Create database backup
 */
function asad_create_database_backup() {
    global $wpdb;

    $upload_dir = wp_upload_dir();
    $backup_dir = $upload_dir['basedir'] . '/db-backups';

    // Create backup directory if it doesn't exist
    if (!file_exists($backup_dir)) {
        wp_mkdir_p($backup_dir);

        // Add .htaccess to protect backup files
        file_put_contents($backup_dir . '/.htaccess', 'Deny from all');
    }

    $filename = 'backup-' . DB_NAME . '-' . date('Y-m-d-His') . '.sql';
    $filepath = $backup_dir . '/' . $filename;

    // Get all tables
    $tables = $wpdb->get_col("SHOW TABLES");

    $sql_dump = '';

    // Add database info
    $sql_dump .= "-- WordPress Database Backup\n";
    $sql_dump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $sql_dump .= "-- Database: " . DB_NAME . "\n";
    $sql_dump .= "-- --------------------------------------------------\n\n";

    foreach ($tables as $table) {
        // Get table structure
        $create_table = $wpdb->get_row("SHOW CREATE TABLE `$table`", ARRAY_N);
        $sql_dump .= "\n\n-- --------------------------------------------------------\n";
        $sql_dump .= "-- Table structure for table `$table`\n";
        $sql_dump .= "-- --------------------------------------------------------\n\n";
        $sql_dump .= "DROP TABLE IF EXISTS `$table`;\n";
        $sql_dump .= $create_table[1] . ";\n\n";

        // Get table data
        $rows = $wpdb->get_results("SELECT * FROM `$table`", ARRAY_A);

        if (!empty($rows)) {
            $sql_dump .= "-- Dumping data for table `$table`\n\n";

            foreach ($rows as $row) {
                $sql_dump .= "INSERT INTO `$table` VALUES(";
                $values = array();

                foreach ($row as $value) {
                    if (is_null($value)) {
                        $values[] = 'NULL';
                    } else {
                        $values[] = "'" . $wpdb->_real_escape($value) . "'";
                    }
                }

                $sql_dump .= implode(',', $values);
                $sql_dump .= ");\n";
            }

            $sql_dump .= "\n";
        }
    }

    // Write to file
    $result = file_put_contents($filepath, $sql_dump);

    if ($result) {
        // Compress the file
        if (function_exists('gzencode')) {
            $compressed = gzencode(file_get_contents($filepath), 9);
            file_put_contents($filepath . '.gz', $compressed);
            unlink($filepath);
            $filepath .= '.gz';
            $filename .= '.gz';
        }

        return array(
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'size' => filesize($filepath),
            'size_formatted' => size_format(filesize($filepath))
        );
    }

    return array('success' => false, 'message' => 'Failed to create backup file');
}

/**
 * Get list of backups
 */
function asad_get_database_backups() {
    $upload_dir = wp_upload_dir();
    $backup_dir = $upload_dir['basedir'] . '/db-backups';

    if (!file_exists($backup_dir)) {
        return array();
    }

    $backups = array();
    $files = glob($backup_dir . '/backup-*.sql*');

    foreach ($files as $file) {
        if (basename($file) !== '.htaccess') {
            $backups[] = array(
                'filename' => basename($file),
                'filepath' => $file,
                'size' => filesize($file),
                'size_formatted' => size_format(filesize($file)),
                'date' => date('Y-m-d H:i:s', filemtime($file)),
                'timestamp' => filemtime($file)
            );
        }
    }

    // Sort by date, newest first
    usort($backups, function($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
    });

    return $backups;
}

/**
 * Delete backup
 */
function asad_delete_backup($filename) {
    $upload_dir = wp_upload_dir();
    $backup_dir = $upload_dir['basedir'] . '/db-backups';
    $filepath = $backup_dir . '/' . basename($filename);

    if (file_exists($filepath) && strpos($filename, 'backup-') === 0) {
        return unlink($filepath);
    }

    return false;
}

/**
 * Download backup
 */
function asad_download_backup($filename) {
    $upload_dir = wp_upload_dir();
    $backup_dir = $upload_dir['basedir'] . '/db-backups';
    $filepath = $backup_dir . '/' . basename($filename);

    if (!file_exists($filepath) || strpos($filename, 'backup-') !== 0) {
        return false;
    }

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Content-Length: ' . filesize($filepath));
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');

    readfile($filepath);
    exit;
}

/**
 * Import database from SQL file
 */
function asad_import_database($file_path) {
    global $wpdb;

    if (!file_exists($file_path)) {
        return array('success' => false, 'message' => 'File not found');
    }

    // Check if compressed
    if (substr($file_path, -3) === '.gz') {
        $sql_content = gzdecode(file_get_contents($file_path));
    } else {
        $sql_content = file_get_contents($file_path);
    }

    if ($sql_content === false) {
        return array('success' => false, 'message' => 'Failed to read file');
    }

    // Split SQL statements
    $statements = array_filter(
        array_map('trim', explode(";\n", $sql_content)),
        function($statement) {
            return !empty($statement) && substr($statement, 0, 2) !== '--';
        }
    );

    $executed = 0;
    $failed = 0;
    $errors = array();

    foreach ($statements as $statement) {
        $result = $wpdb->query($statement);

        if ($result === false) {
            $failed++;
            $errors[] = array(
                'statement' => substr($statement, 0, 100) . '...',
                'error' => $wpdb->last_error
            );
        } else {
            $executed++;
        }
    }

    return array(
        'success' => true,
        'executed' => $executed,
        'failed' => $failed,
        'errors' => $errors
    );
}

/**
 * Clean database - remove transients, revisions, trash, etc.
 */
function asad_clean_database() {
    global $wpdb;

    $results = array(
        'deleted' => array()
    );

    // Delete expired transients
    $deleted = $wpdb->query(
        "DELETE FROM {$wpdb->options}
        WHERE option_name LIKE '_transient_timeout_%'
        AND option_value < UNIX_TIMESTAMP()"
    );
    $results['deleted']['expired_transients'] = $deleted;

    // Delete orphaned transients
    $deleted = $wpdb->query(
        "DELETE FROM {$wpdb->options}
        WHERE option_name LIKE '_transient_%'
        AND option_name NOT LIKE '_transient_timeout_%'
        AND option_name NOT IN (
            SELECT REPLACE(option_name, '_transient_timeout_', '_transient_')
            FROM {$wpdb->options}
            WHERE option_name LIKE '_transient_timeout_%'
        )"
    );
    $results['deleted']['orphaned_transients'] = $deleted;

    // Delete post revisions
    $deleted = $wpdb->query(
        "DELETE FROM {$wpdb->posts} WHERE post_type = 'revision'"
    );
    $results['deleted']['revisions'] = $deleted;

    // Delete auto-drafts
    $deleted = $wpdb->query(
        "DELETE FROM {$wpdb->posts} WHERE post_status = 'auto-draft'"
    );
    $results['deleted']['auto_drafts'] = $deleted;

    // Delete trashed posts
    $deleted = $wpdb->query(
        "DELETE FROM {$wpdb->posts} WHERE post_status = 'trash'"
    );
    $results['deleted']['trash'] = $deleted;

    // Delete spam comments
    $deleted = $wpdb->query(
        "DELETE FROM {$wpdb->comments} WHERE comment_approved = 'spam'"
    );
    $results['deleted']['spam_comments'] = $deleted;

    // Delete trashed comments
    $deleted = $wpdb->query(
        "DELETE FROM {$wpdb->comments} WHERE comment_approved = 'trash'"
    );
    $results['deleted']['trash_comments'] = $deleted;

    // Delete orphaned post meta
    $deleted = $wpdb->query(
        "DELETE pm FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE p.ID IS NULL"
    );
    $results['deleted']['orphaned_postmeta'] = $deleted;

    // Delete orphaned comment meta
    $deleted = $wpdb->query(
        "DELETE cm FROM {$wpdb->commentmeta} cm
        LEFT JOIN {$wpdb->comments} c ON c.comment_ID = cm.comment_id
        WHERE c.comment_ID IS NULL"
    );
    $results['deleted']['orphaned_commentmeta'] = $deleted;

    // Delete orphaned user meta
    $deleted = $wpdb->query(
        "DELETE um FROM {$wpdb->usermeta} um
        LEFT JOIN {$wpdb->users} u ON u.ID = um.user_id
        WHERE u.ID IS NULL"
    );
    $results['deleted']['orphaned_usermeta'] = $deleted;

    return $results;
}

/**
 * Get database statistics
 */
function asad_get_database_stats() {
    global $wpdb;

    $stats = array();

    // Count various items
    $stats['posts'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish'");
    $stats['pages'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish'");
    $stats['revisions'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'revision'");
    $stats['auto_drafts'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'auto-draft'");
    $stats['trash_posts'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'trash'");
    $stats['comments'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = '1'");
    $stats['spam_comments'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = 'spam'");
    $stats['trash_comments'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = 'trash'");
    $stats['users'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->users}");
    $stats['transients'] = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");

    return $stats;
}

/**
 * AJAX: Get database info
 */
function asad_ajax_get_db_info() {
    check_ajax_referer('asad_db_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $info = asad_get_database_info();
    $stats = asad_get_database_stats();
    $backups = asad_get_database_backups();

    wp_send_json_success(array(
        'info' => $info,
        'stats' => $stats,
        'backups' => $backups
    ));
}
add_action('wp_ajax_asad_get_db_info', 'asad_ajax_get_db_info');

/**
 * AJAX: Optimize database
 */
function asad_ajax_optimize_database() {
    check_ajax_referer('asad_db_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $results = asad_optimize_database_tables();
    wp_send_json_success($results);
}
add_action('wp_ajax_asad_optimize_database', 'asad_ajax_optimize_database');

/**
 * AJAX: Repair database
 */
function asad_ajax_repair_database() {
    check_ajax_referer('asad_db_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $results = asad_repair_database_tables();
    wp_send_json_success($results);
}
add_action('wp_ajax_asad_repair_database', 'asad_ajax_repair_database');

/**
 * AJAX: Create backup
 */
function asad_ajax_create_backup() {
    check_ajax_referer('asad_db_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    set_time_limit(300); // 5 minutes

    $result = asad_create_database_backup();

    if ($result['success']) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error($result['message']);
    }
}
add_action('wp_ajax_asad_create_backup', 'asad_ajax_create_backup');

/**
 * AJAX: Delete backup
 */
function asad_ajax_delete_backup() {
    check_ajax_referer('asad_db_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $filename = sanitize_file_name($_POST['filename'] ?? '');

    if (empty($filename)) {
        wp_send_json_error('Filename is required');
    }

    $result = asad_delete_backup($filename);

    if ($result) {
        wp_send_json_success('Backup deleted successfully');
    } else {
        wp_send_json_error('Failed to delete backup');
    }
}
add_action('wp_ajax_asad_delete_backup', 'asad_ajax_delete_backup');

/**
 * AJAX: Clean database
 */
function asad_ajax_clean_database() {
    check_ajax_referer('asad_db_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $results = asad_clean_database();
    wp_send_json_success($results);
}
add_action('wp_ajax_asad_clean_database', 'asad_ajax_clean_database');

/**
 * Handle backup download
 */
function asad_handle_backup_download() {
    if (isset($_GET['asad_download_backup']) && current_user_can('manage_options')) {
        check_admin_referer('asad_download_backup');
        $filename = sanitize_file_name($_GET['asad_download_backup']);
        asad_download_backup($filename);
    }
}
add_action('admin_init', 'asad_handle_backup_download');
