<?php

use WP_Jobsearch\Package_Limits;

add_filter('careerfy_mobile_hderstrip_btns_bfore_html', 'jobsearch_user_mobile_account_hder_btn', 15);

function jobsearch_user_mobile_account_hder_btn($html)
{
    global $jobsearch_plugin_options;
    $menu_links = isset($jobsearch_plugin_options['user_login_myacount_btns']) ? $jobsearch_plugin_options['user_login_myacount_btns'] : '';
    ob_start();

    $btn_class = 'jobsearch-toggle-dashmenu';
    if (!is_user_logged_in()) {
        $btn_class = 'jobsearch-open-signin-tab';
    }
    if ($menu_links == 'on') {
        ?>
        <a href="javascript:void(0);" class="jobsearch-useracount-hdrbtn <?php echo($btn_class) ?>"><i class="fa fa-user"></i></a>
        <?php
    }
    $html .= ob_get_clean();

    return $html;
}

function jobsearch_user_account_linkitems($user_pkg_limits, $page_url, $get_tab) {

    global $jobsearch_plugin_options, $careerfy_framework_options, $wpdb, $sitepress;

    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);
        if ($user_is_employer) {
            $employer_id = jobsearch_get_user_employer_id($user_id);
        }
        if ($user_is_candidate) {
            $candidate_id = jobsearch_get_user_candidate_id($user_id);
        }
        if (jobsearch_user_isemp_member($user_id)) {

            $membusr_perms = jobsearch_emp_accmember_perms($user_id);
            ob_start();
            ?>
            <li<?php echo($get_tab == '' || $get_tab == 'dashboard-settings' ? 'class="active"' : '') ?>>
                <a href="<?php echo($page_url) ?>">
                    <i class="jobsearch-icon jobsearch-group"></i>
                    <?php esc_html_e('Dashboard', 'wp-jobsearch') ?>
                </a>
            </li>
            <?php
            if (is_array($membusr_perms) && in_array('u_post_job', $membusr_perms)) { ?>
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
                        <?php
                    } else if ($emp_menu_item == 'manage_jobs' && $emp_menu_item_switch == '1') {
                        ?>
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
                    <?php } else if ($emp_menu_item == 'saved_candidates' && $emp_menu_item_switch == '1') { ?>
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
                    <?php } else if ($emp_menu_item == 'packages' && $emp_menu_item_switch == '1') { ?>
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
                    $post_ids_query .= " GROUP BY posts.ID";
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
            } else { ?>
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
                <li<?php echo($get_tab == 'favourite-jobs' ? ' class="active"' : '') ?>>
                    <a href="<?php echo add_query_arg(array('tab' => 'favourite-jobs'), $page_url) ?>">
                        <i class="jobsearch-icon jobsearch-heart"></i>
                        <?php esc_html_e('Favorite Jobs', 'wp-jobsearch') ?>
                    </a>
                </li>
                <li<?php echo($get_tab == 'cv-manager' ? ' class="active"' : '') ?>>
                    <a href="<?php echo add_query_arg(array('tab' => 'cv-manager'), $page_url) ?>">
                        <i class="jobsearch-icon jobsearch-id-card"></i>
                        <?php esc_html_e('CV Manager', 'wp-jobsearch') ?>
                    </a>
                </li>
                <li<?php echo($get_tab == 'applied-jobs' ? ' class="active"' : '') ?>>
                    <a href="<?php echo add_query_arg(array('tab' => 'applied-jobs'), $page_url) ?>">
                        <i class="jobsearch-icon jobsearch-briefcase-1"></i>
                        <?php esc_html_e('Applied Jobs', 'wp-jobsearch') ?>
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
                <?php if (class_exists('WC_Subscription')) { ?>
                    <li<?php echo($get_tab == 'user-subscriptions' ? ' class="active"' : '') ?>>
                        <a href="<?php echo add_query_arg(array('tab' => 'user-subscriptions'), $page_url) ?>">
                            <i class="jobsearch-icon jobsearch-business"></i>
                            <?php esc_html_e('Subscriptions', 'wp-jobsearch') ?>
                        </a>
                    </li>
                <?php } ?>
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
        echo apply_filters('jobsearch_dash_menu_links_apend_after', '', $get_tab, $page_url);
        ?>
        <li>
            <a href="<?php echo wp_logout_url(home_url('/')); ?>">
                <i class="jobsearch-icon jobsearch-logout"></i>
                <?php esc_html_e('Logout', 'wp-jobsearch') ?>
            </a>
        </li>
        <?php
    }
}

add_action('jobsearch_user_account_links', 'jobsearch_user_account_links', 10, 1);

