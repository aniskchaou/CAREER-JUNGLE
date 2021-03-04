<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;


/**
 * @since 1.1.0
 */
class JobsListingsTabs extends Widget_Base
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
        return 'jobs-listings-tabs';
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
        return __('Job Listing Tabs', 'careerfy-frame');
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
        return 'fa fa-list';
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
            'taxonomy' => 'sector',
            'hide_empty' => false,
        ));
        $cate_array = array();
        if (is_array($categories) && sizeof($categories) > 0) {
            foreach ($categories as $category) {
                $cate_array[$category->name] = $category->slug;
            }
        }
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Job Listings Tabs Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'job_cats_filter',
            [
                'label' => __('Sector', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'options' => $cate_array,
                'multiple' => true,
            ]
        );
        $this->add_control(
            'job_per_page',
            [
                'label' => __('Job per page', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        global $jobsearch_plugin_options, $sitepress;
        extract(shortcode_atts(array(
            'job_cats_filter' => '',
            'job_per_page' => '',
        ), $atts));
        
        $job_per_page = isset($job_per_page) && !empty($job_per_page) ? $job_per_page : -1;
        $sector_list = $job_cats_filter;
        wp_enqueue_script('isotope-min');
        $sector_terms = get_terms(array(
            'taxonomy' => 'sector',
            'hide_empty' => false,
        ));
        ob_start();
        $html = '';
        ?>
        <div class="careerfy-animate-filter">
            <ul class="filters-button-group">
                <li><a data-filter="*" class="is-checked" href="javascript:void(0)"><?php echo esc_html__('All Categories', 'careerfy-frame'); ?></a></li>
                <?php
                if (!empty($sector_list) && !is_wp_error($sector_list)) {
                    foreach ($sector_list as $sector_list_item) {
                        if (isset($sector_list_item) && !empty($sector_list_item)) {
                            $term_data = get_term_by('slug', $sector_list_item, 'sector');
                            ?>
                            <li><a data-filter=".<?php echo ($term_data->slug) ?>" href="javascript:void(0)"><?php echo ($term_data->name) ?></a></li>
                            <?php
                        }
                    }
                }
                ?>
            </ul>
        </div>
        <div class="careerfy-job-listing careerfy-dream-grid careerfy-animated-filter-list">
            <ul class="row">
                <?php
                if (!empty($sector_list) && !is_wp_error($sector_list)) {
                    do_action('job_fiter_items', $sector_list, $job_per_page);
                }
                ?>
            </ul>
        </div>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        echo $html;
    }

    protected function _content_template()
    {

    }
}