jQuery.fn.ignore = function (sel) {
    return this.clone().find(sel || ">*").remove().end();
};
//
jQuery(window).on('load', function () {
    if (jQuery('.jobsearch-chat-emoji-picker').length > 0) {
        jQuery('.jobsearch-chat-emoji-picker').lsxEmojiPicker({
            closeOnSelect: true,
            twemoji: true,
            width: 330,
            onSelect: function (emoji) {
                var txt = $.trim(emoji.value), _emoji_string;
                _emoji_string = txt.replace('&#', '0');
                var _html = jQuery("textarea[name=message]").html();
                var box = $("textarea[name=message]");
                box.val(box.val() + String.fromCodePoint(_emoji_string));
                //
            }
        });
    }
});
(function ($) {

    var user_id = localStorage.getItem('load_user_chat_backend'),
        user_chat_selector = jobsearch_ajchat_vars.is_admin == true ? jQuery(".jobsearch-chat-user-" + user_id) : jQuery(".jobsearch-chat-user-" + user_id + ':first-child .jobsearch-load-user-chat');

    if (user_id != null) {
        setTimeout(function () {
            //user_chat_selector.addClass("active");
            user_chat_selector.click();
        }, 100)
    }

    jQuery('.jobsearch-chat-user-list').scrollTop(0);
    jQuery.fn.hasScrollBar = function () {
        return this.get(0).scrollHeight > this.get(0).clientHeight;
    };

    jQuery("#chat, .jobsearch-chat-user-employer, .jobsearch-chat-user-candidate").niceScroll({
        cursorborder: "",
        cursorcolor: "#13b5ea",
        boxzoom: false,
        //cursorminheight: 100
    });
    //

})(jQuery);
//
jQuery(document).on('change', 'form[name="fileForm"] input[type=file]', function () {
    jQuery(this).parent('form').submit();
});

var endofscroll = true;
jQuery(document).on("keyup", "#jobsearch-chat-container  #search_field", function () {
    var _value = jQuery(this).val().toLowerCase(), _user_type = jQuery(this).attr('data-user-type'), _selector;
    if (_value.length >= 1) {
        jQuery.ajax({
            type: 'POST',
            url: jobsearch_ajchat_vars.jobsearch_ajax_url,
            data: {
                string: _value,
                user_type: _user_type,
                get_all_users: "false",
                action: 'jobsearch_chat_get_users_by_key'
            },
            datatype: 'HTML',
            success: function (msg) {
                _selector = _user_type == 'emp' ? jQuery('.jobsearch-chat-user-employer').find('ul') : jQuery('.jobsearch-chat-user-candidate').find('ul');
                _selector.html('');
                _selector.append(msg)

            }
        });
    } else {
        jQuery.ajax({
            type: 'POST',
            url: jobsearch_ajchat_vars.jobsearch_ajax_url,
            data: {
                user_type: _user_type,
                get_all_users: "true",
                action: 'jobsearch_chat_get_users_by_key'
            },
            datatype: 'HTML',
            success: function (msg) {
                _selector = _user_type == 'emp' ? jQuery('.jobsearch-chat-user-employer').find('ul') : jQuery('.jobsearch-chat-user-candidate').find('ul');
                _selector.html('');
                _selector.append(msg)

            }
        });
    }
});

jQuery(document).on('click', '.jobsearch-chat-all-users', function () {
    var _user_type = jQuery('#search_field').attr('data-user-type'), _selector;

    jQuery('#search_field').val('');

    jQuery.ajax({
        type: 'POST',
        url: jobsearch_ajchat_vars.jobsearch_ajax_url,
        data: {
            user_type: _user_type,
            get_all_users: "true",
            action: 'jobsearch_chat_get_users_by_key'
        },

        datatype: 'HTML',
        success: function (msg) {
            _selector = _user_type == 'emp' ? jQuery('.jobsearch-chat-user-employer').find('ul') : jQuery('.jobsearch-chat-user-candidate').find('ul');
            _selector.html('');
            _selector.append(msg)

        }
    });
});

function loadMessages(el) {
    var _topOfScroll = jQuery("#chat").scrollTop(), _total_msgs, _data, _html, _loader_html;

    if (_topOfScroll == 0 && endofscroll == true) {
        _total_msgs = jQuery(document).find('ul#chat li').length;
        //
        _loader_html = '<li><div class="jobsearch-chat-loader"><span>' + jobsearch_ajchat_vars.loading + '<small class="jobsearch-chat-loader-dots">...</small></span></div></li>';
        if (!jQuery(el).hasClass('ajax-chat-loading')) {
            jQuery(el).addClass('ajax-chat-loading')
            jQuery(_loader_html).insertBefore("#chat li:first-child");
            jQuery.ajax({
                type: 'POST',
                url: jobsearch_ajchat_vars.jobsearch_ajax_url,
                data: {
                    reciever_id: jQuery('input[name=reciever_id]').val(),
                    limit: _total_msgs,
                    action: 'jobsearch_chat_load_current_user_chat'
                },

                datatype: 'HTML',
                success: function (msg) {

                    jQuery(el).removeClass('ajax-chat-loading')
                    _data = JSON.parse(msg);
                    _html = _data.html;
                    jQuery(".jobsearch-chat-loader").remove();
                    if (false == _data.html) {
                        endofscroll = false;
                        _html = '<li id="jobsearch-chat-first-msg-date">' + _data.first_msg_date + '</li>';
                        jQuery(_html).insertBefore("#chat li:first-child");
                    }

                    jQuery(".jobsearch-chat-user-" + _data.reciever_id + " .jobsearch-chat-unread-message").text(_data.unread_messages);
                    if (_data.html != false) {
                        jQuery(_html).insertBefore("#chat li:first-child");
                        jQuery(el).scrollTop(200);
                    }
                }
            });
        }
    }
}

var scrollTopflag = false,
    scrollTopCand = jQuery("input[name=scrollTop-cand]").val(),
    scrollTopEmp = jQuery("input[name=scrollTop-emp]").val();

jQuery("#jobsearch-chat-container  #search_field").on('click', function () {
    endofscroll = false;
});
jQuery("#jobsearch-chat-container  #search_field").on('focusout', function () {
    endofscroll = true;
});

function slidechat(id) {
    jQuery(".user-" + id + " .jobsearch-chat-floating-chat-list").slideToggle("fast")
}

