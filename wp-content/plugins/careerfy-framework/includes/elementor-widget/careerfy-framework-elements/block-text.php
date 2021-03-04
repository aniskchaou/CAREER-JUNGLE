<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

if (!defined('ABSPATH')) exit;

/**
 * @since 1.1.0
 */
class BlockTextWithVideo extends Widget_Base
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
        return 'block-text';
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
        return __('Block Text with Video', 'careerfy-frame');
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
                'label' => __('Block Text Settings', 'careerfy-frame'),
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
                'label' => __('Content', 'careerfy-frame'),
                'type' => Controls_Manager::WYSIWYG,
                'default' => '',
            ]
        );


        $this->add_control(
            'bg_img',
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
            'bg_color',
            [
                'label' => __('Background Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
            ]
        );

        $this->add_control(
            'btn_txt',
            [
                'label' => __('Button Text', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'btn_url',
            [
                'label' => __('Button URL', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'video_section',
            [
                'label' => __('Video Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'video_url',
            [
                'label' => __('Video URL', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'poster_img',
            [
                'label' => __('Video Poster Image', 'careerfy-frame'),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();

        extract(shortcode_atts(array(
            'title' => '',
            'bg_img' => '',
            'bg_color' => '',
            'btn_txt' => '',
            'btn_url' => '',
            'video_url' => '',
            'poster_img' => '',
            'content' => '',
        ), $atts));

        ob_start();

        $style_str = '';
        if ($bg_img != '') {
            $style_str .= 'background-image: url(' . $bg_img['url'] . ');';
        }
        if ($bg_color != '') {
            $style_str .= 'background-color: ' . $bg_color . ';';
        }
        ?>
        <div class="row">
            <div class="col-md-6 careerfy-parallex-box" <?php echo($style_str != '' ? 'style="' . $style_str . '"' : '') ?>>
                <div class="careerfy-parallex-box-wrap">
                    <h2><?php echo($title) ?></h2>

                    <?php if ($content != '') { ?>
                        <p><?php echo($content) ?></p>
                        <?php
                    }
                    if ($btn_txt != '') { ?>
                        <a href="<?php echo($btn_url) ?>" class="careerfy-parallex-box-btn"><?php echo($btn_txt) ?></a>
                    <?php } ?>
                </div>
            </div>
            <?php if ($video_url != '') {
                wp_enqueue_script('careerfy-mediaelement');
                ?>
                <div class="col-md-6 careerfy-media-player">
                    <video src="<?php echo($video_url) ?>"
                           poster="<?php echo($poster_img != "" ? $poster_img['url'] : '') ?>" controls="controls"
                           preload=""></video>
                </div>
                <script>
                    jQuery(document).ready(function () {
                        jQuery('video').mediaelementplayer({
                            success: function (player, node) {
                                jQuery('#' + node.id + '-mode').html('mode: ' + player.pluginType);
                            }
                        });
                    });
                </script>
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
        var bg_img = settings.bg_img != '' ? settings.bg_img.url : '';
        var bg_color = settings.bg_color;
        var poster_img = settings.poster_img != '' ? settings.poster_img.url : '';
        var btn_txt = settings.btn_txt;
        var btn_url = settings.btn_url;
        var video_url = settings.video_url;
        var content = settings.content;
        #>

        <div class="row">
            <div class="col-md-6 careerfy-parallex-box"
                 style="background-image: url({{{bg_img}}}); background-color: {{{bg_color}}}">
                <div class="careerfy-parallex-box-wrap">
                    <h2>{{{title}}}</h2>
                    <# if(content !=''){ #>
                    <p>{{{content}}}</p>
                    <# } #>

                    <# if (btn_txt != '') {#>
                    <a href="#" class="careerfy-parallex-box-btn">{{{btn_txt}}}</a>
                    <# } #>

                </div>
            </div>
            <# if (video_url != '') { #>
            <?php wp_enqueue_script('careerfy-mediaelement'); ?>
            <div class="col-md-6 careerfy-media-player">
                <video src="{{{video_url}}}" poster="{{{poster_img}}}" controls="controls" preload=""></video>
            </div>

            <# } #>
        </div>

    <?php }
}