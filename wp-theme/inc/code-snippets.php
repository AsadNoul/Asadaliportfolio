<?php
/**
 * Code Snippets Manager
 * Add custom PHP/JS/CSS without editing theme files
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create code snippets tables
 */
function asad_create_code_snippets_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Snippets table
    $table_name = $wpdb->prefix . 'asad_code_snippets';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        description text,
        code longtext NOT NULL,
        code_type varchar(20) NOT NULL,
        location varchar(50) NOT NULL,
        priority int(11) NOT NULL DEFAULT 10,
        is_active tinyint(1) NOT NULL DEFAULT 1,
        conditions longtext,
        created_by bigint(20) NOT NULL,
        created_date datetime NOT NULL,
        modified_date datetime,
        version int(11) NOT NULL DEFAULT 1,
        PRIMARY KEY (id),
        KEY is_active (is_active),
        KEY code_type (code_type)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Snippet versions (for version control)
    $versions_table = $wpdb->prefix . 'asad_snippet_versions';
    $sql2 = "CREATE TABLE IF NOT EXISTS $versions_table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        snippet_id bigint(20) NOT NULL,
        code longtext NOT NULL,
        version int(11) NOT NULL,
        created_by bigint(20) NOT NULL,
        created_date datetime NOT NULL,
        note text,
        PRIMARY KEY (id),
        KEY snippet_id (snippet_id)
    ) $charset_collate;";

    dbDelta($sql2);
}
add_action('after_switch_theme', 'asad_create_code_snippets_tables');

/**
 * Get all snippets
 */
function asad_get_snippets($type = 'all', $active_only = false) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_code_snippets';
    $where = array();

    if ($type !== 'all') {
        $where[] = $wpdb->prepare("code_type = %s", $type);
    }

    if ($active_only) {
        $where[] = "is_active = 1";
    }

    $where_clause = !empty($where) ? "WHERE " . implode(' AND ', $where) : '';

    return $wpdb->get_results(
        "SELECT * FROM $table_name $where_clause ORDER BY priority ASC, created_date DESC",
        ARRAY_A
    );
}

/**
 * Get snippet by ID
 */
function asad_get_snippet($snippet_id) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_code_snippets';

    return $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d",
        $snippet_id
    ), ARRAY_A);
}

/**
 * Save snippet
 */
function asad_save_snippet($data) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_code_snippets';

    $snippet_data = array(
        'name' => sanitize_text_field($data['name']),
        'description' => sanitize_textarea_field($data['description'] ?? ''),
        'code' => $data['code'], // Don't sanitize code
        'code_type' => sanitize_text_field($data['code_type']),
        'location' => sanitize_text_field($data['location']),
        'priority' => intval($data['priority'] ?? 10),
        'is_active' => isset($data['is_active']) ? 1 : 0,
        'conditions' => json_encode($data['conditions'] ?? array()),
        'modified_date' => current_time('mysql')
    );

    if (isset($data['id']) && $data['id'] > 0) {
        // Update existing snippet
        $snippet_id = intval($data['id']);

        // Save version history
        $old_snippet = asad_get_snippet($snippet_id);
        if ($old_snippet) {
            asad_save_snippet_version($snippet_id, $old_snippet['code'], $old_snippet['version']);
        }

        $snippet_data['version'] = intval($old_snippet['version']) + 1;

        $wpdb->update(
            $table_name,
            $snippet_data,
            array('id' => $snippet_id),
            array('%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%d'),
            array('%d')
        );

        return $snippet_id;
    } else {
        // Insert new snippet
        $snippet_data['created_by'] = get_current_user_id();
        $snippet_data['created_date'] = current_time('mysql');

        $wpdb->insert(
            $table_name,
            $snippet_data,
            array('%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%d', '%s')
        );

        return $wpdb->insert_id;
    }
}

/**
 * Save snippet version
 */
