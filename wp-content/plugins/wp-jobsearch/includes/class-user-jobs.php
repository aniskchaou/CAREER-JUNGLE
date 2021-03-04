<?php

class Jobsearch_User_Job_Functions {
    /*
     * Class Construct
     * @return
     */

    public function __construct() {
        //

        add_action('wp', array($this, 'user_job_header'));

        //
        add_action('jobsearch_add_new_package_fields_for_job', array($this, 'add_new_package_fields_for_job'), 10, 2);
        add_action('jobsearch_add_subscribed_package_fields_for_job', array($this, 'add_subscribed_package_fields_for_job'), 10, 2);
        add_action('jobsearch_add_package_fields_for_order', array($this, 'add_package_fields_for_order'), 10, 3);

        add_action('jobsearch_set_job_expiry_and_status', array($this, 'set_job_expiry_and_status'), 10, 1);
        
        add_action('jobsearch_job_update_after_all_fileds', array($this, 'set_job_status_acording_sett'), 15, 2);
        
        //
        add_action('jobsearch_add_job_id_to_order', array($this, 'add_job_id_to_order'), 10, 2);
        //
        add_action('jobsearch_add_featjob_id_to_order', array($this, 'add_featjob_id_to_order'), 10, 2);
        //
        add_action('jobsearch_add_allinjob_id_to_order', array($this, 'add_allinjob_id_to_order'), 10, 2);
        //
        add_action('jobsearch_add_emprofjob_id_to_order', array($this, 'add_emprofjob_id_to_order'), 10, 2);

        //
        add_action('jobsearch_create_new_job_packg_order', array($this, 'create_new_job_packg_order'), 10, 2);
        //
        add_action('jobsearch_create_featured_job_packg_order', array($this, 'create_new_featured_job_packg_order'), 10, 3);

        //
        add_action('wp_ajax_jobsearch_user_dashboard_job_delete', array($this, 'remove_user_job_from_dashboard'));
    }

    /*
     * User job header
     * @return html
     */

    public function user_job_header() {
        global $jobsearch_plugin_options, $sitepress, $job_with_alrdyreg_user, $job_userreg_withmail, $job_form_errs, $package_form_errs;

        $user_id = get_current_user_id();
        $user_id = apply_filters('jobsearch_job_postinhder_top_user_id', $user_id);
        
        $edit_the_joballow = isset($jobsearch_plugin_options['dash_edit_the_job']) ? $jobsearch_plugin_options['dash_edit_the_job'] : '';

        $free_jobs_allow = isset($jobsearch_plugin_options['free-jobs-allow']) ? $jobsearch_plugin_options['free-jobs-allow'] : '';
        $page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $page_id = $user_dashboard_page = jobsearch__get_post_id($page_id, 'page');
        $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);
        
        if ($edit_the_joballow != 'on' && isset($_GET['tab']) && $_GET['tab'] == 'user-job' && isset($_GET['job_id'])) {
            
            $goto_redirect = true;
            if (isset($_GET['step']) && ($_GET['step'] == 'package' || $_GET['step'] == 'confirm')) {
                $goto_redirect = false;
            }
            
            if ($goto_redirect) {
                wp_safe_redirect(add_query_arg(array('tab' => 'user-job'), $page_url));
                exit();
            }
        }
        
        // job post/update actions
        $job_userreg_withmail = false;
        $job_with_alrdyreg_user = false;
        $job_form_errs = $package_form_errs = array();
        
