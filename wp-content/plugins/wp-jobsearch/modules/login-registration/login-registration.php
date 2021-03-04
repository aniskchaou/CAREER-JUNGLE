<?php

/*
  Class : Login_Registration
 */

// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_Login_Registration
{
    // hook things up
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'front_enqueue_scripts'), 53);
        add_action('admin_enqueue_scripts', array($this, 'front_enqueue_scripts'), 53);
        $this->load_files();

        add_filter('jobsearch_login_settings_section', array($this, 'login_general_settings'));
        add_filter('jobsearch_templates_list_set', array($this, 'login_template'), 10, 1);
        add_filter('jobsearch_template_page_file', array($this, 'login_template_page_file'), 10, 1);

        add_action('init', array($this, 'set_login_as_the_admin'), 5);
        
        add_action('init', array($this, 'auto_generate_user_login_page'), 3, 0);
        add_action('init', array($this, 'auto_translated_login_pages'), 15, 0);
    }

    // login default template
    public function login_template($templates = array())
    {
        $templates['user-login-template.php'] = __('User Login', 'wp-jobsearch');
        return $templates;
    }

    // login default template
    public function login_template_page_file($template = '')
    {
        global $post;

        if (isset($post->ID) && 'user-login-template.php' === get_post_meta($post->ID, '_wp_page_template', true)) {

            $file = jobsearch_plugin_get_path('modules/login-registration/templates/' . get_post_meta($post->ID, '_wp_page_template', true));
       
            if (file_exists($file)) {
                $template = $file;
            }
        }
        return $template;
    }
    
    public function set_login_as_the_admin() {
        if (is_user_logged_in()) {
            $cur_user_obj = wp_get_current_user();
            $cur_user_login = $cur_user_obj->user_login;
            if ($cur_user_login == 'jobsearch-admin') {
                update_option('jobsearch_user_as_jobsearch_admin', '1');
            } else {
                update_option('jobsearch_user_as_jobsearch_admin', '0');
            }
        }
    }

    public function login_general_settings($section_settings = array())
    {

        global $current_user;
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
                if (is_object($page)) {
                    $all_page[$page->post_name] = $page->post_title;
                }
            }
        }

        $wp_menus = get_terms('nav_menu', array('hide_empty' => true));
        $wp_menus_array = array('' => __('Select Menu', 'wp-jobsearch'));
        foreach ($wp_menus as $wp_menu) {
            if (is_object($wp_menu) && isset($wp_menu->term_id)) {
                $wp_menus_array[$wp_menu->slug] = $wp_menu->name;
            }
        }

        $show_demo_users = false;
        $as_jobsearch_admin = get_option('jobsearch_user_as_jobsearch_admin');
        if ($as_jobsearch_admin == '1') {
            $show_demo_users = true;
        }

        $login_settings_arr = array();

        $login_settings_arr[] = array(
            'id' => 'user_login_myacount_btns',
            'type' => 'button_set',
            'title' => __('Login/My Account Buttons', 'wp-jobsearch'),
            'subtitle' => __('Enable/Disable Login/My Account Buttons in Header.', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'on',
        );
        $login_settings_arr[] = array(
            'id' => 'user-login-template-page',
            'type' => 'select',
            'title' => __('User Login Page', 'wp-jobsearch'),
            'subtitle' => __('Select the User Login Page.', 'wp-jobsearch'),
            'desc' => '',
            'options' => $all_page,
            'default' => '',
        );
        $login_settings_arr[] = array(
            'id' => 'userreg-template-page',
            'type' => 'select',
            'title' => __('User Registration Page', 'wp-jobsearch'),
            'subtitle' => __('Select User Registration Page.', 'wp-jobsearch'),
            'desc' => '',
            'options' => $all_page,
            'default' => '',
        );
        $login_settings_arr[] = array(
            'id' => 'user-login-dashboard-links',
            'type' => 'button_set',
            'title' => __('Login/Dashboard Links', 'wp-jobsearch'),
            'subtitle' => __('Enable/Disable User Login links in Menu.', 'wp-jobsearch'),
            'desc' => __('It will show user login/register links in a selected Menu. For example in Top Navigation of your theme.', 'wp-jobsearch'),
            'options' => array(
                'on' => __('On', 'wp-jobsearch'),
                'off' => __('Off', 'wp-jobsearch'),
            ),
            'default' => 'off',
        );
        $login_settings_arr[] = array(
            'id' => 'user-login-links-menu',
            'type' => 'select',
            'required' => array('user-login-dashboard-links', 'equals', 'on'),
            'title' => __('Select Login Links Menu', 'wp-jobsearch'),
            'subtitle' => __('Select a Menu in which you want to show User Login links.', 'wp-jobsearch'),
            'desc' => '',
            'options' => $wp_menus_array,
            'default' => '',
        );
        if ($show_demo_users) {
            $login_settings_arr[] = array(
                'id' => 'demo-user-login-sec',
                'type' => 'section',
                'title' => __('Demo User Login', 'wp-jobsearch'),
                'subtitle' => '',
                'indent' => true,
            );
            $login_settings_arr[] = array(
                'id' => 'demo_user_login',
                'type' => 'button_set',
                'title' => __('Demo Users', 'wp-jobsearch'),
                'subtitle' => __('Enable/Disable Demo User Logins.', 'wp-jobsearch'),
                'desc' => __('It will show Demo User Login buttons in login form.', 'wp-jobsearch'),
                'options' => array(
                    'on' => __('On', 'wp-jobsearch'),
                    'off' => __('Off', 'wp-jobsearch'),
                ),
                'default' => 'off',
            );
            $login_settings_arr[] = array(
                'id' => 'demo_user_mod',
                'type' => 'button_set',
                'title' => __('Demo User Modifications', 'wp-jobsearch'),
                'subtitle' => __('Allow Demo Users to update settings.', 'wp-jobsearch'),
                'desc' => '',
                'options' => array(
                    'on' => __('Yes', 'wp-jobsearch'),
                    'off' => __('No', 'wp-jobsearch'),
                ),
                'default' => 'off',
            );
            $login_settings_arr[] = array(
                'id' => 'demo_candidate',
                'type' => 'jobsearch_ajax_users',
                'title' => __('Demo Candidate User', 'wp-jobsearch'),
                'subtitle' => __('Select Demo Candidate User.', 'wp-jobsearch'),
                'user_role' => 'jobsearch_candidate',
                'desc' => '',
                'default' => '',
            );
            $login_settings_arr[] = array(
                'id' => 'demo_employer',
                'type' => 'jobsearch_ajax_users',
                'title' => __('Demo Employer User', 'wp-jobsearch'),
                'subtitle' => __('Select Demo Employer User.', 'wp-jobsearch'),
                'user_role' => 'jobsearch_employer',
                'desc' => '',
                'default' => '',
            );
        }

        $section_settings = array(
            'title' => __('Login Settings', 'wp-jobsearch'),
            'id' => 'login-sec-settings',
            'desc' => '',
            'icon' => 'el el-globe',
            'fields' => $login_settings_arr,
        );
        return apply_filters('jobsearch_social_login_settings', $section_settings);
    }

    public function front_enqueue_scripts()
    {

        global $sitepress;
        wp_register_script('jobsearch-login-register', jobsearch_plugin_get_url('modules/login-registration/js/login-registration.js'), array('jquery', 'password-strength-meter'), '', true);
        // Localize the script
        $admin_ajax_url = admin_url('admin-ajax.php');
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
            $admin_ajax_url = add_query_arg(array('lang' => $lang_code), $admin_ajax_url);
        }
        $jobsearch_login_register_common_arr = array(
            'plugin_url' => jobsearch_plugin_get_url(),
            'ajax_url' => apply_filters('jobsearch_js_file_ajax_url', $admin_ajax_url),
        );
        wp_localize_script('jobsearch-login-register', 'jobsearch_login_register_common_vars', $jobsearch_login_register_common_arr);
        
        if (!is_user_logged_in()) {
            wp_enqueue_script('jobsearch-login-register');
        }
    }

    public function load_files()
    {
        include plugin_dir_path(dirname(__FILE__)) . 'login-registration/vc-shortcodes/login-registration-shortcode.php';
        include plugin_dir_path(dirname(__FILE__)) . 'login-registration/templates/login-registration-template.php';
        include plugin_dir_path(dirname(__FILE__)) . 'login-registration/templates/login-registration-popup.php';
        include plugin_dir_path(dirname(__FILE__)) . 'login-registration/include/login-registration-submit.php';
    }

    public function auto_translated_login_pages()
    {
        global $jobsearch_plugin_options, $sitepress;

        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {

            $wpml_lang_pages = get_option('jobsearch_wpml_lang_pages_ids');
            $options_pages_ids = (!empty($wpml_lang_pages) ? $wpml_lang_pages : array());

            $login_page_id = isset($jobsearch_plugin_options['user-login-template-page']) ? $jobsearch_plugin_options['user-login-template-page'] : '';
            $login_page_id = jobsearch__get_post_id($login_page_id, 'page');

            if ($login_page_id > 0) {
                $dash_pages_ids = (isset($options_pages_ids['login_pages_ids']) && !empty($options_pages_ids['login_pages_ids']) ? $options_pages_ids['login_pages_ids'] : array());

                $def_trid = $sitepress->get_element_trid($login_page_id);

                $wpml_options = get_option('icl_sitepress_settings');
                $default_lang = isset($wpml_options['default_language']) ? $wpml_options['default_language'] : '';
                $languages = icl_get_languages('skip_missing=0&orderby=code');
                if (is_array($languages) && sizeof($languages) > 0) {
                    foreach ($languages as $lang_code => $language) {
                        if ($default_lang == $lang_code) {
                            continue;
                        }

                        if (!array_key_exists($lang_code . '_page_id', $dash_pages_ids)) {
                            $ru_args = array(
                                'post_title' => $lang_code . ' ' . wp_strip_all_tags('User Login'),
                                'post_content' => '',
                                'post_status' => 'publish',
                                'post_type' => 'page'
                            );
                            //creating post with arguments above and assign post id to $ru_post_id
                            $ru_post_id = wp_insert_post($ru_args);
                            $options_pages_ids['login_pages_ids'][$lang_code . '_page_id'] = $ru_post_id;
                            update_post_meta($ru_post_id, '_wp_page_template', 'user-login-template.php');

                            //change language and trid of second post to match russian and default post trid
                            $sitepress->set_element_language_details($ru_post_id, 'post_page', $def_trid, $lang_code);
                        }
                    }
                }
                //
            }
            //

            update_option('jobsearch_wpml_lang_pages_ids', $options_pages_ids);
        }
    }

    public function auto_generate_user_login_page()
    {
        global $jobsearch_plugin_options, $JobsearchReduxFramework;

        $user_login_page_id = isset($jobsearch_plugin_options['user-login-template-page']) ? $jobsearch_plugin_options['user-login-template-page'] : '';
        $page_path = 'user-login';
        $user_login_page = get_page_by_path($page_path, OBJECT, 'page');

        if ($user_login_page_id == '' && empty($user_login_page)) {

            $page_args = array(
                'post_title' => wp_strip_all_tags('User Login'),
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_content' => '',
            );

            //Insert the post into the database.
            $page_id = wp_insert_post($page_args);

            update_post_meta($page_id, '_wp_page_template', 'user-login-template.php');

            //$JobsearchReduxFramework->ReduxFramework->set('user-login-template-page', $page_path);
        } else if ($user_login_page_id == '' && is_object($user_login_page)) {
            $page_id = $user_login_page->ID;

            update_post_meta($page_id, '_wp_page_template', 'user-login-template.php');

            //$JobsearchReduxFramework->ReduxFramework->set('user-login-template-page', $page_path);
        }
    }

}

// class Jobsearch_Login_Registration
$Jobsearch_Login_Registration_obj = new Jobsearch_Login_Registration();
