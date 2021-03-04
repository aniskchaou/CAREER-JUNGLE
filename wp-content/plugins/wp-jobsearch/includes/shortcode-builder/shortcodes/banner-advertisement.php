<?php

global $jobsearch_plugin_options;
$groups_value = isset($jobsearch_plugin_options['ad_banner_groups']) ? $jobsearch_plugin_options['ad_banner_groups'] : '';
$sinle_value = isset($jobsearch_plugin_options['ad_banners_list']) ? $jobsearch_plugin_options['ad_banners_list'] : '';

$group_add_arr = array('' => esc_html__('Select banner', 'wp-jobsearch'));
if (isset($groups_value) && !empty($groups_value) && is_array($groups_value)) {
    for ($ad = 0; $ad < count($groups_value['group_title']); $ad++) {
        $ad_title = $groups_value['group_title'][$ad];
        $ad_code = $groups_value['group_code'][$ad];
        $group_add_arr[$ad_code] = $ad_title;
    }
}
$single_add_arr = array('' => esc_html__('Select banner', 'wp-jobsearch'));
if (isset($sinle_value) && !empty($sinle_value) && is_array($sinle_value)) {
    for ($ad = 0; $ad < count($sinle_value['banner_title']); $ad++) {
        $ad_title = $sinle_value['banner_title'][$ad];
        $ad_code = $sinle_value['banner_code'][$ad];
        $single_add_arr[$ad_code] = $ad_title;
    }
}

$jobsearch_builder_shortcodes['jobsearch_banner_advertisement'] = array(
    'title' => esc_html__('Banner Advertisement', 'wp-jobsearch'),
    'id' => 'jobsearch-banner-advertisement',
    'template' => '[jobsearch_banner_advertisement {{attributes}}] {{content}} [/jobsearch_banner_advertisement]',
    'params' => array(
        'banner_style' => array(
            'type' => 'select',
            'label' => esc_html__('Banner Style', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'single_banner' => esc_html__("Single Banner", "wp-jobsearch"),
                'group_banner' => esc_html__("Group Banner ", "wp-jobsearch"),
            )
        ),
        'banner_sinle_style' => array(
            'type' => 'select',
            'label' => esc_html__('Single Style', 'wp-jobsearch'),
            'desc' => '',
            'options' => $single_add_arr,
        ),
        'banner_group_style' => array(
            'type' => 'select',
            'label' => esc_html__('Group Style', 'wp-jobsearch'),
            'desc' => '',
            'options' => $group_add_arr,
        ),
    )
);
