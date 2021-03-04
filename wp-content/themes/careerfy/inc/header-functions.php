<?php
/**
 * Careerfy Header functions.
 *
 * @package Careerfy
 */
if (!function_exists('careerfy_site_logo')) {

    /**
     * Site Logo.
     * @return markup
     */
    function careerfy_site_logo($link_class = 'careerfy-logo')
    {
        global $careerfy_framework_options;
        $logo_default_val = '';
        $careerfy_theme_options = get_option('careerfy_framework_options');

        $customizer_logo_id = get_theme_mod('custom_logo');
        $customizer_logo_arr = wp_get_attachment_image_src($customizer_logo_id, 'full');
        $customizer_logo = isset($customizer_logo_arr[0]) && $customizer_logo_arr[0] != '' ? $customizer_logo_arr[0] : '';

        if (empty($careerfy_theme_options)) {
            $logo_default_val = get_template_directory_uri() . '/images/logo.png';
        }

        if ($customizer_logo != '') {
            $logo_default_val = $customizer_logo;
        }

        $site_logo_key = 'careerfy-site-logo';
        if (function_exists('icl_get_languages') && function_exists('wpml_init_language_switcher')) {
            global $sitepress;
            $lang_code = $sitepress->get_current_language();
            $default_lang = $sitepress->get_default_language();
            if ($lang_code != $default_lang) {
                $site_logo_key = 'careerfy_site_logo_' . $lang_code;
            }
        }
        $careerfy_logo = isset($careerfy_framework_options[$site_logo_key]['url']) && $careerfy_framework_options[$site_logo_key]['url'] != '' ? $careerfy_framework_options[$site_logo_key]['url'] : $logo_default_val;
        $logo_width = isset($careerfy_framework_options['careerfy-logo-width']) && $careerfy_framework_options['careerfy-logo-width'] > 0 ? ' width="' . $careerfy_framework_options['careerfy-logo-width'] . '"' : '';
        $logo_height = isset($careerfy_framework_options['careerfy-logo-height']) && $careerfy_framework_options['careerfy-logo-height'] > 0 ? ' height="' . $careerfy_framework_options['careerfy-logo-height'] . '"' : '';

        echo '<a class="' . $link_class . '" title="' . get_bloginfo('name') . '" href="' . esc_url(home_url('/')) . '">';
        if ($careerfy_logo != '') {
            echo '<img src="' . esc_url($careerfy_logo) . '"' . $logo_width . $logo_height . ' alt="' . get_bloginfo('name') . '">';
        } else {
            echo get_bloginfo('name');
        }
        echo '</a>';
    }

}

function careerfy_responsive_logo($link_class = 'jobsearch-responsive-logo')
{
    global $careerfy_framework_options;

    $customizer_logo_id = get_theme_mod('custom_logo');
    $customizer_logo_arr = wp_get_attachment_image_src($customizer_logo_id, 'full');
    $customizer_logo = isset($customizer_logo_arr[0]) && $customizer_logo_arr[0] != '' ? $customizer_logo_arr[0] : '';

    $logo_default_val = '';
    $careerfy_theme_options = get_option('careerfy_framework_options');
    if (empty($careerfy_theme_options)) {
        $logo_default_val = get_template_directory_uri() . '/images/logo.png';
    }

    if ($customizer_logo != '') {
        $logo_default_val = $customizer_logo;
    }

    $resp_logo_key = 'geek-responsive-logo';
    if (function_exists('icl_get_languages') && function_exists('wpml_init_language_switcher')) {
        global $sitepress;
        $lang_code = $sitepress->get_current_language();
        $default_lang = $sitepress->get_default_language();
        if ($lang_code != $default_lang) {
            $resp_logo_key = 'careerfy_responsive_logo_' . $lang_code;
        }
    }

    $site_logo_key = 'careerfy-site-logo';
    if (function_exists('icl_get_languages') && function_exists('wpml_init_language_switcher')) {
        global $sitepress;
        $lang_code = $sitepress->get_current_language();
        $default_lang = $sitepress->get_default_language();
        if ($lang_code != $default_lang) {
            $site_logo_key = 'careerfy_site_logo_' . $lang_code;
        }
    }

    $careerfy_logo = isset($careerfy_framework_options[$resp_logo_key]['url']) && $careerfy_framework_options[$resp_logo_key]['url'] != '' ? $careerfy_framework_options[$resp_logo_key]['url'] : '';
    if ($careerfy_logo == '') {
        $careerfy_logo = isset($careerfy_framework_options[$site_logo_key]['url']) && $careerfy_framework_options[$site_logo_key]['url'] != '' ? $careerfy_framework_options[$site_logo_key]['url'] : '';
    }
    if ($careerfy_logo == '') {
        $careerfy_logo = $logo_default_val;
    }
    $logo_width = isset($careerfy_framework_options['geek-resp-logo-width']) && $careerfy_framework_options['geek-resp-logo-width'] > 0 ? ' width="' . $careerfy_framework_options['geek-resp-logo-width'] . '"' : '';
    $logo_height = isset($careerfy_framework_options['geek-resp-logo-height']) && $careerfy_framework_options['geek-resp-logo-height'] > 0 ? ' height="' . $careerfy_framework_options['geek-resp-logo-height'] . '"' : '';

    echo '<a class="' . $link_class . '" title="' . get_bloginfo('name') . '" href="' . esc_url(home_url('/')) . '">';
    if ($careerfy_logo != '') {
        echo '<img src="' . esc_url($careerfy_logo) . '"' . $logo_width . $logo_height . ' alt="' . get_bloginfo('name') . '">';
    } else {
        echo get_bloginfo('name');
    }
    echo '</a>';
}

