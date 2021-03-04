<?php

namespace Wp_JobsearchElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleJobApplyButtons extends Widget_Base
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
        return 'single-job-apply';
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
        return __('Single Job Apply Buttons', 'wp-jobsearch');
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
        return 'fa fa-link';
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
        return ['jobsearch-job-single'];
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
            'content_section', [
                'label' => __('Job Apply Buttons Settings', 'wp-jobsearch'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Apply Jobs Button Styles', 'wp-jobsearch'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_control(
            'skew_button', [
                'label' => __('Skew Button', 'wp-jobsearch'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => __('Alignment', 'wp-jobsearch'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'wp-jobsearch'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'wp-jobsearch'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'wp-jobsearch'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'wp-jobsearch'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'prefix_class' => 'elementor%s-align-',
                'default' => '',
            ]
        );


        $this->start_controls_tabs('tabs_button_style');

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => __('Normal', 'wp-jobsearch'),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .jobsearch-elementor-apply-job  a.jobsearch-applyjob-btn small',
            ]
        );


        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .jobsearch-elementor-apply-job  a.jobsearch-applyjob-btn small',
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label' => __('Border Color', 'wp-jobsearch'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .jobsearch-elementor-apply-job  a.jobsearch-applyjob-btn:hover, {{WRAPPER}} .jobsearch-elementor-apply-job a:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => __('Text Color', 'wp-jobsearch'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .jobsearch-elementor-apply-job  a.jobsearch-applyjob-btn' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label' => __('Background Color', 'wp-jobsearch'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_ACCENT,
                ],
                'selectors' => [
                    '{{WRAPPER}} .jobsearch-elementor-apply-job  a.jobsearch-applyjob-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        /*
         * Apply button hover text styles
         * */
        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => __('Hover', 'wp-jobsearch'),
            ]
        );


        $this->add_control(
            'hover_color_txt',
            [
                'label' => __('Text Hover Color', 'wp-jobsearch'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .jobsearch-elementor-apply-job  a.jobsearch-applyjob-btn:hover, {{WRAPPER}} .jobsearch-elementor-apply-job a:hover:focus' => 'color: {{VALUE !important;}}  ',
                ],
            ]
        );
        $this->add_control(
            'hover_color_button',
            [
                'label' => __('Button Hover Color', 'wp-jobsearch'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .jobsearch-elementor-apply-job  a.jobsearch-applyjob-btn:hover, {{WRAPPER}} .jobsearch-elementor-apply-job a:hover:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        /*
         * Apply button hover text styles section end
         * */
        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} .jobsearch-elementor-apply-job  a.jobsearch-applyjob-btn',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'padding',
            array(
                'label' => __('Padding', 'wp-jobsearch'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .jobsearch-elementor-apply-job  a.jobsearch-applyjob-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'toggle_border_radius',
            [
                'label' => __('Border Radius', 'wp-jobsearch'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'selectors' => array(
                    '{{WRAPPER}} .jobsearch-elementor-apply-job  a.jobsearch-applyjob-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name' => 'button_box_shadow_normal',
                'label' => __('Box Shadow', 'uael'),
                'selector' => '{{WRAPPER}} .jobsearch-elementor-apply-job  a.jobsearch-applyjob-btn',
            )
        );

        $this->end_controls_section();

        /*
         * Application ending text styles
         * */
        $this->start_controls_section(
            'section_application_ending_styles',
            [
                'label' => __('Application Ending Text Styles', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'application_text_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .jobsearch-elementor-apply-job  span.jobsearch-application-ending',
            ]
        );

        $this->add_control(
            'application_ending_color_button',
            [
                'label' => __('Application Ending Text Color', 'wp-jobsearch'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .jobsearch-elementor-apply-job span.jobsearch-application-ending' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'application_text_color',
                'selector' => '{{WRAPPER}} .jobsearch-elementor-apply-job  span.jobsearch-application-ending',
            ]
        );


        $this->end_controls_section();
        /*
         * Application ending text styles section end
         * */

        /*
         * Apply with  text styles
         * */
        $this->start_controls_section(
            'section_apply_with_styles',
            [
                'label' => __('Apply with Text Styles', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'apply_with_text_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .jobsearch-elementor-apply-job  .jobsearch-applywith-title small',
            ]
        );

        $this->add_control(
            'apply_with_color',
            [
                'label' => __('Apply with Text Color', 'wp-jobsearch'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .jobsearch-elementor-apply-job  .jobsearch-applywith-title small' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'apply_with_text_color',
                'selector' => '{{WRAPPER}} .jobsearch-elementor-apply-job  .jobsearch-applywith-title small',
            ]
        );


        $this->end_controls_section();
        /*
         * Apply with styles section end
         * */

        /*
         * Easy Apply text styles
         * */
        $this->start_controls_section(
            'section_easy_apply_with_styles',
            [
                'label' => __('Easy Apply Styles', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'easy_apply_with_text_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .jobsearch-elementor-apply-job .jobsearch-easy-apply-txt',
            ]
        );

        $this->add_control(
            'easy_apply_apply_with_color',
            [
                'label' => __('Easy Apply Text Color', 'wp-jobsearch'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .jobsearch-elementor-apply-job  .jobsearch-easy-apply-txt' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'txt_under_apply_with_text_color',
                'selector' => '{{WRAPPER}} .jobsearch-elementor-apply-job  .jobsearch-easy-apply-txt',
            ]
        );


        $this->end_controls_section();
        /*
         * Easy Apply styles section end
         * */

    }

    protected function render()
    {
        global $post;
        $job_id = is_admin() ? jobsearch_job_id_elementor() : $post->ID;
        $atts = $this->get_settings_for_display();
        if ($atts['skew_button'] == 'yes') { ?>
            <style>
                .jobsearch-elementor-apply-job .jobsearch-applyjob-btn {
                    transform: skewX(-20deg);
                }

                .jobsearch-elementor-apply-job a small {
                    transform: skewX(20deg);
                }

            </style>
        <?php } ?>
        <div class="jobsearch-elementor-apply-job jobsearch_apply_job">
            <?php
            echo jobsearch_job_det_applybtn_acthtml('', $job_id, 'page', 'view1');
            do_action('jobsearch_job_detail_before_footer', $job_id);
            ?>
        </div>
    <?php }

    protected function _content_template()
    { ?>
        <# if(settings.skew_button == 'yes'){ #>
        <style>
            .jobsearch-elementor-apply-job .jobsearch-applyjob-btn {
                transform: skewX(-20deg);
            }

            .jobsearch-elementor-apply-job a small {
                transform: skewX(20deg);
            }

        </style>
        <# } #>
        <div class="jobsearch-elementor-apply-job jobsearch_apply_job">
            <div class="jobsearch_apply_job_wrap">
                <a href="javascript:void(0);"
                   class="jobsearch-applyjob-btn jobsearch-job-apply-btn-con  jobsearch-nonuser-apply-btn">
                    <small><?php echo esc_html__('Apply for the job', 'wp-jobsearch') ?></small>
                </a>
                <span class="jobsearch-application-ending"><?php echo esc_html__('Application ends in  9m 10d 16h 7min', 'wp-jobsearch') ?></span>
                <div class="jobsearch-applywith-title">
                    <small><?php echo esc_html__('OR apply with', 'wp-jobsearch') ?></small>
                </div>
                <p class="jobsearch-easy-apply-txt"><?php echo esc_html__('An easy way to apply for this job. Use the following social media.', 'wp-jobsearch') ?></p>
                <ul>
                    <li><a href="javascript:void(0);" class="jobsearch-applyjob-fb-btn"><i
                                    class="jobsearch-icon jobsearch-facebook-logo-1"></i> <?php echo esc_html__('Facebook', 'wp-jobsearch') ?>
                        </a></li>
                    <li><a href="javascript:void(0);" class="jobsearch-applyjob-linkedin-btn"><i
                                    class="jobsearch-icon jobsearch-linkedin-logo"></i> <?php echo esc_html__('Linkedin', 'wp-jobsearch') ?>
                        </a></li>
                </ul>
            </div>
        </div>
        <p><?php echo esc_html__('Set the the style. Button will be functional on the front end side', 'wp-jobsearch') ?></p>
    <?php }

}
