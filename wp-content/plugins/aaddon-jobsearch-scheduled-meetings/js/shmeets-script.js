jQuery(document).ready(function() {
    jQuery('.meetin-time-insetins').datetimepicker({
        datepicker: false,
        timepicker: true,
        format: 'H:i'
    });
});

jQuery(document).on('click', '.jobsearch-creatmeetin-btn', function () {
    var this_id = jQuery(this).attr('data-id');
    var this_cand = jQuery(this).attr('data-cand');
    jQuery('#JobSearchSchedMeetingCreate').find('input[name=meeting_obj_id]').val(this_id);
    jQuery('#JobSearchSchedMeetingCreate').find('input[name=meeting_with_cand]').val(this_cand);
    jQuery('#JobSearchSchedMeetingCreate').find('.jobsearch-meetinsched-calendr').removeAttr('style');
    jQuery('#JobSearchSchedMeetingCreate').find('.meet-fields-ulcon').removeAttr('style');
    jQuery('#JobSearchSchedMeetingCreate').find('.meet-sched-msg').hide();
    jobsearch_modal_popup_open('JobSearchSchedMeetingCreate');
});

jQuery(document).on('click', '.jobsearch-meet-reschedulepop', function () {
    var this_id = jQuery(this).attr('data-id');
    var this_cd = jQuery(this).attr('data-cd');
    var this_mt = jQuery(this).attr('data-md');
    jQuery('#JobSearchSchedMeetingReSched').find('input[name=meeting_obj_id]').val(this_id);
    jQuery('#JobSearchSchedMeetingReSched').find('input[name=meeting_with_cand]').val(this_cd);
    if (jQuery('#JobSearchSchedMeetingReSched').find('input[name=meeting_with_emp]').length > 0) {
        jQuery('#JobSearchSchedMeetingReSched').find('input[name=meeting_with_emp]').val(this_cd);
        var this_form = jQuery('#JobSearchSchedMeetingReSched').find('form');
        var loader_con = this_form.find('.meet-sched-loder');
        var slots_con = this_form.find('.jobsearch-meetime-slotscon');

        loader_con.removeAttr('style');

        var request = jQuery.ajax({
            url: jobsearch_shmeets_vars.ajax_url,
            method: "POST",
            data: {
                emp: this_cd,
                action: 'jobsearch_generate_scheduled_meeting_time_slots'
            },
            dataType: "json"
        });
        request.done(function (response) {
            if (typeof response.slots !== undefined && response.slots != '') {
                slots_con.html(response.slots);
            }
            if (typeof response.emp_id !== undefined && response.emp_id != '') {
                this_form.attr('data-eid', response.emp_id);
            }
        });
        request.complete(function () {
            loader_con.hide();
        });
    }
    jQuery('#JobSearchSchedMeetingReSched').find('input[name=meeting_id]').val(this_mt);
    jQuery('#JobSearchSchedMeetingReSched').find('.jobsearch-meetinsched-calendr').removeAttr('style');
    jQuery('#JobSearchSchedMeetingReSched').find('.meet-fields-ulcon').removeAttr('style');
    jQuery('#JobSearchSchedMeetingReSched').find('.meet-sched-msg').hide();
    jobsearch_modal_popup_open('JobSearchSchedMeetingReSched');
});

jQuery(document).on('click', '.jobsearch-meet-cancelbtn', function () {
    var _this = jQuery(this);
    var this_mt = _this.attr('data-md');

    var loader_con = _this.find('.cancel-loder');

    if (_this.attr('data-id')) {
        loader_con.html('<i class="fa fa-refresh fa-spin"></i>');

        var request = jQuery.ajax({
            url: jobsearch_shmeets_vars.ajax_url,
            method: "POST",
            data: {
                meet_id: this_mt,
                action: 'jobsearch_cancel_scheduled_meeting_bycand'
            },
            dataType: "json"
        });
        request.done(function (response) {
            loader_con.html('');
            if (typeof response.text !== undefined && response.text != '') {
                _this.html(response.text);
                _this.removeAttr('data-id')
            }
        });
        request.complete(function () {
            loader_con.html('');
        });
    }
});

