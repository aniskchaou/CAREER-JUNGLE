<?php

/*
  Class : Reviews
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_Reviews {

// hook things up
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'front_enqueue_scripts'));
        //
        add_action('jobsearch_dashbord_instyles_list_aftr', array($this, 'enqueue_dash_styles'));
        add_filter('redux/options/jobsearch_plugin_options/sections', array($this, 'jobsearch_location_plugin_option_fields'));
        add_action('init', array($this, 'titles_translation'));
        $this->load_files();
    }
    
    public function enqueue_dash_styles() {

        global $jobsearch_plugin_options;
        $cand_review_switch = isset($jobsearch_plugin_options['candidate_reviews_switch']) ? $jobsearch_plugin_options['candidate_reviews_switch'] : '';
        $emp_review_switch = isset($jobsearch_plugin_options['reviews_switch']) ? $jobsearch_plugin_options['reviews_switch'] : '';
        if ($cand_review_switch == 'on' || $emp_review_switch == 'on') {
            wp_enqueue_style('fontawesome-stars', jobsearch_plugin_get_url('modules/reviews/css/fontawesome-stars.css'), array());
            wp_enqueue_style('jobsearch-reviews', jobsearch_plugin_get_url('modules/reviews/css/reviews-style.css'), array());
        }
    }

    public function front_enqueue_scripts() {
        global $sitepress, $jobsearch_plugin_options;
        
        $cand_review_switch = isset($jobsearch_plugin_options['candidate_reviews_switch']) ? $jobsearch_plugin_options['candidate_reviews_switch'] : '';
        $emp_review_switch = isset($jobsearch_plugin_options['reviews_switch']) ? $jobsearch_plugin_options['reviews_switch'] : '';

        $admin_ajax_url = admin_url('admin-ajax.php');
        if ( function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') ) {
            $lang_code = $sitepress->get_current_language();
            $admin_ajax_url = add_query_arg(array('lang' => $lang_code), $admin_ajax_url);
        }
        $is_page = is_page();
        $page_content = '';
        if ($is_page) {
            $page_id = get_the_ID();
            $page_post = get_post($page_id);
            $page_content = isset($page_post->post_content) ? $page_post->post_content : '';
        }
        $is_jobs_elemnt_page = $is_cands_elemnt_page = $is_emps_elemnt_page = false;
        if (strpos($page_content, 'job_short_counter')) {
            $is_jobs_elemnt_page = true;
        }
        if (strpos($page_content, 'candidate_short_counter')) {
            $is_cands_elemnt_page = true;
        }
        if (strpos($page_content, 'employer_short_counter')) {
            $is_emps_elemnt_page = true;
        }
        
        $enqueue_css = false;
        if ($cand_review_switch == 'on' || $emp_review_switch == 'on') {
            if ($is_page && 
                    (has_shortcode($page_content, 'jobsearch_candidate_shortcode') 
                    || has_shortcode($page_content, 'jobsearch_employer_shortcode') 
                    || has_shortcode($page_content, 'jobsearch_ad'))
                    || $is_cands_elemnt_page
                    || $is_emps_elemnt_page
                ) {
                $enqueue_css = true;
            }
            if (is_singular(array('employer', 'candidate'))) {
                $enqueue_css = true;
            }
        }

        if ($enqueue_css) {
            wp_enqueue_style('fontawesome-stars', jobsearch_plugin_get_url('modules/reviews/css/fontawesome-stars.css'), array());
            wp_enqueue_style('jobsearch-reviews', jobsearch_plugin_get_url('modules/reviews/css/reviews-style.css'), array());
        }
        
        //
        wp_register_script('jobsearch-add-review', jobsearch_plugin_get_url('modules/reviews/js/reviews-functions.js'), array(), '', true);
        $jobsearch_plugin_arr = array(
            'plugin_url' => jobsearch_plugin_get_url(),
            'ajax_url' => $admin_ajax_url,
            'error_msg' => esc_html__('There is some problem.', 'wp-jobsearch'),
            'loading' => esc_html__('Loading...', 'wp-jobsearch'),
        );

        wp_localize_script('jobsearch-add-review', 'jobsearch_reviews_vars', $jobsearch_plugin_arr);
        wp_register_script('jobsearch-barrating', jobsearch_plugin_get_url('modules/reviews/js/jquery.barrating.js'), array(), '', true);
    }

    public function load_files() {
        include plugin_dir_path(dirname(__FILE__)) . 'reviews/include/frontend.php';
        include plugin_dir_path(dirname(__FILE__)) . 'reviews/include/dashboard.php';
    }

    public function titles_translation() {
        global $jobsearch_plugin_options;
        $review_titles = isset($jobsearch_plugin_options['reviews_titles']) ? $jobsearch_plugin_options['reviews_titles'] : '';
        if (!empty($review_titles)) {
            foreach ($review_titles as $review_title) {
                do_action('wpml_register_single_string', 'JobSearch Options', 'Review Title - ' . $review_title, $review_title);
            }
        }
        $cand_review_titles = isset($jobsearch_plugin_options['cand_reviews_titles']) ? $jobsearch_plugin_options['cand_reviews_titles'] : '';
        if (!empty($cand_review_titles)) {
            foreach ($cand_review_titles as $review_title) {
                do_action('wpml_register_single_string', 'JobSearch Options', 'Review Title - ' . $review_title, $review_title);
            }
        }
    }

    public function jobsearch_location_plugin_option_fields($sections) {

        $sections[] = array(
            'title' => __('Reviews Settings', 'wp-jobsearch'),
            'id' => 'reviews-settings',
            'desc' => '',
            'icon' => 'el el-star',
            'fields' => array(
                array(
                    'id' => 'reviews_switch',
                    'type' => 'button_set',
                    'title' => __('Employer Reviews', 'wp-jobsearch'),
                    'subtitle' => __('Switch On/Off Employer Reviews.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'candidate_reviews_switch',
                    'type' => 'button_set',
                    'title' => __('Candidate Reviews', 'wp-jobsearch'),
                    'subtitle' => __('Switch On/Off Reviews at the candidate detail page.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'public_reviews_switch',
                    'type' => 'button_set',
                    'title' => __('Public Reviews', 'wp-jobsearch'),
                    'subtitle' => __('Anyone can review with or without login.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'reviews_titles',
                    'type' => 'multi_text',
                    'title' => __('Employer Review Titles', 'wp-jobsearch'),
                    'required' => array('reviews_switch', 'equals', 'on'),
                    'subtitle' => '',
                    'desc' => __('Add multiple reviews titles.', 'wp-jobsearch'),
                    'default' => array(__('Quality', 'wp-jobsearch'), __('Communication', 'wp-jobsearch'), __('Goodwill', 'wp-jobsearch')),
                ),
                array(
                    'id' => 'cand_reviews_titles',
                    'type' => 'multi_text',
                    'title' => __('Candidate Review Titles', 'wp-jobsearch'),
                    'required' => array('candidate_reviews_switch', 'equals', 'on'),
                    'subtitle' => '',
                    'desc' => __('Add multiple reviews titles.', 'wp-jobsearch'),
                    'default' => array(__('Education', 'wp-jobsearch'), __('Skills', 'wp-jobsearch'), __('Communication', 'wp-jobsearch')),
                ),
                array(
                    'id' => 'review_text_length',
                    'type' => 'text',
                    'title' => __('Review Text Length', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => __('Define Maximum characters Length for Review Text.', 'wp-jobsearch'),
                    'default' => '500',
                ),
            ),
        );
        return $sections;
    }

}

// class Jobsearch_Reviews 
$Jobsearch_Reviews_obj = new Jobsearch_Reviews();
global $Jobsearch_Reviews_obj;

