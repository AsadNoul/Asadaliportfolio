<?php
/**
 * Admin Menu Setup
 *
 * @package Asad_Portfolio_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Admin Menu Pages
 */
function asad_add_admin_menu() {
    // Main menu page
    add_menu_page(
        __('Portfolio Manager', 'asad-portfolio'),
        __('Portfolio Manager', 'asad-portfolio'),
        'manage_options',
        'asad-portfolio-manager',
        'asad_dashboard_page',
        'dashicons-admin-appearance',
        61
    );

    // Dashboard (same as main menu)
    add_submenu_page(
        'asad-portfolio-manager',
        __('Dashboard', 'asad-portfolio'),
        __('Dashboard', 'asad-portfolio'),
        'manage_options',
        'asad-portfolio-manager',
        'asad_dashboard_page'
    );

    // Plugin Manager
    add_submenu_page(
        'asad-portfolio-manager',
        __('Plugin Manager', 'asad-portfolio'),
        __('Plugin Manager', 'asad-portfolio'),
        'manage_options',
        'asad-plugin-manager',
        'asad_plugin_manager_page'
    );

    // Theme Manager
    add_submenu_page(
        'asad-portfolio-manager',
        __('Theme Manager', 'asad-portfolio'),
        __('Theme Manager', 'asad-portfolio'),
        'manage_options',
        'asad-theme-manager',
        'asad_theme_manager_page'
    );

    // Theme Editor
    add_submenu_page(
        'asad-portfolio-manager',
        __('Theme Editor', 'asad-portfolio'),
        __('Theme Editor', 'asad-portfolio'),
        'manage_options',
        'asad-theme-editor',
        'asad_theme_editor_page'
    );

    // Header & Footer Builder
    add_submenu_page(
        'asad-portfolio-manager',
        __('Header & Footer', 'asad-portfolio'),
        __('Header & Footer', 'asad-portfolio'),
        'manage_options',
        'asad-header-footer',
        'asad_header_footer_page'
    );

    // Form Builder
    add_submenu_page(
        'asad-portfolio-manager',
        __('Form Builder', 'asad-portfolio'),
        __('Form Builder', 'asad-portfolio'),
        'manage_options',
        'asad-form-builder',
        'asad_form_builder_page'
    );

    // SEO Manager
    add_submenu_page(
        'asad-portfolio-manager',
        __('SEO Manager', 'asad-portfolio'),
        __('SEO Manager', 'asad-portfolio'),
        'manage_options',
        'asad-seo-manager',
        'asad_seo_manager_page'
    );

    // Performance Optimizer
    add_submenu_page(
        'asad-portfolio-manager',
        __('Performance', 'asad-portfolio'),
        __('Performance', 'asad-portfolio'),
        'manage_options',
        'asad-performance',
        'asad_performance_page'
    );

    // Analytics Dashboard
    add_submenu_page(
        'asad-portfolio-manager',
        __('Analytics', 'asad-portfolio'),
        __('Analytics', 'asad-portfolio'),
        'manage_options',
        'asad-analytics',
        'asad_analytics_page'
    );

    // Security Scanner
    add_submenu_page(
        'asad-portfolio-manager',
        __('Security', 'asad-portfolio'),
        __('Security', 'asad-portfolio'),
        'manage_options',
        'asad-security',
        'asad_security_page'
    );

    // Email Marketing
    add_submenu_page(
        'asad-portfolio-manager',
        __('Email Marketing', 'asad-portfolio'),
        __('Email Marketing', 'asad-portfolio'),
        'manage_options',
        'asad-email-marketing',
        'asad_email_marketing_page'
    );

    // Social Media Manager
    add_submenu_page(
        'asad-portfolio-manager',
        __('Social Media', 'asad-portfolio'),
        __('Social Media', 'asad-portfolio'),
        'manage_options',
        'asad-social-media',
        'asad_social_media_page'
    );

    // Database Manager
    add_submenu_page(
        'asad-portfolio-manager',
        __('Database', 'asad-portfolio'),
        __('Database', 'asad-portfolio'),
        'manage_options',
        'asad-database',
        'asad_database_page'
    );

    // Theme Settings (redirects to customizer)
    add_submenu_page(
        'asad-portfolio-manager',
        __('Theme Settings', 'asad-portfolio'),
        __('Theme Settings', 'asad-portfolio'),
        'manage_options',
        'asad-theme-settings',
        'asad_theme_settings_page'
    );
}
add_action('admin_menu', 'asad_add_admin_menu');

