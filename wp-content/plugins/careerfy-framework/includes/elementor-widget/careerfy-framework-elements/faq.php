<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;


/**
 * @since 1.1.0
 */
class FAQ extends Widget_Base
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
        return 'faq';
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
        return __('FAQ', 'careerfy-frame');
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
        $categories = get_terms(array(
            'taxonomy' => 'faq-category',
            'hide_empty' => false,
        ));

        $cate_array = array(esc_html__("Select Category", "careerfy-frame") => '');
        if (is_array($categories) && sizeof($categories) > 0) {
            foreach ($categories as $category) {
                $cate_array[$category->name] = $category->slug;
            }
        }
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('About Info Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'view',
            [
                'label' => __('Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'style1',
                'options' => [
                    'style1' => __('Style 1', 'careerfy-frame'),
                    'style2' => __('Style 2', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'ques_title',
            [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'op_first_q',
            [
                'label' => __('Open First Question', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'faq_cat',
            [
                'label' => __('Category', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'options' => $cate_array,
            ]
        );

        $this->add_control(
            'faq_excerpt',
            [
                'label' => __('Excerpt Length', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '20',
                'description' => __("Set the number of words you want to show for faq answer.", "careerfy-frame")
            ]
        );

        $this->add_control(
            'faq_orderby',
            [
                'label' => __('Orderby', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'date' => __('Date', 'careerfy-frame'),
                    'title' => __('Title', 'careerfy-frame'),
                ],
                'description' => __("Choose faq list items orderby.", "careerfy-frame")
            ]
        );

        $this->add_control(
            'faq_order',
            [
                'label' => __('Order', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'DESC',
                'options' => [
                    'DESC' => __('Descending', 'careerfy-frame'),
                    'ASC' => __('Ascending', 'careerfy-frame'),
                ],
                'description' => __("Choose the faq list items order.", "careerfy-frame")
            ]
        );
        $this->add_control(
            'num_of_faqs',
            [
                'label' => __('No of Questions', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '10',
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {

        $atts = $this->get_settings_for_display();
        extract(shortcode_atts(array(
            'view' => '',
            'ques_title' => '',
            'op_first_q' => 'yes',
            'faq_cat' => '',
            'faq_excerpt' => '',
            'faq_order' => '',
            'faq_orderby' => '',
            'num_of_faqs' => '',
        ), $atts));

        $faq_shortcode_counter = 1;

        $faq_shortcode_rand_id = rand(10000000, 99999999);

        ob_start();

        if ($ques_title != '') { ?>
            <div class="careerfy-section-title"><h2><?php echo($ques_title) ?></h2></div>
            <?php
        }

        $num_of_faqs = $num_of_faqs == '' ? -1 : absint($num_of_faqs);
        $args = array(
            'post_type' => 'faq',
            'posts_per_page' => $num_of_faqs,
            'post_status' => 'publish',
            'order' => $faq_order,
            'orderby' => $faq_orderby,
        );

        if ($faq_cat && $faq_cat != '' && $faq_cat != '0') {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'faq-category',
                    'field' => 'slug',
                    'terms' => $faq_cat,
                ),
            );
        }

        $faq_query = new \WP_Query($args);
        $total_posts = $faq_query->found_posts;

        if ($faq_query->have_posts()) { ?>

            <?php if ($view == 'style2') { ?>
                <div class="panel-group careerfy-accordion-style2" id="accordion-<?php echo($faq_shortcode_rand_id) ?>">
                    <?php
                    while ($faq_query->have_posts()) : $faq_query->the_post();

                        $item_rand_id = rand(10000000, 99999999);

                        $open_faq_item = false;
                        if ($op_first_q == 'yes' && $faq_shortcode_counter == 1) {
                            $open_faq_item = true;
                        }
                        ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a <?php echo($open_faq_item ? '' : 'class="collapsed"') ?> role="button"
                                                                                                data-toggle="collapse"
                                                                                                data-parent="#accordion-<?php echo($faq_shortcode_rand_id) ?>"
                                                                                                href="#collapse-<?php echo($item_rand_id) ?>"
                                                                                                aria-expanded="true"
                                                                                                aria-controls="collapse-<?php echo($item_rand_id) ?>">

                                        <?php echo get_the_title(get_the_ID()) ?>
                                        <i class="careerfy-icon careerfy-arrow-right-fill"></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse-<?php echo($item_rand_id) ?>"
                                 class="panel-collapse collapse <?php echo($open_faq_item ? 'in' : '') ?>">
                                <div class="panel-body">
                                    <?php echo careerfy_excerpt($faq_excerpt) ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        $faq_shortcode_counter++;

                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            <?php } else { ?>
                <div class="panel-group careerfy-accordion" id="accordion-<?php echo($faq_shortcode_rand_id) ?>">
                    <?php
                    while ($faq_query->have_posts()) : $faq_query->the_post();

                        $item_rand_id = rand(10000000, 99999999);

                        $open_faq_item = false;
                        if ($op_first_q == 'yes' && $faq_shortcode_counter == 1) {
                            $open_faq_item = true;
                        }
                        ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a <?php echo($open_faq_item ? '' : 'class="collapsed"') ?> role="button"
                                                                                                data-toggle="collapse"
                                                                                                data-parent="#accordion-<?php echo($faq_shortcode_rand_id) ?>"
                                                                                                href="#collapse-<?php echo($item_rand_id) ?>"
                                                                                                aria-expanded="true"
                                                                                                aria-controls="collapse-<?php echo($item_rand_id) ?>">
                                        <i class="careerfy-icon careerfy-arrows"></i>
                                        Q. <?php echo get_the_title(get_the_ID()) ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse-<?php echo($item_rand_id) ?>"
                                 class="panel-collapse collapse <?php echo($open_faq_item ? 'in' : '') ?>">
                                <div class="panel-body">
                                    <?php echo careerfy_excerpt($faq_excerpt) ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        $faq_shortcode_counter++;

                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
                <?php
            }
        }
        $html = ob_get_clean();
        echo apply_filters('careerfy_faqs_shrtcde_html', $html, $atts);
    }

    protected function _content_template()
    {
    }
}