function jobsearch_user_account_links($args = array())
{

    global $jobsearch_plugin_options, $careerfy_framework_options, $wpdb;

    $user_pkg_limits = new Package_Limits;

    $login_myacount_btns = isset($jobsearch_plugin_options['user_login_myacount_btns']) ? $jobsearch_plugin_options['user_login_myacount_btns'] : '';
    if ($login_myacount_btns != 'off') {
        //
        echo apply_filters('jobsearch_dashmenu_account_btns_before_items', '', $args);

        $is_popup = isset($args['is_popup']) ? $args['is_popup'] : '';
        $header_style = isset($careerfy_framework_options['header-style']) ? $careerfy_framework_options['header-style'] : '';
        $active_link_class = isset($header_style) && $header_style == 'style12' ? 'careerfy-headertwelve-btn' : '';
        //$account_link_text = isset($header_style) && $header_style == 'style12' ? 'Register / Sign In' : 'Sign In';
        $account_register_icon = isset($header_style) && $header_style == 'style21' ? '<i class="careerfy-icon careerfy-user"></i>' : '';
        $account_link_btn_color_class = isset($header_style) && $header_style != 'style12' && $header_style != 'style22' ? 'jobsearch-color' : '';


        $jobsearch_login_page = isset($jobsearch_plugin_options['user-login-template-page']) ? $jobsearch_plugin_options['user-login-template-page'] : '';
        $jobsearch_login_page = jobsearch__get_post_id($jobsearch_login_page, 'page');

        $jobsearch_registr_page = isset($jobsearch_plugin_options['userreg-template-page']) ? $jobsearch_plugin_options['userreg-template-page'] : '';
        $jobsearch_registr_page = jobsearch__get_post_id($jobsearch_registr_page, 'page');
        //var_dump($jobsearch_registr_page);

        $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');

        $page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
        //$page_url = get_permalink($page_id);
        $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page');

        $get_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';
        //
        if ($header_style == 'style12') {
            $account_link_text = esc_html__('Register / Sign In', 'wp-jobsearch');
        } else if ($header_style == 'style19' || $header_style == 'style20') {
            $account_link_text = esc_html__("Sign Up Free", "wp-jobsearch");
        } else if ($header_style == 'style22') {
            $account_link_text = esc_html__('Login', 'wp-jobsearch');
        } else {
            $account_link_text = esc_html__('Sign In', 'wp-jobsearch');
        }


        if ($header_style == 'style12') {
            $account_link_icon = '<i class="careerfy-icon careerfy-user-1"></i>';
        } else if ($header_style == 'style21') {
            $account_link_icon = '<i class="careerfy-icon careerfy-multimedia"></i>';

        } else if ($header_style == 'style22') {
            $account_link_icon = '<i class="careerfy-icon careerfy-multimedia"></i>';

        } else {
            $account_link_icon = '';
        }

        if (is_user_logged_in()) {

            $user_id = get_current_user_id();
            if (isset($user_dashboard_page) && $user_dashboard_page != '') {
                $user_is_candidate = jobsearch_user_is_candidate($user_id);
                $user_is_employer = jobsearch_user_is_employer($user_id);
                if ($user_is_employer) {
                    $employer_id = jobsearch_get_user_employer_id($user_id);
                }
                if ($user_is_candidate) {
                    $candidate_id = jobsearch_get_user_candidate_id($user_id);
                }
                $my_account_url = esc_url(get_permalink($user_dashboard_page));
                if (wp_is_mobile()) {
                    $my_account_url = 'javascript:void(0);';
                }
                ?>
                <li class="jobsearch-userdash-menumain menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children">
                    <?php if ($header_style == 'style2') { ?>
                        <a href="<?php echo($my_account_url) ?>"
                           class="careerfy-btn-icon jobsearch-open-signin-tab"><i
                                    class="careerfy-icon careerfy-user"></i></a>
                    <?php } else { ?>
                        <a href="<?php echo($my_account_url) ?>"
                           class="<?php echo $account_link_btn_color_class ?> <?php echo $active_link_class ?> elementor-item elementor-item-anchor active"><?php echo apply_filters('jobsearch_header_user_myaccount_txt', esc_html__('My Account', 'wp-jobsearch')); ?></a>
                    <?php } ?>

                    <ul <?php echo apply_filters('jobsearch_hdr_menu_accountlinks_ul_atts', 'class="nav-item-children sub-menu elementor-nav-menu--dropdown"') ?>>
                        <?php jobsearch_user_account_linkitems($user_pkg_limits, $page_url, $get_tab); ?>
                    </ul>
                </li>
                <?php
            }
        } else {

            $op_register_form_allow = isset($jobsearch_plugin_options['login_register_form']) ? $jobsearch_plugin_options['login_register_form'] : '';
            $op_cand_register_allow = isset($jobsearch_plugin_options['login_candidate_register']) ? $jobsearch_plugin_options['login_candidate_register'] : '';
            $op_emp_register_allow = isset($jobsearch_plugin_options['login_employer_register']) ? $jobsearch_plugin_options['login_employer_register'] : '';

            $register_link_view = true;
            if ($op_register_form_allow == 'off') {
                $register_link_view = false;
            }
            if ($op_cand_register_allow == 'no' && $op_emp_register_allow == 'no') {
                $register_link_view = false;
            }
            ob_start();
            if ($is_popup) {

                if ($register_link_view === true && $header_style != 'style12' && $header_style != 'style17' && $header_style != 'style19' && $header_style != 'style20' && $header_style != 'style22') { ?>
                    <li><a href="javascript:void(0);"
                           class="jobsearch-color jobsearch-open-register-tab "><?php echo $account_register_icon; ?><?php echo esc_html__('Register', 'wp-jobsearch'); ?></a>
                    </li>
                <?php } ?>
                <li><a href="javascript:void(0);"
                       class="<?php echo $account_link_btn_color_class ?> jobsearch-open-signin-tab <?php echo $active_link_class ?>"><?php echo $account_link_icon ?><?php echo($account_link_text) ?></a>
                </li>
                <?php if ($header_style == 'style22') { ?>
                    <li class="jobsearch-logto-link"><a
                                href="javascript:void(0);" class="jobsearch-open-register-tab"><i
                                    class="careerfy-icon careerfy-user-1"></i> <?php echo esc_html__('Sign Up', 'wp-jobsearch'); ?>
                        </a>
                    </li>
                <?php } ?>
            <?php } else {
                if ($register_link_view === true) { ?>
                    <li class="jobsearch-regto-link menu-item menu-item-type-custom menu-item-object-custom"><a
                            href="<?php echo esc_url(get_permalink($jobsearch_registr_page)) ?>" class="elementor-item elementor-item-anchor"><?php echo esc_html__('Register', 'wp-jobsearch'); ?></a>
                    </li>
                <?php } ?>
                <li class="jobsearch-logto-link menu-item menu-item-type-custom menu-item-object-custom"><a
                        href="<?php echo esc_url(get_permalink($jobsearch_login_page)) ?>" class="elementor-item elementor-item-anchor"> <?php echo esc_html__('Sign In', 'wp-jobsearch'); ?></a>
                </li>
                <?php
            }
            $links_html = ob_get_clean();
            echo apply_filters('jobsearch_top_login_links', $links_html, $register_link_view);
        }
        echo apply_filters('jobsearch_dashmenu_account_btns_after_items', '', $args);
    }
}

