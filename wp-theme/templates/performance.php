<?php
/**
 * Performance Optimizer Template
 *
 * @package Asad_Portfolio_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

$settings = asad_get_performance_settings();
$stats = asad_get_performance_stats();
?>

<div class="wrap asad-performance-optimizer">
    <h1><?php _e('Performance Optimizer', 'asad-portfolio'); ?></h1>
    <p class="description"><?php _e('Boost your website speed and improve user experience with these optimization tools.', 'asad-portfolio'); ?></p>

    <!-- Performance Stats Dashboard -->
    <div class="performance-dashboard">
        <div class="perf-stat-card">
            <div class="stat-icon" style="background: #3498db;">
                <i class="fas fa-database fa-2x"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['database_size']; ?> MB</h3>
                <p><?php _e('Database Size', 'asad-portfolio'); ?></p>
                <button class="button button-small" id="optimizeDatabaseBtn">
                    <i class="fas fa-sync"></i> <?php _e('Optimize', 'asad-portfolio'); ?>
                </button>
            </div>
        </div>

        <div class="perf-stat-card">
            <div class="stat-icon" style="background: #e74c3c;">
                <i class="fas fa-trash fa-2x"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['transients_count']; ?></h3>
                <p><?php _e('Transients', 'asad-portfolio'); ?></p>
                <button class="button button-small" id="clearCachesBtn">
                    <i class="fas fa-broom"></i> <?php _e('Clear', 'asad-portfolio'); ?>
                </button>
            </div>
        </div>

        <div class="perf-stat-card">
            <div class="stat-icon" style="background: #f39c12;">
                <i class="fas fa-file-alt fa-2x"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['auto_drafts']; ?></h3>
                <p><?php _e('Auto-Drafts', 'asad-portfolio'); ?></p>
            </div>
        </div>

        <div class="perf-stat-card">
            <div class="stat-icon" style="background: #9b59b6;">
                <i class="fas fa-comments fa-2x"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['spam_comments']; ?></h3>
                <p><?php _e('Spam Comments', 'asad-portfolio'); ?></p>
            </div>
        </div>
    </div>

    <div class="performance-sections">
        <!-- Performance Settings -->
        <div class="perf-section">
            <h2><i class="fas fa-sliders-h"></i> <?php _e('Performance Settings', 'asad-portfolio'); ?></h2>

            <form id="performanceSettingsForm">
                <table class="form-table">
                    <tr>
                        <th><?php _e('Image Optimization', 'asad-portfolio'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="lazy_loading" <?php checked($settings['lazy_loading'], true); ?>>
                                <?php _e('Enable Lazy Loading for Images', 'asad-portfolio'); ?>
                            </label>
                            <p class="description"><?php _e('Images load only when they appear in the viewport.', 'asad-portfolio'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th><?php _e('Script Optimization', 'asad-portfolio'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="defer_js" <?php checked($settings['defer_js'], true); ?>>
                                <?php _e('Defer JavaScript Loading', 'asad-portfolio'); ?>
                            </label>
                            <p class="description"><?php _e('Load JavaScript after page content is rendered.', 'asad-portfolio'); ?></p>

                            <label>
                                <input type="checkbox" name="minify_js" <?php checked($settings['minify_js'], true); ?>>
                                <?php _e('Minify JavaScript', 'asad-portfolio'); ?>
                            </label>
                            <p class="description"><?php _e('Remove unnecessary characters from JS files.', 'asad-portfolio'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th><?php _e('CSS Optimization', 'asad-portfolio'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="minify_css" <?php checked($settings['minify_css'], true); ?>>
                                <?php _e('Minify CSS', 'asad-portfolio'); ?>
                            </label>
                            <p class="description"><?php _e('Compress CSS files to reduce file size.', 'asad-portfolio'); ?></p>

                            <label>
                                <input type="checkbox" name="remove_query_strings" <?php checked($settings['remove_query_strings'], true); ?>>
                                <?php _e('Remove Query Strings from Static Resources', 'asad-portfolio'); ?>
                            </label>
                            <p class="description"><?php _e('Improve caching by removing version query strings.', 'asad-portfolio'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th><?php _e('Compression', 'asad-portfolio'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="gzip_compression" <?php checked($settings['gzip_compression'], true); ?>>
                                <?php _e('Enable GZIP Compression', 'asad-portfolio'); ?>
                            </label>
                            <p class="description"><?php _e('Compress HTML, CSS, and JS for faster delivery.', 'asad-portfolio'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th><?php _e('Caching', 'asad-portfolio'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="browser_caching" <?php checked($settings['browser_caching'], true); ?>>
                                <?php _e('Enable Browser Caching', 'asad-portfolio'); ?>
                            </label>
                            <p class="description"><?php _e('Store static files in browser cache.', 'asad-portfolio'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th><?php _e('WordPress Optimization', 'asad-portfolio'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="disable_embeds" <?php checked($settings['disable_embeds'], true); ?>>
                                <?php _e('Disable Embeds', 'asad-portfolio'); ?>
                            </label>
                            <p class="description"><?php _e('Remove oEmbed scripts if you don\'t use WordPress embeds.', 'asad-portfolio'); ?></p>

                            <label>
                                <input type="checkbox" name="disable_emojis" <?php checked($settings['disable_emojis'], true); ?>>
                                <?php _e('Disable Emoji Scripts', 'asad-portfolio'); ?>
                            </label>
                            <p class="description"><?php _e('Remove emoji detection script if you don\'t use emojis.', 'asad-portfolio'); ?></p>

                            <label>
                                <input type="checkbox" name="disable_heartbeat" <?php checked(isset($settings['disable_heartbeat']) ? $settings['disable_heartbeat'] : false, true); ?>>
                                <?php _e('Disable Heartbeat API', 'asad-portfolio'); ?>
                            </label>
                            <p class="description"><?php _e('Reduce server load by disabling auto-save/refresh features.', 'asad-portfolio'); ?></p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary button-large">
                        <i class="fas fa-save"></i> <?php _e('Save Performance Settings', 'asad-portfolio'); ?>
                    </button>
                </p>
            </form>
        </div>

        <!-- Database Optimization -->
        <div class="perf-section">
            <h2><i class="fas fa-database"></i> <?php _e('Database Optimization', 'asad-portfolio'); ?></h2>
            <p><?php _e('Clean up your database to improve performance and reduce size.', 'asad-portfolio'); ?></p>

            <div class="optimization-options">
                <div class="optimization-card">
                    <h4><?php _e('What will be cleaned:', 'asad-portfolio'); ?></h4>
                    <ul>
                        <li><i class="fas fa-check"></i> <?php _e('Transients (temporary cached data)', 'asad-portfolio'); ?></li>
                        <li><i class="fas fa-check"></i> <?php _e('Post meta locks', 'asad-portfolio'); ?></li>
                        <li><i class="fas fa-check"></i> <?php _e('Auto-drafts', 'asad-portfolio'); ?></li>
                        <li><i class="fas fa-check"></i> <?php _e('Trashed posts', 'asad-portfolio'); ?></li>
                        <li><i class="fas fa-check"></i> <?php _e('Spam comments', 'asad-portfolio'); ?></li>
                        <li><i class="fas fa-check"></i> <?php _e('Optimize database tables', 'asad-portfolio'); ?></li>
                    </ul>

                    <button class="button button-primary button-large" id="optimizeDatabaseBtnMain">
                        <i class="fas fa-magic"></i> <?php _e('Optimize Database Now', 'asad-portfolio'); ?>
                    </button>

                    <p class="description" style="margin-top: 15px;">
                        <strong><?php _e('Note:', 'asad-portfolio'); ?></strong>
                        <?php _e('This process is safe but may take a few moments. A backup is recommended before major optimizations.', 'asad-portfolio'); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Cache Management -->
        <div class="perf-section">
            <h2><i class="fas fa-broom"></i> <?php _e('Cache Management', 'asad-portfolio'); ?></h2>
            <p><?php _e('Clear all cached data to see the latest changes on your site.', 'asad-portfolio'); ?></p>

            <div class="cache-actions">
                <button class="button button-large" id="clearCachesBtnMain">
                    <i class="fas fa-trash-alt"></i> <?php _e('Clear All Caches', 'asad-portfolio'); ?>
                </button>
                <p class="description"><?php _e('This will clear WordPress transients and object cache.', 'asad-portfolio'); ?></p>
            </div>
        </div>

        <!-- Performance Tips -->
        <div class="perf-section">
            <h2><i class="fas fa-lightbulb"></i> <?php _e('Performance Tips', 'asad-portfolio'); ?></h2>

            <div class="performance-tips">
                <div class="tip-card">
                    <div class="tip-icon"><i class="fas fa-images"></i></div>
                    <h4><?php _e('Optimize Images', 'asad-portfolio'); ?></h4>
                    <p><?php _e('Use WebP format, compress images, and enable lazy loading for better performance.', 'asad-portfolio'); ?></p>
                </div>

                <div class="tip-card">
                    <div class="tip-icon"><i class="fas fa-server"></i></div>
                    <h4><?php _e('Use a CDN', 'asad-portfolio'); ?></h4>
                    <p><?php _e('Content Delivery Networks serve static files from servers closer to your visitors.', 'asad-portfolio'); ?></p>
                </div>

                <div class="tip-card">
                    <div class="tip-icon"><i class="fas fa-code"></i></div>
                    <h4><?php _e('Minify Resources', 'asad-portfolio'); ?></h4>
                    <p><?php _e('Enable CSS and JavaScript minification to reduce file sizes.', 'asad-portfolio'); ?></p>
                </div>

                <div class="tip-card">
                    <div class="tip-icon"><i class="fas fa-plug"></i></div>
                    <h4><?php _e('Limit Plugins', 'asad-portfolio'); ?></h4>
                    <p><?php _e('Too many plugins can slow down your site. Deactivate unused plugins.', 'asad-portfolio'); ?></p>
                </div>

                <div class="tip-card">
                    <div class="tip-icon"><i class="fas fa-database"></i></div>
                    <h4><?php _e('Regular Maintenance', 'asad-portfolio'); ?></h4>
                    <p><?php _e('Optimize your database regularly to keep it lean and fast.', 'asad-portfolio'); ?></p>
                </div>

                <div class="tip-card">
                    <div class="tip-icon"><i class="fas fa-mobile-alt"></i></div>
                    <h4><?php _e('Mobile First', 'asad-portfolio'); ?></h4>
                    <p><?php _e('Ensure your site loads quickly on mobile devices for better rankings.', 'asad-portfolio'); ?></p>
                </div>
            </div>
        </div>

        <!-- Test Performance -->
        <div class="perf-section">
            <h2><i class="fas fa-tachometer-alt"></i> <?php _e('Test Your Performance', 'asad-portfolio'); ?></h2>
            <p><?php _e('Use these tools to test your website speed and get optimization recommendations.', 'asad-portfolio'); ?></p>

            <div class="test-tools">
                <a href="https://pagespeed.web.dev/?url=<?php echo urlencode(home_url('/')); ?>" target="_blank" class="tool-button">
                    <i class="fab fa-google"></i>
                    <strong>Google PageSpeed Insights</strong>
                    <span><?php _e('Analyze performance and get suggestions', 'asad-portfolio'); ?></span>
                </a>

                <a href="https://tools.pingdom.com/" target="_blank" class="tool-button">
                    <i class="fas fa-chart-line"></i>
                    <strong>Pingdom Speed Test</strong>
                    <span><?php _e('Test load time from multiple locations', 'asad-portfolio'); ?></span>
                </a>

                <a href="https://www.webpagetest.org/" target="_blank" class="tool-button">
                    <i class="fas fa-stopwatch"></i>
                    <strong>WebPageTest</strong>
                    <span><?php _e('Advanced performance analysis', 'asad-portfolio'); ?></span>
                </a>

                <a href="https://gtmetrix.com/" target="_blank" class="tool-button">
                    <i class="fas fa-chart-bar"></i>
                    <strong>GTmetrix</strong>
                    <span><?php _e('Performance scores and recommendations', 'asad-portfolio'); ?></span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.asad-performance-optimizer {
    margin-top: 20px;
}

.performance-dashboard {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 30px 0;
}

.perf-stat-card {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    flex-shrink: 0;
}

.stat-content {
    flex: 1;
}

.stat-content h3 {
    font-size: 1.8rem;
    margin: 0 0 5px;
}

.stat-content p {
    margin: 0 0 10px;
    color: #666;
}

.perf-section {
    background: #fff;
    padding: 25px;
    margin-bottom: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.perf-section h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 2px solid #2271b1;
}

.perf-section h2 i {
    margin-right: 10px;
    color: #2271b1;
}

.optimization-card {
    background: #f9f9f9;
    padding: 25px;
    border-radius: 5px;
    border-left: 4px solid #2ecc71;
}

.optimization-card ul {
    list-style: none;
    padding: 0;
    margin: 20px 0;
}

.optimization-card li {
    padding: 8px 0;
    font-size: 14px;
}

.optimization-card li i {
    color: #2ecc71;
    margin-right: 10px;
}

.cache-actions {
    padding: 20px;
    background: #f9f9f9;
    border-radius: 5px;
}

.performance-tips {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.tip-card {
    padding: 20px;
    background: #f9f9f9;
    border-radius: 5px;
    border-left: 4px solid #2271b1;
}

.tip-icon {
    width: 50px;
    height: 50px;
    background: #2271b1;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5em;
    margin-bottom: 15px;
}

.tip-card h4 {
    margin: 10px 0;
}

.tip-card p {
    margin: 0;
    color: #666;
    font-size: 14px;
}

.test-tools {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.tool-button {
    display: flex;
    flex-direction: column;
    padding: 20px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 5px;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s;
}

.tool-button:hover {
    background: #2271b1;
    color: #fff;
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.tool-button i {
    font-size: 2em;
    margin-bottom: 10px;
}

.tool-button strong {
    display: block;
    margin-bottom: 5px;
    font-size: 16px;
}

.tool-button span {
    font-size: 13px;
    opacity: 0.8;
}

@media (max-width: 768px) {
    .performance-dashboard {
        grid-template-columns: 1fr;
    }

    .performance-tips,
    .test-tools {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Save Performance Settings
    $('#performanceSettingsForm').on('submit', function(e) {
        e.preventDefault();

        const settings = {
            lazy_loading: $('input[name="lazy_loading"]').is(':checked'),
            defer_js: $('input[name="defer_js"]').is(':checked'),
            minify_js: $('input[name="minify_js"]').is(':checked'),
            minify_css: $('input[name="minify_css"]').is(':checked'),
            remove_query_strings: $('input[name="remove_query_strings"]').is(':checked'),
            gzip_compression: $('input[name="gzip_compression"]').is(':checked'),
            browser_caching: $('input[name="browser_caching"]').is(':checked'),
            disable_embeds: $('input[name="disable_embeds"]').is(':checked'),
            disable_emojis: $('input[name="disable_emojis"]').is(':checked'),
            disable_heartbeat: $('input[name="disable_heartbeat"]').is(':checked')
        };

        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> <?php _e('Saving...', 'asad-portfolio'); ?>');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_save_performance_settings',
                nonce: '<?php echo wp_create_nonce('asad-admin-nonce'); ?>',
                settings: JSON.stringify(settings)
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                } else {
                    alert(response.data.message);
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> <?php _e('Save Performance Settings', 'asad-portfolio'); ?>');
            }
        });
    });

    // Optimize Database
    $('#optimizeDatabaseBtn, #optimizeDatabaseBtnMain').on('click', function() {
        if (!confirm('<?php _e('This will optimize your database. Continue?', 'asad-portfolio'); ?>')) {
            return;
        }

        const button = $(this);
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> <?php _e('Optimizing...', 'asad-portfolio'); ?>');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_optimize_database',
                nonce: '<?php echo wp_create_nonce('asad-admin-nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            },
            complete: function() {
                button.prop('disabled', false).html('<i class="fas fa-sync"></i> <?php _e('Optimize', 'asad-portfolio'); ?>');
            }
        });
    });

    // Clear Caches
    $('#clearCachesBtn, #clearCachesBtnMain').on('click', function() {
        if (!confirm('<?php _e('This will clear all caches. Continue?', 'asad-portfolio'); ?>')) {
            return;
        }

        const button = $(this);
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> <?php _e('Clearing...', 'asad-portfolio'); ?>');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_clear_caches',
                nonce: '<?php echo wp_create_nonce('asad-admin-nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            },
            complete: function() {
                button.prop('disabled', false).html('<i class="fas fa-broom"></i> <?php _e('Clear', 'asad-portfolio'); ?>');
            }
        });
    });
});
</script>