        $this_page_id = get_the_ID();
        if (isset($_POST['user_job_posting']) && $_POST['user_job_posting'] == '1') {

            $_POST = jobsearch_input_post_vals_validate($_POST);
            
            $do_insert_job = $do_update_job = false;

            $user_obj = get_user_by('ID', $user_id);

            if (jobsearch_employer_not_allow_to_mod()) {
                $job_form_errs['post_errors'] = wp_kses(__('<strong>Error!</strong> You are not allowed to add or update any job.', 'wp-jobsearch'), array('strong' => array()));
                return false;
            }
            if (jobsearch_candidate_not_allow_to_mod()) {
                $job_form_errs['post_errors'] = wp_kses(__('<strong>Error!</strong> You are not allowed to add or update any job.', 'wp-jobsearch'), array('strong' => array()));
                return false;
            }

            $is_updating = false;
            $job_id = 0;
            if (isset($_GET['job_id']) && $_GET['job_id'] > 0 && jobsearch_is_employer_job($_GET['job_id'])) {
                $real_job_id = $job_id = $_GET['job_id'];
                $is_updating = true;
            }
            $job_title = isset($_POST['job_title']) ? $_POST['job_title'] : '';
            $job_desc = isset($_POST['job_detail']) ? $_POST['job_detail'] : '';

            //
            $user_is_employer = jobsearch_user_is_employer($user_id);
            if (jobsearch_user_isemp_member($user_id)) {
                $user_is_employer = true;
            }
            $employer_id = '';
            if (is_user_logged_in() && $user_is_employer) {
                if (jobsearch_user_isemp_member($user_id)) {
                    $employer_id = jobsearch_user_isemp_member($user_id);
                } else {
                    $employer_id = jobsearch_get_user_employer_id($user_id);
                }
                if ($employer_id <= 0) {
                    $job_form_errs['post_errors'] = esc_html__('Only an employer can post a job.', 'wp-jobsearch');
                }
            }
            //

            $job_title_max_len = isset($jobsearch_plugin_options['job_title_length']) && $jobsearch_plugin_options['job_title_length'] > 0 ? $jobsearch_plugin_options['job_title_length'] : 1000;
            $job_desc_max_len = isset($jobsearch_plugin_options['job_desc_length']) && $jobsearch_plugin_options['job_desc_length'] > 0 ? $jobsearch_plugin_options['job_desc_length'] : 5000;
            if ($job_title == '') {
                $job_form_errs['post_errors'] = esc_html__('The Title field should not be blank.', 'wp-jobsearch');
            }
            if (strlen($job_title) < 1 || strlen($job_title) > $job_title_max_len) {
                $job_form_errs['post_errors'] = sprintf(esc_html__('Title length should be between 1 to %s characters.', 'wp-jobsearch'), $job_title_max_len);
            }

            if (isset($_POST['job_detail'])) {
                if ($job_desc == '') {
                    $job_form_errs['post_errors'] = esc_html__('The Description field should not be blank.', 'wp-jobsearch');
                }
                if (strlen($job_desc) > $job_desc_max_len) {
                    $job_form_errs['post_errors'] = sprintf(esc_html__('Description length should not be exceeded from %s characters.', 'wp-jobsearch'), $job_desc_max_len);
                }
            }

            if (empty($job_form_errs)) {
                
                if (!is_user_logged_in() && isset($_POST['reg_user_email'])) {
                    $signup_username_allow = isset($jobsearch_plugin_options['signup_username_allow']) ? $jobsearch_plugin_options['signup_username_allow'] : '';
                    $employer_auto_approve = isset($jobsearch_plugin_options['employer_auto_approve']) ? $jobsearch_plugin_options['employer_auto_approve'] : '';
                    $reguser_email = sanitize_text_field($_POST['reg_user_email']);
                    if ($signup_username_allow == 'on') {
                        $reguser_name = sanitize_text_field($_POST['reg_user_uname']);
                    } else {
                        $reguser_name = $reguser_email;
                    }
                    
                    $user_reg_err = false;
                    if ($reguser_email == '' || !filter_var($reguser_email, FILTER_VALIDATE_EMAIL)) {
                        $user_reg_err = true;
                        $job_form_errs['post_errors'] = esc_html__('Please enter the proper user Email Address.', 'wp-jobsearch');
                    }
                    if ($reguser_name == '') {
                        $user_reg_err = true;
                        $job_form_errs['post_errors'] = esc_html__('The username field should not be blank.', 'wp-jobsearch');
                    }
                    
                    if ($user_reg_err === false) {
                        
                        if (email_exists($reguser_email)) {
                            $_user_obj = get_user_by('email', $reguser_email);
                            $employer_id = jobsearch_get_user_employer_id($_user_obj->ID);
                            if ($employer_id > 0) {
                                $user_is_employer = true;
                                $user_id = $_user_obj->ID;
                                $job_with_alrdyreg_user = true;
                            } else {
                                $job_form_errs['post_errors'] = esc_html__('Only an employer can post a job.', 'wp-jobsearch');
                            }
                        } else {
                            $becomin_user_pass = wp_generate_password();
                            $new_reguser = wp_create_user($reguser_name, $becomin_user_pass, $reguser_email);

                            if (is_wp_error($new_reguser)) {
                                $job_form_errs['post_errors'] = $new_reguser->get_error_message();
                            } else {
                                //
                                
                                $user_id = $new_reguser;
                                wp_update_user(array('ID' => $user_id, 'role' => 'jobsearch_employer'));
                                $user_obj = get_user_by('ID', $user_id);

                                if ($employer_auto_approve == 'email' || $employer_auto_approve == 'admin_email') {
                                    $job_userreg_withmail = true;
                                    $uverify_code = wp_generate_password(20, false);
                                    update_user_meta($user_id, 'jobsearch_accaprov_key', $uverify_code);
                                    update_user_meta($user_id, 'jobsearch_accaprov_allow', '0');
                                    do_action('jobsearch_new_employer_approval', $user_obj, $becomin_user_pass);
                            
                                    $becomin_user_pass = base64_encode($becomin_user_pass);
                                    update_user_meta($user_id, 'jobsearch_new_user_regtpass', $becomin_user_pass);
                                } else {
                                    do_action('jobsearch_new_user_register', $user_obj, $becomin_user_pass);
                                    wp_set_current_user($user_id);
                                    wp_set_auth_cookie($user_id, true);
                                }

                                $user_is_employer = jobsearch_user_is_employer($user_id);
                                if (jobsearch_user_isemp_member($user_id)) {
                                    $user_is_employer = true;
                                }

                                if (jobsearch_user_isemp_member($user_id)) {
                                    $employer_id = jobsearch_user_isemp_member($user_id);
                                } else {
                                    $employer_id = jobsearch_get_user_employer_id($user_id);
                                }
                                if (is_user_logged_in() && $user_is_employer) {
                                    if ($employer_id <= 0) {
                                        $job_form_errs['post_errors'] = esc_html__('Only an employer can post a job.', 'wp-jobsearch');
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            if (empty($job_form_errs)) {

                if ($job_id > 0) {

                    if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                        $current_lang = $sitepress->get_current_language();
                        $job_id = icl_object_id($job_id, 'job', true, $current_lang);
                    }

                    $up_post = array(
                        'ID' => $job_id,
                        'post_title' => ($job_title),
                        'post_content' => $job_desc,
                    );
                    wp_update_post($up_post);

                    $do_update_job = true;
                } else {
                    $job_def_status = 'awaiting-payment';
                    if ($free_jobs_allow == 'on') {
                        $job_def_status = 'publish';
                    }
                    $ins_post = array(
                        'post_type' => 'job',
                        'post_status' => $job_def_status,
                        'post_title' => wp_strip_all_tags($job_title),
                        'post_content' => $job_desc,
                    );
                    $job_id = wp_insert_post($ins_post);

                    update_post_meta($job_id, 'jobsearch_field_job_featured', '');

                    if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                        $lang_code = $sitepress->get_current_language();
                        $lang_code = apply_filters('jobsearch_set_post_insert_lang_code', $lang_code);
                        $sitepress->set_element_language_details($job_id, 'post_job', false, $lang_code);
                    }
                    $do_insert_job = true;
                }

                // update job employer
                update_post_meta($job_id, 'jobsearch_field_job_posted_by', $employer_id);
                
                update_post_meta($job_id, 'jobsearch_field_job_filled', '');

                // Employer jobs status change according his/her status
                do_action('jobsearch_employer_update_jobs_status', $employer_id);

                $job_expired = true;
                if (!$is_updating) {
                    // job insert time
                    update_post_meta($job_id, 'jobsearch_field_job_publish_date', strtotime(current_time('d-m-Y H:i:s')));
                } else {
                    $job_expiry_date = get_post_meta($job_id, 'jobsearch_field_job_expiry_date', true);
                    if ($job_expiry_date != '' && $job_expiry_date > strtotime(current_time('d-m-Y H:i:s'))) {
                        $job_expired = false;
                    } else {
                        //
                        $c_user = wp_get_current_user();
                        do_action('jobsearch_job_expire_to_employer', $c_user, $job_id);
                    }
                    if (isset($_POST['republishin_job']) && $_POST['republishin_job'] == '1') {
                        update_post_meta($job_id, 'jobsearch_field_job_publish_date', current_time('timestamp', 0));
                        $up_post = array(
                            'ID' => $job_id,
                            'post_date' => date('Y-m-d H:i:s', current_time('timestamp', 0)),
                            'post_date_gmt' => date('Y-m-d H:i:s', current_time('timestamp', 1)),
                        );
                        wp_update_post($up_post);
                    }
                }

                // job skills
                $job_skills_switch = isset($jobsearch_plugin_options['job-skill-switch']) ? $jobsearch_plugin_options['job-skill-switch'] : '';
                if ($job_skills_switch == 'on') {
                    $job_max_skills_allow = isset($jobsearch_plugin_options['job_max_skills']) && $jobsearch_plugin_options['job_max_skills'] > 0 ? $jobsearch_plugin_options['job_max_skills'] : 5;
                    $tags_limit = $job_max_skills_allow;
                    $job_skills = isset($_POST['get_job_skills']) && !empty($_POST['get_job_skills']) ? $_POST['get_job_skills'] : array();
                    if (absint($tags_limit) > 0 && !empty($job_skills) && count($job_skills) > $tags_limit) {
                        $job_skills = array_slice($job_skills, 0, $tags_limit, true);
                    }
                    wp_set_post_terms($job_id, $job_skills, 'skill', FALSE);
                    update_post_meta($job_id, 'jobsearch_job_skills', $job_skills);
                }

                //
                if (isset($_POST['job_sector'])) {
                    $job_sector = ($_POST['job_sector']);
                    $job_sector = is_array($job_sector) ? $job_sector : array($job_sector);
                    wp_set_post_terms($job_id, $job_sector, 'sector', false);
                }
                // job filled
                if (isset($_POST['job_filled'])) {
                    $job_filled = sanitize_text_field($_POST['job_filled']);
                    update_post_meta($job_id, 'jobsearch_field_job_filled', $job_filled);
                }

                // job apply type
                if (isset($_POST['job_apply_type'])) {
                    $job_apply_type = sanitize_text_field($_POST['job_apply_type']);
                    update_post_meta($job_id, 'jobsearch_field_job_apply_type', $job_apply_type);
                }
                if (isset($_POST['job_apply_url'])) {
                    $job_apply_url = sanitize_text_field($_POST['job_apply_url']);
                    update_post_meta($job_id, 'jobsearch_field_job_apply_url', $job_apply_url);
                }
                if (isset($_POST['job_apply_email'])) {
                    $job_apply_email = sanitize_text_field($_POST['job_apply_email']);
                    update_post_meta($job_id, 'jobsearch_field_job_apply_email', $job_apply_email);
                }

                // job min salary
                if (isset($_POST['job_salary'])) {
                    $job_salary = sanitize_text_field($_POST['job_salary']);
                    update_post_meta($job_id, 'jobsearch_field_job_salary', $job_salary);
                }
                // job max salary
                if (isset($_POST['job_max_salary'])) {
                    $job_max_salary = sanitize_text_field($_POST['job_max_salary']);
                    update_post_meta($job_id, 'jobsearch_field_job_max_salary', $job_max_salary);
                }
                // job salary type
                if (isset($_POST['job_salary_type'])) {
                    $job_salary_type = sanitize_text_field($_POST['job_salary_type']);
                    update_post_meta($job_id, 'jobsearch_field_job_salary_type', $job_salary_type);
                }
                // job salary currency
                if (isset($_POST['job_salary_currency'])) {
                    $job_salary_type = ($_POST['job_salary_currency']);
                    update_post_meta($job_id, 'jobsearch_field_job_salary_currency', $job_salary_type);
                }
                // job salary currency pos
                if (isset($_POST['job_salary_pos'])) {
                    $job_salary_type = sanitize_text_field($_POST['job_salary_pos']);
                    update_post_meta($job_id, 'jobsearch_field_job_salary_pos', $job_salary_type);
                }
                // job salary currency decimal
                if (isset($_POST['job_salary_deci'])) {
                    $job_salary_type = sanitize_text_field($_POST['job_salary_deci']);
                    update_post_meta($job_id, 'jobsearch_field_job_salary_deci', $job_salary_type);
                }
                // job salary currency sep
                if (isset($_POST['job_salary_sep'])) {
                    $job_salary_type = sanitize_text_field($_POST['job_salary_sep']);
                    update_post_meta($job_id, 'jobsearch_field_job_salary_sep', $job_salary_type);
                }

                // application deadline
                if (isset($_POST['application_deadline']) && $_POST['application_deadline'] != '') {
                    $application_deadline = sanitize_text_field($_POST['application_deadline']);
                    update_post_meta($job_id, 'jobsearch_field_job_application_deadline_date', strtotime($application_deadline));
                }

                // Cus Fields Upload Files /////
                do_action('jobsearch_custom_field_upload_files_save', $job_id, 'job');
                //
                // Attachments ////////////////////////
                $gal_ids_arr = array();

                $max_gal_imgs_allow = isset($jobsearch_plugin_options['number_of_attachments']) && $jobsearch_plugin_options['number_of_attachments'] > 0 ? $jobsearch_plugin_options['number_of_attachments'] : 5;

                if (isset($_POST['jobsearch_field_job_attachment_files']) && !empty($_POST['jobsearch_field_job_attachment_files'])) {
                    $gal_ids_arr = array_merge($gal_ids_arr, $_POST['jobsearch_field_job_attachment_files']);
                }

                $gal_imgs_count = 0;
                if (!empty($gal_ids_arr)) {
                    $gal_imgs_count = sizeof($gal_ids_arr);
                }

                $gall_ids = jobsearch_attachments_upload('job_attach_files', $gal_imgs_count);
                if (!empty($gall_ids)) {
                    $gal_ids_arr = array_merge($gal_ids_arr, $gall_ids);
                }
                if (!empty($gal_ids_arr) && $max_gal_imgs_allow > 0) {
                    $gal_ids_arr = array_slice($gal_ids_arr, 0, $max_gal_imgs_allow, true);
                }
                update_post_meta($job_id, 'jobsearch_field_job_attachment_files', $gal_ids_arr);
                //

                if (isset($_POST['location_location2'])) {
                    $jobsearch_field_state = sanitize_text_field($_POST['location_location2']);
                    update_post_meta($job_id, 'jobsearch_field_location_location2', $jobsearch_field_state);
                }
                if (isset($_POST['location_location3'])) {
                    $jobsearch_field_city = sanitize_text_field($_POST['location_location3']);
                    update_post_meta($job_id, 'jobsearch_field_location_location3', $jobsearch_field_city);
                }

                if (isset($_POST['job_type'])) {
                    $job_type = ($_POST['job_type']);
                    $job_type_tosave = is_array($job_type) ? $job_type : array($job_type);
                    wp_set_post_terms($job_id, $job_type_tosave, 'jobtype', false);
                }

                // after saving all fields
                do_action('jobsearch_job_dash_save_after', $job_id);

                if (!$is_updating && $free_jobs_allow == 'on') {
                    do_action('jobsearch_set_job_expiry_and_status', $job_id);
                }

                //
                if ($do_insert_job === true) {
                    $users_query = new WP_User_Query(array(
                        'role' => 'administrator',
                        'orderby' => 'display_name'
                    ));
                    $users_result = $users_query->get_results();
                    $adm_user_obj = isset($users_result[0]) ? $users_result[0] : array();
                    do_action('jobsearch_job_submitted_admin', $adm_user_obj, $job_id);
                    
                    do_action('jobsearch_job_postin_dashf_after_create_new', $job_id);
                }

                //
                if ($do_update_job === true) {
                    $c_user = wp_get_current_user();
                    do_action('jobsearch_job_update_to_employer', $c_user, $job_id);
                    
                    //
                    do_action('jobsearch_job_update_after_all_fileds', $job_id, $is_updating);
                }
                
                if ($free_jobs_allow != 'on') {
                    if (!is_user_logged_in() && $job_with_alrdyreg_user === true && $user_is_employer) {
                        $this_page_url = get_permalink($this_page_id);
                        $redirect_url = add_query_arg(array('job_id' => $job_id, 'step' => 'confirm_user_job'), $this_page_url);
                        wp_safe_redirect($redirect_url);
                        exit();
                    }
                    if (!is_user_logged_in() && $job_userreg_withmail === true && $user_is_employer) {
                        $this_page_url = get_permalink($this_page_id);
                        $redirect_url = add_query_arg(array('job_id' => $job_id, 'step' => 'confirm_detail'), $this_page_url);
                        wp_safe_redirect($redirect_url);
                        exit();
                    }
                    if (is_user_logged_in() && $user_is_employer && !$is_updating) {
                        $redirct_step = apply_filters('jobsearch_jobdsave_redirct_step_newjob', 'package');
                        $redirect_url = add_query_arg(array('tab' => 'user-job', 'job_id' => $job_id, 'step' => $redirct_step, 'action' => 'update'), $page_url);
                        wp_safe_redirect($redirect_url);
                        exit();
                    }
                    if (is_user_logged_in() && $user_is_employer && $is_updating && $job_expired) {
                        $redirct_step = apply_filters('jobsearch_jobdsave_redirct_step_expirjob', 'package');
                        $redirect_url = add_query_arg(array('tab' => 'user-job', 'job_id' => $job_id, 'step' => $redirct_step, 'action' => 'update'), $page_url);
                        wp_safe_redirect($redirect_url);
                        exit();
                    }
                    if (is_user_logged_in() && $user_is_employer && $is_updating && !$job_expired) {
                        $redirct_step = apply_filters('jobsearch_jobdsave_redirct_step_aprovdjob', 'confirm');
                        $redirect_url = add_query_arg(array('tab' => 'user-job', 'job_id' => $job_id, 'step' => $redirct_step, 'action' => 'update'), $page_url);
                        wp_safe_redirect($redirect_url);
                        exit();
                    }
                } else {
                    if (!is_user_logged_in() && $job_with_alrdyreg_user === true && $user_is_employer) {
                        $this_page_url = get_permalink($this_page_id);
                        $redirect_url = add_query_arg(array('job_id' => $job_id, 'step' => 'confirm_user_job'), $this_page_url);
                        wp_safe_redirect($redirect_url);
                        exit();
                    }
                    if (!is_user_logged_in() && $job_userreg_withmail === true && $user_is_employer) {
                        $this_page_url = get_permalink($this_page_id);
                        $redirect_url = add_query_arg(array('job_id' => $job_id, 'step' => 'confirm_detail'), $this_page_url);
                        wp_safe_redirect($redirect_url);
                        exit();
                    }
                    if ($user_is_employer) {
                        $redirct_step = apply_filters('jobsearch_jobdsave_redirct_step_toconfrm', 'confirm');
                        $redirect_url = add_query_arg(array('tab' => 'user-job', 'job_id' => $job_id, 'step' => $redirct_step, 'action' => 'update'), $page_url);
                        wp_safe_redirect($redirect_url);
                        exit();
                    }
                }
            }
        }
        //

        if (isset($_POST['user_job_package_chose']) && $_POST['user_job_package_chose'] == '1') {
            $is_updating = false;
            $job_id = 0;
            if (isset($_GET['job_id']) && $_GET['job_id'] > 0 && jobsearch_is_employer_job($_GET['job_id'])) {
                $job_id = $_GET['job_id'];
                $is_updating = true;
            }

            $go_to_confirm = false;

            //
            if (isset($_POST['job_subs_package'])) {
                if (isset($_POST['make_job_feature_alredy'])) {
                    $make_job_feature = $_POST['make_job_feature_alredy'];
                    update_post_meta($job_id, 'make_it_to_feature', $make_job_feature);
                }
            } else {
                if (isset($_POST['make_job_feature'])) {
                    $make_job_feature = $_POST['make_job_feature'];
                    update_post_meta($job_id, 'make_it_to_feature', $make_job_feature);
                }
            }
            
            if (isset($_POST['job_subs_package'])) {
                // For Subscribed Package actions
                $package_order_id = $_POST['job_subs_package'];
                $pkg_type = get_post_meta($package_order_id, 'package_type', true);
                $subs_pkge_exp = jobsearch_pckg_order_is_expired($package_order_id);
                if ($pkg_type == 'featured_jobs') {
                    $subs_pkge_exp = jobsearch_fjobs_pckg_order_is_expired($package_order_id);
                } else if ($pkg_type == 'emp_allin_one') {
                    $subs_pkge_exp = jobsearch_allinpckg_order_is_expired($package_order_id, 'jobs');
                } else if ($pkg_type == 'employer_profile') {
                    $subs_pkge_exp = jobsearch_emprofpckg_order_is_expired($package_order_id, 'jobs');
                }
                if ($is_updating && empty($package_form_errs) && ($pkg_type == 'job' || $pkg_type == 'featured_jobs' || $pkg_type == 'emp_allin_one' || $pkg_type == 'employer_profile') && $subs_pkge_exp === false) {
                    // Saving Package Fields and Values in Job
                    do_action('jobsearch_add_subscribed_package_fields_for_job', $package_order_id, $job_id);
                    do_action('jobsearch_add_job_id_to_order', $job_id, $package_order_id);
                    do_action('jobsearch_add_featjob_id_to_order', $job_id, $package_order_id);
                    do_action('jobsearch_add_allinjob_id_to_order', $job_id, $package_order_id);
                    do_action('jobsearch_add_emprofjob_id_to_order', $job_id, $package_order_id);
                    do_action('jobsearch_set_job_expiry_and_status', $job_id, $package_order_id);

                    // if feature pckg too selected
                    if (isset($_POST['job_package_featured']) && $_POST['job_package_featured'] != '') {
                        $package_id = $_POST['job_package_featured'];
                        $pkg_charges_type = get_post_meta($package_id, 'jobsearch_field_charges_type', true);
                        $pkg_attach_product = get_post_meta($package_id, 'jobsearch_package_product', true);
                        if (!class_exists('WooCommerce')) {
                            $package_form_errs[] = esc_html__('WooCommerce Plugin not exist.', 'wp-jobsearch');
                        }
                        if ($pkg_charges_type == 'paid') {
                            $package_product_obj = $pkg_attach_product != '' ? get_page_by_path($pkg_attach_product, 'OBJECT', 'product') : '';

                            if ($pkg_attach_product != '' && is_object($package_product_obj)) {
                                $product_id = $package_product_obj->ID;
                            } else {
                                $package_form_errs[] = esc_html__('Selected Package Product not found.', 'wp-jobsearch');
                            }
                            if (empty($package_form_errs)) {
                                // add to cart and checkout
                                do_action('jobsearch_woocommerce_payment_checkout', $package_id, 'redirect', $job_id);
                            }
                        }
                    }
                    //
                }
                //
                $go_to_confirm = true;
                $conf_args = array(
                    'is_updating' => $is_updating,
                    'package_order_id' => $package_order_id,
                    'job_id' => $job_id,
                );
                $go_to_confirm = apply_filters('jobsearch_set_subs_pkg_goto_confirm', $go_to_confirm, $conf_args);
                //
            }

            if (isset($_POST['job_package_featured'])) {
                $package_id = $_POST['job_package_featured'];
                $pkg_charges_type = get_post_meta($package_id, 'jobsearch_field_charges_type', true);
                $pkg_attach_product = get_post_meta($package_id, 'jobsearch_package_product', true);
                if (!class_exists('WooCommerce')) {
                    $package_form_errs[] = esc_html__('WooCommerce Plugin not exist.', 'wp-jobsearch');
                }

                if ($pkg_charges_type == 'paid') {
                    $package_product_obj = $pkg_attach_product != '' ? get_page_by_path($pkg_attach_product, 'OBJECT', 'product') : '';

                    if ($pkg_attach_product != '' && is_object($package_product_obj)) {
                        $product_id = $package_product_obj->ID;
                    } else {
                        $package_form_errs[] = esc_html__('Selected Package Product not found.', 'wp-jobsearch');
                    }

                    if ($is_updating && empty($package_form_errs)) {
                        // add to cart and checkout
                        if (isset($_POST['job_package_new']) && $_POST['job_package_new'] != '') {
                            do_action('jobsearch_woocommerce_payment_checkout', $package_id, 'no_where', $job_id);
                        } else {
                            do_action('jobsearch_woocommerce_payment_checkout', $package_id, 'redirect', $job_id);
                        }
                    }
                } else {
                    if ($is_updating && empty($package_form_errs)) {
                        // update job status
                        $up_post = array(
                            'ID' => $job_id,
                            'post_status' => 'publish',
                        );
                        wp_update_post($up_post);
                        // creating order and adding product to order
                        do_action('jobsearch_create_new_job_packg_order', $package_id, $job_id);
                        $go_to_confirm = true;
                    }
                }
            }

            if (isset($_POST['job_package_new'])) {

                $package_id = isset($_POST['job_package_new']) ? $_POST['job_package_new'] : '';

                $pkg__type = get_post_meta($package_id, 'jobsearch_field_package_type', true);
                $pkg_subs_check = jobsearch_pckg_is_subscribed($package_id, $user_id);
                if ($pkg__type == 'featured_jobs') {
                    $pkg_subs_check = jobsearch_fjobs_pckg_is_subscribed($package_id, $user_id);
                } else if ($pkg__type == 'emp_allin_one') {
                    $pkg_subs_check = jobsearch_allinpckg_is_subscribed($package_id, $user_id);
                } else if ($pkg__type == 'employer_profile') {
                    $pkg_subs_check = jobsearch_emprofpckg_is_subscribed($package_id, $user_id);
                }

                if ($pkg_subs_check) {
                    $package_form_errs[] = sprintf(esc_html__('Selected Package "%s" is already subscribed.', 'wp-jobsearch'), get_the_title($package_id));
                }

                $pkg_charges_type = get_post_meta($package_id, 'jobsearch_field_charges_type', true);
                $pkg_attach_product = get_post_meta($package_id, 'jobsearch_package_product', true);

                // For Paid Package actions
                if ($pkg_charges_type == 'paid') {
                    if (!class_exists('WooCommerce')) {
                        $package_form_errs[] = esc_html__('WooCommerce Plugin not exist.', 'wp-jobsearch');
                    }

                    $package_product_obj = $pkg_attach_product != '' ? get_page_by_path($pkg_attach_product, 'OBJECT', 'product') : '';

                    if ($pkg_attach_product != '' && is_object($package_product_obj)) {
                        $product_id = $package_product_obj->ID;
                    } else {
                        $package_form_errs[] = esc_html__('Selected Package Product not found.', 'wp-jobsearch');
                    }

                    if ($is_updating && empty($package_form_errs)) {
                        //
                        $checkout_process = true;
                        $checkout_process = apply_filters('jobsearch_new_job_post_before_checkout', $checkout_process, $package_id, $job_id);

                        // add to cart and checkout
                        if ($checkout_process === true) {
                            if (isset($_POST['job_package_featured']) && $_POST['job_package_featured'] != '') {
                                do_action('jobsearch_woocommerce_payment_checkout', $package_id, 'redirect', $job_id, false);
                            } else {
                                do_action('jobsearch_woocommerce_payment_checkout', $package_id, 'redirect', $job_id);
                            }
                        }
                    }
                } else {
                    // For Free Package actions
                    if (!class_exists('WooCommerce')) {
                        $package_form_errs[] = esc_html__('WooCommerce Plugin not exist.', 'wp-jobsearch');
                    }
                    if ($is_updating && empty($package_form_errs)) {
                        // update job status
                        $up_post = array(
                            'ID' => $job_id,
                            'post_status' => 'publish',
                        );
                        wp_update_post($up_post);
                        // creating order and adding product to order
                        if (isset($_POST['job_package_featured']) && $_POST['job_package_featured'] != '') {
                            do_action('jobsearch_create_new_job_packg_order', $package_id, $job_id);
                            do_action('jobsearch_woocommerce_payment_checkout', $package_id, 'redirect', $job_id, false);
                        } else {
                            do_action('jobsearch_create_new_job_packg_order', $package_id, $job_id);
                            $go_to_confirm = true;
                        }
                    }
                }
            }
            //
            if ($go_to_confirm && empty($package_form_errs)) {
                $redirect_url = add_query_arg(array('tab' => 'user-job', 'job_id' => $job_id, 'step' => 'confirm', 'action' => 'update'), $page_url);
                wp_safe_redirect($redirect_url);
                exit();
            }
            //
        }
    }
    
    public function set_job_status_acording_sett($job_id, $is_updating = false) {
        global $jobsearch_plugin_options;

        if ($is_updating) {
            $def_status_toset = isset($jobsearch_plugin_options['job-default-status']) ? $jobsearch_plugin_options['job-default-status'] : '';
            $update_status_toset = isset($jobsearch_plugin_options['job-onupdate-status']) ? $jobsearch_plugin_options['job-onupdate-status'] : '';
            $job_status = get_post_meta($job_id, 'jobsearch_field_job_status', true);
            if ($job_status == 'approved' && $def_status_toset == 'admin-review' && $update_status_toset == 'admin-review') {
                update_post_meta($job_id, 'jobsearch_field_job_status', 'admin-review');
            }
        }
    }

    public function add_new_package_fields_for_job($package_id, $job_id) {

        $pkg_type = get_post_meta($package_id, 'jobsearch_field_package_type', true);
        $job_package_fields = apply_filters('jobsearch_get_job_package_fields_list', array());
        if ($pkg_type == 'featured_jobs') {
            $job_package_fields = apply_filters('jobsearch_get_featured_jobs_package_fields_list', array());
        }
        if ($pkg_type == 'emp_allin_one') {
            $job_package_fields = apply_filters('jobsearch_get_all_in_one_package_fields_list', array());
        }
        if ($pkg_type == 'employer_profile') {
            $job_package_fields = apply_filters('jobsearch_get_emp_profpkg_cfields_list', array());
        }
        $job_package_fields = apply_filters('jobsearch_set_package_fields_ch_list', $job_package_fields, $pkg_type);

        $is_unlimited_pkg = get_post_meta($package_id, 'jobsearch_field_unlimited_pkg', true);
        
        $packge_fields_arr = array(
            'package_name' => get_the_title($package_id),
            'package_charges_type' => get_post_meta($package_id, 'jobsearch_field_charges_type', true),
            'package_type' => get_post_meta($package_id, 'jobsearch_field_package_type', true),
            'package_price' => get_post_meta($package_id, 'jobsearch_field_package_price', true),
        );
        if ($packge_fields_arr['package_charges_type'] == 'free') {
            $packge_fields_arr['package_price'] = 0;
        }
        if (!empty($job_package_fields)) {
            foreach ($job_package_fields as $job_package_field) {
                $value = get_post_meta($package_id, 'jobsearch_field_' . $job_package_field, true);
                $packge_fields_arr[$job_package_field] = $value;
            }
            //
            if ($is_unlimited_pkg == 'on') {
                $packge_fields_arr['package_expiry_time'] = '10';
                $packge_fields_arr['package_expiry_time_unit'] = 'years';
            }
            if ($pkg_type == 'emp_allin_one') {
                $is_unlimited_jobs = get_post_meta($package_id, 'jobsearch_field_unlim_allinjobs', true);
                $is_unlimited_fjobs = get_post_meta($package_id, 'jobsearch_field_unlim_allinfjobs', true);
                $is_unlimited_jobexp = get_post_meta($package_id, 'jobsearch_field_unlim_allinjobexp', true);
                $is_unlimited_cvs = get_post_meta($package_id, 'jobsearch_field_unlim_allinnumcvs', true);

                if ($is_unlimited_jobexp == 'on') {
                    $packge_fields_arr['allinjob_expiry_time'] = '10';
                    $packge_fields_arr['allinjob_expiry_time_unit'] = 'years';
                }
                if ($is_unlimited_jobs == 'on') {
                    $packge_fields_arr['allin_num_jobs'] = '1000000';
                }
                if ($is_unlimited_fjobs == 'on') {
                    $packge_fields_arr['allin_num_fjobs'] = '1000000';
                }
                if ($is_unlimited_cvs == 'on') {
                    $packge_fields_arr['allin_num_cvs'] = '1000000';
                }
            } else if ($pkg_type == 'employer_profile') {
                $is_unlimited_jobs = get_post_meta($package_id, 'jobsearch_field_unlim_emprofjobs', true);
                $is_unlimited_fjobs = get_post_meta($package_id, 'jobsearch_field_unlim_emproffjobs', true);
                $is_unlimited_jobexp = get_post_meta($package_id, 'jobsearch_field_unlim_emprofjobexp', true);
                $is_unlimited_cvs = get_post_meta($package_id, 'jobsearch_field_unlim_emprofnumcvs', true);

                if ($is_unlimited_jobexp == 'on') {
                    $packge_fields_arr['emprofjob_expiry_time'] = '10';
                    $packge_fields_arr['emprofjob_expiry_time_unit'] = 'years';
                }
                if ($is_unlimited_jobs == 'on') {
                    $packge_fields_arr['emprof_num_jobs'] = '1000000';
                }
                if ($is_unlimited_fjobs == 'on') {
                    $packge_fields_arr['emprof_num_fjobs'] = '1000000';
                }
                if ($is_unlimited_cvs == 'on') {
                    $packge_fields_arr['emprof_num_cvs'] = '1000000';
                }
            } else if ($pkg_type == 'featured_jobs') {
                $is_unlimited_numfjobs = get_post_meta($package_id, 'jobsearch_field_unlimited_numfjobs', true);
                $is_unlimited_fjobscr = get_post_meta($package_id, 'jobsearch_field_unlimited_fjobscr', true);
                $is_unlimited_fjobexp = get_post_meta($package_id, 'jobsearch_field_unlimited_fjobexp', true);

                if ($is_unlimited_fjobexp == 'on') {
                    $packge_fields_arr['fjob_expiry_time'] = '10';
                    $packge_fields_arr['fjob_expiry_time_unit'] = 'years';
                }
                if ($is_unlimited_numfjobs == 'on') {
                    $packge_fields_arr['num_of_fjobs'] = '1000000';
                }
                if ($is_unlimited_fjobscr == 'on') {
                    $packge_fields_arr['feat_job_credits'] = '1000000';
                }
            } else {
                $is_unlimited_numjobs = get_post_meta($package_id, 'jobsearch_field_unlimited_numjobs', true);
                $is_unlimited_jobsexp = get_post_meta($package_id, 'jobsearch_field_unlimited_jobsexp', true);
                if ($is_unlimited_jobsexp == 'on') {
                    $packge_fields_arr['job_expiry_time'] = '10';
                    $packge_fields_arr['job_expiry_time_unit'] = 'years';
                }
                if ($is_unlimited_numjobs == 'on') {
                    $packge_fields_arr['num_of_jobs'] = '1000000';
                }
            }
        }

        $job_packages_arr = get_post_meta($job_id, 'attach_packages_array', true);
        if (empty($job_packages_arr)) {
            $job_packages_arr = array($packge_fields_arr);
            update_post_meta($job_id, 'attach_packages_array', $job_packages_arr);
        } else {
            $job_packages_arr[] = $packge_fields_arr;
            update_post_meta($job_id, 'attach_packages_array', $job_packages_arr);
        }
    }

    public function add_subscribed_package_fields_for_job($order_id, $job_id) {

        $pkg_type = get_post_meta($order_id, 'package_type', true);
        $job_package_fields = apply_filters('jobsearch_get_job_package_fields_list', array());

        if ($pkg_type == 'emp_allin_one') {
            $job_package_fields = apply_filters('jobsearch_get_all_in_one_package_fields_list', array());
        }
        if ($pkg_type == 'employer_profile') {
            $job_package_fields = apply_filters('jobsearch_get_emp_profpkg_cfields_list', array());
        }
        if ($pkg_type == 'featured_jobs') {
            $job_package_fields = apply_filters('jobsearch_get_featured_jobs_package_fields_list', array());
        }
        $job_package_fields = apply_filters('jobsearch_set_package_fields_ch_list', $job_package_fields, $pkg_type);
        
        $is_unlimited_pkg = get_post_meta($order_id, 'unlimited_pkg', true);

        $packge_fields_arr = array(
            'package_name' => get_post_meta($order_id, 'package_name', true),
            'package_type' => get_post_meta($order_id, 'package_type', true),
            'package_price' => get_post_meta($order_id, 'package_price', true),
        );
        if (!empty($job_package_fields)) {
            foreach ($job_package_fields as $job_package_field) {
                $value = get_post_meta($order_id, $job_package_field, true);
                $packge_fields_arr[$job_package_field] = $value;
            }
            //
            if ($is_unlimited_pkg == 'yes') {
                $packge_fields_arr['package_expiry_time'] = '10';
                $packge_fields_arr['package_expiry_time_unit'] = 'years';
            }
            if ($pkg_type == 'emp_allin_one') {
                $is_unlimited_jobs = get_post_meta($order_id, 'unlim_allinjobs', true);
                $is_unlimited_fjobs = get_post_meta($order_id, 'unlim_allinfjobs', true);
                $is_unlimited_jobexp = get_post_meta($order_id, 'unlim_allinjobexp', true);
                $is_unlimited_cvs = get_post_meta($order_id, 'unlim_allinnumcvs', true);

                if ($is_unlimited_jobexp == 'yes') {
                    $packge_fields_arr['allinjob_expiry_time'] = '10';
                    $packge_fields_arr['allinjob_expiry_time_unit'] = 'years';
                }
                if ($is_unlimited_jobs == 'yes') {
                    $packge_fields_arr['allin_num_jobs'] = '1000000';
                }
                if ($is_unlimited_fjobs == 'yes') {
                    $packge_fields_arr['allin_num_fjobs'] = '1000000';
                }
                if ($is_unlimited_cvs == 'yes') {
                    $packge_fields_arr['allin_num_cvs'] = '1000000';
                }
            } else if ($pkg_type == 'employer_profile') {
                $is_unlimited_jobs = get_post_meta($order_id, 'unlim_emprofjobs', true);
                $is_unlimited_fjobs = get_post_meta($order_id, 'unlim_emproffjobs', true);
                $is_unlimited_jobexp = get_post_meta($order_id, 'unlim_emprofjobexp', true);
                $is_unlimited_cvs = get_post_meta($order_id, 'unlim_emprofnumcvs', true);

                if ($is_unlimited_jobexp == 'yes') {
                    $packge_fields_arr['emprofjob_expiry_time'] = '10';
                    $packge_fields_arr['emprofjob_expiry_time_unit'] = 'years';
                }
                if ($is_unlimited_jobs == 'yes') {
                    $packge_fields_arr['emprof_num_jobs'] = '1000000';
                }
                if ($is_unlimited_fjobs == 'yes') {
                    $packge_fields_arr['emprof_num_fjobs'] = '1000000';
                }
                if ($is_unlimited_cvs == 'yes') {
                    $packge_fields_arr['emprof_num_cvs'] = '1000000';
                }
            } else if ($pkg_type == 'featured_jobs') {
                $is_unlimited_numfjobs = get_post_meta($order_id, 'unlimited_numfjobs', true);
                $is_unlimited_fjobscr = get_post_meta($order_id, 'unlimited_fjobscr', true);
                $is_unlimited_fjobexp = get_post_meta($order_id, 'unlimited_fjobexp', true);

                if ($is_unlimited_fjobexp == 'yes') {
                    $packge_fields_arr['fjob_expiry_time'] = '10';
                    $packge_fields_arr['fjob_expiry_time_unit'] = 'years';
                }
                if ($is_unlimited_numfjobs == 'yes') {
                    $packge_fields_arr['num_of_fjobs'] = '1000000';
                }
                if ($is_unlimited_fjobscr == 'yes') {
                    $packge_fields_arr['feat_job_credits'] = '1000000';
                }
            } else {
                $is_unlimited_numjobs = get_post_meta($order_id, 'unlimited_numjobs', true);
                $is_unlimited_jobsexp = get_post_meta($order_id, 'unlimited_jobsexp', true);
                if ($is_unlimited_jobsexp == 'yes') {
                    $packge_fields_arr['job_expiry_time'] = '10';
                    $packge_fields_arr['job_expiry_time_unit'] = 'years';
                }
                if ($is_unlimited_numjobs == 'yes') {
                    $packge_fields_arr['num_of_jobs'] = '1000000';
                }
            }
        }

        $job_packages_arr = get_post_meta($job_id, 'attach_packages_array', true);
        if (empty($job_packages_arr)) {
            $job_packages_arr = array($packge_fields_arr);
            update_post_meta($job_id, 'attach_packages_array', $job_packages_arr);
        } else {
            $job_packages_arr[] = $packge_fields_arr;
            update_post_meta($job_id, 'attach_packages_array', $job_packages_arr);
        }
    }

    public function add_package_fields_for_order($package_id, $order_id, $pkg_type = 'job') {
        if ($pkg_type == 'cv') {
            $_package_fields = apply_filters('jobsearch_get_cv_package_fields_list', array());
        } else if ($pkg_type == 'candidate') {
            $_package_fields = apply_filters('jobsearch_get_candidate_package_fields_list', array());
        } else if ($pkg_type == 'feature_job') {
            $_package_fields = apply_filters('jobsearch_get_feature_job_package_fields_list', array());
        } else if ($pkg_type == 'featured_jobs') {
            $_package_fields = apply_filters('jobsearch_get_featured_jobs_package_fields_list', array());
        } else if ($pkg_type == 'emp_allin_one') {
            $_package_fields = apply_filters('jobsearch_get_all_in_one_package_fields_list', array());
        } else if ($pkg_type == 'promote_profile') {
            $_package_fields = apply_filters('jobsearch_get_promote_profile_package_fields_list', array());
        } else if ($pkg_type == 'urgent_pkg') {
            $_package_fields = apply_filters('jobsearch_get_urgent_pkg_package_fields_list', array());
        } else if ($pkg_type == 'candidate_profile') {
            $_package_fields = apply_filters('jobsearch_get_cand_profpkg_cfields_list', array());
        } else if ($pkg_type == 'employer_profile') {
            $_package_fields = apply_filters('jobsearch_get_emp_profpkg_cfields_list', array());
        } else {
            $_package_fields = apply_filters('jobsearch_get_job_package_fields_list', array());
        }

        $_package_fields = apply_filters('jobsearch_set_package_fields_ch_list', $_package_fields, $pkg_type);

        $order_user_id = get_post_meta($order_id, 'jobsearch_order_user', true);
        
        //
        $act_pkg_type = get_post_meta($package_id, 'jobsearch_field_package_type', true);
        $is_unlimited_pkg = get_post_meta($package_id, 'jobsearch_field_unlimited_pkg', true);

        $packge_fields_arr = array(
            'package_name' => get_the_title($package_id),
            'package_type' => get_post_meta($package_id, 'jobsearch_field_package_type', true),
            'package_price' => get_post_meta($package_id, 'jobsearch_field_package_price', true),
        );

        $pkg_chrgs_type = get_post_meta($package_id, 'jobsearch_field_charges_type', true);
        if ($pkg_chrgs_type == 'free') {
            $packge_fields_arr['package_price'] = 0;
        }

        if (!empty($_package_fields)) {
            foreach ($_package_fields as $_package_field) {
                $value = get_post_meta($package_id, 'jobsearch_field_' . $_package_field, true);
                $packge_fields_arr[$_package_field] = $value;
            }
        }

        if (isset($packge_fields_arr['package_expiry_time']) && $packge_fields_arr['package_expiry_time'] > 0 && isset($packge_fields_arr['package_expiry_time_unit'])) {
            $pkg_expiry = $packge_fields_arr['package_expiry_time'];
            $pkg_expiry_unit = $packge_fields_arr['package_expiry_time_unit'];
            $pkg_expiry_time = strtotime("+" . $pkg_expiry . " " . $pkg_expiry_unit, strtotime(current_time('d-m-Y H:i:s')));
        } else {
            $pkg_expiry_time = strtotime(current_time('d-m-Y H:i:s'));
        }
        $packge_fields_arr['package_expiry_timestamp'] = $pkg_expiry_time;
        
        if ($is_unlimited_pkg == 'on') {
            $pkg_expiry_time = strtotime("+10 years", current_time('timestamp'));
            $packge_fields_arr['package_expiry_time'] = '10';
            $packge_fields_arr['package_expiry_time_unit'] = 'years';
            $packge_fields_arr['package_expiry_timestamp'] = $pkg_expiry_time;
            $packge_fields_arr['unlimited_pkg'] = 'yes';
        }
        //
        if ($act_pkg_type == 'job') {
            $is_unlimited_numjobs = get_post_meta($package_id, 'jobsearch_field_unlimited_numjobs', true);
            $is_unlimited_jobsexp = get_post_meta($package_id, 'jobsearch_field_unlimited_jobsexp', true);
            if ($is_unlimited_jobsexp == 'on') {
                $packge_fields_arr['job_expiry_time'] = '10';
                $packge_fields_arr['job_expiry_time_unit'] = 'years';
                $packge_fields_arr['unlimited_jobexp'] = 'yes';
            }
            if ($is_unlimited_numjobs == 'on') {
                $packge_fields_arr['num_of_jobs'] = '1000000';
                $packge_fields_arr['unlimited_numjobs'] = 'yes';
            }
        } else if ($act_pkg_type == 'featured_jobs') {
            $is_unlimited_numfjobs = get_post_meta($package_id, 'jobsearch_field_unlimited_numfjobs', true);
            $is_unlimited_fjobscr = get_post_meta($package_id, 'jobsearch_field_unlimited_fjobscr', true);
            $is_unlimited_fjobexp = get_post_meta($package_id, 'jobsearch_field_unlimited_fjobexp', true);
            $is_unlimited_fcredexp = get_post_meta($package_id, 'jobsearch_field_unlimited_fcredexp', true);
            
            if ($is_unlimited_fjobexp == 'on') {
                $packge_fields_arr['fjob_expiry_time'] = '10';
                $packge_fields_arr['fjob_expiry_time_unit'] = 'years';
                $packge_fields_arr['unlimited_fjobexp'] = 'yes';
            }
            if ($is_unlimited_fcredexp == 'on') {
                $packge_fields_arr['fcred_expiry_time'] = '10';
                $packge_fields_arr['fcred_expiry_time_unit'] = 'years';
                $packge_fields_arr['unlimited_fcredexp'] = 'yes';
            }
            if ($is_unlimited_numfjobs == 'on') {
                $packge_fields_arr['num_of_fjobs'] = '1000000';
                $packge_fields_arr['unlimited_numfjobs'] = 'yes';
            }
            if ($is_unlimited_fjobscr == 'on') {
                $packge_fields_arr['feat_job_credits'] = '1000000';
                $packge_fields_arr['unlimited_fjobcrs'] = 'yes';
            }
        } else if ($pkg_type == 'emp_allin_one') {
            $is_unlimited_jobs = get_post_meta($package_id, 'jobsearch_field_unlim_allinjobs', true);
            $is_unlimited_fjobs = get_post_meta($package_id, 'jobsearch_field_unlim_allinfjobs', true);
            $is_unlimited_jobexp = get_post_meta($package_id, 'jobsearch_field_unlim_allinjobexp', true);
            $is_unlimited_fcredexp = get_post_meta($package_id, 'jobsearch_field_unlimited_fall_credexp', true);
            $is_unlimited_cvs = get_post_meta($package_id, 'jobsearch_field_unlim_allinnumcvs', true);

            if ($is_unlimited_jobexp == 'on') {
                $packge_fields_arr['allinjob_expiry_time'] = '10';
                $packge_fields_arr['allinjob_expiry_time_unit'] = 'years';
                $packge_fields_arr['unlimited_jobsexp'] = 'yes';
            }
            if ($is_unlimited_fcredexp == 'on') {
                $packge_fields_arr['fall_cred_expiry_time'] = '10';
                $packge_fields_arr['fall_cred_expiry_time_unit'] = 'years';
                $packge_fields_arr['unlimited_fcredexp'] = 'yes';
            }
            if ($is_unlimited_jobs == 'on') {
                $packge_fields_arr['allin_num_jobs'] = '1000000';
                $packge_fields_arr['unlimited_numjobs'] = 'yes';
            }
            if ($is_unlimited_fjobs == 'on') {
                $packge_fields_arr['allin_num_fjobs'] = '1000000';
                $packge_fields_arr['unlimited_numfjobs'] = 'yes';
            }
            if ($is_unlimited_cvs == 'on') {
                $packge_fields_arr['allin_num_cvs'] = '1000000';
                $packge_fields_arr['unlimited_numcvs'] = 'yes';
            }
        } else if ($pkg_type == 'employer_profile') {
            $is_unlimited_jobs = get_post_meta($package_id, 'jobsearch_field_unlim_emprofjobs', true);
            $is_unlimited_fjobs = get_post_meta($package_id, 'jobsearch_field_unlim_emproffjobs', true);
            $is_unlimited_jobexp = get_post_meta($package_id, 'jobsearch_field_unlim_emprofjobexp', true);
            $is_unlimited_fcredexp = get_post_meta($package_id, 'jobsearch_field_unlimited_emprof_fcredexp', true);
            $is_unlimited_cvs = get_post_meta($package_id, 'jobsearch_field_unlim_emprofnumcvs', true);
            $is_unlimited_promote_expiry = get_post_meta($package_id, 'jobsearch_field_unlimited_emprof_promote_exp', true);

            if ($is_unlimited_jobexp == 'on') {
                $packge_fields_arr['emprofjob_expiry_time'] = '10';
                $packge_fields_arr['emprofjob_expiry_time_unit'] = 'years';
                $packge_fields_arr['unlimited_jobsexp'] = 'yes';
            }
            if ($is_unlimited_fcredexp == 'on') {
                $packge_fields_arr['emprof_fcred_expiry_time'] = '10';
                $packge_fields_arr['emprof_fcred_expiry_time_unit'] = 'years';
                $packge_fields_arr['unlimited_fcredexp'] = 'yes';
            }
            if ($is_unlimited_jobs == 'on') {
                $packge_fields_arr['emprof_num_jobs'] = '1000000';
                $packge_fields_arr['unlimited_numjobs'] = 'yes';
            }
            if ($is_unlimited_fjobs == 'on') {
                $packge_fields_arr['emprof_num_fjobs'] = '1000000';
                $packge_fields_arr['unlimited_numfjobs'] = 'yes';
            }
            if ($is_unlimited_cvs == 'on') {
                $packge_fields_arr['emprof_num_cvs'] = '1000000';
                $packge_fields_arr['unlimited_numcvs'] = 'yes';
            }
            
            //
            if (isset($packge_fields_arr['emprof_promote_expiry_time']) && $packge_fields_arr['emprof_promote_expiry_time'] > 0 && isset($packge_fields_arr['emprof_promote_expiry_time_unit'])) {
                $promote_expiry = $packge_fields_arr['emprof_promote_expiry_time'];
                $promote_expiry_unit = $packge_fields_arr['emprof_promote_expiry_time_unit'];
                $promote_expiry_time = strtotime("+" . $promote_expiry . " " . $promote_expiry_unit, strtotime(current_time('d-m-Y H:i:s')));
            } else {
                $promote_expiry_time = strtotime(current_time('d-m-Y H:i:s'));
            }
            $packge_fields_arr['emprof_promote_expiry_timestamp'] = $promote_expiry_time;
            if ($is_unlimited_promote_expiry == 'on') {
                $promote_expiry_time = strtotime("+10 years", current_time('timestamp'));
                $packge_fields_arr['emprof_promote_expiry_time'] = '10';
                $packge_fields_arr['emprof_promote_expiry_time_unit'] = 'years';
                $packge_fields_arr['emprof_promote_expiry_timestamp'] = $promote_expiry_time;
                $packge_fields_arr['unlimited_promote_expiry'] = 'yes';
            }
        } else if ($act_pkg_type == 'cv') {
            $is_unlimited_numcvs = get_post_meta($package_id, 'jobsearch_field_unlimited_numcvs', true);
            if ($is_unlimited_numcvs == 'on') {
                $packge_fields_arr['num_of_cvs'] = '1000000';
                $packge_fields_arr['unlimited_numcvs'] = 'yes';
            }
        } else if ($act_pkg_type == 'candidate') {
            $is_unlimited_numcapps = get_post_meta($package_id, 'jobsearch_field_unlimited_numcapps', true);
            if ($is_unlimited_numcapps == 'on') {
                $packge_fields_arr['num_of_apps'] = '1000000';
                $packge_fields_arr['unlimited_numcapps'] = 'yes';
            }
        } else if ($act_pkg_type == 'candidate_profile') {
            $is_unlimited_numcapps = get_post_meta($package_id, 'jobsearch_field_unlim_candprofnumapps', true);
            if ($is_unlimited_numcapps == 'on') {
                $packge_fields_arr['candprof_num_apps'] = '1000000';
                $packge_fields_arr['unlimited_numcapps'] = 'yes';
            }
            
            //
            $is_unlimited_promote_expiry = get_post_meta($package_id, 'jobsearch_field_unlimited_candprof_promote_exp', true);
            if (isset($packge_fields_arr['candprof_promote_expiry_time']) && $packge_fields_arr['candprof_promote_expiry_time'] > 0 && isset($packge_fields_arr['candprof_promote_expiry_time_unit'])) {
                $promote_expiry = $packge_fields_arr['candprof_promote_expiry_time'];
                $promote_expiry_unit = $packge_fields_arr['candprof_promote_expiry_time_unit'];
                $promote_expiry_time = strtotime("+" . $promote_expiry . " " . $promote_expiry_unit, strtotime(current_time('d-m-Y H:i:s')));
            } else {
                $promote_expiry_time = strtotime(current_time('d-m-Y H:i:s'));
            }
            $packge_fields_arr['candprof_promote_expiry_timestamp'] = $promote_expiry_time;
            if ($is_unlimited_promote_expiry == 'on') {
                $promote_expiry_time = strtotime("+10 years", current_time('timestamp'));
                $packge_fields_arr['candprof_promote_expiry_time'] = '10';
                $packge_fields_arr['candprof_promote_expiry_time_unit'] = 'years';
                $packge_fields_arr['candprof_promote_expiry_timestamp'] = $promote_expiry_time;
                $packge_fields_arr['unlimited_promote_expiry'] = 'yes';
            }
        }

        if (class_exists('WC_Subscription')) {
            $pkg_attach_product = get_post_meta($package_id, 'jobsearch_package_product', true);
            $package_product_obj = $pkg_attach_product != '' ? get_page_by_path($pkg_attach_product, 'OBJECT', 'product') : '';
            if ($pkg_attach_product != '' && is_object($package_product_obj)) {
                $product_id = $package_product_obj->ID;
                $_product_obj = wc_get_product($product_id);
                $_product_type = $_product_obj->is_type('subscription');
                if ($_product_type) {
                    $packge_fields_arr['contains_subscription'] = 'true';
                }
            }
            if (isset($packge_fields_arr['package_expiry_time']) && isset($packge_fields_arr['package_expiry_time_unit'])) {
                $ordr_subscription_id = JobSearch_WC_Subscription::order_subscription($order_id, $order_user_id);
                $subscription_obj = new WC_Subscription($ordr_subscription_id);
                // date_paid
                // last_order_date_created
                // last_order_date_paid
                $subs_last_paydate = $subscription_obj->get_date('last_order_date_created');
                $subs_next_paydate = $subscription_obj->get_date('next_payment');
                //
                if ($subs_last_paydate != '' && $subs_next_paydate != '') {
                    
                    $subs_next_paydate = strtotime($subs_next_paydate);
                    $subs_last_paydate = strtotime($subs_last_paydate);
                    if ($subs_next_paydate > $subs_last_paydate) {
                        $days_between_pay = ceil(abs($subs_next_paydate - $subs_last_paydate) / 86400);
                        $packge_fields_arr['package_expiry_time'] = $days_between_pay;
                        $packge_fields_arr['package_expiry_time_unit'] = 'days';

                        //
                        $pkg_expiry_time = strtotime("+" . $days_between_pay . " days", strtotime(current_time('d-m-Y H:i:s')));
                        $packge_fields_arr['package_expiry_timestamp'] = $pkg_expiry_time;
                    }
                }
                if ($pkg_type == 'cv') {
                    $packge_fields_arr['jobsearch_order_cvs_list'] = '';
                } else if ($pkg_type == 'job') {
                    $packge_fields_arr['jobsearch_order_jobs_list'] = '';
                } else if ($pkg_type == 'featured_jobs') {
                    $packge_fields_arr['jobsearch_order_featc_list'] = '';
                } else if ($pkg_type == 'emp_allin_one') {
                    $packge_fields_arr['jobsearch_order_fjobs_list'] = '';
                    $packge_fields_arr['jobsearch_order_jobs_list'] = '';
                    $packge_fields_arr['jobsearch_order_cvs_list'] = '';
                } else if ($pkg_type == 'employer_profile') {
                    $packge_fields_arr['jobsearch_order_fjobs_list'] = '';
                    $packge_fields_arr['jobsearch_order_jobs_list'] = '';
                    $packge_fields_arr['jobsearch_order_cvs_list'] = '';
                } else if ($pkg_type == 'candidate') {
                    $packge_fields_arr['jobsearch_order_apps_list'] = '';
                }
            }
        }

        //
        $packge_fields_arr = apply_filters('jobsearch_package_fields_arr_before_order_set', $packge_fields_arr, $order_id, $package_id, $pkg_type);
        foreach ($packge_fields_arr as $fields_arr_key => $fields_arr_val) {
            update_post_meta($order_id, $fields_arr_key, $fields_arr_val);
        }
        //
    }

    public function set_job_expiry_and_status($job_id, $order_id = 0) {
        global $jobsearch_plugin_options;
        $free_jobs_allow = isset($jobsearch_plugin_options['free-jobs-allow']) ? $jobsearch_plugin_options['free-jobs-allow'] : '';
        $job_def_status = isset($jobsearch_plugin_options['job-default-status']) ? $jobsearch_plugin_options['job-default-status'] : '';

        $user_id = get_current_user_id();

        $post_to_approve = false;
        
        $job_is_alrdy_post = get_post_meta($job_id, 'jobsearch_job_is_already_posted', true);
        
        if ($free_jobs_allow == 'on') {
            // job expiry in days
            $job_expiry_days = isset($jobsearch_plugin_options['free-job-post-expiry']) && $jobsearch_plugin_options['free-job-post-expiry'] > 0 ? $jobsearch_plugin_options['free-job-post-expiry'] : 30;
            // job expiry time
            if ($job_expiry_days > 0) {
                $job_expiry_date = strtotime("+" . $job_expiry_days . " day", strtotime(current_time('d-m-Y H:i:s')));
                $job_expiry_date = apply_filters('jobsearch_job_assign_expiry_date_front', $job_expiry_date);
                update_post_meta($job_id, 'jobsearch_field_job_expiry_date', $job_expiry_date);
            }
            do_action('jobsearch_after_set_job_expiry_infree', $job_id);

            //
            $post_to_approve = true;
        } else {
            
            $job_packages_arr = get_post_meta($job_id, 'attach_packages_array', true);
            if (!empty($job_packages_arr)) {
                $job_package_fields = end($job_packages_arr);

                //
                $pkg_type = isset($job_package_fields['package_type']) ? $job_package_fields['package_type'] : '';
                if ($pkg_type == 'featured_jobs') {
                    $pkg_job_expiry = isset($job_package_fields['fjob_expiry_time']) ? $job_package_fields['fjob_expiry_time'] : 0;
                    $pkg_job_expiry_unit = isset($job_package_fields['fjob_expiry_time_unit']) ? $job_package_fields['fjob_expiry_time_unit'] : 'days';
                } else if ($pkg_type == 'emp_allin_one') {
                    $pkg_job_expiry = isset($job_package_fields['allinjob_expiry_time']) ? $job_package_fields['allinjob_expiry_time'] : 0;
                    $pkg_job_expiry_unit = isset($job_package_fields['allinjob_expiry_time_unit']) ? $job_package_fields['allinjob_expiry_time_unit'] : 'days';
                } else if ($pkg_type == 'employer_profile') {
                    $pkg_job_expiry = isset($job_package_fields['emprofjob_expiry_time']) ? $job_package_fields['emprofjob_expiry_time'] : 0;
                    $pkg_job_expiry_unit = isset($job_package_fields['emprofjob_expiry_time_unit']) ? $job_package_fields['emprofjob_expiry_time_unit'] : 'days';
                } else {
                    $pkg_job_expiry = isset($job_package_fields['job_expiry_time']) ? $job_package_fields['job_expiry_time'] : 0;
                    $pkg_job_expiry_unit = isset($job_package_fields['job_expiry_time_unit']) ? $job_package_fields['job_expiry_time_unit'] : 'days';
                }
                if ($pkg_job_expiry > 0) {
                    $job_expiry_date = strtotime("+" . $pkg_job_expiry . " " . $pkg_job_expiry_unit, strtotime(current_time('d-m-Y H:i:s')));
                    $job_expiry_date = apply_filters('jobsearch_job_assign_expiry_date_front', $job_expiry_date);
                    update_post_meta($job_id, 'jobsearch_field_job_expiry_date', $job_expiry_date);
                    
                    //
                    $post_to_approve = true;
                }
                
                //
                $up_post = array(
                    'ID' => $job_id,
                    'post_status' => 'publish',
                );
                wp_update_post($up_post);
            }
        }

        // set a cron for job expiry
        if ($job_expiry_date > current_time('timestamp')) {
            wp_clear_scheduled_hook('jobsearch_job_expiry_cron_event_' . $job_id, array($job_id, $user_id));
            wp_schedule_single_event($job_expiry_date, 'jobsearch_job_expiry_cron_event_' . $job_id, array($job_id, $user_id));
            update_post_meta($job_id, 'jobsearch_job_single_exp_cron', 'yes');
        }
        //
        
        do_action('jobsearch_front_job_expiry_set_after', $job_id);

        $employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

        // job default status
        $job_status = get_post_meta($job_id, 'jobsearch_field_job_status', true);

        // Check if job status is already approved
        // then don't change status
        if ($job_status != 'approved') {
            if ($job_def_status == 'admin-review') {
                $post_to_approve = false;
                update_post_meta($job_id, 'jobsearch_field_job_status', 'admin-review');
            } else {
                $employer_status = get_post_meta($employer_id, 'jobsearch_field_employer_approved', true);

                if ($employer_status == 'on') {
                    $post_to_approve = true;
                    update_post_meta($job_id, 'jobsearch_field_job_status', 'approved');
                    update_post_meta($job_id, 'jobsearch_job_employer_status', 'approved');
                    $c_user = wp_get_current_user();
                    do_action('jobsearch_job_approved_to_employer', $c_user, $job_id);
                } else {
                    $post_to_approve = false;
                    update_post_meta($job_id, 'jobsearch_field_job_status', 'admin-review');
                }
            }
            //
            if ($job_is_alrdy_post != 'yes') {
                if ($post_to_approve) {
                    do_action('jobsearch_newjob_posted_at_frontend', $job_id);
                } else {
                    update_post_meta($job_id, 'jobsearch_job_is_under_review', 'yes');
                }
            }
            //
            do_action('jobsearch_job_post_after_approve_review', $job_id);
            update_post_meta($job_id, 'jobsearch_job_is_already_posted', 'yes');
        }
    }

    public function create_new_job_packg_order($pckg_id, $job_id) {
        global $woocommerce;

        $user_id = get_current_user_id();
        $user_id = apply_filters('jobsearch_in_creatjobpkg_order_user_id', $user_id);
        
        $user_obj = get_user_by('ID', $user_id);
        $user_displayname = $user_obj->display_name;
        $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);
        $user_bio = $user_obj->description;
        $user_website = $user_obj->user_url;
        $user_email = $user_obj->user_email;
        $user_fname = $user_obj->first_name;
        $user_lname = $user_obj->last_name;

        $first_name = $user_fname;
        $last_name = $user_lname;
        if ($user_fname == '' && $user_lname == '') {
            $first_name = $user_displayname;
            $last_name = '';
        }

        if (jobsearch_user_isemp_member($user_id)) {
            $employer_id = jobsearch_user_isemp_member($user_id);
        } else {
            $employer_id = jobsearch_get_user_employer_id($user_id);
        }

        $user_phone = get_post_meta($employer_id, 'jobsearch_field_user_phone', true);
        $user_address = get_post_meta($employer_id, 'jobsearch_field_location_address', true);
        $user_city = get_post_meta($employer_id, 'jobsearch_field_location_location3', true);
        $user_state = get_post_meta($employer_id, 'jobsearch_field_location_location2', true);
        $user_country = get_post_meta($employer_id, 'jobsearch_field_location_location1', true);

        $product_id = 0;
        $package_product = get_post_meta($pckg_id, 'jobsearch_package_product', true);
        $package_product_obj = $package_product != '' ? get_page_by_path($package_product, 'OBJECT', 'product') : '';
        if ($package_product != '' && is_object($package_product_obj)) {
            $product_id = $package_product_obj->ID;
        }

        $free_package_restrict = apply_filters('jobsearch_free_package_restrict_multi_memberships', '', $user_id, $pckg_id, 'dash_error');

        if ($product_id > 0 && get_post_type($product_id) == 'product' && empty($free_package_restrict)) {

            $address = array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'company' => '',
                'email' => $user_email,
                'phone' => $user_phone,
                'address_1' => $user_address,
                'address_2' => '',
                'city' => $user_city,
                'state' => $user_state,
                'postcode' => '',
                'country' => $user_country
            );

            // Now we create the order
            $order = wc_create_order();

            $order->add_product(wc_get_product($product_id), 1);
            $order->set_address($address, 'billing');
            //
            $order->calculate_totals();
            $order_id = $order->get_ID();

            $order->update_status('processing');
            //
            update_post_meta($order_id, 'jobsearch_order_attach_with', 'package');
            update_post_meta($order_id, 'jobsearch_order_package', $pckg_id);
            update_post_meta($order_id, 'jobsearch_order_user', $user_id);
            //
            update_post_meta($order_id, 'jobsearch_order_attach_job_id', $job_id);
            // For free package
            update_post_meta($order_id, 'jobsearch_order_transaction_type', 'free');
            //
            $order->update_status('completed');
        }
    }

    public function create_new_featured_job_packg_order($pckg_id, $job_id, $user_id) {
        global $woocommerce;

        $user_obj = get_user_by('ID', $user_id);
        $user_displayname = $user_obj->display_name;
        $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);
        $user_bio = $user_obj->description;
        $user_website = $user_obj->user_url;
        $user_email = $user_obj->user_email;
        $user_fname = $user_obj->first_name;
        $user_lname = $user_obj->last_name;

        $first_name = $user_fname;
        $last_name = $user_lname;
        if ($user_fname == '' && $user_lname == '') {
            $first_name = $user_displayname;
            $last_name = '';
        }
        if (jobsearch_user_isemp_member($user_id)) {
            $employer_id = jobsearch_user_isemp_member($user_id);
        } else {
            $employer_id = jobsearch_get_user_employer_id($user_id);
        }

        $user_phone = get_post_meta($employer_id, 'jobsearch_field_user_phone', true);
        $user_address = get_post_meta($employer_id, 'jobsearch_field_location_address', true);
        $user_city = get_post_meta($employer_id, 'jobsearch_field_location_location3', true);
        $user_state = get_post_meta($employer_id, 'jobsearch_field_location_location2', true);
        $user_country = get_post_meta($employer_id, 'jobsearch_field_location_location1', true);

        $product_id = 0;
        $package_product = get_post_meta($pckg_id, 'jobsearch_package_product', true);
        $package_product_obj = $package_product != '' ? get_page_by_path($package_product, 'OBJECT', 'product') : '';
        if ($package_product != '' && is_object($package_product_obj)) {
            $product_id = $package_product_obj->ID;
        }

        if ($product_id > 0 && get_post_type($product_id) == 'product') {

            $address = array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'company' => '',
                'email' => $user_email,
                'phone' => $user_phone,
                'address_1' => $user_address,
                'address_2' => '',
                'city' => $user_city,
                'state' => $user_state,
                'postcode' => '',
                'country' => $user_country
            );

            // Now we create the order
            $order = wc_create_order();

            $order->add_product(wc_get_product($product_id), 1);
            $order->set_address($address, 'billing');
            //
            $order->calculate_totals();
            $order_id = $order->get_ID();

            //$order->update_status('processing');
            //
            update_post_meta($order_id, 'jobsearch_order_attach_with', 'package');
            update_post_meta($order_id, 'jobsearch_order_package', $pckg_id);
            update_post_meta($order_id, 'jobsearch_order_user', $user_id);
            //
            update_post_meta($order_id, 'jobsearch_order_attach_job_id', $job_id);
            // For paid package
            update_post_meta($order_id, 'jobsearch_order_transaction_type', 'paid');
            update_post_meta($job_id, 'jobsearch_field_job_featured', 'on');
            //
            $pckg_expiry = get_post_meta($pckg_id, 'jobsearch_field_package_expiry_time', true);
            $pckg_expiry_unit = get_post_meta($pckg_id, 'jobsearch_field_package_expiry_time_unit', true);
            $pkg_expiry_time = strtotime("+" . $pckg_expiry . " " . $pckg_expiry_unit, strtotime(current_time('d-m-Y H:i:s')));
            $pkg_expiry_time = date('d-m-Y H:i:s', $pkg_expiry_time);
            update_post_meta($job_id, 'jobsearch_field_job_feature_till', $pkg_expiry_time);
            //
            //$order->update_status('completed');

            wp_delete_post($order_id, true);
        }
    }

