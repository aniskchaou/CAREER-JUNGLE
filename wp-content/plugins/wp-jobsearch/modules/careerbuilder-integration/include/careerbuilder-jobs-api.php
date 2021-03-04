<?php

// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * JobSearch_CareerBuilder_API
 */
class JobSearch_CareerBuilder_API {

    private static $endpoint = "https://api.careerbuilder.com/v3/job";

    /**
     * Get jobs from the careerbuilder API
     * @return array()
     */

    /**
     * Get default args
     */
    private static function get_default_args() {
        $careerbuilder_api_key = get_option('jobsearch_integration_careerbuild_api');
        
        return array(
            'DID' => $careerbuilder_api_key,
            'keyword' => '',
            'location' => '',
            'jobs_per_page' => 10,
            'page' => 1,
            'outputjson' => true,
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
     * Get jobs from the API
     * @return array()
     */
    public static function get_jobs($args) {
        $args = self::format_args(wp_parse_args($args, self::get_default_args()));
        
        $transient_name = 'careerbuilder_' . md5(json_encode($args));
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
                }
            }
        }

        return $results;
    }

}
