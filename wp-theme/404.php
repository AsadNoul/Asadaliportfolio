<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package Asad_Portfolio_Manager
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="container">
            <section class="error-404 not-found">
                <div class="error-404-content">
                    <div class="error-404-icon">
                        <i class="fas fa-exclamation-triangle fa-5x"></i>
                    </div>

                    <header class="page-header">
                        <h1 class="page-title"><?php _e('404 - Page Not Found', 'asad-portfolio'); ?></h1>
                    </header><!-- .page-header -->

                    <div class="page-content">
                        <p><?php _e('Oops! The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'asad-portfolio'); ?></p>

                        <div class="error-404-search">
                            <h3><?php _e('Try searching for what you need:', 'asad-portfolio'); ?></h3>
                            <?php get_search_form(); ?>
                        </div>

                        <div class="error-404-links">
                            <h3><?php _e('Helpful Links:', 'asad-portfolio'); ?></h3>
                            <ul>
                                <li><a href="<?php echo esc_url(home_url('/')); ?>"><i class="fas fa-home"></i> <?php _e('Homepage', 'asad-portfolio'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/blog')); ?>"><i class="fas fa-blog"></i> <?php _e('Blog', 'asad-portfolio'); ?></a></li>
                                <?php if (has_nav_menu('primary')) : ?>
                                    <?php
                                    $menu_items = wp_get_nav_menu_items(get_nav_menu_locations()['primary']);
                                    if ($menu_items && count($menu_items) > 0) :
                                        foreach (array_slice($menu_items, 0, 5) as $item) :
                                            ?>
                                            <li><a href="<?php echo esc_url($item->url); ?>"><i class="fas fa-arrow-right"></i> <?php echo esc_html($item->title); ?></a></li>
                                        <?php endforeach;
                                    endif;
                                    ?>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="error-404-button">
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> <?php _e('Back to Homepage', 'asad-portfolio'); ?>
                            </a>
                        </div>
                    </div><!-- .page-content -->
                </div>
            </section><!-- .error-404 -->
        </div><!-- .container -->
    </main><!-- #main -->
</div><!-- #primary -->

<style>
.error-404-content {
    text-align: center;
    padding: 3rem 0;
}

.error-404-icon {
    color: var(--accent-color);
    margin-bottom: 2rem;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.error-404 .page-title {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.error-404-search,
.error-404-links {
    margin: 3rem 0;
    padding: 2rem;
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 8px;
}

.error-404-links ul {
    list-style: none;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.error-404-links li a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: var(--primary-color);
    color: #fff;
    border-radius: 4px;
    transition: all 0.3s;
}

.error-404-links li a:hover {
    background: var(--secondary-color);
    transform: translateX(5px);
}

.error-404-button {
    margin-top: 2rem;
}
</style>

<?php
get_footer();