function loadUsers(el) {
    var _emp_user_list = jQuery('.jobsearch-chat-user-employer'),
        _offset_emp = jQuery('input[name=employer-user-offset]'),
        _offset_cand = jQuery('input[name=candidate-user-offset]');

    if ((jQuery(el).scrollTop() + jQuery(el).innerHeight() >= jQuery(el)[0].scrollHeight) && endofscroll == true) {
        scrollTopEmp = parseInt(scrollTopEmp);
        if (!_emp_user_list.hasClass("hidden")) {
            jQuery.ajax({
                type: 'POST',
                url: jobsearch_ajchat_vars.jobsearch_ajax_url,
                data: {
                    offset: _offset_emp.val(),
                    action: 'jobsearch_chat_load_next_employers'
                },
                datatype: 'html',
                success: function (msg) {
                    if ('' == msg) {
                        endofscroll = false;
                    }

                    scrollTopEmp = jQuery(el)[0].scrollHeight - parseInt(700);
                    scrollTopflag = true;
                    _offset_emp.val(parseInt(_offset_emp.val()) + parseInt(50));
                    jQuery(msg).insertAfter(".jobsearch-chat-user-employer ul li:last-child");
                    jQuery(el).scrollTop(scrollTopEmp);
                }
            });
        } else {
            scrollTopCand = parseInt(scrollTopCand);
            jQuery.ajax({
                type: 'POST',
                url: jobsearch_ajchat_vars.jobsearch_ajax_url,
                data: {
                    offset: _offset_cand.val(),
                    action: 'jobsearch_chat_load_next_candidate'
                },
                datatype: 'html',
                success: function (msg) {
                    if ('' == msg) {
                        endofscroll = false;
                    }
                    scrollTopCand = jQuery(el)[0].scrollHeight - parseInt(700);
                    scrollTopflag = true;
                    _offset_cand.val(parseInt(_offset_cand.val()) + parseInt(50));
                    jQuery(msg).insertAfter(".jobsearch-chat-user-candidate ul li:last-child");
                    jQuery(el).scrollTop(scrollTopCand);
                }
            });
        }
    }
}

/*
* Chat events
* */
var isSoundMuted = '';
jQuery(document).on('click', '.disableSound', function () {
    if (isSoundMuted == '') {
        isSoundMuted = 'muted';
        jQuery(this).toggleClass("chat-icon chat-speaker chat-icon chat-mute");
    } else if (isSoundMuted == 'muted') {
        jQuery(this).toggleClass("chat-icon chat-mute chat-icon chat-speaker");
        isSoundMuted = '';
    }
});
//
jQuery(document).on('click', '.jobsearch-chat-delete-user', function () {
    var _user_id = jQuery(this).attr('data-user-id'), _user_selector_id = jQuery(this).attr('data-selector-id');
    jQuery('.jobsearch-chat-user-' + _user_selector_id).remove();
    jQuery.ajax({
        type: 'POST',
        url: jobsearch_ajchat_vars.jobsearch_ajax_url,
        data: {
            f_user_id: _user_id,
            user_real_id: _user_selector_id,
            action: 'jobsearch_chat_delete_user_friend'
        },
        datatype: 'html',
        success: function (msg) {

        }
    });
});

jQuery(document).on('click', '.jobsearch-chat-emoji-picker-select-full-view', function () {
    jQuery('.jobsearch-chat-emojis-box').fadeToggle(500)
});

jQuery(".jobsearch-emoji").on('click', function () {
    var _this = jQuery(this), _emoji_val = _this.attr('data-val');
    var box = jQuery('textarea[name=message]');
    box.val(box.val() + _emoji_val);
});

jQuery('.jobsearch-chat-emoji-picker-select-full-view').click(function (e) {
    e.preventDefault();
    e.stopPropagation();
    jQuery('.jobsearch-chat-emojis-box').toggle();
});

jQuery('.jobsearch-chat-emojis-box').click(function (e) {
    e.stopPropagation();
});

jQuery('body').click(function () {
    jQuery('.jobsearch-chat-emojis-box').hide();
});

