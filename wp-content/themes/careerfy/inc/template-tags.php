<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Careerfy
 */

if ( ! function_exists( 'careerfy_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function careerfy_posted_on() {
	?>
	<ul class="careerfy-post-meta-options">
        <li>
            <i aria-hidden="true" class="fa fa-calendar"></i>
            <time datetime="<?php echo esc_html(date('Y-m-d', strtotime(get_the_date()))) ?>"><?php echo esc_html( get_the_date() )?></time>
        </li>
        <li>
            <?php esc_html_e('by', 'careerfy') ?>
            <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) ?>"><?php echo get_the_author() ?></a>
        </li>
        <?php
		$categories_list = get_the_term_list(get_the_ID(), 'category', '<li>' . esc_html__('in ', 'careerfy'), ', ', '</li>');
		if ($categories_list) {
			printf('%1$s', $categories_list);
		}
		?>
    </ul>
    <?php
}
endif;

if ( ! function_exists( 'careerfy_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function careerfy_entry_footer($tags = true) {
	// Hide category and tag text for pages.
	?>
	<footer class="careerfy-post-footer">
	<?php 
	if ( true === $tags ) {
		if ( 'post' === get_post_type() ) {
			
			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html__( ', ', 'careerfy' ) );
			if ( $tags_list ) {
				printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'careerfy' ) . '</span>', $tags_list ); // WPCS: XSS OK.
			}
		}
	
		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			?><span class="comments-link"><?php 
				comments_popup_link('<i class="fa fa-comments"></i> ' . get_comments_number(), '<i class="fa fa-comments"></i> ' . get_comments_number(), '<i class="fa fa-comments"></i> ' . get_comments_number(), 'careerfy-colorhover');
			?></span><?php 
		}
	}

	edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			esc_html__( 'Edit %s', 'careerfy' ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		),
		'<span class="edit-link">',
		'</span>'
	);
	
	?></footer><?php 
}
endif;
