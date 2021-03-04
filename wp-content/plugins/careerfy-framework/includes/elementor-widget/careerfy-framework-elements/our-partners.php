<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
if (!defined('ABSPATH')) exit;

/**
 * @since 1.1.0
 */
class OurPartners extends Widget_Base
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
        return 'our-partners';
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
        return __('Our Partners', 'careerfy-frame');
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
        return 'fa fa-users';
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
                'label' => __('Our Partners Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'partner_view',
            [
                'label' => __('Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'view1',
                'options' => [
                    'view1' => __('Style 1', 'careerfy-frame'),
                    'view2' => __('Style 2', 'careerfy-frame'),
                    'view3' => __('Style 3', 'careerfy-frame'),
                    'view4' => __('Style 4', 'careerfy-frame'),
                    'view5' => __('Style 5', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'partner_title', [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'partner_img', [
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
            'partner_url', [
                'label' => __('URL', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '#'
            ]
        );

        $this->add_control(
            'careerfy_our_partners_item',
            [
                'label' => __('Our partner item', 'careerfy-frame'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => 'Item',
            ]
        );
        $this->end_controls_section();
    }

    protected function careerfy_our_partners_item_shortcode()
    {
        $atts = $this->get_settings_for_display();

        global $partner_view;

        $patr_class = 'careerfy-partner-slider-layer';
        if ($partner_view == 'view2') {
            $patr_class = 'careerfy-partnertwo-slider-layer';
        }
        if ($partner_view == 'view3') {
            $patr_class = 'careerfy-partner-style3-layer';
        }
        foreach ($atts['careerfy_our_partners_item'] as $item) {
            $partner_img = $item['partner_img'] != '' ? $item['partner_img']['url'] : '';
            $partner_url = $item['partner_url'];
            if ($partner_view == 'view4') {
                $html = '
			<li class="col-md-3">';
                if ($partner_img != "") {
                    $html .= '<a href="' . JobsearchAddHTTP($partner_url) . '" target="_blank" ><img src = "' . $partner_img . '" alt = "" ></a>';
                }
                $html .= '</li>';
            } else if ($partner_view == 'view5') {
                $html = '<div class="careerfy-partner-twelve-layer">
                <a href="' . JobsearchAddHTTP($partner_url) . '" target="_blank"><img src="' . $partner_img . '" alt=""></a></div>';
            } else {
                $html = '
    <div class="' . $patr_class . '">
        <a href="' . JobsearchAddHTTP($partner_url) . '" target="_blank"><img src="' . $partner_img . '" alt=""></a>
    </div>';
            }
            echo $html;
        }
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        global $partner_view;
        extract(shortcode_atts(array(
            'partner_view' => '',
            'partner_title' => '',
        ), $atts));
        wp_enqueue_script('careerfy-slick-slider');
        ob_start();
        $patr_class = 'careerfy-partner-slider';

        if ($partner_view == 'view2') {
            $patr_class = 'careerfy-partnertwo-slider';
        }
        if ($partner_view == 'view3') {
            $patr_class = 'careerfy-partner-style3';
        }
        if ($partner_view == 'view4') {
            $patr_class = 'careerfy-partner-style4';

            ?>

            <div class="<?php echo $patr_class ?>">
                <ul class="row">
                    <?php echo $this->careerfy_our_partners_item_shortcode() ?>
                </ul>
            </div>


        <?php } else if ($partner_view == 'view5') {

            $patr_class = 'careerfy-partner-twelve-slider';
            ?>
            <div class="<?php echo $patr_class ?>">
                <?php echo $this->careerfy_our_partners_item_shortcode() ?>
            </div>

            <script>
                var $ = jQuery;
                $(document).ready(function () {
                    $('.careerfy-partner-twelve-slider').slick({
                        slidesToShow: 6,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed: 5000,
                        infinite: true,
                        dots: false,
                        prevArrow: "<span class=\'slick-arrow-left\'><i class=\'fa fa-arrow-left\'></i></span>",
                        nextArrow: "<span class=\'slick-arrow-right\'><i class=\'fa fa-arrow-right\'></i></span>",
                        responsive: [
                            {
                                breakpoint: 1024,
                                settings: {
                                    slidesToShow: 2,
                                    slidesToScroll: 1,
                                    infinite: true,
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
                });
            </script>

        <?php } else { ?>

            <?php echo($partner_view == 'view3' ? '<div class="careerfy-partner-style3-wrap">' : '') ?>
            <?php echo($partner_title != '' ? '<span class="careerfy-partner-title">' . $partner_title . '</span>' : '') ?>

            <div class="<?php echo $patr_class ?>">
                <?php echo $this->careerfy_our_partners_item_shortcode() ?>
            </div>

            <?php echo($partner_view == 'view3' ? '</div>' : '') ?>
        <?php }
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    {
    }
}