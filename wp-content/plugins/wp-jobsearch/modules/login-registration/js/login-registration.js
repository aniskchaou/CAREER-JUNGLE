jQuery(document).ready(function ($) {

    // demo login
    $(document).on('click', '.jobsearch-demo-login-btn', function () {
        var _this = jQuery(this);
        var user_type = 'candidate';
        var icon_class = 'jobsearch-icon jobsearch-user';
        if (_this.hasClass('employer-login-btn')) {
            user_type = 'employer';
            icon_class = 'jobsearch-icon jobsearch-building';
        }

        _this.find('i').attr('class', 'fa fa-refresh fa-spin');

        var request = $.ajax({
            url: jobsearch_login_register_common_vars.ajax_url,
            method: "POST",
            data: {
                'user_type': user_type,
                'action': 'jobsearch_demo_user_login',
            },
            dataType: "json"
        });
        request.done(function (response) {
            if (typeof response.redirect !== 'undefined') {
                window.location.href = response.redirect;
                return false;
            }
            window.location.reload(true);
        });

        request.fail(function (jqXHR, textStatus) {
            _this.find('i').attr('class', icon_class);
        });
    });

    // Post login form
    $(document).on('click', '.jobsearch-login-submit-btn', function (e) {
        e.preventDefault();
        var _this = $(this),
                this_id = $(this).data('id'),
                login_form = $('#login-form-' + this_id),
                msg_con = login_form.find('.login-reg-errors'),
                loader_con = login_form.find('.form-loader');
        var button = $(this).find('button');
        var btn_html = button.html();
        msg_con.hide();
        _this.addClass('disabled-btn');
        _this.attr('disabled', 'disabled');
        //button.html('loading');
        loader_con.show();
        loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
        $.post(jobsearch_login_register_common_vars.ajax_url, login_form.serialize(), function (data) {

            var obj = $.parseJSON(data);
            msg_con.html(obj.message);

            loader_con.hide();
            loader_con.html('');
            _this.removeClass('disabled-btn');
            _this.removeAttr('disabled');
            msg_con.slideDown('slow');
            if (obj.error == false) {
                // $('#pt-user-modal .modal-dialog').addClass('loading');
                //window.location.reload(true);
                if (typeof obj.redirect !== 'undefined') {
                    window.location.href = obj.redirect;
                }
                button.hide();
            }

            button.html(btn_html);
        });
    });
    // end login post

    // Reset Password
    // Switch forms login/register
    $(document).on('click', '.lost-password', function (e) {
        e.preventDefault();
        var this_id = $(this).data('id');
        $('.login-form-' + this_id).slideUp();
        $('.reset-password-' + this_id).slideDown();
    });
    $(document).on('click', '.login-form-btn', function (e) {
        e.preventDefault();
        var this_id = $(this).data('id');
        $('.login-form-' + this_id).slideDown();
        $('.reset-password-' + this_id).slideUp();
    });
    $(document).on('click', '.register-form', function (e) {
        e.preventDefault();
        var login_form = jQuery('#JobSearchModalLogin').find('form[id^="login-form-"]');
        var rgistr_form = jQuery('#JobSearchModalLogin').find('form[id^="registration-form-"]');
        var this_id = $(this).data('id');
        $('.reset-password-' + this_id).slideUp();
        $('.register-' + this_id).slideDown();
        $('.login-form-' + this_id).slideUp();
        
        // for redirect url
        var redrct_hiden_field = login_form.find('input[name="jobsearch_wredirct_url"]');
        if (redrct_hiden_field.length > 0) {
            var redrct_hiden_val = redrct_hiden_field.val();
            rgistr_form.append('<input type="hidden" name="jobsearch_wredirct_url" value="' + redrct_hiden_val + '">');
            redrct_hiden_field.remove();
        }

        // for packages
        var pkginfo_hiden_field = login_form.find('input[name="extra_login_params"]');
        if (pkginfo_hiden_field.length > 0) {
            var pkginfo_hiden_val = pkginfo_hiden_field.val();
            rgistr_form.append('<input type="hidden" name="extra_login_params" value="' + pkginfo_hiden_val + '">');
            pkginfo_hiden_field.remove();
        }
    });
    $(document).on('click', '.reg-tologin-btn', function (e) {
        e.preventDefault();
        var login_form = jQuery('#JobSearchModalLogin').find('form[id^="login-form-"]');
        var rgistr_form = jQuery('#JobSearchModalLogin').find('form[id^="registration-form-"]');
        var this_id = $(this).data('id');
        $('.reset-password-' + this_id).slideUp();
        $('.register-' + this_id).slideUp();
        $('.login-form-' + this_id).slideDown();
        
        // for redirect url
        var redrct_hiden_field = rgistr_form.find('input[name="jobsearch_wredirct_url"]');
        if (redrct_hiden_field.length > 0) {
            var redrct_hiden_val = redrct_hiden_field.val();
            login_form.append('<input type="hidden" name="jobsearch_wredirct_url" value="' + redrct_hiden_val + '">');
            redrct_hiden_field.remove();
        }

        // for packages
        var pkginfo_hiden_field = rgistr_form.find('input[name="extra_login_params"]');
        if (pkginfo_hiden_field.length > 0) {
            var pkginfo_hiden_val = pkginfo_hiden_field.val();
            login_form.append('<input type="hidden" name="extra_login_params" value="' + pkginfo_hiden_val + '">');
            pkginfo_hiden_field.remove();
        }
    });

    $(document).on('click', '.user-type-chose-btn', function () {
        var this_type = $(this).attr('data-type');
        if (this_type != 'jobsearch_employer' && this_type != 'jobsearch_candidate') {
            $('.user-candidate-spec-field').slideUp();
            $('.user-employer-spec-field').slideUp();
            $('.employer-cus-field').slideUp();
            $('.candidate-cus-field').slideUp();
            
            $('.' + this_type + '-spec-field').slideDown();
        } else {
            if (this_type == 'jobsearch_employer') {
                $('.user-candidate-spec-field').slideUp();
                $('.user-employer-spec-field').slideDown();
                $('.employer-cus-field').slideDown();
                $('.candidate-cus-field').slideUp();
                
                $('.jobsearch_recruiter-spec-field').slideUp();
                
                $('.jobsearch-register-form').find('.jobsearch-box-title-sub').slideUp();
                $('.jobsearch-register-form').find('.jobsearch-login-media').slideUp();
            } else {
                $('.user-candidate-spec-field').slideDown();
                $('.user-employer-spec-field').slideUp();
                $('.employer-cus-field').slideUp();
                $('.candidate-cus-field').slideDown();
                
                $('.jobsearch_recruiter-spec-field').slideUp();
                
                $('.jobsearch-register-form').find('.jobsearch-box-title-sub').slideDown();
                $('.jobsearch-register-form').find('.jobsearch-login-media').slideDown();
            }
        }
        $(this).parents('.jobsearch-user-type-choose').find('li').removeClass('active');
        $(this).parent('li').addClass('active');
        $(this).parents('form').find('input[name="pt_user_role"]').val(this_type);
    });

    $(document).on('change', 'input[type="radio"][name="pt_user_role"]', function () {
        var this_type = $(this).val();
        if (this_type != 'jobsearch_employer' && this_type != 'jobsearch_candidate') {
            $('.user-candidate-spec-field').slideUp();
            $('.user-employer-spec-field').slideUp();
            $('.employer-cus-field').slideUp();
            $('.candidate-cus-field').slideUp();
            
            $('.' + this_type + '-spec-field').slideDown();
        } else {
            if (this_type == 'jobsearch_employer') {
                $('.user-candidate-spec-field').slideUp();
                $('.user-employer-spec-field').slideDown();
                $('.employer-cus-field').slideDown();
                $('.candidate-cus-field').slideUp();
                $('.jobsearch_recruiter-spec-field').slideUp();
            } else {
                $('.user-candidate-spec-field').slideDown();
                $('.user-employer-spec-field').slideUp();
                $('.employer-cus-field').slideUp();
                $('.candidate-cus-field').slideDown();
                $('.jobsearch_recruiter-spec-field').slideUp();
            }
        }
    });

    $(document).on('click', '.jobsearch-reset-password-submit-btn', function (e) {
        e.preventDefault();
        var _this = $(this),
                this_id = $(this).data('id'),
                reset_password_form = $('#reset-password-form-' + this_id),
                msg_con = reset_password_form.find('.reset-password-errors'),
                loader_con = reset_password_form.find('.form-loader');
        var button = $(this).find('button');
        var btn_html = button.html();
        //button.html('loading');
        msg_con.hide();
        _this.addClass('disabled-btn');
        _this.attr('disabled', 'disabled');
        loader_con.show();
        loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
        
        var reset_password_form_o = $('#reset-password-form-' + this_id)[0];
        var formData = new FormData(reset_password_form_o);

        var request = $.ajax({
            url: jobsearch_login_register_common_vars.ajax_url,
            method: "POST",
            processData: false,
            contentType: false,
            data: formData,
            dataType: "json"
        });
        request.done(function (response) {
            msg_con.html(response.message);

            msg_con.slideDown('slow');

            _this.removeClass('disabled-btn');
            _this.removeAttr('disabled');

            loader_con.hide();
            loader_con.html('');

            button.html(btn_html);
        });
        request.fail(function (jqXHR, textStatus) {
            _this.removeClass('disabled-btn');
            _this.removeAttr('disabled');
            loader_con.hide();
            loader_con.html('');
        });
    });
    // end reset password

    // Post register form
    $(document).on('click', '.jobsearch-register-submit-btn', function (e) {
        e.preventDefault();
        var _this = $(this),
                this_id = $(this).data('id'),
                registration_form = $('#registration-form-' + this_id),
                msg_con = registration_form.find('.registration-errors'),
                loader_con = registration_form.find('.form-loader');
               
        var reg_user_role = 'jobsearch_candidate';
        var user_role_con = registration_form.find('input[name=pt_user_role]');
        if (user_role_con.attr('type') == 'radio') {
            reg_user_role = registration_form.find('input[name=pt_user_role]:checked').val();
        } else {
            reg_user_role = user_role_con.val();
        }
        
        var _form_error = false;
        
        var form_req_fields = registration_form.find('.jobsearch-regrequire-field');
        if (form_req_fields.length > 0) {
            jQuery.each(form_req_fields, function () {
                var _this_obj = jQuery(this);
                if (typeof _this_obj.attr('name') !== undefined && _this_obj.attr('name') != '' && _this_obj.attr('name') != 'undefined' && !_this_obj.hasClass('selectize-control')) {
                    var field_type = 'text';
                    if (_this_obj.parent('.jobsearch-profile-select').length > 0) {
                        field_type = 'select';
                    }
                    if (_this_obj.attr('type') == 'checkbox' || _this_obj.attr('type') == 'radio') {
                        var chek_field_name = _this_obj.attr('name');
                        if ((jQuery('input[name="' + chek_field_name + '"]:checked').length) <= 0) {
                            _form_error = true;
                            _this_obj.parents('.jobsearch-cusfield-checkbox').css({"border": "1px solid #ff0000"});
                        } else {
                            _this_obj.parents('.jobsearch-cusfield-checkbox').css({"border": "none"});
                        }
                    } else {
                        if (_this_obj.val() == '' || _this_obj.val() === null) {
                            if (field_type == 'select') {
                                _form_error = true;
                                _this_obj.parent('.jobsearch-profile-select').css({"border": "1px solid #ff0000"});
                            } else {
                                if (_this_obj.is(':visible')) {
                                    _form_error = true;
                                    _this_obj.css({"border": "1px solid #ff0000"});
                                }
                            }
                        } else {
                            if (field_type == 'select') {
                                _this_obj.parent('.jobsearch-profile-select').css({"border": "none"});
                            } else {
                                if (!_this_obj.hasClass('phone-input-error')) {
                                    _this_obj.css({"border": "1px solid #efefef"});
                                }
                            }
                        }
                    }
                }
            });
        }
        
        var user_email = registration_form.find('input[name=pt_user_email]');
        
        var cv_file = registration_form.find('input[name="candidate_cv_file"]');
        
        if (cv_file.length > 0 && cv_file.val() == '' && cv_file.hasClass('cv_is_req') && reg_user_role == 'jobsearch_candidate') {
            _form_error = true;
            jQuery('#jobsearch-upload-cv-main' + this_id).css({"border": "1px solid #ff0000"});
        } else {
            jQuery('#jobsearch-upload-cv-main' + this_id).css({"border": "none"});
        }
        
        if (!_form_error) {

            var user_phone = registration_form.find('input[name="pt_user_phone"]');

            var get_terr_val = jobsearch_accept_terms_cond_pop(registration_form);
            if (get_terr_val != 'yes') {
                return false;
            }

            var error = 0;
            //
            if (user_phone.hasClass('phone-input-error')) {
                jQuery('.phone-input-error').css({"border": "1px solid #ff0000"});
                error = 1;
            }

            if (error == 0) {
                var button = $(this).find('button');
                var btn_html = button.html();
                //button.html('loading');
                msg_con.hide();
                _this.addClass('disabled-btn');
                _this.attr('disabled', 'disabled');
                loader_con.show();
                loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
                //var form_data = registration_form.serialize();
                var reg_ser_form = $('#registration-form-' + this_id)[0];
                var formData = new FormData(reg_ser_form);

                var request = $.ajax({
                    url: jobsearch_login_register_common_vars.ajax_url,
                    method: "POST",
                    processData: false,
                    contentType: false,
                    data: formData,
                    dataType: "json"
                });
                request.done(function (response) {
                    if (typeof response.message !== 'undefined') {
                        msg_con.html(response.message);
                        msg_con.slideDown('slow');
                        button.html(btn_html);
                        _this.removeClass('disabled-btn');
                        _this.removeAttr('disabled');

                        if (typeof response.error !== 'undefined' && response.error == true) {
                            loader_con.hide();
                            loader_con.html('');
                            return false;
                        }

                        if (typeof response.redirect !== 'undefined') {
                            window.location.href = response.redirect;
                        } else {
                            if (typeof response.email_auth !== 'undefined' && response.email_auth == '1') {
                                var closePopOpnVrify = setInterval(function() {
                                    loader_con.html('');
                                    jQuery('.jobsearch-modal').removeClass('fade-in').addClass('fade');
                                    jQuery('body').removeClass('jobsearch-modal-active');
                                    jQuery('#JobSearchModalAccountActivationForm').find('input[name="user_email"]').val(user_email.val());
                                    jQuery('#JobSearchModalAccountActivationForm').find('input[name="user_email"]').attr('value', user_email.val());
                                    jobsearch_modal_popup_open('JobSearchModalAccountActivationForm');
                                    clearInterval(closePopOpnVrify);
                                }, 2000);
                            } else {
                                loader_con.html('');
                            }
                        }
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    _this.removeClass('disabled-btn');
                    _this.removeAttr('disabled');
                    loader_con.hide();
                    loader_con.html('');
                });
            }
        }
    });
});

jQuery(document).on('click', '.jobsearch-resend-accactbtn', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var user_login = _this.attr('data-login');
    _this.find('i').remove();
    var _this_html = _this.html();
    //
    var thisemail_err_con = _this.parents('.login-reg-errors').find('.email-exceed-err');
    thisemail_err_con.hide();
    //
    _this.html(_this_html + '&nbsp;<i class="fa fa-refresh fa-spin"></i>');
    var request = jQuery.ajax({
        url: jobsearch_plugin_vars.ajax_url,
        method: "POST",
        data: {
            user_login: user_login,
            action: 'jobsearch_resend_user_acc_approval_email',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.success !== 'undefined' && response.success == '1') {
            _this.html(_this_html + '&nbsp;<i class="fa fa-check"></i>');
            window.location.reload(true);
        } else {
            _this.html(_this_html + '<i class="fa fa-times"></i>');
            if (thisemail_err_con.length > 0) {
                thisemail_err_con.html(response.msg);
            } else {
                _this.parents('.login-reg-errors').append('<div class="alert alert-danger email-exceed-err">' + response.msg + '</div>');
            }
            thisemail_err_con.removeAttr('style');
        }
    });

    request.fail(function (jqXHR, textStatus) {
        _this.html(_this_html + '&nbsp;<i class="fa fa-times"></i>');
    });
});

