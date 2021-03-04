<?php
global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;
$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);

$page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
$page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);

$is_user_member = false;
if (jobsearch_user_isemp_member($user_id)) {
    $is_user_member = true;
    $employer_id = jobsearch_user_isemp_member($user_id);
    $user_id = jobsearch_get_employer_user_id($employer_id);
} else {
    $employer_id = jobsearch_get_user_employer_id($user_id);
}

$reults_per_page = isset($jobsearch_plugin_options['user-dashboard-per-page']) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;

$page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;

if ($employer_id > 0) {
    ?>
    <div class="jobsearch-employer-dasboard">
        <div class="jobsearch-employer-box-section">

            <div class="jobsearch-profile-title">
                <h2><?php esc_html_e('Transactions', 'wp-jobsearch') ?></h2>
            </div>
            <?php
            $args = array(
                'post_type' => 'shop_order',
                'posts_per_page' => $reults_per_page,
                'paged' => $page_num,
                'post_status' => array('wc-pending', 'wc-on-hold', 'wc-cancelled', 'wc-failed', 'wc-processing', 'wc-refunded', 'wc-completed'),
                'order' => 'DESC',
                'orderby' => 'ID',
                'meta_query' => array(
                    array(
                        'key' => 'jobsearch_order_transaction_type',
                        'value' => 'paid',
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'jobsearch_order_user',
                        'value' => $user_id,
                        'compare' => '=',
                    ),
                ),
            );
            $trans_query = new WP_Query($args);
            $total_trans = $trans_query->found_posts;
            if ($trans_query->have_posts()) {
                ?>
                <div class="jobsearch-transactions-list-holder">
                    <div class="jobsearch-employer-transactions">
                        <?php
                        ob_start();
                        ?>
                        <div class="jobsearch-table-layer jobsearch-transactions-thead">
                            <div class="jobsearch-table-row">
                                <div class="jobsearch-table-cell"><?php esc_html_e('Order ID', 'wp-jobsearch') ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e('Package', 'wp-jobsearch') ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e('Amount', 'wp-jobsearch') ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e('Date', 'wp-jobsearch') ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e('Payment Mode', 'wp-jobsearch') ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e('Status', 'wp-jobsearch') ?></div>
                            </div>
                        </div>
                        <?php
                        $hdings_html = ob_get_clean();
                        echo apply_filters('jobsearch_empdash_transactions_hdngs_html', $hdings_html);
                        //
                        while ($trans_query->have_posts()) : $trans_query->the_post();
                            $trans_rand = rand(10000000, 99999999);
                            $trans_order_id = get_the_ID();

                            $order_attach_with = get_post_meta($trans_order_id, 'jobsearch_order_attach_with', true);
                            $trans_order_name = '';
                            $trans_order_price = '';

                            $trans_order_obj = wc_get_order($trans_order_id);

                            if ($trans_order_name == '') {
                                foreach ($trans_order_obj->get_items() as $oitem_id => $oitem_product) {
                                    //Get the WC_Product object
                                    $oproduct = $oitem_product->get_product();

                                    if (is_object($oproduct)) {
                                        $trans_order_name = get_the_title($oproduct->get_ID());
                                    }
                                }
                            }

                            if ($order_attach_with == 'package') {
                                if ($trans_order_name == '') {
                                    $trans_order_name = get_post_meta($trans_order_id, 'package_name', true);
                                }
                            }

                            if ($trans_order_price <= 0) {
                                $trans_order_price = $trans_order_obj->get_total();
                            }

                            $order_price = jobsearch_get_price_format($trans_order_price);

                            $trans_status = $trans_order_obj->get_status();
                            if ($trans_status == 'completed') {
                                $status_txt = esc_html__('Successfull', 'wp-jobsearch');
                                $status_class = '';
                            } else if ($trans_status == 'processing') {
                                $status_txt = esc_html__('Processing', 'wp-jobsearch');
                                $status_class = 'jobsearch-transactions-pending';
                            } else if ($trans_status == 'refunded') {
                                $status_txt = esc_html__('Refunded', 'wp-jobsearch');
                                $status_class = 'jobsearch-transactions-pending';
                            } else {
                                $status_txt = esc_html__('Pending', 'wp-jobsearch');
                                $status_class = 'jobsearch-transactions-pending';
                            }

                            $order_date_obj = $trans_order_obj->get_date_created();
                            $order_date_array = json_decode(json_encode($order_date_obj), true);
                            $order_date = isset($order_date_array['date']) ? $order_date_array['date'] : '';

                            $payment_mode = $trans_order_obj->get_payment_method();
                            $payment_mode = $payment_mode != '' ? $payment_mode : '-';
                            if ($payment_mode == 'cod') {
                                $payment_mode = esc_html__('Cash on Delivery', 'wp-jobsearch');
                            }
                            ob_start();
                            ?>
                            <div class="jobsearch-table-layer jobsearch-transactions-tbody">
                                <div class="jobsearch-table-row">
                                    <div class="jobsearch-table-cell">#<?php echo ($trans_order_id) ?></div>
                                    <div class="jobsearch-table-cell"><span><?php echo ($trans_order_name) ?></span></div>
                                    <div class="jobsearch-table-cell"><small><?php echo ($order_price) ?></small></div>
                                    <div class="jobsearch-table-cell"><?php echo ($order_date != '' ? date_i18n(get_option('date_format'), strtotime($order_date)) : '-') ?></div>
                                    <div class="jobsearch-table-cell"><?php echo ($payment_mode) ?></div>
                                    <div class="jobsearch-table-cell"><i class="fa fa-circle <?php echo ($status_class) ?>"></i> <?php echo ($status_txt) ?></div>
                                </div>
                            </div>
                            <?php
                            $item_html = ob_get_clean();
                            echo apply_filters('jobsearch_empdash_transactions_item_html', $item_html, $trans_order_id);
                            //
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
                <?php
                $total_pages = 1;
                if ($total_trans > 0 && $reults_per_page > 0 && $total_trans > $reults_per_page) {
                    $total_pages = ceil($total_trans / $reults_per_page);
                    ?>
                    <div class="jobsearch-pagination-blog">
                        <?php $Jobsearch_User_Dashboard_Settings->pagination($total_pages, $page_num, $page_url) ?>
                    </div>
                    <?php
                }
            } else {
                ?>
                <p><?php esc_html_e('No record found.', 'wp-jobsearch') ?></p>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
}    