<?php

/*
  Plugin Name: WP All Import Wp Jobsearch Add-On
  Description: An add-on for importing data to certain Wp Jobsearch fields.
  Version: 1.4
  Author: Eyecix
 */

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
include "rapid-addon.php";
include('simple_html_dom.php');

define("jobserch_allimprt_filep", plugin_dir_path(dirname(__FILE__)) . 'wp-all-import-jobsearch/');

function wp_jobsearch_drpdwn_options_arr($dropdown_field_options) {

    $drpdwns_arr = array();
    if (isset($dropdown_field_options['value']) && count($dropdown_field_options['value']) > 0) {
        $option_counter = 0;
        foreach ($dropdown_field_options['value'] as $option) {
            if ($option != '') {
                $option = ltrim(rtrim($option));
                if ($dropdown_field_options['label'][$option_counter] != '' && str_replace(" ", "-", $option) != '') {
                    $option_val = strtolower(str_replace(" ", "-", $option));
                    $option_label = $dropdown_field_options['label'][$option_counter];
                    $drpdwns_arr[$option_val] = $option_label;
                }
            }
            $option_counter ++;
        }
    }
    return $drpdwns_arr;
}

function wp_jobsearch_get_seting_term_id($term_tax = 'sector', $term_name = '', $parent = 'no') {
    global $wpdb;
    $term_id = 0;

    $get_db_term_id = $wpdb->get_var($wpdb->prepare("SELECT terms.term_id FROM $wpdb->terms AS terms"
                    . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                    . " WHERE terms.name = %s AND term_tax.taxonomy = %s", $term_name, $term_tax));
    if ($get_db_term_id) {
        //
        $term_id = $get_db_term_id;
    } else {
        $term_slug = sanitize_title_with_dashes($term_name);
        $wpdb->insert($wpdb->prefix . 'terms', array('name' => $term_name, 'slug' => $term_slug, 'term_group' => 0), array('%s', '%s', '%d'));
        $term_insert_id = $wpdb->insert_id;
        if ($parent == 'no') {
            $wpdb->insert($wpdb->prefix . 'term_taxonomy', array('term_id' => $term_insert_id, 'taxonomy' => $term_tax, 'count' => 0), array('%d', '%s', '%d'));
        } else {
            $wpdb->insert($wpdb->prefix . 'term_taxonomy', array('term_id' => $term_insert_id, 'taxonomy' => $term_tax, 'parent' => 0, 'count' => 0), array('%d', '%s', '%d', '%d'));
        }
        $term_id = $term_insert_id;
        clean_term_cache($term_id, $term_tax);
    }
    return $term_id;
}

function wp_jobsearch_users_uploadir_fpath($dir = '') {

    $cus_dir = 'jobsearch-user-files';
    $dir_path = array(
        'path' => $dir['basedir'] . '/' . $cus_dir,
        'url' => $dir['baseurl'] . '/' . $cus_dir,
        'subdir' => $cus_dir,
    );
    return $dir_path + $dir;
}

function wp_jobsearch_uploadatt_post_with_external_url($image_url, $post_ID = 0, $set_post_thumb = false, $file_name = '') {
    add_filter('upload_dir', 'wp_jobsearch_users_uploadir_fpath');
    $upload_dir = wp_upload_dir();
    $image_data = wp_remote_get($image_url, array('timeout' => 60));

    if (isset($image_data['body'])) {
        $image_data = $image_data['body'];
        $filename = basename($image_url);
        if ($file_name != '') {
            $filename = $file_name;
        }
        if (wp_mkdir_p($upload_dir['path'])) {
            $file = $upload_dir['path'] . '/' . $filename;
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }
        @file_put_contents($file, $image_data);

        $wp_filetype = wp_check_filetype($filename, null);
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment($attachment, $file, $post_ID);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
        wp_update_attachment_metadata($attach_id, $attach_data);

        if ($set_post_thumb == true) {
            set_post_thumbnail($post_ID, $attach_id);
        }

        return $attach_id;
    }

    remove_filter('upload_dir', 'wp_jobsearch_users_uploadir_fpath');
}

