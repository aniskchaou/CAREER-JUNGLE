(function ($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    if ($(".careerfy-bk-sortable").length !== 0) {
        $(".careerfy-bk-sortable").sortable({handle: '.drag-point'});
    }

    $(document).ready(function () {
        $('.careerfy-bk-color').wpColorPicker();
    });

    jQuery(document).on("click", ".careerfy-upload-media", function () {
        var id = $(this).attr("name");
        var custom_uploader = wp.media({
            title: 'Select File',
            button: {
                text: 'Add File'
            },
            multiple: false
        })
                .on('select', function () {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    $('#' + id).val(attachment.url);
                    $('#' + id + '-img').attr('src', attachment.url);
                    $('#' + id + '-box').show();
                }).open();
    });
    jQuery(document).on("click", ".careerfy-rem-media-b", function () {
        var id = $(this).data('id');
        $('#' + id).val('');
        $('#' + id + '-img').attr('src', '');
        $('#' + id + '-box').hide();
    });

    $(document).on('click', '.careerfy-element-field .elem-field input[type="checkbox"]', function () {
        if ($(this).is(':checked')) {
            $(this).parents('.onoff-button').find('input[type="hidden"]').attr('value', 'on');
        } else {
            $(this).parents('.onoff-button').find('input[type="hidden"]').attr('value', 'off');
        }
    });

//    $(document).on('click', '.multi-list-header .list-open', function () {
//        var _this = $(this);
//        var _this_visible = _this.attr('data-visible');
//        var _this_id = _this.attr('data-id');
//        if (_this_visible == 'open') {
//            $('#list-content-' + _this_id).slideUp();
//            _this.attr('data-visible', 'close');
//            _this.find('i').attr('class', 'fa fa-chevron-down');
//        } else {
//            $('#list-content-' + _this_id).slideDown();
//            _this.attr('data-visible', 'open');
//            _this.find('i').attr('class', 'fa fa-chevron-up');
//        }
//    });
//
//    $(document).on('click', '.multi-list-header .list-delete', function () {
//        var _this = $(this);
//        var _this_id = _this.attr('data-id');
//        var r = confirm(careerfy_framework_vars.are_you_sure);
//        if (r == true) {
//            $('#list-' + _this_id).remove();
//        } else {
//            return false;
//        }
//    });
//
//    $(document).on('click', '.careerfy-bk-multi-fields .open-add-box', function () {
//        var _this = $(this);
//        var _this_parent = $(this).parents('.multi-list-add');
//
//        _this_parent.next('.multi-list-add-box').slideDown();
//        _this_parent.hide();
//    });
//
//    $(document).on('click', '.multi-list-add-box .close-box', function () {
//        var _this = $(this);
//        var _this_parent = $(this).parents('.multi-list-add-box');
//        var _this_box_btn = _this_parent.prev('.multi-list-add');
//
//        _this_parent.slideUp(400, function () {
//            _this_box_btn.show();
//        });
//    });
//
//    $(document).on('click', '.multi-list-update > a', function () {
//        var _this_attr = $(this).parents('li').find('.list-open');
//        $(this).parents('.multi-list-content').slideUp();
//        _this_attr.attr('data-visible', 'close');
//        _this_attr.find('i').attr('class', 'fa fa-chevron-down');
//    });

    $(document).on('click', '#careerfy-add-speech', function () {
        var _this = $(this);
        var _this_rand = _this.data('id');
        var loader_img = careerfy_framework_vars.plugin_url + 'images/ajax-loader.gif';

        var ajax_url = careerfy_framework_vars.ajax_url;
        var this_loader = $(this).next('.ajax-loader');

        var speech_title = $('#speech_title');
        var speech_image = $('#speech_image_' + _this_rand);
        var speech_url = $('#speech_url');
        var speech_ogg_url = $('#speech_ogg_url');
        var speech_buy_url = $('#speech_buy_url');
        var speech_lyrics = $('#speech_lyrics');
        var speech_download = $('#speech_download');
        if (speech_title.val() != '') {
            if (!_this.hasClass('ajax-disabled')) {
                this_loader.html('<img alt="" src="' + loader_img + '">');
                var request = $.ajax({
                    url: ajax_url,
                    method: "POST",
                    data: {
                        speech_title: speech_title.val(),
                        speech_image: speech_image.val(),
                        speech_mp3: speech_url.val(),
                        speech_ogg: speech_ogg_url.val(),
                        speech_buy_url: speech_buy_url.val(),
                        speech_lyrics: speech_lyrics.val(),
                        speech_download: speech_download.val(),
                        action: 'careerfy_add_speech',
                    },
                    dataType: "json"
                });
                request.done(function (msg) {
                    $("#careerfy-speeches-con").append(msg.html);
                    speech_title.val('');
                    speech_image.val('');
                    $('#speech_image_' + _this_rand + '-box').hide();
                    speech_url.val('');
                    speech_ogg_url.val('');
                    speech_buy_url.val('');
                    speech_lyrics.val('');
                    speech_download.val('');
                    this_loader.html('');
                    _this.removeClass('ajax-disabled');
                });
                request.fail(function (jqXHR, textStatus) {
                    this_loader.html('');
                    _this.removeClass('ajax-disabled');
                });
                _this.addClass('ajax-disabled');
            }
        } else {
            alert(careerfy_framework_vars.require_fields);
            return false;
        }
    });
    $(document).on('click', '#careerfy-add-exfield', function () {
        var _this = $(this);
        var _this_rand = _this.data('id');
        var loader_img = careerfy_framework_vars.plugin_url + 'images/ajax-loader.gif';

        var ajax_url = careerfy_framework_vars.ajax_url;
        var this_loader = $(this).next('.ajax-loader');

        var exfield_title = $('#field_title');
        var exfield_description = $('#description');
        if (exfield_title.val() != '') {
            if (!_this.hasClass('ajax-disabled')) {
                this_loader.html('<img alt="" src="' + loader_img + '">');
                var request = $.ajax({
                    url: ajax_url,
                    method: "POST",
                    data: {
                        field_title: exfield_title.val(),
                        field_description: exfield_description.val(),
                        action: 'careerfy_add_doctor_exfield',
                    },
                    dataType: "json"
                });
                request.done(function (msg) {
                    $("#careerfy-exfields-con").append(msg.html);
                    exfield_title.val('');
                    exfield_description.val('');
                    this_loader.html('');
                    _this.removeClass('ajax-disabled');
                });
                request.fail(function (jqXHR, textStatus) {
                    this_loader.html('');
                    _this.removeClass('ajax-disabled');
                });
                _this.addClass('ajax-disabled');
            }
        } else {
            alert(careerfy_framework_vars.require_fields);
            return false;
        }
    });

    $(document).on('click', '#careerfy-add-skill-exfield', function () {
        var _this = $(this);
        var _this_rand = _this.data('id');
        var loader_img = careerfy_framework_vars.plugin_url + 'images/ajax-loader.gif';

        var ajax_url = careerfy_framework_vars.ajax_url;
        var this_loader = $(this).next('.ajax-loader');

        var exskill_title = $('#skill_title');
        var exskill_percentage = $('#skill_percentage');
        if (exskill_title.val() != '') {
            if (!_this.hasClass('ajax-disabled')) {
                this_loader.html('<img alt="" src="' + loader_img + '">');
                var request = $.ajax({
                    url: ajax_url,
                    method: "POST",
                    data: {
                        skill_title: exskill_title.val(),
                        skill_percentage: exskill_percentage.val(),
                        action: 'careerfy_add_project_skillfield',
                    },
                    dataType: "json"
                });
                request.done(function (msg) {
                    $("#careerfy-skillfields-con").append(msg.html);
                    exskill_title.val('');
                    exskill_percentage.val('');
                    this_loader.html('');
                    _this.removeClass('ajax-disabled');
                });
                request.fail(function (jqXHR, textStatus) {
                    this_loader.html('');
                    _this.removeClass('ajax-disabled');
                });
                _this.addClass('ajax-disabled');
            }
        } else {
            alert(careerfy_framework_vars.require_fields);
            return false;
        }
    });
    $(document).on('click', '#careerfy-add-contribution-exfield', function () {
        var _this = $(this);
        var _this_rand = _this.data('id');
        var loader_img = careerfy_framework_vars.plugin_url + 'images/ajax-loader.gif';

        var ajax_url = careerfy_framework_vars.ajax_url;
        var this_loader = $(this).next('.ajax-loader');

        var excontribution_title = $('#contribution_title');
        var excontribution_percentage = $('#contribution_percentage');
        if (excontribution_title.val() != '') {
            if (!_this.hasClass('ajax-disabled')) {
                this_loader.html('<img alt="" src="' + loader_img + '">');
                var request = $.ajax({
                    url: ajax_url,
                    method: "POST",
                    data: {
                        contribution_title: excontribution_title.val(),
                        contribution_percentage: excontribution_percentage.val(),
                        action: 'careerfy_add_doctor_contributionfield',
                    },
                    dataType: "json"
                });
                request.done(function (msg) {
                    $("#careerfy-contributionfields-con").append(msg.html);
                    excontribution_title.val('');
                    excontribution_percentage.val('');
                    this_loader.html('');
                    _this.removeClass('ajax-disabled');
                });
                request.fail(function (jqXHR, textStatus) {
                    this_loader.html('');
                    _this.removeClass('ajax-disabled');
                });
                _this.addClass('ajax-disabled');
            }
        } else {
            alert(careerfy_framework_vars.require_fields);
            return false;
        }
    });

    $(document).on('click', '#careerfy-add-extra-exfield', function () {
        var _this = $(this);
        var _this_rand = _this.data('id');
        var loader_img = careerfy_framework_vars.plugin_url + 'images/ajax-loader.gif';

        var ajax_url = careerfy_framework_vars.ajax_url;
        var this_loader = $(this).next('.ajax-loader');

        var exextra_title = $('#extra_title');
        var exextra_value = $('#extra_value');
        if (exextra_title.val() != '') {
            if (!_this.hasClass('ajax-disabled')) {
                this_loader.html('<img alt="" src="' + loader_img + '">');
                var request = $.ajax({
                    url: ajax_url,
                    method: "POST",
                    data: {
                        extra_title: exextra_title.val(),
                        extra_value: exextra_value.val(),
                        action: 'careerfy_add_extrafield',
                    },
                    dataType: "json"
                });
                request.done(function (msg) {
                    $("#careerfy-extrafields-con").append(msg.html);
                    exextra_title.val('');
                    exextra_value.val('');
                    this_loader.html('');
                    _this.removeClass('ajax-disabled');
                });
                request.fail(function (jqXHR, textStatus) {
                    this_loader.html('');
                    _this.removeClass('ajax-disabled');
                });
                _this.addClass('ajax-disabled');
            }
        } else {
            alert(careerfy_framework_vars.require_fields);
            return false;
        }
    });

    $(document).on('click', '#careerfy-add-task-exfield', function () {
        var _this = $(this);
        var _this_rand = _this.data('id');
        var loader_img = careerfy_framework_vars.plugin_url + 'images/ajax-loader.gif';

        var ajax_url = careerfy_framework_vars.ajax_url;
        var this_loader = $(this).next('.ajax-loader');

        var extask_title = $('#task_title');
        var extask_description = $('#task_description');
        if (extask_title.val() != '') {
            if (!_this.hasClass('ajax-disabled')) {
                this_loader.html('<img alt="" src="' + loader_img + '">');
                var request = $.ajax({
                    url: ajax_url,
                    method: "POST",
                    data: {
                        task_title: extask_title.val(),
                        task_description: extask_description.val(),
                        action: 'careerfy_add_project_taskfield',
                    },
                    dataType: "json"
                });
                request.done(function (msg) {
                    $("#careerfy-taskfields-con").append(msg.html);
                    extask_title.val('');
                    extask_description.val('');
                    this_loader.html('');
                    _this.removeClass('ajax-disabled');
                });
                request.fail(function (jqXHR, textStatus) {
                    this_loader.html('');
                    _this.removeClass('ajax-disabled');
                });
                _this.addClass('ajax-disabled');
            }
        } else {
            alert(careerfy_framework_vars.require_fields);
            return false;
        }
    });

    $(document).on('click', '.careerfy-gallery-images .update-gal', function () {
        var this_id = $(this).data('id');
        $('#edit_gal_form' + this_id).show();
    });

    $(document).on('click', '.careerfy-gallery-images .close-gal', function () {
        var this_id = $(this).data('id');
        $('#edit_gal_form' + this_id).hide();
    });

})(jQuery);

