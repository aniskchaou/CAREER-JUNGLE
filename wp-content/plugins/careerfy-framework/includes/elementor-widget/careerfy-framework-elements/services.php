<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
if (!defined('ABSPATH')) exit;


/**
 * @since 1.1.0
 */
class Services extends Widget_Base
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
        return 'serives';
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
        return __('Services', 'careerfy-frame');
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
        return 'fa fa-users-cog';
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
                'label' => __('Services Settings', 'careerfy-frame'),
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
                    'view-8' => __('Style 8', 'careerfy-frame'),
                    'view-9' => __('Style 9', 'careerfy-frame'),
                    'view-10' => __('Style 10', 'careerfy-frame'),
                    'view-11' => __('Style 11', 'careerfy-frame'),
                    'view-12' => __('Style 12', 'careerfy-frame'),
                    'view-13' => __('Style 13', 'careerfy-frame'),
                    'view-14' => __('Style 14', 'careerfy-frame'),
                    'view-15' => __('Style 15', 'careerfy-frame'),
                    'view-16' => __('Style 16', 'careerfy-frame'),
                    'view-17' => __('Style 17', 'careerfy-frame'),
                    'view-18' => __('Style 18', 'careerfy-frame'),
                    'view-19' => __('Style 19', 'careerfy-frame'),
                    'view-20' => __('Style 20', 'careerfy-frame'),
                    'view-21' => __('Style 21', 'careerfy-frame'),
                    'view-22' => __('Style 22', 'careerfy-frame'),

                ],
            ]
        );

        $this->add_control(
            'service_title_color',
            [
                'label' => __('Title Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
            ]
        );
        $this->add_control(
            'service_text_color',
            [
                'label' => __('Description Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
            ]
        );
        $this->add_control(
            'service_icon_color',
            [
                'label' => __('Icon Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
            ]
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'service_img', [
                'label' => __('Image', 'careerfy-frame'),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'label_block' => true,
                'description' => __("This option will not apply to 'Service style 4', 'Service style 8' ,'Service style 11' and style 21 as background image. For Style 21 first uploaded image size should be (width:940px), (height:670). Rest of the images should (width:467px), (height:330)", "careerfy-frame"),
            ]
        );

        $repeater->add_control(
            'service_icon', [
                'label' => __('Icon', 'careerfy-frame'),
                'type' => Controls_Manager::ICONS,
            ]
        );

        $repeater->add_control(
            'service_bg', [
                'label' => __('Background Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'label_block' => true,
                "description" => __("This option will apply to 'Service style 4' only.", "careerfy-frame"),
            ]
        );


        $repeater->add_control(
            'service_title', [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
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

        $repeater->add_control(
            'service_desc', [
                'label' => __('Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'btn_txt', [
                'label' => __('Button Text', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __("Upload Resume", "careerfy-frame"),
                "description" => __("This option will apply on 'Service style 6 and Service style 8' Buttons only.", "careerfy-frame"),
            ]
        );

        $repeater->add_control(
            'btn_color', [
                'label' => __('Button Text', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'label_block' => true,
                'default' => '#04d16a',
                "description" => __("This option will apply on 'Service style 6 and Service style 8' Buttons only.", "careerfy-frame"),]
        );

        $this->add_control(
            'careerfy_services_item',
            [
                'label' => __('Add Counters', 'careerfy-frame'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ service_title }}}',
            ]
        );
        $this->end_controls_section();
    }

    protected function careerfy_services_item_shortcode()
    {
        $atts = $this->get_settings_for_display();
        global $service_shortcode_counter, $view, $service_text_color, $service_icon_color, $service_title_color;

        $icon_color = '';
        if (isset($service_icon_color) && !empty($service_icon_color)) {
            $icon_color = ' style="color:' . $service_icon_color . ' !important"';
        }

        $text_color = '';
        $title_color = '';
        $text_color_a = '';
        $text_color_h2 = '';
        if (isset($service_text_color) && !empty($service_text_color)) {
            $text_color = ' style="color:' . $service_text_color . ' !important"';
        }
        if (isset($service_title_color) && !empty($service_title_color)) {
            $title_color = ' style="color:' . $service_title_color . ' !important"';
        }


        if (isset($service_link) && !empty($service_link)) {
            $text_color_a = $title_color;
        }
        if (isset($text_color_a) && !empty($text_color_a)) {
            $text_color_h2 = '';
        } else {
            $text_color_h2 = $title_color;
        }


        foreach ($atts['careerfy_services_item'] as $info) {
            $rand_num = rand(10000000, 99909999);
            $btn_txt = $info['btn_txt'];
            $btn_color = $info['btn_color'];
            $service_icon = $info['service_icon']['value'];
            $service_img = $info['service_img'] != '' ? $info['service_img']['url'] : '';
            $service_title = $info['service_title'];
            $service_link = $info['service_link'];
            $service_bg = $info['service_bg'];
            $service_desc = $info['service_desc'];


            if ($view == 'view-2') {
                $html = '
        <li class="col-md-4">
            <span><i class="' . $service_icon . '"' . $icon_color . '></i></span>
            <h2' . $text_color_h2 . '>' . ($service_link != '' ? '<a href="' . $service_link . '"' . $text_color_a . '>' : '') . $service_title . ($service_link != '' ? '</a>' : '') . '</h2>
            <p' . $text_color . '>' . $service_desc . '</p>
        </li>';
            } else if ($view == 'view-3' || $view == 'view-4') {
                $html = '
        <li class="col-md-4">
            ' . ($view == 'view-4' ? '<div class="careerfy-services-stylefour-wrap ' . ($service_shortcode_counter == 2 ? 'active' : '') . '"' . ($service_bg != '' ? ' style="background-color: ' . $service_bg . ';"' : '') . '>' : '') . ' 
            <i class="' . $service_icon . '"' . $icon_color . '></i>
            <h2' . $text_color_h2 . '>' . ($service_link != '' ? '<a href="' . $service_link . '"' . $text_color_a . '>' : '') . $service_title . ($service_link != '' ? '</a>' : '') . '</h2>
            <p' . $text_color . '>' . $service_desc . '</p>
            ' . ($view == 'view-4' && $service_shortcode_counter == 2 ? '<a ' . ($service_link != '' ? 'href="' . $service_link . '"' : '') . ' class="careerfy-services-stylefour-btn"><small class="careerfy-icon careerfy-right-arrow"></small></a>' : '') . '
            ' . ($view == 'view-4' ? '</div>' : '') . ' 
        </li>';
            } else if ($view == 'view-5') {
                $html = '
        <li class="col-md-4">
			<a href="' . $service_link . '"><img src="' . $service_img . '" alt=""></a>
            <h2' . $text_color_h2 . '>' . ($service_link != '' ? '<a href="' . $service_link . '"' . $text_color_a . '>' : '') . $service_title . ($service_link != '' ? '</a>' : '') . '</h2>
            <p' . $text_color . '>' . $service_desc . '</p>
        </li>';
            } else if ($view == 'view-6') {

                $html = '
        <style>
         .careerfy-action-style11  .careerfy-services-style11-btn-' . $rand_num . ' {
            border-color: ' . $btn_color . ';
                }
                li:hover .careerfy-services-style11-btn-' . $rand_num . ' {
                background-color: ' . $btn_color . ';
                }
        </style>
        <li class="col-md-4">';
                if (!empty($service_img)) {
                    $html .= '<img src="' . $service_img . '" alt="">';
                }
                $html .= '<h2 ' . $text_color_h2 . '>' . ($service_link != '' ? '<a href="' . $service_link . '"' . $text_color_a . '>' : '') . $service_title . ($service_link != '' ? '</a>' : '') . '</h2>
            <p ' . $text_color . '>' . $service_desc . '</p >
            <a  href = "' . $service_link . '" class="careerfy-services-style11-btn" > ' . $btn_txt . '</a >
        </li >';

            } else if ($view == 'view-7') {
                $html = '<li class="col-md-3">
                     <i class="' . $service_icon . '"' . $icon_color . '></i>
                     <h2 ' . $text_color_h2 . '>' . ($service_link != '' ? '<a href="' . $service_link . '"' . $text_color_a . '>' : '') . $service_title . ($service_link != '' ? '</a>' : '') . '</h2>
                     <p ' . $text_color . '>' . $service_desc . '</p>
                 </li>';
            } else if ($view == 'view-8') {
                $html = '
        <style>
         .careerfy-services-eighteen  .careerfy-action-button-' . $rand_num . ' {
            border-color: ' . $btn_color . ';
            color: ' . $btn_color . ';
                }
                
        </style>
        
       <div class="col-md-3">
          <i class="' . $service_icon . '" ' . $icon_color . '></i>
          <span ' . $title_color . '>' . $service_title . '</span>
          <p ' . $text_color . '>' . $service_desc . '</p >
          <a href="' . $service_link . '" class="careerfy-action-button-' . $rand_num . '">' . $btn_txt . '</a>
        </div>';

            } else if ($view == 'view-9') {
                $html = '
        
       <li class="col-md-6">
       <a href="' . $service_link . '">
          <small><i class="' . $service_icon . '" ' . $icon_color . '></i></small>
          <span ' . $title_color . '>' . $service_title . '</span>
          <p ' . $text_color . '>' . $service_desc . '</p >
         </a>
        </li>';

            } else if ($view == 'view-10') {
                $html = '
        
       <li class="col-md-12">
       <a href="' . $service_link . '">
          <small><i class="' . $service_icon . '" ' . $icon_color . '></i></small>
          <span ' . $title_color . '>' . $service_title . '</span>
          <p ' . $text_color . '>' . $service_desc . '</p >
         </a>
         
        </li>';

            } else if ($view == 'view-11') {

                $imag_id = attachment_url_to_postid($service_img);
                $thumbnail_image = wp_get_attachment_image_src($imag_id, 'thumbnail');
                $no_placeholder_img = '';
                if (function_exists('jobsearch_no_image_placeholder')) {
                    $no_placeholder_img = jobsearch_no_image_placeholder();
                }
                $thumbnail_src = isset($thumbnail_image[0]) && esc_url($thumbnail_image[0]) != '' ? $thumbnail_image[0] : $no_placeholder_img;
                $html = '
       <li class="col-md-6">
       <a href="' . $service_link . '">
        <img src="' . $thumbnail_src . '" alt="">
          <p>
              <small ' . $title_color . '>' . $service_title . '</small>
              <span ' . $text_color . '>' . $service_desc . '</span>
              <strong>' . $btn_txt . '</strong>
          </p >
         </a>
        </li>';

            } else if ($view == 'view-12') {

                $html = '
       <li class="col-md-3">
       <a href="' . $service_link . '">
        <i class="top-icon ' . $service_icon . '" ' . $icon_color . '></i>
              <span ' . $title_color . '>' . $service_title . '</span>
              <p ' . $text_color . '>' . $service_desc . '</p>
              <strong >' . $btn_txt . '</strong>
              <i class="bottom-icon ' . $service_icon . '"></i>
         </a>
        </li>';

            } else if ($view == 'view-13') {

                $html = '

       <li class="col-md-6">
       <a href="' . $service_link . '">
       <div class="careerfy-services-twenty-style2-counter">
       
       <i class="' . $service_icon . '" ' . $icon_color . '></i>
        </div>
        <div class="careerfy-services-twenty-style2-content">
        <small>' . $service_shortcode_counter . '</small>
              <span ' . $title_color . '>' . $service_title . '</span>
              <p ' . $text_color . '>' . $service_desc . '</p>
              </div>
         </a>
        </li>';

            } else if ($view == 'view-14') {

                $html = '

       <li class="col-md-3">
       <a href="' . $service_link . '">
       <i class="' . $service_icon . '" ' . $icon_color . '></i>
         <span ' . $title_color . '>' . $service_title . '</span>
         <p ' . $text_color . '>' . $service_desc . '</p>
         </a>
        </li>';

            } else if ($view == 'view-15') {

                $imag_id = attachment_url_to_postid($service_img);
                $thumbnail_image = wp_get_attachment_image_src($imag_id, 'careerfy-service');
                $no_placeholder_img = '';
                if (function_exists('jobsearch_no_image_placeholder')) {
                    $no_placeholder_img = jobsearch_no_image_placeholder();
                }
                $thumbnail_src = isset($thumbnail_image[0]) && esc_url($thumbnail_image[0]) != '' ? $thumbnail_image[0] : $no_placeholder_img;
                $html = '
       <li class="col-md-3">
       <a href="' . $service_link . '">
       <div class="careerfy-services-twenty-img">
            <img src="' . $thumbnail_src . '" alt=""><small></small>
       </div>
         <span ' . $title_color . '>' . $service_title . '</span>
         <p ' . $text_color . '>' . $service_desc . '</p>
         
         </a>
        </li>';

            } else if ($view == 'view-16') {

                $html = '
       <li class="col-md-3">
       <a href="' . $service_link . '">
       
         <span ' . $title_color . '>0' . $service_shortcode_counter . '. ' . $service_title . '</span>
         <p ' . $text_color . '>' . $service_desc . '</p>
         <i class="' . $service_icon . '"></i>
         </a>
         
        </li>';

            } else if ($view == 'view-17') {

                $imag_id = attachment_url_to_postid($service_img);
                $thumbnail_image = wp_get_attachment_image_src($imag_id, 'thumbnail');
                $no_placeholder_img = '';
                if (function_exists('jobsearch_no_image_placeholder')) {
                    $no_placeholder_img = jobsearch_no_image_placeholder();
                }
                $thumbnail_src = isset($thumbnail_image[0]) && esc_url($thumbnail_image[0]) != '' ? $thumbnail_image[0] : $no_placeholder_img;

                $html = '<li class="col-md-12">
       <a href="' . $service_link . '">
          <img src="' . $thumbnail_src . '" alt="">
          <div class="careerfy-services-twentyone-content">
          <span ' . $title_color . '>' . $service_title . '</span>
          <p ' . $text_color . '>' . $service_desc . '</p >
          </div>
         </a>
        </li>';

            } else if ($view == 'view-18') {
                $html = '
       <li class="col-md-3">
       <a href="' . $service_link . '">
       <small><i class="' . $service_icon . '"></i></small>
         <span ' . $title_color . '>' . $service_title . '</span>
         <p ' . $text_color . '>' . $service_desc . '</p>
         </a>
        </li>';
            } else if ($view == 'view-19') {

                $service_bg_img = !empty($service_img) ? "style='background-image: url($service_img)'" : '';

                $html = '
       <li class="col-md-3">
       <a href="' . $service_link . '"  ' . $service_bg_img . '>
         <span ' . $title_color . '>' . $service_title . '</span>
         <p ' . $text_color . '>' . $service_desc . '</p>
         <small>0' . $service_shortcode_counter . '</small>
         </a>
        </li>';
            } else if ($view == 'view-20') {
                $html = '
       <li class="col-md-3">
       <div class="careerfy-services-twentytwo-inner">
       <a href="' . $service_link . '">
       <i class="' . $service_icon . '"></i>
         <span ' . $title_color . '>' . $service_title . '</span>
         <p ' . $text_color . '>' . $service_desc . '</p>
         </a>
         <a href="' . $service_link . '" class="careerfy-services-twentytwo-btn" >' . $btn_txt . '</a>
         </div>
        </li>';

            } else if ($view == 'view-21') {

                $imag_id = attachment_url_to_postid($service_img);
                $thumbnail_image = wp_get_attachment_image_src($imag_id);
                $no_placeholder_img = '';
                if (function_exists('jobsearch_no_image_placeholder')) {
                    $no_placeholder_img = jobsearch_no_image_placeholder();
                }
                $thumbnail_src = isset($thumbnail_image[0]) && esc_url($thumbnail_image[0]) != '' ? $thumbnail_image[0] : $no_placeholder_img;
                $html = '';
                if ($service_shortcode_counter == 1) {

                    $html = '<div class="col-md-6">
                    <div class="careerfy-services-twentytwo-style2 careerfy-services-big">
                        <a href="' . $service_link . '">
                        <div class="careerfy-services-twentytwo-style2-img">
                        <img src="' . $thumbnail_src . '" >
                        </div>
                    <div class="careerfy-services-twentytwo-style2-content">';

                    if ($service_shortcode_counter == 1) {
                        $html .= '<small > ' . $small_service_title . '</small >';
                    }

                    $html .= '
                <span ' . $title_color . '>' . $service_title . '</span>
                         <p ' . $text_color . '>' . $service_desc . '</p>
                         <strong class="careerfy-services-twentytwo-style2-btn">' . $btn_txt . '</strong>
                </div>
                
                </a>
                
                </div>
            </div>';
                }

                if ($service_shortcode_counter == 2) {
                    $html .= '<div class="col-md-6">
                    <div class="careerfy-services-twentytwo-style2-wrapper">';
                }
                if ($service_shortcode_counter != 1) {
                    $html .= '<div class="careerfy-services-twentytwo-style2">
                    <a href="' . $service_link . '" >
                    <div class="careerfy-services-twentytwo-style2-img">
                     <img src="' . $thumbnail_src . '" >
                    </div>
                    <div class="careerfy-services-twentytwo-style2-content">
        <h2 ' . $title_color . '>' . $service_title . '</h2>
                  <p ' . $text_color . '>' . $service_desc . '</p>
                </div>
                </a>
                </div>';
                }
                if ($service_shortcode_counter == 5) {
                    $html .= '</div></div>';
                }

            } else if ($view == 'view-22') {

                $imag_id = attachment_url_to_postid($service_img);
                $thumbnail_image = wp_get_attachment_image_src($imag_id, 'careerfy-testimonial-thumb');
                $no_placeholder_img = '';
                if (function_exists('jobsearch_no_image_placeholder')) {
                    $no_placeholder_img = jobsearch_no_image_placeholder();
                }
                $thumbnail_src = isset($thumbnail_image[0]) && esc_url($thumbnail_image[0]) != '' ? $thumbnail_image[0] : $no_placeholder_img;

                $html = '
       <li class="col-md-3 ">
       <a href="' . $service_link . '">
       <img src="' . $thumbnail_src . '" alt="">
         <span ' . $title_color . '>' . $service_title . '</span>
         <p ' . $text_color . '>' . $service_desc . '</p>
         </a>
        </li>';
            } else {
                $html = '
        <li class="col-md-4">
            <span>' . $service_shortcode_counter . '</span>
            <i class="' . $service_icon . '"' . $icon_color . '></i>
            <h2' . $text_color_h2 . '>' . ($service_link != '' ? '<a href="' . $service_link . '"' . $text_color_a . '>' : '') . $service_title . ($service_link != '' ? '</a>' : '') . '</h2>
            <p' . $text_color . '>' . $service_desc . '</p>
        </li>';
            }

            $service_shortcode_counter++;
            echo $html;
        }

    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        global $service_shortcode_counter, $view, $service_text_color, $service_icon_color, $service_title_color;
        extract(shortcode_atts(array(
            'view' => '',
            'service_title_color' => '',
            'service_text_color' => '',
            'service_icon_color' => '',
        ), $atts));

        $service_shortcode_counter = 1;

        $service_class = 'careerfy-classic-services';
        if ($view == 'view-2') {
            $service_class = 'careerfy-services-classic';
        }
        if ($view == 'view-3') {
            $service_class = 'careerfy-plain-services';
        }
        if ($view == 'view-4') {
            $service_class = 'careerfy-services careerfy-services-stylefour';
        }
        if ($view == 'view-5') {
            $service_class = 'careerfy-services careerfy-services-stylefive';
        }
        if ($view == 'view-6') {
            $service_class = 'careerfy-services careerfy-services-style11';
        }
        if ($view == 'view-7') {
            $service_class = 'careerfy-seventeen-services';
        }
        if ($view == 'view-8') {
            $service_class = 'careerfy-services-eighteen';
        }
        if ($view == 'view-9') {
            $service_class = 'careerfy-services careerfy-services-nineteen';
        }
        if ($view == 'view-10') {
            $service_class = 'careerfy-services careerfy-services-nineteen-style2';
        }
        if ($view == 'view-11') {
            $service_class = 'careerfy-services careerfy-services-nineteen-style3';
        }
        if ($view == 'view-12') {
            $service_class = 'careerfy-services careerfy-services-twenty';
        }
        if ($view == 'view-13') {
            $service_class = 'careerfy-services careerfy-services-twenty-style2';
        }
        if ($view == 'view-14') {
            $service_class = 'careerfy-services careerfy-services-twenty-style3';
        }
        if ($view == 'view-15') {
            $service_class = 'careerfy-services careerfy-services-twenty-style4';
        }
        if ($view == 'view-16') {
            $service_class = 'careerfy-services careerfy-services-twentyone';
        }
        if ($view == 'view-17') {
            $service_class = 'careerfy-services careerfy-services-twentyone-style2';
        }
        if ($view == 'view-18') {
            $service_class = 'careerfy-services careerfy-services-twentyone-style3';
        }
        if ($view == 'view-19') {
            $service_class = 'careerfy-services careerfy-services-twentyone-style4';
        }
        if ($view == 'view-20') {
            $service_class = 'careerfy-services careerfy-services-twentytwo';
        }

        if ($view == 'view-22') {
            $service_class = 'careerfy-services careerfy-services-twentytwo-style3';
        }

        ob_start();

        ?>
        <?php if ($view != 'view-21') { ?>
            <div class="<?php echo $service_class ?>">
        <?php } ?>

        <?php if ($view == 'view-21' && $view == 'view-8') { ?>
            <div class="row">
                <?php echo $this->careerfy_services_item_shortcode() ?>
            </div>
        <?php } else { ?>
            <ul class="row">
                <?php echo $this->careerfy_services_item_shortcode() ?>
            </ul>
        <?php } ?>

        <?php if ($view != 'view-21') { ?>
            </div>
        <?php } ?>

        <?php
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    {

    }
}