<?php

/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Careerfy
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function careerfy_body_classes($classes) {
    global $careerfy_framework_options;
    
    $careerfy__options = careerfy_framework_options();
    $header_style = isset($careerfy_framework_options['header-style']) ? $careerfy_framework_options['header-style'] : '';
    $body_background_color = isset($careerfy__options['careerfy-body-color']) && $careerfy__options['careerfy-body-color'] != '' ? $careerfy__options['careerfy-body-color'] : '';
    
    $classes[] = 'careerfy-page-loading';
    
    if ($body_background_color != '#fff' && $body_background_color != '#ffffff' && $body_background_color != '') {
        $classes[] = 'body-nowhite-bg';
    }
    
    // Adds a class of group-blog to blogs with more than 1 published author.
    if (is_multi_author()) {
        $classes[] = 'group-blog';
    }

    // Adds a class of hfeed to non-singular pages.
    if (!is_singular()) {
        $classes[] = 'hfeed';
    }
    if($header_style == "style11"){
        $classes[] = 'body-eleven';
    }

    // Adds a class for theme testing.
    if (empty($careerfy_framework_options)) {
        $classes[] = 'careerfy-theme-unit';
    }

    if (function_exists('is_product') && is_product()) {
        $classes[] = 'careerfy-product-detail';
    }

    return $classes;
}

add_filter('body_class', 'careerfy_body_classes');
