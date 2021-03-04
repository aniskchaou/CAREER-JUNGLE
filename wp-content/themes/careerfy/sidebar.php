<?php
/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Careerfy
 */
if (is_single() || is_page()) {
    $layout = get_post_meta($post->ID, 'careerfy_field_post_layout', true);
    $sidebar = get_post_meta($post->ID, 'careerfy_field_post_sidebar', true);
} else {
    $careerfy__options = careerfy_framework_options();
    $layout = isset($careerfy__options['careerfy-default-layout']) ? $careerfy__options['careerfy-default-layout'] : '';
    $sidebar = isset($careerfy__options['careerfy-default-sidebar']) ? $careerfy__options['careerfy-default-sidebar'] : '';
}

$sidebar_class = $layout == 'left' ? 'pull-left' : 'pull-right';
if (is_active_sidebar('sidebar-1') && $layout == '') {
    ?>
    <aside class="col-md-3">
        <?php dynamic_sidebar('sidebar-1'); ?>
    </aside>
    <?php
} else if (( $layout == 'right' || $layout == 'left' )) {
    ?>
    <aside class="col-md-3 <?php echo sanitize_html_class($sidebar_class) ?>">
        <?php dynamic_sidebar($sidebar); ?>
    </aside>
    <?php
}
