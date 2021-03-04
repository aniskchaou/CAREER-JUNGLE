<?php
/**
 * Careerfy Theme functions.
 *
 * @package Careerfy
 */
if (!function_exists('careerfy_google_fonts_url')) {

    /**
     * Google Font URL
     *
     */
    function careerfy_google_fonts_url($fonts = array(), $subsets = array())
    {
        /* URL */
        $base_url = "//fonts.googleapis.com/css";
        $font_args = array();
        $family = array();

        if (!empty($fonts)) {
            /* Format Each Font Family in Array */
            foreach ($fonts as $font_name => $font_weight) {
                $font_name = str_replace(' ', '+', $font_name);
                if (!empty($font_weight)) {
                    if (is_array($font_weight)) {
                        $font_weight = implode(",", $font_weight);
                    }
                    $family[] = trim($font_name . ':' . urlencode(trim($font_weight)));
                } else {
                    $family[] = trim($font_name);
                }
            }

            /* Only return URL if font family defined. */
            if (!empty($family)) {

                /* Make Font Family a String */
                $family = implode("|", $family);

                /* Add font family in args */
                $font_args['family'] = $family;

                /* Add font subsets in args */
                if (!empty($subsets)) {

                    /* format subsets to string */
                    if (is_array($subsets)) {
                        $subsets = implode(',', $subsets);
                    }

                    $font_args['subset'] = urlencode(trim($subsets));
                }

                return add_query_arg($font_args, $base_url);
            }
        }

        return '';
    }

}

if (!function_exists('careerfy_excerpt')) {
    /*
     * Custom excerpt.
     * @return content
     */
    function careerfy_excerpt($length = '', $read_more = false, $cont = false, $id = '')
    {
        $excerpt = get_the_excerpt();
        if ('' != $id) {
            $excerpt = get_the_excerpt($id);
        }
        if (true === $cont) {
            if ('' == $id) {
                $excerpt = get_the_content();
            } else {
                $excerpt = get_post_field('post_content', $id);
            }
        }

        if ($length > 0) {
            $excerpt = wp_trim_words($excerpt, $length, '...');
        }

        if ($read_more) {
            $excerpt .= '<a class="careerfy-readmore-btn careerfy-color" href="' . esc_url(get_permalink(get_the_ID())) . '">' . esc_html__('Read More', 'careerfy') . '</a>';
        }

        return $excerpt;
    }

}

if (!function_exists('careerfy_dynamic_sidebars')) {

    /**
     * Careerfy Dynamic Sidebars.
     * @generate sidebars
     */
    function careerfy_dynamic_sidebars()
    {
        global $careerfy_framework_options;

        $careerfy_sidebars = isset($careerfy_framework_options['careerfy-themes-sidebars']) ? $careerfy_framework_options['careerfy-themes-sidebars'] : '';
        if (is_array($careerfy_sidebars) && sizeof($careerfy_sidebars) > 0) {
            foreach ($careerfy_sidebars as $sidebar) {
                if ($sidebar != '') {
                    $sidebar_id = urldecode(sanitize_title($sidebar));
                    register_sidebar(array(
                        'name' => $sidebar,
                        'id' => $sidebar_id,
                        'description' => esc_html__('Add widgets here.', 'careerfy'),
                        'before_widget' => '<div id="%1$s" class="widget %2$s">',
                        'after_widget' => '</div>',
                        'before_title' => '<div class="careerfy-widget-title"><h2>',
                        'after_title' => '</h2></div>',
                    ));
                }
            }
        }
    }

    add_action('widgets_init', 'careerfy_dynamic_sidebars');
}

if (!function_exists('careerfy_footer_dynamic_sidebars')) {

    /**
     * Careerfy Footer Dynamic Sidebars.
     * @generate sidebars
     */
    function careerfy_footer_dynamic_sidebars()
    {
        global $careerfy_framework_options;

        $footer_style = isset($careerfy_framework_options['footer-style']) ? $careerfy_framework_options['footer-style'] : '';

        $before_title = '<div class="footer-widget-title"><h2>';
        $after_title = '</h2></div>';

        if ($footer_style == 'style3') {
            $before_title = '<div class="careerfy-footer-title3"><h2>';
            $after_title = '</h2></div>';
        } else if ($footer_style == 'style4') {
            $before_title = '<div class="careerfy-footer-title4"><h2>';
            $after_title = '</h2></div>';
        } else if ($footer_style == 'style18' || $footer_style == 'style19') {
            $before_title = '<div class="careerfy-footer-title-style18"><h2>';
            $after_title = '</h2></div>';
        }

        $before_title = apply_filters('careerfy_footer_sidebars_widget_title_before', $before_title);
        $after_title = apply_filters('careerfy_footer_sidebars_widget_title_after', $after_title);

        $careerfy_sidebars = isset($careerfy_framework_options['careerfy-footer-sidebars']) ? $careerfy_framework_options['careerfy-footer-sidebars'] : '';
        if (isset($careerfy_sidebars['col_width']) && is_array($careerfy_sidebars['col_width']) && sizeof($careerfy_sidebars['col_width']) > 0) {
            $sidebar_counter = 0;
            foreach ($careerfy_sidebars['col_width'] as $sidebar_col) {
                $sidebar = isset($careerfy_sidebars['sidebar_name'][$sidebar_counter]) ? $careerfy_sidebars['sidebar_name'][$sidebar_counter] : '';
                if ($sidebar != '') {
                    $sidebar_id = urldecode(sanitize_title($sidebar));
                    register_sidebar(array(
                        'name' => $sidebar,
                        'id' => $sidebar_id,
                        'description' => esc_html__('Add only one widget here.', 'careerfy'),
                        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                        'after_widget' => '</aside>',
                        'before_title' => $before_title,
                        'after_title' => $after_title,
                    ));
                }
                $sidebar_counter++;
            }
        }
    }

    add_action('widgets_init', 'careerfy_footer_dynamic_sidebars');
}

