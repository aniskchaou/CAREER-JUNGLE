<?php

use WP_Jobsearch\Candidate_Profile_Restriction;

if (!function_exists('jobsearch_employer_get_profile_image')) {

    function jobsearch_employer_get_profile_image($employer_id)
    {
        $post_thumbnail_id = '';
        if (isset($employer_id) && $employer_id != '' && has_post_thumbnail($employer_id)) {
            $post_thumbnail_id = get_post_thumbnail_id($employer_id);
        }
        return $post_thumbnail_id;
    }

}

//add_action('init', 'jobsearch_collect_emp_jobscount_meta');

function jobsearch_collect_emp_jobscount_meta()
{
    global $wpdb;

    $apps_query = "SELECT ID FROM $wpdb->posts AS posts"
        . " WHERE 1=1 AND posts.post_type=%s"
        . " ORDER BY ID DESC";
    $apps_resobj = $wpdb->get_results($wpdb->prepare($apps_query, 'employer'), 'ARRAY_A');

    if (!empty($apps_resobj)) {
        foreach ($apps_resobj as $emp_arr) {
            $employer_id = $emp_arr['ID'];
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

            $jobs_query = new WP_Query($jargs);

            $emp_total_jobs = $jobs_query->found_posts;
            update_post_meta($employer_id, 'jobsearch_field_employer_job_count', absint($emp_total_jobs));
        }
    }
}

if (!function_exists('jobsearch_employer_get_company_name')) {

    function jobsearch_employer_get_company_name($employer_id, $before_title = '', $after_title = '')
    {
        $company_name_str = '';
        $employer_field_user = get_post_meta($employer_id, 'jobsearch_field_employer_posted_by', true);
        if (isset($employer_field_user) && $employer_field_user != '') {
            $company_name_str = '<a href="' . get_permalink($employer_field_user) . '">' . $before_title . get_the_title($employer_field_user) . $after_title . '</a>';
        }
        return $company_name_str;
    }

}

if (!function_exists('jobsearch_employer_get_all_employertypes')) {

    function jobsearch_employer_get_all_employertypes($employer_id, $link_class = 'jobsearch-option-btn', $before_title = '', $after_title = '', $before_tag = '', $after_tag = '')
    {

        $employer_type = wp_get_post_terms($employer_id, 'employertype');
        ob_start();
        $html = '';
        if (!empty($employer_type)) {
            $link_class_str = '';
            if ($link_class != '') {
                $link_class_str = 'class="' . $link_class . '"';
            }
            echo($before_tag);
            foreach ($employer_type as $term) :
                $employertype_color = get_term_meta($term->term_id, 'jobsearch_field_employertype_color', true);
                $employertype_textcolor = get_term_meta($term->term_id, 'jobsearch_field_employertype_textcolor', true);
                $employertype_color_str = '';
                if ($employertype_color != '') {
                    $employertype_color_str = ' style="background-color: ' . esc_attr($employertype_color) . '; color: ' . esc_attr($employertype_textcolor) . ' "';
                }
                ?>
                <a <?php echo force_balance_tags($link_class_str) ?> <?php echo force_balance_tags($employertype_color_str); ?>>
                    <?php
                    echo($before_title);
                    echo esc_html($term->name);
                    echo($after_title);
                    ?>
                </a>
            <?php
            endforeach;
            echo($after_tag);
        }
        $html .= ob_get_clean();
        return $html;
    }

}

