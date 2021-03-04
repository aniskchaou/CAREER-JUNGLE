<?php
/**
 * The template for 
 * product detail
 */
if (wp_is_mobile()) {
    get_header('mobile');
} else {
    get_header();
}
?>

<div class="careerfy-main-content">
    <div class="container">
        <div class="row">

            <?php
            $careerfy__options = careerfy_framework_options();
            $post_layout = isset($careerfy__options['careerfy-wooc-layout']) ? $careerfy__options['careerfy-wooc-layout'] : '';
            $post_sidebar = isset($careerfy__options['careerfy-wooc-sidebar']) ? $careerfy__options['careerfy-wooc-sidebar'] : '';

            $col_class = 'col-md-12';
            if (is_active_sidebar($post_sidebar) && ( $post_layout == 'right' || $post_layout == 'left' )) {
                $content_class = $post_layout == 'left' ? 'pull-right' : 'pull-left';
                $col_class = 'col-md-9 ' . $content_class;
            }
            ?>

            <div class="<?php echo esc_html($col_class) ?>">
                <?php
                if (function_exists('woocommerce_content')) {
                    woocommerce_content();
                }
                ?>
            </div>

            <?php
            if (is_active_sidebar($post_sidebar) && ( $post_layout == 'right' || $post_layout == 'left' )) {
                $sidebar_class = $post_layout == 'left' ? 'pull-left' : 'pull-right';
                ?>
                <aside class="col-md-3 <?php echo sanitize_html_class($sidebar_class) ?>">
                    <?php dynamic_sidebar($post_sidebar); ?>
                </aside>
                <?php
            }
            ?>

        </div><!-- row -->
    </div><!-- container -->
</div><!-- careerfy-main-content -->

<?php get_footer(); ?>