add_filter('wp_nav_menu_items', 'jobsearch_login_menu_items', 10, 2);

function jobsearch_login_menu_items($items, $args)
{
    global $jobsearch_plugin_options;
    $menu_slug = isset($jobsearch_plugin_options['user-login-links-menu']) ? $jobsearch_plugin_options['user-login-links-menu'] : '';
    $menu_links = isset($jobsearch_plugin_options['user-login-dashboard-links']) ? $jobsearch_plugin_options['user-login-dashboard-links'] : '';

    if ($menu_links == 'on' && 
        $menu_slug != '' && 
        ((isset($args->menu->slug) && $args->menu->slug == $menu_slug) || (isset($args->menu) && $args->menu == $menu_slug) || (isset($args->slug) && $args->slug == $menu_slug))
        ) {

        ob_start();
        do_action('jobsearch_user_account_links', array());
        $items_html = ob_get_clean();

        $items .= $items_html;
    }
    return $items;
}

add_action('careerfy_mobile_navigation_after', 'jobsearch_dashmenu_mobile_navigation_after');

function jobsearch_dashmenu_mobile_navigation_after()
{
    if (is_user_logged_in()) {

        global $jobsearch_plugin_options;

        $user_pkg_limits = new Package_Limits;

        $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');

        $page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
        //$page_url = get_permalink($page_id);
        $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page');

        $get_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';
        ?>
        <div class="jobsearch-mobile-dashmenu careerfy-inmobile-itemsgen" style="display: none;">
            <ul class="careerfy-mobile-navbar">
                <?php jobsearch_user_account_linkitems($user_pkg_limits, $page_url, $get_tab); ?>
            </ul>
        </div>
        <?php
    }
}
