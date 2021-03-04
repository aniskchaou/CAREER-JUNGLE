<?php
/**
 * Blog Shortcode
 * @return html
 */
add_shortcode('careerfy_blog_shortcode', 'careerfy_blog_shortcode');

function careerfy_blog_shortcode($atts)
{
    global $blog_per_page;
    extract(shortcode_atts(array(
        'blog_cat' => '',
        'blog_view' => '',
        'blog_excerpt' => '20',
        'blog_order' => 'DESC',
        'blog_orderby' => 'date',
        'blog_pagination' => 'yes',
        'blog_per_page' => '9',
    ), $atts));

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

    $blog_query = new WP_Query($args);
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
        if ($blog_pagination == 'yes' && $total_posts > $blog_per_page && ($blog_view == 'view5' || $blog_view == 'view12')) {
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
                        _this.html(this_html + ' <i class="fa fa-refresh fa-spin"></i>');

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
    return $html;
}

add_action('wp_ajax_jobsearch_load_morein_blogs_view5_con', 'load_more_blogs_view5_in_list');
add_action('wp_ajax_nopriv_jobsearch_load_morein_blogs_view5_con', 'load_more_blogs_view5_in_list');

function load_more_blogs_view5_in_list()
{
    $page_num = isset($_POST['page_num']) ? $_POST['page_num'] : '';
    $blog_cat = isset($_POST['blog_cat']) ? $_POST['blog_cat'] : '';
    $blog_excerpt = isset($_POST['blog_excerpt']) ? $_POST['blog_excerpt'] : '';
    $blog_order = isset($_POST['blog_order']) ? $_POST['blog_order'] : '';
    $blog_orderby = isset($_POST['blog_orderby']) ? $_POST['blog_orderby'] : '';
    $blog_per_page = isset($_POST['blog_per_page']) ? $_POST['blog_per_page'] : '';

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $blog_per_page,
        'paged' => $page_num,
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

    $blog_query = new WP_Query($args);

    ob_start();
    while ($blog_query->have_posts()) : $blog_query->the_post();
        do_action('careerfy_news_blog_view5', $post->ID, $blog_excerpt);
    endwhile;
    $html = ob_get_clean();
    echo json_encode(array('html' => $html));

    wp_die();
}

if (!function_exists('careerfy_news_blog_view13')) {

    /*
     * Blog Grid View7
     * @return html
     */

    function careerfy_news_blog_view13($post_id = '', $excerpt_length = '')
    {
        global $counter, $blog_per_page;

        $post_thumbnail_id = get_post_thumbnail_id($post_id);
        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
        $no_placeholder_img = '';
        if (function_exists('jobsearch_no_image_placeholder')) {
            $no_placeholder_img = jobsearch_no_image_placeholder();
        }
        $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : $no_placeholder_img;
        $categories = get_the_category();
        $blog_detail = get_post($post_id);
        $post_date_month = get_the_date('M', $post_id);
        $post_date_day = get_the_date('d', $post_id); ?>

        <div class="col-md-6">
            <div class="careerfy-eighteen-blog-grid-inner">
                <figure>
                    <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><img
                                src="<?php echo esc_url($post_thumbnail_src); ?>" alt=""> <i
                                class="fa fa-arrow-right"></i> </a>
                </figure>
                <div class="careerfy-eighteen-blog-grid-text">
                    <span><?php echo strtoupper($post_date_month) ?>
                        <small><?php echo $post_date_day ?></small>
                    </span>
                    <div class="careerfy-eighteen-blog-grid-left">
                        <h2>
                            <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo $blog_detail->post_title ?></a>
                        </h2>
                        <?php if (careerfy_excerpt($excerpt_length)) { ?>
                            <p><?php echo careerfy_excerpt($excerpt_length) ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php }

    add_action('careerfy_news_blog_view13', 'careerfy_news_blog_view13', 10, 2);
}

if (!function_exists('careerfy_news_blog_view12')) {

    /*
     * Blog Grid View7
     * @return html
     */
    function careerfy_news_blog_view12($post_id = '', $excerpt_length = '')
    {
        global $counter, $blog_per_page;
        $post_thumbnail_id = get_post_thumbnail_id($post_id);
        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
        $no_placeholder_img = '';
        if (function_exists('jobsearch_no_image_placeholder')) {
            $no_placeholder_img = jobsearch_no_image_placeholder();
        }
        $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : $no_placeholder_img;
        $categories = get_the_category();
        $blog_detail = get_post($post_id);
        $post_date = get_the_date(get_option('date_format'), $post_id);
        $post_views_count = get_post_meta($post_id, 'careerfy_post_views_count', true); ?>
        <li class="col-md-6">
            <div class="careerfy-sixteen-blog-medium-inner">
                <figure><a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><img
                                src="<?php echo esc_url($post_thumbnail_src); ?>" alt=""></a></figure>
                <div class="careerfy-sixteen-blog-medium-text">
                    <?php
                    if (!empty($categories)) {
                        foreach ($categories as $category) {
                            echo '<span>' . $category->name . ',</span>';
                        }
                    } ?>
                    <span><?php echo $post_date ?></span>
                    <h2>
                        <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo $blog_detail->post_title ?></a>
                    </h2>
                    <?php if (careerfy_excerpt($excerpt_length)) { ?>
                        <p><?php echo careerfy_excerpt($excerpt_length) ?></p>
                    <?php } ?>
                </div>
            </div>
        </li>
    <?php }

    add_action('careerfy_news_blog_view12', 'careerfy_news_blog_view12', 10, 2);
}

if (!function_exists('careerfy_news_blog_view11')) {

    /*
     * Blog Grid View7
     * @return html
     */

    function careerfy_news_blog_view11($post_id = '', $excerpt_length = '')
    {
        global $counter, $blog_per_page;

        $post_thumbnail_id = get_post_thumbnail_id($post_id);
        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
        $no_placeholder_img = '';
        if (function_exists('jobsearch_no_image_placeholder')) {
            $no_placeholder_img = jobsearch_no_image_placeholder();
        }
        $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : $no_placeholder_img;
        $categories = get_the_category();
        $blog_detail = get_post($post_id);
        $post_date = get_the_date(get_option('date_format'), $post_id);
        $post_views_count = get_post_meta($post_id, 'careerfy_post_views_count', true); ?>
        <li class="col-md-6">
            <div class="careerfy-fifteen-blog-medium-inner">
                <figure><a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><img
                                src="<?php echo esc_url($post_thumbnail_src); ?>" alt=""></a></figure>
                <div class="careerfy-fifteen-blog-medium-text">
                    <h2>
                        <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo $blog_detail->post_title ?></a>
                    </h2>
                    <span><i class="fa fa-calendar"></i><?php echo $post_date ?></span>
                    <span><i class="fa fa-eye"></i> <?php echo $post_views_count ?> <?php echo esc_html__('Viewed', 'careerfy-frame') ?></span>
                    <?php if (careerfy_excerpt($excerpt_length)) { ?>
                        <p><?php echo careerfy_excerpt($excerpt_length) ?></p>
                    <?php } ?>
                </div>
            </div>
        </li>
    <?php }

    add_action('careerfy_news_blog_view11', 'careerfy_news_blog_view11', 10, 2);
}

if (!function_exists('careerfy_news_blog_view10')) {

    /*
     * Blog Grid View7
     * @return html
     */

    function careerfy_news_blog_view10($post_id = '', $excerpt_length = '')
    {
        global $counter, $blog_per_page;

        $post_thumbnail_id = get_post_thumbnail_id($post_id);
        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-img1');
        $no_placeholder_img = '';
        if (function_exists('jobsearch_no_image_placeholder')) {
            $no_placeholder_img = jobsearch_no_image_placeholder();
        }
        $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : $no_placeholder_img;
        $categories = get_the_category($post_id);
        $blog_detail = get_post($post_id);
        $post_date = get_the_date(get_option('date_format'), $post_id);
        $numbers_of_comments = wp_count_comments($post_id);
        $careerfy_like_counter = get_post_meta($post_id, "careerfy_post_likes", true);
        $careerfy_post_like_counter = $careerfy_like_counter != "" ? $careerfy_like_counter : "0";
        $post_views_count = get_post_meta($post_id, 'careerfy_post_views_count', true);
        $author_id = get_post_field('post_author', $post_id);
        $author_total_posts = count_user_posts($author_id, 'post');

        $avatar_link = get_avatar_url($author_id, array('size' => 114));
        if (@getimagesize($avatar_link)) {
            $avatar_link = $avatar_link;
        } else {
            $avatar_link = get_template_directory_uri() . '/images/default_avatar.jpg';
        }
        if ($counter == 1 || $counter == $blog_per_page) {
            $col_class = "col-md-6";
        } else {
            $col_class = 'col-md-3';
        }
        ?>
        <style>
            .careerfy-blog-style14-grid .blog-bg-img-<?php echo $counter ?> {
                background: url('<?php echo esc_url($post_thumbnail_src); ?>');
            }
        </style>
        <li class="<?php echo $col_class ?>">
            <figure>
                <a class="blog-img blog-bg-img-<?php echo $counter ?> "
                   href="<?php echo esc_url(get_permalink(get_the_ID())) ?>">
                    <!--                    <img src="--><?php //echo esc_url($post_thumbnail_src);
                    ?><!--" alt="">-->
                </a>
                <figcaption>
                    <div class="careerfy-blog-style14-top">
                        <?php
                        $category_names = array();
                        if (!empty($categories)) {
                            foreach ($categories as $category) {
                                $category_names[] = '<a class="careerfy-blog-style14-tag" href="' . get_category_link($category->term_id) . '">' . $category->name . '</a>';
                            }
                            echo implode(' / ', $category_names);
                        }
                        ?>
                        <?php echo do_action('careerfy_post_like_btns', $post_id); ?>
                    </div>
                    <div class="careerfy-blog-style14-bottom">
                        <ul>
                            <li><i class="careerfy-icon careerfy-clock"></i> <?php echo $post_date ?></li>
                            <li><i class="fa fa-eye"></i> <?php echo $post_views_count ?></li>
                            <li><a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><i
                                            class="fa fa-commenting-o"></i>
                                    (<?php echo $numbers_of_comments->approved ?>)</a></li>
                        </ul>
                    </div>
                </figcaption>
            </figure>
            <div class="careerfy-blog-style14-grid-text">
                <h2>
                    <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo limit_text($blog_detail->post_title, 3) ?></a>
                </h2>
                <?php
                if (careerfy_excerpt($excerpt_length)) {
                    ?>
                    <p><?php echo careerfy_excerpt($excerpt_length) ?></p>
                    <?php
                }
                ?>
                <a href="<?php echo esc_url(get_author_posts_url($author_id)) ?>"
                   class="careerfy-blog-style14-grid-thumb">
                    <img src="<?php echo esc_url_raw($avatar_link); ?>" alt="">
                    <span><?php echo esc_url(the_author_meta('display_name', $author_id)) ?></span>
                    <small><?php echo $author_total_posts ?><?php echo esc_html__('Posts', 'careerfy-frame') ?></small>
                </a>
            </div>
        </li>
        <?php
    }

    add_action('careerfy_news_blog_view10', 'careerfy_news_blog_view10', 10, 2);
}

if (!function_exists('careerfy_news_blog_view9')) {

    /*
     * Blog Grid View7
     * @return html
     */

    function careerfy_news_blog_view9($post_id = '', $excerpt_length = '')
    {
        $post_thumbnail_id = get_post_thumbnail_id($post_id);
        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-view6');
        $no_placeholder_img = '';
        if (function_exists('jobsearch_no_image_placeholder')) {
            $no_placeholder_img = jobsearch_no_image_placeholder();
        }
        $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : $no_placeholder_img;
        $categories = get_the_category();
        $blog_detail = get_post($post_id);
        $post_date = get_the_date(get_option('date_format'), $post_id);
        $numbers_of_comments = wp_count_comments($post_id);
        $careerfy_like_counter = get_post_meta($post_id, "careerfy_post_likes", true);
        $careerfy_post_like_counter = $careerfy_like_counter != "" ? $careerfy_like_counter : "0";

        ?>
        <li class="col-md-4">
            <div class="careerfy-twelve-blog-grid-inner">
                <figure><a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><img
                                src="<?php echo esc_url($post_thumbnail_src); ?>" alt=""></a></figure>
                <div class="careerfy-twelve-blog-grid-text">
                    <ul>
                        <li><i class="fa fa-calendar"></i> <?php echo $post_date ?></li>
                        <li><a href="javascript:void(0)"><i
                                        class="fa fa-comment"></i> <?php echo $numbers_of_comments->approved ?> <?php echo esc_html__('Comments', 'careerfy-frame') ?>
                            </a></li>
                        <li><a href="javascript:void(0)"><i
                                        class="fa fa-heart"></i><?php echo $careerfy_post_like_counter ?></a></li>
                    </ul>
                    <h2>
                        <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo $blog_detail->post_title ?></a>
                    </h2>
                    <?php
                    if (careerfy_excerpt($excerpt_length)) {
                        ?>
                        <p><?php echo careerfy_excerpt($excerpt_length) ?></p>
                        <?php
                    }
                    ?>
                    <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"
                       class="careerfy-twelve-blog-grid-btn"><?php echo esc_html__('Read More', 'careerfy-frame') ?></a>
                </div>
            </div>
        </li>
        <?php
    }

    add_action('careerfy_news_blog_view9', 'careerfy_news_blog_view9', 10, 2);
}
if (!function_exists('careerfy_news_blog_view8')) {

    /*
     * Blog Grid View7
     * @return html
     */

    function careerfy_news_blog_view8($post_id = '', $excerpt_length = '')
    {
        $post_thumbnail_id = get_post_thumbnail_id($post_id);
        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-view6');
        $no_placeholder_img = '';
        if (function_exists('jobsearch_no_image_placeholder')) {
            $no_placeholder_img = jobsearch_no_image_placeholder();
        }
        $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : $no_placeholder_img;
        $categories = get_the_category();
        $blog_detail = get_post($post_id);
        $post_date = get_the_date(get_option('date_format'), $post_id);
        $numbers_of_comments = wp_count_comments($post_id);
        $careerfy_like_counter = get_post_meta($post_id, "careerfy_post_likes", true);
        $careerfy_post_like_counter = $careerfy_like_counter != "" ? $careerfy_like_counter : "0";

        ?>
        <li class="col-md-4">
            <div class="careerfy-blog-grid-style11-inner">
                <figure>
                    <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><img
                                src="<?php echo esc_url($post_thumbnail_src); ?>" alt=""></a>
                    <figcaption>
                        <img src="<?php echo esc_url($post_thumbnail_src); ?>" alt="">
                    </figcaption>
                </figure>
                <div class="careerfy-blog-grid-style11-text">
                    <h2>
                        <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo $blog_detail->post_title ?></a>
                    </h2>
                    <span><?php echo $post_date ?></span>
                    <?php
                    if (careerfy_excerpt($excerpt_length)) {
                        ?>
                        <p><?php echo careerfy_excerpt($excerpt_length) ?></p>
                        <?php
                    }
                    ?>
                    <ul>
                        <li><a href="#"><i
                                        class="careerfy-icon careerfy-comment-outline"></i><?php echo $numbers_of_comments->approved ?>
                            </a></li>
                        <li><a href="#"><i
                                        class="careerfy-icon careerfy-like"></i><?php echo $careerfy_post_like_counter ?>
                            </a></li>
                    </ul>
                    <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"
                       class="careerfy-blog-grid-style11-btn"><?php echo esc_html__('Read More', 'careerfy-frame') ?></a>
                </div>
            </div>
        </li>
        <?php
    }

    add_action('careerfy_news_blog_view8', 'careerfy_news_blog_view8', 10, 2);
}
if (!function_exists('careerfy_news_blog_view7')) {

    /*
     * Blog Grid View7
     * @return html
     */

    function careerfy_news_blog_view7($post_id = '', $excerpt_length = '')
    {
        $post_thumbnail_id = get_post_thumbnail_id($post_id);
        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-view6');
        $no_placeholder_img = '';
        if (function_exists('jobsearch_no_image_placeholder')) {
            $no_placeholder_img = jobsearch_no_image_placeholder();
        }
        $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : $no_placeholder_img;
        $categories = get_the_category();
        $blog_detail = get_post($post_id);
        $post_date = get_the_date(get_option('date_format'), $post_id);
        $numbers_of_comments = wp_count_comments($post_id);
        $careerfy_like_counter = get_post_meta($post_id, "careerfy_post_likes", true);
        $careerfy_post_like_counter = $careerfy_like_counter != "" ? $careerfy_like_counter : "0";
        ?>
        <li class="col-md-4">
            <div class="careerfy-blog-grid-style10-inner">
                <figure><a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><img
                                src="<?php echo esc_url($post_thumbnail_src); ?>" alt=""></a></figure>
                <div class="careerfy-blog-grid-style10-text">
                    <ul>
                        <li><i class="fa fa-calendar"></i> <?php echo $post_date ?></li>
                        <li><i class="fa fa-comment"></i> <a
                                    href="#"><?php echo $numbers_of_comments->approved ?><?php echo esc_html__('Comments', 'careerfy-frame') ?></a>
                        </li>
                        <li><i class="fa fa-heart"></i> <a href="#"><?php echo $careerfy_post_like_counter ?></a></li>
                    </ul>
                    <h2>
                        <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo $blog_detail->post_title ?></a>
                    </h2>
                    <?php
                    if (careerfy_excerpt($excerpt_length)) {
                        ?>
                        <p><?php echo careerfy_excerpt($excerpt_length) ?></p>
                        <?php
                    }
                    ?>
                    <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"
                       class="careerfy-blog-grid-style10-btn"><?php echo esc_html__('Read More', 'careerfy-frame') ?></a>
                </div>
            </div>
        </li>
        <?php
    }

    add_action('careerfy_news_blog_view7', 'careerfy_news_blog_view7', 10, 2);
}
if (!function_exists('careerfy_news_blog_view6')) {

    /*
     * Blog Grid View6
     * @return html
     */

    function careerfy_news_blog_view6($post_id = '', $excerpt_length = '')
    {

        $post_thumbnail_id = get_post_thumbnail_id($post_id);
        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-view6');
        $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
        $categories = get_the_category();


        ?>

        <li class="col-md-4">
            <figure><a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><img
                            src="<?php echo esc_url($post_thumbnail_src); ?>" alt=""></a></figure>
            <div class="careerfy-blog-view6-text-wrap">
                <div class="careerfy-blog-view6-text">
                    <h2>
                        <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo wp_trim_words(get_the_title(get_the_ID()), 8, '...') ?></a>
                    </h2>
                    <?php
                    if (careerfy_excerpt($excerpt_length)) {
                        ?>
                        <p><?php echo careerfy_excerpt($excerpt_length) ?></p>
                        <?php
                    }
                    ?>

                    <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))) ?>"
                       class="careerfy-blog-view6-thumb">
                        <?php echo get_avatar(get_the_author_meta('ID'), 50); ?>
                    </a>

                    <div class="careerfy-blog-view6-thumb-text">
                        <a class="careerfy-blog-view6-title"
                           href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))) ?>"><?php echo get_the_author() ?></a>
                        <?php
                        $category_names = array();
                        if (!empty($categories)) {
                            foreach ($categories as $category) {
                                $category_names[] = '<a href="' . get_category_link($category->term_id) . '">' . $category->name . '</a>';
                            }
                            echo implode(' / ', $category_names);
                        }
                        ?>
                    </div>
                    <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"
                       class="careerfy-blog-view6-btn"><?php esc_html_e('Read More', 'careerfy-frame'); ?><i
                                class="careerfy-blog-view6-icon"></i></a>
                </div>
            </div>
        </li>
        <?php
    }

    add_action('careerfy_news_blog_view6', 'careerfy_news_blog_view6', 10, 2);
}