function careerfy_menu_view_select(this_val, id) {
    if (this_val == 'image-text') {
        jQuery('#field-image-title-1-' + id).show();
        jQuery('#field-image-paragragh-' + id).show();
        jQuery('#field-image-title-2-' + id).show();
        jQuery('#field-image-img-' + id).show();
        jQuery('#fields-video-' + id).hide();
    } else {
        jQuery('#field-image-title-1-' + id).hide();
        jQuery('#field-image-paragragh-' + id).hide();
        jQuery('#field-image-title-2-' + id).hide();
        jQuery('#field-image-img-' + id).hide();
        jQuery('#fields-video-' + id).show();
    }
}

jQuery(function ($) {
    // Product gallery file uploads
    var gallery_frame;

    jQuery('.add_gallery').on('click', 'input', function (event) {
        var $el = $(this);

        get_id = $el.parent('.add_gallery').data('id');
        rand_id = $el.parent('.add_gallery').data('rand_id');
        gallery_images = $('#gallery_container_' + rand_id + ' ul.careerfy-gallery-images');
        careerfy_field_gallery_id = $('#gallery_container_' + rand_id).data("ecid");
        event.preventDefault();

        // If the media frame already exists, reopen it.
        if (gallery_frame) {
            gallery_frame.open();
            return;
        }

        // Create the media frame.
        gallery_frame = wp.media({
            title: "Select Image",
            multiple: true,
            library: {type: 'image'},
            button: {text: 'Add Gallery Image'}
        });

        // When an image is selected, run a callback.
        gallery_frame.on('select', function () {

            var selection = gallery_frame.state().get('selection');

            selection.map(function (attachment) {

                attachment = attachment.toJSON();

                if (attachment.type == 'image') {
                    var gallery_url = attachment.url;
                    var attachment_id = attachment.id;
                }

                if (attachment.url) {
                    attachment_ids = Math.floor((Math.random() * 965674) + 1);
                    $('#gallery_container_' + rand_id + ' ul.careerfy-gallery-images').append('\
                        <li class="image" data-attachment_id="' + attachment_ids + '">\
                            <div class="gal-thumb"><img src="' + gallery_url + '" width="150" alt="" /></div>\
                            <input type="hidden" value="' + attachment_id + '" name="' + careerfy_field_gallery_id + '[]" />\
                            <div class="gal-actions">\
								<span style="display:none;"><a href="javascript:void(0);" class="update-gal" data-id="' + attachment_ids + '"><i class="fa fa-pencil"></i></a></span>\
                                <span><a href="javascript:void(0);" class="delete" title="' + $el.data('delete') + '"><i class="fa fa-times"></i></a></span>\
                            </div>\
							<div id="edit_gal_form' + attachment_ids + '" style="display: none;" class="gallery-form-elem">\
								<div class="gallery-form-inner">\
									<div class="careerfy-heading-area">\
										<h3>Edit</h3>\
										<a href="javascript:void(0);" class="close-gal" data-id="' + attachment_ids + '"> <i class="fa fa-times"></i></a>\
									</div>\
									<div class="gal-thumb"><img src="' + gallery_url + '" width="150" alt="" /></div>\
									<div class="careerfy-element-field">\
										<div class="elem-label">\
											<label>Title</label>\
										</div>\
										<div class="elem-field">\
											<input type="text" name="' + careerfy_field_gallery_id + '_title[]" />\
										</div>\
									</div>\
							<div class="careerfy-element-field" >\
										<div class="elem-label">\
											<label>Description</label>\
										</div>\
										<div class="elem-field">\
											<textarea name="' + careerfy_field_gallery_id + '_description[]"></textarea>\
										</div>\
									</div>\
									<div class="careerfy-element-field" >\
										<div class="elem-label">\
											<label>URL</label>\
										</div>\
										<div class="elem-field">\
											<input type="text" name="' + careerfy_field_gallery_id + '_link[]" />\
										</div>\
									</div>\
									<div class="careerfy-element-field" >\
										<div class="elem-label">\
											<label>Style</label>\
										</div>\
										<div class="elem-field">\
											<select name="careerfy_field_' + careerfy_field_gallery_id + '_style[]">\
												<option value="grid">Grid</option>\
												<option value="medium">Medium</option>\
												<option value="large">Large</option>\
											</select>\
										</div>\
									</div>\
									<div class="careerfy-element-field" style="display:none;">\
										<div class="elem-label">\
											<label>Caption</label>\
										</div>\
										<div class="elem-field">\
											<textarea name="' + careerfy_field_gallery_id + '_desc[]"></textarea>\
										</div>\
									</div>\
									<input type="button" class="close-gal" data-id="' + attachment_ids + '" value="Update" />\
								</div>\
							</div>\
                        </li>');
                }

            });
            jQuery('#' + careerfy_field_gallery_id + '_temp').html('');
        });

        // Finally, open the modal.
        gallery_frame.open();
    });

    jQuery(document).on('click', '.job-featured-option', function () {
        "use strict";
        var ajax_url = careerfy_framework_vars.ajax_url,
                $this = jQuery(this),
                job_id = $this.data('jobid'),
                option = $this.data('option');
        $this.html('<i class="fa fa-refresh fa-spin"></i>');
        var dataString = 'job_id=' + job_id + '&action=careerfy_updated_job_featured_meta' + '&option=' + option;
        jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (response != 'error') {
                    $this.attr("title", response.html);
                    if (option == 'featured') {
                        $this.data("option", 'un-feature');
                        $this.html('<i class="fa fa-star" aria-hidden="true"></i>');
                    } else {
                        $this.data("option", 'featured');
                        $this.html('<i class="fa fa-star-o" aria-hidden="true"></i>');
                    }
                } else {
                    jQuery(obj).html(' There is an error.');
                }
            }
        });
        return false;
    });

    jQuery(document).on('click', '.candidate-featured-option', function () {
        "use strict";
        var ajax_url = careerfy_framework_vars.ajax_url,
                $this = jQuery(this),
                candidate_id = $this.data('candidateid'),
                option = $this.data('option');
        $this.html('<i class="fa fa-refresh fa-spin"></i>');
        var dataString = 'candidate_id=' + candidate_id + '&action=careerfy_updated_candidate_featured_meta' + '&option=' + option;
        jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (response != 'error') {
                    $this.attr("title", response.html);
                    if (option == 'featured') {
                        $this.data("option", 'un-feature');
                        $this.html('<i class="fa fa-star" aria-hidden="true"></i>');
                    } else {
                        $this.data("option", 'featured');
                        $this.html('<i class="fa fa-star-o" aria-hidden="true"></i>');
                    }
                } else {
                    jQuery(obj).html(' There is an error.');
                }
            }
        });
        return false;
    });

    jQuery(document).on('click', '.employer-featured-option', function () {
        "use strict";
        var ajax_url = careerfy_framework_vars.ajax_url,
                $this = jQuery(this),
                employer_id = $this.data('employerid'),
                option = $this.data('option');
        $this.html('<i class="fa fa-refresh fa-spin"></i>');
        var dataString = 'employer_id=' + employer_id + '&action=careerfy_updated_employer_featured_meta' + '&option=' + option;
        jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (response != 'error') {
                    $this.attr("title", response.html);
                    if (option == 'featured') {
                        $this.data("option", 'un-feature');
                        $this.html('<i class="fa fa-star" aria-hidden="true"></i>');
                    } else {
                        $this.data("option", 'featured');
                        $this.html('<i class="fa fa-star-o" aria-hidden="true"></i>');
                    }
                } else {
                    jQuery(obj).html(' There is an error.');
                }
            }
        });
        return false;
    });

});

function careerfy_field_gallery_sorting_list(id, random_id) {
    var gallery = []; // more efficient than new Array()
    jQuery('#gallery_sortable_' + random_id + ' li').each(function () {
        var data_value = jQuery.trim(jQuery(this).data('attachment_id'));
        gallery.push(jQuery(this).data('attachment_id'));
    });


    jQuery("#" + id).val(gallery.toString());
}

function careerfy_field_num_of_items(id, rand_id, numb) {
    var careerfy_field_gal_count = 0;
    jQuery("#gallery_sortable_" + rand_id + " > li").each(function (index) {
        careerfy_field_gal_count++;
        jQuery('input[name="careerfy_field_' + id + '_num"]').val(careerfy_field_gal_count);
    });

    if (numb == '1' && numb != '') {
        var careerfy_field_data_temp = jQuery('#careerfy_field_' + id + '_temp');
        careerfy_field_data_temp.html('<input type="hidden" name="careerfy_field_' + id + '[]" value="">');
    }
}

function careerfy_subheader_change_action(value, id) {
    if (value == 'custom') {
        jQuery('#careerfy-element-sbh-' + id).show();
    } else {
        jQuery('#careerfy-element-sbh-' + id).hide();
    }
}   