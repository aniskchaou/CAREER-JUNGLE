var $ = jQuery;
jQuery(document).ready(function () {
    jQuery(".gal-all-imgs").sortable({
        handle: '.el-drag',
        cursor: 'move',
        items: '.gal-item',
    });

    
    //
    if (jQuery('.overall-site-aplicnts').length > 0) {
        var loding_strng_mkup = '<i class="fa fa-refresh fa-spin"></i>';
        jQuery('.overall-site-aplicnts').html(loding_strng_mkup);
        jQuery('.overall-site-shaplicnts').html(loding_strng_mkup);
        jQuery('.overall-site-rejaplicnts').html(loding_strng_mkup);
    }
});

jQuery(document).on('click', '.jobsearch-delete-followin-emp', function () {
    var _this = jQuery(this);
    var uid = _this.attr('data-id');
    var loader_con = _this.find('i');

    loader_con.attr('class', 'fa fa-refresh fa-spin');
    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            'emp_id': uid,
            'action': 'jobsearch_userdash_rem_emp_followin'
        },
        dataType: "json"
    });
    request.done(function (response) {
        if (typeof response.success !== 'undefined' && response.success == '1') {
            _this.parents('li').slideUp();
            var doin_refresh = setInterval(function () {
                window.location.reload(true);
                clearInterval(doin_refresh);
            }, 500);
        }
        loader_con.attr('class', 'jobsearch-icon jobsearch-rubbish');
    });

    request.fail(function (jqXHR, textStatus) {
        loader_con.attr('class', 'jobsearch-icon jobsearch-rubbish');
    });
});

jQuery(document).on('click', '.user-dashthumb-remove', function () {
    var _this = jQuery(this);
    var uid = _this.attr('data-uid');
    var loader_con = _this.find('i');

    loader_con.attr('class', 'fa fa-refresh fa-spin');
    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            'user_id': uid,
            'action': 'jobsearch_userdash_profile_delete_pthumb'
        },
        dataType: "json"
    });
    request.done(function (response) {
        if (typeof response.success !== 'undefined' && response.success == '1') {
            _this.hide();
            jQuery('#com-img-holder').find('img').attr('src', response.img_url);
        }
        loader_con.attr('class', 'fa fa-times');
    });

    request.fail(function (jqXHR, textStatus) {
        loader_con.attr('class', 'fa fa-times');
    });
});

jQuery('.lodmore-notific-btn').on('click', function (e) {
    e.preventDefault();
    var _this = jQuery(this),
            total_pages = _this.attr('data-tpages'),
            page_num = _this.attr('data-gtopage'),
            this_html = _this.html(),
            appender_con = jQuery('.jobsearch-dashnotifics-list');
    if (!_this.hasClass('ajax-loadin')) {
        _this.addClass('ajax-loadin');
        _this.html(this_html + ' <i class="fa fa-refresh fa-spin"></i>');

        total_pages = parseInt(total_pages);
        page_num = parseInt(page_num);
        var request = jQuery.ajax({
            url: jobsearch_dashboard_vars.ajax_url,
            method: "POST",
            data: {
                page_num: page_num,
                action: 'jobsearch_load_more_userdash_notifics',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if ('undefined' !== typeof response.html) {
                page_num += 1;
                _this.attr('data-gtopage', page_num);
                if (page_num > total_pages) {
                    _this.parent('div').hide();
                }
                appender_con.append(response.html);
            }
            _this.html(this_html);
            _this.removeClass('ajax-loadin');
        });

        request.fail(function (jqXHR, textStatus) {
            _this.html(this_html);
            _this.removeClass('ajax-loadin');
        });
    }
    return false;

});

function jobsearch_dashboard_read_file_url(input) {

    if (input.files && input.files[0]) {

        var loader_con = jQuery('#user_avatar').parents('figcaption').find('.fileUpLoader');

        var img_file = input.files[0];
        var img_size = img_file.size;
        var pphot_size_allow = jobsearch_dashboard_vars.pphot_size_allow;
        pphot_size_allow = parseInt(pphot_size_allow);

        img_size = parseFloat(img_size / 1024).toFixed(2);

        if (img_size <= pphot_size_allow) {
            loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
            var formData = new FormData();
            formData.append('avatar_file', img_file);
            formData.append('action', 'jobsearch_dashboard_updating_user_avatar_img');

            var request = $.ajax({
                url: jobsearch_dashboard_vars.ajax_url,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json"
            });
            request.done(function (response) {
                if (typeof response.err_msg !== 'undefined' && response.err_msg != '') {
                    loader_con.html(response.err_msg);
                    return false;
                }
                if (typeof response.imgUrl !== 'undefined') {
                    jQuery('#com-img-holder').find('img').attr('src', response.imgUrl);
                    jQuery('.user-dashthumb-remove').show();
                }
                loader_con.html('');
            });

            request.fail(function (jqXHR, textStatus) {
                loader_con.html(jobsearch_dashboard_vars.error_msg);
                loader_con.html('');
            });

        } else {
            alert(jobsearch_dashboard_vars.pphot_size_err);
        }
    }
}

jQuery(document).on('change', '#user_avatar', function () {
    jobsearch_dashboard_read_file_url(this);
});

function jobsearch_dashboard_emp_avatar_url(input) {

    if (input.files && input.files[0]) {

        var loader_con = jQuery('#employer_user_avatar').parents('figcaption').find('.fileUpLoader');

        var img_file = input.files[0];
        var img_size = img_file.size;
        var pphot_size_allow = jobsearch_dashboard_vars.pphot_size_allow;
        pphot_size_allow = parseInt(pphot_size_allow);

        img_size = parseFloat(img_size / 1024).toFixed(2);

        if (img_size <= pphot_size_allow) {
            loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
            
            var imag_reader = new FileReader();
            imag_reader.readAsDataURL(img_file);
            imag_reader.onload = function (e) {

                var obj_image = new Image();

                //Set the Base64 string return from FileReader as source.
                obj_image.src = e.target.result;

                //Validate the File Height and Width.
                obj_image.onload = function () {
                    var img_height = this.height;
                    var img_width = this.width;
                    
                    if (parseInt(img_height) > 250 || parseInt(img_width) > 250) {
                        alert(jobsearch_dashboard_vars.empphot_higwid_err);
                        loader_con.html('');
                        return false;
                    }
                    
                    var formData = new FormData();
                    formData.append('avatar_file', img_file);
                    formData.append('action', 'jobsearch_dashboard_updating_user_avatar_img');

                    var request = $.ajax({
                        url: jobsearch_dashboard_vars.ajax_url,
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: "json"
                    });
                    request.done(function (response) {
                        if (typeof response.err_msg !== 'undefined' && response.err_msg != '') {
                            loader_con.html(response.err_msg);
                            return false;
                        }
                        if (typeof response.imgUrl !== 'undefined') {
                            jQuery('#com-img-holder').find('img').attr('src', response.imgUrl);
                            jQuery('.user-dashthumb-remove').show();
                        }
                        loader_con.html('');
                    });

                    request.fail(function (jqXHR, textStatus) {
                        loader_con.html(jobsearch_dashboard_vars.error_msg);
                        loader_con.html('');
                    });
                }
            }

        } else {
            alert(jobsearch_dashboard_vars.pphot_size_err);
        }
    }
}

jQuery(document).on('change', '#employer_user_avatar', function () {
    jobsearch_dashboard_emp_avatar_url(this);
});

jQuery(document).on('change', '.opt_notific_setcheckbtn', function () {
    var _this = jQuery(this),
            this_loder = _this.parents('.jobsearch-onoffswitch-outer').find('.opt-notific-lodr'),
            this_val = 'no',
            this_type = _this.attr('data-type');
    if (_this.is(":checked")) {
        this_val = 'yes';
    }
    if (!_this.hasClass('ajax-loadin')) {
        _this.addClass('ajax-loadin');
        this_loder.html('<i class="fa fa-refresh fa-spin"></i>');

        var request = jQuery.ajax({
            url: jobsearch_dashboard_vars.ajax_url,
            method: "POST",
            data: {
                notific_val: this_val,
                notific_type: this_type,
                action: 'jobsearch_chekunchk_notific_setin',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.update !== 'undefined' && response.update == '1') {
                _this.removeClass('ajax-loadin');
                this_loder.html('<i class="fa fa-check"></i>');
                return false;
            }
            this_loder.html('');
            _this.removeClass('ajax-loadin');
        });

        request.fail(function (jqXHR, textStatus) {
            this_loder.html('');
            _this.removeClass('ajax-loadin');
        });
    }
    return false;
});

jQuery(document).on('click', '.jobsearch-pckg-mordetail', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    if (_this.hasClass('open-detbox')) {
        jQuery('#packge-detail-box' + this_id).slideUp();
        _this.removeClass('open-detbox');
        _this.html(_this.attr('data-mtxt') + ' <i class="fa fa-angle-right"></i>');
    } else {
        jQuery('.packge-detail-sepbox').hide();
        jQuery('#packge-detail-box' + this_id).slideDown();
        
        jQuery('.jobsearch-pckg-mordetail').removeClass('open-detbox');
        jQuery('.jobsearch-pckg-mordetail').html(_this.attr('data-mtxt') + ' <i class="fa fa-angle-right"></i>');
        
        _this.addClass('open-detbox');
        _this.html(_this.attr('data-ctxt') + ' <i class="fa fa-angle-up"></i>');
    }
});

jQuery(document).on('click', '.notifics-showlist-tobtn', function () {
    jQuery(this).hide();
    jQuery('.notifics-showsetings-tobtn').removeAttr('style');
    jQuery('.jobsearch-notifics-setopts').hide();
    jQuery('.jobsearch-notifics-loistitms').removeAttr('style');
});

jQuery(document).on('click', '.notifics-showsetings-tobtn', function () {
    jQuery(this).hide();
    jQuery('.notifics-showlist-tobtn').removeAttr('style');
    jQuery('.jobsearch-notifics-loistitms').hide();
    jQuery('.jobsearch-notifics-setopts').removeAttr('style');
});

jQuery(document).on('click', '.readmore-notific-btn', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var readmore_txt = _this.attr('data-readm');
    //var readless_txt = _this.attr('data-readl');
    
    var readmode_type = 'readin_more';
    if (_this.hasClass('btn-readless-mode')) {
        readmode_type = 'readin_less';
    }
    
    var this_txt = _this.html();

    _this.html('<i class="fa fa-refresh fa-spin"></i>');
    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            'item_id': this_id,
            'readm_type': readmode_type,
            'action': 'jobsearch_userdash_notific_readmark_act'
        },
        dataType: "json"
    });
    request.done(function (response) {
        if (typeof response.success !== 'undefined' && response.success == '1') {
            if (_this.parents('.jobsearch-notification-item').hasClass('unread-notific')) {
                _this.parents('.jobsearch-notification-item').removeClass('unread-notific').addClass('read-notific');
            }
            if (_this.hasClass('btn-readmore-mode')) {
                //_this.html(readless_txt);
                _this.html('');
                _this.removeClass('btn-readmore-mode').addClass('btn-readless-mode');
            } else {
                _this.html(readmore_txt);
                _this.removeClass('btn-readless-mode').addClass('btn-readmore-mode');
            }
            if (typeof response.msg !== 'undefined' && response.msg != '') {
                _this.parent('strong').find('.notific-onlmsg-con').html(response.msg);
            }
            if (typeof response.count !== 'undefined' && response.count !== '') {
                jQuery('.hder-notifics-count > small').html(response.count);
                jQuery('.hderbell-notifics-count').html(response.count);
            }
            return false;
        }
        _this.html(this_txt);
    });

    request.fail(function (jqXHR, textStatus) {
        _this.html(this_txt);
    });
});

jQuery(document).on('click', '.close-notific-item', function () {
    var _this = jQuery(this),
            this_loder = _this.find('i'),
            this_loder_clas = 'fa fa-close',
            this_id = _this.attr('data-id');
    
    if (!_this.hasClass('ajax-loadin')) {
        _this.addClass('ajax-loadin');
        this_loder.attr('class', 'fa fa-refresh fa-spin');

        var request = jQuery.ajax({
            url: jobsearch_dashboard_vars.ajax_url,
            method: "POST",
            data: {
                notific_id: this_id,
                action: 'jobsearch_close_notific_item_check',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.close !== 'undefined' && response.close == '1') {
                _this.removeClass('ajax-loadin');
                _this.parents('.jobsearch-notification-item').slideUp();
            }
            if (typeof response.count !== 'undefined' && response.count !== '') {
                jQuery('.hder-notifics-count > small').html(response.count);
                jQuery('.hderbell-notifics-count').html(response.count);
            }
            this_loder.attr('class', this_loder_clas);
            _this.removeClass('ajax-loadin');
        });

        request.fail(function (jqXHR, textStatus) {
            this_loder.attr('class', this_loder_clas);
            _this.removeClass('ajax-loadin');
        });
    }
    return false;
});

