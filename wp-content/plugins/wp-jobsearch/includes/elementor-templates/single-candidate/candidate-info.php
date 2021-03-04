<?php

namespace WP_JobsearchCandElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

use WP_Jobsearch\Candidate_Profile_Restriction;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleCandidateInfo extends Widget_Base
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
        return 'single-candidate-info';
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
        return __('Single Candidate Info', 'wp-jobsearch');
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
        return ['jobsearch-cand-single'];
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
                'label' => __('Candidate Info Settings', 'wp-jobsearch'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'job_title', [
                'label' => __('Candidate Title', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'cand_phone', [
                'label' => __('Phone Number', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
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
                'type' => Controls_Manager::SELECT2,
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
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'date_of_birth', [
                'label' => __('Age', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
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
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'member_since', [
                'label' => __('Member Since', 'wp-jobsearch'),
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
        $this->add_control(
            'cv_button', [
                'label' => __('Download CV Button', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'save_button', [
                'label' => __('Save Candidate Button', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->add_control(
            'whatsapp_btn', [
                'label' => __('Whatsapp Chat Button', 'wp-jobsearch'),
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
        $candidate_id = is_admin() ? jobsearch_candidate_id_elementor() : $post->ID;
        $cand_profile_restrict = new Candidate_Profile_Restriction;

        $atts = $this->get_settings_for_display();

        extract(shortcode_atts(array(
            'job_title' => '',
            'cand_phone' => '',
            'sector' => '',
            'salary' => '',
            'date_of_birth' => '',
            'member_since' => '',
            'location' => '',
            'social_share' => '',
            'cv_button' => '',
            'save_button' => '',
            'whatsapp_btn' => '',
        ), $atts));

        $candidates_reviews = isset($jobsearch_plugin_options['candidate_reviews_switch']) ? $jobsearch_plugin_options['candidate_reviews_switch'] : '';

        $all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
        $cand_det_full_address_switch = true;

        $locations_view_type = isset($jobsearch_plugin_options['cand_det_loc_listing']) ? $jobsearch_plugin_options['cand_det_loc_listing'] : '';
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
        if ($loc_view_country || $loc_view_state || $loc_view_city) {
            $cand_det_full_address_switch = false;
        }

        $candidate_obj = get_post($candidate_id);

        $candidate_join_date = isset($candidate_obj->post_date) ? $candidate_obj->post_date : '';

        $candidate_jobtitle = get_post_meta($candidate_id, 'jobsearch_field_candidate_jobtitle', true);
        $candidate_address = get_post_meta($candidate_id, 'jobsearch_field_location_address', true);
        if (function_exists('jobsearch_post_city_contry_txtstr')) {
            $candidate_address = jobsearch_post_city_contry_txtstr($candidate_id, $loc_view_country, $loc_view_state, $loc_view_city, $cand_det_full_address_switch);
        }

        $user_facebook_url = get_post_meta($candidate_id, 'jobsearch_field_user_facebook_url', true);
        $user_twitter_url = get_post_meta($candidate_id, 'jobsearch_field_user_twitter_url', true);
        $user_google_plus_url = get_post_meta($candidate_id, 'jobsearch_field_user_google_plus_url', true);
        $user_youtube_url = get_post_meta($candidate_id, 'jobsearch_field_user_youtube_url', true);
        $user_dribbble_url = get_post_meta($candidate_id, 'jobsearch_field_user_dribbble_url', true);
        $user_linkedin_url = get_post_meta($candidate_id, 'jobsearch_field_user_linkedin_url', true);

        $user_id = jobsearch_get_candidate_user_id($candidate_id);
        $user_obj = get_user_by('ID', $user_id);
        $user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
        $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);
        if ($cand_profile_restrict::cand_field_is_locked('profile_fields|display_name', 'detail_page')) {
            $user_displayname = $cand_profile_restrict::cand_restrict_display_name();
        }

        $cand_dob_switch = isset($jobsearch_plugin_options['cand_dob_switch']) ? $jobsearch_plugin_options['cand_dob_switch'] : 'on';
        $candidate_age = jobsearch_candidate_age($candidate_id);
        $candidate_salary_switch = isset($jobsearch_plugin_options['cand_salary_switch']) ? $jobsearch_plugin_options['cand_salary_switch'] : 'on';
        $candidate_salary = jobsearch_candidate_current_salary($candidate_id);
        //
        $membsectors_enable_switch = isset($jobsearch_plugin_options['usersector_onoff_switch']) ? $jobsearch_plugin_options['usersector_onoff_switch'] : '';
        $sectors_enable_switch = ($membsectors_enable_switch == 'on_cand' || $membsectors_enable_switch == 'on_both') ? 'on' : '';
        $sectors = wp_get_post_terms($candidate_id, 'sector');
        $candidate_sector = isset($sectors[0]->name) ? $sectors[0]->name : '';

        ob_start();
        ?>
        <div class="jobsearch_candidate_info">
            <h2>
                <a><?php echo apply_filters('jobsearch_candidate_detail_side_displayname', $user_displayname, $candidate_id) ?></a>
            </h2>
            <?php
            if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|job_title', 'detail_page') && $job_title == 'yes') {
                ob_start();
                ?>
                <p><?php echo jobsearch_esc_html($candidate_jobtitle) ?></p>
                <?php
                $candidate_jobtitle_html = ob_get_clean();
                echo apply_filters('jobsearch_candetail_jobtitle_html', $candidate_jobtitle_html, $candidate_id, 'view1');
            }
            $phone_number_switch = isset($jobsearch_plugin_options['cand_phone_switch']) ? $jobsearch_plugin_options['cand_phone_switch'] : '';
            if ($phone_number_switch != 'off' && $cand_phone == 'yes') {
                $candidate_phone = get_post_meta($candidate_id, 'jobsearch_field_user_phone', true);
                if ($candidate_phone != '' && !$cand_profile_restrict::cand_field_is_locked('profile_fields|phone')) {
                    echo '<p>' . sprintf(esc_html__('Phone: %s', 'wp-jobsearch'), $candidate_phone) . '</p>';
                }
            }
            if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|sector', 'detail_page') && $sector == 'yes') {
                if ($candidate_sector != '' && $sectors_enable_switch == 'on') {
                    echo '<p>' . sprintf(esc_html__('Sector: %s', 'wp-jobsearch'), apply_filters('jobsearch_gew_wout_anchr_sector_str_html', $candidate_sector, $candidate_id)) . '</p>';
                }
            }

            do_action('jobsearch_cand_detail_side_after_sector', $candidate_id);
            if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|salary', 'detail_page') && $salary == 'yes') {
                if ($candidate_salary != '' && $candidate_salary_switch == 'on') {
                    echo '<p>' . sprintf(esc_html__('Salary: %s', 'wp-jobsearch'), $candidate_salary) . '</p>';
                }
            }
            do_action('jobsearch_cand_detail_side_after_salary', $candidate_id);
            if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|date_of_birth', 'detail_page') && $date_of_birth == 'yes') {
                if ($candidate_age != '' && $cand_dob_switch != 'off') {
                    echo apply_filters('jobsearch_candidate_detail_page_age_html', '<p>' . sprintf(esc_html__('(Age: %s years)', 'wp-jobsearch'), $candidate_age) . '</p>');
                }
            }
            if (!$cand_profile_restrict::cand_field_is_locked('address_defields', 'detail_page') && $candidate_address != '' && $all_location_allow == 'on' && $location == 'yes') {
                ?>
                <span><?php echo jobsearch_esc_html($candidate_address) ?></span>
                <?php
            }
            if ($candidate_join_date != '' && $member_since == 'yes') { ?>
                <small><?php printf(esc_html__('Member Since, %s', 'wp-jobsearch'), date_i18n(get_option('date_format'), strtotime($candidate_join_date))) ?></small>
                <?php
            }

            if (!$cand_profile_restrict::cand_field_is_locked('socialicons_defields', 'detail_page') && $social_share == 'yes') {
                $cand_alow_fb_smm = isset($jobsearch_plugin_options['cand_alow_fb_smm']) ? $jobsearch_plugin_options['cand_alow_fb_smm'] : '';
                $cand_alow_twt_smm = isset($jobsearch_plugin_options['cand_alow_twt_smm']) ? $jobsearch_plugin_options['cand_alow_twt_smm'] : '';
                $cand_alow_gplus_smm = isset($jobsearch_plugin_options['cand_alow_gplus_smm']) ? $jobsearch_plugin_options['cand_alow_gplus_smm'] : '';
                $cand_alow_linkd_smm = isset($jobsearch_plugin_options['cand_alow_linkd_smm']) ? $jobsearch_plugin_options['cand_alow_linkd_smm'] : '';
                $cand_alow_dribbb_smm = isset($jobsearch_plugin_options['cand_alow_dribbb_smm']) ? $jobsearch_plugin_options['cand_alow_dribbb_smm'] : '';
                $candidate_social_mlinks = isset($jobsearch_plugin_options['candidate_social_mlinks']) ? $jobsearch_plugin_options['candidate_social_mlinks'] : '';
                if (!empty($candidate_social_mlinks) || ($cand_alow_fb_smm == 'on' || $cand_alow_twt_smm == 'on' || $cand_alow_gplus_smm == 'on' || $cand_alow_linkd_smm == 'on' || $cand_alow_dribbb_smm == 'on')) {
                    ob_start();
                    ?>
                    <ul>
                        <?php
                        if ($user_facebook_url != '' && $cand_alow_fb_smm == 'on') {
                            ?>
                            <li>
                                <a href="<?php echo jobsearch_esc_html(esc_url($user_facebook_url)) ?>"
                                   data-original-title="facebook"
                                   class="jobsearch-icon jobsearch-facebook-logo"></a></li>
                            <?php
                        }
                        if ($user_twitter_url != '' && $cand_alow_twt_smm == 'on') {
                            ?>
                            <li>
                                <a href="<?php echo jobsearch_esc_html(esc_url($user_twitter_url)) ?>"
                                   target="_blank"
                                   data-original-title="twitter"
                                   class="jobsearch-icon jobsearch-twitter-logo"></a></li>
                            <?php
                        }
                        if ($user_linkedin_url != '' && $cand_alow_linkd_smm == 'on') { ?>
                            <li>
                                <a href="<?php echo jobsearch_esc_html(esc_url($user_linkedin_url)) ?>"
                                   target="_blank"
                                   data-original-title="linkedin"
                                   class="jobsearch-icon jobsearch-linkedin-button"></a></li>
                            <?php
                        }
                        if ($user_dribbble_url != '' && $cand_alow_dribbb_smm == 'on') {
                            ?>
                            <li>
                                <a href="<?php echo jobsearch_esc_html(esc_url($user_dribbble_url)) ?>"
                                   target="_blank"
                                   data-original-title="dribbble"
                                   class="jobsearch-icon jobsearch-dribbble-logo"></a></li>
                            <?php
                        }
                        if (!empty($candidate_social_mlinks)) {
                            if (isset($candidate_social_mlinks['title']) && is_array($candidate_social_mlinks['title'])) {
                                $field_counter = 0;
                                foreach ($candidate_social_mlinks['title'] as $field_title_val) {
                                    $field_random = rand(10000000, 99999999);
                                    $field_icon_styles = '';
                                    $field_icon = isset($candidate_social_mlinks['icon'][$field_counter]) ? $candidate_social_mlinks['icon'][$field_counter] : '';
                                    $field_icon_group = isset($candidate_social_mlinks['icon_group'][$field_counter]) ? $candidate_social_mlinks['icon_group'][$field_counter] : '';
                                    if ($field_icon_group == '') {
                                        $field_icon_group = 'default';
                                    }
                                    $field_icon_clr = isset($candidate_social_mlinks['icon_clr'][$field_counter]) ? $candidate_social_mlinks['icon_clr'][$field_counter] : '';
                                    if ($field_icon_clr != '') {
                                        $field_icon_styles .= 'color: ' . $field_icon_clr . ';';
                                    }
                                    $field_icon_bgclr = isset($candidate_social_mlinks['icon_bgclr'][$field_counter]) ? $candidate_social_mlinks['icon_bgclr'][$field_counter] : '';
                                    if ($field_icon_bgclr != '') {
                                        $field_icon_styles .= ' background-color: ' . $field_icon_bgclr . ';';
                                    }
                                    $cand_dynm_social = get_post_meta($candidate_id, 'jobsearch_field_dynm_social' . $field_counter, true);
                                    if ($field_title_val != '' && $cand_dynm_social != '') {
                                        ?>
                                        <li>
                                            <a href="<?php echo esc_url($cand_dynm_social) ?>"
                                               target="_blank" <?php echo($field_icon_styles != '' ? 'style="' . $field_icon_styles . '"' : '') ?>
                                               class="<?php echo jobsearch_esc_html($field_icon) ?>"></a>
                                        </li>
                                        <?php
                                    }
                                    $field_counter++;
                                }
                            }
                        }
                        ?>
                    </ul>
                    <?php
                    $cand_socilinks = ob_get_clean();
                    echo apply_filters('jobsearch_cand_detail_socilinks_html', $cand_socilinks, $candidate_id);
                }
            }

            if ($cv_button == 'yes') {
                do_action('jobsearch_download_candidate_cv_btn', array('id' => $candidate_id));
            }

            if ($save_button == 'yes') {
                jobsearch_add_employer_resume_to_list_btn(array('id' => $candidate_id));
            }

            if ($whatsapp_btn == 'yes') {
                jobsearch_candidate_detail_whatsapp_btn($candidate_id);
            }

            //
            $cand_chat_args = array('candidate_id' => $candidate_id);

            //do_action('jobsearch_chat_with_candidate', $cand_chat_args);
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
