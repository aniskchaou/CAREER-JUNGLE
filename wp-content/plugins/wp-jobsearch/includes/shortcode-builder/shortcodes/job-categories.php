<?php

$all_page = array('' => esc_html__("Select Page", "wp-jobsearch"));

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

$jobsearch_builder_shortcodes['jobsearch_job_categories'] = array(
    'title' => esc_html__('Job Categories', 'wp-jobsearch'),
    'id' => 'jobsearch-job-categories',
    'template' => '[jobsearch_job_categories {{attributes}}] {{content}} [/jobsearch_job_categories]',
    'params' => array(
        'num_cats' => array(
            'std' => '',
            'type' => 'text',
            'label' => esc_html__('Number of Categories', 'wp-jobsearch'),
            'desc' => '',
        ),
        'result_page' => array(
            'type' => 'select',
            'label' => esc_html__('Result Page', 'wp-jobsearch'),
            'desc' => '',
            'options' => $all_page
        ),
        'order_by' => array(
            'type' => 'select',
            'label' => esc_html__('Sort order', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'jobs_count' => esc_html__("By Jobs Count", "wp-jobsearch"),
                'id' => esc_html__("By Random", "wp-jobsearch"),
            ),
        ),
        'view_more_btn' => array(
            'type' => 'select',
            'label' => esc_html__('View More Button', 'wp-jobsearch'),
            'desc' => '',
            'options' => array(
                'yes' => esc_html__("Yes", "wp-jobsearch"),
                'no' => esc_html__("No", "wp-jobsearch"),
            ),
        ),
        'cat_link_text' => array(
            'std' => 'View All',
            'type' => 'text',
            'label' => esc_html__('Link Button Text', 'wp-jobsearch'),
            'desc' => '',
        ),
        'cat_link_text_url' => array(
            'std' => '',
            'type' => 'text',
            'label' => esc_html__('Link Button URL', 'wp-jobsearch'),
            'desc' => '',
        ),
    )
);