jQuery(document).on('click', '.jobsearch-userdel-profilebtn', function () {
    jobsearch_modal_popup_open('JobSearchModalUserProfileDel');
});

jQuery(document).on('click', '.jobsearch-userdel-profile', function () {

    var this_form = jQuery(this).parents('.jobsearch-user-profiledel-pop');
    var get_terr_val = jobsearch_accept_terms_cond_pop(this_form);
    if (get_terr_val != 'yes') {
        return false;
    }
    var loader_con = jQuery(this).parents('.profile-del-con').find('.loader-con');
    var msg_con = jQuery(this).parents('.profile-del-con').find('.msge-con');

    loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
    var typeu = jQuery(this).attr('data-type');
    var u_pass = jQuery(this).parents('.profile-del-con').find('#d_user_pass');
    var request = $.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            'type': typeu,
            'u_pass': u_pass.val(),
            'action': 'jobsearch_user_profile_delete_for'
        },
        dataType: "json"
    });
    request.done(function (response) {
        if (typeof response.success !== 'undefined' && response.success == '1') {
            msg_con.html(response.msg);
            var doin_refresh = setInterval(function () {
                window.location.reload(true);
                clearInterval(doin_refresh);
            }, 2000);
        } else {
            msg_con.html(response.msg);
        }
        loader_con.html('');
    });

    request.fail(function (jqXHR, textStatus) {
        loader_con.html('');
    });
});

function jobsearch_dashboard_read_cover_photo_url(input) {

    if (input.files && input.files[0]) {

        var loader_con = jQuery('#user_cvr_photo').parents('figcaption').find('.file-loader');

        var img_file = input.files[0];
        var img_size = img_file.size;

        var cvrphot_size_allow = jobsearch_dashboard_vars.cvrphot_size_allow;
        cvrphot_size_allow = parseInt(cvrphot_size_allow);

        img_size = parseFloat(img_size / 1024).toFixed(2);

        if (img_size <= cvrphot_size_allow) {
            loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
            var formData = new FormData();
            formData.append('user_cvr_photo', img_file);
            formData.append('action', 'jobsearch_dashboard_updating_employer_cover_img');

            var request = $.ajax({
                url: jobsearch_dashboard_vars.ajax_url,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json"
            });
            request.done(function (response) {
                if (typeof response.err_msg !== 'undefined' && response.err_msg != '') {
                    loader_con.html(response.err_msg);
                    return false;
                }
                if (typeof response.imgUrl !== 'undefined') {
                    jQuery('#com-cvrimg-holder').find('span').attr('style', "background:url(" + response.imgUrl + ") no-repeat center/cover;");
                    jQuery('.jobsearch-employer-cvr-img').find('.img-cont-sec').show();
                }
                loader_con.html('');
            });

            request.fail(function (jqXHR, textStatus) {
                loader_con.html(jobsearch_dashboard_vars.error_msg);
                loader_con.html('');
            });

        } else {
            alert(jobsearch_dashboard_vars.cvrphot_size_err);
        }
    }
}
function jobsearch_dashboard_read_cover_photo_url_cand(input) {

    if (input.files && input.files[0]) {

        var loader_con = jQuery('#user_cvr_photo_cand').parents('figcaption').find('.file-loader');

        var img_file = input.files[0];
        var img_size = img_file.size;

        var cvrphot_size_allow = jobsearch_dashboard_vars.cvrphot_size_allow;
        cvrphot_size_allow = parseInt(cvrphot_size_allow);

        img_size = parseFloat(img_size / 1024).toFixed(2);

        if (img_size <= cvrphot_size_allow) {
            loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
            var formData = new FormData();
            formData.append('user_cvr_photo_cand', img_file);
            formData.append('action', 'jobsearch_dashboard_updating_candidate_cover_img');

            var request = $.ajax({
                url: jobsearch_dashboard_vars.ajax_url,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json"
            });
            request.done(function (response) {
                if (typeof response.err_msg !== 'undefined' && response.err_msg != '') {
                    loader_con.html(response.err_msg);
                    return false;
                }
                if (typeof response.imgUrl !== 'undefined') {

                    jQuery('#com-cvrimg-holder').find('span').attr('style', "background:url(" + response.imgUrl + ") no-repeat center/cover;");

                    jQuery('.jobsearch-employer-cvr-img').find('.img-cont-sec').show();
                }
                loader_con.html('');
            });

            request.fail(function (jqXHR, textStatus) {
                loader_con.html(jobsearch_dashboard_vars.error_msg);
                loader_con.html('');
            });

        } else {
            alert(jobsearch_dashboard_vars.cvrphot_size_err);
        }
    }
}
jQuery(document).on('change', '#user_cvr_photo', function () {
    jobsearch_dashboard_read_cover_photo_url(this);
});
jQuery(document).on('change', '#user_cvr_photo_cand', function () {
    jobsearch_dashboard_read_cover_photo_url_cand(this);
});

jQuery(document).on('click', '.candidate-remove-coverimg', function () {
    var _this = jQuery(this);
    var this_loader = _this.find('i');

    this_loader.attr('class', 'fa fa-refresh fa-spin');
    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            cover_img: 'remove',
            action: 'jobsearch_candidate_cover_img_remove',
        },
        dataType: "json"
    });

    request.done(function (response) {
        this_loader.attr('class', 'fa fa-times');
        _this.parent('.img-cont-sec').hide();
        _this.parents('.jobsearch-employer-cvr-img').find('figure img').attr('src', '');
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.attr('class', 'fa fa-times');
    });
});

jQuery(document).on('click', '.employer-remove-coverimg', function () {
    var _this = jQuery(this);
    var this_loader = _this.find('i');

    this_loader.attr('class', 'fa fa-refresh fa-spin');
    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            cover_img: 'remove',
            action: 'jobsearch_employer_cover_img_remove',
        },
        dataType: "json"
    });

    request.done(function (response) {
        this_loader.attr('class', 'fa fa-times');
        _this.parent('.img-cont-sec').hide();
        _this.parents('.jobsearch-employer-cvr-img').find('figure img').attr('src', '');
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.attr('class', 'fa fa-times');
    });
});

function jobsearch_dashboard_cv_upload_url(input) {

    if (input.files && input.files[0]) {

        var loader_con = jQuery('#jobsearch-upload-cv-main').find('.fileUpLoader');

        var cv_file = input.files[0];
        var file_size = cv_file.size;
        var file_type = cv_file.type;
        var file_name = cv_file.name;
        jQuery('#jobsearch-uploadfile').attr('placeholder', file_name);
        jQuery('#jobsearch-uploadfile').val(file_name);

        var allowed_types = jobsearch_dashboard_vars.cvdoc_file_types;

        file_size = parseFloat(file_size / 1024).toFixed(2);
        var filesize_allow = jobsearch_dashboard_vars.cvfile_size_allow;
        filesize_allow = parseInt(filesize_allow);

        if (file_size <= filesize_allow) {
            if (allowed_types.indexOf(file_type) >= 0) {
                loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
                var formData = new FormData();
                formData.append('candidate_cv_file', cv_file);
                formData.append('action', 'jobsearch_dashboard_updating_candidate_cv_file');
                console.info(formData);
                var request = $.ajax({
                    url: jobsearch_dashboard_vars.ajax_url,
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json"
                });
                request.done(function (response) {
                    if (typeof response.err_msg !== 'undefined' && response.err_msg != '') {
                        loader_con.html(response.err_msg);
                        return false;
                    }
                    if (typeof response.filehtml !== 'undefined' && response.filehtml != '') {
                        if (jobsearch_dashboard_vars.multiple_cvs_allow == 'on') {
                            jQuery('#com-file-holder').append(response.filehtml);
                            window.location.reload(true);
                        } else {
                            jQuery('#com-file-holder').html(response.filehtml);
                            jQuery('#com-file-holder').find('.jobsearch-cv-manager-list').slideDown();
                            jQuery('#jobsearch-upload-cv-main').slideUp();
                        }
                    }
                    loader_con.html('');
                });

                request.fail(function (jqXHR, textStatus) {
                    loader_con.html(jobsearch_dashboard_vars.error_msg);
                    loader_con.html('');
                });
            } else {
                alert(jobsearch_dashboard_vars.cv_file_types);
            }

        } else {
            alert(jobsearch_dashboard_vars.cvfile_size_err);
        }
    }
}

jQuery(document).on('change', 'input[name="candidate_cv_file"]', function () {
    jobsearch_dashboard_cv_upload_url(this);
});

jQuery(document).on('click', '.user-dashboard-ajax-click', function () {
    var _this = jQuery(this);
    var dashboard_user_type = _this.attr('data-user-type');
    var dashboard_part = _this.attr('data-ajax-part');
    var dashboard_tab = _this.attr('data-ajax-tab');
    var dashboard_loader = jQuery('.user-dashboard-loader');

    var dashboard_url = jobsearch_dashboard_vars.dashboard_url;

    dashboard_loader.html('Loading...');
    dashboard_loader.show();
    if (_this.hasClass('has-loaded')) {
        var load_interval = setInterval(function () {

            if (dashboard_url.indexOf('?') != -1) {
                dashboard_url = dashboard_url + '&' + 'tab=' + dashboard_part;
            } else {
                dashboard_url = dashboard_url + '?' + 'tab=' + dashboard_part;
            }

            dashboard_loader.html('');
            dashboard_loader.hide();
            jQuery('.main-tab-section').hide();
            jQuery('#' + dashboard_tab).show();

            //
            _this.parents('ul').find('li').removeClass('active');
            _this.parents('li').addClass('active');
            //

            if (typeof history !== 'undefined' && history.pushState) {
                history.pushState({}, null, dashboard_url);
            }

            clearInterval(load_interval);
        }, 500);
    } else {
        var request = $.ajax({
            url: jobsearch_dashboard_vars.ajax_url,
            method: "POST",
            data: {
                'user_type': dashboard_user_type,
                'template_name': dashboard_part,
                'action': 'jobsearch_user_dashboard_show_template',
            },
            dataType: "json"
        });

        if (dashboard_url.indexOf('?') != -1) {
            dashboard_url = dashboard_url + '&' + 'tab=' + dashboard_part;
        } else {
            dashboard_url = dashboard_url + '?' + 'tab=' + dashboard_part;
        }

        request.done(function (response) {
            if (typeof response.template_html !== 'undefined') {
                dashboard_loader.html('');
                dashboard_loader.hide();
                jQuery('.main-tab-section').hide();
                jQuery('#' + dashboard_tab).html(response.template_html);
                jQuery('#' + dashboard_tab).show();
                //
                _this.parents('ul').find('li').removeClass('active');
                _this.parents('li').addClass('active');
                //
                _this.addClass('has-loaded');
                if (typeof history !== 'undefined' && history.pushState) {
                    history.pushState({}, null, dashboard_url);
                }
            }
        });

        request.fail(function (jqXHR, textStatus) {
            dashboard_loader.html('');
            dashboard_loader.hide();
        });
    }
});

jQuery(document).on('click', '.jobsearch-trash-job', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    if (this_id > 0) {
        var conf = confirm(jobsearch_dashboard_vars.are_you_sure);
        if (conf) {
            _this.removeClass('jobsearch-icon');
            _this.removeClass('jobsearch-trash-job');
            _this.addClass('fa fa-refresh fa-spin');
            var request = jQuery.ajax({
                url: jobsearch_dashboard_vars.ajax_url,
                method: "POST",
                data: {
                    'job_id': this_id,
                    'action': 'jobsearch_user_dashboard_job_delete',
                },
                dataType: "json"
            });

            request.done(function (response) {
                _this.addClass('jobsearch-icon');
                _this.addClass('jobsearch-trash-job');
                _this.removeClass('fa fa-refresh fa-spin');
                if (typeof response.err_msg !== 'undefined' && response.err_msg != '') {
                    _this.removeClass('jobsearch-trash-job').html(response.err_msg);
                    return false;
                }
                if (typeof response.msg !== 'undefined' && response.msg == 'deleted') {
                    _this.parents('.jobsearch-mangjobs-list-inner').find('.jobsearch-recent-applicants-nav').fadeOut();
                    _this.parents('.jobsearch-mangjobs-list-inner').find('.jobsearch-managejobs-tbody').fadeOut();
                    window.location.reload();
                }
            });

            request.fail(function (jqXHR, textStatus) {
                _this.addClass('jobsearch-trash-job');
            });
        }
    }
});

jQuery(document).on('click', '.jobsearch-del-user-cv', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    if (this_id != '') {
        var conf = confirm(jobsearch_dashboard_vars.are_you_sure);
        if (conf) {
            _this.find('i').attr('class', 'fa fa-refresh fa-spin');
            var request = jQuery.ajax({
                url: jobsearch_dashboard_vars.ajax_url,
                method: "POST",
                data: {
                    'attach_id': this_id,
                    'action': 'jobsearch_act_user_cv_delete',
                },
                dataType: "json"
            });

            request.done(function (response) {
                if (typeof response.err_msg !== 'undefined' && response.err_msg != '') {
                    _this.find('i').removeAttr('class').html(response.err_msg);
                    return false;
                }
                _this.parents('.jobsearch-cv-manager-list').slideUp();
                jQuery('#jobsearch-upload-cv-main').slideDown();
                window.location.reload(true);
            });

            request.fail(function (jqXHR, textStatus) {
                _this.parents('.jobsearch-cv-manager-list').slideUp();
            });
        }
    }
});

