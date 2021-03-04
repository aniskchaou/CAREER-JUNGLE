<?php
/**
 * Careerfy Footer functions.
 *
 * @package Careerfy
 */
if (!function_exists('careerfy_footer_bottom_sec')) {

    /**
     * Footer copyright section.
     * @return markup
     */
    function careerfy_footer_bottom_sec()
    {
        global $careerfy_framework_options, $sitepress;
        $lang_code = '';
        if (function_exists('icl_object_id') && is_object($sitepress)) {
            $lang_code = $sitepress->get_current_language();
        }
        $footer_style = isset($careerfy_framework_options['footer-style']) ? $careerfy_framework_options['footer-style'] : '';
        $footer_social = isset($careerfy_framework_options['careerfy-footer-social']) ? $careerfy_framework_options['careerfy-footer-social'] : '';
        $footer_logo = isset($careerfy_framework_options['careerfy-footer-logo']['url']) ? $careerfy_framework_options['careerfy-footer-logo']['url'] : '';
        $ios_app_link = isset($careerfy_framework_options['footer-ios-app-link']) ? $careerfy_framework_options['footer-ios-app-link'] : '';
        $android_app_link = isset($careerfy_framework_options['footer-android-app-link']) ? $careerfy_framework_options['footer-android-app-link'] : '';
        $appstore_logo = isset($careerfy_framework_options['andriod-app-logo']['url']) ? $careerfy_framework_options['andriod-app-logo']['url'] : '';
        $googlestore_logo = isset($careerfy_framework_options['google-app-logo']['url']) ? $careerfy_framework_options['google-app-logo']['url'] : '';
        $footer_bg_image = isset($careerfy_framework_options['footer-background']['url']) ? $careerfy_framework_options['footer-background']['url'] : '';

        $allowed_tags = array(
            'a' => array(
                'href' => array(),
                'title' => array(),
                'class' => array()
            ),
            'br' => array(),
            'em' => array(),
            'strong' => array(),
        );
        $copyright_text = isset($careerfy_framework_options['careerfy-footer-copyright-text']) ? $careerfy_framework_options['careerfy-footer-copyright-text'] : '';
        if ($copyright_text != '') {
            $copyright_text = apply_filters('wpml_translate_single_string', $copyright_text, 'Careerfy Options', 'Copyright Text - ' . $copyright_text, $lang_code);
        }
        if ($copyright_text == '' && $footer_style != 'style4') {
            $copyright_text = get_bloginfo('name') . ' &copy; ' . date('Y') . ', ' . esc_html__('All Right Reserved - by', 'careerfy') . ' <a href="#" class="careerfy-color">' . esc_html__('Eyecix', 'careerfy') . '</a>';
        }
        ?>
        <!-- CopyRight Section -->
        <?php
        $copyright_class = 'careerfy-copyright';
        if ($footer_style == 'style2') {
            $copyright_class = 'careerfy-copyright-two';
        } else if ($footer_style == 'style3') {
            $copyright_class = 'copyright-three';
        } else if ($footer_style == 'style4') {
            $copyright_class = 'copyright-four';
        } else if ($footer_style == 'style5') {
            $copyright_class = 'copyright-five';
        } else if ($footer_style == 'style6') {
            $copyright_class = 'copyright-six';
        } else if ($footer_style == 'style7') {
            $copyright_class = 'copyright-nine';
        } else if ($footer_style == 'style8') {
            $copyright_class = 'jobsearch-copyright';
        } else if ($footer_style == 'style9') {
            $copyright_class = 'copyright-ten';
        } else if ($footer_style == 'style10') {
            $copyright_class = 'copyright-eleven';
        } else if ($footer_style == 'style11') {
            $copyright_class = 'copyright-twelve';
        } else if ($footer_style == 'style12') {
            $copyright_class = 'copyright-thirteen';
        } else if ($footer_style == 'style13') {
            $copyright_class = 'copyright-fourteen';
        } else if ($footer_style == 'style14') {
            $copyright_class = 'copyright-fifteen';
        } else if ($footer_style == 'style15') {
            $copyright_class = 'copyright-sixteen';
        } else if ($footer_style == 'style16') {
            $copyright_class = 'copyright-eighteen';
        } else if ($footer_style == 'style17') {
            $copyright_class = 'copyright-nineteen';
        } else if ($footer_style == 'style18') {
            $copyright_class = 'copyright-twenty';
        } else if ($footer_style == 'style19') {
            $copyright_class = 'copyright-twentyone';
        }

        if (!(class_exists('Careerfy_MMC') && true == Careerfy_MMC::is_construction_mode_enabled(false))) { ?>
            <div class="<?php echo($copyright_class) ?>">
                <?php
                if ($footer_style == 'style4') {
                    $footer_logo = isset($careerfy_framework_options['footer-logo']['url']) && $careerfy_framework_options['footer-logo']['url'] != '' ? $careerfy_framework_options['footer-logo']['url'] : '';

                    if ($footer_logo != '') { ?>
                        <a href="<?php echo esc_url(home_url('/')) ?>" class="copyright-logo"><img
                                    src="<?php echo($footer_logo) ?>" alt=""></a>
                    <?php } ?>
                    <p><?php echo wp_kses($copyright_text, $allowed_tags) ?></p>
                    <?php
                } else if ($footer_style == 'style3') {
                    $footer_logo = isset($careerfy_framework_options['footer-logo']['url']) && $careerfy_framework_options['footer-logo']['url'] != '' ? $careerfy_framework_options['footer-logo']['url'] : '';

                    if ($footer_logo != '') { ?>
                        <a href="<?php echo esc_url(home_url('/')) ?>" class="copyright-logo"><img
                                    src="<?php echo($footer_logo) ?>" alt=""></a>
                    <?php } ?>
                    <p><?php echo wp_kses($copyright_text, $allowed_tags) ?></p>
                <?php } else if ($footer_style == 'style2') {
                    if ($footer_social == 'on') {
                        do_action('careerfy_social_icons', 'careerfy-copyright-social', 'view-2');
                    } ?>
                    <p><?php echo wp_kses($copyright_text, $allowed_tags) ?></p>
                    <?php
                    if ($ios_app_link != '' && $android_app_link != '') { ?>
                        <ul class="careerfy-copyright-download">
                            <li><?php esc_html_e('Download Apps', 'careerfy') ?></li>
                            <li><a href="<?php echo($ios_app_link) ?>" class="careerfy-icon careerfy-apple"></a></li>
                            <li><a href="<?php echo($android_app_link) ?>"
                                   class="careerfy-icon careerfy-android-logo"></a></li>
                        </ul>
                    <?php }
                } else if ($footer_style == 'style5') { ?>
                    <div class="container">
                        <div class="row">
                            <p><?php echo wp_kses($copyright_text, $allowed_tags) ?></p>
                            <?php
                            if ($appstore_logo != '' || $googlestore_logo != '' || $footer_social == 'on') {
                                ?>
                                <div class="copyright-wrap">
                                    <?php
                                    if ($appstore_logo != '') { ?>
                                        <a href="<?php echo($ios_app_link) ?>"><img src="<?php echo($appstore_logo); ?>"
                                                                                    alt="<?php echo esc_html__("IOS", "careerfy"); ?>"></a>
                                        <?php
                                    }
                                    if ($googlestore_logo != '') { ?>
                                        <a href="<?php echo($android_app_link) ?>"><img
                                                    src="<?php echo($googlestore_logo); ?>"
                                                    alt="<?php echo esc_html__("Google", "careerfy"); ?>"></a>
                                        <?php
                                    }
                                    if ($footer_social == 'on') {
                                        do_action('careerfy_social_icons', '', '');
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php } else if ($footer_style == 'style6') { ?>
                    <div class="careerfy-copyright-wrap">
                        <div class="container">
                            <div class="row">
                                <?php
                                if (has_nav_menu('footer')) {
                                    $args = array(
                                        'theme_location' => 'footer',
                                        'menu_class' => 'footer-menu',
                                        'container_class' => 'footer-container',
                                    );
                                    wp_nav_menu($args);
                                }
                                ?>
                                <p><?php echo wp_kses($copyright_text, $allowed_tags) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php
                } else if ($footer_style == 'style7') { ?>
                    <div class="container">
                        <div class="row">
                            <p><?php echo wp_kses($copyright_text, $allowed_tags) ?></p>
                        </div>
                    </div>
                    <a href="#" class="careerfy-backto-top"><i class="careerfy-icon careerfy-up-arrow"></i></a>
                <?php } else if ($footer_style == 'style9') {
                    if ($footer_social == 'on' && $footer_style == 'style9') {
                        do_action('careerfy_social_icons', 'copyright-ten-social', 'view-4');
                    }
                    ?>

                    <p><?php echo wp_kses($copyright_text, $allowed_tags) ?></p>

                <?php } else if ($footer_style == 'style10') { ?>
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <?php if ($footer_social == 'on' && $footer_style == 'style10') {
                                    do_action('careerfy_social_icons', 'copyright-eleven-social', 'view-5');
                                }
                                ?>
                                <p><?php echo wp_kses($copyright_text, $allowed_tags) ?></p>
                                <div class="copyright-eleven-app">
                                    <a href="<?php echo $android_app_link ?>"><img src="<?php echo $appstore_logo ?>"
                                                                                   alt=""></a>
                                    <a href="<?php echo $ios_app_link ?>"><img src="<?php echo $googlestore_logo ?>"
                                                                               alt=""></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else if ($footer_style == 'style11') { ?>
                    <?php if ($footer_bg_image != "") { ?>
                        <style>
                            .careerfy-footer-twelve {
                                background: url(<?php echo $footer_bg_image ?>);
                            }
                        </style>
                    <?php } ?>
                    <div class="container">
                        <div class="row">
                            <?php if ($footer_social == 'on') {
                                do_action('careerfy_social_icons', 'copyright-twelve-social', 'view-6');
                            } ?>

                            <p><?php echo wp_kses($copyright_text, $allowed_tags) ?></p>
                        </div>
                    </div>

                <?php } else if ($footer_style == 'style12') { ?>
                    <div class="container">
                        <div class="row">
                            <?php if ($footer_social == 'on') {
                                do_action('careerfy_social_icons', 'copyright-thirteen-social', 'view-4');
                            } ?>
                            <p><?php echo wp_kses($copyright_text, $allowed_tags) ?></p>
                        </div>
                    </div>
                <?php } else if ($footer_style == 'style13') { ?>

                    <?php if ($footer_social == 'on') {
                        do_action('careerfy_social_icons', 'copyright-fourteen-social', 'view-4');
                    } ?>

                    <p><?php echo wp_kses($copyright_text, $allowed_tags) ?></p>
                <?php } else if ($footer_style == 'style14') { ?>
                    <div class="container">
                        <div class="row">
                            <?php if ($footer_social == 'on') {
                                do_action('careerfy_social_icons', 'copyright-fifteen-social', 'view-4');
                            } ?>
                            <p><?php echo wp_kses($copyright_text, $allowed_tags) ?></p>
                            <div class="copyright-fifteen-app">
                                <a href="<?php echo $android_app_link ?>"><img src="<?php echo $appstore_logo ?>"
                                                                               alt=""></a>
                                <a href="<?php echo $ios_app_link ?>"><img src="<?php echo $googlestore_logo ?>" alt=""></a>
                            </div>

                        </div>
                    </div>
                <?php } else if ($footer_style == 'style16') { ?>

                    <p><?php echo wp_kses($copyright_text, $allowed_tags) ?></p>

                <?php } else if ($footer_style == 'style17') { ?>

                    <div class="container">

                        <?php if ($footer_social == 'on') {
                            do_action('careerfy_social_icons', 'copyright-nineteen-social', 'view-4');
                        } ?>
                        <p><?php echo wp_kses($copyright_text, $allowed_tags) ?></p>

                    </div>

                <?php } else if ($footer_style == 'style18') { ?>

                    <div class="container">
                        <p><?php echo wp_kses($copyright_text, $allowed_tags) ?></p>
                    </div>

                <?php } else if ($footer_style == 'style19') { ?>

                    <p><?php echo wp_kses($copyright_text, $allowed_tags) ?></p>

                <?php } else if ($footer_style == 'style15') { ?>
                    <div class="container">
                        <div class="row">
                            <div class="copyright-sixteen-download">
                                <span><?php echo esc_html__('Download the App', 'careerfy') ?></span>
                                <a href="<?php echo $ios_app_link ?>"><i class="careerfy-icon careerfy-apple"></i></a>
                                <a href="<?php echo $android_app_link ?>"><i
                                            class="careerfy-icon careerfy-android-logo"></i></a>
                            </div>
                            <p><?php echo wp_kses($copyright_text, $allowed_tags) ?></p>
                            <?php if ($footer_social == 'on') {
                                do_action('careerfy_social_icons', 'copyright-sixteen-social', 'view-4');
                            } ?>
                        </div>
                    </div>
                <?php } else { ?>

                    <p><?php echo wp_kses($copyright_text, $allowed_tags) ?></p>

                    <?php if ($footer_social == 'on' && $footer_style != 'style8') {
                        do_action('careerfy_social_icons', '', '');
                    }
                }
                ?>
            </div>
            <?php
        }
    }

    add_action('careerfy_footer_bottom_sec', 'careerfy_footer_bottom_sec', 10);
}

if (!function_exists('careerfy_footer_upper_sec')) {

    /**
     * Footer copyright section.
     * @return markup
     */
    function careerfy_footer_upper_sec()
    {
        global $careerfy_framework_options;

        do_action('careerfy_footer_sidebar_widgets');
    }

    add_action('careerfy_footer_upper_sec', 'careerfy_footer_upper_sec', 10);
}

if (!function_exists('careerfy_footer_sidebar_widgets')) {

    /**
     * Footer Sidebar Widgets section.
     * @return markup
     */
    function careerfy_footer_sidebar_widgets()
    {
        global $careerfy_framework_options;
        $careerfy_sidebars = isset($careerfy_framework_options['careerfy-footer-sidebars']) ? $careerfy_framework_options['careerfy-footer-sidebars'] : '';
        $careerfy_sidebars_switch = isset($careerfy_framework_options['careerfy-footer-sidebar-switch']) ? $careerfy_framework_options['careerfy-footer-sidebar-switch'] : '';
        $footer_style = isset($careerfy_framework_options['footer-style']) ? $careerfy_framework_options['footer-style'] : '';

        if ($careerfy_sidebars_switch == 'on' && isset($careerfy_sidebars['col_width']) && is_array($careerfy_sidebars['col_width']) && sizeof($careerfy_sidebars['col_width']) > 0) {

            $sidbar_con_class = 'careerfy-footer-widget';
            if ($footer_style == 'style8') {
                $sidbar_con_class = 'jobsearch-footer-widget';
            }
            ?>
            <div class="<?php echo($sidbar_con_class) ?>">
                <?php if ($footer_style == 'style5' || $footer_style == 'style17') { ?>
                <div class="container">
                    <?php } ?>
                    <div class="row">
                        <?php
                        $sidebar_counter = 0;
                        foreach ($careerfy_sidebars['col_width'] as $sidebar_col) {
                            $sidebar = isset($careerfy_sidebars['sidebar_name'][$sidebar_counter]) ? $careerfy_sidebars['sidebar_name'][$sidebar_counter] : '';
                            if ($sidebar != '') {
                                $sidebar_col_arr = explode('_', $sidebar_col);
                                $sidebar_col_class = isset($sidebar_col_arr[0]) && $sidebar_col_arr[0] != '' ? 'col-md-' . $sidebar_col_arr[0] : 'col-md-12';
                                $sidebar_id = sanitize_title($sidebar);
                                //if (is_active_sidebar($sidebar_id)) {
                                ?>
                                <div class="<?php echo($sidebar_col_class) ?>">
                                    <?php dynamic_sidebar($sidebar_id) ?>
                                </div>
                                <?php
                                //}
                            }
                            $sidebar_counter++;
                        }
                        ?>
                    </div>

                    <?php if ($footer_style == 'style5' || $footer_style == 'style17') { ?>
                </div>
            <?php }
            ?>
            </div>
            <?php
        }
    }

    add_action('careerfy_footer_sidebar_widgets', 'careerfy_footer_sidebar_widgets', 10);
}

if (!function_exists('careerfy_footer_class')) {

    /**
     * Footer Class.
     * @return class
     */
    function careerfy_footer_class()
    {
        global $careerfy_framework_options;

        $footer_style = isset($careerfy_framework_options['footer-style']) ? $careerfy_framework_options['footer-style'] : '';

        $footer_classes = array();

        if ($footer_style == 'style2') {
            $footer_classes[] = 'careerfy-footer-two';
        } else if ($footer_style == 'style3') {
            $footer_classes[] = 'careerfy-footer-three';
        } else if ($footer_style == 'style4') {
            $footer_classes[] = 'careerfy-footer-four';
        } else if ($footer_style == 'style5') {
            $footer_classes[] = 'careerfy-footer-five';
        } else if ($footer_style == 'style6') {
            $footer_classes[] = 'careerfy-footer-six';
        } else if ($footer_style == 'style7') {
            $footer_classes[] = 'careerfy-footer-nine';
        } else if ($footer_style == 'style8') {
            $footer_classes[] = 'jobsearch-footer-eight';
        } else if ($footer_style == 'style9') {
            $footer_classes[] = 'careerfy-footer-ten';
        } else if ($footer_style == 'style10') {
            $footer_classes[] = 'careerfy-footer-eleven';
        } else if ($footer_style == 'style11') {
            $footer_classes[] = 'careerfy-footer-twelve';
        } else if ($footer_style == 'style12') {
            $footer_classes[] = 'careerfy-footer-thirteen';
        } else if ($footer_style == 'style13') {
            $footer_classes[] = 'careerfy-footer-fourteen';
        } else if ($footer_style == 'style14') {
            $footer_classes[] = 'careerfy-footer-fifteen';
        } else if ($footer_style == 'style15') {
            $footer_classes[] = 'careerfy-footer-sixteen';
        } else if ($footer_style == 'style16') {
            $footer_classes[] = 'careerfy-footer-eighteen';
        } else if ($footer_style == 'style17') {
            $footer_classes[] = 'careerfy-footer-ninteen';
        } else if ($footer_style == 'style18') {
            $footer_classes[] = 'careerfy-footer-twenty';
        } else if ($footer_style == 'style19') {
            $footer_classes[] = 'careerfy-footer-twentyone';
        } else {
            $footer_classes[] = 'careerfy-footer-one';
        }

        $footer_class = implode(' ', $footer_classes);

        return $footer_class;
    }

}

if (!function_exists('careerfy_footer_top_secion_callback')) {

    function careerfy_footer_top_secion_callback()
    {
        global $careerfy_framework_options;

        $footer_style = isset($careerfy_framework_options['footer-style']) ? $careerfy_framework_options['footer-style'] : '';

        $footer_section_heading = isset($careerfy_framework_options['footer-section-heading']) ? $careerfy_framework_options['footer-section-heading'] : '';
        $footer_section_desc = isset($careerfy_framework_options['footer-section-desc']) ? $careerfy_framework_options['footer-section-desc'] : '';
        $footer_section_tbtn_text = isset($careerfy_framework_options['footer-section-btn-text']) ? $careerfy_framework_options['footer-section-btn-text'] : '';
        $footer_section_tbtn_link = isset($careerfy_framework_options['footer-section-btn-link']) ? $careerfy_framework_options['footer-section-btn-link'] : '';
        $footer_top_section_switch = isset($careerfy_framework_options['careerfy-footer-top-section-switch']) ? $careerfy_framework_options['careerfy-footer-top-section-switch'] : '';
        $footer_logo = isset($careerfy_framework_options['careerfy-footer-logo']['url']) ? $careerfy_framework_options['careerfy-footer-logo']['url'] : '';
        $footer_social = isset($careerfy_framework_options['careerfy-footer-social']) ? $careerfy_framework_options['careerfy-footer-social'] : '';
        $footer_newsletter = isset($careerfy_framework_options['careerfy-footer-top-newsletter']) ? $careerfy_framework_options['careerfy-footer-top-newsletter'] : '';

        if ($footer_style == 'style6' && $footer_top_section_switch == 'on') { ?>
            <div class="careerfy-footer-call-action">
                <div class="row">
                    <div class="col-md-8">
                        <div class="careerfy-footer-text">
                            <h2><?php echo esc_html($footer_section_heading); ?></h2>
                            <p><?php echo esc_html($footer_section_desc); ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="careerfy-footer-reg-btn">
                            <a class="footer-register-btn" target="_blank"
                               href="<?php echo esc_url($footer_section_tbtn_link); ?>"><?php echo esc_html($footer_section_tbtn_text); ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } else if ($footer_style == 'style10' && $footer_top_section_switch == 'on') {
            $footer_logo = isset($careerfy_framework_options['footer-logo']['url']) && $careerfy_framework_options['footer-logo']['url'] != '' ? $careerfy_framework_options['footer-logo']['url'] : '';
            if ($footer_logo != '') { ?>
                <div class="col-md-12">
                    <div class="careerfy-footer-eleven-logo"><img src="<?php echo $footer_logo ?>" alt=""></div>
                </div>
            <?php } ?>
        <?php } else if ($footer_style == 'style7' && $footer_newsletter == 'on') { ?>
            <div class="careerfy-footer-nine-newslatter">
                <?php careerfy_custom_mailchimp('footer9');
                if ($footer_social == 'on') {
                    do_action('careerfy_social_icons', 'careerfy-footer-nine-social', 'view-3');
                }
                ?>
            </div>
            <?php
        }
    }

    add_action('careerfy_footer_top_secion', 'careerfy_footer_top_secion_callback');
}
