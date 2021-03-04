<?php

namespace WP_JobsearchCandElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use WP_Jobsearch\Candidate_Profile_Restriction;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleCandidateCoverImage extends Widget_Base {

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
        return 'single-candidate-coverimg';
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
        return __('Single Candidate Cover Image', 'wp-jobsearch');
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
    protected function _register_controls() {
        $this->start_controls_section(
                'content_section', [
            'label' => __('Candidate Cover Image Settings', 'wp-jobsearch'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );
        $this->end_controls_section();
    }

    protected function render() {
        global $post, $jobsearch_plugin_options;
        $candidate_id = is_admin() ? jobsearch_candidate_id_elementor() : $post->ID;
        $cand_profile_restrict = new Candidate_Profile_Restriction;

        $candidate_cover_image_src_style_str = '';
        if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|cover_img', 'detail_page')) {
            if ($candidate_id != '') {
                $candidate_cover_image_src = '';
                if (function_exists('jobsearch_candidate_covr_url_comn')) {
                    $candidate_cover_image_src = jobsearch_candidate_covr_url_comn($candidate_id);
                }
                if ($candidate_cover_image_src != '') {
                    $candidate_cover_image_src_style_str = ' style="background:url(\'' . ($candidate_cover_image_src) . '\'); background-size:cover; "';
                }
            }
        }
        $subheader_candidate_bg_color = isset($jobsearch_plugin_options['careerfy-candidate-img-overlay-bg-color']) ? $jobsearch_plugin_options['careerfy-candidate-img-overlay-bg-color'] : '';
        if (isset($subheader_candidate_bg_color['rgba'])) {
            $subheader_bg_color = $subheader_candidate_bg_color['rgba'];
        }

        ob_start();
        
        if ($candidate_cover_image_src_style_str != '') {
            ?>
            <div class="jobsearch-job-subheader"<?php echo ($candidate_cover_image_src_style_str); ?>>
                <span class="jobsearch-banner-transparent" style="background: <?php echo ($subheader_bg_color) ?>"></span>
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
