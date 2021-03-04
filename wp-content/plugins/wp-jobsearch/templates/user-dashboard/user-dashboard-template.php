<?php

use WP_Jobsearch\Package_Limits;

global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings, $sitepress;
do_action('jobsearch_user_dashboard_header');

do_action('jobsearch_enqueue_dashboard_styles');

if (wp_is_mobile()) {
    get_header('mobile');
} else {
    get_header();
}

$lang_code = '';
if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
    $lang_code = $sitepress->get_current_language();
}

$user_pkg_limits = new Package_Limits;

$user_id = get_current_user_id();
$user_is_candidate = jobsearch_user_is_candidate($user_id);
$user_is_employer = jobsearch_user_is_employer($user_id);
wp_enqueue_script('jobsearch-user-dashboard');
wp_enqueue_script('fancybox-pack');
wp_enqueue_script('jobsearch-shortlist-functions-script');
wp_enqueue_script('datetimepicker-script');

$plugin_default_view = isset($jobsearch_plugin_options['jobsearch-default-page-view']) ? $jobsearch_plugin_options['jobsearch-default-page-view'] : 'full';
$plugin_default_view_with_str = '';
if ($plugin_default_view == 'boxed') {

    $plugin_default_view_with_str = isset($jobsearch_plugin_options['jobsearch-boxed-view-width']) && $jobsearch_plugin_options['jobsearch-boxed-view-width'] != '' ? $jobsearch_plugin_options['jobsearch-boxed-view-width'] : '1140px';
    if ($plugin_default_view_with_str != '' && !wp_is_mobile()) {
        $plugin_default_view_with_str = ' style="width:' . $plugin_default_view_with_str . '"';
    }
}

