function jobsearch_loc_levels_names_to_address(rand_num) {
    var loc_loc_1 = jQuery('#location_location1_' + rand_num).val();
    var loc_loc_2 = '';
    var loc_loc_3 = '';

    //
    var locMapType = jobsearch_plugin_vars.locmap_type;
    //

    if (jQuery('select[name=jobsearch_field_location_location2]').length > 0) {
        loc_loc_2 = jQuery('select[name=jobsearch_field_location_location2]').val();
    } else if (jQuery('input[name=jobsearch_field_location_location2]').length > 0) {
        loc_loc_2 = jQuery('input[name=jobsearch_field_location_location2]').val();
    }
    if (jQuery('#location_location3_' + rand_num).length > 0) {
        loc_loc_3 = jQuery('#location_location3_' + rand_num).val();
    }

    var loc_request = jQuery.ajax({
        url: jobsearch_location_common_vars.ajax_url,
        method: "POST",
        data: {
            'loc_loc_1': loc_loc_1,
            'loc_loc_2': loc_loc_2,
            'loc_loc_3': loc_loc_3,
            'action': 'jobsearch_loc_levels_names_to_address'
        },
        dataType: "json"
    });
    loc_request.done(function (response) {
        if (typeof response.locadres !== 'undefined' && response.locadres != '') {
            if (jQuery('#check_loc_addr_' + rand_num).val() == '') {
                jQuery('#jobsearch_location_address_' + rand_num).val(response.locadres);
                jQuery('#jobsearch_location_address_' + rand_num).trigger('change');
            }
        }
    });
}

function jobsearch_loc_levels_to_mapcords(_this, rand_num) {
    //
    var locMapType = jobsearch_plugin_vars.locmap_type;
    var mapbox_access_token = jobsearch_plugin_vars.mapbox_token;
    var is_map_allow = jobsearch_plugin_vars.is_map_allow;
    //
    var flyToCond = false;
    var fadresVal = jQuery('input[name=jobsearch_field_location_address]').val();

    //console.log(jQuery('#cityId').val());
    if (_this.attr('id') == 'countryId') {
        jQuery('#stateId').val('');
        jQuery('#cityId').val('');
    }
    if (_this.attr('id') == 'stateId') {
        jQuery('#cityId').val('');
    }
    //console.log(_this.attr('id'));
    if (fadresVal != '') {
        flyToCond = false;
    } else {
        flyToCond = true;
    }
    var contryVal = jQuery('#countryId').val();
    var stateVal = jQuery('#stateId').val();
    var cityVal = jQuery('#cityId').val();
    //
    var locAddr = _this.val();
    if (locAddr != '' && is_map_allow != 'no' && flyToCond === true) {
        var newAddr = '';
        //console.log(cityVal);
        if (cityVal != '' && cityVal != null) {
            var comaDelb = '';
            if (stateVal != '' || contryVal != '') {
                comaDelb = ', ';
            }
            newAddr += cityVal + comaDelb;
        }
        if (stateVal != '' && stateVal != null && newAddr == '') {
            var comaDelb = '';
            if (contryVal != '') {
                comaDelb = ', ';
            }
            newAddr += stateVal + comaDelb;
        }
        if (contryVal != '') {
            newAddr += contryVal;
        }
        console.log(newAddr);

        if (locMapType == 'mapbox') {
            if (mapbox_access_token != '') {
                var cords_url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/' + encodeURI(newAddr) + '.json?access_token=' + mapbox_access_token;
                console.log(cords_url);
                jobsearch_common_getJSON(cords_url, function (status, response) {
                    //console.log(response);
                    if (typeof response.features[0].geometry.coordinates !== 'undefined') {
                        var mapCords = response.features[0].geometry.coordinates;
                        if (mapCords.length > 0) {
                            if (jobsearch_plugin_vars.is_rtl) {
                                mapboxgl.setRTLTextPlugin(
                                    'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.2.3/mapbox-gl-rtl-text.js',
                                    null,
                                    true // Lazy load the plugin
                                );
                            }
                            mapboxgl.accessToken = mapbox_access_token;
                            var mapLng = mapCords[0];
                            var mapLat = mapCords[1];
                            document.getElementById("jobsearch_location_lat_" + rand_num).value = mapLat;
                            document.getElementById("jobsearch_location_lng_" + rand_num).value = mapLng;
                            map.flyTo({
                                center: mapCords,
                            });
                            // remove markers
                            if (currentMarkers !== null) {
                                for (var i = currentMarkers.length - 1; i >= 0; i--) {
                                    currentMarkers[i].remove();
                                }
                            }
                            //
                            var new_marker = new mapboxgl.Marker({
                                draggable: true
                            }).setLngLat(mapCords).addTo(map);
                            currentMarkers.push(new_marker);
                            new_marker.on('dragend', function () {
                                var lngLat = new_marker.getLngLat();
                                document.getElementById("jobsearch_location_lat_" + rand_num).value = lngLat.lat;
                                document.getElementById("jobsearch_location_lng_" + rand_num).value = lngLat.lng;
                            });
                        }
                    }
                });
            }
        } else {

            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({address: newAddr}, function (results, status) {

                //alert(addres);
                if (status == google.maps.GeocoderStatus.OK) {
                    var new_latitude = results[0].geometry.location.lat();
                    var new_longitude = results[0].geometry.location.lng();
                    document.getElementById("jobsearch_location_lat_" + rand_num).value = new_latitude;
                    document.getElementById("jobsearch_location_lng_" + rand_num).value = new_longitude;
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
                    });

                    markers.push(marker);

                    google.maps.event.addListener(marker, 'dragend', function (event) {
                        document.getElementById("jobsearch_location_lat_" + rand_num).value = this.getPosition().lat();
                        document.getElementById("jobsearch_location_lng_" + rand_num).value = this.getPosition().lng();
                    });
                }
            });
        }
    }
    //
}

