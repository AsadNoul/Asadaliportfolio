<?php
/**
 * Theme Editor Template
 *
 * @package Asad_Portfolio_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_theme = wp_get_theme();
$theme_files = asad_get_theme_files();
?>

<div class="wrap asad-theme-editor">
    <h1><?php _e('Theme Editor', 'asad-portfolio'); ?></h1>
    <p class="description">
        <?php _e('Edit your theme files directly. Be careful - incorrect code can break your site!', 'asad-portfolio'); ?>
        <strong><?php _e('Backups are created automatically before saving.', 'asad-portfolio'); ?></strong>
    </p>

    <div class="theme-editor-container">
        <div class="file-browser">
            <div class="file-browser-header">
                <h3><?php echo esc_html($current_theme->get('Name')); ?></h3>
                <button class="button button-small" id="refreshFiles">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <div class="file-tree" id="fileTree">
                <?php echo asad_render_file_tree($theme_files); ?>
            </div>
        </div>

        <div class="editor-panel">
            <div class="editor-toolbar">
                <div class="editor-file-info">
                    <span id="currentFileName"><?php _e('Select a file to edit', 'asad-portfolio'); ?></span>
                </div>
                <div class="editor-actions">
                    <button class="button" id="createFileBtn">
                        <i class="fas fa-file-plus"></i> <?php _e('New File', 'asad-portfolio'); ?>
                    </button>
                    <button class="button button-primary" id="saveFileBtn" disabled>
                        <i class="fas fa-save"></i> <?php _e('Save File', 'asad-portfolio'); ?>
                    </button>
                    <button class="button" id="viewBackupsBtn">
                        <i class="fas fa-history"></i> <?php _e('View Backups', 'asad-portfolio'); ?>
                    </button>
                </div>
            </div>

            <div class="editor-wrapper">
                <textarea id="codeEditor" name="codeEditor"></textarea>
            </div>

            <div class="editor-status">
                <span id="editorStatus"></span>
            </div>
        </div>
    </div>
</div>

<style>
.theme-editor-container {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 0;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    overflow: hidden;
    height: 80vh;
    margin-top: 20px;
}

.file-browser {
    border-right: 1px solid #ddd;
    overflow-y: auto;
    background: #f9f9f9;
}

.file-browser-header {
    padding: 15px;
    background: #fff;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.file-browser-header h3 {
    margin: 0;
    font-size: 16px;
}

.file-tree {
    padding: 10px;
}

.file-tree-item {
    padding: 8px 10px;
    cursor: pointer;
    border-radius: 3px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background 0.2s;
}

.file-tree-item:hover {
    background: #e0e0e0;
}

.file-tree-item.active {
    background: #3498db;
    color: #fff;
}

.file-tree-item i {
    width: 16px;
}

.file-tree-folder {
    margin-left: 15px;
}

.editor-panel {
    display: flex;
    flex-direction: column;
}

.editor-toolbar {
    padding: 15px;
    background: #f5f5f5;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.editor-file-info {
    font-weight: bold;
}

.editor-actions {
    display: flex;
    gap: 10px;
}

.editor-wrapper {
    flex: 1;
    overflow: hidden;
}

#codeEditor {
    width: 100%;
    height: 100%;
    font-family: 'Courier New', monospace;
    font-size: 14px;
    border: none;
    resize: none;
}

.editor-status {
    padding: 10px 15px;
    background: #f9f9f9;
    border-top: 1px solid #ddd;
    font-size: 13px;
    color: #666;
}

.CodeMirror {
    height: 100% !important;
    font-size: 14px;
}
</style>

<?php
function asad_render_file_tree($files, $level = 0) {
    $html = '';
    foreach ($files as $file) {
        if ($file['type'] === 'directory') {
            $html .= '<div class="file-tree-directory">';
            $html .= '<div class="file-tree-item" data-type="directory" style="padding-left: ' . ($level * 15 + 10) . 'px;">';
            $html .= '<i class="fas fa-folder"></i>';
            $html .= '<span>' . esc_html($file['name']) . '</span>';
            $html .= '</div>';
            if (!empty($file['children'])) {
                $html .= '<div class="file-tree-folder">';
                $html .= asad_render_file_tree($file['children'], $level + 1);
                $html .= '</div>';
            }
            $html .= '</div>';
        } else {
            if ($file['editable']) {
                $html .= '<div class="file-tree-item file-item" data-file="' . esc_attr($file['path']) . '" style="padding-left: ' . ($level * 15 + 10) . 'px;">';
                $html .= '<i class="fas fa-file-code"></i>';
                $html .= '<span>' . esc_html($file['name']) . '</span>';
                $html .= '</div>';
            }
        }
    }
    return $html;
}
?>
