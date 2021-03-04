<?php
/* ======================================================
# Login as User WordPress Plugin! - v1.3.0 (Free)
# -------------------------------------------------------
# For WordPress
# Author: Web357
# Copyright (©) 2009-2021 Web357. All rights reserved.
# License: GPLv2 or later, http://www.gnu.org/licenses/gpl-2.0.html
# Website: https:/www.web357.com/
# Demo: https://demo.web357.com/wordpress/login-as-user
# Support: support@web357.com
# Last modified: 09 Jan 2021, 02:18:12
========================================================= */

/**
 * Plugin Name:       Login as User
 * Plugin URI:        https://www.web357.com/product/login-as-user-wordpress-plugin
 * Description:       Login as User is a free WordPress plugin that helps admins switch user accounts instantly to check data.
 * Version:           1.3.0
 * Author:            Web357
 * Author URI:        https://www.web357.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       login-as-user
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
if ( !defined( 'LOGINASUSER_VERSION' ) ) {
	define( 'LOGINASUSER_VERSION', '1.3.0' );
}

/**
 * The code that runs during plugin activation.
 */
function activate_LoginAsUser() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-activator.php';
	LoginAsUser_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_LoginAsUser() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-deactivator.php';
	LoginAsUser_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_LoginAsUser' );
register_deactivation_hook( __FILE__, 'deactivate_LoginAsUser' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-main.php';

/**
 * Begins execution of the plugin.
 */
function run_LoginAsUser() 
{
	$plugin = new LoginAsUser();
	$plugin->run();
}
run_LoginAsUser();

// Load the main functionality of plugin
require_once (plugin_dir_path( __FILE__ ) . 'includes/class-w357-login-as-user.php');