<?php

/*
 * Import the Facebook SDK and load all the classes
 */
include(plugin_dir_path(__FILE__) . 'facebook-sdk/autoload.php');

/*
 * Classes required to call the Facebook API
 * They will be used by our class
 */

use Facebook\Facebook;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Exceptions\FacebookResponseException;

/**
 * Class JobsearchFacebook
 */
class JobsearchFacebook
{

    /**
     * Facebook APP ID
     *
     * @var string
     */
    private $app_id = '';

    /**
     * Facebook APP Secret
     *
     * @var string
     */
    private $app_secret = '';

    /**
     * Callback URL used by the API
     *
     * @var string
     */
    private $callback_url = '';

    /**
     * Access token from Facebook
     *
     * @var string
     */
    private $access_token;

    /**
     * Where we redirect our user after the process
     *
     * @var string
     */
    private $redirect_url;

    /**
     * User details from the API
     */
    private $facebook_details;

    private $facebook_get_pic;

    /**
     * JobsearchFacebook constructor.
     */
    public function __construct() {

        global $jobsearch_plugin_options, $pagenow;
        
        if ($pagenow != 'site-health.php') {
            session_start();
        }

        add_action('wp', array($this, 'btn_shortcode_load'));
        //
        add_action('jobsearch_do_apply_job_fb', array($this, 'do_apply_job_with_facebook'), 10, 1);

        add_action('wp_ajax_jobsearch_facebook_detect_user_logged_in', array($this, 'facebook_detect_user_logged_in'));
        add_action('wp_ajax_nopriv_jobsearch_facebook_detect_user_logged_in', array($this, 'facebook_detect_user_logged_in'));

        add_action('wp_ajax_jobsearch_facebook_get_soc_login_url', array($this, 'login_with_redirect_url'));
        add_action('wp_ajax_nopriv_jobsearch_facebook_get_soc_login_url', array($this, 'login_with_redirect_url'));

        //
        $facebook_app_id = isset($jobsearch_plugin_options['jobsearch-facebook-app-id']) ? $jobsearch_plugin_options['jobsearch-facebook-app-id'] : '';
        $facebook_app_secret = isset($jobsearch_plugin_options['jobsearch-facebook-app-secret']) ? $jobsearch_plugin_options['jobsearch-facebook-app-secret'] : '';

        $this->app_id = $facebook_app_id;
        $this->app_secret = $facebook_app_secret;

        $this->callback_url = admin_url('admin-ajax.php?action=jobsearch_facebook');
        // We register our shortcode


        if (!isset($_GET['jobsearch_instagram_login'])) {
            // Callback URL
            add_action('wp_ajax_jobsearch_facebook', array($this, 'apiCallback'));
            add_action('wp_ajax_nopriv_jobsearch_facebook', array($this, 'apiCallback'));

            //
            add_action('jobsearch_apply_job_with_fb', array($this, 'apply_job_with_fb'), 10, 1);

            add_action('wp_ajax_jobsearch_applying_job_with_facebook', array($this, 'applying_job_with_facebook'));
            add_action('wp_ajax_nopriv_jobsearch_applying_job_with_facebook', array($this, 'applying_job_with_facebook'));
        }
        do_action('jobsearch_facebook_dologin_inend_constr', $this);
    }

    public function btn_shortcode_load() {
        add_shortcode('jobsearch_facebook_login', array($this, 'renderShortcode'));
    }

