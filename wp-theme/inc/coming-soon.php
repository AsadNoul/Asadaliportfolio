<?php
/**
 * Coming Soon & Maintenance Mode
 * Professional maintenance pages with countdown
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get coming soon settings
 */
function asad_get_coming_soon_settings() {
    return get_option('asad_coming_soon_settings', array(
        'enabled' => false,
        'mode' => 'coming_soon', // coming_soon or maintenance
        'template' => 'default',
        'title' => 'Coming Soon',
        'message' => 'We are working on something awesome. Stay tuned!',
        'countdown_enabled' => false,
        'countdown_date' => '',
        'logo' => '',
        'background_image' => '',
        'background_color' => '#2c3e50',
        'text_color' => '#ffffff',
        'show_subscribe_form' => true,
        'social_links' => array(),
        'allowed_ips' => array(),
        'allowed_roles' => array('administrator'),
        'progress_enabled' => false,
        'progress_percentage' => 0
    ));
}

/**
 * Update coming soon settings
 */
function asad_update_coming_soon_settings($settings) {
    return update_option('asad_coming_soon_settings', $settings);
}

/**
 * Check if coming soon mode should be shown
 */
function asad_should_show_coming_soon() {
    $settings = asad_get_coming_soon_settings();

    if (!$settings['enabled']) {
        return false;
    }

    // Don't show to logged-in admins
    if (current_user_can('administrator')) {
        return false;
    }

    // Check allowed roles
    $user = wp_get_current_user();
    if (!empty(array_intersect($settings['allowed_roles'], $user->roles))) {
        return false;
    }

    // Check allowed IPs
    $visitor_ip = asad_get_visitor_ip();
    if (in_array($visitor_ip, $settings['allowed_ips'])) {
        return false;
    }

    // Don't show on wp-admin or login pages
    if (is_admin() || $GLOBALS['pagenow'] === 'wp-login.php') {
        return false;
    }

    return true;
}

/**
 * Display coming soon page
 */
function asad_display_coming_soon_page() {
    if (!asad_should_show_coming_soon()) {
        return;
    }

    $settings = asad_get_coming_soon_settings();

    // Set appropriate HTTP header
    if ($settings['mode'] === 'maintenance') {
        header('HTTP/1.1 503 Service Temporarily Unavailable');
        header('Status: 503 Service Temporarily Unavailable');
        header('Retry-After: 3600');
    }

    // Load template
    asad_load_coming_soon_template($settings);
    exit;
}
add_action('template_redirect', 'asad_display_coming_soon_page', 1);

/**
 * Load coming soon template
 */
