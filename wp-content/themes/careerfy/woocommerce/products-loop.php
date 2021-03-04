<?php
/**
 * Shop Products Lisitng
 * 
 */
if (is_shop()) {
    $shop_id = wc_get_page_id('shop');
    if (have_posts()) :

        do_action('woocommerce_before_shop_loop');
        $products_loop_class = 'careerfy-shopgrid-sec';
        if (isset($_GET['view']) && $_GET['view'] == 'list') {
            $products_loop_class = 'careerfy-shopgrid-sec careerfy-shop-list';
        } else {
            $products_loop_class = 'careerfy-shopgrid-sec';
        }
        ?>
        <div class="careerfy-shop <?php echo esc_html($products_loop_class) ?>">
            <ul class="row">
                <?php
                while (have_posts()) : the_post();
                    get_template_part('woocommerce/content', 'product');
                endwhile; // end of the loop. 
                ?>
            </ul>
        </div>
        <?php
        do_action('woocommerce_after_shop_loop');
    endif;
}