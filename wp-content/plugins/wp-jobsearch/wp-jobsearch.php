<?php

/**
 * WP JobSearch
 *
 * @package     wp-jobsearch
 * @author      Eyecix
 * @copyright   2018 Eyecix
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:       WP JobSearch
 * Plugin URI:        http://eyecix.com/plugins/jobsearch/
 * Description:       WP JobSearch plugin is a complete recruitment solution.
 * Version:           1.6.7
 * Author:            Eyecix
 * Author URI:        http://themeforest.net/user/eyecix/portfolio
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-jobsearch
 * Domain Path:       /languages
 */
if (!defined('WPINC')) {
    die;
}

add_action('init', 'jobsearch_create_user_roles', 5);

function jobsearch_create_user_roles()
{
    // create user role for user
    add_role(
        'jobsearch_candidate', esc_html('JobSearch Candidate'), array(
            'read' => false,
            'edit_posts' => false,
            'delete_posts' => false,
        )
    );
    add_role(
        'jobsearch_employer', esc_html('JobSearch Employer'), array(
            'read' => false,
            'edit_posts' => false,
            'delete_posts' => false,
        )
    );
    add_role(
        'jobsearch_empmnger', esc_html('Employer Manager'), array(
            'read' => false,
            'edit_posts' => false,
            'delete_posts' => false,
        )
    );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-activator.php
 */
function activate_jobsearch_plugin()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-activator.php';
    JobSearch_plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-deactivator.php
 */
function deactivate_jobsearch_plugin()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-deactivator.php';
    JobSearch_plugin_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_jobsearch_plugin');
register_deactivation_hook(__FILE__, 'deactivate_jobsearch_plugin');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-jobsearch-plugin.php';

/**
 * Retrieve the root url of the plugin.
 *
 */
function jobsearch_plugin_get_url($path = '')
{
    return plugin_dir_url(__FILE__) . $path;
}

/**
 * Retrieve the root path of the plugin.
 *
 */
function jobsearch_plugin_get_path($path = '')
{
    return plugin_dir_path(__FILE__) . $path;
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_jobsearch_plugin()
{
    //error_reporting(E_ALL);
    $plugin = new JobSearch_plugin();
}



run_jobsearch_plugin();
