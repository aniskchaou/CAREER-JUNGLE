<?php
/*
  Class : JobFilterHTML
 */
// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}
// main plugin class
class Jobsearch_EmployersTopFilterHTML {

    // hook things up
    public function __construct() {
        add_filter('jobsearch_employer_top_filter_date_posted_box_html', array($this, 'jobsearch_employer_filter_date_posted_box_html_callback'), 1, 3);
    }

    static function jobsearch_employer_filter_date_posted_box_html_callback($html, $global_rand_id, $sh_atts) {
        $posted = isset($_REQUEST['posted']) ? $_REQUEST['posted'] : '';
        $posted = jobsearch_esc_html($posted);
        $rand = rand(234, 34234);
        $default_date_time_formate = 'd-m-Y H:i:s';
        $current_timestamp = current_time('timestamp');

        $posted_date_filter = isset($sh_atts['job_filters_date']) ? $sh_atts['job_filters_date'] : '';

        $date_filter_collapse = isset($sh_atts['job_filters_date_collapse']) ? $sh_atts['job_filters_date_collapse'] : '';
        ob_start();
        ?>
        <li>
            <div class="jobsearch-select-style">
                <select name="posted" class="selectize-select" placeholder="<?php esc_html_e('Date Posted', 'wp-jobsearch'); ?>">
                    <option value=""><?php esc_html_e('Date Posted', 'wp-jobsearch'); ?></option>
                    <option value="lasthour" <?php echo ($posted == 'lasthour' ? 'selected="selected"' : '') ?>><?php esc_html_e('Last Hour', 'wp-jobsearch') ?></option>
                    <option value="last24" <?php echo ($posted == 'last24' ? 'selected="selected"' : '') ?>><?php esc_html_e('Last 24 hours', 'wp-jobsearch') ?></option>
                    <option value="7days" <?php echo ($posted == '7days' ? 'selected="selected"' : '') ?>><?php esc_html_e('Last 7 days', 'wp-jobsearch') ?></option>
                    <option value="14days" <?php echo ($posted == '14days' ? 'selected="selected"' : '') ?>><?php esc_html_e('Last 14 days', 'wp-jobsearch') ?></option>
                    <option value="30days" <?php echo ($posted == '30days' ? 'selected="selected"' : '') ?>><?php esc_html_e('Last 30 days', 'wp-jobsearch') ?></option>
                    <option value="all" <?php echo ($posted == 'all' ? 'selected="selected"' : '') ?>><?php esc_html_e('All', 'wp-jobsearch') ?></option>
                </select>
            </div>
        </li>
        <?php
        $html .= ob_get_clean();
        if ($posted_date_filter == 'no') {
            $html = '';
        }
        return $html;
    }
}

// class $Jobsearch_EmployersTopFilterHTML 
$Jobsearch_EmployersTopFilterHTML = new Jobsearch_EmployersTopFilterHTML();
global $Jobsearch_EmployersTopFilterHTML;

