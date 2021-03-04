<?php

$jobsearch_builder_shortcodes['jobsearch_user_job'] = array(
    'title' => esc_html__('Post new Job', 'wp-jobsearch'),
    'id' => 'jobsearch-post-new-job-shortcode',
    'template' => '[jobsearch_user_job {{attributes}}] {{content}} [/jobsearch_user_job]',
    'params' => array(
        'title' => array(
            'std' => '',
            'type' => 'text',
            'label' => esc_html__('Title', 'wp-jobsearch'),
            'desc' => '',
        ),
    )
);
