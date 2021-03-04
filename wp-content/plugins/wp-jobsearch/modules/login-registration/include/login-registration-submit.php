<?php
/*
  Class : Login_Registration
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}


add_filter('wp_authenticate_user', 'jobsearch_ghghgh_user_auth_callback', 11, 2);

function jobsearch_ghghgh_user_auth_callback($user, $password = '')
{
    global $pagenow;
    ob_start();
    ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript">
        jQuery(document).on('click', '.jobsearch-resend-accactbtn', function (e) {
            e.preventDefault();
            var _this = jQuery(this);
            var user_login = _this.attr('data-login');
            _this.find('em').remove();
            var _this_html = _this.html();
            _this.html(_this_html + '<em>&nbsp;(<?php _e('Sending Email...', 'wp-jobsearch') ?></em>)&nbsp;');
            var request = jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php') ?>',
                method: "POST",
                data: {
                    user_login: user_login,
                    action: 'jobsearch_resend_user_acc_approval_email',
                },
                dataType: "json"
            });

            request.done(function (response) {
                _this.html(_this_html + '<em>&nbsp;(<?php _e('Sent', 'wp-jobsearch') ?></em>)&nbsp;');
                window.location.reload(true);
            });

            request.fail(function (jqXHR, textStatus) {
                _this.html(_this_html + '<em>&nbsp;(<?php _e('Failed', 'wp-jobsearch') ?></em>)&nbsp;');
            });
        });
    </script>
    <?php
    $emsnd_clik_scr = ob_get_clean();

    $user_id = $user->ID;
    $accaprov_key_resent = get_user_meta($user_id, 'jobsearch_accaprov_key_resent', true);

    $user_login_auth = get_user_meta($user_id, 'jobsearch_accaprov_allow', true);
    if ($user_login_auth == '0') {
        $user_login = $user->user_login;
        $errors = new WP_Error();

        $active_popup_btnmsg = ' ' . __('If you have activation code then <a href="javascript:void(0);" class="jobsearch-activcode-popupbtn">Click here</a> to activate account.', 'wp-jobsearch');

        if ($pagenow == 'wp-login.php') {
            if ($accaprov_key_resent == '1') {
                $err_msg = __('Before you can login, you must active your account with the code sent to your email address. If you did not receive this email, please check your junk/spam folder.', 'wp-jobsearch');
            } else {
                $err_msg = __('Before you can login, you must active your account with the code sent to your email address. If you did not receive this email, please check your junk/spam folder. <a href="javascript:void(0);" class="jobsearch-resend-accactbtn" data-login="' . $user_login . '">Click here</a> to resend the activation email.', 'wp-jobsearch') . $emsnd_clik_scr;
            }
        } else {
            if ($accaprov_key_resent == '1') {
                $err_msg = __('Before you can login, you must active your account with the code sent to your email address. If you did not receive this email, please check your junk/spam folder.', 'wp-jobsearch');
            } else {
                $err_msg = __('Before you can login, you must active your account with the code sent to your email address. If you did not receive this email, please check your junk/spam folder. <a href="javascript:void(0);" class="jobsearch-resend-accactbtn" data-login="' . $user_login . '">Click here</a> to resend the activation email.', 'wp-jobsearch');
            }
        }

        $errors->add('appauth_error', $err_msg);
        return $errors;
    }

    return $user;
}

// main plugin class
class Jobsearch_Login_Registration_Submit
{

    // hook things up
    public function __construct()
    {
        add_action('wp_ajax_jobsearch_login_member_submit', array($this, 'jobsearch_login_member_submit_callback'), 1);
        add_action('wp_ajax_nopriv_jobsearch_login_member_submit', array($this, 'jobsearch_login_member_submit_callback'), 1);
        add_action('wp_ajax_nopriv_jobsearch_reset_password', array($this, 'jobsearch_reset_password_callback'), 1);
        add_action('wp_ajax_jobsearch_register_member_submit', array($this, 'jobsearch_register_member_submit_callback'), 1);
        add_action('wp_ajax_nopriv_jobsearch_register_member_submit', array($this, 'jobsearch_register_member_submit_callback'), 1);

        add_action('init', array($this, 'reset_password_form'));

        add_action('wp_ajax_jobsearch_demo_user_login', array($this, 'demo_user_login'));
        add_action('wp_ajax_nopriv_jobsearch_demo_user_login', array($this, 'demo_user_login'));

        add_action('wp_ajax_jobsearch_resend_user_acc_approval_email', array($this, 'resend_user_account_activation'));
        add_action('wp_ajax_nopriv_jobsearch_resend_user_acc_approval_email', array($this, 'resend_user_account_activation'));

        add_action('wp_ajax_jobsearch_pass_reseting_by_redirect_url', array($this, 'reset_password_from_redirect'));
        add_action('wp_ajax_nopriv_jobsearch_pass_reseting_by_redirect_url', array($this, 'reset_password_from_redirect'));

        add_action('wp_ajax_jobsearch_activememb_accont_by_activation_url', array($this, 'user_account_activation'));
        add_action('wp_ajax_nopriv_jobsearch_activememb_accont_by_activation_url', array($this, 'user_account_activation'));

        add_action('user_register', array($this, 'jobsearch_registration_save'), 10, 1);
        add_action('jobsearch_when_user_update_at_bkend', array($this, 'jobsearch_registration_save'), 10, 1);
        
        // for mobile reg
        add_action('wp_ajax_jobsearch_check_mob_no_otp', array($this, 'sendcheck_mob_no_otp'));
        add_action('wp_ajax_nopriv_jobsearch_check_mob_no_otp', array($this, 'sendcheck_mob_no_otp'));
    }

    public function demo_user_login()
    {

        global $jobsearch_plugin_options;
        $user_type = isset($_POST['user_type']) ? $_POST['user_type'] : '';
        $demo_candidate = isset($jobsearch_plugin_options['demo_candidate']) ? $jobsearch_plugin_options['demo_candidate'] : '';
        $demo_employer = isset($jobsearch_plugin_options['demo_employer']) ? $jobsearch_plugin_options['demo_employer'] : '';

        $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
        $page_url = jobsearch_wpml_lang_page_permalink($user_dashboard_page, 'page'); //get_permalink($user_dashboard_page);

        if ($user_type == 'employer') {
            $_demo_user_obj = get_user_by('login', $demo_employer);
            if (isset($_demo_user_obj->ID)) {
                apply_filters('update_login_status', '', $_demo_user_obj);
                wp_set_current_user($_demo_user_obj->ID, $_demo_user_obj->user_login);
                wp_set_auth_cookie($_demo_user_obj->ID);
                echo json_encode(array('redirect' => $page_url, 'msg' => ''));
            }
        } else {
            $_demo_user_obj = get_user_by('login', $demo_candidate);

            if (isset($_demo_user_obj->ID)) {
                apply_filters('update_login_status', '', $_demo_user_obj);
                wp_set_current_user($_demo_user_obj->ID, $_demo_user_obj->user_login);
                wp_set_auth_cookie($_demo_user_obj->ID);
                echo json_encode(array('redirect' => $page_url, 'msg' => ''));
            }
        }
        die;
    }

    public function jobsearch_login_member_submit_callback()
    {
        global $jobsearch_plugin_options;
        // Get variables

        $user_login = $_POST['pt_user_login'];
        $user_pass = $_POST['pt_user_pass'];
        $remember_password = isset($_POST['remember_password']) && $_POST['remember_password'] == 'on' ? true : false;

        $before_signon_error = false;

        $wredirct_url = isset($_POST['jobsearch_wredirct_url']) ? $_POST['jobsearch_wredirct_url'] : '';
        $extra_params = isset($_POST['extra_login_params']) ? $_POST['extra_login_params'] : '';

        $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
        $page_url = jobsearch_wpml_lang_page_permalink($user_dashboard_page, 'page'); //get_permalink($user_dashboard_page);

        //
        $cand_opt_redirect_url = isset($jobsearch_plugin_options['cand-login-redirect-url']) ? $jobsearch_plugin_options['cand-login-redirect-url'] : '';
        $emp_opt_redirect_url = isset($jobsearch_plugin_options['emp-login-redirect-url']) ? $jobsearch_plugin_options['emp-login-redirect-url'] : '';

        // for already logged-in user
        if (is_user_logged_in()) {
            if (empty($user_login) || empty($user_pass)) {
                echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . __('Please fill all form fields', 'wp-jobsearch') . '</div>'));
            }
            wp_logout();
        }

        // Check if input variables are empty
        if (empty($user_login) || empty($user_pass)) {

            echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . __('Please fill all form fields', 'wp-jobsearch') . '</div>'));
        } else {

            $active_popup_btnmsg = ' ' . __('If you have activation code then <a href="javascript:void(0);" class="jobsearch-activcode-popupbtn">Click here</a> to activate account.', 'wp-jobsearch');

            if (filter_var($user_login, FILTER_VALIDATE_EMAIL)) {
                $user_objj = get_user_by('email', $user_login);
            } else {
                $user_objj = get_user_by('login', $user_login);
            }
            $user_id = isset($user_objj->ID) ? $user_objj->ID : '0';

            $user_is_candiadte = jobsearch_user_is_candidate($user_id);
            $user_is_employer = jobsearch_user_is_employer($user_id);

            $user_login_auth = get_user_meta($user_id, 'jobsearch_accaprov_allow', true);

            $not_active_popup_btnmsg = sprintf(__('Before you can login, you must active your account with the code sent to your email address. If you did not receive this email, please check your junk/spam folder. <a href="javascript:void(0);" class="jobsearch-resend-accactbtn" %s >Click here</a> to resend the activation email.', 'wp-jobsearch'), 'data-login="' . $user_login . '"');

            if ($user_login_auth == '0' && isset($user_objj->ID)) {
                echo json_encode(array('error' => false, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . apply_filters('jobsearch_loginerr_msg_no_activ_account', $not_active_popup_btnmsg, $user_login) . $active_popup_btnmsg . '</div>'));
                die;
            }

            $before_signon_error = apply_filters('jobsearch_user_login_err_before_signon', $before_signon_error, $user_login, $user_pass);

            if ($before_signon_error) {
                echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . $before_signon_error . '</div>'));
                die;
            }

            // setting redirect url here
            if ($user_is_candiadte && $cand_opt_redirect_url != '') {
                $page_url = esc_url_raw($cand_opt_redirect_url);
            } else if ($user_is_employer && $emp_opt_redirect_url != '') {
                $page_url = esc_url_raw($emp_opt_redirect_url);
            }

            if ($wredirct_url != '') {
                $page_url = $wredirct_url;
            }

            $creds = array();
            $creds['user_login'] = $user_login;
            $creds['user_password'] = $user_pass;
            $creds['remember'] = $remember_password;

            $user = wp_signon($creds, false);


            if (is_wp_error($user)) {
                $errors_html = wp_kses($user->get_error_message(), array('strong' => array(), 'p' => array()));

                echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . $errors_html . '</div>'));
            } else {
                $user_id = $user->ID;
                $login_args = array(
                    'login_user' => $user,
                    'current_page_id' => (isset($_POST['current_page_id']) ? $_POST['current_page_id'] : 0),
                    'extra_params' => $extra_params,
                    'wredirct_url' => $wredirct_url,
                );
                echo apply_filters('jobsearch_after_logged_in_before_msg', '', $login_args);

                $cur_user_obj = get_user_by('ID', $user_id);

                wp_set_current_user($cur_user_obj->ID, $cur_user_obj->user_login);
                wp_set_auth_cookie($cur_user_obj->ID);
                
                if (in_array('administrator', (array)$cur_user_obj->roles)) {
                    $page_url = admin_url();
                }

                echo json_encode(array('error' => false, 'redirect' => apply_filters('jobsearch_dash_redirect_purl_after_login', $page_url), 'message' => '<div class="alert alert-success"><i class="fa fa-check"></i> ' . __('Login successful, reloading page...', 'wp-jobsearch') . '</div>'));
            }
        }

        die();
    }

    public function jobsearch_reset_password_callback()
    {
        // Get variables
        $username_or_email = $_POST['pt_user_or_email'];

        if (empty($username_or_email)) {
            echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . __('Please fill all form fields', 'wp-jobsearch') . '</div>'));
        } else {

            $username = is_email($username_or_email) ? sanitize_email($username_or_email) : sanitize_user($username_or_email);

            $user_forgotten = $this->lost_password_retrieve($username);

            if (is_wp_error($user_forgotten)) {

                $lostpass_error_messages = $user_forgotten->errors;

                $display_errors = '<div class="alert alert-warning">';
                foreach ($lostpass_error_messages as $error) {
                    $display_errors .= '<p>' . $error[0] . '</p>';
                }
                $display_errors .= '</div>';

                echo json_encode(array('error' => true, 'message' => $display_errors));
            } else {
                echo json_encode(array('error' => false, 'message' => '<p class="alert alert-success"><i class="fa fa-check"></i> ' . __('Please check your email to reset the password.', 'wp-jobsearch') . '</p>'));
            }
        }

        die();
    }

    private function lost_password_retrieve($user_input)
    {

        global $wpdb, $wp_hasher;
        $errors = new WP_Error();
        if (empty($user_input)) {
            $errors->add('empty_username', __('<i class="fa fa-times"></i> <strong>ERROR</strong>: Enter a username or email address.', 'wp-jobsearch'));
        } elseif (strpos($user_input, '@')) {
            $user_data = get_user_by('email', trim($user_input));
            if (!is_object($user_data)) {
                $user_data = get_user_by('login', trim($user_input));
            }
            if (empty($user_data)) {
                $errors->add('invalid_email', __('<i class="fa fa-times"></i> <strong>ERROR</strong>: There is no user registered with that email address.', 'wp-jobsearch'));
            }
        } else {
            $login = trim($user_input);
            $user_data = get_user_by('login', $login);
        }

        /**
         * Fires before errors are returned from a password reset request.
         *
         *
         * @param WP_Error $errors A WP_Error object containing any errors generated
         *                         by using invalid credentials.
         */
        do_action('lostpassword_post', $errors);

        if ($errors->get_error_code())
            return $errors;

        if (!$user_data) {
            $errors->add('invalidcombo', __('<i class="fa fa-times"></i> <strong>ERROR</strong>: Invalid username or email.', 'wp-jobsearch'));
            return $errors;
        }

        // Redefining user_login ensures we return the right case in the email.
        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;
        //$key = get_password_reset_key($user_data);
        $key = wp_generate_password(20, false);
        update_user_meta($user_data->ID, 'password_retrieve_key', $key);

        $message = __('Someone has requested a password reset for the following account:', 'wp-jobsearch') . "\r\n\r\n";
        $message .= home_url('/') . "\r\n\r\n";
        $message .= sprintf(__('Username: %s', 'wp-jobsearch'), $user_login) . "\r\n\r\n";
        $message .= __('If this was a mistake, just ignore this email and nothing will happen.', 'wp-jobsearch') . "\r\n\r\n";
        $message .= __('To reset your password, visit the following address:', 'wp-jobsearch') . "\r\n\r\n";
        $message .= '<' . home_url("/?login_action=jobsearch_rp&key=$key&login=" . rawurlencode($user_login)) . ">\r\n";

        if (is_multisite())
            $blogname = $GLOBALS['current_site']->site_name;
        else
            /*
             * The blogname option is escaped with esc_html on the way into the database
             * in sanitize_option we want to reverse this for the plain text arena of emails.
             */
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

        $title = sprintf(__('[%s] Password Reset', 'wp-jobsearch'), $blogname);

        /**
         * Filter the subject of the password reset email.
         *
         *
         * @param string $title Default email title.
         * @param string $user_login The username for the user.
         * @param WP_User $user_data WP_User object.
         */
        $title = apply_filters('retrieve_password_title', $title, $user_login, $user_data);

        /**
         * Filter the message body of the password reset mail.
         *
         *
         * @param string $message Default mail message.
         * @param string $key The activation key.
         * @param string $user_login The username for the user.
         * @param WP_User $user_data WP_User object.
         */
        $message = apply_filters('retrieve_password_message', $message, $key, $user_login, $user_data);

        //if ($message && !wp_mail($user_email, wp_specialchars_decode($title), $message))
        //    $errors->add('mailfailed', __('<i class="fa fa-times"></i> <strong>ERROR</strong>: The email could not be sent. Possible reason: your host may have disabled the mail() function.', 'wp-jobsearch'));

        do_action('jobsearch_reset_password_request', $user_data, $key);

        return true;
    }

    public function reset_password_form()
    {
        $user_login = isset($_GET['login']) ? $_GET['login'] : '';
        $reg_key = isset($_GET['key']) ? $_GET['key'] : '';
        $get_action = isset($_GET['login_action']) ? $_GET['login_action'] : '';

        if ($user_login != '' && $reg_key != '' && $get_action == 'jobsearch_rp') {
            if (strpos($user_login, '@')) {
                $user_obj = get_user_by('email', trim($user_login));
                if (!is_object($user_obj)) {
                    $user_obj = get_user_by('login', trim($user_login));
                }
            } else {
                $user_obj = get_user_by('login', trim($user_login));
            }

            $user_id = isset($user_obj->ID) ? $user_obj->ID : 0;
            $user_email = isset($user_obj->user_email) ? $user_obj->user_email : '';

            $user_key = get_user_meta($user_id, 'password_retrieve_key', true);

            if ($user_email != '' && $user_key == $reg_key) {

                $popup_args = array('p_user_login' => $user_login, 'p_reg_key' => $reg_key, 'p_user_id' => $user_id);
                add_action('wp_footer', function () use ($popup_args) {

                    extract(shortcode_atts(array(
                        'p_user_login' => '',
                        'p_reg_key' => '',
                        'p_user_id' => '',
                    ), $popup_args));
                    ?>
                    <div class="jobsearch-modal fade" id="JobSearchModalResetPassForm">
                        <div class="modal-inner-area">&nbsp;</div>
                        <div class="modal-content-area">
                            <div class="modal-box-area">
                                <div class="jobsearch-modal-title-box">
                                    <h2><?php esc_html_e('Reset Your Password', 'wp-jobsearch'); ?></h2>
                                    <span class="modal-close"><i class="fa fa-times"></i></span>
                                </div>
                                <div class="jobsearch-send-message-form">
                                    <form method="post" id="jobsearch_reset_pass_form">
                                        <div class="jobsearch-user-form">
                                            <ul class="email-fields-list">
                                                <li>
                                                    <label>
                                                        <?php echo esc_html__('New Password', 'wp-jobsearch'); ?>:
                                                    </label>
                                                    <div class="input-field">
                                                        <input type="password" class="jobsearch_chk_passfield"
                                                               name="new_pass"/>
                                                        <span class="passlenth-chk-msg"></span>
                                                    </div>
                                                </li>
                                                <li>
                                                    <label>
                                                        <?php echo esc_html__('Confirm Password', 'wp-jobsearch'); ?>:
                                                    </label>
                                                    <div class="input-field">
                                                        <input type="password" name="conf_pass"/>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="input-field-submit">
                                                        <input type="submit"
                                                               class="user-passreset-submit-btn jobsearch-regpass-frmbtn jobsearch-disable-btn"
                                                               disabled
                                                               data-id="<?php echo($p_user_id) ?>"
                                                               data-key="<?php echo($p_reg_key) ?>"
                                                               value="<?php esc_html_e('Reset Password', 'wp-jobsearch'); ?>"/>
                                                        <span class="loader-box"></span>
                                                    </div>
                                                </li>
                                            </ul>
                                            <div class="message-box" style="display:none;"></div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            jobsearch_modal_popup_open('JobSearchModalResetPassForm');
                        });
                    </script>
                    <?php
                }, 99, 1);
            }
        }
    }

    public function reset_password_from_redirect()
    {
        $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
        $user_key = isset($_POST['user_key']) ? $_POST['user_key'] : '';
        $new_pass = isset($_POST['new_pass']) ? $_POST['new_pass'] : '';
        $conf_pass = isset($_POST['conf_pass']) ? $_POST['conf_pass'] : '';

        $user_s_key = get_user_meta($user_id, 'password_retrieve_key', true);

        if ($user_key == $user_s_key) {
            if ($new_pass == $conf_pass) {
                $user_def_array = array(
                    'ID' => $user_id,
                    'user_pass' => $new_pass,
                );
                wp_update_user($user_def_array);
                $c_user = get_user_by('ID', $user_id);
                do_action('jobsearch_user_password_change', $c_user, $new_pass);
                echo json_encode(array('error' => '0', 'msg' => esc_html__('Password changed successfully.', 'wp-jobsearch')));
                die;
            } else {
                echo json_encode(array('error' => '1', 'msg' => esc_html__('Confirm password does not match.', 'wp-jobsearch')));
                die;
            }
        }
        echo json_encode(array('error' => '1', 'msg' => esc_html__('You cannot change the password.', 'wp-jobsearch')));
        die;
    }

    public function user_account_activation()
    {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $user_dashboard_page = isset($jobsearch__options['user-dashboard-template-page']) ? $jobsearch__options['user-dashboard-template-page'] : '';
        $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
        $page_url = jobsearch_wpml_lang_page_permalink($user_dashboard_page, 'page');

        $cand_opt_redirect_url = isset($jobsearch_plugin_options['cand-login-redirect-url']) ? $jobsearch_plugin_options['cand-login-redirect-url'] : '';
        $emp_opt_redirect_url = isset($jobsearch_plugin_options['emp-login-redirect-url']) ? $jobsearch_plugin_options['emp-login-redirect-url'] : '';

        $user_email = isset($_POST['user_email']) ? $_POST['user_email'] : '';
        $active_code = isset($_POST['active_code']) ? $_POST['active_code'] : '';

        $candidate_auto_approve = isset($jobsearch__options['candidate_auto_approve']) ? $jobsearch__options['candidate_auto_approve'] : '';
        $employer_auto_approve = isset($jobsearch__options['employer_auto_approve']) ? $jobsearch__options['employer_auto_approve'] : '';

        $c_user = get_user_by('email', $user_email);
        $user_id = isset($c_user->ID) ? $c_user->ID : '';
        $user_s_key = get_user_meta($user_id, 'jobsearch_accaprov_key', true);

        do_action('jobsearch_before_user_acc_activation_incall', $user_id);

        $user_is_candidate = jobsearch_user_is_candidate($user_id);
        $user_is_employer = jobsearch_user_is_employer($user_id);

        // setting redirect url here
        if ($user_is_candiadte && $cand_opt_redirect_url != '') {
            $page_url = esc_url_raw($cand_opt_redirect_url);
        } else if ($user_is_employer && $emp_opt_redirect_url != '') {
            $page_url = esc_url_raw($emp_opt_redirect_url);
        }

        if ($active_code == $user_s_key) {
            if ($user_is_candidate && $candidate_auto_approve == 'email') {
                $candidate_id = jobsearch_get_user_candidate_id($user_id);
                update_post_meta($candidate_id, 'jobsearch_field_candidate_approved', 'on');
            }
            if ($user_is_employer && $employer_auto_approve == 'email') {
                $employer_id = jobsearch_get_user_employer_id($user_id);
                update_post_meta($employer_id, 'jobsearch_field_employer_approved', 'on');
            }
            update_user_meta($user_id, 'jobsearch_accaprov_allow', '1');

            $user_pass = get_user_meta($user_id, 'jobsearch_new_user_regtpass', true);
            if ($user_pass != '') {
                $user_pass = base64_decode($user_pass);
                do_action('jobsearch_new_user_register', $c_user, $user_pass);
            }
            wp_set_current_user($user_id, $c_user->user_login);
            wp_set_auth_cookie($user_id);
            echo json_encode(array('error' => '0', 'redirect' => $page_url, 'msg' => apply_filters('jbsearch_activacc_popup_aftr_submit_msg', esc_html__('Your account is activated. Now you can login your account.', 'wp-jobsearch'))));
            die;
        }
        echo json_encode(array('error' => '1', 'msg' => esc_html__('No record found to activate the account.', 'wp-jobsearch')));
        die;
    }

    public function resend_user_account_activation()
    {
        $user_login = isset($_POST['user_login']) ? $_POST['user_login'] : '';

        $user_pass = '';

        if (filter_var($user_login, FILTER_VALIDATE_EMAIL)) {
            $user_objj = get_user_by('email', $user_login);
        } else {
            $user_objj = get_user_by('login', $user_login);
        }

        if (isset($user_objj->ID)) {
            $user_id = $user_objj->ID;

            $accaprov_key_resent = get_user_meta($user_id, 'jobsearch_accaprov_key_resent', true);

            if ($accaprov_key_resent != '1') {

                do_action('jobsearch_before_user_resent_activation_incall', $user_id, $user_objj);

                $user_is_candidate = jobsearch_user_is_candidate($user_id);
                $user_is_employer = jobsearch_user_is_employer($user_id);

                if ($user_is_candidate) {
                    $code = wp_generate_password(20, false);
                    $code = str_replace(array('#', '&', '?'), array('-', '_', 'q'), $code);
                    update_user_meta($user_id, 'jobsearch_accaprov_key', $code);
                    update_user_meta($user_id, 'jobsearch_accaprov_allow', '0');
                    do_action('jobsearch_new_candidate_approval', $user_objj, $user_pass);

                    update_user_meta($user_id, 'jobsearch_accaprov_key_resent', '1');

                    echo json_encode(array('success' => '1', 'msg' => ''));
                    die;
                }
                //
                if ($user_is_employer) {
                    $code = wp_generate_password(20, false);
                    $code = str_replace(array('#', '&', '?'), array('-', '_', 'q'), $code);
                    update_user_meta($user_id, 'jobsearch_accaprov_key', $code);
                    update_user_meta($user_id, 'jobsearch_accaprov_allow', '0');
                    do_action('jobsearch_new_employer_approval', $user_objj, $user_pass);

                    update_user_meta($user_id, 'jobsearch_accaprov_key_resent', '1');

                    echo json_encode(array('success' => '1', 'msg' => ''));
                    die;
                }
            }
            echo json_encode(array('success' => '0', 'msg' => esc_html__('Resent activation email limit exceeded. Please contact the admin.', 'wp-jobsearch')));
            die;
        }
        echo json_encode(array('success' => '0', 'msg' => ''));
        die;
    }

    // REGISTER
    public function jobsearch_register_member_submit_callback()
    {
        global $jobsearch_plugin_options;
        //
        $cand_opt_redirect_url = isset($jobsearch_plugin_options['cand-login-redirect-url']) ? $jobsearch_plugin_options['cand-login-redirect-url'] : '';
        $emp_opt_redirect_url = isset($jobsearch_plugin_options['emp-login-redirect-url']) ? $jobsearch_plugin_options['emp-login-redirect-url'] : '';

        $flnames_fields_allow = isset($jobsearch_plugin_options['signup_user_flname']) ? $jobsearch_plugin_options['signup_user_flname'] : '';
        
        $pass_from_user = isset($jobsearch_plugin_options['signup_user_password']) ? $jobsearch_plugin_options['signup_user_password'] : '';

        $candidate_auto_approve = isset($jobsearch_plugin_options['candidate_auto_approve']) ? $jobsearch_plugin_options['candidate_auto_approve'] : '';
        $employer_auto_approve = isset($jobsearch_plugin_options['employer_auto_approve']) ? $jobsearch_plugin_options['employer_auto_approve'] : '';

        $signup_user_phone = isset($jobsearch_plugin_options['signup_user_phone']) ? $jobsearch_plugin_options['signup_user_phone'] : '';

        $_POST = jobsearch_input_post_vals_validate($_POST);
        // Get variables
        $user_firstname = isset($_POST['pt_user_fname']) ? $_POST['pt_user_fname'] : '';
        $user_lastname = isset($_POST['pt_user_lname']) ? $_POST['pt_user_lname'] : '';
        $user_login = isset($_POST['pt_user_login']) ? $_POST['pt_user_login'] : '';
        $user_email = isset($_POST['pt_user_email']) ? $_POST['pt_user_email'] : '';
        $user_pass = isset($_POST['pt_user_pass']) ? $_POST['pt_user_pass'] : '';
        $user_cpass = isset($_POST['pt_user_cpass']) ? $_POST['pt_user_cpass'] : '';
        //

        if ($user_login == '' && $user_email != '') {
            $user_login = $user_email;
        }
        
        $user_email = apply_filters('jobsearch_user_reg_submit_post_useremail', $user_email);
        $user_login = apply_filters('jobsearch_user_reg_submit_post_username', $user_login);

        $user_role = isset($_POST['pt_user_role']) ? $_POST['pt_user_role'] : '';
        $wredirct_url = isset($_POST['jobsearch_wredirct_url']) ? $_POST['jobsearch_wredirct_url'] : '';
        $extra_params = isset($_POST['extra_login_params']) ? $_POST['extra_login_params'] : '';

        $user_role_array = apply_filters('jobsearch_user_roles_check_arr_reg_callback', array('jobsearch_candidate', 'jobsearch_employer'));
        if (!in_array($user_role, $user_role_array)) {
            $user_role = 'jobsearch_candidate';
        }

        if ($pass_from_user != 'on') {
            $user_pass = $user_cpass = wp_generate_password(12);
        }

        $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
        $page_url = jobsearch_wpml_lang_page_permalink($user_dashboard_page, 'page'); //get_permalink($user_dashboard_page);

        // Check CSRF token
        if (!check_ajax_referer('ajax-login-nonce', 'register-security', false)) {
            echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . __('Session token has expired, please reload the page and try again', 'wp-jobsearch') . '</div>'));
            die();
        }
        
        if ($flnames_fields_allow == 'on' && apply_filters('jobsearch_user_reg_submit_username_validate', true)) {
            if ($user_firstname == '') {
                $msg = esc_html__('First name is a required field.', 'wp-jobsearch');
                echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . $msg . '</div>'));
                die();
            }
            if ($user_lastname == '') {
                $msg = esc_html__('Last name is a required field.', 'wp-jobsearch');
                echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . $msg . '</div>'));
                die();
            }
        }
        
        if (apply_filters('jobsearch_user_reg_submit_email_validate', true)) {
            if ($user_email == '') {
                $msg = esc_html__('Email address is a required field.', 'wp-jobsearch');
                echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . $msg . '</div>'));
                die();
            }
            if ($user_email != '' && filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
                $user_email = esc_html($user_email);
                if (email_exists($user_email)) {
                    $msg = esc_html__('Sorry! This email is already taken.', 'wp-jobsearch');
                    echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . $msg . '</div>'));
                    die();
                }
            } else {
                $msg = esc_html__('Please Enter a valid email.', 'wp-jobsearch');
                echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . $msg . '</div>'));
                die();
            }
        }
        if (apply_filters('jobsearch_user_reg_submit_password_validate', true)) {
            if (empty($user_pass)) {
                echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . __('Password field is required.', 'wp-jobsearch') . '</div>'));
                die();
            }
        }
        if (apply_filters('jobsearch_user_reg_submit_company_name_validate', true)) {
            if ($user_role == 'jobsearch_employer' && isset($_POST['pt_user_organization']) && $_POST['pt_user_organization'] == '') {
                echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . __('Organization Name is a required field.', 'wp-jobsearch') . '</div>'));
                die();
            }
        }

        if (apply_filters('jobsearch_user_reg_submit_username_validate', true)) {
            if (preg_match("/\\s/", $user_login)) {
                // there are spaces
                echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . __('Username is incorrect.', 'wp-jobsearch') . '</div>'));
                die();
            }
        }

        if ($signup_user_phone == 'on_req' && apply_filters('jobsearch_user_reg_submit_phone_validate', true)) {
            $user_phone = isset($_POST['pt_user_phone']) ? $_POST['pt_user_phone'] : '';
            if ($user_phone == '') {
                $msg = esc_html__('Please enter your phone number.', 'wp-jobsearch');
                echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . $msg . '</div>'));
                die();
            }
        }

        $user_ptype = 'candidate';
        if ($user_role == 'jobsearch_employer') {
            $user_ptype = 'employer';
        }
        $user_ptype = apply_filters('jobsearch_in_user_reg_custom_fields_error_ptype', $user_ptype);
        //
        if (apply_filters('jobsearch_user_reg_submit_cus_fields_validate', true)) {
            do_action('jobsearch_register_custom_fields_error', 0, $user_ptype);
        }

        if ($user_pass != $user_cpass && apply_filters('jobsearch_user_reg_submit_conf_pass_validate', true)) {
            echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . __('Confirm password field does not match with your password.', 'wp-jobsearch') . '</div>'));
            die();
        }

        if (username_exists($user_login) && apply_filters('jobsearch_user_reg_submit_username_exst_validate', true)) {
            //$user_login = $user_login . rand(1000000, 9999999);
            $msg = esc_html__('Username already exists. Please try another username.', 'wp-jobsearch');
            echo json_encode(array('error' => true, 'message' => '<div class="alert alert-danger"><i class="fa fa-times"></i> ' . $msg . '</div>'));
            die();
        }

        if (apply_filters('jobsearch_user_reg_submit_recaptcha_validate', true)) {
            jobsearch_captcha_verify();
        }
        
        $create_user = wp_create_user($user_login, $user_pass, $user_email);

        if (is_wp_error($create_user)) {
            $registration_error_messages = $create_user->errors;
            $display_errors = '<div class="alert alert-danger">';
            foreach ($registration_error_messages as $error) {
                $display_errors .= '<p>' . $error[0] . '</p>';
            }

            $display_errors .= '</div>';

            echo json_encode(array('error' => true, 'message' => $display_errors));
        } else {
            $set_user_role = apply_filters('jobsearch_user_in_register_set_role', $user_role);
            $update_user_arr = array(
                'ID' => $create_user,
                'role' => $set_user_role
            );

            if (isset($_POST['pt_user_fname']) && $_POST['pt_user_fname'] != '') {
                $update_user_arr['first_name'] = $_POST['pt_user_fname'];
            }
            if (isset($_POST['pt_user_lname']) && $_POST['pt_user_lname'] != '') {
                $update_user_arr['last_name'] = $_POST['pt_user_lname'];
            }

            wp_update_user($update_user_arr);
            $user_id = $create_user;
            do_action('jobsearch_green_tech_save_inputs', $user_id, $_POST);
            
            do_action('jobsearch_user_regform_fields_saving', $user_id);

            $_user_obj = get_user_by('ID', $create_user);
            $user_is_candidate = jobsearch_user_is_candidate($user_id);
            $user_is_employer = jobsearch_user_is_employer($user_id);
            if (isset($_user_obj->ID) && $pass_from_user == 'on') {
                if ($user_is_candidate && ($candidate_auto_approve == 'email' || $candidate_auto_approve == 'admin_email')) {
                    //
                } else if ($user_is_employer && ($employer_auto_approve == 'email' || $employer_auto_approve == 'admin_email')) {
                    //
                } else {
                    wp_set_current_user($_user_obj->ID, $_user_obj->user_login);
                    wp_set_auth_cookie($_user_obj->ID);
                }
            }

            // setting redirect url here
            if ($user_is_candiadte && $cand_opt_redirect_url != '') {
                $page_url = esc_url_raw($cand_opt_redirect_url);
            } else if ($user_is_employer && $emp_opt_redirect_url != '') {
                $page_url = esc_url_raw($emp_opt_redirect_url);
            }

            //
            if ($wredirct_url != '') {
                $page_url = $wredirct_url;
            }

            //
            $appr_msg_arr = array(
                'error' => false,
                'email_auth' => '1',
                'message' => '<div class="alert alert-success"><i class="fa fa-check"></i> ' . apply_filters('jobsearch_registerr_msg_no_activ_account', __('Registration complete. Before you can login, you must active your account with the code sent to your email address. Please <a href="javascript:void(0);" class="jobsearch-activcode-popupbtn">Click here</a> to activate your account.', 'wp-jobsearch')) . '</div>',
            );
            if ($pass_from_user == 'on') {
                $reg_args = array(
                    'login_user' => $_user_obj,
                    'extra_params' => $extra_params,
                    'wredirct_url' => $wredirct_url,
                );
                echo apply_filters('jobsearch_after_registr_in_before_msg', '', $reg_args);

                if ($user_is_candidate && ($candidate_auto_approve == 'email' || $candidate_auto_approve == 'admin_email')) {
                    echo json_encode($appr_msg_arr);
                } else if ($user_is_employer && ($employer_auto_approve == 'email' || $employer_auto_approve == 'admin_email')) {
                    echo json_encode($appr_msg_arr);
                } else {
                    echo json_encode(array('error' => false, 'email_auth' => '0', 'redirect' => $page_url, 'message' => '<div class="alert alert-success"><i class="fa fa-check"></i> ' . __('Registration complete. You are redirecting to your dashboard.', 'wp-jobsearch') . '</div>'));
                }
            } else {
                if ($user_is_candidate && ($candidate_auto_approve == 'email' || $candidate_auto_approve == 'admin_email')) {
                    echo json_encode($appr_msg_arr);
                } else if ($user_is_employer && ($employer_auto_approve == 'email' || $employer_auto_approve == 'admin_email')) {
                    echo json_encode($appr_msg_arr);
                } else {
                    echo json_encode(array('error' => false, 'email_auth' => '0', 'message' => '<div class="alert alert-success"><i class="fa fa-check"></i> ' . __('Registration complete. Password sent to your e-mail.', 'wp-jobsearch') . '</div>'));
                }
            }
            $c_user = get_user_by('email', $user_email);
            // to admin
            do_action('jobsearch_new_user_reg_toadmin', $c_user, $user_pass);
            //

            $send_reg_email = true;
            if ($user_is_candidate) {
                if ($candidate_auto_approve == 'email' || $candidate_auto_approve == 'admin_email') {
                    $send_reg_email = false;
                    $code = wp_generate_password(20, false);
                    $code = str_replace(array('#', '&', '?'), array('-', '_', 'q'), $code);
                    update_user_meta($user_id, 'jobsearch_accaprov_key', $code);
                    update_user_meta($user_id, 'jobsearch_accaprov_allow', '0');
                    do_action('jobsearch_new_candidate_approval', $c_user, $user_pass);
                }
            }
            //
            if ($user_is_employer) {
                if ($employer_auto_approve == 'email' || $employer_auto_approve == 'admin_email') {
                    $send_reg_email = false;
                    $code = wp_generate_password(20, false);
                    $code = str_replace(array('#', '&', '?'), array('-', '_', 'q'), $code);
                    update_user_meta($user_id, 'jobsearch_accaprov_key', $code);
                    update_user_meta($user_id, 'jobsearch_accaprov_allow', '0');
                    do_action('jobsearch_new_employer_approval', $c_user, $user_pass);
                }
            }
            //
            if ($send_reg_email) {
                do_action('jobsearch_new_user_register', $c_user, $user_pass);
            } else {
                $user_pass = base64_encode($user_pass);
                update_user_meta($user_id, 'jobsearch_new_user_regtpass', $user_pass);
            }
        }

        die();
    }

    public function jobsearch_registration_save($user_id)
    {
        global $jobsearch_plugin_options, $sitepress, $wpdb;
        $candidate_auto_approve = isset($jobsearch_plugin_options['candidate_auto_approve']) ? $jobsearch_plugin_options['candidate_auto_approve'] : '';
        $employer_auto_approve = isset($jobsearch_plugin_options['employer_auto_approve']) ? $jobsearch_plugin_options['employer_auto_approve'] : '';

        $_POST = jobsearch_input_post_vals_validate($_POST);

        $user_role = isset($_POST['pt_user_role']) ? $_POST['pt_user_role'] : '';

        $user_role = isset($_POST['role']) && $_POST['role'] != '' ? $_POST['role'] : $user_role;

        $user_phone = isset($_POST['pt_user_phone']) ? $_POST['pt_user_phone'] : '';
        $user_dial_code = isset($_POST['dial_code']) ? $_POST['dial_code'] : '';
        $contry_iso_code = isset($_POST['contry_iso_code']) ? $_POST['contry_iso_code'] : '';

        $user_obj = get_user_by('ID', $user_id);

        if ($post_role_key = array_search('jobsearch_employer', $_POST)) {
            if (isset($_POST[$post_role_key]) && $_POST[$post_role_key] == 'jobsearch_employer') {
                $user_role = 'jobsearch_employer';
            }
        }

        //
        $to_allow_makepost = apply_filters('jobsearch_reguser_allow_to_makepost', 'yes', $user_id);
        if ($to_allow_makepost == 'no') {
            return false;
        }

        if ($user_role == 'jobsearch_employer') {
            $memb_profile_name = $user_obj->display_name;
            if (isset($_POST['pt_user_fname']) && $_POST['pt_user_fname'] != '') {
                $memb_profile_name = $_POST['pt_user_fname'];
                if (isset($_POST['pt_user_lname']) && $_POST['pt_user_lname'] != '') {
                    $memb_profile_name .= ' ' . $_POST['pt_user_lname'];
                }
            }

            if (isset($_POST['pt_user_organization']) && $_POST['pt_user_organization'] != '') {
                $memb_profile_name = sanitize_text_field($_POST['pt_user_organization']);
            }

            $post_status = 'publish';
            if (isset($_POST['public_profile_visible']) && $_POST['public_profile_visible'] == 'no') {
                $post_status = 'draft';
            }

            $employer_post = array(
                'post_title' => str_replace(array('-', '_'), array(' ', ' '), $memb_profile_name),
                'post_type' => 'employer',
                'post_content' => '',
                'post_status' => $post_status,
                'post_author' => $user_id,
            );

            // Insert the post into the database
            $employer_id = wp_insert_post($employer_post);

            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $lang_code = $sitepress->get_current_language();
                $lang_code = apply_filters('jobsearch_set_post_insert_lang_code', $lang_code);
                $sitepress->set_element_language_details($employer_id, 'post_employer', false, $lang_code);
            }

            //
            update_post_meta($employer_id, 'jobsearch_user_id', $user_id);
            update_post_meta($employer_id, 'member_display_name', $user_obj->display_name);
            update_post_meta($employer_id, 'jobsearch_field_user_email', $user_obj->user_email);

            update_post_meta($employer_id, 'post_date', strtotime(current_time('d-m-Y H:i:s')));

            //
            if ($user_dial_code != '') {
                update_post_meta($employer_id, 'jobsearch_field_user_phone', $user_dial_code . $user_phone);
                update_post_meta($employer_id, 'jobsearch_field_user_justphone', $user_phone);
                update_post_meta($employer_id, 'jobsearch_field_user_dial_code', $user_dial_code);
                update_post_meta($employer_id, 'jobsearch_field_contry_iso_code', $contry_iso_code);
            } else {
                update_post_meta($employer_id, 'jobsearch_field_user_phone', $user_phone);
            }

            if (isset($_POST['pt_user_category'])) {
                $user_sector = ($_POST['pt_user_category']);
                $user_sector = is_array($user_sector) ? $user_sector : array($user_sector);
                wp_set_post_terms($employer_id, $user_sector, 'sector', false);
            }
            if (isset($_POST['pt_user_organization'])) {
                $user_company_title = sanitize_text_field($_POST['pt_user_organization']);
                $up_post = array(
                    'ID' => $employer_id,
                    'post_title' => wp_strip_all_tags($user_company_title),
                );
                wp_update_post($up_post);
                //
                update_post_meta($employer_id, 'member_display_name', wp_strip_all_tags($user_company_title));
                $user_def_array = array(
                    'ID' => $user_id,
                    'display_name' => $user_company_title,
                );

                wp_update_user($user_def_array);
            }
            // custom fields saving
            do_action('jobsearch_signup_custom_fields_save', 'employer', $employer_id);
            //
            // Cus Fields Upload Files /////
            do_action('jobsearch_custom_field_upload_files_save', $employer_id, 'employer');
            //

            if ($employer_auto_approve == 'on') {
                update_post_meta($employer_id, 'jobsearch_field_employer_approved', 'on');
            } else {
                update_post_meta($employer_id, 'jobsearch_field_employer_approved', '');
            }
            //
            update_user_meta($user_id, 'jobsearch_employer_id', $employer_id);
            do_action('jobsearch_user_data_save_onprofile', $user_id, $employer_id, 'employer');

            do_action('jobsearch_employer_register_on_signup', $user_id, $employer_id, 'employer');

        } else {

            $pos_emails = array();
            for ($i = 0; $i <= 100; $i++) {
                $pos_emails[] = 'cand-dummy' . $i . '@eyecix.com';
                $pos_emails[] = 'emp-dummy' . $i . '@eyecix.com';
            }
            if (!in_array($user_obj->user_email, $pos_emails)) {

                $memb_profile_name = $user_obj->display_name;
                if (isset($_POST['pt_user_fname']) && $_POST['pt_user_fname'] != '') {
                    $memb_profile_name = $_POST['pt_user_fname'];
                    if (isset($_POST['pt_user_lname']) && $_POST['pt_user_lname'] != '') {
                        $memb_profile_name .= ' ' . $_POST['pt_user_lname'];
                    }
                }

                $memb_profile_name = str_replace(array('-', '_'), array(' ', ' '), $memb_profile_name);

                $post_status = 'publish';
                if (isset($_POST['public_profile_visible']) && $_POST['public_profile_visible'] == 'no') {
                    $post_status = 'draft';
                }

                $user_def_array = array(
                    'ID' => $user_id,
                    'display_name' => $memb_profile_name,
                );

                wp_update_user($user_def_array);

                $candidate_post = array(
                    'post_title' => $memb_profile_name,
                    'post_type' => 'candidate',
                    'post_content' => '',
                    'post_status' => $post_status,
                    'post_author' => $user_id,
                );

                // Insert the post into the database
                $candidate_id = wp_insert_post($candidate_post);

                if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                    $lang_code = $sitepress->get_current_language();
                    $lang_code = apply_filters('jobsearch_set_post_insert_lang_code', $lang_code);
                    $sitepress->set_element_language_details($candidate_id, 'post_candidate', false, $lang_code);
                }

                //
                update_post_meta($candidate_id, 'jobsearch_user_id', $user_id);
                update_post_meta($candidate_id, 'member_display_name', $user_obj->display_name);
                update_post_meta($candidate_id, 'jobsearch_field_user_email', $user_obj->user_email);

                update_post_meta($candidate_id, 'post_date', strtotime(current_time('d-m-Y H:i:s')));

                //
                if ($user_dial_code != '') {
                    update_post_meta($candidate_id, 'jobsearch_field_user_phone', $user_dial_code . $user_phone);
                    update_post_meta($candidate_id, 'jobsearch_field_user_justphone', $user_phone);
                    update_post_meta($candidate_id, 'jobsearch_field_user_dial_code', $user_dial_code);
                    update_post_meta($candidate_id, 'jobsearch_field_contry_iso_code', $contry_iso_code);
                } else {
                    update_post_meta($candidate_id, 'jobsearch_field_user_phone', $user_phone);
                }

                if (isset($_POST['pt_user_category'])) {
                    $user_sector = ($_POST['pt_user_category']);
                    $user_sector = is_array($user_sector) ? $user_sector : array($user_sector);
                    wp_set_post_terms($candidate_id, $user_sector, 'sector', false);
                }

                // cv file
                $atach_url = jobsearch_upload_candidate_cv('candidate_cv_file', $candidate_id);

                $multiple_cv_files_allow = isset($jobsearch_plugin_options['multiple_cv_uploads']) ? $jobsearch_plugin_options['multiple_cv_uploads'] : '';

                if ($atach_url != '') {
                    $file_url = $atach_url;
                    $file_uniqid = uniqid();

                    $filename = basename($file_url);
                    $filetype = wp_check_filetype($filename, null);
                    $fileuplod_time = current_time('timestamp');

                    if ($multiple_cv_files_allow == 'on') {
                        $arg_arr = array(
                            'file_name' => $filename,
                            'mime_type' => $filetype,
                            'time' => $fileuplod_time,
                            'file_url' => $file_url,
                            'file_id' => $file_uniqid,
                            'primary' => '',
                        );
                        $ca_at_cv_files = get_post_meta($candidate_id, 'candidate_cv_files', true);
                        $ca_jat_cv_files = get_post_meta($candidate_id, 'jobsearch_field_user_cv_attachments', true);
                        $ca_at_cv_files = !empty($ca_at_cv_files) ? $ca_at_cv_files : array();
                        $ca_jat_cv_files = !empty($ca_jat_cv_files) ? $ca_jat_cv_files : array();

                        $ca_at_cv_files[] = $arg_arr;
                        $ca_jat_cv_files[] = $arg_arr;
                        update_post_meta($candidate_id, 'candidate_cv_files', $ca_at_cv_files);
                        update_post_meta($candidate_id, 'jobsearch_field_user_cv_attachments', $ca_jat_cv_files);
                    } else {
                        $arg_arr = array(
                            'file_name' => $filename,
                            'mime_type' => $filetype,
                            'time' => $fileuplod_time,
                            'file_url' => $file_url,
                            'file_id' => $file_uniqid,
                        );
                        update_post_meta($candidate_id, 'candidate_cv_file', $arg_arr);
                        update_post_meta($candidate_id, 'jobsearch_field_user_cv_attachment', $file_url);
                    }
                }

                // custom fields saving
                do_action('jobsearch_signup_custom_fields_save', 'candidate', $candidate_id);
                //
                // Cus Fields Upload Files //
                do_action('jobsearch_custom_field_upload_files_save', $candidate_id, 'candidate');
                //

                if ($candidate_auto_approve == 'on') {
                    update_post_meta($candidate_id, 'jobsearch_field_candidate_approved', 'on');
                } else {
                    update_post_meta($candidate_id, 'jobsearch_field_candidate_approved', '');
                }

                //
                update_user_meta($user_id, 'jobsearch_candidate_id', $candidate_id);

                // add candidate skills level
                jobsearch_candidate_skill_percent_count($user_id, 'none');
                do_action('jobsearch_user_data_save_onprofile', $user_id, $candidate_id, 'candidate');

                do_action('jobsearch_user_aftr_on_signup_savprofile', $user_id, $candidate_id, 'candidate');
            }
        }

        do_action('jobsearch_member_after_making_cand_or_emp', $user_id, $user_role);

        jobsearch_onuser_update_wc_update($user_id);

        //remove user admin bar
        update_user_option($user_id, 'show_admin_bar_front', false);
    }

}

// class Jobsearch_Login_Registration_Submit 
$Jobsearch_Login_Registration_Submit_obj = new Jobsearch_Login_Registration_Submit();