if (!function_exists('jobsearch_employer_profile_awards')) {

    function jobsearch_employer_profile_awards($employer_id)
    {
        global $jobsearch_plugin_options;
        ob_start();
        $_allow_award_add = isset($jobsearch_plugin_options['allow_empl_awards']) ? $jobsearch_plugin_options['allow_empl_awards'] : '';
        if ($_allow_award_add == 'on') {
            $exfield_list = get_post_meta($employer_id, 'jobsearch_field_award_title', true);
            $award_imagefield_list = get_post_meta($employer_id, 'jobsearch_field_award_image', true);
            if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
                ?>
                <div class="widget widget_emp_awards">
                    <h2><?php esc_html_e('Awards', 'wp-jobsearch') ?></h2>
                    <div class="all-awards-items">
                        <?php
                        $exfield_counter = 0;
                        foreach ($exfield_list as $exfield) {

                            $exfield_title = isset($exfield_list[$exfield_counter]) ? $exfield_list[$exfield_counter] : '';
                            $award_imagefield_val = isset($award_imagefield_list[$exfield_counter]) ? $award_imagefield_list[$exfield_counter] : '';
                            $award_image_id = jobsearch_get_image_id($award_imagefield_val);
                            if ($award_image_id > 0) {
                                $award_thumbnail_image = wp_get_attachment_image_src($award_image_id, 'thumbnail');
                                $award_imagefield_val = isset($award_thumbnail_image[0]) && $award_thumbnail_image[0] != '' ? $award_thumbnail_image[0] : '';
                            }
                            ?>
                            <div class="award-item">
                                <img src="<?php echo($award_imagefield_val) ?>" alt="<?php echo($exfield_title) ?>"
                                     title="<?php echo($exfield_title) ?>">
                            </div>
                            <?php
                            $exfield_counter++;
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
            //
        }
        $html = ob_get_clean();
        $html = apply_filters('jobsearch_empdetal_awards_html', $html, $employer_id);
        return $html;
    }

}

if (!function_exists('jobsearch_employer_profile_affiliations')) {

    function jobsearch_employer_profile_affiliations($employer_id)
    {

        global $jobsearch_plugin_options;
        ob_start();
        $_allow_affiliation_add = isset($jobsearch_plugin_options['allow_empl_affiliations']) ? $jobsearch_plugin_options['allow_empl_affiliations'] : '';
        if ($_allow_affiliation_add == 'on') {
            $exfield_list = get_post_meta($employer_id, 'jobsearch_field_affiliation_title', true);
            $affiliation_imagefield_list = get_post_meta($employer_id, 'jobsearch_field_affiliation_image', true);
            if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
                ?>
                <div class="widget widget_emp_affiliations">
                    <h2><?php esc_html_e('Affiliations', 'wp-jobsearch') ?></h2>
                    <div class="all-affiliations-items">
                        <?php
                        $exfield_counter = 0;
                        foreach ($exfield_list as $exfield) {

                            $exfield_title = isset($exfield_list[$exfield_counter]) ? $exfield_list[$exfield_counter] : '';
                            $affiliation_imagefield_val = isset($affiliation_imagefield_list[$exfield_counter]) ? $affiliation_imagefield_list[$exfield_counter] : '';
                            $affiliation_image_id = jobsearch_get_image_id($affiliation_imagefield_val);
                            if ($affiliation_image_id > 0) {
                                $affiliation_thumbnail_image = wp_get_attachment_image_src($affiliation_image_id, 'thumbnail');
                                $affiliation_imagefield_val = isset($affiliation_thumbnail_image[0]) && $affiliation_thumbnail_image[0] != '' ? $affiliation_thumbnail_image[0] : '';
                            }
                            ?>
                            <div class="affiliation-item">
                                <img src="<?php echo($affiliation_imagefield_val) ?>"
                                     alt="<?php echo($exfield_title) ?>" title="<?php echo($exfield_title) ?>">
                            </div>
                            <?php
                            $exfield_counter++;
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
            //
        }
        $html = ob_get_clean();
        $html = apply_filters('jobsearch_empdetal_affils_html', $html, $employer_id);
        return $html;
    }

}

if (!function_exists('jobsearch_employer_not_allow_to_mod')) {

    function jobsearch_employer_not_allow_to_mod($user_id = 0)
    {
        global $jobsearch_plugin_options;
        if ($user_id <= 0 && is_user_logged_in()) {
            $user_id = get_current_user_id();
        }
        $user_is_employer = jobsearch_user_is_employer($user_id);
        if ($user_is_employer) {
            $demo_user_login = isset($jobsearch_plugin_options['demo_user_login']) ? $jobsearch_plugin_options['demo_user_login'] : '';
            $demo_user_mod = isset($jobsearch_plugin_options['demo_user_mod']) ? $jobsearch_plugin_options['demo_user_mod'] : '';
            $demo_employer = isset($jobsearch_plugin_options['demo_employer']) ? $jobsearch_plugin_options['demo_employer'] : '';
            $_demo_user_obj = get_user_by('login', $demo_employer);
            $_demo_user_id = isset($_demo_user_obj->ID) ? $_demo_user_obj->ID : '';
            if ($user_id == $_demo_user_id && $demo_user_login == 'on' && $demo_user_mod != 'on') {
                return true;
            }
        }
        return false;
    }

}

if (!function_exists('jobsearch_employer_get_all_sectors')) {

    function jobsearch_employer_get_all_sectors($employer_id, $link_class = '', $before_title = '', $after_title = '', $before_tag = '', $after_tag = '', $sec_link = '')
    {

        $sectors = wp_get_post_terms($employer_id, 'sector');
        ob_start();
        $html = '';
        if (!empty($sectors)) {
            $link_class_str = '';
            if ($link_class != '') {
                $link_class_str = 'class="' . $link_class . '"';
            }
            echo($before_tag);
            $flag = 0;
            foreach ($sectors as $term) :
                if ($flag > 0) {
                    echo ", ";
                }
                ?>
                <a <?php echo($sec_link != '' ? 'href="' . $sec_link . '?sector=' . $term->slug . '"' : '') ?>
                        class="<?php echo force_balance_tags($link_class) ?>">
                    <?php
                    echo($before_title);
                    echo esc_html($term->name);
                    echo($after_title);
                    ?>
                </a>
                <?php
                $flag++;
            endforeach;
            echo($after_tag);
        }
        $html .= ob_get_clean();
        return $html;
    }

}
if (!function_exists('jobsearch_get_employer_item_count')) {
    
    function jobsearch_get_employer_item_count($left_filter_count_switch, $count_posts_in, $count_arr, $employer_short_counter, $field_meta_key, $open_house = '') {
        global $wpdb;

        $total_num = 0;
        if ($left_filter_count_switch == 'yes') {
            if (!empty($count_posts_in) && is_array($count_posts_in)) {
                
                if (isset($count_arr[0]['key']) && $count_arr[0]['key'] != '' && !isset($count_arr[1]['key'])) {
                    $count_arr_o = $count_arr[0];
                    $the_meta_val = $count_arr_o['value'];
                    if (isset($count_arr_o['compare']) && $count_arr_o['compare'] == 'BETWEEN' && is_array($the_meta_val)) {
                        $the_meta_key = $count_arr_o['key'];
                        $from_meta_val = isset($the_meta_val[0]) ? $the_meta_val[0] : 0;
                        $to_meta_val = isset($the_meta_val[1]) ? $the_meta_val[1] : 1;
                        $meta_post_ids = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='{$the_meta_key}' AND meta_value BETWEEN {$from_meta_val} AND {$to_meta_val}");
                        if (!empty($meta_post_ids)) {
                            $to_countmeta_arr = array_intersect($count_posts_in, $meta_post_ids);
                            $total_num = !empty($to_countmeta_arr) ? count($to_countmeta_arr) : 0;
                        }
                    } else {
                        $get_meta_cond = get_meta_condition($count_arr_o);
                        $meta_post_ids = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE {$get_meta_cond}");
                        if (!empty($meta_post_ids)) {
                            $to_countmeta_arr = array_intersect($count_posts_in, $meta_post_ids);
                            $total_num = !empty($to_countmeta_arr) ? count($to_countmeta_arr) : 0;
                        }
                    }
                } else if (isset($count_arr[0]['type']) && $count_arr[0]['type'] == 'numeric' && isset($count_arr[1]['key'])) {
                    $count_arr_o = $count_arr[0];
                    $count_arr_1 = $count_arr[1];
                    $the_meta_key = $count_arr_o['key'];
                    $from_meta_val = $count_arr_o['value'];
                    $to_meta_val = $count_arr_1['value'];
                    $meta_post_ids = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='{$the_meta_key}' AND meta_value BETWEEN {$from_meta_val} AND {$to_meta_val}");
                    if (!empty($meta_post_ids)) {
                        $to_countmeta_arr = array_intersect($count_posts_in, $meta_post_ids);
                        $total_num = !empty($to_countmeta_arr) ? count($to_countmeta_arr) : 0;
                    }
                } else {
                    $total_num = !empty($count_posts_in) ? count($count_posts_in) : 0;
                }
            }
        }
        return $total_num;
    }

    function jobsearch_get_employer_item_count_depricate($left_filter_count_switch, $args, $count_arr, $employer_short_counter, $field_meta_key, $open_house = '') {
        if ($left_filter_count_switch == 'yes') {
            global $jobsearch_shortcode_employers_frontend;

            // get all arguments from getting flters
            $left_filter_arr = array();
            $left_filter_arr = $jobsearch_shortcode_employers_frontend->get_filter_arg($employer_short_counter, $field_meta_key);
            if (!empty($count_arr)) {
                // check if count array has multiple condition
                foreach ($count_arr as $count_arr_single) {
                    $left_filter_arr[] = $count_arr_single;
                }
            }

            $post_ids = '';
            if (!empty($left_filter_arr)) {
                // apply all filters and get ids
                $post_ids = $jobsearch_shortcode_employers_frontend->get_employer_id_by_filter($left_filter_arr);
            }

            if (isset($_REQUEST['location']) && $_REQUEST['location'] != '' && !isset($_REQUEST['loc_polygon_path'])) {
                $radius = isset($_REQUEST['radius']) ? $_REQUEST['radius'] : '';
                $post_ids = $jobsearch_shortcode_employers_frontend->employer_location_filter($_REQUEST['location'], $post_ids);
                if (empty($post_ids)) {
                    $post_ids = array(0);
                }
            }

            $all_post_ids = $post_ids;
            if (!empty($all_post_ids)) {
                $args['post__in'] = $all_post_ids;
            }

            $restaurant_loop_obj = jobsearch_get_cached_obj('employer_result_cached_loop_count_obj', $args, 12, false, 'wp_query');
            $restaurant_totnum = $restaurant_loop_obj->found_posts;
            return $restaurant_totnum;
        }
    }

}

function jobsearch_candsh_btn_catlist()
{

    $user_id = get_current_user_id();
    $user_is_employer = jobsearch_user_is_employer($user_id);

    $cats_list = '';

    if ($user_is_employer) {
        $employer_id = jobsearch_get_user_employer_id($user_id);
        $cats_list = get_post_meta($employer_id, 'emp_resumesh_types', true);
    }
    return $cats_list;
}

function jobsearch_is_employer_job_aplicant($candidate_id, $employer_id) {
    global $wpdb;
    $jobs_query = "SELECT ID FROM $wpdb->posts AS posts";
    $jobs_query .= " LEFT JOIN $wpdb->postmeta AS postmeta ON (posts.ID = postmeta.post_id)";
    $jobs_query .= " WHERE post_type='job' AND post_status='publish'";
    $jobs_query .= " AND postmeta.meta_key='jobsearch_field_job_posted_by' AND postmeta.meta_value=$employer_id";
    $jobs_query .= " ORDER BY ID DESC";
    $all_jobs = $wpdb->get_col($jobs_query);
    if (!empty($all_jobs)) {
        foreach ($all_jobs as $job_id) {
            $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
            $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
            if (!empty($job_applicants_list) && is_array($job_applicants_list) && in_array($candidate_id, $job_applicants_list)) {
                return true;
            }
        }
    }
    return false;
}

add_action('jobsearch_add_employer_resume_to_list_btn', 'jobsearch_add_employer_resume_to_list_btn', 10, 1);

function jobsearch_add_employer_resume_to_list_btn($args = array())
{
    $style = isset($args['style']) && $args['style'] != '' ? $args['style'] : '';
    ob_start();
    if (!is_user_logged_in()) {
        if ($style != "" && $style == "style1") {
            ?>
            <a href="javascript:void(0);" class="careerfy-featured-candidates-hr jobsearch-open-signin-tab"><i
                        class="fa fa-heart-o"></i></a>
        <?php } else if ($style == 'style4') { ?>
            <a href="javascript:void(0);" class="careerfy-sixteen-candidate-grid-like jobsearch-open-signin-tab"><i
                        class="fa fa-heart"></i></a>
        <?php } else if ($style == 'style5') { ?>
            <a href="javascript:void(0);" class="careerfy-style8-candidate-like jobsearch-open-signin-tab"><i
                        class="fa fa-heart"></i></a>
        <?php } else if ($style == 'cand5') {

            ?>
            <a href="javascript:void(0);" class="careerfy-candidate-save-btn jobsearch-open-signin-tab"><?php echo apply_filters('jobsearch_candidate_do_save_text', esc_html__('Save Candidate', 'wp-jobsearch')) ?>
            </a>
        <?php } else {

            ?>
            <a href="javascript:void(0);" class="jobsearch-candidate-default-btn jobsearch-open-signin-tab"><i
                        class="jobsearch-icon jobsearch-add-list"></i> <?php echo apply_filters('jobsearch_candidate_do_save_text', esc_html__('Save Candidate', 'wp-jobsearch')) ?>
            </a>
            <?php
        }
    } else {

        $candidate_id = isset($args['id']) ? $args['id'] : '';
        $download_cv = isset($args['download_cv']) ? $args['download_cv'] : '';

        $user_id = get_current_user_id();
        $user_isemp_member = false;
        if (jobsearch_user_isemp_member($user_id)) {
            $employer_id = jobsearch_user_isemp_member($user_id);
            $user_id = jobsearch_get_employer_user_id($employer_id);
            $user_isemp_member = true;
        }
        $user_is_employer = jobsearch_user_is_employer($user_id);
        $employer_resumes_list = array();
        if ($user_is_employer) {
            $employer_id = jobsearch_get_user_employer_id($user_id);

            $employer_resumes_list = get_post_meta($employer_id, 'jobsearch_candidates_list', true);
            $employer_resumes_list = explode(',', $employer_resumes_list);
        }
        $shortlist_str = in_array($candidate_id, $employer_resumes_list) ? apply_filters('jobsearch_candidate_alrdy_saved_text', esc_html__('Saved', 'wp-jobsearch')) : apply_filters('jobsearch_candidate_do_save_text', esc_html__('Save Candidate', 'wp-jobsearch'));

        $cats_list = jobsearch_candsh_btn_catlist();
        if (!empty($cats_list) && !in_array($candidate_id, $employer_resumes_list)) {
            if ($style != "" && $style == "style1") { ?>
                <a href="javascript:void(0);" data-id="<?php echo($candidate_id) ?>"
                   class="careerfy-featured-candidates-hr jobsearch-candidatesh-opopupbtn"><i class="fa fa-heart-o"></i></a>
            <?php } else if ($style != "" && $style == "style4") { ?>
                <a href="javascript:void(0);" data-id="<?php echo($candidate_id) ?>"
                   class="jobsearch-candidatesh-opopupbtn careerfy-sixteen-candidate-grid-like jobsearch-open-signin-tab"><i
                            class="fa fa-heart-o"></i></a>
            <?php } else if ($style != "" && $style == "style5") { ?>
                <a href="javascript:void(0);" data-id="<?php echo($candidate_id) ?>"
                   class="jobsearch-candidatesh-opopupbtn careerfy-style8-candidate-grid-like jobsearch-open-signin-tab"><i
                            class="fa fa-heart-o"></i></a>
            <?php } else if ($style != "" && $style == "cand5") {?>
                <a href="javascript:void(0);"
                   class="careerfy-candidate-save-btn jobsearch-candidatesh-opopupbtn"
                   data-id="<?php echo($candidate_id) ?>"><?php echo($shortlist_str) ?></a>
            <?php } else {
                ?>
                <a href="javascript:void(0);" class="jobsearch-candidate-default-btn jobsearch-candidatesh-opopupbtn"
                   data-id="<?php echo($candidate_id) ?>"><i
                            class="jobsearch-icon jobsearch-add-list"></i> <?php echo($shortlist_str) ?></a>
                <?php
            }
            $popup_args = array('args' => $args, 'cats_list' => $cats_list, 'style' => $style);
            add_action('wp_footer', function () use ($popup_args) {

                extract(shortcode_atts(array(
                    'args' => '',
                    'cats_list' => '',
                    'style' => '',
                ), $popup_args));

                $candidate_id = isset($args['id']) ? $args['id'] : '';
                ?>
                <div class="jobsearch-modal fade" id="JobSearchModalCandShPopup<?php echo($candidate_id) ?>">
                    <div class="modal-inner-area">&nbsp;</div>
                    <div class="modal-content-area">
                        <div class="modal-box-area">
                            <div class="jobsearch-modal-title-box">
                                <h2><?php esc_html_e('Choose Type', 'wp-jobsearch') ?></h2>
                                <span class="modal-close"><i class="fa fa-times"></i></span>
                            </div>
                            <div id="usercand-shrtlistsecs-<?php echo($candidate_id) ?>"
                                 class="jobsearch-usercand-shrtlistsec">
                                <div class="shcand-types-list">
                                    <div class="jobsearch-profile-select">
                                        <select name="shrtlist_type[]" multiple="multiple" class="selectize-select"
                                                placeholder="<?php esc_html_e('Select Types', 'wp-jobsearch') ?>">
                                            <?php
                                            $typse_count = 1;
                                            foreach ($cats_list as $sh_type_key => $sh_type) {
                                                ?>
                                                <option value="<?php echo($sh_type_key) ?>"><?php echo($sh_type) ?></option>
                                                <?php
                                                $typse_count++;
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="usercand-shrtlist-btnsec">
                                    <a href="javascript:void(0);"
                                       class="jobsearch-candidate-default-btn jobsearch-svcand-withtyp-tolist"
                                       data-id="<?php echo($candidate_id) ?>"><i
                                                class="jobsearch-icon jobsearch-add-list"></i> <?php echo apply_filters('jobsearch_candidate_do_save_text', esc_html__('Save Candidate', 'wp-jobsearch')) ?>
                                    </a>
                                    <?php if ($style != "style5") { ?>
                                        <span class="resume-loding-msg"></span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }, 11, 1);
        } else {
            if ($style == "style1" && $style != "") {
                ?>
                <a href="javascript:void(0);"
                   class="careerfy-featured-candidates-hr <?php echo(in_array($candidate_id, $employer_resumes_list) ? '' : 'jobsearch-add-resume-to-list') ?>"
                   data-id="<?php echo($candidate_id) ?>" data-download="<?php echo($download_cv) ?>">
                    <i class="fa fa-heart"></i></a>
            <?php } else if ($style == "style4") { ?>
                <a href="javascript:void(0);"
                   class="careerfy-sixteen-candidate-grid-like <?php echo(in_array($candidate_id, $employer_resumes_list) ? '' : 'jobsearch-add-resume-to-list') ?>"
                   data-id="<?php echo($candidate_id) ?>" data-download="<?php echo($download_cv) ?>">
                    <i class="fa fa-heart"></i></a>
            <?php } else if ($style == "style5") { ?>
                <a href="javascript:void(0);"
                   class="careerfy-style8-candidate-like <?php echo(in_array($candidate_id, $employer_resumes_list) ? '' : 'jobsearch-add-resume-to-list') ?>"
                   data-id="<?php echo($candidate_id) ?>" data-download="<?php echo($download_cv) ?>" data-style="true">
                    <i class="fa fa-heart"></i></a>
            <?php } else if ($style == "cand5") { ?>
                <a href="javascript:void(0);"
                   class="careerfy-candidate-save-btn <?php echo(in_array($candidate_id, $employer_resumes_list) ? '' : 'jobsearch-add-resume-to-list') ?>"
                   data-id="<?php echo($candidate_id) ?>" data-download="<?php echo($download_cv) ?>"> <?php echo($shortlist_str) ?></a>
            <?php } else { ?>
                <a href="javascript:void(0);"
                   class="jobsearch-candidate-default-btn <?php echo(in_array($candidate_id, $employer_resumes_list) ? '' : 'jobsearch-add-resume-to-list') ?>"
                   data-id="<?php echo($candidate_id) ?>" data-download="<?php echo($download_cv) ?>"><i
                            class="jobsearch-icon jobsearch-add-list"></i> <?php echo($shortlist_str) ?></a>

            <?php } ?>
            <?php if ($style != "style5") { ?>
                <span class="resume-loding-msg"></span>
                <?php
            }
        }
    }
    $html = ob_get_clean();
    echo $html;
}

add_action('wp_ajax_jobsearch_upd_employer_resume_to_list', 'jobsearch_upd_employer_resume_to_list');

function jobsearch_upd_employer_resume_to_list() {
    
    $candidate_id = isset($_POST['candidate_id']) ? $_POST['candidate_id'] : '0';
    $act_user_id = $user_id = get_current_user_id();
    $c_user = wp_get_current_user();

    $user_isemp_member = false;
    if (jobsearch_user_isemp_member($user_id)) {
        $employer_id = jobsearch_user_isemp_member($user_id);
        $user_id = jobsearch_get_employer_user_id($employer_id);
        $c_user = get_user_by('ID', $user_id);
        $user_isemp_member = true;
    }

    $user_is_employer = jobsearch_user_is_employer($user_id);
    if ($user_is_employer) {
        
        $employer_id = jobsearch_get_user_employer_id($user_id);
        
        if (isset($_POST['type_selected']) && !empty($_POST['type_selected'])) {
            $types_selected = $_POST['type_selected'];
            $_resume_typsh_list = get_post_meta($employer_id, 'jobsearch_resumtypes_list', true);
            $_resume_typsh_list = !empty($_resume_typsh_list) ? $_resume_typsh_list : array();
            $_resume_typsh_list[$candidate_id] = $types_selected;
            update_post_meta($employer_id, 'jobsearch_resumtypes_list', $_resume_typsh_list);
        }
    }
    echo json_encode(array('msg' => esc_html__('Updated', 'wp-jobsearch'), 'error' => '0'));
    die;
}

add_action('wp_ajax_jobsearch_add_employer_resume_to_list', 'jobsearch_add_employer_resume_to_list');

function jobsearch_add_employer_resume_to_list()
{
    global $jobsearch_plugin_options;
    $free_shortlist_allow = isset($jobsearch_plugin_options['free-shortlist-allow']) ? $jobsearch_plugin_options['free-shortlist-allow'] : '';

    $employer_pkgs_page = isset($jobsearch_plugin_options['resume_package_page']) ? $jobsearch_plugin_options['resume_package_page'] : '';

    $employer_pkgs_page_url = '';
    if ($employer_pkgs_page != '') {
        $employer_pkgs_page_obj = get_page_by_path($employer_pkgs_page);
        if (is_object($employer_pkgs_page_obj) && isset($employer_pkgs_page_obj->ID)) {
            $employer_pkgs_page_url = get_permalink($employer_pkgs_page_obj->ID);
        }
    }

    if (!is_user_logged_in()) {
        echo json_encode(array('msg' => esc_html__('You are not logged in.', 'wp-jobsearch'), 'error' => '1'));
        die;
    }

    //
    $candidate_id = isset($_POST['candidate_id']) ? $_POST['candidate_id'] : '0';
    $act_user_id = $user_id = get_current_user_id();
    $c_user = wp_get_current_user();

    $user_isemp_member = false;
    if (jobsearch_user_isemp_member($user_id)) {
        $employer_id = jobsearch_user_isemp_member($user_id);
        $user_id = jobsearch_get_employer_user_id($employer_id);
        $c_user = get_user_by('ID', $user_id);
        $user_isemp_member = true;
    }

    $user_is_employer = jobsearch_user_is_employer($user_id);
    if ($user_is_employer) {
        $employer_id = jobsearch_get_user_employer_id($user_id);
        $employer_resumes_list = get_post_meta($employer_id, 'jobsearch_candidates_list', true);


        if ($user_isemp_member) {
            $usermemb_resumes_list = get_user_meta($act_user_id, 'jobsearch_candidates_list', true);
        }

        if ($free_shortlist_allow == 'on') {
            if ($employer_resumes_list != '') {
                $employer_resumes_list = explode(',', $employer_resumes_list);
                if (!in_array($candidate_id, $employer_resumes_list)) {
                    $employer_resumes_list[] = $candidate_id;
                }
                $employer_resumes_list = implode(',', $employer_resumes_list);
            } else {
                $employer_resumes_list = $candidate_id;
            }
            update_post_meta($employer_id, 'jobsearch_candidates_list', $employer_resumes_list);
            if ($user_isemp_member) {
                $usermemb_resumes_list = !empty($usermemb_resumes_list) ? $usermemb_resumes_list : array();
                $usermemb_resumes_list[] = $candidate_id;
                update_user_meta($act_user_id, 'jobsearch_candidates_list', $usermemb_resumes_list);
            }

            if (isset($_POST['type_selected']) && !empty($_POST['type_selected'])) {
                $types_selected = $_POST['type_selected'];
                $_resume_typsh_list = get_post_meta($employer_id, 'jobsearch_resumtypes_list', true);
                $_resume_typsh_list = !empty($_resume_typsh_list) ? $_resume_typsh_list : array();
                $_resume_typsh_list[$candidate_id] = $types_selected;
                update_post_meta($employer_id, 'jobsearch_resumtypes_list', $_resume_typsh_list);
            }

            //
            do_action('jobsearch_user_shortlist_to_candidate', $c_user, $candidate_id);
            do_action('jobsearch_user_shortlist_to_employer', $c_user, $candidate_id);

            echo json_encode(array('msg' => esc_html__('Resume added to the list.', 'wp-jobsearch')));
            die;
        } else {
            $is_emp_applicant = jobsearch_is_employer_job_aplicant($candidate_id, $employer_id);
            
            $user_cv_pkg = jobsearch_employer_first_subscribed_cv_pkg($user_id);
            if (!$user_cv_pkg) {
                $user_cv_pkg = jobsearch_allin_first_pkg_subscribed($user_id, 'cvs');
            }
            if (!$user_cv_pkg) {
                $user_cv_pkg = jobsearch_emprof_first_pkg_subscribed($user_id, 'cvs');
            }
            if ($user_cv_pkg || $is_emp_applicant) {
                if ($employer_resumes_list != '') {
                    $employer_resumes_list = explode(',', $employer_resumes_list);
                    if (!in_array($candidate_id, $employer_resumes_list)) {
                        $employer_resumes_list[] = $candidate_id;
                    }
                    $employer_resumes_list = implode(',', $employer_resumes_list);
                } else {
                    $employer_resumes_list = $candidate_id;
                }
                $download_cv = isset($_POST['download_cv']) ? $_POST['download_cv'] : '';
                update_post_meta($employer_id, 'jobsearch_candidates_list', $employer_resumes_list);
                if ($user_isemp_member) {
                    $usermemb_resumes_list = !empty($usermemb_resumes_list) ? $usermemb_resumes_list : array();
                    $usermemb_resumes_list[] = $candidate_id;
                    update_user_meta($act_user_id, 'jobsearch_candidates_list', $usermemb_resumes_list);
                }

                if (isset($_POST['type_selected']) && !empty($_POST['type_selected'])) {
                    $types_selected = $_POST['type_selected'];
                    $_resume_typsh_list = get_post_meta($employer_id, 'jobsearch_resumtypes_list', true);
                    $_resume_typsh_list = !empty($_resume_typsh_list) ? $_resume_typsh_list : array();
                    $_resume_typsh_list[$candidate_id] = $types_selected;
                    update_post_meta($employer_id, 'jobsearch_resumtypes_list', $_resume_typsh_list);
                }
                if (!$is_emp_applicant) {
                    do_action('jobsearch_add_candidate_resume_id_to_order', $candidate_id, $user_cv_pkg);
                }

                $downloadcv_link_btn = '';
                if ($download_cv == '1') {
                    $candidate_cv_file = get_post_meta($candidate_id, 'candidate_cv_file', true);

                    $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
                    if ($multiple_cv_files_allow == 'on') {
                        $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);
                        if (!empty($ca_at_cv_files)) {
                            ob_start();
                            ?>
                            <a href="<?php echo apply_filters('jobsearch_user_attach_cv_file_url', '', $candidate_id, 0) ?>"
                               oncontextmenu="javascript: return false;"
                               onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                               download="<?php echo apply_filters('jobsearch_user_attach_cv_file_title', '', $candidate_id, 0) ?>"
                               class="jobsearch-candidate-download-btn"><i
                                        class="jobsearch-icon jobsearch-download-arrow"></i> <?php echo apply_filters('jobsearch_cand_downlod_cv_btntxt', esc_html__('Download CV', 'wp-jobsearch')) ?>
                            </a>
                            <?php
                            $downloadcv_link_btn = ob_get_clean();
                        }
                    } else if (!empty($candidate_cv_file)) {
                        $file_attach_id = isset($candidate_cv_file['file_id']) ? $candidate_cv_file['file_id'] : '';
                        $file_url = isset($candidate_cv_file['file_url']) ? $candidate_cv_file['file_url'] : '';

                        $filename = isset($candidate_cv_file['file_name']) ? $candidate_cv_file['file_name'] : '';

                        $file_url = apply_filters('wp_jobsearch_user_cvfile_downlod_url', $file_url, $file_attach_id, $candidate_id);
                        ob_start();
                        ?>
                        <a href="<?php echo($file_url) ?>" download="<?php echo($filename) ?>"
                           oncontextmenu="javascript: return false;"
                           onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                           class="jobsearch-candidate-download-btn"><i
                                    class="jobsearch-icon jobsearch-download-arrow"></i> <?php echo apply_filters('jobsearch_cand_downlod_cv_btntxt', esc_html__('Download CV', 'wp-jobsearch')) ?>
                        </a>
                        <?php
                        $downloadcv_link_btn = ob_get_clean();
                    }
                }
                //
                do_action('jobsearch_user_shortlist_to_candidate', $c_user, $candidate_id);
                do_action('jobsearch_user_shortlist_to_employer', $c_user, $candidate_id);
                echo json_encode(array('msg' => esc_html__('Resume added to the list.', 'wp-jobsearch'), 'dbn' => $downloadcv_link_btn));
                die;
            } else {
                if ($employer_pkgs_page_url != '') {
                    $err_msg = wp_kses(sprintf(__('You have no package. <a href="%s">Click here</a> to subscribe a package.', 'wp-jobsearch'), $employer_pkgs_page_url), array('a' => array('href' => array())));
                } else {
                    $err_msg = esc_html__('You have no package. Please subscribe to a package first.', 'wp-jobsearch');
                }
                echo json_encode(array('msg' => $err_msg, 'error' => '1'));
                die;
            }
        }
    } else {
        echo json_encode(array('msg' => esc_html__('You are not an employer.', 'wp-jobsearch'), 'error' => '1'));
        die;
    }
}

add_action('jobsearch_download_candidate_cv_btn', 'jobsearch_download_candidate_cv_btn', 10, 1);

function jobsearch_download_candidate_cv_btn($args = array())
{

    global $jobsearch_plugin_options;
    $free_shortlist_allow = isset($jobsearch_plugin_options['free-shortlist-allow']) ? $jobsearch_plugin_options['free-shortlist-allow'] : '';
    $candidate_id = isset($args['id']) ? $args['id'] : '';
    $classes = isset($args['classes']) ? $args['classes'] : '';
    $view = isset($args['view']) ? $args['view'] : '';
    $classes_ext = '';
    if (isset($classes) && !empty($classes)) {
        $classes_ext = ' ' . $classes . '';
    }
    $candidate_cv_file = get_post_meta($candidate_id, 'candidate_cv_file', true);

    $candidate_obj = get_post($candidate_id);
    $candidate_join_date = isset($candidate_obj->post_date) ? $candidate_obj->post_date : '';

    $candidate_jobtitle = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);

    $candidate_user_id = jobsearch_get_candidate_user_id($candidate_id);
    $candidate_user_obj = get_user_by('ID', $candidate_user_id);
    $candidate_displayname = isset($candidate_user_obj->display_name) ? $candidate_user_obj->display_name : '';
    $candidate_displayname = apply_filters('jobsearch_user_display_name', $candidate_displayname, $candidate_user_obj);

    $user_def_avatar_url = jobsearch_candidate_img_url_comn($candidate_id);

    $candidate_cv_file_att = array();
    $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
    if ($multiple_cv_files_allow == 'on') {
        $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);
        if (!empty($ca_at_cv_files)) {
            $candidate_cv_file_att = array(
                'file_title' => apply_filters('jobsearch_user_attach_cv_file_title', '', $candidate_id, 0),
                'file_url' => apply_filters('jobsearch_user_attach_cv_file_url', '', $candidate_id, 0),
            );
        }
    } else if (!empty($candidate_cv_file)) {
        $file_attach_id = isset($candidate_cv_file['file_id']) ? $candidate_cv_file['file_id'] : '';
        $file_url = isset($candidate_cv_file['file_url']) ? $candidate_cv_file['file_url'] : '';

        $file_url = apply_filters('wp_jobsearch_user_cvfile_downlod_url', $file_url, $file_attach_id, $candidate_id);

        $filename = isset($candidate_cv_file['file_name']) ? $candidate_cv_file['file_name'] : '';

        $candidate_cv_file_att = array(
            'file_title' => $filename,
            'file_url' => $file_url,
        );
    }

    if (!empty($candidate_cv_file_att)) {
        $cv_file_title = isset($candidate_cv_file_att['file_title']) ? $candidate_cv_file_att['file_title'] : '';
        $file_url = isset($candidate_cv_file_att['file_url']) ? $candidate_cv_file_att['file_url'] : '';

        ob_start();
        if ($view == 'cand5') { ?>
            <a href="<?php echo($file_url) ?>" download="<?php echo($cv_file_title) ?>"
               oncontextmenu="javascript: return false;"
               onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
               class="careerfy-candidate-cv-btn<?php echo($classes_ext); ?>"> <?php echo apply_filters('jobsearch_cand_downlod_cv_btntxt', esc_html__('Download CV', 'wp-jobsearch')) ?>
            </a>
        <?php } else { ?>
            <a href="<?php echo($file_url) ?>" download="<?php echo($cv_file_title) ?>"
               oncontextmenu="javascript: return false;"
               onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
               class="jobsearch-candidate-download-btn<?php echo($classes_ext); ?>"><i
                        class="jobsearch-icon jobsearch-download-arrow"></i> <?php echo apply_filters('jobsearch_cand_downlod_cv_btntxt', esc_html__('Download CV', 'wp-jobsearch')) ?>
            </a>

        <?php }
        $download_link_btn = ob_get_clean();
        //
        if (!is_user_logged_in()) {
            if ($view == 'cand5') { ?>
                <a href="javascript:void(0);"
                   class="careerfy-candidate-cv-btn jobsearch-open-signin-tab<?php echo($classes_ext); ?>"><?php echo apply_filters('jobsearch_cand_downlod_cv_btntxt', esc_html__('Download CV', 'wp-jobsearch')) ?></a>
            <?php } else { ?>
                <a href="javascript:void(0);"
                   class="jobsearch-candidate-download-btn jobsearch-open-signin-tab<?php echo($classes_ext); ?>"><?php echo apply_filters('jobsearch_cand_downlod_cv_btntxt', esc_html__('Download CV', 'wp-jobsearch')) ?></a>
            <?php } ?>
        <?php } else {
            $user_id = get_current_user_id();
            $cur_user_obj = wp_get_current_user();
            $is_employer = jobsearch_user_is_employer($user_id);

            if (in_array('administrator', (array)$cur_user_obj->roles)) {
                echo($download_link_btn);
            } else if ($is_employer) {

                $cand_profile_restrict = new Candidate_Profile_Restriction;

                $employer_id = jobsearch_get_user_employer_id($user_id);
                $employer_resumes_list = get_post_meta($employer_id, 'jobsearch_candidates_list', true);
                $employer_resumes_list = explode(',', $employer_resumes_list);

                if (in_array($candidate_id, $employer_resumes_list) || ($free_shortlist_allow == 'on')) {
                    echo ($download_link_btn);
                } else {
                    ob_start();
                    if ($view == 'cand5') { ?>
                        <a href="javascript:void(0);" data-id="<?php echo($candidate_id) ?>"
                           class="careerfy-candidate-cv-btn jobsearch-open-dloadres-popup<?php echo($classes_ext); ?>"> <?php echo apply_filters('jobsearch_cand_downlod_cv_btntxt', esc_html__('Download CV', 'wp-jobsearch')) ?>
                        </a>
                    <?php } else { ?>
                        <a href="javascript:void(0);" data-id="<?php echo($candidate_id) ?>"
                           class="jobsearch-candidate-download-btn jobsearch-open-dloadres-popup<?php echo($classes_ext); ?>"><i
                                    class="jobsearch-icon jobsearch-download-arrow"></i> <?php echo apply_filters('jobsearch_cand_downlod_cv_btntxt', esc_html__('Download CV', 'wp-jobsearch')) ?>
                        </a>
                    <?php } ?>
                    <?php
                    $popup_btn_html = ob_get_clean();
                    echo apply_filters('jobsearch_candetail_downlodcv_savepopup_btn', $popup_btn_html, $classes_ext, $candidate_cv_file_att, $candidate_id);
                    ?>
                    <div class="jobsearch-modal jobsearch-typo-wrap fade"
                         id="JobSearchDLoadResModal<?php echo($candidate_id) ?>">
                        <div class="modal-inner-area">&nbsp;</div>
                        <div class="modal-content-area">
                            <div class="modal-box-area">
                                <div class="user-shortlist-area">
                                    <h4><?php esc_html_e('You must have to save this candidate before download CV.', 'wp-jobsearch') ?></h4>
                                    <div class="shortlisting-user-info">
                                        <?php
                                        if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|profile_img', 'detail_page')) {
                                            ?>
                                            <figure><img src="<?php echo($user_def_avatar_url) ?>" alt=""></figure>
                                            <?php
                                        }
                                        if ($cand_profile_restrict::cand_field_is_locked('profile_fields|display_name', 'detail_page')) {
                                            $user_displayname = $cand_profile_restrict::cand_restrict_display_name();
                                            ?>
                                            <h2><a><?php echo ($user_displayname) ?></a></h2>
                                            <?php
                                        } else {
                                            ?>
                                            <h2><a><?php echo ($candidate_displayname) ?></a></h2>
                                            <?php
                                        }
                                        if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|job_title', 'detail_page')) {
                                            ?>
                                            <p><?php echo($candidate_jobtitle) ?></p>
                                            <?php
                                        }
                                        if ($candidate_join_date != '') {
                                            ?>
                                            <small><?php printf(esc_html__('Member Since, %s', 'wp-jobsearch'), date_i18n(get_option('date_format'), strtotime($candidate_join_date))) ?></small>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <div class="shortlisting-user-btn">
                                        <?php
                                        do_action('jobsearch_add_employer_resume_to_list_btn', array('id' => $candidate_id, 'download_cv' => '1'));
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else { ?>
                <?php if ($view == 'cand5') { ?>
                    <a href="javascript:void(0);"
                       class="careerfy-candidate-cv-btn employer-access-btn<?php echo($classes_ext); ?>"><i
                                class="jobsearch-icon jobsearch-download-arrow"></i> <?php echo apply_filters('jobsearch_cand_downlod_cv_btntxt', esc_html__('Download CV', 'wp-jobsearch')) ?>
                    </a>
                <?php } else { ?>
                    <a href="javascript:void(0);"
                       class="jobsearch-candidate-download-btn employer-access-btn<?php echo($classes_ext); ?>"><i
                                class="jobsearch-icon jobsearch-download-arrow"></i> <?php echo apply_filters('jobsearch_cand_downlod_cv_btntxt', esc_html__('Download CV', 'wp-jobsearch')) ?>
                    </a>
                <?php } ?>
                <span class="employer-access-msg" style="display: none; float: left;"><i
                            class="fa fa-warning"></i> <?php esc_html_e('Only an Employer can download a resume.', 'wp-jobsearch') ?></span>
                <?php
            }
        }
    }
}

add_action('jobsearch_user_profile_before', 'jobsearch_cv_view_credit_consume', 15, 1);

function jobsearch_cv_view_credit_consume($candidate_id)
{
    global $jobsearch_plugin_options;
    $free_shortlist_allow = isset($jobsearch_plugin_options['free-shortlist-allow']) ? $jobsearch_plugin_options['free-shortlist-allow'] : '';

    $user_id = get_current_user_id();
    $user_is_employer = jobsearch_user_is_employer($user_id);
    if ($user_is_employer) {
        $employer_id = jobsearch_get_user_employer_id($user_id);
        
        $is_emp_applicant = jobsearch_is_employer_job_aplicant($candidate_id, $employer_id);

        $allin_pkg_consume = false;
        $emprof_pkg_consume = false;
        $user_cv_pkg = jobsearch_employer_first_subscribed_cv_pkg($user_id);
        if (!$user_cv_pkg) {
            $user_cv_pkg = jobsearch_allin_first_pkg_subscribed($user_id, 'cvs');
            if ($user_cv_pkg) {
                $allin_pkg_consume = true;
            }
        }
        if (!$user_cv_pkg) {
            $user_cv_pkg = jobsearch_emprof_first_pkg_subscribed($user_id, 'cvs');
            if ($user_cv_pkg) {
                $emprof_pkg_consume = true;
            }
        }

        if ($allin_pkg_consume) {
            $onview_credit_consume = get_post_meta($user_cv_pkg, 'allinview_consume_cvs', true);
        } else if ($emprof_pkg_consume) {
            $onview_credit_consume = get_post_meta($user_cv_pkg, 'emprofview_consume_cvs', true);
        } else {
            $onview_credit_consume = get_post_meta($user_cv_pkg, 'onview_consume_cvs', true);
        }
        if (get_post_type($candidate_id) == 'candidate' && is_user_logged_in() && $free_shortlist_allow != 'on' && $onview_credit_consume == 'on' && !$is_emp_applicant) {

            $employer_resumes_list = get_post_meta($employer_id, 'jobsearch_candidates_list', true);

            if ($user_cv_pkg) {
                $add_to_order = false;
                if ($employer_resumes_list != '') {
                    $employer_resumes_list = explode(',', $employer_resumes_list);
                    if (!in_array($candidate_id, $employer_resumes_list)) {
                        $employer_resumes_list[] = $candidate_id;
                        $add_to_order = true;
                    }
                    $employer_resumes_list = implode(',', $employer_resumes_list);
                } else {
                    $employer_resumes_list = $candidate_id;
                    $add_to_order = true;
                }
                //update_post_meta($employer_id, 'jobsearch_candidates_list', $employer_resumes_list);
                //
                if ($add_to_order) {
                    do_action('jobsearch_add_candidate_resume_id_to_order', $candidate_id, $user_cv_pkg);
                }
            }
        }
    }
}

// Check if job is post by current employer
function jobsearch_is_employer_job($job_id = 0, $user_id = 0)
{
    global $sitepress;
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $user_id = apply_filters('jobsearch_in_isempjob_user_id', $user_id, $job_id);
    if (jobsearch_user_isemp_member($user_id)) {
        $employer_id = jobsearch_user_isemp_member($user_id);
    } else {
        $employer_id = jobsearch_get_user_employer_id($user_id);
    }

    $job_employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
    if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
        $current_lang = $sitepress->get_current_language();
        $icl_post_id = icl_object_id($job_employer_id, 'employer', false, $current_lang);

        if ($icl_post_id > 0) {
            $job_employer_id = $icl_post_id;
        }
    }
    if ($employer_id == $job_employer_id) {
        return true;
    }
    return false;
}

// get user package used jobs
function jobsearch_pckg_order_used_fjobs($order_id = 0)
{
    $jobs_list_count = 0;
    if ($order_id > 0) {
        $jobs_list = get_post_meta($order_id, 'jobsearch_order_fjobs_list', true);

        if (!empty($jobs_list)) {
            $jobs_list_count = count(explode(',', $jobs_list));
        }
    }

    return $jobs_list_count;
}

// get user package remaining jobs
function jobsearch_pckg_order_remaining_fjobs($order_id = 0)
{
    $remaining_jobs = 0;
    if ($order_id > 0) {
        $total_jobs = get_post_meta($order_id, 'num_of_fjobs', true);
        $used_jobs = jobsearch_pckg_order_used_fjobs($order_id);

        $remaining_jobs = $total_jobs > $used_jobs ? $total_jobs - $used_jobs : 0;
    }

    return $remaining_jobs;
}

function jobsearch_pckg_order_used_featjob_credits($order_id = 0)
{
    $jobs_list_count = 0;
    if ($order_id > 0) {

        $jobs_list = get_post_meta($order_id, 'jobsearch_order_featc_list', true);

        if (!empty($jobs_list)) {
            $jobs_list_count = count(explode(',', $jobs_list));
        }
    }

    return $jobs_list_count;
}

function jobsearch_pckg_order_remain_featjob_credits($order_id = 0)
{
    $remaining_credits = 0;
    if ($order_id > 0) {
        $total_credits = get_post_meta($order_id, 'feat_job_credits', true);
        $used_jobs = jobsearch_pckg_order_used_featjob_credits($order_id);

        $remaining_credits = $total_credits > $used_jobs ? $total_credits - $used_jobs : 0;
    }

    return $remaining_credits;
}

// check if user package subscribed
function jobsearch_fjobs_pckg_is_subscribed($pckg_id = 0, $user_id = 0)
{
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'package_type',
                'value' => 'featured_jobs',
                'compare' => '=',
            ),
            array(
                'key' => 'jobsearch_order_package',
                'value' => $pckg_id,
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;
    if (!empty($pkgs_query_posts)) {
        foreach ($pkgs_query_posts as $order_post_id) {
            $remaining_jobs = jobsearch_pckg_order_remaining_fjobs($order_post_id);
            if ($remaining_jobs > 0) {
                return $order_post_id;
            }
        }
    }
    return false;
}

function jobsearch_fjobs_first_pkg_subscribed($user_id = 0) {

    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'package_type',
                'value' => 'featured_jobs',
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;
    if (!empty($pkgs_query_posts)) {
        foreach ($pkgs_query_posts as $order_post_id) {
            $remaining_jobs = jobsearch_pckg_order_remaining_fjobs($order_post_id);
            if ($remaining_jobs > 0) {
                return $order_post_id;
            }
        }
    }
    return false;
}

// check if user package subscribed
function jobsearch_fjobs_pckg_order_is_expired($order_id = 0)
{

    $order_post_id = $order_id;
    $expiry_timestamp = get_post_meta($order_post_id, 'package_expiry_timestamp', true);


    if ($expiry_timestamp <= strtotime(current_time('d-m-Y H:i:s'))) {
        return true;
    }

    $remaining_jobs = jobsearch_pckg_order_remaining_fjobs($order_post_id);
    if ($remaining_jobs < 1) {
        return true;
    }
    return false;
}

// get user package used jobs
function jobsearch_pckg_order_used_jobs($order_id = 0)
{
    $jobs_list_count = 0;
    if ($order_id > 0) {
        $total_jobs = get_post_meta($order_id, 'num_of_jobs', true);
        $jobs_list = get_post_meta($order_id, 'jobsearch_order_jobs_list', true);

        if (!empty($jobs_list)) {
            $jobs_list_count = count(explode(',', $jobs_list));
        }
    }

    return $jobs_list_count;
}

// get user package remaining jobs
function jobsearch_pckg_order_remaining_jobs($order_id = 0)
{
    $remaining_jobs = 0;
    if ($order_id > 0) {
        $total_jobs = get_post_meta($order_id, 'num_of_jobs', true);
        $used_jobs = jobsearch_pckg_order_used_jobs($order_id);

        $remaining_jobs = $total_jobs > $used_jobs ? $total_jobs - $used_jobs : 0;
    }

    return $remaining_jobs;
}

// check if user package subscribed
function jobsearch_pckg_is_subscribed($pckg_id = 0, $user_id = 0)
{
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'package_type',
                'value' => 'job',
                'compare' => '=',
            ),
            array(
                'key' => 'jobsearch_order_package',
                'value' => $pckg_id,
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);
    $pkgs_query_posts = $pkgs_query->posts;

    if (!empty($pkgs_query_posts)) {
        foreach ($pkgs_query_posts as $order_post_id) {
            $remaining_jobs = jobsearch_pckg_order_remaining_jobs($order_post_id);
            if ($remaining_jobs > 0) {
                return $order_post_id;
            }
        }
    }
    return false;
}

