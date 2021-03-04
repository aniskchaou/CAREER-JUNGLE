var icons_load_call;
var loaded_icons;
jQuery(document).ready(function ($) {
    'use strict';
    var plugin_url = wp_jobsearch_icons_vars.plugin_url;

    icons_load_call = $.getJSON(plugin_url + "icon-picker/js/selection.json")
    .done(function (response) {
        loaded_icons = response;
    });
});