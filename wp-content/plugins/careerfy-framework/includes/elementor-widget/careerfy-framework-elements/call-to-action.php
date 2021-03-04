<?php

namespace CareerfyElementor\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

if (!defined('ABSPATH')) exit;


/**
 * @since 1.1.0
 */
class CallToAction extends Widget_Base
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
        return 'careerfy-call-to-action';
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
        return __('Careerfy Call To Action', 'careerfy-frame');
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
        global $rand_num;
        $rand_num = rand(10000000, 99909999);
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Call to Action Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'view',
            [
                'label' => __('Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'view-1',
                'options' => [
                    'view-1' => __('Style 1', 'careerfy-frame'),
                    'view-2' => __('Style 2', 'careerfy-frame'),
                    'view-3' => __('Style 3', 'careerfy-frame'),
                    'view-4' => __('Style 4', 'careerfy-frame'),
                    'view-5' => __('Style 5', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'cta_img',
            [
                'label' => __('Image', 'careerfy-frame'),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'view' => array('view-1', 'view-2')
                ]

            ]
        );
        $this->add_control(
            'cta_title1',
            [
                'label' => __('Title 1', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,

            ]
        );
        $this->add_control(
            'cta_title2',
            [
                'label' => __('Title 2', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'view' => array('view-1', 'view-2', 'view-4')
                ]
            ]
        );

        $this->add_control(
            'cta_title_small',
            [
                'label' => __('Small Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'view' => 'view-3'
                ]
            ]
        );
        $this->add_control(
            'cta_desc',
            [
                'label' => __('Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
                'condition' => [
                    'view' => array('view-1', 'view-2', 'view-5')
                ]
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

        $this->add_control(
            'btn_txt_2',
            [
                'label' => __('Button Text 2', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'view' => 'view-5'
                ]
            ]
        );

        $this->add_control(
            'btn_url_2',
            [
                'label' => __('Button URL 2', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'view' => 'view-5'
                ]
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        extract(shortcode_atts(array(
            'view' => '',
            'cta_img' => '',
            'cta_title1' => '',
            'cta_title2' => '',
            'cta_title_small' => '',
            'cta_desc' => '',
            'btn_txt' => '',
            'btn_url' => '',
            'btn_txt_2' => '',
            'btn_url_2' => '',
        ), $atts));

        $cont_col = 'col-md-12';
        if ($cta_img != '') {
            $cont_col = 'col-md-6';
        }

        $cta_title_small = $cta_title_small != "" ? $cta_title_small : "";

        ob_start();
        if ($view == 'view-5') { ?>
            <div class="careerfy-sixteen-parallex careerfy-sixteen-parallex-two">
                <h2><?php echo $cta_title1 ?></h2>
                <span><?php echo $cta_desc ?></span>
                <a href="<?php echo $btn_url ?>"><?php echo $btn_txt ?></a>
                <a href="<?php echo $btn_url_2 ?>"><?php echo $btn_txt_2 ?></a>
            </div>
        <?php } else if ($view == 'view-4') { ?>
            <div class="careerfy-action-style11">
                <h2><?php echo $cta_title1 ?></h2>
                <p><?php echo $cta_title2 ?></p>
                <a href="<?php echo $btn_url ?>"><?php echo $btn_txt ?></a>
            </div>
        <?php } else if ($view == 'view-3') { ?>
            <div class="careerfy-build-action">
                <h2><?php echo $cta_title1 ?>
                    <small><?php echo $cta_title_small ?></small>
                </h2>
                <a href="<?php echo $btn_url ?>"><?php echo $btn_txt ?></a>
            </div>
        <?php } else { ?>
            <div class="row">
                <aside class="<?php echo($cont_col) ?> careerfy-typo-wrap">
                    <div class="careerfy-parallex-text <?php echo($view == 'view-2' ? 'careerfy-logo-text' : '') ?>">
                        <h2><?php echo($cta_title1) ?><?php echo($cta_title2 != '' ? '<br> ' . $cta_title2 : '') ?></h2>
                        <?php echo($cta_desc != '' ? '<p>' . $cta_desc . '</p>' : '') ?>
                        <?php echo($btn_txt != '' ? '<a href="' . $btn_url . '" class="careerfy-static-btn careerfy-bgcolor"><span>' . $btn_txt . '</span></a>' : '') ?>
                    </div>
                </aside>
                <?php
                if ($cta_img != '') { ?>
                    <aside class="col-md-6 careerfy-typo-wrap">
                        <div class="<?php echo($view == 'view-2' ? 'careerfy-logo-thumb' : 'careerfy-right') ?>">
                            <img src="<?php echo($cta_img['url']) ?>" alt="">
                        </div>
                    </aside>
                <?php } ?>
            </div>
        <?php }

        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    { ?>
        <#
        var h_title = settings.h_title;
        var btn_txt = settings.btn_txt;
        var view = settings.view;
        var cta_img = settings.cta_img != "" ? settings.cta_img.url : '' ;
        var cta_title1 = settings.cta_title1;
        var cta_title2 = settings.cta_title2;
        var cta_title_small = settings.cta_title_small;
        var cta_desc = settings.cta_desc;
        var btn_txt = settings.btn_txt;
        var btn_url = settings.btn_url;
        var btn_txt_2 = settings.btn_txt_2;
        var btn_url_2 = settings.btn_url_2;
        var cont_col = 'col-md-12';
        var logo_view = view == 'view-2' ? 'careerfy-logo-thumb' : 'careerfy-right';
        if (cta_img != '') {
        cont_col = 'col-md-6';
        }
        cta_title_small = cta_title_small != "" ? cta_title_small : "";
        if(view == 'view-2')
        {
        var logo_text = view == 'view-2' ? 'careerfy-logo-text' : '';
        }
        #>

        <# if (view == 'view-5') { #>
        <div class="careerfy-sixteen-parallex careerfy-sixteen-parallex-two">
            <h2>{{{cta_title1}}}</h2>
            <span>{{{cta_desc}}}</span>
            <a href="#">{{{btn_txt}}}</a>
            <a href="#">{{{btn_txt_2}}}</a>
        </div>
        <# } else if (view == 'view-4') { #>
        <div class="careerfy-action-style11">
            <h2>{{{cta_title1}}}</h2>
            <p>{{{cta_title2}}}</p>
            <a href="#">{{{btn_txt}}}</a></a>
        </div>
        <# } else if (view == 'view-3') { #>
        <div class="careerfy-build-action">
            <h2>{{{cta_title1}}}
                <small>{{{cta_title_small}}}</small>
            </h2>
            <a href="#">{{{btn_txt}}}</a>
        </div>
        <# } else {  #>
        <div class="row">
            <aside class="{{{cont_col}}} careerfy-typo-wrap">
                <div class="careerfy-parallex-text {{{logo_text}}}">
                    <# if(cta_title2 != ''){ #>
                    <h2>{{{cta_title1}}} <br>{{{cta_title2}}}</h2>
                    <# } else { #>
                    <h2>{{{cta_title1}}}</h2>
                    <# } #>
                    <# if(cta_desc !='') { #>

                    <p>{{{cta_desc}}}</p>
                    <# } #>
                    <# if(btn_txt !=''){ #>
                    <a href="#" class="careerfy-static-btn careerfy-bgcolor"><span>{{{btn_txt}}}</span></a>
                    <# } #>
                </div>
            </aside>

            <# if (cta_img != '') { #>
            <aside class="col-md-6 careerfy-typo-wrap">
                <div class="{{{logo_view}}}"><img src="{{{cta_img}}}" alt=""></div>
            </aside>
            <# } #>
        </div>
        <# } #>
        <?php
    }
}