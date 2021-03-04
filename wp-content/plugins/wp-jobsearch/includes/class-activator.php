<?php

/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 *
 * @package    JobSearch_plugin
 * @subpackage JobSearch_plugin/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 */
class JobSearch_plugin_Activator {
    
     public function __construct() {
         
     }
    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        update_option('wp_jobsearch_plugin_active', 'yes');
        
        $plugin_activ_val = get_option('wp_jobsearch_plugin_active');
        if ($plugin_activ_val == '') {
            update_option('users_can_register', '1');
        }
        
        do_action('jobsearch_plugin_activator_hook');
        
        //
        $cron_timestamp = wp_next_scheduled('jobsearch_expire_pkgs_alert_cron');
        if (!$cron_timestamp) {
            wp_schedule_event(time(), 'twicedaily', 'jobsearch_expire_pkgs_alert_cron');
        }
        
        //
        $cron_timestamp = wp_next_scheduled('jobsearch_job_feature_expire_cron');
        if (!$cron_timestamp) {
            wp_schedule_event(time(), 'every_half_hourly', 'jobsearch_job_feature_expire_cron');
        }
        
        //
        //$import_schedules_timestamp = wp_next_scheduled('jobsearch_job_import_schedules_cron');
        //if (!$import_schedules_timestamp) {
            wp_schedule_event(time(), 'every_five_mins', 'jobsearch_job_import_schedules_cron');
        //}
        wp_schedule_event(time(), 'every_half_hourly', 'jobsearch_half_hour_common_schedule');
        
        //
        $jobalerts_cron_event = wp_next_scheduled('jobsearch_job_alerts_schedule');
        if (!$jobalerts_cron_event) {
            wp_schedule_event(time(), 'daily', 'jobsearch_job_alerts_schedule');
        }
    }
}
