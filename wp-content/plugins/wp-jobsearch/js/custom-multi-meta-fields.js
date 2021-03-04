var $ = jQuery;
$(document).ready(function () {
    'use strict';
    $(document).on('click', '#jobsearch-add-education-exfield', function () {
        var _this = $(this);
        var _this_rand = _this.data('id');
        var loader_img = jobsearch_plugin_vars.plugin_url + 'images/ajax-loader.gif';

        var ajax_url = jobsearch_plugin_vars.ajax_url;
        var this_loader = $(this).next('.ajax-loader');

        var exeducation_title = $('#education_title');
        var exeducation_academy = $('#education_academy');
        var exeducation_start_date = $('#education_start_date');
        var exeducation_end_date = $('#education_end_date');
        var exeducation_prsnt_date = $('#education_prsnt_date');
        var exeducation_description = $('#education_description');
        if (exeducation_title.val() != '') {
            if (!_this.hasClass('ajax-disabled')) {
                this_loader.html('<img alt="" src="' + loader_img + '">');
                var request = $.ajax({
                    url: ajax_url,
                    method: "POST",
                    data: {
                        education_title: exeducation_title.val(),
                        education_academy: exeducation_academy.val(),
                        education_start_date: exeducation_start_date.val(),
                        education_end_date: exeducation_end_date.val(),
                        education_prsnt_date: exeducation_prsnt_date.val(),
                        education_description: exeducation_description.val(),
                        action: 'jobsearch_add_project_educationfield',
                    },
                    dataType: "json"
                });
                request.done(function (msg) {
                    $("#jobsearch-educationfields-con").append(msg.html);
                    exeducation_title.val('');
                    exeducation_academy.val('');
                    exeducation_start_date.val('');
                    exeducation_end_date.val('');
                    exeducation_prsnt_date.val('');
                    exeducation_description.val('');
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
            alert(jobsearch_plugin_vars.require_fields);
            return false;
        }
    });

    $(document).on('click', '#jobsearch-add-experience-exfield', function () {
        var _this = $(this);
        var _this_rand = _this.data('id');
        var loader_img = jobsearch_plugin_vars.plugin_url + 'images/ajax-loader.gif';

        var ajax_url = jobsearch_plugin_vars.ajax_url;
        var this_loader = $(this).next('.ajax-loader');

        var exexperience_title = $('#experience_title');
        var exexperience_start_date = $('#experience_start_date');
        var exexperience_end_date = $('#experience_end_date');
        var exexperience_prsnt_date = $('#experience_prsnt_date');
        var exexperience_description = $('#experience_description');
        var exexperience_company = $('#experience_company');
        if (exexperience_title.val() != '') {
            if (!_this.hasClass('ajax-disabled')) {
                this_loader.html('<img alt="" src="' + loader_img + '">');
                var request = $.ajax({
                    url: ajax_url,
                    method: "POST",
                    data: {
                        experience_title: exexperience_title.val(),
                        experience_start_date: exexperience_start_date.val(),
                        experience_end_date: exexperience_end_date.val(),
                        experience_prsnt_date: exexperience_prsnt_date.val(),
                        experience_description: exexperience_description.val(),
                        experience_company: exexperience_company.val(),
                        action: 'jobsearch_add_project_experiencefield',
                    },
                    dataType: "json"
                });
                request.done(function (msg) {
                    $("#jobsearch-experiencefields-con").append(msg.html);
                    exexperience_title.val('');
                    exexperience_start_date.val('');
                    exexperience_end_date.val('');
                    exexperience_prsnt_date.val('');
                    exexperience_description.val('');
                    exexperience_company.val('');
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
            alert(jobsearch_plugin_vars.require_fields);
            return false;
        }
    });
    
    jQuery(document).on('click', '.jobsearch-upload-upportimg', function () {
        var this_parent = jQuery(this).parents('.jobsearch-eximg-portcon');
        this_parent.find('input[type=file]').trigger('click');
    });
    
    function jobsearch_bkadmn_read_portfolio_file_url(input) {

        if (input.files && input.files[0]) {

            var _this = jQuery(input);
            var this_parent = _this.parents('.jobsearch-eximg-portcon');
            var loader_con = this_parent.find('.file-loader');
            
            var pid = this_parent.attr('data-id');

            var img_file = input.files[0];
            var img_size = img_file.size;

            img_size = parseFloat(img_size / 1024).toFixed(2);

            loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
            loader_con.show();
            var formData = new FormData();
            formData.append('pid', pid);
            formData.append('add_portfolio_img', img_file);
            formData.append('action', 'jobsearch_dashboard_adding_portfolio_img_url');

            var request = jQuery.ajax({
                url: ajaxurl,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json"
            });
            request.done(function (response) {
                if (typeof response.img_url !== 'undefined') {
                    this_parent.find('.jobsearch-browse-med-image').removeAttr('style');
                    this_parent.find('img').attr('src', response.img_url);
                    this_parent.find('input[type=hidden]').val(response.img_id);
                }
                loader_con.html('');
            });

            request.fail(function (jqXHR, textStatus) {
                loader_con.html('');
            });
        }
    }

    jQuery(document).on('change', 'input[name^="add_portfolio_img"]', function () {
        jobsearch_bkadmn_read_portfolio_file_url(this);
    });

    $(document).on('click', '#jobsearch-add-portfolio-exfield', function () {
        var _this = $(this);
        var _this_rand = _this.data('id');
        var _this_pid = _this.data('pid');
        var loader_img = jobsearch_plugin_vars.plugin_url + 'images/ajax-loader.gif';

        var ajax_url = jobsearch_plugin_vars.ajax_url;
        var this_loader = $(this).next('.ajax-loader');

        var exportfolio_title = $('#portfolio_title');
        var exportfolio_image = $('#portfolio_image_' + _this_rand);
        var exportfolio_url = $('#portfolio_url');
        var exportfolio_vurl = $('#portfolio_vurl');
        if (exportfolio_title.val() != '') {
            if (!_this.hasClass('ajax-disabled')) {
                this_loader.html('<img alt="" src="' + loader_img + '">');
                var request = $.ajax({
                    url: ajax_url,
                    method: "POST",
                    data: {
                        pid: _this_pid,
                        portfolio_title: exportfolio_title.val(),
                        portfolio_image: exportfolio_image.val(),
                        portfolio_url: exportfolio_url.val(),
                        portfolio_vurl: exportfolio_vurl.val(),
                        action: 'jobsearch_add_project_portfoliofield',
                    },
                    dataType: "json"
                });
                request.done(function (msg) {
                    $("#jobsearch-portfoliofields-con").append(msg.html);
                    exportfolio_title.val('');
                    $('#portfolio_image_' + _this_rand + '-box').hide();
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
            alert(jobsearch_plugin_vars.require_fields);
            return false;
        }
    });

    $(document).on('click', '#jobsearch-add-award-exfield', function () {
        var _this = $(this);
        var _this_rand = _this.data('id');
        var loader_img = jobsearch_plugin_vars.plugin_url + 'images/ajax-loader.gif';

        var ajax_url = jobsearch_plugin_vars.ajax_url;
        var this_loader = $(this).next('.ajax-loader');

        var exaward_title = $('#award_title');
        var exaward_year = $('#award_year');
        var exaward_description = $('#award_description');
        if (exaward_title.val() != '') {
            if (!_this.hasClass('ajax-disabled')) {
                this_loader.html('<img alt="" src="' + loader_img + '">');
                var request = $.ajax({
                    url: ajax_url,
                    method: "POST",
                    data: {
                        award_title: exaward_title.val(),
                        exaward_year: exaward_year.val(),
                        award_description: exaward_description.val(),
                        action: 'jobsearch_add_project_awardfield',
                    },
                    dataType: "json"
                });
                request.done(function (msg) {
                    $("#jobsearch-awardfields-con").append(msg.html);
                    exaward_title.val('');
                    exaward_year.val('');
                    exaward_description.val('');
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
            alert(jobsearch_plugin_vars.require_fields);
            return false;
        }
    });

    $(document).on('click', '#jobsearch-add-skill-exfield', function () {
        var _this = $(this);
        var _this_rand = _this.data('id');
        var loader_img = jobsearch_plugin_vars.plugin_url + 'images/ajax-loader.gif';

        var ajax_url = jobsearch_plugin_vars.ajax_url;
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
                        exskill_percentage: exskill_percentage.val(),
                        action: 'jobsearch_add_project_skillfield',
                    },
                    dataType: "json"
                });
                request.done(function (msg) {
                    $("#jobsearch-skillfields-con").append(msg.html);
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
            alert(jobsearch_plugin_vars.require_fields);
            return false;
        }
    });

    $(document).on('click', '#jobsearch-add-lang-exfield', function () {
        var _this = $(this);
        var _this_rand = _this.data('id');
        var loader_img = jobsearch_plugin_vars.plugin_url + 'images/ajax-loader.gif';

        var ajax_url = jobsearch_plugin_vars.ajax_url;
        var this_loader = $(this).next('.ajax-loader');

        var exlang_title = $('#lang_title');
        var exlang_level = $('#lang_level');
        var exlang_percentage = $('#lang_percentage');
        if (exlang_title.val() != '') {
            if (!_this.hasClass('ajax-disabled')) {
                this_loader.html('<img alt="" src="' + loader_img + '">');
                var request = $.ajax({
                    url: ajax_url,
                    method: "POST",
                    data: {
                        lang_title: exlang_title.val(),
                        exlang_level: exlang_level.val(),
                        exlang_percentage: exlang_percentage.val(),
                        action: 'jobsearch_add_project_langfield',
                    },
                    dataType: "json"
                });
                request.done(function (msg) {
                    $("#jobsearch-langfields-con").append(msg.html);
                    exlang_title.val('');
                    exlang_percentage.val('');
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
            alert(jobsearch_plugin_vars.require_fields);
            return false;
        }
    });


    $(document).on('click', '#jobsearch-add-team-exfield', function () {
        var _this = $(this);
        var _this_rand = _this.data('id');
        var loader_img = jobsearch_plugin_vars.plugin_url + 'images/ajax-loader.gif';

        var ajax_url = jobsearch_plugin_vars.ajax_url;
        var this_loader = $(this).next('.ajax-loader');

        var exteam_title = $('#team_title');
        var exteam_designation = $('#team_designation');
        var exteam_experience = $('#team_experience');
        var exteam_image = $('#team_image_' + _this_rand);
        var exteam_facebook = $('#team_facebook');
        var exteam_google = $('#team_google');
        var exteam_twitter = $('#team_twitter');
        var exteam_linkedin = $('#team_linkedin');
        var exteam_description = $('#team_description');
        if (exteam_title.val() != '') {
            if (!_this.hasClass('ajax-disabled')) {
                this_loader.html('<img alt="" src="' + loader_img + '">');
                var request = $.ajax({
                    url: ajax_url,
                    method: "POST",
                    data: {
                        team_title: exteam_title.val(),
                        exteam_designation: exteam_designation.val(),
                        exteam_experience: exteam_experience.val(),
                        exteam_image: exteam_image.val(),
                        exteam_facebook: exteam_facebook.val(),
                        exteam_google: exteam_google.val(),
                        exteam_twitter: exteam_twitter.val(),
                        exteam_linkedin: exteam_linkedin.val(),
                        team_description: exteam_description.val(),
                        action: 'jobsearch_add_project_teamfield',
                    },
                    dataType: "json"
                });
                request.done(function (msg) {
                    $("#jobsearch-teamfields-con").append(msg.html);
                    exteam_title.val('');
                    exteam_designation.val('');
                    exteam_experience.val('');
                    exteam_image.val('');
                    $('#team_image_' + _this_rand + '-box').hide();
                    exteam_facebook.val('');
                    exteam_google.val('');
                    exteam_twitter.val('');
                    exteam_linkedin.val('');
                    exteam_description.val('');
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
            alert(jobsearch_plugin_vars.require_fields);
            return false;
        }
    });

    $(document).on('click', '#jobsearch-add-empaward-exfield', function () {
        var _this = $(this);
        var _this_rand = _this.data('id');
        var loader_img = jobsearch_plugin_vars.plugin_url + 'images/ajax-loader.gif';

        var ajax_url = jobsearch_plugin_vars.ajax_url;
        var this_loader = $(this).next('.ajax-loader');

        var exaward_title = $('#award_title');
        var exaward_image = $('#award_image_' + _this_rand);
        if (exaward_title.val() != '' && exaward_image.val() != '') {
            if (!_this.hasClass('ajax-disabled')) {
                this_loader.html('<img alt="" src="' + loader_img + '">');
                var request = $.ajax({
                    url: ajax_url,
                    method: "POST",
                    data: {
                        award_title: exaward_title.val(),
                        exaward_image: exaward_image.val(),
                        action: 'jobsearch_add_projectemp_awardfield',
                    },
                    dataType: "json"
                });
                request.done(function (msg) {
                    $("#jobsearch-awardfields-con").append(msg.html);
                    exaward_title.val('');
                    exaward_image.val('');
                    $('#award_image_' + _this_rand + '-box').hide();
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
            alert(jobsearch_plugin_vars.require_fields);
            return false;
        }
    });

    $(document).on('click', '#jobsearch-add-empaffiliation-exfield', function () {
        var _this = $(this);
        var _this_rand = _this.data('id');
        var loader_img = jobsearch_plugin_vars.plugin_url + 'images/ajax-loader.gif';

        var ajax_url = jobsearch_plugin_vars.ajax_url;
        var this_loader = $(this).next('.ajax-loader');

        var exaffiliation_title = $('#affiliation_title');
        var exaffiliation_image = $('#affiliation_image_' + _this_rand);
        if (exaffiliation_title.val() != '' && exaffiliation_image.val() != '') {
            if (!_this.hasClass('ajax-disabled')) {
                this_loader.html('<img alt="" src="' + loader_img + '">');
                var request = $.ajax({
                    url: ajax_url,
                    method: "POST",
                    data: {
                        affiliation_title: exaffiliation_title.val(),
                        exaffiliation_image: exaffiliation_image.val(),
                        action: 'jobsearch_add_projectemp_affiliationfield',
                    },
                    dataType: "json"
                });
                request.done(function (msg) {
                    $("#jobsearch-affiliationfields-con").append(msg.html);
                    exaffiliation_title.val('');
                    exaffiliation_image.val('');
                    $('#affiliation_image_' + _this_rand + '-box').hide();
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
            alert(jobsearch_plugin_vars.require_fields);
            return false;
        }
    });
});