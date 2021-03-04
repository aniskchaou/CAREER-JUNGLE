var member_chat_list_member = jQuery(".jobsearch-chat-member-list-wrapper"), window_width = jQuery(window).width();

jQuery.fn.ignore = function (sel) {
    return this.clone().find(sel || ">*").remove().end();
};

function FrontChattriggerFile(user_id) {
    jQuery('.user-' + user_id + ' form[name="fileForm"] input:first').click();
}

function FrontChatuploadFileForm(form) {

    jQuery('.jobsearch-chat-no-messages').remove();
    var fileForm = jQuery(form).children('input[type=file]')[0].files[0],
        formId = jQuery(form).attr('id'),
        reg_ser_form = jQuery(form)[0],
        ext_not_allow = ['css', 'html', 'php', 'json'],
        formData = new FormData(reg_ser_form);

    if (fileForm.size > 10000000) {
        alert("cannot upload more than 10mb");
        return;
    }

    var chat_message = fileForm.name,
        last_element,
        chat_message_ext = chat_message.split('.');
    if (chat_message_ext != '') {
        last_element = chat_message_ext[chat_message_ext.length - 1];
        if (ext_not_allow.indexOf(last_element) != -1) {
            alert("File of this extension is not allowed");
            return;
        }
    }

    jQuery.ajax({
        type: 'POST',
        url: jobsearch_ajchat_vars.jobsearch_ajax_url,
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        datatype: 'json',
        beforeSend: function () {
            jQuery(".jobsearch-loading-section").removeClass("hidden");
        },
        success: function (r) {
            jQuery(".jobsearch-loading-section").addClass("hidden");
            scroll_event_front(jQuery(form).find('input[name=reciever_id]').val());
        }
    });
    $('form#' + formId).trigger("reset");
}

//jobsearch-chat-close
jQuery(document).on('click', '.jobsearch-chat-toggle-chat', function (e) {
    e.preventDefault();
    member_chat_list_member.attr('style', '')
    member_chat_list_member.fadeOut(800);
    jQuery(".jobsearch-chat-tab2").addClass("jobsearch-chat-is-toggled");
    jQuery(".jobsearch-chat-floating-chat").removeClass("hidden").hide().delay(100).fadeIn(500);
    localStorage.setItem("is_toggled", "yes");
    setTimeout(function () {
        jQuery('.jobsearch-chat-tabs-wrapper').find('.jobsearch-chat-box-open').last().addClass('jobsearch-chat-margin-right')
    }, 770)

});

//toggle chat view front
jQuery(document).on('click', '.jobsearch-chat-floating-chat', function (e) {
    e.preventDefault();
    jQuery(document).find('.jobsearch-chat-margin-right').removeClass('jobsearch-chat-margin-right')
    jQuery(".jobsearch-chat-floating-chat").addClass("hidden");
    member_chat_list_member.fadeIn(800).css({display: 'inline-block'});
    jQuery(".jobsearch-chat-tab2").removeClass("jobsearch-chat-is-toggled");
    localStorage.setItem("is_toggled", "no");
});

var chat_settings = [];
//load chat box front
jQuery(document).on('click', '.load-chat-box', function () {

    var _this = jQuery(this), _data,
        _receiver_id = jQuery(this).attr('data-user-id'),
        _find_load_box = jQuery(".user-" + _receiver_id),
        _user_name = _this.find('.jobsearch-chat-list-thumb a').ignore("small").text(),
        _user_img = _this.find('img').attr('src'),
        _window_width = jQuery(window).width(),
        _html, _chat_selector = _find_load_box.find('ul#float-chat');

    _this.find('small').addClass('hidden');
    _this.find('small').text(0);
    //
    _find_load_box.find('.jobsearch-chat-title').text(_user_name);
    _find_load_box.find('input[name=user_image]').val(_user_img);
    _find_load_box.find("input[name=reciever_id]").val(_receiver_id);

    if (_find_load_box.length > 0) {
        _this.addClass('active');
        _find_load_box.removeClass('hidden').hide().delay(100).fadeIn(800).addClass('jobsearch-chat-box-open');
    }

    if (_window_width < 600) {
        jQuery('.jobsearch-chat-member-list-wrapper').hide();
    }

    if (chat_settings.indexOf(_receiver_id) == -1) {
        chat_settings.push(_receiver_id);
        localStorage.setItem("chat_box", chat_settings);
    }

    if (jQuery(".user-" + _receiver_id).find("#float-chat li").length == 0) {
        jQuery.ajax({
            type: 'POST',
            url: jobsearch_ajchat_vars.jobsearch_ajax_url,
            data: {
                limit: 0,
                cache: true,
                reciever_id: _receiver_id,
                action: "jobsearch_chat_load_current_user_chat_front"
            },
            datatype: 'JSON',
            success: function (msg) {
                _data = JSON.parse(msg);
                _html = _data.html;
                _find_load_box.find(".notification-count").text(_data.unread_messages);
                jQuery(".jobsearch-loading-section").addClass("hidden");
                _chat_selector.html('');
                if ('' == _html) {
                    _html = '<li id="jobsearch-chat-no-messages">' + jobsearch_ajchat_vars.no_chat_message + '</li>';
                }
                _chat_selector.append(_html);
                scroll_event_front(_receiver_id)
            }
        });
    }
});


