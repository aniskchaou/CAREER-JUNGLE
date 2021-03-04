var employerFilterAjax;

function jobsearch_employer_content_load(counter, view_type, animate_to) {
    //"use strict";

    counter = counter || '';
    animate_to = animate_to || '';
    var view_type = view_type || '';
    // move to top when search filter apply

//    if (animate_to != 'false') {
//        jQuery('html, body').animate({
//            scrollTop: jQuery("#wp-dp-employer-content-" + counter).offset().top - 120
//        }, 700);
//    }
//alert(counter + '<=counter');
    var employer_arg = jQuery("#employer_arg" + counter).html();
    var this_frm = jQuery("#jobsearch_employer_frm_" + counter);


    var split_map = jQuery(".wp-dp-split-map-wrap").length;
    if (split_map > 0) {
        view_type = 'split_map';
    }

    var ads_list_count = jQuery("#ads_list_count_" + counter).val();
    var ads_list_flag = jQuery("#ads_list_flag_" + counter).val();
    var list_flag_count = jQuery("#ads_list_flag_count_" + counter).val();
    var current_page_id = jQuery("#current_page_id").val();
    var loader_con = jQuery("#jobsearch-loader-" + counter);
    // alert("#jobsearch_employer_frm_" + counter);
    if (jQuery("#jobsearch_employer_frm_" + counter).length > 0) {
        //alert('1');
        var data_vals = jQuery(jQuery("#jobsearch_employer_frm_" + counter)[0].elements).not(":input[name='alerts-email'], :input[name='alert-frequency']").serialize();

        //alert('12');
        if (jQuery('form[name="jobsearch-top-map-form"]').length > 0) {
            data_vals += "&" + jQuery('form[name="jobsearch-top-map-form"]').serialize();
        }
        data_vals = data_vals.replace(/[^&]+=\.?(?:&|$)/g, ''); // remove extra and empty variables
        data_vals = data_vals.replace('undefined', ''); // remove extra and empty variables
        data_vals = data_vals + '&ajax_filter=true';
        data_vals = stripUrlParams(data_vals);

        //if (!jQuery('body').hasClass('wp-dp-changing-view')) {
        // top map
        // top_map_change_cords(counter);
        //}
        // alert("2");
        jQuery('#Employer-content-' + counter + ' .employer').addClass('slide-loader');
        jQuery('#jobsearch-data-employer-content-' + counter + ' .all-results').addClass('slide-loader');


        if (typeof (employerFilterAjax) != 'undefined') {
            employerFilterAjax.abort();
        }
        loader_con.html('<div class="jobsearch-listing-loader"><div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>');
        console.info(employer_arg+"---employer_arg")
        var employerFilterAjax = jQuery.ajax({
            type: 'POST',
            dataType: 'HTML',
            url: jobsearch_plugin_vars.ajax_url,
            data: data_vals + '&action=jobsearch_employers_content&view_type=' + view_type + '&employer_arg=' + employer_arg + '&page_id=' + current_page_id,
            success: function (response) {
                jQuery('body').removeClass('wp-dp-changing-view');
                jQuery('#Employer-content-' + counter).html(response);
                // Replace double & from string.
                data_vals = data_vals.replace("&&", "&");
                var current_url = location.protocol + "//" + location.host + location.pathname + "?" + data_vals; //window.location.href;
                current_url = current_url.replace('&=undefined', ''); // remove extra and empty variables
                window.history.pushState(null, null, decodeURIComponent(current_url));
                //jQuery(".chosen-select").chosen();
                //jQuery('#jobsearch-data-employer-content-' + counter + ' .all-results').removeClass('slide-loader');
                //jobsearch_hide_loader();
                // add class when image loaded
                jQuery(".employer-medium .img-holder img, .employer-grid .img-holder img").one("load", function () {
                    jQuery(this).parents(".img-holder").addClass("image-loaded");
                }).each(function () {
                    if (this.complete)
                        jQuery(this).load();
                });
                if (jQuery(".employer-medium.modern").length > 0) {
                    var imageUrlFind = jQuery(".employer-medium.modern .img-holder").css("background-image").match(/url\(["']?([^()]*)["']?\)/).pop();
                    if (imageUrlFind) {
                        jQuery(".employer-medium.modern .img-holder").addClass("image-loaded");
                    }
                }
                jQuery('.wp-dp-employer-content').find('.selectize-select').selectize({
                    //allowEmptyOption: true,
                });
                jQuery('.wp-dp-employer-content').find('.sort-records-per-page').selectize({
                    allowEmptyOption: true,
                });
                loader_con.html('');
                // add class when image loaded
            }
        });
    }
}

function jobsearch_employer_content_load_without_filters(counter, page_var, page_num, ajax_filter, view_type) {
    "use strict";
    counter = counter || '';
    var employer_arg = jQuery("#employer_arg" + counter).html();
    var data_vals = page_var + '=' + page_num;
    jQuery('#jobsearch-data-employer-content-' + counter + ' .all-results').addClass('slide-loader');
    if (typeof (employerFilterAjax) != 'undefined') {
        employerFilterAjax.abort();
    }
    employerFilterAjax = jQuery.ajax({
        type: 'POST',
        dataType: 'HTML',
        url: jobsearch_plugin_vars.ajax_url,
        data: data_vals + '&action=jobsearch_employers_content&view_type=' + view_type + '&employer_arg=' + employer_arg,
        success: function (response) {
            jQuery('#Employer-content-' + counter).html(response);
            // Replace double & from string.
            data_vals = data_vals.replace("&&", "&");
            var current_url = location.protocol + "//" + location.host + location.pathname + "?" + data_vals; //window.location.href;
            window.history.pushState(null, null, decodeURIComponent(current_url));
            jQuery('#jobsearch-data-employer-content-' + counter + ' .all-results').removeClass('slide-loader');
        }
    });
}

function stripUrlParams(args) {
    "use strict";
    var parts = args.split("&");
    var comps = {};
    for (var i = parts.length - 1; i >= 0; i--) {
        var spl = parts[i].split("=");
        // Overwrite only if existing is empty.
        if (typeof comps[ spl[0] ] == "undefined" || (typeof comps[ spl[0] ] != "undefined" && comps[ spl[0] ] == '')) {
            comps[ spl[0] ] = spl[1];
        }
    }
    parts = [];
    for (var a in comps) {
        parts.push(a + "=" + comps[a]);
    }

    return parts.join('&');
}

function jobsearch_employer_filters_content(counter, page_var, page_num, tab) {
    "use strict";
    counter = counter || '';
    var employer_arg = jQuery("#employer_arg" + counter).html();
    var this_frm = jQuery("#jobsearch_employer_frm_" + counter);

    var ads_list_count = jQuery("#ads_list_count_" + counter).val();
    var ads_list_flag = jQuery("#ads_list_flag_" + counter).val();
    var list_flag_count = jQuery("#ads_list_flag_count_" + counter).val();
    var data_vals = 'tab=' + tab + '&' + page_var + '=' + page_num + '&ajax_filter=true';
    jQuery('#jobsearch-data-employer-content-' + counter + ' .all-results').addClass('slide-loader');
    if (typeof (employerFilterAjax) != 'undefined') {
        employerFilterAjax.abort();
    }
    employerFilterAjax = jQuery.ajax({
        type: 'POST',
        dataType: 'HTML',
        url: jobsearch_plugin_vars.ajax_url,
        data: data_vals + '&action=jobsearch_employers_filters_content&employer_arg=' + employer_arg,
        success: function (response) {
            console.log(response);
            jQuery('#employer-tab-content-' + counter).html(response);
            jQuery("#employer-tab-content-" + counter + ' .row').mixItUp({
                selectors: {
                    target: ".portfolio",
                }
            });
            //replace double & from string
            data_vals = data_vals.replace("&&", "&");
            var current_url = location.protocol + "//" + location.host + location.pathname + "?" + data_vals; //window.location.href;
            window.history.pushState(null, null, decodeURIComponent(current_url));
            jQuery(".chosen-select").chosen();
            jQuery('#jobsearch-data-employer-content-' + counter + ' .all-results').removeClass('slide-loader');
            // add class when image loaded
            jQuery(".employer-medium .img-holder img, .employer-grid .img-holder img").one("load", function () {
                jQuery(this).parents(".img-holder").addClass("image-loaded");
            }).each(function () {
                if (this.complete)
                    $(this).load();
            });
            // add class when image loaded
        }
    });

}

function jobsearch_employer_by_categories_filters_content(counter, page_var, page_num, tab) {
    "use strict";
    counter = counter || '';
    var employer_arg = jQuery("#employer_arg" + counter).html();
    var this_frm = jQuery("#jobsearch_employer_frm_" + counter);

    var ads_list_count = jQuery("#ads_list_count_" + counter).val();
    var ads_list_flag = jQuery("#ads_list_flag_" + counter).val();
    var list_flag_count = jQuery("#ads_list_flag_count_" + counter).val();
    var data_vals = 'tab=' + tab + '&' + page_var + '=' + page_num + '&ajax_filter=true';
    jQuery('#jobsearch-data-employer-content-' + counter + ' .all-results').addClass('slide-loader');
    if (typeof (employerFilterAjax) != 'undefined') {
        employerFilterAjax.abort();
    }
    employerFilterAjax = jQuery.ajax({
        type: 'POST',
        dataType: 'HTML',
        url: jobsearch_plugin_vars.ajax_url,
        data: data_vals + '&action=jobsearch_employer_by_categories_filters_content&employer_arg=' + employer_arg,
        success: function (response) {
            jQuery('#employer-tab-content-' + counter).html(response);
            jQuery("#employer-tab-content-" + counter + ' .row').mixItUp({
                selectors: {
                    target: ".portfolio",
                }
            });
            //replace double & from string
            data_vals = data_vals.replace("&&", "&");
            var current_url = location.protocol + "//" + location.host + location.pathname + "?" + data_vals; //window.location.href;
            window.history.pushState(null, null, decodeURIComponent(current_url));
            jQuery(".chosen-select").chosen();
            jQuery('#jobsearch-data-employer-content-' + counter + ' .all-results').removeClass('slide-loader');
            // add class when image loaded
            jQuery(".employer-medium .img-holder img, .employer-grid .img-holder img").one("load", function () {
                jQuery(this).parents(".img-holder").addClass("image-loaded");
            }).each(function () {
                if (this.complete)
                    $(this).load();
            });
            // add class when image loaded
        }
    });

}

function convertHTML(html) {
    "use strict";
    var newHtml = $.trim(html),
            $html = $(newHtml),
            $empty = $();

    $html.each(function (index, value) {
        if (value.nodeType === 1) {
            $empty = $empty.add(this);
        }
    });

    return $empty;
}

function jobsearch_employer_type_search_fields(thisObj, counter, price_switch, view_type) {
    var view_type = view_type || '';
    "use strict";
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: jobsearch_plugin_vars.ajax_url,
        data: '&action=jobsearch_employer_type_search_fields&employer_short_counter=' + counter + '&employer_type_slug=' + thisObj.value + '&price_switch=' + price_switch + '&view_type=' + view_type,
        success: function (response) {
            jQuery('#employer_type_fields_' + counter).html('');
            jQuery('#employer_type_fields_' + counter).html(response.html);
        }
    });

    var checkID = thisObj.getAttribute('id');

    var cat_name = $('#' + checkID).next('label').html();

    $('.map-search-keyword-type-holder .dropdown-types-btn').html(cat_name);

    var dropdownHolder = $('.map-search-keyword-type-holder');
    var dropdownCon = dropdownHolder.find('ul.dropdown-types');
    dropdownCon.slideUp();
}

function jobsearch_employer_type_cate_fields(thisObj, counter, cats_switch, view, color) {
    "use strict";
    if (typeof view === 'undefined') {
        view = 'default';
    }
    if (typeof color === 'undefined') {
        color = 'none';
    }
    var cate_loader = '<b class="spinner-label">' + jobsearch_employer_functions_string.employer_type + '</b><span class="cate-spinning"><i class="fancy-spinner"></i></span>';
    jQuery('#employer_type_cate_fields_' + counter).html(cate_loader);
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: jobsearch_plugin_vars.ajax_url,
        data: '&action=jobsearch_employer_type_cate_fields&employer_short_counter=' + counter + '&employer_type_slug=' + thisObj.value + '&view=' + view + '&color=' + color + '&cats_switch=' + cats_switch,
        success: function (response) {
            jQuery('#employer_type_cate_fields_' + counter).html('');
            jQuery('#employer_type_cate_fields_' + counter).html(response.html);
        }
    });
}