    public function add_job_id_to_order($job_id, $order_id) {
        $pkg_type = get_post_meta($order_id, 'package_type', true);
        if ($pkg_type == 'job') {
            if ($job_id > 0 && $order_id > 0) {
                $order_jobs = get_post_meta($order_id, 'jobsearch_order_jobs_list', true);
                if ($order_jobs != '') {
                    $order_jobs = explode(',', $order_jobs);
                    $order_jobs[] = $job_id;
                    $order_jobs = implode(',', $order_jobs);
                } else {
                    $order_jobs = $job_id;
                }
                update_post_meta($order_id, 'jobsearch_order_jobs_list', $order_jobs);
            }
        }
    }

    public function add_featjob_id_to_order($job_id, $order_id) {
        $make_feature = get_post_meta($job_id, 'make_it_to_feature', true);
        $pkg_type = get_post_meta($order_id, 'package_type', true);
        if ($pkg_type == 'featured_jobs') {
            
            $current_date = current_time('timestamp');
            
            $remain_normal_jobs = jobsearch_pckg_order_remaining_fjobs($order_id);
            $remain_feature_jobs = jobsearch_pckg_order_remain_featjob_credits($order_id);
            
            //
            $order_jobs = get_post_meta($order_id, 'jobsearch_order_fjobs_list', true);
            if ($order_jobs != '') {
                $order_jobs = explode(',', $order_jobs);
                $order_jobs[] = $job_id;
                $order_jobs = implode(',', $order_jobs);
            } else {
                $order_jobs = $job_id;
            }
            update_post_meta($order_id, 'jobsearch_order_fjobs_list', $order_jobs);

            //            
            if ($remain_feature_jobs > 0 && $make_feature == 'yes') {
                
                $order_featc_list = get_post_meta($order_id, 'jobsearch_order_featc_list', true);
                if ($order_featc_list != '') {
                    $order_featc_list = explode(',', $order_featc_list);
                    $order_featc_list[] = $job_id;
                    $order_featc_list = implode(',', $order_featc_list);
                } else {
                    $order_featc_list = $job_id;
                }
                update_post_meta($order_id, 'jobsearch_order_featc_list', $order_featc_list);

                update_post_meta($job_id, 'jobsearch_field_job_featured', 'on');
                
                $fcred_exp_time = get_post_meta($order_id, 'fcred_expiry_time', true);
                $fcred_exp_time_unit = get_post_meta($order_id, 'fcred_expiry_time_unit', true);
                $tofeat_expiry_time = strtotime("+" . $fcred_exp_time . " " . $fcred_exp_time_unit, $current_date);
                if ($tofeat_expiry_time > 0) {
                    $feature_expiry_datetime = date('d-m-Y H:i:s', $tofeat_expiry_time);
                    update_post_meta($job_id, 'jobsearch_field_job_feature_till', $feature_expiry_datetime);
                }
            }
            update_post_meta($job_id, 'make_it_to_feature', '');
        }
    }