jQuery(document).on('click', '.jobsearch-delete-fav-job', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_loader = jQuery(this).find('i');

    var this_loader_b_icon = this_loader.attr('class');

    this_loader.attr('class', 'fa fa-refresh fa-spin');
    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            job_id: this_id,
            action: 'jobsearch_remove_user_fav_job_from_list',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.err_msg !== 'undefined' && response.err_msg != '') {
            this_loader.removeAttr('class').html(response.err_msg);
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {

            _this.parents('tr').fadeOut();
            this_loader.attr('class', this_loader_b_icon);
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.attr('class', this_loader_b_icon);
        this_loader.html(jobsearch_dashboard_vars.error_msg);
    });
});

jQuery(document).on('click', '.jobsearch-delete-applied-job', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_key = _this.attr('data-key');
    var this_loader = jQuery(this).find('i');

    var this_loader_b_icon = this_loader.attr('class');

    this_loader.attr('class', 'fa fa-refresh fa-spin');
    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            job_id: this_id,
            job_key: this_key,
            action: 'jobsearch_remove_user_applied_job_from_list',
        },
        dataType: "json"
    });

    request.done(function (response) {

        if (typeof response.err_msg !== 'undefined' && response.err_msg != '') {
            this_loader.removeAttr('class').html(response.err_msg);
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {

            _this.parents('li').fadeOut();
            this_loader.attr('class', this_loader_b_icon);
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.attr('class', this_loader_b_icon);
        this_loader.html(jobsearch_dashboard_vars.error_msg);
    });
});

//
(function ($) {
    "use strict";
    $.fn.jobsearch_req_field_loop = function (callback, thisArg) {
        var me = this;
        return this.each(function (index, element) {
            return callback.call(thisArg || element, element, index, me);
        });
    };
})(jQuery);

function jobsearch_validate_dashboard_form(that) {
    "use strict";
    var req_class = 'jobsearch-req-field',
            _this_form = $(that),
            form_validity = 'valid';

    _this_form.find('.' + req_class).jobsearch_req_field_loop(function (element, index, set) {

        var eror_str = '';
        if ($(element).val() == '') {
            form_validity = 'invalid';
            eror_str = 'has_error';
        } else {
            $(element).css({"border": "1px solid #eceeef"});
        }

        if (eror_str != '') {
            $(element).css({"border": "1px solid #ff0000"});
        }
    });

    if (form_validity == 'valid') {
        return true;
    } else {
        return false;
    }
}
//

function jobsearch_cand_dash_resume_odd_workings_clbk() {
    var resume_form = jQuery('#jobsearch-candidate-resumesub');
}

jQuery(document).on('click', '#add-education-btn', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var this_pcon = _this.parents('.jobsearch-add-resume-item-popup');
    jobsearch_validate_dashboard_form(this_pcon);

    var this_loader = _this.parent('li').find('.edu-loding-msg');

    var title = jQuery('#add-edu-title');
    var start_date = jQuery('#add-edu-date-start');
    var end_date = jQuery('#add-edu-date-end');
    var present_date = jQuery('#add-edu-date-prsent');
    var institute = jQuery('#add-edu-institute');
    var desc = jQuery('#add-edu-desc');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.css({'background-color': '#32cd32'});
    this_loader.show();

    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            title: title.val(),
            start_date: start_date.val(),
            end_date: end_date.val(),
            present_date: present_date.val(),
            institute: institute.val(),
            desc: desc.val(),
            action: 'jobsearch_add_resume_education_to_list',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);
            this_loader.css({'background-color': '#e40000'});
            return false;
        }
        if (typeof response.apender_msg !== 'undefined' && response.apender_msg != '') {
            this_loader.append(response.apender_msg);

            if (typeof response.html !== 'undefined' && response.html != '') {
                jQuery('#jobsearch-resume-edu-con').find('>ul').append(response.html);
                jobsearch_cand_dash_resume_odd_workings_clbk();
            } else {
                return false;
            }

            title.val('');
            start_date.val('');
            end_date.val('');
            institute.val('');
            desc.val('');

            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);

            if (typeof response.html !== 'undefined' && response.html != '') {
                jQuery('#jobsearch-resume-edu-con').find('>ul').append(response.html);
                jobsearch_cand_dash_resume_odd_workings_clbk();
            } else {
                return false;
            }

            title.val('');
            start_date.val('');
            end_date.val('');
            institute.val('');
            desc.val('');

            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_dashboard_vars.error_msg);
        this_loader.css({'background-color': '#e40000'});
    });
});

jQuery(document).on('click', '#add-experience-btn', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var this_pcon = _this.parents('.jobsearch-add-resume-item-popup');
    jobsearch_validate_dashboard_form(this_pcon);

    var this_loader = _this.parent('li').find('.expr-loding-msg');

    var title = jQuery('#add-expr-title');
    var start_date = jQuery('#add-expr-date-start');
    var end_date = jQuery('#add-expr-date-end');
    var present_date = jQuery('#add-expr-date-prsent');
    var company = jQuery('#add-expr-company');
    var desc = jQuery('#add-expr-desc');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.css({'background-color': '#32cd32'});
    this_loader.show();

    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            title: title.val(),
            start_date: start_date.val(),
            end_date: end_date.val(),
            present_date: present_date.val(),
            company: company.val(),
            desc: desc.val(),
            action: 'jobsearch_add_resume_experience_to_list',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);
            this_loader.css({'background-color': '#e40000'});
            return false;
        }
        if (typeof response.apender_msg !== 'undefined' && response.apender_msg != '') {
            this_loader.append(response.apender_msg);

            if (typeof response.html !== 'undefined' && response.html != '') {
                jQuery('#jobsearch-resume-expr-con').find('>ul').append(response.html);
                jobsearch_cand_dash_resume_odd_workings_clbk();
            } else {
                return false;
            }

            title.val('');
            start_date.val('');
            end_date.val('');
            company.val('');
            desc.val('');

            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);

            if (typeof response.html !== 'undefined' && response.html != '') {
                jQuery('#jobsearch-resume-expr-con').find('>ul').append(response.html);
                jobsearch_cand_dash_resume_odd_workings_clbk();
            } else {
                return false;
            }

            title.val('');
            start_date.val('');
            end_date.val('');
            company.val('');
            desc.val('');

            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_dashboard_vars.error_msg);
        this_loader.css({'background-color': '#e40000'});
    });
});

jQuery(document).on('click', '#add-resume-skills-btn', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var this_pcon = _this.parents('.jobsearch-add-resume-item-popup');
    jobsearch_validate_dashboard_form(this_pcon);

    var this_loader = _this.parent('li').find('.skills-loding-msg');

    var title = jQuery('#add-skill-title');
    var skill_percentage = jQuery('#add-skill-percentage');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.css({'background-color': '#32cd32'});
    this_loader.show();

    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            title: title.val(),
            skill_percentage: skill_percentage.val(),
            action: 'jobsearch_add_resume_skill_to_list',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);
            this_loader.css({'background-color': '#e40000'});
            return false;
        }
        if (typeof response.apender_msg !== 'undefined' && response.apender_msg != '') {
            this_loader.append(response.apender_msg);

            if (typeof response.html !== 'undefined' && response.html != '') {
                jQuery('#jobsearch-resume-skills-con').find('>ul').append(response.html);
                jobsearch_cand_dash_resume_odd_workings_clbk();
            } else {
                return false;
            }

            title.val('');
            skill_percentage.val('');

            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);

            if (typeof response.html !== 'undefined' && response.html != '') {
                jQuery('#jobsearch-resume-skills-con').find('>ul').append(response.html);
                jobsearch_cand_dash_resume_odd_workings_clbk();
            } else {
                return false;
            }

            title.val('');
            skill_percentage.val('');

            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_dashboard_vars.error_msg);
        this_loader.css({'background-color': '#e40000'});
    });
});

jQuery(document).on('click', '#add-resume-langs-btn', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var this_pcon = _this.parents('.jobsearch-add-resume-item-popup');
    jobsearch_validate_dashboard_form(this_pcon);

    var this_loader = _this.parent('li').find('.langs-loding-msg');

    var title = jQuery('#add-lang-title');
    var lang_level = jQuery('#add-lang-level');
    var lang_percentage = jQuery('#add-lang-percentage');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.css({'background-color': '#32cd32'});
    this_loader.show();

    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            title: title.val(),
            lang_level: lang_level.val(),
            lang_percentage: lang_percentage.val(),
            action: 'jobsearch_add_resume_lang_to_list',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);
            this_loader.css({'background-color': '#e40000'});
            return false;
        }
        if (typeof response.apender_msg !== 'undefined' && response.apender_msg != '') {
            this_loader.append(response.apender_msg);

            if (typeof response.html !== 'undefined' && response.html != '') {
                jQuery('#jobsearch-resume-langs-con').find('>ul').append(response.html);
                jobsearch_cand_dash_resume_odd_workings_clbk();
            } else {
                return false;
            }

            title.val('');
            lang_percentage.val('');

            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);

            if (typeof response.html !== 'undefined' && response.html != '') {
                jQuery('#jobsearch-resume-langs-con').find('>ul').append(response.html);
                jobsearch_cand_dash_resume_odd_workings_clbk();
            } else {
                return false;
            }

            title.val('');
            lang_percentage.val('');

            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_dashboard_vars.error_msg);
        this_loader.css({'background-color': '#e40000'});
    });
});

jQuery(document).on('click', '#add-resume-awards-btn', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var this_pcon = _this.parents('.jobsearch-add-resume-item-popup');
    jobsearch_validate_dashboard_form(this_pcon);

    var this_loader = _this.parent('li').find('.awards-loding-msg');

    var title = jQuery('#add-award-title');
    var award_year = jQuery('#add-award-year');
    var award_desc = jQuery('#add-award-desc');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.css({'background-color': '#32cd32'});
    this_loader.show();

    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            title: title.val(),
            award_year: award_year.val(),
            award_desc: award_desc.val(),
            action: 'jobsearch_add_resume_award_to_list',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);
            this_loader.css({'background-color': '#e40000'});
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);

            if (typeof response.html !== 'undefined' && response.html != '') {
                jQuery('#jobsearch-resume-awards-con').find('>ul').append(response.html);
                jobsearch_cand_dash_resume_odd_workings_clbk();
            } else {
                return false;
            }

            title.val('');
            award_year.val('');
            award_desc.val('');

            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_dashboard_vars.error_msg);
        this_loader.css({'background-color': '#e40000'});
    });
});

jQuery(document).on('click', '#add-resume-portfolio-btn', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var this_pcon = _this.parents('.jobsearch-add-resume-item-popup');
    jobsearch_validate_dashboard_form(this_pcon);

    var this_loader = _this.parent('li').find('.portfolio-loding-msg');

    var title = jQuery('#add-portfolio-title');
    var portfolio_img = jQuery('#add-portfolio-img-input');
    var portfolio_url = jQuery('#add-portfolio-url');
    var portfolio_vurl = jQuery('#add-portfolio-vurl');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.css({'background-color': '#32cd32'});
    this_loader.show();

    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            title: title.val(),
            portfolio_img: portfolio_img.val(),
            portfolio_url: portfolio_url.val(),
            portfolio_vurl: portfolio_vurl.val(),
            action: 'jobsearch_add_resume_portfolio_to_list',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);

            this_loader.css({'background-color': '#e40000'});
            return false;
        }
        if (typeof response.apender_msg !== 'undefined' && response.apender_msg != '') {
            this_loader.append(response.apender_msg);

            if (typeof response.html !== 'undefined' && response.html != '') {
                jQuery('#jobsearch-resume-portfolio-con').find('>ul').append(response.html);
                jobsearch_cand_dash_resume_odd_workings_clbk();
            } else {
                return false;
            }

            title.val('');
            portfolio_url.val('');
            portfolio_vurl.val('');
            portfolio_img.val('');
            if (portfolio_img.parents('.upload-img-holder-sec').find('img').length > 0) {
                portfolio_img.parents('.upload-img-holder-sec').find('img').attr('src', '');
            }

            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);

            if (typeof response.html !== 'undefined' && response.html != '') {
                jQuery('#jobsearch-resume-portfolio-con').find('>ul').append(response.html);
                jobsearch_cand_dash_resume_odd_workings_clbk();
            } else {
                return false;
            }

            title.val('');
            portfolio_url.val('');
            portfolio_vurl.val('');
            portfolio_img.val('');
            if (portfolio_img.parents('.upload-img-holder-sec').find('img').length > 0) {
                portfolio_img.parents('.upload-img-holder-sec').find('img').attr('src', '');
            }

            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_dashboard_vars.error_msg);
        this_loader.css({'background-color': '#e40000'});
    });
});