// check if user package subscribed
function jobsearch_pckg_order_is_expired($order_id = 0)
{

    $order_post_id = $order_id;
    $expiry_timestamp = get_post_meta($order_post_id, 'package_expiry_timestamp', true);


    if ($expiry_timestamp <= strtotime(current_time('d-m-Y H:i:s'))) {
        return true;
    }

    $remaining_jobs = jobsearch_pckg_order_remaining_jobs($order_post_id);
    if ($remaining_jobs < 1) {
        return true;
    }
    return false;
}

/*
 * All in on package functions
 * Start here
 */

// get used jobs
function jobsearch_allinpckg_order_used_jobs($order_id = 0)
{
    $jobs_list_count = 0;
    if ($order_id > 0) {
        $total_jobs = get_post_meta($order_id, 'allin_num_jobs', true);
        $jobs_list = get_post_meta($order_id, 'jobsearch_order_jobs_list', true);

        if (!empty($jobs_list)) {
            $jobs_list_count = count(explode(',', $jobs_list));
        }
    }

    return $jobs_list_count;
}

// get remaining jobs
function jobsearch_allinpckg_order_remaining_jobs($order_id = 0)
{
    $remaining_jobs = 0;
    if ($order_id > 0) {
        $total_jobs = get_post_meta($order_id, 'allin_num_jobs', true);
        $used_jobs = jobsearch_allinpckg_order_used_jobs($order_id);

        $remaining_jobs = $total_jobs > $used_jobs ? $total_jobs - $used_jobs : 0;
    }

    return $remaining_jobs;
}

