<?php

/**
 * File Type: Job Change Status Email Templates
 * Description: If job will change with updated then trigger to employer
 * For trigger email use following hook
 * 
 * $user_data = wp_get_current_user();
 * do_action('jobsearch_job_expire_to_employer', $user_data, $post_id);
 * 
 */
if (!class_exists('jobsearch_job_expire_to_employer_template')) {

    class jobsearch_job_expire_to_employer_template {

        public $email_template_type;
        public $codes;
        public $type;
        public $group;
        public $user;
        public $job_id;
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

            add_action('init', array($this, 'jobsearch_job_expire_to_employer_template_init'), 1, 0);
            add_filter('jobsearch_job_expire_to_employer_filter', array($this, 'jobsearch_job_expire_to_employer_filter_callback'), 1, 4);
            add_filter('jobsearch_email_template_settings', array($this, 'template_settings_callback'), 12, 1);
            add_action('jobsearch_job_expire_to_employer', array($this, 'jobsearch_job_expire_to_employer_callback'), 10, 2);
        }

        public function jobsearch_job_expire_to_employer_template_init() {
            $this->user = array();
            $this->rand = rand(0, 99999);
            $this->group = 'job';
            $this->type = 'job_expire_to_employer';
            $this->filter = 'job_expire_to_employer';
            $this->email_template_db_id = 'job_expire_to_employer';
            $this->switch_label = esc_html__('Job Expire to Employer', 'wp-jobsearch');
            $this->default_subject = esc_html__('Job Expire to Employer', 'wp-jobsearch');
            $this->default_recipients = '';
            $default_content = esc_html__('Default content', 'wp-jobsearch');
            $default_content = apply_filters('jobsearch_job_expire_to_employer_filter', $default_content, 'html', 'job-expire-to-employer', 'email-templates');
            $this->default_content = $default_content;
            $this->email_template_prefix = 'job_expire_to_employer';
            $this->email_template_group = 'employer';
            $this->codes = array(
                // value_callback replace with function_callback tag replace with var
                array(
                    'var' => '{job_title}',
                    'display_text' => esc_html__('job title', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_job_added_jobtitle'),
                ),
                array(
                    'var' => '{job_url}',
                    'display_text' => esc_html__('job URL', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_job_added_joburl'),
                ),
                array(
                    'var' => '{job_jobtype}',
                    'display_text' => esc_html__('job type', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_job_added_jobtype'),
                ),
                array(
                    'var' => '{job_sector}',
                    'display_text' => esc_html__('sector', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_job_added_sector'),
                ),
                array(
                    'var' => '{job_publish_date}',
                    'display_text' => esc_html__('publish date', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_job_added_publish_date'),
                ),
                array(
                    'var' => '{job_expiry_date}',
                    'display_text' => esc_html__('expiry date', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_job_added_expiry_date'),
                ),
                array(
                    'var' => '{job_featured}',
                    'display_text' => esc_html__('featured', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_job_added_featured'),
                ),
                array(
                    'var' => '{job_posted_by}',
                    'display_text' => esc_html__('posted by', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_job_added_posted_by'),
                ),
                array(
                    'var' => '{job_status}',
                    'display_text' => esc_html__('status', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_job_added_status'),
                ),
                array(
                    'var' => '{job_posted_by_logo}',
                    'display_text' => esc_html__('posted by logo', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_job_added_employer_logo'),
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

        public function jobsearch_job_expire_to_employer_callback($user = '', $job_id = '') {
            global $sitepress;
            $lang_code = '';
            if ( function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') ) {
                $lang_code = $sitepress->get_current_language();
            }

            $this->user = $user;
            $this->job_id = $job_id;
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
                $subject = (isset($template['subject']) && $template['subject'] != '' ) ? $template['subject'] : __('Job Updated', 'wp-jobsearch');
                $subject = JobSearch_plugin::jobsearch_replace_variables($subject, $this->codes);
                
                $from = (isset($sender_detail_header) && $sender_detail_header != '') ? $sender_detail_header : esc_attr($blogname) . ' <' . $admin_email . '>';
                $recipients = (isset($template['recipients']) && $template['recipients'] != '') ? $template['recipients'] : $this->get_job_added_email();
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
                do_action('jobsearch_send_mail', $args);
                jobsearch_job_expire_to_employer_template::$is_email_sent1 = $this->is_email_sent;
            }
        }

        public static function template_path() {
            return apply_filters('jobsearch_plugin_template_path', 'wp-jobsearch/');
        }

        public function jobsearch_job_expire_to_employer_filter_callback($html, $slug = '', $name = '', $ext_template = '') {
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
            $email_template_options['job_expire_to_employer']['rand'] = $this->rand;
            $email_template_options['job_expire_to_employer']['email_template_prefix'] = $this->email_template_prefix;
            $email_template_options['job_expire_to_employer']['email_template_group'] = $this->email_template_group;
            $email_template_options['job_expire_to_employer']['default_var'] = $this->default_var;
            return $email_template_options;
        }

        public function get_template() {
            return JobSearch_plugin::get_template($this->email_template_db_id, $this->codes, $this->default_content);
        }

        public function get_job_added_user_name() {

            $user_name = $this->user->display_name;
            $user_obj = $this->user;
            $user_name = apply_filters('jobsearch_user_display_name', $user_name, $user_obj);
            return $user_name;
        }

        public function get_job_added_email() {

            $email = $this->user->user_email;
            return $email;
        }

        public function get_job_added_jobtitle() {
            $job_title = get_the_title($this->job_id);
            return $job_title;
        }

        public function get_job_added_joburl() {
            $job_title = get_permalink($this->job_id);
            return $job_title;
        }

        public function get_job_added_jobtype() {

            $terms = wp_get_post_terms($this->job_id, 'jobtype');
            $terms_html = '';
            if (!empty($terms)) {
                $terms_arr = array();
                foreach ($terms as $term) {
                    $terms_arr[] = $term->name;
                }
                $terms_html = implode(', ', $terms_arr);
            }
            return $terms_html;
        }

        public function get_job_added_sector() {

            $terms = wp_get_post_terms($this->job_id, 'sector');
            $terms_html = '';
            if (!empty($terms)) {
                $terms_arr = array();
                foreach ($terms as $term) {
                    $terms_arr[] = $term->name;
                }
                $terms_html = implode(', ', $terms_arr);
            }
            return $terms_html;
        }

        public function get_job_added_sector_list() {

            $terms = wp_get_post_terms($this->job_id, 'sector');
            $terms_html = '';
            if (!empty($terms)) {
                $terms_arr = array();
                foreach ($terms as $term) {
                    $terms_arr[] = $term->name;
                }
                $terms_html = implode(', ', $terms_arr);
            }
            return $terms_html;
        }

        public function get_job_added_publish_date() {
            $date_format = get_option('date_format');
            $date_format = $date_format != '' ? $date_format : 'd-m-Y H:i:s';
            $job_publish_date = get_post_meta($this->job_id, 'jobsearch_field_job_publish_date', true);
            $job_publish_date = isset($job_publish_date) && $job_publish_date != '' ? date_i18n($date_format, $job_publish_date) : '';
            return $job_publish_date;
        }

        public function get_job_added_expiry_date() {
            $date_format = get_option('date_format');
            $date_format = $date_format != '' ? $date_format : 'd-m-Y H:i:s';
            $job_expiry_date = get_post_meta($this->job_id, 'jobsearch_field_job_expiry_date', true);
            $job_expiry_date = isset($job_expiry_date) && $job_expiry_date != '' ? date_i18n($date_format, $job_expiry_date) : '';
            return $job_expiry_date;
        }

        public function get_job_added_featured() {
            $job_featured = get_post_meta($this->job_id, 'jobsearch_field_job_featured', true);
            $job_featured_val = esc_html__('Un Feature', 'wp-jobsearch');
            if ($job_featured == 'on') {
                $job_featured_val = esc_html__('Featured', 'wp-jobsearch');
            }
            return $job_featured_val;
        }

        public function get_job_added_posted_by() {
            $job_posted_by = get_post_meta($this->job_id, 'jobsearch_field_job_posted_by', true);
            $job_posted_by_user = get_the_title($job_posted_by);
            return $job_posted_by_user;
        }

        public function get_job_added_status() {
            $job_status = get_post_meta($this->job_id, 'jobsearch_field_job_status', true);
            return $job_status;
        }

        public function get_job_added_employer_logo() {
            $job_posted_by = get_post_meta($this->job_id, 'jobsearch_field_job_posted_by', true);
            $post_thumbnail_id = get_post_thumbnail_id($job_posted_by);
            $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'large');
            $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
            $image_html = '';
            if ($post_thumbnail_src != '') {
                $image_html .= '<img src="' . esc_url($post_thumbnail_src) . '" alt="">';
            }
            return $image_html;
        }

    }

    new jobsearch_job_expire_to_employer_template();
}