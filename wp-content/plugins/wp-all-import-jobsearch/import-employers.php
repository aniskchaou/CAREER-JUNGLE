<?php

$jobsearch_employers = new RapidAddon('Wp Jobsearch', 'jobsearch_employers');

$jobsearch_employers->add_field('user_email', 'Email', 'text');
$jobsearch_employers->add_field('user_fimg', 'Image URL', 'text');
$jobsearch_employers->add_field('web_url', 'Website URL', 'text');
$jobsearch_employers->add_field('dob', 'Founded Date', 'text');
$jobsearch_employers->add_field('user_phone', 'Phone', 'text');
$jobsearch_employers->add_field('featured_emp', 'Featured Employer', 'radio', array('off' => 'No', 'on' => 'Yes'));
$jobsearch_employers->add_field('emp_status', 'Employer Status', 'radio', array('on' => 'Approved', 'off' => 'Pending'));

$job_custom_fields_saved_data = get_option('jobsearch_custom_field_employer');
if (is_array($job_custom_fields_saved_data) && sizeof($job_custom_fields_saved_data) > 0) {
    $field_names_counter = 0;
    foreach ($job_custom_fields_saved_data as $f_key => $custom_field_saved_data) {
        $cusfield_type = isset($custom_field_saved_data['type']) ? $custom_field_saved_data['type'] : '';
        $cusfield_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $cusfield_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
        if ($cusfield_label != '' && $cusfield_name != '') {
            $jobsearch_employers->add_field('cus_field_' . $cusfield_name, $cusfield_label, 'text');
        }
    }
}
$jobsearch_employers->add_field('user_facebook_url', 'Facebook URL', 'text');
$jobsearch_employers->add_field('user_twitter_url', 'Twitter URL', 'text');
$jobsearch_employers->add_field('user_gplus_url', 'Google Plus URL', 'text');
$jobsearch_employers->add_field('user_linkedin_url', 'Linkedin URL', 'text');
$jobsearch_employers->add_field('user_dribbble_url', 'Dribbble URL', 'text');
$employer_social_mlinks = isset($jobsearch__options['employer_social_mlinks']) ? $jobsearch__options['employer_social_mlinks'] : '';
if (!empty($employer_social_mlinks)) {
    if (isset($employer_social_mlinks['title']) && is_array($employer_social_mlinks['title'])) {
        $field_counter = 0;
        foreach ($employer_social_mlinks['title'] as $field_title_val) {
            $field_random = rand(10000000, 99999999);

            if ($field_title_val != '') {
                $dynm_social_name = 'employer_dynm_social' . ($field_counter);
                $jobsearch_employers->add_field($dynm_social_name, $field_title_val, 'text');
            }
            $field_counter++;
        }
    }
}

$jobsearch_employers->add_field('job_loc_contry', 'Country', 'text');
$jobsearch_employers->add_field('job_loc_state', 'State', 'text');
$jobsearch_employers->add_field('job_loc_city', 'City', 'text');
$jobsearch_employers->add_field('job_loc_address', 'Full Address', 'text');
$jobsearch_employers->add_field('job_loc_postcode', 'Postcode', 'text');
$jobsearch_employers->add_field('job_loclat', 'Latitude', 'text');
$jobsearch_employers->add_field('job_loclng', 'Longitude', 'text');

//
$jobsearch_employers->set_import_function('wp_jobsearch_employers_import');
// admin notice if WPAI and/or Wp Jobsearch isn't installed

if (function_exists('is_plugin_active')) {

    // display this notice if neither the free or pro version of the Wp Jobsearch plugin is active.
    if (!is_plugin_active("wp-jobsearch/wp-jobsearch.php")) {

        // Specify a custom admin notice.
        $jobsearch_employers->admin_notice(
                'The Wp Jobsearch requires WP All Import <a href="http://wordpress.org/plugins/wp-all-import" target="_blank">Free</a> and the <a href="#">Wp Jobsearch</a> plugin.'
        );
    }

    // only run this add-on if the free or pro version of the Wp Jobsearch plugin is active.
    if (is_plugin_active("wp-jobsearch/wp-jobsearch.php")) {

        $jobsearch_employers->run(
                array(
                    "post_types" => array("employer")
                )
        );
    }
}

