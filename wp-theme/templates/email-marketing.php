<?php
/**
 * Email Marketing Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$subscriber_count = asad_get_subscriber_count('subscribed');
$total_count = asad_get_subscriber_count('all');
$campaigns = asad_get_campaigns('all');
?>

<div class="wrap asad-admin-page">
    <h1>Email Marketing</h1>
    <p>Manage subscribers, create campaigns, and send newsletters</p>

    <div class="asad-stats-grid" style="margin: 20px 0;">
        <div class="asad-stat-card">
            <div class="asad-stat-icon" style="background: #2ecc71;">
                <span class="dashicons dashicons-groups"></span>
            </div>
            <div class="asad-stat-content">
                <h3><?php echo number_format($subscriber_count); ?></h3>
                <p>Active Subscribers</p>
            </div>
        </div>

        <div class="asad-stat-card">
            <div class="asad-stat-icon" style="background: #3498db;">
                <span class="dashicons dashicons-email"></span>
            </div>
            <div class="asad-stat-content">
                <h3><?php echo count($campaigns); ?></h3>
                <p>Total Campaigns</p>
            </div>
        </div>

        <div class="asad-stat-card">
            <div class="asad-stat-icon" style="background: #f39c12;">
                <span class="dashicons dashicons-chart-line"></span>
            </div>
            <div class="asad-stat-content">
                <h3>0%</h3>
                <p>Avg Open Rate</p>
            </div>
        </div>

        <div class="asad-stat-card">
            <div class="asad-stat-icon" style="background: #e74c3c;">
                <span class="dashicons dashicons-admin-links"></span>
            </div>
            <div class="asad-stat-content">
                <h3>0%</h3>
                <p>Avg Click Rate</p>
            </div>
        </div>
    </div>

    <div class="asad-tabs">
        <div class="asad-tab-buttons">
            <button class="asad-tab-button active" data-tab="subscribers">Subscribers</button>
            <button class="asad-tab-button" data-tab="campaigns">Campaigns</button>
            <button class="asad-tab-button" data-tab="create">Create Campaign</button>
            <button class="asad-tab-button" data-tab="settings">Settings</button>
        </div>

        <!-- Subscribers Tab -->
        <div class="asad-tab-content active" id="subscribers-tab">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Subscriber List</h2>
                    <div>
                        <button type="button" class="button" id="add-subscriber-btn">Add Subscriber</button>
                        <button type="button" class="button" id="export-subscribers">Export CSV</button>
                    </div>
                </div>
                <div class="asad-card-body">
                    <div class="asad-subscriber-filters">
                        <select id="subscriber-status">
                            <option value="subscribed">Subscribed</option>
                            <option value="unsubscribed">Unsubscribed</option>
                            <option value="all">All</option>
                        </select>
                        <button type="button" class="button" id="filter-subscribers">Filter</button>
                    </div>

                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Source</th>
                                <th>Subscribed Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="subscribers-list">
                            <?php
                            $subscribers = asad_get_subscribers('subscribed', 50, 0);
                            if (!empty($subscribers)):
                                foreach ($subscribers as $sub): ?>
                                    <tr data-id="<?php echo $sub['id']; ?>">
                                        <td><?php echo esc_html($sub['email']); ?></td>
                                        <td><?php echo esc_html($sub['name'] ?: '-'); ?></td>
                                        <td>
                                            <span class="asad-badge asad-badge-<?php echo $sub['status']; ?>">
                                                <?php echo esc_html($sub['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo esc_html($sub['source']); ?></td>
                                        <td><?php echo esc_html($sub['subscribed_date']); ?></td>
                                        <td>
                                            <button type="button" class="button button-small delete-subscriber" data-id="<?php echo $sub['id']; ?>">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="6">No subscribers yet</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Campaigns Tab -->
        <div class="asad-tab-content" id="campaigns-tab">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Email Campaigns</h2>
                </div>
                <div class="asad-card-body">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Campaign Name</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Sent</th>
                                <th>Opens</th>
                                <th>Clicks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($campaigns)):
                                foreach ($campaigns as $campaign): ?>
                                    <tr>
                                        <td><?php echo esc_html($campaign['name']); ?></td>
                                        <td><?php echo esc_html($campaign['subject']); ?></td>
                                        <td>
                                            <span class="asad-badge asad-badge-<?php echo $campaign['status']; ?>">
                                                <?php echo esc_html($campaign['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo number_format($campaign['sent_count']); ?></td>
                                        <td><?php echo number_format($campaign['open_count']); ?></td>
                                        <td><?php echo number_format($campaign['click_count']); ?></td>
                                        <td>
                                            <?php if ($campaign['status'] === 'draft'): ?>
                                                <button type="button" class="button button-primary button-small send-campaign" data-id="<?php echo $campaign['id']; ?>">
                                                    Send
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="button button-small delete-campaign" data-id="<?php echo $campaign['id']; ?>">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="7">No campaigns yet</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Campaign Tab -->
        <div class="asad-tab-content" id="create-tab">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Create New Campaign</h2>
                </div>
                <div class="asad-card-body">
                    <form id="create-campaign-form">
                        <table class="form-table">
                            <tr>
                                <th><label for="campaign-name">Campaign Name</label></th>
                                <td><input type="text" id="campaign-name" name="name" class="regular-text" required></td>
                            </tr>
                            <tr>
                                <th><label for="campaign-subject">Email Subject</label></th>
                                <td><input type="text" id="campaign-subject" name="subject" class="regular-text" required></td>
                            </tr>
                            <tr>
                                <th><label for="campaign-from-name">From Name</label></th>
                                <td><input type="text" id="campaign-from-name" name="from_name" class="regular-text" value="<?php echo get_bloginfo('name'); ?>" required></td>
                            </tr>
                            <tr>
                                <th><label for="campaign-from-email">From Email</label></th>
                                <td><input type="email" id="campaign-from-email" name="from_email" class="regular-text" value="<?php echo get_bloginfo('admin_email'); ?>" required></td>
                            </tr>
                            <tr>
                                <th><label for="campaign-content">Email Content</label></th>
                                <td>
                                    <?php
                                    wp_editor('', 'campaign-content', array(
                                        'textarea_name' => 'content',
                                        'textarea_rows' => 15,
                                        'media_buttons' => true
                                    ));
                                    ?>
                                    <p class="description">Available tags: {{name}}, {{email}}</p>
                                </td>
                            </tr>
                        </table>
                        <p class="submit">
                            <button type="submit" class="button button-primary" name="save_draft">Save as Draft</button>
                            <button type="button" class="button button-primary" id="send-now">Send Now</button>
                        </p>
                    </form>
                </div>
            </div>
        </div>

        <!-- Settings Tab -->
        <div class="asad-tab-content" id="settings-tab">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Email Settings</h2>
                </div>
                <div class="asad-card-body">
                    <h3>Newsletter Form Shortcode</h3>
                    <p>Use this shortcode to add a newsletter subscription form to your pages:</p>
                    <code>[asad_newsletter title="Subscribe" button="Subscribe" placeholder="Enter your email"]</code>

                    <h3 style="margin-top: 30px;">SMTP Settings</h3>
                    <p>Configure SMTP settings for better email delivery. (Coming soon)</p>

                    <h3 style="margin-top: 30px;">Automation</h3>
                    <p>Set up automated email sequences and welcome emails. (Coming soon)</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    const emailNonce = '<?php echo wp_create_nonce('asad_email_marketing_nonce'); ?>';

    // Tab switching
    $('.asad-tab-button').on('click', function() {
        const tab = $(this).data('tab');
        $('.asad-tab-button').removeClass('active');
        $('.asad-tab-content').removeClass('active');
        $(this).addClass('active');
        $('#' + tab + '-tab').addClass('active');
    });

    // Create campaign
    $('#create-campaign-form').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            action: 'asad_create_campaign',
            nonce: emailNonce,
            name: $('#campaign-name').val(),
            subject: $('#campaign-subject').val(),
            from_name: $('#campaign-from-name').val(),
            from_email: $('#campaign-from-email').val(),
            content: tinyMCE.get('campaign-content').getContent()
        };

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Campaign created successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });

    // Send campaign
    $(document).on('click', '.send-campaign', function() {
        if (!confirm('Send this campaign to all subscribers?')) {
            return;
        }

        const button = $(this);
        const campaignId = button.data('id');
        button.prop('disabled', true).text('Sending...');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_send_campaign',
                nonce: emailNonce,
                campaign_id: campaignId
            },
            success: function(response) {
                if (response.success) {
                    alert('Campaign sent: ' + response.data.sent + ' emails sent, ' + response.data.failed + ' failed');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                    button.prop('disabled', false).text('Send');
                }
            }
        });
    });

    // Delete subscriber
    $(document).on('click', '.delete-subscriber', function() {
        if (!confirm('Delete this subscriber?')) {
            return;
        }

        const subscriberId = $(this).data('id');
        const row = $(this).closest('tr');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_delete_subscriber',
                nonce: emailNonce,
                subscriber_id: subscriberId
            },
            success: function(response) {
                if (response.success) {
                    row.remove();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });

    // Delete campaign
    $(document).on('click', '.delete-campaign', function() {
        if (!confirm('Delete this campaign?')) {
            return;
        }

        const campaignId = $(this).data('id');
        const row = $(this).closest('tr');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_delete_campaign',
                nonce: emailNonce,
                campaign_id: campaignId
            },
            success: function(response) {
                if (response.success) {
                    row.remove();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
});
</script>

<style>
.asad-subscriber-filters {
    margin-bottom: 20px;
    display: flex;
    gap: 10px;
}

.asad-badge-subscribed { background: #2ecc71; color: #fff; }
.asad-badge-unsubscribed { background: #95a5a6; color: #fff; }
.asad-badge-draft { background: #95a5a6; color: #fff; }
.asad-badge-sent { background: #2ecc71; color: #fff; }
</style>
