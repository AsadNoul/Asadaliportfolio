<?php
/**
 * SEO Manager
 *
 * @package Asad_Portfolio_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add SEO Meta Box to Posts and Pages
 */
function asad_add_seo_meta_box() {
    $post_types = array('post', 'page', 'portfolio');
    foreach ($post_types as $post_type) {
        add_meta_box(
            'asad_seo_meta_box',
            __('SEO Settings', 'asad-portfolio'),
            'asad_seo_meta_box_callback',
            $post_type,
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'asad_add_seo_meta_box');

/**
 * SEO Meta Box Callback
 */
function asad_seo_meta_box_callback($post) {
    wp_nonce_field('asad_seo_meta_box', 'asad_seo_meta_box_nonce');

    $meta_title = get_post_meta($post->ID, '_asad_meta_title', true);
    $meta_description = get_post_meta($post->ID, '_asad_meta_description', true);
    $meta_keywords = get_post_meta($post->ID, '_asad_meta_keywords', true);
    $og_image = get_post_meta($post->ID, '_asad_og_image', true);
    $canonical_url = get_post_meta($post->ID, '_asad_canonical_url', true);
    $robots_index = get_post_meta($post->ID, '_asad_robots_index', true);
    $robots_follow = get_post_meta($post->ID, '_asad_robots_follow', true);

    ?>
    <div class="asad-seo-meta-box">
        <div class="seo-field">
            <label for="asad_meta_title">
                <strong><?php _e('Meta Title', 'asad-portfolio'); ?></strong>
                <span class="char-count" id="title-count">0 / 60</span>
            </label>
            <input type="text" id="asad_meta_title" name="asad_meta_title" value="<?php echo esc_attr($meta_title); ?>" class="widefat" maxlength="60">
            <p class="description"><?php _e('Recommended: 50-60 characters', 'asad-portfolio'); ?></p>
        </div>

        <div class="seo-field">
            <label for="asad_meta_description">
                <strong><?php _e('Meta Description', 'asad-portfolio'); ?></strong>
                <span class="char-count" id="desc-count">0 / 160</span>
            </label>
            <textarea id="asad_meta_description" name="asad_meta_description" class="widefat" rows="3" maxlength="160"><?php echo esc_textarea($meta_description); ?></textarea>
            <p class="description"><?php _e('Recommended: 150-160 characters', 'asad-portfolio'); ?></p>
        </div>

        <div class="seo-field">
            <label for="asad_meta_keywords"><strong><?php _e('Focus Keywords', 'asad-portfolio'); ?></strong></label>
            <input type="text" id="asad_meta_keywords" name="asad_meta_keywords" value="<?php echo esc_attr($meta_keywords); ?>" class="widefat">
            <p class="description"><?php _e('Comma-separated keywords (e.g., web design, portfolio, freelancer)', 'asad-portfolio'); ?></p>
        </div>

        <div class="seo-field">
            <label for="asad_og_image"><strong><?php _e('Social Media Image (Open Graph)', 'asad-portfolio'); ?></strong></label>
            <input type="url" id="asad_og_image" name="asad_og_image" value="<?php echo esc_url($og_image); ?>" class="widefat">
            <button type="button" class="button upload-og-image"><?php _e('Upload Image', 'asad-portfolio'); ?></button>
            <p class="description"><?php _e('Image for Facebook, Twitter, LinkedIn shares. Recommended: 1200x630px', 'asad-portfolio'); ?></p>
            <?php if ($og_image) : ?>
                <img src="<?php echo esc_url($og_image); ?>" style="max-width: 300px; margin-top: 10px;">
            <?php endif; ?>
        </div>

        <div class="seo-field">
            <label for="asad_canonical_url"><strong><?php _e('Canonical URL', 'asad-portfolio'); ?></strong></label>
            <input type="url" id="asad_canonical_url" name="asad_canonical_url" value="<?php echo esc_url($canonical_url); ?>" class="widefat">
            <p class="description"><?php _e('Leave blank to use default permalink', 'asad-portfolio'); ?></p>
        </div>

        <div class="seo-field">
            <label><strong><?php _e('Robots Meta', 'asad-portfolio'); ?></strong></label>
            <p>
                <label>
                    <input type="checkbox" name="asad_robots_index" value="1" <?php checked($robots_index, '1'); ?>>
                    <?php _e('No Index (Hide from search engines)', 'asad-portfolio'); ?>
                </label>
            </p>
            <p>
                <label>
                    <input type="checkbox" name="asad_robots_follow" value="1" <?php checked($robots_follow, '1'); ?>>
                    <?php _e('No Follow (Don\'t follow links)', 'asad-portfolio'); ?>
                </label>
            </p>
        </div>

        <div class="seo-score">
            <h4><?php _e('SEO Score', 'asad-portfolio'); ?></h4>
            <div class="seo-score-bar">
                <div class="score-indicator" id="seo-score-indicator"></div>
            </div>
            <p class="score-text" id="seo-score-text"><?php _e('Fill in the fields to see your SEO score', 'asad-portfolio'); ?></p>
        </div>
    </div>

    <style>
        .asad-seo-meta-box .seo-field {
            margin-bottom: 20px;
        }
        .asad-seo-meta-box .char-count {
            float: right;
            font-size: 12px;
            color: #666;
        }
        .seo-score {
            margin-top: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .seo-score-bar {
            height: 20px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        .score-indicator {
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, #e74c3c, #f39c12, #2ecc71);
            transition: width 0.3s;
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        // Character count
        function updateCharCount(input, countElement) {
            var count = input.val().length;
            var max = input.attr('maxlength');
            countElement.text(count + ' / ' + max);
        }

        $('#asad_meta_title').on('input', function() {
            updateCharCount($(this), $('#title-count'));
            calculateSEOScore();
        });

        $('#asad_meta_description').on('input', function() {
            updateCharCount($(this), $('#desc-count'));
            calculateSEOScore();
        });

        // Initial count
        updateCharCount($('#asad_meta_title'), $('#title-count'));
        updateCharCount($('#asad_meta_description'), $('#desc-count'));

        // Calculate SEO Score
        function calculateSEOScore() {
            var score = 0;
            var title = $('#asad_meta_title').val();
            var desc = $('#asad_meta_description').val();
            var keywords = $('#asad_meta_keywords').val();
            var ogImage = $('#asad_og_image').val();

            if (title.length >= 50 && title.length <= 60) score += 25;
            else if (title.length > 0) score += 15;

            if (desc.length >= 150 && desc.length <= 160) score += 25;
            else if (desc.length > 0) score += 15;

            if (keywords.length > 0) score += 25;
            if (ogImage.length > 0) score += 25;

            $('#seo-score-indicator').css('width', score + '%');

            var scoreText = '';
            if (score >= 80) scoreText = '<?php _e('Excellent!', 'asad-portfolio'); ?>';
            else if (score >= 60) scoreText = '<?php _e('Good', 'asad-portfolio'); ?>';
            else if (score >= 40) scoreText = '<?php _e('Fair', 'asad-portfolio'); ?>';
            else scoreText = '<?php _e('Needs Improvement', 'asad-portfolio'); ?>';

            $('#seo-score-text').text(scoreText + ' (' + score + '/100)');
        }

        calculateSEOScore();

        // Upload OG Image
        $('.upload-og-image').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var custom_uploader = wp.media({
                title: '<?php _e('Select Image', 'asad-portfolio'); ?>',
                button: { text: '<?php _e('Use this image', 'asad-portfolio'); ?>' },
                multiple: false
            }).on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $('#asad_og_image').val(attachment.url);
                button.after('<img src="' + attachment.url + '" style="max-width: 300px; margin-top: 10px;">');
                calculateSEOScore();
            }).open();
        });
    });
    </script>
    <?php
}

