jQuery.fn.extend({
    cityAutocomplete: function (options) {

        if (jobsearch_plugin_vars.locmap_type == 'mapbox') {

            var mapbox_acces_token = jobsearch_plugin_vars.mapbox_token;
            var mapbox_style_url = jobsearch_plugin_vars.mapbox_style;
            if (mapbox_acces_token != '' && mapbox_style_url != '') {

                var locAutoSrchAjax;
                jQuery('body').append('<div id="jobsearch-bodymapbox-genmap" style="height:0;"></div><div id="jobsearch-bodymapbox-gensbox" style="height:0; display:none;"></div>');
                mapboxgl.accessToken = mapbox_acces_token;
                var cityAcMap = new mapboxgl.Map({
                    container: 'jobsearch-bodymapbox-genmap',
                    style: mapbox_style_url,
                    center: [-96, 37.8],
                    scrollZoom: false,
                    zoom: 3
                });
                var geocodParams = {
                    accessToken: mapboxgl.accessToken,
                    marker: false,
                    flyTo: false,
                    mapboxgl: mapboxgl
                };
                var city_contry_str = false;
                if (jobsearch_plugin_vars.autocomplete_adres_type == 'city_contry') {
                    city_contry_str = true;
                }
                var selected_contries = jobsearch_plugin_vars.sel_countries_json;
                if (selected_contries != '') {
                    var selected_contries_tojs = jQuery.parseJSON(selected_contries);
                    var sel_countries_str = selected_contries_tojs.join();
                    geocodParams['countries'] = sel_countries_str;
                }
                var mapboxGeocoder = new MapboxGeocoder(geocodParams);
                document.getElementById('jobsearch-bodymapbox-gensbox').appendChild(mapboxGeocoder.onAdd(cityAcMap));
                return this.each(function () {
                    var input = jQuery(this), opts = jQuery.extend({}, jQuery.cityAutocomplete);
                    var predictionsDropDown = jQuery('<div class="jobsearch_location_autocomplete"></div>').appendTo(jQuery(this).parent());
                    var request_var = 1;
                    input.keyup(function () {
                        if (request_var == 1) {
                            var searchStr = jQuery(this).val();
                            // Min Number of characters
                            var num_of_chars = 1;
                            if (searchStr.length > num_of_chars) {
                                jQuery(this).parent(".jobsearch_searchloc_div").find('.loc-loader').html("<i class='fa fa-refresh fa-spin'></i>");

                                mapboxGeocoder.query(searchStr);
                                mapboxGeocoder.on('results', function (obj) {
                                    //console.log(obj);
                                    //console.log(obj.features);
                                    updatePredictions(obj);
                                });
                            }
                        } else {
                            //jQuery(".jobsearch_searchloc_div").find('.loc-loader').html('');
                        }
                    });
                    predictionsDropDown.delegate('div', 'click', function () {
                        if (jQuery(this).text() != jobsearch_plugin_vars.var_address_str && jQuery(this).text() != jobsearch_plugin_vars.var_other_locs_str) {
                            // address with slug
                            var jobsearch_address_html = jQuery(this).text();
                            // slug only
                            var jobsearch_address_slug = jQuery(this).find('span').html();
                            // remove slug
                            jQuery(this).find('span').remove();
                            input.val(jQuery(this).text());
                            input.next('.loc_search_keyword').val(jobsearch_address_slug);
                            predictionsDropDown.hide();
                            input.next('.loc_search_keyword').closest("form.side-loc-srch-form").submit();
                        }
                    });
                    jQuery(document).mouseup(function (e) {
                        predictionsDropDown.hide();
                    });
                    jQuery(window).resize(function () {
                        updatePredictionsDropDownDisplay(predictionsDropDown, input);
                    });
                    updatePredictionsDropDownDisplay(predictionsDropDown, input);
                    function updatePredictions(predictions) {

                        var google_results = '';
                        if (typeof predictions.features !== 'undefined' && predictions.features.length > 0) {

                            var predFeats = predictions.features;
                            google_results += '<div class="address_headers"><h5>' + jobsearch_plugin_vars.var_address_str + '</h5></div>';
                            jQuery.each(predFeats, function (i, prediction) {
                                var placename_str;
                                if (city_contry_str === true) {
                                    placename_str = jobsearch_fulladres_to_city_contry(prediction);
                                } else {
                                    placename_str = prediction.place_name;
                                }
                                google_results += '<div class="jobsearch_google_suggestions"><i class="icon-location-arrow"></i>' + placename_str + '<span style="display:none">' + placename_str + '</span></div>';
                            });
                            request_var = 0;
                        } else {
                            predictionsDropDown.empty();
                        }
                        // AJAX GET Locations
                        var dataString = 'action=jobsearch_get_all_db_locations' + '&keyword=' + jQuery('.jobsearch_search_location_field').val();
                        var plugin_url = jobsearch_plugin_vars.ajax_url;

                        if (typeof (locAutoSrchAjax) != 'undefined') {
                            locAutoSrchAjax.abort();
                        }
                        locAutoSrchAjax = jQuery.ajax({
                            type: "POST",
                            url: plugin_url,
                            data: dataString,
                        });

                        locAutoSrchAjax.done(function (data) {
                            jQuery(".jobsearch_searchloc_div").find('.loc-loader').html('');
                            var results = jQuery.parseJSON(data);
                            predictionsDropDown.empty();
                            predictionsDropDown.append(google_results);
                            if (results != '') {

                                predictionsDropDown.append('<div class="address_headers"><h5>' + jobsearch_plugin_vars.var_other_locs_str + '</h5></div>');
                                jQuery(results).each(function (key, value) {
                                    if (value.hasOwnProperty('child')) {
                                        jQuery(value.child).each(function (child_key, child_value) {
                                            predictionsDropDown.append('<div class="jobsearch_location_child">' + child_value.value + '<span style="display:none">' + child_value.slug + '</span></div');
                                        })
                                    } else {
                                        predictionsDropDown.append('<div class="jobsearch_location_parent">' + value.value + '<span style="display:none">' + value.slug + '</span></div');
                                    }
                                })
                            }
                            request_var = 1;
                        });

                        locAutoSrchAjax.fail(function (jqXHR, textStatus) {
                            //jQuery(".jobsearch_searchloc_div").find('.loc-loader').html('');
                        });

                        predictionsDropDown.show();
                    }
                    return input;
                });
            }

        } else {

            return this.each(function () {
                var googleConObj = google.maps;
                //console.log(googleConObj.places);
                if (typeof googleConObj.places !== 'undefined') {
                    var input = jQuery(this), opts = jQuery.extend({}, jQuery.cityAutocomplete);
                    var autocompleteService = new google.maps.places.AutocompleteService();
                    var predictionsDropDown = jQuery('<div class="jobsearch_location_autocomplete" class="city-autocomplete"></div>').appendTo(jQuery(this).parent());
                    var request_var = 1;
                    input.keyup(function () {

                        var adress_type = '';
                        if (jobsearch_plugin_vars.autocomplete_adres_type == 'city_contry') {
                            adress_type = ['(cities)'];
                        }
                        jQuery(this).parent(".jobsearch_searchloc_div").find('.loc-loader').html("<i class='fa fa-refresh fa-spin'></i>");
                        if (request_var == 1) {
                            var searchStr = jQuery(this).val();
                            // Min Number of characters
                            var num_of_chars = 0;
                            if (searchStr.length > num_of_chars) {
                                var params = {
                                    input: searchStr,
                                    bouns: 'upperbound',
                                    types: adress_type
                                };
                                var selected_contries_json = '';
                                var selected_contries = jobsearch_plugin_vars.sel_countries_json;
                                if (selected_contries != '') {
                                    var selected_contries_tojs = jQuery.parseJSON(selected_contries);
                                    selected_contries_json = {country: selected_contries_tojs};
                                }
                                params.componentRestrictions = selected_contries_json; //{country: window.country_code}

                                autocompleteService.getPlacePredictions(params, updatePredictions);
                            } else {
                                jQuery(".jobsearch_searchloc_div").find('.loc-loader').html('');
                            }
                        }
                    });
                    predictionsDropDown.delegate('div', 'click', function () {
                        if (jQuery(this).text() != jobsearch_plugin_vars.var_address_str && jQuery(this).text() != jobsearch_plugin_vars.var_other_locs_str) {
                            // address with slug
                            var jobsearch_address_html = jQuery(this).text();
                            // slug only
                            var jobsearch_address_slug = jQuery(this).find('span').html();
                            // remove slug
                            jQuery(this).find('span').remove();
                            input.val(jQuery(this).text());
                            input.next('.loc_search_keyword').val(jobsearch_address_slug);
                            predictionsDropDown.hide();
                            input.next('.loc_search_keyword').closest("form.side-loc-srch-form").submit();
                        }
                    });
                    jQuery(document).mouseup(function (e) {
                        predictionsDropDown.hide();
                    });
                    jQuery(window).resize(function () {
                        updatePredictionsDropDownDisplay(predictionsDropDown, input);
                    });
                    updatePredictionsDropDownDisplay(predictionsDropDown, input);
                    function updatePredictions(predictions, status) {

                        var google_results = '';
                        if (google.maps.places.PlacesServiceStatus.OK == status) {

                            // AJAX GET ADDRESS FROM GOOGLE
                            google_results += '<div class="address_headers"><h5>' + jobsearch_plugin_vars.var_address_str + '</h5></div>';
                            jQuery.each(predictions, function (i, prediction) {
                                google_results += '<div class="jobsearch_google_suggestions"><i class="icon-location-arrow"></i> ' + prediction.description + '<span style="display:none">' + prediction.description + '</span></div>';
                            });
                            request_var = 0;
                        } else {
                            predictionsDropDown.empty();
                        }
                        // AJAX GET Locations
                        var dataString = 'action=jobsearch_get_all_db_locations' + '&keyword=' + jQuery('.jobsearch_search_location_field').val();
                        var plugin_url = jobsearch_plugin_vars.ajax_url;
                        var request = jQuery.ajax({
                            type: "POST",
                            url: plugin_url,
                            data: dataString,
                        });

                        request.done(function (data) {
                            jQuery(".jobsearch_searchloc_div").find('.loc-loader').html('');
                            var results = jQuery.parseJSON(data);
                            predictionsDropDown.empty();
                            predictionsDropDown.append(google_results);
                            if (results != '') {

                                predictionsDropDown.append('<div class="address_headers"><h5>' + jobsearch_plugin_vars.var_other_locs_str + '</h5></div>');
                                jQuery(results).each(function (key, value) {
                                    if (value.hasOwnProperty('child')) {
                                        jQuery(value.child).each(function (child_key, child_value) {
                                            predictionsDropDown.append('<div class="jobsearch_location_child">' + child_value.value + '<span style="display:none">' + child_value.slug + '</span></div');
                                        })
                                    } else {
                                        predictionsDropDown.append('<div class="jobsearch_location_parent">' + value.value + '<span style="display:none">' + value.slug + '</span></div');
                                    }
                                })
                            }
                            request_var = 1;
                        });

                        request.fail(function (jqXHR, textStatus) {
                            jQuery(".jobsearch_searchloc_div").find('.loc-loader').html('');
                        });

                        predictionsDropDown.show();
                    }
                    return input;
                }
            });
        }
    }
});