jQuery(document).on('click', '#add-team-member-btn', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var this_pcon = _this.parents('.jobsearch-add-resume-item-popup');
    jobsearch_validate_dashboard_form(this_pcon);

    var this_loader = _this.parent('li').find('.portfolio-loding-msg');

    var title = jQuery('#team_title');
    var portfolio_img = jQuery('#team_image_input');
    var team_designation = jQuery('#team_designation');
    var team_experience = jQuery('#team_experience');
    var team_facebook = jQuery('#team_facebook');
    var team_google = jQuery('#team_google');
    var team_twitter = jQuery('#team_twitter');
    var team_linkedin = jQuery('#team_linkedin');
    var team_description = jQuery('#team_description');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.css({'background-color': '#32cd32'});
    this_loader.show();
    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            title: title.val(),
            team_image: portfolio_img.val(),
            team_designation: team_designation.val(),
            team_experience: team_experience.val(),
            team_facebook: team_facebook.val(),
            team_google: team_google.val(),
            team_twitter: team_twitter.val(),
            team_linkedin: team_linkedin.val(),
            team_description: team_description.val(),
            action: 'jobsearch_add_team_member_to_list',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);

            this_loader.css({'background-color': '#e40000'});
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);

            if (typeof response.html !== 'undefined' && response.html != '') {
                jQuery('#jobsearch-team-members-con').find('>ul').append(response.html);
            } else {
                return false;
            }

            title.val('');
            team_designation.val('');
            team_experience.val('');
            team_facebook.val('');
            team_google.val('');
            team_twitter.val('');
            team_linkedin.val('');
            team_description.val('');
            portfolio_img.val('');
            if (portfolio_img.parents('.upload-img-holder-sec').find('img').length > 0) {
                portfolio_img.parents('.upload-img-holder-sec').find('img').attr('src', '');
            }

            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_dashboard_vars.error_msg);
        this_loader.css({'background-color': '#e40000'});
    });
});

jQuery(document).on('click', '#add-emp-award-btn', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var this_pcon = _this.parents('.jobsearch-add-resume-item-popup');
    jobsearch_validate_dashboard_form(this_pcon);

    var this_loader = _this.parent('li').find('.portfolio-loding-msg');

    var title = jQuery('#award_title');
    var portfolio_img = jQuery('#award_image_input');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.css({'background-color': '#32cd32'});
    this_loader.show();
    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            title: title.val(),
            award_image: portfolio_img.val(),
            action: 'jobsearch_add_emp_awards_to_list',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);

            this_loader.css({'background-color': '#e40000'});
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);

            if (typeof response.html !== 'undefined' && response.html != '') {
                jQuery('#jobsearch-emp-awards-con').find('>ul').append(response.html);
            } else {
                return false;
            }

            title.val('');
            portfolio_img.val('');
            if (portfolio_img.parents('.upload-img-holder-sec').find('img').length > 0) {
                portfolio_img.parents('.upload-img-holder-sec').find('img').attr('src', '');
            }

            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_dashboard_vars.error_msg);
        this_loader.css({'background-color': '#e40000'});
    });
});

jQuery(document).on('click', '#add-emp-affiliation-btn', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var this_pcon = _this.parents('.jobsearch-add-resume-item-popup');
    jobsearch_validate_dashboard_form(this_pcon);

    var this_loader = _this.parent('li').find('.portfolio-loding-msg');

    var title = jQuery('#affiliation_title');
    var portfolio_img = jQuery('#affiliation_image_input');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.css({'background-color': '#32cd32'});
    this_loader.show();
    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            title: title.val(),
            affiliation_image: portfolio_img.val(),
            action: 'jobsearch_add_emp_affiliations_to_list',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);

            this_loader.css({'background-color': '#e40000'});
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);

            if (typeof response.html !== 'undefined' && response.html != '') {
                jQuery('#jobsearch-emp-affiliations-con').find('>ul').append(response.html);
            } else {
                return false;
            }

            title.val('');
            portfolio_img.val('');
            if (portfolio_img.parents('.upload-img-holder-sec').find('img').length > 0) {
                portfolio_img.parents('.upload-img-holder-sec').find('img').attr('src', '');
            }

            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_dashboard_vars.error_msg);
        this_loader.css({'background-color': '#e40000'});
    });
});

function jobsearch_dashboard_read_portfolio_file_url(input) {

    if (input.files && input.files[0]) {

        var _this = jQuery(input);
        var loader_con = _this.parents('.upload-img-holder-sec').find('.file-loader');

        var img_file = input.files[0];
        var img_size = img_file.size;

        img_size = parseFloat(img_size / 1024).toFixed(2);

        if (img_size <= 1024) {
            loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
            loader_con.show();
            var formData = new FormData();
            formData.append('add_portfolio_img', img_file);
            formData.append('action', 'jobsearch_dashboard_adding_portfolio_img_url');

            var request = $.ajax({
                url: jobsearch_dashboard_vars.ajax_url,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json"
            });
            request.done(function (response) {
                if (typeof response.img_url !== 'undefined') {
                    _this.parents('.upload-img-holder-sec').find('img').attr('src', response.img_url);
                    if (_this.parents('.upload-img-holder-sec').find('#add-portfolio-img-input').length > 0) {
                        _this.parents('.upload-img-holder-sec').find('#add-portfolio-img-input').val(response.img_id);
                    } else if (_this.parents('.upload-img-holder-sec').find('#add-portfolio-img-input-upopup').length > 0) {
                        _this.parents('.upload-img-holder-sec').find('#add-portfolio-img-input-upopup').val(response.img_id);
                    } else if (_this.parents('.upload-img-holder-sec').find('.img-upload-save-field').length > 0) {
                        _this.parents('.upload-img-holder-sec').find('.img-upload-save-field').val(response.img_id);
                    }
                }
                loader_con.html('');
            });

            request.fail(function (jqXHR, textStatus) {
                loader_con.html(jobsearch_dashboard_vars.error_msg);
                loader_con.html('');
            });

        } else {
            alert(jobsearch_dashboard_vars.com_img_size);
        }
    }
}

jQuery(document).on('change', 'input[name="add_portfolio_img"]', function () {
    jobsearch_dashboard_read_portfolio_file_url(this);
});

function jobsearch_dashboard_read_team_file_url(input) {

    if (input.files && input.files[0]) {

        var _this = jQuery(input);
        var loader_con = _this.parents('.upload-img-holder-sec').find('.file-loader');

        var img_file = input.files[0];
        var img_size = img_file.size;

        img_size = parseFloat(img_size / 1024).toFixed(2);

        if (img_size <= 1024) {
            loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
            loader_con.show();
            var formData = new FormData();
            formData.append('team_image', img_file);
            formData.append('action', 'jobsearch_dashboard_adding_team_img_url');

            var request = $.ajax({
                url: jobsearch_dashboard_vars.ajax_url,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json"
            });
            request.done(function (response) {
                if (typeof response.img_url !== 'undefined') {
                    _this.parents('.upload-img-holder-sec').find('img').attr('src', response.img_url);
                    if (_this.parents('.upload-img-holder-sec').find('input[type=hidden]').length > 0) {
                        _this.parents('.upload-img-holder-sec').find('input[type=hidden]').val(response.img_url);
                    } else if (_this.parents('.upload-img-holder-sec').find('.img-upload-save-field').length > 0) {
                        _this.parents('.upload-img-holder-sec').find('.img-upload-save-field').val(response.img_url);
                    }
                }
                loader_con.html('');
            });

            request.fail(function (jqXHR, textStatus) {
                loader_con.html(jobsearch_dashboard_vars.error_msg);
                loader_con.html('');
            });

        } else {
            alert(jobsearch_dashboard_vars.com_img_size);
        }
    }
}

jQuery(document).on('change', 'input[name="team_image"], input[name="award_image"], input[name="affiliation_image"]', function () {
    jobsearch_dashboard_read_team_file_url(this);
});

jQuery(document).on('click', '.upload-port-img-btn', function () {
    jQuery(this).parents('.upload-img-holder-sec').find('input[type="file"]').trigger('click');
});

//
jQuery(document).on('click', ".jobsearch-resume-addbtn", function () {
    var _this = jQuery(this);
    if (_this.hasClass('jobsearch-portfolio-add-btn')) {
        var total_ports = _this.parents('.jobsearch-candidate-resume-wrap').find('.jobsearch-portfolios-list-con > li').length;
        var max_port_allow = jobsearch_dashboard_vars.max_portfolio_allow;
        var max_port_allow_msg = jobsearch_dashboard_vars.max_portfolio_allow_msg;

        if (max_port_allow <= total_ports) {
            alert(max_port_allow_msg);
            return false;
        }
    }
    jQuery('.jobsearch-add-resume-item-popup').hide();
    jQuery('.jobsearch-update-resume-items-sec').hide();
    _this.parents('.jobsearch-candidate-resume-wrap').find('.jobsearch-add-resume-item-popup').slideToggle("slow", function () {
        jQuery(this).find('span.edu-loding-msg').hide();
    });
    return false;
});

jQuery(document).on('click', '.close-popup-item', function () {
    var e_target = jQuery(this).parent('div');
    e_target.slideUp("slow");
});

jQuery(document).on('click', '.del-resume-item', function () {
    var e_target = jQuery(this).parents('li');
    jobsearch_cand_dash_resume_odd_workings_clbk();
    e_target.fadeOut('slow', function () {
        e_target.remove();
    });
});

jQuery(document).on('click', '.update-resume-item', function () {
    jQuery('.jobsearch-update-resume-items-sec').hide();
    jQuery('.jobsearch-add-resume-item-popup').hide();
    var e_target = jQuery(this).parents('li').find('.jobsearch-update-resume-items-sec');
    e_target.slideToggle("slow");
});

jQuery(document).on('click', '.update-resume-list-btn', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var e_target = _this.parents('.jobsearch-update-resume-items-sec');
    jobsearch_update_resume_items(_this);
    jobsearch_cand_dash_resume_odd_workings_clbk();
    e_target.slideUp("slow");
    return false;
});

function jobsearch_update_resume_items(_this) {
    var main_parent = _this.parents('li.resume-list-item');
    var update_con = main_parent.find('.jobsearch-resume-education-wrap');
    if (_this.parents('li.resume-list-item').hasClass('resume-list-edu')) {
        var title_val = main_parent.find('input[name^="jobsearch_field_education_title"]').val();
        update_con.find('> h2 > a').html(title_val);
        var year_val = main_parent.find('input[name^="jobsearch_field_education_year"]').val();
        update_con.find('> small').html(year_val);
        var inst_val = main_parent.find('input[name^="jobsearch_field_education_academy"]').val();
        update_con.find('> span').html(inst_val);
    } else if (_this.parents('li.resume-list-item').hasClass('resume-list-exp')) {
        var title_val = main_parent.find('input[name^="jobsearch_field_experience_title"]').val();
        update_con.find('> h2 > a').html(title_val);
        var comp_val = main_parent.find('input[name^="jobsearch_field_experience_company"]').val();
        update_con.find('> span').html(comp_val);
    } else if (_this.parents('li.resume-list-item').hasClass('resume-list-port')) {
        update_con = main_parent.find('>figure');
        var title_val = main_parent.find('input[name^="jobsearch_field_portfolio_title"]').val();
        update_con.find('> figcaption span').html(title_val);
        //var img_val = main_parent.find('input[name^="jobsearch_field_portfolio_image"]').val();
        //update_con.find('>a>span').css({'background-image': 'url(' + img_val + ')'});
    } else if (_this.parents('li.resume-list-item').hasClass('resume-list-skill')) {
        update_con = main_parent.find('.jobsearch-add-skills-wrap');
        var title_val = main_parent.find('input[name^="jobsearch_field_skill_title"]').val();
        update_con.find('> h2 > a').html(title_val);
        var skill_val = main_parent.find('input[name^="jobsearch_field_skill_percentage"]').val();
        update_con.find('> span').html(skill_val);
    } else if (_this.parents('li.resume-list-item').hasClass('resume-list-award')) {
        var title_val = main_parent.find('input[name^="jobsearch_field_award_title"]').val();
        update_con.find('> h2 > a').html(title_val);
        var year_val = main_parent.find('input[name^="jobsearch_field_award_year"]').val();
        update_con.find('> small').html(year_val);
    }
}

function jobsearch_gallry_read_file_url__bakup(event) {

    if (window.File && window.FileList && window.FileReader) {

        var files = event.target.files;
        for (var i = 0; i < files.length; i++) {
            var img_file = files[i];
            var img_size = img_file.size;

            img_size = parseFloat(img_size / 1024).toFixed(2);

            if (img_size <= 1024) {
                jQuery('#gallery-imgs-holder').find('>div.jobsearch-column-3').remove();
                var reader = new FileReader();

                reader.onload = function (e) {
                    var rand_number = Math.floor((Math.random() * 99999999) + 1);
                    var ihtml = '\
                    <div class="jobsearch-column-3">\
                        <figure>\
                            <a><img src="' + e.target.result + '" alt=""></a>\
                        </figure>\
                    </div>';

                    jQuery('#gallery-imgs-holder').append(ihtml);
                    jQuery('.jobsearch-company-gal-photo').hide();
                    jQuery('#upload-more-gal-imgs').show();
                }

                reader.readAsDataURL(files[i]);
            } else {
                alert(jobsearch_dashboard_vars.com_img_size);
                return false;
            }
        }
    }
}

