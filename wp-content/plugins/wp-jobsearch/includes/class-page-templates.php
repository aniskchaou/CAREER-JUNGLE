<?php

/**
 * Page templates class
 * 
 * @return object
 */
class JobSearch_Page_Templates {

    public function __construct() {

        $this->templates = array();

        add_filter('theme_page_templates', array($this, 'custom_page_templates_callback'), 1, 1);
        add_filter('template_include', array($this, 'user_dashboard_page_templates'));
        add_action('init', array($this, 'all_templates_list_callback'), 3, 0);
        add_action('init', array($this, 'auto_generate_user_dashboard_page'), 3, 0);
    }

    public function all_templates_list_callback() {
        $all_templates = array(
            'jobsearch-full-template.php' => __('JobSearch Full Page', 'wp-jobsearch'),
            'user-dashboard-template.php' => __('User Dashboard', 'wp-jobsearch'),
            'default-job-listing-template.php' => __('Default Jobs Listing', 'wp-jobsearch'),
            'default-employer-listing-template.php' => __('Default Employers Listing', 'wp-jobsearch'),
            'default-candidate-listing-template.php' => __('Default Candidates Listing', 'wp-jobsearch'),
        );
        $this->templates = apply_filters('jobsearch_templates_list_set', $all_templates);
    }

    public function custom_page_templates_callback($post_templates) {
        $post_templates = array_merge($this->templates, $post_templates);
        return $post_templates;
    }

    public function user_dashboard_page_templates($template) {
        global $post;
        if (!isset($post)) {
            return $template;
        }
        if (!isset($this->templates[get_post_meta($post->ID, '_wp_page_template', true)])) {
            return $template;
        }
        if ('jobsearch-full-template.php' === get_post_meta($post->ID, '_wp_page_template', true)) {

            $file = jobsearch_plugin_get_path('templates/' . get_post_meta($post->ID, '_wp_page_template', true));
            if (file_exists($file)) {
                return $file;
            }
        }
        if ('user-dashboard-template.php' === get_post_meta($post->ID, '_wp_page_template', true)) {

            $file = jobsearch_plugin_get_path('templates/user-dashboard/' . get_post_meta($post->ID, '_wp_page_template', true));
            if (file_exists($file)) {
                return $file;
            }
        }
        if ('default-job-listing-template.php' === get_post_meta($post->ID, '_wp_page_template', true)) {

            $file = jobsearch_plugin_get_path('templates/jobs/' . get_post_meta($post->ID, '_wp_page_template', true));
            if (file_exists($file)) {
                return $file;
            }
        }
        if ('default-employer-listing-template.php' === get_post_meta($post->ID, '_wp_page_template', true)) {

            $file = jobsearch_plugin_get_path('templates/employers/' . get_post_meta($post->ID, '_wp_page_template', true));
            if (file_exists($file)) {
                return $file;
            }
        }
        if ('default-candidate-listing-template.php' === get_post_meta($post->ID, '_wp_page_template', true)) {

            $file = jobsearch_plugin_get_path('templates/candidates/' . get_post_meta($post->ID, '_wp_page_template', true));
            if (file_exists($file)) {
                return $file;
            }
        }
        return apply_filters('jobsearch_template_page_file', $template);
    }

    public function auto_generate_user_dashboard_page() {
        global $jobsearch_plugin_options, $JobsearchReduxFramework;

        $user_dashboard_page_id = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $page_path = 'user-dashboard';
        $user_dash_page = get_page_by_path($page_path, OBJECT, 'page');

        if ($user_dashboard_page_id == '' && empty($user_dash_page)) {
            $page_args = array(
                'post_title' => wp_strip_all_tags('User Dashboard'),
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_content' => '',
            );
            // Insert the post into the database
            $page_id = wp_insert_post($page_args);

            update_post_meta($page_id, '_wp_page_template', 'user-dashboard-template.php');

            if ($JobsearchReduxFramework !== NULL) {
                //$JobsearchReduxFramework->ReduxFramework->set('user-dashboard-template-page', $page_path);
            }
        } else if ($user_dashboard_page_id == '' && is_object($user_dash_page)) {
            $page_id = $user_dash_page->ID;

            update_post_meta($page_id, '_wp_page_template', 'user-dashboard-template.php');

            if ($JobsearchReduxFramework !== NULL) {
                //$JobsearchReduxFramework->ReduxFramework->set('user-dashboard-template-page', $page_path);
            }
        }
    }

}

$JobSearch_page_templates = new JobSearch_Page_Templates();
