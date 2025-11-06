<?php
/**
 * The template for displaying search results
 *
 * @package Asad_Portfolio_Manager
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="container">
            <header class="page-header">
                <h1 class="page-title">
                    <?php
                    printf(
                        __('Search Results for: %s', 'asad-portfolio'),
                        '<span>' . get_search_query() . '</span>'
                    );
                    ?>
                </h1>
                <div class="search-form-header">
                    <?php get_search_form(); ?>
                </div>
            </header><!-- .page-header -->

            <?php if (have_posts()) : ?>

                <div class="search-results-count">
                    <p>
                        <?php
                        global $wp_query;
                        printf(
                            _n(
                                'Found %s result',
                                'Found %s results',
                                $wp_query->found_posts,
                                'asad-portfolio'
                            ),
                            '<strong>' . number_format_i18n($wp_query->found_posts) . '</strong>'
                        );
                        ?>
                    </p>
                </div>

                <div class="posts-grid">
                    <?php
                    while (have_posts()) :
                        the_post();
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-item search-result'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="post-content">
                                <div class="post-type-badge">
                                    <i class="fas fa-<?php echo get_post_type() === 'post' ? 'file-alt' : (get_post_type() === 'page' ? 'file' : 'folder'); ?>"></i>
                                    <?php echo esc_html(get_post_type()); ?>
                                </div>

                                <header class="entry-header">
                                    <?php the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>'); ?>

                                    <div class="entry-meta">
                                        <span class="posted-on">
                                            <i class="far fa-calendar"></i>
                                            <time datetime="<?php echo get_the_date('c'); ?>">
                                                <?php echo get_the_date(); ?>
                                            </time>
                                        </span>
                                        <span class="byline">
                                            <i class="far fa-user"></i>
                                            <?php the_author(); ?>
                                        </span>
                                    </div><!-- .entry-meta -->
                                </header><!-- .entry-header -->

                                <div class="entry-summary">
                                    <?php the_excerpt(); ?>
                                </div><!-- .entry-summary -->

                                <footer class="entry-footer">
                                    <a href="<?php the_permalink(); ?>" class="btn btn-read-more">
                                        <?php _e('Read More', 'asad-portfolio'); ?>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </footer><!-- .entry-footer -->
                            </div><!-- .post-content -->
                        </article><!-- #post-<?php the_ID(); ?> -->
                        <?php
                    endwhile;
                    ?>
                </div><!-- .posts-grid -->

                <?php
                the_posts_pagination(array(
                    'prev_text' => '<i class="fas fa-chevron-left"></i> ' . __('Previous', 'asad-portfolio'),
                    'next_text' => __('Next', 'asad-portfolio') . ' <i class="fas fa-chevron-right"></i>',
                ));

            else :
                ?>

                <div class="no-results">
                    <div class="no-results-icon">
                        <i class="fas fa-search fa-3x"></i>
                    </div>
                    <h2><?php _e('Nothing Found', 'asad-portfolio'); ?></h2>
                    <p><?php _e('Sorry, but nothing matched your search terms. Please try again with different keywords.', 'asad-portfolio'); ?></p>

                    <div class="search-suggestions">
                        <h3><?php _e('Search Suggestions:', 'asad-portfolio'); ?></h3>
                        <ul>
                            <li><?php _e('Make sure all words are spelled correctly', 'asad-portfolio'); ?></li>
                            <li><?php _e('Try different keywords', 'asad-portfolio'); ?></li>
                            <li><?php _e('Try more general keywords', 'asad-portfolio'); ?></li>
                            <li><?php _e('Try fewer keywords', 'asad-portfolio'); ?></li>
                        </ul>
                    </div>

                    <div class="recent-posts-section">
                        <h3><?php _e('Recent Posts:', 'asad-portfolio'); ?></h3>
                        <ul>
                            <?php
                            $recent_posts = wp_get_recent_posts(array('numberposts' => 5));
                            foreach ($recent_posts as $post) :
                                ?>
                                <li>
                                    <a href="<?php echo get_permalink($post['ID']); ?>">
                                        <i class="fas fa-angle-right"></i> <?php echo $post['post_title']; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

            <?php endif; ?>
        </div><!-- .container -->
    </main><!-- #main -->
</div><!-- #primary -->

<style>
.search-form-header {
    margin: 1.5rem 0;
}

.search-results-count {
    margin: 2rem 0;
    padding: 1rem;
    background: var(--primary-color);
    color: #fff;
    border-radius: 4px;
    text-align: center;
}

.post-type-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: var(--secondary-color);
    color: #fff;
    font-size: 0.75rem;
    border-radius: 3px;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
}

.no-results {
    text-align: center;
    padding: 3rem 0;
}

.no-results-icon {
    color: var(--border-color);
    margin-bottom: 2rem;
}

.search-suggestions,
.recent-posts-section {
    margin: 2rem auto;
    max-width: 600px;
    text-align: left;
    padding: 2rem;
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 8px;
}

.search-suggestions ul,
.recent-posts-section ul {
    list-style: none;
    padding: 0;
}

.search-suggestions li,
.recent-posts-section li {
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border-color);
}

.recent-posts-section li a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-color);
}

.recent-posts-section li a:hover {
    color: var(--primary-color);
}
</style>

<?php
get_sidebar();
get_footer();
