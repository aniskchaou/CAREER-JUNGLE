<?php

/**
 * Class Jobsearch Social Login
 */
class JobsearchSocialLogin
{

    /**
     * JobsearchSocialLogin constructor.
     */
    public function __construct()
    {

        add_action('init', array($this, 'social_login_includes'), 1);
        add_action('social_login_html', array($this, 'social_login_html_callback'), 10, 1);

        add_filter('jobsearch_social_login_settings', array($this, 'social_login_general_settings'), 10, 1);
        add_filter('jobsearch_api_settings_section', array($this, 'social_login_api_settings'), 10, 1);
    }

    public function social_login_includes()
    {
        // facebook login module
        include plugin_dir_path(dirname(__FILE__)) . 'social-login/facebook/facebook.php';
        // twitter login module
        include plugin_dir_path(dirname(__FILE__)) . 'social-login/twitter/twitter.php';
        // google login module
        include plugin_dir_path(dirname(__FILE__)) . 'social-login/google/google.php';
        // linkedin login module
        include plugin_dir_path(dirname(__FILE__)) . 'social-login/linkedin/linkedin.php';
        // XING login module
        include plugin_dir_path(dirname(__FILE__)) . 'social-login/xing/xing.php';
    }

    public function social_login_general_settings($section_settings = array())
    {

        if (isset($section_settings['fields'])) {

            $section_settings['fields'][] = array(
                'id' => 'social-login-sett-section',
                'type' => 'section',
                'title' => __('Social Logins', 'wp-jobsearch'),
                'subtitle' => '',
                'indent' => true,
            );

            $section_settings['fields'][] = array(
                'id' => 'facebook-social-login',
                'type' => 'button_set',
                'title' => __('Facebook', 'wp-jobsearch'),
                'subtitle' => __('Social Login with Facebook.', 'wp-jobsearch'),
                'desc' => '',
                'options' => array(
                    'on' => __('On', 'wp-jobsearch'),
                    'off' => __('Off', 'wp-jobsearch'),
                ),
                'default' => 'off',
            );

            $section_settings['fields'][] = array(
                'id' => 'twitter-social-login',
                'type' => 'button_set',
                'title' => __('Twitter', 'wp-jobsearch'),
                'subtitle' => __('Social Login with Twitter.', 'wp-jobsearch'),
                'desc' => '',
                'options' => array(
                    'on' => __('On', 'wp-jobsearch'),
                    'off' => __('Off', 'wp-jobsearch'),
                ),
                'default' => 'off',
            );

            $section_settings['fields'][] = array(
                'id' => 'google-social-login',
                'type' => 'button_set',
                'title' => __('Google', 'wp-jobsearch'),
                'subtitle' => __('Social Login with google.', 'wp-jobsearch'),
                'desc' => '',
                'options' => array(
                    'on' => __('On', 'wp-jobsearch'),
                    'off' => __('Off', 'wp-jobsearch'),
                ),
                'default' => 'off',
            );

            $section_settings['fields'][] = array(
                'id' => 'linkedin-social-login',
                'type' => 'button_set',
                'title' => __('Linkedin', 'wp-jobsearch'),
                'subtitle' => __('Social Login with linkedIn.', 'wp-jobsearch'),
                'desc' => '',
                'options' => array(
                    'on' => __('On', 'wp-jobsearch'),
                    'off' => __('Off', 'wp-jobsearch'),
                ),
                'default' => 'off',
            );
            $section_settings['fields'][] = array(
                'id' => 'xing-social-login',
                'type' => 'button_set',
                'title' => __('Xing', 'wp-jobsearch'),
                'subtitle' => __('Social Login with xing.', 'wp-jobsearch'),
                'desc' => '',
                'options' => array(
                    'on' => __('On', 'wp-jobsearch'),
                    'off' => __('Off', 'wp-jobsearch'),
                ),
                'default' => 'off',
            );
        }

        return $section_settings;
    }

