<?php

/*
  Class : CareerJet Jobs Import
 */

// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class JobSearch_CareerJet_Jobs {

    // hook things up
    public function __construct() {
        $this->load_files();
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

    }

    public function admin_enqueue_scripts() {

        $careerjet_jobs_switch = get_option('jobsearch_integration_careerjet_jobs');

        if ($careerjet_jobs_switch == 'on') {
            wp_enqueue_style('jobsearch-careerjet-jobs', jobsearch_plugin_get_url('modules/careerjet-integration/css/careerjet-jobs.css'));
        }

        //
        if ($careerjet_jobs_switch == 'on') {
            wp_enqueue_script('jobsearch-careerjet-jobs-scripts', jobsearch_plugin_get_url('modules/careerjet-integration/js/careerjet-jobs.js'), array(), '', true);
            $jobsearch_plugin_arr = array(
                'plugin_url' => jobsearch_plugin_get_url(),
                'ajax_url' => admin_url('admin-ajax.php'),
                'error_msg' => esc_html__('There is some problem.', 'wp-jobsearch'),
                'submit_txt' => esc_html__('Submit', 'wp-jobsearch'),
            );

            wp_localize_script('jobsearch-careerjet-jobs-scripts', 'jobsearch_careerjetjobs_vars', $jobsearch_plugin_arr);
        }
    }

    public function load_files() {
        include plugin_dir_path(dirname(__FILE__)) . 'careerjet-integration/include/careerjet-jobs-api.php';
        include plugin_dir_path(dirname(__FILE__)) . 'careerjet-integration/include/careerjet-jobs-hooks.php';
        include plugin_dir_path(dirname(__FILE__)) . 'careerjet-integration/include/careerjet-jobs-frontend-view.php';
    }

}

return new JobSearch_CareerJet_Jobs();
