<?php
/**
 * Social Media Manager Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$settings = asad_get_social_settings();
?>

<div class="wrap asad-admin-page">
    <h1>Social Media Manager</h1>
    <p>Auto-post to social media, manage sharing, and display social feeds</p>

    <div class="asad-tabs">
        <div class="asad-tab-buttons">
            <button class="asad-tab-button active" data-tab="autopost">Auto-Post</button>
            <button class="asad-tab-button" data-tab="sharing">Social Sharing</button>
            <button class="asad-tab-button" data-tab="queue">Post Queue</button>
            <button class="asad-tab-button" data-tab="accounts">Accounts</button>
        </div>

        <!-- Auto-Post Tab -->
        <div class="asad-tab-content active" id="autopost-tab">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Auto-Post Settings</h2>
                </div>
                <div class="asad-card-body">
                    <form id="autopost-settings-form">
                        <table class="form-table">
                            <tr>
                                <th>Enable Auto-Post</th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="auto_post_enabled" value="1" <?php checked($settings['auto_post_enabled'], true); ?>>
                                        Automatically post to social media when publishing new posts
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th>Post to Platforms</th>
                                <td>
                                    <label style="display: block; margin: 5px 0;">
                                        <input type="checkbox" name="auto_post_platforms[]" value="twitter" <?php checked(in_array('twitter', $settings['auto_post_platforms'] ?? [])); ?>>
                                        Twitter
                                    </label>
                                    <label style="display: block; margin: 5px 0;">
                                        <input type="checkbox" name="auto_post_platforms[]" value="facebook" <?php checked(in_array('facebook', $settings['auto_post_platforms'] ?? [])); ?>>
                                        Facebook
                                    </label>
                                    <label style="display: block; margin: 5px 0;">
                                        <input type="checkbox" name="auto_post_platforms[]" value="linkedin" <?php checked(in_array('linkedin', $settings['auto_post_platforms'] ?? [])); ?>>
                                        LinkedIn
                                    </label>
                                </td>
                            </tr>
                        </table>
                        <p class="submit">
                            <button type="submit" class="button button-primary">Save Settings</button>
                        </p>
                    </form>

                    <div class="asad-info-box" style="margin-top: 30px;">
                        <h3>Note: API Integration Required</h3>
                        <p>To use auto-posting features, you need to connect your social media accounts via their respective APIs. This requires API keys and authentication tokens.</p>
                        <p>In a production environment, you would integrate with:</p>
                        <ul>
                            <li>Twitter API v2</li>
                            <li>Facebook Graph API</li>
                            <li>LinkedIn API</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Sharing Tab -->
        <div class="asad-tab-content" id="sharing-tab">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Social Sharing Buttons</h2>
                </div>
                <div class="asad-card-body">
                    <form id="sharing-settings-form">
                        <table class="form-table">
                            <tr>
                                <th>Enable Share Buttons</th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="share_buttons_enabled" value="1" <?php checked($settings['share_buttons_enabled'], true); ?>>
                                        Show social sharing buttons on posts
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th>Button Position</th>
                                <td>
                                    <select name="share_buttons_position">
                                        <option value="top" <?php selected($settings['share_buttons_position'], 'top'); ?>>Above Content</option>
                                        <option value="bottom" <?php selected($settings['share_buttons_position'], 'bottom'); ?>>Below Content</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>Platforms</th>
                                <td>
                                    <label style="display: block; margin: 5px 0;">
                                        <input type="checkbox" name="share_buttons_platforms[]" value="facebook" <?php checked(in_array('facebook', $settings['share_buttons_platforms'] ?? [])); ?>>
                                        Facebook
                                    </label>
                                    <label style="display: block; margin: 5px 0;">
                                        <input type="checkbox" name="share_buttons_platforms[]" value="twitter" <?php checked(in_array('twitter', $settings['share_buttons_platforms'] ?? [])); ?>>
                                        Twitter
                                    </label>
                                    <label style="display: block; margin: 5px 0;">
                                        <input type="checkbox" name="share_buttons_platforms[]" value="linkedin" <?php checked(in_array('linkedin', $settings['share_buttons_platforms'] ?? [])); ?>>
                                        LinkedIn
                                    </label>
                                    <label style="display: block; margin: 5px 0;">
                                        <input type="checkbox" name="share_buttons_platforms[]" value="pinterest" <?php checked(in_array('pinterest', $settings['share_buttons_platforms'] ?? [])); ?>>
                                        Pinterest
                                    </label>
                                    <label style="display: block; margin: 5px 0;">
                                        <input type="checkbox" name="share_buttons_platforms[]" value="whatsapp" <?php checked(in_array('whatsapp', $settings['share_buttons_platforms'] ?? [])); ?>>
                                        WhatsApp
                                    </label>
                                    <label style="display: block; margin: 5px 0;">
                                        <input type="checkbox" name="share_buttons_platforms[]" value="email" <?php checked(in_array('email', $settings['share_buttons_platforms'] ?? [])); ?>>
                                        Email
                                    </label>
                                </td>
                            </tr>
                        </table>
                        <p class="submit">
                            <button type="submit" class="button button-primary">Save Settings</button>
                        </p>
                    </form>
                </div>
            </div>
        </div>

        <!-- Post Queue Tab -->
        <div class="asad-tab-content" id="queue-tab">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Scheduled Posts</h2>
                    <button type="button" class="button" id="schedule-post-btn">Schedule New Post</button>
                </div>
                <div class="asad-card-body">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Platform</th>
                                <th>Message</th>
                                <th>Scheduled Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="post-queue-list">
                            <tr>
                                <td colspan="5">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Accounts Tab -->
        <div class="asad-tab-content" id="accounts-tab">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Connected Accounts</h2>
                </div>
                <div class="asad-card-body">
                    <form id="accounts-settings-form">
                        <table class="form-table">
                            <tr>
                                <th><label for="twitter-username">Twitter Username</label></th>
                                <td>
                                    <input type="text" id="twitter-username" name="twitter_username" class="regular-text" value="<?php echo esc_attr($settings['twitter_username']); ?>" placeholder="@username">
                                </td>
                            </tr>
                            <tr>
                                <th><label for="facebook-page-id">Facebook Page ID</label></th>
                                <td>
                                    <input type="text" id="facebook-page-id" name="facebook_page_id" class="regular-text" value="<?php echo esc_attr($settings['facebook_page_id']); ?>">
                                </td>
                            </tr>
                            <tr>
                                <th><label for="instagram-username">Instagram Username</label></th>
                                <td>
                                    <input type="text" id="instagram-username" name="instagram_username" class="regular-text" value="<?php echo esc_attr($settings['instagram_username']); ?>" placeholder="@username">
                                </td>
                            </tr>
                            <tr>
                                <th><label for="linkedin-company-id">LinkedIn Company ID</label></th>
                                <td>
                                    <input type="text" id="linkedin-company-id" name="linkedin_company_id" class="regular-text" value="<?php echo esc_attr($settings['linkedin_company_id']); ?>">
                                </td>
                            </tr>
                            <tr>
                                <th><label for="pinterest-username">Pinterest Username</label></th>
                                <td>
                                    <input type="text" id="pinterest-username" name="pinterest_username" class="regular-text" value="<?php echo esc_attr($settings['pinterest_username']); ?>" placeholder="@username">
                                </td>
                            </tr>
                        </table>
                        <p class="submit">
                            <button type="submit" class="button button-primary">Save Settings</button>
                        </p>
                    </form>

                    <h3 style="margin-top: 40px;">Social Feed Widget</h3>
                    <p>Display social media feeds on your site using the shortcode:</p>
                    <code>[asad_social_feed platform="twitter" username="yourname" count="5"]</code>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    const socialNonce = '<?php echo wp_create_nonce('asad_social_nonce'); ?>';

    // Tab switching
    $('.asad-tab-button').on('click', function() {
        const tab = $(this).data('tab');
        $('.asad-tab-button').removeClass('active');
        $('.asad-tab-content').removeClass('active');
        $(this).addClass('active');
        $('#' + tab + '-tab').addClass('active');

        if (tab === 'queue') {
            loadPostQueue();
        }
    });

    // Save settings forms
    $('#autopost-settings-form, #sharing-settings-form, #accounts-settings-form').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serialize();
        const button = $(this).find('button[type="submit"]');
        button.prop('disabled', true).text('Saving...');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData + '&action=asad_update_social_settings&nonce=' + socialNonce,
            success: function(response) {
                if (response.success) {
                    alert('Settings saved successfully!');
                } else {
                    alert('Error: ' + response.data);
                }
            },
            complete: function() {
                button.prop('disabled', false).text('Save Settings');
            }
        });
    });

    // Load post queue
    function loadPostQueue() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_get_social_queue',
                nonce: socialNonce
            },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    let html = '';
                    response.data.forEach(item => {
                        html += `<tr>
                            <td>${item.platform}</td>
                            <td>${item.message.substring(0, 100)}...</td>
                            <td>${item.scheduled_date}</td>
                            <td><span class="asad-badge asad-badge-${item.status}">${item.status}</span></td>
                            <td><button class="button button-small">View</button></td>
                        </tr>`;
                    });
                    $('#post-queue-list').html(html);
                } else {
                    $('#post-queue-list').html('<tr><td colspan="5">No scheduled posts</td></tr>');
                }
            }
        });
    }
});
</script>

<style>
.asad-info-box {
    background: #e7f2fa;
    border-left: 4px solid #3498db;
    padding: 20px;
    border-radius: 4px;
}

.asad-info-box h3 {
    margin-top: 0;
}

.asad-info-box ul {
    margin-left: 20px;
}

.asad-badge-pending { background: #f39c12; color: #fff; }
.asad-badge-posted { background: #2ecc71; color: #fff; }
.asad-badge-failed { background: #e74c3c; color: #fff; }
</style>
