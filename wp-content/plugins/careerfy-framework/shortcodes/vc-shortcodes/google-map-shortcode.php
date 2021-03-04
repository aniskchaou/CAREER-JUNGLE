<?php

/**
 * Google Map Shortcode
 * @return html
 */

add_shortcode('careerfy_google_map_shortcode', 'careerfy_google_map_shortcode');
function careerfy_google_map_shortcode($atts, $content = '')
{
    global $map_markers_arr, $jobsearch_plugin_options, $features_map_box_arr;
    extract(shortcode_atts(array(
        'map_latitude' => '51.2',
        'map_longitude' => '0.2',
        'map_zoom' => '8',
        'map_height' => '',
        'map_street_view' => '',
        'map_scrollwheel' => '',
        'map_default_ui' => '',
        'map_marker' => '',
        'map_box_title' => '',
        'map_box_address' => '',
        'map_box_phone' => '',
        'map_box_email' => '',
        'map_box_website' => '',
        'map_styles' => '',
    ), $atts));

    $features_map_box_arr = [];
    $content = do_shortcode($content);
    $cnt_counter = rand(1000000, 9999999);
    ob_start();

    if ($map_latitude != '' && $map_longitude != '') {
        $map_latitude = $map_latitude;
        $map_longitude = $map_longitude;
    } else {
        $location_response = jobsearch_address_to_cords($map_box_address);
        $map_latitude = isset($location_response['lat']) ? $location_response['lat'] : '';
        $map_longitude = isset($location_response['lng']) ? $location_response['lng'] : '';
    }

    if ($map_latitude != '' && $map_longitude != '' && $map_zoom > 0) {

        wp_enqueue_script('careerfy-google-map');
        wp_enqueue_style('careerfy-mapbox-style');

        if ($map_marker != '') {
            $map_marker = ($map_marker);
        }


        ?>
        <div class="careerfy-map">
            <div id="map-<?php echo absint($cnt_counter) ?>" class="careerfy-map-holder"
                 style="height:<?php echo absint($map_height) ?>px;"></div>
        </div>

        <?php
        $location_map_type = isset($jobsearch_plugin_options['location_map_type']) ? $jobsearch_plugin_options['location_map_type'] : '';

        $map_args = array(
            'location_map_type' => $location_map_type,
            'cnt_counter' => $cnt_counter,
            'map_latitude' => $map_latitude,
            'map_longitude' => $map_longitude,
            'map_zoom' => $map_zoom,
            'map_street_view' => $map_street_view,
            'map_scrollwheel' => $map_scrollwheel,
            'map_default_ui' => $map_default_ui,
            'map_styles' => $map_styles,
            'map_marker' => $map_marker,
            'map_box_title' => $map_box_title,
            'map_box_address' => $map_box_address,
            'map_box_phone' => $map_box_phone,
            'map_box_email' => $map_box_email,
            'map_box_website' => $map_box_website,
            'features_map_box_arr' => $features_map_box_arr,
        );
        add_action('wp_footer', function () use ($map_args) {
            global $jobsearch_plugin_options, $map_markers_arr;
            extract(shortcode_atts(array(
                'location_map_type' => '',
                'cnt_counter' => '',
                'map_latitude' => '51.2',
                'map_longitude' => '0.2',
                'map_zoom' => '8',
                'map_street_view' => 'yes',
                'map_scrollwheel' => 'yes',
                'map_default_ui' => 'no',
                'map_marker' => '',
                'map_box_title' => '',
                'map_box_address' => '',
                'map_box_phone' => '',
                'map_box_email' => '',
                'map_box_website' => '',
                'map_styles' => '',
                'features_map_box_arr' => '',
            ), $map_args));

            $map_markers_arr[] = array(
                'map_latitude' => $map_latitude,
                'map_longitude' => $map_longitude,
                'map_marker' => $map_marker,
                'map_box_title' => $map_box_title,
                'map_box_address' => $map_box_address,
                'map_box_phone' => $map_box_phone,
                'map_box_email' => $map_box_email,
                'map_box_website' => $map_box_website,
            );

            if ($map_latitude != '' && $map_longitude != '' && $location_map_type == 'mapbox') {
                $features_map_box_arr[] = '{
                                \'type\': \'Feature\',
                                \'geometry\': {
                                    \'type\': \'Point\',
                                    \'coordinates\': [' . $map_longitude . ', ' . $map_latitude . ']
                                },
                                \'properties\': {
                                    \'title\':   "' . $map_box_title . '",
                                    \'description\': \' <div class="advisor-map-info">\
                                    <span class="map-phone">' . esc_html__('Phone', 'careerfy-frame') . ': ' . $map_box_phone . '</span>\
                                    <span class="map-email">' . esc_html__('Email', 'careerfy-frame') . ': ' . $map_box_email . '</span>\
                                    <span class="map-website">' . esc_html__('Website', 'careerfy-frame') . ': ' . $map_box_website . '</span>\
                                            </div> \',
                                }
                            }';

            }


            ?>
            <script>
                <?php if ($location_map_type == 'mapbox') {


                $mapbox_style_url = isset($jobsearch_plugin_options['mapbox_style_url']) ? $jobsearch_plugin_options['mapbox_style_url'] : '';
                $mapbox_access_token = isset($jobsearch_plugin_options['mapbox_access_token']) ? $jobsearch_plugin_options['mapbox_access_token'] : '';

                if ($map_styles == '') {
                    $map_styles = $mapbox_style_url;
                }

                if ($mapbox_access_token != '') { ?>
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
                    var map = new mapboxgl.Map({
                        container: 'map-<?php echo absint($cnt_counter); ?>',
                        style: '<?php echo($map_styles) ?>',
                        center: [<?php echo($map_longitude) ?>, <?php echo($map_latitude) ?>],
                        zoom: <?php echo esc_js($map_zoom) ?>
                    });


                    <?php if (is_array($map_markers_arr)) { ?>
                    var multi_markers = '<?php echo stripslashes(json_encode($map_markers_arr)) ?>';

                    multi_markers = jQuery.parseJSON(multi_markers);


                    var geojson = {
                        type: 'FeatureCollection',
                        features: [<?php foreach ($features_map_box_arr as $info) {
                            echo $info . ",";
                        }?>]
                    };

                    geojson.features.forEach(function (marker) {
                        var el = document.createElement('div');
                        el.className = 'marker';
                        // make a marker for each feature and add to the map
                        new mapboxgl.Marker(el)
                            .setLngLat(marker.geometry.coordinates)
                            .addTo(map);
                        new mapboxgl.Marker(el)
                            .setLngLat(marker.geometry.coordinates)
                            .setPopup(new mapboxgl.Popup({offset: 25}) // add popups
                                .setHTML('<div class="careerfy-mapbox-title-wrapper"><h3>' + marker.properties.title + '</h3></div>' + marker.properties.description + ''))
                            .addTo(map);
                    });

                    <?php } ?>

                });

                <?php } ?>
                <?php } else { ?>
                jQuery(document).ready(function () {
                    function initMap() {
                        var myLatLng = {
                            lat: <?php echo esc_js($map_latitude) ?>,
                            lng: <?php echo esc_js($map_longitude) ?>};

                        var map = new google.maps.Map(document.getElementById('map-<?php echo absint($cnt_counter) ?>'), {
                            zoom: <?php echo esc_js($map_zoom) ?>,
                            center: myLatLng,
                            <?php
                            if ($map_street_view == 'no') {
                            ?>
                            streetViewControl: false,
                            <?php
                            }
                            if ($map_scrollwheel == 'no') {
                            ?>
                            scrollwheel: false,
                            <?php }

                            if ($map_default_ui == 'yes') {
                            ?>
                            mapTypeControl: false,
                            <?php } ?>
                        });

                        <?php
                        if ($map_styles != '') {

                        $map_styles = stripslashes($map_styles);
                        $map_styles = str_replace(array('``', '`{', '}`', '[{[{', '}]}]'), array('"', '[{', '}]', '[{', '}]'), $map_styles);
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

                        if (is_array($map_markers_arr)) { ?>
                        var multi_markers = '<?php echo stripslashes(json_encode($map_markers_arr)) ?>';
                        multi_markers = jQuery.parseJSON(multi_markers);
                        jQuery.each(multi_markers, function (key, value) {
                            var lat = value.map_latitude;
                            var lng = value.map_longitude;
                            var info_marker = value.map_marker;
                            var info_title = value.map_box_title;
                            var info_address = value.map_box_address;
                            var info_email = value.map_box_email;
                            var info_phone = value.map_box_phone;
                            var info_web = value.map_box_website;

                            if (lat != '' && lng != '') {
                                latlngset = new google.maps.LatLng(lat, lng);

                                var marker = new google.maps.Marker({
                                    map: map,
                                    title: info_title,
                                    icon: info_marker,
                                    position: latlngset
                                });

                                var contentString = '\
							<div class="advisor-map-info">\
								<h2 class="map-title">' + info_title + '</h2>\
								<p class="map-address">' + info_address + '</p>';
                                if (info_phone != '') {
                                    contentString += '<span class="map-phone"><?php _e('Phone', 'careerfy-frame') ?> : ' + info_phone + '</span>';
                                }
                                if (info_email != '') {
                                    contentString += '<span class="map-email"><?php _e('Email', 'careerfy-frame') ?> : <a href="mailto:' + info_email + '">' + info_email + '</a></span>';
                                }
                                if (info_web != '') {
                                    contentString += '<span class="map-website"><?php _e('Website', 'careerfy-frame') ?> : <a href="' + info_web + '">' + info_web + '</a></span>';
                                }
                                contentString += '</div>';

                                var infowindow = new google.maps.InfoWindow({
                                    content: contentString,
                                });

                                marker.addListener('click', function () {
                                    infowindow.open(map, marker);
                                });
                            }
                        });
                        <?php } ?>
                    }

                    google.maps.event.addDomListener(window, 'load', initMap);
                });
                <?php } ?>
            </script>

            <?php
        }, 99, 1);
        ?>

        <?php
    }
    $html = ob_get_clean();
    return $html;
}

