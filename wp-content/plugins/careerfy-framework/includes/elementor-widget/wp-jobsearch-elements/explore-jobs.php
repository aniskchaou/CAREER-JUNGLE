<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

/**
 * @since 1.1.0
 */
class ExploreJobs extends Widget_Base
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
        return 'explore-jobs';
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
        return __('Explore Jobs', 'careerfy-frame');
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
        $all_page = array(esc_html__("Select Page", "careerfy-frame") => '');
        $args = array(
            'sort_order' => 'asc',
            'sort_column' => 'post_title',
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'meta_key' => '',
            'meta_value' => '',
            'authors' => '',
            'child_of' => 0,
            'parent' => -1,
            'exclude_tree' => '',
            'number' => '',
            'offset' => 0,
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $pages = get_pages($args);
        if (!empty($pages)) {
            foreach ($pages as $page) {
                $all_page[$page->ID] = $page->post_title;
            }
        }

        $categories = get_terms(array(
            'taxonomy' => 'sector',
            'hide_empty' => false,
        ));

        $cate_array = array(esc_html__("Select Sector", "careerfy-frame") => '');
        if (is_array($categories) && sizeof($categories) > 0) {
            foreach ($categories as $category) {
                $cate_array[$category->name] = $category->slug;
            }
        }

        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Explore Jobs Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'list_view',
            [
                'label' => __('Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'style1',
                'options' => [
                    'style1' => __('Style 1', 'careerfy-frame'),
                    'style2' => __('Style 2', 'careerfy-frame'),
                    'style3' => __('Style 3', 'careerfy-frame'),
                    'style4' => __('Style 4', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Title Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
            ]
        );
        $this->add_control(
            'list_items_color',
            [
                'label' => __('List Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
            ]
        );
        $this->add_control(
            'btn_color',
            [
                'label' => __('Button Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
            ]
        );
        $this->add_control(
            'btn_text_color',
            [
                'label' => __('Button Text Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
            ]
        );
        $this->add_control(
            'button_text',
            [
                'label' => __('Button Text', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'button_url',
            [
                'label' => __('Button URL', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'load_more',
            [
                'label' => __('Load More', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'load_more_text',
            [
                'label' => __('Load More Text', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,

            ]
        );
        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'jobs_by',
            [
                'label' => __('Jobs by', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'jobtype',
                'options' => [
                    'jobtype' => __('Job Type', 'careerfy-frame'),
                    'skill' => __('Skills', 'careerfy-frame'),
                    'sector' => __('Category', 'careerfy-frame'),
                    'employer' => __('Top Companies', 'careerfy-frame'),
                ],
            ]
        );
        $repeater->add_control(
            'result_page',
            [
                'label' => __('Result Page', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => '',
                'options' => $all_page,
            ]
        );
        $repeater->add_control(
            'employer_cat',
            [
                'label' => __('Jobs by', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => '',
                'description' => __("Select Sector.", "careerfy-frame"),
                'options' => $cate_array,
                'condition' => [
                    'jobs_by' => 'employer'
                ]
            ]
        );
        $repeater->add_control(
            'explore_job_title', [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );
        $repeater->add_control(
            'jobs_numbers', [
                'label' => __('Number of jobs', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '10',
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'job_order',
            [
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
            'explore_job_item',
            [
                'label' => __('Repeater List', 'eyecix-elementor'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'explore_job_title' => __('Explore Job', 'eyecix-elementor'),
                    ]
                ],
                'title_field' => '{{{ explore_job_title }}}',
            ]
        );
        $this->end_controls_section();
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
                   href="<?php echo($cat_goto_link) ?>"><?php echo($list_view_icon) ?><?php echo $data->name ?></a></li>
        <?php }
    }

    public static function load_more_top_companies($employers_posts, $list_items_color, $list_view = '')
    {
        $list_view_icon = $list_view == 'style2' ? '<i class="careerfy-icon careerfy-next-long"></i>' : '';

        $list_items_color = $list_items_color != "" ? 'style="color: ' . $list_view . ' "' : "";
        foreach ($employers_posts as $data) { ?>
            <li><a <?php echo $list_items_color ?>
                        href="<?php echo get_permalink($data->ID) ?>"><?php echo($list_view_icon) ?>
                    &nbsp;<?php echo $data->post_title ?></a></li>
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

        $employers_query = new \WP_Query($args);
        $employers_posts = $employers_query->posts;

        ob_start();
        self::load_more_top_companies($employers_posts, $list_items_color, $list_view);
        $html = ob_get_clean();
        echo json_encode(array('html' => $html));
        wp_die();
    }


    private function careerfy_explore_jobs_item()
    {
        global $title_color, $list_items_color, $load_more, $list_view, $load_more_text, $result_page, $jobs_by;
        $atts = $this->get_settings_for_display();
        foreach ($atts['explore_job_item'] as $explore_jobs_atts) {

            $title_color = $explore_jobs_atts['jobs_by'];
            $jobs_numbers = $explore_jobs_atts['jobs_numbers'];
            $explore_job_title = $explore_jobs_atts['explore_job_title'];
            $job_order = $explore_jobs_atts['job_order'];
            $employer_cat = $explore_jobs_atts['employer_cat'];
            $result_page = $explore_jobs_atts['result_page'];
            $jobs_by = $explore_jobs_atts['jobs_by'];
            $employers_posts = '';
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

                $employers_query = new \WP_Query($args);
                $totl_found_jobs = $employers_query->found_posts;
                $employers_posts = $employers_query->posts;
            }

            $title_color = $title_color != "" ? 'style="color: ' . $title_color . ' "' : "";
            $load_more_text = $load_more_text != "" ? $load_more_text : 'More Jobs';
            $rand_num = rand(10000000, 99909999);

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
                <script>
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
                <script>
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
                                    list_view: '<?php echo($list_view) ?>',
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
        }


    }

    protected function render()
    {
        global $title_color, $list_items_color, $load_more, $list_view, $load_more_text;
        $atts = $this->get_settings_for_display();
        $title_color = $atts['title_color'];
        $list_view = $atts['list_view'];
        $list_items_color = $atts['list_items_color'];
        $btn_color = $atts['btn_color'];
        $button_text = $atts['button_text'];
        $button_url = $atts['button_url'];
        $btn_text_color = $atts['btn_text_color'];
        $load_more = $atts['load_more'];
        $load_more_text = $atts['load_more_text'];

        $html = '<div class="row">' . $this->careerfy_explore_jobs_item() . ' ';

        if ($list_view == 'style2') {
            if ($button_text != "") {
                $html .= '<div class="col-md-12" >
               <div class="careerfy-thirteen-browse-alljobs-btn" > <a href = "' . $button_url . '" > ' . $button_text . '</a > </div >
         </div>';
            }

        } else {

            if ($button_text != "") {
                $html .= '<div class="col-md-12" >
                    <div class="careerfy-fifteen-browse-btn"> <a href = "' . $button_url . '" > ' . $button_text . '</a > </div >
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

        echo $html;
    }


    protected function _content_template()
    {

    }
}