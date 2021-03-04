<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Careerfy
 */
?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php
    $page_title = get_post_meta($post->ID, 'careerfy_field_page_title_switch', true);
    if ($page_title != 'no') {
        ?>
        <header class="careerfy-entry-header">
            <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
        </header><!-- .careerfy-entry-header -->
    <?php } ?>

    <div class="careerfy-entry-content">
        <?php
        the_content();

        wp_link_pages(array(
            'before' => '<div class="page-links">' . esc_html__('Pages:', 'careerfy'),
            'after' => '</div>',
        ));
        ?>
    </div><!-- .careerfy-entry-content -->

    <?php if (is_user_logged_in() && current_user_can('administrator') && get_edit_post_link()) : ?>
        <footer class="careerfy-entry-footer">
            <?php
            edit_post_link(
                sprintf(
                /* translators: %s: Name of current post */
                    esc_html__('Edit %s', 'careerfy'), the_title('<span class="screen-reader-text">"', '"</span>', false)
                ), '<span class="edit-link">', '</span>'
            );
            ?>
        </footer><!-- .careerfy-entry-footer -->
    <?php endif; ?>
</div><!-- #post-## -->
