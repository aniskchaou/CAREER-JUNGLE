<?php

/*
  Class : Ziprecruiter Jobs Import
 */

// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class JobSearch_Ziprecruiter_Jobs {

    // hook things up
    public function __construct() {
        $this->load_files();
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

    }

    public function admin_enqueue_scripts() {

        $ziprecruiter_jobs_switch = get_option('jobsearch_integration_ziprecruiter_jobs');

        if ($ziprecruiter_jobs_switch == 'on') {
            wp_enqueue_style('jobsearch-ziprecruiter-jobs', jobsearch_plugin_get_url('modules/ziprecruiter-integration/css/ziprecruiter-jobs.css'));
        }

        //
        if ($ziprecruiter_jobs_switch == 'on') {
            wp_enqueue_script('jobsearch-ziprecruiter-jobs-scripts', jobsearch_plugin_get_url('modules/ziprecruiter-integration/js/ziprecruiter-jobs.js'), array(), '', true);
            $jobsearch_plugin_arr = array(
                'plugin_url' => jobsearch_plugin_get_url(),
                'ajax_url' => admin_url('admin-ajax.php'),
                'error_msg' => esc_html__('There is some problem.', 'wp-jobsearch'),
                'submit_txt' => esc_html__('Submit', 'wp-jobsearch'),
            );

            wp_localize_script('jobsearch-ziprecruiter-jobs-scripts', 'jobsearch_ziprecruiterjobs_vars', $jobsearch_plugin_arr);
        }
    }

    public function load_files() {
        include plugin_dir_path(dirname(__FILE__)) . 'ziprecruiter-integration/include/ziprecruiter-jobs-api.php';
        include plugin_dir_path(dirname(__FILE__)) . 'ziprecruiter-integration/include/ziprecruiter-jobs-hooks.php';
        include plugin_dir_path(dirname(__FILE__)) . 'ziprecruiter-integration/include/ziprecruiter-jobs-frontend-view.php';
    }

}

return new JobSearch_Ziprecruiter_Jobs();