do_action('jobsearch_translate_profile_with_wpml_source', $user_id);
do_action('jobsearch_user_dash_instart_act', $user_id);
?>
<div class="jobsearch-main-content">
    <div class="jobsearch-plugin-default-container" <?php echo force_balance_tags($plugin_default_view_with_str); ?>>
        <div class="user-dashboard-loader" style="display: none;"></div>
        <div class="jobsearch-row">
            <?php
            $user_alot_advs = get_option('jobsearch_user_assign_advs_' . $user_id);
            if ($user_is_candidate) {
                $candidate_id = jobsearch_get_user_candidate_id($user_id);
                $user_status = get_post_meta($candidate_id, 'jobsearch_field_candidate_approved', true);
                $candidate_unapproved_text = isset($jobsearch_plugin_options['unapproverd_candidate_txt']) ? $jobsearch_plugin_options['unapproverd_candidate_txt'] : '';
                $candidate_unapproved_text = apply_filters('wpml_translate_single_string', $candidate_unapproved_text, 'JobSearch Options', 'Unapproverd Candidate Message - ' . $candidate_unapproved_text, $lang_code);
                //var_dump($lang_code);
                //var_dump($candidate_unapproved_text);
                if ($user_status != 'on' && $candidate_unapproved_text != '') {
                    ?>
                    <div class="jobsearch-column-12">
                        <div class="jobsearch-unapproved-user-con">
                            <p><?php echo apply_filters('the_content', $candidate_unapproved_text); ?></p>
                        </div>
                    </div>
                    <?php
                }
                if (!$user_alot_advs) {
                    // assign packages
                    $cand_asignpkgs_onsignup = isset($jobsearch_plugin_options['cand_asignpkgs_onsignup']) ? $jobsearch_plugin_options['cand_asignpkgs_onsignup'] : '';
                    if (!empty($cand_asignpkgs_onsignup)) {
                        foreach ($cand_asignpkgs_onsignup as $cand_asignpkg_id) {
                            $assing_pkg = jobsearch_self_assign_packge_to_user($user_id, $cand_asignpkg_id);
                        }
                    }
                }
            } else if ($user_is_employer) {
                $cand_followers = get_user_meta($user_id, 'jobsearch-user-followins-list', true);
                $employer_id = jobsearch_get_user_employer_id($user_id);
                $user_status = get_post_meta($employer_id, 'jobsearch_field_employer_approved', true);
                $employer_unapproved_text = isset($jobsearch_plugin_options['unapproverd_employer_txt']) ? $jobsearch_plugin_options['unapproverd_employer_txt'] : '';
                $employer_unapproved_text = apply_filters('wpml_translate_single_string', $employer_unapproved_text, 'JobSearch Options', 'Unapproverd Employer Message - ' . $employer_unapproved_text, $lang_code);
                if ($user_status != 'on' && $employer_unapproved_text != '') { ?>
                    <div class="jobsearch-column-12">
                        <div class="jobsearch-unapproved-user-con">
                            <p><?php echo apply_filters('the_content', $employer_unapproved_text); ?></p>
                        </div>
                    </div>
                    <?php
                }
                if (!$user_alot_advs) {
                    // assign packages
                    $emp_asignpkgs_onsignup = isset($jobsearch_plugin_options['emp_asignpkgs_onsignup']) ? $jobsearch_plugin_options['emp_asignpkgs_onsignup'] : '';
                    if (!empty($emp_asignpkgs_onsignup)) {
                        foreach ($emp_asignpkgs_onsignup as $emp_asignpkg_id) {
                            $assing_pkg = jobsearch_self_assign_packge_to_user($user_id, $emp_asignpkg_id);
                        }
                    }
                }
            }
            //die;
            update_option('jobsearch_user_assign_advs_' . $user_id, '1');
            
            $is_a_member = false;
            
            $get_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';
            //require_once 'user-dashboard-sidebar.php';
            jobsearch_user_dashboard_sidebar_html();
            ?>
            <div class="jobsearch-column-9 jobsearch-typo-wrap">
                <?php
                $dashmenu_links_cand = isset($jobsearch_plugin_options['cand_dashbord_menu']) ? $jobsearch_plugin_options['cand_dashbord_menu'] : '';
                $dashmenu_links_cand = apply_filters('jobsearch_cand_dashbord_menu_items_arr', $dashmenu_links_cand);
                if ($user_is_candidate) {
                    $is_a_member = true;
                    echo '<div id="dashboard-tab-settings" class="main-tab-section">';
                    if ($get_tab == 'dashboard-settings') {
                        if (isset($dashmenu_links_cand['my_profile']) && $dashmenu_links_cand['my_profile'] == '1' && !$user_pkg_limits::cand_field_is_locked('dashtab_fields|my_profile')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'dashboard-settings'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";

                    echo '<div id="dashboard-tab-my-resume" class="main-tab-section">';
                    if ($get_tab == 'my-resume') {
                        if (isset($dashmenu_links_cand['my_resume']) && $dashmenu_links_cand['my_resume'] == '1' && !$user_pkg_limits::cand_field_is_locked('dashtab_fields|my_resume')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'my-resume'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";

                    echo '<div id="dashboard-tab-favourite-jobs" class="main-tab-section">';
                    if ($get_tab == 'favourite-jobs') {
                        if (isset($dashmenu_links_cand['fav_jobs']) && $dashmenu_links_cand['fav_jobs'] == '1' && !$user_pkg_limits::cand_field_is_locked('dashtab_fields|fav_jobs')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'favourite-jobs'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";

                    echo '<div id="dashboard-tab-applied-jobs" class="main-tab-section">';
                    if ($get_tab == 'applied-jobs') {
                        if (isset($dashmenu_links_cand['applied_jobs']) && $dashmenu_links_cand['applied_jobs'] == '1' && !$user_pkg_limits::cand_field_is_locked('dashtab_fields|applied_jobs')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'applied-jobs'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";

                    echo '<div id="dashboard-tab-packages" class="main-tab-section">';
                    if ($get_tab == 'user-packages') {
                        if (isset($dashmenu_links_cand['packages']) && $dashmenu_links_cand['packages'] == '1' && !$user_pkg_limits::cand_field_is_locked('dashtab_fields|packages')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'packages'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";

                    echo '<div id="dashboard-tab-transactions" class="main-tab-section">';
                    if ($get_tab == 'user-transactions') {
                        if (isset($dashmenu_links_cand['transactions']) && $dashmenu_links_cand['transactions'] == '1' && !$user_pkg_limits::cand_field_is_locked('dashtab_fields|transactions')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'transactions'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";

                    echo '<div id="dashboard-tab-cv-manager" class="main-tab-section">';
                    if ($get_tab == 'cv-manager') {
                        if (isset($dashmenu_links_cand['cv_manager']) && $dashmenu_links_cand['cv_manager'] == '1' && !$user_pkg_limits::cand_field_is_locked('dashtab_fields|cv_manager')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'cv-manager'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";

                    echo '<div id="dashboard-tab-stats" class="main-tab-section">';
                    if ($get_tab == '') {
                        echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'stats'));
                    }
                    echo '</div>' . "\n";

                    echo '<div id="dashboard-tab-my-emails" class="main-tab-section">';
                    if ($get_tab == 'my-emails') {
                        if (isset($dashmenu_links_cand['my_emails']) && $dashmenu_links_cand['my_emails'] == '1' && !$user_pkg_limits::cand_field_is_locked('dashtab_fields|my_emails')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'emails'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";

                    echo '<div id="dashboard-tab-following" class="main-tab-section">';
                    if ($get_tab == 'following') {
                        if (isset($dashmenu_links_cand['following']) && $dashmenu_links_cand['following'] == '1' && !$user_pkg_limits::cand_field_is_locked('dashtab_fields|following')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'following'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";

                    echo apply_filters('jobsearch_dashboard_tab_content_ext', '', $get_tab);

                    echo '<div id="dashboard-tab-change-password" class="main-tab-section">';
                    if ($get_tab == 'change-password') {
                        if (isset($dashmenu_links_cand['change_password']) && $dashmenu_links_cand['change_password'] == '1' && !$user_pkg_limits::cand_field_is_locked('dashtab_fields|change_password')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('', 'change-password'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('candidate', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";
                }
                if (jobsearch_user_isemp_member($user_id)) {
                    $is_a_member = true;
                    $membusr_perms = jobsearch_emp_accmember_perms($user_id);

                    echo '<div id="dashboard-tab-settings" class="main-tab-section">';
                    if ($get_tab == '' || $get_tab == 'dashboard-settings') {
                        echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'memb-dashboard'));
                    }
                    echo '</div>' . "\n";
                    if (is_array($membusr_perms) && in_array('u_post_job', $membusr_perms)) {
                        echo '<div id="dashboard-tab-user-job" class="main-tab-section">';
                        if ($get_tab == 'user-job') {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'job'));
                        }
                        echo '</div>' . "\n";
                    }
                    if (is_array($membusr_perms) && in_array('u_manage_jobs', $membusr_perms)) {
                        echo '<div id="dashboard-tab-manage-jobs" class="main-tab-section">';
                        if ($get_tab == 'manage-jobs' || $get_tab == 'manage-applicants') {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'manage-jobs'));
                        }
                        echo '</div>' . "\n";
                    }
                    if (is_array($membusr_perms) && in_array('u_saved_cands', $membusr_perms)) {
                        echo '<div id="dashboard-tab-resumes" class="main-tab-section">';
                        if ($get_tab == 'user-resumes') {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'shortlisted-resumes'));
                        }
                        echo '</div>' . "\n";
                    }
                    if (is_array($membusr_perms) && in_array('u_packages', $membusr_perms)) {
                        echo '<div id="dashboard-tab-packages" class="main-tab-section">';
                        if ($get_tab == 'user-packages') {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'packages'));
                        }
                        echo '</div>' . "\n";
                    }
                    if (is_array($membusr_perms) && in_array('u_transactions', $membusr_perms)) {
                        echo '<div id="dashboard-tab-transactions" class="main-tab-section">';
                        if ($get_tab == 'user-transactions') {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'transactions'));
                        }
                        echo '</div>' . "\n";
                    }
                    echo '<div id="dashboard-tab-change-password" class="main-tab-section">';
                    if ($get_tab == 'change-password') {
                        echo ($Jobsearch_User_Dashboard_Settings->show_template_part('', 'change-password'));
                    }
                    echo '</div>' . "\n";
                }
                if ($user_is_employer) {
                    $is_a_member = true;
                    $dashmenu_links_emp = isset($jobsearch_plugin_options['emp_dashbord_menu']) ? $jobsearch_plugin_options['emp_dashbord_menu'] : '';
                    echo '<div id="dashboard-tab-settings" class="main-tab-section">';
                    if ($get_tab == 'dashboard-settings') {
                        if (isset($dashmenu_links_emp['company_profile']) && $dashmenu_links_emp['company_profile'] == '1' && !$user_pkg_limits::emp_field_is_locked('dashtab_fields|company_profile')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'dashboard-settings'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";

                    echo '<div id="dashboard-tab-manage-jobs" class="main-tab-section">';
                    if (($get_tab == 'manage-jobs' || $get_tab == 'manage-applicants')) {
                        if (isset($dashmenu_links_emp['manage_jobs']) && $dashmenu_links_emp['manage_jobs'] == '1' && !$user_pkg_limits::emp_field_is_locked('dashtab_fields|manage_jobs')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'manage-jobs'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";

                    echo '<div id="dashboard-tab-allapplicants" class="main-tab-section">';
                    if ($get_tab == 'all-applicants') {
                        if (isset($dashmenu_links_emp['all_applicants']) && $dashmenu_links_emp['all_applicants'] == '1' && !$user_pkg_limits::emp_field_is_locked('dashtab_fields|all_applicants')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'all-applicants'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";

                    echo '<div id="dashboard-tab-transactions" class="main-tab-section">';
                    if ($get_tab == 'user-transactions') {
                        if (isset($dashmenu_links_emp['transactions']) && $dashmenu_links_emp['transactions'] == '1' && !$user_pkg_limits::emp_field_is_locked('dashtab_fields|transactions')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'transactions'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";

                    echo '<div id="dashboard-tab-resumes" class="main-tab-section">';
                    if ($get_tab == 'user-resumes') {
                        if (isset($dashmenu_links_emp['saved_candidates']) && $dashmenu_links_emp['saved_candidates'] == '1' && !$user_pkg_limits::emp_field_is_locked('dashtab_fields|saved_candidates')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'shortlisted-resumes'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";

                    echo '<div id="dashboard-tab-packages" class="main-tab-section">';
                    if ($get_tab == 'user-packages') {
                        if (isset($dashmenu_links_emp['packages']) && $dashmenu_links_emp['packages'] == '1' && !$user_pkg_limits::emp_field_is_locked('dashtab_fields|packages')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'packages'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";

                    echo '<div id="dashboard-tab-user-job" class="main-tab-section">';
                    if ($get_tab == 'user-job') {
                        if (isset($dashmenu_links_emp['post_new_job']) && $dashmenu_links_emp['post_new_job'] == '1' && !$user_pkg_limits::emp_field_is_locked('dashtab_fields|post_new_job')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'job'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";

                    echo '<div id="dashboard-tab-stats" class="main-tab-section">';
                    if ($get_tab == '') {
                        $blnk_tabcontnt = apply_filters('jobsearch_emp_dash_firsttab_tabcontnt', 'stats');
                        echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', $blnk_tabcontnt));
                    }
                    echo '</div>' . "\n";

                    echo '<div id="dashboard-tab-my-emails" class="main-tab-section">';
                    if ($get_tab == 'my-emails') {
                        if (isset($dashmenu_links_emp['my_emails']) && $dashmenu_links_emp['my_emails'] == '1' && !$user_pkg_limits::emp_field_is_locked('dashtab_fields|my_emails')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'emails'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";

                    echo '<div id="dashboard-tab-followers" class="main-tab-section">';
                    if ($get_tab == 'followers') {
                        if (isset($dashmenu_links_emp['followers']) && $dashmenu_links_emp['followers'] == '1' && !$user_pkg_limits::emp_field_is_locked('dashtab_fields|followers')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'followers'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";

                    echo apply_filters('jobsearch_dashboard_tab_content_ext', '', $get_tab);

                    echo '<div id="dashboard-tab-change-password" class="main-tab-section">';
                    if ($get_tab == 'change-password') {
                        if (isset($dashmenu_links_emp['change_password']) && $dashmenu_links_emp['change_password'] == '1' && !$user_pkg_limits::emp_field_is_locked('dashtab_fields|change_password')) {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('', 'change-password'));
                        } else {
                            echo ($Jobsearch_User_Dashboard_Settings->show_template_part('employer', 'stats'));
                        }
                    }
                    echo '</div>' . "\n";
                }
                
                if (!$is_a_member) {
                    echo apply_filters('jobsearch_dashboard_tab_content_ext', '', $get_tab);
                }
                echo apply_filters('jobsearch_dash_tab_content_tabsdata_after', '', $get_tab);
                ?>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
