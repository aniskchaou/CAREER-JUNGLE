<?php
/*
  Class : CareerBuilder jobs Front view
 */


// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class JobSearch_CareerBuilder_Jobs_Front {

    // hook things up
    public function __construct() {

        //jobsearch_job_detail_content_info
        //add_filter('jobsearch_job_detail_content_info', array($this, 'job_detail_content_info'), 10, 2);

        //jobsearch_job_detail_content_fields
        //add_filter('jobsearch_job_detail_content_fields', array($this, 'job_detail_content_none'), 10, 2);

        //jobsearch_job_detail_content_detail
        //add_filter('jobsearch_job_detail_content_detail', array($this, 'job_detail_content_detail'), 10, 2);

        //jobsearch_job_detail_content_skills
        //add_filter('jobsearch_job_detail_content_skills', array($this, 'job_detail_content_none'), 10, 2);

        //jobsearch_job_detail_content_related
        //jobsearch_job_detail_sidebar_apply_btns
        //add_filter('jobsearch_job_detail_sidebar_apply_btns', array($this, 'job_detail_content_none'), 10, 2);

        //jobsearch_job_detail_sidebar_related_jobs
    }

    public function job_detail_content_info($content, $post_id) {

        $job_referral = get_post_meta($post_id, 'jobsearch_job_referral', true);
        if ($job_referral == 'careerbuilder') {

            ob_start();

            $jobsearch_job_posted = get_post_meta($post_id, 'jobsearch_field_job_publish_date', true);
            $jobsearch_job_posted_ago = jobsearch_time_elapsed_string($jobsearch_job_posted);
            $jobsearch_job_posted_formated = date_i18n(get_option('date_format'), ($jobsearch_job_posted));


            $job_views_count = get_post_meta($post_id, 'jobsearch_job_views_count', true);

            $job_type_str = jobsearch_job_get_all_jobtypes($post_id, 'jobsearch-jobdetail-type', '', '', '<small>', '</small>');
            $job_company_name = get_post_meta($post_id, 'jobsearch_field_company_name', true);
            
            $get_job_location = get_post_meta($post_id, 'jobsearch_field_location_address', true);
            ?>
            <span>
                <?php
                if ($job_type_str != '') {
                    echo force_balance_tags($job_type_str);
                }
                if ($job_company_name != '') {
                    echo '<a>' . ($job_company_name) . '</a>';
                }
                ?>
                <small class="jobsearch-jobdetail-postinfo"><?php echo jobsearch_esc_html($jobsearch_job_posted_ago); ?></small>
            </span>
            <ul class="jobsearch-jobdetail-options">
                <?php
                if (!empty($get_job_location)) {
                    $google_mapurl = 'https://www.google.com/maps/search/' . $get_job_location;
                    ?>
                    <li><i class="fa fa-map-marker"></i> <?php echo jobsearch_esc_html($get_job_location); ?> <a href="<?php echo esc_url($google_mapurl); ?>" target="_blank" class="jobsearch-jobdetail-view"><?php echo jobsearch_esc_html__('View on Map', 'wp-jobsearch') ?></a></li>
                    <?php
                }
                ?> 
                <li><i class="jobsearch-icon jobsearch-calendar"></i> <?php echo jobsearch_esc_html__('Post Date', 'wp-jobsearch') ?>: <?php echo jobsearch_esc_html($jobsearch_job_posted_formated); ?></li>
                <li><a><i class="jobsearch-icon jobsearch-view"></i> <?php echo jobsearch_esc_html__('View(s)', 'wp-jobsearch') ?> <?php echo absint($job_views_count); ?></a></li>
            </ul>
            <?php
            // wrap in this due to enquire arrange button style.
            $before_label = esc_html__('Shortlist', 'wp-jobsearch');
            $after_label = esc_html__('Shortlisted', 'wp-jobsearch');
            $figcaption_div = true;
            $book_mark_args = array(
                'before_label' => $before_label,
                'after_label' => $after_label,
                'before_icon' => '<i class="fa fa-heart-o"></i>',
                'after_icon' => '<i class="fa fa-heart"></i>',
            );
            do_action('jobsearch_shortlist_frontend_button', $post_id, $book_mark_args, $figcaption_div);

            //
            $popup_args = array(
                'job_id' => $post_id,
            );
            do_action('jobsearch_job_send_to_email_filter', $popup_args);

            //
            wp_enqueue_script('jobsearch-addthis');
            ?>
            <ul class="jobsearch-jobdetail-media">
                <li><span><?php esc_html_e('Share:', 'wp-jobsearch') ?></span></li>
                <li><a href="javascript:void(0);" data-original-title="facebook" class="jobsearch-icon jobsearch-facebook-logo-in-circular-button-outlined-social-symbol addthis_button_facebook"></a></li>
                <li><a href="javascript:void(0);" data-original-title="twitter" class="jobsearch-icon jobsearch-twitter-circular-button addthis_button_twitter"></a></li>
                <li><a href="javascript:void(0);" data-original-title="linkedin" class="jobsearch-icon jobsearch-linkedin addthis_button_linkedin"></a></li>
                <li><a href="javascript:void(0);" data-original-title="share_more" class="jobsearch-icon jobsearch-plus addthis_button_compact"></a></li>
            </ul>
            <?php
            $content = ob_get_clean();
        }
        return $content;
    }

    public function job_detail_content_detail($content, $post_id) {

        global $jobsearch_plugin_options;
        
        $job_referral = get_post_meta($post_id, 'jobsearch_job_referral', true);
        if ($job_referral == 'careerbuilder') {
            $without_login_signin_restriction = isset($jobsearch_plugin_options['without-login-apply-restriction']) ? $jobsearch_plugin_options['without-login-apply-restriction'] : '';

            $apply_without_login = isset($jobsearch_plugin_options['job-apply-without-login']) ? $jobsearch_plugin_options['job-apply-without-login'] : '';

            $external_signin_switch = false;
            if (isset($without_login_signin_restriction) && is_array($without_login_signin_restriction) && sizeof($without_login_signin_restriction) > 0) {
                foreach ($without_login_signin_restriction as $restrict_signin_switch) {
                    if ($restrict_signin_switch == 'external') {
                        $external_signin_switch = true;
                    }
                }
            }

            $login_class = '';
            $job_detail_url = get_post_meta($post_id, 'jobsearch_field_job_detail_url', true);
            if ($apply_without_login == 'off' && $external_signin_switch && !is_user_logged_in()) {
                $job_detail_url = 'javascript:void(0);';
                $login_class = ' jobsearch-open-signin-tab';
            }
            $content .= '<div class="view-more-link"><a href="' . $job_detail_url . '" class="view-more-btn' . $login_class . '">' . esc_html__('view more', 'wp-jobsearch') . '</a></div>';
        }
        return $content;
    }

    public function job_detail_content_none($content, $post_id) {

        $job_referral = get_post_meta($post_id, 'jobsearch_job_referral', true);
        if ($job_referral == 'careerbuilder') {
            $content = '';
        }
        return $content;
    }

}

// Class JobSearch_CareerBuilder_Jobs_Front
$JobSearch_CareerBuilder_Jobs_Front_obj = new JobSearch_CareerBuilder_Jobs_Front();
global $JobSearch_CareerBuilder_Jobs_Front_obj;
