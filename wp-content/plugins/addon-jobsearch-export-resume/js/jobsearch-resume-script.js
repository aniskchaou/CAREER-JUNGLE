jQuery(document).on('click', '.jobsearch-activate-pdf-template', function () {
    var _this = jQuery(this), _template = _this.attr('data-template'), _loader_html;
    _loader_html = '<div class="jobsearch-candidate-pdf-locked pdf-loader"><a href="javascript:void(0)" class="fa fa-refresh fa-spin"></a></div>';
    _this.after(_loader_html);
    jQuery(".jobsearch-candidate-pdf-list").find("figcaption").remove();
    //
    var request = jQuery.ajax({
        url: jobsearch_plugin_vars.ajax_url,
        method: "POST",
        data: {
            template_name: _template,
            action: 'jobsearch_user_pdf_type_save',
        },
        dataType: "json"
    });
    request.done(function (response) {
        if (typeof response.res !== 'undefined' && response.res == true) {
            _this.after('<figcaption>' + jobsearch_export_vars.active + '</figcaption>');
            jQuery(document).find(".pdf-loader").remove();
        }
    });
    request.fail(function (jqXHR, textStatus) {
        console.info(textStatus);
    });
});

jQuery(document).on('click', '.jobsearch-subscribe-pdf-pkg', function () {

    var _this = jQuery(this),
        this_id = _this.attr('data-id'),
        this_loader = jQuery(this).next('.pkg-loding-msg');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.show();
    var request = jQuery.ajax({
        url: jobsearch_plugin_vars.ajax_url,
        method: "POST",
        data: {
            pkg_id: this_id,
            action: 'jobsearch_user_pdf_pckg_subscribe',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);
        }
        if (typeof response.redirect_url !== 'undefined' && response.redirect_url != '') {
            window.location.replace(response.redirect_url);
            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_packages_vars.error_msg);
    });
});