function updatePredictionsDropDownDisplay(dropDown, input) {
    if (typeof (input.offset()) !== 'undefined') {
        dropDown.css({
            'width': input.outerWidth(),
            'left': input.offset().left,
            'top': input.offset().top + input.outerHeight()
        });
    }
}

function jobsearch_fulladres_to_city_contry(geoData) {
    // debugger;
    var region, countryName, placeName, returnStr;
    if (geoData.context) {
        $.each(geoData.context, function (i, v) {
            if (v.id.indexOf('region') >= 0) {
                region = v.text;
            }
            if (v.id.indexOf('country') >= 0) {
                countryName = v.text;
            }
        });
    }
    if (region && countryName) {
        returnStr = region + ", " + countryName;
    } else {
        returnStr = geoData.place_name;
    }
    return returnStr;
}

jQuery(document).on('click', '.jobsearch_search_location_field', function() {
    jQuery('.jobsearch_search_location_field').cityAutocomplete();
});
jQuery(document).on('click', '.jobsearch_searchloc_div', function () {
    jQuery('.jobsearch_search_location_field').prop('disabled', false);
});
jQuery(document).on('change', '.jobsearch_search_location_field', function () {
    var this_new_loc = jQuery(this).val();
    if (typeof jobsearch_listing_dataobj !== 'undefined') {
        var locMapType = jobsearch_plugin_vars.locmap_type;
        if (locMapType == 'mapbox') {
            var mapbox_access_token = jobsearch_plugin_vars.mapbox_token;
            var map_addrapi_uri = 'https://api.mapbox.com/geocoding/v5/mapbox.places/' + encodeURI(this_new_loc) + '.json?access_token=' + mapbox_access_token;
            jobsearch_common_getJSON(map_addrapi_uri, function (new_loc_status, new_loc_response) {
                if (typeof new_loc_response === 'object') {
                    var new_cords = new_loc_response.features[0].geometry.coordinates;
                    if (new_cords !== 'undefined') {
                        jobsearch_listing_map.flyTo({
                            center: new_cords,
                        });
                    }
                }
            });
        } else {
            var google_api_key = jobsearch_plugin_vars.google_api_key;
            var map_addrapi_uri = 'https://maps.googleapis.com/maps/api/geocode/json?address=' + encodeURI(this_new_loc) + '&sensor=false&key=' + google_api_key;
            jobsearch_common_getJSON(map_addrapi_uri, function (new_loc_status, new_loc_response) {
                if (typeof new_loc_response === 'object') {
                    var new_cords = new_loc_response.results[0].geometry.location;
                    if (new_cords !== 'undefined') {
                        jobsearch_listing_map.setCenter(new_cords);
                    }
                }
            });
        }
    }
});

jQuery(document).on('click', 'form', function () {
    var src_loc_val = jQuery(this).find('.jobsearch_search_location_field');
    src_loc_val.next('.loc_search_keyword').val(src_loc_val.val());
    //
    if (jQuery('.jobsearch-filter-keywordsrch').length > 0) {
        var srch_keyword_val = jQuery(this).find('.jobsearch-keywordsrch-inp-field').val();
        jQuery('.jobsearch-filter-keywordsrch').find('.jobsearch-keywordsrch-filinp-field').val(srch_keyword_val);
        jQuery('.jobsearch-filter-keywordsrch').find('.jobsearch-keywordsrch-filinp-field').attr('value', srch_keyword_val);
    }
});