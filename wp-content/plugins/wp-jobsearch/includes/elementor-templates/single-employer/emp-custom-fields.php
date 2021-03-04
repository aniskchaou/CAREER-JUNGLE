<?php

namespace Wp_JobsearchElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Text_Shadow;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleEmployerCustomFields extends Widget_Base
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
        return 'single-emp-custom-fields';
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
        return __('Single Employer Custom Fields', 'wp-jobsearch');
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
        return ['jobsearch-emp-single'];
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
                'label' => __('Employer Custom Fields Settings', 'wp-jobsearch'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'elem_sectors', [
                'label' => __('Sectors', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'elem_posted_date', [
                'label' => __('Posted Date', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'elem_total_views', [
                'label' => __('Total Views', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Custom Fields Styles', 'wp-jobsearch'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_button_style');

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => __('Heading', 'wp-jobsearch'),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .jobsearch-jobdetail-services .jobsearch-services-text',
            ]
        );

        $this->add_control(
            'heading_text_color',
            [
                'label' => __('Text Color', 'wp-jobsearch'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .jobsearch-jobdetail-services li .jobsearch-services-text' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );



        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .jobsearch-jobdetail-services .jobsearch-services-text',
            ]
        );


        $this->add_control(
            'icon_color',
            [
                'label' => __('Icon Color', 'wp-jobsearch'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .jobsearch-jobdetail-services li i' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'icon_size',
            array(
                'label' => __('Icon Size', 'wp-jobsearch'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('em'),
                'range' => array(
                    'em' => array(
                        'min' => 0,
                        'max' => 100,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .jobsearch-jobdetail-services li i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; font-size: calc( {{SIZE}}px / 2 );',
                ),
            )
        );


        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => __('Description', 'wp-jobsearch'),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_desc',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .jobsearch-jobdetail-services .jobsearch-services-text small',
            ]
        );

        $this->add_control(
            'color_txt_desc',
            [
                'label' => __('Text Color', 'wp-jobsearch'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .jobsearch-jobdetail-services .jobsearch-services-text small' => 'color: {{VALUE}}  ',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow_desc',
                'selector' => '{{WRAPPER}} .jobsearch-jobdetail-services .jobsearch-services-text small',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

    }

    protected function render()
    {
        global $post, $jobsearch_plugin_options;
        $atts = $this->get_settings_for_display();

        extract(shortcode_atts(array(
            'elem_sectors' => '',
            'elem_posted_date' => '',
            'elem_total_views' => '',
        ), $atts));

        $employer_id = is_admin() ? jobsearch_employer_id_elementor() : $post->ID;
        $employer_obj = get_post($employer_id);
        $employer_content = $employer_obj->post_content;
        ob_start();
        $custom_all_fields = get_option('jobsearch_custom_field_employer');
        //
        $membsectors_enable_switch = isset($jobsearch_plugin_options['usersector_onoff_switch']) ? $jobsearch_plugin_options['usersector_onoff_switch'] : '';
        $sectors_enable_switch = ($membsectors_enable_switch == 'on_emp' || $membsectors_enable_switch == 'on_both') ? 'on' : '';
        $tjobs_posted_switch = isset($jobsearch_plugin_options['empjobs_posted_count']) ? $jobsearch_plugin_options['empjobs_posted_count'] : '';
        $totl_views_switch = isset($jobsearch_plugin_options['emptotl_views_count']) ? $jobsearch_plugin_options['emptotl_views_count'] : '';
        $employer_views_count = get_post_meta($employer_id, "jobsearch_employer_views_count", true);
        ob_start();
        if (!empty($custom_all_fields) || $employer_content != '') { ?>
            <div class="jobsearch-jobdetail-content jobsearch-employerdetail-content">
                    <?php
                    ob_start();
                    if ($sectors_enable_switch == 'on' && $elem_sectors == 'yes') {
                        $sector_str = jobsearch_employer_get_all_sectors($employer_id, '', '', '', '<small>', '</small>');
                        $sector_str = apply_filters('jobsearch_gew_wout_anchr_sector_str_html', $sector_str, $employer_id, '<small>', '</small>');
                        if ($sector_str != '') {
                            ?>
                            <li class="jobsearch-column-4">
                                <i class="jobsearch-icon jobsearch-folder"></i>
                                <div class="jobsearch-services-text"><?php esc_html_e('Sectors', 'wp-jobsearch') ?><?php echo wp_kses($sector_str, array('small' => array())) ?></div>
                            </li>
                            <?php
                        }
                    }
                    if ($tjobs_posted_switch == 'on' && $elem_posted_date == 'yes') { ?>
                        <li class="jobsearch-column-4">
                            <i class="jobsearch-icon jobsearch-briefcase"></i>
                            <div class="jobsearch-services-text"><?php esc_html_e('Posted Jobs', 'wp-jobsearch') ?>
                                <small><?php echo jobsearch_employer_total_jobs_posted($employer_id) ?></small>
                            </div>
                        </li>
                        <?php
                    }
                    if ($totl_views_switch == 'on' && $elem_total_views == 'yes') { ?>
                        <li class="jobsearch-column-4">
                            <i class="jobsearch-icon jobsearch-view"></i>
                            <div class="jobsearch-services-text"><?php esc_html_e('Viewed', 'wp-jobsearch') ?>
                                <small><?php echo($employer_views_count) ?></small>
                            </div>
                        </li>
                    <?php } ?>
              
                <?php
                $extra_cus_fields = ob_get_clean();
                $cus_fields = array('content' => '');
                if (!empty($custom_all_fields)) {
                    $cus_fields = apply_filters('jobsearch_custom_fields_list', 'employer', $employer_id, $cus_fields, '<li class="jobsearch-column-4">', '</li>', '', true, true, true, 'jobsearch');
                }

                if ((isset($cus_fields['content']) && $cus_fields['content'] != '') || $extra_cus_fields != '') { ?>
                    <div class="jobsearch-content-title">
                        <h2><?php esc_html_e('Overview', 'wp-jobsearch') ?></h2>
                    </div>
                    <div class="jobsearch-jobdetail-services">
                        <ul class="jobsearch-row">
                            <?php
                            echo($extra_cus_fields);
                            //
                            echo($cus_fields['content']);
                            ?>
                        </ul>
                    </div>
                <?php } ?>
            </div>
            <?php
        }
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    {

    }

}
