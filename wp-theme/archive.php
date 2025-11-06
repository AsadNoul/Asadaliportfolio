<?php
/**
 * The template for displaying archive pages
 *
 * @package Asad_Portfolio_Manager
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="container">
            <?php if (have_posts()) : ?>

                <header class="page-header archive-header">
                    <?php
                    the_archive_title('<h1 class="page-title">', '</h1>');
                    the_archive_description('<div class="archive-description">', '</div>');
                    ?>

                    <div class="archive-meta">
                        <?php
                        global $wp_query;
                        printf(
                            _n(
                                '%s post',
                                '%s posts',
                                $wp_query->found_posts,
                                'asad-portfolio'
                            ),
                            '<strong>' . number_format_i18n($wp_query->found_posts) . '</strong>'
                        );
                        ?>
                    </div>
                </header><!-- .page-header -->

                <div class="posts-grid">
                    <?php
                    while (have_posts()) :
                        the_post();
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="post-content">
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
                                        <?php if (has_category()) : ?>
                                            <span class="cat-links">
                                                <i class="far fa-folder"></i>
                                                <?php the_category(', '); ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="comments-link">
                                            <i class="far fa-comments"></i>
                                            <?php comments_number(__('No Comments', 'asad-portfolio'), __('1 Comment', 'asad-portfolio'), __('% Comments', 'asad-portfolio')); ?>
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
                    'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'asad-portfolio') . ' </span>',
                ));

            else :
                ?>
                <div class="no-results">
                    <h1><?php _e('Nothing Found', 'asad-portfolio'); ?></h1>
                    <p><?php _e('It seems we can\'t find what you\'re looking for.', 'asad-portfolio'); ?></p>
                    <?php get_search_form(); ?>
                </div>
            <?php endif; ?>
        </div><!-- .container -->
    </main><!-- #main -->
</div><!-- #primary -->

<style>
.archive-header {
    text-align: center;
    padding: 2rem 0;
    margin-bottom: 3rem;
    border-bottom: 2px solid var(--primary-color);
}

.archive-description {
    margin-top: 1rem;
    color: var(--text-color);
    opacity: 0.8;
}

.archive-meta {
    margin-top: 1rem;
    padding: 0.75rem 1.5rem;
    background: var(--primary-color);
    color: #fff;
    border-radius: 4px;
    display: inline-block;
}
</style>

<?php
get_sidebar();
get_footer();
