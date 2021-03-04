<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
if (!defined('ABSPATH')) exit;


/**
 * @since 1.1.0
 */
class AppPromo extends Widget_Base
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
        return 'app-promo';
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
        return __('App Promo', 'careerfy-frame');
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
        return 'fa fa-tag';
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
        return ['careerfy'];
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

        $this->start_controls_section(
            'content_section',
            [
                'label' => __('App Promo Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'app_promo_view',
            [
                'label' => __('Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'view1',
                'options' => [
                    'view1' => __('Style 1', 'careerfy-frame'),
                    'view2' => __('Style 2', 'careerfy-frame'),
                    'view3' => __('Style 3', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'h_title',
            [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );


        $this->add_control(
            'hc_title_clr',
            [
                'label' => __('Choose Title Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'description' => __("This Color will apply to 'Color Title'.", "careerfy-frame"),
            ]
        );
        $this->add_control(
            'h_desc',
            [
                'label' => __('Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );

        $this->add_control(
            'desc_clr',
            [
                'label' => __('Choose Description Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
            ]
        );
        $this->add_control(
            'link_text',
            [
                'label' => __('Link Text', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'app_promo_view' => 'view2',
                ]
            ]
        );

        $this->add_control(
            'link_text_url',
            [
                'label' => __('Link Text URL', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'app_promo_view' => 'view2',
                ],
            ]
        );

        $this->add_control(
            'careerfy_browse_img_1',
            [
                'label' => __('First Image', 'careerfy-frame'),
                'type' => Controls_Manager::MEDIA,
            ]
        );

        $this->add_control(
            'first_img_link',
            [
                'label' => __('First Image Links URL', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'careerfy_browse_img_2',
            [
                'label' => __('Second Image', 'careerfy-frame'),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'second_img_link',
            [
                'label' => __('Second Image Links URL', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'careerfy_browse_img_3',
            [
                'label' => __('Third Image', 'careerfy-frame'),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'app_promo_view' => 'view2',
                ],
            ]
        );

        $this->add_control(
            'third_img_link',
            [
                'label' => __('Third Image Links URL', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'app_promo_view' => 'view2',
                ],
            ]
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'feat_name', [
                'label' => __('Feature Detail', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'pckg_features',
            [
                'label' => __('Add Feature Package item', 'careerfy-frame'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ feat_name }}}',
                'condition' => [
                    'app_promo_view' => 'view3',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();

        extract(shortcode_atts(array(
            'h_title' => '',
            'hc_title_clr' => '',
            'h_desc' => '',
            'desc_clr' => '',
            'link_text' => '',
            'link_text_url' => '',
            'careerfy_browse_img_1' => '',
            'first_img_link' => '',
            'careerfy_browse_img_2' => '',
            'second_img_link' => '',
            'careerfy_browse_img_3' => '',
            'third_img_link' => '',
            'careerfy_browse_img_4' => '',
            'fourth_img_link' => '',
            'pckg_features' => '',
            'app_promo_view' => 'view1'
        ), $atts));

        ob_start();
        $h_title = $h_title != '' ? $h_title : "";
        $hc_title_clr = $hc_title_clr != "" ? 'style="color: ' . $hc_title_clr . '"' : "";
        $h_desc = $h_desc != '' ? $h_desc : "";
        $link_text = $link_text != '' ? $link_text : "";
        $link_text_url = $link_text_url != '' ? $link_text_url : "#";
        $desc_clr = $desc_clr != "" ? 'style="color: ' . $desc_clr . '"' : "";
        $careerfy_browse_img_1 = $careerfy_browse_img_1 != "" ? '<img src="' . $careerfy_browse_img_1['url'] . '" >' : "";
        $first_img_link = $first_img_link != '' ? $first_img_link : "#";
        $careerfy_browse_img_2 = $careerfy_browse_img_2 != "" ? '<img src="' . $careerfy_browse_img_2['url'] . '" >' : "";
        $second_img_link = $second_img_link != '' ? $second_img_link : "#";
        $careerfy_browse_img_3 = $careerfy_browse_img_3 != "" ? '<img src="' . $careerfy_browse_img_3['url'] . '" >' : "";
        $third_img_link = $third_img_link != '' ? $third_img_link : "#";

        if ($app_promo_view == 'view3') { ?>
            <div class="careerfy-searchjob-text">
                <h2 <?php echo $hc_title_clr ?>><?php echo $h_title ?></h2>
                <?php
                if (!empty($pckg_features)) { ?>
                    <ul>
                        <?php
                        foreach ($pckg_features as $pckg_feature) {
                            $pckg_feat_name = isset($pckg_feature['feat_name']) ? $pckg_feature['feat_name'] : '';
                            ?>
                            <li><img src="<?php echo get_template_directory_uri() ?>/images/search-text-icon.png"
                                     alt=""><?php echo($pckg_feat_name) ?></li>
                        <?php } ?>
                    </ul>
                <?php } ?>
                <span <?php echo $desc_clr ?>><?php echo $h_desc ?></span>
                <div class="careerfy-featured-rating"><span class="careerfy-featured-rating-box"
                                                            style="width: 100%;"></span></div>
                <strong><?php echo esc_html__('Download the job app now', 'careerfy-frame') ?></strong>
                <a href="<?php echo $first_img_link ?>"
                   class="careerfy-searchjob-app"><?php echo $careerfy_browse_img_1 ?></a>
                <a href="<?php echo $second_img_link ?>"
                   class="careerfy-searchjob-app"><?php echo $careerfy_browse_img_2 ?></a>
            </div>
        <?php } else if ($app_promo_view == 'view1') { ?>

            <div class="careerfy-getapp">
                <h2 <?php echo $hc_title_clr ?>><?php echo $h_title ?></h2>
                <p <?php echo $desc_clr ?>><?php echo $h_desc ?></p>
                <div class="clearfix"></div>
                <a href="<?php echo $first_img_link ?>"
                   class="careerfy-getapp-btn"><?php echo $careerfy_browse_img_1 ?></a>

                <a href="<?php echo $second_img_link ?>"
                   class="careerfy-getapp-btn"><?php echo $careerfy_browse_img_2 ?></a>
            </div>

        <?php } else { ?>
            <div class="careerfy-autojobs-mobile-text">
                <h2 <?php echo $hc_title_clr ?>><?php echo $h_title ?></h2>
                <p <?php echo $desc_clr ?>><?php echo $h_desc ?></p>
                <a <?php echo $hc_title_clr ?> href="<?php echo $link_text_url ?>"
                                               class="careerfy-autojobs-mobile-btn"><?php echo $link_text ?></a>
                <div class="clearfix"></div>
                <a href="<?php echo $first_img_link ?>"
                   class="careerfy-autojobs-mobile-thumb"><?php echo $careerfy_browse_img_1 ?></a>
                <a href="<?php echo $second_img_link ?>"
                   class="careerfy-autojobs-mobile-thumb"><?php echo $careerfy_browse_img_2 ?></a>
                <a href="<?php echo $third_img_link ?>"
                   class="careerfy-autojobs-mobile-thumb"><?php echo $careerfy_browse_img_3 ?></a>
            </div>
        <?php }

        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    {

    }
}