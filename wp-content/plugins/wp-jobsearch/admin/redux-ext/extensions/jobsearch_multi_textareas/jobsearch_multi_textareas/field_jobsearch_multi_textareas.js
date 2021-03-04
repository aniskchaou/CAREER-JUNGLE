jQuery(document).on('click', '#multxtarefields-addmore', function () {

    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_loder = _this.parent('div').find('.msocilinks-loder');
    var appendr_con = jQuery('.jobsearch-textareamultifields-holdr');
    this_loder.html('<i class="fa fa-refresh fa-spin"></i>');
    var request = jQuery.ajax({
        url: jobsearch_multi_textareas_vars.ajax_url,
        method: "POST",
        data: {
            'field_id': this_id,
            'action': 'jobsearch_poptions_addmore_txtarea_field_cjax'
        },
        dataType: "html"
    });
    request.done(function (response) {
        if (response != '') {
            appendr_con.append(response);
            //init quicktags
            quicktags({id : 'canedmsgs-desc-admore'});
            //init tinymce
            tinymce.init(tinyMCEPreInit.mceInit['canedmsgs-desc-admore']);
        }
        this_loder.html('');
    });

    request.fail(function (jqXHR, textStatus) {
        this_loder.html('');
    });
});

jQuery(document).on('click', '.remv-upldfiled-item', function () {
    jQuery(this).parents('.textareafield-item').remove();
});