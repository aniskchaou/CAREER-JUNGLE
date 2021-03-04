jQuery(document).on('click', '.jobsearch_ad_img_banner_click', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    if (this_id > 0) {
        var request = jQuery.ajax({
            url: jobsearch_ads_manage_vars.ajax_url,
            method: "POST",
            data: {
                'code_id': this_id,
                'action': 'jobsearch_ad_banner_click_counts',
            },
            dataType: "json"
        });

        request.done(function (response) {
        });

        request.fail(function (jqXHR, textStatus) {
        });
    }
});