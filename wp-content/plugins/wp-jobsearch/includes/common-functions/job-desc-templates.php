<?php
if (!defined('ABSPATH')) {
    die;
}

if (!class_exists('jobsearch_job_desc_templates')) {

    class jobsearch_job_desc_templates {

        // hook things up
        public function __construct() {
            add_action('init', array($this, 'desc_templates_posttype'));
        }

        public function desc_templates_posttype() {
            
            $jobsearch__options = get_option('jobsearch_plugin_options');
            $caned_resps_switch = isset($jobsearch__options['caned_resp_switch']) ? $jobsearch__options['caned_resp_switch'] : '';
            
            if ($caned_resps_switch != 'on') {
                return false;
            }
            
            $labels = array(
                'name' => _x('Description Templates', 'post type general name', 'wp-jobsearch'),
                'singular_name' => _x('Job Template', 'post type singular name', 'wp-jobsearch'),
                'menu_name' => _x('Description Templates', 'admin menu', 'wp-jobsearch'),
                'name_admin_bar' => _x('Description Template', 'add new on admin bar', 'wp-jobsearch'),
                'add_new' => _x('Add New', 'Job Template', 'wp-jobsearch'),
                'add_new_item' => __('Add New Job Template', 'wp-jobsearch'),
                'new_item' => __('New Job Template', 'wp-jobsearch'),
                'edit_item' => __('Edit Job Template', 'wp-jobsearch'),
                'view_item' => __('View Job Template', 'wp-jobsearch'),
                'all_items' => __('Description Templates', 'wp-jobsearch'),
                'search_items' => __('Search Job Templates', 'wp-jobsearch'),
                'parent_item_colon' => __('Parent Job Templates:', 'wp-jobsearch'),
                'not_found' => __('No Job Templates found.', 'wp-jobsearch'),
                'not_found_in_trash' => __('No Job Templates found in Trash.', 'wp-jobsearch')
            );

            $args = array(
                'labels' => $labels,
                'description' => __('Description.', 'wp-jobsearch'),
                'public' => false,
                'publicly_queryable' => false,
                'show_ui' => true,
                'show_in_menu' => 'edit.php?post_type=job',
                'query_var' => false,
                'capability_type' => 'post',
                'has_archive' => false,
                'exclude_from_search' => true,
                'hierarchical' => false,
                'supports' => array('title', 'editor')
            );

            register_post_type('jobdesctemp', $args);
        }
    }

    return new jobsearch_job_desc_templates();
}
