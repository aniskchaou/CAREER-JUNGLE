<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH')) exit;


/**
 * @since 1.1.0
 */
class HomePageSlider extends Widget_Base
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
        return 'home-page-slider';
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
        return __('Home Page Slider', 'careerfy-frame');
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
        return 'fa fa-picture-o';
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
                'label' => __('Slider Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'slider_view',
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
            'slider_img', [
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


        $repeater->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border_a',
                'selector' => '{{WRAPPER}} .elementor-flip-box__front',
                'separator' => 'before',
            ]
        );

        $repeater->add_control(
            'tiny_title', [
                'label' => __('Tiny Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                "description" => __("Will not affect on style 2", "careerfy-frame"),
            ]
        );

        $repeater->add_control(
            'small_title', [
                'label' => __('Small Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );


        $repeater->add_control(
            'big_title', [
                'label' => __('Big Title', 'careerfy-frame'),
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
            'slider_desc', [
                'label' => __('Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'button_bg_color', [
                'label' => __('Button Background Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'label_block' => true,
                "description" => __("Select the background color for the slider button.", "careerfy-frame"),
                'selectors' => [
                    '{{WRAPPER}} .home-page-slider-main-wrapper a' => 'border-color: {{VALUE}}; background-color: {{VALUE}};',
                ],
            ]
        );

        $repeater->add_control(
            'button_title', [
                'label' => __('Button Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'button_url', [
                'label' => __('Button URL', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'button_title_color', [
                'label' => __('Button Title Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'label_block' => true,
                'selectors' => [
                    '{{WRAPPER}} .home-page-slider-main-wrapper a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'careerfy_slider_item',
            [
                'label' => __('Add Slider Content', 'careerfy-frame'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ tiny_title }}}',
            ]
        );
        $this->end_controls_section();
    }

    protected function careerfy_slider_item_shortcode()
    {
        $atts = $this->get_settings_for_display();
        global $slider_shortcode_counter, $slider_view;

        extract(shortcode_atts(array(
            'slider_img' => '',
            'tiny_title' => '',
            'small_title' => '',
            'big_title' => '',
            'slider_desc' => '',
            'button_bg_color' => '',
            'button_title' => '',
            'button_url' => '',
            'button_title_color' => '',
        ), $atts));


        foreach ($atts['careerfy_slider_item'] as $item) {
            $slider_img = $item['slider_img'] != "" ? $item['slider_img']['url'] : '';
            $tiny_title = $item['tiny_title'] != "" ? $item['tiny_title'] : '';
            $small_title = $item['small_title'] != "" ? $item['small_title'] : '';
            $big_title = $item['big_title'] != "" ? $item['big_title'] : '';
            $slider_desc = $item['slider_desc'] != "" ? $item['slider_desc'] : '';
            $button_title = $item['button_title'] != "" ? $item['button_title'] : '';
            $button_url = $item['button_url'] != "" ? $item['button_url'] : '';

            if ($slider_view == 'view1') { ?>

                <div class="careerfy-bannernine-layer">
                    <div class="careerfy-bannernine-caption">
                        <div class="container">
                            <div class="careerfy-bannernine-caption-inner">
                                <span><?php echo($tiny_title) ?></span>
                                <h1><?php echo($small_title) ?></h1>
                                <h2><?php echo($big_title) ?></h2>
                                <p><?php echo($slider_desc) ?></p>
                                <a href="<?php echo($button_url) ?>"><?php echo($button_title) ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="careerfy-bannernine-thumb"><img src="<?php echo($slider_img) ?>" alt=""></div>
                </div>
            <?php } else { ?>
                <div class="careerfy-seventeen-banner-layer">
                    <img src="<?php echo($slider_img) ?>" alt="">
                    <div class="careerfy-seventeen-banner-caption">
                        <div class="container">
                            <div class="careerfy-seventeen-banner-caption-inner">
                                <h1><?php echo($big_title) ?></h1>
                                <p><?php echo($slider_desc) ?></p>
                                <a href="<?php echo($button_url) ?>"
                                   class="careerfy-seventeen-banner-btn"><?php echo($button_title) ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
        }
        $slider_shortcode_counter++;

    }


    protected function render()
    {
        $atts = $this->get_settings_for_display();
        global $slider_shortcode_counter, $slider_view, $careerfy_framework_options;
        extract(shortcode_atts(array(
            'slider_view' => 'view1',
        ), $atts));
        $rand_num = rand(1000000, 9999999);
        $slider_shortcode_counter = 1;

        $careerfy_site_loader = isset($careerfy_framework_options['careerfy-site-loader']) ? $careerfy_framework_options['careerfy-site-loader'] : '';

        if ($slider_view == 'view1') {

            $slider_class = 'careerfy-banner-nine careerfy-banner-' . $rand_num;

        } else {

            $slider_class = 'careerfy-seventeen-banner careerfy-banner-' . $rand_num;
        }
        ob_start();
        ?>
        <div id="careerfy-slidmaintop-<?php echo($rand_num) ?>" style="position: relative; float: left; width: 100%;">
            <div id="careerfy-slidloder-<?php echo($rand_num) ?>" class="careerfy-slidloder-section">
                <div class="ball-scale-multiple">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>

            <div class="home-page-slider-main-wrapper <?php echo($slider_class) ?>">
                <?php echo $this->careerfy_slider_item_shortcode() ?>
            </div>

            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').css({'height': 'auto'});
                    jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').find('.careerfy-bannernine-layer').css({'display': 'inline-block'});

                    <?php if($slider_view == 'view1'){ ?>
                    jQuery('.careerfy-banner-<?php echo($rand_num) ?>').slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed: 5000,
                        infinite: true,
                        dots: false,
                        arrows: false,
                        fade: true,
                        responsive: [
                            {
                                breakpoint: 1024,
                                settings: {
                                    slidesToShow: 1,
                                    slidesToScroll: 1,
                                    infinite: true,
                                }
                            },
                            {
                                breakpoint: 800,
                                settings: {
                                    slidesToShow: 1,
                                    slidesToScroll: 1
                                }
                            },
                            {
                                breakpoint: 400,
                                settings: {
                                    slidesToShow: 1,
                                    slidesToScroll: 1
                                }
                            }
                        ]
                    });
                    <?php } else { ?>
                    jQuery('.careerfy-banner-<?php echo($rand_num) ?>').slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed: 3000,
                        infinite: true,
                        dots: false,
                        arrows: false,
                        responsive: [
                            {
                                breakpoint: 1024,
                                settings: {
                                    slidesToShow: 1,
                                    slidesToScroll: 1,
                                    infinite: true,
                                }
                            },
                            {
                                breakpoint: 800,
                                settings: {
                                    slidesToShow: 1,
                                    slidesToScroll: 1
                                }
                            },
                            {
                                breakpoint: 400,
                                settings: {
                                    slidesToShow: 1,
                                    slidesToScroll: 1
                                }
                            }
                        ]
                    });
                    <?php } ?>
                    var remSlidrLodrInt<?php echo($rand_num) ?> = setInterval(function () {
                        jQuery('#careerfy-slidloder-<?php echo($rand_num) ?>').remove();
                        clearInterval(remSlidrLodrInt<?php echo($rand_num) ?>);
                    }, 1500);

                    var slidrHightInt<?php echo($rand_num) ?> = setInterval(function () {
                        jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').css({'height': 'auto'});
                        jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').find('.careerfy-bannernine-layer').css({'display': 'inline-block'});
                        var slider_act_height_<?php echo($rand_num) ?> = jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').height();

                        var filtr_cname_<?php echo($rand_num) ?> = 'careerfy_main_slider_lheight';
                        var c_date_<?php echo($rand_num) ?> = new Date();
                        c_date_<?php echo($rand_num) ?>.setTime(c_date_<?php echo($rand_num) ?>.getTime() + (60 * 60 * 1000));
                        var c_expires_<?php echo($rand_num) ?> = "; c_expires=" + c_date_<?php echo($rand_num) ?>.toGMTString();
                        document.cookie = filtr_cname_<?php echo($rand_num) ?> + "=" + slider_act_height_<?php echo($rand_num) ?> + c_expires_<?php echo($rand_num) ?> + "; path=/";
                        clearInterval(slidrHightInt<?php echo($rand_num) ?>);
                    }, 2000);
                });
                jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').find('.careerfy-bannernine-layer').css({'display': 'none'});

                var slider_height_<?php echo($rand_num) ?> = '<?php echo(isset($_COOKIE['careerfy_main_slider_lheight']) && $_COOKIE['careerfy_main_slider_lheight'] != '' ? $_COOKIE['careerfy_main_slider_lheight'] . 'px' : '300px') ?>';
                jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').css({'height': slider_height_<?php echo($rand_num) ?>});
            </script>
        </div>
        <?php

        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    {
    }
}