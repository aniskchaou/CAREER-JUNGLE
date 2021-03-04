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
class SingleCandidateExperience extends Widget_Base {

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
        return 'single-candidate-experience';
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
        return __('Single Candidate Experience', 'wp-jobsearch');
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

        $inopt_resm_experience = isset($jobsearch_plugin_options['cand_resm_experience']) ? $jobsearch_plugin_options['cand_resm_experience'] : '';

        ob_start();
        if (!$cand_profile_restrict::cand_field_is_locked('exp_defields', 'detail_page')) {
            ob_start();
            $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_title', true);
            $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_experience_description', true);
            $experience_start_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_start_date', true);
            $experience_end_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_end_date', true);
            $experience_prsnt_datefield_list = get_post_meta($candidate_id, 'jobsearch_field_experience_date_prsnt', true);
            $experience_company_field_list = get_post_meta($candidate_id, 'jobsearch_field_experience_company', true);
            if (is_array($exfield_list) && sizeof($exfield_list) > 0) {

                $exfield_counter = 0;
                ?>
                <div class="jobsearch-candidate-title">
                    <h2>
                        <i class="jobsearch-icon jobsearch-social-media"></i> <?php esc_html_e('Experience', 'wp-jobsearch') ?>
                    </h2>
                </div>
                <div class="jobsearch-candidate-timeline">
                    <ul class="jobsearch-row">
                        <?php
                        foreach ($exfield_list as $exfield) {
                            $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                            $experience_start_datefield_val = isset($experience_start_datefield_list[$exfield_counter]) ? $experience_start_datefield_list[$exfield_counter] : '';
                            $experience_end_datefield_val = isset($experience_end_datefield_list[$exfield_counter]) ? $experience_end_datefield_list[$exfield_counter] : '';
                            $experience_prsnt_datefield_val = isset($experience_prsnt_datefield_list[$exfield_counter]) ? $experience_prsnt_datefield_list[$exfield_counter] : '';
                            $experience_end_companyfield_val = isset($experience_company_field_list[$exfield_counter]) ? $experience_company_field_list[$exfield_counter] : '';
                            ?>
                            <li class="jobsearch-column-12">
                                <?php
                                if ($experience_prsnt_datefield_val == 'on') {
                                    ?>
                                    <small><?php echo ($experience_start_datefield_val != '' ? date('Y', strtotime($experience_start_datefield_val)) : '') . (' - ') . esc_html__('Present', 'wp-jobsearch') ?></small>
                                    <?php
                                } else {
                                    ?>
                                    <small><?php echo ($experience_start_datefield_val != '' ? date('Y', strtotime($experience_start_datefield_val)) : '') . ($experience_end_datefield_val != '' ? ' - ' . date('Y', strtotime($experience_end_datefield_val)) : '') ?></small>
                                    <?php
                                }
                                ?>
                                <div class="jobsearch-candidate-timeline-text">
                                    <span><?php echo($experience_end_companyfield_val) ?></span>
                                    <?php
                                    echo apply_filters('jobsearch_cand_det_resume_axpr_list_aftr_comp', '', $candidate_id, $exfield_counter);
                                    ?>
                                    <h2><a><?php echo jobsearch_esc_html($exfield) ?></a></h2>
                                    <p><?php echo jobsearch_esc_html($exfield_val) ?></p>
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
            $expu_html = ob_get_clean();
            if ($inopt_resm_experience != 'off') {
                echo apply_filters('jobsearch_candidate_detail_exprience_html', $expu_html, $candidate_id);
            }
        }
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template() {
        
    }

}
