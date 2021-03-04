<?php

namespace Wp_JobsearchElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleEmpActiveJobs extends Widget_Base
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
        return 'single-emp-active-jobs';
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
        return __('Single Employer Active Jobs', 'wp-jobsearch');
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
                'label' => __('Employer Active Jobs Settings', 'wp-jobsearch'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        global $post;


        $employer_id = is_admin() ? jobsearch_employer_id_elementor() : $post->ID;

        $user_id = jobsearch_get_employer_user_id($employer_id);
        $user_obj = get_user_by('ID', $user_id);
        $user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
        $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);

        $actjob_per_page = 5;
        $default_date_time_formate = 'd-m-Y H:i:s';
        $args = array(
            'posts_per_page' => $actjob_per_page,
            'paged' => 1,
            'post_type' => 'job',
            'post_status' => 'publish',
            'order' => 'DESC',
            'orderby' => 'ID',
            'meta_query' => array(
                array(
                    'key' => 'jobsearch_field_job_expiry_date',
                    'value' => strtotime(current_time($default_date_time_formate, 1)),
                    'compare' => '>=',
                ),
                array(
                    'key' => 'jobsearch_field_job_status',
                    'value' => 'approved',
                    'compare' => '=',
                ),
                array(
                    'key' => 'jobsearch_field_job_posted_by',
                    'value' => $employer_id,
                    'compare' => '=',
                ),
            ),
        );
        $args = apply_filters('jobsearch_employer_rel_jobs_query_args', $args);
        $jobs_query = new \WP_Query($args);

        $total_active_jobs = $jobs_query->found_posts;
        ob_start();
        if ($jobs_query->have_posts()) { ?>
            <div class="jobsearch-margin-top">
                <div class="jobsearch-section-title">
                    <h2><?php printf(esc_html__('Active Jobs From %s', 'wp-jobsearch'), $user_displayname) ?></h2>
                </div>
                <?php
                ob_start();
                ?>
                <div class="jobsearch-job jobsearch-joblisting-classic jobsearch-jobdetail-joblisting jobsearch-empdetail-activejobs">
                    <ul class="jobsearch-row">
                        <?php
                        while ($jobs_query->have_posts()) : $jobs_query->the_post();
                            $job_id = get_the_ID();
                            jobsearch_employer_det_active_job_html($job_id);
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </ul>
                    <?php
                    if ($total_active_jobs > $actjob_per_page) {
                        $total_pages = ceil($total_active_jobs / $actjob_per_page);
                        ?>
                        <div class="lodmore-jobs-btnsec">
                            <a href="javascript:void(0);"
                               class="lodmore-empactjobs-btn jobsearch-bgcolor"
                               data-id="<?php echo($employer_id) ?>"
                               data-tpages="<?php echo($total_pages) ?>"
                               data-gtopage="2"><?php esc_html_e('Load More', 'wp-jobsearch') ?></a>
                        </div>
                    <?php } ?>
                </div>
                <?php
                $activ_jobs_html = ob_get_clean();
                echo apply_filters('jobsearch_employer_detail_active_jobs_html', $activ_jobs_html, $jobs_query);
                ?>
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
