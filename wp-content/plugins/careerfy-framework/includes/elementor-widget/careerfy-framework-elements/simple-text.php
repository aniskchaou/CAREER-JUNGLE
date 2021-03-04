<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;


/**
 * @since 1.1.0
 */
class SimpleText extends Widget_Base
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
        return 'simple-block-text';
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
        return __('Simple Block Text', 'careerfy-frame');
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
        return 'fa fa-tasks';
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
                'label' => __('Simple Block Text Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'title',
            [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'content',
            [
                'label' => __('Content Text', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => '',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Title Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
            ]
        );

        $this->add_control(
            'desc_color',
            [
                'label' => __('Content Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
            ]
        );

        $this->add_control(
            'btn_txt',
            [
                'label' => __('Button Text 1', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'btn_url',
            [
                'label' => __('Button URL 1', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'btn2_txt',
            [
                'label' => __('Button Text 2', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'btn2_url',
            [
                'label' => __('Button URL 2', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        $title = $atts['title'];
        $title_color = $atts['title_color'];
        $desc_color = $atts['desc_color'];
        $btn_txt = $atts['btn_txt'];
        $btn_url = $atts['btn_url'];
        $btn2_txt = $atts['btn2_txt'];
        $btn2_url = $atts['btn2_url'];
        $content = $atts['content'];
        ob_start();

        ?>
        <div class="careerfy-parallax-text-box">
            <h2 <?php echo($title_color != '' ? ' style="color: ' . $title_color . ';"' : '') ?>><?php echo($title) ?></h2>
            <?php
            if ($content != '') { ?>
                <p <?php echo($desc_color != '' ? ' style="color: ' . $desc_color . ';"' : '') ?>><?php echo($content) ?></p>
                <?php
            }
            if ($btn_txt != '') { ?>
                <a href="<?php echo($btn_url) ?>" class="careerfy-parallax-text-btn"><?php echo($btn_txt) ?></a>
                <?php
            }
            if ($btn2_txt != '') { ?>
                <a href="<?php echo($btn2_url) ?>" class="careerfy-parallax-text-btn"><?php echo($btn2_txt) ?></a>
                <?php } ?>
        </div>

        <?php
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    { ?>
        <#
        var title = settings.title;
        var title_color = settings.title_color;
        var desc_color = settings.desc_color;
        var btn_txt = settings.btn_txt;
        var btn_url = settings.btn_url;
        var btn2_txt = settings.btn2_txt;
        var btn2_url = settings.btn2_url;
        var content = settings.content;
        #>
        <div class="careerfy-parallax-text-box">
            <h2 style="color: {{{title_color}}}">{{{title}}}</h2>

            <# if(content !=""){ #>
            <p style="color: {{{desc_color}}}">{{{content}}}</p>
            <# } #>

            <# if(btn_txt !=""){ #>
            <a href="{{{btn_url}}}" class="careerfy-parallax-text-btn">{{{btn_txt}}}</a>
            <# } #>

            <# if(btn2_txt !=""){ #>
            <a href="{{{btn2_url}}}" class="careerfy-parallax-text-btn">{{{btn2_txt}}}</a>
            <# } #>

        </div>

    <?php }
}