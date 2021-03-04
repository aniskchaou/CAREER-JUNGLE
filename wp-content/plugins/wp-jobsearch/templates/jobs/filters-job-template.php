<?php
global $jobsearch_plugin_options, $jobsearch_jobalertfiltrs_html;
$output = '';
$left_filter_count_switch = 'no';
$sh_atts = isset($job_arg['atts']) ? $job_arg['atts'] : '';
$job_feat_jobs_top = isset($sh_atts['job_feat_jobs_top']) ? $sh_atts['job_feat_jobs_top'] : '';
$job_feats_only = isset($sh_atts['featured_only']) ? $sh_atts['featured_only'] : '';

if (isset($args_count['meta_query']) && $job_feat_jobs_top == 'yes' && $job_feats_only != 'yes') {
    $cou_args_mqury = $args_count['meta_query'];
    $cou_args_mqury = jobsearch_remove_exfeatkeys_jobs_query($cou_args_mqury);
    $args_count['meta_query'] = $cou_args_mqury;
}

//////
if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
    global $sitepress;
    $trans_able_options = $sitepress->get_setting('custom_posts_sync_option', array());
}

//////
$args_count['posts_per_page'] = '-1';
$sector_count_args = $args_count;
$type_count_args = $args_count;
$jobs_loop_obj = new WP_Query($args_count);
$job_totnum = $all_get_posts = $jobs_loop_obj->posts;
if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') && $job_totnum == 0 && isset($trans_able_options['job']) && $trans_able_options['job'] == '2') {
    $sitepress_def_lang = $sitepress->get_default_language();
    $sitepress_curr_lang = $sitepress->get_current_language();
    $sitepress->switch_lang($sitepress_def_lang, true);

    $job_qry = new WP_Query($args_count);

    $all_get_posts = $job_qry->posts;

    //
    $sitepress->switch_lang($sitepress_curr_lang, true);
}
$sector_args_count = $type_args_count = $args_count = $all_get_posts;

$job_types_switch = isset($jobsearch_plugin_options['job_types_switch']) ? $jobsearch_plugin_options['job_types_switch'] : '';

$filters_op_sort = isset($jobsearch_plugin_options['jobs_srch_filtrs_sort']) ? $jobsearch_plugin_options['jobs_srch_filtrs_sort'] : '';

$filters_op_sort = isset($filters_op_sort['fields']) ? $filters_op_sort['fields'] : '';
?>

<div class="jobsearch-column-3 jobsearch-typo-wrap">
    <?php
    if (isset($sh_atts['job_filters_count']) && $sh_atts['job_filters_count'] == 'yes') {
        $left_filter_count_switch = 'yes';
    }
    do_action('jobsearch_jobs_listing_filters_before', array('sh_atts' => $sh_atts), $global_rand_id);

    $filter_sort_by = isset($sh_atts['job_filters_sortby']) ? $sh_atts['job_filters_sortby'] : '';
    
    $mobile_view_flag = false;

    if (wp_is_mobile()) {
        $mobile_view_flag = true;
    }
    if ($mobile_view_flag) {
        ?>

        <div class="jobsearch-mobile-wrap">

            <a href="javascript:void(0);" class="jobsearch-mobile-btn"><?php echo esc_html__('Filter Sorting', 'wp-jobsearch'); ?><i class="careerfy-icon careerfy-up-arrow"></i></a>

            <div class="jobsearch-mobile-section" style="display: none;">   
                <?php
            }
            if (!empty($filters_op_sort)) {
                foreach ($filters_op_sort as $filter_sort_key => $filter_sort_val) {
                    if ($filter_sort_key == 'date_posted') {
                        $output .= apply_filters('jobsearch_job_filter_date_posted_box_html', '', $global_rand_id, $args_count, $left_filter_count_switch, $sh_atts);
                    } else if ($filter_sort_key == 'keyword_search') {
                        $output .= apply_filters('jobsearch_job_filter_keywordsrch_box_html', '', $global_rand_id, $args_count, $left_filter_count_switch, $sh_atts);
                    } else if ($filter_sort_key == 'location') {
                        $output .= apply_filters('jobsearch_job_filter_joblocation_box_html', '', $global_rand_id, $args_count, $left_filter_count_switch, $sh_atts);
                    } else if ($filter_sort_key == 'sector') {
                        $output .= apply_filters('jobsearch_job_filter_sector_box_html', '', $global_rand_id, $sector_args_count, $left_filter_count_switch, $sh_atts);
                    } else if ($filter_sort_key == 'job_type') {
                        if ($job_types_switch != 'off') {
                            $output .= apply_filters('jobsearch_job_filter_jobtype_box_html', '', $global_rand_id, $type_args_count, $left_filter_count_switch, $sh_atts);
                        }
                    } else if ($filter_sort_key == 'custom_fields') {
                        $output .= apply_filters('jobsearch_custom_fields_filter_box_html', '', 'job', $global_rand_id, $args_count, $left_filter_count_switch, 'jobsearch_job_content_load', $filter_sort_by);
                    } else if ($filter_sort_key == 'ads') {
                        $filter_ads_code = isset($jobsearch_plugin_options['jobs_filter_adcode']) ? $jobsearch_plugin_options['jobs_filter_adcode'] : '';
                        if ($filter_ads_code != '') {
                            ob_start();
                            echo do_shortcode($filter_ads_code);
                            $the_ad_code = ob_get_clean();
                            $output .= '<div class="jobsearch-filter-responsive-wrap"><div class="filter-ads-wrap">' . $the_ad_code . '</div></div>';
                        }
                    }
                }
            } else {
                /*
                 * add filter box for job locations filter 
                 */
                $output .= apply_filters('jobsearch_job_filter_joblocation_box_html', '', $global_rand_id, $args_count, $left_filter_count_switch, $sh_atts);
                /*
                 * add filter box for date posted filter 
                 */
                $output .= apply_filters('jobsearch_job_filter_date_posted_box_html', '', $global_rand_id, $args_count, $left_filter_count_switch, $sh_atts);

                /*
                 * add filter box for job types filter 
                 */
                if ($job_types_switch != 'off') {
                    $output .= apply_filters('jobsearch_job_filter_jobtype_box_html', '', $global_rand_id, $args_count, $left_filter_count_switch, $sh_atts);
                }
                /*
                 * add filter box for sectors filter 
                 */
                $output .= apply_filters('jobsearch_job_filter_sector_box_html', '', $global_rand_id, $args_count, $left_filter_count_switch, $sh_atts);
                /*
                 * add filter box for custom fields filter 
                 */
                $output .= apply_filters('jobsearch_custom_fields_filter_box_html', '', 'job', $global_rand_id, $args_count, $left_filter_count_switch, 'jobsearch_job_content_load', $filter_sort_by);
            }
            $jobsearch_jobalertfiltrs_html = apply_filters('jobsearch_job_alerts_filters_html', '', $global_rand_id, $left_filter_count_switch, $sh_atts);
            echo force_balance_tags($output);

            if ($mobile_view_flag) {
                ?>

            </div>
        </div>
    <?php } ?>

    <?php
    ?>
</div>
