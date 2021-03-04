<?php
global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;
$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);

$user_email_adr = isset($user_obj->user_email) ? $user_obj->user_email : '';

$page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
$page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);

$candidate_id = jobsearch_get_user_candidate_id($user_id);

$reults_per_page = isset($jobsearch_plugin_options['user-dashboard-per-page']) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;

$page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;

if ($candidate_id > 0) {
    $user_applied_jobs_list = array();
    $user_applied_jobs_liste = get_user_meta($user_id, 'jobsearch-user-jobs-applied-list', true);
    if (!empty($user_applied_jobs_liste)) {
        foreach ($user_applied_jobs_liste as $er_applied_jobs_list_key => $er_applied_jobs_list_val) {
            $job_id = isset($er_applied_jobs_list_val['post_id']) ? $er_applied_jobs_list_val['post_id'] : 0;
            if (get_post_type($job_id) == 'job') {
                $user_applied_jobs_list[$er_applied_jobs_list_key] = $er_applied_jobs_list_val;
            }
        }
    }
    ?>
    <div class="jobsearch-employer-box-section">
        <div class="jobsearch-profile-title">
            <h2><?php echo apply_filters('jobsearch_dash_applid_jobs_tab_main_title', esc_html__('Applied Jobs', 'wp-jobsearch')) ?></h2>
        </div>
        <?php
        if (!empty($user_applied_jobs_list)) {
            $total_jobs = count($user_applied_jobs_list);
            krsort($user_applied_jobs_list);

            $start = ($page_num - 1) * ($reults_per_page);
            $offset = $reults_per_page;

            $user_applied_jobs_list = array_slice($user_applied_jobs_list, $start, $offset);
            ?>
            <div class="jobsearch-applied-jobs">
                <?php
                do_action('jobsearch_cand_dash_apply_jobs_list_b4r', $candidate_id);
                ?>
                <ul class="jobsearch-row">
                    <?php
                    foreach ($user_applied_jobs_list as $job_key => $job_val) {

                        $job_id = isset($job_val['post_id']) ? $job_val['post_id'] : 0;
                        $job_post_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                        $job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                        $job_post_employer = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

                        $get_job_apps_cv_att = get_post_meta($job_id, 'job_apps_cv_att', true);
                        $attach_cv_job = isset($get_job_apps_cv_att[$candidate_id]) ? $get_job_apps_cv_att[$candidate_id] : '';

                        $job_post_user = jobsearch_get_employer_user_id($job_post_employer);

                        $user_def_avatar_url = get_avatar_url($job_post_user, array('size' => 69));
                        $user_avatar_id = get_post_thumbnail_id($job_post_employer);
                        if ($user_avatar_id > 0) {
                            $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
                            $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
                        }
                        $user_def_avatar_url = $user_def_avatar_url == '' ? jobsearch_no_image_placeholder() : $user_def_avatar_url;

                        $sectors = wp_get_post_terms($job_id, 'sector');
                        $job_sector = isset($sectors[0]->name) ? $sectors[0]->name : '';

                        if (get_post_type($job_id) == 'job') {
                            $application_status = jobsearch_get_applicant_status_tarr($candidate_id, $job_id);
                            ob_start();
                            ?>
                            <li class="jobsearch-column-12">
                                <div class="jobsearch-applied-jobs-wrap">
                                    <a class="jobsearch-applied-jobs-thumb"><img src="<?php echo ($user_def_avatar_url) ?>" alt=""></a>
                                    <div class="jobsearch-applied-jobs-text">
                                        <div class="jobsearch-applied-jobs-left">
                                            <span>@ <?php echo get_the_title($job_post_employer) ?></span> <?php echo (isset($application_status['status_str']) ? '<span class="application-status-str ' . $application_status['status'] . '">(' . sprintf(esc_html__('Status: %s', ''), $application_status['status_str']) . ')</span>' : '') ?>
                                            <h2 class="jobsearch-pst-title"><a href="<?php echo get_permalink($job_id) ?>">
                                                <?php echo get_the_title($job_id) ?></a>
                                            </h2>
                                            <ul>
                                                <?php
                                                if ($job_location != '') {
                                                    ?>
                                                    <li><i class="fa fa-map-marker"></i> <?php echo ($job_location) ?></li>
                                                    <?php
                                                }
                                                if ($job_sector != '') {
                                                    ?>
                                                    <li><i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i> <a><?php echo ($job_sector) ?></a></li>
                                                    <?php
                                                }
                                                if ($job_post_date != '') {
                                                    ?>
                                                    <li><i class="jobsearch-icon jobsearch-calendar"></i> <?php echo date_i18n(get_option('date_format'), $job_post_date) ?></li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                        <?php echo apply_filters('jobsearch_userdash_apliedjobs_itm_aftrtxt', '', $job_id, $job_post_employer) ?>
                                        <a href="javascript:void(0);" class="jobsearch-savedjobs-links jobsearch-delete-applied-job" data-id="<?php echo ($job_id) ?>" data-key="<?php echo ($job_key) ?>"><i class="jobsearch-icon jobsearch-rubbish"></i></a>
                                        <span class="remove-applied-job-loader"></span>
                                        <a href="<?php echo get_permalink($job_id) ?>" class="jobsearch-savedjobs-links"><i class="jobsearch-icon jobsearch-view"></i></a>
                                    </div>
                                </div>
                            </li>
                            <?php
                            $apply_job_html = ob_get_clean();
                            echo apply_filters('jobsearch_cand_dash_apply_jobs_list_itm', $apply_job_html, $job_id, $job_key, $candidate_id);
                        }
                    }
                    ?>
                </ul>
            </div>
            <?php
            $total_pages = 1;
            if ($total_jobs > 0 && $reults_per_page > 0 && $total_jobs > $reults_per_page) {
                $total_pages = ceil($total_jobs / $reults_per_page);
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
    if ($user_email_adr != '') {
        global $wpdb;
        $all_apps = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts AS posts"
            . " LEFT JOIN $wpdb->postmeta AS postmeta ON(posts.ID = postmeta.post_id) "
            . " WHERE post_type=%s AND (postmeta.meta_key = 'jobsearch_app_user_email' AND postmeta.meta_value = '{$user_email_adr}')", 'email_apps'));

        if (!empty($all_apps)) {
            ?>
            <div class="jobsearch-employer-box-section">
                <div class="jobsearch-profile-title">
                    <h2><?php esc_html_e('Applied Jobs by Email', 'wp-jobsearch') ?></h2>
                </div>
                <div class="jobsearch-applied-jobs">
                    <ul class="jobsearch-row">
                        <?php
                        foreach ($all_apps as $app_id) {
                            $job_id = get_post_meta($app_id, 'jobsearch_app_job_id', true);
                            $job_post_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                            $job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                            $job_post_employer = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

                            $get_job_apps_cv_att = get_post_meta($job_id, 'job_apps_cv_att', true);
                            $attach_cv_job = isset($get_job_apps_cv_att[$candidate_id]) ? $get_job_apps_cv_att[$candidate_id] : '';

                            $job_post_user = jobsearch_get_employer_user_id($job_post_employer);

                            $user_def_avatar_url = get_avatar_url($job_post_user, array('size' => 69));
                            $user_avatar_id = get_post_thumbnail_id($job_post_employer);
                            if ($user_avatar_id > 0) {
                                $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
                                $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
                            }
                            $user_def_avatar_url = $user_def_avatar_url == '' ? jobsearch_no_image_placeholder() : $user_def_avatar_url;

                            $sectors = wp_get_post_terms($job_id, 'sector');
                            $job_sector = isset($sectors[0]->name) ? $sectors[0]->name : '';

                            if (get_post_type($job_id) == 'job') {
                                ob_start();
                                ?>
                                <li class="jobsearch-column-12">
                                    <div class="jobsearch-applied-jobs-wrap">
                                        <a class="jobsearch-applied-jobs-thumb"><img src="<?php echo ($user_def_avatar_url) ?>" alt=""></a>
                                        <div class="jobsearch-applied-jobs-text">
                                            <div class="jobsearch-applied-jobs-left">
                                                <span>@ <?php echo get_the_title($job_post_employer) ?></span>
                                                <h2 class="jobsearch-pst-title"><a href="<?php echo get_permalink($job_id) ?>"><?php echo get_the_title($job_id) ?></a></h2>
                                                <ul>
                                                    <?php
                                                    if ($job_location != '') {
                                                        ?>
                                                        <li><i class="fa fa-map-marker"></i> <?php echo ($job_location) ?></li>
                                                        <?php
                                                    }
                                                    if ($job_sector != '') {
                                                        ?>
                                                        <li><i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i> <a><?php echo ($job_sector) ?></a></li>
                                                        <?php
                                                    }
                                                    if ($job_post_date != '') {
                                                        ?>
                                                        <li><i class="jobsearch-icon jobsearch-calendar"></i> <?php echo date_i18n(get_option('date_format'), $job_post_date) ?></li>
                                                        <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <a href="<?php echo get_permalink($job_id) ?>" class="jobsearch-savedjobs-links"><i class="jobsearch-icon jobsearch-view"></i></a>
                                        </div>
                                    </div>
                                </li>
                                <?php
                                $apply_job_html = ob_get_clean();
                                echo apply_filters('jobsearch_cand_dash_apply_jobs_list_itm', $apply_job_html, $job_id, $job_key, $candidate_id);
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <?php
        }
    }
}