jQuery(document).on('change', '#countryId,#stateId,#cityId,select[name^=jobsearch_field_location_location]', function () {
    var _this = jQuery(this);
    var this_rand = _this.attr('data-randid');
    if (typeof this_rand === 'undefined') {
        this_rand = _this.parents('li').attr('data-randid');
    }
    if (_this.attr('id') == 'cityId') {
        var chngeCityLocIntrvl = setInterval(function () {
            jobsearch_loc_levels_to_mapcords(_this, this_rand);
            clearInterval(chngeCityLocIntrvl);
        }, 1000);
    } else {
        jobsearch_loc_levels_to_mapcords(_this, this_rand);
    }
});

jQuery(document).ready(function () {
    if (jQuery('#jobsearch-gdapilocs-statecon').length > 0) {
        var stateChngeSelTime = setInterval(function () {
            var gdapilocs_state_con = jQuery('#jobsearch-gdapilocs-statecon');
            var gdapilocs_state_val = gdapilocs_state_con.attr('data-val');
            if (gdapilocs_state_val != '' && gdapilocs_state_val != '0') {
                var gdapilocs_state_opts = gdapilocs_state_con.find('select').find('option');
                if (gdapilocs_state_opts.length > 1) {
                    var new_states_opthtml = '';
                    gdapilocs_state_opts.each(function (state_elem, state_index) {
                        var this_apistate = jQuery(this);
                        var this_apistate_val = this_apistate.val();
                        var this_apistate_label = this_apistate.html();
                        var this_apistate_id = this_apistate.attr('stateid');
                        if (this_apistate_label != '') {
                            new_states_opthtml += '<option value="' + this_apistate_val + '" ' + (typeof this_apistate_id !== 'undefined' ? 'stateid=' + this_apistate_id : '') + ' ' + (this_apistate_val == gdapilocs_state_val ? 'selected="selected"' : '') + '>' + this_apistate_label + '</option>' + "\n";
                        }
                    });
                    gdapilocs_state_con.find('select').html(new_states_opthtml);
                    jQuery('#jobsearch-gdapilocs-statecon').find('select').trigger('change');

                    // For city
                    if (jQuery('#jobsearch-gdapilocs-citycon').length > 0) {
                        var cityChngeSelTime = setInterval(function () {
                            var gdapilocs_city_con = jQuery('#jobsearch-gdapilocs-citycon');
                            var gdapilocs_city_val = gdapilocs_city_con.attr('data-val');
                            if (gdapilocs_city_val != '' && gdapilocs_city_val != '0') {
                                var gdapilocs_city_opts = gdapilocs_city_con.find('select').find('option');
                                if (gdapilocs_city_opts.length > 1) {
                                    var new_citys_opthtml = '';
                                    gdapilocs_city_opts.each(function (city_elem, city_index) {
                                        var this_apicity = jQuery(this);
                                        var this_apicity_val = this_apicity.val();
                                        var this_apicity_label = this_apicity.html();
                                        if (this_apicity_label != '') {
                                            new_citys_opthtml += '<option value="' + this_apicity_val + '" ' + (this_apicity_val == gdapilocs_city_val ? 'selected="selected"' : '') + '>' + this_apicity_label + '</option>' + "\n";
                                        }
                                    });
                                    gdapilocs_city_con.find('select').html(new_citys_opthtml);
                                }
                            }
                            clearInterval(cityChngeSelTime);
                        }, 1500);
                    }
                    //
                }
            }
            clearInterval(stateChngeSelTime);
        }, 2500);
    }
});

