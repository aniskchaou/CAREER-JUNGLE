<?php
global $post, $jobsearch_plugin_options;
$job_id = $post->ID;
$plugin_default_view = isset($jobsearch_plugin_options['jobsearch-default-page-view']) ? $jobsearch_plugin_options['jobsearch-default-page-view'] : 'full';
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

$job_employer_id = get_post_meta($post->ID, 'jobsearch_field_job_posted_by', true); // get job employer
wp_enqueue_script('jobsearch-job-functions-script');
$employer_cover_image_src_style_str = '';
if ($job_employer_id != '') {
    if (class_exists('JobSearchMultiPostThumbnails')) {
        $employer_cover_image_src = JobSearchMultiPostThumbnails::get_post_thumbnail_url('employer', 'cover-image', $job_employer_id);
        if ($employer_cover_image_src != '') {
            $employer_cover_image_src_style_str = ' style="background:url(' . esc_url($employer_cover_image_src) . ') center/cover no-repeat"';
        }
    }
}
if ($employer_cover_image_src_style_str == '') {
    $emp_def_cvrimg = isset($jobsearch_plugin_options['emp_default_coverimg']['url']) && $jobsearch_plugin_options['emp_default_coverimg']['url'] != '' ? $jobsearch_plugin_options['emp_default_coverimg']['url'] : '';
    $employer_cover_image_src_style_str = ' style="background:url(' . esc_url($emp_def_cvrimg) . ') center/cover no-repeat"';
}
//
$social_share_allow = isset($jobsearch_plugin_options['job_detail_soc_share']) ? $jobsearch_plugin_options['job_detail_soc_share'] : '';
$all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
$job_views_publish_date = isset($jobsearch_plugin_options['job_views_publish_date']) ? $jobsearch_plugin_options['job_views_publish_date'] : '';
//
$job_apps_count_switch = isset($jobsearch_plugin_options['job_detail_apps_count']) ? $jobsearch_plugin_options['job_detail_apps_count'] : '';
$job_views_count_switch = isset($jobsearch_plugin_options['job_detail_views_count']) ? $jobsearch_plugin_options['job_detail_views_count'] : '';
$job_shortlistbtn_switch = isset($jobsearch_plugin_options['job_detail_shrtlist_btn']) ? $jobsearch_plugin_options['job_detail_shrtlist_btn'] : '';
$subheader_employer_bg_color = isset($jobsearch_plugin_options['careerfy-emp-img-overlay-bg-color']) ? $jobsearch_plugin_options['careerfy-emp-img-overlay-bg-color'] : '';
if (isset($subheader_employer_bg_color['rgba'])) {
    $subheader_bg_color = $subheader_employer_bg_color['rgba'];
}
ob_start();
?>
    <!-- SubHeader -->
    <div class="jobsearch-job-subheader"<?php echo force_balance_tags($employer_cover_image_src_style_str); ?>>
        <span class="jobsearch-banner-transparent"
              style="background: <?php echo !empty($subheader_bg_color) ? $subheader_bg_color : 'rgb(48, 56, 68, 0.50)' ?>"></span>
        <div class="jobsearch-plugin-default-container">
            <div class="jobsearch-row">
                <div class="jobsearch-column-12">
                </div>
            </div>
        </div>
    </div>
    <!-- SubHeader -->

    <!-- Main Content -->
    <div class="jobsearch-main-content">
        <!-- Main Section -->
        <div class="jobsearch-main-section">
            <div class="jobsearch-plugin-default-container" <?php echo($plugin_default_view_with_str); ?>>
                <div class="jobsearch-row">
                    <?php
                    while (have_posts()) : the_post();
                        $post_id = $post->ID;

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
                        <!-- Job Detail List -->
                        <div class="jobsearch-column-12">
                            <div class="jobsearch-typo-wrap">
                                <figure class="jobsearch-jobdetail-list">

                                    <?php if ($post_thumbnail_src != '') { ?>
                                        <span class="jobsearch-jobdetail-listthumb">
                                        <?php jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'job_listv1') ?>
                                        <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">

                                        <?php
                                        if ($jobsearch_job_featured == 'on') {
                                            $featured_class = !class_exists('Careerfy_framework') ? 'promotepof-badge' : 'careerfy-jobli-medium3';
                                            ?>
                                            <span class="<?php echo $featured_class ?>"><i
                                                        class="fa fa-star"></i></span>
                                        <?php } ?>
                                    </span>
                                    <?php } ?>

                                    <figcaption>
                                        <h2><?php echo jobsearch_esc_html(force_balance_tags(get_the_title())); ?></h2>
                                        <?php
                                        ob_start();
                                        ?>
                                        <span>
                                            <?php
                                            ob_start();
                                            if ($job_type_str != '') {
                                                echo force_balance_tags($job_type_str);
                                            }
                                            $jobtype_html = ob_get_clean();
                                            echo apply_filters('jobsearch_job_indetail_jobtype_html', $jobtype_html, $job_id, 'default_view');

                                            if ($company_name != '') {
                                                ob_start();
                                                echo force_balance_tags($company_name);
                                                $comp_name_html = ob_get_clean();
                                                echo apply_filters('jobsearch_empname_in_jobdetail', $comp_name_html, $job_id, 'view1');
                                            }
                                            if ($jobsearch_job_posted_ago != '' && $job_views_publish_date == 'on') {
                                                ?>
                                                <small class="jobsearch-jobdetail-postinfo"><?php echo esc_html($jobsearch_job_posted_ago); ?></small>
                                                <?php
                                            }

                                            if ($sectors_enable_switch == 'on') {
                                                echo apply_filters('jobsearch_jobdetail_sector_str_html', $sector_str, $job_id);
                                            }
                                            ?>
                                        </span>
                                        <?php
                                        do_action('jobsearch_job_indetail_before_locat_options', $job_id, 'default_view');
                                        ?>
                                        <ul class="jobsearch-jobdetail-options">
                                            <?php
                                            ob_start();
                                            if ((!empty($get_job_location) || ($locations_lat != '' && $locations_lng != '')) && $all_location_allow == 'on') {
                                                $view_map_loc = urlencode($get_job_location);
                                                if ($locations_lat != '' && $locations_lng != '') {
                                                    $view_map_loc = urlencode($locations_lat . ',' . $locations_lng);
                                                }
                                                $google_mapurl = 'https://www.google.com/maps/search/' . $view_map_loc;
                                                ?>
                                                <li>
                                                    <?php if (!empty($get_job_location)) { ?>
                                                        <i class="fa fa-map-marker"></i> <?php echo jobsearch_esc_html($get_job_location); ?>
                                                        <a href="<?php echo($google_mapurl); ?>" target="_blank"
                                                           class="jobsearch-jobdetail-view"><?php echo esc_html__('View on Map', 'wp-jobsearch') ?></a>
                                                    <?php } ?>
                                                </li>
                                                <?php
                                            }
                                            $loc_html = ob_get_clean();
                                            echo apply_filters('jobsearch_jobdetail_loctext_html', $loc_html, $job_id, 'default_view');

                                            if ($jobsearch_job_posted_formated != '' && $job_views_publish_date == 'on') {
                                                ?>
                                                <li>
                                                    <i class="jobsearch-icon jobsearch-calendar"></i> <?php echo esc_html__('Post Date', 'wp-jobsearch') ?>
                                                    : <?php echo jobsearch_esc_html($jobsearch_job_posted_formated); ?>
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
                                                : <?php echo jobsearch_esc_html($jobsearch_last_date_formated); ?>
                                                </li><?php
                                            }
                                            if ($job_salary != '') {
                                                ?>
                                                <li>
                                                    <i class="fa fa-money"></i> <?php printf(esc_html__('Salary: %s', 'wp-jobsearch'), $job_salary) ?>
                                                </li>
                                                <?php
                                            }

                                            if (isset($job_apply_type) && $job_apply_type != 'external' && $job_apps_count_switch == 'on') { ?>
                                                <li>
                                                    <i class="jobsearch-icon jobsearch-summary"></i> <?php printf(esc_html__('Applications %s', 'wp-jobsearch'), $job_applicants_count) ?>
                                                </li>
                                                <?php
                                            }
                                            if ($job_views_count_switch == 'on') {
                                                ?>
                                                <li>
                                                    <a><i class="jobsearch-icon jobsearch-view"></i> <?php echo esc_html__('View(s)', 'wp-jobsearch') ?> <?php echo absint($job_views_count); ?>
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                        <?php
                                        if ($job_shortlistbtn_switch == 'on') {
                                            // wrap in this this due to enquire arrange button style.
                                            $before_label = esc_html__('Shortlist', 'careerfy');
                                            $after_label = esc_html__('Shortlisted', 'careerfy');
                                            $book_mark_args = array(
                                                'before_label' => $before_label,
                                                'after_label' => $after_label,
                                                'before_icon' => 'careerfy-icon careerfy-add-list',
                                                'after_icon' => 'careerfy-icon careerfy-add-list',
                                                'anchor_class' => 'careerfy-jobdetail-btn active',
                                                'view' => 'job_detail_3',
                                                'job_id' => $job_id,
                                            );
                                            do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args);
                                        }

                                        $popup_args = array(
                                            'job_id' => $job_id,
                                        );
                                        do_action('jobsearch_job_send_to_email_filter', $popup_args);

                                        //
                                        if ($social_share_allow == 'on') {
                                            wp_enqueue_script('jobsearch-addthis');
                                            ?>
                                            <ul class="jobsearch-jobdetail-media">
                                                <li><span><?php esc_html_e('Share:', 'wp-jobsearch') ?></span></li>
                                                <li><a href="javascript:void(0);" data-original-title="facebook"
                                                       class="jobsearch-icon jobsearch-facebook-logo-in-circular-button-outlined-social-symbol addthis_button_facebook"></a>
                                                </li>
                                                <li><a href="javascript:void(0);" data-original-title="twitter"
                                                       class="jobsearch-icon jobsearch-twitter-circular-button addthis_button_twitter"></a>
                                                </li>
                                                <li><a href="javascript:void(0);" data-original-title="linkedin"
                                                       class="jobsearch-icon jobsearch-linkedin addthis_button_linkedin"></a>
                                                </li>
                                                <li><a href="javascript:void(0);" data-original-title="share_more"
                                                       class="jobsearch-icon jobsearch-plus addthis_button_compact"></a>
                                                </li>
                                            </ul>
                                            <?php
                                        }
                                        $job_info_output = ob_get_clean();
                                        echo apply_filters('jobsearch_job_detail_content_info', $job_info_output, $job_id);
                                        ?>
                                    </figcaption>
                                </figure>
                            </div>
                        </div>
                        <!-- Job Detail List -->

                        <!-- Job Detail Content -->
                        <div class="jobsearch-column-8 jobsearch-typo-wrap">

                            <div class="jobsearch-jobdetail-content">
                                <?php
                                ob_start();
                                $cus_fields = array('content' => '');
                                $cus_fields = apply_filters('jobsearch_custom_fields_list', 'job', $post_id, $cus_fields, '<li class="jobsearch-column-4">', '</li>');
                                if (isset($cus_fields['content']) && $cus_fields['content'] != '') {
                                    ?>
                                    <div class="jobsearch-content-title">
                                        <h2><?php echo esc_html__('Job Detail', 'wp-jobsearch') ?></h2>
                                    </div>
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

                                $ad_args = array(
                                    'post_type' => 'job',
                                    'view' => 'view1',
                                    'position' => 'b4_desc',
                                );
                                jobsearch_detail_common_ad_code($ad_args);

                                if ($job_content != '') {
                                    ob_start();
                                    ?>
                                    <div class="jobsearch-content-title">
                                        <h2><?php echo esc_html__('Job Description', 'wp-jobsearch') ?></h2>
                                    </div>
                                    <div class="jobsearch-description">
                                        <?php
                                        echo jobsearch_esc_wp_editor($job_content);
                                        ?>
                                    </div>
                                    <?php
                                    $job_det_output = ob_get_clean();
                                    echo apply_filters('jobsearch_job_detail_content_detail', $job_det_output, $job_id);
                                }
                                do_action('jobsearch_job_detail_after_description', $job_id);
                                echo apply_filters('jobsearch_job_defdetail_after_detcont_html', '', $job_id, 'filter', 'view1');

                                $ad_args = array(
                                    'post_type' => 'job',
                                    'view' => 'view1',
                                    'position' => 'aftr_desc',
                                );
                                jobsearch_detail_common_ad_code($ad_args);

                                $job_attachments_switch = isset($jobsearch_plugin_options['job_attachments']) ? $jobsearch_plugin_options['job_attachments'] : '';
                                if ($job_attachments_switch == 'on') {
                                    $all_attach_files = get_post_meta($job_id, 'jobsearch_field_job_attachment_files', true);
                                    if (!empty($all_attach_files)) {
                                        ?>
                                        <div class="jobsearch-content-title">
                                            <h2><?php esc_html_e('Attached Files', 'wp-jobsearch') ?></h2>
                                        </div>
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
                                                               oncontextmenu="javascript: return false;"
                                                               onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {
                                                                       return false
                                                                   }
                                                                   ;"
                                                               download="<?php echo($attach_name) ?>"
                                                               class="file-download-icon"><i
                                                                        class="<?php echo($file_icon) ?>"></i> <?php echo($attach_name) ?>
                                                            </a>
                                                            <a href="<?php echo($_attach_file) ?>"
                                                               oncontextmenu="javascript: return false;"
                                                               onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {
                                                                       return false
                                                                   }
                                                                   ;"
                                                               download="<?php echo($attach_name) ?>"
                                                               class="file-download-btn"><?php esc_html_e('Download', 'wp-jobsearch') ?>
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
                                echo apply_filters('jobsearch_job_detailpge_after_skills_html', '', $job_id, 'view1');
                                ?>
                            </div>
                            <?php
                            do_action('jobsearch_job_detail_before_relate_jobs', $job_id, 'view1');
                            //
                            $related_job_html = jobsearch_job_related_post($post_id, esc_html__('Other jobs you may like', 'wp-jobsearch'), 1, 5, 'jobsearch-job-like');
                            echo apply_filters('jobsearch_job_detail_content_related', $related_job_html, $job_id);
                            ?>
                        </div>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                    <!-- Job Detail SideBar -->
                    <aside class="jobsearch-column-4 jobsearch-typo-wrap">

                        <?php
                        echo apply_filters('jobsearch_job_detail_sidebar_bef4_apply', '', $job_id);

                        $ad_args = array(
                            'post_type' => 'job',
                            'view' => 'view1',
                            'position' => 'b4_aply',
                        );
                        jobsearch_detail_common_ad_code($ad_args);

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

                        $ad_args = array(
                            'post_type' => 'job',
                            'view' => 'view1',
                            'position' => 'aftr_aply',
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
                            'view' => 'view1',
                            'position' => 'aftr_map',
                        );
                        jobsearch_detail_common_ad_code($ad_args);

                        $company_job_html = jobsearch_job_related_company_post($post_id, esc_html__('More Jobs From ', 'wp-jobsearch') . jobsearch_esc_html(get_the_title($job_field_user)), 3);
                        echo force_balance_tags($company_job_html);

                        $ad_args = array(
                            'post_type' => 'job',
                            'view' => 'view1',
                            'position' => 'aftr_simjobs',
                        );
                        jobsearch_detail_common_ad_code($ad_args);
                        ?>
                    </aside>
                    <!-- Job Detail SideBar -->
                </div>
            </div>
        </div>
        <!-- Main Section -->
    </div>
    <script>
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


    <!-- Main Content -->
<?php
jobsearch_google_job_posting($job_id);
$dethtml = ob_get_clean();
echo apply_filters('jobsearch_job_detail_pagehtml', $dethtml);
