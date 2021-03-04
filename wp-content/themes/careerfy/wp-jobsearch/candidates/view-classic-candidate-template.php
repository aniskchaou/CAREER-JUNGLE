<?php
/**
 * Listing search box
 *
 */

use WP_Jobsearch\Candidate_Profile_Restriction;

global $jobsearch_post_candidate_types, $jobsearch_plugin_options;

if (class_exists('JobSearch_plugin')) {
    
    $cand_profile_restrict = new Candidate_Profile_Restriction;
    
    $user_id = $user_company = '';
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $user_company = get_user_meta($user_id, 'jobsearch_company', true);
    }
    $locations_view_type = isset($atts['candidate_loc_listing']) ? $atts['candidate_loc_listing'] : '';

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
    $default_candidate_no_custom_fields = isset($jobsearch_plugin_options['jobsearch_candidate_no_custom_fields']) ? $jobsearch_plugin_options['jobsearch_candidate_no_custom_fields'] : '';
    if (false === ( $candidate_view = jobsearch_get_transient_obj('jobsearch_candidate_view' . $candidate_short_counter) )) {
        $candidate_view = isset($atts['candidate_view']) ? $atts['candidate_view'] : '';
    }
    $candidates_excerpt_length = isset($atts['candidates_excerpt_length']) ? $atts['candidates_excerpt_length'] : '18';
    $jobsearch_split_map_title_limit = '10';

    $candidate_no_custom_fields = isset($atts['candidate_no_custom_fields']) ? $atts['candidate_no_custom_fields'] : $default_candidate_no_custom_fields;
    if ($candidate_no_custom_fields == '' || !is_numeric($candidate_no_custom_fields)) {
        $candidate_no_custom_fields = 3;
    }
    $candidate_filters = isset($atts['candidate_filters']) ? $atts['candidate_filters'] : '';
    $jobsearch_candidates_title_limit = isset($atts['candidates_title_limit']) ? $atts['candidates_title_limit'] : '5';
    // start ads script
    $candidate_ads_switch = isset($atts['candidate_ads_switch']) ? $atts['candidate_ads_switch'] : 'no';
    if ($candidate_ads_switch == 'yes') {
        $candidate_ads_after_list_series = isset($atts['candidate_ads_after_list_count']) ? $atts['candidate_ads_after_list_count'] : '5';
        if ($candidate_ads_after_list_series != '') {
            $candidate_ads_list_array = explode(",", $candidate_ads_after_list_series);
        }
        $candidate_ads_after_list_array_count = sizeof($candidate_ads_list_array);
        $candidate_ads_after_list_flag = 0;
        $i = 0;
        $array_i = 0;
        $candidate_ads_after_list_array_final = '';
        while ($candidate_ads_after_list_array_count > $array_i) {
            if (isset($candidate_ads_list_array[$array_i]) && $candidate_ads_list_array[$array_i] != '') {
                $candidate_ads_after_list_array[$i] = $candidate_ads_list_array[$array_i];
                $i ++;
            }
            $array_i ++;
        }
        // new count 
        $candidate_ads_after_list_array_count = sizeof($candidate_ads_after_list_array);
    }

    $candidates_ads_array = array();
    if ($candidate_ads_switch == 'yes' && $candidate_ads_after_list_array_count > 0) {
        $list_count = 0;
        for ($i = 0; $i <= $candidate_loop_obj->found_posts; $i ++) {
            if ($list_count == $candidate_ads_after_list_array[$candidate_ads_after_list_flag]) {
                $list_count = 1;
                $candidates_ads_array[] = $i;
                $candidate_ads_after_list_flag ++;
                if ($candidate_ads_after_list_flag >= $candidate_ads_after_list_array_count) {
                    $candidate_ads_after_list_flag = $candidate_ads_after_list_array_count - 1;
                }
            } else {
                $list_count ++;
            }
        }
    }
    $paging_var = 'candidate_page';
    $candidate_page = isset($_REQUEST[$paging_var]) && $_REQUEST[$paging_var] != '' ? $_REQUEST[$paging_var] : 1;
    $candidate_per_page = isset($atts['candidate_per_page']) ? $atts['candidate_per_page'] : '-1';
    $candidate_per_page = isset($_REQUEST['per-page']) ? $_REQUEST['per-page'] : $candidate_per_page;
    $counter = 1;
    if ($candidate_page >= 2) {
        $counter = (
                ($candidate_page - 1) *
                $candidate_per_page ) +
                1;
    }
    // end ads script

    $membsectors_enable_switch = isset($jobsearch_plugin_options['usersector_onoff_switch']) ? $jobsearch_plugin_options['usersector_onoff_switch'] : '';
    $sectors_enable_switch = ($membsectors_enable_switch == 'on_cand' || $membsectors_enable_switch == 'on_both') ? 'on' : '';

    $columns_class = 'col-md-4';

    $http_request = jobsearch_server_protocol();
    ?>
    <div class="careerfy-team-parallex-list" id="careerfy-candidate-<?php echo absint($candidate_short_counter) ?>">

        <ul class="row">
            <?php
            if ($candidate_loop_obj->have_posts()) {
                $flag_number = 1;

                while ($candidate_loop_obj->have_posts()) : $candidate_loop_obj->the_post();
                    global $post, $jobsearch_member_profile;
                    $candidate_id = $post;
                    $candidate_uid = jobsearch_get_candidate_user_id($candidate_id);
                    $user_obj = get_user_by('ID', $candidate_uid);
                    $user_email = isset($user_obj->user_email) ? $user_obj->user_email : '';
                    
                    $post_thumbnail_src = '';
                    if (function_exists('jobsearch_candidate_img_url_comn')) {
                        $post_thumbnail_src = jobsearch_candidate_img_url_comn($candidate_id);
                    }
                    $jobsearch_candidate_approved = get_post_meta($candidate_id, 'jobsearch_field_candidate_approved', true);
                    $get_candidate_location = get_post_meta($candidate_id, 'jobsearch_field_location_address', true);
                    if (function_exists('jobsearch_post_city_contry_txtstr')) {
                        $get_candidate_location = jobsearch_post_city_contry_txtstr($candidate_id, $loc_view_country, $loc_view_state, $loc_view_city);
                    }
                    $jobsearch_candidate_jobtitle = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                    $jobsearch_candidate_company_name = get_post_meta($candidate_id, 'jobsearch_field_candidate_company_name', true);
                    $jobsearch_candidate_company_url = get_post_meta($candidate_id, 'jobsearch_field_candidate_company_url', true);

                    $user_status = get_post_meta($candidate_id, 'jobsearch_field_candidate_approved', true);

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

                    <li class="<?php echo esc_html($columns_class); ?>">
                        <figure>
                            <a href="<?php the_permalink(); ?>">
                                <?php
                                if (function_exists('jobsearch_member_promote_profile_iconlab')) {
                                    echo jobsearch_member_promote_profile_iconlab($candidate_id);
                                }
                                if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|profile_img')) { ?>
                                    <img src="<?php echo ($post_thumbnail_src) ?>" alt=""> <span class="careerfy-team-parallex-status" <?php echo ($final_color) ?>></span>
                                    <?php }  ?>
                            </a>
                            <figcaption>
                                <h2>
                                    <a href="<?php echo esc_url(get_permalink($candidate_id)); ?>">
                                        <?php echo apply_filters('jobsearch_candidate_listing_item_title', wp_trim_words(get_the_title($candidate_id), $jobsearch_split_map_title_limit), $candidate_id); ?>
                                    </a>
                                </h2>
                                <?php
                                if (function_exists('jobsearch_cand_urgent_pkg_iconlab')) {
                                    echo jobsearch_cand_urgent_pkg_iconlab($candidate_id, 'cand_dclassic');
                                }
                                //
                                if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|job_title')) {
                                    if ($candidate_company_str != '') {
                                        ?>
                                        <span><?php echo jobsearch_esc_html($candidate_company_str) ?></span>
                                        <?php
                                    }
                                }
                                ?>
                            </figcaption>
                        </figure>
                    </li>
                    <?php
                    do_action('jobsearch_random_ad_banners', $atts, $candidate_loop_obj, $counter, 'candidate_listing');
                    $counter ++;
                    $flag_number ++; // number variable for candidate
                endwhile;
            } else {
                $reset_link = get_permalink(get_the_ID());
                echo '
                <li class="' . esc_html($columns_class) . '">
                    <div class="no-candidate-match-error">
                        <strong>' . esc_html__('No Record', 'careerfy') . '</strong>
                        <span>' . esc_html__('Sorry!', 'careerfy') . '&nbsp; ' . esc_html__('Does not match record with your keyword', 'careerfy') . ' </span>
                        <span>' . esc_html__('Change your filter keywords to re-submit', 'careerfy') . '</span>
                        <em>' . esc_html__('OR', 'careerfy') . '</em>
                        <a href="' . esc_url($reset_link) . '">' . esc_html__('Reset Filters', 'careerfy') . '</a>
                    </div>
                </li>';
            }
            ?> 
        </ul>
    </div>
    <?php
}