    public function facebook_detect_user_logged_in()
    {
        global $jobsearch_plugin_options;

        $app_id = isset($jobsearch_plugin_options['jobsearch-facebook-app-id']) ? $jobsearch_plugin_options['jobsearch-facebook-app-id'] : '';
        $app_secret = isset($jobsearch_plugin_options['jobsearch-facebook-app-secret']) ? $jobsearch_plugin_options['jobsearch-facebook-app-secret'] : '';

        $access_token = isset($_POST['fb_token']) ? $_POST['fb_token'] : '';
        $facebook = new Facebook([
            'app_id' => $app_id,
            'app_secret' => $app_secret,
            'default_graph_version' => 'v2.10',
            'persistent_data_handler' => 'session'
        ]);

        try {
            $response = $facebook->get('/me?fields=id,name,picture,first_name,last_name,email,link', $access_token);
        } catch (FacebookResponseException $e) {
            $message = __('Graph returned an error: ', 'wp-jobsearch') . $e->getMessage();
            $message = array(
                'type' => 'error',
                'content' => $error
            );
        } catch (FacebookSDKException $e) {
            $message = __('Facebook SDK returned an error: ', 'wp-jobsearch') . $e->getMessage();
            $message = array(
                'type' => 'error',
                'content' => $error
            );
        }

        $user_detail = $response->getGraphUser();

        //var_dump($user_detail);
        //var_dump($user_detail['name']);
        //var_dump($user_detail['picture']['url']);
        if (isset($user_detail['email']) && $user_detail['email'] != '') {
            echo json_encode(array(
                'id' => $user_detail['id'],
                'first_name' => $user_detail['first_name'],
                'last_name' => $user_detail['last_name'],
                'name' => $user_detail['name'],
                'email' => $user_detail['email'],
                'picture' => isset($user_detail['picture']['url']) ? $user_detail['picture']['url'] : '',
            ));
        }
        die;
    }

    public function login_with_redirect_url()
    {

        $user_data = isset($_POST['user_data']) ? $_POST['user_data'] : '';

        if ($user_data != '') {
            $user_data = json_decode(stripslashes($user_data), true);
        }

        if (isset($user_data['email'])) {
            $wp_users = get_users(array(
                'meta_key' => 'jobsearch_facebook_id',
                'meta_value' => isset($user_data['id']) ? $user_data['id'] : '',
                'number' => 1,
                'count_total' => false,
                'fields' => 'id',
            ));

            if (isset($wp_users[0]) && !empty($wp_users[0])) {
                wp_set_auth_cookie($wp_users[0]);
                echo json_encode(array('login' => '1'));
                die;
            }

            $user_id = isset($user_data['id']) ? $user_data['id'] : '';

            $first_name = isset($user_data['first_name']) ? $user_data['first_name'] : '';
            $last_name = isset($user_data['last_name']) ? $user_data['last_name'] : '';

            $user_pic = isset($user_data['picture']) ? $user_data['picture'] : '';

            $name = isset($user_data['name']) ? $user_data['name'] : '';
            $email = isset($user_data['email']) ? $user_data['email'] : '';

            $_social_user_obj = get_user_by('email', $email);
            if (is_object($_social_user_obj) && isset($_social_user_obj->ID)) {
                update_user_meta($_social_user_obj->ID, 'jobsearch_facebook_id', $user_id);
                wp_set_auth_cookie($_social_user_obj->ID);
                echo json_encode(array('login' => '1'));
                die;
            }

            // Create a username
            $username = sanitize_user(str_replace(' ', '_', strtolower($name)));
            if ($username == '') {
                $username = 'user_' . rand(10000, 99999);
            }
            if (username_exists($username)) {
                $username .= '_' . rand(10000, 99999);
            }

            // Creating our user
            $new_user = wp_create_user($username, wp_generate_password(), $email);

            if (!is_wp_error($new_user)) {

                // user role
                $user_role = 'jobsearch_candidate';
                wp_update_user(array('ID' => $new_user, 'role' => $user_role));

                $user_candidate_id = jobsearch_get_user_candidate_id($new_user);
                if ($user_pic != '') {
                    jobsearch_upload_attach_with_external_url($user_pic, $user_candidate_id);
                }

                // Setting the meta
                update_user_meta($new_user, 'first_name', $first_name);
                update_user_meta($new_user, 'last_name', $last_name);
                update_user_meta($new_user, 'jobsearch_facebook_id', $user_id);

                // Log the user ?
                wp_set_auth_cookie($new_user);
                echo json_encode(array('login' => '1'));
                die;
            }
        }
        echo json_encode(array('login' => '1', 'red_url' => $this->getLoginUrl()));
        die;
    }

