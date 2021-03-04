<?php

use WP_Jobsearch\Package_Limits;

global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;
$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);

$user_pkg_limits = new Package_Limits;

$page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
$page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);

$employer_id = jobsearch_get_user_employer_id($user_id);

$reults_per_page = isset($jobsearch_plugin_options['user-dashboard-per-page']) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;

$page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;

$all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';

$user_stats_switch = isset($jobsearch_plugin_options['user_stats_switch']) ? $jobsearch_plugin_options['user_stats_switch'] : '';

if ($employer_id > 0) {

    wp_enqueue_script('jobsearch-morris');
    wp_enqueue_script('jobsearch-raphael');

    $emp_pkgbase_profile = isset($jobsearch_plugin_options['emp_pkg_base_profile']) ? $jobsearch_plugin_options['emp_pkg_base_profile'] : '';

    $rand_id = rand(1000000, 9999999);
    $args = array(
        'post_type' => 'job',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'fields' => 'ids',
        'order' => 'DESC',
        'orderby' => 'ID',
        'meta_query' => array(
            array(
                'key' => 'jobsearch_field_job_posted_by',
                'value' => $employer_id,
                'compare' => '=',
            ),
        ),
    );

    $jobs_query = new WP_Query($args);

    $total_jobs = $jobs_query->found_posts;

    $_job_posts = $jobs_query->posts;

    $overall_viewed_cands = 0;
    $job_short_int_count = 0;
    $job_appls_count = 0;
    $job_unviewed_appls_count = 0;

    if (!empty($_job_posts)) {
        foreach ($_job_posts as $_job_post) {
            $viewed_candidates = get_post_meta($_job_post, 'jobsearch_viewed_candidates', true);
            if (empty($viewed_candidates)) {
                $viewed_candidates = array();
            }
            $viewed_candidates = jobsearch_is_post_ids_array($viewed_candidates, 'candidate');
            $viewed_candidates_count = empty($viewed_candidates) ? 0 : count($viewed_candidates);
            $overall_viewed_cands += $viewed_candidates_count;
            //
            $job_short_int_list = get_post_meta($_job_post, '_job_short_interview_list', true);
            $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : array();
            $job_short_int_list = jobsearch_is_post_ids_array($job_short_int_list, 'candidate');
            $job_short_int_count += count($job_short_int_list);

            //
            $job_applicants_list = get_post_meta($_job_post, 'jobsearch_job_applicants_list', true);
            $job_applicants_list = $job_applicants_list != '' ? explode(',', $job_applicants_list) : array();
            $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
            if (!empty($job_applicants_list)) {
                foreach ($job_applicants_list as $_cand_id) {
                    $_candi_user_id = jobsearch_get_candidate_user_id($_cand_id);
                    //if (absint($_candi_user_id) > 0) {
                        $job_appls_count++;
                    //}
                }
            }

            //
            $email_applicants_list = get_post_meta($_job_post, 'jobsearch_job_emailapps_list', true);
            if (!empty($email_applicants_list)) {
                $job_appls_count += count($email_applicants_list);
            }
        }
        if ($job_appls_count > 0 && $job_appls_count > $overall_viewed_cands) {
            $job_unviewed_appls_count = $job_appls_count - $overall_viewed_cands;
        }
    }
    wp_reset_postdata();

    $employer_resumes_count = 0;
    $employer_resumes_list = get_post_meta($employer_id, 'jobsearch_candidates_list', true);
    if ($employer_resumes_list != '') {
        $employer_resumes_list = explode(',', $employer_resumes_list);
        $employer_resumes_count = count($employer_resumes_list);
    }
    ?>
    <div class="jobsearch-employer-dasboard">
        <?php
        echo apply_filters('jobsearch_empdash_stats_before_start', '', $employer_id);
        if ($emp_pkgbase_profile == 'on') {
            wp_enqueue_script('jobsearch-packages-scripts');
            ?>
            <div class="jobsearch-employer-box-section">
                <?php
                $profpkg_rand = rand(10000000, 99999999);
                $user_has_profpkg = false;
                $profpkg_is_subscribe = false;
                $attpordr_prod_name = esc_html__('No Plan', 'wp-jobsearch');
                $usercurnt_attpordr_id = get_user_meta($user_id, 'att_profpckg_orderid', true);
                $usercurnt_attpordr_pkgid = 0;
                if ($usercurnt_attpordr_id > 0) {
                    $usercurnt_attpordr_pkgid = get_post_meta($usercurnt_attpordr_id, 'jobsearch_order_package', true);
                    $usercurnt_attpordr_pkgid = absint($usercurnt_attpordr_pkgid);

                    $usercurnt_attpordr_exp = get_post_meta($usercurnt_attpordr_id, 'package_expiry_timestamp', true);
                    if (get_post_type($usercurnt_attpordr_id) == 'shop_order' && $usercurnt_attpordr_exp > 0) {
                        $user_has_profpkg = true;
                        $profpkg_is_subscribe = jobsearch_emp_profile_pckg_is_subscribed($usercurnt_attpordr_pkgid, $user_id);

                        $usercurnt_attpordr_obj = get_post($usercurnt_attpordr_id);
                        $usercurnt_attpordr_date = $usercurnt_attpordr_obj->post_date;
                        $usercurnt_attpordr_date = date_i18n(get_option('date_format'), strtotime($usercurnt_attpordr_date));

                        $attpordr_prod_name = jobsearch_get_order_product_name($usercurnt_attpordr_id);
                        $profpckg_order_obj = wc_get_order($usercurnt_attpordr_id);
                        $profpckg_order_price = $profpckg_order_obj->get_total();
                        if ($profpckg_order_price) {
                            $profpckg_order_price = jobsearch_get_price_format($profpckg_order_price);
                        }
                    }
                }
                ?>
                <div class="jobsearch-profile-title">
                    <h2><?php esc_html_e('Profile Package', 'wp-jobsearch') ?></h2>
                    <div class="current-planame-holder">
                        <?php esc_html_e('Current Plan:', 'wp-jobsearch') ?>
                        <span><?php echo($attpordr_prod_name) ?></span>
                    </div>
                </div>
                <div class="jobsearch-dashprofpkgs-con">
                    <?php
                    $cpropkgs_args = array(
                        'post_type' => 'package',
                        'posts_per_page' => -1,
                        'post_status' => 'publish',
                        'fields' => 'ids',
                        'order' => 'ASC',
                        'orderby' => 'title',
                        'meta_query' => array(
                            array(
                                'key' => 'jobsearch_field_package_type',
                                'value' => 'employer_profile',
                                'compare' => '=',
                            ),
                        ),
                        'post__not_in' => array($usercurnt_attpordr_pkgid),
                    );
                    $cpropkgs_query = new WP_Query($cpropkgs_args);
                    $cpropkgs_found = $cpropkgs_query->found_posts;
                    $cpropkgs_posts = $cpropkgs_query->posts;
                    wp_reset_postdata();
                    ob_start();
                    if (!empty($cpropkgs_posts)) {
                        ?>
                        <div class="all-chprofpkg-list">
                            <table>
                                <thead>
                                <tr>
                                    <th class="profpkg-hding">
                                        <span><?php esc_html_e('Plan Name', 'wp-jobsearch') ?></span></th>
                                    <th class="profpkg-titlemid">
                                        <span><?php esc_html_e('Price', 'wp-jobsearch') ?></span></th>
                                    <th class="profpkg-btnsec">
                                        <span><?php esc_html_e('Actions', 'wp-jobsearch') ?></span></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($cpropkgs_posts as $cpropkgs_pkgid) {
                                    $pkg_attach_product = get_post_meta($cpropkgs_pkgid, 'jobsearch_package_product', true);

                                    if ($pkg_attach_product != '' && get_page_by_path($pkg_attach_product, 'OBJECT', 'product')) {
                                        $profp_plan_chtype = get_post_meta($cpropkgs_pkgid, 'jobsearch_field_charges_type', true);
                                        if ($profp_plan_chtype == 'paid') {
                                            $profp_plan_price = get_post_meta($cpropkgs_pkgid, 'jobsearch_field_package_price', true);
                                            $profp_plan_price = jobsearch_get_price_format($profp_plan_price);
                                        } else {
                                            $profp_plan_price = esc_html__('Free', 'wp-jobsearch');
                                        }
                                        //
                                        $popup_args = array('p_pkgid' => $cpropkgs_pkgid);
                                        add_action('wp_footer', function () use ($popup_args) {

                                            $jobsearch__options = get_option('jobsearch_plugin_options');
                                            extract(shortcode_atts(array(
                                                'p_pkgid' => ''
                                            ), $popup_args));

                                            $ppkg_pbase_dashtabs = get_post_meta($p_pkgid, 'jobsearch_field_emp_pbase_dashtabs', true);
                                            $ppkg_pbase_profile = get_post_meta($p_pkgid, 'jobsearch_field_emp_pbase_profile', true);
                                            $ppkg_pbase_social = get_post_meta($p_pkgid, 'jobsearch_field_emp_pbase_social', true);
                                            $ppkg_pbase_cusfields = get_post_meta($p_pkgid, 'jobsearch_field_emp_pbase_cusfields', true);
                                            $ppkg_pbase_stats = get_post_meta($p_pkgid, 'jobsearch_field_emp_pbase_stats', true);
                                            $ppkg_pbase_location = get_post_meta($p_pkgid, 'jobsearch_field_emp_pbase_location', true);
                                            $ppkg_pbase_accmembs = get_post_meta($p_pkgid, 'jobsearch_field_emp_pbase_accmembs', true);
                                            $ppkg_pbase_team = get_post_meta($p_pkgid, 'jobsearch_field_emp_pbase_team', true);
                                            $ppkg_pbase_gphotos = get_post_meta($p_pkgid, 'jobsearch_field_emp_pbase_gphotos', true);

                                            $emp_pkgbase_dashsecs_arr = apply_filters('jobsearch_emp_dash_menu_in_opts', array(
                                                'company_profile' => __('Company Profile', 'wp-jobsearch'),
                                                'post_new_job' => __('Post a New Job', 'wp-jobsearch'),
                                                'manage_jobs' => __('Manage Jobs', 'wp-jobsearch'),
                                                'all_applicants' => __('All Applicants', 'wp-jobsearch'),
                                                'saved_candidates' => __('Saved Candidates', 'wp-jobsearch'),
                                                'packages' => __('Packages', 'wp-jobsearch'),
                                                'transactions' => __('Transactions', 'wp-jobsearch'),
                                                'followers' => __('Followers', 'wp-jobsearch'),
                                                'change_password' => __('Change Password', 'wp-jobsearch'),
                                            ));
                                            $show_dash_tabs = array();
                                            if (!empty($ppkg_pbase_dashtabs)) {
                                                foreach ($ppkg_pbase_dashtabs as $profpkg_dashtab_key) {
                                                    if (isset($emp_pkgbase_dashsecs_arr[$profpkg_dashtab_key])) {
                                                        $show_dash_tabs[] = $emp_pkgbase_dashsecs_arr[$profpkg_dashtab_key];
                                                    }
                                                }
                                            }
                                            if (!empty($show_dash_tabs)) {
                                                $show_dash_tabs = implode(', ', $show_dash_tabs);
                                            } else {
                                                $show_dash_tabs = '-';
                                            }
                                            //
                                            $emp_pkgbase_profilefields = array(
                                                'jobs_cover_img' => esc_html__('Jobs Cover Image', 'wp-jobsearch'),
                                                'profile_url' => esc_html__('Profile URL', 'wp-jobsearch'),
                                                'public_view' => esc_html__('Profile for Public View', 'wp-jobsearch'),
                                                'phone' => esc_html__('Phone', 'wp-jobsearch'),
                                                'website' => esc_html__('Website', 'wp-jobsearch'),
                                                'sector' => esc_html__('Sector', 'wp-jobsearch'),
                                                'founded_date' => esc_html__('Founded Date', 'wp-jobsearch'),
                                                'about_company' => esc_html__('About the Company', 'wp-jobsearch'),
                                            );
                                            $show_profile_fields = array();
                                            if (!empty($ppkg_pbase_profile)) {
                                                foreach ($ppkg_pbase_profile as $profpkg_pfield_key) {
                                                    if (isset($emp_pkgbase_profilefields[$profpkg_pfield_key])) {
                                                        $show_profile_fields[] = $emp_pkgbase_profilefields[$profpkg_pfield_key];
                                                    }
                                                }
                                            }
                                            if (!empty($show_profile_fields)) {
                                                $show_profile_fields = implode(', ', $show_profile_fields);
                                            } else {
                                                $show_profile_fields = '-';
                                            }
                                            //
                                            $employer_social_mlinks = isset($jobsearch__options['employer_social_mlinks']) ? $jobsearch__options['employer_social_mlinks'] : '';
                                            $emp_pkgbase_social_arr = array(
                                                'facebook' => __('Facebook', 'wp-jobsearch'),
                                                'twitter' => __('Twitter', 'wp-jobsearch'),
                                                'google_plus' => __('Google Plus', 'wp-jobsearch'),
                                                'linkedin' => __('Linkedin', 'wp-jobsearch'),
                                                'dribbble' => __('Dribbble', 'wp-jobsearch'),
                                            );
                                            if (!empty($employer_social_mlinks)) {
                                                if (isset($employer_social_mlinks['title']) && is_array($employer_social_mlinks['title'])) {
                                                    $field_counter = 0;
                                                    foreach ($employer_social_mlinks['title'] as $emp_social_mlink) {
                                                        $emp_pkgbase_social_arr['dynm_social' . $field_counter] = $emp_social_mlink;
                                                        $field_counter++;
                                                    }
                                                }
                                            }
                                            $show_social_fields = array();
                                            if (!empty($ppkg_pbase_social)) {
                                                foreach ($ppkg_pbase_social as $profpkg_socialf_key) {
                                                    if (isset($emp_pkgbase_social_arr[$profpkg_socialf_key])) {
                                                        $show_social_fields[] = $emp_pkgbase_social_arr[$profpkg_socialf_key];
                                                    }
                                                }
                                            }
                                            if (!empty($show_social_fields)) {
                                                $show_social_fields = implode(', ', $show_social_fields);
                                            } else {
                                                $show_social_fields = '-';
                                            }
                                            //
                                            $emp_custom_fields_saved_data = get_option('jobsearch_custom_field_employer');
                                            if (is_array($emp_custom_fields_saved_data) && sizeof($emp_custom_fields_saved_data) > 0) {
                                                $emp_pkgbase_cusfileds_arr = array();
                                                foreach ($emp_custom_fields_saved_data as $emp_cus_field_key => $emp_cus_field_kdata) {
                                                    $cusfield_label = isset($emp_cus_field_kdata['label']) ? $emp_cus_field_kdata['label'] : '';
                                                    $cusfield_name = isset($emp_cus_field_kdata['name']) ? $emp_cus_field_kdata['name'] : '';
                                                    if ($cusfield_label != '' && $cusfield_name != '') {
                                                        $emp_pkgbase_cusfileds_arr[$cusfield_name] = $cusfield_label;
                                                    }
                                                }
                                                $show_custf_fields = array();
                                                if (!empty($ppkg_pbase_cusfields)) {
                                                    foreach ($ppkg_pbase_cusfields as $profpkg_custmf_key) {
                                                        if (isset($emp_pkgbase_cusfileds_arr[$profpkg_custmf_key])) {
                                                            $show_custf_fields[] = $emp_pkgbase_cusfileds_arr[$profpkg_custmf_key];
                                                        }
                                                    }
                                                }
                                                if (!empty($show_custf_fields)) {
                                                    $show_custf_fields = implode(', ', $show_custf_fields);
                                                } else {
                                                    $show_custf_fields = '-';
                                                }
                                            }
                                            $pkghdin_name = get_the_title($p_pkgid);
                                            //

                                            $unlimited_numjobs = get_post_meta($p_pkgid, 'jobsearch_field_unlim_emprofjobs', true);
                                            $unlimited_numfjobs = get_post_meta($p_pkgid, 'jobsearch_field_unlim_emproffjobs', true);
                                            $unlimited_jobexptm = get_post_meta($p_pkgid, 'jobsearch_field_unlim_emprofjobexp', true);
                                            $unlimited_numcvs = get_post_meta($p_pkgid, 'jobsearch_field_unlim_emprofnumcvs', true);

                                            $pkg_total_jobs = get_post_meta($p_pkgid, 'jobsearch_field_emprof_num_jobs', true);
                                            if ($unlimited_numjobs == 'on') {
                                                $pkg_total_jobs = esc_html__('Unlimited', 'wp-jobsearch');
                                            }
                                            //
                                            $total_cvs = get_post_meta($p_pkgid, 'jobsearch_field_emprof_num_cvs', true);
                                            if ($unlimited_numcvs == 'on') {
                                                $total_cvs = esc_html__('Unlimited', 'wp-jobsearch');
                                            }
                                            //
                                            $total_fjobs = get_post_meta($p_pkgid, 'jobsearch_field_emprof_num_fjobs', true);
                                            if ($unlimited_numfjobs == 'on') {
                                                $total_fjobs = esc_html__('Unlimited', 'wp-jobsearch');
                                            }

                                            $feat_job_credits = get_post_meta($p_pkgid, 'jobsearch_field_emprof_num_fjobs', true);

                                            $job_exp_dur = get_post_meta($p_pkgid, 'jobsearch_field_emprofjob_expiry_time', true);
                                            $job_exp_dur_unit = get_post_meta($p_pkgid, 'jobsearch_field_emprofjob_expiry_time_unit', true);

                                            $pkg_with_promote = get_post_meta($p_pkgid, 'jobsearch_field_emprof_promote_profile', true);
                                            ?>
                                            <div class="jobsearch-modal profpckg-popup-main fade"
                                                 id="JobSearchModalProfPckg<?php echo($p_pkgid) ?>">
                                                <div class="modal-inner-area">&nbsp;</div>
                                                <div class="modal-content-area">
                                                    <div class="modal-box-area">
                                                        <div class="jobsearch-modal-title-box">
                                                            <h2><?php printf(esc_html__('%s Package Detail', 'wp-jobsearch'), $pkghdin_name) ?></h2>
                                                            <span class="modal-close"><i class="fa fa-times"></i></span>
                                                        </div>

                                                        <div class="profpckg-detail-pcon">
                                                            <div class="profpkg-det-item">
                                                                <div class="detitem-label"><?php esc_html_e('Dashboard Sections:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val"><?php echo($show_dash_tabs) ?></div>
                                                            </div>
                                                            <div class="profpkg-det-item">
                                                                <div class="detitem-label"><?php esc_html_e('Profile Fields:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val"><?php echo($show_profile_fields) ?></div>
                                                            </div>
                                                            <?php
                                                            if (isset($show_custf_fields)) {
                                                                ?>
                                                                <div class="profpkg-det-item">
                                                                    <div class="detitem-label"><?php esc_html_e('Custom Fields:', 'wp-jobsearch') ?></div>
                                                                    <div class="detitem-val"><?php echo($show_custf_fields) ?></div>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                            <div class="profpkg-det-item">
                                                                <div class="detitem-label"><?php esc_html_e('Social Fields:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val"><?php echo($show_social_fields) ?></div>
                                                            </div>
                                                            <div class="profpkg-det-item">
                                                                <div class="detitem-label"><?php esc_html_e('Statistics:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val"><?php echo($ppkg_pbase_stats == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                                            </div>
                                                            <div class="profpkg-det-item">
                                                                <div class="detitem-label"><?php esc_html_e('Location:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val"><?php echo($ppkg_pbase_location == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                                            </div>
                                                            <div class="profpkg-det-item">
                                                                <div class="detitem-label"><?php esc_html_e('Account Members:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val"><?php echo($ppkg_pbase_accmembs == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                                            </div>
                                                            <div class="profpkg-det-item">
                                                                <div class="detitem-label"><?php esc_html_e('Employer Team:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val"><?php echo($ppkg_pbase_team == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                                            </div>
                                                            <div class="profpkg-det-item">
                                                                <div class="detitem-label"><?php esc_html_e('Company Photos/Videos:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val"><?php echo($ppkg_pbase_gphotos == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                                            </div>
                                                            <div class="profpkg-det-item">
                                                                <div class="detitem-label"><?php esc_html_e('Jobs:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val">
                                                                    <?php
                                                                    if ($unlimited_numjobs == 'yes') {
                                                                        esc_html_e('Unlimited', 'wp-jobsearch');
                                                                    } else {
                                                                        echo ($pkg_total_jobs);
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                            <div class="profpkg-det-item">
                                                                <div class="detitem-label"><?php esc_html_e('Featured job credits:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val">
                                                                    <?php
                                                                    if ($unlimited_numfjobs == 'yes') {
                                                                        esc_html_e('Unlimited', 'wp-jobsearch');
                                                                    } else {
                                                                        echo ($total_fjobs);
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                            <div class="profpkg-det-item">
                                                                <div class="detitem-label"><?php esc_html_e('Download candidate CVs:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val">
                                                                    <?php
                                                                    if ($unlimited_numcvs == 'yes') {
                                                                        esc_html_e('Unlimited', 'wp-jobsearch');
                                                                    } else {
                                                                        echo ($total_cvs);
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                            <div class="profpkg-det-item">
                                                                <div class="detitem-label"><?php esc_html_e('Promote Profile:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val">
                                                                    <?php
                                                                    if ($pkg_with_promote == 'on') {
                                                                        esc_html_e('Yes', 'wp-jobsearch');
                                                                    } else {
                                                                        esc_html_e('No', 'wp-jobsearch');
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                            <?php
                                                            if ($pkg_with_promote == 'on') {
                                                                $promote_expiry = get_post_meta($p_pkgid, 'jobsearch_field_emprof_promote_expiry_time', true);
                                                                $promote_expiry_unit = get_post_meta($p_pkgid, 'jobsearch_field_emprof_promote_expiry_time_unit', true);
                                                                $unlimited_promote_expiry = get_post_meta($p_pkgid, 'jobsearch_field_unlimited_emprof_promote_exp', true);
                                                                ?>
                                                                <div class="profpkg-det-item">
                                                                    <div class="detitem-label"><?php esc_html_e('Promote Profile Expiry:', 'wp-jobsearch') ?></div>
                                                                    <div class="detitem-val">
                                                                        <?php
                                                                        if ($unlimited_promote_expiry == 'on') {
                                                                            esc_html_e('Unlimited', 'wp-jobsearch');
                                                                        } else {
                                                                            echo ($promote_expiry) . ' ' . jobsearch_get_duration_unit_str($promote_expiry_unit);
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                            <div class="profpkg-item-subscon">
                                                                <a href="javascript:void(0);"
                                                                   class="jobsearch-subsemp-profile-pkg"
                                                                   data-id="<?php echo($p_pkgid) ?>"><?php esc_html_e('Buy Now', 'wp-jobsearch') ?>
                                                                    <span class="pkg-loding-msg"
                                                                          style="display:none;"></span></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }, 11, 1);
                                        //
                                        ?>
                                        <tr>
                                            <td class="profpkg-hding">
                                                <span><?php echo get_the_title($cpropkgs_pkgid) ?></span></td>
                                            <td class="profpkg-titlemid"><span><?php echo($profp_plan_price) ?></span></td>
                                            <td class="profpkg-btnsec">
                                                <div class="profpkg-btn-holdr">
                                                    <a href="javascript:void(0);"
                                                       class="modelprofpkg-pop-btn modelprofpkg-btn-<?php echo($cpropkgs_pkgid) ?>"
                                                       data-id="<?php echo($cpropkgs_pkgid) ?>"><?php esc_html_e('View Detail', 'wp-jobsearch') ?></a>
                                                    <a href="javascript:void(0);"
                                                       class="jobsearch-subsemp-profile-pkg buy-profpkgbtn"
                                                       data-id="<?php echo($cpropkgs_pkgid) ?>"><?php esc_html_e('Buy Now', 'wp-jobsearch') ?>
                                                        <span class="pkg-loding-msg" style="display:none;"></span></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                            <script>
                                jQuery(document).on('click', '.modelprofpkg-pop-btn', function () {
                                    var _this_porfd_id = jQuery(this).attr('data-id');
                                    jobsearch_modal_popup_open('JobSearchModalProfPckg' + _this_porfd_id);
                                });
                            </script>
                        </div>
                        <?php
                    }
                    $all_othrprof_pkghtml = ob_get_clean();
                    if ($user_has_profpkg) {
                        //
                        $popup_args = array('p_order_id' => $usercurnt_attpordr_id, 'p_rand' => $profpkg_rand);
                        add_action('wp_footer', function () use ($popup_args) {

                            $jobsearch__options = get_option('jobsearch_plugin_options');
                            extract(shortcode_atts(array(
                                'p_order_id' => '',
                                'p_rand' => ''
                            ), $popup_args));

                            $p_order_detail = get_post_meta($p_order_id, 'jobsearch_emp_ppkg_fields_list', true);

                            $emp_pkgbase_dashsecs_arr = apply_filters('jobsearch_emp_dash_menu_in_opts', array(
                                'company_profile' => __('Company Profile', 'wp-jobsearch'),
                                'post_new_job' => __('Post a New Job', 'wp-jobsearch'),
                                'manage_jobs' => __('Manage Jobs', 'wp-jobsearch'),
                                'all_applicants' => __('All Applicants', 'wp-jobsearch'),
                                'saved_candidates' => __('Saved Candidates', 'wp-jobsearch'),
                                'packages' => __('Packages', 'wp-jobsearch'),
                                'transactions' => __('Transactions', 'wp-jobsearch'),
                                'followers' => __('Followers', 'wp-jobsearch'),
                                'change_password' => __('Change Password', 'wp-jobsearch'),
                            ));
                            $show_dash_tabs = array();
                            if (isset($p_order_detail['pbase_dashtabs']) && !empty($p_order_detail['pbase_dashtabs'])) {
                                foreach ($p_order_detail['pbase_dashtabs'] as $profpkg_dashtab_key) {
                                    if (isset($emp_pkgbase_dashsecs_arr[$profpkg_dashtab_key])) {
                                        $show_dash_tabs[] = $emp_pkgbase_dashsecs_arr[$profpkg_dashtab_key];
                                    }
                                }
                            }
                            if (!empty($show_dash_tabs)) {
                                $show_dash_tabs = implode(', ', $show_dash_tabs);
                            } else {
                                $show_dash_tabs = '-';
                            }
                            //
                            $emp_pkgbase_profilefields = array(
                                'jobs_cover_img' => esc_html__('Jobs Cover Image', 'wp-jobsearch'),
                                'profile_url' => esc_html__('Profile URL', 'wp-jobsearch'),
                                'public_view' => esc_html__('Profile for Public View', 'wp-jobsearch'),
                                'phone' => esc_html__('Phone', 'wp-jobsearch'),
                                'website' => esc_html__('Website', 'wp-jobsearch'),
                                'sector' => esc_html__('Sector', 'wp-jobsearch'),
                                'founded_date' => esc_html__('Founded Date', 'wp-jobsearch'),
                                'about_company' => esc_html__('About the Company', 'wp-jobsearch'),
                            );
                            $show_profile_fields = array();
                            if (isset($p_order_detail['pbase_profile']) && !empty($p_order_detail['pbase_profile'])) {
                                foreach ($p_order_detail['pbase_profile'] as $profpkg_pfield_key) {
                                    if (isset($emp_pkgbase_profilefields[$profpkg_pfield_key])) {
                                        $show_profile_fields[] = $emp_pkgbase_profilefields[$profpkg_pfield_key];
                                    }
                                }
                            }
                            if (!empty($show_profile_fields)) {
                                $show_profile_fields = implode(', ', $show_profile_fields);
                            } else {
                                $show_profile_fields = '-';
                            }
                            //
                            $employer_social_mlinks = isset($jobsearch__options['employer_social_mlinks']) ? $jobsearch__options['employer_social_mlinks'] : '';
                            $emp_pkgbase_social_arr = array(
                                'facebook' => __('Facebook', 'wp-jobsearch'),
                                'twitter' => __('Twitter', 'wp-jobsearch'),
                                'google_plus' => __('Google Plus', 'wp-jobsearch'),
                                'linkedin' => __('Linkedin', 'wp-jobsearch'),
                                'dribbble' => __('Dribbble', 'wp-jobsearch'),
                            );
                            if (!empty($employer_social_mlinks)) {
                                if (isset($employer_social_mlinks['title']) && is_array($employer_social_mlinks['title'])) {
                                    $field_counter = 0;
                                    foreach ($employer_social_mlinks['title'] as $emp_social_mlink) {
                                        $emp_pkgbase_social_arr['dynm_social' . $field_counter] = $emp_social_mlink;
                                        $field_counter++;
                                    }
                                }
                            }
                            $show_social_fields = array();
                            if (isset($p_order_detail['pbase_social']) && !empty($p_order_detail['pbase_social'])) {
                                foreach ($p_order_detail['pbase_social'] as $profpkg_socialf_key) {
                                    if (isset($emp_pkgbase_social_arr[$profpkg_socialf_key])) {
                                        $show_social_fields[] = $emp_pkgbase_social_arr[$profpkg_socialf_key];
                                    }
                                }
                            }
                            if (!empty($show_social_fields)) {
                                $show_social_fields = implode(', ', $show_social_fields);
                            } else {
                                $show_social_fields = '-';
                            }
                            //
                            $emp_custom_fields_saved_data = get_option('jobsearch_custom_field_employer');
                            if (is_array($emp_custom_fields_saved_data) && sizeof($emp_custom_fields_saved_data) > 0) {
                                $emp_pkgbase_cusfileds_arr = array();
                                foreach ($emp_custom_fields_saved_data as $emp_cus_field_key => $emp_cus_field_kdata) {
                                    $cusfield_label = isset($emp_cus_field_kdata['label']) ? $emp_cus_field_kdata['label'] : '';
                                    $cusfield_name = isset($emp_cus_field_kdata['name']) ? $emp_cus_field_kdata['name'] : '';
                                    if ($cusfield_label != '' && $cusfield_name != '') {
                                        $emp_pkgbase_cusfileds_arr[$cusfield_name] = $cusfield_label;
                                    }
                                }
                                $show_custf_fields = array();
                                if (isset($p_order_detail['pbase_cusfields']) && !empty($p_order_detail['pbase_cusfields'])) {
                                    foreach ($p_order_detail['pbase_cusfields'] as $profpkg_custmf_key) {
                                        if (isset($emp_pkgbase_cusfileds_arr[$profpkg_custmf_key])) {
                                            $show_custf_fields[] = $emp_pkgbase_cusfileds_arr[$profpkg_custmf_key];
                                        }
                                    }
                                }
                                if (!empty($show_custf_fields)) {
                                    $show_custf_fields = implode(', ', $show_custf_fields);
                                } else {
                                    $show_custf_fields = '-';
                                }
                            }
                            $pkghdin_name = jobsearch_get_order_product_name($p_order_id);
                            //
                            
                            $pkg_total_jobs = get_post_meta($p_order_id, 'emprof_num_jobs', true);
                            $unlimited_numjobs = get_post_meta($p_order_id, 'unlimited_numjobs', true);
                            if ($unlimited_numjobs == 'yes') {
                                $pkg_total_jobs = esc_html__('Unlimited', 'wp-jobsearch');
                            }
                            //
                            $total_fjobs = get_post_meta($p_order_id, 'emprof_num_fjobs', true);
                            $unlimited_numfjobs = get_post_meta($p_order_id, 'unlimited_numfjobs', true);
                            if ($unlimited_numfjobs == 'yes') {
                                $total_fjobs = esc_html__('Unlimited', 'wp-jobsearch');
                            }
                            //
                            $total_cvs = get_post_meta($p_order_id, 'emprof_num_cvs', true);
                            $unlimited_numcvs = get_post_meta($p_order_id, 'unlimited_numcvs', true);
                            if ($unlimited_numcvs == 'yes') {
                                $total_cvs = esc_html__('Unlimited', 'wp-jobsearch');
                            }

                            $job_exp_dur = get_post_meta($p_order_id, 'emprofjob_expiry_time', true);
                            $job_exp_dur_unit = get_post_meta($p_order_id, 'emprofjob_expiry_time_unit', true);

                            $used_jobs = jobsearch_emprofpckg_order_used_jobs($p_order_id);
                            $remaining_jobs = jobsearch_emprofpckg_order_remaining_jobs($p_order_id);
                            if ($unlimited_numjobs == 'yes') {
                                $used_jobs = '-';
                                $remaining_jobs = '-';
                            }
                            //
                            $used_fjobs = jobsearch_emprofpckg_order_used_fjobs($p_order_id);
                            $remaining_fjobs = jobsearch_emprofpckg_order_remaining_fjobs($p_order_id);
                            if ($unlimited_numfjobs == 'yes') {
                                $used_fjobs = '-';
                                $remaining_fjobs = '-';
                            }
                            //
                            $used_cvs = jobsearch_emprofpckg_order_used_cvs($p_order_id);
                            $remaining_cvs = jobsearch_emprofpckg_order_remaining_cvs($p_order_id);
                            if ($unlimited_numcvs == 'yes') {
                                $used_cvs = '-';
                                $remaining_cvs = '-';
                            }
                            
                            $pkg_with_promote = get_post_meta($p_order_id, 'emprof_promote_profile', true);
                            ?>
                            <div class="jobsearch-modal profpckg-popup-main fade"
                                 id="JobSearchModalProfPckg<?php echo($p_rand) ?>">
                                <div class="modal-inner-area">&nbsp;</div>
                                <div class="modal-content-area">
                                    <div class="modal-box-area">
                                        <div class="jobsearch-modal-title-box">
                                            <h2><?php printf(esc_html__('%s Package Detail', 'wp-jobsearch'), $pkghdin_name) ?></h2>
                                            <span class="modal-close"><i class="fa fa-times"></i></span>
                                        </div>
                                        <div class="profpckg-detail-pcon">
                                            <div class="profpkg-det-item">
                                                <div class="detitem-label"><?php esc_html_e('Dashboard Sections:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val"><?php echo($show_dash_tabs) ?></div>
                                            </div>
                                            <div class="profpkg-det-item">
                                                <div class="detitem-label"><?php esc_html_e('Profile Fields:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val"><?php echo($show_profile_fields) ?></div>
                                            </div>
                                            <?php
                                            if (isset($show_custf_fields)) {
                                                ?>
                                                <div class="profpkg-det-item">
                                                    <div class="detitem-label"><?php esc_html_e('Custom Fields:', 'wp-jobsearch') ?></div>
                                                    <div class="detitem-val"><?php echo($show_custf_fields) ?></div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                            <div class="profpkg-det-item">
                                                <div class="detitem-label"><?php esc_html_e('Social Fields:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val"><?php echo($show_social_fields) ?></div>
                                            </div>
                                            <div class="profpkg-det-item">
                                                <div class="detitem-label"><?php esc_html_e('Statistics:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val"><?php echo(isset($p_order_detail['pbase_stats']) && $p_order_detail['pbase_stats'] == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                            </div>
                                            <div class="profpkg-det-item">
                                                <div class="detitem-label"><?php esc_html_e('Location:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val"><?php echo(isset($p_order_detail['pbase_location']) && $p_order_detail['pbase_location'] == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                            </div>
                                            <div class="profpkg-det-item">
                                                <div class="detitem-label"><?php esc_html_e('Account Members:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val"><?php echo(isset($p_order_detail['pbase_accmembs']) && $p_order_detail['pbase_accmembs'] == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                            </div>
                                            <div class="profpkg-det-item">
                                                <div class="detitem-label"><?php esc_html_e('Employer Team:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val"><?php echo(isset($p_order_detail['pbase_team']) && $p_order_detail['pbase_team'] == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                            </div>
                                            <div class="profpkg-det-item">
                                                <div class="detitem-label"><?php esc_html_e('Company Photos/Videos:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val"><?php echo(isset($p_order_detail['pbase_gphotos']) && $p_order_detail['pbase_gphotos'] == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                            </div>
                                            <div class="profpkg-det-item">
                                                <div class="detitem-label"><?php esc_html_e('Jobs:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val">
                                                    <?php
                                                    if ($unlimited_numjobs == 'yes') {
                                                        esc_html_e('Unlimited', 'wp-jobsearch');
                                                    } else {
                                                        ?>
                                                        <?php printf(__('Total: %s', 'wp-jobsearch'), $pkg_total_jobs) ?>, 
                                                        <?php printf(__('Used: %s', 'wp-jobsearch'), $used_jobs) ?>, 
                                                        <?php printf(__('Remaininig: %s', 'wp-jobsearch'), $remaining_jobs) ?>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="profpkg-det-item">
                                                <div class="detitem-label"><?php esc_html_e('Featured job credits:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val">
                                                    <?php
                                                    if ($unlimited_numfjobs == 'yes') {
                                                        esc_html_e('Unlimited', 'wp-jobsearch');
                                                    } else {
                                                        ?>
                                                        <?php printf(__('Total: %s', 'wp-jobsearch'), $total_fjobs) ?>, 
                                                        <?php printf(__('Used: %s', 'wp-jobsearch'), $used_fjobs) ?>, 
                                                        <?php printf(__('Remaininig: %s', 'wp-jobsearch'), $remaining_fjobs) ?>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="profpkg-det-item">
                                                <div class="detitem-label"><?php esc_html_e('Download candidate CVs:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val">
                                                    <?php
                                                    if ($unlimited_numcvs == 'yes') {
                                                        esc_html_e('Unlimited', 'wp-jobsearch');
                                                    } else {
                                                        ?>
                                                        <?php printf(__('Total: %s', 'wp-jobsearch'), $total_cvs) ?>, 
                                                        <?php printf(__('Used: %s', 'wp-jobsearch'), $used_cvs) ?>, 
                                                        <?php printf(__('Remaininig: %s', 'wp-jobsearch'), $remaining_cvs) ?>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="profpkg-det-item">
                                                <div class="detitem-label"><?php esc_html_e('Promote Profile:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val">
                                                    <?php
                                                    if ($pkg_with_promote == 'on') {
                                                        esc_html_e('Yes', 'wp-jobsearch');
                                                    } else {
                                                        esc_html_e('No', 'wp-jobsearch');
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <?php
                                            if ($pkg_with_promote == 'on') {
                                                $unlimited_promote_expiry = get_post_meta($p_order_id, 'unlimited_promote_expiry', true);
                                                $promote_expiry_timestamp = get_post_meta($p_order_id, 'emprof_promote_expiry_timestamp', true);
                                                ?>
                                                <div class="profpkg-det-item">
                                                    <div class="detitem-label"><?php esc_html_e('Promote Profile Expiry:', 'wp-jobsearch') ?></div>
                                                    <div class="detitem-val">
                                                        <?php
                                                        if ($unlimited_promote_expiry == 'yes') {
                                                            esc_html_e('Unlimited', 'wp-jobsearch');
                                                        } else {
                                                            echo date_i18n(get_option('date_format'), $promote_expiry_timestamp);
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }, 11, 1);
                        //
                        ?>
                        <div class="user-subsprofpkg-detail">
                            <table>
                                <tbody>
                                <tr>
                                    <td class="profpkg-hding">
                                        <span><?php esc_html_e('Plan Name', 'wp-jobsearch') ?></span></td>
                                    <td class="profpkg-titlemid"><span><?php echo($attpordr_prod_name) ?></span></td>
                                    <td class="profpkg-btnsec">
                                        <div class="profpkg-btn-holdr"><a href="javascript:void(0);"
                                                                          class="modelprofpkg-btn-<?php echo($profpkg_rand) ?>"><?php esc_html_e('View Detail', 'wp-jobsearch') ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="profpkg-hding"><span><?php esc_html_e('Price', 'wp-jobsearch') ?></span>
                                    </td>
                                    <td class="profpkg-titlemid"><span><?php echo($profpckg_order_price) ?></span></td>
                                    <td class="profpkg-btnsec">
                                        <?php
                                        if ($user_has_profpkg) {
                                            ?>
                                            <div class="profpkg-btn-holdr"><a href="javascript:void(0);"
                                                                              class="change-profpkg-planbtn"><?php esc_html_e('Change Plan', 'wp-jobsearch') ?></a>
                                            </div>
                                            <?php
                                        } else {
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="profpkg-hding"><span><?php esc_html_e('Expiry', 'wp-jobsearch') ?></span>
                                    </td>
                                    <?php
                                    $unlimited_pkg = get_post_meta($usercurnt_attpordr_id, 'unlimited_pkg', true);
                                    if ($unlimited_pkg == 'yes') {
                                        ?>
                                        <td class="profpkg-titlemid"><span
                                                    style="color: #00aa00;"><?php echo($usercurnt_attpordr_date) ?></span>
                                            -
                                            <span style="color: #ff0000;"><?php esc_html_e('Never Expire', 'wp-jobsearch') ?></span>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td class="profpkg-titlemid"><span
                                                    style="color: #00aa00;"><?php echo($usercurnt_attpordr_date) ?></span>
                                            -
                                            <span style="color: #ff0000;"><?php echo date_i18n(get_option('date_format'), $usercurnt_attpordr_exp) ?></span>
                                        </td>
                                        <?php
                                    }
                                    if (!$profpkg_is_subscribe) {
                                        ?>
                                        <td class="profpkg-btnsec">
                                            <div class="profpkg-btn-holdr renewal-btn"><a href="javascript:void(0);"
                                                                                          class="jobsearch-subsemp-profile-pkg"
                                                                                          data-id="<?php echo($usercurnt_attpordr_pkgid) ?>"><?php esc_html_e('Renewal', 'wp-jobsearch') ?>
                                                    <span class="pkg-loding-msg" style="display:none;"></span></a></div>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td class="profpkg-btnsec">&nbsp;</td>
                                        <?php
                                    }
                                    ?>
                                </tr>
                                </tbody>
                            </table>
                            <script>
                                jQuery(document).on('click', '.modelprofpkg-btn-<?php echo($profpkg_rand) ?>', function () {
                                    jobsearch_modal_popup_open('JobSearchModalProfPckg<?php echo($profpkg_rand) ?>');
                                });

                                jQuery(document).on('click', '.change-profpkg-planbtn', function () {
                                    jQuery('#jobsearch-profilpkgs-hcon').slideToggle();
                                });
                            </script>
                        </div>
                        <?php
                    }
                    if ($all_othrprof_pkghtml != '') {
                        ?>
                        <div id="jobsearch-profilpkgs-hcon"
                             class="all-profilpkgs-hcon <?php echo($user_has_profpkg ? 'with-has-profpkg' : '') ?>" <?php echo($user_has_profpkg ? 'style="display: none;"' : '') ?>>
                            <div class="all-profilpkgs-head"><?php esc_html_e('Plans List', 'wp-jobsearch') ?></div>
                            <?php
                            echo($all_othrprof_pkghtml);
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        }
        if ($user_stats_switch != 'off') {
            ?>
            <div class="jobsearch-employer-box-section">
                <?php
                ob_start();
                ?>
                <div class="jobsearch-profile-title">
                    <h2><?php esc_html_e('Applications statistics', 'wp-jobsearch') ?></h2>
                </div>
                <?php
                $main_title = ob_get_clean();
                echo apply_filters('jobsearch_empdash_stats_main_title_html', $main_title);

                //
                if ($user_pkg_limits::emp_field_is_locked('stats_defields')) {
                    echo($user_pkg_limits::emp_gen_locked_html());
                } else {
                    ?>
                    <div class="jobsearch-stats-list">
                        <ul>
                            <?php
                            ob_start();
                            ?>
                            <li>
                                <div class="jobsearch-stats-list-wrap">
                                    <h6><?php esc_html_e('Posted jobs', 'wp-jobsearch') ?></h6>
                                    <span><?php echo absint($total_jobs) ?></span>
                                    <small><?php esc_html_e('to find talent', 'wp-jobsearch') ?></small>
                                </div>
                            </li>
                            <?php
                            $stats_html = ob_get_clean();
                            echo apply_filters('jobsearch_emp_dash_stats_post_jobs', $stats_html, $total_jobs, $_job_posts);
                            ob_start();
                            ?>
                            <li>
                                <div class="jobsearch-stats-list-wrap green">
                                    <h6><?php esc_html_e('Viewed', 'wp-jobsearch') ?></h6>
                                    <span><?php echo absint($overall_viewed_cands) ?></span>
                                    <small><?php esc_html_e('CVs against opportunities', 'wp-jobsearch') ?></small>
                                </div>
                            </li>
                            <?php
                            $stats_html = ob_get_clean();
                            echo apply_filters('jobsearch_emp_dash_stats_reviewed_cands', $stats_html, $overall_viewed_cands, $_job_posts);
                            ob_start();
                            ?>
                            <li>
                                <div class="jobsearch-stats-list-wrap light-blue">
                                    <h6><?php esc_html_e('Saved', 'wp-jobsearch') ?></h6>
                                    <span><?php echo absint($employer_resumes_count) ?></span>
                                    <small><?php esc_html_e('Manually saved candidates', 'wp-jobsearch') ?></small>
                                </div>
                            </li>
                            <?php
                            $stats_html = ob_get_clean();
                            echo apply_filters('jobsearch_emp_dash_stats_shortlist_cands', $stats_html, $employer_resumes_count, $_job_posts);
                            ob_start();
                            ?>
                            <li>
                                <div class="jobsearch-stats-list-wrap dark-blue">
                                    <h6><?php esc_html_e('Shortlisted', 'wp-jobsearch') ?></h6>
                                    <span><?php echo absint($job_short_int_count) ?></span>
                                    <small><?php esc_html_e('Shortlisted for interview', 'wp-jobsearch') ?></small>
                                </div>
                            </li>
                            <?php
                            $stats_html = ob_get_clean();
                            echo apply_filters('jobsearch_emp_dash_stats_interviews_cands', $stats_html, $job_short_int_count, $_job_posts);
                            ?>
                        </ul>
                    </div>
                    <?php
                    wp_enqueue_script('morris');
                    wp_enqueue_script('raphael');

                    //
                    ob_start();
                    ?>
                    <div class="jobsearch-applicants-graph">
                        <div class="jobsearch-chart" id="chart-<?php echo absint($rand_id) ?>"></div>
                        <script>
                            jQuery(function () {
                                Morris.Bar({
                                    element: 'chart-<?php echo absint($rand_id) ?>',
                                    data: [
                                        {
                                            y: '<?php printf(esc_html__('Posted Jobs: %s', 'wp-jobsearch'), $total_jobs) ?>, <?php printf(esc_html__('Saved Candidates: %s', 'wp-jobsearch'), $employer_resumes_count) ?>, <?php printf(esc_html__('Viewed CVs: %s', 'wp-jobsearch'), $overall_viewed_cands) ?>, <?php printf(esc_html__('Shortlisted: %s', 'wp-jobsearch'), $job_short_int_count) ?>',
                                            item_1: <?php echo($total_jobs) ?>,
                                            item_2: <?php echo($employer_resumes_count) ?>,
                                            item_3: <?php echo($overall_viewed_cands) ?>,
                                            item_4: <?php echo($job_short_int_count) ?>,
                                        },
                                    ],
                                    barColors: [
                                        "#717171", "#a869d6", "#84c15a", "#008dc9"],
                                    xkey: 'y',
                                    ykeys: ["item_1", "item_2", "item_3", "item_4"],
                                    labels: [
                                        "<?php esc_html_e('Posted Jobs', 'wp-jobsearch') ?>",
                                        "<?php esc_html_e('Saved Candidates', 'wp-jobsearch') ?>",
                                        "<?php esc_html_e('Viewed CVs', 'wp-jobsearch') ?>",
                                        "<?php esc_html_e('Shortlisted', 'wp-jobsearch') ?>"
                                    ]
                                });
                            });
                        </script>
                    </div>
                    <div class="jobsearch-applicants-stats">
                        <div class="jobsearch-applicants-stats-wrap">
                            <i class="fa fa-users"></i>
                            <span><?php echo absint($job_appls_count) ?></span>
                            <small><?php esc_html_e('Total Applicants', 'wp-jobsearch') ?></small>
                        </div>
                        <ul>
                            <li><i class="fa fa-circle"
                                   style="color: #717171;"></i> <?php esc_html_e('Posted Jobs', 'wp-jobsearch') ?></li>
                            <li><i class="fa fa-circle"></i> <?php esc_html_e('Viewed CVs', 'wp-jobsearch') ?></li>
                            <li>
                                <i class="fa fa-circle light-blue"></i> <?php esc_html_e('Saved Candidates', 'wp-jobsearch') ?>
                            </li>
                            <li>
                                <i class="fa fa-circle dark-blue"></i> <?php esc_html_e('Shortlisted', 'wp-jobsearch') ?>
                            </li>
                        </ul>
                    </div>
                    <?php
                    $stats_html = ob_get_clean();
                    echo apply_filters('jobsearch_emp_dash_stats_graph_html', $stats_html, $employer_id, $_job_posts, $rand_id);
                }
                ?>
            </div>
            <?php
        }
        echo apply_filters('jobsearch_emp_dash_appstats_after_html', '');
        ?>
        <div class="jobsearch-employer-box-section">

            <div class="jobsearch-profile-title">
                <h2><?php esc_html_e('Recent Applicants', 'wp-jobsearch') ?></h2>
            </div>
            <?php
            $candidate_skills = isset($jobsearch_plugin_options['jobsearch_candidate_skills']) ? $jobsearch_plugin_options['jobsearch_candidate_skills'] : '';
            if ($candidate_skills == 'on') {
                $low_skills_clr = isset($jobsearch_plugin_options['skill_low_set_color']) && $jobsearch_plugin_options['skill_low_set_color'] != '' ? $jobsearch_plugin_options['skill_low_set_color'] : '';
                $med_skills_clr = isset($jobsearch_plugin_options['skill_med_set_color']) && $jobsearch_plugin_options['skill_med_set_color'] != '' ? $jobsearch_plugin_options['skill_med_set_color'] : '';
                $high_skills_clr = isset($jobsearch_plugin_options['skill_high_set_color']) && $jobsearch_plugin_options['skill_high_set_color'] != '' ? $jobsearch_plugin_options['skill_high_set_color'] : '';
                $comp_skills_clr = isset($jobsearch_plugin_options['skill_ahigh_set_color']) && $jobsearch_plugin_options['skill_ahigh_set_color'] != '' ? $jobsearch_plugin_options['skill_ahigh_set_color'] : '';
            }

            $emp_email_apps_tab = isset($jobsearch_plugin_options['emp_dash_email_applics']) ? $jobsearch_plugin_options['emp_dash_email_applics'] : '';
            $have_jobs = $have_candidates = false;
            if (!empty($_job_posts)) {
                $appjob_found_count = 0;
                $have_jobs = true;
                foreach ($_job_posts as $_job_id) {

                    $job_views_count = get_post_meta($_job_id, 'jobsearch_job_views_count', true);
                    $job_expiry_date = get_post_meta($_job_id, 'jobsearch_field_job_expiry_date', true);
                    $job_expiry_date = $job_expiry_date == '' ? strtotime(current_time('Y-m-d H:i:s')) : $job_expiry_date;
                    $job_salary = jobsearch_job_offered_salary($_job_id);

                    $job_aply_type = get_post_meta($_job_id, 'jobsearch_field_job_apply_type', true);

                    if ($job_aply_type == 'with_email') {
                        if ($emp_email_apps_tab == 'on') {
                            $apllicans_link = add_query_arg(array('tab' => 'all-applicants', 'view' => 'email-applicants', 'job_id' => $_job_id), $page_url);
                        } else {
                            $apllicans_link = 'javascript:void(0);';
                        }
                        $job_applicants_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts AS posts"
                            . " LEFT JOIN $wpdb->postmeta AS postmeta ON(posts.ID = postmeta.post_id) "
                            . " WHERE post_type=%s AND (postmeta.meta_key = 'jobsearch_app_job_id' AND postmeta.meta_value={$_job_id})", 'email_apps'));

                        $job_applicants_list = get_post_meta($_job_id, 'jobsearch_job_emailapps_list', true);

                        if (!empty($job_applicants_list)) {

                            ob_start();
                            ?>
                            <div class="jobsearch-job-title">
                                <h2 class="jobsearch-pst-title"><a
                                            href="<?php echo($apllicans_link) ?>"><?php echo get_the_title($_job_id) ?></a>
                                </h2>
                            </div>
                            <?php
                            $list_title_html = ob_get_clean();
                            echo apply_filters('jobsearch_empdash_stats_jobslist_jobtitle_html', $list_title_html, $page_url, $_job_id);
                            ?>
                            <div class="jobsearch-recent-applicants-nav">
                                <ul>
                                    <?php
                                    ob_start();
                                    ?>
                                    <li>
                                        <a <?php echo('href="' . $apllicans_link . '"') ?>><span><?php echo absint($job_applicants_count) ?></span>
                                            <small><?php esc_html_e('Total applicants', 'wp-jobsearch') ?></small>
                                        </a></li>
                                    <?php
                                    $list_tapps_html = ob_get_clean();
                                    echo apply_filters('jobsearch_empdash_stats_jobslist_tapps', $list_tapps_html, $job_applicants_count, $_job_id);

                                    ob_start();
                                    if ($job_salary != '') {
                                        ?>
                                        <li>
                                            <small><?php echo($job_salary) ?><?php esc_html_e('Job Salary', 'wp-jobsearch') ?></small>
                                        </li>
                                        <?php
                                    }
                                    $list_jslary_html = ob_get_clean();
                                    echo apply_filters('jobsearch_empdash_stats_jobslist_jslary', $list_jslary_html, $job_salary, $_job_id);

                                    ob_start();
                                    ?>
                                    <li><span><?php echo absint($job_views_count) ?></span>
                                        <small><?php esc_html_e('Total visits', 'wp-jobsearch') ?></small>
                                    </li>
                                    <?php
                                    $list_tvists_html = ob_get_clean();
                                    echo apply_filters('jobsearch_empdash_stats_jobslist_tvists', $list_tvists_html, $job_views_count, $_job_id);
                                    ?>
                                    <li>
                                        <small><?php echo apply_filters('jobsearch_emp_dash_stats_jobsitem_expirydate', sprintf(esc_html__('Expiry Date: %s', 'wp-jobsearch'), date_i18n(get_option('date_format'), $job_expiry_date)), $job_expiry_date) ?></small>
                                    </li>
                                </ul>
                            </div>
                            <div class="jobsearch-candidate jobsearch-candidate-default  jobsearch-applicns-candidate">
                                <ul class="jobsearch-row">
                                    <?php
                                    $apps_start = 0;
                                    $apps_offset = 3;
                                    $job_applicants_list = array_slice($job_applicants_list, $apps_start, $apps_offset);

                                    foreach ($job_applicants_list as $apply_data) {
                                        $app_id = isset($apply_data['app_id']) ? $apply_data['app_id'] : '';
                                        $app_obj = get_post($app_id);
                                        $app_post_date = isset($app_obj->post_date) ? $app_obj->post_date : '';
                                        $job_id = isset($apply_data['id']) ? $apply_data['id'] : '';
                                        $email = isset($apply_data['email']) ? $apply_data['email'] : '';
                                        $user_email = isset($apply_data['user_email']) ? $apply_data['user_email'] : '';

                                        //
                                        $first_name = isset($apply_data['username']) ? $apply_data['username'] : '';
                                        $last_name = isset($apply_data['user_surname']) ? $apply_data['user_surname'] : '';
                                        $user_phone = isset($apply_data['user_phone']) ? $apply_data['user_phone'] : '';
                                        $user_msg = isset($apply_data['user_msg']) ? $apply_data['user_msg'] : '';
                                        $job_title = isset($apply_data['job_title']) ? $apply_data['job_title'] : '';
                                        $current_salary = isset($apply_data['current_salary']) ? $apply_data['current_salary'] : '';
                                        $att_file_path = isset($apply_data['att_file_path']) ? $apply_data['att_file_path'] : '';
                                        $att_file_args = isset($apply_data['att_file_args']) ? $apply_data['att_file_args'] : '';

                                        $current_salary = jobsearch_get_price_format($current_salary);

                                        $user_def_avatar_url = get_avatar_url($user_email, array('size' => 69));
                                        $_candidate_id = '';
                                        $user_page_url = 'javascript:void(0);';
                                        $candidate_title = $first_name . ' ' . $last_name;
                                        $candidate_company_str = $job_title;
                                        $jobsearch_candi_approved = '';

                                        if (email_exists($user_email)) {
                                            $_user_obj = get_user_by('email', $user_email);
                                            $_user_id = isset($_user_obj->ID) ? $_user_obj->ID : '';

                                            if (jobsearch_user_is_candidate($_user_id)) {
                                                $_candidate_id = jobsearch_get_user_candidate_id($_user_id);
                                                $user_page_url = get_permalink($_candidate_id);
                                                $candidate_title = get_the_title($_candidate_id);

                                                $jobsearch_candidate_jobtitle = get_post_meta($_candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                                                $jobsearch_candidate_company_name = get_post_meta($_candidate_id, 'jobsearch_field_candidate_company_name', true);
                                                $jobsearch_candidate_company_url = get_post_meta($_candidate_id, 'jobsearch_field_candidate_company_url', true);
                                                $candidate_company_str = '';
                                                if ($jobsearch_candidate_jobtitle != '') {
                                                    $candidate_company_str = $jobsearch_candidate_jobtitle;
                                                }
                                                $candidate_city_title = jobsearch_post_city_contry_txtstr($_candidate_id, true, false, true);

                                                $jobsearch_candi_approved = get_post_meta($_candidate_id, 'jobsearch_field_candidate_approved', true);
                                                $final_color = '';

                                                if ($candidate_skills == 'on') {

                                                    $overall_candidate_skills = get_post_meta($_candidate_id, 'overall_skills_percentage', true);
                                                    if ($overall_candidate_skills <= 25 && $low_skills_clr != '') {
                                                        $final_color = 'style="color: ' . $low_skills_clr . ';"';
                                                    } else if ($overall_candidate_skills > 25 && $overall_candidate_skills <= 50 && $med_skills_clr != '') {
                                                        $final_color = 'style="color: ' . $med_skills_clr . ';"';
                                                    } else if ($overall_candidate_skills > 50 && $overall_candidate_skills <= 75 && $high_skills_clr != '') {
                                                        $final_color = 'style="color: ' . $high_skills_clr . ';"';
                                                    } else if ($overall_candidate_skills > 75 && $comp_skills_clr != '') {
                                                        $final_color = 'style="color: ' . $comp_skills_clr . ';"';
                                                    }
                                                }
                                            }
                                        }
                                        $user_def_avatar_url = jobsearch_candidate_img_url_comn($_candidate_id);

                                        ?>
                                        <li class="jobsearch-column-12">
                                            <div class="jobsearch-candidate-default-wrap">
                                                <?php
                                                if ($user_def_avatar_url != '') {
                                                    ?>
                                                    <figure>
                                                        <a href="<?php echo($user_page_url) ?>">
                                                            <img src="<?php echo($user_def_avatar_url) ?>" alt="">
                                                        </a>
                                                    </figure>
                                                    <?php
                                                }
                                                ?>
                                                <div class="jobsearch-candidate-default-text">
                                                    <div class="jobsearch-candidate-default-left">
                                                        <h2 class="jobsearch-pst-title">
                                                            <a href="<?php echo($user_page_url); ?>">
                                                                <?php echo esc_html(wp_trim_words($candidate_title, 5)); ?>
                                                            </a>
                                                            <?php
                                                            if ($jobsearch_candi_approved == 'on') {
                                                                ?>
                                                                <i class="jobsearch-icon jobsearch-check-mark" <?php echo($final_color) ?>></i>
                                                                <?php
                                                            }
                                                            //
                                                            echo apply_filters('jobsearch_dash_stats_apps_list_slist_btn', '', $_candidate_id, $_job_id);
                                                            ?>
                                                        </h2>
                                                        <ul>
                                                            <?php
                                                            if (isset($candidate_company_str) && $candidate_company_str != '') {
                                                                ?>
                                                                <li><?php echo($candidate_company_str); ?></li>
                                                                <?php
                                                            }
                                                            ob_start();
                                                            if (isset($candidate_city_title) && !empty($candidate_city_title) && $all_location_allow == 'on') {
                                                                ?>
                                                                <li>
                                                                    <i class="fa fa-map-marker"></i> <?php echo esc_html($candidate_city_title); ?>
                                                                </li>
                                                                <?php
                                                            }
                                                            $loc_html = ob_get_clean();
                                                            echo apply_filters('jobsearch_emp_dash_stats_apps_list_lochtml', $loc_html, $_candidate_id, $_job_id);

                                                            ob_start();
                                                            if ($user_email != '') {
                                                                ?>
                                                                <li>
                                                                    <i class="fa fa-envelope"></i> <a
                                                                            href="mailto:<?php echo($user_email) ?>"><?php echo($user_email) ?></a>
                                                                </li>
                                                                <?php
                                                            }
                                                            $email_html = ob_get_clean();
                                                            echo apply_filters('jobsearch_emp_dash_stats_apps_list_emailhtml', $email_html, $_candidate_id, $_job_id);
                                                            ?>
                                                        </ul>
                                                        <?php
                                                        echo apply_filters('jobsearch_empdash_stats_candlits_aftr_detul', '', $_candidate_id, $_job_id);
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                            <?php
                            $appjob_found_count++;
                        }

                    } else {

                        $job_applicants_list = get_post_meta($_job_id, 'jobsearch_job_applicants_list', true);
                        $job_applicants_list = $job_applicants_list != '' ? explode(',', $job_applicants_list) : array();
                        $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
                        //var_dump($job_applicants_list);
                        $job_applicants_count = count($job_applicants_list);

                        $job_short_int_list = get_post_meta($_job_id, '_job_short_interview_list', true);
                        $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : '';
                        if (empty($job_short_int_list)) {
                            $job_short_int_list = array();
                        }

                        if (!empty($job_applicants_list)) {
                            arsort($job_applicants_list);

                            ob_start();
                            ?>
                            <div class="jobsearch-job-title">
                                <h2 class="jobsearch-pst-title"><a
                                            href="<?php echo add_query_arg(array('tab' => 'manage-jobs', 'view' => 'applicants', 'job_id' => $_job_id), $page_url) ?>"><?php echo get_the_title($_job_id) ?></a>
                                </h2>
                            </div>
                            <?php
                            $list_title_html = ob_get_clean();
                            echo apply_filters('jobsearch_empdash_stats_jobslist_jobtitle_html', $list_title_html, $page_url, $_job_id);
                            ?>
                            <div class="jobsearch-recent-applicants-nav">
                                <ul>
                                    <?php
                                    ob_start();
                                    ?>
                                    <li>
                                        <a href="<?php echo add_query_arg(array('tab' => 'manage-jobs', 'view' => 'applicants', 'job_id' => $_job_id), $page_url) ?>"><span><?php echo absint($job_applicants_count) ?></span>
                                            <small><?php esc_html_e('Total applicants', 'wp-jobsearch') ?></small>
                                        </a></li>
                                    <?php
                                    $list_tapps_html = ob_get_clean();
                                    echo apply_filters('jobsearch_empdash_stats_jobslist_tapps', $list_tapps_html, $job_applicants_count, $_job_id);

                                    ob_start();
                                    if ($job_salary != '') {
                                        ?>
                                        <li>
                                            <small><?php echo($job_salary) ?><?php esc_html_e('Job Salary', 'wp-jobsearch') ?></small>
                                        </li>
                                        <?php
                                    }
                                    $list_jslary_html = ob_get_clean();
                                    echo apply_filters('jobsearch_empdash_stats_jobslist_jslary', $list_jslary_html, $job_salary, $_job_id);

                                    ob_start();
                                    ?>
                                    <li><span><?php echo absint($job_views_count) ?></span>
                                        <small><?php esc_html_e('Total visits', 'wp-jobsearch') ?></small>
                                    </li>
                                    <?php
                                    $list_tvists_html = ob_get_clean();
                                    echo apply_filters('jobsearch_empdash_stats_jobslist_tvists', $list_tvists_html, $job_views_count, $_job_id);
                                    ?>
                                    <li>
                                        <small><?php echo apply_filters('jobsearch_emp_dash_stats_jobsitem_expirydate', sprintf(esc_html__('Expiry Date: %s', 'wp-jobsearch'), date_i18n(get_option('date_format'), $job_expiry_date)), $job_expiry_date) ?></small>
                                    </li>
                                </ul>
                            </div>
                            <div class="jobsearch-candidate jobsearch-candidate-default  jobsearch-applicns-candidate">
                                <ul class="jobsearch-row">
                                    <?php
                                    $app_counter = 1;
                                    foreach ($job_applicants_list as $candidate_id) {
                                        $have_candidates = true;
                                        $candidate_user_id = jobsearch_get_candidate_user_id($candidate_id);
                                        if (absint($candidate_user_id) <= 0) {
                                            continue;
                                        }
                                        $user_def_avatar_url = get_avatar_url($candidate_user_id, array('size' => 69));

                                        $post_thumbnail_src = jobsearch_candidate_img_url_comn($candidate_id);
                                        $jobsearch_candidate_approved = get_post_meta($candidate_id, 'jobsearch_field_candidate_approved', true);
                                        $get_candidate_location = get_post_meta($candidate_id, 'jobsearch_field_location_address', true);

                                        $candidate_city_title = jobsearch_post_city_contry_txtstr($candidate_id, true, false, true);

                                        $jobsearch_candidate_jobtitle = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                                        $jobsearch_candidate_company_name = get_post_meta($candidate_id, 'jobsearch_field_candidate_company_name', true);
                                        $jobsearch_candidate_company_url = get_post_meta($candidate_id, 'jobsearch_field_candidate_company_url', true);
                                        $candidate_company_str = '';
                                        if ($jobsearch_candidate_jobtitle != '') {
                                            $candidate_company_str .= $jobsearch_candidate_jobtitle;
                                        }
                                        $candidate_user_obj = get_user_by('ID', $candidate_user_id);
                                        $candidate_user_email = isset($candidate_user_obj->user_email) ? $candidate_user_obj->user_email : '';

                                        $final_color = '';

                                        if ($candidate_skills == 'on') {

                                            $overall_candidate_skills = get_post_meta($candidate_id, 'overall_skills_percentage', true);
                                            if ($overall_candidate_skills <= 25 && $low_skills_clr != '') {
                                                $final_color = 'style="color: ' . $low_skills_clr . ';"';
                                            } else if ($overall_candidate_skills > 25 && $overall_candidate_skills <= 50 && $med_skills_clr != '') {
                                                $final_color = 'style="color: ' . $med_skills_clr . ';"';
                                            } else if ($overall_candidate_skills > 50 && $overall_candidate_skills <= 75 && $high_skills_clr != '') {
                                                $final_color = 'style="color: ' . $high_skills_clr . ';"';
                                            } else if ($overall_candidate_skills > 75 && $comp_skills_clr != '') {
                                                $final_color = 'style="color: ' . $comp_skills_clr . ';"';
                                            }
                                        }
                                        ?>
                                        <li class="jobsearch-column-12">
                                            <div class="jobsearch-candidate-default-wrap">
                                                <?php
                                                if ($post_thumbnail_src != '') {
                                                    ?>
                                                    <figure>
                                                        <a href="<?php the_permalink(); ?>">
                                                            <img src="<?php echo($post_thumbnail_src) ?>" alt="">
                                                        </a>
                                                    </figure>
                                                    <?php
                                                }
                                                ?>
                                                <div class="jobsearch-candidate-default-text">
                                                    <div class="jobsearch-candidate-default-left">
                                                        <h2 class="jobsearch-pst-title">
                                                            <a href="<?php echo esc_url(get_permalink($candidate_id)); ?>">
                                                                <?php echo esc_html(wp_trim_words(get_the_title($candidate_id), 5)); ?>
                                                            </a>
                                                            <?php
                                                            if ($jobsearch_candidate_approved == 'on') {
                                                                ?>
                                                                <i class="jobsearch-icon jobsearch-check-mark" <?php echo($final_color) ?>></i>
                                                                <?php
                                                            }
                                                            //
                                                            echo apply_filters('jobsearch_dash_stats_apps_list_slist_btn', '', $candidate_id, $_job_id);
                                                            ?>
                                                        </h2>
                                                        <ul>
                                                            <?php
                                                            if ($candidate_company_str != '') {
                                                                ?>
                                                                <li><?php echo($candidate_company_str); ?></li>
                                                                <?php
                                                            }
                                                            ob_start();
                                                            if (!empty($candidate_city_title) && $all_location_allow == 'on') {
                                                                ?>
                                                                <li>
                                                                    <i class="fa fa-map-marker"></i> <?php echo esc_html($candidate_city_title); ?>
                                                                </li>
                                                                <?php
                                                            }
                                                            $loc_html = ob_get_clean();
                                                            echo apply_filters('jobsearch_emp_dash_stats_apps_list_lochtml', $loc_html, $candidate_id, $_job_id);

                                                            ob_start();
                                                            if ($candidate_user_email != '') {
                                                                ?>
                                                                <li>
                                                                    <i class="fa fa-envelope"></i> <a
                                                                            href="mailto:<?php echo($candidate_user_email) ?>"><?php echo($candidate_user_email) ?></a>
                                                                </li>
                                                                <?php
                                                            }
                                                            $email_html = ob_get_clean();
                                                            echo apply_filters('jobsearch_emp_dash_stats_apps_list_emailhtml', $email_html, $candidate_id, $_job_id);
                                                            ?>
                                                        </ul>
                                                        <?php
                                                        echo apply_filters('jobsearch_empdash_stats_candlits_aftr_detul', '', $candidate_id, $_job_id);
                                                        ?>
                                                    </div>
                                                    <?php
                                                    ob_start();
                                                    if (in_array($candidate_id, $job_short_int_list)) {
                                                        ?>
                                                        <a href="javascript:void(0);"
                                                           class="jobsearch-candidate-default-btn"><i
                                                                    class="jobsearch-icon jobsearch-add-list"></i> <?php esc_html_e('Shortlisted', 'wp-jobsearch') ?>
                                                        </a>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <a href="javascript:void(0);"
                                                           class="jobsearch-candidate-default-btn shortlist-cand-to-intrview ajax-enable"
                                                           data-jid="<?php echo($_job_id) ?>"
                                                           data-cid="<?php echo($candidate_id) ?>"><i
                                                                    class="jobsearch-icon jobsearch-add-list"></i> <?php esc_html_e('Shortlist for Interview', 'wp-jobsearch') ?>
                                                            <span class="app-loader"></span></a>
                                                        <?php
                                                    }
                                                    $shlist_html = ob_get_clean();
                                                    echo apply_filters('jobsearch_emp_dash_stats_apps_shlist_html', $shlist_html, $candidate_id, $_job_id);
                                                    ?>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                        if ($app_counter >= 3) {
                                            break;
                                        }
                                        $app_counter++;
                                    }
                                    ?>
                                </ul>
                            </div>
                            <?php
                            $appjob_found_count++;
                        }
                    }
                    $jobs_break_point = apply_filters('jobsearch_empdash_stats_job_posts_list_count', 20);
                    if ($appjob_found_count >= $jobs_break_point) {
                        break;
                    }
                }
            } else {
                ?>
                <p><?php esc_html_e('No Applicants found.', 'wp-jobsearch') ?></p>
                <?php
            }
            if ($have_jobs === true && $have_candidates === false) {
                ?>
                <p><?php esc_html_e('No Applicants found.', 'wp-jobsearch') ?></p>
                <?php
            }
            ?>
        </div>
        <?php
        echo apply_filters('jobsearch_emp_dash_appstats_after_recapps', '');
        ?>
    </div>
    <?php
}    