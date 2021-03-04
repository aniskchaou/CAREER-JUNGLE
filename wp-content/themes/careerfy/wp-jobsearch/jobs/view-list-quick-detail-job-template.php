<?php
/**
 * Listing search box
 *
 */
global $jobsearch_post_job_types, $jobsearch_plugin_options;

wp_enqueue_script('jobsearch-job-application-functions-script');
wp_enqueue_script('jobsearch-shortlist-functions-script');

$user_id = $user_company = '';
if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    $user_company = get_user_meta($user_id, 'jobsearch_company', true);
}

$default_job_no_custom_fields = isset($jobsearch_plugin_options['jobsearch_job_no_custom_fields']) ? $jobsearch_plugin_options['jobsearch_job_no_custom_fields'] : '';
if (false === ($job_view = jobsearch_get_transient_obj('jobsearch_job_view' . $job_short_counter))) {
    $job_view = isset($atts['job_view']) ? $atts['job_view'] : '';
}
$jobs_excerpt_length = isset($atts['jobs_excerpt_length']) ? $atts['jobs_excerpt_length'] : '18';
$jobsearch_split_map_title_limit = '20';

$job_no_custom_fields = isset($atts['job_no_custom_fields']) ? $atts['job_no_custom_fields'] : $default_job_no_custom_fields;
if ($job_no_custom_fields == '' || !is_numeric($job_no_custom_fields)) {
    $job_no_custom_fields = 3;
}
$job_filters = isset($atts['job_filters']) ? $atts['job_filters'] : '';
$jobsearch_jobs_title_limit = isset($atts['jobs_title_limit']) ? $atts['jobs_title_limit'] : '5';
//
$paging_var = 'job_page';
$job_page = isset($_REQUEST[$paging_var]) && $_REQUEST[$paging_var] != '' ? $_REQUEST[$paging_var] : 1;
$job_ad_banners_rep = isset($atts['job_ad_banners_rep']) ? $atts['job_ad_banners_rep'] : '';

$job_per_page = isset($atts['job_per_page']) ? $atts['job_per_page'] : '-1';
$job_per_page = isset($_REQUEST['per-page']) ? $_REQUEST['per-page'] : $job_per_page;
$counter = 1;
if ($job_page >= 2) {
    $counter = (
            ($job_page - 1) *
            $job_per_page) +
        1;
}

$sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';
$all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
$job_types_switch = isset($jobsearch_plugin_options['job_types_switch']) ? $jobsearch_plugin_options['job_types_switch'] : '';

$columns_class = 'jobsearch-column-12';

$http_request = jobsearch_server_protocol();
$locations_view_type = isset($atts['job_loc_listing']) ? $atts['job_loc_listing'] : '';
if (!is_array($locations_view_type)) {

    $loc_types_arr = $locations_view_type != '' ? explode(',', $locations_view_type) : '';
} else {
    $loc_types_arr = $locations_view_type;
}