function jobsearch_gallry_read_file_url(event) {
    if (window.File && window.FileList && window.FileReader) {
        var msg_con = jQuery('.galery-uplod-msg');
        var lodr_con = jQuery('.galery-uplod-lodr');
        var html_con = jQuery('#gallery-imgs-holder').find('ul.gal-all-imgs');
        var count_files = jQuery('#gallery-imgs-holder').find('ul.gal-all-imgs > li').length;
        var max_allow_imgs = jobsearch_dashboard_vars.max_portfolio_allow;
        var files = event.target.files;
        var formData = new FormData();
        for (var i = 0; i < files.length; i++) {
            var img_file = files[i];

            if (formData) {
                formData.append("gall_imgs[]", img_file);
            }
        }
        formData.append('alred_count', count_files);
        formData.append('action', 'jobsearch_empdash_gallery_imgs_url');

        lodr_con.html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: jobsearch_dashboard_vars.ajax_url,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.msg !== 'undefined' && response.msg != '') {
                msg_con.html(response.msg);
            }
            if (typeof response.html !== 'undefined' && response.html != '') {
                html_con.append(response.html);
                jQuery('.jobsearch-company-gal-photo').hide();
                var recount_files = jQuery('#gallery-imgs-holder').find('ul.gal-all-imgs > li').length;
                if (recount_files < max_allow_imgs) {
                    jQuery('#upload-more-gal-imgs').css({display:'inline-block'});
                } else {
                    jQuery('#upload-more-gal-imgs').hide();
                }
            }
            lodr_con.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            msg_con.html('');
            lodr_con.html('');
        });
    }
}

jQuery(document).on('click', '#upload-more-gal-imgs', function () {
    jQuery('#company_gallery_imgs').trigger('click');
});

jQuery(document).on('click', '.gal-item .el-remove', function () {
    var _this = jQuery(this);
    
    var img_id = _this.attr('data-id');
    
    var orig_icon = 'el-remove jobsearch-icon jobsearch-rubbish';
    var loder_icon = 'fa fa-refresh fa-spin';
    
    _this.removeClass(orig_icon).addClass(loder_icon);
    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            id: img_id,
            action: 'jobsearch_empdash_gallery_imgs_remove'
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (response.success == '1') {
            _this.parents('li').fadeOut('slow', 'linear', function () {
                _this.parents('li').remove();
                var imgs_cont = jQuery('#gallery-imgs-holder').find('>ul > li');
                if (imgs_cont.length <= 0) {
                    jQuery('.jobsearch-company-gal-photo').css({display:'inline-block'});
                    jQuery('#upload-more-gal-imgs').hide();
                } else {
                    jQuery('#upload-more-gal-imgs').css({display:'inline-block'});
                }
            });
            return false;
        }
        _this.removeClass(loder_icon).addClass(orig_icon);
    });

    request.fail(function (jqXHR, textStatus) {
        _this.removeClass(loder_icon).addClass(orig_icon);
    });
});


// applicants scripts
jQuery(document).on('click', '#select-all-job-app', function () {
    var _this = jQuery(this);
    if (_this.is(':checked')) {
        jQuery('input[type="checkbox"][id^="app_candidate_sel"]').prop('checked', true);
        jQuery('input[type="checkbox"][id^="app_candidate_sel"]').trigger('change');
    } else {
        jQuery('input[type="checkbox"][id^="app_candidate_sel"]').prop('checked', false);
        jQuery('input[type="checkbox"][id^="app_candidate_sel"]').trigger('change');
    }
});

jQuery(document).on('change', 'input[type="checkbox"][name*="app_candidate_sel"]', function () {
    var checked_box_count = jQuery('input[type="checkbox"][name*="app_candidate_sel"]:checked').length;
    if (checked_box_count > 0) {
        jQuery('#sort-more-field-sec').show();
    } else {
        jQuery('#sort-more-field-sec').hide();
    }
});

jQuery(document).on('click', '.candidate-more-acts-con .more-actions', function () {
    var _this = jQuery(this);
    var all_boxes = jQuery('.candidate-more-acts-con');
    //
    all_boxes.find('ul').slideUp();
    all_boxes.find('.more-actions').removeClass('open-options');
    //
    var this_parent = _this.parent('.candidate-more-acts-con');
    if (_this.hasClass('open-options')) {
        this_parent.find('ul').slideUp();
        _this.removeClass('open-options')
    } else {
        this_parent.find('ul').slideDown();
        _this.addClass('open-options')
    }
});

jQuery(document).on('click', 'body', function (evt) {
    var target = evt.target;
    var this_box = jQuery('.candidate-more-acts-con');
    if (!this_box.is(evt.target) && this_box.has(evt.target).length === 0) {
        this_box.find('ul').slideUp();
        this_box.find('.more-actions').removeClass('open-options');
    }

    var more_box = jQuery('.more-fields-act-btn');
    if (!more_box.is(evt.target) && more_box.has(evt.target).length === 0) {
        more_box.find('ul').slideUp();
        more_box.find('.more-actions').removeClass('open-options');
    }
});

jQuery(document).on('click', '.more-fields-act-btn .more-actions', function () {
    var _this = jQuery(this);

    var this_parent = _this.parent('.more-fields-act-btn');
    if (_this.hasClass('open-options')) {
        this_parent.find('ul').slideUp();
        _this.removeClass('open-options')
    } else {
        this_parent.find('ul').slideDown();
        _this.addClass('open-options')
    }
});

jQuery(document).on('change', '#jobsearch-applicants-sort', function (evt) {
    var _this = jQuery(this);
    _this.parent('form').submit();
});

jQuery(document).on('click', '.applicantto-email-submit-btn', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var _this_rand = _this.attr('data-randid');
    var _job_id = _this.attr('data-jid');
    var _candidate_id = _this.attr('data-cid');
    var _employer_id = _this.attr('data-eid');

    var this_form = _this.parents('form');
    
    var get_terr_val = jobsearch_accept_terms_cond_pop(this_form);
    if (get_terr_val != 'yes') {
        return false;
    }
    
    var this_loader = this_form.find('.loader-box-' + _this_rand);
    var this_msg_con = this_form.find('.message-box-' + _this_rand);

    var email_subject = this_form.find('input[name="send_message_subject"]');
    var email_content = this_form.find('textarea[name="send_message_content"]');

    var error = 0;
    if (email_subject.val() == '') {
        error = 1;
        email_subject.css({"border": "1px solid #ff0000"});
    } else {
        email_subject.css({"border": "1px solid #d3dade"});
    }
    if (email_content.val() == '') {
        error = 1;
        email_content.css({"border": "1px solid #ff0000"});
    } else {
        email_content.css({"border": "1px solid #d3dade"});
    }

    if (error == 0) {

        this_msg_con.hide();
        this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: jobsearch_dashboard_vars.ajax_url,
            method: "POST",
            data: {
                _job_id: _job_id,
                _candidate_id: _candidate_id,
                _employer_id: _employer_id,
                email_subject: email_subject.val(),
                email_content: email_content.val(),
                action: 'jobsearch_send_email_to_applicant_by_employer',
            },
            dataType: "json"
        });

        request.done(function (response) {
            var msg_before = '';
            var msg_after = '';
            if (typeof response.error !== 'undefined') {
                if (response.error == '1') {
                    msg_before = '<div class="alert alert-danger"><i class="fa fa-times"></i> ';
                    msg_after = '</div>';
                } else if (response.error == '0') {
                    msg_before = '<div class="alert alert-success"><i class="fa fa-check"></i> ';
                    msg_after = '</div>';
                }
            }
            if (typeof response.msg !== 'undefined') {
                this_msg_con.html(msg_before + response.msg + msg_after);
                this_msg_con.slideDown();
                if (typeof response.error !== 'undefined' && response.error == '0') {
                    email_subject.val('');
                    email_content.val('');
                    this_form.find('ul.email-fields-list').slideUp();
                }
            } else {
                this_msg_con.html(jobsearch_job_application.error_msg);
            }
            this_loader.html('');

        });

        request.fail(function (jqXHR, textStatus) {
            this_loader.html(jobsearch_dashboard_vars.error_msg);
        });
    }
});

jQuery(document).on('click', '.multi-applicantsto-email-submit', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var this_form = _this.parents('form');
    var _job_id = _this.attr('data-jid');
    var _employer_id = _this.attr('data-eid');

    var get_terr_val = jobsearch_accept_terms_cond_pop(this_form);
    if (get_terr_val != 'yes') {
        return false;
    }
    
    var _selected_apps_str = '';
    var _selected_apps_arr = [];
    var _selected_apps = jQuery('input[type="checkbox"][name*="app_candidate_sel"]:checked');
    _selected_apps.each(function (index, element) {
        if (jQuery(this).val() != '') {
            _selected_apps_arr.push(jQuery(this).val());
        }
    });
    if (_selected_apps_arr.length > 0) {
        _selected_apps_str = _selected_apps_arr.join(",");
    }

    if (_selected_apps_str != '') {
        var this_loader = this_form.find('.loader-box-' + _job_id);
        var this_msg_con = this_form.find('.message-box-' + _job_id);

        var email_subject = this_form.find('input[name="send_message_subject"]');
        var email_content = this_form.find('textarea[name="send_message_content"]');

        var error = 0;
        if (email_subject.val() == '') {
            error = 1;
            email_subject.css({"border": "1px solid #ff0000"});
        } else {
            email_subject.css({"border": "1px solid #d3dade"});
        }
        if (email_content.val() == '') {
            error = 1;
            email_content.css({"border": "1px solid #ff0000"});
        } else {
            email_content.css({"border": "1px solid #d3dade"});
        }

        if (error == 0) {

            this_msg_con.hide();
            this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
            var request = jQuery.ajax({
                url: jobsearch_dashboard_vars.ajax_url,
                method: "POST",
                data: {
                    _job_id: _job_id,
                    _employer_id: _employer_id,
                    _candidate_ids: _selected_apps_str,
                    email_subject: email_subject.val(),
                    email_content: email_content.val(),
                    action: 'jobsearch_send_email_to_multi_applicants_by_employer',
                },
                dataType: "json"
            });

            request.done(function (response) {
                var msg_before = '';
                var msg_after = '';
                if (typeof response.error !== 'undefined') {
                    if (response.error == '1') {
                        msg_before = '<div class="alert alert-danger"><i class="fa fa-times"></i> ';
                        msg_after = '</div>';
                    } else if (response.error == '0') {
                        msg_before = '<div class="alert alert-success"><i class="fa fa-check"></i> ';
                        msg_after = '</div>';
                    }
                }
                if (typeof response.msg !== 'undefined') {
                    this_msg_con.html(msg_before + response.msg + msg_after);
                    this_msg_con.slideDown();
                    if (typeof response.error !== 'undefined' && response.error == '0') {
                        email_subject.val('');
                        email_content.val('');
                        this_form.find('ul.email-fields-list').slideUp();
                    }
                } else {
                    this_msg_con.html(jobsearch_job_application.error_msg);
                }
                this_loader.html('');

            });

            request.fail(function (jqXHR, textStatus) {
                this_loader.html(jobsearch_dashboard_vars.error_msg);
            });
        }
    }
});

