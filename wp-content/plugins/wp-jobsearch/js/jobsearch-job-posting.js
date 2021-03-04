var $ = jQuery;
(function ($) {
    "use strict";
    $.fn.jobsearch_req_field_loop = function (callback, thisArg) {
        var me = this;
        return this.each(function (index, element) {
            return callback.call(thisArg || element, element, index, me);
        });
    };
})(jQuery);

jQuery('#job-sector').find('option').first().val('');
jQuery('#job-type').find('option').first().val('');

jQuery(document).ready(function () {

    var setReqFiled = setInterval(function () {
        var jobTypeFiled = jQuery('#job-type');
        if (jobTypeFiled.length > 0 && jobTypeFiled.hasClass('jobsearch-req-field')) {
            jobTypeFiled.parent('.jobsearch-profile-select').find('.selectize-control').removeClass('jobsearch-req-field');
            jobTypeFiled.parent('.jobsearch-profile-select').find('.selectize-dropdown').removeClass('jobsearch-req-field');
            jQuery('#job-type-selectized').removeClass('jobsearch-req-field');
        }
        clearInterval(setReqFiled);
    }, 1000);
});

function jobsearch_validate_form(that) {
    "use strict";
    var req_class = 'jobsearch-req-field',
            _this_form = $(that),
            form_validity = 'valid';
    var errors_counter = 1;

    _this_form.find('input.' + req_class + ',select.' + req_class + ',textarea.' + req_class).jobsearch_req_field_loop(function (element, index, set) {

        var job_desc_max_len = parseInt(jobsearch_posting_vars.desc_len_exceed_num);
        var eror_str = '';
        //console.log($(element).attr('name'));
        //console.log($(element).val());
        if ($(element).val() == '' || $(element).val() == null) {
            form_validity = 'invalid';
            eror_str = jobsearch_posting_vars.blank_field_msg;

        } else if ($(element).attr('id') == 'job_detail') {
            var field_val = $(element).val();
            if (field_val.length > job_desc_max_len) {
                form_validity = 'invalid';
                eror_str = jobsearch_posting_vars.desc_len_exceed_msg;

            } else {
                $(element).parents('.wp-editor-container').css({"border": "1px solid #eceeef"});
                $(element).parents('.form-field-sec').find('.field-error').html('');
            }
        } else if ($(element).attr('name') == 'reg_user_uname') {
            var field_val = $(element).val();
            if (!field_val.match(/^([a-zA-Z0-9_-]+)$/)) {
                form_validity = 'invalid';
                eror_str = 'Username can contain only alphanumeric characters, underscore(_), dash(-).';
            } else if (field_val.length > 20 || field_val.length < 3) {
                form_validity = 'invalid';
                eror_str = 'Username length should be between 3 to 20 characters.';
            } else {
                $(element).css({"border": "1px solid #eceeef"});
                $(element).parents('.form-field-sec').find('.field-error').html('');
            }
        } else if ($(element).attr('name') == 'reg_user_email') {
            var field_val = $(element).val();
            var email_pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,20}$/i);
            if (field_val == '' || !email_pattern.test(field_val)) {
                form_validity = 'invalid';
                eror_str = 'Please enter proper email address.';
            } else {
                $(element).css({"border": "1px solid #eceeef"});
                $(element).parents('.form-field-sec').find('.field-error').html('');
            }
        } else {
            $(element).css({"border": "1px solid #eceeef"});
        }

        if (eror_str != '') {
            //var animate_to = element;
            if ($(element).attr('id') == 'job_detail') {
                element = $(element).parents('.wp-editor-container');
            }
            if ($(element).hasClass('jobsearch-reqtext-editor')) {
                element = $(element).parents('.wp-editor-container');
            }
            if ($(element).attr('id') == 'job-type' || $(element).attr('id') == 'job-sector') {
                element = $(element).parents('.jobsearch-profile-select');
            }
            if ($(element).hasClass('multiselect-req')) {
                element = $(element).parents('.jobsearch-profile-select');
            }
            $(element).css({"border": "1px solid #ff0000"});
            var animate_to = $(element);

            $(element).parents('li').find('.field-error').html(eror_str);

            if (errors_counter == 1) {
                $('html, body').animate({scrollTop: animate_to.offset().top - 70}, 1000);
            }

            errors_counter++;
        } else {
            if ($(element).attr('id') == 'job-type' || $(element).attr('id') == 'job-sector') {
                element = $(element).parents('.jobsearch-profile-select');
                $(element).removeAttr('style');
            }
            if ($(element).hasClass('multiselect-req')) {
                element = $(element).parents('.jobsearch-profile-select');
                $(element).removeAttr('style');
            }
        }
    });

    if (form_validity == 'valid') {
        // API Locations
        var locations_type = jobsearch_posting_vars.locations_type;
        var is_req_apilocs = jobsearch_posting_vars.required_api_locs;

        if (locations_type == 'api' && is_req_apilocs == 'yes') {
            var api_loc_contry = jQuery('select[name="jobsearch_field_location_location1"]');
            if (api_loc_contry.length > 0) {
                var api_locval = api_loc_contry.val();
                if (api_locval == '') {
                    api_loc_contry.parent('.jobsearch-profile-select').css({"border": "1px solid #ff0000"});
                    var animate_to = api_loc_contry.parent('.jobsearch-profile-select');
                    jQuery('html, body').animate({scrollTop: animate_to.offset().top - 110}, 1000);
                    return false;
                } else {
                    api_loc_contry.parent('.jobsearch-profile-select').css({"border": "none"});
                }
            }
            var api_loc_state = jQuery('select[name="jobsearch_field_location_location2"]');
            if (api_loc_state.length > 0) {
                var api_locval = api_loc_state.val();
                if (api_locval == '') {
                    api_loc_state.parent('.jobsearch-profile-select').css({"border": "1px solid #ff0000"});
                    var animate_to = api_loc_state.parent('.jobsearch-profile-select');
                    jQuery('html, body').animate({scrollTop: animate_to.offset().top - 110}, 1000);
                    return false;
                } else {
                    api_loc_state.parent('.jobsearch-profile-select').css({"border": "none"});
                }
            }
            var api_loc_cities = jQuery('select[name="jobsearch_field_location_location3"]');
            if (api_loc_cities.length > 0) {
                var api_locval = api_loc_cities.val();
                if (api_locval == '') {
                    api_loc_cities.parent('.jobsearch-profile-select').css({"border": "1px solid #ff0000"});
                    var animate_to = api_loc_cities.parent('.jobsearch-profile-select');
                    jQuery('html, body').animate({scrollTop: animate_to.offset().top - 110}, 1000);
                    return false;
                } else {
                    api_loc_cities.parent('.jobsearch-profile-select').css({"border": "none"});
                }
            }
        }
        //

        var salary_validtion = true;
        var job_slary_type = jQuery('select[name="job_salary_type"]');
        if (job_slary_type.length > 0 && job_slary_type.val() == 'negotiable') {
            salary_validtion = false;
        }
        var salry_main_cont = jQuery('.salary-input-fordev');
        if (!salry_main_cont.hasClass('required-field')) {
            salary_validtion = false;
        }

        var min_salary_field = jQuery('.min-salary > input[name="job_salary"]');
        var max_salary_field = jQuery('.max-salary > input[name="job_max_salary"]');
        if (salary_validtion === true && min_salary_field.length > 0 && min_salary_field.length > 0) {
            var min_salary_fieldval = Number(min_salary_field.val());
            var max_salary_fieldval = Number(max_salary_field.val());
            //console.log(min_salary_fieldval);
            if (min_salary_fieldval <= 0) {
                min_salary_field.css({"border": "1px solid #ff0000"});
                var animate_to = min_salary_field;
                jQuery('html, body').animate({scrollTop: animate_to.offset().top - 70}, 1000);
                return false;
            } else if (min_salary_fieldval > 0 && max_salary_fieldval < min_salary_fieldval) {
                max_salary_field.css({"border": "1px solid #ff0000"});
                var animate_to = max_salary_field;
                jQuery('html, body').animate({scrollTop: animate_to.offset().top - 70}, 1000);
                return false;
            } else {
                max_salary_field.css({"border": "1px solid #eceeef"});
            }
        }
        
        if (_this_form.find('.cusfield-checkbox-required').find('input[type=checkbox]').length > 0) {
            var element_to_go = _this_form.find('.cusfield-checkbox-required');
            var req_checkboxs = _this_form.find('.cusfield-checkbox-required').find('input[type=checkbox]');
            var req_checkbox_err = 1;
            req_checkboxs.each(function() {
                if (jQuery(this).is(':checked')) {
                    req_checkbox_err = 0;
                }
            });
            if (req_checkbox_err == 1) {
                jQuery(element_to_go).css({"border": "1px solid #ff0000"});
                jQuery('html, body').animate({scrollTop: element_to_go.offset().top - 100}, 1000);
                return false;
            } else {
                jQuery(element_to_go).removeAttr('style');
            }
        }
        
        jQuery('.jobsearch-postjob-btn').addClass('disabled-btn').attr('disabled', 'disabled');
        return true;
    } else {
        return false;
    }
}

