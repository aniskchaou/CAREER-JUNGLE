<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

if (!defined('ABSPATH')) exit;


/**
 * @since 1.1.0
 */
class AboutCompany extends Widget_Base
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
        return 'about-company';
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
        return __('About Company', 'careerfy-frame');
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
        return 'fa fa-address-card';
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
                'label' => __('About Company Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'ab_view',
            [
                'label' => __('Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'view1',
                'options' => [
                    'view1' => __('Style 1', 'careerfy-frame'),
                    'view2' => __('Style 2', 'careerfy-frame'),
                ],
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
            'bold_txt',
            [
                'label' => __('Bold Text', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'content',
            [
                'label' => __('About Text', 'careerfy-frame'),
                'type' => Controls_Manager::WYSIWYG,
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
            'about_img',
            [
                'label' => __('Choose Image', 'elementor'),
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
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();

        $ab_view = $atts['ab_view'];
        $title = $atts['title'];
        $bold_txt = $atts['bold_txt'];
        $about_img = $atts['about_img'] != "" ? $atts['about_img']['url'] : '';
        $title_color = $atts['title_color'];
        $desc_color = $atts['desc_color'];
        $btn_txt = $atts['btn_txt'];
        $btn_url = $atts['btn_url'];
        $content = $atts['content'];

        $about_param = apply_filters('careerfy_about_company_fields_param', $atts);

        ob_start();
        if ($ab_view == 'view2') {
            $cont_col = 'col-md-12';
            if ($about_img != '') {
                $cont_col = 'col-md-7';
            }
            ?>
            <div class="row">
                <div class="<?php echo($cont_col) ?> careerfy-parallax-style4">
                    <?php
                    if ($title != '') {
                        ?>
                        <span <?php echo($title_color != '' ? 'style="color: ' . $title_color . ';"' : '') ?>><?php echo($title) ?></span>
                        <?php
                    }
                    if ($bold_txt != '') { ?>
                        <h2 <?php echo($title_color != '' ? 'style="color: ' . $title_color . ';"' : '') ?>><?php echo($bold_txt) ?></h2>
                    <?php } ?>
                    <p <?php echo($desc_color != '' ? 'style="color: ' . $desc_color . ';"' : '') ?>><?php echo($content) ?></p>
                    <?php
                    if ($btn_txt != '') { ?>
                        <a href="<?php echo($btn_url) ?>"
                           class="careerfy-parallax-style4-btn"><?php echo($btn_txt) ?></a>
                    <?php } ?>
                </div>
                <?php
                if ($about_img != '') { ?>
                    <div class="col-md-5"><img src="<?php echo($about_img) ?>" alt=""></div>
                <?php } ?>
            </div>
        <?php } else {
            $cont_col = 'col-md-12';
            if ($about_img != '') {
                $cont_col = 'col-md-6';
            } ?>
            <div class="row">
                <div class="<?php echo($cont_col) ?> careerfy-typo-wrap">
                    <div class="careerfy-about-text">
                        <?php
                        if ($title != '') { ?>
                            <h2 <?php echo($title_color != '' ? 'style="color: ' . $title_color . ';"' : '') ?>><?php echo($title) ?></h2>
                        <?php }
                        if ($bold_txt != '') { ?>
                            <span class="careerfy-about-sub" <?php echo($title_color != '' ? 'style="color: ' . $title_color . ';"' : '') ?>><?php echo($bold_txt) ?></span>
                        <?php } ?>
                        <p <?php echo($desc_color != '' ? 'style="color: ' . $desc_color . ';"' : '') ?>><?php echo($content) ?></p>
                        <?php
                        if ($btn_txt != '') { ?>
                            <a href="<?php echo($btn_url) ?>"
                               class="careerfy-static-btn careerfy-bgcolor"><span><?php echo($btn_txt) ?></span></a>
                        <?php }
                        do_action('careerfy_about_company_extra_button', $atts);
                        ?>
                    </div>
                </div>
                <?php if ($about_img != '') { ?>
                    <div class="col-md-6 careerfy-typo-wrap">
                        <div class="careerfy-about-thumb"><img src="<?php echo($about_img) ?>" alt=""></div>
                    </div>
                    <!--ends here-->
                <?php } ?>
            </div>
            <?php
        }
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    { ?>
        <#
        var ab_view = settings.ab_view;
        var title = settings.title;
        var bold_txt = settings.bold_txt;
        var about_img = settings.about_img != '' ? settings.about_img.url : '';
        var title_color = settings.title_color;
        var desc_color = settings.desc_color;
        var btn_txt = settings.btn_txt;
        var btn_url = settings.btn_url;
        var content = settings.content;
        var cont_col = ''; #>

        <# if (ab_view == 'view2') {
        cont_col = 'col-md-12';

        if (about_img != '') {
        cont_col = 'col-md-7';
        }
        #>
        <div class="row">
            <div class="{{{cont_col}}}  careerfy-parallax-style4">
                <# if (title != '') {
                if(title_color != ''){
                #>
                <span style="color: {{{title_color}}}">{{{title}}}</span>
                <# } else { #>
                <span>{{{title}}}</span>
                <# } #>
                <# } #>

                <# if (bold_txt != '') { #>
                <h2 style="color: {{{title_color}}}">{{{bold_txt}}}</h2>
                <# } else { #>
                <h2>{{{bold_txt}}}</h2>
                <# } #>

                <# if(desc_color !=''){ #>
                <p style="color: {{{desc_color}}}">{{{content}}}</p>
                <# } else { #>
                <p>{{{content}}}</p>
                <# } #>

                <# if(btn_txt !=''){ #>
                <a style="color: {{{desc_color}}}" class="careerfy-parallax-style4-btn">{{{btn_txt}}}</a>
                <# } else { #>
                <a>{{{btn_txt}}}</a>
                <# } #>

            </div>

            <# if(about_img != ''){ #>
            <div class="col-md-5"><img src="{{{about_img}}}" alt=""></div>
            <# } #>

        </div>
        <# } else {

        cont_col = 'col-md-12';
        if (about_img != '') {
        cont_col = 'col-md-6';
        }

        #>
        <div class="row">
            <div class="{{{cont_col}}} careerfy-typo-wrap">
                <div class="careerfy-about-text">
                    <# if (title != '') {
                    if(title_color != ''){
                    #>
                    <h2 style="color: {{{title_color}}}">{{{title}}}</h2>
                    <# } else { #>
                    <h2>{{{title}}}</h2>
                    <# } #>
                    <# } #>

                    <# if (bold_txt != '') { #>
                    <span style="color: {{{title_color}}}">{{{bold_txt}}}</span>
                    <# } else { #>
                    <span>{{{bold_txt}}}</span>
                    <# } #>

                    <# if(desc_color !=''){ #>
                    <p style="color: {{{desc_color}}}">{{{content}}}</p>
                    <# } else { #>
                    <p>{{{content}}}</p>
                    <# } #>

                    <# if(btn_txt !=''){ #>
                    <a style="color: {{{desc_color}}}" class="careerfy-static-btn careerfy-bgcolor">{{{btn_txt}}}</a>
                    <# } else { #>
                    <a>{{{btn_txt}}}</a>
                    <# } #>
                </div>
            </div>
            <# if(about_img != ''){ #>
            <div class="col-md-6 careerfy-typo-wrap">
                <div class="careerfy-about-thumb"><img src="{{{about_img}}}" alt=""></div>
            </div>
            <# } #>
        </div>
        <# } #>
    <?php }
}