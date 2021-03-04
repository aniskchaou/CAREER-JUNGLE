<?php
// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// functions class
class JobSearch_EmpDash_ManageJob_Applicants
{

    public function __construct()
    {

        add_filter('jobsearch_empdash_managejob_applicants', array($this, 'managejob_applicants_html'));
    }

    public function managejob_applicants_html($html)
    {
        global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings, $wpdb;

        $_job_id = isset($_GET['job_id']) ? $_GET['job_id'] : '';

        $user_id = get_current_user_id();
        $user_id = apply_filters('jobsearch_in_fromdash_mangejobaplics_user_id', $user_id, $_job_id);
        $user_obj = get_user_by('ID', $user_id);
        $page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
        $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);

        $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';

        $is_user_member = false;
        if (jobsearch_user_isemp_member($user_id)) {
            $is_user_member = true;
            $employer_id = jobsearch_user_isemp_member($user_id);
            $user_id = jobsearch_get_employer_user_id($employer_id);
        } else {
            $employer_id = jobsearch_get_user_employer_id($user_id);
        }

        $reults_per_page = isset($jobsearch_plugin_options['user-dashboard-per-page']) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;
        $page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;

        if ($employer_id > 0) {

            ob_start();
            $job_applicants_list = get_post_meta($_job_id, 'jobsearch_job_applicants_list', true);
            $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
            if (empty($job_applicants_list)) {
                $job_applicants_list = array();
            }

            $job_applicants_list = apply_filters('jobsearch_mangejob_applics_list_arr', $job_applicants_list);

            $job_applicants_count = !empty($job_applicants_list) ? count($job_applicants_list) : 0;

            $viewed_candidates = get_post_meta($_job_id, 'jobsearch_viewed_candidates', true);
            if (empty($viewed_candidates)) {
                $viewed_candidates = array();
            }
            $viewed_candidates = jobsearch_is_post_ids_array($viewed_candidates, 'candidate');

            $job_short_int_list = get_post_meta($_job_id, '_job_short_interview_list', true);
            $job_short_int_list = $job_short_int_list != '' ? explode(',', $job_short_int_list) : '';
            if (empty($job_short_int_list)) {
                $job_short_int_list = array();
            }
            $job_short_int_list = jobsearch_is_post_ids_array($job_short_int_list, 'candidate');
            $job_short_int_list_c = !empty($job_short_int_list) ? count($job_short_int_list) : 0;

            $job_reject_int_list = get_post_meta($_job_id, '_job_reject_interview_list', true);
            $job_reject_int_list = $job_reject_int_list != '' ? explode(',', $job_reject_int_list) : '';
            if (empty($job_reject_int_list)) {
                $job_reject_int_list = array();
            }
            $job_reject_int_list = jobsearch_is_post_ids_array($job_reject_int_list, 'candidate');
            $job_reject_int_list_c = !empty($job_reject_int_list) ? count($job_reject_int_list) : 0;

            //
            $job_posttin_instamatch_cand = isset($jobsearch_plugin_options['job_posttin_instamatch_cand']) ? $jobsearch_plugin_options['job_posttin_instamatch_cand'] : '';
            $job_instamatch_list = get_post_meta($_job_id, 'jobsearch_instamatch_cands', true);
            $job_instamatch_list = jobsearch_is_post_ids_array($job_instamatch_list, 'candidate');
            $job_insta_match_list_c = !empty($job_instamatch_list) ? count($job_instamatch_list) : 0;
            //var_dump($job_instamatch_list);
            //

            $applicants_mange_view = get_post_meta($employer_id, 'applicants_mange_view', true);

            $_selected_view = isset($_GET['ap_view']) && $_GET['ap_view'] != '' ? $_GET['ap_view'] : $applicants_mange_view;
            if ($applicants_mange_view != $_selected_view) {
                update_post_meta($employer_id, 'applicants_mange_view', $_selected_view);
                $_selected_view = get_post_meta($employer_id, 'applicants_mange_view', true);
            }

            $_mod_tab = isset($_GET['mod']) && $_GET['mod'] != '' ? $_GET['mod'] : 'applicants';
            $_sort_selected = isset($_GET['sort_by']) && $_GET['sort_by'] != '' ? $_GET['sort_by'] : '';

            ob_start();
            ?>
            <div class="jobsearch-profile-title">
                <h2><?php printf(esc_html__('"%s" Applicants', 'wp-jobsearch'), get_the_title($_job_id)) ?></h2>
            </div>
            <?php
            $apps_title_html = ob_get_clean();
            echo apply_filters('jobseacrh_dash_manag_apps_maintitle_html', $apps_title_html, $_job_id);
            ?>
            <div class="jobsearch-applicants-tabs">
                <script>
                    jQuery(document).on('click', '.jobsearch-modelemail-btn-<?php echo($_job_id) ?>', function () {
                        jobsearch_modal_popup_open('JobSearchModalSendEmail<?php echo($_job_id) ?>');
                    });
                </script>
                <?php
                $tabs_count_number = 3;
                if ($job_posttin_instamatch_cand == 'on') {
                    $tabs_count_number++;
                }
                $tabs_count_class = 'app_tabs_count_' . apply_filters('jobsearch_empdash_mangejob_appstbs_counter', $tabs_count_number, $_job_id);
                ?>
                <ul class="tabs-list <?php echo($tabs_count_class) ?>">
                    <li <?php echo($_mod_tab == '' || $_mod_tab == 'applicants' ? 'class="active"' : '') ?>><a
                                href="<?php echo add_query_arg(array('tab' => 'manage-jobs', 'view' => 'applicants', 'job_id' => $_job_id), $page_url) ?>"><?php printf(esc_html__('Applicants (%s)', 'wp-jobsearch'), $job_applicants_count) ?></a>
                    </li>
                    <li <?php echo($_mod_tab == 'shortlisted' ? 'class="active"' : '') ?>><a
                                href="<?php echo add_query_arg(array('tab' => 'manage-jobs', 'view' => 'applicants', 'job_id' => $_job_id, 'mod' => 'shortlisted'), $page_url) ?>"><?php printf(esc_html__('Shortlisted for Interview (%s)', 'wp-jobsearch'), $job_short_int_list_c) ?></a>
                    </li>
                    <li <?php echo($_mod_tab == 'rejected' ? 'class="active"' : '') ?>><a
                                href="<?php echo add_query_arg(array('tab' => 'manage-jobs', 'view' => 'applicants', 'job_id' => $_job_id, 'mod' => 'rejected'), $page_url) ?>"><?php printf(esc_html__('Rejected (%s)', 'wp-jobsearch'), $job_reject_int_list_c) ?></a>
                    </li>
                    <?php
                    if ($job_posttin_instamatch_cand == 'on') {
                        ?>
                        <li <?php echo($_mod_tab == 'insta_match' ? 'class="active"' : '') ?>><a
                                    href="<?php echo add_query_arg(array('tab' => 'manage-jobs', 'view' => 'applicants', 'job_id' => $_job_id, 'mod' => 'insta_match'), $page_url) ?>"><?php printf(esc_html__('Insta Match (%s)', 'wp-jobsearch'), $job_insta_match_list_c) ?></a>
                        </li>
                        <?php
                    }
                    echo apply_filters('jobseacrh_empdash_manag_apps_tabs_after', '', $_job_id, $page_url);
                    ?>
                </ul>
                <div class="applied-jobs-sort">
                    <div class="sort-select-all">
                        <input type="checkbox" id="select-all-job-app">
                        <label for="select-all-job-app"></label>
                    </div>
                    <small><?php esc_html_e('Select all', 'wp-jobsearch') ?></small>
                    <?php
                    ob_start();
                    ?>
                    <div class="sort-by-option">
                        <form id="jobsearch-applicants-form" method="get">
                            <input type="hidden" name="tab" value="manage-jobs">
                            <input type="hidden" name="view" value="applicants">
                            <input type="hidden" name="job_id" value="<?php echo absint($_job_id) ?>">
                            <input type="hidden" name="mod" value="<?php echo($_mod_tab) ?>">
                            <input type="hidden" name="ap_view" value="<?php echo($_selected_view) ?>">
                            <?php
                            if (isset($_GET['page_num']) && $_GET['page_num'] != '') {
                                ?>
                                <input type="hidden" name="page_num" value="<?php echo($_GET['page_num']) ?>">
                                <?php
                            }
                            ?>
                            <select id="jobsearch-applicants-sort" class="selectize-select"
                                    placeholder="<?php esc_html_e('Sort by', 'wp-jobsearch') ?>" name="sort_by">
                                <option value=""><?php esc_html_e('Sort by', 'wp-jobsearch') ?></option>
                                <option value="recent"<?php echo($_sort_selected == 'recent' ? ' selected="selected"' : '') ?>><?php esc_html_e('Recent', 'wp-jobsearch') ?></option>
                                <option value="alphabetic"<?php echo($_sort_selected == 'alphabetic' ? ' selected="selected"' : '') ?>><?php esc_html_e('Alphabet Order', 'wp-jobsearch') ?></option>
                                <option value="salary"<?php echo($_sort_selected == 'salary' ? ' selected="selected"' : '') ?>><?php esc_html_e('Expected Salary', 'wp-jobsearch') ?></option>
                                <option value="viewed"<?php echo($_sort_selected == 'viewed' ? ' selected="selected"' : '') ?>><?php esc_html_e('Viewed', 'wp-jobsearch') ?></option>
                                <option value="unviewed"<?php echo($_sort_selected == 'unviewed' ? ' selected="selected"' : '') ?>><?php esc_html_e('Unviewed', 'wp-jobsearch') ?></option>
                            </select>
                        </form>
                    </div>

                    <?php
                    $sort_by_dropdown = ob_get_clean();
                    $sort_by_args = array(
                        'job_id' => $_job_id,
                        'sort_selected' => $_sort_selected,
                        'mob_tab' => $_mod_tab,
                        'selected_view' => $_selected_view,
                    );
                    echo apply_filters('jobsearch_applicants_sortby_dropdown', $sort_by_dropdown, $sort_by_args);
                    ?>
                    <div id="sort-more-field-sec"
                         class="sort-more-fields<?php echo($_mod_tab == 'insta_match' && $job_posttin_instamatch_cand == 'on' ? ' instacands-btns-con' : '') ?>"
                         style="display: none;">
                        <div class="more-fields-act-btn">
                            <?php
                            if ($_mod_tab == 'insta_match' && $job_posttin_instamatch_cand == 'on') {
                                ?>
                                <a href="javascript:void(0);"
                                   class="mail-instacands-btn jobsearch-modelemail-btn-<?php echo($_job_id) ?>"><i
                                            class="jobsearch-icon jobsearch-envelope"></i><?php esc_html_e('Send Mail', 'wp-jobsearch') ?>
                                </a>
                                <?php
                                $popup_args = array('p_job_id' => $_job_id, 'p_emp_id' => $employer_id);
                                add_action('wp_footer', function () use ($popup_args) {

                                    extract(shortcode_atts(array(
                                        'p_job_id' => '',
                                        'p_emp_id' => '',
                                    ), $popup_args));
                                    ?>
                                    <div class="jobsearch-modal fade"
                                         id="JobSearchModalSendEmail<?php echo($p_job_id) ?>">
                                        <div class="modal-inner-area">&nbsp;</div>
                                        <div class="modal-content-area">
                                            <div class="modal-box-area">
                                                <span class="modal-close"><i class="fa fa-times"></i></span>
                                                <div class="jobsearch-send-message-form">
                                                    <form method="post"
                                                          id="jobsearch_send_email_form<?php echo esc_html($p_job_id); ?>">
                                                        <div class="jobsearch-user-form">
                                                            <ul class="email-fields-list">
                                                                <li>
                                                                    <label>
                                                                        <?php echo esc_html__('Subject', 'wp-jobsearch'); ?>
                                                                        :
                                                                    </label>
                                                                    <div class="input-field">
                                                                        <input type="text" name="send_message_subject"
                                                                               value="Pre-selection Notice: {job_title}"/>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <label>
                                                                        <?php echo esc_html__('Message', 'wp-jobsearch'); ?>
                                                                        :
                                                                    </label>
                                                                    <div class="input-field">
                                                                        <textarea name="send_message_content">Congratulations! After reviewing your profile we have pre-selected you for the position of Android Mobile Developer on {job_url}.
Please click here to proceed: <?php echo home_url('/') ?></textarea>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="input-field-submit">
                                                                        <input type="submit"
                                                                               class="multi-instamatchcands-email-submit"
                                                                               data-jid="<?php echo absint($p_job_id); ?>"
                                                                               data-eid="<?php echo absint($p_emp_id); ?>"
                                                                               name="send_message_content"
                                                                               value="Send"/>
                                                                        <span class="loader-box loader-box-<?php echo esc_html($p_job_id); ?>"></span>
                                                                    </div>
                                                                    <?php jobsearch_terms_and_con_link_txt(); ?>
                                                                </li>
                                                            </ul>
                                                            <div class="message-box message-box-<?php echo esc_html($p_job_id); ?>"
                                                                 style="display:none;"></div>
                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }, 11, 1);
                                ?>
                                <a href="javascript:void(0);" class="move-instacands-to-applics ajax-enable"
                                   data-jid="<?php echo absint($_job_id); ?>"><i
                                            class="fa fa-user-plus"></i> <?php esc_html_e('Move to Applicants', 'wp-jobsearch') ?>
                                    <span class="app-loader"></span></a>
                                <?php
                            } else {
                            ?>
                            <a href="javascript:void(0);"
                               class="more-actions"><?php esc_html_e('More', 'wp-jobsearch') ?> <span><i
                                            class="careerfy-icon careerfy-down-arrow"></i></span></a>
                            <ul style="display: none;">
                                <li>
                                    <a href="javascript:void(0);"
                                       class="jobsearch-modelemail-btn-<?php echo($_job_id) ?>"><?php esc_html_e('Email to Candidates', 'wp-jobsearch') ?></a>
                                    <?php
                                    $popup_args = array('p_job_id' => $_job_id, 'p_emp_id' => $employer_id);
                                    add_action('wp_footer', function () use ($popup_args) {

                                        extract(shortcode_atts(array(
                                            'p_job_id' => '',
                                            'p_emp_id' => '',
                                        ), $popup_args));
                                        ?>
                                        <div class="jobsearch-modal fade"
                                             id="JobSearchModalSendEmail<?php echo($p_job_id) ?>">
                                            <div class="modal-inner-area">&nbsp;</div>
                                            <div class="modal-content-area">
                                                <div class="modal-box-area">
                                                    <span class="modal-close"><i class="fa fa-times"></i></span>
                                                    <div class="jobsearch-send-message-form">
                                                        <form method="post"
                                                              id="jobsearch_send_email_form<?php echo esc_html($p_job_id); ?>">
                                                            <div class="jobsearch-user-form">
                                                                <ul class="email-fields-list">
                                                                    <li>
                                                                        <label>
                                                                            <?php echo esc_html__('Subject', 'wp-jobsearch'); ?>
                                                                            :
                                                                        </label>
                                                                        <div class="input-field">
                                                                            <input type="text"
                                                                                   name="send_message_subject"
                                                                                   value=""/>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <label>
                                                                            <?php echo esc_html__('Message', 'wp-jobsearch'); ?>
                                                                            :
                                                                        </label>
                                                                        <div class="input-field">
                                                                            <textarea
                                                                                    name="send_message_content"></textarea>
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <div class="input-field-submit">
                                                                            <input type="submit"
                                                                                   class="multi-applicantsto-email-submit"
                                                                                   data-jid="<?php echo absint($p_job_id); ?>"
                                                                                   data-eid="<?php echo absint($p_emp_id); ?>"
                                                                                   name="send_message_content"
                                                                                   value="Send"/>
                                                                            <span class="loader-box loader-box-<?php echo esc_html($p_job_id); ?>"></span>
                                                                        </div>
                                                                        <?php jobsearch_terms_and_con_link_txt(); ?>
                                                                    </li>
                                                                </ul>
                                                                <div class="message-box message-box-<?php echo esc_html($p_job_id); ?>"
                                                                     style="display:none;"></div>
                                                            </div>
                                                        </form>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }, 11, 1);
                                    ?>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="shortlist-cands-to-intrview ajax-enable"
                                       data-jid="<?php echo absint($_job_id); ?>"><?php esc_html_e('Shortlist', 'wp-jobsearch') ?>
                                        <span class="app-loader"></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="reject-cands-to-intrview ajax-enable"
                                       data-jid="<?php echo absint($_job_id); ?>"><?php esc_html_e('Reject', 'wp-jobsearch') ?>
                                        <span class="app-loader"></span></a>
                                </li>
                                <?php
                                }
                                ?>
                            </ul>
                        </div>
                    </div>

                    <?php do_action('jobsearch_empdash_aplics_btns_aftermore', $_job_id); ?>

                    <?php
                    if ($_mod_tab != 'insta_match') {
                        ob_start();
                        ?>
                        <div class="sort-list-view">
                            <a href="javascript:void(0);"
                               class="apps-view-btn<?php echo($_selected_view == 'list' ? ' active' : '') ?>"
                               data-view="list"><i class="fa fa-list"></i></a>
                            <a href="javascript:void(0);"
                               class="apps-view-btn<?php echo($_selected_view == 'grid' ? ' active' : '') ?>"
                               data-view="grid"><i class="fa fa-bars"></i></a>
                        </div>
                        <?php
                        $app_viewbtns_html = ob_get_clean();
                        echo apply_filters('jobseacrh_dash_manag_apps_viewbtns_html', $app_viewbtns_html, $_selected_view);
                    }
                    ?>
                </div>
                <?php
                if ($_mod_tab == 'insta_match' && $job_posttin_instamatch_cand == 'on') {
                    //
                    $job_all_skills = wp_get_post_terms($_job_id, 'skill');
                    $job_skils_arr = array();
                    if (!empty($job_all_skills)) {
                        foreach ($job_all_skills as $job_alskill) {
                            if (isset($job_alskill->name)) {
                                $job_skils_arr[] = $job_alskill->name;
                            }
                        }
                    }
                    //
                    $job_job_title = get_the_title($_job_id);
                    //
                    $job_all_sectors = wp_get_post_terms($_job_id, 'sector');
                    $job_sectrs_arr = array();
                    if (!empty($job_all_sectors)) {
                        foreach ($job_all_sectors as $job_alsec) {
                            if (isset($job_alsec->term_id)) {
                                $job_sectrs_arr[] = $job_alsec->term_id;
                            }
                        }
                    }
                    //
                    $total_records = $job_insta_match_list_c;

                    //$get_tagw_cands = get_post_meta($_job_id, 'jobsearch_instamatch_cands_fortag', true);
                    //update_post_meta($_job_id, 'jobsearch_instamatch_cands', $get_tagw_cands);

                    $job_applicants_list = jobsearch_job_applicants_sort_list($_job_id, $_sort_selected, 'jobsearch_instamatch_cands');

                    $start = ($page_num - 1) * ($reults_per_page);
                    $offset = $reults_per_page;
                    $job_applicants_list = array_slice($job_applicants_list, $start, $offset);
                    ?>
                    <div class="jobsearch-applied-jobs">
                        <?php
                        if (!empty($job_applicants_list)) { ?>
                            <script>
                                jQuery(function () {
                                    jQuery('.jobsearch-apppli-tooltip').tooltip();
                                });
                            </script>
                            <ul class="jobsearch-row">
                                <?php

                                foreach ($job_applicants_list as $_candidate_id) {

                                    $candidate_user_id = jobsearch_get_candidate_user_id($_candidate_id);
                                    if (absint($candidate_user_id) <= 0) {
                                        continue;
                                    }

                                    $candidate_user_obj = get_user_by('ID', $candidate_user_id);
                                    $user_def_avatar_url = jobsearch_candidate_img_url_comn($_candidate_id);

                                    $candidate_jobtitle = get_post_meta($_candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                                    //
                                    $match_jobtitle_clas = '';
                                    if ($job_job_title != '' && $candidate_jobtitle != '' && preg_match("/{$candidate_jobtitle}/i", $job_job_title)) {
                                        $match_jobtitle_clas = 'instamatch-job-title';
                                    }
                                    //
                                    $get_candidate_location = get_post_meta($_candidate_id, 'jobsearch_field_location_address', true);

                                    $candidate_city_title = '';
                                    $get_candidate_city = get_post_meta($_candidate_id, 'jobsearch_field_location_location3', true);
                                    if ($get_candidate_city == '') {
                                        $get_candidate_city = get_post_meta($_candidate_id, 'jobsearch_field_location_location2', true);
                                    }
                                    if ($get_candidate_city == '') {
                                        $get_candidate_city = get_post_meta($_candidate_id, 'jobsearch_field_location_location1', true);
                                    }

                                    $candidate_city_tax = $get_candidate_city != '' ? get_term_by('slug', $get_candidate_city, 'job-location') : '';
                                    if (is_object($candidate_city_tax)) {
                                        $candidate_city_title = $candidate_city_tax->name;
                                    }

                                    $sectors = wp_get_post_terms($_candidate_id, 'sector');
                                    $candidate_sector = isset($sectors[0]->name) ? $sectors[0]->name : '';
                                    $candidate_sector_id = isset($sectors[0]->term_id) ? $sectors[0]->term_id : '';

                                    $sector_match_clas = '';
                                    if (in_array($candidate_sector_id, $job_sectrs_arr)) {
                                        $sector_match_clas = ' class="insta-match-sector"';
                                    }

                                    $cand_skills = wp_get_post_terms($_candidate_id, 'skill');
                                    $candidate_salary = jobsearch_candidate_current_salary($_candidate_id);
                                    $candidate_age = jobsearch_candidate_age($_candidate_id);
                                    $candidate_phone = get_post_meta($_candidate_id, 'jobsearch_field_user_phone', true);
                                    $job_cver_ltrs = get_post_meta($_job_id, 'jobsearch_job_apply_cvrs', true);
                                    $send_message_form_rand = rand(1000000, 9999999);

                                    ?>
                                    <li class="jobsearch-column-12">
                                        <div class="jobsearch-applied-jobs-wrap">
                                            <div class="candidate-select-box">
                                                <input type="checkbox" name="app_candidate_sel[]"
                                                       id="app_candidate_sel_<?php echo $_candidate_id ?>"
                                                       value="<?php echo $_candidate_id ?>">
                                                <label for="app_candidate_sel_<?php echo $_candidate_id ?>"></label>
                                            </div>
                                            <a class="jobsearch-applied-jobs-thumb">
                                                <img src="<?php echo($user_def_avatar_url) ?>" alt="">
                                            </a>
                                            <div class="jobsearch-applied-jobs-text">
                                                <div class="jobsearch-applied-jobs-left">
                                                    <?php
                                                    $candidate_post = get_post($_candidate_id);
                                                    $candidate_date_posted = isset($candidate_post->post_date) ? $candidate_post->post_date : '';
                                                    ?>
                                                    <h2 class="instamatch-job-maintitle">
                                                        <a href="<?php echo add_query_arg(array('job_id' => $_job_id, 'employer_id' => $employer_id, 'action' => 'preview_profile'), get_permalink($_candidate_id)) ?>"><?php echo get_the_title($_candidate_id) ?></a>
                                                        <?php if ($candidate_age != '') { ?>
                                                            <small><?php echo apply_filters('jobsearch_dash_applicants_age_html', sprintf(esc_html__('(Age: %s years)', 'wp-jobsearch'), $candidate_age), $_candidate_id) ?></small>
                                                        <?php } ?>
                                                    </h2>
                                                    <?php if ($candidate_jobtitle != '') {
                                                        ?>
                                                        <span class="jobcand-job-title <?php echo($match_jobtitle_clas) ?>"> <?php echo($candidate_jobtitle) ?></span>
                                                        <?php
                                                    }

                                                    if (in_array($_candidate_id, $viewed_candidates)) { ?>
                                                        <small class="profile-view viewed"><?php esc_html_e('(Viewed)', 'wp-jobsearch') ?></small>
                                                    <?php } else { ?>
                                                        <small class="profile-view unviewed"><?php esc_html_e('(Unviewed)', 'wp-jobsearch') ?></small>
                                                    <?php } ?>
                                                    <ul>
                                                        <?php
                                                        if ($candidate_date_posted != '') {
                                                            $candidate_date_posted = strtotime($candidate_date_posted);
                                                            ?>
                                                            <li>
                                                                <i class="jobsearch-icon jobsearch-calendar"></i> <?php printf(esc_html__('Member: %s', 'wp-jobsearch'), jobsearch_time_elapsed_string($candidate_date_posted)); ?>
                                                            </li>
                                                        <?php }

                                                        if ($candidate_salary != '') { ?>
                                                            <li>
                                                                <i class="fa fa-money"></i> <?php printf(esc_html__('Salary: %s', 'wp-jobsearch'), $candidate_salary) ?>
                                                            </li>
                                                            <?php
                                                        }

                                                        $candidate_city_title = apply_filters('jobsearch_empdash_jobapp_litem_adrs_str', $candidate_city_title, $_candidate_id);
                                                        if ($candidate_city_title != '' && $all_location_allow == 'on') {
                                                            ?>
                                                            <li>
                                                                <i class="fa fa-map-marker"></i> <?php echo($candidate_city_title) ?>
                                                            </li>
                                                            <?php
                                                        }
                                                        if ($candidate_sector != '') {
                                                            ?>
                                                            <li>
                                                                <i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i>
                                                                <a<?php echo($sector_match_clas) ?>><?php echo($candidate_sector) ?></a>
                                                            </li>
                                                            <?php
                                                        }
                                                        ?>
                                                    </ul>
                                                    <?php if (!empty($cand_skills)) { ?>
                                                        <ul class="candskills-list">
                                                            <li>
                                                                <?php
                                                                $skills_name_arr = $matcskills_name_arr = $half_machskills_name_arr = array();
                                                                foreach ($cand_skills as $cand_skill) {
                                                                    $cand_skill_name = $cand_skill->name;
                                                                    if (in_array($cand_skill_name, $job_skils_arr)) {
                                                                        $matcskills_name_arr[] = '<span class="insta-match-skill">' . $cand_skill_name . '</span>';
                                                                        continue;
                                                                    }

                                                                    $half_match_skill = false;
                                                                    if (!empty($job_skils_arr)) {
                                                                        foreach ($job_skils_arr as $job_skill_name) {
                                                                            if ($cand_skill_name != '' && $job_skill_name != '' && $cand_skill_name != $job_skill_name && preg_match("/{$cand_skill_name}/i", $job_skill_name)) {
                                                                                $half_machskills_name_arr[] = '<span class="insta-halfmatch-skill">' . $cand_skill_name . '</span>';
                                                                                $half_match_skill = true;
                                                                                break;
                                                                            }
                                                                        }
                                                                    }
                                                                    if (!$half_match_skill) {
                                                                        $skills_name_arr[] = '<span>' . $cand_skill_name . '</span>';
                                                                    }
                                                                }
                                                                $cand_skills_html = implode('', $matcskills_name_arr) . implode('', $half_machskills_name_arr) . implode('', $skills_name_arr);
                                                                echo($cand_skills_html);
                                                                ?>
                                                            </li>
                                                        </ul>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>

                                                <div class="jobsearch-applied-job-btns">
                                                    <ul>
                                                        <li>
                                                            <a href="<?php echo add_query_arg(array('job_id' => $_job_id, 'employer_id' => $employer_id, 'action' => 'preview_profile'), get_permalink($_candidate_id)) ?>"
                                                               class="preview-candidate-profile"><i
                                                                        class="fa fa-eye"></i> <?php esc_html_e('Preview', 'wp-jobsearch') ?>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <div class="candidate-more-acts-con">
                                                                <a href="javascript:void(0);"
                                                                   class="more-actions"><?php esc_html_e('Actions', 'wp-jobsearch') ?>
                                                                    <i class="fa fa-angle-down"></i></a>
                                                                <ul>
                                                                    <?php
                                                                    $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
                                                                    $candidate_cv_file = get_post_meta($_candidate_id, 'candidate_cv_file', true);

                                                                    if ($multiple_cv_files_allow == 'on') {
                                                                        $ca_at_cv_files = get_post_meta($_candidate_id, 'candidate_cv_files', true);
                                                                        if (!empty($ca_at_cv_files)) {
                                                                            ?>
                                                                            <li>
                                                                                <a href="<?php echo apply_filters('jobsearch_user_attach_cv_file_url', '', $_candidate_id, $_job_id) ?>"
                                                                                   oncontextmenu="javascript: return false;"
                                                                                   onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                                                                   download="<?php echo apply_filters('jobsearch_user_attach_cv_file_title', '', $_candidate_id, $_job_id) ?>"><?php esc_html_e('Download CV', 'wp-jobsearch') ?></a>
                                                                            </li>
                                                                            <?php
                                                                        }
                                                                    } else if (!empty($candidate_cv_file)) {
                                                                        $file_attach_id = isset($candidate_cv_file['file_id']) ? $candidate_cv_file['file_id'] : '';
                                                                        $file_url = isset($candidate_cv_file['file_url']) ? $candidate_cv_file['file_url'] : '';

                                                                        $filename = isset($candidate_cv_file['file_name']) ? $candidate_cv_file['file_name'] : '';

                                                                        $file_url = apply_filters('wp_jobsearch_user_cvfile_downlod_url', $file_url, $file_attach_id, $_candidate_id);
                                                                        ?>
                                                                        <li><a href="<?php echo($file_url) ?>"
                                                                               oncontextmenu="javascript: return false;"
                                                                               onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                                                               download="<?php echo($filename) ?>"><?php esc_html_e('Download CV', 'wp-jobsearch') ?></a>
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                    <li>
                                                                        <a href="javascript:void(0);"
                                                                           class="move-cand-from-instamatch ajax-enable"
                                                                           data-jid="<?php echo absint($_job_id); ?>"
                                                                           data-cid="<?php echo absint($_candidate_id); ?>"><?php esc_html_e('Move to Applicants', 'wp-jobsearch') ?>
                                                                            <span class="app-loader"></span></a>
                                                                    </li>
                                                                    <?php
                                                                    $args = array(
                                                                        'candidate_id' => $_candidate_id,
                                                                        'view' => 'list',
                                                                    );
                                                                    apply_filters('jobsearch_cand_generate_resume_btn', $args);
                                                                    ?>
                                                                </ul>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                        <?php } ?>
                    </div>
                    <?php
                    if (!empty($job_applicants_list)) {
                        $total_pages = 1;
                        if ($total_records > 0 && $reults_per_page > 0 && $total_records > $reults_per_page) {
                            $total_pages = ceil($total_records / $reults_per_page);
                            ?>
                            <div class="jobsearch-pagination-blog">
                                <?php $Jobsearch_User_Dashboard_Settings->pagination($total_pages, $page_num, $page_url) ?>
                            </div>
                            <?php
                        }
                    }
                } else {
                    if ($_mod_tab == 'shortlisted') {
                        $job_applicants_list = jobsearch_job_applicants_sort_list($_job_id, $_sort_selected, '_job_short_interview_list');
                    } else if ($_mod_tab == 'rejected') {
                        $job_applicants_list = jobsearch_job_applicants_sort_list($_job_id, $_sort_selected, '_job_reject_interview_list');
                    } else {
                        $job_applicants_list = jobsearch_job_applicants_sort_list($_job_id, $_sort_selected);
                    }

                    $job_applicants_list = apply_filters('jobsearch_mangejob_applics_list_arr', $job_applicants_list);

                    $total_records = !empty($job_applicants_list) ? count($job_applicants_list) : 0;

                    $start = ($page_num - 1) * ($reults_per_page);
                    $offset = $reults_per_page;
                    $job_applicants_list = array_slice($job_applicants_list, $start, $offset);

                    //
                    $job_instamatch_candtags = get_post_meta($_job_id, 'jobsearch_instamatch_cands_fortag', true);
                    //
                    ob_start();
                    ?>
                    <div class="jobsearch-applied-jobs <?php echo($_selected_view == 'grid' ? 'aplicants-grid-view' : '') ?>">
                        <?php
                        if (!empty($job_applicants_list)) { ?>
                            <script>
                                jQuery(function () {
                                    jQuery('.jobsearch-apppli-tooltip').tooltip();
                                });
                            </script>
                            <ul class="jobsearch-row">
                                <?php

                                foreach ($job_applicants_list as $_candidate_id) {

                                    $candidate_user_id = jobsearch_get_candidate_user_id($_candidate_id);
                                    if (absint($candidate_user_id) <= 0) {
                                        continue;
                                    }
                                    $insta_cand_app = false;
                                    if (!empty($job_instamatch_candtags) && in_array($_candidate_id, $job_instamatch_candtags)) {
                                        $insta_cand_app = true;
                                    }
                                    $candidate_user_obj = get_user_by('ID', $candidate_user_id);
                                    $user_def_avatar_url = jobsearch_candidate_img_url_comn($_candidate_id);

                                    $candidate_jobtitle = get_post_meta($_candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                                    $get_candidate_location = get_post_meta($_candidate_id, 'jobsearch_field_location_address', true);

                                    $candidate_city_title = '';
                                    $get_candidate_city = get_post_meta($_candidate_id, 'jobsearch_field_location_location3', true);
                                    if ($get_candidate_city == '') {
                                        $get_candidate_city = get_post_meta($_candidate_id, 'jobsearch_field_location_location2', true);
                                    }
                                    if ($get_candidate_city == '') {
                                        $get_candidate_city = get_post_meta($_candidate_id, 'jobsearch_field_location_location1', true);
                                    }

                                    $candidate_city_tax = $get_candidate_city != '' ? get_term_by('slug', $get_candidate_city, 'job-location') : '';
                                    if (is_object($candidate_city_tax)) {
                                        $candidate_city_title = $candidate_city_tax->name;
                                    }

                                    $sectors = wp_get_post_terms($_candidate_id, 'sector');
                                    $candidate_sector = isset($sectors[0]->name) ? $sectors[0]->name : '';

                                    $candidate_salary = jobsearch_candidate_current_salary($_candidate_id);
                                    $candidate_age = jobsearch_candidate_age($_candidate_id);

                                    $candidate_phone = get_post_meta($_candidate_id, 'jobsearch_field_user_phone', true);

                                    $job_cver_ltrs = get_post_meta($_job_id, 'jobsearch_job_apply_cvrs', true);

                                    $send_message_form_rand = rand(1000000, 9999999);
                                    
                                    $applicant_status = jobsearch_get_applicant_status_tarr($_candidate_id, $_job_id);

                                    if ($_selected_view == 'grid') { ?>
                                        <li class="jobsearch-column-4<?php echo (isset($applicant_status['status']) && $applicant_status['status'] == 'pending' ? ' applicant-status-pending' : '') ?>">
                                            
                                            <script>
                                                jQuery(document).on('click', '.jobsearch-modelemail-btn-<?php echo($send_message_form_rand) ?>', function () {
                                                    jobsearch_modal_popup_open('JobSearchModalSendEmail<?php echo($send_message_form_rand) ?>');
                                                });
                                                jQuery(document).on('click', '.jobsearch-modelcvrltr-btn-<?php echo($send_message_form_rand) ?>', function () {
                                                    jobsearch_modal_popup_open('JobSearchCandCovershwModal<?php echo($send_message_form_rand) ?>');
                                                });
                                            </script>
                                            <div class="aplicants-grid-view-wrap">
                                                <?php
                                                $cand_is_pending = false;
                                                if (isset($applicant_status['status']) && $applicant_status['status'] == 'pending') {
                                                    $cand_is_pending = true;
                                                    echo jobsearch_applicant_pend_profile_review_txt();
                                                }
                                                ?>
                                                <div class="aplicants-grid-inner-con">
                                                    <div class="candidate-select-box">
                                                        <?php
                                                        if (!$cand_is_pending) {
                                                            ?>
                                                            <input type="checkbox" name="app_candidate_sel[]"
                                                                   id="app_candidate_sel_<?php echo $_candidate_id ?>"
                                                                   value="<?php echo $_candidate_id ?>">
                                                            <label for="app_candidate_sel_<?php echo $_candidate_id ?>"></label>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                    <a class="aplicants-grid-view-thumb">
                                                        <img src="<?php echo($user_def_avatar_url) ?>" alt="">
                                                    </a>
                                                    <?php echo apply_filters('jobsearch_applicants_list_before_title', '', $_candidate_id, $_job_id); ?>
                                                    <h2>
                                                        <a href="<?php echo add_query_arg(array('job_id' => $_job_id, 'employer_id' => $employer_id, 'action' => 'preview_profile'), get_permalink($_candidate_id)) ?>"><?php echo get_the_title($_candidate_id) ?></a>
                                                    </h2>
                                                    <p>
                                                        <?php
                                                        if ($candidate_jobtitle != '') {
                                                            echo($candidate_jobtitle);
                                                        }
                                                        if ($candidate_jobtitle != '' && $candidate_sector != '') {
                                                            echo ', ';
                                                        }
                                                        if ($candidate_sector != '') {
                                                            echo '<a>' . ($candidate_sector) . '</a>';
                                                        }
                                                        ?>
                                                    </p>
                                                    <?php
                                                    if ($candidate_salary != '') {
                                                        echo '<p>' . sprintf(esc_html__('Salary: %s', 'wp-jobsearch'), $candidate_salary) . '</p>';
                                                    }
                                                    ?>
                                                    <ul class="short-li-icons">
                                                        <li class="jobsearch-apppli-tooltip <?php echo(in_array($_candidate_id, $viewed_candidates) ? 'viewd' : 'unviewed') ?>"
                                                            title="<?php echo(in_array($_candidate_id, $viewed_candidates) ? esc_html__('Viewed', 'wp-jobsearch') : esc_html__('Unviewed', 'wp-jobsearch')) ?>">
                                                            <a href="<?php echo add_query_arg(array('job_id' => $_job_id, 'employer_id' => $employer_id, 'action' => 'preview_profile'), get_permalink($_candidate_id)) ?>"><i
                                                                        class="jobsearch-icon jobsearch-view"></i></a>
                                                        </li>

                                                        <?php
                                                        if ($candidate_phone != '') { ?>
                                                            <li><a class="jobsearch-apppli-tooltip"
                                                                   href="tel:<?php echo($candidate_phone) ?>"
                                                                   title="<?php printf(esc_html__('Phone: %s', 'wp-jobsearch'), $candidate_phone) ?>"><i
                                                                            class="jobsearch-icon jobsearch-technology"></i></a>
                                                            </li>
                                                            <?php
                                                        }
                                                        if (!in_array($_candidate_id, $job_reject_int_list)) {

                                                            if (in_array($_candidate_id, $job_short_int_list)) {
                                                                ?>
                                                                <li><a href="javascript:void(0);"
                                                                       class="shortlist-cand-to-intrview ap-shortlist-btn"><i
                                                                                class="careerfy-icon careerfy-heart"></i> <?php esc_html_e('Shortlisted', 'wp-jobsearch') ?>
                                                                    </a></li>
                                                            <?php } else { ?>
                                                                <li><a href="javascript:void(0);"
                                                                       class="shortlist-cand-to-intrview ap-shortlist-btn ajax-enable"
                                                                       data-jid="<?php echo absint($_job_id); ?>"
                                                                       data-cid="<?php echo absint($_candidate_id); ?>"><i
                                                                                class="jobsearch-icon jobsearch-heart"></i> <?php esc_html_e('Shortlist', 'wp-jobsearch') ?>
                                                                        <span class="app-loader"></span></a></li>
                                                                <?php
                                                            }
                                                        }

                                                        $args = array(
                                                            'candidate_id' => $_candidate_id,
                                                            'view' => 'list',
                                                            'icon' => 'fa fa-file-pdf-o',
                                                            'class' => 'jobsearch-apppli-tooltip jobsearch-pdf-grid-btn',
                                                            'label' => '',
                                                            'title' => esc_html__('Generate PDF', 'wp-jobsearch')
                                                        );
                                                        apply_filters('jobsearch_cand_generate_resume_btn', $args);

                                                        //
                                                        echo apply_filters('employer_dash_apps_acts_grid_after_btns', '', $_candidate_id, $_job_id);
                                                        ?>
                                                    </ul>
                                                </div>

                                                <?php
                                                echo apply_filters('employer_dash_apps_acts_listul_after', '', $_candidate_id, $_job_id);
                                                ?>
                                                <ul class="short-lidown-icons">
                                                    <?php
                                                    $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
                                                    $candidate_cv_file = get_post_meta($_candidate_id, 'candidate_cv_file', true);

                                                    if ($multiple_cv_files_allow == 'on') {
                                                        $ca_at_cv_files = get_post_meta($_candidate_id, 'candidate_cv_files', true);
                                                        if (!empty($ca_at_cv_files)) {
                                                            ?>
                                                            <li class="down-cv-donlod"><a
                                                                        href="<?php echo apply_filters('jobsearch_user_attach_cv_file_url', '', $_candidate_id, $_job_id) ?>"
                                                                        class="jobsearch-apppli-tooltip"
                                                                        title="<?php esc_html_e('Download CV', 'wp-jobsearch') ?>"
                                                                        oncontextmenu="javascript: return false;"
                                                                        onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                                                        download="<?php echo apply_filters('jobsearch_user_attach_cv_file_title', '', $_candidate_id, $_job_id) ?>"><i
                                                                            class="jobsearch-icon jobsearch-download-arrow"></i></a>
                                                            </li>
                                                            <?php
                                                        }
                                                    } else if (!empty($candidate_cv_file)) {
                                                        $file_attach_id = isset($candidate_cv_file['file_id']) ? $candidate_cv_file['file_id'] : '';
                                                        $file_url = isset($candidate_cv_file['file_url']) ? $candidate_cv_file['file_url'] : '';

                                                        $filename = isset($candidate_cv_file['file_name']) ? $candidate_cv_file['file_name'] : '';

                                                        $file_url = apply_filters('wp_jobsearch_user_cvfile_downlod_url', $file_url, $file_attach_id, $_candidate_id);

                                                        ?>
                                                        <li class="down-cv-donlod"><a href="<?php echo($file_url) ?>"
                                                                                      class="jobsearch-apppli-tooltip"
                                                                                      title="<?php esc_html_e('Download CV', 'wp-jobsearch') ?>"
                                                                                      oncontextmenu="javascript: return false;"
                                                                                      onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                                                                      download="<?php echo($filename) ?>"><i
                                                                        class="jobsearch-icon jobsearch-download-arrow"></i></a>
                                                        </li>
                                                        <?php
                                                    }
                                                    echo apply_filters('employer_dash_apps_acts_list_after_download_link', '', $_candidate_id, $_job_id, 'grid');

                                                    if (isset($job_cver_ltrs[$_candidate_id]) && $job_cver_ltrs[$_candidate_id] != '') {
                                                        ?>
                                                        <li class="down-view-cvrltr"><a href="javascript:void(0);"
                                                                                        class="jobsearch-apppli-tooltip jobsearch-modelcvrltr-btn-<?php echo($send_message_form_rand) ?>"
                                                                                        title="<?php esc_html_e('View Cover Letter', 'wp-jobsearch') ?>"><i
                                                                        class="fa fa-eye"></i></a></li>
                                                    <?php } ?>
                                                    <li class="down-emial-candcon">
                                                        <a href="javascript:void(0);"
                                                           class="jobsearch-apppli-tooltip jobsearch-modelemail-btn-<?php echo($send_message_form_rand) ?>"
                                                           title="<?php esc_html_e('Email to Candidate', 'wp-jobsearch') ?>"><i
                                                                    class="fa fa-envelope-o"></i></a>
                                                        <?php
                                                        $popup_args = array('p_job_id' => $_job_id, 'cand_id' => $_candidate_id, 'p_emp_id' => $employer_id, 'p_masg_rand' => $send_message_form_rand);
                                                        add_action('wp_footer', function () use ($popup_args) {

                                                            extract(shortcode_atts(array(
                                                                'p_job_id' => '',
                                                                'p_emp_id' => '',
                                                                'cand_id' => '',
                                                                'p_masg_rand' => ''
                                                            ), $popup_args));
                                                            ?>
                                                            <div class="jobsearch-modal fade"
                                                                 id="JobSearchModalSendEmail<?php echo($p_masg_rand) ?>">
                                                                <div class="modal-inner-area">&nbsp;</div>
                                                                <div class="modal-content-area">
                                                                    <div class="modal-box-area">
                                                                        <span class="modal-close"><i
                                                                                    class="fa fa-times"></i></span>
                                                                        <div class="jobsearch-send-message-form">
                                                                            <form method="post"
                                                                                  id="jobsearch_send_email_form<?php echo esc_html($p_masg_rand); ?>">
                                                                                <div class="jobsearch-user-form">
                                                                                    <ul class="email-fields-list">
                                                                                        <li>
                                                                                            <label>
                                                                                                <?php echo esc_html__('Subject', 'wp-jobsearch'); ?>
                                                                                                :
                                                                                            </label>
                                                                                            <div class="input-field">
                                                                                                <input type="text"
                                                                                                       name="send_message_subject"
                                                                                                       value=""/>
                                                                                            </div>
                                                                                        </li>
                                                                                        <li>
                                                                                            <label>
                                                                                                <?php echo esc_html__('Message', 'wp-jobsearch'); ?>
                                                                                                :
                                                                                            </label>
                                                                                            <div class="input-field">
                                                                                                <textarea
                                                                                                        name="send_message_content"></textarea>
                                                                                            </div>
                                                                                        </li>
                                                                                        <li>
                                                                                            <div class="input-field-submit">
                                                                                                <input type="submit"
                                                                                                       class="applicantto-email-submit-btn"
                                                                                                       data-jid="<?php echo absint($p_job_id); ?>"
                                                                                                       data-eid="<?php echo absint($p_emp_id); ?>"
                                                                                                       data-cid="<?php echo absint($cand_id); ?>"
                                                                                                       data-randid="<?php echo esc_html($p_masg_rand); ?>"
                                                                                                       name="send_message_content"
                                                                                                       value="<?php echo esc_html__('Send', 'wp-jobsearch') ?>"/>
                                                                                                <span class="loader-box loader-box-<?php echo esc_html($p_masg_rand); ?>"></span>
                                                                                            </div>
                                                                                            <?php jobsearch_terms_and_con_link_txt(); ?>
                                                                                        </li>
                                                                                    </ul>
                                                                                    <div class="message-box message-box-<?php echo esc_html($p_masg_rand); ?>"
                                                                                         style="display:none;"></div>
                                                                                </div>
                                                                            </form>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }, 11, 1);
                                                        ?>
                                                    </li>
                                                    <?php
                                                    if (in_array($_candidate_id, $job_reject_int_list)) {
                                                        ?>
                                                        <li class="down-cand-rejct">
                                                            <a href="javascript:void(0);"
                                                               class="undoreject-cand-to-list jobsearch-apppli-tooltip ajax-enable"
                                                               data-jid="<?php echo absint($_job_id); ?>"
                                                               data-cid="<?php echo absint($_candidate_id); ?>"
                                                               title="<?php esc_html_e('Undo Reject', 'wp-jobsearch') ?>"><i
                                                                        class="fa fa-undo"></i> <span
                                                                        class="app-loader"></span></a>
                                                        </li>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <li class="down-cand-rejct"><a href="javascript:void(0);"
                                                                                       class="reject-cand-to-intrview jobsearch-apppli-tooltip ajax-enable"
                                                                                       data-jid="<?php echo absint($_job_id); ?>"
                                                                                       data-cid="<?php echo absint($_candidate_id); ?>"
                                                                                       title="<?php esc_html_e('Reject', 'wp-jobsearch') ?>"><i
                                                                        class="fa fa-ban"></i> <span
                                                                        class="app-loader"></span></a></li>
                                                        <?php
                                                    }
                                                    ?>
                                                    <li class="down-cand-dtrash"><a href="javascript:void(0);"
                                                                                    class="delete-cand-from-job jobsearch-apppli-tooltip ajax-enable"
                                                                                    data-jid="<?php echo absint($_job_id); ?>"
                                                                                    data-cid="<?php echo absint($_candidate_id); ?>"
                                                                                    title="<?php esc_html_e('Delete', 'wp-jobsearch') ?>"><i
                                                                    class="fa fa-trash"></i> <span
                                                                    class="app-loader"></span></a></li>
                                                </ul>
                                            </div>
                                        </li>
                                        <?php
                                    } else {
                                        ?>
                                        <li class="jobsearch-column-12<?php echo (isset($applicant_status['status']) && $applicant_status['status'] == 'pending' ? ' applicant-status-pending' : '') ?>">
                                            <script>
                                                jQuery(document).on('click', '.jobsearch-modelemail-btn-<?php echo($send_message_form_rand) ?>', function () {
                                                    jobsearch_modal_popup_open('JobSearchModalSendEmail<?php echo($send_message_form_rand) ?>');
                                                });
                                                jQuery(document).on('click', '.jobsearch-modelcvrltr-btn-<?php echo($send_message_form_rand) ?>', function () {
                                                    jobsearch_modal_popup_open('JobSearchCandCovershwModal<?php echo($send_message_form_rand) ?>');
                                                });
                                            </script>
                                            <div class="jobsearch-applied-jobs-wrap">
                                                <?php
                                                $cand_is_pending = false;
                                                if (isset($applicant_status['status']) && $applicant_status['status'] == 'pending') {
                                                    $cand_is_pending = true;
                                                    echo jobsearch_applicant_pend_profile_review_txt();
                                                }
                                                ?>
                                                <div class="candidate-select-box">
                                                    <?php
                                                    if (!$cand_is_pending) {
                                                        ?>
                                                        <input type="checkbox" name="app_candidate_sel[]"
                                                               id="app_candidate_sel_<?php echo $_candidate_id ?>"
                                                               value="<?php echo $_candidate_id ?>">
                                                        <label for="app_candidate_sel_<?php echo $_candidate_id ?>"></label>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <a class="jobsearch-applied-jobs-thumb">
                                                    <img src="<?php echo($user_def_avatar_url) ?>" alt="">
                                                </a>
                                                <div class="jobsearch-applied-jobs-text">
                                                    <div class="jobsearch-applied-jobs-left">
                                                        <?php
                                                        if ($insta_cand_app && $job_posttin_instamatch_cand == 'on') {
                                                            ?>
                                                            <div class="jobsearch-instamatch-applic">
                                                                <strong><?php esc_html_e('Insta Match', 'wp-jobsearch') ?></strong>
                                                            </div>
                                                            <?php
                                                        }
                                                        $user_apply_data = get_user_meta($candidate_user_id, 'jobsearch-user-jobs-applied-list', true);
                                                        $aply_date_time = '';
                                                        if (!empty($user_apply_data)) {
                                                            $user_apply_key = array_search($_job_id, array_column($user_apply_data, 'post_id'));
                                                            $aply_date_time = isset($user_apply_data[$user_apply_key]['date_time']) ? $user_apply_data[$user_apply_key]['date_time'] : '';
                                                        }
                                                        if ($candidate_jobtitle != '') {
                                                            ?>
                                                            <span> <?php echo($candidate_jobtitle) ?></span>
                                                            <?php
                                                        }

                                                        if (in_array($_candidate_id, $viewed_candidates)) {
                                                            ?>
                                                            <small class="profile-view viewed"><?php esc_html_e('(Viewed)', 'wp-jobsearch') ?></small>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <small class="profile-view unviewed"><?php esc_html_e('(Unviewed)', 'wp-jobsearch') ?></small>
                                                            <?php
                                                        }

                                                        $user_email_adr = isset($candidate_user_obj->user_email) ? $candidate_user_obj->user_email : '';
                                                        ?>
                                                        <a href="javascript:void(0);"
                                                           class="jobsearch-modelemail-btn-<?php echo($send_message_form_rand) ?> jobsearch-user-email">
                                                            <small> <?php printf(esc_html__('Email: %s', 'wp-jobsearch'), $user_email_adr) ?></small>
                                                        </a>
                                                        <?php

                                                        echo apply_filters('jobsearch_applicants_list_before_title', '', $_candidate_id, $_job_id);
                                                        ?>
                                                        <h2>
                                                            <a href="<?php echo add_query_arg(array('job_id' => $_job_id, 'employer_id' => $employer_id, 'action' => 'preview_profile'), get_permalink($_candidate_id)) ?>"><?php echo get_the_title($_candidate_id) ?></a>
                                                            <?php
                                                            if ($candidate_age != '') {
                                                                ?>
                                                                <small><?php echo apply_filters('jobsearch_dash_applicants_age_html', sprintf(esc_html__('(Age: %s years)', 'wp-jobsearch'), $candidate_age), $_candidate_id) ?></small>
                                                                <?php
                                                            }
                                                            if ($candidate_phone != '') {
                                                                ?>
                                                                <small><?php printf(esc_html__('Phone: %s', 'wp-jobsearch'), $candidate_phone) ?></small>
                                                                <?php
                                                            }
                                                            ?>
                                                        </h2>
                                                        <ul>
                                                            <?php
                                                            if ($aply_date_time > 0) {
                                                                ?>
                                                                <li>
                                                                    <i class="jobsearch-icon jobsearch-calendar"></i> <?php printf(esc_html__('Applied at: %s', 'wp-jobsearch'), (date_i18n(get_option('date_format'), $aply_date_time) . ' ' . date_i18n(get_option('time_format'), $aply_date_time))) ?>
                                                                </li>
                                                                <?php
                                                            }
                                                            if ($candidate_salary != '') {
                                                                ?>
                                                                <li>
                                                                    <i class="fa fa-money"></i> <?php printf(esc_html__('Salary: %s', 'wp-jobsearch'), $candidate_salary) ?>
                                                                </li>
                                                                <?php
                                                            }

                                                            $candidate_city_title = apply_filters('jobsearch_empdash_jobapp_litem_adrs_str', $candidate_city_title, $_candidate_id);
                                                            if ($candidate_city_title != '' && $all_location_allow == 'on') {
                                                                ?>
                                                                <li>
                                                                    <i class="fa fa-map-marker"></i> <?php echo($candidate_city_title) ?>
                                                                </li>
                                                                <?php
                                                            }
                                                            if ($candidate_sector != '') {
                                                                ?>
                                                                <li>
                                                                    <i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i>
                                                                    <a><?php echo($candidate_sector) ?></a></li>
                                                                <?php
                                                            }
                                                            ?>
                                                        </ul>
                                                    </div>
                                                    <?php
                                                    ob_start();
                                                    ?>
                                                    <div class="jobsearch-applied-job-btns">
                                                        <?php
                                                        echo apply_filters('employer_dash_apps_acts_listul_after', '', $_candidate_id, $_job_id);
                                                        ?>
                                                        <ul>
                                                            <li>
                                                                <a href="<?php echo add_query_arg(array('job_id' => $_job_id, 'employer_id' => $employer_id, 'action' => 'preview_profile'), get_permalink($_candidate_id)) ?>"
                                                                   class="preview-candidate-profile"><i
                                                                            class="fa fa-eye"></i> <?php esc_html_e('Preview', 'wp-jobsearch') ?>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <div class="candidate-more-acts-con">
                                                                    <a href="javascript:void(0);"
                                                                       class="more-actions"><?php esc_html_e('Actions', 'wp-jobsearch') ?>
                                                                        <i class="fa fa-angle-down"></i></a>
                                                                    <ul>
                                                                        <?php
                                                                        $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';
                                                                        $candidate_cv_file = get_post_meta($_candidate_id, 'candidate_cv_file', true);

                                                                        if ($multiple_cv_files_allow == 'on') {
                                                                            $ca_at_cv_files = get_post_meta($_candidate_id, 'candidate_cv_files', true);
                                                                            if (!empty($ca_at_cv_files)) { ?>
                                                                                <li>
                                                                                    <a href="<?php echo apply_filters('jobsearch_user_attach_cv_file_url', '', $_candidate_id, $_job_id) ?>"
                                                                                       oncontextmenu="javascript: return false;"
                                                                                       onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                                                                       download="<?php echo apply_filters('jobsearch_user_attach_cv_file_title', '', $_candidate_id, $_job_id) ?>"><?php esc_html_e('Download CV', 'wp-jobsearch') ?></a>
                                                                                </li>
                                                                                <?php
                                                                            }
                                                                        } else if (!empty($candidate_cv_file)) {
                                                                            $file_attach_id = isset($candidate_cv_file['file_id']) ? $candidate_cv_file['file_id'] : '';
                                                                            $file_url = isset($candidate_cv_file['file_url']) ? $candidate_cv_file['file_url'] : '';
                                                                            $filename = isset($candidate_cv_file['file_name']) ? $candidate_cv_file['file_name'] : '';
                                                                            $file_url = apply_filters('wp_jobsearch_user_cvfile_downlod_url', $file_url, $file_attach_id, $_candidate_id);
                                                                            ?>
                                                                            <li><a href="<?php echo($file_url) ?>"
                                                                                   oncontextmenu="javascript: return false;"
                                                                                   onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                                                                   download="<?php echo($filename) ?>"><?php esc_html_e('Download CV', 'wp-jobsearch') ?></a>
                                                                            </li>
                                                                            <?php
                                                                        }
                                                                        echo apply_filters('employer_dash_apps_acts_list_after_download_link', '', $_candidate_id, $_job_id, 'list');

                                                                        if (isset($job_cver_ltrs[$_candidate_id]) && $job_cver_ltrs[$_candidate_id] != '') { ?>
                                                                            <li><a href="javascript:void(0);"
                                                                                   class="jobsearch-modelcvrltr-btn-<?php echo($send_message_form_rand) ?>"><?php esc_html_e('View Cover Letter', 'wp-jobsearch') ?></a>
                                                                            </li>
                                                                        <?php } ?>

                                                                        <li>
                                                                            <a href="javascript:void(0);"
                                                                               class="jobsearch-modelemail-btn-<?php echo($send_message_form_rand) ?>"><?php esc_html_e('Email to Candidate', 'wp-jobsearch') ?></a>
                                                                            <?php
                                                                            $popup_args = array('p_job_id' => $_job_id, 'cand_id' => $_candidate_id, 'p_emp_id' => $employer_id, 'p_masg_rand' => $send_message_form_rand);
                                                                            add_action('wp_footer', function () use ($popup_args) {

                                                                                extract(shortcode_atts(array(
                                                                                    'p_job_id' => '',
                                                                                    'p_emp_id' => '',
                                                                                    'cand_id' => '',
                                                                                    'p_masg_rand' => ''
                                                                                ), $popup_args));
                                                                                ?>
                                                                                <div class="jobsearch-modal fade"
                                                                                     id="JobSearchModalSendEmail<?php echo($p_masg_rand) ?>">
                                                                                    <div class="modal-inner-area">
                                                                                        &nbsp;
                                                                                    </div>
                                                                                    <div class="modal-content-area">
                                                                                        <div class="modal-box-area">
                                                                                            <span class="modal-close"><i
                                                                                                        class="fa fa-times"></i></span>
                                                                                            <div class="jobsearch-send-message-form">
                                                                                                <form method="post"
                                                                                                      id="jobsearch_send_email_form<?php echo esc_html($p_masg_rand); ?>">
                                                                                                    <div class="jobsearch-user-form">
                                                                                                        <ul class="email-fields-list">
                                                                                                            <li>
                                                                                                                <label>
                                                                                                                    <?php echo esc_html__('Subject', 'wp-jobsearch'); ?>
                                                                                                                    :
                                                                                                                </label>
                                                                                                                <div class="input-field">
                                                                                                                    <input type="text"
                                                                                                                           name="send_message_subject"
                                                                                                                           value=""/>
                                                                                                                </div>
                                                                                                            </li>
                                                                                                            <li>
                                                                                                                <label>
                                                                                                                    <?php echo esc_html__('Message', 'wp-jobsearch'); ?>
                                                                                                                    :
                                                                                                                </label>
                                                                                                                <div class="input-field">
                                                                                                                    <textarea
                                                                                                                            name="send_message_content"></textarea>
                                                                                                                </div>
                                                                                                            </li>
                                                                                                            <li>
                                                                                                                <div class="input-field-submit">
                                                                                                                    <input type="submit"
                                                                                                                           class="applicantto-email-submit-btn"
                                                                                                                           data-jid="<?php echo absint($p_job_id); ?>"
                                                                                                                           data-eid="<?php echo absint($p_emp_id); ?>"
                                                                                                                           data-cid="<?php echo absint($cand_id); ?>"
                                                                                                                           data-randid="<?php echo esc_html($p_masg_rand); ?>"
                                                                                                                           name="send_message_content"
                                                                                                                           value="<?php echo esc_html__('Send', 'wp-jobsearch') ?>"/>
                                                                                                                    <span class="loader-box loader-box-<?php echo esc_html($p_masg_rand); ?>"></span>
                                                                                                                </div>
                                                                                                                <?php jobsearch_terms_and_con_link_txt(); ?>
                                                                                                            </li>
                                                                                                        </ul>
                                                                                                        <div class="message-box message-box-<?php echo esc_html($p_masg_rand); ?>"
                                                                                                             style="display:none;"></div>
                                                                                                    </div>
                                                                                                </form>
                                                                                            </div>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <?php
                                                                            }, 11, 1);
                                                                            ?>
                                                                        </li>
                                                                        <?php
                                                                        if (in_array($_candidate_id, $job_reject_int_list)) {
                                                                            ?>
                                                                            <li>
                                                                                <a href="javascript:void(0);"
                                                                                   class="undoreject-cand-to-list ajax-enable"
                                                                                   data-jid="<?php echo absint($_job_id); ?>"
                                                                                   data-cid="<?php echo absint($_candidate_id); ?>"><?php esc_html_e('Undo Reject', 'wp-jobsearch') ?>
                                                                                    <span class="app-loader"></span></a>
                                                                            </li>
                                                                            <?php
                                                                        } else {
                                                                            ?>
                                                                            <li>
                                                                                <?php
                                                                                if (in_array($_candidate_id, $job_short_int_list)) {
                                                                                    ?>
                                                                                    <a href="javascript:void(0);"
                                                                                       class="shortlist-cand-to-intrview"><?php esc_html_e('Shortlisted', 'wp-jobsearch') ?></a>
                                                                                    <?php
                                                                                } else {
                                                                                    ?>
                                                                                    <a href="javascript:void(0);"
                                                                                       class="shortlist-cand-to-intrview ajax-enable"
                                                                                       data-jid="<?php echo absint($_job_id); ?>"
                                                                                       data-cid="<?php echo absint($_candidate_id); ?>"><?php esc_html_e('Shortlist for Interview', 'wp-jobsearch') ?>
                                                                                        <span class="app-loader"></span></a>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </li>
                                                                            <?php
                                                                            $args = array(
                                                                                'candidate_id' => $_candidate_id,
                                                                                'view' => 'list',
                                                                            );
                                                                            apply_filters('jobsearch_cand_generate_resume_btn', $args);
                                                                            ?>
                                                                            <li>
                                                                                <?php
                                                                                if (in_array($_candidate_id, $job_reject_int_list)) {
                                                                                    ?>
                                                                                    <a href="javascript:void(0);"
                                                                                       class="reject-cand-to-intrview"><?php esc_html_e('Rejected', 'wp-jobsearch') ?></a>
                                                                                    <?php
                                                                                } else {
                                                                                    ?>
                                                                                    <a href="javascript:void(0);"
                                                                                       class="reject-cand-to-intrview ajax-enable"
                                                                                       data-jid="<?php echo absint($_job_id); ?>"
                                                                                       data-cid="<?php echo absint($_candidate_id); ?>"><?php esc_html_e('Reject', 'wp-jobsearch') ?>
                                                                                        <span class="app-loader"></span></a>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </li>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                        <li>
                                                                            <a href="javascript:void(0);"
                                                                               class="delete-cand-from-job ajax-enable"
                                                                               data-jid="<?php echo absint($_job_id); ?>"
                                                                               data-cid="<?php echo absint($_candidate_id); ?>"><?php esc_html_e('Delete', 'wp-jobsearch') ?>
                                                                                <span class="app-loader"></span></a>
                                                                        </li>

                                                                    </ul>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <?php
                                                    $app_actbtns_html = ob_get_clean();
                                                    echo apply_filters('jobseacrh_dash_manag_apps_actbtns_html', $app_actbtns_html, $_candidate_id, $_job_id, $employer_id, $send_message_form_rand);
                                                    ?>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    $popup_args = array(
                                        'job_id' => $_job_id,
                                        'rand_num' => $send_message_form_rand,
                                        'candidate_id' => $_candidate_id,
                                    );
                                    add_action('wp_footer', function () use ($popup_args) {

                                        global $jobsearch_plugin_options;

                                        extract(shortcode_atts(array(
                                            'job_id' => '',
                                            'rand_num' => '',
                                            'candidate_id' => '',
                                        ), $popup_args));

                                        $job_cver_ltrs = get_post_meta($job_id, 'jobsearch_job_apply_cvrs', true);
                                        if (isset($job_cver_ltrs[$candidate_id]) && $job_cver_ltrs[$candidate_id] != '') {
                                            ?>
                                            <div class="jobsearch-modal jobsearch-typo-wrap jobsearch-candcover-popup fade"
                                                 id="JobSearchCandCovershwModal<?php echo($rand_num) ?>">
                                                <div class="modal-inner-area">&nbsp;</div>
                                                <div class="modal-content-area">
                                                    <div class="modal-box-area">
                                                        <div class="jobsearch-modal-title-box">
                                                            <h2><?php esc_html_e('Cover Letter', 'wp-jobsearch') ?></h2>
                                                            <span class="modal-close"><i class="fa fa-times"></i></span>
                                                        </div>
                                                        <p><?php echo($job_cver_ltrs[$candidate_id]) ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }, 11, 1);
                                }
                                ?>
                            </ul>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                    if (!empty($job_applicants_list)) {
                        $total_pages = 1;
                        if ($total_records > 0 && $reults_per_page > 0 && $total_records > $reults_per_page) {
                            $total_pages = ceil($total_records / $reults_per_page);
                            ?>
                            <div class="jobsearch-pagination-blog">
                                <?php $Jobsearch_User_Dashboard_Settings->pagination($total_pages, $page_num, $page_url) ?>
                            </div>
                            <?php
                        }
                    }
                    $simp_apps_html = ob_get_clean();
                    echo apply_filters('jobseacrh_empdash_mang_apps_inner_html', $simp_apps_html, $_job_id);
                }
                ?>
            </div>
            <?php
            $apps_html = ob_get_clean();
            return apply_filters('jobseacrh_dash_mange_apps_html', $apps_html, $_job_id);
        }
    }

}

return new JobSearch_EmpDash_ManageJob_Applicants();
