<?php
/**
 * Theme Customizer
 *
 * @package Asad_Portfolio_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Customizer Settings
 */
function asad_customize_register($wp_customize) {

    // ========================================
    // SECTION: General Settings
    // ========================================
    $wp_customize->add_section('asad_general_settings', array(
        'title'    => __('General Settings', 'asad-portfolio'),
        'priority' => 30,
    ));

    // Dark Mode Toggle
    $wp_customize->add_setting('asad_dark_mode', array(
        'default'           => 'false',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('asad_dark_mode', array(
        'label'       => __('Enable Dark Mode', 'asad-portfolio'),
        'section'     => 'asad_general_settings',
        'type'        => 'checkbox',
        'description' => __('Toggle dark mode for the entire site.', 'asad-portfolio'),
    ));

    // ========================================
    // SECTION: Color Scheme
    // ========================================
    $wp_customize->add_section('asad_color_scheme', array(
        'title'       => __('Color Scheme', 'asad-portfolio'),
        'priority'    => 40,
        'description' => __('Customize the color scheme of your theme.', 'asad-portfolio'),
    ));

    // Primary Color
    $wp_customize->add_setting('asad_primary_color', array(
        'default'           => '#3498db',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asad_primary_color', array(
        'label'       => __('Primary Color', 'asad-portfolio'),
        'section'     => 'asad_color_scheme',
        'description' => __('Main brand color used for buttons, links, etc.', 'asad-portfolio'),
    )));

    // Secondary Color
    $wp_customize->add_setting('asad_secondary_color', array(
        'default'           => '#2ecc71',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asad_secondary_color', array(
        'label'       => __('Secondary Color', 'asad-portfolio'),
        'section'     => 'asad_color_scheme',
        'description' => __('Secondary accent color.', 'asad-portfolio'),
    )));

    // Accent Color
    $wp_customize->add_setting('asad_accent_color', array(
        'default'           => '#e74c3c',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asad_accent_color', array(
        'label'       => __('Accent Color', 'asad-portfolio'),
        'section'     => 'asad_color_scheme',
        'description' => __('Used for highlights and special elements.', 'asad-portfolio'),
    )));

    // Background Color
    $wp_customize->add_setting('asad_bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asad_bg_color', array(
        'label'       => __('Background Color', 'asad-portfolio'),
        'section'     => 'asad_color_scheme',
        'description' => __('Main background color (Light mode).', 'asad-portfolio'),
    )));

    // Text Color
    $wp_customize->add_setting('asad_text_color', array(
        'default'           => '#333333',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'asad_text_color', array(
        'label'       => __('Text Color', 'asad-portfolio'),
        'section'     => 'asad_color_scheme',
        'description' => __('Main text color.', 'asad-portfolio'),
    )));

    // Color Scheme Presets
    $wp_customize->add_setting('asad_color_preset', array(
        'default'           => 'default',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('asad_color_preset', array(
        'label'       => __('Color Presets', 'asad-portfolio'),
        'section'     => 'asad_color_scheme',
        'type'        => 'select',
        'choices'     => array(
            'default' => __('Default (Blue)', 'asad-portfolio'),
            'purple'  => __('Purple Dream', 'asad-portfolio'),
            'green'   => __('Nature Green', 'asad-portfolio'),
            'orange'  => __('Sunset Orange', 'asad-portfolio'),
            'pink'    => __('Pink Blush', 'asad-portfolio'),
            'dark'    => __('Dark Professional', 'asad-portfolio'),
        ),
        'description' => __('Quick color scheme presets. Colors will update when you change this.', 'asad-portfolio'),
    ));

    // ========================================
    // SECTION: Typography
    // ========================================
    $wp_customize->add_section('asad_typography', array(
        'title'       => __('Typography', 'asad-portfolio'),
        'priority'    => 50,
        'description' => __('Customize fonts and text settings.', 'asad-portfolio'),
    ));

    // Primary Font
    $wp_customize->add_setting('asad_font_primary', array(
        'default'           => 'Roboto',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('asad_font_primary', array(
        'label'       => __('Primary Font (Body)', 'asad-portfolio'),
        'section'     => 'asad_typography',
        'type'        => 'select',
        'choices'     => asad_get_google_fonts(),
        'description' => __('Font used for body text.', 'asad-portfolio'),
    ));

    // Secondary Font
    $wp_customize->add_setting('asad_font_secondary', array(
        'default'           => 'Poppins',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('asad_font_secondary', array(
        'label'       => __('Secondary Font (Headings)', 'asad-portfolio'),
        'section'     => 'asad_typography',
        'type'        => 'select',
        'choices'     => asad_get_google_fonts(),
        'description' => __('Font used for headings.', 'asad-portfolio'),
    ));

    // Font Size
    $wp_customize->add_setting('asad_font_size', array(
        'default'           => '16',
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('asad_font_size', array(
        'label'       => __('Base Font Size (px)', 'asad-portfolio'),
        'section'     => 'asad_typography',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 12,
            'max'  => 24,
            'step' => 1,
        ),
    ));

    // Line Height
    $wp_customize->add_setting('asad_line_height', array(
        'default'           => '1.6',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('asad_line_height', array(
        'label'       => __('Line Height', 'asad-portfolio'),
        'section'     => 'asad_typography',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 1.0,
            'max'  => 2.5,
            'step' => 0.1,
        ),
    ));

    // ========================================
    // SECTION: Header Settings
    // ========================================
    $wp_customize->add_section('asad_header_settings', array(
        'title'    => __('Header Settings', 'asad-portfolio'),
        'priority' => 60,
    ));

    // Header Layout
    $wp_customize->add_setting('asad_header_layout', array(
        'default'           => 'layout1',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('asad_header_layout', array(
        'label'   => __('Header Layout', 'asad-portfolio'),
        'section' => 'asad_header_settings',
        'type'    => 'select',
        'choices' => array(
            'layout1' => __('Logo Left, Menu Right', 'asad-portfolio'),
            'layout2' => __('Centered Logo, Menu Below', 'asad-portfolio'),
            'layout3' => __('Logo Right, Menu Left', 'asad-portfolio'),
        ),
    ));

    // Show Search in Header
    $wp_customize->add_setting('asad_header_search', array(
        'default'           => true,
        'sanitize_callback' => 'asad_sanitize_checkbox',
    ));

    $wp_customize->add_control('asad_header_search', array(
        'label'   => __('Show Search in Header', 'asad-portfolio'),
        'section' => 'asad_header_settings',
        'type'    => 'checkbox',
    ));

    // Sticky Header
    $wp_customize->add_setting('asad_sticky_header', array(
        'default'           => true,
        'sanitize_callback' => 'asad_sanitize_checkbox',
    ));

    $wp_customize->add_control('asad_sticky_header', array(
        'label'   => __('Enable Sticky Header', 'asad-portfolio'),
        'section' => 'asad_header_settings',
        'type'    => 'checkbox',
    ));

    // ========================================
    // SECTION: Footer Settings
    // ========================================
    $wp_customize->add_section('asad_footer_settings', array(
        'title'    => __('Footer Settings', 'asad-portfolio'),
        'priority' => 70,
    ));

    // Footer Text
    $wp_customize->add_setting('asad_footer_text', array(
        'default'           => '&copy; ' . date('Y') . ' ' . get_bloginfo('name') . '. All rights reserved.',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('asad_footer_text', array(
        'label'       => __('Footer Copyright Text', 'asad-portfolio'),
        'section'     => 'asad_footer_settings',
        'type'        => 'textarea',
        'description' => __('HTML allowed.', 'asad-portfolio'),
    ));

    // Footer Columns
    $wp_customize->add_setting('asad_footer_columns', array(
        'default'           => '3',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('asad_footer_columns', array(
        'label'   => __('Footer Widget Columns', 'asad-portfolio'),
        'section' => 'asad_footer_settings',
        'type'    => 'select',
        'choices' => array(
            '1' => __('1 Column', 'asad-portfolio'),
            '2' => __('2 Columns', 'asad-portfolio'),
            '3' => __('3 Columns', 'asad-portfolio'),
            '4' => __('4 Columns', 'asad-portfolio'),
        ),
    ));

    // Social Media Links
    $social_networks = array(
        'facebook'  => 'Facebook',
        'twitter'   => 'Twitter',
        'instagram' => 'Instagram',
        'linkedin'  => 'LinkedIn',
        'github'    => 'GitHub',
        'youtube'   => 'YouTube',
    );

    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting("asad_social_{$network}", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control("asad_social_{$network}", array(
            'label'   => $label . __(' URL', 'asad-portfolio'),
            'section' => 'asad_footer_settings',
            'type'    => 'url',
        ));
    }

    // ========================================
    // SECTION: Layout Settings
    // ========================================
    $wp_customize->add_section('asad_layout_settings', array(
        'title'    => __('Layout Settings', 'asad-portfolio'),
        'priority' => 80,
    ));

    // Container Width
    $wp_customize->add_setting('asad_container_width', array(
        'default'           => '1200',
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('asad_container_width', array(
        'label'       => __('Container Width (px)', 'asad-portfolio'),
        'section'     => 'asad_layout_settings',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 960,
            'max'  => 1920,
            'step' => 10,
        ),
    ));

    // Sidebar Position
    $wp_customize->add_setting('asad_sidebar_position', array(
        'default'           => 'right',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('asad_sidebar_position', array(
        'label'   => __('Sidebar Position', 'asad-portfolio'),
        'section' => 'asad_layout_settings',
        'type'    => 'select',
        'choices' => array(
            'left'  => __('Left', 'asad-portfolio'),
            'right' => __('Right', 'asad-portfolio'),
            'none'  => __('No Sidebar', 'asad-portfolio'),
        ),
    ));

    // ========================================
    // SECTION: Custom Code
    // ========================================
    $wp_customize->add_section('asad_custom_code', array(
        'title'    => __('Custom Code', 'asad-portfolio'),
        'priority' => 90,
    ));

    // Custom CSS
    $wp_customize->add_setting('asad_custom_css', array(
        'default'           => '',
        'sanitize_callback' => 'wp_strip_all_tags',
        'transport'         => 'postMessage',
    ));

    $wp_customize->add_control('asad_custom_css', array(
        'label'       => __('Custom CSS', 'asad-portfolio'),
        'section'     => 'asad_custom_code',
        'type'        => 'textarea',
        'description' => __('Add your custom CSS here.', 'asad-portfolio'),
    ));

    // Custom JavaScript
    $wp_customize->add_setting('asad_custom_js', array(
        'default'           => '',
        'sanitize_callback' => 'wp_strip_all_tags',
    ));

    $wp_customize->add_control('asad_custom_js', array(
        'label'       => __('Custom JavaScript', 'asad-portfolio'),
        'section'     => 'asad_custom_code',
        'type'        => 'textarea',
        'description' => __('Add your custom JavaScript here (without <script> tags).', 'asad-portfolio'),
    ));

    // Header Scripts
    $wp_customize->add_setting('asad_header_scripts', array(
        'default'           => '',
        'sanitize_callback' => 'asad_sanitize_scripts',
    ));

    $wp_customize->add_control('asad_header_scripts', array(
        'label'       => __('Header Scripts', 'asad-portfolio'),
        'section'     => 'asad_custom_code',
        'type'        => 'textarea',
        'description' => __('Scripts added here will be in the <head> section.', 'asad-portfolio'),
    ));

    // Footer Scripts
    $wp_customize->add_setting('asad_footer_scripts', array(
        'default'           => '',
        'sanitize_callback' => 'asad_sanitize_scripts',
    ));

    $wp_customize->add_control('asad_footer_scripts', array(
        'label'       => __('Footer Scripts', 'asad-portfolio'),
        'section'     => 'asad_custom_code',
        'type'        => 'textarea',
        'description' => __('Scripts added here will be before </body> tag.', 'asad-portfolio'),
    ));
}
add_action('customize_register', 'asad_customize_register');

/**
 * Get Google Fonts List
 */
function asad_get_google_fonts() {
    return array(
        'Roboto'         => 'Roboto',
        'Open Sans'      => 'Open Sans',
        'Lato'           => 'Lato',
        'Montserrat'     => 'Montserrat',
        'Poppins'        => 'Poppins',
        'Raleway'        => 'Raleway',
        'Ubuntu'         => 'Ubuntu',
        'Nunito'         => 'Nunito',
        'Playfair Display' => 'Playfair Display',
        'Merriweather'   => 'Merriweather',
        'Inter'          => 'Inter',
        'Work Sans'      => 'Work Sans',
    );
}

/**
 * Sanitize Checkbox
 */
function asad_sanitize_checkbox($checked) {
    return ((isset($checked) && true == $checked) ? true : false);
}

/**
 * Sanitize Scripts
 */
function asad_sanitize_scripts($input) {
    return $input; // Allow scripts but be careful
}

/**
 * Customizer Live Preview
 */
function asad_customize_preview_js() {
    wp_enqueue_script('asad-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array('customize-preview'), ASAD_THEME_VERSION, true);
}
add_action('customize_preview_init', 'asad_customize_preview_js');

/**
 * Output Custom Scripts in Header
 */
function asad_output_header_scripts() {
    $header_scripts = get_theme_mod('asad_header_scripts', '');
    if (!empty($header_scripts)) {
        echo $header_scripts;
    }
}
add_action('wp_head', 'asad_output_header_scripts', 100);

/**
 * Output Custom Scripts in Footer
 */
function asad_output_footer_scripts() {
    $footer_scripts = get_theme_mod('asad_footer_scripts', '');
    if (!empty($footer_scripts)) {
        echo $footer_scripts;
    }

    $custom_js = get_theme_mod('asad_custom_js', '');
    if (!empty($custom_js)) {
        echo '<script>' . $custom_js . '</script>';
    }
}
add_action('wp_footer', 'asad_output_footer_scripts', 100);
