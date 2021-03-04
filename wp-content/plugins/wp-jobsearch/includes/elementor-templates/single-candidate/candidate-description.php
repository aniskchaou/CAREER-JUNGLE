<?php

namespace WP_JobsearchCandElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use WP_Jobsearch\Candidate_Profile_Restriction;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleCandidateDescription extends Widget_Base
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
        return 'single-candidate-desc';
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
        return __('Single Candidate Description', 'wp-jobsearch');
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
        return ['jobsearch-cand-single'];
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
            'section_style',
            [
                'label' => __('Style', 'elementor-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => __('Alignment', 'elementor-pro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'elementor-pro'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'elementor-pro'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'elementor-pro'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'elementor-pro'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'elementor-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => 'color: {{VALUE}};',
                ],
                'global' => [
                    'default' => Global_Colors::COLOR_TEXT,
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        global $post;
        $candidate_id = is_admin() ? jobsearch_candidate_id_elementor() : $post->ID;
        $cand_profile_restrict = new Candidate_Profile_Restriction;

        $candidate_obj = get_post($candidate_id);
        $candidate_content = $candidate_obj->post_content;

        $query_arg = isset($_GET['action']) ? $_GET['action'] : '';
        if (!is_admin()) {
            $candidate_content = apply_filters('the_content', $candidate_content);
        }
        ob_start();
        if ($candidate_content != '') {
            if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|about_desc', 'detail_page')) {
                ob_start();
                ?>
                <div class="jobsearch-description">
                    <?php echo jobsearch_esc_wp_editor($candidate_content) ?>
                </div>
                <?php
                $desc_html = ob_get_clean();
                echo apply_filters('jobsearch_cand_dash_detel_contnt_html', $desc_html, $candidate_id);
            }
        }
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    {

    }

}
