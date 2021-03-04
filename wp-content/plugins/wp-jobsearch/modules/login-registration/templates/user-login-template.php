<?php
if (wp_is_mobile()) {
    get_header('mobile');
} else {
    get_header();
}

global $jobsearch_plugin_options;
$plugin_default_view = isset($jobsearch_plugin_options['jobsearch-default-page-view']) ? $jobsearch_plugin_options['jobsearch-default-page-view'] : 'full';
$plugin_default_view_with_str = '';
if ($plugin_default_view == 'boxed') {

    $plugin_default_view_with_str = isset($jobsearch_plugin_options['jobsearch-boxed-view-width']) && $jobsearch_plugin_options['jobsearch-boxed-view-width'] != '' ? $jobsearch_plugin_options['jobsearch-boxed-view-width'] : '1140px';
    if ($plugin_default_view_with_str != '') {
        $plugin_default_view_with_str = ' style="width:' . $plugin_default_view_with_str . '"';
    }
}

//
$op_register_form_allow = isset($jobsearch_plugin_options['login_register_form']) ? $jobsearch_plugin_options['login_register_form'] : '';
$op_cand_register_allow = isset($jobsearch_plugin_options['login_candidate_register']) ? $jobsearch_plugin_options['login_candidate_register'] : '';
$op_emp_register_allow = isset($jobsearch_plugin_options['login_employer_register']) ? $jobsearch_plugin_options['login_employer_register'] : '';
?>
<div class="jobsearch-plugin-default-container jobsearch-typo-wrap" <?php echo ($plugin_default_view_with_str); ?>>
    <?php
    echo do_shortcode('[jobsearch_login_registration login_registration_title="" login_register_form="' . $op_register_form_allow . '" login_candidate_register="' . $op_cand_register_allow . '" login_employer_register="' . $op_emp_register_allow . '"]');  ?>
</div>
<?php
get_footer();