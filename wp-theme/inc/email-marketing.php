<?php
/**
 * Email Marketing
 * Newsletter forms, subscriber management, campaigns
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create email marketing tables
 */
function asad_create_email_marketing_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Subscribers table
    $subscribers_table = $wpdb->prefix . 'asad_subscribers';
    $sql1 = "CREATE TABLE IF NOT EXISTS $subscribers_table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        email varchar(255) NOT NULL,
        name varchar(255),
        status varchar(20) NOT NULL DEFAULT 'subscribed',
        source varchar(100),
        ip_address varchar(100),
        subscribed_date datetime NOT NULL,
        unsubscribed_date datetime,
        PRIMARY KEY (id),
        UNIQUE KEY email (email),
        KEY status (status)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql1);

    // Campaigns table
    $campaigns_table = $wpdb->prefix . 'asad_campaigns';
    $sql2 = "CREATE TABLE IF NOT EXISTS $campaigns_table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        subject varchar(255) NOT NULL,
        content longtext NOT NULL,
        from_name varchar(255) NOT NULL,
        from_email varchar(255) NOT NULL,
        status varchar(20) NOT NULL DEFAULT 'draft',
        sent_count int(11) NOT NULL DEFAULT 0,
        open_count int(11) NOT NULL DEFAULT 0,
        click_count int(11) NOT NULL DEFAULT 0,
        created_date datetime NOT NULL,
        sent_date datetime,
        PRIMARY KEY (id),
        KEY status (status)
    ) $charset_collate;";

    dbDelta($sql2);

    // Campaign logs table
    $logs_table = $wpdb->prefix . 'asad_campaign_logs';
    $sql3 = "CREATE TABLE IF NOT EXISTS $logs_table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        campaign_id bigint(20) NOT NULL,
        subscriber_id bigint(20) NOT NULL,
        email varchar(255) NOT NULL,
        status varchar(20) NOT NULL,
        sent_date datetime NOT NULL,
        opened_date datetime,
        clicked_date datetime,
        PRIMARY KEY (id),
        KEY campaign_id (campaign_id),
        KEY subscriber_id (subscriber_id)
    ) $charset_collate;";

    dbDelta($sql3);

    // Lists table
    $lists_table = $wpdb->prefix . 'asad_email_lists';
    $sql4 = "CREATE TABLE IF NOT EXISTS $lists_table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        description text,
        subscriber_count int(11) NOT NULL DEFAULT 0,
        created_date datetime NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    dbDelta($sql4);

    // List subscribers junction table
    $list_subs_table = $wpdb->prefix . 'asad_list_subscribers';
    $sql5 = "CREATE TABLE IF NOT EXISTS $list_subs_table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        list_id bigint(20) NOT NULL,
        subscriber_id bigint(20) NOT NULL,
        added_date datetime NOT NULL,
        PRIMARY KEY (id),
        KEY list_id (list_id),
        KEY subscriber_id (subscriber_id)
    ) $charset_collate;";

    dbDelta($sql5);
}
add_action('after_switch_theme', 'asad_create_email_marketing_tables');

/**
 * Add subscriber
 */
function asad_add_subscriber($email, $name = '', $source = 'manual') {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_subscribers';

    // Check if subscriber already exists
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE email = %s",
        $email
    ));

    if ($existing) {
        // If unsubscribed, resubscribe
        if ($existing->status === 'unsubscribed') {
            $wpdb->update(
                $table_name,
                array(
                    'status' => 'subscribed',
                    'subscribed_date' => current_time('mysql'),
                    'unsubscribed_date' => null
                ),
                array('id' => $existing->id),
                array('%s', '%s', '%s'),
                array('%d')
            );

            return array('success' => true, 'id' => $existing->id, 'message' => 'Resubscribed successfully');
        }

        return array('success' => false, 'message' => 'Email already subscribed');
    }

    // Add new subscriber
    $result = $wpdb->insert(
        $table_name,
        array(
            'email' => $email,
            'name' => $name,
            'status' => 'subscribed',
            'source' => $source,
            'ip_address' => asad_get_visitor_ip(),
            'subscribed_date' => current_time('mysql')
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s')
    );

    if ($result) {
        return array('success' => true, 'id' => $wpdb->insert_id, 'message' => 'Subscribed successfully');
    }

    return array('success' => false, 'message' => 'Failed to subscribe');
}

/**
 * Unsubscribe
 */
function asad_unsubscribe($email) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_subscribers';

    $result = $wpdb->update(
        $table_name,
        array(
            'status' => 'unsubscribed',
            'unsubscribed_date' => current_time('mysql')
        ),
        array('email' => $email),
        array('%s', '%s'),
        array('%s')
    );

    return $result !== false;
}

/**
 * Get subscribers
 */
