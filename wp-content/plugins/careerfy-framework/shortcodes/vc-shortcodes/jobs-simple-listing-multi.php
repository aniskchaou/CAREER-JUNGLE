<?php
/**
 * Simple Jobs Listing Shortcode
 * @return html
 */
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class JobSearch_Careerfy_Simple_Jobs_Listings_multi
{

    public function __construct()
    {
        add_shortcode('jobsearch_simple_jobs_listing_multi', array($this, 'simple_jobs_listing_multi_shortcode'));
    }

    public function simple_jobs_listing_multi_shortcode($atts)
    {
        extract(shortcode_atts(array(
            'job_cat' => '',
            'featured_only' => '',
            'job_order' => 'DESC',
            'job_orderby' => 'date',
            'job_per_page' => '10',
        ), $atts));

        if (class_exists('JobSearch_plugin')) {
            $rand_num = rand(10000000, 99909999);
            $jobsearch__options = get_option('jobsearch_plugin_options');
            $emporler_approval = isset($jobsearch__options['job_listwith_emp_aprov']) ? $jobsearch__options['job_listwith_emp_aprov'] : '';
            $job_per_page = isset($job_per_page) && !empty($job_per_page) && $job_per_page > 0 ? $job_per_page : 10;
            $featured_jobs_posts = self::GetFeaturedJobsContent($emporler_approval, $job_per_page, $job_order, $job_orderby, $job_cat);
            $recent_jobs_posts = self::GetRecentJobsContent($emporler_approval, $job_per_page, $job_order, $job_orderby, $job_cat);

            ob_start(); ?>
            <div class="careerfy-jobs-btn-links">
                <a class="active" data-toggle="tab" href="#recentjobs"><?php echo esc_html__('Recent Jobs' ,'careerfy-frame') ?></a>
                <a data-toggle="tab" href="#featuredjobs"><?php echo esc_html__('Featured Jobs' ,'careerfy-frame') ?></a>
            </div>
            <?php
            if (!empty($featured_jobs_posts) || !empty($recent_jobs_posts)) { ?>
                <div class="tab-content">
                    <div id="recentjobs" class="tab-pane fade in active">
                        <div class="careerfy-refejobs-list">
                            <ul class="row">
                                <?php
                                self::load_more_recent_jobs_posts($recent_jobs_posts, '');
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div id="featuredjobs" class="tab-pane fade">
                        <div class="careerfy-refejobs-list">
                            <ul class="row">
                                <?php
                                self::load_more_featured_jobs_posts($featured_jobs_posts, '');
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                echo '<p>' . esc_html__('No job found.', 'careerfy-frame') . '</p>';
            } ?>
            </div>
            <?php
            $html = ob_get_clean();
            return $html;
        }
    }

    public static function load_more_featured_jobs_posts($jobs_posts, $job_per_page = '')
    {
        global $jobsearch_plugin_options, $sitepress;
        $carrerfy_date_formate = get_option('date_format');
        $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';
        $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
        $job_types_switch = isset($jobsearch_plugin_options['job_types_switch']) ? $jobsearch_plugin_options['job_types_switch'] : '';

        $all_locations_type = isset($jobsearch_plugin_options['all_locations_type']) ? $jobsearch_plugin_options['all_locations_type'] : '';

        if (!empty($jobs_posts)) {
            $count = 1;
            foreach ($jobs_posts as $job_id) {
                $jobsearch_sectors = wp_get_post_terms($job_id, 'sector', array("fields" => "all"));
                $post_thumbnail_id = function_exists('jobsearch_job_get_profile_image') ? jobsearch_job_get_profile_image($job_id) : 0;
                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
                $no_placeholder_img = '';
                if (function_exists('jobsearch_no_image_placeholder')) {
                    $no_placeholder_img = jobsearch_no_image_placeholder();
                }
                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : $no_placeholder_img;

                $jobsearch_job_min_salary = get_post_meta($job_id, 'jobsearch_field_job_salary', true);
                $jobsearch_job_max_salary = get_post_meta($job_id, 'jobsearch_field_job_max_salary', true);
                $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                $job_loc_contry = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
                $job_loc_city = get_post_meta($job_id, 'jobsearch_field_location_location3', true);
                $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
                $job_salary = jobsearch_job_offered_salary($job_id);
                if ($all_locations_type == 'api') {
                    if ($job_loc_city != '' && $job_loc_contry != '') {
                        $get_job_location = $job_loc_city . ', ' . $job_loc_contry;
                    } else if ($job_loc_city != '') {
                        $get_job_location = $job_loc_city;
                    } else if ($job_loc_contry != '') {
                        $get_job_location = $job_loc_contry;
                    }
                } else {
                    $job_city_title = '';
                    $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location3', true);
                    if ($get_job_city == '') {
                        $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location2', true);
                    }
                    if ($get_job_city != '') {
                        $get_job_country = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
                    }

                    $job_city_tax = $get_job_city != '' ? get_term_by('slug', $get_job_city, 'job-location') : '';
                    if (is_object($job_city_tax)) {
                        $job_city_title = isset($job_city_tax->name) ? $job_city_tax->name : '';

                        $job_country_tax = $get_job_country != '' ? get_term_by('slug', $get_job_country, 'job-location') : '';
                        if (is_object($job_country_tax)) {
                            $job_city_title .= isset($job_country_tax->name) ? ', ' . $job_country_tax->name : '';
                        }
                    } else if ($job_city_title == '') {
                        $get_job_country = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
                        $job_country_tax = $get_job_country != '' ? get_term_by('slug', $get_job_country, 'job-location') : '';
                        if (is_object($job_country_tax)) {
                            $job_city_title .= isset($job_country_tax->name) ? $job_country_tax->name : '';
                        }
                    }
                    if ($job_city_title != '') {
                        $get_job_location = $job_city_title;
                    }
                }
                $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
                $job_post_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true); ?>
                <li class="col-md-12">
                    <div class="careerfy-refejobs-list-inner">
                        <?php
                        jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id,'job_listv1');
                        ?>
                        <figure>
                            <a href="<?php echo get_permalink($job_id) ?>"><img src="<?php echo($post_thumbnail_src) ?>" alt=""></a>
                            <figcaption>
                                <h2>
                                    <a href="<?php echo get_permalink($job_id) ?>"><?php echo wp_trim_words(get_the_title($job_id), 3) ?></a>
                                </h2>
                                <span><?php echo jobsearch_job_get_company_name($job_id) ?></span>
                            </figcaption>
                        </figure>
                        <small><i class="careerfy-icon careerfy-briefcase"></i> <?php echo isset($jobsearch_sectors) && count($jobsearch_sectors) > 0 ? $jobsearch_sectors[0]->name : '' ?></small>
                        <small>
                        <?php if (!empty($get_job_location) && $all_location_allow == 'on') { ?>
                            <i class="careerfy-icon careerfy-pin-line"></i> <?php echo jobsearch_esc_html($get_job_location); ?>
                        <?php } ?>
                        </small>

                        <small>
                        <?php if ($jobsearch_job_min_salary != '' || $jobsearch_job_max_salary != '') { ?>
                            <i class="careerfy-icon careerfy-money"></i> <?php echo $job_salary  ?>
                        <?php } ?>
                        </small>
                        <small><i class="careerfy-icon careerfy-calendar-line"></i><?php echo date_i18n($carrerfy_date_formate, $job_post_date) ?></small>
                        <a href="<?php echo get_permalink($job_id) ?>"
                           class="careerfy-refejobs-list-btn"><span><?php echo esc_html__('View', 'careerfy-frame') ?></span></a>
                        <?php if ($jobsearch_job_featured == 'on') { ?>
                            <span class="careerfy-jobli-medium3"><i class="fa fa-star"></i></span>
                        <?php } ?>
                    </div>
                </li>
                <?php
                $count++;
            }
        }
    }

    public static function load_more_recent_jobs_posts($jobs_posts, $job_per_page = '')
    {
        global $jobsearch_plugin_options, $sitepress;
        $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';
        $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
        $job_types_switch = isset($jobsearch_plugin_options['job_types_switch']) ? $jobsearch_plugin_options['job_types_switch'] : '';
        $all_locations_type = isset($jobsearch_plugin_options['all_locations_type']) ? $jobsearch_plugin_options['all_locations_type'] : '';
        $carrerfy_date_formate = get_option('date_format');
        if (!empty($jobs_posts)) {
            $count = 1;
            foreach ($jobs_posts as $job_id) {
                $jobsearch_sectors = wp_get_post_terms($job_id, 'sector', array("fields" => "all"));


                //echo $term_list[0]->description ;
                $post_thumbnail_id = function_exists('jobsearch_job_get_profile_image') ? jobsearch_job_get_profile_image($job_id) : 0;
                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
                $no_placeholder_img = '';
                if (function_exists('jobsearch_no_image_placeholder')) {
                    $no_placeholder_img = jobsearch_no_image_placeholder();
                }
                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : $no_placeholder_img;

                $jobsearch_job_min_salary = get_post_meta($job_id, 'jobsearch_field_job_salary', true);
                $jobsearch_job_max_salary = get_post_meta($job_id, 'jobsearch_field_job_max_salary', true);
                $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                $job_loc_contry = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
                $job_loc_city = get_post_meta($job_id, 'jobsearch_field_location_location3', true);

                if ($all_locations_type == 'api') {
                    if ($job_loc_city != '' && $job_loc_contry != '') {
                        $get_job_location = $job_loc_city . ', ' . $job_loc_contry;
                    } else if ($job_loc_city != '') {
                        $get_job_location = $job_loc_city;
                    } else if ($job_loc_contry != '') {
                        $get_job_location = $job_loc_contry;
                    }
                } else {
                    $job_city_title = '';
                    $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location3', true);
                    if ($get_job_city == '') {
                        $get_job_city = get_post_meta($job_id, 'jobsearch_field_location_location2', true);
                    }
                    if ($get_job_city != '') {
                        $get_job_country = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
                    }

                    $job_city_tax = $get_job_city != '' ? get_term_by('slug', $get_job_city, 'job-location') : '';
                    if (is_object($job_city_tax)) {
                        $job_city_title = isset($job_city_tax->name) ? $job_city_tax->name : '';

                        $job_country_tax = $get_job_country != '' ? get_term_by('slug', $get_job_country, 'job-location') : '';
                        if (is_object($job_country_tax)) {
                            $job_city_title .= isset($job_country_tax->name) ? ', ' . $job_country_tax->name : '';
                        }
                    } else if ($job_city_title == '') {
                        $get_job_country = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
                        $job_country_tax = $get_job_country != '' ? get_term_by('slug', $get_job_country, 'job-location') : '';
                        if (is_object($job_country_tax)) {
                            $job_city_title .= isset($job_country_tax->name) ? $job_country_tax->name : '';
                        }
                    }
                    if ($job_city_title != '') {
                        $get_job_location = $job_city_title;
                    }
                }
                $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
                $job_post_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                ?>
                <li class="col-md-12">
                    <div class="careerfy-refejobs-list-inner">
                        <?php
                        jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id,'job_listv1');
                        ?>
                        <figure>
                            <a href="<?php echo get_permalink($job_id) ?>"><img src="<?php echo($post_thumbnail_src) ?>"
                                                                                alt=""></a>
                            <figcaption>
                                <h2>
                                    <a href="<?php echo get_permalink($job_id) ?>"><?php echo wp_trim_words(get_the_title($job_id), 3) ?></a>
                                </h2>
                                <span><?php echo jobsearch_job_get_company_name($job_id) ?></span>
                            </figcaption>
                        </figure>
                        <small><i class="careerfy-icon careerfy-briefcase"></i> <?php echo isset($jobsearch_sectors) && count($jobsearch_sectors) > 0 ? $jobsearch_sectors[0]->name : '' ?></small>
                        <small>
                        <?php if (!empty($get_job_location) && $all_location_allow == 'on') { ?>
                            <i class="careerfy-icon careerfy-pin-line"></i> <?php echo jobsearch_esc_html($get_job_location); ?>
                        <?php } ?>
                        </small>
                        <small>
                        <?php if ($jobsearch_job_min_salary != '' || $jobsearch_job_max_salary != '') { ?>
                            <i class="careerfy-icon careerfy-money"></i> <?php echo $jobsearch_job_min_salary . "K" ?>
                                -<?php echo $jobsearch_job_max_salary . "K" ?>
                        <?php } ?>
                        </small>
                        <small><i class="careerfy-icon careerfy-calendar-line"></i><?php echo date_i18n($carrerfy_date_formate, $job_post_date) ?>
                        </small>
                        <a href="<?php echo get_permalink($job_id) ?>"
                           class="careerfy-refejobs-list-btn"><span><?php echo esc_html__('View', 'careerfy-frame') ?></span></a>
                    </div>
                </li>
                <?php
                $count++;
            }

        }
    }

    private static function GetFeaturedJobsContent($emporler_approval, $job_per_page, $job_order, $job_orderby, $job_cat)
    {
        $element_filter_arr = array();
        $element_filter_arr[] = array(
            'key' => 'jobsearch_field_job_status',
            'value' => 'approved',
            'compare' => '=',
        );

        if ($emporler_approval != 'off') {
            $element_filter_arr[] = array(
                'key' => 'jobsearch_job_employer_status',
                'value' => 'approved',
                'compare' => '=',
            );
        }

        $element_filter_arr[] = array(
            'key' => 'jobsearch_field_job_featured',
            'value' => 'on',
            'compare' => '=',
        );

        $args = array(
            'posts_per_page' => $job_per_page,
            'post_type' => 'job',
            'post_status' => 'publish',
            'order' => $job_order,
            'orderby' => $job_orderby,
            'fields' => 'ids', // only load ids
            'meta_query' => array(
                $element_filter_arr,
            ),
        );
        if ($job_cat != '') {
            $args['tax_query'][] = array(
                'taxonomy' => 'sector',
                'field' => 'slug',
                'terms' => $job_cat
            );
        }

        $jobs_query = new WP_Query($args);
        return $jobs_query->posts;
    }

    private static function GetRecentJobsContent($emporler_approval, $job_per_page, $job_order, $job_orderby, $job_cat)
    {
        global $jobsearch_shortcode_jobs_frontend;
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $is_filled_jobs = isset($jobsearch__options['job_allow_filled']) ? $jobsearch__options['job_allow_filled'] : '';
        
        $jobsearch_jobs_listin_sh = $jobsearch_shortcode_jobs_frontend;
        
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
        
        //
        $post_ids = array();
        $sh_atts = array();
        $all_post_ids = array();
        if (is_object($jobsearch_jobs_listin_sh)) {
            $all_post_ids = $jobsearch_jobs_listin_sh->job_general_query_filter($post_ids, $sh_atts);
        }
        //
        
//        $element_filter_arr[] = array(
//            'key' => 'jobsearch_field_job_featured',
//            'value' => 'on',
//            'compare' => '!=',
//        );

        if ($emporler_approval != 'off') {
            $element_filter_arr[] = array(
                'key' => 'jobsearch_job_employer_status',
                'value' => 'approved',
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

        $args = array(
            'posts_per_page' => $job_per_page,
            'post_type' => 'job',
            'post_status' => 'publish',
            'order' => 'DESC',
            'orderby' => $job_orderby,
            'fields' => 'ids', // only load ids
            'meta_query' => array(
                $element_filter_arr,
            ),
        );
        if ($job_cat != '') {
            $args['tax_query'][] = array(
                'taxonomy' => 'sector',
                'field' => 'slug',
                'terms' => $job_cat
            );
        }
        
        if (!empty($all_post_ids)) {
            $args['post__in'] = $all_post_ids;
        }

        $jobs_query = new WP_Query($args);
        return $jobs_query->posts;
    }

}

return new JobSearch_Careerfy_Simple_Jobs_Listings_multi();
