<?php
wp_enqueue_style('careerfy-job-detail-four');
wp_enqueue_script('careerfy-countdown');
wp_enqueue_script('jobsearch-addthis');
global $post, $jobsearch_plugin_options;
$job_id = $post->ID;


$job_employer_id = get_post_meta($post->ID, 'jobsearch_field_job_posted_by', true); // get job employer
wp_enqueue_script('jobsearch-job-functions-script');
$employer_cover_image_src_style_str = '';
if ($job_employer_id != '') {
    if (class_exists('JobSearchMultiPostThumbnails')) {
        $employer_cover_image_src = JobSearchMultiPostThumbnails::get_post_thumbnail_url('employer', 'cover-image', $job_employer_id);
        if ($employer_cover_image_src != '') {
            $employer_cover_image_src_style_str = ' style="background:url(' . esc_url($employer_cover_image_src) . ') no-repeat center/cover;"';
        }
    }
}
if ($employer_cover_image_src_style_str == '') {
    $emp_def_cvrimg = isset($jobsearch_plugin_options['emp_default_coverimg']['url']) && $jobsearch_plugin_options['emp_default_coverimg']['url'] != '' ? $jobsearch_plugin_options['emp_default_coverimg']['url'] : '';
    $employer_cover_image_src_style_str = ' style="background:url(' . esc_url($emp_def_cvrimg) . ') no-repeat center/cover;"';
}
//
$social_share_allow = isset($jobsearch_plugin_options['job_detail_soc_share']) ? $jobsearch_plugin_options['job_detail_soc_share'] : '';

$all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
$job_views_publish_date = isset($jobsearch_plugin_options['job_views_publish_date']) ? $jobsearch_plugin_options['job_views_publish_date'] : '';
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
$job_apps_count_switch = isset($jobsearch_plugin_options['job_detail_apps_count']) ? $jobsearch_plugin_options['job_detail_apps_count'] : '';
$job_views_count_switch = isset($jobsearch_plugin_options['job_detail_views_count']) ? $jobsearch_plugin_options['job_detail_views_count'] : '';
$job_shortlistbtn_switch = isset($jobsearch_plugin_options['job_detail_shrtlist_btn']) ? $jobsearch_plugin_options['job_detail_shrtlist_btn'] : '';
$captcha_switch = isset($jobsearch_plugin_options['captcha_switch']) ? $jobsearch_plugin_options['captcha_switch'] : '';
$job_det_contact_form = isset($jobsearch_plugin_options['job_det_contact_form']) ? $jobsearch_plugin_options['job_det_contact_form'] : '';

