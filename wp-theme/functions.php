<?php
/**
 * Asad Portfolio Manager Theme Functions
 *
 * @package Asad_Portfolio_Manager
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define theme constants
define('ASAD_THEME_VERSION', '1.0.0');
define('ASAD_THEME_DIR', get_template_directory());
define('ASAD_THEME_URI', get_template_directory_uri());

/**
 * Theme Setup
 */
function asad_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ));
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('custom-header');
    add_theme_support('custom-background');

    // Register navigation menus
    register_nav_menus(array(
        'primary'   => __('Primary Menu', 'asad-portfolio'),
        'footer'    => __('Footer Menu', 'asad-portfolio'),
        'mobile'    => __('Mobile Menu', 'asad-portfolio'),
    ));

    // Add editor styles
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');

    // Add support for Block Editor (Gutenberg)
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
}
add_action('after_setup_theme', 'asad_theme_setup');

/**
 * Register Widget Areas
 */
function asad_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar', 'asad-portfolio'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here for the sidebar.', 'asad-portfolio'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Widget Area 1', 'asad-portfolio'),
        'id'            => 'footer-1',
        'description'   => __('Add widgets here for the first footer column.', 'asad-portfolio'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Widget Area 2', 'asad-portfolio'),
        'id'            => 'footer-2',
        'description'   => __('Add widgets here for the second footer column.', 'asad-portfolio'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Widget Area 3', 'asad-portfolio'),
        'id'            => 'footer-3',
        'description'   => __('Add widgets here for the third footer column.', 'asad-portfolio'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'asad_widgets_init');

/**
 * Enqueue Scripts and Styles
 */
function asad_scripts() {
    // Main stylesheet
    wp_enqueue_style('asad-style', get_stylesheet_uri(), array(), ASAD_THEME_VERSION);
    wp_enqueue_style('asad-main', ASAD_THEME_URI . '/assets/css/main.css', array(), ASAD_THEME_VERSION);

    // Google Fonts
    $google_fonts = get_theme_mod('asad_google_fonts', 'Roboto:400,500,700|Poppins:400,600,700');
    if ($google_fonts) {
        wp_enqueue_style('asad-google-fonts', 'https://fonts.googleapis.com/css2?family=' . $google_fonts . '&display=swap', array(), null);
    }

    // Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');

    // JavaScript
    wp_enqueue_script('asad-main', ASAD_THEME_URI . '/assets/js/main.js', array('jquery'), ASAD_THEME_VERSION, true);

    // Localize script for AJAX
    wp_localize_script('asad-main', 'asadTheme', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('asad-theme-nonce'),
        'darkMode' => get_theme_mod('asad_dark_mode', 'false'),
        'primaryColor' => get_theme_mod('asad_primary_color', '#3498db'),
        'secondaryColor' => get_theme_mod('asad_secondary_color', '#2ecc71'),
        'accentColor' => get_theme_mod('asad_accent_color', '#e74c3c'),
    ));

    // Comment reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'asad_scripts');

/**
 * Admin Scripts and Styles
 */
function asad_admin_scripts($hook) {
    // Only load on our custom admin pages
    if (strpos($hook, 'asad-') === false) {
        return;
    }

    wp_enqueue_style('asad-admin', ASAD_THEME_URI . '/assets/css/admin.css', array(), ASAD_THEME_VERSION);
    wp_enqueue_script('asad-admin', ASAD_THEME_URI . '/assets/js/admin.js', array('jquery'), ASAD_THEME_VERSION, true);

    // CodeMirror for theme editor
    wp_enqueue_code_editor(array('type' => 'text/html'));
    wp_enqueue_script('wp-theme-plugin-editor');
    wp_enqueue_style('wp-codemirror');

    wp_localize_script('asad-admin', 'asadAdmin', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('asad-admin-nonce'),
    ));
}
add_action('admin_enqueue_scripts', 'asad_admin_scripts');

/**
 * Add Custom CSS from Customizer
 */
function asad_custom_css() {
    $primary_color = get_theme_mod('asad_primary_color', '#3498db');
    $secondary_color = get_theme_mod('asad_secondary_color', '#2ecc71');
    $accent_color = get_theme_mod('asad_accent_color', '#e74c3c');
    $font_primary = get_theme_mod('asad_font_primary', 'Roboto');
    $font_secondary = get_theme_mod('asad_font_secondary', 'Poppins');
    $font_size = get_theme_mod('asad_font_size', '16');
    $custom_css = get_theme_mod('asad_custom_css', '');

    $css = "
    :root {
        --primary-color: {$primary_color};
        --secondary-color: {$secondary_color};
        --accent-color: {$accent_color};
        --font-primary: '{$font_primary}', sans-serif;
        --font-secondary: '{$font_secondary}', sans-serif;
        --font-size-base: {$font_size}px;
    }
    {$custom_css}
    ";

    wp_add_inline_style('asad-style', $css);
}
add_action('wp_enqueue_scripts', 'asad_custom_css');

/**
 * Register Custom Post Type for Portfolio
 */
function asad_register_portfolio_post_type() {
    $labels = array(
        'name'               => __('Portfolio', 'asad-portfolio'),
        'singular_name'      => __('Portfolio Item', 'asad-portfolio'),
        'add_new'            => __('Add New', 'asad-portfolio'),
        'add_new_item'       => __('Add New Portfolio Item', 'asad-portfolio'),
        'edit_item'          => __('Edit Portfolio Item', 'asad-portfolio'),
        'new_item'           => __('New Portfolio Item', 'asad-portfolio'),
        'view_item'          => __('View Portfolio Item', 'asad-portfolio'),
        'search_items'       => __('Search Portfolio', 'asad-portfolio'),
        'not_found'          => __('No portfolio items found', 'asad-portfolio'),
        'not_found_in_trash' => __('No portfolio items found in Trash', 'asad-portfolio'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'portfolio'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-portfolio',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'       => true, // Enable Gutenberg editor
    );

    register_post_type('portfolio', $args);
}
add_action('init', 'asad_register_portfolio_post_type');

/**
 * Include Required Files
 */
require_once ASAD_THEME_DIR . '/inc/customizer.php';
require_once ASAD_THEME_DIR . '/inc/admin-menu.php';
require_once ASAD_THEME_DIR . '/inc/plugin-manager.php';
require_once ASAD_THEME_DIR . '/inc/theme-manager.php';
require_once ASAD_THEME_DIR . '/inc/theme-editor.php';

/**
 * AJAX Handler for Dark Mode Toggle
 */
function asad_toggle_dark_mode() {
    check_ajax_referer('asad-theme-nonce', 'nonce');

    $dark_mode = isset($_POST['dark_mode']) ? sanitize_text_field($_POST['dark_mode']) : 'false';
    set_theme_mod('asad_dark_mode', $dark_mode);

    wp_send_json_success(array('dark_mode' => $dark_mode));
}
add_action('wp_ajax_asad_toggle_dark_mode', 'asad_toggle_dark_mode');
add_action('wp_ajax_nopriv_asad_toggle_dark_mode', 'asad_toggle_dark_mode');

/**
 * Add body classes for dark mode
 */
function asad_body_classes($classes) {
    if (get_theme_mod('asad_dark_mode', 'false') === 'true') {
        $classes[] = 'dark-mode';
    }
    return $classes;
}
add_filter('body_class', 'asad_body_classes');

/**
 * Add admin notice for theme activation
 */
function asad_activation_notice() {
    if (get_option('asad_theme_activated') !== 'yes') {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Asad Portfolio Manager theme activated! Visit the <a href="' . admin_url('themes.php?page=asad-theme-manager') . '">Theme Manager</a> to get started.', 'asad-portfolio'); ?></p>
        </div>
        <?php
        update_option('asad_theme_activated', 'yes');
    }
}
add_action('admin_notices', 'asad_activation_notice');
