<?php
/**
 * Show options for ordering
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/orderby.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$shop_id = wc_get_page_id('shop'); 
?>
<div class="careerfy-right-section">
    <div class="careerfy-select-form">
        <form class="woocommerce-ordering" method="get">
            <select name="orderby" class="orderby">
                <?php foreach ($catalog_orderby_options as $id => $name) : ?>
                    <option value="<?php echo esc_attr($id); ?>" <?php selected($orderby, $id); ?>><?php echo esc_html($name); ?></option>
                <?php endforeach; ?>
            </select>
            <?php wc_query_string_form_fields(null, array('orderby', 'submit')); ?>
        </form>
    </div>
    <ul>
        <li><a href="<?php echo add_query_arg(array('view' => 'grid'), get_permalink($shop_id)) ?>" class="<?php echo (isset($_GET['view']) && $_GET['view'] == 'grid') ? ' active' : '' ?>"><i class="careerfy-icon careerfy-squares"></i></a></li>
        <li><a href="<?php echo add_query_arg(array('view' => 'list'), get_permalink($shop_id)) ?>" class="<?php echo (isset($_GET['view']) && $_GET['view'] == 'list') ? ' active' : '' ?>"><i class="careerfy-icon careerfy-list"></i></a></li>
    </ul>
</div>
</div>