// get used feature jobs
function jobsearch_allinpckg_order_used_fjobs($order_id = 0)
{
    $jobs_list_count = 0;
    if ($order_id > 0) {
        $total_jobs = get_post_meta($order_id, 'allin_num_fjobs', true);
        $jobs_list = get_post_meta($order_id, 'jobsearch_order_fjobs_list', true);

        if (!empty($jobs_list)) {
            $jobs_list_count = count(explode(',', $jobs_list));
        }
    }

    return $jobs_list_count;
}

// get remaining feature jobs
function jobsearch_allinpckg_order_remaining_fjobs($order_id = 0)
{
    $remaining_jobs = 0;
    if ($order_id > 0) {
        $total_jobs = get_post_meta($order_id, 'allin_num_fjobs', true);
        $used_jobs = jobsearch_allinpckg_order_used_fjobs($order_id);

        $remaining_jobs = $total_jobs > $used_jobs ? $total_jobs - $used_jobs : 0;
    }

    return $remaining_jobs;
}

// get used cvs
function jobsearch_allinpckg_order_used_cvs($order_id = 0)
{
    $cvs_list_count = 0;
    if ($order_id > 0) {
        $cvs_list = get_post_meta($order_id, 'jobsearch_order_cvs_list', true);

        if (!empty($cvs_list)) {
            $cvs_list_count = count(explode(',', $cvs_list));
        }
    }

    return $cvs_list_count;
}

// get remaining cvs
function jobsearch_allinpckg_order_remaining_cvs($order_id = 0)
{
    $remaining_cvs = 0;
    if ($order_id > 0) {
        $total_cvs = get_post_meta($order_id, 'allin_num_cvs', true);
        $used_cvs = jobsearch_allinpckg_order_used_cvs($order_id);

        $remaining_cvs = $total_cvs > $used_cvs ? $total_cvs - $used_cvs : 0;
    }

    return $remaining_cvs;
}

// check if user package subscribed
function jobsearch_allinpckg_is_subscribed($pckg_id = 0, $user_id = 0, $ptype = 'jobs')
{
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'package_type',
                'value' => 'emp_allin_one',
                'compare' => '=',
            ),
            array(
                'key' => 'jobsearch_order_package',
                'value' => $pckg_id,
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;
    if (!empty($pkgs_query_posts)) {
        foreach ($pkgs_query_posts as $order_post_id) {
            if ($ptype == 'cvs') {
                $remaining_jobs = jobsearch_allinpckg_order_remaining_cvs($order_post_id);
            } else {
                $remaining_jobs = jobsearch_allinpckg_order_remaining_jobs($order_post_id);
            }
            if ($remaining_jobs > 0) {
                return $order_post_id;
            }
        }
    }
    return false;
}

// check if user package subscribed
function jobsearch_allin_first_pkg_subscribed($user_id = 0, $ptype = 'jobs')
{

    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'package_type',
                'value' => 'emp_allin_one',
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;
    if (!empty($pkgs_query_posts)) {
        foreach ($pkgs_query_posts as $order_post_id) {
            if ($ptype == 'cvs') {
                $remaining_jobs = jobsearch_allinpckg_order_remaining_cvs($order_post_id);
            } else {
                $remaining_jobs = jobsearch_allinpckg_order_remaining_jobs($order_post_id);
            }
            if ($remaining_jobs > 0) {
                return $order_post_id;
            }
        }
    }
    return false;
}

// check if user package subscribed
function jobsearch_allinpckg_order_is_expired($order_id = 0, $ptype = 'jobs')
{

    $order_post_id = $order_id;
    $expiry_timestamp = get_post_meta($order_post_id, 'package_expiry_timestamp', true);


    if ($expiry_timestamp <= strtotime(current_time('d-m-Y H:i:s'))) {
        return true;
    }

    if ($ptype == 'cvs') {
        $remaining_jobs = jobsearch_allinpckg_order_remaining_cvs($order_post_id);
    } else {
        $remaining_jobs = jobsearch_allinpckg_order_remaining_jobs($order_post_id);
    }

    if ($remaining_jobs < 1) {
        return true;
    }
    return false;
}

/*
 * All in on package functions
 * End here
 */

