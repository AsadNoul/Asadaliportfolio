<?php
/**
 * The template for displaying pages
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
                    <header class="entry-header">
                        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                    </header><!-- .entry-header -->

                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="entry-content">
                        <?php
                        the_content();

                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . __('Pages:', 'asad-portfolio'),
                            'after'  => '</div>',
                        ));
                        ?>
                    </div><!-- .entry-content -->

                    <?php if (comments_open() || get_comments_number()) : ?>
                        <footer class="entry-footer">
                            <?php comments_template(); ?>
                        </footer><!-- .entry-footer -->
                    <?php endif; ?>
                </article><!-- #post-<?php the_ID(); ?> -->

                <?php
            endwhile; // End of the loop.
            ?>
        </div><!-- .container -->
    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