if (!function_exists('careerfy_news_blog_view5')) {

    /*
     * Blog Grid View5
     * @return html
     */

    function careerfy_news_blog_view5($post_id = '', $excerpt_length = '')
    {
        global $post;
        $post_thumbnail_id = get_post_thumbnail_id($post_id);
        $post_date = get_the_date(get_option('date_format'), $post_id);
        $numbers_of_comments = wp_count_comments($post_id);
        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'full');
        $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
        $categories = get_the_category();
        ?>
        <li class="col-md-4 masgrid-item">
            <figure><a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><img
                            src="<?php echo esc_url($post_thumbnail_src); ?>" alt=""></a>
                <figcaption>
                    <div class="careerfy-blog-masonry-tag"><?php
                        $category_names = array();
                        if (!empty($categories)) {
                            foreach ($categories as $category) {
                                $category_names[] = '<a href="' . get_category_link($category->term_id) . '">' . $category->name . '</a>';
                            }
                            echo implode(' ', $category_names);
                        }
                        ?> </div>
                    <a href="#" class="careerfy-blog-masonry-like"><i class="careerfy-icon careerfy-heart"></i></a>
                </figcaption>
            </figure>
            <div class="careerfy-blog-masonry-text">
                <h2>
                    <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo wp_trim_words(get_the_title(get_the_ID()), 8, '...') ?></a>
                </h2>
                <ul class="careerfy-blog-masonry-option">
                    <li><i class="fa fa-clock-o"></i><?php echo $post_date ?></li>
                    <li><i class="fa fa-user"></i><?php echo esc_html__('By', 'careerfy-frame') ?> <a
                                href="#"><?php echo the_author_meta('user_nicename', $post->post_author); ?></a></li>
                    <li><i class="fa fa-comment"></i> <a href="#">(<?php echo $numbers_of_comments->approved; ?>)</a>
                    </li>
                </ul>
                <?php if (careerfy_excerpt($excerpt_length)) { ?>
                    <p><?php echo careerfy_excerpt($excerpt_length) ?></p>
                <?php } ?>
            </div>
        </li>
        <?php
    }

    add_action('careerfy_news_blog_view5', 'careerfy_news_blog_view5', 10, 2);
}