function jobsearch_all_locs_del_callback()
{
    global $wpdb;

    $wpdb->query($wpdb->prepare("DELETE $wpdb->terms FROM $wpdb->terms LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id) 
        WHERE $wpdb->term_taxonomy.taxonomy = %s", 'job-location'));

    $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->term_taxonomy WHERE $wpdb->term_taxonomy.taxonomy = %s", 'job-location'));
}

// get user package used jobs
function jobsearch_pckg_order_used_cvs($order_id = 0)
{
    $cvs_list_count = 0;
    if ($order_id > 0) {
        $total_cvs = get_post_meta($order_id, 'num_of_cvs', true);
        $cvs_list = get_post_meta($order_id, 'jobsearch_order_cvs_list', true);

        if (!empty($cvs_list)) {
            $cvs_list_count = count(explode(',', $cvs_list));
        }
    }

    return $cvs_list_count;
}

// get user package remaining cvs
function jobsearch_pckg_order_remaining_cvs($order_id = 0)
{
    $remaining_cvs = 0;
    if ($order_id > 0) {
        $total_cvs = get_post_meta($order_id, 'num_of_cvs', true);
        $used_cvs = jobsearch_pckg_order_used_cvs($order_id);

        $remaining_cvs = $total_cvs > $used_cvs ? $total_cvs - $used_cvs : 0;
    }

    return $remaining_cvs;
}

// check if user package subscribed
function jobsearch_cv_pckg_is_subscribed($pckg_id = 0, $user_id = 0)
{
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'package_type',
                'value' => 'cv',
                'compare' => '=',
            ),
            array(
                'key' => 'jobsearch_order_package',
                'value' => $pckg_id,
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;
    if (!empty($pkgs_query_posts)) {
        foreach ($pkgs_query_posts as $order_post_id) {
            $remaining_cvs = jobsearch_pckg_order_remaining_cvs($order_post_id);
            if ($remaining_cvs > 0) {
                return $order_post_id;
            }
        }
    }
    return false;
}

// check if user package subscribed
function jobsearch_employer_first_subscribed_cv_pkg($user_id = 0)
{
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'jobsearch_order_attach_with',
                'value' => 'package',
                'compare' => '=',
            ),
            array(
                'key' => 'package_type',
                'value' => 'cv',
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;
    if (!empty($pkgs_query_posts)) {
        foreach ($pkgs_query_posts as $order_post_id) {
            $remaining_cvs = jobsearch_pckg_order_remaining_cvs($order_post_id);
            if ($remaining_cvs > 0) {
                return $order_post_id;
            }
        }
    }
    return false;
}

// check if user cv package expired
function jobsearch_cv_pckg_order_is_expired($order_id = 0)
{

    $order_post_id = $order_id;
    $expiry_timestamp = get_post_meta($order_post_id, 'package_expiry_timestamp', true);


    if ($expiry_timestamp <= strtotime(current_time('d-m-Y H:i:s', 1))) {
        return true;
    }

    $remaining_cvs = jobsearch_pckg_order_remaining_cvs($order_post_id);
    if ($remaining_cvs < 1) {
        return true;
    }
    return false;
}

/*
 * Employer profile package functions
 * Start here
 */

// get used jobs
function jobsearch_emprofpckg_order_used_jobs($order_id = 0)
{
    $jobs_list_count = 0;
    if ($order_id > 0) {
        $jobs_list = get_post_meta($order_id, 'jobsearch_order_jobs_list', true);

        if (!empty($jobs_list)) {
            $jobs_list_count = count(explode(',', $jobs_list));
        }
    }

    return $jobs_list_count;
}

// get remaining jobs
function jobsearch_emprofpckg_order_remaining_jobs($order_id = 0)
{
    $remaining_jobs = 0;
    if ($order_id > 0) {
        $total_jobs = get_post_meta($order_id, 'emprof_num_jobs', true);
        $used_jobs = jobsearch_emprofpckg_order_used_jobs($order_id);

        $remaining_jobs = $total_jobs > $used_jobs ? $total_jobs - $used_jobs : 0;
    }

    return $remaining_jobs;
}

// get used feature jobs
function jobsearch_emprofpckg_order_used_fjobs($order_id = 0)
{
    $jobs_list_count = 0;
    if ($order_id > 0) {
        $jobs_list = get_post_meta($order_id, 'jobsearch_order_fjobs_list', true);

        if (!empty($jobs_list)) {
            $jobs_list_count = count(explode(',', $jobs_list));
        }
    }

    return $jobs_list_count;
}

// get remaining feature jobs
function jobsearch_emprofpckg_order_remaining_fjobs($order_id = 0)
{
    $remaining_jobs = 0;
    if ($order_id > 0) {
        $total_jobs = get_post_meta($order_id, 'emprof_num_fjobs', true);
        $used_jobs = jobsearch_emprofpckg_order_used_fjobs($order_id);

        $remaining_jobs = $total_jobs > $used_jobs ? $total_jobs - $used_jobs : 0;
    }

    return $remaining_jobs;
}

// get used cvs
function jobsearch_emprofpckg_order_used_cvs($order_id = 0)
{
    $cvs_list_count = 0;
    if ($order_id > 0) {
        $cvs_list = get_post_meta($order_id, 'jobsearch_order_cvs_list', true);

        if (!empty($cvs_list)) {
            $cvs_list_count = count(explode(',', $cvs_list));
        }
    }

    return $cvs_list_count;
}

// get remaining cvs
function jobsearch_emprofpckg_order_remaining_cvs($order_id = 0)
{
    $remaining_cvs = 0;
    if ($order_id > 0) {
        $total_cvs = get_post_meta($order_id, 'emprof_num_cvs', true);
        $used_cvs = jobsearch_emprofpckg_order_used_cvs($order_id);

        $remaining_cvs = $total_cvs > $used_cvs ? $total_cvs - $used_cvs : 0;
    }

    return $remaining_cvs;
}

// check if user package subscribed
function jobsearch_emprofpckg_is_subscribed($pckg_id = 0, $user_id = 0, $ptype = 'jobs')
{
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'package_type',
                'value' => 'employer_profile',
                'compare' => '=',
            ),
            array(
                'key' => 'jobsearch_order_package',
                'value' => $pckg_id,
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;
    if (!empty($pkgs_query_posts)) {
        foreach ($pkgs_query_posts as $order_post_id) {
            if ($ptype == 'cvs') {
                $remaining_jobs = jobsearch_emprofpckg_order_remaining_cvs($order_post_id);
            } else {
                $remaining_jobs = jobsearch_emprofpckg_order_remaining_jobs($order_post_id);
            }
            if ($remaining_jobs > 0) {
                return $order_post_id;
            }
        }
    }
    return false;
}

// check if user package subscribed
function jobsearch_emprof_first_pkg_subscribed($user_id = 0, $ptype = 'jobs')
{

    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'package_type',
                'value' => 'employer_profile',
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;
    if (!empty($pkgs_query_posts)) {
        foreach ($pkgs_query_posts as $order_post_id) {
            if ($ptype == 'cvs') {
                $remaining_jobs = jobsearch_emprofpckg_order_remaining_cvs($order_post_id);
            } else {
                $remaining_jobs = jobsearch_emprofpckg_order_remaining_jobs($order_post_id);
            }
            if ($remaining_jobs > 0) {
                return $order_post_id;
            }
        }
    }
    return false;
}

// check if user package subscribed
function jobsearch_emprofpckg_order_is_expired($order_id = 0, $ptype = 'jobs')
{

    $order_post_id = $order_id;
    $expiry_timestamp = get_post_meta($order_post_id, 'package_expiry_timestamp', true);


    if ($expiry_timestamp <= strtotime(current_time('d-m-Y H:i:s'))) {
        return true;
    }

    if ($ptype == 'cvs') {
        $remaining_jobs = jobsearch_emprofpckg_order_remaining_cvs($order_post_id);
    } else {
        $remaining_jobs = jobsearch_emprofpckg_order_remaining_jobs($order_post_id);
    }

    if ($remaining_jobs < 1) {
        return true;
    }
    return false;
}

//
function jobsearch_emp_profile_pckg_is_subscribed($pckg_id = 0, $user_id = 0)
{
    if ($user_id <= 0 && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'package_type',
                'value' => 'employer_profile',
                'compare' => '=',
            ),
            array(
                'key' => 'jobsearch_order_package',
                'value' => $pckg_id,
                'compare' => '=',
            ),
            array(
                'key' => 'package_expiry_timestamp',
                'value' => strtotime(current_time('d-m-Y H:i:s')),
                'compare' => '>',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);

    $pkgs_query_posts = $pkgs_query->posts;
    if (isset($pkgs_query_posts[0])) {
        return $pkgs_query_posts[0];
    }
    return false;
}

function jobsearch_emp_profile_pkg_is_expired($order_id = 0)
{

    $order_post_id = $order_id;
    $expiry_timestamp = get_post_meta($order_post_id, 'package_expiry_timestamp', true);


    if ($expiry_timestamp <= strtotime(current_time('d-m-Y H:i:s'))) {
        return true;
    }

    return false;
}

//

function jobsearch_load_employer_team_next_page()
{
    $total_pages = isset($_POST['total_pages']) ? $_POST['total_pages'] : 1;
    $cur_page = isset($_POST['cur_page']) ? $_POST['cur_page'] : 1;
    $employer_id = isset($_POST['employer_id']) ? $_POST['employer_id'] : 1;
    $class_pref = isset($_POST['class_pref']) && $_POST['class_pref'] != '' ? $_POST['class_pref'] : 'jobsearch';
    $team_style = isset($_POST['team_style']) ? $_POST['team_style'] : 'default';


    $per_page_results = 3;

    $start = ($cur_page) * ($per_page_results);
    $offset = $per_page_results;

    $exfield_list = get_post_meta($employer_id, 'jobsearch_field_team_title', true);
    $exfield_list_val = get_post_meta($employer_id, 'jobsearch_field_team_description', true);
    $team_designationfield_list = get_post_meta($employer_id, 'jobsearch_field_team_designation', true);
    $team_experiencefield_list = get_post_meta($employer_id, 'jobsearch_field_team_experience', true);
    $team_imagefield_list = get_post_meta($employer_id, 'jobsearch_field_team_image', true);
    $team_facebookfield_list = get_post_meta($employer_id, 'jobsearch_field_team_facebook', true);
    $team_googlefield_list = get_post_meta($employer_id, 'jobsearch_field_team_google', true);
    $team_twitterfield_list = get_post_meta($employer_id, 'jobsearch_field_team_twitter', true);
    $team_linkedinfield_list = get_post_meta($employer_id, 'jobsearch_field_team_linkedin', true);

    $exfield_list = array_slice($exfield_list, $start, $offset);
    $exfield_list_val = array_slice($exfield_list_val, $start, $offset);
    $team_designationfield_list = array_slice($team_designationfield_list, $start, $offset);
    $team_experiencefield_list = array_slice($team_experiencefield_list, $start, $offset);
    $team_imagefield_list = array_slice($team_imagefield_list, $start, $offset);
    $team_facebookfield_list = array_slice($team_facebookfield_list, $start, $offset);
    $team_googlefield_list = array_slice($team_googlefield_list, $start, $offset);
    $team_twitterfield_list = array_slice($team_twitterfield_list, $start, $offset);
    $team_linkedinfield_list = array_slice($team_linkedinfield_list, $start, $offset);

    ob_start();

    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
        $total_team = sizeof($exfield_list);

        $rand_num_ul = rand(1000000, 99999999);

        $exfield_counter = 0;
        foreach ($exfield_list as $exfield) {
            $rand_num = rand(1000000, 99999999);

            $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
            $team_designationfield_val = isset($team_designationfield_list[$exfield_counter]) ? $team_designationfield_list[$exfield_counter] : '';
            $team_experiencefield_val = isset($team_experiencefield_list[$exfield_counter]) ? $team_experiencefield_list[$exfield_counter] : '';
            $team_imagefield_val = isset($team_imagefield_list[$exfield_counter]) ? $team_imagefield_list[$exfield_counter] : '';
            $team_facebookfield_val = isset($team_facebookfield_list[$exfield_counter]) ? $team_facebookfield_list[$exfield_counter] : '';
            $team_googlefield_val = isset($team_googlefield_list[$exfield_counter]) ? $team_googlefield_list[$exfield_counter] : '';
            $team_twitterfield_val = isset($team_twitterfield_list[$exfield_counter]) ? $team_twitterfield_list[$exfield_counter] : '';
            $team_linkedinfield_val = isset($team_linkedinfield_list[$exfield_counter]) ? $team_linkedinfield_list[$exfield_counter] : '';
            ?>
            <li class="<?php echo($class_pref) ?>-column-4 new-entries" style="display: none;">
                <script>
                    jQuery('a[id^="fancybox_notes"]').fancybox({
                        'titlePosition': 'inside',
                        'transitionIn': 'elastic',
                        'transitionOut': 'elastic',
                        'width': 400,
                        'height': 250,
                        'padding': 40,
                        'autoSize': false
                    });
                </script>
                <figure>
                    <a id="fancybox_notes<?php echo($rand_num) ?>" href="#notes<?php echo($rand_num) ?>"
                       class="jobsearch-candidate-grid-thumb"><img src="<?php echo($team_imagefield_val) ?>" alt="">
                        <span class="jobsearch-candidate-grid-status"></span></a>
                    <figcaption>
                        <h2><a id="fancybox_notes_txt<?php echo($rand_num) ?>"
                               href="#notes<?php echo($rand_num) ?>"><?php echo($exfield) ?></a></h2>
                        <p><?php echo($team_designationfield_val) ?></p>
                        <?php
                        if ($team_experiencefield_val != '') {
                            echo '<span>' . sprintf(esc_html__('Experience: %s', 'wp-jobsearch'), $team_experiencefield_val) . '</span>';
                        }
                        ?>
                    </figcaption>
                </figure>

                <div id="notes<?php echo($rand_num) ?>" style="display: none;"><?php echo($exfield_val) ?></div>
                <?php
                if ($team_facebookfield_val != '' || $team_googlefield_val != '' || $team_twitterfield_val != '' || $team_linkedinfield_val != '') {
                    ?>
                    <ul class="jobsearch-social-icons">
                        <?php
                        if ($team_facebookfield_val != '') {
                            ?>
                            <li><a href="<?php echo($team_facebookfield_val) ?>" data-original-title="facebook"
                                   class="jobsearch-icon jobsearch-facebook-logo"></a></li>
                            <?php
                        }
                        if ($team_googlefield_val != '') {
                            ?>
                            <li><a href="<?php echo($team_googlefield_val) ?>" data-original-title="google-plus"
                                   class="jobsearch-icon jobsearch-google-plus-logo-button"></a></li>
                            <?php
                        }
                        if ($team_twitterfield_val != '') {
                            ?>
                            <li><a href="<?php echo($team_twitterfield_val) ?>" data-original-title="twitter"
                                   class="jobsearch-icon jobsearch-twitter-logo"></a></li>
                            <?php
                        }
                        if ($team_linkedinfield_val != '') {
                            ?>
                            <li><a href="<?php echo($team_linkedinfield_val) ?>" data-original-title="linkedin"
                                   class="jobsearch-icon jobsearch-linkedin-button"></a></li>
                            <?php
                        }
                        ?>
                    </ul>
                    <?php
                }
                ?>
            </li>
            <?php
            $exfield_counter++;
        }
    }

    $html = ob_get_clean();

    $html = apply_filters('careerfy_employer_team_members_view', $html, $_POST);

    echo json_encode(array('html' => $html));
    die;
}

add_action('wp_ajax_jobsearch_load_employer_team_next_page', 'jobsearch_load_employer_team_next_page');
add_action('wp_ajax_nopriv_jobsearch_load_employer_team_next_page', 'jobsearch_load_employer_team_next_page');

add_action('wp_ajax_jobsearch_send_email_to_applicant_by_employer', 'jobsearch_send_email_to_applicant_by_employer');

function jobsearch_send_email_to_applicant_by_employer()
{
    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $candidate_id = isset($_POST['_candidate_id']) ? $_POST['_candidate_id'] : '';
    $employer_id = isset($_POST['_employer_id']) ? $_POST['_employer_id'] : '';
    $email_subject = isset($_POST['email_subject']) ? $_POST['email_subject'] : '';
    $email_content = isset($_POST['email_content']) ? $_POST['email_content'] : '';

    $error = '0';
    if ($email_subject != '' && $error == 0) {
        $email_subject = ($email_subject);
    } else {
        $error = '1';
        $msg = esc_html__('Please Enter subject.', 'wp-jobsearch');
    }
    if ($email_content != '' && $error == 0) {
        $email_content = esc_html($email_content);
    } else {
        $error = '1';
        $msg = esc_html__('Please write your message.', 'wp-jobsearch');
    }

    if ($msg == '' && $error == '0') {

        $cuser_id = jobsearch_get_candidate_user_id($candidate_id);
        $cuser_obj = get_user_by('ID', $cuser_id);

        $cuser_email = isset($cuser_obj->user_email) ? $cuser_obj->user_email : '';

        $subject = $email_subject;

        if ($job_id == 0 && $employer_id > 0) {
            $job_emp = $employer_id;
        } else {
            $job_emp = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
        }
        $euser_id = jobsearch_get_employer_user_id($job_emp);
        $euser_obj = get_user_by('ID', $euser_id);
        $euser_email = isset($euser_obj->user_email) ? $euser_obj->user_email : '';

        add_filter('wp_mail_from', function () {
            $p_mail_from = get_bloginfo('admin_email');
            return $p_mail_from;
        });
        //
        $euser_name = isset($euser_obj->display_name) ? $euser_obj->display_name : '';
        $euser_name = apply_filters('jobsearch_user_display_name', $euser_name, $euser_obj);

        add_filter('wp_mail_from_name', function () {
            $p_mail_from = get_bloginfo('name');
            return $p_mail_from;
        });
        add_filter('wp_mail_content_type', function () {
            return 'text/html';
        });
        
        $headers = array('Reply-To: ' . $euser_name . ' <' . $euser_email . '>');
        
        $email_content = nl2br($email_content);
        $email_content = '<p>' . $email_content . '</p>';

        wp_mail($cuser_email, $subject, $email_content, $headers);
        $msg = esc_html__('Mail sent successfully', 'wp-jobsearch');
        $error = '0';
    }
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}

add_action('wp_ajax_jobsearch_send_email_to_multi_applicants_by_employer', 'jobsearch_send_email_to_multi_applicants_by_employer');

function jobsearch_send_email_to_multi_applicants_by_employer()
{
    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $_candidate_ids = isset($_POST['_candidate_ids']) ? $_POST['_candidate_ids'] : '';
    $employer_id = isset($_POST['_employer_id']) ? $_POST['_employer_id'] : '';
    $email_subject = isset($_POST['email_subject']) ? $_POST['email_subject'] : '';
    $email_content = isset($_POST['email_content']) ? $_POST['email_content'] : '';

    $_candidate_ids = $_candidate_ids != '' ? explode(',', $_candidate_ids) : '';

    $error = '0';
    if ($email_subject != '' && $error == 0) {
        $email_subject = esc_html($email_subject);
    } else {
        $error = '1';
        $msg = esc_html__('Please Enter your Name.', 'wp-jobsearch');
    }
    if ($email_content != '' && $error == 0) {
        $email_content = ($email_content);
        $email_content = nl2br($email_content);
        $email_content = '<p>' . $email_content . '</p>';
    } else {
        $error = '1';
        $msg = esc_html__('Please Enter your Name.', 'wp-jobsearch');
    }

    if ($msg == '' && $error == '0') {

        if (!empty($_candidate_ids)) {

            $subject = $email_subject;

            if ($job_id == 0 && $employer_id > 0) {
                $job_emp = $employer_id;
            } else {
                $job_emp = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
            }
            $euser_id = jobsearch_get_employer_user_id($job_emp);
            $euser_obj = get_user_by('ID', $euser_id);
            $euser_email = isset($euser_obj->user_email) ? $euser_obj->user_email : '';
            
            $euser_name = isset($euser_obj->display_name) ? $euser_obj->display_name : '';
            $euser_name = apply_filters('jobsearch_user_display_name', $euser_name, $euser_obj);
            
            add_filter('wp_mail_from', function () {
                $p_mail_from = get_bloginfo('admin_email');
                return $p_mail_from;
            });
            add_filter('wp_mail_from_name', function () {
                $p_mail_from = get_bloginfo('name');
                return $p_mail_from;
            });
            add_filter('wp_mail_content_type', function () {
                return 'text/html';
            });

            $headers = array('Reply-To: ' . $euser_name . ' <' . $euser_email . '>');

            foreach ($_candidate_ids as $candidate_id) {
                $cuser_id = jobsearch_get_candidate_user_id($candidate_id);
                $cuser_obj = get_user_by('ID', $cuser_id);

                $cuser_email = isset($cuser_obj->user_email) ? $cuser_obj->user_email : '';
                $rec_emails = $cuser_email;
                wp_mail($rec_emails, $subject, $email_content, $headers);
            }

            $msg = esc_html__('Mail sent successfully', 'wp-jobsearch');
            $error = '0';
        } else {
            $msg = esc_html__('Error! There is some problem.', 'wp-jobsearch');
            $error = '1';
        }
    }
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}

add_action('wp_ajax_jobsearch_send_email_to_multi_instamatchs_by_employer', 'jobsearch_send_email_to_multi_instamatchs_by_employer');

function jobsearch_send_email_to_multi_instamatchs_by_employer()
{
    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $_candidate_ids = isset($_POST['_candidate_ids']) ? $_POST['_candidate_ids'] : '';
    $employer_id = isset($_POST['_employer_id']) ? $_POST['_employer_id'] : '';
    $email_subject = isset($_POST['email_subject']) ? $_POST['email_subject'] : '';
    $email_content = isset($_POST['email_content']) ? $_POST['email_content'] : '';

    $_candidate_ids = $_candidate_ids != '' ? explode(',', $_candidate_ids) : '';

    $error = '0';
    if ($email_subject != '' && $error == 0) {
        $email_subject = esc_html($email_subject);
    } else {
        $error = '1';
        $msg = esc_html__('Please Enter your Name.', 'wp-jobsearch');
    }
    if ($email_content != '' && $error == 0) {
        //
    } else {
        $error = '1';
        $msg = esc_html__('Please Enter your Name.', 'wp-jobsearch');
    }

    if ($msg == '' && $error == '0') {

        if (!empty($_candidate_ids)) {

            foreach ($_candidate_ids as $candidate_id) {
                
                $cand_user_id = jobsearch_get_candidate_user_id($candidate_id);
                $cand_user = get_user_by('ID', $cand_user_id);
                do_action('jobsearch_instamatch_by_emp_email', $cand_user, $job_id, $email_subject, $email_content);
            }

            $msg = esc_html__('Mail sent successfully', 'wp-jobsearch');
            $error = '0';
        } else {
            $msg = esc_html__('Error! There is some problem.', 'wp-jobsearch');
            $error = '1';
        }
    }
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}

add_action('wp_ajax_jobsearch_applicant_to_undoreject_by_employer', 'jobsearch_applicant_to_undoreject_by_employer');

function jobsearch_applicant_to_undoreject_by_employer()
{

    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $candidate_id = isset($_POST['_candidate_id']) ? $_POST['_candidate_id'] : '';

    if ($job_id > 0 && $candidate_id > 0) {
        $job_reject_int_list = get_post_meta($job_id, '_job_reject_interview_list', true);

        $job_reject_int_list = $job_reject_int_list != '' ? explode(',', $job_reject_int_list) : array();
        if (empty($job_reject_int_list)) {
            $job_reject_int_list = array();
        }
        if (in_array($candidate_id, $job_reject_int_list) && ($key = array_search($candidate_id, $job_reject_int_list)) !== false) {
            unset($job_reject_int_list[$key]);
            $job_reject_int_list = implode(',', $job_reject_int_list);
            update_post_meta($job_id, '_job_reject_interview_list', $job_reject_int_list);

            //
            $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
            $job_applicants_list = $job_applicants_list != '' ? explode(',', $job_applicants_list) : array();
            if (empty($job_applicants_list)) {
                $job_applicants_list = array();
            }
            if (!in_array($candidate_id, $job_applicants_list)) {
                $job_applicants_list[] = $candidate_id;
                $job_applicants_list = implode(',', $job_applicants_list);
                update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_applicants_list);
            }
            //

            $msg = esc_html__('Undo Rejection', 'wp-jobsearch');
            $error = '0';
            echo json_encode(array('msg' => $msg, 'error' => $error));
            wp_die();
        }
    }
    $msg = '';
    $error = '1';
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}

function jobsearch_employer_det_active_job_html($job_id)
{
    global $jobsearch_plugin_options;

    $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
    $job_types_switch = isset($jobsearch_plugin_options['job_types_switch']) ? $jobsearch_plugin_options['job_types_switch'] : '';
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

    $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';

    $jobsearch_title_limit = isset($jobsearch_plugin_options['related_jobs_title_length']) && $jobsearch_plugin_options['related_jobs_title_length'] > 0 ? $jobsearch_plugin_options['related_jobs_title_length'] : '';
    $post_thumbnail_id = jobsearch_job_get_profile_image($job_id);
    $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, apply_filters('jobsearch_reltedemps_list_thmb_size', 'thumbnail'));
    $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';

    $company_name = jobsearch_job_get_company_name($job_id, '@ ');
    $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
    $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);

    $job_city_title = jobsearch_post_city_contry_txtstr($job_id, true, false, true);

    if ($job_city_title == '') {
        $job_city_title = get_post_meta($job_id, 'jobsearch_field_location_address', true);
    }

    $sector_str = jobsearch_job_get_all_sectors($job_id, '', '', '', '<li><i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i>', '</li>');

    $job_type_str = jobsearch_job_get_all_jobtypes($job_id, 'jobsearch-option-btn');
    ?>
    <li class="jobsearch-column-12">
        <div class="jobsearch-joblisting-classic-wrap">
            <figure>
                <a href="<?php echo get_permalink($job_id) ?>"><img src="<?php echo($post_thumbnail_src) ?>" alt=""></a>
            </figure>
            <div class="jobsearch-joblisting-text">
                <div class="jobsearch-list-option">
                    <h2 class="jobsearch-pst-title">
                        <a href="<?php echo get_permalink($job_id) ?>"><?php echo esc_html(wp_trim_words(get_the_title($job_id), $jobsearch_title_limit)); ?></a>
                        <?php
                        if ($jobsearch_job_featured == 'on') {
                            ?>
                            <span><?php echo esc_html__('Featured', 'wp-jobsearch'); ?></span>
                            <?php
                        }
                        ?>
                    </h2>
                    <ul>
                        <?php
                        if ($company_name != '') {
                            ?>
                            <li><?php echo force_balance_tags($company_name); ?></li>
                            <?php
                        }
                        if (!empty($job_city_title) && $all_location_allow == 'on') {
                            ?>
                            <li>
                                <i class="jobsearch-icon jobsearch-maps-and-flags"></i><?php echo esc_html($job_city_title); ?>
                            </li>
                            <?php
                        }

                        if (!empty($sector_str) && $sectors_enable_switch == 'on') {
                            echo apply_filters('jobsearch_joblisting_sector_str_html', $sector_str, $job_id, '<li><i class="jobsearch-icon jobsearch-calendar"></i>', '</li>');
                        }
                        ?>
                    </ul>
                </div>
                <div class="jobsearch-job-userlist">
                    <?php
                    if ($job_type_str != '' && $job_types_switch != 'off') {
                        echo force_balance_tags($job_type_str);
                    }
                    $book_mark_args = array(
                        'job_id' => $job_id,
                        'before_icon' => 'fa fa-heart-o',
                        'after_icon' => 'fa fa-heart',
                        'anchor_class' => 'jobsearch-job-like'
                    );
                    do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args);
                    ?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </li>
    <?php
}

add_action('wp_ajax_jobsearch_load_more_actemp_jobs_det', 'jobsearch_load_more_actemp_jobs_det');
add_action('wp_ajax_nopriv_jobsearch_load_more_actemp_jobs_det', 'jobsearch_load_more_actemp_jobs_det');

function jobsearch_load_more_actemp_jobs_det()
{
    $page_num = absint($_POST['page_num']);
    $employer_id = absint($_POST['emp_id']);
    $default_date_time_formate = 'd-m-Y H:i:s';

    ob_start();
    $args = array(
        'posts_per_page' => 5,
        'paged' => $page_num,
        'post_type' => 'job',
        'post_status' => 'publish',
        'order' => 'DESC',
        'orderby' => 'ID',
        'meta_query' => array(
            array(
                'key' => 'jobsearch_field_job_expiry_date',
                'value' => strtotime(current_time($default_date_time_formate, 1)),
                'compare' => '>=',
            ),
            array(
                'key' => 'jobsearch_field_job_status',
                'value' => 'approved',
                'compare' => '=',
            ),
            array(
                'key' => 'jobsearch_field_job_posted_by',
                'value' => $employer_id,
                'compare' => '=',
            ),
        ),
    );
    $args = apply_filters('jobsearch_employer_rel_jobs_query_args', $args);

    $jobs_query = new WP_Query($args);

    if ($jobs_query->have_posts()) {
        while ($jobs_query->have_posts()) : $jobs_query->the_post();
            $job_id = get_the_ID();
            jobsearch_employer_det_active_job_html($job_id);
        endwhile;
        wp_reset_postdata();
    }
    $html = ob_get_clean();
    echo json_encode(array('html' => $html));

    wp_die();
}

