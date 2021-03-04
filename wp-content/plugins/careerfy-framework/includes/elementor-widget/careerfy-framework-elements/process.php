<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

/**
 * @since 1.1.0
 */
class Process extends Widget_Base
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
        return 'process';
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
        return __('Process', 'careerfy-frame');
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
        return 'fa fa-gear';
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
                'label' => __('Process Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'icon_color', [
                'label' => __('Icon Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                "description" => __("Select colors for Icons.", "careerfy-frame"),
            ]
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'process_icon', [
                'label' => __('Icon', 'careerfy-frame'),
                'type' => Controls_Manager::ICONS,
            ]
        );
        $repeater->add_control(
            'process_title', [
                'label' => __('Process Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $repeater->add_control(
            'process_desc', [
                'label' => __('Process Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );

        $this->add_control(
            'careerfy_process_item',
            [
                'label' => __('Add Process item', 'careerfy-frame'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ process_title }}}',
            ]
        );
        $this->end_controls_section();
    }

    protected function careerfy_process_item_shortcode()
    {
        $atts = $this->get_settings_for_display();
        extract(shortcode_atts(array(
            'process_icon' => '',
            'icon_color' => '',
            'process_title' => '',
            'process_desc' => '',
        ), $atts));

        foreach ($atts['careerfy_process_item'] as $key => $info) {

            $process_icon = $info['process_icon'] != "" ? '<i class="' . $info['process_icon']['value'] . '" ></i>' : '';
            $html = '<li class="col-md-4">
               ' . $process_icon . '
               <h2>' . $info['process_title'] . '</h2>
               <p>' . $info['process_desc'] . '</p>
            </li>';
            echo $html;
        }
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        $icon_color = $atts['icon_color'];
        $process_class = 'careerfy-services-nineview';
        ob_start();
        ?>

        <div class="<?php echo $process_class ?>">
            <ul class="row">
                <?php echo $this->careerfy_process_item_shortcode() ?>
            </ul>
        </div>
        <style>
            .careerfy-services-nineview i {
                color: <?php echo $icon_color ?>;
            }
        </style>
        <?php
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    {
    }
}