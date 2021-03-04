<?php
/*
  Class : Login_Registration
 */

// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_Login_Registration_Template
{
    // hook things up
    public function __construct()
    {
        add_action('login_registration_form_html', array($this, 'login_registration_form_html_callback'), 1);
        add_action('login_form_html', array($this, 'login_form_html_callback'), 1);
        add_action('registration_form_html', array($this, 'registration_form_html_callback'), 1);
        add_action('login_reg_popup_html', array($this, 'popup_login_reg_form_html_callback'), 10, 1);

        //
        add_action('jobsearch_after_regform_html_action', array($this, 'after_regform_html'));
    }

    public function login_registration_form_html_callback($arg)
    {
        ob_start();
        global $jobsearch_plugin_options;
        $op_register_form_allow = isset($jobsearch_plugin_options['login_register_form']) ? $jobsearch_plugin_options['login_register_form'] : '';
        $op_cand_register_allow = isset($jobsearch_plugin_options['login_candidate_register']) ? $jobsearch_plugin_options['login_candidate_register'] : '';
        $op_emp_register_allow = isset($jobsearch_plugin_options['login_employer_register']) ? $jobsearch_plugin_options['login_employer_register'] : '';
        $register_form_allow = isset($arg['login_register_form']) ? $arg['login_register_form'] : '';
        $cand_register_allow = isset($arg['login_candidate_register']) ? $arg['login_candidate_register'] : '';
        $emp_register_allow = isset($arg['login_employer_register']) ? $arg['login_employer_register'] : '';
        $logreg_form_type = isset($arg['logreg_form_type']) ? $arg['logreg_form_type'] : '';

        $register_form_view = true;
        if ($op_register_form_allow == 'off') {
            $register_form_view = false;
        }
        if ($op_cand_register_allow == 'no' && $op_emp_register_allow == 'no') {
            $register_form_view = false;
        }

        if ($register_form_allow == 'off') {
            $register_form_view = false;
        } else {
            $register_form_view = true;
        }
        if ($cand_register_allow == 'no' && $emp_register_allow == 'no') {
            $register_form_view = false;
        } else {
            if ($register_form_allow != 'off') {
                $register_form_view = true;
            }
        }

        $html = '';

        $one_form_only = false;
        $form_col_class = 'jobsearch-column-6';
        if ($logreg_form_type == 'reg_only') {
            $form_col_class = 'jobsearch-column-12';
            $one_form_only = true;
        }
        if ($logreg_form_type == 'login_only') {
            $form_col_class = 'jobsearch-column-12';
            $one_form_only = true;
        }

        ?>
        <div class="jobsearch-row">
            <?php
            if ($one_form_only) {
                echo '<div class="jobsearch-onlyonelog-form">';
            }
            //
            ob_start();
            if ($logreg_form_type != 'reg_only') {
                ?>
                <div class="<?php echo apply_filters('jobsearch_login_maincol6_boxp_class', $form_col_class); ?>">
                    <?php echo apply_filters('jobsearch_logginpag_before_login_box', ''); ?>
                    <div class="<?php echo apply_filters('jobsearch_logginpag_login_box_mainclass', 'jobsearch-login-box'); ?>">
                        <?php do_action('login_form_html', $arg); ?>
                        <?php
                        ob_start();
                        do_action('social_login_html', $arg);
                        $socialogin_html = ob_get_clean();
                        echo apply_filters('jobsearch_ploginform_socailogin_box_html', $socialogin_html, $arg);
                        ?>
                    </div>
                    <?php echo apply_filters('jobsearch_logginpag_after_login_box', ''); ?>
                </div>
                <?php
            }
            $reg_maincol_html = ob_get_clean();
            echo apply_filters('jobsearch_login_maincol_boxp_html', $reg_maincol_html, $arg);

            //
            ob_start();
            if (!is_user_logged_in() && $logreg_form_type != 'login_only') {
                ?>
                <div class="<?php echo apply_filters('jobsearch_reg_maincol6_boxp_class', $form_col_class); ?>">
                    <?php
                    if ($register_form_view === true) {
                        echo apply_filters('jobsearch_logginpag_before_reg_box', '');
                        ?>
                        <div class="<?php echo apply_filters('jobsearch_logginpag_reg_box_mainclass', 'jobsearch-login-box'); ?>">
                            <?php do_action('registration_form_html', $arg); ?>
                        </div>
                        <?php
                    } else {
                        echo '<div class="alert alert-warning">' . __('Registration is disabled.', 'wp-jobsearch') . '</div>';
                    }
                    ?>
                </div>
                <?php
            }
            $reg_maincol_html = ob_get_clean();
            echo apply_filters('jobsearch_reg_maincol_boxp_html', $reg_maincol_html, $arg);
            //
            if ($one_form_only) {
                echo '</div>';
            }
            ?>
        </div>
        <?php
        $html = ob_get_clean();
        echo force_balance_tags($html);
    }

    public function popup_login_reg_form_html_callback($args = array())
    {
        global $jobsearch_plugin_options;
        $flnames_fields_allow = isset($jobsearch_plugin_options['signup_user_flname']) ? $jobsearch_plugin_options['signup_user_flname'] : '';
        $username_field_allow = isset($jobsearch_plugin_options['signup_username_allow']) ? $jobsearch_plugin_options['signup_username_allow'] : '';
        $uemail_field_title = esc_html__('Email Address', 'wp-jobsearch');

        if ($username_field_allow == 'on') {
            $uemail_field_title = esc_html__('Username/Email Address', 'wp-jobsearch');
        }

        $captcha_switch = isset($jobsearch_plugin_options['captcha_switch']) ? $jobsearch_plugin_options['captcha_switch'] : '';
        $jobsearch_sitekey = isset($jobsearch_plugin_options['captcha_sitekey']) ? $jobsearch_plugin_options['captcha_sitekey'] : '';
        $demo_user_login = isset($jobsearch_plugin_options['demo_user_login']) ? $jobsearch_plugin_options['demo_user_login'] : '';
        $demo_candidate = isset($jobsearch_plugin_options['demo_candidate']) ? $jobsearch_plugin_options['demo_candidate'] : '';
        $demo_employer = isset($jobsearch_plugin_options['demo_employer']) ? $jobsearch_plugin_options['demo_employer'] : '';
        $adm_user_obj = get_user_by('login', 'jobsearch-admin');
        if (is_object($adm_user_obj) && isset($adm_user_obj->ID) && in_array('administrator', jobsearch_get_user_roles_by_user_id($adm_user_obj->ID))) {
            $demo_user_login = $demo_user_login;
        } else {
            $demo_user_login = 'off';
        }
        $op_register_form_allow = isset($jobsearch_plugin_options['login_register_form']) ? $jobsearch_plugin_options['login_register_form'] : '';
        $op_cand_register_allow = isset($jobsearch_plugin_options['login_candidate_register']) ? $jobsearch_plugin_options['login_candidate_register'] : '';
        $op_emp_register_allow = isset($jobsearch_plugin_options['login_employer_register']) ? $jobsearch_plugin_options['login_employer_register'] : '';
        if (isset($args['login_register_form'])) {
            $op_register_form_allow = $args['login_register_form'];
        }
        if (isset($args['login_candidate_register'])) {
            $op_cand_register_allow = $args['login_candidate_register'];
        }
        if (isset($args['login_employer_register'])) {
            $op_emp_register_allow = $args['login_employer_register'];
        }
        $register_form_view = true;
        if ($op_register_form_allow == 'off') {
            $register_form_view = false;
        }
        if ($op_cand_register_allow == 'no' && $op_emp_register_allow == 'no') {
            $register_form_view = false;
        }
        if (!get_option('users_can_register')) {
            //$register_form_view = false;
        }
        $signup_user_sector = isset($jobsearch_plugin_options['signup_user_sector']) ? $jobsearch_plugin_options['signup_user_sector'] : '';
        $signup_org_name = isset($jobsearch_plugin_options['signup_organization_name']) ? $jobsearch_plugin_options['signup_organization_name'] : '';
        $signup_user_phone = isset($jobsearch_plugin_options['signup_user_phone']) ? $jobsearch_plugin_options['signup_user_phone'] : '';
        $pass_from_user = isset($jobsearch_plugin_options['signup_user_password']) ? $jobsearch_plugin_options['signup_user_password'] : '';
        $signup_cv_upload = isset($jobsearch_plugin_options['signup_cv_upload']) ? $jobsearch_plugin_options['signup_cv_upload'] : '';

        ob_start();
        $html = '';
        $rand_numb = rand(1000000, 9999999);
        ?>
        <div class="login-form-<?php echo absint($rand_numb) ?>">
            <div class="jobsearch-modal-title-box">
                <h2><?php _e('Login to your account', 'wp-jobsearch') ?></h2>
                <span class="modal-close"><i class="fa fa-times"></i></span>
            </div>
            <form id="login-form-<?php echo absint($rand_numb) ?>" action="<?php echo home_url('/'); ?>" method="post">
                <?php
                if ($demo_user_login == 'on') {
                    $_demo_candidate_obj = get_user_by('login', $demo_candidate);
                    $_demo_candidate_id = isset($_demo_candidate_obj->ID) ? $_demo_candidate_obj->ID : '';

                    $_demo_employer_obj = get_user_by('login', $demo_employer);
                    $_demo_employer_id = isset($_demo_employer_obj->ID) ? $_demo_employer_obj->ID : '';

                    if ($_demo_candidate_id != '' || $_demo_employer_id != '') {
                        ?>
                        <div class="jobsearch-box-title">
                            <span><?php esc_html_e('Choose your Account Type', 'wp-jobsearch') ?></span>
                        </div>
                        <div class="demo-login-btns jobsearch-user-options">
                            <ul class="jobsearch-user-type-choose">
                                <?php
                                if ($_demo_candidate_id != '') {
                                    ?>
                                    <li class="candidate-login active">
                                        <a href="javascript:void(0);"
                                           class="jobsearch-demo-login-btn candidate-login-btn">
                                            <i class="jobsearch-icon jobsearch-user"></i>
                                            <span><?php esc_html_e('Demo Candidate', 'wp-jobsearch') ?></span>
                                            <small><?php esc_html_e('Logged in as Candidate', 'wp-jobsearch') ?></small>
                                        </a>
                                    </li>
                                    <?php
                                }
                                if ($_demo_employer_id != '') { ?>
                                    <li class="employer-login">
                                        <a href="javascript:void(0);"
                                           class="jobsearch-demo-login-btn employer-login-btn">
                                            <i class="jobsearch-icon jobsearch-building"></i>
                                            <span><?php esc_html_e('Demo Employer', 'wp-jobsearch') ?></span>
                                            <small><?php esc_html_e('Logged in as Employer', 'wp-jobsearch') ?></small>
                                        </a>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>
                        <?php
                    }
                }
                ?>
                <div class="jobsearch-user-form">
                    <ul>
                        <li>
                            <label><?php echo ($uemail_field_title) . ':' ?></label>
                            <input class="required" name="pt_user_login" type="text"
                                   placeholder="<?php echo($uemail_field_title) ?>"/>
                            <i class="jobsearch-icon jobsearch-user"></i>
                        </li>
                        <li>
                            <label><?php _e('Password:', 'wp-jobsearch') ?></label>
                            <input class="required" name="pt_user_pass" type="password"
                                   placeholder="<?php _e('Password', 'wp-jobsearch') ?>">
                            <i class="jobsearch-icon jobsearch-multimedia"></i>
                        </li>
                        <li class="jobsearch-user-form-coltwo-full">
                            <input type="hidden" name="action" value="jobsearch_login_member_submit">
                            <input type="hidden" name="current_page_id" value="<?php echo get_the_ID() ?>">
                            <?php
                            ob_start();
                            ?>
                            <input data-id="<?php echo absint($rand_numb) ?>" class="jobsearch-login-submit-btn"
                                   data-loading-text="<?php _e('Loading...', 'wp-jobsearch') ?>" type="submit"
                                   value="<?php _e('Sign In', 'wp-jobsearch'); ?>">
                            <div class="form-loader"></div>
                            <div class="jobsearch-user-form-info">
                                <p><a href="javascript:void(0);" class="lost-password"
                                      data-id="<?php echo absint($rand_numb) ?>"><?php echo esc_html__("Forgot Password?", "wp-jobsearch"); ?></a><?php if ($register_form_view === true) { ?> |
                                        <a href="javascript:void(0);" class="register-form"
                                           data-id="<?php echo absint($rand_numb) ?>"><?php _e('Sign Up', 'wp-jobsearch') ?></a><?php } ?>
                                </p>
                                <div class="jobsearch-checkbox">
                                    <input type="checkbox" id="r-<?php echo($rand_numb) ?>" name="remember_password">
                                    <label for="r-<?php echo($rand_numb) ?>"><span></span> <?php _e('Save Password', 'wp-jobsearch') ?>
                                    </label>
                                </div>
                            </div>
                            <?php
                            $html = ob_get_clean();
                            echo apply_filters('jobsearch_login_popup_remember_info_links', $html, $rand_numb, $register_form_view);
                            ?>
                        </li>
                        <?php echo apply_filters('jobsearch_after_popup_login_formfields_html', '', $args); ?>
                    </ul>
                    <div class="login-reg-errors"></div>
                </div>
                <?php do_action('social_login_html', $args); ?>
            </form>
        </div>
        <div class="jobsearch-reset-password reset-password-<?php echo absint($rand_numb) ?>" style="display:none;">
            <div class="jobsearch-modal-title-box">
                <h2><?php _e('Reset Password', 'wp-jobsearch') ?></h2>
                <span class="modal-close"><i class="fa fa-times"></i></span>
            </div>
            <form id="reset-password-form-<?php echo absint($rand_numb) ?>" action="<?php echo home_url('/'); ?>"
                  method="post">
                <div class="jobsearch-user-form">
                    <ul>
                        <li class="jobsearch-user-form-coltwo-full">
                            <label><?php echo ($uemail_field_title) . ':' ?></label>
                            <input id="pt_user_or_email_<?php echo absint($rand_numb) ?>" class="required"
                                   name="pt_user_or_email" type="text"
                                   placeholder="<?php echo($uemail_field_title) ?>"/>
                            <i class="jobsearch-icon jobsearch-mail"></i>
                        </li>
                        <li class="jobsearch-user-form-coltwo-full">
                            <input type="hidden" name="action" value="jobsearch_reset_password">
                            <input data-id="<?php echo absint($rand_numb) ?>"
                                   class="jobsearch-reset-password-submit-btn" type="submit"
                                   value="<?php _e('Get a new password', 'wp-jobsearch'); ?>">

                            <div class="form-loader"></div>
                            <div class="jobsearch-user-form-info">
                                <p><a href="javascript:void(0);" class="login-form-btn"
                                      data-id="<?php echo absint($rand_numb) ?>"><?php echo esc_html__("Already have an account? Login", "wp-jobsearch"); ?></a>
                                </p>
                            </div>
                        </li>
                    </ul>

                    <p><?php _e('Enter the username or e-mail you used in your profile. A password reset link will be sent to you by email.', 'wp-jobsearch'); ?></p>

                    <div class="reset-password-errors"></div>
                </div>
            </form>

        </div>
        <?php
        if ($register_form_view === true) {
            $hide_emp_fields = true;
            if ($op_emp_register_allow != 'no' && $op_cand_register_allow == 'no') {
                $hide_emp_fields = false;
            }
            ?>
            <div class="jobsearch-register-form register-<?php echo absint($rand_numb) ?>" style="display:none;">
                <div class="jobsearch-modal-title-box">
                    <h2><?php _e('Signup to your Account', 'wp-jobsearch') ?></h2>
                    <span class="modal-close"><i class="fa fa-times"></i></span>
                </div>
                <form id="registration-form-<?php echo absint($rand_numb) ?>" action="<?php echo home_url('/'); ?>"
                      method="POST" enctype="multipart/form-data">

                    <?php
                    if ($op_cand_register_allow == 'no') {
                        ?>
                        <input type="hidden" name="pt_user_role" value="jobsearch_employer">
                        <?php
                    } else if ($op_emp_register_allow == 'no') { ?>
                        <input type="hidden" name="pt_user_role" value="jobsearch_candidate">
                        <?php
                    } else {
                        ob_start();
                        ?>
                        <div class="jobsearch-box-title">
                            <span><?php _e('Choose your Account Type', 'wp-jobsearch') ?></span>
                            <input type="hidden" name="pt_user_role" value="jobsearch_candidate">
                        </div>
                        <div class="jobsearch-user-options">
                            <ul class="jobsearch-user-type-choose">
                                <li class="active">
                                    <a href="javascript:void(0);" class="user-type-chose-btn"
                                       data-type="jobsearch_candidate">
                                        <i class="jobsearch-icon jobsearch-user"></i>
                                        <span><?php _e('Candidate', 'wp-jobsearch') ?></span>
                                        <small><?php _e('I want to discover awesome companies.', 'wp-jobsearch') ?></small>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="user-type-chose-btn"
                                       data-type="jobsearch_employer">
                                        <i class="jobsearch-icon jobsearch-building"></i>
                                        <span><?php _e('Employer', 'wp-jobsearch') ?></span>
                                        <small><?php _e('I want to attract the best talent.', 'wp-jobsearch') ?></small>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <?php
                        $chose_usert_html = ob_get_clean();
                        echo apply_filters('jobsearch_reg_popup_chose_usertype_html', $chose_usert_html);
                    }
                    ?>
                    <div class="jobsearch-user-form jobsearch-user-form-coltwo">
                        <ul>
                            <?php

                            do_action('jobsearch_registration_extra_fields_start');

                            ob_start();
                            if ($flnames_fields_allow == 'on') { ?>
                                <li>
                                    <label><?php _e('First Name *', 'wp-jobsearch') ?></label>
                                    <input class="jobsearch-regrequire-field" name="pt_user_fname" type="text"
                                           placeholder="<?php _e('First Name *', 'wp-jobsearch'); ?>" required>
                                    <i class="jobsearch-icon jobsearch-user"></i>
                                </li>
                                <li>
                                    <label><?php _e('Last Name *', 'wp-jobsearch') ?></label>
                                    <input class="jobsearch-regrequire-field" name="pt_user_lname" type="text"
                                           placeholder="<?php _e('Last Name *', 'wp-jobsearch'); ?>" required>
                                    <i class="jobsearch-icon jobsearch-user"></i>
                                </li>
                            <?php }
                            if ($username_field_allow == 'on') {
                                $username_label = esc_html__('Username *', 'wp-jobsearch');
                                $username_label = apply_filters('jobsearch_user_signup_username_label', $username_label);
                                ?>
                                <li>
                                    <label><?php echo ($username_label); ?></label>
                                    <input class="jobsearch-regrequire-field" name="pt_user_login" type="text"
                                           placeholder="<?php echo ($username_label); ?>"/>
                                    <i class="jobsearch-icon jobsearch-user"></i>
                                </li>
                                <?php
                            }
                            $useremail_label = esc_html__('Email *', 'wp-jobsearch');
                            $useremail_label = apply_filters('jobsearch_user_signup_useremail_label', $useremail_label);
                            ?>
                            <li <?php echo($username_field_allow == 'on' ? '' : 'class="jobsearch-user-form-coltwo-full"') ?>>
                                <label><?php echo ($useremail_label); ?></label>
                                <input class="jobsearch-regrequire-field" name="pt_user_email"
                                       id="pt_user_email_<?php echo absint($rand_numb) ?>" type="email"
                                       placeholder="<?php echo ($useremail_label); ?>"/>
                                <i class="jobsearch-icon jobsearch-mail"></i>
                            </li>
                            <?php
                            $public_profile_visi = isset($jobsearch_plugin_options['signup_public_profile_visibility']) ? $jobsearch_plugin_options['signup_public_profile_visibility'] : '';
                            $public_profile_check = false;
                            $user_base_class = '';
                            $profile_filed_style = '';
                            if ($public_profile_visi == 'for_emp') {
                                $public_profile_check = true;
                                $user_base_class = 'user-employer-spec-field';
                                $profile_filed_style = 'display: none;';
                                if ($op_emp_register_allow != 'no' && $op_cand_register_allow == 'no') {
                                    $profile_filed_style = 'display: inline-block;';
                                }
                            }
                            if ($public_profile_visi == 'for_cand') {
                                $public_profile_check = true;
                                $user_base_class = 'user-candidate-spec-field';
                            }
                            if ($public_profile_visi == 'for_both') {
                                $public_profile_check = true;
                            }
                            if ($profile_filed_style != '') {
                                $profile_filed_style = ' style="' . $profile_filed_style . '"';
                            }
                            if ($public_profile_check) { ?>
                                <li class="jobsearch-user-form-coltwo-full <?php echo($user_base_class) ?>"<?php echo($profile_filed_style) ?>>
                                    <label><?php _e('Visible Public Profile', 'wp-jobsearch') ?></label>
                                    <div class="jobsearch-profile-select">
                                        <select name="public_profile_visible" class="selectize-select"
                                                placeholder="<?php _e('Visible in Listing and Detail', 'wp-jobsearch') ?>">
                                            <option value="yes"><?php _e('Yes', 'wp-jobsearch') ?></option>
                                            <option value="no"><?php _e('No', 'wp-jobsearch') ?></option>
                                        </select>
                                    </div>
                                </li>
                                <?php
                            }

                            if ($pass_from_user == 'on') { ?>
                                <li>
                                    <label><?php _e('Password *', 'wp-jobsearch') ?></label>
                                    <input class="required jobsearch_chk_passfield" name="pt_user_pass"
                                           id="pt_user_pass_<?php echo absint($rand_numb) ?>" type="password"
                                           placeholder="<?php _e('Password', 'wp-jobsearch'); ?>"/>
                                    <span class="passlenth-chk-msg"></span>
                                    <i class="jobsearch-icon jobsearch-multimedia"></i>
                                </li>
                                <li>
                                    <label><?php _e('Confirm Password *', 'wp-jobsearch') ?></label>
                                    <input class="required" name="pt_user_cpass"
                                           id="pt_user_cpass_<?php echo absint($rand_numb) ?>" type="password"
                                           placeholder="<?php _e('Confirm Password', 'wp-jobsearch'); ?>"/>
                                    <i class="jobsearch-icon jobsearch-multimedia"></i>
                                </li>
                                <?php
                            }
                            $signup_user_phone = apply_filters('jobsearch_signup_phone_field_switch', $signup_user_phone);
                            if ($signup_user_phone != 'off') {
                                $phone_validation_type = isset($jobsearch_plugin_options['intltell_phone_validation']) ? $jobsearch_plugin_options['intltell_phone_validation'] : '';
                                $phone_validation_type = apply_filters('jobsearch_signup_phone_validation_type', $phone_validation_type);
                                ?>
                                <li class="jobsearch-user-form-coltwo-full">
                                    <label><?php _e('Phone:', 'wp-jobsearch') ?><?php echo($signup_user_phone == 'on_req' ? ' *' : '') ?></label>
                                    <?php
                                    if ($phone_validation_type == 'on') {
                                        wp_enqueue_script('jobsearch-intlTelInput');
                                        $itltell_input_ats = array(
                                            'set_before_vals' => 'all',
                                            'field_icon' => 'yes',
                                            'set_condial_intrvl' => 'yes',
                                        );
                                        if ($signup_user_phone == 'on_req') {
                                            $itltell_input_ats['is_required'] = true;
                                            $itltell_input_ats['classes'] = 'jobsearch-regrequire-field';
                                        }
                                        jobsearch_phonenum_itltell_input('pt_user_phone', $rand_numb, '', $itltell_input_ats);
                                    } else {
                                        ?>
                                        <input class="<?php echo ($signup_user_phone == 'on_req' ? 'jobsearch-regrequire-field' : 'required') ?>" name="pt_user_phone"
                                               id="pt_user_phone_<?php echo absint($rand_numb) ?>" type="tel"
                                               placeholder="<?php _e('Phone Number', 'wp-jobsearch'); ?>">
                                        <i class="jobsearch-icon jobsearch-technology"></i>
                                        <?php
                                    }
                                    ?>
                                </li>
                                <?php
                            }
                            if ($signup_org_name == 'on') {
                                $organization_disply = 'none';
                                if ($op_emp_register_allow != 'no' && $op_cand_register_allow == 'no') {
                                    $organization_disply = 'inline-block';
                                }
                                ob_start();
                                ?>
                                <li class="user-employer-spec-field jobsearch-user-form-coltwo-full"
                                    style="display: <?php echo apply_filters('jobsearch_orgnization_regnamefield_popup', $organization_disply) ?>;">
                                    <label><?php _e('Organization Name', 'wp-jobsearch') ?></label>
                                    <input class="required" name="pt_user_organization"
                                           id="pt_user_organization_<?php echo absint($rand_numb) ?>" type="text"
                                           placeholder="<?php _e('Organization Name', 'wp-jobsearch'); ?>"/>
                                    <i class="jobsearch-icon jobsearch-briefcase"></i>
                                </li>
                                <?php
                                $org_field_html = ob_get_clean();
                                echo apply_filters('jobsearch_usereg_form_emp_orgname_field', $org_field_html);
                            }
                            if ($signup_user_sector != 'off') {
                                $sector_selct_method = isset($jobsearch_plugin_options['signup_sector_selct_method']) ? $jobsearch_plugin_options['signup_sector_selct_method'] : '';
                                if ($sector_selct_method == 'multi' || $sector_selct_method == 'multi_req') {
                                    $selct_sector_title = esc_html__('Select Sectors', 'wp-jobsearch');
                                    $selct_sector_class = 'selectize-select';
                                    if ($sector_selct_method == 'multi_req') {
                                        $selct_sector_title = esc_html__('Select Sectors *', 'wp-jobsearch');
                                        $selct_sector_class = 'jobsearch-regrequire-field multiselect-req selectize-select';
                                    }
                                } else {
                                    $selct_sector_title = esc_html__('Select Sector', 'wp-jobsearch');
                                    $selct_sector_class = 'selectize-select';
                                    if ($sector_selct_method == 'single_req') {
                                        $selct_sector_title = esc_html__('Select Sector *', 'wp-jobsearch');
                                        $selct_sector_class = 'jobsearch-regrequire-field selectize-select';
                                    }
                                }
                                
                                $signup_sectr_hideclas = '';
                                $signup_sectr_ishide = false;
                                if ($signup_user_sector == 'emp') {
                                    $signup_sectr_hideclas = ' user-employer-spec-field';
                                    if ($hide_emp_fields) {
                                        $signup_sectr_ishide = true;
                                    }
                                }
                                if ($signup_user_sector == 'cand') {
                                    $signup_sectr_hideclas = ' user-candidate-spec-field';
                                }
                                
                                $multi_sector = false;
                                ?>
                                <li class="jobsearch-user-form-coltwo-full jobsearch-regfield-sector<?php echo ($signup_sectr_hideclas) ?>"<?php echo ($signup_sectr_ishide ? ' style="display: none;"' : '') ?>>
                                    <label><?php echo ($selct_sector_title) ?></label>
                                    <div class="jobsearch-profile-select">
                                        <?php
                                        if ($sector_selct_method == 'multi' || $sector_selct_method == 'multi_req') {
                                            $multi_sector = true;
                                            $jobsector_args = array(
                                                'orderby' => 'name',
                                                'order' => 'ASC',
                                                'fields' => 'all',
                                                'slug' => '',
                                                'hide_empty' => false,
                                            );
                                            $all_sectors = get_terms('sector', $jobsector_args);
                                            ob_start();
                                            if (!empty($all_sectors)) {
                                                ?>
                                                <select id="pt_user_category_<?php echo absint($rand_numb) ?>" name="pt_user_category[]" class="<?php echo ($selct_sector_class) ?>" multiple="" placeholder="<?php esc_html_e('Select Sectors', 'wp-jobsearch') ?>">
                                                    <?php
                                                    foreach ($all_sectors as $sector_obj) {
                                                        $term_id = $sector_obj->term_id;
                                                        ?>
                                                        <option value="<?php echo (string)($term_id) ?>"><?php echo ($sector_obj->name) ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                                <?php
                                            }
                                            $sector_sel_html = ob_get_clean();
                                            echo apply_filters('jobsearch_sector_select_tag_html', $sector_sel_html, 0);
                                        } else {
                                            $sector_args = array(
                                                'show_option_all' => esc_html__('Select Sector', 'wp-jobsearch'),
                                                'show_option_none' => '',
                                                'option_none_value' => '',
                                                'orderby' => 'title',
                                                'order' => 'ASC',
                                                'show_count' => 0,
                                                'hide_empty' => 0,
                                                'echo' => 0,
                                                'selected' => '',
                                                'hierarchical' => 1,
                                                'id' => 'pt_user_category_' . absint($rand_numb),
                                                'class' => $selct_sector_class,
                                                'name' => 'pt_user_category',
                                                'depth' => 0,
                                                'taxonomy' => 'sector',
                                                'hide_if_empty' => false,
                                                'value_field' => 'term_id',
                                            );
                                            $sector_sel_html = wp_dropdown_categories($sector_args);
                                            echo apply_filters('jobsearch_sector_select_tag_html', $sector_sel_html, 0);
                                        }
                                        ?>
                                    </div>
                                    <?php
                                    if ($multi_sector === false) {
                                        ?>
                                        <script type="text/javascript">
                                            jQuery('#pt_user_category_<?php echo absint($rand_numb) ?>').find('option').first().val('');
                                            jQuery('#pt_user_category_<?php echo absint($rand_numb) ?>').attr('placeholder', '<?php esc_html_e('Select Sector', 'wp-jobsearch') ?>');
                                        </script>
                                        <?php
                                    }
                                    ?>
                                </li>
                                <?php
                            }
                            //
                            do_action('jobsearch_registration_extra_fields_after_sector', $args);

                            if (($signup_cv_upload == 'on' || $signup_cv_upload == 'on_req') && $op_cand_register_allow != 'no') {
                                $file_sizes_arr = array(
                                    '300' => __('300KB', 'wp-jobsearch'),
                                    '500' => __('500KB', 'wp-jobsearch'),
                                    '750' => __('750KB', 'wp-jobsearch'),
                                    '1024' => __('1Mb', 'wp-jobsearch'),
                                    '2048' => __('2Mb', 'wp-jobsearch'),
                                    '3072' => __('3Mb', 'wp-jobsearch'),
                                    '4096' => __('4Mb', 'wp-jobsearch'),
                                    '5120' => __('5Mb', 'wp-jobsearch'),
                                    '10120' => __('10Mb', 'wp-jobsearch'),
                                    '50120' => __('50Mb', 'wp-jobsearch'),
                                    '100120' => __('100Mb', 'wp-jobsearch'),
                                    '200120' => __('200Mb', 'wp-jobsearch'),
                                    '300120' => __('300Mb', 'wp-jobsearch'),
                                    '500120' => __('500Mb', 'wp-jobsearch'),
                                    '1000120' => __('1Gb', 'wp-jobsearch'),
                                );
                                $cvfile_size = '5120';
                                $cvfile_size_str = __('5 Mb', 'wp-jobsearch');
                                $cand_cv_file_size = isset($jobsearch_plugin_options['cand_cv_file_size']) ? $jobsearch_plugin_options['cand_cv_file_size'] : '';
                                if (isset($file_sizes_arr[$cand_cv_file_size])) {
                                    $cvfile_size = $cand_cv_file_size;
                                    $cvfile_size_str = $file_sizes_arr[$cand_cv_file_size];
                                }
                                $filesize_act = ceil($cvfile_size / 1024);

                                $cand_files_types = isset($jobsearch_plugin_options['cand_cv_types']) ? $jobsearch_plugin_options['cand_cv_types'] : '';

                                if (empty($cand_files_types)) {
                                    $cand_files_types = array(
                                        'application/msword',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'application/pdf',
                                    );
                                }
                                $cand_files_types_json = json_encode($cand_files_types);
                                $cand_files_types_json = stripslashes($cand_files_types_json);
                                $sutable_files_arr = array();
                                $file_typs_comarr = array(
                                    'text/plain' => __('text', 'wp-jobsearch'),
                                    'image/jpeg' => __('jpeg', 'wp-jobsearch'),
                                    'image/png' => __('png', 'wp-jobsearch'),
                                    'application/msword' => __('doc', 'wp-jobsearch'),
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => __('docx', 'wp-jobsearch'),
                                    'application/vnd.ms-excel' => __('xls', 'wp-jobsearch'),
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => __('xlsx', 'wp-jobsearch'),
                                    'application/pdf' => __('pdf', 'wp-jobsearch'),
                                );
                                foreach ($file_typs_comarr as $file_typ_key => $file_typ_comar) {
                                    if (in_array($file_typ_key, $cand_files_types)) {
                                        $sutable_files_arr[] = '.' . $file_typ_comar;
                                    }
                                }
                                $sutable_files_str = implode(', ', $sutable_files_arr);
                                ?>
                                <li class="user-candidate-spec-field jobsearch-user-form-coltwo-full">
                                    <div id="jobsearch-upload-cv-main<?php echo($rand_numb) ?>"
                                         class="jobsearch-upload-cv jobsearch-signup-upload-cv">
                                        <label><?php _e('Upload Resume', 'wp-jobsearch') ?><?php echo($signup_cv_upload == 'on_req' ? ' *' : '') ?></label>
                                        <div class="jobsearch-drpzon-con jobsearch-drag-dropcustom">
                                            <div id="cvFilesDropzone<?php echo($rand_numb) ?>" class="dropzone"
                                                 ondragover="jobsearch_dragover_evnt<?php echo($rand_numb) ?>(event)"
                                                 ondragleave="jobsearch_leavedrop_evnt<?php echo($rand_numb) ?>(event)"
                                                 ondrop="jobsearch_ondrop_evnt<?php echo($rand_numb) ?>(event)">
                                                <input type="file" id="cand_cv_filefield<?php echo($rand_numb) ?>"
                                                       class="jobsearch-upload-btn <?php echo($signup_cv_upload == 'on_req' ? 'cv_is_req' : '') ?>"
                                                       name="candidate_cv_file">
                                                <div class="fileContainerFileName"
                                                     ondrop="jobsearch_ondrop_evnt<?php echo($rand_numb) ?>(event)"
                                                     id="fileNameContainer<?php echo($rand_numb) ?>">
                                                    <div class="dz-message jobsearch-dropzone-template">
                                                        <span class="upload-icon-con"><i
                                                                    class="jobsearch-icon jobsearch-upload"></i></span>
                                                        <strong><?php esc_html_e('Drop a resume file or click to upload.', 'wp-jobsearch') ?></strong>
                                                        <div class="upload-inffo"><?php printf(__('To upload file size is <span>(Max %s)</span> <span class="uplod-info-and">and</span> allowed file types are <span>(%s)</span>', 'wp-jobsearch'), $cvfile_size_str, $sutable_files_str) ?></div>
                                                        <div class="upload-or-con">
                                                            <span><?php esc_html_e('or', 'wp-jobsearch') ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <a class="jobsearch-drpzon-btn"><i
                                                            class="jobsearch-icon jobsearch-arrows-2"></i> <?php esc_html_e('Upload Resume', 'wp-jobsearch') ?>
                                                </a>
                                            </div>
                                            <script type="text/javascript">
                                                jQuery('#cvFilesDropzone<?php echo($rand_numb) ?>').find('input[name=candidate_cv_file]').css({
                                                    position: 'absolute',
                                                    width: '100%',
                                                    height: '100%',
                                                    top: '0',
                                                    left: '0',
                                                    opacity: '0',
                                                    'z-index': '9',
                                                });

                                                jQuery(document).on('change', 'input#cand_cv_filefield<?php echo($rand_numb) ?>', function () {
                                                    if (this.files && this.files[0]) {
                                                        var upcv_file = this.files[0];
                                                        var upcv_file_type = upcv_file.type;

                                                        var cvup_allowed_types = '<?php echo($cand_files_types_json) ?>';

                                                        if (cvup_allowed_types.indexOf(upcv_file_type) >= 0) {
                                                            var the_show_msg = '<?php esc_html_e('No file has been selected', 'wp-jobsearch') ?>';
                                                            if (this.files.length > 0) {
                                                                var slected_file_name = this.files[0].name;
                                                                the_show_msg = '<?php esc_html_e('The file', 'wp-jobsearch') ?> "' + slected_file_name + '" <?php esc_html_e('has been selected', 'wp-jobsearch') ?>';
                                                            }
                                                            document.getElementById('cvFilesDropzone<?php echo($rand_numb) ?>').classList.remove('fileContainerDragOver');
                                                            try {
                                                                droppedFiles = document.getElementById('cand_cv_filefield<?php echo($rand_numb) ?>').files;
                                                                document.getElementById('fileNameContainer<?php echo($rand_numb) ?>').textContent = the_show_msg;
                                                            } catch (error) {
                                                                ;
                                                            }
                                                            try {
                                                                aName = document.getElementById('cand_cv_filefield<?php echo($rand_numb) ?>').value;
                                                                if (aName !== '') {
                                                                    document.getElementById('fileNameContainer<?php echo($rand_numb) ?>').textContent = the_show_msg;
                                                                }
                                                            } catch (error) {
                                                                ;
                                                            }
                                                        } else {
                                                            alert('<?php esc_html_e('This file type is not allowed.', 'wp-jobsearch') ?>');
                                                        }
                                                    }
                                                });

                                                function jobsearch_ondrop_evnt<?php echo($rand_numb) ?>(e) {
                                                    var the_show_msg = '<?php esc_html_e('No file has been selected', 'wp-jobsearch') ?>';
                                                    if (e.target.files.length > 0) {
                                                        var slected_file_name = e.target.files[0].name;
                                                        the_show_msg = '<?php esc_html_e('The file', 'wp-jobsearch') ?> "' + slected_file_name + '" <?php esc_html_e('has been selected', 'wp-jobsearch') ?>';
                                                    }
                                                    document.getElementById('cvFilesDropzone<?php echo($rand_numb) ?>').classList.remove('fileContainerDragOver');
                                                    try {
                                                        droppedFiles = e.dataTransfer.files;
                                                        document.getElementById('fileNameContainer<?php echo($rand_numb) ?>').textContent = the_show_msg;
                                                    } catch (error) {
                                                        ;
                                                    }
                                                }

                                                function jobsearch_dragover_evnt<?php echo($rand_numb) ?>(e) {
                                                    document.getElementById('cvFilesDropzone<?php echo($rand_numb) ?>').classList.add('fileContainerDragOver');
                                                    e.preventDefault();
                                                    e.stopPropagation();
                                                }

                                                function jobsearch_leavedrop_evnt<?php echo($rand_numb) ?>(e) {
                                                    document.getElementById('cvFilesDropzone<?php echo($rand_numb) ?>').classList.remove('fileContainerDragOver');
                                                }
                                            </script>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            }
                            $normfields_html = ob_get_clean();
                            echo apply_filters('jobsearch_popup_regform_normfields_html', $normfields_html, $args);
                            //

                            $emp_cfields_dis = 'none';
                            $cand_cfields_dis = 'inline-block';
                            if ($op_emp_register_allow != 'no' && $op_cand_register_allow == 'no') {
                                $emp_cfields_dis = 'inline-block';
                                $cand_cfields_dis = 'none';
                            }
                            do_action('jobsearch_signup_custom_fields_load', 0, 'candidate', $cand_cfields_dis);
                            do_action('jobsearch_signup_custom_fields_load', 0, 'employer', $emp_cfields_dis);

                            do_action('jobsearch_registration_extra_fields_end');
                            
                            if ($captcha_switch == 'on' && !is_user_logged_in()) {
                                wp_enqueue_script('jobsearch_google_recaptcha');
                                ?>
                                <li class="jobsearch-user-form-coltwo-full">
                                    <script type="text/javascript">
                                        var recaptcha_popup;
                                        var jobsearch_multicap = function () {
                                            //Render the recaptcha_popup on the element with ID "recaptcha_popup"
                                            recaptcha_popup = grecaptcha.render('recaptcha_popup', {
                                                'sitekey': '<?php echo($jobsearch_sitekey); ?>', //Replace this with your Site key
                                                'theme': 'light'
                                            });
                                        };
                                        jQuery(document).ready(function () {
                                            jQuery('.recaptcha-reload-a').click();
                                        });
                                    </script>
                                    <div class="recaptcha-reload" id="recaptcha_popup_div">
                                        <?php echo jobsearch_recaptcha('recaptcha_popup'); ?>
                                    </div>
                                </li>
                                <?php
                            }
                            ?>
                            <li class="jobsearch-user-form-coltwo-full">
                                <?php
                                ob_start();
                                jobsearch_terms_and_con_link_txt();
                                $trms_con_html = ob_get_clean();
                                echo apply_filters('jobsearch_terms_and_con_link_txt_regpopup', $trms_con_html);
                                ?>
                                <input type="hidden" name="action" value="jobsearch_register_member_submit">
                                <?php
                                ob_start();
                                if ($pass_from_user == 'on') { ?>
                                    <input data-id="<?php echo absint($rand_numb) ?>"
                                           class="jobsearch-register-submit-btn jobsearch-regpass-frmbtn jobsearch-disable-btn"
                                           disabled data-loading-text="<?php _e('Loading...', 'wp-jobsearch') ?>"
                                           type="submit" value="<?php _e('Sign up', 'wp-jobsearch'); ?>">
                                <?php } else { ?>
                                    <input data-id="<?php echo absint($rand_numb) ?>"
                                           class="jobsearch-register-submit-btn"
                                           data-loading-text="<?php _e('Loading...', 'wp-jobsearch') ?>" type="submit"
                                           value="<?php _e('Sign up', 'wp-jobsearch'); ?>">
                                    <?php
                                }
                                $signup_btn_html = ob_get_clean();
                                echo apply_filters('jobsearch_signup_popup_form_submit_btn', $signup_btn_html, $rand_numb, $args);
                                ?>
                                <div class="form-loader"></div>

                                <div class="jobsearch-user-form-info">
                                    <p><a href="javascript:void(0);" class="reg-tologin-btn"
                                          data-id="<?php echo absint($rand_numb) ?>"><?php echo esc_html__("Already have an account? Login", "wp-jobsearch"); ?></a>
                                    </p>
                                </div>
                            </li>
                        </ul>
                        <div class="clearfix"></div>

                        <div class="registration-errors"></div>
                        <?php do_action('jobsearch_after_regform_html_action', 'register-security'); ?>
                    </div>
                    <?php do_action('social_login_html', $args); ?>
                </form>
            </div>
            <?php
        }
        $html = ob_get_clean();
        echo apply_filters('jobsearch_loginreg_popup_whole_html', $html, $args);
    }

    public function login_form_html_callback($arg)
    {
        global $jobsearch_plugin_options;
        ob_start();
        $html = '';
        $rand_numb = rand(1000000, 9999999);

        //
        $flnames_fields_allow = isset($jobsearch_plugin_options['signup_user_flname']) ? $jobsearch_plugin_options['signup_user_flname'] : '';
        $username_field_allow = isset($jobsearch_plugin_options['signup_username_allow']) ? $jobsearch_plugin_options['signup_username_allow'] : '';

        $uemail_field_title = esc_html__('Email Address', 'wp-jobsearch');
        if ($username_field_allow == 'on') {
            $uemail_field_title = esc_html__('Username/Email Address', 'wp-jobsearch');
        }

        if (!is_user_logged_in()) { // only show the registration/login form to non-logged-in members
            $demo_user_login = isset($jobsearch_plugin_options['demo_user_login']) ? $jobsearch_plugin_options['demo_user_login'] : '';
            $demo_candidate = isset($jobsearch_plugin_options['demo_candidate']) ? $jobsearch_plugin_options['demo_candidate'] : '';
            $demo_employer = isset($jobsearch_plugin_options['demo_employer']) ? $jobsearch_plugin_options['demo_employer'] : '';

            $adm_user_obj = get_user_by('login', 'jobsearch-admin');
            if (is_object($adm_user_obj) && isset($adm_user_obj->ID) && in_array('administrator', jobsearch_get_user_roles_by_user_id($adm_user_obj->ID))) {
                $demo_user_login = $demo_user_login;
            } else {
                $demo_user_login = 'off';
            }
            ob_start();
            ?>
            <div class="login-form login-form-<?php echo absint($rand_numb) ?>">
                <?php
                ob_start();
                ?>
                <h2><?php _e('Login to our site', 'wp-jobsearch') ?></h2>
                <?php
                $login_title = ob_get_clean();
                echo apply_filters('jobsearch_loginsignup_login_box_title', $login_title);
                if ($demo_user_login == 'on') {
                    $_demo_candidate_obj = get_user_by('login', $demo_candidate);
                    $_demo_candidate_id = isset($_demo_candidate_obj->ID) ? $_demo_candidate_obj->ID : '';

                    $_demo_employer_obj = get_user_by('login', $demo_employer);
                    $_demo_employer_id = isset($_demo_employer_obj->ID) ? $_demo_employer_obj->ID : '';

                    if ($_demo_candidate_id != '' || $_demo_employer_id != '') { ?>
                        <div class="demo-login-pbtns jobsearch-roles-container">
                            <?php if ($_demo_candidate_id != '') { ?>
                                <div class="jobsearch-radio-checkbox candidate-login active">
                                    <a href="javascript:void(0);"
                                       class="jobsearch-demo-login-btn candidate-login-btn"><i
                                                class="jobsearch-icon jobsearch-user"></i> <?php esc_html_e('Demo Candidate', 'wp-jobsearch') ?>
                                    </a>
                                </div>
                                <?php
                            }
                            if ($_demo_employer_id != '') { ?>
                                <div class="jobsearch-radio-checkbox employer-login">
                                    <a href="javascript:void(0);" class="jobsearch-demo-login-btn employer-login-btn"><i
                                                class="jobsearch-icon jobsearch-building"></i> <?php esc_html_e('Demo Employer', 'wp-jobsearch') ?>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                        <?php
                    }
                }
                ?>
                <span class="enter-userpass-txt"><?php _e('Enter the username and password to login:', 'wp-jobsearch') ?></span>
                <form id="login-form-<?php echo absint($rand_numb) ?>" action="<?php echo home_url('/'); ?>" method="post">
                    <ul>
                        <li>
                            <input class="form-control input-lg required" name="pt_user_login" type="text"
                                   placeholder="<?php echo($uemail_field_title) ?>"/>
                        </li>
                        <li>
                            <input class="form-control input-lg required" name="pt_user_pass" type="password"
                                   placeholder="<?php _e('Password', 'wp-jobsearch') ?>">
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="lost-password"
                               data-id="<?php echo absint($rand_numb) ?>"><?php echo esc_html__("Forgot your password?", "wp-jobsearch"); ?></a>
                            <input type="hidden" name="action" value="jobsearch_login_member_submit"/>
                            <input type="hidden" name="current_page_id" value="<?php echo get_the_ID() ?>">
                            <button data-id="<?php echo absint($rand_numb) ?>"
                                    class="jobsearch-login-submit-btn btn btn-theme btn-lg"
                                    data-loading-text="<?php _e('Loading...', 'wp-jobsearch') ?>"
                                    type="submit"><?php echo apply_filters('jobsearch_login_temp_loginboxform_btntitle', __('Login', 'wp-jobsearch')); ?></button>
                            <div class="form-loader"></div>
                        </li>
                        <?php echo apply_filters('jobsearch_after_login_formfields_html', '', $arg); ?>
                    </ul>

                    <div class="login-reg-errors"></div>
                </form>
            </div>
            <?php
            $login_box_html = ob_get_clean();
            echo apply_filters('jobsearch_login_pform_box_html', $login_box_html, $rand_numb, $arg);

            //
            ob_start();
            ?>
            <div class="pt-reset-password reset-password-<?php echo absint($rand_numb) ?>" style="display:none;">

                <h2><?php _e('Reset Password', 'wp-jobsearch'); ?></h2>

                <form id="reset-password-form-<?php echo absint($rand_numb) ?>" action="<?php echo home_url('/'); ?>"
                      method="post">
                    <ul>
                        <li>
                            <input id="pt_user_or_email" class="form-control input-lg required" name="pt_user_or_email"
                                   type="text" placeholder="<?php echo($uemail_field_title) ?>"/>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="login-form-btn"
                               data-id="<?php echo absint($rand_numb) ?>"><?php echo esc_html__("Already have an account? Login", "wp-jobsearch"); ?></a>
                            <input type="hidden" name="action" value="jobsearch_reset_password">
                            <button data-id="<?php echo absint($rand_numb) ?>"
                                    class="jobsearch-reset-password-submit-btn btn btn-theme btn-lg"
                                    data-loading-text="<?php _e('Loading...', 'wp-jobsearch') ?>"
                                    type="submit"><?php _e('Get a new password', 'wp-jobsearch'); ?></button>
                            <div class="form-loader"></div>
                        </li>
                    </ul>

                    <p><?php _e('Enter the username or e-mail you used in your profile. A password reset link will be sent to you by email.', 'wp-jobsearch'); ?></p>

                    <div class="reset-password-errors"></div>
                </form>

            </div>
            <?php
            $forgpass_box_html = ob_get_clean();
            echo apply_filters('jobsearch_forgpass_pform_box_html', $forgpass_box_html, $rand_numb, $arg);
            ?>
            <div class="pt-loading" style="display:none;">
                <p><i class="fa fa-refresh fa-spin"></i><br><?php _e('Loading...', 'wp-jobsearch') ?></p>
            </div>
            <?php
        } else {
            ?>
            <div class="login-reg-logout">
                <div class="alert alert-info">
                    <?php
                    $current_user = wp_get_current_user();
                    printf(__('You have already logged in as %1$s. <a href="%2$s">Logout?</a>', 'wp-jobsearch'), $current_user->user_login, wp_logout_url(home_url('/')));
                    ?>
                </div>
                <div class="pt-errors"></div>
            </div>
            <?php
        }
        $html = ob_get_clean();
        echo force_balance_tags($html);
    }

    public function registration_form_html_callback($arg)
    {
        global $jobsearch_plugin_options;

        $flnames_fields_allow = isset($jobsearch_plugin_options['signup_user_flname']) ? $jobsearch_plugin_options['signup_user_flname'] : '';
        $username_field_allow = isset($jobsearch_plugin_options['signup_username_allow']) ? $jobsearch_plugin_options['signup_username_allow'] : '';

        $uemail_field_title = esc_html__('Email Address', 'wp-jobsearch');
        if ($username_field_allow == 'on') {
            $uemail_field_title = esc_html__('Username/Email Address', 'wp-jobsearch');
        }

        $captcha_switch = isset($jobsearch_plugin_options['captcha_switch']) ? $jobsearch_plugin_options['captcha_switch'] : '';
        $jobsearch_sitekey = isset($jobsearch_plugin_options['captcha_sitekey']) ? $jobsearch_plugin_options['captcha_sitekey'] : '';
        $op_cand_register_allow = isset($jobsearch_plugin_options['login_candidate_register']) ? $jobsearch_plugin_options['login_candidate_register'] : '';
        $op_emp_register_allow = isset($jobsearch_plugin_options['login_employer_register']) ? $jobsearch_plugin_options['login_employer_register'] : '';
        $cand_register_allow = isset($arg['login_candidate_register']) ? $arg['login_candidate_register'] : '';
        $emp_register_allow = isset($arg['login_employer_register']) ? $arg['login_employer_register'] : '';
        $signup_cv_upload = isset($jobsearch_plugin_options['signup_cv_upload']) ? $jobsearch_plugin_options['signup_cv_upload'] : '';

        $cand_register_view = true;
        if ($op_cand_register_allow == 'no') {
            $cand_register_view = false;
        }
        if ($cand_register_allow == 'no') {
            $cand_register_view = false;
        } else {
            $cand_register_view = true;
        }

        $emp_register_view = true;
        if ($op_emp_register_allow == 'no') {
            $emp_register_view = false;
        }
        if ($emp_register_allow == 'no') {
            $emp_register_view = false;
        } else {
            $emp_register_view = true;
        }

        ob_start();
        $html = '';
        $rand_numb = rand(1000000, 9999999);
        if (!is_user_logged_in()) {
            //
            $signup_user_sector = isset($jobsearch_plugin_options['signup_user_sector']) ? $jobsearch_plugin_options['signup_user_sector'] : '';
            $signup_org_name = isset($jobsearch_plugin_options['signup_organization_name']) ? $jobsearch_plugin_options['signup_organization_name'] : '';
            $signup_user_phone = isset($jobsearch_plugin_options['signup_user_phone']) ? $jobsearch_plugin_options['signup_user_phone'] : '';
            $pass_from_user = isset($jobsearch_plugin_options['signup_user_password']) ? $jobsearch_plugin_options['signup_user_password'] : '';

            $hide_emp_fields = true;
            if ($emp_register_view !== false && $cand_register_view === false) {
                $hide_emp_fields = false;
            }
            ?>
            <!-- Register form -->
            <div class="pt-register">
                <?php
                //if (get_option('users_can_register')) { 
                ob_start();
                ?>
                <h2><?php _e('Sign up now', 'wp-jobsearch'); ?></h2>
                <?php
                $login_title = ob_get_clean();
                echo apply_filters('jobsearch_loginsignup_reg_box_title', $login_title);
                ?>
                <span><?php echo apply_filters('jobsearch_loginsignup_reg_box_top_tagline', __('Fill the form below to get instant access:', 'wp-jobsearch')); ?></span>
                <form id="registration-form-<?php echo absint($rand_numb) ?>" action="<?php echo home_url('/'); ?>" method="POST">

                    <ul>
                        <?php do_action('jobsearch_registration_extra_fields_start') ?>

                        <li>
                            <?php
                            if ($cand_register_view === false) { ?>
                                <input type="hidden" name="pt_user_role" value="jobsearch_employer">
                            <?php } else if ($emp_register_view === false) { ?>
                                <input type="hidden" name="pt_user_role" value="jobsearch_candidate">
                                <?php
                            } else {
                                ob_start();
                                ?>
                                <div class="jobsearch-roles-container">
                                    <div class="jobsearch-radio-checkbox">
                                        <input id="candidate-role-<?php echo($rand_numb) ?>" type="radio"
                                               name="pt_user_role" value="jobsearch_candidate" checked="checked"> <label
                                                for="candidate-role-<?php echo($rand_numb) ?>"><i
                                                    class="jobsearch-icon jobsearch-user"></i> <?php echo apply_filters('jobsearch_logintemp_page_regbox_candtab_text', esc_html__('Candidate', 'wp-jobsearch')) ?>
                                        </label>
                                    </div>
                                    <div class="jobsearch-radio-checkbox">
                                        <input id="employer-role-<?php echo($rand_numb) ?>" type="radio"
                                               name="pt_user_role" value="jobsearch_employer"> <label
                                                for="employer-role-<?php echo($rand_numb) ?>"><i
                                                    class="jobsearch-icon jobsearch-building"></i> <?php esc_html_e('Employer', 'wp-jobsearch') ?>
                                        </label>
                                    </div>
                                </div>
                                <?php
                                $chose_usert_html = ob_get_clean();
                                echo apply_filters('jobsearch_reg_page_chose_usertype_html', $chose_usert_html, $rand_numb);
                            }
                            ?>
                        </li>
                        <?php
                        ob_start();

                        if ($flnames_fields_allow == 'on') { ?>
                            <li>
                                <input class="form-control input-lg jobsearch-regrequire-field" name="pt_user_fname" type="text"
                                       placeholder="<?php _e('First Name *', 'wp-jobsearch'); ?>" required>
                            </li>
                            <li>
                                <input class="form-control input-lg jobsearch-regrequire-field" name="pt_user_lname" type="text"
                                       placeholder="<?php _e('Last Name *', 'wp-jobsearch'); ?>" required>
                            </li>
                            <?php
                        }
                        if ($username_field_allow == 'on') {
                            $username_label = esc_html__('Username *', 'wp-jobsearch');
                            $username_label = apply_filters('jobsearch_user_signup_username_label', $username_label);
                            ?>
                            <li>
                                <input class="form-control input-lg jobsearch-regrequire-field" name="pt_user_login" type="text"
                                       placeholder="<?php echo ($username_label); ?>"/>
                            </li>
                            <?php
                        }
                        $useremail_label = esc_html__('Email *', 'wp-jobsearch');
                        $useremail_label = apply_filters('jobsearch_user_signup_useremail_label', $useremail_label);
                        ?>
                        <li>
                            <input class="form-control input-lg jobsearch-regrequire-field" name="pt_user_email" id="pt_user_email"
                                   type="email" placeholder="<?php echo ($useremail_label); ?>"/>
                        </li>
                        <?php
                        $signup_user_phone = apply_filters('jobsearch_signup_phone_field_switch', $signup_user_phone);
                        if ($signup_user_phone != 'off') {
                            $phone_validation_type = isset($jobsearch_plugin_options['intltell_phone_validation']) ? $jobsearch_plugin_options['intltell_phone_validation'] : '';
                            $phone_validation_type = apply_filters('jobsearch_signup_phone_validation_type', $phone_validation_type);
                            ?>
                            <li>
                                <?php
                                if ($phone_validation_type == 'on') {
                                    wp_enqueue_script('jobsearch-intlTelInput');
                                    $itltell_input_ats = array(
                                        'set_before_vals' => 'all',
                                    );
                                    if ($signup_user_phone == 'on_req') {
                                        $itltell_input_ats['is_required'] = true;
                                        $itltell_input_ats['classes'] = 'jobsearch-regrequire-field';
                                    }
                                    jobsearch_phonenum_itltell_input('pt_user_phone', $rand_numb, '', $itltell_input_ats);
                                } else {
                                    ?>
                                    <input class="<?php echo ($signup_user_phone == 'on_req' ? 'jobsearch-regrequire-field' : 'required') ?>" name="pt_user_phone" id="pt_user_phone" type="tel"
                                           placeholder="<?php _e('Phone Number', 'wp-jobsearch'); ?><?php echo ($signup_user_phone == 'on_req' ? ' *' : '') ?>"/>
                                    <?php
                                }
                                ?>
                            </li>
                            <?php
                        }
                        if ($signup_org_name == 'on') {
                            $organization_disply = 'none';
                            if ($emp_register_view !== false && $cand_register_view === false) {
                                $organization_disply = 'inline-block';
                            }
                            ob_start();
                            ?>
                            <li class="user-employer-spec-field"
                                style="display: <?php echo apply_filters('jobsearch_orgnization_regnamefield_page', $organization_disply) ?>;">
                                <input class="required" name="pt_user_organization" id="pt_user_organization"
                                       type="text" placeholder="<?php _e('Organization Name', 'wp-jobsearch'); ?>"/>
                            </li>
                            <?php
                            $org_field_html = ob_get_clean();
                            echo apply_filters('jobsearch_usereg_form_emp_orgname_field', $org_field_html);
                        }

                        $public_profile_visi = isset($jobsearch_plugin_options['signup_public_profile_visibility']) ? $jobsearch_plugin_options['signup_public_profile_visibility'] : '';
                        $public_profile_check = false;
                        $user_base_class = '';
                        $profile_filed_style = '';
                        if ($public_profile_visi == 'for_emp') {
                            $public_profile_check = true;
                            $user_base_class = 'user-employer-spec-field';
                            $profile_filed_style = 'display: none;';
                            if ($emp_register_view !== false && $cand_register_view === false) {
                                $profile_filed_style = 'display: inline-block;';
                            }
                        }
                        if ($public_profile_visi == 'for_cand') {
                            $public_profile_check = true;
                            $user_base_class = 'user-candidate-spec-field';
                        }
                        if ($public_profile_visi == 'for_both') {
                            $public_profile_check = true;
                        }
                        if ($profile_filed_style != '') {
                            $profile_filed_style = ' style="' . $profile_filed_style . '"';
                        }
                        if ($public_profile_check) {
                            ?>
                            <li class="jobsearch-user-form-coltwo-full <?php echo($user_base_class) ?>"<?php echo($profile_filed_style) ?>>
                                <div class="jobsearch-profile-select">
                                    <select name="public_profile_visible" class="selectize-select"
                                            placeholder="<?php _e('Visible in Listing and Detail', 'wp-jobsearch') ?>">
                                        <option value=""><?php _e('Visible Public Profile', 'wp-jobsearch') ?></option>
                                        <option value="yes"><?php _e('Yes', 'wp-jobsearch') ?></option>
                                        <option value="no"><?php _e('No', 'wp-jobsearch') ?></option>
                                    </select>
                                </div>
                            </li>
                            <?php
                        }
                        if ($signup_user_sector != 'off') {
                            $sector_selct_method = isset($jobsearch_plugin_options['signup_sector_selct_method']) ? $jobsearch_plugin_options['signup_sector_selct_method'] : '';
                            if ($sector_selct_method == 'multi' || $sector_selct_method == 'multi_req') {
                                $selct_sector_title = esc_html__('Select Sectors', 'wp-jobsearch');
                                $selct_sector_class = 'selectize-select';
                                if ($sector_selct_method == 'multi_req') {
                                    $selct_sector_title = esc_html__('Select Sectors *', 'wp-jobsearch');
                                    $selct_sector_class = 'jobsearch-regrequire-field multiselect-req selectize-select';
                                }
                            } else {
                                $selct_sector_title = esc_html__('Select Sector', 'wp-jobsearch');
                                $selct_sector_class = 'selectize-select';
                                if ($sector_selct_method == 'single_req') {
                                    $selct_sector_title = esc_html__('Select Sector *', 'wp-jobsearch');
                                    $selct_sector_class = 'jobsearch-regrequire-field selectize-select';
                                }
                            }

                            $signup_sectr_hideclas = '';
                            $signup_sectr_ishide = false;
                            if ($signup_user_sector == 'emp') {
                                $signup_sectr_hideclas = ' user-employer-spec-field';
                                if ($hide_emp_fields) {
                                    $signup_sectr_ishide = true;
                                }
                            }
                            if ($signup_user_sector == 'cand') {
                                $signup_sectr_hideclas = ' user-candidate-spec-field';
                            }
                            $multi_sector = false;
                            ?>
                            <li class="jobsearch-regfield-sector<?php echo ($signup_sectr_hideclas) ?>"<?php echo ($signup_sectr_ishide ? ' style="display: none;"' : '') ?>>
                                <div class="jobsearch-profile-select">
                                    <?php
                                    if ($sector_selct_method == 'multi' || $sector_selct_method == 'multi_req') {
                                        $multi_sector = true;
                                        $jobsector_args = array(
                                            'orderby' => 'name',
                                            'order' => 'ASC',
                                            'fields' => 'all',
                                            'slug' => '',
                                            'hide_empty' => false,
                                        );
                                        $all_sectors = get_terms('sector', $jobsector_args);
                                        ob_start();
                                        if (!empty($all_sectors)) {
                                            ?>
                                            <select id="pt_user_category_<?php echo absint($rand_numb) ?>" name="pt_user_category[]" class="<?php echo ($selct_sector_class) ?>" multiple="" placeholder="<?php echo ($selct_sector_title) ?>">
                                                <?php
                                                foreach ($all_sectors as $sector_obj) {
                                                    $term_id = $sector_obj->term_id;
                                                    ?>
                                                    <option value="<?php echo (string)($term_id) ?>"><?php echo ($sector_obj->name) ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                            <?php
                                        }
                                        $sector_sel_html = ob_get_clean();
                                        echo apply_filters('jobsearch_sector_select_tag_html', $sector_sel_html, 0);
                                    } else {
                                        $sector_args = array(
                                            'show_option_all' => $selct_sector_title,
                                            'show_option_none' => '',
                                            'option_none_value' => '',
                                            'orderby' => 'title',
                                            'order' => 'ASC',
                                            'show_count' => 0,
                                            'hide_empty' => 0,
                                            'echo' => 0,
                                            'selected' => '',
                                            'hierarchical' => 1,
                                            'id' => 'pt_user_category',
                                            'class' => $selct_sector_class,
                                            'name' => 'pt_user_category',
                                            'depth' => 0,
                                            'taxonomy' => 'sector',
                                            'hide_if_empty' => false,
                                            'value_field' => 'term_id',
                                        );
                                        $sector_sel_html = wp_dropdown_categories($sector_args);
                                        echo apply_filters('jobsearch_sector_select_tag_html', $sector_sel_html, 0);
                                    }
                                    ?>
                                </div>
                                <?php
                                if ($multi_sector === false) {
                                    ?>
                                    <script type="text/javascript">
                                        jQuery('#pt_user_category').find('option').first().val('');
                                        jQuery('#pt_user_category').attr('placeholder', '<?php esc_html_e('Select Sector', 'wp-jobsearch') ?>');
                                    </script>
                                    <?php
                                }
                                ?>
                            </li>
                            <?php
                        }
                        //
                        do_action('jobsearch_registration_extra_fields_after_sector', $arg);

                        if (($signup_cv_upload == 'on' || $signup_cv_upload == 'on_req') && $cand_register_view != false) {
                            $file_sizes_arr = array(
                                '300' => __('300KB', 'wp-jobsearch'),
                                '500' => __('500KB', 'wp-jobsearch'),
                                '750' => __('750KB', 'wp-jobsearch'),
                                '1024' => __('1Mb', 'wp-jobsearch'),
                                '2048' => __('2Mb', 'wp-jobsearch'),
                                '3072' => __('3Mb', 'wp-jobsearch'),
                                '4096' => __('4Mb', 'wp-jobsearch'),
                                '5120' => __('5Mb', 'wp-jobsearch'),
                                '10120' => __('10Mb', 'wp-jobsearch'),
                                '50120' => __('50Mb', 'wp-jobsearch'),
                                '100120' => __('100Mb', 'wp-jobsearch'),
                                '200120' => __('200Mb', 'wp-jobsearch'),
                                '300120' => __('300Mb', 'wp-jobsearch'),
                                '500120' => __('500Mb', 'wp-jobsearch'),
                                '1000120' => __('1Gb', 'wp-jobsearch'),
                            );
                            $cvfile_size = '5120';
                            $cvfile_size_str = __('5 Mb', 'wp-jobsearch');
                            $cand_cv_file_size = isset($jobsearch_plugin_options['cand_cv_file_size']) ? $jobsearch_plugin_options['cand_cv_file_size'] : '';
                            if (isset($file_sizes_arr[$cand_cv_file_size])) {
                                $cvfile_size = $cand_cv_file_size;
                                $cvfile_size_str = $file_sizes_arr[$cand_cv_file_size];
                            }
                            $filesize_act = ceil($cvfile_size / 1024);

                            $cand_files_types = isset($jobsearch_plugin_options['cand_cv_types']) ? $jobsearch_plugin_options['cand_cv_types'] : '';

                            if (empty($cand_files_types)) {
                                $cand_files_types = array(
                                    'application/msword',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'application/pdf',
                                );
                            }

                            $cand_files_types_json = json_encode($cand_files_types);
                            $cand_files_types_json = stripslashes($cand_files_types_json);

                            $sutable_files_arr = array();
                            $file_typs_comarr = array(
                                'text/plain' => __('text', 'wp-jobsearch'),
                                'image/jpeg' => __('jpeg', 'wp-jobsearch'),
                                'image/png' => __('png', 'wp-jobsearch'),
                                'application/msword' => __('doc', 'wp-jobsearch'),
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => __('docx', 'wp-jobsearch'),
                                'application/vnd.ms-excel' => __('xls', 'wp-jobsearch'),
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => __('xlsx', 'wp-jobsearch'),
                                'application/pdf' => __('pdf', 'wp-jobsearch'),
                            );
                            foreach ($file_typs_comarr as $file_typ_key => $file_typ_comar) {
                                if (in_array($file_typ_key, $cand_files_types)) {
                                    $sutable_files_arr[] = '.' . $file_typ_comar;
                                }
                            }
                            $sutable_files_str = implode(', ', $sutable_files_arr);
                            ?>
                            <li class="user-candidate-spec-field jobsearch-user-form-coltwo-full">
                                <div id="jobsearch-upload-cv-main<?php echo($rand_numb) ?>"
                                     class="jobsearch-upload-cv jobsearch-signup-upload-cv">
                                    <label><?php _e('Upload Resume', 'wp-jobsearch') ?><?php echo($signup_cv_upload == 'on_req' ? ' *' : '') ?></label>
                                    <div class="jobsearch-drpzon-con jobsearch-drag-dropcustom">
                                        <div id="cvFilesDropzone<?php echo($rand_numb) ?>" class="dropzone"
                                             ondragover="jobsearch_dragover_evnt<?php echo($rand_numb) ?>(event)"
                                             ondragleave="jobsearch_leavedrop_evnt<?php echo($rand_numb) ?>(event)"
                                             ondrop="jobsearch_ondrop_evnt<?php echo($rand_numb) ?>(event)">
                                            <input type="file" id="cand_cv_filefield<?php echo($rand_numb) ?>"
                                                   class="jobsearch-upload-btn <?php echo($signup_cv_upload == 'on_req' ? 'cv_is_req' : '') ?>"
                                                   name="candidate_cv_file">
                                            <div class="fileContainerFileName"
                                                 ondrop="jobsearch_ondrop_evnt<?php echo($rand_numb) ?>(event)"
                                                 id="fileNameContainer<?php echo($rand_numb) ?>">
                                                <div class="dz-message jobsearch-dropzone-template">
                                                    <span class="upload-icon-con"><i
                                                                class="jobsearch-icon jobsearch-upload"></i></span>
                                                    <strong><?php esc_html_e('Drop a resume file or click to upload.', 'wp-jobsearch') ?></strong>
                                                    <div class="upload-inffo"><?php printf(__('To upload file size is <span>(Max %s)</span> <span class="uplod-info-and">and</span> allowed file types are <span>(%s)</span>', 'wp-jobsearch'), $cvfile_size_str, $sutable_files_str) ?></div>
                                                    <div class="upload-or-con">
                                                        <span><?php esc_html_e('or', 'wp-jobsearch') ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <a class="jobsearch-drpzon-btn"><i
                                                        class="jobsearch-icon jobsearch-arrows-2"></i> <?php esc_html_e('Upload Resume', 'wp-jobsearch') ?>
                                            </a>
                                        </div>
                                        <script type="text/javascript">
                                            jQuery('#cvFilesDropzone<?php echo($rand_numb) ?>').find('input[name=candidate_cv_file]').css({
                                                position: 'absolute',
                                                width: '100%',
                                                height: '100%',
                                                top: '0',
                                                left: '0',
                                                opacity: '0',
                                                'z-index': '9',
                                            });

                                            jQuery(document).on('change', 'input#cand_cv_filefield<?php echo($rand_numb) ?>', function () {
                                                if (this.files && this.files[0]) {
                                                    var upcv_file = this.files[0];
                                                    var upcv_file_type = upcv_file.type;

                                                    var cvup_allowed_types = '<?php echo($cand_files_types_json) ?>';

                                                    if (cvup_allowed_types.indexOf(upcv_file_type) >= 0) {
                                                        var the_show_msg = '<?php esc_html_e('No file has been selected', 'wp-jobsearch') ?>';
                                                        if (this.files.length > 0) {
                                                            var slected_file_name = this.files[0].name;
                                                            the_show_msg = '<?php esc_html_e('The file', 'wp-jobsearch') ?> "' + slected_file_name + '" <?php esc_html_e('has been selected', 'wp-jobsearch') ?>';
                                                        }
                                                        document.getElementById('cvFilesDropzone<?php echo($rand_numb) ?>').classList.remove('fileContainerDragOver');
                                                        try {
                                                            droppedFiles = document.getElementById('cand_cv_filefield<?php echo($rand_numb) ?>').files;
                                                            document.getElementById('fileNameContainer<?php echo($rand_numb) ?>').textContent = the_show_msg;
                                                        } catch (error) {
                                                            ;
                                                        }
                                                        try {
                                                            aName = document.getElementById('cand_cv_filefield<?php echo($rand_numb) ?>').value;
                                                            if (aName !== '') {
                                                                document.getElementById('fileNameContainer<?php echo($rand_numb) ?>').textContent = the_show_msg;
                                                            }
                                                        } catch (error) {
                                                            ;
                                                        }
                                                    } else {
                                                        alert('<?php esc_html_e('This file type is not allowed.', 'wp-jobsearch') ?>');
                                                    }
                                                }
                                            });

                                            function jobsearch_ondrop_evnt<?php echo($rand_numb) ?>(e) {
                                                var the_show_msg = '<?php esc_html_e('No file has been selected', 'wp-jobsearch') ?>';
                                                if (e.target.files.length > 0) {
                                                    var slected_file_name = e.target.files[0].name;
                                                    the_show_msg = '<?php esc_html_e('The file', 'wp-jobsearch') ?> "' + slected_file_name + '" <?php esc_html_e('has been selected', 'wp-jobsearch') ?>';
                                                }
                                                document.getElementById('cvFilesDropzone<?php echo($rand_numb) ?>').classList.remove('fileContainerDragOver');
                                                try {
                                                    droppedFiles = e.dataTransfer.files;
                                                    document.getElementById('fileNameContainer<?php echo($rand_numb) ?>').textContent = the_show_msg;
                                                } catch (error) {
                                                    ;
                                                }
                                            }

                                            function jobsearch_dragover_evnt<?php echo($rand_numb) ?>(e) {
                                                document.getElementById('cvFilesDropzone<?php echo($rand_numb) ?>').classList.add('fileContainerDragOver');
                                                e.preventDefault();
                                                e.stopPropagation();
                                            }

                                            function jobsearch_leavedrop_evnt<?php echo($rand_numb) ?>(e) {
                                                document.getElementById('cvFilesDropzone<?php echo($rand_numb) ?>').classList.remove('fileContainerDragOver');
                                            }
                                        </script>
                                    </div>
                                </div>
                            </li>
                            <?php
                        }

                        $normfields_html = ob_get_clean();
                        echo apply_filters('jobsearch_regform_normfields_html', $normfields_html, $arg);

                        $emp_cfields_dis = 'none';
                        $cand_cfields_dis = 'inline-block';
                        if ($emp_register_view !== false && $cand_register_view === false) {
                            $emp_cfields_dis = 'inline-block';
                            $cand_cfields_dis = 'none';
                        }

                        do_action('jobsearch_signup_custom_fields_load', 0, 'candidate', $cand_cfields_dis);
                        do_action('jobsearch_signup_custom_fields_load', 0, 'employer', $emp_cfields_dis);

                        do_action('jobsearch_registration_extra_fields_end');

                        echo apply_filters('jobsearch_regform_cusfields_after_html', '', $arg);

                        if ($pass_from_user == 'on') {
                            ?>
                            <li>
                                <input class="form-control input-lg required jobsearch_chk_passfield"
                                       name="pt_user_pass" type="password"
                                       placeholder="<?php _e('Password *', 'wp-jobsearch'); ?>"/>
                                <span class="passlenth-chk-msg"></span>
                            </li>
                            <li>
                                <input class="form-control input-lg required" name="pt_user_cpass" type="password"
                                       placeholder="<?php _e('Confirm Password *', 'wp-jobsearch'); ?>"/>
                            </li>
                            <?php
                        }

                        ob_start();
                        if ($captcha_switch == 'on' && !is_user_logged_in()) {
                            wp_enqueue_script('jobsearch_google_recaptcha');
                            ?>
                            <li>
                                <script type="text/javascript">
                                    var recaptcha1;
                                    var jobsearch_multicap = function () {
                                        //Render the recaptcha1 on the element with ID "recaptcha1"
                                        recaptcha1 = grecaptcha.render('recaptcha1', {
                                            'sitekey': '<?php echo($jobsearch_sitekey); ?>', //Replace this with your Site key
                                            'theme': 'light'
                                        });
                                    };
                                    jQuery(document).ready(function () {
                                        jQuery('.recaptcha-reload-a').click();
                                    });
                                </script>
                                <div class="recaptcha-reload" id="recaptcha1_div">
                                    <?php echo jobsearch_recaptcha('recaptcha1'); ?>
                                </div>
                            </li>
                            <?php
                        }
                        $recaptch_html = ob_get_clean();
                        echo apply_filters('jobsearch_login_temp_regbox_captcha_html', $recaptch_html, $captcha_switch, $jobsearch_sitekey, $rand_numb);

                        ob_start();
                        ?>
                        <li>
                            <input type="hidden" name="action" value="jobsearch_register_member_submit"/>
                            <?php if ($pass_from_user == 'on') { ?>
                                <button data-id="<?php echo absint($rand_numb) ?>"
                                        class="jobsearch-register-submit-btn btn btn-theme btn-lg jobsearch-regpass-frmbtn jobsearch-disable-btn"
                                        disabled data-loading-text="<?php _e('Loading...', 'wp-jobsearch') ?>"
                                        type="submit"><?php echo apply_filters('jobsearch_login_temp_regboxform_btntitle', __('Sign up', 'wp-jobsearch')); ?></button>
                            <?php } else { ?>
                                <button data-id="<?php echo absint($rand_numb) ?>"
                                        class="jobsearch-register-submit-btn btn btn-theme btn-lg"
                                        data-loading-text="<?php _e('Loading...', 'wp-jobsearch') ?>"
                                        type="submit"><?php echo apply_filters('jobsearch_login_temp_regboxform_btntitle', __('Sign up', 'wp-jobsearch')); ?></button>
                            <?php } ?>
                            <div class="form-loader"></div>
                        </li>
                        <?php
                        $subbtn_html = ob_get_clean();
                        echo apply_filters('jobsearch_login_temp_regbox_submitcon_html', $subbtn_html, $rand_numb);
                        ?>
                    </ul>
                    <div class="registration-errors"></div>
                    <?php
                    do_action('jobsearch_after_regform_html_action', 'register-security');
                    ob_start();
                    jobsearch_terms_and_con_link_txt();
                    $trms_con_html = ob_get_clean();
                    echo apply_filters('jobsearch_terms_and_con_link_txt_regtmpage', $trms_con_html);
                    ?>
                </form>

                <?php
                //} else {
                //    echo '<div class="alert alert-warning">' . __('Registration is disabled.', 'wp-jobsearch') . '</div>';
                //}
                ?>

            </div>
            <?php
        }
        $html = ob_get_clean();
        echo apply_filters('jobsearch_user_reg_pform_box_html', $html, $rand_numb, $arg);
    }

    public function after_regform_html($form_type = 'register-security')
    {
        $rand_num = rand(1000000, 9999999);
        ?>
        <div id="jobsearch-aterreg-<?php echo($rand_num) ?>"></div>
        <?php
        $popup_args = array('rand_num' => $rand_num, 'form_type' => $form_type);
        add_action('wp_footer', function () use ($popup_args) {

            extract(shortcode_atts(array(
                'rand_num' => '',
                'form_type' => '',
            ), $popup_args));
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    var ajax_req_<?php echo($rand_num) ?> = jQuery.ajax({
                        url: '<?php echo admin_url('admin-ajax.php') ?>',
                        method: "POST",
                        data: {
                            rand_id: '<?php echo($rand_num) ?>',
                            secure_form: '<?php echo($form_type) ?>',
                            action: 'jobsearch_userreg_form_after_nonce'
                        },
                        dataType: "html"
                    });
                    ajax_req_<?php echo($rand_num) ?>.done(function (response) {
                        jQuery('#jobsearch-aterreg-<?php echo($rand_num) ?>').html(response);
                    });
                });
            </script>
            <?php
        }, 20, 2);
    }

}

// class Jobsearch_Login_Registration_Template 
$Jobsearch_Login_Registration_Template_obj = new Jobsearch_Login_Registration_Template();