while (have_posts()) : the_post();
    $post_id = $post->ID;

    $rand_num = rand(1000000, 99999999);
    $post_thumbnail_id = jobsearch_job_get_profile_image($post_id);
    $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, [300,300]);
    $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
    $post_thumbnail_src = $post_thumbnail_src == '' ? jobsearch_no_image_placeholder([300,300]) : $post_thumbnail_src;
    $post_thumbnail_src = apply_filters('jobsearch_jobemp_image_src', $post_thumbnail_src, $job_id);
    $application_deadline = get_post_meta($post_id, 'jobsearch_field_job_application_deadline_date', true);
    $jobsearch_job_posted = get_post_meta($post_id, 'jobsearch_field_job_publish_date', true);

    $locations_lat = get_post_meta($post_id, 'jobsearch_field_location_lat', true);
    $locations_lng = get_post_meta($post_id, 'jobsearch_field_location_lng', true);

    $job_max_salary = get_post_meta($post_id, 'jobsearch_field_job_max_salary', true);
    $job_salary_sep = get_post_meta($post_id, 'jobsearch_field_job_salary_sep', true);
    $job_salary_deci = get_post_meta($post_id, 'jobsearch_field_job_salary_deci', true);

    $job_max_salary = jobsearch_job_offered_salary($post_id);

    $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

    $jobsearch_job_posted_ago = jobsearch_time_elapsed_string($jobsearch_job_posted, ' ' . esc_html__('posted', 'careerfy') . ' ');
    $jobsearch_job_posted_formated = '';
    if ($jobsearch_job_posted != '') {
        $jobsearch_job_posted_formated = date_i18n(get_option('date_format'), ($jobsearch_job_posted));
    }
    $get_job_location = get_post_meta($post_id, 'jobsearch_field_location_address', true);
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
    $job_type_str = jobsearch_job_get_all_jobtypes($post_id, 'careerfy-jobdetail-four-list-status', '', '', '', '', 'span');
    $sector_str = jobsearch_job_get_all_sectors($post_id, '', ' ' . esc_html__('in', 'careerfy') . ' ', '', '<small class="post-in-category">', '</small>');
    $company_name = jobsearch_job_get_company_name($post_id, '');
    $skills_list = jobsearch_job_get_all_skills($post_id);
    $job_obj = get_post($post_id);
    $job_content = isset($job_obj->post_content) ? $job_obj->post_content : '';
    $job_content = apply_filters('the_content', $job_content);
    $job_salary = jobsearch_job_offered_salary($post_id);
    $job_applicants_list = get_post_meta($post_id, 'jobsearch_job_applicants_list', true);
    $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
    $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);

    if (empty($job_applicants_list)) {
        $job_applicants_list = array();
    }
    $job_applicants_count = !empty($job_applicants_list) ? count($job_applicants_list) : 0;

    $current_date = strtotime(current_time('d-m-Y H:i:s'));
    $subheader_employer_bg_color = isset($jobsearch_plugin_options['careerfy-emp-img-overlay-bg-color']) ? $jobsearch_plugin_options['careerfy-emp-img-overlay-bg-color'] : '';
    if (isset($subheader_employer_bg_color['rgba'])) {
        $subheader_bg_color = $subheader_employer_bg_color['rgba'];
    }
    ?>

    <div class="careerfy-jobdetail-four-list" <?php echo($employer_cover_image_src_style_str); ?>>
        <span class="careerfy-jobdetail-four-transparent"
              style="background: <?php echo !empty($subheader_bg_color) ? $subheader_bg_color : 'rgb(48, 56, 68, 0.50)' ?>"></span>
        <div class="container">
            <div class="row">
                <div class="careerfy-column-12">

                    <?php if ($post_thumbnail_src != '') { ?>
                        <figure>
                            <?php
                            if (function_exists('jobsearch_empjobs_urgent_pkg_iconlab')) {
                                jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'job_listv1');
                            }
                            ?>
                            <a href="#"><img src="<?php echo esc_url($post_thumbnail_src) ?>" alt=""></a>
                            <?php if ($jobsearch_job_featured == 'on') { ?>
                                <span class="careerfy-jobli-medium3"><i class="fa fa-star"></i></span>
                            <?php } ?>
                        </figure>
                    <?php }
                    ?>
                    <div class="careerfy-jobdetail-four-list-text">
                        <?php
                        if ($job_type_str != '') {
                            ?>

                            <?php
                            echo force_balance_tags($job_type_str);
                            ?>

                            <?php
                        }
                        ?>
                        <span class="careerfy-jobdetail-four-list-status color"><?php echo esc_html__('Offered Salary', 'careerfy'); ?> : <?php echo($job_max_salary); ?></span>
                        <h1><?php echo get_the_title($post_id); ?></h1>
                        <ul class="careerfy-jobdetail-four-options">

                            <?php
                            if ((!empty($get_job_location) || ($locations_lat != '' && $locations_lng != '')) && $all_location_allow == 'on') {
                                $view_map_loc = urlencode($get_job_location);
                                if ($locations_lat != '' && $locations_lng != '') {
                                    $view_map_loc = urlencode($locations_lat . ',' . $locations_lng);
                                }
                                $google_mapurl = 'https://www.google.com/maps/search/' . $view_map_loc;
                                ?>
                                <li>
                                    <?php
                                    if (!empty($get_job_location)) {
                                        ?>
                                        <i class="fa fa-map-marker"></i> <?php echo jobsearch_esc_html($get_job_location); ?>
                                        <?php
                                    }
                                    ?>
                                    <a href="<?php echo($google_mapurl); ?>" target="_blank"
                                       class="careerfy-jobdetail-view"><?php echo esc_html__('View on Map', 'careerfy') ?></a>
                                </li>
                                <?php
                            }

                            if (!empty($company_name) || !empty($jobsearch_job_posted_ago)) {
                                echo '<li>';
                                if ($company_name != '') {
                                    echo '<i class="careerfy-icon careerfy-building"></i> ';
                                    ob_start();
                                    echo jobsearch_esc_html(force_balance_tags($company_name));
                                    $comp_name_html = ob_get_clean();
                                    echo apply_filters('jobsearch_empname_in_jobdetail', $comp_name_html, $job_id, 'view4');
                                }
                                if ($jobsearch_job_posted_ago != '' && $job_views_publish_date == 'on') {
                                    ?>
                                    <small><?php echo jobsearch_esc_html($jobsearch_job_posted_ago); ?></small>
                                    <?php
                                }
                                echo '</li>';
                            }
                            $jobsearch_last_date_formated = '';
                            if ($application_deadline != '') {
                                $jobsearch_last_date_formated = date_i18n(get_option('date_format'), ($application_deadline));
                            }
                            if ($job_views_count_switch == 'on') {
                                ?>
                                <li><a href="#"><i
                                                class="careerfy-icon careerfy-view"></i> <?php echo esc_html__('Views ', 'careerfy'); ?><?php echo absint($job_views_count); ?>
                                    </a></li>
                                <?php
                            }
                            if (isset($jobsearch_job_posted_formated) && !empty($jobsearch_job_posted_formated)) {
                                ?>
                                <li>
                                <i class="careerfy-icon careerfy-calendar"></i> <?php echo esc_html__('Posted Date ', 'careerfy'); ?>
                                : <?php echo jobsearch_esc_html($jobsearch_job_posted_formated); ?></li><?php
                            }
                            if (isset($jobsearch_last_date_formated) && !empty($jobsearch_last_date_formated)) {
                                ?>
                                <li>
                                <i class="careerfy-icon careerfy-calendar"></i> <?php echo esc_html__('Last Date ', 'careerfy'); ?>
                                : <?php echo jobsearch_esc_html($jobsearch_last_date_formated); ?></li><?php
                            }
                            ?>
                        </ul>
                        <?php

                        if ($job_shortlistbtn_switch == 'on') {
                            // wrap in this this due to enquire arrange button style.
                            $before_label = esc_html__('Save Job', 'careerfy');
                            $after_label = esc_html__('Saved', 'careerfy');
                            $book_mark_args = array(
                                'before_label' => $before_label,
                                'after_label' => $after_label,
                                'before_icon' => 'fa fa-heart-o',
                                'after_icon' => 'fa fa-heart',
                                'view' => 'job_detail_3',
                                'anchor_class' => 'careerfy-jobdetail-four-btn',
                                'job_id' => $job_id,
                            );
                            do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args);
                        }

                        $popup_args = array(
                            'job_id' => $job_id,
                            'btn_class' => 'careerfy-jobdetail-four-btn',
                        );
                        do_action('jobsearch_job_send_to_email_filter', $popup_args);
                        if ($social_share_allow == 'on') { ?>
                            <ul class="careerfy-jobdetail-four-media">
                                <li><span><?php echo esc_html__('Share this Job ', 'careerfy') ?>:</span></li>
                                <li><a href="javascript:void(0);" data-original-title="twitter"
                                       class="fa fa-twitter addthis_button_twitter"></a></li>
                                <li><a href="javascript:void(0);" data-original-title="facebook"
                                       class="fa fa-facebook-f addthis_button_facebook"></a></li>
                                <li><a href="javascript:void(0);" data-original-title="linkedin"
                                       class="fa fa-linkedin addthis_button_linkedin"></a></li>
                                <li><a href="javascript:void(0);" data-original-title="share_more"
                                       class="jobsearch-icon jobsearch-plus addthis_button_compact"></a></li>
                            </ul>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- JobDetail SubHeader Main List -->

    <!-- Main Content -->
    <div class="careerfy-main-content">
        <!-- Main Section -->
        <div class="careerfy-main-section">
            <div class="container">
                <div class="row">
                    <!-- Job Detail Content -->
                    <div class="careerfy-column-8">
                        <div class="careerfy-typo-wrap">
                            <div class="careerfy-jobdetail-content">
                                <?php
                                if ($job_content != '') {
                                    $ad_args = array(
                                        'post_type' => 'job',
                                        'view' => 'view4',
                                        'position' => 'b4_desc',
                                    );
                                    jobsearch_detail_common_ad_code($ad_args);

                                    ob_start();
                                    ?>
                                    <div class="careerfy-content-title">
                                        <h2><?php echo esc_html__('Job Description', 'careerfy') ?></h2></div>
                                    <div class="jobsearch-description">
                                        <?php
                                        echo jobsearch_esc_wp_editor($job_content);
                                        ?>
                                    </div>
                                    <?php
                                    $job_det_output = ob_get_clean();
                                    echo apply_filters('jobsearch_job_detail_content_detail', $job_det_output, $job_id);
                                    //
                                    echo apply_filters('jobsearch_job_defdetail_after_detcont_html', '', $job_id, 'filter', 'view4');

                                    $ad_args = array(
                                        'post_type' => 'job',
                                        'view' => 'view4',
                                        'position' => 'aftr_desc',
                                    );
                                    jobsearch_detail_common_ad_code($ad_args);
                                    //
                                    $job_attachments_switch = isset($jobsearch_plugin_options['job_attachments']) ? $jobsearch_plugin_options['job_attachments'] : '';
                                    if ($job_attachments_switch == 'on') {
                                        $all_attach_files = get_post_meta($job_id, 'jobsearch_field_job_attachment_files', true);
                                        if (!empty($all_attach_files)) { ?>
                                            <div class="jobsearch-content-title">
                                                <h2><?php esc_html_e('Attached Files', 'careerfy') ?></h2></div>
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
                                                        }
                                                        ?>
                                                        <li class="jobsearch-column-4">
                                                            <div class="file-container">
                                                                <a href="<?php echo($_attach_file) ?>"
                                                                   download="<?php echo($attach_name) ?>"
                                                                   class="file-download-icon"><i
                                                                            class="<?php echo($file_icon) ?>"></i> <?php echo($attach_name) ?>
                                                                </a>
                                                                <a href="<?php echo($_attach_file) ?>"
                                                                   download="<?php echo($attach_name) ?>"
                                                                   class="file-download-btn"><?php esc_html_e('Download', 'careerfy') ?>
                                                                    <i class="jobsearch-icon jobsearch-download-arrow"></i></a>
                                                            </div>
                                                        </li>
                                                        <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                                <div class="careerfy-jobdetail-tags-style2">
                                    <?php echo force_balance_tags($skills_list); ?>
                                </div>
                                <div class="careerfy-jobdetail-four-links">
                                    <?php
                                    if ($job_shortlistbtn_switch == 'on') {
                                        // wrap in this this due to enquire arrange button style.
                                        $before_label = esc_html__('Shortlist', 'careerfy');
                                        $after_label = esc_html__('Shortlisted', 'careerfy');
                                        $book_mark_args = array(
                                            'before_label' => $before_label,
                                            'after_label' => $after_label,
                                            'before_icon' => 'careerfy-icon careerfy-heart',
                                            'after_icon' => 'fa fa-heart',
                                            'anchor_class' => 'jobsearch_box_jobdetail_three_apply_btn',
                                            'view' => 'job_detail_3',
                                            'job_id' => $job_id,
                                        );
                                        do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args);
                                    }
                                    ob_start();
                                    echo jobsearch_job_det_applybtn_acthtml('', $job_id, 'page', 'view4');
                                    $apply_bbox = ob_get_clean();
                                    echo apply_filters('jobsearch_job_defdet_applybtn_boxhtml', $apply_bbox, $job_id);
                                    ?>
                                </div>
                                <?php
                                echo apply_filters('jobsearch_job_detailpge_after_skills_html', '', $job_id, 'view4');
                                ?>
                            </div>
                        </div>
                    </div>
                    <!-- Job Detail Content -->
                    <!-- Job Detail SideBar -->
                    <aside class="careerfy-column-4">
                        <div class="careerfy-typo-wrap">
                            <!-- Widget Detail Services -->
                            <?php
                            ob_start();
                            $cus_fields = array('content' => '');
                            $cus_fields = apply_filters('jobsearch_custom_fields_list', 'job', $job_id, $cus_fields, '<li>', '</li>', '', true, true, true, 'careerfy');
                            if (isset($cus_fields['content']) && $cus_fields['content'] != '') { ?>
                                <div class="jobsearch_side_box careerfy-candidatedetail-services">
                                    <ul>
                                        <?php
                                        echo force_balance_tags($cus_fields['content']);
                                        ?>
                                    </ul>
                                </div>
                                <!-- Widget Detail Services -->
                                <?php
                            }
                            $job_fields_output = ob_get_clean();
                            echo apply_filters('jobsearch_job_detail_content_fields', $job_fields_output, $job_id);

                            echo apply_filters('jobsearch_job_detail_sidebar_bef4_apply', '', $job_id, 'view4');

                            $ad_args = array(
                                'post_type' => 'job',
                                'view' => 'view4',
                                'position' => 'aftr_cusfilds',
                            );
                            jobsearch_detail_common_ad_code($ad_args);

                            // map
                            $map_switch_arr = isset($jobsearch_plugin_options['jobsearch-detail-map-switch']) ? $jobsearch_plugin_options['jobsearch-detail-map-switch'] : '';
                            $job_map = false;
                            if (isset($map_switch_arr) && is_array($map_switch_arr) && sizeof($map_switch_arr) > 0) {
                                foreach ($map_switch_arr as $map_switch) {
                                    if ($map_switch == 'job') {
                                        $job_map = true;
                                    }
                                }
                            }
                            if ($job_map) {
                                ?>
                                <div class="jobsearch_side_box jobsearch_box_map">
                                    <?php jobsearch_google_map_with_directions($job_id); ?>
                                </div>

                                <?php
                            }

                            $ad_args = array(
                                'post_type' => 'job',
                                'view' => 'view4',
                                'position' => 'aftr_map',
                            );
                            jobsearch_detail_common_ad_code($ad_args);

                            $send_message_form_rand = rand(1000, 99999);
                            $current_user = wp_get_current_user();
                            $user_id = get_current_user_id();
                            $user_displayname = isset($current_user->display_name) ? $current_user->display_name : '';
                            $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $current_user);

                            $form_arg = array(
                                'user_displayname' => $user_displayname,
                                'job_id' => $job_id
                            );
                            do_action('jobsearch_job_send_message_html', $form_arg);
                            ?>
                        </div>
                    </aside>
                    <!-- Job Detail SideBar -->

                    <div class="careerfy-column-12">
                        <?php
                        do_action('jobsearch_job_detail_before_relate_jobs', $job_id, 'view4');
                        
                        $related_job_html = jobsearch_job_related_post($post_id, esc_html__('Related Jobs', 'careerfy'), 5, 5, '', 'view4');
                        echo $related_job_html;
                        ?>

                        <!-- Job Listings -->
                        <div class="bottom-spacer"></div>
                    </div>

                </div>
            </div>
        </div>
        <!-- Main Section -->

    </div>
<?php
endwhile;
wp_reset_postdata();
?>
    <!-- Main Content -->
    <script>
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
<?php
jobsearch_google_job_posting($job_id);