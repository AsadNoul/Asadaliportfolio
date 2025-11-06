<!DOCTYPE html>
<html <?php language_attributes(); ?> data-theme="<?php echo get_theme_mod('asad_dark_mode', 'false') === 'true' ? 'dark' : 'light'; ?>">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary"><?php _e('Skip to content', 'asad-portfolio'); ?></a>

    <header id="masthead" class="site-header <?php echo get_theme_mod('asad_sticky_header', true) ? 'sticky-header' : ''; ?>">
        <div class="container">
            <div class="header-inner">
                <div class="site-branding">
                    <?php
                    if (has_custom_logo()) {
                        the_custom_logo();
                    } else {
                        ?>
                        <h1 class="site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                        <?php
                        $description = get_bloginfo('description', 'display');
                        if ($description || is_customize_preview()) :
                            ?>
                            <p class="site-description"><?php echo $description; ?></p>
                        <?php endif;
                    }
                    ?>
                </div><!-- .site-branding -->

                <nav id="site-navigation" class="main-navigation">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'fallback_cb'    => false,
                    ));
                    ?>
                </nav><!-- #site-navigation -->

                <div class="header-actions">
                    <?php if (get_theme_mod('asad_header_search', true)) : ?>
                        <button class="search-toggle" aria-label="<?php _e('Search', 'asad-portfolio'); ?>">
                            <i class="fas fa-search"></i>
                        </button>
                    <?php endif; ?>

                    <button class="dark-mode-toggle" id="darkModeToggle" aria-label="<?php _e('Toggle Dark Mode', 'asad-portfolio'); ?>">
                        <i class="fas fa-moon"></i>
                    </button>

                    <button class="mobile-menu-toggle" aria-label="<?php _e('Menu', 'asad-portfolio'); ?>">
                        <i class="fas fa-bars"></i>
                    </button>
                </div><!-- .header-actions -->
            </div><!-- .header-inner -->

            <?php if (get_theme_mod('asad_header_search', true)) : ?>
                <div class="header-search" style="display: none;">
                    <?php get_search_form(); ?>
                </div>
            <?php endif; ?>
        </div><!-- .container -->
    </header><!-- #masthead -->

    <div id="content" class="site-content">
