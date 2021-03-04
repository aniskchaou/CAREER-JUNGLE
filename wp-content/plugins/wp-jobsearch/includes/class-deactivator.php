<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 */
class JobSearch_plugin_Deactivator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        update_option('wp_jobsearch_plugin_active', 'no');
        do_action('jobsearch_plugin_deactivator_hook');
        
        //
        wp_clear_scheduled_hook('jobsearch_expire_pkgs_alert_cron');
        
        //
        wp_clear_scheduled_hook('jobsearch_job_alerts_schedule');
    }

}
