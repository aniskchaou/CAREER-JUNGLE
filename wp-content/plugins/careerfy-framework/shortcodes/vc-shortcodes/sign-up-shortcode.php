<?php

/**
 * Testimonials Shortcode
 * @return html
 */
add_shortcode('careerfy_sign_up', 'careerfy_sign_up_shortcode');

function careerfy_sign_up_shortcode($atts, $content = '')
{
    $html = '';
    $rand_numb = rand(1000000, 9999999);
    extract(shortcode_atts(array(
        'view' => 'style1',
        'srch_desc' => '',
    ), $atts));

    ob_start();


    if ($view == 'style1') { ?>

        <div class="careerfy-nineteen-search-style2-tabs">
            <ul class="careerfy-search-nineteen-tabs-nav-style2">
                <li class="active">
                    <a data-toggle="tab" data-type="jobsearch_employer" data-id="<?php echo absint($rand_numb) ?>"
                       href="#home" class="user-type-btn-sign-up"><i
                                class="fa fa-black-tie"></i><span><?php echo esc_html__('Find Help', 'careerfy-frame') ?></span></a>
                </li>
                <li><a data-toggle="tab" data-type="jobsearch_candidate" data-id="<?php echo absint($rand_numb) ?>"
                       href="#menu1" class="user-type-btn-sign-up"><i
                                class="fa fa-eye"></i><span><?php echo esc_html__('Looking Job', 'careerfy-frame') ?></span></a>
                </li>

            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active">
                    <form method="post" id="registration-form-<?php echo absint($rand_numb) ?>"
                          class="careerfy-nineteen-loc-search-style2" action="<?php echo home_url('/'); ?>">
                        <?php wp_nonce_field('ajax-login-nonce', 'register-security'); ?>
                        <input type="hidden" name="action" value="jobsearch_register_member_submit">
                        <input type="hidden" name="pt_user_role" value="jobsearch_employer">
                        <ul>
                            <li>
                                <input class="required" name="pt_user_fullname" type="text"
                                       placeholder="<?php _e('Full Name *', 'wp-jobsearch'); ?>" required>
                                <input name="pt_user_fname" type="hidden" value="">
                                <input name="pt_user_lname" type="hidden" value="">
                                <i class="jobsearch-icon jobsearch-user"></i>
                            </li>
                            <li>
                                <input class="required" name="pt_user_email"
                                       id="pt_user_email" type="email"
                                       placeholder="<?php _e('Email', 'wp-jobsearch'); ?>"/>
                                <i class="jobsearch-icon jobsearch-mail"></i>
                            </li>
                            <li>
                                <input class="required jobsearch_chk_passfield" name="pt_user_pass"
                                       id="pt_user_pass_<?php echo absint($rand_numb) ?>" type="password"
                                       placeholder="<?php _e('Password', 'wp-jobsearch'); ?>"/>
                                <input class="required jobsearch_chk_passfield" name="pt_user_cpass" type="hidden"/>
                                <span class="passlenth-chk-msg"></span>
                                <i class="jobsearch-icon jobsearch-multimedia"></i>
                            </li>
                            <input class="required" name="pt_user_organization" id="pt_user_organization"
                                   type="hidden" placeholder="<?php _e('Organization Name', 'wp-jobsearch'); ?>"/>
                            <li><input type="submit" class="sign-up-form-submit"
                                       data-id="<?php echo absint($rand_numb) ?>"
                                       value="<?php esc_html_e("Sign Up Free", 'careerfy-frame') ?>"></li>
                            <div class="form-loader"></div>

                            </li>

                        </ul>
                    </form>
                </div>
                <div class="registration-errors"></div>
            </div>
        </div>

    <?php } else if ($view == 'style3') { ?>

        <div class="careerfy-twentyone-signup">
            <div class="col-md-5">
                <div class="careerfy-twentyone-signup-content">
                    <?php if ($content != '') { ?>
                        <h1><?php echo($content) ?></h1>
                    <?php }
                    if ($srch_desc != '') { ?>
                        <span class="careerfy-twentyone-search-description"><?php echo($srch_desc) ?> <img src="<?php echo trailingslashit(get_template_directory_uri()) . 'images/title-arrow.png'; ?>"></span>
                    <?php } ?>
                </div>
            </div>
            <div class="col-md-7">
                <div class="careerfy-twentyone-search-tabs">
                    <ul class="careerfy-search-twentyone-tabs-nav">
                        <li class="active">
                            <a data-toggle="tab"
                               href="#home"><i
                                        class="fa fa-black-tie"></i><span><?php echo esc_html__("Find Help", "careerfy-frame") ?></span></a>
                        </li>

                        <li><a data-toggle="tab"
                               href="#menu1"><i
                                        class="fa fa-eye"></i><span><?php echo esc_html__("Looking job", "careerfy-frame") ?></span></a>
                        </li>

                    </ul>
                    <div class="tab-content">
                        <form method="post" id="registration-form-<?php echo absint($rand_numb) ?>"
                              class="careerfy-twentyone-loc-search" action="<?php echo home_url('/'); ?>">
                            <?php wp_nonce_field('ajax-login-nonce', 'register-security'); ?>
                            <div class="signup-hidden-inputs">
                                <input type="hidden" name="action" value="jobsearch_register_member_submit">
                                <input type="hidden" name="pt_user_role" value="jobsearch_employer">
                                <input class="required" name="pt_user_organization" id="pt_user_organization"
                                       type="hidden" placeholder="<?php _e('Organization Name', 'wp-jobsearch'); ?>"/>
                            </div>
                            <ul>

                                <li>
                                    <input class="required" name="pt_user_email"
                                           id="pt_user_email" type="email"
                                           placeholder="<?php _e('Email', 'wp-jobsearch'); ?>"/>

                                </li>

                                <li>
                                    <input class="required jobsearch_chk_passfield" name="pt_user_pass"
                                           id="pt_user_pass_<?php echo absint($rand_numb) ?>" type="password"
                                           placeholder="<?php _e('Password', 'wp-jobsearch'); ?>"/>
                                    <input class="required jobsearch_chk_passfield" name="pt_user_cpass" type="hidden"/>
                                    <span class="passlenth-chk-msg"></span>

                                </li>

                                <li class="careerfy-twentyone-signup-submit">
                                    <input type="submit" class="sign-up-form-submit"
                                           data-id="<?php echo absint($rand_numb) ?>"
                                           value="<?php esc_html_e("Sign up", 'careerfy-frame') ?>">
                                </li>

                                <div class="form-loader" style="display: none;"></div>
                            </ul>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php } else { ?>

        <div class="careerfy-twenty-signup">
            <div class="col-md-5">
                <div class="careerfy-twenty-signup-content">
                    <?php if ($content != '') { ?>
                        <h1><?php echo($content) ?>
                            <img src="<?php echo trailingslashit(get_template_directory_uri()) . 'images/title-arrow.png'; ?>">
                        </h1>
                    <?php }
                    if ($srch_desc != '') { ?>
                        <span class="careerfy-twenty-search-description"><?php echo($srch_desc) ?></span>
                    <?php } ?>
                </div>
            </div>
            <div class="col-md-7">
                <div class="careerfy-twenty-search-tabs">
                    <ul class="careerfy-search-twenty-tabs-nav">
                        <li class="active">
                            <a data-toggle="tab" data-type="jobsearch_employer"
                               data-id="<?php echo absint($rand_numb) ?>"
                               href="#home"
                               class="user-type-btn-sign-up"><span><?php echo esc_html__('I\'am looking for animal care', 'careerfy-frame') ?></span></a>

                        </li>
                        <li>
                            <a data-toggle="tab" data-type="jobsearch_candidate"
                               data-id="<?php echo absint($rand_numb) ?>"
                               href="#menu1"
                               class="user-type-btn-sign-up"><span><?php echo esc_html__('I\'am looking for a job', 'careerfy-frame') ?></span></a>

                        </li>

                    </ul>
                    <div class="tab-content">
                        <form method="post" id="registration-form-<?php echo absint($rand_numb) ?>"
                              class="careerfy-twenty-loc-search-style2" action="<?php echo home_url('/'); ?>">
                            <?php wp_nonce_field('ajax-login-nonce', 'register-security'); ?>
                            <div class="signup-hidden-inputs">
                                <input type="hidden" name="action" value="jobsearch_register_member_submit">
                                <input type="hidden" name="pt_user_role" value="jobsearch_employer">
                                <input class="required" name="pt_user_organization" id="pt_user_organization"
                                       type="hidden" placeholder="<?php _e('Organization Name', 'wp-jobsearch'); ?>"/>
                            </div>
                            <ul>

                                <li>
                                    <input class="required" name="pt_user_email"
                                           id="pt_user_email" type="email"
                                           placeholder="<?php _e('Email', 'wp-jobsearch'); ?>"/>

                                </li>
                                <li>
                                    <input class="required jobsearch_chk_passfield" name="pt_user_pass"
                                           id="pt_user_pass_<?php echo absint($rand_numb) ?>" type="password"
                                           placeholder="<?php _e('Password', 'wp-jobsearch'); ?>"/>
                                    <input class="required jobsearch_chk_passfield" name="pt_user_cpass" type="hidden"/>
                                    <span class="passlenth-chk-msg"></span>

                                </li>

                                <li class="careerfy-twenty-signup-submit">
                                    <input type="submit" class="sign-up-form-submit"
                                           data-id="<?php echo absint($rand_numb) ?>"
                                           value="<?php esc_html_e("Sign up", 'careerfy-frame') ?>">
                                </li>
                                <div class="form-loader" style="display: none;"></div>
                            </ul>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php }
    $html = ob_get_clean();
    return $html;
}