function asad_load_coming_soon_template($settings) {
    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <title><?php echo esc_html($settings['title']); ?> - <?php bloginfo('name'); ?></title>
        <?php wp_head(); ?>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                background-color: <?php echo esc_attr($settings['background_color']); ?>;
                <?php if (!empty($settings['background_image'])): ?>
                background-image: url('<?php echo esc_url($settings['background_image']); ?>');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
                <?php endif; ?>
                color: <?php echo esc_attr($settings['text_color']); ?>;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                text-align: center;
                padding: 20px;
            }

            .coming-soon-container {
                max-width: 800px;
                width: 100%;
                background: rgba(0, 0, 0, 0.7);
                padding: 60px 40px;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                animation: fadeInUp 1s ease;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .cs-logo {
                margin-bottom: 40px;
            }

            .cs-logo img {
                max-width: 200px;
                height: auto;
            }

            .cs-title {
                font-size: 48px;
                font-weight: bold;
                margin-bottom: 20px;
                line-height: 1.2;
            }

            .cs-message {
                font-size: 20px;
                margin-bottom: 40px;
                opacity: 0.9;
            }

            .cs-countdown {
                display: flex;
                justify-content: center;
                gap: 30px;
                margin: 40px 0;
                flex-wrap: wrap;
            }

            .countdown-item {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .countdown-value {
                font-size: 48px;
                font-weight: bold;
                line-height: 1;
                min-width: 80px;
                background: rgba(255, 255, 255, 0.1);
                padding: 20px;
                border-radius: 10px;
            }

            .countdown-label {
                font-size: 14px;
                margin-top: 10px;
                opacity: 0.8;
                text-transform: uppercase;
            }

            .cs-progress {
                margin: 40px 0;
            }

            .progress-bar {
                height: 10px;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 10px;
                overflow: hidden;
                margin-bottom: 10px;
            }

            .progress-fill {
                height: 100%;
                background: linear-gradient(90deg, #3498db, #2ecc71);
                width: <?php echo intval($settings['progress_percentage']); ?>%;
                transition: width 0.5s ease;
            }

            .progress-text {
                font-size: 14px;
                opacity: 0.8;
            }

            .cs-subscribe-form {
                max-width: 500px;
                margin: 40px auto;
            }

            .cs-subscribe-form input[type="email"] {
                width: 100%;
                padding: 15px 20px;
                border: none;
                border-radius: 30px;
                font-size: 16px;
                margin-bottom: 15px;
            }

            .cs-subscribe-form button {
                width: 100%;
                padding: 15px 30px;
                background: linear-gradient(90deg, #3498db, #2ecc71);
                color: white;
                border: none;
                border-radius: 30px;
                font-size: 16px;
                font-weight: bold;
                cursor: pointer;
                transition: transform 0.2s;
            }

            .cs-subscribe-form button:hover {
                transform: scale(1.05);
            }

            .cs-social {
                display: flex;
                justify-content: center;
                gap: 20px;
                margin-top: 40px;
            }

            .cs-social a {
                color: <?php echo esc_attr($settings['text_color']); ?>;
                font-size: 24px;
                opacity: 0.8;
                transition: opacity 0.2s;
                text-decoration: none;
            }

            .cs-social a:hover {
                opacity: 1;
            }

            @media (max-width: 768px) {
                .coming-soon-container {
                    padding: 40px 20px;
                }

                .cs-title {
                    font-size: 32px;
                }

                .cs-message {
                    font-size: 16px;
                }

                .countdown-value {
                    font-size: 36px;
                    min-width: 60px;
                    padding: 15px;
                }
            }
        </style>
    </head>
    <body>
        <div class="coming-soon-container">
            <?php if (!empty($settings['logo'])): ?>
                <div class="cs-logo">
                    <img src="<?php echo esc_url($settings['logo']); ?>" alt="<?php bloginfo('name'); ?>">
                </div>
            <?php endif; ?>

            <h1 class="cs-title"><?php echo esc_html($settings['title']); ?></h1>
            <p class="cs-message"><?php echo esc_html($settings['message']); ?></p>

            <?php if ($settings['countdown_enabled'] && !empty($settings['countdown_date'])): ?>
                <div class="cs-countdown" id="countdown">
                    <div class="countdown-item">
                        <div class="countdown-value" id="days">0</div>
                        <div class="countdown-label">Days</div>
                    </div>
                    <div class="countdown-item">
                        <div class="countdown-value" id="hours">0</div>
                        <div class="countdown-label">Hours</div>
                    </div>
                    <div class="countdown-item">
                        <div class="countdown-value" id="minutes">0</div>
                        <div class="countdown-label">Minutes</div>
                    </div>
                    <div class="countdown-item">
                        <div class="countdown-value" id="seconds">0</div>
                        <div class="countdown-label">Seconds</div>
                    </div>
                </div>

                <script>
                    const countdownDate = new Date('<?php echo esc_js($settings['countdown_date']); ?>').getTime();

                    function updateCountdown() {
                        const now = new Date().getTime();
                        const distance = countdownDate - now;

                        if (distance < 0) {
                            document.getElementById('countdown').innerHTML = '<p>We are live!</p>';
                            return;
                        }

                        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        document.getElementById('days').innerText = days;
                        document.getElementById('hours').innerText = hours;
                        document.getElementById('minutes').innerText = minutes;
                        document.getElementById('seconds').innerText = seconds;
                    }

                    updateCountdown();
                    setInterval(updateCountdown, 1000);
                </script>
            <?php endif; ?>

            <?php if ($settings['progress_enabled']): ?>
                <div class="cs-progress">
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                    <div class="progress-text"><?php echo intval($settings['progress_percentage']); ?>% Complete</div>
                </div>
            <?php endif; ?>

            <?php if ($settings['show_subscribe_form']): ?>
                <form class="cs-subscribe-form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                    <input type="email" name="email" placeholder="Enter your email" required>
                    <button type="submit">Notify Me When We Launch</button>
                    <input type="hidden" name="action" value="asad_subscribe">
                </form>
            <?php endif; ?>

            <?php if (!empty($settings['social_links'])): ?>
                <div class="cs-social">
                    <?php foreach ($settings['social_links'] as $platform => $url): ?>
                        <?php if (!empty($url)): ?>
                            <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener">
                                <i class="fab fa-<?php echo esc_attr($platform); ?>"></i>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php wp_footer(); ?>
    </body>
    </html>
    <?php
}

/**
 * AJAX: Save coming soon settings
 */
function asad_ajax_save_coming_soon_settings() {
    check_ajax_referer('asad_coming_soon_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $settings = array(
        'enabled' => isset($_POST['enabled']),
        'mode' => sanitize_text_field($_POST['mode'] ?? 'coming_soon'),
        'template' => sanitize_text_field($_POST['template'] ?? 'default'),
        'title' => sanitize_text_field($_POST['title'] ?? 'Coming Soon'),
        'message' => sanitize_textarea_field($_POST['message'] ?? ''),
        'countdown_enabled' => isset($_POST['countdown_enabled']),
        'countdown_date' => sanitize_text_field($_POST['countdown_date'] ?? ''),
        'logo' => esc_url_raw($_POST['logo'] ?? ''),
        'background_image' => esc_url_raw($_POST['background_image'] ?? ''),
        'background_color' => sanitize_hex_color($_POST['background_color'] ?? '#2c3e50'),
        'text_color' => sanitize_hex_color($_POST['text_color'] ?? '#ffffff'),
        'show_subscribe_form' => isset($_POST['show_subscribe_form']),
        'social_links' => array_map('esc_url_raw', $_POST['social_links'] ?? array()),
        'allowed_ips' => array_map('sanitize_text_field', $_POST['allowed_ips'] ?? array()),
        'allowed_roles' => array_map('sanitize_text_field', $_POST['allowed_roles'] ?? array()),
        'progress_enabled' => isset($_POST['progress_enabled']),
        'progress_percentage' => intval($_POST['progress_percentage'] ?? 0)
    );

    asad_update_coming_soon_settings($settings);

    wp_send_json_success('Settings saved successfully');
}
add_action('wp_ajax_asad_save_coming_soon_settings', 'asad_ajax_save_coming_soon_settings');