if (!function_exists('careerfy_news_medium')) {

    /*
     * Blog Grid View medium
     * @return html
     */

    function careerfy_news_medium($post_id = '', $excerpt_length = '')
    {

        $post_thumbnail_id = get_post_thumbnail_id($post_id);
        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-medium');
        $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
        $categories = get_the_category();
        ?>

        <li class="col-md-6">
            <figure><a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><img
                            src="<?php echo esc_url($post_thumbnail_src); ?>" alt=""></a></figure>
            <div class="careerfy-blog-medium-text">
                <span><time datetime="<?php echo date('Y-m-d ' . get_option('time_format'), strtotime(get_the_date())) ?>"><?php echo get_the_date(); ?></time></span>
                <h2>
                    <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo wp_trim_words(get_the_title(get_the_ID()), 8, '...') ?></a>
                </h2>
                <?php
                if (careerfy_excerpt($excerpt_length)) {
                    ?>
                    <p><?php echo careerfy_excerpt($excerpt_length) ?></p>
                    <?php
                }
                ?>
                <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"
                   class="careerfy-blog-medium-btn"><?php esc_html_e('Read More', 'careerfy-frame'); ?><i
                            class="careerfy-icon careerfy-right-arrow-long"></i></a>
                <time datetime="2008-02-14 20:00"><?php echo careerfy_time_elapsed_string(get_the_time('U')); ?></time>
                <a href="#"
                   class="careerfy-blog-medium-thumb"><?php echo get_avatar(get_the_author_meta('ID'), 50); ?><?php echo get_the_author(); ?></a>
            </div>
        </li>
        <?php
    }

    add_action('careerfy_news_medium', 'careerfy_news_medium', 10, 2);
}


