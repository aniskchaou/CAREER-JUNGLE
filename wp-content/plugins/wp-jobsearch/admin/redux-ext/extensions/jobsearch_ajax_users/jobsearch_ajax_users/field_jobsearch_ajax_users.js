jQuery(document).on('click', '.jobsearch-ajax-users select', function () {

    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var main_con = jQuery('#ajax-users-load-sec-' + this_id);
    var this_loader = main_con.find('.ajax-loader');

    var sel_value = main_con.attr('data-value');
    var users_role = main_con.attr('data-role');

    if (!main_con.hasClass('list-added')) {
        this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: jobsearch_plugin_vars.ajax_url,
            method: "POST",
            data: {
                sel_value: sel_value,
                users_role: users_role,
                action: 'jobsearch_get_ajax_users_list',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.list !== 'undefined') {
                _this.append(response.list);
                var click_intervl = setInterval(function () {
                    _this.hide().show().trigger('click');
                    clearInterval(click_intervl);
                }, 1000);
                main_con.addClass('list-added');
            }
            this_loader.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            this_loader.html('');
        });
    }
});