jQuery(document).on('change', 'select[name=job_salary_type]', function () {
    var _this = jQuery(this);
    var slary_input_con = _this.parents('li').find('.salary-input');
    var slary_curncy_con = jQuery('.jobsalary-curency-con');

    if (_this.val() == 'negotiable') {
        slary_input_con.hide();
        slary_curncy_con.hide();
    } else {
        slary_input_con.removeAttr('style');
        slary_curncy_con.removeAttr('style');
    }
});

jQuery(document).on('submit', 'form#job-posting-form', function () {
    // For custom upload field

    var $uplod_file_ret = jobsearch_cusfield_validate_attach_field(jQuery(this));
    if ($uplod_file_ret == false) {
        return false;
    }
    //
    var fields_1 = jobsearch_validate_form(jQuery(this));
    if (!fields_1) {
        return false;
    }
    var fields_2 = jobsearch_validate_seliz_req_form(jQuery(this));
    if (!fields_2) {
        return false;
    }
});

jQuery(document).on('change', 'select[name="job_salary_currency"]', function () {
    var _this = jQuery(this);
    var sel_curr = _this.find(':selected').attr('data-cur');
    jQuery('.salary-input').find('span').html(sel_curr);
});

function jobsearch_job_attach_files_url(event) {

    if (window.File && window.FileList && window.FileReader) {

        var file_types_str = jobsearch_posting_vars.job_files_mime_types;
        if (file_types_str != '') {
            var file_types_array = file_types_str.split('|');
        } else {
            var file_types_array = [];
        }
        var file_allow_size = jobsearch_posting_vars.job_files_max_size;
        var num_files_allow = jobsearch_posting_vars.job_num_files_allow;

        num_files_allow = parseInt(num_files_allow);
        file_allow_size = parseInt(file_allow_size);

        jQuery('#attach-files-holder').find('.adding-file').remove();
        var files = event.target.files;
        for (var i = 0; i < files.length; i++) {

            var _file = files[i];
            var file_type = _file.type;
            var file_size = _file.size;
            var file_name = _file.name;

            file_size = parseFloat(file_size / 1024).toFixed(2);

            if (file_size <= file_allow_size) {
                if (file_types_array.indexOf(file_type) >= 0) {
                    var file_icon = 'fa fa-file-text-o';
                    if (file_type == 'image/png' || file_type == 'image/jpeg') {
                        file_icon = 'fa fa-file-image-o';
                    } else if (file_type == 'application/msword' || file_type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                        file_icon = 'fa fa-file-word-o';
                    } else if (file_type == 'application/vnd.ms-excel' || file_type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                        file_icon = 'fa fa-file-excel-o';
                    } else if (file_type == 'application/pdf') {
                        file_icon = 'fa fa-file-pdf-o';
                    }

                    var rand_number = Math.floor((Math.random() * 99999999) + 1);
                    var ihtml = '\
                    <div class="jobsearch-column-3 adding-file">\
                        <div class="file-container">\
                            <a><i class="' + file_icon + '"></i> ' + file_name + '</a>\
                        </div>\
                    </div>';
                    jQuery('#attach-files-holder').append(ihtml);

                } else {
                    alert(jobsearch_posting_vars.file_type_error);
                    return false;
                }
            } else {
                alert(jobsearch_posting_vars.file_size_error);
                return false;
            }

            if (i >= num_files_allow) {
                break;
            }
        }
    }
}

