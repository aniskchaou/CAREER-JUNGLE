<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

/**
 * @since 1.1.0
 */
class JobsByCategories extends Widget_Base
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
        return 'jobs-by-categories';
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
        return __('Jobs By Categories', 'careerfy-frame');
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
        return 'fa fa-briefcase';
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
        $all_page = array();

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

        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Jobs By Categories Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'num_cats',
            [
                'label' => __('Number of Sectors', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'description' => __('Set the number of Sectors to show', 'careerfy-frame'),
                'default' => '-1',
            ]
        );

        $this->add_control(
            'result_page',
            [
                'label' => __('Result Page', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'on',
                'options' => $all_page,
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label' => __('Order', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'jobs_count',
                'options' => [
                    'jobs_count' => __('By Jobs Count', 'careerfy-frame'),
                    'id' => __('By Random', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'sector_job_counts',
            [
                'label' => __('Show Jobs Counts', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'cat_title',
            [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'cat_link_text',
            [
                'label' => __('Link text', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'cat_link_text_url',
            [
                'label' => __('Link text URL', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        $rand_num = rand(10000000, 99909999);
        extract(shortcode_atts(array(
            'cats_view' => '',
            'num_cats' => '-1',
            'cat_title' => '',
            'result_page' => '',
            'sector_job_counts' => '',
            'cat_link_text' => '',
            'cat_link_text_url' => '',
            'order_by' => 'jobs_count',
        ), $atts));
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

                                ob_start(); ?>
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
                                sector_job_counts: '<?php echo ($sector_job_counts) ?>',
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
        echo $html;
    }

    protected function _content_template()
    {

    }
}