    public static function getSocLoginFbURL()
    {
        $url = $this->getLoginUrl();
        echo json_encode(array('url' => $url));
        die;
    }

    /**
     * Render the shortcode [jobsearch_facebook/]
     *
     * It displays our Login / Register button
     */
    public function renderShortcode()
    {
        global $jobsearch_plugin_options;
        $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');

        $dashboard_page_url = $user_dashboard_page > 0 ? get_permalink($user_dashboard_page) : home_url('/');
        // No need for the button is the user is already logged
        if (is_user_logged_in())
            return;

        // We save the URL for the redirection:
        if (!get_transient('jobsearch_facebook_url')) {
            set_transient('jobsearch_facebook_url', $dashboard_page_url, 60 * 60 * 24 * 30);
        }

        // Different labels according to whether the user is allowed to register or not
        if (get_option('users_can_register')) {
            $button_label = __('Login or Register with Facebook', 'wp-jobsearch');
        } else {
            $button_label = __('Login with Facebook', 'wp-jobsearch');
        }

        $html = '';

        // Messages
        if (get_transient('jobsearch_facebook_message')) {

            $message = get_transient('jobsearch_facebook_message');
            if (isset($message['content'])) {
                $message = $message['content'];
            }

            $html .= '<div id="jobsearch-facebook-message" class="alert alert-danger">' . $message . '</div>';
            // We remove them from the session
            delete_transient('jobsearch_facebook_message');
        }

        // Button
        if ($this->app_id != '' && $this->app_secret != '') {
            $html .= '<li><a class="jobsearch-facebook-bg" href="' . $this->getLoginUrl() . '"><i class="fa fa-facebook"></i>' . __('Login with Facebook', 'wp-jobsearch') . '</a></li>';
        }

        // Write it down
        return $html;
    }

    /**
     * Init the API Connection
     *
     * @return Facebook
     */
    private function initApi()
    {

        $facebook = new Facebook([
            'app_id' => $this->app_id,
            'app_secret' => $this->app_secret,
            'default_graph_version' => 'v2.10',
            'persistent_data_handler' => 'session'
        ]);

        return $facebook;
    }

    /**
     * Login URL to Facebook API
     *
     * @return string
     */
    private function getLoginUrl()
    {

        $fb = $this->initApi();

        $helper = $fb->getRedirectLoginHelper();

        // Optional permissions
        $permissions = ['email'];

        $url = $helper->getLoginUrl($this->callback_url, $permissions);

        return esc_url($url);
    }

    /**
     * API call back running whenever we hit /wp-admin/admin-ajax.php?action=jobsearch_facebook
     * This code handles the Login / Regsitration part
     */
    public function apiCallback()
    {

        global $jobsearch_plugin_options;
        if (isset($_COOKIE['facebook_redirect_url']) && $_COOKIE['facebook_redirect_url'] != '') {
            $real_redirect_url = $_COOKIE['facebook_redirect_url'];
            unset($_COOKIE['facebook_redirect_url']);
            setcookie('facebook_redirect_url', null, -1, '/');
        } else {

            $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
            $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
            $real_redirect_url = $user_dashboard_page > 0 ? get_permalink($user_dashboard_page) : home_url('/');
        }


        $this->redirect_url = $real_redirect_url;

        // We start the connection
        $fb = $this->initApi();

        // We save the token in our instance
        $this->access_token = $this->getToken($fb);

        // We get the user details
        $this->facebook_details = $this->getUserDetails($fb);

        //$this->facebook_get_pic = $this->getUserPictureURL($fb);

        // We first try to login the user
        $this->loginUser();

        // Otherwise, we create a new account
        $this->createUser();

        // Redirect the user
        header("Location: " . $this->redirect_url, true);
        die();
    }

