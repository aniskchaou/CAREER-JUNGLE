<?php
/**
 * The template for displaying product content within loops.
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

global $product, $woocommerce_loop;
$prod_view = '';

// Store loop count we're currently on
if (empty($woocommerce_loop['loop']))
    $woocommerce_loop['loop'] = 0;

if (isset($_GET['view']) && $_GET['view'] == 'list') {
    $prod_view = 'list';
}

// Store column count for displaying the grid
if (isset($woocommerce_loop['columns']) && $woocommerce_loop['columns'] == 'list') {
    $prod_view = 'list';
}

// Ensure visibility
if (!$product || !$product->is_visible())
    return;

// Increase loop count
$woocommerce_loop['loop'] ++;

// Extra post classes
if ($prod_view == 'list') {
    $classes = array('col-md-12');
} else {
    $classes = array('col-md-4');
}

$careerfy_prod_attach_id = get_post_thumbnail_id(get_the_id());
?>
<li class="product <?php echo implode(' ', $classes) ?>">

    <?php
    if ($prod_view == 'list') {
        $careerfy_prod_attach_src = wp_get_attachment_image_src($careerfy_prod_attach_id, 'careerfy-pimg6');
        $post_thumbnail_src = isset($careerfy_prod_attach_src[0]) && esc_url($careerfy_prod_attach_src[0]) != '' ? $careerfy_prod_attach_src[0] : '';
        ?>
    
        <div class="careerfy-shop-grid">
            <figure>
                <?php
                if ($post_thumbnail_src != '') {
                    ?>
                    <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="<?php esc_html(the_title()) ?>"></a> 
                    <?php
                } else {
                    ?> 
                    <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo wc_placeholder_img() ?> </a> 
                    <?php
                }
                if ($product->is_on_sale()) :
                    echo apply_filters('woocommerce_sale_flash', '<span class="careerfy-shop-label"><small>' . esc_html__('Sale!', 'careerfy') . '</small></span>', $post, $product);
                endif;
                ?> 
            </figure>
            <div class="careerfy-grid-info">
                <h6><a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo esc_html(get_the_title(get_the_ID())) ?></a></h6>
                <p><?php echo careerfy_excerpt(25) ?></p>
                <div class="careerfy-cart-button">
                    <?php echo woocommerce_template_loop_price() ?>
                    <span><?php careerfy_loop_add_to_cart('default', esc_html__('Add To Cart', 'careerfy')); ?></span>
                </div>
            </div>
        </div>
    
        <?php
    } else {
        $careerfy_prod_attach_src = wp_get_attachment_image_src($careerfy_prod_attach_id, 'careerfy-pimg3');
        $post_thumbnail_src = isset($careerfy_prod_attach_src[0]) && esc_url($careerfy_prod_attach_src[0]) != '' ? $careerfy_prod_attach_src[0] : '';
        ?>
    
        <div class="careerfy-shop-grid">
            <figure>
                <?php if ($post_thumbnail_src != '') {
                ?><a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="<?php esc_html(the_title()) ?>"></a>
                <?php } else {
                    ?> 
                    <a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo wc_placeholder_img() ?> </a> 
                    <?php
                }
                ?>
            </figure>
            <?php
            if ($product->is_on_sale()) :
                echo apply_filters('woocommerce_sale_flash', '<span class="careerfy-shop-label"><small>' . esc_html__('Sale!', 'careerfy') . '</small></span>', $post, $product);
            endif;
            ?>
            <div class="careerfy-grid-info">
                <h6><a href="<?php echo esc_url(get_permalink(get_the_ID())) ?>"><?php echo esc_html(get_the_title(get_the_ID())); ?></a></h6>
                <?php echo woocommerce_template_loop_price() ?>
                <div class="careerfy-cart-button">
                    <span><?php careerfy_loop_add_to_cart('default', esc_html__('Add To Cart', 'careerfy')); ?></span>
                </div>
            </div>
        </div>   
    
        <?php
    }
    ?>
</li>