jQuery(document).on('click', '.jobsearch-chat-send-message', function () {
    var _html, _selector = jQuery("#chat li"), _user_img, _message = jQuery("textarea[name=message]"),
        _no_message = jQuery("#jobsearch-chat-no-messages"), _data = {
            message: _message.val(),
            sender_id: jQuery("input[name=sender_id]").val(),
            reciever_id: jQuery("input[name=reciever_id]").val(),
            sender_image: jQuery("input[name=sender_image]").val(),
            receiver_image: jQuery("input[name=receiver_image]").val(),
        };
    jQuery('.jobsearch-chat-emojis-box').hide();
    if (_no_message.length > 0) {
        _no_message.remove();
    }
    if (_message.val() == '') {
        _message.addClass('jobsearch-chat-error');
        return;
    } else {
        _message.removeClass('jobsearch-chat-error');
    }
    _message.html('');
    //
    if (_message.val().indexOf('youtube.com/watch?') == -1 && _message.val().indexOf('dailymotion.com/video/') == -1 && _message.val().indexOf('vimeo') == -1) {
        _html = '<li class="you "><img src="' + _data.sender_image + '"><div class="jobsearch-chat-user-events"><a href="javascript:void(0)" class="jobsearch-chat-del-message" data-chat-id=""><i class="chat-icon chat-trash-fill"></i></a></div><div class="jobsearch-chat-user-events"><a href="javascript:void(0)" class="jobsearch-chat-del-message" data-chat-id=""><i class="chat-icon chat-trash-fill"></i></a></div><div class="jobsearch-chat-entete-wrapper"><p>' + _data.message.replace(/(<([^>]+)>)/ig, "") + '</p><div class="jobsearch-chat-entete"><h3>' + jobsearch_ajchat_vars.is_today + ' ' + formatAMPM(new Date) + '</h3><a href="javascript:void(0)" class="jobsearch-color jobsearch-chat-seen hidden">' + jobsearch_ajchat_vars.is_seen + '</a></div></div></li>';

        if (_selector.length != 1) {
            _selector.last().after(_html);
        } else {
            jQuery("#chat").append(_html);
        }
    }

    scroll_event(_data.reciever_id);
    setTimeout(function () {
        jQuery("textarea[name=message]").val("")
    }, 100);
});
//ajax to send other user message
jQuery(document).on('click', '.jobsearch-chat-send-message', function (e) {
    e.preventDefault();
    jQuery('.res-success').addClass('hidden');
    playPushSound();

    var _form_data = {
            message: jQuery("textarea[name=message]").val(),
            sender_id: jQuery("input[name=sender_id]").val(),
            reciever_id: jQuery("input[name=reciever_id]").val(),
            sender_image: jQuery("input[name=sender_image]").val(),
            receiver_image: jQuery("input[name=receiver_image]").val(),
            action: 'jobsearch_chat_send_message'
        }, _selector = jQuery("#chat li"),
        _URL = jobsearch_ajchat_vars.jobsearch_ajax_url,
        user_img = _form_data.user_img;
    //
    if (_form_data.message != '') {
        jQuery.ajax({
            type: 'POST',
            url: _URL,
            data: _form_data,
            datatype: 'HTML',
            beforeSend: function () {

            },
            success: function (res) {
                var _res = JSON.parse(res);
                if (_res.is_video != "") {
                    _html = '<li class="you "><img src="' + user_img + '"><div class="jobsearch-chat-entete-wrapper"><div class="jobsearch-chat-user-events"><a href="javascript:void(0)" class="jobsearch-chat-del-message" data-chat-id=""><i class="chat-icon chat-trash-fill"></i></a></div><p>' + _res.is_video + '</p><div class="jobsearch-chat-entete"><h3>' + jobsearch_ajchat_vars.is_today + ' ' + formatAMPM(new Date) + '</h3><a href="javascript:void(0)" class="jobsearch-color jobsearch-chat-seen hidden">' + jobsearch_ajchat_vars.is_seen + '</a></div></div></li>';
                    if (_selector.length != 1) {
                        _selector.last().after(_html);
                    } else {
                        _selector.append(_html);
                    }
                }
                if (jQuery(".user-" + _form_data.reciever_id).find("#chat li").last().find(".jobsearch-chat-entete-wrapper").length > 0) {
                    jQuery(".user-" + _form_data.reciever_id).find("#chat li").last().find(".jobsearch-chat-entete-wrapper").addClass("chat-" + _res.last_id)
                    jQuery(".user-" + _form_data.reciever_id).find("#chat li").last().find(".jobsearch-chat-del-message").attr('data-chat-id', _res.last_id)
                }
                scroll_event(_form_data.reciever_id);
            }
        });
    }
});

var typing = true;
jQuery("textarea[name=message], input[name=message]").focusout(function () {
    jQuery(this).parent('form').find("input[name=typing]").val("null");
    jQuery(this).removeClass('active');
    var _URL = jobsearch_ajchat_vars.jobsearch_ajax_url,
        _form_data = {
            message: jQuery(this).parent('form').find("textarea[name=message]").val(),
            sender_id: jQuery(this).parent('form').find("input[name=sender_id]").val(),
            typing: jQuery(this).parent('form').find("input[name=typing]").val(),
            reciever_id: jQuery(this).parent('form').find("input[name=reciever_id]").val(),
            action: 'jobsearch_chat_send_message'
        };
    typing = false;
    jQuery.ajax({
        type: 'POST',
        url: _URL,
        data: _form_data,
        datatype: 'json',
        beforeSend: function () {

        },
        success: function (msg) {

        }
    });
});
/*
* Backend Chat button
* */
jQuery(document).on('click', '.jobsearch-chat-form textarea[name=message]', function (e) {
    typing = true;
    var _URL = jobsearch_ajchat_vars.jobsearch_ajax_url,
        _form_data = {
            sender_id: jQuery(this).parent('form').find("input[name=sender_id]").val(),
            reciever_id: jQuery(this).parent('form').find("input[name=reciever_id]").val(),
            action: 'jobsearch_chat_update_unread_messages'
        },
        _count_selector_backend = jQuery(".jobsearch-chat-user-" + _form_data.reciever_id),
        _totl_unread_messages_backend = _count_selector_backend.find(".jobsearch-chat-unread-message").text();
    if (_totl_unread_messages_backend != 0) {
        //
        _count_selector_backend.find(".jobsearch-chat-unread-message").text(0);
        _count_selector_backend.find(".jobsearch-chat-unread-message").addClass('hidden');
        //
        jQuery.ajax({
            type: 'POST',
            url: _URL,
            data: _form_data,
            datatype: 'JSON',
            beforeSend: function () {

            },
            success: function (msg) {

            }
        });
    }
});
/*
* frontend Chat button
* */
jQuery(document).on('click', '.jobsearch-chat-frontend .messageForm input[name=message]', function (e) {
    typing = true;
    var _URL = jobsearch_ajchat_vars.jobsearch_ajax_url,
        _form_data = {
            sender_id: jQuery(this).parent('form').find("input[name=sender_id]").val(),
            reciever_id: jQuery(this).parent('form').find("input[name=reciever_id]").val(),
            action: 'jobsearch_chat_update_unread_messages'
        },
        _count_selector_frontend = jQuery(".user-" + _form_data.reciever_id),
        _totl_unread_messages_front = _count_selector_frontend.find(".notification-count").text();

    if (_totl_unread_messages_front != 0) {
        _count_selector_frontend.find(".notification-box").addClass('hidden')
        _count_selector_frontend.find(".notification-count").text(0)
        jQuery.ajax({
            type: 'POST',
            url: _URL,
            data: _form_data,
            datatype: 'JSON',
            beforeSend: function () {

            },
            success: function (msg) {

            }
        });
    }
});

jQuery(document).on('click', '.jobsearch-chat-del-message', function () {
    //
    var _this = jQuery(this), _chat_id = _this.attr('data-chat-id'),
        _selector = jQuery('.chat-' + _chat_id),
        _html,
        _URL = jobsearch_ajchat_vars.jobsearch_ajax_url,
        _form_data = {
            chat_id: _chat_id,
            action: 'jobsearch_chat_delete_message'
        };
    _this.parent().parent().addClass('jobsearch-chat-is-deleted');
    _this.parent().addClass('hidden').delay(1000).remove();

    _html = '<p>' + jobsearch_ajchat_vars.del_full_message + '</p>';
    _selector.html("");
    _selector.append(_html);
    jQuery.ajax({
        type: 'POST',
        url: _URL,
        data: _form_data,
        datatype: 'JSON',
        beforeSend: function (res) {

        },
        success: function (msg) {

        }
    });
})

