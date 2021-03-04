<?php

if (!class_exists('jobsearch_urgent_package_expire_template')) {

    class jobsearch_urgent_package_expire_template {

        public $email_template_type;
        public $codes;
        public $type;
        public $group;
        public $user;
        public $order_id;
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

            add_action('init', array($this, 'jobsearch_urgent_package_expire_template_init'), 1, 0);
            add_filter('jobsearch_urgent_package_expire_filter', array($this, 'jobsearch_urgent_package_expire_filter_callback'), 1, 4);
            add_filter('jobsearch_email_template_settings', array($this, 'template_settings_callback'), 12, 1);
            add_action('jobsearch_urgent_package_expire_email', array($this, 'jobsearch_urgent_package_expire_callback'), 10, 2);
        }

        public function jobsearch_urgent_package_expire_template_init() {
            $this->user = array();
            $this->order_id = '';
            
            $this->rand = rand(0, 99999);
            $this->group = 'job';
            $this->type = 'urgent_package_expire';
            $this->filter = 'urgent_package_expire';
            $this->email_template_db_id = 'urgent_package_expire';
            $this->switch_label = esc_html__('Urgent Package Expiry', 'wp-jobsearch');
            $this->default_subject = esc_html__('Urgent Package Expire', 'wp-jobsearch');
            $this->default_recipients = '';
            $default_content = esc_html__('Default content', 'wp-jobsearch');
            $default_content = apply_filters('jobsearch_urgent_package_expire_filter', $default_content, 'html', 'urgent-package-expire', 'email-templates');
            $this->default_content = $default_content;
            $this->email_template_prefix = 'urgent_package_expire';
            $this->email_template_group = 'other';
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
                    'var' => '{package_name}',
                    'display_text' => esc_html__('Package Name', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_package_name'),
                ),
                array(
                    'var' => '{package_price}',
                    'display_text' => esc_html__('Package Price', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_package_price'),
                ),
                array(
                    'var' => '{expiry_date}',
                    'display_text' => esc_html__('Package Expiry', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_package_expiry'),
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

        public function jobsearch_urgent_package_expire_callback($user, $order_id = '') {

            global $sitepress;
            $lang_code = '';
            if ( function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') ) {
                $lang_code = $sitepress->get_current_language();
            }
            
            $this->user = $user;
            $this->order_id = $order_id;
            
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
                $subject = (isset($template['subject']) && $template['subject'] != '' ) ? $template['subject'] : __('Package Expire', 'wp-jobsearch');
                $subject = JobSearch_plugin::jobsearch_replace_variables($subject, $this->codes);
                
                $from = (isset($sender_detail_header) && $sender_detail_header != '') ? $sender_detail_header : esc_attr($blogname) . ' <' . $admin_email . '>';
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
                    'message' => $email_message,
                    'email_type' => $email_type,
                    'class_obj' => $this, // temprary comment
                );
                do_action('jobsearch_send_mail', $args);
                jobsearch_urgent_package_expire_template::$is_email_sent1 = $this->is_email_sent;
                return true;
            }
        }

        public static function template_path() {
            return apply_filters('jobsearch_plugin_template_path', 'wp-jobsearch/');
        }

        public function jobsearch_urgent_package_expire_filter_callback($html, $slug = '', $name = '', $ext_template = '') {
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
            $email_template_options['urgent_package_expire']['rand'] = $this->rand;
            $email_template_options['urgent_package_expire']['email_template_prefix'] = $this->email_template_prefix;
            $email_template_options['urgent_package_expire']['email_template_group'] = $this->email_template_group;
            $email_template_options['urgent_package_expire']['default_var'] = $this->default_var;
            return $email_template_options;
        }

        public function get_template() {
            return JobSearch_plugin::get_template($this->email_template_db_id, $this->codes, $this->default_content);
        }

        public function get_username() {

            $user_name = $this->user_name;
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

        public function get_package_name() {

            $trans_order_id = $this->order_id;
            
            $trans_order_name = '';

            $trans_order_obj = wc_get_order($trans_order_id);

            if ($trans_order_name == '') {
                foreach ($trans_order_obj->get_items() as $oitem_id => $oitem_product) {
                    //Get the WC_Product object
                    $oproduct = $oitem_product->get_product();

                    if (is_object($oproduct)) {
                        $trans_order_name = get_the_title($oproduct->get_ID());
                    }
                }
            }

            if ($trans_order_name == '') {
                $trans_order_name = get_post_meta($trans_order_id, 'package_name', true);
            }
            
            if ($trans_order_name == '') {
                $trans_order_name = '-';
            }
            
            return $trans_order_name;
        }
        
        public function get_package_price() {

            $trans_order_id = $this->order_id;
            
            $trans_order_obj = wc_get_order($trans_order_id);
            
            $trans_order_price = $trans_order_obj->get_total();
            
            $order_price = $trans_order_price ? jobsearch_get_price_format($trans_order_price) : '-';
            
            return $order_price;
        }
        
        public function get_package_expiry() {

            $trans_order_id = $this->order_id;
            
            $trans_order_expiry = get_post_meta($trans_order_id, 'package_expiry_timestamp', true);
            
            if ($trans_order_expiry > 0) {
                $order_expdate = date_i18n(get_option('date_format'), $trans_order_expiry);
            } else {
                $order_expdate = '-';
            }
            
            return $order_expdate;
        }

    }

    new jobsearch_urgent_package_expire_template();
}