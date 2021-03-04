<?php

namespace Wp_JobsearchElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleEmpTeamMembers extends Widget_Base
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
        return 'single-emp-team-members';
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
        return __('Single Employer Team Members', 'wp-jobsearch');
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
        return ['jobsearch-emp-single'];
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
                'label' => __('Employer Team Members Settings', 'wp-jobsearch'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        global $post;
        $employer_id = is_admin() ? jobsearch_employer_id_elementor() : $post->ID;

        $exfield_list = get_post_meta($employer_id, 'jobsearch_field_team_title', true);
        $exfield_list_val = get_post_meta($employer_id, 'jobsearch_field_team_description', true);
        $team_designationfield_list = get_post_meta($employer_id, 'jobsearch_field_team_designation', true);
        $team_experiencefield_list = get_post_meta($employer_id, 'jobsearch_field_team_experience', true);
        $team_imagefield_list = get_post_meta($employer_id, 'jobsearch_field_team_image', true);
        $team_facebookfield_list = get_post_meta($employer_id, 'jobsearch_field_team_facebook', true);
        $team_googlefield_list = get_post_meta($employer_id, 'jobsearch_field_team_google', true);
        $team_twitterfield_list = get_post_meta($employer_id, 'jobsearch_field_team_twitter', true);
        $team_linkedinfield_list = get_post_meta($employer_id, 'jobsearch_field_team_linkedin', true);

        ob_start();

        if (is_array($exfield_list) && sizeof($exfield_list) > 0) {
            $total_team = sizeof($exfield_list);
            $rand_num_ul = rand(1000000, 99999999);
            ?>
            <div class="jobsearch-employer-wrap-section">
                <div class="jobsearch-content-title jobsearch-addmore-space">
                    <h2><?php echo apply_filters('jobsearch_emp_detail_team_hdingtxt', sprintf(esc_html__('Team Members (%s)', 'wp-jobsearch'), $total_team)); ?></h2>
                </div>
                <div class="jobsearch-candidate jobsearch-candidate-grid">
                    <ul id="members-holder-<?php echo absint($rand_num_ul) ?>" class="jobsearch-row">
                        <?php
                        $exfield_counter = 0;
                        foreach ($exfield_list as $exfield) {
                            $rand_num = rand(1000000, 99999999);

                            $exfield_val = isset($exfield_list_val[$exfield_counter]) ? jobsearch_esc_html($exfield_list_val[$exfield_counter]) : '';
                            $team_designationfield_val = isset($team_designationfield_list[$exfield_counter]) ? jobsearch_esc_html($team_designationfield_list[$exfield_counter]) : '';
                            $team_experiencefield_val = isset($team_experiencefield_list[$exfield_counter]) ? jobsearch_esc_html($team_experiencefield_list[$exfield_counter]) : '';
                            $team_imagefield_val = isset($team_imagefield_list[$exfield_counter]) ? jobsearch_esc_html($team_imagefield_list[$exfield_counter]) : '';
                            $team_facebookfield_val = isset($team_facebookfield_list[$exfield_counter]) ? jobsearch_esc_html($team_facebookfield_list[$exfield_counter]) : '';
                            $team_googlefield_val = isset($team_googlefield_list[$exfield_counter]) ? jobsearch_esc_html($team_googlefield_list[$exfield_counter]) : '';
                            $team_twitterfield_val = isset($team_twitterfield_list[$exfield_counter]) ? jobsearch_esc_html($team_twitterfield_list[$exfield_counter]) : '';
                            $team_linkedinfield_val = isset($team_linkedinfield_list[$exfield_counter]) ? jobsearch_esc_html($team_linkedinfield_list[$exfield_counter]) : '';
                            $team_imagefield_imgid = jobsearch_get_attachment_id_from_url($team_imagefield_val);

                            ?>
                            <li class="jobsearch-column-4">
                                <script>
                                    jQuery(document).ready(function () {
                                        jQuery('a[id^="fancybox_notes"]').fancybox({
                                            'titlePosition': 'inside',
                                            'transitionIn': 'elastic',
                                            'transitionOut': 'elastic',
                                            'width': 400,
                                            'height': 250,
                                            'padding': 40,
                                            'autoSize': false
                                        });
                                    });
                                </script>
                                <figure>
                                    <?php if ($team_imagefield_imgid > 0) { ?>
                                        <a id="fancybox_notes<?php echo($rand_num) ?>"
                                           href="#notes<?php echo($rand_num) ?>"
                                           class="jobsearch-candidate-grid-thumb">
                                            <img src="<?php echo($team_imagefield_val) ?>" alt="">
                                            <span class="jobsearch-candidate-grid-status"></span>
                                        </a>
                                    <?php } ?>
                                    <figcaption>
                                        <h2><a id="fancybox_notes_txt<?php echo($rand_num) ?>"
                                               href="#notes<?php echo($rand_num) ?>"><?php echo jobsearch_esc_html($exfield) ?></a>
                                        </h2>
                                        <p><?php echo($team_designationfield_val) ?></p>
                                        <?php
                                        if ($team_experiencefield_val != '') {
                                            echo '<span>' . sprintf(esc_html__('Experience: %s', 'wp-jobsearch'), $team_experiencefield_val) . '</span>';
                                        }
                                        ?>
                                    </figcaption>
                                </figure>

                                <div id="notes<?php echo($rand_num) ?>"
                                     style="display: none;"><?php echo($exfield_val) ?></div>
                                <?php if ($team_facebookfield_val != '' || $team_googlefield_val != '' || $team_twitterfield_val != '' || $team_linkedinfield_val != '') { ?>
                                    <ul class="jobsearch-social-icons">
                                        <?php if ($team_facebookfield_val != '') { ?>
                                            <li><a href="<?php echo($team_facebookfield_val) ?>"
                                                   data-original-title="facebook"
                                                   class="jobsearch-icon jobsearch-facebook-logo"></a>
                                            </li>
                                            <?php
                                        }
                                        if ($team_googlefield_val != '') { ?>
                                            <li><a href="<?php echo($team_googlefield_val) ?>"
                                                   data-original-title="google-plus"
                                                   class="jobsearch-icon jobsearch-google-plus-logo-button"></a>
                                            </li>
                                            <?php
                                        }
                                        if ($team_twitterfield_val != '') { ?>
                                            <li><a href="<?php echo($team_twitterfield_val) ?>"
                                                   data-original-title="twitter"
                                                   class="jobsearch-icon jobsearch-twitter-logo"></a>
                                            </li>
                                            <?php
                                        }
                                        if ($team_linkedinfield_val != '') {
                                            ?>
                                            <li><a href="<?php echo($team_linkedinfield_val) ?>"
                                                   data-original-title="linkedin"><i
                                                            class="jobsearch-icon jobsearch-linkedin-button"></i></a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                    <?php
                                }
                                ?>
                            </li>
                            <?php
                            $exfield_counter++;

                            if ($exfield_counter >= 3) {
                                break;
                            }
                        }
                        ?>
                    </ul>
                </div>
                <?php
                $reults_per_page = 3;
                $total_pages = 1;
                if ($total_team > 0 && $reults_per_page > 0 && $total_team > $reults_per_page) {
                    $total_pages = ceil($total_team / $reults_per_page);
                    ?>
                    <div class="jobsearch-load-more">
                        <a class="load-more-team" href="javascript:void(0);"
                           data-id="<?php echo($employer_id) ?>" data-pref="jobsearch"
                           data-rand="<?php echo($rand_num_ul) ?>"
                           data-pages="<?php echo($total_pages) ?>"
                           data-page="1"><?php esc_html_e('Load More', 'wp-jobsearch') ?></a>
                    </div>
                <?php } ?>
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
