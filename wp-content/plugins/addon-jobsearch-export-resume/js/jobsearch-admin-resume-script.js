jQuery(document).ready(function () {

    jQuery(document).on('click', '.jobsearch-cand-profile-pdf', function () {
        jQuery("input[name=jobsearch_cand_profile_btn]").trigger("click");
    });

    jQuery(document).on('click', '.jobsearch-activate-pdf-template', function () {
        var _this = jQuery(this), _template = _this.attr('data-template');
        jQuery(document).find('.jobsearch-active-pdf').remove();
        _this.after('<figcaption class="jobsearch-active-pdf">' + jobsearch_export_vars.active + '</figcaption>');
        jQuery("#cand-pbase-pdfs").val(_template);
    });

});