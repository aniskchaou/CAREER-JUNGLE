<?php
/**
 * The template for displaying 
 * WooCommerace Products
 */
if (wp_is_mobile()) {
    get_header('mobile');
} else {
    get_header();
}
$shop_id = wc_get_page_id('shop');

if (is_shop()) {
    get_template_part('woocommerce/woocommerce', 'shop');
} 
else if (is_single()) {
    get_template_part('woocommerce/woocommerce-single-product', 'page');
}
else if (is_product_category() || is_product_tag()) {

    // Shop Taxonomies pages
    get_template_part('woocommerce/woocommerce-archive', 'page');
} else {
    // Shop Other Pages
    ?>
    <div class="careerfy-shop-wrap">
        <?php 
        if (function_exists('woocommerce_content')) {
            woocommerce_content();
        } 
        ?>
    </div>
    <?php
}
get_footer();
