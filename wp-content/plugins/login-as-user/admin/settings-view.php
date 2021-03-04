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
# Last modified: 09 Jan 2021, 02:18:11
========================================================= */

// Settings page
?>
<div class="wrap">
	<h1><?php echo $this->plugin_name; ?> v<?php echo $this->version; ?></h1>
    <div class="lau-settings">
        <div class="lau-about">
            <h2>
                <?php echo esc_html__( 'About Login as User', 'login-as-user' ); ?>
            </h2>

            <div style="margin-top: 20px; overflow:hidden;">
                <img class="lau-product-img" src="<?php echo esc_url( plugins_url( 'img', (__FILE__) ) ); ?>/login-as-user-wordpress-plugin-120x200.png" alt="Login as User WordPress plugin by Web357" />
                <p>The Login as a User WordPress plugin allows admins to have easy access to the frontend as a specific user and thus solve problems or provide better and faster customer support. With one click, the admin logs in as the external user or customer and handles any situation without wasting any time at all. If you want a WordPress plugin to switch accounts in an instant, Login as User is for you. <a href="https://www.web357.com/product/login-as-user-wordpress-plugin?utm_source=SettingsPage&utm_medium=ReadMoreLink&utm_content=loginasuserwp&utm_campaign=read-more" target="_blank">Read more &raquo;</a></p>
                
            </div>

            <div class="lau-free-vs-pro" style="margin-top: 20px;">
            <hr> 
                <h4>Free vs Pro</h4>
                <table>
                    <tr>
                        <th>Feature</th>
                        <th>Free</th>
                        <th>Pro</th>
                    </th>
                    <tr>
                        <td class="lau-feature-info" title="The button is displayed in the Users page (Users > All Users).">Users page</td>
                        <td><span class="lau-icon lau-icon-tick"></span></td>
                        <td><span class="lau-icon lau-icon-tick"></span></td>
                    </tr>
                     <tr>
                        <td class="lau-feature-info" title="The button is displayed in User's profile page (Users > All Users > User), at the top left.">User's profile page</td>
                        <td><span class="lau-icon lau-icon-tick"></span></td>
                        <td><span class="lau-icon lau-icon-tick"></span></td>
                    </tr>
                    <tr>
                        <td class="lau-feature-info" title="The button is displayed in the (WooCommerce > Orders) page.">WooCommerce Orders page</td>
                        <td><span class="lau-icon lau-icon-x"></span></td>
                        <td><span class="lau-icon lau-icon-tick"></span></td>
                    </tr>
                    <tr>
                        <td class="lau-feature-info" title="The button is displayed in the (WooCommerce > Orders > Order details) page, at the right sidebar as a metabox.">WooCommerce Order details page</td>
                        <td><span class="lau-icon lau-icon-x"></span></td>
                        <td><span class="lau-icon lau-icon-tick"></span></td>
                    </tr>
                    <tr>
                        <td class="lau-feature-info" title="The button is displayed in the (WooCommerce > Subscriptions) page.">WooCommerce Orders page</td>
                        <td><span class="lau-icon lau-icon-x"></span></td>
                        <td><span class="lau-icon lau-icon-tick"></span></td>
                    </tr>
                     <tr>
                        <td class="lau-feature-info" title="The button is displayed in the (WooCommerce > Subscriptions > Subscription details) page, at the right sidebar as a metabox.">WooCommerce Subscription details page</td>
                        <td><span class="lau-icon lau-icon-x"></span></td>
                        <td><span class="lau-icon lau-icon-tick"></span></td>
                    </tr>
                </table>

                <div class="lac-buy-pro-btn-container">
                    <a href="https://www.web357.com/product/login-as-user-wordpress-plugin?utm_source=SettingsPage&utm_medium=BuyProLink&utm_content=loginasuserwp&utm_campaign=upgrade-pro" class="button lac-buy-pro-btn" target="_blank">Buy PRO version</a>
                </div>
                
            </div>

            <div style="margin-top: 20px;">
            <hr> 
                <h4><?php echo esc_html__( 'Need support?', 'login-as-user'); ?></h4>
                <?php
                echo sprintf(
                    __( '<p>If you are having problems with this plugin, please <a href="%1$s">contact us</a> and we will reply as soon as possible.</p>', 'login-as-user' ),
                    esc_url( 'https://www.web357.com/support' )
                );
                ?>
            </div>

            <div style="margin-top: 20px;" class="lac-developed-by">
            <hr> 
                <span><?php echo __('Developed by', 'login-as-user'); ?></span>
                <a href="<?php echo esc_url('https://www.web357.com/'); ?>" target="_blank">
                    <img src="<?php echo esc_url( plugins_url( 'img', (__FILE__) ) ); ?>/web357-logo.png" alt="Web357 logo" />
                </a>
            </div>

        </div>
        <div class="lau-form">
            <h2>
                <?php echo esc_html__( 'How it works?', 'login-as-user' ); ?>
            </h2>
            <?php echo wp_kses( __( '<p style="color:red">You have to navigate to the <a href="users.php"><strong>Users page</strong></a> and then you will see a button with the name "<strong>Login as: `username`</strong>", at the right side of each username. If you click on this button you will login at the front-end of the website as this User.</p>', 'login-as-user' ), array( 'strong' => array(), 'br' => array(), 'p' => array(), 'a' => array('href'=>array()) ) ); ?>

            <h2 style="margin-top: 40px;">
                <?php echo esc_html__( 'Settings', 'login-as-user' ); ?>
            </h2>
            <form action="options.php" method="post">
                <?php settings_fields( 'login-as-user' ); ?>
                <?php do_settings_sections( 'login-as-user' ); ?>
                <?php submit_button( esc_html__( 'Save Settings', 'login-as-user' ) ); ?>
            </form>
        </div>
    </div>
</div>