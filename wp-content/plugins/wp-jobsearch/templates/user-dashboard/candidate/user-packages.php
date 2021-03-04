<?php
global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;
$user_id = get_current_user_id();
$user_obj = get_user_by('ID', $user_id);

$page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
$page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);

$candidate_id = jobsearch_get_user_candidate_id($user_id);

$reults_per_page = isset($jobsearch_plugin_options['user-dashboard-per-page']) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;

$page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;

if ($candidate_id > 0) {
    ?>
    <div class="jobsearch-employer-dasboard">
        <div class="jobsearch-employer-box-section">

            <div class="jobsearch-profile-title">
                <h2><?php esc_html_e('Packages', 'wp-jobsearch') ?></h2>
            </div>
            <?php
            $args = array(
                'post_type' => 'shop_order',
                'posts_per_page' => $reults_per_page,
                'paged' => $page_num,
                'post_status' => 'wc-completed',
                'order' => 'DESC',
                'orderby' => 'ID',
                'meta_query' => apply_filters('jobsearch_cand_dash_pkgs_list_tab_mquery', array(
                    array(
                        'key' => 'jobsearch_order_attach_with',
                        'value' => 'package',
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'package_type',
                        'value' => array('candidate', 'promote_profile', 'urgent_pkg', 'candidate_profile'),
                        'compare' => 'IN',
                    ),
                    array(
                        'key' => 'jobsearch_order_user',
                        'value' => $user_id,
                        'compare' => '=',
                    ),
                )),
            );
            $pkgs_query = new WP_Query($args);
            $total_pkgs = $pkgs_query->found_posts;
            if ($pkgs_query->have_posts()) {
                ?>
                <div class="jobsearch-packages-list-holder">
                    <div class="jobsearch-employer-packages">
                        <div class="jobsearch-table-layer jobsearch-packages-thead">
                            <div class="jobsearch-table-row">
                                <div class="jobsearch-table-cell"><?php esc_html_e('Order ID', 'wp-jobsearch') ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e('Package', 'wp-jobsearch') ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e('Package Expiry', 'wp-jobsearch') ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e('Status', 'wp-jobsearch') ?></div>
                                <div class="jobsearch-table-cell">&nbsp;</div>
                            </div>
                        </div>
                        <?php
                        while ($pkgs_query->have_posts()) : $pkgs_query->the_post();
                            $pkg_rand = rand(10000000, 99999999);
                            $pkg_order_id = get_the_ID();
                            
                            $pkg_order_expiry = get_post_meta($pkg_order_id, 'package_expiry_timestamp', true);
                            
                            $pkg_order_name = get_post_meta($pkg_order_id, 'package_name', true);

                            $unlimited_pkg = get_post_meta($pkg_order_id, 'unlimited_pkg', true);
                            //
                            $pkg_type = get_post_meta($pkg_order_id, 'package_type', true);

                            $total_apps = get_post_meta($pkg_order_id, 'num_of_apps', true);

                            $used_apps = jobsearch_pckg_order_used_apps($pkg_order_id);
                            $remaining_apps = jobsearch_pckg_order_remaining_apps($pkg_order_id);

                            $unlimited_numcapps = get_post_meta($pkg_order_id, 'unlimited_numcapps', true);
                            if ($unlimited_numcapps == 'yes') {
                                $total_apps = esc_html__('Unlimited', 'wp-jobsearch');
                                $used_apps = '-';
                                $remaining_apps = '-';
                            }

                            $pkg_exp_dur = get_post_meta($pkg_order_id, 'package_expiry_time', true);
                            $pkg_exp_dur_unit = get_post_meta($pkg_order_id, 'package_expiry_time_unit', true);

                            $status_txt = esc_html__('Active', 'wp-jobsearch');
                            $status_class = '';

                            if (jobsearch_app_pckg_order_is_expired($pkg_order_id)) {
                                $status_txt = esc_html__('Expired', 'wp-jobsearch');
                                $status_class = 'jobsearch-packages-pending';
                            }
                            if ($pkg_type == 'promote_profile') {
                                $status_txt = esc_html__('Active', 'wp-jobsearch');
                                $status_class = '';

                                if (jobsearch_promote_profile_pkg_is_expired($pkg_order_id)) {
                                    $status_txt = esc_html__('Expired', 'wp-jobsearch');
                                    $status_class = 'jobsearch-packages-pending';
                                }
                            }
                            if ($pkg_type == 'urgent_pkg') {
                                $status_txt = esc_html__('Active', 'wp-jobsearch');
                                $status_class = '';

                                if (jobsearch_member_urgent_pkg_is_expired($pkg_order_id)) {
                                    $status_txt = esc_html__('Expired', 'wp-jobsearch');
                                    $status_class = 'jobsearch-packages-pending';
                                }
                            }
                            if ($pkg_type == 'candidate_profile') {
                                $status_txt = esc_html__('Active', 'wp-jobsearch');
                                $status_class = '';

                                if (jobsearch_cand_profile_pkg_is_expired($pkg_order_id)) {
                                    $status_txt = esc_html__('Expired', 'wp-jobsearch');
                                    $status_class = 'jobsearch-packages-pending';
                                }
                            }
                            ?>
                            <div class="jobsearch-table-layer jobsearch-packages-tbody">
                                <div class="jobsearch-table-row">
                                    <div class="jobsearch-table-cell">#<?php echo($pkg_order_id) ?></div>
                                    <div class="jobsearch-table-cell"><span><?php echo($pkg_order_name) ?></span></div>

                                    <?php
                                    if ($unlimited_pkg == 'yes') {
                                        ?>
                                        <div class="jobsearch-table-cell"><?php esc_html_e('Never', 'wp-jobsearch'); ?></div>
                                    <?php } else { ?>
                                        <div class="jobsearch-table-cell"><?php echo absint($pkg_exp_dur) . ' ' . jobsearch_get_duration_unit_str($pkg_exp_dur_unit) ?></div>
                                    <?php } ?>
                                    <div class="jobsearch-table-cell"><i
                                                class="fa fa-circle <?php echo($status_class) ?>"></i> <?php echo($status_txt) ?>
                                    </div>
                                    <div class="jobsearch-table-cell"><a href="javascript:void(0);" class="jobsearch-pckg-mordetail" data-id="<?php echo ($pkg_order_id) ?>" data-mtxt="<?php esc_html_e('More detail', 'wp-jobsearch'); ?>" data-ctxt="<?php esc_html_e('Close', 'wp-jobsearch'); ?>"><?php esc_html_e('More detail', 'wp-jobsearch'); ?> <i class="fa fa-angle-right"></i></a></div>
                                </div>
                                <div id="packge-detail-box<?php echo ($pkg_order_id) ?>" class="packge-detail-sepbox" style="display: none;">
                                    <table class="packge-detail-table">
                                        <tbody>
                                            <tr class="pakcge-itm-stats">
                                                <td class="pakcge-one-hding"><?php esc_html_e('Applications', 'wp-jobsearch'); ?></td>
                                                <?php
                                                if ($unlimited_numcapps == 'yes') {
                                                    ?>
                                                    <td colspan="3"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></td>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <td><?php printf(__('Total: %s', 'wp-jobsearch'), $total_apps) ?></td>
                                                    <td><?php printf(__('Used: %s', 'wp-jobsearch'), $used_apps) ?></td>
                                                    <td><?php printf(__('Remaininig: %s', 'wp-jobsearch'), $remaining_apps) ?></td>
                                                    <?php
                                                }
                                                ?>
                                            </tr>
                                            <tr class="pakcge-itm-footr">
                                                <td class="pakcge-active-date" colspan="2">
                                                    <div class="date-sec">
                                                        <i class="jobsearch-icon jobsearch-calendar"></i> 
                                                        <?php
                                                        printf(esc_html__('Purchase Date: %s', 'wp-jobsearch'), get_the_date());
                                                        ?>
                                                    </div>
                                                </td>
                                                <td class="pakcge-expiry-date" colspan="2">
                                                    <div class="date-sec">
                                                        <i class="jobsearch-icon jobsearch-calendar"></i> 
                                                        <?php
                                                        $pkg_expires_date = $pkg_order_expiry > 0 ? date_i18n(get_option('date_format'), $pkg_order_expiry) : '';
                                                        if ($unlimited_pkg == 'yes') {
                                                            esc_html_e('Never Expire', 'wp-jobsearch');
                                                        } else {
                                                            printf(esc_html__('Expiry Date: %s', 'wp-jobsearch'), $pkg_expires_date);
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
                <?php
                $total_pages = 1;
                if ($total_pkgs > 0 && $reults_per_page > 0 && $total_pkgs > $reults_per_page) {
                    $total_pages = ceil($total_pkgs / $reults_per_page);
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