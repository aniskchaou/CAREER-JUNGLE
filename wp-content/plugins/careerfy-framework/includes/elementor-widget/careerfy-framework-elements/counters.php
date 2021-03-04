<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;


/**
 * @since 1.1.0
 */
class Counters extends Widget_Base
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
        return 'counters';
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
        return __('Counters', 'careerfy-frame');
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
        return 'fa fa-list-ol';
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
                'label' => __('Counters Settings', 'careerfy-frame'),
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
                    'view-6' => __('Style 6', 'careerfy-frame'),
                    'view-7' => __('Style 7', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'counter_icon_color',
            [
                'label' => __('Icon Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .careerfy-counter-main-wrap i ' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'counter_number_color',
            [
                'label' => __('Number Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,

            ]
        );
        $this->add_control(
            'counter_title_color',
            [
                'label' => __('Title Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
            ]
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'count_icon', [
                'label' => __('Icon', 'careerfy-frame'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'count_number', [
                'label' => __('Number', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'count_title', [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'careerfy_counters_item',
            [
                'label' => __('Add Counters', 'careerfy-frame'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ count_title }}}',
            ]
        );
        $this->end_controls_section();
    }

    protected function careerfy_counters_item_shortcode()
    {
        $atts = $this->get_settings_for_display();
        global $view, $counter_icon_color, $counter_number_color, $counter_title_color;

        $count_icon_colr = $counter_icon_color != '' ? "style=color:$counter_icon_color" : '';
        $count_nmbr_colr = $counter_number_color != '' ? "style=color:$counter_number_color" : '';
        $count_title_colr = $counter_title_color != '' ? "style=color:$counter_title_color" : '';

        $counter_class = 'col-md-4';
        if ($view == 'view-3' or $view == 'view-4' or $view == 'view-5' or $view == 'view-6' or $view == 'view-7') {
            $counter_class = 'col-md-3';
        }
        foreach ($atts['careerfy_counters_item'] as $info) {
            $count_icon = isset($info['count_icon']) && !empty($info['count_icon']['value']) ? $info['count_icon']['value'] : '';
            $count_number = isset($info['count_number']) && !empty($info['count_number']) ? $info['count_number'] : '';
            $count_title = isset($info['count_title']) && !empty($info['count_title']) ? $info['count_title'] : '';

            if ($view == 'view-7') {

                $html = '<li class="' . $counter_class . '">
        ' . ($count_icon != '' ? '<i  class="' . $count_icon . '"></i>' : '') . '
                   <h2 ' . $count_title_colr . '>' . $count_title . '</h2>
                   <span ' . $count_nmbr_colr . ' class="word-counter">' . ($count_number) . '</span>
                </' . ($count_number) . '>';

            } else if ($view == 'view-6') {

                $html = '<li class="' . $counter_class . '">
                   <i class="' . $count_icon . '" ></i>
                   <h2 ' . $count_title_colr . '>' . $count_title . '</h2>
                   <span ' . $count_nmbr_colr . ' class="word-counter">' . ($count_number) . '</span>
                 </li>';

            } else if ($view == 'view-5') {
                $html = '<li class="' . $counter_class . '">
                ' . ($count_icon != '' ? '<i  class="' . $count_icon . '"></i>' : '') . '
                     <span ' . $count_title_colr . '>' . $count_title . '</span>
                     <small ' . $count_nmbr_colr . ' class="word-counter">' . ($count_number) . '</small>
                 </li>';
            } else if ($view == 'view-4') {
                $html = '<li class="' . $counter_class . '">
                 <span ' . $count_nmbr_colr . ' class="word-counter">' . ($count_number) . '</span>
                  <small ' . $count_title_colr . '>' . $count_title . '</small>
                </li>';
            } else {
                $html = '
    <li class="' . $counter_class . '">
        ' . ($count_icon != '' ? '<i  class="' . $count_icon . ' careerfy-color"></i>' : '') . '
        <span ' . $count_nmbr_colr . ' class="word-counter">' . ($count_number) . '</span>
        <small ' . $count_title_colr . '>' . $count_title . '</small>
    </li>';
            }
            echo $html;
        }

    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        global $view, $counter_icon_color, $counter_number_color, $counter_title_color;
        extract(shortcode_atts(array(
            'view' => '',
            'counter_icon_color' => '',
            'counter_number_color' => '',
            'counter_title_color' => '',
        ), $atts));

        wp_enqueue_script('careerfy-counters');

        $counter_class = 'careerfy-counter';
        if ($view == 'view-2') {
            $counter_class = 'careerfy-modren-counter';
        } else if ($view == 'view-3') {
            $counter_class = 'careerfy-counter careerfy-counter-styletwo';
        } else if ($view == 'view-4') {
            $counter_class = 'careerfy-counter-nineview';
        } else if ($view == 'view-5') {
            $counter_class = 'careerfy-counter-style10';
        } else if ($view == 'view-6') {
            $counter_class = 'careerfy-counter-elevenview';
        } else if ($view == 'view-7') {
            $counter_class = 'careerfy-counter-twelveview';
        }

        ob_start();
        ?>
        <div class="careerfy-counter-main-wrap <?php echo $counter_class ?>">
            <ul class="row">
                <?php echo $this->careerfy_counters_item_shortcode() ?>
            </ul>
        </div>
        <?php if ($view == 'view-5') { ?>
        <style>
            .careerfy-counter-style10 span:before {
                background-color: <?php echo $counter_title_color ?>;
            }
        </style>
    <?php }
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    {
    }
}