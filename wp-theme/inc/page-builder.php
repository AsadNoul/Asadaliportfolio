<?php
/**
 * Page Builder
 * Drag-and-drop page creation with pre-built blocks
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Page Builder meta box
 */
function asad_register_page_builder() {
    add_meta_box(
        'asad_page_builder',
        'Page Builder',
        'asad_page_builder_callback',
        array('page', 'post'),
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'asad_register_page_builder');

/**
 * Page Builder meta box callback
 */
function asad_page_builder_callback($post) {
    wp_nonce_field('asad_page_builder_save', 'asad_page_builder_nonce');

    $page_builder_data = get_post_meta($post->ID, '_asad_page_builder_data', true);
    $page_builder_enabled = get_post_meta($post->ID, '_asad_page_builder_enabled', true);

    ?>
    <div class="asad-page-builder-wrapper">
        <div class="asad-pb-header">
            <label>
                <input type="checkbox" name="asad_page_builder_enabled" value="1" <?php checked($page_builder_enabled, '1'); ?>>
                Enable Page Builder for this page
            </label>
        </div>

        <div class="asad-pb-container" style="<?php echo $page_builder_enabled ? '' : 'display:none;'; ?>">
            <div class="asad-pb-sidebar">
                <h3>Available Blocks</h3>
                <div class="asad-pb-blocks">
                    <?php foreach (asad_get_page_builder_blocks() as $block_id => $block): ?>
                        <div class="asad-pb-block-item" data-block-type="<?php echo esc_attr($block_id); ?>">
                            <span class="dashicons <?php echo esc_attr($block['icon']); ?>"></span>
                            <span><?php echo esc_html($block['name']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="asad-pb-canvas">
                <h3>Page Canvas</h3>
                <div class="asad-pb-canvas-area" id="asad-pb-canvas">
                    <?php if (!empty($page_builder_data)): ?>
                        <?php echo $page_builder_data; ?>
                    <?php else: ?>
                        <p class="asad-pb-empty">Drag blocks here to start building your page</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="asad-pb-properties">
                <h3>Block Properties</h3>
                <div id="asad-pb-properties-content">
                    <p>Select a block to edit its properties</p>
                </div>
            </div>
        </div>

        <input type="hidden" name="asad_page_builder_data" id="asad_page_builder_data" value="">
    </div>

    <style>
        .asad-page-builder-wrapper {
            margin: 20px 0;
        }
        .asad-pb-header {
            padding: 15px;
            background: #f0f0f1;
            border: 1px solid #c3c4c7;
            margin-bottom: 15px;
        }
        .asad-pb-container {
            display: flex;
            gap: 15px;
            min-height: 600px;
        }
        .asad-pb-sidebar {
            width: 200px;
            background: #fff;
            border: 1px solid #c3c4c7;
            padding: 15px;
        }
        .asad-pb-blocks {
            margin-top: 10px;
        }
        .asad-pb-block-item {
            padding: 10px;
            background: #f0f0f1;
            margin-bottom: 8px;
            cursor: move;
            border-radius: 3px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .asad-pb-block-item:hover {
            background: #e0e0e1;
        }
        .asad-pb-canvas {
            flex: 1;
            background: #fff;
            border: 1px solid #c3c4c7;
            padding: 15px;
        }
        .asad-pb-canvas-area {
            min-height: 500px;
            background: #fafafa;
            padding: 20px;
            margin-top: 10px;
        }
        .asad-pb-empty {
            text-align: center;
            color: #999;
            padding: 50px;
        }
        .asad-pb-properties {
            width: 250px;
            background: #fff;
            border: 1px solid #c3c4c7;
            padding: 15px;
        }
        .asad-pb-block {
            position: relative;
            margin-bottom: 20px;
            padding: 20px;
            background: #fff;
            border: 2px dashed #c3c4c7;
            cursor: pointer;
        }
        .asad-pb-block:hover {
            border-color: #2271b1;
        }
        .asad-pb-block.selected {
            border-color: #2271b1;
            border-style: solid;
        }
        .asad-pb-block-controls {
            position: absolute;
            top: 5px;
            right: 5px;
            display: none;
        }
        .asad-pb-block:hover .asad-pb-block-controls {
            display: block;
        }
        .asad-pb-block-controls button {
            margin-left: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
    <?php
}

/**
 * Get available page builder blocks
 */
function asad_get_page_builder_blocks() {
    return array(
        'heading' => array(
            'name' => 'Heading',
            'icon' => 'dashicons-editor-textcolor',
            'template' => '<h2 class="asad-heading" contenteditable="true">Your Heading Here</h2>'
        ),
        'paragraph' => array(
            'name' => 'Paragraph',
            'icon' => 'dashicons-editor-paragraph',
            'template' => '<p class="asad-paragraph" contenteditable="true">Your paragraph text here. Click to edit.</p>'
        ),
        'image' => array(
            'name' => 'Image',
            'icon' => 'dashicons-format-image',
            'template' => '<div class="asad-image"><img src="https://via.placeholder.com/800x400" alt="Placeholder" style="max-width:100%;height:auto;"></div>'
        ),
        'button' => array(
            'name' => 'Button',
            'icon' => 'dashicons-button',
            'template' => '<div class="asad-button-wrapper" style="text-align:center;"><a href="#" class="asad-button" style="display:inline-block;padding:12px 30px;background:#3498db;color:#fff;text-decoration:none;border-radius:5px;">Click Me</a></div>'
        ),
        'columns' => array(
            'name' => '2 Columns',
            'icon' => 'dashicons-columns',
            'template' => '<div class="asad-columns" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;"><div class="asad-column" style="padding:20px;background:#f9f9f9;" contenteditable="true">Column 1 content</div><div class="asad-column" style="padding:20px;background:#f9f9f9;" contenteditable="true">Column 2 content</div></div>'
        ),
        'hero' => array(
            'name' => 'Hero Section',
            'icon' => 'dashicons-cover-image',
            'template' => '<div class="asad-hero" style="padding:80px 20px;text-align:center;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;"><h1 contenteditable="true" style="font-size:48px;margin-bottom:20px;">Welcome to Our Site</h1><p contenteditable="true" style="font-size:20px;margin-bottom:30px;">Create amazing pages with our drag-and-drop builder</p><a href="#" class="asad-button" style="display:inline-block;padding:15px 40px;background:#fff;color:#667eea;text-decoration:none;border-radius:5px;font-weight:bold;">Get Started</a></div>'
        ),
        'testimonial' => array(
            'name' => 'Testimonial',
            'icon' => 'dashicons-format-quote',
            'template' => '<div class="asad-testimonial" style="padding:30px;background:#f9f9f9;border-left:4px solid #3498db;"><p contenteditable="true" style="font-size:18px;font-style:italic;margin-bottom:15px;">"This is an amazing product! Highly recommend it to everyone."</p><p contenteditable="true" style="font-weight:bold;">- John Doe, CEO</p></div>'
        ),
        'features' => array(
            'name' => 'Feature Grid',
            'icon' => 'dashicons-grid-view',
            'template' => '<div class="asad-features" style="display:grid;grid-template-columns:repeat(3,1fr);gap:30px;"><div class="asad-feature" style="text-align:center;padding:30px;background:#f9f9f9;border-radius:8px;"><span class="dashicons dashicons-star-filled" style="font-size:48px;color:#3498db;"></span><h3 contenteditable="true">Feature 1</h3><p contenteditable="true">Description of feature 1</p></div><div class="asad-feature" style="text-align:center;padding:30px;background:#f9f9f9;border-radius:8px;"><span class="dashicons dashicons-heart" style="font-size:48px;color:#e74c3c;"></span><h3 contenteditable="true">Feature 2</h3><p contenteditable="true">Description of feature 2</p></div><div class="asad-feature" style="text-align:center;padding:30px;background:#f9f9f9;border-radius:8px;"><span class="dashicons dashicons-awards" style="font-size:48px;color:#f39c12;"></span><h3 contenteditable="true">Feature 3</h3><p contenteditable="true">Description of feature 3</p></div></div>'
        ),
        'cta' => array(
            'name' => 'Call to Action',
            'icon' => 'dashicons-megaphone',
            'template' => '<div class="asad-cta" style="padding:60px 40px;text-align:center;background:#2c3e50;color:#fff;border-radius:8px;"><h2 contenteditable="true" style="margin-bottom:20px;">Ready to Get Started?</h2><p contenteditable="true" style="font-size:18px;margin-bottom:30px;">Join thousands of satisfied customers today</p><a href="#" class="asad-button" style="display:inline-block;padding:15px 40px;background:#3498db;color:#fff;text-decoration:none;border-radius:5px;font-weight:bold;">Start Now</a></div>'
        ),
        'spacer' => array(
            'name' => 'Spacer',
            'icon' => 'dashicons-minus',
            'template' => '<div class="asad-spacer" style="height:50px;"></div>'
        ),
        'divider' => array(
            'name' => 'Divider',
            'icon' => 'dashicons-editor-removeformatting',
            'template' => '<hr class="asad-divider" style="border:none;border-top:2px solid #e0e0e0;margin:30px 0;">'
        ),
        'video' => array(
            'name' => 'Video',
            'icon' => 'dashicons-video-alt3',
            'template' => '<div class="asad-video" style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;"><iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0" allowfullscreen></iframe></div>'
        )
    );
}

/**
 * Save Page Builder data
 */
function asad_save_page_builder($post_id) {
    // Check nonce
    if (!isset($_POST['asad_page_builder_nonce']) || !wp_verify_nonce($_POST['asad_page_builder_nonce'], 'asad_page_builder_save')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save enabled status
    $enabled = isset($_POST['asad_page_builder_enabled']) ? '1' : '0';
    update_post_meta($post_id, '_asad_page_builder_enabled', $enabled);

    // Save page builder data
    if (isset($_POST['asad_page_builder_data'])) {
        $data = wp_kses_post($_POST['asad_page_builder_data']);
        update_post_meta($post_id, '_asad_page_builder_data', $data);
    }
}
add_action('save_post', 'asad_save_page_builder');

/**
 * Render page builder content on frontend
 */
function asad_render_page_builder_content($content) {
    global $post;

    if (!$post) {
        return $content;
    }

    $page_builder_enabled = get_post_meta($post->ID, '_asad_page_builder_enabled', true);

    if ($page_builder_enabled === '1') {
        $page_builder_data = get_post_meta($post->ID, '_asad_page_builder_data', true);

        if (!empty($page_builder_data)) {
            return '<div class="asad-page-builder-content">' . $page_builder_data . '</div>';
        }
    }

    return $content;
}
add_filter('the_content', 'asad_render_page_builder_content');

/**
 * Enqueue Page Builder admin scripts
 */
function asad_enqueue_page_builder_admin_scripts($hook) {
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }

    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_script('jquery-ui-droppable');

    wp_add_inline_script('jquery-ui-sortable', "
        jQuery(document).ready(function($) {
            // Make blocks draggable
            $('.asad-pb-block-item').draggable({
                helper: 'clone',
                connectToSortable: '.asad-pb-canvas-area',
                start: function() {
                    $('.asad-pb-empty').hide();
                }
            });

            // Make canvas droppable and sortable
            $('.asad-pb-canvas-area').droppable({
                accept: '.asad-pb-block-item',
                drop: function(event, ui) {
                    var blockType = ui.draggable.data('block-type');
                    addBlockToCanvas(blockType);
                    ui.draggable.remove();
                }
            }).sortable({
                placeholder: 'asad-pb-block-placeholder',
                cursor: 'move'
            });

            // Add block to canvas
            function addBlockToCanvas(blockType) {
                var blocks = " . json_encode(asad_get_page_builder_blocks()) . ";
                var block = blocks[blockType];

                if (!block) return;

                var blockHtml = '<div class=\"asad-pb-block\" data-block-type=\"' + blockType + '\">' +
                    '<div class=\"asad-pb-block-controls\">' +
                    '<button type=\"button\" class=\"asad-pb-block-edit\">Edit</button>' +
                    '<button type=\"button\" class=\"asad-pb-block-delete\">Delete</button>' +
                    '</div>' +
                    block.template +
                    '</div>';

                $('.asad-pb-canvas-area').append(blockHtml);
                updatePageBuilderData();
            }

            // Block selection
            $(document).on('click', '.asad-pb-block', function(e) {
                e.stopPropagation();
                $('.asad-pb-block').removeClass('selected');
                $(this).addClass('selected');
            });

            // Delete block
            $(document).on('click', '.asad-pb-block-delete', function(e) {
                e.stopPropagation();
                if (confirm('Delete this block?')) {
                    $(this).closest('.asad-pb-block').remove();
                    updatePageBuilderData();
                }
            });

            // Update hidden field with page builder data
            function updatePageBuilderData() {
                var content = $('.asad-pb-canvas-area').html();
                $('#asad_page_builder_data').val(content);
            }

            // Update data on any change
            $(document).on('input', '.asad-pb-canvas-area', function() {
                updatePageBuilderData();
            });

            // Toggle page builder
            $('input[name=\"asad_page_builder_enabled\"]').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.asad-pb-container').show();
                } else {
                    $('.asad-pb-container').hide();
                }
            });

            // Save data before form submit
            $('form#post').on('submit', function() {
                updatePageBuilderData();
            });
        });
    ");
}
add_action('admin_enqueue_scripts', 'asad_enqueue_page_builder_admin_scripts');

/**
 * Add Page Builder templates
 */
function asad_get_page_builder_templates() {
    return array(
        'landing' => array(
            'name' => 'Landing Page',
            'blocks' => array('hero', 'features', 'testimonial', 'cta')
        ),
        'about' => array(
            'name' => 'About Page',
            'blocks' => array('heading', 'paragraph', 'image', 'columns')
        ),
        'services' => array(
            'name' => 'Services Page',
            'blocks' => array('heading', 'features', 'divider', 'cta')
        )
    );
}

/**
 * AJAX: Load page builder template
 */
function asad_ajax_load_pb_template() {
    check_ajax_referer('asad_pb_nonce', 'nonce');

    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Insufficient permissions');
    }

    $template_id = sanitize_text_field($_POST['template'] ?? '');
    $templates = asad_get_page_builder_templates();

    if (!isset($templates[$template_id])) {
        wp_send_json_error('Template not found');
    }

    $template = $templates[$template_id];
    $blocks = asad_get_page_builder_blocks();
    $content = '';

    foreach ($template['blocks'] as $block_type) {
        if (isset($blocks[$block_type])) {
            $content .= '<div class="asad-pb-block" data-block-type="' . $block_type . '">' .
                '<div class="asad-pb-block-controls">' .
                '<button type="button" class="asad-pb-block-edit">Edit</button>' .
                '<button type="button" class="asad-pb-block-delete">Delete</button>' .
                '</div>' .
                $blocks[$block_type]['template'] .
                '</div>';
        }
    }

    wp_send_json_success(array('content' => $content));
}
add_action('wp_ajax_asad_load_pb_template', 'asad_ajax_load_pb_template');
