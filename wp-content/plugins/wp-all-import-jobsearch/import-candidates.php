<?php

$jobsearch_candidates = new RapidAddon('Wp Jobsearch', 'jobsearch_candidates');

$jobsearch__options = get_option('jobsearch_plugin_options');

$jobsearch_candidates->add_field('user_fname', 'First Name', 'text');
$jobsearch_candidates->add_field('user_lname', 'Last Name', 'text');
$jobsearch_candidates->add_field('user_email', 'Email', 'text');
$jobsearch_candidates->add_field('user_resm_id', 'Resume ID', 'text');
$jobsearch_candidates->add_field('job_title', 'Job Title', 'text');
$jobsearch_candidates->add_field('dob', 'Date of Birth', 'text');
$jobsearch_candidates->add_field('user_phone', 'Phone', 'text');
$jobsearch_candidates->add_field('featured_cand', 'Featured Candidate', 'radio', array('off' => 'No', 'on' => 'Yes'));
$jobsearch_candidates->add_field('urgent_cand', 'Urgent Candidate', 'radio', array('off' => 'No', 'on' => 'Yes'));
$jobsearch_candidates->add_field('cand_status', 'Candidate Status', 'radio', array('on' => 'Approved', 'off' => 'Pending'));

$jobsearch_candidates->add_field('cand_resmcv_file', 'Resume CV URL', 'text');
$jobsearch_candidates->add_field('cand_resm_eduxml', 'Education (XML Data Only)', 'text');
$jobsearch_candidates->add_field('cand_resm_expxml', 'Work Experience (XML Data Only)', 'text');
$jobsearch_candidates->add_field('cand_resm_portxml', 'Portfolio (XML Data Only)', 'text');
$jobsearch_candidates->add_field('cand_resm_awardxml', 'Honor & Awards (XML Data Only)', 'text');
$jobsearch_candidates->add_field('cand_resm_skillxml', 'Skills (XML Data Only)', 'text');

$jobsearch_candidates->add_field('job_min_salary', 'Salary', 'text');

$job_salary_types = isset($jobsearch__options['job-salary-types']) ? $jobsearch__options['job-salary-types'] : '';
if (!empty($job_salary_types)) {
    $salary_types = array();
    $slar_type_count = 1;
    foreach ($job_salary_types as $job_salary_type) {
        $salary_types['type_' . $slar_type_count] = $job_salary_type;
        $slar_type_count++;
    }
    $jobsearch_candidates->add_field('job_salary_type', 'Salary Type', 'radio', $salary_types);
} else {
    $jobsearch_candidates->add_field('job_salary_type', 'Salary Type', 'text');
}
$job_custom_fields_saved_data = get_option('jobsearch_custom_field_candidate');
if (is_array($job_custom_fields_saved_data) && sizeof($job_custom_fields_saved_data) > 0) {
    $field_names_counter = 0;
    foreach ($job_custom_fields_saved_data as $f_key => $custom_field_saved_data) {
        $cusfield_type = isset($custom_field_saved_data['type']) ? $custom_field_saved_data['type'] : '';
        $cusfield_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $cusfield_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
        if ($cusfield_label != '' && $cusfield_name != '') {
            if ($cusfield_type == 'dropdown') {
                $field_post_multi = isset($custom_field_saved_data['post-multi']) ? $custom_field_saved_data['post-multi'] : '';
                $dropdown_field_options = isset($custom_field_saved_data['options']) ? $custom_field_saved_data['options'] : '';
                $dropdown_opts = wp_jobsearch_drpdwn_options_arr($dropdown_field_options);
                if (!empty($dropdown_opts)) {
                    $jobsearch_candidates->add_field('cus_field_' . $cusfield_name, $cusfield_label, 'radio', $dropdown_opts);
                } else {
                    $jobsearch_candidates->add_field('cus_field_' . $cusfield_name, $cusfield_label, 'text');
                }
            } else {
                $jobsearch_candidates->add_field('cus_field_' . $cusfield_name, $cusfield_label, 'text');
            }
        }
    }
}
$jobsearch_candidates->add_field('user_facebook_url', 'Facebook URL', 'text');
$jobsearch_candidates->add_field('user_twitter_url', 'Twitter URL', 'text');
$jobsearch_candidates->add_field('user_gplus_url', 'Google Plus URL', 'text');
$jobsearch_candidates->add_field('user_linkedin_url', 'Linkedin URL', 'text');
$jobsearch_candidates->add_field('user_dribbble_url', 'Dribbble URL', 'text');
$candidate_social_mlinks = isset($jobsearch__options['candidate_social_mlinks']) ? $jobsearch__options['candidate_social_mlinks'] : '';
if (!empty($candidate_social_mlinks)) {
    if (isset($candidate_social_mlinks['title']) && is_array($candidate_social_mlinks['title'])) {
        $field_counter = 0;
        foreach ($candidate_social_mlinks['title'] as $field_title_val) {
            $field_random = rand(10000000, 99999999);

            if ($field_title_val != '') {
                $dynm_social_name = 'candidate_dynm_social' . ($field_counter);
                $jobsearch_candidates->add_field($dynm_social_name, $field_title_val, 'text');
            }
            $field_counter++;
        }
    }
}

