<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
if (!defined('ABSPATH')) exit;

/**
 * @since 1.1.0
 */
class ImageServices extends Widget_Base
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
        return 'image-services';
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
        return __('Image Services', 'careerfy-frame');
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
                'label' => __('Image Services Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'service_view',
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

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'service_img', [
                'label' => __('Image', 'careerfy-frame'),
                'type' => Controls_Manager::MEDIA,
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'service_title', [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $repeater->add_control(
            'service_desc', [
                'label' => __('Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'service_link', [
                'label' => __('Link', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'careerfy_image_services_item',
            [
                'label' => __('Add Image Service item', 'careerfy-frame'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ service_title }}}',
            ]
        );
        $this->end_controls_section();
    }

    protected function careerfy_image_services_item_shortcode()
    {
        global $service_view;
        $atts = $this->get_settings_for_display();
        foreach ($atts['careerfy_image_services_item'] as $index => $item) {
            $service_shortcode_counter = $index + 1;
            $service_img = $item['service_img'] != '' ? $item['service_img']['url'] : '';
            $service_title = $item['service_title'];
            $service_desc = $item['service_desc'];
            $service_link = $item['service_link'];

            if ($service_view == 'view2') {

            $html = '<li class="col-md-4">
                    <div class="careerfy-services-stylethree-wrap">
                        <img src="' . $service_img . '" alt="">
                        <h2>' . ($service_link != '' ? '<a href="' . $service_link . '">' : '') . $service_title . ($service_link != '' ? '</a>' : '') . '</h2>
                        <p>' . $service_desc . '</p>
                        <span>' . $service_shortcode_counter . '</span>
                    </div>
                </li>';
            } else {
                $html = '
                <li class="col-md-3">
                    <div class="careerfy-modren-services-wrap">
                        <img src="' . $service_img . '" alt="">
                        <p>' . $service_desc . '</p>
                        <span>' . $service_title . '</span>
                        <a href="' . $service_link . '" class="careerfy-modren-service-link"><i class="careerfy-icon careerfy-right-arrow-long"></i></a>
                    </div>
                </li>';
            }
            echo $html;
        }

    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        global $service_view;
        extract(shortcode_atts(array(
            'service_view' => '',
        ), $atts));

        $serv_class = 'careerfy-services careerfy-modren-services';
        if ($service_view == 'view2') {
            $serv_class = 'careerfy-services careerfy-services-stylethree';
        }
        ob_start();
        ?>
        <div class="<?php echo $serv_class ?>">
            <ul class="row">
                <?php echo $this->careerfy_image_services_item_shortcode(); ?>
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