if (!function_exists('careerfy_custom_str')) {

    /**
     * Careerfy Custom characters strings.
     * @return string
     */
    function careerfy_custom_str($str)
    {
        return $str;
    }

}

if (!function_exists('careerfy_pagination')) {

    /*
     * Pagination.
     * @return markup
     */

    function careerfy_pagination($careerfy_query = '', $return = false)
    {

        global $wp_query;

        $careerfy_big = 999999999; // need an unlikely integer

        $careerfy_cus_query = $wp_query;

        if (!empty($careerfy_query)) {
            $careerfy_cus_query = $careerfy_query;
        }

        $careerfy_pagination = paginate_links(array(
            'base' => str_replace($careerfy_big, '%#%', esc_url(get_pagenum_link($careerfy_big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $careerfy_cus_query->max_num_pages,
            'prev_text' => '<i class="careerfy-icon careerfy-arrows4"></i>',
            'next_text' => '<i class="careerfy-icon careerfy-arrows4"></i>',
            'type' => 'array'
        ));


        if (is_array($careerfy_pagination) && sizeof($careerfy_pagination) > 0) {
            $careerfy_html = '<div class="careerfy-pagination-blog">';
            $careerfy_html .= '<ul>';
            foreach ($careerfy_pagination as $careerfy_link) {
                $prev_item = $next_item = false;
                if (strpos($careerfy_link, 'current') !== false) {
                    $careerfy_html .= '<li class="active"><a>' . preg_replace("/[^0-9]/", "", $careerfy_link) . '</a></li>';
                } else {
                    $dom = new DOMDocument;
                    $dom->loadHTML($careerfy_link, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                    $this_anchor = $dom->getElementsByTagName('a');
                    if ($this_anchor->length > 0) {

                        $a_page_num = $this_anchor->item(0)->nodeValue;
                        if ($this_anchor instanceof DOMNodeList) {
                            foreach ($this_anchor as $this_anch) {
                                if ($this_anch->hasAttribute('class') && false !== strpos($this_anch->getAttribute('class'), 'prev')) {
                                    $a_page_class = str_replace('prev', 'a-prev', $this_anch->getAttribute('class'));
                                    $this_anch->setAttribute('class', $a_page_class);
                                    $prev_item = true;
                                }
                                if ($this_anch->hasAttribute('class') && false !== strpos($this_anch->getAttribute('class'), 'next')) {
                                    $a_page_class = str_replace('next', 'a-next', $this_anch->getAttribute('class'));
                                    $this_anch->setAttribute('class', $a_page_class);
                                    $next_item = true;
                                }
                            }
                        }
                        $careerfy_link = $dom->saveHtml($dom);
                    }
                    if ($prev_item == true) {
                        $careerfy_html .= '<li class="prev">' . $careerfy_link . '</li>';
                    } else if ($next_item == true) {
                        $careerfy_html .= '<li class="next">' . $careerfy_link . '</li>';
                    } else {
                        $careerfy_html .= '<li>' . $careerfy_link . '</li>';
                    }
                }
            }
            $careerfy_html .= '</ul>';

            $careerfy_html .= '</div>';

            if ($return === false) {
                echo($careerfy_html);
            } else {
                return $careerfy_html;
            }
        }
    }

}

if (!function_exists('careerfy_excerpt_more') && !is_admin()) {

    /**
     * Replaces "[...]" (appended to automatically generated excerpts) with ... and a 'Read More' link.
     * @return link
     */
    function careerfy_excerpt_more($more)
    {

        if (post_password_required()) {
            return;
        }

        $link = '...';
        return $link;
    }

    add_filter('excerpt_more', 'careerfy_excerpt_more');
}

if (!function_exists('careerfy_next_prev_custom_links')) {

    /**
     * Next previous links for detail pages
     * @return links
     */
    function careerfy_next_prev_custom_links($post_type = 'post')
    {
        global $post;
        $previd = $nextid = '';

        $next_post = get_next_post();
        if (!empty($next_post)) {
            $nextid = $next_post->ID;
        }
        $prev_post = get_previous_post();
        if (!empty($prev_post)) {
            $previd = $prev_post->ID;
        }

        if (!empty($previd) || !empty($nextid)) {

            if ((isset($previd) && !empty($previd)) || (isset($nextid) && !empty($nextid))) {
                ?>
                <div class="careerfy-prenxt-post">
                    <ul>
                        <li class="careerfy-prenxt-post">
                            <?php
                            if (isset($previd) && !empty($previd)) {
                                $post_thumbnail_id = get_post_thumbnail_id($previd);
                                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
                                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';

                                if ($post_thumbnail_src != '') {
                                    ?>
                                    <figure><img src="<?php echo($post_thumbnail_src) ?>" alt=""></figure>
                                    <?php
                                }
                                ?>
                                <div class="careerfy-prev-post">
                                    <h6>
                                        <a href="<?php echo esc_url(get_permalink($previd)) ?>"><?php echo wp_trim_words(get_the_title($previd), 5, '...') ?></a>
                                    </h6>
                                    <a href="<?php echo esc_url(get_permalink($previd)) ?>"
                                       class="careerfy-arrow-nexpre"><i
                                                class="careerfy-icon careerfy-down-arrow"></i> <?php echo esc_html__('Previous Post', 'careerfy') ?>
                                    </a>
                                </div>
                                <?php
                            }
                            ?>
                        </li>
                        <li class="careerfy-post-next">
                            <?php
                            if (isset($nextid) && !empty($nextid)) {
                                $post_thumbnail_id = get_post_thumbnail_id($nextid);
                                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
                                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';

                                if ($post_thumbnail_src != '') {
                                    ?>
                                    <figure><img src="<?php echo($post_thumbnail_src) ?>" alt=""></figure>
                                    <?php
                                }
                                ?>
                                <div class="careerfy-next-post">
                                    <h6>
                                        <a href="<?php echo esc_url(get_permalink($nextid)) ?>"><?php echo wp_trim_words(get_the_title($nextid), 5, '...') ?></a>
                                    </h6>
                                    <a href="<?php echo esc_url(get_permalink($nextid)) ?>"
                                       class="careerfy-arrow-nexpre"><?php echo esc_html__('Next Post', 'careerfy') ?> <i
                                                class="careerfy-icon careerfy-down-arrow"></i></a>
                                </div>
                                <?php
                            }
                            ?>
                        </li>
                    </ul>
                </div>
                <?php
            }
        }
    }
}

if (function_exists('careerfy_add_param_field')) {

    /**
     * adding multi dropdown param in vc
     * @return markup
     */
    careerfy_add_param_field('careerfy_multi_dropdown', 'careerfy_vc_multi_dropdown_field');

    function careerfy_vc_multi_dropdown_field($settings, $value)
    {
        $dropdown_class = 'wpb_vc_param_value wpb-textinput ' . esc_attr($settings['param_name']) . ' ' . esc_attr($settings['type']) . '_field';
        $dropdown_options = $settings['options'];

        if ($value != '' && !is_array($value)) {
            $value = explode(',', $value);
        }

        $dropdown_html = '
		<select name="' . esc_html($settings['param_name']) . '" class="' . esc_html($dropdown_class) . '" multiple="multiple">';
        foreach ($dropdown_options as $dr_opt_key => $dr_opt_val) {
            $dropdown_html .= '<option' . (is_array($value) && in_array($dr_opt_key, $value) ? ' selected="selected"' : '') . ' value="' . esc_html($dr_opt_key) . '">' . esc_html($dr_opt_val) . '</option>';
        }
        $dropdown_html .= '	
		</select>';
        return $dropdown_html;
    }

    /**
     * adding image browse param in vc
     * @return markup
     */
    careerfy_add_param_field('careerfy_browse_img', 'careerfy_vc_image_browse_field');

    function careerfy_vc_image_browse_field($settings, $value)
    {
        $_class = 'wpb_vc_param_value wpb-textinput ' . esc_attr($settings['param_name']) . ' ' . esc_attr($settings['type']) . '_field';
        $id = esc_attr($settings['param_name']) . rand(1000000, 9999999);
        $image_display = $value == '' ? 'none' : 'block';

        $_html = '
        <div id="' . $id . '-box" class="careerfy-browse-med-image" style="display: ' . $image_display . ';">
            <a class="careerfy-rem-media-b" data-id="' . $id . '"><i class="fa fa-close"></i></a>
            <img id="' . $id . '-img" src="' . $value . '" alt="" />
        </div>';

        $_html .= '<input type="hidden" id="' . $id . '" class="' . esc_html($_class) . '" name="' . esc_attr($settings['param_name']) . '" value="' . $value . '" />';
        $_html .= '<input type="button" class="careerfy-upload-media careerfy-bk-btn" name="' . $id . '" value="' . __('Browse', 'careerfy') . '" />';
        return $_html;
    }
}

function careerfy_wpml_lang_switcher()
{
    if (function_exists('icl_get_languages')) {
        $all_langs = icl_get_languages('skip_missing=&orderby=KEY&order=DIR&link_empty_to=str');
        //var_dump($all_langs);
        if (!empty($all_langs) && count($all_langs) > 1) {
            $current_lang_index = '';
            foreach ($all_langs as $lang_index => $lang_itm_val) {
                if (isset($lang_itm_val['active']) && $lang_itm_val['active'] == 1) {
                    $current_lang_index = $lang_index;
                }
            }
            ?>
            <li>
                <div class="careerfy-wpml-switcher">
                    <?php
                    if ($current_lang_index != '' && isset($all_langs[$current_lang_index])) {
                        $curnt_lng_obj = $all_langs[$current_lang_index];
                        ?>
                        <a href="<?php echo($curnt_lng_obj['url']) ?>" class="current-wpml-lng">
                            <?php
                            if (isset($curnt_lng_obj['country_flag_url']) && $curnt_lng_obj['country_flag_url'] != '') {
                                ?>
                                <img class="wpml-ls-flag" src="<?php echo($curnt_lng_obj['country_flag_url']) ?>"
                                     alt="<?php echo($curnt_lng_obj['code']) ?>"
                                     title="<?php echo($curnt_lng_obj['native_name']) ?>">
                                <?php
                            }
                            ?>
                            <span class="lang-trans-name"><?php echo($curnt_lng_obj['translated_name']) ?></span> <span
                                    class="lang-native-name">(<?php echo($curnt_lng_obj['native_name']) ?>)</span>
                        </a>
                        <?php
                    }
                    ?>
                    <ul>
                        <?php
                        $lang_countr = 0;
                        foreach ($all_langs as $lang_index => $lang_itm) {
                            if ($lang_index == $current_lang_index) {
                                continue;
                            }
                            $lang_act_url = $lang_itm['url'];
                            if ($lang_itm['url'] == 'str' || $lang_itm['url'] == 'str?user-dashboard') {
                                $lang_act_url = get_permalink();
                            }
                            ?>
                            <li>
                                <a href="<?php echo($lang_act_url) ?>" class="wpml-lng-item">
                                    <?php
                                    if (isset($lang_itm['country_flag_url']) && $lang_itm['country_flag_url'] != '') {
                                        ?>
                                        <img class="wpml-ls-flag" src="<?php echo($lang_itm['country_flag_url']) ?>"
                                             alt="<?php echo($lang_itm['code']) ?>"
                                             title="<?php echo($lang_itm['native_name']) ?>">
                                        <?php
                                    }
                                    ?>
                                    <span class="lang-trans-name"><?php echo($lang_itm['translated_name']) ?></span> <span
                                            class="lang-native-name">(<?php echo($lang_itm['native_name']) ?>)</span>
                                </a>
                            </li>
                            <?php
                            $lang_countr++;
                        }
                        ?>
                    </ul>
                </div>
            </li>
            <?php
        }
    }
}
/**
 * @Getting child Comments
 *
 */
if (!function_exists('careerfy_comments')) {

    function careerfy_comments($comment, $args, $depth)
    {
        $GLOBALS['comment'] = $comment;
        global $wpdb;

        $GLOBALS['comment'] = $comment;
        $args['reply_text'] = '<i class="fa fa-share"></i> ' . esc_html__('Reply to this comment', 'careerfy') . '';
        $args['after'] = '';
        $_comment_type = $comment->comment_type;

        switch ($_comment_type) {
            case ($_comment_type == '') :

                $comment_time = strtotime($comment->comment_date);

                $get_author = get_comment_author();
                $author_obj = get_user_by('login', $get_author);
                ?>

                <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
                <div id="comment-<?php comment_ID(); ?>" class="thumblist">
                    <ul>
                        <li>

                            <?php
                            $avatar_link = get_avatar_url($comment, array('size' => 62));
                            if (@getimagesize($avatar_link)) {
                                $avatar_link = $avatar_link;
                            } else {
                                $avatar_link = get_template_directory_uri() . '/images/default_avatar.jpg';
                            }
                            ?>
                            <figure><img src="<?php echo esc_url_raw($avatar_link); ?>" alt=""></figure>
                            <div class="careerfy-comment-text">

                                <h6><a><?php comment_author(); ?></a> -
                                    <span><?php echo date_i18n(get_option('date_format'), strtotime($comment->comment_date)) ?></span>
                                </h6>

                                <?php if ($comment->comment_approved == '0') : ?>
                                    <div class="comment-awaiting-moderation"><?php echo esc_html__('Your comment is awaiting moderation.', 'careerfy'); ?></div>
                                <?php endif; ?>
                                <?php comment_text(); ?>
                                <?php
                                if (function_exists('careerfy_time_elapsed')) {
                                    ?>
                                    <span><?php echo careerfy_time_elapsed($comment_time) ?></span>
                                    <?php
                                }
                                comment_reply_link(array_merge($args, array('depth' => $depth)));
                                ?>
                            </div>
                        </li>
                    </ul>
                </div>
                <?php
                break;
            case ($_comment_type == 'comment') :

                $comment_time = strtotime($comment->comment_date);

                $get_author = get_comment_author();
                $author_obj = get_user_by('login', $get_author);
                ?>

                <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
                <div id="comment-<?php comment_ID(); ?>" class="thumblist">
                    <ul>
                        <li>

                            <?php
                            $avatar_link = get_avatar_url($comment, array('size' => 62));
                            if (@getimagesize($avatar_link)) {
                                $avatar_link = $avatar_link;
                            } else {
                                $avatar_link = get_template_directory_uri() . '/images/default_avatar.jpg';
                            }
                            ?>
                            <figure><img src="<?php echo esc_url_raw($avatar_link); ?>" alt=""></figure>
                            <div class="careerfy-comment-text">

                                <h6><a><?php comment_author(); ?></a> -
                                    <span><?php echo date_i18n(get_option('date_format'), strtotime($comment->comment_date)) ?></span>
                                </h6>

                                <?php if ($comment->comment_approved == '0') : ?>
                                    <div class="comment-awaiting-moderation"><?php echo esc_html__('Your comment is awaiting moderation.', 'careerfy'); ?></div>
                                <?php endif; ?>
                                <?php comment_text(); ?>
                                <?php
                                if (function_exists('careerfy_time_elapsed')) {
                                    ?>
                                    <span><?php echo careerfy_time_elapsed($comment_time) ?></span>
                                    <?php
                                }
                                comment_reply_link(array_merge($args, array('depth' => $depth)));
                                ?>
                            </div>
                        </li>
                    </ul>
                </div>
                <?php
                break;
            case 'pingback' :
            case 'trackback' :
                ?>
                <li class="post pingback">
                <p><?php comment_author_link(); ?><?php edit_comment_link(esc_html__('Edit Comment', 'careerfy'), ' '); ?></p>
                <?php
                break;
        }
    }

}

if (!function_exists('careerfy_password_form')) {

    function careerfy_password_form()
    {
        global $post;
        $label = 'pwbox-' . (empty($post->ID) ? rand() : $post->ID);
        $html = '<form class="post-password-form" action="' . esc_url(site_url('wp-login.php?action=postpass', 'login_post')) . '" method="post">'
            . '<div class="careerfy-protected-content">'
            . '<span>' . esc_html__("Password To view this protected post, enter the password below:", 'careerfy') . '</span>'
            . '<input name="post_password" id="' . esc_html($label) . '" type="password" size="20" maxlength="20" />'
            . '<input type="submit" name="Submit" value="' . esc_attr__("Submit", 'careerfy') . '" />'
            . '</div></form>';
        return $html;
    }

    add_filter('the_password_form', 'careerfy_password_form');
}

if (!function_exists('careerfy_post_detail_author_info')) {

    add_action('careerfy_post_detail_author_info', 'careerfy_post_detail_author_info');

    function careerfy_post_detail_author_info()
    {
        global $post;


        $user_id = get_the_author_meta('ID');

        $avatar_link = get_avatar_url(get_the_author_meta('user_email'), array('size' => 114));

        $author_box_class = 'no-post-thumbnail';
        if (has_post_thumbnail()) {
            $author_box_class = '';
        }
        ?>
        <div class="careerfy-author-detail <?php echo sanitize_html_class($author_box_class) ?>">
            <div class="detail-title"><h2><?php esc_html_e('About the Author', 'careerfy') ?></h2></div>
            <figure>
                <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))) ?>"><img
                            src="<?php echo esc_url_raw($avatar_link); ?>" alt=""></a>
            </figure>
            <div class="detail-content">
                <div class="post-by"><?php esc_html_e('By', 'careerfy') ?> <a
                            href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))) ?>"><?php echo get_the_author() ?></a>
                </div>
                <time datetime="<?php echo date('Y-m-d', strtotime(get_the_date())) ?>"><?php echo get_the_date() ?></time>
                <?php do_action('careerfy_post_acts_btns', $post->ID) ?>
            </div>
            <?php do_action('careerfy_post_author_social_links', $post->ID) ?>
        </div>
        <?php
    }

}

