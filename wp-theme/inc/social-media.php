<?php
/**
 * Social Media Manager
 * Auto-post, social sharing, feed widgets
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create social media tables
 */
function asad_create_social_media_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Social accounts table
    $accounts_table = $wpdb->prefix . 'asad_social_accounts';
    $sql = "CREATE TABLE IF NOT EXISTS $accounts_table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        platform varchar(50) NOT NULL,
        account_name varchar(255) NOT NULL,
        access_token text,
        refresh_token text,
        expires_at datetime,
        is_active tinyint(1) NOT NULL DEFAULT 1,
        created_date datetime NOT NULL,
        PRIMARY KEY (id),
        KEY platform (platform)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Auto-post queue table
    $queue_table = $wpdb->prefix . 'asad_social_queue';
    $sql2 = "CREATE TABLE IF NOT EXISTS $queue_table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) NOT NULL,
        platform varchar(50) NOT NULL,
        account_id bigint(20) NOT NULL,
        message text NOT NULL,
        scheduled_date datetime NOT NULL,
        status varchar(20) NOT NULL DEFAULT 'pending',
        posted_date datetime,
        platform_post_id varchar(255),
        error_message text,
        PRIMARY KEY (id),
        KEY post_id (post_id),
        KEY platform (platform),
        KEY status (status)
    ) $charset_collate;";

    dbDelta($sql2);

    // Social shares tracking
    $shares_table = $wpdb->prefix . 'asad_social_shares';
    $sql3 = "CREATE TABLE IF NOT EXISTS $shares_table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) NOT NULL,
        platform varchar(50) NOT NULL,
        share_count int(11) NOT NULL DEFAULT 0,
        last_updated datetime NOT NULL,
        PRIMARY KEY (id),
        KEY post_id (post_id),
        KEY platform (platform)
    ) $charset_collate;";

    dbDelta($sql3);
}
add_action('after_switch_theme', 'asad_create_social_media_tables');

/**
 * Get social media settings
 */
function asad_get_social_settings() {
    return get_option('asad_social_settings', array(
        'auto_post_enabled' => false,
        'auto_post_platforms' => array(),
        'twitter_username' => '',
        'facebook_page_id' => '',
        'instagram_username' => '',
        'linkedin_company_id' => '',
        'pinterest_username' => '',
        'share_buttons_enabled' => true,
        'share_buttons_position' => 'bottom',
        'share_buttons_platforms' => array('facebook', 'twitter', 'linkedin', 'pinterest')
    ));
}

/**
 * Update social media settings
 */
function asad_update_social_settings($settings) {
    return update_option('asad_social_settings', $settings);
}

/**
 * Add social share buttons to content
 */
