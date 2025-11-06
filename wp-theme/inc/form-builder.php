<?php
/**
 * Contact Form Builder
 *
 * @package Asad_Portfolio_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create Forms Table on Theme Activation
 */
function asad_create_forms_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Forms table
    $table_forms = $wpdb->prefix . 'asad_forms';
    $sql_forms = "CREATE TABLE IF NOT EXISTS $table_forms (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        fields longtext NOT NULL,
        settings longtext,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // Form submissions table
    $table_submissions = $wpdb->prefix . 'asad_form_submissions';
    $sql_submissions = "CREATE TABLE IF NOT EXISTS $table_submissions (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        form_id mediumint(9) NOT NULL,
        data longtext NOT NULL,
        ip_address varchar(45),
        user_agent text,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY form_id (form_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_forms);
    dbDelta($sql_submissions);
}
add_action('after_switch_theme', 'asad_create_forms_tables');

/**
 * Get All Forms
 */
function asad_get_all_forms() {
    global $wpdb;
    $table = $wpdb->prefix . 'asad_forms';
    return $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");
}

/**
 * Get Form by ID
 */
function asad_get_form($form_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'asad_forms';
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $form_id));
}

/**
 * Save Form
 */
function asad_save_form($form_id, $name, $fields, $settings = array()) {
    global $wpdb;
    $table = $wpdb->prefix . 'asad_forms';

    $data = array(
        'name' => sanitize_text_field($name),
        'fields' => json_encode($fields),
        'settings' => json_encode($settings),
    );

    if ($form_id) {
        // Update existing form
        $wpdb->update($table, $data, array('id' => $form_id));
        return $form_id;
    } else {
        // Create new form
        $wpdb->insert($table, $data);
        return $wpdb->insert_id;
    }
}

/**
 * Delete Form
 */
function asad_delete_form($form_id) {
    global $wpdb;
    $table_forms = $wpdb->prefix . 'asad_forms';
    $table_submissions = $wpdb->prefix . 'asad_form_submissions';

    // Delete submissions
    $wpdb->delete($table_submissions, array('form_id' => $form_id));

    // Delete form
    return $wpdb->delete($table_forms, array('id' => $form_id));
}

/**
 * AJAX: Create/Update Form
 */
function asad_ajax_save_form() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => __('Permission denied.', 'asad-portfolio')));
    }

    $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $fields = isset($_POST['fields']) ? json_decode(stripslashes($_POST['fields']), true) : array();
    $settings = isset($_POST['settings']) ? json_decode(stripslashes($_POST['settings']), true) : array();

    if (empty($name)) {
        wp_send_json_error(array('message' => __('Form name is required.', 'asad-portfolio')));
    }

    $saved_id = asad_save_form($form_id, $name, $fields, $settings);

    if ($saved_id) {
        wp_send_json_success(array(
            'message' => __('Form saved successfully.', 'asad-portfolio'),
            'form_id' => $saved_id
        ));
    } else {
        wp_send_json_error(array('message' => __('Failed to save form.', 'asad-portfolio')));
    }
}
add_action('wp_ajax_asad_save_form', 'asad_ajax_save_form');

/**
 * AJAX: Delete Form
 */
function asad_ajax_delete_form() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => __('Permission denied.', 'asad-portfolio')));
    }

    $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;

    if (asad_delete_form($form_id)) {
        wp_send_json_success(array('message' => __('Form deleted successfully.', 'asad-portfolio')));
    } else {
        wp_send_json_error(array('message' => __('Failed to delete form.', 'asad-portfolio')));
    }
}
add_action('wp_ajax_asad_delete_form', 'asad_ajax_delete_form');

/**
 * AJAX: Submit Form (Frontend)
 */
