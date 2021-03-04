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
class SingleJobEmployerContact extends Widget_Base
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
        return 'single-job-contact';
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
        return __('Single Job Employer Contact', 'wp-jobsearch');
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
                'label' => __('Job Employer Contact Settings', 'wp-jobsearch'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Contact Employer Button Styles', 'wp-jobsearch'),
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
                'selector' => '{{WRAPPER}} .jobsearch-elementor-employer-contact a',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .jobsearch-elementor-employer-contact a',
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
                    '{{WRAPPER}} .jobsearch-elementor-employer-contact a:hover, {{WRAPPER}} .jobsearch-elementor-employer-contact a:focus' => 'border-color: {{VALUE}};',
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
                    '{{WRAPPER}} .jobsearch-elementor-employer-contact a' => 'fill: {{VALUE}}; color: {{VALUE}};',
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
                    '{{WRAPPER}} .jobsearch-elementor-employer-contact a' => 'background-color: {{VALUE}};',
                ],
            ]
        );


        $this->end_controls_tab();

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
                    '{{WRAPPER}} .jobsearch-elementor-employer-contact a:hover, {{WRAPPER}} .jobsearch-elementor-employer-contact a:hover:focus' => 'color: {{VALUE !important;}}  ',
                ],
            ]
        );
        $this->add_control(
            'hover_color_button',
            [
                'label' => __('Button Hover Color', 'wp-jobsearch'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .jobsearch-elementor-employer-contact a:hover, {{WRAPPER}} .jobsearch-elementor-employer-contact a:hover:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();


        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} .jobsearch-elementor-employer-contact a',
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
                    '{{WRAPPER}} .jobsearch-elementor-employer-contact a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .jobsearch-elementor-employer-contact a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            ]
        );


        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name' => 'button_box_shadow_normal',
                'label' => __('Box Shadow', 'uael'),
                'selector' => '{{WRAPPER}} .jobsearch-elementor-employer-contact a',
            )
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        global $post;
        $atts = $this->get_settings_for_display();
        $job_id = is_admin() ? jobsearch_job_id_elementor() : $post->ID;
        $job_employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

        ob_start();
        if ($atts['skew_button'] == 'yes') { ?>
            <style>
                .jobsearch-elementor-employer-contact .jobsearch-sendmessage-btn {
                    transform: skewX(-20deg);
                }

                .jobsearch-elementor-employer-contact span {
                    transform: skewX(20deg);
                }

                .jobsearch-elementor-employer-contact i {
                    transform: skewX(20deg);
                }
            </style>
        <?php } ?>
        <div class="jobsearch-elementor-employer-contact">
            <?php

            $btn_text = '<span>' . esc_html('Contact Employer', 'wp-jobsearch') . '</span>';
            $popup_args = array(
                'job_employer_id' => $job_employer_id,
                'job_id' => $job_id,
                'btn_text' => $btn_text,

            );
            $popup_html = apply_filters('jobsearch_job_send_message_html_filter', '', $popup_args);
            echo force_balance_tags($popup_html);
            ?>
        </div>
        <script>
            //for login popup
            jQuery(document).on('click', '.jobsearch-sendmessage-popup-btn', function () {
                jobsearch_modal_popup_open('JobSearchModalSendMessage');
            });
            jQuery(document).on('click', '.jobsearch-sendmessage-messsage-popup-btn', function () {
                jobsearch_modal_popup_open('JobSearchModalSendMessageWarning');
            });
            jQuery(document).on('click', '.jobsearch-applyjob-msg-popup-btn', function () {
                jobsearch_modal_popup_open('JobSearchModalApplyJobWarning');
            });
        </script>
        <?php
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    { ?>
        <# if(settings.skew_button == 'yes'){ #>
        <style>
            .jobsearch-elementor-employer-contact .jobsearch-sendmessage-btn {
                transform: skewX(-20deg);
            }

            .jobsearch-elementor-employer-contact span {
                transform: skewX(20deg);
            }

            .jobsearch-elementor-employer-contact i {
                transform: skewX(20deg);
            }

        </style>
        <# } #>
        <div class="jobsearch-elementor-employer-contact">
            <a href="javascript:void(0);" class="jobsearch-sendmessage-btn jobsearch-sendmessage-popup-btn"><i
                        class="jobsearch-icon jobsearch-envelope"></i><span><?php echo esc_html__('Contact Employer', 'wp-jobsearch') ?></span></a>
        </div>
    <?php }

}