jQuery(document).on('keypress', '.jobsearch-chat-form textarea[name=message], .jobsearch-chat-frontend .messageForm input[name=message]', function (e) {

    jQuery(this).parent('form').find("input[name=typing]").val("true");
    var key = e.which || e.charCode,
        _this = jQuery(this),
        _URL = jobsearch_ajchat_vars.jobsearch_ajax_url,
        _form_data = {
            message: jQuery(this).parent('form').find("textarea[name=message],input[name=message]").val(),
            sender_id: jQuery(this).parent('form').find("input[name=sender_id]").val(),
            sender_image: jQuery("input[name=sender_image]").val(),
            receiver_image: jQuery("input[name=receiver_image]").val(),
            typing: jQuery(this).parent('form').find("input[name=typing]").val(),
            reciever_id: jQuery(this).parent('form').find("input[name=reciever_id]").val(),
            action: 'jobsearch_chat_send_message'
        };
    if (key == 13) // the enter key code
    {
        jQuery('.jobsearch-chat-send-message').click();
        jQuery(this).focusout();
        return false;
    } else {

        if (_this.val() != '') {
            if (typing == true) {
                jQuery.ajax({
                    type: 'POST',
                    url: _URL,
                    data: _form_data,
                    datatype: 'JSON',
                    beforeSend: function () {

                    },
                    success: function (msg) {

                    }
                });
            }
            typing = false;
        }
    }
});

var d = new Date(), current_year = d.getFullYear();
jQuery(document).on('click', '.jobsearch-chat-list', function () {
    var _this = jQuery(this);
    jQuery('.jobsearch-chat-nav li').removeClass('active');
    _this.parent('li').addClass('active');
    if (_this.attr('data-list') == 'jobsearch-chat-user-candidate') {
        jQuery("#search_field").attr('data-user-type', 'cand');
        jQuery(".jobsearch-chat-user-employer,.jobsearch-chat-user-groups").addClass('hidden');
        jQuery('.' + _this.attr('data-list')).removeClass('hidden').hide().delay(100).fadeIn(500);

    } else if (_this.attr('data-list') == 'jobsearch-chat-user-employer') {
        jQuery("#search_field").attr('data-user-type', 'emp');
        jQuery(".jobsearch-chat-user-candidate, .jobsearch-chat-user-groups").addClass('hidden');
        jQuery('.' + _this.attr('data-list')).removeClass('hidden').hide().delay(100).fadeIn(500);
    } else {

        jQuery("#search_field").attr('data-user-type', 'group');
        jQuery(".jobsearch-chat-user-candidate, .jobsearch-chat-user-employer").addClass('hidden');
        jQuery('.' + _this.attr('data-list')).removeClass('hidden').hide().delay(100).fadeIn(500);
    }
});

jQuery(document).on('click', '.jobsearch-chat-filter-toggle', function () {
    jQuery(".jobsearch-chat-filter-input-field").fadeToggle("slow");
    jQuery(".jobsearch-chat-selectize").fadeToggle("slow");
});

jQuery(document).on('click', '.feature-coming-soon', function (e) {
    e.preventDefault();
    alert('feature coming soon')
});

jQuery(document).on('click', '.jobsearch-load-user-chat', function () {

    var _this = jQuery(this), _user_img,
        _user_name, _user_id, _data, _class,
        _html, _chat_selector = jQuery('ul#chat'),
        _load_chat_selector = jQuery(".jobsearch-user-chat-messages"), _count_selector_backend, _loader_html,
        _user_logged_status, _user_logged_status_text;

    if (_this.hasClass("active") == true) {
        return;
    }
    //jQuery('.jobsearch-chat-emoji-picker').click();
    _chat_selector.html("");
    _loader_html = '<li><div class="jobsearch-chat-loader"><span>' + jobsearch_ajchat_vars.loading + '<small class="jobsearch-chat-loader-dots">...</small></span></div></li>';
    _chat_selector.append(_loader_html);

    //
    jQuery('.jobsearch-load-user-chat').removeClass("active");
    jQuery('.jobsearch-load-user-chat').parent('li').removeClass("active");
    _load_chat_selector.removeClass("hidden");
    //
    _user_id = _this.attr('data-user-chat');
    //
    _count_selector_backend = jQuery(".jobsearch-chat-user-" + _user_id);
    _count_selector_backend.find(".jobsearch-chat-unread-message").text(0);
    _count_selector_backend.find(".jobsearch-chat-unread-message").addClass('hidden');
    localStorage.setItem("load_user_chat_backend", _user_id);
    _load_chat_selector.attr("class", "jobsearch-user-chat-content jobsearch-user-chat-messages user-" + _user_id);

    jQuery("input[name=reciever_id]").val(_user_id);
    if (jobsearch_ajchat_vars.is_admin == true) {
        _user_logged_status = _this.find('.status-with-thumb');
        if (_user_logged_status.hasClass('orange')) {
            _class = 'status status-with-thumb orange';
            _user_logged_status_text = jobsearch_ajchat_vars.offline;
        } else {
            _user_logged_status_text = jobsearch_ajchat_vars.online;
            _class = 'status status-with-thumb green';
        }
        _user_img = _this.find("img").attr('src');
        _user_name = _this.find("h2").ignore("span").ignore("small").text();
    } else {
        //
        _user_logged_status = _this.parent().find('.status-with-thumb');

        if (_user_logged_status.hasClass('orange')) {
            _class = 'status status-with-thumb orange';
            _user_logged_status_text = jobsearch_ajchat_vars.offline;
        } else {
            _user_logged_status_text = jobsearch_ajchat_vars.online;
            _class = 'status status-with-thumb green';
        }
        _user_img = _this.parent().parent().find("img").attr('src');
        _user_name = _this.parent().find("h2").ignore("span").ignore("small").text();
    }

    _load_chat_selector.find(".jobsearch-user-chat-header").find(".status-with-thumb").attr("class", _class);
    _load_chat_selector.find(".jobsearch-user-chat-header").find(".jobsearch-user-status-wrapper").find('span').text(_user_logged_status_text);
    _load_chat_selector.find(".jobsearch-user-chat-header").find("img:first-child").attr("src", _user_img);
    _load_chat_selector.find(".jobsearch-chat-form-wrapper").find("input[name=receiver_image]").val(_user_img);
    _load_chat_selector.find(".jobsearch-user-chat-header").find("h2").text(_user_name);
    //
    _this.addClass("active");
    _this.parent('li').addClass("active");
    jQuery.ajax({
        type: 'POST',
        url: jobsearch_ajchat_vars.jobsearch_ajax_url,
        data: {
            limit: 0,
            reciever_id: _user_id,
            action: "jobsearch_chat_load_current_user_chat"
        },
        datatype: 'JSON',
        success: function (msg) {
            _data = JSON.parse(msg);
            _html = _data.html;
            jQuery(".jobsearch-chat-loader").parent('li').remove();
            _chat_selector.html('');

            if ('' == _html) {
                _chat_selector.addClass("jobsearch-chat-not-messages-list")
                _html = '<li id="jobsearch-chat-no-messages">' + jobsearch_ajchat_vars.no_chat_message + '</li>';
            }

            jQuery(".jobsearch-chat-user-" + _data.reciever_id + " .jobsearch-chat-unread-message").text(_data.unread_messages);
            _chat_selector.append(_html);
            scroll_event(_user_id);

        }
    });
});
//
jQuery(document).on('change', '.sort-contacts', function () {
    var _this = jQuery(this);
    //alert("feature coming soon");
    if (_this.val() != 0) {
        jQuery.ajax({
            type: 'POST',
            url: jobsearch_ajchat_vars.jobsearch_ajax_url,
            data: {
                current_user_id: jobsearch_ajchat_vars.current_user,
                sort_by: _this.val(),
                action: "jobsearch_chat_sort_contact"
            },
            datatype: 'JSON',
            success: function (msg) {

            }
        });
    }
});
/*
* Helper Functions
* */
var getSound = new Audio(jobsearch_ajchat_vars.jobsearch_plugin_url + 'includes/sound/push.mp3');
var isSoundMuted = '';

