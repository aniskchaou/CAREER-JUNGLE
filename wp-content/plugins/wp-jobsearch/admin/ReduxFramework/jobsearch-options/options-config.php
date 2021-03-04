<?php

defined('ABSPATH') || exit;

if (!class_exists('Redux')) {
    return;
}

// This is your option name where all the Redux data is stored.
$opt_name = 'jobsearch_plugin_options';

$args = array(
    // This is where your data is stored in the database and also becomes your global variable name.
    'opt_name' => $opt_name,
    // Name that appears at the top of your panel.
    'display_name' => esc_html__('JobSearch Options', 'wp-jobsearch'),
    // Version that appears at the top of your panel.
    'display_version' => JobSearch_plugin::get_version(),
    // Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only).
    'menu_type' => 'menu',
    // Show the sections below the admin menu item or not.
    'allow_sub_menu' => true,
    // The text to appear in the admin menu.
    'menu_title' => esc_html__('JobSearch Options', 'wp-jobsearch'),
    // The text to appear on the page title.
    'page_title' => esc_html__('JobSearch Options', 'wp-jobsearch'),
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
    
    $all_page = array();
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
    
    if (is_admin()) {
        $pages = get_pages($args);
        if (!empty($pages)) {
            $all_page[''] = __('Select Page', 'wp-jobsearch');
            foreach ($pages as $page) {
                $all_page[$page->post_name] = $page->post_title;
            }
        }
    }
    
    $sec_array = array();
    $sec_array[] = array(
        'id' => 'status-settings-section',
        'type' => 'section',
        'title' => __('Status Settings', 'wp-jobsearch'),
        'subtitle' => __('Status settings.', 'wp-jobsearch'),
        'indent' => true,
    );
    $sec_array[] = array(
        'id' => 'jobsearch-approved-color',
        'type' => 'color',
        'transparent' => false,
        'title' => __('Approved Color', 'wp-jobsearch'),
        'subtitle' => __('Approved Status Color.', 'wp-jobsearch'),
        'desc' => '',
        'default' => '#0cd61a'
    );
    $sec_array[] = array(
        'id' => 'jobsearch-pending-color',
        'type' => 'color',
        'transparent' => false,
        'title' => __('Pending Color', 'wp-jobsearch'),
        'subtitle' => __('Pending Status Color.', 'wp-jobsearch'),
        'desc' => '',
        'default' => '#110de2'
    );
    $sec_array[] = array(
        'id' => 'jobsearch-canceled-color',
        'type' => 'color',
        'transparent' => false,
        'title' => __('Canceled Color', 'wp-jobsearch'),
        'subtitle' => __('Canceled Status Color.', 'wp-jobsearch'),
        'desc' => '',
        'default' => '#e50d0d'
    );
    $sec_array = apply_filters('jobsearch_pluginopts_genral_aftr_status_seting', $sec_array);
    $sec_array[] = array(
        'id' => 'sectorscat-settings-section',
        'type' => 'section',
        'title' => __('Sectors Settings', 'wp-jobsearch'),
        'subtitle' => '',
        'indent' => true,
    );
    $sec_array[] = array(
        'id' => 'sectors_onoff_switch',
        'type' => 'button_set',
        'title' => __('Job Sectors On/Off', 'wp-jobsearch'),
        'subtitle' => __('It will disable sectors from jobs.', 'wp-jobsearch'),
        'desc' => '',
        'options' => array(
            'on' => __('On', 'wp-jobsearch'),
            'off' => __('Off', 'wp-jobsearch'),
        ),
        'default' => 'on',
    );
    $sec_array[] = array(
        'id' => 'usersector_onoff_switch',
        'type' => 'button_set',
        'title' => __('Members Sectors On/Off', 'wp-jobsearch'),
        'subtitle' => __('It will disable sectors from candidates/employers.', 'wp-jobsearch'),
        'desc' => '',
        'options' => array(
            'on_cand' => __('On for Candidates only', 'wp-jobsearch'),
            'on_emp' => __('On for Employers only', 'wp-jobsearch'),
            'on_both' => __('On for both', 'wp-jobsearch'),
            'off' => __('Off for both', 'wp-jobsearch'),
        ),
        'default' => 'on_both',
    );
    $sec_array[] = array(
        'id' => 'email-log-settings-section',
        'type' => 'section',
        'title' => __('Email Log Settings', 'wp-jobsearch'),
        'subtitle' => __('Email log settings.', 'wp-jobsearch'),
        'indent' => true,
    );
    $sec_array[] = array(
        'id' => 'jobsearch-email-log-switch',
        'type' => 'button_set',
        'title' => __('Email Log Switch', 'wp-jobsearch'),
        'subtitle' => __('If you want to log every email then switch on.', 'wp-jobsearch'),
        'desc' => '',
        'options' => array(
            'on' => __('On', 'wp-jobsearch'),
            'off' => __('Off', 'wp-jobsearch'),
        ),
        'default' => 'off',
    );
    $sec_array[] = array(
        'id' => 'salary-types-settings-section',
        'type' => 'section',
        'title' => __('Salary', 'wp-jobsearch'),
        'subtitle' => __('Default salary settings for jobs and candidates.', 'wp-jobsearch'),
        'indent' => true,
    );
    $sec_array[] = array(
        'id' => 'job_custom_currency',
        'type' => 'button_set',
        'title' => __('Salary Custom Currency', 'wp-jobsearch'),
        'desc' => '',
        'subtitle' => __('Allow users to select Custom Currency for job and candidate salary.', 'wp-jobsearch'),
        'options' => array(
            'on' => __('On', 'wp-jobsearch'),
            'off' => __('Off', 'wp-jobsearch'),
        ),
        'default' => 'off',
    );
    $sec_array[] = array(
        'id' => 'job-salary-types',
        'type' => 'multi_text',
        'title' => __('Salary Types', 'wp-jobsearch'),
        'desc' => '',
        'default' => array(__('Monthly', 'wp-jobsearch'), __('Weekly', 'wp-jobsearch'), __('Hourly', 'wp-jobsearch')),
        'subtitle' => __('Set salary types.', 'wp-jobsearch'),
    );
    $sec_array[] = array(
        'id' => 'default-view-settings-section',
        'type' => 'section',
        'title' => __('Default View Settings', 'wp-jobsearch'),
        'subtitle' => __('Default view settings.', 'wp-jobsearch'),
        'indent' => true,
    );
    $sec_array[] = array(
        'id' => 'jobsearch-default-page-view',
        'type' => 'button_set',
        'title' => __('Default View', 'wp-jobsearch'),
        'subtitle' => __('If you want to change the plugin default pages view.', 'wp-jobsearch'),
        'desc' => '',
        'options' => array(
            'full' => __('Full Width', 'wp-jobsearch'),
            'boxed' => __('Boxed', 'wp-jobsearch'),
        ),
        'default' => 'full',
    );
    $sec_array[] = array(
        'id' => 'jobsearch-boxed-view-width',
        'type' => 'text',
        'title' => __('Boxed View Width', 'wp-jobsearch'),
        'subtitle' => __("Boxed view default width with unit like px, pt...etc, it will only apply on 'Boxed' view.", 'wp-jobsearch'),
        'desc' => '',
        'default' => '1140px',
    );
    $sec_array[] = array(
        'id' => 'terms-cond-page-section',
        'type' => 'section',
        'title' => __('Terms and Conditions', 'wp-jobsearch'),
        'subtitle' => __('Select page for terms and conditions. You can create new page from pages.', 'wp-jobsearch'),
        'indent' => true,
    );
    $sec_array[] = array(
        'id' => 'terms_conditions_page',
        'type' => 'select',
        'title' => __('Terms and Conditions Page', 'wp-jobsearch'),
        'subtitle' => __('Select Terms and Conditions Page.', 'wp-jobsearch'),
        'desc' => '',
        'options' => $all_page,
        'default' => '',
    );
    
    $redux_genral_options = array(
        'title' => __('General Options', 'wp-jobsearch'),
        'id' => 'general-options',
        'desc' => __('These are really basic options!', 'wp-jobsearch'),
        'icon' => 'el el-home',
        'fields' => apply_filters('jobsearch_options_general_opt_fields', $sec_array),
    );
    Redux::set_section($opt_name, $redux_genral_options);
    
    add_filter('redux/options/jobsearch_plugin_options/sections', 'jobsearch_plugin_core_settings', 1);
    
    function jobsearch_plugin_core_settings($setting_sections) {
        
        global $wpdb;

        $jobsearch__options = get_option('jobsearch_plugin_options');

        $jobdesc_temps = array();
        $job_tmpargs = array(
            'post_type' => 'jobdesctemp',
            'posts_per_page' => 100,
            'post_status' => 'publish',
            'fields' => 'ids',
            'order' => 'DESC',
            'orderby' => 'ID',
        );
        $temps_query = new WP_Query($job_tmpargs);
        $temps_posts = $temps_query->posts;
        if (!empty($temps_posts)) {
            foreach ($temps_posts as $temps_postid) {
                $jobdesc_temps[$temps_postid] = get_the_title($temps_postid);
            }
        }

        wp_reset_postdata();
        $cv_pckgs = array();
        $args = array(
            'post_type' => 'package',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
            'order' => 'ASC',
            'orderby' => 'title',
            'meta_query' => array(
                array(
                    'key' => 'jobsearch_field_package_type',
                    'value' => array('cv', 'emp_allin_one', 'employer_profile'),
                    'compare' => 'IN',
                ),
            ),
        );
        $pkgs_query = new WP_Query($args);

        if ($pkgs_query->found_posts > 0) {
            $pkgs_list = $pkgs_query->posts;

            if (!empty($pkgs_list)) {
                foreach ($pkgs_list as $pkg_item) {
                    $pkg_attach_product = get_post_meta($pkg_item, 'jobsearch_package_product', true);

                    if ($pkg_attach_product != '' && get_page_by_path($pkg_attach_product, 'OBJECT', 'product')) {
                        $cv_pkg_post = get_post($pkg_item);
                        $cv_pkg_post_name = isset($cv_pkg_post->post_name) ? $cv_pkg_post->post_name : '';
                        $cv_pckgs[$cv_pkg_post_name] = $cv_pkg_post->post_title;
                    }
                }
            }
        }
        wp_reset_postdata();

        $all_page = array();
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
            $all_page[''] = __('Select Page', 'wp-jobsearch');
            foreach ($pages as $page) {
                $all_page[$page->post_name] = $page->post_title;
            }
        }

        $api_fields_sec = array();
        $api_fields_sec[] = array(
            'id' => 'twitter-api-section',
            'type' => 'section',
            'title' => __('Twitter API settings section.', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Callback URL is: %s', 'wp-jobsearch'), home_url('/')),
            'indent' => true,
        );
        $api_fields_sec[] = array(
            'id' => 'jobsearch-twitter-consumer-key',
            'type' => 'text',
            'transparent' => false,
            'title' => __('Consumer Key', 'wp-jobsearch'),
            'subtitle' => __('Set Consumer Key for twitter.', 'wp-jobsearch'),
            'desc' => '',
            'default' => ''
        );
        $api_fields_sec[] = array(
            'id' => 'jobsearch-twitter-consumer-secret',
            'type' => 'text',
            'transparent' => false,
            'title' => __('Consumer Secret', 'wp-jobsearch'),
            'subtitle' => __('Set Consumer Secret for twitter.', 'wp-jobsearch'),
            'desc' => '',
            'default' => ''
        );
        $api_fields_sec[] = array(
            'id' => 'jobsearch-twitter-access-token',
            'type' => 'text',
            'transparent' => false,
            'title' => __('Access Token', 'wp-jobsearch'),
            'subtitle' => __('Set Access Token for twitter.', 'wp-jobsearch'),
            'desc' => '',
            'default' => ''
        );
        $api_fields_sec[] = array(
            'id' => 'jobsearch-twitter-token-secret',
            'type' => 'text',
            'transparent' => false,
            'title' => __('Token Secret', 'wp-jobsearch'),
            'subtitle' => __('Set Token Secret for twitter.', 'wp-jobsearch'),
            'desc' => '',
            'default' => ''
        );
        $api_fields_sec[] = array(
            'id' => 'google-captcha-api-section',
            'type' => 'section',
            'title' => __('Google Captcha API settings section.', 'wp-jobsearch'),
            'subtitle' => '',
            'indent' => true,
        );
        $api_fields_sec[] = array(
            'id' => 'captcha_switch',
            'type' => 'button_set',
            'title' => __('Google Captcha', 'wp-jobsearch'),
            'subtitle' => __('Google Captcha Switch.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $api_fields_sec[] = array(
            'id' => 'captcha_sitekey',
            'type' => 'text',
            'transparent' => false,
            'title' => __('Site Key', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Put your site key for captcha. You can get this site key after registering your site on Google. Make sure while create new site in captcha select version v2.', 'wp-jobsearch'),
            'default' => ''
        );
        $api_fields_sec[] = array(
            'id' => 'captcha_secretkey',
            'type' => 'text',
            'transparent' => false,
            'title' => __('Secret Key', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Put your site Secret key for captcha. You can get this Secret Key after registering your site on Google.', 'wp-jobsearch'),
            'default' => ''
        );
        $api_fields_sec[] = array(
            'id' => 'mapbox-api-section',
            'type' => 'section',
            'title' => __('MapBox settings.', 'wp-jobsearch'),
            'subtitle' => '',
            'indent' => true,
        );
        $api_fields_sec[] = array(
            'id' => 'mapbox_access_token',
            'type' => 'text',
            'transparent' => false,
            'title' => __('MapBox Access Token', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Put MapBox Access Token here. Get your MapBox Access Token here <a href="https://www.mapbox.com/" target="_blank">www.mapbox.com/</a>', 'wp-jobsearch'),
            'default' => ''
        );
        $api_fields_sec = apply_filters('jobsearch_api_sett_b4rgoogle_sec', $api_fields_sec);
        $api_fields_sec[] = array(
            'id' => 'google-api-section',
            'type' => 'section',
            'title' => __('Google API settings section.', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Callback URL is: %s', 'wp-jobsearch'), home_url('/')),
            'indent' => true,
        );
        $api_fields_sec[] = array(
            'id' => 'jobsearch-google-api-key',
            'type' => 'text',
            'transparent' => false,
            'title' => __('API Key', 'wp-jobsearch'),
            'subtitle' => __('Please enter the API key of your Google account.', 'wp-jobsearch'),
            'desc' => '',
            'default' => ''
        );

        $section_settings = array(
            'title' => __('API Settings', 'wp-jobsearch'),
            'id' => 'api-settings',
            'desc' => __('Set API\'s for theme.', 'wp-jobsearch'),
            'icon' => 'el el-idea',
            'fields' => $api_fields_sec,
        );

        $setting_sections[] = apply_filters('jobsearch_api_settings_section', $section_settings);
        //

        $section_settings = apply_filters('jobsearch_login_settings_section', array());

        if (isset($section_settings['title'])) {
            $setting_sections[] = $section_settings;
        }

        $cand_custom_fileds = $empl_custom_fileds = array();
        $custom_fields_candidate = get_option('jobsearch_custom_field_candidate');
        if (is_array($custom_fields_candidate) && sizeof($custom_fields_candidate) > 0) {
            foreach ($custom_fields_candidate as $post_cf) {
                $field_name = isset($post_cf['name']) ? $post_cf['name'] : '';
                $field_label = isset($post_cf['label']) ? $post_cf['label'] : '';
                if ($field_label != '') {
                    $cand_custom_fileds[$field_name] = $field_label;
                }
            }
        }
        $custom_fields_employer = get_option('jobsearch_custom_field_employer');
        if (is_array($custom_fields_employer) && sizeof($custom_fields_employer) > 0) {
            foreach ($custom_fields_employer as $post_cf) {
                $field_name = isset($post_cf['name']) ? $post_cf['name'] : '';
                $field_label = isset($post_cf['label']) ? $post_cf['label'] : '';
                if ($field_name != '' && $field_label != '') {
                    $empl_custom_fileds[$field_name] = $field_label;
                }
            }
        }
        $section_settings = array(
            'title' => __('Register Settings', 'wp-jobsearch'),
            'id' => 'sign-up-settings',
            'desc' => __('Register Settings', 'wp-jobsearch'),
            'icon' => 'el el-group-alt',
            'fields' => apply_filters('jobsearch_options_signup_setings_fields', array(
                array(
                    'id' => 'login_register_form',
                    'type' => 'button_set',
                    'title' => __('Register Form', 'wp-jobsearch'),
                    'subtitle' => __('Allow users to register a new account.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'login_candidate_register',
                    'type' => 'button_set',
                    'title' => __('Enable Candidate Registration', 'wp-jobsearch'),
                    'subtitle' => __('Allow users to register a candidate account.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'login_employer_register',
                    'type' => 'button_set',
                    'title' => __('Enable Employer Registration', 'wp-jobsearch'),
                    'subtitle' => __('Allow users to register an employer account.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'signup_user_flname',
                    'type' => 'button_set',
                    'title' => __('First/Last Name', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Allow users to put first and last name in register form.', 'wp-jobsearch'),
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'signup_username_allow',
                    'type' => 'button_set',
                    'title' => __('Username from User', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Allow users to set username from register form.', 'wp-jobsearch'),
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'signup_user_password',
                    'type' => 'button_set',
                    'title' => __('Password from User', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Allow users to set passwords from the registration form.', 'wp-jobsearch'),
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'accptable_pass_strength',
                    'type' => 'button_set',
                    'multi' => true,
                    'required' => array('signup_user_password', 'equals', 'on'),
                    'title' => __('Acceptable Password Lengths', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Select multiple acceptable password lengths.', 'wp-jobsearch'),
                    'options' => array(
                        'very_weak' => __('Very Weak', 'wp-jobsearch'),
                        'weak' => __('Weak', 'wp-jobsearch'),
                        'medium' => __('Medium', 'wp-jobsearch'),
                        'strong' => __('Strong', 'wp-jobsearch'),
                    ),
                    'default' => array('very_weak', 'weak', 'medium', 'strong'),
                ),
                array(
                    'id' => 'signup_custom_fields',
                    'type' => 'button_set',
                    'title' => __('Custom Fields in Register Form', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Allow Custom Fields in Register Form.', 'wp-jobsearch'),
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'candidate_custom_fields',
                    'type' => 'select',
                    'multi' => true,
                    'title' => __('Candidate Custom Fields', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => $cand_custom_fileds,
                    'default' => '',
                    'subtitle' => __('Select Candidate Custom Fields which will show in the registration form.', 'wp-jobsearch'),
                ),
                array(
                    'id' => 'employer_custom_fields',
                    'type' => 'select',
                    'multi' => true,
                    'title' => __('Employer Custom Fields', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => $empl_custom_fileds,
                    'default' => '',
                    'subtitle' => __('Select Employer Custom Fields which will show in the registration form.', 'wp-jobsearch'),
                ),
                array(
                    'id' => 'signup_user_sector',
                    'type' => 'button_set',
                    'title' => __('User Sector', 'wp-jobsearch'),
                    'subtitle' => __('Allow employer and candidates to select Sector while register new account.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'cand' => __('On for candidates only', 'wp-jobsearch'),
                        'emp' => __('On for employers only', 'wp-jobsearch'),
                        'on' => __('On for both', 'wp-jobsearch'),
                        'off' => __('Off for both', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'signup_sector_selct_method',
                    'type' => 'button_set',
                    'required' => array('signup_user_sector', '!=', 'off'),
                    'title' => __('Sector Select Method', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Set sector select method Single/Multiple while register account.', 'wp-jobsearch'),
                    'options' => array(
                        'single' => __('Single', 'wp-jobsearch'),
                        'multi' => __('Multi', 'wp-jobsearch'),
                        'single_req' => __('Single & Required', 'wp-jobsearch'),
                        'multi_req' => __('Multi & Required', 'wp-jobsearch'),
                    ),
                    'default' => 'single',
                ),
                array(
                    'id' => 'signup_user_phone',
                    'type' => 'button_set',
                    'title' => __('User Phone Number', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'on_req' => __('On & Required', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'signup_public_profile_visibility',
                    'type' => 'button_set',
                    'title' => __('User Public Profile Visibility', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => array(
                        'off' => __('Off', 'wp-jobsearch'),
                        'for_emp' => __('For Employer', 'wp-jobsearch'),
                        'for_cand' => __('For Candidate', 'wp-jobsearch'),
                        'for_both' => __('For Both', 'wp-jobsearch'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'signup_organization_name',
                    'type' => 'button_set',
                    'title' => __('Organization Name', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Get Organization Name from employer.', 'wp-jobsearch'),
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'signup_cv_upload',
                    'type' => 'button_set',
                    'title' => __('Resume Upload', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Resume Upload field for candidate signup.', 'wp-jobsearch'),
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'on_req' => __('On & Required', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'off',
                ),
            )),
        );
        $setting_sections[] = $section_settings;

        $all_page = array();
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
            $all_page[''] = __('Select Page', 'wp-jobsearch');
            foreach ($pages as $page) {
                $all_page[$page->post_name] = $page->post_title;
            }
        }
        $gen_dashbord_sett = array();
        $gen_dashbord_sett = apply_filters('jobsearch_dash_opts_gen_before_setts', $gen_dashbord_sett);
        $gen_dashbord_sett[] = array(
            'id' => 'user-dashboard-template-page',
            'type' => 'select',
            'title' => __('User Dashboard Page', 'wp-jobsearch'),
            'subtitle' => __('Select the User Dashboard Page.', 'wp-jobsearch'),
            'desc' => '',
            'options' => $all_page,
            'default' => '',
        );
        $gen_dashbord_sett[] = array(
            'id' => 'cand-login-redirect-url',
            'type' => 'text',
            'title' => __('Candidate Redirect URL after Login', 'wp-jobsearch'),
            'subtitle' => __('Set Candidate Redirect URL after Login.', 'wp-jobsearch'),
            'desc' => '',
            'default' => '',
        );
        $gen_dashbord_sett[] = array(
            'id' => 'emp-login-redirect-url',
            'type' => 'text',
            'title' => __('Employer Redirect URL after Login', 'wp-jobsearch'),
            'subtitle' => __('Set Employer Redirect URL after Login.', 'wp-jobsearch'),
            'desc' => '',
            'default' => '',
        );
        $gen_dashbord_sett[] = array(
            'id' => 'user-dashboard-per-page',
            'type' => 'text',
            'title' => __('Resluts Per Page', 'wp-jobsearch'),
            'subtitle' => __('Set Resluts Per Page in user dashboard pages.', 'wp-jobsearch'),
            'desc' => '',
            'default' => '10',
        );
        $gen_dashbord_sett[] = array(
            'id' => 'profile_photo_file_size',
            'type' => 'select',
            'title' => __('Profile Photo File Size', 'wp-jobsearch'),
            'subtitle' => __('Profile photo image file size for candidates and employers.', 'wp-jobsearch'),
            'options' => array(
                '300' => __('300KB', 'wp-jobsearch'),
                '500' => __('500KB', 'wp-jobsearch'),
                '750' => __('750KB', 'wp-jobsearch'),
                '1024' => __('1Mb', 'wp-jobsearch'),
                '2048' => __('2Mb', 'wp-jobsearch'),
                '3072' => __('3Mb', 'wp-jobsearch'),
                '4096' => __('4Mb', 'wp-jobsearch'),
                '5120' => __('5Mb', 'wp-jobsearch'),
                '10120' => __('10Mb', 'wp-jobsearch'),
                '50120' => __('50Mb', 'wp-jobsearch'),
                '100120' => __('100Mb', 'wp-jobsearch'),
                '200120' => __('200Mb', 'wp-jobsearch'),
                '300120' => __('300Mb', 'wp-jobsearch'),
                '500120' => __('500Mb', 'wp-jobsearch'),
                '1000120' => __('1Gb', 'wp-jobsearch'),
            ),
            'desc' => '',
            'default' => '5120',
        );
        $gen_dashbord_sett[] = array(
            'id' => 'cvr_photo_file_size',
            'type' => 'select',
            'title' => __('Cover Photo File Size', 'wp-jobsearch'),
            'subtitle' => __('Cover photo image file size for candidates and employers.', 'wp-jobsearch'),
            'options' => array(
                '300' => __('300KB', 'wp-jobsearch'),
                '500' => __('500KB', 'wp-jobsearch'),
                '750' => __('750KB', 'wp-jobsearch'),
                '1024' => __('1Mb', 'wp-jobsearch'),
                '2048' => __('2Mb', 'wp-jobsearch'),
                '3072' => __('3Mb', 'wp-jobsearch'),
                '4096' => __('4Mb', 'wp-jobsearch'),
                '5120' => __('5Mb', 'wp-jobsearch'),
                '10120' => __('10Mb', 'wp-jobsearch'),
                '50120' => __('50Mb', 'wp-jobsearch'),
                '100120' => __('100Mb', 'wp-jobsearch'),
                '200120' => __('200Mb', 'wp-jobsearch'),
                '300120' => __('300Mb', 'wp-jobsearch'),
                '500120' => __('500Mb', 'wp-jobsearch'),
                '1000120' => __('1Gb', 'wp-jobsearch'),
            ),
            'desc' => '',
            'default' => '5120',
        );

        $gen_dashbord_sett[] = array(
            'id' => 'intltell_phone_validation',
            'type' => 'button_set',
            'title' => __('Country Code Phone Validation', 'wp-jobsearch'),
            'subtitle' => __('If this option is enabled user will not able to put invalid numbers.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $gen_dashbord_sett[] = array(
            'id' => 'user_delprofile_switch',
            'type' => 'button_set',
            'title' => __('User Delete Profile', 'wp-jobsearch'),
            'subtitle' => __('On/Off User Delete Profile option in dashboard.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $gen_dashbord_sett[] = array(
            'id' => 'user_stats_switch',
            'type' => 'button_set',
            'title' => __('User Statistics', 'wp-jobsearch'),
            'subtitle' => __('On/Off User Statistics in dashboard.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $gen_dashbord_sett[] = array(
            'id' => 'security-questions-switch',
            'type' => 'button_set',
            'title' => __('Security Questions', 'wp-jobsearch'),
            'subtitle' => __('Add security questions for password change in the user dashboard.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $gen_dashbord_sett[] = array(
            'id' => 'jobsearch-security-questions',
            'type' => 'multi_text',
            'title' => __('Questions', 'wp-jobsearch'),
            'subtitle' => __('Create Dynamic List of Questions.', 'wp-jobsearch'),
            'desc' => __('These Questions will use for security purposes like password change.', 'wp-jobsearch'),
            'default' => array(
                __('What is your first pet name?', 'wp-jobsearch'),
                __('What is your uncle&apos;s name?', 'wp-jobsearch'),
                __('What is your teacher&apos;s name?', 'wp-jobsearch'),
                __('What is your place of birth?', 'wp-jobsearch'),
            ),
        );

        $section_settings = array(
            'title' => __('Dashboard Settings', 'wp-jobsearch'),
            'id' => 'user-dashboard-general',
            'desc' => __('Members Dashboard Settings', 'wp-jobsearch'),
            'icon' => 'el el-graph',
            'fields' => $gen_dashbord_sett,
        );
        $setting_sections[] = $section_settings;

        //
        $cand_dashbord_sett = array();
        $cand_dashbord_sett[] = array(
            'id' => 'candidate_auto_approve',
            'type' => 'button_set',
            'title' => __('Candidate Approval', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Set the candidate approval method after registration.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('Auto', 'wp-jobsearch'),
                'off' => __('Admin Review', 'wp-jobsearch'),
                'email' => __('Auto with Email', 'wp-jobsearch'),
                'admin_email' => __('Admin Review with Email', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett = apply_filters('jobsearch_joptons_candash_setbefore_ovrlaycolr', $cand_dashbord_sett);
        $cand_dashbord_sett[] = array(
            'id' => 'unapproverd_candidate_txt',
            'type' => 'editor',
            'args' => array(
                'teeny' => true,
                'media_buttons' => false,
            ),
            'title' => __('Unapproved Candidate Text', 'wp-jobsearch'),
            'required' => array(
                array('candidate_auto_approve', '!=', 'on'),
                array('candidate_auto_approve', '!=', 'email'),
            ),
            'subtitle' => __('This text will show in unapproved candidate dashboard.', 'wp-jobsearch'),
            'desc' => '',
            'default' => '<strong>ACCOUNT ACTIVATION REQUIRED BY ADMIN !</strong>

        <strong>Your account is In-active!</strong>

        Your membership account is awaiting approval by the site administrator. You will not be able to fully interact with the account functions and aspects of this website until your account is approved. Once approved by admin or denied you will receive an email notice.',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'candidate_cover_img_switch',
            'type' => 'button_set',
            'title' => __('Candidate Cover Image', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('On/Off candidate cover image in dashboard.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_jobtitle_switch',
            'type' => 'button_set',
            'title' => __('Job Title', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('It will disable/enable the candidate job title fields in all candidate section.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_salary_switch',
            'type' => 'button_set',
            'title' => __('Salary', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('It will disable/enable the salary fields in all candidate section.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_sector_selct_method',
            'type' => 'button_set',
            'title' => __('Sector Select Method', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Set sector select method Single/Multiple for candidate.', 'wp-jobsearch'),
            'options' => array(
                'single' => __('Single', 'wp-jobsearch'),
                'multi' => __('Multi', 'wp-jobsearch'),
                'single_req' => __('Single & Required', 'wp-jobsearch'),
                'multi_req' => __('Multi & Required', 'wp-jobsearch'),
            ),
            'default' => 'single_req',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_dob_switch',
            'type' => 'button_set',
            'title' => __('Date of Birth', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('It will disable/enable the Date of Birth fields in all candidate section.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'on_req' => __('On & Required', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_phone_switch',
            'type' => 'button_set',
            'title' => __('Phone Number', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('It will disable/enable the Phone fields in candidate dashboard.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'on_req' => __('On & Required', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_desc_with_media',
            'type' => 'button_set',
            'title' => __('Description Media Buttons', 'wp-jobsearch'),
            'subtitle' => __('Allow images and videos in a text editor.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_profile_url_switch',
            'type' => 'button_set',
            'title' => __('Public Profile URL', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Allow the Candidate to change his/her Profile URL.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'public_cand_pview_switch',
            'type' => 'button_set',
            'title' => __('Public Profile view option', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Allow the Candidate to make his/her profile public or draft.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_skills_switch',
            'type' => 'button_set',
            'title' => __('Candidate Skills', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Allow candidates to add skills in resume.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_max_skills',
            'type' => 'text',
            'title' => __('Max. Skills allow', 'wp-jobsearch'),
            'required' => array('cand_skills_switch', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'default' => '5',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_sugg_skills',
            'type' => 'text',
            'title' => __('Max. Suggested Skills Show', 'wp-jobsearch'),
            'required' => array('cand_skills_switch', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'default' => '15',
        );

        ///////////////////////////
        ///////// END /////////////
        ///////////////////////////

        $cand_dashbord_sett = apply_filters('jobsearch_joptons_candash_setbefor_dashmenu_boxes', $cand_dashbord_sett);

        $cand_dashbord_sett[] = array(
            'id' => 'cand-dashbordmenu-settings',
            'type' => 'section',
            'title' => __('Dashboard Menu', 'wp-jobsearch'),
            'subtitle' => __('Dashboard Menu settings.', 'wp-jobsearch'),
            'indent' => true,
        );

        $addnew_pages_arr = array();
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
            $all_page[''] = __('Select Page', 'wp-jobsearch');
            foreach ($pages as $page) {
                $addnew_pages_arr[$page->post_name] = $page->post_title;
            }
        }

        $cust_dashpages_arr = isset($jobsearch__options['cand_dashmenu_cuspages']) ? $jobsearch__options['cand_dashmenu_cuspages'] : '';
        $dash_menu_linksar = array(
            'my_profile' => __('My Profile', 'wp-jobsearch'),
            'my_resume' => __('My Resume', 'wp-jobsearch'),
            'applied_jobs' => __('Applied Jobs', 'wp-jobsearch'),
            'cv_manager' => __('CV Manager', 'wp-jobsearch'),
            'fav_jobs' => __('Favorite Jobs', 'wp-jobsearch'),
            'packages' => __('Packages', 'wp-jobsearch'),
            'transactions' => __('Transactions', 'wp-jobsearch'),
            'my_emails' => __('My Emails', 'wp-jobsearch'),
            'following' => __('Following', 'wp-jobsearch'),
            'change_password' => __('Change Password', 'wp-jobsearch'),
        );
        $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
        $post_ids_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta";
        $post_ids_query .= " ON postmeta.post_id = posts.ID";
        $post_ids_query .= " WHERE post_type='dashb_menu' AND post_status='publish'";
        $post_ids_query .= " AND ((postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='cand') OR (postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='both'));";

        $cusmenu_post_ids = $wpdb->get_col($post_ids_query);

        if (!empty($cusmenu_post_ids)) {
            foreach ($cusmenu_post_ids as $cust_dashpage) {
                $the_page = get_post($cust_dashpage);
                if (isset($the_page->ID)) {
                    $dash_menu_linksar[$the_page->post_name] = $the_page->post_title;
                }
            }
        }
        $cand_dashbord_sett[] = array(
            'id' => 'cand_dashbord_menu',
            'type' => 'sortable',
            'title' => __('Dashboard Menu', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Enable/Disable and reorder candidate dashboard menu items however you want. You can add custom dashboard menu from <a href="%s" target="_blank">here</a>', 'wp-jobsearch'), admin_url('edit.php?post_type=dashb_menu')),
            'desc' => '',
            'mode' => 'checkbox',
            'options' => apply_filters('jobsearch_cand_dash_menu_in_opts', $dash_menu_linksar),
            'default' => apply_filters('jobsearch_cand_dash_menu_in_opts_swch', array(
                'my_profile' => true,
                'my_resume' => true,
                'applied_jobs' => true,
                'cv_manager' => true,
                'fav_jobs' => true,
                'packages' => true,
                'transactions' => true,
                'my_emails' => true,
                'following' => true,
                'change_password' => true,
            )),
        );

        $cand_dashbord_sett = apply_filters('jobsearch_joptons_candash_setafter_dashmenu_boxes', $cand_dashbord_sett);

        $cand_dashbord_sett[] = array(
            'id' => 'cand-resumetabcnt-settings',
            'type' => 'section',
            'title' => __('Resume Section', 'wp-jobsearch'),
            'subtitle' => __('Resume Section settings.', 'wp-jobsearch'),
            'indent' => true,
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_resm_cover_letr',
            'type' => 'button_set',
            'title' => __('Cover Letter', 'wp-jobsearch'),
            'subtitle' => __('Allow the candidate to submit a cover letter while applying new job to the employer.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_resm_education',
            'type' => 'button_set',
            'title' => __('Education', 'wp-jobsearch'),
            'subtitle' => __('Allow to add education history in my resume candidate dashboard.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_resm_experience',
            'type' => 'button_set',
            'title' => __('Experience', 'wp-jobsearch'),
            'subtitle' => __('Allow to add experience history in my resume candidate dashboard.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_resm_portfolio',
            'type' => 'button_set',
            'title' => __('Portfolio', 'wp-jobsearch'),
            'subtitle' => __('Allow to add portfolio history in my resume candidate dashboard.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'max_portfolio_allow',
            'type' => 'text',
            'required' => array('cand_resm_portfolio', 'equals', 'on'),
            'title' => __('Maximum Portfolios allowed', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Set Maximum Portfolios allowed for candidates.', 'wp-jobsearch'),
            'default' => '5',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_resm_skills',
            'type' => 'button_set',
            'title' => __('Expertise', 'wp-jobsearch'),
            'subtitle' => __('Allow to add expertise history in my resume candidate dashboard.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_resm_langs',
            'type' => 'button_set',
            'title' => __('Languages', 'wp-jobsearch'),
            'subtitle' => __('Allow candidates to add languages in dashboard.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_resm_honsawards',
            'type' => 'button_set',
            'title' => __('Honors & Awards', 'wp-jobsearch'),
            'subtitle' => __('Allow to add awards history in my resume candidate dashboard.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand-socilmediadash-settings',
            'type' => 'section',
            'title' => __('Social Links Section', 'wp-jobsearch'),
            'subtitle' => __('Social Links settings.', 'wp-jobsearch'),
            'indent' => true,
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_alow_fb_smm',
            'type' => 'button_set',
            'title' => __('Facebook', 'wp-jobsearch'),
            'subtitle' => __('Allow the candidate to add a Facebook profile URL.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_alow_twt_smm',
            'type' => 'button_set',
            'title' => __('Twitter', 'wp-jobsearch'),
            'subtitle' => __('Allow the candidate to add a Twitter profile URL.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_alow_linkd_smm',
            'type' => 'button_set',
            'title' => __('Linkedin', 'wp-jobsearch'),
            'subtitle' => __('Allow the candidate to add a LinkedIn profile URL.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_alow_dribbb_smm',
            'type' => 'button_set',
            'title' => __('Dribbble', 'wp-jobsearch'),
            'subtitle' => __('Allow the candidate to add a Dribbble profile URL.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'candidate_social_mlinks',
            'type' => 'jobsearch_multi_socialfileds',
            'title' => __('Other Social Links', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Add more Social Media platforms for candidates.', 'wp-jobsearch'),
            'default' => '',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cv-uplodash-settings',
            'type' => 'section',
            'title' => __('CV Upload Section', 'wp-jobsearch'),
            'subtitle' => __('CV Upload settings.', 'wp-jobsearch'),
            'indent' => true,
        );
        $cand_dashbord_sett[] = array(
            'id' => 'multiple_cv_uploads',
            'type' => 'button_set',
            'title' => __('Multiple CV Upload', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Allow candidates to Upload Multiple CV files.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'max_cvs_allow',
            'type' => 'text',
            'title' => __('Maximum CVs allowed', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Set Maximum CVs allowed for candidates.', 'wp-jobsearch'),
            'default' => '5',
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_cv_types',
            'type' => 'select',
            'multi' => true,
            'title' => __('CV File Types', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'text/plain' => __('text', 'wp-jobsearch'),
                'image/jpeg' => __('jpeg', 'wp-jobsearch'),
                'image/png' => __('png', 'wp-jobsearch'),
                'application/msword' => __('doc', 'wp-jobsearch'),
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => __('docx', 'wp-jobsearch'),
                'application/vnd.ms-excel' => __('xls', 'wp-jobsearch'),
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => __('xlsx', 'wp-jobsearch'),
                'application/pdf' => __('pdf', 'wp-jobsearch'),
            ),
            'default' => array('application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf'),
            'subtitle' => __('Select file formats.', 'wp-jobsearch'),
        );
        $cand_dashbord_sett[] = array(
            'id' => 'cand_cv_file_size',
            'type' => 'select',
            'title' => __('Max. File Size', 'wp-jobsearch'),
            'subtitle' => __('Restrict the CV file size to upload.', 'wp-jobsearch'),
            'options' => array(
                '300' => __('300KB', 'wp-jobsearch'),
                '500' => __('500KB', 'wp-jobsearch'),
                '750' => __('750KB', 'wp-jobsearch'),
                '1024' => __('1Mb', 'wp-jobsearch'),
                '2048' => __('2Mb', 'wp-jobsearch'),
                '3072' => __('3Mb', 'wp-jobsearch'),
                '4096' => __('4Mb', 'wp-jobsearch'),
                '5120' => __('5Mb', 'wp-jobsearch'),
                '10120' => __('10Mb', 'wp-jobsearch'),
                '50120' => __('50Mb', 'wp-jobsearch'),
                '100120' => __('100Mb', 'wp-jobsearch'),
                '200120' => __('200Mb', 'wp-jobsearch'),
                '300120' => __('300Mb', 'wp-jobsearch'),
                '500120' => __('500Mb', 'wp-jobsearch'),
                '1000120' => __('1Gb', 'wp-jobsearch'),
            ),
            'desc' => '',
            'default' => '5120',
        );
        $section_settings = array(
            'title' => __('Candidate Dashboard', 'wp-jobsearch'),
            'id' => 'candidate_dash_settings',
            'desc' => __('Candidate Dashboard settings.', 'wp-jobsearch'),
            'subsection' => true,
            'fields' => apply_filters('jobsearch_options_cand_dash_fields_arr', $cand_dashbord_sett),
        );
        $section_settings = apply_filters('jobsearch_options_cand_dash_setins_sec', $section_settings);
        if (isset($section_settings['id']) && $section_settings['id'] != '') {
            $setting_sections[] = $section_settings;
        }

        $emp_dashbord_sett = array();
        $emp_dashbord_sett[] = array(
            'id' => 'employer_auto_approve',
            'type' => 'button_set',
            'title' => __('Employer Approval', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Set the employer&apos;s approval method after registration.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('Auto', 'wp-jobsearch'),
                'off' => __('Admin Review', 'wp-jobsearch'),
                'email' => __('Auto with Email', 'wp-jobsearch'),
                'admin_email' => __('Admin Review with Email', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'unapproverd_employer_txt',
            'type' => 'editor',
            'args' => array(
                'teeny' => true,
                'media_buttons' => false,
            ),
            'title' => __('Unapproved Employer Text', 'wp-jobsearch'),
            'required' => array(
                array('employer_auto_approve', '!=', 'on'),
                array('employer_auto_approve', '!=', 'email'),
            ),
            'subtitle' => __('This text will show in unapproved employer dashboard.', 'wp-jobsearch'),
            'desc' => '',
            'default' => '<strong>ACCOUNT ACTIVATION REQUIRED BY ADMIN !</strong>
                                                <strong>Your account is In-active!</strong>
                                                Your membership account is awaiting approval by the site administrator. You will not be able to fully interact with the account functions and aspects of this website until your account is approved. Once approved by admin or denied you will receive an email notice.',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'employer_cover_img_switch',
            'type' => 'button_set',
            'title' => __('Jobs Cover Image', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('On/Off employer cover image in dashboard.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        
        $emp_dashbord_sett[] = array(
            'id' => 'emp_sector_selct_method',
            'type' => 'button_set',
            'title' => __('Sector Select Method', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Set sector select method Single/Multiple for employer.', 'wp-jobsearch'),
            'options' => array(
                'single' => __('Single', 'wp-jobsearch'),
                'multi' => __('Multi', 'wp-jobsearch'),
                'single_req' => __('Single & Required', 'wp-jobsearch'),
                'multi_req' => __('Multi & Required', 'wp-jobsearch'),
            ),
            'default' => 'single_req',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'employer_phone_field',
            'type' => 'button_set',
            'title' => __('Employer Phone', 'wp-jobsearch'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'on_req' => __('On & Required', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'employer_web_field',
            'type' => 'button_set',
            'title' => __('Employer Website', 'wp-jobsearch'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'employer_founded_date',
            'type' => 'button_set',
            'title' => __('Employer Founded Date', 'wp-jobsearch'),
            'subtitle' => __('Allow the employer to add the Founded Date in the dashboard.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $emp_dashbord_sett = apply_filters('jobsearch_opts_empdash_befor_dashmenu', $emp_dashbord_sett);
        //
        $emp_dashbord_sett[] = array(
            'id' => 'emp-dashbordmenu-settings',
            'type' => 'section',
            'title' => __('Dashboard Menu', 'wp-jobsearch'),
            'subtitle' => __('Dashboard Menu settings.', 'wp-jobsearch'),
            'indent' => true,
        );
        $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
        $post_ids_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta";
        $post_ids_query .= " ON postmeta.post_id = posts.ID";
        $post_ids_query .= " WHERE post_type='dashb_menu' AND post_status='publish'";
        $post_ids_query .= " AND ((postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='emp') OR (postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='both'));";

        $cusmenu_post_ids = $wpdb->get_col($post_ids_query);

        $dash_menu_linksar = array(
            'company_profile' => __('Company Profile', 'wp-jobsearch'),
            'post_new_job' => __('Post a New Job', 'wp-jobsearch'),
            'manage_jobs' => __('Manage Jobs', 'wp-jobsearch'),
            'all_applicants' => __('All Applicants', 'wp-jobsearch'),
            'saved_candidates' => __('Saved Candidates', 'wp-jobsearch'),
            'packages' => __('Packages', 'wp-jobsearch'),
            'transactions' => __('Transactions', 'wp-jobsearch'),
            'my_emails' => __('My Emails', 'wp-jobsearch'),
            'followers' => __('Followers', 'wp-jobsearch'),
            'change_password' => __('Change Password', 'wp-jobsearch'),
        );
        if (!empty($cusmenu_post_ids)) {
            foreach ($cusmenu_post_ids as $cust_dashpage) {
                $the_page = get_post($cust_dashpage);
                if (isset($the_page->ID)) {
                    $dash_menu_linksar[$the_page->post_name] = $the_page->post_title;
                }
            }
        }
        $emp_dashbord_sett[] = array(
            'id' => 'emp_dashbord_menu',
            'type' => 'sortable',
            'title' => __('Dashboard Menu', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Enable/Disable and reorder employer dashboard menu items however you want. You can add custom dashboard menu from <a href="%s" target="_blank">here</a>', 'wp-jobsearch'), admin_url('edit.php?post_type=dashb_menu')),
            'desc' => '',
            'mode' => 'checkbox',
            'options' => apply_filters('jobsearch_emp_dash_menu_in_opts', $dash_menu_linksar),
            'default' => array(
                'company_profile' => true,
                'post_new_job' => true,
                'manage_jobs' => true,
                'all_applicants' => true,
                'saved_candidates' => true,
                'packages' => true,
                'transactions' => true,
                'my_emails' => true,
                'followers' => true,
                'change_password' => true,
            ),
        );
        $emp_dashbord_sett[] = array(
            'id' => 'emp-socilmediadash-settings',
            'type' => 'section',
            'title' => __('Social Links Section', 'wp-jobsearch'),
            'subtitle' => __('Social Links settings.', 'wp-jobsearch'),
            'indent' => true,
        );
        $emp_dashbord_sett[] = array(
            'id' => 'emp_alow_fb_smm',
            'type' => 'button_set',
            'title' => __('Facebook', 'wp-jobsearch'),
            'subtitle' => __('Allow the employer to add a Facebook profile URL.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'emp_alow_twt_smm',
            'type' => 'button_set',
            'title' => __('Twitter', 'wp-jobsearch'),
            'subtitle' => __('Allow the employer to add a Twitter profile URL.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'emp_alow_linkd_smm',
            'type' => 'button_set',
            'title' => __('Linkedin', 'wp-jobsearch'),
            'subtitle' => __('Allow the employer to add a LinkedIn profile URL.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'emp_alow_dribbb_smm',
            'type' => 'button_set',
            'title' => __('Dribbble', 'wp-jobsearch'),
            'subtitle' => __('Allow the employer to add a Dribbble profile URL.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'employer_social_mlinks',
            'type' => 'jobsearch_multi_socialfileds',
            'title' => __('Other Social Links', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Add more Social Media platforms for the employer.', 'wp-jobsearch'),
            'default' => '',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'emp-socilmediadash-settings-section-end',
            'type' => 'section',
            'indent' => false,
        );
        $emp_dashbord_sett[] = array(
            'id' => 'emp_dash_email_applics',
            'type' => 'button_set',
            'title' => __('Email Applicants', 'wp-jobsearch'),
            'subtitle' => __('Allow to show email applicants in employer dashboard for those jobs which are selected apply method by email.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'emp_dash_external_applics',
            'type' => 'button_set',
            'title' => __('External Applicants', 'wp-jobsearch'),
            'subtitle' => __('Allow to show applicants in employer dashboard for those jobs which are selected apply by external URL.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'emp_desc_with_media',
            'type' => 'button_set',
            'title' => __('Description Media Buttons', 'wp-jobsearch'),
            'subtitle' => __('Allow the employer to add images and videos in the profile description.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'emp_profile_url_switch',
            'type' => 'button_set',
            'title' => __('Public Profile URL', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Allow the employer to change his/her profile URL.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'public_pview_switch',
            'type' => 'button_set',
            'title' => __('Public Profile view option', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Allow the employer to make his/her profile public or draft.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'emp_account_membs',
            'type' => 'button_set',
            'title' => __('Account Members', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Allow the Account Members of Employer who can manage employer account with specific permissions.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'allow_team_members',
            'type' => 'button_set',
            'title' => __('Team Members', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Allow the Employer to add Team Members for existing employees basic information.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'allow_empl_awards',
            'type' => 'button_set',
            'title' => __('Awards', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Allow the Employer to add awards in profile.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'allow_empl_affiliations',
            'type' => 'button_set',
            'title' => __('Affiliations', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Allow the Employer to add affiliations in profile.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'allow_compny_galery',
            'type' => 'button_set',
            'title' => __('Company Gallery', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Allow Company Gallery in profile.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $emp_dashbord_sett[] = array(
            'id' => 'max_gal_imgs_allow',
            'type' => 'text',
            'title' => __('Maximum Gallery images allowed', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Set Maximum Gallery images allowed.', 'wp-jobsearch'),
            'default' => '5',
        );
        $section_settings = array(
            'title' => __('Employer Dashboard', 'wp-jobsearch'),
            'id' => 'employer_dash_settings',
            'desc' => __('Employer Dashboard settings.', 'wp-jobsearch'),
            'subsection' => true,
            'fields' => $emp_dashbord_sett,
        );
        $setting_sections[] = $section_settings;
        $aftr_empdash_sec = apply_filters('jobsearch_plugin_opts_after_empdash_setts', array());
        if (!empty($aftr_empdash_sec)) {
            if (isset($aftr_empdash_sec[0]['title'])) {
                foreach ($aftr_empdash_sec as $aftr_empdash_secitm) {
                    $setting_sections[] = $aftr_empdash_secitm;
                }
            } else {
                $setting_sections[] = $aftr_empdash_sec;
            }
        }

        //
        $notifics_dashbord_sett = array();
        $notifics_dashbord_sett[] = array(
            'id' => 'dash_notifics_switch',
            'type' => 'button_set',
            'title' => __('User Notifications', 'wp-jobsearch'),
            'subtitle' => __('Add notifications in the user dashboard.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $notifics_dashbord_sett[] = array(
            'id' => 'header_notifics_btn',
            'type' => 'button_set',
            'required' => array('dash_notifics_switch', 'equals', 'on'),
            'title' => __('Header Notifications Button', 'wp-jobsearch'),
            'subtitle' => __('Show Notifications Button in header.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'public' => __('Public', 'wp-jobsearch'),
                'for_cand' => __('For Candidates', 'wp-jobsearch'),
                'for_emp' => __('For Employers', 'wp-jobsearch'),
                'for_both' => __('Candidates & Employers both', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'public',
        );
        $notifics_dashbord_sett[] = array(
            'id' => 'canddash-notifics-settings',
            'type' => 'section',
            'required' => array('dash_notifics_switch', 'equals', 'on'),
            'title' => __('Candidate Notifications', 'wp-jobsearch'),
            'subtitle' => __('Candidate Notifications settings.', 'wp-jobsearch'),
            'indent' => true,
        );
        $notifics_dashbord_sett[] = array(
            'id' => 'add_notifics_for_cands',
            'type' => 'button_set',
            'title' => __('Candidate Notifications', 'wp-jobsearch'),
            'required' => array('dash_notifics_switch', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $notifics_dashbord_sett[] = array(
            'id' => 'notifics_candto_newjob',
            'type' => 'button_set',
            'title' => __('Notify candidate on new job post', 'wp-jobsearch'),
            'required' => array(
                array('dash_notifics_switch', 'equals', 'on'),
                array('add_notifics_for_cands', 'equals', 'on')
            ),
            'subtitle' => __('Notify candidate for following employers while they post a new job.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('Yes', 'wp-jobsearch'),
                'off' => __('No', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $notifics_dashbord_sett[] = array(
            'id' => 'notifics_cand_shrtforinter',
            'type' => 'button_set',
            'title' => __('Notify candidate on Shortlist for Interview', 'wp-jobsearch'),
            'required' => array(
                array('dash_notifics_switch', 'equals', 'on'),
                array('add_notifics_for_cands', 'equals', 'on')
            ),
            'subtitle' => __('Notify candidate when the followed employer shortlist him/her for an interview.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('Yes', 'wp-jobsearch'),
                'off' => __('No', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $notifics_dashbord_sett[] = array(
            'id' => 'notifics_cand_rejctforinter',
            'type' => 'button_set',
            'title' => __('Notify candidate on Reject for Interview', 'wp-jobsearch'),
            'required' => array(
                array('dash_notifics_switch', 'equals', 'on'),
                array('add_notifics_for_cands', 'equals', 'on')
            ),
            'subtitle' => __('Notify candidate when the followed employer reject him/her for an interview.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('Yes', 'wp-jobsearch'),
                'off' => __('No', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $notifics_dashbord_sett[] = array(
            'id' => 'empdash-notifics-settings',
            'type' => 'section',
            'required' => array('dash_notifics_switch', 'equals', 'on'),
            'title' => __('Employer Notifications', 'wp-jobsearch'),
            'subtitle' => __('Employer Notifications settings.', 'wp-jobsearch'),
            'indent' => true,
        );
        $notifics_dashbord_sett[] = array(
            'id' => 'add_notifics_for_emps',
            'type' => 'button_set',
            'title' => __('Employer Notifications', 'wp-jobsearch'),
            'required' => array('dash_notifics_switch', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $notifics_dashbord_sett[] = array(
            'id' => 'notifics_empto_applyjob',
            'type' => 'button_set',
            'title' => __('Notify employer on apply job', 'wp-jobsearch'),
            'required' => array(
                array('dash_notifics_switch', 'equals', 'on'),
                array('add_notifics_for_emps', 'equals', 'on')
            ),
            'subtitle' => __('Notify employer when someone apply his/her job.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('Yes', 'wp-jobsearch'),
                'off' => __('No', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );

        $section_settings = array(
            'title' => __('Notifications Settings', 'wp-jobsearch'),
            'id' => 'user-dash-notifics',
            'desc' => __('Dashboard Notifications Settings', 'wp-jobsearch'),
            'subsection' => true,
            'fields' => $notifics_dashbord_sett,
        );
        $setting_sections[] = $section_settings;

        $employer_arr = array();
        $employer_arr[] = array(
            'id' => 'employer_rewrite_slug',
            'type' => 'text',
            'title' => __('Employer Rewrite Slug', 'wp-jobsearch'),
            'subtitle' => __('Warning! It will change all employer&apos;s URL.', 'wp-jobsearch'),
            'desc' => sprintf(__('<strong>Please save <a href="%s" target="_blank">permalinks</a> after change this slug.</strong>', 'wp-jobsearch'), admin_url('options-permalink.php')),
            'default' => 'employer',
        );
        $employer_arr[] = array(
            'id' => 'free-shortlist-allow',
            'type' => 'button_set',
            'title' => __('Free Resume Saving', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Allow the employers to save candidate&apos;s resume absolutely package free.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $employer_arr[] = array(
            'id' => 'emp_followin_btn',
            'type' => 'button_set',
            'title' => __('Following Button', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Allow candidates to follow employers.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $employer_arr[] = array(
            'id' => 'resume_package_page',
            'type' => 'select',
            'title' => __('Resume Packages Page', 'wp-jobsearch'),
            'required' => array('free-shortlist-allow', 'equals', 'off'),
            'subtitle' => __('Select Resume Packages Page. It will redirect employers at selected page to buy package.', 'wp-jobsearch'),
            'desc' => '',
            'options' => $all_page,
            'default' => '',
        );

        //
        $employer_arr[] = array(
            'id' => 'emp_srch_filtrs_sort',
            'type' => 'sorter',
            'title' => __('Employers Filter Sort', 'wp-jobsearch'),
            'subtitle' => __('Employers Search Filter Fields Sorting.', 'wp-jobsearch'),
            'desc' => __('Drag and drop to sort the fields.', 'wp-jobsearch'),
            'options' => array(
                'fields' => array(
                    'location' => __('Location', 'wp-jobsearch'),
                    'date_posted' => __('Date Posted', 'wp-jobsearch'),
                    'sector' => __('Sector', 'wp-jobsearch'),
                    'team_size' => __('Team Size', 'wp-jobsearch'),
                    'custom_fields' => __('Custom Fields', 'wp-jobsearch'),
                ),
                'disabled' => array('ads' => __('Advertisement', 'wp-jobsearch'))
            ),
        );
        //
        $employer_arr[] = array(
            'id' => 'employer_no_img',
            'type' => 'media',
            'url' => true,
            'title' => __('Employer Image Placeholder', 'wp-jobsearch'),
            'compiler' => 'true',
            'desc' => '',
            'subtitle' => '',
            'default' => array('url' => jobsearch_plugin_get_url('images/no-image.jpg')),
        );
        $employer_arr[] = array(
            'id' => 'elistin_map_marker_img',
            'type' => 'media',
            'url' => true,
            'title' => __('Employers Map Marker Icon', 'wp-jobsearch'),
            'compiler' => 'true',
            'desc' => '',
            'subtitle' => '',
            'default' => array('url' => ''),
        );
        $employer_arr[] = array(
            'id' => 'elistin_map_cluster_img',
            'type' => 'media',
            'url' => true,
            'title' => __('Employers Map Cluster Icon', 'wp-jobsearch'),
            'compiler' => 'true',
            'desc' => '',
            'subtitle' => '',
            'default' => array('url' => ''),
        );
        $section_settings = array(
            'title' => __('Employer Settings', 'wp-jobsearch'),
            'id' => 'user-employer-genral',
            'desc' => __('Employer Common Settings', 'wp-jobsearch'),
            'icon' => 'el el-user',
            'fields' => apply_filters('jobsearch_bkoptns_empmain_tab_fields', $employer_arr),
        );
        $setting_sections[] = $section_settings;


        //
        $employer_details_arr = array();

        $employer_details_arr = apply_filters('employer_detail_pages_styles', $employer_details_arr);

        //
        $employer_details_arr[] = array(
            'id' => 'empsecinfo-section',
            'type' => 'section',
            'title' => __('Employer Contact Info.', 'wp-jobsearch'),
            'subtitle' => '',
            'indent' => true,
        );
        $employer_details_arr[] = array(
            'id' => 'emp-sensinfo-email',
            'type' => 'button_set',
            'title' => __('Show Employers Email', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Show Employers Email for login or non-login users.', 'wp-jobsearch'),
            'options' => array(
                'public' => __('Public', 'wp-jobsearch'),
                'admin_only' => __('Admin Only', 'wp-jobsearch'),
                'for_login' => __('Login Users', 'wp-jobsearch'),
                'emp_cand' => __('Employers/Candidates', 'wp-jobsearch'),
                'emp_only' => __('Employers only', 'wp-jobsearch'),
                'cand_only' => __('Candidates only', 'wp-jobsearch'),
            ),
            'default' => 'for_login',
        );

        $employer_details_arr[] = array(
            'id' => 'emp-sensinfo-phone',
            'type' => 'button_set',
            'title' => __('Show Employers Phone', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Show Employers Phone for login or non-login users.', 'wp-jobsearch'),
            'options' => array(
                'public' => __('Public', 'wp-jobsearch'),
                'admin_only' => __('Admin Only', 'wp-jobsearch'),
                'for_login' => __('Login Users', 'wp-jobsearch'),
                'emp_cand' => __('Employers/Candidates', 'wp-jobsearch'),
                'emp_only' => __('Employers only', 'wp-jobsearch'),
                'cand_only' => __('Candidates only', 'wp-jobsearch'),
            ),
            'default' => 'for_login',
        );
        $employer_details_arr[] = array(
            'id' => 'emp-sensinfo-weburl',
            'type' => 'button_set',
            'title' => __('Show Employers Website', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Show Employers Website for login or non-login users.', 'wp-jobsearch'),
            'options' => array(
                'public' => __('Public', 'wp-jobsearch'),
                'admin_only' => __('Admin Only', 'wp-jobsearch'),
                'for_login' => __('Login Users', 'wp-jobsearch'),
                'emp_cand' => __('Employers/Candidates', 'wp-jobsearch'),
                'emp_only' => __('Employers only', 'wp-jobsearch'),
                'cand_only' => __('Candidates only', 'wp-jobsearch'),
            ),
            'default' => 'for_login',
        );
        $employer_details_arr[] = array(
            'id' => 'empsecinfo-sett-sec-end',
            'type' => 'section',
            'indent' => false,
        );
        $employer_details_arr[] = array(
            'id' => 'empjobs_posted_count',
            'type' => 'button_set',
            'title' => __('Total jobs posted count', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Enable/Disable Employer total jobs posted count.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $employer_details_arr[] = array(
            'id' => 'emptotl_views_count',
            'type' => 'button_set',
            'title' => __('Total profile views count', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Enable/Disable Employer total profile views count.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $employer_details_arr[] = array(
            'id' => 'emps-coverimg-section',
            'type' => 'section',
            'title' => __('Employer Cover Image', 'wp-jobsearch'),
            'subtitle' => '',
            'indent' => true,
        );
        $employer_details_arr[] = array(
            'id' => 'emp_default_coverimg',
            'type' => 'media',
            'url' => true,
            'title' => __('Default Employer Detail/Jobs Cover Image', 'wp-jobsearch'),
            'compiler' => 'true',
            'desc' => '',
            'subtitle' => '',
            'default' => '',
        );
        $employer_details_arr[] = array(
            'id' => 'careerfy-emp-img-overlay-bg-color',
            'type' => 'color_rgba',
            'transparent' => false,
            'title' => __('Cover Photo Overlay Color', 'wp-jobsearch'),
            'subtitle' => __('Cover Photo Overlay Color.', 'wp-jobsearch'),
            'desc' => '',
            'default' => 'rgba(17,22,44,0.66)'
        );
        $employer_details_arr[] = array(
            'id' => 'emps-coverimg-section',
            'type' => 'section',
            'title' => '',
            'subtitle' => '',
            'indent' => false,
        );
        $employer_details_arr[] = array(
            'id' => 'emp_det_contact_form',
            'type' => 'button_set',
            'title' => __('Employer Detail Contact Form', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Allow user to contact the employer at the Employers detail page.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('Public', 'wp-jobsearch'),
                'cand_login' => __('With Candidate Login', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $employer_details_arr[] = array(
            'id' => 'emp_det_loc_listing',
            'type' => 'select',
            'multi' => true,
            'title' => __('Locations on Employer page', 'wp-jobsearch'),
            'subtitle' => __('Select which type of location in Employer Detail', 'wp-jobsearch'),
            'options' => array(
                'country' => esc_html__("Country", "wp-jobsearch"),
                'state' => esc_html__("State", "wp-jobsearch"),
                'city' => esc_html__("City", "wp-jobsearch"),
            ),
            'default' => '',
        );
        $section_settings = array(
            'title' => __('Employer Detail Settings', 'wp-jobsearch'),
            'id' => 'emp-dtail-settins',
            'desc' => '',
            'subsection' => true,
            'fields' => apply_filters('jobsearch_bkoptns_empdetail_tab_fields', $employer_details_arr),
        );
        $setting_sections[] = $section_settings;

        $aftr_empdetail_sec = apply_filters('jobsearch_plugin_opts_after_empdetail_setts', array());
        if (!empty($aftr_empdetail_sec)) {
            $setting_sections[] = $aftr_empdetail_sec;
        }

        $candidate_arr = array();

        $candidate_arr = apply_filters('candidate_plugoptions_sec_before_pkgs_swich', $candidate_arr);

        $candidate_arr[] = array(
            'id' => 'candidate_rewrite_slug',
            'type' => 'text',
            'title' => __('Candidate Rewrite Slug', 'wp-jobsearch'),
            'subtitle' => __('Warning! It will change all candidate&apos;s URLs.', 'wp-jobsearch'),
            'desc' => sprintf(__('<strong>Please save <a href="%s" target="_blank">permalinks</a> after change this slug.</strong>', 'wp-jobsearch'), admin_url('options-permalink.php')),
            'default' => 'candidate',
        );
        $candidate_arr[] = array(
            'id' => 'jobsearch_cand_result_page',
            'type' => 'select',
            'title' => __('Search Result Page', 'wp-jobsearch'),
            'desc' => '',
            'options' => $all_page,
            'default' => '',
        );
        $candidate_arr[] = array(
            'id' => 'cand_whatsapp_msgallow',
            'type' => 'button_set',
            'title' => __('Whatsapp Message on Profile', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Allow users to send a Whatsapp message from the candidate profile.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $candidate_arr[] = array(
            'id' => 'cand_whatsapp_defmsg',
            'type' => 'text',
            'title' => __('Default Whatsapp Message', 'wp-jobsearch'),
            'required' => array('cand_whatsapp_msgallow', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'default' => 'Hi',
        );
        $candidate_arr[] = array(
            'id' => 'free-job-apply-allow',
            'type' => 'button_set',
            'title' => __('Free Job Apply', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Allow candidates to apply jobs absolutely package free.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $candidate_arr[] = array(
            'id' => 'candidate_package_page',
            'type' => 'select',
            'title' => __('Candidate Packages Page', 'wp-jobsearch'),
            'required' => array('free-job-apply-allow', 'equals', 'off'),
            'subtitle' => __('Select Candidate Packages Page. It will redirect candidates at selected page to buy package.', 'wp-jobsearch'),
            'desc' => '',
            'options' => $all_page,
            'default' => '',
        );
        $candidate_arr[] = array(
            'id' => 'apply_social_platforms',
            'type' => 'button_set',
            'multi' => true,
            'title' => __('Apply Job with Social Platforms', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'facebook' => __('Facebook', 'wp-jobsearch'),
                'linkedin' => __('Linkedin', 'wp-jobsearch'),
                'google' => __('Google', 'wp-jobsearch'),
            ),
            'default' => array('facebook', 'linkedin'),
            'subtitle' => __('Select Social Platforms to apply job.', 'wp-jobsearch'),
        );
        //
        $candidate_arr[] = array(
            'id' => 'cand_srch_filtrs_sort',
            'type' => 'sorter',
            'title' => __('Candidates Filter Sort', 'wp-jobsearch'),
            'subtitle' => __('Candidates Search Filter Fields Sorting.', 'wp-jobsearch'),
            'desc' => __('Drag and drop to sort the fields.', 'wp-jobsearch'),
            'options' => array(
                'fields' => array(
                    'date_posted' => __('Date Posted', 'wp-jobsearch'),
                    'sector' => __('Sector', 'wp-jobsearch'),
                    'custom_fields' => __('Custom Fields', 'wp-jobsearch'),
                ),
                'disabled' => array('ads' => __('Advertisement', 'wp-jobsearch'))
            ),
        );
        $candidate_arr[] = array(
            'id' => 'candidate_no_img',
            'type' => 'media',
            'url' => true,
            'title' => __('Candidate Default profile image', 'wp-jobsearch'),
            'compiler' => 'true',
            'desc' => '',
            'subtitle' => __('If candidate does not upload profile photo this image will apply on those candidates.', 'wp-jobsearch'),
            'default' => array('url' => jobsearch_plugin_get_url('images/no-image.jpg')),
        );
        $candidate_arr[] = array(
            'id' => 'clistin_map_marker_img',
            'type' => 'media',
            'url' => true,
            'title' => __('Candidates Map Marker Icon', 'wp-jobsearch'),
            'compiler' => 'true',
            'desc' => '',
            'subtitle' => __('Candidates Map Marker will show on candidate listing with map.', 'wp-jobsearch'),
            'default' => array('url' => ''),
        );
        $candidate_arr[] = array(
            'id' => 'clistin_map_cluster_img',
            'type' => 'media',
            'url' => true,
            'title' => __('Candidates Map Cluster Icon', 'wp-jobsearch'),
            'compiler' => 'true',
            'desc' => '',
            'subtitle' => __('Candidates Map Cluster icon will show on candidate listing with map.', 'wp-jobsearch'),
            'default' => array('url' => ''),
        );

        //
        $section_settings = array(
            'title' => __('Candidate Settings', 'wp-jobsearch'),
            'id' => 'user-candidate-general',
            'desc' => __('Candidate Common Settings', 'wp-jobsearch'),
            'icon' => 'el el-user',
            'fields' => apply_filters('jobsearch_options_candidate_setings_fields', $candidate_arr),
        );

        if (isset($section_settings['fields']) && !empty($section_settings['fields'])) {
            $setting_sections[] = $section_settings;
        }

        //
        $woutlogin_aply_array = array();
        $woutlogin_aply_array[] = array(
            'id' => 'job_apply_redirect_uri',
            'type' => 'text',
            'title' => __('Apply job Redirect URL', 'wp-jobsearch'),
            'subtitle' => __('Page will redirect to this URL after applied successfully.', 'wp-jobsearch'),
            'desc' => '',
            'default' => '',
        );
        $woutlogin_aply_array[] = array(
            'id' => 'job-apply-without-login',
            'type' => 'button_set',
            'title' => __('Apply job without login', 'wp-jobsearch'),
            'subtitle' => __('Enable, if you want users can apply for a job without login.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $woutlogin_aply_array[] = array(
            'id' => 'aplywout_login_fields_sort',
            'type' => 'sorter',
            'title' => __('Apply Job Fields Sort', 'wp-jobsearch'),
            'required' => array('job-apply-without-login', 'equals', 'on'),
            'subtitle' => __('Apply Job without login form fields sorting.', 'wp-jobsearch'),
            'desc' => __('Drag and drop to sort the fields.', 'wp-jobsearch'),
            'options' => array(
                'fields' => array(
                    'name' => __('Name', 'wp-jobsearch'),
                    'email' => __('Email', 'wp-jobsearch'),
                    'phone' => __('Phone', 'wp-jobsearch'),
                    'current_jobtitle' => __('Current Job Title', 'wp-jobsearch'),
                    'current_salary' => __('Current Salary', 'wp-jobsearch'),
                    'custom_fields' => __('Custom Fields', 'wp-jobsearch'),
                    'cv_attach' => __('CV Attachment', 'wp-jobsearch'),
                ),
            ),
        );
        $woutlogin_aply_array[] = array(
            'id' => 'aplywout_log_fname_swch',
            'type' => 'button_set',
            'title' => __('Name Field', 'wp-jobsearch'),
            'required' => array('job-apply-without-login', 'equals', 'on'),
            'subtitle' => __('Enable Name Field in apply job without login form.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'on_req' => __('On &amp; Required', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $woutlogin_aply_array[] = array(
            'id' => 'aplywout_log_fphone_swch',
            'type' => 'button_set',
            'title' => __('Phone Field', 'wp-jobsearch'),
            'required' => array('job-apply-without-login', 'equals', 'on'),
            'subtitle' => __('Enable Phone Field in apply job without login form.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'on_req' => __('On &amp; Required', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $woutlogin_aply_array[] = array(
            'id' => 'aplywout_log_fcurrent_jobtitle_swch',
            'type' => 'button_set',
            'title' => __('Current Job Title Field', 'wp-jobsearch'),
            'required' => array('job-apply-without-login', 'equals', 'on'),
            'subtitle' => __('Enable Current Job Title Field in apply job without login form.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'on_req' => __('On &amp; Required', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $woutlogin_aply_array[] = array(
            'id' => 'aplywout_log_fcurrent_salary_swch',
            'type' => 'button_set',
            'title' => __('Current Salary Field', 'wp-jobsearch'),
            'required' => array('job-apply-without-login', 'equals', 'on'),
            'subtitle' => __('Enable Current Salary Field in apply job without login form.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'on_req' => __('On &amp; Required', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $woutlogin_aply_array[] = array(
            'id' => 'aplywout_log_fcv_attach_swch',
            'type' => 'button_set',
            'title' => __('CV Attachment Field', 'wp-jobsearch'),
            'required' => array('job-apply-without-login', 'equals', 'on'),
            'subtitle' => __('Enable CV Attachment Field in apply job without login form.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'on_req' => __('On &amp; Required', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $woutlogin_aply_array[] = array(
            'id' => 'aplywout_log_fcustom_fields_swch',
            'type' => 'button_set',
            'title' => __('Custom Fields', 'wp-jobsearch'),
            'required' => array('job-apply-without-login', 'equals', 'on'),
            'subtitle' => __('Enable Custom Fields in apply jobs without login form.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $section_settings = array(
            'title' => __('Apply Job Settings', 'wp-jobsearch'),
            'id' => 'woutlogin-job-applysec',
            'desc' => '',
            'subsection' => true,
            'fields' => apply_filters('jobsearch_poptions_aplywout_login_sec_fields', $woutlogin_aply_array)
        );

        if (!empty($section_settings['fields'])) {
            $setting_sections[] = $section_settings;

            $after_aplyjobsett_sec = apply_filters('jobsearch_poptions_apply_jobsett_after', array());
            if (!empty($after_aplyjobsett_sec)) {
                $setting_sections[] = $after_aplyjobsett_sec;
            }
            $after_aplyjobsett_sec = apply_filters('jobsearch_poptions_apply_jobsett_after2', array());
            if (!empty($after_aplyjobsett_sec)) {
                $setting_sections[] = $after_aplyjobsett_sec;
            }
        }

        //
        $candidate_detail_arr = array();

        $candidate_detail_arr = apply_filters('candidate_detail_pages_styles', $candidate_detail_arr);

        //
        $candidate_detail_arr[] = array(
            'id' => 'cand-coverimg-section',
            'type' => 'section',
            'title' => __('Candidate Cover Image', 'wp-jobsearch'),
            'subtitle' => '',
            'indent' => true,
        );
        $candidate_detail_arr[] = array(
            'id' => 'cand_default_coverimg',
            'type' => 'media',
            'url' => true,
            'title' => __('Default Candidate Cover Image', 'wp-jobsearch'),
            'compiler' => 'true',
            'desc' => '',
            'subtitle' => __('This image will display to all candidate profile detail pages.', 'wp-jobsearch'),
            'default' => '',
        );
        $candidate_detail_arr[] = array(
            'id' => 'careerfy-candidate-img-overlay-bg-color',
            'type' => 'color_rgba',
            'transparent' => false,
            'title' => __('Cover Photo Overlay Color', 'wp-jobsearch'),
            'subtitle' => __('Cover Photo Overlay Color.', 'wp-jobsearch'),
            'desc' => '',
            'default' => 'rgba(17,22,44,0.66)'
        );
        $candidate_detail_arr[] = array(
            'id' => 'cand-coverimg-section',
            'type' => 'section',
            'title' => '',
            'subtitle' => '',
            'indent' => false,
        );
        $candidate_detail_arr[] = array(
            'id' => 'cand_det_contact_form',
            'type' => 'button_set',
            'title' => __('Candidate Detail Contact Form', 'wp-jobsearch'),
            'subtitle' => __('Allow the employer to contact candidates at the Candidate detail page.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $candidate_detail_arr[] = array(
            'id' => 'cand_cntct_wout_login',
            'type' => 'button_set',
            'title' => __('Contact Candidate without Login', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Allow users to contact candidates without login.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('Yes', 'wp-jobsearch'),
                'off' => __('No', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $candidate_detail_arr[] = array(
            'id' => 'cand_det_loc_listing',
            'type' => 'select',
            'multi' => true,
            'title' => __('Locations on Candidate detail page', 'wp-jobsearch'),
            'subtitle' => __('Select which type of location in Candidate Detail', 'wp-jobsearch'),
            'options' => array(
                'country' => esc_html__("Country", "wp-jobsearch"),
                'state' => esc_html__("State", "wp-jobsearch"),
                'city' => esc_html__("City", "wp-jobsearch"),
            ),
            'default' => '',
        );
        $section_settings = array(
            'title' => __('Candidate Detail Settings', 'wp-jobsearch'),
            'id' => 'user-cand-detailsetins',
            'desc' => '',
            'subsection' => true,
            'fields' => apply_filters('jobsearch_options_cand_detailset_sec_fields', $candidate_detail_arr),
        );

        if (!empty($section_settings['fields'])) {
            $setting_sections[] = $section_settings;
        }

        //
        $job_settings = array();
        $job_settings[] = array(
            'id' => 'job_rewrite_slug',
            'type' => 'text',
            'title' => __('Job Rewrite Slug', 'wp-jobsearch'),
            'subtitle' => __('Warning! It will change all jobs URL.', 'wp-jobsearch'),
            'desc' => sprintf(__('<strong>Please save <a href="%s" target="_blank">permalinks</a> after change this slug.</strong>', 'wp-jobsearch'), admin_url('options-permalink.php')),
            'default' => 'job',
        );
        $job_settings = apply_filters('jobsearch_redx_opt_genjobs_start', $job_settings);
        $job_settings[] = array(
            'id' => 'google_jobs_posting',
            'type' => 'button_set',
            'title' => __('Google Jobs Posting', 'wp-jobsearch'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $job_settings[] = array(
            'id' => 'salary_onoff_switch',
            'type' => 'button_set',
            'title' => __('Salary', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('It will disable/enable the salary fields in all job views.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'on_req' => __('On & Required', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on_req',
        );

        $job_settings[] = array(
            'id' => 'job_types_switch',
            'type' => 'button_set',
            'title' => __('Job Types', 'wp-jobsearch'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'on_req' => __('On & Required', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on_req',
        );

        $job_settings[] = array(
            'id' => 'job_types_selection',
            'type' => 'button_set',
            'required' => array('job_types_switch', '!=', 'off'),
            'title' => __('Job Types Selection', 'wp-jobsearch'),
            'subtitle' => __('User can select Single/Multiple types while posting job.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'single' => __('Single', 'wp-jobsearch'),
                'multi' => __('Multiple', 'wp-jobsearch'),
            ),
            'default' => 'single',
        );
        
        $job_settings[] = array(
            'id' => 'job_sector_selct_method',
            'type' => 'button_set',
            'title' => __('Sector Select Method', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Set sector select method Single/Multiple for job.', 'wp-jobsearch'),
            'options' => array(
                'single' => __('Single', 'wp-jobsearch'),
                'multi' => __('Multi', 'wp-jobsearch'),
                'single_req' => __('Single & Required', 'wp-jobsearch'),
                'multi_req' => __('Multi & Required', 'wp-jobsearch'),
            ),
            'default' => 'single_req',
        );
        
        $job_settings[] = array(
            'id' => 'job_detail_soc_share',
            'type' => 'button_set',
            'title' => __('Social Share', 'wp-jobsearch'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $job_settings[] = array(
            'id' => 'job_empcomp_disp',
            'type' => 'button_set',
            'title' => __('Disable Jobs Company Name', 'wp-jobsearch'),
            'subtitle' => __('It will disable jobs employer name from jobs listings and detail pages.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('Yes', 'wp-jobsearch'),
                'off' => __('No', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $job_settings[] = array(
            'id' => 'without-login-apply-restriction',
            'type' => 'button_set',
            'multi' => true,
            'title' => __('Required Login for Apply Methods', 'wp-jobsearch'),
            'subtitle' => __('Restrict candidates to login before applying for a job.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'internal' => __('Internal', 'wp-jobsearch'),
                'external' => __('External', 'wp-jobsearch'),
                'email' => __('By Email', 'wp-jobsearch'),
            ),
        );
        $job_settings[] = array(
            'id' => 'jobsearch_search_list_page',
            'type' => 'select',
            'title' => __('Search Result Page', 'wp-jobsearch'),
            'subtitle' => __('Select the Search Result Page.', 'wp-jobsearch'),
            'desc' => '',
            'options' => $all_page,
            'default' => '',
        );
        $job_settings = apply_filters('jobsearch_joptions_search_result_pages', $job_settings);

        $job_settings[] = array(
            'id' => 'job_listwith_emp_aprov',
            'type' => 'button_set',
            'title' => __('Employer approval for Jobs Listing', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('If this switch is "on" then only approved employer\'s jobs will show in listing.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $job_settings[] = array(
            'id' => 'jobs_srch_filtrs_sort',
            'type' => 'sorter',
            'title' => __('Jobs Filter Sort', 'wp-jobsearch'),
            'subtitle' => __('Jobs Search Filter Fields Sorting.', 'wp-jobsearch'),
            'desc' => __('Drag and drop to sort the fields.', 'wp-jobsearch'),
            'options' => array(
                'fields' => array(
                    'keyword_search' => __('Keyword Search', 'wp-jobsearch'),
                    'location' => __('Location', 'wp-jobsearch'),
                    'date_posted' => __('Date Posted', 'wp-jobsearch'),
                    'job_type' => __('Job Type', 'wp-jobsearch'),
                    'sector' => __('Sector', 'wp-jobsearch'),
                    'custom_fields' => __('Custom Fields', 'wp-jobsearch'),
                ),
                'disabled' => array('ads' => __('Advertisement', 'wp-jobsearch'))
            ),
        );
        $job_settings[] = array(
            'id' => 'default_no_img',
            'type' => 'media',
            'url' => true,
            'title' => __('Job Image Placeholder', 'wp-jobsearch'),
            'compiler' => 'true',
            'desc' => '',
            'subtitle' => __('Job default image on listing and detail if employer has loaded company logo.', 'wp-jobsearch'),
            'default' => array('url' => jobsearch_plugin_get_url('images/no-image.jpg')),
        );
        $job_settings[] = array(
            'id' => 'listin_map_marker_img',
            'type' => 'media',
            'url' => true,
            'title' => __('Jobs Map Marker Icon', 'wp-jobsearch'),
            'compiler' => 'true',
            'desc' => '',
            'subtitle' => '',
            'default' => array('url' => ''),
        );
        $job_settings[] = array(
            'id' => 'listin_map_cluster_img',
            'type' => 'media',
            'url' => true,
            'title' => __('Jobs Map Cluster Icon', 'wp-jobsearch'),
            'compiler' => 'true',
            'desc' => '',
            'subtitle' => '',
            'default' => array('url' => ''),
        );
        $section_settings = array(
            'title' => __('Job Settings', 'wp-jobsearch'),
            'id' => 'user-alljobs-settins',
            'desc' => __('Job Settings', 'wp-jobsearch'),
            'icon' => 'el el-check',
            'fields' => $job_settings,
        );
        $setting_sections[] = $section_settings;

        $postjob_pkgs_arr = array();
        $args = array(
            'post_type' => 'package',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
            'order' => 'DESC',
            'orderby' => 'ID',
            'meta_query' => array(
                array(
                    'key' => 'jobsearch_field_package_type',
                    'value' => apply_filters('jobsearch_emp_postjob_pkg_job_metakey', array('job', 'featured_jobs', 'emp_allin_one', 'employer_profile')),
                    'compare' => 'IN',
                ),
            ),
        );
        $pkgss_query = new WP_Query($args);

        if ($pkgss_query->have_posts()) {
            $pkgss_posts = $pkgss_query->posts;
            if (!empty($pkgss_posts)) {
                foreach ($pkgss_posts as $pkgss_post_id) {
                    $pkg_attach_product = get_post_meta($pkgss_post_id, 'jobsearch_package_product', true);

                    if ($pkg_attach_product != '' && get_page_by_path($pkg_attach_product, 'OBJECT', 'product')) {
                        $post_onj = get_post($pkgss_post_id);
                        $postjob_pkgs_arr[$pkgss_post_id] = isset($post_onj->post_title) ? $post_onj->post_title : '';
                    }
                }
            }
        }
        wp_reset_postdata();

        $postjob_fpkgs_arr = array();
        $args = array(
            'post_type' => 'package',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
            'order' => 'DESC',
            'orderby' => 'ID',
            'meta_query' => array(
                array(
                    'key' => 'jobsearch_field_package_type',
                    'value' => 'feature_job',
                    'compare' => '=',
                ),
            ),
        );
        $fpkgss_query = new WP_Query($args);

        if ($fpkgss_query->have_posts()) {
            $pkgss_posts = $fpkgss_query->posts;
            if (!empty($pkgss_posts)) {
                foreach ($pkgss_posts as $pkgss_post_id) {
                    $pkg_attach_product = get_post_meta($pkgss_post_id, 'jobsearch_package_product', true);

                    if ($pkg_attach_product != '' && get_page_by_path($pkg_attach_product, 'OBJECT', 'product')) {
                        $postjob_fpkgs_arr[$pkgss_post_id] = get_the_title($pkgss_post_id);
                    }
                }
            }
        }
        wp_reset_postdata();

        //
        $section_settings = array(
            'title' => __('Job Post Settings', 'wp-jobsearch'),
            'id' => 'user-job-posting',
            'desc' => __('User Job Post Settings', 'wp-jobsearch'),
            'subsection' => true,
            'fields' => apply_filters('jobsearch_poptions_post_job_sett_fields', array(
                array(
                    'id' => 'free-job-post-expiry',
                    'type' => 'text',
                    'title' => __('Job Expiry Days', 'wp-jobsearch'),
                    'subtitle' => __('Set default time period for job expiry.', 'wp-jobsearch'),
                    'desc' => __('Enter only number. This time period will consider in days only. i.e 1 day, 3 days, 7 days or 30 days.', 'wp-jobsearch'),
                    'default' => '15',
                ),
                array(
                    'id' => 'job-default-status',
                    'type' => 'select',
                    'title' => __('Job Status', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'approved' => __('Approved', 'wp-jobsearch'),
                        'admin-review' => __('Admin Review', 'wp-jobsearch'),
                    ),
                    'subtitle' => __('Set the default status for every new posting job.', 'wp-jobsearch'),
                    'default' => 'approved',
                ),
                array(
                    'id' => 'job-onupdate-status',
                    'type' => 'select',
                    'required' => array('job-default-status', 'equals', 'admin-review'),
                    'title' => __('Job Update Status', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'approved' => __('Approved', 'wp-jobsearch'),
                        'admin-review' => __('Admin Review', 'wp-jobsearch'),
                    ),
                    'subtitle' => __('Set job status when employer update job after approval.', 'wp-jobsearch'),
                    'default' => 'approved',
                ),
                array(
                    'id' => 'job-post-wout-reg',
                    'type' => 'button_set',
                    'title' => __('Job Post without Registration', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'job_desc_with_media',
                    'type' => 'button_set',
                    'title' => __('Job Description Media Buttons', 'wp-jobsearch'),
                    'subtitle' => __('Allow employers to add images and videos in the job description.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'job_post_restrict_img',
                    'type' => 'media',
                    'url' => true,
                    'title' => __('Restriction Image', 'wp-jobsearch'),
                    'required' => array('job-post-wout-reg', 'equals', 'off'),
                    'compiler' => 'true',
                    'subtitle' => __('Job Post Restriction Image', 'wp-jobsearch'),
                    'desc' => '',
                    'default' => array('url' => jobsearch_plugin_get_url('images/restrict-candidate.png')),
                ),
                array(
                    'id' => 'free-jobs-allow',
                    'type' => 'button_set',
                    'title' => __('Free Jobs', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Allow users to post absolutely package free jobs.', 'wp-jobsearch'),
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'preselect-featjob-pkgs',
                    'type' => 'select',
                    'multi' => true,
                    'required' => array('free-jobs-allow', 'equals', 'off'),
                    'title' => __('Pre-Selected Featured Job Packages in Post Job', 'wp-jobsearch'),
                    'subtitle' => __('Selected featured job packages will show in the list while posting a job.', 'wp-jobsearch'),
                    'options' => $postjob_fpkgs_arr,
                    'default' => '',
                    'desc' => sprintf(__('Create new package from <a href="%s" target="_blank">here</a>.', 'wp-jobsearch'), admin_url('edit.php?post_type=package')),
                ),
                array(
                    'id' => 'preselect-postjob-pkgs',
                    'type' => 'select',
                    'multi' => true,
                    'required' => array('free-jobs-allow', 'equals', 'off'),
                    'title' => __('Pre-Selected Packages in Post Job', 'wp-jobsearch'),
                    'subtitle' => __('Selected packages will show in the list while posting a job.', 'wp-jobsearch'),
                    'options' => $postjob_pkgs_arr,
                    'default' => '',
                    'desc' => sprintf(__('Create new package from <a href="%s" target="_blank">here</a>.', 'wp-jobsearch'), admin_url('edit.php?post_type=package')),
                ),
                array(
                    'id' => 'job_allow_filled',
                    'type' => 'button_set',
                    'title' => __('Filled Job', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Allow users to fill thier jobs. So no one able to apply that job if it is marked as a filled job.', 'wp-jobsearch'),
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'job_appliction_deadline',
                    'type' => 'button_set',
                    'title' => __('Job application deadline', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Allow employers to add a job application deadline date for candidates.', 'wp-jobsearch'),
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'on_req' => __('On & Required', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'job-apply-setsection',
                    'type' => 'section',
                    'title' => __('Apply job section', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'job-apply-switch',
                    'type' => 'button_set',
                    'title' => __('Apply Job', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Allow employers to post a new job with different apply job methods.', 'wp-jobsearch'),
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'apply-methods',
                    'type' => 'button_set',
                    'required' => array('job-apply-switch', 'equals', 'on'),
                    'multi' => true,
                    'title' => __('Apply Job Methods', 'wp-jobsearch'),
                    'subtitle' => __('If you want multiple choices for apply job choose here.', 'wp-jobsearch'),
                    'options' => array(
                        'internal' => __('Internal', 'wp-jobsearch'),
                        'external' => __('External Website URL', 'wp-jobsearch'),
                        'email' => __('Only Email', 'wp-jobsearch'),
                    ),
                    'default' => array('internal', 'external', 'email'),
                ),
                array(
                    'id' => 'apply_type_required',
                    'type' => 'button_set',
                    'title' => __('Required Apply Type Field', 'wp-jobsearch'),
                    'required' => array('job-apply-switch', 'equals', 'on'),
                    'desc' => '',
                    'subtitle' => __('Make Apply Type field required (*) field while Post new Job.', 'wp-jobsearch'),
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'job-apply-setsection',
                    'type' => 'section',
                    'title' => '',
                    'desc' => '',
                    'subtitle' => '',
                    'indent' => false,
                ),
                array(
                    'id' => 'caned_resp_switch',
                    'type' => 'button_set',
                    'title' => __('Job Description Templates', 'wp-jobsearch'),
                    'subtitle' => __('Allow Job Description Templates for posting job description.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'caned_resp_jobpost_title',
                    'type' => 'text',
                    'required' => array('caned_resp_switch', 'equals', 'on'),
                    'title' => __('Templates Dropdown Title', 'wp-jobsearch'),
                    'subtitle' => __('Add Job Description Templates dropdown title.', 'wp-jobsearch'),
                    'desc' => '',
                    'default' => __('Canned Description', 'wp-jobsearch'),
                ),
                array(
                    'id' => 'caned_resp_def_tempid',
                    'type' => 'select',
                    'title' => __('Default Template', 'wp-jobsearch'),
                    'required' => array('caned_resp_switch', 'equals', 'on'),
                    'subtitle' => __('Select default template which content will load by default while posting job. Add new templates <a href="' . admin_url('edit.php?post_type=jobdesctemp') . '" target="_blank">here</a>', 'wp-jobsearch'),
                    'options' => $jobdesc_temps,
                    'default' => '',
                    'desc' => '',
                ),
                array(
                    'id' => 'duplicate_the_job',
                    'type' => 'button_set',
                    'title' => __('Duplicate Job', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Allow users to duplicate their jobs.', 'wp-jobsearch'),
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'dash_edit_the_job',
                    'type' => 'button_set',
                    'title' => __('Update Job', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Allow users to update their jobs.', 'wp-jobsearch'),
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'republish_the_job',
                    'type' => 'button_set',
                    'title' => __('Republish Job', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Allow users to re-publish their jobs.', 'wp-jobsearch'),
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'job-skill-switch',
                    'type' => 'button_set',
                    'title' => __('Job Skills', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Allow users to add skills during post job.', 'wp-jobsearch'),
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'job_max_skills',
                    'type' => 'text',
                    'title' => __('Max. Skills allow', 'wp-jobsearch'),
                    'required' => array('job-skill-switch', 'equals', 'on'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '5',
                ),
                array(
                    'id' => 'job_sugg_skills',
                    'type' => 'text',
                    'title' => __('Max. Suggested Skills Show', 'wp-jobsearch'),
                    'required' => array('job-skill-switch', 'equals', 'on'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '15',
                ),
                array(
                    'id' => 'job_title_length',
                    'type' => 'text',
                    'title' => __('Job Title Max. Length', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Define Job Title Max. Length in characters.', 'wp-jobsearch'),
                    'default' => '1000',
                ),
                array(
                    'id' => 'job_desc_length',
                    'type' => 'text',
                    'title' => __('Job Description Max. Length', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Define Job Description Max. Length in character.', 'wp-jobsearch'),
                    'default' => '5000',
                ),
                array(
                    'id' => 'job-attachments-settings',
                    'type' => 'section',
                    'title' => __('Job Attachments', 'wp-jobsearch'),
                    'subtitle' => __('Job Attachments settings.', 'wp-jobsearch'),
                    'indent' => true,
                ),
                array(
                    'id' => 'job_attachments',
                    'type' => 'button_set',
                    'title' => __('Job Attachments', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('Allow users to attach files while posting jobs.', 'wp-jobsearch'),
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'number_of_attachments',
                    'type' => 'text',
                    'title' => __('Number of Attachments', 'wp-jobsearch'),
                    'required' => array('job_attachments', 'equals', 'on'),
                    'subtitle' => __('Allow the employer to add attachments to the job.', 'wp-jobsearch'),
                    'desc' => '',
                    'default' => '5',
                ),
                array(
                    'id' => 'job_attachment_types',
                    'type' => 'select',
                    'multi' => true,
                    'title' => __('Attachments File Types', 'wp-jobsearch'),
                    'required' => array('job_attachments', 'equals', 'on'),
                    'desc' => '',
                    'options' => array(
                        'text/plain' => __('text', 'wp-jobsearch'),
                        'image/jpeg' => __('jpeg', 'wp-jobsearch'),
                        'image/png' => __('png', 'wp-jobsearch'),
                        'application/msword' => __('doc', 'wp-jobsearch'),
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => __('docx', 'wp-jobsearch'),
                        'application/vnd.ms-excel' => __('xls', 'wp-jobsearch'),
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => __('xlsx', 'wp-jobsearch'),
                        'application/pdf' => __('pdf', 'wp-jobsearch'),
                    ),
                    'default' => array('application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf'),
                    'subtitle' => __('Select file formats.', 'wp-jobsearch'),
                ),
                array(
                    'id' => 'attach_file_size',
                    'type' => 'select',
                    'title' => __('Max. File Size', 'wp-jobsearch'),
                    'required' => array('job_attachments', 'equals', 'on'),
                    'subtitle' => __('Restrict file size of attachment files.', 'wp-jobsearch'),
                    'options' => array(
                        '300' => __('300KB', 'wp-jobsearch'),
                        '500' => __('500KB', 'wp-jobsearch'),
                        '750' => __('750KB', 'wp-jobsearch'),
                        '1024' => __('1Mb', 'wp-jobsearch'),
                        '2048' => __('2Mb', 'wp-jobsearch'),
                        '3072' => __('3Mb', 'wp-jobsearch'),
                        '4096' => __('4Mb', 'wp-jobsearch'),
                        '5120' => __('5Mb', 'wp-jobsearch'),
                        '10120' => __('10Mb', 'wp-jobsearch'),
                        '50120' => __('50Mb', 'wp-jobsearch'),
                        '100120' => __('100Mb', 'wp-jobsearch'),
                        '200120' => __('200Mb', 'wp-jobsearch'),
                        '300120' => __('300Mb', 'wp-jobsearch'),
                        '500120' => __('500Mb', 'wp-jobsearch'),
                        '1000120' => __('1Gb', 'wp-jobsearch'),
                    ),
                    'desc' => '',
                    'default' => '1024',
                ),
                array(
                    'id' => 'job-submit-settings',
                    'type' => 'section',
                    'title' => __('Job Submission', 'wp-jobsearch'),
                    'subtitle' => __('Job Submission settings.', 'wp-jobsearch'),
                    'indent' => true,
                ),
                array(
                    'id' => 'job-submit-title',
                    'type' => 'text',
                    'title' => __('Job Submission Title', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('This title will show when a user will submit a new job.', 'wp-jobsearch'),
                    'default' => __('Thank you for submitting', 'wp-jobsearch'),
                ),
                array(
                    'id' => 'job-submit-msge',
                    'type' => 'textarea',
                    'title' => __('Job Submission Message', 'wp-jobsearch'),
                    'desc' => '',
                    'subtitle' => __('This message will show when a user will submit a new job.', 'wp-jobsearch'),
                    'default' => sprintf(__('Thank you for submitting, your job has been published. If you need help please contact us via email %s', 'wp-jobsearch'), get_bloginfo('admin_email')),
                ),
                array(
                    'id' => 'job-submit-img',
                    'type' => 'media',
                    'url' => true,
                    'title' => __('Job Submission Image', 'wp-jobsearch'),
                    'compiler' => 'true',
                    'subtitle' => __('Confirmation Tab Image', 'wp-jobsearch'),
                    'desc' => '',
                    'default' => array('url' => jobsearch_plugin_get_url('images/employer-confirmation-icon.png')),
                ),
                array(
                    'id' => 'job-instamatch-settings',
                    'type' => 'section',
                    'title' => __('Job Insta Match Candidates', 'wp-jobsearch'),
                    'subtitle' => __('Job posting Insta Match candidates settings.', 'wp-jobsearch'),
                    'indent' => true,
                ),
                array(
                    'id' => 'job_posttin_instamatch_cand',
                    'type' => 'button_set',
                    'title' => __('Insta Match Candidates', 'wp-jobsearch'),
                    'subtitle' => __('Insta match candidates will show in applicants section of employer dashboard.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'job_instamatch_by_seprates',
                    'type' => 'button_set',
                    'multi' => true,
                    'required' => array('job_posttin_instamatch_cand', 'equals', 'on'),
                    'title' => __('Match Candidates by', 'wp-jobsearch'),
                    'subtitle' => __('Select sections in which candidates will search for insta match', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'jobtitle' => __('Job Title', 'wp-jobsearch'),
                        'sector' => __('Sector', 'wp-jobsearch'),
                        'skills' => __('Skills', 'wp-jobsearch'),
                        'location' => __('Location', 'wp-jobsearch'),
                    ),
                    'default' => array('jobtitle', 'sector', 'skills', 'location'),
                ),
                array(
                    'id' => 'job_instamatch_max_cands',
                    'type' => 'text',
                    'required' => array('job_posttin_instamatch_cand', 'equals', 'on'),
                    'title' => __('Max. Match Candidates to match', 'wp-jobsearch'),
                    'subtitle' => __('Maximum Candidates to Match with a single job.', 'wp-jobsearch'),
                    'desc' => '',
                    'default' => '100',
                ),
            ))
        );
        $setting_sections[] = $section_settings;

        //
        $job_detail_settins = array();
        $job_detail_settins = apply_filters('job_detail_pages_styles', $job_detail_settins);
        //
        $job_detail_settins[] = array(
            'id' => 'job_detail_views_count',
            'type' => 'button_set',
            'title' => __('Views Count', 'wp-jobsearch'),
            'subtitle' => __('Job views count on job detail page.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $job_detail_settins[] = array(
            'id' => 'job_detail_apps_count',
            'type' => 'button_set',
            'title' => __('Applications Count', 'wp-jobsearch'),
            'subtitle' => __('Job applications count on job detail page.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $job_detail_settins[] = array(
            'id' => 'job_detail_email_btn',
            'type' => 'button_set',
            'title' => __('Send Email Button', 'wp-jobsearch'),
            'subtitle' => __('Send Email Button on job detail page.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $job_detail_settins[] = array(
            'id' => 'job_detail_shrtlist_btn',
            'type' => 'button_set',
            'title' => __('Shortlist Button', 'wp-jobsearch'),
            'subtitle' => __('Show/Hide Shortlist Button for job.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $job_detail_settins[] = array(
            'id' => 'job_detail_rel_jobs',
            'type' => 'button_set',
            'title' => __('Related Jobs', 'wp-jobsearch'),
            'subtitle' => __('Related Jobs on the job detail page.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $job_detail_settins[] = array(
            'id' => 'job_detail_emp_jobs',
            'type' => 'button_set',
            'title' => __('Employer More Jobs', 'wp-jobsearch'),
            'subtitle' => __('Employer\'s more Jobs on the job detail page.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $job_detail_settins[] = array(
            'id' => 'related_jobs_title_length',
            'type' => 'text',
            'required' => array('job_detail_rel_jobs', 'equals', 'on'),
            'title' => __('Related Jobs Title Length', 'wp-jobsearch'),
            'subtitle' => __('Title words length in Related Jobs list.', 'wp-jobsearch'),
            'desc' => '',
            'default' => '50',
        );
        $job_detail_settins[] = array(
            'id' => 'job_det_contact_form',
            'type' => 'button_set',
            'title' => __('Job Detail Contact Form', 'wp-jobsearch'),
            'subtitle' => __('Allow user to contact the employer at the job detail page.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('Public', 'wp-jobsearch'),
                'cand_login' => __('With Candidate Login', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $job_detail_settins[] = array(
            'id' => 'job_det_loc_listing',
            'type' => 'select',
            'multi' => true,
            'title' => __('Locations in Job detail page', 'wp-jobsearch'),
            'subtitle' => __('Select which type of location in Job Detail', 'wp-jobsearch'),
            'options' => array(
                'country' => esc_html__("Country", "wp-jobsearch"),
                'state' => esc_html__("State", "wp-jobsearch"),
                'city' => esc_html__("City", "wp-jobsearch"),
            ),
            'default' => '',
        );
        $job_detail_settins[] = array(
            'id' => 'job_views_publish_date',
            'type' => 'button_set',
            'title' => __('Jobs Publish date', 'wp-jobsearch'),
            'subtitle' => __('Enable/Disable job publish date in jobs listing and detail pages.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $job_detail_settins[] = array(
            'id' => 'expire-job-sec-setting',
            'type' => 'section',
            'title' => __('Expire Job Notification Settings', 'wp-jobsearch'),
            'subtitle' => '',
            'desc' => '',
            'indent' => true,
        );
        $job_detail_settins[] = array(
            'id' => 'expire_job_image',
            'type' => 'media',
            'url' => true,
            'title' => __('Notification Image', 'wp-jobsearch'),
            'compiler' => 'true',
            'subtitle' => __('Add Expire Job Notification image.', 'wp-jobsearch'),
            'desc' => '',
            'default' => array('url' => jobsearch_plugin_get_url('images/job-error.png')),
        );
        $job_detail_settins[] = array(
            'id' => 'expire_job_heading',
            'type' => 'text',
            'title' => __('Notification Heading', 'wp-jobsearch'),
            'subtitle' => '',
            'desc' => '',
            'default' => 'We\'re Sorry Opps! Job Expired',
        );
        $job_detail_settins[] = array(
            'id' => 'expire_job_notify_desc',
            'type' => 'textarea',
            'title' => __('Notification Text', 'wp-jobsearch'),
            'subtitle' => '',
            'desc' => '',
            'default' => 'Unable to access the link. Job has been expired. Please contact the admin or who shared the link with you.',
        );
        $job_detail_settins[] = array(
            'id' => 'expire_job_footer_logo',
            'type' => 'media',
            'url' => true,
            'title' => __('Notification Logo', 'wp-jobsearch'),
            'compiler' => 'true',
            'subtitle' => __('Add Expire Job Notification powered by Logo.', 'wp-jobsearch'),
            'desc' => '',
            'default' => '',
        );
        $section_settings = array(
            'title' => __('Job Detail Settings', 'wp-jobsearch'),
            'id' => 'job-detail-settins',
            'desc' => '',
            'subsection' => true,
            'fields' => $job_detail_settins
        );
        $setting_sections[] = $section_settings;

        $aftr_jobdetail_sec = apply_filters('jobsearch_plugin_opts_after_jobdetail_setts', array());
        if (!empty($aftr_jobdetail_sec)) {
            $setting_sections[] = $aftr_jobdetail_sec;
        }

        // Do not use it for core project
        $aftr_jobdetail_sec = apply_filters('jobsearch_plugin_opts_after_jobdetail_setts_hook', array());
        if (!empty($aftr_jobdetail_sec)) {
            $setting_sections[] = $aftr_jobdetail_sec;
        }

        //
        $pckgs_membs_settings = array();
        $pckgs_membs_settings[] = array(
            'id' => 'once_free_pckg_switch',
            'type' => 'button_set',
            'title' => __('Free package for once', 'wp-jobsearch'),
            'subtitle' => __('Restrict users to subscribe to free package for once only.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $section_settings = array(
            'title' => __('Package Settings', 'wp-jobsearch'),
            'id' => 'jobsearch-oallpck-settins',
            'icon' => 'el el-gift',
            'desc' => '',
            'fields' => $pckgs_membs_settings
        );
        $setting_sections[] = $section_settings;

        // packages list
        $asign_pkgs_arr = array();
        $args = array(
            'post_type' => 'package',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
            'order' => 'DESC',
            'orderby' => 'ID',
            'meta_query' => array(
                array(
                    'key' => 'jobsearch_field_package_type',
                    'value' => array('candidate', 'urgent_pkg', 'promote_profile', 'candidate_profile', 'cand_resume'),
                    'compare' => 'IN',
                ),
            ),
        );
        $pkgss_query = new WP_Query($args);

        if ($pkgss_query->have_posts()) {
            $pkgss_posts = $pkgss_query->posts;
            if (!empty($pkgss_posts)) {
                foreach ($pkgss_posts as $pkgss_post_id) {
                    $pkg_attach_product = get_post_meta($pkgss_post_id, 'jobsearch_package_product', true);

                    if ($pkg_attach_product != '' && get_page_by_path($pkg_attach_product, 'OBJECT', 'product')) {
                        $asign_pkgs_arr[$pkgss_post_id] = get_the_title($pkgss_post_id);
                    }
                }
            }
        }
        wp_reset_postdata();
        //

        $cand_pckgs_settings = array();
        $cand_pckgs_settings[] = array(
            'id' => 'cand_asignpkgs_onsignup',
            'type' => 'select',
            'multi' => true,
            'title' => __('Assign Packages at signup', 'wp-jobsearch'),
            'subtitle' => __('Selected packages will automatically assigned to a newly registered candidate.', 'wp-jobsearch'),
            'options' => $asign_pkgs_arr,
            'default' => '',
            'desc' => sprintf(__('Create new package from <a href="%s" target="_blank">here</a>.', 'wp-jobsearch'), admin_url('edit.php?post_type=package')),
        );
        $cand_pckgs_settings[] = array(
            'id' => 'cand-profpkg-settings',
            'type' => 'section',
            'title' => __('Package Based Profile Settings', 'wp-jobsearch'),
            'subtitle' => '',
            'indent' => true,
        );
        $cand_pckgs_settings[] = array(
            'id' => 'cand_pkg_base_profile',
            'type' => 'button_set',
            'title' => __('Package Based Profile', 'wp-jobsearch'),
            'subtitle' => __('Manage candidate profile settings according to his/her selected package.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $cand_pckgs_settings[] = array(
            'id' => 'cand_pkg_base_profile_info',
            'required' => array('cand_pkg_base_profile', 'equals', 'on'),
            'type' => 'info',
            'style' => 'warning',
            'title' => __('Default Settings for Candidate Profile', 'redux-framework-demo'),
            'icon' => 'el el-info-circle',
            'desc' => __('Set default settings fields and dashboard sections which will allow to modify or perform by candidate without any package.', 'wp-jobsearch')
        );
        $cand_pckgs_settings[] = array(
            'id' => 'cand_pkgbase_profile_defields',
            'type' => 'select',
            'multi' => true,
            'title' => __('Profile Fields', 'wp-jobsearch'),
            'required' => array('cand_pkg_base_profile', 'equals', 'on'),
            'subtitle' => __('Select default profile fields for candidates.', 'wp-jobsearch'),
            'options' => array(
                'cover_img' => __('Cover Photo', 'wp-jobsearch'),
                'profile_url' => __('Profile URL', 'wp-jobsearch'),
                'public_view' => __('Profile for Public View', 'wp-jobsearch'),
                'date_of_birth' => __('Date of Birth', 'wp-jobsearch'),
                'phone' => __('Phone', 'wp-jobsearch'),
                'sector' => __('Sector', 'wp-jobsearch'),
                'job_title' => __('Job Title', 'wp-jobsearch'),
                'salary' => __('Salary', 'wp-jobsearch'),
                'about_desc' => __('Description', 'wp-jobsearch'),
            ),
            'default' => array('cover_img', 'public_view', 'sector'),
            'desc' => '',
        );
        $candidate_social_mlinks = isset($jobsearch__options['candidate_social_mlinks']) ? $jobsearch__options['candidate_social_mlinks'] : '';
        $cand_pkgbase_social_arr = array(
            'facebook' => __('Facebook', 'wp-jobsearch'),
            'twitter' => __('Twitter', 'wp-jobsearch'),
            'linkedin' => __('Linkedin', 'wp-jobsearch'),
            'dribbble' => __('Dribbble', 'wp-jobsearch'),
        );
        if (!empty($candidate_social_mlinks)) {
            if (isset($candidate_social_mlinks['title']) && is_array($candidate_social_mlinks['title'])) {
                $field_counter = 0;
                foreach ($candidate_social_mlinks['title'] as $cand_social_mlink) {
                    $cand_pkgbase_social_arr['dynm_social' . $field_counter] = $cand_social_mlink;
                    $field_counter++;
                }
            }
        }
        $cand_pckgs_settings[] = array(
            'id' => 'cand_pkgbase_social_defields',
            'type' => 'select',
            'multi' => true,
            'title' => __('Social Fields', 'wp-jobsearch'),
            'required' => array('cand_pkg_base_profile', 'equals', 'on'),
            'subtitle' => __('Select default Social fields for candidates.', 'wp-jobsearch'),
            'options' => $cand_pkgbase_social_arr,
            'default' => '',
            'desc' => '',
        );
        $cand_custom_fields_saved_data = get_option('jobsearch_custom_field_candidate');
        if (is_array($cand_custom_fields_saved_data) && sizeof($cand_custom_fields_saved_data) > 0) {
            $cand_pkgbase_cusfileds_arr = array();
            foreach ($cand_custom_fields_saved_data as $cand_cus_field_key => $cand_cus_field_kdata) {
                $cusfield_label = isset($cand_cus_field_kdata['label']) ? $cand_cus_field_kdata['label'] : '';
                $cusfield_name = isset($cand_cus_field_kdata['name']) ? $cand_cus_field_kdata['name'] : '';
                if ($cusfield_label != '' && $cusfield_name != '') {
                    $cand_pkgbase_cusfileds_arr[$cusfield_name] = $cusfield_label;
                }
            }
            //
            $cand_pckgs_settings[] = array(
                'id' => 'cand_pkgbase_custom_defields',
                'type' => 'select',
                'multi' => true,
                'title' => __('Custom Fields', 'wp-jobsearch'),
                'required' => array('cand_pkg_base_profile', 'equals', 'on'),
                'subtitle' => __('Select default custom fields for candidates.', 'wp-jobsearch'),
                'options' => $cand_pkgbase_cusfileds_arr,
                'default' => '',
                'desc' => '',
            );
        }
        $dash_menu_linksar = array(
            'my_profile' => __('My Profile', 'wp-jobsearch'),
            'my_resume' => __('My Resume', 'wp-jobsearch'),
            'applied_jobs' => __('Applied Jobs', 'wp-jobsearch'),
            'cv_manager' => __('CV Manager', 'wp-jobsearch'),
            'fav_jobs' => __('Favorite Jobs', 'wp-jobsearch'),
            'packages' => __('Packages', 'wp-jobsearch'),
            'transactions' => __('Transactions', 'wp-jobsearch'),
            'my_emails' => __('My Emails', 'wp-jobsearch'),
            'following' => __('Following', 'wp-jobsearch'),
            'change_password' => __('Change Password', 'wp-jobsearch'),
        );
        $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
        $post_ids_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta";
        $post_ids_query .= " ON postmeta.post_id = posts.ID";
        $post_ids_query .= " WHERE post_type='dashb_menu' AND post_status='publish'";
        $post_ids_query .= " AND ((postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='cand') OR (postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='both'));";

        $cusmenu_post_ids = $wpdb->get_col($post_ids_query);

        if (!empty($cusmenu_post_ids)) {
            foreach ($cusmenu_post_ids as $cust_dashpage) {
                $the_page = get_post($cust_dashpage);
                if (isset($the_page->ID)) {
                    $dash_menu_linksar[$the_page->post_name] = $the_page->post_title;
                }
            }
        }
        $cand_pckgs_settings[] = array(
            'id' => 'cand_pkgbase_dashtabs_defields',
            'type' => 'select',
            'multi' => true,
            'title' => __('Dashboard Sections', 'wp-jobsearch'),
            'required' => array('cand_pkg_base_profile', 'equals', 'on'),
            'subtitle' => __('Select which dashboard sections a candidate can access by default.', 'wp-jobsearch'),
            'options' => apply_filters('jobsearch_cand_dash_menu_in_opts', $dash_menu_linksar),
            'default' => '',
            'desc' => '',
        );
        $cand_pckgs_settings[] = array(
            'id' => 'cand_pkgbase_stats_defields',
            'type' => 'button_set',
            'title' => __('Candidate Statistics', 'wp-jobsearch'),
            'required' => array('cand_pkg_base_profile', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $cand_pckgs_settings[] = array(
            'id' => 'cand_pkgbase_location_defields',
            'type' => 'button_set',
            'title' => __('Location Fields', 'wp-jobsearch'),
            'required' => array('cand_pkg_base_profile', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $cand_pckgs_settings[] = array(
            'id' => 'cand_pkgbase_coverltr_defields',
            'type' => 'button_set',
            'title' => __('Cover Letter', 'wp-jobsearch'),
            'required' => array('cand_pkg_base_profile', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $cand_pckgs_settings[] = array(
            'id' => 'cand_pkgbase_resmedu_defields',
            'type' => 'button_set',
            'title' => __('Education', 'wp-jobsearch'),
            'required' => array('cand_pkg_base_profile', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $cand_pckgs_settings[] = array(
            'id' => 'cand_pkgbase_resmexp_defields',
            'type' => 'button_set',
            'title' => __('Experience', 'wp-jobsearch'),
            'required' => array('cand_pkg_base_profile', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $cand_pckgs_settings[] = array(
            'id' => 'cand_pkgbase_resmport_defields',
            'type' => 'button_set',
            'title' => __('Portfolio', 'wp-jobsearch'),
            'required' => array('cand_pkg_base_profile', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $cand_pckgs_settings[] = array(
            'id' => 'cand_pkgbase_resmskills_defields',
            'type' => 'button_set',
            'title' => __('Expertise', 'wp-jobsearch'),
            'required' => array('cand_pkg_base_profile', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $cand_pckgs_settings[] = array(
            'id' => 'cand_pkgbase_resmawards_defields',
            'type' => 'button_set',
            'title' => __('Honors & Awards', 'wp-jobsearch'),
            'required' => array('cand_pkg_base_profile', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $cand_pckgs_settings[] = array(
            'id' => 'cand-profpkg-sett-sec-end',
            'type' => 'section',
            'indent' => false,
        );
        $section_settings = array(
            'title' => __('Candidate Package Settings', 'wp-jobsearch'),
            'id' => 'jobsearch-ocandpck-settins',
            'subsection' => true,
            'desc' => '',
            'fields' => apply_filters('jobsearch_options_cand_profpkgset_sec_fields', $cand_pckgs_settings),
        );

        if (!empty($section_settings['fields'])) {
            $setting_sections[] = $section_settings;
        }

        // packages list
        $asign_pkgs_arr = array();
        $args = array(
            'post_type' => 'package',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
            'order' => 'DESC',
            'orderby' => 'ID',
            'meta_query' => array(
                array(
                    'key' => 'jobsearch_field_package_type',
                    'value' => array('job', 'featured_jobs', 'cv', 'urgent_pkg', 'promote_profile', 'employer_profile', 'emp_allin_one'),
                    'compare' => 'IN',
                ),
            ),
        );
        $pkgss_query = new WP_Query($args);

        if ($pkgss_query->have_posts()) {
            $pkgss_posts = $pkgss_query->posts;
            if (!empty($pkgss_posts)) {
                foreach ($pkgss_posts as $pkgss_post_id) {
                    $pkg_attach_product = get_post_meta($pkgss_post_id, 'jobsearch_package_product', true);

                    if ($pkg_attach_product != '' && get_page_by_path($pkg_attach_product, 'OBJECT', 'product')) {
                        $post_onj = get_post($pkgss_post_id);
                        $asign_pkgs_arr[$pkgss_post_id] = isset($post_onj->post_title) ? $post_onj->post_title : '';
                    }
                }
            }
        }
        wp_reset_postdata();
        //

        $emp_pckgs_settings = array();
        $emp_pckgs_settings[] = array(
            'id' => 'emp_asignpkgs_onsignup',
            'type' => 'select',
            'multi' => true,
            'title' => __('Assign Packages at signup', 'wp-jobsearch'),
            'subtitle' => __('Selected packages will automatically assigned to a newly registered employer.', 'wp-jobsearch'),
            'options' => $asign_pkgs_arr,
            'default' => '',
            'desc' => sprintf(__('Create new package from <a href="%s" target="_blank">here</a>.', 'wp-jobsearch'), admin_url('edit.php?post_type=package')),
        );
        $emp_pckgs_settings[] = array(
            'id' => 'emp-profpkg-settings',
            'type' => 'section',
            'title' => __('Package Based Profile Settings', 'wp-jobsearch'),
            'subtitle' => '',
            'indent' => true,
        );
        $emp_pckgs_settings[] = array(
            'id' => 'emp_pkg_base_profile',
            'type' => 'button_set',
            'title' => __('Package Based Profile', 'wp-jobsearch'),
            'subtitle' => __('Manage employer profile settings according to his/her selected package.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $emp_pckgs_settings[] = array(
            'id' => 'emp_pkg_base_profile_info',
            'required' => array('emp_pkg_base_profile', 'equals', 'on'),
            'type' => 'info',
            'style' => 'warning',
            'title' => __('Default Settings for Employer Profile', 'redux-framework-demo'),
            'icon' => 'el el-info-circle',
            'desc' => __('Set default settings fields and dashboard sections which will allow to modify or perform by employer without any package.', 'wp-jobsearch')
        );
        $emp_pckgs_settings[] = array(
            'id' => 'emp_pkgbase_profile_defields',
            'type' => 'select',
            'multi' => true,
            'title' => __('Profile Fields', 'wp-jobsearch'),
            'required' => array('emp_pkg_base_profile', 'equals', 'on'),
            'subtitle' => __('Select default profile fields for employers.', 'wp-jobsearch'),
            'options' => array(
                'jobs_cover_img' => __('Jobs Cover Photo', 'wp-jobsearch'),
                'profile_url' => __('Profile URL', 'wp-jobsearch'),
                'public_view' => __('Profile for Public View', 'wp-jobsearch'),
                'phone' => __('Phone', 'wp-jobsearch'),
                'website' => __('Website', 'wp-jobsearch'),
                'sector' => __('Sector', 'wp-jobsearch'),
                'founded_date' => __('Founded Date', 'wp-jobsearch'),
                'about_company' => __('About the Company', 'wp-jobsearch'),
            ),
            'default' => array('jobs_cover_img', 'public_view', 'sector'),
            'desc' => '',
        );
        $employer_social_mlinks = isset($jobsearch__options['employer_social_mlinks']) ? $jobsearch__options['employer_social_mlinks'] : '';
        $emp_pkgbase_social_arr = array(
            'facebook' => __('Facebook', 'wp-jobsearch'),
            'twitter' => __('Twitter', 'wp-jobsearch'),
            'google_plus' => __('Google Plus', 'wp-jobsearch'),
            'linkedin' => __('Linkedin', 'wp-jobsearch'),
            'dribbble' => __('Dribbble', 'wp-jobsearch'),
        );
        if (!empty($employer_social_mlinks)) {
            if (isset($employer_social_mlinks['title']) && is_array($employer_social_mlinks['title'])) {
                $field_counter = 0;
                foreach ($employer_social_mlinks['title'] as $emp_social_mlink) {
                    $emp_pkgbase_social_arr['dynm_social' . $field_counter] = $emp_social_mlink;
                    $field_counter++;
                }
            }
        }
        $emp_pckgs_settings[] = array(
            'id' => 'emp_pkgbase_social_defields',
            'type' => 'select',
            'multi' => true,
            'title' => __('Social Fields', 'wp-jobsearch'),
            'required' => array('emp_pkg_base_profile', 'equals', 'on'),
            'subtitle' => __('Select default Social fields for employers.', 'wp-jobsearch'),
            'options' => $emp_pkgbase_social_arr,
            'default' => '',
            'desc' => '',
        );
        $emp_custom_fields_saved_data = get_option('jobsearch_custom_field_employer');
        if (is_array($emp_custom_fields_saved_data) && sizeof($emp_custom_fields_saved_data) > 0) {
            $emp_pkgbase_cusfileds_arr = array();
            foreach ($emp_custom_fields_saved_data as $emp_cus_field_key => $emp_cus_field_kdata) {
                $cusfield_label = isset($emp_cus_field_kdata['label']) ? $emp_cus_field_kdata['label'] : '';
                $cusfield_name = isset($emp_cus_field_kdata['name']) ? $emp_cus_field_kdata['name'] : '';
                if ($cusfield_label != '' && $cusfield_name != '') {
                    $emp_pkgbase_cusfileds_arr[$cusfield_name] = $cusfield_label;
                }
            }
            //
            $emp_pckgs_settings[] = array(
                'id' => 'emp_pkgbase_custom_defields',
                'type' => 'select',
                'multi' => true,
                'title' => __('Custom Fields', 'wp-jobsearch'),
                'required' => array('emp_pkg_base_profile', 'equals', 'on'),
                'subtitle' => __('Select default custom fields for employers.', 'wp-jobsearch'),
                'options' => $emp_pkgbase_cusfileds_arr,
                'default' => '',
                'desc' => '',
            );
        }
        $dash_menu_linksar = array(
            'company_profile' => __('Company Profile', 'wp-jobsearch'),
            'post_new_job' => __('Post a New Job', 'wp-jobsearch'),
            'manage_jobs' => __('Manage Jobs', 'wp-jobsearch'),
            'all_applicants' => __('All Applicants', 'wp-jobsearch'),
            'saved_candidates' => __('Saved Candidates', 'wp-jobsearch'),
            'packages' => __('Packages', 'wp-jobsearch'),
            'transactions' => __('Transactions', 'wp-jobsearch'),
            'my_emails' => __('My Emails', 'wp-jobsearch'),
            'followers' => __('Followers', 'wp-jobsearch'),
            'change_password' => __('Change Password', 'wp-jobsearch'),
        );
        $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
        $post_ids_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta";
        $post_ids_query .= " ON postmeta.post_id = posts.ID";
        $post_ids_query .= " WHERE post_type='dashb_menu' AND post_status='publish'";
        $post_ids_query .= " AND ((postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='emp') OR (postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='both'));";

        $cusmenu_post_ids = $wpdb->get_col($post_ids_query);

        if (!empty($cusmenu_post_ids)) {
            foreach ($cusmenu_post_ids as $cust_dashpage) {
                $the_page = get_post($cust_dashpage);
                if (isset($the_page->ID)) {
                    $dash_menu_linksar[$the_page->post_name] = $the_page->post_title;
                }
            }
        }
        $emp_pckgs_settings[] = array(
            'id' => 'emp_pkgbase_dashtabs_defields',
            'type' => 'select',
            'multi' => true,
            'title' => __('Dashboard Sections', 'wp-jobsearch'),
            'required' => array('emp_pkg_base_profile', 'equals', 'on'),
            'subtitle' => __('Select which dashboard sections an employer can access by default.', 'wp-jobsearch'),
            'options' => apply_filters('jobsearch_emp_dash_menu_in_opts', $dash_menu_linksar),
            'default' => '',
            'desc' => '',
        );
        $emp_pckgs_settings[] = array(
            'id' => 'emp_pkgbase_stats_defields',
            'type' => 'button_set',
            'title' => __('Employer Statistics', 'wp-jobsearch'),
            'required' => array('emp_pkg_base_profile', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $emp_pckgs_settings[] = array(
            'id' => 'emp_pkgbase_location_defields',
            'type' => 'button_set',
            'title' => __('Location Fields', 'wp-jobsearch'),
            'required' => array('emp_pkg_base_profile', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $emp_pckgs_settings[] = array(
            'id' => 'emp_pkgbase_accmembs_defields',
            'type' => 'button_set',
            'title' => __('Account Members', 'wp-jobsearch'),
            'required' => array('emp_pkg_base_profile', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $emp_pckgs_settings[] = array(
            'id' => 'emp_pkgbase_team_defields',
            'type' => 'button_set',
            'title' => __('Employer Team', 'wp-jobsearch'),
            'required' => array('emp_pkg_base_profile', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $emp_pckgs_settings[] = array(
            'id' => 'emp_pkgbase_award_defields',
            'type' => 'button_set',
            'title' => __('Employer Awards', 'wp-jobsearch'),
            'required' => array('emp_pkg_base_profile', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $emp_pckgs_settings[] = array(
            'id' => 'emp_pkgbase_affiliation_defields',
            'type' => 'button_set',
            'title' => __('Affiliations', 'wp-jobsearch'),
            'required' => array('emp_pkg_base_profile', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $emp_pckgs_settings[] = array(
            'id' => 'emp_pkgbase_gphotos_defields',
            'type' => 'button_set',
            'title' => __('Company Photos/Videos', 'wp-jobsearch'),
            'required' => array('emp_pkg_base_profile', 'equals', 'on'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $emp_pckgs_settings[] = array(
            'id' => 'emp-profpkg-sett-sec-end',
            'type' => 'section',
            'indent' => false,
        );
        $section_settings = array(
            'title' => __('Employer Package Settings', 'wp-jobsearch'),
            'id' => 'jobsearch-oempcks-settins',
            'subsection' => true,
            'desc' => '',
            'fields' => $emp_pckgs_settings
        );
        $setting_sections[] = $section_settings;

        $can_restcion_settings = array();
        $can_restcion_settings[] = array(
            'id' => 'restrict_candidates_list',
            'type' => 'button_set',
            'title' => __('Fully Restrict Candidates Listing', 'wp-jobsearch'),
            'subtitle' => __('Restrict Candidates Listing page for all users except employers.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $can_restcion_settings[] = array(
            'id' => 'restrict_candidates',
            'type' => 'button_set',
            'title' => __('Fully Restrict Candidate Detail', 'wp-jobsearch'),
            'subtitle' => __('Restrict Candidate detail page for all users except employers.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $can_restcion_settings[] = array(
            'id' => 'restrict_cand_msg',
            'type' => 'textarea',
            'title' => __('Restrict page Message', 'wp-jobsearch'),
            'subtitle' => __('Message for restrict candidate page.', 'wp-jobsearch'),
            'desc' => '',
            'default' => __('THE PAGE IS RESTRICTED ONLY FOR SUBSCRIBED EMPLOYERS', 'wp-jobsearch'),
        );
        $can_restcion_settings[] = array(
            'id' => 'candidate_restrict_img',
            'type' => 'media',
            'url' => true,
            'title' => __('Restriction Image', 'wp-jobsearch'),
            //'required' => array('restrict_candidates', 'equals', 'on'),
            'compiler' => 'true',
            'subtitle' => __('Candidate Restriction Image', 'wp-jobsearch'),
            'desc' => '',
            'default' => array('url' => jobsearch_plugin_get_url('images/restrict-candidate.png')),
        );
        $can_restcion_settings[] = array(
            'id' => 'restrict_candidates_for_users',
            'type' => 'button_set',
            'title' => __('Restrict for Employers', 'wp-jobsearch'),
            //'required' => array('restrict_candidates', 'equals', 'on'),
            'subtitle' => __('1. All registered employers can view candidates. <br> 2. All registered candidates/employers can view candidates. <br> 3. Registered employers who purchased resume package can view candidates. <br> 4. Employer can view only their own applicants candidates.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'register' => __('1. Register Employers', 'wp-jobsearch'),
                'register_empcand' => __('2. Register Candidates/Employers', 'wp-jobsearch'),
                'register_resume' => __('3. Register Employers with package', 'wp-jobsearch'),
                'only_applicants' => __('4. Only Applicants', 'wp-jobsearch'),
            ),
            'default' => 'register',
        );
        $can_restcion_settings[] = array(
            'id' => 'restrict_cv_packages',
            'type' => 'select',
            'multi' => true,
            'title' => __('Cv Packages', 'wp-jobsearch'),
            'required' => array(
                //array('restrict_candidates', 'equals', 'on'),
                array('restrict_candidates_for_users', 'equals', 'register_resume'),
            ),
            'desc' => '',
            'options' => $cv_pckgs,
            'default' => '',
            'subtitle' => __('Select Cv packages for employers.', 'wp-jobsearch'),
        );
        $can_restcion_settings[] = array(
            'id' => 'emp_cv_pkgbase_restrictions_list',
            'type' => 'button_set',
            'required' => array('restrict_candidates_for_users', '!=', 'only_applicants'),
            'title' => __('Listing Restrictions', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Partially Enable/Disable Candidate Listing fields.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $can_restcion_settings[] = array(
            'id' => 'emp_cv_pkgbase_restrictions',
            'type' => 'button_set',
            'required' => array('restrict_candidates_for_users', '!=', 'only_applicants'),
            'title' => __('Profile Detail Restrictions', 'wp-jobsearch'),
            'desc' => '',
            'subtitle' => __('Partially Enable/Disable Candidate profile fields.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $can_restcion_settings[] = array(
            'id' => 'cv_pkgbase_profile_defields',
            'type' => 'select',
            'multi' => true,
            'required' => array('restrict_candidates_for_users', '!=', 'only_applicants'),
            'title' => __('Basic Profile Fields', 'wp-jobsearch'),
            'subtitle' => __('Select default profile fields of candidates which are not restrict for employers.', 'wp-jobsearch'),
            'options' => array(
                'display_name' => __('Display Name', 'wp-jobsearch'),
                'profile_img' => __('Profile Picture', 'wp-jobsearch'),
                'cover_img' => __('Cover Photo', 'wp-jobsearch'),
                'date_of_birth' => __('Date of Birth', 'wp-jobsearch'),
                'email' => __('Email', 'wp-jobsearch'),
                'phone' => __('Phone', 'wp-jobsearch'),
                'sector' => __('Sector', 'wp-jobsearch'),
                'job_title' => __('Job Title', 'wp-jobsearch'),
                'salary' => __('Salary', 'wp-jobsearch'),
                'about_desc' => __('Description', 'wp-jobsearch'),
            ),
            'default' => array('display_name', 'profile_img', 'cover_img', 'sector'),
            'desc' => '',
        );
        $can_restcion_settings[] = array(
            'id' => 'cv_pkgbase_socialicons_defields',
            'type' => 'button_set',
            'title' => __('Social Links', 'wp-jobsearch'),
            'required' => array('restrict_candidates_for_users', '!=', 'only_applicants'),
            'desc' => '',
            'subtitle' => __('Social links section view restriction in candidates profile and listing.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $can_restcion_settings[] = array(
            'id' => 'cv_pkgbase_customfields_defields',
            'type' => 'button_set',
            'title' => __('Custom Fields', 'wp-jobsearch'),
            'required' => array('restrict_candidates_for_users', '!=', 'only_applicants'),
            'desc' => '',
            'subtitle' => __('Custom Fields section view restriction in candidates profile and listing.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $can_restcion_settings[] = array(
            'id' => 'cv_pkgbase_address_defields',
            'type' => 'button_set',
            'title' => __('Location/Address', 'wp-jobsearch'),
            'required' => array('restrict_candidates_for_users', '!=', 'only_applicants'),
            'desc' => '',
            'subtitle' => __('Address Field view restriction in candidate&apos;s profile and listing.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $can_restcion_settings[] = array(
            'id' => 'cv_pkgbase_contactfrm_defields',
            'type' => 'button_set',
            'title' => __('Contact Form', 'wp-jobsearch'),
            'required' => array('restrict_candidates_for_users', '!=', 'only_applicants'),
            'desc' => '',
            'subtitle' => __('Contact Form section view restriction in candidates profile and listing.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $can_restcion_settings[] = array(
            'id' => 'cv_pkgbase_skills_defields',
            'type' => 'button_set',
            'title' => __('Skills', 'wp-jobsearch'),
            'required' => array('restrict_candidates_for_users', '!=', 'only_applicants'),
            'desc' => '',
            'subtitle' => __('Skills section view restriction in candidate&apos;s profile and listing.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $can_restcion_settings[] = array(
            'id' => 'cv_pkgbase_edu_defields',
            'type' => 'button_set',
            'title' => __('Education', 'wp-jobsearch'),
            'required' => array('restrict_candidates_for_users', '!=', 'only_applicants'),
            'desc' => '',
            'subtitle' => __('Education section view restriction in candidate&apos;s profile and listing.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $can_restcion_settings[] = array(
            'id' => 'cv_pkgbase_exp_defields',
            'type' => 'button_set',
            'title' => __('Experience', 'wp-jobsearch'),
            'required' => array('restrict_candidates_for_users', '!=', 'only_applicants'),
            'desc' => '',
            'subtitle' => __('Experience section view restriction in candidate&apos;s profile and listing.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $can_restcion_settings[] = array(
            'id' => 'cv_pkgbase_port_defields',
            'type' => 'button_set',
            'title' => __('Portfolio', 'wp-jobsearch'),
            'required' => array('restrict_candidates_for_users', '!=', 'only_applicants'),
            'desc' => '',
            'subtitle' => __('Portfolio section view restriction in candidate&apos;s profile and listing.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $can_restcion_settings[] = array(
            'id' => 'cv_pkgbase_expertise_defields',
            'type' => 'button_set',
            'title' => __('Expertise', 'wp-jobsearch'),
            'required' => array('restrict_candidates_for_users', '!=', 'only_applicants'),
            'desc' => '',
            'subtitle' => __('Expertise section view restriction in candidate&apos;s profile and listing.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $can_restcion_settings[] = array(
            'id' => 'cv_pkgbase_awards_defields',
            'type' => 'button_set',
            'title' => __('Honors & Awards', 'wp-jobsearch'),
            'required' => array('restrict_candidates_for_users', '!=', 'only_applicants'),
            'desc' => '',
            'subtitle' => __('Honors & Awards section view restriction in candidate&apos;s profile and listing.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );

        $section_settings = array(
            'title' => __('Candidate Restrictions', 'wp-jobsearch'),
            'id' => 'jobsearch-candrestricton-settins',
            'icon' => 'el el-adjust-alt',
            'desc' => '',
            'fields' => $can_restcion_settings
        );
        $setting_sections[] = $section_settings;
        //

        $resume_export_sec = apply_filters('jobsearch_resume_export_section', array());
        if (!empty($resume_export_sec)) {
            $setting_sections[] = $resume_export_sec;
        }

        return $setting_sections;
    }
}