/**
 * Save SEO Meta Data
 */
function asad_save_seo_meta_data($post_id) {
    if (!isset($_POST['asad_seo_meta_box_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['asad_seo_meta_box_nonce'], 'asad_seo_meta_box')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = array(
        'asad_meta_title',
        'asad_meta_description',
        'asad_meta_keywords',
        'asad_og_image',
        'asad_canonical_url',
        'asad_robots_index',
        'asad_robots_follow',
    );

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        } else {
            delete_post_meta($post_id, '_' . $field);
        }
    }
}
add_action('save_post', 'asad_save_seo_meta_data');

/**
 * Output SEO Meta Tags
 */
function asad_output_seo_meta_tags() {
    if (is_singular()) {
        global $post;

        $meta_title = get_post_meta($post->ID, '_asad_meta_title', true);
        $meta_description = get_post_meta($post->ID, '_asad_meta_description', true);
        $meta_keywords = get_post_meta($post->ID, '_asad_meta_keywords', true);
        $og_image = get_post_meta($post->ID, '_asad_og_image', true);
        $canonical_url = get_post_meta($post->ID, '_asad_canonical_url', true);
        $robots_index = get_post_meta($post->ID, '_asad_robots_index', true);
        $robots_follow = get_post_meta($post->ID, '_asad_robots_follow', true);

        // Title
        if ($meta_title) {
            echo '<meta name="title" content="' . esc_attr($meta_title) . '">' . "\n";
        }

        // Description
        if ($meta_description) {
            echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
        }

        // Keywords
        if ($meta_keywords) {
            echo '<meta name="keywords" content="' . esc_attr($meta_keywords) . '">' . "\n";
        }

        // Robots
        $robots = array();
        if ($robots_index) $robots[] = 'noindex';
        if ($robots_follow) $robots[] = 'nofollow';
        if (!empty($robots)) {
            echo '<meta name="robots" content="' . implode(', ', $robots) . '">' . "\n";
        }

        // Canonical
        $canonical = $canonical_url ? $canonical_url : get_permalink();
        echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";

        // Open Graph
        $title = $meta_title ? $meta_title : get_the_title();
        $description = $meta_description ? $meta_description : wp_trim_words(get_the_excerpt(), 20);
        $image = $og_image ? $og_image : (has_post_thumbnail() ? get_the_post_thumbnail_url($post, 'large') : '');

        echo '<meta property="og:type" content="article">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url($canonical) . '">' . "\n";
        if ($image) {
            echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
        }
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";

        // Twitter Card
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
        if ($image) {
            echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
        }
    }
}
add_action('wp_head', 'asad_output_seo_meta_tags', 1);

