<?php

namespace Wp_JobsearchElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleJobInfo extends Widget_Base
{

    /**
     * Retrieve the widget name.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'single-job-info';
    }

    /**
     * Retrieve the widget title.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return __('Single Job Info', 'wp-jobsearch');
    }

    /**
     * Retrieve the widget icon.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'fa fa-link';
    }

    /**
     * Retrieve the list of categories the widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * Note that currently Elementor supports only one category.
     * When multiple categories passed, Elementor uses the first one.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories()
    {
        return ['jobsearch-job-single'];
    }

    /**
     * Register the widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.1.0
     *
     * @access protected
     */
    protected function _register_controls()
    {
        $this->start_controls_section(
            'content_section', [
                'label' => __('Job Info Settings', 'wp-jobsearch'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'job_title', [
                'label' => __('Job Title', 'wp-jobsearch'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'job_type', [
                'label' => __('Job Type', 'wp-jobsearch'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'employer', [
                'label' => __('Employer', 'wp-jobsearch'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'posted_date', [
                'label' => __('Posted Date', 'wp-jobsearch'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'deadline_date', [
                'label' => __('Application Deadline Date', 'wp-jobsearch'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'location', [
                'label' => __('Location', 'wp-jobsearch'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'sector', [
                'label' => __('Sector', 'wp-jobsearch'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'salary', [
                'label' => __('Salary', 'wp-jobsearch'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'num_applicants', [
                'label' => __('Number of Applicants', 'wp-jobsearch'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'views', [
                'label' => __('Views', 'wp-jobsearch'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'savejob_btn', [
                'label' => __('Save Job Button', 'wp-jobsearch'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'email_job', [
                'label' => __('Email Job Button', 'wp-jobsearch'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'social_share', [
                'label' => __('Social Share', 'wp-jobsearch'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        global $post, $jobsearch_plugin_options;
        $job_id = is_admin() ? jobsearch_job_id_elementor() : $post->ID;

        $atts = $this->get_settings_for_display();

        extract(shortcode_atts(array(
            'job_title' => '',
            'job_type' => '',
            'employer' => '',
            'posted_date' => '',
            'deadline_date' => '',
            'sector' => '',
            'location' => '',
            'salary' => '',
            'num_applicants' => '',
            'views' => '',
            'savejob_btn' => '',
            'email_job' => '',
            'social_share' => '',
        ), $atts));

        $job_det_full_address_switch = true;
        $locations_view_type = isset($jobsearch_plugin_options['job_det_loc_listing']) ? $jobsearch_plugin_options['job_det_loc_listing'] : '';
        $loc_view_country = $loc_view_state = $loc_view_city = false;


        if (!empty($locations_view_type)) {
            if (is_array($locations_view_type) && in_array('country', $locations_view_type)) {
                $loc_view_country = true;
            }
            if (is_array($locations_view_type) && in_array('state', $locations_view_type)) {
                $loc_view_state = true;
            }
            if (is_array($locations_view_type) && in_array('city', $locations_view_type)) {
                $loc_view_city = true;
            }
        }

        $social_share_allow = isset($jobsearch_plugin_options['job_detail_soc_share']) ? $jobsearch_plugin_options['job_detail_soc_share'] : '';
        $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
        $job_views_publish_date = isset($jobsearch_plugin_options['job_views_publish_date']) ? $jobsearch_plugin_options['job_views_publish_date'] : '';
        //
        $job_apps_count_switch = isset($jobsearch_plugin_options['job_detail_apps_count']) ? $jobsearch_plugin_options['job_detail_apps_count'] : '';
        $job_views_count_switch = isset($jobsearch_plugin_options['job_detail_views_count']) ? $jobsearch_plugin_options['job_detail_views_count'] : '';
        $job_shortlistbtn_switch = isset($jobsearch_plugin_options['job_detail_shrtlist_btn']) ? $jobsearch_plugin_options['job_detail_shrtlist_btn'] : '';

        $job_apply_type = get_post_meta($job_id, 'jobsearch_field_job_apply_type', true);

        $locations_lat = get_post_meta($job_id, 'jobsearch_field_location_lat', true);
        $locations_lng = get_post_meta($job_id, 'jobsearch_field_location_lng', true);

        $post_thumbnail_id = jobsearch_job_get_profile_image($job_id);
        $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'jobsearch-job-medium');
        $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : '';
        $post_thumbnail_src = $post_thumbnail_src == '' ? jobsearch_no_image_placeholder() : $post_thumbnail_src;
        $post_thumbnail_src = apply_filters('jobsearch_jobemp_image_src', $post_thumbnail_src, $job_id);
        $application_deadline = get_post_meta($job_id, 'jobsearch_field_job_application_deadline_date', true);
        $jobsearch_job_posted = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
        $jobsearch_job_posted_ago = jobsearch_time_elapsed_string($jobsearch_job_posted, ' ' . esc_html__('posted', 'wp-jobsearch') . ' ');
        $jobsearch_job_posted_formated = '';
        if ($jobsearch_job_posted != '') {
            $jobsearch_job_posted_formated = date_i18n(get_option('date_format'), ($jobsearch_job_posted));
        }

        $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
        $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

        $job_city_title = '';
        if (function_exists('jobsearch_post_city_contry_txtstr')) {
            $job_city_title = jobsearch_post_city_contry_txtstr($job_id, $loc_view_country, $loc_view_state, $loc_view_city, $job_det_full_address_switch);
        }

        if ($job_city_title != '') {
            $get_job_location = $job_city_title;
        }
        $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';
        $job_date = get_post_meta($job_id, 'jobsearch_field_job_date', true);
        $job_views_count = get_post_meta($job_id, 'jobsearch_job_views_count', true);
        $job_type_str = jobsearch_job_get_all_jobtypes($job_id, 'jobsearch-jobdetail-type', '', '', '<small>', '</small>');
        $sector_str = jobsearch_job_get_all_sectors($job_id, '', ' ' . esc_html__('in', 'wp-jobsearch') . ' ', '', '<small class="post-in-category">', '</small>');
        $company_name = jobsearch_job_get_company_name($job_id, '');
        $skills_list = jobsearch_job_get_all_skills($job_id);
        $job_obj = get_post($job_id);
        $job_content = isset($job_obj->post_content) ? $job_obj->post_content : '';
        $job_content = apply_filters('the_content', $job_content);
        $job_salary = jobsearch_job_offered_salary($job_id);
        $job_applicants_list = get_post_meta($job_id, 'jobsearch_job_applicants_list', true);
        $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
        $job_field_user = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
        if (empty($job_applicants_list)) {
            $job_applicants_list = array();
        }
        $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
        $job_applicants_count = !empty($job_applicants_list) ? count($job_applicants_list) : 0;

        ob_start();
        ?>
        <div class="elementorsec-job-info">
            <?php if ($job_title == 'yes') { ?>
                <h2><?php echo jobsearch_esc_html(force_balance_tags(get_the_title($job_id))); ?></h2>
                <?php
            }
            ob_start();
            ?>
            <span>
                <?php
                ob_start();
                if ($job_type_str != '' && $job_type == 'yes') {
                    echo force_balance_tags($job_type_str);
                }
                $jobtype_html = ob_get_clean();
                echo apply_filters('jobsearch_job_indetail_jobtype_html', $jobtype_html, $job_id, 'default_view');

                if ($company_name != '' && $employer == 'yes') {
                    ob_start();
                    echo force_balance_tags($company_name);
                    $comp_name_html = ob_get_clean();
                    echo apply_filters('jobsearch_empname_in_jobdetail', $comp_name_html, $job_id, 'view1');
                }
                if ($jobsearch_job_posted_ago != '' && $job_views_publish_date == 'on' && $posted_date == 'yes') {
                    ?>
                    <small class="jobsearch-jobdetail-postinfo"><?php echo esc_html($jobsearch_job_posted_ago); ?></small>
                    <?php
                }

                if ($sectors_enable_switch == 'on' && $sector == 'yes') {
                    echo apply_filters('jobsearch_jobdetail_sector_str_html', $sector_str, $job_id);
                }
                ?>
            </span>
            <?php
            do_action('jobsearch_job_indetail_before_locat_options', $job_id, 'default_view');
            ?>
            <ul class="jobsearch-jobdetail-options">
                <?php
                ob_start();
                if ((!empty($get_job_location) || ($locations_lat != '' && $locations_lng != '')) && $all_location_allow == 'on' && $location == 'yes') {
                    $view_map_loc = urlencode($get_job_location);
                    if ($locations_lat != '' && $locations_lng != '') {
                        $view_map_loc = urlencode($locations_lat . ',' . $locations_lng);
                    }
                    $google_mapurl = 'https://www.google.com/maps/search/' . $view_map_loc;
                    ?>
                    <li>
                        <?php if (!empty($get_job_location)) { ?>
                            <i class="fa fa-map-marker"></i> <?php echo jobsearch_esc_html($get_job_location); ?>
                            <a href="<?php echo($google_mapurl); ?>" target="_blank"
                               class="jobsearch-jobdetail-view"><?php echo esc_html__('View on Map', 'wp-jobsearch') ?></a>
                        <?php } ?>
                    </li>
                    <?php
                }
                $loc_html = ob_get_clean();
                echo apply_filters('jobsearch_jobdetail_loctext_html', $loc_html, $job_id, 'default_view');

                if ($jobsearch_job_posted_formated != '' && $job_views_publish_date == 'on' && $posted_date == 'yes') {
                    ?>
                    <li>
                        <i class="jobsearch-icon jobsearch-calendar"></i> <?php echo esc_html__('Post Date', 'wp-jobsearch') ?>
                        : <?php echo jobsearch_esc_html($jobsearch_job_posted_formated); ?>
                    </li>
                    <?php
                }
                $jobsearch_last_date_formated = '';
                if ($application_deadline != '') {
                    $jobsearch_last_date_formated = date_i18n(get_option('date_format'), ($application_deadline));
                }
                if (isset($jobsearch_last_date_formated) && !empty($jobsearch_last_date_formated) && $deadline_date == 'yes') {
                    ?>
                    <li>
                    <i class="careerfy-icon careerfy-calendar"></i> <?php echo esc_html__('Apply Before ', 'wp-jobsearch'); ?>
                    : <?php echo jobsearch_esc_html($jobsearch_last_date_formated); ?>
                    </li><?php
                }
                if ($job_salary != '' && $salary == 'yes') {
                    ?>
                    <li>
                        <i class="fa fa-money"></i> <?php printf(esc_html__('Salary: %s', 'wp-jobsearch'), $job_salary) ?>
                    </li>
                    <?php
                }

                if (isset($job_apply_type) && $job_apply_type != 'external' && $job_apps_count_switch == 'on' && $num_applicants == 'yes') {
                    ?>
                    <li>
                        <i class="jobsearch-icon jobsearch-summary"></i> <?php printf(esc_html__('Applications %s', 'wp-jobsearch'), $job_applicants_count) ?>
                    </li>
                    <?php
                }
                if ($job_views_count_switch == 'on' && $views == 'yes') {
                    ?>
                    <li>
                        <a><i class="jobsearch-icon jobsearch-view"></i> <?php echo esc_html__('View(s)', 'wp-jobsearch') ?> <?php echo absint($job_views_count); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
            <?php
            if ($job_shortlistbtn_switch == 'on' && $savejob_btn == 'yes') {
                // wrap in this this due to enquire arrange button style.
                $before_label = esc_html__('Shortlist', 'careerfy');
                $after_label = esc_html__('Shortlisted', 'careerfy');
                $book_mark_args = array(
                    'before_label' => $before_label,
                    'after_label' => $after_label,
                    'before_icon' => 'careerfy-icon careerfy-add-list',
                    'after_icon' => 'careerfy-icon careerfy-add-list',
                    'anchor_class' => 'careerfy-jobdetail-btn active',
                    'view' => 'job_detail_3',
                    'job_id' => $job_id,
                );
                do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args);
            }
            //
            if ($email_job == 'yes') {
                $popup_args = array(
                    'job_id' => $job_id,
                );
                do_action('jobsearch_job_send_to_email_filter', $popup_args);
            }
            //
            if ($social_share_allow == 'on' && $social_share == 'yes') {
                wp_enqueue_script('jobsearch-addthis');
                ?>
                <ul class="jobsearch-jobdetail-media">
                    <li><span><?php esc_html_e('Share:', 'wp-jobsearch') ?></span></li>
                    <li><a href="javascript:void(0);" data-original-title="facebook"
                           class="jobsearch-icon jobsearch-facebook-logo-in-circular-button-outlined-social-symbol addthis_button_facebook"></a>
                    </li>
                    <li><a href="javascript:void(0);" data-original-title="twitter"
                           class="jobsearch-icon jobsearch-twitter-circular-button addthis_button_twitter"></a>
                    </li>
                    <li><a href="javascript:void(0);" data-original-title="linkedin"
                           class="jobsearch-icon jobsearch-linkedin addthis_button_linkedin"></a>
                    </li>
                    <li><a href="javascript:void(0);" data-original-title="share_more"
                           class="jobsearch-icon jobsearch-plus addthis_button_compact"></a>
                    </li>
                </ul>
                <?php
            }
            $job_info_output = ob_get_clean();
            echo apply_filters('jobsearch_job_detail_content_info', $job_info_output, $job_id);
            ?>
        </div>
        <?php
        $html = ob_get_clean();
        echo $html;
    }



    protected function _content_template()
    {

    }

}
