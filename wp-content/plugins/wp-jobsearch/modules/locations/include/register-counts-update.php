<?php

/*
  Class : Location
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_Tax_Location_Counts_Update {

    // hook things up
    public function __construct() {
//        add_filter('admin_init', array($this, 'update_locations_jobs_count_meta'));
//        add_filter('admin_init', array($this, 'update_locations_emps_count_meta'));
//        add_filter('admin_init', array($this, 'update_locations_cands_count_meta'));
    }

    public function update_locations_jobs_count_meta() {
        $cachetime = 90;
        $transient = 'jobsearch_locs_jobscount_cache';

        $check_transient = get_transient($transient);

        $act_limit = 2500;
        $page_num = get_option('jobsearch_locs_jobscount_cachepnum');
        $page_num = absint($page_num);

        $page_num = $page_num > 20 ? 0 : $page_num;
        
        if (empty($check_transient)) {
            $locs_offset = $page_num * $act_limit;
            $all_locs = jobsearch_get_terms_wlimit('job-location', $act_limit, $locs_offset);
            if (!empty($all_locs) && !is_wp_error($all_locs)) {

                foreach ($all_locs as $term_loc) {
                    $job_args = array(
                        'posts_per_page' => '1',
                        'post_type' => 'job',
                        'post_status' => 'publish',
                        'fields' => 'ids', // only load ids
                        'meta_query' => array(
                            array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'jobsearch_field_location_location1',
                                    'value' => $term_loc->slug,
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'jobsearch_field_location_location2',
                                    'value' => $term_loc->slug,
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'jobsearch_field_location_location3',
                                    'value' => $term_loc->slug,
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'jobsearch_field_location_location4',
                                    'value' => $term_loc->slug,
                                    'compare' => '=',
                                ),
                            ),
                            array(
                                'key' => 'jobsearch_field_job_publish_date',
                                'value' => current_time('timestamp'),
                                'compare' => '<=',
                            ),
                            array(
                                'key' => 'jobsearch_field_job_expiry_date',
                                'value' => current_time('timestamp'),
                                'compare' => '>=',
                            ),
                            array(
                                'key' => 'jobsearch_field_job_status',
                                'value' => 'approved',
                                'compare' => '=',
                            )
                        ),
                    );
                    $jobs_query = new WP_Query($job_args);
                    $found_jobs = $jobs_query->found_posts;
                    wp_reset_postdata();

                    update_term_meta($term_loc->term_id, 'active_jobs_loc_count', absint($found_jobs));
                }
            } else {
                $page_num = -1;
            }
            //

            set_transient($transient, true, $cachetime);

            //
            update_option('jobsearch_locs_jobscount_cachepnum', ($page_num + 1));
        }
    }

    public function update_locations_emps_count_meta() {
        $cachetime = 110;
        $transient = 'jobsearch_locs_empscount_cache';

        $check_transient = get_transient($transient);

        $act_limit = 2500;
        $page_num = get_option('jobsearch_locs_empscount_cachepnum');
        $page_num = absint($page_num);

        $page_num = $page_num > 20 ? 0 : $page_num;
        
        if (empty($check_transient)) {
            $locs_offset = $page_num * $act_limit;
            $all_locs = jobsearch_get_terms_wlimit('job-location', $act_limit, $locs_offset);
            if (!empty($all_locs) && !is_wp_error($all_locs)) {

                foreach ($all_locs as $term_loc) {
                    $job_args = array(
                        'posts_per_page' => '1',
                        'post_type' => 'employer',
                        'post_status' => 'publish',
                        'fields' => 'ids', // only load ids
                        'meta_query' => array(
                            array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'jobsearch_field_location_location1',
                                    'value' => $term_loc->slug,
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'jobsearch_field_location_location2',
                                    'value' => $term_loc->slug,
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'jobsearch_field_location_location3',
                                    'value' => $term_loc->slug,
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'jobsearch_field_location_location4',
                                    'value' => $term_loc->slug,
                                    'compare' => '=',
                                ),
                            ),
                        ),
                    );
                    $jobs_query = new WP_Query($job_args);
                    $found_jobs = $jobs_query->found_posts;
                    wp_reset_postdata();

                    update_term_meta($term_loc->term_id, 'active_employers_loc_count', absint($found_jobs));
                }
            } else {
                $page_num = -1;
            }
            //

            set_transient($transient, true, $cachetime);

            //
            update_option('jobsearch_locs_empscount_cachepnum', ($page_num + 1));
        }
    }

    public function update_locations_cands_count_meta() {
        $cachetime = 130;
        $transient = 'jobsearch_locs_candscount_cache';

        $check_transient = get_transient($transient);

        $act_limit = 2500;
        $page_num = get_option('jobsearch_locs_candscount_cachepnum');
        $page_num = absint($page_num);

        $page_num = $page_num > 20 ? 0 : $page_num;
        
        if (empty($check_transient)) {
            $locs_offset = $page_num * $act_limit;
            $all_locs = jobsearch_get_terms_wlimit('job-location', $act_limit, $locs_offset);
            if (!empty($all_locs) && !is_wp_error($all_locs)) {

                foreach ($all_locs as $term_loc) {
                    $job_args = array(
                        'posts_per_page' => '1',
                        'post_type' => 'candidate',
                        'post_status' => 'publish',
                        'fields' => 'ids', // only load ids
                        'meta_query' => array(
                            array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'jobsearch_field_location_location1',
                                    'value' => $term_loc->slug,
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'jobsearch_field_location_location2',
                                    'value' => $term_loc->slug,
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'jobsearch_field_location_location3',
                                    'value' => $term_loc->slug,
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'jobsearch_field_location_location4',
                                    'value' => $term_loc->slug,
                                    'compare' => '=',
                                ),
                            ),
                        ),
                    );
                    $jobs_query = new WP_Query($job_args);
                    $found_jobs = $jobs_query->found_posts;
                    wp_reset_postdata();

                    update_term_meta($term_loc->term_id, 'active_candidates_loc_count', absint($found_jobs));
                }
            } else {
                $page_num = -1;
            }
            //

            set_transient($transient, true, $cachetime);

            //
            update_option('jobsearch_locs_candscount_cachepnum', ($page_num + 1));
        }
    }

}

return new Jobsearch_Tax_Location_Counts_Update();