jQuery('.location_location1').on('change', function (e) {
    e.preventDefault();
    var this_id = jQuery(this).data('randid'),
        nextfieldelement = jQuery(this).data('nextfieldelement'),
        nextfieldval = jQuery(this).data('nextfieldval'),
        ajax_url = jobsearch_location_common_vars.ajax_url,
        location_location1 = jQuery('#location_location1_' + this_id),
        location_location2 = jQuery('#location_location2_' + this_id);
    jQuery('.location_location2_' + this_id).html('<i class="fa fa-refresh fa-spin"></i>');

    jobsearch_loc_levels_names_to_address(this_id);

    var request = jQuery.ajax({
        url: ajax_url,
        method: "POST",
        data: {
            location_location: location_location1.val(),
            nextfieldelement: nextfieldelement,
            nextfieldval: nextfieldval,
            action: 'jobsearch_location_load_location2_data',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if ('undefined' !== typeof response.html) {
            if (jQuery('.location_location2_selectize').length > 0) {
                if (jQuery('.location_location2_selectize').hasClass('location2_selectize_ajax')) {
                    jQuery('.location2_selectize_ajax').selectize()[0].selectize.destroy();
                }
            }
            location_location2.html(response.html);
            if (typeof location_location2.parent('.jobsearch-profile-select').find('.selectize-control') !== 'undefined') {
                location_location2.parent('.jobsearch-profile-select').find('.selectize-control').remove();
                location_location2.removeAttr('style');
                location_location2.removeAttr('tabindex');
                location_location2.removeClass('location2_selectize_ajax');
                location_location2.removeClass('selectized');
            }
            jQuery('.location_location2_' + this_id).html('');
            if (nextfieldval != '') {
                jQuery('.location_location2').trigger('change');
            }
            //
            if (jQuery('.location_location2_selectize').length > 0) {
                if (!jQuery('.location_location2_selectize').hasClass('location2_selectize_ajax')) {
                    jQuery('.location_location2_selectize').addClass('location2_selectize_ajax');

                    jQuery('.location2_selectize_ajax').selectize({
                        //allowEmptyOption: true,
                    });
                }
            }

            //
            if (jQuery('.location_location3_selectize').length > 0) {
                if (jQuery('.location_location2_selectize').val() == '') {
                    jQuery('.location_location2_selectize').trigger('change');
                }
            }
        }
    });

    request.fail(function (jqXHR, textStatus) {
    });
    return false;

});

jQuery('.location_location2').on('change', function (e) {
    e.preventDefault();
    var this_id = jQuery(this).data('randid'),
        nextfieldelement = jQuery(this).data('nextfieldelement'),
        nextfieldval = jQuery(this).data('nextfieldval'),
        ajax_url = jobsearch_location_common_vars.ajax_url,
        location_location2 = jQuery('#location_location2_' + this_id),
        location_location3 = jQuery('#location_location3_' + this_id);
    jQuery('.location_location3_' + this_id).html('<i class="fa fa-refresh fa-spin"></i>');

    jobsearch_loc_levels_names_to_address(this_id);

    var request = jQuery.ajax({
        url: ajax_url,
        method: "POST",
        data: {
            location_location: location_location2.val(),
            nextfieldelement: nextfieldelement,
            nextfieldval: nextfieldval,
            action: 'jobsearch_location_load_location2_data',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if ('undefined' !== typeof response.html) {
            if (jQuery('.location_location3_selectize').length > 0) {
                if (jQuery('.location_location3_selectize').hasClass('location3_selectize_ajax')) {
                    jQuery('.location3_selectize_ajax').selectize()[0].selectize.destroy();
                }
            }
            location_location3.html(response.html);
            if (typeof location_location3.parent('.jobsearch-profile-select').find('.selectize-control') !== 'undefined') {
                location_location3.parent('.jobsearch-profile-select').find('.selectize-control').remove();
                location_location3.removeAttr('style');
                location_location3.removeAttr('tabindex');
                location_location3.removeClass('location3_selectize_ajax');
                location_location3.removeClass('selectized');
            }
            jQuery('.location_location3_' + this_id).html('');
            if (nextfieldval != '') {
                jQuery('.location_location3').trigger('change');
            }
            //
            if (jQuery('.location_location3_selectize').length > 0) {
                if (!jQuery('.location_location3_selectize').hasClass('location3_selectize_ajax')) {
                    jQuery('.location_location3_selectize').addClass('location3_selectize_ajax');

                    jQuery('.location3_selectize_ajax').selectize({
                        //allowEmptyOption: true,
                    });
                }
            }

            //
            if (jQuery('.location_location4_selectize').length > 0) {
                if (jQuery('.location_location3_selectize').val() == '') {
                    jQuery('.location_location3_selectize').trigger('change');
                }
            }
        }
    });

    request.fail(function (jqXHR, textStatus) {
    });
    return false;

});



jQuery('.location_location3').on('change', function (e) {
    e.preventDefault();
    var this_id = jQuery(this).data('randid'),
        nextfieldelement = jQuery(this).data('nextfieldelement'),
        nextfieldval = jQuery(this).data('nextfieldval'),
        ajax_url = jobsearch_location_common_vars.ajax_url,
        location_location3 = jQuery('#location_location3_' + this_id),
        location_location4 = jQuery('#location_location4_' + this_id);
    jQuery('.location_location4_' + this_id).html('<i class="fa fa-refresh fa-spin"></i>');

    jobsearch_loc_levels_names_to_address(this_id);

    var request = jQuery.ajax({
        url: ajax_url,
        method: "POST",
        data: {
            location_location: location_location3.val(),
            nextfieldelement: nextfieldelement,
            nextfieldval: nextfieldval,
            action: 'jobsearch_location_load_location2_data',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if ('undefined' !== typeof response.html) {
            if (jQuery('.location_location4_selectize').length > 0) {
                if (jQuery('.location_location4_selectize').hasClass('location4_selectize_ajax')) {
                    jQuery('.location4_selectize_ajax').selectize()[0].selectize.destroy();
                }
            }
            location_location4.html(response.html);
            if (typeof location_location4.parent('.jobsearch-profile-select').find('.selectize-control') !== 'undefined') {
                location_location4.parent('.jobsearch-profile-select').find('.selectize-control').remove();
                location_location4.removeAttr('style');
                location_location4.removeAttr('tabindex');
                location_location4.removeClass('location4_selectize_ajax');
                location_location4.removeClass('selectized');
            }
            jQuery('.location_location4_' + this_id).html('');
            //
            if (jQuery('.location_location4_selectize').length > 0) {
                if (!jQuery('.location_location4_selectize').hasClass('location4_selectize_ajax')) {
                    jQuery('.location_location4_selectize').addClass('location4_selectize_ajax');

                    jQuery('.location4_selectize_ajax').selectize({
                        //allowEmptyOption: true,
                    });
                }
            }
        }
    });

    request.fail(function (jqXHR, textStatus) {
    });
    return false;

});