    /**
     * Get a TOKEN from the Facebook API
     * Or redirect back if there is an error
     *
     * @param $fb Facebook
     * @return string - The Token
     */
    private function getToken($fb)
    {

        // Assign the Session variable for Facebook
        $_SESSION['FBRLH_state'] = $_GET['state'];

        // Load the Facebook SDK helper
        $helper = $fb->getRedirectLoginHelper();

        // Try to get an access token
        try {
            $accessToken = $helper->getAccessToken(admin_url('admin-ajax.php?action=jobsearch_facebook'));
        } // When Graph returns an error
        catch (FacebookResponseException $e) {
            $error = __('Graph returned an error: ', 'wp-jobsearch') . $e->getMessage();
            $message = array(
                'type' => 'error',
                'content' => $error
            );
        } // When validation fails or other local issues
        catch (FacebookSDKException $e) {
            $error = __('Facebook SDK returned an error: ', 'wp-jobsearch') . $e->getMessage();
            $message = array(
                'type' => 'error',
                'content' => $error
            );
        }

        // If we don't got a token, it means we had an error
        if (!isset($accessToken)) {
            // Report our errors

            set_transient('jobsearch_facebook_message', $message, 60 * 60 * 24 * 30);

            // Redirect
            header("Location: " . $this->redirect_url, true);
            die();
        }

        return $accessToken->getValue();
    }

    /**
     * Get user details through the Facebook API
     *
     * @link https://developers.facebook.com/docs/facebook-login/permissions#reference-public_profile
     * @param $fb Facebook
     * @return \Facebook\GraphNodes\GraphUser
     */
    private function getUserDetails($fb)
    {

        try {
            $response = $fb->get('/me?fields=id,name,first_name,last_name,email,link,picture', $this->access_token);
        } catch (FacebookResponseException $e) {
            $message = __('Graph returned an error: ', 'wp-jobsearch') . $e->getMessage();
            $message = array(
                'type' => 'error',
                'content' => $error
            );
        } catch (FacebookSDKException $e) {
            $message = __('Facebook SDK returned an error: ', 'wp-jobsearch') . $e->getMessage();
            $message = array(
                'type' => 'error',
                'content' => $error
            );
        }

        // If we caught an error
        if (isset($message)) {
            // Report our errors
            set_transient('jobsearch_facebook_message', $message, 60 * 60 * 24 * 30);

            // Redirect
            header("Location: " . $this->redirect_url, true);
            die();
        }

        return $response->getGraphUser();
    }

    private function getUserPictureURL($fb)
    {

        try {
            $response = $fb->get('/me/picture', $this->access_token);
        } catch (FacebookResponseException $e) {
            $message = __('Graph returned an error: ', 'wp-jobsearch') . $e->getMessage();
            $message = array(
                'type' => 'error',
                'content' => $error
            );
        } catch (FacebookSDKException $e) {
            $message = __('Facebook SDK returned an error: ', 'wp-jobsearch') . $e->getMessage();
            $message = array(
                'type' => 'error',
                'content' => $error
            );
        }

        return $response->getGraphNode();
    }

    /**
     * Login an user to WordPress
     *
     * @link https://codex.wordpress.org/Function_Reference/get_users
     * @return bool|void
     */
    private function loginUser()
    {

        // We look for the `eo_facebook_id` to see if there is any match
        $wp_users = get_users(array(
            'meta_key' => 'jobsearch_facebook_id',
            'meta_value' => $this->facebook_details['id'],
            'number' => 1,
            'count_total' => false,
            'fields' => 'id',
        ));

        if (empty($wp_users[0])) {
            return false;
        }

        // Log the user ?
        wp_set_auth_cookie($wp_users[0]);

        // apply job
        do_action('jobsearch_do_apply_job_fb', $wp_users[0]);

        header("Location: " . $this->redirect_url, true);
        exit();
    }