jQuery(document).on("keyup", "#search-field-front-full-view", function () {
    var _value = jQuery(this).val().toLowerCase();
    jQuery(document).find(".jobsearch-chat-users-list ul li").filter(function () {
        jQuery(this).toggle(jQuery(this).children('.user-info').find('h2.name').text().toLowerCase().indexOf(_value) > -1);
    });
});

jQuery(document).on("keyup", "#search_field_toggle", function () {
    var _value = jQuery(this).val().toLowerCase();
    jQuery(document).find(".jobsearch-chat-list-toggle-view ul li").filter(function () {
        jQuery(this).toggle(jQuery(this).children('.user-info').find('a.name').text().toLowerCase().indexOf(_value) > -1);
    });
});
//
jQuery(document).on('click', '.jobsearch-chat-emoji-picker-select', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var _this = jQuery(this), _user_id = _this.attr('data-user-id'),
        _selector = jQuery('.jobsearch-chat-user-emoji-' + _user_id);
    _selector.fadeToggle(500);
})

jQuery(".jobsearch-emoji").on('click', function () {
    var _this = jQuery(this), _emoji_val = _this.attr('data-val'), _user_id = _this.attr('data-user-id');
    var box = jQuery(".user-" + _user_id).find('.messageForm').find('input[name=message]');
    box.val(box.val() + _emoji_val);
});

jQuery('.jobsearch-chat-emojis-box').click(function (e) {
    e.stopPropagation();
});

jQuery('body').click(function () {
    jQuery('.jobsearch-chat-emojis-box').hide();
});

jQuery(document).on('click', '.jobsearch-chat-close', function () {
    //
    var _this = jQuery(this), user_id = _this.attr('data-user-id'), _total_user_id;
    var total_active_popup = localStorage.getItem("chat_box");
    _total_user_id = total_active_popup.split(',');
    chat_settings = [];
    for (var i = 0; i < _total_user_id.length; i++) {
        if (_total_user_id[i] != user_id) {
            chat_settings.push(_total_user_id[i])
        }
        localStorage.setItem("chat_box", '');
        localStorage.setItem("chat_box", chat_settings);
    }

    jQuery(".user-" + user_id).fadeOut(600).attr('style', 'display: inline-block').removeClass('jobsearch-chat-box-open');
    setTimeout(function () {
        if (!jQuery(".jobsearch-chat-member-list-wrapper").is(":visible")) {
            jQuery('.jobsearch-chat-tabs-wrapper').find('.jobsearch-chat-box-open').last().addClass('jobsearch-chat-margin-right')
        }
        if (window_width < 600) {
            member_chat_list_member.attr('style', '');
            member_chat_list_member.css({display: 'inline-block'});
        }
    }, 600)
});
//
jQuery(document).on('click', '.jobsearch-chat-candidate-openbox', function (e) {
    var _this = jQuery(this);
    jQuery(".jobsearch-chat-front-user-" + _this.attr('data-cand-user-id')).click();
    if (!jQuery(".jobsearch-chat-member-list-wrapper").is(":visible")) {
        jQuery(".user-" + _this.attr('data-cand-user-id')).addClass("jobsearch-chat-margin-right");
    }
});
//
jQuery(document).on('click', '.jobsearch-chat-employer-openbox', function (e) {
    var _this = jQuery(this);
    jQuery(".jobsearch-chat-front-user-" + _this.attr('data-emp-user-id')).click();

    if (!jQuery(".jobsearch-chat-member-list-wrapper").is(":visible")) {
        jQuery(".user-" + _this.attr('data-emp-user-id')).addClass("jobsearch-chat-margin-right");
    }
});
//
jQuery(document).on('click', '.jobsearch-chat-candidate-add', function () {
    var _this = jQuery(this),
        _user_id = _this.attr('data-cand-id'),
        this_html = _this.html();
    if (!_this.hasClass('ajax-loading')) {
        _this.addClass('ajax-loading');
        _this.html(this_html + '<i class="fa fa-refresh fa-spin"></i>');
        jQuery.ajax({
            type: 'POST',
            url: jobsearch_ajchat_vars.jobsearch_ajax_url,
            data: {
                cand_id: _user_id,
                action: 'jobsearch_chat_add_to_chat_cand'
            },
            cache: true,
            datatype: 'JSON',
            success: function (msg) {
                _this.removeClass('ajax-loading');
                location.reload();
            }
        });
    }
});