if (!function_exists('careerfy_footer_copyright_translation')) {

    add_action('init', 'careerfy_footer_copyright_translation');

    function careerfy_footer_copyright_translation()
    {
        global $careerfy_framework_options;

        $careerfy_copyright_txt = isset($careerfy_framework_options['careerfy-footer-copyright-text']) ? $careerfy_framework_options['careerfy-footer-copyright-text'] : '';
        if (!empty($careerfy_copyright_txt)) {
            do_action('wpml_register_single_string', 'Careerfy Options', 'Copyright Text - ' . $careerfy_copyright_txt, $careerfy_copyright_txt);
        }
    }

}

if (!function_exists('careerfy_plugupdats_popup_closin_forever')) {

    add_action('wp_ajax_careerfy_plugupdats_popup_closin_forever', 'careerfy_plugupdats_popup_closin_forever');

    function careerfy_plugupdats_popup_closin_forever()
    {
        update_option('careerf_plugupdates_popup_closd', 'yes');
        echo json_encode(array('success' => '1'));
        die;
    }

}

if (!function_exists('careerfy_plugupdats_popup_closin_nextup')) {

    add_action('wp_ajax_careerfy_plugupdats_popup_closin_nextup', 'careerfy_plugupdats_popup_closin_nextup');

    function careerfy_plugupdats_popup_closin_nextup()
    {

        $close_arr = array(
            'closed' => 'untill_next_update',
            'version' => CAREERFY_VERSION
        );
        update_option('careerf_plugupdates_popup_closd', $close_arr);

        echo json_encode(array('success' => '1'));
        die;
    }

}

