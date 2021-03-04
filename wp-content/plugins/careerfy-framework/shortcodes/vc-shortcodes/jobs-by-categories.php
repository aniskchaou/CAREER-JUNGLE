<?php
/**
 * Job Categories Shortcode
 * @return html
 */
add_shortcode('careerfy_jobs_by_categories', 'careerfy_jobs_by_categories_shortcode');
function careerfy_jobs_by_categories_shortcode($atts)
{
    extract(shortcode_atts(array(
        'cats_view' => '',
        'num_cats' => '-1',
        'cat_title' => '',
        'result_page' => '',
        'sector_job_counts' => 'yes',
        'cat_link_text' => '',
        'cat_link_text_url' => '',
        'order_by' => 'jobs_count',
    ), $atts));
    //
    $rand_num = rand(10000000, 99909999);
    ob_start();
    if (class_exists('JobSearch_plugin')) { ?>
        <div class="careerfy-section-premium-wrap">
            <div class="careerfy-section-title-style">
                <?php if ($cat_title != '') { ?>
                    <h2><?php echo $cat_title ?></h2>
                <?php } ?>
                <form>
                    <label><?php echo esc_html__('Filter by:', 'careerfy-frame') ?></label>
                    <div class="careerfy-select-style">
                        <select class="selectize-select" id="jobs-by-filter">
                            <option value="categories"><?php echo esc_html__('Categories', 'careerfy-frame') ?></option>
                            <option value="companies"><?php echo esc_html__('Companies', 'careerfy-frame') ?></option>
                        </select>

                    </div>
                </form>
            </div>
            <?php

            $get_db_terms = get_DB_terms($order_by);

            if (!empty($get_db_terms) && !is_wp_error($get_db_terms)) { ?>
                <div class="careerfy-browse-links">
                    <div class="ajax-loader hidden">
                        <div class="ajax-loader-inner">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div>
                    <ul id="append-job-categories-<?php echo($rand_num) ?>">
                        <?php
                        $term_count = 1;
                        foreach ($get_db_terms as $term_id) {
                            $term_sector = get_term_by('id', $term_id, 'sector');

                            $jobs_query = get_jobs_query($term_sector, $num_cats);
                            $found_jobs = $jobs_query->found_posts;
                            wp_reset_postdata();

                            $cat_goto_link = add_query_arg(array('sector_cat' => $term_sector->slug), get_permalink($result_page));
                            $cat_goto_link = apply_filters('jobsearch_job_sector_cat_result_link', $cat_goto_link, $term_sector->slug);

                            ob_start();
                            ?>
                            <li>
                                <a href="<?php echo($cat_goto_link) ?>"><?php echo($term_sector->name) ?><?php
                                    if ($sector_job_counts == 'yes') {
                                        printf(esc_html__('(%s)', 'careerfy-frame'), $found_jobs);
                                    }
                                    ?></a>
                            </li>
                            <?php

                            $catitem_html = ob_get_clean();
                            echo apply_filters('careerfy_job_cats_shcode_citem_html', $catitem_html, $term_sector, $atts, $found_jobs);

                            if ($num_cats > 0 && $term_count >= $num_cats) {
                                break;
                            }
                            $term_count++;
                        }
                        ?>
                    </ul>
                    <?php if ($cat_link_text != "") { ?>
                        <div class="careerfy-browse-links-btn"><a
                                    href="<?php echo $cat_link_text_url ?>"><?php echo $cat_link_text ?></a></div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <script type="text/javascript">
        var $ = jQuery;
        $(document).ready(function () {
            $('#jobs-by-filter').on("change", function () {

                var _this = $(this);
                var _ajaxLoader = $(".ajax-loader");
                var job_filter_action = _this.val() == 'companies' ? 'jobsearch_load_companies_list' : 'jobsearch_load_category_list';
                var _this = jQuery(this),
                    this_html = _this.html(),
                    appender_con = jQuery('#append-job-categories-<?php echo($rand_num) ?>'),
                    ajax_url = '<?php echo admin_url('admin-ajax.php') ?>';

                if (!_this.hasClass('ajax-loadin')) {
                    _this.addClass('ajax-loadin');
                    _ajaxLoader.removeClass("hidden");

                    var request = jQuery.ajax({
                        url: ajax_url,
                        method: "POST",
                        data: {
                            sector_job_counts: '<?php echo($sector_job_counts) ?>',
                            post_per_page: <?php echo $num_cats ?>,
                            action: job_filter_action
                        },
                        dataType: "json"
                    });

                    request.done(function (response) {
                        if ('undefined' !== typeof response.html) {

                            if (_this.val() == 'categories') {
                                _this.find("option[value='categories']").attr("selected", "selected");
                            }
                            appender_con.html("");
                            appender_con.append(response.html).hide().slideDown("1500");
                        }
                        _this.html(this_html);
                        _this.removeClass('ajax-loadin');
                        _ajaxLoader.addClass("hidden");
                    });
                    request.fail(function (jqXHR, textStatus) {
                        _this.html(this_html);
                        _this.removeClass('ajax-loadin');
                    });
                }
                return false;
            })
        })
    </script>
    <?php
    $html = ob_get_clean();
    return $html;
}

function get_DB_terms($order_by)
{
    global $wpdb;
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
    return $get_db_terms;
}

function get_jobs_query($term_sector, $num_cats = 0)
{
    global $jobsearch_shortcode_jobs_frontend;
    $jobsearch_jobs_listin_sh = $jobsearch_shortcode_jobs_frontend;

    $jobsearch__options = get_option('jobsearch_plugin_options');
    $emporler_approval = isset($jobsearch__options['job_listwith_emp_aprov']) ? $jobsearch__options['job_listwith_emp_aprov'] : '';
    $is_filled_jobs = isset($jobsearch__options['job_allow_filled']) ? $jobsearch__options['job_allow_filled'] : '';

    $element_filter_arr = array();
//    $element_filter_arr[] = array(
//        'key' => 'jobsearch_field_job_publish_date',
//        'value' => strtotime(current_time('d-m-Y H:i:s')),
//        'compare' => '<=',
//    );
//    $element_filter_arr[] = array(
//        'key' => 'jobsearch_field_job_expiry_date',
//        'value' => strtotime(current_time('d-m-Y H:i:s')),
//        'compare' => '>=',
//    );
//    $element_filter_arr[] = array(
//        'key' => 'jobsearch_field_job_status',
//        'value' => 'approved',
//        'compare' => '=',
//    );

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

    $job_args = array(
        'posts_per_page' => $num_cats,
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
    );

    if (!empty($element_filter_arr)) {
        $job_args['meta_query'] = $element_filter_arr;
    }

    if (!empty($all_post_ids)) {
        $job_args['post__in'] = $all_post_ids;
    }

    return new WP_Query($job_args);
}

add_action('wp_ajax_jobsearch_load_companies_list', 'jobsearch_load_companies_list');
add_action('wp_ajax_nopriv_jobsearch_load_companies_list', 'jobsearch_load_companies_list');
function jobsearch_load_companies_list()
{
    $sector_job_counts = isset($_POST['sector_job_counts']) ? $_POST['sector_job_counts'] : '';
    $element_filter_arr = array();
    $element_filter_arr[] = array(
        'key' => 'jobsearch_field_employer_approved',
        'value' => 'on',
        'compare' => '=',
    );

    $args = array(
        'posts_per_page' => $_POST['post_per_page'],
        'post_type' => 'employer',
        'post_status' => 'publish',
        'order' => 'DESC',
        'orderby' => 'title',
        'fields' => 'ids', // only load ids
        'meta_query' => array(
            $element_filter_arr,
        ),
    );

    $employers_query = new WP_Query($args);
    $employers_posts = $employers_query->posts;

    if (!empty($employers_posts)) {
        ob_start();
        foreach ($employers_posts as $employer_id) {
            $job_search_employer_info = get_post($employer_id);
            $jobsearch_employer_job_count = get_post_meta($employer_id, 'jobsearch_field_employer_job_count', true);
            ?>
            <li>
                <a href="<?php echo get_permalink($employer_id) ?>">
                    <?php echo $job_search_employer_info->post_title ?>
                    <?php if ($sector_job_counts == 'yes') { ?>
                        &nbsp;(<?php echo absint($jobsearch_employer_job_count) ?>)<?php } ?></a></li>
            <?php

        }

        $html = ob_get_clean();
        echo json_encode(array('html' => $html));
        wp_die();

    } else {
        echo '<p>' . esc_html__('No employer found.', 'careerfy-frame') . '</p>';
    }
}

add_action('wp_ajax_jobsearch_load_category_list', 'jobsearch_load_category_list');
add_action('wp_ajax_nopriv_jobsearch_load_category_list', 'jobsearch_load_category_list');
function jobsearch_load_category_list($order_by)
{
    $sector_job_counts = isset($_POST['sector_job_counts']) ? $_POST['sector_job_counts'] : '';

    $get_db_terms = get_DB_terms($order_by);
    ob_start();
    foreach ($get_db_terms as $term_id) {
        $term_sector = get_term_by('id', $term_id, 'sector');

        $jobs_query = get_jobs_query($term_sector);
        $found_jobs = $jobs_query->found_posts;
        wp_reset_postdata();

        $cat_goto_link = add_query_arg(array('sector_cat' => $term_sector->slug), get_permalink($result_page));
        $cat_goto_link = apply_filters('jobsearch_job_sector_cat_result_link', $cat_goto_link, $term_sector->slug);
        ?>
        <li>
            <a href="<?php echo($cat_goto_link) ?>"><?php echo($term_sector->name) ?><?php if ($sector_job_counts == 'yes') {
                    printf(esc_html__('(%s)', 'careerfy-frame'), $found_jobs);
                } ?></a>
        </li>
        <?php
    }
    $html = ob_get_clean();
    echo json_encode(array('html' => $html));
    wp_die();
}


