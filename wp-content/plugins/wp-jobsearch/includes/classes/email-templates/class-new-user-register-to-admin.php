<?php

/**
 * File Type: Email Templates
 * For trigger email use following hook
 * 
 * $user_data = wp_get_current_user();
 */
if (!class_exists('jobsearch_new_user_reg_toadmin_template')) {

    class jobsearch_new_user_reg_toadmin_template {

        public $email_template_type;
        public $codes;
        public $type;
        public $group;
        public $user;
        public $user_password;
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

            add_action('init', array($this, 'jobsearch_new_user_reg_toadmin_template_init'), 1, 0);
            add_filter('jobsearch_new_user_reg_toadmin_filter', array($this, 'jobsearch_new_user_reg_toadmin_filter_callback'), 1, 4);
            add_filter('jobsearch_email_template_settings', array($this, 'template_settings_callback'), 12, 1);
            add_action('jobsearch_new_user_reg_toadmin', array($this, 'jobsearch_new_user_reg_toadmin_callback'), 10, 2);
        }

        public function jobsearch_new_user_reg_toadmin_template_init() {
            $this->user = array();
            $this->user_password = '';
            $this->rand = rand(0, 99999);
            $this->group = 'job';
            $this->type = 'new_user_reg_toadmin';
            $this->filter = 'new_user_reg_toadmin';
            $this->email_template_db_id = 'new_user_reg_toadmin';
            $this->switch_label = esc_html__('New User Register to Admin', 'wp-jobsearch');
            $this->default_subject = esc_html__('New User Register', 'wp-jobsearch');
            $this->default_recipients = '';
            $default_content = esc_html__('Default content', 'wp-jobsearch');
            $default_content = apply_filters('jobsearch_new_user_reg_toadmin_filter', $default_content, 'html', 'new-user-reg-toadmin', 'email-templates');
            $this->default_content = $default_content;
            $this->email_template_prefix = 'new_user_reg_toadmin';
            $this->email_template_group = 'admin';
            $this->codes = array(
                // value_callback replace with function_callback tag replace with var
                array(
                    'var' => '{username}',
                    'display_text' => esc_html__('Username', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_username'),
                ),
                array(
                    'var' => '{first_name}',
                    'display_text' => esc_html__('First Name', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_user_fname'),
                ),
                array(
                    'var' => '{last_name}',
                    'display_text' => esc_html__('Last Name', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_user_lname'),
                ),
                array(
                    'var' => '{password}',
                    'display_text' => esc_html__('Password', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_user_password'),
                ),
                array(
                    'var' => '{user_email}',
                    'display_text' => esc_html__('User Email', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_user_email'),
                ),
                array(
                    'var' => '{user_type}',
                    'display_text' => esc_html__('User Type', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_user_type'),
                ),
                array(
                    'var' => '{user_profile_url}',
                    'display_text' => esc_html__('Profile Link', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_user_profile_link'),
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

        public function jobsearch_new_user_reg_toadmin_callback($user = '', $password = '') {
            global $sitepress;
            $lang_code = '';
            if ( function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') ) {
                $lang_code = $sitepress->get_current_language();
            }

            $this->user = $user;
            $this->user_password = $password;
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
                $subject = (isset($template['subject']) && $template['subject'] != '' ) ? $template['subject'] : __('New User Register', 'wp-jobsearch');
                $subject = JobSearch_plugin::jobsearch_replace_variables($subject, $this->codes);
                
                $from = (isset($sender_detail_header) && $sender_detail_header != '') ? $sender_detail_header : esc_attr($blogname) . ' <' . $admin_email . '>';
                $recipients = (isset($template['recipients']) && $template['recipients'] != '') ? $template['recipients'] : $admin_email;
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

                $tfrom_name = $user->display_name;
                $tfrom_name = apply_filters('jobsearch_user_display_name', $tfrom_name, $user);
                
                $tfrom_email = $user->user_email;
                
                $args = array(
                    'to' => $recipients,
                    'subject' => $subject,
                    'from' => $from,
                    'from_email' => $tfrom_email,
                    'from_name' => $tfrom_name,
                    'message' => $email_message,
                    'email_type' => $email_type,
                    'class_obj' => $this, // temprary comment
                );
                do_action('jobsearch_send_mail', $args);
                jobsearch_new_user_reg_toadmin_template::$is_email_sent1 = $this->is_email_sent;
            }
        }

        public static function template_path() {
            return apply_filters('jobsearch_plugin_template_path', 'wp-jobsearch/');
        }

        public function jobsearch_new_user_reg_toadmin_filter_callback($html, $slug = '', $name = '', $ext_template = '') {
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
            
            if ($template) {
                load_template($template, false);
            }
            $html = ob_get_clean();
            return $html;
        }

        public function template_settings_callback($email_template_options) {

            $rand = rand(123, 8787987);
            $email_template_options['new_user_reg_toadmin']['rand'] = $this->rand;
            $email_template_options['new_user_reg_toadmin']['email_template_prefix'] = $this->email_template_prefix;
            $email_template_options['new_user_reg_toadmin']['email_template_group'] = $this->email_template_group;
            $email_template_options['new_user_reg_toadmin']['default_var'] = $this->default_var;
            return $email_template_options;
        }

        public function get_template() {
            return JobSearch_plugin::get_template($this->email_template_db_id, $this->codes, $this->default_content);
        }

        public function get_user_password() {

            $user_password = $this->user_password;
            return $user_password;
        }

        public function get_user_type() {

            $user_id = $this->user->ID;
            $user_is_candidate = jobsearch_user_is_candidate($user_id);
            $user_is_employer = jobsearch_user_is_employer($user_id);
            
            $user_type = '-';
            if ($user_is_candidate) {
                $user_type = esc_html__('Candidate', 'wp-jobsearch');
            } else if ($user_is_employer) {
                $user_type = esc_html__('Employer', 'wp-jobsearch');
            }
            return $user_type;
        }

        public function get_user_profile_link() {

            $user_id = $this->user->ID;
            $user_is_candidate = jobsearch_user_is_candidate($user_id);
            $user_is_employer = jobsearch_user_is_employer($user_id);
            
            $user_link = '-';
            if ($user_is_candidate) {
                $candidate_id = jobsearch_get_user_candidate_id($user_id);
                $user_link = '<a href="' . get_permalink($candidate_id) . '">' . get_permalink($candidate_id) . '</a>';
            } else if ($user_is_employer) {
                $employer_id = jobsearch_get_user_employer_id($user_id);
                $user_link = '<a href="' . get_permalink($employer_id) . '">' . get_permalink($employer_id) . '</a>';
            }
            return $user_link;
        }

        public function get_username() {

            $user_name = $this->user->user_login;
            return $user_name;
        }

        public function get_user_fname() {

            $user_fname = $this->user->first_name;
            $user_fname = $user_fname != '' ? $user_fname : '-';
            return $user_fname;
        }

        public function get_user_lname() {

            $user_lname = $this->user->last_name;
            $user_lname = $user_lname != '' ? $user_lname : '-';
            return $user_lname;
        }

        public function get_user_email() {

            $email = $this->user->user_email;
            return $email;
        }

    }

    new jobsearch_new_user_reg_toadmin_template();
}