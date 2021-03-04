<?php

/**
 * Plugin Name: Addon Jobsearch Scheduled Meetings
 * Plugin URI: https://themeforest.net/user/eyecix/
 * Description: This addon is useful with WP Jobsearch Plugin.
 * Version: 1.4
 * Author: Eyecix
 * Author URI: https://themeforest.net/user/eyecix/
 * @package Addon Jobsearch Scheduled Meetings
 * Text Domain: jobsearch-shmeets
 */
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

function jobsearch_sched_meetin_get_path($path = '') {
    return plugin_dir_path(__FILE__) . $path;
}

/**
 * Addon_Jobsearch_Scheduled_Meetings class.
 */
class Addon_Jobsearch_Scheduled_Meetings {

    public $admin_notices;

    /**
     * Defined constants, include classes, enqueue scripts, bind hooks to parent plugin
     */
    public function __construct() {

        $this->admin_notices = array();
        add_action('admin_notices', array($this, 'notices_callback'));
        if (!$this->check_dependencies()) {
            return false;
        }

        // Initialize Addon
        add_action('init', array($this, 'init'), 90);

        add_action('wp_enqueue_scripts', array($this, 'front_style_scripts'), 92);
        
        add_filter('script_loader_tag', array($this, 'add_id_to_script'), 10, 3);

        $this->load_files();
    }

    public static function get_addon_path($path = '') {
        return plugin_dir_path(__FILE__) . $path;
    }

    /**
     * Initialize application, load text domain, enqueue scripts and bind hooks
     */
    public function init() {

        if (function_exists('determine_locale')) {
            $locale = determine_locale();
        } else {
            // @todo Remove when start supporting WP 5.0 or later.
            $locale = is_admin() ? get_user_locale() : get_locale();
        }
        $locale = apply_filters('plugin_locale', $locale, 'jobsearch-shmeets');
        unload_textdomain('jobsearch-shmeets');
        load_textdomain('jobsearch-shmeets', WP_LANG_DIR . '/plugins/jobsearch-shmeets-' . $locale . '.mo');
        load_plugin_textdomain('jobsearch-shmeets', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function front_style_scripts() {
        global $sitepress, $jobsearch_plugin_options;

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        
        $page_id = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $page_id = jobsearch__get_post_id($page_id, 'page');
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $page_id = icl_object_id($page_id, 'page', false, $lang_code);
        }

        $admin_ajax_url = admin_url('admin-ajax.php');
        //
        if (is_page($page_id)) {
            $get_tab = isset($_GET['tab']) ? $_GET['tab'] : '';
            if ($get_tab == 'manage-jobs' || $get_tab == 'all-applicants' || $get_tab == 'meetings') {
                wp_enqueue_style('shmeets-dateselecter', plugin_dir_url(__FILE__) . 'css/multipleDatePicker.css', array(), JobSearch_plugin::get_version());
            }
            if ($get_tab == 'meetings') {
                wp_enqueue_style('shmeets-fullcalendar', plugin_dir_url(__FILE__) . 'css/fullcalendar.css', array(), JobSearch_plugin::get_version());
                wp_enqueue_style('shmeets-addevent', plugin_dir_url(__FILE__) . 'css/addevent.css', array(), JobSearch_plugin::get_version());
            }
            wp_enqueue_style('jobsearch-shmeets-fstyles', plugin_dir_url(__FILE__) . 'css/shmeets-style.css', array(), JobSearch_plugin::get_version());
        }
        wp_register_script('jobsearch-shmeets-scripts', plugin_dir_url(__FILE__) . 'js/shmeets-script.js', array(), JobSearch_plugin::get_version(), true);
        wp_register_script('shmeets-fullcalendar', plugin_dir_url(__FILE__) . 'js/fullcalendar.js', array(), JobSearch_plugin::get_version(), true);
        wp_register_script('shmeets-angular-js', plugin_dir_url(__FILE__) . 'js/angular.min.js', array(), JobSearch_plugin::get_version(), true);
        wp_register_script('shmeets-moment-with-locales', plugin_dir_url(__FILE__) . 'js/moment-with-locales.min.js', array(), JobSearch_plugin::get_version(), true);
        wp_register_script('shmeets-moment-timezone', plugin_dir_url(__FILE__) . 'js/moment-timezone.js', array(), JobSearch_plugin::get_version(), true);
        wp_register_script('shmeets-dateselecter-js', plugin_dir_url(__FILE__) . 'js/multipleDatePicker.min.js', array(), JobSearch_plugin::get_version(), true);
        wp_register_script('shmeets-addevent', 'https://addevent.com/libs/atc/1.6.1/atc.min.js', array(), JobSearch_plugin::get_version(), true);

        $jobsearch_plugin_arr = array(
            'ajax_url' => $admin_ajax_url,
            'wp_locale' => get_locale(),
            'submitting' => esc_html__('Submitting...', 'jobsearch-shmeets'),
            'error_msg' => esc_html__('There is some problem.', 'jobsearch-shmeets'),
        );
        wp_localize_script('jobsearch-shmeets-scripts', 'jobsearch_shmeets_vars', $jobsearch_plugin_arr);
    }
    
    public function add_id_to_script($tag, $handle, $src){
        if ($handle == 'shmeets-angular-js') {
            return '<script src="' . $src . '" id="shmeets-angular-js" data-no-optimize="1" data-cfasync="false"></script>';
        }
        return $tag;
    }

    public function load_files() {
        include dirname(__FILE__) . '/include/common-functions.php';
        include dirname(__FILE__) . '/include/dashtab-content.php';
        include dirname(__FILE__) . '/include/meetings.php';
        include dirname(__FILE__) . '/include/zoom-meetings.php';
        include dirname(__FILE__) . '/include/class-meeting-email-to-candidate.php';
        include dirname(__FILE__) . '/include/class-meeting-reschedule-to-employer.php';
        include dirname(__FILE__) . '/include/class-meeting-cancel-to-employer.php';
    }

    /**
     * Check plugin dependencies if missing.
     *
     * @param boolean $disable disable the plugin if true, defaults to false.
     */
    public function check_dependencies($disable = false) {
        $result = true;
        $active_plugins = get_option('active_plugins', array());
        if (is_multisite()) {
            $active_sitewide_plugins = get_site_option('active_sitewide_plugins', array());
            $active_sitewide_plugins = array_keys($active_sitewide_plugins);
            $active_plugins = array_merge($active_plugins, $active_sitewide_plugins);
        }

        $_is_active = in_array('wp-jobsearch/wp-jobsearch.php', $active_plugins);
        if (!$_is_active) {
            $this->admin_notices[] = '<div class="error">' . __('<em><b>Addon Jobsearch Scheduled Meetings</b></em> needs the <b>WP Jobsearch</b> plugin. Please install and activate it.', 'jobsearch-shmeets') . '</div>';
        }
        if (!$_is_active) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            deactivate_plugins(plugin_basename(__FILE__));
            $result = false;
        }
        return $result;
    }

    public function notices_callback() {
        foreach ($this->admin_notices as $value) {
            echo $value;
        }
    }

}

new Addon_Jobsearch_Scheduled_Meetings();
