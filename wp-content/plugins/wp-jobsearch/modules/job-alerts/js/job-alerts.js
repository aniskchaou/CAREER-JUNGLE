var $ = jQuery;
var jobsearch_selectize_fields = '';
var jobsearch_cfselectize_fields = '';
$(document).on('click', '.email-jobs-top', function () {
    $(".job-alert-container-top .validation").slideUp();
    $(".job-alert-container-top").slideToggle();
    $(".job-alert-container-top").parent('.jobsearch-search-filter-wrap').toggleClass('jobsearch-add-padding');
    return false;
});

$(document).on('click', '.btn-close-job-alert-box', function () {
    $(".job-alert-container-top").slideToggle();
    return false;
});

jQuery(document).on('click', '.jobalert-submit', function (ev) {
    ev.preventDefault();
    var _this = jQuery(this);
    if (_this.hasClass('jobalert-save-withlogin')) {
        jobsearch_modal_popup_open('JobSearchModalLogin');
        jQuery('.reg-tologin-btn').trigger('click');
        return false;
    }
    var pop_html_con = jQuery('#popup_alert_filtrscon');
    var sh_globrnd_id = pop_html_con.find('.jobsearch-job-shatts').attr('data-id');
    var job_shatts_str = pop_html_con.find('.jobsearch-job-shatts').html();

    var jobalert_name_error = jobsearch_jobalerts_vars.name_field_error;
    var jobalert_email_error = jobsearch_jobalerts_vars.email_field_error;

    var validate_msg_con = jQuery(".job-alert-container-top .validation");
    validate_msg_con.slideUp();

    var frequency = $('input[name="alert-frequency"]:checked').val();
    if (typeof frequency == "undefined") {
        frequency = "never";
    }

    var email = _this.parents('.job-alert-box').find(".email-input-top").val();

    var name = _this.parents('.job-alert-box').find(".name-input-top").val();
    var re = RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,20}$/i);
    if (!re.test(email)) {
        validate_msg_con.find('label').html(jobalert_email_error);
        validate_msg_con.addClass("error").slideDown();
        return false;
    } else if (name == '') {
        validate_msg_con.find('label').html(jobalert_name_error);
        validate_msg_con.addClass("error").slideDown();
        return false;
    }

    var popup_main = jQuery('#JobSearchModalJobAlertsSelect');

    popup_main.find('.model-heading h2').html(name);
    popup_main.find('input[name=alerts_name]').val(name);
    popup_main.find('input[name=alerts_email]').val(email);

    var main_filtrs_form = _this.parents('form')[0];
    var _send_data = new FormData(main_filtrs_form);
    _send_data.append('alert_frequency', frequency);
    _send_data.append('sh_globrnd_id', sh_globrnd_id);
    _send_data.append('job_shatts_str', job_shatts_str);
    _send_data.append('action', 'jobsearch_alrtmodal_popup_openhtml');

    _this.attr('disabled', true);
    _this.html('<i class="fa fa-refresh fa-spin"></i>');
    var request = jQuery.ajax({
        type: "POST",
        url: jobsearch_jobalerts_vars.ajax_url,
        processData: false,
        contentType: false,
        data: _send_data,
        dataType: "json",
    });
    request.done(function (response) {
        if (typeof response.pop_html && response.pop_html != '') {
            pop_html_con.html(response.pop_html);
        }
        jobsearch_modal_popup_open('JobSearchModalJobAlertsSelect');
        _this.html(jobsearch_jobalerts_vars.submit_txt);
        _this.removeAttr('disabled');
        jobsearch_selectize_fields = jQuery('.jobsearch-profile-select.to-fancyselect-con').find('select').selectize({
            plugins: ['remove_button'],
            allowEmptyOption: true
        });
        jobsearch_cfselectize_fields = jQuery('.jobsearch-profile-select.to-cffancyselect-con').find('select').selectize({
            plugins: ['remove_button'],
            allowEmptyOption: true
        });
    });
    request.fail(function (jqXHR, textStatus) {
        _this.html(jobsearch_jobalerts_vars.submit_txt);
        _this.removeAttr('disabled');
    });

    return false;
});

jQuery(document).on("click", '.jobsearch-alrtslectize-remove', function () {
    var _this = jQuery(this);
    var this_id = jQuery(this).attr('data-selid');
    var selectize = jobsearch_selectize_fields[this_id].selectize;
    selectize.clear();
    _this.hide();
});

jQuery(document).on("click", '.jobsearch-alrtslectizecf-remove', function () {
    var _this = jQuery(this);
    var this_id = jQuery(this).attr('data-selid');
    var selectize = jobsearch_cfselectize_fields[this_id].selectize;
    selectize.clear();
    _this.hide();
});

