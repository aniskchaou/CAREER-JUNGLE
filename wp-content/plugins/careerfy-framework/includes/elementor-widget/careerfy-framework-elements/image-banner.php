<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

if (!defined('ABSPATH')) exit;


/**
 * @since 1.1.0
 */
class ImageBanner extends Widget_Base
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
        return 'image-banner';
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
        return __('Image Banner', 'careerfy-frame');
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
        return 'fa fa-picture-o';
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
                'label' => __('Image Banner Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'b_bgimg',
            [
                'label' => __('Background Image', 'careerfy-frame'),
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
            'b_title',
            [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'b_subtitle',
            [
                'label' => __('SubTitle', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'b_desc',
            [
                'label' => __('Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );

        $this->add_control(
            'btn1_txt',
            [
                'label' => __('Button 1 Text', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'btn1_url',
            [
                'label' => __('Button 1 URL', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'btn2_txt',
            [
                'label' => __('Button 2 Text', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'btn2_url',
            [
                'label' => __('Button 2 URL', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        extract(shortcode_atts(array(
            'b_bgimg' => '',
            'b_title' => '',
            'b_subtitle' => '',
            'b_desc' => '',
            'btn1_txt' => '',
            'btn1_url' => '',
            'btn2_txt' => '',
            'btn2_url' => '',
        ), $atts));

        $b_bgimg = !empty($b_bgimg) ? $b_bgimg['url'] : '';
        ob_start() ?>
        <div class="careerfy-banner-four careerfy-typo-wrap" <?php echo($b_bgimg != '' ? 'style="background-image: url(\'' . $b_bgimg . '\');"' : '') ?>>
            <div class="container">
                <div class="careerfy-bannerfour-caption">
                    <h1><span><?php echo($b_title) ?></span> <?php echo($b_subtitle) ?></h1>
                    <?php
                    if ($b_desc != '') {
                        echo '<p>' . $b_desc . '</p>';
                    }
                    if ($btn1_txt != '' || $btn2_txt != '') { ?>
                        <ul>
                            <?php
                            if ($btn1_txt != '') {
                                ?>
                                <li><a href="<?php echo($btn1_url) ?>" class="banner-four-btn"><?php echo($btn1_txt) ?>
                                        <i class="careerfy-icon careerfy-arrow-pointing-to-right"></i></a></li>
                                <?php
                            }
                            if ($btn1_txt != '' && $btn2_txt != '') { ?>
                                <li><?php esc_html_e("OR", "careerfy-frame") ?></li>
                                <?php
                            }
                            if ($btn2_txt != '') {
                                ?>
                                <li><a href="<?php echo($btn2_url) ?>"><?php echo($btn2_txt) ?></a></li>
                                <?php
                            }
                            ?>
                        </ul>
                        <?php
                    }
                    ?>
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
        var b_bgimg = settings.b_bgimg != '' ? settings.b_bgimg.url : '';
        var b_title = settings.b_title;
        var b_subtitle = settings.b_subtitle;
        var b_desc = settings.b_desc;
        var btn1_txt  = settings.btn1_txt ;
        var btn1_url = settings.btn1_url;
        var btn2_txt = settings.btn2_txt;
        var btn2_url = settings.btn2_url;
        #>
        <div class="careerfy-banner-four careerfy-typo-wrap" style="background-image: url({{{b_bgimg}}})">
            <div class="container">
                <div class="careerfy-bannerfour-caption">
                    <h1><span>{{{b_title}}}</span>{{{b_subtitle}}}</h1>
                    <# if(b_desc != ''){ #>
                    <p>{{{b_desc}}}</p>
                    <# } #>

                    <#  if(btn1_txt != '' || btn2_txt != ''){ #>
                    <ul>
                        <# if(btn1_txt != ''){  #>
                        <li><a href="#" class="banner-four-btn"><# {{{btn1_txt}}} #>
                                <i class="careerfy-icon careerfy-arrow-pointing-to-right"></i></a></li>
                        <# } #>

                        <# if (btn1_txt != '' && btn2_txt != '') { #>
                        <li>OR</li>
                        <# } #>
                        <# if(btn2_txt != ''){  #>
                        <li><a href="#"><# {{{btn2_txt}}} #></a></li>
                        <# } #>
                    </ul>
                    <# } #>
                </div>
            </div>
        </div>
    <?php }
}