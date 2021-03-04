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
            <?php
            ob_start();
            ?>
            <div class="jobsearch-profile-title">
                <h2><?php esc_html_e('Packages', 'wp-jobsearch') ?></h2>
            </div>
            <?php
            $hding_html = ob_get_clean();
            echo apply_filters('jobsearch_empdash_pckges_cont_mainhding_html', $hding_html);
            
            $args = array(
                'post_type' => 'shop_order',
                'posts_per_page' => $reults_per_page,
                'paged' => $page_num,
                'post_status' => 'wc-completed',
                'order' => 'DESC',
                'orderby' => 'ID',
                'meta_query' => apply_filters('jobsearch_emp_dash_pkgs_list_tab_mquery', array(
                    array(
                        'key' => 'jobsearch_order_attach_with',
                        'value' => 'package',
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'package_type',
                        'value' => apply_filters('jobsearch_emp_dash_pkg_types_in_query', array('job', 'featured_jobs', 'emp_allin_one', 'cv', 'promote_profile', 'urgent_pkg', 'employer_profile')),
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
                $detail_boxes_html = '';
                ob_start();
                ?>
                <div class="jobsearch-packages-list-holder">
                    <div class="jobsearch-employer-packages">
                        <div class="jobsearch-table-layer jobsearch-packages-thead">
                            <div class="jobsearch-table-row">
                                <div class="jobsearch-table-cell"><?php esc_html_e('Order ID', 'wp-jobsearch') ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e('Package', 'wp-jobsearch') ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e('Expiry', 'wp-jobsearch') ?></div>
                                <div class="jobsearch-table-cell"><?php esc_html_e('Status', 'wp-jobsearch') ?></div>
                                <div class="jobsearch-table-cell">&nbsp;</div>
                            </div>
                        </div>
                        <?php
                        while ($pkgs_query->have_posts()) : $pkgs_query->the_post();
                            $pkg_rand = rand(10000000, 99999999);
                            $pkg_order_id = get_the_ID();
                            $pkg_order_name = get_post_meta($pkg_order_id, 'package_name', true);

                            $pkg_order_expiry = get_post_meta($pkg_order_id, 'package_expiry_timestamp', true);
                            //
                            $pkg_type = get_post_meta($pkg_order_id, 'package_type', true);
                            
                            $unlimited_pkg = get_post_meta($pkg_order_id, 'unlimited_pkg', true);

                            if ($pkg_type == 'cv') {
                                $total_cvs = get_post_meta($pkg_order_id, 'num_of_cvs', true);
                                $unlimited_numcvs = get_post_meta($pkg_order_id, 'unlimited_numcvs', true);
                                if ($unlimited_numcvs == 'yes') {
                                    $total_cvs = esc_html__('Unlimited', 'wp-jobsearch');
                                }

                                $used_cvs = jobsearch_pckg_order_used_cvs($pkg_order_id);
                                $remaining_cvs = jobsearch_pckg_order_remaining_cvs($pkg_order_id);
                                if ($unlimited_numcvs == 'yes') {
                                    $used_cvs = '-';
                                    $remaining_cvs = '-';
                                }
                            } else if ($pkg_type == 'emp_allin_one') {
                                
                                $total_jobs = get_post_meta($pkg_order_id, 'allin_num_jobs', true);
                                $unlimited_numjobs = get_post_meta($pkg_order_id, 'unlimited_numjobs', true);
                                if ($unlimited_numjobs == 'yes') {
                                    $total_jobs = esc_html__('Unlimited', 'wp-jobsearch');
                                }
                                //
                                $total_fjobs = get_post_meta($pkg_order_id, 'allin_num_fjobs', true);
                                $unlimited_numfjobs = get_post_meta($pkg_order_id, 'unlimited_numfjobs', true);
                                if ($unlimited_numfjobs == 'yes') {
                                    $total_fjobs = esc_html__('Unlimited', 'wp-jobsearch');
                                }
                                //
                                $total_cvs = get_post_meta($pkg_order_id, 'allin_num_cvs', true);
                                $unlimited_numcvs = get_post_meta($pkg_order_id, 'unlimited_numcvs', true);
                                if ($unlimited_numcvs == 'yes') {
                                    $total_cvs = esc_html__('Unlimited', 'wp-jobsearch');
                                }

                                $job_exp_dur = get_post_meta($pkg_order_id, 'allinjob_expiry_time', true);
                                $job_exp_dur_unit = get_post_meta($pkg_order_id, 'allinjob_expiry_time_unit', true);

                                $used_jobs = jobsearch_allinpckg_order_used_jobs($pkg_order_id);
                                $remaining_jobs = jobsearch_allinpckg_order_remaining_jobs($pkg_order_id);
                                if ($unlimited_numjobs == 'yes') {
                                    $used_jobs = '-';
                                    $remaining_jobs = '-';
                                }
                                //
                                $used_fjobs = jobsearch_allinpckg_order_used_fjobs($pkg_order_id);
                                $remaining_fjobs = jobsearch_allinpckg_order_remaining_fjobs($pkg_order_id);
                                if ($unlimited_numfjobs == 'yes') {
                                    $used_fjobs = '-';
                                    $remaining_fjobs = '-';
                                }
                                //
                                $used_cvs = jobsearch_allinpckg_order_used_cvs($pkg_order_id);
                                $remaining_cvs = jobsearch_allinpckg_order_remaining_cvs($pkg_order_id);
                                if ($unlimited_numcvs == 'yes') {
                                    $used_cvs = '-';
                                    $remaining_cvs = '-';
                                }

                            } else if ($pkg_type == 'employer_profile') {
                                
                                $total_jobs = get_post_meta($pkg_order_id, 'emprof_num_jobs', true);
                                $unlimited_numjobs = get_post_meta($pkg_order_id, 'unlimited_numjobs', true);
                                if ($unlimited_numjobs == 'yes') {
                                    $total_jobs = esc_html__('Unlimited', 'wp-jobsearch');
                                }
                                //
                                $total_fjobs = get_post_meta($pkg_order_id, 'emprof_num_fjobs', true);
                                $unlimited_numfjobs = get_post_meta($pkg_order_id, 'unlimited_numfjobs', true);
                                if ($unlimited_numfjobs == 'yes') {
                                    $total_fjobs = esc_html__('Unlimited', 'wp-jobsearch');
                                }
                                //
                                $total_cvs = get_post_meta($pkg_order_id, 'emprof_num_cvs', true);
                                $unlimited_numcvs = get_post_meta($pkg_order_id, 'unlimited_numcvs', true);
                                if ($unlimited_numcvs == 'yes') {
                                    $total_cvs = esc_html__('Unlimited', 'wp-jobsearch');
                                }

                                $job_exp_dur = get_post_meta($pkg_order_id, 'emprofjob_expiry_time', true);
                                $job_exp_dur_unit = get_post_meta($pkg_order_id, 'emprofjob_expiry_time_unit', true);

                                $used_jobs = jobsearch_emprofpckg_order_used_jobs($pkg_order_id);
                                $remaining_jobs = jobsearch_emprofpckg_order_remaining_jobs($pkg_order_id);
                                if ($unlimited_numjobs == 'yes') {
                                    $used_jobs = '-';
                                    $remaining_jobs = '-';
                                }
                                //
                                $used_fjobs = jobsearch_emprofpckg_order_used_fjobs($pkg_order_id);
                                $remaining_fjobs = jobsearch_emprofpckg_order_remaining_fjobs($pkg_order_id);
                                if ($unlimited_numfjobs == 'yes') {
                                    $used_fjobs = '-';
                                    $remaining_fjobs = '-';
                                }
                                //
                                $used_cvs = jobsearch_emprofpckg_order_used_cvs($pkg_order_id);
                                $remaining_cvs = jobsearch_emprofpckg_order_remaining_cvs($pkg_order_id);
                                if ($unlimited_numcvs == 'yes') {
                                    $used_cvs = '-';
                                    $remaining_cvs = '-';
                                }

                            } else if ($pkg_type == 'featured_jobs') {
                                $total_jobs = get_post_meta($pkg_order_id, 'num_of_fjobs', true);
                                
                                $unlimited_numfjobs = get_post_meta($pkg_order_id, 'unlimited_numfjobs', true);
                                if ($unlimited_numfjobs == 'yes') {
                                    $total_jobs = esc_html__('Unlimited', 'wp-jobsearch');
                                }

                                $job_exp_dur = get_post_meta($pkg_order_id, 'fjob_expiry_time', true);
                                $job_exp_dur_unit = get_post_meta($pkg_order_id, 'fjob_expiry_time_unit', true);

                                $used_jobs = jobsearch_pckg_order_used_fjobs($pkg_order_id);
                                $remaining_jobs = jobsearch_pckg_order_remaining_fjobs($pkg_order_id);
                                if ($unlimited_numfjobs == 'yes') {
                                    $used_jobs = '-';
                                    $remaining_jobs = '-';
                                }
                                
                                //
                                $total_fjobs = get_post_meta($pkg_order_id, 'feat_job_credits', true);
                                $unlimited_numfcrs = get_post_meta($pkg_order_id, 'unlimited_fjobcrs', true);
                                if ($unlimited_numfcrs == 'yes') {
                                    $total_fjobs = esc_html__('Unlimited', 'wp-jobsearch');
                                }
                                
                                $used_fjobs = jobsearch_pckg_order_used_featjob_credits($pkg_order_id);
                                $remaining_fjobs = jobsearch_pckg_order_remain_featjob_credits($pkg_order_id);
                                if ($unlimited_numfcrs == 'yes') {
                                    $used_fjobs = '-';
                                    $remaining_fjobs = '-';
                                }
                                
                            } else {
                                $total_jobs = get_post_meta($pkg_order_id, 'num_of_jobs', true);
                                
                                $unlimited_numjobs = get_post_meta($pkg_order_id, 'unlimited_numjobs', true);
                                if ($unlimited_numjobs == 'yes') {
                                    $total_jobs = esc_html__('Unlimited', 'wp-jobsearch');
                                }
                                $total_jobs = apply_filters('jobsearch_emp_dash_pkg_total_jobs_count', $total_jobs, $pkg_order_id);

                                $job_exp_dur = get_post_meta($pkg_order_id, 'job_expiry_time', true);
                                $job_exp_dur_unit = get_post_meta($pkg_order_id, 'job_expiry_time_unit', true);

                                $used_jobs = jobsearch_pckg_order_used_jobs($pkg_order_id);
                                if ($unlimited_numjobs == 'yes') {
                                    $used_jobs = '-';
                                }
                                $used_jobs = apply_filters('jobsearch_emp_dash_pkg_used_jobs_count', $used_jobs, $pkg_order_id);
                                $remaining_jobs = jobsearch_pckg_order_remaining_jobs($pkg_order_id);
                                if ($unlimited_numjobs == 'yes') {
                                    $remaining_jobs = '-';
                                }
                                $remaining_jobs = apply_filters('jobsearch_emp_dash_pkg_remain_jobs_count', $remaining_jobs, $pkg_order_id);
                            }
                            $pkg_exp_dur = get_post_meta($pkg_order_id, 'package_expiry_time', true);
                            $pkg_exp_dur_unit = get_post_meta($pkg_order_id, 'package_expiry_time_unit', true);

                            $status_txt = esc_html__('Active', 'wp-jobsearch');
                            $status_class = '';
                            if ($pkg_type == 'cv') {
                                if (jobsearch_cv_pckg_order_is_expired($pkg_order_id)) {
                                    $status_txt = esc_html__('Expired', 'wp-jobsearch');
                                    $status_class = 'jobsearch-packages-pending';
                                }
                            } else if ($pkg_type == 'featured_jobs') {
                                if (jobsearch_fjobs_pckg_order_is_expired($pkg_order_id)) {
                                    $status_txt = esc_html__('Expired', 'wp-jobsearch');
                                    $status_class = 'jobsearch-packages-pending';
                                }
                            } else if ($pkg_type == 'job') {
                                if (jobsearch_pckg_order_is_expired($pkg_order_id)) {
                                    $status_txt = esc_html__('Expired', 'wp-jobsearch');
                                    $status_class = 'jobsearch-packages-pending';
                                }
                                $status_txt = apply_filters('jobsearch_emp_dash_jobpkgs_list_status_txt', $status_txt, $pkg_order_id);
                                $status_class = apply_filters('jobsearch_emp_dash_jobpkgs_list_status_class', $status_class, $pkg_order_id);
                            } else if ($pkg_type == 'emp_allin_one') {
                                $allin_jobs_pkgexpire = jobsearch_allinpckg_order_is_expired($pkg_order_id);
                                $allin_fjobs_pkgexpire = jobsearch_allinpckg_order_is_expired($pkg_order_id, 'fjobs');
                                $allin_cvs_pkgexpire = jobsearch_allinpckg_order_is_expired($pkg_order_id, 'cvs');
                                if ($allin_jobs_pkgexpire && $allin_fjobs_pkgexpire && $allin_cvs_pkgexpire) {
                                    $status_txt = esc_html__('Expired', 'wp-jobsearch');
                                    $status_class = 'jobsearch-packages-pending';
                                }
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
                            if ($pkg_type == 'employer_profile') {
                                $emprof_jobs_pkgexpire = jobsearch_emprofpckg_order_is_expired($pkg_order_id);
                                $emprof_fjobs_pkgexpire = jobsearch_emprofpckg_order_is_expired($pkg_order_id, 'fjobs');
                                $emprof_cvs_pkgexpire = jobsearch_emprofpckg_order_is_expired($pkg_order_id, 'cvs');
                                if ($emprof_jobs_pkgexpire && $emprof_fjobs_pkgexpire && $emprof_cvs_pkgexpire) {
                                    $status_txt = esc_html__('Expired', 'wp-jobsearch');
                                    $status_class = 'jobsearch-packages-pending';
                                }
                            }
                            ?>
                            <div class="jobsearch-table-layer jobsearch-packages-tbody">
                                <div class="jobsearch-table-row">
                                    <div class="jobsearch-table-cell">#<?php echo ($pkg_order_id) ?></div>
                                    <div class="jobsearch-table-cell">
                                        <?php
                                        ob_start();
                                        ?>
                                        <span><?php echo ($pkg_order_name) ?></span>
                                        <?php
                                        $pkg_name_html = ob_get_clean();
                                        echo apply_filters('jobsearch_emp_dashboard_pkgs_list_pkg_title', $pkg_name_html, $pkg_order_id);
                                        ?>
                                    </div>
                                    <?php
                                    if ($unlimited_pkg == 'yes') {
                                        ?>
                                        <div class="jobsearch-table-cell"><?php esc_html_e('Never', 'wp-jobsearch'); ?></div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="jobsearch-table-cell"><?php echo absint($pkg_exp_dur) . ' ' . jobsearch_get_duration_unit_str($pkg_exp_dur_unit) ?></div>
                                        <?php
                                    }
                                    ?>
                                    <div class="jobsearch-table-cell">
                                        <i class="fa fa-circle <?php echo apply_filters('jobsearch_emp_dash_pkgs_inlist_pstatus', $status_class, $pkg_order_id) ?>"></i> <?php echo apply_filters('jobsearch_emp_dash_pkgs_inlist_pstatext', $status_txt, $pkg_order_id) ?>
                                    </div>
                                    <div class="jobsearch-table-cell"><a href="javascript:void(0);" class="jobsearch-pckg-mordetail" data-id="<?php echo ($pkg_order_id) ?>" data-mtxt="<?php esc_html_e('More detail', 'wp-jobsearch'); ?>" data-ctxt="<?php esc_html_e('Close', 'wp-jobsearch'); ?>"><?php esc_html_e('More detail', 'wp-jobsearch'); ?> <i class="fa fa-angle-right"></i></a></div>
                                </div>
                                <div id="packge-detail-box<?php echo ($pkg_order_id) ?>" class="packge-detail-sepbox" style="display: none;">
                                    <table class="packge-detail-table">
                                        <tbody>
                                            <?php
                                            if ($pkg_type == 'cv') {
                                                ?>
                                                <tr class="pakcge-itm-stats">
                                                    <td class="pakcge-one-hding"><?php esc_html_e('CVs', 'wp-jobsearch'); ?></td>
                                                    <?php
                                                    if ($unlimited_numcvs == 'yes') {
                                                        ?>
                                                        <td colspan="3"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></td>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <td><?php printf(__('Total: %s', 'wp-jobsearch'), $total_cvs) ?></td>
                                                        <td><?php printf(__('Used: %s', 'wp-jobsearch'), $used_cvs) ?></td>
                                                        <td><?php printf(__('Remaininig: %s', 'wp-jobsearch'), $remaining_cvs) ?></td>
                                                        <?php
                                                    }
                                                    ?>
                                                </tr>
                                                <?php
                                            } else if ($pkg_type == 'featured_jobs') {
                                                ?>
                                                <tr class="pakcge-itm-stats">
                                                    <td class="pakcge-one-hding"><?php esc_html_e('Jobs you can post', 'wp-jobsearch'); ?></td>
                                                    <?php
                                                    if ($unlimited_numfjobs == 'yes') {
                                                        ?>
                                                        <td colspan="3"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></td>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <td><?php printf(__('Total: %s', 'wp-jobsearch'), $total_jobs) ?></td>
                                                        <td><?php printf(__('Used: %s', 'wp-jobsearch'), $used_jobs) ?></td>
                                                        <td><?php printf(__('Remaininig: %s', 'wp-jobsearch'), $remaining_jobs) ?></td>
                                                        <?php
                                                    }
                                                    ?>
                                                </tr>
                                                <tr class="pakcge-itm-stats">
                                                    <td class="pakcge-one-hding"><?php esc_html_e('Featured job credits', 'wp-jobsearch'); ?></td>
                                                    <?php
                                                    if ($unlimited_numfcrs == 'yes') {
                                                        ?>
                                                        <td colspan="3"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></td>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <td><?php printf(__('Total: %s', 'wp-jobsearch'), $total_fjobs) ?></td>
                                                        <td><?php printf(__('Used: %s', 'wp-jobsearch'), $used_fjobs) ?></td>
                                                        <td><?php printf(__('Remaininig: %s', 'wp-jobsearch'), $remaining_fjobs) ?></td>
                                                        <?php
                                                    }
                                                    ?>
                                                </tr>
                                                <?php
                                            } else if ($pkg_type == 'emp_allin_one') {
                                                ?>
                                                <tr class="pakcge-itm-stats">
                                                    <td class="pakcge-one-hding"><?php esc_html_e('Jobs you can post', 'wp-jobsearch'); ?></td>
                                                    <?php
                                                    if ($unlimited_numjobs == 'yes') {
                                                        ?>
                                                        <td colspan="3"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></td>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <td><?php printf(__('Total: %s', 'wp-jobsearch'), $total_jobs) ?></td>
                                                        <td><?php printf(__('Used: %s', 'wp-jobsearch'), $used_jobs) ?></td>
                                                        <td><?php printf(__('Remaininig: %s', 'wp-jobsearch'), $remaining_jobs) ?></td>
                                                        <?php
                                                    }
                                                    ?>
                                                </tr>
                                                <tr class="pakcge-itm-stats">
                                                    <td class="pakcge-one-hding"><?php esc_html_e('Featured job credits', 'wp-jobsearch'); ?></td>
                                                    <?php
                                                    if ($unlimited_numfjobs == 'yes') {
                                                        ?>
                                                        <td colspan="3"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></td>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <td><?php printf(__('Total: %s', 'wp-jobsearch'), $total_fjobs) ?></td>
                                                        <td><?php printf(__('Used: %s', 'wp-jobsearch'), $used_fjobs) ?></td>
                                                        <td><?php printf(__('Remaininig: %s', 'wp-jobsearch'), $remaining_fjobs) ?></td>
                                                        <?php
                                                    }
                                                    ?>
                                                </tr>
                                                <tr class="pakcge-itm-stats">
                                                    <td class="pakcge-one-hding"><?php esc_html_e('Download candidate CVs from database', 'wp-jobsearch'); ?></td>
                                                    <?php
                                                    if ($unlimited_numcvs == 'yes') {
                                                        ?>
                                                        <td colspan="3"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></td>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <td><?php printf(__('Total: %s', 'wp-jobsearch'), $total_cvs) ?></td>
                                                        <td><?php printf(__('Used: %s', 'wp-jobsearch'), $used_cvs) ?></td>
                                                        <td><?php printf(__('Remaininig: %s', 'wp-jobsearch'), $remaining_cvs) ?></td>
                                                        <?php
                                                    }
                                                    ?>
                                                </tr>
                                                <?php
                                            } else if ($pkg_type == 'employer_profile') {
                                                ?>
                                                <tr class="pakcge-itm-stats">
                                                    <td class="pakcge-one-hding"><?php esc_html_e('Jobs you can post', 'wp-jobsearch'); ?></td>
                                                    <?php
                                                    if ($unlimited_numjobs == 'yes') {
                                                        ?>
                                                        <td colspan="3"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></td>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <td><?php printf(__('Total: %s', 'wp-jobsearch'), $total_jobs) ?></td>
                                                        <td><?php printf(__('Used: %s', 'wp-jobsearch'), $used_jobs) ?></td>
                                                        <td><?php printf(__('Remaininig: %s', 'wp-jobsearch'), $remaining_jobs) ?></td>
                                                        <?php
                                                    }
                                                    ?>
                                                </tr>
                                                <tr class="pakcge-itm-stats">
                                                    <td class="pakcge-one-hding"><?php esc_html_e('Featured job credits', 'wp-jobsearch'); ?></td>
                                                    <?php
                                                    if ($unlimited_numfjobs == 'yes') {
                                                        ?>
                                                        <td colspan="3"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></td>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <td><?php printf(__('Total: %s', 'wp-jobsearch'), $total_fjobs) ?></td>
                                                        <td><?php printf(__('Used: %s', 'wp-jobsearch'), $used_fjobs) ?></td>
                                                        <td><?php printf(__('Remaininig: %s', 'wp-jobsearch'), $remaining_fjobs) ?></td>
                                                        <?php
                                                    }
                                                    ?>
                                                </tr>
                                                <tr class="pakcge-itm-stats">
                                                    <td class="pakcge-one-hding"><?php esc_html_e('Download candidate CVs from database', 'wp-jobsearch'); ?></td>
                                                    <?php
                                                    if ($unlimited_numcvs == 'yes') {
                                                        ?>
                                                        <td colspan="3"><?php esc_html_e('Unlimited', 'wp-jobsearch') ?></td>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <td><?php printf(__('Total: %s', 'wp-jobsearch'), $total_cvs) ?></td>
                                                        <td><?php printf(__('Used: %s', 'wp-jobsearch'), $used_cvs) ?></td>
                                                        <td><?php printf(__('Remaininig: %s', 'wp-jobsearch'), $remaining_cvs) ?></td>
                                                        <?php
                                                    }
                                                    ?>
                                                </tr>
                                                <?php
                                            } else if ($pkg_type == 'job') {
                                                ?>
                                                <tr class="pakcge-itm-stats">
                                                    <td class="pakcge-one-hding"><?php esc_html_e('Jobs', 'wp-jobsearch'); ?></td>
                                                    <td><?php printf(__('Total: %s', 'wp-jobsearch'), apply_filters('jobsearch_emp_dash_pkgs_inlist_ttjobs', $total_jobs, $pkg_order_id)) ?></td>
                                                    <td><?php printf(__('Used: %s', 'wp-jobsearch'), apply_filters('jobsearch_emp_dash_pkgs_inlist_uujobs', $used_jobs, $pkg_order_id)) ?></td>
                                                    <td><?php printf(__('Remaininig: %s', 'wp-jobsearch'), apply_filters('jobsearch_emp_dash_pkgs_inlist_rrjobs', $remaining_jobs, $pkg_order_id)) ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
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
                $pkgs_html = ob_get_clean();
                echo apply_filters('jobsearch_empdash_pckges_list_html', $pkgs_html);
                
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