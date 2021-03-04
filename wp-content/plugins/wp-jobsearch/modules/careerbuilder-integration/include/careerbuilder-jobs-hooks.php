<?php
/*
  Class : CareerBuilder jobs Hooks
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class JobSearch_CareerBuilder_Jobs_Hooks {

    // hook things up
    public function __construct() {

        // job meta fields
        //add_action('jobsearch_job_meta_ext_fields', array($this, 'careerbuilder_job_meta_fields'));

        //
        add_action('admin_menu', array($this, 'jobsearch_careerbuilder_jobs_import_page'));
        add_action('wp_ajax_jobsearch_import_careerbuilder_jobs', array($this, 'jobsearch_import_careerbuilder_jobs'));

        // job listings shortcode params hook
        add_filter('jobsearch_job_integrations_types_list', array($this, 'shortcode_params_add'), 11, 1);

        // job listings query args params hook
        add_filter('jobsearch_job_listing_query_args_array', array($this, 'careerbuilder_jobs_listing_parameters'), 10, 2);
    }

    public function jobsearch_careerbuilder_jobs_import_page() {

        $careerbuilder_jobs_switch = get_option('jobsearch_integration_careerbuild_jobs');
        if ($careerbuilder_jobs_switch == 'on') {
            add_submenu_page('edit.php?post_type=job', esc_html__('Import CareerBuilder Jobs', 'wp-jobsearch'), esc_html__('Import CareerBuilder Jobs', 'wp-jobsearch'), 'manage_options', 'import-careerbuilder-jobs', array($this, 'jobsearch_import_careerbuilder_jobs_settings'));
        }
    }

    /**
     * CareerBuilder jobs settings page
     * */
    public function jobsearch_import_careerbuilder_jobs_settings() {
        global $jobsearch_form_fields;
        ?>
        <div id="wrapper" class="jobsearch-post-settings jobsearch-careerbuilder-import-sec">
            <h2><?php esc_html_e('Import CareerBuilder Jobs', 'wp-jobsearch'); ?></h2>
            <div class="error" id="error_msg"><p><strong><?php _e('There is some error importing jobs.', 'wp-jobsearch'); ?></strong></p></div>
            <div id="success_msg" class="updated"><p><strong><?php _e('CareerBuilder jobs are imported successfully.', 'wp-jobsearch'); ?></strong></p></div>
            
            <form id="jobsearch-import-careerbuilder-jobs" class="jobsearch-careerbuilder-jobs" method="post" enctype="multipart/form-data">
                <?php
                wp_nonce_field('jobsearch-import-careerbuilder-jobs-page', '_wpnonce-jobsearch-import-careerbuilder-jobs-page');
                ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Keywords', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'force_std' => '',
                            'id' => 'keyword',
                            'cus_name' => 'keyword',
                            'field_desc' => esc_html__('Enter job title, keywords or company name. The default keyword is all.', 'wp-jobsearch'),
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Location', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'force_std' => '',
                            'id' => 'location',
                            'cus_name' => 'location',
                            'field_desc' => esc_html__('Enter a location for search.', 'wp-jobsearch'),
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Number of jobs', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'force_std' => '10',
                            'id' => 'per_page',
                            'cus_name' => 'per_page',
                            'field_desc' => esc_html__('Enter number of jobs. Default is 10.', 'wp-jobsearch'),
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Expired on', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'force_std' => '0',
                            'id' => 'expire_days',
                            'cus_name' => 'expire_days',
                            'field_desc' => esc_html__('Enter number of days (numeric format) for expiray date after job posted date.', 'wp-jobsearch'),
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Posted By', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        jobsearch_get_custom_post_field('', 'employer', esc_html__('Auto Generate', 'wp-jobsearch'), 'job_username', 'job_username');
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">&nbsp;</div>
                    <div class="elem-field">
                        <input type="button" id="import-careerbuilder-jobs" class="import-careerbuilder-jobs" name="import-careerbuilder-jobs" onclick="javascript:jobsearch_import_careerbuilder_jobs_submit('<?php echo esc_js(admin_url('admin-ajax.php')) ?>');" value="<?php esc_html_e('Import CareerBuilder Jobs', 'wp-jobsearch') ?>">
                        <div id="loading"><i class="fa fa-refresh fa-spin"></i></div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    public function careerbuilder_job_meta_fields() {
        global $jobsearch_form_fields;

        $careerbuilder_jobs_switch = get_option('jobsearch_integration_careerbuild_jobs');

        if ($careerbuilder_jobs_switch == 'on') {
            ?>
            <div class="jobsearch-elem-heading">
                <h2><?php esc_html_e('CareerBuilder Job Fields', 'wp-jobsearch') ?></h2>
            </div>

            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Job Detail Url', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'job_detail_url',
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field">
                <div class="elem-label">
                    <label><?php esc_html_e('Company Name', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'company_name',
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <?php
        }
    }

    public function jobsearch_import_careerbuilder_jobs() {
        $search_keywords = sanitize_text_field(stripslashes($_POST['keyword']));
        $search_location = sanitize_text_field(stripslashes($_POST['location']));
        $per_page = sanitize_text_field($_POST['per_page']);
        $job_username = sanitize_text_field($_POST['job_username']);
        
        if ($per_page < 0) {
            $per_page = 10;
        }
        
        $api_args = array(
            'search' => $search_keywords,
            'location' => $search_location,
            'jobs_per_page' => $per_page,
            'radius_miles' => $radius,
        );

        $careerbuilder_jobs = JobSearch_CareerBuilder_API::get_jobs($api_args);
        
        $json = array();
        if (isset($careerbuilder_jobs['error']) && $careerbuilder_jobs['error'] != '') {
            $json['type'] = 'error';
            $json['message'] = $careerbuilder_jobs['error'];
        } else if (empty($careerbuilder_jobs) || $careerbuilder_jobs === NULL) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Sorry! There are no jobs found.', 'wp-jobsearch');
        } else {
            $no_unique_jobs_found = true;
            $user_id = get_current_user_id();
            foreach ($careerbuilder_jobs as $careerbuilder_job) {
                $job_title = isset($careerbuilder_job['title']) ? $careerbuilder_job['title'] : '';
                $job_desc = isset($careerbuilder_job['tagline']) ? $careerbuilder_job['tagline'] : '';
                $job_location = isset($careerbuilder_job['location']) ? $careerbuilder_job['location'] : '';
                $job_lat = isset($careerbuilder_job['latitude']) ? $careerbuilder_job['latitude'] : '';
                $job_lng = isset($careerbuilder_job['longitude']) ? $careerbuilder_job['longitude'] : '';
                $job_url = isset($careerbuilder_job['url']) ? $careerbuilder_job['url'] : '';
                $job_company = isset($careerbuilder_job['company']) ? $careerbuilder_job['company'] : '';
                $job_type = isset($careerbuilder_job['type']) ? $careerbuilder_job['type'] : '';

                $existing_id = jobsearch_get_postmeta_id_byval('jobsearch_field_job_detail_url', $job_url);

                if ($existing_id > 0) {
                    //
                } else {
                    $no_unique_jobs_found = false;
                    $post_data = array(
                        'post_type' => 'job',
                        'post_title' => $job_title,
                        'post_content' => $job_desc,
                        'post_status' => 'publish',
                        //'post_author' => $user_id
                    );
                    // Insert the job into the database
                    $post_id = wp_insert_post($post_data);

                    //
                    update_post_meta($post_id, 'jobsearch_job_employer_status', 'approved');
                    update_post_meta($post_id, 'jobsearch_field_job_featured', '');

                    // Insert job username meta key
                    if ($job_username > 0) {
                        update_post_meta($post_id, 'jobsearch_field_job_posted_by', $job_username, true);
                    } else {
                        if ($job_company != '') {
                            jobsearch_fake_generate_employer_byname($job_company, $post_id);
                        }
                    }

                    // Insert job posted on meta key
                    update_post_meta($post_id, 'jobsearch_field_job_publish_date', current_time('timestamp'), true);

                    // Insert job expired on meta key
                    $expire_days = $_POST['expire_days'];
                    $expired_date = date('d-m-Y H:i:s', strtotime("$expire_days days", current_time('timestamp')));
                    update_post_meta($post_id, 'jobsearch_field_job_expiry_date', strtotime($expired_date), true);

                    // Insert job status meta key
                    update_post_meta($post_id, 'jobsearch_field_job_status', 'approved', true);

                    // Insert job address meta key
                    if ($job_location != '') {
                        update_post_meta($post_id, 'jobsearch_field_location_address', $job_location, true);
                    }

                    // Insert job latitude meta key
                    update_post_meta($post_id, 'jobsearch_field_location_lat', ($job_lat), true);

                    // Insert job longitude meta key
                    update_post_meta($post_id, 'jobsearch_field_location_lng', ($job_lng), true);

                    // Insert job referral meta key
                    update_post_meta($post_id, 'jobsearch_job_referral', 'careerbuilder', true);

                    // Insert job detail url meta key
                    update_post_meta($post_id, 'jobsearch_field_job_detail_url', ($job_url), true);

                    // Insert job comapny name meta key
                    update_post_meta($post_id, 'jobsearch_field_company_name', $job_company, true);
                    
                    update_post_meta($post_id, 'jobsearch_field_job_apply_type', 'external', true);
                    update_post_meta($post_id, 'jobsearch_field_job_apply_url', $job_url, true);

                    // Create and assign taxonomy to post
                    if ($job_type != '') {
                        wp_insert_term($job_type, 'jobtype');
                        $term = get_term_by('name', $job_type, 'jobtype');
                        wp_set_post_terms($post_id, $term->term_id, 'jobtype');
                    }
                }
            }
            if ($no_unique_jobs_found) {
                $json['type'] = 'error';
                $json['message'] = __('No new job found.', 'wp-jobsearch');
            } else {
                $json['type'] = 'success';
                $json['msg'] = sprintf(__('%s careerbuilder jobs are imported successfully.', 'wp-jobsearch'), count($careerbuilder_jobs));
            }
        }
        if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_import_careerbuilder_jobs') {
            echo json_encode($json);
            die();
        }
    }

    public function array_insert($array, $values, $offset) {
        return array_slice($array, 0, $offset, true) + $values + array_slice($array, $offset, NULL, true);
    }

    public function shortcode_params_add($opts = array()) {
        $careerbuilder_jobs_switch = get_option('jobsearch_integration_careerbuild_jobs');

        if ($careerbuilder_jobs_switch == 'on') {
            $opts[] = 'careerjet';
        }

        return $opts;
    }

    public function careerbuilder_jobs_listing_parameters($args, $attr) {

        $careerbuilder_jobs_switch = get_option('jobsearch_integration_careerbuild_jobs');

        if ($careerbuilder_jobs_switch == 'on') {
            if (isset($attr['job_list_type']) && $attr['job_list_type'] == 'careerbuilder') {
                $filter_arr = array(
                    'key' => 'jobsearch_job_referral',
                    'value' => 'careerbuilder',
                    'compare' => '=',
                );
                $args['meta_query'][] = $filter_arr;
            }
        }

        return $args;
    }

}

// Class JobSearch_CareerBuilder_Jobs_Hooks
global $JobSearch_CareerBuilder_Jobs_Hooks_obj;
$JobSearch_CareerBuilder_Jobs_Hooks_obj = new JobSearch_CareerBuilder_Jobs_Hooks();