jQuery(document).on('click', '.jobsearch-meet-deletebtn', function () {
    var _this = jQuery(this);
    var this_mt = _this.attr('data-md');

    var loader_con = _this.find('.delete-loder');

    if (_this.attr('data-id')) {
        loader_con.html('<i class="fa fa-refresh fa-spin"></i>');

        var request = jQuery.ajax({
            url: jobsearch_shmeets_vars.ajax_url,
            method: "POST",
            data: {
                meet_id: this_mt,
                action: 'jobsearch_delete_scheduled_meeting_byemp'
            },
            dataType: "json"
        });
        request.done(function (response) {
            loader_con.html('');
            if (typeof response.text !== undefined && response.text != '') {
                _this.html(response.text);
                _this.removeAttr('data-id');
                window.location.reload();
            }
        });
        request.complete(function () {
            loader_con.html('');
        });
    }
});

function jobsearch_set_meetdatew_format(date) {
    var today = new Date(date);
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    today = dd + '-' + mm + '-' + yyyy;
    return today;
}

jQuery(document).ready(function() {
    if (jQuery('#meetcalendr-shedule-desct').length > 0) {
        var desct_app = angular.module('jobsearchShMeetingApp', ['multipleDatePicker']);
        desct_app.controller('jobsearchShMeetingController', ['$scope', function ($scope) {
            moment.locale(jobsearch_shmeets_vars.wp_locale);
            $scope.$watch('availArrayOfDates', function (new_selctd_value) {
                if (new_selctd_value) {
                    jQuery(new_selctd_value).each(function (index, elem) {
                        if (typeof elem._d !== 'undefined') {
                            var get_edate = elem._d;
                            var form_date = jobsearch_set_meetdatew_format(get_edate);
                            jQuery('#meetcalendr-shedule-desct').find('input[name="meeting_date"]').val(form_date);
                            var this_form = jQuery('form#sched-creatmeetin-form');
                            var loader_con = this_form.find('.meet-sched-loder');
                            var message_con = this_form.find('.meet-sched-msg');
                            var slots_con = this_form.find('.jobsearch-meetime-slotscon');
                            var meet_date = this_form.find('input[name=meeting_date]');
                            var meet_duration = this_form.find('input[name=meeting_duration]');
                            var emp_id = this_form.attr('data-eid');

                            message_con.hide();
                            loader_con.removeAttr('style');

                            var request = jQuery.ajax({
                                url: jobsearch_shmeets_vars.ajax_url,
                                method: "POST",
                                data: {
                                    emp_id: emp_id,
                                    meet_date: meet_date.val(),
                                    meet_duration: meet_duration.val(),
                                    action: 'jobsearch_set_scheduled_meeting_time_slots'
                                },
                                dataType: "json"
                            });
                            request.done(function (response) {
                                if (typeof response.slots !== undefined && response.slots != '') {
                                    slots_con.html(response.slots);
                                }
                            });
                            request.complete(function () {
                                loader_con.hide();
                            });
                        }
                    });
                }
            }, false);
        }]);
        var cal_desct_dv = document.getElementById('meetcalendr-shedule-desct');
        angular.element(document).ready(function() {
            angular.bootstrap(cal_desct_dv, ['jobsearchShMeetingApp']);
        });
    }
    if (jQuery('#meetcalendr-reshedule-desct').length > 0) {
        var desct_app = angular.module('jobsearchReShMeetingApp', ['multipleDatePicker']);
        desct_app.controller('jobsearchReShMeetingController', ['$scope', function ($scope) {
            moment.locale(jobsearch_shmeets_vars.wp_locale);
            $scope.$watch('availArrayOfDates', function (new_selctd_value) {
                if (new_selctd_value) {
                    jQuery(new_selctd_value).each(function (index, elem) {
                        if (typeof elem._d !== 'undefined') {
                            var get_edate = elem._d;
                            var form_date = jobsearch_set_meetdatew_format(get_edate);
                            jQuery('#meetcalendr-reshedule-desct').find('input[name="meeting_date"]').val(form_date);
                            var this_form = jQuery('form#sched-updatmeetin-form');
                            var loader_con = this_form.find('.meet-sched-loder');
                            var message_con = this_form.find('.meet-sched-msg');
                            var slots_con = this_form.find('.jobsearch-meetime-slotscon');
                            var meet_date = this_form.find('input[name=meeting_date]');
                            var meet_duration = this_form.find('input[name=meeting_duration]');
                            var emp_id = this_form.attr('data-eid');

                            message_con.hide();
                            loader_con.removeAttr('style');

                            var request = jQuery.ajax({
                                url: jobsearch_shmeets_vars.ajax_url,
                                method: "POST",
                                data: {
                                    emp_id: emp_id,
                                    meet_date: meet_date.val(),
                                    meet_duration: meet_duration.val(),
                                    action: 'jobsearch_set_scheduled_meeting_time_slots'
                                },
                                dataType: "json"
                            });
                            request.done(function (response) {
                                if (typeof response.slots !== undefined && response.slots != '') {
                                    slots_con.html(response.slots);
                                }
                            });
                            request.complete(function () {
                                loader_con.hide();
                            });
                        }
                    });
                }
            }, false);
        }]);
        var cal_desct_redv = document.getElementById('meetcalendr-reshedule-desct');
        angular.element(document).ready(function() {
            angular.bootstrap(cal_desct_redv, ['jobsearchReShMeetingApp']);
        });
    }
});