//
//
if (!function_exists('jobsearch_get_cande_user_id')) {

    function jobsearch_get_cande_user_id($candidate_id = 0) {
        $candidate_user_id = get_post_meta($candidate_id, 'jobsearch_user_id', true);
        $candidate_user_id = $candidate_user_id > 0 ? $candidate_user_id : 0;
        $user_obj = get_user_by('ID', $candidate_user_id);
        if ($user_obj) {
            return $user_obj->ID;
        }
        return false;
    }

}
if (!function_exists('jobsearch_get_user_cande_id')) {

    function jobsearch_get_user_cande_id($user_id = 0) {
        $user_candidate_id = get_user_meta($user_id, 'jobsearch_candidate_id', true);
        $user_candidate_id = $user_candidate_id > 0 ? $user_candidate_id : 0;
        $candidate_obj = get_post($user_candidate_id);
        if ($candidate_obj) {
            return $candidate_obj->ID;
        }
        return false;
    }

}

function wp_jobsearch_get_user_emp_id($user_id = 0) {
    $user_employer_id = get_user_meta($user_id, 'jobsearch_employer_id', true);
    $user_employer_id = $user_employer_id > 0 ? $user_employer_id : 0;
    if ($user_employer_id > 0) {
        $employer_obj = get_post($user_employer_id);
        if ($employer_obj) {
            return $employer_obj->ID;
        }
    }
    return false;
}

// Check if user is employer
function wp_jobsearch_user_is_emp($user_id = 0) {
    $user_employer_id = get_user_meta($user_id, 'jobsearch_employer_id', true);
    $user_employer_id = $user_employer_id > 0 ? $user_employer_id : 0;
    if ($user_employer_id > 0) {
        $employer_obj = get_post($user_employer_id);
        if ($employer_obj && isset($employer_obj->ID)) {
            return true;
        }
    }
    return false;
}

if (!function_exists('jobsearch_find_in_multiarray')) {

    function jobsearch_find_in_multiarray($elem, $array, $field) {

        $top = sizeof($array);
        $k = 0;
        $new_array = array();
        for ($i = 0; $i <= $top; $i ++) {
            if (isset($array[$i])) {
                $new_array[$k] = $array[$i];
                $k ++;
            }
        }
        $array = $new_array;
        $top = sizeof($array) - 1;
        $bottom = 0;

        $finded_index = array();
        if (is_array($array)) {
            while ($bottom <= $top) {
                if (isset($array[$bottom][$field]) && $array[$bottom][$field] == $elem)
                    $finded_index[] = $bottom;
                else
                if (isset($array[$bottom][$field]) && is_array($array[$bottom][$field]))
                    if (jobsearch_find_in_multiarray($elem, ($array[$bottom][$field])))
                        $finded_index[] = $bottom;
                $bottom ++;
            }
        }
        return $finded_index;
    }

}

if (!function_exists('remove_index_from_array')) {

    function remove_index_from_array($array, $index_array) {
        $top = sizeof($index_array) - 1;
        $bottom = 0;
        if (is_array($index_array)) {
            while ($bottom <= $top) {
                unset($array[$index_array[$bottom]]);
                $bottom ++;
            }
        }
        if (!empty($array))
            return array_values($array);
        else
            return $array;
    }

}

if (!function_exists('jobsearch_create_user_meta_list')) {

    function jobsearch_create_user_meta_list($post_id, $list_name, $user_id) {
        $current_timestamp = strtotime(current_time('d-m-Y H:i:s'));
        $existing_list_data = array();
        $existing_list_data = get_user_meta($user_id, $list_name, true);
        if (!is_array($existing_list_data)) {
            $existing_list_data = array();
        }

        if (is_array($existing_list_data)) {
            // search duplicat and remove it then arrange new ordering
            $finded = jobsearch_find_in_multiarray($post_id, $existing_list_data, 'post_id');
            $existing_list_data = remove_index_from_array($existing_list_data, $finded);
            // adding one more entry
            $existing_list_data[] = array('post_id' => $post_id, 'date_time' => $current_timestamp);
            update_user_meta($user_id, $list_name, $existing_list_data);
        }
    }

}

