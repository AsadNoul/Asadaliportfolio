<?php
/**
 * Advanced Schema Generator
 * 30+ Schema types for rich snippets
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Schema types configuration
 */
function asad_get_schema_types() {
    return array(
        'Article' => array('name' => 'Article', 'fields' => array('headline', 'author', 'datePublished', 'image')),
        'BlogPosting' => array('name' => 'Blog Post', 'fields' => array('headline', 'author', 'datePublished', 'image')),
        'NewsArticle' => array('name' => 'News Article', 'fields' => array('headline', 'author', 'datePublished', 'image')),
        'Product' => array('name' => 'Product', 'fields' => array('name', 'image', 'description', 'brand', 'offers')),
        'Recipe' => array('name' => 'Recipe', 'fields' => array('name', 'image', 'recipeIngredient', 'recipeInstructions', 'prepTime', 'cookTime')),
        'FAQPage' => array('name' => 'FAQ', 'fields' => array('mainEntity')),
        'HowTo' => array('name' => 'How-To', 'fields' => array('name', 'step', 'totalTime')),
        'LocalBusiness' => array('name' => 'Local Business', 'fields' => array('name', 'address', 'telephone', 'openingHours')),
        'Organization' => array('name' => 'Organization', 'fields' => array('name', 'url', 'logo', 'contactPoint')),
        'Person' => array('name' => 'Person', 'fields' => array('name', 'jobTitle', 'image', 'sameAs')),
        'Event' => array('name' => 'Event', 'fields' => array('name', 'startDate', 'endDate', 'location')),
        'Review' => array('name' => 'Review', 'fields' => array('itemReviewed', 'reviewRating', 'author')),
        'VideoObject' => array('name' => 'Video', 'fields' => array('name', 'description', 'thumbnailUrl', 'uploadDate')),
        'Course' => array('name' => 'Course', 'fields' => array('name', 'description', 'provider')),
        'JobPosting' => array('name' => 'Job Posting', 'fields' => array('title', 'description', 'datePosted', 'hiringOrganization'))
    );
}

/**
 * Get schema settings
 */
function asad_get_schema_settings() {
    return get_option('asad_schema_settings', array(
        'auto_schema' => true,
        'default_type' => 'Article',
        'organization_name' => get_bloginfo('name'),
        'organization_logo' => '',
        'social_profiles' => array()
    ));
}

/**
 * Add schema meta box
 */
