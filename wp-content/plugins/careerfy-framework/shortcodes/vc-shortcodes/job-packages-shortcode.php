<?php
/**
 * Job Packages Shortcode
 * @return html
 */
add_shortcode('careerfy_job_packages', 'careerfy_job_packages_shortcode');
function careerfy_job_packages_shortcode($atts, $content = '')
{
    global $view;
    extract(shortcode_atts(array(
        'view' => 'style1',
    ), $atts));

    $html = '';

    if (class_exists('JobSearch_plugin')) {
        wp_enqueue_script('jobsearch-packages-scripts');
        $html = '
        ' . ($view == 'view3' ? '<div class="careerfy-priceplan-style5-wrap">' : '') . '
        <div class="row">
            ' . do_shortcode($content) . '
        </div>
        ' . ($view == 'view3' ? '</div>' : '') . "\n";
    }


    $html = apply_filters('geek_finder_job_package_view4', $html, $view, $content);
    return $html;
}

add_shortcode('careerfy_job_package_item', 'careerfy_job_package_item_shortcode');

function careerfy_job_package_item_shortcode($atts)
{

    global $view;
    extract(shortcode_atts(array(
        'att_pck' => '',
        'featured' => '',
        'duration' => '',
        'pckg_features' => '',
    ), $atts));

    ob_start();
    if (class_exists('JobSearch_plugin')) {
        $this_wredirct_url = jobsearch_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $att_pck = careerfy__get_post_id($att_pck, 'package');
        if ($att_pck > 0 && get_post_type($att_pck) == 'package') {
            $package_type = get_post_meta($att_pck, 'jobsearch_field_package_type', true);
            $check_pkg_type = apply_filters('careerfy_job_pckges_sh_front_type_check', 'job');

            if ($package_type == $check_pkg_type) {
                $pkg_type = get_post_meta($att_pck, 'jobsearch_field_charges_type', true);
                $pkg_price = get_post_meta($att_pck, 'jobsearch_field_package_price', true);
                $pkg_exfield_title = get_post_meta($att_pck, 'jobsearch_field_package_exfield_title', true);
                $pkg_exfield_val = get_post_meta($att_pck, 'jobsearch_field_package_exfield_val', true);
                $pkg_exfield_status = get_post_meta($att_pck, 'jobsearch_field_package_exfield_status', true);
                if ($view == 'view6') { ?>
                    <div class="col-md-3 careerfy-priceplan-thirteen-wrap">
                        <div class="careerfy-priceplan-thirteen <?php echo($featured == 'yes' ? ' active' : '') ?>">
                            <div class="careerfy-priceplan-thirteen-top">
                                <h2><?php echo get_the_title($att_pck) ?></h2>
                                <?php
                                if ($pkg_type == 'paid') {
                                    $ret_price = '';
                                    if (!empty($pkg_price)) {
                                        if (function_exists('wc_price')) {
                                            $ret_price = wc_price($pkg_price);
                                            $ret_price = wp_kses($pkg_price, array());
                                        } else {
                                            $ret_price = preg_replace("/[^0-9,.]+/iu", "", $pkg_price);
                                            $ret_price = number_format($ret_price, 2, ".", ",");
                                        }
                                    }
                                    if (!empty($ret_price)) {
                                        echo '<span><small>' . jobsearch_get_currency_symbol() . '</small> ' . ($ret_price) . ' ' . ($duration != '' ? '<small> ' . $duration . '</small>' : '') . '</span>';
                                    }
                                } else {
                                    echo '<span>' . esc_html__('Free', 'careerfy-frame') . '</span>';
                                }
                                ?>
                            </div>
                            <ul>
                                <?php
                                if (function_exists('vc_param_group_parse_atts')) {
                                    $pckg_features = vc_param_group_parse_atts($pckg_features);
                                }
                                if (!empty($pckg_features) || !empty($pkg_exfield_title)) { ?>
                                    <ul>
                                        <?php
                                        if (!empty($pkg_exfield_title)) {
                                            $_exf_counter = 0;
                                            foreach ($pkg_exfield_title as $_exfield_title) {
                                                $_exfield_status = isset($pkg_exfield_status[$_exf_counter]) ? $pkg_exfield_status[$_exf_counter] : '';
                                                ?>
                                                <li><?php echo $_exfield_title ?></li>
                                                <?php
                                                $_exf_counter++;
                                            }
                                        }
                                        foreach ($pckg_features as $pckg_feature) {
                                            $pckg_feat_name = isset($pckg_feature['feat_name']) ? $pckg_feature['feat_name'] : '';
                                            $pckg_feat_active = isset($pckg_feature['feat_active']) ? $pckg_feature['feat_active'] : '';
                                            ?>
                                            <li<?php echo($pckg_feat_active == 'yes' ? ' class="active"' : '') ?>><?php echo($pckg_feat_name) ?></li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                <?php } ?>
                            </ul>
                            <?php if (is_user_logged_in()) { ?>
                                <div class="careerfy-priceplan-thirteen-btn">
                                    <a href="javascript:void(0);"
                                       class="jobsearch-subscribe-job-pkg"
                                       data-id="<?php echo($att_pck) ?>"><?php esc_html_e('Get Plan', 'careerfy-frame') ?></a>
                                    <span class="pkg-loding-msg" style="display:none;"></span></div>
                            <?php } else { ?>
                                <div class="careerfy-priceplan-twelve-btn"><a href="javascript:void(0);"
                                                                              class="jobsearch-open-signin-tab jobsearch-wredirct-url jobsearch-pkg-bouybtn"
                                                                              data-id="<?php echo($att_pck) ?>"
                                                                              data-wredircto="<?php echo($this_wredirct_url) ?>"><?php esc_html_e('Get Plan', 'careerfy-frame') ?></a>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                <?php } else if ($view == 'view5') { ?>
                    <div class="col-md-3">
                        <div class="careerfy-priceplan-twelve <?php echo($featured == 'yes' ? ' active' : '') ?>">
                            <div class="careerfy-priceplan-twelve-top">
                                <div><?php echo get_the_title($att_pck) ?></div>
                                <?php
                                if ($pkg_type == 'paid') {
                                    $ret_price = '';
                                    if (!empty($pkg_price)) {
                                        if (function_exists('wc_price')) {
                                            $ret_price = wc_price($pkg_price);
                                            $ret_price = wp_kses($pkg_price, array());
                                        } else {
                                            $ret_price = preg_replace("/[^0-9,.]+/iu", "", $pkg_price);
                                            $ret_price = number_format($ret_price, 2, ".", ",");
                                        }
                                    }
                                    if (!empty($ret_price)) {
                                        echo '<span><em>' . jobsearch_get_currency_symbol() . '</em> ' . ($ret_price) . ' ' . ($duration != '' ? '<small> ' . $duration . '</small>' : '') . '</span>';
                                    }
                                } else {
                                    echo '<span>' . esc_html__('Free', 'careerfy-frame') . '</span>';
                                }
                                ?>
                                <a href="#" class="careerfy-icon careerfy-next-1"></a>
                            </div>
                            <ul>
                                <?php
                                if (function_exists('vc_param_group_parse_atts')) {
                                    $pckg_features = vc_param_group_parse_atts($pckg_features);
                                }
                                if (!empty($pckg_features) || !empty($pkg_exfield_title)) {
                                    ?>
                                    <ul>
                                        <?php
                                        if (!empty($pkg_exfield_title)) {
                                            $_exf_counter = 0;
                                            foreach ($pkg_exfield_title as $_exfield_title) {
                                                $_exfield_status = isset($pkg_exfield_status[$_exf_counter]) ? $pkg_exfield_status[$_exf_counter] : '';
                                                ?>
                                                <li>
                                                    <i class="careerfy-icon careerfy-checked"></i><?php echo $_exfield_title ?>
                                                </li>
                                                <?php
                                                $_exf_counter++;
                                            }
                                        }
                                        foreach ($pckg_features as $pckg_feature) {
                                            $pckg_feat_name = isset($pckg_feature['feat_name']) ? $pckg_feature['feat_name'] : '';
                                            $pckg_feat_active = isset($pckg_feature['feat_active']) ? $pckg_feature['feat_active'] : '';
                                            ?>
                                            <li<?php echo($pckg_feat_active == 'yes' ? ' class="active"' : '') ?>><?php echo($pckg_feat_name) ?></li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                    <?php
                                }
                                ?>
                            </ul>
                            <?php if (is_user_logged_in()) {
                                ?>
                                <div class="careerfy-priceplan-twelve-btn">
                                    <a href="javascript:void(0);"
                                       class="jobsearch-subscribe-job-pkg"
                                       data-id="<?php echo($att_pck) ?>"><?php esc_html_e('Get Started', 'careerfy-frame') ?></a>
                                    <span class="pkg-loding-msg" style="display:none;"></span></div>
                            <?php } else { ?>
                                <div class="careerfy-priceplan-twelve-btn"><a href="javascript:void(0);"
                                                                              class="jobsearch-open-signin-tab jobsearch-wredirct-url jobsearch-pkg-bouybtn"
                                                                              data-id="<?php echo($att_pck) ?>"
                                                                              data-wredircto="<?php echo($this_wredirct_url) ?>"><?php esc_html_e('Get Started', 'careerfy-frame') ?></a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } else if ($view == 'view4') { ?>
                    <div class="col-md-3">
                        <div class="careerfy-dream-packages <?php echo($featured == 'yes' ? ' active' : '') ?>">
                            <h3><?php echo get_the_title($att_pck) ?></h3>
                            <?php
                            if (function_exists('vc_param_group_parse_atts')) {
                                $pckg_features = vc_param_group_parse_atts($pckg_features);
                            }
                            if (!empty($pckg_features) || !empty($pkg_exfield_title)) {
                                ?>
                                <ul>
                                    <?php
                                    if (!empty($pkg_exfield_title)) {
                                        $_exf_counter = 0;
                                        foreach ($pkg_exfield_title as $_exfield_title) {
                                            $_exfield_status = isset($pkg_exfield_status[$_exf_counter]) ? $pkg_exfield_status[$_exf_counter] : '';
                                            ?>
                                            <li<?php echo($_exfield_status == 'active' ? ' class="active"' : '') ?>><?php echo $_exfield_title ?></li>
                                            <?php
                                            $_exf_counter++;
                                        }
                                    }
                                    foreach ($pckg_features as $pckg_feature) {
                                        $pckg_feat_name = isset($pckg_feature['feat_name']) ? $pckg_feature['feat_name'] : '';
                                        $pckg_feat_active = isset($pckg_feature['feat_active']) ? $pckg_feature['feat_active'] : '';
                                        ?>
                                        <li<?php echo($pckg_feat_active == 'yes' ? ' class="active"' : '') ?>><?php echo($pckg_feat_name) ?></li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                                <?php
                            }
                            ?>
                            <div class="careerfy-dream-packagesplan">
                                <?php
                                if ($pkg_type == 'paid') {
                                    $ret_price = '';
                                    if (!empty($pkg_price)) {
                                        if (function_exists('wc_price')) {
                                            $ret_price = wc_price($pkg_price);
                                            $ret_price = wp_kses($pkg_price, array());
                                        } else {
                                            $ret_price = preg_replace("/[^0-9,.]+/iu", "", $pkg_price);
                                            $ret_price = number_format($ret_price, 2, ".", ",");
                                        }
                                    }
                                    if (!empty($ret_price)) {
                                        echo '<span><strong>' . jobsearch_get_currency_symbol() . '</strong> ' . ($ret_price) . ' ' . ($duration != '' ? '<small> ' . $duration . '</small>' : '') . '</span>';
                                    }
                                } else {
                                    echo '<span>' . esc_html__('Free', 'careerfy-frame') . '</span>';
                                }
                                if (is_user_logged_in()) { ?>
                                    <a href="javascript:void(0);"
                                       class="careerfy-priceplan-style5-btn jobsearch-subscribe-job-pkg"
                                       data-id="<?php echo($att_pck) ?>"><?php esc_html_e('Choose Plan', 'careerfy-frame') ?></a>
                                    <span class="pkg-loding-msg" style="display:none;"></span>
                                <?php } else { ?>
                                    <a href="javascript:void(0);"
                                       class="careerfy-priceplan-style5-btn jobsearch-open-signin-tab jobsearch-wredirct-url jobsearch-pkg-bouybtn"
                                       data-id="<?php echo($att_pck) ?>"
                                       data-wredircto="<?php echo($this_wredirct_url) ?>"><?php esc_html_e('Choose Plan', 'careerfy-frame') ?></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } elseif ($view == 'view3') { ?>
                    <div class="col-md-4 <?php echo($featured == 'yes' ? 'active' : '') ?>">
                        <div class="careerfy-priceplan-style5">
                            <?php echo($featured == 'yes' ? '<div class="active-plan">' . esc_html__('Most popular', 'careerfy-frame') . '</div>' : '') ?>
                            <h6><?php echo get_the_title($att_pck) ?></h6>
                            <?php
                            if ($pkg_type == 'paid') {
                                echo '<span>' . jobsearch_get_price_format($pkg_price, 'strong') . ' ' . ($duration != '' ? '<small>/' . $duration . '</small>' : '') . '</span>';
                            } else {
                                echo '<span>' . esc_html__('Free', 'careerfy-frame') . '</span>';
                            }

                            if (function_exists('vc_param_group_parse_atts')) {
                                $pckg_features = vc_param_group_parse_atts($pckg_features);
                            }
                            if (!empty($pckg_features) || !empty($pkg_exfield_title)) {

                                ?>
                                <ul>
                                    <?php
                                    if (!empty($pkg_exfield_title)) {
                                        $_exf_counter = 0;
                                        foreach ($pkg_exfield_title as $_exfield_title) {
                                            $_exfield_status = isset($pkg_exfield_status[$_exf_counter]) ? $pkg_exfield_status[$_exf_counter] : '';
                                            ?>
                                            <li<?php echo($_exfield_status == 'active' ? ' class="active"' : '') ?>><?php echo $_exfield_title ?></li>
                                            <?php
                                            $_exf_counter++;
                                        }
                                    }
                                    foreach ($pckg_features as $pckg_feature) {
                                        $pckg_feat_name = isset($pckg_feature['feat_name']) ? $pckg_feature['feat_name'] : '';
                                        $pckg_feat_active = isset($pckg_feature['feat_active']) ? $pckg_feature['feat_active'] : '';
                                        ?>
                                        <li<?php echo($pckg_feat_active == 'yes' ? ' class="active"' : '') ?>><?php echo($pckg_feat_name) ?></li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            <?php } ?>
                            <div class="clearfix"></div>
                            <?php if (is_user_logged_in()) { ?>
                                <a href="javascript:void(0);"
                                   class="careerfy-priceplan-style5-btn jobsearch-subscribe-job-pkg"
                                   data-id="<?php echo($att_pck) ?>"><?php esc_html_e('Buy now', 'careerfy-frame') ?></a>
                                <span class="pkg-loding-msg" style="display:none;"></span>
                            <?php } else { ?>
                                <a href="javascript:void(0);"
                                   class="careerfy-priceplan-style5-btn jobsearch-open-signin-tab jobsearch-wredirct-url jobsearch-pkg-bouybtn"
                                   data-id="<?php echo($att_pck) ?>"
                                   data-wredircto="<?php echo($this_wredirct_url) ?>"><?php esc_html_e('Buy now', 'careerfy-frame') ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                } else if ($view == 'view2') {
                    ?>
                    <div class="col-md-4">
                        <div class="careerfy-additional-priceplane <?php echo($featured == 'yes' ? 'active' : '') ?>">
                            <h2><?php echo get_the_title($att_pck) ?></h2>
                            <?php
                            if ($pkg_type == 'paid') {
                                echo '<span>' . jobsearch_get_price_format($pkg_price) . ' ' . ($duration != '' ? '<small>' . $duration . '</small>' : '') . '</span>';
                            } else {
                                echo '<span>' . esc_html__('Free', 'careerfy-frame') . '</span>';
                            }

                            if (function_exists('vc_param_group_parse_atts')) {
                                $pckg_features = vc_param_group_parse_atts($pckg_features);
                            }
                            if (!empty($pckg_features) || !empty($pkg_exfield_title)) {
                                ?>
                                <ul>
                                    <?php
                                    if (!empty($pkg_exfield_title)) {
                                        $_exf_counter = 0;
                                        foreach ($pkg_exfield_title as $_exfield_title) {
                                            $_exfield_status = isset($pkg_exfield_status[$_exf_counter]) ? $pkg_exfield_status[$_exf_counter] : '';
                                            ?>
                                            <li<?php echo($_exfield_status == 'active' ? ' class="active"' : '') ?>><?php echo $_exfield_title ?></li>
                                            <?php
                                            $_exf_counter++;
                                        }
                                    }
                                    foreach ($pckg_features as $pckg_feature) {
                                        $pckg_feat_name = isset($pckg_feature['feat_name']) ? $pckg_feature['feat_name'] : '';
                                        $pckg_feat_active = isset($pckg_feature['feat_active']) ? $pckg_feature['feat_active'] : '';
                                        ?>
                                        <li<?php echo($pckg_feat_active == 'yes' ? ' class="active"' : '') ?>><?php echo($pckg_feat_name) ?></li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                                <?php
                            }
                            ?>
                            <div class="clearfix"></div>
                            <?php if (is_user_logged_in()) { ?>
                                <a href="javascript:void(0);"
                                   class="careerfy-additional-priceplane-btn jobsearch-subscribe-job-pkg"
                                   data-id="<?php echo($att_pck) ?>"><?php esc_html_e('Proceed', 'careerfy-frame') ?> <i
                                            class="careerfy-icon careerfy-right-arrow-1"></i></a>
                                <span class="pkg-loding-msg" style="display:none;"></span>
                            <?php } else { ?>
                                <a href="javascript:void(0);"
                                   class="careerfy-additional-priceplane-btn jobsearch-open-signin-tab jobsearch-wredirct-url jobsearch-pkg-bouybtn"
                                   data-id="<?php echo($att_pck) ?>"
                                   data-wredircto="<?php echo($this_wredirct_url) ?>"><?php esc_html_e('Proceed', 'careerfy-frame') ?>
                                    <i class="careerfy-icon careerfy-right-arrow-1"></i></a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                } else {
                    ob_start();
                    ?>
                    <div class="col-md-3">
                        <div class="careerfy-packages-priceplane <?php echo($featured == 'yes' ? 'active' : '') ?>">
                            <h2><?php echo get_the_title($att_pck) ?></h2>
                            <div class="packages-priceplane-price">
                                <?php
                                if ($pkg_type == 'paid') {
                                    echo '<strong>' . jobsearch_get_price_format($pkg_price) . '</strong>';
                                } else {
                                    echo '<strong>' . esc_html__('Free', 'careerfy-frame') . '</strong>';
                                }

                                if ($duration != '' && $pkg_type == 'paid') {
                                    ?>
                                    <span><?php echo($duration) ?></span>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                            if (function_exists('vc_param_group_parse_atts')) {
                                $pckg_features = vc_param_group_parse_atts($pckg_features);
                            }
                            if (!empty($pckg_features) || !empty($pkg_exfield_title)) { ?>
                                <ul>
                                    <?php
                                    if (!empty($pkg_exfield_title)) {
                                        $_exf_counter = 0;
                                        foreach ($pkg_exfield_title as $_exfield_title) {
                                            $_exfield_status = isset($pkg_exfield_status[$_exf_counter]) ? $pkg_exfield_status[$_exf_counter] : '';
                                            ?>
                                            <li<?php echo($_exfield_status == 'active' ? ' class="active"' : '') ?>><i
                                                        class="careerfy-icon careerfy-check-square"></i> <?php echo $_exfield_title ?>
                                            </li>
                                            <?php
                                            $_exf_counter++;
                                        }
                                    }
                                    foreach ($pckg_features as $pckg_feature) {
                                        $pckg_feat_name = isset($pckg_feature['feat_name']) ? $pckg_feature['feat_name'] : '';
                                        $pckg_feat_active = isset($pckg_feature['feat_active']) ? $pckg_feature['feat_active'] : '';
                                        ?>
                                        <li<?php echo($pckg_feat_active == 'yes' ? ' class="active"' : '') ?>><i
                                                    class="careerfy-icon careerfy-check-square"></i> <?php echo($pckg_feat_name) ?>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                                <?php
                            }
                            ?>

                            <?php if (is_user_logged_in()) { ?>
                                <a href="javascript:void(0);"
                                   class="careerfy-packages-priceplane-btn jobsearch-subscribe-job-pkg"
                                   data-id="<?php echo($att_pck) ?>"><?php esc_html_e('Sign up', 'careerfy-frame') ?></a>
                                <span class="pkg-loding-msg" style="display:none;"></span>
                            <?php } else { ?>
                                <a href="javascript:void(0);"
                                   class="careerfy-packages-priceplane-btn jobsearch-open-signin-tab jobsearch-wredirct-url jobsearch-pkg-bouybtn"
                                   data-wredircto="<?php echo($this_wredirct_url) ?>"
                                   data-id="<?php echo($att_pck) ?>"><?php esc_html_e('Sign up', 'careerfy-frame') ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                    $simp_pckghtml = ob_get_clean();
                    echo apply_filters('jobsearch_job_simp_pckge_viewitem_html', $simp_pckghtml, $atts);
                }
            }
        }
    }
    $html = ob_get_clean();
    $html = apply_filters('geek_finder_job_package_view4_items', $html, $view, $atts);
    return $html;
}
