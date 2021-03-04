<?php

add_action('wp_footer', 'wp_jobsearch_all_js_footer_scripts', 999);
add_action('admin_footer', 'wp_jobsearch_all_js_footer_scripts', 999);

function wp_jobsearch_all_js_footer_scripts() {
    ob_start();
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function () {

        if (jQuery('.fancybox-galimg').length > 0) {
            //*** Function FancyBox
            jQuery(".fancybox-galimg").fancybox({
                openEffect: 'elastic',
                closeEffect: 'elastic',
                beforeLoad: function () {
                    var caption = this.element.attr('data-caption');
                    this.tpl.wrap = '<div class="fancybox-wrap" tabIndex="-1"><div class="fancybox-skin"><div class="fancybox-outer"><div class="fancybox-inner"></div><div class="fancybox-title fancybox-title-float-wrap"><span class="gal-img-desc child">' + caption + '</span></div></div></div></div>'

                },
                helpers: {
                    title: {
                        type: 'outside',
                        position: 'top'
                    }
                }
            });
        }

        if (jQuery('.fancybox-video').length > 0) {
            //*** Function FancyBox
            jQuery('.fancybox-video').fancybox({
                maxWidth: 800,
                maxHeight: 600,
                fitToView: false,
                width: '70%',
                height: '70%',
                autoSize: false,
                closeClick: false,
                openEffect: 'none',
                closeEffect: 'none',
                beforeLoad: function () {
                    var caption = this.element.attr('data-caption');
                    this.tpl.wrap = '<div class="fancybox-wrap" tabIndex="-1"><div class="fancybox-skin"><div class="fancybox-outer"><div class="fancybox-inner"></div><div class="fancybox-title fancybox-title-float-wrap"><span class="gal-img-desc child">' + caption + '</span></div></div></div></div>'

                },
                helpers: {
                    title: {
                        type: 'outside',
                        position: 'top'
                    }
                }
            });

        }

        if (jQuery('.jobsearch_progressbar1').length > 0) {
            jQuery('.jobsearch_progressbar1').progressBar({
                percentage: false,
                backgroundColor: "#dbdbdb",
                barColor: jobsearch_plugin_vars.careerfy_theme_color,
                animation: true,
                height: "6",
            });
        }

        if (jQuery('.careerfy_progressbar_candidate_style5').length > 0) {
            jQuery('.careerfy_progressbar_candidate_style5').progressBar({
                percentage: true,
                backgroundColor: "#dbdbdb",
                barColor: jobsearch_plugin_vars.careerfy_theme_color,
                animation: true,
                height: "6",
            });
        }

        // selectize
        if (jQuery('.selectize-select').length > 0) {
            jQuery('.selectize-select').selectize({
                //allowEmptyOption: true,
                plugins: ['remove_button'],
            });
        }
        
        // for dependent fields
        if (jQuery('.depndfield-selectize').length > 0) {
            jQuery('.depndfield-selectize').selectize({
                render: {
                    option: function (data, escape) {
                        return "<div data-depend='" + data.depend + "' data-optid='" + data.optid + "'>" + data.text + "</div>"
                    }
                },
                plugins: ['remove_button'],
            });
        }

        if (jQuery('.sort-records-per-page').length > 0) {
            jQuery('.sort-records-per-page').selectize({
                allowEmptyOption: true,
            });
        }

        //
        var loc_con_main = jQuery('#jobsearch-findby-sectors').find('.jobsearch-sects-allcon');

        if (loc_con_main.length > 0) {
            var loc_contain_li = loc_con_main.find('>li');

            if (loc_contain_li.length > 0) {
                var sect_view_str = jQuery('#jobsearch-findby-sectors').data('view');
                var location_ids_sect = [];
                jQuery.each(loc_contain_li, function () {
                    var _this_li = jQuery(this);
                    var _counter_con = _this_li.find('.jobsearchh-sect-childcount');
                    if (_counter_con.length > 0) {
                        _counter_con.html('<em class="fa fa-refresh fa-spin"></em>');
                        var locat_id = _counter_con.attr('data-id');
                        location_ids_sect.push(locat_id);
                    }
                });

                if (location_ids_sect.length > 0) {
                    var locate_ids_str = location_ids_sect.join();
                    var loc_counts_request = jQuery.ajax({
                        url: jobsearch_plugin_vars.ajax_url,
                        method: "POST",
                        data: {
                            sect_view: sect_view_str,
                            locat_ids: locate_ids_str,
                            action: 'jobsearch_sectscount_add_to_spancons_action',
                        },
                        dataType: "json"
                    });


                    loc_counts_request.done(function (response) {
                        var resp_ids_arr;
                        if (typeof response.counts !== 'undefined' && response.counts != '') {
                            var resp_ids_str = response.counts;
                            resp_ids_arr = resp_ids_str.split(',');
                        }
             
                        jQuery.each(location_ids_sect, function (index, id) {
                            var count_val = resp_ids_arr[index];
                            var _this_countr_con = jQuery('#jobsearchh-sect-cat-item-' + id);
                            _this_countr_con.html(count_val);
                        });
                    });

                    loc_counts_request.fail(function (jqXHR, textStatus) {
                        jQuery.each(location_ids, function (index, id) {
                            var _this_countr_con = jQuery('#jobsearchh-sect-cat-item-' + id);
                            _this_countr_con.html('0');
                        });
                    });
                }
            }
        }
        
        //
        var loc_con_main = jQuery('#jobsearch-findby-jobtypes').find('.jobsearch-sects-allcon');
        if (loc_con_main.length > 0) {
            var loc_contain_li = loc_con_main.find('>li');
            if (loc_contain_li.length > 0) {
                var sect_view_str = jQuery('#jobsearch-findby-jobtypes').data('view');
                var location_ids = [];
                jQuery.each(loc_contain_li, function () {
                    var _this_li = jQuery(this);
                    var _counter_con = _this_li.find('.jobsearchh-type-childcount');
                    if (_counter_con.length > 0) {
                        _counter_con.html('<em class="fa fa-refresh fa-spin"></em>');
                        var locat_id = _counter_con.attr('data-id');
                        location_ids.push(locat_id);
                    }
                });
                if (location_ids.length > 0) {
                    var locate_ids_str = location_ids.join();
                    var loc_counts_request = jQuery.ajax({
                        url: jobsearch_plugin_vars.ajax_url,
                        method: "POST",
                        data: {
                            sect_view: sect_view_str,
                            locat_ids: locate_ids_str,
                            action: 'jobsearch_jobtypecount_add_to_spancons_action',
                        },
                        dataType: "json"
                    });

                    loc_counts_request.done(function (response) {
                        var resp_ids_arr;
                        if (typeof response.counts !== 'undefined' && response.counts != '') {
                            var resp_ids_str = response.counts;
                            resp_ids_arr = resp_ids_str.split(',');
                        }
                        jQuery.each(location_ids, function (index, id) {

                            var count_val = resp_ids_arr[index];
                            var _this_countr_con = jQuery('#jobsearchh-sect-types-item-' + id);
                            _this_countr_con.html(count_val);
                        });
                    });

                    loc_counts_request.fail(function (jqXHR, textStatus) {
                        jQuery.each(location_ids, function (index, id) {
                            var _this_countr_con = jQuery('#jobsearchh-sect-types-item-' + id);
                            _this_countr_con.html('0');
                        });
                    });
                }
            }
        }

        if (window.location.hash !== 'undefined' && window.location.hash == '#_=_') {
            window.location.hash = ''; // for older browsers, leaves a # behind
            history.pushState('', document.title, window.location.pathname); // nice and clean
            e.preventDefault(); // no page reload
        }

        jQuery('.user_field').on('click', function (e) {
            e.preventDefault();
            var this_id = jQuery(this).data('randid'),
                    loaded = jQuery(this).data('loaded'),
                    role = jQuery(this).data('role'),
                    user_field = jQuery('#user_field_' + this_id),
                    ajax_url = jobsearch_plugin_vars.ajax_url,
                    force_std = jQuery(this).data('forcestd');
            if (loaded != true) {
                jQuery('.user_loader_' + this_id).html('<i class="fa fa-refresh fa-spin"></i>');
                var request = jQuery.ajax({
                    url: ajax_url,
                    method: "POST",
                    data: {
                        force_std: force_std,
                        role: role,
                        action: 'jobsearch_load_all_users_data',
                    },
                    dataType: "json"
                });

                request.done(function (response) {
                    if ('undefined' !== typeof response.html) {
                        user_field.html(response.html);
                        jQuery('.user_loader_' + this_id).html('');
                        user_field.data('loaded', true);

                    }
                });

                request.fail(function (jqXHR, textStatus) {
                });
            }
            return false;

        });

        jQuery('.custom_post_field').on('click', function (e) {
            e.preventDefault();
            var this_id = jQuery(this).data('randid'),
                    loaded = jQuery(this).data('loaded'),
                    posttype = jQuery(this).data('posttype'),
                    placelabel = jQuery(this).data('placelabel'),
                    custom_field = jQuery('#custom_post_field_' + this_id),
                    ajax_url = jobsearch_plugin_vars.ajax_url,
                    force_std = jQuery(this).data('forcestd');
            if (loaded != true) {
                jQuery('.custom_post_loader_' + this_id).html('<i class="fa fa-refresh fa-spin"></i>');
                var request = jQuery.ajax({
                    url: ajax_url,
                    method: "POST",
                    data: {
                        force_std: force_std,
                        posttype: posttype,
                        placelabel: placelabel,
                        action: 'jobsearch_load_all_custom_post_data',
                    },
                    dataType: "json"
                });

                request.done(function (response) {
                    if ('undefined' !== typeof response.html) {
                        custom_field.html(response.html);
                        jQuery('.custom_post_loader_' + this_id).html('');
                        custom_field.data('loaded', true);
                    }
                });

                request.fail(function (jqXHR, textStatus) {
                });
            }
            return false;

        });

        if (jQuery('.grid').length > 0 && jQuery('.grid-item').length > 0) {
            //*** Function Masonery
            jQuery('.grid').isotope({
                itemSelector: '.grid-item',
                percentPosition: true,
                masonry: {
                    fitWidth: false
                }
            });
        }
    });
    </script>
    <?php
    $html = ob_get_clean();
    
    echo ($html);
}