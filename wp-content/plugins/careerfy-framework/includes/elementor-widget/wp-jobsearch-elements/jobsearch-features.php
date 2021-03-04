<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

use WP_Jobsearch\Candidate_Profile_Restriction;

if (!defined('ABSPATH')) exit;

/**
 * @since 1.1.0
 */
class JobFeatured extends Widget_Base
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
        return 'jobsearch-featured';
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
        return __('Jobsearch Featured', 'careerfy-frame');
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
        return 'fa fa-tasks';
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
        return ['wp-jobsearch'];
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
            'content_section',
            [
                'label' => __('Jobsearch Featured Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'featured_title',
            [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'featured_section',
            [
                'label' => __('Featured', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'jobs',
                'placeholder' => __('Select Featured', 'careerfy-frame'),
                'options' => [
                    'jobs' => __('Jobs', 'careerfy-frame'),
                    'employer' => __('Employers', 'careerfy-frame'),
                    'candidate' => __('Candidate', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'job_list_loc_listing',
            [
                'label' => __('Locations in listing', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'options' => [
                    'country' => __('Country', 'careerfy-frame'),
                    'state' => __('State', 'careerfy-frame'),
                    'city' => __('City', 'careerfy-frame'),
                ],
                'multiple' => true,
                'default' => ['country', 'state'],
                'description' => __("Select which type of location in listing. If nothing select then full address will display.", "careerfy-frame"),
            ]
        );

        $this->add_control(
            'featured_job_num',
            [
                'label' => __('Num of Posts', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'non_featured_items',
            [
                'label' => __('Non-Featured Items', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("If Featured Items not found show other general items.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'slide_settings',
            [
                'label' => __('Slide Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'job_slide_position',
            [
                'label' => __('Slide Position', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'options' => [
                    'vertical' => __('Vertical', 'careerfy-frame'),
                    'horizontal' => __('Horizontal', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'featured_num_slide',
            [
                'label' => __('Add num of Slides to scroll', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'description' => __("If Featured Items not found show other general items.", "careerfy-frame"),
            ]
        );
        $this->add_control(
            'featured_slide_speed',
            [
                'label' => __('Slide Speed Time', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '5000',
                'description' => __("Slide speed like 5000,7000", "careerfy-frame"),
            ]
        );
        $this->add_control(
            'slide_execution',
            [
                'label' => __('Slide Execution', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'linear',
                'options' => [
                    'linear' => __('Linear', 'careerfy-frame'),
                    'ease' => __('Ease', 'careerfy-frame'),
                    'ease-in' => __('Ease In', 'careerfy-frame'),
                    'ease-out' => __('Ease Out', 'careerfy-frame'),
                    'ease-in-out' => __('Ease In Out', 'careerfy-frame'),
                    'step-start' => __('Step Start', 'careerfy-frame'),
                    'step-end' => __('Step End', 'careerfy-frame'),
                    'initial' => __('Initial', 'careerfy-frame'),
                    'inherit' => __('Inherit', 'careerfy-frame'),
                ],
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        global $jobsearch_plugin_options, $jobsearch_shortcode_jobs_frontend;

        extract(shortcode_atts(array(
            'featured_title' => '',
            'featured_job_num' => '',
            'non_featured_items' => 'yes',
            'job_slide_position' => '',
            'featured_num_slide' => '',
            'featured_slide_speed' => 5000,
            'featured_section' => 'jobs',
            'slide_execution' => 'linear',
            'job_list_loc_listing' => 'country,city',
        ), $atts));

        if (class_exists('JobSearch_plugin')) {
            
            $cand_profile_restrict = new Candidate_Profile_Restriction;
            
            $featured_job_num = absint($featured_job_num) > 0 ? absint($featured_job_num) : '-1';

            $featured_num_slide = isset($featured_num_slide) && !empty($featured_num_slide) ? $featured_num_slide : 4;
            $featured_slide_speed = isset($featured_slide_speed) && !empty($featured_slide_speed) ? $featured_slide_speed : 5000;
            $featured_section = isset($featured_section) && !empty($featured_section) ? $featured_section : 'jobs';
            $slide_execution = isset($slide_execution) && !empty($slide_execution) ? $slide_execution : 'linear';
            $locations_view_type = isset($atts['job_list_loc_listing']) ? $atts['job_list_loc_listing'] : 'country,city';
            $loc_types_arr = $locations_view_type;
            $loc_view_country = $loc_view_state = $loc_view_city = false;
            if (!empty($loc_types_arr)) {
                if (is_array($loc_types_arr) && in_array('country', $loc_types_arr)) {
                    $loc_view_country = true;
                }
                if (is_array($loc_types_arr) && in_array('state', $loc_types_arr)) {
                    $loc_view_state = true;
                }
                if (is_array($loc_types_arr) && in_array('city', $loc_types_arr)) {
                    $loc_view_city = true;
                }
            }

            $candidate_listing_percent = isset($jobsearch_plugin_options['jobsearch_candidate_skills']) ? $jobsearch_plugin_options['jobsearch_candidate_skills'] : '';
            $candmin_listing_percent = isset($jobsearch_plugin_options['cand_min_listpecent']) ? $jobsearch_plugin_options['cand_min_listpecent'] : '';
            $candmin_listing_percent = absint($candmin_listing_percent);

            $job_types_switch = isset($jobsearch_plugin_options['job_types_switch']) ? $jobsearch_plugin_options['job_types_switch'] : '';
            $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
            $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';

            $is_employer_featured = false;
            $is_candisate_featured = false;
            if ($featured_section == 'employer') {
                $featured_args = array(
                    'posts_per_page' => 500,
                    'post_type' => 'employer',
                    'post_status' => 'publish',
                    'fields' => 'ids', // only load ids
                    'meta_query' => array(
                        array(
                            'key' => 'jobsearch_field_employer_approved',
                            'value' => 'on',
                            'compare' => '=',
                        ),
                    ),
                );

                $is_employer_featured = true;
            } elseif ($featured_section == 'candidate') {
                $element_filter_arr = array();
                $element_filter_arr[] = array(
                    'key' => 'jobsearch_field_candidate_approved',
                    'value' => 'on',
                    'compare' => '=',
                );
                if ($candidate_listing_percent == 'on' && $candmin_listing_percent > 0) {
                    $element_filter_arr[] = array(
                        'key' => 'overall_skills_percentage',
                        'value' => $candmin_listing_percent,
                        'compare' => '>=',
                        'type' => 'NUMERIC',
                    );
                }

                $featured_args = array(
                    'posts_per_page' => 500,
                    'post_type' => 'candidate',
                    'post_status' => 'publish',
                    'fields' => 'ids', // only load ids
                    'meta_query' => $element_filter_arr,
                );
                $is_candisate_featured = true;
            } else {
                $jobsearch_jobs_listin_sh = $jobsearch_shortcode_jobs_frontend;

                $jobsearch__options = get_option('jobsearch_plugin_options');
                $emporler_approval = isset($jobsearch__options['job_listwith_emp_aprov']) ? $jobsearch__options['job_listwith_emp_aprov'] : '';
                $is_filled_jobs = isset($jobsearch__options['job_allow_filled']) ? $jobsearch__options['job_allow_filled'] : '';

                $element_filter_arr = array();
                //        $element_filter_arr[] = array(
                //            'key' => 'jobsearch_field_job_publish_date',
                //            'value' => strtotime(current_time('d-m-Y H:i:s')),
                //            'compare' => '<=',
                //        );
                //        $element_filter_arr[] = array(
                //            'key' => 'jobsearch_field_job_expiry_date',
                //            'value' => strtotime(current_time('d-m-Y H:i:s')),
                //            'compare' => '>=',
                //        );
                //        $element_filter_arr[] = array(
                //            'key' => 'jobsearch_field_job_status',
                //            'value' => 'approved',
                //            'compare' => '=',
                //        );

                $post_ids = array();
                $sh_atts = array();
                $all_post_ids = array();
                if (is_object($jobsearch_jobs_listin_sh)) {
                    $all_post_ids = $jobsearch_jobs_listin_sh->job_general_query_filter($post_ids, $sh_atts);
                }

                if ($emporler_approval != 'off') {
                    $element_filter_arr[] = array(
                        'key' => 'jobsearch_job_employer_status',
                        'value' => 'approved',
                        'compare' => '=',
                    );
                }
                if ($non_featured_items == 'no') {
                    $element_filter_arr[] = array(
                        'key' => 'jobsearch_field_job_featured',
                        'value' => 'on',
                        'compare' => '=',
                    );
                }
                if ($is_filled_jobs == 'on') {
                    $element_filter_arr[] = array(
                        'relation' => 'OR',
                        array(
                            'key' => 'jobsearch_field_job_filled',
                            'compare' => 'NOT EXISTS',
                        ),
                        array(
                            array(
                                'key' => 'jobsearch_field_job_filled',
                                'value' => 'on',
                                'compare' => '!=',
                            ),
                        ),
                    );
                }

                $featured_args = array(
                    'posts_per_page' => 500,
                    'post_type' => 'job',
                    'post_status' => 'publish',
                    'fields' => 'ids',
                );

                if (!empty($element_filter_arr)) {
                    $featured_args['meta_query'] = $element_filter_arr;
                }

                if (!empty($all_post_ids)) {
                    $featured_args['post__in'] = $all_post_ids;
                }
            }

            $featured_query = new \WP_Query($featured_args);

            $total_posts = $featured_query->found_posts;

            wp_reset_postdata();

            if ($is_employer_featured || $is_candisate_featured) {
                $ids_arr = array();

                if (isset($featured_query->posts) && !empty($featured_query->posts) && is_array($featured_query->posts)) {
                    foreach ($featured_query->posts as $post_id) {
                        $promote_pckg_subtime = get_post_meta($post_id, 'promote_profile_substime', true);
                        $att_promote_pckg = get_post_meta($post_id, 'att_promote_profile_pkgorder', true);
                        $mber_feature_bk = get_post_meta($post_id, '_feature_mber_frmadmin', true);
                        $emp_count = 0;
                        if (!jobsearch_promote_profile_pkg_is_expired($att_promote_pckg)) {
                            $ids_arr[] = $post_id;
                        } else if ($mber_feature_bk == 'yes') {
                            $ids_arr[] = $post_id;
                        }
                    }
                }

                $ids_arr_count = !empty($ids_arr) ? count($ids_arr) : 0;
                $add_poststo_count = 0;
                if ($featured_job_num > 0 && $ids_arr_count < $featured_job_num) {
                    $add_poststo_count = $featured_job_num - $ids_arr_count;
                }
                if ($featured_job_num == '-1' && $ids_arr_count <= 5) {
                    $add_poststo_count = 20;
                }

                if ($add_poststo_count > 0) {
                    if ($is_employer_featured && $non_featured_items == 'yes') {
                        $ex_emp_args = array(
                            'posts_per_page' => $add_poststo_count,
                            'post_type' => 'employer',
                            'post_status' => 'publish',
                            'fields' => 'ids', // only load ids
                            'meta_query' => array(
                                array(
                                    'key' => 'jobsearch_field_employer_approved',
                                    'value' => 'on',
                                    'compare' => '=',
                                ),
                            ),
                        );
                        if (!empty($ids_arr)) {
                            $ex_emp_args['post__not_in'] = $ids_arr;
                        }
                        $ex_emp_query = new \WP_Query($ex_emp_args);
                        $ex_emp_posts = $ex_emp_query->posts;
                        if (!empty($ex_emp_posts)) {
                            foreach ($ex_emp_posts as $ex_emp_post) {
                                $ids_arr[] = $ex_emp_post;
                            }
                        }
                        wp_reset_postdata();
                    }

                    if ($is_candisate_featured && $non_featured_items == 'yes') {
                        $element_filter_arr = array();
                        $element_filter_arr[] = array(
                            'key' => 'jobsearch_field_candidate_approved',
                            'value' => 'on',
                            'compare' => '=',
                        );
                        if ($candidate_listing_percent == 'on' && $candmin_listing_percent > 0) {
                            $element_filter_arr[] = array(
                                'key' => 'overall_skills_percentage',
                                'value' => $candmin_listing_percent,
                                'compare' => '>=',
                            );
                        }
                        $ex_cand_args = array(
                            'posts_per_page' => $add_poststo_count,
                            'post_type' => 'candidate',
                            'post_status' => 'publish',
                            'fields' => 'ids', // only load ids
                            'meta_query' => $element_filter_arr,
                        );
                        if (!empty($ids_arr)) {
                            $ex_cand_args['post__not_in'] = $ids_arr;
                        }
                        $ex_cand_query = new \WP_Query($ex_cand_args);
                        $ex_cand_posts = $ex_cand_query->posts;
                        if (!empty($ex_cand_posts)) {
                            foreach ($ex_cand_posts as $ex_cand_post) {
                                $ids_arr[] = $ex_cand_post;
                            }
                        }
                        wp_reset_postdata();
                    }
                }
            } else {

                $ids_arr = array();
                if (isset($featured_query->posts) && !empty($featured_query->posts) && is_array($featured_query->posts)) {
                    $ids_arr = $featured_query->posts;
                }

                $add_poststo_count = 0;
                if ($featured_job_num > 0 && $total_posts < $featured_job_num) {
                    $add_poststo_count = $featured_job_num - $total_posts;
                }
                if ($featured_job_num == '-1' && $total_posts <= 5) {
                    $add_poststo_count = 20;
                }
                if ($add_poststo_count > 0 && $non_featured_items == 'yes') {
                    $ex_job_args = array(
                        'posts_per_page' => $add_poststo_count,
                        'post_type' => 'job',
                        'post_status' => 'publish',
                        'fields' => 'ids', // only load ids
                        'meta_query' => array(
                            array(
                                'key' => 'jobsearch_field_job_publish_date',
                                'value' => current_time('timestamp'),
                                'compare' => '<=',
                            ),
                            array(
                                'key' => 'jobsearch_field_job_expiry_date',
                                'value' => current_time('timestamp'),
                                'compare' => '>=',
                            ),
                            array(
                                'key' => 'jobsearch_field_job_status',
                                'value' => 'approved',
                                'compare' => '=',
                            ),
                        ),
                    );
                    if (!empty($ids_arr)) {
                        $ex_job_args['post__not_in'] = $ids_arr;
                    }
                    $ex_job_query = new \WP_Query($ex_job_args);
                    $ex_job_posts = $ex_job_query->posts;

                    if (!empty($ex_job_posts)) {
                        foreach ($ex_job_posts as $ex_job_post) {
                            $ids_arr[] = $ex_job_post;
                        }
                    }
                    wp_reset_postdata();
                }
            }

            $veri_class = '';
            $veri_slide = 'true';
            if ($job_slide_position == 'horizontal') {
                $veri_class = ' vertical';
                $veri_slide = 'false';
            }
            wp_enqueue_script('careerfy-slick-slider');
            ob_start();
            $rand_id = rand(1234, 67888);
            ?>
            <script>
                jQuery(document).ready(function () {
                    'use strict';
                    if (jQuery('#featured-scroll-<?php echo($rand_id); ?>').length > 0) {
                        jQuery('#featured-scroll-<?php echo($rand_id); ?>').slick({
                            slidesToShow: <?php echo($featured_num_slide); ?>,
                            slidesToScroll: 1,
                            autoplay: true,
                            autoplaySpeed: 0,
                            speed: <?php echo($featured_slide_speed); ?>,
                            vertical: <?php echo($veri_slide); ?>,
                            cssEase: '<?php echo($slide_execution); ?>',
                            arrows: 'false',
                            responsive: [
                                {
                                    breakpoint: 1024,
                                    settings: {
                                        slidesToShow: 2,
                                        slidesToScroll: 1,
                                        infinite: true,
                                    }
                                },
                                {
                                    breakpoint: 800,
                                    settings: {
                                        slidesToShow: 2,
                                        slidesToScroll: 1
                                    }
                                },
                                {
                                    breakpoint: 400,
                                    settings: {
                                        slidesToShow: 1,
                                        slidesToScroll: 1
                                    }
                                }
                            ]
                        });
                    }
                });
            </script>

            <?php if (isset($featured_title) && !empty($featured_title)) { ?>
                <div class="careerfy-fancy-title">
                    <h2><?php echo($featured_title); ?></h2>
                </div>
            <?php } ?>

            <?php
            if ($is_employer_featured) { ?>
                <div class="careerfy-employer careerfy-employer-grid">
                    <div id="featured-scroll-<?php echo ($rand_id); ?>">
                        <?php
                        if (isset($ids_arr) && !empty($ids_arr) && is_array($ids_arr)) {
                            if ($featured_job_num > 0 && count($ids_arr) > $featured_job_num) {
                                $ids_arr = array_slice($ids_arr, 0, $featured_job_num, true);
                            }
                            foreach ($ids_arr as $employer_id) {
                                //global $post, $jobsearch_member_profile;
                                //echo $employer_id = $post;
                                $promote_pckg_subtime = get_post_meta($employer_id, 'promote_profile_substime', true);
                                $att_promote_pckg = get_post_meta($employer_id, 'att_promote_profile_pkgorder', true);
                                $employer_random_id = rand(1111111, 9999999);
                                $post_thumbnail_id = jobsearch_employer_get_profile_image($employer_id);
                                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-employer-medium');
                                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : jobsearch_employer_image_placeholder();

                                $jobsearch_employer_featured = get_post_meta($employer_id, 'jobsearch_field_employer_featured', true);
                                $jobsearch_employer_posted = get_post_meta($employer_id, 'jobsearch_field_employer_publish_date', true);
                                $jobsearch_employer_posted = jobsearch_time_elapsed_string($jobsearch_employer_posted);
                                $get_employer_location = get_post_meta($employer_id, 'jobsearch_field_location_address', true);
                                if (function_exists('jobsearch_post_city_contry_txtstr')) {
                                     $get_employer_location = jobsearch_post_city_contry_txtstr($employer_id, $loc_view_country, $loc_view_state, $loc_view_city);
                                 }
                                $jobsearch_employer_team_title_list = get_post_meta($employer_id, 'jobsearch_field_team_title', true);
                                $team_imagefield_list = get_post_meta($employer_id, 'jobsearch_field_team_image', true);
                                $jargs = array(
                                    'post_type' => 'job',
                                    'posts_per_page' => 1,
                                    'post_status' => 'publish',
                                    'order' => 'DESC',
                                    'orderby' => 'ID',
                                    'meta_query' => array(
                                        array(
                                            'key' => 'jobsearch_field_job_posted_by',
                                            'value' => $employer_id,
                                            'compare' => '=',
                                        ),
                                        array(
                                            'key' => 'jobsearch_field_job_status',
                                            'value' => 'approved',
                                            'compare' => '=',
                                        ),
                                    ),
                                );
                                $jobs_query = new \WP_Query($jargs);
                                $emp_total_jobs = $jobs_query->found_posts;
                                update_post_meta($employer_id, 'jobsearch_field_employer_job_count', absint($emp_total_jobs));
                                $jobsearch_employer_job_count = get_post_meta($employer_id, 'jobsearch_field_employer_job_count', true);
                                $jobsearch_employer_team_count = 0;
                                if (is_array($jobsearch_employer_team_title_list) && sizeof($jobsearch_employer_team_title_list) > 0) {
                                    $jobsearch_employer_team_count = sizeof($jobsearch_employer_team_title_list);
                                }
                                $sector_str = jobsearch_employer_get_all_sectors($employer_id, '', '', '', '<small>', '</small>');
                                ?>
                                <div>
                                    <div class="careerfy-employer-grid-wrap">
                                        <?php echo jobsearch_member_promote_profile_iconlab($employer_id, 'employer_list') ?>
                                        <figure>
                                            <?php if ($post_thumbnail_src != '') { ?>
                                                <a href="<?php echo get_permalink($employer_id); ?>" class="careerfy-employer-grid-image"><img src="<?php echo ($post_thumbnail_src) ?>" alt=""></a>
                                            <?php } ?>
                                            <figcaption>
                                                <?php
                                                if (!empty($sector_str) && $sectors_enable_switch == 'on') {
                                                    echo ($sector_str);
                                                }
                                                ?>
                                                <h2>
                                                    <a href="<?php echo esc_url(get_permalink($employer_id)); ?>">
                                                        <?php echo esc_html(wp_trim_words(get_the_title($employer_id), 5)); ?>
                                                    </a>
                                                </h2>
                                                <?php
                                                if (!empty($get_employer_location) && $all_location_allow == 'on') {
                                                    ?>
                                                    <span><?php echo jobsearch_esc_html($get_employer_location); ?></span>
                                                    <?php
                                                }
                                                ?>
                                            </figcaption>
                                        </figure>
                                        <?php
                                        if ($jobsearch_employer_team_count > 0 && (is_array($team_imagefield_list) && sizeof($team_imagefield_list) > 0)) {
                                            $jobsearch_employer_team_count = '+' . $jobsearch_employer_team_count;
                                            ?>
                                            <ul class="careerfy-employer-thumblist">
                                                <?php
                                                $team_flag = 0;
                                                foreach ($team_imagefield_list as $team_imagefield_single) {
                                                    ?>
                                                    <li><a href="javascript:void(0);"><img src="<?php echo esc_url($team_imagefield_single); ?>" alt=""></a></li>
                                                    <?php
                                                    $team_flag++;
                                                    if ($team_flag >= 4) {
                                                        break;
                                                    }
                                                }
                                                ?>
                                            </ul>
                                            <a href="<?php echo esc_url(get_permalink($employer_id)); ?>" class="careerfy-employer-thumblist-size"><?php echo esc_html($jobsearch_employer_team_count); ?> <?php echo esc_html__('team size', 'careerfy'); ?></a>
                                            <?php } ?>
                                    </div>
                                    <?php
                                    $jobsearch_employer_job_count_str = '';
                                    if ($jobsearch_employer_job_count > 1) {
                                        $jobsearch_employer_job_count_str = absint($jobsearch_employer_job_count) . ' ' . esc_html__('Vacancies', 'careerfy');
                                    } else {
                                        $jobsearch_employer_job_count_str = absint($jobsearch_employer_job_count) . ' ' . esc_html__('Vacancy', 'careerfy');
                                    }
                                    ?>
                                    <a href="<?php echo esc_url(get_permalink($employer_id)); ?>" class="careerfy-employer-grid-btn"><?php echo ($jobsearch_employer_job_count_str) ?></a>
                                    <?php

                                    $follow_btn_args = array(
                                        'employer_id' => $employer_id,
                                        'before_label' => esc_html__('Follow', 'wp-jobsearch'),
                                        'after_label' => esc_html__('Followed', 'wp-jobsearch'),
                                        'ext_class' => 'careerfy-employer-grid-btn',
                                        'view' => 'list_grid_view',
                                    );
                                    do_action('jobsearch_employer_followin_btn', $follow_btn_args);
                                  ?>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<li>
                                    <div class="no-employer-match-error">
                                        <strong>' . esc_html__('No Record', 'careerfy') . '</strong>
                                        <span>' . esc_html__('Sorry!', 'careerfy') . '&nbsp; ' . esc_html__('Does not match record with your keyword', 'careerfy') . ' </span>
                                    </div>
                                </li>';
                        }
                        ?>
                    </div>
                </div>
                <?php } elseif ($is_candisate_featured) {
                    
                    $view_candidate = true;
                    $restrict_candidates = isset($jobsearch_plugin_options['restrict_candidates_list']) ? $jobsearch_plugin_options['restrict_candidates_list'] : '';

                    $view_cand_type = 'fully';
                    $emp_cvpbase_restrictions = isset($jobsearch_plugin_options['emp_cv_pkgbase_restrictions_list']) ? $jobsearch_plugin_options['emp_cv_pkgbase_restrictions_list'] : '';
                    $restrict_cand_type = isset($jobsearch_plugin_options['restrict_candidates_for_users']) ? $jobsearch_plugin_options['restrict_candidates_for_users'] : '';
                    if ($emp_cvpbase_restrictions == 'on' && $restrict_cand_type != 'only_applicants') {
                        $view_cand_type = 'partly';
                    }

                    $restrict_candidates_for_users = isset($jobsearch_plugin_options['restrict_candidates_for_users']) ? $jobsearch_plugin_options['restrict_candidates_for_users'] : '';

                    $is_employer = false;
                    if ($restrict_candidates == 'on' && $view_cand_type == 'fully') {
                        $view_candidate = false;

                        if (is_user_logged_in()) {
                            $cur_user_id = get_current_user_id();
                            $cur_user_obj = wp_get_current_user();
                            if (jobsearch_user_isemp_member($cur_user_id)) {
                                $employer_id = jobsearch_user_isemp_member($cur_user_id);
                                $cur_user_id = jobsearch_get_employer_user_id($employer_id);
                            } else {
                                $employer_id = jobsearch_get_user_employer_id($cur_user_id);
                            }
                            $ucandidate_id = jobsearch_get_user_candidate_id($cur_user_id);
                            $employer_dbstatus = get_post_meta($employer_id, 'jobsearch_field_employer_approved', true);
                            if ($employer_id > 0 && $employer_dbstatus == 'on') {
                                $is_employer = true;
                                if ($restrict_candidates_for_users == 'register_resume') {
                                    $user_cv_pkg = jobsearch_employer_first_subscribed_cv_pkg($cur_user_id);
                                    if (!$user_cv_pkg) {
                                        $user_cv_pkg = jobsearch_allin_first_pkg_subscribed($cur_user_id, 'cvs');
                                    }
                                    if ($user_cv_pkg) {
                                        $view_candidate = true;
                                    }
                                } else {
                                    $view_candidate = true;
                                }
                            } else if (in_array('administrator', (array)$cur_user_obj->roles)) {
                                $view_candidate = true;
                            } else if ($restrict_candidates_for_users == 'register_empcand' && ($ucandidate_id > 0 || $employer_id > 0)) {
                                $view_candidate = true;
                            }
                        }
                    }
                if ($view_candidate === false) {
                $restrict_img = isset($jobsearch_plugin_options['candidate_restrict_img']) ? $jobsearch_plugin_options['candidate_restrict_img'] : '';
                $restrict_img_url = isset($restrict_img['url']) ? $restrict_img['url'] : '';

                $restrict_cv_pckgs = isset($jobsearch_plugin_options['restrict_cv_packages']) ? $jobsearch_plugin_options['restrict_cv_packages'] : '';
                $restrict_msg = isset($jobsearch_plugin_options['restrict_cand_msg']) && $jobsearch_plugin_options['restrict_cand_msg'] != '' ? $jobsearch_plugin_options['restrict_cand_msg'] : esc_html__('The Page is Restricted only for Subscribed Employers', 'wp-jobsearch');
                ?>
                <div class="jobsearch-column-12">
                    <div class="restrict-candidate-sec">
                        <img src="<?php echo($restrict_img_url) ?>" alt="">
                        <h2><?php echo($restrict_msg) ?></h2>

                        <?php
                        if ($is_employer) { ?>
                            <p><?php esc_html_e('Please buy a C.V package to view this candidate.', 'wp-jobsearch') ?></p>
                            <?php
                        } else if (is_user_logged_in()) {
                            ?>
                            <p><?php esc_html_e('You are not an employer. Only an Employer can view a candidate.', 'wp-jobsearch') ?></p>
                            <?php
                        } else {
                            ?>
                            <p><?php esc_html_e('If you are employer just login to view this candidate or buy a C.V package to download His Resume.', 'wp-jobsearch') ?></p>
                            <?php
                        }
                        if (is_user_logged_in()) {
                            ?>
                            <div class="login-btns">
                                <a href="<?php echo wp_logout_url(home_url('/')); ?>"><i
                                            class="jobsearch-icon jobsearch-logout"></i><?php esc_html_e('Logout', 'wp-jobsearch') ?>
                                </a>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="login-btns">
                                <a href="javascript:void(0);" class="jobsearch-open-signin-tab"><i
                                            class="jobsearch-icon jobsearch-user"></i><?php esc_html_e('Login', 'wp-jobsearch') ?>
                                </a>
                                <a href="javascript:void(0);" class="jobsearch-open-register-tab"><i
                                            class="jobsearch-icon jobsearch-plus"></i><?php esc_html_e('Become an Employer', 'wp-jobsearch') ?>
                                </a>
                            </div>
                            <?php
                            if (!empty($restrict_cv_pckgs) && is_array($restrict_cv_pckgs) && $restrict_candidates_for_users == 'register_resume') { ?>
                                <div class="jobsearch-box-title">
                                    <span><?php esc_html_e('OR', 'wp-jobsearch') ?></span>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <?php
                    if (!empty($restrict_cv_pckgs) && is_array($restrict_cv_pckgs) && $restrict_candidates_for_users == 'register_resume') {
                        wp_enqueue_script('jobsearch-packages-scripts');
                        ?>
                        <div class="cv-packages-section">
                            <div class="packages-title">
                                <h2><?php esc_html_e('Buy any CV Packages to get started', 'wp-jobsearch') ?></h2></div>
                            <?php
                            ob_start();
                            ?>
                            <div class="jobsearch-row">
                                <?php
                                foreach ($restrict_cv_pckgs as $restrict_cv_pckg) {
                                    $cv_pkg_obj = $restrict_cv_pckg != '' ? get_page_by_path($restrict_cv_pckg, 'OBJECT', 'package') : '';
                                    if (is_object($cv_pkg_obj) && isset($cv_pkg_obj->ID)) {
                                        $cv_pkg_id = $cv_pkg_obj->ID;
                                        $pkg_type = get_post_meta($cv_pkg_id, 'jobsearch_field_charges_type', true);
                                        $pkg_price = get_post_meta($cv_pkg_id, 'jobsearch_field_package_price', true);

                                        $num_of_cvs = get_post_meta($cv_pkg_id, 'jobsearch_field_num_of_cvs', true);
                                        $pkg_exp_dur = get_post_meta($cv_pkg_id, 'jobsearch_field_package_expiry_time', true);
                                        $pkg_exp_dur_unit = get_post_meta($cv_pkg_id, 'jobsearch_field_package_expiry_time_unit', true);

                                        $pkg_exfield_title = get_post_meta($cv_pkg_id, 'jobsearch_field_package_exfield_title', true);
                                        $pkg_exfield_val = get_post_meta($cv_pkg_id, 'jobsearch_field_package_exfield_val', true);
                                        $pkg_exfield_status = get_post_meta($cv_pkg_id, 'jobsearch_field_package_exfield_status', true);
                                        ?>
                                        <div class="jobsearch-column-4">
                                            <div class="jobsearch-classic-priceplane">
                                                <h2><?php echo get_the_title($cv_pkg_id) ?></h2>
                                                <div class="jobsearch-priceplane-section">
                                                    <?php
                                                    if ($pkg_type == 'paid') {
                                                        echo '<span>' . jobsearch_get_price_format($pkg_price) . ' <small>' . esc_html__('only', 'wp-jobsearch') . '</small></span>';
                                                    } else {
                                                        echo '<span>' . esc_html__('Free', 'wp-jobsearch') . '</span>';
                                                    }
                                                    ?>
                                                </div>
                                                <div class="grab-classic-priceplane">
                                                    <ul>
                                                        <?php
                                                        if (!empty($pkg_exfield_title)) {
                                                            $_exf_counter = 0;
                                                            foreach ($pkg_exfield_title as $_exfield_title) {
                                                                $_exfield_val = isset($pkg_exfield_val[$_exf_counter]) ? $pkg_exfield_val[$_exf_counter] : '';
                                                                $_exfield_status = isset($pkg_exfield_status[$_exf_counter]) ? $pkg_exfield_status[$_exf_counter] : '';
                                                                if ($_exfield_val != '') {
                                                                    ?>
                                                                    <li<?php echo($_exfield_status == 'active' ? ' class="active"' : '') ?>>
                                                                        <i class="jobsearch-icon jobsearch-check-square"></i> <?php echo $_exfield_title . ' ' . $_exfield_val ?>
                                                                    </li>
                                                                    <?php
                                                                }
                                                                $_exf_counter++;
                                                            }
                                                        }
                                                        ?>
                                                    </ul>
                                                    <?php if (is_user_logged_in()) { ?>
                                                        <a href="javascript:void(0);"
                                                           class="jobsearch-classic-priceplane-btn jobsearch-subscribe-cv-pkg"
                                                           data-id="<?php echo($cv_pkg_id) ?>"><?php esc_html_e('Get Started', 'wp-jobsearch') ?> </a>
                                                        <span class="pkg-loding-msg" style="display:none;"></span>
                                                    <?php } else { ?>
                                                        <a href="javascript:void(0);"
                                                           class="jobsearch-classic-priceplane-btn jobsearch-open-signin-tab"><?php esc_html_e('Get Started', 'wp-jobsearch') ?> </a>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            <?php
                            $pkgs_html = ob_get_clean();
                            echo apply_filters('jobsearch_restrict_candidate_pakgs_html', $pkgs_html, $restrict_cv_pckgs);
                            ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } else {
                ?>
                <div class="careerfy-candidate careerfy-candidate-grid">
                    <div id="featured-scroll-<?php echo ($rand_id); ?>">
                        <?php
                        if (isset($ids_arr) && !empty($ids_arr) && is_array($ids_arr)) {
                            if ($featured_job_num > 0 && count($ids_arr) > $featured_job_num) {
                                $ids_arr = array_slice($ids_arr, 0, $featured_job_num, true);
                            }

                            foreach ($ids_arr as $candidate_id) {

                                $promote_pckg_subtime = get_post_meta($candidate_id, 'promote_profile_substime', true);
                                $att_promote_pckg = get_post_meta($candidate_id, 'att_promote_profile_pkgorder', true);
                                $cand_count = 0;
                                //if (!jobsearch_promote_profile_pkg_is_expired($att_promote_pckg)) {
                                $candidate_uid = jobsearch_get_candidate_user_id($candidate_id);
                                $user_obj = get_user_by('ID', $candidate_uid);
                                $user_email = isset($user_obj->user_email) ? $user_obj->user_email : '';
                                $post_thumbnail_src = jobsearch_candidate_img_url_comn($candidate_id);
                                $jobsearch_candidate_approved = get_post_meta($candidate_id, 'jobsearch_field_candidate_approved', true);
                                $get_candidate_location = get_post_meta($candidate_id, 'jobsearch_field_location_address', true);
                                if (function_exists('jobsearch_post_city_contry_txtstr')) {
                                     $get_candidate_location = jobsearch_post_city_contry_txtstr($candidate_id, $loc_view_country, $loc_view_state, $loc_view_city);
                                 }
                                $jobsearch_candidate_jobtitle = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
                                $jobsearch_candidate_company_name = get_post_meta($candidate_id, 'jobsearch_field_candidate_company_name', true);
                                $jobsearch_candidate_company_url = get_post_meta($candidate_id, 'jobsearch_field_candidate_company_url', true);
                                $user_status = get_post_meta($candidate_id, 'jobsearch_field_candidate_approved', true);
                                $sdsdffdsf = get_post_meta($candidate_id, 'promote_profile_substime', true);
                                $jobsearch_candidate_jobtitle = jobsearch_esc_html($jobsearch_candidate_jobtitle);
                                $candidate_company_str = '';
                                if ($jobsearch_candidate_jobtitle != '') {
                                    $candidate_company_str .= $jobsearch_candidate_jobtitle;
                                }
                                $sector_str = jobsearch_candidate_get_all_sectors($candidate_id, '', '', '', '', '');

                                $final_color = '';
                                $candidate_skills = isset($jobsearch_plugin_options['jobsearch_candidate_skills']) ? $jobsearch_plugin_options['jobsearch_candidate_skills'] : '';
                                if ($candidate_skills == 'on') {

                                    $low_skills_clr = isset($jobsearch_plugin_options['skill_low_set_color']) && $jobsearch_plugin_options['skill_low_set_color'] != '' ? $jobsearch_plugin_options['skill_low_set_color'] : '';
                                    $med_skills_clr = isset($jobsearch_plugin_options['skill_med_set_color']) && $jobsearch_plugin_options['skill_med_set_color'] != '' ? $jobsearch_plugin_options['skill_med_set_color'] : '';
                                    $high_skills_clr = isset($jobsearch_plugin_options['skill_high_set_color']) && $jobsearch_plugin_options['skill_high_set_color'] != '' ? $jobsearch_plugin_options['skill_high_set_color'] : '';
                                    $comp_skills_clr = isset($jobsearch_plugin_options['skill_ahigh_set_color']) && $jobsearch_plugin_options['skill_ahigh_set_color'] != '' ? $jobsearch_plugin_options['skill_ahigh_set_color'] : '';

                                    $overall_candidate_skills = get_post_meta($candidate_id, 'overall_skills_percentage', true);
                                    if ($overall_candidate_skills <= 25 && $low_skills_clr != '') {
                                        $final_color = 'style="background-color: ' . $low_skills_clr . ';"';
                                    } else if ($overall_candidate_skills > 25 && $overall_candidate_skills <= 50 && $med_skills_clr != '') {
                                        $final_color = 'style="background-color: ' . $med_skills_clr . ';"';
                                    } else if ($overall_candidate_skills > 50 && $overall_candidate_skills <= 75 && $high_skills_clr != '') {
                                        $final_color = 'style="background-color: ' . $high_skills_clr . ';"';
                                    } else if ($overall_candidate_skills > 75 && $comp_skills_clr != '') {
                                        $final_color = 'style="background-color: ' . $comp_skills_clr . ';"';
                                    }
                                }
                                ?>
                                <div>
                                    <figure>
                                        <?php echo jobsearch_member_promote_profile_iconlab($candidate_id); ?>
                                        <?php echo jobsearch_cand_urgent_pkg_iconlab($candidate_id,'cand_listv6'); ?>
                                        <?php if ($post_thumbnail_src != '' && !$cand_profile_restrict::cand_field_is_locked('profile_fields|profile_img')) { ?>
                                            <a href="<?php echo get_permalink($candidate_id); ?>" class="careerfy-candidate-grid-thumb"><img src="<?php echo ($post_thumbnail_src) ?>" alt=""> <span class="careerfy-candidate-grid-status" <?php echo ($final_color) ?>></span></a>
                                        <?php } ?>
                                        <figcaption>
                                            <h2>
                                                <a href="<?php echo esc_url(get_permalink($candidate_id)); ?>">
                                                    <?php echo apply_filters('jobsearch_candidate_listing_item_title', wp_trim_words(get_the_title($candidate_id), 5), $candidate_id); ?>
                                                </a>
                                            </h2>
                                            <?php
                                            if ($candidate_company_str != '') {
                                                ?>
                                                <p><?php echo ($candidate_company_str) ?></p>
                                                <?php
                                            }

                                            if ($get_candidate_location != '' && $all_location_allow == 'on' && !$cand_profile_restrict::cand_field_is_locked('address_defields')) {
                                                ?>
                                                <span><?php echo ($get_candidate_location) ?></span>
                                                <?php
                                            }
                                            ?>
                                        </figcaption>
                                    </figure>
                                    <ul class="careerfy-candidate-grid-option">
                                        <?php
                                        if ($sector_str != '' && $sectors_enable_switch == 'on' && !$cand_profile_restrict::cand_field_is_locked('profile_fields|sector')) {
                                            ?>
                                            <li>
                                                <div class="careerfy-right">
                                                    <span><?php esc_html_e('Sector:', 'careerfy') ?></span>
                                                    <?php echo ($sector_str) ?>
                                                </div>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<li>
                            <div class="no-candidate-match-error">
                                <strong>' . esc_html__('No Record', 'careerfy') . '</strong>
                                <span>' . esc_html__('Sorry!', 'careerfy') . '&nbsp; ' . esc_html__('Does not match record with your keyword', 'careerfy') . ' </span>
    
                            </div>
                        </li>';
                        }
                        ?>

                    </ul>
                </div>
                </div>
                <?php 
                }
                } else { ?>
                <div class="careerfy-jobs-scroll-slider<?php echo ($veri_class); ?>">
                    <ul class="jobs-scroll-wrap scroll" id="featured-scroll-<?php echo ($rand_id); ?>">
                        <?php
                        if (!empty($ids_arr)) {
                            if ($featured_job_num > 0 && count($ids_arr) > $featured_job_num) {
                                $ids_arr = array_slice($ids_arr, 0, $featured_job_num, true);
                            }
                            foreach ($ids_arr as $job_id) {

                                $job_random_id = rand(1111111, 9999999);
                                $post_thumbnail_id = function_exists('jobsearch_job_get_profile_image') ? jobsearch_job_get_profile_image($job_id) : 0;
                                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-job-medium');
                                $no_placeholder_img = '';
                                if (function_exists('jobsearch_no_image_placeholder')) {
                                    $no_placeholder_img = jobsearch_no_image_placeholder();
                                }
                                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : $no_placeholder_img;
                                $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
                                $job_post_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                                $company_name = function_exists('jobsearch_job_get_company_name') ? jobsearch_job_get_company_name($job_id, '@ ') : '';
                                $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                                if (function_exists('jobsearch_post_city_contry_txtstr')) {
                                     $get_job_location = jobsearch_post_city_contry_txtstr($job_id, $loc_view_country, $loc_view_state, $loc_view_city);
                                 }
                                $job_type_str = function_exists('jobsearch_job_get_all_jobtypes') ? jobsearch_job_get_all_jobtypes($job_id, 'careerfy-scroll-jobs-type', '', '', '', '', 'span') : '';
                                $sector_str = function_exists('jobsearch_job_get_all_sectors') ? jobsearch_job_get_all_sectors($job_id, '', '', '', '<li>', '</li>') : '';
                                $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
                                $job_city_title = '';
                                $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location3', true);
                                if ($get_job_city == '') {
                                    $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location2', true);
                                }
                                if ($get_job_city == '') {
                                    $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
                                }
                                $job_city_tax = $get_job_city != '' ? get_term_by('slug', $get_job_city, 'job-location') : '';
                                if (is_object($job_city_tax)) {
                                    $job_city_title = $job_city_tax->name;
                                }
                                ?>
                                <li>
                                    <div class="careerfy-jobs-scroll-slider-wrap">
                                    <?php  jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id,'job_listv1'); ?>
                                        <figure>
                                        <?php
                                            if ($post_thumbnail_src != '') { ?>
                                                <a href="<?php echo get_permalink($job_id); ?>"><img src="<?php echo ($post_thumbnail_src) ?>" alt=""></a>
                                                <?php
                                            }
                                            if ($jobsearch_job_featured == 'on') { ?>
                                                <span class="careerfy-jobli-medium2"><i class="fa fa-star"></i></span>
                                                <?php } ?>
                                            <?php jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'job_listv1') ?>
                                        </figure>
                                        <div class="careerfy-jobs-scroll-slider-text">
                                            <h2><a href="<?php echo esc_url(get_permalink($job_id)); ?>"><?php echo esc_html(wp_trim_words(get_the_title($job_id), 3)); ?></a></h2>
                                            <?php
                                            if (!empty($get_job_location)) { ?>
                                                <span><i class="careerfy-icon careerfy-map-pin"></i> <?php echo jobsearch_esc_html($get_job_location); ?></span>
                                                <?php } ?>
                                            <div class="careerfy-foot-wrap">
                                                <?php
                                                $book_mark_args = array(
                                                    'job_id' => $job_id,
                                                    'before_icon' => 'fa fa-heart-o',
                                                    'after_icon' => 'fa fa-heart',
                                                    'container_class' => '',
                                                    'anchor_class' => 'careerfy-job-like',
                                                );
                                                do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args);
                                                if ($job_type_str != '' && $job_types_switch == 'on') {
                                                    echo ($job_type_str);
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            }
                        } else {
                            echo '<li>
                            <div class="no-job-match-error">
                                <strong>' . esc_html__('No Record', 'careerfy') . '</strong>
                                <span>' . esc_html__('Sorry!', 'careerfy') . '&nbsp; ' . esc_html__('Does not match record with your keyword', 'careerfy') . ' </span>
    
                            </div>
                        </li>';
                        }
                        ?>
                    </ul>
                </div>
                <?php
            }

            $featured_html = ob_get_clean();
            echo $featured_html;
        }
    }

    protected function _content_template()
    {

    }
}