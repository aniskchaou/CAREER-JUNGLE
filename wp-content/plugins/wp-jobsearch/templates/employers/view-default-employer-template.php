<?php
/**
 * Listing search box
 *
 */
global $jobsearch_post_employer_types, $jobsearch_plugin_options;

$user_id = $user_company = '';
if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    $user_company = get_user_meta($user_id, 'jobsearch_company', true);
}

$all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
$locations_view_type = isset($atts['employer_loc_listing']) ? $atts['employer_loc_listing'] : '';

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
$default_employer_no_custom_fields = isset($jobsearch_plugin_options['jobsearch_employer_no_custom_fields']) ? $jobsearch_plugin_options['jobsearch_employer_no_custom_fields'] : '';
if (false === ($employer_view = jobsearch_get_transient_obj('jobsearch_employer_view' . $employer_short_counter))) {
    $employer_view = isset($atts['employer_view']) ? $atts['employer_view'] : '';
}
$employers_excerpt_length = isset($atts['employers_excerpt_length']) ? $atts['employers_excerpt_length'] : '18';
$jobsearch_split_map_title_limit = '10';

$employer_no_custom_fields = isset($atts['employer_no_custom_fields']) ? $atts['employer_no_custom_fields'] : $default_employer_no_custom_fields;
if ($employer_no_custom_fields == '' || !is_numeric($employer_no_custom_fields)) {
    $employer_no_custom_fields = 3;
}
$employer_filters = isset($atts['employer_filters']) ? $atts['employer_filters'] : '';

$jobsearch_employers_title_limit = isset($atts['employers_title_limit']) ? $atts['employers_title_limit'] : '5';
// start ads script
$employer_ads_switch = isset($atts['employer_ads_switch']) ? $atts['employer_ads_switch'] : 'no';
if ($employer_ads_switch == 'yes') {
    $employer_ads_after_list_series = isset($atts['employer_ads_after_list_count']) ? $atts['employer_ads_after_list_count'] : '5';
    if ($employer_ads_after_list_series != '') {
        $employer_ads_list_array = explode(",", $employer_ads_after_list_series);
    }
    $employer_ads_after_list_array_count = sizeof($employer_ads_list_array);
    $employer_ads_after_list_flag = 0;
    $i = 0;
    $array_i = 0;
    $employer_ads_after_list_array_final = '';
    while ($employer_ads_after_list_array_count > $array_i) {
        if (isset($employer_ads_list_array[$array_i]) && $employer_ads_list_array[$array_i] != '') {
            $employer_ads_after_list_array[$i] = $employer_ads_list_array[$array_i];
            $i++;
        }
        $array_i++;
    }
    // new count 
    $employer_ads_after_list_array_count = sizeof($employer_ads_after_list_array);
}

$employers_ads_array = array();
if ($employer_ads_switch == 'yes' && $employer_ads_after_list_array_count > 0) {
    $list_count = 0;
    for ($i = 0; $i <= $employer_loop_obj->found_posts; $i++) {
        if ($list_count == $employer_ads_after_list_array[$employer_ads_after_list_flag]) {
            $list_count = 1;
            $employers_ads_array[] = $i;
            $employer_ads_after_list_flag++;
            if ($employer_ads_after_list_flag >= $employer_ads_after_list_array_count) {
                $employer_ads_after_list_flag = $employer_ads_after_list_array_count - 1;
            }
        } else {
            $list_count++;
        }
    }
}
$paging_var = 'employer_page';
$employer_page = isset($_REQUEST[$paging_var]) && $_REQUEST[$paging_var] != '' ? $_REQUEST[$paging_var] : 1;
$employer_per_page = isset($atts['employer_per_page']) ? $atts['employer_per_page'] : '-1';
$employer_per_page = isset($_REQUEST['per-page']) ? $_REQUEST['per-page'] : $employer_per_page;
$counter = 1;
if ($employer_page >= 2) {
    $counter = (
            ($employer_page - 1) *
            $employer_per_page) +
        1;
}
// end ads script
$membsectors_enable_switch = isset($jobsearch_plugin_options['usersector_onoff_switch']) ? $jobsearch_plugin_options['usersector_onoff_switch'] : '';
$sectors_enable_switch = ($membsectors_enable_switch == 'on_emp' || $membsectors_enable_switch == 'on_both') ? 'on' : '';

