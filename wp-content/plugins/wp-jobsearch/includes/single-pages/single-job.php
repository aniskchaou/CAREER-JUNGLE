<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 */
global $post, $jobsearch_plugin_options;
$job_id = $post->ID;

$allow_page_access = false;
if (is_user_logged_in() && current_user_can('administrator')) {
    $allow_page_access = true;
}
$job_employer = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    $user_is_employer = jobsearch_user_is_employer($user_id);
    if ($user_is_employer) {
        $employer_id = jobsearch_get_user_employer_id($user_id);
        if ($employer_id == $job_employer) {
            $allow_page_access = true;
        }
    }
}

$job_expiry_date = get_post_meta($job_id, 'jobsearch_field_job_expiry_date', true);
$job_expiry_date = apply_filters('jobsearch_jobdetail_expiry_datetime_ttostr', $job_expiry_date, $job_id);
$job_status = get_post_meta($job_id, 'jobsearch_field_job_status', true);

$restrict_content = false;
if ($job_expiry_date < current_time('timestamp') && !$allow_page_access) {
    $restrict_content = true;
}
if ($job_status != 'approved' && !$allow_page_access) {
    $restrict_content = true;
}

$job_view = isset($jobsearch_plugin_options['jobsearch_job_detail_views']) && !empty($jobsearch_plugin_options['jobsearch_job_detail_views']) ? $jobsearch_plugin_options['jobsearch_job_detail_views'] : 'view1';

$job_views_count = isset($jobsearch_plugin_options['job_detail_views_count']) ? $jobsearch_plugin_options['job_detail_views_count'] : '';
if ($job_views_count == 'on') {
    jobsearch_job_views_count($job_id);
}
if (wp_is_mobile()) {
    get_header('mobile');
} else {
    get_header();
}

wp_enqueue_script('fancybox-pack');

ob_start();
if ($restrict_content) {
    $notify_err_image = isset($jobsearch_plugin_options['expire_job_image']['url']) ? $jobsearch_plugin_options['expire_job_image']['url'] : '';
    $notify_err_hding = isset($jobsearch_plugin_options['expire_job_heading']) ? $jobsearch_plugin_options['expire_job_heading'] : '';
    $notify_err_desctxt = isset($jobsearch_plugin_options['expire_job_notify_desc']) ? $jobsearch_plugin_options['expire_job_notify_desc'] : '';
    $notify_err_footr_logo = isset($jobsearch_plugin_options['expire_job_footer_logo']['url']) ? $jobsearch_plugin_options['expire_job_footer_logo']['url'] : '';
    ?>
    <div class="jobsearch-main-content">
        <div class="jobsearch-main-section">
            <div class="jobsearch-plugin-default-container">
                <div class="jobsearch-row">
                    <div class="jobsearch-column-12">
                        <div class="jobsearch-jobexpire-notifyerr">
                            <div class="jobsearch-jobexpire-notifyerr-inner">
                                <div class="jobexpire-notify-hdrlogo"><img src="<?php echo($notify_err_image) ?>"
                                                                           alt="404"></div>
                                <div class="jobexpire-notify-msgcon">
                                    <h2><?php echo($notify_err_hding) ?></h2>
                                    <p><?php echo($notify_err_desctxt) ?></p>
                                    <div class="backto-home-btncon"><a
                                                href="<?php echo home_url('/') ?>"><?php esc_html_e('Back to Home', 'wp-jobsearch') ?></a>
                                    </div>
                                </div>
                                <?php
                                if ($notify_err_footr_logo != '') { ?>
                                    <div class="jobexpire-notify-footrlogo">
                                        <span><?php esc_html_e('Powered by', 'wp-jobsearch') ?></span>
                                        <img src="<?php echo($notify_err_footr_logo) ?>"
                                             alt="<?php bloginfo('name') ?>">
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
} else {
    $job_view = apply_filters('careerfy_job_detail_page_style_display', $job_view, $job_id);

    jobsearch_get_template_part($job_view, 'job', 'detail-pages/job');

    do_action('jobsearch_job_detail_before_footer', $job_id);
}
$html = ob_get_clean();
echo apply_filters('jobsearch_single_job_allover_view_html', $html, $job_id);

get_footer();