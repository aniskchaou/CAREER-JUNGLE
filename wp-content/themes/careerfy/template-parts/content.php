<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Careerfy
 */
$post_thumbnail_id = get_post_thumbnail_id(get_the_ID());
$post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'medium');
$post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';

$no_img_clas = 'no-img-item';
if ($post_thumbnail_src != '') {
    $no_img_clas = '';
}

?>
<li <?php echo post_class('col-md-12 ' . $no_img_clas) ?>>

    <div class="careerfy-post-item">
        <div class="careerfy-post-wrap">
            <?php
            $no_img_clas = 'with-no-img';
            if ($post_thumbnail_src != '') {
                $no_img_clas = '';
                ?>
                <figure>
                    <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><img src="<?php echo esc_url($post_thumbnail_src) ?>" alt=""> </a>
                </figure>
                <?php
            }
            ?>
            <div class="careerfy-result-text <?php echo sanitize_html_class($no_img_clas) ?>">
                <h5>
                    <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo wp_trim_words(get_the_title(get_the_ID()), 7, '...') ?></a>
                    <?php
                    if (is_sticky(get_the_ID())) {
                        ?>
                        <span class="careerfy-featured-post"><i class="fa fa-star"></i></span> 
                        <?php
                    }
                    ?>
                </h5>
                <ul class="careerfy-archive-options">
                    <li>
                        <i class="fa fa-user"></i> 
                        <a class="careerfy-authorpost" href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))) ?>"> <?php echo get_the_author() ?></a>
                    </li>
                    <?php
                    $categories_list = get_the_term_list(get_the_ID(), 'category', '<li class="careerfy-post-categories"> <i class="fa fa-tags"></i> ', ', ', '</li>');
                    if ($categories_list) {
                        printf('%1$s', $categories_list);
                    }
                    $comments = esc_html__('0 Comments', 'careerfy');
                    $comment = esc_html__('1 Comment', 'careerfy');
                    $comment_text = esc_html__('Comments', 'careerfy');

                    ?>
                    <li><i class="fa fa-calendar"></i> <time datetime="<?php echo date('Y-m-d H:i', strtotime(get_the_date())) ?>"><?php echo get_the_date() ?></time></li>
                    <li><i class="fa fa-comment"></i> <a href="<?php comments_link(); ?>"><?php echo comments_number($comments, $comment, '%'.$comment_text); ?></a></li>
                </ul>
                <?php
                if (careerfy_excerpt(12)) {
                    ?>
                    <p><?php echo careerfy_excerpt(12) ?></p>
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
        </div>
    </div>

</li>
