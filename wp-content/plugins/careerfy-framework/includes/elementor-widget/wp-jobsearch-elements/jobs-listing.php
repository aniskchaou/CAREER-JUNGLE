<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

/**
 * @since 1.1.0
 */
class JobsListings extends Widget_Base
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
        return 'jobs-listings';
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
        return __('Jobs Listings', 'careerfy-frame');
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
        $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : 500;

        $job_alerts_switch = isset($jobsearch_plugin_options['job_alerts_switch']) ? $jobsearch_plugin_options['job_alerts_switch'] : '';
        $ads_management_switch = isset($jobsearch_plugin_options['ads_management_switch']) ? $jobsearch_plugin_options['ads_management_switch'] : '';

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

        $this->start_controls_section(
            'general',
            [
                'label' => __('General Settings', 'careerfy-frame'),
                'tab_1' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'job_view',
            [
                'label' => __('Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'view-default',
                'options' => [
                    'view-default' => __('Style 1', 'careerfy-frame'),
                    'view-medium' => __('Style 2', 'careerfy-frame'),
                    'view-listing2' => __('Style 3', 'careerfy-frame'),
                    'view-grid2' => __('Style 4', 'careerfy-frame'),
                    'view-medium2' => __('Style 5', 'careerfy-frame'),
                    'view-grid' => __('Style 6', 'careerfy-frame'),
                    'view-medium3' => __('Style 7', 'careerfy-frame'),
                    'view-grid3' => __('Style 8', 'careerfy-frame'),
                    'view-grid-4' => __('Style 9', 'careerfy-frame'),

                ],
            ]
        );
        $this->add_control(
            'job_cat',
            [
                'label' => __('Sector', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'options' => $cate_array,
            ]
        );

        $this->add_control(
            'display_per_page',
            [
                'label' => __('Job Founds with display counts', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Display the per page jobs count at top of the listing.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'featured_only',
            [
                'label' => __('Featured Only', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'description' => __("If you set Featured Only 'Yes' then only Featured jobs will show.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );


        $this->add_control(
            'job_sort_by',
            [
                'label' => __('Sort by Fields', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'description' => __("Results search sorting section switch. When choosing option yes then jobs display counts will show.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'job_loc_listing',
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
            'job_rss_feed',
            [
                'label' => __('RSS Feed', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'description' => __("If you set Featured Only 'Yes' then only Featured jobs will show.", "careerfy-frame"),
                'condition' => [
                    'job_sort_by' => 'yes',
                ],
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'job_excerpt',
            [
                'label' => __('Excerpt Length', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '20',
                'description' => __("Set the number of words you want to show for excerpt.", "careerfy-frame"),
            ]
        );
        $this->add_control(
            'job_order',
            [
                'label' => __('Order', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'description' => __("If you set Featured Only 'Yes' then only Featured jobs will show.", "careerfy-frame"),
                'options' => [
                    'DESC' => __('Descending', 'careerfy-frame'),
                    'ASC' => __('Ascending', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'job_orderby',
            [
                'label' => __('Orderby', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'description' => __("If you set Featured Only 'Yes' then only Featured jobs will show.", "careerfy-frame"),
                'options' => [
                    'date' => __('Date', 'careerfy-frame'),
                    'title' => __('Title', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'job_pagination',
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
            'job_per_page',
            [
                'label' => __('Jobs per Page', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '10',
                'description' => __("Set number that how much jobs you want to show per page. Leave it blank for all jobs on a single page.", "careerfy-frame"),
            ]
        );
        $this->add_control(
            'quick_apply_job',
            [
                'label' => __('Quick Apply job', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'off',
                'description' => __("By setting this option to yes, when user will click on job title or image, pop-up will be appear from the side.", "careerfy-frame"),
                'options' => [
                    'on' => __('Yes', 'careerfy-frame'),
                    'off' => __('No', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'job_deadline_switch',
            [
                'label' => __('Job Deadline', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'description' => __("Enable / Disable jobs deadline date in listings", "careerfy-frame"),
                'options' => [
                    'Yes' => __('Yes', 'careerfy-frame'),
                    'No' => __('No', 'careerfy-frame'),
                ],
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
                    'job_ad_banners',
                    [
                        'label' => __('Ad Banners', 'careerfy-frame'),
                        'type' => Controls_Manager::SELECT2,
                        'default' => 'no',
                        'description' => __("Show/hide ad banners in job listings.", "careerfy-frame"),
                        'options' => [
                            'yes' => __('Yes', 'careerfy-frame'),
                            'no' => __('No', 'careerfy-frame'),
                        ],
                    ]
                );
                $this->add_control(
                    'job_ad_after_list',
                    [
                        'label' => __('Ad after list count', 'careerfy-frame'),
                        'type' => Controls_Manager::TEXT,
                        'default' => '5',
                        'description' => __("Put number. After how many jobs list an ad banner will show. You can also add comma seprated numbers i.e. 2,5,7", "careerfy-frame"),

                    ]
                );


                $this->add_control(
                    'job_ad_banners_rep',
                    [
                        'label' => __('Repeat Banners Loop', 'careerfy-frame'),
                        'type' => Controls_Manager::SELECT2,
                        'default' => 'no',
                        'description' => __("Repeat ad banner after a specific number of jobs Or display add once.", "careerfy-frame"),
                        'options' => [
                            'yes' => __('Yes', 'careerfy-frame'),
                            'no' => __('No', 'careerfy-frame'),
                        ],
                    ]
                );

                $this->add_control(
                    'job_ads_group',
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

        if ($job_alerts_switch == 'on') {
            $this->add_control(
                'job_alerts_top',
                [
                    'label' => __('Job Alerts Top', 'careerfy-frame'),
                    'type' => Controls_Manager::SELECT2,
                    'default' => 'no',
                    'description' => __("Show/hide job alerts section at top of listings.", "careerfy-frame"),
                    'options' => [
                        'yes' => __('Yes', 'careerfy-frame'),
                        'no' => __('No', 'careerfy-frame'),
                    ],
                ]
            );
        }

        if ($job_alerts_switch == 'on') {
            $this->add_control(
                'job_alerts_bottom',
                [
                    'label' => __('Job Alerts Bottom', 'careerfy-frame'),
                    'type' => Controls_Manager::SELECT2,
                    'default' => 'no',
                    'description' => __("Show/hide job alerts section at bottom of listings.", "careerfy-frame"),
                    'options' => [
                        'yes' => __('Yes', 'careerfy-frame'),
                        'no' => __('No', 'careerfy-frame'),
                    ],
                ]
            );
        }


        $this->add_control(
            'job_filters_type_collapse',
            [
                'label' => __('Job Type Filter Collapse', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );


        $this->add_control(
            'job_filters_keyword',
            [
                'label' => __('Keyword Search', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'description' => __("Jobs filters 'Keyword Search' switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );


        if ($job_alerts_switch == 'on') {
            $this->add_control(
                'job_alerts',
                [
                    'label' => __('Job Alerts', 'careerfy-frame'),
                    'type' => Controls_Manager::SELECT2,
                    'default' => 'no',
                    'description' => __("Show/hide job alerts section in filters of listings.", "careerfy-frame"),
                    'options' => [
                        'yes' => __('Yes', 'careerfy-frame'),
                        'no' => __('No', 'careerfy-frame'),
                    ],
                ]
            );
        }


        $this->add_control(
            'job_filters',
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
            'job_filters_count',
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
            'job_filters_sortby',
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
            'job_filters_loc',
            [
                'label' => __('Locations', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Jobs searching filters 'Location' switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'job_filters_loc_collapse',
            [
                'label' => __('Locations Filter Collapse', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Locations Filter Collapse", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'job_filters_loc_view',
            [
                'label' => __('Locations Filter Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Locations Filter Style", "careerfy-frame"),
                'options' => [
                    'checkboxes' => __('Checkbox List', 'careerfy-frame'),
                    'input' => __('Input Field', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'job_filters_date',
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
            'job_filters_date_collapse',
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
            'job_filters_type',
            [
                'label' => __('Job Type', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Jobs searching filters 'Job Type' switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'job_filters_sector',
            [
                'label' => __('Sector', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Jobs searching filters 'Sector' switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'job_filters_sector_collapse',
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
            'employer-based-jobs',
            [
                'label' => __('Employer Based Jobs', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'jobs_emp_base',
            [
                'label' => __('Employer Base Jobs', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'jobs_emp_base_id',
            [
                'label' => __('Employer ID', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'description' => __("Put employer ID here.", "careerfy-frame"),
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'location-based-jobs',
            [
                'label' => __('Location Based Jobs', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );


        $this->add_control(
            'selct_loc_jobs',
            [
                'label' => __('Selected Location Jobs', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'jobsearch_gapi_locs_cntry',
            [
                'label' => __('Select Country', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,


            ]
        );
        $this->add_control(
            'jobsearch_gapi_locs_state',
            [
                'label' => __('Select State', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,

            ]
        );
        $this->add_control(
            'jobsearch_gapi_locs_city',
            [
                'label' => __('Select City', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,

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
            'job_top_map',
            [
                'label' => __('Jobs top map switch.', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'description' => __("Jobs top map switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'job_top_map_height',
            [
                'label' => __('Map Height', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'description' => __("Jobs top map height.", "careerfy-frame"),
            ]
        );
        $this->add_control(
            'job_top_map_zoom',
            [
                'label' => __('Map Zoom', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '8',
                'description' => __("Jobs top map zoom.", "careerfy-frame"),
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'featured-jobs',
            [
                'label' => __('Featured Jobs', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'job_feat_jobs_top',
            [
                'label' => __('Featured Jobs on Top', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'description' => __("Featured jobs will display on top of listing.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'num_of_feat_jobs',
            [
                'label' => __('Number of Featured jobs', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '',

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
            'job_top_search',
            [
                'label' => __('Top Search Bar', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'description' => __("Jobs top search bar switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'job_top_search_view',
            [
                'label' => __('Top Search Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'simple',
                'description' => __("Jobs top search bar switch.", "careerfy-frame"),
                'options' => [
                    'simple' => __('Simple', 'careerfy-frame'),
                    'advance' => __('Advance Search', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'job_top_search_radius',
            [
                'label' => __('Top Search Radius', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Enable/Disable top search radius.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'top_search_title',
            [
                'label' => __('Job Title, Keywords, or Phrase', 'careerfy-frame'),
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
            'top_search_location',
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
                'top_search_sector',
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
            'custom-field',
            [
                'label' => __('Custom Field', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'job_custom_fields_switch',
            [
                'label' => __('Custom Fields', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Enable / Disable job custom fields", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $jobsearch_job_cus_fields = get_option("jobsearch_custom_field_job");
        $job_cus_field_arr = array();
        if (isset($jobsearch_job_cus_fields) && !empty($jobsearch_job_cus_fields) && sizeof($jobsearch_job_cus_fields) > 0) {
            foreach ($jobsearch_job_cus_fields as $key => $value) {

                $job_cus_field_arr[$key] = $value['label'];
            }
        }

        $this->add_control(
            'job_elem_custom_fields',
            [
                'label' => __('Select Fields', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'description' => __("Enable / Disable job custom fields", "careerfy-frame"),
                'options' => $job_cus_field_arr,
                'multiple' => true,
            ]
        );

        $this->end_controls_section();
    }

    public function jobsearch_jobs_content($job_arg = '')
    {

        global $wpdb, $post, $jobsearch_form_fields, $jobsearch_search_fields, $jobsearch_plugin_options, $sitepress, $jobseacrh_jobsh_attslist;

        $page_id = isset($post->ID) ? $post->ID : '';

        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $trans_able_options = $sitepress->get_setting('custom_posts_sync_option', array());
        }

        $emporler_approval = isset($jobsearch_plugin_options['job_listwith_emp_aprov']) ? $jobsearch_plugin_options['job_listwith_emp_aprov'] : '';
        $is_filled_jobs = isset($jobsearch_plugin_options['job_allow_filled']) ? $jobsearch_plugin_options['job_allow_filled'] : '';

        $def_radius_unit = isset($jobsearch_plugin_options['top_search_radius_unit']) ? $jobsearch_plugin_options['top_search_radius_unit'] : '';

        wp_enqueue_script('jobsearch-job-functions-script');
        wp_enqueue_style('datetimepicker-style');
        wp_enqueue_script('datetimepicker-script');
        wp_enqueue_script('jquery-ui');

        do_action('jobsearch_notes_frontend_modal_popup');
        // getting arg array from ajax

        $page_url = '';
        $all_post_ids = array();

        if (isset($_REQUEST['job_arg']) && $_REQUEST['job_arg']) {
            $job_arg = stripslashes($_REQUEST['job_arg']);
            $job_arg = json_decode($job_arg);
            $job_arg = $this->toArray($job_arg);
            $page_url = isset($job_arg['page_url']) ? $job_arg['page_url'] : '';
        }
        if (isset($job_arg) && $job_arg != '' && !empty($job_arg)) {
            extract($job_arg);
        }
        $default_date_time_formate = 'd-m-Y H:i:s';
        // getting if user set it with his choice
        if (false === ($job_view = jobsearch_get_transient_obj('jobsearch_job_view' . $job_short_counter))) {
            $job_view = isset($atts['job_view']) ? $atts['job_view'] : '';
        }

        $element_job_sort_by = isset($atts['job_sort_by']) ? $atts['job_sort_by'] : 'no';
        $element_job_topmap = '';
        $element_job_map_position = isset($atts['job_map_position']) ? $atts['job_map_position'] : 'full';
        $element_job_layout_switcher = isset($atts['job_layout_switcher']) ? $atts['job_layout_switcher'] : 'no';
        $element_job_layout_switcher_view = isset($atts['job_layout_switcher_view']) ? $atts['job_layout_switcher_view'] : 'grid';
        $element_job_map_height = isset($atts['job_map_height']) ? $atts['job_map_height'] : 400;
        $element_job_footer = isset($atts['job_footer']) ? $atts['job_footer'] : 'no';
        $element_job_search_keyword = isset($atts['job_search_keyword']) ? $atts['job_search_keyword'] : 'no';

        $featured_only = isset($atts['featured_only']) ? $atts['featured_only'] : 'no';
        $element_job_recent_switch = isset($atts['job_recent_switch']) ? $atts['job_recent_switch'] : 'no';
        $job_job_urgent = isset($atts['job_urgent']) ? $atts['job_urgent'] : 'all';
        $job_type = isset($atts['job_type']) ? $atts['job_type'] : 'all';
        $job_filters_sidebar = isset($atts['job_filters']) ? $atts['job_filters'] : '';
        $job_right_sidebar_content = isset($content) ? $content : '';
        $jobsearch_job_sidebar = isset($atts['jobsearch_job_sidebar']) ? $atts['jobsearch_job_sidebar'] : '';
        $jobsearch_map_position = isset($atts['jobsearch_map_position']) && $atts['jobsearch_map_position'] != '' ? ($atts['jobsearch_map_position']) : 'right';

        $job_desc = isset($atts['job_desc']) ? $atts['job_desc'] : '';
        $job_cus_fields = isset($atts['job_cus_fields']) ? $atts['job_cus_fields'] : 'yes';

        $job_per_page = '-1';
        $pagination = 'no';
        $job_per_page = isset($atts['job_per_page']) ? $atts['job_per_page'] : '-1';
        $job_per_page = isset($_REQUEST['per-page']) && $_REQUEST['per-page'] > 0 ? jobsearch_esc_html($_REQUEST['per-page']) : $job_per_page;
        $pagination = isset($atts['job_pagination']) ? $atts['job_pagination'] : 'no';
        $filter_arr = array();
        $qryvar_sort_by_column = '';
        $element_filter_arr = array();
        $content_columns = 'jobsearch-column-12 jobsearch-typo-wrap'; // if filteration not true
        $paging_var = 'job_page';
        // Element fields in filter

        if (isset($_REQUEST['job_type']) && $_REQUEST['job_type'] != '') {
            $job_type = jobsearch_esc_html($_REQUEST['job_type']);
        }

        $skill_in = '';
        if (isset($_REQUEST['skill_in']) && $_REQUEST['skill_in'] != '') {
            $skill_in = jobsearch_esc_html($_REQUEST['skill_in']);
        }
        $loc_polygon_path = '';
        if (isset($_REQUEST['loc_polygon_path']) && $_REQUEST['loc_polygon_path'] != '') {
            $loc_polygon_path = jobsearch_esc_html(_REQUEST['loc_polygon_path']);
        }

        $search_title = isset($_REQUEST['search_title']) ? $_REQUEST['search_title'] : '';
        $job_sort_by = ''; // default value
        $job_sort_order = 'desc'; // default value

        if (isset($_REQUEST['sort-by']) && $_REQUEST['sort-by'] != '') {
            $job_sort_by = jobsearch_esc_html($_REQUEST['sort-by']);
        }
        $job_sort_by = apply_filters('jobsearch_joblistin_filter_sortby_str', $job_sort_by);

        $get_all_args = $this->jobs_list_args($atts);
        $args_count = $get_all_args['args_count'];
        $args = $get_all_args['args'];

        add_filter('posts_where', 'jobsearch_search_query_results_filter', 10, 2);
        $job_loop_obj = new \WP_Query($args);
        remove_filter('posts_where', 'jobsearch_search_query_results_filter', 10);
        $wpml_job_totnum = $job_totnum = $job_loop_obj->found_posts;

        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') && $wpml_job_totnum == 0 && isset($trans_able_options['job']) && $trans_able_options['job'] == '2') {
            $sitepress_def_lang = $sitepress->get_default_language();
            $sitepress_curr_lang = $sitepress->get_current_language();
            $sitepress->switch_lang($sitepress_def_lang, true);

            add_filter('posts_where', 'jobsearch_search_query_results_filter', 10, 2);
            $job_loop_obj = new \WP_Query($args);
            remove_filter('posts_where', 'jobsearch_search_query_results_filter', 10);
            $job_totnum = $job_loop_obj->found_posts;

            //
            $sitepress->switch_lang($sitepress_curr_lang, true);
        }
        //var_dump($job_loop_obj->request);

        $page_container_view = get_post_meta($page_id, 'careerfy_field_page_view', true);
        ?>
        <form id="jobsearch_job_frm_<?php echo absint($job_short_counter); ?>">
            <?php
            $listing_top_map = isset($atts['job_top_map']) ? $atts['job_top_map'] : '';
            $listing_top_map_zoom = isset($atts['job_top_map_zoom']) && $atts['job_top_map_zoom'] > 0 ? $atts['job_top_map_zoom'] : 8;
            $listing_top_map_height = isset($atts['job_top_map_height']) && $atts['job_top_map_height'] > 0 ? $atts['job_top_map_height'] : 450;
            if ($listing_top_map == 'yes') {
                //
                $location_map_type = isset($jobsearch_plugin_options['location_map_type']) ? $jobsearch_plugin_options['location_map_type'] : '';
                if ($location_map_type == 'mapbox') {
                    wp_enqueue_script('jobsearch-mapbox');
                } else {
                    wp_enqueue_script('jobsearch-google-map');
                    wp_enqueue_script('jobsearch-map-infobox');
                    wp_enqueue_script('jobsearch-map-markerclusterer');
                }
                wp_enqueue_script('jobsearch-job-lists-map');
                $map_style = isset($jobsearch_plugin_options['jobsearch-location-map-style']) ? $jobsearch_plugin_options['jobsearch-location-map-style'] : '';
                $map_zoom = $listing_top_map_zoom;
                $loc_def_adres = isset($jobsearch_plugin_options['jobsearch-location-default-address']) ? $jobsearch_plugin_options['jobsearch-location-default-address'] : '';

                $map_latitude = '51.2';
                $map_longitude = '0.2';

                if ($loc_def_adres != '' && !(isset($_REQUEST['location']))) {
                    $adre_to_cords = jobsearch_address_to_cords($loc_def_adres);
                    $map_latitude = isset($adre_to_cords['lat']) && $adre_to_cords['lat'] != '' ? $adre_to_cords['lat'] : $map_latitude;
                    $map_longitude = isset($adre_to_cords['lng']) && $adre_to_cords['lng'] != '' ? $adre_to_cords['lng'] : $map_longitude;
                }

                if (isset($_REQUEST['location']) && $_REQUEST['location'] != '') {
                    $url_get_loc = jobsearch_esc_html($_REQUEST['location']);
                    $adre_to_cords = jobsearch_address_to_cords($url_get_loc);
                    $map_latitude = isset($adre_to_cords['lat']) && $adre_to_cords['lat'] != '' ? $adre_to_cords['lat'] : $map_latitude;
                    $map_longitude = isset($adre_to_cords['lng']) && $adre_to_cords['lng'] != '' ? $adre_to_cords['lng'] : $map_longitude;
                }

                $map_marker_icon = isset($jobsearch_plugin_options['listin_map_marker_img']['url']) ? $jobsearch_plugin_options['listin_map_marker_img']['url'] : '';
                if ($map_marker_icon == '') {
                    $map_marker_icon = jobsearch_plugin_get_url('images/job_map_marker.png');
                }
                $map_cluster_icon = isset($jobsearch_plugin_options['listin_map_cluster_img']['url']) ? $jobsearch_plugin_options['listin_map_cluster_img']['url'] : '';
                if ($map_cluster_icon == '') {
                    $map_cluster_icon = jobsearch_plugin_get_url('images/map_cluster.png');
                }
                //
                $map_list_arr = array();
                $job_all_posts = $job_loop_obj->posts;

                $job_all_posts = !empty($job_all_posts) ? $job_all_posts : array();

                //
                $job_feat_jobs_top = isset($atts['job_feat_jobs_top']) ? $atts['job_feat_jobs_top'] : '';
                if ($job_feat_jobs_top == 'yes' && $featured_only != 'yes') {
                    $num_of_feat_jobs = isset($atts['num_of_feat_jobs']) ? $atts['num_of_feat_jobs'] : '';
                    $feat_jobs_per_page = $num_of_feat_jobs > 0 ? $num_of_feat_jobs : 5;

                    $fjobs_args = $args;

                    if (isset($fjobs_args['meta_query'])) {
                        $fe_args_mqury = $fjobs_args['meta_query'];
                        $fe_args_mqury = jobsearch_remove_exfeatkeys_jobs_query($fe_args_mqury, 'yes');
                        $fjobs_args['meta_query'] = $fe_args_mqury;
                    }

                    // for get feature num posts
                    $gnum_fjobs_args = $fjobs_args;
                    $gnum_fjobs_args['paged'] = '1';
                    $gnum_fjobs_args['posts_per_page'] = $job_per_page;
                    add_filter('posts_where', 'jobsearch_search_query_results_filter', 10, 2);
                    $gnum_fjobs_query = new \WP_Query($gnum_fjobs_args);
                    remove_filter('posts_where', 'jobsearch_search_query_results_filter', 10, 2);

                    add_filter('posts_where', 'jobsearch_search_query_results_filter', 10, 2);
                    $fjobs_query = new \WP_Query($fjobs_args);
                    remove_filter('posts_where', 'jobsearch_search_query_results_filter', 10, 2);

                    $gnum_fjobs_jobs = $fjobs_query->posts;
                    if (!empty($gnum_fjobs_jobs)) {
                        $job_all_posts = array_merge($job_all_posts, $gnum_fjobs_jobs);
                    }
                    wp_reset_postdata();
                }
                //

                foreach ($job_all_posts as $job_post) {
                    $listing_latitude = get_post_meta($job_post, 'jobsearch_field_location_lat', true);
                    $listing_longitude = get_post_meta($job_post, 'jobsearch_field_location_lng', true);

                    if ($listing_latitude != '' && $listing_longitude != '') {
                        //employer html
                        $pos_employer = get_post_meta($job_post, 'jobsearch_field_job_posted_by', true);
                        $pos_employer_html = '';
                        if ($pos_employer > 0) {
                            $pos_employer_html = esc_html__('Posted by', 'wp-jobsearch') . ' <a href="' . get_permalink($pos_employer) . '">' . get_the_title($pos_employer) . '</a>';
                        }
                        //sectors html
                        $get_pos_sectrs = wp_get_post_terms($job_post, 'sector');
                        $map_pos_sectrs_html = '';
                        if (!empty($get_pos_sectrs)) {
                            $map_secpage_id = isset($jobsearch_plugin_options['jobsearch_search_list_page']) ? $jobsearch_plugin_options['jobsearch_search_list_page'] : '';
                            $map_secpage_id = jobsearch__get_post_id($map_secpage_id, 'page');
                            $map_secresult_page = get_permalink($map_secpage_id);
                            $map_pos_sectrs_html .= ' ' . esc_html__('in', 'wp-jobsearch') . ' ';
                            foreach ($get_pos_sectrs as $get_pos_sectr) {
                                $map_pos_sectrs_html .= '<a href="' . add_query_arg(array('sector_cat' => $get_pos_sectr->slug, 'ajax_filter' => 'true'), $map_secresult_page) . '">' . $get_pos_sectr->name . '</a> ';
                            }
                        }
                        //logo img
                        $map_pos_thum_id = jobsearch_job_get_profile_image($job_post);
                        $map_pos_thumb_image = wp_get_attachment_image_src($map_pos_thum_id, 'thumbnail');
                        $map_pos_thumb_src = isset($map_pos_thumb_image[0]) && esc_url($map_pos_thumb_image[0]) != '' ? $map_pos_thumb_image[0] : '';
                        $map_pos_thumb_src = $map_pos_thumb_src == '' ? jobsearch_no_image_placeholder() : $map_pos_thumb_src;

                        //address
                        $map_posadres = jobsearch_job_item_address($job_post);
                        if ($map_posadres != '') {
                            $map_posadres = '<div class="map-info-adres"><i class="jobsearch-icon jobsearch-maps-and-flags"></i> ' . $map_posadres . '</div>';
                        }

                        if ($location_map_type == 'mapbox') {
                            $map_list_arr[] = array(
                                'type' => 'Feature',
                                'geometry' => array(
                                    'type' => 'Point',
                                    'coordinates' => array($listing_longitude, $listing_latitude)
                                ),
                                'properties' => array(
                                    'id' => $job_post,
                                    'title' => wp_trim_words(get_the_title($job_post), 5),
                                    'link' => get_permalink($job_post),
                                    'logo_img_url' => $map_pos_thumb_src,
                                    'address' => $map_posadres,
                                    'employer' => $pos_employer_html,
                                    'sector' => $map_pos_sectrs_html,
                                    'marker' => $map_marker_icon,
                                )
                            );
                        } else {
                            $map_list_arr[] = array(
                                'lat' => $listing_latitude,
                                'long' => $listing_longitude,
                                'id' => $job_post,
                                'title' => wp_trim_words(get_the_title($job_post), 5),
                                'link' => get_permalink($job_post),
                                'logo_img_url' => $map_pos_thumb_src,
                                'address' => $map_posadres,
                                'employer' => $pos_employer_html,
                                'sector' => $map_pos_sectrs_html,
                                'marker' => $map_marker_icon,
                            );
                        }
                    }
                }
                //
                $listn_map_arr = array(
                    'map_id' => $job_short_counter,
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
                <div class="jobsearch-listing-mapcon <?php echo($atts['job_top_search'] != 'no' ? 'with-serch-map-both' : '') ?>">
                    <div id="listings-map-<?php echo absint($job_short_counter); ?>" class="jobsearch-joblist-map"
                         style="height: <?php echo($listing_top_map_height) ?>px;"></div>
                </div>
                <?php
                $map_html = ob_get_clean();
                echo apply_filters('jobsearch_jobs_listin_topmap_html', $map_html, $listn_map_obj, $job_short_counter, $listing_top_map_height, $atts);
                if ($page_container_view == 'wide') {
                    echo '<div class="container">';
                }
            } ?>
            <div style="display:none" id='job_arg<?php echo absint($job_short_counter); ?>'>
                <?php
                $jobs_arggs = apply_filters('jobsearch_injobsh_ajax_args_list', $job_arg);
                echo json_encode($jobs_arggs);
                ?>
            </div>
            <?php
            if ($atts['job_top_search'] != 'no') {

                $top_serch_style = isset($atts['job_top_search_view']) ? $atts['job_top_search_view'] : '';

                $top_search_title = isset($atts['top_search_title']) && !empty($atts['top_search_title']) ? $atts['top_search_title'] : 'yes';
                $top_search_location = isset($atts['top_search_location']) && !empty($atts['top_search_location']) ? $atts['top_search_location'] : 'yes';
                $top_search_sector = isset($atts['top_search_sector']) && !empty($atts['top_search_sector']) ? $atts['top_search_sector'] : 'yes';
                $the_top_search_radius = isset($atts['job_top_search_radius']) ? $atts['job_top_search_radius'] : 'yes';

                $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';

                $search_title_val = isset($_REQUEST['search_title']) ? $_REQUEST['search_title'] : '';
                $location_val = isset($_REQUEST['location']) ? $_REQUEST['location'] : '';
                $location_val = stripslashes($location_val);

                $cat_sector_val = isset($_REQUEST['sector_cat']) ? urldecode($_REQUEST['sector_cat']) : '';

                $search_title_val = jobsearch_esc_html($search_title_val);
                $location_val = jobsearch_esc_html($location_val);
                $cat_sector_val = jobsearch_esc_html($cat_sector_val);

                $search_main_class = '';
                $adv_search_on = '';
                if ($top_serch_style == 'advance') {
                    $search_main_class = 'jobsearch-advance-search-holdr';
                    $adv_search_on = 'has-advance-search';
                }
                if ($listing_top_map == 'yes') {
                    $search_main_class .= ' search-with-map';
                }
                $job_filters_sector = isset($atts['job_filters_sector']) ? $atts['job_filters_sector'] : '';
                $job_filters_location = isset($atts['job_filters_loc']) ? $atts['job_filters_loc'] : '';
                $without_sectr_class = 'search-cat-off';
                if (($top_search_sector == 'yes' && $sectors_enable_switch == 'on') || ($top_search_sector == 'no' && $sectors_enable_switch == 'no')) {
                    $without_sectr_class = '';
                }

                $without_loc_class = 'search-loc-off';
                if ($top_search_location == 'yes') {
                    $without_loc_class = '';
                }

                $without_keyword_class = 'search-keyword-off';
                if ($top_search_title == 'yes') {
                    $without_keyword_class = '';
                }

                $top_search_autofill = isset($atts['top_search_autofill']) ? $atts['top_search_autofill'] : '';
                $top_search_locsugg = isset($jobsearch_plugin_options['top_search_locsugg']) ? $jobsearch_plugin_options['top_search_locsugg'] : '';
                $top_search_geoloc = isset($jobsearch_plugin_options['top_search_geoloc']) ? $jobsearch_plugin_options['top_search_geoloc'] : '';
                $top_search_def_radius = isset($jobsearch_plugin_options['top_search_def_radius']) ? $jobsearch_plugin_options['top_search_def_radius'] : 50;
                $top_search_max_radius = isset($jobsearch_plugin_options['top_search_max_radius']) ? $jobsearch_plugin_options['top_search_max_radius'] : 500;
                $top_search_radius = isset($jobsearch_plugin_options['top_search_radius']) ? $jobsearch_plugin_options['top_search_radius'] : '';

                ob_start();
                ?>
                <div class="jobsearch-top-searchbar jobsearch-typo-wrap <?php echo apply_filters('jobsearch_joblistn_thesrch_mainclass', $search_main_class) ?> <?php echo $adv_search_on ?>">
                    <!-- Sub Header Form -->
                    <div class="jobsearch-subheader-form">
                        <div class="jobsearch-banner-search <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?>">
                            <ul class="<?php echo apply_filters('jobsearch_jobs_listins_topsrch_ul_class', 'jobsearch-jobs-topsrchul', $atts) ?>">
                                <?php
                                if ($top_search_title != 'no') {
                                    if ($top_search_autofill != 'no') {
                                        wp_enqueue_script('jobsearch-search-box-sugg');
                                    }
                                    ?>
                                    <li>
                                        <div class="<?php echo($top_search_autofill != 'no' ? 'jobsearch-sugges-search' : '') ?>">
                                            <input placeholder="<?php echo apply_filters('jobsearch_listin_serchbox_keyphrase_title', esc_html__('Job Title, Keywords, or Phrase', 'wp-jobsearch')) ?>"
                                                   class="jobsearch-keywordsrch-inp-field" name="search_title"
                                                   value="<?php echo($search_title_val) ?>"
                                                   data-type="job" type="text">
                                            <span class="sugg-search-loader"></span>
                                        </div>
                                    </li>
                                <?php }
                                if ($top_search_location == 'yes') { ?>
                                    <li>
                                        <div class="jobsearch_searchloc_div">
                                            <span class="loc-loader"></span>
                                            <?php
                                            $citystat_zip_title = esc_html__('City, State or ZIP', 'wp-jobsearch');

                                            if ($top_search_locsugg == 'no') { ?>
                                                <input placeholder="<?php echo apply_filters('jobsearch_listin_serchbox_location_title', $citystat_zip_title) ?>"
                                                       class="<?php echo($top_search_geoloc != 'no' ? 'srch_autogeo_location' : '') ?>"
                                                       name="location"
                                                       value="<?php echo urldecode($location_val) ?>"
                                                       type="text">

                                            <?php } else {
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
                                                ?>
                                                <input placeholder="<?php echo apply_filters('jobsearch_listin_serchbox_location_title', $citystat_zip_title) ?>"
                                                       autocomplete="off" class="jobsearch_search_location_field"
                                                       value="<?php echo urldecode($location_val) ?>" type="text">
                                                <input type="hidden"
                                                       class="loc_search_keyword <?php echo($top_search_geoloc != 'no' ? 'srch_autogeo_location' : '') ?>"
                                                       name="location"
                                                       value="<?php echo urldecode($location_val) ?>">
                                            <?php }


                                            if ($top_search_radius == 'yes' && $the_top_search_radius == 'yes' && $top_serch_style != 'advance') { ?>
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
                                        if ($top_search_geoloc != 'no') { ?>
                                            <a href="javascript:void(0);" class="geolction-btn"
                                               onclick="JobsearchGetClientLocation()"><i
                                                        class="jobsearch-icon jobsearch-location"></i></a>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                                <?php
                                if ($sectors_enable_switch == 'on') {
                                    if ($top_search_sector == 'yes') {
                                        ob_start();
                                        $sectors_args = array(
                                            'orderby' => 'name',
                                            'order' => 'ASC',
                                            'fields' => 'all',
                                            'hide_empty' => false,
                                        );
                                        $all_sectors = get_terms('sector', $sectors_args);

                                        $selsector_title = esc_html__('Select Sector', 'wp-jobsearch');
                                        ?>
                                        <li>
                                            <div class="jobsearch-select-style">
                                                <select name="sector_cat" class="selectize-select"
                                                        placeholder="<?php echo apply_filters('jobsearch_listin_serchbox_selsector_title', $selsector_title) ?>">
                                                    <option value=""><?php echo apply_filters('jobsearch_listin_serchbox_selsector_title', $selsector_title) ?></option>
                                                    <?php
                                                    if (!empty($all_sectors)) {
                                                        echo jobsearch_sector_terms_hierarchical(0, $all_sectors, '', 0, 0, $cat_sector_val);
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </li>
                                        <?php
                                        $top_sects_html = ob_get_clean();
                                        echo apply_filters('jobsearch_jobs_listin_top_srch_sectors', $top_sects_html);
                                    }
                                }

                                if ($top_serch_style == 'advance') {
                                    ob_start();
                                    ?>
                                    <li class="adv-srch-toggler">
                                        <a href="javascript:void(0);"
                                           class="adv-srch-toggle-btn"><span>+</span> <?php esc_html_e('Advance Search', 'wp-jobsearch') ?>
                                        </a>
                                    </li>
                                    <?php
                                    $advsrch_btn_html = ob_get_clean();
                                    echo apply_filters('jobsearch_joblistn_advsrch_btn_html', $advsrch_btn_html);
                                } ?>
                                <li class="jobsearch-banner-submit">
                                    <input type="hidden" name="ajax_filter" value="true">
                                    <input id="jobsearch-jobadvserach-submit" type="submit" value=""> <i
                                            class="jobsearch-icon jobsearch-search"></i>
                                </li>
                            </ul>
                            <?php
                            if ($top_serch_style == 'advance') {
                                $sh_atts = isset($job_arg['atts']) ? $job_arg['atts'] : '';
                                $top_search_radius = isset($jobsearch_plugin_options['top_search_radius']) ? $jobsearch_plugin_options['top_search_radius'] : '';
                                $top_search_def_radius = isset($jobsearch_plugin_options['top_search_def_radius']) ? $jobsearch_plugin_options['top_search_def_radius'] : 50;
                                $top_search_max_radius = isset($jobsearch_plugin_options['top_search_max_radius']) ? $jobsearch_plugin_options['top_search_max_radius'] : 500;
                                ?>
                                <div class="<?php echo apply_filters('jobsearch_joblistn_advsrch_conclass', 'adv-search-options') ?>">
                                    <?php
                                    echo apply_filters('jobsearch_joblistn_advsrch_bfrul_html', '');
                                    ?>
                                    <ul>
                                        <?php
                                        if ($top_search_radius != 'no' && $the_top_search_radius != 'no') {
                                            ?>
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
                                                          class="radius-numvr-holdr"><?php echo esc_html($tpsrch_complete_str); ?></span>
                                                    <span class="radius-punit"><?php echo($to_radius_unit) ?></span>
                                                    <input type="hidden" id="loc-def-radiusval"
                                                           value="<?php echo esc_html($tpsrch_complete_str) ?>">
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
                                                            var initSlideWidthPerc = (<?php echo($tpsrch_complete_str) ?>/<?php echo absint($tpsrch_field_max); ?>)*100;
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
                                        echo apply_filters('jobsearch_job_top_filter_date_posted_box_html', '', $job_short_counter, $sh_atts);
                                        echo apply_filters('jobsearch_job_top_filter_jobtype_box_html', '', $job_short_counter, $sh_atts);
                                        //echo apply_filters('jobsearch_job_top_filter_sector_box_html', '', $job_short_counter, $sh_atts);
                                        echo apply_filters('jobsearch_custom_fields_top_filters_html', '', 'job', $job_short_counter, $sh_atts);
                                        ?>
                                    </ul>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <!-- Sub Header Form -->
                </div>
                <?php
                $top_srch_html = ob_get_clean();
                echo apply_filters('jobsearch_joblistin_topsrch_whole_html', $top_srch_html, $job_short_counter, $atts);
            }
            ?>
            <div class="jobsearch-row">
                <?php
                set_query_var('job_type', $job_type);
                set_query_var('job_short_counter', $job_short_counter);
                set_query_var('job_arg', $job_arg);
                set_query_var('job_view', $job_view);
                set_query_var('args_count', $args_count);
                if (isset($job_right_sidebar_content)) {
                    set_query_var('job_right_sidebar_content', $job_right_sidebar_content);
                }
                set_query_var('atts', $atts);
                set_query_var('job_totnum', $job_totnum);
                set_query_var('page_url', $page_url);
                set_query_var('job_loop_obj', $job_loop_obj);
                set_query_var('global_rand_id', $job_short_counter);
                if (($job_filters_sidebar == 'yes') || (!empty($jobsearch_job_sidebar))) {  // if sidebar on from element
                    jobsearch_get_template_part('filters', 'job-template', 'jobs');
                    $content_columns = 'jobsearch-column-9 jobsearch-typo-wrap';
                } else {
                    $content_columns = 'jobsearch-column-12 jobsearch-typo-wrap';
                }
                ?>
                <div class="<?php echo esc_html($content_columns); ?>">
                    <?php do_action('jobsearch_jobs_listing_before', array('sh_atts' => (isset($job_arg['atts']) ? $job_arg['atts'] : ''))); ?>
                    <div class="wp-jobsearch-job-content jobsearch-listing-maincon wp-jobsearch-dev-job-content"
                         id="jobsearch-data-job-content-<?php echo esc_html($job_short_counter); ?>"
                         data-id="<?php echo esc_html($job_short_counter); ?>">
                        <div id="jobsearch-loader-<?php echo esc_html($job_short_counter); ?>"></div>
                        <?php
                        $jobs_title = isset($atts['jobs_title']) ? $atts['jobs_title'] : '';
                        $jobs_subtitle = isset($atts['jobs_subtitle']) ? $atts['jobs_subtitle'] : '';
                        $jobs_title_alignment = isset($atts['jobs_title_alignment']) ? $atts['jobs_title_alignment'] : '';
                        $job_element_seperator = isset($atts['jobsearch_jobs_seperator_style']) ? $atts['jobsearch_jobs_seperator_style'] : '';
                        $jobsearch_jobs_element_title_color = isset($atts['jobsearch_jobs_element_title_color']) ? $atts['jobsearch_jobs_element_title_color'] : '';
                        $jobsearch_jobs_element_subtitle_color = isset($atts['jobsearch_jobs_element_subtitle_color']) ? $atts['jobsearch_jobs_element_subtitle_color'] : '';
                        $element_title_color = '';
                        if (isset($jobsearch_jobs_element_title_color) && $jobsearch_jobs_element_title_color != '') {
                            $element_title_color = ' style="color:' . $jobsearch_jobs_element_title_color . ' ! important;"';
                        }
                        $element_subtitle_color = '';
                        if (isset($jobsearch_jobs_element_subtitle_color) && $jobsearch_jobs_element_subtitle_color != '') {
                            $element_subtitle_color = ' style="color:' . $jobsearch_jobs_element_subtitle_color . ' ! important;"';
                        }
                        if ($jobs_title != '' || $jobs_subtitle != '') {
                            ?>
                            <div class="row">
                                <div class="jobsearch-column-12 jobsearch-typo-wrap">
                                    <div class="element-title <?php echo($jobs_title_alignment); ?>">
                                        <?php
                                        if ($jobs_title != '' || $jobs_subtitle != '') {
                                            if ($jobs_title != '') {
                                                ?>
                                                <h2<?php echo force_balance_tags($element_title_color); ?>><?php echo esc_html($jobs_title); ?></h2>
                                                <?php
                                            }
                                            if ($jobs_subtitle != '') {
                                                ?>
                                                <p <?php echo force_balance_tags($element_subtitle_color); ?>><?php echo esc_html($jobs_subtitle); ?></p>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }

                        //
                        $pagi_totjobs = $job_totnum;
                        $pagi_perpjobs = $job_per_page;
                        $total_jobpages = 1;
                        if ($pagi_perpjobs > 0 && $pagi_totjobs > $pagi_perpjobs) {
                            $total_jobpages = ceil($pagi_totjobs / $pagi_perpjobs);
                        }

                        // only ajax request procced
                        // top featured jobs
                        $job_feat_jobs_top = isset($atts['job_feat_jobs_top']) ? $atts['job_feat_jobs_top'] : '';
                        if ($job_feat_jobs_top == 'yes' && $featured_only != 'yes') {
                            $num_of_feat_jobs = isset($atts['num_of_feat_jobs']) ? $atts['num_of_feat_jobs'] : '';
                            $feat_jobs_per_page = $num_of_feat_jobs > 0 ? $num_of_feat_jobs : 5;

                            $fjobs_args = $args;

                            $fjobs_args['post_type'] = 'job';

                            if (isset($fjobs_args['meta_query'])) {
                                $fe_args_mqury = $fjobs_args['meta_query'];
                                $fe_args_mqury = jobsearch_remove_exfeatkeys_jobs_query($fe_args_mqury, 'yes');
                                $fjobs_args['meta_query'] = $fe_args_mqury;
                            }

                            // for get feature num posts
                            $gnum_fjobs_args = $fjobs_args;
                            $gnum_fjobs_args['paged'] = '1';
                            $gnum_fjobs_args['posts_per_page'] = $job_per_page;
                            add_filter('posts_where', 'jobsearch_search_query_results_filter', 10, 2);
                            $gnum_fjobs_query = new \WP_Query($gnum_fjobs_args);
                            remove_filter('posts_where', 'jobsearch_search_query_results_filter', 10, 2);

                            $gnum_fjobs_foundjobs = $gnum_fjobs_query->found_posts;
                            wp_reset_postdata();
                            //
                            $fjobs_args['posts_per_page'] = $feat_jobs_per_page;
                            add_filter('posts_where', 'jobsearch_search_query_results_filter', 10, 2);
                            $fjobs_query = new \WP_Query($fjobs_args);
                            remove_filter('posts_where', 'jobsearch_search_query_results_filter', 10, 2);

                            $fjobs_found_posts = $fjobs_query->found_posts;
                            $fjobs_post_count = $fjobs_query->post_count;

                            $fjobs_posts = $fjobs_query->posts;

                            //$fjobs_posts = array();
                            wp_reset_postdata();
                            set_query_var('featjobs_posts', $fjobs_posts);
                        }

                        //
                        if (isset($job_view)) {
                            $search_keyword_html = apply_filters('jobsearch_search_keywordss', '', $page_url, $atts);
                            echo($search_keyword_html);

                            $top_feat_postfound = 0;
                            $top_show_totjobs = $job_totnum;
                            //var_dump($gnum_fjobs_foundjobs);
                            if (isset($gnum_fjobs_foundjobs) && $gnum_fjobs_foundjobs > 0) {
                                $top_show_totjobs = $job_totnum + $gnum_fjobs_foundjobs;
                                $top_feat_postfound = $gnum_fjobs_foundjobs;
                            }
                            // sorting fields
                            $this->job_search_sort_fields($atts, $job_sort_by, $job_short_counter, $job_view, $job_totnum, $job_per_page, $top_show_totjobs, $top_feat_postfound);
                        }

                        set_query_var('job_loop_obj', $job_loop_obj);
                        set_query_var('job_view', $job_view);
                        set_query_var('job_desc', $job_desc);
                        set_query_var('job_cus_fields', $job_cus_fields);
                        set_query_var('job_short_counter', $job_short_counter);
                        set_query_var('atts', $atts);
                        jobsearch_get_template_part($job_view, 'job-template', 'jobs');
                        echo apply_filters('jobsearch_after_jobs_list_template', '', $job_short_counter, $atts);
                        wp_reset_postdata();
                        ?>
                    </div>
                    <?php do_action('jobsearch_jobs_listing_after', array('sh_atts' => (isset($job_arg['atts']) ? $job_arg['atts'] : ''))); ?>
                    <?php
                    // apply paging
                    $paging_args = array(
                        'total_posts' => $pagi_totjobs,
                        'job_per_page' => $pagi_perpjobs,
                        'paging_var' => $paging_var,
                        'show_pagination' => $pagination,
                        'job_short_counter' => $job_short_counter,
                    );
                    $this->jobsearch_job_pagination_callback($paging_args);
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
                    'classes' => 'job-counter',
                    'std' => $job_short_counter,
                )
            );

            //
            if ($listing_top_map == 'yes') {
                if ($page_container_view == 'wide') {
                    echo '</div>';
                }
            }
            ?>

        </form>
        <?php
        do_action('jobsearch_after_jobs_listing_content', $args, $job_sort_by);
        // only for ajax request
        if (isset($_REQUEST['action']) && strpos($_REQUEST['action'], 'jobsearch_') !== false) {
            die();
        }
    }

    public function jobs_list_args($atts = array())
    {
        global $wpdb, $jobseacrh_jobsh_attslist;

        $jobsearch__options = get_option('jobsearch_plugin_options');
        $emporler_approval = isset($jobsearch__options['job_listwith_emp_aprov']) ? $jobsearch__options['job_listwith_emp_aprov'] : '';

        $is_filled_jobs = isset($jobsearch__options['job_allow_filled']) ? $jobsearch__options['job_allow_filled'] : '';

        $def_radius_unit = isset($jobsearch__options['top_search_radius_unit']) ? $jobsearch__options['top_search_radius_unit'] : '';

        if (empty($atts) && isset($_GET['sh_atts']) && !empty($_GET['sh_atts'])) {
            $get_query_atts = $_GET['sh_atts'];
            $get_query_atts = explode('|', $get_query_atts);
            foreach ($get_query_atts as $_query_att) {
                $_query_att_exp = explode(':', $_query_att);
                $_query_att_key = isset($_query_att_exp[0]) ? $_query_att_exp[0] : '';
                $_query_att_val = isset($_query_att_exp[1]) ? $_query_att_exp[1] : '';
                if ($_query_att_key != '' && $_query_att_val != '') {
                    $atts[$_query_att_key] = $_query_att_val;
                }
            }
        }

        if (!empty($atts)) {
            extract($atts);
        }

        //
        $element_job_sort_by = isset($atts['job_sort_by']) ? $atts['job_sort_by'] : 'no';
        $element_job_topmap = '';
        $element_job_map_position = isset($atts['job_map_position']) ? $atts['job_map_position'] : 'full';
        $element_job_layout_switcher = isset($atts['job_layout_switcher']) ? $atts['job_layout_switcher'] : 'no';
        $element_job_layout_switcher_view = isset($atts['job_layout_switcher_view']) ? $atts['job_layout_switcher_view'] : 'grid';
        $element_job_map_height = isset($atts['job_map_height']) ? $atts['job_map_height'] : 400;
        $element_job_footer = isset($atts['job_footer']) ? $atts['job_footer'] : 'no';
        $element_job_search_keyword = isset($atts['job_search_keyword']) ? $atts['job_search_keyword'] : 'no';

        $featured_only = isset($atts['featured_only']) ? $atts['featured_only'] : 'no';
        $element_job_recent_switch = isset($atts['job_recent_switch']) ? $atts['job_recent_switch'] : 'no';
        $job_job_urgent = isset($atts['job_urgent']) ? $atts['job_urgent'] : 'all';
        $job_type = isset($atts['job_type']) ? $atts['job_type'] : 'all';
        $job_filters_sidebar = isset($atts['job_filters']) ? $atts['job_filters'] : '';
        $job_right_sidebar_content = isset($content) ? $content : '';
        $jobsearch_job_sidebar = isset($atts['jobsearch_job_sidebar']) ? $atts['jobsearch_job_sidebar'] : '';
        $jobsearch_map_position = isset($atts['jobsearch_map_position']) && $atts['jobsearch_map_position'] != '' ? ($atts['jobsearch_map_position']) : 'right';

        $job_desc = isset($atts['job_desc']) ? $atts['job_desc'] : '';
        $job_cus_fields = isset($atts['job_cus_fields']) ? $atts['job_cus_fields'] : 'yes';

        $job_per_page = '-1';
        $pagination = 'no';
        $job_per_page = isset($atts['job_per_page']) ? $atts['job_per_page'] : '-1';
        $job_per_page = isset($_REQUEST['per-page']) && $_REQUEST['per-page'] > 0 ? jobsearch_esc_html($_REQUEST['per-page']) : $job_per_page;
        $pagination = isset($atts['job_pagination']) ? $atts['job_pagination'] : 'no';
        $filter_arr = array();
        $qryvar_sort_by_column = '';
        $element_filter_arr = array();
        $content_columns = 'jobsearch-column-12 jobsearch-typo-wrap'; // if filteration not true
        $paging_var = 'job_page';
        //

        if (get_query_var('location') != '' && !isset($_REQUEST['location'])) {
            $get_queryvar_loc = get_query_var('location');
            $_REQUEST['location'] = $get_queryvar_loc;
        }

        if (isset($_REQUEST['job_type']) && $_REQUEST['job_type'] != '') {
            $job_type = $_REQUEST['job_type'];
        }

        $skill_in = '';
        if (isset($_REQUEST['skill_in']) && $_REQUEST['skill_in'] != '') {
            $skill_in = jobsearch_esc_html($_REQUEST['skill_in']);
        }

        // posted date check
//            $element_filter_arr[] = array(
//                'key' => 'jobsearch_field_job_publish_date',
//                'value' => current_time('timestamp'),
//                'compare' => '<=',
//            );
//
//            $element_filter_arr[] = array(
//                'key' => 'jobsearch_field_job_expiry_date',
//                'value' => current_time('timestamp'),
//                'compare' => '>=',
//            );
//
//            $element_filter_arr[] = array(
//                'key' => 'jobsearch_field_job_status',
//                'value' => 'approved',
//                'compare' => '=',
//            );

        if ($is_filled_jobs == 'on') {
            $element_filter_arr[] = array(
                'relation' => 'OR',
                array(
                    'key' => 'jobsearch_field_job_filled',
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    array(
                        'key' => 'jobsearch_field_job_filled',
                        'value' => 'on',
                        'compare' => '!=',
                    ),
                ),
            );
        }

        if ($emporler_approval != 'off') {
            $element_filter_arr[] = array(
                'key' => 'jobsearch_job_employer_status',
                'value' => 'approved',
                'compare' => '=',
            );
        }

        //
        if (isset($atts['jobs_emp_base']) && $atts['jobs_emp_base'] == 'yes' && isset($atts['jobs_emp_base_id']) && $atts['jobs_emp_base_id'] > 0) {
            $base_emp_id = $atts['jobs_emp_base_id'];
            $element_filter_arr[] = array(
                'key' => 'jobsearch_field_job_posted_by',
                'value' => $base_emp_id,
                'compare' => '=',
            );
        }

        $job_feat_jobs_top = isset($atts['job_feat_jobs_top']) ? $atts['job_feat_jobs_top'] : '';

        if ($featured_only == 'yes') {
            $element_filter_arr[] = array(
                'key' => 'jobsearch_field_job_featured',
                'value' => 'on',
                'compare' => '=',
            );
        } else if (isset($_REQUEST['sort-by']) && $_REQUEST['sort-by'] == 'featured') {
            $element_filter_arr[] = array(
                'key' => 'jobsearch_field_job_featured',
                'value' => 'on',
                'compare' => '=',
            );
        }
        if ($job_feat_jobs_top == 'yes' && $featured_only != 'yes') {
            $element_filter_arr[] = array(
                'key' => 'jobsearch_field_job_featured',
                'value' => 'on',
                'compare' => '!=',
            );
        }

        if (function_exists('jobsearch_visibility_query_args')) {
            $element_filter_arr = jobsearch_visibility_query_args($element_filter_arr);
        }

        if (!isset($_REQUEST[$paging_var])) {
            $_REQUEST[$paging_var] = '';
        }

        // Get all arguments from getting flters.
        $left_filter_arr = $this->get_filter_arg();

        $post_ids = $all_post_ids = array();

        $orig_post_ids = $post_ids = $this->job_general_query_filter($post_ids, $atts);

        if (!empty($left_filter_arr)) {
            // apply all filters and get ids
            $post_ids = $this->get_job_id_by_filter($left_filter_arr, 'job', $post_ids);
        }

        //
        $post_ids = $this->job_location_filter($post_ids, $atts);
        //

        $loc_polygon_path = '';
        if (isset($_REQUEST['loc_polygon_path']) && $_REQUEST['loc_polygon_path'] != '') {
            $loc_polygon_path = jobsearch_esc_html($_REQUEST['loc_polygon_path']);
        }

        $search_title = isset($_REQUEST['search_title']) ? jobsearch_esc_html($_REQUEST['search_title']) : '';

        $radius_locpost_ids = $this->location_radius_filter_ids();
        //var_dump($radius_locpost_ids);
        if (!empty($radius_locpost_ids)) {
            if (!empty($post_ids)) {
                $post_ids = array_intersect($post_ids, $radius_locpost_ids);
            } else {
                $post_ids = $radius_locpost_ids;
            }
        }

        if (empty($orig_post_ids)) {
            $post_ids = array();
        }

        if (!empty($post_ids)) {
            $all_post_ids = $post_ids;
        }
        if (empty($all_post_ids)) {
            $all_post_ids = array(0);
        }

        /*
         * used for relevance sort by filter
         */

        if (isset($_REQUEST['loc_radius']) && $_REQUEST['loc_radius'] > 0 && isset($_REQUEST['location'])) {
            $jobsearch_loc_address = jobsearch_esc_html($_REQUEST['location']);
            $radius = jobsearch_esc_html($_REQUEST['loc_radius']);

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

                $minLat = ($minLat);
                $maxLat = ($maxLat);
                $minLong = ($minLong);
                $maxLong = ($maxLong);

                //echo $minLat . '<br>';
                //echo $maxLat . '<br>';
                //echo $minLong . '<br>';
                //echo $maxLong . '<br>';
                $jobsearch_compare_type = 'DECIMAL';
                if ($radius > 0) {
                    //$jobsearch_compare_type = 'DECIMAL(10,6)';
                }
            }
        }

        $element_filter_arr = apply_filters('jobsearch_jobs_listing_query_meta_fields', $element_filter_arr);

        /*
         * End used for relevance sort by filter
         */

        $args_count = array(
            'posts_per_page' => "1",
            'post_type' => 'job',
            'post_status' => 'publish',
            'suppress_filters' => false,
            'fields' => 'ids', // only load ids
            'meta_query' => array(
                $element_filter_arr,
            ),
        );

        if (isset($_REQUEST['sector_cat']) && $_REQUEST['sector_cat'] != '') {
            $sec_terms_arr = array();
            $sec_terms_str = jobsearch_esc_html($_REQUEST['sector_cat']);
            $sec_terms_expl = explode(',', $sec_terms_str);
            if (!empty($sec_terms_expl)) {
                foreach ($sec_terms_expl as $sec_term_expl) {
                    $sec_terms_arr[] = urldecode($sec_term_expl);
                }
            }
            $args_count['tax_query'][] = array(
                'taxonomy' => 'sector',
                'field' => 'slug',
                'terms' => $sec_terms_arr
            );
        } else if (isset($atts['job_cat']) && $atts['job_cat'] != '') {
            $args_count['tax_query'][] = array(
                'taxonomy' => 'sector',
                'field' => 'slug',
                'terms' => $atts['job_cat']
            );
        }

        if ($job_type != '' && $job_type != 'all') {
            $args_count['tax_query'][] = array(
                'taxonomy' => 'jobtype',
                'field' => 'slug',
                'terms' => $job_type,
            );
        }
        if ($skill_in != '' && $skill_in != 'all') {
            $args_count['tax_query'][] = array(
                'taxonomy' => 'skill',
                'field' => 'slug',
                'terms' => $skill_in,
            );
        }
        $job_sort_by = ''; // default value
        $job_sort_order = 'desc'; // default value

        if (isset($_REQUEST['sort-by']) && $_REQUEST['sort-by'] != '') {
            $job_sort_by = jobsearch_esc_html($_REQUEST['sort-by']);
        }
        $meta_key = 'jobsearch_field_job_publish_date';
        $qryvar_job_sort_type = 'DESC';
        $qryvar_sort_by_column = 'meta_value_num';
        if (isset($atts['job_orderby']) && $atts['job_orderby'] != '') {
            if ($atts['job_orderby'] == 'date') {
                $qryvar_sort_by_column = 'meta_value_num';
            } else {
                $qryvar_sort_by_column = $atts['job_orderby'];
            }
        }
        if (isset($atts['job_order']) && $atts['job_order'] != '') {
            $qryvar_job_sort_type = $atts['job_order'];
        }

        $job_sort_by = apply_filters('jobsearch_joblistin_filter_sortby_str', $job_sort_by);
        if ($job_sort_by == 'recent') {
            $meta_key = 'jobsearch_field_job_publish_date';
            $qryvar_job_sort_type = 'DESC';
            $qryvar_sort_by_column = 'meta_value_num';
        } elseif ($job_sort_by == 'alphabetical') {
            $qryvar_job_sort_type = 'ASC';
            $qryvar_sort_by_column = 'title';
        } elseif ($job_sort_by == 'most_viewed') {
            $qryvar_job_sort_type = 'DESC';
            $qryvar_sort_by_column = 'meta_value_num';
            $meta_key = 'jobsearch_job_views_count';
        } elseif ($job_sort_by == 'featured') {
            $qryvar_job_sort_type = 'DESC';
            $qryvar_sort_by_column = 'meta_value';
            $meta_key = 'jobsearch_field_job_featured';
        }

        $qryvar_job_sort_type = apply_filters('jobsearch_joblistin_sort_order', $qryvar_job_sort_type, $atts);
        $qryvar_sort_by_column = apply_filters('jobsearch_joblistin_sort_orderby', $qryvar_sort_by_column, $atts);
        $meta_key = apply_filters('jobsearch_joblistin_sort_order_metakey', $meta_key, $atts);

        $args = array(
            'posts_per_page' => $job_per_page,
            'paged' => $_REQUEST[$paging_var],
            'post_type' => 'job',
            'post_status' => 'publish',
            'order' => $qryvar_job_sort_type,
            'orderby' => $qryvar_sort_by_column,
            'meta_key' => $meta_key,
            'fields' => 'ids', // only load ids
            'meta_query' => array(
                $element_filter_arr,
            ),
        );

        if (isset($_REQUEST['sector_cat']) && $_REQUEST['sector_cat'] != '') {
            $sec_terms_arr = array();
            $sec_terms_str = jobsearch_esc_html($_REQUEST['sector_cat']);
            $sec_terms_expl = explode(',', $sec_terms_str);
            if (!empty($sec_terms_expl)) {
                foreach ($sec_terms_expl as $sec_term_expl) {
                    $sec_terms_arr[] = urldecode($sec_term_expl);
                }
            }
            $args['tax_query'][] = array(
                'taxonomy' => 'sector',
                'field' => 'slug',
                'terms' => $sec_terms_arr
            );
        } else if (isset($atts['job_cat']) && $atts['job_cat'] != '') {
            $args['tax_query'][] = array(
                'taxonomy' => 'sector',
                'field' => 'slug',
                'terms' => $atts['job_cat']
            );
        }

        if ($job_type != '' && $job_type != 'all') {
            $args['tax_query'][] = array(
                'taxonomy' => 'jobtype',
                'field' => 'slug',
                'terms' => $job_type,
            );
        }
        if ($skill_in != '' && $skill_in != 'all') {
            $args['tax_query'][] = array(
                'taxonomy' => 'skill',
                'field' => 'slug',
                'terms' => $skill_in,
            );
        }

        // recent job query end
        if (!empty($all_post_ids)) {
            $args_count['post__in'] = $all_post_ids;
            $args['post__in'] = $all_post_ids;
        }
        //
        $args_count = apply_filters('jobsearch_job_listing_query_argscount_array', $args_count, $atts);
        $args = apply_filters('jobsearch_job_listing_query_args_array', $args, $atts);
        return array(
            'args' => $args,
            'args_count' => $args_count
        );
    }

    public function get_filter_arg($job_short_counter = '', $exclude_meta_key = '')
    {
        global $jobsearch_post_job_types;
        $filter_arr = array();
        $posted = '';
        $default_date_time_formate = 'd-m-Y H:i:s';
        $current_timestamp = current_time('timestamp');
        if (isset($_REQUEST['posted'])) {
            $posted = jobsearch_esc_html($_REQUEST['posted']);
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
                    'key' => 'jobsearch_field_job_publish_date',
                    'value' => strtotime($lastdate),
                    'compare' => '>=',
                );
            }
        }
        // custom field array for filteration from custom field module
        $filter_arr = apply_filters('jobsearch_custom_fields_load_filter_array_html', 'job', $filter_arr, $exclude_meta_key);
        return $filter_arr;
    }
    
    public function get_job_id_by_filter($left_filter_arr, $post_type = 'job', $post_ids = array())
    {
        global $wpdb;
        $meta_post_ids_arr = '';
        $job_id_condition = '';

        if (isset($left_filter_arr) && !empty($left_filter_arr)) {
            $meta_post_ids_arr = jobsearch_get_query_whereclase_by_array($left_filter_arr);
            // if no result found in filtration
            if (empty($meta_post_ids_arr)) {
                $meta_post_ids_arr = array(0);
            }
            if (empty($post_ids)) {
                $meta_post_ids_arr = '';
            } else {
                if (!empty($meta_post_ids_arr) && is_array($meta_post_ids_arr)) {
                    if (isset($post_ids[0]) && $post_ids[0] == '0') {
                        $meta_post_ids_arr = $meta_post_ids_arr;
                    } else {
                        $meta_post_ids_arr = array_intersect($post_ids, $meta_post_ids_arr);
                    }
                }
            }

            //var_dump($meta_post_ids_arr);
            $ids = !empty($meta_post_ids_arr) ? implode(",", $meta_post_ids_arr) : '0';
            $job_id_condition = " ID in (" . $ids . ") AND ";
        }

        $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $job_id_condition . " post_type='" . $post_type . "' AND post_status='publish'");

        if (empty($post_ids)) {
            $post_ids = array(0);
        }
        return $post_ids;
    }

    public function job_general_query_filter($all_post_ids, $atts = array())
    {
        global $wpdb;
        $current_time = current_time('timestamp');
        $query = "SELECT ID FROM $wpdb->posts AS posts";
        $query .= " LEFT JOIN $wpdb->postmeta AS postmeta ON postmeta.post_id=posts.ID";
        $query .= " LEFT JOIN $wpdb->postmeta AS mt1 ON mt1.post_id=posts.ID";
        $query .= " LEFT JOIN $wpdb->postmeta AS mt2 ON mt2.post_id=posts.ID";
        $query .= " WHERE posts.post_type=%s AND posts.post_status='publish'";
        if (!empty($all_post_ids)) {
            $post_ids = implode(',', $all_post_ids);
            $query .= " AND posts.ID IN ($post_ids)";
        }
        $query .= " AND (postmeta.meta_key='jobsearch_field_job_publish_date' AND postmeta.meta_value <= $current_time)";
        $query .= " AND (mt1.meta_key='jobsearch_field_job_expiry_date' AND mt1.meta_value >= $current_time)";
        $query .= " AND (mt2.meta_key='jobsearch_field_job_status' AND mt2.meta_value='approved')";
        $query .= " ORDER BY ID DESC";

        $job_ids = $wpdb->get_col($wpdb->prepare($query, 'job'));

        return $job_ids;
    }

    public function job_location_filter($all_post_ids, $atts = array())
    {
        global $sitepress;
        if (get_query_var('location') != '' && !isset($_REQUEST['location'])) {
            $get_queryvar_loc = get_query_var('location');
            $_REQUEST['location'] = $get_queryvar_loc;
        }

        $jobsearch__options = get_option('jobsearch_plugin_options');
        $all_locations_type = isset($jobsearch__options['all_locations_type']) ? $jobsearch__options['all_locations_type'] : '';

        $radius = isset($_REQUEST['loc_radius']) ? $_REQUEST['loc_radius'] : '';

        $location_rslt = $all_post_ids;
        $location_location1 = isset($_REQUEST['location_location1']) ? jobsearch_esc_html($_REQUEST['location_location1']) : '';
        $location_location2 = isset($_REQUEST['location_location2']) ? jobsearch_esc_html($_REQUEST['location_location2']) : '';
        $location_location3 = isset($_REQUEST['location_location3']) ? jobsearch_esc_html($_REQUEST['location_location3']) : '';
        $location_location4 = isset($_REQUEST['location_location4']) ? jobsearch_esc_html($_REQUEST['location_location4']) : '';

        if ($all_locations_type == 'api') {

            if (isset($atts['selct_loc_jobs']) && $atts['selct_loc_jobs'] == 'yes') {
                $api_loc_selectd = isset($atts['selct_gapiloc_str']) ? $atts['selct_gapiloc_str'] : '';

                $api_loc_selectd = explode('|', $api_loc_selectd);
                $api_contry_selectd = isset($api_loc_selectd[0]) ? $api_loc_selectd[0] : '';
                $api_state_selectd = isset($api_loc_selectd[1]) ? $api_loc_selectd[1] : '';
                $api_city_selectd = isset($api_loc_selectd[2]) ? $api_loc_selectd[2] : '';
                if (!isset($_REQUEST['location_location1']) && $api_contry_selectd != '' && $api_contry_selectd != 'undefined') {
                    $location_location1 = $api_contry_selectd;
                }
                if (!isset($_REQUEST['location_location2']) && $api_state_selectd != '' && $api_state_selectd != 'undefined') {
                    $location_location2 = $api_state_selectd;
                }
                if (!isset($_REQUEST['location_location3']) && $api_city_selectd != '' && $api_city_selectd != 'undefined') {
                    $location_location3 = $api_city_selectd;
                }
            }
        } else {
            if (isset($atts['selct_loc_jobs']) && $atts['selct_loc_jobs'] == 'yes') {
                $api_contry_selectd = isset($atts['jobsearch_gapi_locs_cntry']) ? $atts['jobsearch_gapi_locs_cntry'] : '';
                $api_state_selectd = isset($atts['jobsearch_gapi_locs_state']) ? $atts['jobsearch_gapi_locs_state'] : '';
                $api_city_selectd = isset($atts['selct_gapiloc_str']) ? $atts['jobsearch_gapi_locs_city'] : '';
                if (!isset($_REQUEST['location_location1']) && $api_contry_selectd != '' && $api_contry_selectd != 'undefined') {
                    $location_location1 = $api_contry_selectd;
                }
                if (!isset($_REQUEST['location_location2']) && $api_state_selectd != '' && $api_state_selectd != 'undefined') {
                    $location_location2 = $api_state_selectd;
                }
                if (!isset($_REQUEST['location_location3']) && $api_city_selectd != '' && $api_city_selectd != 'undefined') {
                    $location_location3 = $api_city_selectd;
                }
            }
        }

        if (isset($_REQUEST['location']) && $_REQUEST['location'] != '') {
            if (isset($_POST['action'])) {
                $loc_decod_str = jobsearch_esc_html($_REQUEST['location']);
            } else {
                //$loc_decod_str = urlencode($_REQUEST['location']);
                $loc_decod_str = jobsearch_esc_html($_REQUEST['location']);
            }

            if ($all_locations_type == 'api') {

            } else {
                $get_loc_tax = get_term_by('name', $loc_decod_str, 'job-location');
                if (isset($get_loc_tax->slug) && $get_loc_tax->slug != '') {
                    $loc_decod_str = $get_loc_tax->slug;
                } else {
                    $loc_decod_str_test = urlencode($_REQUEST['location']);
                    $get_loc_tax = get_term_by('name', $loc_decod_str_test, 'job-location');
                    if (isset($get_loc_tax->slug) && $get_loc_tax->slug != '') {
                        $loc_decod_str = $get_loc_tax->slug;
                    }
                }
            }

            $location_condition_arr = array(
                'relation' => 'OR',
            );

            $location_condition_arr[] = array(
                'key' => 'jobsearch_field_location_address',
                'value' => stripslashes($loc_decod_str),
                'compare' => 'LIKE',
            );
            $location_condition_arr[] = array(
                'key' => 'jobsearch_field_location_location1',
                'value' => stripslashes($loc_decod_str),
                'compare' => 'LIKE',
            );
            $location_condition_arr[] = array(
                'key' => 'jobsearch_field_location_location2',
                'value' => stripslashes($loc_decod_str),
                'compare' => 'LIKE',
            );
            $location_condition_arr[] = array(
                'key' => 'jobsearch_field_location_location3',
                'value' => stripslashes($loc_decod_str),
                'compare' => 'LIKE',
            );
            $location_condition_arr[] = array(
                'key' => 'jobsearch_field_location_location4',
                'value' => stripslashes($loc_decod_str),
                'compare' => 'LIKE',
            );

            $location_condition_arr = apply_filters('jobsearch_job_listin_loc_custmquery_meta_arrs', $location_condition_arr);

            $args_count = array(
                'posts_per_page' => "-1",
                'post_type' => 'job',
                'post_status' => 'publish',
                'fields' => 'ids', // only load ids
                'meta_query' => array(
                    $location_condition_arr,
                ),
            );

            //var_dump($all_post_ids);
            if (!empty($all_post_ids)) {
                $args_count['post__in'] = $all_post_ids;
            }
            $location_rslt = get_posts($args_count);
            if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
                $trans_able_options = $sitepress->get_setting('custom_posts_sync_option', array());
                if (empty($location_rslt) && isset($trans_able_options['job']) && $trans_able_options['job'] == '2') {
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

                    $location_query = new WP_Query($args_count);
                    wp_reset_postdata();
                    $location_rslt = $location_query->posts;

                    $sitepress->switch_lang($sitepress_curr_lang, true);
                }
            }
            if (empty($location_rslt)) {
                $location_rslt = array(0);
            }
        } else if ($location_location1 != '' || $location_location2 != '' || $location_location3 != '' || $location_location4 != '') {

            $location_condition_arr = array(
                'relation' => 'AND',
            );

            if ($location_location1 != '' && $location_location2 == 'other-cities') {
                $location_condition_arr[] = array(
                    'key' => 'jobsearch_field_location_location1',
                    'value' => $location_location1,
                    'compare' => '!=',
                );
            } else {
                if ($location_location1 != '') {
                    $location_condition_arr[] = array(
                        'key' => 'jobsearch_field_location_location1',
                        'value' => $location_location1,
                        'compare' => 'LIKE',
                    );
                }
                if ($location_location2 != '') {
                    $location_condition_arr[] = array(
                        'key' => 'jobsearch_field_location_location2',
                        'value' => $location_location2,
                        'compare' => 'LIKE',
                    );
                }
                if ($location_location3 != '') {
                    $location_condition_arr[] = array(
                        'key' => 'jobsearch_field_location_location3',
                        'value' => $location_location3,
                        'compare' => 'LIKE',
                    );
                }
                if ($location_location4 != '') {
                    $location_condition_arr[] = array(
                        'key' => 'jobsearch_field_location_location4',
                        'value' => $location_location4,
                        'compare' => 'LIKE',
                    );
                }
            }
            $location_condition_arr = apply_filters('jobsearch_job_listin_loc_custmquery_meta_arrs', $location_condition_arr);

            $args_count = array(
                'posts_per_page' => "-1",
                'post_type' => 'job',
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
                //var_dump($location_rslt);
                if (isset($trans_able_options['job']) && $trans_able_options['job'] == '2') {
                    $sitepress_def_lang = $sitepress->get_default_language();
                    $sitepress_curr_lang = $sitepress->get_current_language();

                    $location1 = isset($_REQUEST['location_location1']) ? $_REQUEST['location_location1'] : '';
                    if ($location1 != '') {
                        $loc_taxnomy = jobsearch_get_custom_term_by('slug', $location1, 'job-location');
                        if (isset($loc_taxnomy->term_id)) {
                            $location1_id = $loc_taxnomy->term_id;
                            $default_tax_id = icl_object_id($location1_id, 'job-location', true, $sitepress_def_lang);
                            $sitepress->switch_lang($sitepress_def_lang, true);
                            $loc_taxnomy = jobsearch_get_custom_term_by('term_id', $default_tax_id, 'job-location');
                            $sitepress->switch_lang($sitepress_curr_lang, true);
                        }
                        if (is_object($loc_taxnomy) && isset($loc_taxnomy->slug)) {
                            $args_count['meta_query'][0][0]['value'] = $loc_taxnomy->slug;
                        }
                    }
                    $location2 = isset($_REQUEST['location_location2']) ? $_REQUEST['location_location2'] : '';
                    if ($location2 != '') {
                        $loc_taxnomy = jobsearch_get_custom_term_by('slug', $location2, 'job-location');
                        if (isset($loc_taxnomy->term_id)) {
                            $location1_id = $loc_taxnomy->term_id;
                            $default_tax_id = icl_object_id($location1_id, 'job-location', true, $sitepress_def_lang);
                            $sitepress->switch_lang($sitepress_def_lang, true);
                            $loc_taxnomy = jobsearch_get_custom_term_by('term_id', $default_tax_id, 'job-location');
                            $sitepress->switch_lang($sitepress_curr_lang, true);
                        }
                        if (is_object($loc_taxnomy) && isset($loc_taxnomy->slug)) {
                            $args_count['meta_query'][0][1]['value'] = $loc_taxnomy->slug;
                        }
                    }
                    $location3 = isset($_REQUEST['location_location3']) ? $_REQUEST['location_location3'] : '';
                    if ($location3 != '') {
                        $loc_taxnomy = jobsearch_get_custom_term_by('slug', $location3, 'job-location');
                        if (isset($loc_taxnomy->term_id)) {
                            $location1_id = $loc_taxnomy->term_id;
                            $default_tax_id = icl_object_id($location1_id, 'job-location', true, $sitepress_def_lang);
                            $sitepress->switch_lang($sitepress_def_lang, true);
                            $loc_taxnomy = jobsearch_get_custom_term_by('term_id', $default_tax_id, 'job-location');
                            $sitepress->switch_lang($sitepress_curr_lang, true);
                        }
                        if (is_object($loc_taxnomy) && isset($loc_taxnomy->slug)) {
                            $args_count['meta_query'][0][2]['value'] = $loc_taxnomy->slug;
                        }
                    }

                    $location_query = new WP_Query($args_count);
                    wp_reset_postdata();
                    $location_rslt = $location_query->posts;

                    //var_dump($location_rslt);
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
        $location_rslt = apply_filters('jobsearch_jobs_locs_queryargs_postids', $location_rslt, $all_post_ids, $atts);

        return $location_rslt;
    }

    public function location_radius_filter_ids()
    {
        global $wpdb, $jobsearch_plugin_options, $wp_filesystem;

        if (isset($_REQUEST['loc_radius']) && $_REQUEST['loc_radius'] > 0 && isset($_REQUEST['location'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            WP_Filesystem();

            $countries_file = jobsearch_plugin_get_path('modules/import-locations/data/countries.json');
            $get_json_data = $wp_filesystem->get_contents($countries_file);
            $countries_data = json_decode($get_json_data, true);
            $all_countries_data = isset($countries_data['countries']) ? $countries_data['countries'] : array();

            $default_selctd_contry = isset($jobsearch_plugin_options['restrict_contries_locsugg']) && $jobsearch_plugin_options['restrict_contries_locsugg'] != '' ? $jobsearch_plugin_options['restrict_contries_locsugg'] : '';
            $country_name = getCountryNamebyCode($default_selctd_contry, $all_countries_data);

            $def_radius_unit = isset($jobsearch_plugin_options['top_search_radius_unit']) ? $jobsearch_plugin_options['top_search_radius_unit'] : '';
            $current_time = current_time('timestamp');
            $jobsearch_loc_address = $_REQUEST['location'] . ($country_name != '' ? $country_name : '');
            //

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

//                echo $minLat . '<br>';
//                echo $maxLat . '<br>';
//                echo $minLong . '<br>';
//                echo $maxLong . '<br>';

                $posts_query = "SELECT posts.ID FROM $wpdb->posts AS posts";
                $posts_query .= " LEFT JOIN $wpdb->postmeta AS postmeta ON(posts.ID = postmeta.post_id)";
                $posts_query .= " LEFT JOIN $wpdb->postmeta AS mt1 ON(posts.ID = mt1.post_id)";
                $posts_query .= " LEFT JOIN $wpdb->postmeta AS mt2 ON(posts.ID = mt2.post_id)";
                $posts_query .= " LEFT JOIN $wpdb->postmeta AS mt3 ON(posts.ID = mt3.post_id)";
                $posts_query .= " LEFT JOIN $wpdb->postmeta AS mt4 ON(posts.ID = mt4.post_id)";
                $posts_query .= " WHERE posts.post_type = '%s' AND posts.post_status = 'publish'";
                $posts_query .= " AND postmeta.meta_key = 'jobsearch_field_job_publish_date' AND postmeta.meta_value <= {$current_time}";
                $posts_query .= " AND mt1.meta_key = 'jobsearch_field_job_expiry_date' AND mt1.meta_value >= {$current_time}";
                $posts_query .= " AND mt2.meta_key = 'jobsearch_field_job_status' AND mt2.meta_value = 'approved'";
                $posts_query .= " AND (mt3.meta_key = 'jobsearch_field_location_lat' AND mt3.meta_value BETWEEN {$minLat} AND {$maxLat})";
                $posts_query .= " AND (mt4.meta_key = 'jobsearch_field_location_lng' AND mt4.meta_value BETWEEN {$minLong} AND {$maxLong})";

                $wpdb->prepare($posts_query, 'job');

                $all_posts = $wpdb->get_col($wpdb->prepare($posts_query, 'job'));

                if (empty($all_posts)) {
                    $all_posts = array('0');
                }

                return $all_posts;
            }
        }
    }

    public function job_layout_switcher_fields($atts, $job_short_counter, $view = '', $frc_view = false)
    {

        $counter = isset($atts['job_counter']) && $atts['job_counter'] != '' ? $atts['job_counter'] : '';
        $transient_view = jobsearch_get_transient_obj('jobsearch_job_view' . $counter);

        if ($frc_view == true) {
            $view = $view;
        } else {
            if (false === ($view = jobsearch_get_transient_obj('jobsearch_job_view' . $counter))) {
                $view = isset($atts['job_view']) ? $atts['job_view'] : '';
            }
        }
        if ((isset($atts['job_layout_switcher']) && $atts['job_layout_switcher'] != 'no')) {

            if (isset($atts['job_layout_switcher_view']) && !empty($atts['job_layout_switcher_view'])) {
                $job_layout_switcher_views = array(
                    'grid' => esc_html__('grid', 'wp-jobsearch'),
                    'list' => esc_html__('list', 'wp-jobsearch'),
                );
                ?>
                <ul class="jobs-views-switcher-holder">
                    <li><?php echo esc_html__('jobsearch_view_jobs_by_switcher'); ?></li>
                    <?php
                    $element_job_layout_switcher_view = explode(',', $atts['job_layout_switcher_view']);

                    if (!empty($element_job_layout_switcher_view) && is_array($element_job_layout_switcher_view)) {
                        $views_counter = 0;
                        foreach ($element_job_layout_switcher_view as $single_layout_view) {
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
                                                class="icon-th-list"></i><?php echo esc_html($job_layout_switcher_views[$single_layout_view]); ?>
                                    </a></li>
                                <?php
                            } else {
                                $view_type = '';
                                ?>
                                <li class="<?php echo esc_html($view_class); ?>"><a
                                            href="javascript:void(0);" <?php if ($view == $single_layout_view) echo 'class="active"'; ?> <?php if ($view != $single_layout_view) { ?> onclick="jobsearch_job_view_switch('<?php echo esc_html($single_layout_view) ?>', '<?php echo esc_html($job_short_counter); ?>', '<?php echo esc_html($counter); ?>', '<?php echo esc_html($view_type); ?>');"<?php } ?>><?php echo force_balance_tags($icon); ?></a>
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

    public function toArray($obj)
    {
        if (is_object($obj)) {
            $obj = (array)$obj;
        }
        if (is_array($obj)) {
            $new = array();
            foreach ($obj as $key => $val) {
                $new[$key] = $this->toArray($val);
            }
        } else {
            $new = $obj;
        }

        return $new;
    }

    public function job_geolocation_filter($location_slug, $all_post_ids, $radius)
    {
        global $jobsearch_plugin_options;
        $distance_symbol = isset($jobsearch_plugin_options['jobsearch_distance_measure_by']) ? $jobsearch_plugin_options['jobsearch_distance_measure_by'] : 'km';
        if ($distance_symbol == 'km') {
            $radius = $radius / 1.60934; // 1.60934 == 1 Mile
        }
        if (isset($location_slug) && $location_slug != '') {
            $Jobsearch_Locations = new \Jobsearch_Locations();
            $location_response = $Jobsearch_Locations->jobsearch_get_geolocation_latlng_callback($location_slug);
            $lat = isset($location_response->lat) ? $location_response->lat : '';
            $lng = isset($location_response->lng) ? $location_response->lng : '';
            $radiusCheck = new \RadiusCheck($lat, $lng, $radius);
            $minLat = $radiusCheck->MinLatitude();
            $maxLat = $radiusCheck->MaxLatitude();
            $minLong = $radiusCheck->MinLongitude();
            $maxLong = $radiusCheck->MaxLongitude();
            $jobsearch_compare_type = 'CHAR';
            if ($radius > 0) {
                $jobsearch_compare_type = 'DECIMAL(10,6)';
            }
            $location_condition_arr = array(
                'relation' => 'OR',
                array(
                    'key' => 'jobsearch_field_location_lat',
                    'value' => array($minLat, $maxLat),
                    'compare' => 'BETWEEN',
                    'type' => $jobsearch_compare_type
                ),
                array(
                    'key' => 'jobsearch_field_location_lng',
                    'value' => array($minLong, $maxLong),
                    'compare' => 'BETWEEN',
                    'type' => $jobsearch_compare_type
                ),
                array(
                    'key' => 'jobsearch_field_location_location1',
                    'value' => $location_slug,
                    'compare' => 'LIKE',
                ),
                array(
                    'key' => 'jobsearch_field_location_location1',
                    'value' => sanitize_title($location_slug),
                    'compare' => 'LIKE',
                ),
            );
            $args_count = array(
                'posts_per_page' => "-1",
                'post_type' => 'job',
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
            return $location_rslt;
            $rslt = '';
        }
    }

    public function job_search_sort_fields($atts, $job_sort_by, $job_short_counter, $view = '', $job_totnum = '', $job_per_page = '', $withtop_feature_jobnum = 0, $topfeat_postfounds = 0)
    {
        global $jobsearch_form_fields;

        $page_url = '';
        if (isset($_REQUEST['job_arg']) && $_REQUEST['job_arg'] != '') {
            $job_args = $_REQUEST['job_arg'];
            $job_args_arr = json_decode(jobsearch_esc_html($job_args), true);
            $page_url = isset($job_args_arr['page_url']) ? $job_args_arr['page_url'] : '';
        }

        $rss_feeds_switch = isset($atts['job_rss_feed']) ? $atts['job_rss_feed'] : '';

        $counter = isset($atts['job_counter']) && $atts['job_counter'] != '' ? $atts['job_counter'] : '';
        $transient_view = jobsearch_get_transient_obj('jobsearch_job_view' . $counter);
        $view = isset($transient_view) && $transient_view != '' ? $transient_view : $view;

        $job_type_slug = isset($_REQUEST['job_type']) ? $_REQUEST['job_type'] : '';
        $job_type_text = $job_type_slug;
        if (isset($job_type_slug) && !empty($job_type_slug) && $job_type_slug != 'all') {
            if ($post = get_page_by_path($job_type_slug, OBJECT, 'job-type')) {
                $id = $post->ID;
                $job_type_text = get_the_title($id);
            }
        }

        $view_type = '';

        $job_feat_jobs_top = isset($atts['job_feat_jobs_top']) ? $atts['job_feat_jobs_top'] : '';

        if ((isset($atts['job_sort_by']) && $atts['job_sort_by'] != 'no')) {

            echo '<div class="sortfiltrs-contner' . ($rss_feeds_switch != 'no' ? ' with-rssfeed-enable' : '') . '">';
            //
            echo apply_filters('jobsearch_job_listin_before_top_jobfounds_html', '', $withtop_feature_jobnum, $job_short_counter, $atts, $topfeat_postfounds);
            //
            $paging_var = 'job_page';
            $pagination = isset($atts['job_pagination']) ? $atts['job_pagination'] : 'no';
            $paging_args = array(
                'total_posts' => $job_totnum,
                'job_per_page' => $job_per_page,
                'paging_var' => $paging_var,
                'show_pagination' => $pagination,
                'job_short_counter' => $job_short_counter,
            );
            echo apply_filters('jobsearch_job_listin_before_sort_orders', '', $paging_args, $atts);
            ?>
            <div class="jobsearch-filterable jobsearch-filter-sortable">
                <?php
                ob_start();
                ?>
                <h2>
                    <?php
                    echo absint($withtop_feature_jobnum) . '&nbsp;';
                    if ($withtop_feature_jobnum == 1) {
                        echo esc_html__('Job Found', 'wp-jobsearch');
                    } else {
                        echo esc_html__('Jobs Found', 'wp-jobsearch');
                    }
                    do_action('jobsearch_job_listin_sh_after_jobs_found', $withtop_feature_jobnum, $job_short_counter, $atts, $topfeat_postfounds);
                    ?>
                </h2>
                <?php
                $foundjobs_html = ob_get_clean();
                echo apply_filters('jobsearch_job_listin_top_jobfounds_html', $foundjobs_html, $withtop_feature_jobnum, $job_short_counter, $atts);
                ?>
                <ul class="jobsearch-sort-section">
                    <?php
                    do_action('jobsearch_job_listin_sh_before_topsort_items', $job_short_counter, $atts);
                    ?>
                    <li>
                        <i class="jobsearch-icon jobsearch-sort"></i>
                        <div class="jobsearch-filterable-select">

                            <?php
                            $sortby_option = array(
                                'recent' => esc_html__('Most Recent', 'wp-jobsearch'),
                                'featured' => esc_html__('Featured', 'wp-jobsearch'),
                                'alphabetical' => esc_html__('Alphabet Order', 'wp-jobsearch'),
                                'most_viewed' => esc_html__('Most Viewed', 'wp-jobsearch')
                            );
                            $sortby_option = apply_filters('jobsearch_jobslistin_top_sort_options', $sortby_option);
                            $cs_opt_array = array(
                                'cus_id' => '',
                                'cus_name' => 'sort-by',
                                'force_std' => $job_sort_by,
                                'desc' => '',
                                'classes' => 'selectize-select',
                                'ext_attr' => ' onchange="jobsearch_job_content_load(\'' . esc_js($job_short_counter) . '\')" placeholder="' . esc_html__('Most Recent', 'wp-jobsearch') . '"',
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
                            $paging_options = array();
                            $paging_options[""] = esc_html__("Records Per Page", "wp-jobsearch");
                            $paging_options["10"] = '10 ' . esc_html__("Per Page", "wp-jobsearch");
                            $paging_options["20"] = '20 ' . esc_html__("Per Page", "wp-jobsearch");
                            $paging_options["30"] = '30 ' . esc_html__("Per Page", "wp-jobsearch");
                            $paging_options["50"] = '50 ' . esc_html__("Per Page", "wp-jobsearch");
                            $paging_options["70"] = '70 ' . esc_html__("Per Page", "wp-jobsearch");
                            $paging_options["100"] = '100 ' . esc_html__("Per Page", "wp-jobsearch");
                            $paging_options["200"] = '200 ' . esc_html__("Per Page", "wp-jobsearch");
                            $cs_opt_array = array(
                                'cus_id' => '',
                                'cus_name' => 'per-page',
                                'force_std' => $job_per_page,
                                'desc' => '',
                                'classes' => 'sort-records-per-page',
                                'ext_attr' => ' onchange="jobsearch_job_content_load(\'' . esc_js($job_short_counter) . '\')" placeholder="' . esc_html__('Records Per Page', 'wp-jobsearch') . '"',
                                'options' => apply_filters('jobsearch_joblistin_topsort_paginum_options', $paging_options, $atts, $topfeat_postfounds),
                            );

                            $jobsearch_form_fields->select_field($cs_opt_array);
                            ?>
                        </div>
                    </li>
                    <?php
                    if ($rss_feeds_switch != 'no') {
                        $all_uri_query = array();
                        if (!empty($_REQUEST)) {
                            foreach ($_REQUEST as $_query_req_key => $_query_req_val) {
                                if ($_query_req_key == 'woocommerce-login-nonce' || $_query_req_key == '_wpnonce' || $_query_req_key == 'job_arg' || $_query_req_key == 'ajax_filter') {
                                    continue;
                                }
                                if ($_query_req_val == '') {
                                    continue;
                                }
                                $all_uri_query[$_query_req_key] = jobsearch_esc_html($_query_req_val);
                            }
                        }
                        if (!empty($atts)) {
                            $_all_q_atts = array();

                            foreach ($atts as $_q_atts_key => $_q_atts_val) {
                                if ($_q_atts_val == '') {
                                    continue;
                                }
                                //echo "<pre>".print_r($_q_atts_key,1)."</pre>";
                                if (!is_array($_q_atts_val)) {
                                    $_q_atts_values = jobsearch_esc_html($_q_atts_val);
                                }
                                $_all_q_atts[] = $_q_atts_key . ':' . $_q_atts_values;
                            }

                            if (!empty($_all_q_atts)) {
                                $_all_q_atts = implode('|', $_all_q_atts);
                                $all_uri_query['sh_atts'] = $_all_q_atts;
                            }
                        }

                        $page_rss_url = home_url('/') . '?feed=job_feed';
                        if (!empty($all_uri_query)) {
                            $page_rss_url = add_query_arg($all_uri_query, $page_rss_url);
                        }
                        ?>
                        <li><a href="<?php echo($page_rss_url) ?>" class="jobsearch-rssfeed-btn"><i
                                        class="fa fa-feed"></i> <?php esc_html_e('RSS Feed', 'wp-jobsearch') ?> </a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <?php
                $this->job_layout_switcher_fields($atts, $job_short_counter, $view = '');
                ?>
            </div>
            <!-- filter-moving -->
            <?php
            //
            echo apply_filters('jobsearch_job_listin_after_sort_orders_html', '', $withtop_feature_jobnum, $job_short_counter, $atts);
            //
            echo '</div>';

            $adv_filter_toggle = isset($_REQUEST['adv_filter_toggle']) ? $_REQUEST['adv_filter_toggle'] : 'false';

            $args_more = array(
                'job_type' => $atts['job_type'],
                'job_filters' => $atts['job_filters'],
                'jobsearch_map_position' => isset($atts['jobsearch_map_position']) && $atts['jobsearch_map_position'] != '' ? ($atts['jobsearch_map_position']) : 'right',
                'job_short_counter' => $job_short_counter,
                'job_sort_by' => $atts['job_sort_by'],
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

    public function jobsearch_job_filter_categories($job_type, $category_request_val)
    {
        $jobsearch_job_type_category_array = array();
        $parent_cate_array = array();
        if ($category_request_val != '') {
            $category_request_val_arr = explode(",", $category_request_val);
            $category_request_val = isset($category_request_val_arr[0]) && $category_request_val_arr[0] != '' ? $category_request_val_arr[0] : '';
            $single_term = get_term_by('slug', $category_request_val, 'sector');
            $single_term_id = isset($single_term->term_id) && $single_term->term_id != '' ? $single_term->term_id : '0';
            $parent_cate_array = $this->jobsearch_job_parent_categories($single_term_id);
        }
        $jobsearch_job_type_category_array = $this->jobsearch_job_categories_list($job_type, $parent_cate_array);
        return $jobsearch_job_type_category_array;
    }

    public function jobsearch_job_parent_categories($category_id)
    {
        $parent_cate_array = array();
        $category_obj = get_term_by('id', $category_id, 'sector');
        if (isset($category_obj->parent) && $category_obj->parent != '0') {
            $parent_cate_array .= $this->jobsearch_job_parent_categories($category_obj->parent);
        }
        $parent_cate_array .= isset($category_obj->slug) ? $category_obj->slug . ',' : '';
        return $parent_cate_array;
    }

    public function jobsearch_job_categories_list($job_type, $parent_cate_string)
    {
        $cate_list_found = 0;
        $jobsearch_job_type_category_array = array();
        if ($parent_cate_string != '') {
            $category_request_val_arr = explode(",", $parent_cate_string);
            $count_arr = sizeof($category_request_val_arr);
            while ($count_arr >= 0) {
                if (isset($category_request_val_arr[$count_arr]) && $category_request_val_arr[$count_arr] != '') {
                    if ($cate_list_found == 0) {
                        $single_term = get_term_by('slug', $category_request_val_arr[$count_arr], 'sector');
                        $single_term_id = isset($single_term->term_id) && $single_term->term_id != '' ? $single_term->term_id : '0';
                        $jobsearch_category_array = get_terms('sector', array(
                                'hide_empty' => false,
                                'parent' => $single_term_id,
                            )
                        );
                        if (is_array($jobsearch_category_array) && sizeof($jobsearch_category_array) > 0) {
                            foreach ($jobsearch_category_array as $dir_tag) {
                                $jobsearch_job_type_category_array['cate_list'][] = $dir_tag->slug;
                            }
                            $cate_list_found++;
                        }
                    }
                    if ($cate_list_found > 0) {
                        $jobsearch_job_type_category_array['parent_list'][] = $category_request_val_arr[$count_arr];
                    }
                }
                $count_arr--;
            }
        }

        if ($cate_list_found == 0 && $job_type != '') {
            $job_type_post = get_posts(array('posts_per_page' => '1', 'post_type' => 'job-type', 'name' => "$job_type", 'post_status' => 'publish', 'fields' => 'ids'));
            $job_type_post_id = isset($job_type_post[0]) ? $job_type_post[0] : 0;
            $jobsearch_job_type_category_array['cate_list'] = get_post_meta($job_type_post_id, 'jobsearch_job_type_cats', true);
        }
        return $jobsearch_job_type_category_array;
    }

    public function wp_jobsearch_duplicate_post_as_draft()
    {
        set_time_limit(0);
        global $wpdb;
        if (!(isset($_REQUEST['post']) && (isset($_REQUEST['action']) && 'wp_jobsearch_duplicate_post_as_draft' == $_REQUEST['action']))) {
            wp_die('No post to duplicate has been supplied!');
        }
        echo 'wp_jobsearch_duplicate_post_as_draft 3';
        $count = 1;
        /*
         * get the original post id
         */
        $post_id = (isset($_REQUEST['post']) ? absint($_REQUEST['post']) : absint($_POST['post']));
        /*
         * and all the original post data then
         */
        $post = get_post($post_id);

        /*
         * if you don't want current user to be the new post author,
         * then change next couple of lines to this: $new_post_author = $post->post_author;
         */
        //$current_user = wp_get_current_user();
        //$new_post_author = $current_user->ID;

        /*
         * if post data exists, create the post duplicate
         */
        if (isset($post) && $post != null) {

            /*
             * new post data array
             */
            $args = array(
                'post_content' => $post->post_content,
                'post_name' => $post->post_name,
                'post_status' => 'publish',
                'post_title' => 'Dupplicate - ' . $count . $post->post_title,
                'post_type' => $post->post_type,
            );

            /*
             * insert the post by wp_insert_post() function
             */
            $new_post_id = wp_insert_post($args);

            /*
             * duplicate all post meta just in two SQL queries
             */
            $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
            if (count($post_meta_infos) != 0) {
                $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                foreach ($post_meta_infos as $meta_info) {
                    $meta_key = $meta_info->meta_key;
                    $meta_value = addslashes($meta_info->meta_value);
                    $sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
                }
                $sql_query .= implode(" UNION ALL ", $sql_query_sel);
                $wpdb->query($sql_query);
            }

            echo 'added ';
            /*
             * finally, redirect to the edit post screen for the new draft
             */
            exit;
        } else {
            wp_die('Post creation failed, could not find original post: ' . $post_id);
        }
    }

    public function jobsearch_job_pagination_callback($args)
    {
        global $jobsearch_form_fields;
        $total_posts = '';
        $job_per_page = '5';
        $paging_var = 'job_page';
        $show_pagination = 'yes';
        $job_short_counter = '';
        extract($args);
        $view_type = '';

        $ajax_filter = (isset($_REQUEST['ajax_filter']) || isset($_REQUEST['search_type'])) ? 'true' : 'false';

        if ($show_pagination <> 'yes') {
            return;
        } else if ($total_posts <= $job_per_page) {
            return;
        } else {
            if (!isset($_REQUEST[$paging_var])) {
                $_REQUEST[$paging_var] = '';
            }
            $html = '';
            $dot_pre = '';
            $dot_more = '';
            $total_page = 0;
            if ($total_posts > 0 && $job_per_page > 0) {
                $total_page = ceil($total_posts / $job_per_page);
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
                    'cus_id' => $paging_var . '-' . $job_short_counter,
                    'cus_name' => $paging_var,
                    'std' => '',
                )
            );
            $html .= '<div class="jobsearch-pagination-blog"><ul class="jobsearch-page-numbers">';
            if ($paged_id > 1) {
                $html .= '<li>'
                    . '<a class="prev jobsearch-page-numbers" onclick="jobsearch_job_pagenation_ajax(\'' . $paging_var . '\', \'' . ($paged_id - 1) . '\', \'' . ($job_short_counter) . '\', \'' . ($ajax_filter) . '\', \'' . ($view_type) . '\');" href="javascript:void(0);">';
                $html .= '<span><i class="jobsearch-icon jobsearch-arrows4"><i></span>'
                    . '</a>'
                    . '</li>';
            } else {

            }

            if ($paged_id > 3 && $total_page > 5) {
                $html .= '<li><a class="jobsearch-page-numbers" onclick="jobsearch_job_pagenation_ajax(\'' . $paging_var . '\', \'' . (1) . '\', \'' . ($job_short_counter) . '\', \'' . ($ajax_filter) . '\', \'' . ($view_type) . '\');" href="javascript:void(0);">';
                $html .= '1</a></li>';
            }
            if ($paged_id > 4 && $total_page > 6) {
                $html .= '<li class="disabled"><span>. . .</span></li>';
            }

            if ($total_page > 1) {

                for ($i = $loop_start; $i <= $loop_end; $i++) {

                    if ($i <> $paged_id) {

                        $html .= '<li><a class="jobsearch-page-numbers" onclick="jobsearch_job_pagenation_ajax(\'' . $paging_var . '\', \'' . ($i) . '\', \'' . ($job_short_counter) . '\', \'' . ($ajax_filter) . '\', \'' . ($view_type) . '\');" href="javascript:void(0);">';
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
                $html .= '<li><a class="jobsearch-page-numbers" onclick="jobsearch_job_pagenation_ajax(\'' . $paging_var . '\', \'' . ($total_page) . '\', \'' . ($job_short_counter) . '\', \'' . ($ajax_filter) . '\', \'' . ($view_type) . '\');" href="javascript:void(0);">';
                $html .= $total_page . '</a></li>';
            }
            if ($total_posts > 0 && $job_per_page > 0 && $paged_id < ($total_posts / $job_per_page)) {
                $html .= '<li>'
                    . '<a class="next jobsearch-page-numbers" onclick="jobsearch_job_pagenation_ajax(\'' . $paging_var . '\', \'' . ($paged_id + 1) . '\', \'' . ($job_short_counter) . '\', \'' . ($ajax_filter) . '\', \'' . ($view_type) . '\');" href="javascript:void(0);">';
                $html .= '<span><i class="jobsearch-icon jobsearch-arrows4"></i></span>'
                    . '</a>'
                    . '</li>';
            } else {

            }
            $html .= "</ul></div>";
            echo force_balance_tags($html);
        }
    }

    protected function render()
    {
        global $jobsearch_gdapi_allocation, $jobseacrh_jobsh_attslist;
        $atts = $this->get_settings_for_display();
        extract(shortcode_atts(array(
            'job_cat' => '',
            'job_view' => 'view-default',
            'featured_only' => '',
            'job_sort_by' => '',
            'job_top_search' => '',
            'job_excerpt' => '20',
            'job_order' => 'DESC',
            'job_orderby' => 'date',
            'job_pagination' => 'yes',
            'job_per_page' => '3',
            'job_type' => '',
            'job_custom_fields_switch' => '',
            'job_elem_custom_fields' => '',
            'job_filters' => 'yes',
            'job_deadline_switch' => '',
            'quick_apply_job' => '',
        ), $atts));

        if (empty($atts) && !is_array($atts)) {
            $atts = array();
        }
        if (!isset($atts['job_cat'])) {
            $atts['job_cat'] = '';
        }
        if (!isset($atts['job_view'])) {
            $atts['job_view'] = 'view-default';
        }
        if (!isset($atts['featured_only'])) {
            $atts['featured_only'] = '';
        }
        if (!isset($atts['job_sort_by'])) {
            $atts['job_sort_by'] = '';
        }
        if (!isset($atts['job_top_search'])) {
            $atts['job_top_search'] = '';
        }
        if (!isset($atts['job_excerpt'])) {
            $atts['job_excerpt'] = '20';
        }
        if (!isset($atts['job_order'])) {
            $atts['job_order'] = 'DESC';
        }
        if (!isset($atts['job_orderby'])) {
            $atts['job_orderby'] = 'date';
        }
        if (!isset($atts['job_pagination'])) {
            $atts['job_pagination'] = 'yes';
        }
        if (!isset($atts['job_per_page'])) {
            $atts['job_per_page'] = '10';
        }
        if (!isset($atts['job_type'])) {
            $atts['job_type'] = '';
        }
        if (!isset($atts['job_filters'])) {
            $atts['job_filters'] = 'yes';
        }
        if (!isset($atts['job_filters_loc'])) {
            $atts['job_filters_loc'] = 'yes';
        }
        if (!isset($atts['job_filters_date'])) {
            $atts['job_filters_date'] = 'yes';
        }
        if (!isset($atts['job_filters_type'])) {
            $atts['job_filters_type'] = 'yes';
        }
        if (!isset($atts['job_filters_sector'])) {
            $atts['job_filters_sector'] = 'yes';
        }
        if (!isset($atts['job_custom_fields_switch'])) {
            $atts['job_custom_fields_switch'] = 'no';
        }
        if (!isset($atts['job_elem_custom_fields'])) {
            $atts['job_elem_custom_fields'] = '';
        }
        if (!isset($atts['job_deadline_switch'])) {
            $atts['job_deadline_switch'] = 'no';
        }
        if (!isset($atts['quick_apply_job'])) {
            $atts['quick_apply_job'] = 'no';
        }
        if (!isset($atts['job_loc_listing'])) {
            $atts['job_loc_listing'] = 'country,city';
        }


        $jobseacrh_jobsh_attslist = $atts;

        ob_start();
        wp_enqueue_style('datetimepicker-style');
        wp_enqueue_script('datetimepicker-script');
        wp_enqueue_script('jquery-ui');
        wp_enqueue_script('jobsearch-job-functions-script');
        do_action('jobsearch_notes_frontend_modal_popup');
        $job_short_counter = isset($atts['job_counter']) && $atts['job_counter'] != '' ? ($atts['job_counter']) : rand(1000, 9999); // for shortcode counter
        if (false === ($job_view = jobsearch_get_transient_obj('jobsearch_job_view' . $job_short_counter))) {
            $job_view = isset($atts['job_view']) ? $atts['job_view'] : '';
        }
        jobsearch_set_transient_obj('jobsearch_job_view' . $job_short_counter, $job_view);
        $job_map_counter = rand(10000000, 99999999);
        $element_job_footer = isset($atts['job_footer']) ? $atts['job_footer'] : '';
        $element_job_map_position = isset($atts['job_map_position']) ? $atts['job_map_position'] : '';
        $map_change_class = '';
        if ($job_view == 'map') {
            if ($element_job_footer == 'yes') {
                echo '<script>';
                echo 'jQuery(document).ready(function () {'
                    . 'jQuery("footer#footer").hide();'
                    . '});';
                echo '</script>';
            }
        }
        wp_reset_query();
        do_action('job_checks_enquire_lists_submit');
        do_action('jobsearch_job_compare_sidebar');
        do_action('jobsearch_job_enquiries_sidebar');
        $page_url = get_permalink(get_the_ID());
        /*
         * jobs listing element selected custom fields array
         */
        $job_custom_fields_switch = isset($atts['job_custom_fields_switch']) ? $atts['job_custom_fields_switch'] : 'no';
        $job_elem_custom_fields = isset($atts['job_elem_custom_fields']) ? $atts['job_elem_custom_fields'] : '';
        $selected_fields = array();

        if (isset($job_elem_custom_fields) && !empty($job_elem_custom_fields)) {
            $selected_fields = $job_elem_custom_fields;
        }
        $job_cus_field_arr = array();
        if ($job_custom_fields_switch == 'yes' && !empty($selected_fields)) {
            $jobsearch_job_cus_fields = get_option("jobsearch_custom_field_job");
            if (isset($jobsearch_job_cus_fields) && !empty($jobsearch_job_cus_fields) && sizeof($jobsearch_job_cus_fields) > 0) {
                foreach ($jobsearch_job_cus_fields as $key => $value) {
                    foreach ($selected_fields as $selected_key => $selected_val) {
                        if ($key == $selected_val) {
                            $job_cus_field_arr[$key] = $value;
                        }
                    }
                }
            }
        }
        $content = '';
        /*
         * END jobs listing element selected custom fields array
         */
        ?>
        <div class="wp-dp-job-content" id="wp-dp-job-content-<?php echo esc_html($job_short_counter); ?>">
            <div class="dev-map-class-changer<?php echo($map_change_class); ?>">
                <div id="Job-content-<?php echo esc_html($job_short_counter); ?>">
                    <?php
                    $job_arg = array(
                        'job_short_counter' => $job_short_counter,
                        'atts' => $atts,
                        'content' => $content,
                        'job_map_counter' => $job_map_counter,
                        'page_id' => get_the_ID(),
                        'page_url' => $page_url,
                        'custom_fields' => $job_cus_field_arr,
                    );
                    $this->jobsearch_jobs_content($job_arg);
                    ?>
                </div>
            </div>
        </div>
        <?php
        $html = ob_get_clean();
        echo apply_filters('jobsearch_job_listing_pagehtml', $html);
    }

    protected function _content_template()
    {
    }
}