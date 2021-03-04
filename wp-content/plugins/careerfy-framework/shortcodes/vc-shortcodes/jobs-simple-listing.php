<?php
/**
 * Simple Jobs Listing Shortcode
 * @return html
 */
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class JobSearch_Careerfy_Simple_Jobs_Listins
{

    public function __construct()
    {
        add_shortcode('jobsearch_simple_jobs_listing', array($this, 'simple_jobs_listing_shortcode'));

        add_action('wp_ajax_jobsearch_load_more_insimple_jobslistin_con', array($this, 'load_more_jobs_in_list'));
        add_action('wp_ajax_nopriv_jobsearch_load_more_insimple_jobslistin_con', array($this, 'load_more_jobs_in_list'));
    }

    public function simple_jobs_listing_shortcode($atts)
    {
        global $jobsearch_plugin_options, $sitepress, $rand_num, $jobsearch_shortcode_jobs_frontend;
        extract(shortcode_atts(array(
            'job_list_style' => 'style1',
            'job_list_title' => '',
            'job_list_description' => '',
            'job_cat' => '',
            'featured_only' => 'yes',
            'job_order' => 'DESC',
            'job_orderby' => 'date',
            'job_per_page' => '10',
            'job_load_more' => 'yes',
            'job_link_text' => '',
            'job_link_text_url' => '',
            'title_img' => '',
            'job_list_loc_listing' => 'country,city',
        ), $atts));

        if (class_exists('JobSearch_plugin')) {
            $jobsearch_jobs_listin_sh = $jobsearch_shortcode_jobs_frontend;

            $rand_num = rand(10000000, 99909999);
            $jobsearch__options = get_option('jobsearch_plugin_options');

            $emporler_approval = isset($jobsearch__options['job_listwith_emp_aprov']) ? $jobsearch__options['job_listwith_emp_aprov'] : '';

            $job_per_page = isset($job_per_page) && !empty($job_per_page) && $job_per_page > 0 ? $job_per_page : 10;

            $locations_view_type = isset($atts['job_list_loc_listing']) ? $atts['job_list_loc_listing'] : '';

            $loc_types_arr = $locations_view_type != '' ? explode(',', $locations_view_type) : '';
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
            $post_ids = array();
            $all_post_ids = array();
            if (is_object($jobsearch_jobs_listin_sh)) {
                $all_post_ids = $jobsearch_jobs_listin_sh->job_general_query_filter($post_ids, $atts);
            }
            //

            $element_filter_arr = array();
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

            if (!empty($all_post_ids)) {
                $args['post__in'] = $all_post_ids;
            }

            $jobs_query = new WP_Query($args);

            $totl_found_jobs = $jobs_query->found_posts;
            $jobs_posts = $jobs_query->posts;

            ob_start();
            if ($job_list_style == "style5" || $job_list_style == "style6" || $job_list_style == "style4" || $job_list_style == "style9") {
                ?>
                <script>
                    jQuery(document).on('click', '.lodmore-jlists-<?php echo($rand_num) ?>', function (e) {
                        e.preventDefault();
                        var _this = jQuery(this),
                            total_pages = _this.attr('data-tpages'),
                            page_num = _this.attr('data-gtopage'),
                            this_html = _this.html(),
                            appender_con = jQuery('#main-jlists-<?php echo($rand_num) ?> li:last'),
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
                                    job_cat: '<?php echo($job_cat) ?>',
                                    featured_only: '<?php echo($featured_only) ?>',
                                    job_order: '<?php echo($job_order) ?>',
                                    job_orderby: '<?php echo($job_orderby) ?>',
                                    job_per_page: '<?php echo($job_per_page) ?>',
                                    loc_view_country: '<?php echo($loc_view_country) ?>',
                                    loc_view_state: '<?php echo($loc_view_state) ?>',
                                    loc_view_city: '<?php echo($loc_view_city) ?>',
                                    job_list_style: '<?php echo($job_list_style) ?>',
                                    action: 'jobsearch_load_more_insimple_jobslistin_con',
                                },
                                dataType: "json"
                            });

                            request.done(function (response) {
                                if ('undefined' !== typeof response.html) {
                                    page_num += 1;
                                    console.info(response.html);
                                    _this.attr('data-gtopage', page_num)
                                    if (page_num > total_pages) {
                                        _this.parent('div').hide();
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
                <?php
            }
            if ($job_list_style == "style5") {
                ?>
                <div id="main-jlists-<?php echo($rand_num) ?>">
                    <?php if (!empty($jobs_posts)) { ?>
                        <div class="careerfy-jobslatest-list">
                            <ul class="row">
                                <?php self::load_more_jobs_posts($jobs_posts, $job_list_style, $job_per_page, $loc_view_country, $loc_view_state, $loc_view_city); ?>
                            </ul>
                        </div>
                        <?php
                    } else {
                        echo '<p>' . esc_html__('No job found.', 'careerfy-frame') . '</p>';
                    }
                    if ($job_load_more == 'yes' && $totl_found_jobs > $job_per_page) {
                        $total_pages = ceil($totl_found_jobs / $job_per_page); ?>
                        <div class="careerfy-loadmore-listingsbtn"><a href="javascript:void(0);"
                                                                      class="lodmore-jlists-<?php echo($rand_num) ?>"
                                                                      data-tpages="<?php echo($total_pages) ?>"
                                                                      data-gtopage="2"><?php esc_html_e('Load More Listings', 'careerfy-frame') ?></a>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            <?php } else if ($job_list_style == "style1") {
                if ($job_list_title != '') { ?>
                    <h2 class="careerfy-featured-title"><?php echo($job_list_title) ?></h2>
                <?php } ?>
                <div id="main-jlists-<?php echo($rand_num) ?>" class="careerfy-featuredjobs-list">
                    <?php
                    if (!empty($jobs_posts)) {
                        ?>
                        <ul class="row">
                            <?php self::load_more_jobs_posts($jobs_posts, $job_list_style, $job_per_page, $loc_view_country, $loc_view_state, $loc_view_city); ?>
                        </ul>
                        <?php
                    } else {
                        echo '<p>' . esc_html__('No job found.', 'careerfy-frame') . '</p>';
                    }
                    ?>
                    <script>
                        jQuery(document).on('click', '.lodmore-jlists-<?php echo($rand_num) ?>', function (e) {
                            e.preventDefault();
                            var _this = jQuery(this),
                                total_pages = _this.attr('data-tpages'),
                                page_num = _this.attr('data-gtopage'),
                                this_html = _this.html(),
                                appender_con = jQuery('#main-jlists-<?php echo($rand_num) ?> > ul'),
                                ajax_url = '<?php echo admin_url('admin-ajax.php') ?>';
                            if (!_this.hasClass('ajax-loadin')) {
                                _this.addClass('ajax-loadin');
                                _this.html(this_html + ' <i class="fa fa-refresh fa-spin"></i>');

                                total_pages = parseInt(total_pages);
                                page_num = parseInt(page_num);
                                var request = jQuery.ajax({
                                    url: ajax_url,
                                    method: "POST",
                                    data: {
                                        page_num: page_num,
                                        job_cat: '<?php echo($job_cat) ?>',
                                        featured_only: '<?php echo($featured_only) ?>',
                                        job_order: '<?php echo($job_order) ?>',
                                        job_orderby: '<?php echo($job_orderby) ?>',
                                        job_per_page: '<?php echo($job_per_page) ?>',
                                        loc_view_country: '<?php echo($loc_view_country) ?>',
                                        loc_view_state: '<?php echo($loc_view_state) ?>',
                                        loc_view_city: '<?php echo($loc_view_city) ?>',
                                        job_list_style: '<?php echo($job_list_style) ?>',
                                        action: 'jobsearch_load_more_insimple_jobslistin_con',
                                    },
                                    dataType: "json"
                                });

                                request.done(function (response) {
                                    if ('undefined' !== typeof response.html) {
                                        page_num += 1;

                                        _this.attr('data-gtopage', page_num)
                                        if (page_num > total_pages) {
                                            _this.parent('div').hide();
                                        }
                                        appender_con.append(response.html);
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
                </div>
                <?php
                if ($job_load_more == 'yes' && $totl_found_jobs > $job_per_page) {
                    $total_pages = ceil($totl_found_jobs / $job_per_page); ?>
                    <div class="careerfy-loadmore-listingsbtn"><a href="javascript:void(0);"
                                                                  class="lodmore-jlists-<?php echo($rand_num) ?>"
                                                                  data-tpages="<?php echo($total_pages) ?>"
                                                                  data-gtopage="2"><?php esc_html_e('Load More Listings', 'careerfy-frame') ?></a>
                    </div>
                <?php }
            } else if ($job_list_style == 'style2') { ?>
                <!-- Main Section -->
                <div class="careerfy-main-section careerfy-recent-list-full">
                    <!-- Fancy Title Ten -->
                    <?php if ($job_list_title != '') { ?>
                        <div class="careerfy-fancy-title-ten careerfy-fancy-title-ten-left">
                            <h2><?php echo($job_list_title) ?></h2>
                            <span><?php echo $job_list_description ?></span>
                        </div>
                    <?php } ?>
                    <!-- Recent Listing -->
                    <div id="main-jlists-<?php echo($rand_num) ?>" class="careerfy-recent-list">
                        <?php
                        if (!empty($jobs_posts)) {
                            ?>
                            <ul class="row">
                                <?php self::load_more_jobs_posts($jobs_posts, $job_list_style, $job_per_page, $loc_view_country, $loc_view_state, $loc_view_city); ?>
                            </ul>
                            <?php
                        } else {
                            echo '<p>' . esc_html__('No job found.', 'careerfy-frame') . '</p>';
                        }
                        ?>
                        <script>
                            jQuery(document).on('click', '.lodmore-jlists-<?php echo($rand_num) ?>', function (e) {
                                e.preventDefault();
                                var _this = jQuery(this),
                                    total_pages = _this.attr('data-tpages'),
                                    page_num = _this.attr('data-gtopage'),
                                    this_html = _this.html(),
                                    appender_con = jQuery('#main-jlists-<?php echo($rand_num) ?> > ul'),
                                    ajax_url = '<?php echo admin_url('admin-ajax.php') ?>';
                                if (!_this.hasClass('ajax-loadin')) {
                                    _this.addClass('ajax-loadin');
                                    _this.html(this_html + ' <i class="fa fa-refresh fa-spin"></i>');

                                    total_pages = parseInt(total_pages);
                                    page_num = parseInt(page_num);
                                    var request = jQuery.ajax({
                                        url: ajax_url,
                                        method: "POST",
                                        data: {
                                            page_num: page_num,
                                            job_cat: '<?php echo($job_cat) ?>',
                                            featured_only: '<?php echo($featured_only) ?>',
                                            job_order: '<?php echo($job_order) ?>',
                                            job_orderby: '<?php echo($job_orderby) ?>',
                                            job_per_page: '<?php echo($job_per_page) ?>',
                                            job_list_style: '<?php echo($job_list_style) ?>',
                                            loc_view_country: '<?php echo($loc_view_country) ?>',
                                            loc_view_state: '<?php echo($loc_view_state) ?>',
                                            loc_view_city: '<?php echo($loc_view_city) ?>',
                                            action: 'jobsearch_load_more_insimple_jobslistin_con',
                                        },
                                        dataType: "json"
                                    });

                                    request.done(function (response) {
                                        if ('undefined' !== typeof response.html) {
                                            page_num += 1;
                                            _this.attr('data-gtopage', page_num)
                                            if (page_num > total_pages) {
                                                _this.parent('div').hide();
                                            }
                                            appender_con.append(response.html);
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
                    </div>
                    <!-- Recent Listing -->
                    <?php
                    if ($job_load_more == 'yes' && $totl_found_jobs > $job_per_page) {
                        $total_pages = ceil($totl_found_jobs / $job_per_page); ?>
                        <div class="show-morejobs-btn"><a href="javascript:void(0);"
                                                          class="lodmore-jlists-<?php echo($rand_num) ?>"
                                                          data-tpages="<?php echo($total_pages) ?>"
                                                          data-gtopage="2"><?php esc_html_e('Show More Jobs', 'careerfy-frame') ?></a>
                        </div>
                    <?php } ?>
                </div>
                <!-- Main Section -->
            <?php } else if ($job_list_style == 'style3') {
                $view_all_btn_class = isset($job_per_page) && $job_per_page <= 4 ? 'no-slider' : '';
                echo '
                <div id="careerfy-slidmaintop-' . ($rand_num) . '" style="position: relative; float: left; width: 100%;">
                <div id="careerfy-slidloder-' . ($rand_num) . '" class="careerfy-slidloder-section"><div class="ball-scale-multiple"><div></div><div></div><div></div></div></div>';
                ?>
                <div class="careerfy-section-premium-wrap">
                    <div class="careerfy-section-title-style">
                        <?php if ($job_list_title != '') { ?>
                            <h2><?php echo $job_list_title ?></h2>
                        <?php } ?>

                        <?php if ($job_link_text != '') { ?>
                            <a href="<?php echo $job_link_text_url ?>"
                               class="careerfy-section-title-btn <?php echo $view_all_btn_class ?>"><?php echo $job_link_text ?></a>
                        <?php } ?>
                    </div>
                    <div class="careerfy-premium-jobs-slider" id="jobs-slider-<?php echo($rand_num) ?>">
                        <div class="careerfy-premium-jobs-slider-layer">
                            <div class="careerfy-premium-jobs">
                                <ul class="row main-layer-jobslists-<?php echo($rand_num) ?>">
                                    <?php self::load_more_jobs_posts($jobs_posts, $job_list_style, $job_per_page, $loc_view_country, $loc_view_state, $loc_view_city); ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Premium Section -->
                <script type="text/javascript">
                    //*** Function Top Employers Slider
                    var $ = jQuery;
                    $(document).ready(function () {
                        jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').css({'height': 'auto'});
                        jQuery('#main-jobslists-<?php echo($rand_num) ?>').css({'display': 'inline-block'});
                        jQuery('#jobs-slider-<?php echo($rand_num) ?>').slick({
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            autoplay: true,
                            autoplaySpeed: 5000,
                            infinite: true,
                            dots: false,
                            prevArrow: "<span class='slick-arrow-left'><i class='careerfy-icon careerfy-next'></i></span>",
                            nextArrow: "<span class='slick-arrow-right'><i class='careerfy-icon careerfy-next'></i></span>",
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
                        var remSlidrLodrInt<?php echo($rand_num) ?> = setInterval(function () {
                            jQuery('#careerfy-slidloder-<?php echo($rand_num) ?>').remove();
                            clearInterval(remSlidrLodrInt<?php echo($rand_num) ?>);
                        }, 1500);

                        var slidrHightInt<?php echo($rand_num) ?> = setInterval(function () {
                            jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').css({'height': 'auto'});
                            jQuery('.main-layer-jobslists-<?php echo($rand_num) ?>').css({'display': 'inline-block'});

                            var slider_act_height_<?php echo($rand_num) ?> = jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').height();

                            var filtr_cname_<?php echo($rand_num) ?> = 'careerfy_topemps_slidr_lheight';
                            var c_date_<?php echo($rand_num) ?> = new Date();
                            c_date_<?php echo($rand_num) ?>.setTime(c_date_<?php echo($rand_num) ?>.getTime() + (60 * 60 * 1000));
                            var c_expires_<?php echo($rand_num) ?> = "; c_expires=" + c_date_<?php echo($rand_num) ?>.toGMTString();
                            document.cookie = filtr_cname_<?php echo($rand_num) ?> + "=" + slider_act_height_<?php echo($rand_num) ?> + c_expires_<?php echo($rand_num) ?> + "; path=/";

                            clearInterval(slidrHightInt<?php echo($rand_num) ?>);
                        }, 1700);
                    })
                    jQuery('.main-layer-jobslists-<?php echo($rand_num) ?>').css({'display': 'none'});

                    var slider_height_<?php echo($rand_num) ?> = '<?php echo(isset($_COOKIE['careerfy_topemps_slidr_lheight']) && $_COOKIE['careerfy_topemps_slidr_lheight'] != '' ? $_COOKIE['careerfy_topemps_slidr_lheight'] . 'px' : '300px') ?>';
                    jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').css({'height': slider_height_<?php echo($rand_num) ?>});
                </script>
                <?php
                echo '</div>';
            } else if ($job_list_style == 'style7') { ?>
                <!-- Recent Listing -->
                <div id="main-jlists-<?php echo($rand_num) ?>" class="careerfy-fifteen-recent-jobs">
                    <?php
                    if (!empty($jobs_posts)) {
                        ?>
                        <ul class="row">
                            <?php self::load_more_jobs_posts($jobs_posts, $job_list_style, $job_per_page, $loc_view_country, $loc_view_state, $loc_view_city); ?>
                        </ul>
                        <?php
                    } else {
                        echo '<p>' . esc_html__('No job found.', 'careerfy-frame') . '</p>';
                    }
                    ?>
                </div>
                <?php
                if ($job_load_more == 'yes' && $totl_found_jobs > $job_per_page) {
                    $total_pages = ceil($totl_found_jobs / $job_per_page); ?>
                    <div class="careerfy-fifteen-browse-btn">
                        <a href="javascript:void(0);" class="lodmore-jlists-<?php echo($rand_num) ?>"
                           data-tpages="<?php echo($total_pages) ?>"
                           data-gtopage="2"><?php esc_html_e('Show More Jobs', 'careerfy-frame') ?></a>
                    </div>
                    <script>
                        jQuery(document).on('click', '.lodmore-jlists-<?php echo($rand_num) ?>', function (e) {
                            e.preventDefault();
                            var _this = jQuery(this),
                                total_pages = _this.attr('data-tpages'),
                                page_num = _this.attr('data-gtopage'),
                                this_html = _this.html(),
                                appender_con = jQuery('#main-jlists-<?php echo($rand_num) ?> > ul'),
                                ajax_url = '<?php echo admin_url('admin-ajax.php') ?>';
                            if (!_this.hasClass('ajax-loadin')) {
                                _this.addClass('ajax-loadin');
                                _this.html(this_html + ' <i class="fa fa-refresh fa-spin"></i>');

                                total_pages = parseInt(total_pages);
                                page_num = parseInt(page_num);
                                var request = jQuery.ajax({
                                    url: ajax_url,
                                    method: "POST",
                                    data: {
                                        page_num: page_num,
                                        job_cat: '<?php echo($job_cat) ?>',
                                        featured_only: '<?php echo($featured_only) ?>',
                                        job_order: '<?php echo($job_order) ?>',
                                        job_orderby: '<?php echo($job_orderby) ?>',
                                        job_per_page: '<?php echo($job_per_page) ?>',
                                        job_list_style: '<?php echo($job_list_style) ?>',
                                        loc_view_country: '<?php echo($loc_view_country) ?>',
                                        loc_view_state: '<?php echo($loc_view_state) ?>',
                                        loc_view_city: '<?php echo($loc_view_city) ?>',
                                        action: 'jobsearch_load_more_insimple_jobslistin_con',
                                    },
                                    dataType: "json"
                                });

                                request.done(function (response) {
                                    if ('undefined' !== typeof response.html) {
                                        page_num += 1;
                                        _this.attr('data-gtopage', page_num)
                                        if (page_num > total_pages) {
                                            _this.parent('div').hide();
                                        }
                                        appender_con.append(response.html);
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
                <?php } ?>
            <?php } else if ($job_list_style == 'style9') { ?>
                <div class="careerfy-refejobs-list careerfy-refejobs-list-two"
                     id="main-jlists-<?php echo($rand_num) ?>">
                    <ul class="row">
                        <?php self::load_more_jobs_posts($jobs_posts, $job_list_style, $job_per_page, $loc_view_country, $loc_view_state, $loc_view_city); ?>
                    </ul>
                    <?php
                    if ($job_load_more == 'yes' && $totl_found_jobs > $job_per_page) {
                        $total_pages = ceil($totl_found_jobs / $job_per_page); ?>
                        <div class="careerfy-loadmore-listingsbtn"><a href="javascript:void(0);"
                                                                      class="lodmore-jlists-<?php echo($rand_num) ?>"
                                                                      data-tpages="<?php echo($total_pages) ?>"
                                                                      data-gtopage="2"><?php esc_html_e('Load More Listings', 'careerfy-frame') ?></a>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            <?php } else if ($job_list_style == 'style8') { ?>
                <div class="careerfy-refejobs-list" id="main-jlists-<?php echo($rand_num) ?>">
                    <ul class="row">
                        <?php self::load_more_jobs_posts($jobs_posts, $job_list_style, $job_per_page, $loc_view_country, $loc_view_state, $loc_view_city); ?>
                    </ul>
                </div>
                <?php
                if ($job_load_more == 'yes' && $totl_found_jobs > $job_per_page) {
                    $total_pages = ceil($totl_found_jobs / $job_per_page); ?>
                    <div class="careerfy-refejobs-loadmore-btn">
                        <a href="javascript:void(0);" class="lodmore-jlists-<?php echo($rand_num) ?>"
                           data-tpages="<?php echo($total_pages) ?>"
                           data-gtopage="2"><?php esc_html_e('Show More Jobs', 'careerfy-frame') ?></a>
                    </div>
                <?php } ?>
                <script>
                    jQuery(document).on('click', '.lodmore-jlists-<?php echo($rand_num) ?>', function (e) {
                        e.preventDefault();
                        var _this = jQuery(this),
                            total_pages = _this.attr('data-tpages'),
                            page_num = _this.attr('data-gtopage'),
                            this_html = _this.html(),
                            appender_con = jQuery('#main-jlists-<?php echo($rand_num) ?> > ul'),
                            ajax_url = '<?php echo admin_url('admin-ajax.php') ?>';
                        if (!_this.hasClass('ajax-loadin')) {
                            _this.addClass('ajax-loadin');
                            _this.html(this_html + ' <i class="fa fa-refresh fa-spin"></i>');

                            total_pages = parseInt(total_pages);
                            page_num = parseInt(page_num);
                            var request = jQuery.ajax({
                                url: ajax_url,
                                method: "POST",
                                data: {
                                    page_num: page_num,
                                    job_cat: '<?php echo($job_cat) ?>',
                                    featured_only: '<?php echo($featured_only) ?>',
                                    job_order: '<?php echo($job_order) ?>',
                                    job_orderby: '<?php echo($job_orderby) ?>',
                                    job_per_page: '<?php echo($job_per_page) ?>',
                                    job_list_style: '<?php echo($job_list_style) ?>',
                                    loc_view_country: '<?php echo($loc_view_country) ?>',
                                    loc_view_state: '<?php echo($loc_view_state) ?>',
                                    loc_view_city: '<?php echo($loc_view_city) ?>',
                                    action: 'jobsearch_load_more_insimple_jobslistin_con',
                                },
                                dataType: "json"
                            });

                            request.done(function (response) {
                                if ('undefined' !== typeof response.html) {
                                    page_num += 1;
                                    _this.attr('data-gtopage', page_num)
                                    if (page_num > total_pages) {
                                        _this.parent('div').hide();
                                    }
                                    appender_con.append(response.html);
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
            <?php } else { ?>
                <div id="main-jlists-<?php echo($rand_num) ?>" class="careerfy-recentjobs-list">
                    <div class="careerfy-fancy-title-eleven careerfy-fancy-title-eleven-left">
                        <?php if ($title_img != '') { ?>
                            <img src="<?php echo $title_img ?>" alt="">
                        <?php } ?>
                        <?php if ($job_list_title != '') { ?>
                            <h2><?php echo($job_list_title) ?></h2>
                        <?php } ?>
                        <span><?php echo $job_list_description ?></span>
                    </div>
                    <?php
                    if (!empty($jobs_posts)) { ?>
                        <ul class="row">
                            <?php
                            self::load_more_jobs_posts($jobs_posts, $job_list_style, '', $job_per_page, $loc_view_country, $loc_view_state, $loc_view_city);
                            ?>
                        </ul>
                        <?php
                    } else {
                        echo '<p>' . esc_html__('No job found.', 'careerfy-frame') . '</p>';
                    }
                    if ($job_load_more == 'yes' && $totl_found_jobs > $job_per_page) {
                        $total_pages = ceil($totl_found_jobs / $job_per_page); ?>
                        <div class="careerfy-loadmore-listingsbtn"><a href="javascript:void(0);"
                                                                      class="lodmore-jlists-<?php echo($rand_num) ?>"
                                                                      data-tpages="<?php echo($total_pages) ?>"
                                                                      data-gtopage="2"><?php esc_html_e('Load More Listings', 'careerfy-frame') ?></a>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            <?php }
            $html = ob_get_clean();
            return $html;
        }
    }

    public static function load_more_jobs_posts($jobs_posts, $job_list_style, $job_per_page = '', $loc_view_country, $loc_view_state, $loc_view_city)
    {
        global $jobsearch_plugin_options, $sitepress, $rand_num;
        $date_format = get_option('date_format');
        $job_apply_deadline_sw = isset($jobsearch_plugin_options['job_appliction_deadline']) ? $jobsearch_plugin_options['job_appliction_deadline'] : '';

        $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';
        $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';

        $job_types_switch = isset($jobsearch_plugin_options['job_types_switch']) ? $jobsearch_plugin_options['job_types_switch'] : '';

        $all_locations_type = isset($jobsearch_plugin_options['all_locations_type']) ? $jobsearch_plugin_options['all_locations_type'] : '';

        if (!empty($jobs_posts)) {
            $count = 1;
            foreach ($jobs_posts as $job_id) {
                $jobsearch_job_min_salary = get_post_meta($job_id, 'jobsearch_field_job_salary', true);
                $jobsearch_job_max_salary = get_post_meta($job_id, 'jobsearch_field_job_max_salary', true);
                $post_thumbnail_id = function_exists('jobsearch_job_get_profile_image') ? jobsearch_job_get_profile_image($job_id) : 0;
                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
                $no_placeholder_img = '';
                if (function_exists('jobsearch_no_image_placeholder')) {
                    $no_placeholder_img = jobsearch_no_image_placeholder();
                }
                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : $no_placeholder_img;

                $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
                $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
                $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                if (function_exists('jobsearch_post_city_contry_txtstr')) {
                    $get_job_location = jobsearch_post_city_contry_txtstr($job_id, $loc_view_country, $loc_view_state, $loc_view_city);
                }
                $job_loc_contry = get_post_meta($job_id, 'jobsearch_field_location_location1', true);
                $job_loc_city = get_post_meta($job_id, 'jobsearch_field_location_location3', true);
                $job_deadline = get_post_meta($job_id, 'jobsearch_field_job_application_deadline_date', true);
                $job_post_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                $job_post_date = absint($job_post_date);
                $current_time = current_time('timestamp');
                $elaspedtime = ($current_time) - ($job_post_date);
                $hourz = 24 * 60 * 60;
                $sector_str = '';
                $job_sectors = wp_get_post_terms($job_id, 'sector');
                if (isset($job_sectors[0]->name)) {
                    if ($job_list_style == 'style8' || $job_list_style == 'style9') {
                        $sector_str = '<small><i class="careerfy-icon careerfy-briefcase-work"></i>' . ($job_sectors[0]->name) . '</small>';
                    } else {
                        $sector_str = '<span class="careerfy-featuredjobs-list-sub">' . ($job_sectors[0]->name) . '</span>';
                    }
                }
                $job_type_str = '';
                $job_types = wp_get_post_terms($job_id, 'jobtype');
                if (isset($job_types[0]->name)) {
                    $jobtype_textcolor = get_term_meta($job_types[0]->term_id, 'jobsearch_field_jobtype_color', true);
                    if ($job_list_style == 'style7') {
                        $job_type_str = '<strong ' . ($jobtype_textcolor != '' ? ' style="background-color: ' . $jobtype_textcolor . ';"' : '') . '>' . $job_types[0]->name . '</strong>';
                    } else if ($job_list_style == 'style6') {
                        $job_type_str = '<strong ' . ($jobtype_textcolor != '' ? ' style="background-color: ' . $jobtype_textcolor . ';"' : '') . '>' . $job_types[0]->name . '</strong>';
                    } else if ($job_list_style == 'style5') {
                        $job_type_str = '<span ' . ($jobtype_textcolor != '' ? ' style="background-color: ' . $jobtype_textcolor . ';"' : '') . '>' . $job_types[0]->name . '</span>';
                    } else if ($job_list_style == 'style1') {
                        $job_type_str = '<strong' . ($jobtype_textcolor != '' ? ' style="color: ' . $jobtype_textcolor . ';"' : '') . '>' . ($job_types[0]->name) . '</strong>';
                    } else if ($job_list_style == 'style2') {
                        $job_type_str = '<span class="careerfy-recent-list-status" ' . ($jobtype_textcolor != '' ? ' style="background-color: ' . $jobtype_textcolor . ';"' : '') . '>' . $job_types[0]->name . '</span>';
                    } else if ($job_list_style == 'style3') {
                        $job_type_str = '<small ' . ($jobtype_textcolor != '' ? ' style="background-color: ' . $jobtype_textcolor . ';"' : '') . '>' . $job_types[0]->name . '</small>';
                    } else {
                        $job_type_str = '<div class="careerfy-recentjobs-text three-cell"><span  ' . ($jobtype_textcolor != '' ? ' style="color: ' . $jobtype_textcolor . ';"' : '') . '><i class="fa fa-bookmark"></i> ' . $job_types[0]->name . '</span></div>';
                    }
                }
                $job_salary = jobsearch_job_offered_salary($job_id);
                if ($job_list_style == 'style9') { ?>
                    <li class="col-md-12">
                        <div class="careerfy-refejobs-list-inner">
                            <?php
                            jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'job_listv1');
                            ?>
                            <figure>
                                <a href="<?php echo get_permalink($job_id) ?>"><img
                                            src="<?php echo($post_thumbnail_src) ?>" alt=""></a>
                                <figcaption>
                                    <h2>
                                        <a href="<?php echo get_permalink($job_id) ?>"><?php echo wp_trim_words(get_the_title($job_id), 3) ?></a>
                                    </h2>
                                    <span><?php echo jobsearch_job_get_company_name($job_id) ?></span>
                                </figcaption>
                            </figure>
                            <?php
                            if (!empty($sector_str)) {
                                echo($sector_str);
                            } else {
                                echo '<small></small>';
                            }
                            ?>
                            <small><?php if ($get_job_location != '') { ?><i
                                        class="careerfy-icon careerfy-pin-line"></i><?php echo jobsearch_esc_html($get_job_location);
                                } ?></small>
                            <small><?php if ($job_salary != '') { ?>
                                    <i class="fa fa-money"></i> <?php echo($job_salary) ?>

                                <?php } ?>
                            </small>
                            <small>
                                <?php if ($job_apply_deadline_sw == 'on') { ?>
                                    <i class="fa fa-calendar"></i><?php echo date_i18n($date_format, $job_deadline) ?>
                                <?php } ?>
                            </small>
                            <a href="<?php echo get_permalink($job_id) ?>"
                               class="careerfy-refejobs-list-btn"><span><?php echo esc_html__('Apply', 'careerfy-frame') ?></span></a>
                            <?php if ($jobsearch_job_featured == 'on') { ?>
                                <span class="careerfy-jobli-medium3"><i class="fa fa-star"></i></span>
                            <?php } ?>
                        </div>
                    </li>
                <?php } else if ($job_list_style == 'style8') { ?>
                    <li class="col-md-12">
                        <div class="careerfy-refejobs-list-inner">
                            <?php
                            jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'job_listv1');
                            ?>
                            <figure>
                                <a href="<?php echo get_permalink($job_id) ?>"><img
                                            src="<?php echo($post_thumbnail_src) ?>" alt=""></a>
                                <figcaption>
                                    <h2>
                                        <a href="<?php echo get_permalink($job_id) ?>"><?php echo wp_trim_words(get_the_title($job_id), 3) ?></a>
                                    </h2>
                                    <span><?php echo jobsearch_job_get_company_name($job_id) ?></span>
                                </figcaption>
                            </figure>
                            <?php
                            if (!empty($sector_str)) {
                                echo($sector_str);
                            } else {
                                echo '<small></small>';
                            }
                            ?>
                            <small><?php if ($get_job_location != '') { ?><i
                                        class="careerfy-icon careerfy-pin-line"></i><?php echo esc_html($get_job_location);
                                } ?></small>

                            <small><?php if ($job_salary != '') { ?>
                                    <i class="fa fa-money"></i> <?php echo($job_salary) ?>

                                <?php } ?>
                            </small>

                            <small>
                                <?php if ($job_apply_deadline_sw == 'on') { ?>
                                    <i class="fa fa-calendar"></i><?php echo date_i18n($date_format, $job_deadline) ?>
                                <?php } ?>
                            </small>
                            <a href="<?php echo get_permalink($job_id) ?>"
                               class="careerfy-refejobs-list-btn"><span><?php echo esc_html__('Apply', 'careerfy-frame') ?></span></a>

                            <?php if ($jobsearch_job_featured == 'on') { ?>
                                <span class="careerfy-jobli-medium3"><i class="fa fa-star"></i></span>
                            <?php } ?>
                        </div>
                    </li>

                <?php } else if ($job_list_style == 'style7') { ?>
                    <li class="col-md-12">
                        <div class="careerfy-fifteen-recent-jobs-inner">
                            <?php
                            jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'job_listv1');
                            ?>
                            <?php if ($job_post_date != '') { ?>
                                <time datetime="<?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $job_post_date) ?>">
                                    <i
                                            class="fa fa-clock-o"></i><?php printf(esc_html__('%s', 'careerfy'), jobsearch_time_elapsed_string($job_post_date, '', '', false)); ?>
                                </time>
                            <?php } ?>

                            <figure>
                                <a href="<?php echo get_permalink($job_id) ?>"><img
                                            src="<?php echo($post_thumbnail_src) ?>" alt=""></a>
                                <figcaption>
                                    <h2>
                                        <a href="<?php echo get_permalink($job_id) ?>"><?php echo limit_text(get_the_title($job_id, ''), 2) ?></a>
                                    </h2>
                                    <span><?php echo jobsearch_job_get_company_name($job_id) ?></span>
                                </figcaption>
                            </figure>
                            <?php
                            ob_start();
                            ?>
                            <small>
                                <?php if ($job_apply_deadline_sw == 'on') { ?>
                                    <i class="fa fa-calendar"></i><?php echo date_i18n($date_format, $job_deadline) ?>
                                <?php } ?>
                            </small>
                            <?php
                            $item_date_time = ob_get_clean();
                            echo apply_filters('careerfy_simplejobs_sh_view7_itmdate', $item_date_time, $job_id);
                            ?>

                            <small>
                                <?php
                                if (!empty($get_job_location) && $all_location_allow == 'on') { ?>
                                    <i class="careerfy-icon careerfy-pin-line"></i> <?php echo esc_html($get_job_location); ?>
                                <?php } ?>
                            </small>

                            <?php
                            ob_start();
                            ?>
                            <small>
                                <?php if (!empty($job_salary) != '') { ?>
                                    <i class="fa fa-money"></i><?php echo($job_salary) ?>
                                <?php } ?>
                            </small>
                            <?php
                            $item_salary_html = ob_get_clean();
                            echo apply_filters('careerfy_simplejobs_sh_view7_itmsalary', $item_salary_html, $job_id);

                            //
                            if ($job_type_str != '' && $job_types_switch == 'on') {
                                echo($job_type_str);
                            } ?>
                            <?php if ($jobsearch_job_featured == 'on') { ?>
                                <span class="careerfy-jobli-medium3"><i class="fa fa-star"></i></span>
                            <?php } ?>
                        </div>
                    </li>
                <?php } else if ($job_list_style == 'style5') { ?>
                    <li class="col-md-6">
                        <div class="careerfy-jobslatest-list-inner">

                            <?php
                            jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'job_listv1');
                            ?>
                            <figure>
                                <a href="<?php echo get_permalink($job_id) ?>"><img
                                            src="<?php echo($post_thumbnail_src) ?>" alt=""></a>
                                <figcaption>
                                    <h2>
                                        <a href="<?php echo get_permalink($job_id) ?>"><?php echo get_the_title($job_id, '') ?></a>
                                    </h2>
                                    <small><?php echo jobsearch_job_get_company_name($job_id) ?></small>
                                    <?php
                                    if (!empty($get_job_location) && $all_location_allow == 'on') { ?>
                                        <span><i class="careerfy-icon careerfy-pin-line"></i><?php echo esc_html($get_job_location); ?></span>
                                    <?php } ?>
                                </figcaption>
                            </figure>
                            <div class="careerfy-jobslatest-list-cell">

                                <small>
                                    <?php if ($job_apply_deadline_sw == 'on') {
                                        echo date_i18n($date_format, $job_deadline);
                                    } ?>
                                </small>
                                <?php
                                if ($job_type_str != '' && $job_types_switch == 'on') {
                                    echo($job_type_str);
                                } else {
                                    echo '<strong></strong>';
                                }
                                ?>
                            </div>
                            <?php if ($jobsearch_job_featured == 'on') { ?>
                                <span class="careerfy-jobli-medium3"><i class="fa fa-star"></i></span>
                            <?php } ?>
                        </div>
                    </li>
                <?php } else if ($job_list_style == 'style1') { ?>
                    <li class="col-md-12">
                        <a href="<?php echo get_permalink($job_id) ?>" class="careerfy-simp-featjobs-view1">
                            <figure>
                                <img src="<?php echo($post_thumbnail_src) ?>" alt="">
                                <?php
                                if ($jobsearch_job_featured == 'on') { ?>
                                    <i class="fa fa-star"></i>
                                <?php } ?>
                            </figure>
                            <?php
                            jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'style9');
                            ?>
                            <div class="careerfy-featuredjobs-box careerfy-featuredjobs-sectrr">
                                <h2><?php echo substr(get_the_title($job_id), 0, 20) . (strlen(get_the_title($job_id)) > 20 ? '...' : '') ?><?php echo($elaspedtime > $hourz ? '' : '<span class="careerfy-featuredjobs-listnew">' . esc_html__('New', 'careerfy-frame') . '</span>') ?></h2>
                                <?php
                                if (!empty($sector_str) && $sectors_enable_switch == 'on') {
                                    echo($sector_str);
                                } ?>
                            </div>

                            <div class="careerfy-featuredjobs-box careerfy-featuredjobs-loction">
                                <?php if (!empty($get_job_location) && $all_location_allow == 'on') { ?>
                                    <small><?php echo esc_html($get_job_location); ?></small>
                                <?php } ?>
                            </div>

                            <div class="careerfy-featuredjobs-box careerfy-featuredjobs-posdat">
                                <time datetime="2018-02-14 20:00">
                                    <?php
                                    if ($job_type_str != '' && $job_types_switch == 'on') {
                                        echo($job_type_str);
                                    } ?>
                                    <br> <?php printf(esc_html__('Posted %s', 'careerfy-frame'), jobsearch_time_elapsed_string($job_post_date, '', '', true)); ?>
                                </time>
                            </div>
                        </a>
                    </li>
                <?php } else if ($job_list_style == 'style2') { ?>
                    <li class="col-md-12">
                        <div class="careerfy-recent-list-wrap">
                            <figure><a href="<?php echo get_permalink($job_id) ?>">
                                    <img src="<?php echo($post_thumbnail_src) ?>" alt="">
                                    <?php
                                    if ($jobsearch_job_featured == 'on') { ?>
                                        <i class="fa fa-star"></i>
                                    <?php } ?>
                                </a></figure>
                            <?php
                            jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'job_listv1');
                            ?>
                            <div class="careerfy-recent-list-text">
                                <h2>
                                    <a href="<?php echo get_permalink($job_id) ?>"><?php echo substr(get_the_title($job_id), 0, 20) . (strlen(get_the_title($job_id)) > 20 ? '...' : '') ?><?php echo($elaspedtime > $hourz ? '' : '<span class="careerfy-featuredjobs-listnew">' . esc_html__('New', 'careerfy-frame') . '</span>') ?></a>
                                </h2>
                                <ul>
                                    <?php if ($job_apply_deadline_sw == 'on') { ?>
                                        <li><i class="careerfy-icon careerfy-calendar"></i>
                                            <span><?php echo esc_html__('Deadline:', 'careerfy-frame') ?></span> <?php echo date_i18n($date_format, $job_deadline) . "<br>"; ?>
                                        </li>
                                        <?php
                                    }
                                    if (!empty($get_job_location) && $all_location_allow == 'on') { ?>
                                        <li><i class="careerfy-icon careerfy-pin"></i>
                                            <span><?php echo esc_html__('Location:', 'careerfy-frame') ?></span> <?php echo esc_html__($get_job_location, 'careerfy-frame'); ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                                <?php
                                if ($job_type_str != '' && $job_types_switch == 'on') {
                                    echo($job_type_str);
                                }
                                ?>
                            </div>
                        </div>
                    </li>
                <?php } else if ($job_list_style == 'style3') { ?>
                    <li class="col-md-12">
                        <div class="careerfy-premium-jobs-inner">
                            <?php
                            jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'job_listv1');
                            ?>
                            <figure>
                                <a href="<?php echo get_permalink($job_id) ?>"><img
                                            src="<?php echo($post_thumbnail_src) ?>" alt=""></a>
                                <figcaption>
                                    <h2>
                                        <a href="<?php echo get_permalink($job_id) ?>"><?php echo limit_text(get_the_title($job_id), 5) ?></a>
                                    </h2>
                                    <small><?php echo jobsearch_job_get_company_name($job_id, '') ?><?php echo($elaspedtime > $hourz ? '' : '<span>' . esc_html__('New', 'careerfy-frame') . '</span>') ?></small>
                                </figcaption>
                            </figure>
                            <div class="careerfy-premium-jobs-text">
                                <span><?php echo esc_html($get_job_location); ?></span>
                                <?php
                                if ($job_type_str != '' && $job_types_switch == 'on') {
                                    echo($job_type_str);
                                } ?>
                            </div>
                            <?php if ($jobsearch_job_featured == 'on') { ?>
                                <span class="careerfy-jobli-medium3"><i class="fa fa-star"></i></span>
                            <?php } ?>
                        </div>
                    </li>
                <?php } else { ?>
                    <li class="col-md-12">
                        <div class="careerfy-recentjobs-list-inner">
                            <figure><a href="<?php echo get_permalink($job_id) ?>">
                                    <img src="<?php echo($post_thumbnail_src) ?>" alt="">
                                    <?php
                                    if ($jobsearch_job_featured == 'on') { ?>
                                        <i class="fa fa-star"></i>
                                    <?php } ?>
                                </a></figure>
                            <?php jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'job_listv1'); ?>
                            <div class="careerfy-recentjobs-text">
                                <h2>
                                    <a href="<?php echo get_permalink($job_id) ?>"><?php echo substr(get_the_title($job_id), 0, 20) . (strlen(get_the_title($job_id)) > 20 ? '...' : '') ?><?php echo($elaspedtime > $hourz ? '' : '<span class="careerfy-featuredjobs-listnew">' . esc_html__('New', 'careerfy-frame') . '</span>') ?></a>
                                </h2>
                                <time datetime="2008-02-14 20:00"><i
                                            class="careerfy-icon careerfy-clock"></i> <?php printf(esc_html__('%s', 'careerfy-frame'), jobsearch_time_elapsed_string($job_post_date, '', '', false)); ?>
                                </time>
                            </div>

                            <div class="careerfy-recentjobs-text two-cell">
                                <small>
                                <?php
                                if (!empty($get_job_location) && $all_location_allow == 'on') {
                                    ?>
                                    <i class="careerfy-icon careerfy-pin"></i><?php echo esc_html($get_job_location); ?>
                                <?php } ?>
                                </small>
                            </div>

                            <?php
                            if ($job_type_str != '' && $job_types_switch == 'on') {
                                echo($job_type_str);
                            } else {
                                echo '<strong></strong>';
                            }
                            ?>
                            <div class="careerfy-recentjobs-text">
                                <a href="<?php echo get_permalink($job_id) ?>"
                                   class="careerfy-recentjobs-list-btn"><?php echo esc_html__('Apply', 'careerfy-frame') ?></a>
                            </div>
                            <?php if ($jobsearch_job_featured == 'on') { ?>
                                <span class="careerfy-jobli-medium3"><i class="fa fa-star"></i></span>
                            <?php } ?>
                        </div>
                    </li>
                <?php }
                if ($count % 4 === 0 && $count != $job_per_page && $job_list_style == 'style3') {
                    echo '
                        </ul>
                    </div>
                </div>
                <div class="careerfy-premium-jobs-slider-layer">
                    <div class="careerfy-premium-jobs">
                        <ul class="row main-layer-jobslists-' . $rand_num . '">';
                }
                $count++;
            }

        }
    }

    public function load_more_jobs_in_list()
    {
        global $jobsearch_shortcode_jobs_frontend;

        $page_num = isset($_POST['page_num']) ? $_POST['page_num'] : '';
        $job_cat = isset($_POST['job_cat']) ? $_POST['job_cat'] : '';
        $featured_only = isset($_POST['featured_only']) ? $_POST['featured_only'] : '';
        $job_order = isset($_POST['job_order']) ? $_POST['job_order'] : '';
        $job_orderby = isset($_POST['job_orderby']) ? $_POST['job_orderby'] : '';
        $job_per_page = isset($_POST['job_per_page']) ? $_POST['job_per_page'] : '';
        $job_list_style = isset($_POST['job_list_style']) ? $_POST['job_list_style'] : '';
        $loc_view_country = isset($_POST['loc_view_country']) ? $_POST['loc_view_country'] : '';
        $loc_view_state = isset($_POST['loc_view_state']) ? $_POST['loc_view_state'] : '';
        $loc_view_city = isset($_POST['loc_view_city']) ? $_POST['loc_view_city'] : '';

        $jobsearch__options = get_option('jobsearch_plugin_options');

        $emporler_approval = isset($jobsearch__options['job_listwith_emp_aprov']) ? $jobsearch__options['job_listwith_emp_aprov'] : '';
        $is_filled_jobs = isset($jobsearch__options['job_allow_filled']) ? $jobsearch__options['job_allow_filled'] : '';

        $job_per_page = isset($job_per_page) && !empty($job_per_page) && $job_per_page > 0 ? $job_per_page : 10;

        //
        $post_ids = array();
        $sh_atts = array();
        $all_post_ids = $jobsearch_shortcode_jobs_frontend->job_general_query_filter($post_ids, $sh_atts);
        //

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
            'posts_per_page' => $job_per_page,
            'paged' => $page_num,
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

        if (!empty($all_post_ids)) {
            $args['post__in'] = $all_post_ids;
        }

        $jobs_query = new WP_Query($args);
        $jobs_posts = $jobs_query->posts;

        ob_start();
        self::load_more_jobs_posts($jobs_posts, $job_list_style, $job_per_page, $loc_view_country, $loc_view_state, $loc_view_city);
        $html = ob_get_clean();
        echo json_encode(array('html' => $html));
        wp_die();
    }

}

return new JobSearch_Careerfy_Simple_Jobs_Listins();
