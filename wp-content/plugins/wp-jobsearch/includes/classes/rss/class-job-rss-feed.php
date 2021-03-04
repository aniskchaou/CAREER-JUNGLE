<?php

// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_Job_RSS_Feeds {

    // hook things up
    public function __construct() {
        add_action('init', array($this, 'custom_rss'));
    }

    function custom_rss() {
        add_feed('job_feed', array($this, 'custom_feed_template'));
    }

    function custom_feed_template() {
        global $jobsearch_shortcode_jobs_frontend;
        $jobsearch__options = get_option('jobsearch_plugin_options');
        
        $get_all_args = $jobsearch_shortcode_jobs_frontend->jobs_list_args();
        
        $args = $get_all_args['args'];
        
        if (isset($args['posts_per_page']) && $args['posts_per_page'] == '-1') {
            $args['posts_per_page'] = 10;
        }

        set_query_var('q_args', $args);
        jobsearch_get_template_part('feed', 'jobs', 'rss');
    }

}

return new Jobsearch_Job_RSS_Feeds();

