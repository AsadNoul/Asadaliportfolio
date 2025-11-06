<?php
/**
 * Database Manager Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$db_info = asad_get_database_info();
$db_stats = asad_get_database_stats();
$backups = asad_get_database_backups();
?>

<div class="wrap asad-admin-page">
    <h1>Database Manager</h1>
    <p>Backup, optimize, and manage your WordPress database</p>

    <div class="asad-stats-grid" style="margin: 20px 0;">
        <div class="asad-stat-card">
            <div class="asad-stat-icon" style="background: #3498db;">
                <span class="dashicons dashicons-database"></span>
            </div>
            <div class="asad-stat-content">
                <h3><?php echo $db_info['total_size_formatted']; ?></h3>
                <p>Database Size</p>
            </div>
        </div>

        <div class="asad-stat-card">
            <div class="asad-stat-icon" style="background: #2ecc71;">
                <span class="dashicons dashicons-admin-page"></span>
            </div>
            <div class="asad-stat-content">
                <h3><?php echo $db_info['table_count']; ?></h3>
                <p>Total Tables</p>
            </div>
        </div>

        <div class="asad-stat-card">
            <div class="asad-stat-icon" style="background: #f39c12;">
                <span class="dashicons dashicons-backup"></span>
            </div>
            <div class="asad-stat-content">
                <h3><?php echo count($backups); ?></h3>
                <p>Backups</p>
            </div>
        </div>

        <div class="asad-stat-card">
            <div class="asad-stat-icon" style="background: #e74c3c;">
                <span class="dashicons dashicons-admin-tools"></span>
            </div>
            <div class="asad-stat-content">
                <h3><?php echo $db_info['mysql_version']; ?></h3>
                <p>MySQL Version</p>
            </div>
        </div>
    </div>

    <div class="asad-tabs">
        <div class="asad-tab-buttons">
            <button class="asad-tab-button active" data-tab="overview">Overview</button>
            <button class="asad-tab-button" data-tab="optimize">Optimize</button>
            <button class="asad-tab-button" data-tab="backup">Backup</button>
            <button class="asad-tab-button" data-tab="tables">Tables</button>
        </div>

        <!-- Overview Tab -->
        <div class="asad-tab-content active" id="overview-tab">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Database Statistics</h2>
                </div>
                <div class="asad-card-body">
                    <div class="asad-db-stats-grid">
                        <div class="asad-db-stat-item">
                            <span class="dashicons dashicons-admin-post"></span>
                            <div>
                                <strong><?php echo number_format($db_stats['posts']); ?></strong>
                                <p>Published Posts</p>
                            </div>
                        </div>
                        <div class="asad-db-stat-item">
                            <span class="dashicons dashicons-admin-page"></span>
                            <div>
                                <strong><?php echo number_format($db_stats['pages']); ?></strong>
                                <p>Published Pages</p>
                            </div>
                        </div>
                        <div class="asad-db-stat-item">
                            <span class="dashicons dashicons-backup"></span>
                            <div>
                                <strong><?php echo number_format($db_stats['revisions']); ?></strong>
                                <p>Post Revisions</p>
                            </div>
                        </div>
                        <div class="asad-db-stat-item">
                            <span class="dashicons dashicons-media-document"></span>
                            <div>
                                <strong><?php echo number_format($db_stats['auto_drafts']); ?></strong>
                                <p>Auto Drafts</p>
                            </div>
                        </div>
                        <div class="asad-db-stat-item">
                            <span class="dashicons dashicons-trash"></span>
                            <div>
                                <strong><?php echo number_format($db_stats['trash_posts']); ?></strong>
                                <p>Trashed Posts</p>
                            </div>
                        </div>
                        <div class="asad-db-stat-item">
                            <span class="dashicons dashicons-admin-comments"></span>
                            <div>
                                <strong><?php echo number_format($db_stats['comments']); ?></strong>
                                <p>Comments</p>
                            </div>
                        </div>
                        <div class="asad-db-stat-item">
                            <span class="dashicons dashicons-dismiss"></span>
                            <div>
                                <strong><?php echo number_format($db_stats['spam_comments']); ?></strong>
                                <p>Spam Comments</p>
                            </div>
                        </div>
                        <div class="asad-db-stat-item">
                            <span class="dashicons dashicons-admin-users"></span>
                            <div>
                                <strong><?php echo number_format($db_stats['users']); ?></strong>
                                <p>Users</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Optimize Tab -->
        <div class="asad-tab-content" id="optimize-tab">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Database Optimization</h2>
                </div>
                <div class="asad-card-body">
                    <div class="asad-optimization-actions">
                        <div class="asad-action-card">
                            <div class="asad-action-icon" style="background: #3498db;">
                                <span class="dashicons dashicons-performance"></span>
                            </div>
                            <div class="asad-action-content">
                                <h3>Optimize Tables</h3>
                                <p>Optimize all database tables to improve performance and reduce storage space.</p>
                                <button type="button" class="button button-primary" id="optimize-database">Optimize Now</button>
                            </div>
                        </div>

                        <div class="asad-action-card">
                            <div class="asad-action-icon" style="background: #2ecc71;">
                                <span class="dashicons dashicons-admin-tools"></span>
                            </div>
                            <div class="asad-action-content">
                                <h3>Repair Tables</h3>
                                <p>Repair corrupted or damaged database tables.</p>
                                <button type="button" class="button button-primary" id="repair-database">Repair Tables</button>
                            </div>
                        </div>

                        <div class="asad-action-card">
                            <div class="asad-action-icon" style="background: #f39c12;">
                                <span class="dashicons dashicons-trash"></span>
                            </div>
                            <div class="asad-action-content">
                                <h3>Clean Database</h3>
                                <p>Remove revisions, auto-drafts, trash, spam, and orphaned data.</p>
                                <button type="button" class="button button-primary" id="clean-database">Clean Now</button>
                            </div>
                        </div>
                    </div>

                    <div id="optimization-results" style="display: none; margin-top: 30px;">
                        <h3>Optimization Results</h3>
                        <div id="optimization-message"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backup Tab -->
        <div class="asad-tab-content" id="backup-tab">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Database Backups</h2>
                    <button type="button" class="button button-primary" id="create-backup">Create Backup</button>
                </div>
                <div class="asad-card-body">
                    <div class="asad-backup-info" style="margin-bottom: 20px; padding: 15px; background: #f0f0f1; border-radius: 4px;">
                        <p><strong>Backup Location:</strong> <?php echo wp_upload_dir()['basedir'] . '/db-backups'; ?></p>
                        <p><strong>Note:</strong> Backups are stored on the server. For better security, download and store them externally.</p>
                    </div>

                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Filename</th>
                                <th>Size</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="backups-list">
                            <?php if (!empty($backups)): ?>
                                <?php foreach ($backups as $backup): ?>
                                    <tr data-filename="<?php echo esc_attr($backup['filename']); ?>">
                                        <td><code><?php echo esc_html($backup['filename']); ?></code></td>
                                        <td><?php echo esc_html($backup['size_formatted']); ?></td>
                                        <td><?php echo esc_html($backup['date']); ?></td>
                                        <td>
                                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?asad_download_backup=' . urlencode($backup['filename'])), 'asad_download_backup'); ?>" class="button button-small">Download</a>
                                            <button type="button" class="button button-small delete-backup" data-filename="<?php echo esc_attr($backup['filename']); ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No backups found. Create your first backup!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tables Tab -->
        <div class="asad-tab-content" id="tables-tab">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Database Tables</h2>
                </div>
                <div class="asad-card-body">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Table Name</th>
                                <th>Rows</th>
                                <th>Size</th>
                                <th>Engine</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($db_info['tables'] as $table): ?>
                                <tr>
                                    <td><code><?php echo esc_html($table['name']); ?></code></td>
                                    <td><?php echo number_format($table['rows']); ?></td>
                                    <td><?php echo esc_html($table['size_formatted']); ?></td>
                                    <td><?php echo esc_html($table['engine']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    const dbNonce = '<?php echo wp_create_nonce('asad_db_nonce'); ?>';

    // Tab switching
    $('.asad-tab-button').on('click', function() {
        const tab = $(this).data('tab');
        $('.asad-tab-button').removeClass('active');
        $('.asad-tab-content').removeClass('active');
        $(this).addClass('active');
        $('#' + tab + '-tab').addClass('active');
    });

    // Optimize database
    $('#optimize-database').on('click', function() {
        if (!confirm('Optimize all database tables?')) {
            return;
        }

        const button = $(this);
        button.prop('disabled', true).text('Optimizing...');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_optimize_database',
                nonce: dbNonce
            },
            success: function(response) {
                if (response.success) {
                    alert(`Optimized: ${response.data.optimized} tables\nFailed: ${response.data.failed} tables`);
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            },
            complete: function() {
                button.prop('disabled', false).text('Optimize Now');
            }
        });
    });

    // Repair database
    $('#repair-database').on('click', function() {
        if (!confirm('Repair database tables?')) {
            return;
        }

        const button = $(this);
        button.prop('disabled', true).text('Repairing...');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_repair_database',
                nonce: dbNonce
            },
            success: function(response) {
                if (response.success) {
                    alert(`Repaired: ${response.data.repaired} tables\nFailed: ${response.data.failed} tables`);
                } else {
                    alert('Error: ' + response.data);
                }
            },
            complete: function() {
                button.prop('disabled', false).text('Repair Tables');
            }
        });
    });

    // Clean database
    $('#clean-database').on('click', function() {
        if (!confirm('Clean database? This will remove revisions, auto-drafts, spam, and trash. This cannot be undone!')) {
            return;
        }

        const button = $(this);
        button.prop('disabled', true).text('Cleaning...');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_clean_database',
                nonce: dbNonce
            },
            success: function(response) {
                if (response.success) {
                    let message = 'Database cleaned successfully!\n\n';
                    for (const [key, value] of Object.entries(response.data.deleted)) {
                        message += `${key}: ${value} items\n`;
                    }
                    alert(message);
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            },
            complete: function() {
                button.prop('disabled', false).text('Clean Now');
            }
        });
    });

    // Create backup
    $('#create-backup').on('click', function() {
        const button = $(this);
        button.prop('disabled', true).text('Creating Backup...');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_create_backup',
                nonce: dbNonce
            },
            success: function(response) {
                if (response.success) {
                    alert(`Backup created successfully!\nFilename: ${response.data.filename}\nSize: ${response.data.size_formatted}`);
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            },
            complete: function() {
                button.prop('disabled', false).text('Create Backup');
            }
        });
    });

    // Delete backup
    $(document).on('click', '.delete-backup', function() {
        if (!confirm('Delete this backup?')) {
            return;
        }

        const filename = $(this).data('filename');
        const row = $(this).closest('tr');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_delete_backup',
                nonce: dbNonce,
                filename: filename
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
.asad-db-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.asad-db-stat-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 8px;
}

.asad-db-stat-item .dashicons {
    font-size: 40px;
    width: 40px;
    height: 40px;
    color: #3498db;
}

.asad-db-stat-item strong {
    display: block;
    font-size: 24px;
    margin-bottom: 5px;
}

.asad-db-stat-item p {
    margin: 0;
    color: #666;
    font-size: 14px;
}

.asad-optimization-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.asad-action-card {
    border: 2px solid #eee;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
}

.asad-action-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.asad-action-icon .dashicons {
    font-size: 40px;
    width: 40px;
    height: 40px;
    color: #fff;
}

.asad-action-content h3 {
    margin: 0 0 10px;
}

.asad-action-content p {
    color: #666;
    margin-bottom: 20px;
}
</style>
