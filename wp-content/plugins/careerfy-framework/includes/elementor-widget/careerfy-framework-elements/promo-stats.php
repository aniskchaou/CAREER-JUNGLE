<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;


if (!defined('ABSPATH')) exit;


/**
 * @since 1.1.0
 */
class PromoStats extends Widget_Base
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
        return 'promo-stats';
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
        return __('Promo Statistics', 'careerfy-frame');
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
                'label' => __('Promo Statistics Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'promo_title_1',
            [
                'label' => __('Title 1', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'promo_desc_1',
            [
                'label' => __('Description 1', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => '',
            ]
        );


        $this->add_control(
            'promo_title_2',
            [
                'label' => __('Title 2', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'promo_desc_2',
            [
                'label' => __('Description 2', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );

        $this->add_control(
            'promo_ranking',
            [
                'label' => __('Rating', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'description' => esc_html__('Enter the rating in numbers', 'careerfy-frame'),
                'default' => '1.0'
            ]
        );
        $this->add_control(
            'promo_rating_desc',
            [
                'label' => __('Rating Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );

        $this->end_controls_section();

        /*
         * Easy Apply text styles
         * */
        $this->start_controls_section(
            'section_styles',
            [
                'label' => __('Promo Statistics Styles', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_button_style');

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => __('Title', 'wp-jobsearch'),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => ' {{WRAPPER}} .careerfy-rating-list .careerfy-rating-list-count, {{WRAPPER}} .careerfy-rating-list strong',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Text Color', 'wp-jobsearch'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .careerfy-rating-list .careerfy-rating-list-count, {{WRAPPER}} .careerfy-rating-list strong' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow',
                'selector' => ' {{WRAPPER}} .careerfy-rating-list .careerfy-rating-list-count, {{WRAPPER}} .careerfy-rating-list strong',
            ]
        );





        $this->end_controls_tab();

        /*
         * Apply button hover text styles
         * */
        $this->start_controls_tab(
            'desc_button_hover',
            [
                'label' => __('Description', 'wp-jobsearch'),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'desc_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .careerfy-rating-list small',
            ]
        );

        $this->add_control(
            'desc_color',
            [
                'label' => __('Text Color', 'wp-jobsearch'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .careerfy-rating-list small' => 'color: {{VALUE}}  ',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'desc_shadow',
                'selector' => '{{WRAPPER}} .careerfy-rating-list small',
            ]
        );

        $this->end_controls_tab();


        $this->end_controls_section();
        /*
         * Easy Apply styles section end
         * */

    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();

        extract(shortcode_atts(array(
            'promo_title_1' => '',
            'promo_desc_1' => '',
            'promo_title_2' => '',
            'promo_desc_2' => '',
            'promo_ranking' => '',
            'promo_rating_desc' => '',
        ), $atts));

        $promo_ranking = $promo_ranking != "" ? $promo_ranking : "";
        if ($promo_ranking > 5) {
            $promo_ranking = 5;
        }
        $rating = $promo_ranking * 20;
        ob_start(); ?>
        <div class="careerfy-rating-list">
            <ul class="row">
                <li class="col-md-4">
                    <span class="careerfy-rating-list-count"><?php echo $promo_title_1 ?></span>
                    <small><?php echo $promo_desc_1 ?></small>
                </li>
                <li class="col-md-4">
                    <span class="careerfy-rating-list-count"><?php echo $promo_title_2 ?></span>
                    <small><?php echo $promo_desc_2 ?></small>
                </li>
                <li class="col-md-4">
                    <strong><?php echo $promo_ranking ?></strong>
                    <div class="careerfy-featured-rating"><span class="careerfy-featured-rating-box" style="width: <?php echo $rating ?>%"></span></div>
                    <small><?php echo $promo_rating_desc ?></small>
                </li>
            </ul>
        </div>
        <?php
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    { ?>
        <# var promo_ranking = settings.promo_ranking
        if(promo_ranking > 5){
        promo_ranking = 5;
        }
        var rating = promo_ranking * 20;
        #>
        <div class="careerfy-rating-list">
            <ul class="row">
                <li class="col-md-4">
                    <span class="careerfy-rating-list-count">{{{settings.promo_title_1}}}</span>
                    <small>{{{settings.promo_desc_1}}}</small>
                </li>
                <li class="col-md-4">
                    <span class="careerfy-rating-list-count">{{{settings.promo_title_2}}}</span>
                    <small>{{{settings.promo_desc_2}}}</small>
                </li>
                <li class="col-md-4">
                    <strong>{{{settings.promo_ranking}}}</strong>
                    <div class="careerfy-featured-rating"><span class="careerfy-featured-rating-box"
                                                                style="width: {{{rating}}}%"></span></div>
                    <small>{{{ settings.promo_rating_desc }}}</small>
                </li>
            </ul>
        </div>
    <?php }
}