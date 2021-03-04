<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 */

use WP_Jobsearch\Candidate_Profile_Restriction;

global $post, $jobsearch_plugin_options;
$candidate_id = $post->ID;

$candidate_user_id = jobsearch_get_candidate_user_id($candidate_id);
$cand_profile_restrict = new Candidate_Profile_Restriction;
$candidates_reviews = isset($jobsearch_plugin_options['candidate_reviews_switch']) ? $jobsearch_plugin_options['candidate_reviews_switch'] : '';

$all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
$cand_det_full_address_switch = true;

$locations_view_type = isset($jobsearch_plugin_options['cand_det_loc_listing']) ? $jobsearch_plugin_options['cand_det_loc_listing'] : '';
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
if ($loc_view_country || $loc_view_state || $loc_view_city) {
    $cand_det_full_address_switch = false;
}

$view_candidate = true;
$restrict_candidates = isset($jobsearch_plugin_options['restrict_candidates']) ? $jobsearch_plugin_options['restrict_candidates'] : '';

$view_cand_type = 'fully';
$emp_cvpbase_restrictions = isset($jobsearch_plugin_options['emp_cv_pkgbase_restrictions']) ? $jobsearch_plugin_options['emp_cv_pkgbase_restrictions'] : '';
$restrict_cand_type = isset($jobsearch_plugin_options['restrict_candidates_for_users']) ? $jobsearch_plugin_options['restrict_candidates_for_users'] : '';
if ($emp_cvpbase_restrictions == 'on' && $restrict_cand_type != 'only_applicants') {
    $view_cand_type = 'partly';
}

$restrict_candidates_for_users = isset($jobsearch_plugin_options['restrict_candidates_for_users']) ? $jobsearch_plugin_options['restrict_candidates_for_users'] : '';

$is_employer = false;
if ($restrict_candidates == 'on' && $view_cand_type == 'fully') {
    $view_candidate = false;

    if (is_user_logged_in()) {
        $cur_user_id = get_current_user_id();
        $cur_user_obj = wp_get_current_user();
        if (jobsearch_user_isemp_member($cur_user_id)) {
            $employer_id = jobsearch_user_isemp_member($cur_user_id);
            $cur_user_id = jobsearch_get_employer_user_id($employer_id);
        } else {
            $employer_id = jobsearch_get_user_employer_id($cur_user_id);
        }
        $ucandidate_id = jobsearch_get_user_candidate_id($cur_user_id);
        $employer_dbstatus = get_post_meta($employer_id, 'jobsearch_field_employer_approved', true);
        if ($employer_id > 0 && $employer_dbstatus == 'on') {
            $is_employer = true;
            $is_applicant = false;
            //
            $employer_job_args = array(
                'post_type' => 'job',
                'posts_per_page' => '-1',
                'post_status' => 'publish',
                'fields' => 'ids',
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_field_job_posted_by',
                        'value' => $employer_id,
                        'compare' => '=',
                    ),
                ),
            );
            $employer_jobs_query = new WP_Query($employer_job_args);
            $employer_jobs_posts = $employer_jobs_query->posts;
            if (!empty($employer_jobs_posts) && is_array($employer_jobs_posts)) {
                foreach ($employer_jobs_posts as $employer_job_id) {
                    $finded_result_list = jobsearch_find_index_user_meta_list($employer_job_id, 'jobsearch-user-jobs-applied-list', 'post_id', $candidate_user_id);
                    if (is_array($finded_result_list) && !empty($finded_result_list)) {
                        $is_applicant = true;
                        break;
                    }
                }
            }
            //
            if ($restrict_candidates_for_users == 'register_resume') {
                $user_cv_pkg = jobsearch_employer_first_subscribed_cv_pkg($cur_user_id);
                if (!$user_cv_pkg) {
                    $user_cv_pkg = jobsearch_allin_first_pkg_subscribed($cur_user_id, 'cvs');
                }
                if (!$user_cv_pkg) {
                    $user_cv_pkg = jobsearch_emprof_first_pkg_subscribed($cur_user_id, 'cvs');
                }
                if ($user_cv_pkg) {
                    $view_candidate = true;
                } else {
                    if ($is_applicant) {
                        $view_candidate = true;
                    }
                }
            } else if ($restrict_candidates_for_users == 'only_applicants') {
                if ($is_applicant) {
                    $view_candidate = true;
                }
            } else {
                $view_candidate = true;
            }
        } else if (in_array('administrator', (array)$cur_user_obj->roles)) {
            $view_candidate = true;
        } else if ($ucandidate_id > 0 && $ucandidate_id == $candidate_id) {
            $view_candidate = true;
        } else if ($restrict_candidates_for_users == 'register_empcand' && ($ucandidate_id > 0 || $employer_id > 0)) {
            $view_candidate = true;
        }
    }
}

$captcha_switch = isset($jobsearch_plugin_options['captcha_switch']) ? $jobsearch_plugin_options['captcha_switch'] : '';
$jobsearch_sitekey = isset($jobsearch_plugin_options['captcha_sitekey']) ? $jobsearch_plugin_options['captcha_sitekey'] : '';

$plugin_default_view = isset($jobsearch_plugin_options['jobsearch-default-page-view']) ? $jobsearch_plugin_options['jobsearch-default-page-view'] : 'full';
$plugin_default_view_with_str = '';
if ($plugin_default_view == 'boxed') {

    $plugin_default_view_with_str = isset($jobsearch_plugin_options['jobsearch-boxed-view-width']) && $jobsearch_plugin_options['jobsearch-boxed-view-width'] != '' ? $jobsearch_plugin_options['jobsearch-boxed-view-width'] : '1140px';
    if ($plugin_default_view_with_str != '' && !wp_is_mobile()) {
        $plugin_default_view_with_str = ' style="width:' . $plugin_default_view_with_str . '"';
    }
}

wp_enqueue_script('jobsearch-progressbar');

$inopt_cover_letr = isset($jobsearch_plugin_options['cand_resm_cover_letr']) ? $jobsearch_plugin_options['cand_resm_cover_letr'] : '';
$inopt_resm_education = isset($jobsearch_plugin_options['cand_resm_education']) ? $jobsearch_plugin_options['cand_resm_education'] : '';
$inopt_resm_experience = isset($jobsearch_plugin_options['cand_resm_experience']) ? $jobsearch_plugin_options['cand_resm_experience'] : '';
$inopt_resm_portfolio = isset($jobsearch_plugin_options['cand_resm_portfolio']) ? $jobsearch_plugin_options['cand_resm_portfolio'] : '';
$inopt_resm_skills = isset($jobsearch_plugin_options['cand_resm_skills']) ? $jobsearch_plugin_options['cand_resm_skills'] : '';
$inopt_resm_langs = isset($jobsearch_plugin_options['cand_resm_langs']) ? $jobsearch_plugin_options['cand_resm_langs'] : '';
$inopt_resm_honsawards = isset($jobsearch_plugin_options['cand_resm_honsawards']) ? $jobsearch_plugin_options['cand_resm_honsawards'] : '';

$candidate_obj = get_post($candidate_id);
$candidate_content = $candidate_obj->post_content;
$candidate_content = apply_filters('the_content', $candidate_content);

