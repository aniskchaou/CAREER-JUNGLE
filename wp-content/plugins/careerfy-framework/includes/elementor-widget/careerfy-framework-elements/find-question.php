<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;


/**
 * @since 1.1.0
 */
class FindQuestion extends Widget_Base
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
        return 'find-question';
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
        return __('Find Question', 'careerfy-frame');
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
        return 'fa fa-question-circle';
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
                'label' => __('Find Question Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'search_box',
            [
                'label' => __('Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'show',
                'options' => [
                    'show' => __('Show', 'careerfy-frame'),
                    'hide' => __('Hide', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'search_title',
            [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'search_desc',
            [
                'label' => __('Description', 'careerfy-frame'),
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
        $search_box = $atts['search_box'];
        $search_title = $atts['search_title'];
        $search_desc = $atts['search_desc'];
        $btn_txt = $atts['btn_txt'];
        $btn_url = $atts['btn_url'];

        ob_start();
        if ($search_box == 'show') { ?>
            <div class="widget careerfy-search-form-widget">
                <form method="get" action="<?php echo home_url('/') ?>">
                    <label><?php esc_html_e("Find Your Question:", "careerfy-frame") ?></label>
                    <input placeholder="<?php esc_html_e("Keyword", "careerfy-frame") ?>" type="text" name="s">
                    <input type="submit" value="">
                    <i class="fa fa-search"></i>
                </form>
            </div>
        <?php } ?>

        <div class="widget widget-text-info">
            <h2 class="careerfy-slash-title"><?php echo($search_title) ?></h2>
            <p><?php echo($search_desc) ?></p>
            <a href="<?php echo($btn_url) ?>" class="careerfy-text-btn careerfy-bgcolor"><?php echo($btn_txt) ?></a>
        </div>

        <?php
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    {
        ?>
        <#
        var search_box = settings.search_box;
        var search_title = settings.search_title;
        var search_desc = settings.search_desc;
        var btn_txt = settings.btn_txt;
        var btn_url = settings.btn_url;
        #>

        <# if (search_box == 'show') { #>
        <div class="widget careerfy-search-form-widget">
            <form method="get" action="#">
                <label><?php esc_html_e("Find Your Question:", "careerfy-frame") ?></label>
                <input placeholder="<?php esc_html_e("Keyword", "careerfy-frame") ?>" type="text" name="s">
                <input type="submit" value="">
                <i class="fa fa-search"></i>
            </form>
        </div>
    <# } #>

    <div class="widget widget-text-info">
    <h2 class="careerfy-slash-title">{{{search_title}}}</h2>
    <p>{{{search_desc}}}</p>
    <a href="#"  class="careerfy-text-btn careerfy-bgcolor">{{{btn_txt}}}</a>
    </div>

<?php }
}