function jobsearch_employer_type_split_map_search_fields(thisObj, counter) {

    var view_type = view_type || '';
    "use strict";
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: jobsearch_plugin_vars.ajax_url,
        data: '&action=jobsearch_employer_type_split_map_search_fields&employer_short_counter=' + counter + '&employer_type_slug=' + thisObj.value,
        success: function (response) {
            jQuery('#employer_type_fields_' + counter).html('');
            jQuery('#employer_type_fields_' + counter).html(response.html);
        }
    }).done(function () {
        jobsearch_employer_type_split_map_cate_fields(thisObj.value, counter);
    });

    var checkID = thisObj.getAttribute('id');
    var cat_name = $('#' + checkID).next('label').html();
    $('.map-search-keyword-type-holder .dropdown-types-btn').html(cat_name);

    var dropdownHolder = $('.map-search-keyword-type-holder');
    var dropdownCon = dropdownHolder.find('ul.dropdown-types');
    dropdownCon.slideUp();
}

function jobsearch_employer_type_split_map_cate_fields(thisObj, counter, cats_switch, view, color) {
    "use strict";
    if (typeof view === 'undefined') {
        view = 'default';
    }
    if (typeof color === 'undefined') {
        color = 'none';
    }
    var cate_loader = '<b class="spinner-label">' + jobsearch_employer_functions_string.employer_type + '</b><span class="cate-spinning"><i class="fancy-spinner"></i></span>';
    jQuery('#employer_type_cate_fields_' + counter).html(cate_loader);
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: jobsearch_plugin_vars.ajax_url,
        data: '&action=jobsearch_employer_type_split_map_cate_fields&employer_short_counter=' + counter + '&employer_type_slug=' + thisObj.value + '&view=' + view + '&color=' + color,
        success: function (response) {
            jQuery('#employer_type_cate_fields_' + counter).html('');
            jQuery('#employer_type_cate_fields_' + counter).html(response.html);
        }
    }).done(function () {
        jobsearch_split_map_change_cords(counter);
    });
}

