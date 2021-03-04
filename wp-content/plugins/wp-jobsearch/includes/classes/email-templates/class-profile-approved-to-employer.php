<?php

/**
 * File Type: Employer Profile Approval Email Templates
 * 
 * do_action('jobsearch_profile_approval_to_employer', $user_data, $post_id);
 * 
 */
if (!class_exists('jobsearch_profile_approval_to_employer_template')) {

    class jobsearch_profile_approval_to_employer_template {

        public $email_template_type;
        public $codes;
        public $type;
        public $group;
        public $user;
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

            add_action('init', array($this, 'jobsearch_profile_approval_to_employer_template_init'), 1, 0);
            add_filter('jobsearch_profile_approval_to_employer_filter', array($this, 'jobsearch_profile_approval_to_employer_filter_callback'), 1, 4);
            add_filter('jobsearch_email_template_settings', array($this, 'template_settings_callback'), 12, 1);
            add_action('jobsearch_profile_approval_to_employer', array($this, 'jobsearch_profile_approval_to_employer_callback'), 10, 1);
        }

        public function jobsearch_profile_approval_to_employer_template_init() {
            $this->user = array();
            $this->rand = rand(0, 99999);
            $this->group = 'employer';
            $this->type = 'profile_approval_to_employer';
            $this->filter = 'profile_approval_to_employer';
            $this->email_template_db_id = 'profile_approval_to_employer';
            $this->switch_label = esc_html__('Profile Approval to employer', 'wp-jobsearch');
            $this->default_subject = esc_html__('Your Profile is Approved by Admin', 'wp-jobsearch');
            $this->default_recipients = '';
            $default_content = esc_html__('Default content', 'wp-jobsearch');
            $default_content = apply_filters('jobsearch_profile_approval_to_employer_filter', $default_content, 'html', 'profile-approval-to-employer', 'email-templates');
            $this->default_content = $default_content;
            $this->email_template_prefix = 'profile_approval_to_employer';
            $this->email_template_group = 'employer';
            $this->codes = apply_filters('jobsearch_profile_aproval_toemp_codes', array(
                // value_callback replace with function_callback tag replace with var
                array(
                    'var' => '{first_name}',
                    'display_text' => esc_html__('First Name', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_emp_first_name'),
                ),
                array(
                    'var' => '{last_name}',
                    'display_text' => esc_html__('Last Name', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_emp_last_name'),
                ),
                array(
                    'var' => '{employer_name}',
                    'display_text' => esc_html__('Employer name', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_employer_name'),
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

        public function jobsearch_profile_approval_to_employer_callback($user = '') {

            global $sitepress, $jobsearch_plugin_options;
            $lang_code = '';
            if ( function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') ) {
                $lang_code = $sitepress->get_current_language();
            }
            
            $this->user = $user;
            $template = $this->get_template();
            // checking email notification is enable/disable
            if (isset($template['switch']) && $template['switch'] == 1) {

                $blogname = get_option('blogname');
                $admin_email = get_option('admin_email');

                // getting template fields
                $subject = (isset($template['subject']) && $template['subject'] != '' ) ? $template['subject'] : __('Profile Approved', 'wp-jobsearch');
                $subject = JobSearch_plugin::jobsearch_replace_variables($subject, $this->codes);
                
                $from = esc_attr($blogname) . ' <' . $admin_email . '>';
                $recipients = (isset($template['recipients']) && $template['recipients'] != '') ? $template['recipients'] : $this->user->user_email;
                
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
                    'from_name' => $blogname,
                    'from_email' => $admin_email,
                    'message' => $email_message,
                    'email_type' => $email_type,
                    'class_obj' => $this, // temprary comment
                );
                
                do_action('jobsearch_send_mail', $args);
                jobsearch_profile_approval_to_employer_template::$is_email_sent1 = $this->is_email_sent;
            }
        }

        public static function template_path() {
            return apply_filters('jobsearch_plugin_template_path', 'wp-jobsearch/');
        }

        public function jobsearch_profile_approval_to_employer_filter_callback($html, $slug = '', $name = '', $ext_template = '') {
            ob_start();
            $html = '';
            $template = '';
            if ($ext_template != '') {
                $ext_template = trailingslashit($ext_template);
            }
            if ($name) {
                $template = locate_template(array("{$slug}-{$name}.php", self::template_path() . "templates/{$ext_template}/{$slug}-{$name}.php"));
            }
            if (!$template && $name && file_exists(jobsearch_plugin_get_path() . "templates/{$ext_template}/{$slug}-{$name}.php")) {
                $template = jobsearch_plugin_get_path() . "templates/{$ext_template}{$slug}-{$name}.php";
            }
            if (!$template) {
                $template = locate_template(array("{$slug}.php", self::template_path() . "{$ext_template}/{$slug}.php"));
            }
            //echo $template;exit;
            if ($template) {
                load_template($template, false);
            }
            $html = ob_get_clean();
            return $html;
        }

        public function template_settings_callback($email_template_options) {

            $rand = rand(123, 8787987);
            $email_template_options['profile_approval_to_employer']['rand'] = $this->rand;
            $email_template_options['profile_approval_to_employer']['email_template_prefix'] = $this->email_template_prefix;
            $email_template_options['profile_approval_to_employer']['email_template_group'] = $this->email_template_group;
            $email_template_options['profile_approval_to_employer']['default_var'] = $this->default_var;
            return $email_template_options;
        }

        public function get_template() {
            return JobSearch_plugin::get_template($this->email_template_db_id, $this->codes, $this->default_content);
        }

        public function get_employer_name() {

            $user_name = $this->user->display_name;
            $user_obj = $this->user;
            $user_name = apply_filters('jobsearch_user_display_name', $user_name, $user_obj);
            return $user_name;
        }
        
        public function get_emp_first_name() {
            $first_name = $this->user->first_name;
            
            return ($first_name != '' ? $first_name : '-');
        }
        
        public function get_emp_last_name() {
            $last_name = $this->user->last_name;
            
            return ($last_name != '' ? $last_name : '-');
        }

    }

    new jobsearch_profile_approval_to_employer_template();
}