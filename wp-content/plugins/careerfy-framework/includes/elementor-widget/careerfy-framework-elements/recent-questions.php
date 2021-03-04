<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

/**
 * @since 1.1.0
 */
class RecentQuestions extends Widget_Base
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
        return 'recent-questions';
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
        return __('Recent Questions', 'careerfy-frame');
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
                'label' => __('Recent Questions Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'ques_title',
            [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'q_question', [
                'label' => __('Question', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'q_url', [
                'label' => __('Link', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'careerfy_recent_questions_item',
            [
                'label' => __('Add recent questions item', 'careerfy-frame'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ q_question }}}',
            ]
        );
        $this->end_controls_section();
    }

    protected function careerfy_recent_questions_item_shortcode()
    {
        $atts = $this->get_settings_for_display();
        extract(shortcode_atts(array(
            'q_question' => '',
            'q_url' => '',
        ), $atts));
        foreach ($atts['careerfy_recent_questions_item'] as $info) {
            $html = '<li><a href="' . $info['q_url'] . '">' . $info['q_question'] . '</a></li>';
            echo $html;
        }
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        extract(shortcode_atts(array(
            'ques_title' => '',
        ), $atts));
        ob_start();
        ?>

        <div class="widget widget_faq">
            <?php echo($ques_title != '' ? '<h2 class="careerfy-slash-title">' . $ques_title . '</h2>' : '') ?>
            <ul>
                <?php echo $this->careerfy_recent_questions_item_shortcode() ?>
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