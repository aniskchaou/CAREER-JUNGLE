<?php
/**
 * check if the plugin is enabled, 
 * otherwise stop the script
 */
if (!class_exists('WooCommerce')) {
    return false;
}

add_filter('woocommerce_enqueue_styles', '__return_false');

/**
 * @Woocommerce Support Theme
 *
 */
add_theme_support('woocommerce');

//add_filter('woocommerce_enqueue_styles', '__return_false');

if (!function_exists('careerfy_child_manage_woocommerce_styles')) {

    add_action('wp_enqueue_scripts', 'careerfy_child_manage_woocommerce_styles', 99);

    function careerfy_child_manage_woocommerce_styles() {
        //remove generator meta tag
        remove_action('wp_head', array($GLOBALS['woocommerce'], 'generator'));
        //first check that woo exists to prevent fatal errors
        if (function_exists('is_woocommerce')) {
            //dequeue scripts and styles
            if (!is_woocommerce() && !is_cart() && !is_checkout() && !is_shop()) {
                wp_dequeue_script('wc_price_slider');
                wp_dequeue_script('wc-single-product');
                wp_dequeue_script('wc-cart-fragments');
                wp_dequeue_script('wc-checkout');
                wp_dequeue_script('wc-add-to-cart-variation');
                wp_dequeue_script('wc-single-product');
                wp_dequeue_script('wc-chosen');
                wp_dequeue_script('prettyPhoto');
                wp_dequeue_script('prettyPhoto-init');
                wp_dequeue_script('jquery-placeholder');
                wp_dequeue_script('fancybox');
                wp_dequeue_script('jqueryui');
            }
        }
    }

}
/**
 * @Remove Woocommerce Default
 * @Remove Sidebar
 * @Breadcrumb
 *
 */
if (!function_exists('careerfy_shop_title')) {

    function careerfy_shop_title() {
        $title = '';
        return $title;
    }

    add_filter('woocommerce_show_page_title', 'careerfy_shop_title');
}

remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);

/**
 * @Removing Shop Default Title
 *
 */
if (!function_exists('careerfy_woocommerce_shop_title')) {

    function careerfy_woocommerce_shop_title() {
        $careerfy_shop_title = '';
        return $careerfy_shop_title;
    }

    add_filter('woocommerce_show_page_title', 'careerfy_woocommerce_shop_title');
}


/**
 * @Adding Add to Cart
 * @ Custom Text
 *
 */
if (!function_exists('careerfy_loop_add_to_cart')) {

    function careerfy_loop_add_to_cart($view = 'default', $btn_text = '<i class="careerfy-icon-shopping-cart"></i>') {
        global $product;
        if ($view == 'default') {
            echo apply_filters('woocommerce_loop_add_to_cart_link', sprintf('<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="product_type_simple careerfy-cart-btn add_to_cart_button ajax_add_to_cart %s product_type_%s">%s</a>', esc_url($product->add_to_cart_url()), esc_attr($product->get_id()), esc_attr($product->get_sku()), esc_attr(isset($quantity) ? $quantity : 1 ), $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '', esc_attr($product->get_type()), '<i class="fa fa-shopping-cart" aria-hidden="true"></i> <span>' . $btn_text . '</span>'), $product);
        } else {
            echo apply_filters('woocommerce_loop_add_to_cart_link', sprintf('<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="product_type_simple add_to_cart_button ajax_add_to_cart %s product_type_%s">%s</a>', esc_url($product->add_to_cart_url()), esc_attr($product->get_id()), esc_attr($product->get_sku()), esc_attr(isset($quantity) ? $quantity : 1 ), $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '', esc_attr($product->get_type()), '<i class="fa fa-shopping-cart" aria-hidden="true"></i> <span>' . $btn_text . '</span>'), $product);
        }
    }

}

if (!function_exists('careerfy_woocommerce_before_shop_loop')) {

    add_action('woocommerce_before_shop_loop', 'careerfy_woocommerce_before_shop_loop', 10);

    function careerfy_woocommerce_before_shop_loop() {
        ?><div class="careerfy-shop-wrap"><?php
        }

    }

    if (!function_exists('careerfy_woocommerce_after_shop_loop')) {

        add_action('woocommerce_after_shop_loop', 'careerfy_woocommerce_after_shop_loop', 10);

        function careerfy_woocommerce_after_shop_loop() {
            ?></div><?php
    }

}

if (!function_exists('careerfy_product_img_placeholder_change')) {
    add_filter('woocommerce_placeholder_img', 'careerfy_product_img_placeholder_change');

    function careerfy_product_img_placeholder_change($image) {
        $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $image);
        return $html;
    }

}

/**
 * @Removing Product Image Dimensions
 */
if (!function_exists('careerfy_remove_thumbnail_dimensions')) {

    add_filter('post_thumbnail_html', 'careerfy_remove_thumbnail_dimensions', 10, 3);

    function careerfy_remove_thumbnail_dimensions($html, $post_id, $post_image_id) {
        $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
        return $html;
    }

}
