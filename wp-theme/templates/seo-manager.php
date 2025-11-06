<?php
/**
 * SEO Manager Template
 *
 * @package Asad_Portfolio_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

$stats = asad_get_performance_stats();
?>

<div class="wrap asad-seo-manager">
    <h1><?php _e('SEO Manager', 'asad-portfolio'); ?></h1>
    <p class="description"><?php _e('Optimize your website for search engines and improve your rankings.', 'asad-portfolio'); ?></p>

    <div class="seo-dashboard">
        <div class="seo-stat-card">
            <div class="stat-icon" style="background: #3498db;">
                <i class="fas fa-file-alt fa-2x"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['total_posts']; ?></h3>
                <p><?php _e('Published Posts', 'asad-portfolio'); ?></p>
            </div>
        </div>

        <div class="seo-stat-card">
            <div class="stat-icon" style="background: #2ecc71;">
                <i class="fas fa-file fa-2x"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['total_pages']; ?></h3>
                <p><?php _e('Published Pages', 'asad-portfolio'); ?></p>
            </div>
        </div>

        <div class="seo-stat-card">
            <div class="stat-icon" style="background: #9b59b6;">
                <i class="fas fa-images fa-2x"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['total_media']; ?></h3>
                <p><?php _e('Media Files', 'asad-portfolio'); ?></p>
            </div>
        </div>

        <div class="seo-stat-card">
            <div class="stat-icon" style="background: #e74c3c;">
                <i class="fas fa-sitemap fa-2x"></i>
            </div>
            <div class="stat-content">
                <h3><?php _e('Generate', 'asad-portfolio'); ?></h3>
                <p><?php _e('XML Sitemap', 'asad-portfolio'); ?></p>
                <button class="button button-primary button-small" id="generateSitemapBtn">
                    <i class="fas fa-sync"></i> <?php _e('Generate', 'asad-portfolio'); ?>
                </button>
            </div>
        </div>
    </div>

    <div class="seo-sections">
        <!-- Sitemap Section -->
        <div class="seo-section">
            <h2><i class="fas fa-sitemap"></i> <?php _e('XML Sitemap', 'asad-portfolio'); ?></h2>
            <p><?php _e('Generate an XML sitemap to help search engines discover and index your content.', 'asad-portfolio'); ?></p>

            <div class="sitemap-info" id="sitemapInfo">
                <?php
                $upload_dir = wp_upload_dir();
                $sitemap_file = $upload_dir['basedir'] . '/sitemap.xml';
                if (file_exists($sitemap_file)) {
                    $sitemap_url = $upload_dir['baseurl'] . '/sitemap.xml';
                    ?>
                    <div class="notice notice-success">
                        <p>
                            <strong><?php _e('Sitemap exists!', 'asad-portfolio'); ?></strong><br>
                            <a href="<?php echo esc_url($sitemap_url); ?>" target="_blank"><?php echo esc_url($sitemap_url); ?></a>
                            <button class="button button-small copy-url" data-url="<?php echo esc_url($sitemap_url); ?>">
                                <i class="fas fa-copy"></i> <?php _e('Copy URL', 'asad-portfolio'); ?>
                            </button>
                        </p>
                    </div>
                    <p><?php _e('Last generated:', 'asad-portfolio'); ?> <?php echo date('F j, Y g:i a', filemtime($sitemap_file)); ?></p>
                <?php } else { ?>
                    <div class="notice notice-warning">
                        <p><?php _e('No sitemap found. Click the button above to generate one.', 'asad-portfolio'); ?></p>
                    </div>
                <?php } ?>
            </div>

            <h3><?php _e('Submit Sitemap to Search Engines', 'asad-portfolio'); ?></h3>
            <ul class="sitemap-submit-links">
                <li>
                    <i class="fab fa-google"></i>
                    <strong>Google Search Console:</strong>
                    <a href="https://search.google.com/search-console" target="_blank">
                        <?php _e('Submit to Google', 'asad-portfolio'); ?>
                    </a>
                </li>
                <li>
                    <i class="fab fa-microsoft"></i>
                    <strong>Bing Webmaster Tools:</strong>
                    <a href="https://www.bing.com/webmasters" target="_blank">
                        <?php _e('Submit to Bing', 'asad-portfolio'); ?>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Meta Tags Section -->
        <div class="seo-section">
            <h2><i class="fas fa-tags"></i> <?php _e('Default Meta Tags', 'asad-portfolio'); ?></h2>
            <p><?php _e('Set default meta tags for your website. These can be overridden on individual posts/pages.', 'asad-portfolio'); ?></p>

            <table class="form-table">
                <tr>
                    <th><label for="default_meta_description"><?php _e('Default Meta Description', 'asad-portfolio'); ?></label></th>
                    <td>
                        <textarea id="default_meta_description" name="default_meta_description" class="large-text" rows="3" maxlength="160"><?php echo esc_textarea(get_option('asad_default_meta_description', get_bloginfo('description'))); ?></textarea>
                        <p class="description"><?php _e('Used when no custom description is set.', 'asad-portfolio'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="default_og_image"><?php _e('Default OG Image', 'asad-portfolio'); ?></label></th>
                    <td>
                        <input type="url" id="default_og_image" name="default_og_image" value="<?php echo esc_url(get_option('asad_default_og_image', '')); ?>" class="regular-text">
                        <button type="button" class="button upload-default-og-image"><?php _e('Upload Image', 'asad-portfolio'); ?></button>
                        <p class="description"><?php _e('Default image for social media shares.', 'asad-portfolio'); ?></p>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <button class="button button-primary" id="saveDefaultMetaBtn">
                    <i class="fas fa-save"></i> <?php _e('Save Default Meta Tags', 'asad-portfolio'); ?>
                </button>
            </p>
        </div>

        <!-- Schema Markup Section -->
        <div class="seo-section">
            <h2><i class="fas fa-code"></i> <?php _e('Schema Markup', 'asad-portfolio'); ?></h2>
            <p><?php _e('Schema markup helps search engines understand your content better.', 'asad-portfolio'); ?></p>

            <div class="schema-status">
                <div class="status-item">
                    <i class="fas fa-check-circle" style="color: #2ecc71;"></i>
                    <strong><?php _e('Article Schema:', 'asad-portfolio'); ?></strong> <?php _e('Enabled for blog posts', 'asad-portfolio'); ?>
                </div>
                <div class="status-item">
                    <i class="fas fa-check-circle" style="color: #2ecc71;"></i>
                    <strong><?php _e('Organization Schema:', 'asad-portfolio'); ?></strong> <?php _e('Enabled for website', 'asad-portfolio'); ?>
                </div>
            </div>

            <h3><?php _e('Test Your Schema', 'asad-portfolio'); ?></h3>
            <p>
                <a href="https://search.google.com/test/rich-results" target="_blank" class="button">
                    <i class="fas fa-external-link-alt"></i> <?php _e('Google Rich Results Test', 'asad-portfolio'); ?>
                </a>
                <a href="https://validator.schema.org/" target="_blank" class="button">
                    <i class="fas fa-external-link-alt"></i> <?php _e('Schema.org Validator', 'asad-portfolio'); ?>
                </a>
            </p>
        </div>

        <!-- SEO Tips Section -->
        <div class="seo-section">
            <h2><i class="fas fa-lightbulb"></i> <?php _e('SEO Tips & Best Practices', 'asad-portfolio'); ?></h2>

            <div class="seo-tips">
                <div class="tip-card">
                    <div class="tip-icon"><i class="fas fa-heading"></i></div>
                    <h4><?php _e('Optimize Titles', 'asad-portfolio'); ?></h4>
                    <p><?php _e('Keep titles under 60 characters and include your target keyword near the beginning.', 'asad-portfolio'); ?></p>
                </div>

                <div class="tip-card">
                    <div class="tip-icon"><i class="fas fa-align-left"></i></div>
                    <h4><?php _e('Write Meta Descriptions', 'asad-portfolio'); ?></h4>
                    <p><?php _e('Compelling descriptions (150-160 chars) improve click-through rates from search results.', 'asad-portfolio'); ?></p>
                </div>

                <div class="tip-card">
                    <div class="tip-icon"><i class="fas fa-image"></i></div>
                    <h4><?php _e('Optimize Images', 'asad-portfolio'); ?></h4>
                    <p><?php _e('Use descriptive filenames, add alt text, and compress images for faster loading.', 'asad-portfolio'); ?></p>
                </div>

                <div class="tip-card">
                    <div class="tip-icon"><i class="fas fa-link"></i></div>
                    <h4><?php _e('Internal Linking', 'asad-portfolio'); ?></h4>
                    <p><?php _e('Link related content together to help search engines understand your site structure.', 'asad-portfolio'); ?></p>
                </div>

                <div class="tip-card">
                    <div class="tip-icon"><i class="fas fa-mobile-alt"></i></div>
                    <h4><?php _e('Mobile-Friendly', 'asad-portfolio'); ?></h4>
                    <p><?php _e('Ensure your site works perfectly on mobile devices. Google uses mobile-first indexing.', 'asad-portfolio'); ?></p>
                </div>

                <div class="tip-card">
                    <div class="tip-icon"><i class="fas fa-tachometer-alt"></i></div>
                    <h4><?php _e('Page Speed', 'asad-portfolio'); ?></h4>
                    <p><?php _e('Fast loading times improve user experience and search rankings. Use Performance Optimizer!', 'asad-portfolio'); ?></p>
                </div>
            </div>
        </div>

        <!-- Robots.txt Section -->
        <div class="seo-section">
            <h2><i class="fas fa-robot"></i> <?php _e('Robots.txt', 'asad-portfolio'); ?></h2>
            <p><?php _e('Your robots.txt file tells search engines which pages to crawl.', 'asad-portfolio'); ?></p>

            <p>
                <a href="<?php echo home_url('/robots.txt'); ?>" target="_blank" class="button">
                    <i class="fas fa-external-link-alt"></i> <?php _e('View robots.txt', 'asad-portfolio'); ?>
                </a>
            </p>

            <p class="description">
                <?php _e('The robots.txt file is managed by WordPress. You can edit it using the Settings > Reading page.', 'asad-portfolio'); ?>
            </p>
        </div>
    </div>
</div>

<style>
.asad-seo-manager {
    margin-top: 20px;
}

.seo-dashboard {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 30px 0;
}

.seo-stat-card {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
}

.stat-content h3 {
    font-size: 2rem;
    margin: 0;
}

.stat-content p {
    margin: 5px 0 0;
    color: #666;
}

.seo-section {
    background: #fff;
    padding: 25px;
    margin-bottom: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.seo-section h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 2px solid #2271b1;
}

.seo-section h2 i {
    margin-right: 10px;
    color: #2271b1;
}

.sitemap-submit-links {
    list-style: none;
    padding: 0;
}

.sitemap-submit-links li {
    padding: 15px;
    margin-bottom: 10px;
    background: #f9f9f9;
    border-left: 4px solid #2271b1;
    border-radius: 4px;
}

.sitemap-submit-links i {
    margin-right: 10px;
    color: #2271b1;
    font-size: 1.2em;
}

.schema-status {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 5px;
    margin: 20px 0;
}

.status-item {
    padding: 10px 0;
    font-size: 14px;
}

.status-item i {
    margin-right: 10px;
    font-size: 1.2em;
}

.seo-tips {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.tip-card {
    padding: 20px;
    background: #f9f9f9;
    border-radius: 5px;
    border-left: 4px solid #2271b1;
}

.tip-icon {
    width: 50px;
    height: 50px;
    background: #2271b1;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5em;
    margin-bottom: 15px;
}

.tip-card h4 {
    margin: 10px 0;
}

.tip-card p {
    margin: 0;
    color: #666;
    font-size: 14px;
}

.copy-url {
    margin-left: 10px;
}

@media (max-width: 768px) {
    .seo-dashboard {
        grid-template-columns: 1fr;
    }

    .seo-tips {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Generate Sitemap
    $('#generateSitemapBtn').on('click', function() {
        const button = $(this);
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> <?php _e('Generating...', 'asad-portfolio'); ?>');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_generate_sitemap',
                nonce: '<?php echo wp_create_nonce('asad-admin-nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            },
            complete: function() {
                button.prop('disabled', false).html('<i class="fas fa-sync"></i> <?php _e('Generate', 'asad-portfolio'); ?>');
            }
        });
    });

    // Copy URL
    $('.copy-url').on('click', function() {
        const url = $(this).data('url');
        navigator.clipboard.writeText(url).then(function() {
            alert('<?php _e('URL copied to clipboard!', 'asad-portfolio'); ?>');
        });
    });

    // Save Default Meta
    $('#saveDefaultMetaBtn').on('click', function() {
        const button = $(this);
        const description = $('#default_meta_description').val();
        const ogImage = $('#default_og_image').val();

        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> <?php _e('Saving...', 'asad-portfolio'); ?>');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'update_option',
                nonce: '<?php echo wp_create_nonce('asad-admin-nonce'); ?>',
                option_name: 'asad_default_meta_description',
                option_value: description
            },
            success: function() {
                alert('<?php _e('Settings saved!', 'asad-portfolio'); ?>');
            },
            complete: function() {
                button.prop('disabled', false).html('<i class="fas fa-save"></i> <?php _e('Save Default Meta Tags', 'asad-portfolio'); ?>');
            }
        });
    });

    // Upload OG Image
    $('.upload-default-og-image').on('click', function(e) {
        e.preventDefault();
        const button = $(this);
        const custom_uploader = wp.media({
            title: '<?php _e('Select Image', 'asad-portfolio'); ?>',
            button: { text: '<?php _e('Use this image', 'asad-portfolio'); ?>' },
            multiple: false
        }).on('select', function() {
            const attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#default_og_image').val(attachment.url);
        }).open();
    });
});
</script>
