<?php
/**
 * The template for 
 * displaying shop page
 */
if (wp_is_mobile()) {
    get_header('mobile');
} else {
    get_header();
}
$shop_id = wc_get_page_id('shop');

$page_spacing = get_post_meta($shop_id, 'careerfy_field_page_spacing', true);
$page_spacing_class = '';
if ($page_spacing == 'no') {
    $page_spacing_class = ' no-page-spacing';
}

$page_view = get_post_meta($shop_id, 'careerfy_field_page_view', true);
$page_pading_class = $page_view == 'wide' ? 'careerfy-full-wide-page' : '';

$post_layout = get_post_meta($shop_id, 'careerfy_field_post_layout', true);
?>

<div class="careerfy-main-content<?php echo esc_html($page_spacing_class) ?> <?php echo sanitize_html_class($page_pading_class) ?>">
    <?php
    if ($page_view != 'wide') {
        ?>
        <div class="container">
            <?php
        }
        ?>
        <div class="row">
            <?php
            $post_sidebar = get_post_meta($shop_id, 'careerfy_field_post_sidebar', true);

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
        <?php
        if ($page_view != 'wide') {
            ?>
        </div>
        <?php
    }
    ?>
</div><!-- careerfy-main-content -->

<?php get_footer(); ?>