jQuery(document).on('click', '.jobsearch-company-gallery .el-remove', function () {
    var e_target = jQuery(this).parent('li');
    e_target.fadeOut('slow', function () {
        e_target.remove();
    });
});

jQuery(document).on('change', '#jobsearch_job_apply_type', function () {
    if (jQuery(this).val() == 'external') {
        jQuery('#job-apply-external-url').slideDown();
        jQuery('#job-apply-by-email').hide();
    } else if (jQuery(this).val() == 'with_email') {
        jQuery('#job-apply-by-email').slideDown();
        jQuery('#job-apply-external-url').hide();
    } else {
        jQuery('#job-apply-external-url').hide();
        jQuery('#job-apply-by-email').hide();
    }
});

jQuery(document).on('click', 'input[name="job_package_featured"]', function () {
    var _this = jQuery(this);
    if (_this.is(":checked")) {
        jQuery('input[name="job_package_featured"]').prop('checked', false);
        _this.prop('checked', true);

        //
        jQuery('.jobsearch-pkgs-boughtnew').slideUp();
    }
});

jQuery(document).on('click', 'input[name="job_subs_package"]', function () {
    var _this = jQuery(this);
    if (_this.is(":checked")) {
        jQuery('input[name="job_subs_package"]').prop('checked', false);
        _this.prop('checked', true);
    }
    if (_this.hasClass('with_feature_jobs')) {
        jQuery('.feat-with-already-purp').slideDown();
    } else {
        jQuery('.feat-with-already-purp').slideUp();
    }
});

jQuery(document).on('click', 'input[name="job_package_new"]', function () {
    var _this = jQuery(this);
    if (_this.is(":checked")) {
        jQuery('input[name="job_package_new"]').prop('checked', false);
        _this.prop('checked', true);
    }
    if (_this.hasClass('with_feature_jobs')) {
        jQuery('.feat-with-fresh-npkg').slideDown();
    } else {
        jQuery('.feat-with-fresh-npkg').slideUp();
    }
});

jQuery(document).on('click', '.jobsearch-repblishin-jobtn', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    _this.next('input[name=republishin_job]').val('1');
    _this.parents('form').submit();
    return false;
});