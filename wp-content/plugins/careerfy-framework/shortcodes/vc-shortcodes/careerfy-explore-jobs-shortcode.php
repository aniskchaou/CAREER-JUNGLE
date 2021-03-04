<?php
/**
 * Candidate Packages Shortcode
 * @return html
 */
if (!defined('ABSPATH')) {
    die;
}

class JobSearch_Careerfy_Explore_jobs_list
{
    public function __construct()
    {
        add_shortcode('careerfy_explore_jobs', array($this, 'careerfy_explore_jobs_shortcode'));
        add_shortcode('careerfy_explore_jobs_item', array($this, 'careerfy_explore_jobs_item_shortcode'));

        add_action('wp_ajax_jobsearch_load_more_list', array($this, 'load_more_in_list'));
        add_action('wp_ajax_nopriv_jobsearch_load_more_list', array($this, 'load_more_in_list'));

        add_action('wp_ajax_jobsearch_load_more_top_companies_list', array($this, 'load_more_top_comapnies_list'));
        add_action('wp_ajax_nopriv_jobsearch_load_more_top_companies_list', array($this, 'load_more_top_comapnies_list'));
    }


    public function careerfy_explore_jobs_shortcode($atts, $content = '')
    {
        global $title_color, $list_items_color, $load_more, $list_view, $load_more_text;
        extract(shortcode_atts(array(
            'title_color' => '',
            'list_items_color' => '',
            'btn_color' => '',
            'button_text' => '',
            'button_url' => '',
            'btn_text_color' => '',
            'load_more' => 'yes',
            'list_view' => 'style1',
            'load_more_text' => '',
        ), $atts));

        $html = '
        <div class="row">
            ' . do_shortcode($content) . '';

        if ($list_view == 'style2') {
            if ($button_text != "") {
                $html .= '<div class="col-md-12" >
               <div class="careerfy-thirteen-browse-alljobs-btn" > <a href = "' . $button_url . '" > ' . $button_text . '</a > </div >
        
        </div>';
            }

        } else {

            if ($button_text != "") {
                $html .= '<div class="col-md-12" >
               <div class="careerfy-fifteen-browse-btn" > <a href = "' . $button_url . '" > ' . $button_text . '</a > </div >
           
        </div>';
            }

        }
        $html .= '</div>';


        if ($list_view == 'style2') {

            $html .= '<style>
        .careerfy-thirteen-browse-alljobs-btn a {
                border-color: ' . $btn_color . ';
                background: ' . $btn_color . ';
                color: ' . $btn_text_color . ';
                 }
        </style>
        ' . "\n";
        } else {
            $html .= '<style>
        .careerfy-fifteen-browse-btn a {
                border-color: ' . $btn_color . ';
                color: ' . $btn_text_color . ';
                 }
        </style>' . "\n";
        }

        return $html;
    }