    /**
     * Create a new WordPress account using Facebook Details
     */
    private function createUser() {

        global $jobsearch_plugin_options;
        $fb_user = $this->facebook_details;
        $user_pic_obj = isset($fb_user['picture']) ? $fb_user['picture'] : '';
        $user_pic = '';
        if (isset($user_pic_obj['url'])) {
            $user_pic = $user_pic_obj['url'];
        }
        
        $candidate_auto_approve = isset($jobsearch_plugin_options['candidate_auto_approve']) ? $jobsearch_plugin_options['candidate_auto_approve'] : '';

        // Create an username
        $username = sanitize_user(str_replace(' ', '_', strtolower($this->facebook_details['name'])));

        $_social_user_obj = get_user_by('email', $fb_user['email']);
        if (is_object($_social_user_obj) && isset($_social_user_obj->ID)) {
            update_user_meta($_social_user_obj->ID, 'jobsearch_facebook_id', $fb_user['id']);
            $this->loginUser();
        }

        if (username_exists($username)) {
            $username .= '_' . rand(10000, 99999);
        }

        // Creating our user
        $user_pass = wp_generate_password();
        $new_user = wp_create_user($username, $user_pass, $fb_user['email']);

        if (is_wp_error($new_user)) {
            // Report our errors
            set_transient('jobsearch_facebook_message', $new_user->get_error_message(), 60 * 60 * 24 * 30);
            echo $new_user->get_error_message();
            die;
        } else {
            $user_candidate_id = jobsearch_get_user_candidate_id($new_user);
            // user role
            $user_role = 'jobsearch_candidate';
            wp_update_user(array('ID' => $new_user, 'role' => $user_role));

            // apply job
            do_action('jobsearch_do_apply_job_fb', $new_user);

            // Setting the meta
            update_user_meta($new_user, 'first_name', $fb_user['first_name']);
            update_user_meta($new_user, 'last_name', $fb_user['last_name']);
            update_user_meta($new_user, 'user_url', $fb_user['link']);
            update_user_meta($new_user, 'jobsearch_facebook_id', $fb_user['id']);
            
            if ($candidate_auto_approve == 'on' || $candidate_auto_approve == 'email') {
                update_post_meta($user_candidate_id, 'jobsearch_field_candidate_approved', 'on');
            }
            
            if ($user_pic != '') {
                jobsearch_upload_attach_with_external_url($user_pic, $user_candidate_id);
            }
            $c_user = get_user_by('ID', $new_user);
            do_action('jobsearch_new_user_register', $c_user, $user_pass);

            // Log the user ?
            wp_set_auth_cookie($new_user);
        }
    }

    public function do_apply_job_with_facebook($user_id)
    {

        global $jobsearch_plugin_options;

        $candidate_auto_approve = isset($jobsearch_plugin_options['candidate_auto_approve']) ? $jobsearch_plugin_options['candidate_auto_approve'] : '';

        $user_is_candidate = jobsearch_user_is_candidate($user_id);

        $apply_job_cond = true;
        if ($candidate_auto_approve != 'on') {
            $apply_job_cond = false;
            if ($user_is_candidate) {
                $candidate_id = jobsearch_get_user_candidate_id($user_id);
                $candidate_status = get_post_meta($candidate_id, 'jobsearch_field_candidate_approved', true);
                if ($candidate_status == 'on') {
                    $apply_job_cond = true;
                }
            }
        }

        if (isset($_COOKIE['jobsearch_apply_fb_jobid']) && $_COOKIE['jobsearch_apply_fb_jobid'] > 0) {
            $job_id = $_COOKIE['jobsearch_apply_fb_jobid'];

            //
            if ($user_is_candidate) {
                $candidate_id = jobsearch_get_user_candidate_id($user_id);

                jobsearch_create_user_meta_list($job_id, 'jobsearch-user-jobs-applied-list', $user_id);
                $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
                if ($job_applicants_list != '') {
                    $job_applicants_list = explode(',', $job_applicants_list);
                    if (!in_array($candidate_id, $job_applicants_list)) {
                        $job_applicants_list[] = $candidate_id;
                    }
                    $job_applicants_list = implode(',', $job_applicants_list);
                } else {
                    $job_applicants_list = $candidate_id;
                }
                update_post_meta($job_id, 'jobsearch_job_applicants_list', $job_applicants_list);
                $c_user = get_user_by('ID', $user_id);
                do_action('jobsearch_job_applied_to_employer', $c_user, $job_id);
                do_action('jobsearch_job_applied_to_candidate', $c_user, $job_id);
            }

            unset($_COOKIE['jobsearch_apply_fb_jobid']);
            setcookie('jobsearch_apply_fb_jobid', null, -1, '/');
        }
    }

