<?php
/**
 * The template for displaying single posts
 *
 * @package Asad_Portfolio_Manager
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="container">
            <?php
            while (have_posts()) :
                the_post();
                ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <header class="entry-header">
                        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>

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
                            <?php if (has_tag()) : ?>
                                <span class="tags-links">
                                    <i class="far fa-tags"></i>
                                    <?php the_tags('', ', '); ?>
                                </span>
                            <?php endif; ?>
                        </div><!-- .entry-meta -->
                    </header><!-- .entry-header -->

                    <div class="entry-content">
                        <?php
                        the_content();

                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . __('Pages:', 'asad-portfolio'),
                            'after'  => '</div>',
                        ));
                        ?>
                    </div><!-- .entry-content -->

                    <footer class="entry-footer">
                        <?php
                        // Post navigation
                        the_post_navigation(array(
                            'prev_text' => '<span class="nav-subtitle">' . __('Previous:', 'asad-portfolio') . '</span> <span class="nav-title">%title</span>',
                            'next_text' => '<span class="nav-subtitle">' . __('Next:', 'asad-portfolio') . '</span> <span class="nav-title">%title</span>',
                        ));
                        ?>
                    </footer><!-- .entry-footer -->
                </article><!-- #post-<?php the_ID(); ?> -->

                <?php
                // If comments are open or we have at least one comment, load up the comment template.
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;

            endwhile; // End of the loop.
            ?>
        </div><!-- .container -->
    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
