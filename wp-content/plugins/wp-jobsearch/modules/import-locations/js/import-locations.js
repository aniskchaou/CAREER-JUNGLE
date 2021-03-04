jQuery(document).on('click', '.import-locscontry-data-btn', function () {

    var loader_con = jQuery(this).parents('.load-all-contries').find('.loader-con');
    var msg_con = jQuery(this).parents('.load-all-contries').find('.msge-con');
    var brkdown_con = jQuery(this).parents('.load-all-contries').find('.all-breakdown-con');

    msg_con.html('');
    brkdown_con.html('');
    loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
    var sel_country = jQuery(this).parents('.load-all-contries').find('select#load-select-countries').val();
    var request = $.ajax({
        url: jobsearch_importlocs_vars.ajax_url,
        method: "POST",
        data: {
            'sel_country': sel_country,
            'action': 'jobsearch_import_locations_in_bulk'
        },
        dataType: "json"
    });
    request.done(function (response) {
        if (typeof response.country_id !== 'undefined' && typeof response.state_id !== 'undefined') {
            brkdown_con.append('<div class="state-item-import"><span class="state-name-con">' + (response.state_name) + '</span> <span id="state-import-' + (response.state_id) + '" class="state-import-msg">success</span></div>');
        }
        msg_con.html(response.msg);
        loader_con.html('');
    });

    request.fail(function (jqXHR, textStatus) {
        loader_con.html('');
        msg_con.html(jobsearch_importlocs_vars.error_msg);
    });
});

function jobseacrh_import_state_cities(country_id, state_id, state_name) {
    var msge_con = jQuery('.load-all-contries').find('.msge-con');
    var brkdown_con = jQuery('.load-all-contries').find('.all-breakdown-con');
    brkdown_con.append('<div class="state-item-import"><span class="state-name-con">' + (state_name) + '</span> <span id="state-import-' + (state_id) + '" class="state-import-msg">success</span></div>');
    
    var loader_con = jQuery('#state-import-' + state_id);
    loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
    var request = $.ajax({
        url: jobsearch_importlocs_vars.ajax_url,
        method: "POST",
        data: {
            'country_id': country_id,
            'state_id': state_id,
            'action': 'jobsearch_import_locations_states_cities'
        },
        dataType: "json"
    });
    request.done(function (response) {
        if (typeof response.msg !== 'undefined') {
            msge_con.append(response.msg);
        }
        loader_con.html('success');
    });

    request.fail(function (jqXHR, textStatus) {
        loader_con.html('fail');
    });
}

jQuery(document).on('click', '.import-locsall-data-btn', function () {

    var loader_con = jQuery(this).parents('.load-all-contries').find('.loader-con');
    var msg_con = jQuery(this).parents('.load-all-contries').find('.msge-con');
    var brkdown_con = jQuery(this).parents('.load-all-contries').find('.all-breakdown-con');

    msg_con.html('');
    brkdown_con.html('');
    loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
    var sel_country = 'all';
    var request = $.ajax({
        url: jobsearch_importlocs_vars.ajax_url,
        method: "POST",
        data: {
            'sel_country': sel_country,
            'action': 'jobsearch_import_locations_in_bulk'
        },
        dataType: "json"
    });
    request.done(function (response) {
        if (typeof response.country_id !== 'undefined' && typeof response.country_name !== 'undefined') {
            brkdown_con.append('<div class="contry-item-import"><span class="contry-name-con">' + (response.country_name) + '</span> <span id="contry-import-' + (response.country_id) + '" class="contry-import-msg">success</span></div>');
        }
        msg_con.html(response.msg);
        loader_con.html('');
    });

    request.fail(function (jqXHR, textStatus) {
        loader_con.html('');
        msg_con.html(jobsearch_importlocs_vars.error_msg);
    });
});

function jobseacrh_import_con_state_cities(country_id, next_country_id, next_country_name) {
    var msge_con = jQuery('.load-all-contries').find('.msge-con');
    var brkdown_con = jQuery('.load-all-contries').find('.all-breakdown-con');
    brkdown_con.append('<div class="contry-item-import"><span class="contry-name-con">' + (next_country_name) + '</span> <span id="contry-import-' + (next_country_id) + '" class="contry-import-msg">success</span></div>');
    
    var loader_con = jQuery('#contry-import-' + next_country_id);
    loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
    var request = $.ajax({
        url: jobsearch_importlocs_vars.ajax_url,
        method: "POST",
        data: {
            'country_id': next_country_id,
            'action': 'jobsearch_import_locations_con_states_cities'
        },
        dataType: "json"
    });
    request.done(function (response) {
        if (typeof response.msg !== 'undefined') {
            msge_con.append(response.msg);
        }
        loader_con.html('success');
    });

    request.fail(function (jqXHR, textStatus) {
        loader_con.html('fail');
    });
}