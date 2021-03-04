<?php

/**
 * visual composer actions
 * @config
 */
/**
 * list all hooks adding
 * @return hooks
 */
add_action('vc_before_init', 'careerfy_row_add_view_param');
add_action('vc_before_init', 'careerfy_row_add_overlay_param');
add_action('vc_before_init', 'careerfy_row_add_overlay_color');
add_action('vc_before_init', 'careerfy_wc_products_add_params');
//add_action('vc_before_init', 'careerfy_def_row_parallax_params');

/**
 * adding extra fields to row element
 * @return markup
 */
function careerfy_row_add_view_param() {

    if (function_exists('vc_add_param')) { 
        $attributes = array('type' => 'css_editor', 'heading' => __('Css', "careerfy-frame"), 'param_name' => 'css', 'group' => __('Design options', "careerfy-frame"));
        vc_add_param('careerfy_fancy_heading', $attributes);
    }
    $attributes = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Row View", "careerfy-frame"),
        'param_name' => 'careerfy_container',
        'value' => array(esc_html__("Box", "careerfy-frame") => 'box', esc_html__("Wide", "careerfy-frame") => 'wide'),
        'description' => esc_html__("Choose row view as Box or full Wide as per page width. This option will work only for 'Pages' in case of wide view.", "careerfy-frame")
    );

    if (function_exists('vc_add_param')) {
        vc_add_param('vc_row', $attributes);
    }
}

function careerfy_row_add_overlay_param() {

    $attributes = array(
        'type' => 'dropdown',
        'heading' => esc_html__("Row Overlay", "careerfy-frame"),
        'param_name' => 'careerfy_overlay',
        'value' => array(esc_html__("No", "careerfy-frame") => 'no', esc_html__("Yes", "careerfy-frame") => 'yes'),
        'description' => '',
    );

    if (function_exists('vc_add_param')) {
        vc_add_param('vc_row', $attributes);
    }
}

function careerfy_row_add_overlay_color() {

    $attributes = array(
        'type' => 'colorpicker',
        'heading' => esc_html__("Overlay Color", "careerfy-frame"),
        'param_name' => 'careerfy_overlay_color',
        'value' => '',
        'description' => '',
    );

    if (function_exists('vc_add_param')) {
        vc_add_param('vc_row', $attributes);
    }
}

/**
 * adding extra param in vc
 * @return markup
 */
function careerfy_add_param_field($param_name, $param_field) {
    if (function_exists('vc_add_shortcode_param')) {
        vc_add_shortcode_param($param_name, $param_field);
    }
}

/**
 * adding extra fields to woocommerce lists element
 * @return markup
 */
function careerfy_wc_products_add_params() {

    $attributes = array(
        'type' => 'dropdown',
        'weight' => '99',
        'heading' => esc_html__("Products View", "careerfy-frame"),
        'param_name' => 'columns',
        'value' => array(esc_html__("Grid", "careerfy-frame") => 'grid', esc_html__("List", "careerfy-frame") => 'list'),
        'description' => esc_html__("Choose Products view as Grid or List.", "careerfy-frame")
    );

    if (function_exists('vc_add_param')) {
        vc_remove_param('products', 'columns');
        vc_add_param('products', $attributes);
    }
}

/**
 * changing vc row parallax elements
 * @return markup
 */
function careerfy_def_row_parallax_params() {

    $attributes = array(
        'type' => 'careerfy_browse_img',
        'heading' => esc_html__("Parallax Image", "careerfy-frame"),
        'param_name' => 'parallax_image',
        'value' => '',
        'description' => esc_html__("Browse Image for Parallax.", "careerfy-frame"),
        'weight' => 8,
    );

    if (function_exists('vc_add_param')) {
        vc_remove_param('vc_row', 'parallax');
        vc_remove_param('vc_row', 'parallax_speed_bg');
        vc_add_param('vc_row', $attributes);
    }
}
