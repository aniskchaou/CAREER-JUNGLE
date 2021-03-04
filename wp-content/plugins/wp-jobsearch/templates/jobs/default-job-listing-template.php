<?php
/*
 * Job Default page template
 */

// execute short code
wp_enqueue_style('jobsearch-datetimepicker-style');
do_action('jobsearch_jobtemp_instyles_list_aftr');

$output = do_shortcode('[jobsearch_job_shortcode
            job_cat = ""
            job_view = "view-default"
            job_excerpt = "20"
            job_order = "DESC"
            job_orderby = "date"
            job_sort_by = "yes"
            job_pagination = "yes"
            job_per_page = "10"            
            job_filters = "yes"
            job_type = "" ]');
if (wp_is_mobile()) {
    get_header('mobile');
} else {
    get_header();
}

wp_enqueue_script('jobsearch-datetimepicker-script');
wp_enqueue_script('jquery-ui');
wp_enqueue_script('jobsearch-job-functions-script');

wp_enqueue_script('jobsearch-location-autocomplete');
wp_enqueue_script('jobsearch-search-box-sugg');
global $jobsearch_plugin_options;
$location_map_type = isset($jobsearch_plugin_options['location_map_type']) ? $jobsearch_plugin_options['location_map_type'] : '';
if ($location_map_type == 'mapbox') {
    wp_enqueue_script('jobsearch-mapbox');
    wp_enqueue_script('jobsearch-mapbox');
    wp_enqueue_script('jobsearch-mapbox-geocoder');
    wp_enqueue_script('mapbox-geocoder-polyfill');
    wp_enqueue_script('mapbox-geocoder-polyfillauto');
} else {
    wp_enqueue_script('jobsearch-google-map');
    wp_enqueue_script('jobsearch-map-infobox');
    wp_enqueue_script('jobsearch-map-markerclusterer');
}
wp_enqueue_script('jobsearch-shortlist-functions-script');
$plugin_default_view = isset($jobsearch_plugin_options['jobsearch-default-page-view']) ? $jobsearch_plugin_options['jobsearch-default-page-view'] : 'full';
$plugin_default_view_with_str = '';
if ($plugin_default_view == 'boxed') {

    $plugin_default_view_with_str = isset($jobsearch_plugin_options['jobsearch-boxed-view-width']) && $jobsearch_plugin_options['jobsearch-boxed-view-width'] != '' ? $jobsearch_plugin_options['jobsearch-boxed-view-width'] : '1140px';
    if ($plugin_default_view_with_str != '') {
        $plugin_default_view_with_str = ' style="width:' . $plugin_default_view_with_str . '"';
    }
}
?>
<div class="jobsearch-plugin-default-container" <?php echo force_balance_tags($plugin_default_view_with_str); ?>>
    <!--// Main Section \\-->
    <div class="jobsearch-plugin-section"> 
        <?php echo force_balance_tags($output); ?>
    </div>
    <!--// Main Section \\-->
</div>
<?php
get_footer();
