<?php
/*
  Class : Indeed jobs Hooks
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class JobSearch_Indeed_Jobs_Hooks
{

    // hook things up
    public function __construct()
    {
        // job meta fields
        //add_action('jobsearch_job_meta_ext_fields', array($this, 'indeed_job_meta_fields'));
        //
        add_action('admin_menu', array($this, 'jobsearch_indeed_jobs_import_page'));
        add_action('wp_ajax_jobsearch_import_indeed_jobs', array($this, 'jobsearch_import_indeed_jobs'));
        // job listings vc shortcode params hook
        add_filter('jobsearch_job_listings_vcsh_params', array($this, 'vc_shortcode_params_add'), 11, 1);
        // job listings editor shortcode params hook
        add_filter('jobsearch_job_listings_sheb_params', array($this, 'editor_shortcode_params_add'), 11, 1);
        // job listings query args params hook
        add_filter('jobsearch_job_listing_query_args_array', array($this, 'indeed_jobs_listing_parameters'), 10, 2);
    }

    public function jobsearch_indeed_jobs_import_page()
    {
        $indeed_import_jobs = get_option('jobsearch_integration_indeed_jobs');
        if ($indeed_import_jobs == 'on') {
            add_submenu_page('edit.php?post_type=job', esc_html__('Import Indeed Jobs', 'wp-jobsearch'), esc_html__('Import Indeed Jobs', 'wp-jobsearch'), 'manage_options', 'import-indeed-jobs', array($this, 'jobsearch_import_indeed_jobs_settings'));
        }
    }

    /**
     * Indeed jobs settings page
     * */
    public function jobsearch_import_indeed_jobs_settings()
    {
        global $jobsearch_form_fields;

        ?>
        <div id="wrapper" class="jobsearch-post-settings jobsearch-indeed-import-sec">
            <h2><?php esc_html_e('Import Indeed Jobs', 'wp-jobsearch'); ?></h2>
            <div id="success_msg" class="updated"><p>
                    <strong><?php _e('Indeed jobs are imported successfully.', 'wp-jobsearch'); ?></strong></p></div>
            <div class="error" id="error_msg"><p>
                    <strong><?php _e('Please enter publisher number to import jobs from Indeed.', 'wp-jobsearch'); ?></strong>
                </p></div>
            <div class="error" id="invalid_publisher_number"></div>
            <form id="jobsearch-import-indeed-jobs" class="jobsearch-indeed-jobs" method="post"
                  enctype="multipart/form-data">
                <?php
                wp_nonce_field('jobsearch-import-indeed-jobs-page', '_wpnonce-jobsearch-import-indeed-jobs-page');
                ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Keywords', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'force_std' => '',
                            'id' => 'search_keywords',
                            'cus_name' => 'q',
                            'field_desc' => esc_html__('Enter job title, keywords or company name. The default keyword is all.', 'wp-jobsearch'),
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <?php
                $countries_list = array();
                $countries = JobSearch_Indeed_API::indeed_api_countries();
                if ($countries) {
                    foreach ($countries as $ke => $value) {
                        $countries_list[$ke] = $value;
                    }
                }
                ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Country', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'force_std' => '',
                            'id' => 'search_country',
                            'cus_name' => 'co',
                            'options' => $countries_list,
                            'field_desc' => esc_html__('Select a country for search.', 'wp-jobsearch'),
                        );
                        $jobsearch_form_fields->select_field($field_params);
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
                            'id' => 'search_location',
                            'cus_name' => 'l',
                            'field_desc' => esc_html__('Enter a location for search.', 'wp-jobsearch'),
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Type', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'force_std' => '',
                            'id' => 'job_type',
                            'cus_name' => 'jt',
                            'options' => array(
                                'fulltime' => esc_html__('Full-Time', 'wp-jobsearch'),
                                'parttime' => esc_html__('Part-Time', 'wp-jobsearch'),
                                'contract' => esc_html__('Contract', 'wp-jobsearch'),
                                'internship' => esc_html__('Internship', 'wp-jobsearch'),
                                'temporary' => esc_html__('Temporary', 'wp-jobsearch'),
                            ),
                            'field_desc' => esc_html__('Choose which type of job to query.', 'wp-jobsearch'),
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Sort By', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'force_std' => '',
                            'id' => 'sort_by',
                            'cus_name' => 'sort',
                            'options' => array(
                                'date' => esc_html__('Date', 'wp-jobsearch'),
                                'relevance' => esc_html__('Relevance', 'wp-jobsearch'),
                            ),
                            'field_desc' => esc_html__('Choose sort query results by Date/Relevance.', 'wp-jobsearch'),
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Start Import Jobs', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'force_std' => '',
                            'id' => 'start',
                            'cus_name' => 'start',
                            'field_desc' => esc_html__('Enter start number to import jobs. Default start number is 1.', 'wp-jobsearch'),
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('No. of Jobs to Import (Maximum Limit 25)', 'wp-jobsearch') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'force_std' => '10',
                            'id' => 'limit',
                            'cus_name' => 'limit',
                            'field_desc' => esc_html__('Enter number of jobs to import. Default number of import jobs is 10. Maximum import jobs limit is 25.', 'wp-jobsearch'),
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
                        <input type="button" id="import-indeed-jobs" class="import-indeed-jobs"
                               name="import-indeed-jobs"
                               onclick="javascript:jobsearch_import_indeed_jobs_submit('<?php echo esc_js(admin_url('admin-ajax.php')) ?>');"
                               value="<?php esc_html_e('Import Indeed Jobs', 'wp-jobsearch') ?>">
                        <div id="loading"><i class="fa fa-refresh fa-spin"></i></div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    public function indeed_job_meta_fields()
    {
        global $jobsearch_form_fields;

        $indeed_jobs_switch = get_option('jobsearch_integration_indeed_jobs');

        if ($indeed_jobs_switch == 'on') {?>
            <div class="jobsearch-elem-heading">
                <h2><?php esc_html_e('Indeed Job Fields', 'wp-jobsearch') ?></h2>
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

    public static function get_job_type($type)
    {
        switch ($type) {
            case 'fulltime' :
                $type = esc_html__('Full Time', 'wp-jobsearch');
                break;
            case 'parttime' :
                $type = esc_html__('Part Time', 'wp-jobsearch');
                break;
            case 'contract' :
                $type = esc_html__('Contract', 'wp-jobsearch');
                break;
            case 'internship' :
                $type = esc_html__('Internship', 'wp-jobsearch');
                break;
            case 'temporary' :
                $type = esc_html__('Temporary', 'wp-jobsearch');
                break;
        }
        return $type;
    }

    public function jobsearch_import_indeed_jobs()
    {
        $publisher_number = get_option('jobsearch_integration_indeed_publisherid');
        $search_keywords = sanitize_text_field(stripslashes($_POST['q']));
        $search_country = sanitize_text_field(stripslashes($_POST['co']));
        $search_location = sanitize_text_field(stripslashes($_POST['l']));
        $job_type = sanitize_text_field($_POST['jt']);
        $start = sanitize_text_field($_POST['start']);
        $limit = sanitize_text_field($_POST['limit']);
        $sort_by = sanitize_text_field($_POST['sort']);
        $job_username = sanitize_text_field($_POST['job_username']);

        $limit = $limit ? $limit : 10;
        $start = $start ? ($start - 1) : 0;
        //
        $api_args = array(
            'publisher' => $publisher_number,
            'q' => $search_keywords ? $search_keywords : 'all',
            'l' => $search_location,
            'co' => $search_country,
            'jt' => $job_type,
            'sort' => $sort_by,
            'start' => $start ? ($start - 1) : 0,
            'limit' => $limit ? $limit : 10,
        );

        $indeed_jobs = JobSearch_Indeed_API::get_jobs_from_indeed($api_args);
        $json = array();
        if (isset($indeed_jobs['error']) && $indeed_jobs['error'] != '') {
            $json['type'] = 'error';
            $json['message'] = $indeed_jobs['error'];
        } elseif (empty($indeed_jobs) || $indeed_jobs === NULL) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Sorry! There are no jobs found for your search query.', 'wp-jobsearch');
        } else {
            $no_unique_jobs_found = true;
            $count_jobs = 0;
            $user_id = get_current_user_id();
            foreach ($indeed_jobs as $indeed_job) {
                $indeed_job = (object)$indeed_job;

                $job_company = $indeed_job->company;
                
                $job_url = $indeed_job->url;
                $existing_id = jobsearch_get_postmeta_id_byval('jobsearch_field_job_detail_url', $job_url);
                
                if ($existing_id > 0) {
                    //
                } else {
                    $no_unique_jobs_found = false;
                    $count_jobs++;
                    $post_data = array(
                        'post_type' => 'job',
                        'post_title' => $indeed_job->jobtitle,
                        'post_content' => $indeed_job->snippet,
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
                    $date = date('d-m-Y H:i:s', strtotime($indeed_job->date));
                    update_post_meta($post_id, 'jobsearch_field_job_publish_date', strtotime($date), true);

                    // Insert job expired on meta key
                    $expire_days = $_POST['expire_days'];
                    $expired_date = date('d-m-Y H:i:s', strtotime("$expire_days days", strtotime($indeed_job->date)));
                    update_post_meta($post_id, 'jobsearch_field_job_expiry_date', strtotime($expired_date), true);

                    // Insert job status meta key
                    update_post_meta($post_id, 'jobsearch_field_job_status', 'approved', true);

                    // Insert job address meta key
                    $address = array();
                    if ($indeed_job->city != '') {
                        $address[] = $indeed_job->city;
                    }
                    if ($indeed_job->state != '') {
                        $address[] = $indeed_job->state;
                    }
                    if ($indeed_job->country != '') {
                        $indeed_country = $indeed_job->country;
                        $countries = JobSearch_Indeed_API::indeed_api_countries();
                        $address[] = isset($countries[strtolower($indeed_country)]) ? $countries[strtolower($indeed_country)] : '';
                    }
                    if (!empty($address)) {
                        $address = implode(', ', $address);
                        update_post_meta($post_id, 'jobsearch_field_location_address', $address, true);
                    }

                    // Insert job latitude meta key
                    update_post_meta($post_id, 'jobsearch_field_location_lat', ($indeed_job->latitude), true);

                    // Insert job longitude meta key
                    update_post_meta($post_id, 'jobsearch_field_location_lng', ($indeed_job->longitude), true);

                    // Insert job referral meta key
                    update_post_meta($post_id, 'jobsearch_job_referral', 'indeed', true);

                    // Insert job detail url meta key
                    update_post_meta($post_id, 'jobsearch_field_job_detail_url', ($job_url), true);
                    
                    update_post_meta($post_id, 'jobsearch_field_job_apply_type', 'external', true);
                    update_post_meta($post_id, 'jobsearch_field_job_apply_url', $job_url, true);

                    // Insert job comapny name meta key
                    update_post_meta($post_id, 'jobsearch_field_company_name', $job_company, true);

                    // Create and assign taxonomy to post
                    $job_type = isset($_POST['jt']) ? $_POST['jt'] : '';
                    if ($job_type) {
                        $job_type = self::get_job_type($job_type);
                        $term = get_term_by('name', $job_type, 'jobtype');
                        if ($term == '') {
                            wp_insert_term($job_type, 'jobtype');
                            $term = get_term_by('name', $job_type, 'jobtype');
                        }
                        wp_set_post_terms($post_id, $term->term_id, 'jobtype');
                    }
                }
            }
            
            if ($no_unique_jobs_found) {
                $json['type'] = 'error';
                $json['message'] = __('No new job found.', 'wp-jobsearch');
            } else {
                $json['type'] = 'success';
                $json['msg'] = sprintf(__('%s indeed jobs are imported successfully.', 'wp-jobsearch'), $count_jobs);
            }
        }
        if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_import_indeed_jobs') {
            echo json_encode($json);
            die();
        }
    }

    public function array_insert($array, $values, $offset)
    {
        return array_slice($array, 0, $offset, true) + $values + array_slice($array, $offset, NULL, true);
    }

    public function vc_shortcode_params_add($params = array())
    {

        $indeed_jobs_switch = get_option('jobsearch_integration_indeed_jobs');
        $fields_list = array();
        if ($indeed_jobs_switch == 'on') {

            $fields_list[] = 'indeed';
        }

        $fields_list = apply_filters('jobsearch_job_integrations_types_list', $fields_list);

        if (!empty($fields_list)) {

            $opts_arr = array(esc_html__("All", "wp-jobsearch") => 'all');
            foreach ($fields_list as $opt_itm) {
                $opt_itm_name = ucfirst($opt_itm) . ' Jobs';
                $opts_arr[$opt_itm_name] = $opt_itm;
            }

            $new_element = array(
                array(
                    'type' => 'dropdown',
                    'heading' => esc_html__("Job Listing Type", "wp-jobsearch"),
                    'param_name' => 'job_list_type',
                    'value' => $opts_arr,
                    'description' => esc_html__("Choose type for view jobs.", "wp-jobsearch")
                )
            );
            array_splice($params, 4, 0, $new_element);
        }

        return $params;
    }

    public function editor_shortcode_params_add($params = array())
    {
        global $jobsearch_plugin_options;
        $indeed_jobs_switch = get_option('jobsearch_integration_indeed_jobs');

        $fields_list = array();
        if ($indeed_jobs_switch == 'on') {

            $fields_list[] = 'indeed';
        }

        $fields_list = apply_filters('jobsearch_job_integrations_types_list', $fields_list);

        if (!empty($fields_list)) {

            $opts_arr = array('all' => esc_html__('All', 'wp-jobsearch'));
            foreach ($fields_list as $opt_itm) {
                $opt_itm_name = ucfirst($opt_itm) . ' Jobs';
                $opts_arr[$opt_itm] = $opt_itm_name;
            }
            $new_element = array(
                'job_list_type' => array(
                    'type' => 'select',
                    'label' => esc_html__('Job Listing Type', 'wp-jobsearch'),
                    'desc' => esc_html__('Choose type for view jobs.', 'wp-jobsearch'),
                    'options' => $opts_arr
                ),
            );
            $params = $this->array_insert($params, $new_element, 2);
        }

        return $params;
    }

    public function indeed_jobs_listing_parameters($args, $attr) {

        $indeed_jobs_switch = get_option('jobsearch_integration_indeed_jobs');

        if ($indeed_jobs_switch == 'on') {
            if (isset($attr['job_list_type']) && $attr['job_list_type'] == 'indeed') {
                $filter_arr = array(
                    'key' => 'jobsearch_job_referral',
                    'value' => 'indeed',
                    'compare' => '=',
                );
                $args['meta_query'][] = $filter_arr;
            }
        }

        return $args;
    }

}

// Class JobSearch_Indeed_Jobs_Hooks
global $JobSearch_Indeed_Jobs_Hooks_obj;
$JobSearch_Indeed_Jobs_Hooks_obj = new JobSearch_Indeed_Jobs_Hooks();
