<?php
/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Careerfy
 */
?>
<!--// Search Result \\-->


<?php if (is_home() && current_user_can('publish_posts')) : ?>

    <p><?php printf(wp_kses(__('Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'careerfy'), array('a' => array('href' => array()))), esc_url(admin_url('post-new.php'))); ?></p>
<?php else :
    ?>
    <div class="careerfy-showing-result-title careerfy-search-title"><h2><?php printf(esc_html__('No pages were found containing "%s"', 'careerfy'), get_search_query()); ?></h2></div>
    <ul class="careerfy-no-search-result">
        <li><i class="careerfy-icon-arrows7"></i> <?php esc_html_e('Make sure all words are spelled correctly', 'careerfy'); ?></li>
        <li><i class="careerfy-icon-arrows7"></i> <?php esc_html_e('Wildcard searches (using the Asterisk *) are not supported', 'careerfy'); ?></li>
        <li><i class="careerfy-icon-arrows7"></i> <?php esc_html_e('Try more general keywords, especially if you are attempting a name', 'careerfy'); ?></li>
    </ul>
    <div class="careerfy-form-result">
        <div class="careerfy-showing-result-title"><h2><?php esc_html_e('Perhaps try another search:', 'careerfy'); ?></h2></div>
        <?php echo get_search_form() ?>
    </div> 
<?php 
endif;
