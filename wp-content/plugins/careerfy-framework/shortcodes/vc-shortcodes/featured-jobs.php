<?php

use WP_Jobsearch\Candidate_Profile_Restriction;
/**
 * Advance Search Shortcode
 * @return html
 */
add_shortcode('jobsearch_featured_job', 'jobsearch_featured_job_callback');

function jobsearch_featured_job_callback($atts)
{
    global $jobsearch_plugin_options, $jobsearch_shortcode_jobs_frontend;
    extract(shortcode_atts(array(
        'featured_title' => '',
        'featured_job_num' => '',
        'non_featured_items' => 'yes',
        'job_slide_position' => '',
        'featured_num_slide' => '',
        'featured_slide_speed' => 5000,
        'featured_section' => 'jobs',
        'slide_execution' => 'linear',
        'job_list_loc_listing' => 'country,city',
    ), $atts));

    if (class_exists('JobSearch_plugin')) {
        
        $cand_profile_restrict = new Candidate_Profile_Restriction;
        
        $featured_job_num = absint($featured_job_num) > 0 ? absint($featured_job_num) : '-1';

        $featured_num_slide = isset($featured_num_slide) && !empty($featured_num_slide) ? $featured_num_slide : 4;
        $featured_slide_speed = isset($featured_slide_speed) && !empty($featured_slide_speed) ? $featured_slide_speed : 5000;
        $featured_section = isset($featured_section) && !empty($featured_section) ? $featured_section : 'jobs';
        $slide_execution = isset($slide_execution) && !empty($slide_execution) ? $slide_execution : 'linear';
        $locations_view_type = isset($atts['job_list_loc_listing']) ? $atts['job_list_loc_listing'] : 'country,city';
        $loc_types_arr = $locations_view_type != '' ? explode(',', $locations_view_type) : '';
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

        $candidate_listing_percent = isset($jobsearch_plugin_options['jobsearch_candidate_skills']) ? $jobsearch_plugin_options['jobsearch_candidate_skills'] : '';
        $candmin_listing_percent = isset($jobsearch_plugin_options['cand_min_listpecent']) ? $jobsearch_plugin_options['cand_min_listpecent'] : '';
        $candmin_listing_percent = absint($candmin_listing_percent);

        $job_types_switch = isset($jobsearch_plugin_options['job_types_switch']) ? $jobsearch_plugin_options['job_types_switch'] : '';
        $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
        $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';

        $is_employer_featured = false;
        $is_candisate_featured = false;
        if ($featured_section == 'employer') {
            $featured_args = array(
                'posts_per_page' => 500,
                'post_type' => 'employer',
                'post_status' => 'publish',
                'fields' => 'ids', // only load ids
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_employer_approved',
                        'value' => 'on',
                        'compare' => '=',
                    ),
                ),
            );

            $is_employer_featured = true;
        } elseif ($featured_section == 'candidate') {
            $element_filter_arr = array();
            $element_filter_arr[] = array(
                'key' => 'jobsearch_field_candidate_approved',
                'value' => 'on',
                'compare' => '=',
            );
            if ($candidate_listing_percent == 'on' && $candmin_listing_percent > 0) {
                $element_filter_arr[] = array(
                    'key' => 'overall_skills_percentage',
                    'value' => $candmin_listing_percent,
                    'compare' => '>=',
                    'type' => 'NUMERIC',
                );
            }

            $featured_args = array(
                'posts_per_page' => 500,
                'post_type' => 'candidate',
                'post_status' => 'publish',
                'fields' => 'ids', // only load ids
                'meta_query' => $element_filter_arr,
            );
            $is_candisate_featured = true;
        } else {
            $jobsearch_jobs_listin_sh = $jobsearch_shortcode_jobs_frontend;

            $jobsearch__options = get_option('jobsearch_plugin_options');
            $emporler_approval = isset($jobsearch__options['job_listwith_emp_aprov']) ? $jobsearch__options['job_listwith_emp_aprov'] : '';
            $is_filled_jobs = isset($jobsearch__options['job_allow_filled']) ? $jobsearch__options['job_allow_filled'] : '';

            $element_filter_arr = array();
            //        $element_filter_arr[] = array(
            //            'key' => 'jobsearch_field_job_publish_date',
            //            'value' => strtotime(current_time('d-m-Y H:i:s')),
            //            'compare' => '<=',
            //        );
            //        $element_filter_arr[] = array(
            //            'key' => 'jobsearch_field_job_expiry_date',
            //            'value' => strtotime(current_time('d-m-Y H:i:s')),
            //            'compare' => '>=',
            //        );
            //        $element_filter_arr[] = array(
            //            'key' => 'jobsearch_field_job_status',
            //            'value' => 'approved',
            //            'compare' => '=',
            //        );

            $post_ids = array();
            $sh_atts = array();
            $all_post_ids = array();
            if (is_object($jobsearch_jobs_listin_sh)) {
                $all_post_ids = $jobsearch_jobs_listin_sh->job_general_query_filter($post_ids, $sh_atts);
            }

            if ($emporler_approval != 'off') {
                $element_filter_arr[] = array(
                    'key' => 'jobsearch_job_employer_status',
                    'value' => 'approved',
                    'compare' => '=',
                );
            }
            if ($non_featured_items == 'no') {
                $element_filter_arr[] = array(
                    'key' => 'jobsearch_field_job_featured',
                    'value' => 'on',
                    'compare' => '=',
                );
            }

            if ($is_filled_jobs == 'on') {
                $element_filter_arr[] = array(
                    'relation' => 'OR',
                    array(
                        'key' => 'jobsearch_field_job_filled',
                        'compare' => 'NOT EXISTS',
                    ),
                    array(
                        array(
                            'key' => 'jobsearch_field_job_filled',
                            'value' => 'on',
                            'compare' => '!=',
                        ),
                    ),
                );
            }

            $featured_args = array(
                'posts_per_page' => 500,
                'post_type' => 'job',
                'post_status' => 'publish',
                'fields' => 'ids',
            );

            if (!empty($element_filter_arr)) {
                $featured_args['meta_query'] = $element_filter_arr;
            }

            if (!empty($all_post_ids)) {
                $featured_args['post__in'] = $all_post_ids;
            }
        }

        $featured_query = new WP_Query($featured_args);
        $total_posts = $featured_query->found_posts;
        wp_reset_postdata();
        if ($is_employer_featured || $is_candisate_featured) {
            $ids_arr = array();

            if (isset($featured_query->posts) && !empty($featured_query->posts) && is_array($featured_query->posts)) {
                foreach ($featured_query->posts as $post_id) {
                    $promote_pckg_subtime = get_post_meta($post_id, 'promote_profile_substime', true);
                    $att_promote_pckg = get_post_meta($post_id, 'att_promote_profile_pkgorder', true);
                    $mber_feature_bk = get_post_meta($post_id, '_feature_mber_frmadmin', true);
                    $emp_count = 0;
                    if (!jobsearch_promote_profile_pkg_is_expired($att_promote_pckg)) {
                        $ids_arr[] = $post_id;
                    } else if ($mber_feature_bk == 'yes') {
                        $ids_arr[] = $post_id;
                    }
                }
            }

            $ids_arr_count = !empty($ids_arr) ? count($ids_arr) : 0;
            $add_poststo_count = 0;
            if ($featured_job_num > 0 && $ids_arr_count < $featured_job_num) {
                $add_poststo_count = $featured_job_num - $ids_arr_count;
            }
            if ($featured_job_num == '-1' && $ids_arr_count <= 5) {
                $add_poststo_count = 20;
            }

            if ($add_poststo_count > 0) {
                if ($is_employer_featured && $non_featured_items == 'yes') {
                    $ex_emp_args = array(
                        'posts_per_page' => $add_poststo_count,
                        'post_type' => 'employer',
                        'post_status' => 'publish',
                        'fields' => 'ids', // only load ids
                        'meta_query' => array(
                            array(
                                'key' => 'jobsearch_field_employer_approved',
                                'value' => 'on',
                                'compare' => '=',
                            ),
                        ),
                    );
                    if (!empty($ids_arr)) {
                        $ex_emp_args['post__not_in'] = $ids_arr;
                    }
                    $ex_emp_query = new WP_Query($ex_emp_args);
                    $ex_emp_posts = $ex_emp_query->posts;
                    if (!empty($ex_emp_posts)) {
                        foreach ($ex_emp_posts as $ex_emp_post) {
                            $ids_arr[] = $ex_emp_post;
                        }
                    }
                    wp_reset_postdata();
                }

                if ($is_candisate_featured && $non_featured_items == 'yes') {
                    $element_filter_arr = array();
                    $element_filter_arr[] = array(
                        'key' => 'jobsearch_field_candidate_approved',
                        'value' => 'on',
                        'compare' => '=',
                    );
                    if ($candidate_listing_percent == 'on' && $candmin_listing_percent > 0) {
                        $element_filter_arr[] = array(
                            'key' => 'overall_skills_percentage',
                            'value' => $candmin_listing_percent,
                            'compare' => '>=',
                        );
                    }
                    $ex_cand_args = array(
                        'posts_per_page' => $add_poststo_count,
                        'post_type' => 'candidate',
                        'post_status' => 'publish',
                        'fields' => 'ids', // only load ids
                        'meta_query' => $element_filter_arr,
                    );
                    if (!empty($ids_arr)) {
                        $ex_cand_args['post__not_in'] = $ids_arr;
                    }
                    $ex_cand_query = new WP_Query($ex_cand_args);
                    $ex_cand_posts = $ex_cand_query->posts;
                    if (!empty($ex_cand_posts)) {
                        foreach ($ex_cand_posts as $ex_cand_post) {
                            $ids_arr[] = $ex_cand_post;
                        }
                    }
                    wp_reset_postdata();
                }
            }
        } else {
            $ids_arr = array();
            if (isset($featured_query->posts) && !empty($featured_query->posts) && is_array($featured_query->posts)) {
                $ids_arr = $featured_query->posts;
            }

            $add_poststo_count = 0;
            if ($featured_job_num > 0 && $total_posts < $featured_job_num) {
                $add_poststo_count = $featured_job_num - $total_posts;
            }
            if ($featured_job_num == '-1' && $total_posts <= 5) {
                $add_poststo_count = 20;
            }
            if ($add_poststo_count > 0 && $non_featured_items == 'yes') {
                $ex_job_args = array(
                    'posts_per_page' => $add_poststo_count,
                    'post_type' => 'job',
                    'post_status' => 'publish',
                    'fields' => 'ids', // only load ids
                    'meta_query' => array(
                        array(
                            'key' => 'jobsearch_field_job_publish_date',
                            'value' => current_time('timestamp'),
                            'compare' => '<=',
                        ),
                        array(
                            'key' => 'jobsearch_field_job_expiry_date',
                            'value' => current_time('timestamp'),
                            'compare' => '>=',
                        ),
                        array(
                            'key' => 'jobsearch_field_job_status',
                            'value' => 'approved',
                            'compare' => '=',
                        ),
                    ),
                );
                if (!empty($ids_arr)) {
                    $ex_job_args['post__not_in'] = $ids_arr;
                }
                $ex_job_query = new WP_Query($ex_job_args);
                $ex_job_posts = $ex_job_query->posts;

                if (!empty($ex_job_posts)) {
                    foreach ($ex_job_posts as $ex_job_post) {
                        $ids_arr[] = $ex_job_post;
                    }
                }
                wp_reset_postdata();
            }
        }

        $veri_class = '';
        $veri_slide = 'true';
        if ($job_slide_position == 'horizontal') {
            $veri_class = ' vertical';
            $veri_slide = 'false';
        }
        wp_enqueue_script('careerfy-slick-slider');
        ob_start();
        $rand_id = rand(1234, 67888);
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                'use strict';
                if (jQuery('#featured-scroll-<?php echo($rand_id); ?>').length > 0) {
                    jQuery('#featured-scroll-<?php echo($rand_id); ?>').slick({
                        slidesToShow: <?php echo($featured_num_slide); ?>,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed: 0,
                        speed: <?php echo($featured_slide_speed); ?>,
                        vertical: <?php echo($veri_slide); ?>,
                        cssEase: '<?php echo($slide_execution); ?>',
                        arrows: 'false',
                        responsive: [
                            {
                                breakpoint: 1024,
                                settings: {
                                    slidesToShow: 2,
                                    slidesToScroll: 1,
                                    infinite: true,
                                }
                            },
                            {
                                breakpoint: 800,
                                settings: {
                                    slidesToShow: 2,
                                    slidesToScroll: 1
                                }
                            },
                            {
                                breakpoint: 400,
                                settings: {
                                    slidesToShow: 1,
                                    slidesToScroll: 1
                                }
                            }
                        ]
                    });
                }
            });
        </script>

        <?php if (isset($featured_title) && !empty($featured_title)) { ?>
            <div class="careerfy-fancy-title">
                <h2><?php echo($featured_title); ?></h2>
            </div>
        <?php } ?>

        <?php
        if ($is_employer_featured) {

            ?>
            <div class="careerfy-employer careerfy-employer-grid">
                <div id="featured-scroll-<?php echo ($rand_id); ?>">
                    <?php
                    if (isset($ids_arr) && !empty($ids_arr) && is_array($ids_arr)) {
                        if ($featured_job_num > 0 && count($ids_arr) > $featured_job_num) {
                            $ids_arr = array_slice($ids_arr, 0, $featured_job_num, true);
                        }
                        foreach ($ids_arr as $employer_id) {
                            //global $post, $jobsearch_member_profile;
                            //echo $employer_id = $post;
                            $promote_pckg_subtime = get_post_meta($employer_id, 'promote_profile_substime', true);
                            $att_promote_pckg = get_post_meta($employer_id, 'att_promote_profile_pkgorder', true);
                            $employer_random_id = rand(1111111, 9999999);
                            $post_thumbnail_id = jobsearch_employer_get_profile_image($employer_id);
                            $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-service');
                            $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : jobsearch_employer_image_placeholder();

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
                            $jobs_query = new WP_Query($jargs);
                            $emp_total_jobs = $jobs_query->found_posts;
                            update_post_meta($employer_id, 'jobsearch_field_employer_job_count', absint($emp_total_jobs));
                            $jobsearch_employer_job_count = get_post_meta($employer_id, 'jobsearch_field_employer_job_count', true);
                            $jobsearch_employer_team_count = 0;
                            if (is_array($jobsearch_employer_team_title_list) && sizeof($jobsearch_employer_team_title_list) > 0) {
                                $jobsearch_employer_team_count = sizeof($jobsearch_employer_team_title_list);
                            }
                            $sector_str = jobsearch_employer_get_all_sectors($employer_id, '', '', '', '<small>', '</small>');
                            ?>
                            <div>
                                <div class="careerfy-employer-grid-wrap">
                                    <?php echo jobsearch_member_promote_profile_iconlab($employer_id, 'employer_list') ?>
                                    <figure>
                                        <?php if ($post_thumbnail_src != '') { ?>
                                            <a href="<?php echo get_permalink($employer_id); ?>" class="careerfy-employer-grid-image"><img src="<?php echo ($post_thumbnail_src) ?>" alt=""></a>
                                        <?php } ?>
                                        <figcaption>
                                            <?php
                                            if (!empty($sector_str) && $sectors_enable_switch == 'on') {
                                                echo ($sector_str);
                                            }
                                            ?>
                                            <h2>
                                                <a href="<?php echo esc_url(get_permalink($employer_id)); ?>">
                                                    <?php echo esc_html(wp_trim_words(get_the_title($employer_id), 5)); ?>
                                                </a>
                                            </h2>
                                            <?php
                                            if (!empty($get_employer_location) && $all_location_allow == 'on') {
                                                ?>
                                                <span><?php echo jobsearch_esc_html($get_employer_location); ?></span>
                                                <?php
                                            }
                                            ?>
                                        </figcaption>
                                    </figure>
                                    <?php
                                    if ($jobsearch_employer_team_count > 0 && (is_array($team_imagefield_list) && sizeof($team_imagefield_list) > 0)) {
                                        $jobsearch_employer_team_count = '+' . $jobsearch_employer_team_count;
                                        ?>
                                        <ul class="careerfy-employer-thumblist">
                                            <?php
                                            $team_flag = 0;
                                            foreach ($team_imagefield_list as $team_imagefield_single) {
                                                ?>
                                                <li><a href="javascript:void(0);"><img src="<?php echo esc_url($team_imagefield_single); ?>" alt=""></a></li>
                                                <?php
                                                $team_flag++;
                                                if ($team_flag >= 4) {
                                                    break;
                                                }
                                            }
                                            ?>
                                        </ul>
                                        <a href="<?php echo esc_url(get_permalink($employer_id)); ?>" class="careerfy-employer-thumblist-size"><?php echo esc_html($jobsearch_employer_team_count); ?> <?php echo esc_html__('team size', 'careerfy'); ?></a>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                                $jobsearch_employer_job_count_str = '';
                                if ($jobsearch_employer_job_count > 1) {
                                    $jobsearch_employer_job_count_str = absint($jobsearch_employer_job_count) . ' ' . esc_html__('Vacancies', 'careerfy');
                                } else {
                                    $jobsearch_employer_job_count_str = absint($jobsearch_employer_job_count) . ' ' . esc_html__('Vacancy', 'careerfy');
                                }
                                ?>
                                <a href="<?php echo esc_url(get_permalink($employer_id)); ?>" class="careerfy-employer-grid-btn"><?php echo ($jobsearch_employer_job_count_str) ?></a>
                                <?php

                                $follow_btn_args = array(
                                    'employer_id' => $employer_id,
                                    'before_label' => esc_html__('Follow', 'wp-jobsearch'),
                                    'after_label' => esc_html__('Followed', 'wp-jobsearch'),
                                    'ext_class' => 'careerfy-employer-grid-btn',
                                    'view' => 'list_grid_view',
                                );
                                do_action('jobsearch_employer_followin_btn', $follow_btn_args);
                              ?>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<li>
                                <div class="no-employer-match-error">
                                    <strong>' . esc_html__('No Record', 'careerfy') . '</strong>
                                    <span>' . esc_html__('Sorry!', 'careerfy') . '&nbsp; ' . esc_html__('Does not match record with your keyword', 'careerfy') . ' </span>
                                </div>
                            </li>';
                    }
                    ?>
                </div>
            </div>
            <?php } elseif ($is_candisate_featured) {  ?>
            <div class="careerfy-candidate careerfy-candidate-grid">
                <div id="featured-scroll-<?php echo ($rand_id); ?>">
                    <?php
                    if (isset($ids_arr) && !empty($ids_arr) && is_array($ids_arr)) {
                        if ($featured_job_num > 0 && count($ids_arr) > $featured_job_num) {
                            $ids_arr = array_slice($ids_arr, 0, $featured_job_num, true);
                        }

                        foreach ($ids_arr as $candidate_id) {

                            $promote_pckg_subtime = get_post_meta($candidate_id, 'promote_profile_substime', true);
                            $att_promote_pckg = get_post_meta($candidate_id, 'att_promote_profile_pkgorder', true);
                            $cand_count = 0;
                            //if (!jobsearch_promote_profile_pkg_is_expired($att_promote_pckg)) {
                            $candidate_uid = jobsearch_get_candidate_user_id($candidate_id);
                            $user_obj = get_user_by('ID', $candidate_uid);
                            $user_email = isset($user_obj->user_email) ? $user_obj->user_email : '';
                            $post_thumbnail_src = jobsearch_candidate_img_url_comn($candidate_id);
                            $jobsearch_candidate_approved = get_post_meta($candidate_id, 'jobsearch_field_candidate_approved', true);
                            $get_candidate_location = get_post_meta($candidate_id, 'jobsearch_field_location_address', true);
                            if (function_exists('jobsearch_post_city_contry_txtstr')) {
                                 $get_candidate_location = jobsearch_post_city_contry_txtstr($candidate_id, $loc_view_country, $loc_view_state, $loc_view_city);
                             }
                            $jobsearch_candidate_jobtitle = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                            $jobsearch_candidate_company_name = get_post_meta($candidate_id, 'jobsearch_field_candidate_company_name', true);
                            $jobsearch_candidate_company_url = get_post_meta($candidate_id, 'jobsearch_field_candidate_company_url', true);
                            $user_status = get_post_meta($candidate_id, 'jobsearch_field_candidate_approved', true);
                            $sdsdffdsf = get_post_meta($candidate_id, 'promote_profile_substime', true);
                            
                            $jobsearch_candidate_jobtitle = jobsearch_esc_html($jobsearch_candidate_jobtitle);
                            $candidate_company_str = '';
                            if ($jobsearch_candidate_jobtitle != '') {
                                $candidate_company_str .= $jobsearch_candidate_jobtitle;
                            }
                            $sector_str = jobsearch_candidate_get_all_sectors($candidate_id, '', '', '', '', '');

                            $final_color = '';
                            $candidate_skills = isset($jobsearch_plugin_options['jobsearch_candidate_skills']) ? $jobsearch_plugin_options['jobsearch_candidate_skills'] : '';
                            if ($candidate_skills == 'on') {

                                $low_skills_clr = isset($jobsearch_plugin_options['skill_low_set_color']) && $jobsearch_plugin_options['skill_low_set_color'] != '' ? $jobsearch_plugin_options['skill_low_set_color'] : '';
                                $med_skills_clr = isset($jobsearch_plugin_options['skill_med_set_color']) && $jobsearch_plugin_options['skill_med_set_color'] != '' ? $jobsearch_plugin_options['skill_med_set_color'] : '';
                                $high_skills_clr = isset($jobsearch_plugin_options['skill_high_set_color']) && $jobsearch_plugin_options['skill_high_set_color'] != '' ? $jobsearch_plugin_options['skill_high_set_color'] : '';
                                $comp_skills_clr = isset($jobsearch_plugin_options['skill_ahigh_set_color']) && $jobsearch_plugin_options['skill_ahigh_set_color'] != '' ? $jobsearch_plugin_options['skill_ahigh_set_color'] : '';

                                $overall_candidate_skills = get_post_meta($candidate_id, 'overall_skills_percentage', true);
                                if ($overall_candidate_skills <= 25 && $low_skills_clr != '') {
                                    $final_color = 'style="background-color: ' . $low_skills_clr . ';"';
                                } else if ($overall_candidate_skills > 25 && $overall_candidate_skills <= 50 && $med_skills_clr != '') {
                                    $final_color = 'style="background-color: ' . $med_skills_clr . ';"';
                                } else if ($overall_candidate_skills > 50 && $overall_candidate_skills <= 75 && $high_skills_clr != '') {
                                    $final_color = 'style="background-color: ' . $high_skills_clr . ';"';
                                } else if ($overall_candidate_skills > 75 && $comp_skills_clr != '') {
                                    $final_color = 'style="background-color: ' . $comp_skills_clr . ';"';
                                }
                            }
                            ?>
                            <div>
                                <figure>
                                    <?php echo jobsearch_member_promote_profile_iconlab($candidate_id) ?>
                                    <?php echo jobsearch_cand_urgent_pkg_iconlab($candidate_id,'cand_listv6') ?>
                                    <?php if ($post_thumbnail_src != '' && !$cand_profile_restrict::cand_field_is_locked('profile_fields|profile_img')) { ?>
                                        <a href="<?php echo get_permalink($candidate_id); ?>" class="careerfy-candidate-grid-thumb"><img src="<?php echo ($post_thumbnail_src) ?>" alt=""> <span class="careerfy-candidate-grid-status" <?php echo ($final_color) ?>></span></a>
                                    <?php } ?>
                                    <figcaption>
                                        <h2>
                                            <a href="<?php echo esc_url(get_permalink($candidate_id)); ?>">
                                                <?php echo apply_filters('jobsearch_candidate_listing_item_title', wp_trim_words(get_the_title($candidate_id), 5), $candidate_id); ?>
                                            </a>
                                        </h2>
                                        <?php
                                        if ($candidate_company_str != '') {
                                            ?>
                                            <p><?php echo ($candidate_company_str) ?></p>
                                            <?php
                                        }

                                        if ($get_candidate_location != '' && $all_location_allow == 'on' && !$cand_profile_restrict::cand_field_is_locked('address_defields')) {
                                            ?>
                                            <span><?php echo jobsearch_esc_html($get_candidate_location) ?></span>
                                            <?php
                                        }
                                        ?>
                                    </figcaption>
                                </figure>
                                <ul class="careerfy-candidate-grid-option">
                                    <?php
                                    if ($sector_str != '' && $sectors_enable_switch == 'on' && !$cand_profile_restrict::cand_field_is_locked('profile_fields|sector')) {
                                        ?>
                                        <li>
                                            <div class="careerfy-right">
                                                <span><?php esc_html_e('Sector:', 'careerfy') ?></span>
                                                <?php echo ($sector_str) ?>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<li>
                        <div class="no-candidate-match-error">
                            <strong>' . esc_html__('No Record', 'careerfy') . '</strong>
                            <span>' . esc_html__('Sorry!', 'careerfy') . '&nbsp; ' . esc_html__('Does not match record with your keyword', 'careerfy') . ' </span>

                        </div>
                    </li>';
                    }
                    ?>

                </ul>
            </div>
            <?php
        } else {
            ?>
            <div class="careerfy-jobs-scroll-slider<?php echo ($veri_class); ?>">
                <ul class="jobs-scroll-wrap scroll" id="featured-scroll-<?php echo ($rand_id); ?>">
                    <?php
                    if (!empty($ids_arr)) {
                        if ($featured_job_num > 0 && count($ids_arr) > $featured_job_num) {
                            $ids_arr = array_slice($ids_arr, 0, $featured_job_num, true);
                        }
                        foreach ($ids_arr as $job_id) {

                            $job_random_id = rand(1111111, 9999999);
                            $post_thumbnail_id = function_exists('jobsearch_job_get_profile_image') ? jobsearch_job_get_profile_image($job_id) : 0;
                            $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-job-medium');
                            $no_placeholder_img = '';
                            if (function_exists('jobsearch_no_image_placeholder')) {
                                $no_placeholder_img = jobsearch_no_image_placeholder();
                            }
                            $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : $no_placeholder_img;
                            $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
                            $job_post_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                            $company_name = function_exists('jobsearch_job_get_company_name') ? jobsearch_job_get_company_name($job_id, '@ ') : '';
                            $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                            if (function_exists('jobsearch_post_city_contry_txtstr')) {
                                 $get_job_location = jobsearch_post_city_contry_txtstr($job_id, $loc_view_country, $loc_view_state, $loc_view_city);
                             }
                            $job_type_str = function_exists('jobsearch_job_get_all_jobtypes') ? jobsearch_job_get_all_jobtypes($job_id, 'careerfy-scroll-jobs-type', '', '', '', '', 'span') : '';
                            $sector_str = function_exists('jobsearch_job_get_all_sectors') ? jobsearch_job_get_all_sectors($job_id, '', '', '', '<li>', '</li>') : '';
                            $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
                            $job_city_title = '';
                            $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location3', true);
                            if ($get_job_city == '') {
                                $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location2', true);
                            }
                            if ($get_job_city == '') {
                                $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
                            }
                            $job_city_tax = $get_job_city != '' ? get_term_by('slug', $get_job_city, 'job-location') : '';
                            if (is_object($job_city_tax)) {
                                $job_city_title = $job_city_tax->name;
                            }
                            ?>
                            <li>
                                <div class="careerfy-jobs-scroll-slider-wrap">
                                <?php  jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id,'job_listv1'); ?>
                                    <figure>
                                    <?php
                                        if ($post_thumbnail_src != '') {
                                            ?>
                                            <a href="<?php echo get_permalink($job_id); ?>"><img src="<?php echo ($post_thumbnail_src) ?>" alt=""><span class="jobs-scrolslider-imghelpr"></span></a>
                                            <?php
                                        }
                                        if ($jobsearch_job_featured == 'on') { ?>
                                            <span class="careerfy-jobli-medium2"><i class="fa fa-star"></i></span>
                                            <?php } ?>
                                        <?php jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'job_listv1') ?>
                                    </figure>
                                    <div class="careerfy-jobs-scroll-slider-text">
                                        <h2><a href="<?php echo esc_url(get_permalink($job_id)); ?>"><?php echo esc_html(wp_trim_words(get_the_title($job_id), 3)); ?></a></h2>
                                        <?php
                                        if (!empty($get_job_location)) {
                                            ?>
                                            <span><i class="careerfy-icon careerfy-map-pin"></i> <?php echo jobsearch_esc_html($get_job_location); ?></span>
                                            <?php
                                        }
                                        ?>
                                        <div class="careerfy-foot-wrap">
                                            <?php
                                            $book_mark_args = array(
                                                'job_id' => $job_id,
                                                'before_icon' => 'fa fa-heart-o',
                                                'after_icon' => 'fa fa-heart',
                                                'container_class' => '',
                                                'anchor_class' => 'careerfy-job-like',
                                            );
                                            do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args);
                                            if ($job_type_str != '' && $job_types_switch == 'on') {
                                                echo ($job_type_str);
                                            }
                                            ?>
                                        </div>
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
        }

        $featured_html = ob_get_clean();
        return $featured_html;
    }
}
