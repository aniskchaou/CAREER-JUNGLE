<?php
if (!defined('ABSPATH')) {
    die;
}

global $empall_applicants_handle;
if (!class_exists('jobsearch_empall_applicants_handle')) {

    class jobsearch_empall_applicants_handle
    {

        // hook things up
        public function __construct()
        {
            global $empall_applicants_handle;
            add_action('wp_ajax_jobsearch_empdash_load_single_apswith_job_inlist', array($this, 'load_single_apswith_job_inlist'));
            //
            add_action('wp_ajax_jobsearch_empdash_load_all_apswith_job_posts', array($this, 'load_all_jobs_post_data'));
            add_action('wp_ajax_jobsearch_empdash_load_more_apswith_job_apps', array($this, 'load_more_apswith_job_apps'));
            add_action('wp_ajax_jobsearch_empdash_load_more_apswith_apps_lis', array($this, 'load_more_apswith_apps_lis'));
            //
            add_action('wp_ajax_jobsearch_empdash_alljobs_apps_count_loadboxes', array($this, 'alljobs_apps_count_loadboxes'));
        }

        public function all_applicants_list()
        {
            global $jobsearch_plugin_options, $sitepress;

            $page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
            $page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
            $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page');

            $email_applicants = isset($jobsearch_plugin_options['emp_dash_email_applics']) ? $jobsearch_plugin_options['emp_dash_email_applics'] : '';
            $external_applicants = isset($jobsearch_plugin_options['emp_dash_external_applics']) ? $jobsearch_plugin_options['emp_dash_external_applics'] : '';

            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $sitepress_def_lang = $sitepress->get_default_language();
                $sitepress_curr_lang = $sitepress->get_current_language();
                $sitepress->switch_lang($sitepress_def_lang, true);
            }

            $user_id = get_current_user_id();
            $user_id = apply_filters('jobsearch_in_fromdash_alljobaplics_user_id', $user_id);
            $employer_id = jobsearch_get_user_employer_id($user_id);

            if ($employer_id > 0) {
                $args = array(
                    'post_type' => 'job',
                    'posts_per_page' => 5,
                    'post_status' => array('publish', 'draft'),
                    'fields' => 'ids',
                    'order' => 'DESC',
                    'orderby' => 'ID',
                    'meta_query' => array(
                        array(
                            'key' => 'jobsearch_field_job_posted_by',
                            'value' => $employer_id,
                            'compare' => '=',
                        ),
                        array(
                            'key' => 'jobsearch_job_applicants_list',
                            'value' => '',
                            'compare' => '!=',
                        ),
                    ),
                );
                $jobs_query = new WP_Query($args);
                $totl_found_jobs = $jobs_query->found_posts;
                $jobs_posts = $jobs_query->posts;
                if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                    $sitepress->switch_lang($sitepress_curr_lang, true);
                }
                ?>

                <div class="jobsearch-typo-wrap">
                    <script>
                        jQuery(document).ready(function () {
                            jobsearch_alljobs_apps_count_load();
                        });
                    </script>
                    <div class="jobsearch-employer-box-section">
                        <div class="jobsearch-profile-title">
                            <h2><?php esc_html_e('All Applicants', 'wp-jobsearch') ?></h2>
                            <?php
                            if ($email_applicants == 'on') {
                                ?>
                                <a href="<?php echo add_query_arg(array('tab' => 'all-applicants', 'view' => 'email-applicants'), $page_url) ?>"
                                   class="applicnts-view-btn"><i
                                            class="fa fa-envelope"></i> <?php esc_html_e('Applied with Email', 'wp-jobsearch') ?>
                                </a>
                                <?php
                            }
                            if ($external_applicants == 'on') {
                                ?>
                                <a href="<?php echo add_query_arg(array('tab' => 'all-applicants', 'view' => 'external-applicants'), $page_url) ?>"
                                   class="applicnts-view-btn"><i
                                            class="fa fa-link"></i> <?php esc_html_e('External URL Applicants', 'wp-jobsearch') ?>
                                </a>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="jobsearch-allaplicants-holder" data-uid="<?php echo($user_id) ?>"
                             data-eid="<?php echo($employer_id) ?>">

                            <div class="select-appsjob-con">
                                <div class="allapps-selctcounts-holdr">
                                    <div class="allapps-job-label">
                                        <h2><?php esc_html_e('Filter by Job', 'wp-jobsearch') ?></h2></div>
                                    <div class="allapps-jobselct-con"
                                         style="display: inline-block; position: relative;">
                                        <?php
                                        $job_selcted_by = '';
                                        self::get_custom_post_field($job_selcted_by, 'job', esc_html__('Jobs', 'wp-jobsearch'), 'all_jobs_wapps_selctor');
                                        ?>
                                    </div>
                                </div>
                                <div class="overall-appcreds-con">
                                    <ul>
                                        <li>
                                            <span class="tot-apps"><?php esc_html_e('Total Applicants ', 'wp-jobsearch') ?></span>
                                            <div class="applicnt-count-box tot-apps"><a
                                                        class="overall-site-aplicnts">0</a></div>
                                        </li>
                                        <li>
                                            <span class="sh-apps"><?php esc_html_e('Shortlisted Applicants ', 'wp-jobsearch') ?></span>
                                            <div class="applicnt-count-box sh-apps"><a
                                                        class="overall-site-shaplicnts">0</a></div>
                                        </li>
                                        <li>
                                            <span class="rej-apps"><?php esc_html_e('Rejected Applicants ', 'wp-jobsearch') ?></span>
                                            <div class="applicnt-count-box rej-apps"><a
                                                        class="overall-site-rejaplicnts">0</a></div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <?php
                            if (!empty($jobs_posts)) {
                                add_action('wp_footer', function () {
                                    ?>
                                    <div class="jobsearch-modal fade" id="JobSearchModalSendEmailComm">
                                        <div class="modal-inner-area">&nbsp;</div>
                                        <div class="modal-content-area">
                                            <div class="modal-box-area">
                                                <span class="modal-close"><i class="fa fa-times"></i></span>
                                                <div class="jobsearch-send-message-form">
                                                    <form method="post" id="jobsearch_send_email_form0">
                                                        <div class="jobsearch-user-form">
                                                            <ul class="email-fields-list">
                                                                <li>
                                                                    <label>
                                                                        <?php echo esc_html__('Subject', 'wp-jobsearch'); ?>
                                                                        :
                                                                    </label>
                                                                    <div class="input-field">
                                                                        <input type="text" name="send_message_subject"
                                                                               value=""/>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <label>
                                                                        <?php echo esc_html__('Message', 'wp-jobsearch'); ?>
                                                                        :
                                                                    </label>
                                                                    <div class="input-field">
                                                                        <textarea
                                                                                name="send_message_content"></textarea>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="input-field-submit">
                                                                        <input type="submit"
                                                                               class="applicantto-email-submit-btn"
                                                                               data-jid="0" data-eid="0" data-cid="0"
                                                                               data-randid="0"
                                                                               name="send_message_content"
                                                                               value="<?php esc_html_e('Send', 'wp-jobsearch'); ?>"/>
                                                                        <span class="loader-box loader-box-0"></span>
                                                                    </div>
                                                                    <?php jobsearch_terms_and_con_link_txt(); ?>
                                                                </li>
                                                            </ul>
                                                            <div class="message-box message-box-0"
                                                                 style="display:none;"></div>
                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }, 11, 1);
                                ?>
                                <div class="jobsearch-all-aplicantslst">
                                    <?php
                                    self::load_wapp_jobs_posts($jobs_posts, $employer_id);
                                    ?>
                                </div>

                                <?php
                                if ($totl_found_jobs > 5) {
                                    $total_pages = ceil($totl_found_jobs / 5);
                                    ?>
                                    <div class="lodmore-apps-btnsec">
                                        <a href="javascript:void(0);" class="lodmore-apps-btn"
                                           data-tpages="<?php echo($total_pages) ?>"
                                           data-gtopage="2"><?php esc_html_e('Load More Jobs', 'wp-jobsearch') ?></a>
                                    </div>
                                    <?php
                                }
                            } else { ?>
                                <p><?php esc_html_e('No job found with applicants.', 'wp-jobsearch') ?></p>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <?php
            } else {
                if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                    $sitepress->switch_lang($sitepress_curr_lang, true);
                }
                ?>
                <div class="jobsearch-employer-dasboard jobsearch-typo-wrap">
                    <div class="jobsearch-employer-box-section">
                        <div class="jobsearch-profile-title">
                            <h2><?php esc_html_e('All Applicants', 'wp-jobsearch') ?></h2>
                        </div>
                        <p><?php esc_html_e('No Applicants found.', 'wp-jobsearch') ?></p>
                    </div>
                </div>
                <?php
            }
        }

        public static function get_custom_post_field($selected_id, $custom_post_slug, $field_label, $field_name, $custom_name = '')
        {
            global $jobsearch_form_fields;
            $custom_post_first_element = esc_html__('All ', 'wp-jobsearch');
            $custom_posts = array(
                '' => $custom_post_first_element . $field_label,
            );
            if ($selected_id) {
                $this_custom_posts = get_the_title($selected_id);
                $custom_posts[$selected_id] = $this_custom_posts;
            }

            $rand_num = rand(1234568, 6867867);
            $field_params = array(
                'classes' => 'job_post_cajax_field',
                'id' => 'custom_post_field_' . $rand_num,
                'name' => $field_name,
                'cus_name' => $field_name,
                'options' => $custom_posts,
                'force_std' => $selected_id,
                'ext_attr' => ' data-randid="' . $rand_num . '" data-forcestd="' . $selected_id . '" data-loaded="false" data-posttype="' . $custom_post_slug . '"',
            );
            if (isset($custom_name) && $custom_name != '') {
                $field_params['cus_name'] = $custom_name;
            }
            echo '<div class="jobsearch-profile-select">';
            $jobsearch_form_fields->select_field($field_params);
            echo '</div>';
            ?>
            <span class="jobsearch-field-loader custom_post_loader_<?php echo absint($rand_num); ?>"></span>
            <?php
        }

        public static function list_job_all_apps($_job_id, $employer_id, $apps_start = 0)
        {
            global $jobsearch_plugin_options;
            $job_applicants_list = get_post_meta($_job_id, 'jobsearch_job_applicants_list', true);
            $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
            arsort($job_applicants_list);

            if (empty($job_applicants_list)) {
                $job_applicants_list = array();
            }

            $viewed_candidates = get_post_meta($_job_id, 'jobsearch_viewed_candidates', true);
            if (empty($viewed_candidates)) {
                $viewed_candidates = array();
            }
            $viewed_candidates = jobsearch_is_post_ids_array($viewed_candidates, 'candidate');

            $job_short_int_list = get_post_meta($_job_id, '_job_short_interview_list', true);
            $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : '';
            if (empty($job_short_int_list)) {
                $job_short_int_list = array();
            }
            $job_short_int_list = jobsearch_is_post_ids_array($job_short_int_list, 'candidate');
            $job_short_int_list_c = !empty($job_short_int_list) ? count($job_short_int_list) : 0;

            $job_reject_int_list = get_post_meta($_job_id, '_job_reject_interview_list', true);
            $job_reject_int_list = $job_reject_int_list != '' ? explode(',', $job_reject_int_list) : '';
            if (empty($job_reject_int_list)) {
                $job_reject_int_list = array();
            }
            $job_reject_int_list = jobsearch_is_post_ids_array($job_reject_int_list, 'candidate');
            $job_reject_int_list_c = !empty($job_reject_int_list) ? count($job_reject_int_list) : 0;

            //
            $apps_offset = 6;
            if ($apps_start > 0) {
                $apps_start = ($apps_start - 1) * ($apps_offset);
            }
            $job_applicants_list = array_slice($job_applicants_list, $apps_start, $apps_offset);

            if (!empty($job_applicants_list)) {
                foreach ($job_applicants_list as $_candidate_id) {
                    $candidate_user_id = jobsearch_get_candidate_user_id($_candidate_id);
                    if (absint($candidate_user_id) <= 0) {
                        continue;
                    }
                    $user_def_avatar_url = jobsearch_candidate_img_url_comn($_candidate_id);

                    $candidate_jobtitle = get_post_meta($_candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                    $get_candidate_location = get_post_meta($_candidate_id, 'jobsearch_field_location_address', true);

                    $candidate_city_title = '';
                    $get_candidate_city = get_post_meta($_candidate_id, 'jobsearch_field_location_location3', true);
                    if ($get_candidate_city == '') {
                        $get_candidate_city = get_post_meta($_candidate_id, 'jobsearch_field_location_location2', true);
                    }
                    if ($get_candidate_city == '') {
                        $get_candidate_city = get_post_meta($_candidate_id, 'jobsearch_field_location_location1', true);
                    }

                    $candidate_city_tax = $get_candidate_city != '' ? get_term_by('slug', $get_candidate_city, 'job-location') : '';
                    if (is_object($candidate_city_tax)) {
                        $candidate_city_title = $candidate_city_tax->name;
                    }

                    $sectors = wp_get_post_terms($_candidate_id, 'sector');
                    $candidate_sector = isset($sectors[0]->name) ? $sectors[0]->name : '';

                    $candidate_salary = jobsearch_candidate_current_salary($_candidate_id);
                    $candidate_age = jobsearch_candidate_age($_candidate_id);

                    $candidate_phone = get_post_meta($_candidate_id, 'jobsearch_field_user_phone', true);

                    $job_cver_ltrs = get_post_meta($_job_id, 'jobsearch_job_apply_cvrs', true);

                    $send_message_form_rand = rand(100000, 999999);
                    
                    $applicant_status = jobsearch_get_applicant_status_tarr($_candidate_id, $_job_id);
                    ?>
                    <li class="jobsearch-column-12<?php echo (isset($applicant_status['status']) && $applicant_status['status'] == 'pending' ? ' applicant-status-pending' : '') ?>">
                        <script>
                            jQuery(document).on('click', '.jobsearch-modelemail-btn-<?php echo($send_message_form_rand) ?>', function () {
                                jobsearch_modal_popup_open('JobSearchModalSendEmailComm');
                                jQuery('#JobSearchModalSendEmailComm').find('form').attr('id', 'jobsearch_send_email_form<?php echo($send_message_form_rand) ?>');
                                jQuery('#JobSearchModalSendEmailComm').find('.loader-box').attr('class', 'loader-box loader-box-<?php echo($send_message_form_rand) ?>');
                                jQuery('#JobSearchModalSendEmailComm').find('.message-box').attr('class', 'message-box message-box-<?php echo($send_message_form_rand) ?>');
                                jQuery('#JobSearchModalSendEmailComm').find('.input-field-submit').find('input[type=submit]').attr('data-randid', '<?php echo($send_message_form_rand) ?>');
                                jQuery('#JobSearchModalSendEmailComm').find('.input-field-submit').find('input[type=submit]').attr('data-jid', '<?php echo($_job_id) ?>');
                                jQuery('#JobSearchModalSendEmailComm').find('.input-field-submit').find('input[type=submit]').attr('data-cid', '<?php echo($_candidate_id) ?>');
                                jQuery('#JobSearchModalSendEmailComm').find('.input-field-submit').find('input[type=submit]').attr('data-eid', '<?php echo($employer_id) ?>');
                            });
                            jQuery(document).on('click', '.jobsearch-modelcvrltr-btn-<?php echo($send_message_form_rand) ?>', function () {
                                jobsearch_modal_popup_open('JobSearchCandCovershwModal<?php echo($send_message_form_rand) ?>');
                            });
                        </script>
                        <div class="jobsearch-applied-jobs-wrap">
                            <?php
                            $cand_is_pending = false;
                            if (isset($applicant_status['status']) && $applicant_status['status'] == 'pending') {
                                $cand_is_pending = true;
                                echo jobsearch_applicant_pend_profile_review_txt();
                            }
                            ?>
                            <a class="jobsearch-applied-jobs-thumb">
                                <?php
                                if (!$cand_is_pending) {
                                    echo do_action('jobsearch_export_selection_emp', $_candidate_id, $_job_id);
                                }
                                ?>
                                <img src="<?php echo($user_def_avatar_url) ?>" alt="">
                            </a>
                            <div class="jobsearch-applied-jobs-text">
                                <div class="jobsearch-applied-jobs-left">
                                    <?php
                                    $user_apply_data = get_user_meta($candidate_user_id, 'jobsearch-user-jobs-applied-list', true);
                                    $aply_date_time = '';
                                    if (!empty($user_apply_data)) {
                                        $user_apply_key = array_search($_job_id, array_column($user_apply_data, 'post_id'));
                                        $aply_date_time = isset($user_apply_data[$user_apply_key]['date_time']) ? $user_apply_data[$user_apply_key]['date_time'] : '';
                                    }
                                    if ($candidate_jobtitle != '') {
                                        ?>
                                        <span> <?php echo($candidate_jobtitle) ?></span>
                                        <?php
                                    }

                                    if (in_array($_candidate_id, $viewed_candidates)) {
                                        ?>
                                        <small class="profile-view viewed"><?php esc_html_e('(Viewed)', 'wp-jobsearch') ?></small>
                                        <?php
                                    } else {
                                        ?>
                                        <small class="profile-view unviewed"><?php esc_html_e('(Unviewed)', 'wp-jobsearch') ?></small>
                                        <?php
                                    }
                                    echo apply_filters('jobsearch_applicants_list_before_title', '', $_candidate_id, $_job_id);
                                    ?>
                                    <h2 class="jobsearch-pst-title">
                                        <a href="<?php echo add_query_arg(array('job_id' => $_job_id, 'employer_id' => $employer_id, 'action' => 'preview_profile'), get_permalink($_candidate_id)) ?>"><?php echo get_the_title($_candidate_id) ?></a>
                                        <?php
                                        if ($candidate_age != '') {
                                            ?>
                                            <small><?php echo apply_filters('jobsearch_dash_applicants_age_html', sprintf(esc_html__('(Age: %s years)', 'wp-jobsearch'), $candidate_age)) ?></small>
                                            <?php
                                        }
                                        if ($candidate_phone != '') {
                                            ?>
                                            <small><?php printf(esc_html__('Phone: %s', 'wp-jobsearch'), $candidate_phone) ?></small>
                                            <?php
                                        }
                                        ?>
                                    </h2>
                                    <ul>
                                        <?php
                                        if ($aply_date_time > 0) {
                                            ?>
                                            <li>
                                                <i class="jobsearch-icon jobsearch-calendar"></i> <?php printf(esc_html__('Applied at: %s', 'wp-jobsearch'), (date_i18n(get_option('date_format'), $aply_date_time) . ' ' . date_i18n(get_option('time_format'), $aply_date_time))) ?>
                                            </li>
                                            <?php
                                        }
                                        if ($candidate_salary != '') {
                                            ?>
                                            <li>
                                                <i class="fa fa-money"></i> <?php printf(esc_html__('Salary: %s', 'wp-jobsearch'), $candidate_salary) ?>
                                            </li>
                                            <?php
                                        }
                                        if ($candidate_city_title != '') {
                                            ?>
                                            <li><i class="fa fa-map-marker"></i> <?php echo($candidate_city_title) ?>
                                            </li>
                                            <?php
                                        }
                                        if ($candidate_sector != '') {
                                            ?>
                                            <li><i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i>
                                                <a><?php echo($candidate_sector) ?></a></li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <div class="jobsearch-applied-job-btns">
                                    <?php
                                    echo apply_filters('employer_dash_apps_acts_listul_after', '', $_candidate_id, $_job_id);
                                    ?>
                                    <ul>
                                        <li>
                                            <a href="<?php echo add_query_arg(array('job_id' => $_job_id, 'employer_id' => $employer_id, 'action' => 'preview_profile'), get_permalink($_candidate_id)) ?>"
                                               class="preview-candidate-profile"><i
                                                        class="fa fa-eye"></i> <?php esc_html_e('Preview', 'wp-jobsearch') ?>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="candidate-more-acts-con">
                                                <a href="javascript:void(0);"
                                                   class="more-actions"><?php esc_html_e('Actions', 'wp-jobsearch') ?>
                                                    <i class="fa fa-angle-down"></i></a>
                                                <ul>
                                                    <?php
                                                    $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
                                                    $candidate_cv_file = get_post_meta($_candidate_id, 'candidate_cv_file', true);

                                                    if ($multiple_cv_files_allow == 'on') {
                                                        $ca_at_cv_files = get_post_meta($_candidate_id, 'candidate_cv_files', true);
                                                        if (!empty($ca_at_cv_files)) {
                                                            ?>
                                                            <li>
                                                                <a href="<?php echo apply_filters('jobsearch_user_attach_cv_file_url', '', $_candidate_id, $_job_id) ?>"
                                                                   oncontextmenu="javascript: return false;"
                                                                   onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                                                   download="<?php echo apply_filters('jobsearch_user_attach_cv_file_title', '', $_candidate_id, $_job_id) ?>"><?php esc_html_e('Download CV', 'wp-jobsearch') ?></a>
                                                            </li>
                                                            <?php
                                                        }
                                                    } else if (!empty($candidate_cv_file)) {
                                                        $file_attach_id = isset($candidate_cv_file['file_id']) ? $candidate_cv_file['file_id'] : '';
                                                        $file_url = isset($candidate_cv_file['file_url']) ? $candidate_cv_file['file_url'] : '';

                                                        $filename = isset($candidate_cv_file['file_name']) ? $candidate_cv_file['file_name'] : '';

                                                        $file_url = apply_filters('wp_jobsearch_user_cvfile_downlod_url', $file_url, $file_attach_id, $_candidate_id);
                                                        ?>
                                                        <li><a href="<?php echo($file_url) ?>"
                                                               oncontextmenu="javascript: return false;"
                                                               onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                                               download="<?php echo($filename) ?>"><?php esc_html_e('Download CV', 'wp-jobsearch') ?></a>
                                                        </li>
                                                        <?php
                                                    }
                                                    echo apply_filters('employer_dash_apps_acts_list_after_download_link', '', $_candidate_id, $_job_id);

                                                    //
                                                    if (isset($job_cver_ltrs[$_candidate_id]) && $job_cver_ltrs[$_candidate_id] != '') {
                                                        ?>
                                                        <li><a href="javascript:void(0);"
                                                               class="jobsearch-modelcvrltr-btn-<?php echo($send_message_form_rand) ?>"><?php esc_html_e('View Cover Letter', 'wp-jobsearch') ?></a>
                                                        </li>
                                                        <?php
                                                    }
                                                    ?>
                                                    <li>
                                                        <a href="javascript:void(0);"
                                                           class="jobsearch-modelemail-btn-<?php echo($send_message_form_rand) ?>"><?php esc_html_e('Email to Candidate', 'wp-jobsearch') ?></a>
                                                        <?php
                                                        $popup_args = array('p_job_id' => $_job_id, 'cand_id' => $_candidate_id, 'p_emp_id' => $employer_id, 'p_masg_rand' => $send_message_form_rand);
                                                        add_action('wp_footer', function () use ($popup_args) {

                                                            extract(shortcode_atts(array(
                                                                'p_job_id' => '',
                                                                'p_emp_id' => '',
                                                                'cand_id' => '',
                                                                'p_masg_rand' => ''
                                                            ), $popup_args));
                                                            ?>
                                                            <div class="jobsearch-modal fade"
                                                                 id="JobSearchModalSendEmail<?php echo($p_masg_rand) ?>">
                                                                <div class="modal-inner-area">&nbsp;</div>
                                                                <div class="modal-content-area">
                                                                    <div class="modal-box-area">
                                                                        <span class="modal-close"><i
                                                                                    class="fa fa-times"></i></span>
                                                                        <div class="jobsearch-send-message-form">
                                                                            <form method="post"
                                                                                  id="jobsearch_send_email_form<?php echo esc_html($p_masg_rand); ?>">
                                                                                <div class="jobsearch-user-form">
                                                                                    <ul class="email-fields-list">
                                                                                        <li>
                                                                                            <label>
                                                                                                <?php echo esc_html__('Subject', 'wp-jobsearch'); ?>
                                                                                                :
                                                                                            </label>
                                                                                            <div class="input-field">
                                                                                                <input type="text"
                                                                                                       name="send_message_subject"
                                                                                                       value=""/>
                                                                                            </div>
                                                                                        </li>
                                                                                        <li>
                                                                                            <label>
                                                                                                <?php echo esc_html__('Message', 'wp-jobsearch'); ?>
                                                                                                :
                                                                                            </label>
                                                                                            <div class="input-field">
                                                                                                <textarea
                                                                                                        name="send_message_content"></textarea>
                                                                                            </div>
                                                                                        </li>
                                                                                        <li>
                                                                                            <div class="input-field-submit">
                                                                                                <input type="submit"
                                                                                                       class="applicantto-email-submit-btn"
                                                                                                       data-jid="<?php echo absint($p_job_id); ?>"
                                                                                                       data-eid="<?php echo absint($p_emp_id); ?>"
                                                                                                       data-cid="<?php echo absint($cand_id); ?>"
                                                                                                       data-randid="<?php echo esc_html($p_masg_rand); ?>"
                                                                                                       name="send_message_content"
                                                                                                       value="<?php echo esc_html__('Send', 'wp-jobsearch') ?>"/>
                                                                                                <span class="loader-box loader-box-<?php echo esc_html($p_masg_rand); ?>"></span>
                                                                                            </div>
                                                                                            <?php jobsearch_terms_and_con_link_txt(); ?>
                                                                                        </li>
                                                                                    </ul>
                                                                                    <div class="message-box message-box-<?php echo esc_html($p_masg_rand); ?>"
                                                                                         style="display:none;"></div>
                                                                                </div>
                                                                            </form>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }, 11, 1);
                                                        ?>
                                                    </li>
                                                    <?php if (in_array($_candidate_id, $job_reject_int_list)) { ?>
                                                        <li>
                                                            <a href="javascript:void(0);"
                                                               class="undoreject-cand-to-list ajax-enable"
                                                               data-jid="<?php echo absint($_job_id); ?>"
                                                               data-cid="<?php echo absint($_candidate_id); ?>"><?php esc_html_e('Undo Reject', 'wp-jobsearch') ?>
                                                                <span class="app-loader"></span></a>
                                                        </li>
                                                    <?php } else { ?>
                                                        <li>
                                                            <?php if (in_array($_candidate_id, $job_short_int_list)) { ?>
                                                                <a href="javascript:void(0);"
                                                                   class="shortlist-cand-to-intrview"><?php esc_html_e('Shortlisted', 'wp-jobsearch') ?></a>
                                                            <?php } else { ?>
                                                                <a href="javascript:void(0);"
                                                                   class="shortlist-cand-to-intrview ajax-enable"
                                                                   data-jid="<?php echo absint($_job_id); ?>"
                                                                   data-cid="<?php echo absint($_candidate_id); ?>"><?php esc_html_e('Shortlist for Interview', 'wp-jobsearch') ?>
                                                                    <span class="app-loader"></span></a>
                                                            <?php } ?>
                                                        </li>
                                                        <li>
                                                            <?php
                                                            if (in_array($_candidate_id, $job_reject_int_list)) {
                                                                ?>
                                                                <a href="javascript:void(0);"
                                                                   class="reject-cand-to-intrview"><?php esc_html_e('Rejected', 'wp-jobsearch') ?></a>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <a href="javascript:void(0);"
                                                                   class="reject-cand-to-intrview ajax-enable"
                                                                   data-jid="<?php echo absint($_job_id); ?>"
                                                                   data-cid="<?php echo absint($_candidate_id); ?>"><?php esc_html_e('Reject', 'wp-jobsearch') ?>
                                                                    <span class="app-loader"></span></a>
                                                                <?php
                                                            }
                                                            ?>
                                                        </li>
                                                        <?php
                                                    }
                                                    ?>
                                                    <li>
                                                        <a href="javascript:void(0);"
                                                           class="delete-cand-from-job ajax-enable"
                                                           data-jid="<?php echo absint($_job_id); ?>"
                                                           data-cid="<?php echo absint($_candidate_id); ?>"><?php esc_html_e('Delete', 'wp-jobsearch') ?>
                                                            <span class="app-loader"></span></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php
                    $popup_args = array(
                        'job_id' => $_job_id,
                        'rand_num' => $send_message_form_rand,
                        'candidate_id' => $_candidate_id,
                    );
                    add_action('wp_footer', function () use ($popup_args) {

                        global $jobsearch_plugin_options;

                        extract(shortcode_atts(array(
                            'job_id' => '',
                            'rand_num' => '',
                            'candidate_id' => '',
                        ), $popup_args));

                        $job_cver_ltrs = get_post_meta($job_id, 'jobsearch_job_apply_cvrs', true);
                        if (isset($job_cver_ltrs[$candidate_id]) && $job_cver_ltrs[$candidate_id] != '') {
                            ?>
                            <div class="jobsearch-modal jobsearch-typo-wrap jobsearch-candcover-popup fade"
                                 id="JobSearchCandCovershwModal<?php echo($rand_num) ?>">
                                <div class="modal-inner-area">&nbsp;</div>
                                <div class="modal-content-area">
                                    <div class="modal-box-area">
                                        <div class="jobsearch-modal-title-box">
                                            <h2><?php esc_html_e('Cover Letter', 'wp-jobsearch') ?></h2>
                                            <span class="modal-close"><i class="fa fa-times"></i></span>
                                        </div>
                                        <p><?php echo($job_cver_ltrs[$candidate_id]) ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }, 11, 1);
                }
            }
        }

        public static function load_wapp_jobs_posts($jobs_posts, $employer_id)
        {
            if (!empty($jobs_posts)) {
                foreach ($jobs_posts as $_job_id) {
                    $job_applicants_list = get_post_meta($_job_id, 'jobsearch_job_applicants_list', true);
                    $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');

                    if (empty($job_applicants_list)) {
                        $job_applicants_list = array();
                    }
                    $job_applicants_count = !empty($job_applicants_list) ? count($job_applicants_list) : 0;
                    //
                    $job_short_int_list = get_post_meta($_job_id, '_job_short_interview_list', true);
                    $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : '';
                    if (empty($job_short_int_list)) {
                        $job_short_int_list = array();
                    }
                    $job_short_int_list = jobsearch_is_post_ids_array($job_short_int_list, 'candidate');
                    $job_short_int_list_c = !empty($job_short_int_list) ? count($job_short_int_list) : 0;


                    $job_reject_int_list = get_post_meta($_job_id, '_job_reject_interview_list', true);
                    $job_reject_int_list = $job_reject_int_list != '' ? explode(',', $job_reject_int_list) : '';
                    if (empty($job_reject_int_list)) {
                        $job_reject_int_list = array();
                    }
                    $job_reject_int_list = jobsearch_is_post_ids_array($job_reject_int_list, 'candidate');
                    $job_reject_int_list_c = !empty($job_reject_int_list) ? count($job_reject_int_list) : 0;
                    //
                    ?>
                    <div class="sjob-aplicants-list sjob-aplicants-<?php echo($_job_id) ?>">

                        <div class="thjob-title">
                            <h2><?php echo get_the_title($_job_id) ?></h2>
                            <div class="total-appcreds-con total-aplicnt-cta-<?php echo($_job_id) ?>">
                                <ul>

                                    <li>
                                        <div class="applicnt-count-box tot-apps">
                                            <span><?php esc_html_e('Total Applicants: ', 'wp-jobsearch') ?></span> <?php echo absint($job_applicants_count) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="applicnt-count-box sh-apps">
                                            <span><?php esc_html_e('Shortlisted Applicants: ', 'wp-jobsearch') ?></span> <?php echo absint($job_short_int_list_c) ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="applicnt-count-box rej-apps">
                                            <span><?php esc_html_e('Rejected Applicants: ', 'wp-jobsearch') ?></span> <?php echo absint($job_reject_int_list_c) ?>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php echo do_action('jobsearch_export_select_all_emp', $_job_id) ?>
                        <?php echo do_action('jobsearch_emp_export_btns_list', $_job_id); ?>
                        <div class="jobsearch-applied-jobs">
                            <?php
                            if (!empty($job_applicants_list)) {
                                ?>
                                <ul id="job-apps-list<?php echo($_job_id) ?>" class="jobsearch-row">
                                    <?php
                                    self::list_job_all_apps($_job_id, $employer_id);
                                    ?>
                                </ul>
                                <?php
                                if ($job_applicants_count > 6) {
                                    $total_apps_pages = ceil($job_applicants_count / 6);
                                    ?>
                                    <div class="lodmore-jobapps-btnsec">
                                        <a href="javascript:void(0);" class="lodmore-jobapps-btn"
                                           data-jid="<?php echo($_job_id) ?>"
                                           data-tpages="<?php echo($total_apps_pages) ?>"
                                           data-gtopage="2"><?php esc_html_e('Load More Applicants', 'wp-jobsearch') ?></a>
                                    </div>
                                    <?php
                                }
                            } else {
                                ?>
                                <p><?php esc_html_e('No applicant found.', 'wp-jobsearch') ?></p>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
            }
        }

        public function load_all_jobs_post_data()
        {
            global $sitepress;
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $sitepress_def_lang = $sitepress->get_default_language();
                $sitepress_curr_lang = $sitepress->get_current_language();
                $sitepress->switch_lang($sitepress_def_lang, true);
            }
            
            $force_std = $_POST['force_std'];
            $posttype = $_POST['posttype'];

            $employer_id = isset($_POST['emp_id']) ? $_POST['emp_id'] : 0;
            $employer_id = absint($employer_id);

            $args = array(
                'posts_per_page' => "-1",
                'post_type' => $posttype,
                'post_status' => array('publish', 'draft'),
                'fields' => 'ids',
                'order' => 'DESC',
                'orderby' => 'ID',
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_job_posted_by',
                        'value' => $employer_id,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'jobsearch_job_applicants_list',
                        'value' => '',
                        'compare' => '!=',
                    ),
                ),
            );

            $custom_query = new WP_Query($args);
            $all_records = $custom_query->posts;
            
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $sitepress->switch_lang($sitepress_curr_lang, true);
            }

            $html = "<option value=\"\">" . esc_html__('Please select job', 'wp-jobsearch') . "</option>" . "\n";
            if (isset($all_records) && !empty($all_records)) {
                foreach ($all_records as $user_var) {
                    $selected = $user_var == $force_std ? ' selected="selected"' : '';
                    $post_title = get_the_title($user_var);
                    $html .= "<option{$selected} value=\"{$user_var}\">{$post_title}</option>" . "\n";
                }
            }
            echo json_encode(array('html' => $html));

            wp_die();
        }

        public function load_more_apswith_job_apps()
        {
            global $sitepress;
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $sitepress_def_lang = $sitepress->get_default_language();
                $sitepress_curr_lang = $sitepress->get_current_language();
                $sitepress->switch_lang($sitepress_def_lang, true);
            }
            $page_num = $_POST['page_num'];
            $employer_id = isset($_POST['emp_id']) ? $_POST['emp_id'] : 0;
            $employer_id = absint($employer_id);

            $args = array(
                'post_type' => 'job',
                'posts_per_page' => 5,
                'paged' => $page_num,
                'post_status' => array('publish', 'draft'),
                'fields' => 'ids',
                'order' => 'DESC',
                'orderby' => 'ID',
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_job_posted_by',
                        'value' => $employer_id,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'jobsearch_job_applicants_list',
                        'value' => '',
                        'compare' => '!=',
                    ),
                ),
            );
            $jobs_query = new WP_Query($args);
            $jobs_posts = $jobs_query->posts;
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $sitepress->switch_lang($sitepress_curr_lang, true);
            }

            ob_start();
            self::load_wapp_jobs_posts($jobs_posts, $employer_id);
            $html = ob_get_clean();
            echo json_encode(array('html' => $html));

            wp_die();
        }

        public function load_more_apswith_apps_lis()
        {
            $page_num = absint($_POST['page_num']);
            $_job_id = absint($_POST['_job_id']);

            $employer_id = isset($_POST['emp_id']) ? $_POST['emp_id'] : 0;
            $employer_id = absint($employer_id);

            ob_start();
            self::list_job_all_apps($_job_id, $employer_id, $page_num);
            $html = ob_get_clean();
            echo json_encode(array('html' => $html));

            wp_die();
        }

        public function load_single_apswith_job_inlist()
        {

            $_job_id = absint($_POST['_job_id']);
            $jobs_posts = array($_job_id);
            ob_start();
            self::load_wapp_jobs_posts($jobs_posts, $employer_id);
            $html = ob_get_clean();
            echo json_encode(array('html' => $html));

            wp_die();
        }

        public function alljobs_apps_count_loadboxes()
        {
            global $sitepress;

            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $sitepress_def_lang = $sitepress->get_default_language();
                $sitepress_curr_lang = $sitepress->get_current_language();
                $sitepress->switch_lang($sitepress_def_lang, true);
            }
            
            $employer_id = isset($_POST['emp_id']) ? $_POST['emp_id'] : 0;
            $employer_id = absint($employer_id);

            $appcounts = $shappcounts = $rejappcounts = 0;

            $args = array(
                'post_type' => 'job',
                'posts_per_page' => -1,
                'post_status' => array('publish', 'draft'),
                'fields' => 'ids',
                'order' => 'DESC',
                'orderby' => 'ID',
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_job_posted_by',
                        'value' => $employer_id,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'jobsearch_job_applicants_list',
                        'value' => '',
                        'compare' => '!=',
                    ),
                ),
            );
            $jobs_query = new WP_Query($args);
            $jobs_posts = $jobs_query->posts;
            
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $sitepress->switch_lang($sitepress_curr_lang, true);
            }

            if (!empty($jobs_posts)) {
                foreach ($jobs_posts as $_job_id) {
                    $job_applicants_list = get_post_meta($_job_id, 'jobsearch_job_applicants_list', true);
                    $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
                    if (empty($job_applicants_list)) {
                        $job_applicants_list = array();
                    }

                    $job_applicants_count = !empty($job_applicants_list) ? count($job_applicants_list) : 0;
                    $appcounts += $job_applicants_count;

                    //
                    $job_short_int_list = get_post_meta($_job_id, '_job_short_interview_list', true);
                    $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : '';
                    if (empty($job_short_int_list)) {
                        $job_short_int_list = array();
                    }
                    $job_short_int_list = jobsearch_is_post_ids_array($job_short_int_list, 'candidate');
                    $job_short_int_list_c = !empty($job_short_int_list) ? count($job_short_int_list) : 0;
                    $shappcounts += $job_short_int_list_c;

                    $job_reject_int_list = get_post_meta($_job_id, '_job_reject_interview_list', true);
                    $job_reject_int_list = $job_reject_int_list != '' ? explode(',', $job_reject_int_list) : '';
                    if (empty($job_reject_int_list)) {
                        $job_reject_int_list = array();
                    }
                    $job_reject_int_list = jobsearch_is_post_ids_array($job_reject_int_list, 'candidate');
                    $job_reject_int_list_c = !empty($job_reject_int_list) ? count($job_reject_int_list) : 0;
                    $rejappcounts += $job_reject_int_list_c;
                    //
                }
            }

            echo json_encode(array('appcounts' => $appcounts, 'shappcounts' => $shappcounts, 'rejappcounts' => $rejappcounts));

            wp_die();
        }

    }

    $empall_applicants_handle = new jobsearch_empall_applicants_handle();
}
