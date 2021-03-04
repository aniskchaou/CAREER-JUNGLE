<?php
/*
  Class : Login Registration Popup
 */
// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class JobSearch_Login_Registration_Popup
{
    // hook things up
    public function __construct()
    {
        add_action('wp_footer', array($this, 'popup_html_callback'), 10, 1);
        add_action('wp_footer', array($this, 'accaprov_popup_callback'), 999, 1);
    }

    public function popup_html_callback($args = array())
    {
        $rand_numb = rand(1000000, 9999999);
        if (!is_user_logged_in()) {
            $args = array(
                'type' => 'popup',
            );
            ?>
            <div class="jobsearch-modal jobsearch-typo-wrap fade" id="JobSearchModalLogin">
                <div class="modal-inner-area">&nbsp;</div>
                <div class="modal-content-area">
                    <div class="modal-box-area">
                        <?php do_action('login_reg_popup_html', $args) ?>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    public function accaprov_popup_callback($args = array())
    {
        $rand_numb = rand(1000000, 9999999);
        if (!is_user_logged_in()) {
            $args = array(
                'type' => 'popup',
            );
            ?>
            <div class="jobsearch-modal fade" id="JobSearchModalAccountActivationForm">
                <div class="modal-inner-area">&nbsp;</div>
                <div class="modal-content-area">
                    <div class="modal-box-area">
                        <div class="jobsearch-modal-title-box">
                            <h2><?php esc_html_e('Account Activation', 'wp-jobsearch'); ?></h2>
                            <span class="modal-close"><i class="fa fa-times"></i></span>
                        </div>
                        <div class="jobsearch-send-message-form">
                            <form method="post" id="jobsearch_uaccont_aprov_form">
                                <div class="jobsearch-user-form">
                                    <?php
                                    ob_start();
                                    ?>
                                    <p><?php _e('Before you can login, you must active your account with the code sent to your email address.
If you did not receive this email, please check your junk/spam folder.
<a href="javascript:void(0);" style="color: #000000;" class="jobsearch-resend-accactbtn">Click here</a> to resend the activation email.
If you entered an incorrect email address, you will need to re-register with the correct email address.', 'wp-jobsearch'); ?></p>
                                    <?php
                                    $msg_txt = ob_get_clean();
                                    echo apply_filters('jbsearch_activacc_popup_bfields_msg', $msg_txt);
                                    ?>
                                    <ul class="email-fields-list">
                                        <li>
                                            <label>
                                                <?php echo esc_html__('Your Email', 'wp-jobsearch'); ?>:
                                            </label>
                                            <div class="input-field">
                                                <input type="text" name="user_email"/>
                                            </div>
                                        </li>
                                        <li>
                                            <label>
                                                <?php echo esc_html__('Activation Code', 'wp-jobsearch'); ?>:
                                            </label>
                                            <div class="input-field">
                                                <input type="text" name="activ_code"/>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="input-field-submit">
                                                <input type="submit" class="user-activeacc-submit-btn"
                                                       value="<?php esc_html_e('Activate Account', 'wp-jobsearch'); ?>"/>
                                                <span class="loader-box"></span>
                                            </div>
                                        </li>
                                    </ul>
                                    <div class="message-opbox"
                                         style="display: none; float: left;width: 100%;margin: 10px 0 0;"></div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                <?php
                if (isset($_GET['login_action']) && $_GET['login_action'] == 'jobsearch_accaprov') {
                $ac_code = isset($_GET['key']) ? $_GET['key'] : '';
                $ac_login = isset($_GET['login']) ? $_GET['login'] : '';

                $c_user = get_user_by('login', $ac_login);
                $user_email = isset($c_user->user_email) ? $c_user->user_email : '';
                ?>
                var accact_form = jQuery('#jobsearch_uaccont_aprov_form');
                var accact_code = accact_form.find('input[name="activ_code"]');
                var accact_email = accact_form.find('input[name="user_email"]');
                accact_code.val('<?php echo($ac_code) ?>');
                accact_email.val('<?php echo($user_email) ?>');
                jobsearch_modal_popup_open('JobSearchModalAccountActivationForm');
                accact_form.find('input[type="submit"]').trigger('click');
                <?php } ?>
                });
            </script>
            <?php
        }
    }
}
// class JobSearch_Login_Registration_Template 
$JobSearch_Login_Registration_Popup_obj = new JobSearch_Login_Registration_Popup();