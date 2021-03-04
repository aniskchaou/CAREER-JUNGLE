var $ = jQuery;

jQuery(document).on('click', '.jobsearch-add-job-to-favourite', function () {
    var _this = jQuery(this);
    console.info(_this);
    var this_id = _this.attr('data-id');
    var this_view = _this.attr('data-view');
    var after_label = _this.attr('data-after-label');
    var before_icon = _this.attr('data-before-icon');
    var after_icon = _this.attr('data-after-icon');
    var this_loader = _this.find('i');
    var msg_con = _this.parent('div').find('.job-to-fav-msg-con');

    this_loader.attr('class', 'fa fa-refresh fa-spin');

    var shortlist_view = 'job';
    if (typeof this_view !== 'undefined' && this_view !== '') {
        shortlist_view = this_view;
    }
    var request = jQuery.ajax({
        url: jobsearch_plugin_vars.ajax_url,
        method: "POST",
        data: {
            job_id: this_id,
            action: 'jobsearch_add_candidate_job_to_favourite',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.error !== 'undefined' && response.error == '1') {
            msg_con.html(response.msg);
            this_loader.attr('class', before_icon);
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '' && shortlist_view == 'job') {
            this_loader.attr('class', after_icon);
            _this.removeClass('jobsearch-add-job-to-favourite');
        }
        if (typeof response.msg !== 'undefined' && response.msg != '' && shortlist_view == 'job2') {
            var htm = after_label;
            _this.empty();
            _this.html(htm);
            _this.removeClass('jobsearch-add-job-to-favourite');
        }

        if (typeof response.msg !== 'undefined' && response.msg != '' && shortlist_view == 'job3') {
            //this_loader.attr('class', after_icon);
            var htm = '<i class=" '+after_icon+' "></i> '+after_label+' ';
            _this.empty();
            _this.html(htm);
            _this.removeClass('jobsearch-add-job-to-favourite');
        }
        if (typeof response.msg !== 'undefined' && response.msg != '' && shortlist_view == 'style9') {
            //this_loader.attr('class', after_icon);
            var htm = '<i class=" '+after_icon+' "></i> '+after_label+' ';
            _this.empty();
            _this.html(htm);
            _this.removeClass('jobsearch-add-job-to-favourite');
        }

    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.attr('class', before_icon);
    });
});
