function jobsearch_listing_top_map(top_dataobj, is_ajax) {

    var map_id = top_dataobj.map_id,
            access_token = top_dataobj.access_token,
            map_zoom = top_dataobj.map_zoom,
            map_style = top_dataobj.map_style,
            latitude = top_dataobj.latitude,
            longitude = top_dataobj.longitude,
            cluster_icon = top_dataobj.cluster_icon,
            map_cords_list = top_dataobj.cords_list;

    if (latitude != '' && longitude != '' && access_token != '' && map_style != '') {
        mapboxgl.accessToken = access_token;
        if (jobsearch_plugin_vars.is_rtl) {
            mapboxgl.setRTLTextPlugin(
                'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.2.3/mapbox-gl-rtl-text.js',
                null,
                true // Lazy load the plugin
            );
        }
        if (!is_ajax) {
            jobsearch_listing_map = new mapboxgl.Map({
                container: 'listings-map-' + map_id,
                style: map_style,
                center: [longitude, latitude],
                scrollZoom: false,
                zoom: map_zoom
            });
        }

        jobsearch_listing_map.addControl(new mapboxgl.NavigationControl({
            showCompass: false
        }), 'top-right');

        jobsearch_listing_map.on('load', function () {
            jobsearch_listing_map.addSource('map_source_obj', {
                type: 'geojson',
                data: {
                    type: 'FeatureCollection',
                    features: map_cords_list
                },
                cluster: true,
                clusterMaxZoom: 14, // Max zoom to cluster points on
                clusterRadius: 50 // Radius of each cluster when clustering points (defaults to 50)
            });

            jobsearch_listing_map.addLayer({
                id: 'clusters',
                type: 'circle',
                source: 'map_source_obj',
                filter: ['has', 'point_count'],
                paint: {
                    // Use step expressions (https://docs.mapbox.com/mapbox-gl-js/style-spec/#expressions-step)
                    // with three steps to implement three types of circles:
                    //   * Blue, 20px circles when point count is less than 100
                    //   * Yellow, 30px circles when point count is between 100 and 750
                    //   * Pink, 40px circles when point count is greater than or equal to 750
                    'circle-color': [
                        'step',
                        ['get', 'point_count'],
                        '#51bbd6',
                        100,
                        '#f1f075',
                        750,
                        '#f28cb1'
                    ],
                    'circle-radius': [
                        'step',
                        ['get', 'point_count'],
                        20,
                        100,
                        30,
                        750,
                        40
                    ]
                }
            });

            jobsearch_listing_map.addLayer({
                id: 'cluster-count',
                type: 'symbol',
                source: 'map_source_obj',
                filter: ['has', 'point_count'],
                layout: {
                    'text-field': '{point_count_abbreviated}',
                    'text-font': ['DIN Offc Pro Medium', 'Arial Unicode MS Bold'],
                    'text-size': 12
                }
            });

            jobsearch_listing_map.addLayer({
                id: 'unclustered-point',
                type: 'circle',
                source: 'map_source_obj',
                filter: ['!', ['has', 'point_count']],
                paint: {
                    'circle-color': '#eeeeee',
                    'circle-radius': 4,
                    'circle-stroke-width': 1,
                    'circle-stroke-color': '#ffffff'
                }
            });

            // inspect a cluster on click
            jobsearch_listing_map.on('click', 'clusters', function (e) {
                var features = jobsearch_listing_map.queryRenderedFeatures(e.point, {
                    layers: ['clusters']
                });
                var clusterId = features[0].properties.cluster_id;
                var clusterSource = jobsearch_listing_map.getSource('map_source_obj').getClusterExpansionZoom(
                        clusterId,
                        function (err, zoom) {
                            if (err)
                                return;

                            jobsearch_listing_map.easeTo({
                                center: features[0].geometry.coordinates,
                                zoom: zoom
                            });
                        }
                );
                var clustrMarksRefrsh = setInterval(function() {
                    updateMarkers();
                }, 1000);
            });

            jobsearch_listing_map.on('mouseenter', 'clusters', function () {
                jobsearch_listing_map.getCanvas().style.cursor = 'pointer';
            });
            jobsearch_listing_map.on('mouseleave', 'clusters', function () {
                jobsearch_listing_map.getCanvas().style.cursor = '';
            });
            
            var markers = {};
            var markersOnScreen = {};

            function updateMarkers() {
                var newMarkers = {};
                var features = jobsearch_listing_map.querySourceFeatures('map_source_obj');

                for (var i = 0; i < features.length; i++) {
                    var coords = features[ i ].geometry.coordinates;
                    var props = features[ i ].properties;

                    if (props.cluster) {
                        continue;
                    }
                    
                    var id = props.id;

                    var marker = markers[id];

                    if (!marker) {
                        var el = new Image();
                        el.src = props.marker;
                        el.classList.add('mapMarker');
                        el.dataset.type = 'point'; // you can use custom data if you have assigned it in the GeoJSON data
                        marker = markers[id] = new mapboxgl.Marker({element: el}).setLngLat(coords);
                    }

                    newMarkers[id] = marker;

                    if (!markersOnScreen[id]) {
                        var list_logo_img_url = props.logo_img_url;
                        var list_link = props.link;
                        var list_title = props.title;
                        var list_sector = props.sector;
                        var list_address = props.address;
                        var contentString = '\
                        <div class="jobsearch_map_info mapbox-info-popup">\
                            <div id="listing-info-' +  props.id + '" class="listing-info-inner">\
                                <div class="info-main-container">\
                                    ' + (list_logo_img_url != '' ? '<div class="img-con"><img src="' + list_logo_img_url + '" alt=""></div>' : '') + '\
                                    <div class="info-txt-holder">\
                                        <a class="info-title" href="' + list_link + '">' + list_title + '</a>\
                                        <div class="post-secin">' + list_sector + '</div>\
                                        ' + list_address + '\
                                    </div>\
                                </div>\
                            </div>\
                        </div>';
                        marker.setPopup(new mapboxgl.Popup({offset: 25}) // add popups
                                .setHTML(contentString))
                                .addTo(jobsearch_listing_map);
                    }
                }
                for (id in markersOnScreen) {
                    if (!newMarkers[id]) {
                        markersOnScreen[id].remove();
                    }
                }
                markersOnScreen = newMarkers;
            }
            jobsearch_listing_map.on('data', function (e) {
                if (e.sourceId !== 'map_source_obj' || !e.isSourceLoaded) return;

                jobsearch_listing_map.on('move', updateMarkers);
                jobsearch_listing_map.on('moveend', updateMarkers);
                updateMarkers();
            });
        });
    }
}