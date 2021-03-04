var ajaxCustomFieldRequestText;
var ajaxCustomFieldRequestUploadFile;
var ajaxCustomFieldRequestVideo;
var ajaxCustomFieldRequestLinkURL;
var ajaxCustomFieldRequestHeading;
var ajaxCustomFieldRequestNumber;
var ajaxCustomFieldRequestEmail;
var ajaxCustomFieldRequestDate;
var ajaxCustomFieldRequestRange;
var ajaxCustomFieldRequestSalary;
var ajaxCustomFieldRequestTextarea;
var ajaxCustomFieldRequestCheckbox;
var ajaxCustomFieldRequestDropdown;
var ajaxCustomFieldRequestDepDropdown;
var ajaxCustomFieldRequestDepFields;
var ajaxCustomFieldSaveRequest;
jQuery(document).on('click', '.jobsearch-custom-field-add-field', function () {
    "use strict";
    global_custom_field_counter++;
    var ajax_url = jobsearch_customfield_common_vars.ajax_url,
            $this = jQuery(this),
            randid = $this.data('randid'),
            form_element = jQuery('#jobsearch-custom-field-form-' + randid),
            fieldtype = $this.data('fieldtype'),
            fieldlabel = $this.data('fieldlabel'),
            field_container = jQuery('#foo' + randid),
            all_fields_count = form_element.find('input[name="custom-fields-all-names-count"]'),
            all_fields_string = form_element.find('input[name="custom-fields-all-names"]'),
            empty_container = field_container.find('.custom-field-empty-msg');
    //alert('#jobsearch-custom-field-form-' + randid);
    var action = '';
    var old_text = fieldlabel;
    $this.html('<i class="dashicons dashicons-update fa-spin"></i>');
    action = 'jobsearch_custom_field_html';
    var dataString = 'action=' + action + '&fieldtype=' + fieldtype + '&global_custom_field_counter=' + global_custom_field_counter;

    if (fieldtype == 'heading') {
        if (typeof (ajaxCustomFieldRequestHeading) != 'undefined') {
            ajaxCustomFieldRequestHeading.abort();
        }
        ajaxCustomFieldRequestHeading = jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (response != 'error') {
                    $this.html(old_text);
                    empty_container.hide();
                    field_container.append(response.html);
                    var all_fields_count_val = all_fields_count.val();
                    all_fields_count_val++;
                    all_fields_count.val(all_fields_count_val);
                } else {
                    $this.html(' There is an error.');
                }
            }
        });

    } else if (fieldtype == 'text') {
        if (typeof (ajaxCustomFieldRequestText) != 'undefined') {
            ajaxCustomFieldRequestText.abort();
        }
        ajaxCustomFieldRequestText = jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (response != 'error') {
                    $this.html(old_text);
                    empty_container.hide();
                    field_container.append(response.html);
                    var all_fields_count_val = all_fields_count.val();
                    all_fields_count_val++;
                    all_fields_count.val(all_fields_count_val);
                } else {
                    $this.html(' There is an error.');
                }
            }
        });

    } else if (fieldtype == 'upload_file') {
        if (typeof (ajaxCustomFieldRequestUploadFile) != 'undefined') {
            ajaxCustomFieldRequestUploadFile.abort();
        }
        ajaxCustomFieldRequestUploadFile = jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (response != 'error') {
                    $this.html(old_text);
                    empty_container.hide();
                    field_container.append(response.html);
                    var all_fields_count_val = all_fields_count.val();
                    all_fields_count_val++;
                    all_fields_count.val(all_fields_count_val);
                } else {
                    $this.html(' There is an error.');
                }
            }
        });

    } else if (fieldtype == 'video') {
        if (typeof (ajaxCustomFieldRequestVideo) != 'undefined') {
            ajaxCustomFieldRequestVideo.abort();
        }
        ajaxCustomFieldRequestVideo = jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (response != 'error') {
                    $this.html(old_text);
                    empty_container.hide();
                    field_container.append(response.html);
                    var all_fields_count_val = all_fields_count.val();
                    all_fields_count_val++;
                    all_fields_count.val(all_fields_count_val);
                } else {
                    $this.html(' There is an error.');
                }
            }
        });

    } else if (fieldtype == 'linkurl') {
        if (typeof (ajaxCustomFieldRequestLinkURL) != 'undefined') {
            ajaxCustomFieldRequestLinkURL.abort();
        }
        ajaxCustomFieldRequestLinkURL = jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (response != 'error') {
                    $this.html(old_text);
                    empty_container.hide();
                    field_container.append(response.html);
                    var all_fields_count_val = all_fields_count.val();
                    all_fields_count_val++;
                    all_fields_count.val(all_fields_count_val);
                } else {
                    $this.html(' There is an error.');
                }
            }
        });

    } else if (fieldtype == 'number') {
        if (typeof (ajaxCustomFieldRequestNumber) != 'undefined') {
            ajaxCustomFieldRequestNumber.abort();
        }
        ajaxCustomFieldRequestNumber = jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (response != 'error') {
                    $this.html(old_text);
                    empty_container.hide();
                    field_container.append(response.html);
                    var all_fields_count_val = all_fields_count.val();
                    all_fields_count_val++;
                    all_fields_count.val(all_fields_count_val);
                } else {
                    $this.html(' There is an error.');
                }
            }
        });

    } else if (fieldtype == 'date') {
        if (typeof (ajaxCustomFieldRequestDate) != 'undefined') {
            ajaxCustomFieldRequestDate.abort();
        }
        ajaxCustomFieldRequestDate = jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (response != 'error') {
                    $this.html(old_text);
                    empty_container.hide();
                    field_container.append(response.html);
                    var all_fields_count_val = all_fields_count.val();
                    all_fields_count_val++;
                    all_fields_count.val(all_fields_count_val);
                } else {
                    $this.html(' There is an error.');
                }
            }
        });

    } else if (fieldtype == 'range') {
        if (typeof (ajaxCustomFieldRequestRange) != 'undefined') {
            ajaxCustomFieldRequestRange.abort();
        }
        ajaxCustomFieldRequestRange = jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (response != 'error') {
                    $this.html(old_text);
                    empty_container.hide();
                    field_container.append(response.html);
                    var all_fields_count_val = all_fields_count.val();
                    all_fields_count_val++;
                    all_fields_count.val(all_fields_count_val);
                } else {
                    $this.html(' There is an error.');
                }
            }
        });

    } else if (fieldtype == 'salary') {
        if (typeof (ajaxCustomFieldRequestSalary) != 'undefined') {
            ajaxCustomFieldRequestSalary.abort();
        }
        ajaxCustomFieldRequestSalary = jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (response != 'error') {
                    $this.html(old_text);
                    empty_container.hide();
                    field_container.append(response.html);
                    var all_fields_count_val = all_fields_count.val();
                    all_fields_count_val++;
                    all_fields_count.val(all_fields_count_val);
                } else {
                    $this.html(' There is an error.');
                }
            }
        });

    } else if (fieldtype == 'email') {
        if (typeof (ajaxCustomFieldRequestEmail) != 'undefined') {
            ajaxCustomFieldRequestEmail.abort();
        }
        ajaxCustomFieldRequestEmail = jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (response != 'error') {
                    $this.html(old_text);
                    empty_container.hide();
                    field_container.append(response.html);
                    var all_fields_count_val = all_fields_count.val();
                    all_fields_count_val++;
                    all_fields_count.val(all_fields_count_val);
                } else {
                    $this.html(' There is an error.');
                }
            }
        });

    } else if (fieldtype == 'checkbox') {
        if (typeof (ajaxCustomFieldRequestCheckbox) != 'undefined') {
            ajaxCustomFieldRequestCheckbox.abort();
        }
        ajaxCustomFieldRequestCheckbox = jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (response != 'error') {
                    $this.html(old_text);
                    empty_container.hide();
                    field_container.append(response.html);
                    var all_fields_count_val = all_fields_count.val();
                    all_fields_count_val++;
                    all_fields_count.val(all_fields_count_val);
                } else {
                    $this.html(' There is an error.');
                }
            }
        });

    } else if (fieldtype == 'dropdown') {
        if (typeof (ajaxCustomFieldRequestDropdown) != 'undefined') {
            ajaxCustomFieldRequestDropdown.abort();
        }
        ajaxCustomFieldRequestDropdown = jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (response != 'error') {
                    $this.html(old_text);
                    empty_container.hide();
                    field_container.append(response.html);
                    var all_fields_count_val = all_fields_count.val();
                    all_fields_count_val++;
                    all_fields_count.val(all_fields_count_val);
                } else {
                    $this.html(' There is an error.');
                }
            }
        });

    } else if (fieldtype == 'dependent_dropdown') {
        if (typeof (ajaxCustomFieldRequestDepDropdown) != 'undefined') {
            ajaxCustomFieldRequestDepDropdown.abort();
        }
        ajaxCustomFieldRequestDepDropdown = jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (response != 'error') {
                    $this.html(old_text);
                    empty_container.hide();
                    field_container.append(response.html);
                    var all_fields_count_val = all_fields_count.val();
                    all_fields_count_val++;
                    all_fields_count.val(all_fields_count_val);
                } else {
                    $this.html(' There is an error.');
                }
            }
        });

    } else if (fieldtype == 'dependent_fields') {
        if (typeof (ajaxCustomFieldRequestDepFields) != 'undefined') {
            ajaxCustomFieldRequestDepFields.abort();
        }
        ajaxCustomFieldRequestDepFields = jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (response != 'error') {
                    $this.html(old_text);
                    empty_container.hide();
                    field_container.append(response.html);
                    var all_fields_count_val = all_fields_count.val();
                    all_fields_count_val++;
                    all_fields_count.val(all_fields_count_val);
                } else {
                    $this.html(' There is an error.');
                }
            }
        });

    } else if (fieldtype == 'textarea') {
        if (typeof (ajaxCustomFieldRequestTextarea) != 'undefined') {
            ajaxCustomFieldRequestTextarea.abort();
        }
        ajaxCustomFieldRequestTextarea = jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (response != 'error') {
                    $this.html(old_text);
                    empty_container.hide();
                    field_container.append(response.html);
                    var all_fields_count_val = all_fields_count.val();
                    all_fields_count_val++;
                    all_fields_count.val(all_fields_count_val);
                } else {
                    $this.html(' There is an error.');
                }
            }
        });
    }
    return false;
});

