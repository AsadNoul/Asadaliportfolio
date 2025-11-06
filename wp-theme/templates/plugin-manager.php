<?php
/**
 * Plugin Manager Template
 *
 * @package Asad_Portfolio_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

$all_plugins = asad_get_all_plugins();
?>

<div class="wrap asad-plugin-manager">
    <h1><?php _e('Plugin Manager', 'asad-portfolio'); ?></h1>

    <div class="asad-tabs-wrapper">
        <nav class="nav-tab-wrapper">
            <a href="#installed-plugins" class="nav-tab nav-tab-active"><?php _e('Installed Plugins', 'asad-portfolio'); ?></a>
            <a href="#add-plugins" class="nav-tab"><?php _e('Add New Plugin', 'asad-portfolio'); ?></a>
            <a href="#upload-plugin" class="nav-tab"><?php _e('Upload Plugin', 'asad-portfolio'); ?></a>
        </nav>

        <!-- Installed Plugins Tab -->
        <div id="installed-plugins" class="tab-content active">
            <div class="plugin-search-box">
                <input type="text" id="pluginSearchInput" placeholder="<?php _e('Search plugins...', 'asad-portfolio'); ?>" class="regular-text">
            </div>

            <div id="pluginsList" class="plugins-list">
                <?php foreach ($all_plugins as $plugin) : ?>
                    <div class="plugin-card <?php echo $plugin['is_active'] ? 'active' : 'inactive'; ?>" data-plugin="<?php echo esc_attr($plugin['file']); ?>">
                        <div class="plugin-card-top">
                            <div class="name column-name">
                                <h3><?php echo esc_html($plugin['name']); ?>
                                    <?php if ($plugin['is_active']) : ?>
                                        <span class="plugin-status active-status"><?php _e('Active', 'asad-portfolio'); ?></span>
                                    <?php else : ?>
                                        <span class="plugin-status inactive-status"><?php _e('Inactive', 'asad-portfolio'); ?></span>
                                    <?php endif; ?>
                                </h3>
                            </div>
                            <div class="desc column-description">
                                <p><?php echo wp_kses_post($plugin['description']); ?></p>
                            </div>
                            <div class="plugin-meta">
                                <span class="version"><strong><?php _e('Version:', 'asad-portfolio'); ?></strong> <?php echo esc_html($plugin['version']); ?></span>
                                <span class="author"><strong><?php _e('By:', 'asad-portfolio'); ?></strong>
                                    <?php if ($plugin['author_uri']) : ?>
                                        <a href="<?php echo esc_url($plugin['author_uri']); ?>" target="_blank"><?php echo esc_html($plugin['author']); ?></a>
                                    <?php else : ?>
                                        <?php echo esc_html($plugin['author']); ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                        <div class="plugin-card-bottom">
                            <?php if ($plugin['is_active']) : ?>
                                <button class="button deactivate-plugin" data-plugin="<?php echo esc_attr($plugin['file']); ?>">
                                    <i class="fas fa-times-circle"></i> <?php _e('Deactivate', 'asad-portfolio'); ?>
                                </button>
                            <?php else : ?>
                                <button class="button button-primary activate-plugin" data-plugin="<?php echo esc_attr($plugin['file']); ?>">
                                    <i class="fas fa-check-circle"></i> <?php _e('Activate', 'asad-portfolio'); ?>
                                </button>
                                <button class="button button-danger delete-plugin" data-plugin="<?php echo esc_attr($plugin['file']); ?>">
                                    <i class="fas fa-trash"></i> <?php _e('Delete', 'asad-portfolio'); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Add New Plugin Tab -->
        <div id="add-plugins" class="tab-content">
            <div class="plugin-install-search">
                <h2><?php _e('Search WordPress.org Plugins', 'asad-portfolio'); ?></h2>
                <div class="search-form">
                    <input type="text" id="pluginSearchWPOrg" placeholder="<?php _e('Search for plugins...', 'asad-portfolio'); ?>" class="regular-text">
                    <button class="button button-primary" id="searchWPOrgBtn">
                        <i class="fas fa-search"></i> <?php _e('Search', 'asad-portfolio'); ?>
                    </button>
                </div>
            </div>

            <div id="wpOrgPluginsResults" class="plugins-list">
                <p class="description"><?php _e('Search for plugins from WordPress.org repository.', 'asad-portfolio'); ?></p>
            </div>
        </div>

        <!-- Upload Plugin Tab -->
        <div id="upload-plugin" class="tab-content">
            <h2><?php _e('Upload Plugin ZIP File', 'asad-portfolio'); ?></h2>
            <form id="uploadPluginForm" enctype="multipart/form-data">
                <p>
                    <label for="plugin_zip"><?php _e('Choose a ZIP file:', 'asad-portfolio'); ?></label>
                    <input type="file" name="plugin_zip" id="plugin_zip" accept=".zip" required>
                </p>
                <p>
                    <button type="submit" class="button button-primary">
                        <i class="fas fa-upload"></i> <?php _e('Upload & Install', 'asad-portfolio'); ?>
                    </button>
                </p>
            </form>
        </div>
    </div>
</div>

<style>
.asad-plugin-manager {
    margin: 20px 0;
}

.asad-tabs-wrapper {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-top: 20px;
}

.nav-tab-wrapper {
    border-bottom: 1px solid #ddd;
    margin: 0;
}

.tab-content {
    display: none;
    padding: 20px;
}

.tab-content.active {
    display: block;
}

.plugin-search-box {
    margin-bottom: 20px;
}

.plugin-search-box input {
    width: 100%;
    max-width: 500px;
}

.plugins-list {
    display: grid;
    gap: 20px;
}

.plugin-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    overflow: hidden;
    transition: box-shadow 0.3s;
}

.plugin-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.plugin-card.active {
    border-left: 4px solid #2ecc71;
}

.plugin-card.inactive {
    border-left: 4px solid #e74c3c;
}

.plugin-card-top {
    padding: 20px;
}

.plugin-card-top h3 {
    margin: 0 0 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.plugin-status {
    font-size: 12px;
    padding: 3px 8px;
    border-radius: 3px;
    font-weight: normal;
}

.active-status {
    background: #2ecc71;
    color: #fff;
}

.inactive-status {
    background: #95a5a6;
    color: #fff;
}

.plugin-meta {
    margin-top: 10px;
    display: flex;
    gap: 20px;
    font-size: 13px;
    color: #666;
}

.plugin-card-bottom {
    padding: 10px 20px;
    background: #f9f9f9;
    border-top: 1px solid #ddd;
    display: flex;
    gap: 10px;
}

.button-danger {
    background: #e74c3c;
    color: #fff;
    border-color: #c0392b;
}

.button-danger:hover {
    background: #c0392b;
}

.plugin-install-search {
    margin-bottom: 30px;
}

.search-form {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.search-form input {
    flex: 1;
    max-width: 500px;
}
</style>