function asad_ajax_submit_form() {
    check_ajax_referer('asad-form-nonce', 'nonce');

    $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
    $form_data = isset($_POST['form_data']) ? $_POST['form_data'] : array();

    if (!$form_id) {
        wp_send_json_error(array('message' => __('Invalid form.', 'asad-portfolio')));
    }

    $form = asad_get_form($form_id);
    if (!$form) {
        wp_send_json_error(array('message' => __('Form not found.', 'asad-portfolio')));
    }

    $fields = json_decode($form->fields, true);
    $settings = json_decode($form->settings, true);

    // Validate required fields
    foreach ($fields as $field) {
        if (isset($field['required']) && $field['required']) {
            $field_name = $field['name'];
            if (empty($form_data[$field_name])) {
                wp_send_json_error(array('message' => sprintf(__('%s is required.', 'asad-portfolio'), $field['label'])));
            }
        }

        // Validate email
        if ($field['type'] === 'email' && !empty($form_data[$field['name']])) {
            if (!is_email($form_data[$field['name']])) {
                wp_send_json_error(array('message' => sprintf(__('%s must be a valid email.', 'asad-portfolio'), $field['label'])));
            }
        }
    }

    // Sanitize form data
    $sanitized_data = array();
    foreach ($form_data as $key => $value) {
        if (is_array($value)) {
            $sanitized_data[$key] = array_map('sanitize_text_field', $value);
        } else {
            $sanitized_data[$key] = sanitize_text_field($value);
        }
    }

    // Save submission
    global $wpdb;
    $table = $wpdb->prefix . 'asad_form_submissions';
    $wpdb->insert($table, array(
        'form_id' => $form_id,
        'data' => json_encode($sanitized_data),
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
    ));

    // Send email notification
    if (isset($settings['email_notifications']) && $settings['email_notifications']) {
        $to = isset($settings['notification_email']) ? $settings['notification_email'] : get_option('admin_email');
        $subject = isset($settings['email_subject']) ? $settings['email_subject'] : sprintf(__('New submission for %s', 'asad-portfolio'), $form->name);

        $message = sprintf(__('New form submission for: %s', 'asad-portfolio'), $form->name) . "\n\n";
        foreach ($sanitized_data as $key => $value) {
            $message .= ucfirst($key) . ': ' . (is_array($value) ? implode(', ', $value) : $value) . "\n";
        }
        $message .= "\n" . sprintf(__('Submitted from: %s', 'asad-portfolio'), $_SERVER['REMOTE_ADDR']);

        wp_mail($to, $subject, $message);
    }

    $success_message = isset($settings['success_message']) ? $settings['success_message'] : __('Thank you! Your form has been submitted successfully.', 'asad-portfolio');

    wp_send_json_success(array('message' => $success_message));
}
add_action('wp_ajax_asad_submit_form', 'asad_ajax_submit_form');
add_action('wp_ajax_nopriv_asad_submit_form', 'asad_ajax_submit_form');

/**
 * Get Form Submissions
 */
function asad_get_form_submissions($form_id, $limit = 100) {
    global $wpdb;
    $table = $wpdb->prefix . 'asad_form_submissions';
    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table WHERE form_id = %d ORDER BY created_at DESC LIMIT %d",
        $form_id,
        $limit
    ));
}

/**
 * AJAX: Get Submissions
 */
function asad_ajax_get_submissions() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => __('Permission denied.', 'asad-portfolio')));
    }

    $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
    $submissions = asad_get_form_submissions($form_id);

    wp_send_json_success(array('submissions' => $submissions));
}
add_action('wp_ajax_asad_get_submissions', 'asad_ajax_get_submissions');

/**
 * Form Shortcode
 */
