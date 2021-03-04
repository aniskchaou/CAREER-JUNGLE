<?php
/*
  Class : Location
 */

// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class Jobsearch_Location
{

// hook things up
    public function __construct()
    {
        add_action('jobsearch_admin_location_map', array($this, 'jobsearch_admin_location_map_callback'), 10, 1);
        add_action('jobsearch_dashboard_location_map', array($this, 'jobsearch_dashboard_location_map_callback'), 10, 2);
        //
        add_action('wp_ajax_jobsearch_loc_levels_names_to_address', array($this, 'loc_levels_names_to_address'));
        add_action('wp_ajax_nopriv_jobsearch_loc_levels_names_to_address', array($this, 'loc_levels_names_to_address'));
        //
        add_action('wp_ajax_jobsearch_location_load_location2_data', array($this, 'jobsearch_location_load_location2_data_callback'));
        add_action('wp_ajax_nopriv_jobsearch_location_load_location2_data', array($this, 'jobsearch_location_load_location2_data_callback'));
        //
        add_action('wp_ajax_jobsearch_location_load_cusloc2_data', array($this, 'load_locaton2_data_callback'));
        add_action('wp_ajax_nopriv_jobsearch_location_load_cusloc2_data', array($this, 'load_locaton2_data_callback'));
        //
        add_filter('redux/options/jobsearch_plugin_options/sections', array($this, 'jobsearch_location_plugin_option_fields'));
        add_action('init', array($this, 'titles_translation'));

        add_action('save_post', array($this, 'location_fields_save'), 9999);

        $this->load_files();
    }


    public function location_fields_save($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (isset($_POST['jobsearch_field_location_postalcode'])) {
            $postal_code = $_POST['jobsearch_field_location_postalcode'];
            $old_postal_code = isset($_POST['jobsearch_location_old_postalcode']) ? $_POST['jobsearch_location_old_postalcode'] : '';
            $post_addres = get_post_meta($post_id, 'jobsearch_field_location_address', true);

            $post_addres_parse = explode(', ', $post_addres);
            $post_addres_end = !empty($post_addres_parse) ? end($post_addres_parse) : '';
            if ($post_addres_end == $old_postal_code) {
                $post_addres = str_replace(', ' . $old_postal_code, '', $post_addres);
            }

            if ($post_addres != '') {
                if ($postal_code != '' && strpos($post_addres, $postal_code) === false) {
                    $post_addres .= ', ' . $postal_code;
                    update_post_meta($post_id, 'jobsearch_field_location_address', $post_addres);
                }
            } else {
                if ($postal_code != '') {
                    update_post_meta($post_id, 'jobsearch_field_location_address', $postal_code);
                }
            }
        }
    }

    public function titles_translation()
    {
        global $jobsearch_plugin_options;

        $label_location1 = isset($jobsearch_plugin_options['jobsearch-location-label-location1']) ? $jobsearch_plugin_options['jobsearch-location-label-location1'] : esc_html__('Country', 'wp-jobsearch');
        do_action('wpml_register_single_string', 'JobSearch Options', 'Location First Field - ' . $label_location1, $label_location1);
        $label_location2 = isset($jobsearch_plugin_options['jobsearch-location-label-location2']) ? $jobsearch_plugin_options['jobsearch-location-label-location2'] : esc_html__('State', 'wp-jobsearch');
        do_action('wpml_register_single_string', 'JobSearch Options', 'Location Second Field - ' . $label_location2, $label_location2);
        $label_location3 = isset($jobsearch_plugin_options['jobsearch-location-label-location3']) ? $jobsearch_plugin_options['jobsearch-location-label-location3'] : esc_html__('Region', 'wp-jobsearch');
        do_action('wpml_register_single_string', 'JobSearch Options', 'Location Third Field - ' . $label_location3, $label_location3);
        $label_location4 = isset($jobsearch_plugin_options['jobsearch-location-label-location4']) ? $jobsearch_plugin_options['jobsearch-location-label-location4'] : esc_html__('City', 'wp-jobsearch');
        do_action('wpml_register_single_string', 'JobSearch Options', 'Location Forth Field - ' . $label_location4, $label_location4);
    }

    public function location_front_enqueue_scripts()
    {

    }

    public function load_files()
    {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $all_locations_type = isset($jobsearch__options['all_locations_type']) ? $jobsearch__options['all_locations_type'] : '';
        if ($all_locations_type == 'api') {
            include plugin_dir_path(dirname(__FILE__)) . 'locations/include/location-settings.php';
            include plugin_dir_path(dirname(__FILE__)) . 'locations/include/location-import.php';
            include plugin_dir_path(dirname(__FILE__)) . 'locations/include/location-ajax.php';
            include plugin_dir_path(dirname(__FILE__)) . 'locations/include/locations-vc-hooks.php';
            include plugin_dir_path(dirname(__FILE__)) . 'locations/include/libs/excel-reader.php';
        } else {
            include plugin_dir_path(dirname(__FILE__)) . 'locations/include/register-taxonomy.php';
        }
        include plugin_dir_path(dirname(__FILE__)) . 'locations/include/locations-html.php';
    }

    public function jobsearch_admin_location_map_callback($id = '')
    {
        global $jobsearch_form_fields, $jobsearch_plugin_options, $sitepress, $jobsearch_gdapi_allocation;

        $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';

        if ($all_location_allow == 'on') {

            $mapbox_access_token = isset($jobsearch_plugin_options['mapbox_access_token']) ? $jobsearch_plugin_options['mapbox_access_token'] : '';

            $autocomplete_countries_json = '';
            $autocomplete_def_country = '';
            $autocomplete_countries = isset($jobsearch_plugin_options['restrict_contries_locsugg']) ? $jobsearch_plugin_options['restrict_contries_locsugg'] : '';
            if (!empty($autocomplete_countries) && is_array($autocomplete_countries)) {
                $autocomplete_countries_json = json_encode($autocomplete_countries);
                $autocomplete_def_country = $autocomplete_countries[0];
            }

            $location_map_type = isset($jobsearch_plugin_options['location_map_type']) ? $jobsearch_plugin_options['location_map_type'] : '';
            $all_locations_type = isset($jobsearch_plugin_options['all_locations_type']) ? $jobsearch_plugin_options['all_locations_type'] : '';

            $map_search_locsugg = isset($jobsearch_plugin_options['top_search_locsugg']) ? $jobsearch_plugin_options['top_search_locsugg'] : '';

            $lang_code = '';
            $admin_ajax_url = admin_url('admin-ajax.php');
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $lang_code = $sitepress->get_current_language();
                $admin_ajax_url = add_query_arg(array('lang' => $lang_code), $admin_ajax_url);
            }
            wp_register_script('jobsearch-location-editor', jobsearch_plugin_get_url('modules/locations/js/jobsearch-inline-editor.js'), array('jquery'), '', true);
            wp_register_script('jobsearch-location', jobsearch_plugin_get_url('modules/locations/js/location-functions.js'), array('jquery'), rand(10000, 99999), true);
            // Localize the script
            $jobsearch_location_common_arr = array(
                'plugin_url' => jobsearch_plugin_get_url(),
                'ajax_url' => $admin_ajax_url,
            );

            $switch_location_fields = isset($jobsearch_plugin_options['switch_location_fields']) ? $jobsearch_plugin_options['switch_location_fields'] : '';

            $required_fields_count = isset($jobsearch_plugin_options['jobsearch-location-required-fields-count']) ? $jobsearch_plugin_options['jobsearch-location-required-fields-count'] : 'all';
            $label_location1 = isset($jobsearch_plugin_options['jobsearch-location-label-location1']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location1'], 'JobSearch Options', 'Location First Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location1'], $lang_code) : esc_html__('Country', 'wp-jobsearch');
            $label_location2 = isset($jobsearch_plugin_options['jobsearch-location-label-location2']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location2'], 'JobSearch Options', 'Location Second Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location2'], $lang_code) : esc_html__('State', 'wp-jobsearch');
            $label_location3 = isset($jobsearch_plugin_options['jobsearch-location-label-location3']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location3'], 'JobSearch Options', 'Location Third Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location3'], $lang_code) : esc_html__('Region', 'wp-jobsearch');
            $label_location4 = isset($jobsearch_plugin_options['jobsearch-location-label-location4']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location4'], 'JobSearch Options', 'Location Forth Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location4'], $lang_code) : esc_html__('City', 'wp-jobsearch');

            $default_location = isset($jobsearch_plugin_options['jobsearch-location-default-address']) ? $jobsearch_plugin_options['jobsearch-location-default-address'] : '';

            $allow_full_address = isset($jobsearch_plugin_options['location-allow-full-address']) ? $jobsearch_plugin_options['location-allow-full-address'] : '';

            $allow_postal_code = isset($jobsearch_plugin_options['location_allow_postal_code']) ? $jobsearch_plugin_options['location_allow_postal_code'] : '';

            $allow_location_map = isset($jobsearch_plugin_options['location-allow-map']) ? $jobsearch_plugin_options['location-allow-map'] : '';

            $def_map_zoom = isset($jobsearch_plugin_options['jobsearch-location-map-zoom']) && $jobsearch_plugin_options['jobsearch-location-map-zoom'] > 0 ? absint($jobsearch_plugin_options['jobsearch-location-map-zoom']) : '12';

            $map_styles = isset($jobsearch_plugin_options['jobsearch-location-map-style']) ? $jobsearch_plugin_options['jobsearch-location-map-style'] : '';

            $allow_latlng_fileds = isset($jobsearch_plugin_options['allow_latlng_fileds']) ? $jobsearch_plugin_options['allow_latlng_fileds'] : '';

            wp_localize_script('jobsearch-location', 'jobsearch_location_common_vars', $jobsearch_location_common_arr);
            wp_enqueue_script('jobsearch-location');
            if ($allow_location_map == 'yes') {
                if ($location_map_type == 'mapbox') {
                    wp_enqueue_script('jobsearch-mapbox');
                    wp_enqueue_script('jobsearch-mapbox-geocoder');
                    wp_enqueue_script('mapbox-geocoder-polyfill');
                    wp_enqueue_script('mapbox-geocoder-polyfillauto');
                }
            }
            if ($location_map_type != 'mapbox') {
                wp_enqueue_script('jobsearch-google-map');
            }

            $rand_num = rand(1000000, 99999999);
            $loc_location1 = get_post_meta($id, 'jobsearch_field_location_location1', true);
            $loc_location2 = get_post_meta($id, 'jobsearch_field_location_location2', true);
            $loc_location3 = get_post_meta($id, 'jobsearch_field_location_location3', true);
            $loc_location4 = get_post_meta($id, 'jobsearch_field_location_location4', true);
            //update_post_meta($id, 'jobsearch_field_location_address', '');
            $loc_address = get_post_meta($id, 'jobsearch_field_location_address', true);
            $loc_postalcode = get_post_meta($id, 'jobsearch_field_location_postalcode', true);
            $loc_lat = get_post_meta($id, 'jobsearch_field_location_lat', true);
            $loc_lng = get_post_meta($id, 'jobsearch_field_location_lng', true);
            $loc_zoom = get_post_meta($id, 'jobsearch_field_location_zoom', true);
            $map_height = get_post_meta($id, 'jobsearch_field_map_height', true);
            $marker_image = get_post_meta($id, 'jobsearch_field_marker_image', true);
            if (($loc_lat == '' || $loc_lng == '') && $default_location != '') {

                $loc_geo_cords = jobsearch_address_to_cords($default_location);
                $loc_lat = isset($loc_geo_cords['lat']) ? $loc_geo_cords['lat'] : '';
                $loc_lng = isset($loc_geo_cords['lng']) ? $loc_geo_cords['lng'] : '';
            }

            if ($loc_lat == '' || $loc_lng == '') {
                $loc_lat = '37.090240';
                $loc_lng = '-95.712891';
            }

            if ($map_height == '' || $map_height <= 100) {
                $map_height = 250;
            }
            if ($loc_zoom == '') {
                $loc_zoom = $def_map_zoom;
            }

            if ($all_locations_type != 'api') {
                $please_select = esc_html__('Please select', 'wp-jobsearch');
                $location_location1 = array('' => $please_select . ' ' . $label_location1);
                $location_location2 = array('' => $please_select . ' ' . $label_location2);
                $location_location3 = array('' => $please_select . ' ' . $label_location3);
                $location_location4 = array('' => $please_select . ' ' . $label_location4);
//                $location_obj = get_terms('job-location', array(
//                    'orderby' => 'name',
//                    'order' => 'ASC',
//                    'hide_empty' => 0,
//                    'parent' => 0,
//                ));
                $location_obj = jobsearch_custom_get_terms('job-location');
                foreach ($location_obj as $country_arr) {
                    $location_location1[$country_arr->slug] = $country_arr->name;
                    // get all state, region and city
                    // not neccessory for first load, it will populate on seelct country
                }
            }

            $loc_location1 = jobsearch_esc_html($loc_location1);
            $loc_location2 = jobsearch_esc_html($loc_location2);
            $loc_location3 = jobsearch_esc_html($loc_location3);
            $loc_location4 = jobsearch_esc_html($loc_location4);
            $loc_address = jobsearch_esc_html($loc_address);
            $loc_postalcode = jobsearch_esc_html($loc_postalcode);
            $loc_lat = jobsearch_esc_html($loc_lat);
            $loc_lng = jobsearch_esc_html($loc_lng);
            $loc_zoom = jobsearch_esc_html($loc_zoom);
            $map_height = jobsearch_esc_html($map_height);
            $marker_image = jobsearch_esc_html($marker_image);

            do_action('jobsearch_in_bkloc_sec_before_fields', $id);

            ob_start();
            ?>
            <script type="text/javascript">
                var jobsearch_sloc_country = "<?php echo $loc_location1 ?>";
                var jobsearch_sloc_state = "<?php echo $loc_location2 ?>";
                var jobsearch_sloc_city = "<?php echo $loc_location3 ?>";
                var jobsearch_is_admin = "<?php echo is_admin(); ?>";
            </script>
            <?php if ($all_locations_type != 'api') { ?>
                <div class="jobsearch-element-field"
                     style="display: <?php echo($switch_location_fields == 'on' ? 'inline-block' : 'none') ?>;">
                    <div class="elem-label">
                        <label><?php echo esc_html($label_location1) ?></label>
                    </div>
                    <div class="elem-field">
                        <?php
                        $field_params = array(
                            'classes' => 'location_location1',
                            'id' => 'location_location1_' . $rand_num,
                            'name' => 'location_location1',
                            'options' => $location_location1,
                            'force_std' => $loc_location1,
                            'ext_attr' => ' data-randid="' . $rand_num . '" data-nextfieldelement="' . $please_select . ' ' . $label_location2 . '" data-nextfieldval="' . $loc_location2 . '"',
                        );
                        $jobsearch_form_fields->select_field($field_params);
                        ?>
                    </div>
                </div>
                <?php if ($required_fields_count > 1 || $required_fields_count == 'all') { ?>
                    <div class="jobsearch-element-field"
                         style="display: <?php echo($switch_location_fields == 'on' ? 'inline-block' : 'none') ?>;">
                        <div class="elem-label">
                            <label><?php echo esc_html($label_location2) ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'classes' => 'location_location2',
                                'id' => 'location_location2_' . $rand_num,
                                'name' => 'location_location2',
                                'options' => $location_location2,
                                'force_std' => $loc_location2,
                                'ext_attr' => ' data-randid="' . $rand_num . '" data-nextfieldelement="' . $please_select . ' ' . $label_location3 . '" data-nextfieldval="' . $loc_location3 . '"',
                            );
                            $jobsearch_form_fields->select_field($field_params);
                            ?>
                            <span class="jobsearch-field-loader location_location2_<?php echo absint($rand_num); ?>"></span>
                        </div>
                    </div>
                <?php }
                if ($required_fields_count > 2 || $required_fields_count == 'all') { ?>
                    <div class="jobsearch-element-field"
                         style="display: <?php echo($switch_location_fields == 'on' ? 'inline-block' : 'none') ?>;">
                        <div class="elem-label">
                            <label><?php echo esc_html($label_location3) ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'classes' => 'location_location3',
                                'id' => 'location_location3_' . $rand_num,
                                'name' => 'location_location3',
                                'options' => $location_location3,
                                'force_std' => $loc_location3,
                                'ext_attr' => ' data-randid="' . $rand_num . '" data-nextfieldelement="' . $please_select . ' ' . $label_location4 . '" data-nextfieldval="' . $loc_location4 . '"',
                            );
                            $jobsearch_form_fields->select_field($field_params);
                            ?>
                            <span class="jobsearch-field-loader location_location3_<?php echo absint($rand_num); ?>"></span>
                        </div>
                    </div>
                <?php }
                if ($required_fields_count > 3 || $required_fields_count == 'all') { ?>
                    <div class="jobsearch-element-field"
                         style="display: <?php echo($switch_location_fields == 'on' ? 'inline-block' : 'none') ?>;">
                        <div class="elem-label">
                            <label><?php echo esc_html($label_location4) ?></label>
                        </div>
                        <div class="elem-field">
                            <?php
                            $field_params = array(
                                'classes' => 'location_location4',
                                'id' => 'location_location4_' . $rand_num,
                                'name' => 'location_location4',
                                'options' => $location_location4,
                                'force_std' => $loc_location4,
                                'ext_attr' => ' data-randid="' . $rand_num . '"',
                            );
                            $jobsearch_form_fields->select_field($field_params);
                            ?>
                            <span class="jobsearch-field-loader location_location4_<?php echo absint($rand_num); ?>"></span>
                        </div>
                    </div>
                    <?php
                }
            } else if ($all_locations_type == 'api') {
                wp_enqueue_script('jobsearch-gdlocation-api');
                $jobsearch_locsetin_options = get_option('jobsearch_locsetin_options');
                $api_contries_list = array();

                $api_contries_list = $jobsearch_gdapi_allocation::get_countries();

                $loc_optionstype = isset($jobsearch_locsetin_options['loc_optionstype']) ? $jobsearch_locsetin_options['loc_optionstype'] : '';
                $contry_singl_contry = isset($jobsearch_locsetin_options['contry_singl_contry']) ? $jobsearch_locsetin_options['contry_singl_contry'] : '';
                $contry_order = isset($jobsearch_locsetin_options['contry_order']) ? $jobsearch_locsetin_options['contry_order'] : '';
                $contry_order = $contry_order != '' ? $contry_order : 'alpha';
                $contry_filtring = isset($jobsearch_locsetin_options['contry_filtring']) ? $jobsearch_locsetin_options['contry_filtring'] : '';
                $contry_filtring = $contry_filtring != '' ? $contry_filtring : 'none';
                $contry_filtr_limreslts = isset($jobsearch_locsetin_options['contry_filtr_limreslts']) ? $jobsearch_locsetin_options['contry_filtr_limreslts'] : '';
                $contry_filtr_limreslts = $contry_filtr_limreslts <= 0 ? 1000000 : $contry_filtr_limreslts;
                $contry_filtrinc_contries = isset($jobsearch_locsetin_options['contry_filtrinc_contries']) ? $jobsearch_locsetin_options['contry_filtrinc_contries'] : '';
                $contry_filtrexc_contries = isset($jobsearch_locsetin_options['contry_filtrexc_contries']) ? $jobsearch_locsetin_options['contry_filtrexc_contries'] : '';
                $contry_preselct = isset($jobsearch_locsetin_options['contry_preselct']) ? $jobsearch_locsetin_options['contry_preselct'] : '';
                $contry_preselct = $contry_preselct != '' ? $contry_preselct : 'none';
                $contry_presel_contry = isset($jobsearch_locsetin_options['contry_presel_contry']) ? $jobsearch_locsetin_options['contry_presel_contry'] : '';
                if (empty($api_contries_list)) {
                    $api_contries_list = array();
                }
                // For saved country
                if ($loc_location1 != '' && in_array($loc_location1, $api_contries_list)) {
                    $contry_preselct = 'by_contry';
                    $contry_singl_contry = $contry_presel_contry = array_search($loc_location1, $api_contries_list);
                }
                //
                $continent_group = isset($jobsearch_locsetin_options['continent_group']) ? $jobsearch_locsetin_options['continent_group'] : '';
                $continent_order = isset($jobsearch_locsetin_options['continent_order']) ? $jobsearch_locsetin_options['continent_order'] : '';
                $continent_order = $continent_order != '' ? $continent_order : 'alpha';
                $continent_filter = isset($jobsearch_locsetin_options['continent_filter']) ? $jobsearch_locsetin_options['continent_filter'] : '';
                $continent_filter = $continent_filter != '' ? $continent_filter : 'none';
                $continents_selected = isset($jobsearch_locsetin_options['continents_selected']) ? $jobsearch_locsetin_options['continents_selected'] : '';
                //
                $state_order = isset($jobsearch_locsetin_options['state_order']) ? $jobsearch_locsetin_options['state_order'] : '';
                $state_order = $state_order != '' ? $state_order : 'alpha';
                $state_filtring = isset($jobsearch_locsetin_options['state_filtring']) ? $jobsearch_locsetin_options['state_filtring'] : '';
                $state_filtring = $state_filtring != '' ? $state_filtring : 'none';
                $state_filtr_limreslts = isset($jobsearch_locsetin_options['state_filtr_limreslts']) ? $jobsearch_locsetin_options['state_filtr_limreslts'] : '';
                $state_filtr_limreslts = $state_filtr_limreslts <= 0 ? 1000000 : $state_filtr_limreslts;
                //
                $city_order = isset($jobsearch_locsetin_options['city_order']) ? $jobsearch_locsetin_options['city_order'] : '';
                $city_order = $city_order != '' ? $city_order : 'alpha';
                $city_filtring = isset($jobsearch_locsetin_options['city_filtring']) ? $jobsearch_locsetin_options['city_filtring'] : '';
                $city_filtring = $city_filtring != '' ? $city_filtring : 'none';
                $city_filtr_limreslts = isset($jobsearch_locsetin_options['city_filtr_limreslts']) ? $jobsearch_locsetin_options['city_filtr_limreslts'] : '';
                $city_filtr_limreslts = $city_filtr_limreslts <= 0 ? 1000000 : $city_filtr_limreslts;
                //

                $continents_class = '';
                if ($continent_group == 'on') {
                    $continents_class = ' group-continents';
                    if ($continent_order == 'alpha') {
                        $continents_class .= ' group-order-alpha';
                    } else if ($continent_order == 'by_population') {
                        $continents_class .= ' group-order-pop';
                    } else if ($continent_order == 'north_america') {
                        $continents_class .= ' group-order-na';
                    } else if ($continent_order == 'europe') {
                        $continents_class .= ' group-order-eu';
                    } else if ($continent_order == 'africa') {
                        $continents_class .= ' group-order-af';
                    } else if ($continent_order == 'oceania') {
                        $continents_class .= ' group-order-oc';
                    } else if ($continent_order == 'asia') {
                        $continents_class .= ' group-order-as';
                    } else if ($continent_order == 'rand') {
                        $continents_class .= ' group-order-rand';
                    }

                    //
                    if ($continent_filter == 'by_select' && !empty($continents_selected) && is_array($continents_selected)) {
                        $inc_continents_selected = implode('-', $continents_selected);
                        $continents_class .= ' continent-include-' . $inc_continents_selected;
                    }
                }

                $contries_class = '';
                if ($contry_order == 'alpha') {
                    $contries_class .= ' order-alpha';
                } else if ($contry_order == 'by_population') {
                    $contries_class .= ' order-pop';
                } else if ($contry_order == 'random') {
                    $contries_class .= ' order-rand';
                }
                if ($contry_filtring == 'limt_results' && $contry_filtr_limreslts > 0) {
                    $contries_class .= ' limit-pop-' . absint($contry_filtr_limreslts);
                } else if ($contry_filtring == 'inc_contries' && !empty($contry_filtrinc_contries) && is_array($contry_filtrinc_contries)) {
                    $inc_contries_implist = implode('-', $contry_filtrinc_contries);
                    $contries_class .= ' include-' . $inc_contries_implist;
                } else if ($contry_filtring == 'exc_contries' && !empty($contry_filtrexc_contries) && is_array($contry_filtrexc_contries)) {
                    $exc_contries_implist = implode('-', $contry_filtrexc_contries);
                    $contries_class .= ' exclude-' . $exc_contries_implist;
                }
                if ($contry_preselct == 'by_contry' && $contry_presel_contry != '') {
                    $contries_class .= ' presel-' . $contry_presel_contry;
                } else if ($contry_preselct == 'by_user_ip') {
                    $contries_class .= ' presel-byip';
                }

                //
                $states_class = '';
                if ($state_order == 'alpha') {
                    $states_class .= ' order-alpha';
                } else if ($state_order == 'by_population') {
                    $states_class .= ' order-pop';
                } else if ($state_order == 'random') {
                    $states_class .= ' order-rand';
                }

                //
                $cities_class = '';
                if ($city_order == 'alpha') {
                    $cities_class .= ' order-alpha';
                } else if ($city_order == 'by_population') {
                    $cities_class .= ' order-pop';
                } else if ($city_order == 'random') {
                    $cities_class .= ' order-rand';
                }

                if ($loc_optionstype == '0' || $loc_optionstype == '1') {
                    ?>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('Country', 'wp-jobsearch') ?></label>
                        </div>
                        <div id="jobsearch-gdapilocs-contrycon" data-val="<?php echo($loc_location1) ?>"
                             class="elem-field">
                            <select name="jobsearch_field_location_location1" data-randid="<?php echo($rand_num) ?>"
                                    class="countries selectiz-select" id="countryId">
                                <option value=""><?php esc_html_e('Select Country', 'wp-jobsearch') ?></option>
                            </select>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($loc_optionstype != '4') { ?>
                <div class="jobsearch-element-field">
                    <div class="elem-label">
                        <label><?php esc_html_e('State', 'wp-jobsearch') ?></label>
                    </div>
                    <?php if ($loc_optionstype == '2' || $loc_optionstype == '3') { ?>
                        <input type="hidden" name="jobsearch_field_location_location1" id="countryId"
                               data-randid="<?php echo($rand_num) ?>"
                               value="<?php echo($contry_singl_contry) ?>"/>
                    <?php } ?>
                    <div id="jobsearch-gdapilocs-statecon" data-val="<?php echo($loc_location2) ?>" class="elem-field">
                        <select name="jobsearch_field_location_location2" data-randid="<?php echo($rand_num) ?>"
                                class="states" id="stateId">
                            <option value=""><?php esc_html_e('Select State', 'wp-jobsearch') ?></option>
                        </select>
                    </div>
                </div>
                <?php } ?>
                <?php
                if ($loc_optionstype == '1' || $loc_optionstype == '2' || $loc_optionstype == '4') { ?>
                    <div class="jobsearch-element-field">
                        <div class="elem-label">
                            <label><?php esc_html_e('City', 'wp-jobsearch') ?></label>
                        </div>
                        <div id="jobsearch-gdapilocs-citycon" data-val="<?php echo($loc_location3) ?>"
                             class="elem-field">
                            <select name="jobsearch_field_location_location3" data-randid="<?php echo($rand_num) ?>"
                                    class="cities" id="cityId">
                                <option value=""><?php esc_html_e('Select City', 'wp-jobsearch') ?></option>
                            </select>
                        </div>
                    </div>
                    <?php
                }
            }

            echo apply_filters('jobsearch_post_adminloc_before_full_adres', '', $id);
            ?>
            <div class="jobsearch-element-field" <?php echo(($allow_full_address != 'yes' && $allow_full_address != 'yes_req') ? 'style="display: none;"' : '') ?>>
                <div class="elem-label">
                    <label><?php esc_html_e('Address', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php if ($allow_location_map == 'yes' && $location_map_type == 'mapbox' && $mapbox_access_token != '' && $map_search_locsugg == 'yes') { ?>
                        <div id="jobsearch_location_address_<?php echo($rand_num) ?>" class="mapbox-geocoder-searchtxt"
                             style="display:none;"></div>
                        <div id="jobsearch-locaddres-suggscon" class="jobsearch_searchloc_div">
                            <span class="loc-loader"></span>
                            <input id="jobsearch_lochiden_addr_<?php echo($rand_num) ?>" type="text"
                                   name="jobsearch_field_location_address" value="<?php echo($loc_address) ?>">
                        </div>
                    <?php } else {
                        $field_params = array(
                            'id' => 'jobsearch_location_address_' . $rand_num,
                            'name' => 'location_address',
                            'force_std' => $loc_address,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                    }

                    if ($allow_full_address != 'yes' && $allow_full_address != 'yes_req') {
                        ?>
                        <input id="check_loc_addr_<?php echo($rand_num) ?>" type="hidden" value="">
                        <?php
                    } else {
                        ?>
                        <input id="check_loc_addr_<?php echo($rand_num) ?>" type="hidden"
                               value="<?php echo($loc_address) ?>">
                        <?php
                    }
                    ?>
                </div>
            </div>

            <?php
            $loc_fields_html = ob_get_clean();
            echo apply_filters('jobsearch_admin_loc_address_simpfields', $loc_fields_html, $id, $rand_num);
            ?>

            <div class="jobsearch-element-field" <?php echo($allow_postal_code == 'yes' ? '' : 'style="display: none;"') ?>>
                <div class="elem-label">
                    <label><?php esc_html_e('Postal Code', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <input type="hidden" name="jobsearch_location_old_postalcode"
                           value="<?php echo($loc_postalcode) ?>">
                    <input id="jobsearch_loc_postalcode_<?php echo($rand_num) ?>" type="text"
                           name="jobsearch_field_location_postalcode" value="<?php echo($loc_postalcode) ?>">
                </div>
            </div>
            <?php
            //
            $autocomplete_adres_type = isset($jobsearch_plugin_options['autocomplete_adres_type']) ? $jobsearch_plugin_options['autocomplete_adres_type'] : '';
            ?>

            <div class="jobsearch-element-field" <?php echo($allow_location_map == 'yes' && $allow_latlng_fileds == 'yes' ? '' : 'style="display: none;"') ?>>
                <div class="elem-label">
                    <label><?php esc_html_e('Latitude', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'id' => 'jobsearch_location_lat_' . $rand_num,
                        'name' => 'location_lat',
                        'force_std' => $loc_lat,
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <div class="jobsearch-element-field" <?php echo($allow_location_map == 'yes' && $allow_latlng_fileds == 'yes' ? '' : 'style="display: none;"') ?>>
                <div class="elem-label">
                    <label><?php esc_html_e('Longitude', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'id' => 'jobsearch_location_lng_' . $rand_num,
                        'name' => 'location_lng',
                        'force_std' => $loc_lng,
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <?php
            $show_zoom = '';
            if ($allow_location_map == 'yes' && $allow_latlng_fileds == 'yes') {
                $show_zoom = '';
            } else {
                $show_zoom = 'style="display: none;"';
            }
            if ($mapbox_access_token == '' && $location_map_type == 'mapbox') {
                $show_zoom = 'style="display: none;"';
            }
            ?>
            <div class="jobsearch-element-field" <?php echo($show_zoom) ?>>
                <div class="elem-label">
                    <label><?php esc_html_e('Zoom Level', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'id' => 'jobsearch_location_zoom_' . $rand_num,
                        'name' => 'location_zoom',
                        'force_std' => $loc_zoom,
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <?php
            $show_map_height = '';
            if ($allow_location_map != 'yes') {
                $show_map_height = 'style="display: none;"';
            }
            if ($allow_location_map == 'yes' && $mapbox_access_token == '' && $location_map_type == 'mapbox') {
                $show_map_height = 'style="display: none;"';
            }
            ?>
            <div class="jobsearch-element-field" <?php echo($show_map_height) ?>>
                <div class="elem-label">
                    <label><?php esc_html_e('Map Height', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'name' => 'map_height',
                        'force_std' => $map_height,
                    );
                    $jobsearch_form_fields->input_field($field_params);
                    ?>
                </div>
            </div>
            <?php
            $show_marker = '';
            if ($allow_location_map != 'yes') {
                $show_marker = 'style="display: none;"';
            }
            if ($allow_location_map == 'yes' && $mapbox_access_token == '' && $location_map_type == 'mapbox') {
                $show_marker = 'style="display: none;"';
            }
            ?>
            <div class="jobsearch-element-field" <?php echo($show_marker) ?>>
                <div class="elem-label">
                    <label><?php esc_html_e('Marker', 'wp-jobsearch') ?></label>
                </div>
                <div class="elem-field">
                    <?php
                    $field_params = array(
                        'id' => 'marker_image_' . $rand_num,
                        'name' => 'marker_image',
                        'force_std' => $marker_image,
                    );
                    $jobsearch_form_fields->image_upload_field($field_params);
                    ?>
                </div>
            </div>
            <?php
            if ($allow_location_map != 'yes') {
                $map_height = 0;
            }
            if ($allow_location_map == 'yes' && $mapbox_access_token == '' && $location_map_type == 'mapbox') {
                $map_height = 0;
            }
            ?>
            <div id="jobsearch-map-<?php echo absint($rand_num); ?>"
                 style="width: 100%; height: <?php echo($allow_location_map != 'yes' ? '0' : $map_height) ?>px;"></div>

            <?php
            do_action('jobsearch_in_bkloc_sec_after_fields', $id);
            ?>

            <script>
                <?php
                if ($location_map_type == 'mapbox') {
                $mapbox_access_token = isset($jobsearch_plugin_options['mapbox_access_token']) ? $jobsearch_plugin_options['mapbox_access_token'] : '';
                $mapbox_style_url = isset($jobsearch_plugin_options['mapbox_style_url']) ? $jobsearch_plugin_options['mapbox_style_url'] : '';
                ?>
                var map;
                var currentMarkers;
                <?php
                if ($allow_location_map == 'yes' && $mapbox_access_token != '' && $mapbox_style_url != '') {
                ?>
                jQuery('body').on('click', function (e) {
                    var this_dom = e.target;
                    var thisdom_obj = jQuery(this_dom);
                    if (thisdom_obj.parents('#jobsearch-locaddres-suggscon').length > 0) {
                        //
                    } else {
                        jQuery('#jobsearch-locaddres-suggscon').find('.jobsearch_location_autocomplete').hide();
                    }
                });
                var geocoder<?php echo($rand_num) ?>;
                var _the_adres_input = '';
                jQuery('#jobsearch_lochiden_addr_<?php echo($rand_num) ?>').keyup(function () {
                    _the_adres_input = jQuery(this).val();
                    if (_the_adres_input.length > 1) {
                        geocoder<?php echo($rand_num) ?>.query(_the_adres_input);
                    }
                });
                jQuery(document).ready(function () {
                    mapboxgl.accessToken = '<?php echo($mapbox_access_token) ?>';
                    <?php
                    if (is_rtl()) {
                        ?>
                        mapboxgl.setRTLTextPlugin(
                            'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.2.3/mapbox-gl-rtl-text.js',
                            null,
                            true // Lazy load the plugin
                        );
                        <?php
                    }
                    ?>
                    map = new mapboxgl.Map({
                        container: 'jobsearch-map-<?php echo absint($rand_num); ?>',
                        style: '<?php echo($mapbox_style_url) ?>',
                        center: [<?php echo esc_js($loc_lng) ?>, <?php echo esc_js($loc_lat) ?>],
                        scrollZoom: false,
                        zoom: <?php echo esc_js($loc_zoom) ?>
                    });
                    currentMarkers = [];
                    geocoder<?php echo($rand_num) ?> = new MapboxGeocoder({
                        accessToken: mapboxgl.accessToken,
                        marker: false,
                        //flyTo: false,
                        mapboxgl: mapboxgl
                    });
                    var selected_contries = jobsearch_plugin_vars.sel_countries_json;
                    if (selected_contries != '') {
                        var selected_contries_tojs = jQuery.parseJSON(selected_contries);
                        var sel_countries_str = selected_contries_tojs.join();
                        geocoder<?php echo($rand_num) ?>['countries'] = sel_countries_str;
                    }
                    document.getElementById('jobsearch_location_address_<?php echo($rand_num) ?>').appendChild(geocoder<?php echo($rand_num) ?>.onAdd(map));
                    //
                    var predictionsDropDown = jQuery('<div class="jobsearch_location_autocomplete"></div>').appendTo(jQuery('#jobsearch-locaddres-suggscon'));
                    geocoder<?php echo($rand_num) ?>.on('results', function (predictions) {
                        //console.log(obj);
                        //console.log(obj.features);
                        var predic_line_html = '';
                        if (typeof predictions.features !== 'undefined' && predictions.features.length > 0) {

                            var predFeats = predictions.features;
                            jQuery.each(predFeats, function (i, prediction) {
                                var placename_str = prediction.place_name;
                                var toshow_placename_str = placename_str.split(_the_adres_input).join('<strong>' + _the_adres_input + '</strong>');
                                predic_line_html += '<div class="jobsearch_google_suggestions"><i class="icon-location-arrow"></i> ' + toshow_placename_str + '<span style="display:none">' + placename_str + '</span></div>';
                            });
                        }
                        predictionsDropDown.empty();
                        predictionsDropDown.append(predic_line_html);
                        predictionsDropDown.show();
                    });
                    //
                    predictionsDropDown.delegate('div', 'click', function () {
                        var predic_loc_val = jQuery(this).find('span').html();
                        jQuery('#jobsearch_lochiden_addr_<?php echo($rand_num) ?>').val(predic_loc_val);
                        var map_center_coords = [];
                        var map_addrapi_uri = 'https://api.mapbox.com/geocoding/v5/mapbox.places/' + encodeURI(predic_loc_val) + '.json?access_token=<?php echo($mapbox_access_token) ?>';
                        jobsearch_common_getJSON(map_addrapi_uri, function (new_loc_status, new_loc_response) {
                            if (typeof new_loc_response === 'object') {
                                map_center_coords = new_loc_response.features[0].geometry.coordinates;
                                if (map_center_coords !== 'undefined') {
                                    map.flyTo({
                                        center: map_center_coords,
                                    });
                                }
                            }
                            if (map_center_coords.length > 1) {
                                var map_center_lng = map_center_coords[0];
                                var map_center_lat = map_center_coords[1];
                                document.getElementById("jobsearch_location_lat_<?php echo absint($rand_num); ?>").value = map_center_lat;
                                document.getElementById("jobsearch_location_lng_<?php echo absint($rand_num); ?>").value = map_center_lng;
                                // remove markers
                                if (currentMarkers !== null) {
                                    for (var i = currentMarkers.length - 1; i >= 0; i--) {
                                        currentMarkers[i].remove();
                                    }
                                }
                                //
                                var new_marker = new mapboxgl.Marker({
                                    draggable: true
                                }).setLngLat(map_center_coords).addTo(map);
                                currentMarkers.push(new_marker);
                                new_marker.on('dragend', function () {
                                    var lngLat = new_marker.getLngLat();
                                    document.getElementById("jobsearch_location_lat_<?php echo absint($rand_num); ?>").value = lngLat.lat;
                                    document.getElementById("jobsearch_location_lng_<?php echo absint($rand_num); ?>").value = lngLat.lng;
                                });
                            }
                        });
                        predictionsDropDown.hide();
                    });

                    //
                    map.addControl(new mapboxgl.NavigationControl({
                        showCompass: false
                    }), 'top-right');
                    var marker = new mapboxgl.Marker({
                        draggable: true
                    }).setLngLat([<?php echo esc_js($loc_lng) ?>, <?php echo esc_js($loc_lat) ?>]).addTo(map);
                    currentMarkers.push(marker);

                    function onDragEnd<?php echo absint($rand_num); ?>() {
                        var lngLat = marker.getLngLat();
                        document.getElementById("jobsearch_location_lat_<?php echo absint($rand_num); ?>").value = lngLat.lat;
                        document.getElementById("jobsearch_location_lng_<?php echo absint($rand_num); ?>").value = lngLat.lng;
                    }

                    marker.on('dragend', onDragEnd<?php echo absint($rand_num); ?>);

                    function onZoomMap<?php echo absint($rand_num); ?>(objZoom) {
                        if (typeof objZoom.target._easeOptions.zoom !== 'undefined') {
                            var getZoomLvel = objZoom.target._easeOptions.zoom;
                            document.getElementById("jobsearch_location_zoom_<?php echo absint($rand_num); ?>").value = getZoomLvel;
                        }
                    }

                    map.on('zoom', onZoomMap<?php echo absint($rand_num); ?>);
                });
                <?php
                }
                } else {
                ?>
                var $ = jQuery;
                var map;
                var markers = [];
                var getinAutoAdresFormtd = '';
                jQuery(document).ready(function () {
                    if (typeof google !== 'undefined') {
                        function jobsearch_map_autocomplete_fields_<?php echo($rand_num) ?>() {
                            var autocomplete_input = document.getElementById('jobsearch_location_address_<?php echo($rand_num) ?>');

                            var autcomplete_options = {};
                            <?php
                            if ($autocomplete_adres_type == 'city_contry') {
                            ?>
                            var autcomplete_options = {
                                types: ['(cities)'],
                            };
                            <?php
                            }
                            ?>
                            var selected_contries_json = '';
                            var selected_contries = '<?php echo($autocomplete_countries_json) ?>';
                            if (selected_contries != '') {
                                var selected_contries_tojs = jQuery.parseJSON(selected_contries);
                                selected_contries_json = {country: selected_contries_tojs};
                                autcomplete_options.componentRestrictions = selected_contries_json;
                            }

                            var autocomplete = new google.maps.places.Autocomplete(autocomplete_input, autcomplete_options);
                            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                                var $ = jQuery;
                                var getinAutoAdres = autocomplete.getPlace();
                                getinAutoAdresFormtd = getinAutoAdres.formatted_address;
                                find_on_map<?php echo($rand_num) ?>($('#jobsearch_location_address_<?php echo($rand_num) ?>'), getinAutoAdresFormtd);
                            });
                        }

                        google.maps.event.addDomListener(window, 'load', jobsearch_map_autocomplete_fields_<?php echo($rand_num) ?>);
                        <?php
                        if ($loc_lat != '' && $loc_lng != '' && $loc_zoom != '') { ?>
                        function initMap<?php echo($rand_num) ?>() {
                            var myLatLng = {lat: <?php echo esc_js($loc_lat) ?>, lng: <?php echo esc_js($loc_lng) ?>};
                            map = new google.maps.Map(document.getElementById('jobsearch-map-<?php echo absint($rand_num); ?>'), {
                                zoom: <?php echo esc_js($loc_zoom) ?>,
                                center: myLatLng,
                                streetViewControl: false,
                                scrollwheel: false,
                                mapTypeControl: false,
                            });
                            <?php
                            if ($map_styles != '') {
                            $map_styles = stripslashes($map_styles);
                            $map_styles = preg_replace('/\s+/', ' ', trim($map_styles));
                            ?>
                            var styles = '<?php echo($map_styles) ?>';
                            if (styles != '') {
                                styles = jQuery.parseJSON(styles);
                                var styledMap = new google.maps.StyledMapType(
                                    styles,
                                    {name: 'Styled Map'}
                                );
                                map.mapTypes.set('map_style', styledMap);
                                map.setMapTypeId('map_style');
                            }
                            <?php } ?>

                            var marker = new google.maps.Marker({
                                position: myLatLng,
                                map: map,
                                draggable: true,
                                title: '',
                                icon: '<?php echo esc_js($marker_image) ?>',
                            });

                            markers.push(marker);

                            google.maps.event.addListener(map, 'zoom_changed', function () {
                                var zoom_lvl = map.getZoom();
                                document.getElementById("jobsearch_location_zoom_<?php echo absint($rand_num); ?>").value = zoom_lvl;
                            });
                            google.maps.event.addListener(marker, 'dragend', function (event) {
                                document.getElementById("jobsearch_location_lat_<?php echo absint($rand_num); ?>").value = this.getPosition().lat();
                                document.getElementById("jobsearch_location_lng_<?php echo absint($rand_num); ?>").value = this.getPosition().lng();
                            });
                        }

                        google.maps.event.addDomListener(window, 'load', initMap<?php echo($rand_num) ?>);

                        function find_on_map<?php echo($rand_num) ?>(_this, std_val) {
                            var $ = jQuery;
                            var geocoder = new google.maps.Geocoder();
                            var addres = _this.val();
                            if (typeof std_val !== 'undefined' && std_val != '') {
                                addres = std_val;
                            }
                            var lat_con = $('#jobsearch_location_lat_<?php echo($rand_num) ?>');
                            var lng_con = $('#jobsearch_location_lng_<?php echo($rand_num) ?>');
                            geocoder.geocode({address: addres}, function (results, status) {

                                //alert(addres);
                                if (status == google.maps.GeocoderStatus.OK) {
                                    var new_latitude = results[0].geometry.location.lat();
                                    var new_longitude = results[0].geometry.location.lng();
                                    lat_con.val(new_latitude);
                                    lng_con.val(new_longitude);

                                    //
                                    document.getElementById("jobsearch_location_lat_<?php echo($rand_num) ?>").value = new_latitude;
                                    jQuery('#jobsearch_location_lat_<?php echo($rand_num) ?>').attr('value', new_latitude);
                                    document.getElementById("jobsearch_location_lng_<?php echo($rand_num) ?>").value = new_longitude;
                                    jQuery('#jobsearch_location_lng_<?php echo($rand_num) ?>').attr('value', new_longitude);
                                    //
                                    map.setCenter(results[0].geometry.location);//center the map over the result

                                    // clear markers
                                    for (var i = 0; i < markers.length; i++) {
                                        markers[i].setMap(null);
                                    }

                                    //place a marker at the location
                                    var marker = new google.maps.Marker({
                                        map: map,
                                        position: results[0].geometry.location,
                                        draggable: true,
                                        title: '',
                                        icon: '<?php echo esc_js($marker_image) ?>',
                                    });

                                    markers.push(marker);

                                    google.maps.event.addListener(marker, 'dragend', function (event) {
                                        document.getElementById("jobsearch_location_lat_<?php echo($rand_num) ?>").value = this.getPosition().lat();
                                        document.getElementById("jobsearch_location_lng_<?php echo($rand_num) ?>").value = this.getPosition().lng();
                                    });
                                }
                            });
                        }

                        jQuery(document).on('change', '#jobsearch_location_address_<?php echo($rand_num) ?>', function () {
                            var $ = jQuery;
                            <?php
                            if ($autocomplete_def_country != '') {
                            ?>
                            $(this).val($(this).val() + ' <?php echo($autocomplete_def_country) ?>');
                            <?php
                            }
                            ?>
                            find_on_map<?php echo($rand_num) ?>($(this));
                        });

                        jQuery(document).on('click', '#jobsearch-findmap-<?php echo absint($rand_num); ?>', function (e) {
                            e.preventDefault();
                            find_on_map<?php echo($rand_num) ?>($('#jobsearch_location_address_<?php echo($rand_num) ?>'));
                            false;
                        });
                        <?php } ?>
                    }
                });
                // load state
                <?php
                }
                ?>
            </script>
            <?php
            if ($loc_location1 != '') { ?>
                <script>
                    jQuery(document).ready(function () {
                        if (jQuery('.location_location1').length > 0) {
                            jQuery('.location_location1').trigger('change');
                        }
                    });
                </script>
            <?php }
        }
    }

    public function jobsearch_dashboard_location_map_callback($id = '')
    {

        global $jobsearch_form_fields, $jobsearch_plugin_options, $sitepress, $jobsearch_gdapi_allocation;
        $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
        if ($all_location_allow == 'on') {

            $mapbox_access_token = isset($jobsearch_plugin_options['mapbox_access_token']) ? $jobsearch_plugin_options['mapbox_access_token'] : '';

            $autocomplete_countries_json = '';
            $autocomplete_def_country = '';
            $autocomplete_countries = isset($jobsearch_plugin_options['restrict_contries_locsugg']) ? $jobsearch_plugin_options['restrict_contries_locsugg'] : '';
            if (!empty($autocomplete_countries) && is_array($autocomplete_countries)) {
                $autocomplete_countries_json = json_encode($autocomplete_countries);
                $autocomplete_def_country = $autocomplete_countries[0];
            }

            $location_map_type = isset($jobsearch_plugin_options['location_map_type']) ? $jobsearch_plugin_options['location_map_type'] : '';
            $all_locations_type = isset($jobsearch_plugin_options['all_locations_type']) ? $jobsearch_plugin_options['all_locations_type'] : '';

            $map_search_locsugg = isset($jobsearch_plugin_options['top_search_locsugg']) ? $jobsearch_plugin_options['top_search_locsugg'] : '';

            $lang_code = '';
            $admin_ajax_url = admin_url('admin-ajax.php');
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $lang_code = $sitepress->get_current_language();
                $admin_ajax_url = add_query_arg(array('lang' => $lang_code), $admin_ajax_url);
            }

            wp_register_script('jobsearch-location', jobsearch_plugin_get_url('modules/locations/js/location-functions.js'), array('jquery'), '', true);
            // Localize the script
            $jobsearch_location_common_arr = array(
                'plugin_url' => jobsearch_plugin_get_url(),
                'ajax_url' => $admin_ajax_url,
            );

            $switch_location_fields = isset($jobsearch_plugin_options['switch_location_fields']) ? $jobsearch_plugin_options['switch_location_fields'] : '';
            $required_fields_count = isset($jobsearch_plugin_options['jobsearch-location-required-fields-count']) ? $jobsearch_plugin_options['jobsearch-location-required-fields-count'] : 'all';
            $label_location1 = isset($jobsearch_plugin_options['jobsearch-location-label-location1']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location1'], 'JobSearch Options', 'Location First Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location1'], $lang_code) : esc_html__('Country', 'wp-jobsearch');
            $label_location2 = isset($jobsearch_plugin_options['jobsearch-location-label-location2']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location2'], 'JobSearch Options', 'Location Second Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location2'], $lang_code) : esc_html__('State', 'wp-jobsearch');
            $label_location3 = isset($jobsearch_plugin_options['jobsearch-location-label-location3']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location3'], 'JobSearch Options', 'Location Third Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location3'], $lang_code) : esc_html__('Region', 'wp-jobsearch');
            $label_location4 = isset($jobsearch_plugin_options['jobsearch-location-label-location4']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location4'], 'JobSearch Options', 'Location Forth Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location4'], $lang_code) : esc_html__('City', 'wp-jobsearch');

            $default_location = isset($jobsearch_plugin_options['jobsearch-location-default-address']) ? $jobsearch_plugin_options['jobsearch-location-default-address'] : '';

            $map_styles = isset($jobsearch_plugin_options['jobsearch-location-map-style']) ? $jobsearch_plugin_options['jobsearch-location-map-style'] : '';

            $allow_full_address = isset($jobsearch_plugin_options['location-allow-full-address']) ? $jobsearch_plugin_options['location-allow-full-address'] : '';

            $allow_postal_code = isset($jobsearch_plugin_options['location_allow_postal_code']) ? $jobsearch_plugin_options['location_allow_postal_code'] : '';

            $allow_location_map = isset($jobsearch_plugin_options['location-allow-map']) ? $jobsearch_plugin_options['location-allow-map'] : '';

            $def_map_zoom = isset($jobsearch_plugin_options['jobsearch-location-map-zoom']) && $jobsearch_plugin_options['jobsearch-location-map-zoom'] > 0 ? absint($jobsearch_plugin_options['jobsearch-location-map-zoom']) : '12';

            //
            $loc_firstf_is_req = isset($jobsearch_plugin_options['loc_firstf_is_req']) ? $jobsearch_plugin_options['loc_firstf_is_req'] : '';
            $loc_scndf_is_req = isset($jobsearch_plugin_options['loc_scndf_is_req']) ? $jobsearch_plugin_options['loc_scndf_is_req'] : '';
            $loc_thrdf_is_req = isset($jobsearch_plugin_options['loc_thrdf_is_req']) ? $jobsearch_plugin_options['loc_thrdf_is_req'] : '';
            $loc_forthf_is_req = isset($jobsearch_plugin_options['loc_forthf_is_req']) ? $jobsearch_plugin_options['loc_forthf_is_req'] : '';
            //
            $allow_latlng_fileds = isset($jobsearch_plugin_options['allow_latlng_fileds']) ? $jobsearch_plugin_options['allow_latlng_fileds'] : '';
            //

            wp_localize_script('jobsearch-location', 'jobsearch_location_common_vars', $jobsearch_location_common_arr);
            wp_enqueue_script('jobsearch-location');

            if ($allow_location_map == 'yes') {
                if ($location_map_type == 'mapbox') {
                    wp_enqueue_script('jobsearch-mapbox');
                    wp_enqueue_script('jobsearch-mapbox-geocoder');
                    wp_enqueue_script('mapbox-geocoder-polyfill');
                    wp_enqueue_script('mapbox-geocoder-polyfillauto');
                } else {
                    wp_enqueue_script('jobsearch-google-map');
                }
            }
            $rand_num = rand(1000000, 99999999);
            $loc_location1 = get_post_meta($id, 'jobsearch_field_location_location1', true);

            $loc_location2 = get_post_meta($id, 'jobsearch_field_location_location2', true);

            $loc_location3 = get_post_meta($id, 'jobsearch_field_location_location3', true);
            $loc_location4 = get_post_meta($id, 'jobsearch_field_location_location4', true);
            $loc_address = get_post_meta($id, 'jobsearch_field_location_address', true);
            $loc_postalcode = get_post_meta($id, 'jobsearch_field_location_postalcode', true);
            $loc_lat = get_post_meta($id, 'jobsearch_field_location_lat', true);
            $loc_lng = get_post_meta($id, 'jobsearch_field_location_lng', true);
            $loc_zoom = get_post_meta($id, 'jobsearch_field_location_zoom', true);
            $map_height = get_post_meta($id, 'jobsearch_field_map_height', true);
            $marker_image = get_post_meta($id, 'jobsearch_field_marker_image', true);

            if (isset($_GET['tab']) && $_GET['tab'] == 'user-job' && $id < 1) {
                if (is_user_logged_in()) {
                    $curr_user_id = get_current_user_id();
                    if (jobsearch_user_isemp_member($curr_user_id)) {
                        $employer_id = jobsearch_user_isemp_member($curr_user_id);
                        $curr_user_id = jobsearch_get_employer_user_id($employer_id);
                    } else {
                        $employer_id = jobsearch_get_user_employer_id($curr_user_id);
                    }
                    if ($employer_id) {
                        $loc_location1 = get_post_meta($employer_id, 'jobsearch_field_location_location1', true);
                        $loc_location2 = get_post_meta($employer_id, 'jobsearch_field_location_location2', true);
                        $loc_location3 = get_post_meta($employer_id, 'jobsearch_field_location_location3', true);
                        $loc_location4 = get_post_meta($employer_id, 'jobsearch_field_location_location4', true);
                        $loc_address = get_post_meta($employer_id, 'jobsearch_field_location_address', true);
                        $loc_postalcode = get_post_meta($employer_id, 'jobsearch_field_location_postalcode', true);
                        $loc_lat = get_post_meta($employer_id, 'jobsearch_field_location_lat', true);
                        $loc_lng = get_post_meta($employer_id, 'jobsearch_field_location_lng', true);
                        $loc_zoom = get_post_meta($employer_id, 'jobsearch_field_location_zoom', true);
                        $map_height = get_post_meta($employer_id, 'jobsearch_field_map_height', true);
                        $marker_image = get_post_meta($employer_id, 'jobsearch_field_marker_image', true);
                    }
                }
            }

            $loc_location1 = jobsearch_esc_html($loc_location1);
            $loc_location2 = jobsearch_esc_html($loc_location2);
            $loc_location3 = jobsearch_esc_html($loc_location3);
            $loc_location4 = jobsearch_esc_html($loc_location4);
            $loc_address = jobsearch_esc_html($loc_address);
            $loc_postalcode = jobsearch_esc_html($loc_postalcode);
            $loc_lat = jobsearch_esc_html($loc_lat);
            $loc_lng = jobsearch_esc_html($loc_lng);
            $loc_zoom = jobsearch_esc_html($loc_zoom);
            $map_height = jobsearch_esc_html($map_height);
            $marker_image = jobsearch_esc_html($marker_image);

            if ($allow_full_address == 'no') {
                $loc_address = '';
            }

            if (($loc_lat == '' || $loc_lng == '') && $default_location != '') {

                $loc_geo_cords = jobsearch_address_to_cords($default_location);
                $loc_lat = isset($loc_geo_cords['lat']) ? $loc_geo_cords['lat'] : '';
                $loc_lng = isset($loc_geo_cords['lng']) ? $loc_geo_cords['lng'] : '';
            }

            if ($loc_lat == '' || $loc_lng == '') {
                $loc_lat = '37.090240';
                $loc_lng = '-95.712891';
            }

            if ($map_height == '' || $map_height <= 100) {
                $map_height = 250;
            }
            if ($loc_zoom == '') {
                $loc_zoom = $def_map_zoom;
            }

            if ($all_locations_type != 'api') {
                $please_select = esc_html__('Please select', 'wp-jobsearch');
                $location_location1 = array('' => $please_select . ' ' . $label_location1);
                $location_location2 = array('' => $please_select . ' ' . $label_location2);
                $location_location3 = array('' => $please_select . ' ' . $label_location3);
                $location_location4 = array('' => $please_select . ' ' . $label_location4);
//                $location_obj = get_terms('job-location', array(
//                    'orderby' => 'name',
//                    'order' => 'ASC',
//                    'hide_empty' => 0,
//                    'parent' => 0,
//                ));
                $location_obj = jobsearch_custom_get_terms('job-location');
                foreach ($location_obj as $country_arr) {
                    $location_location1[$country_arr->slug] = $country_arr->name;
                    // get all state, region and city
                    // not neccessory for first load, it will populate on select country
                }
            }

            ?>
            <script type="text/javascript">
                var jobsearch_sloc_country = "<?php echo $loc_location1 ?>";
                var jobsearch_sloc_state = "<?php echo $loc_location2 ?>";
                var jobsearch_sloc_city = "<?php echo $loc_location3 ?>";
                var jobsearch_is_admin = "<?php echo is_admin(); ?>";
            </script>

            <div class="jobsearch-employer-box-section">
                <?php
                ob_start();
                ?>
                <div class="jobsearch-profile-title">
                    <h2><?php esc_html_e('Address / Location', 'wp-jobsearch') ?></h2>
                </div>
                <?php
                $loc_title_html = ob_get_clean();
                echo apply_filters('jobsearch_dash_locfields_title_html', $loc_title_html);

                do_action('jobsearch_in_dashloc_sec_before_fields', $id);
                ?>
                <ul class="jobsearch-row jobsearch-employer-profile-form">
                    <?php
                    do_action('jobsearch_in_dash_loc_simpfields_before', $id);
                    ob_start();

                    if ($all_locations_type != 'api') { ?>
                        <li class="jobsearch-column-6"
                            style="display: <?php echo($switch_location_fields == 'on' ? 'inline-block' : 'none') ?>;">
                            <label><?php echo esc_html($label_location1) ?><?php echo($loc_firstf_is_req == 'yes' ? ' *' : '') ?></label>
                            <div class="jobsearch-profile-select">
                                <?php
                                $field_params = array(
                                    'classes' => 'location_location1 selectize-select' . ($loc_firstf_is_req == 'yes' && $switch_location_fields == 'on' ? ' selectize-req-field' : ''),
                                    'id' => 'location_location1_' . $rand_num,
                                    'name' => 'location_location1',
                                    'options' => $location_location1,
                                    'force_std' => $loc_location1,
                                    'ext_attr' => ' data-randid="' . $rand_num . '" data-nextfieldelement="' . $please_select . ' ' . $label_location2 . '" data-nextfieldval="' . $loc_location2 . '"',
                                );
                                $jobsearch_form_fields->select_field($field_params);
                                ?>
                            </div>
                        </li>
                        <?php
                        if ($required_fields_count > 1 || $required_fields_count == 'all') {
                            ?>
                            <li class="jobsearch-column-6"
                                style="display: <?php echo($switch_location_fields == 'on' ? 'inline-block' : 'none') ?>;">
                                <label><?php echo esc_html($label_location2) ?><?php echo($loc_scndf_is_req == 'yes' ? ' *' : '') ?></label>
                                <div class="jobsearch-profile-select">
                                    <?php
                                    $field_params = array(
                                        'classes' => 'location_location2 location_location2_selectize' . ($loc_scndf_is_req == 'yes' && $switch_location_fields == 'on' ? ' selectize-req-field' : ''),
                                        'id' => 'location_location2_' . $rand_num,
                                        'name' => 'location_location2',
                                        'options' => $location_location2,
                                        'force_std' => $loc_location2,
                                        'ext_attr' => ' data-randid="' . $rand_num . '" data-nextfieldelement="' . $please_select . ' ' . $label_location3 . '" data-nextfieldval="' . $loc_location3 . '"',
                                    );
                                    $jobsearch_form_fields->select_field($field_params);
                                    ?>
                                    <span class="jobsearch-field-loader location_location2_<?php echo absint($rand_num); ?>"></span>
                                </div>
                            </li>
                            <?php
                        }
                        if ($required_fields_count > 2 || $required_fields_count == 'all') { ?>
                            <li class="jobsearch-column-6"
                                style="display: <?php echo($switch_location_fields == 'on' ? 'inline-block' : 'none') ?>;">
                                <label><?php echo esc_html($label_location3) ?><?php echo($loc_thrdf_is_req == 'yes' ? ' *' : '') ?></label>
                                <div class="jobsearch-profile-select">
                                    <?php
                                    $field_params = array(
                                        'classes' => 'location_location3 location_location3_selectize' . ($loc_thrdf_is_req == 'yes' && $switch_location_fields == 'on' ? ' selectize-req-field' : ''),
                                        'id' => 'location_location3_' . $rand_num,
                                        'name' => 'location_location3',
                                        'options' => $location_location3,
                                        'force_std' => $loc_location3,
                                        'ext_attr' => ' data-randid="' . $rand_num . '" data-nextfieldelement="' . $please_select . ' ' . $label_location4 . '" data-nextfieldval="' . $loc_location4 . '"',
                                    );
                                    $jobsearch_form_fields->select_field($field_params);
                                    ?>
                                    <span class="jobsearch-field-loader location_location3_<?php echo absint($rand_num); ?>"></span>
                                </div>
                            </li>
                        <?php }
                        if ($required_fields_count > 3 || $required_fields_count == 'all') { ?>
                            <li class="jobsearch-column-6"
                                style="display: <?php echo($switch_location_fields == 'on' ? 'inline-block' : 'none') ?>;">
                                <label><?php echo esc_html($label_location4) ?><?php echo($loc_forthf_is_req == 'yes' ? ' *' : '') ?></label>
                                <div class="jobsearch-profile-select">
                                    <?php
                                    $field_params = array(
                                        'classes' => 'location_location4 location_location4_selectize' . ($loc_forthf_is_req == 'yes' && $switch_location_fields == 'on' ? ' selectize-req-field' : ''),
                                        'id' => 'location_location4_' . $rand_num,
                                        'name' => 'location_location4',
                                        'options' => $location_location4,
                                        'force_std' => $loc_location4,
                                        'ext_attr' => ' data-randid="' . $rand_num . '"',
                                    );
                                    $jobsearch_form_fields->select_field($field_params);
                                    ?>
                                    <span class="jobsearch-field-loader location_location4_<?php echo absint($rand_num); ?>"></span>
                                </div>
                            </li>
                            <?php
                        }
                    } else {
                        wp_enqueue_script('jobsearch-gdlocation-api');
                        $jobsearch_locsetin_options = get_option('jobsearch_locsetin_options');

                        $api_contries_list = $jobsearch_gdapi_allocation::get_countries();


                        $loc_required_fields = isset($jobsearch_locsetin_options['loc_required_fields']) ? $jobsearch_locsetin_options['loc_required_fields'] : '';
                        $loc_optionstype = isset($jobsearch_locsetin_options['loc_optionstype']) ? $jobsearch_locsetin_options['loc_optionstype'] : '';
                        $contry_singl_contry = isset($jobsearch_locsetin_options['contry_singl_contry']) ? $jobsearch_locsetin_options['contry_singl_contry'] : '';
                        $contry_order = isset($jobsearch_locsetin_options['contry_order']) ? $jobsearch_locsetin_options['contry_order'] : '';
                        $contry_order = $contry_order != '' ? $contry_order : 'alpha';
                        $contry_filtring = isset($jobsearch_locsetin_options['contry_filtring']) ? $jobsearch_locsetin_options['contry_filtring'] : '';
                        $contry_filtring = $contry_filtring != '' ? $contry_filtring : 'none';
                        $contry_filtr_limreslts = isset($jobsearch_locsetin_options['contry_filtr_limreslts']) ? $jobsearch_locsetin_options['contry_filtr_limreslts'] : '';
                        $contry_filtr_limreslts = $contry_filtr_limreslts <= 0 ? 1000000 : $contry_filtr_limreslts;
                        $contry_filtrinc_contries = isset($jobsearch_locsetin_options['contry_filtrinc_contries']) ? $jobsearch_locsetin_options['contry_filtrinc_contries'] : '';
                        $contry_filtrexc_contries = isset($jobsearch_locsetin_options['contry_filtrexc_contries']) ? $jobsearch_locsetin_options['contry_filtrexc_contries'] : '';
                        $contry_preselct = isset($jobsearch_locsetin_options['contry_preselct']) ? $jobsearch_locsetin_options['contry_preselct'] : '';
                        $contry_preselct = $contry_preselct != '' ? $contry_preselct : 'none';
                        $contry_presel_contry = isset($jobsearch_locsetin_options['contry_presel_contry']) ? $jobsearch_locsetin_options['contry_presel_contry'] : '';

                        // For saved country
                        if (empty($api_contries_list)) {
                            $api_contries_list = array();
                        }
                        if ($loc_location1 != '' && in_array($loc_location1, $api_contries_list)) {
                            $contry_preselct = 'by_contry';
                            $contry_singl_contry = $contry_presel_contry = array_search($loc_location1, $api_contries_list);
                        }
                        //
                        $continent_group = isset($jobsearch_locsetin_options['continent_group']) ? $jobsearch_locsetin_options['continent_group'] : '';
                        $continent_order = isset($jobsearch_locsetin_options['continent_order']) ? $jobsearch_locsetin_options['continent_order'] : '';
                        $continent_order = $continent_order != '' ? $continent_order : 'alpha';
                        $continent_filter = isset($jobsearch_locsetin_options['continent_filter']) ? $jobsearch_locsetin_options['continent_filter'] : '';
                        $continent_filter = $continent_filter != '' ? $continent_filter : 'none';
                        $continents_selected = isset($jobsearch_locsetin_options['continents_selected']) ? $jobsearch_locsetin_options['continents_selected'] : '';
                        //
                        $state_order = isset($jobsearch_locsetin_options['state_order']) ? $jobsearch_locsetin_options['state_order'] : '';
                        $state_order = $state_order != '' ? $state_order : 'alpha';
                        $state_filtring = isset($jobsearch_locsetin_options['state_filtring']) ? $jobsearch_locsetin_options['state_filtring'] : '';
                        $state_filtring = $state_filtring != '' ? $state_filtring : 'none';
                        $state_filtr_limreslts = isset($jobsearch_locsetin_options['state_filtr_limreslts']) ? $jobsearch_locsetin_options['state_filtr_limreslts'] : '';
                        $state_filtr_limreslts = $state_filtr_limreslts <= 0 ? 1000000 : $state_filtr_limreslts;
                        //
                        $city_order = isset($jobsearch_locsetin_options['city_order']) ? $jobsearch_locsetin_options['city_order'] : '';
                        $city_order = $city_order != '' ? $city_order : 'alpha';
                        $city_filtring = isset($jobsearch_locsetin_options['city_filtring']) ? $jobsearch_locsetin_options['city_filtring'] : '';
                        $city_filtring = $city_filtring != '' ? $city_filtring : 'none';
                        $city_filtr_limreslts = isset($jobsearch_locsetin_options['city_filtr_limreslts']) ? $jobsearch_locsetin_options['city_filtr_limreslts'] : '';
                        $city_filtr_limreslts = $city_filtr_limreslts <= 0 ? 1000000 : $city_filtr_limreslts;
                        //

                        $continents_class = '';
                        if ($continent_group == 'on') {
                            $continents_class = ' group-continents';
                            if ($continent_order == 'alpha') {
                                $continents_class .= ' group-order-alpha';
                            } else if ($continent_order == 'by_population') {
                                $continents_class .= ' group-order-pop';
                            } else if ($continent_order == 'north_america') {
                                $continents_class .= ' group-order-na';
                            } else if ($continent_order == 'europe') {
                                $continents_class .= ' group-order-eu';
                            } else if ($continent_order == 'africa') {
                                $continents_class .= ' group-order-af';
                            } else if ($continent_order == 'oceania') {
                                $continents_class .= ' group-order-oc';
                            } else if ($continent_order == 'asia') {
                                $continents_class .= ' group-order-as';
                            } else if ($continent_order == 'rand') {
                                $continents_class .= ' group-order-rand';
                            }

                            //
                            if ($continent_filter == 'by_select' && !empty($continents_selected) && is_array($continents_selected)) {
                                $inc_continents_selected = implode('-', $continents_selected);
                                $continents_class .= ' continent-include-' . $inc_continents_selected;
                            }
                        }

                        $contries_class = '';
                        if ($contry_order == 'alpha') {
                            $contries_class .= ' order-alpha';
                        } else if ($contry_order == 'by_population') {
                            $contries_class .= ' order-pop';
                        } else if ($contry_order == 'random') {
                            $contries_class .= ' order-rand';
                        }
                        if ($contry_filtring == 'limt_results' && $contry_filtr_limreslts > 0) {
                            $contries_class .= ' limit-pop-' . absint($contry_filtr_limreslts);
                        } else if ($contry_filtring == 'inc_contries' && !empty($contry_filtrinc_contries) && is_array($contry_filtrinc_contries)) {
                            $inc_contries_implist = implode('-', $contry_filtrinc_contries);
                            $contries_class .= ' include-' . $inc_contries_implist;
                        } else if ($contry_filtring == 'exc_contries' && !empty($contry_filtrexc_contries) && is_array($contry_filtrexc_contries)) {
                            $exc_contries_implist = implode('-', $contry_filtrexc_contries);
                            $contries_class .= ' exclude-' . $exc_contries_implist;
                        }
                        if ($contry_preselct == 'by_contry' && $contry_presel_contry != '') {
                            $contries_class .= ' presel-' . $contry_presel_contry;
                        } else if ($contry_preselct == 'by_user_ip') {
                            $contries_class .= ' presel-byip';
                        }

                        //
                        $states_class = '';
                        if ($state_order == 'alpha') {
                            $states_class .= ' order-alpha';
                        } else if ($state_order == 'by_population') {
                            $states_class .= ' order-pop';
                        } else if ($state_order == 'random') {
                            $states_class .= ' order-rand';
                        }

                        //
                        $cities_class = '';
                        if ($city_order == 'alpha') {
                            $cities_class .= ' order-alpha';
                        } else if ($city_order == 'by_population') {
                            $cities_class .= ' order-pop';
                        } else if ($city_order == 'random') {
                            $cities_class .= ' order-rand';
                        }

                        if ($loc_optionstype == '0' || $loc_optionstype == '1') {
                            ?>
                            <li class="jobsearch-column-6" data-randid="<?php echo($rand_num) ?>">
                                <label><?php esc_html_e('Country', 'wp-jobsearch') ?><?php echo($loc_required_fields == 'yes' ? '*' : '') ?></label>
                                <div id="jobsearch-gdapilocs-contrycon" data-val="<?php echo($loc_location1) ?>"
                                     class="jobsearch-profile-select">
                                    <select name="jobsearch_field_location_location1"
                                            data-randid="<?php echo($rand_num) ?>"
                                            class="countries"
                                            id="countryId">
                                        <option value=""><?php esc_html_e('Select Country', 'wp-jobsearch') ?></option>
                                    </select>
                                </div>
                            </li>
                        <?php } ?>
                        <?php if ($loc_optionstype != '4') { ?>
                            <li class="jobsearch-column-6" data-randid="<?php echo($rand_num) ?>">
                                <label><?php esc_html_e('State', 'wp-jobsearch') ?><?php echo($loc_required_fields == 'yes' ? '*' : '') ?></label>
                                <?php
                                if ($loc_optionstype == '2' || $loc_optionstype == '3') { ?>
                                    <input type="hidden" data-randid="<?php echo($rand_num) ?>"
                                           name="jobsearch_field_location_location1"
                                           id="countryId"
                                           value="<?php echo($contry_singl_contry) ?>"/>
                                <?php } ?>
                                <div id="jobsearch-gdapilocs-statecon" data-val="<?php echo($loc_location2) ?>"
                                     class="jobsearch-profile-select">
                                    <select name="jobsearch_field_location_location2"
                                            data-randid="<?php echo($rand_num) ?>"
                                            class="states"
                                            id="stateId">
                                        <option value=""><?php esc_html_e('Select State', 'wp-jobsearch') ?></option>
                                    </select>
                                </div>
                            </li>
                        <?php } ?>
                        <?php
                        if ($loc_optionstype == '1' || $loc_optionstype == '2' || $loc_optionstype == '4') {
                            ?>
                            <li class="jobsearch-column-6" data-randid="<?php echo($rand_num) ?>">
                                <label><?php esc_html_e('City', 'wp-jobsearch') ?><?php echo($loc_required_fields == 'yes' ? '*' : '') ?></label>
                                <div id="jobsearch-gdapilocs-citycon" data-val="<?php echo($loc_location3) ?>"
                                     class="jobsearch-profile-select">
                                    <select name="jobsearch_field_location_location3"
                                            data-randid="<?php echo($rand_num) ?>"
                                            class="cities<?php echo($cities_class) ?>"
                                            id="cityId">
                                        <option value=""><?php esc_html_e('Select City', 'wp-jobsearch') ?></option>
                                    </select>
                                </div>
                            </li>
                            <?php
                        }
                    }

                    ?>
                    <li class="jobsearch-column-6" <?php echo($allow_postal_code == 'yes' ? '' : 'style="display: none;"') ?>>
                        <label><?php esc_html_e('Postal Code', 'wp-jobsearch') ?></label>
                        <input type="hidden" name="jobsearch_location_old_postalcode"
                               value="<?php echo($loc_postalcode) ?>">
                        <input id="jobsearch_loc_postalcode_<?php echo($rand_num) ?>" type="text"
                               name="jobsearch_field_location_postalcode" value="<?php echo($loc_postalcode) ?>">
                    </li>
                    <?php

                    $full_addr_title = esc_html__('Full Address', 'wp-jobsearch');
                    //
                    if ($allow_full_address == 'yes_req') {
                        $full_addr_title = esc_html__('Full Address *', 'wp-jobsearch');
                    }
                    if ($location_map_type == 'mapbox') {
                        $addrfield_col_size = '12';
                    } else {
                        if ($allow_location_map != 'yes') {
                            $addrfield_col_size = '12';
                        } else {
                            $addrfield_col_size = '10';
                        }
                    }

                    echo apply_filters('jobsearch_post_frontloc_before_full_adres', '', $id);
                    ?>
                    <li class="jobsearch-column-<?php echo($addrfield_col_size) ?>" <?php echo(($allow_full_address != 'yes' && $allow_full_address != 'yes_req') ? 'style="display: none;"' : '') ?>>
                        <label><?php echo($full_addr_title) ?></label>

                        <?php
                        if ($allow_location_map == 'yes' && $location_map_type == 'mapbox' && $mapbox_access_token != '' && $map_search_locsugg == 'yes') { ?>
                            <div id="jobsearch_location_address_<?php echo($rand_num) ?>"
                                 class="mapbox-geocoder-searchtxt" style="display: none;"></div>
                            <div id="jobsearch-locaddres-suggscon" class="jobsearch_searchloc_div">
                                <span class="loc-loader"></span>
                                <input id="jobsearch_lochiden_addr_<?php echo($rand_num) ?>" type="text"
                                       class="<?php echo(($allow_full_address == 'yes_req') ? ' jobsearch-req-field jobsearch-cpreq-field' : '') ?>"
                                       name="jobsearch_field_location_address" value="<?php echo($loc_address) ?>">
                            </div>
                            <?php
                        } else {
                            ?>
                            <input type="text" id="jobsearch_location_address_<?php echo($rand_num) ?>"
                                   name="jobsearch_field_location_address"
                                   class="<?php echo(($allow_full_address == 'yes_req') ? ' jobsearch-req-field jobsearch-cpreq-field' : '') ?>"
                                   placeholder="<?php esc_html_e('Enter a location', 'wp-jobsearch') ?>"
                                   value="<?php echo($loc_address) ?>">
                        <?php } ?>
                        <input id="check_loc_addr_<?php echo($rand_num) ?>" type="hidden"
                               value="<?php echo($loc_address) ?>">
                    </li>

                    <?php
                    if ($location_map_type != 'mapbox') {
                        ?>
                        <li id="find-on-mapbtn" class="jobsearch-column-2" <?php echo(($allow_location_map == 'yes') && ($allow_full_address == 'yes' || $allow_full_address == 'yes_req') ? '' : 'style="display: none;"') ?>>
                            <button id="jobsearch-findmap-<?php echo absint($rand_num); ?>" class="jobsearch-findmap-btn"><?php esc_html_e('Find on Map', 'wp-jobsearch') ?></button>
                        </li>
                        <?php
                    }
                    $loc_fields_html = ob_get_clean();
                    echo apply_filters('jobsearch_dash_loc_address_simpfields', $loc_fields_html, $id, $allow_full_address, $rand_num);
                    ?>

                    <li class="jobsearch-column-4 dash-maploc-latfield" <?php echo($allow_location_map == 'yes' && $allow_latlng_fileds == 'yes' ? '' : 'style="display: none;"') ?>>
                        <label><?php esc_html_e('Latitude', 'wp-jobsearch') ?></label>
                        <?php
                        $field_params = array(
                            'id' => 'jobsearch_location_lat_' . $rand_num,
                            'name' => 'location_lat',
                            'force_std' => $loc_lat,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </li>
                    <li class="jobsearch-column-4 dash-maploc-lngfield" <?php echo($allow_location_map == 'yes' && $allow_latlng_fileds == 'yes' ? '' : 'style="display: none;"') ?>>
                        <label><?php esc_html_e('Longitude', 'wp-jobsearch') ?></label>
                        <?php
                        $field_params = array(
                            'id' => 'jobsearch_location_lng_' . $rand_num,
                            'name' => 'location_lng',
                            'force_std' => $loc_lng,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </li>
                    <li class="jobsearch-column-4 dash-maploc-zoomfield" <?php echo($allow_location_map == 'yes' && $allow_latlng_fileds == 'yes' ? '' : 'style="display: none;"') ?>>
                        <label><?php esc_html_e('Zoom', 'wp-jobsearch') ?></label>
                        <?php
                        $field_params = array(
                            'id' => 'jobsearch_location_zoom_' . $rand_num,
                            'name' => 'location_zoom',
                            'force_std' => $loc_zoom,
                        );
                        $jobsearch_form_fields->input_field($field_params);
                        ?>
                    </li>
                    <?php
                    $map_con_style = '';
                    if ($allow_location_map != 'yes') {
                        $map_con_style = 'style="display: none;"';
                    }
                    if ($allow_location_map == 'yes' && $mapbox_access_token == '' && $location_map_type == 'mapbox') {
                        $map_con_style = 'style="display: none;"';
                    }
                    ?>
                    <li class="jobsearch-column-12" <?php echo($map_con_style) ?>>
                        <div class="jobsearch-profile-map">
                            <div id="jobsearch-map-<?php echo absint($rand_num); ?>"
                                 style="width: 100%; height: <?php echo($allow_location_map != 'yes' ? '0' : $map_height) ?>px;"></div>
                        </div>
                        <span class="jobsearch-short-message" <?php echo($allow_location_map != 'yes' ? 'style="display: none;"' : '') ?>><?php esc_html_e('For the precise location, you can drag and drop the pin.', 'wp-jobsearch') ?></span>
                    </li>
                </ul>
                <?php
                do_action('jobsearch_in_dashloc_sec_after_fields', $id);
                ?>
            </div>
            <?php
            if ($allow_location_map == 'yes') {
                $autocomplete_adres_type = isset($jobsearch_plugin_options['autocomplete_adres_type']) ? $jobsearch_plugin_options['autocomplete_adres_type'] : '';
                ?>
                <script>
                    <?php
                    if ($location_map_type == 'mapbox') {
                    $mapbox_access_token = isset($jobsearch_plugin_options['mapbox_access_token']) ? $jobsearch_plugin_options['mapbox_access_token'] : '';
                    $mapbox_style_url = isset($jobsearch_plugin_options['mapbox_style_url']) ? $jobsearch_plugin_options['mapbox_style_url'] : '';
                    ?>
                    var map;
                    var currentMarkers;
                    <?php
                    if ($allow_location_map == 'yes' && $mapbox_access_token != '' && $mapbox_style_url != '') {
                    ?>
                    jQuery('body').on('click', function (e) {
                        var this_dom = e.target;
                        var thisdom_obj = jQuery(this_dom);
                        if (thisdom_obj.parents('#jobsearch-locaddres-suggscon').length > 0) {
                            //
                        } else {
                            jQuery('#jobsearch-locaddres-suggscon').find('.jobsearch_location_autocomplete').hide();
                        }
                    });
                    var geocoder<?php echo($rand_num) ?>;
                    var _the_adres_input = '';
                    jQuery('#jobsearch_lochiden_addr_<?php echo($rand_num) ?>').keyup(function () {
                        _the_adres_input = jQuery(this).val();
                        if (_the_adres_input.length > 1) {
                            geocoder<?php echo($rand_num) ?>.query(_the_adres_input);
                        }
                    });
                    jQuery(document).ready(function () {
                        mapboxgl.accessToken = '<?php echo($mapbox_access_token) ?>';
                        <?php
                        if (is_rtl()) {
                            ?>
                            mapboxgl.setRTLTextPlugin(
                                'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.2.3/mapbox-gl-rtl-text.js',
                                null,
                                true // Lazy load the plugin
                            );
                            <?php
                        }
                        ?>
                        map = new mapboxgl.Map({
                            container: 'jobsearch-map-<?php echo absint($rand_num); ?>',
                            style: '<?php echo($mapbox_style_url) ?>',
                            center: [<?php echo esc_js($loc_lng) ?>, <?php echo esc_js($loc_lat) ?>],
                            scrollZoom: false,
                            zoom: <?php echo esc_js($loc_zoom) ?>
                        });
                        currentMarkers = [];
                        geocoder<?php echo($rand_num) ?> = new MapboxGeocoder({
                            accessToken: mapboxgl.accessToken,
                            marker: false,
                            //flyTo: false,
                            mapboxgl: mapboxgl
                        });
                        var selected_contries = jobsearch_plugin_vars.sel_countries_json;
                        if (selected_contries != '') {
                            var selected_contries_tojs = jQuery.parseJSON(selected_contries);
                            var sel_countries_str = selected_contries_tojs.join();
                            geocoder<?php echo($rand_num) ?>['countries'] = sel_countries_str;
                        }
                        document.getElementById('jobsearch_location_address_<?php echo($rand_num) ?>').appendChild(geocoder<?php echo($rand_num) ?>.onAdd(map));

                        //
                        var predictionsDropDown = jQuery('<div class="jobsearch_location_autocomplete"></div>').appendTo(jQuery('#jobsearch-locaddres-suggscon'));
                        geocoder<?php echo($rand_num) ?>.on('results', function (predictions) {
                            //console.log(obj);
                            //console.log(obj.features);
                            var predic_line_html = '';
                            if (typeof predictions.features !== 'undefined' && predictions.features.length > 0) {

                                var predFeats = predictions.features;
                                jQuery.each(predFeats, function (i, prediction) {
                                    var placename_str = prediction.place_name;
                                    var toshow_placename_str = placename_str.split(_the_adres_input).join('<strong class="jobsearch-color">' + _the_adres_input + '</strong>');
                                    predic_line_html += '<div class="jobsearch_google_suggestions"><i class="icon-location-arrow"></i> ' + toshow_placename_str + '<span style="display:none">' + placename_str + '</span></div>';
                                });
                            }
                            predictionsDropDown.empty();
                            predictionsDropDown.append(predic_line_html);
                            predictionsDropDown.show();
                        });
                        //
                        predictionsDropDown.delegate('div', 'click', function () {
                            var predic_loc_val = jQuery(this).find('span').html();
                            jQuery('#jobsearch_lochiden_addr_<?php echo($rand_num) ?>').val(predic_loc_val);
                            var map_center_coords = [];
                            var map_addrapi_uri = 'https://api.mapbox.com/geocoding/v5/mapbox.places/' + encodeURI(predic_loc_val) + '.json?access_token=<?php echo($mapbox_access_token) ?>';
                            jobsearch_common_getJSON(map_addrapi_uri, function (new_loc_status, new_loc_response) {
                                if (typeof new_loc_response === 'object') {
                                    map_center_coords = new_loc_response.features[0].geometry.coordinates;
                                    if (map_center_coords !== 'undefined') {
                                        map.flyTo({
                                            center: map_center_coords,
                                        });
                                    }
                                }
                                if (map_center_coords.length > 1) {
                                    var map_center_lng = map_center_coords[0];
                                    var map_center_lat = map_center_coords[1];
                                    document.getElementById("jobsearch_location_lat_<?php echo absint($rand_num); ?>").value = map_center_lat;
                                    document.getElementById("jobsearch_location_lng_<?php echo absint($rand_num); ?>").value = map_center_lng;
                                    // remove markers
                                    if (currentMarkers !== null) {
                                        for (var i = currentMarkers.length - 1; i >= 0; i--) {
                                            currentMarkers[i].remove();
                                        }
                                    }
                                    //
                                    var new_marker = new mapboxgl.Marker({
                                        draggable: true
                                    }).setLngLat(map_center_coords).addTo(map);
                                    currentMarkers.push(new_marker);
                                    new_marker.on('dragend', function () {
                                        var lngLat = new_marker.getLngLat();
                                        document.getElementById("jobsearch_location_lat_<?php echo absint($rand_num); ?>").value = lngLat.lat;
                                        document.getElementById("jobsearch_location_lng_<?php echo absint($rand_num); ?>").value = lngLat.lng;
                                    });
                                }
                            });
                            predictionsDropDown.hide();
                        });

                        //
                        map.addControl(new mapboxgl.NavigationControl({
                            showCompass: false
                        }), 'top-right');
                        var marker = new mapboxgl.Marker({
                            draggable: true
                        }).setLngLat([<?php echo esc_js($loc_lng) ?>, <?php echo esc_js($loc_lat) ?>]).addTo(map);
                        currentMarkers.push(marker);

                        function onDragEnd<?php echo absint($rand_num); ?>() {
                            var lngLat = marker.getLngLat();
                            document.getElementById("jobsearch_location_lat_<?php echo absint($rand_num); ?>").value = lngLat.lat;
                            document.getElementById("jobsearch_location_lng_<?php echo absint($rand_num); ?>").value = lngLat.lng;
                        }

                        marker.on('dragend', onDragEnd<?php echo absint($rand_num); ?>);

                        function onZoomMap<?php echo absint($rand_num); ?>(objZoom) {
                            if (typeof objZoom.target._easeOptions.zoom !== 'undefined') {
                                var getZoomLvel = objZoom.target._easeOptions.zoom;
                                document.getElementById("jobsearch_location_zoom_<?php echo absint($rand_num); ?>").value = getZoomLvel;
                            }
                        }

                        map.on('zoom', onZoomMap<?php echo absint($rand_num); ?>);
                    });
                    <?php
                    }
                    } else {
                    ?>
                    var map;
                    var getinAutoAdresFormtd = '';
                    var markers = [];
                    jQuery(document).ready(function () {
                        function jobsearch_map_autocomplete_fields_<?php echo($rand_num) ?>() {
                            var autocomplete_input = document.getElementById('jobsearch_location_address_<?php echo($rand_num) ?>');
                            var autcomplete_options = {};
                            <?php
                            if ($autocomplete_adres_type == 'city_contry') {
                            ?>
                            var autcomplete_options = {
                                types: ['(cities)'],
                            };
                            <?php
                            }
                            ?>
                            var selected_contries_json = '';
                            var selected_contries = '<?php echo($autocomplete_countries_json) ?>';
                            if (selected_contries != '') {
                                var selected_contries_tojs = jQuery.parseJSON(selected_contries);
                                selected_contries_json = {country: selected_contries_tojs};
                                autcomplete_options.componentRestrictions = selected_contries_json;
                            }

                            var autocomplete = new google.maps.places.Autocomplete(autocomplete_input, autcomplete_options);
                            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                                var getinAutoAdres = autocomplete.getPlace();
                                getinAutoAdresFormtd = getinAutoAdres.formatted_address;
                                find_on_map<?php echo($rand_num) ?>($('#jobsearch_location_address_<?php echo($rand_num) ?>'), getinAutoAdresFormtd);
                            });
                        }

                        //google.maps.event.addDomListener(window, 'load', jobsearch_map_autocomplete_fields_<?php echo($rand_num) ?>);
                        jobsearch_map_autocomplete_fields_<?php echo($rand_num) ?>();
                        <?php
                        if ($loc_lat != '' && $loc_lng != '' && $loc_zoom != '') {
                        ?>
                        function initMap<?php echo($rand_num) ?>() {
                            var myLatLng = {lat: <?php echo esc_js($loc_lat) ?>, lng: <?php echo esc_js($loc_lng) ?>};
                            map = new google.maps.Map(document.getElementById('jobsearch-map-<?php echo absint($rand_num); ?>'), {
                                zoom: <?php echo esc_js($loc_zoom) ?>,
                                center: myLatLng,
                                streetViewControl: false,
                                scrollwheel: false,
                                mapTypeControl: false,
                            });
                            <?php
                            if ($map_styles != '') {
                            $map_styles = stripslashes($map_styles);
                            $map_styles = preg_replace('/\s+/', ' ', trim($map_styles));
                            ?>
                            var styles = '<?php echo($map_styles) ?>';
                            if (styles != '') {
                                styles = jQuery.parseJSON(styles);
                                var styledMap = new google.maps.StyledMapType(
                                    styles,
                                    {name: 'Styled Map'}
                                );
                                map.mapTypes.set('map_style', styledMap);
                                map.setMapTypeId('map_style');
                            }
                            <?php
                            }
                            ?>

                            var marker = new google.maps.Marker({
                                position: myLatLng,
                                map: map,
                                draggable: true,
                                title: '',
                                icon: '<?php echo esc_js($marker_image) ?>',
                            });

                            markers.push(marker);

                            google.maps.event.addListener(map, 'zoom_changed', function () {
                                var zoom_lvl = map.getZoom();
                                document.getElementById("jobsearch_location_zoom_<?php echo absint($rand_num); ?>").value = zoom_lvl;
                            });
                            google.maps.event.addListener(marker, 'dragend', function (event) {
                                document.getElementById("jobsearch_location_lat_<?php echo absint($rand_num); ?>").value = this.getPosition().lat();
                                document.getElementById("jobsearch_location_lng_<?php echo absint($rand_num); ?>").value = this.getPosition().lng();
                            });
                            
                            // check for google geocoder
                            var geocodr_chek_loc = 'London, United Kingdom';
                            var geocoder_chek = new google.maps.Geocoder();
                            geocoder_chek.geocode({address: geocodr_chek_loc}, function (results, status) {
                                if (status == google.maps.GeocoderStatus.OK) {
                                    //console.log(results);
                                } else {
                                    jQuery('#find-on-mapbtn').hide();
                                }
                            });
                        }

                        //google.maps.event.addDomListener(window, 'load', initMap<?php echo($rand_num) ?>);
                        initMap<?php echo($rand_num) ?>();

                        function find_on_map<?php echo($rand_num) ?>(_this, std_val) {
                            var $ = jQuery;
                            var geocoder = new google.maps.Geocoder();
                            var addres = _this.val();

                            if (typeof std_val !== 'undefined' && std_val != '') {
                                addres = std_val;
                            }
                            var lat_con = $('#jobsearch_location_lat_<?php echo($rand_num) ?>');
                            var lng_con = $('#jobsearch_location_lng_<?php echo($rand_num) ?>');
                            geocoder.geocode({address: addres}, function (results, status) {
                                if (status == google.maps.GeocoderStatus.OK) {
                                    var new_latitude = results[0].geometry.location.lat();
                                    var new_longitude = results[0].geometry.location.lng();
                                    lat_con.val(new_latitude);
                                    lng_con.val(new_longitude);
                                    map.setCenter(results[0].geometry.location);//center the map over the result

                                    //
                                    document.getElementById("jobsearch_location_lat_<?php echo($rand_num) ?>").value = new_latitude;
                                    jQuery('#jobsearch_location_lat_<?php echo($rand_num) ?>').attr('value', new_latitude);
                                    document.getElementById("jobsearch_location_lng_<?php echo($rand_num) ?>").value = new_longitude;
                                    jQuery('#jobsearch_location_lng_<?php echo($rand_num) ?>').attr('value', new_longitude);
                                    //

                                    // clear markers
                                    for (var i = 0; i < markers.length; i++) {
                                        markers[i].setMap(null);
                                    }
                                    //

                                    //place a marker at the location
                                    var marker = new google.maps.Marker({
                                        map: map,
                                        position: results[0].geometry.location,
                                        draggable: true,
                                        title: '',
                                        icon: '<?php echo esc_js($marker_image) ?>',
                                    });

                                    markers.push(marker);

                                    google.maps.event.addListener(marker, 'dragend', function (event) {
                                        document.getElementById("jobsearch_location_lat_<?php echo($rand_num) ?>").value = this.getPosition().lat();
                                        document.getElementById("jobsearch_location_lng_<?php echo($rand_num) ?>").value = this.getPosition().lng();
                                    });
                                }
                            });
                        }

                        jQuery(document).on('change', '#jobsearch_location_address_<?php echo($rand_num) ?>', function () {
                            var $ = jQuery;
                            <?php
                            if ($autocomplete_def_country != '') {
                            ?>
                            $(this).val($(this).val() + ' <?php echo($autocomplete_def_country) ?>');
                            <?php
                            }
                            ?>
                            find_on_map<?php echo($rand_num) ?>($(this));
                        });

                        jQuery(document).on('click', '#jobsearch-findmap-<?php echo absint($rand_num); ?>', function (e) {
                            e.preventDefault();
                            find_on_map<?php echo($rand_num) ?>($('#jobsearch_location_address_<?php echo($rand_num) ?>'));
                            false;
                        });
                        <?php
                        }
                        ?>
                    });
                    <?php
                    }
                    ?>
                </script>
                <?php
            }
            ?>
            <script>
                jQuery(document).ready(function () {
                    if (jQuery('.location_location1').length > 0) {
                        jQuery('.location_location1').trigger('change');
                    }
                });
            </script>
            <?php
        }
    }

    public function loc_levels_names_to_address()
    {
        $loc_loc_1 = isset($_POST['loc_loc_1']) ? $_POST['loc_loc_1'] : '';
        $loc_loc_2 = isset($_POST['loc_loc_2']) ? $_POST['loc_loc_2'] : '';
        $loc_loc_3 = isset($_POST['loc_loc_3']) ? $_POST['loc_loc_3'] : '';

        $job_city_title = '';
        $get_job_city = $loc_loc_2;
        if ($get_job_city == '' && $loc_loc_3 != '') {
            $get_job_city = $loc_loc_3;
        }

        $get_job_country = $loc_loc_1;

        $job_city_tax = $get_job_city != '' ? jobsearch_get_custom_term_by('slug', $get_job_city, 'job-location') : '';
        if ($get_job_city != '' && is_object($job_city_tax)) {
            $job_city_title = isset($job_city_tax->name) ? $job_city_tax->name : '';

            $job_country_tax = $get_job_country != '' ? jobsearch_get_custom_term_by('slug', $get_job_country, 'job-location') : '';
            if (is_object($job_country_tax)) {
                $job_city_title .= isset($job_country_tax->name) ? ', ' . $job_country_tax->name : '';
            }
        } else if ($job_city_title == '') {
            $job_country_tax = $get_job_country != '' ? jobsearch_get_custom_term_by('slug', $get_job_country, 'job-location') : '';
            if (is_object($job_country_tax)) {
                $job_city_title .= isset($job_country_tax->name) ? $job_country_tax->name : '';
            }
        }

        echo json_encode(array('locadres' => $job_city_title));
        die;
    }

    public function jobsearch_location_load_location2_data_callback()
    {
        $html = '';
        $nextfieldelement = $_POST['nextfieldelement'];
        $nextfieldval = $_POST['nextfieldval'];
        $html .= "<option value=\"\">" . $nextfieldelement . "</option>" . "\n";
        if (isset($_POST['location_location']) && $_POST['location_location'] != '') {
            $location = $_POST['location_location'];
            //$term = get_term_by('slug', $location, 'job-location');
            $term = jobsearch_get_custom_term_by('slug', $location);

            if (!empty($term)) {

//                $location_obj = get_terms('job-location', array(
//                    'orderby' => 'name',
//                    'order' => 'ASC',
//                    'hide_empty' => 0,
//                    'parent' => $term->term_id,
//                ));
                $term_parent = $term->term_id;
                $location_obj = jobsearch_custom_get_terms('job-location', $term_parent);

                if (!empty($location_obj)) {
                    foreach ($location_obj as $country_arr) {
                        $selected = $country_arr->slug == $nextfieldval ? ' selected="selected"' : '';
                        $html .= "<option{$selected} value=\"{$country_arr->slug}\">{$country_arr->name}</option>" . "\n";
                    }
                }
            }
        }
        echo json_encode(array('html' => $html));

        wp_die();
    }

    public function load_locaton2_data_callback()
    {
        global $jobsearch_plugin_options, $sitepress;

        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }

        $html = '';
        $nextfieldelement = $_POST['nextfieldelement'];
        $nextfieldval = $_POST['nextfieldval'];

        $please_select = esc_html__('Please select', 'wp-jobsearch');
        $label_location3 = isset($jobsearch_plugin_options['jobsearch-location-label-location3']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location3'], 'JobSearch Options', 'Location Third Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location3'], $lang_code) : esc_html__('Region', 'wp-jobsearch');
        $loc_location3 = isset($_REQUEST['location_location3']) ? $_REQUEST['location_location3'] : '';

        ob_start();
        if (isset($_POST['location_location']) && $_POST['location_location'] != '') {
            $location = $_POST['location_location'];
            $rand_num = $_POST['randid'];
            //$term = get_term_by('slug', $location, 'job-location');
            $term = jobsearch_get_custom_term_by('slug', $location);

            if (!empty($term)) {

                $term_parent = $term->term_id;
                $location_obj = jobsearch_custom_get_terms('job-location', $term_parent);

                if (!empty($location_obj)) { ?>
                    <select id="location_location2_<?php echo($rand_num) ?>" name="location_location2"
                            class="location_location2" data-randid="<?php echo($rand_num) ?>"
                            data-nextfieldelement="<?php echo($please_select . ' ' . $label_location3) ?>"
                            data-nextfieldval="<?php echo($loc_location3) ?>">
                        <?php
                        echo "<option value=\"\">" . $nextfieldelement . "</option>" . "\n";
                        foreach ($location_obj as $country_arr) {
                            $selected = $country_arr->slug == $nextfieldval ? ' selected="selected"' : ''; ?>
                            <option <?php echo($selected) ?>
                                    value="<?php echo($country_arr->slug) ?>"><?php echo($country_arr->name) ?></option>
                        <?php } ?>
                    </select>
                <?php } else { ?>
                    <select id="location_location2_<?php echo($rand_num) ?>" name="location_location2"
                            class="location_location2" data-randid="<?php echo($rand_num) ?>"
                            data-nextfieldelement="<?php echo($please_select . ' ' . $label_location3) ?>"
                            data-nextfieldval="<?php echo($loc_location3) ?>">
                        <?php
                        echo "<option value=\"\">" . $nextfieldelement . "</option>" . "\n";
                        ?>
                    </select>
                    <?php
                }
            }
        }
        $html = ob_get_clean();
        echo json_encode(array('html' => $html));

        wp_die();
    }

    public function jobsearch_location_plugin_option_fields($sections)
    {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        WP_Filesystem();
        global $wp_filesystem;
        $countries_file = jobsearch_plugin_get_path('modules/import-locations/data/countries.json');
        $get_json_data = $wp_filesystem->get_contents($countries_file);
        $countries_data = json_decode($get_json_data, true);
        $all_countries_data = isset($countries_data['countries']) ? $countries_data['countries'] : array();

        $res_countries_list = array();
        if (!empty($all_countries_data)) {
            foreach ($all_countries_data as $country_data) {
                $res_countries_list[$country_data['sortname']] = $country_data['name'];
            }
        }
        //$sections = array(); // Delete this if you want to keep original sections!
        $sections[] = array(
            'title' => __('Location Settings', 'wp-jobsearch'),
            'id' => 'location-settings',
            'desc' => __('Location fields setting.', 'wp-jobsearch'),
            'icon' => 'el el-map-marker',
            'fields' => array(
                array(
                    'id' => 'location-settings-section',
                    'type' => 'section',
                    'title' => __('Location fields settings', 'wp-jobsearch'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'all_location_allow',
                    'type' => 'button_set',
                    'title' => __('Locations', 'wp-jobsearch'),
                    'subtitle' => __('Enable/Disable Locations from completely.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('Enable', 'wp-jobsearch'),
                        'off' => __('Disable', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'all_locations_type',
                    'type' => 'button_set',
                    'title' => __('Locations Type', 'wp-jobsearch'),
                    'required' => array('all_location_allow', 'equals', 'on'),
                    'subtitle' => __('Select locations type.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'manual' => __('Manual', 'wp-jobsearch'),
                        'api' => __('API Locations', 'wp-jobsearch'),
                    ),
                    'default' => 'api',
                ),
                array(
                    'id' => 'switch_location_fields',
                    'type' => 'button_set',
                    'title' => __('Location Fields', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                    ),
                    'subtitle' => __('Enable/Disable Location fields (Countries, Cities, States).', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'jobsearch-location-required-fields-count',
                    'type' => 'select',
                    'title' => __('Enable Fields', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('switch_location_fields', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                    ),
                    'subtitle' => __('Select how many locations fields enable.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'all' => __('All Fields', 'wp-jobsearch'),
                        '1' => __('One Field only', 'wp-jobsearch'),
                        '2' => __('Two Fields', 'wp-jobsearch'),
                        '3' => __('Three Fields', 'wp-jobsearch'),
                    ),
                    'default' => 'all',
                ),
                array(
                    'id' => 'jobsearch-location-label-location1',
                    'type' => 'text',
                    'title' => __('First Field Label', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('switch_location_fields', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                    ),
                    'subtitle' => __('First location field label i.e Country', 'wp-jobsearch'),
                    'desc' => '',
                    'default' => 'Country',
                ),
                array(
                    'id' => 'loc_firstf_is_req',
                    'type' => 'button_set',
                    'title' => __('Required First Field', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('switch_location_fields', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                    ),
                    'subtitle' => __('Choose if you want to make this field "required" by user or not.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'jobsearch-location-label-location2',
                    'type' => 'text',
                    'title' => __('Second Field Label', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('switch_location_fields', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                        array('jobsearch-location-required-fields-count', '!=', '1'),
                    ),
                    'subtitle' => __('Second location field label i.e State', 'wp-jobsearch'),
                    'desc' => '',
                    'default' => 'State',
                ),
                array(
                    'id' => 'loc_scndf_is_req',
                    'type' => 'button_set',
                    'title' => __('Required Second Field', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('switch_location_fields', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                        array('jobsearch-location-required-fields-count', '!=', '1'),
                    ),
                    'subtitle' => __('Choose if you want to make this field "required" by user or not.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'jobsearch-location-label-location3',
                    'type' => 'text',
                    'title' => __('Third Field Label', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('switch_location_fields', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                        array('jobsearch-location-required-fields-count', '!=', '1'),
                        array('jobsearch-location-required-fields-count', '!=', '2'),
                    ),
                    'subtitle' => __('Third location field label i.e City', 'wp-jobsearch'),
                    'desc' => '',
                    'default' => 'Region',
                ),
                array(
                    'id' => 'loc_thrdf_is_req',
                    'type' => 'button_set',
                    'title' => __('Required Third Field', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('switch_location_fields', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                        array('jobsearch-location-required-fields-count', '!=', '1'),
                        array('jobsearch-location-required-fields-count', '!=', '2'),
                    ),
                    'subtitle' => __('Choose if you want to make this field "required" by user or not.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'jobsearch-location-label-location4',
                    'type' => 'text',
                    'title' => __('Fourth Field Label', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('switch_location_fields', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                        array('jobsearch-location-required-fields-count', '!=', '1'),
                        array('jobsearch-location-required-fields-count', '!=', '2'),
                        array('jobsearch-location-required-fields-count', '!=', '3'),
                    ),
                    'subtitle' => __('Fourth location field label i.e Area', 'wp-jobsearch'),
                    'desc' => '',
                    'default' => 'City',
                ),
                array(
                    'id' => 'loc_forthf_is_req',
                    'type' => 'button_set',
                    'title' => __('Required Fourth Field', 'wp-jobsearch'),
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('switch_location_fields', 'equals', 'on'),
                        array('all_locations_type', 'equals', 'manual'),
                        array('jobsearch-location-required-fields-count', '!=', '1'),
                        array('jobsearch-location-required-fields-count', '!=', '2'),
                        array('jobsearch-location-required-fields-count', '!=', '3'),
                    ),
                    'subtitle' => __('Choose if you want to make this field "required" by user or not.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'location-allow-full-address',
                    'type' => 'button_set',
                    'title' => __('Allow Full Address', 'wp-jobsearch'),
                    'required' => array('all_location_allow', 'equals', 'on'),
                    'subtitle' => __('Allow users to enter the full address.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'yes_req' => __('Yes & Required', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'jobsearch-location-default-address',
                    'type' => 'text',
                    'required' => array(
                        array('all_location_allow', 'equals', 'on'),
                        array('location-allow-full-address', '!=', 'no'),
                    ),
                    'title' => __('Default Address', 'wp-jobsearch'),
                    'subtitle' => __('Set Default Location address for your site.', 'wp-jobsearch'),
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'location_allow_postal_code',
                    'type' => 'button_set',
                    'title' => __('Postal Code Field', 'wp-jobsearch'),
                    'required' => array('all_location_allow', 'equals', 'on'),
                    'subtitle' => __('Allow users to enter postal code.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'locmapsettings-sec',
                    'type' => 'section',
                    'title' => __('Map settings', 'wp-jobsearch'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'location-allow-map',
                    'type' => 'button_set',
                    'title' => __('Allow Map', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'location_map_type',
                    'type' => 'button_set',
                    'title' => __('Map Type', 'wp-jobsearch'),
                    'required' => array('location-allow-map', 'equals', 'yes'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => array(
                        'google' => __('Google Maps', 'wp-jobsearch'),
                        'mapbox' => __('MapBox', 'wp-jobsearch'),
                    ),
                    'default' => 'mapbox',
                ),
                array(
                    'id' => 'allow_latlng_fileds',
                    'type' => 'button_set',
                    'title' => __('Allow Lat/Lng/Zoom Fields', 'wp-jobsearch'),
                    'subtitle' => '',
                    'required' => array('location-allow-map', 'equals', 'yes'),
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'jobsearch-location-map-zoom',
                    'type' => 'text',
                    'required' => array('location-allow-map', 'equals', 'yes'),
                    'title' => __('Default Map Zoom', 'wp-jobsearch'),
                    'subtitle' => __('Set Default Zoom Level for Map.', 'wp-jobsearch'),
                    'desc' => '',
                    'default' => '12',
                ),
                array(
                    'id' => 'jobsearch-location-map-style',
                    'type' => 'textarea',
                    'title' => __('Map Style', 'wp-jobsearch'),
                    'required' => array(
                        array('location-allow-map', 'equals', 'yes'),
                        array('location_map_type', 'equals', 'google')
                    ),
                    'subtitle' => sprintf(__('You can get all map styles from <a href="%s" target="_blank">here</a>', 'wp-jobsearch'), 'https://snazzymaps.com/'),
                    'desc' => '',
                    'default' => '',
                ),
                array(
                    'id' => 'mapbox_style_url',
                    'type' => 'text',
                    'title' => __('MapBox Style', 'wp-jobsearch'),
                    'required' => array(
                        array('location-allow-map', 'equals', 'yes'),
                        array('location_map_type', 'equals', 'mapbox')
                    ),
                    'subtitle' => __('Paste mapbox style URL here. Get your MapBox Style URL from here <a href="https://www.mapbox.com/" target="_blank">www.mapbox.com/</a>', 'wp-jobsearch'),
                    'desc' => '',
                    'default' => 'mapbox://styles/mapbox/streets-v11',
                ),
                array(
                    'id' => 'jobsearch-detail-map-switch',
                    'type' => 'button_set',
                    'multi' => true,
                    'title' => __('Display Map', 'wp-jobsearch'),
                    'required' => array('location-allow-map', 'equals', 'yes'),
                    'subtitle' => '',
                    'desc' => __('Enable / Disable Map view in detail Pages', 'wp-jobsearch'),
                    'options' => array(
                        'employer' => __('Employer', 'wp-jobsearch'),
                        'job' => __('Job', 'wp-jobsearch'),
                        'candidate' => __('Candidate', 'wp-jobsearch'),
                    ),
                    'default' => array('employer', 'job'),
                ),
                array(
                    'id' => 'geo-location-settings-sec',
                    'type' => 'section',
                    'title' => __('Geo-Location settings', 'wp-jobsearch'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'top_search_geoloc',
                    'type' => 'button_set',
                    'title' => __('AutoFill Geo Location', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'top_search_locsugg',
                    'type' => 'button_set',
                    'title' => __('Location Suggestions', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'restrict_contries_locsugg',
                    'type' => 'select',
                    'multi' => true,
                    'title' => __('Autocomplete Countries List', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => $res_countries_list,
                    'default' => '',
                ),
                array(
                    'id' => 'autocomplete_adres_type',
                    'type' => 'button_set',
                    'title' => __('Autocomplete Address Type', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => array(
                        'full_address' => __('Full Address', 'wp-jobsearch'),
                        'city_contry' => __('City/Countries Only', 'wp-jobsearch'),
                    ),
                    'default' => 'full_address',
                ),
                array(
                    'id' => 'radius-settings-sec',
                    'type' => 'section',
                    'title' => __('Radius settings', 'wp-jobsearch'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'top_search_radius',
                    'type' => 'button_set',
                    'title' => __('Radius', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => array(
                        'yes' => __('Yes', 'wp-jobsearch'),
                        'no' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'yes',
                ),
                array(
                    'id' => 'top_search_radius_unit',
                    'type' => 'button_set',
                    'title' => __('Radius measure unit', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'options' => array(
                        'km' => __('KM', 'wp-jobsearch'),
                        'miles' => __('Miles', 'wp-jobsearch'),
                    ),
                    'default' => 'km',
                ),
                array(
                    'id' => 'top_search_def_radius',
                    'type' => 'text',
                    'title' => __('Default Radius', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '50',
                ),
                array(
                    'id' => 'top_search_max_radius',
                    'type' => 'text',
                    'title' => __('Maximum Radius', 'wp-jobsearch'),
                    'subtitle' => '',
                    'desc' => '',
                    'default' => '500',
                ),
            ),
        );
        return $sections;
    }
}

// class Jobsearch_Location
global $Jobsearch_Location_obj;
$Jobsearch_Location_obj = new Jobsearch_Location();