if (!function_exists('jobsearch_allimprt_address_to_cords')) {

    function jobsearch_allimprt_address_to_cords($address = '') {

        $jobsearch__options = get_option('jobsearch_plugin_options');
        $location_map_type = isset($jobsearch__options['location_map_type']) ? $jobsearch__options['location_map_type'] : '';

        if (empty($address)) {
            return false;
        }

        $cords_array = array();
        if ($location_map_type == 'mapbox') {
            $mapbox_access_token = isset($jobsearch__options['mapbox_access_token']) ? $jobsearch__options['mapbox_access_token'] : '';
            $geo_loc_url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/' . urlencode($address) . '.json?access_token=' . $mapbox_access_token;
            $location_geo = wp_remote_get($geo_loc_url);
            if (!is_wp_error($location_geo)) {
                if (isset($location_geo['body'])) {
                    $cords_info = json_decode($location_geo['body'], true);
                    $format_address = isset($cords_info['features'][0]['place_name']) ? $cords_info['features'][0]['place_name'] : '';
                    $cords = isset($cords_info['features'][0]['geometry']['coordinates']) ? $cords_info['features'][0]['geometry']['coordinates'] : '';
                    $latitude = isset($cords[1]) ? $cords[1] : '';
                    $longitude = isset($cords[0]) ? $cords[0] : '';
                    if (!empty($latitude) && !empty($longitude)) {
                        $cords_array['lat'] = $latitude;
                        $cords_array['lng'] = $longitude;
                        $cords_array['formatted_address'] = $format_address;
                    }
                }
            }
        } else {
            $google_api_key = isset($jobsearch__options['jobsearch-google-api-key']) ? $jobsearch__options['jobsearch-google-api-key'] : '';
            $geo_loc_url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=false' . ($google_api_key != '' ? '&key=' . $google_api_key : '');
            $location_geo = wp_remote_get($geo_loc_url);
            if (!is_wp_error($location_geo)) {
                if (isset($location_geo['body'])) {
                    $cords_info = json_decode($location_geo['body'], true);
                    if (isset($cords_info['results']) && empty($cords_info['results'])) {
                        $location_geo = wp_remote_get('https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address));
                    }
                }

                if (isset($location_geo['body'])) {
                    $cords_info = json_decode($location_geo['body'], true);

                    if (isset($cords_info['status']) && $cords_info['status'] == 'OK') {
                        $latitude = isset($cords_info['results'][0]['geometry']['location']['lat']) ? $cords_info['results'][0]['geometry']['location']['lat'] : '';
                        $longitude = isset($cords_info['results'][0]['geometry']['location']['lng']) ? $cords_info['results'][0]['geometry']['location']['lng'] : '';

                        $formatted_address = isset($cords_info['results'][0]['formatted_address']) ? $cords_info['results'][0]['formatted_address'] : '';

                        if (!empty($latitude) && !empty($longitude)) {
                            $cords_array['lat'] = $latitude;
                            $cords_array['lng'] = $longitude;
                            $cords_array['formatted_address'] = $formatted_address;
                        }
                    }
                }
            }
        }

        return $cords_array;
    }

}

function jobsearch_allimport_get_post_id_bytitle($title, $type = 'post') {
    global $wpdb;
    if ($title != '') {
        $post_query = "SELECT posts.ID FROM $wpdb->posts AS posts";
        $post_query .= " WHERE posts.post_title='{$title}' AND posts.post_type='{$type}'";
        $post_query .= " LIMIT 1";
        $get_db_res = $wpdb->get_col($post_query);
        if (isset($get_db_res[0])) {
            return $get_db_res[0];
        }
    }
    return 0;
}

//
//

include "import-jobs.php";
include "import-candidates.php";
include "import-employers.php";
