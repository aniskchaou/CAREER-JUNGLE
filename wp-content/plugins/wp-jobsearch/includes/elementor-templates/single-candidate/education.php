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
class SingleCandidateEducation extends Widget_Base {

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
        return 'single-candidate-education';
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
        return __('Single Candidate Education', 'wp-jobsearch');
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
        
    }

    protected function render() {
        global $post, $jobsearch_plugin_options;
        $candidate_id = is_admin() ? jobsearch_candidate_id_elementor() : $post->ID;
        $cand_profile_restrict = new Candidate_Profile_Restriction;

        $inopt_resm_education = isset($jobsearch_plugin_options['cand_resm_education']) ? $jobsearch_plugin_options['cand_resm_education'] : '';
        ob_start();
        if (!$cand_profile_restrict::cand_field_is_locked('edu_defields', 'detail_page')) {
            $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_education_title', true);
            $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_education_description', true);
            $education_academyfield_list = get_post_meta($candidate_id, 'jobsearch_field_education_academy', true);
            $education_yearfield_list = get_post_meta($candidate_id, 'jobsearch_field_education_year', true);
            $education_start_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_education_start_date', true);
            $education_end_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_education_end_date', true);
            $education_prsnt_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_education_date_prsnt', true);

            $edu_start_metaexist = metadata_exists('post', $candidate_id, 'jobsearch_field_education_start_date');

            ob_start();
            if (!empty($exfield_list)) { ?>
                <div class="jobsearch-candidate-title"><h2><i
                                class="jobsearch-icon jobsearch-mortarboard"></i> <?php esc_html_e('Education', 'wp-jobsearch') ?>
                    </h2></div>
                <div class="jobsearch-candidate-timeline">
                    <ul class="jobsearch-row">
                        <?php
                        $exfield_counter = 0;
                        foreach ($exfield_list as $exfield) {
                            $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                            $education_academyfield_val = isset($education_academyfield_list[$exfield_counter]) ? $education_academyfield_list[$exfield_counter] : '';
                            $education_yearfield_val = isset($education_yearfield_list[$exfield_counter]) ? $education_yearfield_list[$exfield_counter] : '';
                            $education_start_datefield_val = isset($education_start_datefield_list[$exfield_counter]) ? $education_start_datefield_list[$exfield_counter] : '';
                            $education_end_datefield_val = isset($education_end_datefield_list[$exfield_counter]) ? $education_end_datefield_list[$exfield_counter] : '';
                            $education_prsnt_datefield_val = isset($education_prsnt_datefield_list[$exfield_counter]) ? $education_prsnt_datefield_list[$exfield_counter] : '';
                            ?>
                            <li class="jobsearch-column-12">
                                <?php
                                if ($edu_start_metaexist) {
                                    if ($education_prsnt_datefield_val == 'on') {
                                        ?>
                                        <small><?php echo ($education_start_datefield_val != '' ? date('Y', strtotime($education_start_datefield_val)) : '') . (' - ') . esc_html__('Present', 'wp-jobsearch') ?></small>
                                        <?php
                                    } else {
                                        ?>
                                        <small><?php echo ($education_start_datefield_val != '' ? date('Y', strtotime($education_start_datefield_val)) : '') . ($education_end_datefield_val != '' ? ' - ' . date('Y', strtotime($education_end_datefield_val)) : '') ?></small>
                                        <?php
                                    }
                                } else { ?>
                                    <small><?php echo($education_yearfield_val) ?></small>
                                <?php } ?>
                                <div class="jobsearch-candidate-timeline-text">
                                    <span><?php echo($education_academyfield_val) ?></span>
                                    <?php
                                    echo apply_filters('jobsearch_cand_det_resume_edu_list_aftr_inst', '', $candidate_id, $exfield_counter);
                                    ?>
                                    <h2><a><?php echo($exfield) ?></a></h2>
                                    <p><?php echo($exfield_val) ?></p>
                                </div>
                            </li>
                            <?php
                            $exfield_counter++;
                        }
                        ?>
                    </ul>
                </div>
                <?php
            }
            $edu_html = ob_get_clean();

            if ($inopt_resm_education != 'off') {
                echo apply_filters('jobsearch_candidate_detail_education_html', $edu_html, $candidate_id);
            }
        }
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template() {
        
    }

}
