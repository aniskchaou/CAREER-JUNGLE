/* global confirm, redux, redux_change */

jQuery(document).on("click", ".jobsearch-uplopumflit-media", function () {
    var id = $(this).attr("name");
    var custom_uploader = wp.media({
        title: 'Select File',
        button: {
            text: 'Add File'
        },
        multiple: false
    })
    .on('select', function () {
        var attachment = custom_uploader.state().get('selection').first().toJSON();
        $('#' + id).val(attachment.url);
        $('#' + id + '-img').attr('src', attachment.url);
        $('#' + id + '-box').show();
    }).open();
});
jQuery(document).on("click", ".jobsearch-rem-media-b", function () {
    var id = $(this).data('id');
    $('#' + id).val('');
    $('#' + id + '-img').attr('src', '');
    $('#' + id + '-box').hide();
});

jQuery('#pumflit-type').on('change', function () {
    var _this = jQuery(this);
    var img_con = jQuery('#pumflit-banner-img-field');
    var mg_url_con = jQuery('#pumflit-banner-img-url-field');
    var url_target_con = jQuery('#pumflit-banner-url-target-field');
    var adsense_con = jQuery('#pumflit-banner-adsense-field');
    if (_this.val() == 'adsense') {
        img_con.hide();
        mg_url_con.hide();
        url_target_con.hide();
        adsense_con.show();
    } else {
        img_con.show();
        mg_url_con.show();
        url_target_con.show();
        adsense_con.hide();
    }
});

jQuery(document).on('change', '.jobsearch-pumflit-type-select', function () {
    var _this = jQuery(this);
    var _this_id = _this.attr('data-id');

    var img_con = jQuery('#pumflit-banner-img-field-' + _this_id);
    var mg_url_con = jQuery('#pumflit-banner-img-url-field-' + _this_id);
    var url_target_con = jQuery('#pumflit-banner-url-target-field-' + _this_id);
    var adsense_con = jQuery('#pumflit-banner-adsense-field-' + _this_id);
    if (_this.val() == 'adsense') {
        img_con.hide();
        mg_url_con.hide();
        url_target_con.hide();
        adsense_con.show();
    } else {
        img_con.show();
        mg_url_con.show();
        url_target_con.show();
        adsense_con.hide();
    }
});

jQuery(document).on('click', '.update-ad', function () {
    jQuery(this).parents('.pumflit-item').find('.action-update-con').slideToggle();
});

jQuery(document).on('click', '.update-group', function () {
    jQuery(this).parents('.group-item').find('.action-update-con').slideToggle();
});

jQuery(document).on('click', '.update-the-list', function () {
    jQuery(this).parents('.group-item').find('.action-update-con').slideToggle();
});

jQuery(document).on('click', '.update-the-banner', function () {
    jQuery(this).parents('.pumflit-item').find('.action-update-con').slideToggle();
});

jQuery(document).on('click', '.remove-group', function () {
    var conf = confirm(jobsearch_plugin_vars.are_you_sure);
    if (conf) {
        jQuery(this).parents('.group-item').remove();
    }
});

jQuery(document).on('click', '.remove-ad', function () {
    var conf = confirm(jobsearch_plugin_vars.are_you_sure);
    if (conf) {
        jQuery(this).parents('.pumflit-item').remove();
    }
});

