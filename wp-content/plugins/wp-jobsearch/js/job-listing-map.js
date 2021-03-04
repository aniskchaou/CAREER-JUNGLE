function jobsearch_listing_top_map(top_dataobj, is_ajax) {

    var map_id = top_dataobj.map_id,
            map_zoom = top_dataobj.map_zoom,
            this_map_style = top_dataobj.map_style,
            latitude = top_dataobj.latitude,
            longitude = top_dataobj.longitude,
            cluster_icon = top_dataobj.cluster_icon,
            map_cords_list = top_dataobj.cords_list,
            cordsActualLimit = 1000;

    var open_info_window;
    if (latitude != '' && longitude != '') {

        var marker;
        all_marker = [];
        reset_top_map_marker = [];

        var LatLngList = [];

        if (is_ajax != 'true') {
            map_zoom = parseInt(map_zoom);
            if (!jQuery.isNumeric(map_zoom)) {
                var map_zoom = 9;
            }
            var map_type = google.maps.MapTypeId.ROADMAP;
            var mapLatlng = new google.maps.LatLng(latitude, longitude);

            jobsearch_listing_map = new google.maps.Map(jQuery('#listings-map-' + map_id).get(0), {
                zoom: map_zoom,
                center: mapLatlng,
                mapTypeControl: false,
                streetViewControl: false,
                mapTypeId: map_type,
                zoomControl: false,
                scrollwheel: false,
                draggable: true,
            });

            jobsearch_listing_map.controls[google.maps.ControlPosition.TOP_LEFT].push(mapZoomControlBtns(jobsearch_listing_map, 'fa fa-plus', 'fa fa-minus'));
        } else {
            jobsearch_listing_map.panTo(new google.maps.LatLng(latitude, longitude));
        }

        if (typeof this_map_style !== 'undefined' && this_map_style != '') {
            var cust_style = jQuery.parseJSON(this_map_style);
            var styledMap = new google.maps.StyledMapType(cust_style, {name: 'Styled Map'});
            jobsearch_listing_map.mapTypes.set('map_style', styledMap);
            jobsearch_listing_map.setMapTypeId('map_style');
        }

        function mapZoomControlBtns(jobsearch_listing_map, icon_plus, icon_minus) {
            'use strict';
            var controlDiv = document.createElement('div');
            controlDiv.className = 'jobsearch-map-zoom-controls';
            controlDiv.index = 1;
            controlDiv.style.margin = '6px';
            var controlPlus = document.createElement('a');
            controlPlus.className = 'control-zoom-in';
            controlPlus.innerHTML = '<i class=\"' + icon_plus + '\"></i>';
            controlDiv.appendChild(controlPlus);
            var controlMinus = document.createElement('a');
            controlMinus.className = 'control-zoom-out';
            controlMinus.innerHTML = '<i class=\"' + icon_minus + '\"></i>';
            controlDiv.appendChild(controlMinus);

            google.maps.event.addDomListener(controlPlus, 'click', function () {
                var curZoom = jobsearch_listing_map.getZoom();
                if (curZoom < 20) {
                    var newZoom = curZoom + 1;
                    jobsearch_listing_map.setZoom(newZoom);
                    var mapZoomLvl = jobsearch_listing_map.getZoom();
                }
            });
            google.maps.event.addDomListener(controlMinus, 'click', function () {
                var curZoom = jobsearch_listing_map.getZoom();
                if (curZoom > 0) {
                    var newZoom = curZoom - 1;
                    jobsearch_listing_map.setZoom(newZoom);
                    var mapZoomLvl = jobsearch_listing_map.getZoom();
                }
            });
            return controlDiv;
        }

        if (typeof map_cords_list === 'object' && map_cords_list.length > 0) {
            var actual_length;
            if (map_cords_list.length > cordsActualLimit) {
                actual_length = cordsActualLimit;
            } else {
                actual_length = map_cords_list.length;
            }

            var def_cords_obj = [];
            var def_cords_creds = [];

            // variables for same lat lng merge
            var ohterLatLonObj = [];
            var sameLatLonObjMajor = [];
            var sameLatLonIndObj = [];

            var sameAddIndex = [];
            var allPostsMajorObj = [];

            jQuery.each(map_cords_list, function (index, element) {
                if (typeof element.lat !== 'undefined' && typeof element.lat != '' && typeof element.long !== 'undefined' && typeof element.long != '') {
                    var other_pos = true;
                    for (var oi = 0; oi < map_cords_list.length; oi++) {
                        if (
                                oi !== index &&
                                sameAddIndex.indexOf(oi) === -1 &&
                                map_cords_list[oi]['lat'] === element.lat &&
                                map_cords_list[oi]['long'] === element.long
                                ) {
                            sameAddIndex.push(oi);
                            other_pos = false;
                        }
                    }
                    if (other_pos === true && sameAddIndex.indexOf(index) === -1) {
                        var thisObj = {
                            obj_type: 'single',
                            lat: element.lat,
                            long: element.long,
                            id: element.id,
                            title: element.title,
                            link: element.link,
                            marker: element.marker,
                            logo_img_url: element.logo_img_url,
                            address: element.address,
                            employer: element.employer,
                            sector: element.sector,
                        };
                        ohterLatLonObj.push(thisObj);
                        allPostsMajorObj.push(thisObj);
                    } else {
                        var sameLatLonObj = [];
                        for (var oi = 0; oi < map_cords_list.length; oi++) {
                            if (map_cords_list[oi]['lat'] === element.lat && map_cords_list[oi]['long'] === element.long && sameLatLonIndObj.indexOf(oi) === -1) {
                                var thisObj = {
                                    lat: map_cords_list[oi]['lat'],
                                    long: map_cords_list[oi]['long'],
                                    id: map_cords_list[oi]['id'],
                                    title: map_cords_list[oi]['title'],
                                    link: map_cords_list[oi]['link'],
                                    marker: map_cords_list[oi]['marker'],
                                    logo_img_url: map_cords_list[oi]['logo_img_url'],
                                    address: map_cords_list[oi]['address'],
                                    employer: map_cords_list[oi]['employer'],
                                    sector: map_cords_list[oi]['sector'],
                                };
                                sameLatLonObj.push(thisObj);
                                sameLatLonIndObj.push(oi);
                            }
                        }
                        if (sameLatLonObj.length > 0) {
                            var thisObj = {
                                obj_type: 'multiple',
                                allObjs: sameLatLonObj,
                            };
                            sameLatLonObjMajor.push(thisObj);
                            allPostsMajorObj.push(thisObj);
                        }
                    }
                }
            });

            jQuery.each(allPostsMajorObj, function (index, element) {
                if (element.obj_type == 'multiple') {

                    if (element.allObjs.length > 0) {
                        var post_lats = [];
                        var post_longs = [];
                        var post_ids = [];
                        var post_titles = [];
                        var post_links = [];
                        var post_markers = [];
                        var post_logo_img_urls = [];
                        var post_addresss = [];
                        var post_employers = [];
                        var post_sectors = [];

                        for (var oi = 0; oi < element.allObjs.length; oi++) {
                            var thisElem = element.allObjs[oi];

                            post_lats.push(thisElem.lat);
                            post_longs.push(thisElem.long);
                            post_ids.push(thisElem.id);
                            post_titles.push(thisElem.title);
                            post_links.push(thisElem.link);
                            post_markers.push(thisElem.marker);
                            post_logo_img_urls.push(thisElem.logo_img_url);
                            post_addresss.push(thisElem.address);
                            post_employers.push(thisElem.employer);
                            post_sectors.push(thisElem.sector);
                        }

                        var thisElemF = element.allObjs[0];

                        if (index === actual_length) {
                            return false;
                        }
                        var i = index;

                        var db_lat = parseFloat(thisElemF.lat);
                        var db_long = parseFloat(thisElemF.long);
                        var list_title = thisElemF.title;
                        var list_marker = thisElemF.marker;
                        var list_marker_hover = thisElemF.marker_hover;

                        var def_cords = {lat: db_lat, lng: db_long};
                        def_cords_obj.push(def_cords);

                        var def_coroeds = {list_title: list_title, list_marker: list_marker, element: thisElemF};
                        def_cords_creds.push(def_coroeds);

                        var db_latLng = new google.maps.LatLng(db_lat, db_long);

                        LatLngList.push(new google.maps.LatLng(db_lat, db_long));

                        var markerPointsLen = '' + element.allObjs.length;
                        marker = new google.maps.Marker({
                            position: db_latLng,
                            center: db_latLng,
                            map: jobsearch_listing_map,
                            animation: google.maps.Animation.DROP,
                            draggable: false,
                            icon: cluster_icon,
                            label: {text: markerPointsLen, color: "#ffffff"},
                            post_lats: post_lats,
                            post_longs: post_longs,
                            post_ids: post_ids,
                            post_titles: post_titles,
                            post_links: post_links,
                            post_markers: post_markers,
                            post_logo_img_urls: post_logo_img_urls,
                            post_addresss: post_addresss,
                            post_employers: post_employers,
                            post_sectors: post_sectors,
                        });

                        google.maps.event.addListener(marker, 'click', (function (marker, i) {
                            return function () {

                                var contentString = '';
                                for (var oi = 0; oi < marker.post_ids.length; oi++) {
                                    var infoElemObj = {
                                        lat: marker.post_lats[oi],
                                        long: marker.post_longs[oi],
                                        id: marker.post_ids[oi],
                                        title: marker.post_titles[oi],
                                        link: marker.post_links[oi],
                                        marker: marker.post_markers[oi],
                                        logo_img_url: marker.post_logo_img_urls[oi],
                                        address: marker.post_addresss[oi],
                                        employer: marker.post_employers[oi],
                                        sector: marker.post_sectors[oi],
                                    };

                                    contentString += infoContentString(infoElemObj);

                                }

                                var infowindow = new InfoBox({
                                    boxClass: 'jobsearch_map_info multi_listings',
                                    content: contentString,
                                    disableAutoPan: true,
                                    maxWidth: 0,
                                    alignBottom: true,
                                    pixelOffset: new google.maps.Size(40, 50),
                                    zIndex: null,
                                    closeBoxMargin: "2px",
                                    closeBoxURL: "close",
                                    infoBoxClearance: new google.maps.Size(1, 1),
                                    isHidden: false,
                                    pane: "floatPane",
                                    enableEventPropagation: false
                                });

                                jobsearch_listing_map.panTo(marker.getPosition());
                                jobsearch_listing_map.panBy(100, -50);
                                if (open_info_window)
                                    open_info_window.close();
                                infowindow.open(jobsearch_listing_map, this);
                                open_info_window = infowindow;

                            }
                        })(marker, i));
                        all_marker.push(marker);
                        reset_top_map_marker.push(marker);

                    }
                } else {
                    if (index === actual_length) {
                        return false;
                    }
                    var i = index;
                    var db_lat = parseFloat(element.lat);
                    var db_long = parseFloat(element.long);
                    var list_title = element.title;
                    var list_id = element.id;

                    var list_marker = element.marker;
                    var list_marker_hover = element.marker_hover;

                    var def_cords = {lat: db_lat, lng: db_long};
                    def_cords_obj.push(def_cords);

                    var def_coroeds = {list_title: list_title, list_marker: list_marker, element: element};
                    def_cords_creds.push(def_coroeds);

                    var db_latLng = new google.maps.LatLng(db_lat, db_long);

                    LatLngList.push(new google.maps.LatLng(db_lat, db_long));

                    marker = new google.maps.Marker({
                        position: db_latLng,
                        center: db_latLng,
                        map: jobsearch_listing_map,
                        animation: google.maps.Animation.DROP,
                        draggable: false,
                        icon: list_marker,
                        title: list_title,
                        id: list_id,
                        icon_marker: list_marker,
                        icon_marker_hover: list_marker_hover,
                    });

                    google.maps.event.addListener(marker, 'click', (function (marker, i) {
                        return function () {

                            var contentString = infoContentString(element);

                            var infowindow = new InfoBox({
                                boxClass: 'jobsearch_map_info',
                                content: contentString,
                                disableAutoPan: true,
                                maxWidth: 0,
                                alignBottom: true,
                                pixelOffset: new google.maps.Size(40, 50),
                                zIndex: null,
                                closeBoxMargin: "2px",
                                closeBoxURL: "close",
                                infoBoxClearance: new google.maps.Size(1, 1),
                                isHidden: false,
                                pane: "floatPane",
                                enableEventPropagation: false
                            });

                            jobsearch_listing_map.panTo(marker.getPosition());
                            jobsearch_listing_map.panBy(100, -50);
                            if (open_info_window)
                                open_info_window.close();
                            infowindow.open(jobsearch_listing_map, this);
                            open_info_window = infowindow;
                        }
                    })(marker, i));

                    all_marker.push(marker);
                    reset_top_map_marker.push(marker);
                }
            });

            if (LatLngList.length > 0) {
                var latlngbounds = new google.maps.LatLngBounds();
                for (var i = 0; i < LatLngList.length; i++) {
                    latlngbounds.extend(LatLngList[i]);
                }
                jobsearch_listing_map.setCenter(latlngbounds.getCenter(), jobsearch_listing_map.fitBounds(latlngbounds));
            }

            google.maps.event.addListener(jobsearch_listing_map, "click", function (event) {
                if (open_info_window) {
                    open_info_window.close();
                }
            });
        }

        function jobsearchMapClusters() {
            if (all_marker) {
                var mcOptions;
                var clusterStyles = [
                    {
                        textColor: '#ffffff',
                        opt_textColor: '#ffffff',
                        url: cluster_icon,
                        height: 41,
                        width: 28,
                        textSize: 12
                    }
                ];
                mcOptions = {
                    gridSize: 15,
                    ignoreHidden: true,
                    maxZoom: 12,
                    styles: clusterStyles
                };
                markerClusterers = new MarkerClusterer(jobsearch_listing_map, all_marker, mcOptions);
            }
        }

        jobsearchMapClusters();

        function infoContentString(element) {
            var listing_id = element.id;
            var list_title = element.title;
            var list_link = element.link;
            var list_logo_img_url = element.logo_img_url;
            var list_address = element.address;
            var list_employer = element.employer;
            var list_sector = element.sector;

            var contentString = '\
            <div id="listing-info-' + listing_id + '" class="listing-info-inner">\
                <div class="info-main-container">\
                    ' + (list_logo_img_url != '' ? '<div class="img-con"><img src="' + list_logo_img_url + '" alt=""></div>' : '') + '\
                    <div class="info-txt-holder">\
                        <a class="info-title" href="' + list_link + '">' + list_title + '</a>\
                        <div class="post-secin">' + list_employer + list_sector + '</div>\
                        ' + list_address + '\
                    </div>\
                </div>\
            </div>';

            return contentString;
        }
    }
}