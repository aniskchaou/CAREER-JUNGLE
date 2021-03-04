<?php

/*
  Class : CustomFieldAjax
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_CustomFieldAjax {

// hook things up
    public function __construct() {
        add_action('wp_ajax_jobsearch_custom_field_html', array($this, 'jobsearch_custom_field_html_callback'));
        add_action('wp_ajax_nopriv_jobsearch_custom_field_html', array($this, 'jobsearch_custom_field_html_callback'));
        // save custom fields
        add_action('wp_ajax_jobsearch_custom_fields_save', array($this, 'jobsearch_custom_fields_save_callback'));
        add_action('wp_ajax_nopriv_jobsearch_custom_fields_save', array($this, 'jobsearch_custom_fields_save_callback'));
        // check availibilty
        add_action('wp_ajax_jobsearch_custom_fields_avalibility', array($this, 'jobsearch_custom_fields_avalibility_callback'));
        add_action('wp_ajax_nopriv_jobsearch_custom_fields_avalibility', array($this, 'jobsearch_custom_fields_avalibility_callback'));
        add_action('save_post', array($this, 'jobsearch_custom_field_save_post_type_dates_callback'), 20, 2);
    }

    static function jobsearch_custom_fields_avalibility_callback() {
        $custom_all_fileds = $_POST['custom_all_fileds'];
        $custom_all_fileds_arr = explode(',', $custom_all_fileds);
        
        if (isset($_POST['this_string']) && $_POST['this_string'] == 'post_ID') {
            $this_string = 'post_ID';
        } else {
            $this_string = sanitize_title($_POST['this_string']);
            $this_string = urldecode($this_string);
        }
        
        if (in_array(trim($this_string), $custom_all_fileds_arr) && count($custom_all_fileds_arr) > 1) {
            $this_string = $this_string . rand(1000000, 9999999);
        }
        $response = array();
        if (preg_match('/\s/', $this_string)) {
            $response['ret_string'] = $this_string;
            $response['type'] = 'error';
            $response['message'] = esc_html__('Whitespaces not allowed', 'wp-jobsearch');
            echo json_encode($response);
        } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬]/', $this_string)) {
            // one or more of the 'special characters' found in $string
            $response['ret_string'] = $this_string;
            $response['type'] = 'error';
            $response['message'] = '<i class="icon-times"></i> ' . esc_html__('Special character not allowed but only (_,-).', 'wp-jobsearch');
            echo json_encode($response);
        } elseif (in_array(trim($this_string), $custom_all_fileds_arr) && count($custom_all_fileds_arr) > 1) {
            $response['ret_string'] = $this_string;
            $response['type'] = 'error';
            $response['message'] = '<i class="icon-times"></i> ' . esc_html__('Name already exist.', 'wp-jobsearch');
            echo json_encode($response);
        } else { // valid case
            $response['ret_string'] = $this_string;
            $response['type'] = 'success';
            $response['message'] = '<i class="dashicons dashicons-yes"></i>';
            echo json_encode($response);
        }
        
        wp_die();
    }

    static function jobsearch_custom_field_html_callback($return = false) {

        $fieldtype = $_POST['fieldtype'];
        $global_custom_field_counter = $_REQUEST['global_custom_field_counter'];
        $li_rand_id = rand(454, 999999);
        $html = '<li class="custom-field-class-' . $li_rand_id . '">';
        if ($fieldtype == 'text') {
            $text_html = '';
            $text_html .= apply_filters('jobsearch_custom_field_text_html', $text_html, $global_custom_field_counter, '');
            $html .= $text_html;
        }
        if ($fieldtype == 'upload_file') {
            $upload_html = '';
            $upload_html .= apply_filters('jobsearch_custom_field_upload_file_html', $upload_html, $global_custom_field_counter, '');
            $html .= $upload_html;
        }
        if ($fieldtype == 'video') {
            $video_html = '';
            $video_html .= apply_filters('jobsearch_custom_field_video_html', $video_html, $global_custom_field_counter, '');
            $html .= $video_html;
        }
        if ($fieldtype == 'linkurl') {
            $linkurl_html = '';
            $linkurl_html .= apply_filters('jobsearch_custom_field_linkurl_html', $video_html, $global_custom_field_counter, '');
            $html .= $linkurl_html;
        }
        if ($fieldtype == 'email') {
            $email_html = '';
            $email_html .= apply_filters('jobsearch_custom_field_email_html', $email_html, $global_custom_field_counter, '');
            $html .= $email_html;
        }
        if ($fieldtype == 'heading') {
            $heading_html = '';
            $heading_html .= apply_filters('jobsearch_custom_field_heading_html', $heading_html, $global_custom_field_counter, '');
            $html .= $heading_html;
        }
        if ($fieldtype == 'checkbox') {
            $checkbox_html = '';
            $checkbox_html .= apply_filters('jobsearch_custom_field_checkbox_html', $checkbox_html, $global_custom_field_counter, '');
            $html .= $checkbox_html;
        }
        if ($fieldtype == 'dropdown') {
            $dropdown_html = '';
            $dropdown_html .= apply_filters('jobsearch_custom_field_dropdown_html', $dropdown_html, $global_custom_field_counter, '');
            $html .= $dropdown_html;
        }
        if ($fieldtype == 'dependent_dropdown') {
            $dependent_dropdown_html = '';
            $dependent_dropdown_html .= apply_filters('jobsearch_custom_field_dependent_dropdown_html', $dependent_dropdown_html, $global_custom_field_counter, '');
            $html .= $dependent_dropdown_html;
        }
        if ($fieldtype == 'dependent_fields') {
            $dependent_fields_html = '';
            $dependent_fields_html .= apply_filters('jobsearch_custom_field_dependent_fields_html', $dependent_fields_html, $global_custom_field_counter, '');
            $html .= $dependent_fields_html;
        }
        if ($fieldtype == 'textarea') {
            $textarea_html = '';
            $textarea_html .= apply_filters('jobsearch_custom_field_textarea_html', $textarea_html, $global_custom_field_counter, '');
            $html .= $textarea_html;
        }
        if ($fieldtype == 'number') {
            $number_html = '';
            $number_html .= apply_filters('jobsearch_custom_field_number_html', $number_html, $global_custom_field_counter, '');
            $html .= $number_html;
        }
        if ($fieldtype == 'salary') {
            $salary_html = '';
            $salary_html .= apply_filters('jobsearch_custom_field_salary_html', $salary_html, $global_custom_field_counter, '');
            $html .= $salary_html;
        }
        if ($fieldtype == 'date') {
            $date_html = '';
            $date_html .= apply_filters('jobsearch_custom_field_date_html', $date_html, $global_custom_field_counter, '');
            $html .= $date_html;
        }
        if ($fieldtype == 'range') {
            $range_html = '';
            $range_html .= apply_filters('jobsearch_custom_field_range_html', $range_html, $global_custom_field_counter, '');
            $html .= $range_html;
        }
        if ($fieldtype == 'company_benefits') {
            $company_benefits_html = '';
            $company_benefits_html .= apply_filters('jobsearch_custom_field_company_benefits_html', $company_benefits_html, $global_custom_field_counter, '');
            $html .= $company_benefits_html;
        }
        // action btns
        $html .= apply_filters('jobsearch_custom_field_actions_html', $li_rand_id, $global_custom_field_counter, $fieldtype);
        $html .= '</li>';
        echo json_encode(array('html' => $html));
        wp_die();
    }

    public function jobsearch_custom_fields_save_callback() {
        $post_data = $_POST;
        $post_data = empty($post_data) ? array() : $post_data;
        $heading_counter = $textarea_counter = $dropdown_counter = $checkbox_counter = $date_counter = $email_counter = $upload_counter = $text_counter = $linkurl_counter = $video_counter = $number_counter = $dependent_dropdown_counter = $dependent_fields_counter = $range_counter = $salary_counter = $company_benefits = $error = 0;
        $custom_field_entity = $post_data['entitytype'];
        $error_msg = '';
        $custom_field_final_array = array();
        
        if (isset($_POST['jobsearch-custom-fields-type']) && sizeof($_POST['jobsearch-custom-fields-type']) > 0) {
            $field_index = 0;
            foreach ($post_data['jobsearch-custom-fields-type'] as $custom_fields_type) {
                $custom_fields_id = isset($_POST['jobsearch-custom-fields-id'][$field_index]) ? $post_data['jobsearch-custom-fields-id'][$field_index] : '';
                $rand_numb = rand(1342121, 9974532);
                $custom_field_array = array();
                switch ($custom_fields_type) {
                    case('text'):
                        $custom_field_array = $this->jobsearch_custom_field_ready_array($text_counter, $custom_fields_type, $custom_field_array);
                        $text_counter ++;
                        break;
                    case('video'):
                        $custom_field_array = $this->jobsearch_custom_field_ready_array($video_counter, $custom_fields_type, $custom_field_array);
                        $video_counter ++;
                        break;
                    case('linkurl'):
                        $custom_field_array = $this->jobsearch_custom_field_ready_array($linkurl_counter, $custom_fields_type, $custom_field_array);
                        $linkurl_counter ++;
                        break;
                    case('upload_file'):
                        $custom_field_array = $this->jobsearch_custom_field_ready_array($upload_counter, $custom_fields_type, $custom_field_array);
                        
                        if (isset($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['allow_types'][$custom_fields_id]) && (strlen(implode($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['allow_types'][$custom_fields_id])) != 0)) {
                            $custom_field_array['allow_types'] = array();
                            $option_counter = 0;
                            foreach ($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['allow_types'][$custom_fields_id] as $option) {
                                if ($option != '') {
                                    $custom_field_array['allow_types'][] = $option;
                                }
                                $option_counter ++;
                            }
                        }
                        
                        $upload_counter ++;
                        break;
                    case('heading'):
                        $custom_field_array = $this->jobsearch_custom_field_ready_array($heading_counter, $custom_fields_type, $custom_field_array);
                        $heading_counter ++;
                        break;
                    case('textarea'):
                        $custom_field_array = $this->jobsearch_custom_field_ready_array($textarea_counter, $custom_fields_type, $custom_field_array);
                        $textarea_counter ++;
                        break;
                    case('date'):
                        $custom_field_array = $this->jobsearch_custom_field_ready_array($date_counter, $custom_fields_type, $custom_field_array);
                        $date_counter ++;
                        break;
                    case('email'):
                        $custom_field_array = $this->jobsearch_custom_field_ready_array($email_counter, $custom_fields_type, $custom_field_array);
                        $email_counter ++;
                        break;
                    case('number'):
                        $custom_field_array = $this->jobsearch_custom_field_ready_array($number_counter, $custom_fields_type, $custom_field_array);
                        $number_counter ++;
                        break;
                    case('checkbox'):

                        $custom_field_array = $this->jobsearch_custom_field_ready_array($checkbox_counter, $custom_fields_type, $custom_field_array);
                        if (isset($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['value'][$custom_fields_id]) && (strlen(implode($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['value'][$custom_fields_id])) != 0)) {
                            $custom_field_array['options'] = array();
                            $option_counter = 0;
                            foreach ($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['value'][$custom_fields_id] as $option) {
                                if ($option != '') {
                                    $option = ltrim(rtrim($option));
                                    if ($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['label'][$custom_fields_id][$option_counter] != '') {
                                        $custom_field_array['options']['label'][] = isset($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['label'][$custom_fields_id][$option_counter]) ? $_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['label'][$custom_fields_id][$option_counter] : '';
                                        //$custom_field_array['options']['value'][] = isset($option) && $option != '' ? strtolower(str_replace(" ", "-", $option)) : '';
                                        $custom_field_array['options']['value'][] = isset($option) && $option != '' ? $option : '';
                                    }
                                }
                                $option_counter ++;
                            }
                        } else {
                            $error = 1;
                            $error_msg .= esc_html__('Please select at least one option for the field.', 'wp-jobsearch') . "'<br/>";
                        }

                        $checkbox_counter ++;
                        break;
                    case('dropdown'):

                        $custom_field_array = $this->jobsearch_custom_field_ready_array($dropdown_counter, $custom_fields_type, $custom_field_array);
                        if (isset($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['value'][$custom_fields_id]) && (strlen(implode($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['value'][$custom_fields_id])) != 0)) {
                            $custom_field_array['options'] = array();
                            $option_counter = 0;
                            foreach ($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['value'][$custom_fields_id] as $option) {
                                if ($option != '') {
                                    $option = ltrim(rtrim($option));
                                    $option = str_replace(array('<', '>', '"', '\''), array('', '', '', ''), $option);
                                    if ($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['label'][$custom_fields_id][$option_counter] != '') {
                                        $custom_field_array['options']['label'][] = isset($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['label'][$custom_fields_id][$option_counter]) ? $_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['label'][$custom_fields_id][$option_counter] : '';
                                        //$custom_field_array['options']['value'][] = isset($option) && $option != '' ? strtolower(str_replace(" ", "-", $option)) : '';
                                        $custom_field_array['options']['value'][] = isset($option) && $option != '' ? str_replace(',', '',$option) : '';
                                    }
                                }
                                $option_counter ++;
                            }

                        } else {
                            $error = 1;
                            $error_msg .= esc_html__('Please select at least one option for the field.', 'wp-jobsearch') . "'<br/>";
                        }

                        $dropdown_counter ++;
                        break;
                    case('dependent_dropdown'):
                        $custom_field_array = $this->jobsearch_custom_field_ready_array($dependent_dropdown_counter, $custom_fields_type, $custom_field_array);
                        $dropdown_cont_optsid = $custom_fields_id;
                        if (isset($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options_field_counter_id'][$dependent_dropdown_counter])) {
                            $dropdown_cont_optsid = $_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options_field_counter_id'][$dependent_dropdown_counter];
                            $custom_field_array['options_list_id'] = $dropdown_cont_optsid;
                        }
                        if (isset($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options'][$dropdown_cont_optsid][0]['id']) && !empty($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options'][$dropdown_cont_optsid][0]['id'])) {
                            $custom_field_array['options_list'] = array();
                            $custom_field_array['options_list'] = $_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options'][$dropdown_cont_optsid];
                        } else {
                            $error = 1;
                            $error_msg .= esc_html__('Please select at least one option for the field.', 'wp-jobsearch') . "'<br/>";
                        }
                        
                        $dependent_dropdown_counter ++;
                        break;
                    case('dependent_fields'):
                        $custom_field_array = $this->jobsearch_custom_field_ready_array($dependent_fields_counter, $custom_fields_type, $custom_field_array);
                        //var_dump($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options'][$custom_fields_id]);
                        if (isset($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options'][$custom_fields_id]) && count($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options'][$custom_fields_id]) > 0) {
                            $custom_field_array['options'] = array();
                            $option_counter = 0;
                            foreach ($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options'][$custom_fields_id] as $opt_rkey => $option) {
                                $custom_field_array['options'][$opt_rkey] = $option;
                                $option_counter ++;
                            }

                        } else {
                            $error = 1;
                            $error_msg .= esc_html__('Please select at least one option for the field.', 'wp-jobsearch') . "'<br/>";
                        }
                        
                        $dependent_fields_counter ++;
                        break;
                    case('range'):
                        $custom_field_array = $this->jobsearch_custom_field_ready_array($range_counter, $custom_fields_type, $custom_field_array);
                        $range_counter ++;
                        break;
                    case('salary'):
                        $custom_field_array = $this->jobsearch_custom_field_ready_array($salary_counter, $custom_fields_type, $custom_field_array);
                        $salary_counter ++;
                        break;
                    case('company_benefits'):
                        $custom_field_array = $this->jobsearch_custom_field_ready_array($company_benefits, $custom_fields_type, $custom_field_array);
                        if (isset($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['value'][$custom_fields_id]) && (strlen(implode($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['value'][$custom_fields_id])) != 0)) {
                            $custom_field_array['options'] = array();
                            $option_counter = 0;
                            foreach ($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['value'][$custom_fields_id] as $option) {
                                if ($option != '') {
                                    $option = ltrim(rtrim($option));
                                    $option = str_replace(array('<', '>', '"', '\''), array('', '', '', ''), $option);
                                    if ($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['label'][$custom_fields_id][$option_counter] != '') {
                                        $custom_field_array['options']['icon'][] = isset($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['icon'][$custom_fields_id][$option_counter]) ? $_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['icon'][$custom_fields_id][$option_counter] : '';
                                        $custom_field_array['options']['icon_group'][] = isset($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['icon_group'][$custom_fields_id][$option_counter]) ? $_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['icon_group'][$custom_fields_id][$option_counter] : '';
                                        $custom_field_array['options']['label'][] = isset($_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['label'][$custom_fields_id][$option_counter]) ? $_POST["jobsearch-custom-fields-{$custom_fields_type}"]['options']['label'][$custom_fields_id][$option_counter] : '';
                                        $custom_field_array['options']['value'][] = isset($option) && $option != '' ? $option : '';
                                    }
                                }
                                $option_counter ++;
                            }
                        }
                        $company_benefits ++;
                        break;
                }
                $custom_field_final_array[$rand_numb] = $custom_field_array;
                $field_index++; // field index increament
            }
        }


        if ($error == 0) {
            update_option("jobsearch_custom_field_" . $custom_field_entity, $custom_field_final_array);
            $error = 0;
            $error_msg = esc_html__('Custom fields have been saved successfully', 'wp-jobsearch');
        }
        echo json_encode(array('msg' => $error_msg, 'error' => $error));
        wp_die();
    }

    public function jobsearch_custom_field_ready_array($array_counter = 0, $field_type = '', $custom_field_element_array = array()) {
        $possible_element_fields = apply_filters('jobsearch_custom_fields_ready_array', array('non_reg_user', 'media_buttons', 'rich_editor', 'multi_files', 'field_type', 'numof_files', 'allow_types', 'file_size', 'link_target', 'public_visible', 'name', 'required', 'label', 'placeholder', 'classes', 'enable-search', 'enable-advsrch', 'icon', 'icon-group', 'multi', 'post-multi', 'collapse-search', 'date-format', 'min', 'laps', 'interval', 'min1', 'laps1', 'interval1', 'min2', 'laps2', 'interval2', 'min3', 'laps3', 'interval3', 'min4', 'laps4', 'interval4', 'min5', 'laps5', 'interval5', 'field-style', 'options'));
        $custom_field_element_array['type'] = $field_type;
        foreach ($possible_element_fields as $field) {
            if (isset($_POST["jobsearch-custom-fields-{$field_type}"][$field][$array_counter])) {
                $field_label = isset($_POST["jobsearch-custom-fields-{$field_type}"]['label'][$array_counter]) && $_POST["jobsearch-custom-fields-{$field_type}"]['label'][$array_counter] != '' ? $_POST["jobsearch-custom-fields-{$field_type}"]['label'][$array_counter] : 'Field Name';
                if ($field == 'name') {
                    $savname_field = $_POST["jobsearch-custom-fields-{$field_type}"][$field][$array_counter];
                    if ($savname_field == '') {
                        $savname_field = $field_label . ' ' . rand(1000000, 9999999);
                    }
                    if ($savname_field == 'post_ID') {
                        $savname_field = 'post_ID';
                    } else {
                        $savname_field = sanitize_title($savname_field);
                        $savname_field = urldecode($savname_field);
                    }
                    $custom_field_element_array[$field] = $savname_field;
                } else {
                    $custom_field_element_array[$field] = $_POST["jobsearch-custom-fields-{$field_type}"][$field][$array_counter];
                }
            }
        }
        return $custom_field_element_array;
    }

    public function jobsearch_custom_field_save_post_type_dates_callback( $post_id, $post) {
        if ($post_id != '') {
            $custom_field_entity = $post->post_type;
            $jobsearch_post_type_cus_fields = get_option("jobsearch_custom_field_" . $custom_field_entity);
            
            if (is_array($jobsearch_post_type_cus_fields) && isset($jobsearch_post_type_cus_fields) && !empty($jobsearch_post_type_cus_fields)) {

                foreach ($jobsearch_post_type_cus_fields as $jobsearch_cus_field) {
                    $field_type = isset($jobsearch_cus_field['type']) ? $jobsearch_cus_field['type'] : '';
                    $name = isset($jobsearch_cus_field['name']) ? $jobsearch_cus_field['name'] : '';
                    if ($field_type == 'date') {
                        if ($name != '') {
                            $cus_field_values = '';
                            $cus_field_values = isset($_POST[$name]) ? $_POST[$name] : '';
                            if ($cus_field_values) {
                                update_post_meta($post_id, $name, strtotime($cus_field_values));
                            }
                        }
                    }
                }
            }
        }
    }

}

// class Jobsearch_CustomFieldAjax 
$Jobsearch_CustomFieldAjax_obj = new Jobsearch_CustomFieldAjax();
global $Jobsearch_CustomFieldAjax_obj;
