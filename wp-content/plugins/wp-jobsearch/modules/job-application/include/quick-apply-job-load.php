<?php
/**
 * Directory Plus JobApplicationLoads Module
 */
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Jobsearch_QuickJobApplyLoad')) {

    class Jobsearch_QuickJobApplyLoad
    {
        public function __construct()
        {
            add_filter('jobsearch_job_applications_forms', array($this, 'jobsearch_job_applications_forms_callback'), 11, 2);

        }

        public static function jobsearch_quick_apply_job_detail_content($job_id)
        {
            global $jobsearch_plugin_options, $jobsearch_job_application_load;

            $plugin_default_view = isset($jobsearch_plugin_options['jobsearch-default-page-view']) ? $jobsearch_plugin_options['jobsearch-default-page-view'] : 'full';

            $location_map_type = isset($jobsearch_plugin_options['location_map_type']) ? $jobsearch_plugin_options['location_map_type'] : '';

            $job_det_full_address_switch = true;
            $locations_view_type = isset($jobsearch_plugin_options['job_det_loc_listing']) ? $jobsearch_plugin_options['job_det_loc_listing'] : '';

            $loc_view_country = $loc_view_state = $loc_view_city = false;
            if (!empty($locations_view_type)) {
                if (is_array($locations_view_type) && in_array('country', $locations_view_type)) {
                    $loc_view_country = true;

                }
                if (is_array($locations_view_type) && in_array('state', $locations_view_type)) {
                    $loc_view_state = true;
                }
                if (is_array($locations_view_type) && in_array('city', $locations_view_type)) {
                    $loc_view_city = true;
                }
            }

            $plugin_default_view_with_str = '';
            if ($plugin_default_view == 'boxed') {

                $plugin_default_view_with_str = isset($jobsearch_plugin_options['jobsearch-boxed-view-width']) && $jobsearch_plugin_options['jobsearch-boxed-view-width'] != '' ? $jobsearch_plugin_options['jobsearch-boxed-view-width'] : '1140px';
                if ($plugin_default_view_with_str != '' && !wp_is_mobile()) {
                    $plugin_default_view_with_str = ' style="width:' . $plugin_default_view_with_str . '"';
                }
            }

            $job_employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true); // get job employer
            wp_enqueue_script('jobsearch-job-functions-script');
            $employer_cover_image_src_style_str = '';
            if ($job_employer_id != '') {
                if (class_exists('JobSearchMultiPostThumbnails')) {
                    $employer_cover_image_src = JobSearchMultiPostThumbnails::get_post_thumbnail_url('employer', 'cover-image', $job_employer_id);
                    if ($employer_cover_image_src != '') {
                        $employer_cover_image_src_style_str = ' style="background:url(' . esc_url($employer_cover_image_src) . ')"';
                    }
                }
            }
            if ($employer_cover_image_src_style_str == '') {
                $emp_def_cvrimg = isset($jobsearch_plugin_options['emp_default_coverimg']['url']) && $jobsearch_plugin_options['emp_default_coverimg']['url'] != '' ? $jobsearch_plugin_options['emp_default_coverimg']['url'] : '';
                $employer_cover_image_src_style_str = ' style="background:url(' . esc_url($emp_def_cvrimg) . ');"';
            }
//

            $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
            $job_views_publish_date = isset($jobsearch_plugin_options['job_views_publish_date']) ? $jobsearch_plugin_options['job_views_publish_date'] : '';

            ?>

            <!-- Main Section -->
            <div <?php echo($plugin_default_view_with_str); ?>>
                <div class="jobsearch-typo-wrap">
                    <?php
                    $post_id = $job_id;
                    $rand_num = rand(1000000, 99999999);
                    $job_apply_type = get_post_meta($post_id, 'jobsearch_field_job_apply_type', true);
                    $locations_lat = get_post_meta($post_id, 'jobsearch_field_location_lat', true);
                    $locations_lng = get_post_meta($post_id, 'jobsearch_field_location_lng', true);
                    $post_thumbnail_id = jobsearch_job_get_profile_image($post_id);
                    $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'jobsearch-job-medium');
                    $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
                    $post_thumbnail_src = $post_thumbnail_src == '' ? jobsearch_no_image_placeholder() : $post_thumbnail_src;
                    $post_thumbnail_src = apply_filters('jobsearch_jobemp_image_src', $post_thumbnail_src, $job_id);
                    $application_deadline = get_post_meta($post_id, 'jobsearch_field_job_application_deadline_date', true);
                    $jobsearch_job_posted = get_post_meta($post_id, 'jobsearch_field_job_publish_date', true);
                    $jobsearch_job_posted_ago = jobsearch_time_elapsed_string($jobsearch_job_posted, ' ' . esc_html__('posted', 'wp-jobsearch') . ' ');
                    $jobsearch_job_posted_formated = '';
                    if ($jobsearch_job_posted != '') {
                        $jobsearch_job_posted_formated = date_i18n(get_option('date_format'), ($jobsearch_job_posted));
                    }

                    $get_job_location = get_post_meta($post_id, 'jobsearch_field_location_address', true);
                    $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

                    $job_city_title = '';
                    if (function_exists('jobsearch_post_city_contry_txtstr')) {
                        $job_city_title = jobsearch_post_city_contry_txtstr($post_id, $loc_view_country, $loc_view_state, $loc_view_city, $job_det_full_address_switch);
                    }

                    if ($job_city_title != '') {
                        $get_job_location = $job_city_title;
                    }
                    $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';
                    $job_date = get_post_meta($post_id, 'jobsearch_field_job_date', true);
                    $job_views_count = get_post_meta($post_id, 'jobsearch_job_views_count', true);
                    $job_type_str = jobsearch_job_get_all_jobtypes($post_id, 'jobsearch-jobdetail-type', '', '', '<small>', '</small>');
                    $sector_str = jobsearch_job_get_all_sectors($post_id, '', ' ' . esc_html__('in', 'wp-jobsearch') . ' ', '', '<small class="post-in-category">', '</small>');
                    $company_name = jobsearch_job_get_company_name($post_id, '');
                    $skills_list = jobsearch_job_get_all_skills($post_id);
                    $job_obj = get_post($post_id);
                    $job_content = isset($job_obj->post_content) ? $job_obj->post_content : '';
                    $job_content = apply_filters('the_content', $job_content);
                    $job_salary = jobsearch_job_offered_salary($post_id);
                    $job_applicants_list = get_post_meta($post_id, 'jobsearch_job_applicants_list', true);
                    $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
                    $job_field_user = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
                    if (empty($job_applicants_list)) {
                        $job_applicants_list = array();
                    }
                    $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
                    $job_applicants_count = !empty($job_applicants_list) ? count($job_applicants_list) : 0;
                    ?>
                    <div class="jobsearch-quick-apply-image">
                        <figure class="jobsearch-jobdetail-list">
                            <?php
                            if ($post_thumbnail_src != '') { ?>
                                <span class="jobsearch-jobdetail-listthumb">
                                        <?php jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'job_listv1') ?>
                                        <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">
                                    <?php
                                    if ($jobsearch_job_featured == 'on') {
                                        $featured_class = !class_exists('Careerfy_framework') ? 'promotepof-badge' : 'careerfy-jobli-medium3';
                                        ?>
                                        <span class="<?php echo $featured_class ?>"><i class="fa fa-star"></i></span>
                                    <?php } ?>
                                    </span>
                            <?php } ?>

                            <figcaption>
                                <h3><?php echo force_balance_tags(get_the_title($post_id)); ?></h3>
                                <?php
                                ob_start();
                                ?>
                                <span>
                                        <?php
                                        if ($job_type_str != '') {
                                            echo force_balance_tags($job_type_str);
                                        }
                                        if ($company_name != '') {
                                            ob_start();
                                            echo force_balance_tags($company_name);
                                            $comp_name_html = ob_get_clean();
                                            echo apply_filters('jobsearch_empname_in_jobdetail', $comp_name_html, $job_id, 'view1');
                                        }

                                        if ($sectors_enable_switch == 'on') {
                                            echo apply_filters('jobsearch_jobdetail_sector_str_html', $sector_str, $job_id);
                                        }
                                        ?>
                                    </span>
                                <ul class="jobsearch-jobdetail-options">
                                    <?php
                                    if ((!empty($get_job_location) || ($locations_lat != '' && $locations_lng != '')) && $all_location_allow == 'on') {
                                        $view_map_loc = urlencode($get_job_location);
                                        if ($locations_lat != '' && $locations_lng != '') {
                                            $view_map_loc = urlencode($locations_lat . ',' . $locations_lng);
                                        }
                                        $google_mapurl = 'https://www.google.com/maps/search/' . $view_map_loc;
                                        ?>
                                        <li>
                                            <?php if (!empty($get_job_location)) { ?>
                                                <i class="fa fa-map-marker"></i> <?php echo esc_html($get_job_location); ?>
                                            <?php } ?>
                                            <a href="<?php echo($google_mapurl); ?>" target="_blank"
                                               class="jobsearch-jobdetail-view"><?php echo esc_html__('View on Map', 'wp-jobsearch') ?></a>
                                        </li>
                                        <?php
                                    }
                                    if ($jobsearch_job_posted_formated != '' && $job_views_publish_date == 'on') { ?>
                                        <li>
                                            <i class="jobsearch-icon jobsearch-calendar"></i> <?php echo esc_html__('Post Date', 'wp-jobsearch') ?>
                                            : <?php echo esc_html($jobsearch_job_posted_formated); ?>
                                        </li>
                                        <?php
                                    }
                                    $jobsearch_last_date_formated = '';
                                    if ($application_deadline != '') {
                                        $jobsearch_last_date_formated = date_i18n(get_option('date_format'), ($application_deadline));
                                    }
                                    if (isset($jobsearch_last_date_formated) && !empty($jobsearch_last_date_formated)) {
                                        ?>
                                        <li>
                                        <i class="careerfy-icon careerfy-calendar"></i> <?php echo esc_html__('Apply Before ', 'wp-jobsearch'); ?>
                                        : <?php echo esc_html($jobsearch_last_date_formated); ?>
                                        </li><?php } ?>


                                </ul>
                                <?php
                                $job_info_output = ob_get_clean();
                                echo apply_filters('jobsearch_job_detail_content_info', $job_info_output, $job_id);
                                ?>
                            </figcaption>
                            <div class="jobsearch_side_box jobsearch_apply_job">
                                <?php
                                ob_start();
                                echo jobsearch_job_det_applybtn_acthtml('', $job_id, 'page', 'view6');
                                $apply_bbox = ob_get_clean();
                                echo apply_filters('jobsearch_job_defdet_applybtn_boxhtml', $apply_bbox, $job_id);
                                //
                                $popup_args = array(
                                    'job_employer_id' => $job_employer_id,
                                    'job_id' => $job_id,
                                );
                                $popup_html = apply_filters('jobsearch_job_send_message_html_filter', '', $popup_args);
                                echo force_balance_tags($popup_html);
                                ?>
                            </div>
                            <?php
                            $sidebar_apply_output = ob_get_clean();
                            echo apply_filters('jobsearch_job_detail_sidebar_apply_btns', $sidebar_apply_output, $job_id);
                            ?>

                            <script type="text/javascript">
                                //for login popup
                                jQuery(document).on('click', '.jobsearch-sendmessage-popup-btn', function () {
                                    jobsearch_modal_popup_open('JobSearchModalSendMessage');
                                });
                                jQuery(document).on('click', '.jobsearch-sendmessage-messsage-popup-btn', function () {
                                    jobsearch_modal_popup_open('JobSearchModalSendMessageWarning');
                                });
                                jQuery(document).on('click', '.jobsearch-applyjob-msg-popup-btn', function () {
                                    jobsearch_modal_popup_open('JobSearchModalApplyJobWarning');
                                });
                            </script>
                        </figure>
                    </div>
                    <?php
                    $map_switch_arr = isset($jobsearch_plugin_options['jobsearch-detail-map-switch']) ? $jobsearch_plugin_options['jobsearch-detail-map-switch'] : '';
                    $job_map = false;
                    if (isset($map_switch_arr) && is_array($map_switch_arr) && sizeof($map_switch_arr) > 0) {
                        foreach ($map_switch_arr as $map_switch) {
                            if ($map_switch == 'job') {
                                $job_map = true;
                            }
                        }
                    }
                    if ($job_map) { ?>
                        <div class="jobsearch_side_box jobsearch_box_map">
                            <?php jobsearch_google_map_with_directions($job_id, 250, true); ?>
                        </div>
                    <?php } ?>

                    <div class="jobsearch-jobdetail-content jobsearch-quick-apply-content">
                        <?php
                        ob_start();
                        $cus_fields = array('content' => '');
                        $cus_fields = apply_filters('jobsearch_custom_fields_list', 'job', $post_id, $cus_fields, '<li class="jobsearch-column-4">', '</li>');
                        if (isset($cus_fields['content']) && $cus_fields['content'] != '') { ?>
                            <div class="jobsearch-content-title">
                                <h2><?php echo esc_html__('Job Detail', 'wp-jobsearch') ?></h2></div>
                            <div class="jobsearch-jobdetail-services">
                                <ul class="jobsearch-row">
                                    <?php
                                    // All custom fields with value
                                    echo force_balance_tags($cus_fields['content']);
                                    ?>
                                </ul>
                            </div>
                            <?php
                        }
                        $job_fields_output = ob_get_clean();
                        echo apply_filters('jobsearch_job_detail_content_fields', $job_fields_output, $job_id);
                        //
                        if ($job_content != '') {
                            ob_start();
                            ?>
                            <div class="jobsearch-content-title">
                                <h2><?php echo esc_html__('Job Description', 'wp-jobsearch') ?></h2>
                            </div>
                            <div class="jobsearch-description">
                                <?php echo force_balance_tags($job_content); ?>
                            </div>
                            <?php
                            $job_det_output = ob_get_clean();
                            echo apply_filters('jobsearch_job_detail_content_detail', $job_det_output, $job_id);
                        }

                        do_action('jobsearch_job_detail_after_description', $job_id);
                        $job_attachments_switch = isset($jobsearch_plugin_options['job_attachments']) ? $jobsearch_plugin_options['job_attachments'] : '';
                        if ($job_attachments_switch == 'on') {
                            $all_attach_files = get_post_meta($job_id, 'jobsearch_field_job_attachment_files', true);
                            if (!empty($all_attach_files)) { ?>
                                <div class="jobsearch-content-title">
                                    <h2><?php esc_html_e('Attached Files', 'wp-jobsearch') ?></h2></div>
                                <div class="jobsearch-file-attach-sec">
                                    <ul class="jobsearch-row">
                                        <?php
                                        foreach ($all_attach_files as $_attach_file) {

                                            $_attach_id = jobsearch_get_attachment_id_from_url($_attach_file);
                                            $_attach_post = get_post($_attach_id);
                                            $_attach_mime = isset($_attach_post->post_mime_type) ? $_attach_post->post_mime_type : '';
                                            $_attach_guide = isset($_attach_post->guid) ? $_attach_post->guid : '';
                                            $attach_name = basename($_attach_guide);

                                            $file_icon = 'fa fa-file-text-o';
                                            if ($_attach_mime == 'image/png' || $_attach_mime == 'image/jpeg') {
                                                $file_icon = 'fa fa-file-image-o';
                                            } else if ($_attach_mime == 'application/msword' || $_attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                                $file_icon = 'fa fa-file-word-o';
                                            } else if ($_attach_mime == 'application/vnd.ms-excel' || $_attach_mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                                $file_icon = 'fa fa-file-excel-o';
                                            } else if ($_attach_mime == 'application/pdf') {
                                                $file_icon = 'fa fa-file-pdf-o';
                                            } ?>
                                            <li class="jobsearch-column-4">
                                                <div class="file-container">
                                                    <a href="<?php echo($_attach_file) ?>"
                                                       oncontextmenu="javascript: return false;"
                                                       onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                                       download="<?php echo($attach_name) ?>"
                                                       class="file-download-icon"><i
                                                                class="<?php echo($file_icon) ?>"></i> <?php echo($attach_name) ?>
                                                    </a>
                                                    <a href="<?php echo($_attach_file) ?>"
                                                       oncontextmenu="javascript: return false;"
                                                       onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                                       download="<?php echo($attach_name) ?>"
                                                       class="file-download-btn"><?php esc_html_e('Download', 'wp-jobsearch') ?>
                                                        <i class="jobsearch-icon jobsearch-download-arrow"></i></a>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <?php
                            }
                        }

                        do_action('jobsearch_job_detail_before_skills', $job_id);
                        if ($skills_list != '') {
                            ob_start();
                            ?>
                            <div class="jobsearch-content-title">
                                <h2><?php echo esc_html__('Required skills', 'wp-jobsearch') ?></h2>
                            </div>
                            <div class="jobsearch-jobdetail-tags">
                                <?php echo($skills_list); ?>
                            </div>
                            <?php
                            $job_skills_output = ob_get_clean();
                            echo apply_filters('jobsearch_job_detail_content_skills', $job_skills_output, $job_id);
                        }
                        ?>
                    </div>
                </div>
                <?php
                wp_reset_postdata();
                ?>
            </div>
            <?php
            echo apply_filters('jobsearch_job_detail_quick_apply_before_footer', $job_id);
        }

        public static function quick_job_application_popup_form($job_id)
        {
            global $jobsearch_plugin_options, $post;
            wp_enqueue_script('jobsearch-job-application-functions-script');
            echo apply_filters('jobsearch_job_detail_sidebar_bef4_apply', '', $job_id);
            $job_employer_id = get_post_meta($post->ID, 'jobsearch_field_job_posted_by', true); // get job employer
            ob_start();
            ?>
            <div class="jobsearch_side_box jobsearch_apply_job">
                <?php
                ob_start();
                echo jobsearch_job_det_applybtn_acthtml('', $job_id, 'page', 'view1');
                $apply_bbox = ob_get_clean();
                echo apply_filters('jobsearch_job_defdet_applybtn_boxhtml', $apply_bbox, $job_id);
                //
                $popup_args = array(
                    'job_employer_id' => $job_employer_id,
                    'job_id' => $job_id,
                );
                $popup_html = apply_filters('jobsearch_job_send_message_html_filter', '', $popup_args);
                echo force_balance_tags($popup_html);
                ?>
            </div>
            <?php
            $sidebar_apply_output = ob_get_clean();
            echo apply_filters('jobsearch_job_detail_sidebar_apply_btns', $sidebar_apply_output, $job_id);
            ?>

            <script type="text/javascript">
                //for login popup
                jQuery(document).on('click', '.jobsearch-sendmessage-popup-btn', function () {
                    jobsearch_modal_popup_open('JobSearchModalSendMessage');
                });
                jQuery(document).on('click', '.jobsearch-sendmessage-messsage-popup-btn', function () {
                    jobsearch_modal_popup_open('JobSearchModalSendMessageWarning');
                });
                jQuery(document).on('click', '.jobsearch-applyjob-msg-popup-btn', function () {
                    jobsearch_modal_popup_open('JobSearchModalApplyJobWarning');
                });
            </script>
        <?php }


        public
        function jobsearch_job_applications_forms_callback($html, $arg = array())
        {
            global $jobsearch_plugin_options;
            $rand_id = rand(123400, 9999999);
            extract(shortcode_atts(array(
                'classes' => 'jobsearch-applyjob-btn',
                'btn_after_label' => '',
                'btn_before_label' => '',
                'btn_applied_label' => '',
                'before_icon' => '',
                'job_id' => ''
            ), $arg));

            $job_extrnal_apply_switch_arr = isset($jobsearch_plugin_options['apply-methods']) ? $jobsearch_plugin_options['apply-methods'] : '';
            $without_login_signin_restriction = isset($jobsearch_plugin_options['without-login-apply-restriction']) ? $jobsearch_plugin_options['without-login-apply-restriction'] : '';

            $job_apply_switch = isset($jobsearch_plugin_options['job-apply-switch']) ? $jobsearch_plugin_options['job-apply-switch'] : 'on';
            if (isset($job_apply_switch) && $job_apply_switch != 'on') {
                return $html;
            }

            $job_extrnal_apply_internal_switch = '';
            $job_extrnal_apply_external_switch = '';
            $job_extrnal_apply_email_switch = '';
            if (isset($job_extrnal_apply_switch_arr) && is_array($job_extrnal_apply_switch_arr) && sizeof($job_extrnal_apply_switch_arr) > 0) {
                foreach ($job_extrnal_apply_switch_arr as $apply_switch) {
                    if ($apply_switch == 'internal') {
                        $job_extrnal_apply_internal_switch = 'internal';
                    }
                    if ($apply_switch == 'external') {
                        $job_extrnal_apply_external_switch = 'external';
                    }
                    if ($apply_switch == 'email') {
                        $job_extrnal_apply_email_switch = 'email';
                    }
                }
            }

            $job_aply_type = get_post_meta($job_id, 'jobsearch_field_job_apply_type', true);
            if (empty($job_aply_type)) {
                $job_aply_type = 'internal';
            }

            $job_aply_extrnal_url = get_post_meta($job_id, 'jobsearch_field_job_apply_url', true);
            $apply_without_login = isset($jobsearch_plugin_options['job-apply-without-login']) ? $jobsearch_plugin_options['job-apply-without-login'] : '';
            $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
            if (is_user_logged_in()) {
                if (!jobsearch_user_is_candidate()) { ?>
                    <div class="jobsearch-quick-apply-validate">
                        <span><?php echo esc_html__("Required 'Candidate' login applying this job.", 'wp-jobsearch'); ?> </span>
                        <span><?php echo esc_html__("Click here to", 'wp-jobsearch'); ?> <a
                                    href="<?php echo wp_logout_url(get_permalink()); ?>"><?php echo esc_html__("logout", 'wp-jobsearch'); ?></a> </span>
                        <span><?php echo esc_html__("And try again", 'wp-jobsearch'); ?> </span>
                    </div>
                    <?php
                    wp_die();
                }
            }
            if ($job_id != '') {
                $classes_str = 'jobsearch-open-signin-tab jobsearch-wredirct-url';
                $multi_cvs = false;
                if (is_user_logged_in()) {
                    if (jobsearch_user_is_candidate()) {
                        if ($multiple_cv_files_allow == 'on') {
                            $multi_cvs = true;
                        }
                        $classes_str = 'jobsearch-apply-btn';
                    } else {
                        $classes_str = 'jobsearch-other-role-btn jobsearch-applyjob-msg-popup-btn';
                    }
                }
                ob_start();
                $jobsearch_applied_list = array();
                $btn_text = $btn_before_label;
                if (!is_user_logged_in() && $apply_without_login != 'on') {
                    $btn_text = apply_filters('jobsearch_loginto_apply_job_btn_text', esc_html__('Login to Apply Job', 'wp-jobsearch'));
                }
                $is_applied = false;
                if (is_user_logged_in()) {
                    $finded_result_list = jobsearch_find_index_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', 'post_id', jobsearch_get_user_id());
                    if (is_array($finded_result_list) && !empty($finded_result_list)) {
                        $classes_str = 'jobsearch-applied-btn';
                        $btn_text = $btn_applied_label;
                        $is_applied = true;
                    }
                }

                if ($apply_without_login == 'on' && !is_user_logged_in()) {
                    $classes_str = 'jobsearch-nonuser-apply-btn';
                }

                //
                $insta_applied = false;
                if (isset($_GET['jobsearch_apply_instamatch']) && $_GET['jobsearch_apply_instamatch'] == '1') {
                    $insta_id = isset($_GET['id']) ? $_GET['id'] : '';
                    $insta_ids = explode('|', $insta_id);
                    $insta_job_id = isset($insta_ids[0]) ? $insta_ids[0] : '';
                    $insta_user_id = isset($insta_ids[1]) ? $insta_ids[1] : '';
                    if ($insta_user_id > 0 && $insta_job_id > 0) {
                        $finded_instaresult_list = jobsearch_find_index_user_meta_list($job_id, 'jobsearch_instamatch_job_ids', 'post_id', $insta_user_id);
                        if (!empty($finded_instaresult_list) && is_array($finded_instaresult_list)) {
                            $insta_applied = true;
                        }
                    }
                }

                if ($insta_applied) {
                    $classes_str = 'jobsearch-applied-btn';
                    $btn_text = $btn_applied_label;
                    $is_applied = true;
                }

                // signin restriction on without login methods

                $internal_signin_switch = false;
                $external_signin_switch = false;
                $email_signin_switch = false;
                if (isset($without_login_signin_restriction) && is_array($without_login_signin_restriction) && sizeof($without_login_signin_restriction) > 0) {
                    foreach ($without_login_signin_restriction as $restrict_signin_switch) {
                        if ($restrict_signin_switch == 'internal') {
                            $internal_signin_switch = true;
                        }
                        if ($restrict_signin_switch == 'external') {
                            $external_signin_switch = true;
                        }
                        if ($restrict_signin_switch == 'email') {
                            $email_signin_switch = true;
                        }
                    }
                }

                if ($job_extrnal_apply_email_switch == 'email' && $job_aply_type == 'with_email') {
                    if ($is_applied == true) { ?>
                        <div class="jobsearch-quick-apply-validate">
                            <span><?php echo esc_html__('You have already applied for this job', 'wp-jobsearch'); ?></span>
                        </div>
                        <?php
                        wp_die();
                    }
                    if ($apply_without_login == 'off' && !is_user_logged_in() && $email_signin_switch) {
                        $classes_str = 'jobsearch-open-signin-tab';
                        ?>
                        <a href="javascript:void(0);"
                           class="<?php echo esc_html($classes_str); ?> <?php echo !empty($before_icon) ? '<i class=' . $before_icon . '></i>' : ''; ?> <?php echo esc_html($classes); ?>"><?php echo esc_html($btn_text) ?> </a>

                    <?php } else {

                        $phone_validation_type = isset($jobsearch_plugin_options['intltell_phone_validation']) ? $jobsearch_plugin_options['intltell_phone_validation'] : '';
                        if ($phone_validation_type == 'on') {
                            wp_enqueue_script('jobsearch-intlTelInput');
                        }

                        $popup_args = array(
                            'p_job_id' => $job_id,
                            'p_rand_id' => $rand_id,
                            'p_btn_text' => $btn_text,
                            'p_classes' => $classes,
                            'p_classes_str' => $classes_str,
                            'p_btn_after_label' => $btn_after_label,
                        );

                        $phone_validation_type = isset($jobsearch_plugin_options['intltell_phone_validation']) ? $jobsearch_plugin_options['intltell_phone_validation'] : '';
                        $wout_fields_sort = isset($jobsearch_plugin_options['aplywout_login_fields_sort']) ? $jobsearch_plugin_options['aplywout_login_fields_sort'] : '';
                        $wout_fields_sort = isset($wout_fields_sort['fields']) ? $wout_fields_sort['fields'] : '';

                        extract(shortcode_atts(array(
                            'p_job_id' => '',
                            'p_rand_id' => '',
                            'p_btn_text' => '',
                            'p_classes' => '',
                            'p_classes_str' => '',
                            'p_btn_after_label' => '',
                        ), $popup_args));

                        $user_dname = '';
                        $user_demail = '';

                        if (is_user_logged_in()) {
                            $cuser_id = get_current_user_id();
                            $cuser_obj = get_user_by('ID', $cuser_id);
                            $user_dname = $cuser_obj->display_name;
                            $user_demail = $cuser_obj->user_email;
                        }

                        $file_sizes_arr = array(
                            '300' => __('300KB', 'wp-jobsearch'),
                            '500' => __('500KB', 'wp-jobsearch'),
                            '750' => __('750KB', 'wp-jobsearch'),
                            '1024' => __('1Mb', 'wp-jobsearch'),
                            '2048' => __('2Mb', 'wp-jobsearch'),
                            '3072' => __('3Mb', 'wp-jobsearch'),
                            '4096' => __('4Mb', 'wp-jobsearch'),
                            '5120' => __('5Mb', 'wp-jobsearch'),
                            '10120' => __('10Mb', 'wp-jobsearch'),
                            '50120' => __('50Mb', 'wp-jobsearch'),
                            '100120' => __('100Mb', 'wp-jobsearch'),
                            '200120' => __('200Mb', 'wp-jobsearch'),
                            '300120' => __('300Mb', 'wp-jobsearch'),
                            '500120' => __('500Mb', 'wp-jobsearch'),
                            '1000120' => __('1Gb', 'wp-jobsearch'),
                        );
                        $cvfile_size = '5120';
                        $cvfile_size_str = __('5 Mb', 'wp-jobsearch');
                        $cand_cv_file_size = isset($jobsearch_plugin_options['cand_cv_file_size']) ? $jobsearch_plugin_options['cand_cv_file_size'] : '';
                        if (isset($file_sizes_arr[$cand_cv_file_size])) {
                            $cvfile_size = $cand_cv_file_size;
                            $cvfile_size_str = $file_sizes_arr[$cand_cv_file_size];
                        }
                        $filesize_act = ceil($cvfile_size / 1024);

                        $cand_files_types = isset($jobsearch_plugin_options['cand_cv_types']) ? $jobsearch_plugin_options['cand_cv_types'] : '';

                        if (empty($cand_files_types)) {
                            $cand_files_types = array(
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/pdf',
                            );
                        }
                        $sutable_files_arr = array();
                        $file_typs_comarr = array(
                            'text/plain' => __('text', 'wp-jobsearch'),
                            'image/jpeg' => __('jpeg', 'wp-jobsearch'),
                            'image/png' => __('png', 'wp-jobsearch'),
                            'application/msword' => __('doc', 'wp-jobsearch'),
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => __('docx', 'wp-jobsearch'),
                            'application/vnd.ms-excel' => __('xls', 'wp-jobsearch'),
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => __('xlsx', 'wp-jobsearch'),
                            'application/pdf' => __('pdf', 'wp-jobsearch'),
                        );
                        foreach ($file_typs_comarr as $file_typ_key => $file_typ_comar) {
                            if (in_array($file_typ_key, $cand_files_types)) {
                                $sutable_files_arr[] = '.' . $file_typ_comar;
                            }
                        }
                        $sutable_files_str = implode(', ', $sutable_files_arr);
                        ob_start();
                        if (isset($_COOKIE["jobsearch_email_apply_job_" . $p_job_id])) { ?>
                            <div class="jobsearch-quick-apply-validate">
                                <span><?php esc_html_e('You have already applied for this job.', 'wp-jobsearch') ?></span>
                            </div>
                        <?php } else { ?>
                            <form id="apply-withemail-<?php echo($p_rand_id) ?>">
                                <div class="jobsearch-apply-withemail-con jobsearch-user-form jobsearch-user-form-coltwo">
                                    <ul class="apply-fields-list">
                                        <?php
                                        ob_start();
                                        if (isset($wout_fields_sort['name'])) {
                                            foreach ($wout_fields_sort as $field_sort_key => $field_sort_val) {
                                                $field_name_swich_key = 'aplywout_log_f' . $field_sort_key . '_swch';
                                                $field_name_swich = isset($jobsearch_plugin_options[$field_name_swich_key]) ? $jobsearch_plugin_options[$field_name_swich_key] : '';
                                                if ($field_sort_key == 'name' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                    ?>
                                                    <li>
                                                        <label><?php esc_html_e('First Name:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                        <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                               name="user_fullname" type="text"
                                                               placeholder="<?php esc_html_e('First Name', 'wp-jobsearch') ?>">
                                                    </li>
                                                    <li>
                                                        <label><?php esc_html_e('Last Name:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                        <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                               name="user_surname" type="text"
                                                               placeholder="<?php esc_html_e('Last Name', 'wp-jobsearch') ?>">
                                                    </li>
                                                <?php } else if ($field_sort_key == 'email') {
                                                    $logedusr_email = '';
                                                    if (is_user_logged_in()) {
                                                        $loged_user_obj = wp_get_current_user();
                                                        $logedusr_email = isset($loged_user_obj->user_email) ? $loged_user_obj->user_email : '';
                                                    } ?>
                                                    <li>
                                                        <label><?php esc_html_e('Email: *', 'wp-jobsearch') ?></label>
                                                        <input class="required" name="user_email"
                                                               type="text" <?php if ($logedusr_email != '') { ?> value="<?php echo($logedusr_email) ?>" readonly<?php } ?>
                                                               placeholder="<?php esc_html_e('Email Address', 'wp-jobsearch') ?>">
                                                    </li>
                                                <?php } else if ($field_sort_key == 'phone' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) { ?>
                                                    <li>
                                                        <label><?php esc_html_e('Phone:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                        <?php
                                                        if ($phone_validation_type == 'on') {
                                                            $rand_numb = rand(10000000, 99999999);
                                                            jobsearch_phonenum_itltell_input('user_phone', $rand_numb);
                                                        } else {
                                                            ?>
                                                            <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                                   name="user_phone" type="tel"
                                                                   placeholder="<?php esc_html_e('Phone Number', 'wp-jobsearch') ?>">
                                                            <?php
                                                        }
                                                        ?>
                                                    </li>
                                                <?php } else if ($field_sort_key == 'current_jobtitle' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) { ?>
                                                    <li>
                                                        <label><?php esc_html_e('Current Job Title:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                        <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                               name="user_job_title" type="text"
                                                               placeholder="<?php esc_html_e('Current Job Title', 'wp-jobsearch') ?>">
                                                    </li>
                                                <?php } else if ($field_sort_key == 'current_salary' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) { ?>
                                                    <li>
                                                        <label><?php esc_html_e('Current Salary:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                        <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                               name="user_salary" type="text"
                                                               placeholder="<?php esc_html_e('Current Salary', 'wp-jobsearch') ?>">
                                                    </li>
                                                    <?php
                                                } else if ($field_sort_key == 'custom_fields' && $field_name_swich == 'on') {
                                                    do_action('jobsearch_form_custom_fields_load', 0, 'candidate');
                                                } else if ($field_sort_key == 'cv_attach' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                                    ?>
                                                    <li class="jobsearch-user-form-coltwo-full">
                                                        <div id="jobsearch-upload-cv-main"
                                                             class="jobsearch-upload-cv jobsearch-applyjob-upload-cv">
                                                            <label><?php esc_html_e('Resume', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                            <div class="jobsearch-drpzon-con jobsearch-drag-dropcustom">
                                                                <div id="cvFilesDropzone"
                                                                     class="dropzone"
                                                                     ondragover="jobsearch_dragover_evnt(event)"
                                                                     ondragleave="jobsearch_leavedrop_evnt(event)"
                                                                     ondrop="jobsearch_ondrop_evnt(event)">
                                                                    <input type="file"
                                                                           id="cand_cv_filefield"
                                                                           class="jobsearch-upload-btn <?php echo($field_name_swich == 'on_req' ? 'cv_is_req' : '') ?>"
                                                                           name="cuser_cv_file"
                                                                           onchange="jobsearchFileContainerChangeFile(event)">
                                                                    <div class="fileContainerFileName"
                                                                         ondrop="jobsearch_ondrop_evnt(event)"
                                                                         id="fileNameContainer">
                                                                        <div class="dz-message jobsearch-dropzone-template">
                                                                                            <span class="upload-icon-con"><i
                                                                                                        class="jobsearch-icon jobsearch-upload"></i></span>
                                                                            <strong><?php esc_html_e('Drop a resume file or click to upload.', 'wp-jobsearch') ?></strong>
                                                                            <div class="upload-inffo"><?php printf(__('To upload file size is <span>(Max %s)</span> <span class="uplod-info-and">and</span> allowed file types are <span>(%s)</span>', 'wp-jobsearch'), $cvfile_size_str, $sutable_files_str) ?></div>
                                                                            <div class="upload-or-con">
                                                                                <span><?php esc_html_e('or', 'wp-jobsearch') ?></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <a class="jobsearch-drpzon-btn"><i
                                                                                class="jobsearch-icon jobsearch-arrows-2"></i> <?php esc_html_e('Upload Resume', 'wp-jobsearch') ?>
                                                                    </a>
                                                                </div>
                                                                <script>
                                                                    jQuery('#cvFilesDropzone').find('input[name=cuser_cv_file]').css({
                                                                        position: 'absolute',
                                                                        width: '100%',
                                                                        height: '100%',
                                                                        top: '0',
                                                                        left: '0',
                                                                        opacity: '0',
                                                                        'z-index': '9',
                                                                    });

                                                                    function jobsearchFileContainerChangeFile(e) {
                                                                        var the_show_msg = '<?php esc_html_e('No file has been selected', 'wp-jobsearch') ?>';
                                                                        if (e.target.files.length > 0) {
                                                                            var slected_file_name = e.target.files[0].name;
                                                                            the_show_msg = '<?php esc_html_e('The file', 'wp-jobsearch') ?> "' + slected_file_name + '" <?php esc_html_e('has been selected', 'wp-jobsearch') ?>';
                                                                        }
                                                                        document.getElementById('cvFilesDropzone').classList.remove('fileContainerDragOver');
                                                                        try {
                                                                            droppedFiles = document.getElementById('cand_cv_filefield').files;
                                                                            document.getElementById('fileNameContainer').textContent = the_show_msg;
                                                                        } catch (error) {

                                                                        }
                                                                        try {
                                                                            aName = document.getElementById('cand_cv_filefield').value;
                                                                            if (aName !== '') {
                                                                                document.getElementById('fileNameContainer').textContent = the_show_msg;
                                                                            }
                                                                        } catch (error) {

                                                                        }
                                                                    }

                                                                    function jobsearch_ondrop_evnt(e) {
                                                                        var the_show_msg = '<?php esc_html_e('No file has been selected', 'wp-jobsearch') ?>';
                                                                        if (e.target.files.length > 0) {
                                                                            var slected_file_name = e.target.files[0].name;
                                                                            the_show_msg = '<?php esc_html_e('The file', 'wp-jobsearch') ?> "' + slected_file_name + '" <?php esc_html_e('has been selected', 'wp-jobsearch') ?>';
                                                                        }
                                                                        document.getElementById('cvFilesDropzone').classList.remove('fileContainerDragOver');
                                                                        try {
                                                                            droppedFiles = e.dataTransfer.files;
                                                                            document.getElementById('fileNameContainer').textContent = the_show_msg;
                                                                        } catch (error) {
                                                                            ;
                                                                        }
                                                                    }

                                                                    function jobsearch_dragover_evnt(e) {
                                                                        document.getElementById('cvFilesDropzone').classList.add('fileContainerDragOver');
                                                                        e.preventDefault();
                                                                        e.stopPropagation();
                                                                    }

                                                                    function jobsearch_leavedrop_evnt(e) {
                                                                        document.getElementById('cvFilesDropzone').classList.remove('fileContainerDragOver');
                                                                    }
                                                                </script>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            <li class="form-textarea jobsearch-user-form-coltwo-full">
                                                <label><?php esc_html_e('Message', 'wp-jobsearch') ?>:</label>
                                                <textarea name="user_msg"
                                                          placeholder="<?php esc_html_e('Type your Message', 'wp-jobsearch') ?>"></textarea>
                                            </li>
                                            <?php
                                        }
                                        $cv_html = ob_get_clean();
                                        echo apply_filters('jobsearch_aply_with_cv_form_cv_field', $cv_html, $p_job_id, $p_rand_id);
                                        ?>
                                        <li class="jobsearch-user-form-coltwo-full">
                                            <input type="hidden" name="job_id"
                                                   value="<?php echo($p_job_id) ?>">
                                            <input type="hidden" name="action"
                                                   value="jobsearch_applying_job_with_email">
                                            <?php
                                            jobsearch_terms_and_con_link_txt();
                                            //
                                            ob_start();
                                            ?>
                                            <div class="terms-priv-chek-con">
                                                <p><input type="checkbox"
                                                          name="email_commun_check"> <?php esc_html_e('You accept email communication.', 'wp-jobsearch') ?>
                                                </p>
                                            </div>
                                            <?php


                                            $accpt_html = ob_get_clean();
                                            echo apply_filters('jobsearch_jobaply_byemail_comuni_chkhtml', $accpt_html);
                                            ?>
                                            <a href="javascript:void(0);"
                                               class="<?php echo esc_html($p_classes); ?> jobsearch-applyin-withemail"
                                               data-randid="<?php echo absint($p_rand_id); ?>"
                                               data-jobid="<?php echo absint($p_job_id); ?>"
                                               data-btnafterlabel="<?php echo esc_html($p_btn_after_label) ?>"
                                               data-btnbeforelabel="<?php echo esc_html($p_btn_text) ?>"><?php echo esc_html($p_btn_text) ?></a>
                                        </li>
                                    </ul>
                                    <div class="apply-job-form-msg"></div>
                                    <div class="apply-job-loader"></div>
                                </div>
                            </form>
                            <?php
                        }
                        $popupp_hmtl = ob_get_clean();
                        echo apply_filters('jobsearch_applyjob_withemail_popup_html', $popupp_hmtl, $popup_args);

                    }
                } else if ($job_extrnal_apply_external_switch == 'external' && $job_aply_type == 'external' && $job_aply_extrnal_url != '') {
                    if ($is_applied == true) { ?>
                        <div class="jobsearch-quick-apply-validate">
                            <span><?php echo esc_html__('You have already applied for this job', 'wp-jobsearch'); ?></span>
                        </div>
                        <?php
                        wp_die();
                    }
                    if ($apply_without_login == 'off' && !is_user_logged_in() && $external_signin_switch) {
                        $classes_str = 'jobsearch-open-signin-tab'; ?>
                        <a href="javascript:void(0);"
                           class="<?php echo esc_html($classes_str); ?> <?php echo esc_html($classes); ?>"><?php echo !empty($before_icon) ? '<i class=' . $before_icon . '></i>' : ''; ?><?php echo esc_html($btn_text) ?> </a>
                    <?php } else { ?>
                        <a href="<?php echo($job_aply_extrnal_url) ?>" class="<?php echo esc_html($classes); ?>"
                           target="_blank"><?php echo !empty($before_icon) ? '<i class=' . $before_icon . '></i>' : ''; ?><?php echo esc_html($btn_text) ?></a>
                        <?php
                    }
                } else if ($job_extrnal_apply_internal_switch == 'internal' && $job_aply_type == 'internal') {
                    if ($is_applied == true) { ?>
                        <div class="jobsearch-quick-apply-validate">
                            <span><?php echo esc_html__('You have already applied for this job', 'wp-jobsearch'); ?></span>
                        </div>
                        <?php
                        wp_die();
                    }

                    $this_wredirct_url = jobsearch_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                    if ($apply_without_login == 'off' && !is_user_logged_in() && $internal_signin_switch) {
                        $classes_str = 'jobsearch-open-signin-tab'; ?>
                        <a href="javascript:void(0);"
                           class="<?php echo esc_html($classes_str); ?> <?php echo esc_html($classes); ?>" <?php echo(!is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '') ?>><?php echo !empty($before_icon) ? '<i class=' . $before_icon . '></i>' : ''; ?><?php echo esc_html($btn_text) ?> </a><?php
                    } else {

                        if ($multi_cvs === true) {
                            wp_enqueue_script('dropzone');

                            $max_cvs_allow = isset($jobsearch_plugin_options['max_cvs_allow']) && absint($jobsearch_plugin_options['max_cvs_allow']) > 0 ? absint($jobsearch_plugin_options['max_cvs_allow']) : 5;
                            $popup_args = array(
                                'p_job_id' => $job_id,
                                'p_rand_id' => $rand_id,
                                'p_btn_text' => $btn_text,
                                'p_classes' => $classes,
                                'p_classes_str' => $classes_str,
                                'p_btn_after_label' => $btn_after_label,
                                'max_cvs_allow' => $max_cvs_allow,
                            );

                            $user_id = get_current_user_id();
                            $candidate_id = jobsearch_get_user_candidate_id($user_id);

                            extract(shortcode_atts(array(
                                'p_job_id' => '',
                                'p_rand_id' => '',
                                'p_btn_text' => '',
                                'p_classes' => '',
                                'p_classes_str' => '',
                                'p_btn_after_label' => '',
                                'max_cvs_allow' => '',
                            ), $popup_args));

                            //
                            $file_sizes_arr = array(
                                '300' => __('300KB', 'wp-jobsearch'),
                                '500' => __('500KB', 'wp-jobsearch'),
                                '750' => __('750KB', 'wp-jobsearch'),
                                '1024' => __('1Mb', 'wp-jobsearch'),
                                '2048' => __('2Mb', 'wp-jobsearch'),
                                '3072' => __('3Mb', 'wp-jobsearch'),
                                '4096' => __('4Mb', 'wp-jobsearch'),
                                '5120' => __('5Mb', 'wp-jobsearch'),
                                '10120' => __('10Mb', 'wp-jobsearch'),
                                '50120' => __('50Mb', 'wp-jobsearch'),
                                '100120' => __('100Mb', 'wp-jobsearch'),
                                '200120' => __('200Mb', 'wp-jobsearch'),
                                '300120' => __('300Mb', 'wp-jobsearch'),
                                '500120' => __('500Mb', 'wp-jobsearch'),
                                '1000120' => __('1Gb', 'wp-jobsearch'),
                            );
                            $cvfile_size = '5120';
                            $cvfile_size_str = __('5 Mb', 'wp-jobsearch');
                            $cand_cv_file_size = isset($jobsearch_plugin_options['cand_cv_file_size']) ? $jobsearch_plugin_options['cand_cv_file_size'] : '';
                            if (isset($file_sizes_arr[$cand_cv_file_size])) {
                                $cvfile_size = $cand_cv_file_size;
                                $cvfile_size_str = $file_sizes_arr[$cand_cv_file_size];
                            }
                            $filesize_act = ceil($cvfile_size / 1024);

                            $cand_files_types = isset($jobsearch_plugin_options['cand_cv_types']) ? $jobsearch_plugin_options['cand_cv_types'] : '';

                            if (empty($cand_files_types)) {
                                $cand_files_types = array(
                                    'application/msword',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'application/pdf',
                                );
                            }
                            $sutable_files_arr = array();
                            $file_typs_comarr = array(
                                'text/plain' => __('text', 'wp-jobsearch'),
                                'image/jpeg' => __('jpeg', 'wp-jobsearch'),
                                'image/png' => __('png', 'wp-jobsearch'),
                                'application/msword' => __('doc', 'wp-jobsearch'),
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => __('docx', 'wp-jobsearch'),
                                'application/vnd.ms-excel' => __('xls', 'wp-jobsearch'),
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => __('xlsx', 'wp-jobsearch'),
                                'application/pdf' => __('pdf', 'wp-jobsearch'),
                            );
                            foreach ($file_typs_comarr as $file_typ_key => $file_typ_comar) {
                                if (in_array($file_typ_key, $cand_files_types)) {
                                    $sutable_files_arr[] = '.' . $file_typ_comar;
                                }
                            }

                            $sutable_files_str = implode(', ', $sutable_files_arr);
                            $cand_cvr_leter = get_post_meta($candidate_id, 'jobsearch_field_resume_cover_letter', true);

                            $cand_resm_coverletr = isset($jobsearch_plugin_options['cand_resm_cover_letr']) ? $jobsearch_plugin_options['cand_resm_cover_letr'] : '';
                            ?>
                            <div class="modal-box-area">
                                <?php
                                $user_id = get_current_user_id();
                                $candidate_id = jobsearch_get_user_candidate_id($user_id);
                                $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);
                                if (!empty($ca_at_cv_files)) { ?>
                                    <div class="jobsearch-modal-title-box">
                                        <h2><?php esc_html_e('Select CV', 'wp-jobsearch') ?></h2>
                                    </div>
                                <?php } ?>
                                <div class="jobsearch-apply-withcvs">
                                    <?php
                                    $cv_files_count = 0;
                                    if (!empty($ca_at_cv_files)) {
                                        $cv_files_count = count($ca_at_cv_files);
                                        ?>
                                        <ul class="user-cvs-list">
                                            <?php
                                            $cvfile_count = 1;
                                            foreach ($ca_at_cv_files as $cv_file_key => $cv_file_val) {
                                                $file_attach_id = isset($cv_file_val['file_id']) ? $cv_file_val['file_id'] : '';
                                                $file_url = isset($cv_file_val['file_url']) ? $cv_file_val['file_url'] : '';
                                                $filename = isset($cv_file_val['file_name']) ? $cv_file_val['file_name'] : '';
                                                $filetype = isset($cv_file_val['mime_type']) ? $cv_file_val['mime_type'] : '';
                                                $fileuplod_time = isset($cv_file_val['time']) ? $cv_file_val['time'] : '';
                                                if (is_numeric($file_attach_id) && get_post_type($file_attach_id) == 'attachment') {
                                                    $attach_mime = isset($attach_post->post_mime_type) ? $attach_post->post_mime_type : '';
                                                    $filetype = array('type' => $attach_mime);
                                                }

                                                $cv_file_title = $filename;
                                                $attach_date = $fileuplod_time;
                                                $attach_mime = isset($filetype['type']) ? $filetype['type'] : '';

                                                if ($cvfile_count == 1) {
                                                    $cv_primary = 'yes';
                                                } else {
                                                    $cv_primary = isset($cv_file_val['primary']) ? $cv_file_val['primary'] : '';
                                                }

                                                if (is_numeric($file_attach_id) && get_post_type($file_attach_id) == 'attachment') {
                                                    $cv_file_title = get_the_title($file_attach_id);
                                                    $attach_post = get_post($file_attach_id);
                                                    $file_path = get_attached_file($file_attach_id);
                                                    $filename = basename($file_path);

                                                    $attach_date = isset($attach_post->post_date) ? $attach_post->post_date : '';
                                                    $attach_date = strtotime($attach_date);
                                                    $attach_mime = isset($attach_post->post_mime_type) ? $attach_post->post_mime_type : '';
                                                }

                                                if ($attach_mime == 'application/pdf') {
                                                    $attach_icon = 'fa fa-file-pdf-o';
                                                } else if ($attach_mime == 'application/msword' || $attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                                    $attach_icon = 'fa fa-file-word-o';
                                                } else if ($attach_mime == 'text/plain') {
                                                    $attach_icon = 'fa fa-file-text-o';
                                                } else if ($attach_mime == 'application/vnd.ms-excel' || $attach_mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                                    $attach_icon = 'fa fa-file-excel-o';
                                                } else if ($attach_mime == 'image/jpeg' || $attach_mime == 'image/png') {
                                                    $attach_icon = 'fa fa-file-image-o';
                                                } else {
                                                    $attach_icon = 'fa fa-file-word-o';
                                                }
                                                if (!empty($filetype)) {
                                                    ?>
                                                    <li<?php echo($cv_primary == 'yes' ? ' class="active"' : '') ?>>
                                                        <i class="<?php echo($attach_icon) ?>"></i>
                                                        <label for="cv_file_<?php echo($file_attach_id) ?>">
                                                            <input id="cv_file_<?php echo($file_attach_id) ?>"
                                                                   type="radio" class="cv_file_item"
                                                                   name="cv_file_item" <?php echo($cv_primary == 'yes' ? 'checked="checked"' : '') ?>
                                                                   value="<?php echo($file_attach_id) ?>">
                                                            <?php echo(strlen($cv_file_title) > 40 ? substr($cv_file_title, 0, 40) . '...' : $cv_file_title) ?>
                                                            <?php
                                                            if ($attach_date != '') { ?>
                                                                <span class="upload-datetime"><i
                                                                            class="fa fa-calendar"></i> <?php echo date_i18n(get_option('date_format'), ($attach_date)) . ' ' . date_i18n(get_option('time_format'), ($attach_date)) ?></span>
                                                            <?php } ?>
                                                        </label>
                                                    </li>
                                                    <?php
                                                }
                                                $cvfile_count++;
                                            }
                                            ?>
                                        </ul>
                                        <?php if (isset($cv_files_count) && $cv_files_count < $max_cvs_allow) { ?>
                                            <div class="upload-cvs-sep">
                                                <div class="jobsearch-box-title">
                                                    <span><?php esc_html_e('OR', 'wp-jobsearch') ?></span>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } else { ?>
                                        <ul class="user-cvs-list"></ul>
                                        <?php
                                    }
                                    if (isset($cv_files_count) && $cv_files_count < $max_cvs_allow) { ?>
                                        <div class="upload-new-cv-sec">
                                            <h4><?php esc_html_e('Upload New CV', 'wp-jobsearch') ?> <span
                                                        class="fileUpLoader"></span></h4>
                                            <div class="jobsearch-drpzon-con">
                                                <script>

                                                    Dropzone.options.cvFilesDropzone = {
                                                        uploadMultiple: false,
                                                        maxFiles: 1,
                                                        <?php
                                                        if (!empty($cand_files_types)) {
                                                        ?>
                                                        acceptedFiles: '<?php echo implode(',', $cand_files_types) ?>',
                                                        <?php
                                                        }
                                                        ?>
                                                        maxFilesize: <?php echo absint($filesize_act) ?>,
                                                        paramName: 'on_apply_cv_file',
                                                        init: function () {
                                                            this.on("complete", function (file) {
                                                                //console.log(file);
                                                                if (file.status == 'success') {
                                                                    var ajresponse = file.xhr.response;
                                                                    ajresponse = jQuery.parseJSON(ajresponse);
                                                                    //console.log(ajresponse);
                                                                    jQuery('.jobsearch-apply-withcvs .user-cvs-list').append(ajresponse.filehtml);
                                                                    jQuery('.jobsearch-apply-withcvs .user-cvs-list').removeAttr('style');
                                                                    jQuery('.jobsearch-apply-withcvs .user-nocvs-found').hide();
                                                                    jQuery('.jobsearch-apply-withcvs .user-cvs-list li:last-child').find('input').trigger('click');
                                                                }
                                                                jQuery('.upload-new-cv-sec .fileUpLoader').html('');
                                                            });
                                                        },
                                                        addedfile: function () {
                                                            jQuery('.jobsearch-drpzon-con').css({
                                                                'pointer-events': 'none',
                                                                'opacity': '0.4'
                                                            });
                                                            jQuery('.upload-new-cv-sec .fileUpLoader').html('<i class="fa fa-refresh fa-spin"></i>');
                                                        }
                                                    }

                                                </script>
                                                <form action="<?php echo admin_url('admin-ajax.php') ?>"
                                                      id="cvFilesDropzone" method="post" class="dropzone">
                                                    <div class="dz-message jobsearch-dropzone-template">
                                                                    <span class="upload-icon-con"><i
                                                                                class="jobsearch-icon jobsearch-upload"></i></span>
                                                        <strong><?php esc_html_e('Drop files here to upload.', 'wp-jobsearch') ?></strong>
                                                        <div class="upload-inffo"><?php printf(__('To upload file size is <span>(Max %s)</span> <span class="uplod-info-and">and</span> allowed file types are <span>(%s)</span>', 'wp-jobsearch'), $cvfile_size_str, $sutable_files_str) ?></div>
                                                        <div class="upload-or-con">
                                                            <span><?php esc_html_e('or', 'wp-jobsearch') ?></span>
                                                        </div>
                                                        <a class="jobsearch-drpzon-btn"><i
                                                                    class="jobsearch-icon jobsearch-arrows-2"></i> <?php esc_html_e('Upload Resume', 'wp-jobsearch') ?>
                                                        </a>
                                                    </div>
                                                    <input type="hidden" name="action"
                                                           value="jobsearch_apply_job_with_cv_file">
                                                </form>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    //
                                    if ($cand_resm_coverletr == 'on') { ?>
                                        <div class="jobsearch-user-form jobsearch-user-form-coltwo jobsearch-frmfields-sec aply-cvr-letter">
                                            <ul class="apply-fields-list">
                                                <li class="form-textarea jobsearch-user-form-coltwo-full">
                                                    <label><?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>
                                                        :</label>
                                                    <textarea name="cand_cover_letter"
                                                              placeholder="<?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>"><?php echo($cand_cvr_leter) ?></textarea>
                                                </li>
                                            </ul>
                                        </div>
                                        <?php
                                    }

                                    echo apply_filters('jobsearch_applying_job_after_cv_upload_file', '');
                                    echo apply_filters('jobsearch_applying_job_before_apply', '');

                                    ?>
                                    <a href="javascript:void(0);"
                                       class="<?php echo esc_html($p_classes_str); ?> jobsearch-applyjob-btn jobsearch-apply-btn-<?php echo absint($p_rand_id); ?> <?php echo esc_html($p_classes); ?>" <?php echo(!is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '') ?>
                                       data-randid="<?php echo absint($p_rand_id); ?>"
                                       data-jobid="<?php echo absint($p_job_id); ?>"
                                       data-btnafterlabel="<?php echo esc_html($p_btn_after_label) ?>"
                                       data-btnbeforelabel="<?php echo esc_html($p_btn_text) ?>"><?php echo esc_html($p_btn_text) ?></a>
                                    <small class="apply-bmsg"></small>
                                </div>
                            </div>

                        <?php } else {

                            $cand_resm_coverletr = isset($jobsearch_plugin_options['cand_resm_cover_letr']) ? $jobsearch_plugin_options['cand_resm_cover_letr'] : '';


                            ob_start();
                            if ($cand_resm_coverletr == 'on') {
                                if ($apply_without_login == 'on' && !is_user_logged_in()) {
                                    echo self::job_application_quick_apply_form_without_login($job_id);
                                } else if (!is_user_logged_in()) {
                                    $ferd_classes = 'jobsearch-open-signin-tab jobsearch-wredirct-url'; ?>
                                    <a href="javascript:void(0);"
                                       class="<?php echo esc_html($classes); ?> <?php echo($is_applied || (!is_user_logged_in()) ? '' : 'jobsearch-modelsimpapply-btn-' . $rand_id) ?> <?php echo($ferd_classes) ?>"
                                        <?php echo(!is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '') ?>><?php echo !empty($before_icon) ? '<i class=' . $before_icon . '></i>' : ''; ?><?php echo esc_html($btn_text) ?></a>
                                <?php } else {
                                    //
                                    $popup_args = array(
                                        'p_job_id' => $job_id,
                                        'p_rand_id' => $rand_id,
                                        'p_btn_text' => $btn_text,
                                        'p_classes' => $classes,
                                        'p_classes_str' => $classes_str,
                                        'p_btn_after_label' => $btn_after_label,
                                        'this_wredirct_url' => $this_wredirct_url,
                                    );
                                    $user_id = get_current_user_id();
                                    $candidate_id = jobsearch_get_user_candidate_id($user_id);

                                    extract(shortcode_atts(array(
                                        'p_job_id' => '',
                                        'p_rand_id' => '',
                                        'p_btn_text' => '',
                                        'p_classes' => '',
                                        'p_classes_str' => '',
                                        'p_btn_after_label' => '',
                                        'this_wredirct_url' => '',
                                    ), $popup_args));


                                    $cand_cvr_leter = get_post_meta($candidate_id, 'jobsearch_field_resume_cover_letter', true);
                                    ?>

                                    <div class="jobsearch-user-form jobsearch-user-form-coltwo jobsearch-frmfields-sec aply-cvr-letter">
                                        <ul class="apply-fields-list">
                                            <li class="form-textarea jobsearch-user-form-coltwo-full">
                                                <label><?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>
                                                    :</label>
                                                <textarea name="cand_cover_letter"
                                                          placeholder="<?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>"><?php echo($cand_cvr_leter) ?></textarea>
                                            </li>
                                        </ul>
                                    </div>
                                    <a href="javascript:void(0);"
                                       class="<?php echo esc_html($p_classes_str); ?> jobsearch-apply-btn-<?php echo absint($p_rand_id); ?> <?php echo esc_html($p_classes); ?>" <?php echo(!is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '') ?>
                                       data-randid="<?php echo absint($p_rand_id); ?>"
                                       data-jobid="<?php echo absint($p_job_id); ?>"
                                       data-btnafterlabel="<?php echo esc_html($p_btn_after_label) ?>"
                                       data-btnbeforelabel="<?php echo esc_html($p_btn_text) ?>"><?php echo esc_html($p_btn_text) ?></a>
                                    <small class="apply-bmsg"></small>

                                <?php } ?>

                            <?php } else { ?>
                                <a href="javascript:void(0);"
                                   class="<?php echo esc_html($classes_str); ?> jobsearch-apply-btn-<?php echo absint($rand_id); ?> <?php echo esc_html($classes); ?>" <?php echo(!is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '') ?>
                                   data-randid="<?php echo absint($rand_id); ?>"
                                   data-jobid="<?php echo absint($job_id); ?>"
                                   data-btnafterlabel="<?php echo esc_html($btn_after_label) ?>"
                                   data-btnbeforelabel="<?php echo esc_html($btn_text) ?>"><?php echo !empty($before_icon) ? '<i class="' . $before_icon . '"></i>' : ''; ?><?php echo esc_html($btn_text) ?></a>
                                <small class="apply-bmsg"></small>
                                <?php
                            }
                            $appbtn_html = ob_get_clean();
                            echo apply_filters('jobsearch_jobaplybtn_simple_default', $appbtn_html, $classes_str, $rand_id, $classes, $job_id, $btn_after_label, $btn_text);


                            //
                        }
                    }
                }
            }

            $html .= ob_get_clean();
            return $html;
        }

        public
        static function job_application_quick_apply_form_without_login($job_id)
        {
            global $jobsearch_plugin_options;
            $phone_validation_type = isset($jobsearch_plugin_options['intltell_phone_validation']) ? $jobsearch_plugin_options['intltell_phone_validation'] : '';
            $rand_num = rand(100000, 9999999);
            $apply_without_login = isset($jobsearch_plugin_options['job-apply-without-login']) ? $jobsearch_plugin_options['job-apply-without-login'] : '';
            if ($apply_without_login == 'on' && !is_user_logged_in()) {

                if ($phone_validation_type == 'on') {
                    wp_enqueue_script('jobsearch-intlTelInput');
                }

                $wout_fields_sort = isset($jobsearch_plugin_options['aplywout_login_fields_sort']) ? $jobsearch_plugin_options['aplywout_login_fields_sort'] : '';
                $wout_fields_sort = isset($wout_fields_sort['fields']) ? $wout_fields_sort['fields'] : '';

                $popup_args = array(
                    'job_id' => $job_id,
                    'rand_num' => $rand_num,
                    'wout_fields_sort' => $wout_fields_sort,
                );

                extract(shortcode_atts(array(
                    'job_id' => '',
                    'rand_num' => '',
                    'wout_fields_sort' => '',
                ), $popup_args));

                $phone_validation_type = isset($jobsearch_plugin_options['intltell_phone_validation']) ? $jobsearch_plugin_options['intltell_phone_validation'] : '';

                $file_sizes_arr = array(
                    '300' => __('300KB', 'wp-jobsearch'),
                    '500' => __('500KB', 'wp-jobsearch'),
                    '750' => __('750KB', 'wp-jobsearch'),
                    '1024' => __('1Mb', 'wp-jobsearch'),
                    '2048' => __('2Mb', 'wp-jobsearch'),
                    '3072' => __('3Mb', 'wp-jobsearch'),
                    '4096' => __('4Mb', 'wp-jobsearch'),
                    '5120' => __('5Mb', 'wp-jobsearch'),
                    '10120' => __('10Mb', 'wp-jobsearch'),
                    '50120' => __('50Mb', 'wp-jobsearch'),
                    '100120' => __('100Mb', 'wp-jobsearch'),
                    '200120' => __('200Mb', 'wp-jobsearch'),
                    '300120' => __('300Mb', 'wp-jobsearch'),
                    '500120' => __('500Mb', 'wp-jobsearch'),
                    '1000120' => __('1Gb', 'wp-jobsearch'),
                );
                $cvfile_size = '5120';
                $cvfile_size_str = __('5 Mb', 'wp-jobsearch');
                $cand_cv_file_size = isset($jobsearch_plugin_options['cand_cv_file_size']) ? $jobsearch_plugin_options['cand_cv_file_size'] : '';
                if (isset($file_sizes_arr[$cand_cv_file_size])) {
                    $cvfile_size = $cand_cv_file_size;
                    $cvfile_size_str = $file_sizes_arr[$cand_cv_file_size];
                }
                $filesize_act = ceil($cvfile_size / 1024);

                $cand_files_types = isset($jobsearch_plugin_options['cand_cv_types']) ? $jobsearch_plugin_options['cand_cv_types'] : '';

                if (empty($cand_files_types)) {
                    $cand_files_types = array(
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/pdf',
                    );
                }
                $sutable_files_arr = array();
                $file_typs_comarr = array(
                    'text/plain' => __('text', 'wp-jobsearch'),
                    'image/jpeg' => __('jpeg', 'wp-jobsearch'),
                    'image/png' => __('png', 'wp-jobsearch'),
                    'application/msword' => __('doc', 'wp-jobsearch'),
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => __('docx', 'wp-jobsearch'),
                    'application/vnd.ms-excel' => __('xls', 'wp-jobsearch'),
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => __('xlsx', 'wp-jobsearch'),
                    'application/pdf' => __('pdf', 'wp-jobsearch'),
                );
                foreach ($file_typs_comarr as $file_typ_key => $file_typ_comar) {
                    if (in_array($file_typ_key, $cand_files_types)) {
                        $sutable_files_arr[] = '.' . $file_typ_comar;
                    }
                }
                $sutable_files_str = implode(', ', $sutable_files_arr);

                ?>
                <div class="jobsearch-quick-apply-form">
                    <div class="jobsearch-modal-title-box">
                        <h2><?php esc_html_e('Apply for this Job', 'wp-jobsearch') ?></h2>
                        <span><?php esc_html_e('Your Contact Info', 'wp-jobsearch') ?></span>
                    </div>
                    <form id="apply-form-<?php echo absint($rand_num) ?>" method="post">
                        <div class="jobsearch-user-form jobsearch-user-form-coltwo">
                            <ul class="apply-fields-list">
                                <?php
                                if (isset($wout_fields_sort['name'])) {
                                    foreach ($wout_fields_sort as $field_sort_key => $field_sort_val) {
                                        $field_name_swich_key = 'aplywout_log_f' . $field_sort_key . '_swch';
                                        $field_name_swich = isset($jobsearch_plugin_options[$field_name_swich_key]) ? $jobsearch_plugin_options[$field_name_swich_key] : '';
                                        if ($field_sort_key == 'name' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                            ?>
                                            <li>
                                                <label><?php esc_html_e('First Name:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                       name="pt_user_fname" type="text"
                                                       placeholder="<?php esc_html_e('First Name', 'wp-jobsearch') ?>">
                                            </li>
                                            <li>
                                                <label><?php esc_html_e('Last Name:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                       name="pt_user_lname" type="text"
                                                       placeholder="<?php esc_html_e('Last Name', 'wp-jobsearch') ?>">
                                            </li>
                                        <?php } else if ($field_sort_key == 'email') { ?>
                                            <li>
                                                <label><?php esc_html_e('Email: *', 'wp-jobsearch') ?></label>
                                                <input class="required" name="user_email" type="text"
                                                       placeholder="<?php esc_html_e('Email Address', 'wp-jobsearch') ?>">
                                            </li>
                                            <?php
                                        } else if ($field_sort_key == 'phone' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                            ?>
                                            <li>
                                                <label><?php esc_html_e('Phone:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                <?php
                                                if ($phone_validation_type == 'on') {
                                                    $rand_numb = rand(100000000, 999999999);
                                                    $itltell_input_ats = array(
                                                        'sepc_name' => 'user_phone',
                                                        'set_condial_intrvl' => 'yes',
                                                    );
                                                    jobsearch_phonenum_itltell_input('pt_user_phone', $rand_numb, '', $itltell_input_ats);
                                                } else {
                                                    ?>
                                                    <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                           name="user_phone" type="tel"
                                                           placeholder="<?php esc_html_e('Phone Number', 'wp-jobsearch') ?>">
                                                    <?php
                                                }
                                                ?>
                                            </li>
                                            <?php
                                        } else if ($field_sort_key == 'current_jobtitle' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                            ?>
                                            <li>
                                                <label><?php esc_html_e('Current Job Title:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                       name="user_job_title" type="text"
                                                       placeholder="<?php esc_html_e('Current Job Title', 'wp-jobsearch') ?>">
                                            </li>
                                            <?php
                                        } else if ($field_sort_key == 'current_salary' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                            ?>
                                            <li>
                                                <label><?php esc_html_e('Current Salary:', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                <input class="<?php echo($field_name_swich == 'on_req' ? 'required-apply-field' : 'required') ?>"
                                                       name="user_salary" type="text"
                                                       placeholder="<?php esc_html_e('Current Salary', 'wp-jobsearch') ?>">
                                            </li>
                                            <?php
                                        } else if ($field_sort_key == 'custom_fields' && $field_name_swich == 'on') {
                                            do_action('jobsearch_form_custom_fields_load', 0, 'candidate');
                                        } else if ($field_sort_key == 'cv_attach' && ($field_name_swich == 'on' || $field_name_swich == 'on_req')) {
                                            ?>
                                            <li class="jobsearch-user-form-coltwo-full">
                                                <div id="jobsearch-upload-cv-main"
                                                     class="jobsearch-upload-cv jobsearch-applyjob-upload-cv">
                                                    <label><?php esc_html_e('Resume', 'wp-jobsearch') ?><?php echo($field_name_swich == 'on_req' ? ' *' : '') ?></label>
                                                    <div class="jobsearch-drpzon-con jobsearch-drag-dropcustom">
                                                        <div id="cvFilesDropzone" class="dropzone"
                                                             ondragover="jobsearch_dragover_evnt(event)"
                                                             ondragleave="jobsearch_leavedrop_evnt(event)"
                                                             ondrop="jobsearch_ondrop_evnt(event)">
                                                            <input type="file" id="cand_cv_filefield"
                                                                   class="jobsearch-upload-btn <?php echo($field_name_swich == 'on_req' ? 'cv_is_req' : '') ?>"
                                                                   name="candidate_cv_file"
                                                                   onchange="jobsearchFileContainerChangeFile(event)">
                                                            <div class="fileContainerFileName"
                                                                 ondrop="jobsearch_ondrop_evnt(event)"
                                                                 id="fileNameContainer">
                                                                <div class="dz-message jobsearch-dropzone-template">
                                                                                <span class="upload-icon-con"><i
                                                                                            class="jobsearch-icon jobsearch-upload"></i></span>
                                                                    <strong><?php esc_html_e('Drop a resume file or click to upload.', 'wp-jobsearch') ?></strong>
                                                                    <div class="upload-inffo"><?php printf(__('To upload file size is <span>(Max %s)</span> <span class="uplod-info-and">and</span> allowed file types are <span>(%s)</span>', 'wp-jobsearch'), $cvfile_size_str, $sutable_files_str) ?></div>
                                                                    <div class="upload-or-con">
                                                                        <span><?php esc_html_e('or', 'wp-jobsearch') ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <a class="jobsearch-drpzon-btn"><i
                                                                        class="jobsearch-icon jobsearch-arrows-2"></i> <?php esc_html_e('Upload Resume', 'wp-jobsearch') ?>
                                                            </a>
                                                        </div>
                                                        <script>
                                                            jQuery('#cvFilesDropzone').find('input[name=candidate_cv_file]').css({
                                                                position: 'absolute',
                                                                width: '100%',
                                                                height: '100%',
                                                                top: '0',
                                                                left: '0',
                                                                opacity: '0',
                                                                'z-index': '9',
                                                            });

                                                            function jobsearchFileContainerChangeFile(e) {
                                                                var the_show_msg = '<?php esc_html_e('No file has been selected', 'wp-jobsearch') ?>';
                                                                if (e.target.files.length > 0) {
                                                                    var slected_file_name = e.target.files[0].name;
                                                                    the_show_msg = '<?php esc_html_e('The file', 'wp-jobsearch') ?> "' + slected_file_name + '" <?php esc_html_e('has been selected', 'wp-jobsearch') ?>';
                                                                }
                                                                document.getElementById('cvFilesDropzone').classList.remove('fileContainerDragOver');
                                                                try {
                                                                    droppedFiles = document.getElementById('cand_cv_filefield').files;
                                                                    document.getElementById('fileNameContainer').textContent = the_show_msg;
                                                                } catch (error) {
                                                                    ;
                                                                }
                                                                try {
                                                                    aName = document.getElementById('cand_cv_filefield').value;
                                                                    if (aName !== '') {
                                                                        document.getElementById('fileNameContainer').textContent = the_show_msg;
                                                                    }
                                                                } catch (error) {
                                                                    ;
                                                                }
                                                            }

                                                            function jobsearch_ondrop_evnt(e) {
                                                                var the_show_msg = '<?php esc_html_e('No file has been selected', 'wp-jobsearch') ?>';
                                                                if (e.target.files.length > 0) {
                                                                    var slected_file_name = e.target.files[0].name;
                                                                    the_show_msg = '<?php esc_html_e('The file', 'wp-jobsearch') ?> "' + slected_file_name + '" <?php esc_html_e('has been selected', 'wp-jobsearch') ?>';
                                                                }
                                                                document.getElementById('cvFilesDropzone').classList.remove('fileContainerDragOver');
                                                                try {
                                                                    droppedFiles = e.dataTransfer.files;
                                                                    document.getElementById('fileNameContainer').textContent = the_show_msg;
                                                                } catch (error) {
                                                                    ;
                                                                }
                                                            }

                                                            function jobsearch_dragover_evnt(e) {
                                                                document.getElementById('cvFilesDropzone').classList.add('fileContainerDragOver');
                                                                e.preventDefault();
                                                                e.stopPropagation();
                                                            }

                                                            function jobsearch_leavedrop_evnt(e) {
                                                                document.getElementById('cvFilesDropzone').classList.remove('fileContainerDragOver');
                                                            }
                                                        </script>
                                                    </div>
                                                </div>
                                            </li>
                                            <?php
                                        }
                                    }
                                    $cand_resm_coverletr = isset($jobsearch_plugin_options['cand_resm_cover_letr']) ? $jobsearch_plugin_options['cand_resm_cover_letr'] : '';
                                    if ($cand_resm_coverletr == 'on') {
                                        ?>
                                        <li class="form-textarea jobsearch-user-form-coltwo-full">
                                            <label><?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>
                                                :</label>
                                            <textarea name="cand_cover_letter"
                                                      placeholder="<?php esc_html_e('Cover Letter', 'wp-jobsearch') ?>"></textarea>
                                        </li>
                                        <?php
                                    }
                                } else {
                                    //
                                }
                                ?>
                                <li class="jobsearch-user-form-coltwo-full">
                                    <input type="hidden" name="action"
                                           value="<?php echo apply_filters('jobsearch_apply_btn_action_without_reg', 'jobsearch_job_apply_without_login') ?>">
                                    <input type="hidden" name="job_id"
                                           value="<?php echo absint($job_id) ?>">
                                    <?php jobsearch_terms_and_con_link_txt() ?>
                                    <input class="<?php echo apply_filters('jobsearch_apply_btn_class_without_reg', 'jobsearch-apply-woutreg-btn') ?>"
                                           data-id="<?php echo absint($rand_num) ?>" type="submit"
                                           value="<?php esc_html_e('Apply Job', 'wp-jobsearch') ?>">
                                    <div class="form-loader"></div>
                                </li>
                            </ul>
                            <div class="apply-job-form-msg"></div>
                        </div>
                    </form>
                </div>
                <?php

            }
        }
    }

}
global $Jobsearch_QuickJobApplyLoad;
$Jobsearch_QuickJobApplyLoad = new Jobsearch_QuickJobApplyLoad();