jQuery(document).on('click', '.create-meetin-callbtn', function() {
    var _this = jQuery(this);
    var this_form = _this.parents('form');
    var loader_con = this_form.find('.meet-sched-loder');
    var message_con = this_form.find('.meet-sched-msg');
    var fields_ulcon = this_form.find('.meet-fields-ulcon');
    var calnder_con = this_form.find('.jobsearch-meetinsched-calendr');
    
    var zoom_meet = '0';
    if (_this.hasClass('create-zoomeetin-btn')) {
        zoom_meet = '1';
    }
    
    var meet_form_elem = this_form[0];
    var meet_form_data = new FormData(meet_form_elem);
    meet_form_data.append('zoom_meet', zoom_meet);
    
    loader_con.removeAttr('style');
    message_con.hide();
    
    var request = jQuery.ajax({
        url: jobsearch_shmeets_vars.ajax_url,
        method: "POST",
        processData: false,
        contentType: false,
        data: meet_form_data,
        dataType: "json"
    });
    request.done(function (response) {
        if (typeof response.msg !== undefined) {
            message_con.html(response.msg);
            message_con.removeAttr('style');
        }
        if (typeof response.error !== undefined && response.error == '0') {
            fields_ulcon.slideUp();
            calnder_con.slideUp();
        }
    });
    request.complete(function () {
        loader_con.hide();
    });
});

jQuery(document).on('submit', '#jobsearch-meetin-settinsform', function(ev) {
    ev.preventDefault();
    var this_form = jQuery(this);
    var loader_con = this_form.find('.meet-settings-loder');
    var message_con = this_form.find('.meet-settins-fmsg');
    var meet_form_elem = this_form[0];
    var meet_form_data = new FormData(meet_form_elem);
    
    loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
    message_con.hide();
    
    var request = jQuery.ajax({
        url: jobsearch_shmeets_vars.ajax_url,
        method: "POST",
        processData: false,
        contentType: false,
        data: meet_form_data,
        dataType: "json"
    });
    request.done(function (response) {
        if (typeof response.msg !== undefined) {
            message_con.html(response.msg);
            message_con.removeAttr('style');
        }
    });
    request.complete(function () {
        loader_con.html('');
    });
    return false;
});

jQuery(document).on('click', '.meetview-tosettinsbtn', function() {
    var _this = jQuery(this);
    var other_btn = jQuery('.meetview-backlistsbtn');
    var listing_con = jQuery('.meetings-list-con');
    var settins_con = jQuery('.meetings-settins-con');
    
    _this.hide();
    other_btn.removeAttr('style');
    listing_con.hide();
    settins_con.removeAttr('style');
});

