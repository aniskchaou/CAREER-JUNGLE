<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Careerfy Demo Data
 * Plugin URI:        http://eyecix.com/demo/careerfy/
 * Description:       Careerfy Demo Data is a supporting plugin.
 * Version:           2.5
 * Author:            Eyecix
 * Author URI:        http://eyecix.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       careerfy-demo
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Retrieve the root url of the plugin.
 *
 */
function careerfy_demo_data_get_url($path = '') {
    return plugin_dir_url(__FILE__) . $path;
}

/**
 * Retrieve the root path of the plugin.
 *
 */
function careerfy_demo_data_get_path($path = '') {
    return plugin_dir_path(__FILE__) . $path;
}

