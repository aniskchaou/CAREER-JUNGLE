<?php
/*
  Class : Maintenance Mode
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Careerfy_MMC {

    // hook things up
    public function __construct() {
        
        add_action('careerfy_maintenance_mode_before_temp_load', array($this, 'maintenance_mode_before_temp_load'), 10);
        
        add_filter('careerfy_theme_body_tag_atts', array($this, 'body_tag_add_style_attr'), 10);
        
        add_filter('careerfy_theme_after_body_tag_html', array($this, 'after_body_tag_html'), 10);
        
        add_action('add_meta_boxes', array($this, 'careerfy_page_maintenace_mode_meta_boxes'), 0, 10);
        // under construction page for site
        if (!is_admin()) {
            add_action('wp', array($this, 'display_construction_page'), 0, 1);
        }
    }

    public function careerfy_page_maintenace_mode_meta_boxes() {
        global $careerfy_framework_options;
        $maintenance_mode_pagemeta_switch = isset($careerfy_framework_options['maintenance-mode-pagemeta-switch']) ? $careerfy_framework_options['maintenance-mode-pagemeta-switch'] : '';
        if ($maintenance_mode_pagemeta_switch) {
            add_meta_box('careerfy-page-maintenace-mode', esc_html__('Coming Soon', 'careerfy-frame'), array($this, 'careerfy_maintenace_mode_meta'), 'page', 'normal');
        }
    }

    /**
     * Page sub-header meta box callback.
     */
    public function careerfy_maintenace_mode_meta() {
        global $post, $careerfy_form_fields;
        
        $_post_id = $post->ID;
        
        $end_date_time = get_post_meta($_post_id, 'careerfy_field_maintnanace_endtime', true);
        ?>
        <div class="careerfy-page-title">
            <div class="careerfy-page-view">
                <div class="careerfy-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Switch', 'careerfy-frame') ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'maintenance_mode_comming_soon',
                        );
                        $careerfy_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="careerfy-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('End Date & Time', 'wp-jobsearch'); ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'force_std' => $end_date_time,
                            'id' => 'jobsearch_page_maintnanace_endtime',
                            'name' => 'maintnanace_endtime',
                        );
                        $careerfy_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="careerfy-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Background Image', 'wp-jobsearch'); ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'id' => 'jobsearch_page_maintnanace_bgimg',
                            'name' => 'maintnanace_bgimg',
                        );
                        $careerfy_form_fields->image_upload_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="careerfy-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Background Color', 'wp-jobsearch'); ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'id' => 'jobsearch_page_maintnanace_bgcolor',
                            'name' => 'maintnanace_bgcolor',
                            'classes' => 'careerfy-bk-color',
                        );
                        $careerfy_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="careerfy-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Small Title', 'wp-jobsearch'); ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'maintnanace_smalltitle',
                        );
                        $careerfy_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="careerfy-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Large Title', 'wp-jobsearch'); ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'maintnanace_largtitle',
                        );
                        $careerfy_form_fields->input_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="careerfy-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Content', 'wp-jobsearch'); ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'maintnanace_content',
                        );
                        $careerfy_form_fields->textarea_field($field_params);
                        ?>
                    </div>
                </div>
                <div class="careerfy-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('Social Network', 'wp-jobsearch'); ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'name' => 'maintnanace_social_ntwork',
                        );
                        $careerfy_form_fields->checkbox_field($field_params);
                        ?>
                    </div>
                </div>
            </div>
            <script>
                jQuery(document).ready(function () {
                    jQuery('#jobsearch_page_maintnanace_endtime').datetimepicker({
                        timepicker: true,
                        format: 'd-m-Y H:i:s'
                    });
                });
            </script>
        </div>
        <?php
    }

    static function display_construction_page() {
        global $careerfy_framework_options;
        $maintenance_mode_date = isset($careerfy_framework_options['maintenance-mode-date']) ? $careerfy_framework_options['maintenance-mode-date'] : '';
        $maintenance_mode_time = isset($careerfy_framework_options['maintenance-mode-time']) ? $careerfy_framework_options['maintenance-mode-time'] : '';
        $maintenance_mode_datetime = '0000-00-00 00:00';
        if (isset($maintenance_mode_date) && $maintenance_mode_date != '') {
            $maintenance_mode_datetime = date("Y-m-d", strtotime($maintenance_mode_date));
            if (isset($maintenance_mode_time) && $maintenance_mode_time != '') {
                $maintenance_mode_datetime = $maintenance_mode_datetime . ' ' . date("H:i", strtotime($maintenance_mode_time));
            } else {
                $maintenance_mode_datetime = $maintenance_mode_datetime . ' 00:00';
            }
        }
        $request_uri = trailingslashit(strtolower(@parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));

        // some URLs have to be accessible at all times
        if ($request_uri == '/wp-admin/' ||
                $request_uri == '/feed/' ||
                $request_uri == '/feed/rss/' ||
                $request_uri == '/feed/rss2/' ||
                $request_uri == '/feed/rdf/' ||
                $request_uri == '/feed/atom/' ||
                $request_uri == '/admin/' ||
                $request_uri == '/wp-login.php') {
            return;
        }

        if (true == self::is_construction_mode_enabled(false) || (is_user_logged_in() && isset($_GET['ucp_preview']))) {
            header(self::get_server_protocol() . ' 503 Service Unavailable');
            if ($maintenance_mode_datetime && $maintenance_mode_datetime != '0000-00-00 00:00') {
                header('Retry-After: ' . date('D, d M Y H:i:s T', strtotime($maintenance_mode_datetime)));
            } else {
                header('Retry-After: ' . DAY_IN_SECONDS);
            }
            //
            do_action('careerfy_maintenance_mode_before_temp_load');
            add_filter('body_class', function ($classes) {
                $classes[] = 'careerfy-maintenance-mode';
                return $classes;
            });
            //echo 'under cunstruction';
            self::careerfy_get_template_part('default', 'template', 'maintenance');
            exit;
        }
    }

    static function get_server_protocol() {
        $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : '';
        if ($protocol && !in_array($protocol, array('HTTP/1.1', 'HTTP/2', 'HTTP/2.0'))) {
            $protocol = 'HTTP/1.0';
        }

        return $protocol;
    }

    // wp_get_server_protocol
    // checks if construction mode is enabled for the current visitor
    static function is_construction_mode_enabled($settings_only = false) {
        global $post, $careerfy_framework_options;
        $page_id = get_the_ID(); // page id
        $page_comming_soon_switch = 'off';
        if ($page_id != '') {
            $page_comming_soon_switch = get_post_meta($page_id, 'careerfy_field_maintenance_mode_comming_soon', true);
        }
        // getting page comming soon option
        $is_page_mode = false;
        if ($page_comming_soon_switch == 'on') {
            $maintenance_mode_switch = 1;
            $is_page_mode = true;
        } else {
            $maintenance_mode_switch = isset($careerfy_framework_options['maintenance-mode-switch']) ? $careerfy_framework_options['maintenance-mode-switch'] : '';
        }
        $maintenance_mode_date = isset($careerfy_framework_options['maintenance-mode-date']) ? $careerfy_framework_options['maintenance-mode-date'] : '';
        $maintenance_mode_time = isset($careerfy_framework_options['maintenance-mode-time']) ? $careerfy_framework_options['maintenance-mode-time'] : '';
        $maintenance_whitelisted_user = isset($careerfy_framework_options['maintenance-whitelisted-user']) ? $careerfy_framework_options['maintenance-whitelisted-user'] : '';
        $maintenance_whitelisted_ips = isset($careerfy_framework_options['maintenance-whitelisted-ips']) ? $careerfy_framework_options['maintenance-whitelisted-ips'] : '';
        $maintenance_whitelisted_ips = $maintenance_whitelisted_ips != '' ? explode(',', $maintenance_whitelisted_ips) : array();
        $maintenance_mode_datetime = '0000-00-00 00:00';
        if (isset($maintenance_mode_date) && $maintenance_mode_date != '') {
            $maintenance_mode_datetime = date("Y-m-d", strtotime($maintenance_mode_date));
            if (isset($maintenance_mode_time) && $maintenance_mode_time != '') {
                $maintenance_mode_datetime = $maintenance_mode_datetime . ' ' . date("H:i", strtotime($maintenance_mode_time));
            } else {
                $maintenance_mode_datetime = $maintenance_mode_datetime . ' 00:00';
            }
        }
        
        $user_ip = careerfy_get_user_ip_addr();
        $current_user = wp_get_current_user();

        // just to be on the safe side
        if (defined('DOING_CRON') && DOING_CRON) {
            return false;
        }
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return false;
        }
        if (defined('WP_CLI') && WP_CLI) {
            return false;
        }

        // just check if it's generally enabled
        if ($settings_only) {
            if ($maintenance_mode_switch) {
                return true;
            } else {
                return false;
            }
        } else {
            // check if enabled for current user
            if (!$maintenance_mode_switch) {
                return false;
            } elseif (self::user_has_role($maintenance_whitelisted_user)) {
                return false;
            }
            elseif (in_array($user_ip, $maintenance_whitelisted_ips)) {
                return false;
            } 
            elseif (strlen($maintenance_mode_datetime) === 16 && $maintenance_mode_datetime !== '0000-00-00 00:00' && $maintenance_mode_datetime < current_time('Y-m-d H:i')) {
                if ($is_page_mode === true) {
                    $page_end_date_time = get_post_meta($page_id, 'careerfy_field_maintnanace_endtime', true);
                    if ($page_end_date_time != '') {
                        $page_end_date_time = strtotime($page_end_date_time);
                        if ($page_end_date_time > current_time('timestamp')) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }
    }
    
    public function maintenance_mode_before_temp_load() {
        global $careerfy_framework_options, $maintenance_style_str, $maintenance_aftrbody_html;
        $page_id = get_the_ID();
        
        $maintenance_background = isset($careerfy_framework_options['maintenance-background']['url']) && $careerfy_framework_options['maintenance-background']['url'] != '' ? $careerfy_framework_options['maintenance-background']['url'] : '';
        $maintenance_background_color = isset($careerfy_framework_options['maintenance-background-color']) ? $careerfy_framework_options['maintenance-background-color'] : '';
        $maintenance_background_color_str = '';
        $maintenance_style_str = '';
        if (isset($maintenance_background_color['rgba'])) {
            $maintenance_background_color = $maintenance_background_color['rgba'];
        }
        $maintenance_bgcolor_style_str = '';
        
        if ($maintenance_background_color != '') {
            $maintenance_background_color_str .= 'background-color: ' . $maintenance_background_color . ' !important;';
        }
        $maintenance_background_str = '';

        if ($maintenance_background != '') {
            $maintenance_background_str = ' background-image: url(\'' . $maintenance_background . '\');';
        }
        if ($maintenance_background != '') {
            $maintenance_style_str = 'style="' . $maintenance_background_str . '"';
        }
        if ($maintenance_background_color_str != '') {
            $maintenance_bgcolor_style_str = ' style="' . $maintenance_background_color_str . '"';
        }
        $maintenance_mode_pagemeta_switch = isset($careerfy_framework_options['maintenance-mode-pagemeta-switch']) ? $careerfy_framework_options['maintenance-mode-pagemeta-switch'] : '';
        if ($maintenance_mode_pagemeta_switch && $page_id != '') {
            $page_comming_soon_switch = get_post_meta($page_id, 'careerfy_field_maintenance_mode_comming_soon', true);
            if ($page_comming_soon_switch == 'on') {
                $maintenance_background = get_post_meta($page_id, 'careerfy_field_maintnanace_bgimg', true);
                $maintenance_background_color = get_post_meta($page_id, 'careerfy_field_maintnanace_bgcolor', true);

                $maintenance_background_color_str = '';
                $maintenance_style_str = '';
                if ($maintenance_background_color != '') {
                    $maintenance_background_color_str .= 'background-color: ' . $maintenance_background_color . ' !important;';
                }
                $maintenance_background_str = '';

                if ($maintenance_background != '') {
                    $maintenance_background_str = ' background-image: url(\'' . $maintenance_background . '\');';
                }
                if ($maintenance_background != '') {
                    $maintenance_style_str = 'style="' . $maintenance_background_str . '"';
                }
                if ($maintenance_background_color_str != '') {
                    $maintenance_bgcolor_style_str = ' style="' . $maintenance_background_color_str . '"';
                }
            }
        }
        
        //
        $maintenance_aftrbody_html = '<span class="careerfy-transparent"' . $maintenance_bgcolor_style_str . '></span>';
    }
    
    public function body_tag_add_style_attr($atts) {
        global $maintenance_style_str;
        
        return $maintenance_style_str;
    }
    
    public function after_body_tag_html($html) {
        global $maintenance_aftrbody_html;
        
        return $maintenance_aftrbody_html;
    }

    // check if user has the specified role
    static function user_has_role($roles) {

        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            if ($current_user->roles) {
                $user_role = $current_user->roles[0];
            } else {
                $user_role = 'guest';
            }

            if (is_array($roles)) {
                return in_array($user_role, $roles);
            }
        }
        return false;
    }

    public static function template_path() {
        return apply_filters('careerfy_plugin_template_path', 'careerfy-framework/');
    }

    /**
     * Include any template file 
     * with wordpress standards
     */
    static function careerfy_get_template_part($slug = '', $name = '', $ext_template = '') {
        $template = '';
        //echo self::template_path() . "{$ext_template}/{$slug}-{$name}.php";
        if ($ext_template != '') {
            $ext_template = trailingslashit($ext_template);
        }
        if ($name) {
            $template = locate_template(array("{$slug}-{$name}.php", self::template_path() . "templates/{$ext_template}/{$slug}-{$name}.php"));
        }
        if (!$template && $name && file_exists(careerfy_framework_get_path() . "templates/{$ext_template}/{$slug}-{$name}.php")) {
            $template = careerfy_framework_get_path() . "templates/{$ext_template}{$slug}-{$name}.php";
        }
        if (!$template) {
            $template = locate_template(array("{$slug}.php", self::template_path() . "{$ext_template}/{$slug}.php"));
        }
        //echo $template;exit;
        if ($template) {
            load_template($template, false);
        }
    }

}

// class Careerfy_MMC 
$Careerfy_MMC_obj = new Careerfy_MMC();
