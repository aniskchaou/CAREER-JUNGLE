<?php

$jobsearch_builder_shortcodes['jobsearch_login_registration'] = array(
    'title' => esc_html__('Login Registration Form', 'wp-jobsearch'),
    'id' => 'jobsearch-login-registration-shortcode',
    'template' => '[jobsearch_login_registration {{attributes}}] {{content}} [/jobsearch_login_registration]',
    'params' => array(
        'login_registration_title' => array(
            'std' => '',
            'type' => 'text',
            'label' => esc_html__('Title', 'wp-jobsearch'),
            'desc' => '',
        ),
        'login_register_form' => array(
            'std' => '',
            'type' => 'select',
            'label' => esc_html__('Enable Register', 'wp-jobsearch'),
            'options' => array(
                'on' => esc_html__("Yes", "wp-jobsearch"),
                'off' => esc_html__("No", "wp-jobsearch"),
            ),
            'desc' => '',
        ),
        'logreg_form_type' => array(
            'std' => '',
            'type' => 'select',
            'label' => esc_html__('Form Type', 'wp-jobsearch'),
            'options' => array(
                'both' => esc_html__("Both Forms", "wp-jobsearch"),
                'reg_only' => esc_html__("Register Form Only", "wp-jobsearch"),
                'login_only' => esc_html__("Login Form Only", "wp-jobsearch"),
            ),
            'desc' => '',
        ),
        'login_candidate_register' => array(
            'std' => '',
            'type' => 'select',
            'label' => esc_html__('Enable Candidate Registration', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__("Yes", "wp-jobsearch"),
                'no' => esc_html__("No", "wp-jobsearch"),
            ),
            'desc' => '',
        ),
        'login_employer_register' => array(
            'std' => '',
            'type' => 'select',
            'label' => esc_html__('Enable Employer Registration', 'wp-jobsearch'),
            'options' => array(
                'yes' => esc_html__("Yes", "wp-jobsearch"),
                'no' => esc_html__("No", "wp-jobsearch"),
            ),
            'desc' => '',
        ),
    )
);
