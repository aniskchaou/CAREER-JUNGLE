<?php
/**
 * The header for our theme.
 *
 * @package Careerfy
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <?php
    wp_head();
    ?>
</head>

<body <?php body_class(); ?> <?php echo apply_filters('careerfy_theme_body_tag_atts', '') ?>>
<?php echo apply_filters('careerfy_theme_after_body_tag_html', '') ?>
<!--// Main Wrapper \\-->
<div class="careerfy-wrapper">
<?php
if (!(class_exists('Careerfy_MMC') && true == Careerfy_MMC::is_construction_mode_enabled(false))) {
    global $careerfy_framework_options;
    $header_style = isset($careerfy_framework_options['header-style']) ? $careerfy_framework_options['header-style'] : '';

    $careerfy__options = careerfy_framework_options();
    $careerfy_loader = isset($careerfy__options['careerfy-site-loader']) ? $careerfy__options['careerfy-site-loader'] : '';
    if ($careerfy_loader == 'on') { ?>
        <div class="careerfy-loading-section">
            <div class="line-scale-pulse-out">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    <?php } ?>
    <!--// Header \\-->
    <header id="careerfy-header" class="<?php echo careerfy_header_class() ?>">
        <?php
        // header section
        do_action('careerfy_header_section');
        ?>
    </header>
    <?php do_action('careerfy_header_after_html');

    do_action('header_advance_search');

    do_action('header_navigation_style_twelve');

    do_action('careerfy_header_breadcrumbs');
}