jQuery(document).on('click', '.jobsearch-chat-emp-add', function () {
    var _this = jQuery(this),
        _user_id = _this.attr('data-emp-id'),
        this_html = _this.html();
    if (!_this.hasClass('ajax-loading')) {
        _this.addClass('ajax-loading');
        _this.html(this_html + '<i class="fa fa-refresh fa-spin"></i>');
        jQuery.ajax({
            type: 'POST',
            url: jobsearch_ajchat_vars.jobsearch_ajax_url,
            data: {
                emp_id: _user_id,
                action: 'jobsearch_chat_add_to_chat_emp'
            },
            cache: true,
            datatype: 'JSON',
            success: function (msg) {
                _this.removeClass('ajax-loading');
                location.reload();
            }
        });
    }
});

// load chat settings toggle view
jQuery(document).on('click', '.jobsearch-chat-user-settings-btn', function (e) {
    e.preventDefault();
    jQuery(".jobsearch-chat-user-settings-con").show();
});

//
function chat_enble_disble_chat(data) {

    var _URL = jobsearch_ajchat_vars.jobsearch_ajax_url, _res, _chckbox_val,
        _checked_val = jQuery(data).prop("checked") == true ? 1 : 0,
        _data = {
            val: _checked_val,
            action: 'jobsearch_chat_enable_disable_chat',
        };
    //$(this).prop("checked") == true
    console.info(jQuery(data).prop("checked"));
    jQuery.ajax({
        type: 'POST',
        url: _URL,
        data: _data,
        datatype: 'json',
        beforeSend: function () {

        },
        success: function (msg) {
            _res = JSON.parse(msg);
            jQuery(".jobsearch-chat-user-result-msg").html('');
            if (_res.response == 1) {
                _chckbox_val = 0;
            } else {
                _chckbox_val = 1;
            }
            //
            jQuery(data).attr('value', _chckbox_val);
            if (_checked_val != 1) {
                jQuery(".jobsearch-chat-user-result-msg").append('<p>' + jobsearch_ajchat_vars.chat_enable_msg + '</p>')
                setTimeout(function () {
                    jQuery(".jobsearch-chat-user-result-msg").html('');
                }, 4000)
            } else {
                jQuery(".jobsearch-chat-user-result-msg").append('<p>' + jobsearch_ajchat_vars.chat_disable_msg + '</p>')
                setTimeout(function () {
                    jQuery(".jobsearch-chat-user-result-msg").html('');
                }, 4000)
            }

        }
    });
}