    public function add_allinjob_id_to_order($job_id, $order_id) {
        $make_feature = get_post_meta($job_id, 'make_it_to_feature', true);
        $pkg_type = get_post_meta($order_id, 'package_type', true);
        //
        if ($pkg_type == 'emp_allin_one') {
        
            $current_date = current_time('timestamp');
            
            $remain_normal_jobs = jobsearch_allinpckg_order_remaining_jobs($order_id);
            $remain_feature_jobs = jobsearch_allinpckg_order_remaining_fjobs($order_id);
            
            //
            $order_jobs = get_post_meta($order_id, 'jobsearch_order_jobs_list', true);
            if ($order_jobs != '') {
                $order_jobs = explode(',', $order_jobs);
                $order_jobs[] = $job_id;
                $order_jobs = implode(',', $order_jobs);
            } else {
                $order_jobs = $job_id;
            }
            update_post_meta($order_id, 'jobsearch_order_jobs_list', $order_jobs);

            //
            if ($remain_feature_jobs > 0 && $make_feature == 'yes') {
                
                $order_featc_list = get_post_meta($order_id, 'jobsearch_order_fjobs_list', true);
                if ($order_featc_list != '') {
                    $order_featc_list = explode(',', $order_featc_list);
                    $order_featc_list[] = $job_id;
                    $order_featc_list = implode(',', $order_featc_list);
                } else {
                    $order_featc_list = $job_id;
                }
                update_post_meta($order_id, 'jobsearch_order_fjobs_list', $order_featc_list);

                update_post_meta($job_id, 'jobsearch_field_job_featured', 'on');
                
                $fcred_exp_time = get_post_meta($order_id, 'fall_cred_expiry_time', true);
                $fcred_exp_time_unit = get_post_meta($order_id, 'fall_cred_expiry_time_unit', true);
                $tofeat_expiry_time = strtotime("+" . $fcred_exp_time . " " . $fcred_exp_time_unit, $current_date);
                if ($tofeat_expiry_time > 0) {
                    $feature_expiry_datetime = date('d-m-Y H:i:s', $tofeat_expiry_time);
                    update_post_meta($job_id, 'jobsearch_field_job_feature_till', $feature_expiry_datetime);
                }
            }
            update_post_meta($job_id, 'make_it_to_feature', '');
        }
    }

