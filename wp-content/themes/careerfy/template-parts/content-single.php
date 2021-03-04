<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Careerfy
 */
$post_layout = get_post_meta($post->ID, 'careerfy_field_post_layout', true);

$full_layout = true;
if ($post_layout == 'right' || $post_layout == 'left') {
    $full_layout = false;
}
if (is_active_sidebar('sidebar-1') && $post_layout == '') {
    $full_layout = false;
}

if ($full_layout === true) {
    if (has_post_thumbnail()) {
        ?>
        <div class="row">
            <div class="col-md-9 careerfy-content-col">
                <?php
                $post_thumbnail_id = get_post_thumbnail_id(get_the_ID());
                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-img2');
                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
                if ($post_thumbnail_src != '') {
                    ?>
                    <figure class="careerfy-blog-thumb" style="background-image: url('<?php echo esc_url($post_thumbnail_src) ?>');"></figure>
                    <?php
                }
                ?>
            </div>
            <aside class="col-md-3 careerfy-sidebar-col">
                <?php do_action('careerfy_post_detail_author_info'); ?>
            </aside>
        </div>
        <?php
    }
} else {
    if (has_post_thumbnail()) {
        $post_thumbnail_id = get_post_thumbnail_id(get_the_ID());
        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-img2');
        $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
        if ($post_thumbnail_src != '') { ?>
            <figure class="careerfy-blog-thumb" style="background-image: url('<?php echo esc_url($post_thumbnail_src) ?>');"></figure>
            <?php
        }
    }
}
?>

<div class="content-col-wrap">           

    <div class="careerfy-detail-wrap">
        <div class="careerfy-detail-editore">
            <?php
            the_content();
            wp_link_pages(array(
                'before' => '<div class="page-links">' . esc_html__('Pages:', 'careerfy'),
                'after' => '</div>',
            ));
            ?>
        </div>

        <?php
        ob_start();
        if (function_exists('careerfy_social_share')) {
            careerfy_social_share();
        }
        $social_html = ob_get_clean();
        echo '<div class="row">';
        $tags_list = get_the_term_list(get_the_ID(), 'post_tag', '<div class="careerfy-post-tags"><span><i class="fa fa-tag"></i> ' . esc_html__('Tags', 'careerfy') . '</span> ', '', '</div>');
        if ($tags_list) {
            $tags_class = $social_html != '' ? 'col-md-6' : 'col-md-12';
            echo '<div class="' . $tags_class . '">';
            printf('%1$s', $tags_list);
            echo '</div>';
        }
         echo '<div class="col-md-6">';
         if (function_exists('careerfy_social_share')) { ?>
            <div class="involved-social-icone">
                <?php
                careerfy_social_share();
                ?>
            </div>
            <?php
            }
        echo '</div></div>';

        careerfy_next_prev_custom_links();

        $related_posts = get_post_meta(get_the_ID(), 'careerfy_field_related_posts', true);

        if ($related_posts == 'on') {

            $post_cats = wp_get_post_categories($post->ID, array('fields' => 'ids'));
            $args = array(
                'category__in' => $post_cats,
                'posts_per_page' => 2,
                'post__not_in' => array(get_the_ID()),
            );
            $rel_qry = new WP_Query($args);
            if ($rel_qry->have_posts()) {
                ?>
                <div class="careerfy-widgettitle"> <h2><?php esc_html_e('Related', 'careerfy') ?> <span><?php esc_html_e('Posts', 'careerfy') ?></span></h2> </div>

                <div class="careerfy-blog careerfy-related-blog">
                    <ul class="row">
                        <?php
                        while ($rel_qry->have_posts()) : $rel_qry->the_post();
                            global $post;
                            $post_thumbnail_id = get_post_thumbnail_id(get_the_ID());
                            $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-img4');
                            $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
                            $post_views_count = get_post_meta(get_the_ID(), 'careerfy_post_views_count', true);
                            ?>
                            <li class="col-md-12">
                                <figure>
                                    <?php
                                    if ($post_thumbnail_src != '') {
                                        ?>
                                        <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><img src="<?php echo esc_url($post_thumbnail_src) ?>" alt=""></a>
                                        <?php
                                    }
                                    ?>
                                </figure>
                                <div class="careerfy-related-blog-text">
                                    <h2><a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo wp_trim_words(get_the_title(get_the_ID()), 5, '...') ?></a></h2>
                                    <ul class="careerfy-related-blog-option">
                                        <li><i class="fa fa-calendar"></i> <time datetime="<?php echo date('Y-m-d H:i:s', strtotime(get_the_date())) ?>"><?php echo get_the_date(); ?></time></li>
                                        <li><i class="fa fa-comment"></i> <a href="<?php comments_link(); ?>"><?php echo comments_number('0 Comments', '1 Comment', '% Comments'); ?></a></li>
                                        <li><i class="fa fa-eye"></i> <?php echo absint($post_views_count); ?></li>
                                    </ul>
                                    <?php
                                    if (careerfy_excerpt(15)) {
                                        ?>
                                        <p><?php echo careerfy_excerpt(15) ?></p>
                                        <?php
                                    }
                                    $avatar_link = get_avatar_url(get_the_author_meta('ID'), array('size' => 62));
                                    if (@getimagesize($avatar_link)) {
                                        $avatar_link = $avatar_link;
                                    } else {
                                        $avatar_link = get_template_directory_uri() . '/images/default_avatar.jpg';
                                    }
                                    ?>
                                    <div class="post-author"><a class="author-img" href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))) ?>"><img src="<?php echo esc_url_raw($avatar_link); ?>" alt=""></a><a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))) ?>"><?php echo get_the_author() ?></a></div>
                                    <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>" class="careerfy-continue-reading"><?php esc_html_e('Continue Reading', 'careerfy'); ?> <i class="fa fa-angle-right"></i></a>
                                </div>
                            </li>

                            <?php
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </ul>
                </div>
                <?php
            }
        }

// entry footer
        careerfy_entry_footer(false)
        ?>  
        <!-- #post-## -->
    </div>
    <?php
// If comments are open or we have at least one comment, load up the comment template.
    if (comments_open() || get_comments_number()) :
        comments_template();
    endif;
    ?>
</div> <!-- content-col-wrap -->