if (!function_exists('careerfy_blog_grid')) {

    /*
     * Blog Grid View
     * @return html
     */

    function careerfy_blog_grid($post_id = '', $excerpt_length = '')
    {

        $post_thumbnail_id = get_post_thumbnail_id($post_id);
        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'medium');
        $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';

        $categories = get_the_category();
        ?>

        <li class="col-md-4">
            <figure>
                <?php
                if ($post_thumbnail_src != '') {
                    ?>
                    <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><img
                                src="<?php echo esc_url($post_thumbnail_src) ?>" alt=""></a>
                    <?php
                }
                ?>
            </figure>
            <div class="careerfy-blog-grid-text">
                <?php
                if (!empty($categories)) {
                    echo '<div class="careerfy-blog-tag"> <a href="' . esc_url(get_category_link($categories[0]->term_id)) . '"> ' . esc_html($categories[0]->name) . '</a> </div>';
                }
                ?>
                <h2>
                    <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo wp_trim_words(get_the_title(get_the_ID()), 8, '...') ?></a>
                </h2>
                <ul class="careerfy-blog-grid-option">
                    <li><?php esc_html_e('BY', 'careerfy-frame'); ?> <a
                                href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))) ?>"
                                class="careerfy-color"><?php echo get_the_author() ?></a></li>
                    <li>
                        <time datetime="<?php echo date('Y-m-d ' . get_option('time_format'), strtotime(get_the_date())) ?>"><?php echo get_the_date(); ?></time>
                    </li>
                </ul>
                <?php
                if (careerfy_excerpt($excerpt_length)) {
                    ?>
                    <p><?php echo careerfy_excerpt($excerpt_length) ?></p>
                    <?php
                }
                $red_art_titl = apply_filters('careerfy_blogsh_gridview_readmore_title', esc_html__('Read Articles', 'careerfy-frame'));
                ?>
                <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"
                   class="careerfy-read-more careerfy-bgcolor"><?php echo($red_art_titl); ?></a>
            </div>
        </li>
        <?php
    }

    add_action('careerfy_blog_grid', 'careerfy_blog_grid', 10, 2);
}

