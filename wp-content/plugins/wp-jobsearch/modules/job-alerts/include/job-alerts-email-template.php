<?php
/**
 * File Type: Job Alerts Email Templates
 * For trigger email use following hook
 * 
 * do_action('jobsearch_job_alerts_email', $alert_detail);
 * 
 */
if (!class_exists('jobsearch_job_alerts_email_template')) {

    class jobsearch_job_alerts_email_template {

        public $email_template_type;
        public $codes;
        public $type;
        public $group;
        public $alert_detail;
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

            add_action('init', array($this, 'jobsearch_job_alerts_email_template_init'), 1, 0);
            add_filter('jobsearch_job_alerts_email_filter', array($this, 'jobsearch_job_alerts_email_filter_callback'), 1, 4);
            add_filter('jobsearch_email_template_settings', array($this, 'template_settings_callback'), 12, 1);
            add_action('jobsearch_new_job_alerts_email', array($this, 'jobsearch_job_alerts_email_callback'), 10, 1);
        }

        public function jobsearch_job_alerts_email_template_init() {
            $this->alert_detail = array();
            $this->rand = rand(0, 99999);
            $this->group = 'job';
            $this->type = 'job_alerts_email';
            $this->filter = 'job_alerts_email';
            $this->email_template_db_id = 'job_alerts_email';
            $this->switch_label = esc_html__('New Jobs Alert Email', 'wp-jobsearch');
            $this->default_subject = esc_html__('New Jobs Alert Email', 'wp-jobsearch');
            $this->default_recipients = '';
            $default_content = esc_html__('Default content', 'wp-jobsearch');
            $default_content = apply_filters('jobsearch_job_alerts_email_filter', $default_content, 'html', 'new-job-alerts', '');
            $this->default_content = $default_content;
            $this->email_template_prefix = 'job_alerts_email';
            $this->email_template_group = 'candidate';
            $this->codes = array(
                // value_callback replace with function_callback tag replace with var
                array(
                    'var' => '{job_alert_title)',
                    'display_text' => esc_html__('Job Alert Title', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_job_alert_title'),
                ),
                array(
                    'var' => '{jobs_list}',
                    'display_text' => esc_html__('Filtered Jobs List', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_filtered_jobs_list'),
                ),
                array(
                    'var' => '{total_jobs_count}',
                    'display_text' => esc_html__('Total Jobs Found', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_total_jobs_count'),
                ),
                array(
                    'var' => '{unsubscribe_list}',
                    'display_text' => esc_html__('Job Alert Unsubscribe Link', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_unsubscribe_link'),
                ),
                array(
                    'var' => '{job_alert_frequency}',
                    'display_text' => esc_html__('Job Alert Frequency', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_frequency'),
                ),
                array(
                    'var' => '{new_listing_url}',
                    'display_text' => esc_html__('Job Alert Full Listing URL', 'wp-jobsearch'),
                    'function_callback' => array($this, 'get_full_listing_url'),
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

        public function jobsearch_job_alerts_email_callback($alert_detail = array()) {

            global $sitepress;
            $lang_code = '';
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $lang_code = $sitepress->get_current_language();
            }

            $this->alert_detail = $alert_detail;
            $job_id = isset($this->alert_detail['id']) ? $this->alert_detail['id'] : 0;
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
                $subject = (isset($template['subject']) && $template['subject'] != '' ) ? $template['subject'] : __('Jobs alert from ', 'wp-jobsearch') . get_bloginfo('name');
                $subject = JobSearch_plugin::jobsearch_replace_variables($subject, $this->codes);
                
                $from = (isset($sender_detail_header) && $sender_detail_header != '') ? $sender_detail_header : esc_attr($blogname) . ' <' . $admin_email . '>';
                $given_email = isset($this->alert_detail['email']) ? $this->alert_detail['email'] : '';
                $recipients = (isset($template['recipients']) && $template['recipients'] != '') ? $template['recipients'] : $given_email;
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

                $args = array(
                    'to' => $recipients,
                    'subject' => $subject,
                    'from' => $from,
                    'message' => $email_message,
                    'email_type' => $email_type,
                    'class_obj' => $this, // temprary comment
                );
                do_action('jobsearch_send_mail', $args);
                update_post_meta($job_id, 'last_time_email_sent', time());
                jobsearch_job_alerts_email_template::$is_email_sent1 = $this->is_email_sent;
            }
        }

        public static function template_path() {
            return apply_filters('jobsearch_plugin_template_path', 'wp-jobsearch/');
        }

        public function jobsearch_job_alerts_email_filter_callback($html, $slug = '', $name = '', $ext_template = '') {
            ob_start();
            $html = '';
            $template = '';
            if ($ext_template != '') {
                $ext_template = trailingslashit($ext_template);
            }
            if ($name) {
                $template = locate_template(array("{$slug}-{$name}.php", self::template_path() . "{$ext_template}/{$slug}-{$name}.php"));
            }
            if (!$template && $name && file_exists(jobsearch_plugin_get_path() . "modules/job-alerts/templates/{$ext_template}/{$slug}-{$name}.php")) {
                $template = jobsearch_plugin_get_path() . "modules/job-alerts/templates/{$ext_template}{$slug}-{$name}.php";
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
            $email_template_options['job_alerts_email']['rand'] = $this->rand;
            $email_template_options['job_alerts_email']['email_template_prefix'] = $this->email_template_prefix;
            $email_template_options['job_alerts_email']['email_template_group'] = $this->email_template_group;
            $email_template_options['job_alerts_email']['default_var'] = $this->default_var;
            return $email_template_options;
        }

        public function get_template() {
            return JobSearch_plugin::get_template($this->email_template_db_id, $this->codes, $this->default_content);
        }

        public function get_job_alert_title() {
            if (isset($this->alert_detail['title'])) {
                return ucfirst($this->alert_detail['title']);
            }
            return false;
        }

        public function get_filtered_jobs_list() {
            global $jobsearch_plugin_options;
            if (isset($this->alert_detail['jobs_query'])) {
                $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';
                $job_views_publish_date = isset($jobsearch_plugin_options['job_views_publish_date']) ? $jobsearch_plugin_options['job_views_publish_date'] : '';
                $jobs_query = $this->alert_detail['jobs_query'];
                $frequency = str_replace('+', '-', $this->alert_detail['frequency']);
                $jobs_query['meta_query'][0][0] = array(
                    'key' => 'jobsearch_field_job_publish_date',
                    'value' => strtotime(date('Y-m-d', strtotime($frequency))),
                    'compare' => '>=',
                );
                $jobs_query['posts_per_page'] = 10;
                $jobs_query = apply_filters('jobsearch_jobalert_get_list_queryargs_inemail', $jobs_query, $this->alert_detail);
                $loop = new WP_Query($jobs_query);
                
                $loop_posts = $loop->posts;
                ob_start();
                if (!empty($loop_posts)) {
                    ?>
                    <table cellspacing="0" width="100%" style="border-spacing: 0em 0.7em;">
                        <tbody>
                            <?php
                            foreach ($loop_posts as $job_id) {
                                $job_random_id = rand(1111111, 9999999);

                                //$job_id = get_the_id();
                                $job_publish_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                                $post_thumbnail_id = jobsearch_job_get_profile_image($job_id);
                                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
                                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
                                $post_thumbnail_src = $post_thumbnail_src == '' ? jobsearch_no_image_placeholder() : $post_thumbnail_src;
                                $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
                                $company_name = jobsearch_job_get_company_name($job_id, '@ ');
                                $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);

                                $job_city_title = '';
                                $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location3', true);
                                if ($get_job_city == '') {
                                    $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location2', true);
                                }
                                if ($get_job_city != '') {
                                    $get_job_country = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
                                }

                                $job_city_tax = $get_job_city != '' ? get_term_by('slug', $get_job_city, 'job-location') : '';
                                if (is_object($job_city_tax)) {
                                    $job_city_title = isset($job_city_tax->name) ? $job_city_tax->name : '';

                                    $job_country_tax = $get_job_country != '' ? get_term_by('slug', $get_job_country, 'job-location') : '';
                                    if (is_object($job_country_tax)) {
                                        $job_city_title .= isset($job_country_tax->name) ? ', ' . $job_country_tax->name : '';
                                    }
                                } else if ($job_city_title == '') {
                                    $get_job_country = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
                                    $job_country_tax = $get_job_country != '' ? get_term_by('slug', $get_job_country, 'job-location') : '';
                                    if (is_object($job_country_tax)) {
                                        $job_city_title .= isset($job_country_tax->name) ? $job_country_tax->name : '';
                                    }
                                }

                                $job_type_str = jobsearch_job_get_all_jobtypes($job_id, 'jobsearch-option-btn');
                                $sector_str = jobsearch_job_get_all_sectors($job_id, '', '', '', '<li><i class="jobsearch-icon jobsearch-filter-tool-black-shape"></i>', '</li>');
                                ?>
                                <tr>
                                    <td width="100" style="border: 1px solid #ececec; border-right: none; padding: 19px 0px 19px 19px;"><img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="" style="border-radius: 100%; width: 100px;"></td>
                                    <td style="padding-left: 30px; border: 1px solid #ececec; border-left: none; border-right: none;">
                                        <h2 style="display: block; font-size: 18px; margin-bottom: -10px;"><a href="<?php echo (get_permalink($job_id)) ?>"><?php echo (get_the_title($job_id)) ?></a></h2>
                                        <?php
                                        if ($sectors_enable_switch == 'on' && !empty($sector_str)) {
                                            ?>
                                            <br> <small style="font-size: 14px;"><?php esc_html_e('Sector', 'wp-jobsearch'); ?>: <?php echo wp_kses($sector_str, array()) ?></small>
                                            <?php
                                        }
                                        if ($company_name != '') {
                                            ?>
                                            <br> <small style="font-size: 14px;"><?php esc_html_e('Company', 'wp-jobsearch'); ?>: <?php echo ($company_name) ?></small>
                                            <?php
                                        }
                                        if ($job_publish_date != '') {
                                            ?>
                                            <br> <small style="font-size: 14px;"><?php printf(esc_html__('Published %s', 'wp-jobsearch'), jobsearch_time_elapsed_string($job_publish_date)); ?></small>
                                            <?php
                                        }
                                        if ($job_city_title != '') {
                                            ?>
                                            <br> <small style="font-size: 14px;"><?php esc_html_e('Address', 'wp-jobsearch'); ?>: <?php echo ($job_city_title) ?></small>
                                            <?php
                                        } else if (!empty($get_job_location)) {
                                            ?>
                                            <br> <small style="font-size: 14px;"><?php esc_html_e('Address', 'wp-jobsearch'); ?>: <?php echo ($get_job_location) ?></small>
                                            <?php
                                        }
                                        ?> 
                                    </td>
                                    <td style="text-align: right; border: 1px solid #ececec; border-left: none; padding-right: 19px;"><a href="<?php echo (get_permalink($job_id)) ?>" style="border: 1px solid #ececec; text-decoration: none; padding: 10px 22px; color: #333; font-size: 13px; outline: none; "><?php esc_html_e('View', 'wp-jobsearch'); ?></a></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                } else {
                    esc_html_e('No new jobs found.', 'wp-jobsearch');
                }
                $html1 = ob_get_clean();
                return $html1;
            }
            return false;
        }

        public static function get_job_alerts_count($jobs_query, $frequency) {
            $frequency = str_replace('+', '-', $frequency);

            if (!empty($jobs_query)) {
                $jobs_query['meta_query'][0][0] = array(
                    'key' => 'jobsearch_field_job_publish_date',
                    'value' => strtotime(date('Y-m-d', strtotime($frequency))),
                    'compare' => '>=',
                );
                $jobs_query['posts_per_page'] = 1;
                $loop_count = new WP_Query($jobs_query);
                return $loop_count->found_posts;
            } else {
                return '0';
            }
        }

        public function get_total_jobs_count() {

            if (isset($this->alert_detail['jobs_query']) && isset($this->alert_detail['frequency'])) {
                return self::get_job_alerts_count($this->alert_detail['jobs_query'], $this->alert_detail['frequency']);
            }
            return false;
        }

        public function get_unsubscribe_link() {
            if (isset($this->alert_detail['id'])) {
                return '<a href="' . admin_url('admin-ajax.php') . '?action=jobsearch_unsubscribe_job_alert&jaid=' . $this->alert_detail['id'] . '">' . esc_html__('Unsubscribe', 'wp-jobsearch') . '</a>';
            }
            return false;
        }

        public function get_frequency() {
            if (isset($this->alert_detail['set_frequency'])) {
                return $this->alert_detail['set_frequency'];
            }
            return false;
        }

        public function get_full_listing_url() {
            if (isset($this->alert_detail['page_url']) && $this->alert_detail['page_url'] != '') {

                return '<a href="' . $this->alert_detail['page_url'] . '">' . esc_html__('View Full Listing', 'wp-jobsearch') . '</a>';
            }
            return false;
        }

    }

    new jobsearch_job_alerts_email_template();
}