$columns_class = 'jobsearch-column-12';

$http_request = jobsearch_server_protocol();

?>
<div class="jobsearch-employer jobsearch-employer-list"
     id="jobsearch-employer-<?php echo absint($employer_short_counter) ?>">

    <ul class="jobsearch-row">
        <?php
        if ($employer_loop_obj->have_posts()) {
            $flag_number = 1;

            while ($employer_loop_obj->have_posts()) : $employer_loop_obj->the_post();
                global $post, $jobsearch_member_profile;

                $employer_id = $post;
                $employer_random_id = rand(1111111, 9999999);
                $post_thumbnail_id = jobsearch_employer_get_profile_image($employer_id);
                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, apply_filters('jobsearch_emps_actlist_thmb_size', 'thumbnail'));
                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
                $post_thumbnail_src = $post_thumbnail_src == '' ? jobsearch_employer_image_placeholder() : $post_thumbnail_src;
                $jobsearch_employer_featured = get_post_meta($employer_id, 'jobsearch_field_employer_featured', true);
                $jobsearch_employer_posted = get_post_meta($employer_id, 'jobsearch_field_employer_publish_date', true);
                $jobsearch_employer_posted = jobsearch_time_elapsed_string($jobsearch_employer_posted);
                $get_employer_location = get_post_meta($employer_id, 'jobsearch_field_location_address', true);
                if (function_exists('jobsearch_post_city_contry_txtstr')) {
                    $get_employer_location = jobsearch_post_city_contry_txtstr($employer_id, $loc_view_country, $loc_view_state, $loc_view_city);
                }
                $jobsearch_employer_team_title_list = get_post_meta($employer_id, 'jobsearch_field_team_title', true);
                $team_imagefield_list = get_post_meta($employer_id, 'jobsearch_field_team_image', true);

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
                $jobsearch_employer_team_count = 0;
                if (is_array($jobsearch_employer_team_title_list) && sizeof($jobsearch_employer_team_title_list) > 0) {
                    $jobsearch_employer_team_count = sizeof($jobsearch_employer_team_title_list);
                }
                $sector_str = jobsearch_employer_get_all_sectors($employer_id, '', '', '', '<small>', '</small>');

                ?>
                <li class="<?php echo esc_html($columns_class); ?>">
                    <div class="jobsearch-table-layer">
                        <?php echo jobsearch_member_promote_profile_iconlab($employer_id, 'employer_list') ?>
                        <div class="jobsearch-table-row">
                            <?php if ($post_thumbnail_src != '') { ?>
                                <div class="jobsearch-table-cell">
                                    <figure>
                                        <a href="<?php the_permalink(); ?>">
                                            <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">
                                        </a>
                                    </figure>
                                </div>
                            <?php } ?>
                            <div class="jobsearch-table-cell">
                                <?php
                                if (!empty($sector_str) && $sectors_enable_switch == 'on') {
                                    echo ($sector_str);
                                }
                                ob_start();
                                ?>
                                <h2 class="jobsearch-pst-title">
                                    <a href="<?php echo esc_url(get_permalink($employer_id)); ?>">
                                        <?php echo jobsearch_esc_html(wp_trim_words(get_the_title($employer_id), $jobsearch_split_map_title_limit)); ?>
                                    </a>
                                </h2>
                                <?php
                                $emptitle_html = ob_get_clean();
                                echo apply_filters('jobsearch_employer_listin_emptitle', $emptitle_html, $employer_id, $jobsearch_split_map_title_limit);
                                if (!empty($get_employer_location) && $all_location_allow == 'on') {  ?>
                                    <span><i class="fa fa-map-marker"></i><?php echo ($get_employer_location); ?></span>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="jobsearch-table-cell">
                                <?php
                                if ($jobsearch_employer_team_count > 0 && (is_array($team_imagefield_list) && sizeof($team_imagefield_list) > 0)) {
                                    $jobsearch_employer_team_count = '+' . $jobsearch_employer_team_count;
                                    ?>

                                    <ul class="jobsearch-employer-thumblist">
                                        <?php
                                        $team_flag = 0;
                                        foreach ($team_imagefield_list as $team_imagefield_single) {?>
                                            <li><a href="javascript:void(0);"><img
                                                            src="<?php echo esc_url($team_imagefield_single); ?>"
                                                            alt=""></a></li>
                                            <?php
                                            $team_flag++;
                                            if ($team_flag >= 4) {
                                                break;
                                            }
                                        }
                                        ?>
                                    </ul>

                                    <a href="<?php echo esc_url(get_permalink($employer_id)); ?>" class="jobsearch-employer-thumblist-size"><?php echo jobsearch_esc_html($jobsearch_employer_team_count); ?><?php echo esc_html__('team size', 'wp-jobsearch'); ?></a>
                                <?php } else { ?>
                                    &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                            ob_start();
                            $jobsearch_employer_job_count_str = '';
                            if ($jobsearch_employer_job_count > 1) {
                                $jobsearch_employer_job_count_str = absint($jobsearch_employer_job_count) . ' ' . esc_html__('Vacancies', 'wp-jobsearch');
                            } else {
                                $jobsearch_employer_job_count_str = absint($jobsearch_employer_job_count) . ' ' . esc_html__('Vacancy', 'wp-jobsearch');
                            }
                            ob_start();
                            if ($jobsearch_employer_job_count > 0) {
                                ?>
                                <a href="<?php echo esc_url(get_permalink($employer_id)); ?>" class="jobsearch-employer-list-btn"><?php echo jobsearch_esc_html($jobsearch_employer_job_count_str); ?> </a>
                                <?php
                            }
                            $opn_vacn_html = ob_get_clean();
                            echo apply_filters('jobsearch_employer_totl_job_openings_vacn', $opn_vacn_html, $employer_id, $jobsearch_employer_job_count);

                            $follow_btn_args = array(
                                'employer_id' => $employer_id,
                                'before_label' => esc_html__('Follow', 'wp-jobsearch'),
                                'after_label' => esc_html__('Followed', 'wp-jobsearch'),
                                'view' => 'list_view1',
                            );
                            do_action('jobsearch_employer_followin_btn', $follow_btn_args);
                            $rite_btns_html = ob_get_clean();

                            if ($rite_btns_html != '') {
                                ?>
                                <div class="jobsearch-table-cell">
                                    <?php echo ($rite_btns_html) ?>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </li>
                <?php
                do_action('jobsearch_random_ad_banners', $atts, $employer_loop_obj, $counter, 'employer_listing');
                $counter++;
                $flag_number++; // number variable for employer
            endwhile;
        } else {
            echo '
            <li class="' . esc_html($columns_class) . '">
                <div class="no-employer-match-error">
                    <strong>' . esc_html__('No Record', 'wp-jobsearch') . '</strong>
                    <span>' . esc_html__('Sorry!', 'wp-jobsearch') . '&nbsp; ' . esc_html__('Does not match record with your keyword', 'wp-jobsearch') . ' </span>
                    <span>' . esc_html__('Change your filter keywords to re-submit', 'wp-jobsearch') . '</span>
                    <em>' . esc_html__('OR', 'wp-jobsearch') . '</em>
                    <a href="' . esc_url($page_url) . '">' . esc_html__('Reset Filters', 'wp-jobsearch') . '</a>
                </div>
            </li>';
        }
        ?>
    </ul>
</div>