if (!function_exists('careerfy_news_grid')) {

    /*
     * News Grid View
     * @return html
     */

    function careerfy_news_grid($post_id = '', $excerpt_length = '')
    {
        $post_thumbnail_id = get_post_thumbnail_id($post_id);
        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'medium');
        $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
        $categories = get_the_category();
        ?>

        <li class="col-md-4">
            <div class="careerfy-news-grid-wrap">
                <figure>
                    <?php if ($post_thumbnail_src != '') { ?>
                        <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><img
                                    src="<?php echo esc_url($post_thumbnail_src) ?>" alt=""></a>
                    <?php } ?>
                </figure>
                <div class="careerfy-news-grid-text">
                    <ul>
                        <li><?php echo get_the_date() ?></li>
                        <?php
                        if (!empty($categories)) {
                            echo '<li><a href="' . esc_url(get_category_link($categories[0]->term_id)) . '"> ' . esc_html($categories[0]->name) . '</a></li>';
                        }
                        ?>
                    </ul>
                    <h2>
                        <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo wp_trim_words(get_the_title(get_the_ID()), 8, '...') ?></a>
                    </h2>
                    <?php
                    if (careerfy_excerpt($excerpt_length)) { ?>
                        <p><?php echo careerfy_excerpt($excerpt_length) ?></p>
                    <?php } ?>
                    <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>" class="careerfy-modren-service-link"><i
                                class="careerfy-icon careerfy-right-arrow-long"></i></a>
                </div>
            </div>
        </li>
        <?php
    }

    add_action('careerfy_news_grid', 'careerfy_news_grid', 10, 2);
}
