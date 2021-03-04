<?php

use WP_Jobsearch\Package_Limits;

add_action('init', 'jobsearch_loginbtns_shortcode_translation', 12);

function jobsearch_loginbtns_shortcode_translation()
{
    do_action('wpml_register_single_string', 'Jobsearch Shortcode', 'Login account signin text', 'Sign In');
    do_action('wpml_register_single_string', 'Jobsearch Shortcode', 'Login account register text', 'Register');
}

/**
 * Login Links Shortcode
 * @return html
 */
add_shortcode('jobsearch_myaccount_links', 'jobsearch_login_myaccount_links');

function jobsearch_login_myaccount_links($atts)
{
    global $jobsearch_plugin_options, $sitepress;
    extract(shortcode_atts(array(
        'user_register' => 'yes',
        'signin_text' => 'Sign In',
        'register_text' => 'Register',
    ), $atts));

    $user_pkg_limits = new Package_Limits;

    $lang_code = '';
    if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
        $lang_code = $sitepress->get_current_language();
    }
    ob_start();
    ?>
    <div class="jobsearch-useraccount-linksbtn">
        <?php
        if (is_user_logged_in()) {
            $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
            $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
            $page_id = jobsearch__get_post_id($user_dashboard_page, 'page');
            $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page');
            $profile_page_url = $page_url;
            if (wp_is_mobile()) {
                $profile_page_url = 'javascript:void(0);';
            }
            $get_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';
            ?>
            <a href="<?php echo($profile_page_url) ?>"
               class="jobsearch-user-loginbtn"><?php echo apply_filters('jobsearch_header_user_myaccount_txt', esc_html__('My Account', 'wp-jobsearch')); ?></a>
            <ul <?php echo apply_filters('jobsearch_hdr_menu_accountlinks_ul_atts', 'class="nav-item-children sub-menu"') ?>>
                <?php jobsearch_user_account_linkitems($user_pkg_limits, $page_url, $get_tab); ?>
            </ul>
        <?php } else { ?>
            <a href="javascript:void(0);"
               class="jobsearch-user-loginbtn jobsearch-open-signin-tab"><?php echo apply_filters('wpml_translate_single_string', $signin_text, 'Jobsearch Shortcode', 'Login account signin text', $lang_code) ?></a>
            <?php
            if ($user_register == 'yes') {
                ?>
                <a href="javascript:void(0);"
                   class="jobsearch-user-loginbtn jobsearch-open-register-tab"><?php echo apply_filters('wpml_translate_single_string', $register_text, 'Jobsearch Shortcode', 'Login account register text', $lang_code) ?></a>
                <?php
            }
        } ?>
    </div>
    <?php
    $html = ob_get_clean();
    return $html;
}
