<?php
if (!defined('ABSPATH')) {
    die;
}

global $jobsearch_plugin_options, $empall_applicants_handle, $empemail_applicants_handle, $empexternal_applicants_handle;

$email_applicants = isset($jobsearch_plugin_options['emp_dash_email_applics']) ? $jobsearch_plugin_options['emp_dash_email_applics'] : '';
$external_applicants = isset($jobsearch_plugin_options['emp_dash_external_applics']) ? $jobsearch_plugin_options['emp_dash_external_applics'] : '';

if (isset($_GET['view']) && $_GET['view'] == 'email-applicants' && $email_applicants == 'on') {
    $empemail_applicants_handle->applicants_list();
} else if (isset($_GET['view']) && $_GET['view'] == 'external-applicants' && $external_applicants == 'on') {
    $empexternal_applicants_handle->applicants_list();
} else {
    $empall_applicants_handle->all_applicants_list();
}
