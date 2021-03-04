var $ = jQuery;

jQuery(document).on('click', '.jobsearch-subscand-profile-pkg', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_loader = jQuery(this).find('.pkg-loding-msg');
    if (this_loader.length == 0) {
        this_loader = jQuery(this).next('.pkg-loding-msg');
    }

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.show();
    var request = jQuery.ajax({
        url: jobsearch_packages_vars.ajax_url,
        method: "POST",
        data: {
            pkg_id: this_id,
            action: 'jobsearch_cand_profile_pckg_subscribe',
        },
        dataType: "json"
    });

    request.done(function (response) {

        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);
        }
        if (typeof response.redirect_url !== 'undefined' && response.redirect_url != '') {
            window.location.replace(response.redirect_url);
            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_packages_vars.error_msg);
    });
});

jQuery(document).on('click', '.jobsearch-subsemp-profile-pkg', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_loader = jQuery(this).find('.pkg-loding-msg');
    if (this_loader.length == 0) {
        this_loader = jQuery(this).next('.pkg-loding-msg');
    }

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.show();
    var request = jQuery.ajax({
        url: jobsearch_packages_vars.ajax_url,
        method: "POST",
        data: {
            pkg_id: this_id,
            action: 'jobsearch_emp_profile_pckg_subscribe',
        },
        dataType: "json"
    });

    request.done(function (response) {

        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);
        }
        if (typeof response.redirect_url !== 'undefined' && response.redirect_url != '') {
            window.location.replace(response.redirect_url);
            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_packages_vars.error_msg);
    });
});

jQuery(document).on('click', '.jobsearch-subs-allinone-pkg', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_loader = jQuery(this).next('.pkg-loding-msg');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.show();
    var request = jQuery.ajax({
        url: jobsearch_packages_vars.ajax_url,
        method: "POST",
        data: {
            pkg_id: this_id,
            action: 'jobsearch_user_allinone_pckg_subscribe',
        },
        dataType: "json"
    });

    request.done(function (response) {

        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);
        }
        if (typeof response.redirect_url !== 'undefined' && response.redirect_url != '') {
            window.location.replace(response.redirect_url);
            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_packages_vars.error_msg);
    });
});

jQuery(document).on('click', '.jobsearch-subscribe-cv-pkg', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_loader = jQuery(this).next('.pkg-loding-msg');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.show();
    var request = jQuery.ajax({
        url: jobsearch_packages_vars.ajax_url,
        method: "POST",
        data: {
            pkg_id: this_id,
            action: 'jobsearch_user_cv_pckg_subscribe',
        },
        dataType: "json"
    });

    request.done(function (response) {

        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);
        }
        if (typeof response.redirect_url !== 'undefined' && response.redirect_url != '') {
            window.location.replace(response.redirect_url);
            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_packages_vars.error_msg);
    });
});

jQuery(document).on('click', '.jobsearch-subscribe-candidate-pkg', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_loader = jQuery(this).next('.pkg-loding-msg');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.show();
    var request = jQuery.ajax({
        url: jobsearch_packages_vars.ajax_url,
        method: "POST",
        data: {
            pkg_id: this_id,
            action: 'jobsearch_user_candidate_pckg_subscribe',
        },
        dataType: "json"
    });

    request.done(function (response) {
        console.info(response)
        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);
        }
        if (typeof response.redirect_url !== 'undefined' && response.redirect_url != '') {
            window.location.replace(response.redirect_url);
            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_packages_vars.error_msg);
    });
});

jQuery(document).on('click', '.jobsearch-subscribe-job-pkg', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_loader = jQuery(this).next('.pkg-loding-msg');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.show();
    var request = jQuery.ajax({
        url: jobsearch_packages_vars.ajax_url,
        method: "POST",
        data: {
            pkg_id: this_id,
            action: 'jobsearch_user_job_pckg_subscribe',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);
        }
        if (typeof response.redirect_url !== 'undefined' && response.redirect_url != '') {
            window.location.replace(response.redirect_url);
            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_packages_vars.error_msg);
    });
});

jQuery(document).on('click', '.jobsearch-subscribe-fjobs-pkg', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_loader = jQuery(this).next('.pkg-loding-msg');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.show();
    var request = jQuery.ajax({
        url: jobsearch_packages_vars.ajax_url,
        method: "POST",
        data: {
            pkg_id: this_id,
            action: 'jobsearch_user_fjobs_pckg_subscribe',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);
        }
        if (typeof response.redirect_url !== 'undefined' && response.redirect_url != '') {
            window.location.replace(response.redirect_url);
            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_packages_vars.error_msg);
    });
});

jQuery(document).on('click', '.jobsearch-promoteprof-pkg', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_loader = jQuery(this).next('.pkg-loding-msg');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.show();
    var request = jQuery.ajax({
        url: jobsearch_packages_vars.ajax_url,
        method: "POST",
        data: {
            pkg_id: this_id,
            action: 'jobsearch_user_promote_profile_pckg_sub',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);
        }
        if (typeof response.redirect_url !== 'undefined' && response.redirect_url != '') {
            window.location.replace(response.redirect_url);
            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_packages_vars.error_msg);
    });
});

jQuery(document).on('click', '.jobsearch-urgentsub-pkg', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_loader = jQuery(this).next('.pkg-loding-msg');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.show();
    var request = jQuery.ajax({
        url: jobsearch_packages_vars.ajax_url,
        method: "POST",
        data: {
            pkg_id: this_id,
            action: 'jobsearch_user_urgentsub_pckg_sub',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);
        }
        if (typeof response.redirect_url !== 'undefined' && response.redirect_url != '') {
            window.location.replace(response.redirect_url);
            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_packages_vars.error_msg);
    });
});

jQuery(document).on('click', '.jobsearch-candpdf-resm-pkg', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_loader = jQuery(this).next('.pkg-loding-msg');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    this_loader.show();
    var request = jQuery.ajax({
        url: jobsearch_packages_vars.ajax_url,
        method: "POST",
        data: {
            pkg_id: this_id,
            action: 'jobsearch_all_pckges_buy_checkout_call',
        },
        dataType: "json"
    });

    request.done(function (response) {

        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);
        }
        if (typeof response.redirect_url !== 'undefined' && response.redirect_url != '') {
            window.location.replace(response.redirect_url);
            return false;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_packages_vars.error_msg);
    });
});