    public function applying_job_with_facebook()
    {
        global $jobsearch_plugin_options;

        $candidate_auto_approve = isset($jobsearch_plugin_options['candidate_auto_approve']) ? $jobsearch_plugin_options['candidate_auto_approve'] : '';

        $job_id = isset($_POST['job_id']) ? $_POST['job_id'] : '';
        if ($job_id > 0 && get_post_type($job_id) == 'job') {

            setcookie('jobsearch_apply_fb_jobid', $job_id, time() + (180), "/");
            if ($candidate_auto_approve == 'on') {
                $real_redirect_url = get_permalink($job_id);
                setcookie('facebook_redirect_url', $real_redirect_url, time() + (360), "/");
            }

            echo json_encode(array('redirect_url' => $this->getLoginUrl()));
            die;
        } else {
            echo json_encode(array('msg' => esc_html__('There is some problem.', 'wp-jobsearch')));
            die;
        }
    }

    public function apply_job_with_fb($args = array())
    {
        global $jobsearch_plugin_options;
        $facebook_login = isset($jobsearch_plugin_options['facebook-social-login']) ? $jobsearch_plugin_options['facebook-social-login'] : '';
        if ($this->app_id != '' && $this->app_secret != '' && $facebook_login == 'on') {
            $job_id = isset($args['job_id']) ? $args['job_id'] : '';
            $classes = isset($args['classes']) && !empty($args['classes']) ? $args['classes'] : 'jobsearch-applyjob-fb-btn';

            $label = isset($args['label']) ? $args['label'] : '';
            $view = isset($args['view']) ? $args['view'] : '';

            if ($view == 'job2') { ?>
                <a href="javascript:void(0);" class="<?php echo($classes); ?>"
                   data-id="<?php echo($job_id) ?>"><?php echo($label); ?></a>
            <?php } elseif ($view == 'job3') { ?>
                <li><a href="javascript:void(0);" class="<?php echo($classes); ?>" data-id="<?php echo($job_id) ?>"></a>
                </li>
            <?php } elseif ($view == 'job4') { ?>
                <a href="javascript:void(0);" class="<?php echo($classes); ?>"
                   data-id="<?php echo($job_id) ?>"><i
                            class="jobsearch-icon jobsearch-facebook-logo-1"></i> <?php esc_html_e('Apply with Facebook', 'wp-jobsearch') ?>
                </a>
            <?php } elseif ($view == 'job5') { ?>
                <a href="javascript:void(0);" class="<?php echo($classes); ?>"
                   data-id="<?php echo($job_id) ?>"><i
                            class="jobsearch-icon jobsearch-facebook-logo-1"></i><?php echo($label); ?></a>
            <?php } elseif ($view == 'job6') { ?>
                <li><a href="javascript:void(0);" class="<?php echo($classes); ?>"
                       data-id="<?php echo($job_id) ?>"><i
                                class="jobsearch-icon jobsearch-facebook-logo-1"></i><?php echo($label); ?></a></li>
            <?php } else { ?>
                <li><a href="javascript:void(0);" class="<?php echo($classes); ?>" data-id="<?php echo($job_id) ?>"><i
                                class="jobsearch-icon jobsearch-facebook-logo-1"></i> <?php esc_html_e('Facebook', 'wp-jobsearch') ?>
                    </a></li>
                <?php
            }
        }
    }

}

/*
 * Starts our plugins, easy!
 */
new JobsearchFacebook();
