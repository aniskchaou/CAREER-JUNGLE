<?php
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * JobSearch_Indeed_API
 */
class JobSearch_Indeed_API {

    /**
     * Get jobs from the indeed API
     * @return array()
    */
    public static function get_jobs_from_indeed($args) {
        
        // default indeed api arguments
        $default_args = array(
            'v' => 2,
            'format' => 'json',
            'radius' => 25,
            'start' => 0,
            'latlong' => 1
        );
        // getting user ip
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $default_args['userip'] = $ip;
        // getting user agent
        $default_args['useragent'] = $_SERVER['HTTP_USER_AGENT'];
        
        $endpoint = "http://api.indeed.com/ads/apisearch?";
        
        $args = wp_parse_args( $args, $default_args );
        $results = array();
		
        $result = wp_remote_get( $endpoint . http_build_query($args, '', '&') );
        if (!is_wp_error($result) && !empty($result['body'])) {
            $results = (array) json_decode($result['body']);
        }
        return isset($results['results']) ? $results['results'] : $results;
    }
    
    /**
     * indeed api countries list
    */
    public static function indeed_api_countries() {
        $country = array();
        $country['us'] = esc_html__('United States', 'wp-jobsearch');
        $country['ar'] = esc_html__('Argentina', 'wp-jobsearch');
        $country['au'] = esc_html__('Australia', 'wp-jobsearch');
        $country['at'] = esc_html__('Austria', 'wp-jobsearch');
        $country['bh'] = esc_html__('Bahrain', 'wp-jobsearch');
        $country['be'] = esc_html__('Belgium', 'wp-jobsearch');
        $country['br'] = esc_html__('Brazil', 'wp-jobsearch');
        $country['ca'] = esc_html__('Canada', 'wp-jobsearch');
        $country['cl'] = esc_html__('Chile', 'wp-jobsearch');
        $country['cn'] = esc_html__('China', 'wp-jobsearch');
        $country['co'] = esc_html__('Colombia', 'wp-jobsearch');
        $country['cz'] = esc_html__('Czech Republic', 'wp-jobsearch');
        $country['dk'] = esc_html__('Denmark', 'wp-jobsearch');
        $country['fi'] = esc_html__('Finland', 'wp-jobsearch');
        $country['fr'] = esc_html__('France', 'wp-jobsearch');
        $country['de'] = esc_html__('Germany', 'wp-jobsearch');
        $country['gr'] = esc_html__('Greece', 'wp-jobsearch');
        $country['hk'] = esc_html__('Hong Kong', 'wp-jobsearch');
        $country['hu'] = esc_html__('Hungary', 'wp-jobsearch');
        $country['in'] = esc_html__('India', 'wp-jobsearch');
        $country['id'] = esc_html__('Indonesia', 'wp-jobsearch');
        $country['ie'] = esc_html__('Ireland', 'wp-jobsearch');
        $country['il'] = esc_html__('Israel', 'wp-jobsearch');
        $country['it'] = esc_html__('Italy', 'wp-jobsearch');
        $country['jp'] = esc_html__('Japan', 'wp-jobsearch');
        $country['kr'] = esc_html__('Korea', 'wp-jobsearch');
        $country['kw'] = esc_html__('Kuwait', 'wp-jobsearch');
        $country['lu'] = esc_html__('Luxembourg', 'wp-jobsearch');
        $country['my'] = esc_html__('Malaysia', 'wp-jobsearch');
        $country['mx'] = esc_html__('Mexico', 'wp-jobsearch');
        $country['nl'] = esc_html__('Netherlands', 'wp-jobsearch');
        $country['nz'] = esc_html__('New Zealand', 'wp-jobsearch');
        $country['no'] = esc_html__('Norway', 'wp-jobsearch');
        $country['om'] = esc_html__('Oman', 'wp-jobsearch');
        $country['pk'] = esc_html__('Pakistan', 'wp-jobsearch');
        $country['pe'] = esc_html__('Peru', 'wp-jobsearch');
        $country['ph'] = esc_html__('Philippines', 'wp-jobsearch');
        $country['pl'] = esc_html__('Poland', 'wp-jobsearch');
        $country['pt'] = esc_html__('Portugal', 'wp-jobsearch');
        $country['qa'] = esc_html__('Qatar', 'wp-jobsearch');
        $country['ro'] = esc_html__('Romania', 'wp-jobsearch');
        $country['ru'] = esc_html__('Russia', 'wp-jobsearch');
        $country['sa'] = esc_html__('Saudi Arabia', 'wp-jobsearch');
        $country['sg'] = esc_html__('Singapore', 'wp-jobsearch');
        $country['za'] = esc_html__('South Africa', 'wp-jobsearch');
        $country['es'] = esc_html__('Spain', 'wp-jobsearch');
        $country['se'] = esc_html__('Sweden', 'wp-jobsearch');
        $country['ch'] = esc_html__('Switzerland', 'wp-jobsearch');
        $country['tw'] = esc_html__('Taiwan', 'wp-jobsearch');
        $country['tr'] = esc_html__('Turkey', 'wp-jobsearch');
        $country['ae'] = esc_html__('United Arab Emirates', 'wp-jobsearch');
        $country['gb'] = esc_html__('United Kingdom', 'wp-jobsearch');
        $country['ve'] = esc_html__('Venezuela', 'wp-jobsearch');
        return $country;
    }

}