if (!function_exists('careerfy_latest_plugins_update_admin_notice')) {

    function careerfy_latest_plugins_update_admin_notice()
    {
        $closed_popup = get_option('careerf_plugupdates_popup_closd');

        $show_popup = true;
        if ($closed_popup == 'yes') {
            $show_popup = false;
        }
        if (isset($closed_popup['closed']) && $closed_popup['closed'] == 'untill_next_update') {
            if ($closed_popup['version'] == CAREERFY_VERSION) {
                $show_popup = false;
            }
        }

        $get_page = isset($_GET['page']) ? $_GET['page'] : '';
        $upd_str = array();
        if (class_exists('Careerfy_framework')) {
            if (Careerfy_framework::get_version() != CAREERFY_VERSION) {
                $upd_str[] = __('Careerfy Framework Plugin', 'careerfy');
            }
        }
        if (class_exists('JobSearch_plugin')) {
            if (JobSearch_plugin::get_version() != WP_JOBSEARCH_VERSION) {
                $upd_str[] = __('WP Jobsearch Plugin', 'careerfy');
            }
        }
        if (!empty($upd_str) && $get_page != 'tgmpa-install-plugins' && $show_popup) {
            ?>
            <style>
                .careerfy-modal {
                    position: fixed;
                    top: 0px;
                    left: 0px;
                    width: 100%;
                    height: 100%;
                    z-index: 99999;
                }

                body.careerfy-modal-active {
                    overflow: hidden;
                    -ms-overflow: hidden;
                    padding-right: 16px;
                }

                .careerfy-modal {
                    -webkit-transition: all 0.3s ease-in-out;
                    -moz-transition: all 0.3s ease-in-out;
                    -ms-transition: all 0.3s ease-in-out;
                    -o-transition: all 0.3s ease-in-out;
                    transition: all 0.3s ease-in-out;
                }

                .careerfy-modal.fade {
                    visibility: hidden;
                    -ms-visibility: hidden;
                    opacity: 0;
                }

                .careerfy-modal.fade-in {
                    visibility: visible;
                    -ms-visibility: visible;
                    opacity: 1;
                }

                .careerfy-modal .modal-inner-area {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: #000000;
                    opacity: 0.70;
                    z-index: 9999;
                }

                .careerfy-modal .modal-content-area {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    overflow-y: scroll;
                    -ms-overflow-y: scroll;
                    z-index: 99999;
                    text-align: center;
                    padding-right: 30px;
                }

                .careerfy-modal .modal-box-area {
                    width: 600px;
                    background-color: #ffffff;
                    border-radius: 4px;
                    padding: 30px 30px 30px 30px;
                    position: absolute;
                    margin: -260px 0px 0px -260px;
                    top: 50%;
                    left: 50%;
                }

                .careerfy-modal span.modal-close {
                    cursor: pointer;
                    float: right;
                    color: #d1d1d1;
                    font-size: 19px;
                    line-height: 1;
                    -webkit-transition: all 0.4s ease-in-out;
                    -moz-transition: all 0.4s ease-in-out;
                    -ms-transition: all 0.4s ease-in-out;
                    -o-transition: all 0.4s ease-in-out;
                    transition: all 0.4s ease-in-out;
                }

                .careerfy-modal-title-box {
                    float: left;
                    width: 100%;
                    margin-bottom: 15px;
                }

                .careerfy-modal-title-box h2 {
                    float: left;
                    text-transform: uppercase;
                    font-size: 20px;
                    margin: 0px;
                    line-height: 1;
                }

                .update-plugsbox-con {
                    float: left;
                    width: 100%;
                }

                .update-plugsbox-con > ul {
                    float: left;
                    text-align: left;
                    width: 100%;
                    list-style: disc;
                    margin: 0 0 10px 0;
                    padding: 0 0 0 10px;
                }

                .update-plugsbox-con > li {
                    display: inline-block;
                    width: 100%;
                    margin: 0 0 10px;
                }

                .update-plugsbox-con .no-thanks-btn {
                    display: inline-block;
                    border: 2px solid #0a6cab;
                    padding: 12px 30px 14px 30px;
                    color: #1f2b34;
                    text-decoration: none;
                    text-transform: uppercase;
                    line-height: 1;
                    font-size: 14px;
                    font-weight: 700;
                    border-radius: 3px;
                    margin: 25px 15px 0px 15px;
                    -webkit-transition: all 0.4s ease-in-out;
                    -moz-transition: all 0.4s ease-in-out;
                    -ms-transition: all 0.4s ease-in-out;
                    -o-transition: all 0.4s ease-in-out;
                    transition: all 0.4s ease-in-out;
                }

                .update-plugsbox-con .no-thanks-btn:hover {
                    background-color: #026cb2;
                    color: #ffffff;
                }

                .careerfy-modal-title-box,
                .update-plugsbox-con {
                    display: inline-block;
                    width: 100%;
                    text-align: center;
                }

                .careerfy-modal-title-box h2 {
                    font-size: 30px;
                    color: #ee6565;
                    margin-bottom: 19px;
                    text-transform: capitalize;
                    display: inline-block;
                    width: 65%;
                    float: none;
                    line-height: 1.3;
                }

                .careerfy-modal .careerfy-modal-title-box .modal-close {
                    position: absolute;
                    right: 13px;
                    top: 8px;
                    color: #467ba2;
                    font-size: 25px;
                }

                .careerfy-modal .careerfy-modal-title-box .modal-close i {
                    font-size: 25px;
                }

                .update-plugsbox-con-btn {
                    display: inline-block;
                    text-transform: uppercase;
                    color: #333;
                    text-decoration: none;
                    outline: none;
                    font-size: 17px;
                    font-weight: 600;
                    min-width: 250px;
                    text-align: left;
                    margin-bottom: 10px;
                }

                .update-plugsbox-con-btn:hover {
                    color: #333;
                }

                .update-plugsbox-con-btn i {
                    margin: -3px 14px 0px 0px;
                    color: #026cb2;
                    font-size: 24px;
                    float: left;
                }

                #CareerfyUpdatePluginsNotice .modal-box-area {
                    padding-left: 0px;
                    padding-right: 0px;
                }

                .update-plugsbox-bottom {
                    display: inline-block;
                    width: 100%;
                    border-top: 1px solid #ebeaeb;
                    padding: 40px 30px 5px 30px;
                    margin-top: 30px;
                    box-sizing: border-box;
                }

                .update-plugsbox-bottom span {
                    float: left;
                    font-size: 18px;
                    font-weight: 700;
                    color: #000000;
                }

                .update-plugsbox-bottom span.plugsbox-right-box {
                    float: right;
                }

                .update-plugsbox-bottom span a {
                    color: #036bb2;
                    text-decoration: none;
                }

                .plugsbox-update-button {
                    text-decoration: none;
                    display: inline-block;
                    line-height: 2.7;
                    border: 2px solid #0a6cab;
                    border-radius: 3px;
                    font-size: 14px;
                    font-weight: 700;
                    text-transform: uppercase;
                    padding: 0px 0px 0px 24px;
                    margin-bottom: 10px;
                    -webkit-transition: all 0.4s ease-in-out;
                    -moz-transition: all 0.4s ease-in-out;
                    -ms-transition: all 0.4s ease-in-out;
                    -o-transition: all 0.4s ease-in-out;
                    transition: all 0.4s ease-in-out;
                }

                .plugsbox-update-button i {
                    float: right;
                    padding: 9px 12px;
                    background-color: #024171;
                    color: #ffffff;
                    margin-left: 24px;
                }

                .plugsbox-update-button:hover {
                    background-color: #026cb2;
                    color: #ffffff;
                }

                .permnent-closepop-btns {
                    margin: 10px 0px;
                }

                .permnent-closepop-btns a {
                    display: inline-block;
                    border: 2px solid #ee6565;
                    padding: 12px 30px 14px 30px;
                    color: #ffffff;
                    text-decoration: none;
                    text-transform: capitalize;
                    line-height: 1;
                    font-size: 14px;
                    font-weight: 700;
                    border-radius: 3px;
                    margin: 15px 8px 15px 8px;
                    background-color: #ee6565;
                    -webkit-transition: all 0.4s ease-in-out;
                    -moz-transition: all 0.4s ease-in-out;
                    -ms-transition: all 0.4s ease-in-out;
                    -o-transition: all 0.4s ease-in-out;
                    transition: all 0.4s ease-in-out;
                }

                .permnent-closepop-btns a:hover {
                    background-color: #ffffff;
                    color: #ee6565;
                    border-color: #ee6565;
                }
            </style>
            <div class="careerfy-modal fade" id="CareerfyUpdatePluginsNotice">
                <div class="modal-inner-area">&nbsp;</div>
                <div class="modal-content-area">
                    <div class="modal-box-area">
                        <div class="permnent-closepop-btns">
                            <a href="javascript:void(0);"
                               class="closforvr-updplgns-notice"><?php esc_html_e('Close Forever', 'careerfy'); ?></a>
                            <a href="javascript:void(0);"
                               class="closuntil-updplgns-notice"><?php esc_html_e('Close Until Next Update', 'careerfy'); ?></a>
                        </div>
                        <div class="careerfy-modal-title-box">
                            <h2><?php esc_html_e('Must Update Careerfy Required Plugins!', 'careerfy'); ?></h2>
                            <span class="modal-close"><i class="dashicons dashicons-dismiss"></i></span>
                        </div>
                        <div class="update-plugsbox-con">
                            <?php
                            foreach ($upd_str as $plugin_name) { ?>
                                <a class="update-plugsbox-con-btn"><i
                                            class="dashicons dashicons-thumbs-up"></i> <?php echo($plugin_name) ?></a>
                                <br>
                                <?php } ?>
                            <a href="<?php echo admin_url('themes.php?page=tgmpa-install-plugins') ?>"
                               class="plugsbox-update-button"><?php esc_html_e('Go To Updates', 'careerfy'); ?> <i
                                        class="dashicons dashicons-arrow-right-alt"></i></a>
                            <a href="javascript:void(0);"
                               class="modal-close no-thanks-btn"><?php esc_html_e('No Thanks', 'careerfy'); ?></a>
                        </div>
                        <div class="update-plugsbox-bottom">
                            <span>Contact support: <a href="#">support@eyecix.com</a></span>
                            <span class="plugsbox-right-box">Skype: <a href="#">eyecix</a></span>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function careerfy_modal_popup_open(target) {
                    jQuery('#' + target).removeClass('fade').addClass('fade-in');
                    jQuery('body').addClass('careerfy-modal-active');
                }

                jQuery(document).on('click', '.careerfy-modal .modal-close', function () {
                    jQuery('.careerfy-modal').removeClass('fade-in').addClass('fade');
                    jQuery('body').removeClass('careerfy-modal-active');
                });
                jQuery(document).ready(function () {
                    careerfy_modal_popup_open('CareerfyUpdatePluginsNotice');
                });
                //
                jQuery(document).on('click', '.closforvr-updplgns-notice', function () {
                    var request = jQuery.ajax({
                        url: '<?php echo admin_url('admin-ajax.php') ?>',
                        method: "POST",
                        data: {
                            'type': 'updates_popup_closin',
                            'action': 'careerfy_plugupdats_popup_closin_forever'
                        },
                        dataType: "json"
                    });
                    request.done(function (response) {
                        if (typeof response.success !== 'undefined' && response.success == '1') {
                            console.log('Popup Closed Forever.');
                        }
                    });
                    //
                    request.fail(function (jqXHR, textStatus) {
                    });

                    //
                    jQuery('.careerfy-modal').removeClass('fade-in').addClass('fade');
                    jQuery('body').removeClass('careerfy-modal-active');
                });
                //
                jQuery(document).on('click', '.closuntil-updplgns-notice', function () {
                    var request = jQuery.ajax({
                        url: '<?php echo admin_url('admin-ajax.php') ?>',
                        method: "POST",
                        data: {
                            'type': 'updates_popup_closin',
                            'action': 'careerfy_plugupdats_popup_closin_nextup'
                        },
                        dataType: "json"
                    });
                    request.done(function (response) {
                        if (typeof response.success !== 'undefined' && response.success == '1') {
                            console.log('Popup closed untill next update.');
                        }
                    });
                    //
                    request.fail(function (jqXHR, textStatus) {
                    });

                    //
                    jQuery('.careerfy-modal').removeClass('fade-in').addClass('fade');
                    jQuery('body').removeClass('careerfy-modal-active');
                });
            </script>
            <?php
        }
    }

    add_action('admin_footer', 'careerfy_latest_plugins_update_admin_notice');
}

if (!function_exists('advisor_special_fun')) {

    function advisor_special_fun($data = '')
    {
        return $data;
    }
}