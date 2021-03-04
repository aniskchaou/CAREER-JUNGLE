<?php
/**
 * JobSearch Candidate Admin Widget Class
 *
 * @package Candidate Admin Widget
 */
if (!class_exists('JobSearch_Candidate_Widget')) {

    /**
      JobSearch  Image Ads class used to implement the Custom flicker gallery widget.
     */
    class JobSearch_Candidate_Widget {

        /**
         * Sets up a new jobsearch candidate widget instance.
         */
        public function __construct() {
            add_action('wp_dashboard_setup', array($this, 'jobsearch_register_candidate_dashboard_widget'));
        }

        public function jobsearch_register_candidate_dashboard_widget() {
            wp_add_dashboard_widget(
                    'jobsearch_candidate_dashboard_widget', esc_html__('Candidate Statistics', 'wp-jobsearch'), array($this, 'jobsearch_candidate_dashboard_widget_display')
            );
        }

        public function jobsearch_candidate_dashboard_widget_display() {
            // total number of candidate
            $rand_num = rand(1000000, 9999999);
            ?>
            <script>
                var count_request_<?php absint($rand_num) ?> = jQuery.ajax({
                    url: ajaxurl,
                    method: "POST",
                    data: {
                        counting: 'candidates_dash',
                        rand_id: '<?php absint($rand_num) ?>',
                        action: 'jobsearch_countin_candidates_dash_stats',
                    },
                    dataType: "json"
                });
                //
                count_request_<?php absint($rand_num) ?>.done(function (response) {
                    if (response.tot_counts !== 'undefined') {
                        jQuery('#tot-candcounts-<?php absint($rand_num) ?>').find('a em').remove();
                        jQuery('#tot-candcounts-<?php absint($rand_num) ?>').find('a strong').append(response.tot_counts);
                        jQuery('#active-candcounts-<?php absint($rand_num) ?>').find('a em').remove();
                        jQuery('#active-candcounts-<?php absint($rand_num) ?>').find('a strong').append(response.active_counts);
                        jQuery('#pending-candcounts-<?php absint($rand_num) ?>').find('a em').remove();
                        jQuery('#pending-candcounts-<?php absint($rand_num) ?>').find('a strong').append(response.pending_counts);
                    }
                });
            </script>
            <ul class="jobsearch-candidates-admin-widget jobsearch-job-admin-widget">	
                <li id="tot-candcounts-<?php absint($rand_num) ?>" class="tot-jobs">
                    <a href="<?php echo admin_url('/edit.php?post_type=candidate') ?>"><strong><i class="fa fa-globe fa-lg"></i> <em class="fa fa-refresh fa-spin"></em></strong> <?php echo esc_html__('Candidates', 'wp-jobsearch') ?></a>
                </li> 
                <li id="active-candcounts-<?php absint($rand_num) ?>" class="tot-active-jobs">
                    <a href="<?php echo admin_url('/edit.php?post_type=candidate&candidate_status=approved') ?>"><strong><i class="fa fa-check-circle-o fa-lg"></i> <em class="fa fa-refresh fa-spin"></em></strong> <?php echo esc_html__('Active Candidates', 'wp-jobsearch') ?></a>
                </li>
                <li id="pending-candcounts-<?php absint($rand_num) ?>" class="tot-pending-jobs">
                    <a href="<?php echo admin_url('/edit.php?post_type=candidate&candidate_status=pending') ?>"><strong><i class="fa fa-clock-o fa-lg"></i> <em class="fa fa-refresh fa-spin"></em></strong> <?php echo esc_html__('Pending Candidates', 'wp-jobsearch') ?></a>
                </li>
            </ul>
            <?php
        }

    }
    // class Candidatesearch_CustomField 
    $JobSearch_Candidate_Widget_obj = new JobSearch_Candidate_Widget();
    global $JobSearch_Candidate_Widget_obj;
}