<?php
/*
  Class : Job Alerts Hooks
 */

use WP_Jobsearch\Package_Limits;

// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class JobSearch_Job_Alerts_Hooks
{

// hook things up
    public function __construct()
    {

        add_filter('redux/options/jobsearch_plugin_options/sections', array($this, 'plugin_option_fields'));
        //
        add_action('jobsearch_jobs_listing_filters_before', array($this, 'frontend_before_filters_ui_callback'), 10, 1);
        //
        add_action('jobsearch_jobs_listing_before', array($this, 'frontend_before_listings_ui_callback'), 10, 1);
        add_action('jobsearch_jobs_listing_after', array($this, 'frontend_after_listings_ui_callback'), 10, 1);
        //
        add_action('jobsearch_jobs_listing_quick_detail_before', array($this, 'frontend_before_listings_quick_detail_ui_callback'), 10, 1);

        add_action('jobsearch_after_jobs_listing_content', array($this, 'after_jobs_listing_callback'), 10, 2);
        //
        add_action('wp_ajax_jobsearch_create_job_alert', array($this, 'create_job_alert_callback'));
        add_action('wp_ajax_nopriv_jobsearch_create_job_alert', array($this, 'create_job_alert_callback'));

        // job listings vc shortcode params hook
        add_filter('jobsearch_job_listings_vcsh_params', array($this, 'vc_shortcode_params_add'), 10, 1);

        // job listings editor shortcode params hook
        add_filter('jobsearch_job_listings_sheb_params', array($this, 'editor_shortcode_params_add'), 10, 1);

        // job listings editor shortcode top params hook
        add_filter('jobsearch_job_listings_sheb_params', array($this, 'editor_shortcode_top_params_add'), 10, 1);

        // jobsearch menu tab link add hook
        add_filter('jobsearch_dashboard_menu_items_ext', array($this, 'dashboard_menu_items_ext'), 10, 3);

        // jobsearch menu tab in options add hook
        add_filter('jobsearch_cand_dash_menu_in_opts', array($this, 'dashboard_menu_items_inopts_arr'), 10, 1);
        add_filter('jobsearch_cand_dash_menu_in_opts_swch', array($this, 'dashboard_menu_items_inopts_swch_arr'), 10, 1);
        add_filter('jobsearch_cand_menudash_link_job_alerts_item', array($this, 'dashboard_menu_items_in_fmenu'), 10, 5);

        // jobsearch dashboard tab content add hook
        add_filter('jobsearch_dashboard_tab_content_ext', array($this, 'dashboard_tab_content_add'), 10, 2);

        add_action('wp_ajax_jobsearch_unsubscribe_job_alert', array($this, 'unsubscribe_job_alert'));
        add_action('wp_ajax_nopriv_jobsearch_unsubscribe_job_alert', array($this, 'unsubscribe_job_alert'));

        add_action('wp_ajax_jobsearch_user_job_alert_delete', array($this, 'remove_job_alert'));

        add_action('wp_ajax_jobsearch_jobsearch_alert_tags_update', array($this, 'job_alert_criteria_selist'));
        add_action('wp_ajax_nopriv_jobsearch_jobsearch_alert_tags_update', array($this, 'job_alert_criteria_selist'));

        add_action('wp_footer', array($this, 'job_alert_popup'), 20);
    }

    public function plugin_option_fields($sections)
    {

        $sections[] = array(
            'title' => __('Job Alerts Settings', 'wp-jobsearch'),
            'id' => 'job-alerts-settings',
            'desc' => '',
            'icon' => 'el el-bell',
            'fields' => apply_filters('jobsearch_jobalerts_settoptions_fields', array(
                array(
                    'id' => 'job_alerts_switch',
                    'type' => 'button_set',
                    'title' => __('Job Alerts', 'wp-jobsearch'),
                    'subtitle' => __('Switch On/Off Job Alerts.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'save_alerts_withlogin',
                    'type' => 'button_set',
                    'title' => __('Job Alerts for logged in users', 'wp-jobsearch'),
                    'subtitle' => __('Job Alerts for logged in users only.', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'wp-jobsearch'),
                        'off' => __('Off', 'wp-jobsearch'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'job-alerts-frequencies-section',
                    'type' => 'section',
                    'title' => __('Set Alert Frequencies', 'wp-jobsearch'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'job_alerts_frequency_annually',
                    'type' => 'button_set',
                    'title' => __('Annually', 'wp-jobsearch'),
                    'subtitle' => __('Do you want to allow users to set alert frequency to annually?', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('Yes', 'wp-jobsearch'),
                        'off' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'job_alerts_frequency_biannually',
                    'type' => 'button_set',
                    'title' => __('Biannually', 'wp-jobsearch'),
                    'subtitle' => __('Do you want to allow users to set alert frequency to biannually?', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('Yes', 'wp-jobsearch'),
                        'off' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'job_alerts_frequency_monthly',
                    'type' => 'button_set',
                    'title' => __('Monthly', 'wp-jobsearch'),
                    'subtitle' => __('Do you want to allow users to set alert frequency to monthly?', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('Yes', 'wp-jobsearch'),
                        'off' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'job_alerts_frequency_fortnightly',
                    'type' => 'button_set',
                    'title' => __('Fortnightly', 'wp-jobsearch'),
                    'subtitle' => __('Do you want to allow users to set alert frequency to fortnightly?', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('Yes', 'wp-jobsearch'),
                        'off' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'job_alerts_frequency_weekly',
                    'type' => 'button_set',
                    'title' => __('Weekly', 'wp-jobsearch'),
                    'subtitle' => __('Do you want to allow users to set alert frequency to weekly?', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('Yes', 'wp-jobsearch'),
                        'off' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'job_alerts_frequency_daily',
                    'type' => 'button_set',
                    'title' => __('Daily', 'wp-jobsearch'),
                    'subtitle' => __('Do you want to allow users to set alert frequency to daily?', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('Yes', 'wp-jobsearch'),
                        'off' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'job_alerts_frequency_hourly',
                    'type' => 'button_set',
                    'title' => __('Hourly', 'wp-jobsearch'),
                    'subtitle' => __('Do you want to allow users to set alert frequency to hourly?', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('Yes', 'wp-jobsearch'),
                        'off' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'job_alerts_frequency_never',
                    'type' => 'button_set',
                    'title' => __('Never', 'wp-jobsearch'),
                    'subtitle' => __('Do you want to allow users to set alert frequency to never?', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('Yes', 'wp-jobsearch'),
                        'off' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'off',
                ),
                array(
                    'id' => 'job-alerts-frequencies-sectionclose',
                    'type' => 'section',
                    'title' => '',
                    'subtitle' => '',
                    'indent' => false,
                ),
                array(
                    'id' => 'job-alerts-filtesr-section',
                    'type' => 'section',
                    'title' => __('Set Alert Filter', 'wp-jobsearch'),
                    'subtitle' => '',
                    'indent' => true,
                ),
                array(
                    'id' => 'job_alerts_filtr_sectr',
                    'type' => 'button_set',
                    'title' => __('Sector filter', 'wp-jobsearch'),
                    'subtitle' => __('Do you want to show Sector filter in alert popup?', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('Yes', 'wp-jobsearch'),
                        'off' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'job_alerts_filtr_jobtype',
                    'type' => 'button_set',
                    'title' => __('Job Type filter', 'wp-jobsearch'),
                    'subtitle' => __('Do you want to show Job Type filter in alert popup?', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('Yes', 'wp-jobsearch'),
                        'off' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'job_alerts_filtr_location',
                    'type' => 'button_set',
                    'title' => __('Location filter', 'wp-jobsearch'),
                    'subtitle' => __('Do you want to show the Location filter in alert popup?', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('Yes', 'wp-jobsearch'),
                        'off' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'job_alerts_filtr_cusfield',
                    'type' => 'button_set',
                    'title' => __('Custom Fields filter', 'wp-jobsearch'),
                    'subtitle' => __('Do you want to show Custom Fields filter in alert popup?', 'wp-jobsearch'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('Yes', 'wp-jobsearch'),
                        'off' => __('No', 'wp-jobsearch'),
                    ),
                    'default' => 'on',
                ),
            )),
        );
        return $sections;
    }

    public function array_insert($array, $values, $offset)
    {
        return array_slice($array, 0, $offset, true) + $values + array_slice($array, $offset, NULL, true);
    }

    public function vc_shortcode_params_add($params = array())
    {
        global $jobsearch_plugin_options;
        $job_alerts_switch = isset($jobsearch_plugin_options['job_alerts_switch']) ? $jobsearch_plugin_options['job_alerts_switch'] : '';

        if ($job_alerts_switch == 'on') {
            $new_element = array(
                array(
                    'type' => 'dropdown',
                    'heading' => esc_html__("Job Alerts Top", "wp-jobsearch"),
                    'param_name' => 'job_alerts_top',
                    'value' => array(
                        esc_html__("No", "wp-jobsearch") => 'no',
                        esc_html__("Yes", "wp-jobsearch") => 'yes',
                    ),
                    'description' => esc_html__("Show/hide job alerts section at top of listings.", "wp-jobsearch"),
                    'group' => esc_html__("Filters Settings", "wp-jobsearch"),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => esc_html__("Job Alerts Bottom", "wp-jobsearch"),
                    'param_name' => 'job_alerts_bottom',
                    'value' => array(
                        esc_html__("No", "wp-jobsearch") => 'no',
                        esc_html__("Yes", "wp-jobsearch") => 'yes',
                    ),
                    'description' => esc_html__("Show/hide job alerts section at bottom of listings.", "wp-jobsearch"),
                    'group' => esc_html__("Filters Settings", "wp-jobsearch"),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => esc_html__("Job Alerts", "wp-jobsearch"),
                    'param_name' => 'job_alerts',
                    'value' => array(
                        esc_html__("Yes", "wp-jobsearch") => 'yes',
                        esc_html__("No", "wp-jobsearch") => 'no',
                    ),
                    'description' => esc_html__("Show/hide job alerts section in filters of listings.", "wp-jobsearch"),
                    'group' => esc_html__("Filters Settings", "wp-jobsearch"),
                )
            );
            array_splice($params, 4, 0, $new_element);
        }

        return $params;
    }

    public function editor_shortcode_params_add($params = array())
    {
        global $jobsearch_plugin_options;
        $job_alerts_switch = isset($jobsearch_plugin_options['job_alerts_switch']) ? $jobsearch_plugin_options['job_alerts_switch'] : '';

        if ($job_alerts_switch == 'on') {
            $new_element = array(
                'job_alerts' => array(
                    'type' => 'select',
                    'label' => esc_html__('Job Alerts', 'wp-jobsearch'),
                    'desc' => esc_html__('Show/hide job alerts section in filters of listings.', 'wp-jobsearch'),
                    'options' => array(
                        'yes' => esc_html__('Yes', 'wp-jobsearch'),
                        'no' => esc_html__('No', 'wp-jobsearch'),
                    )
                ),
            );
            $params = $this->array_insert($params, $new_element, 3);
        }

        return $params;
    }

    public function editor_shortcode_top_params_add($params = array())
    {
        global $jobsearch_plugin_options;
        $job_alerts_switch = isset($jobsearch_plugin_options['job_alerts_switch']) ? $jobsearch_plugin_options['job_alerts_switch'] : '';

        if ($job_alerts_switch == 'on') {
            $new_element = array(
                'job_alerts_top' => array(
                    'type' => 'select',
                    'label' => esc_html__('Job Alerts Top', 'wp-jobsearch'),
                    'desc' => esc_html__('Show/hide job alerts section at top of listings.', 'wp-jobsearch'),
                    'options' => array(
                        'no' => esc_html__('No', 'wp-jobsearch'),
                        'yes' => esc_html__('Yes', 'wp-jobsearch'),
                    )
                ),
                'job_alerts_bottom' => array(
                    'type' => 'select',
                    'label' => esc_html__('Job Alerts Botttom', 'wp-jobsearch'),
                    'desc' => esc_html__('Show/hide job alerts section at bottom of listings.', 'wp-jobsearch'),
                    'options' => array(
                        'no' => esc_html__('No', 'wp-jobsearch'),
                        'yes' => esc_html__('Yes', 'wp-jobsearch'),
                    )
                ),
            );
            $params = $this->array_insert($params, $new_element, 2);
        }

        return $params;
    }

    public function dashboard_menu_items_ext($html = '', $get_tab = '', $page_url = '')
    {
        global $jobsearch_plugin_options;
        $job_alerts_switch = isset($jobsearch_plugin_options['job_alerts_switch']) ? $jobsearch_plugin_options['job_alerts_switch'] : '';

        if ($job_alerts_switch == 'on') {
            ob_start();

            $user_id = get_current_user_id();
            $is_employer = jobsearch_user_is_employer($user_id);
            if (!$is_employer) {
                ?>
                <li<?php echo($get_tab == 'job-alerts' ? ' class="active"' : '') ?>>
                    <a href="<?php echo add_query_arg(array('tab' => 'job-alerts'), $page_url) ?>">
                        <i class="jobsearch-icon jobsearch-alarm"></i>
                        <?php esc_html_e('Job Alerts', 'wp-jobsearch') ?>
                    </a>
                </li>
                <?php
            }
            $html .= ob_get_clean();
        }

        return $html;
    }

    public function dashboard_menu_items_inopts_arr($opts_arr = array())
    {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $job_alerts_switch = isset($jobsearch__options['job_alerts_switch']) ? $jobsearch__options['job_alerts_switch'] : '';

        if ($job_alerts_switch == 'on') {
            $opts_arr['job_alerts'] = __('Job Alerts', 'wp-jobsearch');
        }

        return $opts_arr;
    }

    public function dashboard_menu_items_inopts_swch_arr($opts_arr = array())
    {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $job_alerts_switch = isset($jobsearch__options['job_alerts_switch']) ? $jobsearch__options['job_alerts_switch'] : '';

        if ($job_alerts_switch == 'on') {
            $opts_arr['job_alerts'] = true;
        }

        return $opts_arr;
    }

    public function dashboard_menu_items_in_fmenu($opts_item = '', $cand_menu_item, $get_tab, $page_url, $candidate_id)
    {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $job_alerts_switch = isset($jobsearch__options['job_alerts_switch']) ? $jobsearch__options['job_alerts_switch'] : '';

        $user_pkg_limits = new Package_Limits;

        if ($job_alerts_switch == 'on') {
            $dashmenu_links_cand = isset($jobsearch__options['cand_dashbord_menu']) ? $jobsearch__options['cand_dashbord_menu'] : '';
            $dashmenu_links_cand = apply_filters('jobsearch_cand_dashbord_menu_items_arr', $dashmenu_links_cand);
            ob_start();
            $link_item_switch = isset($dashmenu_links_cand['job_alerts']) ? $dashmenu_links_cand['job_alerts'] : '';
            if ($cand_menu_item == 'job_alerts' && $link_item_switch == '1') {
                ?>
                <li<?php echo($get_tab == 'job_alerts' ? ' class="active"' : '') ?>>
                    <?php
                    if ($user_pkg_limits::cand_field_is_locked('dashtab_fields|job_alerts')) {
                        echo($user_pkg_limits::dashtab_locked_html('job-alerts', 'jobsearch-icon jobsearch-alarm', esc_html__('Job Alerts', 'wp-jobsearch')));
                    } else {
                        ?>
                        <a href="<?php echo add_query_arg(array('tab' => 'job-alerts'), $page_url) ?>">
                            <i class="jobsearch-icon jobsearch-alarm"></i>
                            <?php esc_html_e('Job Alerts', 'wp-jobsearch') ?>
                        </a>
                        <?php
                    }
                    ?>
                </li>
                <?php
            }
            $opts_item .= ob_get_clean();
        }

        return $opts_item;
    }

    public function dashboard_tab_content_add($html = '', $get_tab = '')
    {
        global $jobsearch_plugin_options, $Jobsearch_User_Dashboard_Settings;
        $job_alerts_switch = isset($jobsearch_plugin_options['job_alerts_switch']) ? $jobsearch_plugin_options['job_alerts_switch'] : '';

        $user_id = get_current_user_id();
        $is_employer = jobsearch_user_is_employer($user_id);
        
        $dashmenu_links_cand = isset($jobsearch_plugin_options['cand_dashbord_menu']) ? $jobsearch_plugin_options['cand_dashbord_menu'] : '';
        $dashmenu_links_cand = apply_filters('jobsearch_cand_dashbord_menu_items_arr', $dashmenu_links_cand);
        
        if ($job_alerts_switch == 'on' && $get_tab == 'job-alerts' && !$is_employer && isset($dashmenu_links_cand['job_alerts']) && $dashmenu_links_cand['job_alerts'] == '1') {
            wp_enqueue_script('jobsearch-job-alerts-scripts');
            $user_id = get_current_user_id();
            $page_id = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
            $page_id = jobsearch__get_post_id($page_id, 'page');
            $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);
            $reults_per_page = isset($jobsearch_plugin_options['user-dashboard-per-page']) && $jobsearch_plugin_options['user-dashboard-per-page'] > 0 ? $jobsearch_plugin_options['user-dashboard-per-page'] : 10;

            $page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;
            ob_start();
            ?>
            <div class="jobsearch-employer-box-section">
                <div class="jobsearch-profile-title">
                    <h2><?php esc_html_e('Job Alerts', 'wp-jobsearch') ?></h2>
                </div>
                <div class="jobsearch-job-alerts">
                    <div class="jobsearch-job-alerts-wrap">
                        <?php
                        $args = array(
                            'author' => $user_id,
                            'post_type' => 'job-alert',
                            'posts_per_page' => $reults_per_page,
                            'paged' => $page_num,
                            'orderby' => 'post_date',
                            'order' => 'DESC',
                        );
                        $job_alerts = new WP_Query($args);

                        $total_jobs = $job_alerts->found_posts;

                        if ($job_alerts->have_posts()) {
                            ?>
                            <table>
                                <thead>
                                <tr>
                                    <th><?php esc_html_e('Title', 'wp-jobsearch') ?></th>
                                    <th><?php esc_html_e('Criteria', 'wp-jobsearch') ?></th>
                                    <th><?php esc_html_e('Created Date', 'wp-jobsearch') ?></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                while ($job_alerts->have_posts()) : $job_alerts->the_post();

                                    $alert_id = get_the_ID();

                                    $search_criteria = get_post_meta($alert_id, 'jobsearch_field_alert_query', true);
                                    $alert_page_url = get_post_meta($alert_id, 'jobsearch_field_alert_page_url', true);
                                    ?>
                                    <tr>
                                        <td>
                                            <span><?php echo get_the_title($alert_id) ?></span>
                                        </td>
                                        <td><?php echo $this->alert_criteria_breakdown($search_criteria) ?></td>
                                        <td><?php echo get_the_date() ?></td>
                                        <td>
                                            <a href="javascript:void(0);"
                                               class="jobsearch-savedjobs-links jobsearch-del-user-job-alert"
                                               data-id="<?php echo($alert_id) ?>"><i
                                                        class="jobsearch-icon jobsearch-rubbish"></i></a>
                                            <a href="<?php echo($alert_page_url) ?>"
                                               class="jobsearch-savedjobs-links"><i
                                                        class="jobsearch-icon jobsearch-view"></i></a>
                                        </td>
                                    </tr>
                                <?php
                                endwhile;
                                wp_reset_postdata();
                                ?>
                                </tbody>
                            </table>
                            <?php
                            $total_pages = 1;
                            if ($total_jobs > 0 && $reults_per_page > 0 && $total_jobs > $reults_per_page) {
                                $total_pages = ceil($total_jobs / $reults_per_page);
                                ?>
                                <div class="jobsearch-pagination-blog">
                                    <?php $Jobsearch_User_Dashboard_Settings->pagination($total_pages, $page_num, $page_url) ?>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<p>' . esc_html__('No record found.', 'wp-jobsearch') . '</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php
            $html .= ob_get_clean();
        }

        return $html;
    }

    public function after_jobs_listing_callback($jobs_query, $sort_by)
    {
        echo '<div class="jobs_query" style="display:none;">' . json_encode($jobs_query) . '</div>';
    } //

    public function frontend_before_listings_ui_callback($args = array()) {
        global $jobsearch_plugin_options;

        $sh_atts = isset($args['sh_atts']) ? $args['sh_atts'] : '';
        $job_alerts_param = isset($sh_atts['job_alerts_top']) ? $sh_atts['job_alerts_top'] : '';

        if ($job_alerts_param == 'yes') {
            $this->frontend_full_listings_ui($args);
        }
    }

    public function frontend_after_listings_ui_callback($args = array()) {
        global $jobsearch_plugin_options;

        $sh_atts = isset($args['sh_atts']) ? $args['sh_atts'] : '';
        $job_alerts_param = isset($sh_atts['job_alerts_bottom']) ? $sh_atts['job_alerts_bottom'] : '';

        if ($job_alerts_param == 'yes') {
            $this->frontend_full_listings_ui($args);
        }
    }
    
    public function frontend_full_listings_ui() {
        global $jobsearch_plugin_options;

        $job_alerts_switch = isset($jobsearch_plugin_options['job_alerts_switch']) ? $jobsearch_plugin_options['job_alerts_switch'] : '';
        $for_login_only = isset($jobsearch_plugin_options['save_alerts_withlogin']) ? $jobsearch_plugin_options['save_alerts_withlogin'] : '';

        if ($job_alerts_switch == 'on') {
            wp_enqueue_script('jobsearch-job-alerts-scripts');

            $frequencies = array(
                'job_alerts_frequency_hourly' => esc_html__('Hourly', 'wp-jobsearch'),
                'job_alerts_frequency_daily' => esc_html__('Daily', 'wp-jobsearch'),
                'job_alerts_frequency_weekly' => esc_html__('Weekly', 'wp-jobsearch'),
                'job_alerts_frequency_fortnightly' => esc_html__('Fortnightly', 'wp-jobsearch'),
                'job_alerts_frequency_monthly' => esc_html__('Monthly', 'wp-jobsearch'),
                'job_alerts_frequency_biannually' => esc_html__('Biannually', 'wp-jobsearch'),
                'job_alerts_frequency_annually' => esc_html__('Annually', 'wp-jobsearch'),
                'job_alerts_frequency_never' => esc_html__('Never', 'wp-jobsearch'),
            );
            $frequencies = apply_filters('jobsearch_job_alert_frequencies_list', $frequencies);
            $options_str = '';
            $is_one_checked = false;
            $checked = 'checked="checked"';
            foreach ($frequencies as $frequency => $label) {

                $rand_id = rand(10000000, 99999999);
                if (isset($jobsearch_plugin_options[$frequency]) && 'on' == $jobsearch_plugin_options[$frequency]) {
                    $options_str .= '<li><input id="frequency' . $rand_id . '" name="alert-frequency" class="radio-frequency" maxlength="75" type="radio" value="' . ($frequency) . '" ' . $checked . '> <label for="frequency' . $rand_id . '"><span></span>' . $label . '</label></li>';
                    if (false == $is_one_checked) {
                        $checked = '';
                        $is_one_checked = true;
                    }
                }
            }

            $user = wp_get_current_user();
            $disabled = '';
            $email = '';
            if ($user->ID > 0) {
                $email = $user->user_email;
                $disabled = ' disabled="disabled"';
            }
            echo '
            <div class="jobsearch-alert-in-content job-alerts-sec">
            <div class="email-me-top">
                <button class="email-jobs-top"><i class="fa fa-envelope"></i> ' . esc_html__('Email me new jobs', 'wp-jobsearch') . '</button>
            </div>
            <div class="jobsearch-search-filter-wrap jobsearch-without-toggle jobsearch-add-padding">
            <div class="job-alert-box job-alert job-alert-container-top">
                <div class="alerts-fields">
                    <ul>
                        <li>
                            <input name="alerts-name" placeholder="' . esc_html__('Job alert name...', 'wp-jobsearch') . '" class="name-input-top" maxlength="75" type="text">
                        </li>
                        <li>
                            <input type="email" class="email-input-top alerts-email" placeholder=' . esc_html__("example@email.com", 'wp-jobsearch') . ' name="alerts-email" value="' . $email . '" ' . $disabled . '>
                        </li>
                        <li>
                            <button class="jobalert-submit' . ($for_login_only == 'on' && !is_user_logged_in() ? ' jobalert-save-withlogin' : '') . '" type="submit">' . esc_html__('Create Alert', 'wp-jobsearch') . '</button>
                        </li>
                    </ul>
                </div>' . (
                strlen($options_str) == 0 ? '' : (
                    '<div class="alert-frequency">
                            <ul class="jobsearch-checkbox">
                            ' . $options_str . '
                            </ul>
                        </div>'
                )
                ) .
                '<div class="validation error" style="display:none;">
                    <label for="alerts-email-top"></label>
                </div>
            </div>
            </div>
            </div>';
        }
    }

    public function frontend_before_listings_quick_detail_ui_callback($args = array())
    {
        global $jobsearch_plugin_options;

        $job_alerts_switch = isset($jobsearch_plugin_options['job_alerts_switch']) ? $jobsearch_plugin_options['job_alerts_switch'] : '';
        $for_login_only = isset($jobsearch_plugin_options['save_alerts_withlogin']) ? $jobsearch_plugin_options['save_alerts_withlogin'] : '';

        $sh_atts = isset($args['sh_atts']) ? $args['sh_atts'] : '';

        $job_alerts_param = isset($sh_atts['job_alerts_top']) ? $sh_atts['job_alerts_top'] : '';

        if ($job_alerts_param == 'yes' && $job_alerts_switch == 'on') {
            wp_enqueue_script('jobsearch-job-alerts-scripts');

            $frequencies = array(
                'job_alerts_frequency_hourly' => esc_html__('Hourly', 'wp-jobsearch'),
                'job_alerts_frequency_daily' => esc_html__('Daily', 'wp-jobsearch'),
                'job_alerts_frequency_weekly' => esc_html__('Weekly', 'wp-jobsearch'),
                'job_alerts_frequency_fortnightly' => esc_html__('Fortnightly', 'wp-jobsearch'),
                'job_alerts_frequency_monthly' => esc_html__('Monthly', 'wp-jobsearch'),
                'job_alerts_frequency_biannually' => esc_html__('Biannually', 'wp-jobsearch'),
                'job_alerts_frequency_annually' => esc_html__('Annually', 'wp-jobsearch'),
                'job_alerts_frequency_never' => esc_html__('Never', 'wp-jobsearch'),
            );
            $frequencies = apply_filters('jobsearch_job_alert_frequencies_list', $frequencies);
            $options_str = '';
            $is_one_checked = false;
            $checked = 'checked="checked"';
            foreach ($frequencies as $frequency => $label) {

                $rand_id = rand(10000000, 99999999);
                if (isset($jobsearch_plugin_options[$frequency]) && 'on' == $jobsearch_plugin_options[$frequency]) {
                    $options_str .= '<li><input id="frequency' . $rand_id . '" name="alert-frequency" class="radio-frequency" maxlength="75" type="radio" value="' . ($frequency) . '" ' . $checked . '> <label for="frequency' . $rand_id . '"><span></span>' . $label . '</label></li>';
                    if (false == $is_one_checked) {
                        $checked = '';
                        $is_one_checked = true;
                    }
                }
            }

            $user = wp_get_current_user();
            $disabled = '';
            $email = '';
            if ($user->ID > 0) {
                $email = $user->user_email;
                $disabled = ' disabled="disabled"';
            }
            echo '
            <div class="jobsearch-alert-in-content job-alerts-sec jobsearch-alert-quick-detail">
            <div class="email-me-top">
                <button class="email-jobs-top jobsearch-create-alert"><i class="fa fa-envelope"></i> ' . esc_html__('Create Alert', 'wp-jobsearch') . '</button>
            </div>
            <div class="jobsearch-search-filter-wrap jobsearch-without-toggle jobsearch-add-padding">
            <div class="job-alert-box job-alert job-alert-container-top">
                <div class="alerts-fields">
                    <ul>
                        <li>
                            <input name="alerts-name" placeholder="' . esc_html__('Job alert name...', 'wp-jobsearch') . '" class="name-input-top" maxlength="75" type="text">
                        </li>
                        <li>
                            <input type="email" class="email-input-top alerts-email" placeholder=' . esc_html__("example@email.com", 'wp-jobsearch') . ' name="alerts-email" value="' . $email . '" ' . $disabled . '>
                        </li>
                        <li>
                            <button class="jobalert-submit' . ($for_login_only == 'on' && !is_user_logged_in() ? ' jobalert-save-withlogin' : '') . '" type="submit">' . esc_html__('Create Alert', 'wp-jobsearch') . '</button>
                        </li>
                    </ul>
                </div>' . (
                strlen($options_str) == 0 ? '' : (
                    '<div class="alert-frequency">
                            <ul class="jobsearch-checkbox">
                            ' . $options_str . '
                            </ul>
                        </div>'
                )
                ) .
                '<div class="validation error" style="display:none;">
                    <label for="alerts-email-top"></label>
                </div>
            </div>
            </div>
            </div>';
        }
    }
    
    public function beautify_item_key_name($item_key) {
        
        if (preg_match("/location1/i", $item_key) || preg_match("/location_1/i", $item_key)) {
            $item_key = esc_html__('Country', 'wp-jobsearch');
        }
        if (preg_match("/location2/i", $item_key) || preg_match("/location_2/i", $item_key)) {
            $item_key = esc_html__('State', 'wp-jobsearch');
        }
        if (preg_match("/location3/i", $item_key) || preg_match("/location_3/i", $item_key)) {
            $item_key = esc_html__('City', 'wp-jobsearch');
        }
        if (preg_match("/sector_cat/i", $item_key)) {
            $item_key = esc_html__('Sector', 'wp-jobsearch');
        }
        if (preg_match("/jobsearch_field_job_salary_type/i", $item_key)) {
            $item_key = esc_html__('Salary Type', 'wp-jobsearch');
        } else if (preg_match("/job_salary/i", $item_key)) {
            $item_key = esc_html__('Salary', 'wp-jobsearch');
        }
        if (preg_match("/job_industries/i", $item_key)) {
            $item_key = esc_html__('Industries', 'wp-jobsearch');
        }
        
        $item_key = ucfirst(str_replace(array('-', '_'), array(' ', ' '), $item_key));
        
        return $item_key;
    }

    public function alert_criteria_breakdown($criteria)
    {
        global $jobsearch_plugin_options, $sitepress;
        $lang_code = '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $lang_code = $sitepress->get_current_language();
        }
        
        $cusfileds_names_arr = array();
        $jobsearch_post_cus_fields = get_option('jobsearch_custom_field_job');
        if (is_array($jobsearch_post_cus_fields) && sizeof($jobsearch_post_cus_fields) > 0) {
            foreach ($jobsearch_post_cus_fields as $acus_key => $cus_field) {
                $f_custf_name = isset($cus_field['name']) ? $cus_field['name'] : '';
                $f_custf_type = isset($cus_field['type']) ? $cus_field['type'] : '';
                
                if ($f_custf_type == 'checkbox' || $f_custf_type == 'dropdown') {
                    $cusfileds_names_arr[$acus_key] = $f_custf_name;
                }
            }
        }
        
        $html = '';
        $items_html = '';
        if ($criteria != '') {
            $disalow_keys = array('ajax_filter', 'posted', 'sort-by', 'alerts-name');
            $criteria_arr = explode('&', $criteria);
            if (!empty($criteria_arr)) {
                $criteria_arr = array_unique($criteria_arr);
                $criteria_arr = apply_filters('jobsearch_dash_alert_criteria_list', $criteria_arr);
                foreach ($criteria_arr as $crite_item) {
                    //echo ($crite_item); echo '<br>';
                    $item_expl = explode('=', $crite_item);
                    if (isset($item_expl[0]) && isset($item_expl[1]) && $item_expl[0] != '' && $item_expl[1] != '') {
                        $item_key = $item_expl[0];
                        $item_key_exclude = apply_filters('jobsearch_is_jobalert_query_exclude_key', false, $item_key);
                        if ($item_key_exclude) {
                            continue;
                        }
                        $item_val = $item_expl[1];
                        if ($item_key == 'sector_cat') {
                            $sector_catobj = get_term_by('slug', $item_val, 'sector');
                            if (isset($sector_catobj->name)) {
                                $item_val = $sector_catobj->name;
                            }
                        }
                        if (in_array($item_key, $cusfileds_names_arr)) {
                            $cusobj_key = array_search($item_key, $cusfileds_names_arr);
                            $cus_field_objarr = $jobsearch_post_cus_fields[$cusobj_key];
                            $item_key = isset($cus_field_objarr['label']) ? $cus_field_objarr['label'] : $item_key;
                            $f_custf_type = isset($cus_field_objarr['type']) ? $cus_field_objarr['type'] : '';
                            $field_put_val = $item_val;
                            if ($f_custf_type == 'dropdown') {
                                $drop_down_arr = array();
                                $cut_field_flag = 0;
                                foreach ($cus_field_objarr['options']['value'] as $key => $cus_field_options_value) {

                                    $drop_down_arr[$cus_field_options_value] = (apply_filters('wpml_translate_single_string', $cus_field_objarr['options']['label'][$cut_field_flag], 'Custom Fields', 'Dropdown Option Label - ' . $cus_field_objarr['options']['label'][$cut_field_flag], $lang_code));
                                    $cut_field_flag++;
                                }
                                if (is_array($field_put_val) && !empty($field_put_val)) {
                                    $field_put_valarr = array();
                                    foreach ($field_put_val as $fil_putval) {
                                        if (isset($drop_down_arr[$fil_putval]) && $drop_down_arr[$fil_putval] != '') {
                                            $field_put_valarr[] = $drop_down_arr[$fil_putval];
                                        }
                                    }
                                    $item_val = implode(', ', $field_put_valarr);
                                } else {
                                    if (isset($drop_down_arr[$field_put_val]) && $drop_down_arr[$field_put_val] != '') {
                                        $item_val = $drop_down_arr[$field_put_val];
                                    }
                                }
                            } else if ($f_custf_type == 'checkbox') {
                                $drop_down_arr = array();
                                $cut_field_flag = 0;
                                foreach ($cus_field_objarr['options']['value'] as $key => $cus_field_options_value) {

                                    $drop_down_arr[$cus_field_options_value] = (apply_filters('wpml_translate_single_string', $cus_field_objarr['options']['label'][$cut_field_flag], 'Custom Fields', 'Checkbox Option Label - ' . $cus_field_objarr['options']['label'][$cut_field_flag], $lang_code));
                                    $cut_field_flag++;
                                }
                                if (is_array($field_put_val) && !empty($field_put_val)) {
                                    $field_put_valarr = array();
                                    foreach ($field_put_val as $fil_putval) {
                                        if (isset($drop_down_arr[$fil_putval]) && $drop_down_arr[$fil_putval] != '') {
                                            $field_put_valarr[] = $drop_down_arr[$fil_putval];
                                        }
                                    }
                                    $item_val = implode(', ', $field_put_valarr);
                                } else {
                                    if (isset($drop_down_arr[$field_put_val]) && $drop_down_arr[$field_put_val] != '') {
                                        $item_val = $drop_down_arr[$field_put_val];
                                    }
                                }
                            }
                        }
                        if ($item_key == 'job_type') {
                            if (is_array($item_val)) {
                                $termnames_arr = array();
                                foreach ($item_val as $itmval_slug) {
                                    $sector_catobj = get_term_by('slug', $itmval_slug, 'jobtype');
                                    if (isset($sector_catobj->name)) {
                                        $termnames_arr[] = $sector_catobj->name;
                                    }
                                }
                                $item_val = implode(', ', $termnames_arr);
                            } else {
                                $sector_catobj = get_term_by('slug', $item_val, 'jobtype');
                                if (isset($sector_catobj->name)) {
                                    $item_val = $sector_catobj->name;
                                }
                            }
                        }
                        if ($item_key == 'job_salary_type') {
                            $job_salary_types = isset($jobsearch_plugin_options['job-salary-types']) ? $jobsearch_plugin_options['job-salary-types'] : '';
                            if (!empty($job_salary_types)) {
                                $slar_type_count = 1;
                                foreach ($job_salary_types as $job_salary_typ) {
                                    $job_salary_typ = apply_filters('wpml_translate_single_string', $job_salary_typ, 'JobSearch Options', 'Salary Type - ' . $job_salary_typ, $lang_code);
                                    if ($item_val == 'type_' . $slar_type_count) {
                                        $item_val = $job_salary_typ;
                                    }
                                    $slar_type_count++;
                                }
                            }
                        }
                        $item_key = $this->beautify_item_key_name($item_key);
                        if (!in_array($item_key, $disalow_keys)) {
                            if (strpos($item_val, 'job_alerts_frequency_') !== false) {
                                $item_val = str_replace('job_alerts_frequency_', '', $item_val);
                            }
                            $items_html .= '<li>' . $item_key . ': ' . $item_val . '</li>';
                        }
                    }
                }
                if ($items_html != '') {
                    $html .= '<ul class="jobalert-criteria-con">' . $items_html . '</ul>' . "\n";
                }
            }
            //
        }
        return $html;
    }

    public function frontend_before_filters_ui_callback($args = array())
    {
        global $jobsearch_plugin_options;

        $job_alerts_switch = isset($jobsearch_plugin_options['job_alerts_switch']) ? $jobsearch_plugin_options['job_alerts_switch'] : '';
        $for_login_only = isset($jobsearch_plugin_options['save_alerts_withlogin']) ? $jobsearch_plugin_options['save_alerts_withlogin'] : '';

        $sh_atts = isset($args['sh_atts']) ? $args['sh_atts'] : '';
        $job_alerts_param = isset($sh_atts['job_alerts']) ? $sh_atts['job_alerts'] : '';

        if ($job_alerts_param != 'no' && $job_alerts_switch == 'on') {
            wp_enqueue_script('jobsearch-job-alerts-scripts');

            $frequencies = array(
                'job_alerts_frequency_hourly' => esc_html__('Hourly', 'wp-jobsearch'),
                'job_alerts_frequency_daily' => esc_html__('Daily', 'wp-jobsearch'),
                'job_alerts_frequency_weekly' => esc_html__('Weekly', 'wp-jobsearch'),
                'job_alerts_frequency_fortnightly' => esc_html__('Fortnightly', 'wp-jobsearch'),
                'job_alerts_frequency_monthly' => esc_html__('Monthly', 'wp-jobsearch'),
                'job_alerts_frequency_biannually' => esc_html__('Biannually', 'wp-jobsearch'),
                'job_alerts_frequency_annually' => esc_html__('Annually', 'wp-jobsearch'),
                'job_alerts_frequency_never' => esc_html__('Never', 'wp-jobsearch'),
            );
            $frequencies = apply_filters('jobsearch_job_alert_frequencies_list', $frequencies);
            $options_str = '';
            $is_one_checked = false;
            $checked = 'checked="checked"';
            foreach ($frequencies as $frequency => $label) {

                $rand_id = rand(10000000, 99999999);
                if (isset($jobsearch_plugin_options[$frequency]) && 'on' == $jobsearch_plugin_options[$frequency]) {
                    $options_str .= '<li><input id="frequency' . $rand_id . '" name="alert-frequency" class="radio-frequency" maxlength="75" type="radio" value="' . ($frequency) . '" ' . $checked . '> <label for="frequency' . $rand_id . '"><span></span>' . $label . '</label></li>';
                    if (false == $is_one_checked) {
                        $checked = '';
                        $is_one_checked = true;
                    }
                }
            }

            $user = wp_get_current_user();
            $disabled = '';
            $email = '';
            if ($user->ID > 0) {
                $email = $user->user_email;
                $disabled = ' disabled="disabled"';
            }
            echo '
            <div class="jobsearch-filter-responsive-wrap job-alerts-sec">
            <div class="email-me-top">
                <button class="email-jobs-top"><i class="fa fa-envelope"></i> ' . esc_html__('Email me new jobs', 'wp-jobsearch') . '</button>
            </div>
            <div class="jobsearch-search-filter-wrap jobsearch-without-toggle jobsearch-add-padding">
            <div class="job-alert-box job-alert job-alert-container-top">
                    <div class="alerts-fields">
                        <input name="alerts-name" placeholder="' . esc_html__('Job alert name...', 'wp-jobsearch') . '" class="name-input-top" maxlength="75" type="text">
                        <input type="email" class="email-input-top alerts-email" placeholder=' . esc_html__("example@email.com", 'wp-jobsearch') . ' name="alerts-email" value="' . $email . '" ' . $disabled . '>
                    </div>' . (
                strlen($options_str) == 0 ? '' : (
                    '<div class="alert-frequency">
                            <ul class="jobsearch-checkbox">
                            ' . $options_str . '
                            </ul>
                        </div>'
                )
                ) .
                '<div class="validation error" style="display:none;">
                    <label for="alerts-email-top"></label>
                </div>
                <button class="jobalert-submit' . ($for_login_only == 'on' && !is_user_logged_in() ? ' jobalert-save-withlogin' : '') . '" type="submit">' . esc_html__('Create Alert', 'wp-jobsearch') . '</button>
            </div>
            </div>
            </div>';
        }
    }

    public function job_alert_criteria_selist()
    {
        $tags_list = array();

        if (isset($_REQUEST['search_title']) && $_REQUEST['search_title'] != '') {
            $job_search_title = $_REQUEST['search_title'];
            $job_search_title = jobsearch_esc_html($job_search_title);
            $tags_list['search_title'] = $job_search_title;
        }

        $loc_val = '';
        if (isset($_REQUEST['location']) && $_REQUEST['location'] != '') {
            $loc_val = $_REQUEST['location'];
        }
        if (isset($_REQUEST['location_location1']) && $_REQUEST['location_location1'] != '') {
            $loc_val = $_REQUEST['location_location1'];
            if (isset($_REQUEST['location_location2']) && $_REQUEST['location_location2'] != '') {
                $loc_val = $_REQUEST['location_location2'] . ', ' . $loc_val;
            }
            if (isset($_REQUEST['location_location3']) && $_REQUEST['location_location3'] != '') {
                $loc_val = $_REQUEST['location_location3'] . ', ' . $loc_val;
            }
        }
        $loc_val = jobsearch_esc_html($loc_val);
        if ($loc_val != '') {
            $tags_list['location'] = $loc_val;
        }
        if (isset($_REQUEST['sector_cat']) && $_REQUEST['sector_cat'] != '') {
            $job_sector = $_REQUEST['sector_cat'];
            $tags_list['sector'] = $job_sector;
        }
        if (isset($_REQUEST['job_type']) && $_REQUEST['job_type'] != '') {
            $job_type = $_REQUEST['job_type'];
            $tags_list['job_type'] = $job_type;
        }
        if (isset($_REQUEST['get_job_industries']) && $_REQUEST['get_job_industries'] != '') {
            $job_industry = $_REQUEST['get_job_industries'];
            $tags_list['industries'] = $job_industry;
        }

        //
        $job_cus_fields = get_option("jobsearch_custom_field_job");
        if (!empty($job_cus_fields)) {
            foreach ($job_cus_fields as $cus_fieldvar => $cus_field) {
                if ($cus_field['type'] == 'salary') {
                    $query_str_var_name = 'jobsearch_field_job_salary';
                } else {
                    $query_str_var_name = isset($cus_field['name']) ? $cus_field['name'] : '';
                }
                if (isset($_REQUEST[$query_str_var_name]) && !empty($_REQUEST[$query_str_var_name])) {
                    $tags_list[$query_str_var_name] = $_REQUEST[$query_str_var_name];
                }
            }
        }
        
        $tags_list = apply_filters('jobsearch_alertpop_top_tags_arr', $tags_list);

        ob_start();
        if (!empty($tags_list)) {
            ?>
            <div class="jobsearch-filterable">
                <ul class="filtration-tags">
                    <?php
                    foreach ($tags_list as $qry_var => $qry_val) {
                        if (is_array($qry_val)) {
                            $qry_val = implode(', ', $qry_val);
                        }
                        ?>
                        <li>
                            <a title="<?php echo ucwords(str_replace(array("+", "-", "_"), " ", $qry_var)) ?>"><?php echo jobsearch_esc_html($qry_val) ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <?php
        }
        
        $html = ob_get_clean();

        if (isset($_POST['jobsearch_alert_tagsup']) && $_POST['jobsearch_alert_tagsup'] == '1') {
            echo json_encode(array('html' => $html));
            die;
        }
        echo $html;
    }

    public function job_alert_popup()
    {

        global $jobsearch_plugin_options, $jobsearch_jobalertfiltrs_html;

        $job_alerts_switch = isset($jobsearch_plugin_options['job_alerts_switch']) ? $jobsearch_plugin_options['job_alerts_switch'] : '';
        $for_login_only = isset($jobsearch_plugin_options['save_alerts_withlogin']) ? $jobsearch_plugin_options['save_alerts_withlogin'] : '';

        $to_add_popup = true;
        if ($for_login_only == 'on' && !is_user_logged_in()) {
            $to_add_popup = false;
        }

        if ($job_alerts_switch == 'on' && $to_add_popup) {

            $frequencies = array(
                'job_alerts_frequency_hourly' => esc_html__('Hourly', 'wp-jobsearch'),
                'job_alerts_frequency_daily' => esc_html__('Daily', 'wp-jobsearch'),
                'job_alerts_frequency_weekly' => esc_html__('Weekly', 'wp-jobsearch'),
                'job_alerts_frequency_fortnightly' => esc_html__('Fortnightly', 'wp-jobsearch'),
                'job_alerts_frequency_monthly' => esc_html__('Monthly', 'wp-jobsearch'),
                'job_alerts_frequency_biannually' => esc_html__('Biannually', 'wp-jobsearch'),
                'job_alerts_frequency_annually' => esc_html__('Annually', 'wp-jobsearch'),
                'job_alerts_frequency_never' => esc_html__('Never', 'wp-jobsearch'),
            );
            ?>
            <div class="jobsearch-modal jobalerts_modal_popup fade" id="JobSearchModalJobAlertsSelect">
                <div class="modal-inner-area">&nbsp;</div>
                <div class="modal-content-area">
                    <div class="modal-box-area">
                        <div class="jobsearch-modal-title-box">
                            <h2><?php esc_html_e('Job Alerts', 'wp-jobsearch') ?></h2>
                            <span class="modal-close"><i class="fa fa-times"></i></span>
                        </div>
                        <div class="jobsearch-jobalerts-popcon">
                            <div id="modpop-criteria-tags" class="criteria-tags-popmain">
                                <?php $this->job_alert_criteria_selist(); ?>
                            </div>
                            <form id="popup_alert_filtrsform" method="post">
                                <div id="popup_alert_filtrscon" class="popup-jobfilters-con">
                                    <?php
                                    echo($jobsearch_jobalertfiltrs_html);
                                    ?>
                                </div>
                                <div class="alret-submitbtn-con">
                                    <input type="hidden" name="alerts_name" value="">
                                    <input type="hidden" name="alerts_email" value="">
                                    <input type="hidden" name="action" value="jobsearch_create_job_alert">
                                    <a href="javascript:void(0);"
                                       class="jobsearch-savejobalrts-sbtn"><?php esc_html_e('Save Jobs Alert', 'wp-jobsearch') ?></a>
                                    <div class="falrets-msg"></div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            <?php
        }
    }

    public function create_job_alert_callback()
    {

        global $sitepress, $jobsearch_plugin_options;

        $jobs_listin_class = new Jobsearch_Shortcode_Jobs_Frontend();

        $for_login_only = isset($jobsearch_plugin_options['save_alerts_withlogin']) ? $jobsearch_plugin_options['save_alerts_withlogin'] : '';

        // Read data from user input.
        $email = sanitize_text_field($_POST['alerts_email']);
        $name = sanitize_text_field($_POST['alerts_name']);
        $location = sanitize_text_field($_POST['window_location']);

        $page_url = explode('?', $location);
        $page_url = isset($page_url[0]) ? $page_url[0] : '';

        //$query = end(explode('?', $location));
        $all_posts_data = $_POST;
        if (isset($all_posts_data['alert_frequency'])) {
            unset($all_posts_data['alert_frequency']);
        }
        if (isset($all_posts_data['alerts_name'])) {
            unset($all_posts_data['alerts_name']);
        }
        if (isset($all_posts_data['alerts_email'])) {
            unset($all_posts_data['alerts_email']);
        }
        if (isset($all_posts_data['action'])) {
            unset($all_posts_data['action']);
        }
        if (isset($all_posts_data['window_location'])) {
            unset($all_posts_data['window_location']);
        }
        if (isset($all_posts_data['search_query'])) {
            unset($all_posts_data['search_query']);
        }
        if (isset($all_posts_data['job_shatts_str'])) {
            unset($all_posts_data['job_shatts_str']);
        }

        $post_d_query = '';
        $post_page_query = '';
        $post_dcounter = 1;
        $post_p_dcounter = 1;
        foreach ($all_posts_data as $postd_key => $postd_val) {
            if (is_array($postd_val)) {
                $postd_val = implode(',', $postd_val);
            }
            $post_d_query .= ($post_dcounter > 1 ? '&' : '') . $postd_key . '=' . $postd_val;
            if ($postd_val != '') {
                $post_page_query .= ($post_p_dcounter > 1 ? '&' : '') . $postd_key . '=' . $postd_val;
                $post_p_dcounter++;
            }
            $post_dcounter++;
        }
        $query = $post_d_query;
        if ($post_page_query != '') {
            $page_url = $page_url . '?' . $post_page_query . '&ajax_filter=true';
        }

        $frequency = sanitize_text_field($_POST['alert_frequency']);
        if ($frequency != '' && strpos($frequency, 'job_alerts_frequency_') !== false) {
            $frequency = str_replace('job_alerts_frequency_', 'alert_', $frequency);
        }

        $jobs_query = isset($_POST['search_query']) ? $_POST['search_query'] : '';

        $jobs_sh_atts = isset($_POST['job_shatts_str']) ? $_POST['job_shatts_str'] : '';
        if ($jobs_sh_atts != '') {
            $sh_atts = stripslashes($jobs_sh_atts);
            $sh_atts = json_decode($sh_atts, true);
            //
            $jobs_query_arr = $jobs_listin_class->jobs_list_args($sh_atts);
            if (isset($jobs_query_arr['args'])) {
                $jobs_query = $jobs_query_arr['args'];
                if (isset($jobs_query['post__in'])) {
                    unset($jobs_query['post__in']);
                }
                if (isset($jobs_query['meta_query'])) {
                    $jobs_query['meta_query'][0][] = array(
                        'key' => 'jobsearch_field_job_status',
                        'value' => 'approved',
                        'compare' => '=',
                    );
                }
                $jobs_query = json_encode($jobs_query);
            }
        }

        if ($for_login_only == 'on' && !is_user_logged_in()) {
            $return = array('success' => false, "message" => esc_html__("Only a logged-in user can save job alerts.", 'wp-jobsearch'));
            echo json_encode($return);
            wp_die();
        }

        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $is_employer = jobsearch_user_is_employer($user_id);
            if ($is_employer) {
                $return = array('success' => false, "message" => esc_html__("You cannot create a job alert.", 'wp-jobsearch'));
                echo json_encode($return);
                wp_die();
            }
        }

        if (empty($name) || empty($email) || empty($query) || empty($frequency)) {
            $return = array('success' => false, "message" => esc_html__("Provided data is incomplete.", 'wp-jobsearch'));
        } else {
            $meta_query = array(
                array(
                    'key' => 'jobsearch_field_alert_email',
                    'value' => $email,
                    'compare' => '=',
                ),
                array(
                    'key' => 'jobsearch_field_' . $frequency,
                    'value' => 'on',
                    'compare' => '=',
                ),
            );
            if ($jobs_query <> '') {
                $meta_query[] = array(
                    'key' => 'jobsearch_field_alert_jobs_query',
                    'value' => stripslashes($jobs_query),
                    'compare' => '=',
                );
            }
            $args = array(
                'post_type' => 'job-alert',
                'meta_query' => $meta_query,
            );
            $obj_query = new WP_Query($args);
            $count = $obj_query->post_count;
            do_action('jobsearch_before_job_alert_create_byuser', $jobs_query);
            if ($count > 0) {
                $return = array('success' => false, "message" => esc_html__("Alert already exists with this criteria", 'wp-jobsearch'));
            } else {
                // Insert Job Alert as a post.
                $job_alert_data = array(
                    'post_title' => $name,
                    'post_status' => 'publish',
                    'post_type' => 'job-alert',
                    'comment_status' => 'closed',
                    'post_author' => get_current_user_id(),
                );
                $job_alert_id = wp_insert_post($job_alert_data);
                if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                    $lang_code = $sitepress->get_default_language();
                    $lang_code = $lang_code;
                    $sitepress->set_element_language_details($job_alert_id, 'post_job-alert', false, $lang_code);
                }

                // Update email.
                update_post_meta($job_alert_id, 'jobsearch_field_alert_email', $email);
                // Update name.
                update_post_meta($job_alert_id, 'jobsearch_field_alert_name', $name);
                // Update frequencies.
                update_post_meta($job_alert_id, 'jobsearch_field_' . $frequency, 'on');

                // Update query.
                update_post_meta($job_alert_id, 'jobsearch_field_alert_query', $query);

                // Update listings url.
                update_post_meta($job_alert_id, 'jobsearch_field_alert_page_url', $page_url);

                // Last time email sent.
                update_post_meta($job_alert_id, 'last_time_email_sent', 0);

                // Query.
                update_post_meta($job_alert_id, 'jobsearch_field_alert_jobs_query', stripslashes($jobs_query));
                
                $jobsearch_post_cus_fields = get_option('jobsearch_custom_field_job');
                if (is_array($jobsearch_post_cus_fields) && sizeof($jobsearch_post_cus_fields) > 0) {
                    foreach ($jobsearch_post_cus_fields as $cus_field) {
                        if ($cus_field['type'] == 'salary') {
                            $query_str_var_name = 'jobsearch_field_job_salary';
                            $str_salary_type_name = 'job_salary_type';
                            if (isset($_REQUEST[$str_salary_type_name]) && !empty($_REQUEST[$str_salary_type_name])) {
                                update_post_meta($job_alert_id, $str_salary_type_name, $_REQUEST[$str_salary_type_name]);
                            }
                        } else {
                            $f_custf_name = isset($cus_field['name']) ? $cus_field['name'] : '';
                            $query_str_var_name = trim(str_replace(' ', '', $f_custf_name));
                        }
                        if (isset($_REQUEST[$query_str_var_name]) && !empty($_REQUEST[$query_str_var_name])) {
                            update_post_meta($job_alert_id, $query_str_var_name, $_REQUEST[$query_str_var_name]);
                        }
                    }
                }
                
                do_action('jobsearch_after_jobalert_save_by_user', $job_alert_id);

                $return = array('success' => true, "message" => esc_html__("Job alert successfully added.", 'wp-jobsearch'));
            }
        }
        echo json_encode($return);
        wp_die();
    }

    public function unsubscribe_job_alert()
    {
        if (isset($_REQUEST['jaid'])) {
            $job_alert_id = sanitize_text_field($_REQUEST['jaid']);
            $post_data = get_post($job_alert_id);
            if ($post_data) {
                wp_delete_post($job_alert_id, true);
                echo '<div class="job_alert_unsubscribe_msg" style="text-align: center;"><h3>' . esc_html__('Job alert successfully unsubscribed.', 'wp-jobsearch') . '</h3></div>';
            } else {
                echo '<div class="job_alert_unsubscribe_msg" style="text-align: center;"><h3>' . esc_html__('Sorry! Job alert already unsubscribed.', 'wp-jobsearch') . '</h3></div>';
            }
        }
        die();
    }

    public function remove_job_alert()
    {
        if (isset($_REQUEST['alert_id'])) {
            if (jobsearch_candidate_not_allow_to_mod()) {
                $msg = esc_html__('You are not allowed to delete this.', 'wp-jobsearch');
                echo json_encode(array('msg' => $msg));
                die;
            }
            if (jobsearch_employer_not_allow_to_mod()) {
                $msg = esc_html__('You are not allowed to delete this.', 'wp-jobsearch');
                echo json_encode(array('msg' => $msg));
                die;
            }
            $job_alert_id = sanitize_text_field($_REQUEST['alert_id']);
            $post_data = get_post($job_alert_id);
            if ($post_data) {
                wp_delete_post($job_alert_id, true);
            }
        }
        die();
    }

}

// Class JobSearch_Job_Alerts_Hooks
global $JobSearch_Job_Alerts_Hooks_obj;
$JobSearch_Job_Alerts_Hooks_obj = new JobSearch_Job_Alerts_Hooks();
