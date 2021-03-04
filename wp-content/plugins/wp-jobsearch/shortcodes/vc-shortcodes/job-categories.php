<?php
/**
 * Advance Search Shortcode
 * @return html
 */
add_shortcode('jobsearch_job_categories', 'jobsearch_job_categories_shcallback');

function jobsearch_job_categories_shcallback($atts) {
    global $jobsearch_plugin_options, $wpdb;

    extract(shortcode_atts(array(
        'num_cats' => '',
        'result_page' => '',
        'order_by' => '',
        'view_more_btn' => '',
        'cat_link_text' => '',
        'cat_link_text_url' => '',
                    ), $atts));

    $to_result_page = $result_page;

    $joptions_search_page = isset($jobsearch_plugin_options['jobsearch_search_list_page']) ? $jobsearch_plugin_options['jobsearch_search_list_page'] : '';
    if ($joptions_search_page != '') {
        $joptions_search_page = jobsearch__get_post_id($joptions_search_page, 'page');
    }
    if ($result_page <= 0 && $joptions_search_page > 0) {
        $to_result_page = $joptions_search_page;
    }

    $to_result_page = absint($to_result_page);

    if ($order_by == 'id') {
        $get_db_terms = $wpdb->get_col($wpdb->prepare("SELECT terms.term_id FROM $wpdb->terms AS terms"
                        . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                        . " WHERE term_tax.taxonomy=%s"
                        . " ORDER BY terms.term_id DESC", 'sector'));
    } else {
        $get_db_terms = $wpdb->get_col($wpdb->prepare("SELECT terms.term_id FROM $wpdb->terms AS terms"
                        . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                        . " LEFT JOIN $wpdb->termmeta AS term_meta ON(terms.term_id = term_meta.term_id) "
                        . " WHERE term_tax.taxonomy=%s AND term_meta.meta_key=%s"
                        . " ORDER BY cast(term_meta.meta_value as unsigned) DESC", 'sector', 'active_jobs_count'));
    }

    ob_start();
    ?>
    <div class="categories-list">
        <?php
        if (!empty($get_db_terms) && !is_wp_error($get_db_terms)) {
            ?>
            <ul class="jobsearch-row">
                <?php
                foreach ($get_db_terms as $term_id) {
                    $term_sector = get_term_by('id', $term_id, 'sector');
                    $job_args = array(
                        'posts_per_page' => '1',
                        'post_type' => 'job',
                        'post_status' => 'publish',
                        'fields' => 'ids', // only load ids
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'sector',
                                'field' => 'slug',
                                'terms' => $term_sector->slug
                            )
                        ),
                        'meta_query' => array(
                            array(
                                'key' => 'jobsearch_field_job_publish_date',
                                'value' => strtotime(current_time('d-m-Y H:i:s')),
                                'compare' => '<=',
                            ),
                            array(
                                'key' => 'jobsearch_field_job_expiry_date',
                                'value' => strtotime(current_time('d-m-Y H:i:s')),
                                'compare' => '>=',
                            ),
                            array(
                                'key' => 'jobsearch_field_job_status',
                                'value' => 'approved',
                                'compare' => '=',
                            )
                        ),
                    );
                    $jobs_query = new WP_Query($job_args);
                    $found_jobs = $jobs_query->found_posts;
                    wp_reset_postdata();

                    //
                    $cat_goto_link = add_query_arg(array('sector_cat' => $term_sector->slug), get_permalink($to_result_page));
                    $cat_goto_link = apply_filters('jobsearch_job_sector_cat_result_link', $cat_goto_link, $term_sector->slug);
                    ?>
                    <li class="jobsearch-column-3">
                        <a href="<?php echo($cat_goto_link) ?>"><?php echo($term_sector->name) ?></a>
                        <?php
                        if ($found_jobs == 1) {
                            ?>
                            <span><?php printf(esc_html__('(%s Open Vacancy)', 'wp-jobsearch'), $found_jobs) ?></span>
                            <?php
                        } else {
                            ?>
                            <span><?php printf(esc_html__('(%s Open Vacancies)', 'wp-jobsearch'), $found_jobs) ?></span>
                            <?php
                        }
                        ?>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <?php
        }
        ?>
    </div>
    <?php
    if ($view_more_btn == 'yes') {
        ?>
        <div class="jobsearch-jobcates-btn"><a href="<?php echo ($cat_link_text_url) ?>"><?php echo ($cat_link_text) ?></a></div>
        <?php
    }
    $html = ob_get_clean();
    return $html;
}
