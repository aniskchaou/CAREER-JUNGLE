<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Careerfy
 */
if (wp_is_mobile()) {
    get_header('mobile');
} else {
    get_header();
}

$page_spacing = get_post_meta($post->ID, 'careerfy_field_page_spacing', true);
$page_spacing_class = '';
if ($page_spacing == 'no') {
    $page_spacing_class = ' no-page-spacing';
}

$page_view = get_post_meta($post->ID, 'careerfy_field_page_view', true);
$page_pading_class = $page_view == 'wide' ? 'careerfy-full-wide-page' : '';
?>
<div class="careerfy-main-content<?php echo esc_html($page_spacing_class) ?> <?php echo sanitize_html_class($page_pading_class) ?>"<?php echo apply_filters('careerfy_page_maincon_extr_atts', '', $post->ID) ?>>
    <?php
    $page_layout = get_post_meta($post->ID, 'careerfy_field_post_layout', true);
    if ($page_view != 'wide') {
        ?><div class="container"><?php
        if ($page_layout == 'full') {
            ?><div class="row">
                    <div class="col-md-12"><?php
                    }
                }

                if ($page_layout == 'right' || $page_layout == 'left') {
                    $content_class = $page_layout == 'left' ? 'pull-right' : 'pull-left';
                    if ($page_view != 'wide') {
                        ?><div class="row"><?php
                    }
                    ?><div class="col-md-9 <?php echo sanitize_html_class($content_class); ?>"><?php
                    }

                    if (is_active_sidebar('sidebar-1') && $page_layout == '') {
                        ?><div class="row">
                                <div class="col-md-9"><?php
                                }

                                while (have_posts()) : the_post();
                                    get_template_part('template-parts/content', 'page');
                                    // If comments are open or we have at least one comment, load up the comment template.
                                    if (comments_open() || get_comments_number()) :
                                        comments_template();
                                    endif;

                                endwhile; // End of the loop.

                                if ($page_layout == 'right' || $page_layout == 'left') {
                                    ?></div><?php
                            }
                            if (is_active_sidebar('sidebar-1') && $page_layout == '') {
                                ?></div><?php
                            }

                            // page sidebar
                            get_sidebar();

                            if ($page_layout == 'right' || $page_layout == 'left') {
                                if ($page_view != 'wide') {
                                    ?></div><?php
                            }
                        }
                        if (is_active_sidebar('sidebar-1') && $page_layout == '') {
                            ?></div><?php
                    }

                    if ($page_view != 'wide') { ?>
                </div>
                <?php
                if ($page_layout == 'full') {
                    ?>
                </div>
            </div>
            <?php
        }
    }
    ?>

</div><!-- careerfy-main-content -->

<?php
get_footer();
