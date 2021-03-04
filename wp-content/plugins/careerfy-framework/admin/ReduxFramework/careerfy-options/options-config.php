<?php

defined('ABSPATH') || exit;

if (!class_exists('Redux')) {
    return;
}

// This is your option name where all the Redux data is stored.
$opt_name = 'careerfy_framework_options';

$theme = wp_get_theme();
$args = array(
    // This is where your data is stored in the database and also becomes your global variable name.
    'opt_name' => $opt_name,
    // Name that appears at the top of your panel.
    'display_name' => $theme->get('Name'),
    // Version that appears at the top of your panel.
    'display_version' => $theme->get('Version'),
    // Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only).
    'menu_type' => 'menu',
    // Show the sections below the admin menu item or not.
    'allow_sub_menu' => true,
    // The text to appear in the admin menu.
    'menu_title' => __('Theme Options', 'careerfy-frame'),
    // The text to appear on the page title.
    'page_title' => __('Theme Options', 'careerfy-frame'),
    // Enabled the Webfonts typography module to use asynchronous fonts.
    'async_typography' => false,
    // Disable to create your own google fonts loader.
    'disable_google_fonts_link' => false,
    // Show the panel pages on the admin bar.
    'admin_bar' => true,
    // Icon for the admin bar menu.
    'admin_bar_icon' => 'dashicons-portfolio',
    // Priority for the admin bar menu.
    'admin_bar_priority' => 50,
    // Sets a different name for your global variable other than the opt_name.
    'global_variable' => '',
    // Show the time the page took to load, etc (forced on while on localhost or when WP_DEBUG is enabled).
    'dev_mode' => true,
    // Enable basic customizer support.
    'customizer' => true,
    // Allow the panel to opened expanded.
    'open_expanded' => false,
    // Disable the save warning when a user changes a field.
    'disable_save_warn' => false,
    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
    'page_priority' => null,
    // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters.
    'page_parent' => 'themes.php',
    // Permissions needed to access the options panel.
    'page_permissions' => 'manage_options',
    // Specify a custom URL to an icon.
    'menu_icon' => '',
    // Force your panel to always open to a specific tab (by id).
    'last_tab' => '',
    // Icon displayed in the admin panel next to your menu_title.
    'page_icon' => 'icon-themes',
    // Page slug used to denote the panel, will be based off page title, then menu title, then opt_name if not provided.
    'page_slug' => $opt_name,
    // On load save the defaults to DB before user clicks save.
    'save_defaults' => true,
    // Display the default value next to each field when not set to the default value.
    'default_show' => false,
    // What to print by the field's title if the value shown is default.
    'default_mark' => '*',
    // Shows the Import/Export panel when not used as a field.
    'show_import_export' => true,
    // The time transinets will expire when the 'database' arg is set.
    'transient_time' => 60 * MINUTE_IN_SECONDS,
    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output.
    'output' => true,
    // Allows dynamic CSS to be generated for customizer and google fonts,
    // but stops the dynamic CSS from going to the page head.
    'output_tag' => true,
    // Disable the footer credit of Redux. Please leave if you can help it.
    'footer_credit' => '',
    // If you prefer not to use the CDN for ACE Editor.
    // You may download the Redux Vendor Support plugin to run locally or embed it in your code.
    'use_cdn' => true,
    // Set the theme of the option panel.  Use 'wp' to use a more modern style, default is classic.
    'admin_theme' => 'wp',
    // HINTS.
    'hints' => array(
        'icon' => 'el el-question-sign',
        'icon_position' => 'right',
        'icon_color' => 'lightgray',
        'icon_size' => 'normal',
        'tip_style' => array(
            'color' => 'red',
            'shadow' => true,
            'rounded' => false,
            'style' => '',
        ),
        'tip_position' => array(
            'my' => 'top left',
            'at' => 'bottom right',
        ),
        'tip_effect' => array(
            'show' => array(
                'effect' => 'slide',
                'duration' => '500',
                'event' => 'mouseover',
            ),
            'hide' => array(
                'effect' => 'slide',
                'duration' => '500',
                'event' => 'click mouseleave',
            ),
        ),
    ),
    // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
    // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
    'database' => '',
    'network_admin' => true,
);

$redux_class = new Redux;

