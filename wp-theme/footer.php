    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
        <div class="container">
            <?php if (is_active_sidebar('footer-1') || is_active_sidebar('footer-2') || is_active_sidebar('footer-3')) : ?>
                <div class="footer-widgets" style="grid-template-columns: repeat(<?php echo get_theme_mod('asad_footer_columns', '3'); ?>, 1fr);">
                    <?php if (is_active_sidebar('footer-1')) : ?>
                        <div class="footer-widget-area">
                            <?php dynamic_sidebar('footer-1'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (is_active_sidebar('footer-2')) : ?>
                        <div class="footer-widget-area">
                            <?php dynamic_sidebar('footer-2'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (is_active_sidebar('footer-3')) : ?>
                        <div class="footer-widget-area">
                            <?php dynamic_sidebar('footer-3'); ?>
                        </div>
                    <?php endif; ?>
                </div><!-- .footer-widgets -->
            <?php endif; ?>

            <div class="footer-bottom">
                <div class="social-links">
                    <?php
                    $social_networks = array(
                        'facebook'  => 'fab fa-facebook-f',
                        'twitter'   => 'fab fa-twitter',
                        'instagram' => 'fab fa-instagram',
                        'linkedin'  => 'fab fa-linkedin-in',
                        'github'    => 'fab fa-github',
                        'youtube'   => 'fab fa-youtube',
                    );

                    foreach ($social_networks as $network => $icon) {
                        $url = get_theme_mod("asad_social_{$network}", '');
                        if (!empty($url)) {
                            echo '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer" aria-label="' . ucfirst($network) . '">';
                            echo '<i class="' . esc_attr($icon) . '"></i>';
                            echo '</a>';
                        }
                    }
                    ?>
                </div><!-- .social-links -->

                <div class="site-info">
                    <?php
                    $footer_text = get_theme_mod('asad_footer_text', '&copy; ' . date('Y') . ' ' . get_bloginfo('name') . '. All rights reserved.');
                    echo wp_kses_post($footer_text);
                    ?>
                </div><!-- .site-info -->
            </div><!-- .footer-bottom -->
        </div><!-- .container -->
    </footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
