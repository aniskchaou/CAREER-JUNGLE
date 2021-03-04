<?php

if (!class_exists('jobsearch_instamatch_by_emp_template')) {

    class jobsearch_instamatch_by_emp_template {

        public $email_template_type;
        public $codes;
        public $type;
        public $group;
        public $user;
        public $job_id;
        public $job_employer;
        public $employer_subj;
        public $employer_msg;
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

            add_action('init', array($this, 'jobsearch_instamatch_by_emp_template_init'), 1, 0);
            add_filter('jobsearch_instamatch_by_emp_filter', array($this, 'jobsearch_instamatch_by_emp_filter_callback'), 1, 4);
            add_filter('jobsearch_email_template_settings', array($this, 'template_settings_callback'), 12, 1);
            add_action('jobsearch_instamatch_by_emp_email', array($this, 'jobsearch_instamatch_by_emp_callback'), 10, 4);
        }

        public function jobsearch_instamatch_by_emp_template_init() {
            $this->user = array();
            $this->job_id = '';
            $this->job_employer = '';
            $this->employer_subj = '';
            $this->rand = rand(0, 99999);
            $this->group = 'employer';
            $this->type = 'instamatch_by_emp';
            $this->filter = 'instamatch_by_emp';
            $this->email_template_db_id = 'instamatch_by_emp';
            $this->switch_label = esc_html__('To Instamatch Candidates by Employer', 'wp-jobsearch');
            $this->default_subject = esc_html__('Profile Matched with job posted by employer', 'wp-jobsearch');
            $this->default_recipients = '';
            $default_content = esc_html__('Default content', 'wp-jobsearch');
            $default_content = apply_filters('jobsearch_instamatch_by_emp_filter', $default_content, 'html', 'instamatch-by-emp', 'email-templates');
            $this->default_content = $default_content;
            $this->email_template_prefix = 'instamatch_by_emp';
            $this->email_template_group = 'employer';
            $this->codes = array(
                // value_callback replace with function_callback tag replace with var
                array(
                    'var' => '{candidate_name}',
                    'display_text' => esc_html__('Candidate Name', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_candidate_name'),
                ),
                array(
                    'var' => '{company_name}',
                    'display_text' => esc_html__('Company Name', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_company_name'),
                ),
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
                    'var' => '{employer_message}',
                    'display_text' => esc_html__('Employer Message', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_employer_message'),
                ),
            );

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

        public function jobsearch_instamatch_by_emp_callback($user = '', $job_id = '', $employer_subj = '', $employer_msg = '') {
            global $sitepress;
            $lang_code = '';
            if ( function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') ) {
                $lang_code = $sitepress->get_current_language();
            }

            $this->user = $user;
            $this->job_id = $job_id;
            
            $job_employer = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
            $this->job_employer = $job_employer;
            
            $employer_user_id = get_post_meta($job_employer, 'jobsearch_user_id', true);
            $employer_user_obj = get_user_by('ID', $employer_user_id);
            
            $employer_msg = JobSearch_plugin::jobsearch_replace_variables($employer_msg, $this->codes);
            $this->employer_msg = $employer_msg;
            
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
                
                $this->employer_subj = $employer_subj;

                // getting template fields
                $subject = (isset($template['subject']) && $template['subject'] != '' ) ? $template['subject'] : __('Your Profile Matched with job posted by employer', 'wp-jobsearch');
                if ($employer_subj != '') {
                    $subject = $employer_subj;
                }
                $subject = JobSearch_plugin::jobsearch_replace_variables($subject, $this->codes);
                
                $from = (isset($sender_detail_header) && $sender_detail_header != '') ? $sender_detail_header : esc_attr($blogname) . ' <' . $admin_email . '>';
                $recipients = (isset($template['recipients']) && $template['recipients'] != '') ? $template['recipients'] : $user->user_email;
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

                $from_name = get_the_title($job_employer);
                $from_email = isset($employer_user_obj->user_email) ? $employer_user_obj->user_email : '';
                $args = array(
                    'to' => $recipients,
                    'subject' => $subject,
                    'from' => $from,
                    'from_name' => $from_name,
                    'from_email' => $from_email,
                    'message' => $email_message,
                    'email_type' => $email_type,
                    'class_obj' => $this, // temprary comment
                );
                do_action('jobsearch_send_mail', $args);
                jobsearch_instamatch_by_emp_template::$is_email_sent1 = $this->is_email_sent;
            }
        }

        public static function template_path() {
            return apply_filters('jobsearch_plugin_template_path', 'wp-jobsearch/');
        }

        public function jobsearch_instamatch_by_emp_filter_callback($html, $slug = '', $name = '', $ext_template = '') {
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
            $email_template_options['instamatch_by_emp']['rand'] = $this->rand;
            $email_template_options['instamatch_by_emp']['email_template_prefix'] = $this->email_template_prefix;
            $email_template_options['instamatch_by_emp']['email_template_group'] = $this->email_template_group;
            $email_template_options['instamatch_by_emp']['default_var'] = $this->default_var;
            return $email_template_options;
        }

        public function get_template() {
            return JobSearch_plugin::get_template($this->email_template_db_id, $this->codes, $this->default_content);
        }

        public function get_candidate_name() {

            $user_id = $this->user->ID;
            $user_obj = get_user_by('ID', $user_id);
            $user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
            $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);
            $user_displayname = $user_displayname != '' ? $user_displayname : '-';
            
            return $user_displayname;
        }

        public function get_company_name() {

            $employer_id = $this->job_employer;
            $employer_name = get_the_title($employer_id);
            
            return $employer_name;
        }

        public function get_job_title() {

            $job_id = $this->job_id;
            $job_name = get_the_title($job_id);
            
            return $job_name;
        }

        public function get_job_url() {

            $job_id = $this->job_id;
            $job_url = get_permalink($job_id);
            
            return $job_url;
        }
        
        public function get_employer_message() {
            $msg = $this->employer_msg;
            $msg = $msg != '' ? $msg : '-';
            
            return $msg;
        }

    }

    new jobsearch_instamatch_by_emp_template();
}