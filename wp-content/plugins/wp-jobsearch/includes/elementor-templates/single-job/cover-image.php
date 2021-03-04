<?php

namespace Wp_JobsearchElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleJobCoverImage extends Widget_Base {

    /**
     * Retrieve the widget name.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'single-job-coverimg';
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
    public function get_title() {
        return __('Single Job Cover Image', 'wp-jobsearch');
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
    public function get_icon() {
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
    public function get_categories() {
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
    protected function _register_controls() {
        $this->start_controls_section(
                'content_section', [
            'label' => __('Job Cover Image Settings', 'wp-jobsearch'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );
        $this->end_controls_section();
    }

    protected function render() {
        global $post, $jobsearch_plugin_options;
        $job_id = is_admin() ? jobsearch_job_id_elementor() : $post->ID;

        $employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
        
        $employer_cover_image_src_style_str = '';
        if ($employer_id != '') {
            if (class_exists('JobSearchMultiPostThumbnails')) {
                $employer_cover_image_src = \JobSearchMultiPostThumbnails::get_post_thumbnail_url('employer', 'cover-image', $employer_id);
                if ($employer_cover_image_src != '') {
                    $employer_cover_image_src_style_str = ' style="background:url(' . esc_url($employer_cover_image_src) . ') center/cover no-repeat"';
                }
            }
        }
        if ($employer_cover_image_src_style_str == '') {
            $emp_def_cvrimg = isset($jobsearch_plugin_options['emp_default_coverimg']['url']) && $jobsearch_plugin_options['emp_default_coverimg']['url'] != '' ? $jobsearch_plugin_options['emp_default_coverimg']['url'] : '';
            $employer_cover_image_src_style_str = ' style="background:url(' . esc_url($emp_def_cvrimg) . '); center/cover no-repeat "';
        }
        
        $subheader_bg_color = '';
        $subheader_employer_bg_color = isset($jobsearch_plugin_options['careerfy-emp-img-overlay-bg-color']) ? $jobsearch_plugin_options['careerfy-emp-img-overlay-bg-color'] : '';
        if (isset($subheader_employer_bg_color['rgba'])) {
            $subheader_bg_color = $subheader_employer_bg_color['rgba'];
        }
        ob_start();
        if ($employer_cover_image_src_style_str != '') { ?>
            <div class="jobsearch-job-subheader"<?php echo ($employer_cover_image_src_style_str); ?>>
                <span class="jobsearch-banner-transparent" style="background: <?php echo !empty($subheader_bg_color) ? $subheader_bg_color : 'rgb(48, 56, 68, 0.50)' ?>"></span>
                <div class="jobsearch-plugin-default-container">
                    <div class="jobsearch-row">
                        <div class="jobsearch-column-12"></div>
                    </div>
                </div>
            </div>
            <?php
        }
        
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template() {
        
    }

}