$jobsearch_candidates->add_field('job_loc_contry', 'Country', 'text');
$jobsearch_candidates->add_field('job_loc_state', 'State', 'text');
$jobsearch_candidates->add_field('job_loc_city', 'City', 'text');
$jobsearch_candidates->add_field('job_loc_address', 'Full Address', 'text');
$jobsearch_candidates->add_field('job_loc_postcode', 'Postcode', 'text');
$jobsearch_candidates->add_field('job_loclat', 'Latitude', 'text');
$jobsearch_candidates->add_field('job_loclng', 'Longitude', 'text');

$jobsearch_candidates->set_import_function('wp_jobsearch_candidates_import');
// admin notice if WPAI and/or Wp Jobsearch isn't installed

if (function_exists('is_plugin_active')) {

    // display this notice if neither the free or pro version of the Wp Jobsearch plugin is active.
    if (!is_plugin_active("wp-jobsearch/wp-jobsearch.php")) {

        // Specify a custom admin notice.
        $jobsearch_candidates->admin_notice(
                'The Wp Jobsearch requires WP All Import <a href="http://wordpress.org/plugins/wp-all-import" target="_blank">Free</a> and the <a href="#">Wp Jobsearch</a> plugin.'
        );
    }

    // only run this add-on if the free or pro version of the Wp Jobsearch plugin is active.
    if (is_plugin_active("wp-jobsearch/wp-jobsearch.php")) {

        $jobsearch_candidates->run(
                array(
                    "post_types" => array("candidate")
                )
        );
    }
}

