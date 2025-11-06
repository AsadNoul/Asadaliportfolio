<?php
/**
 * Image Optimization & CDN
 * Automatic image compression, WebP conversion, lazy loading
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get image optimization settings
 */
function asad_get_image_settings() {
    return get_option('asad_image_settings', array(
        'auto_compress' => true,
        'compression_quality' => 80,
        'convert_webp' => true,
        'lazy_loading' => true,
        'max_width' => 2000,
        'max_height' => 2000,
        'cdn_enabled' => false,
        'cdn_url' => '',
        'remove_metadata' => true
    ));
}

/**
 * Update image settings
 */
function asad_update_image_settings($settings) {
    return update_option('asad_image_settings', $settings);
}

/**
 * Compress image on upload
 */
function asad_compress_image_on_upload($file) {
    $settings = asad_get_image_settings();

    if (!$settings['auto_compress']) {
        return $file;
    }

    $image_path = $file['file'];
    $image_type = $file['type'];

    // Only process images
    if (!in_array($image_type, array('image/jpeg', 'image/jpg', 'image/png', 'image/gif'))) {
        return $file;
    }

    // Compress image
    $compressed = asad_compress_image($image_path, $settings['compression_quality']);

    if ($compressed) {
        // Convert to WebP if enabled
        if ($settings['convert_webp']) {
            asad_create_webp_version($image_path);
        }
    }

    return $file;
}
add_filter('wp_handle_upload', 'asad_compress_image_on_upload');

/**
 * Compress image file
 */
function asad_compress_image($file_path, $quality = 80) {
    $info = getimagesize($file_path);

    if ($info === false) {
        return false;
    }

    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg':
        case 'image/jpg':
            $image = imagecreatefromjpeg($file_path);
            if ($image) {
                imagejpeg($image, $file_path, $quality);
                imagedestroy($image);
                return true;
            }
            break;

        case 'image/png':
            $image = imagecreatefrompng($file_path);
            if ($image) {
                // PNG compression level (0-9)
                $png_quality = round(9 - ($quality / 100) * 9);
                imagepng($image, $file_path, $png_quality);
                imagedestroy($image);
                return true;
            }
            break;

        case 'image/gif':
            // GIFs are usually already optimized
            return true;
    }

    return false;
}

/**
 * Create WebP version of image
 */
function asad_create_webp_version($file_path) {
    if (!function_exists('imagewebp')) {
        return false;
    }

    $info = getimagesize($file_path);

    if ($info === false) {
        return false;
    }

    $mime = $info['mime'];
    $webp_path = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $file_path);

    switch ($mime) {
        case 'image/jpeg':
        case 'image/jpg':
            $image = imagecreatefromjpeg($file_path);
            break;

        case 'image/png':
            $image = imagecreatefrompng($file_path);
            break;

        case 'image/gif':
            $image = imagecreatefromgif($file_path);
            break;

        default:
            return false;
    }

    if ($image) {
        $result = imagewebp($image, $webp_path, 80);
        imagedestroy($image);
        return $result;
    }

    return false;
}

/**
 * Add lazy loading to images
 */
function asad_add_lazy_loading($content) {
    $settings = asad_get_image_settings();

    if (!$settings['lazy_loading']) {
        return $content;
    }

    // Add loading="lazy" to img tags
    $content = preg_replace('/<img(.*?)src=/i', '<img$1loading="lazy" src=', $content);

    return $content;
}
add_filter('the_content', 'asad_add_lazy_loading', 999);

/**
 * Replace image URLs with CDN URLs
 */
function asad_replace_with_cdn($content) {
    $settings = asad_get_image_settings();

    if (!$settings['cdn_enabled'] || empty($settings['cdn_url'])) {
        return $content;
    }

    $upload_dir = wp_upload_dir();
    $base_url = $upload_dir['baseurl'];
    $cdn_url = rtrim($settings['cdn_url'], '/');

    $content = str_replace($base_url, $cdn_url, $content);

    return $content;
}
add_filter('the_content', 'asad_replace_with_cdn', 1000);

/**
 * Bulk optimize existing images
 */
function asad_bulk_optimize_images($limit = 50, $offset = 0) {
    $args = array(
        'post_type' => 'attachment',
        'post_mime_type' => array('image/jpeg', 'image/jpg', 'image/png'),
        'post_status' => 'inherit',
        'posts_per_page' => $limit,
        'offset' => $offset
    );

    $attachments = get_posts($args);
    $settings = asad_get_image_settings();

    $optimized = 0;
    $failed = 0;

    foreach ($attachments as $attachment) {
        $file_path = get_attached_file($attachment->ID);

        if (file_exists($file_path)) {
            $result = asad_compress_image($file_path, $settings['compression_quality']);

            if ($result) {
                $optimized++;

                // Create WebP version
                if ($settings['convert_webp']) {
                    asad_create_webp_version($file_path);
                }
            } else {
                $failed++;
            }
        }
    }

    return array(
        'optimized' => $optimized,
        'failed' => $failed,
        'total' => count($attachments)
    );
}

