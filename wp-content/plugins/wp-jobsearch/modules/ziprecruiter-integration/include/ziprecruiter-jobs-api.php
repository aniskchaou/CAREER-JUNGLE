<?php

// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * JobSearch_Ziprecruiter_API
 */
class JobSearch_Ziprecruiter_API {

    private static $endpoint = "https://api.ziprecruiter.com/jobs/v1/";

    /**
     * Get jobs from the ziprecruiter API
     * @return array()
     */

    /**
     * Get default args
     */
    private static function get_default_args() {
        $ziprecruiter_api_key = get_option('jobsearch_integration_ziprecruiter_api');
        
        return array(
            'api_key' => $ziprecruiter_api_key,
            'search' => '',
            'location' => '',
            'jobs_per_page' => 10,
            'page' => 1,
            'radius_miles' => 20,
            'days_ago' => ''
        );
    }

    /**
     * Format args before sending them to the api
     * @param  array $args
     * @return array
     */
    private static function format_args($args) {
        foreach ($args as $key => $value) {
            if (method_exists(__CLASS__, 'format_arg_' . strtolower($key))) {
                $args[$key] = call_user_func(__CLASS__ . "::format_arg_" . strtolower($key), $value);
            }
        }
        return $args;
    }

    /**
     * Return job in standard format
     * @param  array $raw_job
     * @return object
     */
    private static function format_job($raw_job) {
        $job = array(
            'title' => sanitize_text_field($raw_job->name),
            'company' => sanitize_text_field($raw_job->hiring_company->name),
            'tagline' => sanitize_text_field($raw_job->snippet),
            'url' => esc_url_raw($raw_job->url),
            'location' => sanitize_text_field($raw_job->location),
            'latitude' => '',
            'longitude' => '',
            'type' => '',
            'type_slug' => '',
            'timestamp' => strtotime($raw_job->posted_time),
            'link_attributes' => array(),
            'logo' => ''
        );
        return $job;
    }

    /**
     * Get jobs from the API
     * @return array()
     */
    public static function get_jobs($args) {
        $args = self::format_args(wp_parse_args($args, self::get_default_args()));
        
        $transient_name = 'ziprecruiter_' . md5(json_encode($args));
        $total_pages = 0;
        $total_jobs = 0;
        $jobs = array();

        if (false === ( $results = get_transient($transient_name) )) {
            $results = array();
            $result = wp_remote_get(self::$endpoint . '?' . http_build_query($args, '', '&'), array('timeout' => 10));

            if (!is_wp_error($result) && !empty($result['body'])) {
                $results = json_decode($result['body']);

                if ($results && !empty($results->success)) {
                    set_transient($transient_name, $results, 1800);
                } else {
                    return self::response(0, 0, array());
                }
            } else {
                return self::response(0, 0, array());
            }
        }

        $total_jobs = absint($results->total_jobs);
        $total_pages = ceil($total_jobs / $args['jobs_per_page']);

        foreach ($results->jobs as $result) {
            $jobs[] = self::format_job($result);
        }

        return self::response($total_pages, $total_jobs, $jobs);
    }

    /**
     * Return a response containing jobs
     * @param  integer $total_pages
     * @param  integer $total_jobs
     * @param  array   $jobs
     * @return array
     */
    public static function response($total_pages = 0, $total_jobs = 0, $jobs = array()) {
        return array(
            'total_pages' => $total_pages,
            'total_jobs' => $total_jobs,
            'jobs' => $jobs
        );
    }

}