$loc_view_country = $loc_view_state = $loc_view_city = false;
if (!empty($loc_types_arr)) {
    if (is_array($loc_types_arr) && in_array('country', $loc_types_arr)) {
        $loc_view_country = true;
    }
    if (is_array($loc_types_arr) && in_array('state', $loc_types_arr)) {
        $loc_view_state = true;
    }
    if (is_array($loc_types_arr) && in_array('city', $loc_types_arr)) {
        $loc_view_city = true;
    }
}
$job_deadline_switch = isset($atts['job_deadline_switch']) ? $atts['job_deadline_switch'] : '';
$has_featured_posts = false;
if (isset($featjobs_posts) && !empty($featjobs_posts)) {
    $has_featured_posts = true;
    $job_views_publish_date = isset($jobsearch_plugin_options['job_views_publish_date']) ? $jobsearch_plugin_options['job_views_publish_date'] : '';
    ?>
    <div class="jobsearch-job jobsearch-joblisting-classic jobsearch-jobs-listings jobsearch-quick-detail-list">
        <?php do_action('jobsearch_jobs_listing_quick_detail_before', array('sh_atts' => (isset($atts) ? $atts : ''))); ?>
        <ul class="jobsearch-row">
            <?php
            foreach ($featjobs_posts as $fjobs_post) {
                $job_id = $fjobs_post;

                apply_filters('jobsearch_job_detail_quick_apply_before_footer', $job_id);

                $job_random_id = rand(1111111, 9999999);
                $job_publish_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                $post_thumbnail_id = jobsearch_job_get_profile_image($job_id);
                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, apply_filters('jobsearch_jobs_actlist_thmb_size', 'thumbnail'));
                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
                $post_thumbnail_src = $post_thumbnail_src == '' ? jobsearch_no_image_placeholder() : $post_thumbnail_src;
                $post_thumbnail_src = apply_filters('jobsearch_jobemp_image_src', $post_thumbnail_src, $job_id);
                $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
                $company_name = jobsearch_job_get_company_name($job_id, '@ ');
                $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
                $job_city_title = jobsearch_post_city_contry_txtstr($job_id, $loc_view_country, $loc_view_state, $loc_view_city);
                $job_type_str = jobsearch_job_get_all_jobtypes($job_id, '', '', '', '', '', 'a', 'no_color');
                $sector_str = jobsearch_job_get_all_sectors($job_id, '', '', '', '<li><i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i>', '</li>');
                $job_salary = jobsearch_job_offered_salary($job_id);
                $current_time = time();
                $job_post_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                $elaspedtime = ($current_time) - ($job_post_date);
                $hourz = 24 * 60 * 60;
                ?>
                <li class="<?php echo($columns_class); ?>">
                    <div class="jobsearch-joblisting-classic-wrap careerfy-jobsearch-grid-wrap careerfy-jobsearch-grid-wrap-quick-detail <?php echo !wp_is_mobile() ? 'jobsearch-quick-detail-box' : '' ?>"
                         data-job-id="<?php echo esc_html($job_id); ?>">
                        <?php
                        jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'job_listv1');
                        if ($jobsearch_job_featured == 'on') { ?>
                            <div class="jobsearch-featured-job-quick-detail">
                                <span><?php echo esc_html__('Featured', 'jobsearch-quick-detail'); ?></span>
                            </div>
                        <?php }
                        ob_start();
                        if ($post_thumbnail_src != '') { ?>
                            <figure>
                                <a href="<?php echo !wp_is_mobile() ? 'javascript:void(0)' : esc_url(get_permalink($job_id)); ?>">
                                    <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">
                                </a>

                            </figure>
                            <?php
                        }
                        $list_emp_img = ob_get_clean();
                        echo apply_filters('jobsearch_jobs_listing_emp_img_html', $list_emp_img, $job_id, 'view1');
                        ?>
                        <div class="jobsearch-joblisting-text">
                            <div class="jobsearch-table-layer">
                                <div class="jobsearch-table-row">
                                    <div class="jobsearch-table-cell">
                                        <div class="jobsearch-list-option">
                                            <h2 class="jobsearch-pst-title">
                                                <a href="<?php echo !wp_is_mobile() ? 'javascript:void(0)' : esc_url(get_permalink($job_id)); ?>"
                                                   title="<?php echo esc_html(get_the_title($job_id)); ?>">
                                                    <?php echo esc_html(get_the_title($job_id)); ?>
                                                </a>

                                                <?php if ($elaspedtime < $hourz) { ?>
                                                    <a href="#" class="jobsearch-job-new">
                                                        <?php echo esc_html__('New!', 'jobsearch-quick-detail') ?>
                                                    </a>
                                                <?php } ?>
                                            </h2>
                                            <?php do_action('jobsearch_jobs_listing_after_title', $job_id, 'jobs_list_default'); ?>
                                            <ul>
                                                <?php
                                                if ($company_name != '') {
                                                    ob_start();
                                                    ?>
                                                    <li class="job-company-name"><?php echo force_balance_tags($company_name); ?></li>
                                                    <?php
                                                    $comp_name_html = ob_get_clean();
                                                    echo apply_filters('jobsearch_empname_in_joblistin', $comp_name_html, $job_id, 'view1');
                                                } ?>
                                            </ul>
                                            <ul>
                                                <?php
                                                if ($job_city_title != '' && $all_location_allow == 'on') { ?>
                                                    <li>
                                                        <i class="jobsearch-icon jobsearch-maps-and-flags"></i><?php echo esc_html($job_city_title); ?>
                                                    </li>
                                                <?php } else if (!empty($get_job_location) && $all_location_allow == 'on') { ?>
                                                    <li>
                                                        <i class="jobsearch-icon jobsearch-maps-and-flags"></i><?php echo esc_html($get_job_location); ?>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                            <ul>
                                                <?php if ($job_type_str != '') { ?>
                                                    <li><?php echo force_balance_tags($job_type_str); ?></li>
                                                <?php } ?>
                                                <li><?php echo($job_salary) ?></li>
                                            </ul>
                                            <ul>
                                                <?php
                                                if ($job_publish_date != '' && $job_views_publish_date == 'on') { ?>
                                                    <li>
                                                        <i class="jobsearch-icon jobsearch-calendar"></i><?php printf(esc_html__('Published %s', 'jobsearch-quick-detail'), jobsearch_time_elapsed_string($job_publish_date)); ?>
                                                    </li>
                                                <?php }
                                                echo do_action('jobsearch_job_listing_deadline', $atts, $job_id);
                                                ?>
                                            </ul>
                                            <?php do_action('jobsearch_job_listing_custom_fields', $atts, $job_id, $job_arg['custom_fields']); ?>
                                        </div>
                                    </div>

                                    <div class="jobsearch-job-userlist">
                                        <?php
                                        if ($job_type_str != '' && $job_types_switch == 'on') {
                                            echo force_balance_tags($job_type_str);
                                        }
                                        $book_mark_args = array(
                                            'job_id' => $job_id,
                                            'before_icon' => 'fa fa-heart-o',
                                            'after_icon' => 'fa fa-heart',
                                        );
                                        do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args);
                                        ?>
                                        <?php echo jobsearch_job_det_applybtn_acthtml('', $job_id, 'page', 'view7'); ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <?php
                            do_action('jobsearch_job_listing_after_excerpt', $job_id);
                            ?>
                        </div>
                    </div>
                </li>
                <?php
            }
            ?>
        </ul>
    </div>