jQuery(document).on('click', '.multi-instamatchcands-email-submit', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var this_form = _this.parents('form');
    var _job_id = _this.attr('data-jid');
    var _employer_id = _this.attr('data-eid');
    
    var get_terr_val = jobsearch_accept_terms_cond_pop(this_form);
    if (get_terr_val != 'yes') {
        return false;
    }

    var _selected_apps_str = '';
    var _selected_apps_arr = [];
    var _selected_apps = jQuery('input[type="checkbox"][name*="app_candidate_sel"]:checked');
    _selected_apps.each(function (index, element) {
        if (jQuery(this).val() != '') {
            _selected_apps_arr.push(jQuery(this).val());
        }
    });
    if (_selected_apps_arr.length > 0) {
        _selected_apps_str = _selected_apps_arr.join(",");
    }

    if (_selected_apps_str != '') {
        var this_loader = this_form.find('.loader-box-' + _job_id);
        var this_msg_con = this_form.find('.message-box-' + _job_id);

        var email_subject = this_form.find('input[name="send_message_subject"]');
        var email_content = this_form.find('textarea[name="send_message_content"]');

        var error = 0;
        if (email_subject.val() == '') {
            error = 1;
            email_subject.css({"border": "1px solid #ff0000"});
        } else {
            email_subject.css({"border": "1px solid #d3dade"});
        }
        if (email_content.val() == '') {
            error = 1;
            email_content.css({"border": "1px solid #ff0000"});
        } else {
            email_content.css({"border": "1px solid #d3dade"});
        }

        if (error == 0) {

            this_msg_con.hide();
            this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
            var request = jQuery.ajax({
                url: jobsearch_dashboard_vars.ajax_url,
                method: "POST",
                data: {
                    _job_id: _job_id,
                    _employer_id: _employer_id,
                    _candidate_ids: _selected_apps_str,
                    email_subject: email_subject.val(),
                    email_content: email_content.val(),
                    action: 'jobsearch_send_email_to_multi_instamatchs_by_employer',
                },
                dataType: "json"
            });

            request.done(function (response) {
                var msg_before = '';
                var msg_after = '';
                if (typeof response.error !== 'undefined') {
                    if (response.error == '1') {
                        msg_before = '<div class="alert alert-danger"><i class="fa fa-times"></i> ';
                        msg_after = '</div>';
                    } else if (response.error == '0') {
                        msg_before = '<div class="alert alert-success"><i class="fa fa-check"></i> ';
                        msg_after = '</div>';
                    }
                }
                if (typeof response.msg !== 'undefined') {
                    this_msg_con.html(msg_before + response.msg + msg_after);
                    this_msg_con.slideDown();
                    if (typeof response.error !== 'undefined' && response.error == '0') {
                        email_subject.val('');
                        email_content.val('');
                        this_form.find('ul.email-fields-list').slideUp();
                    }
                } else {
                    this_msg_con.html(jobsearch_job_application.error_msg);
                }
                this_loader.html('');

            });

            request.fail(function (jqXHR, textStatus) {
                this_loader.html(jobsearch_dashboard_vars.error_msg);
            });
        }
    }
});

jQuery(document).on('click', '.shortlist-cand-to-intrview', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var _job_id = _this.attr('data-jid');
    var _candidate_id = _this.attr('data-cid');

    var this_loader = _this.find('.app-loader');
    var this_msg_con = _this;

    if (_this.hasClass('ajax-enable')) {
        this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: jobsearch_dashboard_vars.ajax_url,
            method: "POST",
            data: {
                _job_id: _job_id,
                _candidate_id: _candidate_id,
                action: 'jobsearch_applicant_to_interview_by_employer',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.msg !== 'undefined' && typeof response.error !== 'undefined' && response.error == '0') {
                this_msg_con.html(response.msg);
                _this.removeClass('ajax-enable');
                window.location.reload(true);
            }
            this_loader.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            this_loader.html('');
        });
    }
});

jQuery(document).on('click', '.reject-cand-to-intrview', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var _job_id = _this.attr('data-jid');
    var _candidate_id = _this.attr('data-cid');

    var this_loader = _this.parent('li').find('.app-loader');
    var this_msg_con = _this;

    if (_this.hasClass('ajax-enable')) {
        this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: jobsearch_dashboard_vars.ajax_url,
            method: "POST",
            data: {
                _job_id: _job_id,
                _candidate_id: _candidate_id,
                action: 'jobsearch_applicant_to_reject_by_employer',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.msg !== 'undefined' && typeof response.error !== 'undefined' && response.error == '0') {
                this_msg_con.html(response.msg);
                _this.removeClass('ajax-enable');
            }
            this_loader.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            this_loader.html('');
        });
    }
});

jQuery(document).on('click', '.undoreject-cand-to-list', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var _job_id = _this.attr('data-jid');
    var _candidate_id = _this.attr('data-cid');

    var this_loader = _this.parent('li').find('.app-loader');
    var this_msg_con = _this;

    if (_this.hasClass('ajax-enable')) {
        this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: jobsearch_dashboard_vars.ajax_url,
            method: "POST",
            data: {
                _job_id: _job_id,
                _candidate_id: _candidate_id,
                action: 'jobsearch_applicant_to_undoreject_by_employer',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.msg !== 'undefined' && typeof response.error !== 'undefined' && response.error == '0') {
                this_msg_con.html(response.msg);
                _this.removeClass('ajax-enable');
                window.location.reload(true);
            }
            this_loader.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            this_loader.html('');
        });
    }
});

jQuery(document).on('click', '.delete-cand-from-job', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var _job_id = _this.attr('data-jid');
    var _candidate_id = _this.attr('data-cid');

    var this_loader = _this.parent('li').find('.app-loader');
    var this_msg_con = _this;

    if (_this.hasClass('ajax-enable')) {
        this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: jobsearch_dashboard_vars.ajax_url,
            method: "POST",
            data: {
                _job_id: _job_id,
                _candidate_id: _candidate_id,
                action: 'jobsearch_delete_applicant_by_employer',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.msg !== 'undefined' && typeof response.error !== 'undefined' && response.error == '0') {
                this_msg_con.html(response.msg);
                _this.removeClass('ajax-enable');
                _this.parents('li.jobsearch-column-12').slideUp();
            }
            this_loader.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            this_loader.html('');
        });
    }
});

jQuery(document).on('click', '.shortlist-cands-to-intrview', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var _job_id = _this.attr('data-jid');

    var _selected_apps_str = '';
    var _selected_apps_arr = [];
    var _selected_apps = jQuery('input[type="checkbox"][name*="app_candidate_sel"]:checked');
    _selected_apps.each(function (index, element) {
        if (jQuery(this).val() != '') {
            _selected_apps_arr.push(jQuery(this).val());
        }
    });
    if (_selected_apps_arr.length > 0) {
        _selected_apps_str = _selected_apps_arr.join(",");
    }

    var this_loader = _this.parent('li').find('.app-loader');
    var this_msg_con = _this;

    if (_this.hasClass('ajax-enable')) {
        this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: jobsearch_dashboard_vars.ajax_url,
            method: "POST",
            data: {
                _job_id: _job_id,
                _candidate_ids: _selected_apps_str,
                action: 'jobsearch_multi_apps_to_interview_by_employer',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.msg !== 'undefined' && typeof response.error !== 'undefined' && response.error == '0') {
                this_msg_con.html(response.msg + ' <span class="app-loader"><i class="fa fa-refresh fa-spin"></i></span>');
                _this.removeClass('ajax-enable');
                window.location.reload(true);
                return false;
            }
            this_loader.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            this_loader.html('');
        });
    }
});

jQuery(document).on('click', '.reject-cands-to-intrview', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var _job_id = _this.attr('data-jid');

    var _selected_apps_str = '';
    var _selected_apps_arr = [];
    var _selected_apps = jQuery('input[type="checkbox"][name*="app_candidate_sel"]:checked');
    _selected_apps.each(function (index, element) {
        if (jQuery(this).val() != '') {
            _selected_apps_arr.push(jQuery(this).val());
        }
    });
    if (_selected_apps_arr.length > 0) {
        _selected_apps_str = _selected_apps_arr.join(",");
    }

    var this_loader = _this.parent('li').find('.app-loader');
    var this_msg_con = _this;

    if (_this.hasClass('ajax-enable')) {
        this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: jobsearch_dashboard_vars.ajax_url,
            method: "POST",
            data: {
                _job_id: _job_id,
                _candidate_ids: _selected_apps_str,
                action: 'jobsearch_multi_apps_to_reject_by_employer',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.msg !== 'undefined' && typeof response.error !== 'undefined' && response.error == '0') {
                this_msg_con.html(response.msg + ' <span class="app-loader"><i class="fa fa-refresh fa-spin"></i></span>');
                _this.removeClass('ajax-enable');
                window.location.reload(true);
                return false;
            }
            this_loader.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            this_loader.html('');
        });
    }
});

jQuery(document).on('click', '.apps-view-btn', function () {
    var _this = jQuery(this);
    var view_input = jQuery('input[name="ap_view"]');
    if (_this.attr('data-view') == 'grid') {
        view_input.val('grid');
    } else {
        view_input.val('list');
    }
    view_input.parent('form').submit();
});

jQuery(document).on('click', '.move-cand-from-instamatch', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var _job_id = _this.attr('data-jid');
    var _candidate_id = _this.attr('data-cid');

    var this_loader = _this.find('.app-loader');
    var this_msg_con = _this;

    if (_this.hasClass('ajax-enable')) {
        this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: jobsearch_dashboard_vars.ajax_url,
            method: "POST",
            data: {
                _job_id: _job_id,
                _candidate_id: _candidate_id,
                action: 'jobsearch_job_instamatch_moveto_applicant',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.msg !== 'undefined' && typeof response.error !== 'undefined' && response.error == '0') {
                window.location.reload(true);
                _this.removeClass('ajax-enable');
                return false;
            }
            this_loader.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            this_loader.html('');
        });
    }
});

jQuery(document).on('click', '.move-instacands-to-applics', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var _job_id = _this.attr('data-jid');

    var _selected_apps_str = '';
    var _selected_apps_arr = [];
    var _selected_apps = jQuery('input[type="checkbox"][name*="app_candidate_sel"]:checked');
    _selected_apps.each(function (index, element) {
        if (jQuery(this).val() != '') {
            _selected_apps_arr.push(jQuery(this).val());
        }
    });
    if (_selected_apps_arr.length > 0) {
        _selected_apps_str = _selected_apps_arr.join(",");
    }

    var this_loader = _this.find('.app-loader');
    var this_msg_con = _this;

    if (_this.hasClass('ajax-enable')) {
        this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: jobsearch_dashboard_vars.ajax_url,
            method: "POST",
            data: {
                _job_id: _job_id,
                _candidate_ids: _selected_apps_str,
                action: 'jobsearch_multi_move_instamatch_to_apps',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.msg !== 'undefined' && typeof response.error !== 'undefined' && response.error == '0') {
                this_msg_con.html('<i class="fa fa-user-plus"></i> ' + response.msg + ' &nbsp;<span class="app-loader"><i class="fa fa-refresh fa-spin"></i></span>');
                _this.removeClass('ajax-enable');
                window.location.reload(true);
                return false;
            }
            this_loader.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            this_loader.html('');
        });
    }
});

function jobsearch_is_valid_phone_number(that) {
    var val = that.value;
    var element = jQuery(that);
    var matches = val.match(/^[0-9\-\(\)\/\+\s]*$/);
    if (matches) {
        element.css({"border-color": "#eceeef"});
    } else {
        element.css({"border-color": "#ff0000"});
    }
}

jQuery(document).on('click', '.jobsearch-feat-job-form input[type=checkbox]', function () {
    
    var _this = jQuery(this);
    var this_con = _this.parents('.jobsearch-feat-job-form');
    this_con.find('input[type=checkbox]:checked').prop('checked', false);
    _this.prop('checked', true);
    
    var checkout_btn = this_con.find('.jobsearch-feature-pkg-buybtn');
    var feat_job_btn = this_con.find('.jobsearch-feature-pkg-alpurbtn');
    
    if (_this.attr('name') == 'alpur_feature_pkg') {
        checkout_btn.hide();
        feat_job_btn.removeAttr('style');
    } else {
        checkout_btn.removeAttr('style');
        feat_job_btn.hide();
    }
});

jQuery(document).on('click', '.jobsearch-feature-pkg-alpurbtn', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_con = jQuery('#fpkgs-lista-' + this_id);

    var ajax_url = jobsearch_dashboard_vars.ajax_url;
    var loader_con = this_con.find('.fpkgs-loader');
    var msg_con = this_con.find('.fpkgs-msg');

    var order_id = this_con.find('input[type=checkbox]:checked');

    msg_con.html('');
    loader_con.html('<i class="fa fa-refresh fa-spin"></i>');

    var request = jQuery.ajax({
        url: ajax_url,
        method: "POST",
        data: {
            'job_id': this_id,
            'order_id': order_id.val(),
            'action': 'jobsearch_doing_feat_job_with_alorder',
        },
        dataType: "json"
    });

    request.done(function (response) {

        var msg_before = '';
        var msg_after = '';
        
        if (typeof response.msg !== 'undefined' && typeof response.error !== 'undefined' && response.error == '1') {
            msg_before = '<div class="alert alert-danger"><i class="fa fa-times"></i> ';
            msg_after = '</div>';
            loader_con.html('');
            msg_con.html(msg_before + response.msg + msg_after);
        } else {
            msg_con.html(response.msg);
            window.location.reload();
        }
    });

    request.fail(function (jqXHR, textStatus) {
        loader_con.html('');
    });

    return false;
});