/**
 * Find unused images
 */
function asad_find_unused_images() {
    global $wpdb;

    // Get all image attachments
    $all_images = $wpdb->get_col(
        "SELECT ID FROM {$wpdb->posts}
         WHERE post_type = 'attachment'
         AND post_mime_type LIKE 'image/%'"
    );

    $unused = array();

    foreach ($all_images as $image_id) {
        // Check if image is used in any post
        $is_used = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_content LIKE %s
             OR post_excerpt LIKE %s",
            '%' . $wpdb->esc_like(wp_get_attachment_url($image_id)) . '%',
            '%' . $wpdb->esc_like(wp_get_attachment_url($image_id)) . '%'
        ));

        // Check if it's a featured image
        $is_featured = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->postmeta}
             WHERE meta_key = '_thumbnail_id'
             AND meta_value = %d",
            $image_id
        ));

        if (!$is_used && !$is_featured) {
            $unused[] = array(
                'id' => $image_id,
                'url' => wp_get_attachment_url($image_id),
                'size' => filesize(get_attached_file($image_id)),
                'date' => get_the_date('Y-m-d H:i:s', $image_id)
            );
        }
    }

    return $unused;
}

/**
 * Fix image dimensions
 */
function asad_fix_image_dimensions($attachment_id) {
    $file_path = get_attached_file($attachment_id);

    if (!file_exists($file_path)) {
        return false;
    }

    $info = getimagesize($file_path);

    if ($info === false) {
        return false;
    }

    // Update attachment metadata
    update_post_meta($attachment_id, '_wp_attachment_image_width', $info[0]);
    update_post_meta($attachment_id, '_wp_attachment_image_height', $info[1]);

    return true;
}

/**
 * Get image optimization stats
 */
function asad_get_image_stats() {
    global $wpdb;

    $stats = array();

    // Total images
    $stats['total_images'] = $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->posts}
         WHERE post_type = 'attachment'
         AND post_mime_type LIKE 'image/%'"
    );

    // Total size
    $attachments = $wpdb->get_col(
        "SELECT ID FROM {$wpdb->posts}
         WHERE post_type = 'attachment'
         AND post_mime_type LIKE 'image/%'"
    );

    $total_size = 0;
    foreach ($attachments as $id) {
        $file = get_attached_file($id);
        if (file_exists($file)) {
            $total_size += filesize($file);
        }
    }

    $stats['total_size'] = $total_size;
    $stats['total_size_formatted'] = size_format($total_size);

    return $stats;
}

/**
 * AJAX: Save image settings
 */
function asad_ajax_save_image_settings() {
    check_ajax_referer('asad_image_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $settings = array(
        'auto_compress' => isset($_POST['auto_compress']),
        'compression_quality' => intval($_POST['compression_quality'] ?? 80),
        'convert_webp' => isset($_POST['convert_webp']),
        'lazy_loading' => isset($_POST['lazy_loading']),
        'max_width' => intval($_POST['max_width'] ?? 2000),
        'max_height' => intval($_POST['max_height'] ?? 2000),
        'cdn_enabled' => isset($_POST['cdn_enabled']),
        'cdn_url' => esc_url_raw($_POST['cdn_url'] ?? ''),
        'remove_metadata' => isset($_POST['remove_metadata'])
    );

    asad_update_image_settings($settings);

    wp_send_json_success('Settings saved successfully');
}
add_action('wp_ajax_asad_save_image_settings', 'asad_ajax_save_image_settings');

/**
 * AJAX: Bulk optimize images
 */
function asad_ajax_bulk_optimize() {
    check_ajax_referer('asad_image_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    set_time_limit(300);

    $offset = intval($_POST['offset'] ?? 0);
    $result = asad_bulk_optimize_images(50, $offset);

    wp_send_json_success($result);
}
add_action('wp_ajax_asad_bulk_optimize', 'asad_ajax_bulk_optimize');

/**
 * AJAX: Find unused images
 */
function asad_ajax_find_unused() {
    check_ajax_referer('asad_image_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $unused = asad_find_unused_images();

    wp_send_json_success($unused);
}
add_action('wp_ajax_asad_find_unused', 'asad_ajax_find_unused');