    public function careerfy_explore_jobs_item_shortcode($atts)
    {
        global $title_color, $list_items_color, $load_more, $list_view, $load_more_text, $result_page, $jobs_by;
        extract(shortcode_atts(array(
            'jobs_by' => 'jobtype',
            'jobs_numbers' => '10',
            'explore_job_title' => '',
            'job_order' => 'DESC',
            'employer_cat' => '',
            'result_page' => '',
        ), $atts));

        $totl_explore_jobs = '';
        $total_jobs = '';
        if ($jobs_by != 'employer') {

            $jobs_detail = get_terms(array(
                'taxonomy' => $jobs_by,
                'hide_empty' => false,
            ));
            $total_jobs = count($jobs_detail);

            $jobs_by_detail = get_terms(array(
                'taxonomy' => $jobs_by,
                'hide_empty' => false,
                'number' => $jobs_numbers,
                'offset' => 0,
                'order' => $job_order,
            ));
            $totl_explore_jobs = count($jobs_by_detail);

        } else {

            $element_filter_arr = array();
            $element_filter_arr[] = array(
                'key' => 'jobsearch_field_employer_approved',
                'value' => 'on',
                'compare' => '=',
            );

            $args = array(
                'posts_per_page' => $jobs_numbers,
                'post_type' => 'employer',
                'post_status' => 'publish',
                'order' => $job_order,
                'orderby' => 'meta_value_num',
                'meta_key' => 'jobsearch_field_employer_job_count',
                'meta_query' => array(
                    $element_filter_arr,
                ),
            );
            if ($employer_cat != '') {
                $args['tax_query'][] = array(
                    'taxonomy' => 'sector',
                    'field' => 'slug',
                    'terms' => $employer_cat
                );
            }

            $employers_query = new WP_Query($args);
            $totl_found_jobs = $employers_query->found_posts;
            $employers_posts = $employers_query->posts;

        }
        $title_color = $title_color != "" ? 'style="color: ' . $title_color . ' "' : "";
        $load_more_text = $load_more_text != "" ? $load_more_text : 'More Jobs';
        $rand_num = rand(10000000, 99909999);

        ob_start();

        $list_items_view = '';
        if ($list_view == 'style1') {
            $list_items_view = 'careerfy-explore-jobs-links';
        }
        $list_items_view_sec = '';
        if ($list_view == 'style2') {

            $list_items_view_sec = 'careerfy-browsejobs-links';

        } else if ($list_view == 'style3') {

            $list_items_view_sec = 'careerfy-fifteen-browse-links';

        } else {

            $list_items_view_sec = 'careerfy-sixteen-jobs-links';

        } ?>
        <!-- Services Links -->
        <div class="col-md-3 <?php echo $list_items_view ?>">
            <h2 <?php echo $title_color ?>><?php echo $explore_job_title ?></h2>

            <?php if ($list_view != 'style1'){ ?>

            <div class="<?php echo $list_items_view_sec ?>">
                <?php } ?>

                <ul id="main-list-<?php echo($rand_num) ?>">

                    <?php if ($jobs_by != 'employer') {

                        if ($totl_explore_jobs > 0) { ?>
                            <?php self::load_more_explore_jobs($jobs_by_detail, $list_items_color, $list_view); ?>
                            <?php if ($total_jobs >= $jobs_numbers && $load_more == 'yes') { ?>
                                <li class="morejobs-link"><a
                                            href="javascript:void(0)" class="load-more-<?php echo $rand_num ?>"
                                            data-tpages="<?php echo $totl_explore_jobs ?>"
                                            list-style="<?php echo $list_items_color ?>"
                                            data-totalJobs="<?php echo $total_jobs ?>"><?php echo esc_html__($load_more_text, 'careerfy-frame') ?><?php echo $list_view == 'style1' ? '<i class="fa fa-angle-double-right"></i>' : ''; ?></a>
                                </li>
                            <?php }
                        } else { ?>
                            <p><?php echo esc_html__("No record found", "careerfy-frame") ?></p>
                        <?php }

                    } else {

                        if ($totl_found_jobs > 0) {

                            self::load_more_top_companies($employers_posts, $list_items_color, $list_view);

                            if ($totl_found_jobs >= $jobs_numbers && $load_more == 'yes') { ?>
                                <li class="morejobs-link"><a
                                            href="javascript:void(0)"
                                            class="load-more-companies-<?php echo $rand_num ?>"
                                            data-tpages="<?php echo $totl_found_jobs ?>"
                                            list-item-color="<?php echo $list_items_color ?>"
                                            jobs-results="<?php echo $jobs_numbers ?>"
                                            data-gtopage="2"><?php echo esc_html__($load_more_text, 'careerfy-frame') ?><?php echo $list_view == 'style1' ? '<i class="fa fa-angle-double-right"></i>' : ''; ?></a>
                                </li>
                            <?php }

                        } else { ?>

                            <p><?php echo esc_html__("No record found", "careerfy-frame") ?></p>

                        <?php }
                    } ?>

                </ul>
                <?php if ($list_view != 'style1'){ ?>
            </div>
        <?php } ?>
        </div>
        <!-- Services Links -->
        <?php if ($jobs_by != 'employer') { ?>
        <script type="text/javascript">
            jQuery(document).on('click', '.load-more-<?php echo($rand_num) ?>', function (e) {
                e.preventDefault();
                var _this = jQuery(this),
                    total_jobs_list = _this.attr('data-totalJobs'),
                    page_num = _this.attr('data-tpages'),
                    list_items_color = _this.attr('list-item-color'),
                    this_html = _this.html(),
                    appender_con = jQuery('#main-list-<?php echo($rand_num) ?> li:last'),
                    ajax_url = '<?php echo admin_url('admin-ajax.php') ?>';
                if (!_this.hasClass('ajax-loadin')) {
                    _this.addClass('ajax-loadin');
                    _this.html(this_html + ' <i class="fa fa-refresh fa-spin"></i>');
                    page_num = parseInt(page_num);

                    var request = jQuery.ajax({
                        url: ajax_url,
                        method: "POST",
                        data: {
                            page_num: page_num,
                            list_items_color: list_items_color,
                            jobs_by: '<?php echo($jobs_by) ?>',
                            jobs_numbers: '<?php echo($jobs_numbers) ?>',
                            action: 'jobsearch_load_more_list'
                        },
                        dataType: "json"
                    });
                    request.done(function (response) {

                        if ('undefined' !== typeof response.html) {
                            page_num += <?php echo $jobs_numbers ?>;
                            _this.attr('data-tpages', page_num);
                            if (page_num >= total_jobs_list) {
                                _this.parent('li').hide();
                            }
                            appender_con.before(response.html);
                        }
                        _this.html(this_html);
                        _this.removeClass('ajax-loadin');
                    });

                    request.fail(function (jqXHR, textStatus) {
                        _this.html(this_html);
                        _this.removeClass('ajax-loadin');
                    });
                }
                return false;
            })

        </script>
    <?php } else { ?>
        <script type="text/javascript">
            jQuery(document).on('click', '.load-more-companies-<?php echo($rand_num) ?>', function (e) {
                e.preventDefault();
                var _this = jQuery(this),
                    total_pages = _this.attr('data-tpages'),
                    jobs_results = _this.attr('jobs-results'),
                    page_num = _this.attr('data-gtopage'),
                    list_items_color = _this.attr('list-item-color'),
                    this_html = _this.html(),
                    appender_con = jQuery('#main-list-<?php echo($rand_num) ?> li:last'),
                    ajax_url = '<?php echo admin_url('admin-ajax.php') ?>';

                if (!_this.hasClass('ajax-loadin')) {
                    _this.addClass('ajax-loadin');
                    _this.html(this_html + ' <i class="fa fa-refresh fa-spin"></i>');
                    total_pages = parseInt(total_pages);
                    page_num = parseInt(page_num);
                    jobs_results = parseInt(jobs_results);
                    var request = jQuery.ajax({
                        url: ajax_url,
                        method: "POST",
                        data: {
                            page_num: page_num,
                            employer_cat: '<?php echo($employer_cat) ?>',
                            employer_order: '<?php echo($job_order) ?>',
                            jobs_numbers: '<?php echo($jobs_numbers) ?>',
                            list_items_color: list_items_color,
                            action: 'jobsearch_load_more_top_companies_list'
                        },
                        dataType: "json"
                    });
                    request.done(function (response) {
                        if ('undefined' !== typeof response.html) {
                            page_num += <?php echo $jobs_numbers ?>;
                            jobs_results += <?php echo $jobs_numbers ?>;
                            _this.attr('data-gtopage', page_num);
                            _this.attr('jobs-results', jobs_results);
                            if (jobs_results >= total_pages) {
                                _this.parent('li').hide();
                            }
                            appender_con.before(response.html);
                        }
                        _this.html(this_html);
                        _this.removeClass('ajax-loadin');
                    });
                    request.fail(function (jqXHR, textStatus) {
                        _this.html(this_html);
                        _this.removeClass('ajax-loadin');
                    });
                }
                return false;

            });
        </script>
    <?php }
        $html = ob_get_clean();
        return $html;
    }