function asad_get_subscribers($status = 'subscribed', $limit = 100, $offset = 0) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_subscribers';

    if ($status === 'all') {
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY subscribed_date DESC LIMIT %d OFFSET %d",
            $limit, $offset
        ), ARRAY_A);
    }

    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE status = %s ORDER BY subscribed_date DESC LIMIT %d OFFSET %d",
        $status, $limit, $offset
    ), ARRAY_A);
}

/**
 * Get subscriber count
 */
function asad_get_subscriber_count($status = 'subscribed') {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_subscribers';

    if ($status === 'all') {
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    }

    return $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE status = %s",
        $status
    ));
}

/**
 * Create campaign
 */
function asad_create_campaign($data) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_campaigns';

    $result = $wpdb->insert(
        $table_name,
        array(
            'name' => $data['name'],
            'subject' => $data['subject'],
            'content' => $data['content'],
            'from_name' => $data['from_name'],
            'from_email' => $data['from_email'],
            'status' => 'draft',
            'created_date' => current_time('mysql')
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );

    if ($result) {
        return array('success' => true, 'id' => $wpdb->insert_id);
    }

    return array('success' => false, 'message' => 'Failed to create campaign');
}

/**
 * Update campaign
 */
function asad_update_campaign($campaign_id, $data) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_campaigns';

    $result = $wpdb->update(
        $table_name,
        array(
            'name' => $data['name'],
            'subject' => $data['subject'],
            'content' => $data['content'],
            'from_name' => $data['from_name'],
            'from_email' => $data['from_email']
        ),
        array('id' => $campaign_id),
        array('%s', '%s', '%s', '%s', '%s'),
        array('%d')
    );

    return $result !== false;
}

/**
 * Get campaigns
 */
function asad_get_campaigns($status = 'all') {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_campaigns';

    if ($status === 'all') {
        return $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_date DESC", ARRAY_A);
    }

    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE status = %s ORDER BY created_date DESC",
        $status
    ), ARRAY_A);
}

/**
 * Get campaign
 */
function asad_get_campaign($campaign_id) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_campaigns';

    return $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d",
        $campaign_id
    ), ARRAY_A);
}

/**
 * Send campaign
 */
function asad_send_campaign($campaign_id) {
    global $wpdb;

    $campaign = asad_get_campaign($campaign_id);

    if (!$campaign) {
        return array('success' => false, 'message' => 'Campaign not found');
    }

    // Get subscribers
    $subscribers = asad_get_subscribers('subscribed', 10000, 0);

    if (empty($subscribers)) {
        return array('success' => false, 'message' => 'No subscribers found');
    }

    $logs_table = $wpdb->prefix . 'asad_campaign_logs';
    $sent_count = 0;
    $failed_count = 0;

    foreach ($subscribers as $subscriber) {
        // Prepare email content
        $content = str_replace(
            array('{{name}}', '{{email}}'),
            array($subscriber['name'], $subscriber['email']),
            $campaign['content']
        );

        // Add unsubscribe link
        $unsubscribe_link = home_url('?asad_unsubscribe=' . base64_encode($subscriber['email']));
        $content .= '<p style="font-size:12px;color:#999;margin-top:30px;">Don\'t want these emails? <a href="' . $unsubscribe_link . '">Unsubscribe</a></p>';

        // Send email
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $campaign['from_name'] . ' <' . $campaign['from_email'] . '>'
        );

        $sent = wp_mail($subscriber['email'], $campaign['subject'], $content, $headers);

        // Log the send
        $wpdb->insert(
            $logs_table,
            array(
                'campaign_id' => $campaign_id,
                'subscriber_id' => $subscriber['id'],
                'email' => $subscriber['email'],
                'status' => $sent ? 'sent' : 'failed',
                'sent_date' => current_time('mysql')
            ),
            array('%d', '%d', '%s', '%s', '%s')
        );

        if ($sent) {
            $sent_count++;
        } else {
            $failed_count++;
        }
    }

    // Update campaign status
    $campaigns_table = $wpdb->prefix . 'asad_campaigns';
    $wpdb->update(
        $campaigns_table,
        array(
            'status' => 'sent',
            'sent_count' => $sent_count,
            'sent_date' => current_time('mysql')
        ),
        array('id' => $campaign_id),
        array('%s', '%d', '%s'),
        array('%d')
    );

    return array(
        'success' => true,
        'sent' => $sent_count,
        'failed' => $failed_count,
        'message' => "Campaign sent to $sent_count subscribers"
    );
}

/**
 * Delete campaign
 */
function asad_delete_campaign($campaign_id) {
    global $wpdb;

    $campaigns_table = $wpdb->prefix . 'asad_campaigns';
    $logs_table = $wpdb->prefix . 'asad_campaign_logs';

    // Delete logs
    $wpdb->delete($logs_table, array('campaign_id' => $campaign_id), array('%d'));

    // Delete campaign
    $result = $wpdb->delete($campaigns_table, array('id' => $campaign_id), array('%d'));

    return $result !== false;
}

