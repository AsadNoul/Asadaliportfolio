<?php
/**
 * The main template file
 *
 * @package Asad_Portfolio_Manager
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="container">
            <?php
            if (have_posts()) :

                if (is_home() && !is_front_page()) :
                    ?>
                    <header>
                        <h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
                    </header>
                    <?php
                endif;

                // Start the Loop
                ?>
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
                                    <?php
                                    if (is_singular()) :
                                        the_title('<h1 class="entry-title">', '</h1>');
                                    else :
                                        the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
                                    endif;
                                    ?>

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
                // Pagination
                the_posts_pagination(array(
                    'prev_text' => '<i class="fas fa-chevron-left"></i> ' . __('Previous', 'asad-portfolio'),
                    'next_text' => __('Next', 'asad-portfolio') . ' <i class="fas fa-chevron-right"></i>',
                ));

            else :
                ?>
                <div class="no-results">
                    <h1><?php _e('Nothing Found', 'asad-portfolio'); ?></h1>
                    <p><?php _e('It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'asad-portfolio'); ?></p>
                    <?php get_search_form(); ?>
                </div>
                <?php
            endif;
            ?>
        </div><!-- .container -->
    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
