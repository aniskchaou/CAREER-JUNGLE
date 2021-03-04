<?php

use WP_Jobsearch\Package_Limits;

global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings, $wpdb;
$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);

$user_email_adr = isset($user_obj->user_email) ? $user_obj->user_email : '';

$user_pkg_limits = new Package_Limits;

$page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
$page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);

$candidate_id = jobsearch_get_user_candidate_id($user_id);

$reults_per_page = isset($jobsearch_plugin_options['user-dashboard-per-page']) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;
$all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';

$user_stats_switch = isset($jobsearch_plugin_options['user_stats_switch']) ? $jobsearch_plugin_options['user_stats_switch'] : '';

$page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;

if ($candidate_id > 0) {
    
    wp_enqueue_script('jobsearch-morris');
    wp_enqueue_script('jobsearch-raphael');

    $cand_pkgbase_profile = isset($jobsearch_plugin_options['cand_pkg_base_profile']) ? $jobsearch_plugin_options['cand_pkg_base_profile'] : '';

    $rand_id = rand(1000000, 9999999);

    $page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;

    $user_applied_jobs_list = array();
    $user_applied_jobs_liste = get_user_meta($user_id, 'jobsearch-user-jobs-applied-list', true);
    if (!empty($user_applied_jobs_liste)) {
        foreach ($user_applied_jobs_liste as $er_applied_jobs_list_key => $er_applied_jobs_list_val) {
            $job_id = isset($er_applied_jobs_list_val['post_id']) ? $er_applied_jobs_list_val['post_id'] : 0;
            if (get_post_type($job_id) == 'job') {
                $user_applied_jobs_list[$er_applied_jobs_list_key] = $er_applied_jobs_list_val;
            }
        }
    }

    $user_applied_jobs_count = empty($user_applied_jobs_list) ? 0 : count($user_applied_jobs_list);
    
    $email_apps_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts AS posts"
    . " LEFT JOIN $wpdb->postmeta AS postmeta ON(posts.ID = postmeta.post_id) "
    . " WHERE post_type=%s AND (postmeta.meta_key = 'jobsearch_app_user_email' AND postmeta.meta_value = '{$user_email_adr}')", 'email_apps'));
    if ($email_apps_count > 0) {
        $user_applied_jobs_count += $email_apps_count;
    }

    $fav_jobs_list = array();
    $candidate_fav_jobs_liste = get_post_meta($candidate_id, 'jobsearch_fav_jobs_list', true);
    $candidate_fav_jobs_liste = $candidate_fav_jobs_liste != '' ? explode(',', $candidate_fav_jobs_liste) : array();
    if (!empty($candidate_fav_jobs_liste)) {
        foreach ($candidate_fav_jobs_liste as $er_fav_job_list) {
            $job_id = $er_fav_job_list;
            if (get_post_type($job_id) == 'job') {
                $fav_jobs_list[] = $job_id;
            }
        }
    }
    if (!empty($fav_jobs_list)) {
        $fav_jobs_list = implode(',', $fav_jobs_list);
    } else {
        $fav_jobs_list = '';
    }

    $fav_jobs_list = $fav_jobs_list != '' ? explode(',', $fav_jobs_list) : array();
    $fav_jobs_list_count = empty($fav_jobs_list) ? 0 : count($fav_jobs_list);

    $args = array(
        'author' => $user_id,
        'post_type' => 'job-alert',
        'posts_per_page' => 1,
        'orderby' => 'post_date',
        'order' => 'DESC',
    );
    $job_alerts = new WP_Query($args);

    $total_alerts = $job_alerts->found_posts;

    wp_reset_postdata();

    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => 1,
        'post_status' => 'wc-completed',
        'order' => 'DESC',
        'orderby' => 'ID',
        'meta_query' => array(
            array(
                'key' => 'jobsearch_order_attach_with',
                'value' => 'package',
                'compare' => '=',
            ),
            array(
                'key' => 'package_type',
                'value' => array('candidate'),
                'compare' => 'IN',
            ),
            array(
                'key' => 'jobsearch_order_user',
                'value' => $user_id,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);
    $total_pkgs = $pkgs_query->found_posts;
    wp_reset_postdata();
    ?>
    <div class="jobsearch-employer-dasboard">
        <?php
        echo apply_filters('jobsearch_candash_stats_before_start', '', $candidate_id);
        if ($cand_pkgbase_profile == 'on') {
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
                        $profpkg_is_subscribe = jobsearch_cand_profile_pckg_is_subscribed($usercurnt_attpordr_pkgid, $user_id);

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
                                'value' => 'candidate_profile',
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

                                            $ppkg_pbase_dashtabs = get_post_meta($p_pkgid, 'jobsearch_field_cand_pbase_dashtabs', true);
                                            $ppkg_pbase_profile = get_post_meta($p_pkgid, 'jobsearch_field_cand_pbase_profile', true);
                                            $ppkg_pbase_social = get_post_meta($p_pkgid, 'jobsearch_field_cand_pbase_social', true);
                                            $ppkg_pbase_cusfields = get_post_meta($p_pkgid, 'jobsearch_field_cand_pbase_cusfields', true);
                                            $ppkg_pbase_stats = get_post_meta($p_pkgid, 'jobsearch_field_cand_pbase_stats', true);
                                            $ppkg_pbase_location = get_post_meta($p_pkgid, 'jobsearch_field_cand_pbase_location', true);
                                            $ppkg_pbase_coverltr = get_post_meta($p_pkgid, 'jobsearch_field_cand_pbase_coverltr', true);
                                            $ppkg_pbase_resmedu = get_post_meta($p_pkgid, 'jobsearch_field_cand_pbase_resmedu', true);
                                            $ppkg_pbase_resmexp = get_post_meta($p_pkgid, 'jobsearch_field_cand_pbase_resmexp', true);
                                            $ppkg_pbase_resmport = get_post_meta($p_pkgid, 'jobsearch_field_cand_pbase_resmport', true);
                                            $ppkg_pbase_resmskills = get_post_meta($p_pkgid, 'jobsearch_field_cand_pbase_resmskills', true);
                                            $ppkg_pbase_resmawards = get_post_meta($p_pkgid, 'jobsearch_field_cand_pbase_resmawards', true);

                                            $cand_pkgbase_dashsecs_arr = apply_filters('jobsearch_cand_dash_menu_in_opts', array(
                                                'my_profile' => __('My Profile', 'wp-jobsearch'),
                                                'my_resume' => __('My Resume', 'wp-jobsearch'),
                                                'applied_jobs' => __('Applied Jobs', 'wp-jobsearch'),
                                                'cv_manager' => __('CV Manager', 'wp-jobsearch'),
                                                'fav_jobs' => __('Favorite Jobs', 'wp-jobsearch'),
                                                'packages' => __('Packages', 'wp-jobsearch'),
                                                'transactions' => __('Transactions', 'wp-jobsearch'),
                                                'following' => __('Following', 'wp-jobsearch'),
                                                'change_password' => __('Change Password', 'wp-jobsearch'),
                                            ));
                                            $show_dash_tabs = array();
                                            if (!empty($ppkg_pbase_dashtabs)) {
                                                foreach ($ppkg_pbase_dashtabs as $profpkg_dashtab_key) {
                                                    if (isset($cand_pkgbase_dashsecs_arr[$profpkg_dashtab_key])) {
                                                        $show_dash_tabs[] = $cand_pkgbase_dashsecs_arr[$profpkg_dashtab_key];
                                                    }
                                                }
                                            }
                                            if (!empty($show_dash_tabs)) {
                                                $show_dash_tabs = implode(', ', $show_dash_tabs);
                                            } else {
                                                $show_dash_tabs = '-';
                                            }
                                            //
                                            $cand_pkgbase_profilefields = array(
                                                'cover_img' => esc_html__('Cover Image', 'wp-jobsearch'),
                                                'profile_url' => esc_html__('Profile URL', 'wp-jobsearch'),
                                                'public_view' => esc_html__('Profile for Public View', 'wp-jobsearch'),
                                                'date_of_birth' => esc_html__('Date of Birth', 'wp-jobsearch'),
                                                'phone' => esc_html__('Phone', 'wp-jobsearch'),
                                                'sector' => esc_html__('Sector', 'wp-jobsearch'),
                                                'job_title' => esc_html__('Job Title', 'wp-jobsearch'),
                                                'salary' => esc_html__('Salary', 'wp-jobsearch'),
                                                'about_desc' => esc_html__('Description', 'wp-jobsearch'),
                                            );
                                            $show_profile_fields = array();
                                            if (!empty($ppkg_pbase_profile)) {
                                                foreach ($ppkg_pbase_profile as $profpkg_pfield_key) {
                                                    if (isset($cand_pkgbase_profilefields[$profpkg_pfield_key])) {
                                                        $show_profile_fields[] = $cand_pkgbase_profilefields[$profpkg_pfield_key];
                                                    }
                                                }
                                            }
                                            if (!empty($show_profile_fields)) {
                                                $show_profile_fields = implode(', ', $show_profile_fields);
                                            } else {
                                                $show_profile_fields = '-';
                                            }
                                            //
                                            $candidate_social_mlinks = isset($jobsearch__options['candidate_social_mlinks']) ? $jobsearch__options['candidate_social_mlinks'] : '';
                                            $cand_pkgbase_social_arr = array(
                                                'facebook' => __('Facebook', 'wp-jobsearch'),
                                                'twitter' => __('Twitter', 'wp-jobsearch'),
                                                'google_plus' => __('Google Plus', 'wp-jobsearch'),
                                                'linkedin' => __('Linkedin', 'wp-jobsearch'),
                                                'dribbble' => __('Dribbble', 'wp-jobsearch'),
                                            );
                                            if (!empty($candidate_social_mlinks)) {
                                                if (isset($candidate_social_mlinks['title']) && is_array($candidate_social_mlinks['title'])) {
                                                    $field_counter = 0;
                                                    foreach ($candidate_social_mlinks['title'] as $cand_social_mlink) {
                                                        $cand_pkgbase_social_arr['dynm_social' . $field_counter] = $cand_social_mlink;
                                                        $field_counter++;
                                                    }
                                                }
                                            }
                                            $show_social_fields = array();
                                            if (!empty($ppkg_pbase_social)) {
                                                foreach ($ppkg_pbase_social as $profpkg_socialf_key) {
                                                    if (isset($cand_pkgbase_social_arr[$profpkg_socialf_key])) {
                                                        $show_social_fields[] = $cand_pkgbase_social_arr[$profpkg_socialf_key];
                                                    }
                                                }
                                            }
                                            if (!empty($show_social_fields)) {
                                                $show_social_fields = implode(', ', $show_social_fields);
                                            } else {
                                                $show_social_fields = '-';
                                            }
                                            //
                                            $cand_custom_fields_saved_data = get_option('jobsearch_custom_field_candidate');
                                            if (is_array($cand_custom_fields_saved_data) && sizeof($cand_custom_fields_saved_data) > 0) {
                                                $cand_pkgbase_cusfileds_arr = array();
                                                foreach ($cand_custom_fields_saved_data as $cand_cus_field_key => $cand_cus_field_kdata) {
                                                    $cusfield_label = isset($cand_cus_field_kdata['label']) ? $cand_cus_field_kdata['label'] : '';
                                                    $cusfield_name = isset($cand_cus_field_kdata['name']) ? $cand_cus_field_kdata['name'] : '';
                                                    if ($cusfield_label != '' && $cusfield_name != '') {
                                                        $cand_pkgbase_cusfileds_arr[$cusfield_name] = $cusfield_label;
                                                    }
                                                }
                                                $show_custf_fields = array();
                                                if (!empty($ppkg_pbase_cusfields)) {
                                                    foreach ($ppkg_pbase_cusfields as $profpkg_custmf_key) {
                                                        if (isset($cand_pkgbase_cusfileds_arr[$profpkg_custmf_key])) {
                                                            $show_custf_fields[] = $cand_pkgbase_cusfileds_arr[$profpkg_custmf_key];
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
                                            
                                            $pkg_with_promote = get_post_meta($p_pkgid, 'jobsearch_field_candprof_promote_profile', true);
                                            
                                            $unlimited_numapps = get_post_meta($p_pkgid, 'jobsearch_field_unlim_candprofnumapps', true);
                                            $total_apps = get_post_meta($p_pkgid, 'jobsearch_field_candprof_num_apps', true);
                                            if ($unlimited_numapps == 'on') {
                                                $total_apps = esc_html__('Unlimited', 'wp-jobsearch');
                                            }
                                            //
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
                                                                <div class="detitem-label"><?php esc_html_e('Cover Letter:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val"><?php echo($ppkg_pbase_coverltr == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                                            </div>
                                                            <div class="profpkg-det-item">
                                                                <div class="detitem-label"><?php esc_html_e('Education:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val"><?php echo($ppkg_pbase_resmedu == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                                            </div>
                                                            <div class="profpkg-det-item">
                                                                <div class="detitem-label"><?php esc_html_e('Experience:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val"><?php echo($ppkg_pbase_resmexp == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                                            </div>
                                                            <div class="profpkg-det-item">
                                                                <div class="detitem-label"><?php esc_html_e('Portfolio:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val"><?php echo($ppkg_pbase_resmport == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                                            </div>
                                                            <div class="profpkg-det-item">
                                                                <div class="detitem-label"><?php esc_html_e('Expertise:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val"><?php echo($ppkg_pbase_resmskills == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                                            </div>
                                                            <div class="profpkg-det-item">
                                                                <div class="detitem-label"><?php esc_html_e('Honors & Awards:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val"><?php echo($ppkg_pbase_resmawards == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                                            </div>
                                                            <div class="profpkg-det-item">
                                                                <div class="detitem-label"><?php esc_html_e('Number of Applications:', 'wp-jobsearch') ?></div>
                                                                <div class="detitem-val">
                                                                    <?php
                                                                    if ($unlimited_numapps == 'yes') {
                                                                        esc_html_e('Unlimited', 'wp-jobsearch');
                                                                    } else {
                                                                        echo ($total_apps);
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                            <?php
                                                            if ($pkg_with_promote == 'on') {
                                                                $promote_expiry = get_post_meta($p_pkgid, 'jobsearch_field_candprof_promote_expiry_time', true);
                                                                $promote_expiry_unit = get_post_meta($p_pkgid, 'jobsearch_field_candprof_promote_expiry_time_unit', true);
                                                                $unlimited_promote_expiry = get_post_meta($p_pkgid, 'jobsearch_field_unlimited_candprof_promote_exp', true);
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
                                                                   class="jobsearch-subscand-profile-pkg"
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
                                                       class="jobsearch-subscand-profile-pkg buy-profpkgbtn"
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

                            $p_order_detail = get_post_meta($p_order_id, 'jobsearch_cand_ppkg_fields_list', true);

                            $cand_pkgbase_dashsecs_arr = apply_filters('jobsearch_cand_dash_menu_in_opts', array(
                                'my_profile' => __('My Profile', 'wp-jobsearch'),
                                'my_resume' => __('My Resume', 'wp-jobsearch'),
                                'applied_jobs' => __('Applied Jobs', 'wp-jobsearch'),
                                'cv_manager' => __('CV Manager', 'wp-jobsearch'),
                                'fav_jobs' => __('Favorite Jobs', 'wp-jobsearch'),
                                'packages' => __('Packages', 'wp-jobsearch'),
                                'transactions' => __('Transactions', 'wp-jobsearch'),
                                'following' => __('Following', 'wp-jobsearch'),
                                'change_password' => __('Change Password', 'wp-jobsearch'),
                            ));
                            $show_dash_tabs = array();
                            if (isset($p_order_detail['pbase_dashtabs']) && !empty($p_order_detail['pbase_dashtabs'])) {
                                foreach ($p_order_detail['pbase_dashtabs'] as $profpkg_dashtab_key) {
                                    if (isset($cand_pkgbase_dashsecs_arr[$profpkg_dashtab_key])) {
                                        $show_dash_tabs[] = $cand_pkgbase_dashsecs_arr[$profpkg_dashtab_key];
                                    }
                                }
                            }
                            if (!empty($show_dash_tabs)) {
                                $show_dash_tabs = implode(', ', $show_dash_tabs);
                            } else {
                                $show_dash_tabs = '-';
                            }
                            //
                            $cand_pkgbase_profilefields = array(
                                'cover_img' => esc_html__('Cover Image', 'wp-jobsearch'),
                                'profile_url' => esc_html__('Profile URL', 'wp-jobsearch'),
                                'public_view' => esc_html__('Profile for Public View', 'wp-jobsearch'),
                                'date_of_birth' => esc_html__('Date of Birth', 'wp-jobsearch'),
                                'phone' => esc_html__('Phone', 'wp-jobsearch'),
                                'sector' => esc_html__('Sector', 'wp-jobsearch'),
                                'job_title' => esc_html__('Job Title', 'wp-jobsearch'),
                                'salary' => esc_html__('Salary', 'wp-jobsearch'),
                                'about_desc' => esc_html__('Description', 'wp-jobsearch'),
                            );
                            $show_profile_fields = array();
                            if (isset($p_order_detail['pbase_profile']) && !empty($p_order_detail['pbase_profile'])) {
                                foreach ($p_order_detail['pbase_profile'] as $profpkg_pfield_key) {
                                    if (isset($cand_pkgbase_profilefields[$profpkg_pfield_key])) {
                                        $show_profile_fields[] = $cand_pkgbase_profilefields[$profpkg_pfield_key];
                                    }
                                }
                            }
                            if (!empty($show_profile_fields)) {
                                $show_profile_fields = implode(', ', $show_profile_fields);
                            } else {
                                $show_profile_fields = '-';
                            }
                            //
                            $candidate_social_mlinks = isset($jobsearch__options['candidate_social_mlinks']) ? $jobsearch__options['candidate_social_mlinks'] : '';
                            $cand_pkgbase_social_arr = array(
                                'facebook' => __('Facebook', 'wp-jobsearch'),
                                'twitter' => __('Twitter', 'wp-jobsearch'),
                                'google_plus' => __('Google Plus', 'wp-jobsearch'),
                                'linkedin' => __('Linkedin', 'wp-jobsearch'),
                                'dribbble' => __('Dribbble', 'wp-jobsearch'),
                            );
                            if (!empty($candidate_social_mlinks)) {
                                if (isset($candidate_social_mlinks['title']) && is_array($candidate_social_mlinks['title'])) {
                                    $field_counter = 0;
                                    foreach ($candidate_social_mlinks['title'] as $cand_social_mlink) {
                                        $cand_pkgbase_social_arr['dynm_social' . $field_counter] = $cand_social_mlink;
                                        $field_counter++;
                                    }
                                }
                            }
                            $show_social_fields = array();
                            if (isset($p_order_detail['pbase_social']) && !empty($p_order_detail['pbase_social'])) {
                                foreach ($p_order_detail['pbase_social'] as $profpkg_socialf_key) {
                                    if (isset($cand_pkgbase_social_arr[$profpkg_socialf_key])) {
                                        $show_social_fields[] = $cand_pkgbase_social_arr[$profpkg_socialf_key];
                                    }
                                }
                            }
                            if (!empty($show_social_fields)) {
                                $show_social_fields = implode(', ', $show_social_fields);
                            } else {
                                $show_social_fields = '-';
                            }
                            //
                            $cand_custom_fields_saved_data = get_option('jobsearch_custom_field_candidate');
                            if (is_array($cand_custom_fields_saved_data) && sizeof($cand_custom_fields_saved_data) > 0) {
                                $cand_pkgbase_cusfileds_arr = array();
                                foreach ($cand_custom_fields_saved_data as $cand_cus_field_key => $cand_cus_field_kdata) {
                                    $cusfield_label = isset($cand_cus_field_kdata['label']) ? $cand_cus_field_kdata['label'] : '';
                                    $cusfield_name = isset($cand_cus_field_kdata['name']) ? $cand_cus_field_kdata['name'] : '';
                                    if ($cusfield_label != '' && $cusfield_name != '') {
                                        $cand_pkgbase_cusfileds_arr[$cusfield_name] = $cusfield_label;
                                    }
                                }
                                $show_custf_fields = array();
                                if (isset($p_order_detail['pbase_cusfields']) && !empty($p_order_detail['pbase_cusfields'])) {
                                    foreach ($p_order_detail['pbase_cusfields'] as $profpkg_custmf_key) {
                                        if (isset($cand_pkgbase_cusfileds_arr[$profpkg_custmf_key])) {
                                            $show_custf_fields[] = $cand_pkgbase_cusfileds_arr[$profpkg_custmf_key];
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
                            
                            //
                            $total_apps = get_post_meta($p_order_id, 'candprof_num_apps', true);
                            $unlimited_numapps = get_post_meta($p_order_id, 'unlimited_numcapps', true);
                            if ($unlimited_numapps == 'yes') {
                                $total_apps = esc_html__('Unlimited', 'wp-jobsearch');
                            }
                            //
                            $used_apps = jobsearch_candprofpckg_order_used_apps($p_order_id);
                            $remaining_apps = jobsearch_candprofpckg_order_remaining_apps($p_order_id);
                            if ($unlimited_numapps == 'yes') {
                                $used_apps = '-';
                                $remaining_apps = '-';
                            }
                            
                            $pkg_with_promote = get_post_meta($p_order_id, 'candprof_promote_profile', true);
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
                                                <div class="detitem-label"><?php esc_html_e('Cover Letter:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val"><?php echo(isset($p_order_detail['pbase_coverltr']) && $p_order_detail['pbase_coverltr'] == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                            </div>
                                            <div class="profpkg-det-item">
                                                <div class="detitem-label"><?php esc_html_e('Education:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val"><?php echo(isset($p_order_detail['pbase_resmedu']) && $p_order_detail['pbase_resmedu'] == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                            </div>
                                            <div class="profpkg-det-item">
                                                <div class="detitem-label"><?php esc_html_e('Experience:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val"><?php echo(isset($p_order_detail['pbase_resmexp']) && $p_order_detail['pbase_resmexp'] == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                            </div>
                                            <div class="profpkg-det-item">
                                                <div class="detitem-label"><?php esc_html_e('Portfolio:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val"><?php echo(isset($p_order_detail['pbase_resmport']) && $p_order_detail['pbase_resmport'] == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                            </div>
                                            <div class="profpkg-det-item">
                                                <div class="detitem-label"><?php esc_html_e('Expertise:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val"><?php echo(isset($p_order_detail['pbase_resmskills']) && $p_order_detail['pbase_resmskills'] == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                            </div>
                                            <div class="profpkg-det-item">
                                                <div class="detitem-label"><?php esc_html_e('Honors & Awards:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val"><?php echo(isset($p_order_detail['pbase_resmawards']) && $p_order_detail['pbase_resmawards'] == 'on' ? esc_html__('On', 'wp-jobsearch') : esc_html__('Off', 'wp-jobsearch')) ?></div>
                                            </div>
                                            <div class="profpkg-det-item">
                                                <div class="detitem-label"><?php esc_html_e('Number of Applications:', 'wp-jobsearch') ?></div>
                                                <div class="detitem-val">
                                                    <?php
                                                    if ($unlimited_numapps == 'yes') {
                                                        esc_html_e('Unlimited', 'wp-jobsearch');
                                                    } else {
                                                        ?>
                                                        <?php printf(__('Total: %s', 'wp-jobsearch'), $total_apps) ?>, 
                                                        <?php printf(__('Used: %s', 'wp-jobsearch'), $used_apps) ?>, 
                                                        <?php printf(__('Remaininig: %s', 'wp-jobsearch'), $remaining_apps) ?>
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
                                                $promote_expiry_timestamp = get_post_meta($p_order_id, 'candprof_promote_expiry_timestamp', true);
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
                                                                                          class="jobsearch-subscand-profile-pkg"
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
                            <script type="text/javascript">
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
                <div class="jobsearch-profile-title">
                    <?php
                    ob_start();
                    ?>
                    <h2><?php esc_html_e('Applications Statistics', 'wp-jobsearch') ?></h2>
                    <?php
                    $tapp_html = ob_get_clean();
                    echo apply_filters('jobsearch_cand_dash_stats_appjobs_mtitle', $tapp_html);
                    ?>
                </div>
                <?php
                if ($user_pkg_limits::cand_field_is_locked('stats_defields')) {
                    echo($user_pkg_limits::cand_gen_locked_html());
                } else {
                    ?>
                    <div class="jobsearch-stats-list">
                        <?php
                        ob_start();
                        ?>
                        <ul>
                            <li>
                                <div class="jobsearch-stats-list-wrap dark-blue">
                                    <h6><?php esc_html_e('Applied jobs', 'wp-jobsearch') ?></h6>
                                    <span><?php echo absint($user_applied_jobs_count) ?></span>
                                    <small><?php esc_html_e('to find a career', 'wp-jobsearch') ?></small>
                                </div>
                            </li>
                            <li>
                                <div class="jobsearch-stats-list-wrap light-blue">
                                    <h6><?php esc_html_e('Favorite Jobs', 'wp-jobsearch') ?></h6>
                                    <span><?php echo absint($fav_jobs_list_count) ?></span>
                                    <small><?php esc_html_e('against opportunities', 'wp-jobsearch') ?></small>
                                </div>
                            </li>
                            <li>
                                <div class="jobsearch-stats-list-wrap green">
                                    <h6><?php esc_html_e('Job Alerts', 'wp-jobsearch') ?></h6>
                                    <span><?php echo absint($total_alerts) ?></span>
                                    <small><?php esc_html_e('to get the latest updates', 'wp-jobsearch') ?></small>
                                </div>
                            </li>
                            <li>
                                <div class="jobsearch-stats-list-wrap">
                                    <h6><?php esc_html_e('Packages', 'wp-jobsearch') ?></h6>
                                    <span><?php echo absint($total_pkgs) ?></span>
                                    <small><?php esc_html_e('to apply jobs', 'wp-jobsearch') ?></small>
                                </div>
                            </li>
                        </ul>
                        <?php
                        $tapp_html = ob_get_clean();
                        echo apply_filters('jobsearch_cand_dash_stats_numboxes_html', $tapp_html, $user_applied_jobs_count, $fav_jobs_list_count, $total_alerts, $total_pkgs);
                        ?>
                    </div>
                    <?php
                    wp_enqueue_script('morris');
                    wp_enqueue_script('raphael');

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
                                            y: '<?php printf(esc_html__('Applied Jobs: %s', 'wp-jobsearch'), $user_applied_jobs_count) ?>, <?php printf(esc_html__('Favorite Jobs: %s', 'wp-jobsearch'), $fav_jobs_list_count) ?>, <?php printf(esc_html__('Job Alerts: %s', 'wp-jobsearch'), $total_alerts) ?>',
                                            item_1: <?php echo($user_applied_jobs_count) ?>,
                                            item_2: <?php echo($fav_jobs_list_count) ?>,
                                            item_3: <?php echo($total_alerts) ?>,
                                        },
                                    ],
                                    barColors: [
                                        "#008dc9", "#a869d6", "#84c15a"],
                                    xkey: 'y',
                                    ykeys: ["item_1", "item_2", "item_3",],
                                    labels: [
                                        "<?php esc_html_e('Applied Jobs', 'wp-jobsearch') ?>",
                                        "<?php esc_html_e('Favorite Jobs', 'wp-jobsearch') ?>",
                                        "<?php esc_html_e('Job Alerts', 'wp-jobsearch') ?>"
                                    ]
                                });
                            });
                        </script>
                    </div>
                    <div class="jobsearch-applicants-stats">
                        <div class="jobsearch-applicants-stats-wrap">
                            <i class="fa fa-users"></i>
                            <span><?php echo absint($user_applied_jobs_count) ?></span>
                            <small><?php esc_html_e('Total Applied Jobs', 'wp-jobsearch') ?></small>
                        </div>
                        <ul>
                            <li>
                                <i class="fa fa-circle dark-blue"></i> <?php esc_html_e('Applied Jobs', 'wp-jobsearch') ?>
                            </li>
                            <li>
                                <i class="fa fa-circle light-blue"></i> <?php esc_html_e('Favorite Jobs', 'wp-jobsearch') ?>
                            </li>
                            <li><i class="fa fa-circle"></i> <?php esc_html_e('Job Alerts', 'wp-jobsearch') ?></li>
                        </ul>
                    </div>
                    <?php
                    $tapp_html = ob_get_clean();
                    echo apply_filters('jobsearch_cand_dash_stats_numstats_html', $tapp_html, $rand_id);
                }
                ?>
            </div>
            <?php
        }
        echo apply_filters('jobsearch_cand_dash_stats_before_recent_applies', '', $candidate_id);
        ?>
        <div class="jobsearch-employer-box-section">

            <div class="jobsearch-profile-title">
                <h2><?php esc_html_e('Jobs Applied Recently', 'wp-jobsearch') ?></h2>
            </div>
            <?php
            $all_apps = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts AS posts"
                . " LEFT JOIN $wpdb->postmeta AS postmeta ON(posts.ID = postmeta.post_id) "
                . " WHERE post_type=%s AND (postmeta.meta_key = 'jobsearch_app_user_email' AND postmeta.meta_value = '{$user_email_adr}')", 'email_apps'));

            if (!empty($all_apps)) {
                foreach ($all_apps as $app_id) {
                    $_job_id = get_post_meta($app_id, 'jobsearch_app_job_id', true);
                    
                    $job_applicants_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts AS posts"
                    . " LEFT JOIN $wpdb->postmeta AS postmeta ON(posts.ID = postmeta.post_id) "
                    . " WHERE post_type=%s AND (postmeta.meta_key = 'jobsearch_app_job_id' AND postmeta.meta_value={$_job_id})", 'email_apps'));

                    $job_short_int_list = get_post_meta($_job_id, '_job_short_interview_list', true);
                    $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : '';
                    if (empty($job_short_int_list)) {
                        $job_short_int_list = array();
                    }

                    $job_views_count = get_post_meta($_job_id, 'jobsearch_job_views_count', true);
                    $job_expiry_date = get_post_meta($_job_id, 'jobsearch_field_job_expiry_date', true);
                    $job_expiry_date = $job_expiry_date == '' ? strtotime(current_time('Y-m-d H:i:s')) : $job_expiry_date;
                    $job_salary = jobsearch_job_offered_salary($_job_id);

                    ob_start();
                    ?>
                    <div class="jobsearch-recent-applicants-nav">
                        <ul>
                            <?php ob_start(); ?>
                            <li>
                                <span><?php echo absint($job_applicants_count) ?></span>
                                <small><?php esc_html_e('Total applicants', 'wp-jobsearch') ?></small>
                            </li>
                            <?php
                            $tapp_html = ob_get_clean();
                            echo apply_filters('jobsearch_cand_dash_stats_appjobs_tapps', $tapp_html, $app_id);
                            if ($job_salary != '') {
                                ?>
                                <li>
                                    <small><?php echo($job_salary) ?><?php esc_html_e('Job Salary', 'wp-jobsearch') ?></small>
                                </li>
                                <?php
                            }
                            ?>
                            <li><span><?php echo absint($job_views_count) ?></span>
                                <small><?php esc_html_e('Total visits', 'wp-jobsearch') ?></small>
                            </li>
                            <?php
                            ob_start();
                            ?>
                            <li>
                                <small><?php printf(esc_html__('Expiry Date: %s', 'wp-jobsearch'), date_i18n(get_option('date_format'), $job_expiry_date)) ?></small>
                            </li>
                            <?php
                            $tapp_html = ob_get_clean();
                            echo apply_filters('jobsearch_cand_dash_stats_appjobs_expdate', $tapp_html, $app_id, $job_expiry_date);
                            ?>
                        </ul>
                    </div>
                    <div class="jobsearch-candidate jobsearch-candidate-default  jobsearch-applicns-candidate">
                        <ul class="jobsearch-row">
                            <?php
                            $job_post_date = get_post_meta($_job_id, 'jobsearch_field_job_publish_date', true);
                            $job_location = get_post_meta($_job_id, 'jobsearch_field_location_address', true);
                            $job_post_employer = get_post_meta($_job_id, 'jobsearch_field_job_posted_by', true);

                            $job_post_user = jobsearch_get_employer_user_id($job_post_employer);
                            $user_def_avatar_url = get_avatar_url($job_post_user, array('size' => 69));
                            $user_avatar_id = get_post_thumbnail_id($job_post_employer);
                            if ($user_avatar_id > 0) {
                                $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
                                $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
                            }
                            $user_def_avatar_url = $user_def_avatar_url == '' ? jobsearch_no_image_placeholder() : $user_def_avatar_url;

                            $job_city_title = '';
                            $get_job_city = get_post_meta($_job_id, 'jobsearch_field_location_location3', true);
                            if ($get_job_city == '') {
                                $get_job_city = get_post_meta($_job_id, 'jobsearch_field_location_location2', true);
                            }
                            if ($get_job_city != '') {
                                $get_job_country = get_post_meta($_job_id, 'jobsearch_field_location_location1', true);
                            }

                            $job_city_tax = $get_job_city != '' ? get_term_by('slug', $get_job_city, 'job-location') : '';
                            if (is_object($job_city_tax)) {
                                $job_city_title = isset($job_city_tax->name) ? $job_city_tax->name : '';

                                $job_country_tax = $get_job_country != '' ? get_term_by('slug', $get_job_country, 'job-location') : '';
                                if (is_object($job_country_tax)) {
                                    $job_city_title .= isset($job_country_tax->name) ? ', ' . $job_country_tax->name : '';
                                }
                            }

                            $sectors = wp_get_post_terms($_job_id, 'sector');
                            $job_sector = isset($sectors[0]->name) ? $sectors[0]->name : '';
                            ?>
                            <li class="jobsearch-column-12">
                                <div class="jobsearch-candidate-default-wrap">
                                    <?php if ($user_def_avatar_url != '') { ?>
                                        <figure>
                                            <a href="<?php the_permalink($_job_id); ?>">
                                                <img src="<?php echo esc_url($user_def_avatar_url) ?>" alt="">
                                            </a>
                                        </figure>
                                    <?php } ?>
                                    <div class="jobsearch-candidate-default-text">
                                        <div class="jobsearch-candidate-default-left">
                                            <h2 class="jobsearch-pst-title">
                                                <a href="<?php echo esc_url(get_permalink($_job_id)); ?>">
                                                    <?php echo esc_html(wp_trim_words(get_the_title($_job_id), 5)); ?>
                                                </a>
                                            </h2>
                                            <ul>
                                                <?php if ($job_post_date != '') { ?>
                                                    <li>
                                                        <i class="jobsearch-icon jobsearch-calendar"></i> <?php echo date_i18n(get_option('date_format'), $job_post_date); ?>
                                                    </li>
                                                    <?php
                                                }
                                                if ($job_sector != '') {
                                                    ?>
                                                    <li>
                                                        <i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i>
                                                        <a><?php echo($job_sector) ?></a></li>
                                                    <?php
                                                }
                                                if (!empty($job_city_title) && $all_location_allow == 'on') {
                                                    ?>
                                                    <li>
                                                        <i class="fa fa-map-marker"></i> <?php echo esc_html($job_city_title); ?>
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>

                        </ul>
                    </div>
                    <?php
                    $itme_html = ob_get_clean();
                    echo apply_filters('jobsearch_cand_dash_stats_appjobitm_html', $itme_html, $app_id);
                }
            }
            if (!empty($user_applied_jobs_list)) {
                $total_records = apply_filters('jobsearch_cand_dash_appjobs_listar_count', count($user_applied_jobs_list));
                krsort($user_applied_jobs_list);
                $start = ($page_num - 1) * ($reults_per_page);
                $offset = $reults_per_page;
                $user_applied_jobs_list = array_slice($user_applied_jobs_list, $start, $offset);
                $user_applied_jobs_list = apply_filters('jobsearch_cand_dash_appjobs_listar_forech', $user_applied_jobs_list);
                foreach ($user_applied_jobs_list as $_job_apply) {

                    $_job_id = isset($_job_apply['post_id']) ? $_job_apply['post_id'] : 0;
                    $job_applicants_list = get_post_meta($_job_id, 'jobsearch_job_applicants_list', true);
                    $job_applicants_list = $job_applicants_list != '' ? explode(',', $job_applicants_list) : array();
                    $job_applicants_count = count($job_applicants_list);

                    $job_short_int_list = get_post_meta($_job_id, '_job_short_interview_list', true);
                    $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : '';
                    if (empty($job_short_int_list)) {
                        $job_short_int_list = array();
                    }

                    if (!empty($job_applicants_list)) {
                        arsort($job_applicants_list);
                        $job_views_count = get_post_meta($_job_id, 'jobsearch_job_views_count', true);
                        $job_expiry_date = get_post_meta($_job_id, 'jobsearch_field_job_expiry_date', true);
                        $job_expiry_date = $job_expiry_date == '' ? strtotime(current_time('Y-m-d '.get_option('time_format'))) : $job_expiry_date;
                        $job_salary = jobsearch_job_offered_salary($_job_id);

                        ob_start();
                        ?>
                        <div class="jobsearch-recent-applicants-nav">
                            <ul>
                                <?php ob_start(); ?>
                                <li><span><?php echo absint($job_applicants_count) ?></span>
                                    <small><?php esc_html_e('Total applicants', 'wp-jobsearch') ?></small>
                                </li>
                                <?php
                                $tapp_html = ob_get_clean();
                                echo apply_filters('jobsearch_cand_dash_stats_appjobs_tapps', $tapp_html, $_job_apply);
                                if ($job_salary != '') {
                                    ?>
                                    <li>
                                        <small><?php echo($job_salary) ?><?php esc_html_e('Job Salary', 'wp-jobsearch') ?></small>
                                    </li>
                                    <?php
                                }
                                ?>
                                <li><span><?php echo absint($job_views_count) ?></span>
                                    <small><?php esc_html_e('Total visits', 'wp-jobsearch') ?></small>
                                </li>
                                <?php
                                ob_start();
                                ?>
                                <li>
                                    <small><?php printf(esc_html__('Expiry Date: %s', 'wp-jobsearch'), date_i18n(get_option('date_format'), $job_expiry_date)) ?></small>
                                </li>
                                <?php
                                $tapp_html = ob_get_clean();
                                echo apply_filters('jobsearch_cand_dash_stats_appjobs_expdate', $tapp_html, $_job_apply, $job_expiry_date);
                                ?>
                            </ul>
                        </div>
                        <div class="jobsearch-candidate jobsearch-candidate-default  jobsearch-applicns-candidate">
                            <ul class="jobsearch-row">
                                <?php
                                $job_post_date = get_post_meta($_job_id, 'jobsearch_field_job_publish_date', true);
                                $job_location = get_post_meta($_job_id, 'jobsearch_field_location_address', true);
                                $job_post_employer = get_post_meta($_job_id, 'jobsearch_field_job_posted_by', true);

                                $job_post_user = jobsearch_get_employer_user_id($job_post_employer);
                                $user_def_avatar_url = get_avatar_url($job_post_user, array('size' => 69));
                                $user_avatar_id = get_post_thumbnail_id($job_post_employer);
                                if ($user_avatar_id > 0) {
                                    $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
                                    $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
                                }
                                $user_def_avatar_url = $user_def_avatar_url == '' ? jobsearch_no_image_placeholder() : $user_def_avatar_url;

                                $job_city_title = '';
                                $get_job_city = get_post_meta($_job_id, 'jobsearch_field_location_location3', true);
                                if ($get_job_city == '') {
                                    $get_job_city = get_post_meta($_job_id, 'jobsearch_field_location_location2', true);
                                }
                                if ($get_job_city != '') {
                                    $get_job_country = get_post_meta($_job_id, 'jobsearch_field_location_location1', true);
                                }

                                $job_city_tax = $get_job_city != '' ? get_term_by('slug', $get_job_city, 'job-location') : '';
                                if (is_object($job_city_tax)) {
                                    $job_city_title = isset($job_city_tax->name) ? $job_city_tax->name : '';

                                    $job_country_tax = $get_job_country != '' ? get_term_by('slug', $get_job_country, 'job-location') : '';
                                    if (is_object($job_country_tax)) {
                                        $job_city_title .= isset($job_country_tax->name) ? ', ' . $job_country_tax->name : '';
                                    }
                                }

                                $sectors = wp_get_post_terms($_job_id, 'sector');
                                $job_sector = isset($sectors[0]->name) ? $sectors[0]->name : '';
                                ?>
                                <li class="jobsearch-column-12">
                                    <div class="jobsearch-candidate-default-wrap">
                                        <?php if ($user_def_avatar_url != '') { ?>
                                            <figure>
                                                <a href="<?php the_permalink($_job_id); ?>">
                                                    <img src="<?php echo esc_url($user_def_avatar_url) ?>" alt="">
                                                </a>
                                            </figure>
                                        <?php } ?>
                                        <div class="jobsearch-candidate-default-text">
                                            <div class="jobsearch-candidate-default-left">
                                                <h2 class="jobsearch-pst-title">
                                                    <a href="<?php echo esc_url(get_permalink($_job_id)); ?>">
                                                        <?php echo esc_html(wp_trim_words(get_the_title($_job_id), 5)); ?>
                                                    </a>
                                                </h2>
                                                <ul>
                                                    <?php if ($job_post_date != '') { ?>
                                                        <li>
                                                            <i class="jobsearch-icon jobsearch-calendar"></i> <?php echo date_i18n(get_option('date_format'), $job_post_date); ?>
                                                        </li>
                                                        <?php
                                                    }
                                                    if ($job_sector != '') {
                                                        ?>
                                                        <li>
                                                            <i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i>
                                                            <a><?php echo($job_sector) ?></a></li>
                                                        <?php
                                                    }
                                                    if (!empty($job_city_title) && $all_location_allow == 'on') {
                                                        ?>
                                                        <li>
                                                            <i class="fa fa-map-marker"></i> <?php echo esc_html($job_city_title); ?>
                                                        </li>
                                                        <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                            </ul>
                        </div>
                        <?php
                        $itme_html = ob_get_clean();
                        echo apply_filters('jobsearch_cand_dash_stats_appjobitm_html', $itme_html, $_job_apply);
                    }
                }
                $total_pages = 1;
                if ($total_records > 0 && $reults_per_page > 0 && $total_records > $reults_per_page) {
                    $total_pages = ceil($total_records / $reults_per_page);
                    ?>
                    <div class="jobsearch-pagination-blog">
                        <?php $Jobsearch_User_Dashboard_Settings->pagination($total_pages, $page_num, $page_url) ?>
                    </div>
                    <?php
                }
            } else {
                ?>
                <p><?php esc_html_e('No Applications found.', 'wp-jobsearch') ?></p>
                <?php
            }
            ?>
        </div>
        <?php
        echo apply_filters('jobsearch_cand_dash_appstats_after_recjobs', '');
        ?>
    </div>
    <?php
}    