function jobsearch_split_map_change_cords(counter, hide_overlay) {
    "use strict";
    var hide_overlay = hide_overlay || '';
    if (hide_overlay === 'true') {
        //jQuery(".main-search.split-map .field-holder.more-filters-btn").toggleClass('open');
        //jQuery("div.split-map-overlay").remove();
        //jQuery('#employer_type_fields_'+ counter ).toggle('slow');
        //jQuery(".main-search.split-map .adv_filter_toggle").val('false');
    }
    var top_map = jQuery('.wp-dp-ontop-gmap');
    var loader_div = jQuery('.wp-dp-splitmap-advance-filter_' + counter);
    var loader_html = '<div class="split-map-loader"><span><i class="fancy-spinner"></i></span></div>';
    if (loader_div.length !== 0) {
        loader_div.html(loader_html);
    }
    if (top_map.length !== 0) {
        var ajax_url = jobsearch_plugin_vars.ajax_url;
        var data_vals = 'ajax_filter=true&map=top_map&action=jobsearch_top_map_search&' + jQuery(jQuery("#jobsearch_employer_frm_" + counter)[0].elements).not(":input[name='alerts-email'], :input[name='alert-frequency']").serialize();
        if (jQuery('form[name="wp-dp-top-map-form"]').length > 0) {
            data_vals += "&" + jQuery('form[name="wp-dp-top-map-form"]').serialize() + '&atts=' + jQuery('#atts').html();
        }
        data_vals = stripUrlParams(data_vals);
        var loading_top_map = $.ajax({
            url: ajax_url,
            method: "POST",
            data: data_vals,
            dataType: "json"
        }).done(function (response) {
            if (typeof response.html !== 'undefined') {
                jQuery('.top-map-action-scr').html(response.html);
            }
            jobsearch_employer_split_map_content(counter, '', '', hide_overlay);
        }).fail(function () {
        });
    }
}

