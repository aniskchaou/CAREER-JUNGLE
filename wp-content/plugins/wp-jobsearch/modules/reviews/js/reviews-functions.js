jQuery(document).ready(function () {
    if (jQuery('select[id^="review-stars-selector"]').length > 0) {
        jQuery('select[id^="review-stars-selector"]').barrating({
            theme: 'fontawesome-stars',
            showSelectedRating: false
        });
    }
});

jQuery(document).on('click', '.jobsearch-go-to-review-form', function () {
    var _this_target = jQuery(this).attr('data-target');
    if (jQuery("#" + _this_target).length > 0) {
        jQuery('html, body').animate({
            scrollTop: eval(jQuery("#" + _this_target).offset().top - 100)
        }, 1000);
    }
});

jQuery(document).on('change', '.review-stars-holder select[id^="review-stars-selector"]', function () {
    var _this = jQuery(this);
    var total_rating = 0;
    var count_ratings = 0;
    jQuery(this).parents('form').find('.review-stars-sec select[id^="review-stars-selector"]').each(function (index, element) {
        var each_this = jQuery(this);
        total_rating += parseInt(each_this.val());
        count_ratings++;
    });
    if (count_ratings > 0) {
        var avg_rating = total_rating/count_ratings;
        jQuery(this).parents('form').find('.review-overall-stars-sec .rating-num').html(avg_rating.toFixed(1));
        
        var avg_rating_perc = (avg_rating / 5) * 100;
        jQuery(this).parents('form').find('.review-overall-stars-sec .jobsearch-company-rating-box').css({'width': avg_rating_perc + '%'});
    }
});

jQuery('form[id^=jobsearch-review-form]').on('submit', function (e) {
    e.preventDefault();
    var _form = jQuery(this);
    var submit_btn = _form.find('input[type="submit"]');
    var msg_con = _form.find('.jobsearch-review-msg');
    var loader_con = _form.find('.jobsearch-review-loader');
    var review_desc = _form.find('textarea[name="user_comment"]');
    var form_data = _form.serialize();

    if (review_desc.val() == '') {
        review_desc.css({"border": "1px solid #ff0000"});
        return false;
    }

    if (!submit_btn.hasClass('jobsearch-loading')) {
        review_desc.css({"border": "1px solid #eceeef"});
        submit_btn.addClass('jobsearch-loading');
        msg_con.hide();
        loader_con.show();
        loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: jobsearch_plugin_vars.ajax_url,
            method: "POST",
            data: form_data,
            dataType: "json"
        });

        request.done(function (response) {
            if ('undefined' !== typeof response.msg && response.msg != '') {
                msg_con.html(response.msg);
                msg_con.slideDown();
            }
            if ('undefined' !== typeof response.error && response.error == '1') {
                msg_con.removeClass('alert-success').addClass('alert-danger');
            } else {
                msg_con.removeClass('alert-danger').addClass('alert-success');
            }
            submit_btn.removeClass('jobsearch-loading');
            if ('undefined' !== typeof response.acton && response.acton == 'update') {
                window.location.reload(true);
                return false;
            }
            loader_con.hide();
            loader_con.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            submit_btn.removeClass('jobsearch-loading');
            loader_con.hide();
            loader_con.html('');
        });
    }

    return false;
});

jQuery(".reviw-mainitem-con figure .jobsearch-company-review-left").hover(function() {
    jQuery(this).parents('.reviw-mainitem-con').find('.review-detail-popover').slideDown();
}, function(event) {
    var this_target = jQuery(event.target);
    console.log(this_target);
    if (!this_target.is('.review-detail-popover')) {
        jQuery(this).parents('.reviw-mainitem-con').find('.review-detail-popover').slideUp();
    }
});

jQuery(".jobsearch-company-review figure .jobsearch-company-review-left").hover(function(){
    jQuery(this).parents('.reviw-mainitem-con').find('.review-detail-popover').slideDown();
},function(){
    jQuery(this).parents('.reviw-mainitem-con').find('.review-detail-popover').slideUp();
});

jQuery(".careerfy-company-review figure .jobsearch-company-review-left").hover(function(){
    jQuery(this).parents('.reviw-mainitem-con').find('.review-detail-popover').slideDown();
},function(){
    jQuery(this).parents('.reviw-mainitem-con').find('.review-detail-popover').slideUp();
});

jQuery(document).on('click', '.reply-review', function() {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    jQuery('#coment-reply-holdr' + this_id).find('.comrnt-reply-con').slideToggle();
});

jQuery(document).on('click', '.reply-review-close', function() {
    var _this = jQuery(this);
    _this.parent('.comrnt-reply-con').slideUp();
});

jQuery(document).on('click', '.update-cuser-review', function() {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    jQuery('#coment-updatrev-holdr' + this_id).slideToggle();
});

jQuery(document).on('click', '.update-review-close', function() {
    var _this = jQuery(this);
    _this.parent('.jobsearch-updaterev-holdr').slideUp();
});

jQuery(document).on('click', '.reply-review-submit', function() {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    
    var reply_txt = jQuery('#coment-reply-holdr' + this_id).find('.comrnt-reply-con').find('textarea[name="comernt_reply"]');
    
    var reply_error = 0;
    if (reply_txt.val() == '') {
        reply_error = 1;
        reply_txt.css({"border": "1px solid #ff0000"});
    } else {
        reply_txt.css({"border": "1px solid #d3dade"});
    }
    
    if (reply_error == 0) {
        var loader_con = _this.parent('.submt-replybtn-con').find('.revreply-loder');

        loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
        var com_id = _this.attr('data-id');
        var request = jQuery.ajax({
            url: jobsearch_reviews_vars.ajax_url,
            method: "POST",
            data: {
                'com_id': com_id,
                'reply_txt': reply_txt.val(),
                'action': 'jobsearch_user_replying_to_review'
            },
            dataType: "json"
        });
        request.done(function (response) {
            loader_con.html('');
            if (typeof response.reply !== 'undefined' && response.reply != '') {
                jQuery('#coment-reply-holdr' + this_id).append(response.reply);
                jQuery('#coment-reply-holdr' + this_id).find('.comrnt-reply-con').remove();
                jQuery('#coment-reply-holdr' + this_id).find('.replied-review-box').slideDown();
            }
            if (typeof response.msg !== 'undefined' && response.msg != '') {
                loader_con.html(response.msg);
            }
        });

        request.fail(function (jqXHR, textStatus) {
            loader_con.html('');
        });
    }
});