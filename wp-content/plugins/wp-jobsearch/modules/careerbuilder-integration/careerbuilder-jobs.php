<?php

/*
  Class : CareerBuilder Jobs Import
 */

// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class JobSearch_CareerBuilder_Jobs {

    // hook things up
    public function __construct() {
        $this->load_files();
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

    }

    public function admin_enqueue_scripts() {

        $careerbuilder_jobs_switch = get_option('jobsearch_integration_careerbuild_jobs');

        if ($careerbuilder_jobs_switch == 'on') {
            wp_enqueue_style('jobsearch-careerbuilder-jobs', jobsearch_plugin_get_url('modules/careerbuilder-integration/css/careerbuilder-jobs.css'));
        }

        //
        if ($careerbuilder_jobs_switch == 'on') {
            wp_enqueue_script('jobsearch-careerbuilder-jobs-scripts', jobsearch_plugin_get_url('modules/careerbuilder-integration/js/careerbuilder-jobs.js'), array(), '', true);
            $jobsearch_plugin_arr = array(
                'plugin_url' => jobsearch_plugin_get_url(),
                'ajax_url' => admin_url('admin-ajax.php'),
                'error_msg' => esc_html__('There is some problem.', 'wp-jobsearch'),
                'submit_txt' => esc_html__('Submit', 'wp-jobsearch'),
            );

            wp_localize_script('jobsearch-careerbuilder-jobs-scripts', 'jobsearch_careerbuilderjobs_vars', $jobsearch_plugin_arr);
        }
    }

    public function load_files() {
        include plugin_dir_path(dirname(__FILE__)) . 'careerbuilder-integration/include/careerbuilder-jobs-api.php';
        include plugin_dir_path(dirname(__FILE__)) . 'careerbuilder-integration/include/careerbuilder-jobs-hooks.php';
        include plugin_dir_path(dirname(__FILE__)) . 'careerbuilder-integration/include/careerbuilder-jobs-frontend-view.php';
    }

}

return new JobSearch_CareerBuilder_Jobs();
