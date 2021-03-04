<?php

global $jobsearch_plugin_options;

$all_page = array();

$args = array(
    'sort_order' => 'asc',
    'sort_column' => 'post_title',
    'hierarchical' => 1,
    'exclude' => '',
    'include' => '',
    'meta_key' => '',
    'meta_value' => '',
    'authors' => '',
    'child_of' => 0,
    'parent' => -1,
    'exclude_tree' => '',
    'number' => '',
    'offset' => 0,
    'post_type' => 'page',
    'post_status' => 'publish'
);
$pages = get_pages($args);
if (!empty($pages)) {
    foreach ($pages as $page) {
        $all_page[$page->post_name] = $page->post_title;
    }
}

$required_fields_count = isset($jobsearch_plugin_options['jobsearch-location-required-fields-count']) ? $jobsearch_plugin_options['jobsearch-location-required-fields-count'] : 'all';

$params_arr = array();
$params_arr['result_page'] = array(
    'type' => 'select',
    'label' => esc_html__('Search Result Page', 'wp-jobsearch'),
    'desc' => esc_html__('Select the Search Result Page.', 'wp-jobsearch'),
    'options' => $all_page
);
$params_arr['keyword_field'] = array(
    'type' => 'select',
    'label' => esc_html__('Keyword Field', 'wp-jobsearch'),
    'desc' => esc_html__("Show/Hide Keyword Field.", 'wp-jobsearch'),
    'options' => array(
        'show' => esc_html__('Show', 'wp-jobsearch'),
        'hide' => esc_html__('Hide', 'wp-jobsearch'),
    )
);
$params_arr['cat_field'] = array(
    'type' => 'select',
    'label' => esc_html__('Sector Field', 'wp-jobsearch'),
    'desc' => esc_html__("Show/Hide Sector Field.", 'wp-jobsearch'),
    'options' => array(
        'show' => esc_html__('Show', 'wp-jobsearch'),
        'hide' => esc_html__('Hide', 'wp-jobsearch'),
    )
);
$params_arr['job_type_field'] = array(
    'type' => 'select',
    'label' => esc_html__('Job Type Field', 'wp-jobsearch'),
    'desc' => esc_html__("Show/Hide Job Type Field.", 'wp-jobsearch'),
    'options' => array(
        'show' => esc_html__('Show', 'wp-jobsearch'),
        'hide' => esc_html__('Hide', 'wp-jobsearch'),
    )
);
$params_arr['loc_field'] = array(
    'type' => 'select',
    'label' => esc_html__('Locations Field', 'wp-jobsearch'),
    'desc' => esc_html__("Show/Hide Locations Field.", 'wp-jobsearch'),
    'options' => array(
        'show' => esc_html__('Show', 'wp-jobsearch'),
        'hide' => esc_html__('Hide', 'wp-jobsearch'),
    )
);
$params_arr['loc_type'] = array(
    'type' => 'select',
    'label' => esc_html__('Locations Field Type', 'wp-jobsearch'),
    'desc' => esc_html__("Select Location Field Type.", 'wp-jobsearch'),
    'options' => array(
        'dropdown' => esc_html__('Dropdown Fields', 'wp-jobsearch'),
        'input' => esc_html__('User Input Field', 'wp-jobsearch'),
    )
);
$params_arr['loc_field1'] = array(
    'type' => 'select',
    'label' => esc_html__('Locations Field 1', 'wp-jobsearch'),
    'desc' => esc_html__("Show/Hide Locations Field 1.", 'wp-jobsearch'),
    'options' => array(
        'show' => esc_html__('Show', 'wp-jobsearch'),
        'hide' => esc_html__('Hide', 'wp-jobsearch'),
    )
);
if ($required_fields_count > 1 || $required_fields_count == 'all') {
    $params_arr['loc_field2'] = array(
        'type' => 'select',
        'label' => esc_html__('Locations Field 2', 'wp-jobsearch'),
        'desc' => esc_html__("Show/Hide Locations Field 2.", 'wp-jobsearch'),
        'options' => array(
            'show' => esc_html__('Show', 'wp-jobsearch'),
            'hide' => esc_html__('Hide', 'wp-jobsearch'),
        )
    );
}
if ($required_fields_count > 2 || $required_fields_count == 'all') {
    $params_arr['loc_field3'] = array(
        'type' => 'select',
        'label' => esc_html__('Locations Field 3', 'wp-jobsearch'),
        'desc' => esc_html__("Show/Hide Locations Field 3.", 'wp-jobsearch'),
        'options' => array(
            'show' => esc_html__('Show', 'wp-jobsearch'),
            'hide' => esc_html__('Hide', 'wp-jobsearch'),
        )
    );
}
if ($required_fields_count > 3 || $required_fields_count == 'all') {
    $params_arr['loc_field4'] = array(
        'type' => 'select',
        'label' => esc_html__('Locations Field 4', 'wp-jobsearch'),
        'desc' => esc_html__("Show/Hide Locations Field 4.", 'wp-jobsearch'),
        'options' => array(
            'show' => esc_html__('Show', 'wp-jobsearch'),
            'hide' => esc_html__('Hide', 'wp-jobsearch'),
        )
    );
}
if ($required_fields_count > 1 || $required_fields_count == 'all') {
    $label_location1 = isset($jobsearch_plugin_options['jobsearch-location-label-location1']) ? $jobsearch_plugin_options['jobsearch-location-label-location1'] : esc_html__('Country', 'wp-jobsearch');
    $label_location2 = isset($jobsearch_plugin_options['jobsearch-location-label-location2']) ? $jobsearch_plugin_options['jobsearch-location-label-location2'] : esc_html__('State', 'wp-jobsearch');
    //
    $please_select = esc_html__('Please select', 'wp-jobsearch');
    $location_location1 = array('' => $please_select . ' ' . $label_location1);
//    $location_obj = get_terms('job-location', array(
//        'orderby' => 'count',
//        'hide_empty' => 0,
//        'parent' => 0,
//    ));
    $location_obj = jobsearch_custom_get_terms('job-location');
    foreach ($location_obj as $country_arr) {
        $location_location1[$country_arr->slug] = $country_arr->name;
    }
    //
    $params_arr['loc_locate_1'] = array(
        'type' => 'select',
        'label' => sprintf(esc_html__('Select %s', 'wp-jobsearch'), $label_location1),
        'desc' => sprintf(esc_html__('If you will select %s, then all %s for selected %s will show in search.', 'wp-jobsearch'), $label_location1, $label_location2, $label_location1),
        'options' => $location_location1,
    );
}
$params_arr['serch_btn_txt'] = array(
    'std' => '',
    'type' => 'text',
    'label' => esc_html__('Search the Button Text', 'wp-jobsearch'),
    'desc' => esc_html__('Select the Search Button Text.', 'wp-jobsearch'),
);
$params_arr['serch_txt_color'] = array(
    'std' => '',
    'classes' => 'jobsearch-bk-color',
    'type' => 'text',
    'label' => esc_html__('Search the Button Text Color', 'wp-jobsearch'),
    'desc' => esc_html__('Select the Search Button Text Color.', 'wp-jobsearch'),
);
$params_arr['serch_bg_color'] = array(
    'std' => '',
    'classes' => 'jobsearch-bk-color',
    'type' => 'text',
    'label' => esc_html__('Search the Button Background Color', 'wp-jobsearch'),
    'desc' => esc_html__('Select the Search Button Background Color.', 'wp-jobsearch'),
);
$params_arr['serch_hov_color'] = array(
    'std' => '',
    'classes' => 'jobsearch-bk-color',
    'type' => 'text',
    'label' => esc_html__('Search the Button Hover Color', 'wp-jobsearch'),
    'desc' => esc_html__('Select the Search Button Hover Color.', 'wp-jobsearch'),
);
$jobsearch_builder_shortcodes['advance_search'] = array(
    'title' => esc_html__('Advance Search', 'wp-jobsearch'),
    'id' => 'jobsearch-advance-search-shortcode',
    'template' => '[jobsearch_advance_search {{attributes}}] {{content}} [/jobsearch_advance_search]',
    'params' => $params_arr,
);