add_action('wp_ajax_jobsearch_delete_applicant_by_employer', 'jobsearch_delete_applicant_by_employer');

function jobsearch_delete_applicant_by_employer()
{

    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $candidate_id = isset($_POST['_candidate_id']) ? $_POST['_candidate_id'] : '';

    if ($job_id > 0 && $candidate_id > 0) {

        $user_id = jobsearch_get_candidate_user_id($candidate_id);

        $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
        $job_applicants_list = $job_applicants_list != '' ? explode(',', $job_applicants_list) : array();

        if (jobsearch_employer_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to delete this.', 'wp-jobsearch');
            $error = '1';
            echo json_encode(array('msg' => $msg, 'error' => $error));
            die;
        }

        if (!empty($job_applicants_list)) {

            //
            $job_short_int_list = get_post_meta($job_id, '_job_short_interview_list', true);
            $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : '';
            if (empty($job_short_int_list)) {
                $job_short_int_list = array();
            }
            if (($key = array_search($candidate_id, $job_short_int_list)) !== false) {
                unset($job_short_int_list[$key]);

                $job_short_int_list = implode(',', $job_short_int_list);
                update_post_meta($job_id, '_job_short_interview_list', $job_short_int_list);
            }

            $job_reject_int_list = get_post_meta($job_id, '_job_reject_interview_list', true);
            $job_reject_int_list = $job_reject_int_list != '' ? explode(',', $job_reject_int_list) : '';
            if (empty($job_reject_int_list)) {
                $job_reject_int_list = array();
            }
            if (($key = array_search($candidate_id, $job_reject_int_list)) !== false) {
                unset($job_reject_int_list[$key]);

                $job_reject_int_list = implode(',', $job_reject_int_list);
                update_post_meta($job_id, '_job_reject_interview_list', $job_reject_int_list);
            }
            //

            if (($key = array_search($candidate_id, $job_applicants_list)) !== false) {
                unset($job_applicants_list[$key]);

                $job_applicants_list = implode(',', $job_applicants_list);
                update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_applicants_list);
                jobsearch_remove_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', $user_id);
            }
        }

        $msg = esc_html__('Deleted', 'wp-jobsearch');
        $error = '0';
        echo json_encode(array('msg' => $msg, 'error' => $error));
        wp_die();
    }
    $msg = '';
    $error = '1';
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}

add_action('wp_ajax_jobsearch_applicant_to_interview_by_employer', 'jobsearch_applicant_to_interview_by_employer');

function jobsearch_applicant_to_interview_by_employer()
{

    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $candidate_id = isset($_POST['_candidate_id']) ? $_POST['_candidate_id'] : '';

    if ($job_id > 0 && $candidate_id > 0) {

        $current_user_id = get_current_user_id();
        $curuser_is_employer = jobsearch_user_is_employer($current_user_id);
        if ($curuser_is_employer) {
            $c_user = wp_get_current_user();
        } else {
            $job_employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
            $emp_user_id = jobsearch_get_employer_user_id($job_employer_id);
            $c_user = get_user_by('ID', $emp_user_id);
        }

        $job_short_int_list = get_post_meta($job_id, '_job_short_interview_list', true);
        if ($job_short_int_list != '') {
            $job_short_int_list = explode(',', $job_short_int_list);
            if (!in_array($candidate_id, $job_short_int_list)) {
                $job_short_int_list[] = $candidate_id;

                $job_short_int_list = implode(',', $job_short_int_list);
                update_post_meta($job_id, '_job_short_interview_list', $job_short_int_list);
                do_action('jobsearch_user_shortlist_for_interview', $c_user, $job_id, $candidate_id);
                $msg = esc_html__('Shortlisted', 'wp-jobsearch');
                $error = '0';
                echo json_encode(array('msg' => $msg, 'error' => $error));
                wp_die();
            }
        } else {
            $job_short_int_list = array($candidate_id);
            $job_short_int_list = implode(',', $job_short_int_list);
            update_post_meta($job_id, '_job_short_interview_list', $job_short_int_list);
            do_action('jobsearch_user_shortlist_for_interview', $c_user, $job_id, $candidate_id);
            $msg = esc_html__('Shortlisted', 'wp-jobsearch');
            $error = '0';
            echo json_encode(array('msg' => $msg, 'error' => $error));
            wp_die();
        }
    }
    $msg = '';
    $error = '1';
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}

add_action('wp_ajax_jobsearch_multi_apps_to_interview_by_employer', 'jobsearch_multi_apps_to_interview_by_employer');

function jobsearch_multi_apps_to_interview_by_employer()
{

    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $_candidate_ids = isset($_POST['_candidate_ids']) ? $_POST['_candidate_ids'] : '';

    $_candidate_ids = $_candidate_ids != '' ? explode(',', $_candidate_ids) : '';
    if (!empty($_candidate_ids) && $job_id > 0) {
        $current_user_id = get_current_user_id();
        $curuser_is_employer = jobsearch_user_is_employer($current_user_id);
        if ($curuser_is_employer) {
            $c_user = wp_get_current_user();
        } else {
            $job_employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
            $emp_user_id = jobsearch_get_employer_user_id($job_employer_id);
            $c_user = get_user_by('ID', $emp_user_id);
        }
        foreach ($_candidate_ids as $candidate_id) {
            $job_short_int_list = get_post_meta($job_id, '_job_short_interview_list', true);
            $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : array();
            if (!in_array($candidate_id, $job_short_int_list)) {
                $job_short_int_list[] = $candidate_id;

                $job_short_int_list = implode(',', $job_short_int_list);
                update_post_meta($job_id, '_job_short_interview_list', $job_short_int_list);
                do_action('jobsearch_user_shortlist_for_interview', $c_user, $job_id, $candidate_id);
            }
        }
        $msg = esc_html__('Shortlisting', 'wp-jobsearch');
        $error = '0';
        echo json_encode(array('msg' => $msg, 'error' => $error));
        wp_die();
    }
    $msg = '';
    $error = '1';
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}

add_action('wp_ajax_jobsearch_applicant_to_reject_by_employer', 'jobsearch_applicant_to_reject_by_employer');

function jobsearch_applicant_to_reject_by_employer()
{

    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $candidate_id = isset($_POST['_candidate_id']) ? $_POST['_candidate_id'] : '';

    $current_user_id = get_current_user_id();
    $curuser_is_employer = jobsearch_user_is_employer($current_user_id);
    if ($curuser_is_employer) {
        $c_user = wp_get_current_user();
    } else {
        $job_employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
        $emp_user_id = jobsearch_get_employer_user_id($job_employer_id);
        $c_user = get_user_by('ID', $emp_user_id);
    }
    if ($job_id > 0 && $candidate_id > 0) {
        $job_reject_int_list = get_post_meta($job_id, '_job_reject_interview_list', true);

        $job_reject_int_list = $job_reject_int_list != '' ? explode(',', $job_reject_int_list) : array();
        if (empty($job_reject_int_list)) {
            $job_reject_int_list = array();
        }
        if (!in_array($candidate_id, $job_reject_int_list)) {
            $job_reject_int_list[] = $candidate_id;

            $job_reject_int_list = implode(',', $job_reject_int_list);
            update_post_meta($job_id, '_job_reject_interview_list', $job_reject_int_list);

            //
            $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
            $job_applicants_list = $job_applicants_list != '' ? explode(',', $job_applicants_list) : array();
            if (($key = array_search($candidate_id, $job_applicants_list)) !== false) {
                unset($job_applicants_list[$key]);
                $job_applicants_list = implode(',', $job_applicants_list);
                update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_applicants_list);
            }
            //
            $job_short_int_list = get_post_meta($job_id, '_job_short_interview_list', true);
            $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : array();
            if (($key = array_search($candidate_id, $job_short_int_list)) !== false) {
                unset($job_short_int_list[$key]);
                $job_short_int_list = implode(',', $job_short_int_list);
                update_post_meta($job_id, '_job_short_interview_list', $job_short_int_list);
            }
            //
            do_action('jobsearch_user_rejected_for_interview', $c_user, $job_id, $candidate_id);

            $msg = esc_html__('Rejected', 'wp-jobsearch');
            $error = '0';
            echo json_encode(array('msg' => $msg, 'error' => $error));
            wp_die();
        }
    }
    $msg = '';
    $error = '1';
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}

add_action('wp_ajax_jobsearch_multi_apps_to_reject_by_employer', 'jobsearch_multi_apps_to_reject_by_employer');

function jobsearch_multi_apps_to_reject_by_employer()
{

    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';
    $_candidate_ids = isset($_POST['_candidate_ids']) ? $_POST['_candidate_ids'] : '';

    $_candidate_ids = $_candidate_ids != '' ? explode(',', $_candidate_ids) : '';

    $current_user_id = get_current_user_id();
    $curuser_is_employer = jobsearch_user_is_employer($current_user_id);
    if ($curuser_is_employer) {
        $c_user = wp_get_current_user();
    } else {
        $job_employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
        $emp_user_id = jobsearch_get_employer_user_id($job_employer_id);
        $c_user = get_user_by('ID', $emp_user_id);
    }

    //
    $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
    $job_applicants_list = $job_applicants_list != '' ? explode(',', $job_applicants_list) : array();
    //

    $job_short_int_list = get_post_meta($job_id, '_job_short_interview_list', true);
    $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : array();

    if (!empty($_candidate_ids) && $job_id > 0) {
        foreach ($_candidate_ids as $candidate_id) {
            $job_reject_int_list = get_post_meta($job_id, '_job_reject_interview_list', true);
            $job_reject_int_list = $job_reject_int_list != '' ? explode(',', $job_reject_int_list) : array();
            if (!in_array($candidate_id, $job_reject_int_list)) {
                $job_reject_int_list[] = $candidate_id;

                $job_reject_int_list = implode(',', $job_reject_int_list);
                update_post_meta($job_id, '_job_reject_interview_list', $job_reject_int_list);

                //
                if (($key = array_search($candidate_id, $job_applicants_list)) !== false) {
                    unset($job_applicants_list[$key]);
                    $job_applicants_list = implode(',', $job_applicants_list);
                    update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_applicants_list);
                }
                //
                //
                if (($key = array_search($candidate_id, $job_short_int_list)) !== false) {
                    unset($job_short_int_list[$key]);
                    $job_short_int_list = implode(',', $job_short_int_list);
                    update_post_meta($job_id, '_job_short_interview_list', $job_short_int_list);
                }
                //
                do_action('jobsearch_user_rejected_for_interview', $c_user, $job_id, $candidate_id);
            }
        }
        $msg = esc_html__('Rejecting', 'wp-jobsearch');
        $error = '0';
        echo json_encode(array('msg' => $msg, 'error' => $error));
        wp_die();
    }
    $msg = '';
    $error = '1';
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}

add_action('wp_ajax_jobsearch_job_filled_by_employer', 'jobsearch_job_filled_by_employer');

function jobsearch_job_filled_by_employer()
{
    $job_id = isset($_POST['_job_id']) ? $_POST['_job_id'] : '';

    if ($job_id > 0) {
        $user = wp_get_current_user();
        $user_id = $user->ID;
        
        $user_id = apply_filters('jobsearch_in_jobfill_fromdash_user_id', $user_id, $job_id);

        if (jobsearch_user_isemp_member($user_id)) {
            $employer_id = jobsearch_user_isemp_member($user_id);
        } else {
            $employer_id = jobsearch_get_user_employer_id($user_id);
        }

        $job_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

        if ($employer_id == $job_emp_id) {
            update_post_meta($job_id, 'jobsearch_field_job_filled', 'on');
            $msg = esc_html__('(Filled)', 'wp-jobsearch');
            $error = '0';
            echo json_encode(array('msg' => $msg, 'error' => $error));
            wp_die();
        }
    }
    $msg = '';
    $error = '1';
    echo json_encode(array('msg' => $msg, 'error' => $error));
    wp_die();
}

function jobsearch_employer_info_div_visible($tpye = 'email')
{
    global $jobsearch_plugin_options;

    $show_info_flag = true;
    $show_info_for = isset($jobsearch_plugin_options['emp-sensinfo-email']) ? $jobsearch_plugin_options['emp-sensinfo-email'] : '';
    if ($tpye == 'phone') {
        $show_info_for = isset($jobsearch_plugin_options['emp-sensinfo-phone']) ? $jobsearch_plugin_options['emp-sensinfo-phone'] : '';
    } else if ($tpye == 'weburl') {
        $show_info_for = isset($jobsearch_plugin_options['emp-sensinfo-weburl']) ? $jobsearch_plugin_options['emp-sensinfo-weburl'] : '';
    }

    if ($show_info_for == 'public') {
        $show_info_flag = true;
    } else if ($show_info_for == 'for_login') {
        if (is_user_logged_in()) {
            $show_info_flag = true;
        } else {
            $show_info_flag = false;
        }
    } else if ($show_info_for == 'emp_cand') {
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $user_is_candidate = jobsearch_user_is_candidate($user_id);
            $user_is_employer = jobsearch_user_is_employer($user_id);
            if ($user_is_candidate || $user_is_employer) {
                $show_info_flag = true;
            } else {
                $show_info_flag = false;
            }
        } else {
            $show_info_flag = false;
        }
    } else if ($show_info_for == 'emp_only') {
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $user_is_employer = jobsearch_user_is_employer($user_id);
            if ($user_is_employer) {
                $show_info_flag = true;
            } else {
                $show_info_flag = false;
            }
        } else {
            $show_info_flag = false;
        }
    } else if ($show_info_for == 'cand_only') {
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $user_is_candidate = jobsearch_user_is_candidate($user_id);
            if ($user_is_candidate) {
                $show_info_flag = true;
            } else {
                $show_info_flag = false;
            }
        } else {
            $show_info_flag = false;
        }
    }

    if ($show_info_for == 'admin_only') {
        $show_info_flag = false;
    }

    if (is_user_logged_in()) {
        $cur_user_obj = wp_get_current_user();
        if (in_array('administrator', (array)$cur_user_obj->roles)) {
            $show_info_flag = true;
        }
    }

    return $show_info_flag;
}

if (!function_exists('jobsearch_employer_info_encoding_callback')) {

    function jobsearch_employer_info_encoding_callback($emp_data = '', $tpye = 'email')
    {

        $show_info_flag = jobsearch_employer_info_div_visible($tpye);

        if ($show_info_flag == true) {
            return $emp_data;
        } else {
            return 'xxx-xxx-xxx';
        }
    }

    add_filter('jobsearch_employer_info_encoding', 'jobsearch_employer_info_encoding_callback', 10, 2);
}

add_action('wp_ajax_jobsearch_remove_emp_resmue_shlist_from_list', 'jobsearch_remove_empdash_resmue_shlist_from_list');

function jobsearch_remove_empdash_resmue_shlist_from_list()
{
    $candidate_id = isset($_POST['_cand_id']) ? $_POST['_cand_id'] : '';

    $user_id = get_current_user_id();
    $user_obj = get_user_by('ID', $user_id);

    $is_emp_member = false;
    if (jobsearch_user_isemp_member($user_id)) {
        $is_emp_member = true;
        $employer_id = jobsearch_user_isemp_member($user_id);
    } else {
        $employer_id = jobsearch_get_user_employer_id($user_id);
    }

    $employer_resumes_list = get_post_meta($employer_id, 'jobsearch_candidates_list', true);
    if ($employer_resumes_list != '') {
        $employer_resumes_list = explode(',', $employer_resumes_list);

        if (!empty($employer_resumes_list) && in_array($candidate_id, $employer_resumes_list)) {
            $capp_key = array_search($candidate_id, $employer_resumes_list);
            unset($employer_resumes_list[$capp_key]);
            $employer_resumes_list = implode(',', $employer_resumes_list);
            update_post_meta($employer_id, 'jobsearch_candidates_list', $employer_resumes_list);
        }
    }
    if ($is_emp_member) {
        $usermemb_resumes_list = get_user_meta($user_id, 'jobsearch_candidates_list', true);
        if (!empty($usermemb_resumes_list) && in_array($candidate_id, $usermemb_resumes_list)) {
            $capp_key = array_search($candidate_id, $usermemb_resumes_list);
            unset($usermemb_resumes_list[$capp_key]);
            update_user_meta($user_id, 'jobsearch_candidates_list', $usermemb_resumes_list);
        }
    }
    echo json_encode(array('msg' => 'done'));
    die;
}

add_action('jobsearch_emp_listin_sh_after_jobs_found', 'jobsearch_emp_listin_totalemps_found_html', 10, 3);
add_filter('jobsearch_emp_listin_top_jobfounds_html', 'jobsearch_emp_listin_top_jobfounds_html', 12, 4);
add_filter('jobsearch_emp_listin_before_top_jobfounds_html', 'jobsearch_emp_listin_before_top_jobfounds_html', 12, 4);
add_filter('jobsearch_emp_listin_after_sort_orders_html', 'jobsearch_emp_listin_after_sort_orders_html', 12, 4);