function asad_save_snippet_version($snippet_id, $code, $version, $note = '') {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_snippet_versions';

    return $wpdb->insert(
        $table_name,
        array(
            'snippet_id' => $snippet_id,
            'code' => $code,
            'version' => $version,
            'created_by' => get_current_user_id(),
            'created_date' => current_time('mysql'),
            'note' => $note
        ),
        array('%d', '%s', '%d', '%d', '%s', '%s')
    );
}

/**
 * Get snippet versions
 */
function asad_get_snippet_versions($snippet_id) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_snippet_versions';

    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE snippet_id = %d ORDER BY version DESC",
        $snippet_id
    ), ARRAY_A);
}

/**
 * Delete snippet
 */
function asad_delete_snippet($snippet_id) {
    global $wpdb;

    $snippets_table = $wpdb->prefix . 'asad_code_snippets';
    $versions_table = $wpdb->prefix . 'asad_snippet_versions';

    // Delete versions
    $wpdb->delete($versions_table, array('snippet_id' => $snippet_id), array('%d'));

    // Delete snippet
    return $wpdb->delete($snippets_table, array('id' => $snippet_id), array('%d'));
}

/**
 * Toggle snippet status
 */
function asad_toggle_snippet($snippet_id, $active) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_code_snippets';

    return $wpdb->update(
        $table_name,
        array('is_active' => $active ? 1 : 0),
        array('id' => $snippet_id),
        array('%d'),
        array('%d')
    );
}

/**
 * Check if snippet should run based on conditions
 */
function asad_should_run_snippet($conditions) {
    if (empty($conditions) || $conditions === '[]') {
        return true; // No conditions, run everywhere
    }

    $conditions = json_decode($conditions, true);

    if (!is_array($conditions)) {
        return true;
    }

    // Check page type conditions
    if (isset($conditions['pages'])) {
        $pages = $conditions['pages'];

        if (in_array('all', $pages)) {
            return true;
        }

        if (is_front_page() && in_array('homepage', $pages)) {
            return true;
        }

        if (is_single() && in_array('posts', $pages)) {
            return true;
        }

        if (is_page() && in_array('pages', $pages)) {
            return true;
        }

        if (is_archive() && in_array('archives', $pages)) {
            return true;
        }

        if (is_404() && in_array('404', $pages)) {
            return true;
        }

        if (is_admin() && in_array('admin', $pages)) {
            return true;
        }
    }

    // Check specific post/page IDs
    if (isset($conditions['post_ids']) && !empty($conditions['post_ids'])) {
        global $post;
        if ($post && in_array($post->ID, $conditions['post_ids'])) {
            return true;
        }
    }

    // Check user roles
    if (isset($conditions['user_roles']) && !empty($conditions['user_roles'])) {
        $user = wp_get_current_user();
        if (!empty(array_intersect($conditions['user_roles'], $user->roles))) {
            return true;
        }
    }

    return false;
}

/**
 * Execute PHP snippets
 */
function asad_execute_php_snippets() {
    $snippets = asad_get_snippets('php', true);

    foreach ($snippets as $snippet) {
        if (asad_should_run_snippet($snippet['conditions'])) {
            try {
                eval('?>' . $snippet['code']);
            } catch (Exception $e) {
                error_log('Snippet error (' . $snippet['name'] . '): ' . $e->getMessage());
            }
        }
    }
}
add_action('init', 'asad_execute_php_snippets', 999);

/**
 * Output CSS snippets
 */
function asad_output_css_snippets() {
    $snippets = asad_get_snippets('css', true);

    if (empty($snippets)) {
        return;
    }

    echo '<style type="text/css" id="asad-custom-css">' . "\n";

    foreach ($snippets as $snippet) {
        if (asad_should_run_snippet($snippet['conditions'])) {
            echo "/* " . esc_html($snippet['name']) . " */\n";
            echo $snippet['code'] . "\n\n";
        }
    }

    echo '</style>' . "\n";
}
add_action('wp_head', 'asad_output_css_snippets', 999);

