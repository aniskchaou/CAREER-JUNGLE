<?php
/*
  Class : CustomFieldLoad
 */

use WP_Jobsearch\Package_Limits;

// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_CustomFieldLoad
{
// hook things up
    public function __construct()
    {
        // Save custom fields
        add_action('jobsearch_custom_fields_load', array($this, 'jobsearch_custom_fields_load_callback'), 1, 2);
        add_filter('jobsearch_custom_field_text_load', array($this, 'jobsearch_custom_field_text_load_callback'), 1, 4);
        add_filter('jobsearch_custom_field_video_load', array($this, 'jobsearch_custom_field_video_load_callback'), 1, 4);
        add_filter('jobsearch_custom_field_linkurl_load', array($this, 'jobsearch_custom_field_linkurl_load_callback'), 1, 4);
        add_filter('jobsearch_custom_field_upload_file_load', array($this, 'jobsearch_custom_field_upload_file_load_callback'), 1, 4);
        add_filter('jobsearch_custom_field_checkbox_load', array($this, 'jobsearch_custom_field_checkbox_load_callback'), 1, 4);
        add_filter('jobsearch_custom_field_dropdown_load', array($this, 'jobsearch_custom_field_dropdown_load_callback'), 1, 4);
        add_filter('jobsearch_custom_field_dependent_dropdown_load', array($this, 'jobsearch_custom_field_dependent_dropdown_load_callback'), 1, 4);
        add_filter('jobsearch_custom_field_heading_load', array($this, 'jobsearch_custom_field_heading_load_callback'), 1, 4);
        add_filter('jobsearch_custom_field_textarea_load', array($this, 'jobsearch_custom_field_textarea_load_callback'), 1, 4);
        add_filter('jobsearch_custom_field_email_load', array($this, 'jobsearch_custom_field_email_load_callback'), 1, 4);
        add_filter('jobsearch_custom_field_number_load', array($this, 'jobsearch_custom_field_number_load_callback'), 1, 4);
        add_filter('jobsearch_custom_field_date_load', array($this, 'jobsearch_custom_field_date_load_callback'), 1, 4);
        add_filter('jobsearch_custom_field_range_load', array($this, 'jobsearch_custom_field_range_load_callback'), 1, 4);

        // For frontend dashboard custom fields
        add_action('jobsearch_dashboard_custom_fields_load', array($this, 'jobsearch_dashboard_custom_fields_load_callback'), 1, 2);
        add_filter('jobsearch_dashboard_custom_field_text_load', array($this, 'jobsearch_dashboard_custom_field_text_load_callback'), 1, 5);
        add_filter('jobsearch_dashboard_custom_field_video_load', array($this, 'jobsearch_dashboard_custom_field_video_load_callback'), 1, 5);
        add_filter('jobsearch_dashboard_custom_field_linkurl_load', array($this, 'jobsearch_dashboard_custom_field_linkurl_load_callback'), 1, 5);
        add_filter('jobsearch_dashboard_custom_field_upload_file_load', array($this, 'jobsearch_dashboard_custom_field_upload_file_load_callback'), 1, 4);
        add_filter('jobsearch_dashboard_custom_field_checkbox_load', array($this, 'jobsearch_dashboard_custom_field_checkbox_load_callback'), 1, 5);
        add_filter('jobsearch_dashboard_custom_field_dropdown_load', array($this, 'jobsearch_dashboard_custom_field_dropdown_load_callback'), 1, 5);
        add_filter('jobsearch_dashboard_custom_field_dependent_dropdown_load', array($this, 'jobsearch_dashboard_custom_field_dependent_dropdown_load_callback'), 1, 5);
        add_filter('jobsearch_dashboard_custom_field_heading_load', array($this, 'jobsearch_dashboard_custom_field_heading_load_callback'), 1, 5);
        add_filter('jobsearch_dashboard_custom_field_textarea_load', array($this, 'jobsearch_dashboard_custom_field_textarea_load_callback'), 1, 5);
        add_filter('jobsearch_dashboard_custom_field_email_load', array($this, 'jobsearch_dashboard_custom_field_email_load_callback'), 1, 5);
        add_filter('jobsearch_dashboard_custom_field_number_load', array($this, 'jobsearch_dashboard_custom_field_number_load_callback'), 1, 5);
        add_filter('jobsearch_dashboard_custom_field_date_load', array($this, 'jobsearch_dashboard_custom_field_date_load_callback'), 1, 5);
        add_filter('jobsearch_dashboard_custom_field_range_load', array($this, 'jobsearch_dashboard_custom_field_range_load_callback'), 1, 5);
        //
        // For simple form custom fields
        add_action('jobsearch_form_custom_fields_load', array($this, 'jobsearch_form_custom_fields_load_callback'), 1, 3);
        add_action('jobsearch_signup_custom_fields_load', array($this, 'jobsearch_signup_custom_fields_load_callback'), 1, 3);
        add_action('jobsearch_register_custom_fields_error', array($this, 'register_custom_fields_error'), 10, 2);

        add_filter('jobsearch_form_custom_field_text_load', array($this, 'jobsearch_form_custom_field_text_load_callback'), 1, 7);
        add_filter('jobsearch_form_custom_field_video_load', array($this, 'jobsearch_form_custom_field_video_load_callback'), 1, 7);
        add_filter('jobsearch_form_custom_field_linkurl_load', array($this, 'jobsearch_form_custom_field_linkurl_load_callback'), 1, 7);
        add_filter('jobsearch_form_custom_field_upload_file_load', array($this, 'jobsearch_form_custom_field_upload_file_load_callback'), 1, 7);
        add_filter('jobsearch_form_custom_field_checkbox_load', array($this, 'jobsearch_form_custom_field_checkbox_load_callback'), 1, 7);
        add_filter('jobsearch_form_custom_field_dropdown_load', array($this, 'jobsearch_form_custom_field_dropdown_load_callback'), 1, 7);
        add_filter('jobsearch_form_custom_field_dependent_dropdown_load', array($this, 'jobsearch_form_custom_field_dependent_dropdown_load_callback'), 1, 7);
        add_filter('jobsearch_form_custom_field_heading_load', array($this, 'jobsearch_form_custom_field_heading_load_callback'), 1, 7);
        add_filter('jobsearch_form_custom_field_textarea_load', array($this, 'jobsearch_form_custom_field_textarea_load_callback'), 1, 7);
        add_filter('jobsearch_form_custom_field_email_load', array($this, 'jobsearch_form_custom_field_email_load_callback'), 1, 7);
        add_filter('jobsearch_form_custom_field_number_load', array($this, 'jobsearch_form_custom_field_number_load_callback'), 1, 7);
        add_filter('jobsearch_form_custom_field_date_load', array($this, 'jobsearch_form_custom_field_date_load_callback'), 1, 7);
        add_filter('jobsearch_form_custom_field_range_load', array($this, 'jobsearch_form_custom_field_range_load_callback'), 1, 7);
        //
        // For translate custom fields
        add_action('init', array($this, 'custom_fields_translation'), 10);
        add_action('jobsearch_custom_field_heading_translate', array($this, 'jobsearch_custom_field_heading_translate'), 10, 1);
        add_action('jobsearch_custom_field_text_translate', array($this, 'jobsearch_custom_field_text_translate'), 10, 1);
        add_action('jobsearch_custom_field_video_translate', array($this, 'jobsearch_custom_field_video_translate'), 10, 1);
        add_action('jobsearch_custom_field_linkurl_translate', array($this, 'jobsearch_custom_field_linkurl_translate'), 10, 1);
        add_action('jobsearch_custom_field_email_translate', array($this, 'jobsearch_custom_field_email_translate'), 10, 1);
        add_action('jobsearch_custom_field_textarea_translate', array($this, 'jobsearch_custom_field_textarea_translate'), 10, 1);
        add_action('jobsearch_custom_field_date_translate', array($this, 'jobsearch_custom_field_date_translate'), 10, 1);
        add_action('jobsearch_custom_field_number_translate', array($this, 'jobsearch_custom_field_number_translate'), 10, 1);
        add_action('jobsearch_custom_field_range_translate', array($this, 'jobsearch_custom_field_range_translate'), 10, 1);
        add_action('jobsearch_custom_field_dropdown_translate', array($this, 'jobsearch_custom_field_dropdown_translate'), 10, 1);
        add_action('jobsearch_custom_field_dependent_dropdown_translate', array($this, 'jobsearch_custom_field_dependent_dropdown_translate'), 10, 1);
        add_action('jobsearch_custom_field_salary_translate', array($this, 'jobsearch_custom_field_salary_translate'), 10, 1);
        //
        // Save custom fields values to duplicate post
        add_action('jobsearch_dashboard_pass_values_to_duplicate_post', array($this, 'pass_values_to_duplicate_post'), 10, 3);
        //
        // Save cus fields upload files
        add_action('jobsearch_custom_field_upload_files_save', array($this, 'cus_fields_upload_files_save'), 10, 2);

        add_filter('jobsearch_custom_fields_list', array($this, 'jobsearch_custom_fields_list_callback'), 11, 12);
        add_filter('jobsearch_custom_fields_filter_box_html', array($this, 'jobsearch_custom_fields_filter_box_html_callback'), 1, 7);
        add_filter('jobsearch_custom_fields_filter_box_quick_detail_html', array($this, 'jobsearch_custom_fields_filter_box_quick_apply_html_callback'), 1, 6);
        add_filter('jobsearch_custom_fields_filter_box_quick_detail_html_mob', array($this, 'jobsearch_custom_fields_filter_box_quick_apply_mob_html_callback'), 1, 6);
        add_filter('jobsearch_custom_fields_top_filters_html', array($this, 'custom_fields_top_filter_box_html_callback'), 1, 4);
        add_filter('jobsearch_custom_fields_load_filter_array_html', array($this, 'jobsearch_custom_fields_load_filter_array_html_callback'), 1, 4);
        add_filter('jobsearch_custom_fields_load_precentage_array', array($this, 'jobsearch_custom_fields_load_precentage_array_callback'), 1, 2);

        // Save custom fields values in signup form
        add_action('jobsearch_signup_custom_fields_save', array($this, 'signup_custom_fields_save'), 10, 2);
        //
    }

    public function signup_custom_fields_save($custom_field_entity, $post_id)
    {
        // load all saved fields
        $field_db_slug = "jobsearch_custom_field_" . $custom_field_entity;
        $custom_all_fields_saved_data = get_option($field_db_slug);

        if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0) {

            foreach ($custom_all_fields_saved_data as $f_key => $custom_field_saved_data) {

                $field_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
                if ($field_name != '' && isset($_POST[$field_name])) {
                    if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == 'dependent_field') {
                        foreach ($_POST as $post_key => $post_value) {
                            if (strstr($post_key, $field_name)) {
                                update_post_meta($post_id, $post_key, $post_value);
                            }
                        }
                        update_post_meta($post_id, $field_name, $_POST[$field_name]);
                    } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == 'date') {
                        $date_value = $_POST[$field_name];
                        $date_value = $date_value != '' ? strtotime($date_value) : '';
                        update_post_meta($post_id, $field_name, $date_value);
                    } else {
                        update_post_meta($post_id, $field_name, $_POST[$field_name]);
                    }
                }
            }
        }
    }

    public function cus_fields_upload_files_save($post_id, $custom_field_entity)
    {
        // load all saved fields
        $field_db_slug = "jobsearch_custom_field_" . $custom_field_entity;
        $custom_all_fields_saved_data = get_option($field_db_slug);

        if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0) {
            foreach ($custom_all_fields_saved_data as $f_key => $custom_field_saved_data) {
                $field_type = isset($custom_field_saved_data['type']) ? $custom_field_saved_data['type'] : '';

                if ($field_type == 'upload_file') {
                    $field_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
                    $upload_field_multifiles = isset($custom_field_saved_data['multi_files']) ? $custom_field_saved_data['multi_files'] : '';
                    $upload_field_numof_files = isset($custom_field_saved_data['numof_files']) ? $custom_field_saved_data['numof_files'] : '';
                    $upload_field_numof_files = $upload_field_numof_files > 0 ? $upload_field_numof_files : 5;
                    $upload_field_allow_types = isset($custom_field_saved_data['allow_types']) ? $custom_field_saved_data['allow_types'] : '';
                    $upload_field_allow_types = !empty($upload_field_allow_types) ? $upload_field_allow_types : array();
                    $upload_field_file_size = isset($custom_field_saved_data['file_size']) ? $custom_field_saved_data['file_size'] : '';
                    $upload_field_file_size = $upload_field_file_size == '' ? '5MB' : $upload_field_file_size;

                    if ($upload_field_multifiles != 'yes') {
                        $upload_field_numof_files = 1;
                    }

                    $gal_ids_arr = array();

                    $max_gal_imgs_allow = $upload_field_numof_files;

                    $post_files_name = 'jobsearch_cfupfiles_' . $field_name;

                    if (isset($_POST[$post_files_name]) && !empty($_POST[$post_files_name])) {
                        $gal_ids_arr = array_merge($gal_ids_arr, $_POST[$post_files_name]);
                    }
                    $gal_imgs_count = 0;
                    if (!empty($gal_ids_arr)) {
                        $gal_imgs_count += sizeof($gal_ids_arr);
                    }

                    $gall_ids = jobsearch_cus_fields_attachments_upload($field_name, $gal_imgs_count, $upload_field_numof_files, $upload_field_allow_types, $upload_field_file_size);
                    if (!empty($gall_ids)) {
                        $gal_ids_arr = array_merge($gal_ids_arr, $gall_ids);
                    }

                    update_post_meta($post_id, $post_files_name, $gal_ids_arr);
                }
            }
        }
    }

    public function pass_values_to_duplicate_post($post_id, $duplicate_post_id, $custom_field_entity)
    {
        // load all saved fields
        $field_db_slug = "jobsearch_custom_field_" . $custom_field_entity;
        $custom_all_fields_saved_data = get_option($field_db_slug);

        if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0) {

            foreach ($custom_all_fields_saved_data as $f_key => $custom_field_saved_data) {

                $field_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
                if ($field_name != '') {
                    $field_name_db_val = get_post_meta($post_id, $field_name, true);
                    update_post_meta($duplicate_post_id, $field_name, $field_name_db_val);
                }
            }
        }
    }

    public function custom_fields_translation()
    {
        $custom_field_entities = array('job', 'candidate', 'employer');

        foreach ($custom_field_entities as $custom_field_entity) {
            $field_db_slug = "jobsearch_custom_field_" . $custom_field_entity;
            $custom_all_fields_saved_data = get_option($field_db_slug);
            $count_node = time();
            $all_fields_name_str = '';
            if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0) {
                $field_names_counter = 0;
                foreach ($custom_all_fields_saved_data as $f_key => $custom_field_saved_data) {

                    if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "heading") {
                        do_action('jobsearch_custom_field_heading_translate', $custom_field_saved_data);
                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "text") {
                        do_action('jobsearch_custom_field_text_translate', $custom_field_saved_data);
                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "video") {
                        do_action('jobsearch_custom_field_video_translate', $custom_field_saved_data);
                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "linkurl") {
                        do_action('jobsearch_custom_field_linkurl_translate', $custom_field_saved_data);
                    } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "email") {
                        do_action('jobsearch_custom_field_email_translate', $custom_field_saved_data);
                    } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "textarea") {
                        do_action('jobsearch_custom_field_textarea_translate', $custom_field_saved_data);
                    } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "date") {
                        do_action('jobsearch_custom_field_date_translate', $custom_field_saved_data);
                    } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "number") {
                        do_action('jobsearch_custom_field_number_translate', $custom_field_saved_data);
                    } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "range") {
                        do_action('jobsearch_custom_field_range_translate', $custom_field_saved_data);
                    } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "dropdown") {
                        do_action('jobsearch_custom_field_dropdown_translate', $custom_field_saved_data);
                    } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "dependent_dropdown") {
                        do_action('jobsearch_custom_field_dependent_dropdown_translate', $custom_field_saved_data);
                    } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "salary") {
                        do_action('jobsearch_custom_field_salary_translate', $custom_field_saved_data);
                    }
                }
            }
        }
    }

    public function jobsearch_custom_field_salary_translate($custom_field_saved_data)
    {

        global $sitepress;
        $text_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Salary Label - ' . $text_field_label, $text_field_label);
    }

    public function jobsearch_custom_field_text_translate($custom_field_saved_data)
    {

        global $sitepress;

        $text_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $text_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Text Field Label - ' . $text_field_label, $text_field_label);
        do_action('wpml_register_single_string', 'Custom Fields', 'Text Field Placeholder - ' . $text_field_placeholder, $text_field_placeholder);
    }

    public function jobsearch_custom_field_video_translate($custom_field_saved_data)
    {

        global $sitepress;

        $video_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $video_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Video Field Label - ' . $video_field_label, $video_field_label);
        do_action('wpml_register_single_string', 'Custom Fields', 'Video Field Placeholder - ' . $video_field_placeholder, $video_field_placeholder);
    }

    public function jobsearch_custom_field_linkurl_translate($custom_field_saved_data)
    {

        global $sitepress;

        $linkurl_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $linkurl_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'URL Field Label - ' . $linkurl_field_label, $linkurl_field_label);
        do_action('wpml_register_single_string', 'Custom Fields', 'URL Field Placeholder - ' . $linkurl_field_placeholder, $linkurl_field_placeholder);
    }

    public function jobsearch_custom_field_email_translate($custom_field_saved_data)
    {

        global $sitepress;

        $email_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $email_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Email Field Label - ' . $email_field_label, $email_field_label);
        do_action('wpml_register_single_string', 'Custom Fields', 'Email Field Placeholder - ' . $email_field_placeholder, $email_field_placeholder);
    }

    public function jobsearch_custom_field_number_translate($custom_field_saved_data)
    {

        global $sitepress;

        $number_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $number_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Number Field Label - ' . $number_field_label, $number_field_label);
        do_action('wpml_register_single_string', 'Custom Fields', 'Number Field Placeholder - ' . $number_field_placeholder, $number_field_placeholder);
    }

    public function jobsearch_custom_field_date_translate($custom_field_saved_data)
    {

        global $sitepress;

        $date_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $date_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Date Field Label - ' . $date_field_label, $date_field_label);
        do_action('wpml_register_single_string', 'Custom Fields', 'Date Field Placeholder - ' . $date_field_placeholder, $date_field_placeholder);
    }

    public function jobsearch_custom_field_range_translate($custom_field_saved_data)
    {

        global $sitepress;
        $range_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $range_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Range Field Label - ' . $range_field_label, $range_field_label);
        do_action('wpml_register_single_string', 'Custom Fields', 'Range Field Placeholder - ' . $range_field_placeholder, $range_field_placeholder);
    }

    public function jobsearch_custom_field_dropdown_translate($custom_field_saved_data)
    {
        global $sitepress;
        $dropdown_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $dropdown_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $dropdown_field_options = isset($custom_field_saved_data['options']) ? $custom_field_saved_data['options'] : '';
        if (isset($dropdown_field_options['value']) && count($dropdown_field_options['value']) > 0) {
            $option_counter = 0;
            foreach ($dropdown_field_options['value'] as $option) {
                if ($option != '') {
                    $option = ltrim(rtrim($option));
                    if ($dropdown_field_options['label'][$option_counter] != '' && str_replace(" ", "-", $option) != '') {
                        $option_label = $dropdown_field_options['label'][$option_counter];
                        $option_label = stripslashes($option_label);

                        $lang_code = '';
                        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                            $lang_code = $sitepress->get_current_language();
                        }
                        do_action('wpml_register_single_string', 'Custom Fields', 'Dropdown Option Label - ' . $option_label, $option_label);
                    }
                }
                $option_counter++;
            }
        }

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Dropdown Field Label - ' . $dropdown_field_label, $dropdown_field_label);
        do_action('wpml_register_single_string', 'Custom Fields', 'Dropdown Field Placeholder - ' . $dropdown_field_placeholder, $dropdown_field_placeholder);
    }

    public function jobsearch_custom_field_dependent_dropdown_translate($custom_field_saved_data)
    {
        $dropdown_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $dropdown_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $dropdown_field_options = isset($custom_field_saved_data['options_list']) ? $custom_field_saved_data['options_list'] : '';
        $dropdown_cont_optsid = isset($custom_field_saved_data['options_list_id']) && $custom_field_saved_data['options_list_id'] != '' ? $custom_field_saved_data['options_list_id'] : 0;

        if (isset($dropdown_field_options[0]['label']) && !empty($dropdown_field_options[0]['label']) && is_array($dropdown_field_options[0]['label']) && sizeof($dropdown_field_options[0]['label']) > 0) {

            $field_count = 0;
            foreach ($dropdown_field_options as $opt_field_key => $opt_field_list) {
                $field_group_label = isset($opt_field_list['group_label']) ? $opt_field_list['group_label'] : '';
                $field_labels = isset($opt_field_list['label']) ? $opt_field_list['label'] : '';

                do_action('wpml_register_single_string', 'Custom Fields', 'Dependent Dropdown Field Group Label - ' . $field_group_label, $field_group_label);

                if (!empty($field_labels) && is_array($field_labels)) {
                    $sin_field_count = 0;
                    foreach ($field_labels as $sin_field_label) {

                        do_action('wpml_register_single_string', 'Custom Fields', 'Dependent Dropdown Field Label - ' . $sin_field_label, $sin_field_label);

                        $sin_field_count++;
                    }
                }

                $field_count++;
            }

        }

        do_action('wpml_register_single_string', 'Custom Fields', 'Dropdown Field Label - ' . $dropdown_field_label, $dropdown_field_label);
        do_action('wpml_register_single_string', 'Custom Fields', 'Dropdown Field Placeholder - ' . $dropdown_field_placeholder, $dropdown_field_placeholder);
    }

    public function jobsearch_custom_field_textarea_translate($custom_field_saved_data)
    {

        global $sitepress;

        $textarea_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $textarea_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Textarea Field Label - ' . $textarea_field_label, $textarea_field_label);
        do_action('wpml_register_single_string', 'Custom Fields', 'Textarea Field Placeholder - ' . $textarea_field_placeholder, $textarea_field_placeholder);
    }

    public function jobsearch_custom_field_heading_translate($custom_field_saved_data)
    {

        global $sitepress;

        $heading_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Heading Field Label - ' . $heading_field_label, $heading_field_label);
    }

    static function jobsearch_custom_fields_load_callback($post_id, $custom_field_entity)
    {
        global $custom_field_posttype;

        $custom_field_posttype = $custom_field_entity;
        // load all saved fields
        $field_db_slug = "jobsearch_custom_field_" . $custom_field_entity;
        $custom_all_fields_saved_data = get_option($field_db_slug);
        $count_node = time();
        $all_fields_name_str = '';
        if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0) {
            $field_names_counter = 0;
            $fields_prefix = '';
            $output = '';
            foreach ($custom_all_fields_saved_data as $f_key => $custom_field_saved_data) {

                if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "heading") {
                    $output .= apply_filters('jobsearch_custom_field_heading_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "text") {
                    $output .= apply_filters('jobsearch_custom_field_text_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "linkurl") {
                    $output .= apply_filters('jobsearch_custom_field_linkurl_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "video") {
                    $output .= apply_filters('jobsearch_custom_field_video_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "upload_file") {
                    $output .= apply_filters('jobsearch_custom_field_upload_file_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "email") {
                    $output .= apply_filters('jobsearch_custom_field_email_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "textarea") {
                    $output .= apply_filters('jobsearch_custom_field_textarea_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "date") {
                    $output .= apply_filters('jobsearch_custom_field_date_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "number") {
                    $output .= apply_filters('jobsearch_custom_field_number_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "range") {
                    $output .= apply_filters('jobsearch_custom_field_range_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "checkbox") {
                    $output .= apply_filters('jobsearch_custom_field_checkbox_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "dropdown") {
                    $output .= apply_filters('jobsearch_custom_field_dropdown_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "dependent_dropdown") {
                    $output .= apply_filters('jobsearch_custom_field_dependent_dropdown_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "dependent_fields") {
                    $output .= apply_filters('jobsearch_custom_field_dependent_fields_load', '', $post_id, $custom_field_saved_data, $fields_prefix, $f_key, 'admin');
                }
            }
            $output .= apply_filters('jobsearch_custom_fields_load_after', '', $post_id, $custom_field_entity);
            echo force_balance_tags($output);
        }
    }

    static function jobsearch_custom_field_text_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix)
    {

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $text_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $text_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $text_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $text_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $text_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $text_field_required_str = '';
        if ($text_field_required == 'yes') {
            $text_field_required_str = 'required="required"';
        }
        // get db value if saved
        $text_field_name_db_val = get_post_meta($post_id, $text_field_name, true);
        $text_field_name_db_val = jobsearch_esc_html($text_field_name_db_val);
        ?>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php echo jobsearch_esc_html($text_field_label) ?></label>
            </div>
            <div class="elem-field">
                <input type="text" name="<?php echo jobsearch_esc_html($text_field_name) ?>"
                       class="<?php echo jobsearch_esc_html($text_field_classes) ?>"
                       placeholder="<?php echo jobsearch_esc_html($text_field_placeholder) ?>" <?php echo force_balance_tags($text_field_required_str) ?>
                       value="<?php echo jobsearch_esc_html($text_field_name_db_val) ?>"/>
            </div>
        </div>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_custom_field_video_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix)
    {

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $video_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $video_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $video_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $video_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $video_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $video_field_required_str = '';
        if ($video_field_required == 'yes') {
            $video_field_required_str = 'required="required"';
        }
        // get db value if saved
        $video_field_name_db_val = get_post_meta($post_id, $video_field_name, true);
        $video_field_name_db_val = jobsearch_esc_html($video_field_name_db_val);
        ?>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php echo jobsearch_esc_html($video_field_label) ?></label>
            </div>
            <div class="elem-field">
                <input type="text" name="<?php echo jobsearch_esc_html($video_field_name) ?>"
                       class="<?php echo jobsearch_esc_html($video_field_classes) ?>"
                       placeholder="<?php echo jobsearch_esc_html($video_field_placeholder) ?>" <?php echo force_balance_tags($video_field_required_str) ?>
                       value="<?php echo jobsearch_esc_html($video_field_name_db_val) ?>"/>
            </div>
        </div>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_custom_field_linkurl_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix)
    {

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $linkurl_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $linkurl_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $linkurl_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $linkurl_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $linkurl_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $linkurl_field_required_str = '';
        if ($linkurl_field_required == 'yes') {
            $linkurl_field_required_str = 'required="required"';
        }
        // get db value if saved
        $linkurl_field_name_db_val = get_post_meta($post_id, $linkurl_field_name, true);
        $linkurl_field_name_db_val = jobsearch_esc_html($linkurl_field_name_db_val);
        ?>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php echo jobsearch_esc_html($linkurl_field_label) ?></label>
            </div>
            <div class="elem-field">
                <input type="text" name="<?php echo jobsearch_esc_html($linkurl_field_name) ?>"
                       class="<?php echo jobsearch_esc_html($linkurl_field_classes) ?>"
                       placeholder="<?php echo jobsearch_esc_html($linkurl_field_placeholder) ?>" <?php echo force_balance_tags($linkurl_field_required_str) ?>
                       value="<?php echo jobsearch_esc_html($linkurl_field_name_db_val) ?>"/>
            </div>
        </div>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_custom_field_upload_file_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix)
    {

        global $sitepress;

        $rand_num = rand(10000000, 99999999);

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $upload_file_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $upload_file_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $upload_file_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $upload_file_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';

        $upload_field_multifiles = isset($custom_field_saved_data['multi_files']) ? $custom_field_saved_data['multi_files'] : '';
        $upload_field_numof_files = isset($custom_field_saved_data['numof_files']) ? $custom_field_saved_data['numof_files'] : '';
        $upload_field_numof_files = $upload_field_numof_files > 0 ? $upload_field_numof_files : 5;
        $upload_field_allow_types = isset($custom_field_saved_data['allow_types']) ? $custom_field_saved_data['allow_types'] : '';
        $upload_field_allow_types = !empty($upload_field_allow_types) ? $upload_field_allow_types : array();
        $upload_field_file_size = isset($custom_field_saved_data['file_size']) ? $custom_field_saved_data['file_size'] : '';
        $upload_field_file_size = $upload_field_file_size == '' ? '5MB' : $upload_field_file_size;

        $upload_file_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $upload_file_field_required_str = '';
        if ($upload_file_field_required == 'yes') {
            $upload_file_field_required_str = 'required="required"';
            $upload_file_field_label = $upload_file_field_label . ' *';
        }
        // get db value if saved
        $upload_file_field_name_db_val = get_post_meta($post_id, $upload_file_field_name, true);

        if ($upload_field_multifiles != 'yes') {
            $upload_field_numof_files = 1;
        }

        $uplod_file_size_num = abs((int)filter_var($upload_field_file_size, FILTER_SANITIZE_NUMBER_INT));
        $uplod_file_size_num = $uplod_file_size_num > 0 ? $uplod_file_size_num : 5;
        $uplod_file_size = $uplod_file_size_num * 1024;
        ?>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php echo jobsearch_esc_html($upload_file_field_label) ?></label>
            </div>
            <div class="elem-field">
                <div class="jobsearch-fileUpload">
                    <input id="att-upload-files-<?php echo($rand_num) ?>" type="button" class="upload jobsearch-upload"
                           value="<?php esc_html_e('Upload Files', 'wp-jobsearch') ?>"/>
                </div>
                <div id="field-files-holder-<?php echo($rand_num) ?>" class="uplodfield-files-holder">
                    <?php
                    $post_files_name = 'jobsearch_cfupfiles_' . $upload_file_field_name;

                    $all_attach_files = get_post_meta($post_id, $post_files_name, true);
                    //var_dump($all_attach_files);
                    if (!empty($all_attach_files)) {
                        ?>
                        <ul>
                            <?php
                            foreach ($all_attach_files as $_attach_file) {
                                $_attach_id = jobsearch_get_attachment_id_from_url($_attach_file);
                                if ($_attach_id > 0) {
                                    $_attach_post = get_post($_attach_id);
                                    $_attach_mime = isset($_attach_post->post_mime_type) ? $_attach_post->post_mime_type : '';
                                    $_attach_guide = isset($_attach_post->guid) ? $_attach_post->guid : '';
                                    $attach_name = basename($_attach_guide);
                                    $file_icon = 'fa fa-file-text-o';
                                    if ($_attach_mime == 'image/png' || $_attach_mime == 'image/jpeg') {
                                        $file_icon = 'fa fa-file-image-o';
                                    } else if ($_attach_mime == 'application/msword' || $_attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                        $file_icon = 'fa fa-file-word-o';
                                    } else if ($_attach_mime == 'application/vnd.ms-excel' || $_attach_mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                        $file_icon = 'fa fa-file-excel-o';
                                    } else if ($_attach_mime == 'application/pdf') {
                                        $file_icon = 'fa fa-file-pdf-o';
                                    }
                                    ?>
                                    <li class="jobsearch-column-3">
                                        <a href="javascript:void(0);"
                                           class="fa fa-remove el-remove elback-remove-<?php echo($rand_num) ?>"></a>
                                        <div class="file-container">
                                            <a href="<?php echo($_attach_file) ?>"
                                               oncontextmenu="javascript: return false;"
                                               onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                               download="<?php echo($attach_name) ?>"><i
                                                        class="<?php echo($file_icon) ?>"></i> <?php echo($attach_name) ?>
                                                <?php
                                                if ($_attach_mime == 'image/png' || $_attach_mime == 'image/jpeg') {
                                                    $attach_file_url = str_replace(ABSPATH, get_site_url() . '/', $_attach_guide);
                                                    ?>
                                                    <br>
                                                    <img src="<?php echo($attach_file_url) ?>" alt=""
                                                         style="max-width: 100%;">
                                                    <?php
                                                }
                                                ?>
                                            </a>
                                        </div>
                                        <input type="hidden" name="<?php echo($post_files_name) ?>[]"
                                               value="<?php echo($_attach_file) ?>">
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <script type="text/javascript">
                jQuery('#att-upload-files-<?php echo($rand_num) ?>').click(function (e) { // job attachment
                    e.preventDefault();
                    mediaUploader = wp.media.frames.file_frame = wp.media({
                        title: 'Choose File',
                        button: {
                            text: 'Choose File'
                        }, multiple: true
                    });
                    mediaUploader.on('select', function () {
                        var attachment = mediaUploader.state().get('selection').toJSON();
                        attachment.map(function (attachment) {
                            var file_icon = 'fa fa-file-text-o';
                            if (attachment.type == 'image/png' || attachment.type == 'image/jpeg') {
                                file_icon = 'fa fa-file-image-o';
                            } else if (attachment.type == 'application/msword' || attachment.subtype == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                file_icon = 'fa fa-file-word-o';
                            } else if (attachment.type == 'application/vnd.ms-excel' || attachment.subtype == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                file_icon = 'fa fa-file-excel-o';
                            } else if (attachment.type == 'application/pdf') {
                                file_icon = 'fa fa-file-pdf-o';
                            }
                            var ihtml = '\
                                    <div class="jobsearch-column-3">\
                                        <a href="javascript:void(0);" class="fa fa-remove el-remove elback-remove-<?php echo($rand_num) ?>"></a>\
                                        <div class="file-container">\
                                            <a><i class="' + file_icon + '"></i> ' + attachment.filename + '</a>\
                                        </div>\
                                        <input type="hidden" name="<?php echo($post_files_name) ?>[]" value="' + attachment.url + '">\
                                    </div>';
                            jQuery('#field-files-holder-<?php echo($rand_num) ?>').append(ihtml);
                        });
                    });
                    mediaUploader.open();
                });
                //
                jQuery(document).on('click', '.elback-remove-<?php echo($rand_num) ?>', function () {
                    if (jQuery(this).parent('div').length > 0) {
                        jQuery(this).parent('div').remove();
                    }
                    if (jQuery(this).parent('li').length > 0) {
                        jQuery(this).parent('li').remove();
                    }
                });
            </script>
        </div>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_custom_field_email_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix)
    {
        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $email_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $email_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $email_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $email_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $email_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $email_field_required_str = '';
        if ($email_field_required == 'yes') {
            $email_field_required_str = 'required="required"';
        }
        // get db value if saved
        $email_field_name_db_val = get_post_meta($post_id, $email_field_name, true);
        $email_field_name_db_val = jobsearch_esc_html($email_field_name_db_val);
        ?>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php echo jobsearch_esc_html($email_field_label) ?></label>
            </div>
            <div class="elem-field">
                <input type="email" name="<?php echo jobsearch_esc_html($email_field_name) ?>"
                       class="<?php echo jobsearch_esc_html($email_field_classes) ?>"
                       placeholder="<?php echo jobsearch_esc_html($email_field_placeholder) ?>" <?php echo force_balance_tags($email_field_required_str) ?>
                       value="<?php echo jobsearch_esc_html($email_field_name_db_val) ?>"/>
            </div>
        </div>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_custom_field_number_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix)
    {
        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $number_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $number_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $number_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $number_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $number_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $number_field_required_str = '';
        if ($number_field_required == 'yes') {
            $number_field_required_str = 'required="required"';
        }
        // get db value if saved
        $number_field_name_db_val = get_post_meta($post_id, $number_field_name, true);
        $number_field_name_db_val = jobsearch_esc_html($number_field_name_db_val);
        ?>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php echo jobsearch_esc_html($number_field_label) ?></label>
            </div>
            <div class="elem-field">
                <input type="number" name="<?php echo jobsearch_esc_html($number_field_name) ?>"
                       class="<?php echo jobsearch_esc_html($number_field_classes) ?>"
                       placeholder="<?php echo jobsearch_esc_html($number_field_placeholder) ?>" <?php echo force_balance_tags($number_field_required_str) ?>
                       value="<?php echo jobsearch_esc_html($number_field_name_db_val) ?>"/>
            </div>
        </div>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_custom_field_date_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix)
    {
        ob_start();
        $field_rand_id = rand(454, 999999);
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $date_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $date_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $date_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $date_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $date_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $date_field_date_format = isset($custom_field_saved_data['date-format']) && $custom_field_saved_data['date-format'] != '' ? $custom_field_saved_data['date-format'] : 'd-m-Y';
        $date_field_required_str = '';
        if ($date_field_required == 'yes') {
            $date_field_required_str = 'required="required"';
        }
        // get db value if saved
        $date_field_name_db_val = get_post_meta($post_id, $date_field_name, true);
        $date_field_name_db_val = jobsearch_esc_html($date_field_name_db_val);
        if ($date_field_name_db_val != '') {
            $date_field_name_db_val = date($date_field_date_format, $date_field_name_db_val);
        }
        ?>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php echo jobsearch_esc_html($date_field_label) ?></label>
            </div>
            <div class="elem-field">
                <input type="text" id="<?php echo jobsearch_esc_html($date_field_name . $field_rand_id) ?>"
                       name="<?php echo jobsearch_esc_html($date_field_name) ?>"
                       class="<?php echo jobsearch_esc_html($date_field_classes) ?>"
                       placeholder="<?php echo jobsearch_esc_html($date_field_placeholder) ?>" <?php echo force_balance_tags($date_field_required_str) ?>
                       value="<?php echo jobsearch_esc_html($date_field_name_db_val) ?>"/>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery('#<?php echo jobsearch_esc_html($date_field_name . $field_rand_id) ?>').datetimepicker({
                    format: '<?php echo jobsearch_esc_html($date_field_date_format) ?>'
                });
            });
        </script>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_custom_field_range_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix)
    {
        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $range_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $range_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $range_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $range_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $range_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $range_field_min = isset($custom_field_saved_data['min']) ? $custom_field_saved_data['min'] : '0';
        $range_field_laps = isset($custom_field_saved_data['laps']) ? $custom_field_saved_data['laps'] : '20';
        $range_field_laps = $range_field_laps > 200 ? 200 : $range_field_laps;
        $range_field_interval = isset($custom_field_saved_data['interval']) ? $custom_field_saved_data['interval'] : '10000';
        $rand_id = rand(123, 123467);
        $range_field_required_str = '';
        if ($range_field_required == 'yes') {
            $range_field_required_str = 'required="required"';
        }
        // get db value if saved
        $range_field_name_db_val = get_post_meta($post_id, $range_field_name, true);
        $range_field_name_db_val = jobsearch_esc_html($range_field_name_db_val);

        wp_enqueue_style('jquery-ui');
        wp_enqueue_script('jquery-ui');
        $range_field_max = $range_field_min;
        $i = 0;
        while ($range_field_laps > $i) {
            $range_field_max = $range_field_max + $range_field_interval;
            $i++;
        }
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery("#slider-range<?php echo jobsearch_esc_html($range_field_name . $rand_id) ?>").slider({
                    range: "max",
                    min: <?php echo absint($range_field_min); ?>,
                    max: <?php echo absint($range_field_max); ?>,
                    value: <?php echo absint($range_field_name_db_val); ?>,
                    slide: function (event, ui) {
                        jQuery("#<?php echo jobsearch_esc_html($range_field_name . $rand_id) ?>").val(ui.value);
                    }
                });
                jQuery("#<?php echo jobsearch_esc_html($range_field_name . $rand_id) ?>").val(jQuery("#slider-range<?php echo jobsearch_esc_html($range_field_name . $rand_id) ?>").slider("value"));
            });
        </script>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php echo jobsearch_esc_html($range_field_label) ?></label>
            </div>
            <div class="elem-field">
                <input type="text" id="<?php echo jobsearch_esc_html($range_field_name . $rand_id) ?>"
                       name="<?php echo jobsearch_esc_html($range_field_name) ?>" value="" readonly
                       style="border:0; color:#f6931f; font-weight:bold;"/>
                <div id="slider-range<?php echo jobsearch_esc_html($range_field_name . $rand_id) ?>"></div>
            </div>
        </div>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_custom_field_checkbox_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix)
    {
        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $checkbox_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $checkbox_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $checkbox_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $checkbox_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $checkbox_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $field_post_multi = isset($custom_field_saved_data['post-multi']) ? $custom_field_saved_data['post-multi'] : '';
        $checkbox_field_options = isset($custom_field_saved_data['options']) ? $custom_field_saved_data['options'] : '';
        $checkbox_field_required_str = '';
        if ($checkbox_field_required == 'yes') {
            $checkbox_field_required_str = 'required="required"';
        }
        // get db value if saved
        $checkbox_field_name_db_val = get_post_meta($post_id, $checkbox_field_name, true);
        // creat options string
        ?>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php echo jobsearch_esc_html($checkbox_field_label) ?></label>
            </div>

            <div class="elem-field">
                <?php
                if (isset($checkbox_field_options['value']) && count($checkbox_field_options['value']) > 0) {
                    $option_counter = 0;
                    ?>
                    <div class="jobsearch-cusfield-checkbox <?php echo jobsearch_esc_html($checkbox_field_classes) ?>">
                        <?php
                        foreach ($checkbox_field_options['value'] as $option) {
                            if ($option != '') {
                                $option = ltrim(rtrim($option));
                                if ($checkbox_field_options['label'][$option_counter] != '' && str_replace(" ", "-", $option) != '') {
                                    //$option_val = strtolower(str_replace(" ", "-", $option));
                                    $option_val = $option;
                                    $option_label = $checkbox_field_options['label'][$option_counter];
                                    if (is_array($checkbox_field_name_db_val)) {
                                        $option_selected = in_array($option_val, $checkbox_field_name_db_val) ? ' checked="checked"' : '';
                                    } else {
                                        $option_selected = $checkbox_field_name_db_val == $option_val ? ' checked="checked"' : '';
                                    }
                                    if ($field_post_multi == 'yes') {
                                        ?>
                                        <div class="cusfield-checkbox-radioitm">
                                            <input id="opt-<?php echo($checkbox_field_name . '-' . $option_counter) ?>"
                                                   type="checkbox" name="<?php echo($checkbox_field_name) ?>[]"
                                                   value="<?php echo($option_val) ?>" <?php echo($option_selected) ?>>
                                            <label for="opt-<?php echo($checkbox_field_name . '-' . $option_counter) ?>">
                                                <span></span> <?php echo($option_label) ?>
                                            </label>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="cusfield-checkbox-radioitm">
                                            <input id="opt-<?php echo($checkbox_field_name . '-' . $option_counter) ?>"
                                                   type="radio" name="<?php echo($checkbox_field_name) ?>"
                                                   value="<?php echo($option_val) ?>" <?php echo($option_selected) ?>>
                                            <label for="opt-<?php echo($checkbox_field_name . '-' . $option_counter) ?>">
                                                <span></span> <?php echo($option_label) ?>
                                            </label>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            $option_counter++;
                        }
                        ?>
                    </div>
                    <?php
                } else {
                    ?>
                    <span><?php echo jobsearch_esc_html('Field did not configure properly', 'wp-jobsearch'); ?></span>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_custom_field_dropdown_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix)
    {
        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $dropdown_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $dropdown_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $dropdown_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $dropdown_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $dropdown_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $field_post_multi = isset($custom_field_saved_data['post-multi']) ? $custom_field_saved_data['post-multi'] : '';
        $dropdown_field_options = isset($custom_field_saved_data['options']) ? $custom_field_saved_data['options'] : '';
        $dropdown_field_required_str = '';
        if ($dropdown_field_required == 'yes') {
            $dropdown_field_required_str = 'required="required"';
        }
        // get db value if saved
        $dropdown_field_name_db_val = get_post_meta($post_id, $dropdown_field_name, true);
        // creat options string
        $dropdown_field_options_str = '';
        if (isset($dropdown_field_options['value']) && count($dropdown_field_options['value']) > 0) {
            $option_counter = 0;
            foreach ($dropdown_field_options['value'] as $option) {
                if ($option != '') {
                    $option = ltrim(rtrim($option));
                    if ($dropdown_field_options['label'][$option_counter] != '' && str_replace(" ", "-", $option) != '') {
                        //$option_val = strtolower(str_replace(" ", "-", $option));
                        $option_val = $option;
                        $option_label = $dropdown_field_options['label'][$option_counter];
                        if (is_array($dropdown_field_name_db_val)) {
                            $option_selected = in_array($option_val, $dropdown_field_name_db_val) ? ' selected="selected"' : '';
                        } else {
                            $option_selected = $dropdown_field_name_db_val == $option_val ? ' selected="selected"' : '';
                        }
                        $dropdown_field_options_str .= '<option ' . ($option_selected) . ' value="' . ($option_val) . '">' . ($option_label) . '</option>';
                    }
                }
                $option_counter++;
            }
        }
        ?>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php echo jobsearch_esc_html($dropdown_field_label) ?></label>
            </div>

            <div class="elem-field">
                <?php
                if ($dropdown_field_options_str != '') {
                    ?>
                    <select
                        <?php echo($field_post_multi == 'yes' ? 'multiple="multiple" ' : '') ?>name="<?php echo jobsearch_esc_html($dropdown_field_name) ?><?php echo($field_post_multi == 'yes' ? '[]' : '') ?>"
                        class="<?php echo jobsearch_esc_html($dropdown_field_classes) ?>"
                        placeholder="<?php echo jobsearch_esc_html($dropdown_field_placeholder) ?>" <?php echo force_balance_tags($dropdown_field_required_str) ?>>
                        <?php
                        if ($dropdown_field_placeholder != '') {
                            echo '<option value="">' . $dropdown_field_placeholder . '</option>';
                        }
                        echo($dropdown_field_options_str);
                        ?>
                    </select>
                    <?php
                } else {
                    ?>
                    <span><?php echo jobsearch_esc_html('Field did not configure properly', 'wp-jobsearch'); ?></span>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_custom_field_dependent_dropdown_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix)
    {
        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $dropdown_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $dropdown_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $dropdown_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $dropdown_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $dropdown_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $dropdown_field_options = isset($custom_field_saved_data['options_list']) ? $custom_field_saved_data['options_list'] : '';
        $dropdown_cont_optsid = isset($custom_field_saved_data['options_list_id']) && $custom_field_saved_data['options_list_id'] != '' ? $custom_field_saved_data['options_list_id'] : 0;

        $dropdown_field_required_str = '';
        if ($dropdown_field_required == 'yes') {
            $dropdown_field_required_str = 'required="required"';
        }
        // get db value if saved
        $dropdown_field_name_db_val = get_post_meta($post_id, $dropdown_field_name, true);
        ?>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php echo jobsearch_esc_html($dropdown_field_label) ?></label>
            </div>

            <div class="elem-field">
                <div class="jobsearch-depdrpdwn-fields <?php echo($dropdown_field_classes) ?>">
                    <?php
                    $depdrpdwn_fields = jobsearch_dependent_dropdown_list_html($dropdown_field_options, $dropdown_cont_optsid, $custom_field_saved_data, $dropdown_field_name_db_val);
                    echo($depdrpdwn_fields);
                    ?>
                </div>
            </div>
        </div>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_custom_field_textarea_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix)
    {
        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $textarea_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $textarea_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $textarea_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $textarea_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $textarea_field_media_btns = isset($custom_field_saved_data['media_buttons']) ? $custom_field_saved_data['media_buttons'] : '';
        $textarea_field_rich_editor = isset($custom_field_saved_data['rich_editor']) ? $custom_field_saved_data['rich_editor'] : '';
        $textarea_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $textarea_field_required_str = '';
        if ($textarea_field_required == 'yes') {
            $textarea_field_required_str = 'required="required"';
        }
        // get db value if saved
        $textarea_field_name_db_val = get_post_meta($post_id, $textarea_field_name, true);
        if ($textarea_field_rich_editor == 'no') {
            $textarea_field_name_db_val = jobsearch_esc_html($textarea_field_name_db_val);
        } else {
            $textarea_field_name_db_val = jobsearch_esc_wp_editor($textarea_field_name_db_val);
        }
        ?>
        <div class="jobsearch-element-field">
            <div class="elem-label">
                <label><?php echo jobsearch_esc_html($textarea_field_label) ?></label>
            </div>
            <div class="elem-field">
                <?php
                if ($textarea_field_rich_editor == 'no') {
                    ?>
                    <textarea
                            name="<?php echo($textarea_field_name) ?>"<?php echo($textarea_field_classes != '' ? ' class="' . $textarea_field_classes . '"' : '') ?><?php echo($textarea_field_placeholder != '' ? ' placeholder="' . $textarea_field_placeholder . '"' : '') ?>><?php echo($textarea_field_name_db_val) ?></textarea>
                    <?php
                } else {
                    $wped_settings = array(
                        'media_buttons' => ($textarea_field_media_btns == 'yes' ? true : false),
                        'editor_class' => $textarea_field_classes,
                        'quicktags' => array('buttons' => 'strong,em,del,ul,ol,li,close'),
                        'tinymce' => array(
                            'toolbar1' => 'bold,bullist,numlist,italic,underline,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                            'toolbar2' => '',
                            'toolbar3' => '',
                        ),
                    );
                    wp_editor($textarea_field_name_db_val, $textarea_field_name, $wped_settings);
                }
                ?>
            </div>
        </div>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_custom_field_heading_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix)
    {
        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $heading_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        ?>
        <div class="jobsearch-elem-heading">
            <h2><?php echo jobsearch_esc_html($heading_field_label) ?></h2>
        </div>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_dashboard_custom_fields_load_callback($post_id, $custom_field_entity)
    {
        global $custom_field_posttype;
        // load all saved fields
        $field_db_slug = "jobsearch_custom_field_" . $custom_field_entity;
        $custom_field_posttype = $custom_field_entity;
        $custom_all_fields_saved_data = get_option($field_db_slug);
        $count_node = time();
        $all_fields_name_str = '';
        if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0) {
            $field_names_counter = 0;
            $fields_prefix = '';
            $output = '
            <div class="jobsearch-employer-box-section">
            <div class="jobsearch-profile-title"><h2>' . esc_html__('Other Information', 'wp-jobsearch') . '</h2></div>
            <ul class="jobsearch-row jobsearch-employer-profile-form">';
            foreach ($custom_all_fields_saved_data as $f_key => $custom_field_saved_data) {

                if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "heading") {
                    $output .= apply_filters('jobsearch_dashboard_custom_field_heading_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "text") {
                    $output .= apply_filters('jobsearch_dashboard_custom_field_text_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "video") {
                    $output .= apply_filters('jobsearch_dashboard_custom_field_video_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "linkurl") {
                    $output .= apply_filters('jobsearch_dashboard_custom_field_linkurl_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "upload_file") {
                    $output .= apply_filters('jobsearch_dashboard_custom_field_upload_file_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "email") {
                    $output .= apply_filters('jobsearch_dashboard_custom_field_email_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "textarea") {
                    $output .= apply_filters('jobsearch_dashboard_custom_field_textarea_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "date") {
                    $output .= apply_filters('jobsearch_dashboard_custom_field_date_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "number") {
                    $output .= apply_filters('jobsearch_dashboard_custom_field_number_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "range") {
                    $output .= apply_filters('jobsearch_dashboard_custom_field_range_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "checkbox") {
                    $output .= apply_filters('jobsearch_dashboard_custom_field_checkbox_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "dropdown") {
                    $output .= apply_filters('jobsearch_dashboard_custom_field_dropdown_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "dependent_dropdown") {
                    $output .= apply_filters('jobsearch_dashboard_custom_field_dependent_dropdown_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "dependent_fields") {
                    $output .= apply_filters('jobsearch_dashboard_custom_field_dependent_fields_load', '', $post_id, $custom_field_saved_data, $fields_prefix, $f_key);
                }
                $output .= apply_filters('jobsearch_dash_cust_fields_after_load', '', $post_id, $custom_field_saved_data, $fields_prefix);
            }

            $output .= '
            </ul>
            </div>';
            $output .= apply_filters('jobsearch_dashboard_custom_fields_after', '', $post_id, $custom_field_entity);
            echo force_balance_tags($output);
        }
    }

    static function jobsearch_dashboard_custom_field_text_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $ffield_attr = array())
    {

        global $sitepress, $custom_field_posttype;

        $user_pkg_limits = new Package_Limits;

        $user_id = get_current_user_id();
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $text_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $text_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $text_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $text_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $text_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $text_field_required_str = '';

        $field_label_reqstar = '';
        if (isset($ffield_attr['required'])) {
            if ($ffield_attr['required'] == 'yes') {
                $text_field_classes .= ' jobsearch-req-field';
                $field_label_reqstar = ' *';
            }
        } else if ($text_field_required == 'yes') {
            $text_field_required_str = 'required="required"';
            $field_label_reqstar = ' *';
        }
        // get db value if saved
        $text_field_name_db_val = get_post_meta($post_id, $text_field_name, true);
        $text_field_name_db_val = jobsearch_esc_html($text_field_name_db_val);
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        ?>
        <li class="jobsearch-column-6">
            <label><?php echo apply_filters('wpml_translate_single_string', $text_field_label, 'Custom Fields', 'Text Field Label - ' . $text_field_label, $lang_code) ?><?php echo($field_label_reqstar) ?></label>
            <?php
            if ($custom_field_posttype == 'candidate' && $user_is_candidate && $user_pkg_limits::cand_field_is_locked('cusfields|' . $text_field_name)) {
                echo($user_pkg_limits::cand_gen_locked_html());
            } else if ($custom_field_posttype == 'employer' && $user_is_employer && $user_pkg_limits::emp_field_is_locked('cusfields|' . $text_field_name)) {
                echo($user_pkg_limits::emp_gen_locked_html());
            } else {
                ?>
                <input type="text" name="<?php echo jobsearch_esc_html($text_field_name) ?>"
                       class="<?php echo jobsearch_esc_html($text_field_classes) ?>"
                       placeholder="<?php echo apply_filters('wpml_translate_single_string', $text_field_placeholder, 'Custom Fields', 'Text Field Placeholder - ' . $text_field_placeholder, $lang_code) ?>" <?php echo force_balance_tags($text_field_required_str) ?>
                       value="<?php echo jobsearch_esc_html($text_field_name_db_val) ?>"/>
                <?php
            }
            ?>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_dashboard_custom_field_video_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $ffield_attr = array())
    {

        global $sitepress, $custom_field_posttype;

        $user_pkg_limits = new Package_Limits;

        $user_id = get_current_user_id();
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $video_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $video_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $video_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $video_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $video_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $video_field_required_str = '';
        if (isset($ffield_attr['required'])) {
            if ($ffield_attr['required'] == 'yes') {
                $video_field_classes .= ' jobsearch-req-field';
                $video_field_label = $video_field_label . ' *';
            }
        } else if ($video_field_required == 'yes') {
            $video_field_required_str = 'required="required"';
            $video_field_label = $video_field_label . ' *';
        }
        // get db value if saved
        $video_field_name_db_val = get_post_meta($post_id, $video_field_name, true);
        $video_field_name_db_val = jobsearch_esc_html($video_field_name_db_val);
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        ?>
        <li class="jobsearch-column-6">
            <label><?php echo apply_filters('wpml_translate_single_string', $video_field_label, 'Custom Fields', 'Text Field Label - ' . $video_field_label, $lang_code) ?></label>
            <?php
            if ($custom_field_posttype == 'candidate' && $user_is_candidate && $user_pkg_limits::cand_field_is_locked('cusfields|' . $video_field_name)) {
                echo($user_pkg_limits::cand_gen_locked_html());
            } else if ($custom_field_posttype == 'employer' && $user_is_employer && $user_pkg_limits::emp_field_is_locked('cusfields|' . $video_field_name)) {
                echo($user_pkg_limits::emp_gen_locked_html());
            } else {
                ?>
                <input type="text" name="<?php echo jobsearch_esc_html($video_field_name) ?>"
                       class="<?php echo jobsearch_esc_html($video_field_classes) ?>"
                       placeholder="<?php echo apply_filters('wpml_translate_single_string', $video_field_placeholder, 'Custom Fields', 'Text Field Placeholder - ' . $video_field_placeholder, $lang_code) ?>" <?php echo force_balance_tags($video_field_required_str) ?>
                       value="<?php echo jobsearch_esc_html($video_field_name_db_val) ?>"/>
                <?php
            }
            ?>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_dashboard_custom_field_linkurl_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $ffield_attr = array())
    {

        global $sitepress, $custom_field_posttype;

        $user_pkg_limits = new Package_Limits;

        $user_id = get_current_user_id();
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $linkurl_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $linkurl_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $linkurl_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $linkurl_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $linkurl_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $linkurl_field_required_str = '';
        if (isset($ffield_attr['required'])) {
            if ($ffield_attr['required'] == 'yes') {
                $linkurl_field_classes .= ' jobsearch-req-field';
                $linkurl_field_label = $linkurl_field_label . ' *';
            }
        } else if ($linkurl_field_required == 'yes') {
            $linkurl_field_required_str = 'required="required"';
            $linkurl_field_label = $linkurl_field_label . ' *';
        }
        // get db value if saved
        $linkurl_field_name_db_val = get_post_meta($post_id, $linkurl_field_name, true);
        $linkurl_field_name_db_val = jobsearch_esc_html($linkurl_field_name_db_val);
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        ?>
        <li class="jobsearch-column-6">
            <label><?php echo apply_filters('wpml_translate_single_string', $linkurl_field_label, 'Custom Fields', 'Text Field Label - ' . $linkurl_field_label, $lang_code) ?></label>
            <?php
            if ($custom_field_posttype == 'candidate' && $user_is_candidate && $user_pkg_limits::cand_field_is_locked('cusfields|' . $linkurl_field_name)) {
                echo($user_pkg_limits::cand_gen_locked_html());
            } else if ($custom_field_posttype == 'employer' && $user_is_employer && $user_pkg_limits::emp_field_is_locked('cusfields|' . $linkurl_field_name)) {
                echo($user_pkg_limits::emp_gen_locked_html());
            } else {
                ?>
                <input type="text" name="<?php echo jobsearch_esc_html($linkurl_field_name) ?>"
                       class="<?php echo jobsearch_esc_html($linkurl_field_classes) ?>"
                       placeholder="<?php echo apply_filters('wpml_translate_single_string', $linkurl_field_placeholder, 'Custom Fields', 'Text Field Placeholder - ' . $linkurl_field_placeholder, $lang_code) ?>" <?php echo force_balance_tags($linkurl_field_required_str) ?>
                       value="<?php echo jobsearch_esc_html($linkurl_field_name_db_val) ?>"/>
                <?php
            }
            ?>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_dashboard_custom_field_upload_file_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix)
    {

        global $sitepress, $custom_field_posttype;

        $rand_num = rand(10000000, 99999999);

        $user_pkg_limits = new Package_Limits;

        $user_id = get_current_user_id();
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $upload_file_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $upload_file_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $upload_file_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $upload_file_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';

        $upload_field_multifiles = isset($custom_field_saved_data['multi_files']) ? $custom_field_saved_data['multi_files'] : '';
        $upload_field_numof_files = isset($custom_field_saved_data['numof_files']) ? $custom_field_saved_data['numof_files'] : '';
        $upload_field_numof_files = $upload_field_numof_files > 0 ? $upload_field_numof_files : 5;
        $upload_field_allow_types = isset($custom_field_saved_data['allow_types']) ? $custom_field_saved_data['allow_types'] : '';
        $upload_field_allow_types = !empty($upload_field_allow_types) ? $upload_field_allow_types : array();
        $upload_field_file_size = isset($custom_field_saved_data['file_size']) ? $custom_field_saved_data['file_size'] : '';
        $upload_field_file_size = $upload_field_file_size == '' ? '5MB' : $upload_field_file_size;

        $upload_file_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $upload_file_field_required_str = '';
        if ($upload_file_field_required == 'yes') {
            $upload_file_field_required_str = 'required="required"';
            $upload_file_field_label = $upload_file_field_label . ' *';
        }
        // get db value if saved
        $upload_file_field_name_db_val = get_post_meta($post_id, $upload_file_field_name, true);
        $post_files_name = 'jobsearch_cfupfiles_' . $upload_file_field_name;

        $all_attach_files = get_post_meta($post_id, $post_files_name, true);

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        if ($upload_field_multifiles != 'yes') {
            $upload_field_numof_files = 1;
        }

        $uplod_file_size_num = abs((int)filter_var($upload_field_file_size, FILTER_SANITIZE_NUMBER_INT));
        $uplod_file_size_num = $uplod_file_size_num > 0 ? $uplod_file_size_num : 5;
        $uplod_file_size = $uplod_file_size_num * 1024;
        ?>
        <li class="jobsearch-column-12">
            <label><?php echo apply_filters('wpml_translate_single_string', $upload_file_field_label, 'Custom Fields', 'Upload File Field Label - ' . $upload_file_field_label, $lang_code) ?></label>
            <?php
            if ($custom_field_posttype == 'candidate' && $user_is_candidate && $user_pkg_limits::cand_field_is_locked('cusfields|' . $upload_file_field_name)) {
                echo($user_pkg_limits::cand_gen_locked_html());
            } else if ($custom_field_posttype == 'employer' && $user_is_employer && $user_pkg_limits::emp_field_is_locked('cusfields|' . $upload_file_field_name)) {
                echo($user_pkg_limits::emp_gen_locked_html());
            } else {
                ?>
                <div class="jobsearch-fileUpload">
                    <span><i class="jobsearch-icon jobsearch-upload"></i> <?php echo($upload_field_numof_files > 1 ? esc_html__('Upload Files', 'wp-jobsearch') : esc_html__('Upload File', 'wp-jobsearch')); ?></span>
                    <input name="<?php echo jobsearch_esc_html($upload_file_field_name) ?>[]" type="file"
                           class="upload jobsearch-upload jobsearch-uploadfile-field <?php echo($upload_file_field_required == 'yes' && empty($all_attach_files) ? 'jobsearch-cusfieldatt-req' : '') ?>"
                           multiple="multiple"
                           onchange="jobsearch_job_attach_files_url_<?php echo($rand_num) ?>(event)"/>
                    <?php
                    if ($upload_field_numof_files > 1) {
                        ?>
                        <div class="jobsearch-fileUpload-info">
                            <p><?php printf(esc_html__('You can upload up to %s files.', 'wp-jobsearch'), $upload_field_numof_files); ?></p>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div id="field-files-holder-<?php echo($rand_num) ?>" class="uplodfield-files-holder">
                    <?php
                    //var_dump($all_attach_files);
                    if (!empty($all_attach_files)) {
                        ?>
                        <ul>
                            <?php
                            foreach ($all_attach_files as $_attach_file) {
                                $_attach_id = jobsearch_get_attachment_id_from_url($_attach_file);
                                if ($_attach_id > 0) {
                                    $_attach_post = get_post($_attach_id);
                                    $_attach_mime = isset($_attach_post->post_mime_type) ? $_attach_post->post_mime_type : '';
                                    $_attach_guide = isset($_attach_post->guid) ? $_attach_post->guid : '';
                                    $attach_name = basename($_attach_guide);
                                    $file_icon = 'fa fa-file-text-o';
                                    if ($_attach_mime == 'image/png' || $_attach_mime == 'image/jpeg') {
                                        $file_icon = 'fa fa-file-image-o';
                                    } else if ($_attach_mime == 'application/msword' || $_attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                        $file_icon = 'fa fa-file-word-o';
                                    } else if ($_attach_mime == 'application/vnd.ms-excel' || $_attach_mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                        $file_icon = 'fa fa-file-excel-o';
                                    } else if ($_attach_mime == 'application/pdf') {
                                        $file_icon = 'fa fa-file-pdf-o';
                                    }
                                    ?>
                                    <li class="jobsearch-column-3">
                                        <a href="javascript:void(0);" class="fa fa-remove el-remove"></a>
                                        <div class="file-container">
                                            <a href="<?php echo($_attach_file) ?>"
                                               oncontextmenu="javascript: return false;"
                                               onclick="javascript: if ((event.button == 0 && event.ctrlKey)) {return false};"
                                               download="<?php echo($attach_name) ?>"><i
                                                        class="<?php echo($file_icon) ?>"></i> <?php echo($attach_name) ?>
                                            </a>
                                        </div>
                                        <input type="hidden" name="<?php echo jobsearch_esc_html($post_files_name) ?>[]"
                                               value="<?php echo jobsearch_esc_html($_attach_file) ?>">
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                        <?php
                    }
                    ?>
                </div>
                <script type="text/javascript">
                    jQuery(document).on('click', '.uplodfield-files-holder .el-remove', function () {
                        var e_target = jQuery(this).parent('li');
                        e_target.fadeOut('slow', function () {
                            e_target.remove();
                        });
                    });

                    function jobsearch_job_attach_files_url_<?php echo($rand_num) ?>(event) {

                        if (window.File && window.FileList && window.FileReader) {

                            var file_types_str = '<?php echo implode('|', $upload_field_allow_types) ?>';
                            if (file_types_str != '') {
                                var file_types_array = file_types_str.split('|');
                            } else {
                                var file_types_array = [];
                            }
                            var file_allow_size = '<?php echo($uplod_file_size) ?>';
                            var num_files_allow = '<?php echo($upload_field_numof_files) ?>';
                            file_allow_size = parseInt(file_allow_size);
                            num_files_allow = parseInt(num_files_allow);
                            jQuery('#field-files-holder-<?php echo($rand_num) ?>').find('.adding-file').remove();
                            var files = event.target.files;
                            for (var i = 0; i < files.length; i++) {

                                var _file = files[i];
                                var file_type = _file.type;
                                var file_size = _file.size;
                                var file_name = _file.name;
                                file_size = parseFloat(file_size / 1024).toFixed(2);
                                if (file_size <= file_allow_size) {
                                    if (file_types_array.indexOf(file_type) >= 0) {
                                        var file_icon = 'fa fa-file-text-o';
                                        if (file_type == 'image/png' || file_type == 'image/jpeg') {
                                            file_icon = 'fa fa-file-image-o';
                                        } else if (file_type == 'application/msword' || file_type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                            file_icon = 'fa fa-file-word-o';
                                        } else if (file_type == 'application/vnd.ms-excel' || file_type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                            file_icon = 'fa fa-file-excel-o';
                                        } else if (file_type == 'application/pdf') {
                                            file_icon = 'fa fa-file-pdf-o';
                                        }

                                        var rand_number = Math.floor((Math.random() * 99999999) + 1);
                                        var ihtml = '\
                                        <div class="jobsearch-column-3 adding-file">\
                                            <div class="file-container">\
                                                <a><i class="' + file_icon + '"></i> ' + file_name + '</a>\
                                            </div>\
                                        </div>';
                                        jQuery('#field-files-holder-<?php echo($rand_num) ?>').append(ihtml);
                                    } else {
                                        alert('<?php esc_html_e('This File type is not allowed.') ?>');
                                        return false;
                                    }
                                } else {
                                    alert('<?php esc_html_e('The file size is too large.') ?>');
                                    return false;
                                }

                                if (i == (num_files_allow - 1)) {
                                    return false;
                                }
                            }
                        }
                    }
                </script>
                <?php
            }
            ?>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_dashboard_custom_field_email_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $ffield_attr = array())
    {

        global $sitepress, $custom_field_posttype;

        $user_pkg_limits = new Package_Limits;

        $user_id = get_current_user_id();
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $email_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $email_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $email_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $email_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $email_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $email_field_required_str = '';
        if (isset($ffield_attr['required'])) {
            if ($ffield_attr['required'] == 'yes') {
                $email_field_classes .= ' jobsearch-req-field';
                $email_field_label = $textarea_field_label . ' *';
            }
        } else if ($email_field_required == 'yes') {
            $email_field_required_str = 'required="required"';
            $email_field_label = $email_field_label . ' *';
        }
        // get db value if saved
        $email_field_name_db_val = get_post_meta($post_id, $email_field_name, true);
        $email_field_name_db_val = jobsearch_esc_html($email_field_name_db_val);

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        ?>
        <li class="jobsearch-column-6">
            <label><?php echo apply_filters('wpml_translate_single_string', $email_field_label, 'Custom Fields', 'Email Field Label - ' . $email_field_label, $lang_code) ?></label>
            <?php
            if ($custom_field_posttype == 'candidate' && $user_is_candidate && $user_pkg_limits::cand_field_is_locked('cusfields|' . $email_field_name)) {
                echo($user_pkg_limits::cand_gen_locked_html());
            } else if ($custom_field_posttype == 'employer' && $user_is_employer && $user_pkg_limits::emp_field_is_locked('cusfields|' . $email_field_name)) {
                echo($user_pkg_limits::emp_gen_locked_html());
            } else {
                ?>
                <input type="email" name="<?php echo jobsearch_esc_html($email_field_name) ?>"
                       class="<?php echo jobsearch_esc_html($email_field_classes) ?>"
                       placeholder="<?php echo apply_filters('wpml_translate_single_string', $email_field_placeholder, 'Custom Fields', 'Email Field Placeholder - ' . $email_field_placeholder, $lang_code) ?>" <?php echo force_balance_tags($email_field_required_str) ?>
                       value="<?php echo jobsearch_esc_html($email_field_name_db_val) ?>"/>
                <?php
            }
            ?>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_dashboard_custom_field_number_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $ffield_attr = array())
    {

        global $sitepress, $custom_field_posttype;

        $user_pkg_limits = new Package_Limits;

        $user_id = get_current_user_id();
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $number_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $number_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $number_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $number_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $number_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $number_field_required_str = '';
        if (isset($ffield_attr['required'])) {
            if ($ffield_attr['required'] == 'yes') {
                $number_field_classes .= ' jobsearch-req-field';
                $number_field_label = $number_field_label . ' *';
            }
        } else if ($number_field_required == 'yes') {
            $number_field_required_str = 'required="required"';
            $number_field_label = $number_field_label . ' *';
        }
        // get db value if saved
        $number_field_name_db_val = get_post_meta($post_id, $number_field_name, true);
        $number_field_name_db_val = jobsearch_esc_html($number_field_name_db_val);

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        ?>
        <li class="jobsearch-column-6">
            <label><?php echo apply_filters('wpml_translate_single_string', $number_field_label, 'Custom Fields', 'Number Field Label - ' . $number_field_label, $lang_code) ?></label>
            <?php
            if ($custom_field_posttype == 'candidate' && $user_is_candidate && $user_pkg_limits::cand_field_is_locked('cusfields|' . $number_field_name)) {
                echo($user_pkg_limits::cand_gen_locked_html());
            } else if ($custom_field_posttype == 'employer' && $user_is_employer && $user_pkg_limits::emp_field_is_locked('cusfields|' . $number_field_name)) {
                echo($user_pkg_limits::emp_gen_locked_html());
            } else {
                ?>
                <input type="number" name="<?php echo jobsearch_esc_html($number_field_name) ?>"
                       class="<?php echo jobsearch_esc_html($number_field_classes) ?>" min="0"
                       placeholder="<?php echo apply_filters('wpml_translate_single_string', $number_field_placeholder, 'Custom Fields', 'Number Field Placeholder - ' . $number_field_placeholder, $lang_code) ?>" <?php echo force_balance_tags($number_field_required_str) ?>
                       value="<?php echo jobsearch_esc_html($number_field_name_db_val) ?>"/>
                <?php
            }
            ?>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_dashboard_custom_field_date_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $ffield_attr = array())
    {

        global $sitepress, $custom_field_posttype;

        $user_pkg_limits = new Package_Limits;

        $user_id = get_current_user_id();
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);

        ob_start();
        $field_rand_id = rand(454, 999999);
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $date_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $date_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $date_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $date_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $date_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $date_field_date_format = isset($custom_field_saved_data['date-format']) && $custom_field_saved_data['date-format'] != '' ? $custom_field_saved_data['date-format'] : 'd-m-Y';
        $date_field_required_str = '';
        if (isset($ffield_attr['required'])) {
            if ($ffield_attr['required'] == 'yes') {
                $date_field_classes .= ' jobsearch-req-field';
                $date_field_label = $date_field_label . ' *';
            }
        } else if ($date_field_required == 'yes') {
            $date_field_required_str = 'required="required"';
            $date_field_label = $date_field_label . ' *';
        }
        // get db value if saved
        $date_field_name_db_val = get_post_meta($post_id, $date_field_name, true);
        $date_field_name_db_val = jobsearch_esc_html($date_field_name_db_val);
        //var_dump($date_field_name_db_val);
        if ($date_field_name_db_val != '') {
            $date_field_name_db_val = date($date_field_date_format, ($date_field_name_db_val));
        }

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        ?>
        <li class="jobsearch-column-6">
            <label><?php echo apply_filters('wpml_translate_single_string', $date_field_label, 'Custom Fields', 'Date Field Label - ' . $date_field_label, $lang_code) ?></label>
            <?php
            if ($custom_field_posttype == 'candidate' && $user_is_candidate && $user_pkg_limits::cand_field_is_locked('cusfields|' . $date_field_name)) {
                echo($user_pkg_limits::cand_gen_locked_html());
            } else if ($custom_field_posttype == 'employer' && $user_is_employer && $user_pkg_limits::emp_field_is_locked('cusfields|' . $date_field_name)) {
                echo($user_pkg_limits::emp_gen_locked_html());
            } else {
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function () {
                        jQuery('#<?php echo jobsearch_esc_html($date_field_name . $field_rand_id) ?>').datetimepicker({
                            format: '<?php echo jobsearch_esc_html($date_field_date_format) ?>'
                        });
                    });
                </script>
                <input type="text" id="<?php echo jobsearch_esc_html($date_field_name . $field_rand_id) ?>"
                       name="<?php echo jobsearch_esc_html($date_field_name) ?>"
                       class="<?php echo jobsearch_esc_html($date_field_classes) ?>"
                       placeholder="<?php echo apply_filters('wpml_translate_single_string', $date_field_placeholder, 'Custom Fields', 'Date Field Placeholder - ' . $date_field_placeholder, $lang_code) ?>" <?php echo force_balance_tags($date_field_required_str) ?>
                       value="<?php echo jobsearch_esc_html($date_field_name_db_val) ?>"/>
                <?php
            }
            ?>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_dashboard_custom_field_range_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $ffield_attr = array())
    {
        ob_start();
        global $sitepress, $custom_field_posttype;

        $user_pkg_limits = new Package_Limits;

        $user_id = get_current_user_id();
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);

        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $range_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $range_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $range_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $range_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $range_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $range_field_style = isset($custom_field_saved_data['field-style']) ? $custom_field_saved_data['field-style'] : '';
        $range_field_min = isset($custom_field_saved_data['min']) ? $custom_field_saved_data['min'] : '0';
        $range_field_laps = isset($custom_field_saved_data['laps']) ? $custom_field_saved_data['laps'] : '20';
        $range_field_interval = isset($custom_field_saved_data['interval']) ? $custom_field_saved_data['interval'] : '1000';
        $rand_id = rand(123, 123467);
        $range_field_required_str = '';

        $field_label_reqstar = '';
        if (isset($ffield_attr['required'])) {
            if ($ffield_attr['required'] == 'yes') {
                $textarea_field_classes .= ' jobsearch-req-field';
                $field_label_reqstar = ' *';
            }
        } else if ($range_field_required == 'yes') {
            $range_field_classes = 'required="required"';
            $field_label_reqstar = ' *';
        }
        // get db value if saved
        $range_field_name_db_val = get_post_meta($post_id, $range_field_name, true);
        $range_field_name_db_val = jobsearch_esc_html($range_field_name_db_val);

        $range_field_max = $range_field_min;

        if ($range_field_style == 'slider') {
            wp_enqueue_style('jquery-ui');
            wp_enqueue_script('jquery-ui');
            $i = 0;
            while ($range_field_laps > $i) {
                $range_field_max = $range_field_max + $range_field_interval;
                $i++;
            }
        }
        ?>
        <li class="jobsearch-column-6">
            <?php
            $lang_code = '';
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $lang_code = $sitepress->get_current_language();
            }
            ?>
            <label><?php echo apply_filters('wpml_translate_single_string', $range_field_label, 'Custom Fields', 'Range Field Label - ' . $range_field_label, $lang_code) ?><?php echo($field_label_reqstar) ?></label>
            <?php
            if ($custom_field_posttype == 'candidate' && $user_is_candidate && $user_pkg_limits::cand_field_is_locked('cusfields|' . $range_field_name)) {
                echo($user_pkg_limits::cand_gen_locked_html());
            } else if ($custom_field_posttype == 'employer' && $user_is_employer && $user_pkg_limits::emp_field_is_locked('cusfields|' . $range_field_name)) {
                echo($user_pkg_limits::emp_gen_locked_html());
            } else {
                //
                if ($range_field_style == 'slider') {
                    ?>
                    <div class="range-field-container">
                        <input type="text" id="<?php echo jobsearch_esc_html($range_field_name . $rand_id) ?>"
                               name="<?php echo jobsearch_esc_html($range_field_name) ?>" value="" readonly
                               style="border:0; color:#f6931f; font-weight:bold;"/>
                        <div id="slider-range<?php echo jobsearch_esc_html($range_field_name . $rand_id) ?>"></div>
                    </div>
                    <script type="text/javascript">
                        //jQuery(document).ready(function () {
                        jQuery("#slider-range<?php echo jobsearch_esc_html($range_field_name . $rand_id) ?>").slider({
                            range: "max",
                            min: <?php echo absint($range_field_min); ?>,
                            max: <?php echo absint($range_field_max); ?>,
                            value: <?php echo absint($range_field_name_db_val); ?>,
                            slide: function (event, ui) {
                                jQuery("#<?php echo jobsearch_esc_html($range_field_name . $rand_id) ?>").val(ui.value);
                            }
                        });
                        jQuery("#<?php echo jobsearch_esc_html($range_field_name . $rand_id) ?>").val(jQuery("#slider-range<?php echo jobsearch_esc_html($range_field_name . $rand_id) ?>").slider("value"));
                        //});
                    </script>
                <?php
                } else {
                ?>
                    <div class="jobsearch-profile-select">
                        <select name="<?php echo jobsearch_esc_html($range_field_name) ?>"
                                class="<?php echo($range_field_classes) ?> selectize-select" <?php echo($range_field_placeholder != '' ? 'placeholder="' . $range_field_placeholder . '"' : '') ?> <?php echo($range_field_required == 'yes' ? 'required="required"' : '') ?>>
                            <?php
                            echo($range_field_placeholder != '' ? '<option value="">' . $range_field_placeholder . '</option>' : '');
                            $i = 0;
                            while ($range_field_laps > $i) {
                                echo '<option value="' . $range_field_max . '">' . $range_field_max . '</option>';
                                $range_field_max = $range_field_max + $range_field_interval;
                                $i++;
                            }
                            ?>
                        </select>
                    </div>
                    <?php
                }
            }
            ?>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_dashboard_custom_field_checkbox_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $ffield_attr = array())
    {

        global $sitepress, $custom_field_posttype;

        $user_pkg_limits = new Package_Limits;

        $user_id = get_current_user_id();
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $checkbox_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $checkbox_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $checkbox_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $checkbox_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $checkbox_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $field_post_multi = isset($custom_field_saved_data['post-multi']) ? $custom_field_saved_data['post-multi'] : '';
        $checkbox_field_options = isset($custom_field_saved_data['options']) ? $custom_field_saved_data['options'] : '';
        $checkbox_field_required_str = '';

        $field_label_reqstar = '';
        if (isset($ffield_attr['required'])) {
            if ($ffield_attr['required'] == 'yes') {
                $checkbox_field_classes .= ' cusfield-checkbox-required';
                $field_label_reqstar = ' *';
            }
        } else if ($checkbox_field_required == 'yes') {
            $checkbox_field_required_str = 'required="required"';
            $field_label_reqstar = ' *';
        }
        // get db value if saved
        $checkbox_field_name_db_val = get_post_meta($post_id, $checkbox_field_name, true);
        // creat options string

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        ?>
        <li class="jobsearch-column-12">
            <label><?php echo apply_filters('wpml_translate_single_string', $checkbox_field_label, 'Custom Fields', 'Dropdown Field Label - ' . $checkbox_field_label, $lang_code) ?><?php echo($field_label_reqstar) ?></label>
            <?php
            if ($custom_field_posttype == 'candidate' && $user_is_candidate && $user_pkg_limits::cand_field_is_locked('cusfields|' . $checkbox_field_name)) {
                echo($user_pkg_limits::cand_gen_locked_html());
            } else if ($custom_field_posttype == 'employer' && $user_is_employer && $user_pkg_limits::emp_field_is_locked('cusfields|' . $checkbox_field_name)) {
                echo($user_pkg_limits::emp_gen_locked_html());
            } else {
                if (isset($checkbox_field_options['value']) && count($checkbox_field_options['value']) > 0) {
                    $option_counter = 0;
                    ?>
                    <div class="jobsearch-cusfield-checkbox <?php echo jobsearch_esc_html($checkbox_field_classes) ?><?php echo($checkbox_field_required == 'yes' ? ' cusfield-checkbox-required' : '') ?>">
                        <?php
                        foreach ($checkbox_field_options['value'] as $option) {
                            if ($option != '') {
                                $option = ltrim(rtrim($option));
                                if ($checkbox_field_options['label'][$option_counter] != '' && str_replace(" ", "-", $option) != '') {
                                    //$option_val = strtolower(str_replace(" ", "-", $option));
                                    $option_val = $option;
                                    $option_label = $checkbox_field_options['label'][$option_counter];
                                    if (is_array($checkbox_field_name_db_val)) {
                                        $option_selected = in_array($option_val, $checkbox_field_name_db_val) ? ' checked="checked"' : '';
                                    } else {
                                        $option_selected = $checkbox_field_name_db_val == $option_val ? ' checked="checked"' : '';
                                    }
                                    if ($field_post_multi == 'yes') { ?>
                                        <div class="cusfield-checkbox-radioitm jobsearch-checkbox">
                                            <input id="opt-<?php echo($checkbox_field_name . '-' . $option_counter) ?>"
                                                   type="checkbox" name="<?php echo($checkbox_field_name) ?>[]"
                                                   value="<?php echo($option_val) ?>" <?php echo($option_selected) ?>>
                                            <label for="opt-<?php echo($checkbox_field_name . '-' . $option_counter) ?>">
                                                <span></span> <?php echo($option_label) ?>
                                            </label>
                                        </div>
                                    <?php } else { ?>
                                        <div class="cusfield-checkbox-radioitm jobsearch-checkbox">
                                            <input id="opt-<?php echo($checkbox_field_name . '-' . $option_counter) ?>"
                                                   type="radio" name="<?php echo($checkbox_field_name) ?>"
                                                   value="<?php echo($option_val) ?>" <?php echo($option_selected) ?>>
                                            <label for="opt-<?php echo($checkbox_field_name . '-' . $option_counter) ?>">
                                                <span></span> <?php echo($option_label) ?>
                                            </label>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            $option_counter++;
                        }
                        ?>
                    </div>
                    <?php
                } else {
                    ?>
                    <span><?php echo jobsearch_esc_html('Field did not configure properly', 'wp-jobsearch'); ?></span>
                    <?php
                }
            }
            ?>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_dashboard_custom_field_dropdown_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $ffield_attr = array())
    {

        global $sitepress, $custom_field_posttype;

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        $user_pkg_limits = new Package_Limits;

        $user_id = get_current_user_id();
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $dropdown_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $dropdown_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $dropdown_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $dropdown_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $dropdown_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $field_post_multi = isset($custom_field_saved_data['post-multi']) ? $custom_field_saved_data['post-multi'] : '';
        $dropdown_field_options = isset($custom_field_saved_data['options']) ? $custom_field_saved_data['options'] : '';
        $dropdown_field_required_str = '';

        $field_label_reqstar = '';
        if (isset($ffield_attr['required'])) {
            if ($ffield_attr['required'] == 'yes') {
                $dropdown_field_classes .= ' jobsearch-req-field';
                $field_label_reqstar = ' *';
            }
        } else if ($dropdown_field_required == 'yes') {
            $dropdown_field_required_str = 'required="required"';
            $field_label_reqstar = ' *';
        }
        // get db value if saved
        $dropdown_field_name_db_val = get_post_meta($post_id, $dropdown_field_name, true);
        
        // creat options string
        $dropdown_field_options_str = '';
        if ($dropdown_field_placeholder != '') {
            $dropdown_field_options_str = '<option value="">' . apply_filters('wpml_translate_single_string', $dropdown_field_placeholder, 'Custom Fields', 'Dropdown Field Placeholder - ' . $dropdown_field_placeholder, $lang_code) . '</option>';
        }
        if (isset($dropdown_field_options['value']) && count($dropdown_field_options['value']) > 0) {
            $option_counter = 0;
            foreach ($dropdown_field_options['value'] as $option) {
                if ($option != '') {
                    $option = ltrim(rtrim($option));
                    if ($dropdown_field_options['label'][$option_counter] != '' && str_replace(" ", "-", $option) != '') {
                        //$option_val = strtolower(str_replace(" ", "-", $option));
                        $option_val = $option;
                        $option_label = $dropdown_field_options['label'][$option_counter];
                        $option_label = stripslashes($option_label);

                        if (is_array($dropdown_field_name_db_val)) {
                            $option_selected = in_array($option_val, $dropdown_field_name_db_val) ? ' selected="selected"' : '';
                        } else {
                            $option_selected = $dropdown_field_name_db_val == $option_val ? ' selected="selected"' : '';
                        }
                        do_action('wpml_register_single_string', 'Custom Fields', 'Dropdown Option Label - ' . $option_label, $option_label);

                        $dropdown_field_options_str .= '<option ' . force_balance_tags($option_selected) . ' value="' . esc_html($option_val) . '">' . apply_filters('wpml_translate_single_string', $option_label, 'Custom Fields', 'Dropdown Option Label - ' . $option_label, $lang_code) . '</option>';
                    }
                }
                $option_counter++;
            }
        }
        ?>
        <li class="jobsearch-column-6">
            <label><?php echo apply_filters('wpml_translate_single_string', $dropdown_field_label, 'Custom Fields', 'Dropdown Field Label - ' . $dropdown_field_label, $lang_code) ?><?php echo($field_label_reqstar) ?></label>
            <?php
            //var_dump($dropdown_field_name_db_val);
            if ($custom_field_posttype == 'candidate' && $user_is_candidate && $user_pkg_limits::cand_field_is_locked('cusfields|' . $dropdown_field_name)) {
                echo($user_pkg_limits::cand_gen_locked_html());
            } else if ($custom_field_posttype == 'employer' && $user_is_employer && $user_pkg_limits::emp_field_is_locked('cusfields|' . $dropdown_field_name)) {
                echo($user_pkg_limits::emp_gen_locked_html());
            } else {
                if ($dropdown_field_options_str != '') {
                    ob_start();
                    ?>
                    <div class="jobsearch-profile-select">
                        <select
                            <?php echo($field_post_multi == 'yes' ? 'multiple="multiple" ' : '') ?>name="<?php echo jobsearch_esc_html($dropdown_field_name) ?><?php echo($field_post_multi == 'yes' ? '[]' : '') ?>"
                            placeholder="<?php echo apply_filters('wpml_translate_single_string', $dropdown_field_placeholder, 'Custom Fields', 'Dropdown Field Placeholder - ' . $dropdown_field_placeholder, $lang_code) ?>"
                            class="<?php echo jobsearch_esc_html($dropdown_field_classes) ?> selectize-select" <?php echo force_balance_tags($dropdown_field_required_str) ?>>
                            <?php
                            echo($dropdown_field_options_str);
                            ?>
                        </select>
                    </div>
                    <?php
                    $drpdown_html = ob_get_clean();
                    $drpdwn_args = array(
                        'dropdown_field_name' => $dropdown_field_name,
                        'dropdown_field_classes' => $dropdown_field_classes,
                        'dropdown_field_required' => $dropdown_field_required,
                        'field_post_multi' => $field_post_multi,
                        'dropdown_field_options' => $dropdown_field_options,
                        'dropdown_field_name_db_val' => $dropdown_field_name_db_val,
                    );
                    echo apply_filters('jobsearch_custm_field_dropdown_dash', $drpdown_html, $drpdwn_args);
                } else {
                    ?>
                    <span><?php echo jobsearch_esc_html('Field did not configure properly', 'wp-jobsearch'); ?></span>
                    <?php
                }
            }
            ?>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_dashboard_custom_field_dependent_dropdown_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $ffield_attr = array())
    {

        global $sitepress, $custom_field_posttype;

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        $user_pkg_limits = new Package_Limits;

        $user_id = get_current_user_id();
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $dropdown_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $dropdown_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $dropdown_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $dropdown_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $dropdown_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $dropdown_field_options = isset($custom_field_saved_data['options_list']) ? $custom_field_saved_data['options_list'] : '';
        $dropdown_cont_optsid = isset($custom_field_saved_data['options_list_id']) && $custom_field_saved_data['options_list_id'] != '' ? $custom_field_saved_data['options_list_id'] : 0;

        $dropdown_field_label = apply_filters('wpml_translate_single_string', $dropdown_field_label, 'Custom Fields', 'Dropdown Field Label - ' . $dropdown_field_label, $lang_code);

        $dropdown_field_required_str = '';
        if (isset($ffield_attr['required'])) {
            if ($ffield_attr['required'] == 'yes') {
                $dropdown_field_classes .= ' jobsearch-req-field';
                $dropdown_field_label = $dropdown_field_label . ' *';
            }
        } else if ($dropdown_field_required == 'yes') {
            $dropdown_field_required_str = 'required="required"';
            $dropdown_field_label = $dropdown_field_label . ' *';
        }
        // get db value if saved
        $dropdown_field_name_db_val = get_post_meta($post_id, $dropdown_field_name, true);

        //
        if ($custom_field_posttype == 'candidate' && $user_is_candidate && $user_pkg_limits::cand_field_is_locked('cusfields|' . $dropdown_field_name)) {
            echo '<li class="jobsearch-column-6">';
            echo '<label>' . ($dropdown_field_label) . '</label>';
            echo($user_pkg_limits::cand_gen_locked_html());
            echo '</li>';
        } else if ($custom_field_posttype == 'employer' && $user_is_employer && $user_pkg_limits::emp_field_is_locked('cusfields|' . $dropdown_field_name)) {
            echo '<li class="jobsearch-column-6">';
            echo '<label>' . ($dropdown_field_label) . '</label>';
            echo($user_pkg_limits::emp_gen_locked_html());
            echo '</li>';
        } else {
            ?>
            <li class="jobsearch-column-12 jobsearch-depdrpdwn-fields <?php echo($dropdown_field_classes) ?>">
                <ul class="jobsearch-row" style="margin: 0 -10px;">
                    <?php
                    $depdrpdwn_fields = jobsearch_dependent_dropdown_list_html($dropdown_field_options, $dropdown_cont_optsid, $custom_field_saved_data, $dropdown_field_name_db_val, 0, 'dashboard', $dropdown_field_label);
                    echo($depdrpdwn_fields);
                    ?>
                </ul>
            </li>
            <?php
        }
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_dashboard_custom_field_textarea_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $ffield_attr = array())
    {

        global $sitepress, $custom_field_posttype;

        $user_pkg_limits = new Package_Limits;

        $user_id = get_current_user_id();
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $textarea_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $textarea_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $textarea_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $textarea_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $textarea_field_media_btns = isset($custom_field_saved_data['media_buttons']) ? $custom_field_saved_data['media_buttons'] : '';
        $textarea_field_rich_editor = isset($custom_field_saved_data['rich_editor']) ? $custom_field_saved_data['rich_editor'] : '';
        $textarea_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';

        if (isset($ffield_attr['required'])) {
            if ($ffield_attr['required'] == 'yes') {
                if ($textarea_field_rich_editor == 'no') {
                    $textarea_field_classes .= ' jobsearch-req-field';
                } else {
                    $textarea_field_classes .= ' jobsearch-req-field jobsearch-reqtext-editor';
                }
                $textarea_field_label = $textarea_field_label . ' *';
            }
        } else if ($textarea_field_required == 'yes') {
            if ($textarea_field_rich_editor == 'no') {
                $textarea_field_classes .= ' jobsearch-req-field';
            } else {
                $textarea_field_classes .= ' jobsearch-req-field jobsearch-reqtext-editor';
            }
            $textarea_field_label = $textarea_field_label . ' *';
        }
        // get db value if saved
        $textarea_field_name_db_val = get_post_meta($post_id, $textarea_field_name, true);
        if ($textarea_field_rich_editor == 'no') {
            $textarea_field_name_db_val = jobsearch_esc_html($textarea_field_name_db_val);
        } else {
            $textarea_field_name_db_val = jobsearch_esc_wp_editor($textarea_field_name_db_val);
        }

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        ?>
        <li class="jobsearch-column-12">
            <label><?php echo apply_filters('wpml_translate_single_string', $textarea_field_label, 'Custom Fields', 'Textarea Field Label - ' . $textarea_field_label, $lang_code) ?></label>
            <?php
            if ($custom_field_posttype == 'candidate' && $user_is_candidate && $user_pkg_limits::cand_field_is_locked('cusfields|' . $textarea_field_name)) {
                echo($user_pkg_limits::cand_gen_locked_html());
            } else if ($custom_field_posttype == 'employer' && $user_is_employer && $user_pkg_limits::emp_field_is_locked('cusfields|' . $textarea_field_name)) {
                echo($user_pkg_limits::emp_gen_locked_html());
            } else {
                //
                if ($textarea_field_rich_editor == 'no') {
                    ?>
                    <textarea
                            name="<?php echo($textarea_field_name) ?>"<?php echo($textarea_field_required == 'yes' ? ' required' : '') ?><?php echo($textarea_field_classes != '' ? ' class="' . $textarea_field_classes . '"' : '') ?><?php echo($textarea_field_placeholder != '' ? ' placeholder="' . $textarea_field_placeholder . '"' : '') ?>><?php echo($textarea_field_name_db_val) ?></textarea>
                    <?php
                } else {
                    $wped_settings = array(
                        'media_buttons' => ($textarea_field_media_btns == 'yes' ? true : false),
                        'editor_class' => $textarea_field_classes,
                        'quicktags' => array('buttons' => 'strong,em,del,ul,ol,li,close'),
                        'tinymce' => array(
                            'toolbar1' => 'bold,bullist,numlist,italic,underline,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                            'toolbar2' => '',
                            'toolbar3' => '',
                        ),
                    );
                    wp_editor($textarea_field_name_db_val, $textarea_field_name, $wped_settings);
                }
            }
            ?>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_dashboard_custom_field_heading_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $ffield_attr = array())
    {

        global $sitepress, $custom_field_posttype;

        $user_pkg_limits = new Package_Limits;

        $user_id = get_current_user_id();
        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $heading_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        ?>
        <li class="jobsearch-column-12">
            <div class="jobsearch-profile-title jobsearch-dashboard-heading">
                <h2><?php echo apply_filters('wpml_translate_single_string', $heading_field_label, 'Custom Fields', 'Heading Field Label - ' . $heading_field_label, $lang_code) ?></h2>
            </div>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_form_custom_fields_load_callback($post_id, $custom_field_entity, $self_vals = array())
    {
        global $custom_field_posttype;
        // load all saved fields
        $custom_field_posttype = $custom_field_entity;
        $field_db_slug = "jobsearch_custom_field_" . $custom_field_entity;
        $custom_all_fields_saved_data = get_option($field_db_slug);
        $count_node = time();
        $all_fields_name_str = '';
        if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0) {
            $field_names_counter = 0;
            $fields_prefix = '';
            $output = '';
            foreach ($custom_all_fields_saved_data as $f_key => $custom_field_saved_data) {

                if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "heading") {
                    $output .= apply_filters('jobsearch_form_custom_field_heading_load', '', $post_id, $custom_field_saved_data, $fields_prefix, '', '', $self_vals);
                } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "text") {
                    $output .= apply_filters('jobsearch_form_custom_field_text_load', '', $post_id, $custom_field_saved_data, $fields_prefix, '', '', $self_vals);
                } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "video") {
                    $output .= apply_filters('jobsearch_form_custom_field_video_load', '', $post_id, $custom_field_saved_data, $fields_prefix, '', '', $self_vals);
                } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "linkurl") {
                    $output .= apply_filters('jobsearch_form_custom_field_linkurl_load', '', $post_id, $custom_field_saved_data, $fields_prefix, '', '', $self_vals);
                } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "upload_file") {
                    $output .= apply_filters('jobsearch_form_custom_field_upload_file_load', '', $post_id, $custom_field_saved_data, $fields_prefix, '', '', $self_vals);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "email") {
                    $output .= apply_filters('jobsearch_form_custom_field_email_load', '', $post_id, $custom_field_saved_data, $fields_prefix, '', '', $self_vals);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "textarea") {
                    $output .= apply_filters('jobsearch_form_custom_field_textarea_load', '', $post_id, $custom_field_saved_data, $fields_prefix, '', '', $self_vals);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "date") {
                    $output .= apply_filters('jobsearch_form_custom_field_date_load', '', $post_id, $custom_field_saved_data, $fields_prefix, '', '', $self_vals);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "number") {
                    $output .= apply_filters('jobsearch_form_custom_field_number_load', '', $post_id, $custom_field_saved_data, $fields_prefix, '', '', $self_vals);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "range") {
                    $output .= apply_filters('jobsearch_form_custom_field_range_load', '', $post_id, $custom_field_saved_data, $fields_prefix, '', '', $self_vals);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "checkbox") {
                    $output .= apply_filters('jobsearch_form_custom_field_checkbox_load', '', $post_id, $custom_field_saved_data, $fields_prefix, '', '', $self_vals);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "dropdown") {
                    $output .= apply_filters('jobsearch_form_custom_field_dropdown_load', '', $post_id, $custom_field_saved_data, $fields_prefix, '', '', $self_vals);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "dependent_dropdown") {
                    $output .= apply_filters('jobsearch_form_custom_field_dependent_dropdown_load', '', $post_id, $custom_field_saved_data, $fields_prefix, '', '', $self_vals);
                } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "dependent_fields") {
                    $output .= apply_filters('jobsearch_form_custom_field_dependent_fields_load', '', $post_id, $custom_field_saved_data, $fields_prefix, $f_key, 'simp_form', $self_vals);
                }
            }

            echo($output);
        }
    }

    public function jobsearch_signup_custom_fields_load_callback($post_id, $custom_field_entity, $f_display = 'block')
    {
        global $jobsearch_plugin_options, $custom_field_posttype;

        $reg_custom_fields = isset($jobsearch_plugin_options['signup_custom_fields']) ? $jobsearch_plugin_options['signup_custom_fields'] : '';

        if ($reg_custom_fields == 'on') {

            $custom_field_posttype = $custom_field_entity;

            if ($custom_field_entity == 'employer') {
                $selected_fields = isset($jobsearch_plugin_options['employer_custom_fields']) ? $jobsearch_plugin_options['employer_custom_fields'] : '';
                $con_class = 'employer-cus-field';
            } else {
                $selected_fields = isset($jobsearch_plugin_options['candidate_custom_fields']) ? $jobsearch_plugin_options['candidate_custom_fields'] : '';
                $con_class = 'candidate-cus-field';
            }

            // load all saved fields
            $field_db_slug = "jobsearch_custom_field_" . $custom_field_entity;
            $custom_all_fields_saved_data = get_option($field_db_slug);
            $count_node = time();
            $all_fields_name_str = '';
            if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0 && !empty($selected_fields)) {
                $field_names_counter = 0;
                $fields_prefix = '';
                $output = '';
                foreach ($custom_all_fields_saved_data as $f_key => $custom_field_saved_data) {
                    $field_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
                    $is_qequired = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
                    $con_class_ext = '';
                    if ($is_qequired == 'yes') {
                        if ($custom_field_entity == 'employer') {
                            $con_class_ext = ' jobsearch-regemp-require';
                        } else {
                            $con_class_ext = ' jobsearch-regcand-require';
                        }
                    }
                    
                    if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "heading" && (in_array($field_name, $selected_fields))) {
                        $output .= apply_filters('jobsearch_form_custom_field_heading_load', '', $post_id, $custom_field_saved_data, $fields_prefix, $con_class . $con_class_ext, $f_display);
                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "text" && ($field_name != '' && in_array($field_name, $selected_fields))) {
                        $output .= apply_filters('jobsearch_form_custom_field_text_load', '', $post_id, $custom_field_saved_data, $fields_prefix, $con_class . $con_class_ext, $f_display);
                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "video" && ($field_name != '' && in_array($field_name, $selected_fields))) {
                        $output .= apply_filters('jobsearch_form_custom_field_video_load', '', $post_id, $custom_field_saved_data, $fields_prefix, $con_class . $con_class_ext, $f_display);
                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "linkurl" && ($field_name != '' && in_array($field_name, $selected_fields))) {
                        $output .= apply_filters('jobsearch_form_custom_field_linkurl_load', '', $post_id, $custom_field_saved_data, $fields_prefix, $con_class . $con_class_ext, $f_display);
                    } elseif (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "upload_file" && ($field_name != '' && in_array($field_name, $selected_fields))) {
                        $output .= apply_filters('jobsearch_form_custom_field_upload_file_load', '', $post_id, $custom_field_saved_data, $fields_prefix, $con_class . $con_class_ext, $f_display);
                    } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "email" && ($field_name != '' && in_array($field_name, $selected_fields))) {
                        $output .= apply_filters('jobsearch_form_custom_field_email_load', '', $post_id, $custom_field_saved_data, $fields_prefix, $con_class . $con_class_ext, $f_display);
                    } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "textarea" && ($field_name != '' && in_array($field_name, $selected_fields))) {
                        $output .= apply_filters('jobsearch_form_custom_field_textarea_load', '', $post_id, $custom_field_saved_data, $fields_prefix, $con_class . $con_class_ext, $f_display);
                    } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "date" && ($field_name != '' && in_array($field_name, $selected_fields))) {
                        $output .= apply_filters('jobsearch_form_custom_field_date_load', '', $post_id, $custom_field_saved_data, $fields_prefix, $con_class . $con_class_ext, $f_display);
                    } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "number" && ($field_name != '' && in_array($field_name, $selected_fields))) {
                        $output .= apply_filters('jobsearch_form_custom_field_number_load', '', $post_id, $custom_field_saved_data, $fields_prefix, $con_class . $con_class_ext, $f_display);
                    } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "range" && ($field_name != '' && in_array($field_name, $selected_fields))) {
                        $output .= apply_filters('jobsearch_form_custom_field_range_load', '', $post_id, $custom_field_saved_data, $fields_prefix, $con_class . $con_class_ext, $f_display);
                    } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "checkbox" && ($field_name != '' && in_array($field_name, $selected_fields))) {
                        $output .= apply_filters('jobsearch_form_custom_field_checkbox_load', '', $post_id, $custom_field_saved_data, $fields_prefix, $con_class . $con_class_ext, $f_display);
                    } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "dropdown" && ($field_name != '' && in_array($field_name, $selected_fields))) {
                        $output .= apply_filters('jobsearch_form_custom_field_dropdown_load', '', $post_id, $custom_field_saved_data, $fields_prefix, $con_class . $con_class_ext, $f_display);
                    } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "dependent_dropdown" && ($field_name != '' && in_array($field_name, $selected_fields))) {
                        $output .= apply_filters('jobsearch_form_custom_field_dependent_dropdown_load', '', $post_id, $custom_field_saved_data, $fields_prefix, $con_class . $con_class_ext, $f_display);
                    } else if (isset($custom_field_saved_data['type']) && $custom_field_saved_data['type'] == "dependent_fields" && ($field_name != '' && in_array($field_name, $selected_fields))) {
                        $output .= apply_filters('jobsearch_form_custom_field_dependent_fields_load', '', $post_id, $custom_field_saved_data, $fields_prefix, $f_key, 'signup', '', $con_class . $con_class_ext, $f_display);
                    }
                }

                echo($output);
            }
        }
    }

    public function register_custom_fields_error($post_id, $custom_field_entity)
    {
        global $jobsearch_plugin_options;

        $reg_custom_fields = isset($jobsearch_plugin_options['signup_custom_fields']) ? $jobsearch_plugin_options['signup_custom_fields'] : '';

        if ($reg_custom_fields == 'on') {

            if ($custom_field_entity == 'employer') {
                $selected_fields = isset($jobsearch_plugin_options['employer_custom_fields']) ? $jobsearch_plugin_options['employer_custom_fields'] : '';
                $con_class = 'employer-cus-field';
            } else {
                $selected_fields = isset($jobsearch_plugin_options['candidate_custom_fields']) ? $jobsearch_plugin_options['candidate_custom_fields'] : '';
                $con_class = 'candidate-cus-field';
            }

            // load all saved fields
            $field_db_slug = "jobsearch_custom_field_" . $custom_field_entity;
            $custom_all_fields_saved_data = get_option($field_db_slug);
            $count_node = time();
            $all_fields_name_str = '';
            if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0 && !empty($selected_fields)) {
                $field_names_counter = 0;
                $fields_prefix = '';
                $output = '';
                foreach ($custom_all_fields_saved_data as $f_key => $custom_field_saved_data) {
                    $field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
                    $field_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
                    $field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
                    $field_type = isset($custom_field_saved_data['type']) ? $custom_field_saved_data['type'] : '';

                    if ($field_name != '' && in_array($field_name, $selected_fields)) {
                        if ($field_required == 'yes') {
                            if ($field_type == 'checkbox' && (!isset($_POST[$field_name]) || (isset($_POST[$field_name]) && $_POST[$field_name] == ''))) {
                                echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . sprintf(__('%s value should not be blank.', 'wp-jobsearch'), $field_label) . '</div>'));
                                die();
                            } else if ($field_type == 'upload_file' && isset($_FILES[$field_name])) {
                                $uploding_attach_files = $_FILES[$field_name];
                                if (isset($uploding_attach_files['name'][0]) && $uploding_attach_files['name'][0] == '') {
                                    echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . sprintf(__('%s value should not be blank.', 'wp-jobsearch'), $field_label) . '</div>'));
                                    die();
                                }
                            } else {
                                if (isset($_POST[$field_name]) && empty($_POST[$field_name])) {
                                    echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . sprintf(__('%s value should not be blank.', 'wp-jobsearch'), $field_label) . '</div>'));
                                    die();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    static function jobsearch_form_custom_field_text_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $con_class = '', $f_display = '', $self_vals = array())
    {

        global $sitepress;

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $text_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $text_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $text_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $text_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $text_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $text_field_required_str = '';
        if ($text_field_required == 'yes') {
            $text_field_required_str = 'required="required"';
        }

        $con_clas_attr = '';
        if ($con_class != '') {
            $con_clas_attr = ' class="' . $con_class . '"';
        }

        $con_f_style = '';
        if ($f_display != '') {
            $con_f_style = ' style="display: ' . $f_display . ';"';
        }

        // get db value if saved
        $text_field_name_db_val = get_post_meta($post_id, $text_field_name, true);
        if (isset($self_vals[$text_field_name])) {
            $text_field_name_db_val = $self_vals[$text_field_name];
        }

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Text Field Label - ' . $text_field_label, $text_field_label);

        $text_field_placeholder = apply_filters('wpml_translate_single_string', $text_field_placeholder, 'Custom Fields', 'Text Field Placeholder - ' . $text_field_placeholder, $lang_code);
        ?>
        <li<?php echo($con_clas_attr) ?><?php echo($con_f_style) ?>>
            <label><?php echo apply_filters('wpml_translate_single_string', $text_field_label, 'Custom Fields', 'Text Field Label - ' . $text_field_label, $lang_code) ?><?php echo($text_field_required == 'yes' ? '*' : '') ?></label>
            <input type="text" name="<?php echo jobsearch_esc_html($text_field_name) ?>"
                   class="<?php echo jobsearch_esc_html($text_field_classes) ?>"
                   placeholder="<?php echo jobsearch_esc_html($text_field_placeholder) ?>" <?php echo force_balance_tags($text_field_required_str) ?>
                   value="<?php echo jobsearch_esc_html($text_field_name_db_val) ?>"/>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_form_custom_field_video_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $con_class = '', $f_display = '', $self_vals = array())
    {

        global $sitepress;

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $video_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $video_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $video_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $video_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $video_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $video_field_required_str = '';
        if ($video_field_required == 'yes') {
            $video_field_required_str = 'required="required"';
        }

        $con_clas_attr = '';
        if ($con_class != '') {
            $con_clas_attr = ' class="' . $con_class . '"';
        }

        $con_f_style = '';
        if ($f_display != '') {
            $con_f_style = ' style="display: ' . $f_display . ';"';
        }

        // get db value if saved
        $video_field_name_db_val = get_post_meta($post_id, $video_field_name, true);
        if (isset($self_vals[$video_field_name])) {
            $video_field_name_db_val = $self_vals[$video_field_name];
        }

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Text Field Label - ' . $video_field_label, $video_field_label);

        $video_field_placeholder = apply_filters('wpml_translate_single_string', $video_field_placeholder, 'Custom Fields', 'Video Field Placeholder - ' . $video_field_placeholder, $lang_code);
        ?>
        <li<?php echo($con_clas_attr) ?><?php echo($con_f_style) ?>>
            <label><?php echo apply_filters('wpml_translate_single_string', $video_field_label, 'Custom Fields', 'Video Field Label - ' . $video_field_label, $lang_code) ?><?php echo($video_field_required == 'yes' ? '*' : '') ?></label>
            <input type="text" name="<?php echo jobsearch_esc_html($video_field_name) ?>"
                   class="<?php echo jobsearch_esc_html($video_field_classes) ?>"
                   placeholder="<?php echo jobsearch_esc_html($video_field_placeholder) ?>" <?php echo force_balance_tags($video_field_required_str) ?>
                   value="<?php echo jobsearch_esc_html($video_field_name_db_val) ?>"/>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_form_custom_field_linkurl_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $con_class = '', $f_display = '', $self_vals = array())
    {

        global $sitepress;

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $linkurl_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $linkurl_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $linkurl_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $linkurl_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $linkurl_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $linkurl_field_required_str = '';
        if ($linkurl_field_required == 'yes') {
            $linkurl_field_required_str = 'required="required"';
        }

        $con_clas_attr = '';
        if ($con_class != '') {
            $con_clas_attr = ' class="' . $con_class . '"';
        }

        $con_f_style = '';
        if ($f_display != '') {
            $con_f_style = ' style="display: ' . $f_display . ';"';
        }

        // get db value if saved
        $linkurl_field_name_db_val = get_post_meta($post_id, $linkurl_field_name, true);
        if (isset($self_vals[$linkurl_field_name])) {
            $linkurl_field_name_db_val = $self_vals[$linkurl_field_name];
        }

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Text Field Label - ' . $linkurl_field_label, $linkurl_field_label);
        $linkurl_field_placeholder = apply_filters('wpml_translate_single_string', $linkurl_field_placeholder, 'Custom Fields', 'URL Field Placeholder - ' . $linkurl_field_placeholder, $lang_code);
        ?>
        <li<?php echo($con_clas_attr) ?><?php echo($con_f_style) ?>>
            <label><?php echo apply_filters('wpml_translate_single_string', $linkurl_field_label, 'Custom Fields', 'URL Field Label - ' . $linkurl_field_label, $lang_code) ?><?php echo($linkurl_field_required == 'yes' ? '*' : '') ?></label>
            <input type="text" name="<?php echo jobsearch_esc_html($linkurl_field_name) ?>"
                   class="<?php echo jobsearch_esc_html($linkurl_field_classes) ?>"
                   placeholder="<?php echo jobsearch_esc_html($linkurl_field_placeholder) ?>" <?php echo force_balance_tags($linkurl_field_required_str) ?>
                   value="<?php echo jobsearch_esc_html($linkurl_field_name_db_val) ?>"/>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_form_custom_field_upload_file_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $con_class = '', $f_display = '', $self_vals = array())
    {

        global $sitepress;
        $rand_num = rand(10000000, 99999999);

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $upload_file_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $upload_file_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $upload_file_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $upload_file_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';

        $upload_field_multifiles = isset($custom_field_saved_data['multi_files']) ? $custom_field_saved_data['multi_files'] : '';
        $upload_field_numof_files = isset($custom_field_saved_data['numof_files']) ? $custom_field_saved_data['numof_files'] : '';
        $upload_field_numof_files = $upload_field_numof_files > 0 ? $upload_field_numof_files : 5;
        $upload_field_allow_types = isset($custom_field_saved_data['allow_types']) ? $custom_field_saved_data['allow_types'] : '';
        $upload_field_allow_types = !empty($upload_field_allow_types) ? $upload_field_allow_types : array();
        $upload_field_file_size = isset($custom_field_saved_data['file_size']) ? $custom_field_saved_data['file_size'] : '';
        $upload_field_file_size = $upload_field_file_size == '' ? '5MB' : $upload_field_file_size;

        $upload_file_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $upload_file_field_required_str = '';
        if ($upload_file_field_required == 'yes') {
            $upload_file_field_required_str = 'required="required"';
        }

        $con_clas_attr = '';
        if ($con_class != '') {
            $con_clas_attr = ' class="' . $con_class . '"';
        }

        $con_f_style = '';
        if ($f_display != '') {
            $con_f_style = ' style="display: ' . $f_display . ';"';
        }

        // get db value if saved
        $upload_file_field_name_db_val = get_post_meta($post_id, $upload_file_field_name, true);
        if (isset($self_vals[$upload_file_field_name])) {
            $upload_file_field_name_db_val = $self_vals[$upload_file_field_name];
        }

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Text Field Label - ' . $upload_file_field_label, $upload_file_field_label);

        if ($upload_field_multifiles != 'yes') {
            $upload_field_numof_files = 1;
        }

        $uplod_file_size_num = abs((int)filter_var($upload_field_file_size, FILTER_SANITIZE_NUMBER_INT));
        $uplod_file_size_num = $uplod_file_size_num > 0 ? $uplod_file_size_num : 5;
        $uplod_file_size = $uplod_file_size_num * 1024;
        ?>
        <li<?php echo($con_clas_attr) ?><?php echo($con_f_style) ?>>
            <label><?php echo apply_filters('wpml_translate_single_string', $upload_file_field_label, 'Custom Fields', 'Text Field Label - ' . $upload_file_field_label, $lang_code) ?><?php echo($upload_file_field_required == 'yes' ? ' *' : '') ?></label>
            <div class="jobsearch-fileUpload">
                <span><i class="jobsearch-icon jobsearch-upload"></i> <?php echo($upload_field_numof_files > 1 ? esc_html__('Upload Files', 'wp-jobsearch') : esc_html__('Upload File', 'wp-jobsearch')); ?></span>
                <input name="<?php echo jobsearch_esc_html($upload_file_field_name) ?>[]" type="file"
                       class="upload jobsearch-upload jobsearch-uploadfile-field" multiple="multiple"
                       onchange="jobsearch_job_attach_files_url_<?php echo($rand_num) ?>(event)"/>
                <?php if ($upload_field_numof_files > 1) { ?>
                    <div class="jobsearch-fileUpload-info">
                        <p><?php printf(esc_html__('You can upload up to %s files.', 'wp-jobsearch'), $upload_field_numof_files); ?></p>
                    </div>
                <?php } ?>
            </div>
            <div id="field-files-holder-<?php echo($rand_num) ?>" class="uplodfield-files-holder"></div>
            <script type="text/javascript">
                function jobsearch_job_attach_files_url_<?php echo($rand_num) ?>(event) {

                    if (window.File && window.FileList && window.FileReader) {

                        var file_types_str = '<?php echo implode('|', $upload_field_allow_types) ?>';
                        if (file_types_str != '') {
                            var file_types_array = file_types_str.split('|');
                        } else {
                            var file_types_array = [];
                        }
                        var file_allow_size = '<?php echo($uplod_file_size) ?>';
                        var num_files_allow = '<?php echo($upload_field_numof_files) ?>';
                        file_allow_size = parseInt(file_allow_size);
                        num_files_allow = parseInt(num_files_allow);
                        jQuery('#field-files-holder-<?php echo($rand_num) ?>').find('.adding-file').remove();
                        var files = event.target.files;
                        for (var i = 0; i < files.length; i++) {

                            var _file = files[i];
                            var file_type = _file.type;
                            var file_size = _file.size;
                            var file_name = _file.name;
                            file_size = parseFloat(file_size / 1024).toFixed(2);
                            if (file_size <= file_allow_size) {
                                if (file_types_array.indexOf(file_type) >= 0) {
                                    var file_icon = 'fa fa-file-text-o';
                                    if (file_type == 'image/png' || file_type == 'image/jpeg') {
                                        file_icon = 'fa fa-file-image-o';
                                    } else if (file_type == 'application/msword' || file_type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                        file_icon = 'fa fa-file-word-o';
                                    } else if (file_type == 'application/vnd.ms-excel' || file_type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                        file_icon = 'fa fa-file-excel-o';
                                    } else if (file_type == 'application/pdf') {
                                        file_icon = 'fa fa-file-pdf-o';
                                    }

                                    var rand_number = Math.floor((Math.random() * 99999999) + 1);
                                    var ihtml = '\
                                    <div class="jobsearch-column-3 adding-file">\
                                        <div class="file-container">\
                                            <a><i class="' + file_icon + '"></i> ' + file_name + '</a>\
                                        </div>\
                                    </div>';
                                    jQuery('#field-files-holder-<?php echo($rand_num) ?>').append(ihtml);
                                } else {
                                    alert('<?php esc_html_e('This File type is not allowed.') ?>');
                                    return false;
                                }
                            } else {
                                alert('<?php esc_html_e('The file size is too large.') ?>');
                                return false;
                            }

                            if (i == (num_files_allow - 1)) {
                                return false;
                            }
                        }
                    }
                }
            </script>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_form_custom_field_email_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $con_class = '', $f_display = '', $self_vals = array())
    {

        global $sitepress;

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $email_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $email_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $email_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $email_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $email_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $email_field_required_str = '';
        if ($email_field_required == 'yes') {
            $email_field_required_str = 'required="required"';
        }

        $con_clas_attr = '';
        if ($con_class != '') {
            $con_clas_attr = ' class="' . $con_class . '"';
        }

        $con_f_style = '';
        if ($f_display != '') {
            $con_f_style = ' style="display: ' . $f_display . ';"';
        }

        // get db value if saved
        $email_field_name_db_val = get_post_meta($post_id, $email_field_name, true);
        if (isset($self_vals[$email_field_name])) {
            $email_field_name_db_val = $self_vals[$email_field_name];
        }

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Email Field Label - ' . $email_field_label, $email_field_label);
        $email_field_placeholder = apply_filters('wpml_translate_single_string', $email_field_placeholder, 'Custom Fields', 'Email Field Placeholder - ' . $email_field_placeholder, $lang_code);
        ?>
        <li<?php echo($con_clas_attr) ?><?php echo($con_f_style) ?>>
            <label><?php echo apply_filters('wpml_translate_single_string', $email_field_label, 'Custom Fields', 'Email Field Label - ' . $email_field_label, $lang_code) ?><?php echo($email_field_required == 'yes' ? '*' : '') ?></label>
            <input type="email" name="<?php echo jobsearch_esc_html($email_field_name) ?>"
                   class="<?php echo jobsearch_esc_html($email_field_classes) ?>"
                   placeholder="<?php echo jobsearch_esc_html($email_field_placeholder) ?>" <?php echo force_balance_tags($email_field_required_str) ?>
                   value="<?php echo jobsearch_esc_html($email_field_name_db_val) ?>"/>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_form_custom_field_number_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $con_class = '', $f_display = '', $self_vals = array())
    {

        global $sitepress;

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $number_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $number_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $number_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $number_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $number_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $number_field_required_str = '';
        if ($number_field_required == 'yes') {
            $number_field_required_str = 'required="required"';
        }

        $con_clas_attr = '';
        if ($con_class != '') {
            $con_clas_attr = ' class="' . $con_class . '"';
        }

        $con_f_style = '';
        if ($f_display != '') {
            $con_f_style = ' style="display: ' . $f_display . ';"';
        }

        // get db value if saved
        $number_field_name_db_val = get_post_meta($post_id, $number_field_name, true);
        if (isset($self_vals[$number_field_name])) {
            $number_field_name_db_val = $self_vals[$number_field_name];
        }

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Number Field Label - ' . $number_field_label, $number_field_label);
        $number_field_placeholder = apply_filters('wpml_translate_single_string', $number_field_placeholder, 'Custom Fields', 'Number Field Placeholder - ' . $number_field_placeholder, $lang_code);
        ?>
        <li<?php echo($con_clas_attr) ?><?php echo($con_f_style) ?>>
            <label><?php echo apply_filters('wpml_translate_single_string', $number_field_label, 'Custom Fields', 'Number Field Label - ' . $number_field_label, $lang_code) ?><?php echo($number_field_required == 'yes' ? '*' : '') ?></label>
            <input type="number" name="<?php echo jobsearch_esc_html($number_field_name) ?>"
                   class="<?php echo jobsearch_esc_html($number_field_classes) ?>"
                   placeholder="<?php echo jobsearch_esc_html($number_field_placeholder) ?>" <?php echo force_balance_tags($number_field_required_str) ?>
                   value="<?php echo jobsearch_esc_html($number_field_name_db_val) ?>"/>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_form_custom_field_date_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $con_class = '', $f_display = '', $self_vals = array())
    {

        global $sitepress;

        ob_start();
        $field_rand_id = rand(454, 999999);
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $date_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $date_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $date_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $date_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $date_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $date_field_date_format = isset($custom_field_saved_data['date-format']) ? $custom_field_saved_data['date-format'] : 'd-m-Y';
        $date_field_required_str = '';
        if ($date_field_required == 'yes') {
            $date_field_required_str = 'required="required"';
        }

        $con_clas_attr = '';
        if ($con_class != '') {
            $con_clas_attr = ' class="' . $con_class . '"';
        }

        $con_f_style = '';
        if ($f_display != '') {
            $con_f_style = ' style="display: ' . $f_display . ';"';
        }

        // get db value if saved
        $date_field_name_db_val = get_post_meta($post_id, $date_field_name, true);
        if (isset($self_vals[$date_field_name])) {
            $date_field_name_db_val = $self_vals[$date_field_name];
        }
        if ($date_field_name_db_val != '') {
            $date_field_name_db_val = date($date_field_date_format, $date_field_name_db_val);
        }

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Date Field Label - ' . $date_field_label, $date_field_label);
        $date_field_placeholder = apply_filters('wpml_translate_single_string', $date_field_placeholder, 'Custom Fields', 'Date Field Placeholder - ' . $date_field_placeholder, $lang_code);
        ?>
        <li<?php echo($con_clas_attr) ?><?php echo($con_f_style) ?>>
            <label><?php echo apply_filters('wpml_translate_single_string', $date_field_label, 'Custom Fields', 'Date Field Label - ' . $date_field_label, $lang_code) ?><?php echo($date_field_required == 'yes' ? '*' : '') ?></label>
            <input type="text" id="<?php echo jobsearch_esc_html($date_field_name . $field_rand_id) ?>"
                   name="<?php echo jobsearch_esc_html($date_field_name) ?>"
                   class="<?php echo jobsearch_esc_html($date_field_classes) ?>"
                   placeholder="<?php echo jobsearch_esc_html($date_field_placeholder) ?>" <?php echo force_balance_tags($date_field_required_str) ?>
                   value="<?php echo jobsearch_esc_html($date_field_name_db_val) ?>"/>
        </li>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery('#<?php echo jobsearch_esc_html($date_field_name . $field_rand_id) ?>').datetimepicker({
                    format: '<?php echo jobsearch_esc_html($date_field_date_format) ?>'
                });
            });
        </script>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_form_custom_field_range_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $con_class = '', $f_display = '', $self_vals = array())
    {
        global $sitepress;

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $range_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $range_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $range_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $range_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $range_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $range_field_min = isset($custom_field_saved_data['min']) ? $custom_field_saved_data['min'] : '0';
        $range_field_laps = isset($custom_field_saved_data['laps']) ? $custom_field_saved_data['laps'] : '20';
        $range_field_interval = isset($custom_field_saved_data['interval']) ? $custom_field_saved_data['interval'] : '10000';
        $rand_id = rand(123, 123467);
        $range_field_required_str = '';
        if ($range_field_required == 'yes') {
            $range_field_required_str = 'required="required"';
        }

        $con_clas_attr = '';
        if ($con_class != '') {
            $con_clas_attr = $con_class;
        }

        $con_f_style = '';
        if ($f_display != '') {
            $con_f_style = ' style="display: ' . $f_display . ';"';
        }

        // get db value if saved
        $range_field_name_db_val = get_post_meta($post_id, $range_field_name, true);
        if (isset($self_vals[$range_field_name])) {
            $range_field_name_db_val = $self_vals[$range_field_name];
        }

        wp_enqueue_style('jquery-ui');
        wp_enqueue_script('jquery-ui');
        $range_field_max = $range_field_min;
        $i = 0;
        while ($range_field_laps > $i) {
            $range_field_max = $range_field_max + $range_field_interval;
            $i++;
        }

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Range Field Label - ' . $range_field_label, $range_field_label);
        ?>
        <li class="range-in-user-form <?php echo($con_clas_attr) ?>"<?php echo($con_f_style) ?>>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery("#slider-range<?php echo jobsearch_esc_html($range_field_name . $rand_id) ?>").slider({
                        range: "max",
                        min: <?php echo absint($range_field_min); ?>,
                        max: <?php echo absint($range_field_max); ?>,
                        value: <?php echo absint($range_field_name_db_val); ?>,
                        slide: function (event, ui) {
                            jQuery("#<?php echo jobsearch_esc_html($range_field_name . $rand_id) ?>").val(ui.value);
                        }
                    });
                    jQuery("#<?php echo jobsearch_esc_html($range_field_name . $rand_id) ?>").val(jQuery("#slider-range<?php echo jobsearch_esc_html($range_field_name . $rand_id) ?>").slider("value"));
                });
            </script>
            <label><?php echo apply_filters('wpml_translate_single_string', $range_field_label, 'Custom Fields', 'Range Field Label - ' . $range_field_label, $lang_code) ?><?php echo($range_field_required == 'yes' ? '*' : '') ?></label>
            <input type="text" id="<?php echo jobsearch_esc_html($range_field_name . $rand_id) ?>"
                   name="<?php echo jobsearch_esc_html($range_field_name) ?>" value="" readonly
                   style="border:0; color:#f6931f; font-weight:bold;"/>
            <div id="slider-range<?php echo jobsearch_esc_html($range_field_name . $rand_id) ?>"></div>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    public function jobsearch_form_custom_field_checkbox_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $con_class = '', $f_display = '', $self_vals = array())
    {

        global $sitepress;

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $checkbox_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $checkbox_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $checkbox_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $checkbox_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $field_post_multi = isset($custom_field_saved_data['post-multi']) ? $custom_field_saved_data['post-multi'] : '';
        $checkbox_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $checkbox_field_options = isset($custom_field_saved_data['options']) ? $custom_field_saved_data['options'] : '';
        $checkbox_field_required_str = '';
        if ($checkbox_field_required == 'yes') {
            $checkbox_field_required_str = 'class="required-cussel-field"';
        }

        $con_clases = 'jobsearch-user-form-coltwo-full';
        if ($con_class != '') {
            $con_clases .= ' ' . $con_class;
        }

        $con_clas_attr = 'class="' . $con_clases . '"';

        $con_f_style = '';
        if ($f_display != '') {
            $con_f_style = ' style="display: ' . $f_display . ';"';
        }

        // get db value if saved
        $checkbox_field_name_db_val = get_post_meta($post_id, $checkbox_field_name, true);
        if (isset($self_vals[$checkbox_field_name])) {
            $checkbox_field_name_db_val = $self_vals[$checkbox_field_name];
        }
        // creat options string

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Dropdown Field Label - ' . $checkbox_field_label, $checkbox_field_label);
        ?>
        <li <?php echo($con_clas_attr) ?><?php echo($con_f_style) ?>>
            <label><?php echo apply_filters('wpml_translate_single_string', $checkbox_field_label, 'Custom Fields', 'Dropdown Field Label - ' . $checkbox_field_label, $lang_code) ?><?php echo($checkbox_field_required == 'yes' ? '*' : '') ?></label>
            <?php
            if (isset($checkbox_field_options['value']) && count($checkbox_field_options['value']) > 0) {
                $option_counter = 0;
                ?>
                <div class="jobsearch-cusfield-checkbox <?php echo jobsearch_esc_html($checkbox_field_classes) ?>">
                    <?php
                    foreach ($checkbox_field_options['value'] as $option) {
                        if ($option != '') {
                            $option = ltrim(rtrim($option));
                            if ($checkbox_field_options['label'][$option_counter] != '' && str_replace(" ", "-", $option) != '') {
                                //$option_val = strtolower(str_replace(" ", "-", $option));
                                $option_val = $option;
                                $option_label = $checkbox_field_options['label'][$option_counter];
                                if (is_array($checkbox_field_name_db_val)) {
                                    $option_selected = in_array($option_val, $checkbox_field_name_db_val) ? ' checked="checked"' : '';
                                } else {
                                    $option_selected = $checkbox_field_name_db_val == $option_val ? ' checked="checked"' : '';
                                }
                                if ($field_post_multi == 'yes') {
                                    ?>
                                    <div class="cusfield-checkbox-radioitm jobsearch-checkbox">
                                        <input id="opt-<?php echo($checkbox_field_name . '-' . $option_counter) ?>" <?php echo($checkbox_field_required_str) ?>
                                               type="checkbox" name="<?php echo($checkbox_field_name) ?>[]"
                                               value="<?php echo($option_val) ?>" <?php echo($option_selected) ?>>
                                        <label for="opt-<?php echo($checkbox_field_name . '-' . $option_counter) ?>">
                                            <span></span> <?php echo($option_label) ?>
                                        </label>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="cusfield-checkbox-radioitm jobsearch-checkbox">
                                        <input id="opt-<?php echo($checkbox_field_name . '-' . $option_counter) ?>" <?php echo($checkbox_field_required_str) ?>
                                               type="radio" name="<?php echo($checkbox_field_name) ?>"
                                               value="<?php echo($option_val) ?>" <?php echo($option_selected) ?>>
                                        <label for="opt-<?php echo($checkbox_field_name . '-' . $option_counter) ?>">
                                            <span></span> <?php echo($option_label) ?>
                                        </label>
                                    </div>
                                    <?php
                                }
                            }
                        }
                        $option_counter++;
                    }
                    ?>
                </div>
                <?php
            } else {
                ?>
                <span><?php echo jobsearch_esc_html('Field did not configure properly', 'wp-jobsearch'); ?></span>
                <?php
            }
            ?>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_form_custom_field_dropdown_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $con_class = '', $f_display = '', $self_vals = array())
    {

        global $sitepress;

        //
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $dropdown_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $dropdown_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $dropdown_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $dropdown_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $field_post_multi = isset($custom_field_saved_data['post-multi']) ? $custom_field_saved_data['post-multi'] : '';
        $dropdown_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $dropdown_field_options = isset($custom_field_saved_data['options']) ? $custom_field_saved_data['options'] : '';
        $dropdown_field_required_str = '';
        if ($dropdown_field_required == 'yes') {
            $dropdown_field_required_str = 'required-cussel-field';
        }

        $con_clas_attr = '';
        if ($con_class != '') {
            $con_clas_attr = ' class="' . $con_class . '"';
        }

        $con_f_style = '';
        if ($f_display != '') {
            $con_f_style = ' style="display: ' . $f_display . ';"';
        }

        // get db value if saved
        $dropdown_field_name_db_val = get_post_meta($post_id, $dropdown_field_name, true);
        if (isset($self_vals[$dropdown_field_name])) {
            $dropdown_field_name_db_val = $self_vals[$dropdown_field_name];
        }

        // creat options string
        $dropdown_field_options_str = '';
        if ($dropdown_field_placeholder != '') {
            $dropdown_field_options_str = '<option value="">' . $dropdown_field_placeholder . '</option>';
        }
        if (isset($dropdown_field_options['value']) && count($dropdown_field_options['value']) > 0) {
            $option_counter = 0;
            foreach ($dropdown_field_options['value'] as $option) {
                if ($option != '') {
                    $option = ltrim(rtrim($option));
                    if ($dropdown_field_options['label'][$option_counter] != '' && str_replace(" ", "-", $option) != '') {
                        //$option_val = strtolower(str_replace(" ", "-", $option));
                        $option_val = $option;
                        $option_label = $dropdown_field_options['label'][$option_counter];
                        $option_label = stripslashes($option_label);
                        $option_selected = $dropdown_field_name_db_val == $option_val ? ' selected="selected"' : '';

                        do_action('wpml_register_single_string', 'Custom Fields', 'Dropdown Option Label - ' . $option_label, $option_label);

                        $dropdown_field_options_str .= '<option ' . force_balance_tags($option_selected) . ' value="' . esc_html($option_val) . '">' . apply_filters('wpml_translate_single_string', $option_label, 'Custom Fields', 'Dropdown Option Label - ' . $option_label, $lang_code) . '</option>';
                    }
                }
                $option_counter++;
            }
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Dropdown Field Label - ' . $dropdown_field_label, $dropdown_field_label);
        $dropdown_field_placeholder = apply_filters('wpml_translate_single_string', $dropdown_field_placeholder, 'Custom Fields', 'Dropdown Field Placeholder - ' . $dropdown_field_placeholder, $lang_code);
        
        ob_start();
        ?>
        <li<?php echo($con_clas_attr) ?><?php echo($con_f_style) ?>>
            <label><?php echo apply_filters('wpml_translate_single_string', $dropdown_field_label, 'Custom Fields', 'Dropdown Field Label - ' . $dropdown_field_label, $lang_code) ?><?php echo($dropdown_field_required == 'yes' ? '*' : '') ?></label>
            <?php
            if ($dropdown_field_options_str != '') {
                ?>
                <div class="jobsearch-profile-select">
                    <select
                        <?php echo($field_post_multi == 'yes' ? 'multiple="multiple" ' : '') ?>name="<?php echo jobsearch_esc_html($dropdown_field_name) ?><?php echo($field_post_multi == 'yes' ? '[]' : '') ?>" <?php echo($dropdown_field_placeholder != '' ? 'placeholder="' . $dropdown_field_placeholder . '"' : '') ?>
                        class="<?php echo jobsearch_esc_html($dropdown_field_classes) ?> selectize-select <?php echo($dropdown_field_required_str) ?>">
                        <?php
                        echo force_balance_tags($dropdown_field_options_str);
                        ?>
                    </select>
                </div>
                <?php
            } else {
                ?>
                <span><?php echo jobsearch_esc_html('Field did not configure properly', 'wp-jobsearch'); ?></span>
                <?php
            }
            ?>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_form_custom_field_dependent_dropdown_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $con_class = '', $f_display = '', $self_vals = array())
    {
        global $sitepress;

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $dropdown_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $dropdown_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $dropdown_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $dropdown_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $dropdown_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $dropdown_field_options = isset($custom_field_saved_data['options_list']) ? $custom_field_saved_data['options_list'] : '';
        $dropdown_cont_optsid = isset($custom_field_saved_data['options_list_id']) && $custom_field_saved_data['options_list_id'] != '' ? $custom_field_saved_data['options_list_id'] : 0;

        $dropdown_field_label = apply_filters('wpml_translate_single_string', $dropdown_field_label, 'Custom Fields', 'Dropdown Field Label - ' . $dropdown_field_label, $lang_code);

        $dropdown_field_required_str = '';
        if ($dropdown_field_required == 'yes') {
            $dropdown_field_required_str = 'required="required"';
        }

        $con_clas_attr = '';
        if ($con_class != '') {
            $con_clas_attr = $con_class;
        }

        $con_f_style = '';
        if ($f_display != '') {
            $con_f_style = ' style="display: ' . $f_display . ';"';
        }

        // get db value if saved
        $dropdown_field_name_db_val = get_post_meta($post_id, $dropdown_field_name, true);
        if (isset($self_vals[$dropdown_field_name])) {
            $dropdown_field_name_db_val = $self_vals[$dropdown_field_name];
        }

        ?>
        <li class="jobsearch-user-form-coltwo-full jobsearch-depdrpdwn-fields <?php echo($dropdown_field_classes) ?><?php echo($con_clas_attr) ?>"<?php echo($con_f_style) ?>>
            <ul class="jobsearch-row">
                <?php
                $depdrpdwn_fields = jobsearch_dependent_dropdown_list_html($dropdown_field_options, $dropdown_cont_optsid, $custom_field_saved_data, $dropdown_field_name_db_val, 0, 'dashboard', $dropdown_field_label);
                echo($depdrpdwn_fields);
                ?>
            </ul>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_form_custom_field_textarea_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $con_class = '', $f_display = '', $self_vals = array())
    {

        global $sitepress;

        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $textarea_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
        $textarea_field_name = isset($custom_field_saved_data['name']) ? $fields_prefix . $custom_field_saved_data['name'] : '';
        $textarea_field_classes = isset($custom_field_saved_data['classes']) ? $custom_field_saved_data['classes'] : '';
        $textarea_field_placeholder = isset($custom_field_saved_data['placeholder']) ? $custom_field_saved_data['placeholder'] : '';
        $textarea_field_rich_editor = isset($custom_field_saved_data['rich_editor']) ? $custom_field_saved_data['rich_editor'] : '';
        $textarea_field_required = isset($custom_field_saved_data['required']) ? $custom_field_saved_data['required'] : '';
        $textarea_field_required_str = '';
        if ($textarea_field_required == 'yes') {
            $textarea_field_required_str = 'required="required"';
            $textarea_field_classes .= ' required-cussel-field';
        }

        $con_clas_attr = '';
        if ($con_class != '') {
            $con_clas_attr = $con_class;
        }

        $con_f_style = '';
        if ($f_display != '') {
            $con_f_style = ' style="display: ' . $f_display . ';"';
        }

        // get db value if saved
        $textarea_field_name_db_val = get_post_meta($post_id, $textarea_field_name, true);
        if (isset($self_vals[$textarea_field_name])) {
            $textarea_field_name_db_val = $self_vals[$textarea_field_name];
        }

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Textarea Field Label - ' . $textarea_field_label, $textarea_field_label);
        ?>
        <li class="jobsearch-user-form-coltwo-full form-textarea <?php echo($con_clas_attr) ?>"<?php echo($con_f_style) ?>>
            <label><?php echo apply_filters('wpml_translate_single_string', $textarea_field_label, 'Custom Fields', 'Textarea Field Label - ' . $textarea_field_label, $lang_code) ?><?php echo($textarea_field_required == 'yes' ? '*' : '') ?></label>
            <?php
            if ($textarea_field_rich_editor == 'no') {
                ?>
                <textarea
                        name="<?php echo($textarea_field_name) ?>" <?php echo($textarea_field_required_str) ?><?php echo($textarea_field_classes != '' ? ' class="' . $textarea_field_classes . '"' : '') ?><?php echo($textarea_field_placeholder != '' ? ' placeholder="' . $textarea_field_placeholder . '"' : '') ?>><?php echo($textarea_field_name_db_val) ?></textarea>
                <?php
            } else {
                $wped_settings = array(
                    'media_buttons' => false,
                    'editor_class' => $textarea_field_classes,
                    'quicktags' => array('buttons' => 'strong,em,del,ul,ol,li,close'),
                    'tinymce' => array(
                        'toolbar1' => 'bold,bullist,numlist,italic,underline,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                        'toolbar2' => '',
                        'toolbar3' => '',
                    ),
                );
                wp_editor($textarea_field_name_db_val, $textarea_field_name, $wped_settings);
            }
            ?>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_form_custom_field_heading_load_callback($html, $post_id, $custom_field_saved_data, $fields_prefix, $con_class = '', $f_display = '', $self_vals = array())
    {
        global $sitepress;
        ob_start();
        $field_for_wuser = isset($custom_field_saved_data['non_reg_user']) ? $custom_field_saved_data['non_reg_user'] : '';
        $heading_field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';

        $con_clas_attr = '';
        if ($con_class != '') {
            $con_clas_attr = $con_class;
        }

        $con_f_style = '';
        if ($f_display != '') {
            $con_f_style = ' style="display: ' . $f_display . ';"';
        }

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        do_action('wpml_register_single_string', 'Custom Fields', 'Heading Field Label - ' . $heading_field_label, $heading_field_label);
        ?>
        <li class="jobsearch-user-form-coltwo-full <?php echo($con_clas_attr) ?>"<?php echo($con_f_style) ?>>
            <h2><?php echo apply_filters('wpml_translate_single_string', $heading_field_label, 'Custom Fields', 'Heading Field Label - ' . $heading_field_label, $lang_code) ?></h2>
        </li>
        <?php
        $html .= ob_get_clean();

        if ($field_for_wuser == 'non_reg' && is_user_logged_in()) {
            $html = '';
        } else if ($field_for_wuser == 'for_reg' && !is_user_logged_in()) {
            $html = '';
        }

        return $html;
    }

    static function jobsearch_custom_fields_list_callback($custom_field_entity = '', $post_id = '', $custom_fields = array(), $before_html = '<li>', $after_html = '</li>', $fields_number = '', $field_label = true, $field_icon = true, $custom_value_position = true, $prefix = 'jobsearch', $selected_fields = array(), $fields_cus_vals = array())
    {
        global $post, $jobsearch_post_post_types, $sitepress, $jobsearch_cusfdepfields_rendring;
        $candidate_list = isset($custom_fields['candidate_list']) && $custom_fields['candidate_list'] == true ? 'on' : '';
        $dependent_drop_col = isset($custom_fields['dependent_col']) ? $custom_fields['dependent_col'] : 'jobsearch-column-4';

        if ($post_id == '') {
            $post_id = $post->ID;
        }

        $fields_prefix = ''; // 'jobsearch_field_' . $custom_field_entity . '_';
        $content = '';

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        $orig_before_html = $before_html;
        if ((($fields_number != '' && $fields_number > 0) || $fields_number == '') && $custom_field_entity != '') {

            $jobsearch_post_type_cus_fields = get_option("jobsearch_custom_field_" . $custom_field_entity);
            if (isset($selected_fields) && !empty($selected_fields) && is_array($selected_fields) && sizeof($selected_fields) > 0) {
                $jobsearch_post_type_cus_fields = $selected_fields;
            }

            if (is_array($jobsearch_post_type_cus_fields) && isset($jobsearch_post_type_cus_fields) && !empty($jobsearch_post_type_cus_fields)) {
                ob_start();
                $custom_field_flag = 1;
                foreach ($jobsearch_post_type_cus_fields as $cus_fieldvar => $cus_field) {
                    $field_vtype = isset($cus_field['non_reg_user']) ? $cus_field['non_reg_user'] : '';
                    if ($field_vtype == 'admin_view_only') {
                        continue;
                    }

                    $type = isset($cus_field['type']) ? $cus_field['type'] : '';
                    $cus_field_label_arr = isset($cus_field['label']) ? $cus_field['label'] : '';
                    if (isset($cus_field['name']) && $cus_field['name'] <> '') {
                        $field_name = $fields_prefix . $cus_field['name'];
                        $is_field_visible = isset($cus_field['public_visible']) ? $cus_field['public_visible'] : '';
                        if (!empty($fields_cus_vals)) {
                            $cus_field_value_arr = isset($fields_cus_vals[$field_name]) ? $fields_cus_vals[$field_name] : '';
                        } else {
                            $cus_field_value_arr = get_post_meta($post_id, $field_name, true);
                        }
                        //
                        if ($type == 'dependent_dropdown') {
                            $dep_drpdwns_options = isset($cus_field['options_list']) ? $cus_field['options_list'] : '';
                            $dep_drpdwn_valle = jobsearch_dependent_dropdown_showval_html($dep_drpdwns_options, $cus_field, $cus_field_value_arr, '0', 'careerfy', $dependent_drop_col);
                            if ($dep_drpdwn_valle != '') {
                                $cus_field_value_arr = $dep_drpdwn_valle;
                            } else {
                                $cus_field_value_arr = '';
                            }
                        }

                        if ($type == 'dependent_fields') {
                            $depnt_fields_valle = $jobsearch_cusfdepfields_rendring->dependent_fields_showval_html($post_id, $cus_field, $cus_fieldvar, $field_name, $cus_field_value_arr, $orig_before_html, $after_html, $prefix);
                            if ($depnt_fields_valle != '') {
                                $cus_field_value_arr = $depnt_fields_valle;
                            } else {
                                $cus_field_value_arr = '';
                            }
                        }
                        //
                        $cus_field_icon_arr = isset($cus_field['icon']) ? $cus_field['icon'] : '';
                        $cus_format = isset($cus_field['date-format']) ? $cus_field['date-format'] : '';

                        if ($type == 'text') {
                            $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Text Field Label - ' . $cus_field_label_arr, $lang_code);
                        } else if ($type == 'video') {
                            $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Video Field Label - ' . $cus_field_label_arr, $lang_code);
                        } else if ($type == 'linkurl') {
                            $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'URL Field Label - ' . $cus_field_label_arr, $lang_code);
                        } else if ($type == 'email') {
                            $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Email Field Label - ' . $cus_field_label_arr, $lang_code);
                        } else if ($type == 'number') {
                            $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Number Field Label - ' . $cus_field_label_arr, $lang_code);
                        } else if ($type == 'date') {
                            $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Date Field Label - ' . $cus_field_label_arr, $lang_code);
                        } else if ($type == 'dropdown') {
                            $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Dropdown Field Label - ' . $cus_field_label_arr, $lang_code);
                        } else if ($type == 'range') {
                            $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Range Field Label - ' . $cus_field_label_arr, $lang_code);
                        } else if ($type == 'textarea') {
                            $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Textarea Field Label - ' . $cus_field_label_arr, $lang_code);
                        }

                        if (($type == 'textarea' || $type == 'video' || $type == 'dependent_dropdown')) {
                            if (strpos($before_html, $prefix . '-column-4') !== false) {
                                $before_html = str_replace(array($prefix . '-column-4'), array($prefix . '-column-12'), $before_html);
                            } else if (strpos($before_html, 'jobsearch-column-4') !== false) {
                                $before_html = str_replace(array('jobsearch-column-4'), array('jobsearch-column-12'), $before_html);
                            } else if (strpos($before_html, 'careerfy-column-4') !== false) {
                                $before_html = str_replace(array('careerfy-column-4'), array('careerfy-column-12'), $before_html);
                            } else if (strpos($before_html, $prefix . '-column-6') !== false) {
                                $before_html = str_replace(array($prefix . '-column-6'), array($prefix . '-column-12'), $before_html);
                            }
                        } else {
                            $before_html = $orig_before_html;
                        }

                        if ($type == 'dropdown') {
                            $drop_down_arr = array();
                            $cut_field_flag = 0;
                            foreach ($cus_field['options']['value'] as $key => $cus_field_options_value) {
                                $drop_down_arr[$cus_field_options_value] = (apply_filters('wpml_translate_single_string', $cus_field['options']['label'][$cut_field_flag], 'Custom Fields', 'Dropdown Option Label - ' . $cus_field['options']['label'][$cut_field_flag], $lang_code));
                                $cut_field_flag++;
                            }
                        }

                        if ($type == 'checkbox') {
                            $drop_down_arr = array();
                            $cut_field_flag = 0;
                            foreach ($cus_field['options']['value'] as $key => $cus_field_options_value) {

                                $drop_down_arr[$cus_field_options_value] = (apply_filters('wpml_translate_single_string', $cus_field['options']['label'][$cut_field_flag], 'Custom Fields', 'Checkbox Option Label - ' . $cus_field['options']['label'][$cut_field_flag], $lang_code));
                                $cut_field_flag++;
                            }
                        }

                        if ($type == 'upload_file' && $is_field_visible != 'no') {
                            $fielad_name = 'jobsearch_cfupfiles_' . $field_name;
                            if (!empty($fields_cus_vals)) {
                                $cusupf_field_value = isset($fields_cus_vals[$field_name]) ? $fields_cus_vals[$field_name] : '';
                            } else {
                                $cusupf_field_value = get_post_meta($post_id, $fielad_name, true);
                            }
                            if (!empty($cusupf_field_value)) {
                                foreach ($cusupf_field_value as $cusupf_file) {
                                    echo $before_html;

                                    $_attach_id = jobsearch_get_attachment_id_from_url($cusupf_file);
                                    $_attach_post = get_post($_attach_id);
                                    $_attach_mime = isset($_attach_post->post_mime_type) ? $_attach_post->post_mime_type : '';
                                    $_attach_guide = isset($_attach_post->guid) ? $_attach_post->guid : '';
                                    $attach_name = basename($_attach_guide);

                                    $file_icon = 'fa fa-file-text-o';
                                    if ($_attach_mime == 'image/png' || $_attach_mime == 'image/jpeg') {
                                        $file_icon = 'fa fa-file-image-o';
                                    } else if ($_attach_mime == 'application/msword' || $_attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                        $file_icon = 'fa fa-file-word-o';
                                    } else if ($_attach_mime == 'application/vnd.ms-excel' || $_attach_mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                        $file_icon = 'fa fa-file-excel-o';
                                    } else if ($_attach_mime == 'application/pdf') {
                                        $file_icon = 'fa fa-file-pdf-o';
                                    }

                                    echo '<i class="' . $file_icon . '"></i>';
                                    echo '<div class="jobsearch-services-text">';
                                    echo '<small>';
                                    if ($_attach_mime == 'image/png' || $_attach_mime == 'image/jpeg') {
                                        echo '<a href="' . $cusupf_file . '" download="' . $attach_name . '"><img src="' . $_attach_guide . '" alt=""></a>';
                                    } else {
                                        echo '<a href="' . $cusupf_file . '" download="' . $attach_name . '">' . esc_html($attach_name) . '</a>';
                                    }
                                    echo '</small>';
                                    echo '</div>';
                                    echo $after_html;
                                }
                            }
                        }

                        if (is_array($cus_field_value_arr)) {
                            $cus_field_value_arr = array_filter($cus_field_value_arr);
                        }

                        if (isset($cus_field_value_arr) && (is_array($cus_field_value_arr) && !empty($cus_field_value_arr)) || (!is_array($cus_field_value_arr) && $cus_field_value_arr <> '')) {
                            echo $before_html;
                            $no_icon_class = ' has-no-icon';
                            if (isset($cus_field_icon_arr) && $cus_field_icon_arr <> '' && $field_icon == true) {
                                $no_icon_class = '';
                                ?>
                                <i class="<?php echo jobsearch_esc_html($cus_field_icon_arr) ?>"></i>
                                <?php
                            }

                            if ($type == 'dependent_dropdown') {
                                echo($cus_field_value_arr);
                            } else {

                                if (!(isset($selected_fields) && !empty($selected_fields) && is_array($selected_fields) && sizeof($selected_fields) > 0)) { // dont show in job listing
                                    echo '<div class="' . $prefix . '-services-text' . $no_icon_class . '">';
                                }

                                if (is_array($cus_field_value_arr)) {

                                    if (isset($cus_field_label_arr) && $cus_field_label_arr <> '') {
                                        echo jobsearch_esc_html($cus_field_label_arr) . ' ';
                                    }
                                    foreach ($cus_field_value_arr as $key => $single_value) {
                                        $single_value = jobsearch_esc_html($single_value);
                                        if ($single_value != '') {
                                            if (isset($cus_format) && $cus_format != '') {
                                                echo '<small>';
                                                echo date($cus_format, $single_value);
                                                echo '</small>';
                                            } else if (($type == 'dropdown' || $type == 'checkbox') && isset($drop_down_arr[$single_value]) && $drop_down_arr[$single_value] != '') {
                                                echo '<small>';
                                                echo jobsearch_esc_html($drop_down_arr[$single_value]);
                                                echo '</small>';
                                            } else {
                                                echo '<small>';
                                                echo jobsearch_esc_html(ucwords(str_replace("-", " ", $single_value)));
                                                echo '</small>';
                                            }
                                        }
                                    }
                                    if (isset($cus_field_label_arr) && $cus_field_label_arr <> '' && ($type != 'dropdown' && $type != 'checkbox') && $type != 'date') {
                                        echo '<span>' . jobsearch_esc_html($cus_field_label_arr) . ' </span>';
                                    }
                                } else {

                                    if (isset($cus_field_label_arr) && $cus_field_label_arr <> '') {
                                        if ($custom_value_position) {
                                            if ($field_label == true) {
                                                echo jobsearch_esc_html(stripslashes($cus_field_label_arr)) . ' ';
                                            }
                                        }
                                    }

                                    if (isset($cus_format) && $cus_format != '') {
                                        echo '<small>';
                                        echo date($cus_format, $cus_field_value_arr);
                                        echo '</small>';
                                    } else if (($type == 'dropdown' || $type == 'checkbox') && isset($drop_down_arr[$cus_field_value_arr]) && $drop_down_arr[$cus_field_value_arr] != '') {
                                        echo '<small>';
                                        echo(stripslashes(jobsearch_esc_html($drop_down_arr[$cus_field_value_arr])));
                                        echo '</small>';
                                    } else if ($type == 'textarea') {
                                        echo '<div class="text-content">';
                                        echo jobsearch_esc_wp_editor($cus_field_value_arr);
                                        echo '</div>';
                                    } else if ($type == 'video') {
                                        if ($cus_field_value_arr != '') {
                                            echo '<div class="custom-video-contner">';
                                            echo wp_oembed_get(($cus_field_value_arr));
                                            echo '</div>';
                                        }
                                    } else if ($type == 'linkurl') {
                                        if ($cus_field_value_arr != '') {
                                            $cus_field_link_target = isset($cus_field['link_target']) ? $cus_field['link_target'] : '';
                                            echo '<div class="custom-url-contner">';
                                            echo '<a href="' . jobsearch_esc_html(esc_url($cus_field_value_arr)) . '" target="' . $cus_field_link_target . '">' . jobsearch_esc_html($cus_field_value_arr) . '</a>';
                                            echo '</div>';
                                        }
                                    } else {
                                        if ($custom_value_position) {
                                            if ($candidate_list == 'on') {
                                                echo(ucwords(str_replace("-", " ", jobsearch_esc_html($cus_field_value_arr))));
                                            } else {
                                                echo '<small>';
                                                echo(ucwords(str_replace("-", " ", jobsearch_esc_html($cus_field_value_arr))));
                                                echo '</small>';
                                            }
                                        }
                                    }
                                }

                                if (!(isset($selected_fields) && !empty($selected_fields) && is_array($selected_fields) && sizeof($selected_fields) > 0)) { // dont show in job listing
                                    echo '</div>';
                                }
                            }

                            echo $after_html;
                            $custom_field_flag++;
                            if ($custom_field_flag > $fields_number && $fields_number != '') {
                                break;
                            }
                        }
                    } else if ($type == 'heading') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Heading Field Label - ' . $cus_field_label_arr, $lang_code);

                        if (strpos($before_html, $prefix . '-column-4') !== false) {
                            $before_html = str_replace(array($prefix . '-column-4'), array($prefix . '-column-12'), $before_html);
                        }
                        echo $before_html;
                        echo '<div class="' . $prefix . '-content-title"><h2>' . $cus_field_label_arr . '</h2></div>';
                        echo $after_html;
                    }
                }
                $content = ob_get_clean();
            }
        }

        $custom_fields['content'] = $content;
        return $custom_fields;
    }

    static function jobsearch_custom_fields_filter_box_quick_apply_mob_html_callback($html, $custom_field_entity = '', $global_rand_id, $args_count, $left_filter_count_switch, $submit_js_function)
    {
        global $jobsearch_form_fields, $jobsearch_plugin_options, $sitepress;

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        $submit_js_function_str = '';
        if ($submit_js_function != '') {
            $submit_js_function_str = $submit_js_function . '(' . $global_rand_id . ')';
        }

        $salary_onoff_switch = isset($jobsearch_plugin_options['salary_onoff_switch']) ? $jobsearch_plugin_options['salary_onoff_switch'] : ''; // for job salary check
        if ($custom_field_entity == 'candidate') {
            $salary_onoff_switch = isset($jobsearch_plugin_options['cand_salary_switch']) ? $jobsearch_plugin_options['cand_salary_switch'] : 'on'; // for candidate salry check
        }
        $job_cus_fields = get_option("jobsearch_custom_field_" . $custom_field_entity);
        ob_start();
        $custom_field_flag = 11;
        if (!empty($job_cus_fields)) {
            foreach ($job_cus_fields as $cus_fieldvar => $cus_field) {
                $all_item_empty = 0;
                if (isset($cus_field['options']['value']) && is_array($cus_field['options']['value'])) {
                    foreach ($cus_field['options']['value'] as $cus_field_options_value) {

                        if ($cus_field_options_value != '') {
                            $all_item_empty = 0;
                            break;
                        } else {
                            $all_item_empty = 1;
                        }
                    }
                }
                if ($cus_field['type'] == 'salary') {
                    $cus_field['enable-search'] = 'yes';
                }
                if (isset($cus_field['enable-search']) && $cus_field['enable-search'] == 'yes' && ($all_item_empty == 0)) {
                    if ($cus_field['type'] == 'salary') {
                        $query_str_var_name = 'jobsearch_field_job_salary';
                        $str_salary_type_name = 'job_salary_type';
                        if ($custom_field_entity == 'candidate') {
                            $query_str_var_name = 'jobsearch_field_candidate_salary';
                            $str_salary_type_name = 'candidate_salary_type';
                        }
                    } else {
                        $query_str_var_name = isset($cus_field['name']) ? $cus_field['name'] : '';
                    }
                    $collapse_condition = 'no';
                    if (isset($cus_field['collapse-search'])) {
                        $collapse_condition = $cus_field['collapse-search'];
                    }

                    $cus_field_label_arr = isset($cus_field['label']) ? $cus_field['label'] : '';
                    $type = isset($cus_field['type']) ? $cus_field['type'] : '';

                    if ($type == 'text') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Text Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'email') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Email Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'number') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Number Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'date') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Date Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'checkbox') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Checkbox Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'dropdown') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Dropdown Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'range') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Range Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'textarea') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Textarea Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'heading') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Heading Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'salary') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Salary Label - ' . $cus_field_label_arr, $lang_code);
                    }
                    $cus_field_label_arr = stripslashes($cus_field_label_arr);
                    $mobile_toggle = wp_is_mobile() ? 'jobsearch-filter-' . $query_str_var_name : '';
                    if ($cus_field['type'] == 'heading' && $cus_field_label_arr != '') { ?>
                        <div class="jobsearch-sfiltrs-heding"><h2><?php echo($cus_field_label_arr) ?></h2></div>
                    <?php } else { ?>
                        <div class="jobsearch-filter-responsive-wrap jobsearch-sub-filters <?php echo($mobile_toggle) ?>">
                            <div class="jobsearch-search-filter-wrap">
                                <div class="jobsearch-fltbox-title"></div>
                                <div class="jobsearch-checkbox-toggle">
                                    <?php
                                    if ($cus_field['type'] == 'dropdown') {
                                        $request_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                                        $request_val_arr = explode(",", $request_val);
                                        ?>
                                    <input type="hidden" value="<?php echo jobsearch_esc_html($request_val); ?>"
                                           name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                           id="hidden_input-<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                           class="<?php echo jobsearch_esc_html($query_str_var_name); ?>"/>

                                    <?php
                                    if ($query_str_var_name != '') {
                                    ?>
                                        <script type="text/javascript">
                                            jQuery(function () {
                                                'use strict'
                                                var $checkboxes = jQuery("input[type=checkbox].<?php echo jobsearch_esc_html($query_str_var_name); ?>");
                                                $checkboxes.on('change', function () {
                                                    var ids = $checkboxes.filter(':checked').map(function () {
                                                        return this.value;
                                                    }).get().join(',');
                                                    jQuery('#hidden_input-<?php echo jobsearch_esc_html($query_str_var_name); ?>').val(ids);
                                                    <?php echo($submit_js_function_str); ?>
                                                });
                                            });
                                        </script>
                                    <?php
                                    }
                                    ?>
                                        <ul class="jobsearch-checkbox">
                                            <?php
                                            $number_option_flag = 1;
                                            $cut_field_flag = 0;
                                            if (isset($cus_field['options']['value']) && !empty($cus_field['options']['value'])) {
                                                foreach ($cus_field['options']['value'] as $cus_field_options_value) {
                                                    if ($cus_field['options']['value'][$cut_field_flag] == '' || $cus_field['options']['label'][$cut_field_flag] == '') {
                                                        $cut_field_flag++;
                                                        continue;
                                                    }
                                                    // get count of each item
                                                    // extra condidation
                                                    if (isset($cus_field['post-multi']) && $cus_field['post-multi'] == 'yes') {

                                                        $dropdown_count_arr = array(
                                                            array(
                                                                'key' => $query_str_var_name,
                                                                'value' => ($cus_field_options_value),
                                                                'compare' => 'Like',
                                                            )
                                                        );
                                                    } else {
                                                        $dropdown_count_arr = array(
                                                            array(
                                                                'key' => $query_str_var_name,
                                                                'value' => $cus_field_options_value,
                                                                'compare' => '=',
                                                            )
                                                        );
                                                    }
                                                    // main query array $args_count
                                                    if ($cus_field_options_value != '') {
                                                        if (isset($cus_field['multi']) && $cus_field['multi'] == 'yes') {
                                                            $checked = '';
                                                            if (!empty($request_val_arr) && in_array($cus_field_options_value, $request_val_arr)) {
                                                                $checked = ' checked="checked"';
                                                            }
                                                            ?>
                                                            <li class="<?php echo($number_option_flag > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">

                                                                <input type="checkbox"
                                                                       id="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>"
                                                                       value="<?php echo jobsearch_esc_html($cus_field_options_value); ?>"
                                                                       class="<?php echo jobsearch_esc_html($query_str_var_name); ?>" <?php echo($checked); ?> />
                                                                <label for="<?php echo force_balance_tags($query_str_var_name . '_' . $number_option_flag) ?>">
                                                                    <span></span><?php echo(apply_filters('wpml_translate_single_string', $cus_field['options']['label'][$cut_field_flag], 'Custom Fields', 'Dropdown Option Label - ' . $cus_field['options']['label'][$cut_field_flag], $lang_code)); ?>
                                                                </label>
                                                                <?php if ($left_filter_count_switch == 'yes') {
                                                                    $dropdown_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $dropdown_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                                                    ?>
                                                                    <span class="filter-post-count"><?php echo absint($dropdown_totnum); ?></span>
                                                                <?php } ?>
                                                            </li>
                                                            <?php
                                                            //
                                                        } else {
                                                            //get count for this itration
                                                            $dropdown_arr = array();
                                                            if (isset($cus_field['post-multi']) && $cus_field['post-multi'] == 'yes') {
                                                                $dropdown_arr = array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => serialize($cus_field_options_value),
                                                                    'compare' => 'Like',
                                                                );
                                                            } else {
                                                                $dropdown_arr = array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => $cus_field_options_value,
                                                                    'compare' => '=',
                                                                );
                                                            }

                                                            $custom_dropdown_selected = '';
                                                            if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == $cus_field_options_value) {
                                                                $custom_dropdown_selected = ' checked="checked"';
                                                            }
                                                            ?>
                                                            <li class="<?php echo($number_option_flag > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">
                                                                <input type="radio"
                                                                       name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                                       id="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>"
                                                                       value="<?php echo jobsearch_esc_html($cus_field_options_value); ?>" <?php echo($custom_dropdown_selected); ?>
                                                                       onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                                <label for="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>">
                                                                    <span></span><?php echo(apply_filters('wpml_translate_single_string', $cus_field['options']['label'][$cut_field_flag], 'Custom Fields', 'Dropdown Option Label - ' . $cus_field['options']['label'][$cut_field_flag], $lang_code)); ?>
                                                                </label>
                                                                <?php if ($left_filter_count_switch == 'yes') {
                                                                    $dropdown_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $dropdown_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                                                    ?>
                                                                    <span class="filter-post-count"><?php echo absint($dropdown_totnum); ?></span>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </li>
                                                            <?php
                                                        }
                                                    }
                                                    $number_option_flag++;
                                                    $cut_field_flag++;
                                                }
                                            }
                                            ?>
                                        </ul>
                                    <?php
                                    if ($number_option_flag > 6) {
                                        echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
                                    }
                                    //
                                    } else if ($cus_field['type'] == 'checkbox') {
                                    $request_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                                    $request_val_arr = explode(",", $request_val);
                                    ?>
                                    <input type="hidden" value="<?php echo jobsearch_esc_html($request_val); ?>"
                                           name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                           id="hidden_input-<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                           class="<?php echo jobsearch_esc_html($query_str_var_name); ?>"/>
                                    <?php if ($query_str_var_name != '') { ?>
                                        <script type="text/javascript">
                                            jQuery(function () {
                                                'use strict'
                                                var $checkboxes = jQuery("input[type=checkbox].<?php echo jobsearch_esc_html($query_str_var_name); ?>");
                                                $checkboxes.on('change', function () {
                                                    var ids = $checkboxes.filter(':checked').map(function () {
                                                        return this.value;
                                                    }).get().join(',');
                                                    jQuery('#hidden_input-<?php echo jobsearch_esc_html($query_str_var_name); ?>').val(ids);
                                                    <?php echo($submit_js_function_str); ?>
                                                });
                                            });
                                        </script>
                                    <?php } ?>
                                        <ul class="jobsearch-checkbox">
                                            <?php
                                            $number_option_flag = 1;
                                            $cut_field_flag = 0;
                                            if (isset($cus_field['options']['value']) && !empty($cus_field['options']['value'])) {
                                                foreach ($cus_field['options']['value'] as $cus_field_options_value) {
                                                    if ($cus_field['options']['value'][$cut_field_flag] == '' || $cus_field['options']['label'][$cut_field_flag] == '') {
                                                        $cut_field_flag++;
                                                        continue;
                                                    }
                                                    // get count of each item
                                                    // extra condidation
                                                    if (isset($cus_field['post-multi']) && $cus_field['post-multi'] == 'yes') {

                                                        $dropdown_count_arr = array(
                                                            array(
                                                                'key' => $query_str_var_name,
                                                                'value' => ($cus_field_options_value),
                                                                'compare' => 'Like',
                                                            )
                                                        );
                                                    } else {
                                                        $dropdown_count_arr = array(
                                                            array(
                                                                'key' => $query_str_var_name,
                                                                'value' => $cus_field_options_value,
                                                                'compare' => '=',
                                                            )
                                                        );
                                                    }
                                                    // main query array $args_count

                                                    $dropdown_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $dropdown_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                                    if ($cus_field_options_value != '') {
                                                        if (isset($cus_field['multi']) && $cus_field['multi'] == 'yes') {
                                                            $checked = '';
                                                            if (!empty($request_val_arr) && in_array($cus_field_options_value, $request_val_arr)) {
                                                                $checked = ' checked="checked"';
                                                            }
                                                            ?>
                                                            <li class="<?php echo($number_option_flag > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">
                                                                <input type="checkbox"
                                                                       id="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>"
                                                                       value="<?php echo jobsearch_esc_html($cus_field_options_value); ?>"
                                                                       class="<?php echo jobsearch_esc_html($query_str_var_name); ?>" <?php echo($checked); ?> />
                                                                <label for="<?php echo force_balance_tags($query_str_var_name . '_' . $number_option_flag) ?>">
                                                                    <span></span><?php echo(apply_filters('wpml_translate_single_string', $cus_field['options']['label'][$cut_field_flag], 'Custom Fields', 'Dropdown Option Label - ' . $cus_field['options']['label'][$cut_field_flag], $lang_code)); ?>
                                                                </label>
                                                                <?php if ($left_filter_count_switch == 'yes') { ?>
                                                                    <span class="filter-post-count"><?php echo absint($dropdown_totnum); ?></span>
                                                                <?php } ?>
                                                            </li>
                                                            <?php
                                                            //
                                                        } else {
                                                            //get count for this itration
                                                            $dropdown_arr = array();
                                                            if (isset($cus_field['post-multi']) && $cus_field['post-multi'] == 'yes') {
                                                                $dropdown_arr = array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => serialize($cus_field_options_value),
                                                                    'compare' => 'Like',
                                                                );
                                                            } else {
                                                                $dropdown_arr = array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => $cus_field_options_value,
                                                                    'compare' => '=',
                                                                );
                                                            }

                                                            $custom_dropdown_selected = '';
                                                            if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == $cus_field_options_value) {
                                                                $custom_dropdown_selected = ' checked="checked"';
                                                            }
                                                            ?>
                                                            <li class="<?php echo($number_option_flag > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">
                                                                <input type="radio"
                                                                       name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                                       id="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>"
                                                                       value="<?php echo jobsearch_esc_html($cus_field_options_value); ?>" <?php echo($custom_dropdown_selected); ?>
                                                                       onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                                <label for="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>">
                                                                    <span></span><?php echo(apply_filters('wpml_translate_single_string', $cus_field['options']['label'][$cut_field_flag], 'Custom Fields', 'Dropdown Option Label - ' . $cus_field['options']['label'][$cut_field_flag], $lang_code)); ?>
                                                                </label>
                                                                <?php if ($left_filter_count_switch == 'yes') { ?>
                                                                    <span class="filter-post-count"><?php echo absint($dropdown_totnum); ?></span>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </li>
                                                            <?php
                                                        }
                                                    }
                                                    $number_option_flag++;
                                                    $cut_field_flag++;
                                                }
                                            }
                                            ?>
                                        </ul>
                                    <?php
                                    if ($number_option_flag > 6) {
                                        echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
                                    }
                                    //
                                    } else if ($cus_field['type'] == 'dependent_dropdown') {
                                    $depdrpdwn_field_req_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                                    $depdrpdwn_field_options = isset($cus_field['options_list']) ? $cus_field['options_list'] : '';
                                    $depdrpdwn_cont_optsid = isset($cus_field['options_list_id']) && $cus_field['options_list_id'] != '' ? $cus_field['options_list_id'] : 0;
                                    ?>
                                        <div class="jobsearch-depdrpdwn-fields">
                                            <?php
                                            $depdrpdwn_fields = jobsearch_dependent_dropdown_list_html($depdrpdwn_field_options, $depdrpdwn_cont_optsid, $cus_field, $depdrpdwn_field_req_val);
                                            echo($depdrpdwn_fields);
                                            ?>
                                            <a href="javascript:void(0);"
                                               class="depdrpdwn-form-submitbtn jobsearch-bgcolor btn"
                                               onclick="<?php echo($submit_js_function_str); ?>"><?php esc_html_e('Submit', 'wp-jobsearch') ?></a>
                                        </div>
                                    <?php } else if ($cus_field['type'] == 'text' || $cus_field['type'] == 'textarea' || $cus_field['type'] == 'email') {
                                        $text_field_req_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';

                                        ?>
                                        <ul class="jobsearch-checkbox">
                                            <li>
                                                <input type="text"
                                                       name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                       id="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                       value="<?php echo jobsearch_esc_html($text_field_req_val); ?>"
                                                       onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                            </li>
                                        </ul>
                                        <?php
                                    } else if ($cus_field['type'] == 'number') {
                                        $number_field_req_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                                        ?>
                                        <ul class="jobsearch-checkbox">
                                            <li>
                                                <input type="number"
                                                       name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                       id="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                       value="<?php echo jobsearch_esc_html($number_field_req_val); ?>"
                                                       onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                            </li>
                                        </ul>
                                        <?php
                                    } else if ($cus_field['type'] == 'date') {
                                        $fromdate_field_req_val = isset($_REQUEST['from-' . $query_str_var_name]) ? $_REQUEST['from-' . $query_str_var_name] : '';
                                        $todate_field_req_val = isset($_REQUEST['to-' . $query_str_var_name]) ? $_REQUEST['to-' . $query_str_var_name] : '';
                                        wp_enqueue_style('datetimepicker-style');
                                        wp_enqueue_script('datetimepicker-script');
                                        wp_enqueue_script('jquery-ui');
                                        $cus_field_date_formate_arr = explode(" ", $cus_field['date-format']);
                                        ?>

                                        <ul class="jobsearch-checkbox">
                                            <li>
                                                <div class="filter-datewise-con">
                                                    <script type="text/javascript">
                                                        jQuery(document).ready(function () {
                                                            jQuery("#from<?php echo jobsearch_esc_html($query_str_var_name) . $global_rand_id; ?>").datetimepicker({
                                                                format: "<?php echo jobsearch_esc_html($cus_field_date_formate_arr[0]); ?>",
                                                                timepicker: false
                                                            });
                                                            jQuery("#to<?php echo jobsearch_esc_html($query_str_var_name) . $global_rand_id; ?>").datetimepicker({
                                                                format: "<?php echo jobsearch_esc_html($cus_field_date_formate_arr[0]); ?>",
                                                                timepicker: false
                                                            });
                                                        });
                                                    </script>
                                                    <label for="from<?php echo jobsearch_esc_html($query_str_var_name) . $global_rand_id; ?>">
                                                        <input type="text"
                                                               name="from-<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                               id="from<?php echo jobsearch_esc_html($query_str_var_name) . $global_rand_id; ?>"
                                                               value="<?php echo jobsearch_esc_html($fromdate_field_req_val); ?>"
                                                               placeholder="<?php esc_html_e('Date From', 'wp-jobsearch') ?>"
                                                               onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                    </label>
                                                    <label for="to<?php echo jobsearch_esc_html($query_str_var_name) . $global_rand_id; ?>">
                                                        <input type="text"
                                                               name="to-<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                               id="to<?php echo jobsearch_esc_html($query_str_var_name) . $global_rand_id; ?>"
                                                               value="<?php echo jobsearch_esc_html($todate_field_req_val); ?>"
                                                               placeholder="<?php esc_html_e('Date To', 'wp-jobsearch') ?>"
                                                               onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                    </label>
                                                </div>
                                            </li>
                                        </ul>
                                        <?php
                                    } elseif ($cus_field['type'] == 'range') {

                                        $range_min = $cus_field['min'];
                                        $range_laps = $cus_field['laps'];
                                        $range_laps = $range_laps > 100 ? 100 : $range_laps;
                                        $range_interval = $cus_field['interval'];
                                        $range_field_type = isset($cus_field['field-style']) ? $cus_field['field-style'] : 'simple'; //input, slider, input_slider

                                        if (strpos($range_field_type, '-') !== FALSE) {
                                            $range_field_type_arr = explode("_", $range_field_type);
                                        } else {
                                            $range_field_type_arr[0] = $range_field_type;
                                        }
                                        $range_flag = 0;
                                    while (count($range_field_type_arr) > $range_flag) {
                                    if ($range_field_type_arr[$range_flag] == 'simple') { // if input style
                                        $filter_more_counter = 1;
                                        ?>
                                        <ul class="jobsearch-checkbox">
                                            <?php
                                            $loop_flag = 1;
                                            while ($loop_flag <= $range_laps) { ?>
                                            <li class="<?php echo($filter_more_counter > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">
                                                <?php
                                                // main query array $args_count
                                                $range_first = $range_min + 1;
                                                $range_seond = $range_min + $range_interval;
                                                $range_count_arr = array(
                                                    array(
                                                        'key' => $query_str_var_name,
                                                        'value' => $range_first,
                                                        'compare' => '>=',
                                                        'type' => 'numeric'
                                                    ),
                                                    array(
                                                        'key' => $query_str_var_name,
                                                        'value' => $range_seond,
                                                        'compare' => '<=',
                                                        'type' => 'numeric'
                                                    )
                                                );
                                                $range_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $range_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                                $custom_slider_selected = '';
                                                if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == (($range_min + 1) . "-" . ($range_min + $range_interval))) {
                                                    $custom_slider_selected = ' checked="checked"';
                                                }
                                                ?>
                                                <input type="radio"
                                                       name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                       id="<?php echo jobsearch_esc_html($query_str_var_name . $loop_flag); ?>"
                                                       value="<?php echo jobsearch_esc_html((($range_min + 1) . "-" . ($range_min + $range_interval))); ?>" <?php echo jobsearch_esc_html($custom_slider_selected); ?>
                                                       onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                <label for="<?php echo jobsearch_esc_html($query_str_var_name . $loop_flag); ?>"><span></span><?php echo force_balance_tags((($range_min + 1) . " - " . ($range_min + $range_interval))); ?>
                                                </label>
                                                <?php if ($left_filter_count_switch == 'yes') { ?>
                                                    <span class="filter-post-count"><?php echo absint($range_totnum); ?></span>
                                                <?php } ?>
                                                </li><?php
                                                $range_min = $range_min + $range_interval;
                                                $loop_flag++;
                                                $filter_more_counter++;
                                            }
                                            ?>
                                        </ul>
                                        <?php
                                        if ($filter_more_counter > 6) {
                                            echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
                                        }
                                    } elseif ($range_field_type_arr[$range_flag] == 'slider') { // if slider style
                                        wp_enqueue_style('jquery-ui');
                                        wp_enqueue_script('jquery-ui');
                                        $rand_id = rand(123, 1231231);
                                        $range_field_max = $range_min;
                                        $i = 0;
                                        while ($range_laps > $i) {
                                            $range_field_max = $range_field_max + $range_interval;
                                            $i++;
                                        }
                                        $range_complete_str_first = "";
                                        $range_complete_str_second = "";
                                        $range_complete_str = '';
                                        $range_complete_str_first = $range_min;
                                        $range_complete_str_second = $range_field_max;
                                        if (isset($_REQUEST[$query_str_var_name])) {
                                            $range_complete_str = $_REQUEST[$query_str_var_name];
                                            $range_complete_str_arr = explode("-", $range_complete_str);
                                            $range_complete_str_first = isset($range_complete_str_arr[0]) ? $range_complete_str_arr[0] : '';
                                            $range_complete_str_second = isset($range_complete_str_arr[1]) ? $range_complete_str_arr[1] : '';
                                        }
                                        ?>
                                        <ul class="jobsearch-checkbox">
                                            <li>
                                                <input type="text"
                                                       name="<?php echo jobsearch_esc_html($query_str_var_name) ?>"
                                                       id="<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"
                                                       value="<?php echo jobsearch_esc_html($range_complete_str); ?>"
                                                       readonly
                                                       style="border:0; color:#f6931f; font-weight:bold;"/>
                                                <div id="slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"></div>
                                                <script type="text/javascript">
                                                    jQuery(document).ready(function () {


                                                        jQuery("#slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider({
                                                            range: true,
                                                            min: <?php echo absint($range_min); ?>,
                                                            max: <?php echo absint($range_field_max); ?>,
                                                            values: [<?php echo absint($range_complete_str_first); ?>, <?php echo absint($range_complete_str_second); ?>],
                                                            slide: function (event, ui) {
                                                                jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(ui.values[0] + "-" + ui.values[1]);
                                                            },
                                                            stop: function (event, ui) {
                                                                <?php echo force_balance_tags($submit_js_function_str); ?>;
                                                            }
                                                        });
                                                        jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(jQuery("#slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 0) +
                                                            "-" + jQuery("#slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 1));
                                                    });
                                                </script>
                                            </li>
                                        </ul>
                                        <?php
                                    }
                                        $range_flag++;
                                    }
                                    } elseif ($cus_field['type'] == 'salary' && $salary_onoff_switch != 'off') {

                                        $job_salary_types = isset($jobsearch_plugin_options['job-salary-types']) ? $jobsearch_plugin_options['job-salary-types'] : '';

                                        $salary_min = $cus_field['min'];
                                        $salary_laps = $cus_field['laps'];
                                        $salary_laps = $salary_laps > 200 ? 200 : $salary_laps;
                                        $salary_interval = $cus_field['interval'];
                                        $salary_field_type = isset($cus_field['field-style']) ? $cus_field['field-style'] : 'simple'; //input, slider, input_slider

                                        if (strpos($salary_field_type, '-') !== FALSE) {
                                            $salary_field_type_arr = explode("_", $salary_field_type);
                                        } else {
                                            $salary_field_type_arr[0] = $salary_field_type;
                                        }

                                        // Salary Types
                                    if (!empty($job_salary_types)) {
                                        $slar_type_count = 1;
                                        ?>
                                        <div class="jobsearch-salary-types-filter">
                                            <ul>
                                                <?php
                                                foreach ($job_salary_types as $job_salary_type) {
                                                    $job_salary_type = apply_filters('wpml_translate_single_string', $job_salary_type, 'JobSearch Options', 'Salary Type - ' . $job_salary_type, $lang_code);
                                                    $slalary_type_selected = '';
                                                    if (isset($_REQUEST[$str_salary_type_name]) && $_REQUEST[$str_salary_type_name] == 'type_' . $slar_type_count) {
                                                        $slalary_type_selected = ' checked="checked"';
                                                    }
                                                    ?>
                                                    <li class="salary-type-radio">
                                                        <input type="radio"
                                                               id="salary_type_<?php echo($slar_type_count) ?>"
                                                               name="<?php echo($str_salary_type_name) ?>"
                                                               class="job_salary_type"<?php echo($slalary_type_selected) ?>
                                                               value="type_<?php echo($slar_type_count) ?>"
                                                               onchange="<?php echo force_balance_tags($submit_js_function_str); ?>">
                                                        <label for="salary_type_<?php echo($slar_type_count) ?>">
                                                            <span></span>
                                                            <small><?php echo($job_salary_type) ?></small>
                                                        </label>
                                                    </li>
                                                    <?php
                                                    $slar_type_count++;
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                        <?php
                                    }
                                        //

                                        $salary_flag = 0;
                                    while (count($salary_field_type_arr) > $salary_flag) {
                                    if ($salary_field_type_arr[$salary_flag] == 'simple') { // if input style
                                        $filter_more_counter = 1;
                                        ?>
                                        <ul class="jobsearch-checkbox">
                                            <?php
                                            $loop_flag = 1;
                                            while ($loop_flag <= $salary_laps) {
                                                ?>
                                            <li class="<?php echo($filter_more_counter > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">
                                                <?php
                                                // main query array $args_count
                                                $salary_first = $salary_min + 1;
                                                $salary_seond = $salary_min + $salary_interval;
                                                $salary_count_arr = array(
                                                    array(
                                                        'key' => $query_str_var_name,
                                                        'value' => $salary_first,
                                                        'compare' => '>=',
                                                        'type' => 'numeric'
                                                    ),
                                                    array(
                                                        'key' => $query_str_var_name,
                                                        'value' => $salary_seond,
                                                        'compare' => '<=',
                                                        'type' => 'numeric'
                                                    )
                                                );
                                                $salary_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $salary_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                                $custom_slider_selected = '';
                                                if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == (($salary_min + 1) . "-" . ($salary_min + $salary_interval))) {
                                                    $custom_slider_selected = ' checked="checked"';
                                                }
                                                ?>
                                                <input type="radio"
                                                       name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                       id="<?php echo jobsearch_esc_html($query_str_var_name . $loop_flag); ?>"
                                                       value="<?php echo jobsearch_esc_html((($salary_min + 1) . "-" . ($salary_min + $salary_interval))); ?>" <?php echo($custom_slider_selected); ?>
                                                       onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                <?php
                                                $salary_from = ($salary_min + 1);
                                                $salary_upto = ($salary_min + $salary_interval);
                                                ?>
                                                <label for="<?php echo jobsearch_esc_html($query_str_var_name . $loop_flag); ?>"><span></span><?php echo((($salary_from) . " - " . ($salary_upto))); ?>
                                                </label>
                                                <?php if ($left_filter_count_switch == 'yes') { ?>
                                                    <span class="filter-post-count"><?php echo absint($salary_totnum); ?></span>
                                                <?php } ?>
                                                </li><?php
                                                $salary_min = $salary_min + $salary_interval;
                                                $loop_flag++;
                                                $filter_more_counter++;
                                            }
                                            ?>
                                        </ul>
                                        <?php
                                        if ($filter_more_counter > 6) {
                                            echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
                                        }
                                    } elseif ($salary_field_type_arr[$salary_flag] == 'slider') { // if slider style
                                        wp_enqueue_style('jquery-ui');
                                        wp_enqueue_script('jquery-ui');
                                        $rand_id = rand(1231110, 9231231);
                                        $salary_field_max = $salary_min;
                                        $i = 0;
                                        while ($salary_laps > $i) {
                                            $salary_field_max = $salary_field_max + $salary_interval;
                                            $i++;
                                        }
                                        $salary_complete_str_first = "";
                                        $salary_complete_str_second = "";
                                        $salary_complete_str = '';
                                        $salary_complete_str_first = $salary_min;
                                        $salary_complete_str_second = $salary_field_max;
                                        if (isset($_REQUEST[$query_str_var_name])) {
                                            $salary_complete_str = $_REQUEST[$query_str_var_name];
                                            $salary_complete_str_arr = explode("-", $salary_complete_str);
                                            $salary_complete_str_first = isset($salary_complete_str_arr[0]) ? $salary_complete_str_arr[0] : '';
                                            $salary_complete_str_second = isset($salary_complete_str_arr[1]) ? $salary_complete_str_arr[1] : '';
                                        }
                                        ?>
                                        <ul class="jobsearch-checkbox">
                                            <li class="salary-filter-slider">
                                                <div class="filter-slider-range">
                                                    <input type="text"
                                                           name="<?php echo jobsearch_esc_html($query_str_var_name) ?>"
                                                           id="<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"
                                                           value="<?php echo jobsearch_esc_html($salary_complete_str); ?>"
                                                           readonly
                                                           style="border:0; color:#f6931f; font-weight:bold;"/>
                                                </div>
                                                <div id="slider-salary<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"></div>
                                                <script type="text/javascript">
                                                    jQuery(document).ready(function () {

                                                        jQuery("#slider-salary<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider({
                                                            salary: true,
                                                            min: <?php echo absint($salary_min); ?>,
                                                            max: <?php echo absint($salary_field_max); ?>,
                                                            values: [<?php echo absint($salary_complete_str_first); ?>, <?php echo absint($salary_complete_str_second); ?>],
                                                            slide: function (event, ui) {
                                                                jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(ui.values[0] + "-" + ui.values[1]);
                                                            },
                                                            stop: function (event, ui) {
                                                                <?php echo force_balance_tags($submit_js_function_str); ?>;
                                                            }
                                                        });
                                                        jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(jQuery("#slider-salary<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 0) +
                                                            "-" + jQuery("#slider-salary<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 1));
                                                    });
                                                </script>
                                            </li>
                                        </ul>
                                        <?php
                                    }
                                        $salary_flag++;
                                    }
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>

                        <?php
                    }
                }
            }
        }
        $html .= ob_get_clean();
        return $html;
    }

    static function jobsearch_custom_fields_filter_box_quick_apply_html_callback($html, $custom_field_entity = '', $global_rand_id, $args_count, $left_filter_count_switch, $submit_js_function)
    {
        global $jobsearch_form_fields, $jobsearch_plugin_options, $sitepress;

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        $submit_js_function_str = '';
        if ($submit_js_function != '') {
            $submit_js_function_str = $submit_js_function . '(' . $global_rand_id . ')';
        }

        $salary_onoff_switch = isset($jobsearch_plugin_options['salary_onoff_switch']) ? $jobsearch_plugin_options['salary_onoff_switch'] : ''; // for job salary check
        if ($custom_field_entity == 'candidate') {
            $salary_onoff_switch = isset($jobsearch_plugin_options['cand_salary_switch']) ? $jobsearch_plugin_options['cand_salary_switch'] : 'on'; // for candidate salry check
        }

        $job_cus_fields = get_option("jobsearch_custom_field_" . $custom_field_entity);


        ob_start();
        $custom_field_flag = 11;
        if (!empty($job_cus_fields)) {
            foreach ($job_cus_fields as $cus_fieldvar => $cus_field) {
                $all_item_empty = 0;
                if (isset($cus_field['options']['value']) && is_array($cus_field['options']['value'])) {
                    foreach ($cus_field['options']['value'] as $cus_field_options_value) {

                        if ($cus_field_options_value != '') {
                            $all_item_empty = 0;
                            break;
                        } else {
                            $all_item_empty = 1;
                        }
                    }
                }
                if ($cus_field['type'] == 'salary') {
                    $cus_field['enable-search'] = 'yes';
                }

                if (isset($cus_field['enable-search']) && $cus_field['enable-search'] == 'yes' && ($all_item_empty == 0)) {

                    if ($cus_field['type'] == 'salary') {
                        $query_str_var_name = 'jobsearch_field_job_salary';
                        $str_salary_type_name = 'job_salary_type';
                        if ($custom_field_entity == 'candidate') {
                            $query_str_var_name = 'jobsearch_field_candidate_salary';
                            $str_salary_type_name = 'candidate_salary_type';
                        }
                    } else {
                        $query_str_var_name = isset($cus_field['name']) ? $cus_field['name'] : '';
                    }
                    $collapse_condition = 'no';
                    if (isset($cus_field['collapse-search'])) {
                        $collapse_condition = $cus_field['collapse-search'];
                    }

                    $cus_field_label_arr = isset($cus_field['label']) ? $cus_field['label'] : '';
                    $type = isset($cus_field['type']) ? $cus_field['type'] : '';

                    if ($type == 'text') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Text Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'email') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Email Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'number') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Number Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'date') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Date Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'checkbox') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Checkbox Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'dropdown') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Dropdown Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'range') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Range Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'textarea') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Textarea Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'heading') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Heading Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'salary') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Salary Label - ' . $cus_field_label_arr, $lang_code);
                    }

                    $cus_field_label_arr = stripslashes($cus_field_label_arr);
                    $mobile_view = wp_is_mobile() ? 'data-quick-detail-toggle="jobsearch-filter-' . $query_str_var_name . ' "' : '';
                    if ($cus_field['type'] == 'heading' && $cus_field_label_arr != '') { ?>
                        <div class="jobsearch-sfiltrs-heding"><h2><?php echo($cus_field_label_arr) ?></h2></div>
                    <?php } else { ?>

                        <li><a href="javascript:void(0)"
                               class="addon-quick-detail-toggle" <?php echo($mobile_view); ?>
                            ><?php echo jobsearch_esc_html(stripslashes($cus_field_label_arr)); ?>
                                <i class="careerfy-icon careerfy-down-arrow"></i></a>
                            <?php if (!wp_is_mobile()) { ?>
                                <div class="jobsearch-filter-responsive-wrap jobsearch-sub-filters">
                                    <div class="jobsearch-search-filter-wrap">
                                        <div class="jobsearch-fltbox-title"></div>
                                        <div class="jobsearch-checkbox-toggle">
                                            <?php
                                            if ($cus_field['type'] == 'dropdown') {
                                                $request_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                                                $request_val_arr = explode(",", $request_val);
                                                ?>
                                            <input type="hidden" value="<?php echo jobsearch_esc_html($request_val); ?>"
                                                   name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                   id="hidden_input-<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                   class="<?php echo jobsearch_esc_html($query_str_var_name); ?>"/>
                                            <?php
                                            if ($query_str_var_name != '') {
                                            ?>
                                                <script type="text/javascript">
                                                    jQuery(function () {
                                                        'use strict'
                                                        var $checkboxes = jQuery("input[type=checkbox].<?php echo jobsearch_esc_html($query_str_var_name); ?>");
                                                        $checkboxes.on('change', function () {
                                                            var ids = $checkboxes.filter(':checked').map(function () {
                                                                return this.value;
                                                            }).get().join(',');
                                                            jQuery('#hidden_input-<?php echo jobsearch_esc_html($query_str_var_name); ?>').val(ids);
                                                            <?php echo($submit_js_function_str); ?>
                                                        });
                                                    });
                                                </script>
                                            <?php
                                            }
                                            ?>
                                                <ul class="jobsearch-checkbox">
                                                    <?php
                                                    $number_option_flag = 1;
                                                    $cut_field_flag = 0;
                                                    if (isset($cus_field['options']['value']) && !empty($cus_field['options']['value'])) {
                                                        foreach ($cus_field['options']['value'] as $cus_field_options_value) {
                                                            if ($cus_field['options']['value'][$cut_field_flag] == '' || $cus_field['options']['label'][$cut_field_flag] == '') {
                                                                $cut_field_flag++;
                                                                continue;
                                                            }
                                                            // get count of each item
                                                            // extra condidation
                                                            if (isset($cus_field['post-multi']) && $cus_field['post-multi'] == 'yes') {

                                                                $dropdown_count_arr = array(
                                                                    array(
                                                                        'key' => $query_str_var_name,
                                                                        'value' => ($cus_field_options_value),
                                                                        'compare' => 'Like',
                                                                    )
                                                                );
                                                            } else {
                                                                $dropdown_count_arr = array(
                                                                    array(
                                                                        'key' => $query_str_var_name,
                                                                        'value' => $cus_field_options_value,
                                                                        'compare' => '=',
                                                                    )
                                                                );
                                                            }
                                                            // main query array $args_count
                                                            if ($cus_field_options_value != '') {
                                                                if (isset($cus_field['multi']) && $cus_field['multi'] == 'yes') {
                                                                    $checked = '';
                                                                    if (!empty($request_val_arr) && in_array($cus_field_options_value, $request_val_arr)) {
                                                                        $checked = ' checked="checked"';
                                                                    }
                                                                    ?>
                                                                    <li class="<?php echo($number_option_flag > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">

                                                                        <input type="checkbox"
                                                                               id="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>"
                                                                               value="<?php echo jobsearch_esc_html($cus_field_options_value); ?>"
                                                                               class="<?php echo jobsearch_esc_html($query_str_var_name); ?>" <?php echo($checked); ?> />
                                                                        <label for="<?php echo force_balance_tags($query_str_var_name . '_' . $number_option_flag) ?>">
                                                                            <span></span><?php echo(apply_filters('wpml_translate_single_string', $cus_field['options']['label'][$cut_field_flag], 'Custom Fields', 'Dropdown Option Label - ' . $cus_field['options']['label'][$cut_field_flag], $lang_code)); ?>
                                                                        </label>
                                                                        <?php if ($left_filter_count_switch == 'yes') {
                                                                            $dropdown_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $dropdown_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                                                            ?>
                                                                            <span class="filter-post-count"><?php echo absint($dropdown_totnum); ?></span>
                                                                        <?php } ?>
                                                                    </li>
                                                                    <?php
                                                                    //
                                                                } else {
                                                                    //get count for this itration
                                                                    $dropdown_arr = array();
                                                                    if (isset($cus_field['post-multi']) && $cus_field['post-multi'] == 'yes') {
                                                                        $dropdown_arr = array(
                                                                            'key' => $query_str_var_name,
                                                                            'value' => serialize($cus_field_options_value),
                                                                            'compare' => 'Like',
                                                                        );
                                                                    } else {
                                                                        $dropdown_arr = array(
                                                                            'key' => $query_str_var_name,
                                                                            'value' => $cus_field_options_value,
                                                                            'compare' => '=',
                                                                        );
                                                                    }

                                                                    $custom_dropdown_selected = '';
                                                                    if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == $cus_field_options_value) {
                                                                        $custom_dropdown_selected = ' checked="checked"';
                                                                    }
                                                                    ?>
                                                                    <li class="<?php echo($number_option_flag > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">
                                                                        <input type="radio"
                                                                               name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                                               id="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>"
                                                                               value="<?php echo jobsearch_esc_html($cus_field_options_value); ?>" <?php echo($custom_dropdown_selected); ?>
                                                                               onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                                        <label for="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>">
                                                                            <span></span><?php echo(apply_filters('wpml_translate_single_string', $cus_field['options']['label'][$cut_field_flag], 'Custom Fields', 'Dropdown Option Label - ' . $cus_field['options']['label'][$cut_field_flag], $lang_code)); ?>
                                                                        </label>
                                                                        <?php if ($left_filter_count_switch == 'yes') {
                                                                            $dropdown_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $dropdown_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                                                            ?>
                                                                            <span class="filter-post-count"><?php echo absint($dropdown_totnum); ?></span>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </li>
                                                                    <?php
                                                                }
                                                            }
                                                            $number_option_flag++;
                                                            $cut_field_flag++;
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                            <?php
                                            if ($number_option_flag > 6) {
                                                echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
                                            }
                                            //
                                            } else if ($cus_field['type'] == 'checkbox') {
                                            $request_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                                            $request_val_arr = explode(",", $request_val);
                                            ?>
                                            <input type="hidden" value="<?php echo jobsearch_esc_html($request_val); ?>"
                                                   name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                   id="hidden_input-<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                   class="<?php echo jobsearch_esc_html($query_str_var_name); ?>"/>
                                            <?php
                                            if ($query_str_var_name != '') {
                                            ?>
                                                <script type="text/javascript">
                                                    jQuery(function () {
                                                        'use strict'
                                                        var $checkboxes = jQuery("input[type=checkbox].<?php echo jobsearch_esc_html($query_str_var_name); ?>");
                                                        $checkboxes.on('change', function () {
                                                            var ids = $checkboxes.filter(':checked').map(function () {
                                                                return this.value;
                                                            }).get().join(',');
                                                            jQuery('#hidden_input-<?php echo jobsearch_esc_html($query_str_var_name); ?>').val(ids);
                                                            <?php echo($submit_js_function_str); ?>
                                                        });
                                                    });
                                                </script>
                                            <?php
                                            }
                                            ?>
                                                <ul class="jobsearch-checkbox">
                                                    <?php
                                                    $number_option_flag = 1;
                                                    $cut_field_flag = 0;
                                                    if (isset($cus_field['options']['value']) && !empty($cus_field['options']['value'])) {
                                                        foreach ($cus_field['options']['value'] as $cus_field_options_value) {
                                                            if ($cus_field['options']['value'][$cut_field_flag] == '' || $cus_field['options']['label'][$cut_field_flag] == '') {
                                                                $cut_field_flag++;
                                                                continue;
                                                            }
                                                            // get count of each item
                                                            // extra condidation
                                                            if (isset($cus_field['post-multi']) && $cus_field['post-multi'] == 'yes') {

                                                                $dropdown_count_arr = array(
                                                                    array(
                                                                        'key' => $query_str_var_name,
                                                                        'value' => ($cus_field_options_value),
                                                                        'compare' => 'Like',
                                                                    )
                                                                );
                                                            } else {
                                                                $dropdown_count_arr = array(
                                                                    array(
                                                                        'key' => $query_str_var_name,
                                                                        'value' => $cus_field_options_value,
                                                                        'compare' => '=',
                                                                    )
                                                                );
                                                            }
                                                            // main query array $args_count

                                                            $dropdown_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $dropdown_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                                            if ($cus_field_options_value != '') {
                                                                if (isset($cus_field['multi']) && $cus_field['multi'] == 'yes') {
                                                                    $checked = '';
                                                                    if (!empty($request_val_arr) && in_array($cus_field_options_value, $request_val_arr)) {
                                                                        $checked = ' checked="checked"';
                                                                    }
                                                                    ?>
                                                                    <li class="<?php echo($number_option_flag > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">
                                                                        <input type="checkbox"
                                                                               id="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>"
                                                                               value="<?php echo jobsearch_esc_html($cus_field_options_value); ?>"
                                                                               class="<?php echo jobsearch_esc_html($query_str_var_name); ?>" <?php echo($checked); ?> />
                                                                        <label for="<?php echo force_balance_tags($query_str_var_name . '_' . $number_option_flag) ?>">
                                                                            <span></span><?php echo(apply_filters('wpml_translate_single_string', $cus_field['options']['label'][$cut_field_flag], 'Custom Fields', 'Dropdown Option Label - ' . $cus_field['options']['label'][$cut_field_flag], $lang_code)); ?>
                                                                        </label>
                                                                        <?php if ($left_filter_count_switch == 'yes') { ?>
                                                                            <span class="filter-post-count"><?php echo absint($dropdown_totnum); ?></span>
                                                                        <?php } ?>
                                                                    </li>
                                                                    <?php
                                                                    //
                                                                } else {
                                                                    //get count for this itration
                                                                    $dropdown_arr = array();
                                                                    if (isset($cus_field['post-multi']) && $cus_field['post-multi'] == 'yes') {
                                                                        $dropdown_arr = array(
                                                                            'key' => $query_str_var_name,
                                                                            'value' => serialize($cus_field_options_value),
                                                                            'compare' => 'Like',
                                                                        );
                                                                    } else {
                                                                        $dropdown_arr = array(
                                                                            'key' => $query_str_var_name,
                                                                            'value' => $cus_field_options_value,
                                                                            'compare' => '=',
                                                                        );
                                                                    }

                                                                    $custom_dropdown_selected = '';
                                                                    if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == $cus_field_options_value) {
                                                                        $custom_dropdown_selected = ' checked="checked"';
                                                                    }
                                                                    ?>
                                                                    <li class="<?php echo($number_option_flag > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">
                                                                        <input type="radio"
                                                                               name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                                               id="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>"
                                                                               value="<?php echo jobsearch_esc_html($cus_field_options_value); ?>" <?php echo($custom_dropdown_selected); ?>
                                                                               onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                                        <label for="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>">
                                                                            <span></span><?php echo(apply_filters('wpml_translate_single_string', $cus_field['options']['label'][$cut_field_flag], 'Custom Fields', 'Dropdown Option Label - ' . $cus_field['options']['label'][$cut_field_flag], $lang_code)); ?>
                                                                        </label>
                                                                        <?php if ($left_filter_count_switch == 'yes') { ?>
                                                                            <span class="filter-post-count"><?php echo absint($dropdown_totnum); ?></span>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </li>
                                                                    <?php
                                                                }
                                                            }
                                                            $number_option_flag++;
                                                            $cut_field_flag++;
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                            <?php
                                            if ($number_option_flag > 6) {
                                                echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
                                            }
                                            //
                                            } else if ($cus_field['type'] == 'dependent_dropdown') {
                                            $depdrpdwn_field_req_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                                            $depdrpdwn_field_options = isset($cus_field['options_list']) ? $cus_field['options_list'] : '';
                                            $depdrpdwn_cont_optsid = isset($cus_field['options_list_id']) && $cus_field['options_list_id'] != '' ? $cus_field['options_list_id'] : 0;
                                            ?>
                                                <div class="jobsearch-depdrpdwn-fields">
                                                    <?php
                                                    $depdrpdwn_fields = jobsearch_dependent_dropdown_list_html($depdrpdwn_field_options, $depdrpdwn_cont_optsid, $cus_field, $depdrpdwn_field_req_val);
                                                    echo($depdrpdwn_fields);
                                                    ?>
                                                    <a href="javascript:void(0);"
                                                       class="depdrpdwn-form-submitbtn jobsearch-bgcolor btn"
                                                       onclick="<?php echo($submit_js_function_str); ?>"><?php esc_html_e('Submit', 'wp-jobsearch') ?></a>
                                                </div>
                                            <?php } else if ($cus_field['type'] == 'text' || $cus_field['type'] == 'textarea' || $cus_field['type'] == 'email') {
                                                $text_field_req_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';

                                                ?>
                                                <ul class="jobsearch-checkbox">
                                                    <li>
                                                        <input type="text"
                                                               name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                               id="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                               value="<?php echo jobsearch_esc_html($text_field_req_val); ?>"
                                                               onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                    </li>
                                                </ul>
                                                <?php
                                            } else if ($cus_field['type'] == 'number') {
                                                $number_field_req_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                                                ?>
                                                <ul class="jobsearch-checkbox">
                                                    <li>
                                                        <input type="number"
                                                               name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                               id="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                               value="<?php echo jobsearch_esc_html($number_field_req_val); ?>"
                                                               onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                    </li>
                                                </ul>
                                                <?php
                                            } else if ($cus_field['type'] == 'date') {
                                                $fromdate_field_req_val = isset($_REQUEST['from-' . $query_str_var_name]) ? $_REQUEST['from-' . $query_str_var_name] : '';
                                                $todate_field_req_val = isset($_REQUEST['to-' . $query_str_var_name]) ? $_REQUEST['to-' . $query_str_var_name] : '';
                                                wp_enqueue_style('datetimepicker-style');
                                                wp_enqueue_script('datetimepicker-script');
                                                wp_enqueue_script('jquery-ui');
                                                $cus_field_date_formate_arr = explode(" ", $cus_field['date-format']);
                                                ?>

                                                <ul class="jobsearch-checkbox">
                                                    <li>
                                                        <div class="filter-datewise-con">
                                                            <script type="text/javascript">
                                                                jQuery(document).ready(function () {
                                                                    jQuery("#from<?php echo jobsearch_esc_html($query_str_var_name) . $global_rand_id; ?>").datetimepicker({
                                                                        format: "<?php echo jobsearch_esc_html($cus_field_date_formate_arr[0]); ?>",
                                                                        timepicker: false
                                                                    });
                                                                    jQuery("#to<?php echo jobsearch_esc_html($query_str_var_name) . $global_rand_id; ?>").datetimepicker({
                                                                        format: "<?php echo jobsearch_esc_html($cus_field_date_formate_arr[0]); ?>",
                                                                        timepicker: false
                                                                    });
                                                                });
                                                            </script>
                                                            <label for="from<?php echo jobsearch_esc_html($query_str_var_name) . $global_rand_id; ?>">
                                                                <input type="text"
                                                                       name="from-<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                                       id="from<?php echo jobsearch_esc_html($query_str_var_name) . $global_rand_id; ?>"
                                                                       value="<?php echo jobsearch_esc_html($fromdate_field_req_val); ?>"
                                                                       placeholder="<?php esc_html_e('Date From', 'wp-jobsearch') ?>"
                                                                       onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                            </label>
                                                            <label for="to<?php echo jobsearch_esc_html($query_str_var_name) . $global_rand_id; ?>">
                                                                <input type="text"
                                                                       name="to-<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                                       id="to<?php echo jobsearch_esc_html($query_str_var_name) . $global_rand_id; ?>"
                                                                       value="<?php echo jobsearch_esc_html($todate_field_req_val); ?>"
                                                                       placeholder="<?php esc_html_e('Date To', 'wp-jobsearch') ?>"
                                                                       onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                            </label>
                                                        </div>
                                                    </li>
                                                </ul>
                                                <?php
                                            } elseif ($cus_field['type'] == 'range') {

                                                $range_min = $cus_field['min'];
                                                $range_laps = $cus_field['laps'];
                                                $range_laps = $range_laps > 100 ? 100 : $range_laps;
                                                $range_interval = $cus_field['interval'];
                                                $range_field_type = isset($cus_field['field-style']) ? $cus_field['field-style'] : 'simple'; //input, slider, input_slider

                                                if (strpos($range_field_type, '-') !== FALSE) {
                                                    $range_field_type_arr = explode("_", $range_field_type);
                                                } else {
                                                    $range_field_type_arr[0] = $range_field_type;
                                                }
                                                $range_flag = 0;
                                            while (count($range_field_type_arr) > $range_flag) {
                                            if ($range_field_type_arr[$range_flag] == 'simple') { // if input style
                                                $filter_more_counter = 1;
                                                ?>
                                                <ul class="jobsearch-checkbox">
                                                    <?php
                                                    $loop_flag = 1;
                                                    while ($loop_flag <= $range_laps) { ?>
                                                    <li class="<?php echo($filter_more_counter > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">
                                                        <?php
                                                        // main query array $args_count
                                                        $range_first = $range_min + 1;
                                                        $range_seond = $range_min + $range_interval;
                                                        $range_count_arr = array(
                                                            array(
                                                                'key' => $query_str_var_name,
                                                                'value' => $range_first,
                                                                'compare' => '>=',
                                                                'type' => 'numeric'
                                                            ),
                                                            array(
                                                                'key' => $query_str_var_name,
                                                                'value' => $range_seond,
                                                                'compare' => '<=',
                                                                'type' => 'numeric'
                                                            )
                                                        );
                                                        $range_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $range_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                                        $custom_slider_selected = '';
                                                        if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == (($range_min + 1) . "-" . ($range_min + $range_interval))) {
                                                            $custom_slider_selected = ' checked="checked"';
                                                        }
                                                        ?>
                                                        <input type="radio"
                                                               name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                               id="<?php echo jobsearch_esc_html($query_str_var_name . $loop_flag); ?>"
                                                               value="<?php echo jobsearch_esc_html((($range_min + 1) . "-" . ($range_min + $range_interval))); ?>" <?php echo jobsearch_esc_html($custom_slider_selected); ?>
                                                               onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                        <label for="<?php echo jobsearch_esc_html($query_str_var_name . $loop_flag); ?>"><span></span><?php echo force_balance_tags((($range_min + 1) . " - " . ($range_min + $range_interval))); ?>
                                                        </label>
                                                        <?php if ($left_filter_count_switch == 'yes') { ?>
                                                            <span class="filter-post-count"><?php echo absint($range_totnum); ?></span>
                                                        <?php } ?>
                                                        </li><?php
                                                        $range_min = $range_min + $range_interval;
                                                        $loop_flag++;
                                                        $filter_more_counter++;
                                                    }
                                                    ?>
                                                </ul>
                                                <?php
                                                if ($filter_more_counter > 6) {
                                                    echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
                                                }
                                            } elseif ($range_field_type_arr[$range_flag] == 'slider') { // if slider style
                                                wp_enqueue_style('jquery-ui');
                                                wp_enqueue_script('jquery-ui');
                                                $rand_id = rand(123, 1231231);
                                                $range_field_max = $range_min;
                                                $i = 0;
                                                while ($range_laps > $i) {
                                                    $range_field_max = $range_field_max + $range_interval;
                                                    $i++;
                                                }
                                                $range_complete_str_first = "";
                                                $range_complete_str_second = "";
                                                $range_complete_str = '';
                                                $range_complete_str_first = $range_min;
                                                $range_complete_str_second = $range_field_max;
                                                if (isset($_REQUEST[$query_str_var_name])) {
                                                    $range_complete_str = $_REQUEST[$query_str_var_name];
                                                    $range_complete_str_arr = explode("-", $range_complete_str);
                                                    $range_complete_str_first = isset($range_complete_str_arr[0]) ? $range_complete_str_arr[0] : '';
                                                    $range_complete_str_second = isset($range_complete_str_arr[1]) ? $range_complete_str_arr[1] : '';
                                                }
                                                ?>
                                                <ul class="jobsearch-checkbox">
                                                    <li>
                                                        <input type="text"
                                                               name="<?php echo jobsearch_esc_html($query_str_var_name) ?>"
                                                               id="<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"
                                                               value="<?php echo jobsearch_esc_html($range_complete_str); ?>"
                                                               readonly
                                                               style="border:0; color:#f6931f; font-weight:bold;"/>
                                                        <div id="slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"></div>
                                                        <script type="text/javascript">
                                                            jQuery(document).ready(function () {


                                                                jQuery("#slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider({
                                                                    range: true,
                                                                    min: <?php echo absint($range_min); ?>,
                                                                    max: <?php echo absint($range_field_max); ?>,
                                                                    values: [<?php echo absint($range_complete_str_first); ?>, <?php echo absint($range_complete_str_second); ?>],
                                                                    slide: function (event, ui) {
                                                                        jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(ui.values[0] + "-" + ui.values[1]);
                                                                    },
                                                                    stop: function (event, ui) {
                                                                        <?php echo force_balance_tags($submit_js_function_str); ?>;
                                                                    }
                                                                });
                                                                jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(jQuery("#slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 0) +
                                                                    "-" + jQuery("#slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 1));
                                                            });
                                                        </script>
                                                    </li>
                                                </ul>
                                                <?php
                                            }
                                                $range_flag++;
                                            }
                                            } elseif ($cus_field['type'] == 'salary' && $salary_onoff_switch != 'off') {

                                                $job_salary_types = isset($jobsearch_plugin_options['job-salary-types']) ? $jobsearch_plugin_options['job-salary-types'] : '';

                                                $salary_min = $cus_field['min'];
                                                $salary_laps = $cus_field['laps'];
                                                $salary_laps = $salary_laps > 200 ? 200 : $salary_laps;
                                                $salary_interval = $cus_field['interval'];
                                                $salary_field_type = isset($cus_field['field-style']) ? $cus_field['field-style'] : 'simple'; //input, slider, input_slider

                                                if (strpos($salary_field_type, '-') !== FALSE) {
                                                    $salary_field_type_arr = explode("_", $salary_field_type);
                                                } else {
                                                    $salary_field_type_arr[0] = $salary_field_type;
                                                }

                                                // Salary Types
                                            if (!empty($job_salary_types)) {
                                                $slar_type_count = 1;
                                                ?>
                                                <div class="jobsearch-salary-types-filter">
                                                    <ul>
                                                        <?php
                                                        foreach ($job_salary_types as $job_salary_type) {
                                                            $job_salary_type = apply_filters('wpml_translate_single_string', $job_salary_type, 'JobSearch Options', 'Salary Type - ' . $job_salary_type, $lang_code);
                                                            $slalary_type_selected = '';
                                                            if (isset($_REQUEST[$str_salary_type_name]) && $_REQUEST[$str_salary_type_name] == 'type_' . $slar_type_count) {
                                                                $slalary_type_selected = ' checked="checked"';
                                                            }
                                                            ?>
                                                            <li class="salary-type-radio">
                                                                <input type="radio"
                                                                       id="salary_type_<?php echo($slar_type_count) ?>"
                                                                       name="<?php echo($str_salary_type_name) ?>"
                                                                       class="job_salary_type"<?php echo($slalary_type_selected) ?>
                                                                       value="type_<?php echo($slar_type_count) ?>"
                                                                       onchange="<?php echo force_balance_tags($submit_js_function_str); ?>">
                                                                <label for="salary_type_<?php echo($slar_type_count) ?>">
                                                                    <span></span>
                                                                    <small><?php echo($job_salary_type) ?></small>
                                                                </label>
                                                            </li>
                                                            <?php
                                                            $slar_type_count++;
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                                <?php
                                            }
                                                //

                                                $salary_flag = 0;
                                            while (count($salary_field_type_arr) > $salary_flag) {
                                            if ($salary_field_type_arr[$salary_flag] == 'simple') { // if input style
                                                $filter_more_counter = 1;
                                                ?>
                                                <ul class="jobsearch-checkbox">
                                                    <?php
                                                    $loop_flag = 1;
                                                    while ($loop_flag <= $salary_laps) {
                                                        ?>
                                                    <li class="<?php echo($filter_more_counter > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">
                                                        <?php
                                                        // main query array $args_count
                                                        $salary_first = $salary_min + 1;
                                                        $salary_seond = $salary_min + $salary_interval;
                                                        $salary_count_arr = array(
                                                            array(
                                                                'key' => $query_str_var_name,
                                                                'value' => $salary_first,
                                                                'compare' => '>=',
                                                                'type' => 'numeric'
                                                            ),
                                                            array(
                                                                'key' => $query_str_var_name,
                                                                'value' => $salary_seond,
                                                                'compare' => '<=',
                                                                'type' => 'numeric'
                                                            )
                                                        );
                                                        $salary_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $salary_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                                        $custom_slider_selected = '';
                                                        if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == (($salary_min + 1) . "-" . ($salary_min + $salary_interval))) {
                                                            $custom_slider_selected = ' checked="checked"';
                                                        }
                                                        ?>
                                                        <input type="radio"
                                                               name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                               id="<?php echo jobsearch_esc_html($query_str_var_name . $loop_flag); ?>"
                                                               value="<?php echo jobsearch_esc_html((($salary_min + 1) . "-" . ($salary_min + $salary_interval))); ?>" <?php echo jobsearch_esc_html($custom_slider_selected); ?>
                                                               onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                        <?php
                                                        $salary_from = ($salary_min + 1);
                                                        $salary_upto = ($salary_min + $salary_interval);
                                                        ?>
                                                        <label for="<?php echo jobsearch_esc_html($query_str_var_name . $loop_flag); ?>"><span></span><?php echo((($salary_from) . " - " . ($salary_upto))); ?>
                                                        </label>
                                                        <?php if ($left_filter_count_switch == 'yes') { ?>
                                                            <span class="filter-post-count"><?php echo absint($salary_totnum); ?></span>
                                                        <?php } ?>
                                                        </li><?php
                                                        $salary_min = $salary_min + $salary_interval;
                                                        $loop_flag++;
                                                        $filter_more_counter++;
                                                    }
                                                    ?>
                                                </ul>
                                                <?php
                                                if ($filter_more_counter > 6) {
                                                    echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
                                                }
                                            } elseif ($salary_field_type_arr[$salary_flag] == 'slider') { // if slider style
                                                wp_enqueue_style('jquery-ui');
                                                wp_enqueue_script('jquery-ui');
                                                $rand_id = rand(1231110, 9231231);
                                                $salary_field_max = $salary_min;
                                                $i = 0;
                                                while ($salary_laps > $i) {
                                                    $salary_field_max = $salary_field_max + $salary_interval;
                                                    $i++;
                                                }
                                                $salary_complete_str_first = "";
                                                $salary_complete_str_second = "";
                                                $salary_complete_str = '';
                                                $salary_complete_str_first = $salary_min;
                                                $salary_complete_str_second = $salary_field_max;
                                                if (isset($_REQUEST[$query_str_var_name])) {
                                                    $salary_complete_str = $_REQUEST[$query_str_var_name];
                                                    $salary_complete_str_arr = explode("-", $salary_complete_str);
                                                    $salary_complete_str_first = isset($salary_complete_str_arr[0]) ? $salary_complete_str_arr[0] : '';
                                                    $salary_complete_str_second = isset($salary_complete_str_arr[1]) ? $salary_complete_str_arr[1] : '';
                                                }
                                                ?>
                                                <ul class="jobsearch-checkbox">
                                                    <li class="salary-filter-slider">
                                                        <div class="filter-slider-range">
                                                            <input type="text"
                                                                   name="<?php echo jobsearch_esc_html($query_str_var_name) ?>"
                                                                   id="<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"
                                                                   value="<?php echo jobsearch_esc_html($salary_complete_str); ?>"
                                                                   readonly
                                                                   style="border:0; color:#f6931f; font-weight:bold;"/>
                                                        </div>
                                                        <div id="slider-salary<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"></div>
                                                        <script type="text/javascript">
                                                            jQuery(document).ready(function () {

                                                                jQuery("#slider-salary<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider({
                                                                    salary: true,
                                                                    min: <?php echo absint($salary_min); ?>,
                                                                    max: <?php echo absint($salary_field_max); ?>,
                                                                    values: [<?php echo absint($salary_complete_str_first); ?>, <?php echo absint($salary_complete_str_second); ?>],
                                                                    slide: function (event, ui) {
                                                                        jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(ui.values[0] + "-" + ui.values[1]);
                                                                    },
                                                                    stop: function (event, ui) {
                                                                        <?php echo force_balance_tags($submit_js_function_str); ?>;
                                                                    }
                                                                });
                                                                jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(jQuery("#slider-salary<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 0) +
                                                                    "-" + jQuery("#slider-salary<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 1));
                                                            });
                                                        </script>
                                                    </li>
                                                </ul>
                                                <?php
                                            }
                                                $salary_flag++;
                                            }
                                            }
                                            ?>

                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </li>
                        <?php
                    }
                }
            }
        }
        $html .= ob_get_clean();
        return $html;
    }

    static function jobsearch_custom_fields_filter_box_html_callback($html, $custom_field_entity = '', $global_rand_id, $args_count, $left_filter_count_switch, $submit_js_function, $filter_sort_by = 'default')
    {
        global $jobsearch_form_fields, $jobsearch_plugin_options, $sitepress;

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        $submit_js_function_str = '';
        if ($submit_js_function != '') {
            $submit_js_function_str = $submit_js_function . '(' . $global_rand_id . ')';
        }

        $salary_onoff_switch = isset($jobsearch_plugin_options['salary_onoff_switch']) ? $jobsearch_plugin_options['salary_onoff_switch'] : ''; // for job salary check
        if ($custom_field_entity == 'candidate') {
            $salary_onoff_switch = isset($jobsearch_plugin_options['cand_salary_switch']) ? $jobsearch_plugin_options['cand_salary_switch'] : 'on'; // for candidate salry check 
        }

        $job_cus_fields = get_option("jobsearch_custom_field_" . $custom_field_entity);
        ob_start();
        $custom_field_flag = 11;
        if (!empty($job_cus_fields)) {
            foreach ($job_cus_fields as $cus_fieldvar => $cus_field) {
                $all_item_empty = 0;
                if (isset($cus_field['options']['value']) && is_array($cus_field['options']['value'])) {
                    foreach ($cus_field['options']['value'] as $cus_field_options_value) {

                        if ($cus_field_options_value != '') {
                            $all_item_empty = 0;
                            break;
                        } else {
                            $all_item_empty = 1;
                        }
                    }
                }
                if ($cus_field['type'] == 'salary') {
                    $cus_field['enable-search'] = 'yes';
                }

                if (isset($cus_field['enable-search']) && ($cus_field['enable-search'] == 'yes' || $cus_field['enable-search'] == 'on') && ($all_item_empty == 0)) {

                    if ($cus_field['type'] == 'salary') {
                        $query_str_var_name = 'jobsearch_field_job_salary';
                        $str_salary_type_name = 'job_salary_type';
                        if ($custom_field_entity == 'candidate') {
                            $query_str_var_name = 'jobsearch_field_candidate_salary';
                            $str_salary_type_name = 'candidate_salary_type';
                        }
                    } else {
                        $query_str_var_name = isset($cus_field['name']) ? $cus_field['name'] : '';
                    }
                    $collapse_condition = 'no';
                    if (isset($cus_field['collapse-search'])) {
                        $collapse_condition = $cus_field['collapse-search'];
                    }

                    $cus_field_label_arr = isset($cus_field['label']) ? $cus_field['label'] : '';
                    $type = isset($cus_field['type']) ? $cus_field['type'] : '';

                    if ($type == 'text') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Text Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'email') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Email Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'number') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Number Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'date') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Date Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'checkbox') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Checkbox Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'dropdown' || $type == 'dependent_dropdown') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Dropdown Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'range') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Range Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'textarea') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Textarea Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'heading') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Heading Field Label - ' . $cus_field_label_arr, $lang_code);
                    } else if ($type == 'salary') {
                        $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Salary Label - ' . $cus_field_label_arr, $lang_code);
                    }

                    $cus_field_label_arr = stripslashes($cus_field_label_arr);

                    if ($cus_field['type'] == 'heading' && $cus_field_label_arr != '') { ?>
                        <div class="jobsearch-sfiltrs-heding"><h2><?php echo($cus_field_label_arr) ?></h2></div>
                        <?php
                    } else {
                        $filter_collapse_cval = 'open';
                        if ($collapse_condition == 'yes') {
                            $filter_collapse_cval = 'close';
                        }
                        $filter_collapse_cname = isset($cus_field['name']) ? sanitize_title($cus_field['name']) . '_csec_collapse' : '';
                        if (isset($_COOKIE[$filter_collapse_cname]) && $_COOKIE[$filter_collapse_cname] != '') {
                            $filter_collapse_cval = $_COOKIE[$filter_collapse_cname];
                            if ($_COOKIE[$filter_collapse_cname] == 'open') {
                                $collapse_condition = 'no';
                            } else {
                                $collapse_condition = 'yes';
                            }
                        }
                        ?>
                        <div class="jobsearch-filter-responsive-wrap">
                            <div class="jobsearch-search-filter-wrap <?php echo($collapse_condition == 'yes' ? 'jobsearch-search-filter-toggle jobsearch-remove-padding' : 'jobsearch-search-filter-toggle') ?>">
                                <div class="jobsearch-fltbox-title">
                                    <a href="javascript:void(0);" data-cname="<?php echo($filter_collapse_cname) ?>"
                                       data-cval="<?php echo($filter_collapse_cval) ?>" class="jobsearch-click-btn">
                                        <?php echo jobsearch_esc_html(stripslashes($cus_field_label_arr)); ?>
                                    </a>
                                </div>
                                <div class="jobsearch-checkbox-toggle" <?php echo($collapse_condition == 'yes' ? 'style="display: none;"' : '') ?>>
                                    <?php
                                    $filter_args = array(
                                        'custom_field_entity' => $custom_field_entity,
                                        'global_rand_id' => $global_rand_id,
                                        'args_count' => $args_count,
                                        'left_filter_count_switch' => $left_filter_count_switch,
                                        'submit_js_function' => $submit_js_function,
                                        'cus_field' => $cus_field,
                                        'cus_fieldvar' => $cus_fieldvar,
                                    );
                                    echo apply_filters('jobsearch_cusfields_left_filters_before_dropdwn', '', $filter_args);
                                    if ($cus_field['type'] == 'dropdown') {
                                        $request_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                                        $request_val_arr = explode(",", $request_val);
                                        ?>
                                    <input type="hidden" value="<?php echo jobsearch_esc_html($request_val); ?>"
                                           name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                           id="hidden_input-<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                           class="<?php echo jobsearch_esc_html($query_str_var_name); ?>"/>
                                    <?php
                                    if ($query_str_var_name != '') {
                                    ?>
                                        <script type="text/javascript">
                                            jQuery(function () {
                                                'use strict'
                                                var $checkboxes = jQuery("input[type=checkbox].<?php echo jobsearch_esc_html($query_str_var_name); ?>");
                                                $checkboxes.on('change', function () {
                                                    var ids = $checkboxes.filter(':checked').map(function () {
                                                        return this.value;
                                                    }).get().join(',');
                                                    jQuery('#hidden_input-<?php echo jobsearch_esc_html($query_str_var_name); ?>').val(ids);
                                                    <?php echo($submit_js_function_str); ?>
                                                });
                                            });
                                        </script>
                                    <?php
                                    }
                                    ?>
                                        <ul class="jobsearch-checkbox">
                                            <?php
                                            $number_option_flag = 1;
                                            $cut_field_flag = 0;
                                            $filter_html_arr = array();
                                            if (isset($cus_field['options']['value']) && !empty($cus_field['options']['value'])) {
                                                foreach ($cus_field['options']['value'] as $cus_field_options_value) {
                                                    if ($cus_field['options']['value'][$cut_field_flag] == '' || $cus_field['options']['label'][$cut_field_flag] == '') {
                                                        $cut_field_flag++;
                                                        continue;
                                                    }
                                                    // get count of each item
                                                    // extra condidation
                                                    if (isset($cus_field['post-multi']) && $cus_field['post-multi'] == 'yes') {

                                                        $dropdown_count_arr = array(
                                                            array(
                                                                'key' => $query_str_var_name,
                                                                'value' => ($cus_field_options_value),
                                                                'compare' => 'Like',
                                                            )
                                                        );
                                                    } else {
                                                        $dropdown_count_arr = array(
                                                            array(
                                                                'key' => $query_str_var_name,
                                                                'value' => $cus_field_options_value,
                                                                'compare' => '=',
                                                            )
                                                        );
                                                    }
                                                    // main query array $args_count

                                                    if ($cus_field_options_value != '') {
                                                        ob_start();
                                                        if (isset($cus_field['multi']) && $cus_field['multi'] == 'yes') {
                                                            $checked = '';
                                                            if (!empty($request_val_arr) && in_array($cus_field_options_value, $request_val_arr)) {
                                                                $checked = ' checked="checked"';
                                                            }
                                                            ?>
                                                            <li class="<?php echo($number_option_flag > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">

                                                                <input type="checkbox"
                                                                       id="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>"
                                                                       value="<?php echo jobsearch_esc_html($cus_field_options_value); ?>"
                                                                       class="<?php echo jobsearch_esc_html($query_str_var_name); ?>" <?php echo($checked); ?> />
                                                                <label for="<?php echo force_balance_tags($query_str_var_name . '_' . $number_option_flag) ?>">
                                                                    <span></span><?php echo(apply_filters('wpml_translate_single_string', $cus_field['options']['label'][$cut_field_flag], 'Custom Fields', 'Dropdown Option Label - ' . $cus_field['options']['label'][$cut_field_flag], $lang_code)); ?>
                                                                </label>
                                                                <?php 
                                                                $dropdown_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $dropdown_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);

                                                                if ($left_filter_count_switch == 'yes') {
                                                                    ?>
                                                                    <span class="filter-post-count"><?php echo absint($dropdown_totnum); ?></span>
                                                                <?php } ?>
                                                            </li>
                                                            <?php
                                                            //
                                                        } else {
                                                            //get count for this itration
                                                            $dropdown_arr = array();
                                                            if (isset($cus_field['post-multi']) && $cus_field['post-multi'] == 'yes') {
                                                                $dropdown_arr = array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => serialize($cus_field_options_value),
                                                                    'compare' => 'Like',
                                                                );
                                                            } else {
                                                                $dropdown_arr = array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => $cus_field_options_value,
                                                                    'compare' => '=',
                                                                );
                                                            }

                                                            $custom_dropdown_selected = '';
                                                            if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == $cus_field_options_value) {
                                                                $custom_dropdown_selected = ' checked="checked"';
                                                            }
                                                            ?>
                                                            <li class="<?php echo($number_option_flag > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">
                                                                <input type="radio"
                                                                       name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                                       id="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>"
                                                                       value="<?php echo jobsearch_esc_html($cus_field_options_value); ?>" <?php echo($custom_dropdown_selected); ?>
                                                                       onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                                <label for="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>">
                                                                    <span></span><?php echo(apply_filters('wpml_translate_single_string', $cus_field['options']['label'][$cut_field_flag], 'Custom Fields', 'Dropdown Option Label - ' . $cus_field['options']['label'][$cut_field_flag], $lang_code)); ?>
                                                                </label>
                                                                <?php
                                                                $dropdown_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $dropdown_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);

                                                                if ($left_filter_count_switch == 'yes') {
                                                                    ?>
                                                                    <span class="filter-post-count"><?php echo absint($dropdown_totnum); ?></span>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </li>
                                                            <?php
                                                        }
                                                        $filter_itm_html = ob_get_clean();
                                                        $filter_html_arr[] = array(
                                                            'title' => $cus_field['options']['label'][$cut_field_flag],
                                                            'count' => $dropdown_totnum,
                                                            'html' => $filter_itm_html
                                                        );
                                                    }
                                                    $number_option_flag++;
                                                    $cut_field_flag++;
                                                }
                                                
                                                if (!empty($filter_html_arr)) {
                                                    if ($filter_sort_by == 'desc') {
                                                        krsort($filter_html_arr);
                                                    } else if ($filter_sort_by == 'alpha') {
                                                        usort($filter_html_arr, function ($a, $b) {
                                                            return strcmp($a["title"], $b["title"]);
                                                        });
                                                    } else if ($filter_sort_by == 'count') {
                                                        usort($filter_html_arr, function ($a, $b) {
                                                            if ($a['count'] == $b['count']) {
                                                                $ret_val = 0;
                                                            }
                                                            $ret_val = ($b['count'] < $a['count']) ? -1 : 1;
                                                            return $ret_val;
                                                        });
                                                    }
                                                    foreach ($filter_html_arr as $filtr_item_html) {
                                                        echo ($filtr_item_html['html']);
                                                    }
                                                }
                                            }
                                            ?>
                                        </ul>
                                    <?php
                                    if ($number_option_flag > 6) {
                                        echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
                                    }
                                    //
                                    } else if ($cus_field['type'] == 'checkbox') {
                                    $request_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                                    $request_val_arr = explode(",", $request_val);
                                    ?>
                                    <input type="hidden" value="<?php echo jobsearch_esc_html($request_val); ?>"
                                           name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                           id="hidden_input-<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                           class="<?php echo jobsearch_esc_html($query_str_var_name); ?>"/>
                                    <?php
                                    if ($query_str_var_name != '') {
                                    ?>
                                        <script type="text/javascript">
                                            jQuery(function () {
                                                'use strict'
                                                var $checkboxes = jQuery("input[type=checkbox].<?php echo jobsearch_esc_html($query_str_var_name); ?>");
                                                $checkboxes.on('change', function () {
                                                    var ids = $checkboxes.filter(':checked').map(function () {
                                                        return this.value;
                                                    }).get().join(',');
                                                    jQuery('#hidden_input-<?php echo jobsearch_esc_html($query_str_var_name); ?>').val(ids);
                                                    <?php echo($submit_js_function_str); ?>
                                                });
                                            });
                                        </script>
                                    <?php
                                    }
                                    ?>
                                        <ul class="jobsearch-checkbox">
                                            <?php
                                            $number_option_flag = 1;
                                            $cut_field_flag = 0;
                                            $filter_html_arr = array();
                                            if (isset($cus_field['options']['value']) && !empty($cus_field['options']['value'])) {
                                                foreach ($cus_field['options']['value'] as $cus_field_options_value) {
                                                    if ($cus_field['options']['value'][$cut_field_flag] == '' || $cus_field['options']['label'][$cut_field_flag] == '') {
                                                        $cut_field_flag++;
                                                        continue;
                                                    }
                                                    // get count of each item
                                                    // extra condidation
                                                    if (isset($cus_field['post-multi']) && $cus_field['post-multi'] == 'yes') {

                                                        $dropdown_count_arr = array(
                                                            array(
                                                                'key' => $query_str_var_name,
                                                                'value' => ($cus_field_options_value),
                                                                'compare' => 'Like',
                                                            )
                                                        );
                                                    } else {
                                                        $dropdown_count_arr = array(
                                                            array(
                                                                'key' => $query_str_var_name,
                                                                'value' => $cus_field_options_value,
                                                                'compare' => '=',
                                                            )
                                                        );
                                                    }
                                                    // main query array $args_count

                                                    $dropdown_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $dropdown_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                                    if ($cus_field_options_value != '') {
                                                        ob_start();
                                                        if (isset($cus_field['multi']) && $cus_field['multi'] == 'yes') {
                                                            $checked = '';
                                                            if (!empty($request_val_arr) && in_array($cus_field_options_value, $request_val_arr)) {
                                                                $checked = ' checked="checked"';
                                                            }
                                                            ?>
                                                            <li class="<?php echo($number_option_flag > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">

                                                                <input type="checkbox"
                                                                       id="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>"
                                                                       value="<?php echo jobsearch_esc_html($cus_field_options_value); ?>"
                                                                       class="<?php echo jobsearch_esc_html($query_str_var_name); ?>" <?php echo($checked); ?> />
                                                                <label for="<?php echo force_balance_tags($query_str_var_name . '_' . $number_option_flag) ?>">
                                                                    <span></span><?php echo(apply_filters('wpml_translate_single_string', $cus_field['options']['label'][$cut_field_flag], 'Custom Fields', 'Dropdown Option Label - ' . $cus_field['options']['label'][$cut_field_flag], $lang_code)); ?>
                                                                </label>
                                                                <?php if ($left_filter_count_switch == 'yes') { ?>
                                                                    <span class="filter-post-count"><?php echo absint($dropdown_totnum); ?></span>
                                                                <?php } ?>
                                                            </li>
                                                            <?php
                                                            //
                                                        } else {
                                                            //get count for this itration
                                                            $dropdown_arr = array();
                                                            if (isset($cus_field['post-multi']) && $cus_field['post-multi'] == 'yes') {
                                                                $dropdown_arr = array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => serialize($cus_field_options_value),
                                                                    'compare' => 'Like',
                                                                );
                                                            } else {
                                                                $dropdown_arr = array(
                                                                    'key' => $query_str_var_name,
                                                                    'value' => $cus_field_options_value,
                                                                    'compare' => '=',
                                                                );
                                                            }

                                                            $custom_dropdown_selected = '';
                                                            if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == $cus_field_options_value) {
                                                                $custom_dropdown_selected = ' checked="checked"';
                                                            }
                                                            ?>
                                                            <li class="<?php echo($number_option_flag > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">
                                                                <input type="radio"
                                                                       name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                                       id="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>"
                                                                       value="<?php echo jobsearch_esc_html($cus_field_options_value); ?>" <?php echo($custom_dropdown_selected); ?>
                                                                       onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                                <label for="<?php echo jobsearch_esc_html($query_str_var_name . '_' . $number_option_flag); ?>">
                                                                    <span></span><?php echo(apply_filters('wpml_translate_single_string', $cus_field['options']['label'][$cut_field_flag], 'Custom Fields', 'Dropdown Option Label - ' . $cus_field['options']['label'][$cut_field_flag], $lang_code)); ?>
                                                                </label>
                                                                <?php if ($left_filter_count_switch == 'yes') { ?>
                                                                    <span class="filter-post-count"><?php echo absint($dropdown_totnum); ?></span>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </li>
                                                            <?php
                                                        }
                                                        $filter_itm_html = ob_get_clean();
                                                        $filter_html_arr[] = array(
                                                            'title' => $cus_field['options']['label'][$cut_field_flag],
                                                            'count' => $dropdown_totnum,
                                                            'html' => $filter_itm_html
                                                        );
                                                    }
                                                    $number_option_flag++;
                                                    $cut_field_flag++;
                                                }
                                                if (!empty($filter_html_arr)) {
                                                    if ($filter_sort_by == 'desc') {
                                                        krsort($filter_html_arr);
                                                    } else if ($filter_sort_by == 'alpha') {
                                                        usort($filter_html_arr, function ($a, $b) {
                                                            return strcmp($a["title"], $b["title"]);
                                                        });
                                                    } else if ($filter_sort_by == 'count') {
                                                        usort($filter_html_arr, function ($a, $b) {
                                                            if ($a['count'] == $b['count']) {
                                                                $ret_val = 0;
                                                            }
                                                            $ret_val = ($b['count'] < $a['count']) ? -1 : 1;
                                                            return $ret_val;
                                                        });
                                                    }
                                                    foreach ($filter_html_arr as $filtr_item_html) {
                                                        echo ($filtr_item_html['html']);
                                                    }
                                                }
                                            }
                                            ?>
                                        </ul>
                                    <?php
                                    if ($number_option_flag > 6) {
                                        echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
                                    }
                                    //
                                    } else if ($cus_field['type'] == 'dependent_dropdown') {
                                    $depdrpdwn_field_req_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                                    $depdrpdwn_field_options = isset($cus_field['options_list']) ? $cus_field['options_list'] : '';
                                    $depdrpdwn_cont_optsid = isset($cus_field['options_list_id']) && $cus_field['options_list_id'] != '' ? $cus_field['options_list_id'] : 0;
                                    ?>
                                        <div class="jobsearch-depdrpdwn-fields">
                                            <?php
                                            $depdrpdwn_fields = jobsearch_dependent_dropdown_list_html($depdrpdwn_field_options, $depdrpdwn_cont_optsid, $cus_field, $depdrpdwn_field_req_val);
                                            echo($depdrpdwn_fields);
                                            ?>
                                            <a href="javascript:void(0);"
                                               class="depdrpdwn-form-submitbtn jobsearch-bgcolor btn"
                                               onclick="<?php echo($submit_js_function_str); ?>"><?php esc_html_e('Submit', 'wp-jobsearch') ?></a>
                                        </div>
                                    <?php } else if ($cus_field['type'] == 'text' || $cus_field['type'] == 'textarea' || $cus_field['type'] == 'email') {
                                        $text_field_req_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';

                                        ?>
                                        <ul class="jobsearch-checkbox">
                                            <li>
                                                <input type="text"
                                                       name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                       id="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                       value="<?php echo jobsearch_esc_html($text_field_req_val); ?>"
                                                       onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                            </li>
                                        </ul>
                                        <?php
                                    } else if ($cus_field['type'] == 'number') {
                                        $number_field_req_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                                        ?>
                                        <ul class="jobsearch-checkbox">
                                            <li>
                                                <input type="number"
                                                       name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                       id="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                       value="<?php echo jobsearch_esc_html($number_field_req_val); ?>"
                                                       onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                            </li>
                                        </ul>
                                        <?php
                                    } else if ($cus_field['type'] == 'date') {
                                        $fromdate_field_req_val = isset($_REQUEST['from-' . $query_str_var_name]) ? $_REQUEST['from-' . $query_str_var_name] : '';
                                        $todate_field_req_val = isset($_REQUEST['to-' . $query_str_var_name]) ? $_REQUEST['to-' . $query_str_var_name] : '';
                                        wp_enqueue_style('datetimepicker-style');
                                        wp_enqueue_script('datetimepicker-script');
                                        wp_enqueue_script('jquery-ui');
                                        $cus_field_date_formate_arr = explode(" ", $cus_field['date-format']);
                                        ?>

                                        <ul class="jobsearch-checkbox">
                                            <li>
                                                <div class="filter-datewise-con">
                                                    <script type="text/javascript">
                                                        jQuery(document).ready(function () {
                                                            jQuery("#from<?php echo jobsearch_esc_html($query_str_var_name) . $global_rand_id; ?>").datetimepicker({
                                                                format: "<?php echo jobsearch_esc_html($cus_field_date_formate_arr[0]); ?>",
                                                                timepicker: false
                                                            });
                                                            jQuery("#to<?php echo jobsearch_esc_html($query_str_var_name) . $global_rand_id; ?>").datetimepicker({
                                                                format: "<?php echo jobsearch_esc_html($cus_field_date_formate_arr[0]); ?>",
                                                                timepicker: false
                                                            });
                                                        });
                                                    </script>
                                                    <label for="from<?php echo jobsearch_esc_html($query_str_var_name) . $global_rand_id; ?>">
                                                        <input type="text"
                                                               name="from-<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                               id="from<?php echo jobsearch_esc_html($query_str_var_name) . $global_rand_id; ?>"
                                                               value="<?php echo jobsearch_esc_html($fromdate_field_req_val); ?>"
                                                               placeholder="<?php esc_html_e('Date From', 'wp-jobsearch') ?>"
                                                               onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                    </label>
                                                    <label for="to<?php echo jobsearch_esc_html($query_str_var_name) . $global_rand_id; ?>">
                                                        <input type="text"
                                                               name="to-<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                               id="to<?php echo jobsearch_esc_html($query_str_var_name) . $global_rand_id; ?>"
                                                               value="<?php echo jobsearch_esc_html($todate_field_req_val); ?>"
                                                               placeholder="<?php esc_html_e('Date To', 'wp-jobsearch') ?>"
                                                               onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                    </label>
                                                </div>
                                            </li>
                                        </ul>
                                        <?php
                                    } elseif ($cus_field['type'] == 'range') {

                                        $range_min = $cus_field['min'];
                                        $range_laps = $cus_field['laps'];
                                        $range_laps = $range_laps > 100 ? 100 : $range_laps;
                                        $range_interval = $cus_field['interval'];
                                        $range_field_type = isset($cus_field['field-style']) ? $cus_field['field-style'] : 'simple'; //input, slider, input_slider

                                        if (strpos($range_field_type, '-') !== FALSE) {
                                            $range_field_type_arr = explode("_", $range_field_type);
                                        } else {
                                            $range_field_type_arr[0] = $range_field_type;
                                        }
                                        $range_flag = 0;
                                    while (count($range_field_type_arr) > $range_flag) {
                                    if ($range_field_type_arr[$range_flag] == 'simple') { // if input style
                                        $filter_more_counter = 1;
                                        ?>
                                        <ul class="jobsearch-checkbox">
                                            <?php
                                            $loop_flag = 1;
                                            while ($loop_flag <= $range_laps) {
                                                ?>
                                            <li class="<?php echo($filter_more_counter > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">
                                                <?php
                                                // main query array $args_count
                                                $range_first = $range_min + 1;
                                                $range_seond = $range_min + $range_interval;
                                                $range_count_arr = array(
                                                    array(
                                                        'key' => $query_str_var_name,
                                                        'value' => $range_first,
                                                        'compare' => '>=',
                                                        'type' => 'numeric'
                                                    ),
                                                    array(
                                                        'key' => $query_str_var_name,
                                                        'value' => $range_seond,
                                                        'compare' => '<=',
                                                        'type' => 'numeric'
                                                    )
                                                );
                                                $range_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $range_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                                $custom_slider_selected = '';
                                                if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == (($range_min + 1) . "-" . ($range_min + $range_interval))) {
                                                    $custom_slider_selected = ' checked="checked"';
                                                }
                                                ?>
                                                <input type="radio"
                                                       name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                       id="<?php echo jobsearch_esc_html($query_str_var_name . $loop_flag); ?>"
                                                       value="<?php echo jobsearch_esc_html((($range_min + 1) . "-" . ($range_min + $range_interval))); ?>" <?php echo($custom_slider_selected); ?>
                                                       onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                <label for="<?php echo jobsearch_esc_html($query_str_var_name . $loop_flag); ?>"><span></span><?php echo force_balance_tags((($range_min + 1) . " - " . ($range_min + $range_interval))); ?>
                                                </label>
                                                <?php if ($left_filter_count_switch == 'yes') { ?>
                                                    <span class="filter-post-count"><?php echo absint($range_totnum); ?></span>
                                                <?php } ?>
                                                </li><?php
                                                $range_min = $range_min + $range_interval;
                                                $loop_flag++;
                                                $filter_more_counter++;
                                            }
                                            ?>
                                        </ul>
                                        <?php
                                        if ($filter_more_counter > 6) {
                                            echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
                                        }
                                    } elseif ($range_field_type_arr[$range_flag] == 'slider') { // if slider style
                                        wp_enqueue_style('jquery-ui');
                                        wp_enqueue_script('jquery-ui');
                                        $rand_id = rand(123, 1231231);
                                        $range_field_max = $range_min;
                                        $i = 0;
                                        while ($range_laps > $i) {
                                            $range_field_max = $range_field_max + $range_interval;
                                            $i++;
                                        }
                                        $range_complete_str_first = "";
                                        $range_complete_str_second = "";
                                        $range_complete_str = '';
                                        $range_complete_str_first = $range_min;
                                        $range_complete_str_second = $range_field_max;
                                        if (isset($_REQUEST[$query_str_var_name])) {
                                            $range_complete_str = $_REQUEST[$query_str_var_name];
                                            $range_complete_str_arr = explode("-", $range_complete_str);
                                            $range_complete_str_first = isset($range_complete_str_arr[0]) ? $range_complete_str_arr[0] : '';
                                            $range_complete_str_second = isset($range_complete_str_arr[1]) ? $range_complete_str_arr[1] : '';
                                        }
                                        ?>
                                        <ul class="jobsearch-checkbox">
                                            <li>
                                                <input type="text"
                                                       name="<?php echo jobsearch_esc_html($query_str_var_name) ?>"
                                                       id="<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"
                                                       value="<?php echo jobsearch_esc_html($range_complete_str); ?>"
                                                       readonly
                                                       style="border:0; color:#f6931f; font-weight:bold;"/>
                                                <div id="slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"></div>
                                                <script type="text/javascript">
                                                    jQuery(document).ready(function () {


                                                        jQuery("#slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider({
                                                            range: true,
                                                            min: <?php echo absint($range_min); ?>,
                                                            max: <?php echo absint($range_field_max); ?>,
                                                            values: [<?php echo absint($range_complete_str_first); ?>, <?php echo absint($range_complete_str_second); ?>],
                                                            slide: function (event, ui) {
                                                                jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(ui.values[0] + "-" + ui.values[1]);
                                                            },
                                                            stop: function (event, ui) {
                                                                <?php echo force_balance_tags($submit_js_function_str); ?>;
                                                            }
                                                        });
                                                        jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(jQuery("#slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 0) +
                                                            "-" + jQuery("#slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 1));
                                                    });
                                                </script>
                                            </li>
                                        </ul>
                                        <?php
                                    }
                                        $range_flag++;
                                    }
                                    } elseif ($cus_field['type'] == 'salary' && $salary_onoff_switch != 'off') {

                                        $job_salary_types = isset($jobsearch_plugin_options['job-salary-types']) ? $jobsearch_plugin_options['job-salary-types'] : '';

                                        $post_salary_types = isset($jobsearch_plugin_options['job-salary-types']) ? $jobsearch_plugin_options['job-salary-types'] : '';

                                        $salary_min = isset($cus_field['min']) ? $cus_field['min'] : '';
                                        $salary_laps = isset($cus_field['laps']) ? $cus_field['laps'] : '';
                                        $salary_laps = $salary_laps > 200 ? 200 : $salary_laps;
                                        $salary_interval = isset($cus_field['interval']) ? $cus_field['interval'] : '';

                                        $salary_field_type = isset($cus_field['field-style']) ? $cus_field['field-style'] : 'simple'; //input, slider, input_slider

                                        if (strpos($salary_field_type, '-') !== FALSE) {
                                            $salary_field_type_arr = explode("_", $salary_field_type);
                                        } else {
                                            $salary_field_type_arr[0] = $salary_field_type;
                                        }

                                        // Salary Types
                                    if (!empty($job_salary_types)) {
                                        $slar_type_count = 1;
                                        ?>
                                        <div class="jobsearch-salary-types-filter">
                                            <ul>
                                                <?php
                                                foreach ($job_salary_types as $job_salary_type) {
                                                    $job_salary_type = apply_filters('wpml_translate_single_string', $job_salary_type, 'JobSearch Options', 'Salary Type - ' . $job_salary_type, $lang_code);
                                                    $slalary_type_selected = '';
                                                    if (isset($_REQUEST[$str_salary_type_name]) && $_REQUEST[$str_salary_type_name] == 'type_' . $slar_type_count) {
                                                        $slalary_type_selected = ' checked="checked"';
                                                    }
                                                    ?>
                                                    <li class="salary-type-radio">
                                                        <input type="radio"
                                                               id="salary_type_<?php echo($slar_type_count) ?>"
                                                               name="<?php echo($str_salary_type_name) ?>"
                                                               class="job_salary_type"<?php echo($slalary_type_selected) ?>
                                                               value="type_<?php echo($slar_type_count) ?>"
                                                               onchange="<?php echo force_balance_tags($submit_js_function_str); ?>">
                                                        <label for="salary_type_<?php echo($slar_type_count) ?>">
                                                            <span></span>
                                                            <small><?php echo($job_salary_type) ?></small>
                                                        </label>
                                                    </li>
                                                    <?php
                                                    $slar_type_count++;
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                        <?php
                                    }
                                        //
                                        $salary_flag = 0;
                                    while (count($salary_field_type_arr) > $salary_flag) {
                                    if ($salary_field_type_arr[$salary_flag] == 'simple') { // if input style

                                    if (!empty($post_salary_types)) {
                                        $get_the_salary_type = isset($_REQUEST[$str_salary_type_name]) ? $_REQUEST[$str_salary_type_name] : '';
                                        $slar_type_count = 1;
                                    foreach ($post_salary_types as $post_salary_typ) {

                                        $salary_min = isset($cus_field['min' . $slar_type_count]) ? $cus_field['min' . $slar_type_count] : '';
                                        $salary_interval = isset($cus_field['interval' . $slar_type_count]) ? $cus_field['interval' . $slar_type_count] : '';
                                        $salary_laps = isset($cus_field['laps' . $slar_type_count]) ? $cus_field['laps' . $slar_type_count] : '';
                                        $salary_laps = $salary_laps > 200 ? 200 : $salary_laps;

                                        $style_tag = ' style="display: none;"';
                                        if ($get_the_salary_type == '' && $slar_type_count == 1) {
                                            $style_tag = '';
                                        } else if ($get_the_salary_type != '' && $get_the_salary_type == 'type_' . $slar_type_count) {
                                            $style_tag = '';
                                        }

                                        $filter_more_counter = 1;
                                        ?>
                                        <div class="salarytypes-rangelist-con salarytypes-rangeitm-<?php echo($slar_type_count) ?>"<?php echo($style_tag) ?>>
                                            <ul class="jobsearch-checkbox">
                                                <?php
                                                $loop_flag = 1;
                                                while ($loop_flag <= $salary_laps) { ?>
                                                <li class="<?php echo($filter_more_counter > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">
                                                    <?php
                                                    // main query array $args_count
                                                    $salary_first = $salary_min + 1;
                                                    $salary_seond = $salary_min + $salary_interval;
                                                    $salary_count_arr = array(
                                                        array(
                                                            'key' => $query_str_var_name,
                                                            'value' => $salary_first,
                                                            'compare' => '>=',
                                                            'type' => 'numeric'
                                                        ),
                                                        array(
                                                            'key' => $query_str_var_name,
                                                            'value' => $salary_seond,
                                                            'compare' => '<=',
                                                            'type' => 'numeric'
                                                        )
                                                    );
                                                    $salary_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $salary_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                                    $custom_slider_selected = '';
                                                    if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == (($salary_min + 1) . "-" . ($salary_min + $salary_interval))) {
                                                        $custom_slider_selected = ' checked="checked"';
                                                    }
                                                    ?>
                                                    <input type="radio"
                                                           name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                           id="<?php echo jobsearch_esc_html($query_str_var_name . $loop_flag); ?>"
                                                           value="<?php echo jobsearch_esc_html((($salary_min + 1) . "-" . ($salary_min + $salary_interval))); ?>" <?php echo($custom_slider_selected); ?>
                                                           onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                    <?php
                                                    $salary_from = ($salary_min + 1);
                                                    $salary_upto = ($salary_min + $salary_interval);
                                                    ?>
                                                    <label for="<?php echo jobsearch_esc_html($query_str_var_name . $loop_flag); ?>"><span></span><?php echo((jobsearch_get_price_format($salary_from) . " - " . jobsearch_get_price_format($salary_upto))); ?>
                                                    </label>
                                                    <?php if ($left_filter_count_switch == 'yes') { ?>
                                                        <span class="filter-post-count"><?php echo absint($salary_totnum); ?></span>
                                                    <?php } ?>
                                                    </li><?php
                                                    $salary_min = $salary_min + $salary_interval;
                                                    $loop_flag++;
                                                    $filter_more_counter++;
                                                }
                                                ?>
                                            </ul>
                                            <?php
                                            if ($filter_more_counter > 6) {
                                                echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
                                            }
                                            ?>
                                        </div>
                                        <?php

                                        $slar_type_count++;
                                    }
                                    } else {

                                        $filter_more_counter = 1;
                                        ?>
                                        <ul class="jobsearch-checkbox">
                                            <?php
                                            $loop_flag = 1;
                                            while ($loop_flag <= $salary_laps) { ?>
                                            <li class="<?php echo($filter_more_counter > 6 ? 'filter-more-fields' : '') ?><?php echo($left_filter_count_switch != 'yes' ? ' no-filter-counts' : '') ?>">
                                                <?php
                                                // main query array $args_count
                                                $salary_first = $salary_min + 1;
                                                $salary_seond = $salary_min + $salary_interval;
                                                $salary_count_arr = array(
                                                    array(
                                                        'key' => $query_str_var_name,
                                                        'value' => $salary_first,
                                                        'compare' => '>=',
                                                        'type' => 'numeric'
                                                    ),
                                                    array(
                                                        'key' => $query_str_var_name,
                                                        'value' => $salary_seond,
                                                        'compare' => '<=',
                                                        'type' => 'numeric'
                                                    )
                                                );
                                                $salary_totnum = jobsearch_get_item_count($left_filter_count_switch, $args_count, $salary_count_arr, $global_rand_id, $query_str_var_name, $custom_field_entity);
                                                $custom_slider_selected = '';
                                                if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == (($salary_min + 1) . "-" . ($salary_min + $salary_interval))) {
                                                    $custom_slider_selected = ' checked="checked"';
                                                }
                                                ?>
                                                <input type="radio"
                                                       name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                                       id="<?php echo jobsearch_esc_html($query_str_var_name . $loop_flag); ?>"
                                                       value="<?php echo jobsearch_esc_html((($salary_min + 1) . "-" . ($salary_min + $salary_interval))); ?>" <?php echo($custom_slider_selected); ?>
                                                       onchange="<?php echo force_balance_tags($submit_js_function_str); ?>"/>
                                                <?php
                                                $salary_from = ($salary_min + 1);
                                                $salary_upto = ($salary_min + $salary_interval);
                                                ?>
                                                <label for="<?php echo jobsearch_esc_html($query_str_var_name . $loop_flag); ?>"><span></span><?php echo((jobsearch_get_price_format($salary_from) . " - " . jobsearch_get_price_format($salary_upto))); ?>
                                                </label>
                                                <?php if ($left_filter_count_switch == 'yes') { ?>
                                                    <span class="filter-post-count"><?php echo absint($salary_totnum); ?></span>
                                                <?php } ?>
                                                </li><?php
                                                $salary_min = $salary_min + $salary_interval;
                                                $loop_flag++;
                                                $filter_more_counter++;
                                            }
                                            ?>
                                        </ul>
                                        <?php
                                        if ($filter_more_counter > 6) {
                                            echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
                                        }
                                    }
                                    } else if ($salary_field_type_arr[$salary_flag] == 'slider') { // if slider style
                                        wp_enqueue_style('jquery-ui');
                                        wp_enqueue_script('jquery-ui');
                                        $rand_id = rand(1231110, 9231231);
                                        $salary_field_max = absint($salary_min);
                                        $i = 0;
                                        while ($salary_laps > $i) {
                                            $salary_field_max = $salary_field_max + $salary_interval;
                                            $i++;
                                        }
                                        $salary_complete_str_first = "";
                                        $salary_complete_str_second = "";
                                        $salary_complete_str = '';
                                        $salary_complete_str_first = $salary_min;
                                        $salary_complete_str_second = $salary_field_max;
                                        if (isset($_REQUEST[$query_str_var_name])) {
                                            $salary_complete_str = $_REQUEST[$query_str_var_name];
                                            $salary_complete_str_arr = explode("-", $salary_complete_str);
                                            $salary_complete_str_first = isset($salary_complete_str_arr[0]) ? $salary_complete_str_arr[0] : '';
                                            $salary_complete_str_second = isset($salary_complete_str_arr[1]) ? $salary_complete_str_arr[1] : '';
                                        }
                                        ?>
                                        <ul class="jobsearch-checkbox">
                                            <li class="salary-filter-slider">
                                                <div class="filter-slider-range">
                                                    <input type="text"
                                                           name="<?php echo jobsearch_esc_html($query_str_var_name) ?>"
                                                           id="<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"
                                                           value="<?php echo jobsearch_esc_html($salary_complete_str); ?>"
                                                           readonly style="border:0; color:#f6931f; font-weight:bold;"/>
                                                </div>
                                                <div id="slider-salary<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"></div>
                                                <script type="text/javascript">
                                                    jQuery(document).ready(function () {

                                                        jQuery("#slider-salary<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider({
                                                            salary: true,
                                                            min: <?php echo absint($salary_min); ?>,
                                                            max: <?php echo absint($salary_field_max); ?>,
                                                            values: [<?php echo absint($salary_complete_str_first); ?>, <?php echo absint($salary_complete_str_second); ?>],
                                                            slide: function (event, ui) {
                                                                jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(ui.values[0] + "-" + ui.values[1]);
                                                            },
                                                            stop: function (event, ui) {
                                                                <?php echo force_balance_tags($submit_js_function_str); ?>;
                                                            }
                                                        });
                                                        jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(jQuery("#slider-salary<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 0) +
                                                            "-" + jQuery("#slider-salary<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 1));
                                                    });
                                                </script>
                                            </li>
                                        </ul>
                                        <?php
                                    }
                                        $salary_flag++;
                                    }
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
            }
        }
        $html .= ob_get_clean();
        return $html;
    }

    static function custom_fields_top_filter_box_html_callback($html, $custom_field_entity = '', $global_rand_id, $allow_type = 'adv_search')
    {
        global $jobsearch_form_fields, $jobsearch_plugin_options, $sitepress;

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        $submit_js_function_str = '';

        //
        $salary_onoff_switch = isset($jobsearch_plugin_options['salary_onoff_switch']) ? $jobsearch_plugin_options['salary_onoff_switch'] : '';

        $job_cus_fields = get_option("jobsearch_custom_field_" . $custom_field_entity);
        ob_start();
        $custom_field_flag = 11;
        if (!empty($job_cus_fields)) {
            foreach ($job_cus_fields as $cus_fieldvar => $cus_field) {
                $all_item_empty = 0;
                if (isset($cus_field['options']['value']) && is_array($cus_field['options']['value'])) {
                    foreach ($cus_field['options']['value'] as $cus_field_options_value) {

                        if ($cus_field_options_value != '') {
                            $all_item_empty = 0;
                            break;
                        } else {
                            $all_item_empty = 1;
                        }
                    }
                }

                $cus_field_label_arr = isset($cus_field['label']) ? $cus_field['label'] : '';
                $type = isset($cus_field['type']) ? $cus_field['type'] : '';

                if ($type == 'text') {
                    $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Text Field Label - ' . $cus_field_label_arr, $lang_code);
                } else if ($type == 'email') {
                    $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Email Field Label - ' . $cus_field_label_arr, $lang_code);
                } else if ($type == 'number') {
                    $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Number Field Label - ' . $cus_field_label_arr, $lang_code);
                } else if ($type == 'date') {
                    $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Date Field Label - ' . $cus_field_label_arr, $lang_code);
                } else if ($type == 'checkbox') {
                    $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Checkbox Field Label - ' . $cus_field_label_arr, $lang_code);
                } else if ($type == 'dropdown') {
                    $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Dropdown Field Label - ' . $cus_field_label_arr, $lang_code);
                } else if ($type == 'range') {
                    $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Range Field Label - ' . $cus_field_label_arr, $lang_code);
                } else if ($type == 'textarea') {
                    $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Textarea Field Label - ' . $cus_field_label_arr, $lang_code);
                } else if ($type == 'heading') {
                    $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Heading Field Label - ' . $cus_field_label_arr, $lang_code);
                } else if ($type == 'salary') {
                    $cus_field_label_arr = apply_filters('wpml_translate_single_string', $cus_field_label_arr, 'Custom Fields', 'Salary Label - ' . $cus_field_label_arr, $lang_code);
                }

                $cus_field_label_arr = stripslashes($cus_field_label_arr);

                $dropdown_main_class = 'jobsearch-select-style';
                $field_label_html = '';
                $enable_this_fields = false;
                if (isset($cus_field['enable-advsrch']) && ($cus_field['enable-advsrch'] == 'yes' || $cus_field['enable-advsrch'] == 'on')) {
                    $enable_this_fields = true;
                }
                if ($allow_type == 'enable_search' && isset($cus_field['enable-search']) && ($cus_field['enable-search'] == 'yes' || $cus_field['enable-search'] == 'on')) {
                    $enable_this_fields = true;
                    $dropdown_main_class = 'jobsearch-profile-select';
                    $field_label_html = '<label>' . $cus_field_label_arr . '</label>';
                }

                if ($enable_this_fields && ($all_item_empty == 0)) {
                    if ($cus_field['type'] == 'salary') {
                        $query_str_var_name = 'jobsearch_field_job_salary';
                        $str_salary_type_name = 'job_salary_type';
                        if ($custom_field_entity == 'candidate') {
                            $query_str_var_name = 'jobsearch_field_candidate_salary';
                            $str_salary_type_name = 'candidate_salary_type';
                        }
                    } else {
                        $query_str_var_name = isset($cus_field['name']) ? $cus_field['name'] : '';
                    }
                    $collapse_condition = 'no';
                    if (isset($cus_field['collapse-search'])) {
                        $collapse_condition = $cus_field['collapse-search'];
                    }

                    if ($cus_field['type'] == 'heading' && $cus_field_label_arr != '') { ?>
                        <li class="advsrch-fields-hdng">
                            <h2><?php echo($cus_field_label_arr) ?></h2>
                        </li>
                        <?php
                    }

                    $filter_args = array(
                        'custom_field_entity' => $custom_field_entity,
                        'global_rand_id' => $global_rand_id,
                        'allow_type' => $allow_type,
                        'cus_field' => $cus_field,
                        'cus_fieldvar' => $cus_fieldvar,
                    );
                    echo apply_filters('jobsearch_cusfields_top_filters_before_dropdwn', '', $filter_args);

                    if ($cus_field['type'] == 'dropdown' || $cus_field['type'] == 'checkbox') {
                        $request_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                        $request_val_arr = explode(",", $request_val);

                        $number_option_flag = 1;
                        $cut_field_flag = 0;
                        if (isset($cus_field['multi']) && $cus_field['multi'] == 'yes') {
                            $select_param = 'multiple="multiple"';
                            $select_class = 'jobsearch-select-multi';
                        } else {
                            $select_param = '';
                            $select_class = '';
                        }

                        ?>
                        <li>
                            <?php echo($field_label_html) ?>
                            <div class="<?php echo($dropdown_main_class) ?> <?php echo $select_class ?>">
                                <select name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                        class="selectize-select"<?php echo apply_filters('jobsearch_listin_top_filtcusfield_dropdwn_exatts', '', $global_rand_id, array(), $custom_field_entity) ?> <?php echo($select_param) ?>
                                        placeholder="<?php echo($cus_field_label_arr); ?>">
                                    <?php

                                    $cutsf_field_flag = 1;
                                    foreach ($cus_field['options']['value'] as $cus_field_options_value) {
                                        if ($cus_field['options']['value'][$cut_field_flag] == '' || $cus_field['options']['label'][$cut_field_flag] == '') {
                                            $cut_field_flag++;
                                            continue;
                                        }

                                        if ($cus_field_options_value != '') {
                                            if (isset($cus_field['multi']) && $cus_field['multi'] == 'yes') {
                                                $checked = '';

                                                if (!empty($request_val_arr) && in_array($cus_field_options_value, $request_val_arr)) {
                                                    $checked = 'selected="selected"';
                                                }

                                                ?>
                                                <option value="<?php echo jobsearch_esc_html($cus_field_options_value); ?>" <?php echo($checked) ?>><?php echo(apply_filters('wpml_translate_single_string', $cus_field['options']['label'][$cut_field_flag], 'Custom Fields', 'Dropdown Option Label - ' . $cus_field['options']['label'][$cut_field_flag], $lang_code)); ?></option>
                                            <?php } else {
                                                if ($cutsf_field_flag == 1) { ?>
                                                    <option value=""><?php echo($cus_field_label_arr); ?></option>
                                                    <?php
                                                }
                                                $custom_dropdown_selected = '';
                                                if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == $cus_field_options_value) {
                                                    $custom_dropdown_selected = ' selected="selected"';
                                                } ?>
                                                <option value="<?php echo jobsearch_esc_html($cus_field_options_value); ?>" <?php echo($custom_dropdown_selected) ?>><?php echo(apply_filters('wpml_translate_single_string', $cus_field['options']['label'][$cut_field_flag], 'Custom Fields', 'Dropdown Option Label - ' . $cus_field['options']['label'][$cut_field_flag], $lang_code)); ?></option>
                                                <?php
                                                $cutsf_field_flag++;
                                            }
                                        }
                                        $number_option_flag++;
                                        $cut_field_flag++;
                                    }
                                    ?>
                                </select>
                            </div>
                        </li>
                        <?php
                    } else if ($cus_field['type'] == 'text' || $cus_field['type'] == 'email') {
                        $text_field_req_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                        ?>
                        <li>
                            <?php echo($field_label_html) ?>
                            <input type="text" name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                   id="<?php echo jobsearch_esc_html($query_str_var_name); ?>"<?php echo apply_filters('jobsearch_listin_top_filtcusfield_txt_exatts', '', $global_rand_id, array(), $custom_field_entity) ?>
                                   placeholder="<?php echo($cus_field_label_arr) ?>"
                                   value="<?php echo jobsearch_esc_html($text_field_req_val); ?>"/>
                        </li>
                        <?php
                    } else if ($cus_field['type'] == 'textarea') {
                        $text_field_req_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                        ?>
                        <li>
                            <?php echo($field_label_html) ?>
                            <textarea name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                      id="<?php echo jobsearch_esc_html($query_str_var_name); ?>"<?php echo apply_filters('jobsearch_listin_top_filtcusfield_txtarea_exatts', '', $global_rand_id, array(), $custom_field_entity) ?>
                                      placeholder="<?php echo($cus_field_label_arr) ?>"><?php echo jobsearch_esc_html($text_field_req_val); ?></textarea>
                        </li>
                        <?php
                    } else if ($cus_field['type'] == 'number') {
                        $number_field_req_val = isset($_REQUEST[$query_str_var_name]) ? $_REQUEST[$query_str_var_name] : '';
                        ?>
                        <li>
                            <?php echo($field_label_html) ?>
                            <input type="number" name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                   id="<?php echo jobsearch_esc_html($query_str_var_name); ?>"<?php echo apply_filters('jobsearch_listin_top_filtcusfield_num_exatts', '', $global_rand_id, array(), $custom_field_entity) ?>
                                   placeholder="<?php echo($cus_field_label_arr) ?>"
                                   value="<?php echo jobsearch_esc_html($number_field_req_val); ?>"/>
                        </li>
                        <?php
                    } else if ($cus_field['type'] == 'date') {
                        $fromdate_field_req_val = isset($_REQUEST['from-' . $query_str_var_name]) ? $_REQUEST['from-' . $query_str_var_name] : '';
                        $todate_field_req_val = isset($_REQUEST['to-' . $query_str_var_name]) ? $_REQUEST['to-' . $query_str_var_name] : '';
                        wp_enqueue_style('datetimepicker-style');
                        wp_enqueue_script('datetimepicker-script');
                        wp_enqueue_script('jquery-ui');
                        $cus_field_date_formate_arr = explode(" ", $cus_field['date-format']);
                        ?>
                        <li>
                            <?php echo($field_label_html) ?>
                            <div class="filter-datewise-con">
                                <script type="text/javascript">
                                    jQuery(document).ready(function () {
                                        jQuery("#from<?php echo jobsearch_esc_html($query_str_var_name); ?>").datetimepicker({
                                            format: "<?php echo jobsearch_esc_html($cus_field_date_formate_arr[0]); ?>",
                                            timepicker: false
                                        });
                                        jQuery("#to<?php echo jobsearch_esc_html($query_str_var_name); ?>").datetimepicker({
                                            format: "<?php echo jobsearch_esc_html($cus_field_date_formate_arr[0]); ?>",
                                            timepicker: false
                                        });
                                    });
                                </script>
                                <label for="from<?php echo jobsearch_esc_html($query_str_var_name); ?>">
                                    <input type="text"
                                           name="from-<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                           id="from<?php echo jobsearch_esc_html($query_str_var_name); ?>"<?php echo apply_filters('jobsearch_listin_top_filtcusfield_datefrm_exatts', '', $global_rand_id, array(), $custom_field_entity) ?>
                                           placeholder="<?php esc_html_e('Date From', 'wp-jobsearch') ?>"
                                           value="<?php echo jobsearch_esc_html($fromdate_field_req_val); ?>"/>
                                </label>
                                <label for="to<?php echo jobsearch_esc_html($query_str_var_name); ?>">
                                    <input type="text" name="to-<?php echo jobsearch_esc_html($query_str_var_name); ?>"
                                           id="to<?php echo jobsearch_esc_html($query_str_var_name); ?>"<?php echo apply_filters('jobsearch_listin_top_filtcusfield_dateto_exatts', '', $global_rand_id, array(), $custom_field_entity) ?>
                                           placeholder="<?php esc_html_e('To From', 'wp-jobsearch') ?>"
                                           value="<?php echo jobsearch_esc_html($todate_field_req_val); ?>"/>
                                </label>
                            </div>
                        </li>
                        <?php
                    } elseif ($cus_field['type'] == 'range') {

                        $range_min = $cus_field['min'];
                        $range_laps = $cus_field['laps'];
                        $range_interval = $cus_field['interval'];
                        $range_field_type = isset($cus_field['field-style']) ? $cus_field['field-style'] : 'simple'; //input, slider, input_slider

                        if (strpos($range_field_type, '-') !== FALSE) {
                            $range_field_type_arr = explode("_", $range_field_type);
                        } else {
                            $range_field_type_arr[0] = $range_field_type;
                        }
                        $range_flag = 0;
                        while (count($range_field_type_arr) > $range_flag) {
                            if ($range_field_type_arr[$range_flag] == 'simple') { // if input style
                                $filter_more_counter = 1;
                                $loop_flag = 1;
                                ?>
                                <li>
                                    <?php echo($field_label_html) ?>
                                    <div class="<?php echo($dropdown_main_class) ?>">
                                        <select name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"<?php echo apply_filters('jobsearch_listin_top_filtcusfield_range_exatts', '', $global_rand_id, array(), $custom_field_entity) ?>
                                                class="selectize-select"
                                                placeholder="<?php echo($cus_field_label_arr); ?>">
                                            <?php
                                            while ($loop_flag <= $range_laps) {

                                                // main query array $args_count
                                                $range_first = $range_min + 1;
                                                $range_seond = $range_min + $range_interval;

                                                $custom_slider_selected = '';
                                                if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == (($range_min + 1) . "-" . ($range_min + $range_interval))) {
                                                    $custom_slider_selected = ' selected="selected"';
                                                }
                                                if ($loop_flag == 1) {
                                                    ?>
                                                    <option value=""><?php echo($cus_field_label_arr); ?></option>
                                                    <?php
                                                }
                                                ?>
                                                <option value="<?php echo jobsearch_esc_html((($range_min + 1) . "-" . ($range_min + $range_interval))); ?>" <?php echo($custom_slider_selected) ?>><?php echo force_balance_tags((($range_min + 1) . " - " . ($range_min + $range_interval))); ?></option>
                                                <?php
                                                $range_min = $range_min + $range_interval;
                                                $loop_flag++;
                                                $filter_more_counter++;
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </li>
                                <?php
                                if ($filter_more_counter > 6) {
                                    //echo '<a href="javascript:void(0);" class="show-toggle-filter-list">' . esc_html__('+ see more', 'wp-jobsearch') . '</a>';
                                }
                            } elseif ($range_field_type_arr[$range_flag] == 'slider') { // if slider style 
                                wp_enqueue_style('jquery-ui');
                                wp_enqueue_script('jquery-ui');
                                $rand_id = rand(123, 1231231);
                                $range_field_max = $range_min;
                                $i = 0;
                                while ($range_laps > $i) {
                                    $range_field_max = $range_field_max + $range_interval;
                                    $i++;
                                }
                                $range_complete_str_first = "";
                                $range_complete_str_second = "";
                                $range_complete_str = '';
                                $range_complete_str_first = $range_min;
                                $range_complete_str_second = $range_field_max;
                                if (isset($_REQUEST[$query_str_var_name])) {
                                    $range_complete_str = $_REQUEST[$query_str_var_name];
                                    $range_complete_str_arr = explode("-", $range_complete_str);
                                    $range_complete_str_first = isset($range_complete_str_arr[0]) ? $range_complete_str_arr[0] : '';
                                    $range_complete_str_second = isset($range_complete_str_arr[1]) ? $range_complete_str_arr[1] : '';
                                }
                                ?>
                                <li>
                                    <?php echo($field_label_html) ?>
                                    <input type="text" name="<?php echo jobsearch_esc_html($query_str_var_name) ?>"
                                           id="<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"<?php echo apply_filters('jobsearch_listin_top_filtcusfield_range_exatts', '', $global_rand_id, array(), $custom_field_entity) ?>
                                           value="<?php echo jobsearch_esc_html($range_complete_str); ?>" readonly
                                           style="border:0; color:#f6931f; font-weight:bold;"/>
                                    <div id="slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"></div>
                                    <script type="text/javascript">
                                        jQuery(document).ready(function () {
                                            jQuery("#slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider({
                                                range: true,
                                                min: <?php echo absint($range_min); ?>,
                                                max: <?php echo absint($range_field_max); ?>,
                                                values: [<?php echo absint($range_complete_str_first); ?>, <?php echo absint($range_complete_str_second); ?>],
                                                slide: function (event, ui) {
                                                    jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(ui.values[0] + "-" + ui.values[1]);
                                                },
                                                stop: function (event, ui) {
                                                    <?php //echo force_balance_tags($submit_js_function_str); ?>;
                                                }
                                            });
                                            jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(jQuery("#slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 0) +
                                                "-" + jQuery("#slider-range<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 1));
                                        });
                                    </script>
                                </li>
                                <?php
                            }
                            $range_flag++;
                        }
                    } else if ($cus_field['type'] == 'salary' && $salary_onoff_switch != 'off') {

                        $job_salary_types = isset($jobsearch_plugin_options['job-salary-types']) ? $jobsearch_plugin_options['job-salary-types'] : '';

                        $salary_min = $cus_field['min'];
                        $salary_laps = $cus_field['laps'];
                        $salary_laps = $salary_laps > 200 ? 200 : $salary_laps;
                        $salary_interval = $cus_field['interval'];
                        $salary_field_type = isset($cus_field['field-style']) ? $cus_field['field-style'] : 'simple'; //input, slider, input_slider

                        if (strpos($salary_field_type, '-') !== FALSE) {
                            $salary_field_type_arr = explode("_", $salary_field_type);
                        } else {
                            $salary_field_type_arr[0] = $salary_field_type;
                        }

                        // Salary Types
                        if (!empty($job_salary_types)) {
                            $slar_type_count = 1;
                            ob_start();
                            ?>
                            <li>
                                <?php echo($field_label_html) ?>
                                <div class="<?php echo($dropdown_main_class) ?>">
                                    <select name="<?php echo jobsearch_esc_html($str_salary_type_name); ?>"<?php echo apply_filters('jobsearch_listin_top_filtcusfield_salrytype_exatts', '', $global_rand_id, array(), $custom_field_entity) ?>
                                            class="selectize-select"
                                            placeholder="<?php esc_html_e('Salary Type', 'wp-jobsearch') ?>">
                                        <option value=""><?php esc_html_e('Salary Type', 'wp-jobsearch') ?></option>
                                        <?php
                                        foreach ($job_salary_types as $job_salary_type) {
                                            $job_salary_type = apply_filters('wpml_translate_single_string', $job_salary_type, 'JobSearch Options', 'Salary Type - ' . $job_salary_type, $lang_code);
                                            $slalary_type_selected = '';
                                            if (isset($_REQUEST[$str_salary_type_name]) && $_REQUEST[$str_salary_type_name] == 'type_' . $slar_type_count) {
                                                $slalary_type_selected = ' selected="selected"';
                                            }
                                            ?>
                                            <option value="type_<?php echo($slar_type_count) ?>" <?php echo($slalary_type_selected) ?>><?php echo($job_salary_type); ?></option>
                                            <?php
                                            $slar_type_count++;
                                        }
                                        ?>
                                    </select>
                                </div>
                            </li>
                            <?php
                            $advsrch_slrytype_html = ob_get_clean();
                            echo apply_filters('jobsearch_joblistn_advsrch_salrytype_html', $advsrch_slrytype_html);
                        }
                        //
                        ?>
                        <li>
                            <?php echo($field_label_html) ?>
                            <?php
                            $salary_flag = 0;
                            while (count($salary_field_type_arr) > $salary_flag) {
                                if ($salary_field_type_arr[$salary_flag] == 'simple') { // if input style
                                    $filter_more_counter = 1;
                                    ?>
                                    <div class="<?php echo($dropdown_main_class) ?>">
                                        <select name="<?php echo jobsearch_esc_html($query_str_var_name); ?>"<?php echo apply_filters('jobsearch_listin_top_filtcusfield_salry_exatts', '', $global_rand_id, array(), $custom_field_entity) ?>
                                                class="selectize-select"
                                                placeholder="<?php echo($cus_field_label_arr); ?>">
                                            <?php
                                            $loop_flag = 1;
                                            while ($loop_flag <= $salary_laps) {

                                                // main query array $args_count
                                                $salary_first = $salary_min + 1;
                                                $salary_seond = $salary_min + $salary_interval;

                                                $custom_slider_selected = '';
                                                if (isset($_REQUEST[$query_str_var_name]) && $_REQUEST[$query_str_var_name] == (($salary_min + 1) . "-" . ($salary_min + $salary_interval))) {
                                                    $custom_slider_selected = ' selected="selected"';
                                                }
                                                if ($loop_flag == 1) {
                                                    ?>
                                                    <option value=""><?php echo($cus_field_label_arr); ?></option>
                                                    <?php
                                                }
                                                ?>
                                                <option value="<?php echo jobsearch_esc_html((($salary_min + 1) . "-" . ($salary_min + $salary_interval))); ?>" <?php echo($custom_slider_selected) ?>><?php echo force_balance_tags((jobsearch_get_price_format($salary_min + 1) . " - " . jobsearch_get_price_format($salary_min + $salary_interval))); ?></option>
                                                <?php
                                                $salary_min = $salary_min + $salary_interval;
                                                $loop_flag++;
                                                $filter_more_counter++;
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <?php
                                } elseif ($salary_field_type_arr[$salary_flag] == 'slider') { // if slider style 
                                    wp_enqueue_style('jquery-ui');
                                    wp_enqueue_script('jquery-ui');
                                    $rand_id = rand(1231110, 9231231);
                                    $salary_field_max = $salary_min;
                                    $i = 0;
                                    while ($salary_laps > $i) {
                                        $salary_field_max = $salary_field_max + $salary_interval;
                                        $i++;
                                    }
                                    $salary_complete_str_first = "";
                                    $salary_complete_str_second = "";
                                    $salary_complete_str = '';
                                    $salary_complete_str_first = $salary_min;
                                    $salary_complete_str_second = $salary_field_max;
                                    if (isset($_REQUEST[$query_str_var_name])) {
                                        $salary_complete_str = $_REQUEST[$query_str_var_name];
                                        $salary_complete_str_arr = explode("-", $salary_complete_str);
                                        $salary_complete_str_first = isset($salary_complete_str_arr[0]) ? $salary_complete_str_arr[0] : '';
                                        $salary_complete_str_second = isset($salary_complete_str_arr[1]) ? $salary_complete_str_arr[1] : '';
                                    }
                                    ?>
                                    <div class="salary-filter-slider">
                                        <div class="filter-slider-range">
                                            <input type="text"
                                                   name="<?php echo jobsearch_esc_html($query_str_var_name) ?>"<?php echo apply_filters('jobsearch_listin_top_filtcusfield_salry_exatts', '', $global_rand_id, array(), $custom_field_entity) ?>
                                                   id="<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"
                                                   value="<?php echo jobsearch_esc_html($salary_complete_str); ?>"
                                                   readonly
                                                   style="border:0; color:#f6931f; font-weight:bold;"/>
                                        </div>
                                        <div id="slider-salary<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>"></div>
                                        <script type="text/javascript">
                                            jQuery(document).ready(function () {

                                                jQuery("#slider-salary<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider({
                                                    salary: true,
                                                    min: <?php echo absint($salary_min); ?>,
                                                    max: <?php echo absint($salary_field_max); ?>,
                                                    values: [<?php echo absint($salary_complete_str_first); ?>, <?php echo absint($salary_complete_str_second); ?>],
                                                    slide: function (event, ui) {
                                                        jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(ui.values[0] + "-" + ui.values[1]);
                                                    },
                                                    stop: function (event, ui) {
                                                        <?php //echo force_balance_tags($submit_js_function_str); ?>;
                                                    }
                                                });
                                                jQuery("#<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").val(jQuery("#slider-salary<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 0) +
                                                    "-" + jQuery("#slider-salary<?php echo jobsearch_esc_html($query_str_var_name . $rand_id) ?>").slider("values", 1));
                                            });
                                        </script>
                                    </div>
                                    <?php
                                }
                                $salary_flag++;
                            }
                            ?>
                        </li>
                        <?php
                    }
                }
            }
        }
        $html .= ob_get_clean();
        return $html;
    }

    static function jobsearch_custom_fields_load_filter_array_html_callback($custom_field_entity = '', $filter_arr, $exclude_meta_key, $cust_request_arr = '')
    {
        $field_db_slug = "jobsearch_custom_field_" . $custom_field_entity;

        if (!empty($cust_request_arr)) {
            $_request_arr = $cust_request_arr;
        } else {
            $_request_arr = $_REQUEST;
        }

        $jobsearch_post_cus_fields = get_option($field_db_slug);
        if (is_array($jobsearch_post_cus_fields) && sizeof($jobsearch_post_cus_fields) > 0) {
            $custom_field_flag = 1;
            foreach ($jobsearch_post_cus_fields as $cus_fieldvar => $cus_field) {
                if ($cus_field['type'] == 'salary') {
                    $cus_field['enable-search'] = 'yes';
                }
                if ((isset($cus_field['enable-search']) && $cus_field['enable-search'] == 'yes') || (isset($cus_field['enable-advsrch']) && $cus_field['enable-advsrch'] == 'yes')) {

                    if ($cus_field['type'] == 'salary') {
                        $query_str_var_name = 'jobsearch_field_job_salary';
                        $str_salary_type_name = 'job_salary_type';
                        if (isset($_request_arr['jobsearch_field_candidate_salary'])) {
                            $query_str_var_name = 'jobsearch_field_candidate_salary';
                        }
                        if (isset($_request_arr['candidate_salary_type'])) {
                            $str_salary_type_name = 'candidate_salary_type';
                        }
                    } else {
                        $f_custf_name = isset($cus_field['name']) ? $cus_field['name'] : '';
                        $query_str_var_name = trim(str_replace(' ', '', $f_custf_name));
                    }

                    // only for date type field need to change field name
                    if ($exclude_meta_key != $query_str_var_name) {
                        if ($cus_field['type'] == 'date') {
                            if ($cus_field['type'] == 'date') {

                                $from_date = 'from-' . $query_str_var_name;
                                $to_date = 'to-' . $query_str_var_name;
                                if (isset($_request_arr[$from_date]) && $_request_arr[$from_date] != '') {
                                    $filter_arr[] = array(
                                        'key' => $query_str_var_name,
                                        'value' => strtotime($_request_arr[$from_date]),
                                        'compare' => '>=',
                                    );
                                }
                                if (isset($_request_arr[$to_date]) && $_request_arr[$to_date] != '') {
                                    $filter_arr[] = array(
                                        'key' => $query_str_var_name,
                                        'value' => strtotime($_request_arr[$to_date]),
                                        'compare' => '<=',
                                    );
                                }
                            }
                        } else if (isset($_request_arr[$query_str_var_name]) && $_request_arr[$query_str_var_name] != '') {

                            if ($cus_field['type'] == 'dropdown' || $cus_field['type'] == 'checkbox') {
                                if (isset($cus_field['multi']) && $cus_field['multi'] == 'yes') {
                                    $dropdown_query_str_var_name = explode(",", $_request_arr[$query_str_var_name]);
                                    if (isset($dropdown_query_str_var_name[0]) && $dropdown_query_str_var_name[0] !== 'null') {
                                        $filter_multi_arr = array();
                                        $filter_multi_arr ['relation'] = 'OR';
                                        //var_dump($dropdown_query_str_var_name);
                                        foreach ($dropdown_query_str_var_name as $query_str_var_name_key) {
                                            if ($query_str_var_name_key == 'null') {
                                                $query_str_var_name_key = '';
                                            }
                                            if (isset($cus_field['post-multi']) && $cus_field['post-multi'] == 'yes') {
                                                $filter_multi_arr[] = array(
                                                    'key' => $query_str_var_name,
                                                    'value' => ($query_str_var_name_key),
                                                    'compare' => 'LIKE',
                                                );
                                            } else {
                                                $filter_multi_arr[] = array(
                                                    'key' => $query_str_var_name,
                                                    'value' => $query_str_var_name_key,
                                                    'compare' => 'LIKE',
                                                );
                                            }
                                        }
                                        $filter_arr[] = array(
                                            $filter_multi_arr
                                        );
                                    }
                                } else {
                                    if (isset($_request_arr[$query_str_var_name]) && $_request_arr[$query_str_var_name] == 'null') {
                                        $_request_arr[$query_str_var_name] = '';
                                    }
                                    if (isset($cus_field['post-multi']) && $cus_field['post-multi'] == 'yes') {

                                        $filter_arr[] = array(
                                            'key' => $query_str_var_name,
                                            'value' => ($_request_arr[$query_str_var_name]),
                                            'compare' => 'LIKE',
                                        );
                                    } else {
                                        $filter_arr[] = array(
                                            'key' => $query_str_var_name,
                                            'value' => $_request_arr[$query_str_var_name],
                                            'compare' => '=',
                                        );
                                    }
                                }
                            } elseif ($cus_field['type'] == 'dependent_dropdown') {
                                if ($_request_arr[$query_str_var_name] != '') {
                                    $filter_arr[] = array(
                                        'key' => $query_str_var_name,
                                        'value' => $_request_arr[$query_str_var_name],
                                        'compare' => 'LIKE',
                                    );
                                }
                            } elseif ($cus_field['type'] == 'text' || $cus_field['type'] == 'email') {
                                if ($_request_arr[$query_str_var_name] != '') {
                                    $filter_arr[] = array(
                                        'key' => $query_str_var_name,
                                        'value' => $_request_arr[$query_str_var_name],
                                        'compare' => 'LIKE',
                                    );
                                }
                            } elseif ($cus_field['type'] == 'number') {
                                if ($_request_arr[$query_str_var_name] != 0 && $_request_arr[$query_str_var_name] != '') {
                                    $filter_arr[] = array(
                                        'key' => $query_str_var_name,
                                        'value' => $_request_arr[$query_str_var_name],
                                        'compare' => '>=',
                                        'type' => 'numeric'
                                    );
                                }
                            } elseif ($cus_field['type'] == 'range') {
                                $ranges_str_arr = explode("-", $_request_arr[$query_str_var_name]);
                                if (!isset($ranges_str_arr[1])) {
                                    $ranges_str_arr = explode("-", $ranges_str_arr[0]);
                                }
                                $range_first = $ranges_str_arr[0];
                                $range_seond = $ranges_str_arr[1];
                                $filter_arr[] = array(
                                    'key' => $query_str_var_name,
                                    'value' => $range_first,
                                    'compare' => '>=',
                                    'type' => 'numeric'
                                );
                                $filter_arr[] = array(
                                    'key' => $query_str_var_name,
                                    'value' => $range_seond,
                                    'compare' => '<=',
                                    'type' => 'numeric'
                                );
                            }
                        }
                        if ($cus_field['type'] == 'salary') {

                            if (isset($_request_arr[$query_str_var_name]) && $_request_arr[$query_str_var_name] != '') {
                                $salarys_str_arr = explode("-", $_request_arr[$query_str_var_name]);
                                if (!isset($salarys_str_arr[1])) {
                                    $salarys_str_arr = explode("-", $salarys_str_arr[0]);
                                }
                                $salary_first = isset($salarys_str_arr[0]) ? $salarys_str_arr[0] : '';
                                $salary_seond = isset($salarys_str_arr[1]) ? $salarys_str_arr[1] : '';
                                $filter_arr[] = array(
                                    'key' => $query_str_var_name,
                                    'value' => $salary_first,
                                    'compare' => '>=',
                                    'type' => 'numeric'
                                );
                                $filter_arr[] = array(
                                    'key' => $query_str_var_name,
                                    'value' => $salary_seond,
                                    'compare' => '<=',
                                    'type' => 'numeric'
                                );
                            }

                            $salary_type_str = isset($_request_arr[$str_salary_type_name]) ? $_request_arr[$str_salary_type_name] : '';
                            if ($salary_type_str != '') {
                                $filter_arr[] = array(
                                    'key' => 'jobsearch_field_' . $str_salary_type_name,
                                    'value' => $salary_type_str,
                                    'compare' => '=',
                                );
                            }
                            //
                        }
                    }
                }
                $custom_field_flag++;
            }
        }
        $filtr_args = array(
            'custom_field_entity' => $custom_field_entity,
            'exclude_meta_key' => $exclude_meta_key,
            'cust_request_arr' => $cust_request_arr,
        );
        return apply_filters('jobsearch_listing_filters_cusf_query_arr', $filter_arr, $filtr_args);
    }

    static function jobsearch_custom_fields_load_precentage_array_callback($custom_field_entity = '', $skills_array = array())
    {
        $field_db_slug = "jobsearch_custom_field_" . $custom_field_entity;

        $jobsearch_post_cus_fields = get_option($field_db_slug);
        if (is_array($jobsearch_post_cus_fields) && sizeof($jobsearch_post_cus_fields) > 0) {
            $custom_fields_array = array();

            $skills_array['custom_fields']['name'] = esc_html__('Custom Fields', 'wp-jobsearch');
            foreach ($jobsearch_post_cus_fields as $job_field) {
                $meta_key = isset($job_field['name']) ? $job_field['name'] : '';
                $field_label = isset($job_field['label']) ? $job_field['label'] : '';
                if ($meta_key != '' && $field_label != '') {
                    $custom_fields_array[$meta_key] = array(
                        'name' => $field_label,
                    );
                }
            }
            $skills_array['custom_fields']['list'] = $custom_fields_array;
            if (empty($custom_fields_array)) {
                unset($skills_array['custom_fields']);
            }
        }
        return $skills_array;
    }

}

// class Jobsearch_CustomFieldLoad 
$Jobsearch_CustomFieldLoad_obj = new Jobsearch_CustomFieldLoad();
global $Jobsearch_CustomFieldLoad_obj;