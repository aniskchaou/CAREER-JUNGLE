<?php

if (!defined('ABSPATH')) {
    die;
}

class jobsearch_meeting_email_to_candidate_template {

    public $email_template_type;
    public $codes;
    public $type;
    public $group;
    public $user;
    public $meeting_id;
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

        add_action('init', array($this, 'jobsearch_meeting_email_to_candidate_template_init'), 1, 0);
        add_filter('jobsearch_meeting_email_to_candidate_filter', array($this, 'jobsearch_meeting_email_to_candidate_filter_callback'), 1, 4);
        add_filter('jobsearch_email_template_settings', array($this, 'template_settings_callback'), 12, 1);
        add_action('jobsearch_meeting_email_to_candidate', array($this, 'jobsearch_meeting_email_to_candidate_callback'), 10, 3);
    }

    public function jobsearch_meeting_email_to_candidate_template_init() {
        $this->user = array();
        $this->rand = rand(0, 99999);
        $this->group = 'job';
        $this->type = 'meeting_email_to_candidate';
        $this->filter = 'meeting_email_to_candidate';
        $this->email_template_db_id = 'meeting_email_to_candidate';
        $this->switch_label = esc_html__('New Job Meeting with Candidate', 'jobsearch-shmeets');
        $this->default_subject = esc_html__('New Job Meeting', 'jobsearch-shmeets');
        $this->default_recipients = '';
        $default_content = esc_html__('Default content', 'jobsearch-shmeets');
        $default_content = apply_filters('jobsearch_meeting_email_to_candidate_filter', $default_content, 'html', 'job-meeting-with-cand', 'email-templates');
        $this->default_content = $default_content;
        $this->email_template_prefix = 'meeting_email_to_candidate';
        $this->email_template_group = 'candidate';
        $this->codes = apply_filters('jobsearch_meetin_email_tocand_codes', array(
            // value_callback replace with function_callback tag replace with var
            array(
                'var' => '{candidate_name}',
                'display_text' => esc_html__('Candidate name', 'jobsearch-shmeets'),
                'function_callback' => array($this, 'get_candidate_name'),
            ),
            array(
                'var' => '{meeting_date}',
                'display_text' => esc_html__('Meeting Date', 'jobsearch-shmeets'),
                'function_callback' => array($this, 'get_meeting_date'),
            ),
            array(
                'var' => '{meeting_time}',
                'display_text' => esc_html__('Meeting Time', 'jobsearch-shmeets'),
                'function_callback' => array($this, 'get_meeting_time'),
            ),
            array(
                'var' => '{meeting_duration}',
                'display_text' => esc_html__('Meeting Duration', 'jobsearch-shmeets'),
                'function_callback' => array($this, 'get_meeting_duration'),
            ),
            array(
                'var' => '{meeting_notes}',
                'display_text' => esc_html__('Meeting Notes', 'jobsearch-shmeets'),
                'function_callback' => array($this, 'get_meeting_notes'),
            ),
            array(
                'var' => '{employer_name}',
                'display_text' => esc_html__('Employer name', 'jobsearch-shmeets'),
                'function_callback' => array($this, 'get_employer_name'),
            ),
            array(
                'var' => '{employer_link}',
                'display_text' => esc_html__('Employer Link', 'jobsearch-shmeets'),
                'function_callback' => array($this, 'get_employer_link'),
            ),
            array(
                'var' => '{job_title}',
                'display_text' => esc_html__('Job Title', 'jobsearch-shmeets'),
                'function_callback' => array($this, 'get_job_title'),
            ),
            array(
                'var' => '{job_link}',
                'display_text' => esc_html__('Job Link', 'jobsearch-shmeets'),
                'function_callback' => array($this, 'get_job_link'),
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

    public function jobsearch_meeting_email_to_candidate_callback($user = '', $meeting_id = '', $job_id = '') {

        global $sitepress, $jobsearch_plugin_options;
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        $this->user = $user;
        $this->job_id = $job_id;
        $this->meeting_id = $meeting_id;
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
            $subject = (isset($template['subject']) && $template['subject'] != '' ) ? $template['subject'] : __('New Job Meeting', 'jobsearch-shmeets');
            $subject = JobSearch_plugin::jobsearch_replace_variables($subject, $this->codes);

            $from = (isset($sender_detail_header) && $sender_detail_header != '') ? $sender_detail_header : esc_attr($blogname) . ' <' . $admin_email . '>';
            $recipients = (isset($template['recipients']) && $template['recipients'] != '') ? $template['recipients'] : $this->get_recipient_email();
            $recipients = apply_filters('jobsearch_job_aplyto_cand_email_recipients', $recipients, $job_id);
            $email_type = (isset($template['email_type']) && $template['email_type'] != '') ? $template['email_type'] : 'html';

            $email_message = isset($template['email_template']) ? $template['email_template'] : '';

            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
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

            //
            //var_dump($att_file_id);
            $job_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
            $employer_user_id = jobsearch_get_employer_user_id($job_emp_id);
            $empp_user = get_user_by('ID', $employer_user_id);
            $user_email = $empp_user->user_email;
            $user_name = $empp_user->display_name;
            $user_name = apply_filters('jobsearch_user_display_name', $user_name, $empp_user);

            $args = array(
                'to' => $recipients,
                'subject' => $subject,
                'from' => $from,
                'from_name' => $user_name,
                'from_email' => $user_email,
                'message' => $email_message,
                'email_type' => $email_type,
                'class_obj' => $this, // temprary comment
            );
            do_action('jobsearch_send_mail', $args);
            jobsearch_meeting_email_to_candidate_template::$is_email_sent1 = $this->is_email_sent;
        }
    }

    public static function template_path() {
        return apply_filters('jobsearch_plugin_template_path', 'jobsearch-shmeets/');
    }

    public function jobsearch_meeting_email_to_candidate_filter_callback($html, $slug = '', $name = '', $ext_template = '') {
        ob_start();
        $html = '';
        $template = '';
        if ($ext_template != '') {
            $ext_template = trailingslashit($ext_template);
        }
        if (!$template && $name && file_exists(jobsearch_sched_meetin_get_path() . "{$ext_template}/{$slug}-{$name}.php")) {
            $template = jobsearch_sched_meetin_get_path() . "{$ext_template}{$slug}-{$name}.php";
        }
        if ($template) {
            load_template($template, false);
        }
        $html = ob_get_clean();
        return $html;
    }

    public function template_settings_callback($email_template_options) {

        $rand = rand(123, 8787987);
        $email_template_options['meeting_email_to_candidate']['rand'] = $this->rand;
        $email_template_options['meeting_email_to_candidate']['email_template_prefix'] = $this->email_template_prefix;
        $email_template_options['meeting_email_to_candidate']['email_template_group'] = $this->email_template_group;
        $email_template_options['meeting_email_to_candidate']['default_var'] = $this->default_var;
        return $email_template_options;
    }

    public function get_template() {
        return JobSearch_plugin::get_template($this->email_template_db_id, $this->codes, $this->default_content);
    }

    public function get_recipient_email() {

        $user_obj = $this->user;
        $user_email = $user_obj->user_email;
        return $user_email;
    }

    public function get_candidate_name() {

        $user_name = $this->user->display_name;
        $user_obj = $this->user;
        $user_name = apply_filters('jobsearch_user_display_name', $user_name, $user_obj);
        return $user_name;
    }

    public function get_meeting_date() {
        $meeting_id = $this->meeting_id;
        $value = get_post_meta($meeting_id, 'meeting_date', true);
        if ($value > 0) {
            $value = date_i18n(get_option('date_format'), $value);
        }
        return $value;
    }

    public function get_meeting_time() {
        $meeting_id = $this->meeting_id;
        $value = get_post_meta($meeting_id, 'meeting_time', true);
        return $value;
    }

    public function get_meeting_duration() {
        $meeting_id = $this->meeting_id;
        $value = get_post_meta($meeting_id, 'meeting_duration', true);
        if ($value != '') {
            $value = sprintf(esc_html__('%s Minutes', 'jobsearch-shmeets'), $value);
        }
        return $value;
    }

    public function get_meeting_notes() {
        $meeting_id = $this->meeting_id;
        $value = get_post_meta($meeting_id, 'meeting_note', true);
        return $value;
    }

    public function get_employer_name() {
        $job_posted_by = get_post_meta($this->job_id, 'jobsearch_field_job_posted_by', true);
        $job_posted_by_user = get_the_title($job_posted_by);
        return $job_posted_by_user;
    }

    public function get_employer_link() {
        $job_posted_by = get_post_meta($this->job_id, 'jobsearch_field_job_posted_by', true);
        $job_posted_by_link = get_permalink($job_posted_by);
        return $job_posted_by_link;
    }

    public function get_job_title() {
        $job_title = get_the_title($this->job_id);
        return $job_title;
    }

    public function get_job_link() {
        $job_title = get_permalink($this->job_id);
        return $job_title;
    }

}

new jobsearch_meeting_email_to_candidate_template();