function jobsearch_emp_listin_totalemps_found_html($job_totnum, $employer_short_counter, $atts)
{

    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }
    if ($counts_on) {
        $per_page = isset($atts['employer_per_page']) && absint($atts['employer_per_page']) > 0 ? $atts['employer_per_page'] : 0;
        if (isset($_REQUEST['per-page']) && $_REQUEST['per-page'] > 1) {
            $per_page = $_REQUEST['per-page'];
        }
        if ($per_page > 1) {
            $page_num = isset($_REQUEST['employer_page']) && $_REQUEST['employer_page'] > 1 ? $_REQUEST['employer_page'] : 1;
            $start_frm = $page_num > 1 ? (($page_num - 1) * $per_page) : 1;
            $offset = $page_num > 1 ? ($page_num * $per_page) : $per_page;

            $offset = $offset > $job_totnum ? $job_totnum : $offset;

            $strt_toend_disp = absint($job_totnum) > 0 ? ($start_frm > 1 ? ($start_frm + 1) : $start_frm) . ' - ' . $offset : '0';
            ?>
            <div class="displayed-here"><?php printf(esc_html__('Displayed Here: %s Employers', 'wp-jobsearch'), $strt_toend_disp) ?></div>
            <?php
        } else {
            $per_page = isset($atts['employer_per_page']) && absint($atts['employer_per_page']) > 0 ? $atts['employer_per_page'] : $job_totnum;
            $per_page = $per_page > $job_totnum ? $job_totnum : $per_page;

            $strt_toend_disp = absint($job_totnum) > 0 ? '1 - ' . $per_page : '0';
            ?>
            <div class="displayed-here"><?php printf(esc_html__('Displayed Here: %s Employers', 'wp-jobsearch'), $strt_toend_disp) ?></div>
            <?php
        }
    }
}

function jobsearch_emp_listin_top_jobfounds_html($html, $job_totnum, $employer_short_counter, $atts)
{
    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }
    if ($counts_on) {
        $html = '';
    }
    return $html;
}

function jobsearch_emp_listin_before_top_jobfounds_html($html, $job_totnum, $employer_short_counter, $atts)
{
    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }
    if ($counts_on) {
        ob_start();
        ?>
        <div class="jobsearch-filterable jobsearch-filter-sortable jobsearch-topfound-title">
            <h2 class="jobsearch-fltcount-title">
                <?php
                echo absint($job_totnum) . '&nbsp;';
                if ($job_totnum == 1) {
                    echo esc_html__('Employer Found', 'wp-jobsearch');
                } else {
                    echo esc_html__('Employers Found', 'wp-jobsearch');
                }
                do_action('jobsearch_emp_listin_sh_after_jobs_found', $job_totnum, $employer_short_counter, $atts);
                ?>
            </h2>
        </div>
        <?php
        echo '<div class="jobsearch-topsort-holder">';
        $html = ob_get_clean();
    }
    return $html;
}

function jobsearch_emp_listin_after_sort_orders_html($html, $job_totnum, $employer_short_counter, $atts)
{
    $counts_on = true;
    if (isset($atts['display_per_page']) && $atts['display_per_page'] == 'no') {
        $counts_on = false;
    }
    if ($counts_on) {
        $html = '</div>';
    }
    return $html;
}

add_action('admin_footer', 'jobsearch_remove_empmnger_role_onadd');

function jobsearch_remove_empmnger_role_onadd() {
    global $pagenow;
    if ($pagenow == 'user-new.php') {
        ?>
        <script>jQuery('select#role').find('option[value="jobsearch_empmnger"]').remove();</script>
        <?php
    }
}

add_action('wp_ajax_jobsearch_employer_ading_member_account', 'jobsearch_addingemp_accmemb_ajax_callback');

function jobsearch_addingemp_accmemb_ajax_callback()
{
    $first_name = isset($_POST['u_firstname']) ? $_POST['u_firstname'] : '';
    $last_name = isset($_POST['u_lastname']) ? $_POST['u_lastname'] : '';
    $user_name = isset($_POST['u_username']) ? $_POST['u_username'] : '';
    $user_email = isset($_POST['u_emailadres']) ? $_POST['u_emailadres'] : '';
    $u_pass = isset($_POST['u_password']) ? $_POST['u_password'] : '';
    $conf_pass = isset($_POST['u_confpass']) ? $_POST['u_confpass'] : '';
    $mem_perms = isset($_POST['u_memb_perms']) ? $_POST['u_memb_perms'] : '';

    //
    if (isset($_POST['cus_employer_id'])) {
        $emp_user_id = $_POST['cus_employer_id'];
    } else {
        $emp_user_id = get_current_user_id();
    }
    $employer_id = jobsearch_get_user_employer_id($emp_user_id);
    if ($employer_id > 0) {
        //
        $error = 0;


        if ($user_email != '' && $error == 0 && filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            $user_email = esc_html($user_email);
        } else {
            $error = 1;
            $msg = esc_html__('Please Enter a valid email.', 'wp-jobsearch');
        }

        if ($u_pass != '' && $error == 0) {
            $u_pass = esc_html($u_pass);
        } else {
            $error = 1;
            $msg = esc_html__('Please enter a value for the password field.', 'wp-jobsearch');
        }

        if ($conf_pass != '' && $error == 0) {
            $conf_pass = esc_html($conf_pass);
        } else {
            $error = 1;
            $msg = esc_html__('Please enter a value for the confirm password field.', 'wp-jobsearch');
        }

        if ($u_pass != $conf_pass && $error == 0) {
            $error = 1;
            $msg = esc_html__('Confirm password does not match.', 'wp-jobsearch');
        }

        if ($error == 1) {
            echo json_encode(array('error' => '1', 'msg' => $msg));
            die;
        }

        $user_login = $user_name;
        if ($user_login == '') {
            $email_parts = explode("@", $user_email);
            $user_login = isset($email_parts[0]) ? $email_parts[0] : '';
            if ($user_login != '' && username_exists($user_login)) {
                $user_login .= '_' . rand(10000, 99999);
            }
        }
        if ($user_login == '') {
            $user_login = 'user_' . rand(10000, 99999);
            $user_email = 'user_' . rand(10000, 99999) . '@example.com';
        }

        $user_pass = $u_pass;

        $create_user = wp_create_user($user_login, $user_pass, $user_email);

        if (is_wp_error($create_user)) {

            $registration_error_messages = $create_user->errors;

            $display_errors = '';
            foreach ($registration_error_messages as $error) {
                $display_errors .= $error[0];
            }

            echo json_encode(array('error' => '1', 'msg' => $display_errors));
            die;
        } else {
            $user_id = $create_user;
            wp_update_user(array('ID' => $user_id, 'role' => 'jobsearch_empmnger'));
            if ($first_name != '') {
                $disply_name = $last_name != '' ? $first_name . ' ' . $last_name : $first_name;
                $user_def_array = array(
                    'ID' => $user_id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'display_name' => $disply_name,
                );
                wp_update_user($user_def_array);
            }
            $candidate_id = jobsearch_get_user_candidate_id($user_id);
            if ($candidate_id > 0) {
                wp_delete_post($candidate_id, true);
                delete_user_meta($user_id, 'jobsearch_candidate_id');
            }

            do_action('jobsearch_empmembr_savin_after_usrcreatup', $user_id, $employer_id);

            $emp_members = get_post_meta($employer_id, 'emp_acount_member_acounts', true);
            $emp_members = !empty($emp_members) ? $emp_members : array();

            $emp_members[] = $user_id;
            update_post_meta($employer_id, 'emp_acount_member_acounts', $emp_members);

            //
            update_user_meta($user_id, 'attached_profile_empid', $employer_id);
            update_user_meta($user_id, 'attached_profile_empuid', $emp_user_id);
            update_user_meta($user_id, 'jobsearch_attchprof_perms', $mem_perms);

            echo json_encode(array('error' => '0', 'msg' => esc_html__('This member has been added to your profile successfully.', 'wp-jobsearch')));
            die;
        }
    } else {
        echo json_encode(array('error' => '1', 'msg' => esc_html__('You are not allowed to add an account member.', 'wp-jobsearch')));
        die;
    }
}

add_action('wp_ajax_jobsearch_employer_update_member_account', 'jobsearch_updtingemp_accmemb_ajax_callback');

function jobsearch_updtingemp_accmemb_ajax_callback()
{
    $first_name = isset($_POST['u_firstname']) ? $_POST['u_firstname'] : '';
    $last_name = isset($_POST['u_lastname']) ? $_POST['u_lastname'] : '';
    $member_uid = isset($_POST['member_uid']) && $_POST['member_uid'] > 0 ? $_POST['member_uid'] : 0;
    $mem_perms = isset($_POST['u_memb_perms']) ? $_POST['u_memb_perms'] : '';

    //
    if (isset($_POST['cus_employer_id'])) {
        $emp_user_id = $_POST['cus_employer_id'];
    } else {
        $emp_user_id = get_current_user_id();
    }
    $employer_id = jobsearch_get_user_employer_id($emp_user_id);

    $emp_accmembers = get_post_meta($employer_id, 'emp_acount_member_acounts', true);
    $emp_accmembers = !empty($emp_accmembers) ? $emp_accmembers : array();
    $memb_uid_key = array_search($member_uid, $emp_accmembers);
    if ($employer_id > 0 && isset($emp_accmembers[$memb_uid_key]) && $emp_accmembers[$memb_uid_key] == $member_uid) {
        //
        $error = 0;

        $user_id = $member_uid;
        $get_acuser_obj = get_user_by('ID', $user_id);

        if (isset($get_acuser_obj->ID)) {
            $disply_name = $get_acuser_obj->disply_name;
            if ($first_name != '') {
                $disply_name = $last_name != '' ? $first_name . ' ' . $last_name : $first_name;
            }
            $user_def_array = array(
                'ID' => $user_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'display_name' => $disply_name,
            );
            wp_update_user($user_def_array);

            do_action('jobsearch_empmembr_savin_after_usrcreatup', $user_id, $employer_id);

            //
            update_user_meta($user_id, 'jobsearch_attchprof_perms', $mem_perms);

            echo json_encode(array('error' => '0', 'msg' => esc_html__('Member info updated successfully.', 'wp-jobsearch')));
            die;
        } else {
            echo json_encode(array('error' => '1', 'msg' => esc_html__('You are not allowed to update this account member.', 'wp-jobsearch')));
            die;
        }
    } else {
        echo json_encode(array('error' => '1', 'msg' => esc_html__('You are not allowed to update this account member.', 'wp-jobsearch')));
        die;
    }
}

add_action('wp_ajax_jobsearch_employer_remove_member_account', 'jobsearch_rmovinemp_accmemb_ajax_callback');

function jobsearch_rmovinemp_accmemb_ajax_callback()
{
    $member_uid = isset($_POST['member_uid']) && $_POST['member_uid'] > 0 ? $_POST['member_uid'] : 0;

    //
    if (isset($_POST['cus_employer_id']) && $_POST['cus_employer_id'] > 0) {
        $emp_user_id = $_POST['cus_employer_id'];
    } else {
        $emp_user_id = get_current_user_id();
    }
    $employer_id = jobsearch_get_user_employer_id($emp_user_id);

    $emp_accmembers = get_post_meta($employer_id, 'emp_acount_member_acounts', true);
    $emp_accmembers = !empty($emp_accmembers) ? $emp_accmembers : array();
    
    $memb_uid_key = array_search($member_uid, $emp_accmembers);
    
    if ($employer_id > 0 && isset($emp_accmembers[$memb_uid_key]) && $emp_accmembers[$memb_uid_key] == $member_uid) {
        //
        $error = 0;

        $user_id = $member_uid;
        $get_acuser_obj = get_user_by('ID', $user_id);

        if (isset($get_acuser_obj->ID)) {

            //wp_delete_user($user_id);

            //
            unset($emp_accmembers[$memb_uid_key]);
            update_post_meta($employer_id, 'emp_acount_member_acounts', $emp_accmembers);

            echo json_encode(array('error' => '0', 'msg' => esc_html__('Member removed successfully.', 'wp-jobsearch')));
            die;
        } else {
            echo json_encode(array('error' => '1', 'msg' => esc_html__('You are not allowed to remove this account member.', 'wp-jobsearch')));
            die;
        }
    } else {
        echo json_encode(array('error' => '1', 'msg' => esc_html__('You are not allowed to remove this account member.', 'wp-jobsearch')));
        die;
    }
}

function jobsearch_user_isemp_member($user_id) {

    $par_empuser_id = get_user_meta($user_id, 'attached_profile_empuid', true);

    $par_emp_id = jobsearch_get_user_employer_id($par_empuser_id);

    if ($par_emp_id > 0) {
        return $par_emp_id;
    }
}

function jobsearch_user_parmember_uid($user_id)
{

    $par_empuser_id = get_user_meta($user_id, 'attached_profile_empuid', true);

    if ($par_empuser_id > 0) {
        return $par_empuser_id;
    }
}

function jobsearch_emp_accmember_perms($user_id)
{

    $user_perms = get_user_meta($user_id, 'jobsearch_attchprof_perms', true);

    return $user_perms;
}

if (!function_exists('jobsearch_empmeta_serchuser_throgh_popup')) {

    function jobsearch_empmeta_serchuser_throgh_popup()
    {

        global $wpdb;
        $keyword = ($_POST['keyword']);

        $attusers_query = "SELECT users.ID,users.display_name FROM $wpdb->users AS users";
        if ($keyword != '') {
            $keyword = sanitize_text_field($keyword);
            $attusers_query .= " WHERE users.display_name LIKE %s";
        }

        $attusers_query .= " ORDER BY ID DESC LIMIT %d";

        if ($keyword != '') {
            $attall_users = $wpdb->get_results($wpdb->prepare($attusers_query, "%{$keyword}%", 10), 'ARRAY_A');
        } else {
            $attall_users = $wpdb->get_results($wpdb->prepare($attusers_query, 10), 'ARRAY_A');
        }

        $countusrs_query = "SELECT COUNT(*) FROM $wpdb->users AS users";
        if ($keyword != '') {
            $keyword = sanitize_text_field($keyword);
            $countusrs_query .= " WHERE users.display_name LIKE '%{$keyword}%'";
        }
        $totl_users = $wpdb->get_var($countusrs_query);

        $total_pages = 1;
        if ($totl_users > 10) {
            $total_pages = ceil($totl_users / 10);
        }

        ob_start();
        ?>
        <a href="javascript:void(0);" class="lodmore-users-btn" data-tpages="<?php echo($total_pages) ?>"
           data-keyword="<?php echo($keyword) ?>" data-gtopage="2"><?php esc_html_e('Load More', 'wp-jobsearch') ?></a>
        <?php
        $lodrhtml = ob_get_clean();

        ob_start();
        if (!empty($attall_users)) {
            foreach ($attall_users as $attch_usritm) {
                $to_att_userid = $attch_usritm['ID'];
                $toatch_user_obj = get_user_by('ID', $to_att_userid);
                if (!in_array('administrator', (array)$toatch_user_obj->roles)) {
                    ?>
                    <li><a href="javascript:void(0);" class="atchuser-itm-btn"
                           data-id="<?php echo($attch_usritm['ID']) ?>"><?php echo($attch_usritm['display_name']) ?></a>
                        <span></span>
                    </li>
                    <?php
                }
            }
        } else {
            ?>
            <li><?php esc_html_e('No User Found.', 'wp-jobsearch') ?></li>
            <?php
        }

        $html = ob_get_clean();

        echo json_encode(array('html' => $html, 'count' => $totl_users, 'lodrhtml' => $lodrhtml));

        wp_die();
    }

    add_action('wp_ajax_jobsearch_empmeta_serchuser_throgh_popup', 'jobsearch_empmeta_serchuser_throgh_popup');
}

if (!function_exists('jobsearch_load_musers_empmeta_popupinlist')) {

    function jobsearch_load_musers_empmeta_popupinlist()
    {

        global $wpdb;
        $page_num = absint($_POST['page_num']);
        if ($page_num > 1) {
            $offset = ($page_num - 1) * 10;
        } else {
            $offset = 10;
        }
        $keyword = ($_POST['keyword']);

        $attusers_query = "SELECT users.ID,users.display_name FROM $wpdb->users AS users";
        if ($keyword != '') {
            $keyword = sanitize_text_field($keyword);
            $attusers_query .= " WHERE users.display_name LIKE %s";
        }

        $attusers_query .= " ORDER BY ID DESC LIMIT %d OFFSET %d";

        if ($keyword != '') {
            $attall_users = $wpdb->get_results($wpdb->prepare($attusers_query, "%{$keyword}%", 10, $offset), 'ARRAY_A');
        } else {
            $attall_users = $wpdb->get_results($wpdb->prepare($attusers_query, 10, $offset), 'ARRAY_A');
        }

        ob_start();
        if (!empty($attall_users)) {
            foreach ($attall_users as $attch_usritm) {
                $to_att_userid = $attch_usritm['ID'];
                $toatch_user_obj = get_user_by('ID', $to_att_userid);
                if (!in_array('administrator', (array)$toatch_user_obj->roles)) {
                    ?>
                    <li><a href="javascript:void(0);" class="atchuser-itm-btn"
                           data-id="<?php echo($attch_usritm['ID']) ?>"><?php echo($attch_usritm['display_name']) ?></a>
                        <span></span></li>
                    <?php
                }
            }
        }

        $html = ob_get_clean();

        echo json_encode(array('html' => $html));

        wp_die();
    }

    add_action('wp_ajax_jobsearch_load_musers_empmeta_popupinlist', 'jobsearch_load_musers_empmeta_popupinlist');
}