    public static function load_more_top_companies($employers_posts, $list_items_color, $list_view = '')
    {
        $list_view_icon = $list_view == 'style2' ? '<i class="careerfy-icon careerfy-next-long"></i>' : '';

        $list_items_color = $list_items_color != "" ? 'style="color: ' . $list_view . ' "' : "";
        foreach ($employers_posts as $data) { ?>
            <li><a <?php echo $list_items_color ?>
                        href="<?php echo get_permalink($data->ID) ?>"><?php echo($list_view_icon) ?>&nbsp;<?php echo $data->post_title ?></a></li>
        <?php }
    }

    public function load_more_top_comapnies_list()
    {
        $page_num = isset($_POST['page_num']) ? $_POST['page_num'] : '';
        $employer_cat = isset($_POST['employer_cat']) ? $_POST['employer_cat'] : '';
        $job_order = isset($_POST['job_order']) ? $_POST['job_order'] : '';
        $jobs_numbers = isset($_POST['jobs_numbers']) ? $_POST['jobs_numbers'] : '';
        $list_items_color = isset($_POST['list_items_color']) ? $_POST['list_items_color'] : '';
        $list_view = isset($_POST['list_view']) ? $_POST['list_view'] : '';

        $element_filter_arr = array();
        $element_filter_arr[] = array(
            'key' => 'jobsearch_field_employer_approved',
            'value' => 'on',
            'compare' => '=',
        );

        $args = array(
            'posts_per_page' => $jobs_numbers,
            'post_type' => 'employer',
            'post_status' => 'publish',
            'order' => $job_order,
            //'paged' => $page_num,
            'offset' => $page_num,
            'orderby' => 'meta_value_num',
            'meta_key' => 'jobsearch_field_employer_job_count',
            'meta_query' => array(
                $element_filter_arr,
            ),
        );
        if ($employer_cat != '') {
            $args['tax_query'][] = array(
                'taxonomy' => 'sector',
                'field' => 'slug',
                'terms' => $employer_cat
            );
        }

        $employers_query = new WP_Query($args);
        $employers_posts = $employers_query->posts;

        ob_start();
        self::load_more_top_companies($employers_posts, $list_items_color,$list_view);
        $html = ob_get_clean();
        echo json_encode(array('html' => $html));
        wp_die();
    }

