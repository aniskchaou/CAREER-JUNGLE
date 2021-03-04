<?php

namespace Wp_JobsearchElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleJobLogo extends Widget_Base
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
        return 'single-job-logo';
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
        return __('Single Job Logo', 'wp-jobsearch');
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
                'label' => __('Job Logo Settings', 'wp-jobsearch'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'featured_tag', [
                'label' => __('Featured Tag', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'urgent_tag', [
                'label' => __('Urgent Tag', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        global $post;
        ob_start();
        $job_id = is_admin() ? jobsearch_job_id_elementor() : $post->ID;


        $atts = $this->get_settings_for_display();
        extract(shortcode_atts(array(
            'featured_tag' => '',
            'urgent_tag' => '',
        ), $atts));

        $post_thumbnail_id = jobsearch_job_get_profile_image($job_id);
        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'jobsearch-job-medium');
        $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
        $post_thumbnail_src = $post_thumbnail_src == '' ? jobsearch_no_image_placeholder() : $post_thumbnail_src;
        $post_thumbnail_src = apply_filters('jobsearch_jobemp_image_src', $post_thumbnail_src, $job_id);

        $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
        $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);


        if ($post_thumbnail_src != '') { ?>
            <div class="elementor-sec-joblogo">
                <span>
                    <?php
                    if ($urgent_tag == 'yes') {
                       echo jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'job_listv1');
                    } ?>
                    <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">

                    <?php if ($jobsearch_job_featured == 'on' && $featured_tag == 'yes') {
                        $featured_class = 'promotepof-badge'; ?>
                        <span class="<?php echo($featured_class) ?>"><i class="fa fa-star"></i></span>
                    <?php } ?>
                </span>
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
