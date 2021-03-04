<?php
wp_enqueue_style('careerfy-job-detail-three');
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
            $employer_cover_image_src_style_str = ' style="background:url(' . esc_url($employer_cover_image_src) . ') no-repeat center/cover; padding-top: 100px;"';
        }
    }
}

if ($employer_cover_image_src_style_str == '') {
    $emp_def_cvrimg = isset($jobsearch_plugin_options['emp_default_coverimg']['url']) && $jobsearch_plugin_options['emp_default_coverimg']['url'] != '' ? $jobsearch_plugin_options['emp_default_coverimg']['url'] : '';
    $employer_cover_image_src_style_str = ' style="background:url(' . esc_url($emp_def_cvrimg) . ') no-repeat center/cover; padding-top: 100px;"';
}

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
$subheader_employer_bg_color = isset($jobsearch_plugin_options['careerfy-emp-img-overlay-bg-color']) ? $jobsearch_plugin_options['careerfy-emp-img-overlay-bg-color'] : '';
if (isset($subheader_employer_bg_color['rgba'])) {
    $subheader_bg_color = $subheader_employer_bg_color['rgba'];
}
?>
    <!-- Main Content -->
    <div class="job-three-cover"<?php echo($employer_cover_image_src_style_str); ?>>
        <span class="careerfy-light-transparent"
              style="background: <?php echo !empty($subheader_bg_color) ? $subheader_bg_color : 'rgb(48, 56, 68, 0.50)' ?>"></span>
    </div>
    <div class="careerfy-main-content">

        <!-- Main Section -->
        <div class="careerfy-main-section">
            <div class="container" style="position: relative;">
                <div class="row">
                    <?php
                    while (have_posts()) : the_post();
                        $post_id = $post->ID;
                        $rand_num = rand(1000000, 99999999);
                        $post_thumbnail_id = jobsearch_job_get_profile_image($post_id);
                        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'jobsearch-job-medium');
                        $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
                        $post_thumbnail_src = $post_thumbnail_src == '' ? jobsearch_no_image_placeholder() : $post_thumbnail_src;
                        $post_thumbnail_src = apply_filters('jobsearch_jobemp_image_src', $post_thumbnail_src, $job_id);
                        $application_deadline = get_post_meta($post_id, 'jobsearch_field_job_application_deadline_date', true);
                        $jobsearch_job_posted = get_post_meta($post_id, 'jobsearch_field_job_publish_date', true);

                        $job_max_salary = jobsearch_job_offered_salary($post_id);
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
                        $job_type_str = jobsearch_job_get_all_jobtypes($post_id, '', '', '', '', '', 'span');
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

                        $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

                        $job_applicants_count = !empty($job_applicants_list) ? count($job_applicants_list) : 0;

                        $current_date = strtotime(current_time('d-m-Y H:i:s'));
                        ?>
                        <!-- Job Detail SideBar -->
                        <aside class="careerfy-column-4">
                            <div class="careerfy-typo-wrap">

                                <div class="jobsearch_side_box jobsearch_box_jobdetail_three_apply">
                                    <div class="jobsearch_box_jobdetail_three_apply_wrap">
                                        <?php
                                        if (function_exists('jobsearch_empjobs_urgent_pkg_iconlab')) {
                                            jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'job_listv1');
                                        }
                                        ?>
                                        <?php if ($post_thumbnail_src != '') { ?>
                                            <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">
                                            <?php
                                        }
                                        if ($jobsearch_job_featured == 'on') {
                                            ?>
                                            <span class="careerfy-jobli-medium3"><i class="fa fa-star"></i></span>
                                            <?php
                                        }
                                        if ($company_name != '') {
                                            echo '<h2>';
                                            ob_start();
                                            echo jobsearch_esc_html(force_balance_tags($company_name));
                                            $comp_name_html = ob_get_clean();
                                            echo apply_filters('jobsearch_empname_in_jobdetail', $comp_name_html, $job_id, 'view3');
                                            echo '</h2>';
                                        }
                                        $post_avg_review_args = array(
                                            'post_id' => $job_employer_id,
                                            'prefix' => 'careerfy',
                                            'view' => 'job3',
                                        );
                                        do_action('jobsearch_post_avg_rating', $post_avg_review_args);
                                        ?>
                                        <?php if ($job_views_count_switch == 'on') { ?>
                                            <span class="jobsearch_box_jobdetail_three_apply_view"><i
                                                        class="careerfy-icon careerfy-view"></i> <?php echo absint($job_views_count); ?></span>
                                            <?php
                                        }
                                        $job_apply_deadline_sw = isset($jobsearch_plugin_options['job_appliction_deadline']) ? $jobsearch_plugin_options['job_appliction_deadline'] : '';
                                        $dead_y = 0;
                                        $dead_m = 0;
                                        $dead_d = 0;
                                        if ($job_apply_deadline_sw == 'on' && $application_deadline != '' && $application_deadline > $current_date) {
                                            $dead_y = date('Y', $application_deadline);
                                            $dead_m = date('m', $application_deadline);
                                            $dead_d = date('d', $application_deadline);
                                            ?>
                                            <div class="careerfy-applywith-title">
                                                <small><?php echo esc_html__('Application End', 'careerfy'); ?></small>
                                            </div>
                                            <div id="widget-application-countdown"
                                                 class="jobsearch-box-application-countdown"></div>
                                            <?php
                                        }
                                        ?>
                                        <ul class="jobsearch_box_jobdetail_three_apply_list">
                                            <?php
                                            if (isset($job_max_salary) && !empty($job_max_salary)) {
                                                echo '<li><strong>' . esc_html__('Offered Salary', 'careerfy') . '</strong></li>';
                                                echo '<li>' . $job_max_salary . '</li>';
                                            }
                                            if (isset($jobsearch_job_posted) && !empty($jobsearch_job_posted)) {
                                                echo '<li><strong>' . esc_html__('Posted Date', 'careerfy') . '</strong></li>';
                                                echo '<li>' . ($jobsearch_job_posted_formated) . '</li>';
                                            }
                                            if (isset($application_deadline) && !empty($application_deadline)) {
                                                echo '<li><strong>' . esc_html__('Closed Date', 'careerfy') . '</strong></li>';
                                                echo '<li>' . date_i18n(get_option('date_format'), ($application_deadline)) . '</li>';
                                            }
                                            if (isset($get_job_location) && !empty($get_job_location)) {
                                                echo '<li><strong>' . esc_html__('Location', 'careerfy') . '</strong></li>';
                                                echo '<li>' . ($get_job_location) . '</li>';
                                            }

                                            $jobsearch_employer_job_count = get_post_meta($job_employer_id, 'jobsearch_field_employer_job_count', true);

                                            if (isset($jobsearch_employer_job_count) && !empty($jobsearch_employer_job_count)) {
                                                echo '<li><strong>' . esc_html__('No. Vacancy', 'careerfy') . '</strong></li>';
                                                echo '<li>' . ($jobsearch_employer_job_count) . '</li>';
                                            }
                                            ?>
                                        </ul>
                                        <?php
                                        if ($social_share_allow == 'on') {
                                            ?>
                                            <ul class="jobsearch_box_jobdetail_three_apply_social">
                                                <li><strong><?php echo esc_html__('Share this Job', 'careerfy'); ?>
                                                        : </strong></li>
                                                <li><a href="javascript:void(0);" data-original-title="twitter"
                                                       class="fa fa-twitter addthis_button_twitter"></a></li>
                                                <li><a href="javascript:void(0);" data-original-title="facebook"
                                                       class="fa fa-facebook-f addthis_button_facebook"></a></li>
                                                <li><a href="javascript:void(0);" data-original-title="linkedin"
                                                       class="fa fa-linkedin addthis_button_linkedin"></a></li>
                                                <li><a href="javascript:void(0);" data-original-title="share_more"
                                                       class="jobsearch-icon jobsearch-plus addthis_button_compact"></a>
                                                </li>
                                            </ul>
                                            <?php
                                        }
                                        if ($job_shortlistbtn_switch == 'on') {
                                            // wrap in this this due to enquire arrange button style.
                                            $before_label = esc_html__('Save Job', 'careerfy');
                                            $after_label = esc_html__('Saved', 'careerfy');
                                            $book_mark_args = array(
                                                'before_label' => $before_label,
                                                'after_label' => $after_label,
                                                'before_icon' => 'fa fa-heart-o',
                                                'after_icon' => 'fa fa-heart',
                                                'anchor_class' => 'jobsearch_box_jobdetail_three_apply_btn',
                                                'view' => 'job_detail_3',
                                                'job_id' => $job_id,
                                            );
                                            do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args);
                                        }
                                        $popup_args = array(
                                            'job_id' => $job_id,
                                            'btn_class' => 'jobsearch_box_jobdetail_three_apply_btn last',
                                        );
                                        do_action('jobsearch_job_send_to_email_filter', $popup_args);

                                        ob_start();
                                        echo jobsearch_job_det_applybtn_acthtml('', $job_id, 'page', 'view3');
                                        $apply_bbox = ob_get_clean();
                                        echo apply_filters('jobsearch_job_defdet_applybtn_boxhtml', $apply_bbox, $job_id);
                                        ?>
                                    </div>

                                    <?php
                                    $popup_args = array(
                                        'job_employer_id' => $job_employer_id,
                                        'job_id' => $job_id,
                                        'btn_class' => 'jobsearch_box_jobdetail_three_send_btn',
                                        'btn_text' => esc_html__('Send a message', 'careerfy'),
                                    );
                                    $popup_html = apply_filters('jobsearch_job_send_message_html_filter', '', $popup_args);
                                    echo force_balance_tags($popup_html);
                                    ?>

                                </div>
                                <!--Widget Detail Services -->
                                <?php
                                echo apply_filters('jobsearch_job_detail_sidebar_bef4_apply', '', $job_id);

                                $ad_args = array(
                                    'post_type' => 'job',
                                    'view' => 'view3',
                                    'position' => 'aftr_aply',
                                );
                                jobsearch_detail_common_ad_code($ad_args);

                                ob_start();
                                $cus_fields = array('content' => '');
                                $cus_fields = apply_filters('jobsearch_custom_fields_list', 'job', $post_id, $cus_fields, '<li>', '</li>', '', true, true, true, 'careerfy');
                                if (isset($cus_fields['content']) && $cus_fields['content'] != '') {
                                    ?>
                                    <div class="jobsearch_side_box careerfy-candidatedetail-services">
                                        <ul>
                                            <?php
                                            echo force_balance_tags($cus_fields['content']);
                                            ?>
                                        </ul>
                                    </div>
                                    <?php
                                }
                                $job_fields_output = ob_get_clean();
                                echo apply_filters('jobsearch_job_detail_content_fields', $job_fields_output, $job_id);

                                $ad_args = array(
                                    'post_type' => 'job',
                                    'view' => 'view3',
                                    'position' => 'aftr_cusfilds',
                                );
                                jobsearch_detail_common_ad_code($ad_args);
                                ?>
                                <!--Widget Detail Services -->
                                <!--Widget Similar Jobs -->

                                <?php
                                $company_job_html = jobsearch_job_related_company_post($job_id, esc_html__('Similar Jobs', 'careerfy'), 3, 5, 'view2');
                                echo (force_balance_tags($company_job_html));

                                $ad_args = array(
                                    'post_type' => 'job',
                                    'view' => 'view3',
                                    'position' => 'aftr_simjobs',
                                );
                                jobsearch_detail_common_ad_code($ad_args);
                                ?>

                                <!--Widget Similar Jobs -->
                            </div>
                        </aside>
                        <!--Job Detail SideBar -->
                        <!--Job Detail Content -->
                        <div class="careerfy-column-8">
                            <div class="careerfy-typo-wrap">
                                <div class="careerfy-jobdetail-content">
                                    <div class="careerfy-jobdetail-content-section">
                                        <h1><?php echo force_balance_tags(get_the_title()); ?></h1>
                                        <?php
                                        if ($job_type_str != '') {

                                            echo jobsearch_esc_html(force_balance_tags($job_type_str));

                                        }
                                        echo '<strong>';
                                        if ($company_name != '') {
                                            ob_start();
                                            echo($company_name);
                                            $comp_name_html = ob_get_clean();
                                            echo apply_filters('jobsearch_empname_in_jobdetail', $comp_name_html, $job_id, 'view2');
                                        }
                                        if ($jobsearch_job_posted_ago != '' && $job_views_publish_date == 'on') { ?>
                                            <small><?php echo esc_html($jobsearch_job_posted_ago); ?></small>
                                            <?php
                                        }
                                        echo '</strong>';
                                        ?>
                                    </div>
                                    <?php
                                    $ad_args = array(
                                        'post_type' => 'job',
                                        'view' => 'view3',
                                        'position' => 'b4_desc',
                                    );
                                    jobsearch_detail_common_ad_code($ad_args);

                                    if ($job_content != '') {
                                        ob_start();
                                        ?>
                                        <div class="careerfy-content-title">
                                            <h2><?php echo esc_html__('Job Description', 'careerfy') ?></h2></div>
                                        <div class="jobsearch-description">
                                            <?php echo jobsearch_esc_wp_editor($job_content); ?>
                                        </div>
                                        <?php
                                        $job_det_output = ob_get_clean();
                                        echo apply_filters('jobsearch_job_detail_content_detail', $job_det_output, $job_id);
                                    }
                                    echo apply_filters('jobsearch_job_defdetail_after_detcont_html', '', $job_id, 'filter', 'view3');
                                    $ad_args = array(
                                        'post_type' => 'job',
                                        'view' => 'view3',
                                        'position' => 'aftr_desc',
                                    );
                                    jobsearch_detail_common_ad_code($ad_args);
                                    $job_attachments_switch = isset($jobsearch_plugin_options['job_attachments']) ? $jobsearch_plugin_options['job_attachments'] : '';
                                    if ($job_attachments_switch == 'on') {
                                        $all_attach_files = get_post_meta($job_id, 'jobsearch_field_job_attachment_files', true);
                                        if (!empty($all_attach_files)) {
                                            ?>
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
                                    ?>
                                    <div class="careerfy-jobdetail-tags-style2">
                                        <?php echo force_balance_tags($skills_list); ?>
                                    </div>
                                    <?php
                                    echo apply_filters('jobsearch_job_detailpge_after_skills_html', '', $job_id, 'view2');
                                    ?>
                                </div>

                                <?php
                                do_action('jobsearch_job_detail_before_relate_jobs', $job_id, 'view3');
                                
                                $related_job_html = jobsearch_job_related_post($post_id, esc_html__('Related Jobs', 'careerfy'), 5, 5, '', 'view3');
                                echo ($related_job_html);
                                ?>

                            </div>
                        </div>
                        <!-- Job Detail Content -->
                    <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        </div>
        <!-- Main Section -->

    </div>
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

        <?php
        if ($dead_y != '' && $dead_m != '' && $dead_d != '') {
        ?>
        jQuery(document).ready(function ($) {
            if (jQuery('#widget-application-countdown').length > 0) {
                var austDay = new Date(<?php echo $dead_y; ?>, <?php echo $dead_m; ?> -1, <?php echo $dead_d; ?>, 00, 00, 00);
                jQuery('#widget-application-countdown').countdown({
                    until: austDay,
                    layout: '<span class="countdown-row countdown-show4">{y<}<span class="countdown-section"><span class="countdown-amount">{yn}</span> <span class="countdown-period">{yl}</span></span>{y>}{o<}<span class="countdown-section"><span class="countdown-amount">{on}</span> <span class="countdown-period">{ol}</span></span>{o>}' +
                        '{d<}<span class="countdown-section"><span class="countdown-amount">{dn}</span> <span class="countdown-period"><?php echo esc_html_e('Days', 'careerfy') ?></span></span>{d>}{h<}<span class="countdown-section"><span class="countdown-amount">{hn}</span> <span class="countdown-period"><?php echo esc_html_e('Hours', 'careerfy') ?></span></span>{h>}' +
                        '{m<}<span class="countdown-section"><span class="countdown-amount">{mn}</span> <span class="countdown-period"><?php echo esc_html_e('Minutes', 'careerfy') ?></span></span>{m>}{s<}<span class="countdown-section"><span class="countdown-amount">{sn}</span> <span class="countdown-period"><?php echo esc_html_e('Seconds', 'careerfy') ?></span></span>{s>}</span>'
                });
            }
        });
        <?php
        }
        ?>
    </script>
<?php
jobsearch_google_job_posting($job_id);