function asad_add_social_share_buttons($content) {
    if (!is_single() && !is_page()) {
        return $content;
    }

    $settings = asad_get_social_settings();

    if (!$settings['share_buttons_enabled']) {
        return $content;
    }

    global $post;
    $url = urlencode(get_permalink());
    $title = urlencode(get_the_title());
    $image = urlencode(get_the_post_thumbnail_url($post->ID, 'full'));

    $buttons = '<div class="asad-social-share">';
    $buttons .= '<span class="asad-share-label">Share this:</span>';

    $platforms = $settings['share_buttons_platforms'];

    if (in_array('facebook', $platforms)) {
        $buttons .= '<a href="https://www.facebook.com/sharer/sharer.php?u=' . $url . '" target="_blank" class="asad-share-btn asad-share-facebook" title="Share on Facebook"><span class="dashicons dashicons-facebook"></span></a>';
    }

    if (in_array('twitter', $platforms)) {
        $buttons .= '<a href="https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title . '" target="_blank" class="asad-share-btn asad-share-twitter" title="Share on Twitter"><span class="dashicons dashicons-twitter"></span></a>';
    }

    if (in_array('linkedin', $platforms)) {
        $buttons .= '<a href="https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '&title=' . $title . '" target="_blank" class="asad-share-btn asad-share-linkedin" title="Share on LinkedIn"><span class="dashicons dashicons-linkedin"></span></a>';
    }

    if (in_array('pinterest', $platforms)) {
        $buttons .= '<a href="https://pinterest.com/pin/create/button/?url=' . $url . '&media=' . $image . '&description=' . $title . '" target="_blank" class="asad-share-btn asad-share-pinterest" title="Pin on Pinterest"><span class="dashicons dashicons-pinterest"></span></a>';
    }

    if (in_array('whatsapp', $platforms)) {
        $buttons .= '<a href="https://wa.me/?text=' . $title . '%20' . $url . '" target="_blank" class="asad-share-btn asad-share-whatsapp" title="Share on WhatsApp"><span class="dashicons dashicons-whatsapp"></span></a>';
    }

    if (in_array('email', $platforms)) {
        $buttons .= '<a href="mailto:?subject=' . $title . '&body=' . $url . '" class="asad-share-btn asad-share-email" title="Share via Email"><span class="dashicons dashicons-email"></span></a>';
    }

    $buttons .= '</div>';

    // Add styles
    $buttons .= '<style>
        .asad-social-share {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 30px 0;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        .asad-share-label {
            font-weight: 600;
            margin-right: 10px;
        }
        .asad-share-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: #fff;
            text-decoration: none;
            transition: transform 0.2s;
        }
        .asad-share-btn:hover {
            transform: scale(1.1);
        }
        .asad-share-facebook { background: #1877f2; }
        .asad-share-twitter { background: #1da1f2; }
        .asad-share-linkedin { background: #0077b5; }
        .asad-share-pinterest { background: #e60023; }
        .asad-share-whatsapp { background: #25d366; }
        .asad-share-email { background: #666; }
        .asad-share-btn .dashicons {
            font-size: 20px;
            width: 20px;
            height: 20px;
        }
    </style>';

    if ($settings['share_buttons_position'] === 'top') {
        return $buttons . $content;
    } else {
        return $content . $buttons;
    }
}
add_filter('the_content', 'asad_add_social_share_buttons');

/**
 * Auto-post to social media when post is published
 */
function asad_auto_post_to_social($post_id, $post, $update) {
    // Don't auto-post for updates
    if ($update) {
        return;
    }

    // Only for posts
    if ($post->post_type !== 'post') {
        return;
    }

    $settings = asad_get_social_settings();

    if (!$settings['auto_post_enabled']) {
        return;
    }

    // Prepare message
    $message = get_the_title($post_id) . "\n\n" . get_permalink($post_id);

    // Queue for each enabled platform
    foreach ($settings['auto_post_platforms'] as $platform) {
        asad_queue_social_post($post_id, $platform, $message);
    }
}
add_action('publish_post', 'asad_auto_post_to_social', 10, 3);

/**
 * Queue social post
 */
function asad_queue_social_post($post_id, $platform, $message, $scheduled_date = null) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_social_queue';

    if (!$scheduled_date) {
        $scheduled_date = current_time('mysql');
    }

    // Get account for platform
    $accounts_table = $wpdb->prefix . 'asad_social_accounts';
    $account = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $accounts_table WHERE platform = %s AND is_active = 1 LIMIT 1",
        $platform
    ));

    if (!$account) {
        return false;
    }

    return $wpdb->insert(
        $table_name,
        array(
            'post_id' => $post_id,
            'platform' => $platform,
            'account_id' => $account->id,
            'message' => $message,
            'scheduled_date' => $scheduled_date,
            'status' => 'pending'
        ),
        array('%d', '%s', '%d', '%s', '%s', '%s')
    );
}

/**
 * Process social media queue
 */
function asad_process_social_queue() {
    global $wpdb;

    $queue_table = $wpdb->prefix . 'asad_social_queue';

    // Get pending posts that are scheduled for now or earlier
    $pending = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $queue_table WHERE status = 'pending' AND scheduled_date <= %s LIMIT 10",
        current_time('mysql')
    ), ARRAY_A);

    foreach ($pending as $item) {
        // Post to platform
        $result = asad_post_to_platform($item['platform'], $item['message'], $item['post_id']);

        if ($result['success']) {
            // Update status to posted
            $wpdb->update(
                $queue_table,
                array(
                    'status' => 'posted',
                    'posted_date' => current_time('mysql'),
                    'platform_post_id' => $result['post_id'] ?? ''
                ),
                array('id' => $item['id']),
                array('%s', '%s', '%s'),
                array('%d')
            );
        } else {
            // Update status to failed
            $wpdb->update(
                $queue_table,
                array(
                    'status' => 'failed',
                    'error_message' => $result['error'] ?? 'Unknown error'
                ),
                array('id' => $item['id']),
                array('%s', '%s'),
                array('%d')
            );
        }
    }
}

/**
 * Post to social media platform
 * This is a simplified version - in production, you'd integrate with actual APIs
 */
function asad_post_to_platform($platform, $message, $post_id) {
    // This would contain actual API integration code for each platform
    // For now, return success as placeholder

    switch ($platform) {
        case 'twitter':
            // Twitter API integration would go here
            return array('success' => true, 'post_id' => 'twitter_' . time());

        case 'facebook':
            // Facebook API integration would go here
            return array('success' => true, 'post_id' => 'facebook_' . time());

        case 'linkedin':
            // LinkedIn API integration would go here
            return array('success' => true, 'post_id' => 'linkedin_' . time());

        default:
            return array('success' => false, 'error' => 'Unsupported platform');
    }
}

/**
 * Get social media feed widget
 */
function asad_social_feed_widget($platform, $username, $count = 5) {
    ob_start();
    ?>
    <div class="asad-social-feed" data-platform="<?php echo esc_attr($platform); ?>">
        <h3><?php echo esc_html(ucfirst($platform)); ?> Feed</h3>
        <div class="asad-feed-items">
            <?php
            // This would fetch actual feed items from the platform API
            // For now, show placeholder
            for ($i = 1; $i <= $count; $i++): ?>
                <div class="asad-feed-item">
                    <div class="asad-feed-content">
                        <p>Sample post content from <?php echo esc_html($platform); ?> #<?php echo $i; ?></p>
                    </div>
                    <div class="asad-feed-meta">
                        <span class="asad-feed-date"><?php echo human_time_diff(time() - ($i * 3600)); ?> ago</span>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        <a href="https://<?php echo esc_attr($platform); ?>.com/<?php echo esc_attr($username); ?>" target="_blank" class="asad-feed-follow">
            Follow @<?php echo esc_html($username); ?>
        </a>
    </div>
    <style>
        .asad-social-feed {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .asad-social-feed h3 {
            margin-top: 0;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }
        .asad-feed-items {
            margin: 20px 0;
        }
        .asad-feed-item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .asad-feed-item:last-child {
            border-bottom: none;
        }
        .asad-feed-content p {
            margin: 0 0 10px 0;
        }
        .asad-feed-meta {
            font-size: 12px;
            color: #999;
        }
        .asad-feed-follow {
            display: inline-block;
            padding: 10px 20px;
            background: var(--primary-color, #3498db);
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
    <?php
    return ob_get_clean();
}

/**
 * Social feed shortcode
 */
function asad_social_feed_shortcode($atts) {
    $atts = shortcode_atts(array(
        'platform' => 'twitter',
        'username' => '',
        'count' => 5
    ), $atts);

    return asad_social_feed_widget($atts['platform'], $atts['username'], $atts['count']);
}
add_shortcode('asad_social_feed', 'asad_social_feed_shortcode');

/**
 * Get social share counts (simplified)
 */
function asad_get_share_counts($post_id) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_social_shares';
    $url = get_permalink($post_id);

    // This would fetch actual share counts from platform APIs
    // For now, return mock data
    $counts = array(
        'facebook' => rand(10, 100),
        'twitter' => rand(5, 50),
        'linkedin' => rand(2, 30),
        'pinterest' => rand(1, 20)
    );

    return $counts;
}

/**
 * AJAX: Update social settings
 */
function asad_ajax_update_social_settings() {
    check_ajax_referer('asad_social_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $settings = array(
        'auto_post_enabled' => isset($_POST['auto_post_enabled']),
        'auto_post_platforms' => $_POST['auto_post_platforms'] ?? array(),
        'twitter_username' => sanitize_text_field($_POST['twitter_username'] ?? ''),
        'facebook_page_id' => sanitize_text_field($_POST['facebook_page_id'] ?? ''),
        'instagram_username' => sanitize_text_field($_POST['instagram_username'] ?? ''),
        'linkedin_company_id' => sanitize_text_field($_POST['linkedin_company_id'] ?? ''),
        'pinterest_username' => sanitize_text_field($_POST['pinterest_username'] ?? ''),
        'share_buttons_enabled' => isset($_POST['share_buttons_enabled']),
        'share_buttons_position' => sanitize_text_field($_POST['share_buttons_position'] ?? 'bottom'),
        'share_buttons_platforms' => $_POST['share_buttons_platforms'] ?? array()
    );

    asad_update_social_settings($settings);

    wp_send_json_success('Settings updated successfully');
}
add_action('wp_ajax_asad_update_social_settings', 'asad_ajax_update_social_settings');

/**
 * AJAX: Get social queue
 */
function asad_ajax_get_social_queue() {
    check_ajax_referer('asad_social_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    global $wpdb;
    $queue_table = $wpdb->prefix . 'asad_social_queue';

    $queue = $wpdb->get_results(
        "SELECT * FROM $queue_table ORDER BY scheduled_date DESC LIMIT 50",
        ARRAY_A
    );

    wp_send_json_success($queue);
}
add_action('wp_ajax_asad_get_social_queue', 'asad_ajax_get_social_queue');

/**
 * AJAX: Schedule social post
 */
function asad_ajax_schedule_social_post() {
    check_ajax_referer('asad_social_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $post_id = intval($_POST['post_id'] ?? 0);
    $platform = sanitize_text_field($_POST['platform'] ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');
    $scheduled_date = sanitize_text_field($_POST['scheduled_date'] ?? '');

    if (!$post_id || !$platform || !$message) {
        wp_send_json_error('Missing required fields');
    }

    $result = asad_queue_social_post($post_id, $platform, $message, $scheduled_date);

    if ($result) {
        wp_send_json_success('Post scheduled successfully');
    } else {
        wp_send_json_error('Failed to schedule post');
    }
}
add_action('wp_ajax_asad_schedule_social_post', 'asad_ajax_schedule_social_post');

/**
 * Cron job to process social media queue
 */
function asad_schedule_social_cron() {
    if (!wp_next_scheduled('asad_process_social_queue_cron')) {
        wp_schedule_event(time(), 'hourly', 'asad_process_social_queue_cron');
    }
}
add_action('wp', 'asad_schedule_social_cron');
add_action('asad_process_social_queue_cron', 'asad_process_social_queue');
