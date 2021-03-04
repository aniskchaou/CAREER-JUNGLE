<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Careerfy
 */
do_action('careerfy_before_single_post_header', get_the_ID());
if (wp_is_mobile()) {
    get_header('mobile');
} else {
    get_header();
}

while (have_posts()) : the_post(); ?>

    <div class="careerfy-main-content">
    <div class="careerfy-main-section careerfy-single-post">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php
                global $subheader_title;

                if ($subheader_title !== true) { ?>
                    <div class="blog-heading"><?php the_title('<h2>', '</h2>'); ?></div>
                <?php } ?>

                <ul class="careerfy-blog-other">
                    <?php
                    $categories_list = get_the_term_list(get_the_ID(), 'category', '<li class="careerfy-post-categories"> <i class="fa fa-tags"></i> ', ', ', '</li>');
                    if ($categories_list) {
                        printf('%1$s', $categories_list);
                    }
                    $comments = esc_html__('Comments', 'careerfy');
                    $comment = esc_html__('Comment', 'careerfy');
                    ?>
                    <li><i class="fa fa-comment"></i>
                        <a href="<?php comments_link(); ?>"><?php echo comments_number('0 ' . $comments . '', '1 ' . $comment . ' ', '% ' . $comments . ''); ?></a>
                    </li>
                </ul>
            </div>
            <?php
            $post_layout = get_post_meta($post->ID, 'careerfy_field_post_layout', true);
            $post_sidebar = get_post_meta($post->ID, 'careerfy_field_post_sidebar', true);
            //
            $full_layout = true;
            if ($post_layout == 'right' || $post_layout == 'left') {
            $full_layout = false;
            $content_class = $post_layout == 'left' ? 'pull-right' : 'pull-left';
            ?>
            <div class="col-md-9 careerfy-content-col <?php echo sanitize_html_class($content_class); ?>">
                <?php
                } else if (is_active_sidebar('sidebar-1') && $post_layout == '') {
                $full_layout = false;
                ?>
                <div class="col-md-9 careerfy-content-col">
                    <?php } else { ?>
                    <div class="col-md-12">
                        <?php
                        }
                        get_template_part('template-parts/content', 'single');
                        ?>
                    </div>
                    <?php
                    // page sidebar
                    $sidebar_class = $post_layout == 'left' ? 'pull-left' : 'pull-right';
                    if ($full_layout !== true) { ?>
                        <aside class="col-md-3 careerfy-sidebar-col <?php echo sanitize_html_class($sidebar_class) ?>">
                            <?php
                            do_action('careerfy_post_detail_author_info');
                            if (is_active_sidebar('sidebar-1') && $post_layout == '') {
                                dynamic_sidebar('sidebar-1');
                            } else if (($post_layout == 'right' || $post_layout == 'left')) {
                                dynamic_sidebar($post_sidebar);
                            }
                            ?>
                        </aside>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div><!-- careerfy-main-content -->

<?php
endwhile; // End of the loop.
get_footer();
        