function wp_jobsearch_employers_import($post_id, $data, $import_options) {

    global $jobsearch_employers;

    $jobsearch__options = get_option('jobsearch_plugin_options');

    if ($jobsearch_employers->can_update_meta('user_email', $import_options)) {
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
                    'role' => 'jobsearch_employer',
                );
                if (isset($data['web_url']) && $data['web_url'] != '') {
                    $user_def_array['user_url'] = $data['web_url'];
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
                update_user_meta($user_id, 'jobsearch_employer_id', $post_id);
            }
        }
    }

    if ($jobsearch_employers->can_update_meta('dob', $import_options)) {
        $emp_dob = $data['dob'];
        if ($emp_dob != '') {
            $emp_dob = strtotime($emp_dob);
            $emp_dob_dd = date('d', $emp_dob);
            $emp_dob_mm = date('m', $emp_dob);
            $emp_dob_yy = date('Y', $emp_dob);

            update_post_meta($post_id, 'jobsearch_field_user_dob_dd', $emp_dob_dd);
            update_post_meta($post_id, 'jobsearch_field_user_dob_mm', $emp_dob_mm);
            update_post_meta($post_id, 'jobsearch_field_user_dob_yy', $emp_dob_yy);
        }
    }
    if ($jobsearch_employers->can_update_meta('user_fimg', $import_options)) {
        $user_fimg = $data['user_fimg'];
        if ($user_fimg != '') {
            wp_jobsearch_uploadatt_post_with_external_url($user_fimg, $post_id, true);
        }
    }
    if ($jobsearch_employers->can_update_meta('user_phone', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_user_justphone', $data['user_phone']);
        update_post_meta($post_id, 'jobsearch_field_user_phone', $data['user_phone']);
    }
    if ($jobsearch_employers->can_update_meta('featured_emp', $import_options)) {
        $is_featured_emp = $data['featured_emp'];
        if ($is_featured_emp == 'on') {
            update_post_meta($post_id, '_feature_mber_frmadmin', 'yes');
            update_post_meta($post_id, 'jobsearch_field_feature_emp', 'on');
            update_post_meta($post_id, 'cusemp_feature_fbckend', 'on');
        } else {
            update_post_meta($post_id, '_feature_mber_frmadmin', 'no');
            update_post_meta($post_id, 'jobsearch_field_feature_emp', 'off');
        }
    }
    if ($jobsearch_employers->can_update_meta('emp_status', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_employer_approved', $data['emp_status']);
    }
    $job_custom_fields_saved_data = get_option('jobsearch_custom_field_employer');
    if (is_array($job_custom_fields_saved_data) && sizeof($job_custom_fields_saved_data) > 0) {
        $field_names_counter = 0;
        foreach ($job_custom_fields_saved_data as $f_key => $custom_field_saved_data) {
            $cusfield_type = isset($custom_field_saved_data['type']) ? $custom_field_saved_data['type'] : '';
            $cusfield_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
            $cusfield_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
            if ($cusfield_label != '' && $cusfield_name != '') {
                update_post_meta($post_id, $cusfield_name, $data['cus_field_' . $cusfield_name]);
            }
        }
    }
    if ($jobsearch_employers->can_update_meta('user_facebook_url', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_user_facebook_url', $data['user_facebook_url']);
    }
    if ($jobsearch_employers->can_update_meta('user_twitter_url', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_user_twitter_url', $data['user_twitter_url']);
    }
    if ($jobsearch_employers->can_update_meta('user_gplus_url', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_user_google_plus_url', $data['user_gplus_url']);
    }
    if ($jobsearch_employers->can_update_meta('user_linkedin_url', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_user_linkedin_url', $data['user_linkedin_url']);
    }
    if ($jobsearch_employers->can_update_meta('user_dribbble_url', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_user_dribbble_url', $data['user_dribbble_url']);
    }
    $employer_social_mlinks = isset($jobsearch__options['employer_social_mlinks']) ? $jobsearch__options['employer_social_mlinks'] : '';
    if (!empty($employer_social_mlinks)) {
        if (isset($employer_social_mlinks['title']) && is_array($employer_social_mlinks['title'])) {
            $field_counter = 0;
            foreach ($employer_social_mlinks['title'] as $field_title_val) {
                $field_random = rand(10000000, 99999999);

                if ($field_title_val != '' && $jobsearch_employers->can_update_meta('employer_dynm_social' . $field_counter, $import_options)) {
                    $dynm_social_name = 'jobsearch_field_dynm_social' . ($field_counter);
                    update_post_meta($post_id, $dynm_social_name, $data['employer_dynm_social' . $field_counter]);
                }
                $field_counter++;
            }
        }
    }

    $addres_str = '';
    $country_str = '';
    $city_str = '';
    if ($jobsearch_employers->can_update_meta('job_loc_contry', $import_options)) {
        $country_str = $data['job_loc_contry'];
        update_post_meta($post_id, 'jobsearch_field_location_location1', $data['job_loc_contry']);
    }
    if ($jobsearch_employers->can_update_meta('job_loc_state', $import_options)) {
        update_post_meta($post_id, 'jobsearch_field_location_location2', $data['job_loc_state']);
    }
    if ($jobsearch_employers->can_update_meta('job_loc_city', $import_options)) {
        $city_str = $data['job_loc_city'];
        update_post_meta($post_id, 'jobsearch_field_location_location3', $data['job_loc_city']);
    }
    if ($country_str != '' && $city_str != '') {
        $addres_str = $city_str. ', ' . $country_str;
    } else {
        $addres_str = $country_str;
    }
    if ($jobsearch_employers->can_update_meta('job_loc_address', $import_options)) {
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
        if ($jobsearch_employers->can_update_meta('job_loclat', $import_options)) {
            update_post_meta($post_id, 'jobsearch_field_location_lat', $data['job_loclat']);
        }
        if ($jobsearch_employers->can_update_meta('job_loclng', $import_options)) {
            update_post_meta($post_id, 'jobsearch_field_location_lng', $data['job_loclng']);
        }
    }
}
