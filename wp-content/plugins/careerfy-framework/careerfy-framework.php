<?php


/**
 * @wordpress-plugin
 * Plugin Name:       Careerfy Framework
 * Plugin URI:        http://eyecix.com/demo/careerfy/
 * Description:       Careerfy Framework is a supporting plugin.
 * Version:           5.5.0
 * Author:            Eyecix
 * Author URI:        http://eyecix.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       careerfy-frame
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-careerfy-framework.php';

/**
 * Retrieve the root url of the plugin.
 *
 */
function careerfy_framework_get_url($path = '') {
    return plugin_dir_url(__FILE__) . $path;
}

/**
 * Retrieve the root path of the plugin.
 *
 */
function careerfy_framework_get_path($path = '') {
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
function run_careerfy_framework() {
    $plugin = new Careerfy_framework();
}

run_careerfy_framework();

