jQuery(document).on('click', '#upldmultifields-addmore', function () {

    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_loder = _this.parent('div').find('.msocilinks-loder');
    var appendr_con = jQuery('.jobsearch-socialmultifields-holdr');
    this_loder.html('<i class="fa fa-refresh fa-spin"></i>');
    var request = jQuery.ajax({
        url: jobsearch_multi_socialfileds_vars.ajax_url,
        method: "POST",
        data: {
            'field_id': this_id,
            'action': 'jobsearch_poptions_addmore_social_field_cjax'
        },
        dataType: "json"
    });
    request.done(function (response) {
        if (typeof response.success !== 'undefined' && response.success == '1') {
            appendr_con.append(response.html);
        }
        this_loder.html('');
    });

    request.fail(function (jqXHR, textStatus) {
        this_loder.html('');
    });
});

jQuery(document).on('click', '.upldmultifields-show-icon', function () {

    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var field_name = _this.attr('data-name');
    var field_icon = _this.attr('data-icon');
    var icon_group = _this.attr('data-icong');
    //
    var this_loder = _this.parent('div').find('.showicn-loder');
    var appendr_con = _this.parent('div');
    this_loder.html('<i class="fa fa-refresh fa-spin"></i>');
    var request = jQuery.ajax({
        url: jobsearch_multi_socialfileds_vars.ajax_url,
        method: "POST",
        data: {
            'random_id': this_id,
            'field_name': field_name,
            'field_icon': field_icon,
            'icon_group': icon_group,
            'action': 'jobsearch_poptions_social_field_show_icon_cjax'
        },
        dataType: "json"
    });
    request.done(function (response) {
        if (typeof response.success !== 'undefined' && response.success == '1') {
            appendr_con.html(response.html);
        }
        this_loder.html('');
    });

    request.fail(function (jqXHR, textStatus) {
        this_loder.html('');
    });
});

jQuery(document).on('click', '.remv-upldfiled-item', function () {
    jQuery(this).parents('.upldfield-item').remove();
});