<?php

/**
 * Advance Search Shortcode
 * @return html
 */
add_shortcode('jobsearch_banner_advertisement', 'jobsearch_banner_advertisement_callback');

function jobsearch_banner_advertisement_callback($atts) {
    global $jobsearch_plugin_options;

    extract(shortcode_atts(array(
        'banner_style' => '',
        'banner_sinle_style' => '',
        'banner_group_style' => '',
                    ), $atts));

    $shortcode_html = '';
    if (isset($banner_style) && $banner_style == 'group_banner') {
        $shortcode_html = '[jobsearch_ads_group code="' . $banner_group_style . '"]';
    } else {
        $shortcode_html = '[jobsearch_ad code="' . $banner_sinle_style . '"]';
    }
    ob_start();
    echo do_shortcode($shortcode_html);
    $ad_html = ob_get_clean();
    return $ad_html;
}
