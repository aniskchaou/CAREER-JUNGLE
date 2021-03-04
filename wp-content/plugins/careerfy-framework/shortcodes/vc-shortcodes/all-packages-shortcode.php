<?php
/**
 * Job Packages Shortcode
 * @return html
 */

add_shortcode('careerfy_all_packages', 'careerfy_all_packages_shortcode');

function careerfy_all_packages_shortcode($atts, $content = '')
{
    global $view;
    extract(shortcode_atts(array(
        'view' => 'view1',
    ), $atts));

    $html = '';
    if (class_exists('JobSearch_plugin')) {
        wp_enqueue_script('jobsearch-packages-scripts');

        if ($view == 'view7') {
            $html = '<div class="careerfy-allpkg-comcon"><div class="row"> <div class="careerfy-priceplan-wrapper">
            ' . do_shortcode($content) . '
                    </div>
                  </div></div>' . "\n";
        } else {
            $html = '<div class="careerfy-allpkg-comcon">' . ($view == 'view3' ? '<div class="careerfy-priceplan-style5-wrap">' : '') . '
            <div class="row">
                ' . do_shortcode($content) . '
            </div>
            ' . ($view == 'view3' ? '</div>' : '') . '</div>' . "\n";
        }
    }

    return $html;
}

add_shortcode('features', 'all_packages_features_shortcode');
function all_packages_features_shortcode($atts)
{
    ob_start();
    $pckg_feat_name = isset($atts['name']) ? $atts['name'] : '';
    $pckg_feat_active = isset($atts['active']) ? $atts['active'] : '';
    ?>
    <li<?php echo($pckg_feat_active == 'yes' ? ' class="active"' : '') ?>><i class="careerfy-icon careerfy-check-square"></i> <?php echo($pckg_feat_name) ?></li>
    <?php
    $html = ob_get_clean();
    echo $html;

}