function wp_jobsearch_candidates_import($post_id, $data, $import_options) {

    global $jobsearch_candidates;

    $jobsearch__options = get_option('jobsearch_plugin_options');

    if ($jobsearch_candidates->can_update_meta('user_email', $import_options)) {
        $user_email = $data['user_email'];
        if ($user_email != '' && filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            if (email_exists($user_email)) {
                $user_obj = get_user_by('email', $user_email);
                $user_id = $user_obj->ID;
            } else {
                $user_login = substr($user_email, 0, strpos($user_email, '@'));
                if (username_exists($user_login)) {
                    $user_login = $user_login . rand(1000000, 9999999);
                }
                $user_pass = wp_generate_password(12);
                $create_user = wp_create_user($user_login, $user_pass, $user_email);
                if (!is_wp_error($create_user)) {
                    $user_id = $create_user;
                }
            }
            if (isset($user_id) && $user_id > 0) {
                //
                $user_def_array = array(
                    'ID' => $user_id,
                    'display_name' => get_the_title($post_id),
                    'role' => 'jobsearch_candidate',
                );
                if (isset($data['user_fname']) && $data['user_fname'] != '') {
                    $user_def_array['first_name'] = $data['user_fname'];
                }
                if (isset($data['user_lname']) && $data['user_lname'] != '') {
                    $user_def_array['last_name'] = $data['user_lname'];
                }
                wp_update_user($user_def_array);
                //
                $get_user_cand = jobsearch_get_user_candidate_id($user_id);
                if ($get_user_cand > 0) {
                    wp_delete_post($get_user_cand, true);
                }
                //
                update_post_meta($post_id, 'jobsearch_user_id', $user_id);
                update_post_meta($post_id, 'member_display_name', get_the_title($post_id));
                update_post_meta($post_id, 'jobsearch_field_user_email', $user_email);

                update_post_meta($post_id, 'post_date', current_time('timestamp'));

                //
                update_user_meta($user_id, 'jobsearch_candidate_id', $post_id);
            }
        }
    }

    $cand_real_title = get_the_title($post_id);
    $cand_title_slug = sanitize_title($cand_real_title);
    update_post_meta($post_id, 'jobsearch_crm_useresm_id', $cand_title_slug);
    if ($jobsearch_candidates->can_update_meta('user_resm_id', $import_options)) {
        //update_post_meta($post_id, 'jobsearch_crm_useresm_id', $data['user_resm_id']);
    }
    if ($jobsearch_candidates->can_update_meta('job_title', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_candidate_jobtitle', $data['job_title']);
    }
    if ($jobsearch_candidates->can_update_meta('dob', $import_options)) {
        $cand_dob = $data['dob'];
        if ($cand_dob != '') {
            $cand_dob = strtotime($cand_dob);
            $cand_dob_dd = date('d', $cand_dob);
            $cand_dob_mm = date('m', $cand_dob);
            $cand_dob_yy = date('Y', $cand_dob);

            update_post_meta($post_id, 'jobsearch_field_user_dob_dd', $cand_dob_dd);
            update_post_meta($post_id, 'jobsearch_field_user_dob_mm', $cand_dob_mm);
            update_post_meta($post_id, 'jobsearch_field_user_dob_yy', $cand_dob_yy);
        }
    }
    if ($jobsearch_candidates->can_update_meta('user_phone', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_user_justphone', $data['user_phone']);
        update_post_meta($post_id, 'jobsearch_field_user_phone', $data['user_phone']);
    }
    if ($jobsearch_candidates->can_update_meta('featured_cand', $import_options)) {
        $is_featured_cand = $data['featured_cand'];
        if ($is_featured_cand == 'on') {
            update_post_meta($post_id, '_feature_mber_frmadmin', 'yes');
            update_post_meta($post_id, 'jobsearch_field_feature_cand', 'on');
            update_post_meta($post_id, 'cuscand_feature_fbckend', 'on');
        } else {
            update_post_meta($post_id, '_feature_mber_frmadmin', 'no');
            update_post_meta($post_id, 'jobsearch_field_feature_cand', 'off');
        }
    }
    if ($jobsearch_candidates->can_update_meta('urgent_cand', $import_options)) {
        $is_urgent_cand = $data['urgent_cand'];
        if ($is_urgent_cand == 'on') {
            update_post_meta($post_id, '_urgent_cand_frmadmin', 'yes');
            update_post_meta($post_id, 'jobsearch_field_urgent_cand', 'on');
            update_post_meta($post_id, 'cuscand_urgent_fbckend', 'on');
        } else {
            update_post_meta($post_id, '_urgent_cand_frmadmin', 'no');
            update_post_meta($post_id, 'jobsearch_field_urgent_cand', 'off');
        }
    }
    if ($jobsearch_candidates->can_update_meta('cand_status', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_candidate_approved', $data['cand_status']);
    }

    if ($jobsearch_candidates->can_update_meta('cand_resm_eduxml', $import_options)) {
        $cand_resm_eduxml = $data['cand_resm_eduxml'];
        $xml_doc = simplexml_load_string($cand_resm_eduxml);
        if ($xml_doc && $cand_resm_eduxml != '') {
            $cand_resm_eduxml = new SimpleXMLElement($cand_resm_eduxml);
            $cand_resm_eduxml = json_decode(json_encode($cand_resm_eduxml), true);
            if (!empty($cand_resm_eduxml) && isset($cand_resm_eduxml['Education']) && is_array($cand_resm_eduxml['Education']) && sizeof($cand_resm_eduxml['Education']) > 0) {
                $edu_titles_arr = $edu_insts_arr = $edu_start_dates_arr = $edu_to_dates_arr = $edu_descs_arr = array();
                foreach ($cand_resm_eduxml['Education'] as $educ_item) {
                    $edu_title = isset($educ_item['ED_DegreeSpecialty']) ? $educ_item['ED_DegreeSpecialty'] : '';

                    if ($edu_title != '') {
                        $edu_inst = isset($educ_item['ED_Institute']) ? $educ_item['ED_Institute'] : '';
                        $edu_start_date = isset($educ_item['ED_From']) ? $educ_item['ED_From'] : '';
                        $edu_to_date = isset($educ_item['ED_To']) ? $educ_item['ED_To'] : '';
                        $edu_desc = isset($educ_item['ED_Description']) ? $educ_item['ED_Description'] : '';

                        $edu_titles_arr[] = $edu_title;
                        $edu_insts_arr[] = $edu_inst;
                        $edu_start_dates_arr[] = $edu_start_date;
                        $edu_to_dates_arr[] = $edu_to_date;
                        $edu_descs_arr[] = (!is_array($edu_desc) ? $edu_desc : '');
                    }
                }

                if (!empty($edu_titles_arr)) {
                    update_post_meta($post_id, 'jobsearch_field_education_title', $edu_titles_arr);
                    update_post_meta($post_id, 'jobsearch_field_education_academy', $edu_insts_arr);
                    update_post_meta($post_id, 'jobsearch_field_education_year', $edu_start_dates_arr);
                    update_post_meta($post_id, 'jobsearch_field_education_to_year', $edu_to_dates_arr);
                    update_post_meta($post_id, 'jobsearch_field_education_description', $edu_descs_arr);
                }
            }
        }
    }
    if ($jobsearch_candidates->can_update_meta('cand_resm_expxml', $import_options)) {
        $cand_resm_expxml = $data['cand_resm_expxml'];
        $xml_doc = simplexml_load_string($cand_resm_expxml);
        if ($xml_doc && $cand_resm_expxml != '') {
            $cand_resm_expxml = new SimpleXMLElement($cand_resm_expxml);
            $cand_resm_expxml = json_decode(json_encode($cand_resm_expxml), true);
            if (!empty($cand_resm_expxml) && isset($cand_resm_expxml['WorkExperience']) && is_array($cand_resm_expxml['WorkExperience']) && sizeof($cand_resm_expxml['WorkExperience']) > 0) {
                $exp_titles_arr = $exp_insts_arr = $exp_start_dates_arr = $exp_to_dates_arr = $exp_descs_arr = array();
                foreach ($cand_resm_expxml['WorkExperience'] as $exp_item) {
                    $exp_title = isset($exp_item['WE_JobTitle']) ? $exp_item['WE_JobTitle'] : '';

                    if ($exp_title != '') {
                        $exp_inst = isset($exp_item['WE_Company']) ? $exp_item['WE_Company'] : '';
                        $exp_start_date = isset($exp_item['WE_From']) ? $exp_item['WE_From'] : '';
                        if ($exp_start_date != '') {
                            $exp_start_date = date('d-m-Y', strtotime($exp_start_date));
                        }
                        $exp_to_date = isset($exp_item['WE_To']) ? $exp_item['WE_To'] : '';
                        if ($exp_to_date != '') {
                            $exp_to_date = date('d-m-Y', strtotime($exp_to_date));
                        }
                        $exp_desc = isset($exp_item['WE_Description']) ? $exp_item['WE_Description'] : '';

                        $exp_titles_arr[] = $exp_title;
                        $exp_insts_arr[] = $exp_inst;
                        $exp_start_dates_arr[] = $exp_start_date;
                        $exp_to_dates_arr[] = $exp_to_date;
                        $exp_descs_arr[] = (!is_array($exp_desc) ? $exp_desc : '');
                    }
                }

                if (!empty($exp_titles_arr)) {
                    update_post_meta($post_id, 'jobsearch_field_experience_title', $exp_titles_arr);
                    update_post_meta($post_id, 'jobsearch_field_experience_company', $exp_insts_arr);
                    update_post_meta($post_id, 'jobsearch_field_experience_start_date', $exp_start_dates_arr);
                    update_post_meta($post_id, 'jobsearch_field_experience_end_date', $exp_to_dates_arr);
                    update_post_meta($post_id, 'jobsearch_field_experience_description', $exp_descs_arr);
                }
            }
        }
    }
    if ($jobsearch_candidates->can_update_meta('cand_resm_portxml', $import_options)) {
        $cand_resm_portxml = $data['cand_resm_portxml'];
        $xml_doc = simplexml_load_string($cand_resm_portxml);
        if ($xml_doc && $cand_resm_portxml != '') {
            $cand_resm_portxml = new SimpleXMLElement($cand_resm_portxml);
            $cand_resm_portxml = json_decode(json_encode($cand_resm_portxml), true);
            if (!empty($cand_resm_portxml) && isset($cand_resm_portxml['Portfolio']) && is_array($cand_resm_portxml['Portfolio']) && sizeof($cand_resm_portxml['Portfolio']) > 0) {
                $port_titles_arr = $port_img_urls_arr = $port_urls_arr = $port_vid_urls_arr = array();
                foreach ($cand_resm_portxml['Portfolio'] as $port_item) {
                    $port_title = isset($port_item['PT_Title']) ? $port_item['PT_Title'] : '';

                    if ($port_title != '') {
                        $port_img_url = isset($port_item['PT_ImgURL']) ? $port_item['PT_ImgURL'] : '';
                        $port_url = isset($port_item['PT_URL']) ? $port_item['PT_URL'] : '';
                        $port_vid_url = isset($port_item['PT_VideoURL']) ? $port_item['PT_VideoURL'] : '';

                        $port_titles_arr[] = $port_title;
                        $port_img_urls_arr[] = $port_img_url;
                        $port_urls_arr[] = $port_url;
                        $port_vid_urls_arr[] = $port_vid_url;
                    }
                }

                if (!empty($port_titles_arr)) {
                    update_post_meta($post_id, 'jobsearch_field_portfolio_title', $port_titles_arr);
                    update_post_meta($post_id, 'jobsearch_field_portfolio_image', $port_img_urls_arr);
                    update_post_meta($post_id, 'jobsearch_field_portfolio_url', $port_urls_arr);
                    update_post_meta($post_id, 'jobsearch_field_portfolio_vurl', $port_vid_urls_arr);
                }
            }
        }
    }
    if ($jobsearch_candidates->can_update_meta('cand_resm_awardxml', $import_options)) {
        $cand_resm_awardxml = $data['cand_resm_awardxml'];
        $xml_doc = simplexml_load_string($cand_resm_awardxml);
        if ($xml_doc && $cand_resm_awardxml != '') {
            $cand_resm_awardxml = new SimpleXMLElement($cand_resm_awardxml);
            $cand_resm_awardxml = json_decode(json_encode($cand_resm_awardxml), true);
            if (!empty($cand_resm_awardxml) && isset($cand_resm_awardxml['Award']) && is_array($cand_resm_awardxml['Award']) && sizeof($cand_resm_awardxml['Award']) > 0) {
                $award_titles_arr = $award_years_arr = $award_descs_arr = array();
                foreach ($cand_resm_awardxml['Award'] as $award_item) {
                    $award_title = isset($award_item['AW_Title']) ? $award_item['AW_Title'] : '';

                    if ($award_title != '') {
                        $award_year = isset($award_item['AW_Year']) ? $award_item['AW_Year'] : '';
                        $award_desc = isset($award_item['AW_Description']) ? $award_item['AW_Description'] : '';

                        $award_titles_arr[] = $award_title;
                        $award_years_arr[] = $award_year;
                        $award_descs_arr[] = $award_desc;
                    }
                }

                if (!empty($award_titles_arr)) {
                    update_post_meta($post_id, 'jobsearch_field_award_title', $award_titles_arr);
                    update_post_meta($post_id, 'jobsearch_field_award_year', $award_years_arr);
                    update_post_meta($post_id, 'jobsearch_field_award_description', $award_descs_arr);
                }
            }
        }
    }
    if ($jobsearch_candidates->can_update_meta('cand_resm_skillxml', $import_options)) {
        $cand_resm_skillxml = $data['cand_resm_skillxml'];
        $xml_doc = simplexml_load_string($cand_resm_skillxml);
        if ($xml_doc && $cand_resm_skillxml != '') {
            $cand_resm_skillxml = new SimpleXMLElement($cand_resm_skillxml);
            $cand_resm_skillxml = json_decode(json_encode($cand_resm_skillxml), true);
            if (!empty($cand_resm_skillxml) && isset($cand_resm_skillxml['Skill']) && is_array($cand_resm_skillxml['Skill']) && sizeof($cand_resm_skillxml['Skill']) > 0) {
                $skill_titles_arr = $skill_percs_arr = array();
                foreach ($cand_resm_skillxml['Skill'] as $skill_item) {
                    $skill_title = isset($skill_item['SK_Title']) ? $skill_item['SK_Title'] : '';

                    if ($skill_title != '') {
                        $skill_perc = isset($skill_item['SK_Percentage']) ? $skill_item['SK_Percentage'] : '';

                        $skill_titles_arr[] = $skill_title;
                        $skill_percs_arr[] = $skill_perc;
                    }
                }

                if (!empty($skill_titles_arr)) {
                    update_post_meta($post_id, 'jobsearch_field_skill_title', $skill_titles_arr);
                    update_post_meta($post_id, 'jobsearch_field_skill_percentage', $skill_percs_arr);
                }
            }
        }
    }

    if ($jobsearch_candidates->can_update_meta('job_min_salary', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_candidate_salary', $data['job_min_salary']);
    }
    if ($jobsearch_candidates->can_update_meta('job_salary_type', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_candidate_salary_type', $data['job_salary_type']);
    }
    $job_custom_fields_saved_data = get_option('jobsearch_custom_field_candidate');
    if (is_array($job_custom_fields_saved_data) && sizeof($job_custom_fields_saved_data) > 0) {
        $field_names_counter = 0;
        foreach ($job_custom_fields_saved_data as $f_key => $custom_field_saved_data) {
            $cusfield_type = isset($custom_field_saved_data['type']) ? $custom_field_saved_data['type'] : '';
            $cusfield_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
            $cusfield_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
            if ($cusfield_label != '' && $cusfield_name != '') {
                if ($jobsearch_candidates->can_update_meta('cus_field_' . $cusfield_name, $import_options)) {
                    update_post_meta($post_id, $cusfield_name, $data['cus_field_' . $cusfield_name]);
                }
            }
        }
    }
    if ($jobsearch_candidates->can_update_meta('user_facebook_url', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_user_facebook_url', $data['user_facebook_url']);
    }
    if ($jobsearch_candidates->can_update_meta('user_twitter_url', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_user_twitter_url', $data['user_twitter_url']);
    }
    if ($jobsearch_candidates->can_update_meta('user_gplus_url', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_user_google_plus_url', $data['user_gplus_url']);
    }
    if ($jobsearch_candidates->can_update_meta('user_linkedin_url', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_user_linkedin_url', $data['user_linkedin_url']);
    }
    if ($jobsearch_candidates->can_update_meta('user_dribbble_url', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_user_dribbble_url', $data['user_dribbble_url']);
    }
    $candidate_social_mlinks = isset($jobsearch__options['candidate_social_mlinks']) ? $jobsearch__options['candidate_social_mlinks'] : '';
    if (!empty($candidate_social_mlinks)) {
        if (isset($candidate_social_mlinks['title']) && is_array($candidate_social_mlinks['title'])) {
            $field_counter = 0;
            foreach ($candidate_social_mlinks['title'] as $field_title_val) {
                $field_random = rand(10000000, 99999999);

                if ($field_title_val != '' && $jobsearch_candidates->can_update_meta('candidate_dynm_social' . $field_counter, $import_options)) {
                    $dynm_social_name = 'jobsearch_field_dynm_social' . ($field_counter);
                    update_post_meta($post_id, $dynm_social_name, $data['candidate_dynm_social' . $field_counter]);
                }
                $field_counter++;
            }
        }
    }

    $addres_str = '';
    $country_str = '';
    $city_str = '';
    if ($jobsearch_candidates->can_update_meta('job_loc_contry', $import_options)) {
        $country_str = $data['job_loc_contry'];
        update_post_meta($post_id, 'jobsearch_field_location_location1', $data['job_loc_contry']);
    }
    if ($jobsearch_candidates->can_update_meta('job_loc_state', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_location_location2', $data['job_loc_state']);
    }
    if ($jobsearch_candidates->can_update_meta('job_loc_city', $import_options)) {
        $city_str = $data['job_loc_city'];
        update_post_meta($post_id, 'jobsearch_field_location_location3', $data['job_loc_city']);
    }
    if ($country_str != '' && $city_str != '') {
        $addres_str = $city_str. ', ' . $country_str;
    } else {
        $addres_str = $country_str;
    }
    if ($jobsearch_candidates->can_update_meta('job_loc_address', $import_options)) {
        $the_adres = $data['job_loc_address'];
        if ($the_adres != '') {
            $addres_str = $the_adres;
        }
        update_post_meta($post_id, 'jobsearch_field_location_address', $the_adres);
    }
    $lat_lng_updted = false;
    if ($addres_str != '' && function_exists('jobsearch_allimprt_address_to_cords')) {
        $cords_arr = jobsearch_allimprt_address_to_cords($addres_str);
        if (isset($cords_arr['lat']) && isset($cords_arr['lng'])) {
            $lat_lng_updted = true;
            $latitude = $cords_arr['lat'];
            $longitude = $cords_arr['lng'];
            update_post_meta($post_id, 'jobsearch_field_location_lat', $latitude);
            update_post_meta($post_id, 'jobsearch_field_location_lng', $longitude);
        }
    }
    
    if (!$lat_lng_updted) {
        if ($jobsearch_candidates->can_update_meta('job_loclat', $import_options)) {
            update_post_meta($post_id, 'jobsearch_field_location_lat', $data['job_loclat']);
        }
        if ($jobsearch_candidates->can_update_meta('job_loclng', $import_options)) {
            update_post_meta($post_id, 'jobsearch_field_location_lng', $data['job_loclng']);
        }
    }
}
