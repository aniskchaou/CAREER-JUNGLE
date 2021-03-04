<?php

/**
 * Register Backend and Frontend CSS Styles
 */
add_action('vc_base_register_front_css', 'careerfy_vc_iconpicker_base_register_css');

//add_action('vc_base_register_admin_css', 'careerfy_vc_iconpicker_base_register_css');

function careerfy_vc_iconpicker_base_register_css()
{
    wp_register_style('careerfy-flaticon', careerfy_framework_get_url('icons-manager/assets/default/style.css'), array(), Careerfy_framework::get_version());
}

/**
 * Enqueue Backend and Frontend CSS Styles
 */
//add_action('vc_backend_editor_enqueue_js_css', 'careerfy_vc_iconpicker_editor_jscss');
add_action('vc_frontend_editor_enqueue_js_css', 'careerfy_vc_iconpicker_editor_jscss');

function careerfy_vc_iconpicker_editor_jscss()
{
    wp_enqueue_style('careerfy-flaticon');
}

/**
 * Enqueue CSS in Frontend when it's used
 */
//add_action('vc_enqueue_font_icon_element', 'careerfy_enqueue_font_flaticon');

function careerfy_enqueue_font_flaticon($font)
{
    switch ($font) {
        case 'flaticon':
            wp_enqueue_style('flaticon');
    }
}

/**
 * Define the Icons for VC Iconpicker
 */
add_filter('vc_iconpicker-type-fontawesome', 'careerfy_vc_iconpicker_type_flaticon');

function careerfy_vc_iconpicker_type_flaticon($icons)
{
    global $wp_filesystem;
    require_once ABSPATH . '/wp-admin/includes/file.php';

    if (false === ($creds = request_filesystem_credentials(wp_nonce_url('post.php'), '', false, false, array()))) {
        return true;
    }
    if (!WP_Filesystem($creds)) {
        request_filesystem_credentials(wp_nonce_url('post.php'), '', true, false, array());
        return true;
    }

    $icons_selection_file = careerfy_framework_get_path('icons-manager/assets/default/selection.json');

    $cachetime = 900;
    $transient = 'careerfy_site_icons_cache';

    $cus_icons_arr = array();

    $check_transient = get_transient($transient);
    if (!empty($check_transient)) {
        $saved_data = get_option('careerfy_site_icons_arr');
        if (!empty($saved_data)) {
            $cus_icons_arr = $saved_data;
        }
    } else {
        if (is_file($icons_selection_file)) {
            $get_json_data = $wp_filesystem->get_contents($icons_selection_file);

            $get_json_data = json_decode($get_json_data, true);

            if (isset($get_json_data['icons']) && !empty($get_json_data['icons'])) {
                $sd = 1;
                foreach ($get_json_data['icons'] as $icon_data) {
                    if (isset($icon_data['properties']['name'])) {
                        $cus_icons_arr[] = array('careerfy-icon careerfy-' . $icon_data['properties']['name'] => $icon_data['properties']['name']);
                        $sd++;
                    }
                }

                set_transient($transient, true, $cachetime);
                update_option('careerfy_site_icons_arr', $cus_icons_arr);
            }
        }
    }

    $flaticon_icons = array(
        'Careerfy Flaticon' => $cus_icons_arr,
    );

    $flaticon_icons = apply_filters('careerfy_vc_custom_icons_list_arr', $flaticon_icons);

    return array_merge($icons, $flaticon_icons);
}