jQuery(document).on('click', '.user-passreset-submit-btn', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var _user_id = _this.attr('data-id');
    var _user_key = _this.attr('data-key');

    var this_form = _this.parents('form');
    var this_loader = this_form.find('.loader-box');
    var this_msg_con = this_form.find('.message-box');

    var new_pass = this_form.find('input[name="new_pass"]');
    var conf_pass = this_form.find('input[name="conf_pass"]');
    
    var error = 0;
    if (new_pass.val() == '') {
        error = 1;
        new_pass.css({"border": "1px solid #ff0000"});
    } else {
        new_pass.css({"border": "1px solid #d3dade"});
    }
    if (conf_pass.val() == '') {
        error = 1;
        conf_pass.css({"border": "1px solid #ff0000"});
    } else {
        conf_pass.css({"border": "1px solid #d3dade"});
    }

    if (error == 0) {

        this_msg_con.hide();
        this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: jobsearch_plugin_vars.ajax_url,
            method: "POST",
            data: {
                user_id: _user_id,
                user_key: _user_key,
                new_pass: new_pass.val(),
                conf_pass: conf_pass.val(),
                action: 'jobsearch_pass_reseting_by_redirect_url',
            },
            dataType: "json"
        });

        request.done(function (response) {
            var msg_before = '';
            var msg_after = '';
            if (typeof response.error !== 'undefined') {
                if (response.error == '1') {
                    msg_before = '<div class="alert alert-danger"><i class="fa fa-times"></i>';
                    msg_after = '</div>';
                } else if (response.error == '0') {
                    msg_before = '<div class="alert alert-success"><i class="fa fa-check"></i>';
                    msg_after = '</div>';
                }
            }
            if (typeof response.msg !== 'undefined') {
                this_msg_con.html(msg_before + response.msg + msg_after);
                this_msg_con.slideDown();
                if (typeof response.error !== 'undefined' && response.error == '0') {
                    new_pass.val('');
                    conf_pass.val('');
                    this_form.find('ul.email-fields-list').slideUp();
                }
            } else {
                this_msg_con.html(jobsearch_plugin_vars.error_msg);
            }
            this_loader.html('');

        });

        request.fail(function (jqXHR, textStatus) {
            this_loader.html(jobsearch_plugin_vars.error_msg);
        });
    }
});