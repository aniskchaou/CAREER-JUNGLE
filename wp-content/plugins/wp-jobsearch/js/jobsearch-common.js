var $ = jQuery;

var jobsearch_common_getJSON = function (url, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.responseType = 'json';
    xhr.onload = function () {
        var status = xhr.status;
        if (status === 200) {
            callback(null, xhr.response);
        } else {
            callback(status, xhr.response);
        }
    };
    xhr.send();
};

jQuery(document).on('click', '.load_users_field', function (e) {
    e.preventDefault();
    var this_id = jQuery(this).data('randid'),
            loaded = jQuery(this).data('loaded'),
            custom_field = jQuery('#load_users_field_' + this_id),
            ajax_url = jobsearch_plugin_vars.ajax_url,
            force_std = jQuery(this).data('forcestd');
    if (loaded != true) {
        jQuery('.load_users_loader_' + this_id).html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: ajax_url,
            method: "POST",
            data: {
                force_std: force_std,
                action: 'jobsearch_load_all_users_list_opts',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if ('undefined' !== typeof response.html) {
                custom_field.html(response.html);
                jQuery('.load_users_loader_' + this_id).html('');
                custom_field.data('loaded', true);
            }
        });

        request.fail(function (jqXHR, textStatus) {
        });
    }
    return false;

});

function jobsearch_multicap_all_functions() {
    var all_elements = jQuery(".g-recaptcha");
    for (var i = 0; i < all_elements.length; i++) {
        var id = all_elements[i].getAttribute('id');
        var site_key = all_elements[i].getAttribute('data-sitekey');
        if (null != id) {
            grecaptcha.render(id, {
                'sitekey': site_key
            });
        }
    }
}

function jobsearch_captcha_reload(admin_url, captcha_id) {
    "use strict";
    var dataString = '&action=jobsearch_captcha_reload&captcha_id=' + captcha_id;
    jQuery.ajax({
        type: "POST",
        url: admin_url,
        data: dataString,
        dataType: 'html',
        success: function (data) {
            jQuery("#" + captcha_id + "_div").html(data);
//            jQuery('.g-recaptcha').each(function () {
//                jQuery(this).find('iframe:first')
//                        .removeAttr('width')
//                        .addClass('img-responsive')
//                        .parent().parent()
//                        .css({'width': 'auto'});
//            });
        }
    });
}

window.djangoReCaptcha = {
    list: [],
    setup: function () {
        $('.g-recaptcha').each(function () {
            var $container = $(this);
            var config = $container.data();

            alert($container.attr('class'));

            djangoReCaptcha.init($container, config);
        });

        $(window).on('resize orientationchange', function () {
            $(djangoReCaptcha.list).each(function (idx, el) {
                djangoReCaptcha.resize.apply(null, el);
            });
        });
    },
    init: function ($container, config) {
        grecaptcha.render($container.get(0), config);
        alert(3434);
        var captchaSize, scaleFactor;
        var $iframe = $container.find('iframe').eq(0);

        $iframe.on('load', function () {
            $container.addClass('g-recaptcha-initted');
            captchaSize = captchaSize || {w: $iframe.width() - 2, h: $iframe.height()};
            djangoReCaptcha.resize($container, captchaSize);
            djangoReCaptcha.list.push([$container, captchaSize]);
        });
    },
};

window.djangoReCaptchaSetup = window.djangoReCaptcha.setup;

