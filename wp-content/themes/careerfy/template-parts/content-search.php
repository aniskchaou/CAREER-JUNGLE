<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Careerfy
 */
?>
<li class="col-md-12">
    <?php
    $post_thumbnail_id = get_post_thumbnail_id(get_the_ID());
    $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-img1');
    $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
    $careerfy_excerpt = careerfy_excerpt();
    if ($post_thumbnail_src) { ?>
        <figure>
            <a title="<?php echo esc_html(get_the_title(get_the_ID())); ?>"
               href="<?php echo esc_url(get_permalink(get_the_ID())) ?>">
                <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">
            </a>
        </figure>
        <?php } ?>
    <div class="careerfy-result-text">
        <h5>
            <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo wp_trim_words(get_the_title(get_the_ID()), 7, '...'); ?></a>
            <?php if (is_sticky(get_the_ID())) { ?>
                <span class="careerfy-featured-post"><?php esc_html_e('Featured Post', 'careerfy') ?></span>
            <?php } ?>
        </h5>
        <p><?php echo apply_filters('the_content', $careerfy_excerpt); ?></p>
        <a class="careerfy-banner-btn"
           href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php esc_html_e('read more', 'careerfy'); ?></a>
    </div>
</li>