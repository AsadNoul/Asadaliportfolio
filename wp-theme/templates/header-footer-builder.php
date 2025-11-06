<?php
/**
 * Header & Footer Builder Template
 *
 * @package Asad_Portfolio_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap asad-header-footer-builder">
    <h1><?php _e('Header & Footer Builder', 'asad-portfolio'); ?></h1>

    <div class="asad-tabs-wrapper">
        <nav class="nav-tab-wrapper">
            <a href="#header-settings" class="nav-tab nav-tab-active"><?php _e('Header Settings', 'asad-portfolio'); ?></a>
            <a href="#footer-settings" class="nav-tab"><?php _e('Footer Settings', 'asad-portfolio'); ?></a>
            <a href="#menu-settings" class="nav-tab"><?php _e('Menu Settings', 'asad-portfolio'); ?></a>
        </nav>

        <!-- Header Settings Tab -->
        <div id="header-settings" class="tab-content active">
            <h2><?php _e('Header Configuration', 'asad-portfolio'); ?></h2>

            <table class="form-table">
                <tr>
                    <th><label for="header_layout"><?php _e('Header Layout', 'asad-portfolio'); ?></label></th>
                    <td>
                        <select id="header_layout" name="header_layout">
                            <option value="layout1" <?php selected(get_theme_mod('asad_header_layout', 'layout1'), 'layout1'); ?>>
                                <?php _e('Logo Left, Menu Right', 'asad-portfolio'); ?>
                            </option>
                            <option value="layout2" <?php selected(get_theme_mod('asad_header_layout', 'layout1'), 'layout2'); ?>>
                                <?php _e('Centered Logo, Menu Below', 'asad-portfolio'); ?>
                            </option>
                            <option value="layout3" <?php selected(get_theme_mod('asad_header_layout', 'layout1'), 'layout3'); ?>>
                                <?php _e('Logo Right, Menu Left', 'asad-portfolio'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="sticky_header"><?php _e('Sticky Header', 'asad-portfolio'); ?></label></th>
                    <td>
                        <input type="checkbox" id="sticky_header" name="sticky_header" value="1" <?php checked(get_theme_mod('asad_sticky_header', true), true); ?>>
                        <span class="description"><?php _e('Enable sticky header on scroll', 'asad-portfolio'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><label for="header_search"><?php _e('Show Search', 'asad-portfolio'); ?></label></th>
                    <td>
                        <input type="checkbox" id="header_search" name="header_search" value="1" <?php checked(get_theme_mod('asad_header_search', true), true); ?>>
                        <span class="description"><?php _e('Display search in header', 'asad-portfolio'); ?></span>
                    </td>
                </tr>
            </table>

            <div class="header-preview">
                <h3><?php _e('Logo Management', 'asad-portfolio'); ?></h3>
                <p>
                    <a href="<?php echo admin_url('customize.php?autofocus[control]=custom_logo'); ?>" class="button button-primary">
                        <i class="fas fa-image"></i> <?php _e('Upload/Change Logo', 'asad-portfolio'); ?>
                    </a>
                </p>
                <?php if (has_custom_logo()) : ?>
                    <div class="current-logo">
                        <?php the_custom_logo(); ?>
                    </div>
                <?php endif; ?>
            </div>

            <p class="submit">
                <button class="button button-primary" id="saveHeaderSettings">
                    <i class="fas fa-save"></i> <?php _e('Save Header Settings', 'asad-portfolio'); ?>
                </button>
            </p>
        </div>

        <!-- Footer Settings Tab -->
        <div id="footer-settings" class="tab-content">
            <h2><?php _e('Footer Configuration', 'asad-portfolio'); ?></h2>

            <table class="form-table">
                <tr>
                    <th><label for="footer_columns"><?php _e('Widget Columns', 'asad-portfolio'); ?></label></th>
                    <td>
                        <select id="footer_columns" name="footer_columns">
                            <option value="1" <?php selected(get_theme_mod('asad_footer_columns', '3'), '1'); ?>>1 <?php _e('Column', 'asad-portfolio'); ?></option>
                            <option value="2" <?php selected(get_theme_mod('asad_footer_columns', '3'), '2'); ?>>2 <?php _e('Columns', 'asad-portfolio'); ?></option>
                            <option value="3" <?php selected(get_theme_mod('asad_footer_columns', '3'), '3'); ?>>3 <?php _e('Columns', 'asad-portfolio'); ?></option>
                            <option value="4" <?php selected(get_theme_mod('asad_footer_columns', '3'), '4'); ?>>4 <?php _e('Columns', 'asad-portfolio'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="footer_text"><?php _e('Copyright Text', 'asad-portfolio'); ?></label></th>
                    <td>
                        <textarea id="footer_text" name="footer_text" rows="3" class="large-text"><?php echo esc_textarea(get_theme_mod('asad_footer_text', '&copy; ' . date('Y') . ' ' . get_bloginfo('name'))); ?></textarea>
                        <p class="description"><?php _e('HTML is allowed', 'asad-portfolio'); ?></p>
                    </td>
                </tr>
            </table>

            <h3><?php _e('Social Media Links', 'asad-portfolio'); ?></h3>
            <table class="form-table">
                <?php
                $social_networks = array(
                    'facebook'  => 'Facebook',
                    'twitter'   => 'Twitter',
                    'instagram' => 'Instagram',
                    'linkedin'  => 'LinkedIn',
                    'github'    => 'GitHub',
                    'youtube'   => 'YouTube',
                );

                foreach ($social_networks as $network => $label) :
                ?>
                    <tr>
                        <th><label for="social_<?php echo $network; ?>"><?php echo $label; ?></label></th>
                        <td>
                            <input type="url" id="social_<?php echo $network; ?>" name="social_<?php echo $network; ?>" value="<?php echo esc_url(get_theme_mod("asad_social_{$network}", '')); ?>" class="regular-text">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <p>
                <a href="<?php echo admin_url('widgets.php'); ?>" class="button">
                    <i class="fas fa-th"></i> <?php _e('Manage Footer Widgets', 'asad-portfolio'); ?>
                </a>
            </p>

            <p class="submit">
                <button class="button button-primary" id="saveFooterSettings">
                    <i class="fas fa-save"></i> <?php _e('Save Footer Settings', 'asad-portfolio'); ?>
                </button>
            </p>
        </div>

        <!-- Menu Settings Tab -->
        <div id="menu-settings" class="tab-content">
            <h2><?php _e('Menu Management', 'asad-portfolio'); ?></h2>

            <div class="menu-info-box">
                <p><?php _e('Manage your navigation menus here. You have access to:', 'asad-portfolio'); ?></p>
                <ul>
                    <li><strong><?php _e('Primary Menu', 'asad-portfolio'); ?></strong> - <?php _e('Main navigation in header', 'asad-portfolio'); ?></li>
                    <li><strong><?php _e('Footer Menu', 'asad-portfolio'); ?></strong> - <?php _e('Menu in footer area', 'asad-portfolio'); ?></li>
                    <li><strong><?php _e('Mobile Menu', 'asad-portfolio'); ?></strong> - <?php _e('Mobile-responsive menu', 'asad-portfolio'); ?></li>
                </ul>
            </div>

            <p>
                <a href="<?php echo admin_url('nav-menus.php'); ?>" class="button button-primary button-hero">
                    <i class="fas fa-bars"></i> <?php _e('Go to Menu Editor', 'asad-portfolio'); ?>
                </a>
            </p>

            <div class="current-menus">
                <h3><?php _e('Current Menu Assignments', 'asad-portfolio'); ?></h3>
                <?php
                $locations = get_nav_menu_locations();
                $menus = wp_get_nav_menus();
                $menu_locations = get_registered_nav_menus();

                if (!empty($menu_locations)) :
                    echo '<table class="widefat">';
                    echo '<thead><tr><th>' . __('Location', 'asad-portfolio') . '</th><th>' . __('Assigned Menu', 'asad-portfolio') . '</th></tr></thead>';
                    echo '<tbody>';
                    foreach ($menu_locations as $location => $description) :
                        echo '<tr>';
                        echo '<td><strong>' . esc_html($description) . '</strong></td>';
                        echo '<td>';
                        if (isset($locations[$location]) && $locations[$location] != 0) {
                            $menu = wp_get_nav_menu_object($locations[$location]);
                            if ($menu) {
                                echo esc_html($menu->name);
                            }
                        } else {
                            echo '<em>' . __('No menu assigned', 'asad-portfolio') . '</em>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    endforeach;
                    echo '</tbody>';
                    echo '</table>';
                endif;
                ?>
            </div>
        </div>
    </div>
</div>

<style>
.menu-info-box {
    background: #e7f3ff;
    border-left: 4px solid #3498db;
    padding: 15px;
    margin: 20px 0;
}

.menu-info-box ul {
    margin: 10px 0 0 20px;
}

.current-menus {
    margin-top: 30px;
}

.current-menus table {
    margin-top: 15px;
}

.header-preview {
    background: #f9f9f9;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.current-logo {
    margin-top: 15px;
    padding: 10px;
    background: #fff;
    border: 1px solid #ddd;
    display: inline-block;
}

.current-logo img {
    max-width: 300px;
    height: auto;
}
</style>
