<?php

/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 */
global $post, $jobsearch_plugin_options;
$candidate_id = $post->ID;
jobsearch_candidate_views_count($candidate_id);
do_action('jobsearch_user_profile_before', $candidate_id);
$cand_view = isset($jobsearch_plugin_options['jobsearch_cand_detail_views']) && !empty($jobsearch_plugin_options['jobsearch_cand_detail_views']) ? $jobsearch_plugin_options['jobsearch_cand_detail_views'] : 'view1';
if (wp_is_mobile()) {
    get_header('mobile');
} else {
    get_header();
}

wp_enqueue_script('fancybox-pack');

ob_start();
$cand_view = apply_filters('careerfy_cand_detail_page_style_display', $cand_view, $candidate_id);
jobsearch_get_template_part($cand_view, 'candidate', 'detail-pages/candidate');
$html = ob_get_clean();
echo apply_filters('jobsearch_single_cand_allover_view_html', $html, $candidate_id);

get_footer();