    public function add_emprofjob_id_to_order($job_id, $order_id) {
        $make_feature = get_post_meta($job_id, 'make_it_to_feature', true);
        $pkg_type = get_post_meta($order_id, 'package_type', true);
        //
        if ($pkg_type == 'employer_profile') {
        
            $current_date = current_time('timestamp');
            
            $remain_normal_jobs = jobsearch_emprofpckg_order_remaining_jobs($order_id);
            $remain_feature_jobs = jobsearch_emprofpckg_order_remaining_fjobs($order_id);
            
            //
            $order_jobs = get_post_meta($order_id, 'jobsearch_order_jobs_list', true);
            if ($order_jobs != '') {
                $order_jobs = explode(',', $order_jobs);
                $order_jobs[] = $job_id;
                $order_jobs = implode(',', $order_jobs);
            } else {
                $order_jobs = $job_id;
            }
            update_post_meta($order_id, 'jobsearch_order_jobs_list', $order_jobs);

            //
            if ($remain_feature_jobs > 0 && $make_feature == 'yes') {
                
                $order_featc_list = get_post_meta($order_id, 'jobsearch_order_fjobs_list', true);
                if ($order_featc_list != '') {
                    $order_featc_list = explode(',', $order_featc_list);
                    $order_featc_list[] = $job_id;
                    $order_featc_list = implode(',', $order_featc_list);
                } else {
                    $order_featc_list = $job_id;
                }
                update_post_meta($order_id, 'jobsearch_order_fjobs_list', $order_featc_list);

                update_post_meta($job_id, 'jobsearch_field_job_featured', 'on');
                
                $fcred_exp_time = get_post_meta($order_id, 'emprof_fcred_expiry_time', true);
                $fcred_exp_time_unit = get_post_meta($order_id, 'emprof_fcred_expiry_time_unit', true);
                $tofeat_expiry_time = strtotime("+" . $fcred_exp_time . " " . $fcred_exp_time_unit, $current_date);
                if ($tofeat_expiry_time > 0) {
                    $feature_expiry_datetime = date('d-m-Y H:i:s', $tofeat_expiry_time);
                    update_post_meta($job_id, 'jobsearch_field_job_feature_till', $feature_expiry_datetime);
                }
            }
            update_post_meta($job_id, 'make_it_to_feature', '');
        }
    }

    public function remove_user_job_from_dashboard() {
        $job_id = isset($_POST['job_id']) ? ($_POST['job_id']) : '';

        $user_id = get_current_user_id();
        $user_id = apply_filters('jobsearch_in_jobremve_fromdash_user_id', $user_id, $job_id);
        
        if (jobsearch_user_isemp_member($user_id)) {
            $employer_id = jobsearch_user_isemp_member($user_id);
        } else {
            $employer_id = jobsearch_get_user_employer_id($user_id);
        }

        if (jobsearch_employer_not_allow_to_mod()) {
            $msg = esc_html__('You are not allowed to delete this.', 'wp-jobsearch');
            echo json_encode(array('err_msg' => $msg));
            die;
        }

        $job_employer = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
        if ($job_employer == $employer_id) {
            wp_delete_post($job_id, true);
            echo json_encode(array('msg' => esc_html__('deleted', 'wp-jobsearch')));
        } else {
            echo json_encode(array('msg' => esc_html__('You are not allowed to delete this.', 'wp-jobsearch')));
        }
        die;
    }

}

global $Jobsearch_User_Job_Functions;
$Jobsearch_User_Job_Functions = new Jobsearch_User_Job_Functions();