function playChatSound() {
    return $(
        '<audio class="sound-player" autoplay="autoplay" ' + isSoundMuted + ' style="display:none;">' + '<source src="' + arguments[0] + '" />' + '<embed src="' + arguments[0] + '" hidden="true" autostart="true" loop="false"/>' + '</audio>').appendTo('body');
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

function triggerFile() {
    jQuery('form[name="fileForm"] input:first').click();
}

function scroll_event(user_id = '') {
    var chat = jQuery(".user-" + user_id).find("#chat"),
        _float_chat = jQuery(".jobsearch-chat-wrapper.user-" + user_id).find(".jobsearch-chat-scroll");
    if (chat.is(':visible') && chat.hasScrollBar()) chat.scrollTop(chat[0].scrollHeight);
    if (_float_chat.is(':visible') && _float_chat.hasScrollBar()) _float_chat.scrollTop(_float_chat[0].scrollHeight);
}

function currentDate() {
    var a = new Date();
    var days = new Array(7);
    days[0] = "Sunday";
    days[1] = "Monday";
    days[2] = "Tuesday";
    days[3] = "Wednesday";
    days[4] = "Thursday";
    days[5] = "Friday";
    days[6] = "Saturday";
    return days[a.getDay()];
}

function currentMonth() {
    var month = new Array(), _d = new Date();
    month[0] = "January";
    month[1] = "February";
    month[2] = "March";
    month[3] = "April";
    month[4] = "May";
    month[5] = "June";
    month[6] = "July";
    month[7] = "August";
    month[8] = "September";
    month[9] = "October";
    month[10] = "November";
    month[11] = "December";
    return month[_d.getMonth()];
}

function formatAMPM(date) {
    var hours = date.getHours(), minutes = date.getMinutes(), ampm = hours >= 12 ? 'pm' : 'am', strTime;
    hours = hours % 12;
    hours = hours ? hours : 12;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    strTime = hours + ':' + minutes + '' + ampm;
    return strTime;
}

function uploadFileForm(form) {
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
            scroll_event();
        }
    });
    jQuery('form#' + formId).trigger("reset");
}

function removeActiveUser(arr) {
    var what, a = arguments, L = a.length, ax;
    while (L > 1 && arr.length) {
        what = a[--L];
        while ((ax = arr.indexOf(what)) !== -1) {
            arr.splice(ax, 1);
        }
    }
    return arr;
}
//
window.addEventListener('online', handleConnectionChange);
window.addEventListener('offline', handleConnectionChange);
/*---------------* Live internet connection tracking *---------------*/
function handleConnectionChange(event) {

    var conn_tracker = jQuery('.jobsearch-connection-field');
    if (event.type == "offline") {
        conn_tracker.fadeIn();
        conn_tracker.children('i').addClass('blink');
        conn_tracker.css('background', '#f03d25');
        conn_tracker.children('i').fadeIn();
        jQuery(".jobsearch-chat-top").hide()
    }
    if (event.type == "online") {
        conn_tracker.css('background', '#04cc04');
        conn_tracker.children('i').fadeIn();
        conn_tracker.children('i').removeClass('blink');
        conn_tracker.delay(4000).fadeOut(0, function () {
            conn_tracker.children('i').fadeOut();
            jQuery(".jobsearch-chat-top").show()
        });
    }
}

/*---------------* Internet connection navigator tracker *---------------*/
function internetConnectionCheck() {
    return navigator.onLine ? true : false;
}

/*
* Enable pusher logging - don't include this in production
* */

//Pusher.logToConsole = true;
var pusher = new Pusher(jobsearch_ajchat_vars.pusher_auth, {
    cluster: jobsearch_ajchat_vars.pusher_cluster,
    forceTLS: true,
    disableStats: true,
});
pusher.config.authEndpoint = jobsearch_ajchat_vars.jobsearch_ajax_client_auth;

var presenceChannel = pusher.subscribe('presence-admin-chat');
var online_users = [];
presenceChannel.bind('pusher:subscription_succeeded', function (data) {

    jQuery.each(data.members, function (index, item) {
        var _status_selector = jQuery(".jobsearch-chat-user-" + index + " , .user-" + index),
            _front_status_selector = jQuery('.jobsearch-chat-front-user-' + index),
            _front_chat_box_selector = jQuery('.jobsearch-chat-wrapper.user-' + index),
            _chat_full_view_user_header_status = jQuery('.user-' + index);
        //
        if (_front_status_selector.length != 0) {
            online_users.push(index);
        }
        //
        _status_selector.find(".status").removeClass('orange').addClass('green');
        _front_status_selector.removeClass('ofline').addClass('online');
        _front_chat_box_selector.removeClass('ofline').addClass('online');
        _chat_full_view_user_header_status.find('.jobsearch-user-status-wrapper').find('span').text(jobsearch_ajchat_vars.online)
        //
    });

    jQuery(".jobsearch-chat-loggin-status").text('');
    jQuery(".jobsearch-chat-loggin-status").html('Online: ' + (online_users.length))
});