/**
 * Newsletter subscription shortcode
 */
function asad_newsletter_form_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => 'Subscribe to Our Newsletter',
        'button' => 'Subscribe',
        'placeholder' => 'Enter your email'
    ), $atts);

    ob_start();
    ?>
    <div class="asad-newsletter-form">
        <h3><?php echo esc_html($atts['title']); ?></h3>
        <form class="asad-newsletter-subscribe" method="post">
            <input type="email" name="subscriber_email" placeholder="<?php echo esc_attr($atts['placeholder']); ?>" required>
            <input type="text" name="subscriber_name" placeholder="Your Name (optional)">
            <button type="submit"><?php echo esc_html($atts['button']); ?></button>
            <div class="asad-newsletter-message"></div>
        </form>
    </div>
    <style>
        .asad-newsletter-form {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 8px;
            max-width: 500px;
            margin: 20px auto;
        }
        .asad-newsletter-form h3 {
            margin-bottom: 20px;
            text-align: center;
        }
        .asad-newsletter-subscribe input[type="email"],
        .asad-newsletter-subscribe input[type="text"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .asad-newsletter-subscribe button {
            width: 100%;
            padding: 12px;
            background: var(--primary-color, #3498db);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .asad-newsletter-subscribe button:hover {
            opacity: 0.9;
        }
        .asad-newsletter-message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            display: none;
        }
        .asad-newsletter-message.success {
            background: #d4edda;
            color: #155724;
            display: block;
        }
        .asad-newsletter-message.error {
            background: #f8d7da;
            color: #721c24;
            display: block;
        }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('asad_newsletter', 'asad_newsletter_form_shortcode');

/**
 * Handle newsletter subscription
 */
function asad_handle_newsletter_subscription() {
    if (isset($_GET['asad_unsubscribe'])) {
        $email = base64_decode($_GET['asad_unsubscribe']);
        if (asad_unsubscribe($email)) {
            wp_die('You have been unsubscribed successfully.', 'Unsubscribed');
        }
    }
}
add_action('init', 'asad_handle_newsletter_subscription');

/**
 * AJAX: Subscribe
 */
function asad_ajax_subscribe() {
    check_ajax_referer('asad_newsletter_nonce', 'nonce');

    $email = sanitize_email($_POST['email'] ?? '');
    $name = sanitize_text_field($_POST['name'] ?? '');

    if (empty($email) || !is_email($email)) {
        wp_send_json_error('Please enter a valid email address');
    }

    $result = asad_add_subscriber($email, $name, 'website_form');

    if ($result['success']) {
        wp_send_json_success($result['message']);
    } else {
        wp_send_json_error($result['message']);
    }
}
add_action('wp_ajax_asad_subscribe', 'asad_ajax_subscribe');
add_action('wp_ajax_nopriv_asad_subscribe', 'asad_ajax_subscribe');

/**
 * AJAX: Get subscribers
 */
function asad_ajax_get_subscribers() {
    check_ajax_referer('asad_email_marketing_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $status = sanitize_text_field($_POST['status'] ?? 'subscribed');
    $page = intval($_POST['page'] ?? 1);
    $per_page = 50;
    $offset = ($page - 1) * $per_page;

    $subscribers = asad_get_subscribers($status, $per_page, $offset);
    $total = asad_get_subscriber_count($status);

    wp_send_json_success(array(
        'subscribers' => $subscribers,
        'total' => $total,
        'page' => $page,
        'total_pages' => ceil($total / $per_page)
    ));
}
add_action('wp_ajax_asad_get_subscribers', 'asad_ajax_get_subscribers');

/**
 * AJAX: Send campaign
 */
function asad_ajax_send_campaign() {
    check_ajax_referer('asad_email_marketing_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $campaign_id = intval($_POST['campaign_id'] ?? 0);

    if (!$campaign_id) {
        wp_send_json_error('Invalid campaign ID');
    }

    $result = asad_send_campaign($campaign_id);

    if ($result['success']) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error($result['message']);
    }
}
add_action('wp_ajax_asad_send_campaign', 'asad_ajax_send_campaign');

/**
 * AJAX: Delete subscriber
 */
function asad_ajax_delete_subscriber() {
    check_ajax_referer('asad_email_marketing_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $subscriber_id = intval($_POST['subscriber_id'] ?? 0);

    if (!$subscriber_id) {
        wp_send_json_error('Invalid subscriber ID');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'asad_subscribers';

    $result = $wpdb->delete($table_name, array('id' => $subscriber_id), array('%d'));

    if ($result) {
        wp_send_json_success('Subscriber deleted');
    } else {
        wp_send_json_error('Failed to delete subscriber');
    }
}
add_action('wp_ajax_asad_delete_subscriber', 'asad_ajax_delete_subscriber');
