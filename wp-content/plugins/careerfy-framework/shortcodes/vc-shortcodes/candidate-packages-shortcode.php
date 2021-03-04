<?php
/**
 * Candidate Packages Shortcode
 * @return html
 */
add_shortcode('careerfy_candidate_packages', 'careerfy_candidate_packages_shortcode');

function careerfy_candidate_packages_shortcode($atts, $content = '')
{

    global $view;
    extract(shortcode_atts(array(
        'view' => '',
    ), $atts));

    $html = '';

    if (class_exists('JobSearch_plugin')) {
        wp_enqueue_script('jobsearch-packages-scripts');
        $html = '
        <div class="row">
            ' . do_shortcode($content) . '
        </div>' . "\n";
    }

    return apply_filters('careerfy_candidate_pkgs_sh_parent_front', $html, $view);
}

add_shortcode('careerfy_candidate_package_item', 'careerfy_candidate_package_item_shortcode');

function careerfy_candidate_package_item_shortcode($atts)
{

    global $view;
    extract(shortcode_atts(array(
        'att_pck' => '',
        'featured' => '',
        'subtitle' => '',
        'desc' => '',
        'pckg_features' => '',
    ), $atts));

    ob_start();
    if (class_exists('JobSearch_plugin')) {
        $this_wredirct_url = jobsearch_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $att_pck = careerfy__get_post_id($att_pck, 'package');
        if ($att_pck > 0 && get_post_type($att_pck) == 'package') {

            $package_type = get_post_meta($att_pck, 'jobsearch_field_package_type', true);
            if ($package_type == 'candidate') {

                $pkg_type = get_post_meta($att_pck, 'jobsearch_field_charges_type', true);
                $pkg_price = get_post_meta($att_pck, 'jobsearch_field_package_price', true);

                $pkg_exfield_title = get_post_meta($att_pck, 'jobsearch_field_package_exfield_title', true);
                $pkg_exfield_val = get_post_meta($att_pck, 'jobsearch_field_package_exfield_val', true);
                $pkg_exfield_status = get_post_meta($att_pck, 'jobsearch_field_package_exfield_status', true);
                ob_start();
                ?>
                <div class="col-md-4">
                    <div class="careerfy-classic-priceplane <?php echo($featured == 'yes' ? 'active' : '') ?>">
                        <h2><?php echo get_the_title($att_pck) ?></h2>
                        <?php
                        if ($subtitle != '') { ?>
                            <span class="careerfy-classic-priceplane-title"><?php echo($subtitle) ?></span>
                        <?php } ?>
                        <div class="careerfy-priceplane-section">
                            <?php

                            if ($pkg_type == 'paid') {
                                echo '<span>' . jobsearch_get_price_format($pkg_price) . ' <small>' . esc_html__('only', 'careerfy-frame') . '</small></span>';
                            } else {
                                echo '<span>' . esc_html__('Free', 'careerfy-frame') . '</span>';
                            }

                            if ($desc != '') { ?>
                                <p><?php echo($desc) ?></p>
                            <?php } ?>
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
                                   class="careerfy-classic-priceplane-btn jobsearch-subscribe-candidate-pkg"
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
                $item_html = ob_get_clean();
                echo apply_filters('careerfy_candidate_pkgs_sh_item_front', $item_html, $att_pck, $atts, $view);
            }
        }
    }

    $html = ob_get_clean();

    return $html;
}