    public function social_login_api_settings($section_settings = array())
    {
        if (isset($section_settings['fields'])) {
            $section_settings['fields'][] = array(
                'id' => 'jobsearch-google-client-id',
                'type' => 'text',
                'transparent' => false,
                'title' => __('Client ID', 'wp-jobsearch'),
                'subtitle' => __('Please enter the Client ID of your Google account.', 'wp-jobsearch'),
                'desc' => '',
                'default' => ''
            );

            $section_settings['fields'][] = array(
                'id' => 'jobsearch-google-client-secret',
                'type' => 'text',
                'transparent' => false,
                'title' => __('Client secret', 'wp-jobsearch'),
                'subtitle' => __('Please enter the Client secret of your Google account.', 'wp-jobsearch'),
                'desc' => '',
                'default' => ''
            );

            $section_settings['fields'][] = array(
                'id' => 'facebook-api-section',
                'type' => 'section',
                'title' => __('Facebook API settings section.', 'wp-jobsearch'),
                'subtitle' => sprintf(__('Callback URL is: %s', 'wp-jobsearch'), admin_url('admin-ajax.php?action=jobsearch_facebook')),
                'indent' => true,
            );

            $section_settings['fields'][] = array(
                'id' => 'jobsearch-facebook-app-id',
                'type' => 'text',
                'transparent' => false,
                'title' => __('App ID', 'wp-jobsearch'),
                'subtitle' => __('Please enter App the ID of your Facebook account.', 'wp-jobsearch'),
                'desc' => '',
                'default' => ''
            );

            $section_settings['fields'][] = array(
                'id' => 'jobsearch-facebook-app-secret',
                'type' => 'text',
                'transparent' => false,
                'title' => __('App Secret', 'wp-jobsearch'),
                'subtitle' => __('Please enter the App Secret of your Facebook account.', 'wp-jobsearch'),
                'desc' => '',
                'default' => ''
            );

            // Linkedin
            $section_settings['fields'][] = array(
                'id' => 'linkedin-api-section',
                'type' => 'section',
                'title' => __('Linkedin API settings section.', 'wp-jobsearch'),
                'subtitle' => sprintf(__('Callback URL is: %s', 'wp-jobsearch'), home_url('/')),
                'indent' => true,
            );
            $section_settings['fields'][] = array(
                'id' => 'jobsearch_linkedin_app_id',
                'type' => 'text',
                'transparent' => false,
                'title' => __('Client ID', 'wp-jobsearch'),
                'subtitle' => __('Please enter the Client ID of your LinkedIn app.', 'wp-jobsearch'),
                'desc' => '',
                'default' => ''
            );
            $section_settings['fields'][] = array(
                'id' => 'jobsearch_linkedin_secret',
                'type' => 'text',
                'transparent' => false,
                'title' => __('Client Secret', 'wp-jobsearch'),
                'subtitle' => __('Please enter the Client Secret of your LinkedIn app.', 'wp-jobsearch'),
                'desc' => '',
                'default' => ''
            );


            // XING
            $section_settings['fields'][] = array(
                'id' => 'xing-api-section',
                'type' => 'section',
                'title' => __('XING API settings section.', 'wp-jobsearch'),
                'subtitle' => sprintf(__('Callback URL is: %s', 'wp-jobsearch'), home_url('/')),
                'indent' => true,
            );
            $section_settings['fields'][] = array(
                'id' => 'jobsearch_xing_consumer_key',
                'type' => 'text',
                'transparent' => false,
                'title' => __('Consumer Key', 'wp-jobsearch'),
                'subtitle' => __('Please enter the Client consumer key of your xing app.', 'wp-jobsearch'),
                'desc' => '',
                'default' => ''
            );


        }
        return $section_settings;
    }

    public function social_login_html_callback($args = array())
    {
        ob_start();
        $html = '';
        if (!is_user_logged_in()) {
            global $jobsearch_plugin_options;

            $facebook_login = isset($jobsearch_plugin_options['facebook-social-login']) ? $jobsearch_plugin_options['facebook-social-login'] : '';
            $twitter_login = isset($jobsearch_plugin_options['twitter-social-login']) ? $jobsearch_plugin_options['twitter-social-login'] : '';
            $google_login = isset($jobsearch_plugin_options['google-social-login']) ? $jobsearch_plugin_options['google-social-login'] : '';
            $linkedin_login = isset($jobsearch_plugin_options['linkedin-social-login']) ? $jobsearch_plugin_options['linkedin-social-login'] : '';
            $xing_login = isset($jobsearch_plugin_options['xing-social-login']) ? $jobsearch_plugin_options['xing-social-login'] : '';

            if ($facebook_login == 'on' || $twitter_login == 'on' || $google_login == 'on' || $linkedin_login == 'on') {
                if (isset($args['type']) && $args['type'] == 'popup') {
                    ?>
                    <div class="jobsearch-box-title jobsearch-box-title-sub">
                        <span><?php _e('Or Sign In With', 'wp-jobsearch') ?></span>
                    </div>
                    <div class="clearfix"></div>
                    <ul class="jobsearch-login-media">
                        <?php
                        if ($facebook_login == 'on') {
                            echo do_shortcode('[jobsearch_facebook_login]');
                        }
                        if ($twitter_login == 'on') {
                            echo do_shortcode('[jobsearch_twitter_login]');
                        }
                        if ($google_login == 'on') {
                            echo do_shortcode('[jobsearch_google_login]');
                        }
                        if ($linkedin_login == 'on') {
                            echo do_shortcode('[jobsearch_linkedin_login]');
                        }
                        if ($xing_login == 'on') {
                            echo do_shortcode('[jobsearch_xing_login]');
                        }

                        apply_filters('jobsearch_social_logins_extra_sl_btns', '', 'popup');
                        ?>
                    </ul>
                    <?php
                } else {
                    ?>
                    <div class="jobsearch-login-with">
                        <span><?php echo apply_filters('jobsearch_sociallgin_inloginbox_tagline', __('or login with:', 'wp-jobsearch')) ?></span>
                        <ul>
                            <?php
                            if ($facebook_login == 'on') {
                                echo do_shortcode('[jobsearch_facebook_login]');
                            }
                            if ($twitter_login == 'on') {
                                echo do_shortcode('[jobsearch_twitter_login]');
                            }
                            if ($google_login == 'on') {
                                echo do_shortcode('[jobsearch_google_login]');
                            }
                            if ($linkedin_login == 'on') {
                                echo do_shortcode('[jobsearch_linkedin_login]');
                            }
                            if ($xing_login == 'on') {
                                echo do_shortcode('[jobsearch_xing_login]');
                            }
                            //
                            apply_filters('jobsearch_social_logins_extra_sl_btns', '', 'element');
                            ?>
                        </ul>
                    </div>
                    <?php
                }
            }
        }
        $html = ob_get_clean();
        echo force_balance_tags($html);
    }

}

/*
 * Starts social login
 */
new JobsearchSocialLogin();