/**
 * Output JS snippets
 */
function asad_output_js_snippets() {
    $snippets = asad_get_snippets('javascript', true);

    if (empty($snippets)) {
        return;
    }

    $location = is_admin() ? 'admin' : 'frontend';

    foreach ($snippets as $snippet) {
        if ($snippet['location'] !== $location && $snippet['location'] !== 'both') {
            continue;
        }

        if (asad_should_run_snippet($snippet['conditions'])) {
            ?>
            <script type="text/javascript">
            /* <?php echo esc_html($snippet['name']); ?> */
            <?php echo $snippet['code']; ?>
            </script>
            <?php
        }
    }
}
add_action('wp_footer', 'asad_output_js_snippets', 999);
add_action('admin_footer', 'asad_output_js_snippets', 999);

/**
 * Export snippets
 */
function asad_export_snippets($snippet_ids = array()) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_code_snippets';

    if (empty($snippet_ids)) {
        $snippets = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    } else {
        $ids = implode(',', array_map('intval', $snippet_ids));
        $snippets = $wpdb->get_results("SELECT * FROM $table_name WHERE id IN ($ids)", ARRAY_A);
    }

    return array(
        'version' => '1.0',
        'export_date' => current_time('mysql'),
        'snippets' => $snippets
    );
}

/**
 * Import snippets
 */
function asad_import_snippets($data) {
    if (!isset($data['snippets']) || !is_array($data['snippets'])) {
        return array('success' => false, 'message' => 'Invalid import data');
    }

    $imported = 0;

    foreach ($data['snippets'] as $snippet) {
        // Remove ID to create new snippet
        unset($snippet['id']);
        unset($snippet['version']);

        $snippet['created_by'] = get_current_user_id();
        $snippet['created_date'] = current_time('mysql');

        if (asad_save_snippet($snippet)) {
            $imported++;
        }
    }

    return array(
        'success' => true,
        'message' => "Successfully imported $imported snippets"
    );
}

/**
 * Get snippet library (popular snippets)
 */
function asad_get_snippet_library() {
    return array(
        array(
            'name' => 'Disable WordPress Admin Bar',
            'code_type' => 'php',
            'code' => "add_filter('show_admin_bar', '__return_false');",
            'description' => 'Hide the admin bar for all users on the frontend'
        ),
        array(
            'name' => 'Custom Login Logo',
            'code_type' => 'css',
            'code' => ".login h1 a {\n    background-image: url('YOUR-LOGO-URL');\n    width: 300px;\n    height: 80px;\n    background-size: contain;\n}",
            'description' => 'Replace WordPress login logo with your own'
        ),
        array(
            'name' => 'Smooth Scroll to Anchor Links',
            'code_type' => 'javascript',
            'code' => "document.querySelectorAll('a[href^=\"#\"]').forEach(anchor => {\n    anchor.addEventListener('click', function (e) {\n        e.preventDefault();\n        document.querySelector(this.getAttribute('href')).scrollIntoView({\n            behavior: 'smooth'\n        });\n    });\n});",
            'description' => 'Add smooth scrolling for anchor links'
        ),
        array(
            'name' => 'Remove WordPress Version',
            'code_type' => 'php',
            'code' => "remove_action('wp_head', 'wp_generator');\nadd_filter('the_generator', '__return_empty_string');",
            'description' => 'Hide WordPress version for security'
        ),
        array(
            'name' => 'Custom Excerpt Length',
            'code_type' => 'php',
            'code' => "function custom_excerpt_length(\$length) {\n    return 30;\n}\nadd_filter('excerpt_length', 'custom_excerpt_length');",
            'description' => 'Change the default excerpt length to 30 words'
        ),
        array(
            'name' => 'Disable Right Click',
            'code_type' => 'javascript',
            'code' => "document.addEventListener('contextmenu', function(e) {\n    e.preventDefault();\n    alert('Right-click is disabled');\n});",
            'description' => 'Prevent right-click on your website'
        ),
        array(
            'name' => 'Hide Update Notices for Non-Admins',
            'code_type' => 'php',
            'code' => "if (!current_user_can('administrator')) {\n    remove_action('admin_notices', 'update_nag', 3);\n}",
            'description' => 'Hide WordPress update notices for non-admin users'
        ),
        array(
            'name' => 'Custom CSS Variables',
            'code_type' => 'css',
            'code' => ":root {\n    --brand-primary: #007bff;\n    --brand-secondary: #6c757d;\n    --brand-success: #28a745;\n    --brand-danger: #dc3545;\n}",
            'description' => 'Define custom CSS variables for consistent branding'
        )
    );
}

