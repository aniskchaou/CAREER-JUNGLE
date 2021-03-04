<?php
/**
 * Plugin Name: Addon Jobsearch Resume Export
 * Plugin URI: https://themeforest.net/user/eyecix/
 * Description: This addon is useful for exporting CVs like PDFs and excel 'xlsx' formate.
 * Version: 1.8
 * Author: Eyecix
 * Author URI: https://themeforest.net/user/eyecix/
 * @package Addon Jobsearch Resume Export
 * Text Domain: jobsearch-resume-export
 */

//Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Addon Jobsearch Resume Export.
 *
 */
class Addon_Jobsearch_RESUME_EXPORT
{
    public $admin_notices;
    public $jobsearch_admin;
    public $args;

    /**
     * Defined constants, include classes, enqueue scripts, bind hooks to parent plugin
     */
    public function __construct()
    {
        $this->load_files();
        $this->admin_notices = array();
        add_action('admin_notices', array($this, 'notices_callback'));
        if (!$this->check_dependencies()) {
            return false;
        }
        // Initialize Addon
        add_action('init', array($this, 'init'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'),100);
        add_action('jobsearch_enqueue_dashboard_styles', array($this, 'dashboard_styles_resume'));
    }

    public function load_files()
    {
        include dirname(__FILE__) . '/includes/mpdf/autoload.php';
        include dirname(__FILE__) . '/includes/common-functions.php';
        include dirname(__FILE__) . '/includes/jobsearch-resume-export-hooks.php';
        include dirname(__FILE__) . '/includes/jobsearch-resume-export-admin-hooks.php';
        include dirname(__FILE__) . '/includes/user-uploaded-resume-export.php';
        /*
         * Load All PDF template files
         */
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-default-template.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-one.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-two.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-three.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-four.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-five.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-six.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-seven.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-eight.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-nine.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-ten.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-eleven.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-twelve.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-thirteen.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-fourteen.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-fifteen.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-sixteen.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-seventeen.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-eighteen.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-nineteen.php';
        include dirname(__FILE__) . '/templates/jobsearch-candidate-resume-template-twenty.php';
    }

    /**
     * Initialize application, load text domain, enqueue scripts and bind hooks
     */
    public function init()
    {
        if (function_exists('determine_locale')) {
            $locale = determine_locale();
        } else {
            // @todo Remove when start supporting WP 5.0 or later.
            $locale = is_admin() ? get_user_locale() : get_locale();
        }
        $locale = apply_filters('plugin_locale', $locale, 'jobsearch-resume-export');
        unload_textdomain('jobsearch-resume-export');
        load_textdomain('jobsearch-resume-export', WP_LANG_DIR . '/plugins/jobsearch-resume-export-' . $locale . '.mo');
        load_plugin_textdomain('jobsearch-resume-export', false, dirname(dirname(plugin_basename(__FILE__))) . '/languages');
    }

    public function dashboard_styles_resume()
    {
        wp_enqueue_style('jobsearch-pdf-export-style', plugin_dir_url(__FILE__) . 'css/jobsearch-export-resume.css', array(), JobSearch_plugin::get_version());
    }

    public function wp_enqueue_scripts()
    {
        global $jobsearch_plugin_options, $sitepress;
        //
        $page_id = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $page_id = jobsearch__get_post_id($page_id, 'page');
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $page_id = icl_object_id($page_id, 'page', false, $lang_code);
        }
        if (!is_page($page_id)) {
            return;
        }
        wp_enqueue_script('jobsearch-pdf-export-script', plugin_dir_url(__FILE__) . 'js/jobsearch-resume-script.js', array(), JobSearch_plugin::get_version());
        $jobsearch_plugin_arr = array(
            'active' => esc_html__('Active', 'jobsearch-resume-export'),
        );
        wp_localize_script('jobsearch-pdf-export-script', 'jobsearch_export_vars', $jobsearch_plugin_arr);
    }

    public function admin_enqueue_scripts()
    {
        wp_register_style('jobsearch-admin-pdf-export-style', plugin_dir_url(__FILE__) . 'css/jobsearch-admin-export-resume.css', array(), JobSearch_plugin::get_version());
        wp_enqueue_script('jobsearch-pdf-export-script', plugin_dir_url(__FILE__) . 'js/jobsearch-admin-resume-script.js', array(), JobSearch_plugin::get_version());
        $jobsearch_plugin_arr = array(
            'export_title' => esc_html__('Export', 'jobsearch-resume-export'),
            'export_to_pdf' => esc_html__('Export to PDF', 'jobsearch-resume-export'),
            'export_to_excel' => esc_html__('Export to Excel', 'jobsearch-resume-export'),
            'active' => esc_html__('Active', 'jobsearch-resume-export'),
        );
        wp_localize_script('jobsearch-pdf-export-script', 'jobsearch_export_vars', $jobsearch_plugin_arr);
    }

    /**
     * Check plugin dependencies if missing.
     *
     * @param boolean $disable disable the plugin if true, defaults to false.
     */
    public function check_dependencies($disable = false)
    {
        $result = true;
        $active_plugins = get_option('active_plugins', array());
        if (is_multisite()) {
            $active_sitewide_plugins = get_site_option('active_sitewide_plugins', array());
            $active_sitewide_plugins = array_keys($active_sitewide_plugins);
            $active_plugins = array_merge($active_plugins, $active_sitewide_plugins);
        }

        $_is_active = in_array('wp-jobsearch/wp-jobsearch.php', $active_plugins);
        if (!$_is_active) {
            $this->admin_notices[] = '<div class="error">' . __('<em><b>Addon Jobsearch Resume Export</b></em> needs the <b>WP Jobsearch</b> plugin. Please install and activate it.', 'jobsearch-resume-export') . '</div>';
        }
        if (!$_is_active) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            deactivate_plugins(plugin_basename(__FILE__));
            $result = false;
        }
        return $result;
    }

    public function jobsearch_resume_export_get_path($path = '')
    {
        return plugin_dir_path(__FILE__) . $path;
    }

    public function jobsearch_pdf_resume_get_url($path = '')
    {
        return plugin_dir_url(__FILE__) . $path;
    }

    public function notices_callback()
    {
        foreach ($this->admin_notices as $value) {
            echo $value;
        }
    }
}

global $jobsearch_resume_export;
$jobsearch_resume_export = new Addon_Jobsearch_RESUME_EXPORT();