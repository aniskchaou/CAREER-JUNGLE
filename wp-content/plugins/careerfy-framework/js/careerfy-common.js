var $ = jQuery;

jQuery(document).on('click', '.careerfy-ct-form', function (e) {
    e.preventDefault();
    var this_id = $(this).data('id'),
        msg_form = $('#ct-form-' + this_id),
        ajax_url = msg_form.data('ajax-url'),
        msg_con = msg_form.find('.careerfy-ct-msg'),
        msg_name = msg_form.find('input[name="u_name"]'),
        msg_email = msg_form.find('input[name="u_email"]'),
        msg_subject = msg_form.find('input[name="u_subject"]'),
        msg_phone = msg_form.find('input[name="u_number"]'),
        msg_type = msg_form.find('input[name="u_type"]'),
        msg_txt = msg_form.find('textarea[name="u_msg"]'),
        error = 0;

    var email_pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);

    if (msg_name.val() == '') {
        error = 1;
        msg_name.css({"border": "1px solid #ff0000"});
    } else {
        msg_name.css({"border": "1px solid #d3dade"});
    }

    if (msg_subject.val() == '') {
        error = 1;
        msg_subject.css({"border": "1px solid #ff0000"});
    } else {
        msg_subject.css({"border": "1px solid #d3dade"});
    }

    if (msg_email.val() == '') {
        error = 1;
        msg_email.css({"border": "1px solid #ff0000"});
    } else {
        if (!email_pattern.test(msg_email.val())) {
            error = 1;
            msg_email.css({"border": "1px solid #ff0000"});
        } else {
            msg_email.css({"border": "1px solid #d3dade"});
        }
    }

    if (msg_txt.val() == '') {
        error = 1;
        msg_txt.css({"border": "1px solid #ff0000"});
    } else {
        msg_txt.css({"border": "1px solid #d3dade"});
    }

    if (error == 0) {
        msg_con.html('<i class="fa fa-refresh fa-spin"></i>');

        var request = $.ajax({
            url: ajax_url,
            method: "POST",
            data: {
                u_name: msg_name.val(),
                u_email: msg_email.val(),
                u_subject: msg_subject.val(),
                u_phone: msg_phone.val(),
                u_msg: msg_txt.val(),
                u_type: msg_type.val(),
                action: 'careerfy_contact_form_submit',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.msg !== 'undefined') {
                msg_name.val('');
                msg_email.val('');
                msg_subject.val('');
                msg_phone.val('');
                msg_txt.val('');
                msg_con.html(response.msg);
            } else {
                msg_con.html(careerfy_framework_vars.error_msg);
            }
        });

        request.fail(function (jqXHR, textStatus) {
            msg_con.html(careerfy_framework_vars.error_msg);
        });
    }

    return false;
});

jQuery(document).on('click', '.careerfy-blog-post-like-btn', function () {

    'use strict';
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var icon_class = 'fa fa-heart-o';
    var icon_fill_class = 'fa fa-heart';
    var this_loader = _this.find('i');
    var this_counter = _this.find('span');
    this_loader.attr('class', 'fa fa-refresh fa-spin');

    var request = $.ajax({
        url: careerfy_funnc_vars.ajax_url,
        method: "POST",
        data: {
            post_id: this_id,
            action: 'careerfy_post_likes_count',
        },
        dataType: "json"
    });
    request.done(function (response) {
        if (typeof response.counter !== 'undefined' && response.counter != '') {

            this_counter.html(response.counter);
        }
        _this.find('i').attr('class', icon_fill_class);
    });
    request.fail(function (jqXHR, textStatus) {
        _this.find('i').attr('class', icon_class);
    });
});

jQuery(document).on('click', '.careerfy-post-like-btn', function () {

    'use strict';
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var icon_class = 'fa fa-heart-o';
    var icon_fill_class = 'fa fa-heart';
    var this_loader = _this.find('i');
    var this_counter = _this.find('span');
    this_loader.attr('class', 'fa fa-refresh fa-spin');

    var request = $.ajax({
        url: careerfy_funnc_vars.ajax_url,
        method: "POST",
        data: {
            post_id: this_id,
            action: 'careerfy_post_likes_count',
        },
        dataType: "json"
    });
    request.done(function (response) {
        if (typeof response.counter !== 'undefined' && response.counter != '') {
            this_counter.html(response.counter);
        }
        _this.removeAttr('class');
        _this.find('i').attr('class', icon_fill_class);
    });
    request.fail(function (jqXHR, textStatus) {
        _this.find('i').attr('class', icon_class);
    });
});