function asad_form_shortcode($atts) {
    $atts = shortcode_atts(array('id' => 0), $atts);
    $form_id = intval($atts['id']);

    if (!$form_id) {
        return '<p>' . __('Invalid form ID.', 'asad-portfolio') . '</p>';
    }

    $form = asad_get_form($form_id);
    if (!$form) {
        return '<p>' . __('Form not found.', 'asad-portfolio') . '</p>';
    }

    $fields = json_decode($form->fields, true);
    $settings = json_decode($form->settings, true);

    ob_start();
    ?>
    <div class="asad-form-wrapper" data-form-id="<?php echo esc_attr($form_id); ?>">
        <form class="asad-contact-form" id="asad-form-<?php echo esc_attr($form_id); ?>">
            <?php wp_nonce_field('asad-form-nonce', 'asad_form_nonce_' . $form_id); ?>
            <input type="hidden" name="form_id" value="<?php echo esc_attr($form_id); ?>">

            <?php foreach ($fields as $field) : ?>
                <div class="asad-form-field asad-field-<?php echo esc_attr($field['type']); ?>">
                    <label for="<?php echo esc_attr($field['name']); ?>">
                        <?php echo esc_html($field['label']); ?>
                        <?php if (isset($field['required']) && $field['required']) : ?>
                            <span class="required">*</span>
                        <?php endif; ?>
                    </label>

                    <?php
                    switch ($field['type']) {
                        case 'text':
                        case 'email':
                        case 'tel':
                        case 'number':
                            ?>
                            <input type="<?php echo esc_attr($field['type']); ?>"
                                   id="<?php echo esc_attr($field['name']); ?>"
                                   name="<?php echo esc_attr($field['name']); ?>"
                                   placeholder="<?php echo isset($field['placeholder']) ? esc_attr($field['placeholder']) : ''; ?>"
                                   <?php echo (isset($field['required']) && $field['required']) ? 'required' : ''; ?>>
                            <?php
                            break;

                        case 'textarea':
                            ?>
                            <textarea id="<?php echo esc_attr($field['name']); ?>"
                                      name="<?php echo esc_attr($field['name']); ?>"
                                      rows="<?php echo isset($field['rows']) ? esc_attr($field['rows']) : '5'; ?>"
                                      placeholder="<?php echo isset($field['placeholder']) ? esc_attr($field['placeholder']) : ''; ?>"
                                      <?php echo (isset($field['required']) && $field['required']) ? 'required' : ''; ?>></textarea>
                            <?php
                            break;

                        case 'select':
                            ?>
                            <select id="<?php echo esc_attr($field['name']); ?>"
                                    name="<?php echo esc_attr($field['name']); ?>"
                                    <?php echo (isset($field['required']) && $field['required']) ? 'required' : ''; ?>>
                                <option value="">-- <?php _e('Select', 'asad-portfolio'); ?> --</option>
                                <?php if (isset($field['options'])) : ?>
                                    <?php foreach ($field['options'] as $option) : ?>
                                        <option value="<?php echo esc_attr($option); ?>"><?php echo esc_html($option); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php
                            break;

                        case 'checkbox':
                            ?>
                            <div class="checkbox-group">
                                <?php if (isset($field['options'])) : ?>
                                    <?php foreach ($field['options'] as $option) : ?>
                                        <label class="checkbox-label">
                                            <input type="checkbox"
                                                   name="<?php echo esc_attr($field['name']); ?>[]"
                                                   value="<?php echo esc_attr($option); ?>">
                                            <?php echo esc_html($option); ?>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <?php
                            break;

                        case 'radio':
                            ?>
                            <div class="radio-group">
                                <?php if (isset($field['options'])) : ?>
                                    <?php foreach ($field['options'] as $option) : ?>
                                        <label class="radio-label">
                                            <input type="radio"
                                                   name="<?php echo esc_attr($field['name']); ?>"
                                                   value="<?php echo esc_attr($option); ?>"
                                                   <?php echo (isset($field['required']) && $field['required']) ? 'required' : ''; ?>>
                                            <?php echo esc_html($option); ?>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <?php
                            break;
                    }
                    ?>

                    <?php if (isset($field['description']) && $field['description']) : ?>
                        <small class="field-description"><?php echo esc_html($field['description']); ?></small>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <div class="asad-form-submit">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i>
                    <?php echo isset($settings['submit_text']) ? esc_html($settings['submit_text']) : __('Submit', 'asad-portfolio'); ?>
                </button>
            </div>

            <div class="asad-form-message"></div>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('asad_form', 'asad_form_shortcode');

/**
 * Enqueue Form Styles and Scripts
 */
function asad_enqueue_form_scripts() {
    if (is_singular() && has_shortcode(get_post()->post_content, 'asad_form')) {
        wp_enqueue_style('asad-forms', ASAD_THEME_URI . '/assets/css/forms.css', array(), ASAD_THEME_VERSION);
        wp_enqueue_script('asad-forms', ASAD_THEME_URI . '/assets/js/forms.js', array('jquery'), ASAD_THEME_VERSION, true);

        wp_localize_script('asad-forms', 'asadForms', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
        ));
    }
}
add_action('wp_enqueue_scripts', 'asad_enqueue_form_scripts');