if (!function_exists('careerfy_header_section')) {

    /**
     * Site header section.
     * @return markup
     */
    function careerfy_header_section()
    {
        global $careerfy_framework_options, $jobsearch_plugin_options;

        $header_style = isset($careerfy_framework_options['header-style']) ? $careerfy_framework_options['header-style'] : '';
        $header_btn_txt = isset($careerfy_framework_options['header-button-text']) ? $careerfy_framework_options['header-button-text'] : '';
        $header_btn_url = isset($careerfy_framework_options['header-button-url']) ? $careerfy_framework_options['header-button-url'] : '';
        $header_btn_page = isset($careerfy_framework_options['header-button-page']) ? $careerfy_framework_options['header-button-page'] : '';
        $header_link_page_1 = isset($careerfy_framework_options['header-button-page-1']) ? $careerfy_framework_options['header-button-page-1'] : '';
        $header_link_page_2 = isset($careerfy_framework_options['header-button-page-2']) ? $careerfy_framework_options['header-button-page-2'] : '';
        $top_header = isset($careerfy_framework_options['careerfy-top-header']) ? $careerfy_framework_options['careerfy-top-header'] : '';

        //$post_without_reg = isset($jobsearch_plugin_options['job-post-wout-reg']) ? $jobsearch_plugin_options['job-post-wout-reg'] : '';
        if ($header_style == 'style9') {
            $header_email = isset($careerfy_framework_options['header_email']) ? $careerfy_framework_options['header_email'] : '';
            $header_phone = isset($careerfy_framework_options['header_phone']) ? $careerfy_framework_options['header_phone'] : '';
        }

        $top_strip = isset($careerfy_framework_options['geek-top-strip']) ? $careerfy_framework_options['geek-top-strip'] : '';
        $top_location = isset($careerfy_framework_options['geek-top-location']) ? $careerfy_framework_options['geek-top-location'] : '';
        $top_social = isset($careerfy_framework_options['geek-top-social']) ? $careerfy_framework_options['geek-top-social'] : '';
        $top_phone = isset($careerfy_framework_options['geek-top-phone']) ? $careerfy_framework_options['geek-top-phone'] : '';
        $top_days = isset($careerfy_framework_options['careerfy-top-days']) ? $careerfy_framework_options['careerfy-top-days'] : '';
        $top_time = isset($careerfy_framework_options['careerfy-top-time']) ? $careerfy_framework_options['careerfy-top-time'] : '';
        $top_days_sec = isset($careerfy_framework_options['careerfy-top-days-second']) ? $careerfy_framework_options['careerfy-top-days-second'] : '';
        $top_time_sec = isset($careerfy_framework_options['careerfy-top-time-second']) ? $careerfy_framework_options['careerfy-top-time-second'] : '';

        if ($header_style == 'style22') {
            ?>

            <?php if ($top_strip == 'on') { ?>
                <div class="container">
                <div class="careerfy-header-twentytwo-strip">
                    <?php
                    careerfy_site_logo();
                    ?>
                    <div class="careerfy-header-strip-list">
                        <ul>
                            <li><i class="careerfy-icon careerfy-clock"></i>
                                <span><?php echo esc_html__($top_days, 'careerfy') ?></span>
                                <small><?php echo esc_html__($top_time, 'careerfy') ?></small>
                            </li>
                            <li><i class="fa fa-map-marker"></i>
                                <span><?php echo esc_html__($top_days_sec, 'careerfy') ?></span>
                                <small><?php echo esc_html__($top_time_sec, 'careerfy') ?></small>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php } ?>
            <div class="careerfy-header-twentytwo-wrapper">

                <nav class="jobsearch-navigation">
                    <div class="collapse" id="jobsearch-navbar-collapse-1">
                        <ul class="navbar-nav">
                            <?php do_action('careerfy_header_navigation') ?>
                        </ul>
                    </div>
                </nav>
                <div class="top-strip-social-links">
                    <ul class="careerfy-header-twentytwo-user">
                        <?php
                        $args = array(
                            'is_popup' => true,
                        );
                        do_action('jobsearch_user_account_links', $args);
                        ?>
                    </ul>

                    <?php if ($top_phone != '') { ?>
                        <p><i class="fa fa-phone"></i> <?php echo($top_phone) ?></p>
                    <?php } ?>
                </div>
            </div>
            </div>


        <?php } else if ($header_style == 'style21') { ?>

            <?php if ($top_strip == 'on') { ?>
                <div class="careerfy-header-twentyone-strip">
                    <div class="container">
                        <?php if ($top_phone != '') { ?>
                            <p>
                                <i class="fa fa-phone"></i><?php echo esc_html__('Call us on:', 'careerfy') ?> <a
                                        href="tel:<?php echo($top_phone) ?>"><?php echo($top_phone) ?></a>
                            </p>
                        <?php } ?>
                        <div class="top-strip-social-links">
                            <?php
                            if ($top_social == 'on') {
                                do_action('careerfy_social_icons', 'careerfy-header-twentyone-social', '');
                            }
                            ?>

                            <ul class="careerfy-header-twentyone-user">
                                <?php
                                $args = array(
                                    'is_popup' => true,
                                );
                                do_action('jobsearch_user_account_links', $args);
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="careerfy-header-twentyone-wrapper">
                <div class="container">
                    <?php
                    careerfy_site_logo();
                    ?>
                    <nav class="jobsearch-navigation">
                        <div class="collapse" id="jobsearch-navbar-collapse-1">
                            <ul class="navbar-nav">
                                <?php do_action('careerfy_header_navigation') ?>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>


        <?php } else if ($header_style == 'style20') { ?>

            <div class="container">
                <div class="careerfy-header-twenty-wrapper">
                    <?php
                    careerfy_site_logo();
                    ?>
                    <div class="careerfy-header-twenty-inner-wrapper">

                        <ul class="careerfy-header-twenty-user">
                            <?php
                            $args = array(
                                'is_popup' => true,
                            );
                            do_action('jobsearch_user_account_links', $args);
                            ?>
                        </ul>
                        <nav class="jobsearch-navigation">
                            <div class="collapse" id="jobsearch-navbar-collapse-1">
                                <ul class="navbar-nav">
                                    <?php do_action('careerfy_header_navigation') ?>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>


        <?php } else if ($header_style == 'style19') { ?>

            <div class="container">
                <?php
                careerfy_site_logo();
                ?>
                <div class="careerfy-header-nineteen-wrapper">
                    <?php if ($top_strip == 'on') { ?>
                        <div class="careerfy-header-nineteen-strip">

                            <?php
                            if ($top_social == 'on') {
                                do_action('careerfy_social_icons', 'careerfy-header-nineteen-social', '');
                            }
                            ?>

                            <?php if ($top_phone != '') { ?>
                                <p><i class="fa fa-phone"></i><?php echo($top_phone) ?></p>
                            <?php } ?>
                            <?php if (!empty($top_time)) { ?>
                                <p>
                                    <i class="fa fa-clock-o"></i><?php echo esc_html__($top_time, 'careerfy') ?>
                                </p>
                            <?php } ?>
                            <?php if ($top_location != '') { ?>
                                <p><i class="fa fa-map-marker"></i><?php echo($top_location) ?></p>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <nav class="jobsearch-navigation">
                        <div class="collapse" id="jobsearch-navbar-collapse-1">
                            <ul class="navbar-nav">
                                <?php do_action('careerfy_header_navigation') ?>
                            </ul>
                        </div>
                    </nav>

                    <ul class="careerfy-headerninteen-user">
                        <?php
                        $args = array(
                            'is_popup' => true,
                        );
                        do_action('jobsearch_user_account_links', $args);
                        ?>
                    </ul>
                </div>
            </div>

        <?php } else if ($header_style == 'style18') { ?>

            <div class="careerfy-header-eighteen-main">
                <div class="careerfy-blockeighteen-element">
                    <?php
                    careerfy_site_logo();
                    ?>
                    <nav class="jobsearch-navigation">
                        <div class="collapse" id="jobsearch-navbar-collapse-1">
                            <ul class="navbar-nav">
                                <?php do_action('careerfy_header_navigation') ?>
                            </ul>
                        </div>
                    </nav>
                </div>
                <div class="careerfy-blockeighteen-element">
                    <ul class="careerfy-headereighteen-user">
                        <?php
                        $args = array(
                            'is_popup' => true,
                        );
                        do_action('jobsearch_user_account_links', $args);
                        ?>
                    </ul>
                    <?php
                    $btn_page_obj = '';
                    if ($header_btn_page != '') {
                        $btn_page_obj = get_page_by_path($header_btn_page, 'OBJECT', 'page');
                    }

                    if (is_object($btn_page_obj) && isset($btn_page_obj->ID)) {
                        ob_start();
                        ?>
                        <a href="<?php echo apply_filters('careerfy_header_button_url', get_permalink($btn_page_obj->ID), $btn_page_obj->ID) ?>"
                           class="careerfy-headerfifteen-btn"><?php echo apply_filters('careerfy_header_button_text', get_the_title($btn_page_obj->ID), $btn_page_obj->ID) ?>
                        </a>
                        <?php
                        $btn_html = ob_get_clean();
                        echo apply_filters('careerfy_header_button_html', $btn_html);
                    } else {
                        if ($header_btn_txt != '' && $header_btn_url != '') {
                            ?>
                            <a href="<?php echo($header_btn_url) ?>"
                               class="careerfy-headerfifteen-btn"><?php echo($header_btn_txt) ?>
                            </a>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <?php
        } else if ($header_style == 'style17') {

            $top_strip = isset($careerfy_framework_options['geek-top-strip']) ? $careerfy_framework_options['geek-top-strip'] : '';
            $top_phone = isset($careerfy_framework_options['geek-top-phone']) ? $careerfy_framework_options['geek-top-phone'] : '';
            $top_email = isset($careerfy_framework_options['geek-top-email']) ? $careerfy_framework_options['geek-top-email'] : '';
            $top_social = isset($careerfy_framework_options['geek-top-social']) ? $careerfy_framework_options['geek-top-social'] : '';

            ob_start();
            if ($top_strip == 'on') {
                ?>
                <div class="careerfy-header-seventeen-strip">
                    <div class="container">
                        <?php if ($top_email != '') { ?>
                            <p><i class="fa fa-envelope"></i><a
                                        href="mailto:<?php echo($top_email) ?>"><?php echo($top_email) ?></a></p>
                            <?php
                        }
                        if ($top_phone != '') {
                            ?>
                            <p><i class="fa fa-phone"></i><a
                                        href="tel:<?php echo $top_phone ?>"><?php echo $top_phone ?></a></p>
                        <?php } ?>
                        <?php
                        if ($top_social == 'on') {
                            do_action('careerfy_social_icons', 'careerfy-header-seventeen-social', '');
                        }
                        ?>
                    </div>
                </div>
                <div class="careerfy-header-seventeen-main">
                    <div class="container">
                        <?php
                        careerfy_site_logo();
                        ?>
                        <div class="careerfy-header-seventeen-right">
                            <div class="careerfy-blockseventeen-element">
                                <ul class="careerfy-headerseventeen-user">
                                    <?php
                                    $args = array(
                                        'is_popup' => true,
                                    );
                                    do_action('jobsearch_user_account_links', $args);
                                    ?>
                                </ul>
                                <?php
                                $btn_page_obj = '';
                                if ($header_btn_page != '') {
                                    $btn_page_obj = get_page_by_path($header_btn_page, 'OBJECT', 'page');
                                }
                                if (is_object($btn_page_obj) && isset($btn_page_obj->ID)) {
                                    ob_start();
                                    ?>
                                    <a href="<?php echo apply_filters('careerfy_header_button_url', get_permalink($btn_page_obj->ID), $btn_page_obj->ID) ?>"
                                       class="careerfy-headerseventeen-btn"><?php echo apply_filters('careerfy_header_button_text', get_the_title($btn_page_obj->ID), $btn_page_obj->ID) ?>
                                    </a>
                                    <?php
                                    $btn_html = ob_get_clean();
                                    echo apply_filters('careerfy_header_button_html', $btn_html);
                                } else {
                                    if ($header_btn_txt != '' && $header_btn_url != '') {
                                        ?>
                                        <a href="<?php echo($header_btn_url) ?>"
                                           class="careerfy-headerseventeen-btn"><?php echo($header_btn_txt) ?>
                                        </a>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            <nav class="jobsearch-navigation">
                                <div class="collapse" id="jobsearch-navbar-collapse-1">
                                    <ul class="navbar-nav">
                                        <?php do_action('careerfy_header_navigation') ?>
                                    </ul>
                                </div>
                            </nav>
                        </div>
                    </div>

                </div>
            <?php } ?>
            <?php
        } else if ($header_style == 'style16') {
            ob_start();
            ?>
            <div class="careerfy-header-sixteen-main">
                <div class="careerfy-blocksixteen-element">
                    <?php
                    careerfy_site_logo();
                    ?>

                    <nav class="jobsearch-navigation">
                        <div class="collapse" id="jobsearch-navbar-collapse-1">
                            <ul class="navbar-nav">
                                <?php do_action('careerfy_header_navigation') ?>
                            </ul>
                        </div>
                    </nav>
                </div>
                <div class="careerfy-blocksixteen-element">
                    <ul class="careerfy-headersixteen-user">
                        <?php
                        $args = array(
                            'is_popup' => true,
                        );
                        do_action('jobsearch_user_account_links', $args);
                        ?>
                    </ul>
                    <?php
                    $btn_page_obj = '';
                    if ($header_btn_page != '') {
                        $btn_page_obj = get_page_by_path($header_btn_page, 'OBJECT', 'page');
                    }
                    if (is_object($btn_page_obj) && isset($btn_page_obj->ID)) {
                        ob_start();
                        ?>
                        <a href="<?php echo apply_filters('careerfy_header_button_url', get_permalink($btn_page_obj->ID), $btn_page_obj->ID) ?>"
                           class="careerfy-headersixteen-btn"><?php echo apply_filters('careerfy_header_button_text', get_the_title($btn_page_obj->ID), $btn_page_obj->ID) ?>
                        </a>
                        <?php
                        $btn_html = ob_get_clean();
                        echo apply_filters('careerfy_header_button_html', $btn_html);
                    } else {
                        if ($header_btn_txt != '' && $header_btn_url != '') {
                            ?>
                            <a href="<?php echo($header_btn_url) ?>"
                               class="careerfy-headersixteen-btn"><?php echo($header_btn_txt) ?>
                            </a>
                            <?php
                        }
                    }
                    ?>
                </div>

            </div>
            <?php
        } else if ($header_style == 'style15') {
            ob_start();
            ?>
            <div class="careerfy-header-fifteen-main">
                <?php
                careerfy_site_logo();
                ?>
                <nav class="jobsearch-navigation">
                    <div class="collapse" id="jobsearch-navbar-collapse-1">
                        <ul class="navbar-nav">
                            <?php do_action('careerfy_header_navigation') ?>
                        </ul>
                    </div>
                </nav>

                <div class="careerfy-blockfifteen-element">
                    <ul class="careerfy-headerfifteen-user">
                        <?php
                        $args = array(
                            'is_popup' => true,
                        );
                        do_action('jobsearch_user_account_links', $args);
                        ?>
                    </ul>
                    <?php
                    $btn_page_obj = '';
                    if ($header_btn_page != '') {
                        $btn_page_obj = get_page_by_path($header_btn_page, 'OBJECT', 'page');
                    }
                    if (is_object($btn_page_obj) && isset($btn_page_obj->ID)) {
                        ob_start();
                        ?>
                        <a href="<?php echo apply_filters('careerfy_header_button_url', get_permalink($btn_page_obj->ID), $btn_page_obj->ID) ?>"
                           class="careerfy-headerfifteen-btn"><?php echo apply_filters('careerfy_header_button_text', get_the_title($btn_page_obj->ID), $btn_page_obj->ID) ?>
                        </a>
                        <?php
                        $btn_html = ob_get_clean();
                        echo apply_filters('careerfy_header_button_html', $btn_html);
                    } else {
                        if ($header_btn_txt != '' && $header_btn_url != '') {
                            ?>
                            <a href="<?php echo($header_btn_url) ?>"
                               class="careerfy-headerfifteen-btn"><?php echo($header_btn_txt) ?>
                            </a>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        <?php } else if ($header_style == 'style14') {
            ob_start();
            ?>
            <div class="careerfy-header-fourteen-main">
                <nav class="jobsearch-navigation">
                    <div class="collapse" id="jobsearch-navbar-collapse-1">
                        <ul class="navbar-nav">
                            <?php do_action('careerfy_header_navigation') ?>
                        </ul>
                    </div>
                </nav>
                <?php
                careerfy_site_logo('careerfy-logo-fourteen');
                ?>
                <div class="careerfy-blockfourteen-element">
                    <ul class="careerfy-headerfourteen-user">
                        <?php
                        $args = array(
                            'is_popup' => true,
                        );
                        do_action('jobsearch_user_account_links', $args);
                        ?>
                    </ul>
                    <?php
                    $btn_page_obj = '';
                    if ($header_btn_page != '') {
                        $btn_page_obj = get_page_by_path($header_btn_page, 'OBJECT', 'page');
                    }
                    if (is_object($btn_page_obj) && isset($btn_page_obj->ID)) {
                        ob_start();
                        ?>
                        <a href="<?php echo apply_filters('careerfy_header_button_url', get_permalink($btn_page_obj->ID), $btn_page_obj->ID) ?>"
                           class="careerfy-headerfourteen-btn"><?php echo apply_filters('careerfy_header_button_text', get_the_title($btn_page_obj->ID), $btn_page_obj->ID) ?>
                        </a>
                        <?php
                        $btn_html = ob_get_clean();
                        echo apply_filters('careerfy_header_button_html', $btn_html);
                    } else {
                        if ($header_btn_txt != '' && $header_btn_url != '') {
                            ?>
                            <a href="<?php echo($header_btn_url) ?>"
                               class="careerfy-headerfourteen-btn"><?php echo($header_btn_txt) ?>
                            </a>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>

            <?php
        } else if ($header_style == "style13") {
            ob_start();
            ?>
            <!-- Header Main -->
            <div class="careerfy-header-thirteen-main">
                <div class="careerfy-blockthirteen-element">
                    <?php
                    careerfy_site_logo();
                    ?>
                    <nav class="jobsearch-navigation">
                        <div class="collapse " id="jobsearch-navbar-collapse-1">
                            <ul class="navbar-nav">
                                <?php do_action('careerfy_header_navigation') ?>
                            </ul>
                        </div>
                    </nav>
                </div>
                <div class="careerfy-blockthirteen-element">
                    <ul class="careerfy-headerthirteen-user">
                        <?php
                        $header_link_page_1_obj = '';
                        if ($header_link_page_1 != '') {
                            $header_link_page_1_obj = get_page_by_path($header_link_page_1, 'OBJECT', 'page');
                        }
                        if (is_object($header_link_page_1_obj) && isset($header_link_page_1_obj->ID)) {
                            ?>
                            <li>
                                <a href="<?php echo get_permalink($header_link_page_1_obj->ID) ?>"><?php echo get_the_title($header_link_page_1_obj->ID) ?></a>
                            </li>
                            <?php
                        }

                        $header_link_page_2_obj = '';
                        if ($header_link_page_2 != '') {
                            $header_link_page_2_obj = get_page_by_path($header_link_page_2, 'OBJECT', 'page');
                        }

                        if (is_object($header_link_page_2_obj) && isset($header_link_page_2_obj->ID)) {
                            ?>
                            <li>
                                <a href="<?php echo get_permalink($header_link_page_2_obj->ID) ?>"><?php echo get_the_title($header_link_page_2_obj->ID) ?></a>
                            </li>
                            <?php
                        }

                        $args = array(
                            'is_popup' => true,
                        );
                        do_action('jobsearch_user_account_links', $args);
                        ?>
                    </ul>
                    <?php
                    $btn_page_obj = '';

                    if ($header_btn_page != '') {
                        $btn_page_obj = get_page_by_path($header_btn_page, 'OBJECT', 'page');
                    }

                    if (is_object($btn_page_obj) && isset($btn_page_obj->ID)) {
                        ob_start();
                        ?>
                        <a href="<?php echo get_permalink($btn_page_obj->ID) ?>"
                           class="careerfy-headerthirteen-btn"><?php echo apply_filters('careerfy_header_button_text', get_the_title($btn_page_obj->ID), $btn_page_obj->ID) ?>
                        </a>
                        <?php
                        $btn_html = ob_get_clean();
                        echo apply_filters('careerfy_header_button_html', $btn_html);
                    } else {
                        if ($header_btn_txt != '' && $header_btn_url != '') {
                            ob_start();
                            ?>
                            <a href="<?php echo get_permalink($btn_page_obj->ID) ?>"
                               class="careerfy-headerthirteen-btn"><?php echo($header_btn_txt) ?>
                            </a>
                            <?php
                            $btn_html = ob_get_clean();
                            echo apply_filters('careerfy_header_button_html', $btn_html);
                        }
                    }
                    ?>
                </div>
            </div>
            <?php
        } else if ($header_style == "style12") {
            ob_start();
            if ($top_header == 'on') {
                ?>
                <div class="careerfy-header-twelve-main">
                    <div class="container">
                        <div class="careerfy-block-element">
                            <?php
                            careerfy_site_logo();
                            ?>
                        </div>
                        <div class="careerfy-block-element">
                            <?php
                            do_action('careerfy_header_twelve_top_navigation');
                            ?>
                        </div>
                        <div class="careerfy-block-element">
                            <ul class="careerfy-user-section">
                                <?php
                                $args = array(
                                    'is_popup' => true,
                                );
                                do_action('jobsearch_user_account_links', $args);
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php
        } else if ($header_style == "style11") {
            ob_start();
            ?>
            <div class="careerfy-header-eleven-main">
                <?php
                careerfy_site_logo();
                ?>
                <nav class="jobsearch-navigation">
                    <div class="collapse" id="jobsearch-navbar-collapse-1">
                        <ul class="navbar-nav">
                            <?php do_action('careerfy_header_navigation') ?>
                        </ul>
                    </div>
                </nav>
                <div class="careerfy-headereleven-right">
                    <ul class="careerfy-headereleven-user">
                        <?php
                        $args = array(
                            'is_popup' => true,
                        );

                        do_action('jobsearch_user_account_links', $args);
                        ?>
                    </ul>
                    <?php
                    $btn_page_obj = '';
                    if ($header_btn_page != '') {
                        $btn_page_obj = get_page_by_path($header_btn_page, 'OBJECT', 'page');
                    }

                    if (is_object($btn_page_obj) && isset($btn_page_obj->ID)) {
                        ob_start();
                        ?>
                        <a href="<?php echo apply_filters('careerfy_header_button_url', get_permalink($btn_page_obj->ID), $btn_page_obj->ID) ?>"
                           class="careerfy-headereleven-btn"><?php echo apply_filters('careerfy_header_button_text', get_the_title($btn_page_obj->ID), $btn_page_obj->ID) ?>
                        </a>
                        <?php
                        $btn_html = ob_get_clean();
                        echo apply_filters('careerfy_header_button_html', $btn_html);
                    } else {

                        if ($header_btn_txt != '' && $header_btn_url != '') {
                            ?>
                            <a href="<?php echo($header_btn_url) ?>"
                               class="careerfy-headereleven-btn"><?php echo($header_btn_txt) ?>
                            </a>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <?php
        } else if ($header_style == "style10") {
            ob_start();
            ?>
            <div class="careerfy-header10-main">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <?php
                            careerfy_site_logo();
                            ?>
                            <div class="careerfy-header10-usersec-wrap">
                                <ul class="careerfy-header10-usersec">
                                    <?php careerfy_wpml_lang_switcher(); ?>
                                    <?php
                                    $args = array(
                                        'is_popup' => true,
                                    );
                                    do_action('jobsearch_user_account_links', $args);
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main -->
            <!-- MainHeader -->
            <div class="careerfy-headerten-mainnav">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">

                            <nav class="jobsearch-navigation">
                                <div class="collapse" id="jobsearch-navbar-collapse-1">
                                    <ul class="navbar-nav">
                                        <?php do_action('careerfy_header_navigation') ?>
                                    </ul>
                                </div>
                            </nav>
                            <?php
                            $btn_page_obj = '';
                            if ($header_btn_page != '') {
                                $btn_page_obj = get_page_by_path($header_btn_page, 'OBJECT', 'page');
                            }

                            if (is_object($btn_page_obj) && isset($btn_page_obj->ID)) {
                                ob_start();
                                ?>
                                <a href="<?php echo apply_filters('careerfy_header_button_url', get_permalink($btn_page_obj->ID), $btn_page_obj->ID) ?>"
                                   class="careerfy-headerten-btn"><i
                                            class="careerfy-icon careerfy-plus-fill-circle"></i><?php echo apply_filters('careerfy_header_button_text', get_the_title($btn_page_obj->ID), $btn_page_obj->ID) ?>
                                </a>
                                <?php
                                $btn_html = ob_get_clean();
                                echo apply_filters('careerfy_header_button_html', $btn_html);
                            } else {
                                if ($header_btn_txt != '' && $header_btn_url != '') {
                                    ?>
                                    <a href="<?php echo($header_btn_url) ?>"
                                       class="careerfy-headerten-btn"><i
                                                class="careerfy-icon careerfy-plus-fill-circle"></i><?php echo($header_btn_txt) ?>
                                    </a>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- MainHeader -->
            <?php
        } else if ($header_style == "style9") {
            ob_start();
            ?>
            <!-- TopStrip -->
            <?php
            ob_start();
            ?>
            <div class="careerfy-topstrip">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <?php if ($header_email != "") { ?>
                                <p><i class="fa fa-envelope"></i> <a
                                            href="mailto:<?php echo $header_email ?>"><?php echo $header_email ?></a>
                                </p>
                            <?php } ?>
                            <?php if ($header_phone != "") { ?>
                                <p><i class="fa fa-phone"></i><a
                                            href="tel:<?php echo $header_phone ?>"><?php echo $header_phone ?></a></p>
                            <?php } ?>
                            <ul class="careerfy-stripuser">
                                <?php
                                $args = array(
                                    'is_popup' => true,
                                );
                                do_action('jobsearch_user_account_links', $args);
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $top_strphtml = ob_get_clean();
            echo apply_filters('careerfy_header_style9_top_strip_html', $top_strphtml, $header_email, $header_phone);
            ?>
            <!-- TopStrip -->

            <!-- MainHeader -->
            <div class="careerfy-headernine-main">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <?php
                            careerfy_site_logo();
                            ?>
                            <div class="careerfy-right">
                                <nav class="jobsearch-navigation">

                                    <div class="collapse navbar-collapse" id="jobsearch-navbar-collapse-1">
                                        <?php do_action('careerfy_header_navigation') ?>
                                    </div>
                                </nav>
                                <?php
                                $btn_page_obj = '';
                                if ($header_btn_page != '') {
                                    $btn_page_obj = get_page_by_path($header_btn_page, 'OBJECT', 'page');
                                }

                                if (is_object($btn_page_obj) && isset($btn_page_obj->ID)) {
                                    ob_start();
                                    ?>
                                    <a href="<?php echo apply_filters('careerfy_header_button_url', get_permalink($btn_page_obj->ID), $btn_page_obj->ID) ?>"
                                       class="careerfy-headernine-btn"><?php echo apply_filters('careerfy_header_button_text', get_the_title($btn_page_obj->ID), $btn_page_obj->ID) ?></a>
                                    <?php
                                    $btn_html = ob_get_clean();
                                    echo apply_filters('careerfy_header_button_html', $btn_html);
                                } else {
                                    if ($header_btn_txt != '' && $header_btn_url != '') {
                                        ?>
                                        <a href="<?php echo($header_btn_url) ?>"
                                           class="careerfy-headernine-btn"><?php echo($header_btn_txt) ?></a>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- MainHeader -->
            <?php
        } else if ($header_style == 'style8') {
            $top_strip = isset($careerfy_framework_options['geek-top-strip']) ? $careerfy_framework_options['geek-top-strip'] : '';
            $top_location = isset($careerfy_framework_options['geek-top-location']) ? $careerfy_framework_options['geek-top-location'] : '';
            $top_phone = isset($careerfy_framework_options['geek-top-phone']) ? $careerfy_framework_options['geek-top-phone'] : '';
            $top_email = isset($careerfy_framework_options['geek-top-email']) ? $careerfy_framework_options['geek-top-email'] : '';
            $top_social = isset($careerfy_framework_options['geek-top-social']) ? $careerfy_framework_options['geek-top-social'] : '';
            if (!(class_exists('Careerfy_MMC') && true == Careerfy_MMC::is_construction_mode_enabled(false))) {
                if ($top_strip == 'on') {
                    ?>
                    <div class="jobsearch-top-strip">
                        <ul class="jobsearch-strip-info">
                            <?php
                            if ($top_location != '') {
                                ?>
                                <li><i class="careerfy-icon careerfy-pin-line"></i> <?php echo($top_location) ?></li>
                                <?php
                            }
                            if ($top_phone != '') {
                                ?>
                                <li>
                                    <i class="careerfy-icon careerfy-technology"></i> <a
                                            href="tel:<?php echo($top_phone) ?>"><?php printf(esc_html__('%s', 'careerfy'), $top_phone) ?></a>
                                </li>
                                <?php
                            }
                            if ($top_email != '') {
                                ?>
                                <li><i class="careerfy-icon careerfy-envelope"></i> <a
                                            href="mailto:<?php echo($top_email) ?>"><?php echo($top_email) ?></a></li>
                            <?php } ?>
                        </ul>
                        <?php
                        if ($top_social == 'on') {
                            do_action('careerfy_social_icons', 'jobsearch-strip-media', '');
                        }
                        ?>
                    </div>
                <?php } ?>
                <div class="jobsearch-main-header">
                    <div class="header-tabel">
                        <div class="header-row">
                            <div class="eight-cell">
                                <?php
                                careerfy_site_logo();
                                ?>
                            </div>

                            <div class="eight-cell">
                                <div class="eight-right">
                                    <ul class="jobsearch-headeight-option">
                                        <?php
                                        $args = array(
                                            'is_popup' => true,
                                        );
                                        do_action('jobsearch_user_account_links', $args);
                                        $btn_page_obj = '';
                                        if ($header_btn_page != '') {
                                            $btn_page_obj = get_page_by_path($header_btn_page, 'OBJECT', 'page');
                                        }
                                        if (is_object($btn_page_obj) && isset($btn_page_obj->ID)) {
                                            ob_start();
                                            ?>
                                            <li class="jobsearch-open-signup-tab active"><a
                                                        href="<?php echo apply_filters('careerfy_header_button_url', get_permalink($btn_page_obj->ID), $btn_page_obj->ID) ?>"><i
                                                            class="careerfy-icon careerfy-upload-arrow"></i> <?php echo apply_filters('careerfy_header_button_text', get_the_title($btn_page_obj->ID), $btn_page_obj->ID) ?>
                                                </a></li>
                                            <?php
                                            $btn_html = ob_get_clean();
                                            echo apply_filters('careerfy_header_button_html', $btn_html);
                                        }
                                        ?>
                                    </ul>
                                    <?php do_action('careerfy_header8_navigation') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else if ($header_style == 'style4') { ?>
            <div class="container">
                <div class="row">
                    <div class="header-tabel">
                        <div class="header-row">
                            <div class="careerfy-logo-con">
                                <?php
                                careerfy_site_logo();
                                ?>
                                <div class="careerfy-nav-area-wrap">
                                    <a href="javascript:void(0);" class="nav-list-mode careerfy-nav-toogle"><img
                                                src="<?php echo get_template_directory_uri() ?>/images/nav-list-icon.png"
                                                alt=""></a>
                                    <div class="careerfy-nav-area">
                                        <?php do_action('careerfy_header_navigation') ?>
                                    </div>
                                </div>
                            </div>
                            <?php if (class_exists('Careerfy_framework')) { ?>
                                <div class="careerfy-btns-con">
                                    <ul class="careerfy-header-option">
                                        <?php
                                        $args = array(
                                            'is_popup' => true,
                                        );
                                        do_action('jobsearch_user_account_links', $args);
                                        $btn_page_obj = '';
                                        if ($header_btn_page != '') {
                                            $btn_page_obj = get_page_by_path($header_btn_page, 'OBJECT', 'page');
                                        }
                                        if (is_object($btn_page_obj) && isset($btn_page_obj->ID)) {
                                            ob_start();
                                            ?>
                                            <li>
                                                <a href="<?php echo apply_filters('careerfy_header_button_url', get_permalink($btn_page_obj->ID), $btn_page_obj->ID) ?>"><i
                                                            class="careerfy-icon careerfy-upload-arrow"></i> <?php echo apply_filters('careerfy_header_button_text', get_the_title($btn_page_obj->ID), $btn_page_obj->ID) ?>
                                                </a>
                                            </li>
                                            <?php
                                            $btn_html = ob_get_clean();
                                            echo apply_filters('careerfy_header_button_html', $btn_html);
                                        } else {
                                            if ($header_btn_txt != '' && $header_btn_url != '') {
                                                ?>
                                                <li><a href="<?php echo($header_btn_url) ?>"><i
                                                                class="careerfy-icon careerfy-upload-arrow"></i> <?php echo($header_btn_txt) ?>
                                                    </a></li>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <?php
                                do_action('careerfy_header_top_after_btns');
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else if ($header_style == 'style5') { ?>
            <div class="container">
                <?php
                careerfy_site_logo();
                ?>
                <div class="careerfy-right">
                    <div class="careerfy-nav-wrap">
                        <div class="careerfy-navigation-wrap">
                            <?php do_action('careerfy_header_navigation') ?>
                        </div>
                        <?php
                        if (class_exists('Careerfy_framework')) {
                            ?>
                            <ul class="careerfy-headfive-option">
                                <?php
                                $btn_page_obj = '';
                                if ($header_btn_page != '') {
                                    $btn_page_obj = get_page_by_path($header_btn_page, 'OBJECT', 'page');
                                }
                                if (is_object($btn_page_obj) && isset($btn_page_obj->ID)) {
                                    ob_start();
                                    ?>
                                    <li>
                                        <a href="<?php echo apply_filters('careerfy_header_button_url', get_permalink($btn_page_obj->ID), $btn_page_obj->ID) ?>"><i
                                                    class="careerfy-icon careerfy-upload-arrow"></i> <?php echo apply_filters('careerfy_header_button_text', get_the_title($btn_page_obj->ID), $btn_page_obj->ID) ?>
                                        </a></li>
                                    <?php
                                    $btn_html = ob_get_clean();
                                    echo apply_filters('careerfy_header_button_html', $btn_html);
                                } else {
                                    if ($header_btn_txt != '' && $header_btn_url != '') {
                                        ?>
                                        <li><a href="<?php echo($header_btn_url) ?>"><i
                                                        class="careerfy-icon careerfy-upload-arrow"></i> <?php echo($header_btn_txt) ?>
                                            </a></li>
                                        <?php
                                    }
                                }
                                $args = array(
                                    'is_popup' => true,
                                );
                                do_action('jobsearch_user_account_links', $args);
                                ?>
                            </ul>
                            <?php
                            do_action('careerfy_header_top_after_btns');
                        }
                        ?>
                    </div>

                </div>
            </div>
        <?php } else if ($header_style == 'style6') { ?>
            <div class="container">
                <div class="row">
                    <?php
                    careerfy_site_logo();
                    ?>
                    <div class="careerfy-right">
                        <div class="careerfy-navigation-wrapper">
                            <?php do_action('careerfy_header_navigation') ?>
                        </div>
                        <?php
                        if (class_exists('Careerfy_framework')) {
                            ?>
                            <ul class="careerfy-headsix-option">
                                <?php
                                $btn_page_obj = '';
                                if ($header_btn_page != '') {
                                    $btn_page_obj = get_page_by_path($header_btn_page, 'OBJECT', 'page');
                                }
                                if (is_object($btn_page_obj) && isset($btn_page_obj->ID)) {
                                    ob_start();
                                    ?>
                                    <li><a class="careerfy-color"
                                           href="<?php echo apply_filters('careerfy_header_button_url', get_permalink($btn_page_obj->ID), $btn_page_obj->ID) ?>"><?php echo apply_filters('careerfy_header_button_text', get_the_title($btn_page_obj->ID), $btn_page_obj->ID) ?></a>
                                    </li>
                                    <?php
                                    $btn_html = ob_get_clean();
                                    echo apply_filters('careerfy_header_button_html', $btn_html);
                                } else {
                                    if ($header_btn_txt != '' && $header_btn_url != '') {
                                        ?>
                                        <li><a href="<?php echo($header_btn_url) ?>"
                                               class="careerfy-color"><?php echo($header_btn_txt) ?></a></li>
                                        <?php
                                    }
                                }
                                $args = array(
                                    'is_popup' => true,
                                );
                                do_action('jobsearch_user_account_links', $args);
                                ?>
                            </ul>
                            <?php
                            do_action('careerfy_header_top_after_btns');
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php } else if ($header_style == 'style7') { ?>
            <div class="container">
                <div class="row">
                    <?php
                    careerfy_site_logo();
                    ?>
                    <div class="careerfy-right">
                        <div class="careerfy-navigation-wrap">
                            <?php do_action('careerfy_header_navigation') ?>
                        </div>
                        <?php
                        if (class_exists('Careerfy_framework')) {
                            ?>
                            <ul class="careerfy-headseven-option">
                                <?php
                                $btn_page_obj = '';
                                if ($header_btn_page != '') {
                                    $btn_page_obj = get_page_by_path($header_btn_page, 'OBJECT', 'page');
                                }

                                if (is_user_logged_in()) {
                                    if (is_object($btn_page_obj) && isset($btn_page_obj->ID)) {
                                        ob_start();
                                        ?>
                                        <li><a class="careerfy-color careerfy-post-job"
                                               href="<?php echo apply_filters('careerfy_header_button_url', get_permalink($btn_page_obj->ID), $btn_page_obj->ID) ?>"><?php echo apply_filters('careerfy_header_button_text', get_the_title($btn_page_obj->ID), $btn_page_obj->ID) ?></a>
                                        </li>
                                        <?php
                                        $btn_html = ob_get_clean();
                                        echo apply_filters('careerfy_header_button_html', $btn_html);
                                    } else {
                                        if ($header_btn_txt != '' && $header_btn_url != '') {
                                            ?>
                                            <li><a href="<?php echo($header_btn_url) ?>"
                                                   class="careerfy-color"><?php echo($header_btn_txt) ?></a></li>
                                            <?php
                                        }
                                    }
                                }
                                $args = array(
                                    'is_popup' => true,
                                );
                                do_action('jobsearch_user_account_links', $args);
                                ?>
                            </ul>
                            <?php
                            do_action('careerfy_header_top_after_btns');
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php } else if ($header_style == 'style3') {
            $top_strip = isset($careerfy_framework_options['geek-top-strip']) ? $careerfy_framework_options['geek-top-strip'] : '';
            $top_location = isset($careerfy_framework_options['geek-top-location']) ? $careerfy_framework_options['geek-top-location'] : '';
            $top_phone = isset($careerfy_framework_options['geek-top-phone']) ? $careerfy_framework_options['geek-top-phone'] : '';
            $top_email = isset($careerfy_framework_options['geek-top-email']) ? $careerfy_framework_options['geek-top-email'] : '';
            $top_social = isset($careerfy_framework_options['geek-top-social']) ? $careerfy_framework_options['geek-top-social'] : '';
            if (!(class_exists('Careerfy_MMC') && true == Careerfy_MMC::is_construction_mode_enabled(false))) {
                if ($top_strip == 'on') {
                    ob_start();
                    ?>
                    <div class="jobsearch-top-strip">
                        <ul class="jobsearch-strip-info">
                            <?php
                            if ($top_location != '') {
                                ?>
                                <li><i class="careerfy-icon careerfy-pin-line"></i> <?php echo($top_location) ?></li>
                                <?php
                            }
                            if ($top_phone != '') {
                                ?>
                                <li>
                                    <i class="careerfy-icon careerfy-technology"></i> <a
                                            href="tel:<?php echo($top_phone) ?>"><?php printf(esc_html__('%s', 'careerfy'), $top_phone) ?></a>
                                </li>
                                <?php
                            }
                            if ($top_email != '') {
                                ?>
                                <li><i class="careerfy-icon careerfy-envelope"></i> <a
                                            href="mailto:<?php echo($top_email) ?>"><?php echo($top_email) ?></a></li>
                            <?php } ?>
                        </ul>
                        <?php
                        if ($top_social == 'on') {
                            do_action('careerfy_social_icons', 'jobsearch-strip-media', '');
                        }
                        ?>
                    </div>
                    <?php
                    $topstrip_html = ob_get_clean();
                    echo apply_filters('careerfy_header_style_3_html', $topstrip_html);
                }
            }
            ?>
            <div class="container">
                <div class="row">
                    <div class="header-tabel">
                        <div class="header-row">
                            <div class="careerfy-logo-con">
                                <?php
                                careerfy_site_logo();
                                ?>
                            </div>
                            <div class="careerfy-menu-con">

                                <div class="careerfy-right">
                                    <div class="navigation-subthree"><?php do_action('careerfy_header_navigation') ?></div>
                                    <?php
                                    if (class_exists('Careerfy_framework')) {
                                        ?>
                                        <ul class="careerfy-user-log">
                                            <?php
                                            $args = array(
                                                'is_popup' => true,
                                            );
                                            do_action('jobsearch_user_account_links', $args);
                                            ?>
                                        </ul>
                                        <?php
                                        do_action('careerfy_header_top_after_btns');
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else if ($header_style == 'style2') { ?>
            <div class="header-tabel">
                <div class="header-row">
                    <div class="careerfy-logo-con">
                        <?php
                        careerfy_site_logo();
                        ?>
                    </div>
                    <div class="careerfy-menu-con">
                        <div class="careerfy-right">
                            <?php do_action('careerfy_header_navigation') ?>
                            <?php
                            if (class_exists('Careerfy_framework')) {
                                ?>
                                <ul class="careerfy-user-option">
                                    <?php
                                    $btn_page_obj = '';
                                    if ($header_btn_page != '') {
                                        $btn_page_obj = get_page_by_path($header_btn_page, 'OBJECT', 'page');
                                    }
                                    if (is_object($btn_page_obj) && isset($btn_page_obj->ID)) {
                                        ob_start();
                                        ?>
                                        <li>
                                            <a href="<?php echo apply_filters('careerfy_header_button_url', get_permalink($btn_page_obj->ID), $btn_page_obj->ID) ?>"
                                               class="careerfy-post-btn"><i
                                                        class="careerfy-icon careerfy-upload-arrow"></i> <?php echo apply_filters('careerfy_header_button_text', get_the_title($btn_page_obj->ID), $btn_page_obj->ID) ?>
                                            </a></li>
                                        <?php
                                        $btn_html = ob_get_clean();
                                        echo apply_filters('careerfy_header_button_html', $btn_html);
                                    } else {
                                        if ($header_btn_txt != '' && $header_btn_url != '') {
                                            ?>
                                            <li><a href="<?php echo($header_btn_url) ?>"
                                                   class="careerfy-post-btn"><i
                                                            class="careerfy-icon careerfy-upload-arrow"></i> <?php echo($header_btn_txt) ?>
                                                </a></li>
                                            <?php
                                        }
                                    }
                                    $args = array(
                                        'is_popup' => true,
                                    );
                                    do_action('jobsearch_user_account_links', $args);
                                    ?>
                                </ul>
                                <?php
                                do_action('careerfy_header_top_after_btns');
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="container">
                <div class="row">
                    <div class="header-tabel">
                        <div class="header-row">
                            <?php
                            ob_start();
                            ?>
                            <div class="careerfy-logo-con">
                                <?php careerfy_site_logo() ?>
                                <?php
                                ob_start();
                                ?>
                                <div class="navigation-sub">
                                    <?php do_action('careerfy_header_navigation') ?>
                                </div>
                                <?php
                                $navmenu_html = ob_get_clean();
                                echo apply_filters('careerfy_hder1_nav_menuplace_html', $navmenu_html);
                                ?>
                            </div>
                            <?php

                            if (class_exists('Careerfy_framework')) {
                                ?>
                                <div class="careerfy-btns-con">
                                    <div class="careerfy-right">
                                        <ul class="careerfy-user-section">
                                            <?php
                                            $args = array(
                                                'is_popup' => true,
                                            );
                                            do_action('jobsearch_user_account_links', $args);
                                            ?>
                                        </ul>
                                        <?php
                                        $btn_page_obj = '';
                                        if ($header_btn_page != '') {
                                            $btn_page_obj = get_page_by_path($header_btn_page, 'OBJECT', 'page');
                                        }
                                        if (is_object($btn_page_obj) && isset($btn_page_obj->ID)) {
                                            ob_start();
                                            ?>
                                            <a href="<?php echo apply_filters('careerfy_header_button_url', get_permalink($btn_page_obj->ID), $btn_page_obj->ID) ?>"
                                               class="careerfy-simple-btn careerfy-bgcolor"><span> <i
                                                            class="careerfy-icon careerfy-upload-arrow"></i> <?php echo apply_filters('careerfy_header_button_text', get_the_title($btn_page_obj->ID), $btn_page_obj->ID) ?></span></a>
                                            <?php
                                            $btn_html = ob_get_clean();
                                            echo apply_filters('careerfy_header_button_html', $btn_html);
                                        } else {
                                            if ($header_btn_txt != '' && $header_btn_url != '') {
                                                ?>
                                                <a href="<?php echo($header_btn_url) ?>"
                                                   class="careerfy-simple-btn careerfy-bgcolor"><span> <i
                                                                class="careerfy-icon careerfy-upload-arrow"></i> <?php echo($header_btn_txt) ?></span></a>
                                                <?php
                                            }
                                        }
                                        do_action('careerfy_header_top_after_btns');
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                            $hder_1_html = ob_get_clean();
                            echo apply_filters('careerfy_hder1_all_over_html', $hder_1_html, $header_btn_page);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        if (!(class_exists('Careerfy_MMC') && true == Careerfy_MMC::is_construction_mode_enabled(false))) {

        }
    }

    add_action('careerfy_header_section', 'careerfy_header_section', 10);
}

if (!function_exists('careerfy_nav_fallback')) {

    /**
     * Site Navigation fallback.
     * @return markup
     */
    function careerfy_nav_fallback()
    {
        $pages = wp_list_pages(array('title_li' => '', 'echo' => false));

        echo '
        <ul class="level-1 nav navbar-nav">
            ' . $pages . '
        </ul>';
    }

}

if (!function_exists('careerfy_header8_navigation')) {

    /**
     * Site Navigation.
     * @return markup
     */
    function careerfy_header8_navigation()
    {
        global $careerfy_framework_options;
        $header_btn_page = isset($careerfy_framework_options['header-button-page']) ? $careerfy_framework_options['header-button-page'] : '';
        ?>
        <!-- Navigation -->
        <a href="#menu" class="menu-link active"><span></span></a>
        <nav id="menu" class="careerfy-navigation navbar navbar-default menu">
            <?php
            $args = array(
                'theme_location' => 'primary',
                'menu_class' => 'level-1 nav navbar-nav',
                'container_class' => '',
                'container_id' => '',
                'container' => '',
                'fallback_cb' => 'careerfy_nav_fallback',
            );

            if (class_exists('careerfy_mega_menu_walker')) {
                $args['walker'] = new careerfy_mega_menu_walker();
            }
            wp_nav_menu($args);
            ?>
        </nav>
        <!-- Navigation -->
        <?php
    }

    add_action('careerfy_header8_navigation', 'careerfy_header8_navigation', 10);
}

if (!function_exists('careerfy_header_navigation')) {

    /**
     * Site Navigation.
     * @return markup
     */
    function careerfy_header_navigation()
    {
        ?>
        <!-- Navigation -->
        <a href="#menu" class="menu-link active"><span></span></a>
        <nav id="menu" class="careerfy-navigation navbar navbar-default menu">
            <?php
            $args = array(
                'theme_location' => 'primary',
                'menu_class' => 'level-1 nav navbar-nav',
                'container_class' => '',
                'container_id' => '',
                'container' => '',
                'fallback_cb' => 'careerfy_nav_fallback',
            );
            if (class_exists('careerfy_mega_menu_walker')) {
                $args['walker'] = new careerfy_mega_menu_walker();
            }
            wp_nav_menu($args);
            ?>
        </nav>
        <!-- Navigation -->
        <?php
    }

    add_action('careerfy_header_navigation', 'careerfy_header_navigation', 10);
}

if (!function_exists('careerfy_mobile_navigation')) {

    /**
     * Mobile Navigation.
     * @return markup
     */
    function careerfy_mobile_navigation()
    {
        ?>
        <div class="careerfy-sidebar-navigation careerfy-inmobile-itemsgen" style="display: none;">
            <?php
            $args = array(
                'theme_location' => 'primary',
                'menu_id' => 'careerfy-mobile-navbar-nav',
                'menu_class' => 'careerfy-mobile-navbar',
                'container_class' => '',
                'container_id' => '',
                'container' => '',
                'fallback_cb' => 'careerfy_nav_fallback',
            );
            $args['walker'] = new careerfy_mobile_menu_walker();
            wp_nav_menu($args);
            ?>
        </div>
        <?php
    }

    add_action('careerfy_mobile_navigation', 'careerfy_mobile_navigation', 10);
}

add_filter('careerfy_header_after_html', 'mobile_hder_strip', 35);

function mobile_hder_strip()
{
    global $careerfy_framework_options;
    $header_btn_page = isset($careerfy_framework_options['header-button-page']) ? $careerfy_framework_options['header-button-page'] : '';

    $mobile_header_style = isset($careerfy_framework_options['mobile_header_style']) ? $careerfy_framework_options['mobile_header_style'] : '';
    $mobile_hder_class = 'mobile-hder-style1';
    if ($mobile_header_style == 'style2') {
        $mobile_hder_class = 'mobile-hder-style2';
    } else if ($mobile_header_style == 'style3') {
        $mobile_hder_class = 'mobile-hder-style3';
    }

    //
    $menu_btn_display = true;
    if (has_nav_menu('primary')) {
        $theme_locations = get_nav_menu_locations();
        $theme_location_id = isset($theme_locations['primary']) ? $theme_locations['primary'] : '';
        $primary_menu_obj = get_term($theme_location_id, 'nav_menu');
        if (isset($primary_menu_obj->term_id)) {
            $menu_id = $primary_menu_obj->term_id;
            $menu_items = wp_get_nav_menu_items($menu_id);
            if (empty($menu_items)) {
                $menu_btn_display = false;
            }
        } else {
            $menu_btn_display = false;
        }
    }
    ?>
    <div class="careerfy-mobilehder-strip <?php echo($mobile_hder_class) ?>" style="display: none;">
        <?php
        if ($mobile_header_style == 'style3') {
            ?>
            <div class="mobile-hder-topcon">
                <div class="mobile-logocon">
                    <?php
                    if ($menu_btn_display) {
                        ?>
                        <a id="careerfy-mobile-navbtn" href="javascript:void(0);" class="mobile-navigation-togglebtn"><i
                                    class="fa fa-bars"></i></a>
                        <?php
                    }
                    careerfy_responsive_logo();
                    ?>
                </div>
                <div class="mobile-right-btnscon">
                    <?php echo apply_filters('careerfy_mobile_hderstrip_btns_bfore_html', ''); ?>
                    <?php echo apply_filters('careerfy_mobile_hderstrip_btns_after_html', ''); ?>
                </div>
            </div>
            <?php
        } else if ($mobile_header_style == 'style2') {
            ?>
            <div class="mobile-hder-topcon">
                <div class="mobile-right-btnscon">
                    <?php
                    if ($menu_btn_display) {
                        ?>
                        <a id="careerfy-mobile-navbtn" href="javascript:void(0);" class="mobile-navigation-togglebtn"><i
                                    class="fa fa-bars"></i></a>
                        <?php
                    }
                    echo apply_filters('careerfy_mobile_hderstrip_btns_bfore_html', ''); ?>
                    <?php echo apply_filters('careerfy_mobile_hderstrip_btns_after_html', ''); ?>
                </div>
                <div class="mobile-logocon">
                    <?php careerfy_responsive_logo() ?>
                </div>
            </div>
            <?php
        } else {
            ?>
            <div class="mobile-hder-topcon">
                <div class="mobile-logocon">
                    <?php careerfy_responsive_logo() ?>
                </div>
                <div class="mobile-right-btnscon">
                    <?php
                    if ($menu_btn_display) {
                        ?>
                        <a id="careerfy-mobile-navbtn" href="javascript:void(0);" class="mobile-navigation-togglebtn"><i
                                    class="fa fa-bars"></i></a>
                        <?php
                    }
                    echo apply_filters('careerfy_mobile_hderstrip_btns_bfore_html', ''); ?>
                    <?php echo apply_filters('careerfy_mobile_hderstrip_btns_after_html', ''); ?>
                </div>
            </div>
            <?php
        }
        $btn_page_obj = '';
        if ($header_btn_page != '') {
            $btn_page_obj = get_page_by_path($header_btn_page, 'OBJECT', 'page');
        }
        if (is_object($btn_page_obj) && isset($btn_page_obj->ID)) {
            ob_start();
            ?>
            <a href="<?php echo apply_filters('careerfy_header_button_url', get_permalink($btn_page_obj->ID), $btn_page_obj->ID) ?>"
               class="mobile-hdr-custombtn"><i
                        class="careerfy-icon careerfy-upload-arrow"></i> <?php echo apply_filters('careerfy_header_button_text', get_the_title($btn_page_obj->ID), $btn_page_obj->ID) ?>
            </a>
            <?php
            $btn_html = ob_get_clean();
            echo apply_filters('careerfy_header_button_html', $btn_html);
        }
        ?>
    </div>
    <?php
}

add_filter('careerfy_theme_after_body_tag_html', 'careerfy_mobile_sidebar_html');

function careerfy_mobile_sidebar_html()
{
    global $careerfy_framework_options;
    $mobile_header_style = isset($careerfy_framework_options['mobile_header_style']) ? $careerfy_framework_options['mobile_header_style'] : '';
    $mobile_menu_class = 'mobile-menu-style1';
    if ($mobile_header_style == 'style2') {
        $mobile_menu_class = 'mobile-menu-style2';
    } else if ($mobile_header_style == 'style3') {
        $mobile_menu_class = 'mobile-menu-style3';
    }
    ?>
    <div class="careerfy-mobile-hdr-sidebar <?php echo($mobile_menu_class) ?>">
        <?php do_action('careerfy_mobile_navigation'); ?>
        <?php do_action('careerfy_mobile_navigation_after'); ?>
        <a href="javascript:void(0);" class="mobile-navclose-btn"><i class="fa fa-times"></i></a>
    </div>
    <?php
}

if (!function_exists('careerfy_header_class')) {

    /**
     * Header Class.
     * @return class
     */
    function careerfy_header_class()
    {
        global $post, $careerfy_framework_options;

        $header_style = isset($careerfy_framework_options['header-style']) ? $careerfy_framework_options['header-style'] : '';
        $header_classes = array();

        if ($header_style == 'style2') {
            $header_classes[] = 'careerfy-header-two common-header-transparent';
        } else if ($header_style == 'style3') {
            $header_classes[] = 'careerfy-header-three';
        } else if ($header_style == 'style4') {
            $header_classes[] = 'careerfy-header-four common-header-transparent';
        } else if ($header_style == 'style5') {
            $header_classes[] = 'careerfy-header-six';
        } else if ($header_style == 'style6') {
            $header_classes[] = 'careerfy-header-seven';
        } else if ($header_style == 'style7') {
            $header_classes[] = 'careerfy-header-eight common-header-transparent';
        } else if ($header_style == 'style8') {
            $header_classes[] = 'jobsearch-header-eight';
        } else if ($header_style == 'style9') {
            $header_classes[] = 'careerfy-header-nine';
        } else if ($header_style == 'style10') {
            $header_classes[] = 'careerfy-header-ten';
        } else if ($header_style == 'style11') {
            $header_classes[] = 'careerfy-header-eleven';
        } else if ($header_style == 'style12') {
            $header_classes[] = 'careerfy-header-twelve';
        } else if ($header_style == 'style13') {
            $header_classes[] = 'careerfy-header-thirteen';
        } else if ($header_style == 'style14') {
            $header_classes[] = 'careerfy-header-fourteen';
        } else if ($header_style == 'style15') {
            $header_classes[] = 'careerfy-header-fifteen';
        } else if ($header_style == 'style16') {
            $header_classes[] = 'careerfy-header-sixteen';
        } else if ($header_style == 'style17') {
            $header_classes[] = 'careerfy-header-seventeen';
        } else if ($header_style == 'style18') {
            $header_classes[] = 'careerfy-header-eighteen';
        } else if ($header_style == 'style19') {
            $header_classes[] = 'careerfy-header-nineteen';
        } else if ($header_style == 'style20') {
            $header_classes[] = 'careerfy-header-twenty';
        } else if ($header_style == 'style21') {
            $header_classes[] = 'careerfy-header-twentyone';
        } else if ($header_style == 'style22') {
            $header_classes[] = 'careerfy-header-twentytwo';
        } else {
            $header_classes[] = 'careerfy-header-one';
        }

        $header_class = implode(' ', $header_classes);

        return $header_class;
    }

}

if (!function_exists('careerfy_top_menu_login_links')) {

    /**
     * Header Login Links.
     * @return html
     */
    function careerfy_top_menu_login_links($links, $register_link_view = true)
    {
        global $careerfy_framework_options;
        $header_style = isset($careerfy_framework_options['header-style']) ? $careerfy_framework_options['header-style'] : '';
        if ($header_style == 'style2') {
            ob_start();
            ?>
            <li><a href="javascript:void(0);" class="careerfy-btn-icon jobsearch-open-signin-tab"><i
                            class="careerfy-icon careerfy-user"></i></a></li>
            <?php
            $links = ob_get_clean();
        } else if ($header_style == 'style3') {
            ob_start();
            ?>
            <li><a href="javascript:void(0);" class="jobsearch-open-signin-tab"><i
                            class="careerfy-icon careerfy-login"></i> <?php esc_html_e('Login', 'careerfy') ?></a>
            </li>
            <?php
            if ($register_link_view === true) {
                ?>
                <li><a href="javascript:void(0);" class="active jobsearch-open-register-tab"><i
                                class="careerfy-icon careerfy-user-plus"></i> <?php esc_html_e('Register', 'careerfy') ?>
                    </a></li>
                <?php
            }
            $links = ob_get_clean();
        } else if ($header_style == 'style4') {
            ob_start();
            ?>
            <li class="active jobsearch-open-signin-tab"><a href="javascript:void(0);"><i
                            class="careerfy-icon careerfy-user"></i> <?php esc_html_e('Sign In', 'careerfy') ?></a>
            </li>
            <?php
            $links = ob_get_clean();
        } else if ($header_style == 'style5') {
            if (is_user_logged_in()) {
                return $links;
            }
            ob_start();
            ?>
            <li class="careerfy-color"><a href="javascript:void(0);" class="jobsearch-open-signin-tab"><i
                            class="careerfy-icon careerfy-logout"></i> <?php echo esc_html__('Sign In', 'careerfy'); ?>
                </a></li>
            <?php
            if ($register_link_view === true) {
                ?>
                <li class="careerfy-color active"><a href="javascript:void(0);" class="jobsearch-open-register-tab"><i
                                class="careerfy-icon careerfy-user-plus"></i> <?php echo esc_html__('Sign Up', 'careerfy'); ?>
                    </a></li>
                <?php
            }
            $links = ob_get_clean();
        } else if ($header_style == 'style6') {
            if (is_user_logged_in()) {
                return $links;
            }
            ob_start();
            if ($register_link_view === true) {
                ?>
                <li class="active">
                    <a href="javascript:void(0);"
                       class="jobsearch-open-register-tab"> <?php echo esc_html__('Register / Login', 'careerfy'); ?></a>
                </li>
                <?php
            }
            $links = ob_get_clean();
        } else if ($header_style == 'style7') {
            if (is_user_logged_in()) {
                return $links;
            }
            ob_start();
            if ($register_link_view === true) {
                ?>
                <li class="active"><a href="javascript:void(0);"
                                      class="jobsearch-open-register-tab"> <?php echo esc_html__('Post New Job', 'careerfy'); ?></a>
                </li>
                <?php
            }
            $links = ob_get_clean();
        } else if ($header_style == 'style8') {
            if (is_user_logged_in()) {
                return $links;
            }
            ob_start();
            if ($register_link_view === true) {
                ?>
                <li><a href="javascript:void(0);" class="jobsearch-open-signin-tab"><i
                                class="careerfy-icon careerfy-logout"></i> <?php esc_html_e('Sign In', 'careerfy') ?>
                    </a></li>
                <?php
                if ($register_link_view === true) {
                    ?>
                    <li><a href="javascript:void(0);" class="jobsearch-open-register-tab"><i
                                    class="careerfy-icon careerfy-user-plus"></i> <?php esc_html_e('Register', 'careerfy') ?>
                        </a></li>
                    <?php
                }
            }
            $links = ob_get_clean();
        } else if ($header_style == 'style9') {
            if (is_user_logged_in()) {
                return $links;
            }
            ob_start();
            if ($register_link_view === true) {
                ?>
                <li><a href="javascript:void(0)" class="careerfy-open-signup-tab jobsearch-open-signin-tab"><i
                                class="careerfy-icon careerfy-user-plus"></i><?php echo esc_html__('Login', 'careerfy'); ?>
                    </a></li>
                <li><a href="javascript:void(0)" class="careerfy-open-signup-tab jobsearch-open-register-tab"><i
                                class="careerfy-icon careerfy-multimedia"></i><?php echo esc_html__('Signup', 'careerfy'); ?>
                    </a></li>
                <?php
            }
            $links = ob_get_clean();
        } else if ($header_style == 'style10') {
            if (is_user_logged_in()) {
                return $links;
            }
            ob_start();
            if ($register_link_view === true) {
                ?>
                <li><a href="javascript:void(0)" class="careerfy-open-signup-tab jobsearch-open-signin-tab"><i
                                class="careerfy-icon careerfy-login"></i><?php echo esc_html__('Login', 'careerfy'); ?>
                    </a></li>
                <li><a href="javascript:void(0)" class="careerfy-open-signup-tab jobsearch-open-register-tab"><i
                                class="careerfy-icon careerfy-password"></i><?php echo esc_html__('Signup', 'careerfy'); ?>
                    </a></li>
                <?php
            }
            $links = ob_get_clean();
        } else if ($header_style == 'style11') {
            if (is_user_logged_in()) {
                return $links;
            }
            ob_start();
            if ($register_link_view === true) {
                ?>
                <li><a href="javascript:void(0)"
                       class="careerfy-open-signup-tab jobsearch-open-signin-tab"><?php echo esc_html__('Login', 'careerfy'); ?>
                        <i class="careerfy-icon careerfy-next-2"></i></a></li>
                <li><a href="javascript:void(0)"
                       class="careerfy-open-signup-tab jobsearch-open-register-tab"><?php echo esc_html__('Signup', 'careerfy'); ?>
                        <i class="careerfy-icon careerfy-user"></i></a></li>
                <?php
            }
            $links = ob_get_clean();
        }
        return $links;
    }

    add_filter('jobsearch_top_login_links', 'careerfy_top_menu_login_links', 10, 2);
}

if (!function_exists('header_advance_search')) {

    function header_advance_search()
    {
        global $careerfy_framework_options;
        $header_style = isset($careerfy_framework_options['header-style']) ? $careerfy_framework_options['header-style'] : '';
        $header_bg_img = isset($careerfy_framework_options['careerfy-header-bg-img']) ? $careerfy_framework_options['careerfy-header-bg-img'] : '';
        $header_overlay_color = isset($careerfy_framework_options['careerfy-header-overlay-color']) ? $careerfy_framework_options['careerfy-header-overlay-color'] : '';
        $header_adv_shortcode = isset($careerfy_framework_options['careerfy-adv-search-shortcode']) ? $careerfy_framework_options['careerfy-adv-search-shortcode'] : '';
        $top_header = isset($careerfy_framework_options['careerfy-top-header']) ? $careerfy_framework_options['careerfy-top-header'] : '';
        $header_advance_search = isset($careerfy_framework_options['careerfy-header-advance-search']) ? $careerfy_framework_options['careerfy-header-advance-search'] : '';
        $header_slider = isset($careerfy_framework_options['careerfy-header-slider']) ? $careerfy_framework_options['careerfy-header-slider'] : '';
        $header_slider_list = isset($careerfy_framework_options['careerfy-header-slider-list']) ? $careerfy_framework_options['careerfy-header-slider-list'] : '';

        if (($header_bg_img != "" || $header_overlay_color != "") && $header_style == 'style12' && $header_advance_search == 'on') {
            ?>
            <style>
                .careerfy-banner-twelve {
                    background: url('<?php echo $header_bg_img['url'] ?>');
                }

                .careerfy-banner-twelve-transparent {
                    background-color: <?php echo $header_overlay_color['rgba'] ?>
                }
            </style>
            <?php
        }
        if ($header_style == 'style12' && $top_header == 'on' && $header_slider == 'off') {
            ?>
            <div class="careerfy-banner-twelve">
                <span class="careerfy-banner-twelve-transparent"></span>
                <?php
                if ($header_advance_search == 'on') {
                    echo do_shortcode($header_adv_shortcode);
                    ?>
                <?php } ?>
            </div>
            <?php
        }
        if ($header_style == 'style12' && $top_header == 'on' && $header_slider == 'on') {
            ?>
            <?php echo do_shortcode('[rev_slider alias="' . $header_slider_list . '"]') ?>
            <?php
        }
    }

    add_action('header_advance_search', 'header_advance_search', 10);
}

if (!function_exists('header_navigation_style_twelve')) {

    function header_navigation_style_twelve()
    {
        global $careerfy_framework_options;
        $header_style = isset($careerfy_framework_options['header-style']) ? $careerfy_framework_options['header-style'] : '';
        $top_header = isset($careerfy_framework_options['careerfy-top-header']) ? $careerfy_framework_options['careerfy-top-header'] : '';

        if ($header_style != "style12") {
            return;
        }
        $theme_locations = get_nav_menu_locations();

        if ($theme_locations != "" || count($theme_locations['primary']) > 0) {
            ?>
            <div class="careerfy-twelve-navigation">
                <nav class="jobsearch-navigation">
                    <div class="navbar-collapse" id="jobsearch-navbar-collapse-1">
                        <ul class="navbar-nav">
                            <?php do_action('careerfy_header_navigation') ?>
                        </ul>
                    </div>
                </nav>
            </div>

            <?php
        }
    }

    add_action('header_navigation_style_twelve', 'header_navigation_style_twelve', 10);
}

if (!function_exists('careerfy_header_twelve_top_location')) {

    function careerfy_header_twelve_top_location()
    {
        global $careerfy_framework_options;
        $header_style = isset($careerfy_framework_options['header-style']) ? $careerfy_framework_options['header-style'] : '';
        if ($header_style != 'style12') {
            return;
        }
        register_nav_menus(
            array(
                'header-twelve-top-menu' => __('Header Twelve Top Menu', 'careerfy'),
            )
        );
    }

    add_action('init', 'careerfy_header_twelve_top_location');
}

if (!function_exists('careerfy_header_twelve_top_navigation')) {

    /**
     * Header 12 Top Menu Navigation.
     * @return markup
     */
    function careerfy_header_twelve_top_navigation()
    {

        $args = array(
            'theme_location' => 'header-twelve-top-menu',
            'menu_class' => 'careerfy-headertwelve-user',
            'container_class' => '',
            'container_id' => '',
            'container' => '',
            'fallback_cb' => 'careerfy_nav_fallback',
        );
        if (class_exists('careerfy_mega_menu_walker')) {
            $args['walker'] = new careerfy_mega_menu_walker();
        }
        wp_nav_menu($args);
    }

    add_action('careerfy_header_twelve_top_navigation', 'careerfy_header_twelve_top_navigation', 10);
}

