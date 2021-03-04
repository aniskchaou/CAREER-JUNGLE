/**  jquery document.ready functions */
var $ = jQuery;
var ajaxRequest;

/*
 * Uploading Zip File
 */
jQuery(document).on("change", "#careerfy_icons_fonts_zip_rand", function () {
    var attachment_id   = jQuery(this).val();
    var th_loader = jQuery(".careerfy-icons-uploadMedia").parents('.wrap').find('.icon-manager-loder');
    th_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: careerfy_icons_manager.ajax_url,
        data: 'attachment_id=' + attachment_id + '&action=careerfy_reading_zip',
        success: function (response) {
            jQuery(".careerfy-icons-msg").html('<div class="notice notice-'+ response.type +' is-dismissible"><p>'+ response.msg +'</p></div>');
            jQuery(".careerfy-icons-msg").slideDown();
            setTimeout(function() {
                jQuery(".careerfy-icons-msg").slideUp();
            }, 5000);
            location.reload();
            th_loader.html('');
        }
    });
});

/*
 * Load Library Icons
 */
jQuery(document).on("change", ".careerfy-icon-library", function () {
    var icons_library   = jQuery(this).val();
    var thisParent  = jQuery(this).closest('.careerfy-icon-choser');
    var thisName    = jQuery(this).closest('.careerfy-icon-choser').data('name');
    var thisId    = jQuery(this).closest('.careerfy-icon-choser').data('id');
    var thisValue    = jQuery(this).closest('.careerfy-icon-choser').data('value');
    thisParent.find('.careerfy-library-icons-list').find('.icons-selector').find('.selector').append('<span class="icons-manager-loader"><i class="dashicons-before dashicons-admin-generic"></i></span>');
    jQuery.ajax({
        type: 'POST',
        url: careerfy_icons_manager.ajax_url,
        data: 'icons_library=' + icons_library + '&name=' + thisName + '&id=' + thisId + '&value=' + thisValue + '&action=careerfy_library_icons_list',
        success: function (response) {
           thisParent.find('.careerfy-library-icons-list').html(response);
        }
    });
});

/*
 * Removing Group
 */
var html_popup = "<div class='confirm-overlay' style='display:block'> \
    <div class='confirm-box'><div class='confirm-text'>Are you sure to do this?</div> \
    <div class='confirm-buttons'><div class='button confirm-yes'>Delete</div>\
    <div class='button confirm-no'>Cancel</div><br class='clear'></div> <span class='deling-loder'></span></div></div>";


jQuery(document).on("click", ".careerfy-group-remove", function () {
    var thisParent   = jQuery(this).closest('.careerfy-icons-manager-list');
    var group_name   = thisParent.data('id');
    jQuery(this).parent().append(html_popup);
    jQuery(document).on('click', '.confirm-yes', function (event) {
        jQuery(this).parents('.confirm-box').find('.deling-loder').html('<i class="fa fa-refresh fa-spin"></i>');
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: careerfy_icons_manager.ajax_url,
            data: 'group_name=' + group_name + '&action=careerfy_remove_group',
            success: function (response) {
                jQuery(".careerfy-icons-msg").html('<div class="notice notice-'+ response.type +' is-dismissible"><p>'+ response.msg +'</p></div>');
                jQuery(".careerfy-icons-msg").slideDown();
                setTimeout(function() {
                    jQuery(".careerfy-icons-msg").slideUp();
                }, 5000);
                if( response.type == 'success' ){
                    jQuery("#confirmOverlay").remove();
                    thisParent.slideUp(400, function() {
                        thisParent.remove();
                    });
                }
            },
            error: function (response) {
                jQuery("#confirmOverlay").remove();
            } 
        });
    });
    jQuery(document).on('click', '.confirm-no', function (event) {
        jQuery("#confirmOverlay").remove();
    });
});



/*
 * Media
 */
jQuery(document).on("click", ".careerfy-icons-uploadMedia", function () {
    "use strict";
    var $ = jQuery;
    var id = "careerfy_icons_fonts_zip_rand";
    var custom_uploader = wp.media({
        title: 'Select File',
        button: {
            text: 'Add File'
        },
        multiple: false
    }).on('select', function () {
        var attachment = custom_uploader.state().get('selection').first().toJSON();
        jQuery('#' + id).val(attachment.id);
        jQuery('#' + id).change();
    }).open();

});

/*
 * Enable / Disable Group
 */
jQuery(document).on("change", ".careerfy-icons-enable-group", function () {
    //var data_id = jQuery(this).data('id');
    var _this = jQuery(this);
    var data_value = 'off';
    if (_this.is(':checked')) {
        data_value = 'on';
    }
    var group_name = jQuery(this).data('group');
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: careerfy_icons_manager.ajax_url,
        data: 'group_name=' + group_name + '&status_value=' + data_value + '&action=careerfy_group_status',
        success: function (response) {
            jQuery(".careerfy-icons-msg").html('<div class="notice notice-'+ response.type +' is-dismissible"><p>'+ response.msg +'</p></div>');
            jQuery(".careerfy-icons-msg").slideDown();
            setTimeout(function() {
                 jQuery(".careerfy-icons-msg").slideUp();
            }, 5000);
        }
    });
});


jQuery(document).ready(function() {
    jQuery('body').on('click', '.export-icons-wrapper .export-btn a.export-icons-btn', function () {
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: 'action=careerfy_export_icons',
            dataType: 'json',
            success: function (response) {
                if( response.type === 'success' ){
                    if( response.url !== '' ){
                        jQuery('.export-btn a.export-icons').prop('href', response.url);
                        jQuery('.export-btn a.export-icons').attr('download', response.name);
                        jQuery('#export-icons').get(0).click();
                    }
                }
            }
        });
    });
});