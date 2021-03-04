<?php

namespace CareerfyElementor\Widgets;

use WP_Jobsearch\Candidate_Profile_Restriction;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

/**
 * @since 1.1.0
 */
class CandidateListings extends Widget_Base
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
        return 'candidate-listings';
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
        return __('Candidate Listings', 'careerfy-frame');
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
        return 'fa fa-list';
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
        return ['wp-jobsearch'];
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
        global $jobsearch_plugin_options, $jobsearch_gdapi_allocation;
        $all_locations_type = isset($jobsearch_plugin_options['all_locations_type']) ? $jobsearch_plugin_options['all_locations_type'] : '';
        $ads_management_switch = isset($jobsearch_plugin_options['ads_management_switch']) ? $jobsearch_plugin_options['ads_management_switch'] : '';
        $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : 500;

        $categories = get_terms(array(
            'taxonomy' => 'sector',
            'hide_empty' => false,
        ));
        $cate_array = array(esc_html__("Select Sector", "careerfy-frame") => '');
        if (is_array($categories) && sizeof($categories) > 0) {
            foreach ($categories as $category) {
                $cate_array[$category->name] = $category->slug;
            }
        }

        $jobsearch_cand_cus_fields = get_option("jobsearch_custom_field_candidate");
        $job_cand_field_arr = array();
        if (isset($jobsearch_cand_cus_fields) && !empty($jobsearch_cand_cus_fields) && sizeof($jobsearch_cand_cus_fields) > 0) {
            foreach ($jobsearch_cand_cus_fields as $key => $value) {
                $job_cand_field_arr[$key] = $value['label'];
            }
        }

        $this->start_controls_section(
            'general',
            [
                'label' => __('General', 'careerfy-frame'),
                'tab_1' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'candidate_view',
            [
                'label' => __('Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'view-default',
                'options' => [
                    'view-default' => __('Style 1', 'careerfy-frame'),
                    'view-grid' => __('Style 2', 'careerfy-frame'),
                    'view-classic' => __('Style 3', 'careerfy-frame'),
                    'view-modern' => __('Style 4', 'careerfy-frame'),
                    'view-fancy' => __('Style 5', 'careerfy-frame'),
                    'view-fancy-2' => __('Style 6', 'careerfy-frame'),
                    'view-fancy-3' => __('Style 7', 'careerfy-frame'),
                    'view-fancy-4' => __('Style 8', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'candidate_cat',
            [
                'label' => __('Sector', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'options' => $cate_array,
            ]
        );

        $this->add_control(
            'display_per_page',
            [
                'label' => __('Candidate Founds with display counts', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Display the per page candidates count at top of the listing.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'first_btn_color',
            [
                'label' => __('First Button Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'description' => __("", "careerfy-frame"),
                'condition' => [
                    'candidate_view' => 'view-fancy-3',
                ],

            ]
        );
        $this->add_control(
            'second_btn_color',
            [
                'label' => __('Second Button Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'description' => __("", "careerfy-frame"),
                'condition' => [
                    'candidate_view' => 'view-fancy-3',
                ],

            ]
        );
        $this->add_control(
            'candidate_loc_listing',
            [
                'label' => __('Locations in listing', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'options' => [
                    'country' => __('Country', 'careerfy-frame'),
                    'state' => __('State', 'careerfy-frame'),
                    'city' => __('City', 'careerfy-frame'),
                ],
                'multiple' => true,
                'default' => ['country', 'state'],
                'description' => __("Select which type of location in listing. If nothing select then full address will display.", "careerfy-frame"),
            ]
        );
        $this->add_control(
            'candidate_sort_by',
            [
                'label' => __('Sort by Fields', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'description' => __("Results search sorting section switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'candidate_excerpt',
            [
                'label' => __('Excerpt Length', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '20',
                'description' => __("Set the number of words you want to show for excerpt.", "careerfy-frame"),
            ]
        );

        $this->add_control(
            'candidate_order',
            [
                'label' => __('Order', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'DESC',
                'description' => __("If you set Featured Only 'Yes' then only Featured jobs will show.", "careerfy-frame"),
                'options' => [
                    'DESC' => __('Descending', 'careerfy-frame'),
                    'ASC' => __('Ascending', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'candidate_orderby',
            [
                'label' => __('Orderby', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'date',
                'description' => __("If you set Featured Only 'Yes' then only Featured jobs will show.", "careerfy-frame"),
                'options' => [
                    'date' => __('Date', 'careerfy-frame'),
                    'title' => __('Title', 'careerfy-frame'),
                    'promote_profile' => __('Promote Profile', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'candidate_pagination',
            [
                'label' => __('Pagination', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'description' => __("Choose yes if you want to show pagination for job items.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'candidate_per_page',
            [
                'label' => __('Items per Page', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '10',
                'description' => __("Set number that how much candidates you want to show per page. Leave it blank for all candidates on a single page.", "careerfy-frame"),
            ]
        );

        $this->end_controls_section();
        if (class_exists('JobSearch_plugin')) {
            if ($ads_management_switch == 'on') {
                $groups_value = isset($jobsearch_plugin_options['ad_banner_groups']) ? $jobsearch_plugin_options['ad_banner_groups'] : '';

                $groups_select_opts = array(esc_html__('Select Group', 'careerfy-frame') => '');
                if (isset($groups_value['group_title']) && !empty($groups_value['group_title'])) {
                    $b_group_counter = 0;
                    $group_codes = isset($groups_value['group_code']) ? $groups_value['group_code'] : '';
                    foreach ($groups_value['group_title'] as $group_title) {
                        $group_code = isset($group_codes[$b_group_counter]) ? $group_codes[$b_group_counter] : '';
                        $groups_select_opts[$group_code] = $group_title;
                        $b_group_counter++;
                    }
                }

                $this->start_controls_section(
                    'ad-banner-setting',
                    [
                        'label' => __('AD Banner Settings', 'careerfy-frame'),
                        'tab' => Controls_Manager::TAB_CONTENT,
                    ]
                );

                $this->add_control(
                    'candidate_ad_banners',
                    [
                        'label' => __('Ad Banners', 'careerfy-frame'),
                        'type' => Controls_Manager::SELECT2,
                        'default' => 'no',
                        'description' => __("Show/hide ad banners in candidate listings.", "careerfy-frame"),
                        'options' => [
                            'yes' => __('Yes', 'careerfy-frame'),
                            'no' => __('No', 'careerfy-frame'),
                        ],
                    ]
                );
                $this->add_control(
                    'candidate_ad_after_list',
                    [
                        'label' => __('Ad after list count', 'careerfy-frame'),
                        'type' => Controls_Manager::TEXT,
                        'default' => '5',
                        'description' => __("Put number. After how many candidates list an ad banner will show. You can also add comma seprated numbers i.e. 2,5,7", "careerfy-frame"),
                    ]
                );

                $this->add_control(
                    'candidate_ads_group',
                    [
                        'label' => __('Banners Group', 'careerfy-frame'),
                        'type' => Controls_Manager::SELECT2,
                        'default' => 'no',
                        'description' => __("Select Ad Banners Group.", "careerfy-frame"),
                        'options' => $groups_select_opts,
                    ]
                );
                $this->end_controls_section();
            }
        }
        $this->start_controls_section(
            'Filter-settings',
            [
                'label' => __('Filters Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'candidate_filters',
            [
                'label' => __('Filters', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Jobs searching filters switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'candidate_filters_count',
            [
                'label' => __('Filters Count', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Show result counts in front of every filter.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'candidate_filters_sortby',
            [
                'label' => __('Filters Sort by', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'default',
                'description' => __("Show result counts in front of every filter.", "careerfy-frame"),
                'options' => [
                    'default' => __('Default', 'careerfy-frame'),
                    'asc' => __('Ascending', 'careerfy-frame'),
                    'desc' => __('Descending', 'careerfy-frame'),
                    'alpha' => __('Alphabetical', 'careerfy-frame'),
                    'count' => __('Highest Count', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'candidate_filters_date',
            [
                'label' => __('Date Posted', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Jobs searching filters 'Date Posted' switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'candidate_filters_date_collapse',
            [
                'label' => __('Date Posted Filter Collapse', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'candidate_filters_sector',
            [
                'label' => __('Sector', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Candidates searching filters 'Sector' switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'candidate_filters_sector_collapse',
            [
                'label' => __('Sector Filter Collapse', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'map-settings',
            [
                'label' => __('Map Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'cand_top_map',
            [
                'label' => __('Top map switch', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'description' => __("Candidates top map switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'cand_top_map_height',
            [
                'label' => __('Map Height', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'description' => __("Candidates top map height.", "careerfy-frame"),
            ]
        );
        $this->add_control(
            'cand_top_map_zoom',
            [
                'label' => __('Map Zoom', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '8',
                'description' => __("Candidates top map zoom.", "careerfy-frame"),
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'top-search',
            [
                'label' => __('Top Search', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'cand_top_search',
            [
                'label' => __('Candidates Search Bar', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => '',
                'description' => __("Candidates top search bar switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'cand_top_search_view',
            [
                'label' => __('Candidates Search Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'simple',
                'description' => __("Candidates top search bar switch.", "careerfy-frame"),
                'options' => [
                    'simple' => __('Simple', 'careerfy-frame'),
                    'advance' => __('Advance Search', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'cand_top_search_radius',
            [
                'label' => __('Top Search Radius', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Candidates top search radius.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'cand_top_search_title',
            [
                'label' => __('Candidates Title, Keywords, or Phrase', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => '',
                'description' => __("Enable/Disable search keyword field.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'cand_top_search_location',
            [
                'label' => __('Location', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Enable/Disable location field.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        if ($sectors_enable_switch == 'on') {
            $this->add_control(
                'cand_top_search_sector',
                [
                    'label' => __('Sector', 'careerfy-frame'),
                    'type' => Controls_Manager::SELECT2,
                    'default' => 'yes',
                    'description' => __("Enable/Disable Sector Dropdown field.", "careerfy-frame"),
                    'options' => [
                        'yes' => __('Yes', 'careerfy-frame'),
                        'no' => __('No', 'careerfy-frame'),
                    ],
                ]
            );
        }
        $this->add_control(
            'top_search_autofill',
            [
                'label' => __('AutoFill Search Box', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Enable/Disable autofill in search keyword field.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'custom-fields',
            [
                'label' => __('Custom Fields', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'candidate_custom_fields_switch',
            [
                'label' => __('Custom Fields', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Enable/Disable autofill in search keyword field.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'candidate_elem_custom_fields',
            [
                'label' => __('Select Fields', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'description' => __("", "careerfy-frame"),
                'options' => $job_cand_field_arr,
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        global $jobsearch_plugin_options, $first_btn_color, $second_btn_color, $jobsearch__options;
        $atts = $this->get_settings_for_display();
        extract(shortcode_atts(array(
            'candidate_cat' => '',
            'candidate_view' => 'view-default',
            'candidate_sort_by' => '',
            'candidate_excerpt' => '20',
            'candidate_order' => 'DESC',
            'candidate_orderby' => 'date',
            'candidate_pagination' => 'yes',
            'candidate_per_page' => '3',
            'candidate_type' => '',
            'first_btn_color' => '',
            'second_btn_color' => '',
            // extra fields
            'candidate_filters' => 'yes',
            'candidate_custom_fields_switch' => '',
            'candidate_elem_custom_fields' => '',
        ), $atts));

        //
        if (empty($atts) && !is_array($atts)) {
            $atts = array();
        }
        if (!isset($atts['candidate_cat'])) {
            $atts['candidate_cat'] = '';
        }
        if (!isset($atts['candidate_view'])) {
            $atts['candidate_view'] = 'view-default';
        }
        if (!isset($atts['candidate_sort_by'])) {
            $atts['candidate_sort_by'] = '';
        }
        if (!isset($atts['candidate_excerpt'])) {
            $atts['candidate_excerpt'] = '20';
        }
        if (!isset($atts['candidate_order'])) {
            $atts['candidate_order'] = 'DESC';
        }
        if (!isset($atts['candidate_orderby'])) {
            $atts['candidate_orderby'] = 'date';
        }
        if (!isset($atts['candidate_pagination'])) {
            $atts['candidate_pagination'] = 'yes';
        }
        if (!isset($atts['candidate_per_page'])) {
            $atts['candidate_per_page'] = '10';
        }
        if (!isset($atts['candidate_type'])) {
            $atts['candidate_type'] = '';
        }
        if (!isset($atts['first_btn_color'])) {
            $atts['first_btn_color'] = '';
        }
        if (!isset($atts['second_btn_color'])) {
            $atts['second_btn_color'] = '';
        }
        if (!isset($atts['candidate_filters'])) {
            $atts['candidate_filters'] = 'yes';
        }
        if (!isset($atts['candidate_filters_date'])) {
            $atts['candidate_filters_date'] = 'yes';
        }
        if (!isset($atts['candidate_filters_sector'])) {
            $atts['candidate_filters_sector'] = 'yes';
        }
        if (!isset($atts['candidate_loc_listing'])) {
            $atts['candidate_loc_listing'] = 'country,city';
        }
        if (!isset($atts['candidate_custom_fields_switch'])) {
            $atts['candidate_custom_fields_switch'] = 'no';
        }
        if (!isset($atts['candidate_elem_custom_fields'])) {
            $atts['candidate_elem_custom_fields'] = '';
        }


        $view_candidate = true;
        $restrict_candidates = isset($jobsearch_plugin_options['restrict_candidates_list']) ? $jobsearch_plugin_options['restrict_candidates_list'] : '';

        $view_cand_type = 'fully';
        $emp_cvpbase_restrictions = isset($jobsearch_plugin_options['emp_cv_pkgbase_restrictions_list']) ? $jobsearch_plugin_options['emp_cv_pkgbase_restrictions_list'] : '';
        $restrict_cand_type = isset($jobsearch_plugin_options['restrict_candidates_for_users']) ? $jobsearch_plugin_options['restrict_candidates_for_users'] : '';
        if ($emp_cvpbase_restrictions == 'on' && $restrict_cand_type != 'only_applicants') {
            $view_cand_type = 'partly';
        }

        $restrict_candidates_for_users = isset($jobsearch_plugin_options['restrict_candidates_for_users']) ? $jobsearch_plugin_options['restrict_candidates_for_users'] : '';

        $is_employer = false;
        if ($restrict_candidates == 'on' && $view_cand_type == 'fully') {
            $view_candidate = false;

            if (is_user_logged_in()) {
                $cur_user_id = get_current_user_id();
                $cur_user_obj = wp_get_current_user();
                if (jobsearch_user_isemp_member($cur_user_id)) {
                    $employer_id = jobsearch_user_isemp_member($cur_user_id);
                    $cur_user_id = jobsearch_get_employer_user_id($employer_id);
                } else {
                    $employer_id = jobsearch_get_user_employer_id($cur_user_id);
                }
                $ucandidate_id = jobsearch_get_user_candidate_id($cur_user_id);
                $employer_dbstatus = get_post_meta($employer_id, 'jobsearch_field_employer_approved', true);
                if ($employer_id > 0 && $employer_dbstatus == 'on') {
                    $is_employer = true;
                    if ($restrict_candidates_for_users == 'register_resume') {
                        $user_cv_pkg = jobsearch_employer_first_subscribed_cv_pkg($cur_user_id);
                        if (!$user_cv_pkg) {
                            $user_cv_pkg = jobsearch_allin_first_pkg_subscribed($cur_user_id, 'cvs');
                        }
                        if ($user_cv_pkg) {
                            $view_candidate = true;
                        }
                    } else {
                        $view_candidate = true;
                    }
                } else if (in_array('administrator', (array)$cur_user_obj->roles)) {
                    $view_candidate = true;
                } else if ($restrict_candidates_for_users == 'register_empcand' && ($ucandidate_id > 0 || $employer_id > 0)) {
                    $view_candidate = true;
                }
            }
        }

        ob_start();

        if ($view_candidate === false) {
            $restrict_img = isset($jobsearch_plugin_options['candidate_restrict_img']) ? $jobsearch_plugin_options['candidate_restrict_img'] : '';
            $restrict_img_url = isset($restrict_img['url']) ? $restrict_img['url'] : '';

            $restrict_cv_pckgs = isset($jobsearch_plugin_options['restrict_cv_packages']) ? $jobsearch_plugin_options['restrict_cv_packages'] : '';
            $restrict_msg = isset($jobsearch_plugin_options['restrict_cand_msg']) && $jobsearch_plugin_options['restrict_cand_msg'] != '' ? $jobsearch_plugin_options['restrict_cand_msg'] : esc_html__('The Page is Restricted only for Subscribed Employers', 'wp-jobsearch');
            ?>
            <div class="jobsearch-column-12">
                <div class="restrict-candidate-sec">
                    <img src="<?php echo($restrict_img_url) ?>" alt="">
                    <h2><?php echo($restrict_msg) ?></h2>

                    <?php
                    if ($is_employer) { ?>
                        <p><?php esc_html_e('Please buy a C.V package to view this candidate.', 'wp-jobsearch') ?></p>
                        <?php
                    } else if (is_user_logged_in()) {
                        ?>
                        <p><?php esc_html_e('You are not an employer. Only an Employer can view a candidate.', 'wp-jobsearch') ?></p>
                        <?php
                    } else {
                        ?>
                        <p><?php esc_html_e('If you are employer just login to view this candidate or buy a C.V package to download His Resume.', 'wp-jobsearch') ?></p>
                        <?php
                    }
                    if (is_user_logged_in()) {
                        ?>
                        <div class="login-btns">
                            <a href="<?php echo wp_logout_url(home_url('/')); ?>"><i
                                        class="jobsearch-icon jobsearch-logout"></i><?php esc_html_e('Logout', 'wp-jobsearch') ?>
                            </a>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="login-btns">
                            <a href="javascript:void(0);" class="jobsearch-open-signin-tab"><i
                                        class="jobsearch-icon jobsearch-user"></i><?php esc_html_e('Login', 'wp-jobsearch') ?>
                            </a>
                            <a href="javascript:void(0);" class="jobsearch-open-register-tab"><i
                                        class="jobsearch-icon jobsearch-plus"></i><?php esc_html_e('Become an Employer', 'wp-jobsearch') ?>
                            </a>
                        </div>
                        <?php
                        if (!empty($restrict_cv_pckgs) && is_array($restrict_cv_pckgs) && $restrict_candidates_for_users == 'register_resume') { ?>
                            <div class="jobsearch-box-title">
                                <span><?php esc_html_e('OR', 'wp-jobsearch') ?></span>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <?php
                if (!empty($restrict_cv_pckgs) && is_array($restrict_cv_pckgs) && $restrict_candidates_for_users == 'register_resume') {
                    wp_enqueue_script('jobsearch-packages-scripts');
                    ?>
                    <div class="cv-packages-section">
                        <div class="packages-title">
                            <h2><?php esc_html_e('Buy any CV Packages to get started', 'wp-jobsearch') ?></h2></div>
                        <?php
                        ob_start();
                        ?>
                        <div class="jobsearch-row">
                            <?php
                            foreach ($restrict_cv_pckgs as $restrict_cv_pckg) {
                                $cv_pkg_obj = $restrict_cv_pckg != '' ? get_page_by_path($restrict_cv_pckg, 'OBJECT', 'package') : '';
                                if (is_object($cv_pkg_obj) && isset($cv_pkg_obj->ID)) {
                                    $cv_pkg_id = $cv_pkg_obj->ID;
                                    $pkg_type = get_post_meta($cv_pkg_id, 'jobsearch_field_charges_type', true);
                                    $pkg_price = get_post_meta($cv_pkg_id, 'jobsearch_field_package_price', true);

                                    $num_of_cvs = get_post_meta($cv_pkg_id, 'jobsearch_field_num_of_cvs', true);
                                    $pkg_exp_dur = get_post_meta($cv_pkg_id, 'jobsearch_field_package_expiry_time', true);
                                    $pkg_exp_dur_unit = get_post_meta($cv_pkg_id, 'jobsearch_field_package_expiry_time_unit', true);

                                    $pkg_exfield_title = get_post_meta($cv_pkg_id, 'jobsearch_field_package_exfield_title', true);
                                    $pkg_exfield_val = get_post_meta($cv_pkg_id, 'jobsearch_field_package_exfield_val', true);
                                    $pkg_exfield_status = get_post_meta($cv_pkg_id, 'jobsearch_field_package_exfield_status', true);
                                    ?>
                                    <div class="jobsearch-column-4">
                                        <div class="jobsearch-classic-priceplane">
                                            <h2><?php echo get_the_title($cv_pkg_id) ?></h2>
                                            <div class="jobsearch-priceplane-section">
                                                <?php
                                                if ($pkg_type == 'paid') {
                                                    echo '<span>' . jobsearch_get_price_format($pkg_price) . ' <small>' . esc_html__('only', 'wp-jobsearch') . '</small></span>';
                                                } else {
                                                    echo '<span>' . esc_html__('Free', 'wp-jobsearch') . '</span>';
                                                }
                                                ?>
                                            </div>
                                            <div class="grab-classic-priceplane">
                                                <ul>
                                                    <?php
                                                    if (!empty($pkg_exfield_title)) {
                                                        $_exf_counter = 0;
                                                        foreach ($pkg_exfield_title as $_exfield_title) {
                                                            $_exfield_val = isset($pkg_exfield_val[$_exf_counter]) ? $pkg_exfield_val[$_exf_counter] : '';
                                                            $_exfield_status = isset($pkg_exfield_status[$_exf_counter]) ? $pkg_exfield_status[$_exf_counter] : '';
                                                            if ($_exfield_val != '') {
                                                                ?>
                                                                <li<?php echo($_exfield_status == 'active' ? ' class="active"' : '') ?>>
                                                                    <i class="jobsearch-icon jobsearch-check-square"></i> <?php echo $_exfield_title . ' ' . $_exfield_val ?>
                                                                </li>
                                                                <?php
                                                            }
                                                            $_exf_counter++;
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                                <?php if (is_user_logged_in()) { ?>
                                                    <a href="javascript:void(0);"
                                                       class="jobsearch-classic-priceplane-btn jobsearch-subscribe-cv-pkg"
                                                       data-id="<?php echo($cv_pkg_id) ?>"><?php esc_html_e('Get Started', 'wp-jobsearch') ?> </a>
                                                    <span class="pkg-loding-msg" style="display:none;"></span>
                                                <?php } else { ?>
                                                    <a href="javascript:void(0);"
                                                       class="jobsearch-classic-priceplane-btn jobsearch-open-signin-tab"><?php esc_html_e('Get Started', 'wp-jobsearch') ?> </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <?php
                        $pkgs_html = ob_get_clean();
                        echo apply_filters('jobsearch_restrict_candidate_pakgs_html', $pkgs_html, $restrict_cv_pckgs);
                        ?>
                    </div>
                <?php } ?>
            </div>
        <?php } else {
            wp_enqueue_style('datetimepicker-style');
            wp_enqueue_script('datetimepicker-script');
            wp_enqueue_script('jquery-ui');
            wp_enqueue_script('jobsearch-candidate-functions-script');
            do_action('jobsearch_notes_frontend_modal_popup');
            $candidate_short_counter = isset($atts['candidate_counter']) && $atts['candidate_counter'] != '' ? ($atts['candidate_counter']) : rand(123, 9999); // for shortcode counter
            if (false === ($candidate_view = jobsearch_get_transient_obj('jobsearch_candidate_view' . $candidate_short_counter))) {
                $candidate_view = isset($atts['candidate_view']) ? $atts['candidate_view'] : '';
            }


            /*
         * jobs listing element selected custom fields array
         */

            $job_custom_fields_switch = isset($atts['candidate_custom_fields_switch']) ? $atts['candidate_custom_fields_switch'] : 'no';
            $job_elem_custom_fields = isset($atts['candidate_elem_custom_fields']) ? $atts['candidate_elem_custom_fields'] : '';

            $selected_fields = array();
            if (isset($job_elem_custom_fields) && !empty($job_elem_custom_fields)) {
                $selected_fields = $job_elem_custom_fields;
            }
            $candidate_cus_field_arr = array();
            if ($job_custom_fields_switch == 'yes' && !empty($selected_fields)) {
                $jobsearch_job_cus_fields = get_option("jobsearch_custom_field_candidate");
                if (isset($jobsearch_job_cus_fields) && !empty($jobsearch_job_cus_fields) && sizeof($jobsearch_job_cus_fields) > 0) {
                    foreach ($jobsearch_job_cus_fields as $key => $value) {
                        foreach ($selected_fields as $selected_key => $selected_val) {
                            if ($key == $selected_val) {
                                $candidate_cus_field_arr[$key] = $value;
                            }
                        }
                    }
                }
            }


            /*
             * END jobs listing element selected custom fields array
             */

            jobsearch_set_transient_obj('jobsearch_candidate_view' . $candidate_short_counter, $candidate_view);
            $candidate_map_counter = rand(10000000, 99999999);
            $element_candidate_footer = isset($atts['candidate_footer']) ? $atts['candidate_footer'] : '';
            $element_candidate_map_position = isset($atts['candidate_map_position']) ? $atts['candidate_map_position'] : '';
            $map_change_class = '';
            if ($candidate_view == 'map') {
                if ($element_candidate_footer == 'yes') {
                    echo '<script>';
                    echo 'jQuery(document).ready(function () {'
                        . 'jQuery("footer#footer").hide();'
                        . '});';
                    echo '</script>';
                }
            }
            wp_reset_query();
            do_action('candidate_checks_enquire_lists_submit');
            do_action('jobsearch_candidate_compare_sidebar');
            do_action('jobsearch_candidate_enquiries_sidebar');
            $page_url = get_permalink(get_the_ID());
            ?>
            <div class="wp-dp-candidate-content"
                 id="wp-dp-candidate-content-<?php echo esc_html($candidate_short_counter); ?>">
                <div class="dev-map-class-changer<?php echo($map_change_class); ?>">
                    <div id="Candidate-content-<?php echo esc_html($candidate_short_counter); ?>">
                        <?php
                        $content = '';
                        $candidate_arg = array(
                            'candidate_short_counter' => $candidate_short_counter,
                            'atts' => $atts,
                            'content' => $content,
                            'candidate_map_counter' => $candidate_map_counter,
                            'page_url' => $page_url,
                            'page_id' => get_the_ID(),
                            'custom_fields' => $candidate_cus_field_arr,
                        );
                        $this->jobsearch_candidates_content($candidate_arg);
                        ?>
                    </div>
                </div>
            </div>
            <?php
        }
        $html = ob_get_clean();
        echo $html;

    }

    public function candidate_layout_switcher_fields($atts, $candidate_short_counter, $view = '', $frc_view = false)
    {
        $counter = isset($atts['candidate_counter']) && $atts['candidate_counter'] != '' ? $atts['candidate_counter'] : '';
        $transient_view = jobsearch_get_transient_obj('jobsearch_candidate_view' . $counter);

        if ($frc_view == true) {
            $view = $view;
        } else {
            if (false === ($view = jobsearch_get_transient_obj('jobsearch_candidate_view' . $counter))) {
                $view = isset($atts['candidate_view']) ? $atts['candidate_view'] : '';
            }
        }
        if ((isset($atts['candidate_layout_switcher']) && $atts['candidate_layout_switcher'] != 'no')) {

            if (isset($atts['candidate_layout_switcher_view']) && !empty($atts['candidate_layout_switcher_view'])) {
                $candidate_layout_switcher_views = array(
                    'grid' => esc_html__('grid', 'wp-jobsearch'),
                    'list' => esc_html__('list', 'wp-jobsearch'),
                );
                ?>
                <ul class="candidates-views-switcher-holder">
                    <li><?php echo esc_html__('jobsearch_view_candidates_by_switcher'); ?></li>
                    <?php
                    $element_candidate_layout_switcher_view = explode(',', $atts['candidate_layout_switcher_view']);

                    if (!empty($element_candidate_layout_switcher_view) && is_array($element_candidate_layout_switcher_view)) {
                        $views_counter = 0;
                        foreach ($element_candidate_layout_switcher_view as $single_layout_view) {
                            $case_for_list = $single_layout_view;
                            if ($single_layout_view == 'list') {
                                $case_for_list = 'listed';
                            }
                            if ($single_layout_view == 'grid-medern') {
                                $case_for_list = 'grid-medern';
                            }
                            switch ($case_for_list) {
                                case 'grid':
                                    $icon = '<i class="icon-th-large"></i> ';
                                    $icon .= esc_html__('grid', 'wp-jobsearch');
                                    $view_class = 'grid-view';
                                    break;
                                case 'listed':
                                    $icon = '<i class="icon-th-list"></i> ';
                                    $icon .= esc_html__('list', 'wp-jobsearch');
                                    $view_class = 'list-view';
                                    break;
                                case 'grid-medern':
                                    $icon = '<i class="icon-th"></i> ';
                                    $icon .= esc_html__('modern grid', 'wp-jobsearch');
                                    $view_class = 'grid-modern-view';
                                    break;
                                case 'grid-classic':
                                    $icon = '<i class="icon-grid_on"></i> ';
                                    $icon .= esc_html__('classic grid', 'wp-jobsearch');
                                    $view_class = 'grid-classic-view';
                                    break;
                                case 'grid-default':
                                    $icon = '<i class="icon-menu4"></i> ';
                                    $icon .= esc_html__('default grid', 'wp-jobsearch');
                                    $view_class = 'grid-default-view';
                                    break;
                                case 'list-modern':
                                    $icon = '<i class="icon-list5"></i> ';
                                    $icon .= esc_html__('modern list', 'wp-jobsearch');
                                    $view_class = 'list-modern-view';
                                    break;
                                default:
                                    $icon = '<i class="icon-th-list"></i> ';
                                    $icon .= esc_html__('list', 'wp-jobsearch');
                                    $view_class = 'list-view';
                            }
                            if (empty($view) && $views_counter === 0) {
                                ?>
                                <li><a href="javascript:void(0);" class="active"><i
                                                class="icon-th-list"></i><?php echo esc_html($candidate_layout_switcher_views[$single_layout_view]); ?>
                                    </a></li>
                            <?php } else {
                                $view_type = '';
                                ?>
                                <li class="<?php echo esc_html($view_class); ?>"><a
                                            href="javascript:void(0);" <?php if ($view == $single_layout_view) echo 'class="active"'; ?> <?php if ($view != $single_layout_view) { ?> onclick="jobsearch_candidate_view_switch('<?php echo esc_html($single_layout_view) ?>', '<?php echo esc_html($candidate_short_counter); ?>', '<?php echo esc_html($counter); ?>', '<?php echo esc_html($view_type); ?>');"<?php } ?>><?php echo force_balance_tags($icon); ?></a>
                                </li>
                                <?php
                            }
                            $views_counter++;
                        }
                    }
                    ?>
                </ul>
                <?php
            }
        }
    }

    public function candidate_search_sort_fields($atts, $candidate_sort_by, $candidate_short_counter, $view = '', $candidate_totnum = '', $candidate_per_page = '')
    {
        global $jobsearch_form_fields;

        $counter = isset($atts['candidate_counter']) && $atts['candidate_counter'] != '' ? $atts['candidate_counter'] : '';
        $transient_view = jobsearch_get_transient_obj('jobsearch_candidate_view' . $counter);
        $view = isset($transient_view) && $transient_view != '' ? $transient_view : $view;

        $candidate_type_slug = isset($_REQUEST['candidate_type']) ? $_REQUEST['candidate_type'] : '';
        $candidate_type_text = $candidate_type_slug;
        if (isset($candidate_type_slug) && !empty($candidate_type_slug) && $candidate_type_slug != 'all') {
            if ($post = get_page_by_path($candidate_type_slug, OBJECT, 'candidate-type')) {
                $id = $post->ID;
                $candidate_type_text = get_the_title($id);
            }
        }

        $view_type = '';

        if ((isset($atts['candidate_sort_by']) && $atts['candidate_sort_by'] != 'no')) {

            echo '<div class="sortfiltrs-contner">';
            //
            echo apply_filters('jobsearch_cand_listin_before_top_jobfounds_html', '', $candidate_totnum, $candidate_short_counter, $atts);
            //
            $paging_var = 'candidate_page';
            $pagination = isset($atts['candidate_pagination']) ? $atts['candidate_pagination'] : 'no';
            $paging_args = array(
                'total_posts' => $candidate_totnum,
                'candidate_per_page' => $candidate_per_page,
                'paging_var' => $paging_var,
                'show_pagination' => $pagination,
                'candidate_short_counter' => $candidate_short_counter,
            );
            echo apply_filters('jobsearch_cand_listin_before_sort_orders', '', $paging_args, $atts);
            ?>
            <div class="jobsearch-filterable jobsearch-filter-sortable">
                <?php
                ob_start();
                ?>
                <h2>
                    <?php
                    echo absint($candidate_totnum) . '&nbsp;';
                    if ($candidate_totnum > 1) {
                        echo esc_html__('Candidates Found', 'careerfy-frame');
                    } else {
                        echo esc_html__('Candidate Found', 'careerfy-frame');
                    }

                    do_action('jobsearch_cand_listin_sh_after_jobs_found', $candidate_totnum, $candidate_short_counter, $atts);
                    ?>
                </h2>
                <?php
                $foundemps_html = ob_get_clean();
                echo apply_filters('jobsearch_cand_listin_top_jobfounds_html', $foundemps_html, $candidate_totnum, $candidate_short_counter, $atts);
                ?>
                <ul class="jobsearch-sort-section">
                    <?php
                    do_action('jobsearch_cand_listin_sh_before_topsort_items', $candidate_short_counter, $atts);
                    ?>
                    <li>
                        <i class="jobsearch-icon jobsearch-sort"></i>
                        <div class="jobsearch-filterable-select">
                            <?php
                            $sortby_option = array('recent' => esc_html__('Most Recent', 'careerfy-frame'),
                                'approved' => esc_html__('Approved', 'careerfy-frame'),
                                'alphabetical' => esc_html__('Alphabet Order', 'careerfy-frame'),
                                'most_viewed' => esc_html__('Most Viewed', 'careerfy-frame')
                            );
                            $sortby_option = apply_filters('candidate_hunt_candidates_sort_options', $sortby_option);
                            $cs_opt_array = array(
                                'cus_id' => '',
                                'cus_name' => 'sort-by',
                                'force_std' => $candidate_sort_by,
                                'desc' => '',
                                'classes' => 'selectize-select',
                                'ext_attr' => ' onchange="jobsearch_candidate_content_load(\'' . esc_js($candidate_short_counter) . '\')" placeholder="' . esc_html__('Most Recent', 'careerfy-frame') . '"',
                                'options' => $sortby_option,
                            );
                            $jobsearch_form_fields->select_field($cs_opt_array);
                            ?>
                        </div>
                    </li>
                    <li>
                        <i class="jobsearch-icon jobsearch-sort"></i>
                        <div class="jobsearch-filterable-select">
                            <?php
                            $paging_options[""] = '' . esc_html__("Records Per Page", "careerfy-frame");
                            $paging_options["10"] = '10 ' . esc_html__("Per Page", "careerfy-frame");
                            $paging_options["20"] = '20 ' . esc_html__("Per Page", "careerfy-frame");
                            $paging_options["30"] = '30 ' . esc_html__("Per Page", "careerfy-frame");
                            $paging_options["50"] = '50 ' . esc_html__("Per Page", "careerfy-frame");
                            $paging_options["70"] = '70 ' . esc_html__("Per Page", "careerfy-frame");
                            $paging_options["100"] = '100 ' . esc_html__("Per Page", "careerfy-frame");
                            $paging_options["200"] = '200 ' . esc_html__("Per Page", "careerfy-frame");
                            $cs_opt_array = array(
                                'cus_id' => '',
                                'cus_name' => 'per-page',
                                'force_std' => $candidate_per_page,
                                'desc' => '',
                                'classes' => 'sort-records-per-page',
                                'ext_attr' => ' onchange="jobsearch_candidate_content_load(\'' . esc_js($candidate_short_counter) . '\')" placeholder="' . esc_html__('Records Per Page', 'careerfy-frame') . '"',
                                'options' => apply_filters('jobsearch_candlistin_topsort_paginum_options', $paging_options),
                            );

                            $jobsearch_form_fields->select_field($cs_opt_array);
                            ?>
                        </div>
                    </li>
                </ul>
                <?php
                $this->candidate_layout_switcher_fields($atts, $candidate_short_counter, $view = '');
                ?>
            </div>
            <!-- filter-moving -->
            <?php
            //
            echo apply_filters('jobsearch_cand_listin_after_sort_orders_html', '', $candidate_totnum, $candidate_short_counter, $atts);
            //
            echo '</div>';

            $adv_filter_toggle = isset($_REQUEST['adv_filter_toggle']) ? $_REQUEST['adv_filter_toggle'] : 'false';

            $args_more = array(
                'candidate_type' => $atts['candidate_type'],
                'candidate_filters' => $atts['candidate_filters'],
                'jobsearch_map_position' => isset($atts['jobsearch_map_position']) && $atts['jobsearch_map_position'] != '' ? ($atts['jobsearch_map_position']) : 'right',
                'candidate_short_counter' => $candidate_short_counter,
                'candidate_sort_by' => $atts['candidate_sort_by'],
                'adv_filter_toggle' => $adv_filter_toggle,
            );
            do_action('jobsearch_search_more_filter', $args_more);
            $jobsearch_form_fields->input_hidden_field(
                array(
                    'simple' => true,
                    'classes' => "adv_filter_toggle",
                    'cust_name' => 'adv_filter_toggle',
                    'std' => $adv_filter_toggle,
                )
            );
        }
    }

    public function jobsearch_candidate_pagination_callback($args)
    {
        global $jobsearch_form_fields;
        $total_posts = '';
        $candidate_per_page = '5';
        $paging_var = 'candidate_page';
        $show_pagination = 'yes';
        $candidate_short_counter = '';
        extract($args);
        $view_type = '';

        $ajax_filter = (isset($_REQUEST['ajax_filter']) || isset($_REQUEST['search_type'])) ? 'true' : 'false';

        if ($show_pagination <> 'yes') {
            return;
        } else if ($total_posts <= $candidate_per_page) {
            return;
        } else {
            if (!isset($_REQUEST[$paging_var])) {
                $_REQUEST[$paging_var] = '';
            }
            $html = '';
            $dot_pre = '';
            $dot_more = '';
            $total_page = 0;
            if ($total_posts > 0 && $candidate_per_page > 0) {
                $total_page = ceil($total_posts / $candidate_per_page);
            }
            $paged_id = 1;
            if (isset($_REQUEST[$paging_var]) && $_REQUEST[$paging_var] != '') {
                $paged_id = $_REQUEST[$paging_var];
            }
            $loop_start = $paged_id - 2;

            $loop_end = $paged_id + 2;

            if ($paged_id < 3) {

                $loop_start = 1;

                if ($total_page < 5)
                    $loop_end = $total_page;
                else
                    $loop_end = 5;
            } else if ($paged_id >= $total_page - 1) {

                if ($total_page < 5)
                    $loop_start = 1;
                else
                    $loop_start = $total_page - 4;

                $loop_end = $total_page;
            }
            $html .= $jobsearch_form_fields->input_hidden_field(
                array(
                    'cus_id' => $paging_var . '-' . $candidate_short_counter,
                    'cus_name' => $paging_var,
                    'std' => '',
                )
            );
            $html .= '<div class="jobsearch-pagination-blog"><ul class="jobsearch-page-numbers">';
            if ($paged_id > 1) {
                $html .= '<li>'
                    . '<a class="prev jobsearch-page-numbers" onclick="jobsearch_candidate_pagenation_ajax(\'' . $paging_var . '\', \'' . ($paged_id - 1) . '\', \'' . ($candidate_short_counter) . '\', \'' . ($ajax_filter) . '\', \'' . ($view_type) . '\');" href="javascript:void(0);">';
                $html .= '<span><i class="jobsearch-icon jobsearch-arrows4"><i></span>'
                    . '</a>'
                    . '</li>';
            } else {

            }

            if ($paged_id > 3 && $total_page > 5) {
                $html .= '<li><a class="jobsearch-page-numbers" onclick="jobsearch_candidate_pagenation_ajax(\'' . $paging_var . '\', \'' . (1) . '\', \'' . ($candidate_short_counter) . '\', \'' . ($ajax_filter) . '\', \'' . ($view_type) . '\');" href="javascript:void(0);">';
                $html .= '1</a></li>';
            }
            if ($paged_id > 4 && $total_page > 6) {
                $html .= '<li class="disabled"><span>. . .</span></li>';
            }

            if ($total_page > 1) {

                for ($i = $loop_start; $i <= $loop_end; $i++) {

                    if ($i <> $paged_id) {

                        $html .= '<li><a class="jobsearch-page-numbers" onclick="jobsearch_candidate_pagenation_ajax(\'' . $paging_var . '\', \'' . ($i) . '\', \'' . ($candidate_short_counter) . '\', \'' . ($ajax_filter) . '\', \'' . ($view_type) . '\');" href="javascript:void(0);">';
                        $html .= $i . '</a></li>';
                    } else {
                        $html .= '<li><span class="jobsearch-page-numbers current">' . $i . '</span></li>';
                    }
                }
            }
            if ($loop_end <> $total_page && $loop_end <> $total_page - 1) {
                $html .= '<li class="no-border"><a>. . .</a></li>';
            }
            if ($loop_end <> $total_page) {
                $html .= '<li><a class="jobsearch-page-numbers" onclick="jobsearch_candidate_pagenation_ajax(\'' . $paging_var . '\', \'' . ($total_page) . '\', \'' . ($candidate_short_counter) . '\', \'' . ($ajax_filter) . '\', \'' . ($view_type) . '\');" href="javascript:void(0);">';
                $html .= $total_page . '</a></li>';
            }
            if ($total_posts > 0 && $candidate_per_page > 0 && $paged_id < ($total_posts / $candidate_per_page)) {
                $html .= '<li>'
                    . '<a class="next jobsearch-page-numbers" onclick="jobsearch_candidate_pagenation_ajax(\'' . $paging_var . '\', \'' . ($paged_id + 1) . '\', \'' . ($candidate_short_counter) . '\', \'' . ($ajax_filter) . '\', \'' . ($view_type) . '\');" href="javascript:void(0);">';
                $html .= '<span><i class="jobsearch-icon jobsearch-arrows4"></i></span>'
                    . '</a>'
                    . '</li>';
            } else {

            }
            $html .= "</ul></div>";

            echo force_balance_tags($html);

        }
    }

    public function get_filter_arg($candidate_short_counter = '', $exclude_meta_key = '')
    {
        global $jobsearch_post_candidate_types;
        $filter_arr = array();
        $posted = '';
        $default_date_time_formate = 'd-m-Y H:i:s';
        $current_timestamp = current_time('timestamp');
        if (isset($_REQUEST['posted'])) {
            $posted = $_REQUEST['posted'];
        }
        if ($posted != '') {
            $lastdate = '';
            $now = '';
            if ($posted == 'lasthour') {
                $now = date($default_date_time_formate, $current_timestamp);
                $lastdate = date($default_date_time_formate, strtotime('-1 hours', $current_timestamp));
            } elseif ($posted == 'last24') {
                $now = date($default_date_time_formate, $current_timestamp);
                $lastdate = date($default_date_time_formate, strtotime('-24 hours', $current_timestamp));
            } elseif ($posted == '7days') {
                $now = date($default_date_time_formate, $current_timestamp);
                $lastdate = date($default_date_time_formate, strtotime('-7 days', $current_timestamp));
            } elseif ($posted == '14days') {
                $now = date($default_date_time_formate, $current_timestamp);
                $lastdate = date($default_date_time_formate, strtotime('-14 days', $current_timestamp));
            } elseif ($posted == '30days') {
                $now = date($default_date_time_formate, $current_timestamp);
                $lastdate = date($default_date_time_formate, strtotime('-30 days', $current_timestamp));
            }
            if ($lastdate != '' && $now != '') {
                $filter_arr[] = array(
                    'key' => 'post_date',
                    'value' => strtotime($lastdate),
                    'compare' => '>=',
                );
            }
        }
        // custom field array for filteration from custom field module
        $filter_arr = apply_filters('jobsearch_custom_fields_load_filter_array_html', 'candidate', $filter_arr, $exclude_meta_key);
        return $filter_arr;
    }

    public function get_candidate_id_by_filter($left_filter_arr)
    {
        global $wpdb;
        $meta_post_ids_arr = '';
        $candidate_id_condition = '';

        if (isset($left_filter_arr) && !empty($left_filter_arr)) {
            $meta_post_ids_arr = jobsearch_get_query_whereclase_by_array($left_filter_arr);

            // if no result found in filtration
            if (empty($meta_post_ids_arr)) {
                $meta_post_ids_arr = array(0);
            }
            if (isset($_REQUEST['loc_polygon_path']) && $_REQUEST['loc_polygon_path'] != '' && $meta_post_ids_arr != '') {
                $meta_post_ids_arr = $this->candidate_polygon_filter($_REQUEST['loc_polygon_path'], $meta_post_ids_arr);
                if (empty($meta_post_ids_arr)) {
                    $meta_post_ids_arr = '';
                }
            }
            $ids = $meta_post_ids_arr != '' ? implode(",", $meta_post_ids_arr) : '0';
            $candidate_id_condition = " ID in (" . $ids . ") AND ";
        }

        $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $candidate_id_condition . " post_type='candidate' AND post_status='publish'");

        if (empty($post_ids)) {
            $post_ids = array(0);
        }
        return $post_ids;
    }

    public function candidate_location_filter($all_post_ids)
    {

        global $sitepress;

        $radius = isset($_REQUEST['loc_radius']) ? $_REQUEST['loc_radius'] : '';
        $search_type = isset($_REQUEST['location_location1']) ? $_REQUEST['location_location1'] : '';

        $location_rslt = $all_post_ids;

        if (isset($_REQUEST['location']) && $_REQUEST['location'] != '') {
            $location_condition_arr = array(
                'relation' => 'OR',
            );

            $location_condition_arr[] = array(
                'key' => 'jobsearch_field_location_address',
                'value' => isset($_REQUEST['location']) ? stripslashes($_REQUEST['location']) : '',
                'compare' => 'LIKE',
            );
            $location_condition_arr[] = array(
                'key' => 'jobsearch_field_location_location1',
                'value' => isset($_REQUEST['location']) ? stripslashes($_REQUEST['location']) : '',
                'compare' => 'LIKE',
            );
            $location_condition_arr[] = array(
                'key' => 'jobsearch_field_location_location2',
                'value' => isset($_REQUEST['location']) ? stripslashes($_REQUEST['location']) : '',
                'compare' => 'LIKE',
            );
            $location_condition_arr[] = array(
                'key' => 'jobsearch_field_location_location3',
                'value' => isset($_REQUEST['location']) ? stripslashes($_REQUEST['location']) : '',
                'compare' => 'LIKE',
            );
            $location_condition_arr[] = array(
                'key' => 'jobsearch_field_location_location4',
                'value' => isset($_REQUEST['location']) ? stripslashes($_REQUEST['location']) : '',
                'compare' => 'LIKE',
            );

            //$element_filters_arr[] = $location_condition_arr;

            $args_count = array(
                'posts_per_page' => "-1",
                'post_type' => 'candidate',
                'post_status' => 'publish',
                'fields' => 'ids', // only load ids
                'meta_query' => array(
                    $location_condition_arr,
                ),
            );

            if (!empty($all_post_ids)) {
                $args_count['post__in'] = $all_post_ids;
            }
            $location_rslt = get_posts($args_count);
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $trans_able_options = $sitepress->get_setting('custom_posts_sync_option', array());
                if (empty($location_rslt) && isset($trans_able_options['candidate']) && $trans_able_options['candidate'] == '2') {
                    $sitepress_def_lang = $sitepress->get_default_language();
                    $sitepress_curr_lang = $sitepress->get_current_language();
                    $sitepress->switch_lang($sitepress_def_lang, true);

                    $location = isset($_REQUEST['location']) ? $_REQUEST['location'] : '';
                    if ($location != '') {
                        $loc_taxnomy = get_term_by('slug', $location, 'job-location');
                        if (is_object($loc_taxnomy) && isset($loc_taxnomy->slug)) {
                            $args_count['meta_query'][0][0]['value'] = $loc_taxnomy->slug;
                            $args_count['meta_query'][0][1]['value'] = $loc_taxnomy->slug;
                            $args_count['meta_query'][0][2]['value'] = $loc_taxnomy->slug;
                            $args_count['meta_query'][0][3]['value'] = $loc_taxnomy->slug;
                            $args_count['meta_query'][0][4]['value'] = $loc_taxnomy->slug;
                        }
                    }

                    $location_query = new \WP_Query($args_count);
                    wp_reset_postdata();
                    $location_rslt = $location_query->posts;

                    $sitepress->switch_lang($sitepress_curr_lang, true);
                }
            }
            if (empty($location_rslt)) {
                $location_rslt = array(0);
            }
        } else if (isset($_REQUEST['location_location1']) || isset($_REQUEST['location_location2']) || isset($_REQUEST['location_location3']) || isset($_REQUEST['location_location4'])) {

            $location_condition_arr = array(
                'relation' => 'AND',
            );
            if (isset($_REQUEST['location_location1']) && $_REQUEST['location_location1'] != '' && isset($_REQUEST['location_location2']) && $_REQUEST['location_location2'] == 'other-cities') {
                $location_condition_arr[] = array(
                    'key' => 'jobsearch_field_location_location1',
                    'value' => isset($_REQUEST['location_location1']) ? $_REQUEST['location_location1'] : '',
                    'compare' => '!=',
                );
            } else {
                if (isset($_REQUEST['location_location1']) && $_REQUEST['location_location1'] != '') {
                    $location_condition_arr[] = array(
                        'key' => 'jobsearch_field_location_location1',
                        'value' => isset($_REQUEST['location_location1']) ? $_REQUEST['location_location1'] : '',
                        'compare' => 'LIKE',
                    );
                }
                if (isset($_REQUEST['location_location2']) && $_REQUEST['location_location2'] != '') {
                    $location_condition_arr[] = array(
                        'key' => 'jobsearch_field_location_location2',
                        'value' => isset($_REQUEST['location_location2']) ? $_REQUEST['location_location2'] : '',
                        'compare' => 'LIKE',
                    );
                }
                if (isset($_REQUEST['location_location3']) && $_REQUEST['location_location3'] != '') {
                    $location_condition_arr[] = array(
                        'key' => 'jobsearch_field_location_location3',
                        'value' => isset($_REQUEST['location_location3']) ? $_REQUEST['location_location3'] : '',
                        'compare' => 'LIKE',
                    );
                }
                if (isset($_REQUEST['location_location4']) && $_REQUEST['location_location4'] != '') {
                    $location_condition_arr[] = array(
                        'key' => 'jobsearch_field_location_location4',
                        'value' => isset($_REQUEST['location_location4']) ? $_REQUEST['location_location4'] : '',
                        'compare' => 'LIKE',
                    );
                }
            }

            //$element_filters_arr[] = $location_condition_arr;

            $args_count = array(
                'posts_per_page' => "-1",
                'post_type' => 'candidate',
                'post_status' => 'publish',
                'fields' => 'ids', // only load ids
                'meta_query' => array(
                    $location_condition_arr,
                ),
            );

            if (!empty($all_post_ids)) {
                $args_count['post__in'] = $all_post_ids;
            }
            $location_rslt = get_posts($args_count);
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $trans_able_options = $sitepress->get_setting('custom_posts_sync_option', array());
                if (empty($location_rslt) && isset($trans_able_options['candidate']) && $trans_able_options['candidate'] == '2') {
                    $sitepress_def_lang = $sitepress->get_default_language();
                    $sitepress_curr_lang = $sitepress->get_current_language();
                    $sitepress->switch_lang($sitepress_def_lang, true);

                    $location_query = new \WP_Query($args_count);
                    wp_reset_postdata();
                    $location_rslt = $location_query->posts;

                    $sitepress->switch_lang($sitepress_curr_lang, true);
                }
            }
            if (empty($location_rslt)) {
                $location_rslt = array(0);
            }
            //print_r($location_rslt);
        }
        if ($radius > 0) {
            return $all_post_ids;
        }
        return $location_rslt;
    }

    public function location_radius_filter_ids()
    {

        global $wpdb, $jobsearch_plugin_options;

        if (isset($_REQUEST['loc_radius']) && $_REQUEST['loc_radius'] > 0 && isset($_REQUEST['location'])) {

            $default_selctd_contry = isset($jobsearch_plugin_options['restrict_contries_locsugg']) && $jobsearch_plugin_options['restrict_contries_locsugg'] != '' ? $jobsearch_plugin_options['restrict_contries_locsugg'] : '';

            $def_radius_unit = isset($jobsearch_plugin_options['top_search_radius_unit']) ? $jobsearch_plugin_options['top_search_radius_unit'] : '';

            $current_time = current_time('timestamp');

            $jobsearch_loc_address = $_REQUEST['location'] . (isset($default_selctd_contry[0]) && $default_selctd_contry[0] != '' ? ' ' . $default_selctd_contry[0] : '');
            $radius = $_REQUEST['loc_radius'];

            $location_response = jobsearch_address_to_cords($jobsearch_loc_address);

            $lat = isset($location_response['lat']) ? $location_response['lat'] : '';
            $lng = isset($location_response['lng']) ? $location_response['lng'] : '';

            if ($lat != '' && $lng != '') {
                if ($def_radius_unit != 'miles') {
                    $radius = $radius / 1.60934; // 1.60934 == 1 Mile
                }
                $radiusCheck = new \RadiusCheck($lat, $lng, $radius);
                $minLat = $radiusCheck->MinLatitude();
                $maxLat = $radiusCheck->MaxLatitude();
                $minLong = $radiusCheck->MinLongitude();
                $maxLong = $radiusCheck->MaxLongitude();

                //echo $minLat . '<br>';
                //echo $maxLat . '<br>';
                //echo $minLong . '<br>';
                //echo $maxLong . '<br>';

                $posts_query = "SELECT posts.ID FROM $wpdb->posts AS posts";
                $posts_query .= " LEFT JOIN $wpdb->postmeta AS postmeta ON(posts.ID = postmeta.post_id)";
                $posts_query .= " LEFT JOIN $wpdb->postmeta AS mt3 ON(posts.ID = mt3.post_id)";
                $posts_query .= " LEFT JOIN $wpdb->postmeta AS mt4 ON(posts.ID = mt4.post_id)";
                $posts_query .= " WHERE posts.post_type = '%s' AND posts.post_status = 'publish'";
                $posts_query .= " AND postmeta.meta_key = 'jobsearch_field_candidate_approved' AND postmeta.meta_value='on'";
                $posts_query .= " AND (mt3.meta_key = 'jobsearch_field_location_lat' AND mt3.meta_value BETWEEN {$minLat} AND {$maxLat})";
                $posts_query .= " AND (mt4.meta_key = 'jobsearch_field_location_lng' AND mt4.meta_value BETWEEN {$minLong} AND {$maxLong})";

                $all_posts = $wpdb->get_col($wpdb->prepare($posts_query, 'candidate'));

                if (empty($all_posts)) {
                    $all_posts = array('0');
                }

                return $all_posts;
            }
        }
    }

    public function candidate_polygon_filter($polygon_pathstr, $post_ids, $custom_meta_array = '')
    {
        global $wpdb;
        if (empty($post_ids)) {
            if (isset($custom_meta_array) && !empty($custom_meta_array) && is_array($custom_meta_array)) {
                $post_ids = jobsearch_get_query_whereclase_by_array($custom_meta_array);
            }
        }
        $polygon_path = array();
        $polygon_path = explode('||', $polygon_pathstr);
        if (count($polygon_path) > 0) {
            array_walk($polygon_path, function (&$val) {
                $val = explode(',', $val);
            });
        }
        $new_post_ids = array();
        $th_counter = 0;
        foreach ($post_ids as $candidate_id) {
            $qry = "SELECT meta_value FROM $wpdb->postmeta WHERE 1=1 AND post_id='" . $candidate_id . "' AND meta_key='jobsearch_field_location_lat'";
            $candidate_latitude_arr = $wpdb->get_col($qry);
            $candidate_latitude = isset($candidate_latitude_arr[0]) ? $candidate_latitude_arr[0] : '';

            $qry = "SELECT meta_value FROM $wpdb->postmeta WHERE 1=1 AND post_id='" . $candidate_id . "' AND meta_key='jobsearch_field_location_lng'";
            $candidate_longitude_arr = $wpdb->get_col($qry);
            $candidate_longitude = isset($candidate_longitude_arr[0]) ? $candidate_longitude_arr[0] : '';

            if ($this->pointInPolygon(array($candidate_latitude, $candidate_longitude), $polygon_path)) {
                $new_post_ids[] = $candidate_id;
            }
            if ($th_counter > 3000) {
                break;
            }
            $th_counter++;
        }
        return $new_post_ids;
    }

    public function jobsearch_candidates_content($candidate_arg = '')
    {
        global $wpdb, $post, $jobsearch_form_fields, $jobsearch_search_fields, $pagenow, $sitepress, $jobsearch_plugin_options;
        $page_id = isset($post->ID) ? $post->ID : '';
        $cand_profile_restrict = new Candidate_Profile_Restriction;
        $candidate_listing_percent = isset($jobsearch_plugin_options['jobsearch_candidate_skills']) ? $jobsearch_plugin_options['jobsearch_candidate_skills'] : '';
        $candmin_listing_percent = isset($jobsearch_plugin_options['cand_min_listpecent']) ? $jobsearch_plugin_options['cand_min_listpecent'] : '';
        $candmin_listing_percent = absint($candmin_listing_percent);

        $restrict_candidates = isset($jobsearch_plugin_options['restrict_candidates_list']) ? $jobsearch_plugin_options['restrict_candidates_list'] : '';

        $def_radius_unit = isset($jobsearch_plugin_options['top_search_radius_unit']) ? $jobsearch_plugin_options['top_search_radius_unit'] : '';

        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $trans_able_options = $sitepress->get_setting('custom_posts_sync_option', array());
        }
        // getting arg array from ajax

        $page_id = get_the_ID();
        $all_post_ids = array();
        if (isset($_POST['candidate_arg']) && $_POST['candidate_arg']) {
            $candidate_arg = stripslashes(html_entity_decode($_POST['candidate_arg']));
            $candidate_arg = json_decode($candidate_arg);
            $candidate_arg = $this->toArray($candidate_arg);
        }
        if (isset($candidate_arg) && $candidate_arg != '' && !empty($candidate_arg)) {
            extract($candidate_arg);
        }
        $default_date_time_formate = 'd-m-Y H:i:s';
        // getting if user set it with his choice
        if (false === ($candidate_view = jobsearch_get_transient_obj('jobsearch_candidate_view' . $candidate_short_counter))) {
            $candidate_view = isset($atts['candidate_view']) ? $atts['candidate_view'] : '';
        }

        $element_candidate_sort_by = isset($atts['candidate_sort_by']) ? $atts['candidate_sort_by'] : 'no';
        $element_candidate_topmap = '';
        $element_candidate_map_position = isset($atts['candidate_map_position']) ? $atts['candidate_map_position'] : 'full';
        $element_candidate_layout_switcher = isset($atts['candidate_layout_switcher']) ? $atts['candidate_layout_switcher'] : 'no';
        $element_candidate_layout_switcher_view = isset($atts['candidate_layout_switcher_view']) ? $atts['candidate_layout_switcher_view'] : 'grid';
        $element_candidate_map_height = isset($atts['candidate_map_height']) ? $atts['candidate_map_height'] : 400;
        $element_candidate_footer = isset($atts['candidate_footer']) ? $atts['candidate_footer'] : 'no';
        $element_candidate_search_keyword = isset($atts['candidate_search_keyword']) ? $atts['candidate_search_keyword'] : 'no';

        $element_candidate_recent_switch = isset($atts['candidate_recent_switch']) ? $atts['candidate_recent_switch'] : 'no';
        $candidate_candidate_urgent = isset($atts['candidate_urgent']) ? $atts['candidate_urgent'] : 'all';
        $candidate_type = isset($atts['candidate_type']) ? $atts['candidate_type'] : 'all';
        $candidate_filters_sidebar = isset($atts['candidate_filters']) ? $atts['candidate_filters'] : '';

        $candidate_right_sidebar_content = isset($content) ? $content : '';
        $jobsearch_candidate_sidebar = isset($atts['jobsearch_candidate_sidebar']) ? $atts['jobsearch_candidate_sidebar'] : '';
        $jobsearch_map_position = isset($atts['jobsearch_map_position']) && $atts['jobsearch_map_position'] != '' ? ($atts['jobsearch_map_position']) : 'right';

        $candidate_desc = isset($atts['candidate_desc']) ? $atts['candidate_desc'] : '';
        $candidate_cus_fields = isset($atts['candidate_cus_fields']) ? $atts['candidate_cus_fields'] : 'yes';

        $candidate_per_page = '-1';
        $pagination = 'no';
        $candidate_per_page = isset($atts['candidate_per_page']) ? $atts['candidate_per_page'] : '-1';
        $candidate_per_page = isset($_REQUEST['per-page']) && $_REQUEST['per-page'] > 0 ? $_REQUEST['per-page'] : $candidate_per_page;
        $pagination = isset($atts['candidate_pagination']) ? $atts['candidate_pagination'] : 'no';
        $filter_arr = array();
        $qryvar_sort_by_column = '';
        $element_filter_arr = array();

        $element_filter_arr[] = array(
            'key' => 'jobsearch_field_candidate_approved',
            'value' => 'on',
            'compare' => '=',
        );
        if ($candidate_listing_percent == 'on' && $candmin_listing_percent > 0) {
            $element_filter_arr[] = array(
                'key' => 'overall_skills_percentage',
                'value' => $candmin_listing_percent,
                'compare' => '>=',
                'type' => 'NUMERIC',
            );
        }

        $content_columns = 'jobsearch-column-12 jobsearch-typo-wrap'; // if filteration not true
        $paging_var = 'candidate_page';
        // Element fields in filter
        if (isset($_REQUEST['candidate_type']) && $_REQUEST['candidate_type'] != '') {
            $candidate_type = $_REQUEST['candidate_type'];
        }

        if (function_exists('jobsearch_visibility_query_args')) {
            $element_filter_arr = jobsearch_visibility_query_args($element_filter_arr);
        }

        if (!isset($_REQUEST[$paging_var])) {
            $_REQUEST[$paging_var] = '';
        }

        // Get all arguments from getting flters.
        $left_filter_arr = $this->get_filter_arg($candidate_short_counter);

        $post_ids = array();
        if (!empty($left_filter_arr)) {
            // apply all filters and get ids
            $post_ids = $this->get_candidate_id_by_filter($left_filter_arr);
        }

        $post_ids = $this->candidate_location_filter($post_ids);

        $loc_polygon_path = '';
        if (isset($_REQUEST['loc_polygon_path']) && $_REQUEST['loc_polygon_path'] != '') {
            $loc_polygon_path = $_REQUEST['loc_polygon_path'];
        }

        $radius_locpost_ids = $this->location_radius_filter_ids();
        //var_dump($radius_locpost_ids);
        if (!empty($radius_locpost_ids)) {
            if (!empty($post_ids)) {
                $post_ids = array_intersect($post_ids, $radius_locpost_ids);
            } else {
                $post_ids = $radius_locpost_ids;
            }
        }

        if (!empty($post_ids)) {
            $all_post_ids = $post_ids;
        }

        $search_title = isset($_REQUEST['search_title']) ? $_REQUEST['search_title'] : '';


        /*
         * used for relevance sort by filter
         */

        if (isset($_REQUEST['loc_radius']) && $_REQUEST['loc_radius'] > 0 && isset($_REQUEST['location'])) {

            $jobsearch_loc_address = $_REQUEST['location'];
            $radius = $_REQUEST['loc_radius'];

            $location_response = jobsearch_address_to_cords($jobsearch_loc_address);
            $lat = isset($location_response['lat']) ? $location_response['lat'] : '';
            $lng = isset($location_response['lng']) ? $location_response['lng'] : '';

            if ($lat != '' && $lng != '') {
                if ($def_radius_unit != 'miles') {
                    $radius = $radius / 1.60934; // 1.60934 == 1 Mile
                }
                $radiusCheck = new \RadiusCheck($lat, $lng, $radius);
                $minLat = $radiusCheck->MinLatitude();
                $maxLat = $radiusCheck->MaxLatitude();
                $minLong = $radiusCheck->MinLongitude();
                $maxLong = $radiusCheck->MaxLongitude();
                $jobsearch_compare_type = 'DECIMAL';
                if ($radius > 0) {
                    //$jobsearch_compare_type = 'DECIMAL(10,6)';
                }
            }
        }

        /*
         * End used for relevance sort by filter
         */
        $candidate_sort_by = ''; // default value

        if (isset($_REQUEST['sort-by']) && $_REQUEST['sort-by'] != '') {
            $candidate_sort_by = $_REQUEST['sort-by'];
        }
        $meta_key = '';
        $qryvar_candidate_sort_type = 'DESC';
        $qryvar_sort_by_column = 'post_date';

        //
        $candidate_act_orderby = isset($atts['candidate_orderby']) ? $atts['candidate_orderby'] : '';
        $candidate_act_order = isset($atts['candidate_order']) ? $atts['candidate_order'] : '';

        $candidate_act_orderby = apply_filters('jobsearch_cands_listinsh_args_ordery', $candidate_act_orderby);

        if ($candidate_act_orderby == 'promote_profile') {

            add_filter('posts_join_paged', array($this, 'edit_join'), 999, 2);
            add_filter('posts_orderby', array($this, 'edit_orderby'), 999, 2);
        }
        //
        if ($candidate_sort_by == '' && $candidate_act_orderby == 'title') {
            $candidate_sort_by = 'by_title';
        }

        $candidate_sort_by = apply_filters('jobsearch_candlistin_filter_sortby_str', $candidate_sort_by);
        if ($candidate_sort_by == 'recent') {
            $qryvar_candidate_sort_type = 'DESC';
            $qryvar_sort_by_column = 'post_date';
        } elseif ($candidate_sort_by == 'by_title') {
            if ($candidate_act_order == 'ASC') {
                $qryvar_candidate_sort_type = 'ASC';
            } else {
                $qryvar_candidate_sort_type = 'DESC';
            }
            $qryvar_sort_by_column = 'post_title';
        } elseif ($candidate_sort_by == 'alphabetical') {
            $qryvar_candidate_sort_type = 'ASC';
            $qryvar_sort_by_column = 'post_title';
            remove_filter('posts_join_paged', array($this, 'edit_join'), 999, 2);
            remove_filter('posts_orderby', array($this, 'edit_orderby'), 999, 2);
        } elseif ($candidate_sort_by == 'approved') {
            $qryvar_candidate_sort_type = 'DESC';
            $qryvar_sort_by_column = 'jobsearch_field_candidate_approved';
            $meta_key = 'jobsearch_field_candidate_approved';
            remove_filter('posts_join_paged', array($this, 'edit_join'), 999, 2);
            remove_filter('posts_orderby', array($this, 'edit_orderby'), 999, 2);
        } elseif ($candidate_sort_by == 'most_viewed') {
            $qryvar_candidate_sort_type = 'DESC';
            $qryvar_sort_by_column = 'meta_value_num';
            $meta_key = 'jobsearch_candidate_views_count';
            remove_filter('posts_join_paged', array($this, 'edit_join'), 999, 2);
            remove_filter('posts_orderby', array($this, 'edit_orderby'), 999, 2);
        }

        $skill_in = '';
        if (isset($_REQUEST['skill_in']) && $_REQUEST['skill_in'] != '') {
            $skill_in = $_REQUEST['skill_in'];
        }

        $args_count = array(
            'posts_per_page' => "1",
            'post_type' => 'candidate',
            'post_status' => 'publish',
            'fields' => 'ids', // only load ids
            'meta_query' => array(
                $element_filter_arr,
            ),
        );
        if (isset($_REQUEST['sector_cat']) && $_REQUEST['sector_cat'] != '') {

            $args_count['tax_query'][] = array(
                'taxonomy' => 'sector',
                'field' => 'slug',
                'terms' => $_REQUEST['sector_cat']
            );
        } else if (isset($atts['candidate_cat']) && $atts['candidate_cat'] != '') {
            $args_count['tax_query'][] = array(
                'taxonomy' => 'sector',
                'field' => 'slug',
                'terms' => $atts['candidate_cat']
            );
        }
        if ($skill_in != '' && $skill_in != 'all') {
            $args_count['tax_query'][] = array(
                'taxonomy' => 'skill',
                'field' => 'slug',
                'terms' => $skill_in,
            );
        }

        $args = array(
            'posts_per_page' => $candidate_per_page,
            'paged' => $_REQUEST[$paging_var],
            'post_type' => 'candidate',
            'post_status' => 'publish',
            'meta_key' => $meta_key,
            'order' => $qryvar_candidate_sort_type,
            'orderby' => $qryvar_sort_by_column,
            'fields' => 'ids', // only load ids
            'meta_query' => array(
                $element_filter_arr,
            ),
        );
        if ((isset($_REQUEST['sector_cat']) && $_REQUEST['sector_cat'] != '')) {

            $args['tax_query'][] = array(
                'taxonomy' => 'sector',
                'field' => 'slug',
                'terms' => $_REQUEST['sector_cat']
            );
        } else if (isset($atts['candidate_cat']) && $atts['candidate_cat'] != '') {
            $args['tax_query'][] = array(
                'taxonomy' => 'sector',
                'field' => 'slug',
                'terms' => $atts['candidate_cat']
            );
        }
        if ($skill_in != '' && $skill_in != 'all') {
            $args['tax_query'][] = array(
                'taxonomy' => 'skill',
                'field' => 'slug',
                'terms' => $skill_in,
            );
        }

        if (isset($_REQUEST['loc_polygon_path']) && $_REQUEST['loc_polygon_path'] != '') {
            $loc_polygon_path = $_REQUEST['loc_polygon_path'];
            $all_post_ids = $this->candidate_polygon_filter($loc_polygon_path, $all_post_ids, $element_filter_arr);
        }

        if (isset($search_title) && $search_title != '') {

//                $search_query = "SELECT ID FROM $wpdb->posts AS posts";
//                $search_query .= " WHERE posts.post_type=%s AND posts.post_status='publish' AND (posts.post_title LIKE %s OR posts.post_content LIKE '%$search_title%')";
//                if (!empty($all_post_ids)) {
//                    $_post_ids = implode(',', $all_post_ids);
//                    $search_query .= " AND posts.ID IN ($_post_ids)";
//                }
//                $search_query .= " ORDER BY ID DESC";
//                $_job_ids = $wpdb->get_col($wpdb->prepare($search_query, 'candidate', "%$search_title%"));
//                if (!empty($_job_ids)) {
//                    $all_post_ids = $_job_ids;
//                } else {
//                    $all_post_ids = array(0);
//                }
        }

        // recent candidate query end

        $restrict_candidates_for_users = isset($jobsearch_plugin_options['restrict_candidates_for_users']) ? $jobsearch_plugin_options['restrict_candidates_for_users'] : '';
        if ($restrict_candidates == 'on' && $restrict_candidates_for_users == 'only_applicants') {
            $cur_user_id = get_current_user_id();
            $cur_user_obj = wp_get_current_user();
            if (!in_array('administrator', (array)$cur_user_obj->roles)) {
                $employer_id = jobsearch_get_user_employer_id($cur_user_id);
                $overall_apps_list = array();
                $employer_job_args = array(
                    'post_type' => 'job',
                    'posts_per_page' => '-1',
                    'post_status' => 'publish',
                    'fields' => 'ids',
                    'meta_query' => array(
                        array(
                            'key' => 'jobsearch_field_job_posted_by',
                            'value' => $employer_id,
                            'compare' => '=',
                        ),
                    ),
                );
                $employer_jobs_query = new \WP_Query($employer_job_args);
                $employer_jobs_posts = $employer_jobs_query->posts;
                if (!empty($employer_jobs_posts) && is_array($employer_jobs_posts)) {
                    foreach ($employer_jobs_posts as $employer_job_id) {
                        $job_applicants_list = get_post_meta($employer_job_id, 'jobsearch_job_applicants_list', true);
                        $job_applicants_list = jobsearch_is_post_ids_array($job_applicants_list, 'candidate');
                        if (empty($job_applicants_list)) {
                            $job_applicants_list = array();
                        }
                        if (is_array($job_applicants_list) && !empty($job_applicants_list)) {
                            $overall_apps_list = array_merge($overall_apps_list, $job_applicants_list);
                        }
                    }
                }
                wp_reset_postdata();

                if (!empty($overall_apps_list)) {
                    $overall_apps_list = array_unique($overall_apps_list);
                    //var_dump($overall_apps_list);
                    if (empty($all_post_ids)) {
                        $all_post_ids = $overall_apps_list;
                    } else {
                        $all_post_ids = array_intersect($all_post_ids, $overall_apps_list);
                    }
                } else {
                    $all_post_ids = array(0);
                }
            }
        }

        if (!empty($all_post_ids)) {
            $args_count['post__in'] = $all_post_ids;
            $args['post__in'] = $all_post_ids;
        }
        $args = apply_filters('jobsearch_candidate_listing_query_args_array', $args, $atts);
        add_filter('posts_where', 'jobsearch_search_query_results_filter', 10, 2);
        $candidate_loop_obj = jobsearch_get_cached_obj('candidate_result_cached_loop_obj1', $args, 12, false, 'wp_query');
        remove_filter('posts_where', 'jobsearch_search_query_results_filter', 10);
        //if (isset($_GET['eropiuy'])) {
        //    var_dump($candidate_loop_obj->request);
        //}
        $wpml_candidate_totnum = $candidate_totnum = $candidate_loop_obj->found_posts;

        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') && $wpml_candidate_totnum == 0 && isset($trans_able_options['candidate']) && $trans_able_options['candidate'] == '2') {
            $sitepress_def_lang = $sitepress->get_default_language();
            $sitepress_curr_lang = $sitepress->get_current_language();
            $sitepress->switch_lang($sitepress_def_lang, true);

            add_filter('posts_where', 'jobsearch_search_query_results_filter', 10, 2);
            $candidate_loop_obj = jobsearch_get_cached_obj('candidate_result_cached_loop_obj1', $args, 12, false, 'wp_query');
            remove_filter('posts_where', 'jobsearch_search_query_results_filter', 10);
            $candidate_totnum = $candidate_loop_obj->found_posts;

            //
            $sitepress->switch_lang($sitepress_curr_lang, true);
        }

        remove_filter('posts_join_paged', array($this, 'edit_join'), 999, 2);
        remove_filter('posts_orderby', array($this, 'edit_orderby'), 999, 2);

        $page_container_view = get_post_meta($page_id, 'careerfy_field_page_view', true);
        ?>
        <form id="jobsearch_candidate_frm_<?php echo absint($candidate_short_counter); ?>">
            <?php
            //
            $cand_top_search = isset($atts['cand_top_search']) ? $atts['cand_top_search'] : '';

            //
            $listing_top_map = isset($atts['cand_top_map']) ? $atts['cand_top_map'] : '';
            $listing_top_map_zoom = isset($atts['cand_top_map_zoom']) && $atts['cand_top_map_zoom'] > 0 ? $atts['cand_top_map_zoom'] : 8;
            $listing_top_map_height = isset($atts['cand_top_map_height']) && $atts['cand_top_map_height'] > 0 ? $atts['cand_top_map_height'] : 450;
            if ($listing_top_map == 'yes') {
                $location_map_type = isset($jobsearch_plugin_options['location_map_type']) ? $jobsearch_plugin_options['location_map_type'] : '';
                if ($location_map_type == 'mapbox') {
                    wp_enqueue_script('jobsearch-mapbox');
                } else {
                    wp_enqueue_script('jobsearch-google-map');
                    wp_enqueue_script('jobsearch-map-infobox');
                    wp_enqueue_script('jobsearch-map-markerclusterer');
                }
                wp_enqueue_script('jobsearch-candidate-lists-map');
                $map_style = isset($jobsearch_plugin_options['jobsearch-location-map-style']) ? $jobsearch_plugin_options['jobsearch-location-map-style'] : '';
                $map_zoom = $listing_top_map_zoom;
                $loc_def_adres = isset($jobsearch_plugin_options['jobsearch-location-default-address']) ? $jobsearch_plugin_options['jobsearch-location-default-address'] : '';

                $map_latitude = '51.2';
                $map_longitude = '0.2';

                if ($loc_def_adres != '' && !(isset($_GET['location']))) {
                    $adre_to_cords = jobsearch_address_to_cords($loc_def_adres);
                    $map_latitude = isset($adre_to_cords['lat']) && $adre_to_cords['lat'] != '' ? $adre_to_cords['lat'] : $map_latitude;
                    $map_longitude = isset($adre_to_cords['lng']) && $adre_to_cords['lng'] != '' ? $adre_to_cords['lng'] : $map_longitude;
                }
                if (isset($_GET['location']) && $_GET['location'] != '') {
                    $url_get_loc = $_GET['location'];
                    $adre_to_cords = jobsearch_address_to_cords($url_get_loc);
                    $map_latitude = isset($adre_to_cords['lat']) && $adre_to_cords['lat'] != '' ? $adre_to_cords['lat'] : $map_latitude;
                    $map_longitude = isset($adre_to_cords['lng']) && $adre_to_cords['lng'] != '' ? $adre_to_cords['lng'] : $map_longitude;
                }

                $map_marker_icon = isset($jobsearch_plugin_options['clistin_map_marker_img']['url']) ? $jobsearch_plugin_options['clistin_map_marker_img']['url'] : '';
                if ($map_marker_icon == '') {
                    $map_marker_icon = jobsearch_plugin_get_url('images/candidate_map_marker.png');
                }
                $map_cluster_icon = isset($jobsearch_plugin_options['clistin_map_cluster_img']['url']) ? $jobsearch_plugin_options['clistin_map_cluster_img']['url'] : '';
                if ($map_cluster_icon == '') {
                    $map_cluster_icon = jobsearch_plugin_get_url('images/map_cluster.png');
                }
                //
                $map_list_arr = array();
                $candidate_all_posts = $candidate_loop_obj->posts;

                foreach ($candidate_all_posts as $candidate_post) {
                    $listing_latitude = get_post_meta($candidate_post, 'jobsearch_field_location_lat', true);
                    $listing_longitude = get_post_meta($candidate_post, 'jobsearch_field_location_lng', true);

                    if ($listing_latitude != '' && $listing_longitude != '') {
                        //sectors html
                        $get_pos_sectrs = wp_get_post_terms($candidate_post, 'sector');
                        $map_pos_sectrs_html = '';
                        if (!empty($get_pos_sectrs)) {
                            $map_secpage_id = isset($jobsearch_plugin_options['jobsearch_cand_result_page']) ? $jobsearch_plugin_options['jobsearch_cand_result_page'] : '';
                            $map_secpage_id = jobsearch__get_post_id($map_secpage_id, 'page');
                            $map_secresult_page = get_permalink($map_secpage_id);
                            $map_pos_sectrs_html .= ' ' . esc_html__('in', 'wp-jobsearch') . ' ';
                            foreach ($get_pos_sectrs as $get_pos_sectr) {
                                $map_pos_sectrs_html .= '<a href="' . add_query_arg(array('sector_cat' => $get_pos_sectr->slug, 'ajax_filter' => 'true'), $map_secresult_page) . '">' . $get_pos_sectr->name . '</a> ';
                            }
                        }
                        //logo img
                        $map_pos_thumb_src = jobsearch_candidate_img_url_comn($candidate_post);

                        //address
                        $map_posadres = jobsearch_job_item_address($candidate_post);
                        if ($map_posadres != '') {
                            $map_posadres = '<div class="map-info-adres"><i class="jobsearch-icon jobsearch-maps-and-flags"></i> ' . $map_posadres . '</div>';
                        }

                        if ($location_map_type == 'mapbox') {
                            $list_all_atts = array(
                                'type' => 'Feature',
                                'geometry' => array(
                                    'type' => 'Point',
                                    'coordinates' => array($listing_longitude, $listing_latitude)
                                ),
                                'properties' => array(
                                    'id' => $candidate_post,
                                    'title' => wp_trim_words(get_the_title($candidate_post), 5),
                                    'link' => get_permalink($candidate_post),
                                    'logo_img_url' => $map_pos_thumb_src,
                                    'address' => $map_posadres,
                                    'sector' => $map_pos_sectrs_html,
                                    'marker' => $map_marker_icon,
                                )
                            );
                        } else {
                            $list_all_atts = array(
                                'lat' => $listing_latitude,
                                'long' => $listing_longitude,
                                'id' => $candidate_post,
                                'title' => wp_trim_words(get_the_title($candidate_post), 5),
                                'link' => get_permalink($candidate_post),
                                'logo_img_url' => $map_pos_thumb_src,
                                'address' => $map_posadres,
                                'sector' => $map_pos_sectrs_html,
                                'marker' => $map_marker_icon,
                            );
                        }
                        if ($cand_profile_restrict::cand_field_is_locked('display_name|display_name')) {
                            $list_all_atts['title'] = '';
                        }
                        if ($cand_profile_restrict::cand_field_is_locked('display_name|profile_img')) {
                            $list_all_atts['logo_img_url'] = '';
                        }
                        if ($cand_profile_restrict::cand_field_is_locked('display_name|sector')) {
                            $list_all_atts['sector'] = '';
                        }
                        if ($cand_profile_restrict::cand_field_is_locked('address_defields')) {
                            $list_all_atts['address'] = '';
                        }
                        $map_list_arr[] = $list_all_atts;
                    }
                }
                //
                $listn_map_arr = array(
                    'map_id' => $candidate_short_counter,
                    'map_zoom' => $map_zoom,
                    'map_style' => $map_style,
                    'latitude' => $map_latitude,
                    'longitude' => $map_longitude,
                    'cluster_icon' => $map_cluster_icon,
                    'cords_list' => $map_list_arr,
                );
                if ($location_map_type == 'mapbox') {
                    $mapbox_access_token = isset($jobsearch_plugin_options['mapbox_access_token']) ? $jobsearch_plugin_options['mapbox_access_token'] : '';
                    $mapbox_style_url = isset($jobsearch_plugin_options['mapbox_style_url']) ? $jobsearch_plugin_options['mapbox_style_url'] : '';
                    $listn_map_arr['access_token'] = $mapbox_access_token;
                    $listn_map_arr['map_style'] = $mapbox_style_url;
                }
                $listn_map_obj = json_encode($listn_map_arr);

                ob_start();
                ?>
                <script>
                    var jobsearch_listing_map;
                    var reset_top_map_marker = [];
                    var markerClusterers;
                    var jobsearch_listing_dataobj = jQuery.parseJSON('<?php echo addslashes($listn_map_obj) ?>');
                    <?php
                    if (isset($_REQUEST['ajax_filter']) && $_REQUEST['ajax_filter'] == 'true' && isset($_REQUEST['action']) && $_REQUEST['action'] == 'jobsearch_jobs_content') {
                    ?>
                    jobsearch_listing_top_map(jobsearch_listing_dataobj, 'true');
                    <?php
                    }
                    ?>
                    jQuery(document).ready(function () {
                        jobsearch_listing_top_map(jobsearch_listing_dataobj, '');
                    });
                </script>
                <div class="jobsearch-listing-mapcon <?php echo($cand_top_search == 'yes' ? 'with-serch-map-both' : '') ?>">
                    <div id="listings-map-<?php echo absint($candidate_short_counter); ?>"
                         class="jobsearch-joblist-map"
                         style="height: <?php echo($listing_top_map_height) ?>px;"></div>
                </div>
                <?php
                $map_html = ob_get_clean();
                echo apply_filters('jobsearch_cands_listin_topmap_html', $map_html, $listn_map_obj, $candidate_short_counter, $listing_top_map_height, $atts);
                if ($page_container_view == 'wide') {
                    echo '<div class="container">';
                }
            }
            ?>
            <div style="display:none" id='candidate_arg<?php echo absint($candidate_short_counter); ?>'><?php
                echo json_encode($candidate_arg);
                ?>
            </div>
            <?php
            if ($cand_top_search == 'yes') {

                $location_map_type = isset($jobsearch_plugin_options['location_map_type']) ? $jobsearch_plugin_options['location_map_type'] : '';
                if ($location_map_type == 'mapbox') {
                    wp_enqueue_script('jobsearch-mapbox');
                    wp_enqueue_script('jobsearch-mapbox-geocoder');
                    wp_enqueue_script('mapbox-geocoder-polyfill');
                    wp_enqueue_script('mapbox-geocoder-polyfillauto');
                } else {
                    wp_enqueue_script('jobsearch-google-map');
                }
                wp_enqueue_script('jobsearch-location-autocomplete');

                //
                wp_enqueue_script('jobsearch-search-box-sugg');

                $top_serch_style = isset($atts['cand_top_search_view']) ? $atts['cand_top_search_view'] : '';
                $top_search_title = isset($atts['cand_top_search_title']) && !empty($atts['cand_top_search_title']) ? $atts['cand_top_search_title'] : 'yes';
                $top_search_location = isset($atts['cand_top_search_location']) && !empty($atts['cand_top_search_location']) ? $atts['cand_top_search_location'] : 'yes';
                $top_search_sector = isset($atts['cand_top_search_sector']) && !empty($atts['cand_top_search_sector']) ? $atts['cand_top_search_sector'] : 'yes';
                $vc_top_search_radius = isset($atts['cand_top_search_radius']) && !empty($atts['cand_top_search_radius']) ? $atts['cand_top_search_radius'] : 'yes';
                $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';

                $search_title_val = isset($_REQUEST['search_title']) ? $_REQUEST['search_title'] : '';
                $location_val = isset($_REQUEST['location']) ? esc_html($_REQUEST['location']) : '';
                $cat_sector_val = isset($_REQUEST['sector_cat']) ? urldecode($_REQUEST['sector_cat']) : '';

                $search_main_class = '';
                $adv_search_on = '';
                if ($top_serch_style == 'advance') {
                    $search_main_class = 'jobsearch-advance-search-holdr';
                    $adv_search_on = 'has-advance-search';
                }
                if ($listing_top_map == 'yes') {
                    $search_main_class .= ' search-with-map';
                }

                $without_sectr_class = 'search-cat-off';
                if (($top_search_sector == 'yes' && $sectors_enable_switch == 'on') || ($top_search_sector == 'no' && $sectors_enable_switch == 'no')) {
                    $without_sectr_class = '';
                }
                $without_loc_class = 'search-loc-off';
                if ($top_search_location == 'yes') {
                    $without_loc_class = '';
                }

                $top_search_autofill = isset($atts['top_search_autofill']) ? $atts['top_search_autofill'] : '';
                $top_search_locsugg = isset($jobsearch_plugin_options['top_search_locsugg']) ? $jobsearch_plugin_options['top_search_locsugg'] : '';
                $top_search_geoloc = isset($jobsearch_plugin_options['top_search_geoloc']) ? $jobsearch_plugin_options['top_search_geoloc'] : '';
                $top_search_radius = isset($jobsearch_plugin_options['top_search_radius']) ? $jobsearch_plugin_options['top_search_radius'] : '';
                $top_search_def_radius = isset($jobsearch_plugin_options['top_search_def_radius']) ? $jobsearch_plugin_options['top_search_def_radius'] : 50;
                $top_search_max_radius = isset($jobsearch_plugin_options['top_search_max_radius']) ? $jobsearch_plugin_options['top_search_max_radius'] : 500;
                ?>
                <div class="jobsearch-top-searchbar jobsearch-typo-wrap <?php echo($search_main_class) ?> <?php echo($adv_search_on) ?>">
                    <!-- Sub Header Form -->
                    <div class="jobsearch-subheader-form">
                        <div class="jobsearch-banner-search <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?>">
                            <ul>
                                <?php if ($top_search_title == 'yes') { ?>
                                    <li>
                                        <div class="<?php echo($top_search_autofill != 'no' ? 'jobsearch-sugges-search' : '') ?>">
                                            <input placeholder="<?php esc_html_e('Title, Keywords, or Phrase', 'wp-jobsearch') ?>"
                                                   name="search_title"
                                                   value="<?php echo jobsearch_esc_html($search_title_val) ?>"
                                                   data-type="candidate" type="text">
                                            <span class="sugg-search-loader"></span>
                                        </div>
                                    </li>
                                <?php } ?>
                                <?php if ($top_search_location == 'yes') { ?>
                                    <li>
                                        <div class="jobsearch_searchloc_div">
                                            <span class="loc-loader"></span>
                                            <?php

                                            if ($top_search_locsugg == 'no') { ?>
                                                <input placeholder="<?php esc_html_e('City, State or ZIP', 'wp-jobsearch') ?>"
                                                       class="<?php echo($top_search_geoloc != 'no' ? 'srch_autogeo_location' : '') ?>"
                                                       name="location"
                                                       value="<?php echo jobsearch_esc_html(urldecode($location_val)) ?>"
                                                       type="text">
                                            <?php } else { ?>
                                                <input placeholder="<?php esc_html_e('City, State or ZIP', 'wp-jobsearch') ?>"
                                                       autocomplete="off" class="jobsearch_search_location_field"
                                                       value="<?php echo jobsearch_esc_html(urldecode($location_val)) ?>"
                                                       type="text">
                                                <input type="hidden"
                                                       class="loc_search_keyword <?php echo($top_search_geoloc != 'no' ? 'srch_autogeo_location' : '') ?>"
                                                       name="location"
                                                       value="<?php echo jobsearch_esc_html(urldecode($location_val)) ?>">
                                            <?php }
                                            if ($top_search_radius == 'yes' && $vc_top_search_radius == 'yes' && $top_serch_style != 'advance') { ?>
                                                <div class="careerfy-radius-tooltip">
                                                    <label><?php echo esc_html__('Radius', 'careerfy-frame') ?>
                                                        ( <?php echo esc_html__($def_radius_unit, 'careerfy-frame') ?>
                                                        )</label><input
                                                            type="number" name="loc_radius"
                                                            value="<?php echo($top_search_def_radius) ?>"
                                                            max="<?php echo($top_search_max_radius) ?>"></div>
                                            <?php } ?>
                                        </div>

                                        <?php
                                        if ($top_search_geoloc != 'no') {
                                            ?>
                                            <a href="javascript:void(0);" class="geolction-btn"
                                               onclick="JobsearchGetClientLocation()"><i
                                                        class="jobsearch-icon jobsearch-location"></i></a>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                                <?php
                                if ($sectors_enable_switch == 'on') {
                                    if ($top_search_sector == 'yes') {

                                        $sectors_args = array(
                                            'orderby' => 'name',
                                            'order' => 'ASC',
                                            'fields' => 'all',
                                            'hide_empty' => false,
                                        );
                                        $all_sectors = get_terms('sector', $sectors_args);
                                        ?>
                                        <li>
                                            <div class="jobsearch-select-style">
                                                <select name="sector_cat" class="selectize-select"
                                                        placeholder="<?php esc_html_e('Select Sector', 'wp-jobsearch') ?>">
                                                    <option value=""><?php esc_html_e('Select Sector', 'wp-jobsearch') ?></option>
                                                    <?php
                                                    if (!empty($all_sectors)) {
                                                        echo jobsearch_sector_terms_hierarchical(0, $all_sectors, '', 0, 0, $cat_sector_val);
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                }
                                if ($top_serch_style == 'advance') {
                                    ?>
                                    <li class="adv-srch-toggler"><a href="javascript:void(0);"
                                                                    class="adv-srch-toggle-btn"><span>+</span> <?php esc_html_e('Advance Search', 'wp-jobsearch') ?>
                                        </a></li>
                                    <?php
                                }
                                ?>
                                <li class="jobsearch-banner-submit">
                                    <input type="hidden" name="ajax_filter" value="true">
                                    <input id="jobsearch-jobadvserach-submit" type="submit" value=""> <i class="jobsearch-icon jobsearch-search"></i>
                                </li>
                            </ul>
                            <?php
                            if ($top_serch_style == 'advance') {
                                $sh_atts = isset($candidate_arg['atts']) ? $candidate_arg['atts'] : '';
                                $top_search_radius = isset($jobsearch_plugin_options['top_search_radius']) ? $jobsearch_plugin_options['top_search_radius'] : '';
                                $top_search_def_radius = isset($jobsearch_plugin_options['top_search_def_radius']) ? $jobsearch_plugin_options['top_search_def_radius'] : 50;
                                $top_search_max_radius = isset($jobsearch_plugin_options['top_search_max_radius']) ? $jobsearch_plugin_options['top_search_max_radius'] : 500;
                                ?>
                                <div class="adv-search-options">
                                    <ul>
                                        <?php
                                        if ($top_search_radius != 'no') { ?>
                                            <li class="srch-radius-slidr">
                                                <?php
                                                wp_enqueue_style('jquery-ui');
                                                wp_enqueue_script('jquery-ui');
                                                $tprand_id = rand(1000000, 99999999);
                                                $tpsrch_min = 0;
                                                $tpsrch_field_max = $top_search_max_radius > 0 ? $top_search_max_radius : 500;
                                                $tpsrch_complete_str_first = "";
                                                $tpsrch_complete_str_second = "";
                                                $tpsrch_complete_str = '0';
                                                $tpsrch_complete_str_first = $tpsrch_min;
                                                $tpsrch_complete_str_second = $tpsrch_field_max;
                                                $tpsrch_str_var_name = 'loc_radius';
                                                if (isset($_REQUEST[$tpsrch_str_var_name])) {
                                                    $tpsrch_complete_str = $_REQUEST[$tpsrch_str_var_name];
                                                    $tpsrch_complete_str_arr = explode("-", $tpsrch_complete_str);
                                                    $tpsrch_complete_str_first = isset($tpsrch_complete_str_arr[0]) ? $tpsrch_complete_str_arr[0] : '';
                                                    $tpsrch_complete_str_second = isset($tpsrch_complete_str_arr[1]) ? $tpsrch_complete_str_arr[1] : '';
                                                } else {
                                                    $tpsrch_complete_str = absint($top_search_def_radius);
                                                    $tpsrch_complete_str_first = absint($top_search_def_radius);
                                                }
                                                $to_radius_unit = esc_html__('Km', 'wp-jobsearch');
                                                if ($def_radius_unit == 'miles') {
                                                    $to_radius_unit = esc_html__('Miles', 'wp-jobsearch');
                                                }
                                                ?>
                                                <div class="filter-slider-range">
                                                    <span class="radius-txt"><?php esc_html_e('Radius:', 'wp-jobsearch') ?></span>
                                                    <span id="radius-num-<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>"
                                                          class="radius-numvr-holdr"><?php echo jobsearch_esc_html($tpsrch_complete_str); ?></span>
                                                    <span class="radius-punit"><?php echo($to_radius_unit) ?></span>
                                                    <input type="hidden" id="loc-def-radiusval"
                                                           value="<?php echo jobsearch_esc_html($tpsrch_complete_str) ?>">
                                                    <input type="hidden" name="loc_radius"
                                                           id="<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>"
                                                           value="">
                                                </div>

                                                <div id="slider-tpsrch<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>"></div>
                                                <script>
                                                    jQuery(document).ready(function () {
                                                        var toSetRadiusVal = setInterval(function () {
                                                            jQuery('input#<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>').val('');
                                                            <?php
                                                            if ($tpsrch_complete_str > 0 && $tpsrch_field_max > $tpsrch_complete_str) {
                                                            ?>
                                                            var initSlideWidthPerc = (<?php echo jobsearch_esc_html($tpsrch_complete_str) ?>/<?php echo absint($tpsrch_field_max); ?>)*100;
                                                            jQuery("#slider-tpsrch<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>").find('.ui-slider-range').css({width: initSlideWidthPerc + '%'});
                                                            <?php
                                                            }
                                                            ?>
                                                            clearInterval(toSetRadiusVal);
                                                        }, 1000);

                                                        jQuery("#slider-tpsrch<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>").slider({
                                                            tpsrch: true,
                                                            range: "min",
                                                            min: <?php echo absint($tpsrch_min); ?>,
                                                            max: <?php echo absint($tpsrch_field_max); ?>,
                                                            values: [<?php echo absint($tpsrch_complete_str_first); ?>],
                                                            slide: function (event, ui) {
                                                                var slideWidthPerc = ((ui.values[0]) /<?php echo absint($tpsrch_field_max); ?>) * 100;
                                                                jQuery("#slider-tpsrch<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>").find('.ui-slider-range').css({width: slideWidthPerc + '%'});
                                                                jQuery("#<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>").val(ui.values[0]);
                                                                jQuery("#radius-num-<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>").html(ui.values[0]);
                                                            },
                                                        });
                                                        jQuery("#<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>").val(jQuery("#slider-tpsrch<?php echo esc_html($tpsrch_str_var_name . $tprand_id) ?>").slider("values", 0));

                                                    });
                                                </script>
                                            </li>
                                            <?php
                                        }
                                        echo apply_filters('jobsearch_candidate_top_filter_date_posted_box_html', '', $candidate_short_counter, $sh_atts);
                                        echo apply_filters('jobsearch_custom_fields_top_filters_html', '', 'candidate', $candidate_short_counter);
                                        ?>
                                    </ul>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <!-- Sub Header Form -->
                </div>
            <?php } ?>
            <div class="jobsearch-row">
                <?php
                if (($candidate_filters_sidebar == 'yes') || (!empty($jobsearch_candidate_sidebar))) {  // if sidebar on from element
                    set_query_var('candidate_type', $candidate_type);
                    set_query_var('candidate_short_counter', $candidate_short_counter);
                    set_query_var('candidate_arg', $candidate_arg);
                    set_query_var('candidate_view', $candidate_view);
                    set_query_var('args_count', $args_count);
                    set_query_var('candidate_right_sidebar_content', $candidate_right_sidebar_content);
                    set_query_var('atts', $atts);
                    set_query_var('candidate_totnum', $candidate_totnum);
                    set_query_var('page_url', $page_url);
                    set_query_var('candidate_loop_obj', $candidate_loop_obj);
                    set_query_var('global_rand_id', $candidate_short_counter);
                    jobsearch_get_template_part('filters', 'candidate-template', 'candidates');
                    $content_columns = 'jobsearch-column-9 jobsearch-typo-wrap';
                } else {
                    set_query_var('candidate_arg', $candidate_arg);
                    $content_columns = 'jobsearch-column-12 jobsearch-typo-wrap';
                }
                ?>
                <div class="<?php echo esc_html($content_columns); ?>">
                    <div class="wp-jobsearch-candidate-content wp-jobsearch-dev-candidate-content"
                         id="jobsearch-data-candidate-content-<?php echo esc_html($candidate_short_counter); ?>"
                         data-id="<?php echo esc_html($candidate_short_counter); ?>">
                        <div id="jobsearch-loader-<?php echo esc_html($candidate_short_counter); ?>"></div>
                        <?php
                        $candidates_title = isset($atts['candidates_title']) ? $atts['candidates_title'] : '';
                        $candidates_subtitle = isset($atts['candidates_subtitle']) ? $atts['candidates_subtitle'] : '';
                        $candidates_title_alignment = isset($atts['candidates_title_alignment']) ? $atts['candidates_title_alignment'] : '';
                        $candidate_element_seperator = isset($atts['jobsearch_candidates_seperator_style']) ? $atts['jobsearch_candidates_seperator_style'] : '';
                        $jobsearch_candidates_element_title_color = isset($atts['jobsearch_candidates_element_title_color']) ? $atts['jobsearch_candidates_element_title_color'] : '';
                        $jobsearch_candidates_element_subtitle_color = isset($atts['jobsearch_candidates_element_subtitle_color']) ? $atts['jobsearch_candidates_element_subtitle_color'] : '';
                        $element_title_color = '';
                        if (isset($jobsearch_candidates_element_title_color) && $jobsearch_candidates_element_title_color != '') {
                            $element_title_color = ' style="color:' . $jobsearch_candidates_element_title_color . ' ! important"';
                        }
                        $element_subtitle_color = '';
                        if (isset($jobsearch_candidates_element_subtitle_color) && $jobsearch_candidates_element_subtitle_color != '') {
                            $element_subtitle_color = ' style="color:' . $jobsearch_candidates_element_subtitle_color . ' ! important"';
                        }
                        if ($candidates_title != '' || $candidates_subtitle != '') {
                            ?>
                            <div class="row">
                                <div class="jobsearch-column-12 jobsearch-typo-wrap">
                                    <div class="element-title <?php echo($candidates_title_alignment); ?>">
                                        <?php
                                        if ($candidates_title != '' || $candidates_subtitle != '') {
                                            if ($candidates_title != '') {
                                                ?>
                                                <h2<?php echo force_balance_tags($element_title_color); ?>><?php echo esc_html($candidates_title); ?></h2>
                                                <?php
                                            }
                                            if ($candidates_subtitle != '') {
                                                ?>
                                                <p <?php echo force_balance_tags($element_subtitle_color); ?>><?php echo esc_html($candidates_subtitle); ?></p>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        // only ajax request procced
                        if (isset($candidate_view)) {
                            // search keywords
                            $search_keyword_html = apply_filters('jobsearch_candidate_search_keyword', '', $page_url, $atts);
                            echo force_balance_tags($search_keyword_html);
                            // sorting fields
                            $this->candidate_search_sort_fields($atts, $candidate_sort_by, $candidate_short_counter, $candidate_view, $candidate_totnum, $candidate_per_page);
                        }

                        set_query_var('candidate_loop_obj', $candidate_loop_obj);
                        set_query_var('candidate_view', $candidate_view);
                        set_query_var('candidate_desc', $candidate_desc);
                        set_query_var('candidate_cus_fields', $candidate_cus_fields);
                        set_query_var('candidate_short_counter', $candidate_short_counter);
                        set_query_var('atts', $atts);
                        jobsearch_get_template_part($candidate_view, 'candidate-template', 'candidates');
                        wp_reset_postdata();
                        ?>
                    </div>
                    <?php
                    // apply paging
                    $paging_args = array('total_posts' => $candidate_totnum,
                        'candidate_per_page' => $candidate_per_page,
                        'paging_var' => $paging_var,
                        'show_pagination' => $pagination,
                        'candidate_short_counter' => $candidate_short_counter,
                    );
                    $this->jobsearch_candidate_pagination_callback($paging_args);
                    ?>
                </div>
            </div>
            <?php
            if ($loc_polygon_path != '') {
                $jobsearch_form_fields->input_hidden_field(
                    array(
                        'simple' => true,
                        'cust_id' => "loc_polygon_path",
                        'cust_name' => 'loc_polygon_path',
                        'std' => $loc_polygon_path,
                    )
                );
            }
            $jobsearch_form_fields->input_hidden_field(
                array(
                    'return' => false,
                    'cust_name' => '',
                    'classes' => 'candidate-counter',
                    'std' => $candidate_short_counter,
                )
            );

            if ($listing_top_map == 'yes') {
                if ($page_container_view == 'wide') {
                    echo '</div>';
                }
            }
            ?>


        </form>
        <?php
        // only for ajax request
        if (isset($_GET['action']) && $_GET['action'] != 'elementor') {
            if (isset($_REQUEST['action']) && $pagenow != 'post.php') {
                die();
            }
        }
    }

    public function edit_join($join_paged_statement, $wp_query)
    {
        global $wpdb;
        if (
            !isset($wp_query->query) || $wp_query->is_page || (isset($wp_query->query['post_type']) && $wp_query->query['post_type'] != 'candidate')
        ) {
            return $join_paged_statement;
        }

        $join_to_add = "
                LEFT JOIN {$wpdb->prefix}postmeta AS postmeta
                    ON ({$wpdb->prefix}posts.ID = postmeta.post_id
                        AND postmeta.meta_key = 'promote_profile_substime')";

        // Only add if it's not already in there
        if (strpos($join_paged_statement, $join_to_add) === false) {
            $join_paged_statement = $join_paged_statement . $join_to_add;
        }

        return $join_paged_statement;
    }

    public function edit_orderby($orderby_statement, $wp_query)
    {
        if (
            !isset($wp_query->query) || $wp_query->is_page || (isset($wp_query->query['post_type']) && $wp_query->query['post_type'] != 'candidate')
        ) {
            return $orderby_statement;
        }

        $orderby_statement = "cast(postmeta.meta_value as unsigned) DESC, ID DESC";

        return $orderby_statement;
    }

    protected function _content_template()
    {
    }
}