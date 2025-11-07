<?php
/**
 * Security Scanner Template
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get current security audit
$audit = asad_security_audit();
$blocked_ips = asad_get_blocked_ips();
$security_logs = asad_get_security_logs(50);
?>

<div class="wrap asad-admin-page">
    <h1>Security Scanner</h1>
    <p>Protect your WordPress site with malware scanning, firewall, and security audits</p>

    <!-- Security Score -->
    <div class="asad-security-score">
        <div class="asad-score-circle <?php echo $audit['score'] >= 80 ? 'good' : ($audit['score'] >= 60 ? 'warning' : 'danger'); ?>">
            <svg width="150" height="150">
                <circle cx="75" cy="75" r="65" fill="none" stroke="#eee" stroke-width="10"/>
                <circle cx="75" cy="75" r="65" fill="none" stroke="currentColor" stroke-width="10"
                    stroke-dasharray="<?php echo ($audit['score'] / 100) * 408; ?> 408"
                    transform="rotate(-90 75 75)"/>
            </svg>
            <div class="asad-score-text">
                <h2><?php echo $audit['score']; ?></h2>
                <p>Security Score</p>
            </div>
        </div>
        <div class="asad-score-status">
            <h3>Status: <?php echo $audit['score'] >= 80 ? 'Good' : ($audit['score'] >= 60 ? 'Needs Improvement' : 'Critical'); ?></h3>
            <p><?php echo count($audit['issues']); ?> issues found, <?php echo count($audit['passed']); ?> checks passed</p>
            <button type="button" class="button button-primary" id="run-security-audit">Run Security Audit</button>
        </div>
    </div>

    <!-- Security Tabs -->
    <div class="asad-tabs">
        <div class="asad-tab-buttons">
            <button class="asad-tab-button active" data-tab="audit">Security Audit</button>
            <button class="asad-tab-button" data-tab="malware">Malware Scanner</button>
            <button class="asad-tab-button" data-tab="firewall">Firewall</button>
            <button class="asad-tab-button" data-tab="logs">Security Logs</button>
        </div>

        <!-- Security Audit Tab -->
        <div class="asad-tab-content active" id="audit-tab">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Security Issues</h2>
                </div>
                <div class="asad-card-body">
                    <?php if (!empty($audit['issues'])): ?>
                        <div class="asad-security-issues">
                            <?php foreach ($audit['issues'] as $issue): ?>
                                <div class="asad-issue asad-issue-<?php echo esc_attr($issue['severity']); ?>">
                                    <div class="asad-issue-icon">
                                        <span class="dashicons dashicons-<?php echo $issue['severity'] === 'high' ? 'warning' : 'info'; ?>"></span>
                                    </div>
                                    <div class="asad-issue-content">
                                        <h4><?php echo esc_html($issue['title']); ?></h4>
                                        <p><?php echo esc_html($issue['description']); ?></p>
                                        <p class="asad-issue-fix"><strong>Fix:</strong> <?php echo esc_html($issue['fix']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="asad-no-issues">
                            <span class="dashicons dashicons-yes-alt"></span>
                            <h3>No Security Issues Found!</h3>
                            <p>Your WordPress installation appears to be secure.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="asad-card" style="margin-top: 20px;">
                <div class="asad-card-header">
                    <h2>Passed Security Checks</h2>
                </div>
                <div class="asad-card-body">
                    <ul class="asad-passed-checks">
                        <?php foreach ($audit['passed'] as $check): ?>
                            <li>
                                <span class="dashicons dashicons-yes"></span>
                                <?php echo esc_html($check); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Malware Scanner Tab -->
        <div class="asad-tab-content" id="malware-tab">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Malware Scanner</h2>
                    <button type="button" class="button button-primary" id="run-malware-scan">Run Full Scan</button>
                </div>
                <div class="asad-card-body">
                    <div id="scan-results" style="display: none;">
                        <div class="asad-scan-summary">
                            <div class="asad-stat-item">
                                <strong>Files Scanned:</strong>
                                <span id="scanned-files">0</span>
                            </div>
                            <div class="asad-stat-item">
                                <strong>Threats Found:</strong>
                                <span id="threats-found">0</span>
                            </div>
                            <div class="asad-stat-item">
                                <strong>Warnings:</strong>
                                <span id="warnings-found">0</span>
                            </div>
                            <div class="asad-stat-item">
                                <strong>Scan Time:</strong>
                                <span id="scan-time">0s</span>
                            </div>
                        </div>

                        <div id="threats-list"></div>
                        <div id="warnings-list"></div>
                    </div>

                    <div id="scan-loading" style="display: none; text-align: center; padding: 40px;">
                        <div class="asad-spinner"></div>
                        <p>Scanning files for malware...</p>
                    </div>

                    <div id="scan-initial">
                        <p>Click "Run Full Scan" to check your WordPress installation for malware and suspicious code.</p>
                        <ul class="asad-scan-features">
                            <li><span class="dashicons dashicons-yes"></span> Scans all PHP files in themes and plugins</li>
                            <li><span class="dashicons dashicons-yes"></span> Detects suspicious functions and patterns</li>
                            <li><span class="dashicons dashicons-yes"></span> Checks file permissions</li>
                            <li><span class="dashicons dashicons-yes"></span> Fast and efficient scanning</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Firewall Tab -->
        <div class="asad-tab-content" id="firewall-tab">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>IP Firewall</h2>
                </div>
                <div class="asad-card-body">
                    <div class="asad-firewall-add">
                        <h3>Block IP Address</h3>
                        <form id="block-ip-form" style="display: flex; gap: 10px; margin-bottom: 20px;">
                            <input type="text" id="block-ip" placeholder="Enter IP address" required style="flex: 1;">
                            <input type="text" id="block-reason" placeholder="Reason (optional)" style="flex: 1;">
                            <button type="submit" class="button button-primary">Block IP</button>
                        </form>
                    </div>

                    <h3>Blocked IP Addresses</h3>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>Reason</th>
                                <th>Blocked Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="blocked-ips-list">
                            <?php if (!empty($blocked_ips)): ?>
                                <?php foreach ($blocked_ips as $ip): ?>
                                    <tr data-ip="<?php echo esc_attr($ip['ip_address']); ?>">
                                        <td><code><?php echo esc_html($ip['ip_address']); ?></code></td>
                                        <td><?php echo esc_html($ip['reason'] ?: '-'); ?></td>
                                        <td><?php echo esc_html($ip['blocked_date']); ?></td>
                                        <td>
                                            <button type="button" class="button button-small unblock-ip" data-ip="<?php echo esc_attr($ip['ip_address']); ?>">
                                                Unblock
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No blocked IPs</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Security Logs Tab -->
        <div class="asad-tab-content" id="logs-tab">
            <div class="asad-card">
                <div class="asad-card-header">
                    <h2>Security Logs</h2>
                </div>
                <div class="asad-card-body">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Severity</th>
                                <th>Message</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($security_logs)): ?>
                                <?php foreach ($security_logs as $log): ?>
                                    <tr>
                                        <td><?php echo esc_html($log['log_date']); ?></td>
                                        <td><?php echo esc_html($log['log_type']); ?></td>
                                        <td>
                                            <span class="asad-badge asad-badge-<?php echo esc_attr($log['severity']); ?>">
                                                <?php echo esc_html($log['severity']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo esc_html($log['message']); ?></td>
                                        <td><code><?php echo esc_html($log['ip_address']); ?></code></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">No security logs</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    const securityNonce = '<?php echo wp_create_nonce('asad_security_nonce'); ?>';

    // Tab switching
    $('.asad-tab-button').on('click', function() {
        const tab = $(this).data('tab');
        $('.asad-tab-button').removeClass('active');
        $('.asad-tab-content').removeClass('active');
        $(this).addClass('active');
        $('#' + tab + '-tab').addClass('active');
    });

    // Run security audit
    $('#run-security-audit').on('click', function() {
        const button = $(this);
        button.prop('disabled', true).text('Running Audit...');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_security_audit',
                nonce: securityNonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Security audit completed. Page will reload.');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            },
            complete: function() {
                button.prop('disabled', false).text('Run Security Audit');
            }
        });
    });

    // Run malware scan
    $('#run-malware-scan').on('click', function() {
        const button = $(this);
        button.prop('disabled', true).text('Scanning...');
        $('#scan-initial').hide();
        $('#scan-results').hide();
        $('#scan-loading').show();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_scan_malware',
                nonce: securityNonce
            },
            success: function(response) {
                if (response.success) {
                    displayScanResults(response.data);
                } else {
                    alert('Error: ' + response.data);
                }
            },
            complete: function() {
                button.prop('disabled', false).text('Run Full Scan');
                $('#scan-loading').hide();
            }
        });
    });

    function displayScanResults(data) {
        $('#scanned-files').text(data.scanned_files);
        $('#threats-found').text(data.threats.length);
        $('#warnings-found').text(data.warnings.length);
        $('#scan-time').text(data.scan_time + 's');

        if (data.threats.length > 0) {
            let threatsHtml = '<h3 style="color: #e74c3c;">Threats Found:</h3><ul class="asad-threats-list">';
            data.threats.forEach(threat => {
                threatsHtml += `<li><strong>${threat.file}</strong>: ${threat.threat}</li>`;
            });
            threatsHtml += '</ul>';
            $('#threats-list').html(threatsHtml);
        }

        if (data.warnings.length > 0) {
            let warningsHtml = '<h3 style="color: #f39c12;">Warnings:</h3><ul class="asad-warnings-list">';
            data.warnings.forEach(warning => {
                warningsHtml += `<li><strong>${warning.file}</strong>: ${warning.warning}</li>`;
            });
            warningsHtml += '</ul>';
            $('#warnings-list').html(warningsHtml);
        }

        $('#scan-results').show();
    }

    // Block IP
    $('#block-ip-form').on('submit', function(e) {
        e.preventDefault();

        const ip = $('#block-ip').val();
        const reason = $('#block-reason').val();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_block_ip',
                nonce: securityNonce,
                ip: ip,
                reason: reason
            },
            success: function(response) {
                if (response.success) {
                    alert('IP blocked successfully');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });

    // Unblock IP
    $('.unblock-ip').on('click', function() {
        if (!confirm('Unblock this IP address?')) {
            return;
        }

        const ip = $(this).data('ip');
        const row = $(this).closest('tr');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_unblock_ip',
                nonce: securityNonce,
                ip: ip
            },
            success: function(response) {
                if (response.success) {
                    row.remove();
                    alert('IP unblocked successfully');
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
});
</script>

<style>
.asad-security-score {
    background: #fff;
    padding: 30px;
    margin: 20px 0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 40px;
}

.asad-score-circle {
    position: relative;
}

.asad-score-circle.good { color: #2ecc71; }
.asad-score-circle.warning { color: #f39c12; }
.asad-score-circle.danger { color: #e74c3c; }

.asad-score-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.asad-score-text h2 {
    margin: 0;
    font-size: 40px;
    font-weight: bold;
}

.asad-score-text p {
    margin: 5px 0 0;
    font-size: 14px;
    color: #666;
}

.asad-security-issues {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.asad-issue {
    display: flex;
    gap: 15px;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid;
}

.asad-issue-high {
    background: #fee;
    border-color: #e74c3c;
}

.asad-issue-medium {
    background: #fef5e6;
    border-color: #f39c12;
}

.asad-issue-low {
    background: #e6f7ff;
    border-color: #3498db;
}

.asad-issue-icon .dashicons {
    font-size: 30px;
    width: 30px;
    height: 30px;
}

.asad-issue-content h4 {
    margin: 0 0 10px;
}

.asad-issue-fix {
    color: #666;
    font-size: 14px;
}

.asad-no-issues {
    text-align: center;
    padding: 60px;
}

.asad-no-issues .dashicons {
    font-size: 80px;
    width: 80px;
    height: 80px;
    color: #2ecc71;
}

.asad-passed-checks {
    list-style: none;
    padding: 0;
}

.asad-passed-checks li {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.asad-passed-checks li .dashicons {
    color: #2ecc71;
}

.asad-spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.asad-scan-features {
    list-style: none;
    padding: 0;
}

.asad-scan-features li {
    padding: 10px 0;
}

.asad-scan-features .dashicons {
    color: #2ecc71;
}

.asad-badge-warning { background: #f39c12; color: #fff; }
.asad-badge-info { background: #3498db; color: #fff; }
.asad-badge-high { background: #e74c3c; color: #fff; }
</style>
