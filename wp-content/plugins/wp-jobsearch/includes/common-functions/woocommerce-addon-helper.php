<?php
// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// functions class
class JobSearch_WooAdon_Common_Funcs {

    public function __construct() {

        add_filter('jobsearch_postjob_featpkg_listitm_after', array($this, 'product_itm_after_addon'), 10, 4);
        add_filter('jobsearch_postjob_pkgs_buynew_item_html', array($this, 'product_itm_after_addon'), 10, 4);
    }

    public function product_itm_after_addon($html, $pkg_id, $job_id, $colspan = '3') {

        global $product;

        $pkg_attach_product = get_post_meta($pkg_id, 'jobsearch_package_product', true);

        $package_product_obj = $pkg_attach_product != '' ? get_page_by_path($pkg_attach_product, 'OBJECT', 'product') : '';

        $product_id = 0;
        if ($pkg_attach_product != '' && is_object($package_product_obj) && isset($package_product_obj->ID)) {
            $product_id = $package_product_obj->ID;
        }
        
        if (!class_exists('WooCommerce')) {
            return $html;
        }
        if (!class_exists('WC_Product_Addons_Helper')) {
            return $html;
        }
        
        ob_start();

        if ($product_id > 0) {
            $product = wc_get_product($product_id);

            $wc_addon_display = new WC_Product_Addons_Display;

            $product_addons = WC_Product_Addons_Helper::get_product_addons($product_id, false);

            if (is_array($product_addons) && count($product_addons) > 0) {
                ?>
                <tr class="wc-prodaddon-info" style="display: none;">
                    <td colspan="<?php echo ($colspan) ?>">
                        <?php
                        do_action( 'woocommerce_product_addons_start', $product_id );

                        foreach ($product_addons as $addon) {
                            if (!isset($addon['field_name'])) {
                                continue;
                            }
                            $this->addon_start($addon);

                            echo $wc_addon_display->get_addon_html($addon);

                            $this->addon_end($addon);
                        }

                        do_action( 'woocommerce_product_addons_end', $product_id );
                        ?>
                    </td>
                </tr>
                <?php
            }
        }
        $html .= ob_get_clean();

        return $html;
    }

    public function addon_start($addon) {
        global $product;

        $price_display = '';
        $name = !empty($addon['name']) ? $addon['name'] : '';
        $description = !empty($addon['description']) ? $addon['description'] : '';
        $title_format = !empty($addon['title_format']) ? $addon['title_format'] : '';
        $addon_type = !empty($addon['type']) ? $addon['type'] : '';
        $addon_price = !empty($addon['price']) ? $addon['price'] : '';
        $addon_price_type = !empty($addon['price_type']) ? $addon['price_type'] : '';
        $adjust_price = !empty($addon['adjust_price']) ? $addon['adjust_price'] : '';
        $required = WC_Product_Addons_Helper::is_addon_required($addon);
        $has_per_person_pricing = ( isset($addon['wc_booking_person_qty_multiplier']) && 1 === $addon['wc_booking_person_qty_multiplier'] ) ? true : false;
        $has_per_block_pricing = ( isset($addon['wc_booking_block_qty_multiplier']) && 1 === $addon['wc_booking_block_qty_multiplier'] ) ? true : false;
        $product_title = WC_Product_Addons_Helper::is_wc_gte('3.0') ? $product->get_name() : $product->post_title;

        if ('checkbox' !== $addon_type && 'multiple_choice' !== $addon_type && 'custom_price' !== $addon_type) {
            $price_prefix = 0 < $addon_price ? '+' : '';
            $price_type = $addon_price_type;
            $adjust_price = $adjust_price;
            $price_raw = apply_filters('woocommerce_product_addons_price_raw', $addon_price, $addon);
            $required = '1' == $required;

            if ('percentage_based' === $price_type) {
                $price_display = apply_filters('woocommerce_product_addons_price', '1' == $adjust_price && $price_raw ? '(' . $price_prefix . $price_raw . '%)' : '', $addon, 0, $addon_type
                );
            } else {
                $price_display = apply_filters('woocommerce_product_addons_price', '1' == $adjust_price && $price_raw ? '(' . $price_prefix . wc_price(WC_Product_Addons_Helper::get_product_addon_price_for_display($price_raw)) . ')' : '', $addon, 0, $addon_type
                );
            }
        }
        ?>

        <div class="wc-pao-addon-container <?php echo $required ? 'wc-pao-required-addon' : ''; ?> wc-pao-addon wc-pao-addon-<?php echo sanitize_title($name); ?>" data-product-name="<?php echo esc_attr($product_title); ?>">

            <?php do_action('wc_product_addon_start', $addon); ?>

            <?php
            if ($name) {
                if ('heading' === $addon_type) {
                    ?>
                    <h3 class="wc-pao-addon-heading"><?php echo wptexturize($name); ?></h3>
                    <?php
                } else {
                    switch ($title_format) {
                        case 'heading':
                            ?>
                            <h3 class="wc-pao-addon-name" data-addon-name="<?php echo esc_attr(wptexturize($name)); ?>" data-has-per-person-pricing="<?php echo esc_attr($has_per_person_pricing); ?>" data-has-per-block-pricing="<?php echo esc_attr($has_per_block_pricing); ?>"><?php echo wptexturize($name); ?> <?php echo $required ? '<em class="required" title="' . __('Required field', 'woocommerce-product-addons') . '">*</em>&nbsp;' : ''; ?><?php echo wp_kses_post($price_display); ?></h3>
                            <?php
                            break;
                        case 'hide':
                            ?>
                            <label class="wc-pao-addon-name" data-addon-name="<?php echo esc_attr(wptexturize($name)); ?>" data-has-per-person-pricing="<?php echo esc_attr($has_per_person_pricing); ?>" data-has-per-block-pricing="<?php echo esc_attr($has_per_block_pricing); ?>" style="display:none;"></label>
                            <?php
                            break;
                        case 'label':
                        default:
                            ?>
                            <label class="wc-pao-addon-name" data-addon-name="<?php echo esc_attr(wptexturize($name)); ?>" data-has-per-person-pricing="<?php echo esc_attr($has_per_person_pricing); ?>" data-has-per-block-pricing="<?php echo esc_attr($has_per_block_pricing); ?>"><?php echo wptexturize($name); ?> <?php echo $required ? '<em class="required" title="' . __('Required field', 'woocommerce-product-addons') . '">*</em>&nbsp;' : ''; ?><?php echo wp_kses_post($price_display); ?></label>
                            <?php
                            break;
                    }
                }
            }
            ?>

            <?php if ($description) { ?>
                <?php echo '<div class="wc-pao-addon-description">' . wpautop(wptexturize($description)) . '</div>'; ?>
            <?php }; ?>

            <?php
            do_action('wc_product_addon_options', $addon);
        }

        public function addon_end($addon) {
            do_action('wc_product_addon_end', $addon);
            ?>

            <div class="clear"></div>
        </div>
        <?php
    }

}

return new JobSearch_WooAdon_Common_Funcs();
