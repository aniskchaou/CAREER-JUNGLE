<?php

use WP_Jobsearch\Package_Limits;

function jobsearch_user_dashboard_sidebar_html()
{
    global $jobsearch_plugin_options, $wpdb, $sitepress;

    $get_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';
    $page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
    $page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
    $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);
    $user_pkg_limits = new Package_Limits;
    $candidate_listing_percent = isset($jobsearch_plugin_options['jobsearch_cand_listpecent']) ? $jobsearch_plugin_options['jobsearch_cand_listpecent'] : '';
    $candidate_skills = isset($jobsearch_plugin_options['jobsearch_candidate_skills']) ? $jobsearch_plugin_options['jobsearch_candidate_skills'] : '';
    $user_id = get_current_user_id();

    $user_obj = get_user_by('ID', $user_id);
    $user_def_avatar_url = get_avatar_url($user_id, array('size' => 132));

    $user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
    $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);

    $user_is_candidate = jobsearch_user_is_candidate($user_id);
    $user_is_employer = jobsearch_user_is_employer($user_id);

    $user_has_cimg = false;
    if ($user_is_employer) {
        $employer_id = jobsearch_get_user_employer_id($user_id);
        $user_avatar_id = get_post_thumbnail_id($employer_id);
        if ($user_avatar_id > 0) {
            $user_has_cimg = true;
            $def_img_size = 'thumbnail';
            $def_img_size = apply_filters('jobsearch_emp_dashside_pimg_size', $def_img_size);
            $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, $def_img_size);
            $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
        }
        $user_def_avatar_url = $user_def_avatar_url == '' ? jobsearch_employer_image_placeholder() : $user_def_avatar_url;
        $user_type = 'emp';
    } else {
        $candidate_id = jobsearch_get_user_candidate_id($user_id);
        $user_avatar_dburl = get_post_meta($candidate_id, 'jobsearch_user_avatar_url', true);
        $user_def_avatar_url = '';
        if (isset($user_avatar_dburl['file_url']) && $user_avatar_dburl['file_url'] != '') {
            $user_has_cimg = true;
        } else {
            $user_avatar_id = get_post_thumbnail_id($candidate_id);
            if ($user_avatar_id > 0) {
                $user_has_cimg = true;
            }
        }
        $user_def_avatar_url = jobsearch_candidate_img_url_comn($candidate_id);
        $user_type = 'cand';
    }
    ob_start();
    ?>
    <aside class="jobsearch-column-3 jobsearch-typo-wrap">
        <div class="jobsearch-typo-wrap">
            <div class="jobsearch-employer-dashboard-nav">
                <?php
                echo apply_filters('jobsearch_indash_side_before_figureimg', '');
                if ($user_is_candidate || $user_is_employer) { ?>
                    <figure>
                        <?php
                        if ($user_is_candidate) {
                            ob_start();
                            if ($candidate_skills == 'on') {
                                ?>
                                <style>
                                    #circle {
                                        width: 150px;
                                        height: 150px;
                                        position: relative;
                                    }

                                    #circle img {
                                        border-radius: 100%;
                                        position: absolute;
                                        left: 9px;
                                        top: 9px;
                                    }
                                </style>
                                <?php
                                wp_enqueue_script('jobsearch-circle-progressbar');
                            }
                            ?>
                            <a href="javascript:void(0);" class="user-dashthumb-remove jobsearch-tooltipcon"
                               title="<?php esc_html_e('Delete', 'wp-jobsearch') ?>"
                               data-uid="<?php echo($user_id) ?>" <?php echo($user_has_cimg ? '' : 'style="display: none;"') ?>><i
                                        class="fa fa-times"></i></a>
                            <a id="com-img-holder" href="<?php echo($page_url) ?>" class="employer-dashboard-thumb">
                                <?php if ($candidate_skills == 'on') { ?>
                                <div id="circle"><?php } ?><img src="<?php echo($user_def_avatar_url) ?>" alt=""
                                                                style="max-width: 132px;"><?php if ($candidate_skills == 'on') { ?>
                                </div><?php } ?>
                            </a>
                            <?php
                            $cand_prfo_photo = ob_get_clean();
                            echo apply_filters('jobsearch_candidate_dash_profile_img_html', $cand_prfo_photo, $page_url, $user_def_avatar_url, $user_has_cimg);
                        } else {
                            ob_start();
                            ?>
                            <a href="javascript:void(0);" class="user-dashthumb-remove jobsearch-tooltipcon"
                               title="<?php esc_html_e('Delete', 'wp-jobsearch') ?>"
                               data-uid="<?php echo($user_id) ?>" <?php echo($user_has_cimg ? '' : 'style="display: none;"') ?>><i
                                        class="fa fa-times"></i></a>
                            <a id="com-img-holder" href="<?php echo($page_url) ?>" class="employer-dashboard-thumb">
                                <img src="<?php echo($user_def_avatar_url) ?>" alt="" style="max-width: 132px;">
                            </a>
                            <?php
                            $emp_prfo_photo = ob_get_clean();
                            echo apply_filters('jobsearch_employer_dash_profile_img_html', $emp_prfo_photo, $page_url, $user_def_avatar_url, $user_has_cimg);
                        }
                        $uplod_txt = '';
                        if ($user_is_candidate) {
                            $uplod_txt = esc_html__('Upload Photo', 'wp-jobsearch');
                            $uplod_txt = apply_filters('jobsearch_dash_side_cand_upload_photobtn_txt', $uplod_txt);
                        } else if ($user_is_employer) {
                            $uplod_txt = esc_html__('Upload Company Logo', 'wp-jobsearch');
                        }

                        ob_start();
                        ?>
                        <figcaption>
                            <?php
                            ob_start();
                            ?>
                            <span class="fileUpLoader"></span>
                            <div class="jobsearch-fileUpload">
                                <span><i class="jobsearch-icon jobsearch-add"></i> <?php echo($uplod_txt) ?></span>
                                <input type="file"
                                       id="<?php echo($user_is_employer ? 'employer_user_avatar' : 'user_avatar') ?>"
                                       name="user_avatar" class="jobsearch-upload">
                            </div>
                            <?php
                            if ($user_is_employer) { ?>
                                <div class="imag-resoultion-msg">
                                    <small><?php esc_html_e('Logo height and width should not be greater than 250x250.', 'wp-jobsearch') ?></small>
                                </div>
                                <?php
                            }
                            $uplbtn_prfo_photo = ob_get_clean();
                            echo apply_filters('jobsearch_emp_dash_profile_imguplodbtn_html', $uplbtn_prfo_photo);
                            ?>
                            <h2><a href="<?php echo($page_url) ?>"><?php echo($user_displayname) ?></a></h2>
                            <?php
                            if ($user_is_candidate) {

                                ob_start();
                                $job_title = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                                ?>
                                <span class="jobsearch-dashboard-subtitle"><?php echo($job_title) ?></span>
                                <?php
                                $job_title_html = ob_get_clean();
                                $job_title_html = apply_filters('jobsearch_candidate_dash_side_job_title_html', $job_title_html, $candidate_id);
                                echo($job_title_html);
                                if ($candidate_skills == 'on') {
                                    $overall_candidate_skills = get_post_meta($candidate_id, 'overall_skills_percentage', true);
                                    ?>
                                    <div class="required-skills-detail">
                                        <?php
                                        $all_skill_msgs = jobsearch_candidate_skill_percent_count($user_id, 'msgs');
                                        if (!empty($all_skill_msgs) && $overall_candidate_skills < 100) {
                                            if (isset($all_skill_msgs[0])) {
                                                ?>
                                                <span class="skills-perc"><?php echo($all_skill_msgs[0]) ?></span>
                                                <?php
                                            }

                                            if (count($all_skill_msgs) > 1) {
                                                ?>
                                                <a id="skill-detail-popup-btn" href="javascript:void(0);"
                                                   class="get-skill-detail-btn"><?php esc_html_e('Complete Profile', 'wp-jobsearch') ?></a>
                                                <?php
                                                $popup_args = array(
                                                    'p_all_skill_msgs' => $all_skill_msgs,
                                                    'p_overall_skills' => $overall_candidate_skills,
                                                );
                                                add_action('wp_footer', function () use ($popup_args) {

                                                    global $jobsearch_plugin_options;
                                                    extract(shortcode_atts(array(
                                                        'p_all_skill_msgs' => '',
                                                        'p_overall_skills' => '',
                                                    ), $popup_args));

                                                    $candidate_min_skill = isset($jobsearch_plugin_options['jobsearch-candidate-skills-percentage']) && $jobsearch_plugin_options['jobsearch-candidate-skills-percentage'] > 0 ? $jobsearch_plugin_options['jobsearch-candidate-skills-percentage'] : 0;
                                                    $p_overall_skills = $p_overall_skills > 0 ? $p_overall_skills : 0;

                                                    $low_skills_clr = isset($jobsearch_plugin_options['skill_low_set_color']) && $jobsearch_plugin_options['skill_low_set_color'] != '' ? $jobsearch_plugin_options['skill_low_set_color'] : '#13b5ea';
                                                    $med_skills_clr = isset($jobsearch_plugin_options['skill_med_set_color']) && $jobsearch_plugin_options['skill_med_set_color'] != '' ? $jobsearch_plugin_options['skill_med_set_color'] : '#13b5ea';
                                                    $high_skills_clr = isset($jobsearch_plugin_options['skill_high_set_color']) && $jobsearch_plugin_options['skill_high_set_color'] != '' ? $jobsearch_plugin_options['skill_high_set_color'] : '#13b5ea';
                                                    $comp_skills_clr = isset($jobsearch_plugin_options['skill_ahigh_set_color']) && $jobsearch_plugin_options['skill_ahigh_set_color'] != '' ? $jobsearch_plugin_options['skill_ahigh_set_color'] : '#13b5ea';

                                                    $final_color = '#13b5ea';
                                                    if ($p_overall_skills <= 25) {
                                                        $final_color = $low_skills_clr;
                                                    } else if ($p_overall_skills > 25 && $p_overall_skills <= 50) {
                                                        $final_color = $med_skills_clr;
                                                    } else if ($p_overall_skills > 50 && $p_overall_skills <= 75) {
                                                        $final_color = $high_skills_clr;
                                                    } else if ($p_overall_skills > 75) {
                                                        $final_color = $comp_skills_clr;
                                                    }
                                                    ?>
                                                    <div class="jobsearch-modal fade" id="JobSearchModalSkillsDetail">
                                                        <div class="modal-inner-area">&nbsp;</div>
                                                        <div class="modal-content-area">
                                                            <div class="modal-box-area">
                                                                <div class="jobsearch-modal-title-box">
                                                                    <h2><?php esc_html_e('Profile Completion', 'wp-jobsearch') ?></h2>
                                                                    <span class="modal-close"><i
                                                                                class="fa fa-times"></i></span>
                                                                </div>
                                                                <div class="jobsearch-skills-set-popup">
                                                                    <div class="profile-completion-con">
                                                                        <div class="complet-percent">
                                                                            <span class="percent-num"
                                                                                  style="color: <?php echo($final_color) ?>;"><?php echo($p_overall_skills) ?>%</span>
                                                                            <div class="percent-bar">
                                                                                <span style="width: <?php echo($p_overall_skills) ?>%; background-color: <?php echo($final_color) ?>;"></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="minimum-percent">
                                                                            <span><?php esc_html_e('Minimum Required', 'wp-jobsearch') ?></span>
                                                                            <small><?php echo($candidate_min_skill) ?>
                                                                                % <?php esc_html_e('to apply job', 'wp-jobsearch') ?></small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="profile-improve-con">
                                                                        <div class="improve-title">
                                                                            <h5><?php esc_html_e('Improve your profile', 'wp-jobsearch') ?></h5>
                                                                        </div>
                                                                        <ul>
                                                                            <?php
                                                                            foreach ($p_all_skill_msgs as $all_skill_msg) {
                                                                                ?>
                                                                                <li><?php echo($all_skill_msg) ?></li>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </ul>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }, 11, 1);
                                            }
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                                echo apply_filters('jobsearch_cand_dash_side_in_figcaption', '', $candidate_id, $user_id);
                            }
                            ?>
                        </figcaption>
                        <?php
                        $cand_prfo_photof = ob_get_clean();
                        echo apply_filters('jobsearch_cand_dash_profile_imgfcaption_html', $cand_prfo_photof, $page_url, $user_displayname);
                        ?>
                    </figure>
                    <?php
                } else {
                    ?>
                    <h2><a><?php echo($user_obj->display_name) ?></a></h2>
                    <?php
                }
                ?>
                <ul>
                    <?php
                    if ($user_is_candidate) {
                        ob_start();
                        $dashmenu_links_cand = isset($jobsearch_plugin_options['cand_dashbord_menu']) ? $jobsearch_plugin_options['cand_dashbord_menu'] : '';
                        $dashmenu_links_cand = apply_filters('jobsearch_cand_dashbord_menu_items_arr', $dashmenu_links_cand);
                        ?>
                        <li<?php echo($get_tab == '' ? ' class="active"' : '') ?>>
                            <a href="<?php echo($page_url) ?>">
                                <i class="jobsearch-icon jobsearch-group"></i>
                                <?php esc_html_e('Dashboard', 'wp-jobsearch') ?>
                            </a>
                        </li>
                        <?php
                        if (!empty($dashmenu_links_cand)) {
                            $cust_dashpages_arr = isset($jobsearch_plugin_options['cand_dashmenu_cuspages']) ? $jobsearch_plugin_options['cand_dashmenu_cuspages'] : '';
                            foreach ($dashmenu_links_cand as $cand_menu_item => $cand_menu_item_switch) {
                                if ($cand_menu_item == 'my_profile' && $cand_menu_item_switch == '1') {
                                    ?>
                                    <li<?php echo($get_tab == 'dashboard-settings' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::cand_field_is_locked('dashtab_fields|my_profile')) {
                                            echo($user_pkg_limits::dashtab_locked_html('dashboard-settings', 'jobsearch-icon jobsearch-user', esc_html__('My Profile', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'dashboard-settings'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-user"></i>
                                                <?php esc_html_e('My Profile', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                } else if ($cand_menu_item == 'my_resume' && $cand_menu_item_switch == '1') {
                                    ?>
                                    <li<?php echo($get_tab == 'my-resume' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::cand_field_is_locked('dashtab_fields|my_resume')) {
                                            echo($user_pkg_limits::dashtab_locked_html('my-resume', 'jobsearch-icon jobsearch-resume', esc_html__('My Resume', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'my-resume'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-resume"></i>
                                                <?php esc_html_e('My Resume', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                } else if ($cand_menu_item == 'fav_jobs' && $cand_menu_item_switch == '1') {
                                    ?>
                                    <li<?php echo($get_tab == 'favourite-jobs' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::cand_field_is_locked('dashtab_fields|fav_jobs')) {
                                            echo($user_pkg_limits::dashtab_locked_html('favourite-jobs', 'jobsearch-icon jobsearch-heart', esc_html__('Favorite Jobs', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'favourite-jobs'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-heart"></i>
                                                <?php esc_html_e('Favorite Jobs', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                } else if ($cand_menu_item == 'cv_manager' && $cand_menu_item_switch == '1') {
                                    ?>
                                    <li<?php echo($get_tab == 'cv-manager' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::cand_field_is_locked('dashtab_fields|cv_manager')) {
                                            echo($user_pkg_limits::dashtab_locked_html('cv-manager', 'jobsearch-icon jobsearch-id-card', esc_html__('CV Manager', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'cv-manager'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-id-card"></i>
                                                <?php esc_html_e('CV Manager', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                } else if ($cand_menu_item == 'applied_jobs' && $cand_menu_item_switch == '1') {
                                    ?>
                                    <li<?php echo($get_tab == 'applied-jobs' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::cand_field_is_locked('dashtab_fields|applied_jobs')) {
                                            echo($user_pkg_limits::dashtab_locked_html('applied-jobs', 'jobsearch-icon jobsearch-briefcase-1', esc_html__('Applied Jobs', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'applied-jobs'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-briefcase-1"></i>
                                                <?php esc_html_e('Applied Jobs', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                } else if ($cand_menu_item == 'packages' && $cand_menu_item_switch == '1') {
                                    ?>
                                    <li<?php echo($get_tab == 'user-packages' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::cand_field_is_locked('dashtab_fields|packages')) {
                                            echo($user_pkg_limits::dashtab_locked_html('user-packages', 'jobsearch-icon jobsearch-credit-card-1', esc_html__('Packages', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'user-packages'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-credit-card-1"></i>
                                                <?php esc_html_e('Packages', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                    if (class_exists('WC_Subscription')) {
                                        ?>
                                        <li<?php echo($get_tab == 'user-subscriptions' ? ' class="active"' : '') ?>>
                                            <a href="<?php echo add_query_arg(array('tab' => 'user-subscriptions'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-business"></i>
                                                <?php esc_html_e('Subscriptions', 'wp-jobsearch') ?>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                } else if ($cand_menu_item == 'transactions' && $cand_menu_item_switch == '1') {
                                    ?>
                                    <li<?php echo($get_tab == 'user-transactions' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::cand_field_is_locked('dashtab_fields|transactions')) {
                                            echo($user_pkg_limits::dashtab_locked_html('user-transactions', 'jobsearch-icon jobsearch-salary', esc_html__('Transactions', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'user-transactions'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-salary"></i>
                                                <?php esc_html_e('Transactions', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                } else if ($cand_menu_item == 'my_emails' && $cand_menu_item_switch == '1') {
                                    ?>
                                    <li<?php echo($get_tab == 'my-emails' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::cand_field_is_locked('dashtab_fields|my_emails')) {
                                            echo($user_pkg_limits::dashtab_locked_html('my_emails', 'jobsearch-icon jobsearch-mail', esc_html__('My Emails', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'my-emails'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-mail"></i>
                                                <?php esc_html_e('My Emails', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                } else if ($cand_menu_item == 'following' && $cand_menu_item_switch == '1') {
                                    ?>
                                    <li<?php echo($get_tab == 'following' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::cand_field_is_locked('dashtab_fields|following')) {
                                            echo($user_pkg_limits::dashtab_locked_html('following', 'fa fa-thumbs-o-up', esc_html__('Following', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'following'), $page_url) ?>">
                                                <i class="fa fa-thumbs-o-up"></i>
                                                <?php esc_html_e('Following', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                } else if ($cand_menu_item == 'change_password' && $cand_menu_item_switch == '1') {
                                    ?>
                                    <li<?php echo($get_tab == 'change-password' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::cand_field_is_locked('dashtab_fields|change_password')) {
                                            echo($user_pkg_limits::dashtab_locked_html('change-password', 'jobsearch-icon jobsearch-multimedia', esc_html__('Change Password', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'change-password'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-multimedia"></i>
                                                <?php esc_html_e('Change Password', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                }
                                $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
                                $post_ids_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta";
                                $post_ids_query .= " ON postmeta.post_id = posts.ID";
                                if (function_exists('icl_object_id')) {
                                    $trans_tble = $wpdb->prefix . 'icl_translations';
                                    $post_ids_query .= " LEFT JOIN {$trans_tble} AS icl_trans";
                                    $post_ids_query .= " ON posts.ID = icl_trans.element_id";
                                }
                                $post_ids_query .= " WHERE post_type='dashb_menu' AND post_status='publish'";
                                if (function_exists('icl_object_id')) {
                                    $post_ids_query .= " AND icl_trans.language_code='" . $sitepress->get_current_language() . "'";
                                }
                                $post_ids_query .= " AND ((postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='cand') OR (postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='both'));";

                                $cust_dashpages_arr = $wpdb->get_col($post_ids_query);
                                if (!empty($cust_dashpages_arr)) {
                                    foreach ($cust_dashpages_arr as $cust_dashpage) {
                                        $the_page = get_post($cust_dashpage);
                                        if (isset($the_page->post_name) && $cand_menu_item == $the_page->post_name && $cand_menu_item_switch == '1') {
                                            $cuspage_id = $the_page->ID;
                                            $menu_post_name = $the_page->post_name;
                                            $menu_post_title = $the_page->post_title;
                                            $field_icon_arr = get_post_meta($cuspage_id, 'jobsearch_field_dashmenu_icon', true);
                                            $menu_icon_class = 'fa fa-link';
                                            if (isset($field_icon_arr['icon']) && $field_icon_arr['icon'] != '') {
                                                $menu_icon_class = $field_icon_arr['icon'];
                                            }
                                            ?>
                                            <li>
                                                <?php
                                                if ($user_pkg_limits::cand_field_is_locked('dashtab_fields|' . $menu_post_name)) {
                                                    echo($user_pkg_limits::dashtab_locked_html($menu_post_name, $menu_icon_class, $menu_post_title));
                                                } else {
                                                    //
                                                    $cusmenu_type = get_post_meta($cuspage_id, 'jobsearch_field_menu_type', true);
                                                    $cusmenu_url = add_query_arg(array('tab' => 'cust-' . $menu_post_name), $page_url);
                                                    if ($cusmenu_type == 'url') {
                                                        $cusmenu_url = get_post_meta($cuspage_id, 'jobsearch_field_menu_exturl', true);
                                                    }
                                                    ?>
                                                    <a href="<?php echo($cusmenu_url) ?>">
                                                        <i class="<?php echo($menu_icon_class) ?>"></i>
                                                        <?php echo($menu_post_title) ?>
                                                    </a>
                                                    <?php
                                                }
                                                ?>
                                            </li>
                                            <?php
                                        }
                                    }
                                }
                                echo apply_filters("jobsearch_cand_menudash_link_{$cand_menu_item}_item", '', $cand_menu_item, $get_tab, $page_url, $candidate_id);
                            }
                        } else {
                            ?>
                            <li<?php echo($get_tab == 'dashboard-settings' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'dashboard-settings'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-user"></i>
                                    <?php esc_html_e('My Profile', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <li<?php echo($get_tab == 'my-resume' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'my-resume'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-resume"></i>
                                    <?php esc_html_e('My Resume', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <li<?php echo($get_tab == 'applied-jobs' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'applied-jobs'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-briefcase-1"></i>
                                    <?php esc_html_e('Applied Jobs', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <li<?php echo($get_tab == 'cv-manager' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'cv-manager'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-id-card"></i>
                                    <?php esc_html_e('CV Manager', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <li<?php echo($get_tab == 'favourite-jobs' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'favourite-jobs'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-heart"></i>
                                    <?php esc_html_e('Favorite Jobs', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <?php
                            ob_start();
                            ?>
                            <li<?php echo($get_tab == 'user-packages' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'user-packages'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-credit-card-1"></i>
                                    <?php esc_html_e('Packages', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <?php
                            if (class_exists('WC_Subscription')) {
                                ?>
                                <li<?php echo($get_tab == 'user-subscriptions' ? ' class="active"' : '') ?>>
                                    <a href="<?php echo add_query_arg(array('tab' => 'user-subscriptions'), $page_url) ?>">
                                        <i class="jobsearch-icon jobsearch-business"></i>
                                        <?php esc_html_e('Subscriptions', 'wp-jobsearch') ?>
                                    </a>
                                </li>
                                <?php
                            }
                            ?>
                            <li<?php echo($get_tab == 'user-transactions' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'user-transactions'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-salary"></i>
                                    <?php esc_html_e('Transactions', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <?php
                            $pkgtrans_html = ob_get_clean();
                            echo apply_filters('jobsearch_user_dash_links_pkgtrans_html', $pkgtrans_html, $get_tab, $page_url);
                            ?>
                            <?php echo apply_filters('jobsearch_dashboard_menu_items_ext', '', $get_tab, $page_url) ?>
                            <li<?php echo($get_tab == 'change-password' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'change-password'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-multimedia"></i>
                                    <?php esc_html_e('Change Password', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <?php
                        }
                        $menu_items_html = ob_get_clean();
                        echo apply_filters('jobsearch_cand_dash_side_menulinks_html', $menu_items_html, $get_tab, $page_url, $candidate_id);
                    }

                    if (jobsearch_user_isemp_member($user_id)) {

                        $membusr_perms = jobsearch_emp_accmember_perms($user_id);
                        ob_start();
                        ?>
                        <li<?php echo($get_tab == '' || $get_tab == 'dashboard-settings' ? ' class="active"' : '') ?>>
                            <a href="<?php echo($page_url) ?>">
                                <i class="jobsearch-icon jobsearch-group"></i>
                                <?php esc_html_e('Dashboard', 'wp-jobsearch') ?>
                            </a>
                        </li>
                        <?php
                        if (is_array($membusr_perms) && in_array('u_post_job', $membusr_perms)) {
                            ?>
                            <li<?php echo($get_tab == 'user-job' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'user-job'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-plus"></i>
                                    <?php esc_html_e('Post a New Job', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <?php
                        }
                        if (is_array($membusr_perms) && in_array('u_manage_jobs', $membusr_perms)) {
                            ?>
                            <li<?php echo($get_tab == 'manage-jobs' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'manage-jobs'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-briefcase-1"></i>
                                    <?php esc_html_e('Manage Jobs', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <?php
                        }
                        if (is_array($membusr_perms) && in_array('u_saved_cands', $membusr_perms)) {
                            ?>
                            <li<?php echo($get_tab == 'user-resumes' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'user-resumes'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-heart"></i>
                                    <?php esc_html_e('Saved Candidates', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <?php
                        }
                        if (is_array($membusr_perms) && in_array('u_packages', $membusr_perms)) {
                            ?>
                            <li<?php echo($get_tab == 'user-packages' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'user-packages'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-credit-card-1"></i>
                                    <?php esc_html_e('Packages', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <?php
                            if (class_exists('WC_Subscription')) {
                                ?>
                                <li<?php echo($get_tab == 'user-subscriptions' ? ' class="active"' : '') ?>>
                                    <a href="<?php echo add_query_arg(array('tab' => 'user-subscriptions'), $page_url) ?>">
                                        <i class="jobsearch-icon jobsearch-business"></i>
                                        <?php esc_html_e('Subscriptions', 'wp-jobsearch') ?>
                                    </a>
                                </li>
                                <?php
                            }
                        }
                        if (is_array($membusr_perms) && in_array('u_transactions', $membusr_perms)) {
                            ?>
                            <li<?php echo($get_tab == 'user-transactions' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'user-transactions'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-salary"></i>
                                    <?php esc_html_e('Transactions', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                        <li<?php echo($get_tab == 'change-password' ? ' class="active"' : '') ?>>
                            <a href="<?php echo add_query_arg(array('tab' => 'change-password'), $page_url) ?>">
                                <i class="jobsearch-icon jobsearch-multimedia"></i>
                                <?php esc_html_e('Change Password', 'wp-jobsearch') ?>
                            </a>
                        </li>
                        <?php
                        $menu_items_html = ob_get_clean();
                        echo apply_filters('jobsearch_emp_accmemb_dash_side_menulinks_html', $menu_items_html, $get_tab, $page_url);
                    }
                    if ($user_is_employer) {
                        $dashmenu_links_emp = isset($jobsearch_plugin_options['emp_dashbord_menu']) ? $jobsearch_plugin_options['emp_dashbord_menu'] : '';
                        $dashmenu_links_emp = apply_filters('jobsearch_emp_dashbord_menu_items_arr', $dashmenu_links_emp);
                        ob_start();

                        $dashbord_menu_itm = '
                        <li' . ($get_tab == '' ? ' class="active"' : '') . '>
                            <a href="' . ($page_url) . '">
                                <i class="jobsearch-icon jobsearch-group"></i>
                                ' . esc_html__('Dashboard', 'wp-jobsearch') . '
                            </a>
                        </li>';
                        echo apply_filters('jobsearch_emp_dash_first_menulinks_html', $dashbord_menu_itm, $get_tab, $page_url);

                        if (!empty($dashmenu_links_emp)) {
                            $cust_dashpages_arr = isset($jobsearch_plugin_options['emp_dashmenu_cuspages']) ? $jobsearch_plugin_options['emp_dashmenu_cuspages'] : '';
                            foreach ($dashmenu_links_emp as $emp_menu_item => $emp_menu_item_switch) {
                                if ($emp_menu_item == 'company_profile' && $emp_menu_item_switch == '1') {
                                    ?>
                                    <li<?php echo($get_tab == 'dashboard-settings' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::emp_field_is_locked('dashtab_fields|company_profile')) {
                                            echo($user_pkg_limits::dashtab_locked_html('dashboard-settings', 'jobsearch-icon jobsearch-user', esc_html__('Company Profile', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'dashboard-settings'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-user"></i>
                                                <?php esc_html_e('Company Profile', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                } else if ($emp_menu_item == 'post_new_job' && $emp_menu_item_switch == '1') {
                                    ?>
                                    <li<?php echo($get_tab == 'user-job' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::emp_field_is_locked('dashtab_fields|post_new_job')) {
                                            echo($user_pkg_limits::dashtab_locked_html('user-job', 'jobsearch-icon jobsearch-plus', esc_html__('Post a New Job', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'user-job'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-plus"></i>
                                                <?php esc_html_e('Post a New Job', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                <?php } else if ($emp_menu_item == 'manage_jobs' && $emp_menu_item_switch == '1') { ?>
                                    <li<?php echo($get_tab == 'manage-jobs' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::emp_field_is_locked('dashtab_fields|manage_jobs')) {
                                            echo($user_pkg_limits::dashtab_locked_html('manage-jobs', 'jobsearch-icon jobsearch-briefcase-1', esc_html__('Manage Jobs', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'manage-jobs'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-briefcase-1"></i>
                                                <?php esc_html_e('Manage Jobs', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                <?php } else if ($emp_menu_item == 'all_applicants' && $emp_menu_item_switch == '1') { ?>
                                    <li<?php echo($get_tab == 'all-applicants' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::emp_field_is_locked('dashtab_fields|all_applicants')) {
                                            echo($user_pkg_limits::dashtab_locked_html('all-applicants', 'jobsearch-icon jobsearch-company-workers', esc_html__('All Applicants', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'all-applicants'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-company-workers"></i>
                                                <?php esc_html_e('All Applicants', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                } else if ($emp_menu_item == 'saved_candidates' && $emp_menu_item_switch == '1') {
                                    ?>
                                    <li<?php echo($get_tab == 'user-resumes' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::emp_field_is_locked('dashtab_fields|saved_candidates')) {
                                            echo($user_pkg_limits::dashtab_locked_html('user-resumes', 'jobsearch-icon jobsearch-heart', esc_html__('Saved Candidates', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'user-resumes'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-heart"></i>
                                                <?php esc_html_e('Saved Candidates', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                } else if ($emp_menu_item == 'packages' && $emp_menu_item_switch == '1') {
                                    ?>
                                    <li<?php echo($get_tab == 'user-packages' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::emp_field_is_locked('dashtab_fields|packages')) {
                                            echo($user_pkg_limits::dashtab_locked_html('user-packages', 'jobsearch-icon jobsearch-credit-card-1', esc_html__('Packages', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'user-packages'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-credit-card-1"></i>
                                                <?php esc_html_e('Packages', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                    if (class_exists('WC_Subscription')) {
                                        ?>
                                        <li<?php echo($get_tab == 'user-subscriptions' ? ' class="active"' : '') ?>>
                                            <a href="<?php echo add_query_arg(array('tab' => 'user-subscriptions'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-business"></i>
                                                <?php esc_html_e('Subscriptions', 'wp-jobsearch') ?>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                } else if ($emp_menu_item == 'transactions' && $emp_menu_item_switch == '1') {
                                    ?>
                                    <li<?php echo($get_tab == 'user-transactions' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::emp_field_is_locked('dashtab_fields|transactions')) {
                                            echo($user_pkg_limits::dashtab_locked_html('user-transactions', 'jobsearch-icon jobsearch-salary', esc_html__('Transactions', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'user-transactions'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-salary"></i>
                                                <?php esc_html_e('Transactions', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                } else if ($emp_menu_item == 'my_emails' && $emp_menu_item_switch == '1') {
                                    ?>
                                    <li<?php echo($get_tab == 'my-emails' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::emp_field_is_locked('dashtab_fields|my_emails')) {
                                            echo($user_pkg_limits::dashtab_locked_html('my_emails', 'jobsearch-icon jobsearch-mail', esc_html__('My Emails', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'my-emails'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-mail"></i>
                                                <?php esc_html_e('My Emails', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                } else if ($emp_menu_item == 'followers' && $emp_menu_item_switch == '1') {
                                    ?>
                                    <li<?php echo($get_tab == 'followers' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::emp_field_is_locked('dashtab_fields|followers')) {
                                            echo($user_pkg_limits::dashtab_locked_html('followers', 'fa fa-thumbs-o-up', esc_html__('Followers', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'followers'), $page_url) ?>">
                                                <i class="fa fa-thumbs-o-up"></i>
                                                <?php esc_html_e('Followers', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                } else if ($emp_menu_item == 'change_password' && $emp_menu_item_switch == '1') {
                                    ?>
                                    <li<?php echo($get_tab == 'change-password' ? ' class="active"' : '') ?>>
                                        <?php
                                        if ($user_pkg_limits::emp_field_is_locked('dashtab_fields|change_password')) {
                                            echo($user_pkg_limits::dashtab_locked_html('change-password', 'jobsearch-icon jobsearch-multimedia', esc_html__('Change Password', 'wp-jobsearch')));
                                        } else {
                                            ?>
                                            <a href="<?php echo add_query_arg(array('tab' => 'change-password'), $page_url) ?>">
                                                <i class="jobsearch-icon jobsearch-multimedia"></i>
                                                <?php esc_html_e('Change Password', 'wp-jobsearch') ?>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                }
                                $post_ids_query = "SELECT ID FROM $wpdb->posts AS posts";
                                $post_ids_query .= " INNER JOIN {$wpdb->postmeta} AS postmeta";
                                $post_ids_query .= " ON postmeta.post_id = posts.ID";
                                if (function_exists('icl_object_id')) {
                                    $trans_tble = $wpdb->prefix . 'icl_translations';
                                    $post_ids_query .= " LEFT JOIN {$trans_tble} AS icl_trans";
                                    $post_ids_query .= " ON posts.ID = icl_trans.element_id";
                                }
                                $post_ids_query .= " WHERE post_type='dashb_menu' AND post_status='publish'";
                                if (function_exists('icl_object_id')) {
                                    $post_ids_query .= " AND icl_trans.language_code='" . $sitepress->get_current_language() . "'";
                                }
                                $post_ids_query .= " AND ((postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='emp') OR (postmeta.meta_key='jobsearch_field_menu_user_type' AND postmeta.meta_value='both'));";

                                $cust_dashpages_arr = $wpdb->get_col($post_ids_query);
                                if (!empty($cust_dashpages_arr)) {
                                    foreach ($cust_dashpages_arr as $cust_dashpage) {
                                        $the_page = get_post($cust_dashpage);
                                        if (isset($the_page->post_name) && $emp_menu_item == $the_page->post_name && $emp_menu_item_switch == '1') {
                                            $cuspage_id = $the_page->ID;
                                            $menu_post_name = $the_page->post_name;
                                            $menu_post_title = $the_page->post_title;
                                            $field_icon_arr = get_post_meta($cuspage_id, 'jobsearch_field_dashmenu_icon', true);
                                            $menu_icon_class = 'fa fa-link';
                                            if (isset($field_icon_arr['icon']) && $field_icon_arr['icon'] != '') {
                                                $menu_icon_class = $field_icon_arr['icon'];
                                            }
                                            ?>
                                            <li>
                                                <?php
                                                if ($user_pkg_limits::emp_field_is_locked('dashtab_fields|' . $menu_post_name)) {
                                                    echo($user_pkg_limits::dashtab_locked_html($menu_post_name, $menu_icon_class, $menu_post_title));
                                                } else {
                                                    //
                                                    $cusmenu_type = get_post_meta($cuspage_id, 'jobsearch_field_menu_type', true);
                                                    $cusmenu_url = add_query_arg(array('tab' => 'cust-' . $menu_post_name), $page_url);
                                                    if ($cusmenu_type == 'url') {
                                                        $cusmenu_url = get_post_meta($cuspage_id, 'jobsearch_field_menu_exturl', true);
                                                    }
                                                    ?>
                                                    <a href="<?php echo($cusmenu_url) ?>">
                                                        <i class="<?php echo($menu_icon_class) ?>"></i>
                                                        <?php echo($menu_post_title) ?>
                                                    </a>
                                                    <?php
                                                }
                                                ?>
                                            </li>
                                            <?php
                                        }
                                    }
                                }
                                echo apply_filters("jobsearch_emp_menudash_link_{$emp_menu_item}_item", '', $emp_menu_item, $get_tab, $page_url, $employer_id);
                            }
                        } else {
                            ?>
                            <li<?php echo($get_tab == 'dashboard-settings' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'dashboard-settings'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-user"></i>
                                    <?php esc_html_e('Company Profile', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <li<?php echo($get_tab == 'user-job' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'user-job'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-plus"></i>
                                    <?php esc_html_e('Post a New Job', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <li<?php echo($get_tab == 'manage-jobs' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'manage-jobs'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-briefcase-1"></i>
                                    <?php esc_html_e('Manage Jobs', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <li<?php echo($get_tab == 'all-applicants' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'all-applicants'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-company-workers"></i>
                                    <?php esc_html_e('All Applicants', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <li<?php echo($get_tab == 'user-resumes' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'user-resumes'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-heart"></i>
                                    <?php esc_html_e('Saved Candidates', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <?php
                            ob_start();
                            ?>
                            <li<?php echo($get_tab == 'user-packages' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'user-packages'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-credit-card-1"></i>
                                    <?php esc_html_e('Packages', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <?php
                            if (class_exists('WC_Subscription')) {
                                ?>
                                <li<?php echo($get_tab == 'user-subscriptions' ? ' class="active"' : '') ?>>
                                    <a href="<?php echo add_query_arg(array('tab' => 'user-subscriptions'), $page_url) ?>">
                                        <i class="jobsearch-icon jobsearch-business"></i>
                                        <?php esc_html_e('Subscriptions', 'wp-jobsearch') ?>
                                    </a>
                                </li>
                                <?php
                            }
                            ?>
                            <li<?php echo($get_tab == 'user-transactions' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'user-transactions'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-salary"></i>
                                    <?php esc_html_e('Transactions', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <?php
                            $pkgtrans_html = ob_get_clean();
                            echo apply_filters('jobsearch_user_dash_links_pkgtrans_html', $pkgtrans_html, $get_tab, $page_url);
                            ?>
                            <?php echo apply_filters('jobsearch_dashboard_menu_items_ext', '', $get_tab, $page_url) ?>
                            <li<?php echo($get_tab == 'change-password' ? ' class="active"' : '') ?>>
                                <a href="<?php echo add_query_arg(array('tab' => 'change-password'), $page_url) ?>">
                                    <i class="jobsearch-icon jobsearch-multimedia"></i>
                                    <?php esc_html_e('Change Password', 'wp-jobsearch') ?>
                                </a>
                            </li>
                            <?php
                        }
                        $menu_items_html = ob_get_clean();
                        echo apply_filters('jobsearch_emp_dash_side_menulinks_html', $menu_items_html, $get_tab, $page_url, $employer_id);
                    }
                    echo apply_filters('jobsearch_dash_menu_links_apend_after', '', $get_tab, $page_url);
                    ?>
                    <li>
                        <a href="<?php echo wp_logout_url(home_url('/')); ?>">
                            <i class="jobsearch-icon jobsearch-logout"></i>
                            <?php esc_html_e('Logout', 'wp-jobsearch') ?>
                        </a>
                    </li>
                    <?php
                    $user_delprofile_switch = isset($jobsearch_plugin_options['user_delprofile_switch']) ? $jobsearch_plugin_options['user_delprofile_switch'] : '';
                    if ($user_delprofile_switch == 'on') {
                        ob_start();
                        ?>
                        <li class="profile-del-btnlink">
                            <a class="jobsearch-userdel-profilebtn" href="javascript:void(0);"><i
                                        class="fa fa-trash-o"></i><?php esc_html_e('Delete Profile', 'wp-jobsearch') ?>
                            </a>
                        </li>
                        <?php
                        $delbtn_html = ob_get_clean();
                        echo apply_filters('jobsearch_user_dash_side_delprofile_btn', $delbtn_html);
                    }
                    ?>
                </ul>
                <?php
                $popup_args = array('p_user_type' => $user_type);
                add_action('wp_footer', function () use ($popup_args) {

                    extract(shortcode_atts(array(
                        'p_user_type' => '',
                    ), $popup_args));
                    ?>
                    <div class="jobsearch-modal fade" id="JobSearchModalUserProfileDel">
                        <div class="modal-inner-area">&nbsp;</div>
                        <div class="modal-content-area">
                            <div class="modal-box-area">
                                <span class="modal-close"><i class="fa fa-times"></i></span>
                                <div class="jobsearch-user-profiledel-pop">
                                    <p class="conf-msg"><?php esc_html_e('Are you sure! You want to delete your profile.', 'wp-jobsearch') ?></p>
                                    <p class="undone-msg"><?php esc_html_e('This can\'t be undone!', 'wp-jobsearch') ?></p>
                                    <div class="profile-del-con">
                                        <div class="pass-user-ara">
                                            <p><?php esc_html_e('Please enter your login Password to confirm', 'wp-jobsearch') ?>
                                                :</p>
                                            <input id="d_user_pass" type="password" placeholder="<?php esc_html_e('Password', 'wp-jobsearch') ?>">
                                            <i class="jobsearch-icon jobsearch-multimedia"></i>
                                        </div>
                                        <div class="del-action-btns">
                                            <a class="jobsearch-userdel-profile" href="javascript:void(0);"
                                               data-type="<?php echo($p_user_type) ?>"><?php esc_html_e('Delete Profile', 'wp-jobsearch') ?></a>
                                            <a class="jobsearch-userdel-cancel modal-close"
                                               href="javascript:void(0);"><?php esc_html_e('Cancel', 'wp-jobsearch') ?></a>
                                        </div>
                                        <span class="loader-con"></span>
                                        <span class="msge-con"></span>
                                    </div>
                                    <?php
                                    ob_start();
                                    jobsearch_terms_and_con_link_txt();
                                    $terms_html = ob_get_clean();
                                    echo apply_filters('jobsearch_dash_delprofile_terms_html', $terms_html);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }, 11, 1);
                if ($user_is_candidate && $candidate_skills == 'on') {
                    //
                    $overall_candidate_skills = get_post_meta($candidate_id, 'overall_skills_percentage', true);
                    $overall_skills_perc = 0;
                    if ($overall_candidate_skills > 0) {
                        $overall_skills_perc = $overall_candidate_skills / 100;
                    }
                    //
                    $low_skills_clr = isset($jobsearch_plugin_options['skill_low_set_color']) && $jobsearch_plugin_options['skill_low_set_color'] != '' ? $jobsearch_plugin_options['skill_low_set_color'] : '#13b5ea';
                    $med_skills_clr = isset($jobsearch_plugin_options['skill_med_set_color']) && $jobsearch_plugin_options['skill_med_set_color'] != '' ? $jobsearch_plugin_options['skill_med_set_color'] : '#13b5ea';
                    $high_skills_clr = isset($jobsearch_plugin_options['skill_high_set_color']) && $jobsearch_plugin_options['skill_high_set_color'] != '' ? $jobsearch_plugin_options['skill_high_set_color'] : '#13b5ea';
                    $comp_skills_clr = isset($jobsearch_plugin_options['skill_ahigh_set_color']) && $jobsearch_plugin_options['skill_ahigh_set_color'] != '' ? $jobsearch_plugin_options['skill_ahigh_set_color'] : '#13b5ea';

                    $final_color = '#13b5ea';
                    if ($overall_candidate_skills <= 25) {
                        $final_color = $low_skills_clr;
                    } else if ($overall_candidate_skills > 25 && $overall_candidate_skills <= 50) {
                        $final_color = $med_skills_clr;
                    } else if ($overall_candidate_skills > 50 && $overall_candidate_skills <= 75) {
                        $final_color = $high_skills_clr;
                    } else if ($overall_candidate_skills > 75) {
                        $final_color = $comp_skills_clr;
                    }
                    ?>
                    <script>
                        jQuery(document).ready(function () {
                            var bar = new ProgressBar.Circle(circle, {
                                color: '<?php echo($final_color) ?>',
                                trailColor: '#f7f7f7',
                                trailWidth: 4,
                                duration: 1400,
                                strokeWidth: 4,
                                from: {color: '<?php echo($final_color) ?>', a: 0},
                                to: {color: '<?php echo($final_color) ?>', a: 1},
                                // Set default step function for all animate calls
                                step: function (state, circle) {
                                    circle.path.setAttribute('stroke', state.color);
                                    var value = Math.round(circle.value() * 100);
                                    if (value === 0) {
                                        circle.setText('');
                                    } else {
                                        circle.setText(value + '%');
                                    }
                                }
                            });

                            bar.animate(<?php echo($overall_skills_perc) ?>);  // Number from 0.0 to 1.0
                            bar.text.style.left = '0';
                            bar.text.style.right = '80%';
                            bar.text.style.top = '5%';
                            bar.text.style.bottom = '100%';
                            bar.text.style.color = '<?php echo($final_color) ?>';
                            bar.text.style.fontSize = '16px';
                            bar.text.style.fontWeight = 'bold';
                        });
                    </script>
                    <?php
                }
                ?>
            </div>

        </div>
        <?php
        echo apply_filters('jobsearch_dash_aside_endext_html', '');
        ?>
    </aside>
    <?php
    $html = ob_get_clean();
    echo($html);
}