function removeURLParameter(url, parameter) {
    //prefer to use l.search if you have a location/link object
    var urlparts = url.split('?');
    if (urlparts.length >= 2) {

        var prefix = encodeURIComponent(parameter) + '=';
        var pars = urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i = pars.length; i-- > 0; ) {
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                pars.splice(i, 1);
            }
        }

        url = urlparts[0] + '?' + pars.join('&');
        return url;
    } else {
        return url;
    }
}

function jobsearch_employer_split_map_content(counter, view_type, animate_to, hide_overlay) {
    //"use strict";

    counter = counter || '';
    var hide_overlay = hide_overlay || '';
    animate_to = animate_to || '';
    var view_type = view_type || '';
    var loader_div = jQuery('.wp-dp-splitmap-advance-filter_' + counter);
    var loader_html = '<div class="split-map-loader"><span><i class="fancy-spinner"></i></span></div>';
    // move to top when search filter apply

    if (animate_to != 'false') {
        jQuery('html, body').animate({
            scrollTop: jQuery("#wp-dp-employer-content-" + counter).offset().top - 120
        }, 700);
    }
    var employer_arg = jQuery("#employer_arg" + counter).html();
    var this_frm = jQuery("#jobsearch_employer_frm_" + counter);


    var split_map = jQuery(".wp-dp-split-map-wrap").length;
    if (split_map > 0) {
        view_type = 'split_map';
    }

    var ads_list_count = jQuery("#ads_list_count_" + counter).val();
    var ads_list_flag = jQuery("#ads_list_flag_" + counter).val();
    var list_flag_count = jQuery("#ads_list_flag_count_" + counter).val();

    if (jQuery("#jobsearch_employer_frm_" + counter).length > 0) {
        var data_vals = jQuery(jQuery("#jobsearch_employer_frm_" + counter)[0].elements).not(":input[name='alerts-email'], :input[name='alert-frequency']").serialize();
        var data_vals = 'ajax_filter=true&map=top_map&action=jobsearch_top_map_search&' + jQuery(jQuery("#jobsearch_employer_frm_" + counter)[0].elements).not(":input[name='alerts-email'], :input[name='alert-frequency']").serialize();
        if (jQuery('form[name="wp-dp-top-map-form"]').length > 0) {
            data_vals += "&" + jQuery('form[name="wp-dp-top-map-form"]').serialize();
        }
        data_vals = data_vals.replace(/[^&]+=\.?(?:&|$)/g, ''); // remove extra and empty variables
        data_vals = data_vals.replace('undefined', ''); // remove extra and empty variables
        data_vals = data_vals + '&ajax_filter=true';
        data_vals = stripUrlParams(data_vals);
        if (!jQuery('body').hasClass('wp-dp-changing-view')) {
            // top map
            //top_map_change_cords(counter);
        }

        if (hide_overlay === 'true') {
            data_vals = removeURLParameter(data_vals, 'adv_filter_toggle');
            data_vals = data_vals.replace('adv_filter_toggle=true', 'adv_filter_toggle=false');
        }

        jQuery('#Employer-content-' + counter + ' .employer').addClass('slide-loader');
        jQuery('#jobsearch-data-employer-content-' + counter + ' .all-results').addClass('slide-loader');
        //if (typeof (employerFilterAjax) !== 'undefined') {
        //    employerFilterAjax.abort();
        //}

        console.log(data_vals);
        var employerFilterAjax = jQuery.ajax({
            type: 'POST',
            dataType: 'HTML',
            url: jobsearch_plugin_vars.ajax_url,
            data: data_vals + '&action=jobsearch_employers_content&view_type=' + view_type + '&employer_arg=' + employer_arg,
            success: function (response) {
                jQuery('body').removeClass('wp-dp-changing-view');
                jQuery('#Employer-content-' + counter).html(response);

                if (hide_overlay === 'false' && hide_overlay !== '') {
                    jQuery('#wp-jobsearch-employer-' + counter).hide();
                    jQuery('.no-employer-match-error').hide();
                    jQuery('.page-nation').hide();
                }
                if (hide_overlay === 'true' && hide_overlay !== '') {
                    //jQuery(".main-search.split-map .field-holder.more-filters-btn").toggleClass('open');
                    jQuery("div.split-map-overlay").remove();
                    //jQuery('#employer_type_fields_'+ counter ).toggle('slow');
                }

                // Replace double & from string.
                data_vals = data_vals.replace("&&", "&");
                var current_url = location.protocol + "//" + location.host + location.pathname + "?" + data_vals; //window.location.href;
                current_url = current_url.replace('&=undefined', ''); // remove extra and empty variables
                window.history.pushState(null, null, decodeURIComponent(current_url));
                jQuery(".chosen-select").chosen();
                jQuery('#jobsearch-data-employer-content-' + counter + ' .all-results').removeClass('slide-loader');
                jobsearch_hide_loader();
                // add class when image loaded
                jQuery(".employer-medium .img-holder img, .employer-grid .img-holder img").one("load", function () {
                    jQuery(this).parents(".img-holder").addClass("image-loaded");
                }).each(function () {
                    if (this.complete)
                        jQuery(this).load();
                });
                if (jQuery(".employer-medium.modern").length > 0) {
                    var imageUrlFind = jQuery(".employer-medium.modern .img-holder").css("background-image").match(/url\(["']?([^()]*)["']?\)/).pop();
                    if (imageUrlFind) {
                        jQuery(".employer-medium.modern .img-holder").addClass("image-loaded");
                    }
                }

                // add class when image loaded

                if (loader_div.length !== 0) {
                    loader_div.html('');
                }
            }
        });
    }
}

function jobsearch_empty_loc_polygon(counter) {
    if (jQuery("#jobsearch_employer_frm_" + counter + " input[name=loc_polygon_path]").length) {
        jQuery("#jobsearch_employer_frm_" + counter + " input[name=loc_polygon_path]").val('');
    }
}

function jobsearch_employer_view_switch(view, counter, employer_short_counter, view_type) {
    "use strict";
    var view_type = view_type || '';
    jQuery.ajax({
        type: 'POST',
        dataType: 'HTML',
        url: jobsearch_plugin_vars.ajax_url,
        data: '&action=jobsearch_employer_view_switch&view=' + view + '&employer_short_counter=' + employer_short_counter,
        success: function () {
            jQuery('[data-toggle="popover"]').popover();
            jQuery('body').addClass('wp-dp-changing-view');
            jobsearch_employer_content(counter, view_type);
        }
    });
}

function jobsearch_employer_pagenation_ajax(page_var, page_num, counter, ajax_filter, view_type) {
    "use strict";
    var view_type = view_type || '';
    jQuery('html, body').animate({
        scrollTop: jQuery("#wp-dp-employer-content-" + counter).offset().top - 120
    }, 1000);
    jQuery('#' + page_var + '-' + counter).val(page_num);
    jobsearch_employer_content_load(counter, view_type);
    //alert("aaaaaaaa");
//    if (ajax_filter == 'false') {
//        if (view_type == "split_map") {
//            jobsearch_employer_content_load(counter, view_type);
//        } else {
//            jobsearch_employer_content_load_without_filters(counter, page_var, page_num, ajax_filter, view_type);
//        }
//    } else {
//        jobsearch_employer_content_load(counter, view_type);
//    }
}

function jobsearch_employer_filters_pagenation_ajax(page_var, page_num, counter, tab) {
    "use strict";
    jQuery('#' + page_var + '-' + counter).val(page_num);
    jobsearch_employer_filters_content(counter, page_var, page_num, tab);
}

function jobsearch_employer_by_category_filters_pagenation_ajax(page_var, page_num, counter, tab) {
    "use strict";
    jQuery('#' + page_var + '-' + counter).val(page_num);
    jobsearch_employer_by_categories_filters_content(counter, page_var, page_num, tab);
}

function jobsearch_advanced_search_field(counter, tab, element) {
    "use strict";
    if (tab == 'simple') {
        jQuery('#employer_type_fields_' + counter).slideUp();
        jQuery('#nav-tabs-' + counter + ' li').removeClass('active');
        jQuery(element).parent().addClass('active');
    } else if (tab == 'advance') {
        jQuery('#employer_type_fields_' + counter).slideDown();
        jQuery('#nav-tabs-' + counter + ' li').removeClass('active');
        jQuery(element).parent().addClass('active');
    } else {
        jQuery('#employer_type_fields_' + counter).slideToggle();

        if (jQuery(".main-search.split-map .field-holder.more-filters-btn").length > 0) {
            jQuery('.main-search.split-map .field-holder.more-filters-btn').toggleClass('open');
            if (jQuery('.main-search.split-map .field-holder.more-filters-btn').hasClass('open')) {
                var NewContent = '<div class="split-map-overlay"></div>';
                jQuery(".wp-dp-top-map-holder").after(NewContent);
                jQuery('#wp-jobsearch-employer-' + counter).hide();
                jQuery('.page-nation').hide();
                jQuery('.no-employer-match-error').hide();
            } else {
                jQuery("div.split-map-overlay").remove();
                jQuery('#wp-jobsearch-employer-' + counter).show();
                jQuery('.page-nation').show();
                jQuery('.no-employer-match-error').show();
            }
        }
    }
}

if (jQuery(".main-search.split-map .field-holder.more-filters-btn").length > 0) {
    function jobsearch_split_map_close_search(counter) {
        jQuery(".main-search.split-map .field-holder.more-filters-btn").toggleClass('open');
        jQuery("div.split-map-overlay").remove();
        jQuery('#employer_type_fields_' + counter).toggle('slow');
        jQuery('#wp-jobsearch-employer-' + counter).show();
        jQuery('.page-nation').show();
        jQuery('.no-employer-match-error').show();
    }
}

function jobsearch_search_features(element, counter) {
    "use strict";
    jQuery('#employer_type_fields_' + counter + ' .features-field-expand').slideToggle();
    var expand_class = jQuery('#employer_type_fields_' + counter + ' .features-list .advance-trigger').find('i').attr('class');
    if (expand_class == 'icon-plus') {
        console.log(expand_class);
        jQuery('#employer_type_fields_' + counter + ' .features-list .advance-trigger').find('i').removeClass(expand_class).addClass('icon-minus')
    } else {
        jQuery('#employer_type_fields_' + counter + ' .features-list .advance-trigger').find('i').removeClass(expand_class).addClass('icon-plus')
    }
}


jQuery(document).on('click', '.dev-employer-list-enquiry-own-employer', function (e) {
    e.stopImmediatePropagation();
    var msg_obj = {msg: jobsearch_plugin_vars.own_employer_error, type: 'error'};
    jobsearch_show_response(msg_obj);
});


/*
 * Enquiries Block
 */

jQuery(document).ready(function () {
    if (jQuery('#enquires-sidebar-panel').length > 0) {
        enquiry_sidebar_arrow();
    }
});

function enquiry_sidebar_arrow() {
    jQuery('.fixed-sidebar-panel.left .sidebar-panel-btn').click(function (e) {
        e.preventDefault();
        if (jQuery('#enquires-sidebar-panel').hasClass('sidebar-panel-open')) {
            jQuery('#enquires-sidebar-panel').removeClass('sidebar-panel-open');
        } else {
            jQuery('#enquires-sidebar-panel').addClass('sidebar-panel-open');
        }
    });
    jQuery('#enquires-sidebar-panel .sidebar-panel-title .sidebar-panel-btn-close').click(function (e) {
        jQuery('#enquires-sidebar-panel').removeClass('sidebar-panel-open');
    });
}
/*
 * Enquiry Multiple select
 */

jQuery(document).on('click', '.employer-list-enquiry-check', function () {
    var data_id = jQuery(this).data('id');
    var thisObj = jQuery(this);
    if (thisObj.hasClass('active')) {
        jQuery('.chosen-enquires-list .sidebar-employers-list ul li[data-id="' + data_id + '"] .employer-item-dpove').click();
        return;
    }
    thisObj.append('<span class="enquiry-loader"><i class="fancy-spinner"></i></span>');
    jQuery.ajax({
        type: 'POST',
        dataType: 'HTML',
        url: jobsearch_plugin_vars.ajax_url,
        data: 'action=jobsearch_enquiry_list_frontend&employer_id=' + data_id + '&add_enquiry=yes',
        success: function (response) {
            thisObj.find('.enquiry-loader').remove();
            jQuery('.chosen-enquires-list .sidebar-employers-list ul').append(response);
            if (response != '') {
                thisObj.addClass('active');
                if (!jQuery('.chosen-enquires-list').hasClass('sidebar-panel-open')) {
                    jQuery('#enquires-sidebar-panel').addClass('sidebar-panel-open');
                    jQuery('#enquires-sidebar-panel .sidebar-panel-btn').fadeIn('slow');
                }
                var _appending_inp = jQuery("#jobsearch_employer_id");
                if (_appending_inp.val() == '') {
                    _appending_inp.val(data_id);
                } else {
                    console.log(data_id);
                    var new_val = _appending_inp.val() + ',' + data_id;
                    _appending_inp.val(new_val);
                }
            }
        }
    });
});

/*
 * Enquiry Remove from Multiple select
 */

jQuery(document).on('click', '.chosen-enquires-list .sidebar-employers-list ul li .employer-item-dpove', function () {
    var thisObj = jQuery(this);
    var data_id = thisObj.closest('li').data('id');
    thisObj.html('<i class="fancy-spinner"></i>');
    jQuery('.employer-list-enquiry-check[data-id="' + data_id + '"]').append('<span class="enquiry-loader"><i class="fancy-spinner"></i></span>');
    jQuery.ajax({
        type: 'POST',
        dataType: 'HTML',
        url: jobsearch_plugin_vars.ajax_url,
        data: 'action=jobsearch_enquiry_list_remove_frontend&employer_id=' + data_id,
        success: function (response) {
            jQuery('.employer-list-enquiry-check[data-id="' + data_id + '"]').find('.enquiry-loader').remove();
            jQuery('.employer-list-enquiry-check[data-id="' + data_id + '"]').removeClass('active');
            var strVal = jQuery("#jobsearch_employer_id").val();
            var dataArray = strVal.split(",");
            dataArray.splice(dataArray.indexOf(data_id), 1);
            strVal = dataArray.join(',');
            jQuery("#jobsearch_employer_id").val(strVal);
            if (strVal == '') {
                jQuery('#enquires-sidebar-panel').removeClass('sidebar-panel-open');
                jQuery('#enquires-sidebar-panel .sidebar-panel-btn').fadeOut('slow');
            }
            thisObj.closest('li').slideUp(400, function () {
                thisObj.closest('li').remove();
            });
        }
    });
});


function SidbarPanelHeight() {
    var WindowHeightForSidbarPanel = $(window).height();
    $(".sidebar-employers-list ul").css({"max-height": WindowHeightForSidbarPanel - 200, "overflow-y": "auto"});
}
SidbarPanelHeight();
$(window).resize(function () {
    SidbarPanelHeight();
});

/*
 * Enquiry Reset all Multiple select
 */

jQuery(document).on('click', '.chosen-enquires-list .enquiry-reset-btn', function () {
    var thisObj = jQuery(this);
    jobsearch_show_loader(".chosen-enquires-list .enquiry-reset-btn", "", "button_loader", thisObj);
    jQuery.ajax({
        type: 'POST',
        dataType: 'HTML',
        url: jobsearch_plugin_vars.ajax_url,
        data: 'action=jobsearch_enquiry_list_clear_frontend',
        success: function (response) {
            jQuery('.chosen-enquires-list .sidebar-employers-list ul li').remove();
            jobsearch_hide_button_loader('.chosen-enquires-list .enquiry-reset-btn');
            jQuery('.employer-list-enquiry-check').removeClass('active');
            jQuery("#jobsearch_employer_id").val('');
            jQuery('#enquires-sidebar-panel').removeClass('sidebar-panel-open');
            jQuery('#enquires-sidebar-panel .sidebar-panel-btn').fadeOut('slow');
        }
    });
});
/*
 *  Advance search toggle
 */
jQuery(document).on('click', '.adv-srch-toggle-btn', function () {
    jQuery(this).parents('.jobsearch-advance-search-holdr').find('.adv-search-options').slideToggle();
    var slider_input_con = jQuery(this).parents('.jobsearch-advance-search-holdr').find('.adv-search-options').find('.filter-slider-range');
    var def_radius_val = slider_input_con.find('#loc-def-radiusval').val();
    slider_input_con.find('input[name=loc_radius]').val(def_radius_val);
});