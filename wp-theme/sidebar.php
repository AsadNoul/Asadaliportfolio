<?php
/**
 * The sidebar template
 *
 * @package Asad_Portfolio_Manager
 */

$sidebar_position = get_theme_mod('asad_sidebar_position', 'right');

if ($sidebar_position === 'none' || !is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside id="secondary" class="widget-area sidebar-<?php echo esc_attr($sidebar_position); ?>">
    <?php dynamic_sidebar('sidebar-1'); ?>
</aside><!-- #secondary -->