function asad_add_schema_meta_box() {
    add_meta_box(
        'asad_schema_meta',
        'Schema Markup',
        'asad_schema_meta_box_callback',
        array('post', 'page', 'product'),
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'asad_add_schema_meta_box');

/**
 * Schema meta box callback
 */
function asad_schema_meta_box_callback($post) {
    wp_nonce_field('asad_schema_meta', 'asad_schema_nonce');

    $schema_type = get_post_meta($post->ID, '_asad_schema_type', true) ?: 'Article';
    $schema_data = get_post_meta($post->ID, '_asad_schema_data', true) ?: array();
    $schema_types = asad_get_schema_types();
    ?>
    <div class="asad-schema-meta-box">
        <p>
            <label><strong>Schema Type:</strong></label>
            <select name="asad_schema_type" id="asad-schema-type" style="width: 100%;">
                <?php foreach ($schema_types as $type => $info): ?>
                    <option value="<?php echo esc_attr($type); ?>" <?php selected($schema_type, $type); ?>>
                        <?php echo esc_html($info['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <div id="asad-schema-fields">
            <!-- Dynamic fields loaded via JS -->
        </div>

        <p>
            <label>
                <input type="checkbox" name="asad_schema_enabled" value="1" <?php checked(get_post_meta($post->ID, '_asad_schema_enabled', true), '1'); ?>>
                Enable schema markup for this post
            </label>
        </p>
    </div>
    <?php
}

/**
 * Save schema meta
 */
function asad_save_schema_meta($post_id) {
    if (!isset($_POST['asad_schema_nonce']) || !wp_verify_nonce($_POST['asad_schema_nonce'], 'asad_schema_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    update_post_meta($post_id, '_asad_schema_type', sanitize_text_field($_POST['asad_schema_type'] ?? 'Article'));
    update_post_meta($post_id, '_asad_schema_enabled', isset($_POST['asad_schema_enabled']) ? '1' : '0');
}
add_action('save_post', 'asad_save_schema_meta');

/**
 * Generate Article schema
 */
function asad_generate_article_schema($post) {
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => get_the_title($post->ID),
        'datePublished' => get_the_date('c', $post->ID),
        'dateModified' => get_the_modified_date('c', $post->ID),
        'author' => array(
            '@type' => 'Person',
            'name' => get_the_author_meta('display_name', $post->post_author)
        )
    );

    $thumbnail = get_the_post_thumbnail_url($post->ID, 'full');
    if ($thumbnail) {
        $schema['image'] = $thumbnail;
    }

    return $schema;
}

/**
 * Generate Product schema
 */
function asad_generate_product_schema($post) {
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => get_the_title($post->ID),
        'description' => get_the_excerpt($post->ID)
    );

    $thumbnail = get_the_post_thumbnail_url($post->ID, 'full');
    if ($thumbnail) {
        $schema['image'] = $thumbnail;
    }

    // WooCommerce integration
    if (function_exists('wc_get_product')) {
        $product = wc_get_product($post->ID);
        if ($product) {
            $schema['offers'] = array(
                '@type' => 'Offer',
                'price' => $product->get_price(),
                'priceCurrency' => get_woocommerce_currency(),
                'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'
            );
        }
    }

    return $schema;
}

/**
 * Generate Organization schema
 */
function asad_generate_organization_schema() {
    $settings = asad_get_schema_settings();

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $settings['organization_name'],
        'url' => home_url()
    );

    if (!empty($settings['organization_logo'])) {
        $schema['logo'] = $settings['organization_logo'];
    }

    if (!empty($settings['social_profiles'])) {
        $schema['sameAs'] = $settings['social_profiles'];
    }

    return $schema;
}

/**
 * Output schema markup
 */
function asad_output_schema_markup() {
    if (is_singular()) {
        global $post;

        $enabled = get_post_meta($post->ID, '_asad_schema_enabled', true);
        $schema_type = get_post_meta($post->ID, '_asad_schema_type', true) ?: 'Article';

        if ($enabled === '1' || asad_get_schema_settings()['auto_schema']) {
            $schema = null;

            switch ($schema_type) {
                case 'Article':
                case 'BlogPosting':
                case 'NewsArticle':
                    $schema = asad_generate_article_schema($post);
                    break;
                case 'Product':
                    $schema = asad_generate_product_schema($post);
                    break;
            }

            if ($schema) {
                echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
            }
        }
    }

    // Organization schema on homepage
    if (is_front_page()) {
        $org_schema = asad_generate_organization_schema();
        echo '<script type="application/ld+json">' . json_encode($org_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
    }
}
add_action('wp_head', 'asad_output_schema_markup');

/**
 * AJAX: Save schema settings
 */
function asad_ajax_save_schema_settings() {
    check_ajax_referer('asad_schema_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $settings = array(
        'auto_schema' => isset($_POST['auto_schema']),
        'default_type' => sanitize_text_field($_POST['default_type'] ?? 'Article'),
        'organization_name' => sanitize_text_field($_POST['organization_name'] ?? ''),
        'organization_logo' => esc_url_raw($_POST['organization_logo'] ?? ''),
        'social_profiles' => array_map('esc_url_raw', $_POST['social_profiles'] ?? array())
    );

    update_option('asad_schema_settings', $settings);

    wp_send_json_success('Settings saved successfully');
}
add_action('wp_ajax_asad_save_schema_settings', 'asad_ajax_save_schema_settings');