    public static function load_more_explore_jobs($jobs_by_detail, $list_items_color, $list_view = '')
    {
        global $jobsearch_plugin_options, $result_page, $jobs_by;
        $job_slug;
        if ($jobs_by == 'jobtype') {
            $job_slug = 'job_type';
        } else if ($jobs_by == 'skill') {
            $job_slug = 'skill';
        } else if ($jobs_by == 'sector') {
            $job_slug = 'sector_cat';
        }
        $to_result_page = $result_page;
        $joptions_search_page = isset($jobsearch_plugin_options['jobsearch_search_list_page']) ? $jobsearch_plugin_options['jobsearch_search_list_page'] : '';
        if ($joptions_search_page != '') {
            $joptions_search_page = careerfy__get_post_id($joptions_search_page, 'page');
        }
        if ($result_page <= 0 && $joptions_search_page > 0) {
            $to_result_page = $joptions_search_page;
        }
        $list_view_icon = $list_view == 'style2' ? '<i class="careerfy-icon careerfy-next-long"></i>' : '';
        $to_result_page = absint($to_result_page);
        foreach ($jobs_by_detail as $data) {
            $cat_goto_link = add_query_arg(array($job_slug => $data->slug), get_permalink($to_result_page));
            $cat_goto_link = apply_filters('jobsearch_job_sector_cat_result_link', $cat_goto_link, $data->slug);
            ?>
            <li><a style="color: <?php echo $list_items_color ?>;"
                   href="<?php echo($cat_goto_link) ?>"><?php echo($list_view_icon) ?>&nbsp;<?php echo $data->name ?></a></li>
        <?php }
    }

    public function load_more_in_list()
    {
        $page_num = isset($_POST['page_num']) ? $_POST['page_num'] : '';
        $jobs_numbers = isset($_POST['jobs_numbers']) ? $_POST['jobs_numbers'] : '';
        $jobs_by = isset($_POST['jobs_by']) ? $_POST['jobs_by'] : '';
        $list_items_color = isset($_POST['list_items_color']) ? $_POST['list_items_color'] : '';
        $jobs_by_detail = get_terms(array(
            'taxonomy' => $jobs_by,
            'hide_empty' => false,
            'number' => $jobs_numbers,
            'offset' => $page_num
        ));

        ob_start();
        self::load_more_explore_jobs($jobs_by_detail, $list_items_color);
        $html = ob_get_clean();
        echo json_encode(array('html' => $html));
        wp_die();
    }


}

return new JobSearch_Careerfy_Explore_jobs_list();