jQuery(document).on('click', '.meetview-backlistsbtn', function() {
    var _this = jQuery(this);
    var other_btn = jQuery('.meetview-tosettinsbtn');
    var listing_con = jQuery('.meetings-list-con');
    var settins_con = jQuery('.meetings-settins-con');
    
    _this.hide();
    other_btn.removeAttr('style');
    settins_con.hide();
    listing_con.removeAttr('style');
});

jQuery(document).on('change', '.meetime-slot-duration', function() {
    var _this = jQuery(this);
    var this_form = _this.parents('form');
    var loader_con = this_form.find('.meet-sched-loder');
    var message_con = this_form.find('.meet-sched-msg');
    var slots_con = this_form.find('.jobsearch-meetime-slotscon');
    var meet_date = this_form.find('input[name=meeting_date]');
    var meet_duration = this_form.find('input[name=meeting_duration]');
    var emp_id = this_form.attr('data-eid');
    
    message_con.hide();
    loader_con.removeAttr('style');
    
    var request = jQuery.ajax({
        url: jobsearch_shmeets_vars.ajax_url,
        method: "POST",
        data: {
            emp_id: emp_id,
            meet_date: meet_date.val(),
            meet_duration: meet_duration.val(),
            action: 'jobsearch_set_scheduled_meeting_time_slots'
        },
        dataType: "json"
    });
    request.done(function (response) {
        if (typeof response.slots !== undefined && response.slots != '') {
            slots_con.html(response.slots);
        }
    });
    request.complete(function () {
        loader_con.hide();
    });
    return false;
});

jQuery(document).on('change', '#opt-zoomeet-switch', function () {
    var _this = jQuery(this);

    var switch_val = 'off';
    if (_this.is(':checked')) {
        switch_val = 'on';
    }

    var this_parent = _this.parents('.chekunchk-opt-box');
    var this_hinp = this_parent.find('input[type=hidden]');
    this_hinp.val(switch_val);
    var this_loader = this_parent.find('.opt-notific-lodr');
    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            btn_val: switch_val,
            action: 'jobsearch_zoom_meetin_onoff_callback'
        },
        dataType: "json"
    });

    request.done(function (response) {
        //
        this_loader.html('');
    });
    request.complete(function () {
        this_loader.html('');
    });
});

jQuery(document).on('submit', '.getauth-withzoom-form', function(ev) {
    ev.preventDefault();
    
    var _this = jQuery(this);
    var this_form = _this;
    
    var form_btn = this_form.find('input[type=submit]');
    var form_btn_txt = form_btn.val();
    
    var zoom_email = this_form.find('input[name=zoom_email]').val();
    var client_id = this_form.find('input[name=zoom_client_id]').val();
    var client_secret = this_form.find('input[name=client_secret]').val();

    form_btn.prop('disabled', true);
    form_btn.val(jobsearch_shmeets_vars.submitting);
    var request = jQuery.ajax({
        url: jobsearch_shmeets_vars.ajax_url,
        method: "POST",
        data: {
            zoom_email: zoom_email,
            client_id: client_id,
            client_secret: client_secret,
            action: 'jobsearch_zoom_auth_clientid_callback'
        },
        dataType: "json"
    });

    request.done(function (response) {
        //
        this_form.append(response.html);
    });
    request.complete(function () {
        form_btn.prop('disabled', false);
        form_btn.val(form_btn_txt);
    });
    
    return false;
});

jQuery(document).on('click', '.zoom-userreauth-btn', function() {
    jQuery('form.getauth-withzoom-form').slideDown();
});

jQuery(document).on('click', '.meetin-notespop-btn', function() {
    var this_id = jQuery(this).attr('data-id');
    if (jQuery('#JobSearchSchedMeetinNotes' + this_id).length > 0) {
        jobsearch_modal_popup_open('JobSearchSchedMeetinNotes' + this_id);
    }
});