<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
if (!defined('ABSPATH')) exit;


/**
 * @since 1.1.0
 */
class HowItWorks extends Widget_Base
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
        return 'how-it-works';
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
        return __('How It Works', 'careerfy-frame');
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
            'step_1_section',
            [
                'label' => __('Step 1 Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'step_1_image',
            [
                'label' => __('Image', 'careerfy-frame'),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'step_1_image_desc',
            [
                'label' => __('Image Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
                'description' => __('Description will show below Image.', 'careerfy-frame'),
            ]
        );

        $this->add_control(
            'step_1_title',
            [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'step_1_desc',
            [
                'label' => __('Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'step_2_section',
            [
                'label' => __('Step 2 Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'step_2_image',
            [
                'label' => __('Image', 'careerfy-frame'),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );


        $this->add_control(
            'step_2_icon',
            [
                'label' => __('Icon', 'careerfy-frame'),
                'type' => Controls_Manager::ICONS,
                'description' => __('', 'careerfy-frame'),
            ]
        );
        $this->add_control(
            'step_2_icon_color',
            [
                'label' => __('Icon Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'description' => __('Icon color will only effect on style 2.', 'careerfy-frame'),
            ]
        );

        $this->add_control(
            'step_2_image_desc',
            [
                'label' => __('Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
                'description' => esc_html__('Description will show below Image.', 'careerfy-frame'),
            ]
        );

        $this->add_control(
            'step_2_title',
            [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'step_2_desc',
            [
                'label' => __('Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'step_3_section',
            [
                'label' => __('Step 3 Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'step_3_icon',
            [
                'label' => __('Icon', 'careerfy-frame'),
                'type' => Controls_Manager::ICONS,
            ]
        );

        $repeater->add_control(
            'step_3_icon_color',
            [
                'label' => __('Icon Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
            ]
        );

        $repeater->add_control(
            'step_3_title',
            [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $repeater->add_control(
            'step_3_checked_1',
            [
                'label' => __('Checked', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'step_3_opts',
            [
                'label' => __('Add Process item', 'careerfy-frame'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ step_3_title }}}',
            ]
        );


        $this->add_control(
            'step_3_title',
            [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'step_3_desc',
            [
                'label' => __('Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'step_4_section',
            [
                'label' => __('Step 4 Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'step_4_image',
            [
                'label' => __('Image', 'careerfy-frame'),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'step_4_title',
            [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'step_4_desc',
            [
                'label' => __('Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );
        $this->end_controls_section();
    }

    protected function HowItWorksList()
    {
        $atts = $this->get_settings_for_display();

        global $view;
        extract(shortcode_atts(array(
            'step_1_image' => '',
            'step_1_image_desc' => '',
            'step_1_title' => '',
            'step_1_desc' => '',
            'step_2_image' => '',
            'step_2_icon' => '',
            'step_2_image_desc' => '',
            'step_2_title' => '',
            'step_2_desc' => '',
            'step_2_icon_color' => '',
            'step_3_opts' => '',
            'step_3_title' => '',
            'step_3_desc' => '',
            'step_4_image' => '',
            'step_4_title' => '',
            'step_4_desc' => '',
            'step_4_icon' => '',
            'step_4_icon_color' => '',
        ), $atts));
        $step_1_image = $step_1_image != "" ? $step_1_image['url'] : jobsearch_employer_image_placeholder();
        $step_2_image = $step_2_image != "" ? $step_2_image['url'] : jobsearch_employer_image_placeholder();
        $step_4_image = $step_4_image != "" ? $step_4_image['url'] : jobsearch_employer_image_placeholder();

        $carrerfy_step_2_icon_color = '';
        if ($step_2_icon_color != "") {
            $carrerfy_step_2_icon_color = 'style="color: ' . $step_2_icon_color . '"';
        }


        if (!empty($step_1_title)) {
            ?>
            <li class="col-md-12">
                <figure>
                    <a href="#"><img src="<?php echo esc_url($step_1_image) ?>" alt=""></a>
                    <figcaption>
                        <span><?php echo $step_1_image_desc ?></span>
                    </figcaption>
                </figure>
                <div class="careerfy-howit-works-text">
                    <small><?php echo esc_html__('01', 'careerfy-frame') ?></small>
                    <span><?php echo $step_1_title ?></span>
                    <p><?php echo $step_1_desc ?></p>
                </div>
            </li>
        <?php } ?>
        <?php if ($step_2_title != "") { ?>
        <li class="col-md-12 flip-list">
            <figure>
                <a href="#"><img src="<?php echo $step_2_image ?>" alt=""></a>
                <figcaption>
                    <span><i class="<?php echo $step_2_icon['value'] ?>" <?php echo $carrerfy_step_2_icon_color ?>></i><?php echo esc_html__($step_2_image_desc) ?></span>
                </figcaption>
            </figure>
            <div class="careerfy-howit-works-text">
                <small><?php echo esc_html__('02', 'careerfy-frame') ?></small>
                <span><?php echo $step_2_title ?></span>
                <p><?php echo $step_2_desc ?></p>
            </div>
        </li>
    <?php } ?>
        <?php if ($step_3_title != "") { ?>
        <li class="col-md-12 careerfy-spam-list">
            <figure>
                <figcaption>
                    <?php
                    foreach ($step_3_opts as $options) {
                        $icon_color = isset($options['step_3_icon_color']) && $options['step_3_icon_color'] != "" ? 'style="color: ' . $options['step_3_icon_color'] . '"  ' : '';
                        $option_status = isset($options['step_3_checked_1']) && $options['step_3_checked_1'] == "yes" ? '<em class="careerfy-icon careerfy-checked"></em>' : '<em class="fa fa-times"></em>';
                        ?>
                        <span><i class="<?php echo $options['step_3_icon']['value'] ?>"  <?php echo $icon_color ?>></i> <?php echo $options['step_3_title'] ?> <?php echo $option_status ?></span>
                        <br/>
                    <?php } ?>
                </figcaption>
            </figure>
            <div class="careerfy-howit-works-text">
                <small><?php echo esc_html__('03', 'careerfy-frame') ?></small>
                <?php if ($step_3_title != "") { ?>
                    <span><?php echo $step_3_title ?></span>
                <?php } ?>
                <?php if ($step_3_desc != "") { ?>
                    <p><?php echo $step_3_desc ?></p>
                <?php } ?>
            </div>
        </li>
    <?php } ?>
        <?php if ($step_4_title != "") { ?>
        <li class="col-md-12 flip-list">
            <figure>
                <a href="#"><img src="<?php echo $step_4_image ?>" alt=""></a>
            </figure>
            <div class="careerfy-howit-works-text">
                <small><?php echo esc_html__('04', 'careerfy-frame'); ?></small>
                <span><?php echo $step_4_title ?></span>
                <p><?php echo $step_4_desc ?></p>
            </div>
        </li>
    <?php } ?>
        <?php

    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();

        global $view;
        ob_start();
        $parent_class = 'careerfy-howit-works-list';
        ?>
        <div class="<?php echo $parent_class ?>">
            <ul class="row">
                <?php echo $this->HowItWorksList(); ?>
            </ul>
        </div>

        <?php $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    {

    }
}