jQuery(document).on('click', '.jobsearch-chat-close-settings', function (e) {
    e.preventDefault();
    jQuery(".jobsearch-chat-user-settings-con").hide();

});
//load chat message front
jQuery(document).on('click', '.jobsearch-chat-front-send-message', function (e) {
    e.preventDefault();
    var _html,
        _user_id = jQuery(this).attr('data-user-id'),
        _selector = jQuery(".user-" + _user_id + " #float-chat li"),
        _message = jQuery(".user-" + _user_id + " form.messageForm input[name=message]"),
        _no_message = jQuery(".user-" + _user_id + " #jobsearch-chat-no-messages"), _data = {
            message: _message.val(),
            sender_id: jQuery(".user-" + _user_id + " form.messageForm input[name=sender_id]").val(),
            sender_image: jQuery(".user-" + _user_id + " form.messageForm input[name=sender_image]").val(),
            receiver_image: jQuery(".user-" + _user_id + " form.messageForm input[name=receiver_image]").val(),
            reciever_id: jQuery(".user-" + _user_id + " form.messageForm input[name=reciever_id]").val(),
        };
    jQuery('.jobsearch-chat-user-emoji-' + _user_id).hide();
    if (_no_message.length > 0) {
        _no_message.remove();
    }
    if (_message.val() == '') {
        _message.addClass('jobsearch-chat-error');
        return;
    } else {
        _message.removeClass('jobsearch-chat-error');
    }
    //
    _html = '<li class="conversation-me"><span><img src="' + _data.sender_image + '"></span><div class="jobsearch-chat-user-events"><a href="javascript:void(0)" class="jobsearch-chat-del-message" data-chat-id=""><i class="chat-icon chat-trash-fill"></i></a></div><div class="jobsearch-chat-list-thumb"><p>' + _data.message.replace(/(<([^>]+)>)/ig, "") + '</p><small class="jobsearch-chat-msg-time">' + jobsearch_ajchat_vars.is_today + ' ' + formatAMPM(new Date) + '</small></div></li>';
    if (_message.val().indexOf('youtube.com/watch?') == -1 && _message.val().indexOf('dailymotion.com/video/') == -1 && _message.val().indexOf('vimeo') == -1) {
        if (_selector.length != 1) {
            _selector.last().after(_html);
        } else {
            jQuery(".user-" + _user_id + " #float-chat").append(_html);
        }
    }

    scroll_event_front(_user_id);
    setTimeout(function () {
        jQuery(".user-" + _user_id + " form.messageForm input[name=message]").val("")
    }, 100);
    jobsearch_front_chat_msg(jQuery(this))
})

function jobsearch_front_chat_msg(_this) {
    playPushSound();

    var _user_id = _this.attr('data-user-id'),
        _html,
        _btn = jQuery(".user-" + _user_id + " .jobsearch-chat-send-message"),
        _message = jQuery(".user-" + _user_id + " form.messageForm input[name=message]"),
        _selector = jQuery(".user-" + _user_id + " #float-chat li"),
        _form_data = {
            message: _message.val(),
            sender_id: jQuery(".user-" + _user_id + " form.messageForm input[name=sender_id]").val(),
            sender_image: jQuery(".user-" + _user_id + " form.messageForm input[name=sender_image]").val(),
            receiver_image: jQuery(".user-" + _user_id + " form.messageForm input[name=receiver_image]").val(),
            reciever_id: jQuery(".user-" + _user_id + " form.messageForm input[name=reciever_id]").val(),
            action: 'jobsearch_chat_send_message',
        }, _res,
        _URL = jobsearch_ajchat_vars.jobsearch_ajax_url;

    if (_form_data.message != '') {

        jQuery.ajax({
            type: 'POST',
            url: _URL,
            data: _form_data,
            datatype: 'json',
            beforeSend: function () {
                _btn.attr('disabled');
            },
            success: function (res) {
                _res = JSON.parse(res);
                _btn.removeAttr('disabled');
                if (_res.is_video != "") {
                    _html = '<li class="conversation-me"><span><img src="' + _form_data.user_img + '"></span><div class="jobsearch-chat-list-thumb"><p>' + _res.is_video + '</p><small class="jobsearch-chat-msg-time">' + jobsearch_ajchat_vars.is_today + ' ' + formatAMPM(new Date) + '<a href="javascript:void(0)" data-chat-id="" class="jobsearch-color jobsearch-chat-del-message">' + jobsearch_ajchat_vars.del_message + '</a></small></div></li>';
                    if (_selector.length != 1) {
                        _selector.last().after(_html);
                    } else {
                        jQuery(".user-" + _user_id + " #float-chat").append(_html);
                    }
                }
                if (jQuery(".user-" + _user_id).find("#float-chat li").last().find(".jobsearch-chat-list-thumb").length > 0) {
                    jQuery(".user-" + _user_id).find("#float-chat li").last().find(".jobsearch-chat-list-thumb").addClass("chat-" + _res.last_id)
                    jQuery(".user-" + _user_id).find("#float-chat li").last().find("a").attr('data-chat-id', _res.last_id)
                }
            }
        });
    }
}