$candidate_join_date = isset($candidate_obj->post_date) ? $candidate_obj->post_date : '';

$candidate_jobtitle = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
$candidate_address = get_post_meta($candidate_id, 'jobsearch_field_location_address', true);
if (function_exists('jobsearch_post_city_contry_txtstr')) {
    $candidate_address = jobsearch_post_city_contry_txtstr($candidate_id, $loc_view_country, $loc_view_state, $loc_view_city, $cand_det_full_address_switch);
}

$user_facebook_url = get_post_meta($candidate_id, 'jobsearch_field_user_facebook_url', true);
$user_twitter_url = get_post_meta($candidate_id, 'jobsearch_field_user_twitter_url', true);
$user_google_plus_url = get_post_meta($candidate_id, 'jobsearch_field_user_google_plus_url', true);
$user_youtube_url = get_post_meta($candidate_id, 'jobsearch_field_user_youtube_url', true);
$user_dribbble_url = get_post_meta($candidate_id, 'jobsearch_field_user_dribbble_url', true);
$user_linkedin_url = get_post_meta($candidate_id, 'jobsearch_field_user_linkedin_url', true);

$user_id = jobsearch_get_candidate_user_id($candidate_id);
$user_obj = get_user_by('ID', $user_id);
$user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
$user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);
if ($cand_profile_restrict::cand_field_is_locked('profile_fields|display_name', 'detail_page')) {
    $user_displayname = $cand_profile_restrict::cand_restrict_display_name();
}

$candidate_cover_image_src_style_str = '';
if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|cover_img', 'detail_page')) {
    if ($candidate_id != '') {
        $candidate_cover_image_src = '';
        if (function_exists('jobsearch_candidate_covr_url_comn')) {
            $candidate_cover_image_src = jobsearch_candidate_covr_url_comn($candidate_id);
        }
        if ($candidate_cover_image_src != '') {
            $candidate_cover_image_src_style_str = ' style="background:url(\'' . ($candidate_cover_image_src) . '\'); background-size:cover; "';
        }
    }
}

$subheader_candidate_bg_color = isset($jobsearch_plugin_options['careerfy-candidate-img-overlay-bg-color']) ? $jobsearch_plugin_options['careerfy-candidate-img-overlay-bg-color'] : '';
if (isset($subheader_candidate_bg_color['rgba'])) {
    $subheader_bg_color = $subheader_candidate_bg_color['rgba'];
}

$user_def_avatar_url = jobsearch_candidate_img_url_comn($candidate_id);
wp_enqueue_script('isotope-min');

