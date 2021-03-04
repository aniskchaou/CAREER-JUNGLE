<?php

/*
  Class : Indeed Jobs Import
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class JobSearch_Indeed_Jobs {

    // hook things up
    public function __construct() {
        $this->load_files();
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

    }

    public function admin_enqueue_scripts() {

        $indeed_jobs_switch = get_option('jobsearch_integration_indeed_jobs');

        if ($indeed_jobs_switch == 'on') {
            wp_enqueue_style('jobsearch-indeed-jobs', jobsearch_plugin_get_url('modules/indeed-jobs-import/css/indeed-jobs.css'));
        }

        //
        if ($indeed_jobs_switch == 'on') {
            wp_enqueue_script('jobsearch-indeed-jobs-scripts', jobsearch_plugin_get_url('modules/indeed-jobs-import/js/indeed-jobs.js'), array(), '', true);
            $jobsearch_plugin_arr = array(
                'plugin_url' => jobsearch_plugin_get_url(),
                'ajax_url' => admin_url('admin-ajax.php'),
                'error_msg' => esc_html__('There is some problem.', 'wp-jobsearch'),
                'submit_txt' => esc_html__('Submit', 'wp-jobsearch'),
            );

            wp_localize_script('jobsearch-indeed-jobs-scripts', 'jobsearch_indeedjobs_vars', $jobsearch_plugin_arr);
        }
    }

    public function load_files() {
        include plugin_dir_path(dirname(__FILE__)) . 'indeed-jobs-import/include/indeed-jobs-api.php';
        include plugin_dir_path(dirname(__FILE__)) . 'indeed-jobs-import/include/indeed-jobs-hooks.php';
        include plugin_dir_path(dirname(__FILE__)) . 'indeed-jobs-import/include/indeed-jobs-frontend-view.php';
    }

}

// class JobSearch_Indeed_Jobs
$JobSearch_Indeed_Jobs_obj = new JobSearch_Indeed_Jobs();
