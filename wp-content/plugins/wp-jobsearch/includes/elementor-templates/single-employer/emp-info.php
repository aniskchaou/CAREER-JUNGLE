<?php

namespace Wp_JobsearchElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleEmpInfo extends Widget_Base
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
        return 'single-emp-info';
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
        return __('Single Employer Info', 'wp-jobsearch');
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
        return ['jobsearch-emp-single'];
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
                'label' => __('Employer Info Settings', 'wp-jobsearch'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'emp_title', [
                'label' => __('Employer Title', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'reviews_switch_control', [
                'label' => __('Reviews', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __('Also refer to the jobsearch options to check the switch', 'wp-jobsearch'),
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );

        $this->add_control(
            'location', [
                'label' => __('Location', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'emp_email_switch', [
                'label' => __('Email', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __('Email will only show if employer email is added.', 'wp-jobsearch'),
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'emp_phone_switch_control', [
                'label' => __('Employer Phone', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __('Also refer to the jobsearch options to check the switch', 'wp-jobsearch'),
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'review_btn', [
                'label' => __('Review Button', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'follow_btn', [
                'label' => __('Follow Button', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'emp_web_switch_control', [
                'label' => __('Employer Web', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __('Also refer to the jobsearch options to check the switch', 'wp-jobsearch'),
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'email_job', [
                'label' => __('Email Job Button', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
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
                'type' => Controls_Manager::SELECT2,
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
        $employer_id = is_admin() ? jobsearch_employer_id_elementor() : $post->ID;
        $atts = $this->get_settings_for_display();

        extract(shortcode_atts(array(
            'emp_title' => '',
            'reviews_switch_control' => '',
            'emp_phone_switch_control' => '',
            'location' => '',
            'review_btn' => '',
            'emp_web_switch_control' => '',
            'social_share' => '',
            'emp_email_switch' => '',
            'follow_btn' => '',
        ), $atts));

        $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
        $emp_det_full_address_switch = true;
        $locations_view_type = isset($jobsearch_plugin_options['emp_det_loc_listing']) ? $jobsearch_plugin_options['emp_det_loc_listing'] : '';

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


        $plugin_default_view = isset($jobsearch_plugin_options['jobsearch-default-page-view']) ? $jobsearch_plugin_options['jobsearch-default-page-view'] : 'full';
        $plugin_default_view_with_str = '';
        if ($plugin_default_view == 'boxed') {

            $plugin_default_view_with_str = isset($jobsearch_plugin_options['jobsearch-boxed-view-width']) && $jobsearch_plugin_options['jobsearch-boxed-view-width'] != '' ? $jobsearch_plugin_options['jobsearch-boxed-view-width'] : '1140px';
            if ($plugin_default_view_with_str != '' && !wp_is_mobile()) {
                $plugin_default_view_with_str = ' style="width:' . $plugin_default_view_with_str . '"';
            }
        }

        $reviews_switch = isset($jobsearch_plugin_options['reviews_switch']) ? $jobsearch_plugin_options['reviews_switch'] : '';
        $employer_views_count = get_post_meta($employer_id, "jobsearch_employer_views_count", true);
//
        $user_facebook_url = get_post_meta($employer_id, 'jobsearch_field_user_facebook_url', true);
        $user_twitter_url = get_post_meta($employer_id, 'jobsearch_field_user_twitter_url', true);
        $user_google_plus_url = get_post_meta($employer_id, 'jobsearch_field_user_google_plus_url', true);
        $user_youtube_url = get_post_meta($employer_id, 'jobsearch_field_user_youtube_url', true);
        $user_dribbble_url = get_post_meta($employer_id, 'jobsearch_field_user_dribbble_url', true);
        $user_linkedin_url = get_post_meta($employer_id, 'jobsearch_field_user_linkedin_url', true);

        $membsectors_enable_switch = isset($jobsearch_plugin_options['usersector_onoff_switch']) ? $jobsearch_plugin_options['usersector_onoff_switch'] : '';
        $sectors_enable_switch = ($membsectors_enable_switch == 'on_emp' || $membsectors_enable_switch == 'on_both') ? 'on' : '';
        $tjobs_posted_switch = isset($jobsearch_plugin_options['empjobs_posted_count']) ? $jobsearch_plugin_options['empjobs_posted_count'] : '';
        $totl_views_switch = isset($jobsearch_plugin_options['emptotl_views_count']) ? $jobsearch_plugin_options['emptotl_views_count'] : '';
//
        $emp_phone_switch = isset($jobsearch_plugin_options['employer_phone_field']) ? $jobsearch_plugin_options['employer_phone_field'] : '';
        $emp_web_switch = isset($jobsearch_plugin_options['employer_web_field']) ? $jobsearch_plugin_options['employer_web_field'] : '';
        $emp_foundate_switch = isset($jobsearch_plugin_options['employer_founded_date']) ? $jobsearch_plugin_options['employer_founded_date'] : '';

        $employer_obj = get_post($employer_id);
        $employer_content = $employer_obj->post_content;

        $employer_address = get_post_meta($employer_id, 'jobsearch_field_location_address', true);

        if (function_exists('jobsearch_post_city_contry_txtstr')) {
            $employer_address = jobsearch_post_city_contry_txtstr($employer_id, $loc_view_country, $loc_view_state, $loc_view_city, $emp_det_full_address_switch);
        }

        $locations_lat = get_post_meta($employer_id, 'jobsearch_field_location_lat', true);
        $locations_lng = get_post_meta($employer_id, 'jobsearch_field_location_lng', true);
        $employer_phone = get_post_meta($employer_id, 'jobsearch_field_user_phone', true);
        $user_id = jobsearch_get_employer_user_id($employer_id);
        $user_obj = get_user_by('ID', $user_id);
        $user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
        $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);

        wp_enqueue_script('isotope-min');
        ob_start();
        ?>

        <div class="elementorsec-emp-info">
            <?php
            if ($reviews_switch == 'on' && $reviews_switch_control == 'yes') {
                $post_avg_review_args = array(
                    'post_id' => $employer_id,
                );
                do_action('jobsearch_post_avg_rating', $post_avg_review_args);
            }

            ?>
            <?php
            ob_start();
            if ($emp_title == 'yes') { ?>
                <h2><?php echo($user_displayname) ?></h2>
            <?php } ?>

            <?php
            $title_html = ob_get_clean();
            echo apply_filters('jobsearch_emp_detail_maintitle_html', $title_html, $employer_id, 'view1');
            ?>
            <ul class="jobsearch-jobdetail-options">
                <?php
                if ((!empty($employer_address) || ($locations_lat != '' && $locations_lng != '')) && $all_location_allow == 'on' && $location == 'yes') {
                    $view_map_loc = urlencode($employer_address);
                    if ($locations_lat != '' && $locations_lng != '') {
                        $view_map_loc = urlencode($locations_lat . ',' . $locations_lng);
                    }
                    $google_mapurl = 'https://www.google.com/maps/search/' . $view_map_loc;
                    ?>
                    <li>
                        <?php
                        if (!empty($employer_address)) { ?>
                            <i class="fa fa-map-marker"></i> <?php echo jobsearch_esc_html($employer_address) ?>
                            <a href="<?php echo jobsearch_esc_html($google_mapurl) ?>"
                               target="_blank"
                               class="jobsearch-jobdetail-view"><?php esc_html_e('View on Map', 'wp-jobsearch') ?></a>
                        <?php } ?>
                    </li>
                    <?php
                }
                if (isset($user_obj->user_url) && $user_obj->user_url != '' && $emp_web_switch == 'on' && jobsearch_employer_info_div_visible('weburl') && $emp_web_switch_control == 'yes') {
                    $user_url = apply_filters('jobsearch_employer_info_encoding', $user_obj->user_url, 'weburl');
                    ob_start();
                    ?>
                    <li>
                        <i class="jobsearch-icon jobsearch-internet"></i> <a
                                href="<?php echo esc_url($user_url) ?>"
                                target="_blank"><?php echo jobsearch_esc_html(esc_url($user_url)) ?></a>
                    </li>
                    <?php
                    $website_html = ob_get_clean();
                    echo apply_filters('jobsearch_emp_detail_website_html', $website_html);
                }
                if (isset($user_obj->user_email) && $user_obj->user_email != '' && jobsearch_employer_info_div_visible('email') && $emp_email_switch == 'yes') {
                    $user_email = apply_filters('jobsearch_employer_info_encoding', $user_obj->user_email, 'email');
                    $tr_email = sprintf(__('<a href="mailto: %s">Email: %s</a>', 'wp-jobsearch'), $user_email, $user_email);
                    ?>
                    <li>
                        <i class="jobsearch-icon jobsearch-mail"></i> <?php echo wp_kses($tr_email, array('a' => array('href' => array(), 'target' => array(), 'title' => array()))) ?>
                    </li>
                    <?php
                }
                if ($employer_phone != '' && $emp_phone_switch != 'off' && jobsearch_employer_info_div_visible('phone') && $emp_phone_switch_control == 'yes') {
                    $user_phone = apply_filters('jobsearch_employer_info_encoding', $employer_phone, 'phone');
                    $user_phone = jobsearch_esc_html($user_phone);
                    ob_start();
                    ?>
                    <li>
                        <i class="jobsearch-icon jobsearch-technology"></i> <?php printf(esc_html__('Telephone: %s', 'wp-jobsearch'), $user_phone) ?>
                    </li>
                    <?php
                    $tele_output = ob_get_clean();
                    echo apply_filters('jobsearch_emp_detail_tele_num_html', $tele_output, $employer_id);
                }
                ?>
            </ul>
            <?php
            if ($reviews_switch == 'on' && $review_btn == 'yes') {
                $add_review_args = array(
                    'post_id' => $employer_id,
                );
                do_action('jobsearch_add_review_btn', $add_review_args);
            }
            //
            $follow_btn_args = array(
                'employer_id' => $employer_id,
                'before_label' => esc_html__('Follow', 'wp-jobsearch'),
                'after_label' => esc_html__('Followed', 'wp-jobsearch'),
                'ext_class' => 'jobsearch-employerdetail-btn',
                'view' => 'detail_view1',
            );
            if ($follow_btn == 'yes') {
                do_action('jobsearch_employer_followin_btn', $follow_btn_args);
            }
            //
            $emp_alow_fb_smm = isset($jobsearch_plugin_options['emp_alow_fb_smm']) ? $jobsearch_plugin_options['emp_alow_fb_smm'] : '';
            $emp_alow_twt_smm = isset($jobsearch_plugin_options['emp_alow_twt_smm']) ? $jobsearch_plugin_options['emp_alow_twt_smm'] : '';
            $emp_alow_gplus_smm = isset($jobsearch_plugin_options['emp_alow_gplus_smm']) ? $jobsearch_plugin_options['emp_alow_gplus_smm'] : '';
            $emp_alow_linkd_smm = isset($jobsearch_plugin_options['emp_alow_linkd_smm']) ? $jobsearch_plugin_options['emp_alow_linkd_smm'] : '';
            $emp_alow_dribbb_smm = isset($jobsearch_plugin_options['emp_alow_dribbb_smm']) ? $jobsearch_plugin_options['emp_alow_dribbb_smm'] : '';
            $employer_social_mlinks = isset($jobsearch_plugin_options['employer_social_mlinks']) ? $jobsearch_plugin_options['employer_social_mlinks'] : '';
            if ($social_share == 'yes') {
                if (!empty($employer_social_mlinks) || ($emp_alow_fb_smm == 'on' || $emp_alow_twt_smm == 'on' || $emp_alow_gplus_smm == 'on' || $emp_alow_linkd_smm == 'on' || $emp_alow_dribbb_smm == 'on')) {
                    ob_start();
                    ?>
                    <ul class="jobsearch-jobdetail-media jobsearch-add-space">
                        <?php echo self::social_link_heading($user_facebook_url, $emp_alow_fb_smm, $user_twitter_url, $emp_alow_twt_smm, $user_linkedin_url, $emp_alow_gplus_smm, $user_google_plus_url, $emp_alow_linkd_smm, $user_dribbble_url, $emp_alow_dribbb_smm); ?>
                        <?php
                        if ($user_facebook_url != '' && $emp_alow_fb_smm == 'on') { ?>
                            <li><a href="<?php echo esc_url($user_facebook_url) ?>" target="_blank"
                                   data-original-title="facebook"
                                   class="jobsearch-icon jobsearch-facebook-logo"></a></li>
                            <?php
                        }
                        if ($user_twitter_url != '' && $emp_alow_twt_smm == 'on') {
                            ?>
                            <li><a href="<?php echo esc_url($user_twitter_url) ?>" target="_blank"
                                   data-original-title="twitter"
                                   class="jobsearch-icon jobsearch-twitter-logo"></a></li>
                            <?php
                        }
                        if ($user_linkedin_url != '' && $emp_alow_linkd_smm == 'on') {
                            ?>
                            <li><a href="<?php echo esc_url($user_linkedin_url) ?>" target="_blank"
                                   data-original-title="linkedin"
                                   class=""><i class="jobsearch-icon jobsearch-linkedin-button"></i></a>
                            </li>
                            <?php
                        }
                        if ($user_google_plus_url != '' && $emp_alow_gplus_smm == 'on') {
                            ?>
                            <li><a href="<?php echo esc_url($user_google_plus_url) ?>" target="_blank"
                                   data-original-title="google-plus"
                                   class="jobsearch-icon jobsearch-google-plus-logo-button"></a></li>
                            <?php
                        }
                        if ($user_dribbble_url != '' && $emp_alow_dribbb_smm == 'on') {
                            ?>
                            <li><a href="<?php echo esc_url($user_dribbble_url) ?>" target="_blank"
                                   data-original-title="dribbble"
                                   class="jobsearch-icon jobsearch-dribbble-logo"></a></li>
                        <?php } ?>
                    </ul>
                    <?php
                    $emp_socilinks = ob_get_clean();
                    echo apply_filters('jobsearch_emp_detail_socilinks_html', $emp_socilinks, $employer_id);
                }
            }
            ?>
        </div>

        <?php
        $html = ob_get_clean();
        echo $html;
    }

    private static function social_link_heading($user_facebook_url, $emp_alow_fb_smm, $user_twitter_url, $emp_alow_twt_smm, $user_linkedin_url, $emp_alow_gplus_smm, $user_google_plus_url, $emp_alow_linkd_smm, $user_dribbble_url, $emp_alow_dribbb_smm)
    {
        if ($user_facebook_url != '' && $emp_alow_fb_smm == 'on' || $user_twitter_url != '' && $emp_alow_twt_smm == 'on' || $user_linkedin_url != '' && $emp_alow_gplus_smm == 'on' || $user_google_plus_url != '' && $emp_alow_linkd_smm == 'on' || $user_dribbble_url != '' && $emp_alow_dribbb_smm == 'on') {
            ob_start();
            ?>
            <li><span><?php esc_html_e('Social Links:', 'wp-jobsearch') ?></span></li>
            <?php return ob_get_clean(); ?>
        <?php }
    }

    protected function _content_template()
    {

    }

}