<?php } ?>
<div class="jobsearch-job jobsearch-joblisting-classic jobsearch-jobs-listings jobsearch-quick-detail-list"
     id="jobsearch-job-<?php echo absint($job_short_counter) ?>">
    <?php do_action('jobsearch_jobs_listing_quick_detail_before', array('sh_atts' => (isset($atts) ? $atts : ''))); ?>
    <ul class="jobsearch-row">
        <?php
        if ($job_loop_obj->have_posts()) {
            $flag_number = 1;

            $job_views_publish_date = isset($jobsearch_plugin_options['job_views_publish_date']) ? $jobsearch_plugin_options['job_views_publish_date'] : '';

            $ads_rep_counter = 1;
            while ($job_loop_obj->have_posts()) : $job_loop_obj->the_post();
                global $post, $jobsearch_member_profile;
                $job_id = $post;

                apply_filters('jobsearch_job_detail_quick_apply_before_footer', $job_id);

                $job_random_id = rand(1111111, 9999999);
                $job_publish_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                $post_thumbnail_id = jobsearch_job_get_profile_image($job_id);
                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, apply_filters('jobsearch_jobs_actlist_thmb_size', 'thumbnail'));
                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
                $post_thumbnail_src = $post_thumbnail_src == '' ? jobsearch_no_image_placeholder() : $post_thumbnail_src;
                $post_thumbnail_src = apply_filters('jobsearch_jobemp_image_src', $post_thumbnail_src, $job_id);
                $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
                $company_name = jobsearch_job_get_company_name($job_id, '@ ');
                $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
                $job_city_title = jobsearch_post_city_contry_txtstr($job_id, $loc_view_country, $loc_view_state, $loc_view_city);
                $job_type_str = jobsearch_job_get_all_jobtypes($job_id, '', '', '', '', '', 'a', 'no_color');
                $sector_str = jobsearch_job_get_all_sectors($job_id, '', '', '', '<li><i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i>', '</li>');
                $job_salary = jobsearch_job_offered_salary($job_id);
                $current_time = time();
                $job_post_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                $elaspedtime = ($current_time) - ($job_post_date);
                $hourz = 24 * 60 * 60;
                ?>
                <li class="<?php echo esc_html($columns_class); ?>">
                    <div class="jobsearch-joblisting-classic-wrap careerfy-jobsearch-grid-wrap careerfy-jobsearch-grid-wrap-quick-detail <?php echo !wp_is_mobile() ? 'jobsearch-quick-detail-box' : '' ?>"
                         data-job-id="<?php echo esc_html($job_id); ?>" data-link-redirect="">
                        <?php jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'job_listv1') ?>
                        <?php
                        if ($jobsearch_job_featured == 'on') { ?>
                            <div class="jobsearch-featured-job-quick-detail">
                                <span><?php echo esc_html__('Featured', 'jobsearch-quick-detail'); ?></span>
                            </div>
                        <?php } ?>
                        <?php
                        $get_job_lat = get_post_meta($job_id, 'jobsearch_field_location_lat', true);
                        $get_job_lng = get_post_meta($job_id, 'jobsearch_field_location_lng', true);
                        //echo $get_job_lat . '|' . $get_job_lng;
                        ob_start();
                        if ($post_thumbnail_src != '') { ?>
                            <figure>
                                <a href="<?php echo !wp_is_mobile() ? 'javascript:void(0)' : esc_url(get_permalink($job_id)); ?>">
                                    <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">
                                </a>
                            </figure>
                            <?php
                        }
                        $list_emp_img = ob_get_clean();
                        echo apply_filters('jobsearch_jobs_listing_emp_img_html', $list_emp_img, $job_id, 'view1');
                        ?>
                        <div class="jobsearch-joblisting-text">
                            <div class="jobsearch-table-layer">
                                <div class="jobsearch-table-row">
                                    <div class="jobsearch-table-cell">
                                        <div class="jobsearch-list-option">
                                            <h2 class="jobsearch-pst-title">
                                                <a href="<?php echo !wp_is_mobile() ? 'javascript:void(0)' : esc_url(get_permalink($job_id)); ?>"
                                                   title="<?php echo esc_html(get_the_title($job_id)); ?>">
                                                    <?php echo esc_html(get_the_title($job_id)); ?>
                                                </a>
                                                <?php if ($elaspedtime < $hourz) { ?>
                                                    <a href="#" class="jobsearch-job-new">
                                                        <?php echo esc_html__('New!', 'jobsearch-quick-detail') ?>
                                                    </a>
                                                <?php } ?>
                                            </h2>
                                            <?php do_action('jobsearch_jobs_listing_after_title', $job_id, 'jobs_list_default'); ?>
                                            <ul>
                                                <?php
                                                if ($company_name != '') {
                                                    ob_start(); ?>
                                                    <li class="job-company-name"><?php echo force_balance_tags($company_name); ?></li>
                                                    <?php
                                                    $comp_name_html = ob_get_clean();
                                                    echo apply_filters('jobsearch_empname_in_joblistin', $comp_name_html, $job_id, 'view1');
                                                } ?>
                                            </ul>
                                            <ul>
                                                <?php
                                                if ($job_city_title != '' && $all_location_allow == 'on') { ?>
                                                    <li>
                                                        <i class="jobsearch-icon jobsearch-maps-and-flags"></i><?php echo esc_html($job_city_title); ?>
                                                    </li>
                                                <?php } else if (!empty($get_job_location) && $all_location_allow == 'on') { ?>
                                                    <li>
                                                        <i class="jobsearch-icon jobsearch-maps-and-flags"></i><?php echo esc_html($get_job_location); ?>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                            <ul>
                                                <?php if ($job_type_str != '') { ?>
                                                    <li><?php echo force_balance_tags($job_type_str); ?></li>
                                                <?php } ?>
                                                <li><?php echo($job_salary) ?></li>
                                            </ul>
                                            <ul>
                                                <?php if ($job_publish_date != '' && $job_views_publish_date == 'on') { ?>
                                                    <li>
                                                        <i class="jobsearch-icon jobsearch-calendar"></i><?php printf(esc_html__('Published %s', 'jobsearch-quick-detail'), jobsearch_time_elapsed_string($job_publish_date)); ?>
                                                    </li>
                                                    <?php
                                                }
                                                echo do_action('jobsearch_job_listing_deadline', $atts, $job_id);
                                                ?>
                                            </ul>
                                            <?php
                                            do_action('jobsearch_job_listing_custom_fields', $atts, $job_id, $job_arg['custom_fields']);
                                            ?>
                                        </div>
                                    </div>

                                    <div class="jobsearch-job-userlist">
                                        <?php
                                        $book_mark_args = array(
                                            'job_id' => $job_id,
                                            'before_icon' => 'fa fa-heart-o',
                                            'after_icon' => 'fa fa-heart',
                                        );
                                        do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args);
                                        ?>
                                        <?php echo jobsearch_job_det_applybtn_acthtml('', $job_id, 'page', 'view7'); ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <?php
                            //
                            do_action('jobsearch_job_listing_after_excerpt', $job_id);
                            ?>
                        </div>
                    </div>
                </li>
                <?php
                if ($job_ad_banners_rep == 'no') {
                    ob_start();
                    do_action('jobsearch_random_ad_banners', $atts, $job_loop_obj, $counter, 'job_listing');
                    $baner_html = ob_get_clean();
                    if ($baner_html != '' && $ads_rep_counter == 1) {
                        echo($baner_html);
                        $ads_rep_counter++;
                    }

                } else {
                    do_action('jobsearch_random_ad_banners', $atts, $job_loop_obj, $counter, 'job_listing');
                }

                $counter++;
                $flag_number++; // number variable for job
            endwhile;
            wp_reset_postdata();
        } else {
            if (!$has_featured_posts) {
                echo
                    '<li class="' . esc_html($columns_class) . '">
                    <div class="no-job-match-error">
                        <strong>' . esc_html__('No Record', 'jobsearch-quick-detail') . '</strong>
                        <span>' . esc_html__('Sorry!', 'jobsearch-quick-detail') . '&nbsp; ' . esc_html__('Does not match record with your keyword', 'jobsearch-quick-detail') . ' </span>
                        <span>' . esc_html__('Change your filter keywords to re-submit', 'jobsearch-quick-detail') . '</span>
                        <em>' . esc_html__('OR', 'jobsearch-quick-detail') . '</em>
                        <a href="' . esc_url($page_url) . '">' . esc_html__('Reset Filters', 'jobsearch-quick-detail') . '</a>
                    </div>
                </li>';
            }
        }
        ?>
    </ul>
</div>

