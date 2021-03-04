<?php

/*
  Class : Woocommerce_Checkout
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

if (!class_exists('WooCommerce')) {
    return false;
}

// main plugin class
class Jobsearch_Woocommerce_Checkout {

    // hook things up
    public function __construct() {
        add_filter('jobsearch_price_format', array($this, 'price_amount_format'), 10, 1);
        add_action('jobsearch_woocommerce_payment_checkout', array($this, 'post_prod_payment_checkout'), 10, 4);
        add_action('woocommerce_checkout_order_processed', array($this, 'woocommerce_after_checkout_process'));
        add_action('woocommerce_order_status_completed', array($this, 'woocommerce_order_status_complete'));
        
        add_action('woocommerce_thankyou', array($this, 'thankyou_redirectcustom'), 5);
    }

    public function thankyou_redirectcustom($order_id) {
        global $jobsearch_plugin_options;
        $order_obj = wc_get_order($order_id);
        $order_attach_with = get_post_meta($order_id, 'jobsearch_order_attach_with', true);
        if ($order_attach_with == 'package') {
            $payment_method = $order_obj->get_payment_method();
            if ($payment_method != 'bacs' && $payment_method != 'cod') {
                $order_job_id = get_post_meta($order_id, 'jobsearch_order_attach_job_id', true);
                $page_id = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
                $page_id = jobsearch__get_post_id($page_id, 'page');
                if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                    $page_id = icl_object_id($page_id, 'page', false, $lang_code);
                }
                if ($order_job_id > 0 && get_post_type($order_job_id) == 'job') {
                    $url = add_query_arg(array('tab' => 'user-job', 'job_id' => $order_job_id, 'step' => 'confirm', 'action' => 'update'), get_permalink($page_id));
                    if ( ! $order_obj->has_status( 'failed' ) ) {
                        wp_safe_redirect( $url );
                        exit;
                    }
                } else {
                    $url = add_query_arg(array('tab' => 'user-transactions'), get_permalink($page_id));
                    if ( ! $order_obj->has_status( 'failed' ) ) {
                        wp_safe_redirect( $url );
                        exit;
                    }
                }
            }
        }
    }

    public function price_amount_format($price = 0) {
        if ($price > 0) {
            $price = $price;
        } else {
            $price = '0.00';
        }
        $price = (float) $price;
        return $price;
    }

    public function post_prod_payment_checkout($post_id, $return_type = 'redirect', $job_id = 0, $all_clear = true) {

        global $woocommerce;

        $post_type = get_post_type($post_id);
        if ($post_type == 'package') {
            $product_name = get_post_meta($post_id, 'jobsearch_package_product', true);
        }

        if (isset($product_name) && $product_name != '') {
            $product_obj = get_page_by_path($product_name, 'OBJECT', 'product');
            if (is_object($product_obj)) {
                $product_id = $product_obj->ID;

                // if job id exist
                if ($job_id > 0) {
                    setcookie('wc_checkout_package_' . $product_id . '_job', $job_id, time() + (3600), "/");
                }
                //
                if (WC()->cart->get_cart_contents_count() > 0 && $all_clear !== false) {
                    WC()->cart->empty_cart();
                }
                $woocommerce->cart->add_to_cart($product_id);
                if ($return_type != 'no_where') {
                    if ($return_type == 'checkout_url') {
                        echo wc_get_checkout_url();
                    } else {
                        wp_safe_redirect(wc_get_checkout_url());
                        exit();
                    }
                }
            }
        }
    }

    public function woocommerce_after_checkout_process($order_id) {
        global $woocommerce;

        if (is_user_logged_in()) {
            $user_id = get_current_user_id();

            $pckgs_count = 0;
            foreach (WC()->cart->get_cart() as $cart_item) {
                //var_dump($cart_item);
                $product_id = isset($cart_item['product_id']) ? $cart_item['product_id'] : '';

                $prod_attach_with = get_post_meta($product_id, 'jobsearch_attach_with', true);
                if ($prod_attach_with == 'package') {

                    $pckgs_count++;
                    $package_name = get_post_meta($product_id, 'jobsearch_attach_package', true);
                    $product_package_obj = $package_name != '' ? get_page_by_path($package_name, 'OBJECT', 'package') : '';
                    if ($package_name != '' && is_object($product_package_obj)) {
                        $package_id = $product_package_obj->ID;
                        $order_pkg_type = get_post_meta($package_id, 'jobsearch_field_package_type', true);
                        if ($order_pkg_type == 'feature_job') {
                            update_post_meta($order_id, 'jobsearch_order_too_feature', 'yes');
                            update_post_meta($order_id, 'jobsearch_order_too_feature_pid', $package_id);
                        }

                        // if job id exist
                        if (isset($_COOKIE['wc_checkout_package_' . $product_id . '_job'])) {
                            $job_id = $_COOKIE['wc_checkout_package_' . $product_id . '_job'];
                            update_post_meta($order_id, 'jobsearch_order_attach_job_id', $job_id);
                            unset($_COOKIE['wc_checkout_package_' . $product_id . '_job']);
                            setcookie('wc_checkout_package_' . $product_id . '_job', null, -1, '/');
                            
                            $user_id = apply_filters('jobsearch_in_jobwoo_checkout_chng_user_id', $user_id, $job_id);
                        }
                        //

                        //
                        update_post_meta($order_id, 'jobsearch_order_attach_with', 'package');
                        update_post_meta($order_id, 'jobsearch_order_package', $package_id);
                        update_post_meta($order_id, 'jobsearch_order_user', $user_id);

                        // For paid package
                        update_post_meta($order_id, 'jobsearch_order_transaction_type', 'paid');
                    }
                }
                //
            }
            if ($pckgs_count == 1) {
                $fetured_pkg_too = get_post_meta($order_id, 'jobsearch_order_too_feature', true);
                if ($fetured_pkg_too == 'yes') {
                    update_post_meta($order_id, 'jobsearch_order_too_feature', '');
                    update_post_meta($order_id, 'jobsearch_order_too_feature_pid', '');
                }
            }
        }
    }

    public function woocommerce_order_status_complete($order_id) {

        $order_type = get_post_meta($order_id, 'jobsearch_order_attach_with', true);
        $order_user = get_post_meta($order_id, 'jobsearch_order_user', true);

        $user_obj = get_user_by('ID', $order_user);
        $user_login = $user_obj->user_login;

        if ($order_type == 'package') {

            $order_package = get_post_meta($order_id, 'jobsearch_order_package', true);
            $order_package_obj = get_post($order_package);

            if (is_object($order_package_obj)) {
                $package_id = $order_package_obj->ID;

                $order_job_id = get_post_meta($order_id, 'jobsearch_order_attach_job_id', true);

                $order_pkg_type = get_post_meta($package_id, 'jobsearch_field_package_type', true);
                $order_pkg_type = $order_pkg_type == '' ? 'job' : $order_pkg_type;
                
                if (class_exists('WC_Subscription')) {
                    $ordr_subscription_id = JobSearch_WC_Subscription::order_subscription($order_id, $order_user);
                    
                    $subscription_obj = new WC_Subscription($ordr_subscription_id);
                    $_related_orders = $subscription_obj->get_related_orders();
                    if (!empty($_related_orders) && sizeof($_related_orders) > 1) {
                        $parent_order_id = end($_related_orders);
                        if ($parent_order_id > 0) {
                            delete_post_meta($order_id, 'jobsearch_order_attach_with', 'package');
                            $order_id = $parent_order_id;
                        }
                    }
                }

                // Before adding pkg fields to order
                do_action('jobsearch_before_add_pkge_fields_in_order', $package_id, $order_id, $order_pkg_type);
                
                // Saving Package Fields and Values in Order
                do_action('jobsearch_add_package_fields_for_order', $package_id, $order_id, $order_pkg_type);
                
                if ($order_job_id > 0) {
                    $order_too_feature = get_post_meta($order_id, 'jobsearch_order_too_feature', true);
                    if ($order_too_feature == 'yes') {
                        $order_too_feature_pid = get_post_meta($order_id, 'jobsearch_order_too_feature_pid', true);
                        do_action('jobsearch_create_featured_job_packg_order', $order_too_feature_pid, $order_job_id, $order_user);
                    }
                    $jobr_args = array(
                        'order_pkg_type' => $order_pkg_type,
                        'package_id' => $package_id,
                        'order_job_id' => $order_job_id,
                        'order_id' => $order_id,
                    );
                    do_action('jobsearch_add_woo_pkg_order_fields_before', $jobr_args);
                    if ($order_pkg_type == 'job' || $order_pkg_type == 'featured_jobs' || $order_pkg_type == 'emp_allin_one' || $order_pkg_type == 'employer_profile') {
                        // Saving Package Fields and Values in Job
                        do_action('jobsearch_add_new_package_fields_for_job', $package_id, $order_job_id);
                        do_action('jobsearch_add_job_id_to_order', $order_job_id, $order_id);
                        do_action('jobsearch_add_featjob_id_to_order', $order_job_id, $order_id);
                        do_action('jobsearch_add_allinjob_id_to_order', $order_job_id, $order_id);
                        do_action('jobsearch_add_emprofjob_id_to_order', $order_job_id, $order_id);
                        do_action('jobsearch_set_job_expiry_and_status', $order_job_id, $order_id);
                    } else if ($order_pkg_type == 'feature_job') {
                        update_post_meta($order_job_id, 'jobsearch_field_job_featured', 'on');
                        $order_expiry_time = get_post_meta($order_id, 'package_expiry_timestamp', true);
                        if ($order_expiry_time > 0) {
                            $order_expiry_datetime = date('d-m-Y H:i:s', $order_expiry_time);
                            update_post_meta($order_job_id, 'jobsearch_field_job_feature_till', $order_expiry_datetime);
                        }
                    }
                }
            }
            //
        }
    }

}

// class Jobsearch_Woocommerce_Checkout 
$Jobsearch_Woocommerce_Checkout_obj = new Jobsearch_Woocommerce_Checkout();
