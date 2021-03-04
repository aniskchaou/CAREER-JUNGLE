<?php

/*
  Class : Import Locations
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class JobSearch_Import_Locations {

    // hook things up
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        $this->load_files();
    }

    public function admin_enqueue_scripts() {

        $jobsearch__options = get_option('jobsearch_plugin_options');
        $all_locations_type = isset($jobsearch__options['all_locations_type']) ? $jobsearch__options['all_locations_type'] : '';
        
        if ($all_locations_type != 'api') {
            wp_enqueue_style('jobsearch-import-locations', jobsearch_plugin_get_url('modules/import-locations/css/import-locations.css'));

            //
            wp_register_script('jobsearch-import-locs', jobsearch_plugin_get_url('modules/import-locations/js/import-locations.js'), array(), '', true);
            $jobsearch_plugin_arr = array(
                'plugin_url' => jobsearch_plugin_get_url(),
                'ajax_url' => admin_url('admin-ajax.php'),
                'error_msg' => esc_html__('There is some problem.', 'wp-jobsearch'),
            );

            wp_localize_script('jobsearch-import-locs', 'jobsearch_importlocs_vars', $jobsearch_plugin_arr);
        }
    }

    public function load_files() {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $all_locations_type = isset($jobsearch__options['all_locations_type']) ? $jobsearch__options['all_locations_type'] : '';
        
        if ($all_locations_type != 'api') {
            include plugin_dir_path(dirname(__FILE__)) . 'import-locations/include/import-locations-functions.php';
        }
    }

}

// class JobSearch_Import_Locations
$JobSearch_Import_Locations_obj = new JobSearch_Import_Locations();
