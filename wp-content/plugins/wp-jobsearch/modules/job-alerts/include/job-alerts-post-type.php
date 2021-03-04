<?php
/*
  Class : Job Alerts Post Type
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class JobSearch_Job_Alerts_Post {

// hook things up
    public function __construct() {

        add_action('init', array($this, 'register_post'), 1);
        //
        add_action('add_meta_boxes', array($this, 'meta_box_for_job_alerts'));
        
        add_filter('manage_job-alert_posts_columns', array($this, 'columns_add'));
        add_action('manage_job-alert_posts_custom_column', array($this, 'custom_columns'));
    }

    public function register_post() {

        $labels = array(
            'name' => _x('Job Alerts', 'post type general name', 'wp-jobsearch'),
            'singular_name' => _x('Job Alert', 'post type singular name', 'wp-jobsearch'),
            'menu_name' => _x('Job Alerts', 'admin menu', 'wp-jobsearch'),
            'name_admin_bar' => _x('Job Alert', 'add new on admin bar', 'wp-jobsearch'),
            'add_new' => _x('Add New', 'book', 'wp-jobsearch'),
            'add_new_item' => esc_html__('Add New Job Alert', 'wp-jobsearch'),
            'new_item' => esc_html__('New Job Alert', 'wp-jobsearch'),
            'edit_item' => esc_html__('Edit Job Alert', 'wp-jobsearch'),
            'view_item' => esc_html__('View Job Alert', 'wp-jobsearch'),
            'all_items' => esc_html__('Job Alerts', 'wp-jobsearch'),
            'search_items' => esc_html__('Search Job Alerts', 'wp-jobsearch'),
            'parent_item_colon' => esc_html__('Parent Job Alerts:', 'wp-jobsearch'),
            'not_found' => esc_html__('No Job Alerts found.', 'wp-jobsearch'),
            'not_found_in_trash' => esc_html__('No Job Alerts found in Trash.', 'wp-jobsearch'),
        );

        $args = array(
            'labels' => $labels,
            'description' => esc_html__('This allows the user to manage job alerts.', 'wp-jobsearch'),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'edit.php?post_type=job',
            'query_var' => true,
            'capability_type' => 'post',
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'hierarchical' => false,
            'rewrite' => array('slug' => 'job-alert'),
            'supports' => false,
            'has_archive' => false,
        );

        // Register custom post type.
        register_post_type("job-alert", $args);
    }
    
    public function columns_add($columns) {

        unset($columns['date']);
        $columns['user_email'] = esc_html__('User Email', 'wp-jobsearch');
        $columns['frequencies'] = esc_html__('Email Frequencies', 'wp-jobsearch');
        $columns['last_email'] = esc_html__('Last Email Sent', 'wp-jobsearch');
        $columns['next_email'] = esc_html__('Next Email Time', 'wp-jobsearch');
        $columns['date'] = esc_html__('Date', 'wp-jobsearch');

        return $columns;
    }
    
    public function custom_columns($column) {
        global $post, $wpdb;
        
        $alert_id = $post->ID;
        switch ($column) {
            case 'user_email' :
                $user_email = get_post_meta($alert_id, 'jobsearch_field_alert_email', true);
                echo ($user_email);
                break;
            case 'frequencies' :
                $frequency_annually = get_post_meta($alert_id, 'jobsearch_field_alert_annually', true);
                $frequency_biannually = get_post_meta($alert_id, 'jobsearch_field_alert_biannually', true);
                $frequency_monthly = get_post_meta($alert_id, 'jobsearch_field_alert_monthly', true);
                $frequency_fortnightly = get_post_meta($alert_id, 'jobsearch_field_alert_fortnightly', true);
                $frequency_weekly = get_post_meta($alert_id, 'jobsearch_field_alert_weekly', true);
                $frequency_daily = get_post_meta($alert_id, 'jobsearch_field_alert_daily', true);
                $frequency_hourly = get_post_meta($alert_id, 'jobsearch_field_alert_hourly', true);
                
                $frequencies_list = array();
                if ($frequency_hourly == 'on') {
                    $frequencies_list[] = esc_html__('Hourly', 'wp-jobsearch');
                }
                if ($frequency_daily == 'on') {
                    $frequencies_list[] = esc_html__('Daily', 'wp-jobsearch');
                }
                if ($frequency_weekly == 'on') {
                    $frequencies_list[] = esc_html__('Weekly', 'wp-jobsearch');
                }
                if ($frequency_fortnightly == 'on') {
                    $frequencies_list[] = esc_html__('Fortnightly', 'wp-jobsearch');
                }
                if ($frequency_monthly == 'on') {
                    $frequencies_list[] = esc_html__('Monthly', 'wp-jobsearch');
                }
                if ($frequency_biannually == 'on') {
                    $frequencies_list[] = esc_html__('Biannually', 'wp-jobsearch');
                }
                if ($frequency_annually == 'on') {
                    $frequencies_list[] = esc_html__('Annually', 'wp-jobsearch');
                }
                if (!empty($frequencies_list)) {
                    $frequency_type = implode(', ', $frequencies_list);
                } else {
                    $frequency_type = '-';
                }
                echo apply_filters('jobsearch_inadmin_list_frequency_type_text', $frequency_type, $alert_id);
                break;
            case 'last_email' :
                $last_email_sent = get_post_meta($post->ID, 'last_time_email_sent', true);
                if ($last_email_sent > 0) {
                    $time_format = get_option('time_format');
                    if ($time_format == '') {
                        $time_format = 'g:i A';
                    }
                    echo date_i18n(get_option('date_format'), $last_email_sent) . ' ' . date_i18n($time_format, $last_email_sent);
                } else {
                    echo '-';
                }
                break;
            case 'next_email' :
                $last_email_sent = get_post_meta($post->ID, 'last_time_email_sent', true);
                $last_email_sent = $last_email_sent != '' && $last_email_sent > 0 ? $last_email_sent : current_time('timestamp');

                $frequency_annually = get_post_meta($alert_id, 'jobsearch_field_alert_annually', true);
                $frequency_biannually = get_post_meta($alert_id, 'jobsearch_field_alert_biannually', true);
                $frequency_monthly = get_post_meta($alert_id, 'jobsearch_field_alert_monthly', true);
                $frequency_fortnightly = get_post_meta($alert_id, 'jobsearch_field_alert_fortnightly', true);
                $frequency_weekly = get_post_meta($alert_id, 'jobsearch_field_alert_weekly', true);
                $frequency_daily = get_post_meta($alert_id, 'jobsearch_field_alert_daily', true);
                $frequency_hourly = get_post_meta($alert_id, 'jobsearch_field_alert_hourly', true);

                if ($frequency_hourly == 'on') {
                    $selected_frequency = '+1 hour';
                } else if ($frequency_daily == 'on') {
                    $selected_frequency = '+1 days';
                } else if ($frequency_weekly == 'on') {
                    $selected_frequency = '+7 days';
                } else if ($frequency_fortnightly == 'on') {
                    $selected_frequency = '+15 days';
                } else if ($frequency_monthly == 'on') {
                    $selected_frequency = '+30 days';
                } else if ($frequency_biannually == 'on') {
                    $selected_frequency = '+182 days';
                } else if ($frequency_annually == 'on') {
                    $selected_frequency = '+365 days';
                }

                if (isset($selected_frequency)) {
                    $next_email_time = strtotime($selected_frequency, intval($last_email_sent));
                    $time_format = get_option('time_format');
                    if ($time_format == '') {
                        $time_format = 'g:i A';
                    }
                    echo date_i18n(get_option('date_format'), $next_email_time) . ' ' . date_i18n($time_format, $next_email_time);
                } else {
                    echo '-';
                }
                
                break;
        }
    }

    public function meta_box_for_job_alerts() {
        add_meta_box('job_alert_meta_options', esc_html__('Job Alert Options', 'wp-jobsearch'), array($this, 'meta_box_options'), 'job-alert', 'normal', 'high');
    }
    
    public function meta_box_options() {
        global $jobsearch_form_fields, $post;
        //$dcdss = get_post_meta($post->ID, 'last_time_email_sent', true);
        //var_dump($dcdss);
        $gjobs_query = get_post_meta($post->ID, 'jobsearch_field_alert_jobs_query', true);
        $gjobs_query = str_replace('< =', '<=', $gjobs_query);
        $gjobs_query = json_decode($gjobs_query, true);
        //echo '<pre>';
        //var_dump($gjobs_query);
        //echo '</pre>';
        
        //echo '<pre>';
        //var_dump(get_post_meta($post->ID));
        
        $selected_frequency = '+1 days';
        $frequency = str_replace('+', '-', $selected_frequency);
        
        if (isset($gjobs_query['meta_query'])) {
            $gjobs_query['meta_query'][0][] = array(
                'key' => 'jobsearch_field_job_expiry_date',
                'value' => current_time('timestamp'),
                'compare' => '>=',
            );
            $gjobs_query['meta_query'][0][0] = array(
                'key' => 'jobsearch_field_job_publish_date',
                'value' => strtotime(date('Y-m-d', strtotime($frequency))),
                'compare' => '>=',
            );
        }

        ?>
        <div class="jobsearch-post-settings">
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Email', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'alert_email',
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Name', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'alert_name',
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Alert Query', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'alert_query',
                    );
                    $jobsearch_form_fields->textarea_field($field_params);
                    ?>
                </div>
            </div>
            <?php do_action('jobsearch_inbk_alertmeta_fields_bfr_freqs') ?>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Annually Frequency', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'alert_annually',
                    );
                    $jobsearch_form_fields->checkbox_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Biannually Frequency', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'alert_biannually',
                    );
                    $jobsearch_form_fields->checkbox_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Monthly Frequency', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'alert_monthly',
                    );
                    $jobsearch_form_fields->checkbox_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Fortnightly Frequency', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'alert_fortnightly',
                    );
                    $jobsearch_form_fields->checkbox_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Weekly Frequency', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'alert_weekly',
                    );
                    $jobsearch_form_fields->checkbox_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Daily Frequency', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'alert_daily',
                    );
                    $jobsearch_form_fields->checkbox_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Hourly Frequency', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'alert_hourly',
                    );
                    $jobsearch_form_fields->checkbox_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Never', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'alert_never',
                    );
                    $jobsearch_form_fields->checkbox_field($field_params);
                    ?>
                </div>
            </div>
        </div>
        <?php
    }

}

// Class JobSearch_Job_Alerts_Hooks
$JobSearch_Job_Alerts_Post_obj = new JobSearch_Job_Alerts_Post();
global $JobSearch_Job_Alerts_Post_obj;