/**
 * AJAX: Save snippet
 */
function asad_ajax_save_snippet() {
    check_ajax_referer('asad_snippets_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $data = array(
        'id' => intval($_POST['id'] ?? 0),
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'code' => stripslashes($_POST['code'] ?? ''),
        'code_type' => $_POST['code_type'] ?? 'php',
        'location' => $_POST['location'] ?? 'frontend',
        'priority' => intval($_POST['priority'] ?? 10),
        'is_active' => isset($_POST['is_active']),
        'conditions' => json_decode(stripslashes($_POST['conditions'] ?? '[]'), true)
    );

    $snippet_id = asad_save_snippet($data);

    if ($snippet_id) {
        wp_send_json_success(array('id' => $snippet_id, 'message' => 'Snippet saved successfully'));
    } else {
        wp_send_json_error('Failed to save snippet');
    }
}
add_action('wp_ajax_asad_save_snippet', 'asad_ajax_save_snippet');

/**
 * AJAX: Delete snippet
 */
function asad_ajax_delete_snippet() {
    check_ajax_referer('asad_snippets_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $snippet_id = intval($_POST['snippet_id'] ?? 0);

    if (asad_delete_snippet($snippet_id)) {
        wp_send_json_success('Snippet deleted');
    } else {
        wp_send_json_error('Failed to delete snippet');
    }
}
add_action('wp_ajax_asad_delete_snippet', 'asad_ajax_delete_snippet');

/**
 * AJAX: Toggle snippet
 */
function asad_ajax_toggle_snippet() {
    check_ajax_referer('asad_snippets_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $snippet_id = intval($_POST['snippet_id'] ?? 0);
    $active = isset($_POST['active']) && $_POST['active'] === 'true';

    if (asad_toggle_snippet($snippet_id, $active)) {
        wp_send_json_success('Snippet toggled');
    } else {
        wp_send_json_error('Failed to toggle snippet');
    }
}
add_action('wp_ajax_asad_toggle_snippet', 'asad_ajax_toggle_snippet');

/**
 * AJAX: Export snippets
 */
function asad_ajax_export_snippets() {
    check_ajax_referer('asad_snippets_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $snippet_ids = isset($_POST['snippet_ids']) ? array_map('intval', $_POST['snippet_ids']) : array();
    $data = asad_export_snippets($snippet_ids);

    wp_send_json_success($data);
}
add_action('wp_ajax_asad_export_snippets', 'asad_ajax_export_snippets');

/**
 * AJAX: Import snippets
 */
function asad_ajax_import_snippets() {
    check_ajax_referer('asad_snippets_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $json_data = stripslashes($_POST['import_data'] ?? '');
    $data = json_decode($json_data, true);

    if (!$data) {
        wp_send_json_error('Invalid JSON data');
    }

    $result = asad_import_snippets($data);

    if ($result['success']) {
        wp_send_json_success($result['message']);
    } else {
        wp_send_json_error($result['message']);
    }
}
add_action('wp_ajax_asad_import_snippets', 'asad_ajax_import_snippets');