if (class_exists('Redux') && method_exists($redux_class, 'set_args')) {
    Redux::set_args($opt_name, $args);

    $gen_opts_atts = array();
    $gen_opts_atts[] = array(
        'id' => 'careerfy-site-logo',
        'type' => 'media',
        'url' => true,
        'title' => __('Site Logo', 'careerfy-frame'),
        'compiler' => 'true',
        'desc' => __('Site Logo media uploader.', 'careerfy-frame'),
        'subtitle' => __('Site Logo media uploader.', 'careerfy-frame'),
        'default' => array('url' => trailingslashit(get_template_directory_uri()) . 'images/logo.png'),
    );
    if (function_exists('icl_get_languages') && function_exists('wpml_init_language_switcher')) {
        global $sitepress;
        $wpml_all_langs = icl_get_languages();
        if (!empty($wpml_all_langs)) {
            $default_lang = $sitepress->get_default_language();
            foreach ($wpml_all_langs as $lang_code => $wpml_lang) {
                if ($lang_code != $default_lang) {
                    $lang_name = isset($wpml_lang['native_name']) ? $wpml_lang['native_name'] : $lang_code;
                    $gen_opts_atts[] = array(
                        'id' => 'careerfy_site_logo_' . $lang_code,
                        'type' => 'media',
                        'url' => true,
                        'title' => sprintf(__('Site Logo for %s Language', 'careerfy-frame'), $lang_name),
                        'compiler' => 'true',
                        'desc' => '',
                        'subtitle' => sprintf(__('Site Logo media uploader for %s Language.', 'careerfy-frame'), $lang_name),
                        'default' => array('url' => trailingslashit(get_template_directory_uri()) . 'images/logo.png'),
                    );
                }
            }
        }
    }
    $gen_opts_atts[] = array(
        'id' => 'careerfy-logo-width',
        'type' => 'slider',
        'title' => __('Logo Width', 'careerfy-frame'),
        'subtitle' => __('Set Logo Width', 'careerfy-frame'),
        'desc' => __('Set Logo Width in (px)', 'careerfy-frame'),
        "default" => 0,
        "min" => 0,
        "step" => 1,
        "max" => 500,
        'display_value' => 'text'
    );
    $gen_opts_atts[] = array(
        'id' => 'careerfy-logo-height',
        'type' => 'slider',
        'title' => __('Logo Height', 'careerfy-frame'),
        'subtitle' => __('Set Logo Height', 'careerfy-frame'),
        'desc' => __('Set Logo Height in (px)', 'careerfy-frame'),
        "default" => 0,
        "min" => 0,
        "step" => 1,
        "max" => 500,
        'display_value' => 'text'
    );
    $gen_opts_atts[] = array(
        'id' => 'geek-responsive-logo',
        'type' => 'media',
        'url' => true,
        'title' => __('Responsive Logo', 'careerfy-frame'),
        'compiler' => 'true',
        'desc' => __('Responsive Logo media uploader.', 'careerfy-frame'),
        'subtitle' => __('Responsive Logo media uploader.', 'careerfy-frame'),
        'default' => array('url' => trailingslashit(get_template_directory_uri()) . 'images/logo.png'),
    );
    if (function_exists('icl_get_languages') && function_exists('wpml_init_language_switcher')) {
        global $sitepress;
        $wpml_all_langs = icl_get_languages();
        if (!empty($wpml_all_langs)) {
            $default_lang = $sitepress->get_default_language();
            foreach ($wpml_all_langs as $lang_code => $wpml_lang) {
                if ($lang_code != $default_lang) {
                    $lang_name = isset($wpml_lang['native_name']) ? $wpml_lang['native_name'] : $lang_code;
                    $gen_opts_atts[] = array(
                        'id' => 'careerfy_responsive_logo_' . $lang_code,
                        'type' => 'media',
                        'url' => true,
                        'title' => sprintf(__('Responsive Logo for %s Language', 'careerfy-frame'), $lang_name),
                        'compiler' => 'true',
                        'desc' => '',
                        'subtitle' => sprintf(__('Responsive Logo media uploader for %s Language.', 'careerfy-frame'), $lang_name),
                        'default' => array('url' => trailingslashit(get_template_directory_uri()) . 'images/logo.png'),
                    );
                }
            }
        }
    }
    $gen_opts_atts[] = array(
        'id' => 'geek-resp-logo-height',
        'type' => 'slider',
        'title' => __('Responsive Logo Height', 'careerfy-frame'),
        'subtitle' => __('Set Responsive Logo Height', 'careerfy-frame'),
        'desc' => __('Set Responsive Logo Height in (px)', 'careerfy-frame'),
        "default" => 0,
        "min" => 0,
        "step" => 1,
        "max" => 500,
        'display_value' => 'text'
    );
    $gen_opts_atts[] = array(
        'id' => 'geek-resp-logo-width',
        'type' => 'slider',
        'title' => __('Responsive Logo Width', 'careerfy-frame'),
        'subtitle' => __('Set Responsive Logo Width', 'careerfy-frame'),
        'desc' => __('Set Responsive Logo Width in (px)', 'careerfy-frame'),
        "default" => 0,
        "min" => 0,
        "step" => 1,
        "max" => 500,
        'display_value' => 'text'
    );
    $gen_opts_atts[] = array(
        'id' => 'careerfy-site-loader',
        'type' => 'button_set',
        'title' => __('Site loader', 'careerfy-frame'),
        'subtitle' => __('Site loader on page loading.', 'careerfy-frame'),
        'desc' => '',
        'options' => array(
            'on' => __('On', 'careerfy-frame'),
            'off' => __('Off', 'careerfy-frame'),
        ),
        'default' => 'on',
    );

    $redux_genral_options = array(
        'title' => __('General Options', 'careerfy-frame'),
        'id' => 'general-options',
        'desc' => __('These are really basic options!', 'careerfy-frame'),
        'icon' => 'el el-home',
        'fields' => apply_filters('careerfy_framewrok_options_general', $gen_opts_atts)
    );
    Redux::set_section($opt_name, $redux_genral_options);

    add_filter('redux/options/careerfy_framework_options/sections', 'careerfy_frame_plugin_core_settings', 1);

    function careerfy_frame_plugin_core_settings($setting_sections)
    {
        global $careerfy_framework_options, $wpdb;
        //
        $careerfy_framework_options = get_option('careerfy_framework_options');
        $jobsearch_plugin_options = get_option('jobsearch_plugin_options');

        $header_opt_settings = array(
            'title' => __('Header', 'careerfy-frame'),
            'id' => 'general-options-header',
            'desc' => __('Set Header Fields.', 'careerfy-frame'),
            'icon' => 'el el-credit-card',
            'fields' => array()
        );

        $all_page = array('', __('Select Page', 'careerfy-frame'));

        $args = array(
            'sort_order' => 'asc',
            'sort_column' => 'post_title',
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'meta_key' => '',
            'meta_value' => '',
            'authors' => '',
            'child_of' => 0,
            'parent' => -1,
            'exclude_tree' => '',
            'number' => '',
            'offset' => 0,
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $pages = get_pages($args);
        if (!empty($pages)) {
            foreach ($pages as $page) {
                $all_page[$page->post_name] = $page->post_title;
            }
        }

        $total_slider = [];
        if (class_exists('RevSliderFront')) {
            $revslider_lists_result = $wpdb->get_results("SELECT title,alias FROM {$wpdb->prefix}revslider_sliders", OBJECT);
            if (!empty($revslider_lists_result)) {
                foreach ($revslider_lists_result as $revsliders) {
                    $total_slider[$revsliders->alias] = $revsliders->title;
                }
            }
        }

        $header_opt_settings['fields'][] = array(
            'id' => 'header-style',
            'type' => 'select',
            'title' => __('Header Styles', 'careerfy-frame'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'style1' => __('Header Style 1', 'careerfy-frame'),
                'style2' => __('Header Style 2', 'careerfy-frame'),
                'style3' => __('Header Style 3', 'careerfy-frame'),
                'style4' => __('Header Style 4', 'careerfy-frame'),
                'style5' => __('Header Style 5', 'careerfy-frame'),
                'style6' => __('Header Style 6', 'careerfy-frame'),
                'style7' => __('Header Style 7', 'careerfy-frame'),
                'style8' => __('Header Style 8', 'careerfy-frame'),
                'style9' => __('Header Style 9', 'careerfy-frame'),
                'style10' => __('Header Style 10', 'careerfy-frame'),
                'style11' => __('Header Style 11', 'careerfy-frame'),
                'style12' => __('Header Style 12', 'careerfy-frame'),
                'style13' => __('Header Style 13', 'careerfy-frame'),
                'style14' => __('Header Style 14', 'careerfy-frame'),
                'style15' => __('Header Style 15', 'careerfy-frame'),
                'style16' => __('Header Style 16', 'careerfy-frame'),
                'style17' => __('Header Style 17', 'careerfy-frame'),
                'style18' => __('Header Style 18', 'careerfy-frame'),
                'style19' => __('Header Style 19', 'careerfy-frame'),
                'style20' => __('Header Style 20', 'careerfy-frame'),
                'style21' => __('Header Style 21', 'careerfy-frame'),
                'style22' => __('Header Style 22', 'careerfy-frame'),
            ),
            'default' => 'style1',
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'mobile_header_style',
            'type' => 'select',
            'title' => __('Mobile Header Styles', 'careerfy-frame'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'style1' => __('Style 1', 'careerfy-frame'),
                'style2' => __('Style 2', 'careerfy-frame'),
                'style3' => __('Style 3', 'careerfy-frame'),
            ),
            'default' => 'style1',
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'header_email',
            'type' => 'text',
            'title' => __('Header Email', 'careerfy-frame'),
            'required' => array('header-style', 'equals', 'style9'),
            'subtitle' => __('Put email address here to show on header left', 'careerfy-frame'),
            'desc' => '',
            'default' => '',
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'header_phone',
            'type' => 'text',
            'title' => __('Header Phone', 'careerfy-frame'),
            'required' => array('header-style', 'equals', 'style9'),
            'subtitle' => __('Put phone number  here to show on header left', 'careerfy-frame'),
            'desc' => '',
            'default' => '',
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'careerfy-sticky-header',
            'type' => 'button_set',
            'title' => __('Sticky Header', 'careerfy-frame'),
            'subtitle' => __('Sticky Header on/off.', 'careerfy-frame'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'careerfy-frame'),
                'off' => __('Off', 'careerfy-frame'),
            ),
            'default' => 'off',
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'careerfy-top-header',
            'type' => 'button_set',
            'title' => __('Top Header', 'careerfy-frame'),
            'subtitle' => __('Top Header on/off.', 'careerfy-frame'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'careerfy-frame'),
                'off' => __('Off', 'careerfy-frame'),
            ),
            'default' => 'off',
            'required' => array('header-style', 'equals', 'style12'),
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'careerfy-header-option',
            'type' => 'button_set',
            'title' => __('Header Option', 'careerfy-frame'),
            'subtitle' => '',
            'options' => array(
                'header-bg-image' => __('Banner Image', 'careerfy-frame'),
                'header-bg-slider' => __('Banner Slider', 'careerfy-frame'),
            ),
            'default' => 'header-bg-image',
            'required' => array(
                array('header-style', 'equals', 'style12'),
                array('careerfy-top-header', 'equals', 'on'),
            ),
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'careerfy-header-slider',
            'type' => 'button_set',
            'title' => __('Header Slider', 'careerfy-frame'),
            'required' => array(
                array('header-style', 'equals', 'style12'),
                array('careerfy-header-option', 'equals', 'header-bg-slider')
            ),
            'subtitle' => __('Header Slider pages on/off.', 'careerfy-frame'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'careerfy-frame'),
                'off' => __('Off', 'careerfy-frame'),
            ),
            'default' => 'off',
        );

        $totsliders_arr_revse = array_reverse($total_slider);
        $header_opt_settings['fields'][] = array(
            'id' => 'careerfy-header-slider-list',
            'type' => 'select',
            'title' => __('Header Slider', 'careerfy-frame'),
            'subtitle' => '',
            'desc' => '',
            'options' => $total_slider,
            'required' => array('careerfy-header-slider', 'equals', 'on'),
            'default' => array_pop($totsliders_arr_revse),
        );

        $header_opt_settings['fields'][] = array(
            'id' => 'careerfy-header-bg-img',
            'type' => 'media',
            'url' => true,
            'title' => __('Header Background Image', 'careerfy-frame'),
            'compiler' => 'true',
            'desc' => __('Header media uploader.', 'careerfy-frame'),
            'subtitle' => __('Header media uploader.', 'careerfy-frame'),
            'default' => '',
            'required' => array(
                array('header-style', 'equals', 'style12'),
                array('careerfy-header-option', 'equals', 'header-bg-image')
            ),
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'careerfy-header-overlay-color',
            'type' => 'color_rgba',
            'transparent' => false,
            'title' => __('Overlay Color', 'careerfy-frame'),
            'subtitle' => __('Set Overlay Color.', 'careerfy-frame'),
            'desc' => '',
            'default' => '',
            'required' => array(
                array('header-style', 'equals', 'style12'),
                array('careerfy-top-header', 'equals', 'on'),
                array('careerfy-header-option', 'equals', 'header-bg-image')
            ),
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'careerfy-header-advance-search',
            'type' => 'button_set',
            'title' => __('Header Advance Search', 'careerfy-frame'),
            'subtitle' => __('Header Advance Search for inner pages on/off.', 'careerfy-frame'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'careerfy-frame'),
                'off' => __('Off', 'careerfy-frame'),
            ),
            'default' => 'off',
            'required' => array('careerfy-top-header', 'equals', 'on'),
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'careerfy-adv-search-shortcode',
            'type' => 'textarea',
            'title' => __('Advanced Search Shortcode', 'careerfy-frame'),
            'subtitle' => __('Set Advanced Search here.', 'careerfy-frame'),
            'desc' => '',
            'default' => '',
            'required' => array(
                array('header-style', 'equals', 'style12'),
                array('careerfy-top-header', 'equals', 'on'),
            ),
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'header-button-page-1',
            'type' => 'select',
            'title' => __('Header User Link 1', 'careerfy-frame'),
            'required' => array(
                array('header-style', 'equals', 'style13'),
            ),
            'subtitle' => __('Select Page for header User first link.', 'careerfy-frame'),
            'desc' => '',
            'options' => $all_page,
            'default' => '',
        );

        $header_opt_settings['fields'][] = array(
            'id' => 'header-button-page-2',
            'type' => 'select',
            'title' => __('Header User Link 2', 'careerfy-frame'),
            'required' => array(
                array('header-style', 'equals', 'style13'),
            ),
            'subtitle' => __('Select Page for header User second link.', 'careerfy-frame'),
            'desc' => '',
            'options' => $all_page,
            'default' => '',
        );

        $header_opt_settings['fields'][] = array(
            'id' => 'header-button-page',
            'type' => 'select',
            'title' => __('Header Button Page', 'careerfy-frame'),
            'required' => array(
                array('header-style', '!=', 'style3'),
                array('header-style', '!=', 'style12'),
            ),
            'subtitle' => __('Select Page for header button link.', 'careerfy-frame'),
            'desc' => '',
            'options' => $all_page,
            'default' => '',
        );

        $header_opt_settings['fields'][] = array(
            'id' => 'geek-top-strip-section',
            'type' => 'section',
            'required' => array(
                array('header-style', '!=', 'style1'),
                array('header-style', '!=', 'style2'),
                //array('header-style', '!=', 'style3'),
                array('header-style', '!=', 'style4'),
                array('header-style', '!=', 'style5'),
                array('header-style', '!=', 'style6'),
                array('header-style', '!=', 'style7'),
                array('header-style', '!=', 'style9'),
                array('header-style', '!=', 'style11'),
                array('header-style', '!=', 'style12'),
                array('header-style', '!=', 'style13'),
                array('header-style', '!=', 'style14'),
                array('header-style', '!=', 'style15'),
                array('header-style', '!=', 'style16'),
                array('header-style', '!=', 'style18'),
                array('header-style', '!=', 'style20'),
            ),
            'title' => __('Top Strip', 'careerfy-frame'),
            'subtitle' => '',
            'indent' => true,
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'geek-top-strip',
            'type' => 'button_set',
            'title' => __('Top Strip', 'careerfy-frame'),
            'subtitle' => __('Top Strip on/off.', 'careerfy-frame'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'careerfy-frame'),
                'off' => __('Off', 'careerfy-frame'),
            ),
            'default' => 'off',
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'geek-top-location',
            'type' => 'text',
            'title' => __('Location Address', 'careerfy-frame'),
            'subtitle' => __('Top Strip Location Address.', 'careerfy-frame'),
            'desc' => '',
            'default' => '1233 Mercer Street Stanley, WI 54768',
            'required' => array(
                array('header-style', '!=', 'style21'),
                array('header-style', '!=', 'style22'),
            ),

        );
        $header_opt_settings['fields'][] = array(
            'id' => 'geek-top-phone',
            'type' => 'text',
            'title' => __('Phone Number', 'careerfy-frame'),
            'subtitle' => __('Top Strip Phone Number.', 'careerfy-frame'),
            'desc' => '',
            'default' => '+012 345 6789',
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'careerfy-top-days',
            'type' => 'text',
            'title' => __('Days', 'careerfy-frame'),
            'subtitle' => __('Enter Days e.g (Monday - Friday).', 'careerfy-frame'),
            'desc' => '',
            'default' => 'Monday - Friday',
            'required' => array(
                array('header-style', 'equals', 'style22'),
            ),
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'careerfy-top-time',
            'type' => 'text',
            'title' => __('Time', 'careerfy-frame'),
            'subtitle' => __('Enter time e.g (9pm - 10am).', 'careerfy-frame'),
            'desc' => '',
            'default' => '9am - 10pm',
            'required' => array(
                array('header-style', '!=', 'style1'),
                array('header-style', '!=', 'style2'),
                array('header-style', '!=', 'style3'),
                array('header-style', '!=', 'style4'),
                array('header-style', '!=', 'style5'),
                array('header-style', '!=', 'style6'),
                array('header-style', '!=', 'style7'),
                array('header-style', '!=', 'style8'),
                array('header-style', '!=', 'style9'),
                array('header-style', '!=', 'style10'),
                array('header-style', '!=', 'style11'),
                array('header-style', '!=', 'style12'),
                array('header-style', '!=', 'style13'),
                array('header-style', '!=', 'style14'),
                array('header-style', '!=', 'style15'),
                array('header-style', '!=', 'style16'),
                array('header-style', '!=', 'style17'),
                array('header-style', '!=', 'style18'),
                array('header-style', '!=', 'style20'),
                array('header-style', '!=', 'style21'),

            ),
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'careerfy-top-days-second',
            'type' => 'text',
            'title' => __('Second Days', 'careerfy-frame'),
            'subtitle' => __('Enter Days e.g (Monday - Friday).', 'careerfy-frame'),
            'desc' => '',
            'default' => 'Monday - Friday',
            'required' => array(
                array('header-style', 'equals', 'style22'),
            ),
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'careerfy-top-time-second',
            'type' => 'text',
            'title' => __('Second Time', 'careerfy-frame'),
            'subtitle' => __('Enter time e.g (9am - 10pm).', 'careerfy-frame'),
            'desc' => '',
            'default' => '9am - 10pm',
            'required' => array(
                array('header-style', 'equals', 'style22'),
            ),
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'geek-top-email',
            'type' => 'text',
            'title' => __('Email Address', 'careerfy-frame'),
            'subtitle' => __('Top Strip Email Address.', 'careerfy-frame'),
            'desc' => '',
            'default' => 'contact@hireright.com',
            'required' => array(
                array('header-style', '!=', 'style22'),
                array('header-style', '!=', 'style19'),
                array('header-style', '!=', 'style21'),
            ),
        );
        $header_opt_settings['fields'][] = array(
            'id' => 'geek-top-social',
            'type' => 'button_set',
            'title' => __('Social Networking', 'careerfy-frame'),
            'subtitle' => __('Top Strip Social Networking on/off.', 'careerfy-frame'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'careerfy-frame'),
                'off' => __('Off', 'careerfy-frame'),
            ),
            'default' => 'on',
            'required' => array(
                array('header-style', '!=', 'style22'),
            ),
        );
        $header_opt_settings['fields'] = apply_filters('careerfy_framewrok_options_headers', $header_opt_settings['fields']);
        $setting_sections[] = $header_opt_settings;
        $section_settings = array(
            'title' => __('Sub Header', 'careerfy-frame'),
            'id' => 'subheader-options',
            'desc' => __('Default Sub Header settings.', 'careerfy-frame'),
            'icon' => 'el el-lines',
            'fields' => array(
                array(
                    'id' => 'careerfy-subheader',
                    'type' => 'button_set',
                    'title' => __('Sub Header', 'careerfy-frame'),
                    'subtitle' => __('Sub Header on/off.', 'careerfy-frame'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'careerfy-frame'),
                        'off' => __('Off', 'careerfy-frame'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'careerfy-subheader-height',
                    'type' => 'slider',
                    'title' => __('Sub Header Height', 'careerfy-frame'),
                    'required' => array('careerfy-subheader', 'equals', 'on'),
                    'subtitle' => __('Set Sub Header Height', 'careerfy-frame'),
                    'desc' => __('Set Sub Header Height in (px)', 'careerfy-frame'),
                    "default" => 0,
                    "min" => 0,
                    "step" => 1,
                    "max" => 1000,
                    'display_value' => 'text'
                ),
                array(
                    'id' => 'careerfy-subheader-pading-top',
                    'type' => 'slider',
                    'title' => __('Padding Top', 'careerfy-frame'),
                    'required' => array('careerfy-subheader', 'equals', 'on'),
                    'subtitle' => __('Set Sub Header Padding Top', 'careerfy-frame'),
                    'desc' => __('Set Sub Header Padding Top', 'careerfy-frame'),
                    "default" => 0,
                    "min" => 0,
                    "step" => 1,
                    "max" => 1000,
                    'display_value' => 'text'
                ),
                array(
                    'id' => 'careerfy-subheader-pading-bottom',
                    'type' => 'slider',
                    'title' => __('Padding Bottom', 'careerfy-frame'),
                    'required' => array('careerfy-subheader', 'equals', 'on'),
                    'subtitle' => __('Set Sub Header Padding Bottom', 'careerfy-frame'),
                    'desc' => __('Set Sub Header Padding Bottom', 'careerfy-frame'),
                    "default" => 0,
                    "min" => 0,
                    "step" => 1,
                    "max" => 1000,
                    'display_value' => 'text'
                ),
                array(
                    'id' => 'careerfy-subheader-title',
                    'type' => 'button_set',
                    'title' => __('Sub Header Title', 'careerfy-frame'),
                    'required' => array('careerfy-subheader', 'equals', 'on'),
                    'subtitle' => __('Sub Header Title on/off.', 'careerfy-frame'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'careerfy-frame'),
                        'off' => __('Off', 'careerfy-frame'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'careerfy-subheader-subtitle',
                    'type' => 'text',
                    'title' => __('Subtitle', 'careerfy-frame'),
                    'required' => array('careerfy-subheader', 'equals', 'on'),
                    'subtitle' => __('Set Subtitle', 'careerfy-frame'),
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'careerfy-subheader-breadcrumb',
                    'type' => 'button_set',
                    'title' => __('Sub Header Breadcrumb', 'careerfy-frame'),
                    'required' => array('careerfy-subheader', 'equals', 'on'),
                    'subtitle' => __('Sub Header Breadcrumb on/off.', 'careerfy-frame'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'careerfy-frame'),
                        'off' => __('Off', 'careerfy-frame'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'careerfy-subheader-bg-img',
                    'type' => 'media',
                    'url' => true,
                    'title' => __('Sub Header Background Image', 'careerfy-frame'),
                    'required' => array('careerfy-subheader', 'equals', 'on'),
                    'compiler' => 'true',
                    'desc' => __('Sub Header media uploader.', 'careerfy-frame'),
                    'subtitle' => __('Sub Header media uploader.', 'careerfy-frame'),
                    'default' => '',
                ),
                array(
                    'id' => 'careerfy-subheader-bg-color',
                    'type' => 'color_rgba',
                    'transparent' => false,
                    'title' => __('Sub Header Background Color', 'careerfy-frame'),
                    'required' => array('careerfy-subheader', 'equals', 'on'),
                    'subtitle' => __('Set Sub Header Background Color.', 'careerfy-frame'),
                    'desc' => '',
                    'default' => 'rgba(17,22,44,0.66)'
                ),

            )
        );

        $setting_sections[] = $section_settings;
        $args = array(
            'post_type' => 'department',
            'post_status' => 'publish',
            'post_per_page' => '-1',
            'fields' => 'ids',
        );
        $query = new WP_Query($args);
        $all_departments = $query->posts;

        $all_departments_array = array(esc_html__("Select Department", "careerfy-frame") => '');
        if (is_array($all_departments) && sizeof($all_departments) > 0) {
            foreach ($all_departments as $all_department) {
                $all_departments_array[$all_department] = get_the_title($all_department);
            }
        }

        // footer section start
        $header_opt_settings = array(
            'title' => __('Footer', 'careerfy-frame'),
            'id' => 'general-options-footer',
            'desc' => __('Set Footer Fields.', 'careerfy-frame'),
            'icon' => 'el el-tasks',
            'fields' => array(
                array(
                    'id' => 'footer-style',
                    'type' => 'select',
                    'title' => __('Footer Style', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => array(
                        'style1' => __('Footer Style 1', 'careerfy-frame'),
                        'style2' => __('Footer Style 2', 'careerfy-frame'),
                        'style3' => __('Footer Style 3', 'careerfy-frame'),
                        'style4' => __('Footer Style 4', 'careerfy-frame'),
                        'style5' => __('Footer Style 5', 'careerfy-frame'),
                        'style6' => __('Footer Style 6', 'careerfy-frame'),
                        'style7' => __('Footer Style 7', 'careerfy-frame'),
                        'style8' => __('Footer Style 8', 'careerfy-frame'),
                        'style9' => __('Footer Style 9', 'careerfy-frame'),
                        'style10' => __('Footer Style 10', 'careerfy-frame'),
                        'style11' => __('Footer Style 11', 'careerfy-frame'),
                        'style12' => __('Footer Style 12', 'careerfy-frame'),
                        'style13' => __('Footer Style 13', 'careerfy-frame'),
                        'style14' => __('Footer Style 14', 'careerfy-frame'),
                        'style15' => __('Footer Style 15', 'careerfy-frame'),
                        'style16' => __('Footer Style 16', 'careerfy-frame'),
                        'style17' => __('Footer Style 17', 'careerfy-frame'),
                        'style18' => __('Footer Style 18', 'careerfy-frame'),
                        'style19' => __('Footer Style 19', 'careerfy-frame'),
                    ),
                    'default' => 'style1',
                ),
                array(
                    'id' => 'careerfy-footer-copyright-text',
                    'type' => 'textarea',
                    'title' => __('Copyright Text', 'careerfy-frame'),
                    'subtitle' => __('Set Copyright Text here.', 'careerfy-frame'),
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'careerfy-footer-social',
                    'type' => 'button_set',
                    'title' => __('Footer Social Icons', 'careerfy-frame'),
                    'required' => array(
                        array('footer-style', '!=', 'style3'),
                        array('footer-style', '!=', 'style4'),
                        array('footer-style', '!=', 'style6'),
                        array('footer-style', '!=', 'style8'),
                        array('footer-style', '!=', 'style16'),
                        array('footer-style', '!=', 'style18'),
                        array('footer-style', '!=', 'style19'),
                    ),
                    'subtitle' => __('Footer Social Icons Area on/off', 'careerfy-frame'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'careerfy-frame'),
                        'off' => __('Off', 'careerfy-frame'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'footer-ios-app-link',
                    'type' => 'text',
                    'title' => __('iOS App Link', 'careerfy-frame'),
                    'required' => array(
                        array('footer-style', '!=', 'style1'),
                        array('footer-style', '!=', 'style3'),
                        array('footer-style', '!=', 'style4'),
                        array('footer-style', '!=', 'style6'),
                        array('footer-style', '!=', 'style7'),
                        array('footer-style', '!=', 'style8'),
                        array('footer-style', '!=', 'style9'),
                        array('footer-style', '!=', 'style11'),
                        array('footer-style', '!=', 'style12'),
                        array('footer-style', '!=', 'style13'),
                        array('footer-style', '!=', 'style16'),
                        array('footer-style', '!=', 'style17'),
                        array('footer-style', '!=', 'style18'),
                        array('footer-style', '!=', 'style19'),
                    ),
                    'subtitle' => __('Put iOS App Link here.', 'careerfy-frame'),
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'footer-android-app-link',
                    'type' => 'text',
                    'title' => __('Android App Link', 'careerfy-frame'),
                    'required' => array(
                        array('footer-style', '!=', 'style1'),
                        array('footer-style', '!=', 'style3'),
                        array('footer-style', '!=', 'style4'),
                        array('footer-style', '!=', 'style6'),
                        array('footer-style', '!=', 'style7'),
                        array('footer-style', '!=', 'style8'),
                        array('footer-style', '!=', 'style9'),
                        array('footer-style', '!=', 'style11'),
                        array('footer-style', '!=', 'style12'),
                        array('footer-style', '!=', 'style13'),
                        array('footer-style', '!=', 'style16'),
                        array('footer-style', '!=', 'style17'),
                        array('footer-style', '!=', 'style18'),
                        array('footer-style', '!=', 'style19'),
                    ),
                    'subtitle' => __('Put Android App Link here.', 'careerfy-frame'),
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'footer-logo',
                    'type' => 'media',
                    'url' => true,
                    'title' => __('Footer Logo', 'careerfy-frame'),
                    'required' => array(
                        array('footer-style', '!=', 'style1'),
                        array('footer-style', '!=', 'style2'),
                        array('footer-style', '!=', 'style5'),
                        array('footer-style', '!=', 'style6'),
                        array('footer-style', '!=', 'style7'),
                        array('footer-style', '!=', 'style8'),
                        array('footer-style', '!=', 'style9'),
                        array('footer-style', '!=', 'style11'),
                        array('footer-style', '!=', 'style12'),
                        array('footer-style', '!=', 'style13'),
                        array('footer-style', '!=', 'style14'),
                        array('footer-style', '!=', 'style15'),
                        array('footer-style', '!=', 'style16'),
                        array('footer-style', '!=', 'style17'),
                        array('footer-style', '!=', 'style18'),
                        array('footer-style', '!=', 'style19'),
                    ),
                    'compiler' => 'true',
                    'desc' => __('Footer Logo media uploader.', 'careerfy-frame'),
                    'subtitle' => __('Footer Logo media uploader.', 'careerfy-frame'),
                    'default' => array('url' => ''),
                ),
                array(
                    'id' => 'footer-background',
                    'type' => 'media',
                    'url' => true,
                    'title' => __('Footer Background', 'careerfy-frame'),
                    'required' => array(
                        array('footer-style', '!=', 'style1'),
                        array('footer-style', '!=', 'style2'),
                        array('footer-style', '!=', 'style5'),
                        array('footer-style', '!=', 'style6'),
                        array('footer-style', '!=', 'style7'),
                        array('footer-style', '!=', 'style8'),
                        array('footer-style', '!=', 'style9'),
                        array('footer-style', '!=', 'style12'),
                        array('footer-style', '!=', 'style13'),
                        array('footer-style', '!=', 'style14'),
                        array('footer-style', '!=', 'style15'),
                        array('footer-style', '!=', 'style16'),
                        array('footer-style', '!=', 'style17'),
                        array('footer-style', '!=', 'style18'),
                        array('footer-style', '!=', 'style19'),
                    ),
                    'compiler' => 'true',
                    'desc' => __('Footer Background media uploader.', 'careerfy-frame'),
                    'subtitle' => __('Footer Background media uploader.', 'careerfy-frame'),
                    'default' => array('url' => ''),
                ),
                array(
                    'id' => 'andriod-app-logo',
                    'type' => 'media',
                    'url' => true,
                    'title' => __('Apple App Logo', 'careerfy-frame'),
                    'required' => array(
                        array('footer-style', '!=', 'style1'),
                        array('footer-style', '!=', 'style3'),
                        array('footer-style', '!=', 'style4'),
                        array('footer-style', '!=', 'style6'),
                        array('footer-style', '!=', 'style7'),
                        array('footer-style', '!=', 'style8'),
                        array('footer-style', '!=', 'style9'),
                        array('footer-style', '!=', 'style11'),
                        array('footer-style', '!=', 'style12'),
                        array('footer-style', '!=', 'style13'),
                        array('footer-style', '!=', 'style15'),
                        array('footer-style', '!=', 'style16'),
                        array('footer-style', '!=', 'style17'),
                        array('footer-style', '!=', 'style18'),
                        array('footer-style', '!=', 'style19'),
                    ),
                    'compiler' => 'true',
                    'desc' => __('Andriod App Logo media uploader.', 'careerfy-frame'),
                    'subtitle' => __('Andriod App Logo media uploader.', 'careerfy-frame'),
                    'default' => array('url' => ''),
                ),
                array(
                    'id' => 'google-app-logo',
                    'type' => 'media',
                    'url' => true,
                    'title' => __('Google App Logo', 'careerfy-frame'),
                    'required' => array(
                        array('footer-style', '!=', 'style1'),
                        array('footer-style', '!=', 'style3'),
                        array('footer-style', '!=', 'style4'),
                        array('footer-style', '!=', 'style6'),
                        array('footer-style', '!=', 'style7'),
                        array('footer-style', '!=', 'style8'),
                        array('footer-style', '!=', 'style9'),
                        array('footer-style', '!=', 'style11'),
                        array('footer-style', '!=', 'style12'),
                        array('footer-style', '!=', 'style13'),
                        array('footer-style', '!=', 'style15'),
                        array('footer-style', '!=', 'style16'),
                        array('footer-style', '!=', 'style17'),
                        array('footer-style', '!=', 'style18'),
                        array('footer-style', '!=', 'style19'),
                    ),
                    'compiler' => 'true',
                    'desc' => __('Google App media uploader.', 'careerfy-frame'),
                    'subtitle' => __('Google App media uploader.', 'careerfy-frame'),
                    'default' => array('url' => ''),
                ),
                array(
                    'id' => 'footer-top-section',
                    'required' => array(
                        array('footer-style', '!=', 'style1'),
                        array('footer-style', '!=', 'style2'),
                        array('footer-style', '!=', 'style3'),
                        array('footer-style', '!=', 'style4'),
                        array('footer-style', '!=', 'style5'),
                        array('footer-style', '!=', 'style8'),
                        array('footer-style', '!=', 'style9'),
                        array('footer-style', '!=', 'style16'),
                        array('footer-style', '!=', 'style17'),
                        array('footer-style', '!=', 'style18'),
                        array('footer-style', '!=', 'style19'),
                    ),
                    'type' => 'section',
                    'title' => __('Footer Top Section', 'careerfy-frame'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'careerfy-footer-top-section-switch',
                    'type' => 'button_set',
                    'title' => __('Footer Top Section Switch', 'careerfy-frame'),
                    'required' => array(
                        array('footer-style', '!=', 'style1'),
                        array('footer-style', '!=', 'style2'),
                        array('footer-style', '!=', 'style3'),
                        array('footer-style', '!=', 'style4'),
                        array('footer-style', '!=', 'style5'),
                        array('footer-style', '!=', 'style8'),
                        array('footer-style', '!=', 'style14'),
                        array('footer-style', '!=', 'style17'),
                        array('footer-style', '!=', 'style19'),
                    ),
                    'subtitle' => __('Footer Top Section on/off', 'careerfy-frame'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'careerfy-frame'),
                        'off' => __('Off', 'careerfy-frame'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'careerfy-footer-top-newsletter',
                    'type' => 'button_set',
                    'title' => __('Newsletter', 'careerfy-frame'),
                    'required' => array('footer-style', 'equals', 'style7'),
                    'subtitle' => __('Footer Top Newsletter on/off', 'careerfy-frame'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'careerfy-frame'),
                        'off' => __('Off', 'careerfy-frame'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'footer-section-heading',
                    'type' => 'text',
                    'title' => __('Heading', 'careerfy-frame'),
                    'required' => array('footer-style', 'equals', 'style6'),
                    'subtitle' => __('Write Heading text here', 'careerfy-frame'),
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'footer-section-desc',
                    'type' => 'textarea',
                    'title' => __('Description', 'careerfy-frame'),
                    'required' => array('footer-style', 'equals', 'style6'),
                    'subtitle' => __('Write Description text here.', 'careerfy-frame'),
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'footer-section-btn-text',
                    'type' => 'text',
                    'title' => __('Button Text', 'careerfy-frame'),
                    'required' => array('footer-style', 'equals', 'style6'),
                    'subtitle' => __('Write button text here.', 'careerfy-frame'),
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'footer-section-btn-link',
                    'type' => 'text',
                    'title' => __('Button Link', 'careerfy-frame'),
                    'required' => array('footer-style', 'equals', 'style6'),
                    'subtitle' => __('Add button link here.', 'careerfy-frame'),
                    'desc' => '',
                    'default' => '#',
                ),
            )
        );
        $setting_sections[] = $header_opt_settings;

        // footer sidebars section start
        $footer_sidebar_settings = array(
            'title' => __('Footer Sidebars', 'careerfy-frame'),
            'id' => 'footer-sidebar-options',
            'desc' => __('Set Footer Sidebars.', 'careerfy-frame'),
            'icon' => 'el el-th',
            'fields' => array(
                array(
                    'id' => 'careerfy-footer-sidebar-switch',
                    'type' => 'button_set',
                    'title' => __('Footer Widgets Area', 'careerfy-frame'),
                    'subtitle' => __('Footer Widgets Area on/off', 'careerfy-frame'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'careerfy-frame'),
                        'off' => __('Off', 'careerfy-frame'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'careerfy-footer-sidebars',
                    'type' => 'careerfy_multi_select',
                    'select_title' => __('Select Column Width', 'careerfy-frame'),
                    'input_title' => __('Sidebar Name', 'careerfy-frame'),
                    'select_name' => 'col_width',
                    'input_name' => 'sidebar_name',
                    'add_more_text' => __('Add Sidebar', 'careerfy-frame'),
                    'select_options' => array(
                        '12_12' => '12/12',
                        '6_12' => '6/12',
                        '4_12' => '4/12',
                        '3_12' => '3/12',
                        '9_12' => '9/12',
                        '2_12' => '2/12',
                        '10_12' => '10/12',
                        '8_12' => '8/12',
                        '5_12' => '5/12',
                        '7_12' => '7/12',
                    ),
                    'title' => __('Footer Sidebars', 'careerfy-frame'),
                    'required' => array('careerfy-footer-sidebar-switch', 'equals', 'on'),
                    'subtitle' => __('Set Footer Sidebars here.', 'careerfy-frame'),
                    'desc' => '',
                    'default' => '',
                ),
            )
        );
        $setting_sections[] = $footer_sidebar_settings;

        $section_settings = array(
            'title' => __('Color', 'careerfy-frame'),
            'id' => 'theme-all-colors',
            'desc' => __('Set the First color for theme.', 'careerfy-frame'),
            'icon' => 'el el-brush',
            'fields' => array(
                array(
                    'id' => 'careerfy-main-color',
                    'type' => 'color',
                    'transparent' => false,
                    'title' => __('Theme Color', 'careerfy-frame'),
                    'subtitle' => __('Set Main Theme Color.', 'careerfy-frame'),
                    'desc' => '',
                    'default' => ''
                ),
                array(
                    'id' => 'careerfy-body-color',
                    'type' => 'color',
                    'transparent' => false,
                    'title' => __('Body Background Color', 'careerfy-frame'),
                    'subtitle' => __('Set Body Background Color.', 'careerfy-frame'),
                    'desc' => '',
                    'default' => ''
                ),
                array(
                    'id' => 'header-colors-section',
                    'type' => 'section',
                    'title' => __('Header Colors', 'careerfy-frame'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'header-bg-color',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('Header Background Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => __('This color will apply to Header Background.', 'careerfy-frame'),
                    'default' => '#ffffff',
                ),
                array(
                    'id' => 'header-btn-bg-color',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('Buttons Background Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => __('This color will apply to Header Background Buttons.', 'careerfy-frame'),
                    'default' => '#ffffff',
                ),
                array(
                    'id' => 'header-btn-text-color',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('Buttons Text Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => __('This color will apply to the header buttons text.', 'careerfy-frame'),
                    'default' => '#ffffff',
                ),
                array(
                    'id' => 'header-btn-link-color',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('Link Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => __('This color will apply to the header Sign in/Signup/MyAccount text.', 'careerfy-frame'),
                    'default' => '#ffffff',
                ),

                array(
                    'id' => 'header-colors-section',
                    'type' => 'section',
                    'title' => __('Top Menu Header Colors', 'careerfy-frame'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'top-menu-link-color',
                    'type' => 'link_color',
                    'title' => __('Menu Links Color Option', 'careerfy-frame'),
                    'subtitle' => '',
                    'visited' => true,
                    'desc' => __('This colors will apply on Top header navigation menu items in header style 12.', 'careerfy-frame'),
                    'default' => array(
                        'regular' => '#656c6c',
                        'hover' => '#13b5ea',
                        'active' => '#13b5ea',
                        'visited' => '',
                    )
                ),

                array(
                    'id' => 'sticky-header-colors-section',
                    'type' => 'section',
                    'title' => __('Sticky Header Colors', 'careerfy-frame'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'sticky-bg-color',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('Sticky Background Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => __('This color will apply to Sticky Header Background.', 'careerfy-frame'),
                    'default' => '#ffffff',
                ),
                array(
                    'id' => 'sticky-menu-link-color',
                    'type' => 'link_color',
                    'title' => __('Sticky Menu Links Color Option', 'careerfy-frame'),
                    'subtitle' => '',
                    'visited' => true,
                    'desc' => __('These colors will apply to sticky header navigation menu items.', 'careerfy-frame'),
                    'default' => array(
                        'regular' => '#656c6c',
                        'hover' => '#13b5ea',
                        'active' => '#13b5ea',
                        'visited' => '',
                    )
                ),
                array(
                    'id' => 'menu-colors-section',
                    'type' => 'section',
                    'title' => __('Header Menu Colors', 'careerfy-frame'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'menu-link-color',
                    'type' => 'link_color',
                    'title' => __('Menu Links Color Option', 'careerfy-frame'),
                    'subtitle' => '',
                    'visited' => true,
                    'desc' => __('These colors will apply to header navigation menu items.', 'careerfy-frame'),
                    'default' => array(
                        'regular' => '#656c6c',
                        'hover' => '#13b5ea',
                        'active' => '#13b5ea',
                        'visited' => '',
                    )
                ),
                array(
                    'id' => 'submenu-bg-color',
                    'type' => 'color',
                    'transparent' => false,
                    'title' => __('SubMenu Background Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => __('This color will apply to SubMenu Background.', 'careerfy-frame'),
                    'default' => '#ffffff',
                ),
                array(
                    'id' => 'submenu-border-color',
                    'type' => 'color',
                    'transparent' => false,
                    'title' => __('SubMenu Border Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => __('This color will apply to SubMenu Border.', 'careerfy-frame'),
                    'default' => '#f2f2f2',
                ),
                array(
                    'id' => 'submenu-link-color',
                    'type' => 'link_color',
                    'title' => __('SubMenu Links Color Option', 'careerfy-frame'),
                    'subtitle' => '',
                    'visited' => true,
                    'desc' => __('These colors will apply to header navigation sub-menu items.', 'careerfy-frame'),
                    'default' => array(
                        'regular' => '#656c6c',
                        'hover' => '#13b5ea',
                        'active' => '#13b5ea',
                        'visited' => '',
                    )
                ),
                array(
                    'id' => 'submenu-link-bg-color',
                    'type' => 'link_color',
                    'title' => __('SubMenu Links Background Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'regular' => false,
                    'desc' => __('These colors will apply to the header navigation sub-menu Link Background.', 'careerfy-frame'),
                    'default' => array(
                        'hover' => '#ffffff',
                        'active' => '#ffffff',
                    )
                ),
                array(
                    'id' => 'mega-menu',
                    'type' => 'section',
                    'title' => __('Mega Menu Colors', 'careerfy-frame'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'megamenu-text-color',
                    'type' => 'color',
                    'title' => __('MegaMenu Paragraph Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'visited' => true,
                    'transparent' => false,
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'megamenu-bg-color',
                    'type' => 'color',
                    'transparent' => false,
                    'title' => __('MegaMenu Background Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '#ffffff',
                ),
                array(
                    'id' => 'megamenu-border-color',
                    'type' => 'color',
                    'transparent' => false,
                    'title' => __('MegaMenu Border Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '#f2f2f2',
                ),
                array(
                    'id' => 'megamenu-sublink-color',
                    'type' => 'link_color',
                    'title' => __('MegaMenu subLinks Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'visited' => true,
                    'desc' => '',
                    'default' => array(
                        'regular' => '#656c6c',
                        'hover' => '#13b5ea',
                        'active' => '#13b5ea',
                        'visited' => '',
                    )
                ),

                array(
                    'id' => 'mobile-header-colors-section',
                    'type' => 'section',
                    'title' => __('Mobile Header Colors', 'careerfy-frame'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'mobile_header_bg_color',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('Mobile Header Background Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => __('This color will apply to mobile view header background.', 'careerfy-frame'),
                    'default' => '#ffffff',
                ),
                array(
                    'id' => 'mobile_hder_menubtn_color',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('Hamburger Menu Icon Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'mobile_hder_notify_color',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('Notification Icon Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'mobile_hder_notify_bgcolor',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('Notification Icon Background Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'mobile_hder_userlogin_color',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('User Login Icon Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'mobile_hder_userlogin_bgcolor',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('User Login Icon Background Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'mobile_hder_cusbtn_color',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('Header Button Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'mobile_hder_cusbtn_bgcolor',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('Header Button Background Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '',
                ),

                array(
                    'id' => 'mobilehder-menu-colors',
                    'type' => 'section',
                    'title' => __('Mobile Menu Colors', 'careerfy-frame'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'mobile_header_menubg_color',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('Menu Background Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'mobile_hder_close_iconcolor',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('Menu Close Icon Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'mobile_hder_menuitm_color',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('Menu Link Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'mobile_hder_menuitm_bgclr',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('Menu Link Background Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'mobile_hder_menuitm_borderclr',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('Menu Link Border Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'mobile_hder_activ_menuitm_color',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('Menu Active Link Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'mobile_hder_activ_menuitm_bgclr',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('Menu Active Link Background Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'mobile_hder_submenuitm_color',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('SubMenu Link Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'mobile_hder_submenuitm_bgclr',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('SubMenu Background Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'mobile_hder_submenuitm_borderclr',
                    'type' => 'color_rgba',
                    'transparent' => true,
                    'title' => __('SubMenu Link Border Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '',
                ),

                array(
                    'id' => 'footer-colors-section',
                    'type' => 'section',
                    'title' => __('Footer Colors', 'careerfy-frame'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'footer-bg-color',
                    'type' => 'color',
                    'transparent' => false,
                    'title' => __('Footer Background Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => __('This color will apply on the Footer Background.', 'careerfy-frame'),
                    'default' => '#26272b',
                ),
                array(
                    'id' => 'footer-text-color',
                    'type' => 'color',
                    'transparent' => false,
                    'title' => __('Footer Text Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => __('This color will apply to Footer Text.', 'careerfy-frame'),
                    'default' => '#999999',
                ),
                array(
                    'id' => 'footer-link-color',
                    'type' => 'link_color',
                    'title' => __('Footer Links Color Option', 'careerfy-frame'),
                    'subtitle' => '',
                    'visited' => true,
                    'desc' => __('These colors will apply to Footer links.', 'careerfy-frame'),
                    'default' => array(
                        'regular' => '#999999',
                        'hover' => '#ffffff',
                        'active' => '#999999',
                        'visited' => '#ffffff',
                    )
                ),
                array(
                    'id' => 'footer-border-color',
                    'type' => 'color',
                    'transparent' => false,
                    'title' => __('Footer Borders Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => __('This color will apply on Footer all Borders like widgets etc.', 'careerfy-frame'),
                    'default' => '#2e2e2e',
                ),
                array(
                    'id' => 'footer-copyright-bgcolor',
                    'type' => 'color',
                    'transparent' => false,
                    'title' => __('Footer copyright Background', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => __('This color will apply to Footer copyright Background.', 'careerfy-frame'),
                    'default' => '#26272b',
                ),
                array(
                    'id' => 'footer-copyright-color',
                    'type' => 'color',
                    'transparent' => false,
                    'title' => __('Footer copyright Text Color', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => __('This color will apply to Footer copyright Text.', 'careerfy-frame'),
                    'default' => '#999999',
                ),
            ),
        );
        $setting_sections[] = $section_settings;

        $footer_sidebar_settings = array(
            'title' => __('Typography', 'careerfy-frame'),
            'id' => 'custom-typo-sec',
            'desc' => '',
            'icon' => 'el el-font',
            'fields' => array(
                array(
                    'id' => 'body-typo',
                    'type' => 'typography',
                    'title' => __('Body Typography', 'careerfy-frame'),
                    'google' => true,
                    'text-transform' => true,
                    'letter-spacing' => true,
                    'word-spacing' => true,
                    'text-align' => false,
                    'font-backup' => true,
                    'output' => array('body, .jobsearch-description ul li, .jobsearch-typo-wrap p'),
                    'units' => 'px',
                    'subtitle' => __('Typography options with each property can be called individually.', 'careerfy-frame'),
                    'default' => array(
                        'color' => '#333333',
                        'font-style' => 'normal',
                        'font-family' => 'Roboto',
                        'google' => true,
                        'font-size' => '14px',
                        'line-height' => '20px'
                    ),
                ),
                array(
                    'id' => 'menu-typo',
                    'type' => 'typography',
                    'title' => __('Menu Typography', 'careerfy-frame'),
                    'google' => true,
                    'color' => false,
                    'text-transform' => true,
                    'letter-spacing' => true,
                    'word-spacing' => true,
                    'text-align' => false,
                    'font-backup' => true,
                    'output' => array('.navbar-nav > li > a'),
                    'units' => 'px',
                    'subtitle' => __('Typography options with each property can be called individually.', 'careerfy-frame'),
                    'default' => array(
                        'font-style' => 'normal',
                        'font-family' => 'Roboto',
                        'google' => true,
                        'font-size' => '14px',
                        'line-height' => '20px'
                    ),
                ),
                array(
                    'id' => 'submenu-typo',
                    'type' => 'typography',
                    'title' => __('SubMenu Typography', 'careerfy-frame'),
                    'google' => true,
                    'text-transform' => true,
                    'letter-spacing' => true,
                    'word-spacing' => true,
                    'text-align' => false,
                    'color' => false,
                    'font-backup' => true,
                    'output' => array('.navbar-nav .sub-menu li a', '.navbar-nav .children li a', '.careerfy-megalist li a'),
                    'units' => 'px',
                    'subtitle' => __('Typography options with each property can be called individually.', 'careerfy-frame'),
                    'default' => array(
                        'font-style' => 'normal',
                        'font-family' => 'Roboto',
                        'google' => true,
                        'font-size' => '14px',
                        'line-height' => '20px'
                    ),
                ),
                array(
                    'id' => 'header-megamenu-typo',
                    'type' => 'typography',
                    'title' => __('Mega Menu Typography', 'careerfy-frame'),
                    'google' => true,
                    'text-transform' => true,
                    'letter-spacing' => true,
                    'word-spacing' => true,
                    'text-align' => false,
                    'font-backup' => true,
                    'output' => array('.megamenu-title, .careerfy-megamenu li h4'),
                    'units' => 'px',
                    'subtitle' => __('Typography options with each property can be called individually.', 'careerfy-frame'),
                    'default' => array(
                        'color' => '#ffffff',
                        'font-style' => 'normal',
                        'font-family' => 'Roboto',
                        'google' => true,
                        'font-size' => '18px',
                        'line-height' => '22px'
                    ),
                ),
                array(
                    'id' => 'h1-typo',
                    'type' => 'typography',
                    'title' => __('H1 Typography', 'careerfy-frame'),
                    'google' => true,
                    'text-transform' => true,
                    'letter-spacing' => true,
                    'word-spacing' => true,
                    'text-align' => false,
                    'font-backup' => true,
                    'output' => array('h1', 'body h1'),
                    'units' => 'px',
                    'subtitle' => __('Typography options with each property can be called individually.', 'careerfy-frame'),
                    'default' => array(
                        'color' => '#333333',
                        'font-style' => 'normal',
                        'font-family' => 'Roboto',
                        'google' => true,
                        'font-size' => '26px',
                        'line-height' => '30px'
                    ),
                ),
                array(
                    'id' => 'h2-typo',
                    'type' => 'typography',
                    'title' => __('H2 Typography', 'careerfy-frame'),
                    'google' => true,
                    'text-transform' => true,
                    'letter-spacing' => true,
                    'word-spacing' => true,
                    'text-align' => false,
                    'font-backup' => true,
                    'output' => array('h2', 'body h2'),
                    'units' => 'px',
                    'subtitle' => __('Typography options with each property can be called individually.', 'careerfy-frame'),
                    'default' => array(
                        'color' => '#333333',
                        'font-style' => 'normal',
                        'font-family' => 'Roboto',
                        'google' => true,
                        'font-size' => '24px',
                        'line-height' => '28px'
                    ),
                ),
                array(
                    'id' => 'h3-typo',
                    'type' => 'typography',
                    'title' => __('H3 Typography', 'careerfy-frame'),
                    'google' => true,
                    'text-transform' => true,
                    'letter-spacing' => true,
                    'word-spacing' => true,
                    'text-align' => false,
                    'font-backup' => true,
                    'output' => array('h3', 'body h3'),
                    'units' => 'px',
                    'subtitle' => __('Typography options with each property can be called individually.', 'careerfy-frame'),
                    'default' => array(
                        'color' => '#333333',
                        'font-style' => 'normal',
                        'font-family' => 'Roboto',
                        'google' => true,
                        'font-size' => '22px',
                        'line-height' => '26px'
                    ),
                ),
                array(
                    'id' => 'h4-typo',
                    'type' => 'typography',
                    'title' => __('H4 Typography', 'careerfy-frame'),
                    'google' => true,
                    'text-transform' => true,
                    'letter-spacing' => true,
                    'word-spacing' => true,
                    'text-align' => false,
                    'font-backup' => true,
                    'output' => array('h4', 'body h4'),
                    'units' => 'px',
                    'subtitle' => __('Typography options with each property can be called individually.', 'careerfy-frame'),
                    'default' => array(
                        'color' => '#333333',
                        'font-style' => 'normal',
                        'font-family' => 'Roboto',
                        'google' => true,
                        'font-size' => '20px',
                        'line-height' => '24px'
                    ),
                ),
                array(
                    'id' => 'h5-typo',
                    'type' => 'typography',
                    'title' => __('H5 Typography', 'careerfy-frame'),
                    'google' => true,
                    'text-transform' => true,
                    'letter-spacing' => true,
                    'word-spacing' => true,
                    'text-align' => false,
                    'font-backup' => true,
                    'output' => array('h5', 'body h5'),
                    'units' => 'px',
                    'subtitle' => __('Typography options with each property can be called individually.', 'careerfy-frame'),
                    'default' => array(
                        'color' => '#333333',
                        'font-style' => 'normal',
                        'font-family' => 'Roboto',
                        'google' => true,
                        'font-size' => '18px',
                        'line-height' => '22px'
                    ),
                ),
                array(
                    'id' => 'h6-typo',
                    'type' => 'typography',
                    'title' => __('H6 Typography', 'careerfy-frame'),
                    'google' => true,
                    'text-transform' => true,
                    'letter-spacing' => true,
                    'word-spacing' => true,
                    'text-align' => false,
                    'font-backup' => true,
                    'output' => array('h6', 'body h6'),
                    'units' => 'px',
                    'subtitle' => __('Typography options with each property can be called individually.', 'careerfy-frame'),
                    'default' => array(
                        'color' => '#333333',
                        'font-style' => 'normal',
                        'font-family' => 'Roboto',
                        'google' => true,
                        'font-size' => '16px',
                        'line-height' => '20px'
                    ),
                ),
                array(
                    'id' => 'fancy-title-typo',
                    'type' => 'typography',
                    'title' => __('Fancy Title Typography', 'careerfy-frame'),
                    'google' => true,
                    'text-transform' => true,
                    'letter-spacing' => true,
                    'word-spacing' => true,
                    'text-align' => false,
                    'font-backup' => true,
                    'output' => array('.careerfy-fancy-title h2'),
                    'units' => 'px',
                    'subtitle' => __('Typography options with each property can be called individually.', 'careerfy-frame'),
                    'default' => array(
                        'color' => '#333333',
                        'font-style' => 'normal',
                        'font-family' => 'Roboto',
                        'google' => true,
                        'font-size' => '24px',
                        'line-height' => '28px'
                    ),
                ),
                array(
                    'id' => 'page-title-typo',
                    'type' => 'typography',
                    'title' => __('Page Title Typography', 'careerfy-frame'),
                    'google' => true,
                    'text-transform' => true,
                    'letter-spacing' => true,
                    'word-spacing' => true,
                    'text-align' => false,
                    'font-backup' => true,
                    'output' => array('.careerfy-page-title h1,.jobsearch-page-title h1'),
                    'units' => 'px',
                    'subtitle' => __('Typography options with each property can be called individually.', 'careerfy-frame'),
                    'default' => array(
                        'color' => '#ffffff',
                        'font-style' => '600',
                        'font-family' => 'Roboto',
                        'google' => true,
                        'font-size' => '30px',
                        'line-height' => '34px'
                    ),
                ),
                array(
                    'id' => 'sidebar-widget-typo',
                    'type' => 'typography',
                    'title' => __('Sidebar widget title Typography', 'careerfy-frame'),
                    'google' => true,
                    'text-transform' => true,
                    'letter-spacing' => true,
                    'word-spacing' => true,
                    'text-align' => false,
                    'font-backup' => true,
                    'output' => array('.careerfy-widget-title h2'),
                    'units' => 'px',
                    'subtitle' => __('Typography options with each property can be called individually.', 'careerfy-frame'),
                    'default' => array(
                        'color' => '#333333',
                        'font-style' => 'normal',
                        'font-family' => 'Roboto',
                        'google' => true,
                        'font-size' => '20px',
                        'line-height' => '24px'
                    ),
                ),
                array(
                    'id' => 'footer-widget-typo',
                    'type' => 'typography',
                    'title' => __('Footer widget title Typography', 'careerfy-frame'),
                    'google' => true,
                    'text-transform' => true,
                    'letter-spacing' => true,
                    'word-spacing' => true,
                    'text-align' => false,
                    'font-backup' => true,
                    'output' => array('.footer-widget-title h2,.careerfy-footer-title3 h2,.careerfy-footer-title4 h2'),
                    'units' => 'px',
                    'subtitle' => __('Typography options with each property can be called individually.', 'careerfy-frame'),
                    'default' => array(
                        'color' => '#ffffff',
                        'font-style' => 'normal',
                        'font-family' => 'Roboto',
                        'google' => true,
                        'font-size' => '18px',
                        'line-height' => '22px'
                    ),
                ),
            )
        );
        $setting_sections[] = $footer_sidebar_settings;

        $section_settings = array(
            'title' => __('Social Sharing', 'careerfy-frame'),
            'id' => 'social-sharing',
            'desc' => __('Select platforms to share your posts.', 'careerfy-frame'),
            'icon' => 'el el-share',
            'fields' => array(
                array(
                    'id' => 'careerfy-social-sharing-facebook',
                    'type' => 'button_set',
                    'title' => __('Facebook', 'careerfy-frame'),
                    'subtitle' => __('Social Sharing on Facebook.', 'careerfy-frame'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'careerfy-frame'),
                        'off' => __('Off', 'careerfy-frame'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'careerfy-social-sharing-twitter',
                    'type' => 'button_set',
                    'title' => __('Twitter', 'careerfy-frame'),
                    'subtitle' => __('Social Sharing on Twitter.', 'careerfy-frame'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'careerfy-frame'),
                        'off' => __('Off', 'careerfy-frame'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'careerfy-social-sharing-google',
                    'type' => 'button_set',
                    'title' => __('Google Plus', 'careerfy-frame'),
                    'subtitle' => __('Social Sharing on Google Plus.', 'careerfy-frame'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'careerfy-frame'),
                        'off' => __('Off', 'careerfy-frame'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'careerfy-social-sharing-tumblr',
                    'type' => 'button_set',
                    'title' => __('Tumblr', 'careerfy-frame'),
                    'subtitle' => __('Social Sharing on Tumblr.', 'careerfy-frame'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'careerfy-frame'),
                        'off' => __('Off', 'careerfy-frame'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'careerfy-social-sharing-dribbble',
                    'type' => 'button_set',
                    'title' => __('Dribbble', 'careerfy-frame'),
                    'subtitle' => __('Social Sharing on Dribbble.', 'careerfy-frame'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'careerfy-frame'),
                        'off' => __('Off', 'careerfy-frame'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'careerfy-social-sharing-stumbleupon',
                    'type' => 'button_set',
                    'title' => __('StumbleUpon', 'careerfy-frame'),
                    'subtitle' => __('Social Sharing on StumbleUpon.', 'careerfy-frame'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'careerfy-frame'),
                        'off' => __('Off', 'careerfy-frame'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'careerfy-social-sharing-youtube',
                    'type' => 'button_set',
                    'title' => __('Youtube', 'careerfy-frame'),
                    'subtitle' => __('Social Sharing on Youtube.', 'careerfy-frame'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'careerfy-frame'),
                        'off' => __('Off', 'careerfy-frame'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'careerfy-social-sharing-more',
                    'type' => 'button_set',
                    'title' => __('Share More', 'careerfy-frame'),
                    'subtitle' => __('Social Sharing on other platforms.', 'careerfy-frame'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'careerfy-frame'),
                        'off' => __('Off', 'careerfy-frame'),
                    ),
                    'default' => 'on',
                ),
            )
        );
        $setting_sections[] = $section_settings;

        $section_settings = array(
            'title' => __('Social Networking', 'careerfy-frame'),
            'id' => 'social-networking',
            'desc' => __('Set profile links for your Social Networking platforms.', 'careerfy-frame'),
            'icon' => 'el el-random',
            'fields' => array(
                array(
                    'id' => 'careerfy-social-networking-twitter',
                    'type' => 'text',
                    'title' => __('Twitter', 'careerfy-frame'),
                    'subtitle' => __('Set a profile link for Twitter.', 'careerfy-frame'),
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'careerfy-social-networking-facebook',
                    'type' => 'text',
                    'title' => __('Facebook', 'careerfy-frame'),
                    'subtitle' => __('Set a profile link for Facebook.', 'careerfy-frame'),
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'careerfy-social-networking-google',
                    'type' => 'text',
                    'title' => __('Google Plus', 'careerfy-frame'),
                    'subtitle' => __('Set a profile link for Google Plus.', 'careerfy-frame'),
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'careerfy-social-networking-youtube',
                    'type' => 'text',
                    'title' => __('YouTube', 'careerfy-frame'),
                    'subtitle' => __('Set a profile link for youtube.', 'careerfy-frame'),
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'careerfy-social-networking-vimeo',
                    'type' => 'text',
                    'title' => __('Vimeo', 'careerfy-frame'),
                    'subtitle' => __('Set a profile link for Vimeo.', 'careerfy-frame'),
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'careerfy-social-networking-linkedin',
                    'type' => 'text',
                    'title' => __('Linkedin', 'careerfy-frame'),
                    'subtitle' => __('Set a profile link for linkedin.', 'careerfy-frame'),
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'careerfy-social-networking-pinterest',
                    'type' => 'text',
                    'title' => __('Pinterest', 'careerfy-frame'),
                    'subtitle' => __('Set a profile link for Pinterest.', 'careerfy-frame'),
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'careerfy-social-networking-instagram',
                    'type' => 'text',
                    'title' => __('Instagram', 'careerfy-frame'),
                    'subtitle' => __('Set a profile link for Instagram.', 'careerfy-frame'),
                    'desc' => '',
                    'default' => '',
                ),
            )
        );
        $setting_sections[] = $section_settings;


        $careerfy_framework_mailchimp_list = array();
        if (isset($careerfy_framework_options['careerfy-mailchimp-api-key'])) {
            $mailchimp_key = $careerfy_framework_options['careerfy-mailchimp-api-key'];
            if ($mailchimp_key != '') {
                if (function_exists('careerfy_framework_mailchimp_list')) {
                    $mc_list = careerfy_framework_mailchimp_list($mailchimp_key);
                    if (is_array($mc_list) && isset($mc_list['data'])) {
                        foreach ($mc_list['data'] as $list) {
                            $careerfy_framework_mailchimp_list[$list['id']] = $list['name'];
                        }
                    }
                }
            }
        }

        $api_set_arr = array();
        $api_set_arr[] = array(
            'id' => 'flickr-api-section',
            'type' => 'section',
            'title' => __('Flickr API settings section.', 'careerfy-frame'),
            'subtitle' => '',
            'indent' => true,
        );
        $api_set_arr[] = array(
            'id' => 'careerfy-flickr-api',
            'type' => 'text',
            'transparent' => false,
            'title' => __('Flickr API', 'careerfy-frame'),
            'subtitle' => __('Set a Flickr API key.', 'careerfy-frame'),
            'desc' => '',
            'default' => ''
        );
        $show_twitter_apis = true;
        if (isset($jobsearch_plugin_options['jobsearch-twitter-consumer-key']) && $jobsearch_plugin_options['jobsearch-twitter-consumer-key'] != '') {
            $show_twitter_apis = false;
        }
        $api_set_arr[] = array(
            'id' => 'twitter-api-section',
            'type' => 'section',
            'title' => __('Twitter API settings section.', 'careerfy-frame'),
            'subtitle' => '',
            'indent' => true,
        );
        if ($show_twitter_apis === true) {
            $api_set_arr[] = array(
                'id' => 'careerfy-twitter-consumer-key',
                'type' => 'text',
                'transparent' => false,
                'title' => __('Consumer Key', 'careerfy-frame'),
                'subtitle' => __('Set Consumer Key for twitter.', 'careerfy-frame'),
                'desc' => '',
                'default' => ''
            );
            $api_set_arr[] = array(
                'id' => 'careerfy-twitter-consumer-secret',
                'type' => 'text',
                'transparent' => false,
                'title' => __('Consumer Secret', 'careerfy-frame'),
                'subtitle' => __('Set Consumer Secret for twitter.', 'careerfy-frame'),
                'desc' => '',
                'default' => ''
            );
            $api_set_arr[] = array(
                'id' => 'careerfy-twitter-access-token',
                'type' => 'text',
                'transparent' => false,
                'title' => __('Access Token', 'careerfy-frame'),
                'subtitle' => __('Set Access Token for twitter.', 'careerfy-frame'),
                'desc' => '',
                'default' => ''
            );
            $api_set_arr[] = array(
                'id' => 'careerfy-twitter-token-secret',
                'type' => 'text',
                'transparent' => false,
                'title' => __('Token Secret', 'careerfy-frame'),
                'subtitle' => __('Set Token Secret for twitter.', 'careerfy-frame'),
                'desc' => '',
                'default' => ''
            );
        }
        $api_set_arr[] = array(
            'id' => 'careerfy-twitter-num-tweets',
            'type' => 'text',
            'transparent' => false,
            'title' => __('Number of tweets', 'careerfy-frame'),
            'subtitle' => __('Please enter a number of tweets to show. i.e 5 for 5 tweets.', 'careerfy-frame'),
            'desc' => '',
            'default' => '5'
        );
        $api_set_arr[] = array(
            'id' => 'careerfy-twitter-cache-limit',
            'type' => 'text',
            'transparent' => false,
            'title' => __('Cache Time Limit', 'careerfy-frame'),
            'subtitle' => __('Please enter the time limit in minutes for refresh cache. i.e 30 for 30 Minutes', 'careerfy-frame'),
            'desc' => '',
            'default' => '30'
        );
        $api_set_arr[] = array(
            'id' => 'careerfy-twitter-time-format',
            'type' => 'select',
            'title' => __('Date Time Formate', 'careerfy-frame'),
            'subtitle' => __('Select date time formate for tweets.', 'careerfy-frame'),
            'desc' => '',
            'options' => array(
                'default' => __('Displays Like December 09, 2016', 'careerfy-frame'),
                'eng_suff' => __('Displays Like 6th December', 'careerfy-frame'),
                'ddmm' => __('Displays Like 09 Dec', 'careerfy-frame'),
                'ddmmyy' => __('Displays Like 09 Dec 2016', 'careerfy-frame'),
                'full_date' => __('Displays Like Tues 09 Dec 2016', 'careerfy-frame'),
                'time_since' => __('Displays in hours, minutes, etc', 'careerfy-frame'),
            ),
            'default' => 'default',
        );
        $api_set_arr[] = array(
            'id' => 'mailchimp-api-section',
            'type' => 'section',
            'title' => __('Mailchimp API settings section.', 'careerfy-frame'),
            'subtitle' => '',
            'indent' => true,
        );
        $api_set_arr[] = array(
            'id' => 'careerfy-mailchimp-api-key',
            'type' => 'text',
            'transparent' => false,
            'title' => __('API Key', 'careerfy-frame'),
            'subtitle' => __('Please enter the API key of your Mailchimp account.', 'careerfy-frame'),
            'desc' => '',
            'default' => ''
        );
        $api_set_arr[] = array(
            'id' => 'careerfy-mailchimp-list',
            'type' => 'select',
            'title' => __('Mailchimp List', 'careerfy-frame'),
            'subtitle' => __('Select Mailchimp List for subscribers.', 'careerfy-frame'),
            'desc' => '',
            'options' => $careerfy_framework_mailchimp_list,
            'default' => '',
        );

        $show_google_apis = true;
        if (isset($jobsearch_plugin_options['jobsearch-google-api-key']) && $jobsearch_plugin_options['jobsearch-google-api-key'] != '') {
            $show_google_apis = false;
        }
        if ($show_google_apis === true) {
            $api_set_arr[] = array(
                'id' => 'google-api-section',
                'type' => 'section',
                'title' => __('Google API settings section.', 'careerfy-frame'),
                'subtitle' => '',
                'indent' => true,
            );
            $api_set_arr[] = array(
                'id' => 'careerfy-google-api-key',
                'type' => 'text',
                'transparent' => false,
                'title' => __('API Key', 'careerfy-frame'),
                'subtitle' => __('Please enter the API key of your Google account.', 'careerfy-frame'),
                'desc' => '',
                'default' => ''
            );
        }
        $api_set_arr[] = array(
            'id' => 'instagram-api-section',
            'type' => 'section',
            'title' => __('Instagram API settings section.', 'careerfy-frame'),
            'subtitle' => '',
            'indent' => true,
        );
        $api_set_arr[] = array(
            'id' => 'instagram-client-id',
            'type' => 'text',
            'transparent' => false,
            'title' => __('Client ID', 'careerfy-frame'),
            'subtitle' => __('Please enter the Client ID of your Instagram app.', 'careerfy-frame'),
            'desc' => '',
            'default' => ''
        );
        $api_set_arr[] = array(
            'id' => 'instagram-client-secret',
            'type' => 'text',
            'transparent' => false,
            'title' => __('Client Secret', 'careerfy-frame'),
            'subtitle' => __('Please enter Client Secret of your Instagram app.', 'careerfy-frame'),
            'desc' => '',
            'default' => ''
        );
        $api_set_arr[] = array(
            'id' => 'instagram-redirect-uri',
            'type' => 'text',
            'transparent' => false,
            'title' => __('Redirect URI', 'careerfy-frame'),
            'subtitle' => __('Please set this URI to your Instagram app.', 'careerfy-frame'),
            'desc' => '',
            'default' => home_url('/')
        );

        $section_settings = array(
            'title' => __('API Settings', 'careerfy-frame'),
            'id' => 'api-settings',
            'desc' => __('Set API\'s for theme.', 'careerfy-frame'),
            'icon' => 'el el-idea',
            'fields' => $api_set_arr,
        );

        $instagram_access_token = isset($careerfy_framework_options['instagram-access-token']) ? $careerfy_framework_options['instagram-access-token'] : '';

        if ($instagram_access_token == '') {
            $section_settings['fields'][] = array(
                'id' => 'instagram-access_token-btn',
                'type' => 'button_set',
                'title' => __('Access Token', 'careerfy-frame'),
                'subtitle' => '',
                'desc' => '',
                'options' => array(
                    'access_token' => __('Get Access Token', 'careerfy-frame'),
                ),
                'default' => 'access_token'
            );
        } else {
            $section_settings['fields'][] = array(
                'id' => 'instagram-access-token',
                'type' => 'text',
                'title' => __('Access Token', 'careerfy-frame'),
                'subtitle' => '',
                'desc' => '',
                'default' => ''
            );
            $section_settings['fields'][] = array(
                'id' => 'instagram-user-id',
                'type' => 'text',
                'title' => __('User ID', 'careerfy-frame'),
                'subtitle' => '',
                'desc' => __('Put user id here. This user media will show in the Instagram gallery.', 'careerfy-frame'),
                'default' => ''
            );
        }
        $setting_sections[] = $section_settings;

        $careerfy_framework_google_api_key = isset($careerfy_framework_options['careerfy-google-api-key']) ? $careerfy_framework_options['careerfy-google-api-key'] : '';
        if (!function_exists('get_editable_roles')) {
            require_once ABSPATH . 'wp-admin/includes/user.php';
        }
        $tmp_roles = get_editable_roles();
        $roles = array();
        foreach ($tmp_roles as $tmp_role => $details) {
            $name = translate_user_role($details['name']);
            $roles[$tmp_role] = $name;
        }
        $time_array = careerfy_framework_get_times_array('+30 minutes');
        $section_settings = array(
            'title' => __('Maintenance Mode', 'careerfy-frame'),
            'id' => 'maintenance-mode',
            'desc' => __('Maintenance Mode for theme.', 'careerfy-frame'),
            'icon' => 'el el-align-justify',
            'fields' => array(
                array(
                    'id' => 'maintenance-mode-section',
                    'type' => 'section',
                    'title' => __('Maintenance Mode settings section.', 'careerfy-frame'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'maintenance-mode-switch',
                    'type' => 'button_set',
                    'title' => __('Status', 'careerfy-frame'),
                    'subtitle' => __('By enabling maintenance mode all users will not be able to access the site\'s content. They will only see the under construction page.', 'careerfy-frame'),
                    'desc' => '',
                    'options' => array(
                        '0' => __('Off', 'careerfy-frame'),
                        '1' => __('On', 'careerfy-frame'),
                    ),
                    'default' => '0',
                ),
                array(
                    'id' => 'maintenance-mode-date',
                    'type' => 'date',
                    'title' => __('Automatic End Date & Time', 'careerfy-frame'),
                    'desc' => '',
                    'subtitle' => __('If enabled, maintenance mode will automatically stop showing on the selected date. This option will not "auto-enable" maintenance mode. Status has to be set to "On".', 'careerfy-frame'),
                    'placeholder' => 'Click to enter an end date'
                ),
                array(
                    'id' => 'maintenance-mode-time',
                    'type' => 'select',
                    'title' => __('Time', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => $time_array,
                ),
                array(
                    'id' => 'maintenance-whitelisted-user',
                    'type' => 'select',
                    'title' => __('Whitelisted User Roles', 'careerfy-frame'),
                    'subtitle' => __('Selected user roles will not be affected by the maintenance mode and will always see the "normal" site. Default: administrator.', 'careerfy-frame'),
                    'desc' => '',
                    'multi' => true,
                    'options' => $roles,
                    'default' => 'off',
                ),
                array(
                    'id' => 'maintenance-whitelisted-ips',
                    'type' => 'textarea',
                    'title' => __('Whitelisted IP(ipv4) addresses', 'careerfy-frame'),
                    'subtitle' => __('Comma seperated IP addresses will not be affected by the maintenance mode and will always see the "normal" site.', 'careerfy-frame'),
                    'desc' => '',
                    'multi' => true,
                    'default' => '',
                ),
                array(
                    'id' => 'maintenance-background',
                    'type' => 'media',
                    'url' => true,
                    'title' => __('Background Image', 'careerfy-frame'),
                    'compiler' => 'true',
                    'desc' => __('For better design choose the background image for the template', 'careerfy-frame'),
                    'subtitle' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'maintenance-background-color',
                    'type' => 'color_rgba',
                    'url' => true,
                    'title' => __('Background Color', 'careerfy-frame'),
                    'compiler' => 'true',
                    'desc' => __('For better design choose the background color for the template', 'careerfy-frame'),
                    'subtitle' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'maintenance-mode-text-small-title',
                    'type' => 'text',
                    'title' => __('Template Small Title', 'careerfy-frame'),
                    'subtitle' => __('You can set a small title for template', 'careerfy-frame'),
                    'desc' => __('', 'careerfy-frame'),
                    'default' => 'OUR WEBSITE IS',
                ),
                array(
                    'id' => 'maintenance-mode-text-large-title',
                    'type' => 'text',
                    'title' => __('Template large Title', 'careerfy-frame'),
                    'subtitle' => __('You can set a large title for template', 'careerfy-frame'),
                    'desc' => __('', 'careerfy-frame'),
                    'default' => 'COMING SOON',
                ),
                array(
                    'id' => 'maintenance-mode-text-content',
                    'type' => 'editor',
                    'title' => __('Template Content', 'careerfy-frame'),
                    'subtitle' => __('You can set complete content for template', 'careerfy-frame'),
                    'default' => '<p><span>Keep Me Updated!</span>Please fill in your e-mail address so you\'ll be noticed when website is up:</p>',
                    'args' => array(
                        'teeny' => true,
                        'textarea_rows' => 10
                    )
                ),
                array(
                    'id' => 'maintenance-social-icons',
                    'type' => 'button_set',
                    'title' => __('Social Network', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => array(
                        'off' => __('Off', 'careerfy-frame'),
                        'on' => __('On', 'careerfy-frame'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'maintenance-mode-pagemeta-switch',
                    'type' => 'button_set',
                    'title' => __('Pages Coming Soon Option', 'careerfy-frame'),
                    'subtitle' => __('By enabling coming soon mode all pages will allow the comming soon option in every page.', 'careerfy-frame'),
                    'desc' => '',
                    'options' => array(
                        '0' => __('Off', 'careerfy-frame'),
                        '1' => __('On', 'careerfy-frame'),
                    ),
                    'default' => '0',
                ),
            ),
        );
        $setting_sections[] = $section_settings;

        // footer section start
        $sidebars_array = array('' => esc_html__('Select Sidebar', 'careerfy-frame'));
        $careerfy_framework_sidebars = isset($careerfy_framework_options['careerfy-themes-sidebars']) ? $careerfy_framework_options['careerfy-themes-sidebars'] : '';
        if (is_array($careerfy_framework_sidebars) && sizeof($careerfy_framework_sidebars) > 0) {
            foreach ($careerfy_framework_sidebars as $sidebar) {
                $sidebars_array[sanitize_title($sidebar)] = $sidebar;
            }
        }
        $sidebar_opt_settings = array(
            'title' => __('Layouts', 'careerfy-frame'),
            'id' => 'themes-layouts',
            'desc' => __('Set Theme layouts and sidebars list.', 'careerfy-frame'),
            'icon' => 'el el-pause',
            'fields' => array(
                array(
                    'id' => 'careerfy-themes-sidebars',
                    'type' => 'multi_text',
                    'title' => __('Sidebars', 'careerfy-frame'),
                    'subtitle' => __('Create a Dynamic List of Sidebars.', 'careerfy-frame'),
                    'desc' => __('These Sidebars will list in Theme Appearance >> Widgets.', 'careerfy-frame'),
                    'default' => '',
                ),
                array(
                    'id' => 'careerfy-default-layout',
                    'type' => 'image_select',
                    'title' => __('Select Layout', 'careerfy-frame'),
                    'subtitle' => '',
                    'desc' => __('Select default Layout for default pages.', 'careerfy-frame'),
                    'options' => array(
                        'full' => array(
                            'alt' => __('Full Width', 'careerfy-frame'),
                            'img' => ReduxFramework::$_url . 'assets/img/1col.png'
                        ),
                        'right' => array(
                            'alt' => __('Right Sidebar', 'careerfy-frame'),
                            'img' => ReduxFramework::$_url . 'assets/img/2cr.png'
                        ),
                        'left' => array(
                            'alt' => __('Left Sidebar', 'careerfy-frame'),
                            'img' => ReduxFramework::$_url . 'assets/img/2cl.png'
                        ),
                    ),
                    'default' => ''
                ),
                array(
                    'id' => 'careerfy-default-sidebar',
                    'type' => 'select',
                    'title' => __('Select Sidebar', 'careerfy-frame'),
                    'required' => array('careerfy-default-layout', '!=', 'full'),
                    'subtitle' => '',
                    'desc' => __('Select default Sidebars for default pages.', 'careerfy-frame'),
                    'options' => $sidebars_array,
                    'default' => ''
                ),
            )
        );

        // woocommerce settings
        $sidebar_opt_settings['fields'][] = array(
            'id' => 'careerfy-wooc-layout',
            'type' => 'image_select',
            'title' => __('Select Shop Layout', 'careerfy-frame'),
            'subtitle' => '',
            'desc' => __('Select default Layout for Shop pages.', 'careerfy-frame'),
            'options' => array(
                'full' => array(
                    'alt' => __('Full Width', 'careerfy-frame'),
                    'img' => ReduxFramework::$_url . 'assets/img/1col.png'
                ),
                'right' => array(
                    'alt' => __('Right Sidebar', 'careerfy-frame'),
                    'img' => ReduxFramework::$_url . 'assets/img/2cr.png'
                ),
                'left' => array(
                    'alt' => __('Left Sidebar', 'careerfy-frame'),
                    'img' => ReduxFramework::$_url . 'assets/img/2cl.png'
                ),
            ),
            'default' => ''
        );
        $sidebar_opt_settings['fields'][] = array(
            'id' => 'careerfy-wooc-sidebar',
            'type' => 'select',
            'title' => __('Select Shop Sidebar', 'careerfy-frame'),
            'required' => array('careerfy-wooc-layout', '!=', 'full'),
            'subtitle' => '',
            'desc' => __('Select default Sidebars for Shop pages.', 'careerfy-frame'),
            'options' => $sidebars_array,
            'default' => ''
        );

        $setting_sections[] = $sidebar_opt_settings;

        $footer_sidebar_settings = array(
            'title' => __('Custom Js', 'careerfy-frame'),
            'id' => 'custom-css-js',
            'desc' => __('Add Custom Js code.', 'careerfy-frame'),
            'icon' => 'el el-edit',
            'fields' => array(
                array(
                    'id' => 'javascript_editor',
                    'type' => 'ace_editor',
                    'title' => __('Js Code', 'careerfy-frame'),
                    'subtitle' => __('Paste your Js code here.', 'careerfy-frame'),
                    'mode' => 'javascript',
                    'theme' => 'chrome',
                    'desc' => __('Possible modes can be found at <a href="http://ace.c9.io" target="_blank">http://ace.c9.io/</a>.', 'careerfy-frame'),
                    'default' => "jQuery(document).ready(function(){\n\n});"
                ),
            )
        );
        $setting_sections[] = $footer_sidebar_settings;

        return $setting_sections;
    }
}