if ($view_candidate !== false) { ?>

    <div class="jobsearch-job-subheader"<?php echo($candidate_cover_image_src_style_str); ?>>
        <span class="jobsearch-banner-transparent" style="background: <?php echo $subheader_bg_color ?>"></span>
        <div class="jobsearch-plugin-default-container">
            <div class="jobsearch-row">
                <div class="jobsearch-column-12">
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

<div class="jobsearch-main-content">

    <!-- Main Section -->
    <div class="jobsearch-main-section">
        <div class="jobsearch-plugin-default-container" <?php echo($plugin_default_view_with_str); ?>>
            <div class="jobsearch-row">
                <?php
                if ($view_candidate === false) {
                    $restrict_img = isset($jobsearch_plugin_options['candidate_restrict_img']) ? $jobsearch_plugin_options['candidate_restrict_img'] : '';
                    $restrict_img_url = isset($restrict_img['url']) ? $restrict_img['url'] : '';
                    $restrict_cv_pckgs = isset($jobsearch_plugin_options['restrict_cv_packages']) ? $jobsearch_plugin_options['restrict_cv_packages'] : '';
                    $restrict_msg = isset($jobsearch_plugin_options['restrict_cand_msg']) && $jobsearch_plugin_options['restrict_cand_msg'] != '' ? $jobsearch_plugin_options['restrict_cand_msg'] : esc_html__('The Page is Restricted only for Subscribed Employers', 'wp-jobsearch');

                    $op_emp_register_allow = isset($jobsearch_plugin_options['login_employer_register']) ? $jobsearch_plugin_options['login_employer_register'] : '';
                    ?>
                    <div class="jobsearch-column-12">
                        <div class="restrict-candidate-sec">
                            <img src="<?php echo($restrict_img_url) ?>" alt="">
                            <h2><?php echo($restrict_msg) ?></h2>

                            <?php
                            if ($is_employer) { ?>
                                <p><?php esc_html_e('Please buy a C.V package to view this candidate.', 'wp-jobsearch') ?></p>
                            <?php } else if (is_user_logged_in()) { ?>
                                <p><?php esc_html_e('You are not an employer. Only an Employer can view a candidate.', 'wp-jobsearch') ?></p>
                            <?php } else { ?>
                                <p><?php esc_html_e('If you are employer just login to view this candidate or buy a C.V package to download His Resume.', 'wp-jobsearch') ?></p>
                                <?php
                            }
                            if (is_user_logged_in()) { ?>
                                <div class="login-btns">
                                    <a href="<?php echo wp_logout_url(home_url('/')); ?>"><i
                                                class="jobsearch-icon jobsearch-logout"></i><?php esc_html_e('Logout', 'wp-jobsearch') ?>
                                    </a>
                                </div>
                            <?php } else { ?>
                                <div class="login-btns">
                                    <a href="javascript:void(0);" class="jobsearch-open-signin-tab"><i
                                                class="jobsearch-icon jobsearch-user"></i><?php esc_html_e('Login', 'wp-jobsearch') ?>
                                    </a>
                                    <?php
                                    if ($op_emp_register_allow != 'no') {
                                        ?>
                                        <a href="javascript:void(0);"
                                           class="jobsearch-open-register-tab company-register-tab"><i
                                                    class="jobsearch-icon jobsearch-plus"></i><?php esc_html_e('Become an Employer', 'wp-jobsearch') ?>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            if (!empty($restrict_cv_pckgs) && is_array($restrict_cv_pckgs) && $restrict_candidates_for_users == 'register_resume') {
                                ?>
                                <div class="jobsearch-box-title">
                                    <span><?php esc_html_e('OR', 'wp-jobsearch') ?></span>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                        if (!empty($restrict_cv_pckgs) && is_array($restrict_cv_pckgs) && $restrict_candidates_for_users == 'register_resume') {
                            wp_enqueue_script('jobsearch-packages-scripts');
                            ?>
                            <div class="cv-packages-section">
                                <div class="packages-title">
                                    <h2><?php esc_html_e('Buy any CV Packages to get started', 'wp-jobsearch') ?></h2>
                                </div>
                                <?php
                                ob_start();
                                ?>
                                <div class="jobsearch-row">
                                    <?php
                                    foreach ($restrict_cv_pckgs as $restrict_cv_pckg) {
                                        $cv_pkg_obj = $restrict_cv_pckg != '' ? get_page_by_path($restrict_cv_pckg, 'OBJECT', 'package') : '';
                                        if (is_object($cv_pkg_obj) && isset($cv_pkg_obj->ID)) {
                                            $cv_pkg_id = $cv_pkg_obj->ID;
                                            $pkg_type = get_post_meta($cv_pkg_id, 'jobsearch_field_charges_type', true);
                                            $pkg_price = get_post_meta($cv_pkg_id, 'jobsearch_field_package_price', true);

                                            $num_of_cvs = get_post_meta($cv_pkg_id, 'jobsearch_field_num_of_cvs', true);
                                            $pkg_exp_dur = get_post_meta($cv_pkg_id, 'jobsearch_field_package_expiry_time', true);
                                            $pkg_exp_dur_unit = get_post_meta($cv_pkg_id, 'jobsearch_field_package_expiry_time_unit', true);

                                            $pkg_exfield_title = get_post_meta($cv_pkg_id, 'jobsearch_field_package_exfield_title', true);
                                            $pkg_exfield_val = get_post_meta($cv_pkg_id, 'jobsearch_field_package_exfield_val', true);
                                            $pkg_exfield_status = get_post_meta($cv_pkg_id, 'jobsearch_field_package_exfield_status', true);

                                            ?>
                                            <div class="jobsearch-column-4">
                                                <div class="jobsearch-classic-priceplane">
                                                    <h2><?php echo get_the_title($cv_pkg_id) ?></h2>
                                                    <div class="jobsearch-priceplane-section">
                                                        <?php
                                                        if ($pkg_type == 'paid') {
                                                            echo '<span>' . jobsearch_get_price_format($pkg_price) . ' <small>' . esc_html__('only', 'wp-jobsearch') . '</small></span>';
                                                        } else {
                                                            echo '<span>' . esc_html__('Free', 'wp-jobsearch') . '</span>';
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="grab-classic-priceplane">
                                                        <ul>
                                                            <?php
                                                            if (!empty($pkg_exfield_title)) {
                                                                $_exf_counter = 0;
                                                                foreach ($pkg_exfield_title as $_exfield_title) {
                                                                    $_exfield_val = isset($pkg_exfield_val[$_exf_counter]) ? $pkg_exfield_val[$_exf_counter] : '';
                                                                    $_exfield_status = isset($pkg_exfield_status[$_exf_counter]) ? $pkg_exfield_status[$_exf_counter] : '';
                                                                    if ($_exfield_title != '') {
                                                                        ?>
                                                                        <li<?php echo($_exfield_status == 'active' ? ' class="active"' : '') ?>>
                                                                            <i class="jobsearch-icon jobsearch-check-square"></i> <?php echo $_exfield_title . ' ' . $_exfield_val ?>
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                    $_exf_counter++;
                                                                }
                                                            }
                                                            ?>
                                                        </ul>
                                                        <?php if (is_user_logged_in()) { ?>
                                                            <a href="javascript:void(0);"
                                                               class="jobsearch-classic-priceplane-btn jobsearch-subscribe-cv-pkg"
                                                               data-id="<?php echo($cv_pkg_id) ?>"><?php esc_html_e('Get Started', 'wp-jobsearch') ?> </a>
                                                            <span class="pkg-loding-msg" style="display:none;"></span>
                                                        <?php } else { ?>
                                                            <a href="javascript:void(0);"
                                                               class="jobsearch-classic-priceplane-btn jobsearch-open-signin-tab"><?php esc_html_e('Get Started', 'wp-jobsearch') ?> </a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                                <?php
                                $pkgs_html = ob_get_clean();
                                echo apply_filters('jobsearch_restrict_candidate_pakgs_html', $pkgs_html, $restrict_cv_pckgs);
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                } else {
                    $cand_dob_switch = isset($jobsearch_plugin_options['cand_dob_switch']) ? $jobsearch_plugin_options['cand_dob_switch'] : 'on';
                    $candidate_age = jobsearch_candidate_age($candidate_id);
                    $candidate_salary_switch = isset($jobsearch_plugin_options['cand_salary_switch']) ? $jobsearch_plugin_options['cand_salary_switch'] : 'on';
                    $candidate_salary = jobsearch_candidate_current_salary($candidate_id);
                    //
                    $membsectors_enable_switch = isset($jobsearch_plugin_options['usersector_onoff_switch']) ? $jobsearch_plugin_options['usersector_onoff_switch'] : '';
                    $sectors_enable_switch = ($membsectors_enable_switch == 'on_cand' || $membsectors_enable_switch == 'on_both') ? 'on' : '';
                    $sectors = wp_get_post_terms($candidate_id, 'sector');
                    $candidate_sector = isset($sectors[0]->name) ? $sectors[0]->name : '';
                    ?>
                    <aside class="jobsearch-column-4 jobsearch-typo-wrap">
                        <div class="jobsearch_side_box jobsearch_box_candidate_info">
                            <div class="jobsearch_candidate_info">
                                <?php echo jobsearch_member_promote_profile_iconlab($candidate_id); ?>
                                <?php
                                ob_start();
                                echo jobsearch_cand_urgent_pkg_iconlab($candidate_id, 'cand_listv2');
                                $urgnt_html = ob_get_clean();
                                echo apply_filters('jobsearch_cand_urgent_pkg_iconlab_html', $urgnt_html, $candidate_id, 'cand_listv2');

                                if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|profile_img', 'detail_page')) { ?>
                                    <figure><img src="<?php echo($user_def_avatar_url) ?>" alt=""></figure>
                                    <?php
                                }
                                if ($candidates_reviews == 'on') {
                                    $post_avg_review_args = array(
                                        'post_id' => $candidate_id,
                                    );
                                    do_action('jobsearch_cand_detail_post_avg_rating', $post_avg_review_args);
                                }

                                ob_start();
                                ?>
                                <h2>
                                    <a><?php echo apply_filters('jobsearch_candidate_detail_side_displayname', $user_displayname, $candidate_id) ?></a>
                                </h2>
                                <?php
                                if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|job_title', 'detail_page')) {
                                    ob_start();
                                    ?>
                                    <p><?php echo jobsearch_esc_html($candidate_jobtitle) ?></p>
                                    <?php
                                    $candidate_jobtitle_html = ob_get_clean();
                                    echo apply_filters('jobsearch_candetail_jobtitle_html', $candidate_jobtitle_html, $candidate_id, 'view1');
                                }
                                $phone_number_switch = isset($jobsearch_plugin_options['cand_phone_switch']) ? $jobsearch_plugin_options['cand_phone_switch'] : '';
                                if ($phone_number_switch != 'off') {
                                    $candidate_phone = get_post_meta($candidate_id, 'jobsearch_field_user_phone', true);
                                    if ($candidate_phone != '' && !$cand_profile_restrict::cand_field_is_locked('profile_fields|phone')) {
                                        echo '<p>' . sprintf(esc_html__('Phone: %s', 'wp-jobsearch'), $candidate_phone) . '</p>';
                                    }
                                }
                                if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|sector', 'detail_page')) {
                                    if ($candidate_sector != '' && $sectors_enable_switch == 'on') {
                                        echo '<p>' . sprintf(esc_html__('Sector: %s', 'wp-jobsearch'), apply_filters('jobsearch_gew_wout_anchr_sector_str_html', $candidate_sector, $candidate_id)) . '</p>';
                                    }
                                }

                                do_action('jobsearch_cand_detail_side_after_sector', $candidate_id);
                                if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|salary', 'detail_page')) {
                                    if ($candidate_salary != '' && $candidate_salary_switch == 'on') {
                                        echo '<p>' . sprintf(esc_html__('Salary: %s', 'wp-jobsearch'), $candidate_salary) . '</p>';
                                    }
                                }
                                do_action('jobsearch_cand_detail_side_after_salary', $candidate_id);
                                if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|date_of_birth', 'detail_page')) {
                                    if ($candidate_age != '' && $cand_dob_switch != 'off') {
                                        echo apply_filters('jobsearch_candidate_detail_page_age_html', '<p>' . sprintf(esc_html__('(Age: %s years)', 'wp-jobsearch'), $candidate_age) . '</p>');
                                    }
                                }
                                if (!$cand_profile_restrict::cand_field_is_locked('address_defields', 'detail_page') && $candidate_address != '' && $all_location_allow == 'on') {
                                    ?>
                                    <span><?php echo jobsearch_esc_html($candidate_address) ?></span>
                                    <?php
                                }
                                if ($candidate_join_date != '') { ?>
                                    <small><?php printf(esc_html__('Member Since, %s', 'wp-jobsearch'), date_i18n(get_option('date_format'), strtotime($candidate_join_date))) ?></small>
                                    <?php
                                }

                                if (!$cand_profile_restrict::cand_field_is_locked('socialicons_defields', 'detail_page')) {
                                    $cand_alow_fb_smm = isset($jobsearch_plugin_options['cand_alow_fb_smm']) ? $jobsearch_plugin_options['cand_alow_fb_smm'] : '';
                                    $cand_alow_twt_smm = isset($jobsearch_plugin_options['cand_alow_twt_smm']) ? $jobsearch_plugin_options['cand_alow_twt_smm'] : '';
                                    $cand_alow_gplus_smm = isset($jobsearch_plugin_options['cand_alow_gplus_smm']) ? $jobsearch_plugin_options['cand_alow_gplus_smm'] : '';
                                    $cand_alow_linkd_smm = isset($jobsearch_plugin_options['cand_alow_linkd_smm']) ? $jobsearch_plugin_options['cand_alow_linkd_smm'] : '';
                                    $cand_alow_dribbb_smm = isset($jobsearch_plugin_options['cand_alow_dribbb_smm']) ? $jobsearch_plugin_options['cand_alow_dribbb_smm'] : '';
                                    $candidate_social_mlinks = isset($jobsearch_plugin_options['candidate_social_mlinks']) ? $jobsearch_plugin_options['candidate_social_mlinks'] : '';
                                    if (!empty($candidate_social_mlinks) || ($cand_alow_fb_smm == 'on' || $cand_alow_twt_smm == 'on' || $cand_alow_gplus_smm == 'on' || $cand_alow_linkd_smm == 'on' || $cand_alow_dribbb_smm == 'on')) {
                                        ob_start();
                                        ?>
                                        <ul>
                                            <?php
                                            if ($user_facebook_url != '' && $cand_alow_fb_smm == 'on') {
                                                ?>
                                                <li>
                                                    <a href="<?php echo jobsearch_esc_html(esc_url($user_facebook_url)) ?>"
                                                       data-original-title="facebook"
                                                       class="jobsearch-icon jobsearch-facebook-logo"></a></li>
                                                <?php
                                            }
                                            if ($user_twitter_url != '' && $cand_alow_twt_smm == 'on') {
                                                ?>
                                                <li>
                                                    <a href="<?php echo jobsearch_esc_html(esc_url($user_twitter_url)) ?>"
                                                       target="_blank"
                                                       data-original-title="twitter"
                                                       class="jobsearch-icon jobsearch-twitter-logo"></a></li>
                                                <?php
                                            }
                                            if ($user_linkedin_url != '' && $cand_alow_linkd_smm == 'on') { ?>
                                                <li>
                                                    <a href="<?php echo jobsearch_esc_html(esc_url($user_linkedin_url)) ?>"
                                                       target="_blank"
                                                       data-original-title="linkedin"
                                                       class="jobsearch-icon jobsearch-linkedin-button"></a></li>
                                                <?php
                                            }
                                            if ($user_dribbble_url != '' && $cand_alow_dribbb_smm == 'on') {
                                                ?>
                                                <li>
                                                    <a href="<?php echo jobsearch_esc_html(esc_url($user_dribbble_url)) ?>"
                                                       target="_blank"
                                                       data-original-title="dribbble"
                                                       class="jobsearch-icon jobsearch-dribbble-logo"></a></li>
                                                <?php
                                            }
                                            if (!empty($candidate_social_mlinks)) {
                                                if (isset($candidate_social_mlinks['title']) && is_array($candidate_social_mlinks['title'])) {
                                                    $field_counter = 0;
                                                    foreach ($candidate_social_mlinks['title'] as $field_title_val) {
                                                        $field_random = rand(10000000, 99999999);
                                                        $field_icon_styles = '';
                                                        $field_icon = isset($candidate_social_mlinks['icon'][$field_counter]) ? $candidate_social_mlinks['icon'][$field_counter] : '';
                                                        $field_icon_group = isset($candidate_social_mlinks['icon_group'][$field_counter]) ? $candidate_social_mlinks['icon_group'][$field_counter] : '';
                                                        if ($field_icon_group == '') {
                                                            $field_icon_group = 'default';
                                                        }
                                                        $field_icon_clr = isset($candidate_social_mlinks['icon_clr'][$field_counter]) ? $candidate_social_mlinks['icon_clr'][$field_counter] : '';
                                                        if ($field_icon_clr != '') {
                                                            $field_icon_styles .= 'color: ' . $field_icon_clr . ';';
                                                        }
                                                        $field_icon_bgclr = isset($candidate_social_mlinks['icon_bgclr'][$field_counter]) ? $candidate_social_mlinks['icon_bgclr'][$field_counter] : '';
                                                        if ($field_icon_bgclr != '') {
                                                            $field_icon_styles .= ' background-color: ' . $field_icon_bgclr . ';';
                                                        }
                                                        $cand_dynm_social = get_post_meta($candidate_id, 'jobsearch_field_dynm_social' . $field_counter, true);
                                                        if ($field_title_val != '' && $cand_dynm_social != '') {
                                                            ?>
                                                            <li>
                                                                <a href="<?php echo esc_url($cand_dynm_social) ?>"
                                                                   target="_blank" <?php echo($field_icon_styles != '' ? 'style="' . $field_icon_styles . '"' : '') ?>
                                                                   class="<?php echo jobsearch_esc_html($field_icon) ?>"></a>
                                                            </li>
                                                            <?php
                                                        }
                                                        $field_counter++;
                                                    }
                                                }
                                            }
                                            ?>
                                        </ul>
                                        <?php
                                        $cand_socilinks = ob_get_clean();
                                        echo apply_filters('jobsearch_cand_detail_socilinks_html', $cand_socilinks, $candidate_id);
                                    }
                                }

                                do_action('jobsearch_download_candidate_cv_btn', array('id' => $candidate_id));
                                jobsearch_add_employer_resume_to_list_btn(array('id' => $candidate_id));

                                jobsearch_candidate_detail_whatsapp_btn($candidate_id);

                                $info_html = ob_get_clean();
                                echo apply_filters('jobsearch_candetail_page_sideinfo_html', $info_html, $candidate_id);
                                //
                                $cand_chat_args = array('candidate_id' => $candidate_id);

                                do_action('jobsearch_chat_with_candidate', $cand_chat_args);

                                ?>
                            </div>
                        </div>
                        <?php do_action('jobsearch_candidate_detail_side_after_info', array('id' => $candidate_id)); ?>
                        <?php
                        $map_switch_arr = isset($jobsearch_plugin_options['jobsearch-detail-map-switch']) ? $jobsearch_plugin_options['jobsearch-detail-map-switch'] : '';
                        $detail_map = is_array($map_switch_arr) && in_array('candidate', $map_switch_arr) ? 'on' : '';
                        if (!$cand_profile_restrict::cand_field_is_locked('address_defields', 'detail_page') && $detail_map == 'on') {
                            ?>
                            <div class="jobsearch_side_box jobsearch_box_map">
                                <?php jobsearch_google_map_with_directions($candidate_id); ?>
                            </div>
                            <?php
                        }

                        //
                        $ad_args = array(
                            'post_type' => 'candidate',
                            'view' => 'view1',
                            'position' => 'b4_cntct',
                        );
                        jobsearch_detail_common_ad_code($ad_args);
                        $cand_det_contact_form = isset($jobsearch_plugin_options['cand_det_contact_form']) ? $jobsearch_plugin_options['cand_det_contact_form'] : '';
                        if ($cand_det_contact_form == 'on') {
                            if (!$cand_profile_restrict::cand_field_is_locked('contactfrm_defields', 'detail_page')) {
                                ob_start();
                                ?>
                                <div class="jobsearch_side_box jobsearch_box_contact_form">
                                    <?php
                                    $cnt_counter = rand(1000000, 9999999);

                                    $cnt__cand_wout_log = isset($jobsearch_plugin_options['cand_cntct_wout_login']) ? $jobsearch_plugin_options['cand_cntct_wout_login'] : '';

                                    $cur_user_name = '';
                                    $cur_user_email = '';
                                    $field_readonly = false;
                                    if (is_user_logged_in()) {
                                        if ($cnt__cand_wout_log != 'on') {
                                            $field_readonly = true;
                                        }
                                        $cur_user_id = get_current_user_id();
                                        $cur_user_obj = wp_get_current_user();
                                        $cur_user_name = isset($cur_user_obj->display_name) ? $cur_user_obj->display_name : '';
                                        $cur_user_email = isset($cur_user_obj->user_email) ? $cur_user_obj->user_email : '';
                                        if (jobsearch_user_is_employer($cur_user_id)) {
                                            $cnt_emp_id = jobsearch_get_user_employer_id($cur_user_id);
                                            $cur_user_name = get_the_title($cnt_emp_id);
                                        }
                                    }
                                    ?>
                                    <div class="jobsearch-wdg-box-title">
                                        <h2><?php esc_html_e('Contact Form', 'wp-jobsearch') ?></h2>
                                    </div>
                                    <form id="ct-form-<?php echo absint($cnt_counter) ?>"
                                          data-uid="<?php echo absint($user_id) ?>" method="post">
                                        <ul>
                                            <li>
                                                <label><?php esc_html_e('Name:', 'wp-jobsearch') ?></label>
                                                <input name="u_name"
                                                       placeholder="<?php esc_html_e('Enter Your Name', 'wp-jobsearch') ?>"
                                                       type="text" <?php echo($field_readonly ? 'readonly' : '') ?>
                                                       value="<?php echo($cur_user_name) ?>">
                                                <i class="jobsearch-icon jobsearch-user"></i>
                                            </li>
                                            <li>
                                                <label><?php esc_html_e('Email Address:', 'wp-jobsearch') ?></label>
                                                <input name="u_email"
                                                       placeholder="<?php esc_html_e('Enter Your Email Address', 'wp-jobsearch') ?>"
                                                       type="text" <?php echo($field_readonly ? 'readonly' : '') ?>
                                                       value="<?php echo($cur_user_email) ?>">
                                                <i class="jobsearch-icon jobsearch-mail"></i>
                                            </li>
                                            <li>
                                                <label><?php esc_html_e('Phone Number:', 'wp-jobsearch') ?></label>
                                                <input name="u_number"
                                                       placeholder="<?php esc_html_e('Enter Your Phone Number', 'wp-jobsearch') ?>"
                                                       type="text">
                                                <i class="jobsearch-icon jobsearch-technology"></i>
                                            </li>
                                            <li>
                                                <label><?php esc_html_e('Message:', 'wp-jobsearch') ?></label>
                                                <textarea name="u_msg"
                                                          placeholder="<?php esc_html_e('Type Your Message here', 'wp-jobsearch') ?>"></textarea>
                                            </li>
                                            <?php
                                            if ($captcha_switch == 'on') {
                                                wp_enqueue_script('jobsearch_google_recaptcha');
                                                ?>
                                                <li>
                                                    <script>
                                                        var recaptcha_cand_contact;
                                                        var jobsearch_multicap = function () {
                                                            //Render the recaptcha_cand_contact on the element with ID "recaptcha1"
                                                            recaptcha_cand_contact = grecaptcha.render('recaptcha_cand_contact', {
                                                                'sitekey': '<?php echo($jobsearch_sitekey); ?>', //Replace this with your Site key
                                                                'theme': 'light'
                                                            });
                                                        };
                                                        jQuery(document).ready(function () {
                                                            jQuery('.recaptcha-reload-a').click();
                                                        });
                                                    </script>
                                                    <div class="recaptcha-reload" id="recaptcha_cand_contact_div">
                                                        <?php echo jobsearch_recaptcha('recaptcha_cand_contact'); ?>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                            <li>
                                                <?php
                                                jobsearch_terms_and_con_link_txt();
                                                ?>
                                                <input type="submit" class="jobsearch-candidate-ct-form"
                                                       data-id="<?php echo absint($cnt_counter) ?>"
                                                       value="<?php esc_html_e('Send now', 'wp-jobsearch') ?>">
                                                <?php
                                                if (!is_user_logged_in() && $cnt__cand_wout_log != 'on') {
                                                    ?>
                                                    <a class="jobsearch-open-signin-tab"
                                                       style="display: none;"><?php esc_html_e('login', 'wp-jobsearch') ?></a>
                                                    <?php
                                                }
                                                ?>
                                            </li>
                                        </ul>
                                        <span class="jobsearch-ct-msg"></span>
                                    </form>
                                </div>
                                <?php
                                $cand_cntct_form = ob_get_clean();
                                echo apply_filters('jobsearch_candidate_detail_cntct_frm_html', $cand_cntct_form, $candidate_id);
                            }
                        }
                        $ad_args = array(
                            'post_type' => 'candidate',
                            'view' => 'view1',
                            'position' => 'aftr_cntct',
                        );
                        jobsearch_detail_common_ad_code($ad_args);
                        ?>
                    </aside>
                    <div class="jobsearch-column-8 jobsearch-typo-wrap">
                        <div class="container-wrapper container-wrapper-view1">
                            <div class="jobsearch-candidate-editor">
                                <?php
                                $show_disp_name = apply_filters('jobsearch_candidate_detail_content_top_displayname', $user_displayname, $candidate_id);
                                ?>
                                <div class="jobsearch-content-title">
                                    <h2><?php printf(esc_html__('About %s', 'wp-jobsearch'), $show_disp_name) ?></h2>
                                </div>
                                <?php
                                if (!$cand_profile_restrict::cand_field_is_locked('customfields_defields', 'detail_page')) {
                                    echo apply_filters('jobsearch_canddetail_page_before_cusfields_html', '', $candidate_id);
                                    $custom_all_fields = get_option('jobsearch_custom_field_candidate');

                                    if (!empty($custom_all_fields)) { ?>
                                        <div class="jobsearch-jobdetail-services">
                                            <ul class="jobsearch-row">
                                                <?php
                                                $cus_fields = array('content' => '');
                                                $cus_fields = apply_filters('jobsearch_custom_fields_list', 'candidate', $candidate_id, $cus_fields, '<li class="jobsearch-column-4">', '</li>');
                                                if (isset($cus_fields['content']) && $cus_fields['content'] != '') {
                                                    echo($cus_fields['content']);
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                        <?php
                                    }
                                }

                                do_action('jobseach_candidate_detail_after_cusfields', $candidate_id);

                                $ad_args = array(
                                    'post_type' => 'candidate',
                                    'view' => 'view1',
                                    'position' => 'b4_desc',
                                );
                                jobsearch_detail_common_ad_code($ad_args);

                                if ($candidate_content != '') {
                                    if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|about_desc', 'detail_page')) {
                                        ob_start();
                                        ?>
                                        <div class="jobsearch-content-title">
                                            <h2><?php esc_html_e('About me', 'wp-jobsearch') ?></h2></div>
                                        <div class="jobsearch-description">
                                            <?php echo jobsearch_esc_wp_editor($candidate_content) ?>
                                        </div>
                                        <?php
                                        $desc_html = ob_get_clean();
                                        echo apply_filters('jobsearch_cand_dash_detel_contnt_html', $desc_html, $candidate_id);
                                    }
                                }

                                $ad_args = array(
                                    'post_type' => 'candidate',
                                    'view' => 'view1',
                                    'position' => 'aftr_desc',
                                );
                                jobsearch_detail_common_ad_code($ad_args);

                                //
                                do_action('jobseach_candidate_detail_after_desctxt', $candidate_id);
                                ?>
                            </div>
                            <?php
                            if (!$cand_profile_restrict::cand_field_is_locked('edu_defields', 'detail_page')) {
                                $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_education_title', true);
                                $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_education_description', true);
                                $education_academyfield_list = get_post_meta($candidate_id, 'jobsearch_field_education_academy', true);
                                $education_yearfield_list = get_post_meta($candidate_id, 'jobsearch_field_education_year', true);
                                $education_start_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_education_start_date', true);
                                $education_end_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_education_end_date', true);
                                $education_prsnt_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_education_date_prsnt', true);

                                $edu_start_metaexist = metadata_exists('post', $candidate_id, 'jobsearch_field_education_start_date');

                                ob_start();
                                if (!empty($exfield_list)) { ?>
                                    <div class="jobsearch-candidate-title"><h2><i
                                                    class="jobsearch-icon jobsearch-mortarboard"></i> <?php esc_html_e('Education', 'wp-jobsearch') ?>
                                        </h2></div>
                                    <div class="jobsearch-candidate-timeline">
                                        <ul class="jobsearch-row">
                                            <?php
                                            $exfield_counter = 0;
                                            foreach ($exfield_list as $exfield) {
                                                $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                                                $education_academyfield_val = isset($education_academyfield_list[$exfield_counter]) ? $education_academyfield_list[$exfield_counter] : '';
                                                $education_yearfield_val = isset($education_yearfield_list[$exfield_counter]) ? $education_yearfield_list[$exfield_counter] : '';
                                                $education_start_datefield_val = isset($education_start_datefield_list[$exfield_counter]) ? $education_start_datefield_list[$exfield_counter] : '';
                                                $education_end_datefield_val = isset($education_end_datefield_list[$exfield_counter]) ? $education_end_datefield_list[$exfield_counter] : '';
                                                $education_prsnt_datefield_val = isset($education_prsnt_datefield_list[$exfield_counter]) ? $education_prsnt_datefield_list[$exfield_counter] : '';
                                                ?>
                                                <li class="jobsearch-column-12">
                                                    <?php
                                                    if ($edu_start_metaexist) {
                                                        if ($education_prsnt_datefield_val == 'on') {
                                                            ?>
                                                            <small><?php echo ($education_start_datefield_val != '' ? date('Y', strtotime($education_start_datefield_val)) : '') . (' - ') . esc_html__('Present', 'wp-jobsearch') ?></small>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <small><?php echo ($education_start_datefield_val != '' ? date('Y', strtotime($education_start_datefield_val)) : '') . ($education_end_datefield_val != '' ? ' - ' . date('Y', strtotime($education_end_datefield_val)) : '') ?></small>
                                                            <?php
                                                        }
                                                    } else { ?>
                                                        <small><?php echo($education_yearfield_val) ?></small>
                                                    <?php } ?>
                                                    <div class="jobsearch-candidate-timeline-text">
                                                        <span><?php echo($education_academyfield_val) ?></span>
                                                        <?php
                                                        echo apply_filters('jobsearch_cand_det_resume_edu_list_aftr_inst', '', $candidate_id, $exfield_counter);
                                                        ?>
                                                        <h2><a><?php echo($exfield) ?></a></h2>
                                                        <p><?php echo($exfield_val) ?></p>
                                                    </div>
                                                </li>
                                                <?php
                                                $exfield_counter++;
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                    <?php
                                }
                                $edu_html = ob_get_clean();

                                if ($inopt_resm_education != 'off') {
                                    echo apply_filters('jobsearch_candidate_detail_education_html', $edu_html, $candidate_id);
                                }
                            }
                            $ad_args = array(
                                'post_type' => 'candidate',
                                'view' => 'view1',
                                'position' => 'aftr_edu',
                            );
                            jobsearch_detail_common_ad_code($ad_args);
                            //
                            if (!$cand_profile_restrict::cand_field_is_locked('exp_defields', 'detail_page')) {
                                ob_start();
                                $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_title', true);
                                $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_experience_description', true);
                                $experience_start_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_start_date', true);
                                $experience_end_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_end_date', true);
                                $experience_prsnt_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_date_prsnt', true);
                                $experience_company_field_list = get_post_meta($candidate_id, 'jobsearch_field_experience_company', true);
                                if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

                                    $exfield_counter = 0;
                                    ?>
                                    <div class="jobsearch-candidate-title">
                                        <h2>
                                            <i class="jobsearch-icon jobsearch-social-media"></i> <?php esc_html_e('Experience', 'wp-jobsearch') ?>
                                        </h2>
                                    </div>
                                    <div class="jobsearch-candidate-timeline">
                                        <ul class="jobsearch-row">
                                            <?php
                                            foreach ($exfield_list as $exfield) {
                                                $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                                                $experience_start_datefield_val = isset($experience_start_datefield_list[$exfield_counter]) ? $experience_start_datefield_list[$exfield_counter] : '';
                                                $experience_end_datefield_val = isset($experience_end_datefield_list[$exfield_counter]) ? $experience_end_datefield_list[$exfield_counter] : '';
                                                $experience_prsnt_datefield_val = isset($experience_prsnt_datefield_list[$exfield_counter]) ? $experience_prsnt_datefield_list[$exfield_counter] : '';
                                                $experience_end_companyfield_val = isset($experience_company_field_list[$exfield_counter]) ? $experience_company_field_list[$exfield_counter] : '';
                                                ?>
                                                <li class="jobsearch-column-12">
                                                    <?php
                                                    if ($experience_prsnt_datefield_val == 'on') {
                                                        ?>
                                                        <small><?php echo ($experience_start_datefield_val != '' ? date('Y', strtotime($experience_start_datefield_val)) : '') . (' - ') . esc_html__('Present', 'wp-jobsearch') ?></small>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <small><?php echo ($experience_start_datefield_val != '' ? date('Y', strtotime($experience_start_datefield_val)) : '') . ($experience_end_datefield_val != '' ? ' - ' . date('Y', strtotime($experience_end_datefield_val)) : '') ?></small>
                                                        <?php
                                                    }
                                                    ?>
                                                    <div class="jobsearch-candidate-timeline-text">
                                                        <span><?php echo($experience_end_companyfield_val) ?></span>
                                                        <?php
                                                        echo apply_filters('jobsearch_cand_det_resume_axpr_list_aftr_comp', '', $candidate_id, $exfield_counter);
                                                        ?>
                                                        <h2><a><?php echo jobsearch_esc_html($exfield) ?></a></h2>
                                                        <p><?php echo jobsearch_esc_html($exfield_val) ?></p>
                                                    </div>
                                                </li>
                                                <?php
                                                $exfield_counter++;
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                    <?php
                                }
                                $expu_html = ob_get_clean();
                                if ($inopt_resm_experience != 'off') {
                                    echo apply_filters('jobsearch_candidate_detail_exprience_html', $expu_html, $candidate_id);
                                }
                            }
                            $ad_args = array(
                                'post_type' => 'candidate',
                                'view' => 'view1',
                                'position' => 'aftr_exp',
                            );
                            jobsearch_detail_common_ad_code($ad_args);
                            if (!$cand_profile_restrict::cand_field_is_locked('expertise_defields', 'detail_page')) {
                                ob_start();
                                $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_skill_title', true);
                                $skill_percentagefield_list = get_post_meta($candidate_id, 'jobsearch_field_skill_percentage', true);
                                if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
                                    ?>
                                    <div class="jobsearch_progressbar_wrap">
                                        <div class="jobsearch-row">
                                            <div class="jobsearch-column-12">
                                                <div class="jobsearch-candidate-title"><h2><i
                                                                class="jobsearch-icon jobsearch-design-skills"></i> <?php esc_html_e('Expertise', 'wp-jobsearch') ?>
                                                    </h2></div>
                                            </div>
                                            <?php
                                            $exfield_counter = 0;
                                            foreach ($exfield_list as $exfield) {
                                                $rand_num = rand(1000000, 99999999);
                                                $skill_percentagefield_val = isset($skill_percentagefield_list[$exfield_counter]) ? absint($skill_percentagefield_list[$exfield_counter]) : '';
                                                $skill_percentagefield_val = $skill_percentagefield_val > 100 ? 100 : $skill_percentagefield_val;
                                                ?>
                                                <div class="jobsearch-column-6">
                                                    <div class="jobsearch_progressbar1"
                                                         data-width='<?php echo($skill_percentagefield_val) ?>'><?php echo($exfield) ?></div>
                                                </div>
                                                <?php
                                                $exfield_counter++;
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <?php
                                }
                                $expertise_html = ob_get_clean();
                                if ($inopt_resm_skills != 'off') {
                                    echo apply_filters('jobsearch_candidate_detail_expertise_html', $expertise_html, $candidate_id);
                                }
                            }
                            $ad_args = array(
                                'post_type' => 'candidate',
                                'view' => 'view1',
                                'position' => 'aftr_expert',
                            );
                            jobsearch_detail_common_ad_code($ad_args);
                            //

                            //
                            ob_start();
                            $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_lang_title', true);
                            $lang_percentagefield_list = get_post_meta($candidate_id, 'jobsearch_field_lang_percentage', true);
                            $lang_level_list = get_post_meta($candidate_id, 'jobsearch_field_lang_level', true);
                            if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
                                ?>
                                <div class="jobsearch_progressbar_wrap jobsearch-candidate-langs">
                                    <div class="jobsearch-row">
                                        <div class="jobsearch-column-12">
                                            <div class="jobsearch-candidate-title">
                                                <h2>
                                                    <i class="jobsearch-icon jobsearch-design-skills"></i> <?php esc_html_e('Languages', 'wp-jobsearch') ?>
                                                </h2>
                                            </div>
                                        </div>
                                        <?php
                                        $exfield_counter = 0;
                                        foreach ($exfield_list as $exfield) {
                                            $rand_num = rand(1000000, 99999999);
                                            $lang_percentagefield_val = isset($lang_percentagefield_list[$exfield_counter]) ? absint($lang_percentagefield_list[$exfield_counter]) : '';
                                            $lang_percentagefield_val = $lang_percentagefield_val > 100 ? 100 : $lang_percentagefield_val;
                                            $lang_level_val = isset($lang_level_list[$exfield_counter]) ? ($lang_level_list[$exfield_counter]) : '';

                                            $lang_level_str = esc_html__('Beginner', 'wp-jobsearch');
                                            if ($lang_level_val == 'proficient') {
                                                $lang_level_str = esc_html__('Proficient', 'wp-jobsearch');
                                            } else if ($lang_level_val == 'intermediate') {
                                                $lang_level_str = esc_html__('Intermediate', 'wp-jobsearch');
                                            }
                                            ?>
                                            <div class="jobsearch-column-4">
                                                <strong><?php echo($exfield) ?></strong>
                                                <div class="jobsearch_progressbar1"
                                                     data-width='<?php echo($lang_percentagefield_val) ?>'><?php echo($lang_level_str) ?></div>
                                            </div>
                                            <?php
                                            $exfield_counter++;
                                        }
                                        ?>
                                    </div>
                                </div>

                                <?php
                            }
                            $languages_html = ob_get_clean();
                            if ($inopt_resm_langs != 'off') {
                                echo apply_filters('jobsearch_candidate_detail_$languages_html', $languages_html, $candidate_id);
                            }
                            //

                            if (!$cand_profile_restrict::cand_field_is_locked('port_defields', 'detail_page')) {
                                ob_start();

                                $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_portfolio_title', true);
                                $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_portfolio_image', true);
                                $exfield_portfolio_url = get_post_meta($candidate_id, 'jobsearch_field_portfolio_url', true);
                                $exfield_portfolio_vurl = get_post_meta($candidate_id, 'jobsearch_field_portfolio_vurl', true);
                                if (is_array($exfield_list) && sizeof($exfield_list) > 0) { ?>
                                    <div class="jobsearch-candidate-title"><h2><i
                                                    class="jobsearch-icon jobsearch-briefcase"></i> <?php esc_html_e('Portfolio', 'wp-jobsearch') ?>
                                        </h2></div>
                                    <div class="jobsearch-gallery candidate_portfolio">
                                        <ul class="jobsearch-row grid">
                                            <?php
                                            $exfield_counter = 0;
                                            foreach ($exfield_list as $exfield) {
                                                $rand_num = rand(1000000, 99999999);

                                                $portfolio_img = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                                                $portfolio_url = isset($exfield_portfolio_url[$exfield_counter]) ? $exfield_portfolio_url[$exfield_counter] : '';
                                                $portfolio_vurl = isset($exfield_portfolio_vurl[$exfield_counter]) ? $exfield_portfolio_vurl[$exfield_counter] : '';

                                                if ($portfolio_vurl != '') {
                                                    if (strpos($portfolio_vurl, 'watch?v=') !== false) {
                                                        $portfolio_vurl = str_replace('watch?v=', 'embed/', $portfolio_vurl);
                                                    }

                                                    if (strpos($portfolio_vurl, '?') !== false) {
                                                        $portfolio_vurl .= '&autoplay=1';
                                                    } else {
                                                        $portfolio_vurl .= '?autoplay=1';
                                                    }
                                                }

                                                $port_thumb_img = jobsearch_get_cand_portimg_url($candidate_id, $portfolio_img, 'large');
                                                ?>
                                                <li class="grid-item <?php echo($exfield_counter == 1 ? 'jobsearch-column-6' : 'jobsearch-column-3') ?>">
                                                    <figure>
                                                        <span class="grid-item-thumb"><small
                                                                    style="background-image: url('<?php echo($port_thumb_img) ?>');"></small></span>
                                                        <figcaption>
                                                            <div class="img-icons">
                                                                <a href="<?php echo($portfolio_vurl != '' ? $portfolio_vurl : $port_thumb_img) ?>"
                                                                   class="<?php echo($portfolio_vurl != '' ? 'fancybox-video' : 'fancybox-galimg') ?>"
                                                                   title="<?php echo($exfield) ?>" <?php echo($portfolio_vurl != '' ? 'data-fancybox-type="iframe"' : '') ?>
                                                                   data-fancybox-group="group"><i
                                                                            class="<?php echo($portfolio_vurl != '' ? 'fa fa-play' : 'fa fa-image') ?>"></i></a>
                                                                <?php
                                                                if ($portfolio_url != '') { ?>
                                                                    <a href="<?php echo($portfolio_url) ?>"
                                                                       target="_blank"><i class="fa fa-chain"></i></a>
                                                                <?php } ?>
                                                            </div>
                                                        </figcaption>
                                                    </figure>
                                                </li>
                                                <?php
                                                $exfield_counter++;
                                            }
                                            ?>
                                        </ul>
                                    </div>

                                    <?php
                                }

                                $ports_html = ob_get_clean();

                                if ($inopt_resm_portfolio != 'off') {
                                    echo apply_filters('jobsearch_candidate_detail_portsfolio_html', $ports_html, $candidate_id);
                                }
                            }
                            $ad_args = array(
                                'post_type' => 'candidate',
                                'view' => 'view1',
                                'position' => 'aftr_port',
                            );
                            jobsearch_detail_common_ad_code($ad_args);
                            if (!$cand_profile_restrict::cand_field_is_locked('awards_defields', 'detail_page')) {
                                if ($inopt_resm_honsawards != 'off') {
                                    $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_award_title', true);
                                    $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_award_description', true);
                                    $award_yearfield_list = get_post_meta($candidate_id, 'jobsearch_field_award_year', true);
                                    if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
                                        ?>
                                        <div class="jobsearch-candidate-title"><h2><i
                                                        class="jobsearch-icon jobsearch-trophy"></i> <?php esc_html_e('Honors & awards', 'wp-jobsearch') ?>
                                            </h2></div>
                                        <div class="jobsearch-candidate-timeline">
                                            <ul class="jobsearch-row">
                                                <?php
                                                $exfield_counter = 0;
                                                foreach ($exfield_list as $exfield) {
                                                    $rand_num = rand(1000000, 99999999);

                                                    $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                                                    $award_yearfield_val = isset($award_yearfield_list[$exfield_counter]) ? $award_yearfield_list[$exfield_counter] : '';
                                                    ?>
                                                    <li class="jobsearch-column-12">
                                                        <small><?php echo($award_yearfield_val) ?></small>
                                                        <div class="jobsearch-candidate-timeline-text">
                                                            <h2><a><?php echo jobsearch_esc_html($exfield) ?></a></h2>
                                                            <p><?php echo jobsearch_esc_html($exfield_val) ?></p>
                                                        </div>
                                                    </li>
                                                    <?php
                                                    $exfield_counter++;
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            $ad_args = array(
                                'post_type' => 'candidate',
                                'view' => 'view1',
                                'position' => 'aftr_awards',
                            );
                            jobsearch_detail_common_ad_code($ad_args);

                            if (!$cand_profile_restrict::cand_field_is_locked('skills_defields', 'detail_page')) {
                                $skills_list = jobsearch_job_get_all_skills($candidate_id, '', '', '', '', '', '', 'candidate');
                                $skills_list = apply_filters('jobsearch_cand_detail_skills_list_html', $skills_list, $candidate_id);
                                ob_start();
                                if ($skills_list != '') {
                                    ?>
                                    <div class="jobsearch-content-title">
                                        <h2><?php echo esc_html__('Skills', 'wp-jobsearch') ?></h2></div>
                                    <div class="jobsearch-jobdetail-tags">
                                        <?php echo($skills_list); ?>
                                    </div>
                                    <?php
                                }
                                $skills_html = ob_get_clean();
                                echo apply_filters('jobsearch_candetail_skills_html_afiltr', $skills_html, $candidate_id);
                            }
                            echo apply_filters('jobsearch_candetail_after_skills_afiltr', '', $candidate_id);

                            if ($candidates_reviews == 'on') {
                                $post_reviews_args = array(
                                    'post_id' => $candidate_id,
                                    'list_label' => esc_html__('Candidate Reviews', 'wp-jobsearch'),
                                );
                                do_action('jobsearch_post_reviews_list', $post_reviews_args);

                                $review_form_args = array(
                                    'post_id' => $candidate_id,
                                    'must_login' => 'no',
                                );
                                do_action('jobsearch_add_review_form', $review_form_args);
                            }
                            ?>
                        </div>
                        <?php
                        echo apply_filters('jobsearch_cand_detail_after_conwraper', '', $candidate_id);
                        ?>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <!-- Main Section -->
</div>