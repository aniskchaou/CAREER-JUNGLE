=== Login as User ===
Contributors: yiannistaos, johnbillion
Tags: user, login, admin, login as user, web357
Donate link: https://www.paypal.me/web357
Requires at least: 5.3
Tested up to: 5.6
Requires PHP: 7.0
Stable tag: 1.3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Login as User is a free WordPress plugin that helps admins switch user accounts instantly to check data.

== Description ==
[Login as a User WordPress plugin](https://www.web357.com/product/login-as-user-wordpress-plugin) allows admins to have easy access to the frontend as a specific user and thus solve problems or provide better and faster customer support. With one click, the admin logs in as the external user or customer and handles any situation without wasting any time at all. If you want a WordPress plugin to switch accounts in an instant, Login as User is for you.

== Video Description ==

[youtube https://www.youtube.com/watch?v=OrptAiAQo04]

== Demo ==

[Try Demo](https://demo.web357.com/wordpress/login-as-user/wp-admin/)
`
    Username: demo
    Password: demo
`

== Basic Features ==
- In the Admin area, you select Users in the left-hand side menu and click All Users in the sub-menu. Now, all users of your website appear on the screen along with the Login as… button besides each name. You can click the button of the user you want to switch account.
- Are you in a user’s profile and want to login as this user? Just click the button Login as:… at the top left-hand side and you will be able to check data and help this specific user with any problem.
- You can choose the position of the "Login as user" toolbar. There are two available positions, the top and the bottom.

== PRO Features ==
- Are you using the WooCommerce plugin? In the WooCommerce orders page, the Login as user button appears besides each customer to help you provide better customer support.
- Is one of your customers having trouble with their order? Do you want to check the details of a customer’s order? You can easily check the customer’s problem from his/her perspective by switching with the Login as User button in the WooCommerce order details page.

== Installation ==
The plugin is simple to install:

1. Download the file `login-as-user.zip`.
2. Unzip it.
3. Upload `login-as-user` directory to your `/wp-content/plugins` directory.
4. Go to the plugin management page and enable the plugin.
5. Configure the options from the `Settings > Login as User` page

== Frequently Asked Questions ==
= Why would I want to use this plugin? =

This plugin is helpful if you are an admin and you want to sign in as any user, or if you would like to check and confirm if the users see the correct data into their account page. This plugin prevent admins to ask for login details (username and password).

== Screenshots ==
1. In the Admin area, you select Users in the left-hand side menu and click All Users in the sub-menu. Now, all users of your website appear on the screen along with the Login as… button besides each name. You can click the button of the user you want to switch account.
2. Are you in a user’s profile and want to login as this user? Just click the button Login as:… at the top left-hand side and you will be able to check data and help this specific user with any problem.
3. Are you using the WooCommerce plugin? In the WooCommerce orders page, the Login as user button appears besides each customer to help you provide better customer support.
4. Is one of your customers having trouble with their order? Do you want to check the details of a customer’s order? You can easily check the customer’s problem from his/her perspective by switching with the Login as User button in the WooCommerce order details page.
5. Settings.

== Changelog ==
= 09-Jan-2021 : v1.3.0 =
* [Compatibility] Fully compatible with Loco Translate plugin.
* [New feature] Validate website license key in the plugin settings  (only for the premium version).
* [Compatibility] Fully compatible with WordPress v5.6 and WooCommerce v4.8.

= 19-Oct-2020 : v1.2.2 =
* [New option] You can now choose the position of the "Login as user" toolbar. There are two available positions, the TOP and the BOTTOM.
* [Compatibility] Fully compatible with WordPress 5.5 and WooCommerce 4.6.

= 15-Apr-2020 : v1.2.1 =
* [Bug Fixed] Error with the redirection URL after a successful login attempt. Admin is redirected to a URL that contains twice the value of the function "home_url()", and gives a 404 error.

= 30-Mar-2020 : v1.2.0 =
* [Style Improvement] Remove any margin of the button that displayed on the toolbar at frontend.
* [Bug Fixed] The "login as user" button is not displayed. There was a filter conflict because a 3rd plugin calls the "manage_users_custom_column" filter too. We have change the priority and the issue has been resolved. [Many thanks to Michael Kuhlman for his help]
* [Bug Fixed] Error with the redirection URL. Admin is not redirected to the correct page from settings, after logged in as a user.

= 14-Feb-2020 : v1.1.0 =
* [New feature] You can now choose from the settings, which string will be displayed on the "Login as User" button. You can choose one from the following, nickname, or first name, or last name, or full name. For example Login as «Yiannis», or log in as «Christodoulou», or log in as «Johnathan99», or log in as «Yiannis Christodoulou».
* [New Feature] Show only the first X characters of the username, or first/last name, or full name, on the "Login as...«option»" button. For example, if you choose 5, the button will be displayed like this: Login as «Yiann...», or Login as «Chris...», or Login as «Johna...», or Login as «Yiann...».
* [Bug Fixed] Do not load the files "public.min.css" and "public.min.js" if the Login as User functionality is not enabled.
* Minor bug fixes and improvements.

= 29-Jul-2019 : v1.0.1 =
* If an admin is already logged in, a short message replaces the button. Example: "Already logged in" instead of "---".
* The language files have been updated with new strings.

= 11-Jul-2019 : v1.0.0 =
* First beta release