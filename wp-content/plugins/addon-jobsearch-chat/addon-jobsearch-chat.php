<?php

/**
 * Plugin Name: Addon Jobsearch Chat
 * Plugin URI: https://themeforest.net/user/eyecix/
 * Description: This addon is useful for Employer to Candidate chat.
 * Version: 1.9
 * Author: Eyecix
 * Author URI: https://themeforest.net/user/eyecix/
 * @package Addon Jobsearch Chat
 * Text Domain: jobsearch-ajchat
 */
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Addon_Jobsearch Chat class.
 *
 */
class Addon_Jobsearch_Chat
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
        //Initialize Addon
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'front_enqueue_scripts'), 100);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'chat_enqueue_style_backend'), 100);
        add_action('init', array($this, 'jobsearch_create_chat_user_role'));
        add_action('admin_menu', array($this, 'jobsearch_modifier_role_add_caps'));
        register_activation_hook(__FILE__, array($this, 'jobsearch_chat_activate'));
    }

    public function load_files()
    {
        include dirname(__FILE__) . '/includes/jobsearch-chat-hooks.php';
        include dirname(__FILE__) . '/includes/common-functions.php';
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

        $locale = apply_filters('plugin_locale', $locale, 'jobsearch-ajchat');
        unload_textdomain('jobsearch-resume-export');
        load_textdomain('jobsearch-resume-export', WP_LANG_DIR . '/plugins/jobsearch-ajchat-' . $locale . '.mo');
        load_plugin_textdomain('jobsearch-resume-export', false, dirname(dirname(plugin_basename(__FILE__))) . '/languages');
    }

    public function jobsearch_create_chat_user_role()
    {
        //remove_role("jobsearch_chat_support");
        // create user role for user
        add_role(
            'jobsearch_chat_support', esc_html('Chat Support'), array(
                'jobsearch_chat_support' => true,
                'read' => false,
                'edit_posts' => false,
                'delete_posts' => false,
            )
        );
    }

    public function jobsearch_modifier_role_add_caps()
    {
        $roles = wp_get_current_user()->roles;
        if (!in_array('jobsearch_chat_support', $roles)) {
            return;
        }
        remove_menu_page('edit.php'); //Posts
        remove_menu_page('upload.php');
        remove_menu_page('edit-comments.php');
        remove_menu_page('edit.php?post_type=faq');
        remove_menu_page('edit.php?post_type=job');
        remove_menu_page('edit.php?post_type=employer');
        remove_menu_page('edit.php?post_type=candidate');
        remove_menu_page('edit.php?post_type=dashb_menu');
        remove_menu_page('edit.php?post_type=package');
    }

    public function jobsearch_chat_activate()
    {
        global $wpdb;
        //
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset_collate = $wpdb->get_charset_collate();
        $sql_1 = "CREATE TABLE `{$wpdb->base_prefix}chatmessages` (
        `chat_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `sender_id` INT(11) NOT NULL,
        `reciever_id` INT(11) NOT NULL,
        `message` longtext NOT NULL,
        `viewed` INT(11) DEFAULT '0',
        `is_deleted` TINYINT(1) NOT NULL DEFAULT '0',
        `time_sent` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`chat_id`)
        ) $charset_collate;";
        //
        $sql_2 = "CREATE TABLE `{$wpdb->base_prefix}chatfriendlist` (
        `f_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `employer_id` INT(11) NOT NULL,
        `candidate_id` INT(11) NOT NULL,
        `is_active` INT(11) NOT NULL,
        `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`f_id`)
        ) $charset_collate;";

        dbDelta($sql_1);
        dbDelta($sql_2);
    }

    public function front_enqueue_scripts()
    {
        wp_enqueue_style('jobsearch-chat-floating-window-style', plugin_dir_url(__FILE__) . 'css/jobsearch-floating-window-styles.css', array(), JobSearch_plugin::get_version());
        wp_enqueue_script('jobsearch-chat-floating-window-script', plugin_dir_url(__FILE__) . 'js/jobsearch-floating-window-script.js', array(), JobSearch_plugin::get_version(), true);
        wp_enqueue_style('jobsearch-chat-app', plugin_dir_url(__FILE__) . 'css/jobsearch-chat-style.css', array(), JobSearch_plugin::get_version());
    }

    public function chat_enqueue_style_backend()
    {
        if (isset($_GET['page']) && $_GET['page'] == 'jobsearch-chat-box') {
            wp_enqueue_style('jobsearch-chat-app-backend', plugin_dir_url(__FILE__) . 'css/jobsearch-chat-backend-style.css', array(), JobSearch_plugin::get_version());
        }
    }

    public function enqueue_scripts()
    {
        global $sitepress, $jobsearch_plugin_options;
        $admin_ajax_url = admin_url('admin-ajax.php');
        if (function_exists('icl_object_id')) {
            $lang_code = $sitepress->get_current_language();
            $admin_ajax_url = add_query_arg(array('lang' => $lang_code), $admin_ajax_url);
        }
        //
        wp_enqueue_style('jobsearch-chat-emoji-style', 'https://www.jqueryscript.net/css/jquerysctipttop.css', array(), JobSearch_plugin::get_version());
        wp_enqueue_style('jobsearch-chat-fonts', plugin_dir_url(__FILE__) . 'css/chat-font-style.css', array(), JobSearch_plugin::get_version());
        //
        wp_enqueue_script('jobsearch-pusher', 'https://js.pusher.com/5.1/pusher.min.js', array(), JobSearch_plugin::get_version(), true);
        wp_enqueue_script('jobsearch-chat-nice-scroll', plugin_dir_url(__FILE__) . 'js/jquery.nicescroll.min.js', array(), JobSearch_plugin::get_version(), true);
        //
        wp_enqueue_script('jobsearch-chat-app', plugin_dir_url(__FILE__) . 'js/jobsearch-chat-functions.js', array(), JobSearch_plugin::get_version(), true);
        //
        $php_pusher_auth = isset($jobsearch_plugin_options['jobsearch-php-pusher-auth-key']) ? $jobsearch_plugin_options['jobsearch-php-pusher-auth-key'] : '';
        $php_pusher_cluster = isset($jobsearch_plugin_options['jobsearch-php-pusher-auth-cluster']) ? $jobsearch_plugin_options['jobsearch-php-pusher-auth-cluster'] : '';
        $jobsearch_plugin_arr = array(
            'ajax_url' => $admin_ajax_url,
            'current_user' => get_current_user_id(),
            'jobsearch_ajax_url' => plugin_dir_url(__FILE__) . 'includes/jobsearch-chat-ajax.php',
            'jobsearch_ajax_url_emoji' => plugin_dir_url(__FILE__) . 'includes/jobsearch-chat-emoji-ajax.php',
            'jobsearch_ajax_client_auth' => plugin_dir_url(__FILE__) . 'includes/jobsearch-chat-auth-client.php',
            'jobsearch_plugin_url' => plugin_dir_url(__FILE__),
            'error_msg' => esc_html__('There is some problem.', 'jobsearch-ajchat'),
            'no_chat_message' => esc_html__('There are no messages in this chat yet', 'jobsearch-ajchat'),
            'online' => esc_html__('Online', 'jobsearch-ajchat'),
            'offline' => esc_html__('Offline', 'jobsearch-ajchat'),
            'is_admin' => is_admin(),
            'pusher_auth' => $php_pusher_auth,
            'pusher_cluster' => $php_pusher_cluster,
            'del_message' => esc_html__('Delete', 'jobsearch-ajchat'),
            'is_seen' => esc_html__('Seen', 'jobsearch-ajchat'),
            'is_today' => esc_html__('Today at', 'jobsearch-ajchat'),
            'del_full_message' => esc_html__('The message has been deleted.', 'jobsearch-ajchat'),
            'loading' => esc_html__('Loading', 'jobsearch-ajchat'),
            'chat_enable_msg' => esc_html__('Chat is enabled now', 'jobsearch-ajchat'),
            'chat_disable_msg' => esc_html__('Chat is disabled now', 'jobsearch-ajchat'),
        );
        wp_localize_script('jobsearch-chat-app', 'jobsearch_ajchat_vars', $jobsearch_plugin_arr);
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
            $this->admin_notices[] = '<div class="error">' . __('<em><b>Addon Jobsearch Chat</b></em> needs the <b>WP Jobsearch</b> plugin. Please install and activate it.', 'jobsearch-ajchat') . '</div>';
        }
        if (!$_is_active) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            deactivate_plugins(plugin_basename(__FILE__));
            $result = false;
        }
        return $result;
    }

    public function notices_callback()
    {
        foreach ($this->admin_notices as $value) {
            echo $value;
        }
    }
}

new Addon_Jobsearch_Chat();