<?php
/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Careerfy
 */
/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">
    <?php
    // You can start editing here -- including this comment!
    if (have_comments()) : ?>
        <div class="careerfy-comments careerfy-postreviews">
            <div class="careerfy-widgettitle">
                <h2><?php echo esc_html__('Comments', 'careerfy') ?></h2>
            </div>
            <ul>
                <?php
                wp_list_comments(array(
                    'style' => 'ul',
                    'short_ping' => true,
                    'callback' => 'careerfy_comments'
                ));
                ?>
            </ul><!-- .comment-list -->

            <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : // Are there comments to navigate through?  ?>
                <nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
                    <h2 class="screen-reader-text"><?php esc_html_e('Comment navigation', 'careerfy'); ?></h2>
                    <div class="nav-links">

                        <div class="nav-previous"><?php previous_comments_link(esc_html__('Older Comments', 'careerfy')); ?></div>
                        <div class="nav-next"><?php next_comments_link(esc_html__('Newer Comments', 'careerfy')); ?></div>

                    </div><!-- .nav-links -->
                </nav><!-- #comment-nav-below -->

                <?php
            endif; // Check for comment navigation.
            ?>
        </div>
        <?php
    endif; // Check for have_comments().
    // If comments are closed and there are comments, let's leave a little note, shall we?
    if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) :
        ?>

        <p class="no-comments"><?php esc_html_e('Comments are closed.', 'careerfy'); ?></p>
        <?php
    endif;
    ?>

    <div class="comment-respond careerfy-form">
        <?php
        $defaults = array(
            'title_reply' => esc_html__('Leave a Comment', 'careerfy'),
            'title_reply_before' => '<div class="careerfy-widgettitle"><h2>',
            'title_reply_after' => '</h2></div>',
            'comment_notes_before' => '',
            'fields' => apply_filters('comment_form_default_fields', array(
                'author' => '
                <p>
                <label>' . esc_html__('Name', 'careerfy') . '</label>
                <input id="author" name="author" class="nameinput" placeholder="' . esc_html__('Enter Your Name', 'careerfy') . '" required type="text" tabindex="1">
                ' .
                '</p>',
                'email' => '' .
                '<p>
                <label>' . esc_html__('Email', 'careerfy') . '</label>
                <input id="email" name="email" class="emailinput" type="text" placeholder="' . esc_html__('Enter Your Email', 'careerfy') . '" required tabindex="2">
                ' .
                '</p>',

                    )
            ),
            'comment_field' => '<p class="careerfy-full-form">
                <label>' . esc_html__('Comments', 'careerfy') . '</label>
                <textarea id="comment" name="comment" class="commenttextarea" placeholder="' . esc_html__('Type Your Comment', 'careerfy') . '"></textarea>
            </p>'
        );
        comment_form($defaults, get_the_ID());
        ?>
    </div>
</div><!-- #comments -->