jQuery(document).on('click', '.load-more-team', function () {

    var _this = jQuery(this),
            total_pages = _this.attr('data-pages'),
            cur_page = _this.attr('data-page'),
            this_rand = _this.attr('data-rand'),
            this_view = _this.attr('data-view'),
            employer_id = _this.attr('data-id'),
            class_pref = _this.attr('data-pref'),
            ajax_url = jobsearch_plugin_vars.ajax_url;

    var team_view = 'default';
    if ('undefined' !== typeof this_view && this_view != '') {
        team_view = this_view;
    }



    var members_holder = jQuery('#members-holder-' + this_rand);
    var this_html = _this.html();

    if (!_this.hasClass('jobsearch-loading')) {
        _this.addClass('jobsearch-loading');
        _this.html('<i class="fa fa-refresh fa-spin"></i> ' + jobsearch_plugin_vars.loading);
        var request = jQuery.ajax({
            url: ajax_url,
            method: "POST",
            data: {
                total_pages: total_pages,
                cur_page: cur_page,
                employer_id: employer_id,
                class_pref: class_pref,
                team_style: team_view,
                action: 'jobsearch_load_employer_team_next_page',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if ('undefined' !== typeof response.html && response.html != '') {
                members_holder.append(response.html);
                members_holder.find('.new-entries').slideDown().removeClass('new-entries');
                var current_page = parseInt(cur_page) + 1;
                _this.attr('data-page', current_page);
                if (current_page == total_pages) {
                    _this.hide();
                }
            }
            _this.html(this_html);
            _this.removeClass('jobsearch-loading');
        });

        request.fail(function (jqXHR, textStatus) {
            _this.html(this_html);
            _this.removeClass('jobsearch-loading');
        });
    }
    return false;

});

jQuery(document).on('click', ".jobsearch-click-btn", function () {
    var t_tihs = jQuery(this);
    var filtr_cname = t_tihs.attr('data-cname');
    var filtr_cval = t_tihs.attr('data-cval');
    if (filtr_cval == 'close') {
        filtr_cval = 'open';
    } else {
        filtr_cval = 'close';
    }


    t_tihs.parents('.jobsearch-search-filter-toggle').find('.jobsearch-checkbox-toggle').slideToggle("slow", function () {
        var c_date = new Date();
        c_date.setTime(c_date.getTime() + (60 * 60 * 1000));
        var c_expires = "; c_expires=" + c_date.toGMTString();
        console.info(filtr_cname + "=" + filtr_cval + c_expires + "; path=/");
        document.cookie = filtr_cname + "=" + filtr_cval + c_expires + "; path=/";
        //console.info(document.cookie);
    });

    t_tihs.parents('.jobsearch-search-filter-toggle').toggleClass("jobsearch-remove-padding");
    return false;
});

if (jQuery('.jobsearch-mobile-btn').length > 0) {
    jQuery(document).on('click', '.jobsearch-mobile-btn', function () {
        jQuery('.jobsearch-mobile-section').slideToggle(1000);
        jQuery(this).toggleClass("open");
    });
}

jQuery(document).on('change', '.jobsearch-depndfield-srchange', function() {
    var _this = jQuery(this);
    var field_parent = _this.parents('.jobsearch-depndetfield-con');
    var field_mid = field_parent.attr('data-mid');
    var field_type = field_parent.attr('data-ftype');
    var field_place = field_parent.attr('data-plc');
    var field_thid = field_parent.attr('data-thid');
    var selectize_control = field_parent.find('.selectize-control');
    var selectize_drpdown = selectize_control.find('.selectize-dropdown-content');
    var slectd_optionval = _this.val();
    var selctd_selctize_val = selectize_drpdown.find('div[data-value="' + slectd_optionval + '"]');
    var has_depnd = selctd_selctize_val.attr('data-depend');
    var opt_id = selctd_selctize_val.attr('data-optid');
    var all_nextdep_fields = field_parent.nextAll('.jobsearch-deparent-field.deparent-' + field_thid);
    if (all_nextdep_fields.length > 0) {
        all_nextdep_fields.each(function() {
            jQuery(this).remove();
        });
    }
    if (has_depnd == 'true') {
        field_parent.addClass('disabled-field');
        field_parent.append('<span class="jobsearch-depfield-loder"><i class="fa fa-refresh fa-spin"></i></span>');
        var loc_counts_request = jQuery.ajax({
            url: jobsearch_plugin_vars.ajax_url,
            method: "POST",
            data: {
                opt_id: opt_id,
                field_mid: field_mid,
                field_type: field_type,
                field_plc: field_place,
                action: 'jobsearch_get_depend_fields_infront',
            },
            dataType: "json"
        });
        loc_counts_request.done(function (response) {
            if (typeof response.html !== undefined) {
                field_parent.after(response.html);
            }
        });
        loc_counts_request.complete(function () {
            field_parent.removeClass('disabled-field');
            field_parent.find('.jobsearch-depfield-loder').remove();
        });
    }
});

jQuery(document).on('change', '.jobsearch-depndfield-mulchange', function() {
    var _this = jQuery(this);
    var field_parent = _this.parents('.jobsearch-depndetfield-con');
    var field_mid = field_parent.attr('data-mid');
    var field_type = field_parent.attr('data-ftype');
    var field_place = field_parent.attr('data-plc');
    var field_thid = field_parent.attr('data-thid');
    var selectize_drpdown = field_parent.find('.hiden-multiselc-opts');
    
    var slectd_optionval = _this.val();
    
    var opt_ids = '';
    if (slectd_optionval.length > 0) {
        var opt_ids_arr = [];
        jQuery(slectd_optionval).each(function(index, _this_val) {
            var selctize_val_div = selectize_drpdown.find('div[data-value="' + _this_val + '"]');
            if (selctize_val_div.attr('data-depend') == 'true') {
                opt_ids_arr.push(selctize_val_div.attr('data-optid'));
            }
        });
        if (opt_ids_arr.length > 0) {
            opt_ids = opt_ids_arr.join(',');
        }
    }
    
    //
    var all_nextdep_fields = field_parent.nextAll('.jobsearch-deparent-field.deparent-' + field_thid);
    if (all_nextdep_fields.length > 0) {
        all_nextdep_fields.each(function() {
            jQuery(this).remove();
        });
    }
    
    if (opt_ids != '') {
        field_parent.addClass('disabled-field');
        field_parent.append('<span class="jobsearch-depfield-loder"><i class="fa fa-refresh fa-spin"></i></span>');
        var loc_counts_request = jQuery.ajax({
            url: jobsearch_plugin_vars.ajax_url,
            method: "POST",
            data: {
                opt_id: opt_ids,
                field_mid: field_mid,
                field_type: field_type,
                field_plc: field_place,
                action: 'jobsearch_get_depend_fields_infront',
            },
            dataType: "json"
        });
        loc_counts_request.done(function (response) {
            if (typeof response.html !== undefined) {
                field_parent.after(response.html);
            }
        });
        loc_counts_request.complete(function () {
            field_parent.removeClass('disabled-field');
            field_parent.find('.jobsearch-depfield-loder').remove();
        });
    }
});

jQuery(document).on('change', '.jobsearch-depndfield-rchchange', function() {
    var _this = jQuery(this);
    var field_parent = _this.parents('.jobsearch-depndetfield-con');
    var field_mid = field_parent.attr('data-mid');
    var field_type = field_parent.attr('data-ftype');
    var field_place = field_parent.attr('data-plc');
    var field_thid = field_parent.attr('data-thid');
    
    var opt_ids = '';
    var opt_ids_arr = [];
    field_parent.find('.jobsearch-depndfield-rchchange').each(function() {
        if (jQuery(this).is(':checked') && jQuery(this).attr('data-depend') == 'true') {
            opt_ids_arr.push(jQuery(this).attr('data-optid'));
        }
    });
    if (opt_ids_arr.length > 0) {
        opt_ids = opt_ids_arr.join(',');
    }
    
    //
    var all_nextdep_fields = field_parent.nextAll('.jobsearch-deparent-field.deparent-' + field_thid);
    if (all_nextdep_fields.length > 0) {
        all_nextdep_fields.each(function() {
            jQuery(this).remove();
        });
    }
    
    if (opt_ids != '') {
        field_parent.addClass('disabled-field');
        field_parent.append('<span class="jobsearch-depfield-loder"><i class="fa fa-refresh fa-spin"></i></span>');
        var loc_counts_request = jQuery.ajax({
            url: jobsearch_plugin_vars.ajax_url,
            method: "POST",
            data: {
                opt_id: opt_ids,
                field_mid: field_mid,
                field_type: field_type,
                field_plc: field_place,
                action: 'jobsearch_get_depend_fields_infront',
            },
            dataType: "json"
        });
        loc_counts_request.done(function (response) {
            if (typeof response.html !== undefined) {
                field_parent.after(response.html);
            }
        });
        loc_counts_request.complete(function () {
            field_parent.removeClass('disabled-field');
            field_parent.find('.jobsearch-depfield-loder').remove();
        });
    }
});