jQuery(document).on('click', '.add-banner-to-list', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var _this_id = _this.attr('data-id');
    var this_cont = _this.parents('.pumflit-banner-fields-sec');

    var html_con = jQuery('.pumflit-banners-list-sec');

    var this_loader = this_cont.find('.loader-box');
    var this_msg_con = this_cont.find('.message-box');

    var banner_title = this_cont.find('input[id="pumflit-title"]');
    var banner_type = this_cont.find('select[id="pumflit-type"]');
    var banner_style = this_cont.find('select[id="pumflit-style"]');
    var banner_img = this_cont.find('input[id="pumflit-image"]');
    var banner_img_url = this_cont.find('input[id="pumflit-img-url"]');
    var banner_url_target = this_cont.find('select[id="pumflit-target"]');
    var banner_adsense_code = this_cont.find('textarea[id="pumflit-adsense-code"]');

    var error = 0;
    if (banner_title.val() == '') {
        error = 1;
        banner_title.css({"border": "1px solid #ff0000"});
    } else {
        banner_title.css({"border": "1px solid #d3dade"});
    }

    if (banner_type.val() == 'adsense') {
        if (banner_adsense_code.val() == '') {
            error = 1;
            banner_adsense_code.css({"border": "1px solid #ff0000"});
        } else {
            banner_adsense_code.css({"border": "1px solid #d3dade"});
        }
    } else {
        if (banner_img.val() == '') {
            error = 1;
            var banner_brwse_btn = jQuery('input[name="pumflit-image"]');
            banner_brwse_btn.css({"border": "1px solid #ff0000"});
        } else {
            var banner_brwse_btn = jQuery('input[name="pumflit-image"]');
            banner_brwse_btn.css({"border": "1px solid #d3dade"});
        }
        if (banner_img_url.val() == '') {
            error = 1;
            banner_img_url.css({"border": "1px solid #ff0000"});
        } else {
            banner_img_url.css({"border": "1px solid #d3dade"});
        }
    }

    if (error == 0) {

        this_msg_con.hide();
        this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: jobsearch_plugin_vars.ajax_url,
            method: "POST",
            data: {
                option_name: _this_id,
                banner_title: banner_title.val(),
                banner_type: banner_type.val(),
                banner_style: banner_style.val(),
                banner_img: banner_img.val(),
                banner_img_url: banner_img_url.val(),
                banner_url_target: banner_url_target.val(),
                banner_adsense_code: banner_adsense_code.val(),
                action: 'jobsearch_add_banner_ad_to_list',
            },
            dataType: "json"
        });

        request.done(function (response) {
            var msg_before = '';
            var msg_after = '';
            if (typeof response.error !== 'undefined') {
                if (response.error == '1') {
                    msg_before = '<div class="alert alert-danger"><i class="fa fa-times"></i> ';
                    msg_after = '</div>';
                } else if (response.error == '0') {
                    msg_before = '<div class="alert alert-success"><i class="fa fa-check"></i> ';
                    msg_after = '</div>';
                }
            }
            if (typeof response.msg !== 'undefined') {
                this_msg_con.html(msg_before + response.msg + msg_after);
                this_msg_con.slideDown();
                if (typeof response.error !== 'undefined' && response.error == '0') {
                    html_con.append(response.html);
                    banner_title.val('');
                    banner_img_url.val('');
                    banner_adsense_code.val('');
                    banner_img.val('');
                    jQuery('#pumflit-image-box').hide();
                }
            } else {
                this_msg_con.html(jobsearch_plugin_vars.error_msg);
            }
            this_loader.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            this_loader.html(jobsearch_plugin_vars.error_msg);
        });
    }
});

jQuery(document).on('click', '.add-pumflit-group-to-list', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var this_cont = _this.parents('.pumflit-banner-fields-sec');

    var html_con = jQuery('.pumflit-groups-list-sec');

    var this_loader = this_cont.find('.loader-box');
    var this_msg_con = this_cont.find('.message-box');

    var group_title = this_cont.find('input[id="group-title"]');
    var group_sort = this_cont.find('select[id="group-sort"]');
    var group_visible_ads = this_cont.find('select[id="group-vis-ads"]');

    var error = 0;
    if (group_title.val() == '') {
        error = 1;
        group_title.css({"border": "1px solid #ff0000"});
    } else {
        group_title.css({"border": "1px solid #d3dade"});
    }

    if (error == 0) {

        this_msg_con.hide();
        this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: jobsearch_plugin_vars.ajax_url,
            method: "POST",
            data: {
                group_title: group_title.val(),
                group_sort: group_sort.val(),
                group_visible_ads: group_visible_ads.val(),
                action: 'jobsearch_add_ad_group_to_list',
            },
            dataType: "json"
        });

        request.done(function (response) {
            var msg_before = '';
            var msg_after = '';
            if (typeof response.error !== 'undefined') {
                if (response.error == '1') {
                    msg_before = '<div class="alert alert-danger"><i class="fa fa-times"></i> ';
                    msg_after = '</div>';
                } else if (response.error == '0') {
                    msg_before = '<div class="alert alert-success"><i class="fa fa-check"></i> ';
                    msg_after = '</div>';
                }
            }
            if (typeof response.msg !== 'undefined') {
                this_msg_con.html(msg_before + response.msg + msg_after);
                this_msg_con.slideDown();
                if (typeof response.error !== 'undefined' && response.error == '0') {
                    html_con.append(response.html);
                    group_title.val('');
                }
            } else {
                this_msg_con.html(jobsearch_plugin_vars.error_msg);
            }
            this_loader.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            this_loader.html(jobsearch_plugin_vars.error_msg);
        });
    }
});