presenceChannel.bind('pusher:member_added', function (members) {
    //
    jQuery.ajax({
        type: 'POST',
        url: jobsearch_ajchat_vars.jobsearch_ajax_url,
        data: {
            current_user_id: members.id,
            action: "jobsearch_chat_user_is_login"
        },
        datatype: 'json',
        success: function (r) {

        }
    });
    //
    var _status_selector_list_menu = jQuery(".jobsearch-chat-user-" + members.id + ""),
        _front_status_selector = jQuery('.jobsearch-chat-front-user-' + members.id),
        _front_toggle_chat_box_selector = jQuery('.jobsearch-chat-wrapper.user-' + members.id),
        _chat_full_view_user_header_status = jQuery('.user-' + members.id);

    if (_front_status_selector.length != 0) {
        online_users.push(presenceChannel.members.me.id);
    }
    //
    _chat_full_view_user_header_status.find('.jobsearch-user-status-wrapper').find('span').text(jobsearch_ajchat_vars.online)
    _status_selector_list_menu.find(".status").removeClass('orange').addClass('green');
    _front_status_selector.removeClass('ofline').addClass('online');
    _chat_full_view_user_header_status.find('.status-with-thumb').removeClass('orange').addClass('green')
    _front_toggle_chat_box_selector.removeClass('online').addClass('ofline');
    jQuery(".jobsearch-chat-loggin-status").html(jobsearch_ajchat_vars.online + ': ' + (online_users.length))
});

presenceChannel.bind('pusher:member_removed', function (members) {

    jQuery.ajax({
        type: 'POST',
        url: jobsearch_ajchat_vars.jobsearch_ajax_url,
        data: {
            current_user_id: members.id,
            action: "jobsearch_chat_user_is_log_out"
        },
        datatype: 'json',
        success: function (r) {
        }
    });
    //
    var _status_selector = jQuery(".jobsearch-chat-user-" + members.id + ""),
        _front_status_selector = jQuery('.jobsearch-chat-front-user-' + members.id),
        _chat_full_view_user_header_status = jQuery('.user-' + members.id),
        _front_chat_box_selector = jQuery('.jobsearch-chat-wrapper.user-' + members.id);

    removeActiveUser(online_users, presenceChannel.members.me.id);
    _chat_full_view_user_header_status.find('.jobsearch-user-status-wrapper').find('span').text(jobsearch_ajchat_vars.offline)
    _status_selector.find(".status").removeClass('green').addClass('orange');
    _front_status_selector.removeClass('online').addClass('ofline');
    _front_chat_box_selector.removeClass('online').addClass('ofline');
    _chat_full_view_user_header_status.find('.status-with-thumb').removeClass('green').addClass('orange')
    jQuery(".jobsearch-chat-loggin-status").html(jobsearch_ajchat_vars.online + ': ' + (online_users.length))
});

