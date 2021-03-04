<?php

namespace CareerfyElementor\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
if (!defined('ABSPATH')) exit;


/**
 * @since 1.1.0
 */
class TopJobSlider extends Widget_Base
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
        return 'top-job-slider';
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
        return __('Top Job Slider', 'careerfy-frame');
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
        return 'fa fa-picture-o';
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
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $categories = get_terms(array(
            'taxonomy' => 'sector',
            'hide_empty' => false,
        ));

        $all_locations_type = isset($jobsearch__options['all_locations_type']) ? $jobsearch__options['all_locations_type'] : '';

        $cate_array = array(esc_html__("Select Sector", "careerfy-frame") => '');
        if (is_array($categories) && sizeof($categories) > 0) {
            foreach ($categories as $category) {
                $cate_array[$category->name] = $category->slug;
            }
        }

        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Top Job Slider Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'top_job_style',
            [
                'label' => __('Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'slider',
                'options' => [
                    'slider' => __('Style 1', 'careerfy-frame'),
                    'slider2' => __('Style 2', 'careerfy-frame'),
                    'slider3' => __('Style 3', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'title_img',
            [
                'label' => __('Title Image', 'careerfy-frame'),
                'type' => Controls_Manager::MEDIA,
                'description' => esc_html__("Image will show above title", "careerfy-frame"),
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'top_job_style' => 'slider2'
                ]
            ]
        );

        $this->add_control(
            'top_recruiter_title',
            [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'top_job_style' => 'slider2'
                ]
            ]
        );

        $this->add_control(
            'top_recruiter_desc', [
                'label' => __('Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
                'condition' => [
                    'top_job_style' => 'slider2'
                ]
            ]
        );

        $this->add_control(
            'top_recruiter_cat', [
                'label' => __('Sector', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'options' => $cate_array,
                'description' => esc_html__("Select Sector.", "careerfy-frame")
            ]
        );

        $this->add_control(
            'featured_only', [
                'label' => __('Featured', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'top_recruiter_order', [
                'label' => __('Order', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'DESC',
                'options' => [
                    'DESC' => __('Descending', 'careerfy-frame'),
                    'ASC' => __('Ascending', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'job_list_loc_listing', [
                'label' => __('Locations in listing', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'description' => esc_html__("Select which type of location in listing. If nothing select then full address will display.", "careerfy-frame"),
                'multiple' => true,
                'default' => ['country', 'city'],
                'options' => [
                    'country' => __('Country', 'careerfy-frame'),
                    'state' => __('State', 'careerfy-frame'),
                    'city' => __('City', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'top_recruiter_orderby', [
                'label' => __('Order By', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'description' => esc_html__("Select which type of location in listing. If nothing select then full address will display.", "careerfy-frame"),
                'default' => 'date',
                'options' => [
                    'date' => __('Date', 'careerfy-frame'),
                    'title' => __('Title', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'top_recruiter_per_page', [
                'label' => __('Number of top Jobs', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '10',
            ]
        );
        $this->end_controls_section();
    }

    private static function GetThumbnail($top_recruiter_id)
    {
        $post_thumbnail_id = function_exists('jobsearch_job_get_profile_image') ? jobsearch_job_get_profile_image($top_recruiter_id) : 0;
        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
        return isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : jobsearch_no_image_placeholder();
    }

    private static function GetSliderList($top_job_posts, $top_job_style, $loc_view_country, $loc_view_state, $loc_view_city)
    {
        global $jobsearch_plugin_options;
        $job_types_switch = isset($jobsearch_plugin_options['job_types_switch']) ? $jobsearch_plugin_options['job_types_switch'] : '';

        foreach ($top_job_posts as $top_recruiter_id) {
            $post_thumbnail_src = self::GetThumbnail($top_recruiter_id);
            $top_recruiter_job_types = wp_get_post_terms($top_recruiter_id, 'jobtype');
            $get_top_recruiter_job_location = get_post_meta($top_recruiter_id, 'jobsearch_field_location_address', true);
            if (function_exists('jobsearch_post_city_contry_txtstr')) {
                $get_top_recruiter_job_location = jobsearch_post_city_contry_txtstr($top_recruiter_id, $loc_view_country, $loc_view_state, $loc_view_city);
            }


            $get_job_title = get_the_title($top_recruiter_id);
            $job_employer_id = get_post_meta($top_recruiter_id, 'jobsearch_field_job_posted_by', true); // get job employer
            wp_enqueue_script('jobsearch-job-functions-script');
            $employer_cover_image_src_style_str = '';
            if ($job_employer_id != '') {
                if (class_exists('JobSearchMultiPostThumbnails')) {
                    $employer_cover_image_src = \JobSearchMultiPostThumbnails::get_post_thumbnail_url('employer', 'cover-image', $job_employer_id);
                    if ($employer_cover_image_src != '') {
                        $employer_cover_image_src_style_str = ' style="background:url(' . esc_url($employer_cover_image_src) . ') no-repeat center/cover; "';
                    }
                }
            }
            $wp_date_formate = get_option('date_format');
            $jobsearch_job_featured = get_post_meta($top_recruiter_id, 'jobsearch_field_job_featured', true);
            $job_deadline = get_post_meta($top_recruiter_id, 'jobsearch_field_job_application_deadline_date', true);
            $postby_emp_id = get_post_meta($top_recruiter_id, 'jobsearch_field_job_posted_by', true);
            $top_recruiter_date_date = get_post_meta($top_recruiter_id, 'jobsearch_field_job_publish_date', true);
            $top_recruiter_date_date = absint($top_recruiter_date_date);
            $current_time = current_time('timestamp');
            $elaspedtime = ($current_time) - ($top_recruiter_date_date);
            $hourz = 24 * 60 * 60;
            $top_recruiter_post = get_post($top_recruiter_id);
            $top_recruiter_post_content = $top_recruiter_post->post_content;

            $job_type_str = '';
            if (isset($top_recruiter_job_types[0]->name)) {

                $jobtype_textcolor = get_term_meta($top_recruiter_job_types[0]->term_id, 'jobsearch_field_jobtype_color', true);
                if ($top_job_style == 'slider') {
                    $job_type_str = '<span class="careerfy-top-recruiters-status" style="background-color: ' . $jobtype_textcolor . ' ">' . $top_recruiter_job_types[0]->name . '</span>';
                } else if ($top_job_style == 'slider2') {

                    $job_type_str = '<span style="color: ' . $jobtype_textcolor . ' "><i class="fa fa-bookmark"></i>' . $top_recruiter_job_types[0]->name . '</span>';

                } else {
                    $job_type_str = '<small class="careerfy-recentjob-type-text" style="background-color: ' . $jobtype_textcolor . ' ">' . $top_recruiter_job_types[0]->name . '</small>';

                }

            }


            if ($top_job_style == 'slider') { ?>
                <div class="careerfy-top-recruiters-slider-layer">
                    <div class="careerfy-top-recruiters">
                        <div class="careerfy-top-recruiters-slider-image">
                            <?php
                            if ($jobsearch_job_featured == 'on') { ?>
                                <strong class="promotepof-badge"><i class="fa fa-star"></i></strong>
                            <?php } ?>
                            <img src="<?php echo $post_thumbnail_src ?>" alt="">
                            <?php if (function_exists('jobsearch_empjobs_urgent_pkg_iconlab')) {
                                jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $top_recruiter_id, 'job_listv1');
                            } ?>
                        </div>
                        <div class="careerfy-top-recruiters-inner">
                            <?php
                            if ($job_type_str != '') {
                                echo($job_type_str);
                            } ?>
                            <h2>
                                <a href="<?php echo get_permalink($top_recruiter_id) ?>"><?php echo substr(get_the_title($top_recruiter_id), 0, 20) . (strlen(get_the_title($top_recruiter_id)) > 20 ? '...' : '') ?><?php echo($elaspedtime > $hourz ? '' : '<span class="careerfy-featuredjobs-listnew">' . esc_html__('New', 'careerfy-frame') . '</span>') ?></a>
                            </h2>
                            <ul>
                                <?php if (!empty($job_deadline)) { ?>
                                    <li><i class="careerfy-icon careerfy-calendar"></i>
                                        <span><?php echo esc_html__('Deadline:', 'careerfy-frame') ?></span> <?php echo esc_html__(date($wp_date_formate, $job_deadline), 'careerfy-frame') . "<br>"; ?>
                                    </li>
                                <?php } ?>
                                <?php if ($get_top_recruiter_job_location != "") { ?>
                                    <li><i class="careerfy-icon careerfy-pin"></i>
                                        <span><?php echo esc_html__('Location:', 'careerfy-frame') ?></span>
                                        <?php echo jobsearch_esc_html($get_top_recruiter_job_location); ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="careerfy-top-recruiters-inner2">
                            <p><?php echo substr(wp_kses($top_recruiter_post_content, array('p' => array())), 0, 100) ?></p>
                            <a href="<?php echo get_permalink($top_recruiter_id) ?>"
                               class="careerfy-top-recruiters-btn"><?php echo esc_html__('Get in touch', 'careerfy-frame') ?></a>
                        </div>
                    </div>
                </div>
            <?php } else if ($top_job_style == 'slider2') { ?>

                <div class="careerfy-top-recruiters-slider-layer">
                    <div class="careerfy-recruiters-top-list">
                        <?php if (function_exists('jobsearch_empjobs_urgent_pkg_iconlab')) {
                            jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $top_recruiter_id, 'job_listv1');
                        } ?>
                        <div class="careerfy-top-recruiters-slider-image">
                            <img src="<?php echo $post_thumbnail_src ?>" alt="">
                        </div>
                        <?php
                        if ($jobsearch_job_featured == 'on') { ?>
                            <strong class="promotepof-badge"><i class="fa fa-star"></i></strong>
                        <?php } ?>
                        <div class="careerfy-recruiters-top-list-top">
                            <h2>
                                <a href="<?php echo get_permalink($top_recruiter_id) ?>"><?php echo substr(get_the_title($top_recruiter_id), 0, 20) . (strlen(get_the_title($top_recruiter_id)) > 20 ? '...' : '') ?><?php echo($elaspedtime > $hourz ? '' : '<span class="careerfy-featuredjobs-listnew">' . esc_html__('New', 'careerfy-frame') . '</span>') ?></a>
                            </h2>
                            <?php
                            if ($job_type_str != '') {
                                echo($job_type_str);
                            }
                            if ($get_top_recruiter_job_location != "") { ?>
                                <small>
                                    <i class="careerfy-icon careerfy-pin"></i> <?php echo jobsearch_esc_html($get_top_recruiter_job_location); ?>
                                </small>
                            <?php } ?>
                        </div>
                        <div class="careerfy-recruiters-top-list-bottom">
                            <p><?php echo substr($top_recruiter_post_content, 0, 100) ?></p>
                            <a href="<?php echo get_permalink($top_recruiter_id) ?>"
                               class="careerfy-top-recruiters-btn"><?php echo esc_html__('Get in touch', 'careerfy-frame') ?></a>
                        </div>
                    </div>
                </div>

            <?php } else if ($top_job_style == 'slider3') { ?>

                <div class="careerfy-sixteen-jobs-layer">
                    <div class="careerfy-sixteen-jobs-grid">
                        <figure>
                            <?php if ($jobsearch_job_featured == 'on') { ?>
                                <span class="careerfy-jobs-style9-featured jobsearch-tooltipcon" title="Featured"><i
                                            class="fa fa-star"></i></span>
                            <?php } ?>
                            <div class="">
                                <?php if (function_exists('jobsearch_empjobs_urgent_pkg_iconlab')) {
                                    jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $top_recruiter_id, 'style10');
                                } ?>
                            </div>
                            <a <?php echo($employer_cover_image_src_style_str) ?>
                                    href="<?php echo get_permalink($top_recruiter_id) ?>"><img
                                        src="<?php echo $post_thumbnail_src ?>" alt=""></a>
                        </figure>
                        <div class="careerfy-sixteen-jobs-grid-text">
                            <div class="careerfy-sixteen-jobs-grid-top">
                                <?php if ($get_top_recruiter_job_location != "") { ?>
                                    <span><i class="fa fa-map-marker"></i> <?php echo jobsearch_esc_html($get_top_recruiter_job_location); ?></span>
                                <?php } ?>
                                <h2>
                                    <a href="<?php echo get_permalink($top_recruiter_id) ?>"><?php echo $get_job_title ?></a>
                                </h2>
                                <?php
                                $book_mark_args = array(
                                    'job_id' => $top_recruiter_id,
                                    'before_icon' => 'fa fa-heart-o',
                                    'after_icon' => 'fa fa-heart',
                                    'anchor_class' => 'careerfy-sixteen-jobs-grid-like'
                                );
                                do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args);
                                ?>
                            </div>
                            <p><?php echo limit_text(filter_var($top_recruiter_post_content, FILTER_SANITIZE_STRING), 12) ?></p>
                            <?php
                            if ($job_type_str != '' && $job_types_switch == 'on') {
                                echo force_balance_tags($job_type_str);
                            } ?>
                        </div>
                    </div>
                </div>
            <?php }
        }
    }

    private static function JobDetail($emporler_approval, $featured_only, $top_recruiter_per_page, $top_recruiter_order, $top_recruiter_orderby, $top_recruiter_cat)
    {
        global $jobsearch_shortcode_jobs_frontend;
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $is_filled_jobs = isset($jobsearch__options['job_allow_filled']) ? $jobsearch__options['job_allow_filled'] : '';

        $sh_atts = array();
        $post_ids = array();
        $all_post_ids = $jobsearch_shortcode_jobs_frontend->job_general_query_filter($post_ids, $sh_atts);

        $element_filter_arr = array();
//        $element_filter_arr[] = array(
//            'key' => 'jobsearch_field_job_publish_date',
//            'value' => current_time('timestamp'),
//            'compare' => '<=',
//        );
//
//        $element_filter_arr[] = array(
//            'key' => 'jobsearch_field_job_expiry_date',
//            'value' => current_time('timestamp'),
//            'compare' => '>=',
//        );
//
//        $element_filter_arr[] = array(
//            'key' => 'jobsearch_field_job_status',
//            'value' => 'approved',
//            'compare' => '=',
//        );

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
        if ($emporler_approval != 'off') {
            $element_filter_arr[] = array(
                'key' => 'jobsearch_job_employer_status',
                'value' => 'approved',
                'compare' => '=',
            );
        }

        if ($featured_only == 'yes') {
            $element_filter_arr[] = array(
                'key' => 'jobsearch_field_job_featured',
                'value' => 'on',
                'compare' => '=',
            );
        }

        $args = array(
            'posts_per_page' => $top_recruiter_per_page,
            'post_type' => 'job',
            'post_status' => 'publish',
            'order' => $top_recruiter_order,
            'orderby' => $top_recruiter_orderby,
            'fields' => 'ids', // only load ids
            'meta_query' => array(
                $element_filter_arr,
            ),
        );
        if ($top_recruiter_cat != '') {
            $args['tax_query'][] = array(
                'taxonomy' => 'sector',
                'field' => 'slug',
                'terms' => $top_recruiter_cat
            );
        }

        if (!empty($all_post_ids)) {
            $args['post__in'] = $all_post_ids;
        }

        return new \WP_Query($args);
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        extract(shortcode_atts(array(
            'top_recruiter_title' => '',
            'top_recruiter_desc' => '',
            'top_recruiter_cat' => '',
            'top_recruiter_order' => 'date',
            'top_recruiter_orderby' => 'DESC',
            'top_recruiter_per_page' => '10',
            'featured_only' => 'yes',
            'top_job_style' => 'slider',
            'title_img' => '',
            'job_list_loc_listing' => 'country,city',
        ), $atts));
        $title_img = $title_img != '' ? $title_img['url'] : '';

        $rand_num = rand(10000000, 99909999);
        $jobsearch__options = get_option('jobsearch_plugin_options');

        $emporler_approval = isset($jobsearch__options['job_listwith_emp_aprov']) ? $jobsearch__options['job_listwith_emp_aprov'] : '';
        $locations_view_type = isset($atts['job_list_loc_listing']) ? $atts['job_list_loc_listing'] : 'country,city';
        $loc_types_arr = $locations_view_type != '' ? $locations_view_type : '';
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
        $jobs_query = self::JobDetail($emporler_approval, $featured_only, $top_recruiter_per_page, $top_recruiter_order, $top_recruiter_orderby, $top_recruiter_cat);
        $totl_found_top_recruiter = $jobs_query->found_posts;
        $top_job_posts = $jobs_query->posts;

        ob_start();
        if ($top_job_style == 'slider') { ?>
            <?php if ($totl_found_top_recruiter > 0) { ?>
                <div class="careerfy-main-section careerfy-recent-list-full">
                    <div class="careerfy-top-recruiters-slider" id="top-recruiters-slider-<?php echo $rand_num ?>">
                        <?php echo self::GetSliderList($top_job_posts, $top_job_style, $loc_view_country, $loc_view_state, $loc_view_city); ?>
                    </div>
                </div>
            <?php } else {
                echo '<p>' . esc_html__('No Top Job Found.', 'careerfy-frame') . '</p>';
            } ?>

        <?php } else if ($top_job_style == 'slider2') { ?>

            <div class="careerfy-fancy-title-eleven careerfy-fancy-title-eleven-left">
                <?php if ($title_img != '') { ?>
                    <img src="<?php echo $title_img ?>" alt="">
                <?php } ?>
                <h2><?php echo $top_recruiter_title ?></h2>
                <span><?php echo $top_recruiter_desc ?></span>
            </div>
            <div class="careerfy-recruiters-top-list-two recruiters-slider-two"
                 id="top-recruiters-slider-<?php echo $rand_num ?>">
                <?php echo self::GetSliderList($top_job_posts, $top_job_style, $loc_view_country, $loc_view_state, $loc_view_city); ?>
            </div>

        <?php } else if ($top_job_style == 'slider3') { ?>

            <div class="careerfy-sixteen-jobs-slider">
                <?php if ($totl_found_top_recruiter > 0) {
                    echo self::GetSliderList($top_job_posts, $top_job_style, $loc_view_country, $loc_view_state, $loc_view_city);
                } else {
                    echo '<p>' . esc_html__('No Top Job Found.', 'careerfy-frame') . '</p>';
                } ?>
            </div>

        <?php } ?>
        <!-- Main Section -->
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                <?php if ($top_job_style == 'slider' || $top_job_style == 'slider2') {  ?>
                //*** Function Testimonial Slider
                jQuery('#top-recruiters-slider-<?php echo $rand_num ?>').slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 5000,
                    infinite: true,
                    dots: false,
                    arrows: false,
                    //prevArrow: "<span class='slick-arrow-left'><i class='careerfy-icon careerfy-next'></i></span>",
                    //nextArrow: "<span class='slick-arrow-right'><i class='careerfy-icon careerfy-next'></i></span>",
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                infinite: true,
                            }
                        },
                        {
                            breakpoint: 800,
                            settings: {
                                slidesToShow: 1,
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

                <?php } else { ?>
                jQuery('.careerfy-sixteen-jobs-slider').slick({
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    infinite: true,
                    dots: true,
                    arrows: false,
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
                <?php } ?>
            });
        </script>
        <?php
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    {
    }
}