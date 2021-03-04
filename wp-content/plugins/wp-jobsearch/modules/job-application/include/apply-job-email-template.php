<?php
/**
 * File Type: Job Alerts Email Templates
 * For trigger email use following hook
 * 
 * do_action('jobsearch_apply_job_by_email', $apply_detail);
 * 
 */
if (!class_exists('jobsearch_apply_job_by_email_template')) {

    class jobsearch_apply_job_by_email_template {

        public $email_template_type;
        public $codes;
        public $type;
        public $group;
        public $apply_detail;
        public $is_email_sent;
        public $email_template_prefix;
        public $email_template_group;
        public $default_content;
        public $default_subject;
        public $default_recipients;
        public $switch_label;
        public $email_template_db_id;
        public $default_var;
        public $rand;
        public static $is_email_sent1;

        public function __construct() {

            add_action('init', array($this, 'jobsearch_apply_job_by_email_template_init'), 1, 0);
            add_filter('jobsearch_apply_job_by_email_filter', array($this, 'jobsearch_apply_job_by_email_filter_callback'), 1, 4);
            add_filter('jobsearch_email_template_settings', array($this, 'template_settings_callback'), 12, 1);
            add_action('jobsearch_new_apply_job_by_email', array($this, 'jobsearch_apply_job_by_email_callback'), 15, 1);
        }

        public function jobsearch_apply_job_by_email_template_init() {
            $this->apply_detail = array();
            $this->rand = rand(1000, 999999);
            $this->group = 'job';
            $this->type = 'apply_job_by_email';
            $this->filter = 'apply_job_by_email';
            $this->email_template_db_id = 'apply_job_by_email';
            $this->switch_label = esc_html__('Apply job by Email', 'wp-jobsearch');
            $this->default_subject = esc_html__('Apply job by Email', 'wp-jobsearch');
            $this->default_recipients = '';
            $default_content = esc_html__('Default content', 'wp-jobsearch');
            $default_content = apply_filters('jobsearch_apply_job_by_email_filter', $default_content, 'html', 'apply-job-by-email', '');
            $this->default_content = $default_content;
            $this->email_template_prefix = 'apply_job_by_email';
            $this->email_template_group = 'employer';
            $this->codes = apply_filters('jobsearch_jobaply_by_email_temp_codes', array(
                // value_callback replace with function_callback tag replace with var
                array(
                    'var' => '{job_title}',
                    'display_text' => esc_html__('Job Title', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_job_title'),
                ),
                array(
                    'var' => '{job_url}',
                    'display_text' => esc_html__('Job URL', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_job_url'),
                ),
                array(
                    'var' => '{first_name}',
                    'display_text' => esc_html__('First Name', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_username'),
                ),
                array(
                    'var' => '{last_name}',
                    'display_text' => esc_html__('Last Name', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_user_surname'),
                ),
                array(
                    'var' => '{user_email}',
                    'display_text' => esc_html__('User Email', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_user_email'),
                ),
                array(
                    'var' => '{user_phone}',
                    'display_text' => esc_html__('User Phone', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_user_phone'),
                ),
                array(
                    'var' => '{candidate_job_title}',
                    'display_text' => esc_html__('User Job Title', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_cand_job_title'),
                ),
                array(
                    'var' => '{current_salary}',
                    'display_text' => esc_html__('Current Salary', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_current_salary'),
                ),
                array(
                    'var' => '{custom_fields}',
                    'display_text' => esc_html__('Custom Fields', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_custom_fields'),
                ),
                array(
                    'var' => '{user_message}',
                    'display_text' => esc_html__('User Message', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_user_msg'),
                ),
                array(
                    'var' => '{accepts_email_communication}',
                    'display_text' => esc_html__('User accepts email communication', 'wp-jobsearch'),
                    'function_callback' => array($this, 'user_accepts_email_communication'),
                ),
            ), $this);

            $this->default_var = array(
                'switch_label' => $this->switch_label,
                'default_subject' => $this->default_subject,
                'default_recipients' => $this->default_recipients,
                'default_content' => $this->default_content,
                'group' => $this->group,
                'type' => $this->type,
                'filter' => $this->filter,
                'codes' => $this->codes,
            );
        }

        public function jobsearch_apply_job_by_email_callback($apply_detail = array()) {

            global $sitepress;
            $lang_code = '';
            if ( function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') ) {
                $lang_code = $sitepress->get_current_language();
            }
            
            $this->apply_detail = $apply_detail;
            $job_id = isset($this->apply_detail['id']) ? $this->apply_detail['id'] : 0;
            
            $template = $this->get_template();
            // checking email notification is enable/disable
            if (isset($template['switch']) && $template['switch'] == 1) {

                $blogname = get_option('blogname');
                $admin_email = get_option('admin_email');
                $sender_detail_header = '';
                if (isset($template['from']) && $template['from'] != '') {
                    $sender_detail_header = $template['from'];
                    if (isset($template['from_name']) && $template['from_name'] != '') {
                        $sender_detail_header = $template['from_name'] . ' <' . $sender_detail_header . '> ';
                    }
                }

                // getting template fields
                $subject = (isset($template['subject']) && $template['subject'] != '' ) ? $template['subject'] : __('Apply Job by Email', 'wp-jobsearch');
                $subject = JobSearch_plugin::jobsearch_replace_variables($subject, $this->codes);
                
                $from = (isset($sender_detail_header) && $sender_detail_header != '') ? $sender_detail_header : esc_attr($blogname) . ' <' . $admin_email . '>';
                $recipients = (isset($template['recipients']) && $template['recipients'] != '') ? $template['recipients'] : (isset($this->apply_detail['email']) ? $this->apply_detail['email'] : '');
                $email_type = (isset($template['email_type']) && $template['email_type'] != '') ? $template['email_type'] : 'html';

                $email_message = isset($template['email_template']) ? $template['email_template'] : '';
                
                if ( function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') ) {
                    $temp_trnaslated = get_option('jobsearch_translate_email_templates');
                    $template_type = $this->type;
                    if (isset($temp_trnaslated[$template_type]['lang_' . $lang_code]['subject'])) {
                        $subject = $temp_trnaslated[$template_type]['lang_' . $lang_code]['subject'];
                        $subject = JobSearch_plugin::jobsearch_replace_variables($subject, $this->codes);
                    }
                    if (isset($temp_trnaslated[$template_type]['lang_' . $lang_code]['content'])) {
                        $email_message = $temp_trnaslated[$template_type]['lang_' . $lang_code]['content'];
                        $email_message = JobSearch_plugin::jobsearch_replace_variables($email_message, $this->codes);
                    }
                }
                
                $args = array(
                    'to' => $recipients,
                    'subject' => $subject,
                    'from' => $from,
                    'message' => $email_message,
                    'email_type' => $email_type,
                    'class_obj' => $this, // temprary comment
                );
                if (isset($this->apply_detail['user_fullname'])) {
                    $args['from_name'] = $this->apply_detail['user_fullname'];
                }
                if (isset($this->apply_detail['user_email'])) {
                    $args['from_email'] = $this->apply_detail['user_email'];
                }
                
                if (isset($apply_detail['att_file_path'])) {
                    $args['att_file_path'] = $apply_detail['att_file_path'];
                }
                
                do_action('jobsearch_send_mail', $args);
                jobsearch_apply_job_by_email_template::$is_email_sent1 = $this->is_email_sent;
            }
        }

        public static function template_path() {
            return apply_filters('jobsearch_plugin_template_path', 'wp-jobsearch/');
        }

        public function jobsearch_apply_job_by_email_filter_callback($html, $slug = '', $name = '', $ext_template = '') {
            ob_start();
            $html = '';
            $template = '';
            if ($ext_template != '') {
                $ext_template = trailingslashit($ext_template);
            }
            if ($name) {
                $template = locate_template(array("{$slug}-{$name}.php", self::template_path() . "{$ext_template}/{$slug}-{$name}.php"));
            }
            if (!$template && $name && file_exists(jobsearch_plugin_get_path() . "modules/job-application/templates/{$ext_template}/{$slug}-{$name}.php")) {
                $template = jobsearch_plugin_get_path() . "modules/job-application/templates/{$ext_template}{$slug}-{$name}.php";
            }
            if (!$template) {
                $template = locate_template(array("{$slug}.php", self::template_path() . "{$ext_template}/{$slug}.php"));
            }
            //echo $template; exit;
            if ($template) {
                load_template($template, false);
            }
            $html = ob_get_clean();
            return $html;
        }

        public function template_settings_callback($email_template_options) {

            $rand = rand(123, 8787987);
            $email_template_options['apply_job_by_email']['rand'] = $this->rand;
            $email_template_options['apply_job_by_email']['email_template_prefix'] = $this->email_template_prefix;
            $email_template_options['apply_job_by_email']['email_template_group'] = $this->email_template_group;
            $email_template_options['apply_job_by_email']['default_var'] = $this->default_var;
            return $email_template_options;
        }

        public function get_template() {
            return JobSearch_plugin::get_template($this->email_template_db_id, $this->codes, $this->default_content);
        }

        public function get_job_title() {
            if (isset($this->apply_detail['id'])) {
                $job_id = $this->apply_detail['id'];
                return get_the_title($job_id);
            }
        }
        
        public function get_job_url() {
            
            if (isset($this->apply_detail['id'])) {
                $job_id = $this->apply_detail['id'];
                return '<a href="' . get_permalink($job_id) . '">' . get_permalink($job_id) . '</a>';
            }
        }

        public function get_username() {
            if (isset($this->apply_detail['username'])) {
                $username = $this->apply_detail['username'];
                return $username;
            }
        }
        
        public function get_user_surname() {
            
            if (isset($this->apply_detail['user_surname'])) {
                $user_surname = $this->apply_detail['user_surname'];
                
                $user_surname = $user_surname != '' ? $user_surname : '-';
                return $user_surname;
            }
        }

        public function get_user_email() {
            if (isset($this->apply_detail['user_email'])) {
                $user_email = $this->apply_detail['user_email'];
                return $user_email;
            }
        }

        public function get_user_phone() {
            if (isset($this->apply_detail['user_phone'])) {
                $user_phone = $this->apply_detail['user_phone'];
                
                $user_phone = $user_phone != '' ? $user_phone : '-';
                return $user_phone;
            }
        }

        public function get_user_msg() {
            if (isset($this->apply_detail['user_msg'])) {
                $user_msg = $this->apply_detail['user_msg'];
                
                $user_msg = $user_msg != '' ? $user_msg : '-';
                return $user_msg;
            }
        }
        
        public function get_cand_job_title() {
            if (isset($this->apply_detail['_post_vals'])) {
                $post_vals = $this->apply_detail['_post_vals'];
                
                $job_title = isset($post_vals['user_job_title']) && $post_vals['user_job_title'] != '' ? $post_vals['user_job_title'] : '-';
                return $job_title;
            }
        }
        
        public function get_current_salary() {
            if (isset($this->apply_detail['_post_vals'])) {
                $post_vals = $this->apply_detail['_post_vals'];
                
                $user_salary = isset($post_vals['user_salary']) && $post_vals['user_salary'] != '' ? $post_vals['user_salary'] : '-';
                return $user_salary;
            }
        }
        
        public function get_custom_fields() {
            global $sitepress;
            $fields_val = '-';
            if (isset($this->apply_detail['_post_vals'])) {
                $post_vals = $this->apply_detail['_post_vals'];
                
                $custom_all_fields_saved_data = get_option('jobsearch_custom_field_candidate');

                $lang_code = '';
                if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                    $lang_code = $sitepress->get_current_language();
                }
                if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0) {
                    
                    $fields_data = array();
                    foreach ($custom_all_fields_saved_data as $f_key => $custom_field_saved_data) {

                        $field_name = isset($custom_field_saved_data['name']) ? $custom_field_saved_data['name'] : '';
                        $field_label = isset($custom_field_saved_data['label']) ? $custom_field_saved_data['label'] : '';
                        $type = isset($custom_field_saved_data['type']) ? $custom_field_saved_data['type'] : '';
                        
                        if ($field_name != '' && isset($post_vals[$field_name])) {
                            $field_put_val = !empty($post_vals[$field_name]) ? ($post_vals[$field_name]) : '-';
                            if ($type == 'dropdown') {
                                $drop_down_arr = array();
                                $cut_field_flag = 0;
                                foreach ($custom_field_saved_data['options']['value'] as $key => $cus_field_options_value) {

                                    $drop_down_arr[$cus_field_options_value] = (apply_filters('wpml_translate_single_string', $custom_field_saved_data['options']['label'][$cut_field_flag], 'Custom Fields', 'Dropdown Option Label - ' . $custom_field_saved_data['options']['label'][$cut_field_flag], $lang_code));
                                    $cut_field_flag++;
                                }
                                if (is_array($field_put_val) && !empty($field_put_val)) {
                                    $field_put_valarr = array();
                                    foreach ($field_put_val as $fil_putval) {
                                        if (isset($drop_down_arr[$fil_putval]) && $drop_down_arr[$fil_putval] != '') {
                                            $field_put_valarr[] = $drop_down_arr[$fil_putval];
                                        }
                                    }
                                    $field_put_val = implode(', ', $field_put_valarr);
                                } else {
                                    if (isset($drop_down_arr[$field_put_val]) && $drop_down_arr[$field_put_val] != '') {
                                        $field_put_val = $drop_down_arr[$field_put_val];
                                    }
                                }
                            }
                            if ($type == 'checkbox') {
                                $drop_down_arr = array();
                                $cut_field_flag = 0;
                                foreach ($custom_field_saved_data['options']['value'] as $key => $cus_field_options_value) {

                                    $drop_down_arr[$cus_field_options_value] = (apply_filters('wpml_translate_single_string', $custom_field_saved_data['options']['label'][$cut_field_flag], 'Custom Fields', 'Checkbox Option Label - ' . $custom_field_saved_data['options']['label'][$cut_field_flag], $lang_code));
                                    $cut_field_flag++;
                                }
                                if (is_array($field_put_val) && !empty($field_put_val)) {
                                    $field_put_valarr = array();
                                    foreach ($field_put_val as $fil_putval) {
                                        if (isset($drop_down_arr[$fil_putval]) && $drop_down_arr[$fil_putval] != '') {
                                            $field_put_valarr[] = $drop_down_arr[$fil_putval];
                                        }
                                    }
                                    $field_put_val = implode(', ', $field_put_valarr);
                                } else {
                                    if (isset($drop_down_arr[$field_put_val]) && $drop_down_arr[$field_put_val] != '') {
                                        $field_put_val = $drop_down_arr[$field_put_val];
                                    }
                                }
                            }
                            $fields_data[] = array(
                                'label' => $field_label,
                                'value' => $field_put_val,
                            );
                        }
                    }
                    if (!empty($fields_data)) {
                        $fields_val = '<table width="100%"><tbody>';
                        foreach ($fields_data as $field_data) {
                            $field_label = isset($field_data['label']) ? $field_data['label'] : '';
                            $field_put_val = isset($field_data['value']) ? $field_data['value'] : '-';
                            $fields_val .= '<tr><td>' . $field_label . '</td><td>' . $field_put_val . '</td></tr>';
                        }
                        $fields_val .= '</tbody></table>';
                    }
                }
            }
            return $fields_val;
        }
        
        public function user_accepts_email_communication() {
            $txt = esc_html__('No', 'wp-jobsearch');
            if ($this->apply_detail['email_commun_check']) {
                $txt = esc_html__('Yes', 'wp-jobsearch');
            }
            return $txt;
        }

    }

    new jobsearch_apply_job_by_email_template();
}