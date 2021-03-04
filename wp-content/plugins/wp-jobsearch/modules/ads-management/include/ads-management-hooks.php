<?php
/*
  Class : Ads Management Hooks
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class JobSearch_Ads_Management_Hooks {

// hook things up
    public function __construct() {

        add_filter('redux/options/jobsearch_plugin_options/sections', array($this, 'plugin_option_fields'));

        // ads banner options add ajax
        add_action('wp_ajax_jobsearch_add_banner_ad_to_list', array($this, 'add_banner_ad_to_list'));

        // ads Group options add ajax
        add_action('wp_ajax_jobsearch_add_ad_group_to_list', array($this, 'add_ads_group_to_list'));

        // job listings vc shortcode params hook
        add_filter('jobsearch_job_listings_vcsh_params', array($this, 'vc_shortcode_params_add'), 10, 1);

        // job listings editor shortcode params hook
        add_filter('jobsearch_job_listings_sheb_params', array($this, 'editor_shortcode_params_add'), 10, 1);

        // employer listings vc shortcode params hook
        add_filter('jobsearch_employer_listings_vcsh_params', array($this, 'vc_employer_shortcode_params_add'), 10, 1);

        // employer listings editor shortcode params hook
        add_filter('jobsearch_employer_listings_sheb_params', array($this, 'editor_employer_shortcode_params_add'), 10, 1);

        // candidate listings vc shortcode params hook
        add_filter('jobsearch_candidate_listings_vcsh_params', array($this, 'vc_candidate_shortcode_params_add'), 10, 1);

        // candidate listings editor shortcode params hook
        add_filter('jobsearch_candidate_listings_sheb_params', array($this, 'editor_candidate_shortcode_params_add'), 10, 1);

        // listing random ads html
        add_action('jobsearch_random_ad_banners', array($this, 'listing_random_ads'), 10, 6);

        // ad banner shortcode
        add_shortcode('jobsearch_ad', array($this, 'ad_banner_shortcode'));

        // ad group shortcode
        add_shortcode('jobsearch_ads_group', array($this, 'ads_group_shortcode'));

        // ad click counter callback
        add_action('wp_ajax_jobsearch_ad_banner_click_counts', array($this, 'ad_banner_click_counts'));
        add_action('wp_ajax_nopriv_jobsearch_ad_banner_click_counts', array($this, 'ad_banner_click_counts'));

        // get group title by code -> filter
        add_filter('jobsearch_get_ads_group_title', array($this, 'get_ads_group_title'), 10, 1);
    }

    public function plugin_option_fields($sections) {

        $sections[] = array(
            'title' => __('Ads Settings', 'wp-jobsearch'),
            'id' => 'ads-management-settings',
            'desc' => '',
            'icon' => 'el el-bell',
            'fields' => apply_filters('jobsearch_joptions_banners_fields_array', array(
                array(
                    'id' => 'ads_management_switch',
                    'type' => 'button_set',
                    'title' => __('Ads Switch', 'wp-jobsearch'),
                    'subtitle' => __('Switch On/Off Ads.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'ad_banners_list',
                    'type' => 'jobsearch_ads_banner',
                    'title' => __('Ad Banners', 'wp-jobsearch'),
                    'subtitle' => __('Set Ad Banners here.', 'wp-jobsearch'),
                    'default_groups' => array(
                        '143433890' => array(
                            'title' => __('Top Banners', 'wp-jobsearch'),
                            'sort' => 'random',
                            'visible_ads' => '1',
                        ),
                        '243433891' => array(
                            'title' => __('Bottom Banners', 'wp-jobsearch'),
                            'sort' => 'random',
                            'visible_ads' => '1',
                        ),
                        '343433892' => array(
                            'title' => __('Side Banners', 'wp-jobsearch'),
                            'sort' => 'random',
                            'visible_ads' => '1',
                        ),
                    ),
                    'desc' => '',
                    'default' => '',
                ),
            )),
        );
        
        $filter_ads_arr = array();
        $filter_ads_arr[] = array(
            'id' => 'jobs_filter_adcode',
            'type' => 'textarea',
            'title' => __('Jobs Filter Ad Code', 'wp-jobsearch'),
            'subtitle' => __('Jobs Listing Filter Advertisement Code.', 'wp-jobsearch'),
            'desc' => '',
        );
        $filter_ads_arr[] = array(
            'id' => 'emps_filter_adcode',
            'type' => 'textarea',
            'title' => __('Employers Filter Ad Code', 'wp-jobsearch'),
            'subtitle' => __('Employers Listing Filter Advertisement Code.', 'wp-jobsearch'),
            'desc' => '',
        );
        $filter_ads_arr[] = array(
            'id' => 'cands_filter_adcode',
            'type' => 'textarea',
            'title' => __('Candidates Filter Ad Code', 'wp-jobsearch'),
            'subtitle' => __('Candidates Listing Filter Advertisement Code.', 'wp-jobsearch'),
            'desc' => '',
        );
        $sections[] = array(
            'title' => __('Filters Ads', 'wp-jobsearch'),
            'id' => 'listin-filters-adsettins',
            'desc' => '',
            'subsection' => true,
            'fields' => apply_filters('jobsearch_optns_listin_filters_ads_fields', $filter_ads_arr),
        );
        
        $job_detail_settins = array();
        $job_detail_settins[] = array(
            'id' => 'job_detail_adcode_b4_desc',
            'type' => 'textarea',
            'title' => __('Before Description', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code Before Job Description. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $job_detail_settins[] = array(
            'id' => 'job_detail_adcode_aftr_desc',
            'type' => 'textarea',
            'title' => __('After Description', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code After Job Description. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $job_detail_settins[] = array(
            'id' => 'job_detail_adcode_aftr_cusfilds',
            'type' => 'textarea',
            'required' => array(
                array('jobsearch_job_detail_views', '!=', 'view1'),
                array('jobsearch_job_detail_views', '!=', 'view2'),
            ),
            'title' => __('After Custom Fields Section', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code After Custom Fields Section. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $job_detail_settins[] = array(
            'id' => 'job_detail_adcode_b4_aply',
            'type' => 'textarea',
            'required' => array(
                array('jobsearch_job_detail_views', '!=', 'view3'),
            ),
            'title' => __('Before Apply Job Section', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code Before Apply Job Section. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $job_detail_settins[] = array(
            'id' => 'job_detail_adcode_aftr_aply',
            'type' => 'textarea',
            'title' => __('After Apply Job Section', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code After Apply Job Section. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $job_detail_settins[] = array(
            'id' => 'job_detail_adcode_aftr_map',
            'type' => 'textarea',
            'title' => __('After Google Map Section', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code After Google Map Section. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $job_detail_settins[] = array(
            'id' => 'job_detail_adcode_aftr_simjobs',
            'type' => 'textarea',
            'required' => array(
                array('jobsearch_job_detail_views', '!=', 'view2'),
                array('jobsearch_job_detail_views', '!=', 'view4'),
            ),
            'title' => __('After Similar Jobs Section', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code After Similar Jobs Section. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $sections[] = array(
            'title' => __('Job Detail Ads', 'wp-jobsearch'),
            'id' => 'job-detaill-adsettins',
            'desc' => '',
            'subsection' => true,
            'fields' => apply_filters('jobsearch_optns_job_dtail_ads_fields', $job_detail_settins),
        );
        
        $employer_details_arr = array();
        $employer_details_arr[] = array(
            'id' => 'employer_detail_adcode_b4_desc',
            'type' => 'textarea',
            'title' => __('Before Description', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code Before Employer Description. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $employer_details_arr[] = array(
            'id' => 'employer_detail_adcode_aftr_desc',
            'type' => 'textarea',
            'title' => __('After Description', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code After Employer Description. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $employer_details_arr[] = array(
            'id' => 'employer_detail_adcode_aftr_team',
            'type' => 'textarea',
            'title' => __('After Team Members', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code After Employer Team Members. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $employer_details_arr[] = array(
            'id' => 'employer_detail_adcode_b4_cntct',
            'type' => 'textarea',
            'title' => __('Before Contact Form', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code Before Employer Contact Form. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $employer_details_arr[] = array(
            'id' => 'employer_detail_adcode_b4_map',
            'type' => 'textarea',
            'title' => __('Before Google Map', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code Before Employer Google Map. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $employer_details_arr[] = array(
            'id' => 'employer_detail_adcode_aftr_map',
            'type' => 'textarea',
            'title' => __('After Google Map', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code After Employer Google Map. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $employer_details_arr[] = array(
            'id' => 'employer_detail_adcode_b4_oficpics',
            'type' => 'textarea',
            'title' => __('Before Office Photos', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code Before Employer Office Photos. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $employer_details_arr[] = array(
            'id' => 'employer_detail_adcode_aftr_oficpics',
            'type' => 'textarea',
            'title' => __('After Office Photos', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code After Employer Office Photos. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $sections[] = array(
            'title' => __('Employer Detail Ads', 'wp-jobsearch'),
            'id' => 'emplyr-detail-adsettins',
            'desc' => '',
            'subsection' => true,
            'fields' => apply_filters('jobsearch_optns_emp_dtail_ads_fields', $employer_details_arr),
        );
        
        $candidate_detail_arr = array();
        $candidate_detail_arr[] = array(
            'id' => 'candidate_detail_adcode_b4_desc',
            'type' => 'textarea',
            'title' => __('Before Description', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code Before Candidate Description. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $candidate_detail_arr[] = array(
            'id' => 'candidate_detail_adcode_aftr_desc',
            'type' => 'textarea',
            'title' => __('After Description', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code After Candidate Description. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $candidate_detail_arr[] = array(
            'id' => 'candidate_detail_adcode_b4_cntct',
            'type' => 'textarea',
            'title' => __('Before Contact Form', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code Before Candidate Contact Form. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $candidate_detail_arr[] = array(
            'id' => 'candidate_detail_adcode_aftr_cntct',
            'type' => 'textarea',
            'title' => __('After Contact Form', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code After Candidate Contact Form. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $candidate_detail_arr[] = array(
            'id' => 'candidate_detail_adcode_aftr_edu',
            'type' => 'textarea',
            'title' => __('After Education', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code After Candidate Education. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $candidate_detail_arr[] = array(
            'id' => 'candidate_detail_adcode_aftr_exp',
            'type' => 'textarea',
            'title' => __('After Experience', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code After Candidate Experience. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $candidate_detail_arr[] = array(
            'id' => 'candidate_detail_adcode_aftr_expert',
            'type' => 'textarea',
            'title' => __('After Expertise', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code After Candidate Expertise. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $candidate_detail_arr[] = array(
            'id' => 'candidate_detail_adcode_aftr_port',
            'type' => 'textarea',
            'title' => __('After Portfolio', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code After Candidate Portfolio. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $candidate_detail_arr[] = array(
            'id' => 'candidate_detail_adcode_aftr_awards',
            'type' => 'textarea',
            'title' => __('After Honors & Awards', 'wp-jobsearch'),
            'subtitle' => sprintf(__('Ad Code After Candidate Honors & Awards. Generate ad codes from %s.', 'wp-jobsearch'), '<a href="' . admin_url('admin.php?page=jobsearch_options&tab=24') . '" target="_blank">Ads Settings</a>'),
            'desc' => '',
        );
        $sections[] = array(
            'title' => __('Candidate Detail Ads', 'wp-jobsearch'),
            'id' => 'candidte-detail-adsettins',
            'desc' => '',
            'subsection' => true,
            'fields' => apply_filters('jobsearch_optns_cand_dtail_ads_fields', $candidate_detail_arr),
        );

        return $sections;
    }

    public function add_banner_ad_to_list() {
        global $jobsearch_plugin_options;
        $rand_num = rand(100000000, 999999999);
        $option_name = isset($_POST['option_name']) ? $_POST['option_name'] : '';
        $banner_title = isset($_POST['banner_title']) ? $_POST['banner_title'] : '';
        $banner_type = isset($_POST['banner_type']) ? $_POST['banner_type'] : '';
        $banner_style = isset($_POST['banner_style']) ? $_POST['banner_style'] : '';
        $banner_img = isset($_POST['banner_img']) ? $_POST['banner_img'] : '';
        $banner_img_url = isset($_POST['banner_img_url']) ? $_POST['banner_img_url'] : '';
        $banner_url_target = isset($_POST['banner_url_target']) ? $_POST['banner_url_target'] : '';
        $banner_adsense_code = isset($_POST['banner_adsense_code']) ? $_POST['banner_adsense_code'] : '';

        $option_name = "jobsearch_plugin_options[{$option_name}]";
        if ($banner_type == 'adsense') {
            $banner_type_txt = __('Adsense Code', 'wp-jobsearch');
        } else {
            $banner_type_txt = __('Image Ad', 'wp-jobsearch');
        }

        if ($banner_title == '') {
            $msg = __('Please add a banner title.', 'wp-jobsearch');
            echo json_encode(array('html' => '', 'error' => '1', 'msg' => $msg));
            die;
        }
        if ($banner_type == 'adsense') {
            $banner_counts = '-';
            if ($banner_adsense_code == '') {
                $msg = __('Please add the Adsense code.', 'wp-jobsearch');
                echo json_encode(array('html' => '', 'error' => '1', 'msg' => $msg));
                die;
            }
        } else {
            $banner_counts = '0';
            if ($banner_img == '') {
                $msg = __('Please add a banner image.', 'wp-jobsearch');
                echo json_encode(array('html' => '', 'error' => '1', 'msg' => $msg));
                die;
            }
            if ($banner_img_url == '') {
                $msg = __('Please add a banner image URL.', 'wp-jobsearch');
                echo json_encode(array('html' => '', 'error' => '1', 'msg' => $msg));
                die;
            }
        }

        $groups_value = isset($jobsearch_plugin_options['ad_banner_groups']) ? $jobsearch_plugin_options['ad_banner_groups'] : '';

        ob_start();
        ?>
        <div class="pumflit-item">
            <ul>
                <li><?php echo ($banner_title) ?></li>
                <li><?php echo ($banner_type_txt) ?></li>
                <li><?php echo apply_filters('jobsearch_get_ads_group_title', $banner_style) ?></li>
                <li><?php echo ($banner_counts) ?></li>
                <li class="pumflit-code"><?php echo ('[jobsearch_ad code="' . $rand_num . '"]') ?></li>
                <li>
                    <div class="action-btns">
                        <a href="javascript:void(0);" class="update-ad"><i class="dashicons dashicons-edit"></i></a>
                        <a href="javascript:void(0);" class="remove-ad"><i class="dashicons dashicons-trash"></i></a>
                    </div>
                </li>
            </ul>
            <div class="action-update-con">
                <div class="pumflit-banner-field-con ads-input-field">
                    <div class="field-label"><?php esc_html_e('Banner Title', 'wp-jobsearch') ?></div>
                    <div class="field-value">
                        <input type="text" name="<?php echo ($option_name) . '[banner_title][]' ?>" value="<?php echo ($banner_title) ?>">
                        <input type="hidden" name="<?php echo ($option_name) . '[banner_code][]' ?>" value="<?php echo ($rand_num) ?>">
                    </div>
                </div>
                <?php
                $groups_select_opts = array('' => esc_html__('Select Group', 'wp-jobsearch'));
                if (isset($groups_value['group_title']) && !empty($groups_value['group_title'])) {
                    $b_group_counter = 0;
                    $group_codes = isset($groups_value['group_code']) ? $groups_value['group_code'] : '';
                    foreach ($groups_value['group_title'] as $group_title) {
                        $group_code = isset($group_codes[$b_group_counter]) ? $group_codes[$b_group_counter] : '';
                        $groups_select_opts[$group_code] = $group_title;
                        $b_group_counter++;
                    }
                }
                ?>
                <div class="pumflit-banner-field-con ads-select-field">
                    <div class="field-label"><?php esc_html_e('Banner Group', 'wp-jobsearch') ?></div>
                    <div class="field-value">
                        <select name="<?php echo ($option_name) . '[banner_group][]' ?>">
                            <?php
                            foreach ($groups_select_opts as $group_opt_key => $group_opt_title) {
                                ?>
                                <option value="<?php echo ($group_opt_key) ?>"<?php echo ($banner_style == $group_opt_key ? ' selected="selected"' : '') ?>><?php echo ($group_opt_title) ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="pumflit-banner-field-con ads-select-field">
                    <div class="field-label"><?php esc_html_e('Banner Type', 'wp-jobsearch') ?></div>
                    <div class="field-value">
                        <select class="jobsearch-pumflit-type-select" name="<?php echo ($option_name) . '[banner_type][]' ?>" data-id="<?php echo ($rand_num) ?>">
                            <option value="image"<?php echo ($banner_type == 'image' ? ' selected="selected"' : '') ?>><?php esc_html_e('Image Ad', 'wp-jobsearch') ?></option>
                            <option value="adsense"<?php echo ($banner_type == 'adsense' ? ' selected="selected"' : '') ?>><?php esc_html_e('Adsense Ad', 'wp-jobsearch') ?></option>
                        </select>
                    </div>
                </div>
                <div id="pumflit-banner-img-field-<?php echo ($rand_num) ?>" class="pumflit-banner-field-con ads-input-field" style="display: <?php echo ($banner_type == 'image' ? 'block' : 'none') ?>;">
                    <div class="field-label"><?php esc_html_e('Banner Image', 'wp-jobsearch') ?></div>
                    <div class="field-value">
                        <div id="pumflit-image-<?php echo ($rand_num) ?>-box" class="jobsearch-browse-med-image" style="display: <?php echo ($banner_img != '' ? 'block' : 'none') ?>;">
                            <a class="jobsearch-rem-media-b" data-id="pumflit-image-<?php echo ($rand_num) ?>"><i class="dashicons dashicons-no-alt"></i></a>
                            <img id="pumflit-image-<?php echo ($rand_num) ?>-img" src="<?php echo ($banner_img) ?>" alt="">
                        </div>
                        <input type="hidden" id="pumflit-image-<?php echo ($rand_num) ?>" name="<?php echo ($option_name) . '[banner_image][]' ?>" value="<?php echo ($banner_img) ?>">
                        <input type="button" class="jobsearch-uplopumflit-media jobsearch-bk-btn" name="pumflit-image-<?php echo ($rand_num) ?>" value="<?php esc_html_e('Browse', 'wp-jobsearch') ?>"> 
                    </div>
                </div>
                <div id="pumflit-banner-img-url-field-<?php echo ($rand_num) ?>" class="pumflit-banner-field-con ads-input-field" style="display: <?php echo ($banner_type == 'image' ? 'block' : 'none') ?>;">
                    <div class="field-label"><?php esc_html_e('Image URL', 'wp-jobsearch') ?></div>
                    <div class="field-value">
                        <input type="text" name="<?php echo ($option_name) . '[banner_img_url][]' ?>" value="<?php echo ($banner_img_url) ?>">
                    </div>
                </div>
                <div id="pumflit-banner-url-target-field-<?php echo ($rand_num) ?>" class="pumflit-banner-field-con ads-input-field" style="display: <?php echo ($banner_type == 'image' ? 'block' : 'none') ?>;">
                    <div class="field-label"><?php esc_html_e('URL Target', 'wp-jobsearch') ?></div>
                    <div class="field-value">
                        <select name="<?php echo ($option_name) . '[banner_url_target][]' ?>">
                            <option value="blank"<?php echo ($banner_url_target == 'blank' ? ' selected="selected"' : '') ?>><?php esc_html_e('_blank', 'wp-jobsearch') ?></option>
                            <option value="self"<?php echo ($banner_url_target == 'self' ? ' selected="selected"' : '') ?>><?php esc_html_e('_self', 'wp-jobsearch') ?></option>
                        </select>
                    </div>
                </div>
                <div id="pumflit-banner-adsense-field-<?php echo ($rand_num) ?>" class="pumflit-banner-field-con ads-textarea-field" style="display: <?php echo ($banner_type == 'adsense' ? 'block' : 'none') ?>;">
                    <div class="field-label"><?php esc_html_e('Adsense Code', 'wp-jobsearch') ?></div>
                    <div class="field-value">
                        <textarea name="<?php echo ($option_name) . '[banner_adsense_code][]' ?>"><?php echo ($banner_adsense_code) ?></textarea>
                    </div>
                </div>
                <div class="pumflit-banner-field-con ads-submit-field">
                    <div class="submit-btn">
                        <a href="javascript:void(0);" class="update-the-list"><?php esc_html_e('Update Banner', 'wp-jobsearch') ?></a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $html = ob_get_clean();

        echo json_encode(array('html' => $html, 'error' => '0', 'msg' => __('Banner added to the list.', 'wp-jobsearch')));
        die;
    }

    public function add_ads_group_to_list() {
        $rand_num = rand(100000000, 999999999);
        $group_title = isset($_POST['group_title']) ? $_POST['group_title'] : '';
        $group_sort = isset($_POST['group_sort']) ? $_POST['group_sort'] : '';
        $group_visible_ads = isset($_POST['group_visible_ads']) ? $_POST['group_visible_ads'] : '';

        $option_name = "jobsearch_plugin_options[ad_banner_groups]";

        if ($group_title == '') {
            $msg = __('Please add the Group title.', 'wp-jobsearch');
            echo json_encode(array('html' => '', 'error' => '1', 'msg' => $msg));
            die;
        }

        ob_start();
        ?>
        <div class="group-item">
            <ul>
                <li><?php echo ($group_title) ?></li>
                <li><?php echo ($group_sort) ?></li>
                <li><?php echo ($group_visible_ads) ?></li>
                <li class="group-code"><?php echo ('[jobsearch_ads_group code="' . $rand_num . '"]') ?></li>
                <li>
                    <div class="action-btns">
                        <a href="javascript:void(0);" class="update-group"><i class="dashicons dashicons-edit"></i></a>
                        <a href="javascript:void(0);" class="remove-group"><i class="dashicons dashicons-trash"></i></a>
                    </div>
                </li>
            </ul>
            <div class="action-update-con">
                <div class="pumflit-banner-field-con ads-input-field">
                    <div class="field-label"><?php esc_html_e('Group Title', 'wp-jobsearch') ?></div>
                    <div class="field-value">
                        <input type="text" name="<?php echo ($option_name) . '[group_title][]' ?>" value="<?php echo ($group_title) ?>">
                        <input type="hidden" name="<?php echo ($option_name) . '[group_code][]' ?>" value="<?php echo ($rand_num) ?>">
                    </div>
                </div>
                <div class="pumflit-banner-field-con ads-select-field">
                    <div class="field-label"><?php esc_html_e('Ads Sorting', 'wp-jobsearch') ?></div>
                    <div class="field-value">
                        <select name="<?php echo ($option_name) . '[group_sort][]' ?>">
                            <option value="random"<?php echo ($group_sort == 'random' ? ' selected="selected"' : '') ?>><?php esc_html_e('Random Ads', 'wp-jobsearch') ?></option>
                            <option value="ordered"<?php echo ($group_sort == 'ordered' ? ' selected="selected"' : '') ?>><?php esc_html_e('Ordered Ads', 'wp-jobsearch') ?></option>
                        </select>
                    </div>
                </div>
                <div class="pumflit-banner-field-con ads-select-field">
                    <div class="field-label"><?php esc_html_e('Visible Ads', 'wp-jobsearch') ?></div>
                    <div class="field-value">
                        <select name="<?php echo ($option_name) . '[group_visible_ads][]' ?>">
                            <?php
                            for ($vi = 1; $vi <= 10; $vi++) {
                                ?>
                                <option value="<?php echo ($vi) ?>"<?php echo ($group_visible_ads == $vi ? ' selected="selected"' : '') ?>><?php echo ($vi) ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="pumflit-banner-field-con ads-submit-field">
                    <div class="submit-btn">
                        <a href="javascript:void(0);" class="update-the-list"><?php esc_html_e('Update Group', 'wp-jobsearch') ?></a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $html = ob_get_clean();

        echo json_encode(array('html' => $html, 'error' => '0', 'msg' => __('Group added to the list.', 'wp-jobsearch')));
        die;
    }

    public function get_ads_group_title($code = '') {

        if ($code != '') {
            global $jobsearch_plugin_options;
            $groups_value = isset($jobsearch_plugin_options['ad_banner_groups']) ? $jobsearch_plugin_options['ad_banner_groups'] : '';
            $group_titles = isset($groups_value['group_title']) ? $groups_value['group_title'] : '';
            $group_codes = isset($groups_value['group_code']) ? $groups_value['group_code'] : '';
            if (in_array($code, $group_codes)) {
                $index_key = array_search($code, $group_codes);
                $code = isset($group_titles[$index_key]) ? $group_titles[$index_key] : '';
            }
        } else {
            $code = __('No Group', 'wp-jobsearch');
        }
        return $code;
    }

    public function array_insert($array, $values, $offset) {
        return array_slice($array, 0, $offset, true) + $values + array_slice($array, $offset, NULL, true);
    }

    public function vc_shortcode_params_add($params = array()) {
        global $jobsearch_plugin_options;
        $ads_management_switch = isset($jobsearch_plugin_options['ads_management_switch']) ? $jobsearch_plugin_options['ads_management_switch'] : '';

        if ($ads_management_switch == 'on') {
            $groups_value = isset($jobsearch_plugin_options['ad_banner_groups']) ? $jobsearch_plugin_options['ad_banner_groups'] : '';

            $groups_select_opts = array(esc_html__('Select Group', 'wp-jobsearch') => '');
            if (isset($groups_value['group_title']) && !empty($groups_value['group_title'])) {
                $b_group_counter = 0;
                $group_codes = isset($groups_value['group_code']) ? $groups_value['group_code'] : '';
                foreach ($groups_value['group_title'] as $group_title) {
                    $group_code = isset($group_codes[$b_group_counter]) ? $group_codes[$b_group_counter] : '';
                    $groups_select_opts[$group_title] = $group_code;
                    $b_group_counter++;
                }
            }

            $new_element = array(
                array(
                    'type' => 'dropdown',
                    'heading' => esc_html__("Ad Banners", "wp-jobsearch"),
                    'param_name' => 'job_ad_banners',
                    'value' => array(
                        esc_html__("No", "wp-jobsearch") => 'no',
                        esc_html__("Yes", "wp-jobsearch") => 'yes',
                    ),
                    'description' => esc_html__("Show/hide ad banners in job listings.", "wp-jobsearch"),
                    'group' => esc_html__("Ad Banner Settings", "wp-jobsearch"),
                ),
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__("Ad after list count", "wp-jobsearch"),
                    'param_name' => 'job_ad_after_list',
                    'value' => '5',
                    'description' => esc_html__("Put number. After how many jobs list an ad banner will show. You can also add comma seprated numbers i.e. 2,5,7", "wp-jobsearch"),
                    'group' => esc_html__("Ad Banner Settings", "wp-jobsearch"),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => esc_html__("Repeat Banners Loop", "wp-jobsearch"),
                    'param_name' => 'job_ad_banners_rep',
                    'value' => array(
                        esc_html__("Yes", "wp-jobsearch") => 'yes',
                        esc_html__("No", "wp-jobsearch") => 'no',
                    ),
                    'description' => esc_html__("Repeat ad banner after a specific number of jobs Or display add once.", "wp-jobsearch"),
                    'group' => esc_html__("Ad Banner Settings", "wp-jobsearch"),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => esc_html__("Banners Group", "wp-jobsearch"),
                    'param_name' => 'job_ads_group',
                    'value' => $groups_select_opts,
                    'description' => esc_html__("Select Ad Banners Group.", "wp-jobsearch"),
                    'group' => esc_html__("Ad Banner Settings", "wp-jobsearch"),
                ),
            );
            array_splice($params, 4, 0, $new_element);
        }

        return $params;
    }

    public function editor_shortcode_params_add($params = array()) {
        global $jobsearch_plugin_options;
        $ads_management_switch = isset($jobsearch_plugin_options['ads_management_switch']) ? $jobsearch_plugin_options['ads_management_switch'] : '';

        if ($ads_management_switch == 'on') {
            $groups_value = isset($jobsearch_plugin_options['ad_banner_groups']) ? $jobsearch_plugin_options['ad_banner_groups'] : '';

            $groups_select_opts = array('' => esc_html__('Select Group', 'wp-jobsearch'));
            if (isset($groups_value['group_title']) && !empty($groups_value['group_title'])) {
                $b_group_counter = 0;
                $group_codes = isset($groups_value['group_code']) ? $groups_value['group_code'] : '';
                foreach ($groups_value['group_title'] as $group_title) {
                    $group_code = isset($group_codes[$b_group_counter]) ? $group_codes[$b_group_counter] : '';
                    $groups_select_opts[$group_code] = $group_title;
                    $b_group_counter++;
                }
            }
            $new_element = array(
                'job_ad_banners' => array(
                    'type' => 'select',
                    'label' => esc_html__('Ad Banners', 'wp-jobsearch'),
                    'desc' => esc_html__('Show/hide ad banners in job listings.', 'wp-jobsearch'),
                    'options' => array(
                        'no' => esc_html__('No', 'wp-jobsearch'),
                        'yes' => esc_html__('Yes', 'wp-jobsearch'),
                    )
                ),
                'job_ad_after_list' => array(
                    'type' => 'text',
                    'label' => esc_html__('Ad after list count', 'wp-jobsearch'),
                    'desc' => esc_html__('Put number. After how many jobs list an ad banner will show. You can also add comma seprated numbers i.e. 2,5,7', 'wp-jobsearch'),
                    'std' => '5',
                ),
                'job_ad_banners_rep' => array(
                    'type' => 'select',
                    'label' => esc_html__('Repeat Banners Loop', 'wp-jobsearch'),
                    'desc' => esc_html__('Repeat ad banner after a specific number of jobs Or display adds once.', 'wp-jobsearch'),
                    'options' => array(
                        'yes' => esc_html__('Yes', 'wp-jobsearch'),
                        'no' => esc_html__('No', 'wp-jobsearch'),
                    )
                ),
                'job_ads_group' => array(
                    'type' => 'select',
                    'label' => esc_html__('Banners Group', 'wp-jobsearch'),
                    'desc' => esc_html__('Select Ad Banners Group.', 'wp-jobsearch'),
                    'options' => $groups_select_opts,
                ),
            );
            $params = $this->array_insert($params, $new_element, 3);
        }

        return $params;
    }

    public function vc_employer_shortcode_params_add($params = array()) {
        global $jobsearch_plugin_options;
        $ads_management_switch = isset($jobsearch_plugin_options['ads_management_switch']) ? $jobsearch_plugin_options['ads_management_switch'] : '';

        if ($ads_management_switch == 'on') {
            $groups_value = isset($jobsearch_plugin_options['ad_banner_groups']) ? $jobsearch_plugin_options['ad_banner_groups'] : '';

            $groups_select_opts = array(esc_html__('Select Group', 'wp-jobsearch') => '');
            if (isset($groups_value['group_title']) && !empty($groups_value['group_title'])) {
                $b_group_counter = 0;
                $group_codes = isset($groups_value['group_code']) ? $groups_value['group_code'] : '';
                foreach ($groups_value['group_title'] as $group_title) {
                    $group_code = isset($group_codes[$b_group_counter]) ? $group_codes[$b_group_counter] : '';
                    $groups_select_opts[$group_title] = $group_code;
                    $b_group_counter++;
                }
            }

            $new_element = array(
                array(
                    'type' => 'dropdown',
                    'heading' => esc_html__("Ad Banners", "wp-jobsearch"),
                    'param_name' => 'employer_ad_banners',
                    'value' => array(
                        esc_html__("No", "wp-jobsearch") => 'no',
                        esc_html__("Yes", "wp-jobsearch") => 'yes',
                    ),
                    'description' => esc_html__("Show/hide ad banners in employer listings.", "wp-jobsearch"),
                    'group' => esc_html__("Ad Banner Settings", "wp-jobsearch"),
                ),
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__("Ad after list count", "wp-jobsearch"),
                    'param_name' => 'employer_ad_after_list',
                    'value' => '5',
                    'description' => esc_html__("Put number. After how many employers list an ad banner will show. You can also add comma seprated numbers i.e. 2,5,7", "wp-jobsearch"),
                    'group' => esc_html__("Ad Banner Settings", "wp-jobsearch"),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => esc_html__("Banners Group", "wp-jobsearch"),
                    'param_name' => 'employer_ads_group',
                    'value' => $groups_select_opts,
                    'description' => esc_html__("Select Ad Banners Group.", "wp-jobsearch"),
                    'group' => esc_html__("Ad Banner Settings", "wp-jobsearch"),
                ),
            );
            array_splice($params, 3, 0, $new_element);
        }

        return $params;
    }

    public function editor_employer_shortcode_params_add($params = array()) {
        global $jobsearch_plugin_options;
        $ads_management_switch = isset($jobsearch_plugin_options['ads_management_switch']) ? $jobsearch_plugin_options['ads_management_switch'] : '';

        if ($ads_management_switch == 'on') {
            $groups_value = isset($jobsearch_plugin_options['ad_banner_groups']) ? $jobsearch_plugin_options['ad_banner_groups'] : '';

            $groups_select_opts = array('' => esc_html__('Select Group', 'wp-jobsearch'));
            if (isset($groups_value['group_title']) && !empty($groups_value['group_title'])) {
                $b_group_counter = 0;
                $group_codes = isset($groups_value['group_code']) ? $groups_value['group_code'] : '';
                foreach ($groups_value['group_title'] as $group_title) {
                    $group_code = isset($group_codes[$b_group_counter]) ? $group_codes[$b_group_counter] : '';
                    $groups_select_opts[$group_code] = $group_title;
                    $b_group_counter++;
                }
            }
            $new_element = array(
                'employer_ad_banners' => array(
                    'type' => 'select',
                    'label' => esc_html__('Ad Banners', 'wp-jobsearch'),
                    'desc' => esc_html__('Show/hide ad banners in employer listings.', 'wp-jobsearch'),
                    'options' => array(
                        'no' => esc_html__('No', 'wp-jobsearch'),
                        'yes' => esc_html__('Yes', 'wp-jobsearch'),
                    )
                ),
                'employer_ad_after_list' => array(
                    'type' => 'text',
                    'label' => esc_html__('Ad after list count', 'wp-jobsearch'),
                    'desc' => esc_html__('Put number. After how many employers list an ad banner will show. You can also add comma seprated numbers i.e. 2,5,7', 'wp-jobsearch'),
                    'std' => '5',
                ),
                'employer_ads_group' => array(
                    'type' => 'select',
                    'label' => esc_html__('Banners Group', 'wp-jobsearch'),
                    'desc' => esc_html__('Select Ad Banners Group.', 'wp-jobsearch'),
                    'options' => $groups_select_opts,
                ),
            );
            $params = $this->array_insert($params, $new_element, 1);
        }

        return $params;
    }

    public function vc_candidate_shortcode_params_add($params = array()) {
        global $jobsearch_plugin_options;
        $ads_management_switch = isset($jobsearch_plugin_options['ads_management_switch']) ? $jobsearch_plugin_options['ads_management_switch'] : '';

        if ($ads_management_switch == 'on') {
            $groups_value = isset($jobsearch_plugin_options['ad_banner_groups']) ? $jobsearch_plugin_options['ad_banner_groups'] : '';

            $groups_select_opts = array(esc_html__('Select Group', 'wp-jobsearch') => '');
            if (isset($groups_value['group_title']) && !empty($groups_value['group_title'])) {
                $b_group_counter = 0;
                $group_codes = isset($groups_value['group_code']) ? $groups_value['group_code'] : '';
                foreach ($groups_value['group_title'] as $group_title) {
                    $group_code = isset($group_codes[$b_group_counter]) ? $group_codes[$b_group_counter] : '';
                    $groups_select_opts[$group_title] = $group_code;
                    $b_group_counter++;
                }
            }

            $new_element = array(
                array(
                    'type' => 'dropdown',
                    'heading' => esc_html__("Ad Banners", "wp-jobsearch"),
                    'param_name' => 'candidate_ad_banners',
                    'value' => array(
                        esc_html__("No", "wp-jobsearch") => 'no',
                        esc_html__("Yes", "wp-jobsearch") => 'yes',
                    ),
                    'description' => esc_html__("Show/hide ad banners in candidate listings.", "wp-jobsearch"),
                    'group' => esc_html__("Ad Banner Settings", "wp-jobsearch"),
                ),
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__("Ad after list count", "wp-jobsearch"),
                    'param_name' => 'candidate_ad_after_list',
                    'value' => '5',
                    'description' => esc_html__("Put number. After how many candidates list an ad banner will show. You can also add comma seprated numbers i.e. 2,5,7", "wp-jobsearch"),
                    'group' => esc_html__("Ad Banner Settings", "wp-jobsearch"),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => esc_html__("Banners Group", "wp-jobsearch"),
                    'param_name' => 'candidate_ads_group',
                    'value' => $groups_select_opts,
                    'description' => esc_html__("Select Ad Banners Group.", "wp-jobsearch"),
                    'group' => esc_html__("Ad Banner Settings", "wp-jobsearch"),
                ),
            );
            array_splice($params, 3, 0, $new_element);
        }

        return $params;
    }

    public function editor_candidate_shortcode_params_add($params = array()) {
        global $jobsearch_plugin_options;
        $ads_management_switch = isset($jobsearch_plugin_options['ads_management_switch']) ? $jobsearch_plugin_options['ads_management_switch'] : '';

        if ($ads_management_switch == 'on') {
            $groups_value = isset($jobsearch_plugin_options['ad_banner_groups']) ? $jobsearch_plugin_options['ad_banner_groups'] : '';

            $groups_select_opts = array('' => esc_html__('Select Group', 'wp-jobsearch'));
            if (isset($groups_value['group_title']) && !empty($groups_value['group_title'])) {
                $b_group_counter = 0;
                $group_codes = isset($groups_value['group_code']) ? $groups_value['group_code'] : '';
                foreach ($groups_value['group_title'] as $group_title) {
                    $group_code = isset($group_codes[$b_group_counter]) ? $group_codes[$b_group_counter] : '';
                    $groups_select_opts[$group_code] = $group_title;
                    $b_group_counter++;
                }
            }
            $new_element = array(
                'candidate_ad_banners' => array(
                    'type' => 'select',
                    'label' => esc_html__('Ad Banners', 'wp-jobsearch'),
                    'desc' => esc_html__('Show/hide ad banners in candidate listings.', 'wp-jobsearch'),
                    'options' => array(
                        'no' => esc_html__('No', 'wp-jobsearch'),
                        'yes' => esc_html__('Yes', 'wp-jobsearch'),
                    )
                ),
                'candidate_ad_after_list' => array(
                    'type' => 'text',
                    'label' => esc_html__('Ad after list count', 'wp-jobsearch'),
                    'desc' => esc_html__('Put number. After how many candidates list an ad banner will show. You can also add comma seprated numbers i.e. 2,5,7', 'wp-jobsearch'),
                    'std' => '5',
                ),
                'candidate_ads_group' => array(
                    'type' => 'select',
                    'label' => esc_html__('Banners Group', 'wp-jobsearch'),
                    'desc' => esc_html__('Select Ad Banners Group.', 'wp-jobsearch'),
                    'options' => $groups_select_opts,
                ),
            );
            $params = $this->array_insert($params, $new_element, 1);
        }

        return $params;
    }

    public function listing_random_ads($atts = array(), $listing_loop, $counter, $listing_type = 'job_listing', $wrapper_tag = 'li', $wrapper_tag_class = 'jobsearch-column-12') {
        global $jobsearch_plugin_options;
        $ads_management_switch = isset($jobsearch_plugin_options['ads_management_switch']) ? $jobsearch_plugin_options['ads_management_switch'] : '';

        if ($ads_management_switch == 'on') {

            if ($listing_type == 'employer_listing') {
                $listing_ad_switch_key = 'employer_ad_banners';
                $listing_ad_count_key = 'employer_ad_after_list';
                $listing_ads_group_key = 'employer_ads_group';
            } else if ($listing_type == 'candidate_listing') {
                $listing_ad_switch_key = 'candidate_ad_banners';
                $listing_ad_count_key = 'candidate_ad_after_list';
                $listing_ads_group_key = 'candidate_ads_group';
            } else {
                $listing_ad_switch_key = 'job_ad_banners';
                $listing_ad_count_key = 'job_ad_after_list';
                $listing_ads_group_key = 'job_ads_group';
            }

            $job_ads_switch = isset($atts[$listing_ad_switch_key]) ? $atts[$listing_ad_switch_key] : '';
            $job_ads_group = isset($atts[$listing_ads_group_key]) ? $atts[$listing_ads_group_key] : '';
            $job_ads_after_list_series = isset($atts[$listing_ad_count_key]) && $atts[$listing_ad_count_key] != '' ? $atts[$listing_ad_count_key] : '5';
            if ($job_ads_switch == 'yes') {
                if ($job_ads_after_list_series != '') {
                    $job_ads_list_array = explode(",", $job_ads_after_list_series);
                }
                $job_ads_after_list_array_count = sizeof($job_ads_list_array);
                $job_ads_after_list_flag = 0;
                $i = 0;
                $array_i = 0;
                $job_ads_after_list_array_final = '';
                while ($job_ads_after_list_array_count > $array_i) {
                    if (isset($job_ads_list_array[$array_i]) && $job_ads_list_array[$array_i] != '') {
                        $job_ads_after_list_array[$i] = $job_ads_list_array[$array_i];
                        $i ++;
                    }
                    $array_i ++;
                }
                // new count 
                $job_ads_after_list_array_count = sizeof($job_ads_after_list_array);
            }

            $jobs_ads_array = array();
            if ($job_ads_switch == 'yes' && $job_ads_after_list_array_count > 0) {
                $list_count = 0;
                for ($i = 0; $i <= $listing_loop->found_posts; $i ++) {
                    if ($list_count == $job_ads_after_list_array[$job_ads_after_list_flag]) {
                        $list_count = 1;
                        $jobs_ads_array[] = $i;
                        $job_ads_after_list_flag ++;
                        if ($job_ads_after_list_flag >= $job_ads_after_list_array_count) {
                            $job_ads_after_list_flag = $job_ads_after_list_array_count - 1;
                        }
                    } else {
                        $list_count ++;
                    }
                }
            }

            if (sizeof($jobs_ads_array) > 0 && in_array($counter, $jobs_ads_array) && $job_ads_group != '') {
                do_shortcode('[jobsearch_ads_group code="' . $job_ads_group . '" wrapper_tag="' . $wrapper_tag . '" wrapper_tag_class="' . $wrapper_tag_class . '"]');
            }
            //
        }
    }

    public function ads_group_shortcode($atts = array()) {
        extract(shortcode_atts(array(
            'code' => '',
            'wrapper_tag' => '',
            'wrapper_tag_class' => '',
                        ), $atts));
        global $jobsearch_plugin_options;

        if ($code != '') {
            $banners_value = isset($jobsearch_plugin_options['ad_banners_list']) ? $jobsearch_plugin_options['ad_banners_list'] : '';
            if (isset($banners_value['banner_group']) && !empty($banners_value['banner_group'])) {

                // Group Settings
                $group_sort = 'random';
                $group_visible_ads = '1';
                $groups_value = isset($jobsearch_plugin_options['ad_banner_groups']) ? $jobsearch_plugin_options['ad_banner_groups'] : '';
                $group_codes = isset($groups_value['group_code']) ? $groups_value['group_code'] : '';
                $group_sorts = isset($groups_value['group_sort']) ? $groups_value['group_sort'] : '';
                $group_visible_adss = isset($groups_value['group_visible_ads']) ? $groups_value['group_visible_ads'] : '';
                if (!empty($group_codes) && in_array($code, $group_codes)) {
                    $index_key = array_search($code, $group_codes);
                    $group_sort = isset($group_sorts[$index_key]) ? $group_sorts[$index_key] : 'random';
                    $group_visible_ads = isset($group_visible_adss[$index_key]) ? $group_visible_adss[$index_key] : '1';
                }
                //

                $banner_groups = $banners_value['banner_group'];
                $banner_codes = isset($banners_value['banner_code']) ? $banners_value['banner_code'] : '';

                $banner_codes_array = array();
                $b_counter = 0;
                foreach ($banner_groups as $banner_group) {
                    $banner_code = isset($banner_codes[$b_counter]) ? $banner_codes[$b_counter] : '';

                    if ($banner_group == $code) {
                        $banner_codes_array[] = $banner_code;
                    }

                    $b_counter++;
                }
                if (!empty($banner_codes_array)) {
                    if ($group_sort == 'random') {
                        shuffle($banner_codes_array);
                    }

                    $ad_limit_counter = 1;
                    foreach ($banner_codes_array as $banner_codes_arr) {
                        if ($wrapper_tag != '') {
                            echo ('<' . $wrapper_tag . ($wrapper_tag_class != '' ? ' class="' . $wrapper_tag_class . '"' : '') . '>');
                        }
                        echo do_shortcode('[jobsearch_ad code="' . $banner_codes_arr . '"]');
                        if ($wrapper_tag != '') {
                            echo ('</' . $wrapper_tag . '>');
                        }

                        if ($ad_limit_counter >= absint($group_visible_ads)) {
                            break;
                        }
                        $ad_limit_counter++;
                    }
                }
            }
        }
    }

    public function ad_banner_shortcode($atts = array()) {
        extract(shortcode_atts(array(
            'code' => '',
                        ), $atts));

        global $jobsearch_plugin_options;
        
        ob_start();
        $all_ad_banners = isset($jobsearch_plugin_options['ad_banners_list']) ? $jobsearch_plugin_options['ad_banners_list'] : '';
        if (isset($all_ad_banners['banner_code']) && is_array($all_ad_banners['banner_code'])) {
            wp_enqueue_script('jobsearch-ads-management-scripts');
            $i = 0;
            foreach ($all_ad_banners['banner_code'] as $banner_code) :
                if ($all_ad_banners['banner_code'][$i] == $code) {
                    break;
                }
                $i++;
            endforeach;

            $banner_title = isset($all_ad_banners['banner_title'][$i]) ? $all_ad_banners['banner_title'][$i] : '';
            $banner_type = isset($all_ad_banners['banner_type'][$i]) ? $all_ad_banners['banner_type'][$i] : '';
            $banner_style = isset($all_ad_banners['banner_group'][$i]) ? $all_ad_banners['banner_group'][$i] : '';
            $banner_img = isset($all_ad_banners['banner_image'][$i]) ? $all_ad_banners['banner_image'][$i] : '';
            $banner_img_url = isset($all_ad_banners['banner_img_url'][$i]) ? $all_ad_banners['banner_img_url'][$i] : '';
            $banner_url_target = isset($all_ad_banners['banner_url_target'][$i]) ? $all_ad_banners['banner_url_target'][$i] : '';
            $banner_adsense_code = isset($all_ad_banners['banner_adsense_code'][$i]) ? $all_ad_banners['banner_adsense_code'][$i] : '';
            ?>
            <div class="jobsearch-pumflit-banner-con">
                <?php
                if ($banner_type == 'adsense') {
                    echo stripslashes($banner_adsense_code);
                } else {
                    $banner_url_target = ($banner_url_target == 'blank' ? '_blank' : '_self');
                    if (!isset($_COOKIE["banner_click_" . $code])) {
                        ?>
                        <a href="<?php echo ($banner_img_url) ?>" class="jobsearch_ad_img_banner_click" data-id="<?php echo ($code) ?>" target="_blank"><img src="<?php echo ($banner_img) ?>" alt="<?php echo ($banner_title) ?>" /></a>
                            <?php
                        } else {
                            ?>
                        <a href="<?php echo ($banner_img_url) ?>" target="<?php echo ($banner_url_target) ?>"><img src="<?php echo ($banner_img) ?>" alt="<?php echo ($banner_title) ?>" /></a>
                        <?php
                    }
                }
                ?>
            </div>
            <?php
        }
        $html = ob_get_clean();
        return $html;
    }

    public function ad_banner_click_counts() {
        $code = isset($_POST['code_id']) ? $_POST['code_id'] : '';

        if ($code != '' && $code > 0) {
            $banner_counter = get_option('banner_click_' . $code);
            if (!isset($_COOKIE["banner_click_" . $code])) {
                setcookie("banner_click_" . $code, 'yes', time() + (86400 * 120), "/");
                update_option('banner_click_' . $code, (absint($banner_counter) + 1));
            }
        }
        die;
    }

}

// Class JobSearch_Ads_Management_Hooks
$JobSearch_Ads_Management_Hooks_obj = new JobSearch_Ads_Management_Hooks();
global $JobSearch_Ads_Management_Hooks_obj;
