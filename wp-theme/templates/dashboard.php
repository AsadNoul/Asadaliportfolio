<?php
/**
 * Dashboard Template
 *
 * @package Asad_Portfolio_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap asad-dashboard">
    <h1><?php _e('Portfolio Manager Dashboard', 'asad-portfolio'); ?></h1>

    <div class="asad-dashboard-grid">
        <div class="dashboard-card">
            <div class="card-icon" style="background: #3498db;">
                <i class="fas fa-plug fa-2x"></i>
            </div>
            <div class="card-content">
                <h2><?php _e('Plugin Manager', 'asad-portfolio'); ?></h2>
                <p><?php _e('Install, activate, deactivate, and manage all your WordPress plugins from one place.', 'asad-portfolio'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=asad-plugin-manager'); ?>" class="button button-primary">
                    <?php _e('Manage Plugins', 'asad-portfolio'); ?>
                </a>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-icon" style="background: #2ecc71;">
                <i class="fas fa-paint-brush fa-2x"></i>
            </div>
            <div class="card-content">
                <h2><?php _e('Theme Manager', 'asad-portfolio'); ?></h2>
                <p><?php _e('Browse, install, activate, and customize WordPress themes with ease.', 'asad-portfolio'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=asad-theme-manager'); ?>" class="button button-primary">
                    <?php _e('Manage Themes', 'asad-portfolio'); ?>
                </a>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-icon" style="background: #e74c3c;">
                <i class="fas fa-code fa-2x"></i>
            </div>
            <div class="card-content">
                <h2><?php _e('Theme Editor', 'asad-portfolio'); ?></h2>
                <p><?php _e('Edit theme files with syntax highlighting and live preview.', 'asad-portfolio'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=asad-theme-editor'); ?>" class="button button-primary">
                    <?php _e('Edit Theme', 'asad-portfolio'); ?>
                </a>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-icon" style="background: #9b59b6;">
                <i class="fas fa-sliders-h fa-2x"></i>
            </div>
            <div class="card-content">
                <h2><?php _e('Theme Customizer', 'asad-portfolio'); ?></h2>
                <p><?php _e('Customize colors, fonts, dark mode, logo, and more with live preview.', 'asad-portfolio'); ?></p>
                <a href="<?php echo admin_url('customize.php'); ?>" class="button button-primary">
                    <?php _e('Customize', 'asad-portfolio'); ?>
                </a>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-icon" style="background: #f39c12;">
                <i class="fas fa-th-large fa-2x"></i>
            </div>
            <div class="card-content">
                <h2><?php _e('Header & Footer', 'asad-portfolio'); ?></h2>
                <p><?php _e('Build and customize your header and footer with widgets and custom code.', 'asad-portfolio'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=asad-header-footer'); ?>" class="button button-primary">
                    <?php _e('Customize', 'asad-portfolio'); ?>
                </a>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-icon" style="background: #1abc9c;">
                <i class="fas fa-briefcase fa-2x"></i>
            </div>
            <div class="card-content">
                <h2><?php _e('Portfolio Items', 'asad-portfolio'); ?></h2>
                <p><?php _e('Manage your portfolio projects and showcase your work.', 'asad-portfolio'); ?></p>
                <a href="<?php echo admin_url('edit.php?post_type=portfolio'); ?>" class="button button-primary">
                    <?php _e('Manage Portfolio', 'asad-portfolio'); ?>
                </a>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-icon" style="background: #16a085;">
                <i class="fas fa-envelope fa-2x"></i>
            </div>
            <div class="card-content">
                <h2><?php _e('Form Builder', 'asad-portfolio'); ?></h2>
                <p><?php _e('Create beautiful contact forms with drag-and-drop builder and manage submissions.', 'asad-portfolio'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=asad-form-builder'); ?>" class="button button-primary">
                    <?php _e('Build Forms', 'asad-portfolio'); ?>
                </a>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-icon" style="background: #d35400;">
                <i class="fas fa-search fa-2x"></i>
            </div>
            <div class="card-content">
                <h2><?php _e('SEO Manager', 'asad-portfolio'); ?></h2>
                <p><?php _e('Optimize your website for search engines with meta tags, sitemaps, and schema markup.', 'asad-portfolio'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=asad-seo-manager'); ?>" class="button button-primary">
                    <?php _e('Optimize SEO', 'asad-portfolio'); ?>
                </a>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-icon" style="background: #c0392b;">
                <i class="fas fa-tachometer-alt fa-2x"></i>
            </div>
            <div class="card-content">
                <h2><?php _e('Performance Optimizer', 'asad-portfolio'); ?></h2>
                <p><?php _e('Boost your website speed with caching, minification, and database optimization.', 'asad-portfolio'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=asad-performance'); ?>" class="button button-primary">
                    <?php _e('Optimize Performance', 'asad-portfolio'); ?>
                </a>
            </div>
        </div>
    </div>

    <div class="asad-info-section">
        <h2><?php _e('System Information', 'asad-portfolio'); ?></h2>
        <table class="widefat">
            <tr>
                <td><strong><?php _e('WordPress Version:', 'asad-portfolio'); ?></strong></td>
                <td><?php echo get_bloginfo('version'); ?></td>
            </tr>
            <tr>
                <td><strong><?php _e('PHP Version:', 'asad-portfolio'); ?></strong></td>
                <td><?php echo PHP_VERSION; ?></td>
            </tr>
            <tr>
                <td><strong><?php _e('Active Theme:', 'asad-portfolio'); ?></strong></td>
                <td><?php echo wp_get_theme()->get('Name') . ' v' . wp_get_theme()->get('Version'); ?></td>
            </tr>
            <tr>
                <td><strong><?php _e('Active Plugins:', 'asad-portfolio'); ?></strong></td>
                <td><?php echo count(get_option('active_plugins', array())); ?></td>
            </tr>
        </table>
    </div>
</div>

<style>
.asad-dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
    margin: 30px 0;
}

.dashboard-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.card-icon {
    padding: 30px;
    text-align: center;
    color: #fff;
}

.card-content {
    padding: 20px;
}

.card-content h2 {
    margin: 0 0 10px;
    font-size: 20px;
}

.card-content p {
    margin: 0 0 15px;
    color: #666;
}

.asad-info-section {
    margin: 30px 0;
    background: #fff;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
}

.asad-info-section table {
    margin-top: 20px;
}

.asad-info-section table td {
    padding: 10px;
}
</style>
