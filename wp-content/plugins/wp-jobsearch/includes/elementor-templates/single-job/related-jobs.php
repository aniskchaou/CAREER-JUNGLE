<?php

namespace Wp_JobsearchElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleJobRelatedJobs extends Widget_Base {

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
        return 'single-job-related-jobs';
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
        return __('Single Job Related Jobs', 'wp-jobsearch');
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
            'label' => __('Job Related Jobs Settings', 'wp-jobsearch'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );

        $this->add_control(
                'jobs_num', [
            'label' => __('Number of Jobs', 'wp-jobsearch'),
            'type' => Controls_Manager::TEXT,
            'default' => '5',
                ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        global $post, $jobsearch_plugin_options;
        $job_id = is_admin() ? jobsearch_job_id_elementor() : $post->ID;

        $atts = $this->get_settings_for_display();

        extract(shortcode_atts(array(
            'jobs_num' => '',
                        ), $atts));

        $job_field_user = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

        ob_start();
        $company_job_html = jobsearch_job_related_post($job_id, '', $jobs_num, 5, 'jobsearch-job-like');
        echo force_balance_tags($company_job_html);
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template() {

    }

}