add_shortcode('careerfy_all_package_item', 'careerfy_all_package_item_shortcode');
function careerfy_all_package_item_shortcode($atts)
{
    global $view;
    extract(shortcode_atts(array(
        'att_pck' => '',
        'subtitle' => '',
        'featured' => '',
        'duration' => '',
        'pckg_features' => '',
        'desc' => '',
    ), $atts));

    ob_start();
    if (class_exists('JobSearch_plugin')) {
        $this_wredirct_url = jobsearch_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $att_pck = careerfy__get_post_id($att_pck, 'package');
        if ($att_pck > 0 && get_post_type($att_pck) == 'package') {
            $package_type = get_post_meta($att_pck, 'jobsearch_field_package_type', true);
            $check_pkg_types = apply_filters('jobsearch_careerfy_allpkg_shtypes_list', array('job', 'featured_jobs', 'cv', 'emp_allin_one', 'feature_job', 'candidate', 'promote_profile', 'urgent_pkg', 'candidate_profile', 'employer_profile','cand_resume'));
            if (in_array($package_type, $check_pkg_types)) {
                $pkg_type = get_post_meta($att_pck, 'jobsearch_field_charges_type', true);
                $pkg_price = get_post_meta($att_pck, 'jobsearch_field_package_price', true);
                $pkg_exfield_title = get_post_meta($att_pck, 'jobsearch_field_package_exfield_title', true);
                $pkg_exfield_val = get_post_meta($att_pck, 'jobsearch_field_package_exfield_val', true);
                $pkg_exfield_status = get_post_meta($att_pck, 'jobsearch_field_package_exfield_status', true);
                $buy_btn_class = 'jobsearch-subscribe-job-pkg';

                if ($package_type == 'featured_jobs') {
                    $buy_btn_class = 'jobsearch-subscribe-fjobs-pkg';
                } else if ($package_type == 'cv') {
                    $buy_btn_class = 'jobsearch-subscribe-cv-pkg';
                } else if ($package_type == 'candidate') {
                    $buy_btn_class = 'jobsearch-subscribe-candidate-pkg';
                } else if ($package_type == 'promote_profile') {
                    $buy_btn_class = 'jobsearch-promoteprof-pkg';
                } else if ($package_type == 'urgent_pkg') {
                    $buy_btn_class = 'jobsearch-urgentsub-pkg';
                } else if ($package_type == 'candidate_profile') {
                    $buy_btn_class = 'jobsearch-subscand-profile-pkg';
                } else if ($package_type == 'employer_profile') {
                    $buy_btn_class = 'jobsearch-subsemp-profile-pkg';
                } else if ($package_type == 'emp_allin_one') {
                    $buy_btn_class = 'jobsearch-subs-allinone-pkg';
                } else if ($package_type == 'cand_resume') {
                    $buy_btn_class = 'jobsearch-candpdf-resm-pkg';
                }

                $buy_btn_class = apply_filters('jobsearch_allpkg_sh_btnclass_filtr', $buy_btn_class, $att_pck);
                if ($view == 'view12') { ?>
                    <div class="col-md-4 careerfy-sixteen-priceplan-wrap">
                        <div class="careerfy-sixteen-priceplan <?php echo($featured == 'yes' ? ' active' : '') ?>">
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
                                    echo '<span>' . jobsearch_get_currency_symbol() . '' . ($ret_price) . ' ' . ($duration != '' ? '<small> ' . $duration . '</small>' : '') . '</span>';
                                }
                            } else {
                                echo '<span>' . esc_html__('Free', 'careerfy-frame') . '</span>';
                            }
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
                            <?php }
                            if (is_user_logged_in()) { ?>
                                <div class="careerfy-sixteen-priceplan-btn">
                                    <a href="javascript:void(0);"
                                       class="<?php echo($buy_btn_class) ?>"
                                       data-id="<?php echo($att_pck) ?>"><?php esc_html_e('Select Plan', 'careerfy-frame') ?></a>
                                    <span class="pkg-loding-msg" style="display:none;"></span></div>
                            <?php } else { ?>
                                <div class="careerfy-sixteen-priceplan-btn"><a href="javascript:void(0);"
                                                                               class="jobsearch-open-signin-tab jobsearch-wredirct-url jobsearch-pkg-bouybtn"
                                                                               data-id="<?php echo($att_pck) ?>"
                                                                               data-wredircto="<?php echo($this_wredirct_url) ?>"><?php esc_html_e('Select Plan', 'careerfy-frame') ?></a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } else if ($view == 'view10') { ?>
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
                                        echo '<span><small>' . jobsearch_get_currency_symbol() . '</small>' . ($ret_price) . ' ' . ($duration != '' ? '<strong> ' . $duration . '</strong>' : '') . '</span>';
                                    }
                                } else {
                                    echo '<span>' . esc_html__('Free', 'careerfy-frame') . '</span>';
                                }
                                ?>
                            </div>

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
                                    <?php } ?>
                                </ul>
                            <?php } ?>

                            <?php if (is_user_logged_in()) { ?>
                                <div class="careerfy-priceplan-thirteen-btn">
                                    <a href="javascript:void(0);"
                                       class="<?php echo($buy_btn_class) ?> "
                                       data-id="<?php echo($att_pck) ?>"><?php esc_html_e('Get Plan', 'careerfy-frame') ?></a>
                                    <span class="pkg-loding-msg" style="display:none;"></span></div>
                            <?php } else { ?>
                                <div class="careerfy-priceplan-thirteen-btn">
                                    <a href="javascript:void(0);"
                                       class="jobsearch-open-signin-tab jobsearch-wredirct-url jobsearch-pkg-bouybtn "
                                       data-id="<?php echo($att_pck) ?>"
                                       data-wredircto="<?php echo($this_wredirct_url) ?>"><?php esc_html_e('Get Plan', 'careerfy-frame') ?></a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } else if ($view == 'view11') { ?>
                    <div class="col-md-3">
                        <div class="careerfy-fifteen-packages-plan <?php echo($featured == 'yes' ? ' active' : '') ?>">
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
                                    echo '<span><strong>' . jobsearch_get_currency_symbol() . '</strong> ' . ($ret_price) . ' ' . ($duration != '' ? '<small> ' . $duration . '</small>' : '') . '</span>';
                                }
                            } else {
                                echo '<span>' . esc_html__('Free', 'careerfy-frame') . '</span>';
                            }
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
                            <?php }
                            if (is_user_logged_in()) { ?>
                                <a href="javascript:void(0);"
                                   class="<?php echo($buy_btn_class) ?> careerfy-fifteen-packages-plan-btn"
                                   data-id="<?php echo($att_pck) ?>"><?php esc_html_e('Get Plan', 'careerfy-frame') ?></a>
                                <span class="pkg-loding-msg" style="display:none;"></span>
                            <?php } else { ?>
                                <a href="javascript:void(0);"
                                   class="jobsearch-open-signin-tab jobsearch-wredirct-url jobsearch-pkg-bouybtn careerfy-fifteen-packages-plan-btn"
                                   data-id="<?php echo($att_pck) ?>"
                                   data-wredircto="<?php echo($this_wredirct_url) ?>"><?php esc_html_e('Get Plan', 'careerfy-frame') ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } else if ($view == 'view9') { ?>
                    <div class="col-md-3">
                        <div class="careerfy-priceplan-twelve <?php echo($featured == 'yes' ? ' active' : '') ?>">
                            <div class="careerfy-priceplan-twelve-top ">
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
                                <a href="#" class="careerfy-icon careerfy-right-arrow-long"></a>
                            </div>
                            <ul>
                                <?php
                                if (function_exists('vc_param_group_parse_atts')) {
                                    $pckg_features = vc_param_group_parse_atts($pckg_features);
                                }
                                if (!empty($pckg_features) || !empty($pkg_exfield_title)) {
                                    ?>

                                    <?php
                                    if (!empty($pkg_exfield_title)) {
                                        $_exf_counter = 0;
                                        foreach ($pkg_exfield_title as $_exfield_title) {
                                            $_exfield_status = isset($pkg_exfield_status[$_exf_counter]) ? $pkg_exfield_status[$_exf_counter] : '';
                                            ?>
                                            <li<?php echo($_exfield_status == 'active' ? ' class="active"' : '') ?>>
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
                                        <li<?php echo($pckg_feat_active == 'yes' ? ' class="active"' : '') ?>><i
                                                    class="careerfy-icon careerfy-checked"></i><?php echo($pckg_feat_name) ?>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                            <?php if (is_user_logged_in()) {
                                ?>
                                <div class="careerfy-priceplan-twelve-btn">
                                    <a href="javascript:void(0);"
                                       class="<?php echo($buy_btn_class) ?>"
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
                <?php } else if ($view == 'view8') { ?>
                    <div class="col-md-3">
                        <div class="careerfy-dream-packages<?php echo($featured == 'yes' ? ' active' : '') ?>">
                            <?php
                            ob_start();
                            ?>
                            <h3><?php echo get_the_title($att_pck) ?></h3>
                            <?php
                            $titlhtml = ob_get_clean();
                            echo apply_filters('jobsearch_allpkg_sh_view8_title_html', $titlhtml, $atts);
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
                                       class="careerfy-priceplan-style5-btn <?php echo($buy_btn_class) ?>"
                                       data-id="<?php echo($att_pck) ?>"><?php echo apply_filters('jobsearch_allpkg_sh_view8_btntitle_txt', esc_html__('Choose Plan', 'careerfy-frame')) ?></a>
                                    <span class="pkg-loding-msg" style="display:none;"></span>
                                <?php } else { ?>
                                    <a href="javascript:void(0);"
                                       class="careerfy-priceplan-style5-btn jobsearch-open-signin-tab jobsearch-wredirct-url jobsearch-pkg-bouybtn"
                                       data-id="<?php echo($att_pck) ?>"
                                       data-wredircto="<?php echo($this_wredirct_url) ?>"><?php echo apply_filters('jobsearch_allpkg_sh_view8_btntitle_txt', esc_html__('Choose Plan', 'careerfy-frame')) ?></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php
                } elseif ($view == 'view7') { ?>
                    <div class="col-md-4 careerfy-view7-priceplane-wrap">
                        <div class="careerfy-view7-priceplane <?php echo($featured == 'yes' ? 'active' : '') ?>">
                            <?php
                            if (isset($subtitle) && $subtitle != '') {
                                ?>
                                <span class="careerfy-view7-priceplane-title"><?php echo($subtitle) ?></span>
                                <?php
                            }
                            ?>
                            <div class="view7-priceplane-section-wrap">
                                <div class="view7-priceplane-section">
                                    <h2><?php echo get_the_title($att_pck) ?></h2>

                                    <?php
                                    if ($pkg_type == 'paid') {
                                        echo '<span>' . jobsearch_get_price_format($pkg_price) . '</span>';
                                    } else {
                                        echo '<span>' . esc_html__('Free', 'careerfy-frame') . '</span>';
                                    }
                                    ?>
                                </div>
                            </div>


                            <div class="grab-view7-priceplane">
                                <?php
                                if (isset($desc) && $desc != '') {
                                    ?>
                                    <p><?php echo($desc) ?></p>
                                    <?php
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
                                                <li<?php echo($_exfield_status == 'active' ? ' class="active"' : '') ?>>
                                                    <i class="careerfy-icon careerfy-check-mark"></i> <?php echo $_exfield_title ?>
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
                                                        class="careerfy-icon careerfy-check-mark"></i> <?php echo($pckg_feat_name) ?>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                <?php }

                                if (is_user_logged_in()) { ?>
                                    <a href="javascript:void(0);"
                                       class="careerfy-view7-priceplane-btn <?php echo($buy_btn_class) ?>"
                                       data-id="<?php echo($att_pck) ?>"><?php esc_html_e('Read More', 'careerfy-frame') ?>
                                        <i class="careerfy-icon careerfy-arrow-right"></i> </a>
                                    <span class="pkg-loding-msg" style="display:none;"></span>
                                <?php } else { ?>
                                    <a href="javascript:void(0);"
                                       class="careerfy-view7-priceplane-btn jobsearch-open-signin-tab jobsearch-wredirct-url jobsearch-pkg-bouybtn"
                                       data-wredircto="<?php echo($this_wredirct_url) ?>"
                                       data-id="<?php echo($att_pck) ?>"><?php esc_html_e('Read More', 'careerfy-frame') ?>
                                        <i class="careerfy-icon careerfy-arrow-right"></i> </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php
                } elseif ($view == 'view6') { ?>
                    <div class="col-md-4">
                        <div class="careerfy-classic-priceplane <?php echo($featured == 'yes' ? 'active' : '') ?>">
                            <h2><?php echo get_the_title($att_pck) ?></h2>
                            <?php
                            if ($subtitle != '') {
                                ?>
                                <span class="careerfy-classic-priceplane-title"><?php echo($subtitle) ?></span>
                            <?php } ?>
                            <div class="careerfy-priceplane-section">
                                <?php
                                if ($pkg_type == 'paid') {
                                    echo '<span>' . jobsearch_get_price_format($pkg_price) . ' <small>' . esc_html__('only', 'careerfy-frame') . '</small></span>';
                                } else {
                                    echo '<span>' . esc_html__('Free', 'careerfy-frame') . '</span>';
                                }
                                if ($desc != '') {
                                    ?>
                                    <p><?php echo($desc) ?></p>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="grab-classic-priceplane">
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
                                                <li<?php echo($_exfield_status == 'active' ? ' class="active"' : '') ?>>
                                                    <i class="careerfy-icon careerfy-check-square"></i> <?php echo $_exfield_title ?>
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
                                <?php } ?>

                                <?php if (is_user_logged_in()) { ?>
                                    <a href="javascript:void(0);"
                                       class="careerfy-classic-priceplane-btn <?php echo($buy_btn_class) ?>"
                                       data-id="<?php echo($att_pck) ?>"><?php esc_html_e('Get Started', 'careerfy-frame') ?> </a>
                                    <span class="pkg-loding-msg" style="display:none;"></span>
                                <?php } else { ?>
                                    <a href="javascript:void(0);"
                                       class="careerfy-classic-priceplane-btn jobsearch-open-signin-tab jobsearch-wredirct-url jobsearch-pkg-bouybtn"
                                       data-wredircto="<?php echo($this_wredirct_url) ?>"
                                       data-id="<?php echo($att_pck) ?>"><?php esc_html_e('Get Started', 'careerfy-frame') ?> </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php
                } else if ($view == 'view5') { ?>
                    <div class="col-md-3 careerfy-simple-priceplane <?php echo($featured == 'yes' ? 'active' : '') ?>">
                        <div class="careerfy-simple-priceplane-wrap">
                            <?php
                            if ($featured == 'yes' && $subtitle != '') {
                                ?>
                                <span class="careerfy-simple-priceplane-active"><?php echo($subtitle) ?></span>
                                <?php
                            }
                            ?>
                            <div class="careerfy-simple-priceplane-basic">
                                <h2><?php echo get_the_title($att_pck) ?></h2>
                                <?php
                                if ($pkg_type == 'paid') {
                                    echo '<span>' . jobsearch_get_price_format($pkg_price) . ' <small>' . esc_html__('only', 'careerfy-frame') . '</small></span>';
                                } else {
                                    echo '<span>' . esc_html__('Free', 'careerfy-frame') . '</span>';
                                }
                                ?>
                                <?php if (is_user_logged_in()) { ?>
                                    <a href="javascript:void(0);" class="<?php echo($buy_btn_class) ?>"
                                       data-id="<?php echo($att_pck) ?>"><?php esc_html_e('Choose Plan', 'careerfy-frame') ?> </a>
                                    <span class="pkg-loding-msg" style="display:none;"></span>
                                <?php } else { ?>
                                    <a href="javascript:void(0);"
                                       class="jobsearch-open-signin-tab jobsearch-wredirct-url jobsearch-pkg-bouybtn"
                                       data-id="<?php echo($att_pck) ?>"
                                       data-wredircto="<?php echo($this_wredirct_url) ?>"><?php esc_html_e('Choose Plan', 'careerfy-frame') ?> </a>
                                <?php } ?>
                            </div>
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
                        </div>
                    </div>
                <?php } else if ($view == 'view4') { ?>
                    <div class="col-md-4">
                        <div class="careerfy-classic-priceplane <?php echo($featured == 'yes' ? 'active' : '') ?>">
                            <h2><?php echo get_the_title($att_pck) ?></h2>
                            <?php
                            if ($subtitle != '') {
                                ?>
                                <span class="careerfy-classic-priceplane-title"><?php echo($subtitle) ?></span>
                                <?php
                            }
                            ?>
                            <div class="careerfy-priceplane-section">
                                <?php
                                if ($pkg_type == 'paid') {
                                    echo '<span>' . jobsearch_get_price_format($pkg_price) . ' <small>' . esc_html__('only', 'careerfy-frame') . '</small></span>';
                                } else {
                                    echo '<span>' . esc_html__('Free', 'careerfy-frame') . '</span>';
                                }

                                if ($desc != '') {
                                    ?>
                                    <p><?php echo($desc) ?></p>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="grab-classic-priceplane">
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
                                                <li<?php echo($_exfield_status == 'active' ? ' class="active"' : '') ?>>
                                                    <i class="careerfy-icon careerfy-check-square"></i> <?php echo $_exfield_title ?>
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
                                       class="careerfy-classic-priceplane-btn <?php echo($buy_btn_class) ?>"
                                       data-id="<?php echo($att_pck) ?>"><?php esc_html_e('Get Started', 'careerfy-frame') ?> </a>
                                    <span class="pkg-loding-msg" style="display:none;"></span>
                                <?php } else { ?>
                                    <a href="javascript:void(0);"
                                       class="careerfy-classic-priceplane-btn jobsearch-open-signin-tab jobsearch-wredirct-url jobsearch-pkg-bouybtn"
                                       data-id="<?php echo($att_pck) ?>"
                                       data-wredircto="<?php echo($this_wredirct_url) ?>"><?php esc_html_e('Get Started', 'careerfy-frame') ?> </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } else if ($view == 'view3') { ?>
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
                                <?php
                            }
                            ?>
                            <div class="clearfix"></div>
                            <?php if (is_user_logged_in()) { ?>
                                <a href="javascript:void(0);"
                                   class="careerfy-priceplan-style5-btn <?php echo($buy_btn_class) ?>"
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
                                   class="careerfy-additional-priceplane-btn <?php echo($buy_btn_class) ?>"
                                   data-id="<?php echo($att_pck) ?>"><?php echo esc_html__('Proceed', 'careerfy-frame') ?>
                                    <i
                                            class="careerfy-icon careerfy-right-arrow-1"></i></a>
                                <span class="pkg-loding-msg" style="display:none;"></span>
                            <?php } else { ?>
                                <a href="javascript:void(0);"
                                   class="careerfy-additional-priceplane-btn jobsearch-open-signin-tab jobsearch-wredirct-url jobsearch-pkg-bouybtn"
                                   data-id="<?php echo($att_pck) ?>"
                                   data-wredircto="<?php echo($this_wredirct_url) ?>"><?php echo esc_html__('Proceed', 'careerfy-frame') ?>
                                    <i class="careerfy-icon careerfy-right-arrow-1"></i></a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                } else {
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
                            if (!empty($pckg_features) || !empty($pkg_exfield_title)) {
                                ?>
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
                                   class="careerfy-packages-priceplane-btn <?php echo($buy_btn_class) ?>"
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
                }
            }
        }
    }
    $html = ob_get_clean();
    return $html;
}