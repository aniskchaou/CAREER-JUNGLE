var jobFilterAjax;

function jobsearch_job_content_load(counter, view_type, animate_to) {
    //"use strict";

    counter = counter || '';
    animate_to = animate_to || '';
    var view_type = view_type || '';
    // move to top when search filter apply

    var job_arg = jQuery("#job_arg" + counter).html();
    var this_frm = jQuery("#jobsearch_job_frm_" + counter);


    var split_map = jQuery(".wp-dp-split-map-wrap").length;
    if (split_map > 0) {
        view_type = 'split_map';
    }

    var ads_list_count = jQuery("#ads_list_count_" + counter).val();
    var ads_list_flag = jQuery("#ads_list_flag_" + counter).val();
    var list_flag_count = jQuery("#ads_list_flag_count_" + counter).val();
    var current_page_id = jQuery("#current_page_id").val();
    var loader_con = jQuery("#jobsearch-loader-" + counter);

    //
    if (jQuery("#jobsearch_job_frm_" + counter).length > 0) {

        var data_vals = jQuery(jQuery("#jobsearch_job_frm_" + counter)[0].elements).not(":input[name='alerts-email'], :input[name='alert-frequency'], :input[name^='loc_count_qargs_']").serialize();

        if (jQuery('form[name="jobsearch-top-map-form"]').length > 0) {
            data_vals += "&" + jQuery('form[name="jobsearch-top-map-form"]').serialize();
        }
        data_vals = data_vals.replace(/[^&]+=\.?(?:&|$)/g, ''); // remove extra and empty variables
        data_vals = data_vals.replace('undefined', ''); // remove extra and empty variables
        data_vals = data_vals + '&ajax_filter=true';
        data_vals = stripUrlParams(data_vals);

        jQuery('#Job-content-' + counter + ' .job').addClass('slide-loader');
        jQuery('#jobsearch-data-job-content-' + counter + ' .all-results').addClass('slide-loader');

        if (typeof (jobFilterAjax) != 'undefined') {
            jobFilterAjax.abort();
        }
        loader_con.html('<div class="jobsearch-listing-loader"><div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>');
        var jobFilterAjax = jQuery.ajax({
            type: 'POST',
            dataType: 'HTML',
            url: jobsearch_plugin_vars.ajax_url,
            data: data_vals + '&action=jobsearch_jobs_content&view_type=' + view_type + '&job_arg=' + job_arg,
            success: function (response) {
                jQuery('body').removeClass('wp-dp-changing-view');
                jQuery('#Job-content-' + counter).html(response);
                // Replace double & from string.
                data_vals = data_vals.replace("&&", "&");
                var current_url = location.protocol + "//" + location.host + location.pathname + "?" + data_vals; //window.location.href;
                current_url = current_url.replace('&=undefined', ''); // remove extra and empty variables
                window.history.pushState(null, null, decodeURIComponent(current_url));
                //jQuery(".chosen-select").chosen();
                //jQuery('#jobsearch-data-job-content-' + counter + ' .all-results').removeClass('slide-loader');
                //jobsearch_hide_loader();
                // add class when image loaded
                jQuery(".job-medium .img-holder img, .job-grid .img-holder img").one("load", function () {
                    jQuery(this).parents(".img-holder").addClass("image-loaded");
                }).each(function () {
                    if (this.complete)
                        jQuery(this).load();
                });
                if (jQuery(".job-medium.modern").length > 0) {
                    var imageUrlFind = jQuery(".job-medium.modern .img-holder").css("background-image").match(/url\(["']?([^()]*)["']?\)/).pop();
                    if (imageUrlFind) {
                        jQuery(".job-medium.modern .img-holder").addClass("image-loaded");
                    }
                }

                jQuery('.wp-dp-job-content').find('.selectize-select').selectize({
                    //allowEmptyOption: true,
                });
                jQuery('.wp-dp-job-content').find('.sort-records-per-page').selectize({
                    allowEmptyOption: true,
                });
                loader_con.html('');
                // add class when image loaded
            }
        });
    }
}

function jobsearch_job_content_load_without_filters(counter, page_var, page_num, ajax_filter, view_type) {
    "use strict";
    counter = counter || '';
    var job_arg = jQuery("#job_arg" + counter).html();
    var data_vals = page_var + '=' + page_num;
    jQuery('#jobsearch-data-job-content-' + counter + ' .all-results').addClass('slide-loader');
    if (typeof (jobFilterAjax) != 'undefined') {
        jobFilterAjax.abort();
    }
    jobFilterAjax = jQuery.ajax({
        type: 'POST',
        dataType: 'HTML',
        url: jobsearch_plugin_vars.ajax_url,
        data: data_vals + '&action=jobsearch_jobs_content&view_type=' + view_type + '&job_arg=' + job_arg,
        success: function (response) {
            jQuery('#Job-content-' + counter).html(response);
            // Replace double & from string.
            data_vals = data_vals.replace("&&", "&");
            var current_url = location.protocol + "//" + location.host + location.pathname + "?" + data_vals; //window.location.href;
            window.history.pushState(null, null, decodeURIComponent(current_url));
            jQuery('#jobsearch-data-job-content-' + counter + ' .all-results').removeClass('slide-loader');
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
        if (typeof comps[spl[0]] == "undefined" || (typeof comps[spl[0]] != "undefined" && comps[spl[0]] == '')) {
            comps[spl[0]] = spl[1];
        }
    }
    parts = [];
    for (var a in comps) {
        parts.push(a + "=" + comps[a]);
    }

    return parts.join('&');
}

function jobsearch_job_filters_content(counter, page_var, page_num, tab) {
    "use strict";
    counter = counter || '';
    var job_arg = jQuery("#job_arg" + counter).html();
    var this_frm = jQuery("#jobsearch_job_frm_" + counter);

    var ads_list_count = jQuery("#ads_list_count_" + counter).val();
    var ads_list_flag = jQuery("#ads_list_flag_" + counter).val();
    var list_flag_count = jQuery("#ads_list_flag_count_" + counter).val();
    var data_vals = 'tab=' + tab + '&' + page_var + '=' + page_num + '&ajax_filter=true';
    jQuery('#jobsearch-data-job-content-' + counter + ' .all-results').addClass('slide-loader');
    if (typeof (jobFilterAjax) != 'undefined') {
        jobFilterAjax.abort();
    }
    jobFilterAjax = jQuery.ajax({
        type: 'POST',
        dataType: 'HTML',
        url: jobsearch_plugin_vars.ajax_url,
        data: data_vals + '&action=jobsearch_jobs_filters_content&job_arg=' + job_arg,
        success: function (response) {
            console.log(response);
            jQuery('#job-tab-content-' + counter).html(response);
            jQuery("#job-tab-content-" + counter + ' .row').mixItUp({
                selectors: {
                    target: ".portfolio",
                }
            });
            //replace double & from string
            data_vals = data_vals.replace("&&", "&");
            var current_url = location.protocol + "//" + location.host + location.pathname + "?" + data_vals; //window.location.href;
            window.history.pushState(null, null, decodeURIComponent(current_url));
            jQuery(".chosen-select").chosen();
            jQuery('#jobsearch-data-job-content-' + counter + ' .all-results').removeClass('slide-loader');
            // add class when image loaded
            jQuery(".job-medium .img-holder img, .job-grid .img-holder img").one("load", function () {
                jQuery(this).parents(".img-holder").addClass("image-loaded");
            }).each(function () {
                if (this.complete)
                    $(this).load();
            });
            // add class when image loaded
        }
    });

}

function jobsearch_job_by_categories_filters_content(counter, page_var, page_num, tab) {
    "use strict";
    counter = counter || '';
    var job_arg = jQuery("#job_arg" + counter).html();
    var this_frm = jQuery("#jobsearch_job_frm_" + counter);

    var ads_list_count = jQuery("#ads_list_count_" + counter).val();
    var ads_list_flag = jQuery("#ads_list_flag_" + counter).val();
    var list_flag_count = jQuery("#ads_list_flag_count_" + counter).val();
    var data_vals = 'tab=' + tab + '&' + page_var + '=' + page_num + '&ajax_filter=true';
    jQuery('#jobsearch-data-job-content-' + counter + ' .all-results').addClass('slide-loader');
    if (typeof (jobFilterAjax) != 'undefined') {
        jobFilterAjax.abort();
    }
    jobFilterAjax = jQuery.ajax({
        type: 'POST',
        dataType: 'HTML',
        url: jobsearch_plugin_vars.ajax_url,
        data: data_vals + '&action=jobsearch_job_by_categories_filters_content&job_arg=' + job_arg,
        success: function (response) {
            jQuery('#job-tab-content-' + counter).html(response);
            jQuery("#job-tab-content-" + counter + ' .row').mixItUp({
                selectors: {
                    target: ".portfolio",
                }
            });
            //replace double & from string
            data_vals = data_vals.replace("&&", "&");
            var current_url = location.protocol + "//" + location.host + location.pathname + "?" + data_vals; //window.location.href;
            window.history.pushState(null, null, decodeURIComponent(current_url));
            jQuery(".chosen-select").chosen();
            jQuery('#jobsearch-data-job-content-' + counter + ' .all-results').removeClass('slide-loader');
            // add class when image loaded
            jQuery(".job-medium .img-holder img, .job-grid .img-holder img").one("load", function () {
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

function jobsearch_job_type_search_fields(thisObj, counter, price_switch, view_type) {
    var view_type = view_type || '';
    "use strict";
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: jobsearch_plugin_vars.ajax_url,
        data: '&action=jobsearch_job_type_search_fields&job_short_counter=' + counter + '&job_type_slug=' + thisObj.value + '&price_switch=' + price_switch + '&view_type=' + view_type,
        success: function (response) {
            jQuery('#job_type_fields_' + counter).html('');
            jQuery('#job_type_fields_' + counter).html(response.html);
        }
    });

    var checkID = thisObj.getAttribute('id');

    var cat_name = $('#' + checkID).next('label').html();

    $('.map-search-keyword-type-holder .dropdown-types-btn').html(cat_name);

    var dropdownHolder = $('.map-search-keyword-type-holder');
    var dropdownCon = dropdownHolder.find('ul.dropdown-types');
    dropdownCon.slideUp();
}

function jobsearch_job_type_cate_fields(thisObj, counter, cats_switch, view, color) {
    "use strict";
    if (typeof view === 'undefined') {
        view = 'default';
    }
    if (typeof color === 'undefined') {
        color = 'none';
    }
    var cate_loader = '<b class="spinner-label">' + jobsearch_job_functions_string.job_type + '</b><span class="cate-spinning"><i class="fancy-spinner"></i></span>';
    jQuery('#job_type_cate_fields_' + counter).html(cate_loader);
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: jobsearch_plugin_vars.ajax_url,
        data: '&action=jobsearch_job_type_cate_fields&job_short_counter=' + counter + '&job_type_slug=' + thisObj.value + '&view=' + view + '&color=' + color + '&cats_switch=' + cats_switch,
        success: function (response) {
            jQuery('#job_type_cate_fields_' + counter).html('');
            jQuery('#job_type_cate_fields_' + counter).html(response.html);
        }
    });
}

function jobsearch_job_type_split_map_search_fields(thisObj, counter) {

    var view_type = view_type || '';
    "use strict";
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: jobsearch_plugin_vars.ajax_url,
        data: '&action=jobsearch_job_type_split_map_search_fields&job_short_counter=' + counter + '&job_type_slug=' + thisObj.value,
        success: function (response) {
            jQuery('#job_type_fields_' + counter).html('');
            jQuery('#job_type_fields_' + counter).html(response.html);
        }
    }).done(function () {
        jobsearch_job_type_split_map_cate_fields(thisObj.value, counter);
    });

    var checkID = thisObj.getAttribute('id');
    var cat_name = $('#' + checkID).next('label').html();
    $('.map-search-keyword-type-holder .dropdown-types-btn').html(cat_name);

    var dropdownHolder = $('.map-search-keyword-type-holder');
    var dropdownCon = dropdownHolder.find('ul.dropdown-types');
    dropdownCon.slideUp();
}

function jobsearch_job_type_split_map_cate_fields(thisObj, counter, cats_switch, view, color) {
    "use strict";
    if (typeof view === 'undefined') {
        view = 'default';
    }
    if (typeof color === 'undefined') {
        color = 'none';
    }
    var cate_loader = '<b class="spinner-label">' + jobsearch_job_functions_string.job_type + '</b><span class="cate-spinning"><i class="fancy-spinner"></i></span>';
    jQuery('#job_type_cate_fields_' + counter).html(cate_loader);
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: jobsearch_plugin_vars.ajax_url,
        data: '&action=jobsearch_job_type_split_map_cate_fields&job_short_counter=' + counter + '&job_type_slug=' + thisObj.value + '&view=' + view + '&color=' + color,
        success: function (response) {
            jQuery('#job_type_cate_fields_' + counter).html('');
            jQuery('#job_type_cate_fields_' + counter).html(response.html);
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
        //jQuery('#job_type_fields_'+ counter ).toggle('slow');
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
        var data_vals = 'ajax_filter=true&map=top_map&action=jobsearch_top_map_search&' + jQuery(jQuery("#jobsearch_job_frm_" + counter)[0].elements).not(":input[name='alerts-email'], :input[name='alert-frequency']").serialize();
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
            jobsearch_job_split_map_content(counter, '', '', hide_overlay);
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
        for (var i = pars.length; i-- > 0;) {
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

function jobsearch_job_split_map_content(counter, view_type, animate_to, hide_overlay) {
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
            scrollTop: jQuery("#wp-dp-job-content-" + counter).offset().top - 120
        }, 700);
    }
    var job_arg = jQuery("#job_arg" + counter).html();
    var this_frm = jQuery("#jobsearch_job_frm_" + counter);


    var split_map = jQuery(".wp-dp-split-map-wrap").length;
    if (split_map > 0) {
        view_type = 'split_map';
    }

    var ads_list_count = jQuery("#ads_list_count_" + counter).val();
    var ads_list_flag = jQuery("#ads_list_flag_" + counter).val();
    var list_flag_count = jQuery("#ads_list_flag_count_" + counter).val();

    if (jQuery("#jobsearch_job_frm_" + counter).length > 0) {
        var data_vals = jQuery(jQuery("#jobsearch_job_frm_" + counter)[0].elements).not(":input[name='alerts-email'], :input[name='alert-frequency']").serialize();
        var data_vals = 'ajax_filter=true&map=top_map&action=jobsearch_top_map_search&' + jQuery(jQuery("#jobsearch_job_frm_" + counter)[0].elements).not(":input[name='alerts-email'], :input[name='alert-frequency']").serialize();
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

        jQuery('#Job-content-' + counter + ' .job').addClass('slide-loader');
        jQuery('#jobsearch-data-job-content-' + counter + ' .all-results').addClass('slide-loader');
        //if (typeof (jobFilterAjax) !== 'undefined') {
        //    jobFilterAjax.abort();
        //}

        var jobFilterAjax = jQuery.ajax({
            type: 'POST',
            dataType: 'HTML',
            url: jobsearch_plugin_vars.ajax_url,
            data: data_vals + '&action=jobsearch_jobs_content&view_type=' + view_type + '&job_arg=' + job_arg,
            success: function (response) {
                jQuery('body').removeClass('wp-dp-changing-view');
                jQuery('#Job-content-' + counter).html(response);

                if (hide_overlay === 'false' && hide_overlay !== '') {
                    jQuery('#wp-jobsearch-job-' + counter).hide();
                    jQuery('.no-job-match-error').hide();
                    jQuery('.page-nation').hide();
                }
                if (hide_overlay === 'true' && hide_overlay !== '') {
                    //jQuery(".main-search.split-map .field-holder.more-filters-btn").toggleClass('open');
                    jQuery("div.split-map-overlay").remove();
                    //jQuery('#job_type_fields_'+ counter ).toggle('slow');
                }

                // Replace double & from string.
                data_vals = data_vals.replace("&&", "&");
                var current_url = location.protocol + "//" + location.host + location.pathname + "?" + data_vals; //window.location.href;
                current_url = current_url.replace('&=undefined', ''); // remove extra and empty variables
                window.history.pushState(null, null, decodeURIComponent(current_url));
                jQuery(".chosen-select").chosen();
                jQuery('#jobsearch-data-job-content-' + counter + ' .all-results').removeClass('slide-loader');
                jobsearch_hide_loader();
                // add class when image loaded
                jQuery(".job-medium .img-holder img, .job-grid .img-holder img").one("load", function () {
                    jQuery(this).parents(".img-holder").addClass("image-loaded");
                }).each(function () {
                    if (this.complete)
                        jQuery(this).load();
                });
                if (jQuery(".job-medium.modern").length > 0) {
                    var imageUrlFind = jQuery(".job-medium.modern .img-holder").css("background-image").match(/url\(["']?([^()]*)["']?\)/).pop();
                    if (imageUrlFind) {
                        jQuery(".job-medium.modern .img-holder").addClass("image-loaded");
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
    if (jQuery("#jobsearch_job_frm_" + counter + " input[name=loc_polygon_path]").length) {
        jQuery("#jobsearch_job_frm_" + counter + " input[name=loc_polygon_path]").val('');
    }
}

function jobsearch_job_view_switch(view, counter, job_short_counter, view_type) {
    "use strict";
    var view_type = view_type || '';
    jQuery.ajax({
        type: 'POST',
        dataType: 'HTML',
        url: jobsearch_plugin_vars.ajax_url,
        data: '&action=jobsearch_job_view_switch&view=' + view + '&job_short_counter=' + job_short_counter,
        success: function () {
            jQuery('[data-toggle="popover"]').popover();
            jQuery('body').addClass('wp-dp-changing-view');
            jobsearch_job_content(counter, view_type);
        }
    });
}

function jobsearch_job_pagenation_ajax(page_var, page_num, counter, ajax_filter, view_type) {
    "use strict";
    var view_type = view_type || '';
    jQuery('html, body').animate({
        scrollTop: jQuery("#wp-dp-job-content-" + counter).offset().top - 120
    }, 1000);
    jQuery('#' + page_var + '-' + counter).val(page_num);
    jobsearch_job_content_load(counter, view_type);
    //alert("aaaaaaaa");
//    if (ajax_filter == 'false') {
//        if (view_type == "split_map") {
//            jobsearch_job_content_load(counter, view_type);
//        } else {
//            jobsearch_job_content_load_without_filters(counter, page_var, page_num, ajax_filter, view_type);
//        }
//    } else {
//        jobsearch_job_content_load(counter, view_type);
//    }
}

function jobsearch_job_filters_pagenation_ajax(page_var, page_num, counter, tab) {
    "use strict";
    jQuery('#' + page_var + '-' + counter).val(page_num);
    jobsearch_job_filters_content(counter, page_var, page_num, tab);
}

function jobsearch_job_by_category_filters_pagenation_ajax(page_var, page_num, counter, tab) {
    "use strict";
    jQuery('#' + page_var + '-' + counter).val(page_num);
    jobsearch_job_by_categories_filters_content(counter, page_var, page_num, tab);
}

function jobsearch_advanced_search_field(counter, tab, element) {
    "use strict";
    if (tab == 'simple') {
        jQuery('#job_type_fields_' + counter).slideUp();
        jQuery('#nav-tabs-' + counter + ' li').removeClass('active');
        jQuery(element).parent().addClass('active');
    } else if (tab == 'advance') {
        jQuery('#job_type_fields_' + counter).slideDown();
        jQuery('#nav-tabs-' + counter + ' li').removeClass('active');
        jQuery(element).parent().addClass('active');
    } else {
        jQuery('#job_type_fields_' + counter).slideToggle();

        if (jQuery(".main-search.split-map .field-holder.more-filters-btn").length > 0) {
            jQuery('.main-search.split-map .field-holder.more-filters-btn').toggleClass('open');
            if (jQuery('.main-search.split-map .field-holder.more-filters-btn').hasClass('open')) {
                var NewContent = '<div class="split-map-overlay"></div>';
                jQuery(".wp-dp-top-map-holder").after(NewContent);
                jQuery('#wp-jobsearch-job-' + counter).hide();
                jQuery('.page-nation').hide();
                jQuery('.no-job-match-error').hide();
            } else {
                jQuery("div.split-map-overlay").remove();
                jQuery('#wp-jobsearch-job-' + counter).show();
                jQuery('.page-nation').show();
                jQuery('.no-job-match-error').show();
            }
        }
    }
}

if (jQuery(".main-search.split-map .field-holder.more-filters-btn").length > 0) {
    function jobsearch_split_map_close_search(counter) {
        jQuery(".main-search.split-map .field-holder.more-filters-btn").toggleClass('open');
        jQuery("div.split-map-overlay").remove();
        jQuery('#job_type_fields_' + counter).toggle('slow');
        jQuery('#wp-jobsearch-job-' + counter).show();
        jQuery('.page-nation').show();
        jQuery('.no-job-match-error').show();
    }
}

function jobsearch_search_features(element, counter) {
    "use strict";
    jQuery('#job_type_fields_' + counter + ' .features-field-expand').slideToggle();
    var expand_class = jQuery('#job_type_fields_' + counter + ' .features-list .advance-trigger').find('i').attr('class');
    if (expand_class == 'icon-plus') {
        console.log(expand_class);
        jQuery('#job_type_fields_' + counter + ' .features-list .advance-trigger').find('i').removeClass(expand_class).addClass('icon-minus')
    } else {
        jQuery('#job_type_fields_' + counter + ' .features-list .advance-trigger').find('i').removeClass(expand_class).addClass('icon-plus')
    }
}


jQuery(document).on('click', '.dev-job-list-enquiry-own-job', function (e) {
    e.stopImmediatePropagation();
    var msg_obj = {msg: jobsearch_plugin_vars.own_job_error, type: 'error'};
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

//
if (jQuery('.jobsearch-advance-search-holdr').length > 0) {
    var top_srch_holder = jQuery('.jobsearch-advance-search-holdr');
    var srch_form_id = top_srch_holder.parent('form').attr('id');
    jQuery(document).on('submit', 'form#' + srch_form_id, function () {
        top_srch_holder.find('.adv-search-options').find('input, select').each(function () {
            var this_form_itm = $(this);
            if (typeof this_form_itm.attr('name') !== 'undefined' && typeof this_form_itm.attr('type') !== 'undefined' && (this_form_itm.attr('type') == 'checkbox' || this_form_itm.attr('type') == 'radio')) {
                if (this_form_itm.is(':checked')) {
                    jQuery('form#' + srch_form_id).append('<input type="hidden" name="' + this_form_itm.attr('name') + '" value="' + this_form_itm.val() + '">');
                }
            } else if (typeof this_form_itm.attr('name') !== 'undefined' && typeof this_form_itm.attr('type') === 'undefined') { // for select field
                jQuery('form#' + srch_form_id).append('<input type="hidden" name="' + this_form_itm.attr('name') + '" value="' + this_form_itm.val() + '">');
            }
        });
    });
}
jQuery(document).on('click', '.adv-srch-toggle-btn', function () {
    jQuery(this).parents('.jobsearch-advance-search-holdr').find('.adv-search-options').slideToggle();
    var slider_input_con = jQuery(this).parents('.jobsearch-advance-search-holdr').find('.adv-search-options').find('.filter-slider-range');
    var def_radius_val = slider_input_con.find('#loc-def-radiusval').val();
    slider_input_con.find('input[name=loc_radius]').val(def_radius_val);
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
 * Enquiry Reset all Multiple select
 */

jQuery(document).on('click', '.send-message-submit-btn', function () {
    var thisObj = jQuery(this);

    var rand_id = thisObj.data('randid');
    var action = thisObj.data('action');
    var form_id = jQuery("#jobsearch_send_message_form" + rand_id);
    var meessage_box = jQuery(".message-box-" + rand_id);
    var serializeForm = form_id.serialize();

    var get_terr_val = jobsearch_accept_terms_cond_pop(form_id);
    if (get_terr_val != 'yes') {
        return false;
    }
    thisObj.attr("disabled", true);

    meessage_box.html('<div class="split-map-loader"><span><i class="fa fa-spin fa fa-refresh"></i></span></div>');
    meessage_box.show();

    jQuery.ajax({
        type: 'POST',
        dataType: 'JSON',
        url: jobsearch_plugin_vars.ajax_url,
        data: 'action=' + action + '&' + serializeForm,
        success: function (response) {
            //
            meessage_box.css({'color': '#ffffff'});
            meessage_box.html(response.html);
            if (response.error == '1') {
                meessage_box.css({'background-color': '#ff0000'});
            } else {
                meessage_box.css({'background-color': '#32cd32'});
                form_id[0].reset();
            }
        }

    });
    thisObj.removeAttr("disabled");
    return false;
});

//***************************
// FilterAble Function
//***************************

jQuery(window).on('load', function () {

    if (jQuery('.careerfy-animated-filter-list,.careerfy-product-grid').length > 0) {

        var $grid = $('.careerfy-animated-filter-list,.careerfy-product-grid').isotope({
            itemSelector: '.element-item',
            layoutMode: 'fitRows'
        });
        // filter functions
        var filterFns = {
            // show if number is greater than 50
            numberGreaterThan50: function () {
                var number = $(this).find('.number').text();
                return parseInt(number, 10) > 50;
            },
            // show if name ends with -ium
            ium: function () {
                var name = $(this).find('.name').text();
                return name.match(/ium$/);
            }
        };
        // bind filter button click
        $('.filters-button-group,.filters-button-group-two').on('click', 'a', function () {
            var filterValue = $(this).attr('data-filter');
            console.info(filterValue);
            // use filterFn if matches value
            filterValue = filterFns[filterValue] || filterValue;
            $grid.isotope({filter: filterValue});
        });
        // change is-checked class on buttons
        $('.filters-button-group,.filters-button-group-two').each(function (i, buttonGroup) {
            var $buttonGroup = $(buttonGroup);
            $buttonGroup.on('click', 'a', function () {
                $buttonGroup.find('.is-checked').removeClass('is-checked');
                $(this).addClass('is-checked');
            });
        });
    }

});

jQuery(document).on('click', '.jobsearch-quick-apply-btn-close', function (e) {
    var _selector = jQuery(".jobsearch-quick-apply-box"), _selector_inner = jQuery('.jobsearch-quick-apply-box-inner');
    _selector.removeAttr('style').delay(100).hide(500);
    _selector_inner.remove()

});

jQuery(document).on('click', '.jobsearch-quick-apply-show', function () {

    var _selector = jQuery(".jobsearch-quick-apply-box"), _loader = jQuery("#jobsearch-loader"),
        _event_class = jQuery(".jobsearch-pst-title"), job_id = jQuery(this).attr('data-job-id');
    _event_class.removeClass('jobsearch-quick-apply-show');
    _selector.show();
    if (jobsearch_plugin_vars.is_rtl == true) {
        _selector.animate({
            left: '-15px'
        });
    } else {
        _selector.animate({
            right: "-15px"
        });
    }


    setTimeout(function () {
        _loader.html('<div class="careerfy-loading-section"><div class="line-scale-pulse-out"><div></div><div></div><div></div><div></div><div></div></div></div>');
    }, 300)

    var request = jQuery.ajax({
        url: jobsearch_plugin_vars.ajax_url,
        method: "POST",
        data: 'action=jobsearch_quick_job_popup&job_id=' + job_id,
        dataType: 'HTML',
    });

    request.done(function (response) {
        _loader.html('');
        _event_class.addClass('jobsearch-quick-apply-show');
        _selector.append(response);
    });
    request.fail(function (jqXHR, textStatus) {
        //msg_con.html(jobsearch_job_application.error_msg);
        //msg_loader.html('');
    });

});

jQuery(document).on('change', '.jobalert-crti-typebtn', function() {
    var this_val = jQuery('.jobalert-crti-typebtn:checked').val();
    var field_id = jQuery(this).parents('.jobalert-salrytype-filter').attr('data-id');
    //console.log(this_val);
    var pop_html_con = jQuery('#modpop-criteria-tags');
    pop_html_con.append('<span class="tags-loder"><i class="fa fa-refresh fa-spin"></i></span>');
    var appender = jQuery('.salarytypes-rangelist-con');
    var request = jQuery.ajax({
        url: jobsearch_plugin_vars.ajax_url,
        method: "POST",
        data: 'action=jobsearch_joblert_pop_salry_html&type_id=' + this_val + '&fid=' + field_id,
        dataType: 'json',
    });
    request.done(function (response) {
        if (undefined !== typeof response.html && response.html != '') {
            appender.html(response.html);
            jQuery('.salary-load-selectize').selectize({
                allowEmptyOption: true,
            });
        }
        pop_html_con.find('.tags-loder').remove();
    });
    request.complete(function () {
        pop_html_con.find('.tags-loder').remove();
    });
});