jQuery(document).on('click', '.custom-fields-remove', function () {
    "use strict";
    var $this = jQuery(this),
            randid = $this.data('randid'),
            randid = $this.data('randid');
    var parent_ul = jQuery('.custom-field-class-' + randid).parents('ul');
    jQuery('.custom-field-class-' + randid).slideUp("normal").promise().done(function () {
        jQuery(this).remove();
        // show empty msg if all lis removed
        if (parent_ul.children().length <= 1) {
            parent_ul.find('.custom-field-empty-msg').show('slow');
        }
    });
});

jQuery(document).on('click', '.custom-fields-submit', function () {
    "use strict";
    var ajax_url = jobsearch_customfield_common_vars.ajax_url,
            $this = jQuery(this),
            randid = $this.data('randid'),
            entitytype = $this.data('entitytype'),
            btntext = $this.data('btntext'),
            submited_form = jQuery('#jobsearch-custom-field-form-' + randid);
    $this.html('<i class="dashicons dashicons-update fa-spin"></i>');
    jQuery('.custom-field-msg-' + randid).html('')
    var dataString = 'action=jobsearch_custom_fields_save&entitytype=' + entitytype + '&' + submited_form.serialize();
    if (typeof (ajaxCustomFieldSaveRequest) !== 'undefined') {
        ajaxCustomFieldSaveRequest.abort();
    }
    ajaxCustomFieldSaveRequest = jQuery.ajax({
        type: "POST",
        url: ajax_url,
        data: dataString,
        dataType: "json",
        success: function (response) {
            $this.html(btntext);
            if (response != 'error') {
                jQuery('.custom-field-msg-' + randid).html(response.msg);
            } else {
                jQuery('.custom-field-msg-' + randid).html('There is an error.');
            }
            jQuery('.custom-field-msg-' + randid).show();
        }
    });
    return false;
});

