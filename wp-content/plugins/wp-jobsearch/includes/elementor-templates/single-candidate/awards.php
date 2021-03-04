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
class SingleCandidateAwards extends Widget_Base {

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
        return 'single-candidate-awards';
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
        return __('Single Candidate Honors & Awards', 'wp-jobsearch');
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
        $candidate_id = isset($post->ID) ? $post->ID : '';
        $cand_profile_restrict = new Candidate_Profile_Restriction;

        $inopt_resm_honsawards = isset($jobsearch_plugin_options['cand_resm_honsawards']) ? $jobsearch_plugin_options['cand_resm_honsawards'] : '';

        ob_start();
        if (!$cand_profile_restrict::cand_field_is_locked('awards_defields', 'detail_page')) {
            if ($inopt_resm_honsawards != 'off') {
                $exfield_list = get_post_meta($candidate_id, 'jobsearch_field_award_title', true);
                $exfield_list_val = get_post_meta($candidate_id, 'jobsearch_field_award_description', true);
                $award_yearfield_list = get_post_meta($candidate_id, 'jobsearch_field_award_year', true);
                if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
                    ?>
                    <div class="jobsearch-candidate-title"><h2><i
                                class="jobsearch-icon jobsearch-trophy"></i> <?php esc_html_e('Honors & awards', 'wp-jobsearch') ?>
                        </h2></div>
                    <div class="jobsearch-candidate-timeline">
                        <ul class="jobsearch-row">
                            <?php
                            $exfield_counter = 0;
                            foreach ($exfield_list as $exfield) {
                                $rand_num = rand(1000000, 99999999);

                                $exfield_val = isset($exfield_list_val[$exfield_counter]) ? $exfield_list_val[$exfield_counter] : '';
                                $award_yearfield_val = isset($award_yearfield_list[$exfield_counter]) ? $award_yearfield_list[$exfield_counter] : '';
                                ?>
                                <li class="jobsearch-column-12">
                                    <small><?php echo($award_yearfield_val) ?></small>
                                    <div class="jobsearch-candidate-timeline-text">
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
            }
        }
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template() {
        
    }

}
