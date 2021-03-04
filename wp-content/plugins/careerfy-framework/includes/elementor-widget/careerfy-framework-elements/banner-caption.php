<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;


/**
 * @since 1.1.0
 */
class BannerCaption extends Widget_Base
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
        return 'banner-caption';
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
        return __('Banner Caption', 'careerfy-frame');
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
        return 'fa fa-flag';
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
        $rand_num = rand(10000000, 99909999);
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Banner Caption Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'banner_title',
            [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'banner_desc',
            [
                'label' => __('Description', 'careerfy-frame'),
                'type' => Controls_Manager::WYSIWYG,
            ]
        );
        $this->add_control(
            'btn_heading',
            [
                'label' => __('Button Heading', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'btn_txt', [
                'label' => __('Button Text', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );
        $repeater->add_control(
            'btn_link', [
                'label' => __('Button Link', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );
        $this->add_control(
            'btn_item',
            [
                'label' => __('Repeater List', 'eyecix-elementor'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'btn_txt' => __('Button Text', 'eyecix-elementor'),
                    ]
                ],
                'title_field' => '{{{ btn_txt }}}',
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        $banner_title = $atts['banner_title'];
        $banner_desc = $atts['banner_desc'];
        $btn_heading = $atts['btn_heading'];
        ob_start(); ?>

        <div class="careerfy-thirteen-banner-caption">
            <div class="container">
                <?php if (!empty($banner_title)) { ?>
                    <h1><?php echo $banner_title ?></h1>
                <?php } ?>
                <br>
                <?php if (!empty($banner_desc)) { ?>
                    <div class="banner-desc">
                        <?php echo $banner_desc ?>
                    </div>
                <?php } ?>
                <?php if (!empty($btn_heading)) { ?>
                    <h2><?php echo $btn_heading ?></h2>
                <?php } ?>
                <div class="clearfix"></div>
                <div class="careerfy-thirteen-banner-btn">
                    <?php
                    if (!empty($atts['btn_item']) || !empty($atts['btn_item'])) {
                        foreach ($atts['btn_item'] as $banner_btns) { ?>
                            <a href="<?php echo $banner_btns['btn_link'] ?>"><?php echo($banner_btns['btn_txt']) ?></a>
                        <?php }
                    } ?>
                </div>
            </div>
        </div>

        <?php
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    { ?>
        <#
        var banner_title = settings.banner_title;
        var banner_desc = settings.banner_desc;
        var btn_heading = settings.btn_heading;
        #>
        <div class="careerfy-thirteen-banner-caption">
            <div class="container">
                <# if (banner_title != "") { #>
                <h1><# banner_title #></h1>
                <# } #>
                <br>
                <# if (banner_desc != "") { #>
                <div class="banner-desc">
                    <# banner_desc #>
                </div>
                <# } #>
                <# if (btn_heading != "") { #>
                <h2><# $btn_heading #></h2>
                <# } #>
                <div class="clearfix"></div>
                <div class="careerfy-thirteen-banner-btn">

                    <#
                    if (settings.btn_item !="") {
                    _.each(settings.btn_item, function(item,index){ #>
                    <a href="{{{item.btn_link}}}">{{{item.btn_txt}}}</a>
                    <# }); #>
                    <# } #>
                </div>
            </div>
        </div>
    <?php }
}