jQuery(document).on("change", '.to-fancyselect-con select', function () {
    var _this = jQuery(this);
    var this_remove_btn = _this.parents('.to-fancyselect-con').find('.jobsearch-alrtslectize-remove');
    if (this_remove_btn.length > 0) {
        if (_this.val() == '') {
            this_remove_btn.hide();
        } else {
            this_remove_btn.removeAttr('style');
        }
    }
});

jQuery(document).on("change", '.to-cffancyselect-con select', function () {
    var _this = jQuery(this);
    var this_remove_btn = _this.parents('.to-cffancyselect-con').find('.jobsearch-alrtslectizecf-remove');
    if (this_remove_btn.length > 0) {
        if (_this.val() == '') {
            this_remove_btn.hide();
        } else {
            this_remove_btn.removeAttr('style');
        }
    }
});

jQuery(document).on('click', '.jobsearch-savejobalrts-sbtn', function () {
    var _this = jQuery(this);
    var pop_html_con = jQuery('#popup_alert_filtrscon');
    var job_shatts_str = pop_html_con.find('.jobsearch-job-shatts').html();
    var pop_msg_con = _this.parent('.alret-submitbtn-con').find('.falrets-msg');
    var main_filtrs_form = _this.parents('form')[0];
    var _send_data = new FormData(main_filtrs_form);

    _send_data.append('window_location', window.location.toString());
    _send_data.append('search_query', jQuery(".jobs_query").text());
    _send_data.append('job_shatts_str', job_shatts_str);

    _this.attr('disabled', true);
    _this.html('<i class="fa fa-refresh fa-spin"></i>');

    var request = jQuery.ajax({
        type: "POST",
        url: jobsearch_jobalerts_vars.ajax_url,
        processData: false,
        contentType: false,
        data: _send_data,
        dataType: "json",
    });
    request.done(function (response) {
        var msg_before = '<div class="alert alert-success"><i class="fa fa-check"></i> ';
        var msg_after = '</div>';
        if (response.success == false) {
            var msg_before = '<div class="alert alert-danger"><i class="fa fa-times"></i> ';
            var msg_after = '</div>';
        }
        if (typeof response.message && response.message != '') {
            pop_msg_con.html(msg_before + response.message + msg_after);
            if (response.success == true) {
                var closing_alrt_popup = setInterval(function () {
                    jQuery('.jobsearch-modal').removeClass('fade-in').addClass('fade');
                    jQuery('body').removeClass('jobsearch-modal-active');
                    clearInterval(closing_alrt_popup);
                }, 1000);
            }
        }
        _this.html(jobsearch_jobalerts_vars.save_alert_txt);
        _this.removeAttr('disabled');
    });
    request.fail(function (jqXHR, textStatus) {
        _this.html(jobsearch_jobalerts_vars.save_alert_txt);
        _this.removeAttr('disabled');
    });
    return false;
});

jQuery(document).on('change', '#popup_alert_filtrsform input.chagn-keywords-field, #popup_alert_filtrsform select', function () {
    var _this = jQuery(this);
    var pop_html_con = jQuery('#modpop-criteria-tags');
    var lodear_con = pop_html_con.find('.tags-loder');

    var main_filtrs_form = _this.parents('form')[0];
    var _send_data = new FormData(main_filtrs_form);

    _send_data.delete('action');
    _send_data.append('jobsearch_alert_tagsup', '1');
    _send_data.append('action', 'jobsearch_jobsearch_alert_tags_update');

    pop_html_con.append('<span class="tags-loder"><i class="fa fa-refresh fa-spin"></i></span>');

    var request = jQuery.ajax({
        type: "POST",
        url: jobsearch_jobalerts_vars.ajax_url,
        processData: false,
        contentType: false,
        data: _send_data,
        dataType: "json",
    });
    request.done(function (response) {
        if (typeof response.html && response.html != '') {
            pop_html_con.html(response.html);
            return false;
        }
        pop_html_con.find('.tags-loder').remove();
    });
    request.fail(function (jqXHR, textStatus) {
        pop_html_con.find('.tags-loder').remove();
    });
    return false;
});

jQuery(document).on('click', '.jobsearch-del-user-job-alert', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    if (this_id > 0) {
        var conf = confirm('Are you sure!');
        if (conf) {
            _this.find('i').attr('class', 'fa fa-refresh fa-spin');
            var request = jQuery.ajax({
                url: jobsearch_jobalerts_vars.ajax_url,
                method: "POST",
                data: {
                    'alert_id': this_id,
                    'action': 'jobsearch_user_job_alert_delete',
                },
                dataType: "json"
            });

            request.done(function (response) {
                _this.parents('tr').fadeOut();
            });

            request.fail(function (jqXHR, textStatus) {
                _this.parents('tr').fadeOut();
            });
        }
    }
});