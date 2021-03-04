<?php

/*
  Class : Job Alerts
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class JobSearch_Job_Alerts
{
    public static $job_details = array();
    // hook things up
    public function __construct()
    {
        $this->load_files();
        add_action('wp_enqueue_scripts', array($this, 'front_enqueue_scripts'));
        add_action('jobsearch_job_alerts_schedule', array($this, 'job_alerts_schedule_callback'));
        //
        add_action('jobsearch_dashbord_instyles_list_aftr', array($this, 'enqueue_script_styles'));
        add_action('jobsearch_jobtemp_instyles_list_aftr', array($this, 'enqueue_script_styles'), 20);
    }

    public function enqueue_script_styles() {

        global $jobsearch_plugin_options, $sitepress;

        $admin_ajax_url = admin_url('admin-ajax.php');
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        
        $job_alerts_switch = isset($jobsearch_plugin_options['job_alerts_switch']) ? $jobsearch_plugin_options['job_alerts_switch'] : '';
        if ($job_alerts_switch == 'on') {
            wp_enqueue_style('jobsearch-job-alerts', jobsearch_plugin_get_url('modules/job-alerts/css/job-alerts.css'));
            wp_enqueue_script('jobsearch-job-alerts-scripts', jobsearch_plugin_get_url('modules/job-alerts/js/job-alerts.js'), array(), JobSearch_plugin::get_version(), true);
            $jobsearch_plugin_arr = array(
                'plugin_url' => jobsearch_plugin_get_url(),
                'ajax_url' => $admin_ajax_url,
                'error_msg' => esc_html__('There is some problem.', 'wp-jobsearch'),
                'email_field_error' => esc_html__('Please enter a valid email.', 'wp-jobsearch'),
                'name_field_error' => esc_html__('Please enter an alert name.', 'wp-jobsearch'),
                'submit_txt' => esc_html__('Create Alert', 'wp-jobsearch'),
                'save_alert_txt' => esc_html__('Save Jobs Alert', 'wp-jobsearch'),
            );

            wp_localize_script('jobsearch-job-alerts-scripts', 'jobsearch_jobalerts_vars', $jobsearch_plugin_arr);
        }
    }

    public function front_enqueue_scripts()
    {
        global $jobsearch_plugin_options, $sitepress;
        $admin_ajax_url = admin_url('admin-ajax.php');
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
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
        $job_alerts_switch = isset($jobsearch_plugin_options['job_alerts_switch']) ? $jobsearch_plugin_options['job_alerts_switch'] : '';
        if ($job_alerts_switch == 'on') {
            if ($is_page && (has_shortcode($page_content, 'jobsearch_job_shortcode') || $is_jobs_elemnt_page) || class_exists('Addon_Jobsearch_Quick_Job_detail')) {
                //
                wp_enqueue_style('jobsearch-job-alerts', jobsearch_plugin_get_url('modules/job-alerts/css/job-alerts.css'));
                wp_enqueue_script('jobsearch-job-alerts-scripts', jobsearch_plugin_get_url('modules/job-alerts/js/job-alerts.js'), array(), JobSearch_plugin::get_version(), true);
                $jobsearch_plugin_arr = array(
                    'plugin_url' => jobsearch_plugin_get_url(),
                    'ajax_url' => $admin_ajax_url,
                    'error_msg' => esc_html__('There is some problem.', 'wp-jobsearch'),
                    'email_field_error' => esc_html__('Please enter a valid email.', 'wp-jobsearch'),
                    'name_field_error' => esc_html__('Please enter an alert name.', 'wp-jobsearch'),
                    'submit_txt' => esc_html__('Create Alert', 'wp-jobsearch'),
                    'save_alert_txt' => esc_html__('Save Jobs Alert', 'wp-jobsearch'),
                );
                wp_localize_script('jobsearch-job-alerts-scripts', 'jobsearch_jobalerts_vars', $jobsearch_plugin_arr);
            }
        }
    }

    public function load_files()
    {
        include plugin_dir_path(dirname(__FILE__)) . 'job-alerts/include/job-filters-alerts.php';
        include plugin_dir_path(dirname(__FILE__)) . 'job-alerts/include/job-alerts-hooks.php';
        include plugin_dir_path(dirname(__FILE__)) . 'job-alerts/include/job-alerts-post-type.php';
        include plugin_dir_path(dirname(__FILE__)) . 'job-alerts/include/job-alerts-email-template.php';
    }

    public function create_alerts_schedule()
    {
        global $jobsearch_plugin_options;
        $job_alerts_switch = isset($jobsearch_plugin_options['job_alerts_switch']) ? $jobsearch_plugin_options['job_alerts_switch'] : '';
        // Use wp_next_scheduled to check if the event is already scheduled.
        $timestamp = wp_next_scheduled('jobsearch_job_alerts_schedule');
        if (!$timestamp && $job_alerts_switch == 'on') {
            wp_schedule_event(time(), 'daily', 'jobsearch_job_alerts_schedule');
        } else if ($job_alerts_switch != 'on') {
            wp_unschedule_event($timestamp, 'jobsearch_job_alerts_schedule');
        }
    }

    public function job_alerts_schedule_callback()
    {
        global $wpdb;
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $job_alerts_switch = isset($jobsearch__options['job_alerts_switch']) ? $jobsearch__options['job_alerts_switch'] : '';
        if ($job_alerts_switch != 'on') {
            return false;
        }
        // Get alerts
        $args = array(
            'posts_per_page' => '-1',
            'post_type' => 'job-alert',
            'post_status' => 'publish',
            'fields' => 'ids',
            'order' => 'DESC',
            'orderby' => 'ID',
        );
        $job_details = array();
        $job_alerts = new WP_Query($args);
        if ($job_alerts->have_posts()) {
            while ($job_alerts->have_posts()) : $job_alerts->the_post();

                $alert_id = get_the_ID();

                $frequency_annually = get_post_meta($alert_id, 'jobsearch_field_alert_annually', true);
                $frequency_biannually = get_post_meta($alert_id, 'jobsearch_field_alert_biannually', true);
                $frequency_monthly = get_post_meta($alert_id, 'jobsearch_field_alert_monthly', true);
                $frequency_fortnightly = get_post_meta($alert_id, 'jobsearch_field_alert_fortnightly', true);
                $frequency_weekly = get_post_meta($alert_id, 'jobsearch_field_alert_weekly', true);
                $frequency_daily = get_post_meta($alert_id, 'jobsearch_field_alert_daily', true);
                $frequency_hourly = get_post_meta($alert_id, 'jobsearch_field_alert_hourly', true);
                $frequency_never = get_post_meta($alert_id, 'jobsearch_field_alert_never', true);
                $last_time_email_sent = get_post_meta($alert_id, 'last_time_email_sent', true);

                $set_frequency = '';
                if (!empty($frequency_annually)) {
                    $selected_frequency = '+365 days';
                    $set_frequency = esc_html__('Annually', 'wp-jobsearch');
                } else if (!empty($frequency_biannually)) {
                    $selected_frequency = '+182 days';
                    $set_frequency = esc_html__('Biannually', 'wp-jobsearch');
                } else if (!empty($frequency_monthly)) {
                    $selected_frequency = '+30 days';
                    $set_frequency = esc_html__('Monthly', 'wp-jobsearch');
                } else if (!empty($frequency_fortnightly)) {
                    $selected_frequency = '+15 days';
                    $set_frequency = esc_html__('Fortnightly', 'wp-jobsearch');
                } else if (!empty($frequency_weekly)) {
                    $selected_frequency = '+7 days';
                    $set_frequency = esc_html__('Weekly', 'wp-jobsearch');
                } else if (!empty($frequency_daily)) {
                    $selected_frequency = '+1 days';
                    $set_frequency = esc_html__('Daily', 'wp-jobsearch');
                } else if (!empty($frequency_hourly)) {
                    $selected_frequency = '+1 hour';
                    $set_frequency = esc_html__('Hourly', 'wp-jobsearch');
                } else if (!empty($frequency_never)) {
                    $selected_frequency = false;
                    $set_frequency = esc_html__('Never', 'wp-jobsearch');
                } else {
                    $selected_frequency = false;
                    $set_frequency = '';
                }
                if ($selected_frequency != false) {

                    if (time() > strtotime($selected_frequency, intval($last_time_email_sent))) {
                        // Set this for email data.
                        $alert_t_title = get_post_meta($alert_id, 'jobsearch_field_alert_name', true);
                        $gjobs_query = get_post_meta($alert_id, 'jobsearch_field_alert_jobs_query', true);
                        $gjobs_query = str_replace('< =', '<=', $gjobs_query);
                        $gjobs_query = json_decode($gjobs_query, true);
                        if (isset($gjobs_query['meta_query'])) {
                            $gjobs_query['meta_query'][0][] = array(
                                'key' => 'jobsearch_field_job_expiry_date',
                                'value' => current_time('timestamp'),
                                'compare' => '>=',
                            );
                        }
                        //
                        $custom_fields_requstarr = array();
                        $jobsearch_post_cus_fields = get_option('jobsearch_custom_field_job');
                        if (is_array($jobsearch_post_cus_fields) && sizeof($jobsearch_post_cus_fields) > 0) {
                            foreach ($jobsearch_post_cus_fields as $cus_field) {
                                if ($cus_field['type'] == 'salary') {
                                    $query_str_var_name = 'jobsearch_field_job_salary';
                                    $str_salary_type_name = 'job_salary_type';
                                    $alert_saved_saltypval = get_post_meta($alert_id, $str_salary_type_name, true);
                                    if (!empty($alert_saved_saltypval)) {
                                        $custom_fields_requstarr[$str_salary_type_name] = $alert_saved_saltypval;
                                    }
                                } else {
                                    $f_custf_name = isset($cus_field['name']) ? $cus_field['name'] : '';
                                    $query_str_var_name = trim(str_replace(' ', '', $f_custf_name));
                                }
                                $alert_saved_cfval = get_post_meta($alert_id, $query_str_var_name, true);
                                if (!empty($alert_saved_cfval)) {
                                    if (is_array($alert_saved_cfval)) {
                                        $alert_saved_cfval = implode(',', $alert_saved_cfval);
                                    }
                                    $custom_fields_requstarr[$query_str_var_name] = $alert_saved_cfval;
                                }
                            }
                        }
                        if (!empty($custom_fields_requstarr)) {
                            $cusfields_filter_arr = apply_filters('jobsearch_custom_fields_load_filter_array_html', 'job', array(), '', $custom_fields_requstarr);
                            if (!empty($cusfields_filter_arr)) {
                                $meta_post_ids_arr = jobsearch_get_query_whereclase_by_array($cusfields_filter_arr);
                                $ids = !empty($meta_post_ids_arr) ? implode(",", $meta_post_ids_arr) : '0';
                                $job_id_condition = " ID in (" . $ids . ") AND ";
                                $retpost_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $job_id_condition . " post_type='job' AND post_status='publish'");
                                $retpost_ids = !empty($retpost_ids) ? $retpost_ids : array(0);
                                
                                $gjobs_query['post__in'] = $retpost_ids;
                            } else {
                                $gjobs_query['post__in'] = array(0);
                            }
                        }
                        //
                        self::$job_details = array(
                            'id' => $alert_id,
                            'title' => $alert_t_title != '' ? $alert_t_title : '-',
                            'jobs_query' => $gjobs_query,
                            'email' => get_post_meta($alert_id, 'jobsearch_field_alert_email', true),
                            'page_url' => get_post_meta($alert_id, 'jobsearch_field_alert_page_url', true),
                            'url_query' => get_post_meta($alert_id, 'jobsearch_field_alert_query', true),
                            'frequency' => $selected_frequency,
                            'set_frequency' => $set_frequency,
                        );
                        $template = '';

                        $al_jobs_count = self::get_job_alerts_count(self::$job_details['jobs_query'], self::$job_details['frequency']);

                        if ($al_jobs_count > 0) {
                            do_action('jobsearch_new_job_alerts_email', self::$job_details);
                        }
                    }
                }
            endwhile;
        }
        wp_reset_postdata();
    }

    public static function get_job_alerts_count($jobs_query, $frequency)
    {
        $frequency = str_replace('+', '-', $frequency);
        $jobs_query['meta_query'][0][] = array(
            'key' => 'jobsearch_field_job_publish_date',
            'value' => strtotime(date('Y-m-d', strtotime($frequency))),
            'compare' => '>=',
        );

        $jobs_query['posts_per_page'] = 1;
        $loop_count = new WP_Query($jobs_query);
        return $loop_count->found_posts;
    }

}

global $JobSearch_Job_Alerts_obj;
$JobSearch_Job_Alerts_obj = new JobSearch_Job_Alerts();
