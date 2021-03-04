<?php
/**
 * JobSearch Job Admin Widget Class
 *
 * @package Job Admin Widget
 */
if (!class_exists('JobSearch_Job_Widget')) {

    /**
      JobSearch  Image Ads class used to implement the Custom flicker gallery widget.
     */
    class JobSearch_Job_Widget {

        /**
         * Sets up a new jobsearch job widget instance.
         */
        public function __construct() {
            add_action('wp_dashboard_setup', array($this, 'jobsearch_register_job_dashboard_widget'));
        }

        public function jobsearch_register_job_dashboard_widget() {
            wp_add_dashboard_widget(
                    'jobsearch_job_dashboard_widget', esc_html__('Job Statistics', 'wp-jobsearch'), array($this, 'jobsearch_job_dashboard_widget_display')
            );
        }

        public function jobsearch_job_dashboard_widget_display() {
            // total number of jobs
            
            $rand_num = rand(1000000, 9999999);
            ?>
            <script>
                var count_request_<?php absint($rand_num) ?> = jQuery.ajax({
                    url: ajaxurl,
                    method: "POST",
                    data: {
                        counting: 'jobs_dash',
                        rand_id: '<?php absint($rand_num) ?>',
                        action: 'jobsearch_countin_jobs_dash_stats',
                    },
                    dataType: "json"
                });
                //
                count_request_<?php absint($rand_num) ?>.done(function (response) {
                    if (response.tot_counts !== 'undefined') {
                        jQuery('#tot-jobcounts-<?php absint($rand_num) ?>').find('a em').remove();
                        jQuery('#tot-jobcounts-<?php absint($rand_num) ?>').find('a strong').append(response.tot_counts);
                        jQuery('#active-jobcounts-<?php absint($rand_num) ?>').find('a em').remove();
                        jQuery('#active-jobcounts-<?php absint($rand_num) ?>').find('a strong').append(response.active_counts);
                        jQuery('#pending-jobcounts-<?php absint($rand_num) ?>').find('a em').remove();
                        jQuery('#pending-jobcounts-<?php absint($rand_num) ?>').find('a strong').append(response.pending_counts);
                        jQuery('#expire-jobcounts-<?php absint($rand_num) ?>').find('strong em').remove();
                        jQuery('#expire-jobcounts-<?php absint($rand_num) ?>').find('strong').append(response.expire_counts);
                    }
                });
            </script>
            <ul class="jobsearch-job-admin-widget">	
                <li id="tot-jobcounts-<?php absint($rand_num) ?>" class="tot-jobs">
                    <a href="<?php echo admin_url('/edit.php?post_type=job') ?>"><strong><i class="fa fa-globe fa-lg"></i> <em class="fa fa-refresh fa-spin"></em></strong> <?php echo esc_html__('Jobs', 'wp-jobsearch') ?></a>				
                </li>
                <li id="active-jobcounts-<?php absint($rand_num) ?>" class="tot-active-jobs">
                    <a href="<?php echo admin_url('/edit.php?post_type=job&job_status=approved') ?>"><strong><i class="fa fa-check-circle-o fa-lg"></i> <em class="fa fa-refresh fa-spin"></em></strong> <?php echo esc_html__('Active Jobs', 'wp-jobsearch') ?></a>
                </li>
                <li id="pending-jobcounts-<?php absint($rand_num) ?>" class="tot-pending-jobs">
                    <a href="<?php echo admin_url('/edit.php?post_type=job&job_status=pending') ?>"><strong><i class="fa fa-clock-o fa-lg"></i> <em class="fa fa-refresh fa-spin"></em></strong> <?php echo esc_html__('Pending Jobs', 'wp-jobsearch') ?></a>
                </li>
                <li id="expire-jobcounts-<?php absint($rand_num) ?>" class="tot-expiry-jobs">
                    <strong><i class="fa fa-calendar-times-o fa-lg"></i> <em class="fa fa-refresh fa-spin"></em></strong> <?php echo esc_html__('Expired Jobs', 'wp-jobsearch') ?>    
                </li> 
            </ul>
            <?php
        }

    }

    // class Jobsearch_CustomField 
    $JobSearch_Job_Widget_obj = new JobSearch_Job_Widget();
    global $JobSearch_Job_Widget_obj;
} 

