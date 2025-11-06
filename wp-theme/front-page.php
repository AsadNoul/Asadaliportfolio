<?php
/**
 * The front page template file
 *
 * @package Asad_Portfolio_Manager
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main front-page">

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="hero-content">
                    <h1 class="hero-title animate-on-scroll">
                        <?php
                        $hero_title = get_theme_mod('asad_hero_title', get_bloginfo('name'));
                        echo esc_html($hero_title);
                        ?>
                    </h1>
                    <p class="hero-description animate-on-scroll">
                        <?php
                        $hero_description = get_theme_mod('asad_hero_description', get_bloginfo('description'));
                        echo esc_html($hero_description);
                        ?>
                    </p>
                    <div class="hero-buttons animate-on-scroll">
                        <a href="#portfolio" class="btn btn-primary btn-lg">
                            <i class="fas fa-briefcase"></i> <?php _e('View Portfolio', 'asad-portfolio'); ?>
                        </a>
                        <a href="#contact" class="btn btn-secondary btn-lg">
                            <i class="fas fa-envelope"></i> <?php _e('Get In Touch', 'asad-portfolio'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Portfolio Section -->
        <section id="portfolio" class="portfolio-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title"><?php _e('Latest Projects', 'asad-portfolio'); ?></h2>
                    <p class="section-description"><?php _e('Check out my recent work', 'asad-portfolio'); ?></p>
                </div>

                <?php
                $portfolio_query = new WP_Query(array(
                    'post_type' => 'portfolio',
                    'posts_per_page' => 6,
                ));

                if ($portfolio_query->have_posts()) :
                    ?>
                    <div class="portfolio-grid">
                        <?php
                        while ($portfolio_query->have_posts()) :
                            $portfolio_query->the_post();
                            ?>
                            <div class="portfolio-item animate-on-scroll">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="portfolio-thumbnail">
                                        <?php the_post_thumbnail('large'); ?>
                                        <div class="portfolio-overlay">
                                            <a href="<?php the_permalink(); ?>" class="portfolio-link">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="portfolio-content">
                                    <h3 class="portfolio-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    <div class="portfolio-excerpt">
                                        <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>

                    <div class="view-all-projects">
                        <a href="<?php echo get_post_type_archive_link('portfolio'); ?>" class="btn btn-primary">
                            <?php _e('View All Projects', 'asad-portfolio'); ?> <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                <?php else : ?>
                    <p><?php _e('No portfolio items found.', 'asad-portfolio'); ?></p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Latest Blog Posts -->
        <section class="blog-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title"><?php _e('Latest Blog Posts', 'asad-portfolio'); ?></h2>
                    <p class="section-description"><?php _e('Read my latest articles', 'asad-portfolio'); ?></p>
                </div>

                <?php
                $blog_query = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 3,
                ));

                if ($blog_query->have_posts()) :
                    ?>
                    <div class="posts-grid">
                        <?php
                        while ($blog_query->have_posts()) :
                            $blog_query->the_post();
                            ?>
                            <article class="post-item animate-on-scroll">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="post-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <div class="post-content">
                                    <header class="entry-header">
                                        <?php the_title('<h3 class="entry-title"><a href="' . esc_url(get_permalink()) . '">', '</a></h3>'); ?>
                                        <div class="entry-meta">
                                            <span class="posted-on">
                                                <i class="far fa-calendar"></i> <?php echo get_the_date(); ?>
                                            </span>
                                        </div>
                                    </header>
                                    <div class="entry-summary">
                                        <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                    </div>
                                    <footer class="entry-footer">
                                        <a href="<?php the_permalink(); ?>" class="btn btn-read-more">
                                            <?php _e('Read More', 'asad-portfolio'); ?> <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </footer>
                                </div>
                            </article>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>

                    <div class="view-all-posts">
                        <a href="<?php echo get_permalink(get_option('page_for_posts')); ?>" class="btn btn-primary">
                            <?php _e('View All Posts', 'asad-portfolio'); ?> <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

    </main><!-- #main -->
</div><!-- #primary -->

<style>
/* Hero Section */
.hero-section {
    padding: 6rem 0;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: #fff;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"><path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="rgba(255,255,255,0.1)"/></svg>') no-repeat bottom;
    background-size: cover;
    opacity: 0.1;
}

.hero-content {
    position: relative;
    z-index: 1;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    line-height: 1.2;
}

.hero-description {
    font-size: 1.5rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.hero-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-lg {
    padding: 1rem 2rem;
    font-size: 1.1rem;
}

/* Section Styles */
.portfolio-section,
.blog-section {
    padding: 5rem 0;
}

.section-header {
    text-align: center;
    margin-bottom: 3rem;
}

.section-title {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.section-description {
    font-size: 1.2rem;
    color: var(--text-color);
    opacity: 0.7;
}

/* Portfolio Grid */
.portfolio-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px var(--shadow-color);
    transition: transform 0.3s, box-shadow 0.3s;
}

.portfolio-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 25px var(--shadow-color);
}

.portfolio-thumbnail {
    position: relative;
    overflow: hidden;
    height: 250px;
}

.portfolio-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.portfolio-item:hover .portfolio-thumbnail img {
    transform: scale(1.1);
}

.portfolio-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(52, 152, 219, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
}

.portfolio-item:hover .portfolio-overlay {
    opacity: 1;
}

.portfolio-link {
    width: 60px;
    height: 60px;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--primary-color);
}

.view-all-projects,
.view-all-posts {
    text-align: center;
    margin-top: 3rem;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }

    .hero-description {
        font-size: 1.1rem;
    }

    .section-title {
        font-size: 2rem;
    }

    .hero-buttons {
        flex-direction: column;
        align-items: center;
    }

    .btn-lg {
        width: 100%;
        max-width: 300px;
    }
}
</style>

<?php
get_footer();
