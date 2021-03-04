<?php

Class WpJobSearchLogin
{

    const _AUTHORIZE_URL = 'https://www.linkedin.com/uas/oauth2/authorization';
    const _TOKEN_URL = 'https://www.linkedin.com/uas/oauth2/accessToken';
    const _BASE_URL = 'https://api.linkedin.com/v2/me';

    // LinkedIn Application Key
    public $li_api_key;
    // LinkedIn Application Secret
    public $li_secret_key;
    // Stores Access Token
    public $access_token;
    // Stores OAuth Object
    public $oauth;
    // Stores the user redirect after login
    public $user_redirect = false;
    private $redirect_url = '';
    private $linkedin_details;
    private $linkedin_email_detail;
    // Stores our LinkedIn options 
    public $li_options;

    public function __construct()
    {

        global $jobsearch_plugin_options;
        $user_login_page_id = isset($jobsearch_plugin_options['user-login-template-page']) ? $jobsearch_plugin_options['user-login-template-page'] : '';
        $user_login_page_id = jobsearch__get_post_id($user_login_page_id, 'page');

        $linkedin_app_id = isset($jobsearch_plugin_options['jobsearch_linkedin_app_id']) ? $jobsearch_plugin_options['jobsearch_linkedin_app_id'] : '';

        $linkedin_secret = isset($jobsearch_plugin_options['jobsearch_linkedin_secret']) ? $jobsearch_plugin_options['jobsearch_linkedin_secret'] : '';

        add_shortcode('jobsearch_linkedin_login', array($this, 'display_login_button'));

        //
        add_action('jobsearch_do_apply_job_linkedin', array($this, 'do_apply_job_with_linkedin'), 10, 1);

        // This action displays the LinkedIn Login button on the default WordPress Login Page


        // This action processes any LinkedIn Login requests
        //add_action('init', array($this, 'process_login'));
        // Set LinkedIn keys class variables - These will be used throughout the class
        $this->li_api_key = $linkedin_app_id;
        $this->li_secret_key = $linkedin_secret;

        // Get plugin options
        $this->li_options = array(
            'li_cancel_redirect_url' => '',
            'li_redirect_url' => '',
            'li_auto_profile_update' => '',
            'li_registration_redirect_url' => '',
            'li_logged_in_message' => '',
        );

        // Require OAuth2 client to process authentications
        require_once('linkedin_oauth2.class.php');

        // Create new Oauth client
        $this->oauth = new Wp_JobSearch_OAuth2Client($this->li_api_key, $this->li_secret_key);

        // Set Oauth URLs
        $home_url = home_url('/');
        if (strpos($home_url, '?') > 0) {
            $home_url = substr($home_url, 0, strpos($home_url, '?'));
        }
        $this->oauth->redirect_uri = $home_url . '?action=linkedin_login';
        $this->oauth->authorize_url = self::_AUTHORIZE_URL;
        $this->oauth->token_url = self::_TOKEN_URL;
        $this->oauth->api_base_url = self::_BASE_URL;

        // Set user token if user is logged in
        if (get_current_user_id()) {
            $this->oauth->access_token = get_user_meta(get_current_user_id(), 'jobsearch_access_token', true);
        }

        if (!isset($_GET['jobsearch_instagram_login'])) {

            $this->process_login();

            //
            add_action('jobsearch_apply_job_with_linkedin', array($this, 'apply_job_with_linkedin'), 10, 1);

            add_action('wp_ajax_jobsearch_applying_job_with_linkedin', array($this, 'applying_job_with_linkedin'));
            add_action('wp_ajax_nopriv_jobsearch_applying_job_with_linkedin', array($this, 'applying_job_with_linkedin'));
        }
        do_action('jobsearch_linkedin_dologin_inend_constr', $this);
    }

    public function do_apply_job_with_linkedin($user_id)
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

        if (isset($_COOKIE['jobsearch_apply_linkedin_jobid']) && $_COOKIE['jobsearch_apply_linkedin_jobid'] > 0) {
            $job_id = $_COOKIE['jobsearch_apply_linkedin_jobid'];

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

            unset($_COOKIE['jobsearch_apply_linkedin_jobid']);
            setcookie('jobsearch_apply_linkedin_jobid', null, -1, '/');
        }
    }

    // Returns LinkedIn authorization URL
    public function get_auth_url($redirect = false)
    {

        $state = wp_generate_password(12, false);
        $authorize_url = $this->oauth->authorizeUrl(array('scope' => 'r_liteprofile r_emailaddress',
            'state' => $state));

        // Store state in database in temporarily till checked back
        if (!isset($_SESSION['li_api_state'])) {
            $_SESSION['li_api_state'] = $state;
        }

        // Store redirect URL in session
        $_SESSION['li_api_redirect'] = $redirect;

        return $authorize_url;
    }

    // This function displays the login button on the default WP login page
    public function display_login_button()
    {

        // User is not logged in, display login button
        echo '<li><a class="jobsearch-linkedin-bg" href="' . $this->get_auth_url() . '" data-original-title="linkedin"><i class="fa fa-linkedin"></i>' . __('Login with Linkedin', 'wp-jobsearch') . '</a></li>';
    }

    // Logs in a user after he has authorized his LinkedIn account
    function process_login()
    {
        global $jobsearch_plugin_options;
        // If this is not a linkedin sign-in request, do nothing
        if (!$this->is_linkedin_signin()) {
            return;
        }

        // If this is a user sign-in request, but the user denied granting access, redirect to login URL
        if (isset($_REQUEST['error']) && $_REQUEST['error'] == 'access_denied') {

            // Get our cancel redirect URL
            $cancel_redirect_url = $this->li_options['li_cancel_redirect_url'];

            // Redirect to login URL if left blank
            if (empty($cancel_redirect_url)) {
                wp_redirect(home_url('/'));
            }

            // Redirect to our given URL
            wp_safe_redirect($cancel_redirect_url);
        }

        // Another error occurred, create an error log entry
        if (isset($_REQUEST['error'])) {
            $error = $_REQUEST['error'];
            $error_description = $_REQUEST['error_description'];
            error_log("WP_LinkedIn Login Error\nError: $error\nDescription: $error_description");
        }

        // Get profile XML response
        $profile_xml = $this->get_linkedin_profile();
        $email_xml = $this->get_linkedin_profile_email();

        //wp_logout();

        $profile_xml = json_decode($profile_xml, true);
        $email_xml = json_decode($email_xml, true);

        if (!is_array($profile_xml) || !isset($profile_xml['id'])) {
            return false;
        }

        $this->linkedin_details = $profile_xml;

        $this->linkedin_email_details = $email_xml;

        $this->loginUser();
        // Otherwise, we create a new account
        $this->createUser();
        //
        $home_url = home_url('/');
        if (strpos($home_url, '?') > 0) {
            $home_url = substr($home_url, 0, strpos($home_url, '?'));
        }
        if (isset($_COOKIE['linkedin_redirect_url']) && $_COOKIE['linkedin_redirect_url'] != '') {
            $real_redirect_url = $_COOKIE['linkedin_redirect_url'];
            unset($_COOKIE['linkedin_redirect_url']);
            setcookie('linkedin_redirect_url', null, -1, '/');
        } else {
            $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
            $user_dashboard_page = isset($user_dashboard_page) && !empty($user_dashboard_page) ? jobsearch__get_post_id($user_dashboard_page, 'page') : 0;
            $real_redirect_url = $user_dashboard_page > 0 ? get_permalink($user_dashboard_page) : $home_url;
        }


        $this->redirect_url = $real_redirect_url;
        //

        header("Location: " . $this->redirect_url, true);
        die();
    }

    /*
     * Get the user LinkedIN profile and return it as XML
     */

    private function get_linkedin_profile()
    {

        // Use GET method since POST isn't working
        $this->oauth->curl_authenticate_method = 'GET';

        // Request access token
        $response = $this->oauth->authenticate($_REQUEST['code']);

        if ($response) {
            $this->access_token = $response->{'access_token'};
        }

        // Get first name, last name and email address, and load 
        // response into XML object
        $xml = ($this->oauth->get('https://api.linkedin.com/v2/me?projection=(id,firstName,lastName,email-address,profilePicture(displayImage~:playableStreams))'));

        return $xml;
    }

    private function get_linkedin_profile_email()
    {

        // Use GET method since POST isn't working
        $this->oauth->curl_authenticate_method = 'GET';

        // Request access token
        $response = $this->oauth->authenticate($_REQUEST['code']);

        if ($response) {
            $this->access_token = $response->{'access_token'};
        }

        // Get first name, last name and email address, and load 
        // response into XML object
        $xml = ($this->oauth->get('https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))'));

        return $xml;
    }

    /*
     * Checks if this is a LinkedIn sign-in request for our plugin
     */

    private function is_linkedin_signin()
    {

        // If no action is requested or the action is not ours
        if (!isset($_REQUEST['action']) || ($_REQUEST['action'] != "linkedin_login")) {
            return false;
        }

        // If a code is not returned, and no error as well, then OAuth did not proceed properly
        if (!isset($_REQUEST['code']) && !isset($_REQUEST['error'])) {
            return false;
        }
        /*
         * Temporarily disabled this because we're getting two different states at random times

          // If state is not set, or it is different than what we expect there might be a request forgery
          if ( ! isset($_SESSION['li_api_state'] ) || $_REQUEST['state'] != $_SESSION['li_api_state']) {
          return false;
          }
         */

        // This is a LinkedIn signing-request - unset state and return true
        unset($_SESSION['li_api_state']);

        return true;
    }

    private function loginUser()
    {
        global $jobsearch_plugin_options;
        $linkedin_user = $this->linkedin_details;
        $user_id = isset($linkedin_user['id']) ? $linkedin_user['id'] : '';


        // We look for the `eo_linkedin_id` to see if there is any match
        $wp_users = get_users(array(
            'meta_key' => 'jobsearch_linkedin_id',
            'meta_value' => $user_id,
            'number' => 1,
            'count_total' => false,
            'fields' => 'id',
        ));

        if (empty($wp_users[0])) {
            return false;
        }

        $home_url = home_url('/');
        if (strpos($home_url, '?') > 0) {
            $home_url = substr($home_url, 0, strpos($home_url, '?'));
        }

        if (isset($_COOKIE['linkedin_redirect_url']) && $_COOKIE['linkedin_redirect_url'] != '') {
            $real_redirect_url = $_COOKIE['linkedin_redirect_url'];
            unset($_COOKIE['linkedin_redirect_url']);
            setcookie('linkedin_redirect_url', null, -1, '/');
        } else {
            $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
            $user_dashboard_page = isset($user_dashboard_page) && !empty($user_dashboard_page) ? jobsearch__get_post_id($user_dashboard_page, 'page') : 0;
            $real_redirect_url = $user_dashboard_page > 0 ? get_permalink($user_dashboard_page) : $home_url;
        }


        $this->redirect_url = $real_redirect_url;
        //
        // Log the user ?
        wp_set_auth_cookie($wp_users[0]);

        // apply job
        do_action('jobsearch_do_apply_job_linkedin', $wp_users[0]);
        //$this->do_apply_job_with_linkedin($wp_users[0]);

        header("Location: " . $this->redirect_url, true);
        exit();
    }

    /**
     * Create a new WordPress account using Linkedin Details
     */
    private function createUser()
    {

        global $jobsearch_plugin_options;
        $candidate_auto_approve = isset($jobsearch_plugin_options['candidate_auto_approve']) ? $jobsearch_plugin_options['candidate_auto_approve'] : '';
        $linkedin_user = $this->linkedin_details;
        $linkedin_user_email = $this->linkedin_email_details;

        $img_get_arry = isset($linkedin_user['profilePicture']['displayImage~']['elements']) ? $linkedin_user['profilePicture']['displayImage~']['elements'] : '';
        $pitcure_url = isset($img_get_arry[3]['identifiers'][0]['identifier']) ? $img_get_arry[3]['identifiers'][0]['identifier'] : '';
        if ($pitcure_url == '') {
            $pitcure_url = isset($img_get_arry[2]['identifiers'][0]['identifier']) ? $img_get_arry[3]['identifiers'][0]['identifier'] : '';
        }
        if ($pitcure_url == '') {
            $pitcure_url = isset($img_get_arry[1]['identifiers'][0]['identifier']) ? $img_get_arry[3]['identifiers'][0]['identifier'] : '';
        }
        if ($pitcure_url == '') {
            $pitcure_url = isset($img_get_arry[0]['identifiers'][0]['identifier']) ? $img_get_arry[3]['identifiers'][0]['identifier'] : '';
        }

        $user_id = isset($linkedin_user['id']) ? $linkedin_user['id'] : '';

        $first_name = '';
        $last_name = '';

        $first_name_arr = isset($linkedin_user['firstName']['localized']) ? $linkedin_user['firstName']['localized'] : '';
        $last_name_arr = isset($linkedin_user['lastName']['localized']) ? $linkedin_user['lastName']['localized'] : '';

        if (!empty($first_name_arr)) {
            foreach ($first_name_arr as $firs_name_key => $firs_name_val) {
                $first_name = $firs_name_val;
            }
        }
        if (!empty($last_name_arr)) {
            foreach ($last_name_arr as $las_name_key => $las_name_val) {
                $last_name = $las_name_val;
            }
        }

        $email = isset($linkedin_user_email['elements'][0]['handle~']['emailAddress']) ? $linkedin_user_email['elements'][0]['handle~']['emailAddress'] : '';

        $_social_user_obj = get_user_by('email', $email);
        if (is_object($_social_user_obj) && isset($_social_user_obj->ID)) {
            update_user_meta($_social_user_obj->ID, 'jobsearch_linkedin_id', $user_id);
            $this->loginUser();
        }

        if ($first_name != '' && $last_name != '') {
            $name = $first_name . '_' . $last_name;
            $name = str_replace(array(' '), array('_'), $name);
            $username = sanitize_user(str_replace(' ', '_', strtolower($name)));
        } else {
            $username = $email;
        }

        if (username_exists($username)) {
            $username .= '_' . rand(10000, 99999);
        }

        $user_pass = wp_generate_password();

        // Creating our user
        $new_user = wp_create_user($username, $user_pass, $email);

        if (is_wp_error($new_user)) {
            // Report our errors
            set_transient('jobsearch_linkedin_message', $new_user->get_error_message(), 60 * 60 * 24 * 30);
            echo $new_user->get_error_message();
            die;
        } else {
            $user_candidate_id = jobsearch_get_user_candidate_id($new_user);
            // user role
            $user_role = 'jobsearch_candidate';
            wp_update_user(array('ID' => $new_user, 'role' => $user_role));

            // apply job
            do_action('jobsearch_do_apply_job_linkedin', $new_user);
            //$this->do_apply_job_with_linkedin($new_user);

            // Setting the meta
            update_user_meta($new_user, 'first_name', $first_name);
            update_user_meta($new_user, 'last_name', $last_name);
            update_user_meta($new_user, 'jobsearch_linkedin_id', $user_id);
            
            if ($candidate_auto_approve == 'on' || $candidate_auto_approve == 'email') {
                update_post_meta($user_candidate_id, 'jobsearch_field_candidate_approved', 'on');
            }

            if ($pitcure_url != '') {
                jobsearch_upload_attach_with_external_url($pitcure_url, $user_candidate_id);
            }
            $c_user = get_user_by('ID', $new_user);
            do_action('jobsearch_new_user_register', $c_user, $user_pass);
            // Log the user ?
            wp_set_auth_cookie($new_user);
        }
    }

    public function applying_job_with_linkedin()
    {
        global $jobsearch_plugin_options;

        $candidate_auto_approve = isset($jobsearch_plugin_options['candidate_auto_approve']) ? $jobsearch_plugin_options['candidate_auto_approve'] : '';

        $job_id = isset($_POST['job_id']) ? $_POST['job_id'] : '';
        if ($job_id > 0 && get_post_type($job_id) == 'job') {

            setcookie('jobsearch_apply_linkedin_jobid', $job_id, time() + 180, "/");
            if ($candidate_auto_approve == 'on') {
                $real_redirect_url = get_permalink($job_id);
                setcookie('linkedin_redirect_url', $real_redirect_url, time() + 360, "/");
            }
            echo json_encode(array('redirect_url' => $this->get_auth_url()));
            die;
        } else {
            echo json_encode(array('msg' => esc_html__('There is some problem.', 'wp-jobsearch')));
            die;
        }
    }

    public function apply_job_with_linkedin($args = array())
    {
        global $jobsearch_plugin_options;
        $linkedin_login = isset($jobsearch_plugin_options['linkedin-social-login']) ? $jobsearch_plugin_options['linkedin-social-login'] : '';
        if ($linkedin_login == 'on') {
            $job_id = isset($args['job_id']) ? $args['job_id'] : '';
            $classes = isset($args['classes']) && !empty($args['classes']) ? $args['classes'] : 'jobsearch-applyjob-linkedin-btn';

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
                   data-id="<?php echo($job_id) ?>"><i class="careerfy-icon careerfy-linkedin"></i><?php esc_html_e('Apply with Linkedin', 'wp-jobsearch') ?></a>
            <?php } elseif ($view == 'job5') { ?>
                <a href="javascript:void(0);" class="<?php echo($classes); ?>" data-id="<?php echo($job_id) ?>"><i
                            class="careerfy-icon careerfy-linkedin"></i> <?php echo ($label) ?>
                </a>
            <?php } elseif ($view == 'job6') { ?>
                <li><a href="javascript:void(0);" class="<?php echo($classes); ?>" data-id="<?php echo($job_id) ?>"><i
                                class="jobsearch-icon jobsearch-linkedin-logo"></i> <?php echo ($label) ?>
                    </a></li>
            <?php } else { ?>
                <li><a href="javascript:void(0);" class="<?php echo($classes); ?>" data-id="<?php echo($job_id) ?>"><i
                                class="jobsearch-icon jobsearch-linkedin-logo"></i> <?php esc_html_e('Linkedin', 'wp-jobsearch') ?>
                    </a></li>
                <?php
            }
        }
    }

}

$wp_jobsearch_login = new WpJobSearchLogin();
