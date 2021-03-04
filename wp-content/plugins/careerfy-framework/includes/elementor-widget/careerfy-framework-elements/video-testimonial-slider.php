<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
if (!defined('ABSPATH')) exit;


/**
 * @since 1.1.0
 */
class VideoTestimonialSlider extends Widget_Base
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
        return 'Video-testimonial-slider';
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
        return __('Video Testimonial Slider', 'careerfy-frame');
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
        return 'fa fa-file-video-o';
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
                'label' => __('Video Testimonials Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'vid_testimonial_view',
            [
                'label' => __('Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'view1',
                'options' => [
                    'view1' => __('Style 1', 'careerfy-frame'),
                    'view2' => __('Style 2', 'careerfy-frame'),
                    'view3' => __('Style 3', 'careerfy-frame'),
                ],
            ]
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'vid_testimonial_img', [
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
            'vid_url', [
                'label' => __('Video URL', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                "description" => __("Put Video URL of Vimeo, Youtube.", "careerfy-frame")
            ]
        );

        $repeater->add_control(
            'client_title', [
                'label' => __('Client Name', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                "description" => __('client Title will  be applied on style 1 and style 3.', "careerfy-frame")
            ]
        );


        $repeater->add_control(
            'rank_title', [
                'label' => __('Client Rank', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                "description" => __('Client Rank will be applied on style 1 and style 3.', "careerfy-frame")
            ]
        );

        $repeater->add_control(
            'company_title', [
                'label' => __('Client Company', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                "description" => __("Client Company will be applied to style 1 and style 2.", "careerfy-frame")
            ]
        );

        $repeater->add_control(
            'testimonial_desc', [
                'label' => __('Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
                'label_block' => true,
                "description" => __("Testimonial Description will be applied to style 3 only.", "careerfy-frame")
            ]
        );

        $repeater->add_control(
            'client_location', [
                'label' => __('Client Location', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                "description" => __("Client Location will be applied to style 3 only.", "careerfy-frame")
            ]
        );
        $this->add_control(
            'careerfy_video_testimonial_item',
            [
                'label' => __('Add video testimonial item', 'careerfy-frame'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ company_title }}}',
            ]
        );
        $this->end_controls_section();
    }

    protected function careerfy_video_testimonial_item_shortcode()
    {
        global $vid_testimonial_shortcode_counter, $vid_testimonial_view;
        $atts = $this->get_settings_for_display();



        foreach ($atts['careerfy_video_testimonial_item'] as $item) {

            $vid_testimonial_img = $item['vid_testimonial_img'] != '' ? $item['vid_testimonial_img']['url'] : '';
            $client_title = $item['client_title'] != '' ? $item['client_title'] : '';
            $rank_title = $item['rank_title'] != '' ? $item['rank_title'] : '';
            $company_title = $item['company_title'] != '' ? $item['company_title'] : '';
            $client_location = $item['client_location'] != '' ? $item['client_location'] : '';
            $testimonial_desc = $item['testimonial_desc'] != '' ? $item['testimonial_desc'] : '';


            $gal_video_url = $item['vid_url'];
            if ($gal_video_url != '') {
                if (strpos($gal_video_url, 'watch?v=') !== false) {
                    $gal_video_url = str_replace('watch?v=', 'embed/', $gal_video_url);
                }
                if (strpos($gal_video_url, '?') !== false) {
                    $gal_video_url .= '&autoplay=1';
                } else {
                    $gal_video_url .= '?autoplay=1';
                }
            }

            if ($vid_testimonial_view == 'view2') {
                $html = ' <div class="careerfy-services-video-layer">
                     <figure><a href="' . ($gal_video_url != '' ? $gal_video_url : 'javascript:void(0);') . '" class="' . ($gal_video_url != '' ? 'fancybox-video' : 'vid-testimonial') . '"' . ($gal_video_url != '' ? ' data-fancybox-type="iframe" data-fancybox-group="group"' : '') . '><img src="' . $vid_testimonial_img . '" alt=""><span><i class="fa fa-play-circle"></i></span></a></figure>
                     <h2><a href="#">' . $client_title . '</a></h2>
                     <p>' . $rank_title . '<br> ' . $company_title . '</p>
                  </div>';
            } else if ($vid_testimonial_view == 'view1') {
                $html = '
            <div class="careerfy-services-video-layer">
                <figure><a href="' . ($gal_video_url != '' ? $gal_video_url : 'javascript:void(0);') . '" class="' . ($gal_video_url != '' ? 'fancybox-video' : 'vid-testimonial') . '"' . ($gal_video_url != '' ? ' data-fancybox-type="iframe" data-fancybox-group="group"' : '') . '><img src="' . $vid_testimonial_img . '" alt=""><span><i class="fa fa-play-circle"></i></span></a></figure>
                <h2><a href="#">' . $client_title . '</a></h2>
                <p>' . $rank_title . '</p>
            </div>';
            } else {
                $html = '<div class="careerfy-testimonial-slider-layer">
                    <div class="careerfy-testimonial-style11-slider">
                    <i class="careerfy-icon careerfy-phrase"></i>
                    <p>' . $testimonial_desc . '</p>
                     <figure>
                      <a href="#" tabindex="-1"><img src="' . $vid_testimonial_img . '" alt=""></a>
                      <figcaption>
                       <h2>' . $client_title . '</h2>
                       <span>' . $client_location . '</span>
                      </figcaption>
                    </figure>
                   </div>
                   
                <div class="careerfy-testimonial-video-wrap">
                <a href="' . ($gal_video_url != '' ? $gal_video_url : 'javascript:void(0);') . '" class="' . ($gal_video_url != '' ? 'fancybox-video' : 'vid-testimonial') . '"' . ($gal_video_url != '' ? ' data-fancybox-type="iframe" data-fancybox-group="group"' : '') . '><img src="' . $vid_testimonial_img . '" alt=""><span class="vid-testimonial-icon"><i class="fa fa-play-circle"></i></span></a>
                </div>
                
            </div>';
            }
            echo $html;
        }
        $vid_testimonial_shortcode_counter++;

    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        global $vid_testimonial_shortcode_counter, $vid_testimonial_view;

        extract(shortcode_atts(array(
            'vid_testimonial_view' => 'view1'), $atts));
        $rand_num = rand(1000000, 9999999);

        $vid_testimonial_shortcode_counter = 1;
        if ($vid_testimonial_view == 'view1') {
            $vid_testimonial_class = 'careerfy-services-video careerfy-banner-' . $rand_num;
        } else if ($vid_testimonial_view == 'view2') {
            $vid_testimonial_class = 'careerfy-services-video careerfy-services-video-two careerfy-banner-' . $rand_num;
        } else {
            $vid_testimonial_class = 'careerfy-video-testimonial-slider testimonial-slider-' . $rand_num;

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

            <div class="<?php echo($vid_testimonial_class) ?>">
                <?php echo $this->careerfy_video_testimonial_item_shortcode() ?>
            </div>
            <script type="text/javascript">
                var $ = jQuery;
                $(document).ready(function () {
                    jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').css({'height': 'auto'});
                    jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').find('.careerfy-services-video-layer').css({'display': 'inline-block'});

                    <?php if($vid_testimonial_view == 'view3'){ ?>
                    $('.testimonial-slider-<?php echo($rand_num) ?>').slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed: 5000,
                        infinite: true,
                        dots: true,
                        arrows: false,
                        responsive: [
                            {
                                breakpoint: 1024,
                                settings: {
                                    slidesToShow: 1,
                                    slidesToScroll: 1,
                                    infinite: true
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
                    $('.careerfy-banner-<?php echo($rand_num) ?>').slick({
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed: 5000,
                        infinite: true,
                        dots: false,
                        prevArrow: "<span class=\'slick-arrow-left\'><i class=\'careerfy-icon careerfy-arrow-right-light\'></i></span>",
                        nextArrow: "<span class=\'slick-arrow-right\'><i class=\'careerfy-icon careerfy-arrow-right-light\'></i></span>",
                        responsive: [
                            {
                                breakpoint: 1024,
                                settings: {
                                    slidesToShow: 3,
                                    slidesToScroll: 1,
                                    infinite: true
                                }
                            },
                            {
                                breakpoint: 800,
                                settings: {
                                    slidesToShow: 2,
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
                    //

                    var slidrHightInt<?php echo($rand_num) ?> = setInterval(function () {
                        jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').css({'height': 'auto'});
                        jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').find('.careerfy-services-video-layer').css({'display': 'inline-block'});

                        var slider_act_height_<?php echo($rand_num) ?> = jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').height();

                        var filtr_cname_<?php echo($rand_num) ?> = 'careerfy_vidtests_slidr_lheight';
                        var c_date_<?php echo($rand_num) ?> = new Date();
                        c_date_<?php echo($rand_num) ?>.setTime(c_date_<?php echo($rand_num) ?>.getTime() + (60 * 60 * 1000));
                        var c_expires_<?php echo($rand_num) ?> = "; c_expires=" + c_date_<?php echo($rand_num) ?>.toGMTString();
                        document.cookie = filtr_cname_<?php echo($rand_num) ?> + "=" + slider_act_height_<?php echo($rand_num) ?> + c_expires_<?php echo($rand_num) ?> + "; path=/";

                        clearInterval(slidrHightInt<?php echo($rand_num) ?>);
                    }, 2000);
                });
                jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').find('.careerfy-services-video-layer').css({'display': 'none'});

                var slider_height_<?php echo($rand_num) ?> = '<?php echo(isset($_COOKIE['careerfy_vidtests_slidr_lheight']) && $_COOKIE['careerfy_vidtests_slidr_lheight'] != '' ? $_COOKIE['careerfy_vidtests_slidr_lheight'] . 'px' : '300px') ?>';
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