<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

/**
 * @since 1.1.0
 */
class HelpLinks extends Widget_Base
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
        return 'help-link';
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
        return __('Help Link', 'careerfy-frame');
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
        return 'fa fa-link';
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
                'label' => __('Help Links Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'help_icon', [
                'label' => __('Icon', 'careerfy-frame'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'help_title', [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $repeater->add_control(
            'btn_txt', [
                'label' => __('Button Text', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $repeater->add_control(
            'btn_url', [
                'label' => __('Button URL', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'careerfy_help_links_item',
            [
                'label' => __('Help Links', 'careerfy-frame'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => 'Item',
            ]
        );
        $this->end_controls_section();
    }

    protected function careerfy_help_links_item_shortcode()
    {
        $atts = $this->get_settings_for_display();

        extract(shortcode_atts(array(
            'help_icon' => '',
            'help_title' => '',
            'btn_txt' => '',
            'btn_url' => '',
        ), $atts));

        foreach ($atts['careerfy_help_links_item'] as $info) { ?>
            <li class="col-md-4">
                <h2><?php echo($info['help_title']) ?></h2>
                <i class="<?php echo($info['help_icon']['value']) ?>"></i>
                <a href="<?php echo($info['btn_url']) ?>"><?php echo($info['btn_txt']) ?></a>
            </li>
        <?php } ?>
    <?php }

    protected function render()
    {
        ob_start();
        ?>
        <div class="contact-service">
            <ul class="row">
                <?php echo $this->careerfy_help_links_item_shortcode() ?>
            </ul>
        </div>
        <?php
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    {
    }
}