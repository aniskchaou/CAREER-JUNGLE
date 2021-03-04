<?php

$jobsearch_builder_shortcodes['jobsearch_embeddable_jobs'] = array(
    'title' => esc_html__('Embeddable Jobs', 'wp-jobsearch'),
    'id' => 'jobsearch-embeddable-jobs',
    'template' => '[jobsearch_embeddable_jobs_generator {{attributes}}]',
    'params' => array(
        'site_title' => array(
            'type' => 'select',
            'label' => esc_html__('Site Title', 'wp-jobsearch'),
            'desc' => esc_html__('Site title in embeddable jobs on/off.', 'wp-jobsearch'),
            'options' => array(
                'on' => esc_html__('On', 'wp-jobsearch'),
                'off' => esc_html__('Off', 'wp-jobsearch'),
            )
        ),
        'employer_base_jobs' => array(
            'type' => 'select',
            'label' => esc_html__('Employer base jobs', 'wp-jobsearch'),
            'desc' => esc_html__('Allow user to search employer base jobs.', 'wp-jobsearch'),
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
        'keyword_search' => array(
            'type' => 'select',
            'label' => esc_html__('Keyword search', 'wp-jobsearch'),
            'desc' => esc_html__('Allow user to search keyword base jobs.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'location_search' => array(
            'type' => 'select',
            'label' => esc_html__('Location search', 'wp-jobsearch'),
            'desc' => esc_html__('Allow user to search loaction base jobs.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'job_sector' => array(
            'type' => 'select',
            'label' => esc_html__('Sector search', 'wp-jobsearch'),
            'desc' => esc_html__('Allow user to search sector base jobs.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'job_type' => array(
            'type' => 'select',
            'label' => esc_html__('Job Type search', 'wp-jobsearch'),
            'desc' => esc_html__('Allow user to search job type base jobs.', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
                'no' => esc_html__('No', 'wp-jobsearch'),
            )
        ),
        'custom_fields' => array(
            'type' => 'select',
            'label' => esc_html__('Custom Fields', 'wp-jobsearch'),
            'desc' => esc_html__('Allow Custom Fields to search jobs.', 'wp-jobsearch'),
            'options' => array(
                'no' => esc_html__('No', 'wp-jobsearch'),
                'yes' => esc_html__('Yes', 'wp-jobsearch'),
            )
        ),
    )
);