jQuery(document).on('click', '.jobsearch-feature-pkg-buybtn', function () {
    var _this = $(this);
    var this_id = _this.attr('data-id');
    var this_con = jQuery('#fpkgs-lista-' + this_id);

    var ajax_url = jobsearch_dashboard_vars.ajax_url;
    var loader_con = this_con.find('.fpkgs-loader');
    var msg_con = this_con.find('.fpkgs-msg');

    var pkg_id = this_con.find('input[name="feature_pkg"]:checked');

    msg_con.html('');
    loader_con.html('<i class="fa fa-refresh fa-spin"></i>');

    var request = jQuery.ajax({
        url: ajax_url,
        method: "POST",
        data: {
            'job_id': this_id,
            'pkg_id': pkg_id.val(),
            'action': 'jobsearch_doing_mjobs_feature_job',
        },
        dataType: "json"
    });

    request.done(function (response) {

        var msg_before = '';
        var msg_after = '';
        if (typeof response.error !== 'undefined' && response.error == '1') {
            msg_before = '<div class="alert alert-danger"><i class="fa fa-times"></i> ';
            msg_after = '</div>';
        }
        if (typeof response.msg !== 'undefined' && typeof response.error !== 'undefined' && response.error == '1') {
            loader_con.html('');
            msg_con.html(msg_before + response.msg + msg_after);
        } else {
            msg_con.html(response.msg);
        }
    });

    request.fail(function (jqXHR, textStatus) {
        loader_con.html('');
    });

    return false;

});

jQuery(document).on('click', '.jobsearch-fill-the-job', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var _job_id = _this.attr('data-id');

    var this_loader = _this.parent('.jobsearch-filledjobs-links').find('.fill-job-loader');

    if (_this.hasClass('ajax-enable')) {
        this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: jobsearch_dashboard_vars.ajax_url,
            method: "POST",
            data: {
                _job_id: _job_id,
                action: 'jobsearch_job_filled_by_employer',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.msg !== 'undefined' && typeof response.error !== 'undefined' && response.error == '0') {
                _this.removeClass('ajax-enable');
                _this.parents('.jobsearch-table-row').find('.job-filled').html(response.msg);
                _this.append('<i class="fa fa-check"></i>');
                _this.removeAttr('href');
            }
            this_loader.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            this_loader.html('');
        });
    }
});

jQuery(document).on('click', '#skill-detail-popup-btn', function () {
    jobsearch_modal_popup_open('JobSearchModalSkillsDetail');
});

jQuery(document).on('click', '.jobsearch-subs-detail', function () {
    var rnd_id = $(this).attr('data-rid');
    $('#pkgs-table-subsc-' + rnd_id).slideToggle();
});



jQuery(document).on('click', '.jobsearch-remresmuesh-item-cc', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var _cand_id = _this.attr('data-id');

    var this_loder = _this.find('i');

    this_loder.attr('class', 'fa fa-refresh fa-spin');
    var request = jQuery.ajax({
        url: jobsearch_dashboard_vars.ajax_url,
        method: "POST",
        data: {
            _cand_id: _cand_id,
            action: 'jobsearch_remove_emp_resmue_shlist_from_list',
        },
        dataType: "json"
    });

    request.done(function (response) {

        _this.parents('li').fadeOut('slow');
    });

    request.fail(function (jqXHR, textStatus) {
        this_loder.attr('class', 'fa fa-times');
    });
});

jQuery(document).on('click', '.jobsearch-empmember-add-popup', function () {
    jobsearch_modal_popup_open('JobSearchModalEmpAccMembAdd');
});

jQuery(document).on('click', '.jobsearch-empmember-add-btn', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var this_form = _this.parents('form[id="addempmemb-account-form"]');

    var ajax_url = jobsearch_dashboard_vars.ajax_url;

    var msg_con = this_form.find('.form-msg');
    var msg_loader = this_form.find('.form-loader');

    var first_name = this_form.find('input[name="u_firstname"]');
    var last_name = this_form.find('input[name="u_lastname"]');
    var msg_name = this_form.find('input[name="u_username"]');
    var msg_email = this_form.find('input[name="u_emailadres"]');
    var u_pass = this_form.find('input[name="u_password"]');
    var u_conf_pass = this_form.find('input[name="u_confpass"]');

    var error = 0;
    var email_pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,20}$/i);

    if (msg_name.val() == '') {
        error = 1;
        msg_name.css({"border": "1px solid #ff0000"});
    } else {
        msg_name.css({"border": "1px solid #efefef"});
    }

    if (msg_email.val() == '') {
        error = 1;
        msg_email.css({"border": "1px solid #ff0000"});
    } else {
        if (!email_pattern.test(msg_email.val())) {
            error = 1;
            msg_email.css({"border": "1px solid #ff0000"});
        } else {
            msg_email.css({"border": "1px solid #efefef"});
        }
    }

    if (error == 0) {
        msg_loader.html('<i class="fa fa-refresh fa-spin"></i>');

        var form_data = new FormData(this_form[0]);
        var request = $.ajax({
            url: ajax_url,
            method: "POST",
            data: form_data,
            processData: false,
            contentType: false,
            dataType: "json"
        });

        request.done(function (response) {

            var msg_before = '';
            var msg_after = '';
            if (typeof response.error !== 'undefined') {
                if (response.error == '1') {
                    msg_before = '<div class="alert alert-danger"><i class="fa fa-times"></i> ';
                    msg_after = '</div>';
                } else if (response.error == '0') {
                    msg_before = '<div class="alert alert-success"><i class="fa fa-check"></i> ';
                    msg_after = '</div>';
                }
            }
            if (typeof response.msg !== 'undefined') {
                msg_con.html(msg_before + response.msg + msg_after);
                if (typeof response.error !== 'undefined' && response.error == '0') {
                    first_name.val('');
                    last_name.val('');
                    u_pass.val('');
                    u_conf_pass.val('');
                    msg_name.val('');
                    msg_email.val('');

                    window.location.reload(true);
                }
            }
            msg_loader.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            msg_loader.html('');
        });
    }

    return false;

});

jQuery(document).on('click', '.emp-memb-updatebtn', function () {
    var _this_rid = jQuery(this).attr('data-id');
    jobsearch_modal_popup_open('JobSearchModalEmpAccMembUpdate' + _this_rid);
});

jQuery(document).on('click', '.jobsearch-empmember-updte-btn', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var rand_id = _this.attr('data-id');
    var this_form = _this.parents('form[id="editempmemb-account-form-' + rand_id + '"]');

    var ajax_url = jobsearch_dashboard_vars.ajax_url;

    var msg_con = this_form.find('.form-msg');
    var msg_loader = this_form.find('.form-loader');

    var error = 0;

    if (error == 0) {
        msg_loader.html('<i class="fa fa-refresh fa-spin"></i>');

        var form_data = new FormData(this_form[0]);
        var request = $.ajax({
            url: ajax_url,
            method: "POST",
            data: form_data,
            processData: false,
            contentType: false,
            dataType: "json"
        });

        request.done(function (response) {

            var msg_before = '';
            var msg_after = '';
            if (typeof response.error !== 'undefined') {
                if (response.error == '1') {
                    msg_before = '<div class="alert alert-danger"><i class="fa fa-times"></i> ';
                    msg_after = '</div>';
                } else if (response.error == '0') {
                    msg_before = '<div class="alert alert-success"><i class="fa fa-check"></i> ';
                    msg_after = '</div>';
                }
            }
            if (typeof response.msg !== 'undefined') {
                msg_con.html(msg_before + response.msg + msg_after);
            }
            msg_loader.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            msg_loader.html('');
        });
    }

    return false;

});

jQuery(document).on('click', '.emp-memb-removebtn', function (e) {
    e.preventDefault();
    var _this = jQuery(this);
    var user_id = _this.attr('data-id');
    var ajax_url = jobsearch_dashboard_vars.ajax_url;

    var emp_user_id = '0';
    if (typeof _this.attr('data-euid') !== 'undefined' && _this.attr('data-euid') > 0) {
        emp_user_id = _this.attr('data-euid');
    }

    var msg_loader = _this.find('i');
    var msg_lodr_class = msg_loader.attr('class');

    var conf = confirm(jobsearch_dashboard_vars.are_you_sure);
    if (conf) {
        msg_loader.attr('class', 'fa fa-refresh fa-spin');

        var form_data = 'member_uid=' + user_id + '&cus_employer_id=' + emp_user_id + '&action=jobsearch_employer_remove_member_account';
        var request = jQuery.ajax({
            url: ajax_url,
            method: "POST",
            data: form_data,
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.error !== 'undefined' && response.error == '0') {
                window.location.reload(true);
            } else {
                msg_loader.attr('class', msg_lodr_class);
            }
        });

        request.fail(function (jqXHR, textStatus) {
            msg_loader.attr('class', msg_lodr_class);
        });
    }

    return false;

});

jQuery(document).on('click', '.updte-profile-slugbtn', function () {
    var _this = jQuery(this);
    var parnt_con = _this.parent('.jobsearch-userprofile-url');
    var slug_input = parnt_con.find('.profile-slug-field');
    var ok_btn = parnt_con.find('.ok-profile-slugbtn');
    parnt_con.find('strong').hide();
    _this.hide();
    ok_btn.show();
    slug_input.show();
});

jQuery(document).on('click', '.ok-profile-slugbtn', function () {
    var _this = jQuery(this);
    var parnt_con = _this.parent('.jobsearch-userprofile-url');
    var _loader = parnt_con.find('.slugchng-loder');
    var slug_input = parnt_con.find('.profile-slug-field');
    var update_btn = parnt_con.find('.updte-profile-slugbtn');

    if (slug_input != '') {
        _loader.html('<i class="fa fa-refresh fa-spin"></i>');
        var form_data = 'updte_slug=' + (slug_input.val()) + '&action=jobsearch_user_update_profileslug';
        var request = jQuery.ajax({
            url: jobsearch_dashboard_vars.ajax_url,
            method: "POST",
            data: form_data,
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.suc !== 'undefined' && response.suc == '1') {
                _this.hide();
                slug_input.hide();
                parnt_con.find('strong').html(response.updated_slug);
                parnt_con.find('strong').show();
                update_btn.show();
            }
            _loader.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            _loader.html('');
        });
    }
});

//
jQuery('.job_post_cajax_field').on('click', function (e) {
    e.preventDefault();
    var emp_id = jQuery('.jobsearch-allaplicants-holder').attr('data-eid');
    var usr_id = jQuery('.jobsearch-allaplicants-holder').attr('data-uid');
    var this_id = jQuery(this).data('randid'),
            loaded = jQuery(this).data('loaded'),
            posttype = jQuery(this).data('posttype'),
            custom_field = jQuery('#custom_post_field_' + this_id),
            ajax_url = jobsearch_plugin_vars.ajax_url,
            force_std = jQuery(this).data('forcestd');
    if (loaded != true) {
        jQuery('.custom_post_loader_' + this_id).html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: ajax_url,
            method: "POST",
            data: {
                force_std: force_std,
                posttype: posttype,
                emp_id: emp_id,
                usr_id: usr_id,
                action: 'jobsearch_empdash_load_all_apswith_job_posts',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if ('undefined' !== typeof response.html) {
                custom_field.html(response.html);
                jQuery('.custom_post_loader_' + this_id).html('');
                custom_field.data('loaded', true);
            }
        });

        request.fail(function (jqXHR, textStatus) {
        });
    }
    return false;

});