/**
 * Dashboard Page
 */
function asad_dashboard_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    include ASAD_THEME_DIR . '/templates/dashboard.php';
}

/**
 * Plugin Manager Page
 */
function asad_plugin_manager_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    include ASAD_THEME_DIR . '/templates/plugin-manager.php';
}

/**
 * Theme Manager Page
 */
function asad_theme_manager_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    include ASAD_THEME_DIR . '/templates/theme-manager.php';
}

/**
 * Theme Editor Page
 */
function asad_theme_editor_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    include ASAD_THEME_DIR . '/templates/theme-editor.php';
}

/**
 * Header & Footer Builder Page
 */
function asad_header_footer_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    include ASAD_THEME_DIR . '/templates/header-footer-builder.php';
}

/**
 * Form Builder Page
 */
function asad_form_builder_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    include ASAD_THEME_DIR . '/templates/form-builder.php';
}

/**
 * SEO Manager Page
 */
function asad_seo_manager_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    include ASAD_THEME_DIR . '/templates/seo-manager.php';
}

/**
 * Performance Page
 */
function asad_performance_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    include ASAD_THEME_DIR . '/templates/performance.php';
}

/**
 * Theme Settings Page (Redirects to Customizer)
 */
function asad_theme_settings_page() {
    wp_redirect(admin_url('customize.php'));
    exit;
}

/**
 * Analytics Dashboard Page
 */
function asad_analytics_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    include ASAD_THEME_DIR . '/templates/analytics.php';
}

/**
 * Security Scanner Page
 */
function asad_security_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    include ASAD_THEME_DIR . '/templates/security.php';
}

/**
 * Email Marketing Page
 */
function asad_email_marketing_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    include ASAD_THEME_DIR . '/templates/email-marketing.php';
}

/**
 * Social Media Manager Page
 */
function asad_social_media_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    include ASAD_THEME_DIR . '/templates/social-media.php';
}

/**
 * Database Manager Page
 */
function asad_database_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    include ASAD_THEME_DIR . '/templates/database.php';
}

/**
 * Add Quick Links to Admin Bar
 */
function asad_admin_bar_menu($wp_admin_bar) {
    if (!current_user_can('manage_options')) {
        return;
    }

    $wp_admin_bar->add_node(array(
        'id'    => 'asad-portfolio-manager',
        'title' => '<span class="ab-icon dashicons-admin-appearance"></span> Portfolio Manager',
        'href'  => admin_url('admin.php?page=asad-portfolio-manager'),
    ));

    $wp_admin_bar->add_node(array(
        'id'     => 'asad-plugin-manager',
        'parent' => 'asad-portfolio-manager',
        'title'  => __('Plugin Manager', 'asad-portfolio'),
        'href'   => admin_url('admin.php?page=asad-plugin-manager'),
    ));

    $wp_admin_bar->add_node(array(
        'id'     => 'asad-theme-manager',
        'parent' => 'asad-portfolio-manager',
        'title'  => __('Theme Manager', 'asad-portfolio'),
        'href'   => admin_url('admin.php?page=asad-theme-manager'),
    ));

    $wp_admin_bar->add_node(array(
        'id'     => 'asad-theme-editor',
        'parent' => 'asad-portfolio-manager',
        'title'  => __('Theme Editor', 'asad-portfolio'),
        'href'   => admin_url('admin.php?page=asad-theme-editor'),
    ));

    $wp_admin_bar->add_node(array(
        'id'     => 'asad-customizer',
        'parent' => 'asad-portfolio-manager',
        'title'  => __('Customize Theme', 'asad-portfolio'),
        'href'   => admin_url('customize.php'),
    ));
}
add_action('admin_bar_menu', 'asad_admin_bar_menu', 100);
