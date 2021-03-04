<?php
/**
 * Advance Search Shortcode
 * @return html
 */
add_shortcode('jobsearch_advance_search', 'jobsearch_advance_search_shortcode');

function jobsearch_advance_search_shortcode($atts)
{
    global $jobsearch_plugin_options, $sitepress, $jobsearch_gdapi_allocation;

    $lang_code = '';
    if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
        $lang_code = $sitepress->get_current_language();
    }

    extract(shortcode_atts(array(
        'result_page' => '',
        'keyword_field' => 'show',
        'loc_field' => 'show',
        'loc_type' => 'dropdown',
        'loc_field1' => '',
        'loc_field2' => '',
        'loc_field3' => '',
        'loc_field4' => '',
        'loc_locate_1' => '',
        'cat_field' => 'show',
        'job_type_field' => 'show',
        'serch_txt_color' => '',
        'serch_bg_color' => '',
        'serch_hov_color' => '',
        'serch_btn_txt' => '',
    ), $atts));

    $html = '';
    if ($keyword_field == 'show' || $loc_field == 'show' || $cat_field == 'show' || $job_type_field == 'show') {
        ob_start();

        $all_locations_type = isset($jobsearch_plugin_options['all_locations_type']) ? $jobsearch_plugin_options['all_locations_type'] : '';

        $rand_num = rand(1000000, 9999999);
        if ($result_page != '') {
            $result_page_obj = jobsearch_get_page_by_slug($result_page, 'OBJECT', 'page');
            $result_page = isset($result_page_obj->ID) ? $result_page_obj->ID : 0;
        }

        $loc_location1 = isset($_REQUEST['location_location1']) ? $_REQUEST['location_location1'] : '';

        if ($loc_locate_1 != '') {
            $loc_location1 = $loc_locate_1;
        }

        $loc_location2 = isset($_REQUEST['location_location2']) ? $_REQUEST['location_location2'] : '';
        $loc_location3 = isset($_REQUEST['location_location3']) ? $_REQUEST['location_location3'] : '';
        $loc_location4 = isset($_REQUEST['location_location4']) ? $_REQUEST['location_location4'] : '';

        $required_fields_count = isset($jobsearch_plugin_options['jobsearch-location-required-fields-count']) ? $jobsearch_plugin_options['jobsearch-location-required-fields-count'] : 'all';
        $label_location1 = isset($jobsearch_plugin_options['jobsearch-location-label-location1']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location1'], 'JobSearch Options', 'Location First Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location1'], $lang_code) : esc_html__('Country', 'wp-jobsearch');
        $label_location2 = isset($jobsearch_plugin_options['jobsearch-location-label-location2']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location2'], 'JobSearch Options', 'Location Second Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location2'], $lang_code) : esc_html__('State', 'wp-jobsearch');
        $label_location3 = isset($jobsearch_plugin_options['jobsearch-location-label-location3']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location3'], 'JobSearch Options', 'Location Third Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location3'], $lang_code) : esc_html__('Region', 'wp-jobsearch');
        $label_location4 = isset($jobsearch_plugin_options['jobsearch-location-label-location4']) ? apply_filters('wpml_translate_single_string', $jobsearch_plugin_options['jobsearch-location-label-location4'], 'JobSearch Options', 'Location Forth Field - ' . $jobsearch_plugin_options['jobsearch-location-label-location4'], $lang_code) : esc_html__('City', 'wp-jobsearch');

        if ($all_locations_type != 'api') {
            $please_select = esc_html__('Please select', 'wp-jobsearch');
            $location_location1 = array('' => apply_filters('jobsearch_adv_srch_sh_front_contry_label', $please_select . ' ' . $label_location1));
            $location_location2 = array('' => apply_filters('jobsearch_adv_srch_sh_front_state_label', $please_select . ' ' . $label_location2));
            $location_location3 = array('' => apply_filters('jobsearch_adv_srch_sh_front_region_label', $please_select . ' ' . $label_location3));
            $location_location4 = array('' => apply_filters('jobsearch_adv_srch_sh_front_city_label', $please_select . ' ' . $label_location4));

//        $location_obj = get_terms('job-location', array(
//            'orderby' => 'count',
//            'hide_empty' => 0,
//            'parent' => 0,
//        ));
            $location_obj = jobsearch_custom_get_terms('job-location');
            foreach ($location_obj as $country_arr) {
                $location_location1[$country_arr->slug] = $country_arr->name;
            }
        }

        //
        if ($loc_locate_1 != '') {
            $tax2_getby = get_term_by('slug', $loc_locate_1, 'job-location');
            if(!empty($tax2_getby)) {

//            $location2_obj = get_terms('job-location', array(
//                'orderby' => 'count',
//                'hide_empty' => 0,
//                'parent' => $tax2_getby->term_id,
//            ));
                $tax2_getby_termid = $tax2_getby->term_id;
                $location2_obj = jobsearch_custom_get_terms('job-location', $tax2_getby_termid);
                foreach ($location2_obj as $city_arr) {
                    $location_location2[$city_arr->slug] = $city_arr->name;
                }
                $location_location2['other-cities'] = esc_html__('Other Locations', 'wp-jobsearch');
            }
        }
        //

        if ($serch_hov_color != '') { ?>
            <style>
                .jobsearch-search-container .jobsearch-banner-search input[type="submit"]:hover,
                .dynamic-class-<?php echo ($rand_num) ?> input:hover {
                    background: <?php echo ($serch_hov_color) ?> !important;
                }
            </style>
        <?php } ?>
        <div class="jobsearch-search-container">
            <form class="jobsearch-banner-search" method="get" action="<?php echo(get_permalink($result_page)); ?>">
                <ul>
                    <?php
                    ob_start();
                    if ($keyword_field == 'show') {
                        ?>
                        <li>
                            <div class="jobsearch-sugges-search">
                                <input placeholder="<?php echo apply_filters('jobsearch_own_sh_adv_srch_keywords_str', esc_html__('Job Title, Keywords, or Phrase', 'wp-jobsearch')) ?>" class="jobsearch-keywordsrch-inp-field" name="search_title" type="text">
                                <span class="sugg-search-loader"></span>
                            </div>
                        </li>
                        <?php
                    }
                    $srchbox_html = ob_get_clean();

                    ob_start();
                    if ($loc_field == 'show') {
                        if ($loc_type == 'input') {
                            $top_search_geoloc = isset($jobsearch_plugin_options['top_search_geoloc']) ? $jobsearch_plugin_options['top_search_geoloc'] : '';
                            $def_radius_unit = isset($jobsearch_plugin_options['top_search_radius_unit']) ? $jobsearch_plugin_options['top_search_radius_unit'] : '';
                            $top_search_def_radius = isset($jobsearch_plugin_options['top_search_def_radius']) ? $jobsearch_plugin_options['top_search_def_radius'] : 50;
                            $top_search_max_radius = isset($jobsearch_plugin_options['top_search_max_radius']) ? $jobsearch_plugin_options['top_search_max_radius'] : 500;
                            $top_search_radius = isset($jobsearch_plugin_options['top_search_radius']) ? $jobsearch_plugin_options['top_search_radius'] : '';
                            ?>
                            <li>
                                <div class="jobsearch_searchloc_div">
                                    <span class="loc-loader"></span>
                                    <?php
                                    $citystat_zip_title = esc_html__('City, State or ZIP', 'wp-jobsearch');

                                    $location_map_type = isset($jobsearch_plugin_options['location_map_type']) ? $jobsearch_plugin_options['location_map_type'] : '';
                                    if ($location_map_type == 'mapbox') {
                                        wp_enqueue_script('jobsearch-mapbox');
                                        wp_enqueue_script('jobsearch-mapbox-geocoder');
                                        wp_enqueue_script('mapbox-geocoder-polyfill');
                                        wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                    } else {
                                        wp_enqueue_script('jobsearch-google-map');
                                    }
                                    wp_enqueue_script('jobsearch-location-autocomplete');
                                    ?>
                                    <input placeholder="<?php echo apply_filters('jobsearch_listin_serchbox_location_title', $citystat_zip_title) ?>"
                                           autocomplete="off" class="jobsearch_search_location_field"
                                           value="" type="text">
                                    <input type="hidden"
                                           class="loc_search_keyword <?php echo($top_search_geoloc != 'no' ? 'srch_autogeo_location' : '') ?>"
                                           name="location"
                                           value="">
                                    <?php
                                    if ($top_search_radius == 'yes') { ?>
                                        <div class="careerfy-radius-tooltip">
                                            <label><?php echo esc_html__('Radius', 'wp-jobsearch') ?>
                                                ( <?php echo esc_html__($def_radius_unit, 'wp-jobsearch') ?>
                                                )</label><input
                                                    type="number" name="loc_radius"
                                                    value="<?php echo($top_search_def_radius) ?>"
                                                    max="<?php echo($top_search_max_radius) ?>"></div>
                                    <?php } ?>
                                </div>
                                <?php
                                if ($top_search_geoloc != 'no') { ?>
                                    <a href="javascript:void(0);" class="geolction-btn"
                                       onclick="JobsearchGetClientLocation()"><i
                                                class="jobsearch-icon jobsearch-location"></i></a>
                                <?php } ?>
                            </li>
                            <?php
                        } else {
                            if ($all_locations_type != 'api') {

                                if ($loc_locate_1 != '') {
                                    ?>
                                    <li>
                                        <input type="hidden" name="location_location1" value="<?php echo($loc_location1) ?>">
                                        <div class="jobsearch-select-style location-level-select">
                                            <select id="location_location2_<?php echo($rand_num) ?>"
                                                    name="location_location2" class="location_location2 selectize-select"
                                                    data-randid="<?php echo($rand_num) ?>"
                                                    data-nextfieldelement="<?php echo($please_select . ' ' . $label_location3) ?>"
                                                    data-nextfieldval="<?php echo($loc_location3) ?>">
                                                <?php
                                                if (!empty($location_location2)) {
                                                    foreach ($location_location2 as $loc2_key => $loc2_val) {
                                                        ?>
                                                        <option value="<?php echo($loc2_key) ?>"<?php echo($loc_location2 == $loc2_key ? ' selected="selected"' : '') ?>><?php echo($loc2_val) ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <span class="jobsearch-field-loader location_location2_<?php echo absint($rand_num); ?>"></span>
                                        </div>
                                    </li>
                                    <?php
                                } else {
                                    if ($loc_field1 == 'show') {
                                        ob_start();
                                        ?>
                                        <li>
                                            <div class="jobsearch-select-style location-level-select">
                                                <select id="location_location1_<?php echo($rand_num) ?>"
                                                        name="location_location1"
                                                        class="location_location1_ccus selectize-select"
                                                        data-randid="<?php echo($rand_num) ?>"
                                                        data-nextfieldelement="<?php echo apply_filters('jobsearch_adv_srch_sh_front_state_label', ($please_select . ' ' . $label_location2)) ?>"
                                                        data-nextfieldval="<?php echo($loc_location2) ?>">
                                                    <?php
                                                    if (!empty($location_location1)) {
                                                        foreach ($location_location1 as $loc1_key => $loc1_val) {
                                                            ?>
                                                            <option value="<?php echo($loc1_key) ?>"<?php echo($loc_location1 == $loc1_key ? ' selected="selected"' : '') ?>><?php echo($loc1_val) ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </li>
                                        <?php
                                        $loc_1_html = ob_get_clean();
                                        echo apply_filters('jobsearch_manual_locs_loc1field_html', $loc_1_html, $location_location1, $loc_location1, $rand_num, $loc_location2, $label_location2);
                                    }
                                    if (($required_fields_count > 1 || $required_fields_count == 'all') && $loc_field2 == 'show') {
                                        ?>
                                        <li>
                                            <div class="jobsearch-select-style location-level-select">
                                                <div id="location_location2_cus_<?php echo($rand_num) ?>">
                                                    <select id="location_location2_<?php echo($rand_num) ?>"
                                                            name="location_location2"
                                                            class="location_location2 selectize-select"
                                                            data-randid="<?php echo($rand_num) ?>"
                                                            data-nextfieldelement="<?php echo($please_select . ' ' . $label_location3) ?>"
                                                            data-nextfieldval="<?php echo($loc_location3) ?>">
                                                        <?php
                                                        if (!empty($location_location2)) {
                                                            foreach ($location_location2 as $loc2_key => $loc2_val) {
                                                                ?>
                                                                <option value="<?php echo($loc2_key) ?>"<?php echo($loc_location2 == $loc2_key ? ' selected="selected"' : '') ?>><?php echo($loc2_val) ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <span class="jobsearch-field-loader location_location2_<?php echo absint($rand_num); ?>"></span>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    if (($required_fields_count > 2 || $required_fields_count == 'all') && $loc_field3 == 'show') {
                                        ?>
                                        <li>
                                            <div class="jobsearch-select-style location-level-select">
                                                <select id="location_location3_<?php echo($rand_num) ?>"
                                                        name="location_location3"
                                                        class="location_location3 location_location3_selectize"
                                                        data-randid="<?php echo($rand_num) ?>"
                                                        data-nextfieldelement="<?php echo($please_select . ' ' . $label_location4) ?>"
                                                        data-nextfieldval="<?php echo($loc_location4) ?>">
                                                    <?php
                                                    if (!empty($location_location3)) {
                                                        foreach ($location_location3 as $loc3_key => $loc3_val) {
                                                            ?>
                                                            <option value="<?php echo($loc3_key) ?>"<?php echo($loc_location3 == $loc3_key ? ' selected="selected"' : '') ?>><?php echo($loc3_val) ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <span class="jobsearch-field-loader location_location3_<?php echo absint($rand_num); ?>"></span>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    if (($required_fields_count > 3 || $required_fields_count == 'all') && $loc_field4 == 'show') {
                                        ?>
                                        <li>
                                            <div class="jobsearch-select-style location-level-select">
                                                <select id="location_location4_<?php echo($rand_num) ?>"
                                                        name="location_location4"
                                                        class="location_location4 location_location4_selectize"
                                                        data-randid="<?php echo($rand_num) ?>">
                                                    <?php
                                                    if (!empty($location_location4)) {
                                                        foreach ($location_location4 as $loc4_key => $loc4_val) {
                                                            ?>
                                                            <option value="<?php echo($loc4_key) ?>"<?php echo($loc_location4 == $loc4_key ? ' selected="selected"' : '') ?>><?php echo($loc4_val) ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <span class="jobsearch-field-loader location_location4_<?php echo absint($rand_num); ?>"></span>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                }
                            } else {

                                wp_enqueue_script('jobsearch-gdlocation-api');
                                $jobsearch_locsetin_options = get_option('jobsearch_locsetin_options');

                                $jobsearch_gdapi_allocation->load_locations_js(true, false);

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

                                if ($loc_optionstype == '0' || $loc_optionstype == '1') { ?>
                                    <li>
                                        <div id="jobsearch-gdapilocs-contrycon" data-val="<?php echo($loc_location1) ?>"
                                             class="jobsearch-select-style location-level-select">
                                            <select name="location_location1"
                                                    class="countries<?php echo($contries_class . $continents_class) ?>"
                                                    id="countryId">
                                                <option value=""><?php esc_html_e('Select Country', 'wp-jobsearch') ?></option>
                                            </select>
                                        </div>
                                    </li>
                                <?php } ?>
                                <?php if ($loc_optionstype != '4') { ?>
                                <li>
                                    <?php
                                    if ($loc_optionstype == '2' || $loc_optionstype == '3') {
                                        ?>
                                        <input type="hidden" name="country" id="countryId"
                                               value="<?php echo($contry_singl_contry) ?>"/>
                                        <?php } ?>
                                    <div id="jobsearch-gdapilocs-statecon" data-val="<?php echo($loc_location2) ?>"
                                         class="jobsearch-select-style location-level-select">
                                        <select name="location_location2" class="states<?php echo($states_class) ?>"
                                                id="stateId">
                                            <option value=""><?php esc_html_e('Select State', 'wp-jobsearch') ?></option>
                                        </select>
                                    </div>
                                </li>
                                <?php } ?>
                                <?php
                                if ($loc_optionstype == '1' || $loc_optionstype == '2' || $loc_optionstype == '4') {
                                    ?>
                                    <li>
                                        <div id="jobsearch-gdapilocs-citycon" data-val="<?php echo($loc_location3) ?>"
                                             class="jobsearch-select-style location-level-select">
                                            <select name="location_location3" class="cities <?php echo($cities_class) ?>"
                                                    id="cityId">
                                                <option value=""><?php esc_html_e('Select City', 'wp-jobsearch') ?></option>
                                            </select>
                                        </div>
                                    </li>
                                    <?php
                                }
                            }
                        }
                    }
                    $locations_html = ob_get_clean();

                    ob_start();
                    $all_sectors = get_terms(array(
                        'taxonomy' => 'sector',
                        'hide_empty' => false,
                    ));

                    if (!empty($all_sectors) && !is_wp_error($all_sectors) && $cat_field == 'show') {
                        ?>
                        <li>
                            <div class="jobsearch-select-style">
                                <select name="sector_cat" class="selectize-select">
                                    <option value=""><?php echo apply_filters('jobsearch_own_sh_adv_srch_select_cat_str', esc_html__('Select Sector', 'wp-jobsearch')) ?></option>
                                    <?php echo jobsearch_sector_terms_hierarchical(0, $all_sectors, '', 0, 0, ''); ?>
                                </select>
                            </div>
                        </li>
                        <?php
                    }
                    $sectors_html = ob_get_clean();

                    ob_start();
                    $all_job_types = get_terms(array(
                        'taxonomy' => 'jobtype',
                        'hide_empty' => false,
                    ));

                    if (!empty($all_job_types) && !is_wp_error($all_job_types) && $job_type_field == 'show') {
                        ?>
                        <li>
                            <div class="jobsearch-select-style">
                                <select name="job_type" class="selectize-select">
                                    <option value=""><?php esc_html_e('Select Type', 'wp-jobsearch') ?></option>
                                    <?php
                                    foreach ($all_job_types as $term_type) {
                                        ?>
                                        <option value="<?php echo($term_type->slug) ?>"><?php echo($term_type->name) ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </li>
                        <?php
                    }
                    $job_types_html = ob_get_clean();

                    $ov_serch_html = $srchbox_html . $locations_html . $sectors_html . $job_types_html;

                    echo apply_filters('jobsearch_adv_srch_front_sh_html', $ov_serch_html, $srchbox_html, $locations_html, $sectors_html, $job_types_html);

                    $srch_btn_style = '';
                    if ($serch_txt_color != '') {
                        $srch_btn_style .= ' color: ' . $serch_txt_color . ';';
                    }
                    if ($serch_bg_color != '') {
                        $srch_btn_style .= ' background-color: ' . $serch_bg_color . ';';
                    }

                    if ($srch_btn_style != '') {
                        $srch_btn_style = ' style="' . $srch_btn_style . '"';
                    }
                    if ($serch_btn_txt != '') {
                        ?>
                        <li class="jobsearch-banner-submit with-btn-txt dynamic-class-<?php echo($rand_num) ?>">
                            <input type="submit" value="<?php echo($serch_btn_txt) ?>"<?php echo($srch_btn_style) ?>>
                        </li>
                        <?php
                    } else {
                        ?>
                        <li class="jobsearch-banner-submit"><input type="submit" value=""> <i
                                    class="jobsearch-icon jobsearch-search"<?php echo($srch_btn_style) ?>></i></li>
                        <?php
                    }
                    ?>
                </ul>
                <?php
                if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                    global $sitepress;
                    $current_lang = $sitepress->get_current_language();
                    ?>
                    <input type="hidden" name="lang" value="<?php echo ($current_lang) ?>">
                    <?php
                }
                ?>
                <input type="hidden" name="ajax_filter" value="true">
            </form>
        </div>
        <?php
        $html = ob_get_clean();
    }

    return $html;
}
