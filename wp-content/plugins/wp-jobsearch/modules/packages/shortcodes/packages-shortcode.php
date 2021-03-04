<?php
add_shortcode('jobsearch_packages', 'jobsearch_packages_shortcode');

function jobsearch_packages_shortcode($atts, $content = "") {

    global $jobsearch_plugin_options;
    extract(shortcode_atts(array(
        'title' => '',
        'packages_type' => 'cv',
        'num_packages' => '',
                    ), $atts));

    ob_start();

    if ($title != '') { ?>
        <h2><?php echo ($title) ?></h2>
        <?php
    }

    $package_per_page = $num_packages == '' ? -1 : absint($num_packages);

    $packages_type = $packages_type != '' ? $packages_type : 'job';

    $args = array(
        'post_type' => 'package',
        'posts_per_page' => $package_per_page,
        'post_status' => 'publish',
        'order' => 'ASC',
        'orderby' => 'title',
        'meta_query' => array(
            array(
                'key' => 'jobsearch_field_package_type',
                'value' => $packages_type,
                'compare' => '=',
            ),
        ),
    );
    $pkgs_query = new WP_Query($args);
    if ($pkgs_query->have_posts()) {
        wp_enqueue_script('jobsearch-packages-scripts');
        ?>
        <div class="jobsearch-row">
            <?php
            while ($pkgs_query->have_posts()) : $pkgs_query->the_post();
                $pkg_rand = rand(10000000, 99999999);
                $pkg_id = get_the_ID();
                
                $pkg_attach_product = get_post_meta($pkg_id, 'jobsearch_package_product', true);

                if ($pkg_attach_product != '' && get_page_by_path($pkg_attach_product, 'OBJECT', 'product')) {

                    $pkg_type = get_post_meta($pkg_id, 'jobsearch_field_charges_type', true);
                    $pkg_price = get_post_meta($pkg_id, 'jobsearch_field_package_price', true);

                    $pkg_exp_dur = get_post_meta($pkg_id, 'jobsearch_field_package_expiry_time', true);
                    $pkg_exp_dur_unit = get_post_meta($pkg_id, 'jobsearch_field_package_expiry_time_unit', true);

                    $pkg_exfield_title = get_post_meta($pkg_id, 'jobsearch_field_package_exfield_title', true);
                    $pkg_exfield_val = get_post_meta($pkg_id, 'jobsearch_field_package_exfield_val', true);
                    $pkg_exfield_status = get_post_meta($pkg_id, 'jobsearch_field_package_exfield_status', true);
                    ?>
                    <div class="jobsearch-column-4">
                        <div class="jobsearch-classic-priceplane">
                            <h2><?php echo get_the_title($pkg_id) ?></h2>
                            <div class="jobsearch-priceplane-section">
                                <?php
                                if ($pkg_type == 'paid') {
                                    echo '<span>' . jobsearch_get_price_format($pkg_price) . ' <small>' . esc_html__('only', 'wp-jobsearch') . '</small></span>';
                                } else {
                                    esc_html_e('Free', 'wp-jobsearch');
                                }
                                ?>
                            </div>
                            <div class="grab-classic-priceplane">
                                <ul>
                                    <?php
                                    if (!empty($pkg_exfield_title)) {
                                        $_exf_counter = 0;
                                        foreach ($pkg_exfield_title as $_exfield_title) {
                                            $_exfield_status = isset($pkg_exfield_status[$_exf_counter]) ? $pkg_exfield_status[$_exf_counter] : '';
                                            ?>
                                            <li<?php echo ( $_exfield_status == 'active' ? ' class="active"' : '') ?>><i class="jobsearch-icon jobsearch-check-square"></i> <?php echo $_exfield_title ?></li>
                                            <?php
                                            $_exf_counter++;
                                        }
                                    }
                                    ?>
                                </ul>
                                <?php if (is_user_logged_in()) { ?>
                                    <?php
                                    if ($packages_type == 'job') {
                                        ?>
                                        <a href="javascript:void(0);" class="jobsearch-classic-priceplane-btn jobsearch-subscribe-job-pkg" data-id="<?php echo ($pkg_id) ?>"><?php esc_html_e('Get Started', 'wp-jobsearch') ?> </a>
                                        <?php
                                    } else if ($packages_type == 'candidate') {
                                        ?>
                                        <a href="javascript:void(0);" class="jobsearch-classic-priceplane-btn jobsearch-subscribe-candidate-pkg" data-id="<?php echo ($pkg_id) ?>"><?php esc_html_e('Get Started', 'wp-jobsearch') ?> </a>
                                        <?php
                                    } else if ($packages_type == 'promote_profile') {
                                        ?>
                                        <a href="javascript:void(0);" class="jobsearch-classic-priceplane-btn jobsearch-promoteprof-pkg" data-id="<?php echo ($pkg_id) ?>"><?php esc_html_e('Get Started', 'wp-jobsearch') ?> </a>
                                        <?php
                                    } else if ($packages_type == 'urgent_pkg') {
                                        ?>
                                        <a href="javascript:void(0);" class="jobsearch-classic-priceplane-btn jobsearch-urgentsub-pkg" data-id="<?php echo ($pkg_id) ?>"><?php esc_html_e('Get Started', 'wp-jobsearch') ?> </a>
                                        <?php
                                    } else if ($packages_type == 'emp_allin_one') {
                                        ?>
                                        <a href="javascript:void(0);" class="jobsearch-classic-priceplane-btn jobsearch-subs-allinone-pkg" data-id="<?php echo ($pkg_id) ?>"><?php esc_html_e('Get Started', 'wp-jobsearch') ?> </a>
                                        <?php
                                    } else {
                                        ?>
                                        <a href="javascript:void(0);" class="jobsearch-classic-priceplane-btn jobsearch-subscribe-cv-pkg" data-id="<?php echo ($pkg_id) ?>"><?php esc_html_e('Get Started', 'wp-jobsearch') ?> </a>
                                        <?php
                                    }
                                    ?>
                                    <span class="pkg-loding-msg" style="display:none;"></span>
                                <?php } else { ?>
                                    <a href="javascript:void(0);" class="jobsearch-classic-priceplane-btn jobsearch-open-signin-tab"><?php esc_html_e('Get Started', 'wp-jobsearch') ?> </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
        <?php
    }

    $html = ob_get_clean();
    return $html;
}