/**
 * Generate XML Sitemap
 */
function asad_generate_sitemap() {
    if (!current_user_can('manage_options')) {
        return false;
    }

    $posts = get_posts(array(
        'post_type' => array('post', 'page', 'portfolio'),
        'post_status' => 'publish',
        'numberposts' => -1,
    ));

    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // Homepage
    $sitemap .= '<url>' . "\n";
    $sitemap .= '<loc>' . esc_url(home_url('/')) . '</loc>' . "\n";
    $sitemap .= '<changefreq>daily</changefreq>' . "\n";
    $sitemap .= '<priority>1.0</priority>' . "\n";
    $sitemap .= '</url>' . "\n";

    // Posts and Pages
    foreach ($posts as $post) {
        $sitemap .= '<url>' . "\n";
        $sitemap .= '<loc>' . esc_url(get_permalink($post)) . '</loc>' . "\n";
        $sitemap .= '<lastmod>' . mysql2date('Y-m-d', $post->post_modified) . '</lastmod>' . "\n";
        $sitemap .= '<changefreq>weekly</changefreq>' . "\n";
        $sitemap .= '<priority>0.8</priority>' . "\n";
        $sitemap .= '</url>' . "\n";
    }

    $sitemap .= '</urlset>';

    // Save to file
    $upload_dir = wp_upload_dir();
    $sitemap_file = $upload_dir['basedir'] . '/sitemap.xml';
    file_put_contents($sitemap_file, $sitemap);

    return $upload_dir['baseurl'] . '/sitemap.xml';
}

/**
 * AJAX: Generate Sitemap
 */
function asad_ajax_generate_sitemap() {
    check_ajax_referer('asad-admin-nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => __('Permission denied.', 'asad-portfolio')));
    }

    $sitemap_url = asad_generate_sitemap();

    if ($sitemap_url) {
        wp_send_json_success(array(
            'message' => __('Sitemap generated successfully!', 'asad-portfolio'),
            'url' => $sitemap_url
        ));
    } else {
        wp_send_json_error(array('message' => __('Failed to generate sitemap.', 'asad-portfolio')));
    }
}
add_action('wp_ajax_asad_generate_sitemap', 'asad_ajax_generate_sitemap');

/**
 * Add Schema.org Markup
 */
function asad_add_schema_markup() {
    if (is_singular('post')) {
        global $post;
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title(),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author(),
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_site_icon_url(),
                ),
            ),
        );

        if (has_post_thumbnail()) {
            $schema['image'] = get_the_post_thumbnail_url($post, 'large');
        }

        echo '<script type="application/ld+json">' . json_encode($schema) . '</script>' . "\n";
    }
}
add_action('wp_head', 'asad_add_schema_markup');
