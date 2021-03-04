<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

define('CAREERFY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CAREERFY_PLUGIN_URL', plugin_dir_url(__FILE__));

final class CareerfyElementor
{
    const VERSION = '1.0.0';
    const MINIMUM_ELEMENTOR_VERSION = '2.0.0';
    const MINIMUM_PHP_VERSION = '7.0';

    public function __construct()
    {
        add_action('init', array($this, 'i18n'));
        add_action('plugins_loaded', array($this, 'init'));
        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);
    }

    public function i18n()
    {
        load_plugin_textdomain('careerfy-frame');
    }


    public function init()
    {

        // Check if Elementor installed and activated
        if (!did_action('elementor/loaded')) {
            //add_action('admin_notices', array($this, 'admin_notice_missing_main_plugin'));
            return;
        }

        // Check for required Elementor version
        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            //add_action('admin_notices', array($this, 'admin_notice_minimum_elementor_version'));
            return;
        }

        // Check for required PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            //add_action('admin_notices', array($this, 'admin_notice_minimum_php_version'));
            return;
        }

        // Once we get here, We have passed all validation checks so we can safely include our plugin
        require_once('class-elementor-init.php');
    }

    public function admin_notice_missing_main_plugin()
    {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
        /* translators: 1: Plugin name 2: Elementor */
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'careerfy-frame'),
            '<strong>' . esc_html__('Elementor Eyecix', 'careerfy-frame') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'careerfy-frame') . '</strong>'
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required Elementor version.
     *
     * @since 1.0.0
     * @access public
     */
    public function admin_notice_minimum_elementor_version()
    {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
        /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'careerfy-frame'),
            '<strong>' . esc_html__('Elementor Eyecix', 'careerfy-frame') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'careerfy-frame') . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required PHP version.
     *
     * @since 1.0.0
     * @access public
     */
    public function admin_notice_minimum_php_version()
    {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
        /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'careerfy-frame'),
            '<strong>' . esc_html__('Elementor Eyecix', 'careerfy-frame') . '</strong>',
            '<strong>' . esc_html__('PHP', 'careerfy-frame') . '</strong>',
            self::MINIMUM_PHP_VERSION
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    public function add_elementor_widget_categories($elements_manager)
    {
        $elements_manager->add_category(
            'wp-jobsearch',
            [
                'title' => __('WP Jobsearch', 'careerfy-frame'),
                'icon' => 'fa fa-plug',
            ]
        );
        $elements_manager->add_category(
            'careerfy',
            [
                'title' => __('Careerfy', 'careerfy-frame'),
                'icon' => 'fa fa-plug',
            ]
        );
    }
}
new CareerfyElementor();