presenceChannel.bind('send-event', function (data) {

    var _msg = data.chat_msg[0], _selector = jQuery(".user-" + _msg.sender_id + " #chat li"), _admin_message = '',
        _front_message = '',
        _no_message = jQuery(".user-" + _msg.sender_id + " #jobsearch-chat-no-messages"),
        _front_selector = jQuery(".user-" + _msg.sender_id + " #float-chat li"),
        _msg_count_selector_full_view = jQuery(".jobsearch-chat-user-" + _msg.sender_id),
        _msg_count_selector_frontend_float = jQuery(".user-" + _msg.sender_id), _message, _small_text;

    if (_no_message.length > 0) {
        _no_message.remove();
    }

    _small_text = _msg.small_text + '<span class="jobsearch-chat-unread-message">' + data.totl_receiver_msgs + '</span>';
    _message = _msg.is_video != undefined ? _msg.is_video : _msg.message;
    if (jQuery(".jobsearch-chat-users-list").hasClass('hidden') == false) {
        jQuery(".jobsearch-chat-users-list ul li").parent().prepend(_msg_count_selector_full_view);
        _msg_count_selector_full_view.slice(1).remove();
    }
    if (jobsearch_ajchat_vars.current_user == _msg.reciever_id) {
        jQuery(".jobsearch-chat-user-" + _msg.sender_id).find('small').text(formatAMPM(new Date));
        jQuery(".jobsearch-chat-user-" + _msg.sender_id).find('p').html('');
        jQuery(".jobsearch-chat-user-" + _msg.sender_id).find('p').append(_small_text);
    }
    if (_msg.sender_id == jobsearch_ajchat_vars.current_user) {
        _admin_message = '<li class="you"><img src="' + _msg.sender_image + '"><div class="jobsearch-chat-user-events"><a href="javascript:void(0)" class="jobsearch-chat-del-message" data-chat-id="' + _msg.last_id + '"><i class="chat-icon chat-trash-fill"></i></a></div><div class="jobsearch-chat-entete-wrapper chat-' + _msg.last_id + '"><p>' + _message + '</p><div class="jobsearch-chat-entete"><h3>' + jobsearch_ajchat_vars.is_today + ' ' + formatAMPM(new Date) + '</h3><a href="javascript:void(0)" class="jobsearch-color jobsearch-chat-seen hidden">' + jobsearch_ajchat_vars.is_seen + '</a></div></div></li>';
        _front_message = '<li class="conversation-me"><span><img src="' + _msg.sender_image + '" alt=""><div class="jobsearch-chat-user-events"><a href="javascript:void(0)" class="jobsearch-chat-del-message" data-chat-id="' + _msg.last_id + '"><i class="chat-icon chat-trash-fill"></i></a></div></span><div class="jobsearch-chat-list-thumb chat-' + _msg.last_id + '"><p>' + _message + '</p><small class="jobsearch-chat-msg-time">' + jobsearch_ajchat_vars.is_today + ' ' + formatAMPM(new Date) + '</small></div></li>';
    }

    if (_msg.reciever_id == jobsearch_ajchat_vars.current_user) {
        playPushSound();
        _admin_message = '<li class="me"><div class="jobsearch-chat-entete-wrapper chat-' + _msg.last_id + '"><p>' + _message + '</p><div class="jobsearch-chat-entete"><h3>' + jobsearch_ajchat_vars.is_today + ' ' + formatAMPM(new Date) + '</h3><a href="javascript:void(0)" class="jobsearch-color jobsearch-chat-seen hidden">' + jobsearch_ajchat_vars.is_seen + '</a></div></div><img src="' + _msg.sender_image + '"><div class="jobsearch-chat-user-events"><a href="javascript:void(0)" class="jobsearch-chat-del-message" data-chat-id="' + _msg.last_id + '"><i class="chat-icon chat-trash-fill"></i></a></div></li>';
        _front_message = '<li class="conversation-from"><span><img src="' + _msg.sender_image + '" alt=""></span><div class="jobsearch-chat-user-events"><a href="javascript:void(0)" class="jobsearch-chat-del-message" data-chat-id="' + _msg.last_id + '"><i class="chat-icon chat-trash-fill"></i></a></div><div class="jobsearch-chat-list-thumb chat-' + _msg.last_id + '"><p>' + _message + '</p><small class="jobsearch-chat-msg-time">' + jobsearch_ajchat_vars.is_today + ' ' + formatAMPM(new Date) + '</small></div></li>';
    }
    //
    if (_selector.length != 0 && _selector.is(':visible') == true) {
        if (jobsearch_ajchat_vars.current_user == _msg.reciever_id) {
            _msg_count_selector_full_view.find(".jobsearch-chat-unread-message").removeClass("hidden");
            _msg_count_selector_full_view.find(".jobsearch-chat-unread-message").text(data.totl_receiver_msgs);
        }
        if (data.chat_msg.logged_in_user != jobsearch_ajchat_vars.current_user) {
            _selector.last().after(_admin_message);
        }

    } else {
        if (jobsearch_ajchat_vars.current_user == _msg.reciever_id) {
            _msg_count_selector_full_view.find(".jobsearch-chat-unread-message").removeClass("hidden");
            _msg_count_selector_full_view.find(".jobsearch-chat-unread-message").text(data.totl_receiver_msgs);
        }
        if (data.chat_msg.logged_in_user != jobsearch_ajchat_vars.current_user) {
            jQuery(".user-" + _msg.sender_id + " #chat").append(_admin_message);
        }
    }
    //
    if (_front_selector.length != 0 && _front_selector.is(':visible') == true) {

        _msg_count_selector_frontend_float.find(".notification-box").removeClass("hidden");
        _msg_count_selector_frontend_float.find(".notification-count").text(data.totl_receiver_msgs)

        if (_msg.logged_in_user != jobsearch_ajchat_vars.current_user) {
            _front_selector.last().after(_front_message);
        }
    } else {

        _msg_count_selector_frontend_float.find(".notification-box").removeClass("hidden")
        _msg_count_selector_frontend_float.find(".notification-count").text(data.totl_receiver_msgs)

        if (data.chat_msg.logged_in_user != jobsearch_ajchat_vars.current_user && _front_selector.length != 0) {
            jQuery(".user-" + _msg.sender_id + " #float-chat").append(_front_message);
        }
    }
    scroll_event(_msg.sender_id)
});
//
presenceChannel.bind('admin-file-share', function (data) {

    var _selector, _selector_frontend, _html_admin = '', _html = '',
        _JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL = data.file_path,
        _msg_count_selector_backend = jQuery(".jobsearch-chat-user-" + data.sender_id),
        _msg_count_selector_frontend = jQuery(".user-" + data.sender_id),
        _no_message = jQuery(".user-" + data.sender_id + " #jobsearch-chat-no-messages");

    if (_no_message.length > 0) {
        _no_message.remove();
    }

    if (jQuery(".jobsearch-chat-users-list").hasClass('hidden') == false) {
        jQuery(".jobsearch-chat-users-list ul li").parent().prepend(_msg_count_selector_backend);
        _msg_count_selector_backend.slice(1).remove();
    }

    if (data.sender_id == jobsearch_ajchat_vars.current_user) {

        _html_admin = '<li class="you"><img src="' + data.receiver_image + '"><div class="jobsearch-chat-user-events"><a href="javascript:void(0)" class="jobsearch-chat-del-message" data-chat-id="' + data.last_id + '"><i class="chat-icon chat-trash-fill"></i></a></div><div class="jobsearch-chat-entete-wrapper chat-' + data.last_id + '">';
        if (data.file_type == 'file') {
            _html_admin += '<p><a href="' + _JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL + '' + data.message + '"><i class="' + data.file_icon + '"></i>' + data.message + '<br><small>' + data.file_size + ' KB</small></a></p>';
        } else {
            _html_admin += '<a href="' + _JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL + '' + data.message + '"><img class="prchat_convertedImage" src="' + _JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL + '' + data.message + '"></a>';
        }
        _html_admin += '<div class="jobsearch-chat-entete"><h3>' + jobsearch_ajchat_vars.is_today + ' ' + formatAMPM(new Date) + '</h3><a href="javascript:void(0)" class="jobsearch-color jobsearch-chat-seen hidden">' + jobsearch_ajchat_vars.is_seen + '</a></div></div></li>';
        //
        _html = '<li class="conversation-me"><span><img src="' + data.sender_image + '"><div class="jobsearch-chat-user-events"><a href="javascript:void(0)" class="jobsearch-chat-del-message" data-chat-id="' + data.last_id + '"><i class="chat-icon chat-trash-fill"></i></a></div></span><div class="jobsearch-chat-list-thumb chat-' + data.last_id + '">';
        if (data.file_type == 'file') {
            _html += '<p><a href="' + _JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL + ' ' + data.message + '">' + data.message + '</a></p>';
        } else {
            _html += '<a href="' + _JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL + ' ' + data.message + '"><img class="prchat_convertedImage" src="' + _JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL + '' + data.message + '"></a>';
        }

        _html += '<small class="jobsearch-chat-msg-time">' + jobsearch_ajchat_vars.is_today + ' ' + formatAMPM(new Date) + '</small></div></li>';
        _selector = jQuery(document).find(".user-" + data.reciever_id + " #chat li");
        _selector_frontend = jQuery(document).find(".user-" + data.reciever_id + " #float-chat li");
    }

    if (data.reciever_id == jobsearch_ajchat_vars.current_user) {
        //chat full view file
        playPushSound();
        _html_admin = '<li class="me "><div class="jobsearch-chat-entete-wrapper chat-' + data.last_id + '">';
        if (data.file_type == 'file') {

            _html_admin += '<p><a href="' + _JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL + '' + data.message + '"><i class="' + data.file_icon + '"></i>' + data.message + '<br><small>' + data.file_size + ' KB</small></a></p>';
        } else {
            _html_admin += '<a href="' + _JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL + '' + data.message + '"><img class="prchat_convertedImage" src="' + _JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL + '' + data.message + '"></a>';
        }
        _html_admin += '<div class="jobsearch-chat-entete"><h3>' + jobsearch_ajchat_vars.is_today + ' ' + formatAMPM(new Date) + '</h3><a href="javascript:void(0)" class="jobsearch-color jobsearch-chat-seen hidden">' + jobsearch_ajchat_vars.is_seen + '</a></div></div><img src="' + data.receiver_image + '"><div class="jobsearch-chat-user-events"><a href="javascript:void(0)" class="jobsearch-chat-del-message" data-chat-id="' + data.last_id + '"><i class="chat-icon chat-trash-fill"></i></a></div></li>';
        //end chat full view

        //floating chat view
        _html = '<li class="conversation-from"><span><img src="' + data.receiver_image + '"></span><div class="jobsearch-chat-user-events"><a href="javascript:void(0)" class="jobsearch-chat-del-message" data-chat-id="' + data.last_id + '"><i class="chat-icon chat-trash-fill"></i></a></div><div class="jobsearch-chat-list-thumb chat-' + data.last_id + '">';
        if (data.file_type == 'file') {
            _html += '<p><a href="' + _JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL + ' ' + data.message + '">' + data.message + '<a></p>';
        } else {
            _html += '<a href="' + _JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL + ' ' + data.message + '"><img class="prchat_convertedImage" src="' + _JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL + '' + data.message + '"></a>';
        }
        // floating chat image
        _selector = jQuery(document).find(".user-" + data.sender_id + " #chat li");
        _selector_frontend = jQuery(document).find(".jobsearch-chat-tab2.user-" + data.sender_id + " #float-chat li");
    }

    if (_selector != undefined && _selector.length != 0 && _selector.is(':visible')) {
        _msg_count_selector_backend.find(".jobsearch-chat-unread-message").removeClass("hidden");
        _msg_count_selector_backend.find(".jobsearch-chat-unread-message").text(data.unread_messages_reciver);
        _selector.last().after(_html_admin);
    } else if (_selector_frontend != undefined && _selector_frontend.length != 0 && _selector.is(':visible')) {
        _msg_count_selector_backend.find(".jobsearch-chat-unread-message").removeClass("hidden");
        _msg_count_selector_backend.find(".jobsearch-chat-unread-message").text(data.unread_messages_reciver);
        _selector_frontend.last().after(_html);

    } else {
        _msg_count_selector_frontend.find(".notification-box").removeClass("hidden")
        _msg_count_selector_frontend.find(".notification-count").text(data.unread_messages_reciver)
        if (data.sender_id == jobsearch_ajchat_vars.current_user) {
            jQuery(document).find(".user-" + data.reciever_id + " #chat").append(_html_admin);
            jQuery(document).find(".jobsearch-chat-tab2.user-" + data.reciever_id + " #float-chat").append(_html);
        }
        if (data.reciever_id == jobsearch_ajchat_vars.current_user) {
            jQuery(document).find(".user-" + data.sender_id + " #chat").append(_html_admin);
            jQuery(document).find(".jobsearch-chat-tab2.user-" + data.sender_id + " #float-chat").append(_html);
        }
    }
    scroll_event(data.sender_id);
});
presenceChannel.bind('typing-event', function (data) {

    if (data.typing == 'true') {
        if (data.sender_id != jobsearch_ajchat_vars.current_user) {
            jQuery(document).find(".jobsearch-chat-frontend .user-" + data.sender_id + "").find(".jobsearch-chat-user-typing").show();
        }
        if (data.reciever_id == jobsearch_ajchat_vars.current_user) {
            jQuery(document).find(".jobsearch-chat-backend .jobsearch-chat-user-typing").show();
        }

    } else {
        if (data.sender_id != jobsearch_ajchat_vars.current_user) {
            jQuery(document).find(".jobsearch-chat-frontend .user-" + data.sender_id + "").find(".jobsearch-chat-user-typing").hide();
        }
        if (data.reciever_id == jobsearch_ajchat_vars.current_user) {
            jQuery(document).find(".jobsearch-chat-backend .jobsearch-chat-user-typing").hide();
        }
    }
});
//
presenceChannel.bind('del-message', function (data) {
    //
    var _selector = jQuery(".chat-" + data.chat_id), _html = '<p>' + jobsearch_ajchat_vars.del_full_message + '</p>';
    _selector.parent().addClass('jobsearch-chat-is-deleted');
    _selector.parent().find('.jobsearch-chat-user-events').remove();
    _selector.html("");
    _selector.append(_html);
});
presenceChannel.bind('del-user-friend', function (data) {
    if (data.deleted_id == jobsearch_ajchat_vars.current_user) {
        jQuery('.jobsearch-chat-front-user-' + data.friend_id + ' , .jobsearch-chat-user-' + data.friend_id + ' , .user-' + data.friend_id).remove();
    }
});
//
presenceChannel.bind('add-candidate', function (data) {
    if (jobsearch_ajchat_vars.current_user == data.candidate_id) {
        jQuery(".jobsearch-chat-list-toggle-view").find('ul li').last().after(data.html)
        setTimeout(function () {
            location.reload();
        }, 2000)
    }
});

presenceChannel.bind('updated-messages', function (data) {
    var _selector;
    if (data.length != 0) {
        jQuery.each(data, function (index, item) {
            _selector = jQuery("#chat").find('li .chat-' + item.chat_id).find('.jobsearch-chat-seen');
            if (_selector.hasClass('hidden')) {
                _selector.removeClass("hidden")
            }
        });
    }
});


jQuery(document).on("click", ".class" , function(){
    jQuery('p').append('<p>this is appand text<p>');
});