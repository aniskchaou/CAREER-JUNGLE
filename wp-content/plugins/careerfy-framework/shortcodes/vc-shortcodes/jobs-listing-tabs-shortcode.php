<?php
/**
 * Advance Search Shortcode
 * @return html
 */
add_shortcode('jobsearch_job_listin_tabs_shortcode', 'jobsearch_job_listin_tabs_shortcode_callback');

function jobsearch_job_listin_tabs_shortcode_callback($atts) {
    global $jobsearch_plugin_options, $sitepress;
    extract(shortcode_atts(array(
        'job_cats_filter' => '',
        'job_per_page' => '',
                    ), $atts));

    if (class_exists('JobSearch_plugin')) {
        $job_per_page = isset($job_per_page) && !empty($job_per_page) ? $job_per_page : -1;
        $sector_list = explode(",", $job_cats_filter);
        wp_enqueue_script('isotope-min');
        $sector_terms = get_terms(array(
            'taxonomy' => 'sector',
            'hide_empty' => false,
        ));
        wp_enqueue_script('jobsearch-job-functions-script');
        wp_enqueue_script('jobsearch-shortlist-functions-script');
        ob_start();
        $html = '';
        ?>
        <div class="careerfy-animate-filter">
            <ul class="filters-button-group">
                <li><a data-filter="*" class="is-checked" href="javascript:void(0)"><?php echo esc_html__('All Categories', 'careerfy-frame'); ?></a></li>
                <?php
                if (!empty($sector_list) && !is_wp_error($sector_list)) {
                    foreach ($sector_list as $sector_list_item) {
                        if (isset($sector_list_item) && !empty($sector_list_item)) {
                            $term_data = get_term_by('slug', $sector_list_item, 'sector');
                            ?>
                            <li><a data-filter=".<?php echo ($term_data->slug) ?>" href="javascript:void(0)"><?php echo ($term_data->name) ?></a></li>
                            <?php
                        }
                    }
                }
                ?>
            </ul>
        </div>
        <div class="careerfy-job-listing careerfy-dream-grid careerfy-animated-filter-list">
            <ul class="row">
                <?php
                if (!empty($sector_list) && !is_wp_error($sector_list)) {
                    do_action('job_fiter_items', $sector_list, $job_per_page);
                }
                ?>
            </ul>
        </div>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
}

function job_fiter_items_callback($cat_list = array(), $job_per_page = '') {
    
    wp_enqueue_script('jobsearch-job-functions-script');
    if (class_exists('JobSearch_plugin')) {
        $jobsearch__options = get_option('jobsearch_plugin_options');

        $emporler_approval = isset($jobsearch__options['job_listwith_emp_aprov']) ? $jobsearch__options['job_listwith_emp_aprov'] : '';
        $element_filter_arr = array();
        $element_filter_arr[] = array(
            'key' => 'jobsearch_field_job_publish_date',
            'value' => current_time('timestamp'),
            'compare' => '<=',
        );

        $element_filter_arr[] = array(
            'key' => 'jobsearch_field_job_expiry_date',
            'value' => current_time('timestamp'),
            'compare' => '>=',
        );

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
        $args = array(
            'posts_per_page' => $job_per_page,
            'post_type' => 'job',
            'post_status' => 'publish',
            'fields' => 'ids',
            'meta_query' => array(
                $element_filter_arr,
            ),
        );
        $args['tax_query'][] = array(
            'taxonomy' => 'sector',
            'field' => 'slug',
            'terms' => ($cat_list)
        );

        $the_query = new WP_Query($args);
        if ($the_query->have_posts()) :
            while ($the_query->have_posts()) : $the_query->the_post();
                global $post, $jobsearch_member_profile;
                $job_id = $post;
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
                $job_type_str = function_exists('jobsearch_job_get_all_jobtypes') ? jobsearch_job_get_all_jobtypes($job_id, '', '', '', '', '', 'span') : '';
                $sector_str = function_exists('jobsearch_job_get_all_sectors') ? jobsearch_job_get_all_sectors($job_id, '', '', '', '<li>', '</li>') : '';
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
                $terms = get_the_terms($job_id, 'sector');
                $term = array_pop($terms);
                ?>
                <li class="col-md-4 element-item <?php echo ($term->slug); ?>">
                    <div class="careerfy-dream-grid-wrap">
                        <figure><?php if ($post_thumbnail_src != '') { ?>
                                <a href="<?php the_permalink(); ?>">
                                    <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">
                                </a>
                            <?php } if ($jobsearch_job_featured == 'on') {
                                ?>
                                <span class="careerfy-dream-featured"><?php echo esc_html__('Featured', 'careerfy-frame'); ?></span>
                                <?php
                            }
                            $like_args = array(
                                'job_id' => $job_id,
                                'before_icon' => 'fa fa-heart-o',
                                'after_icon' => 'fa fa-heart',
                                'container_class' => '',
                                'anchor_class' => 'careerfy-dream-grid-like',
                            );
                            do_action('jobsearch_job_shortlist_button_frontend', $like_args);
                            ?>
                        </figure>
                        <div class="careerfy-dream-grid-text">
                            <h2><a href="<?php echo esc_url(get_permalink($job_id)); ?>"><?php echo esc_html(wp_trim_words(get_the_title($job_id), 6)); ?></a></h2>
                            <?php
                            ob_start();
                            ?>
                            <span><?php printf(esc_html__('Published %s by ', 'careerfy-frame'), jobsearch_time_elapsed_string($job_post_date)); ?><?php echo ($company_name) ?></span>
                            <?php
                            $comp_name_html = ob_get_clean();
                            echo apply_filters('jobsearch_empname_in_joblistin', $comp_name_html, $job_id, 'job_listin_tabs');
                            ?>
                            <ul>
                                <?php
                                if ($job_city_title != '') { ?>
                                    <li><i class="fa fa-map-marker"></i> <?php echo jobsearch_esc_html($job_city_title) ?></li>
                                <?php } ?>
                                <li><i class="fa fa-clock-o"></i> <?php
                                    $job_type = wp_get_post_terms($job_id, 'jobtype');
                                    if (isset($job_type) && $job_type != '') {
                                        $job_type_count = count($job_type);
                                        foreach ($job_type as $term) :
                                            echo esc_html($term->name) . ' ';
                                        endforeach;
                                    }
                                    ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
                <?php
            endwhile;
        endif;
        wp_reset_postdata();
    }
}

add_action('job_fiter_items', 'job_fiter_items_callback', 10, 2);
