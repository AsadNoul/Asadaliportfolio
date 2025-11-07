<?php
/**
 * Analytics Dashboard Template
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get analytics data
$stats = asad_get_analytics_stats(30);
$sources = asad_get_traffic_sources(10);
?>

<div class="wrap asad-admin-page">
    <h1>Analytics Dashboard</h1>
    <p>Track visitor statistics, popular content, and traffic sources</p>

    <div class="asad-analytics-header">
        <div class="asad-analytics-period">
            <label for="analytics-period">Time Period:</label>
            <select id="analytics-period">
                <option value="7">Last 7 Days</option>
                <option value="30" selected>Last 30 Days</option>
                <option value="90">Last 90 Days</option>
                <option value="365">Last Year</option>
            </select>
            <button type="button" class="button" id="refresh-analytics">Refresh</button>
            <button type="button" class="button button-secondary" id="clear-analytics">Clear All Data</button>
        </div>
    </div>

    <div class="asad-stats-grid">
        <div class="asad-stat-card">
            <div class="asad-stat-icon" style="background: #3498db;">
                <span class="dashicons dashicons-visibility"></span>
            </div>
            <div class="asad-stat-content">
                <h3 id="total-views"><?php echo number_format($stats['total_views']); ?></h3>
                <p>Total Page Views</p>
            </div>
        </div>

        <div class="asad-stat-card">
            <div class="asad-stat-icon" style="background: #2ecc71;">
                <span class="dashicons dashicons-groups"></span>
            </div>
            <div class="asad-stat-content">
                <h3 id="unique-visitors"><?php echo number_format($stats['unique_visitors']); ?></h3>
                <p>Unique Visitors</p>
            </div>
        </div>

        <div class="asad-stat-card">
            <div class="asad-stat-icon" style="background: #f39c12;">
                <span class="dashicons dashicons-chart-line"></span>
            </div>
            <div class="asad-stat-content">
                <h3 id="avg-views"><?php echo number_format($stats['avg_views_per_day'], 1); ?></h3>
                <p>Avg Views Per Day</p>
            </div>
        </div>

        <div class="asad-stat-card">
            <div class="asad-stat-icon" style="background: #9b59b6;">
                <span class="dashicons dashicons-admin-site"></span>
            </div>
            <div class="asad-stat-content">
                <h3 id="bounce-rate">N/A</h3>
                <p>Bounce Rate</p>
            </div>
        </div>
    </div>

    <div class="asad-analytics-row">
        <div class="asad-analytics-col asad-col-8">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Page Views Over Time</h2>
                </div>
                <div class="asad-card-body">
                    <canvas id="views-chart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="asad-analytics-col asad-col-4">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Device Breakdown</h2>
                </div>
                <div class="asad-card-body">
                    <canvas id="device-chart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="asad-analytics-row">
        <div class="asad-analytics-col asad-col-6">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Popular Posts</h2>
                </div>
                <div class="asad-card-body">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Post Title</th>
                                <th>Views</th>
                            </tr>
                        </thead>
                        <tbody id="popular-posts">
                            <?php if (!empty($stats['popular_posts'])): ?>
                                <?php foreach ($stats['popular_posts'] as $post): ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo get_permalink($post['post_id']); ?>" target="_blank">
                                                <?php echo esc_html($post['post_title']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo number_format($post['views']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2">No data available yet</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="asad-analytics-col asad-col-6">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Traffic Sources</h2>
                </div>
                <div class="asad-card-body">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Source</th>
                                <th>Type</th>
                                <th>Visits</th>
                            </tr>
                        </thead>
                        <tbody id="traffic-sources">
                            <?php if (!empty($sources)): ?>
                                <?php foreach ($sources as $source): ?>
                                    <tr>
                                        <td><?php echo esc_html($source['source_name']); ?></td>
                                        <td>
                                            <span class="asad-badge asad-badge-<?php echo sanitize_html_class(strtolower($source['source_type'])); ?>">
                                                <?php echo esc_html($source['source_type']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo number_format($source['visit_count']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3">No data available yet</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="asad-analytics-row">
        <div class="asad-analytics-col asad-col-12">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Browser Statistics</h2>
                </div>
                <div class="asad-card-body">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Browser</th>
                                <th>Visits</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($stats['browsers'])): ?>
                                <?php
                                $total_browsers = array_sum(array_column($stats['browsers'], 'count'));
                                foreach ($stats['browsers'] as $browser):
                                    $percentage = ($browser['count'] / $total_browsers) * 100;
                                ?>
                                    <tr>
                                        <td><?php echo esc_html($browser['browser']); ?></td>
                                        <td><?php echo number_format($browser['count']); ?></td>
                                        <td>
                                            <div class="asad-progress-bar">
                                                <div class="asad-progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                                                <span class="asad-progress-text"><?php echo number_format($percentage, 1); ?>%</span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3">No data available yet</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
jQuery(document).ready(function($) {
    const analyticsData = <?php echo json_encode($stats); ?>;

    // Views chart
    const viewsCtx = document.getElementById('views-chart');
    if (viewsCtx) {
        const viewsData = analyticsData.daily_views || [];
        new Chart(viewsCtx, {
            type: 'line',
            data: {
                labels: viewsData.map(d => d.date),
                datasets: [{
                    label: 'Page Views',
                    data: viewsData.map(d => d.views),
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // Device chart
    const deviceCtx = document.getElementById('device-chart');
    if (deviceCtx) {
        const deviceData = analyticsData.devices || [];
        new Chart(deviceCtx, {
            type: 'doughnut',
            data: {
                labels: deviceData.map(d => d.device_type),
                datasets: [{
                    data: deviceData.map(d => d.count),
                    backgroundColor: ['#3498db', '#2ecc71', '#f39c12', '#e74c3c']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Refresh analytics
    $('#refresh-analytics').on('click', function() {
        const days = $('#analytics-period').val();
        location.href = '?page=asad-analytics&days=' + days;
    });

    // Clear analytics
    $('#clear-analytics').on('click', function() {
        if (!confirm('Are you sure you want to clear all analytics data? This cannot be undone.')) {
            return;
        }

        const button = $(this);
        button.prop('disabled', true).text('Clearing...');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_clear_analytics',
                nonce: '<?php echo wp_create_nonce('asad_analytics_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert('Analytics data cleared successfully');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                    button.prop('disabled', false).text('Clear All Data');
                }
            }
        });
    });
});
</script>

<style>
.asad-analytics-header {
    background: #fff;
    padding: 20px;
    margin: 20px 0;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.asad-analytics-period {
    display: flex;
    align-items: center;
    gap: 10px;
}

.asad-analytics-period label {
    font-weight: 600;
}

.asad-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.asad-stat-card {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 20px;
}

.asad-stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
}

.asad-stat-icon .dashicons {
    font-size: 30px;
    width: 30px;
    height: 30px;
}

.asad-stat-content h3 {
    margin: 0;
    font-size: 32px;
    font-weight: bold;
}

.asad-stat-content p {
    margin: 5px 0 0;
    color: #666;
}

.asad-analytics-row {
    display: flex;
    gap: 20px;
    margin: 20px 0;
}

.asad-analytics-col {
    flex: 1;
}

.asad-col-4 { flex: 0 0 33.333%; }
.asad-col-6 { flex: 0 0 50%; }
.asad-col-8 { flex: 0 0 66.666%; }
.asad-col-12 { flex: 0 0 100%; }

.asad-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.asad-card-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.asad-card-header h2 {
    margin: 0;
    font-size: 18px;
}

.asad-card-body {
    padding: 20px;
}

.asad-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.asad-badge-direct { background: #3498db; color: #fff; }
.asad-badge-search.engine { background: #2ecc71; color: #fff; }
.asad-badge-social.media { background: #e74c3c; color: #fff; }
.asad-badge-referral { background: #f39c12; color: #fff; }

.asad-progress-bar {
    position: relative;
    height: 24px;
    background: #f0f0f0;
    border-radius: 12px;
    overflow: hidden;
}

.asad-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #3498db, #2ecc71);
    transition: width 0.3s;
}

.asad-progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 12px;
    font-weight: 600;
}
</style>
