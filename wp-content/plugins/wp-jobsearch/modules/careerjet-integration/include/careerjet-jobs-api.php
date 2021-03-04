<?php

// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * JobSearch_CareerJet_API
 */
class JobSearch_CareerJet_API {
    
    /**
     * Get jobs from the careerjet API
     * @return array()
     */

    /**
     * Get default args
     */
    private static function get_default_args() {
        $careerjet_api_key = get_option('jobsearch_integration_careerjet_affid');
        
        return array(
            'affid' => $careerjet_api_key,
            'keywords' => '',
            'location' => '',
            'page' => 1,
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
        require_once 'Careerjet_API.php';
        $args = self::format_args(wp_parse_args($args, self::get_default_args()));
        
        $api = new Careerjet_API('en_GB') ;
        
        $transient_name = 'careerjet_' . md5(json_encode($args));
        
        if (false === ( $results = get_transient($transient_name) )) {
            $results = array();
            $result = $api->search($args);
            
            if ($result->type == 'JOBS') {
                $num_jobs = $result->hits;
                $total_pages = $result->pages;
                $results = $result->jobs;

                if (!empty($results)) {
                    set_transient($transient_name, $results, 1800);
                }
            }
        }

        return $results;
    }

}