function loadFloatMessages(el, user_id) {
    var _topOfScroll = jQuery(el).scrollTop(), _total_msgs, _data, _html, _loader_html,
        _no_message_loader = jQuery(document).find('.user-' + user_id + ' #float-chat #jobsearch-chat-no-messages'),
        _msg_loader_selector = jQuery(document).find('.user-' + user_id + ' #float-chat .jobsearch-chat-loader');
    if (_topOfScroll == 0 && jQuery(el).attr('data-msgs-end') == 'false') {
        _total_msgs = jQuery(document).find('.user-' + user_id + ' #float-chat li').length;
        //
        _loader_html = '<li><div class="jobsearch-chat-loader"><span>' + jobsearch_ajchat_vars.loading + '<small class="jobsearch-chat-loader-dots">...</small></span></div></li>';
        if (!jQuery(el).hasClass('ajax-chat-loading')) {
            jQuery(el).addClass('ajax-chat-loading');
            jQuery(_loader_html).insertBefore(".user-" + user_id + " #float-chat li:first-child");

            //
            jQuery.ajax({
                type: 'POST',
                url: jobsearch_ajchat_vars.jobsearch_ajax_url,
                data: {
                    reciever_id: jQuery('.user-' + user_id + ' form.messageForm').find('input[name=reciever_id]').val(),
                    limit: _total_msgs,
                    action: 'jobsearch_chat_load_current_user_chat_front'
                },
                cache: true,
                datatype: 'HTML',
                success: function (msg) {
                    _data = JSON.parse(msg);
                    _html = _data.html;
                    jQuery(".jobsearch-chat-loader").remove();
                    if ('' == _html) {
                        jQuery(el).attr('data-msgs-end', 'true');
                        _html = '<li id="jobsearch-chat-first-msg-date">' + _data.first_msg_date + '</li>';
                    }
                    jQuery(".jobsearch-loading-section").addClass("hidden");
                    if (_no_message_loader.length == 0) {
                        jQuery(_html).insertBefore(".user-" + user_id + " #float-chat li:first-child");
                    }
                    if ('' != _html) {
                        jQuery(el).scrollTop(200);
                    }
                    jQuery(el).removeClass('ajax-chat-loading')
                }
            });
        }
    }
}

function scroll_event_front(user_id = '') {
    var chat = jQuery(".user-" + user_id + " .jobsearch-chat-scroll");
    if (chat.is(':visible') && chat.hasScrollBar()) chat.scrollTop(chat[0].scrollHeight);
}

// sound events
var getSound = new Audio(jobsearch_ajchat_vars.jobsearch_plugin_url + 'includes/sound/push.mp3');
var isSoundMuted = '';

function playChatSound() {
    return $('<audio class="sound-player" autoplay="autoplay" ' + isSoundMuted + ' style="display:none;">' + '<source src="' + arguments[0] + '" />' + '<embed src="' + arguments[0] + '" hidden="true" autostart="true" loop="false"/>' + '</audio>').appendTo('body');
}

function stopSound() {
    setTimeout(function () {
        jQuery(".sound-player").remove();
    }, 1000);
}

function playPushSound() {
    playChatSound(getSound.src);
    stopSound();
}

var typing = false;
jQuery(document).on('keypress', 'input[name=message]', function (e) {
    if (jQuery(this).hasClass("jobsearch-chat-error") == 1) {
        jQuery(this).removeClass("jobsearch-chat-error");
    }
    var key = e.which;
    if (key == 13)  // the enter key code
    {
        jQuery(this).parent('form').find('.jobsearch-chat-front-send-message').click();
        return false;
    }
});
var span = document.getElementsByClassName('jobsearch-chat-loader-dots');
var int = setInterval(function () {
    if ((span.innerHTML += '.').length == 4)
        span.innerHTML = '';
}, 200);
//
(function ($) {

    if (localStorage.getItem("is_toggled") == "yes") {
        member_chat_list_member.attr('style', '');
        member_chat_list_member.css({display: "none"});
        jQuery('.jobsearch-chat-floating-chat').removeClass('hidden')
    } else {

        member_chat_list_member.attr('style', '');
        member_chat_list_member.css({display: 'inline-block'});
        jQuery('.jobsearch-chat-floating-chat').hide().addClass('hidden')
    }

    var total_active_popup = localStorage.getItem("chat_box"), user_id;
    if (total_active_popup == null) return;
    user_id = total_active_popup.split(',');
    //
    for (var i = 0; i < user_id.length; i++) {
        jQuery(".jobsearch-chat-front-user-" + user_id[i]).trigger("click")
    }


    if (!jQuery(".jobsearch-chat-member-list-wrapper").is(":visible")) {
        jQuery('.jobsearch-chat-tabs-wrapper').find('.jobsearch-chat-box-open').last().addClass('jobsearch-chat-margin-right')
    }

})(jQuery);