jQuery(document).on('click', '#employer-detail2-tabs li', function () {
    jQuery('#employer-detail2-tabs > li').removeClass('active');
    jQuery(this).addClass('active');
});

jQuery(document).on('click', '#careerfy-detail5-tabs li', function () {
    jQuery('#careerfy-detail5-tabs > li').removeClass('active');
    jQuery(this).addClass('active');
});

jQuery(document).on('click', '.careerfy-post-dislike-btn', function () {
    'use strict';
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var icon_class = 'fa fa-thumbs-o-up';
    var icon_fill_class = 'fa fa-thumbs-up';
    var this_loader = _this.find('i');
    var this_counter = _this.find('span');
    this_loader.attr('class', 'fa fa-refresh fa-spin');
    var request = $.ajax({
        url: careerfy_funnc_vars.ajax_url,
        method: "POST",
        data: {
            post_id: this_id,
            action: 'careerfy_post_dislikes_count',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.counter !== 'undefined' && response.counter != '') {
            this_counter.html(response.counter);
        }
        _this.removeAttr('class');
        _this.find('i').attr('class', icon_fill_class);
    });

    request.fail(function (jqXHR, textStatus) {
        _this.find('i').attr('class', icon_class);
    });
});

jQuery(document).on('click', '.user-type-btn-sign-up', function () {
    'use strict';
    var _this = jQuery(this),
        this_type = _this.attr('data-type'),
        this_id = $(this).data('id'),
        registration_form = $('#registration-form-' + this_id);
    this_type == 'jobsearch_employer' ? registration_form.find('input[name=pt_user_role]').val('').val('jobsearch_employer') : registration_form.find('input[name=pt_user_role]').val('').val('jobsearch_candidate');

});

if (jQuery('.careerfy-candidate .careerfy-candidate-style8-wrapper').length > 0) {
    jQuery(document).on('click', '.careerfy-candidate .careerfy-candidate-style8-wrapper', function (event) {
        var _this = jQuery(this);
        var this_target = jQuery(event.target);
        var this_target_obj = this_target.get(0);
        var dest_go_to = _this.find('.careerfy-candidate-style8-box1 > a');

        if (this_target.is('a') || this_target.parent('a').length > 0 || this_target_obj.parentNode == null) {
            //do nothing
        } else {
            window.location.href = dest_go_to.attr('href');
        }
    });
}

jQuery(document).on('click', '.sign-up-form-submit', function (e) {
    e.preventDefault();

    var _this = jQuery(this),
        this_id = _this.data('id'),
        registration_form = jQuery('#registration-form-' + this_id),
        pt_user_fullname = registration_form.find('input[name=pt_user_fullname]'),
        pt_user_email = registration_form.find('input[name=pt_user_email]'),
        pt_user_pass = registration_form.find('input[name=pt_user_pass]'),
        pt_user_cpass = registration_form.find('input[name=pt_user_cpass]'),
        pt_user_fname = registration_form.find('input[name=pt_user_fname]'),
        pt_user_lname = registration_form.find('input[name=pt_user_lname]'),
        pt_user_role = registration_form.find('input[name=pt_user_role]'),
        pt_user_organization = registration_form.find('input[name=pt_user_organization]'),
        msg_con = registration_form.find('.registration-errors'),
        loader_con = registration_form.find('.form-loader');

    _form_error = false;

    if (pt_user_fullname.length > 0) {
        if (pt_user_fullname.val() == '') {
            _form_error = true;
            pt_user_fullname.css({border: '1px solid #ff0000'});
        } else {
            pt_user_fullname.css({border: '1px solid #efefef'});
        }
    }
    pt_user_organization.val(pt_user_fullname.val());

    var user_name = pt_user_fullname.val().split(" ");
    pt_user_fname.val(user_name[0]);
    pt_user_lname.val(user_name[1]);

    if (pt_user_email.length > 0) {
        if (pt_user_email.val() == '') {
            _form_error = true;
            pt_user_email.css({border: '1px solid #ff0000'});
        } else {
            pt_user_email.css({border: '1px solid #efefef'});
        }
    }

    if (pt_user_pass.length > 0) {
        if (pt_user_pass.val() == '') {
            _form_error = true;
            pt_user_pass.css({border: '1px solid #ff0000'});
        } else {
            pt_user_pass.css({border: '1px solid #efefef'});
        }
    }

    pt_user_cpass.val(pt_user_pass.val());

    if (!_form_error) {
        var button = $(this).find('button');
        var btn_html = button.html();
        //button.html('loading');
        msg_con.hide();
        _this.addClass('disabled-btn');
        _this.attr('disabled', 'disabled');
        loader_con.show();
        loader_con.html('<i class="fa fa-refresh fa-spin"></i>');


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
})
