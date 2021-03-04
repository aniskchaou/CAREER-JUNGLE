<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

/**
 * @since 1.1.0
 */
class Blog extends Widget_Base
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
        return 'blog';
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
        return __('Blog', 'careerfy-frame');
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
        return 'fa fa-blog';
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
        $categories = get_categories(array(
            'orderby' => 'name',
        ));

        $cate_array = array(esc_html__("Select Category", "careerfy-frame") => '');
        if (is_array($categories) && sizeof($categories) > 0) {
            foreach ($categories as $category) {
                $cate_array[$category->cat_name] = $category->slug;
            }
        }

        $this->start_controls_section(
            'blog',
            [
                'label' => __('Blog Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'blog_view',
            [
                'label' => __('Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'view1',
                'options' => [
                    'view1' => __('Style 1', 'careerfy-frame'),
                    'view2' => __('Style 2', 'careerfy-frame'),
                    'view3' => __('Style 3', 'careerfy-frame'),
                    'view4' => __('Style 4', 'careerfy-frame'),
                    'view5' => __('Style 5', 'careerfy-frame'),
                    'view7' => __('Style 6', 'careerfy-frame'),
                    'view8' => __('Style 7', 'careerfy-frame'),
                    'view9' => __('Style 8', 'careerfy-frame'),
                    'view10' => __('Style 9', 'careerfy-frame'),
                    'view11' => __('Style 10', 'careerfy-frame'),
                    'view12' => __('Style 11', 'careerfy-frame'),
                    'view13' => __('Style 12', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'blog_cat',
            [
                'label' => __('Category', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => '0',
                'options' => $cate_array,
            ]
        );

        $this->add_control(
            'blog_excerpt',
            [
                'label' => __('Excerpt Length', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default'=> '20',
                'description' => __("Set the number of words you want to show for post excerpt.", "careerfy-frame")
            ]
        );

        $this->add_control(
            'blog_order',
            [
                'label' => __('Orderby', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'DESC',
                'description' => __("Choose blog list items orderby.", "careerfy-frame"),
                'options' => [
                    'DESC' => __('Descending', 'careerfy-frame'),
                    'ASC' => __('Ascending', 'careerfy-frame'),

                ],
            ]
        );

        $this->add_control(
            'blog_orderby',
            [
                'label' => __('Orderby', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'date',
                'description' => __("Choose blog list items orderby.", "careerfy-frame"),
                'options' => [
                    'date' => __('Date', 'careerfy-frame'),
                    'title' => __('Title', 'careerfy-frame'),

                ],
            ]
        );

        $this->add_control(
            'blog_pagination',
            [
                'label' => __('Pagination', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Choose Yes if you want to show pagination for post items.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
                'condition' => [
                    'blog_view' => array('view1', 'view2', 'view3', 'view4', 'view5', 'view9', 'view10', 'view11', 'view12', 'view13')
                ]
            ]
        );

        $this->add_control(
            'blog_per_page',
            [
                'label' => __('Posts per Page', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '9',
                'description' => __("Set number that how much posts you want to show per page. Leave it blank for all posts on a single page.", "careerfy-frame")
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        global $blog_per_page;
        $atts = $this->get_settings_for_display();
        $blog_cat = $atts['blog_cat'];
        $blog_view = $atts['blog_view'];
        $blog_excerpt = $atts['blog_excerpt'];
        $blog_order = $atts['blog_order'];
        $blog_orderby = $atts['blog_orderby'];
        $blog_pagination = $atts['blog_pagination'];
        $blog_per_page = $atts['blog_per_page'];
        $rand_id = rand(100000, 999999);
        $html = '';
        $blog_per_page = $blog_per_page == '' ? -1 : absint($blog_per_page);

        $blog_paged = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;

        if ($blog_view == 'view5') {
            $blog_paged = 1;
        }
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $blog_per_page,
            'paged' => $blog_paged,
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'order' => $blog_order,
            'orderby' => $blog_orderby,
        );

        if ($blog_cat && $blog_cat != '' && $blog_cat != '0') {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'category',
                    'field' => 'slug',
                    'terms' => $blog_cat,
                ),
            );
        }

        $blog_query = new \WP_Query($args);
        
        $total_posts = $blog_query->found_posts;
        ob_start();
        $row_class = "";
        if ($blog_query->have_posts()) {
            global $post;

            $blog_class = 'careerfy-blog careerfy-blog-grid';
            if ($blog_view == 'view2') {
                $blog_class = 'careerfy-blog careerfy-news-grid';
            }
            if ($blog_view == 'view3') {
                $blog_class = 'careerfy-blog careerfy-blog-medium';
            }
            if ($blog_view == 'view4') {
                $blog_class = 'careerfy-blog careerfy-blog-view6';
            }
            if ($blog_view == 'view5') {
                $blog_class = 'careerfy-blog careerfy-blog-masonry blog-masonry-' . $rand_id;
                $row_class = 'isomas-grid-' . $rand_id;
            }
            if ($blog_view == 'view7') {
                $blog_class = 'careerfy-blog careerfy-blog-grid-style10';
            }
            if ($blog_view == 'view8') {
                $blog_class = 'careerfy-blog careerfy-blog-grid-style11';
            }
            if ($blog_view == 'view9') {
                $blog_class = 'careerfy-blog careerfy-twelve-blog-grid';
            }
            if ($blog_view == 'view10') {
                $blog_class = 'careerfy-blog careerfy-blog-style14-grid';

            }
            if ($blog_view == 'view11') {
                $blog_class = 'careerfy-blog careerfy-fifteen-blog-medium';

            }
            if ($blog_view == 'view12') {
                $blog_class = 'careerfy-blog careerfy-sixteen-blog-medium';

            }
            if ($blog_view == 'view13') {
                $blog_class = 'careerfy-eighteen-blog-grid';

            }

            echo '<div class="' . $blog_class . '">';
            echo '<ul class="row  ' . $row_class . '">';
            global $counter;
            $counter = 1;
            while ($blog_query->have_posts()) : $blog_query->the_post();
                if ($blog_view == 'view13') {
                    do_action('careerfy_news_blog_view13', $post->ID, $blog_excerpt);
                } else if ($blog_view == 'view12') {
                    do_action('careerfy_news_blog_view12', $post->ID, $blog_excerpt);
                } else if ($blog_view == 'view11') {
                    do_action('careerfy_news_blog_view11', $post->ID, $blog_excerpt);
                } else if ($blog_view == 'view10') {
                    do_action('careerfy_news_blog_view10', $post->ID, $blog_excerpt);
                } else if ($blog_view == 'view9') {
                    do_action('careerfy_news_blog_view9', $post->ID, $blog_excerpt);
                } else if ($blog_view == 'view8') {
                    do_action('careerfy_news_blog_view8', $post->ID, $blog_excerpt);
                } else if ($blog_view == 'view7') {
                    do_action('careerfy_news_blog_view7', $post->ID, $blog_excerpt);
                } else if ($blog_view == 'view5') {
                    do_action('careerfy_news_blog_view5', $post->ID, $blog_excerpt);
                } else if ($blog_view == 'view4') {
                    do_action('careerfy_news_blog_view6', $post->ID, $blog_excerpt);
                } elseif ($blog_view == 'view3') {
                    do_action('careerfy_news_medium', $post->ID, $blog_excerpt);
                } elseif ($blog_view == 'view2') {
                    do_action('careerfy_news_grid', $post->ID, $blog_excerpt);
                } else {
                    do_action('careerfy_blog_grid', $post->ID, $blog_excerpt);
                }
                $counter++;

            endwhile;

            echo '</ul>';
            echo '</div>';
            wp_reset_postdata();
            // pagination
            if ($blog_pagination == 'yes' && $total_posts > $blog_per_page && $blog_view == 'view5' || $blog_view == 'view12') {
                $total_pages = ceil($total_posts / $blog_per_page);


                echo '<div class="careerfy-blog-masonry-btn-wrap"><a href="javascript:void(0);" class="careerfy-blog-masonry-btn lodmore-jlists-' . ($rand_id) . '" data-tpages="' . ($total_pages) . '" data-gtopage="2">' . esc_html__('Load More Blog Posts', "careerfy-frame") . '</a></div>';
                ob_start();
                ?>
                <script>
                    var careerfyMasGrid = jQuery('.isomas-grid-<?php echo($rand_id) ?>');
                    jQuery(document).ready(function () {
                        careerfyMasGrid.isotope({
                            itemSelector: '.masgrid-item',
                        });
                        var strtIsoTime<?php echo($rand_id) ?> = setInterval(function () {
                            careerfyMasGrid.isotope({
                                itemSelector: '.masgrid-item',
                            });
                        }, 1500);
                    });
                    jQuery(document).on('click', '.lodmore-jlists-<?php echo($rand_id) ?>', function (e) {
                        e.preventDefault();
                        var _this = jQuery(this),
                            total_pages = _this.attr('data-tpages'),
                            page_num = _this.attr('data-gtopage'),
                            this_html = _this.html(),
                            appender_con = jQuery('.blog-masonry-<?php echo($rand_id) ?> > ul'),
                            ajax_url = '<?php echo admin_url('admin-ajax.php') ?>';
                        if (!_this.hasClass('ajax-loadin')) {
                            _this.addClass('ajax-loadin');
                            _this.html(this_html + '<i class="fa fa-refresh fa-spin"></i>');

                            total_pages = parseInt(total_pages);
                            page_num = parseInt(page_num);
                            var request = jQuery.ajax({
                                url: ajax_url,
                                method: "POST",
                                data: {
                                    page_num: page_num,
                                    blog_cat: '<?php echo($blog_cat) ?>',
                                    blog_excerpt: '<?php echo($blog_excerpt) ?>',
                                    blog_order: '<?php echo($blog_order) ?>',
                                    blog_orderby: '<?php echo($blog_orderby) ?>',
                                    blog_per_page: '<?php echo($blog_per_page) ?>',
                                    action: 'jobsearch_load_morein_blogs_view5_con',
                                },
                                dataType: "json"
                            });

                            request.done(function (response) {
                                if ('undefined' !== typeof response.html) {
                                    page_num += 1;
                                    _this.attr('data-gtopage', page_num);
                                    if (page_num > total_pages) {
                                        _this.parent('div').hide();
                                    }
                                    var appendinItems = $(response.html);
                                    careerfyMasGrid.append(appendinItems);
                                    careerfyMasGrid.isotope('appended', appendinItems);
                                    var ajxIsoTime<?php echo($rand_id) ?> = setInterval(function () {
                                        careerfyMasGrid.isotope({
                                            itemSelector: '.masgrid-item',
                                        });
                                    }, 1500);
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
                <?php
                $lodmore_script = ob_get_clean();
                echo($lodmore_script);
            }
            if ($blog_pagination == 'yes' && $total_posts > $blog_per_page && $blog_view != 'view5' && $blog_view != 'view7' && $blog_view != 'view8' && $blog_view != 'view9') {
                careerfy_pagination($blog_query);
            }
        } else {
            esc_html_e("No post found.", "careerfy-frame");
        }
        $html .= ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    {

    }
}