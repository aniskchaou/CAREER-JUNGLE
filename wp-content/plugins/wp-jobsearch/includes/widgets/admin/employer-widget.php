<?php
/**
 * JobSearch Employer Admin Widget Class
 *
 * @package Employer Admin Widget
 */
if (!class_exists('JobSearch_Employer_Widget')) {

    /**
      JobSearch  Image Ads class used to implement the Custom flicker gallery widget.
     */
    class JobSearch_Employer_Widget {

        /**
         * Sets up a new jobsearch employer widget instance.
         */
        public function __construct() {
            add_action('wp_dashboard_setup', array($this, 'jobsearch_register_employer_dashboard_widget'));
        }

        public function jobsearch_register_employer_dashboard_widget() {
            wp_add_dashboard_widget(
                    'jobsearch_employer_dashboard_widget', esc_html__('Employer Statistics', 'wp-jobsearch'), array($this, 'jobsearch_employer_dashboard_widget_display')
            );
        }

        public function jobsearch_employer_dashboard_widget_display() {
            // total number of employer
            
            $rand_num = rand(1000000, 9999999);
            ?>
            <script>
                var count_request_<?php absint($rand_num) ?> = jQuery.ajax({
                    url: ajaxurl,
                    method: "POST",
                    data: {
                        counting: 'employers_dash',
                        rand_id: '<?php absint($rand_num) ?>',
                        action: 'jobsearch_countin_employers_dash_stats',
                    },
                    dataType: "json"
                });
                //
                count_request_<?php absint($rand_num) ?>.done(function (response) {
                    if (response.tot_counts !== 'undefined') {
                        jQuery('#tot-empcounts-<?php absint($rand_num) ?>').find('a em').remove();
                        jQuery('#tot-empcounts-<?php absint($rand_num) ?>').find('a strong').append(response.tot_counts);
                        jQuery('#active-empcounts-<?php absint($rand_num) ?>').find('a em').remove();
                        jQuery('#active-empcounts-<?php absint($rand_num) ?>').find('a strong').append(response.active_counts);
                        jQuery('#pending-empcounts-<?php absint($rand_num) ?>').find('a em').remove();
                        jQuery('#pending-empcounts-<?php absint($rand_num) ?>').find('a strong').append(response.pending_counts);
                    }
                });
            </script>
            <ul class="jobsearch-employers-admin-widget jobsearch-job-admin-widget">	
                <li id="tot-empcounts-<?php absint($rand_num) ?>" class="tot-employers">
                    <a href="<?php echo admin_url('/edit.php?post_type=employer') ?>"><strong><i class="fa fa-globe fa-lg"></i> <em class="fa fa-refresh fa-spin"></em></strong> <?php echo esc_html__('Employers', 'wp-jobsearch') ?></a>
                </li>
                <li id="active-empcounts-<?php absint($rand_num) ?>" class="tot-active-jobs">
                    <a href="<?php echo admin_url('/edit.php?post_type=employer&employer_status=approved') ?>"><strong><i class="fa fa-check-circle-o fa-lg"></i> <em class="fa fa-refresh fa-spin"></em></strong> <?php echo esc_html__('Active Employers', 'wp-jobsearch') ?></a>
                </li>
                <li id="pending-empcounts-<?php absint($rand_num) ?>" class="tot-pending-jobs">
                    <a href="<?php echo admin_url('/edit.php?post_type=employer&employer_status=pending') ?>"><strong><i class="fa fa-clock-o fa-lg"></i> <em class="fa fa-refresh fa-spin"></em></strong> <?php echo esc_html__('Pending Employers', 'wp-jobsearch') ?></a>
                </li>
            </ul>
            <?php
        }

    }

    // class Employersearch_CustomField 
    $JobSearch_Employer_Widget_obj = new JobSearch_Employer_Widget();
    global $JobSearch_Employer_Widget_obj;
} 