if (!function_exists('jobsearch_empmeta_atchuser_throgh_popup')) {

    function jobsearch_empmeta_atchuser_throgh_popup() {

        $user_id = absint($_POST['id']);
        $emp_id = absint($_POST['p_id']);
        $user_obj = get_user_by('ID', $user_id);

        $username = esc_html__('N/L', 'wp-jobsearch');
        $useremail = esc_html__('N/L', 'wp-jobsearch');
        $user_phone = esc_html__('N/L', 'wp-jobsearch');
        if (is_object($user_obj)) {
            $username = $user_obj->user_login;
            $useremail = $user_obj->user_email;
            $user_phone = get_post_meta($emp_id, 'jobsearch_field_user_phone', true);
            $user_phone = $user_phone != '' ? $user_phone : esc_html__('N/L', 'wp-jobsearch');
        }
        echo json_encode(array('id' => $user_id, 'username' => $username, 'email' => $useremail, 'phone' => $user_phone));

        wp_die();
    }

    add_action('wp_ajax_jobsearch_empmeta_atchuser_throgh_popup', 'jobsearch_empmeta_atchuser_throgh_popup');
}

if (!function_exists('jobsearch_load_all_users_list_opts')) {

    function jobsearch_load_all_users_list_opts()
    {
        global $wpdb;

        $users_query = "SELECT users.ID,users.display_name FROM $wpdb->users AS users";

        $all_users = $wpdb->get_results($wpdb->prepare($users_query, 'users'), 'ARRAY_A');

        $html = "";
        if (!empty($all_users)) {
            foreach ($all_users as $user_item) {
                $user_id = $user_item['ID'];
                $selected = $user_id == $force_std ? ' selected="selected"' : '';
                $post_title = $user_item['display_name'];
                $html .= "<option{$selected} value=\"{$user_id}\">{$post_title}</option>" . "\n";
            }
        }
        echo json_encode(array('html' => $html));

        wp_die();
    }

    add_action('wp_ajax_jobsearch_load_all_users_list_opts', 'jobsearch_load_all_users_list_opts');
    add_action('wp_ajax_nopriv_jobsearch_load_all_users_list_opts', 'jobsearch_load_all_users_list_opts');
}

if (!function_exists('jobsearch_get_all_users_list_opts')) {

    function jobsearch_get_all_users_list_opts($selected_id, $field_label, $field_name, $custom_name = '')
    {
        global $jobsearch_form_fields;
        $custom_post_first_element = esc_html__('Please select ', 'wp-jobsearch');
        $custom_posts = array(
            '' => $custom_post_first_element . $field_label,
        );
        if ($selected_id) {
            $user_obj = get_user_by('ID', $selected_id);
            if (is_object($user_obj) && isset($user_obj->user_email)) {
                $custom_posts[$selected_id] = $user_obj->display_name;
            }
        }

        $rand_num = rand(1234568, 6867867);
        $field_params = array(
            'classes' => 'load_users_field',
            'id' => 'load_users_field_' . $rand_num,
            'name' => $field_name,
            'options' => $custom_posts,
            'force_std' => $selected_id,
            'ext_attr' => ' data-randid="' . $rand_num . '" data-forcestd="' . $selected_id . '" data-loaded="false"',
        );
        if (isset($custom_name) && $custom_name != '') {
            $field_params['cus_name'] = $custom_name;
        }
        $jobsearch_form_fields->select_field($field_params);
        ?>
        <span class="jobsearch-field-loader load_users_loader_<?php echo absint($rand_num); ?>"></span>
        <?php
    }

}

add_action('jobsearch_employer_followin_btn', 'jobsearch_employer_following_btn_html');

function jobsearch_employer_following_btn_html($args = array())
{
    global $jobsearch_plugin_options;

    $emp_followin_btn = isset($jobsearch_plugin_options['emp_followin_btn']) ? $jobsearch_plugin_options['emp_followin_btn'] : '';

    if ($emp_followin_btn == 'on') {

        $employer_id = isset($args['employer_id']) ? $args['employer_id'] : '';
        $before_label = isset($args['before_label']) ? $args['before_label'] : '';
        $after_label = isset($args['after_label']) ? $args['after_label'] : '';
        $ext_class = isset($args['ext_class']) ? $args['ext_class'] : '';
        $view = isset($args['view']) ? $args['view'] : '';

        $extra_class = !empty($ext_class) ? $ext_class : 'jobsearch-employer-followin-btn';
        $btn_label_text = $before_label;
        $btn_class = 'employer-followin-btnaction';
        if (!is_user_logged_in()) {
            $this_wredirct_url = jobsearch_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $btn_class = 'jobsearch-open-signin-tab jobsearch-wredirct-url';
        } else {
            $user_id = get_current_user_id();
            if (!jobsearch_user_is_candidate()) {
                $btn_class = 'jobsearch-othercand-role-btn';
                add_action('wp_footer', function () {
                    ?>
                    <div class="jobsearch-modal jobsearch-candother-rolepop fade" id="JobSearchModalOtherCandRolePop">
                        <div class="modal-inner-area">&nbsp;</div>
                        <div class="modal-content-area">
                            <div class="modal-box-area">
                                <span class="modal-close"><i class="fa fa-times"></i></span>
                                <p><?php esc_html_e('Only a candidate can perform this action.', 'wp-jobsearch') ?></p>
                            </div>
                        </div>
                    </div>
                    <script>
                        jQuery(document).on('click', '.jobsearch-othercand-role-btn', function () {
                            jobsearch_modal_popup_open('JobSearchModalOtherCandRolePop');
                        });
                    </script>
                    <?php
                }, 11, 1);
            } else {
                $candidate_id = jobsearch_get_user_candidate_id($user_id);
                $cand_followin_list = get_post_meta($candidate_id, 'jobsearch_cand_followins_list', true);
                $cand_followin_list = $cand_followin_list != '' ? explode(',', $cand_followin_list) : array();
                if (in_array($employer_id, $cand_followin_list)) {
                    $btn_label_text = $after_label;
                    $btn_class = 'employer-followed-already';
                }
            }
        }
        ?>
        <a href="javascript:void(0);" class="<?php echo $extra_class; ?> <?php echo($btn_class) ?>"
           data-id="<?php echo($employer_id) ?>" data-beforelbl="<?php echo($before_label) ?>"
           data-afterlbl="<?php echo($after_label) ?>" <?php echo(!is_user_logged_in() ? 'data-wredircto="' . $this_wredirct_url . '"' : '') ?>><i
                    class="fa fa-user-plus"></i> <?php echo esc_html($btn_label_text); ?></a>
        <?php
    }
}

add_action('wp_ajax_jobsearch_add_employer_followin_to_list', 'jobsearch_employer_following_action_callback');

function jobsearch_employer_following_action_callback()
{
    $after_label = isset($_POST['label_aftr']) ? $_POST['label_aftr'] : '';
    $employer_id = isset($_POST['emp_id']) ? $_POST['emp_id'] : '';
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        if (jobsearch_user_is_candidate($user_id) && get_post_type($employer_id) == 'employer') {
            $candidate_id = jobsearch_get_user_candidate_id($user_id);
            $cand_followin_list = get_post_meta($candidate_id, 'jobsearch_cand_followins_list', true);
            $cand_followin_list = $cand_followin_list != '' ? explode(',', $cand_followin_list) : array();
            if (!in_array($employer_id, $cand_followin_list)) {
                $cand_followin_list[] = $employer_id;
                $cand_followin_list = implode(',', $cand_followin_list);
                update_post_meta($candidate_id, 'jobsearch_cand_followins_list', $cand_followin_list);
                //
                $employer_user_id = jobsearch_get_employer_user_id($employer_id);
                jobsearch_create_user_meta_list($candidate_id, 'jobsearch-user-followins-list', $employer_user_id);
            }
        }
    }
    $response['status'] = 1;
    $response['label'] = $after_label;
    echo json_encode($response);
    wp_die();
}

function jobsearch_fake_generate_employer_byname($job_empname, $post_id, $logo_url = '') {
    $check_emp_id = jobsearch_get_post_id_bytitle($job_empname, 'employer');
    if ($check_emp_id > 0) {
        update_post_meta($post_id, 'jobsearch_field_job_posted_by', $check_emp_id);
        return $check_emp_id;
    } else {
        $user_login = sanitize_title($job_empname) . '_' . rand(1970, 2020);
        $user_email = $user_login . '@fakeabc.com';
        $user_pass = wp_generate_password(12);
        $create_user = wp_create_user($user_login, $user_pass, $user_email);
        if (!is_wp_error($create_user)) {
            $user_id = $create_user;
            $update_user_arr = array(
                'ID' => $create_user,
                'role' => 'jobsearch_employer'
            );
            wp_update_user($update_user_arr);
            $user_candidate_id = jobsearch_get_user_candidate_id($create_user);
            wp_delete_post($user_candidate_id, true);
            //
            $employer_post = array(
                'post_title' => str_replace(array('-', '_'), array(' ', ' '), $job_empname),
                'post_type' => 'employer',
                'post_content' => '',
                'post_status' => 'publish',
            );
            $employer_id = wp_insert_post($employer_post);
            update_post_meta($employer_id, 'jobsearch_user_id', $create_user);
            update_user_meta($user_id, 'jobsearch_employer_id', $employer_id);
            update_post_meta($employer_id, 'member_display_name', $job_empname);
            update_post_meta($employer_id, 'jobsearch_field_user_email', $user_email);

            update_post_meta($employer_id, 'post_date', strtotime(current_time('d-m-Y H:i:s')));
            update_post_meta($employer_id, 'jobsearch_field_employer_approved', 'on');

            update_user_option($user_id, 'show_admin_bar_front', false);
            update_post_meta($post_id, 'jobsearch_field_job_posted_by', $employer_id);
            
            if ($logo_url != '') {
                jobsearch_attach_emp_img_by_extrnal_url($logo_url, $employer_id);
            }
            
            return $employer_id;
        }
    }
}

function jobsearch_attach_emp_img_by_extrnal_url($image_url, $employer_id) {
    add_filter('upload_dir', 'jobsearch_user_upload_files_path');
    $upload_dir = wp_upload_dir();
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    WP_Filesystem();
    global $wp_filesystem;
    $image_data = $wp_filesystem->get_contents($image_url);
    if (!$image_data && function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $image_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $image_data = curl_exec($ch);
        curl_close($ch);
    }

    if ($image_data) {
        $filename = basename($image_url);
        if (wp_mkdir_p($upload_dir['path'])) {
            $upload_img_path = $upload_dir['path'] . '/' . $filename;
        } else {
            $upload_img_path = $upload_dir['basedir'] . '/' . $filename;
        }
        $wp_filesystem->put_contents($upload_img_path, $image_data);
        
        $wp_filetype = wp_check_filetype($filename, null);
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment($attachment, $upload_img_path, $employer_id);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $upload_img_path);
        wp_update_attachment_metadata($attach_id, $attach_data);

        set_post_thumbnail($employer_id, $attach_id);
    }
    remove_filter('upload_dir', 'jobsearch_user_upload_files_path');
}

//
add_action('admin_init', 'jobsearch_new_employer_add_bk_action');

function jobsearch_new_employer_add_bk_action() {
    if (isset($_POST['post_type']) && $_POST['post_type'] == 'employer' && isset($_POST['user_reg_with_email']) && $_POST['user_reg_with_email'] != '') {
        $post_id = $_POST['post_ID'];
        $user_email = $_POST['user_reg_with_email'];
        
        if (filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            
            if (!email_exists($user_email)) {
                $user_pass = wp_generate_password(12);
                
                $username = $user_email;
                if (isset($_POST['post_title']) && $_POST['post_title'] != '') {
                    $username = sanitize_title($_POST['post_title']);
                }

                $create_user = wp_create_user($username, $user_pass, $user_email);

                if (!is_wp_error($create_user)) {
                    $user_id = $create_user;
                    $update_user_arr = array(
                        'ID' => $user_id,
                        'role' => 'jobsearch_employer'
                    );
                    wp_update_user($update_user_arr);
                    
                    $user_cand_id = get_user_meta($user_id, 'jobsearch_candidate_id', true);
                    if ($user_cand_id > 0 && get_post_type($user_cand_id) == 'candidate') {
                        wp_delete_post($user_cand_id, true);
                    }
                    
                    //
                    update_user_meta($user_id, 'jobsearch_employer_id', $post_id);
                    update_post_meta($post_id, 'jobsearch_user_id', $user_id);
                    update_post_meta($post_id, 'jobsearch_field_user_email', $user_email);
                    update_user_option($user_id, 'show_admin_bar_front', false);
                    //
                    
                    $c_user = get_user_by('email', $user_email);
                    do_action('jobsearch_new_user_register', $c_user, $user_pass);
                }
            } else {
                $user_obj = get_user_by('email', $user_email);
                if (in_array('administrator', (array)$user_obj->roles)) {
                    return false;
                }
                $user_id = $user_obj->ID;
                
                $user_cand_id = get_user_meta($user_id, 'jobsearch_employer_id', true);
                if ($user_cand_id == '') {
                    //
                    update_user_meta($user_id, 'jobsearch_employer_id', $post_id);
                    update_post_meta($post_id, 'jobsearch_user_id', $user_id);
                    update_post_meta($post_id, 'jobsearch_field_user_email', $user_email);
                    //
                }
            }
        }
    }
}
//

function jobsearch_onuser_update_wc_update($user_id) {
    
    $user_is_candidate = jobsearch_user_is_candidate($user_id);
    $user_is_employer = jobsearch_user_is_employer($user_id);
    if ($user_is_employer) {
        $member_id = jobsearch_get_user_employer_id($user_id);
        $employer_name = get_the_title($member_id);
        update_user_meta($member_id, 'billing_company', $employer_name);
        
    } else if ($user_is_candidate) {
        $member_id = jobsearch_get_user_candidate_id($user_id);
    }
    
    if (isset($member_id)) {
        $member_phone = get_post_meta($member_id, 'jobsearch_field_user_phone', true);
        $member_adres = get_post_meta($member_id, 'jobsearch_field_location_address', true);
        $member_country = get_post_meta($member_id, 'jobsearch_field_location_location1', true);
        $member_state = get_post_meta($member_id, 'jobsearch_field_location_location2', true);
        $member_city = get_post_meta($member_id, 'jobsearch_field_location_location3', true);

        if ($member_phone != '') {
            update_user_meta($member_id, 'billing_phone', $member_phone);
        }
        if ($member_adres != '') {
            update_user_meta($member_id, 'billing_address_1', $member_adres);
        }
        if ($member_country != '') {
            update_user_meta($member_id, 'billing_country', $member_country);
        }
        if ($member_state != '') {
            update_user_meta($member_id, 'billing_state', $member_state);
        }
        if ($member_city != '') {
            update_user_meta($member_id, 'billing_city', $member_city);
        }
    }
}

add_action('profile_update', 'jobsearch_onuser_profile_update', 10, 2);

function jobsearch_onuser_profile_update($user_id, $old_user_data) {
    global $jobsearch_plugin_options;
    
    if (isset($_POST['user_settings_form']) && $_POST['user_settings_form'] == '1') {
        return false;
    }
    $loc_fields_count = isset($jobsearch_plugin_options['jobsearch-location-required-fields-count']) ? $jobsearch_plugin_options['jobsearch-location-required-fields-count'] : 'all';
    
    $user_billing_phone = get_user_meta($user_id, 'billing_phone', true);
    $user_billing_company = get_user_meta($user_id, 'billing_company', true);
    $user_billing_adress1 = get_user_meta($user_id, 'billing_address_1', true);
    $user_billing_country = get_user_meta($user_id, 'billing_country', true);
    $user_billing_state = get_user_meta($user_id, 'billing_state', true);
    $user_billing_city = get_user_meta($user_id, 'billing_city', true);
    
    $member_id = 0;
    
    $user_is_candidate = jobsearch_user_is_candidate($user_id);
    $user_is_employer = jobsearch_user_is_employer($user_id);
    if ($user_is_employer) {
        $member_id = jobsearch_get_user_employer_id($user_id);
        
        if ($user_billing_company != '') {
            $up_post = array(
                'ID' => $member_id,
                'post_title' => wp_strip_all_tags($user_billing_company),
            );
            wp_update_post($up_post);
            update_post_meta($member_id, 'member_display_name', wp_strip_all_tags($user_billing_company));
        }
    } else if ($user_is_candidate) {
        $member_id = jobsearch_get_user_candidate_id($user_id);
    }
    
    if ($member_id > 0) {
        if ($user_billing_phone != '') {
            update_post_meta($member_id, 'jobsearch_field_user_phone', $user_billing_phone);
        }

        if ($user_billing_adress1 != '') {
            update_post_meta($member_id, 'jobsearch_field_location_address', $user_billing_adress1);
        }
        if ($user_billing_country != '') {
            update_post_meta($member_id, 'jobsearch_field_location_location1', $user_billing_country);
        }
        if ($user_billing_state != '') {
            update_post_meta($member_id, 'jobsearch_field_location_location2', $user_billing_state);
        }
        if ($user_billing_city != '') {
            update_post_meta($member_id, 'jobsearch_field_location_location3', $user_billing_city);
            if ($loc_fields_count <= 2) {
                update_post_meta($member_id, 'jobsearch_field_location_location2', $user_billing_city);
            }
        }
    }
}