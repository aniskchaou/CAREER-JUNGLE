jQuery.fn.extend({
    JobsearchSearchBoxAutocomplete: function (options) {

        return this.each(function () {

            var srchboxFilterAjax;

            var input = jQuery(this), opts = jQuery.extend({}, jQuery.JobsearchSearchBoxAutocomplete);
            var predictionsDropDown = jQuery('<div class="sugg-search-results"></div>').appendTo(jQuery(this).parent());
            var request_var = 1;
            input.keyup(function () {

                jQuery(this).parent(".jobsearch-sugges-search").find('.sugg-search-loader').html("<i class='fa fa-refresh fa-spin'></i>");
                if (request_var == 1) {
                    var searchStr = jQuery(this).val();
                    var post_type = jQuery(this).attr('data-type');
                    // Min Number of characters
                    var num_of_chars = 0;
                    if (searchStr != ' ' && searchStr != '  ' && searchStr.length > num_of_chars) {
                        // AJAX GET results
                        var dataString = 'action=jobsearch_get_search_box_posts_results' + '&keyword=' + searchStr + '&post_type=' + post_type;
                        var plugin_url = jobsearch_plugin_vars.ajax_url;

                        if (typeof (srchboxFilterAjax) != 'undefined') {
                            srchboxFilterAjax.abort();
                        }

                        srchboxFilterAjax = jQuery.ajax({
                            type: "POST",
                            url: plugin_url,
                            data: dataString,
                        });

                        srchboxFilterAjax.done(function (data) {
                            jQuery(".jobsearch-sugges-search").find('.sugg-search-loader').html('');
                            var results = jQuery.parseJSON(data);
                            predictionsDropDown.empty();
                            if (results != '') {
                                jQuery(results).each(function (key, value) {
                                    if (typeof value.item_all !== 'undefined') {
                                        predictionsDropDown.append(value.item_all);
                                    } else {
                                        predictionsDropDown.append('<div class="search-res-item">' + value.item + '</div>');
                                    }
                                })
                            }
                            var search_stragin = input.val();
                            if (search_stragin == '') {
                                jQuery('.jobsearch-sugges-search').find('.sugg-search-results').html('');
                            }
                            request_var = 1;
                        });

                        srchboxFilterAjax.fail(function (jqXHR, textStatus) {
                            //jQuery(".jobsearch-sugges-search").find('.sugg-search-loader').html('');
                        });

                        predictionsDropDown.show();
                    } else {
                        jQuery(".jobsearch-sugges-search").find('.sugg-search-loader').html('');
                        jQuery('.jobsearch-sugges-search').find('.sugg-search-results').hide();
                        jQuery('.jobsearch-sugges-search').find('.sugg-search-results').html('');
                    }
                }
            });
            return input;
        });
    }
});

jQuery(document).on('click', '.jobsearch-sugges-search input[type="text"]', function() {
    jQuery('.jobsearch-sugges-search input[type="text"]').JobsearchSearchBoxAutocomplete();
});

jQuery(function ($) {
    $('body').click(function (e) {
        var clickedOn = $(e.target);
        if (clickedOn.parents().addBack().is('.jobsearch-sugges-search')) {
            //
        } else {
            jQuery('.jobsearch-sugges-search').find('.sugg-search-results').hide();
        }
    });
    
    jQuery(document).on('click', '.jobsearch-sugges-search .show-all-results a', function() {
        $(this).parents('form').submit();
    });
    
    jQuery(document).on('click', '.jobsearch-sugges-search .search-res-item', function() {
        var anc_location = $(this).find('.post-title a').attr('href');
        window.location.href = anc_location;
    });
});