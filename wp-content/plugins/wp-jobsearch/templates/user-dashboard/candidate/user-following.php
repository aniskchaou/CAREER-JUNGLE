<?php
global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;
$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);

$page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
$page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);

$candidate_id = jobsearch_get_user_candidate_id($user_id);
$emp_det_full_address_switch = true;
$locations_view_type = isset($jobsearch_plugin_options['emp_det_loc_listing']) ? $jobsearch_plugin_options['emp_det_loc_listing'] : '';
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

$reults_per_page = isset($jobsearch_plugin_options['user-dashboard-per-page']) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;

$page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;

if ($candidate_id > 0) {
    $user_followings_list = $user_followings_oarr = array();
    $user_followins_list_str = get_post_meta($candidate_id, 'jobsearch_cand_followins_list', true);
    if ($user_followins_list_str != '') {
        $user_followings_list = explode(',', $user_followins_list_str);
    }
    if (!empty($user_followings_list)) {
        foreach ($user_followings_list as $employer_id) {
            if (get_post_type($employer_id) == 'employer') {
                $user_followings_oarr[] = $employer_id;
            }
        }
    }
    ?>
    <div class="jobsearch-employer-box-section">
        <div class="jobsearch-profile-title">
            <h2><?php esc_html_e('Following', 'wp-jobsearch') ?></h2>
        </div>
        <?php
        if (!empty($user_followings_oarr)) {
            $total_emps = count($user_followings_oarr);
            krsort($user_followings_oarr);

            $start = ($page_num - 1) * ($reults_per_page);
            $offset = $reults_per_page;

            $user_followings_oarr = array_slice($user_followings_oarr, $start, $offset);
            ?>
            <div class="jobsearch-applied-jobs">
                <ul class="jobsearch-row">
                    <?php
                    foreach ($user_followings_oarr as $employer_id) {


                        $employer_address = get_post_meta($employer_id, 'jobsearch_field_location_address', true);
                        if (function_exists('jobsearch_post_city_contry_txtstr')) {
                            $employer_address = jobsearch_post_city_contry_txtstr($employer_id, $loc_view_country, $loc_view_state, $loc_view_city, $emp_det_full_address_switch);
                        }


                        $user_def_avatar_url = '';
                        $user_avatar_id = get_post_thumbnail_id($employer_id);
                        if ($user_avatar_id > 0) {
                            $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
                            $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
                        }
                        $user_def_avatar_url = $user_def_avatar_url == '' ? jobsearch_employer_image_placeholder() : $user_def_avatar_url;
                        
                        $jobsearch_employer_posted = get_post_meta($employer_id, 'jobsearch_field_employer_publish_date', true);
                        $jobsearch_employer_posted = jobsearch_time_elapsed_string($jobsearch_employer_posted);
                        
                        $jargs = array(
                            'post_type' => 'job',
                            'posts_per_page' => 1,
                            'post_status' => 'publish',
                            'order' => 'DESC',
                            'orderby' => 'ID',
                            'meta_query' => array(
                                array(
                                    'key' => 'jobsearch_field_job_posted_by',
                                    'value' => $employer_id,
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'jobsearch_field_job_status',
                                    'value' => 'approved',
                                    'compare' => '=',
                                ),
                            ),
                        );
                        $jargs = apply_filters('jobsearch_employer_totljobs_query_args', $jargs);

                        $jobs_query = new \WP_Query($jargs);

                        $emp_total_jobs = $jobs_query->found_posts;
                        update_post_meta($employer_id, 'jobsearch_field_employer_job_count', absint($emp_total_jobs));

                        $jobsearch_employer_job_count = get_post_meta($employer_id, 'jobsearch_field_employer_job_count', true);
                        
                        if ($jobsearch_employer_job_count > 1) {
                            $jobsearch_employer_job_count_str = absint($jobsearch_employer_job_count) . ' ' . esc_html__('Vacancies', 'wp-jobsearch');
                        } else {
                            $jobsearch_employer_job_count_str = absint($jobsearch_employer_job_count) . ' ' . esc_html__('Vacancy', 'wp-jobsearch');
                        }

                        $sectors = wp_get_post_terms($employer_id, 'sector');
                        $emp_sector = isset($sectors[0]->name) ? $sectors[0]->name : '';

                        if (get_post_type($employer_id) == 'employer') {
                            ?>
                            <li class="jobsearch-column-12">
                                <div class="jobsearch-applied-jobs-wrap">
                                    <a class="jobsearch-applied-jobs-thumb"><img src="<?php echo ($user_def_avatar_url) ?>" alt=""></a>
                                    <div class="jobsearch-applied-jobs-text">
                                        <div class="jobsearch-applied-jobs-left">
                                            <span><?php echo ($jobsearch_employer_job_count_str) ?></span>
                                            <h2 class="jobsearch-pst-title"><a href="<?php echo get_permalink($employer_id) ?>"><?php echo get_the_title($employer_id) ?></a></h2>
                                            <ul>
                                                <?php

                                                if ($employer_address != '') { ?>
                                                    <li><i class="fa fa-map-marker"></i> <?php echo jobsearch_esc_html($employer_address) ?></li>
                                                    <?php
                                                }
                                                if ($emp_sector != '') {
                                                    ?>
                                                    <li><i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i> <a><?php echo ($emp_sector) ?></a></li>
                                                    <?php
                                                }
                                                if ($jobsearch_employer_posted != '') {
                                                    ?>
                                                    <li><i class="jobsearch-icon jobsearch-calendar"></i> <?php echo ($jobsearch_employer_posted) ?></li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                        <a href="javascript:void(0);" class="jobsearch-savedjobs-links jobsearch-delete-followin-emp" data-id="<?php echo ($employer_id) ?>"><i class="jobsearch-icon jobsearch-rubbish"></i></a>
                                        <span class="remove-applied-job-loader"></span>
                                        <a href="<?php echo get_permalink($employer_id) ?>" class="jobsearch-savedjobs-links"><i class="jobsearch-icon jobsearch-view"></i></a>
                                    </div>
                                </div>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
            <?php
            $total_pages = 1;
            if ($total_emps > 0 && $reults_per_page > 0 && $total_emps > $reults_per_page) {
                $total_pages = ceil($total_emps / $reults_per_page);
                ?>
                <div class="jobsearch-pagination-blog">
                    <?php $Jobsearch_User_Dashboard_Settings->pagination($total_pages, $page_num, $page_url) ?>
                </div>
                <?php
            }
        } else {
            echo '<p>' . esc_html__('No record found.', 'wp-jobsearch') . '</p>';
        }
        ?>
    </div>
    <?php
}