add_shortcode('careerfy_map_item', 'careerfy_map_item_shortcode');
function careerfy_map_item_shortcode($atts, $content = '')
{
    global $map_markers_arr, $jobsearch_plugin_options, $features_map_box_arr;
    extract(shortcode_atts(array(
        'map_latitude' => '',
        'map_longitude' => '',
        'map_marker' => '',
        'map_box_title' => '',
        'map_box_address' => '',
        'map_box_phone' => '',
        'map_box_email' => '',
        'map_box_website' => '',
    ), $atts));

    if ($map_marker != '') {
        $map_marker = ($map_marker);
    }

    if ($map_latitude != '' && $map_longitude != '') {
        $map_latitude = $map_latitude;
        $map_longitude = $map_longitude;
    } else {
        $location_response = jobsearch_address_to_cords($map_box_address);
        $map_latitude = isset($location_response['lat']) ? $location_response['lat'] : '';
        $map_longitude = isset($location_response['lng']) ? $location_response['lng'] : '';
    }

    $location_map_type = isset($jobsearch_plugin_options['location_map_type']) ? $jobsearch_plugin_options['location_map_type'] : '';


    if ($map_latitude != '' && $map_longitude != '' && $location_map_type == 'mapbox') {
        $features_map_box_arr[] = '{
                                \'type\': \'Feature\',
                                \'geometry\': {
                                    \'type\': \'Point\',
                                    \'coordinates\': [' . $map_longitude . ', ' . $map_latitude . ']
                                },
                                \'properties\': {
                                    \'title\':   "' . $map_box_title . '",
                                    \'description\': \' <div class="advisor-map-info">\
                                    <span class="map-phone">' . esc_html__('Phone', 'careerfy-frame') . ': ' . $map_box_phone . '</span>\
                                    <span class="map-email">' . esc_html__('Email', 'careerfy-frame') . ': ' . $map_box_email . '</span>\
                                    <span class="map-website">' . esc_html__('Website', 'careerfy-frame') . ': ' . $map_box_website . '</span>\
                                            </div> \',
                                }
                            }';

    }


    //$map_markers_arr = array();
    if ($map_latitude != '' && $map_longitude != '') {
        $map_markers_arr[] = array(
            'map_latitude' => $map_latitude,
            'map_longitude' => $map_longitude,
            'map_marker' => $map_marker,
            'map_box_title' => $map_box_title,
            'map_box_address' => $map_box_address,
            'map_box_phone' => $map_box_phone,
            'map_box_email' => $map_box_email,
            'map_box_website' => $map_box_website,
        );
    }
    ?>
    <style>
        .marker {
            background-image: url('<?php echo !empty($map_marker) ? $map_marker : careerfy_framework_get_url('images/mapbox-icon.png') ?>');
            background-size: cover;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
        }

        .mapboxgl-popup {
            max-width: 200px;
        }
        
    </style>
    <?php
}