jQuery(document).on('change', 'select[name="all_jobs_wapps_selctor"]', function (e) {
    e.preventDefault();
    var emp_id = jQuery('.jobsearch-allaplicants-holder').attr('data-eid');
    var usr_id = jQuery('.jobsearch-allaplicants-holder').attr('data-uid');
    var _this = jQuery(this),
            loaderr = _this.parents('.allapps-jobselct-con').find('span'),
            job_id = _this.val(),
            appender_con = jQuery('.jobsearch-all-aplicantslst'),
            ajax_url = jobsearch_plugin_vars.ajax_url;
    if (job_id != '') {
        _this.addClass('ajax-loadin');
        loaderr.html('<i class="fa fa-refresh fa-spin"></i>');

        var request = jQuery.ajax({
            url: ajax_url,
            method: "POST",
            data: {
                _job_id: job_id,
                emp_id: emp_id,
                usr_id: usr_id,
                action: 'jobsearch_empdash_load_single_apswith_job_inlist',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if ('undefined' !== typeof response.html) {
                //
                appender_con.html(response.html);
                jQuery('.lodmore-apps-btnsec').hide();
            }
            loaderr.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            loaderr.html('');
        });
    }
    return false;

});

jQuery('.lodmore-apps-btn').on('click', function (e) {
    e.preventDefault();
    var emp_id = jQuery('.jobsearch-allaplicants-holder').attr('data-eid');
    var usr_id = jQuery('.jobsearch-allaplicants-holder').attr('data-uid');
    var _this = jQuery(this),
            total_pages = _this.attr('data-tpages'),
            page_num = _this.attr('data-gtopage'),
            this_html = _this.html(),
            appender_con = jQuery('.jobsearch-all-aplicantslst'),
            ajax_url = jobsearch_plugin_vars.ajax_url;
    if (!_this.hasClass('ajax-loadin')) {
        _this.addClass('ajax-loadin');
        _this.html(this_html + ' <i class="fa fa-refresh fa-spin"></i>');

        total_pages = parseInt(total_pages);
        page_num = parseInt(page_num);
        var request = jQuery.ajax({
            url: ajax_url,
            method: "POST",
            data: {
                page_num: page_num,
                emp_id: emp_id,
                usr_id: usr_id,
                action: 'jobsearch_empdash_load_more_apswith_job_apps',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if ('undefined' !== typeof response.html) {
                page_num += 1;
                _this.attr('data-gtopage', page_num)
                if (page_num > total_pages) {
                    _this.parent('div').hide();
                }
                appender_con.append(response.html);
            }
            _this.html(this_html);
            _this.removeClass('ajax-loadin');
        });

        request.fail(function (jqXHR, textStatus) {
            _this.html(this_html);
            _this.removeClass('ajax-loadin');
        });
    }
    return false;

});

jQuery(document).on('click', '.lodmore-jobapps-btn', function (e) {
    e.preventDefault();
    var emp_id = jQuery('.jobsearch-allaplicants-holder').attr('data-eid');
    var usr_id = jQuery('.jobsearch-allaplicants-holder').attr('data-uid');
    var _this = jQuery(this),
            total_pages = _this.attr('data-tpages'),
            page_num = _this.attr('data-gtopage'),
            job_id = _this.attr('data-jid'),
            this_html = _this.html(),
            appender_con = jQuery('#job-apps-list' + job_id),
            ajax_url = jobsearch_plugin_vars.ajax_url;
    if (!_this.hasClass('ajax-loadin')) {
        _this.addClass('ajax-loadin');
        _this.html(this_html + ' <i class="fa fa-refresh fa-spin"></i>');

        total_pages = parseInt(total_pages);
        page_num = parseInt(page_num);
        var request = jQuery.ajax({
            url: ajax_url,
            method: "POST",
            data: {
                _job_id: job_id,
                page_num: page_num,
                emp_id: emp_id,
                usr_id: usr_id,
                action: 'jobsearch_empdash_load_more_apswith_apps_lis',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if ('undefined' !== typeof response.html) {
                page_num += 1;
                _this.attr('data-gtopage', page_num)
                if (page_num > total_pages) {
                    _this.parent('div').hide();
                }
                appender_con.append(response.html);
            }
            _this.html(this_html);
            _this.removeClass('ajax-loadin');
        });

        request.fail(function (jqXHR, textStatus) {
            _this.html(this_html);
            _this.removeClass('ajax-loadin');
        });
    }
    return false;

});

function jobsearch_alljobs_apps_count_load() {
    var ajax_url = jobsearch_plugin_vars.ajax_url;

    var emp_id = jQuery('.jobsearch-allaplicants-holder').attr('data-eid');
    var usr_id = jQuery('.jobsearch-allaplicants-holder').attr('data-uid');
    var request = jQuery.ajax({
        url: ajax_url,
        method: "POST",
        data: {
            doing: 'alljobs_apps_count',
            emp_id: emp_id,
            usr_id: usr_id,
            action: 'jobsearch_empdash_alljobs_apps_count_loadboxes',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if ('undefined' !== typeof response.appcounts) {
            jQuery('.overall-site-aplicnts').html(response.appcounts);
            jQuery('.overall-site-shaplicnts').html(response.shappcounts);
            jQuery('.overall-site-rejaplicnts').html(response.rejappcounts);
        }
    });

    request.fail(function (jqXHR, textStatus) {
        jQuery('.overall-site-aplicnts').html('0');
        jQuery('.overall-site-shaplicnts').html('0');
        jQuery('.overall-site-rejaplicnts').html('0');
    });
    return false;
}
//

//
// Email Applicants Script
// Start
//

jQuery('.job_postemil_cajax_field').on('click', function (e) {
    e.preventDefault();
    var emp_id = jQuery('.jobsearch-allaplicants-holder').attr('data-eid');
    var usr_id = jQuery('.jobsearch-allaplicants-holder').attr('data-uid');
    var this_id = jQuery(this).data('randid'),
            loaded = jQuery(this).data('loaded'),
            posttype = jQuery(this).data('posttype'),
            custom_field = jQuery('#custom_post_field_' + this_id),
            ajax_url = jobsearch_plugin_vars.ajax_url,
            force_std = jQuery(this).data('forcestd');
    if (loaded != true) {
        jQuery('.custom_post_loader_' + this_id).html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: ajax_url,
            method: "POST",
            data: {
                force_std: force_std,
                posttype: posttype,
                emp_id: emp_id,
                usr_id: usr_id,
                action: 'jobsearch_empdash_load_email_apswith_job_posts',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if ('undefined' !== typeof response.html) {
                custom_field.html(response.html);
                jQuery('.custom_post_loader_' + this_id).html('');
                custom_field.data('loaded', true);
            }
        });

        request.fail(function (jqXHR, textStatus) {
        });
    }
    return false;

});

jQuery(document).on('change', 'select[name="email_jobs_wapps_selctor"]', function (e) {
    e.preventDefault();
    var emp_id = jQuery('.jobsearch-allaplicants-holder').attr('data-eid');
    var usr_id = jQuery('.jobsearch-allaplicants-holder').attr('data-uid');
    var _this = jQuery(this),
            loaderr = _this.parent('.allapps-jobselct-con').find('span'),
            job_id = _this.val(),
            appender_con = jQuery('.jobsearch-all-aplicantslst'),
            ajax_url = jobsearch_plugin_vars.ajax_url;
    if (job_id != '') {
        _this.addClass('ajax-loadin');
        loaderr.html('<i class="fa fa-refresh fa-spin"></i>');

        var request = jQuery.ajax({
            url: ajax_url,
            method: "POST",
            data: {
                _job_id: job_id,
                emp_id: emp_id,
                usr_id: usr_id,
                action: 'jobsearch_empdash_load_single_eapswith_job_inlist',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if ('undefined' !== typeof response.html) {
                //
                appender_con.html(response.html);
                jQuery('.lodmoreemil-apps-btnsec').hide();
            }
            loaderr.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            loaderr.html('');
        });
    }
    return false;

});

jQuery('.lodmoreemil-apps-btn').on('click', function (e) {
    e.preventDefault();
    var emp_id = jQuery('.jobsearch-allaplicants-holder').attr('data-eid');
    var usr_id = jQuery('.jobsearch-allaplicants-holder').attr('data-uid');
    var _this = jQuery(this),
            total_pages = _this.attr('data-tpages'),
            page_num = _this.attr('data-gtopage'),
            this_html = _this.html(),
            appender_con = jQuery('.jobsearch-all-aplicantslst'),
            ajax_url = jobsearch_plugin_vars.ajax_url;
    if (!_this.hasClass('ajax-loadin')) {
        _this.addClass('ajax-loadin');
        _this.html(this_html + ' <i class="fa fa-refresh fa-spin"></i>');

        total_pages = parseInt(total_pages);
        page_num = parseInt(page_num);
        var request = jQuery.ajax({
            url: ajax_url,
            method: "POST",
            data: {
                page_num: page_num,
                emp_id: emp_id,
                usr_id: usr_id,
                action: 'jobsearch_empdash_load_email_apswith_job_apps',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if ('undefined' !== typeof response.html) {
                page_num += 1;
                _this.attr('data-gtopage', page_num)
                if (page_num > total_pages) {
                    _this.parent('div').hide();
                }
                appender_con.append(response.html);
            }
            _this.html(this_html);
            _this.removeClass('ajax-loadin');
        });

        request.fail(function (jqXHR, textStatus) {
            _this.html(this_html);
            _this.removeClass('ajax-loadin');
        });
    }
    return false;

});

jQuery(document).on('click', '.lodmoreemil-jobapps-btn', function (e) {
    e.preventDefault();
    var emp_id = jQuery('.jobsearch-allaplicants-holder').attr('data-eid');
    var usr_id = jQuery('.jobsearch-allaplicants-holder').attr('data-uid');
    var _this = jQuery(this),
            total_pages = _this.attr('data-tpages'),
            page_num = _this.attr('data-gtopage'),
            job_id = _this.attr('data-jid'),
            this_html = _this.html(),
            appender_con = jQuery('#job-apps-list' + job_id),
            ajax_url = jobsearch_plugin_vars.ajax_url;
    if (!_this.hasClass('ajax-loadin')) {
        _this.addClass('ajax-loadin');
        _this.html(this_html + ' <i class="fa fa-refresh fa-spin"></i>');

        total_pages = parseInt(total_pages);
        page_num = parseInt(page_num);
        var request = jQuery.ajax({
            url: ajax_url,
            method: "POST",
            data: {
                _job_id: job_id,
                page_num: page_num,
                emp_id: emp_id,
                usr_id: usr_id,
                action: 'jobsearch_empdash_load_email_apswith_apps_lis',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if ('undefined' !== typeof response.html) {
                page_num += 1;
                _this.attr('data-gtopage', page_num)
                if (page_num > total_pages) {
                    _this.parent('div').hide();
                }
                appender_con.append(response.html);
            }
            _this.html(this_html);
            _this.removeClass('ajax-loadin');
        });

        request.fail(function (jqXHR, textStatus) {
            _this.html(this_html);
            _this.removeClass('ajax-loadin');
        });
    }
    return false;

});

function jobsearch_alljobs_apps_count_load_email() {
    var ajax_url = jobsearch_plugin_vars.ajax_url;

    var emp_id = jQuery('.jobsearch-allaplicants-holder').attr('data-eid');
    var usr_id = jQuery('.jobsearch-allaplicants-holder').attr('data-uid');
    var request = jQuery.ajax({
        url: ajax_url,
        method: "POST",
        data: {
            doing: 'alljobs_apps_count',
            emp_id: emp_id,
            usr_id: usr_id,
            action: 'jobsearch_empdash_emailjobs_apps_count_loadboxes',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if ('undefined' !== typeof response.appcounts) {
            jQuery('.overall-site-aplicnts').html(response.appcounts);
        }
    });

    request.fail(function (jqXHR, textStatus) {
        jQuery('.overall-site-aplicnts').html('0');
    });
    return false;
}
//
// Email Applicants Script
// End
//

jQuery(document).on('click', '.jobsearch-duplict-cusjob', function () {

    var _this = jQuery(this);
    var origjob_id = _this.attr('data-id');
    var this_classes = _this.attr('class');

    if (!_this.hasClass('ajax-loding')) {
        _this.attr('class', 'fa fa-refresh fa-spin jobsearch-duplict-cusjob ajax-loding');

        var request = jQuery.ajax({
            url: jobsearch_dashboard_vars.ajax_url,
            method: "POST",
            data: {
                origjob_id: origjob_id,
                action: 'jobsearch_add_duplicate_post_byuser',
            },
            dataType: "json"
        });

        request.done(function (response) {
            if ('undefined' !== typeof response.duplicate && response.duplicate == '1') {
                window.location.reload(true);
                return false;
            }
            _this.attr('class', this_classes);
        });

        request.fail(function (jqXHR, textStatus) {
            _this.attr('class', this_classes);
        });
    }
});

jQuery('.jobsearch-applics-filterbtn').click(function () {
    if (!jQuery('.jobsearch-applics-filterscon').hasClass('animate-filters-open')) {
        //jQuery('.jobsearch-applics-filterscon').removeAttr('style');
        jQuery('.jobsearch-applics-filterscon').addClass('animate-filters-open');
    }
});

jQuery('.applicfilters-sideclose-btn').click(function () {
    jQuery('.jobsearch-applics-filterscon').removeClass('animate-filters-open');
    //jQuery('.jobsearch-applics-filterscon').hide();
});

jQuery('.email-detailbox-btn').on('click', function () {
    var _this = jQuery(this);
    var email_id = _this.attr('data-id');
    var ststr = _this.attr('data-ststr');
    var ajax_url = jobsearch_plugin_vars.ajax_url;
    jobsearch_modal_popup_open('JobSearchModalEmailLog' + email_id);
    jQuery('#email-status-' + email_id).html(ststr);

    jQuery.ajax({
        url: ajax_url,
        method: "POST",
        data: {
            email_id: email_id,
            action: 'jobsearch_userdash_change_email_read_status',
        },
        dataType: "json"
    });
});

jQuery('.user-email-field').on('change', function () {
    var _this = jQuery(this);
    var email_val = _this.val();
    var this_parent = _this.parent('li');
    var loder_con = this_parent.find('.chk-loder');
    var msg_con = this_parent.find('.email-chek-msg');
    
    var ajax_url = jobsearch_plugin_vars.ajax_url;
    msg_con.hide();
    loder_con.html('<i class="fa fa-refresh fa-spin"></i>');
    var request = jQuery.ajax({
        url: ajax_url,
        method: "POST",
        data: {
            email: email_val,
            action: 'jobsearch_user_change_email_check_avail',
        },
        dataType: "json"
    });
    request.done(function (response) {
        if ('undefined' !== typeof response.msg && response.msg != '') {
            msg_con.removeAttr('style');
            if (response.error == '1') {
                msg_con.html('<span class="error-msg">' + response.msg + '</span>');
            } else {
                msg_con.html('<span class="success-msg">' + response.msg + '</span>');
            }
        }
        loder_con.html('');
    });

    request.fail(function (jqXHR, textStatus) {
        loder_con.html('');
    });
});