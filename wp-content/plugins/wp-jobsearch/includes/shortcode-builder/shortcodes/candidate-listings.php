<?php

$categories = get_terms(array(
    'taxonomy' => 'sector',
    'hide_empty' => false,
        ));

$cate_array = array('' => esc_html__("Select Sector", "wp-jobsearch"));
if (is_array($categories) && sizeof($categories) > 0) {
    foreach ($categories as $category) {
        $cate_array[$category->slug] = $category->name;
    }
}

$jobsearch_cand_cus_fields = get_option("jobsearch_custom_field_candidate");
$cand_cus_field_arr = array();
if (isset($jobsearch_cand_cus_fields) && !empty($jobsearch_cand_cus_fields) && sizeof($jobsearch_cand_cus_fields) > 0) {
    foreach ($jobsearch_cand_cus_fields as $key => $value) {
        $cand_cus_field_arr[$key] = $value['label'];
    }
}

$jobsearch_builder_shortcodes['candidate_listings'] = array(
    'title' => esc_html__('Candidate Listing', 'wp-jobsearch'),
    'id' => 'jobsearch-candidate-listings-shortcode',
    'template' => '[jobsearch_candidate_shortcode {{attributes}}] {{content}} [/jobsearch_candidate_shortcode]',
    'params' => apply_filters('jobsearch_candidate_listings_sheb_params', array(
        'candidate_cat' => array(
            'type' => 'select',
            'label' => esc_html__('Sector', 'wp-jobsearch'),
            'desc' => esc_html__('Select Sector.', 'wp-jobsearch'),
            'options' => $cate_array
        ),
        'display_per_page' => array(
            'type' => 'select',
            'label' => esc_html__('Candidate Founds with display counts', 'wp-jobsearch'),
            'desc' => esc_html__("Display the per page candidates count at top of the listing.", 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'candidate_filters' => array(
            'type' => 'select',
            'label' => esc_html__('Filters', 'wp-jobsearch'),
            'desc' => esc_html__('Candidates searching filters switch.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'candidate_loc_listing' => array(
            'type' => 'multi_checkbox',
            'label' => esc_html__('Locations in listing', 'wp-jobsearch'),
            'desc' => esc_html__('Select which type of location in listing. If nothing select then full address will display.', 'wp-jobsearch'),
            'options' => array(
                'country' => esc_html__("Country", "wp-jobsearch"),
                'state' => esc_html__("State", "wp-jobsearch"),
                'city' => esc_html__("City", "wp-jobsearch"),
            ),
        ),
        'candidate_filters_count' => array(
            'type' => 'select',
            'label' => esc_html__('Filters Count', 'wp-jobsearch'),
            'desc' => esc_html__('Show result counts in front of every filter.', 'wp-jobsearch'),
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'candidate_filters_sortby' => array(
            'type' => 'select',
            'label' => esc_html__('Filters Sort by', 'wp-jobsearch'),
            'desc' => esc_html__('Set filters sorting.', 'wp-jobsearch'),
            'options' => array(
                'default' => esc_html__('Default', 'wp-jobsearch'),
                'asc' => esc_html__('Ascending', 'wp-jobsearch'),
                'desc' => esc_html__('Descending', 'wp-jobsearch'),
                'alpha' => esc_html__('Alphabetical', 'wp-jobsearch'),
                'count' => esc_html__('Highest Count', 'wp-jobsearch'),
            )
        ),
        'candidate_filters_date' => array(
            'type' => 'select',
            'label' => esc_html__('Posted Date Filter', 'wp-jobsearch'),
            'desc' => esc_html__('Candidates searching filters "Posted Date" switch.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'candidate_filters_date_collapse' => array(
            'type' => 'select',
            'label' => esc_html__('Posted Date Filter Collapse', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'candidate_filters_sector' => array(
            'type' => 'select',
            'label' => esc_html__('Candidate Sector Filter', 'wp-jobsearch'),
            'desc' => esc_html__('Candidates searching filters "Sector" switch.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'candidate_filters_sector_collapse' => array(
            'type' => 'select',
            'label' => esc_html__('Candidate Sector Filter Collapse', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'cand_top_map' => array(
            'type' => 'select',
            'label' => esc_html__('Top Map', 'wp-jobsearch'),
            'desc' => esc_html__('Candidates top map switch.', 'wp-jobsearch'),
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'cand_top_map_height' => array(
            'std' => '450',
            'type' => 'text',
            'label' => esc_html__('Map Height', 'wp-jobsearch'),
            'desc' => esc_html__('Candidates top map height.', 'wp-jobsearch'),
        ),
        'cand_top_map_zoom' => array(
            'std' => '8',
            'type' => 'text',
            'label' => esc_html__('Map Zoom', 'wp-jobsearch'),
            'desc' => esc_html__('Candidates top map zoom.', 'wp-jobsearch'),
        ),
        'cand_top_search' => array(
            'type' => 'select',
            'label' => esc_html__('Top Search Bar', 'wp-jobsearch'),
            'desc' => esc_html__('Results top search bar section switch.', 'wp-jobsearch'),
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'cand_top_search_view' => array(
            'type' => 'select',
            'label' => esc_html__('Top Search Style', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'simple' => esc_html__('Simple', 'wp-jobsearch'),
                'advance' => esc_html__('Advance Search', 'wp-jobsearch'),
            )
        ),
        'cand_top_search_title' => array(
            'type' => 'select',
            'label' => esc_html__("Top Search Title, Keyword", "wp-jobsearch"),
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            ),
            'desc' => esc_html__("Enable/Disable search keyword field.", "wp-jobsearch"),
        ),
        'cand_top_search_location' => array(
            'type' => 'select',
            'label' => esc_html__("Top Search Location", "wp-jobsearch"),
            'param_name' => 'emp_top_search_location',
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            ),
            'desc' => esc_html__("Enable/Disable location field.", "wp-jobsearch"),
        ),
        'cand_top_search_sector' => array(
            'type' => 'select',
            'label' => esc_html__("Top Search Sector", "wp-jobsearch"),
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            ),
            'desc' => esc_html__("Enable/Disable Sector Dropdown field.", "wp-jobsearch"),
        ),
        'top_search_autofill' => array(
            'type' => 'select',
            'label' => esc_html__('AutoFill Search Box', 'wp-jobsearch'),
            'desc' => esc_html__('Enable/Disable autofill in search keyword field.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'candidate_custom_fields_switch' => array(
            'type' => 'select',
            'label' => esc_html__('Custom Fields', 'wp-jobsearch'),
            'desc' => esc_html__('Show Custom Fields in every list.', 'wp-jobsearch'),
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'candidate_elem_custom_fields' => array(
            'type' => 'multi_checkbox',
            'label' => esc_html__('Select Custom Fields', 'wp-jobsearch'),
            'desc' => '',
            'options' => $cand_cus_field_arr
        ),
        'candidate_sort_by' => array(
            'type' => 'select',
            'label' => esc_html__('Sort by Fields', 'wp-jobsearch'),
            'desc' => esc_html__('Results search sorting section switch.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'candidate_excerpt' => array(
            'std' => '20',
            'type' => 'text',
            'label' => esc_html__('Excerpt Length', 'wp-jobsearch'),
            'desc' => esc_html__('Set the number of words you want to show for excerpt.', 'wp-jobsearch'),
        ),
        'candidate_order' => array(
            'type' => 'select',
            'label' => esc_html__('Order', 'wp-jobsearch'),
            'desc' => esc_html__('Choose candidate list items order.', 'wp-jobsearch'),
            'options' => array(
                'DESC' => esc_html__('Descending', 'wp-jobsearch'),
                'ASC' => esc_html__('Ascending', 'wp-jobsearch'),
            )
        ),
        'candidate_orderby' => array(
            'type' => 'select',
            'label' => esc_html__('Orderby', 'wp-jobsearch'),
            'desc' => esc_html__('Choose candidate list items orderby.', 'wp-jobsearch'),
            'options' => array(
                'ID' => esc_html__('Date', 'wp-jobsearch'),
                'title' => esc_html__('Title', 'wp-jobsearch'),
                'promote_profile' => esc_html__('Promote Profile', 'wp-jobsearch'),
            )
        ),
        'candidate_pagination' => array(
            'type' => 'select',
            'label' => esc_html__('Pagination', 'wp-jobsearch'),
            'desc' => esc_html__('Choose yes if you want to show pagination for candidate items.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'candidate_per_page' => array(
            'std' => '10',
            'type' => 'text',
            'label' => esc_html__('Records per Page', 'wp-jobsearch'),
            'desc' => esc_html__('Set number that how much candidates you want to show per page. Leave it blank for all candidates on a single page.', 'wp-jobsearch'),
        ),
    ))
);
