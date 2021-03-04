<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

/**
 * @since 1.1.0
 */
class AllPackages extends Widget_Base
{
    /**
     * Retrieve the widget name.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'all-packages';
    }

    /**
     * Retrieve the widget title.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return __('All Packages', 'careerfy-frame');
    }

    /**
     * Retrieve the widget icon.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'fa fa-list-alt';
    }

    /**
     * Retrieve the list of categories the widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * Note that currently Elementor supports only one category.
     * When multiple categories passed, Elementor uses the first one.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories()
    {
        return ['wp-jobsearch'];
    }

    /**
     * Register the widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.1.0
     *
     * @access protected
     */

    protected function _register_controls()
    {
        $all_pckgs = array(esc_html__("Select Package", "careerfy-frame") => '');
        $args = apply_filters('careerfy_job_pkgs_vcsh_args', array(
            'post_type' => 'package',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
            'order' => 'ASC',
            'orderby' => 'title',
            'meta_query' => array(
                array(
                    'key' => 'jobsearch_field_package_type',
                    'value' => apply_filters('jobsearch_careerfy_allpkg_shtypes_list', array('job', 'featured_jobs', 'cv', 'emp_allin_one', 'feature_job', 'candidate', 'promote_profile', 'urgent_pkg', 'candidate_profile', 'employer_profile', 'cand_resume')),
                    'compare' => 'IN',
                ),
            ),
        ));
        $pkgs_query = new \WP_Query($args);
        if ($pkgs_query->found_posts > 0) {
            $pkgs_list = $pkgs_query->posts;
            if (!empty($pkgs_list)) {
                foreach ($pkgs_list as $pkg_item) {
                    $pkg_attach_product = get_post_meta($pkg_item, 'jobsearch_package_product', true);

                    if ($pkg_attach_product != '' && get_page_by_path($pkg_attach_product, 'OBJECT', 'product')) {
                        $job_pkg_post = get_post($pkg_item);
                        $job_pkg_post_name = isset($job_pkg_post->post_name) ? $job_pkg_post->post_name : '';
                        $all_pckgs[$job_pkg_post_name] = get_the_title($pkg_item);
                    }
                }
            }
        }

        $this->start_controls_section(
            'content_section',
            [
                'label' => __('All Packages Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'view',
            [
                'label' => __('Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'view1',
                'options' => [
                    'view1' => __('Style 1', 'careerfy-frame'),
                    'view2' => __('Style 2', 'careerfy-frame'),
                    'view3' => __('Style 3', 'careerfy-frame'),
                    'view4' => __('Style 4', 'careerfy-frame'),
                    'view5' => __('Style 5', 'careerfy-frame'),
                    'view6' => __('Style 6', 'careerfy-frame'),
                    'view7' => __('Style 7', 'careerfy-frame'),
                    'view8' => __('Style 8', 'careerfy-frame'),
                    'view9' => __('Style 9', 'careerfy-frame'),
                    'view10' => __('Style 10', 'careerfy-frame'),
                    'view11' => __('Style 11', 'careerfy-frame'),
                    'view12' => __('Style 12', 'careerfy-frame'),
                ],
            ]
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'att_pck', [
                'label' => __('Select Package', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'options' => $all_pckgs
            ]
        );

        $repeater->add_control(
            'featured', [
                'label' => __('Featured', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ]
            ]
        );
        $repeater->add_control(
            'duration', [
                'label' => __('Duration', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $repeater->add_control(
            'subtitle', [
                'label' => __('Subtitle', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $repeater->add_control(
            'desc', [
                'label' => __('Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );

        $repeater->add_control(
            'pckg_features', [
                'label' => __('Features', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __('[features name = "What is Lorem Ipsum" active = "yes"]
                                [features name = "What is Lorem Ipsum" active = "yes" ]
                                [features name = "What is Lorem Ipsum" active = "no" ]
                                [features name = "What is Lorem Ipsum" active = "no" ]', 'careerfy-frame'),
                'description' => __('', 'careerfy-frame'),
            ]
        );

        $this->add_control(
            'careerfy_all_package_item',
            [
                'label' => __('Add Features', 'careerfy-frame'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ att_pck }}}',
            ]
        );

        $this->end_controls_section();
    }

    public function careerfy_all_package_item_shortcode($info)
    {
        global $view;

        $atts = $this->get_settings_for_display();
        $view = $atts['view'];

        $att_pck = $info['att_pck'];
        $subtitle = $info['subtitle'];
        $featured = $info['featured'];
        $pckg_features = $info['pckg_features'];


        $duration = $info['duration'];
        $desc = $info['desc'];

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


                                        do_shortcode($pckg_features);

                                        ?>
                                    </ul>
                                <?php }
                                if (is_user_logged_in()) { ?>
                                    <div class="careerfy-sixteen-priceplan-btn">
                                        <a href="javascript:void(0);"
                                           class="<?php echo ($buy_btn_class) ?>"
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
                                    } ?>
                                </div>

                                <?php

                                if (!empty($pckg_features) || !empty($pkg_exfield_title)) {
                                    ?>
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
                                        do_shortcode($pckg_features);
                                        ?>
                                    </ul>
                                <?php } ?>

                                <?php if (is_user_logged_in()) { ?>
                                    <div class="careerfy-priceplan-thirteen-btn">
                                        <a href="javascript:void(0);"
                                           class="<?php echo ($buy_btn_class) ?> "
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
                                        do_shortcode($pckg_features);

                                        ?>
                                    </ul>
                                <?php }
                                if (is_user_logged_in()) { ?>
                                    <a href="javascript:void(0);"
                                       class="<?php echo ($buy_btn_class) ?> careerfy-fifteen-packages-plan-btn"
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

                                    if (!empty($pckg_features) || !empty($pkg_exfield_title)) {
                                        ?>

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


                                        do_shortcode($pckg_features);

                                    }
                                    ?>
                                </ul>
                                <?php if (is_user_logged_in()) {
                                    ?>
                                    <div class="careerfy-priceplan-twelve-btn">
                                        <a href="javascript:void(0);"
                                           class="<?php echo ($buy_btn_class) ?>"
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


                                        do_shortcode($pckg_features);

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
                    <?php } elseif ($view == 'view7') { ?>
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
                                            do_shortcode($pckg_features);
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
                    <?php } elseif ($view == 'view6') { ?>
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


                                            do_shortcode($pckg_features);

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
                                        do_shortcode($pckg_features);
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

                                            do_shortcode($pckg_features);

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

                                        do_shortcode($pckg_features);

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


                                        do_shortcode($pckg_features);
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
                    <?php } else {

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

                                if (!empty($pckg_features) || !empty($pkg_exfield_title)) { ?>
                                    <ul>
                                        <?php
                                        do_shortcode($pckg_features);
                                        ?>
                                    </ul>
                                <?php } ?>

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
        echo $html;
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        extract(shortcode_atts(array(
            'view' => '',
        ), $atts));

        $html = '';
        ob_start();
        if (class_exists('JobSearch_plugin')) {
            wp_enqueue_script('jobsearch-packages-scripts');

            if ($view == 'view7') {


                ?>
                <div class="careerfy-allpkg-comcon">
                    <div class="row">
                        <div class="careerfy-priceplan-wrapper">
                            <?php
                            foreach ($atts['careerfy_all_package_item'] as $info) {
                                $this->careerfy_all_package_item_shortcode($info);
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php } else { ?>

                <?php $html = '<div class="careerfy-allpkg-comcon">' . ($view == 'view3' ? '<div class="careerfy-priceplan-style5-wrap">' : ''); ?>
                <div class="row">
                    <?php foreach ($atts['careerfy_all_package_item'] as $info) {
                        $this->careerfy_all_package_item_shortcode($info);
                    } ?>
                </div>
                <?php ($view == 'view3' ? '</div>' : '</div>');
            }
        }
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    {

    }
}