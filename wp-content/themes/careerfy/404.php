<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Careerfy
 */
if (wp_is_mobile()) {
    get_header('mobile');
} else {
    get_header();
}
?>
<div class="careerfy-main-content">
    <div class="careerfy-main-section">

        <?php
        $allowed_tags = array(
            'a' => array(
                'href' => array(),
                'title' => array(),
            )
        );
        ?>
        <div class="careerfy-errorpage-bg">
            <span class="careerfy-errorpage-transparent"></span>
                
                    <div class="container">
                        <div class="careerfy-errorpage">
                            <img src="<?php echo get_template_directory_uri() ?>/images/error-text.png" alt="">
                            <h2><?php esc_html_e('Ooops! Page Not Found!', 'careerfy'); ?></h2>
                            <p><?php esc_html_e('Sorry !  We could not Find What You Are Looking For, Try With New Search', 'careerfy'); ?></p>
                            <div class="careerfy-error-search"><?php echo get_search_form() ?></div>
                            <a href="<?php echo esc_url(home_url('/')) ?>"><span><?php esc_html_e('back To homepage', 'careerfy'); ?></span></a>
                        </div>
                    </div>
                				
            
        </div>

    </div>  
</div>
<?php
get_footer();
