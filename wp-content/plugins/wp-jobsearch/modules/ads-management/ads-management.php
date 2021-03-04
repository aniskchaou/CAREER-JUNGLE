<?php

/*
  Class : JobSearch_Ads_management
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class JobSearch_Ads_management {

    // hook things up
    public function __construct() {
        $this->load_files();
        add_action('wp_enqueue_scripts', array($this, 'front_enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'backend_enqueue_scripts'));
        //
        add_action('jobsearch_dashbord_instyles_list_aftr', array($this, 'enqueue_ad_styles'));
    }

    public function backend_enqueue_scripts() {

        global $jobsearch_plugin_options;
        $ads_management_switch = isset($jobsearch_plugin_options['ads_management_switch']) ? $jobsearch_plugin_options['ads_management_switch'] : '';
    }

    public function enqueue_ad_styles() {

        global $jobsearch_plugin_options;
        $ads_management_switch = isset($jobsearch_plugin_options['ads_management_switch']) ? $jobsearch_plugin_options['ads_management_switch'] : '';
        if ($ads_management_switch == 'on') {
            //wp_enqueue_style('jobsearch-ads-management-styles', jobsearch_plugin_get_url('modules/ads-management/css/ads-management.css'), array(), JobSearch_plugin::get_version());
        }
    }

    public function front_enqueue_scripts() {

        global $jobsearch_plugin_options;
        $ads_management_switch = isset($jobsearch_plugin_options['ads_management_switch']) ? $jobsearch_plugin_options['ads_management_switch'] : '';

        $is_page = is_page();
        $page_content = '';
        if ($is_page) {
            $page_id = get_the_ID();
            $page_post = get_post($page_id);
            $page_content = isset($page_post->post_content) ? $page_post->post_content : '';
        }
        $is_jobs_elemnt_page = $is_cands_elemnt_page = $is_emps_elemnt_page = false;
        if (strpos($page_content, 'job_short_counter')) {
            $is_jobs_elemnt_page = true;
        }
        if (strpos($page_content, 'candidate_short_counter')) {
            $is_cands_elemnt_page = true;
        }
        if (strpos($page_content, 'employer_short_counter')) {
            $is_emps_elemnt_page = true;
        }
        
        if ($ads_management_switch == 'on') {
            if ($is_page && 
                    (has_shortcode($page_content, 'jobsearch_job_shortcode')
                    || has_shortcode($page_content, 'jobsearch_candidate_shortcode') 
                    || has_shortcode($page_content, 'jobsearch_employer_shortcode') 
                    || has_shortcode($page_content, 'jobsearch_ad'))
                    || $is_jobs_elemnt_page
                    || $is_cands_elemnt_page
                    || $is_emps_elemnt_page
                ) {
                //wp_enqueue_style('jobsearch-ads-management-styles', jobsearch_plugin_get_url('modules/ads-management/css/ads-management.css'), array(), JobSearch_plugin::get_version());
            }
            if (is_singular(array('job', 'employer', 'candidate'))) {
                wp_enqueue_style('jobsearch-ads-management-styles', jobsearch_plugin_get_url('modules/ads-management/css/ads-management.css'), array(), JobSearch_plugin::get_version());
            }
            wp_register_script('jobsearch-ads-management-scripts', jobsearch_plugin_get_url('modules/ads-management/js/ads-management.js'), array(), JobSearch_plugin::get_version(), true);
            $jobsearch_plugin_arr = array(
                'plugin_url' => jobsearch_plugin_get_url(),
                'ajax_url' => admin_url('admin-ajax.php'),
                'error_msg' => esc_html__('There is some problem.', 'wp-jobsearch'),
                'submit_txt' => esc_html__('Submit', 'wp-jobsearch'),
            );

            wp_localize_script('jobsearch-ads-management-scripts', 'jobsearch_ads_manage_vars', $jobsearch_plugin_arr);
        }
    }

    public function load_files() {
        include plugin_dir_path(dirname(__FILE__)) . 'ads-management/include/redux-ext/loader.php';
        include plugin_dir_path(dirname(__FILE__)) . 'ads-management/include/ads-management-hooks.php';
    }

}

// Class JobSearch_Ads_management
$JobSearch_Ads_management_obj = new JobSearch_Ads_management();
