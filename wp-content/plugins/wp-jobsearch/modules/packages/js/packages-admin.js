function jobsearch_onchange_package_type(value) {
    if (typeof jQuery('#' + value + '_package_fields') !== 'undefined') {
        jQuery('.specific-pkges-fields').hide();
        jQuery('#' + value + '_package_fields').show();
        if (value == 'cand_resume') {
            jQuery('.to_unlimit_pexp').hide();
        } else {
            jQuery('.to_unlimit_pexp').show();
        }
    }
}

function jobsearch_onchange_package_price_type(value) {
    if (value == 'free') {
        jQuery('.jobsearch-package-price-fields').hide();
        jQuery('.jobsearch-package-price-fields').find('#jobsearch-package-price').val('');
    } else {
        jQuery('.jobsearch-package-price-fields').show();
    }
}

jQuery('form#post').on('submit', function () {

    var price_reg = /^(\d{1,3})*(\.\d{2})?$/;
    if (jQuery('select#jobsearch-package-charges-type').length > 0 && jQuery('input#jobsearch-package-price').length > 0) {
        var _price = jQuery('input#jobsearch-package-price').val();

        if (jQuery('select#jobsearch-package-charges-type').val() == 'paid' && (_price == '' || (!price_reg.test(_price)) || parseFloat(_price) <= 0)) {
            jQuery('input#jobsearch-package-price').css({
                'border': '1px solid #ff0000',
            });
            return false;
        } else {
            jQuery('input#jobsearch-package-price').css({
                'border': '1px solid #dddddd',
            });
        }
    }
});

if (jQuery(".pckg-extra-fields-con").length !== 0) {
    jQuery(".pckg-extra-fields-con").sortable({handle: '.drag-point'});
}
jQuery(document).on('click', '.add-pkg-more-fields', function () {

    jQuery('.pckg-extra-fields-con').append('<div class="pckg-extra-field-item">\
        <div class="field-heder">\
            <a class="drag-point"><i class="dashicons dashicons-image-flip-vertical"></i></a>\
            <h2>Extra Field</h2>\
        </div>\
        <div class="field-remove-con">\
            <a href="javascript:void(0);" class="field-remove-btn"><i class="dashicons dashicons-no-alt"></i></a>\
        </div>\
        <div class="jobsearch-element-field">\
            <div class="elem-label">\
                Field Text\
            </div>\
            <div class="elem-field">\
                <input type="text" name="jobsearch_field_package_exfield_title[]">\
            </div>\
        </div>\
        <div class="jobsearch-element-field">\
            <div class="elem-label">\
                <label>Field Status</label>\
            </div>\
            <div class="elem-field">\
                <select name="jobsearch_field_package_exfield_status[]">\
                    <option value="active">Active</option>\
                    <option value="inactive">Inactive</option>\
                </select>\
            </div>\
        </div>\
    </div>');
});

jQuery(document).on('click', '.field-remove-btn', function () {

    jQuery(this).parents('.pckg-extra-field-item').remove();
});