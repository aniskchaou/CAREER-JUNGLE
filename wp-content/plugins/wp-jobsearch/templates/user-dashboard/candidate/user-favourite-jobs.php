<?php
global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;
$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);

$page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
$page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);

$candidate_id = jobsearch_get_user_candidate_id($user_id);

$reults_per_page = isset($jobsearch_plugin_options['user-dashboard-per-page']) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;

$page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;

if ($candidate_id > 0) {
    $candidate_fav_jobs_list = array();
    $candidate_fav_jobs_liste = get_post_meta($candidate_id, 'jobsearch_fav_jobs_list', true);
    $candidate_fav_jobs_liste = $candidate_fav_jobs_liste != '' ? explode(',', $candidate_fav_jobs_liste) : array();
    if (!empty($candidate_fav_jobs_liste)) {
        foreach ($candidate_fav_jobs_liste as $er_fav_job_list) {
            $job_id = $er_fav_job_list;
            if (get_post_type($job_id) == 'job') {
                $candidate_fav_jobs_list[] = $job_id;
            }
        }
    }
    if (!empty($candidate_fav_jobs_list)) {
        $candidate_fav_jobs_list = implode(',', $candidate_fav_jobs_list);
    } else {
        $candidate_fav_jobs_list = '';
    }
    ?>
    <div class="jobsearch-employer-box-section">
        <div class="jobsearch-profile-title">
            <h2><?php echo apply_filters('jobsearch_cand_dash_favjobs_mainhead_title', esc_html__('Favorite Jobs', 'wp-jobsearch')) ?></h2>
        </div>
        <?php
        if ($candidate_fav_jobs_list != '') {
            $candidate_fav_jobs_list = explode(',', $candidate_fav_jobs_list);

            if (!empty($candidate_fav_jobs_list)) {
                $total_jobs = count($candidate_fav_jobs_list);
                krsort($candidate_fav_jobs_list);
                $start = ($page_num - 1) * ($reults_per_page);
                $offset = $reults_per_page;
                $candidate_fav_jobs_list = array_slice($candidate_fav_jobs_list, $start, $offset);
                ob_start();
                ?>
                <div class="jobsearch-candidate-savedjobs">
                    <table>
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Job Title', 'wp-jobsearch') ?></th>
                                <th><?php esc_html_e('Company', 'wp-jobsearch') ?></th>
                                <th><?php esc_html_e('Posted Date', 'wp-jobsearch') ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($candidate_fav_jobs_list as $job_id) {

                                $job_post_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                                $job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                                $job_post_employer = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

                                $job_post_user = jobsearch_get_employer_user_id($job_post_employer);

                                $user_def_avatar_url = get_avatar_url($job_post_user, array('size' => 44));
                                $user_avatar_id = get_post_thumbnail_id($job_post_employer);
                                if ($user_avatar_id > 0) {
                                    $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
                                    $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
                                }
                                $user_def_avatar_url = $user_def_avatar_url == '' ? jobsearch_no_image_placeholder() : $user_def_avatar_url;

                                $sectors = wp_get_post_terms($job_id, 'sector');
                                $job_sector = isset($sectors[0]->name) ? $sectors[0]->name : '';
                                ?>
                                <tr>
                                    <td>
                                        <a class="jobsearch-savedjobs-thumb"><img src="<?php echo ($user_def_avatar_url) ?>" alt=""></a>
                                        <h2 class="jobsearch-pst-title"><a href="<?php echo get_permalink($job_id) ?>"><?php echo get_the_title($job_id) ?></a></h2>
                                    </td>
                                    <td><span>@ <?php echo get_the_title($job_post_employer) ?></span></td>
                                    <?php
                                    if ($job_post_date != '') {
                                        ?>
                                        <td><?php echo date_i18n(get_option('date_format'), $job_post_date) ?></td>
                                        <?php
                                    }
                                    ?>
                                    <td>
                                        <a href="javascript:void(0);" class="jobsearch-savedjobs-links jobsearch-delete-fav-job" data-id="<?php echo ($job_id) ?>"><i class="jobsearch-icon jobsearch-rubbish"></i></a>
                                        <span class="remove-fav-job-loader"></span>
                                        <a href="<?php echo get_permalink($job_id) ?>" class="jobsearch-savedjobs-links"><i class="jobsearch-icon jobsearch-view"></i></a>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                $favjobs_html = ob_get_clean();
                echo apply_filters('jobsearch_cand_dash_favjobs_list_html', $favjobs_html, $candidate_fav_jobs_list, $candidate_id);
                
                $total_pages = 1;
                if ($total_jobs > 0 && $reults_per_page > 0 && $total_jobs > $reults_per_page) {
                    $total_pages = ceil($total_jobs / $reults_per_page);
                    ?>
                    <div class="jobsearch-pagination-blog">
                        <?php $Jobsearch_User_Dashboard_Settings->pagination($total_pages, $page_num, $page_url) ?>
                    </div>
                    <?php
                }
            }
        } else {
            echo '<p>' . esc_html__('No record found.', 'wp-jobsearch') . '</p>';
        }
        ?>
    </div>
    <?php
}    