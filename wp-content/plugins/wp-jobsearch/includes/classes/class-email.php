<?php
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * File Type: JobSearch Email
 */
if (!class_exists('JobSearch_Email')) {

    class JobSearch_Email {

        public function __construct() {
            //add_action('wp_ajax_jobsearch_process_emails', array($this, 'jobsearch_process_emails_callback'), 99);
            //add_action('wp_ajax_nopriv_jobsearch_process_emails', array($this, 'jobsearch_process_emails_callback'), 99);
            add_action('jobsearch_send_mail', array($this, 'jobsearch_send_mail_callback'), 20, 1);
            add_action('wp_ajax_jobsearch_email_log_clear_cronjob', array($this, 'jobsearch_email_log_clear_cronjob_callback'), 99);
            add_action('wp_ajax_nopriv_jobsearch_email_log_clear_cronjob', array($this, 'jobsearch_email_log_clear_cronjob_callback'), 99);

            //
            //add_filter('wp_mail_from', array($this, 'sender_email'));
            //add_filter('wp_mail_from_name', array($this, 'sender_name'));
        }

        public function sender_email($original_email_address) {
            $field_db_slug = "jobsearch_email_templates";
            $email_all_templates_saved_data = get_option($field_db_slug);
            $from = isset($email_all_templates_saved_data['jobsearch_email_template_sender_email']) ? $email_all_templates_saved_data['jobsearch_email_template_sender_email'] : '';
            if ($from != '') {
                $original_email_address = $from;
            }
            return $original_email_address;
        }

        // Function to change sender name
        public function sender_name($original_email_from) {
            global $sitepress;
            $lang_code = '';
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $lang_code = $sitepress->get_current_language();
            }
            $field_db_slug = "jobsearch_email_templates";
            $email_all_templates_saved_data = get_option($field_db_slug);
            $from_name = isset($email_all_templates_saved_data['jobsearch_email_template_sender_name']) ? $email_all_templates_saved_data['jobsearch_email_template_sender_name'] : '';

            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $temp_trnaslated = get_option('jobsearch_translate_email_templates');
                if (isset($temp_trnaslated['global_settings']['lang_' . $lang_code]['sender_name'])) {
                    $from_name = $temp_trnaslated['global_settings']['lang_' . $lang_code]['sender_name'];
                }
            }

            if ($from_name != '') {
                $original_email_from = $from_name;
            }
            return $original_email_from;
        }

        public function jobsearch_send_mail_callback($args) {
            global $jobsearch_plugin_options;

            $email_logs_switch = isset($jobsearch_plugin_options['jobsearch-email-log-switch']) ? $jobsearch_plugin_options['jobsearch-email-log-switch'] : 'off'; // by default 

            $send_to = (isset($args['to'])) ? $args['to'] : '';
            $subject = (isset($args['subject'])) ? $args['subject'] : '';
            $message = (isset($args['message'])) ? $args['message'] : '';
            $headers = array();
            if (isset($args['from']) && $args['from'] != '') {
                $headers[] = 'From: ' . $args['from'];
            }
            $email_type = 'plain_text';
            if (isset($args['email_type'])) {
                $email_type = $args['email_type'];
            }

            $headers = ( isset($args['headers']) ) ? $args['headers'] : $headers;
            $class_obj = ( isset($args['class_obj']) ) ? $args['class_obj'] : '';

            $post_id = $this->jobsearch_save_email(array(
                'sent_to' => $send_to,
                'subject' => $subject,
                'message' => $message,
                'headers' => $headers,
                'email_type' => $email_type,
            ));

            if ($post_id != 0) {
                //wp_remote_get(admin_url('admin-ajax.php?action=jobsearch_process_emails&post_id=' . $post_id), array('timeout' => 0, 'httpversion' => '1.1'));
                $this->jobsearch_process_emails_callback($post_id, $args);
            }

            if ($class_obj != '') {
                $class_obj->is_email_sent = true;
            }

            if ($email_logs_switch != 'on' && $post_id != '' && is_numeric($post_id)) {
                wp_delete_post($post_id, true);
            }
        }

        public function jobsearch_save_email($args) {
            // Create post object
            $email_post = array(
                'post_title' => $args['subject'],
                'post_content' => $args['message'],
                'post_status' => 'publish',
                'post_type' => 'email',
            );
            // Insert the post into the database.
            $id = wp_insert_post($email_post);

            if (!is_wp_error($id)) {
                update_post_meta($id, 'email_status', 'new');
                update_post_meta($id, 'email_send_satus', 0);
                update_post_meta($id, 'email_headers', $args['headers']);
                update_post_meta($id, 'email_send_to', $args['sent_to']);
                update_post_meta($id, 'email_type', $args['email_type']);
                return $id;
            } else {
                return 0;
            }
        }

        public function jobsearch_process_emails_callback($_post_id = 0, $mail_args = array()) {

            add_filter('wp_mail_from', function ($original_email_address) use ($mail_args) {
                $field_db_slug = "jobsearch_email_templates";
                $email_all_templates_saved_data = get_option($field_db_slug);
                $from = isset($email_all_templates_saved_data['jobsearch_email_template_sender_email']) ? $email_all_templates_saved_data['jobsearch_email_template_sender_email'] : '';
                
                $site_email_addr = get_bloginfo('admin_email');
                if ($site_email_addr != '') {
                    $original_email_address = $site_email_addr;
                }
                if ($from != '') {
                    $original_email_address = $from;
                }
                return $original_email_address;
            });

            add_filter('wp_mail_from_name', function ($original_email_from) use ($mail_args) {
                global $sitepress;
                $lang_code = '';
                if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                    $lang_code = $sitepress->get_current_language();
                }
                $field_db_slug = "jobsearch_email_templates";
                $email_all_templates_saved_data = get_option($field_db_slug);
                $from_name = isset($email_all_templates_saved_data['jobsearch_email_template_sender_name']) ? $email_all_templates_saved_data['jobsearch_email_template_sender_name'] : '';

                if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                    $temp_trnaslated = get_option('jobsearch_translate_email_templates');
                    if (isset($temp_trnaslated['global_settings']['lang_' . $lang_code]['sender_name'])) {
                        $from_name = $temp_trnaslated['global_settings']['lang_' . $lang_code]['sender_name'];
                    }
                }

                $site_name = get_bloginfo('name');
                if ($site_name != '') {
                    $original_email_from = $site_name;
                }

                if ($from_name != '') {
                    $original_email_from = $from_name;
                }
                return $original_email_from;
            });
            
            $args = array(
                'post_type' => 'email',
            );

            if ($_post_id > 0) {
                $post_id = $_post_id;
            } else {
                $post_id = isset($_REQUEST['post_id']) ? $_REQUEST['post_id'] : 0;
            }
            //update_post_meta($post_id, 'email_status', 'new');

            if ($post_id != 0) {
                $args['post__in'] = array(intval($post_id));
            }
            $args['meta_query'] = array(
                array(
                    'key' => 'email_status',
                    'value' => 'new',
                    'compare' => 'LIKE',
                )
            );
            $query = new WP_Query($args);

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $email_log_post_id = get_the_ID();
                    $email_obj = get_post($email_log_post_id);
                    $email_content = $email_obj->post_content;
                    $email_content = apply_filters('the_content', $email_content);
                    $subject = get_the_title();
                    $message = $email_content;
                    $send_to = get_post_meta($email_log_post_id, 'email_send_to', true);
                    $headers = get_post_meta($email_log_post_id, 'email_headers', true);
                    $email_type = get_post_meta($email_log_post_id, 'email_type', true);
                    if (!empty($email_type)) {
                        if ($email_type == 'html') {
                            add_filter('wp_mail_content_type', function () {
                                return 'text/html';
                            });
                        } else {
                            add_filter('wp_mail_content_type', function () {
                                return 'text/plain';
                            });
                        }
                    }
                    
                    $headers = !empty($headers) ? $headers : array();
                    $email_from_name = isset($mail_args['from_name']) ? $mail_args['from_name'] : '';
                    $email_from_email = isset($mail_args['from_email']) ? $mail_args['from_email'] : '';
                    
                    if ($email_from_name != '' && $email_from_email != '') {
                        $headers[] = 'Reply-To: ' . $email_from_name . ' <' . $email_from_email . '>';
                    }

                    $attachment = '';
                    if (isset($mail_args['att_file_path']) && $mail_args['att_file_path'] != '') {
                        $attachment = $mail_args['att_file_path'];
                    }

                    $confirm = false;

                    $send_to = str_replace(' ', '', $send_to);
                    $send_to_adrs = explode(',', $send_to);

                    if (!empty($send_to_adrs) && sizeof($send_to_adrs) > 1) {
                        foreach ($send_to_adrs as $email_to) {
                            $confirm = wp_mail($email_to, $subject, $message, $headers, $attachment);
                        }
                    } else {
                        $confirm = wp_mail($send_to, $subject, $message, $headers, $attachment);
                    }

                    update_post_meta($email_log_post_id, 'email_status', 'processed');
                    update_post_meta($email_log_post_id, 'email_send_satus', $confirm);
                }
                wp_reset_postdata();
            } else {
                //echo esc_html__('No Posts found', 'wp-jobsearch');
            }
            //wp_die();
        }

        public function jobsearch_email_log_clear_cronjob_callback() {
            global $wpdb;
            // do something every hour
            $query = "DELETE a,b,c
            FROM " . $wpdb->prefix . "posts a
            LEFT JOIN " . $wpdb->prefix . "term_relationships b
                ON (a.ID = b.object_id)
            LEFT JOIN " . $wpdb->prefix . "postmeta c
                ON (a.ID = c.post_id)
            WHERE a.post_type = 'email';";
            $wpdb->query($query);
            die;
        }
    }

    new JobSearch_Email();
}
