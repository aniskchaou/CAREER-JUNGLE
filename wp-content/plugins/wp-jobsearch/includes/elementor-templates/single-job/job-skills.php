<?php

namespace Wp_JobsearchElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleJobSkills extends Widget_Base {

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
        return 'single-job-skills';
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
        return __('Single Job Skills', 'wp-jobsearch');
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
            'label' => __('Job Skills Settings', 'wp-jobsearch'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        global $post, $jobsearch_plugin_options;
        $job_id = is_admin() ? jobsearch_job_id_elementor() : $post->ID;
        
        $skills_list = jobsearch_job_get_all_skills($job_id);

        ob_start();
        if ($skills_list != '') {
            ?>
            <div class="jobsearch-jobdetail-tags">
                <?php echo ($skills_list); ?>
            </div>
            <?php
        }
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template() {
        
    }

}
