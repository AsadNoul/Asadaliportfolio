<?php
/**
 * Theme Manager Template
 *
 * @package Asad_Portfolio_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

$all_themes = asad_get_all_themes();
?>

<div class="wrap asad-theme-manager">
    <h1><?php _e('Theme Manager', 'asad-portfolio'); ?></h1>

    <div class="asad-tabs-wrapper">
        <nav class="nav-tab-wrapper">
            <a href="#installed-themes" class="nav-tab nav-tab-active"><?php _e('Installed Themes', 'asad-portfolio'); ?></a>
            <a href="#add-themes" class="nav-tab"><?php _e('Add New Theme', 'asad-portfolio'); ?></a>
            <a href="#upload-theme" class="nav-tab"><?php _e('Upload Theme', 'asad-portfolio'); ?></a>
        </nav>

        <!-- Installed Themes Tab -->
        <div id="installed-themes" class="tab-content active">
            <div class="themes-grid">
                <?php foreach ($all_themes as $theme) : ?>
                    <div class="theme-card <?php echo $theme['is_active'] ? 'active' : 'inactive'; ?>" data-theme="<?php echo esc_attr($theme['slug']); ?>">
                        <div class="theme-screenshot">
                            <?php if ($theme['screenshot']) : ?>
                                <img src="<?php echo esc_url($theme['screenshot']); ?>" alt="<?php echo esc_attr($theme['name']); ?>">
                            <?php else : ?>
                                <div class="no-screenshot">
                                    <i class="fas fa-image fa-3x"></i>
                                </div>
                            <?php endif; ?>
                            <?php if ($theme['is_active']) : ?>
                                <div class="active-badge"><?php _e('Active', 'asad-portfolio'); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="theme-card-content">
                            <h3><?php echo esc_html($theme['name']); ?></h3>
                            <p class="theme-version"><?php printf(__('Version: %s', 'asad-portfolio'), esc_html($theme['version'])); ?></p>
                            <p class="theme-author">
                                <?php if ($theme['author_uri']) : ?>
                                    <a href="<?php echo esc_url($theme['author_uri']); ?>" target="_blank"><?php echo esc_html($theme['author']); ?></a>
                                <?php else : ?>
                                    <?php echo esc_html($theme['author']); ?>
                                <?php endif; ?>
                            </p>
                            <p class="theme-description"><?php echo esc_html($theme['description']); ?></p>
                        </div>
                        <div class="theme-actions">
                            <?php if ($theme['is_active']) : ?>
                                <a href="<?php echo admin_url('customize.php'); ?>" class="button button-primary">
                                    <i class="fas fa-sliders-h"></i> <?php _e('Customize', 'asad-portfolio'); ?>
                                </a>
                            <?php else : ?>
                                <button class="button button-primary activate-theme" data-theme="<?php echo esc_attr($theme['slug']); ?>">
                                    <i class="fas fa-check"></i> <?php _e('Activate', 'asad-portfolio'); ?>
                                </button>
                                <button class="button button-danger delete-theme" data-theme="<?php echo esc_attr($theme['slug']); ?>">
                                    <i class="fas fa-trash"></i> <?php _e('Delete', 'asad-portfolio'); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Add New Theme Tab -->
        <div id="add-themes" class="tab-content">
            <div class="theme-install-search">
                <h2><?php _e('Search WordPress.org Themes', 'asad-portfolio'); ?></h2>
                <div class="search-form">
                    <input type="text" id="themeSearchWPOrg" placeholder="<?php _e('Search for themes...', 'asad-portfolio'); ?>" class="regular-text">
                    <button class="button button-primary" id="searchThemesBtn">
                        <i class="fas fa-search"></i> <?php _e('Search', 'asad-portfolio'); ?>
                    </button>
                </div>
            </div>

            <div id="wpOrgThemesResults" class="themes-grid">
                <p class="description"><?php _e('Search for themes from WordPress.org repository.', 'asad-portfolio'); ?></p>
            </div>
        </div>

        <!-- Upload Theme Tab -->
        <div id="upload-theme" class="tab-content">
            <h2><?php _e('Upload Theme ZIP File', 'asad-portfolio'); ?></h2>
            <form id="uploadThemeForm" enctype="multipart/form-data">
                <p>
                    <label for="theme_zip"><?php _e('Choose a ZIP file:', 'asad-portfolio'); ?></label>
                    <input type="file" name="theme_zip" id="theme_zip" accept=".zip" required>
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
.themes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    padding: 20px 0;
}

.theme-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}

.theme-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.theme-card.active {
    border: 2px solid #2ecc71;
}

.theme-screenshot {
    position: relative;
    width: 100%;
    height: 200px;
    overflow: hidden;
    background: #f0f0f0;
}

.theme-screenshot img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-screenshot {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #ccc;
}

.active-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #2ecc71;
    color: #fff;
    padding: 5px 10px;
    border-radius: 3px;
    font-weight: bold;
    font-size: 12px;
}

.theme-card-content {
    padding: 15px;
}

.theme-card-content h3 {
    margin: 0 0 10px;
}

.theme-version,
.theme-author {
    font-size: 13px;
    color: #666;
    margin: 5px 0;
}

.theme-description {
    font-size: 14px;
    color: #444;
    margin: 10px 0;
}

.theme-actions {
    padding: 10px 15px;
    background: #f9f9f9;
    border-top: 1px solid #ddd;
    display: flex;
    gap: 10px;
}
</style>
