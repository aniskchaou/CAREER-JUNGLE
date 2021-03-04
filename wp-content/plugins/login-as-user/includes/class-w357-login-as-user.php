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

class w357LoginAsUser
{
	/**
	 * Sets up all the filters and actions.
	 */
	public function run()
	{
		add_filter('user_has_cap', array($this, 'filter_user_has_cap'), 10, 4);
		add_filter('map_meta_cap', array($this, 'filter_map_meta_cap'), 10, 4);
		add_action('init', array($this, 'action_init'));
		add_action('wp_logout', array($this, 'login_as_user_clear_olduser_cookie'));
		add_action('wp_login', array($this, 'login_as_user_clear_olduser_cookie'));
		add_filter('wp_head', array($this, 'filter_login_message'), 1);
		add_filter('removable_query_args', array($this, 'filter_removable_query_args'));
		add_filter('manage_users_columns', array($this, 'loginasuser_col'), 1000);
		add_filter('manage_users_custom_column', array($this, 'loginasuser_col_content'), 15, 3);
		add_action('personal_options', array($this, 'w357_personal_options'));
		add_action('admin_print_styles', array($this, 'loginasuser_col_style'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_filter('login_redirect', array($this,'login_redirect'), 20, 3 );
		add_filter('manage_edit-shop_order_columns', array($this, 'loginasuser_col'), 1000);
		add_filter('manage_edit-shop_subscription_columns', array($this, 'loginasuser_col'), 1000);
		add_action('manage_shop_order_posts_custom_column', array($this, 'loginasuser_woo_col_content'));
		add_action('manage_shop_subscription_posts_custom_column', array($this, 'loginasuser_woo_col_content'));
		add_action('add_meta_boxes', array($this, 'add_login_as_user_metabox'));
	}

	public function login_redirect($redirect_to, $requested, $user)
	{
		if (!isset($_REQUEST['action'])) 
		{
			return $redirect_to;
		}
		
		if ($_REQUEST['action'] != 'login_as_user' && $_REQUEST['action'] != 'login_as_olduser') 
		{
			return $redirect_to;
		}
    }

	/**
	 * Returns whether or not the current logged in user is being remembered in the form of a persistent browser cookie
	 * (ie. they checked the 'Remember Me' check box when they logged in). This is used to persist the 'remember me'
	 * value when the user switches to another user.
	 *
	 * @return bool Whether the current user is being 'remembered' or not.
	 */
	public static function remember_me()
	{
		/** This filter is documented in wp-includes/pluggable.php */
		$cookie_life = apply_filters('auth_cookie_expiration', 259200, get_current_user_id(), false);
		$current     = wp_parse_auth_cookie('', 'logged_in');

		// Here we calculate the expiration length of the current auth cookie and compare it to the default expiration.
		// If it's greater than this, then we know the user checked 'Remember Me' when they logged in.
		return (($current['expiration'] - time()) > $cookie_life);
	}

	/**
	 * Loads localisation files and routes actions depending on the 'action' query var.
	 */
	public function action_init()
	{
		if (!isset($_REQUEST['action'])) {
			return;
		}

		$current_user = (is_user_logged_in()) ? wp_get_current_user() : null;

		switch ($_REQUEST['action']) {

				// We're attempting to switch to another user:
			case 'login_as_user':
				if (isset($_REQUEST['user_id'])) {
					$user_id = absint($_REQUEST['user_id']);
				} else {
					$user_id = 0;
				}

				// Check authentication:
				if (!current_user_can('login_as_user', $user_id)) {
					wp_die(esc_html__('Could not login as user.', 'login-as-user'));
				}

				// Check intent:
				check_admin_referer("login_as_user_{$user_id}");

				// Switch user:
				$user = $this->login_as_user($user_id, self::remember_me());
				if ($user) {
					$redirect_to = self::get_redirect($user, $current_user);

					// Redirect to the dashboard or the home URL depending on capabilities:
					$args = array(
						'logged_in_as_user' => 'true',
					);

					if ($redirect_to) 
					{
						// check if the home url exists in redirect to
						if (strpos($redirect_to, home_url('/')) !== false) 
						{
							wp_safe_redirect(add_query_arg($args, $redirect_to), 302, 'Login as User - WordPress Plugin');
						}
						else
						{
							wp_safe_redirect(add_query_arg($args, home_url('/') . $redirect_to), 302, 'Login as User - WordPress Plugin');
						}
					} 
					elseif (!current_user_can('read')) 
					{
						wp_safe_redirect(add_query_arg($args, home_url('/')), 302, 'Login as User - WordPress Plugin');
					} 
					else 
					{
						$options = (object) get_option( 'login_as_user_options' );
						$redirect_to = (!empty($options->redirect_to)) ? home_url('/') . $options->redirect_to : home_url('/');
						wp_safe_redirect(add_query_arg($args, $redirect_to), 302, 'Login as User - WordPress Plugin');
					}
					exit;
				} else {
					wp_die(esc_html__('Could not login as user.', 'login-as-user'));
				}
				break;

				// We're attempting to switch back to the originating user:
			case 'login_as_olduser':
				// Fetch the originating user data:
				$old_user = $this->get_old_user();
				if (!$old_user) {
					wp_die(esc_html__('Could not login as user.', 'login-as-user'));
				}

				// Check authentication:
				if (!self::authenticate_old_user($old_user)) {
					wp_die(esc_html__('Could not login as user.', 'login-as-user'));
				}

				// Check intent:
				check_admin_referer("login_as_olduser_{$old_user->ID}");

				// Switch user:
				if ($this->login_as_user($old_user->ID, self::remember_me(), false)) {

					if (!empty($_REQUEST['interim-login'])) {
						$GLOBALS['interim_login'] = 'success'; // @codingStandardsIgnoreLine
						login_header('', '');
						exit;
					}

					$redirect_to = self::get_redirect($old_user, $current_user);
					$args        = array(
						'logged_in_as_user' => 'true',
					);

					if ($redirect_to) {
						wp_safe_redirect(add_query_arg($args, $redirect_to), 302, 'Login as User - WordPress Plugin');
					} else {
						// redirect the user to the correct page
						$login_as_user_get_back_url_cookie = $this->login_as_user_get_back_url_cookie();
						$back_url = (!empty($login_as_user_get_back_url_cookie)) ? urldecode($login_as_user_get_back_url_cookie) : admin_url('users.php');
						wp_safe_redirect(add_query_arg($args, $back_url), 302, 'Login as User - WordPress Plugin');
					}
					exit;
				} else {
					wp_die(esc_html__('Could not switch users.', 'login-as-user'));
				}
				break;
		}
	}

	/**
	 * Fetches the URL to redirect to for a given user (used after switching).
	 *
	 * @param  WP_User $new_user Optional. The new user's WP_User object.
	 * @param  WP_User $old_user Optional. The old user's WP_User object.
	 * @return string The URL to redirect to.
	 */
	protected static function get_redirect(WP_User $new_user = null, WP_User $old_user = null)
	{
		if (!empty($_REQUEST['redirect_to'])) {
			$redirect_to           = self::remove_query_args(wp_unslash($_REQUEST['redirect_to']));
			$requested_redirect_to = wp_unslash($_REQUEST['redirect_to']);
		} else {
			$redirect_to           = '';
			$requested_redirect_to = '';
		}

		if (!$new_user) {
			/** This filter is documented in wp-login.php */
			$redirect_to = apply_filters('logout_redirect', $redirect_to, $requested_redirect_to, $old_user);
		} else {
			/** This filter is documented in wp-login.php */
			$redirect_to = apply_filters('login_redirect', $redirect_to, $requested_redirect_to, $new_user);
		}

		return $redirect_to;
	}

	/**
	 * Validates the old user cookie and returns its user data.
	 *
	 * @return false|WP_User False if there's no old user cookie or it's invalid, WP_User object if it's present and valid.
	 */
	public function get_old_user()
	{
		$cookie = $this->login_as_user_get_olduser_cookie();
		if (!empty($cookie)) {
			$old_user_id = wp_validate_auth_cookie($cookie, 'logged_in');

			if ($old_user_id) {
				return get_userdata($old_user_id);
			}
		}
		return false;
	}

	/**
	 * Authenticates an old user by verifying the latest entry in the auth cookie.
	 *
	 * @param WP_User $user A WP_User object (usually from the logged_in cookie).
	 * @return bool Whether verification with the auth cookie passed.
	 */
	public function authenticate_old_user(WP_User $user)
	{
		$cookie = $this->login_as_user_get_auth_cookie();
		if (!empty($cookie)) {
			if (self::secure_auth_cookie()) {
				$scheme = 'secure_auth';
			} else {
				$scheme = 'auth';
			}

			$old_user_id = wp_validate_auth_cookie(end($cookie), $scheme);

			if ($old_user_id) {
				return ($user->ID === $old_user_id);
			}
		}
		return false;
	}

	/**
	 * Adds a 'Switch back to {user}' link to the WordPress frontend
	 *
	 * @param  string $message The login screen message.
	 * @return string The login screen message.
	 */
	public function filter_login_message($message)
	{
		$options = (object) get_option( 'login_as_user_options' );
		$login_as_user_toolbar_position_option = (!empty($options->login_as_user_toolbar_position)) ? $options->login_as_user_toolbar_position : 'top';
		$old_user = $this->get_old_user();

		if ($old_user instanceof WP_User) {
			$link = sprintf(
				/* Translators: 1: user display name; 2: username; */
				__('go back to admin as %1$s (%2$s)', 'login-as-user'),
				$old_user->display_name,
				$old_user->user_login
			);
			$url = self::back_url($old_user);

			if (!empty($_REQUEST['interim-login'])) {
				$url = add_query_arg(array(
					'interim-login' => '1',
				), $url);
			} elseif (!empty($_REQUEST['redirect_to'])) {
				$url = add_query_arg(array(
					'redirect_to' => urlencode(wp_unslash($_REQUEST['redirect_to'])),
				), $url);
			}

			$current_user = (is_user_logged_in()) ? wp_get_current_user() : null;
			$current_user_name = sprintf(
				/* Translators: 1: user display name; 2: username; */
				__('%1$s (%2$s)', 'login-as-user'),
				$current_user->display_name,
				$current_user->user_login
			);

			$toolbar_position = $login_as_user_toolbar_position_option; // top or bottom
			if (is_admin_bar_showing() && $toolbar_position == 'top') 
			{
				$css = <<<CSS
				body { 
					margin-top: 60px !important; 
					padding-top: 70px !important; 
				}
				.login-as-user-top { 
					top: 32px!important; 
				}
				@media only screen and (max-width: 782px) {
					.login-as-user-top { 
						top: 46px!important; 
					}
				}
CSS;
			}
			elseif (is_admin_bar_showing() && $toolbar_position == 'bottom') 
			{
				$css = <<<CSS
				body { 
					margin-bottom: 60px !important; 
					padding-bottom: 70px !important; 
				}
				.login-as-user-bottom { 
					bottom: 0; 
				}
CSS;
			} 
			elseif ( $toolbar_position == 'top') 
			{
				$css = <<<CSS
				body { 
					margin-top: 60px !important; 
					padding-top: 70px !important; 
				}
				.login-as-user-top { 
					top: 32px!important; 
				}
				@media only screen and (max-width: 782px) {
					.login-as-user-top { 
						top: 46px!important; 
					}
				}
CSS;
			}
			elseif ($toolbar_position == 'bottom') 
			{
				$css = <<<CSS
				body { 
					margin-bottom: 60px !important; 
					padding-bottom: 70px !important; 
				}
				.login-as-user-bottom { 
					bottom: 0; 
				}
CSS;
			} 
			else 
			{
				$css = <<<CSS
				body { 
					padding-top: 70px !important; 
				}
				@media only screen and (max-width: 420px) {
					body { 
						padding-top: 120px !important; 
					}
				}
CSS;
			}

			// Load the css and js files only if the login as user functionality is enabled
			wp_enqueue_style( 'login-as-user', plugin_dir_url( dirname( __FILE__ ) ) . 'public/css/public.min.css', array(), LOGINASUSER_VERSION, 'all' );
			wp_enqueue_script( 'login-as-user', plugin_dir_url( dirname( __FILE__ ) ) . 'public/js/public.min.js', array( 'jquery' ), LOGINASUSER_VERSION, false );

			// Inline CSS
			wp_add_inline_style('login-as-user-inline-style', $css);

			// add the class to the body
			add_filter( 'body_class', array($this, 'addBodyClass'), 10, 3);

			// output
			echo  '<div class="login-as-user login-as-user-'.$toolbar_position.'">'; // login-as-user-top or login-as-user-bottom
			echo  '<div class="login-as-user-inner">';
			echo  '<div class="login-as-user-content">';
			echo  '<div class="login-as-user-msg">'.sprintf(__('You have been logged in as the user <strong>%1$s</strong>', 'login-as-user'), esc_html__($current_user_name)).'</div>';
			echo  '<a class="button w357-login-as-user-btn" href="' . esc_url($url) . '">' . esc_html($link) . '</a>';
			echo  '</div>';
			echo  '</div>';
			echo  '</div>';
		}
	}

	public function addBodyClass( $classes ) 
	{
		$classes[] = 'admin-has-been-logged-in-as-a-user';
		return $classes;
	}

	public function enqueue_styles()
	{
		wp_register_style('login-as-user-inline-style', false);
		wp_enqueue_style('login-as-user-inline-style');
	}

	/**
	 * Filters the list of query arguments which get removed from admin area URLs in WordPress.
	 *
	 * @link https://core.trac.wordpress.org/ticket/23367
	 *
	 * @param string[] $args List of removable query arguments.
	 * @return string[] Updated list of removable query arguments.
	 */
	public function filter_removable_query_args(array $args)
	{
		return array_merge($args, array('logged_in_as_user'));
	}

	/**
	 * Returns the switch to or switch back URL for a given user.
	 *
	 * @param  WP_User $user The user to be switched to.
	 * @return string|false The required URL, or false if there's no old user or the user doesn't have the required capability.
	 */
	public function build_the_login_as_user_url(WP_User $user)
	{
		$old_user = $this->get_old_user();

		if ($old_user && ($old_user->ID === $user->ID)) {
			return self::back_url($old_user);
		} elseif (current_user_can('login_as_user', $user->ID)) {
			return self::loginasuser_url($user);
		} else {
			return false;
		}
	}

	/**
	 * Returns the nonce-secured URL needed to switch to a given user ID.
	 *
	 * @param  WP_User $user The user to be switched to.
	 * @return string The required URL.
	 */
	public static function loginasuser_url(WP_User $user)
	{
		$current_url = urlencode(wp_unslash("//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"));

		return wp_nonce_url(add_query_arg(array(
			'action'  => 'login_as_user',
			'user_id' => $user->ID,
			'back_url' => $current_url,
		), wp_login_url()), "login_as_user_{$user->ID}");
	}

	/**
	 * Returns the nonce-secured URL needed to switch back to the originating user.
	 *
	 * @param  WP_User $user The old user.
	 * @return string        The required URL.
	 */
	public static function back_url(WP_User $user)
	{
		return wp_nonce_url(add_query_arg(array(
			'action' => 'login_as_olduser',

		), wp_login_url()), "login_as_olduser_{$user->ID}");
	}

	/**
	 * Returns the current URL.
	 *
	 * @return string The current URL.
	 */
	public static function current_url()
	{
		return (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; // @codingStandardsIgnoreLine
	}

	/**
	 * Removes a list of common confirmation-style query args from a URL.
	 *
	 * @param  string $url A URL.
	 * @return string The URL with query args removed.
	 */
	public static function remove_query_args($url)
	{
		if (function_exists('wp_removable_query_args')) {
			$url = remove_query_arg(wp_removable_query_args(), $url);
		}

		return $url;
	}

	/**
	 * Returns whether or not User Switching's equivalent of the 'logged_in' cookie should be secure.
	 *
	 * This is used to set the 'secure' flag on the old user cookie, for enhanced security.
	 *
	 * @link https://core.trac.wordpress.org/ticket/15330
	 *
	 * @return bool Should the old user cookie be secure?
	 */
	public static function secure_olduser_cookie()
	{
		return (is_ssl() && ('https' === parse_url(home_url(), PHP_URL_SCHEME)));
	}

	public static function secure_back_url_cookie()
	{
		return (is_ssl() && ('https' === parse_url(home_url(), PHP_URL_SCHEME)));
	}

	/**
	 * Returns whether or not User Switching's equivalent of the 'auth' cookie should be secure.
	 *
	 * This is used to determine whether to set a secure auth cookie or not.
	 *
	 * @return bool Should the auth cookie be secure?
	 */
	public static function secure_auth_cookie()
	{
		return (is_ssl() && ('https' === parse_url(wp_login_url(), PHP_URL_SCHEME)));
	}

	/**
	 * Instructs WooCommerce to forget the session for the current user, without deleting it.
	 *
	 * @param WooCommerce $wc The WooCommerce instance.
	 */
	public static function forget_woocommerce_session(WooCommerce $wc)
	{
		if (!property_exists($wc, 'session')) {
			return false;
		}

		if (!method_exists($wc->session, 'forget_session')) {
			return false;
		}

		$wc->session->forget_session();
	}

	/**
	 * Filters a user's capabilities so they can be altered at runtime.
	 *
	 * This is used to:
	 *  - Grant the 'login_as_user' capability to the user if they have the ability to edit the user they're trying to
	 *    switch to (and that user is not themselves).
	 *  - Grant the 'switch_off' capability to the user if they can edit other users.
	 *
	 * Important: This does not get called for Super Admins. See filter_map_meta_cap() below.
	 *
	 * @param bool[]   $user_caps     Array of key/value pairs where keys represent a capability name and boolean values
	 *                                represent whether the user has that capability.
	 * @param string[] $required_caps Required primitive capabilities for the requested capability.
	 * @param array    $args {
	 *     Arguments that accompany the requested capability check.
	 *
	 *     @type string    $0 Requested capability.
	 *     @type int       $1 Concerned user ID.
	 *     @type mixed  ...$2 Optional second and further parameters.
	 * }
	 * @param WP_User  $user          Concerned user object.
	 * @return bool[] Concerned user's capabilities.
	 */
	public function filter_user_has_cap(array $user_caps, array $required_caps, array $args, WP_User $user)
	{
		if ('login_as_user' === $args[0]) 
		{
			$user_caps['login_as_user'] = (user_can($user->ID, 'edit_user', $args[2]) && ($args[2] !== $user->ID));
		}

		return $user_caps;
	}

	/**
	 * Filters the required primitive capabilities for the given primitive or meta capability.
	 *
	 * This is used to:
	 *  - Add the 'do_not_allow' capability to the list of required capabilities when a Super Admin is trying to switch
	 *    to themselves.
	 *
	 * It affects nothing else as Super Admins can do everything by default.
	 *
	 * @param string[] $required_caps Required primitive capabilities for the requested capability.
	 * @param string   $cap           Capability or meta capability being checked.
	 * @param int      $user_id       Concerned user ID.
	 * @param array    $args {
	 *     Arguments that accompany the requested capability check.
	 *
	 *     @type mixed ...$0 Optional second and further parameters.
	 * }
	 * @return string[] Required capabilities for the requested action.
	 */
	public function filter_map_meta_cap(array $required_caps, $cap, $user_id, array $args)
	{
		if (('login_as_user' === $cap) && ($args[0] === $user_id)) 
		{
			$required_caps[] = 'do_not_allow';
		}
		return $required_caps;
	}

	// Add a custom metabox only for shop_order and shop_subscription post types
	public function add_login_as_user_metabox()
	{
		add_meta_box( 'login_as_user_metabox', __( 'Login as User' ), array($this, 'login_as_user_metabox'), array('shop_order', 'shop_subscription'), 'side', 'low');
	}

	// Get the string type for the Login as ... button.
	public function login_as_type($user, $allow_trim_name = true)
	{
		$options = (object) get_option( 'login_as_user_options' );
		if (!empty($options->login_as_type))
		{
			switch ($options->login_as_type) 
			{
				case 'user_login':
					$login_as_type = esc_html__($user->user_login, 'login-as-user');
					break;
					
				case 'user_firstname':
					$login_as_type = (!empty($user->user_firstname)) ? esc_html__($user->user_firstname, 'login-as-user') : esc_html__($user->user_login, 'login-as-user');
					break;

				case 'user_lastname':
					$login_as_type = (!empty($user->user_lastname)) ? esc_html__($user->user_lastname, 'login-as-user') : esc_html__($user->user_login, 'login-as-user');
					break;

				case 'user_fullname':
					$login_as_type = (!empty($user->user_firstname) || !empty($user->user_lastname)) ? esc_html__($user->user_firstname . ' ' . $user->user_lastname, 'login-as-user') : esc_html__($user->user_login, 'login-as-user');
					break;
			
				default:
					$login_as_type = esc_html__($user->user_login, 'login-as-user');
					break;
			}
		}
		else
		{
			$login_as_type = esc_html__($user->user_login, 'login-as-user');
		}

		$login_as_type_characters_limit = (isset($options->login_as_type_characters_limit)) ? $options->login_as_type_characters_limit : 0;
		if (is_numeric($login_as_type_characters_limit) && $login_as_type_characters_limit > 0 && $allow_trim_name === TRUE)
		{
			if(strlen($login_as_type) > $login_as_type_characters_limit)
			{
				$login_as_type = trim(substr($login_as_type, 0, $login_as_type_characters_limit)) . '&hellip;';
			}
		}

		return $login_as_type;
	}

	public function login_as_user_metabox()
	{

		echo $this->onlyInProTextLink();
		
	}

	public function w357_personal_options( WP_User $user ) 
	{
		$login_as_user_url = $this->build_the_login_as_user_url($user);

		if (get_current_user_id() != $user->ID && !empty($user->user_login))
		{
			echo '<a class="button w357-login-as-user-btn" href="' . esc_url($login_as_user_url) . '" title="'.esc_html__('Login as', 'login-as-user').': ' . $this->login_as_type($user, false) . '"><span class="dashicons dashicons-admin-users"></span> '.esc_html__('Login as', 'login-as-user').': <strong>' . $this->login_as_type($user) . '</strong></a>';
		}
		else
		{
			if (!current_user_can('login_as_user', $user->ID)) {
				echo __('Could not login as this user.', 'login-as-user');
			}
			else
			{
				echo __('You are already logged in.', 'login-as-user');
			}
		}
	}

	public function loginasuser_col_content($val, $column_name, $user_id)
	{
		global $wpdb;
		switch ($column_name) {
			case 'loginasuser_col':
				$user = new WP_User($user_id);

				$login_as_user_url = $this->build_the_login_as_user_url($user);

				if (!current_user_can('login_as_user', $user_id)) {
					return __('Could not login as this user.', 'login-as-user');
				}
				
				if (!$login_as_user_url || empty($user->user_login)) 
				{
					return __('Already logged in.', 'login-as-user');
				}

				return '<a class="button w357-login-as-user-btn" href="' . esc_url($login_as_user_url) . '" title="'.esc_html__('Login as', 'login-as-user').': ' . $this->login_as_type($user, false) . '"><span class="dashicons dashicons-admin-users"></span> '.esc_html__('Login as', 'login-as-user').': <strong>' . $this->login_as_type($user) . '</strong></a>';
				break;
			default:
		}
		return $val;
	}

	public function loginasuser_woo_col_content($column)
	{
		if ('loginasuser_col' === $column) 
		{

			echo $this->onlyInProTextLink();
			
		}
	}

	function onlyInProTextLink()
	{
		echo '<a title="'.__('The Login as User functionality for WooCommerce is only available in the PRO version.', 'login-as-user').'" href="https://www.web357.com/product/login-as-user-wordpress-plugin?utm_source=buyprolink-loginasuserwp&utm_medium=CLIENT-WP-Backend-BuyProLink-Web357-loginasuserwp&utm_campaign=buyprolink-loginasuserwp#pricing" target="_blank"><small>Only in PRO version</small></a>';
	}

	/**
	 * Add extra column in users/woocommerce orders/subscriptions page.
	 */
	function loginasuser_col($columns)
	{
		$new_columns = array();

		foreach ($columns as $column_name => $column_info) {

			$new_columns[$column_name] = $column_info;

			if ('username' === $column_name || 'order_number' === $column_name || 'order_title' === $column_name) {
				$new_columns['loginasuser_col'] = __('Login as User', 'login-as-user');
			}
		}

		return $new_columns;
	}

	/**
	 * Adjusts the styles for the new column.
	 */
	function loginasuser_col_style()
	{
		$css = '.widefat .column-loginasuser_col { width: 20%; }';
		wp_add_inline_style('woocommerce_admin_styles', $css);
	}

	/**
	 * Sets authorisation cookies containing the originating user information.
	 *
	 * @since 1.4.0 The `$token` parameter was added.
	 *
	 * @param int    $old_user_id The ID of the originating user, usually the current logged in user.
	 * @param bool   $pop         Optional. Pop the latest user off the auth cookie, instead of appending the new one. Default false.
	 * @param string $token       Optional. The old user's session token to store for later reuse. Default empty string.
	 */
	public function login_as_user_set_olduser_cookie($old_user_id, $pop = false, $token = '')
	{
		$secure_auth_cookie    = w357LoginAsUser::secure_auth_cookie();
		$secure_olduser_cookie = w357LoginAsUser::secure_olduser_cookie();
		$secure_back_url_cookie = w357LoginAsUser::secure_back_url_cookie();
		$expiration            = time() + 259200; // 3 days
		$auth_cookie           = $this->login_as_user_get_auth_cookie();
		$olduser_cookie        = wp_generate_auth_cookie($old_user_id, $expiration, 'logged_in', $token);

		if ($secure_auth_cookie) {
			$auth_cookie_name = 'wp_loginasuser_secure_'.COOKIEHASH;
			$scheme           = 'secure_auth';
		} else {
			$auth_cookie_name = 'wp_loginasuser_'.COOKIEHASH;
			$scheme           = 'auth';
		}

		if ($pop) {
			array_pop($auth_cookie);
		} else {
			array_push($auth_cookie, wp_generate_auth_cookie($old_user_id, $expiration, $scheme, $token));
		}

		$auth_cookie = json_encode($auth_cookie);

		/** This filter is documented in wp-includes/pluggable.php */
		if (!apply_filters('send_auth_cookies', true)) {
			return;
		}

		setcookie($auth_cookie_name, $auth_cookie, $expiration, SITECOOKIEPATH, COOKIE_DOMAIN, $secure_auth_cookie, true);
		setcookie('wp_loginasuser_olduser_'.COOKIEHASH, $olduser_cookie, $expiration, COOKIEPATH, COOKIE_DOMAIN, $secure_olduser_cookie, true);
		$get_back_url = isset( $_GET['back_url'] ) ? esc_url_raw( $_GET['back_url'] ) : '';

		setcookie('wp_loginasuser_backurl_'.COOKIEHASH, $get_back_url, $expiration, COOKIEPATH, COOKIE_DOMAIN, $secure_back_url_cookie, true);
	}

	/**
	 * Clears the cookies containing the originating user, or pops the latest item off the end if there's more than one.
	 *
	 * @param bool $clear_all Optional. Whether to clear the cookies (as opposed to just popping the last user off the end). Default true.
	 */
	public function login_as_user_clear_olduser_cookie($clear_all = true)
	{
		$auth_cookie = $this->login_as_user_get_auth_cookie();
		if (!empty($auth_cookie)) {
			array_pop($auth_cookie);
		}
		if ($clear_all || empty($auth_cookie)) {
			/**
			 * Fires just before the user switching cookies are cleared.
			 *
			 * @since 1.4.0
			 */
			//do_action('clear_olduser_cookie');

			/** This filter is documented in wp-includes/pluggable.php */
			if (!apply_filters('send_auth_cookies', true)) {
				return;
			}

			$expire = time() - 31536000;
			setcookie('wp_loginasuser_'.COOKIEHASH,         ' ', $expire, SITECOOKIEPATH, COOKIE_DOMAIN);
			setcookie('wp_loginasuser_secure_'.COOKIEHASH,  ' ', $expire, SITECOOKIEPATH, COOKIE_DOMAIN);
			setcookie('wp_loginasuser_olduser_'.COOKIEHASH, ' ', $expire, COOKIEPATH, COOKIE_DOMAIN);
			setcookie('wp_loginasuser_backurl_'.COOKIEHASH, ' ', $expire, COOKIEPATH, COOKIE_DOMAIN);
		} else {
			if (w357LoginAsUser::secure_auth_cookie()) {
				$scheme = 'secure_auth';
			} else {
				$scheme = 'auth';
			}

			$old_cookie = end($auth_cookie);

			$old_user_id = wp_validate_auth_cookie($old_cookie, $scheme);
			if ($old_user_id) {
				$parts = wp_parse_auth_cookie($old_cookie, $scheme);
				$this->login_as_user_set_olduser_cookie($old_user_id, true, $parts['token']);
			}
		}
	}

	/**
	 * Gets the value of the cookie containing the originating user.
	 *
	 * @return string|false The old user cookie, or boolean false if there isn't one.
	 */
	public function login_as_user_get_olduser_cookie()
	{
		if (isset($_COOKIE['wp_loginasuser_olduser_'.COOKIEHASH])) {
			return wp_unslash($_COOKIE['wp_loginasuser_olduser_'.COOKIEHASH]);
		} else {
			return false;
		}
	}

	/**
	 * Gets the value of the cookie containing the originating user.
	 *
	 * @return string|false The old user cookie, or boolean false if there isn't one.
	 */
	public function login_as_user_get_back_url_cookie()
	{
		if (isset($_COOKIE['wp_loginasuser_backurl_'.COOKIEHASH])) {
			return wp_unslash($_COOKIE['wp_loginasuser_backurl_'.COOKIEHASH]);
		} else {
			return false;
		}
	}

	/**
	 * Gets the value of the auth cookie containing the list of originating users.
	 *
	 * @return string[] Array of originating user authentication cookie values. Empty array if there are none.
	 */
	public function login_as_user_get_auth_cookie()
	{
		if (w357LoginAsUser::secure_auth_cookie()) {
			$auth_cookie_name = 'wp_loginasuser_secure_'.COOKIEHASH;
		} else {
			$auth_cookie_name = 'wp_loginasuser_'.COOKIEHASH;
		}

		if (isset($_COOKIE[$auth_cookie_name]) && is_string($_COOKIE[$auth_cookie_name])) {
			$cookie = json_decode(wp_unslash($_COOKIE[$auth_cookie_name]));
		}
		if (!isset($cookie) || !is_array($cookie)) {
			$cookie = array();
		}
		return $cookie;
	}

	/**
	 * Switches the current logged in user to the specified user.
	 *
	 * @param  int  $user_id      The ID of the user to switch to.
	 * @param  bool $remember     Optional. Whether to 'remember' the user in the form of a persistent browser cookie. Default false.
	 * @param  bool $set_old_user Optional. Whether to set the old user cookie. Default true.
	 * @return false|WP_User WP_User object on success, false on failure.
	 */
	public function login_as_user($user_id, $remember = false, $set_old_user = true)
	{
		$user = get_userdata($user_id);

		if (!$user) 
		{
			return false;
		}

		$old_user_id  = (is_user_logged_in()) ? get_current_user_id() : false;
		$old_token    = function_exists('wp_get_session_token') ? wp_get_session_token() : '';
		$auth_cookie  = $this->login_as_user_get_auth_cookie();
		$cookie_parts = wp_parse_auth_cookie(end($auth_cookie));

		if ($set_old_user && $old_user_id) {
			// Switching to another user
			$new_token = '';
			$this->login_as_user_set_olduser_cookie($old_user_id, false, $old_token);
		} else {
			// Switching back, either after being switched off or after being switched to another user
			$new_token = isset($cookie_parts['token']) ? $cookie_parts['token'] : '';
			$this->login_as_user_clear_olduser_cookie(false);
		}

		/**
		 * Attaches the original user ID and session token to the new session when a user switches to another user.
		 *
		 * @param array $session Array of extra data.
		 * @param int   $user_id User ID.
		 * @return array Array of extra data.
		 */
		$session_filter = function (array $session, $user_id) use ($old_user_id, $old_token) {
			$session['logged_in_from_id']      = $old_user_id;
			$session['logged_in_from_session'] = $old_token;
			return $session;
		};

		add_filter('attach_session_information', $session_filter, 99, 2);

		wp_clear_auth_cookie();
		wp_set_auth_cookie($user_id, $remember, '', $new_token);
		wp_set_current_user($user_id);

		remove_filter('attach_session_information', $session_filter, 99);

		if ($set_old_user) {
			/**
			 * Fires when a user switches to another user account.
			 *
			 * @since 0.6.0
			 * @since 1.4.0 The `$new_token` and `$old_token` parameters were added.
			 *
			 * @param int    $user_id     The ID of the user being switched to.
			 * @param int    $old_user_id The ID of the user being switched from.
			 * @param string $new_token   The token of the session of the user being switched to. Can be an empty string
			 *                            or a token for a session that may or may not still be valid.
			 * @param string $old_token   The token of the session of the user being switched from.
			 */
			//do_action('login_as_user', $user_id, $old_user_id, $new_token, $old_token);
		} else {
			/**
			 * Fires when a user switches back to their originating account.
			 *
			 * @since 0.6.0
			 * @since 1.4.0 The `$new_token` and `$old_token` parameters were added.
			 *
			 * @param int       $user_id     The ID of the user being switched back to.
			 * @param int|false $old_user_id The ID of the user being switched from, or false if the user is switching back
			 *                               after having been switched off.
			 * @param string    $new_token   The token of the session of the user being switched to. Can be an empty string
			 *                               or a token for a session that may or may not still be valid.
			 * @param string    $old_token   The token of the session of the user being switched from.
			 */
			//do_action('switch_back_user', $user_id, $old_user_id, $new_token, $old_token);
		}

		if ($old_token && $old_user_id && !$set_old_user) {
			// When switching back, destroy the session for the old user
			$manager = WP_Session_Tokens::get_instance($old_user_id);
			$manager->destroy($old_token);
		}

		// When switching, instruct WooCommerce to forget about the current user's session
		if (function_exists('WC')) {
			w357LoginAsUser::forget_woocommerce_session(WC());
		}

		return $user;
	}

}

$plugin = new w357LoginAsUser();
$plugin->run();