jQuery(document).on("click", ".option-field-add-btn", function (e) {
    "use strict";
    e.preventDefault();
    var _this = jQuery(this),
            b = _this.closest('div.field-options-list');
    b.clone().insertAfter(b);
});
jQuery(document).on("click", ".option-field-remove", function (e) {
    "use strict";
    e.preventDefault();
    var _this = jQuery(this);
    _this.parents('div.field-options-list').remove();
});

jQuery(document).on("change", ".check-name-availability", function (e) {
    "use strict";

    var ajax_url = jobsearch_customfield_common_vars.ajax_url,
            _this = jQuery(this),
            parentForm = _this.closest('form'),
            this_string = _this.val(),
            custom_all_fileds = parentForm.find('input[name="custom-fields-all-names"]'),
            custom_all_fileds_val = custom_all_fileds.val(),
            custom_all_fileds_count = parentForm.find('input[name="custom-fields-all-names-count"]'),
            custom_all_fileds_count_val = custom_all_fileds_count.val(),
            msg_container = _this.nextAll('.available-msg:first');
    msg_container.html('<i class="dashicons dashicons-update fa-spin"></i>');
    var dataString = 'action=jobsearch_custom_fields_avalibility&custom_all_fileds=' + custom_all_fileds_val + '&this_string=' + this_string;
    jQuery.ajax({
        type: "POST",
        url: ajax_url,
        data: dataString,
        dataType: "json",
        success: function (response) {
//            $this.html(btntext);
            if (typeof response.ret_string !== 'undefined' && response.ret_string != '') {
                _this.val(response.ret_string);
            }
            if (response.type != 'error') {

                // var elements = jQuery("#select_<?php echo $doctors_field_key_form; ?> option:selected").length;
                // var input = jQuery("#<?php echo $doctors_field_key_form; ?>"); 
                //jQuery.each(jQuery("#select_<?php echo $doctors_field_key_form; ?> option:selected"), function (index, element) {
                custom_all_fileds.val(custom_all_fileds.val() + "," + this_string);
                custom_all_fileds_count_val++;
                custom_all_fileds_count.val(custom_all_fileds_count_val);
//                                            if (index < elements - 1) {
//                                                //remove last comma                    
//                                            }
                //});
                msg_container.html(response.message);
            } else {
                msg_container.html(response.message);

            }
        }
    });
    //alert(custom_all_fileds.val());

});
