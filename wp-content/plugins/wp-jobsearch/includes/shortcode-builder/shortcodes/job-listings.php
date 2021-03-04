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

$jobsearch_job_cus_fields = get_option("jobsearch_custom_field_job");
$job_cus_field_arr = array();
if (isset($jobsearch_job_cus_fields) && !empty($jobsearch_job_cus_fields) && sizeof($jobsearch_job_cus_fields) > 0) {
    foreach ($jobsearch_job_cus_fields as $key => $value) {
        $job_cus_field_arr[$key] = $value['label'];
    }
}

$jobsearch_builder_shortcodes['job_listings'] = array(
    'title' => esc_html__('Jobs Listing', 'wp-jobsearch'),
    'id' => 'jobsearch-job-listings-shortcode',
    'template' => '[jobsearch_job_shortcode {{attributes}}] {{content}} [/jobsearch_job_shortcode]',
    'params' => apply_filters('jobsearch_job_listings_sheb_params', array(
        'job_cat' => array(
            'type' => 'select',
            'label' => esc_html__('Sector', 'wp-jobsearch'),
            'desc' => esc_html__('Select Sector.', 'wp-jobsearch'),
            'options' => $cate_array
        ),
        'display_per_page' => array(
            'type' => 'select',
            'label' => esc_html__('Job Founds with display counts', 'wp-jobsearch'),
            'desc' => esc_html__("Display the per page jobs count at top of the listing.", 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'featured_only' => array(
            'type' => 'select',
            'label' => esc_html__('Featured Only', 'wp-jobsearch'),
            'desc' => esc_html__("If you set Featured Only 'Yes' then only Featured jobs will show.", 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'job_custom_fields_switch' => array(
            'type' => 'select',
            'label' => esc_html__('Custom Fields', 'wp-jobsearch'),
            'desc' => esc_html__('Show Custom Fields in every list.', 'wp-jobsearch'),
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'quick_apply_job' => array(
            'type' => 'select',
            'label' => esc_html__('Quick Apply job', 'wp-jobsearch'),
            'desc' => esc_html__('By setting this option, when user will click on job title or image pop-up will be appear from the side.', 'wp-jobsearch'),
            'options' => array(
                'off' => esc_html__('No', 'wp-jobsearch'),
                'on' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'job_loc_listing' => array(
            'type' => 'multi_checkbox',
            'label' => esc_html__('Locations in listing', 'wp-jobsearch'),
            'desc' => esc_html__('Select which type of location in listing. If nothing select then full address will display.', 'wp-jobsearch'),
            'options' => array(
                'country' => esc_html__("Country", "wp-jobsearch"),
                'state' => esc_html__("State", "wp-jobsearch"),
                'city' => esc_html__("City", "wp-jobsearch"),
            ),
        ),
        'job_deadline_switch' => array(
            'type' => 'select',
            'label' => esc_html__('Job Deadline', 'wp-jobsearch'),
            'desc' => esc_html__('Enable / Disable jobs deadline date in listings.', 'wp-jobsearch'),
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'job_filters' => array(
            'type' => 'select',
            'label' => esc_html__('Filters', 'wp-jobsearch'),
            'desc' => esc_html__('Jobs searching filters switch.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'job_filters_count' => array(
            'type' => 'select',
            'label' => esc_html__('Filters Count', 'wp-jobsearch'),
            'desc' => esc_html__('Show result counts in front of every filter.', 'wp-jobsearch'),
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'job_filters_sortby' => array(
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
        'job_filters_keyword' => array(
            'type' => 'select',
            'label' => esc_html__('Keyword Search Filter', 'wp-jobsearch'),
            'desc' => esc_html__('Keyword Search filter switch.', 'wp-jobsearch'),
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'job_filters_loc' => array(
            'type' => 'select',
            'label' => esc_html__('Locations Filter', 'wp-jobsearch'),
            'desc' => esc_html__('Jobs searching filters "Locations" switch.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'job_filters_loc_collapse' => array(
            'type' => 'select',
            'label' => esc_html__('Locations Filter Collapse', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'job_filters_loc_view' => array(
            'type' => 'select',
            'label' => esc_html__('Locations Filter Style', 'wp-jobsearch'),
            'desc' => esc_html__('Jobs searching filters "Locations" Style.', 'wp-jobsearch'),
            'options' => array(
                'checkboxes' => esc_html__('Checkbox List', 'wp-jobsearch'),
                'dropdowns' => esc_html__('Dropdown Fields', 'wp-jobsearch'),
                'input' => esc_html__('Input Field', 'wp-jobsearch'),
            )
        ),
        'job_filters_date' => array(
            'type' => 'select',
            'label' => esc_html__('Posted Date Filter', 'wp-jobsearch'),
            'desc' => esc_html__('Jobs searching filters "Posted Date" switch.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'job_filters_date_collapse' => array(
            'type' => 'select',
            'label' => esc_html__('Posted Date Filter Collapse', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'job_filters_type' => array(
            'type' => 'select',
            'label' => esc_html__('Job Type Filter', 'wp-jobsearch'),
            'desc' => esc_html__('Jobs searching filters "Job Type" switch.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'job_filters_type_collapse' => array(
            'type' => 'select',
            'label' => esc_html__('Job Type Filter Collapse', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'job_filters_sector' => array(
            'type' => 'select',
            'label' => esc_html__('Job Sector Filter', 'wp-jobsearch'),
            'desc' => esc_html__('Jobs searching filters "Job Sector" switch.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'job_filters_sector_collapse' => array(
            'type' => 'select',
            'label' => esc_html__('Job Sector Filter Collapse', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'job_top_map' => array(
            'type' => 'select',
            'label' => esc_html__('Top Map', 'wp-jobsearch'),
            'desc' => esc_html__('Jobs top map switch.', 'wp-jobsearch'),
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'job_top_map_height' => array(
            'std' => '450',
            'type' => 'text',
            'label' => esc_html__('Map Height', 'wp-jobsearch'),
            'desc' => esc_html__('Jobs top map height.', 'wp-jobsearch'),
        ),
        'job_top_map_zoom' => array(
            'std' => '8',
            'type' => 'text',
            'label' => esc_html__('Map Zoom', 'wp-jobsearch'),
            'desc' => esc_html__('Jobs top map zoom.', 'wp-jobsearch'),
        ),
        'job_feat_jobs_top' => array(
            'type' => 'select',
            'label' => esc_html__('Featured Jobs on Top', 'wp-jobsearch'),
            'desc' => esc_html__('Featured jobs will display on top of listing.', 'wp-jobsearch'),
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'num_of_feat_jobs' => array(
            'std' => '5',
            'type' => 'text',
            'label' => esc_html__('Number of Featured jobs', 'wp-jobsearch'),
            'desc' => '',
        ),
        'job_top_search' => array(
            'type' => 'select',
            'label' => esc_html__('Top Search Bar', 'wp-jobsearch'),
            'desc' => esc_html__('Results top search bar section switch.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'job_top_search_view' => array(
            'type' => 'select',
            'label' => esc_html__('Top Search Style', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'simple' => esc_html__('Simple', 'wp-jobsearch'),
                'advance' => esc_html__('Advance Search', 'wp-jobsearch'),
            )
        ),
        'top_search_title' => array(
            'type' => 'select',
            'label' => esc_html__("Job Title, Keywords, or Phrase", "wp-jobsearch"),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            ),
            'desc' => esc_html__("Enable/Disable search keyword field in Top Search.", "wp-jobsearch"),
        ),
        'top_search_location' => array(
            'type' => 'select',
            'label' => esc_html__("Top Search Location", "wp-jobsearch"),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            ),
            'desc' => esc_html__("Enable/Disable location field in Top Search.", "wp-jobsearch"),
        ),
        'top_search_sector' => array(
            'type' => 'select',
            'label' => esc_html__("Top Search Sector", "wp-jobsearch"),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            ),
            'desc' => esc_html__("Enable/Disable Sector Dropdown field in Top Search.", "wp-jobsearch"),
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
        'job_elem_custom_fields' => array(
            'type' => 'multi_checkbox',
            'label' => esc_html__('Select Custom Fields', 'wp-jobsearch'),
            'desc' => '',
            'options' => $job_cus_field_arr
        ),
        'job_sort_by' => array(
            'type' => 'select',
            'label' => esc_html__('Sort by Fields', 'wp-jobsearch'),
            'desc' => esc_html__('Results search sorting section switch.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'job_rss_feed' => array(
            'type' => 'select',
            'label' => esc_html__('RSS Feed', 'wp-jobsearch'),
            'desc' => esc_html__('RSS Feed section switch.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'jobs_emp_base' => array(
            'type' => 'select',
            'label' => esc_html__('Employer Base Jobs', 'wp-jobsearch'),
            'desc' => esc_html__('Show only Selected Employer Jobs.', 'wp-jobsearch'),
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'jobs_emp_base_id' => array(
            'std' => '',
            'type' => 'text',
            'label' => esc_html__('Employer ID', 'wp-jobsearch'),
            'desc' => esc_html__('Put employer ID here.', 'wp-jobsearch'),
        ),
        'job_excerpt' => array(
            'std' => '20',
            'type' => 'text',
            'label' => esc_html__('Excerpt Length', 'wp-jobsearch'),
            'desc' => esc_html__('Set the number of words you want to show for excerpt.', 'wp-jobsearch'),
        ),
        'job_order' => array(
            'type' => 'select',
            'label' => esc_html__('Order', 'wp-jobsearch'),
            'desc' => esc_html__('Choose job list items order.', 'wp-jobsearch'),
            'options' => array(
                'DESC' => esc_html__('Descending', 'wp-jobsearch'),
                'ASC' => esc_html__('Ascending', 'wp-jobsearch'),
            )
        ),
        'job_orderby' => array(
            'type' => 'select',
            'label' => esc_html__('Orderby', 'wp-jobsearch'),
            'desc' => esc_html__('Choose job list items orderby.', 'wp-jobsearch'),
            'options' => array(
                'ID' => esc_html__('Date', 'wp-jobsearch'),
                'title' => esc_html__('Title', 'wp-jobsearch'),
            )
        ),
        'job_pagination' => array(
            'type' => 'select',
            'label' => esc_html__('Pagination', 'wp-jobsearch'),
            'desc' => esc_html__('Choose yes if you want to show pagination for job items.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'job_per_page' => array(
            'std' => '10',
            'type' => 'text',
            'label' => esc_html__('Records per Page', 'wp-jobsearch'),
            'desc' => esc_html__('Set number that how much jobs you want to show per page. Leave it blank for all jobs on a single page.', 'wp-jobsearch'),
        ),
    ))
);
