<?php

namespace CareerfyElementor\Widgets;


use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

/**
 * @since 1.1.0
 */
class EmployeeListings extends Widget_Base
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
        return 'employee-listings';
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
        return __('Employer Listings', 'careerfy-frame');
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
            'employer_view',
            [
                'label' => __('Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'view-default',
                'options' => [
                    'view-default' => __('Style 1', 'careerfy-frame'),
                    'view-grid' => __('Style 2', 'careerfy-frame'),
                    'view-slider' => __('Style 3', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'employer_cat',
            [
                'label' => __('Sector', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'options' => $cate_array,
            ]
        );

        $this->add_control(
            'display_per_page',
            [
                'label' => __('Employer Founds with display counts', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Display the per page Employers count at top of the listing.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'employer_loc_listing',
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
            'employer_sort_by',
            [
                'label' => __('Sort by Fields', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Results search sorting section switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'employer_excerpt',
            [
                'label' => __('Excerpt Length', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '20',
                'description' => __("Set the number of words you want to show for excerpt.", "careerfy-frame"),
            ]
        );

        $this->add_control(
            'employer_order',
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
            'employer_orderby',
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
            'employer_pagination',
            [
                'label' => __('Pagination', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'description' => __("Choose yes if you want to show pagination for employer items.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'employer_per_page',
            [
                'label' => __('Items per Page', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '10',
                'description' => __("Set number that how much employers you want to show per page. Leave it blank for all employers on a single page.", "careerfy-frame"),
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
                    'employer_ad_banners',
                    [
                        'label' => __('Ad Banners', 'careerfy-frame'),
                        'type' => Controls_Manager::SELECT2,
                        'default' => 'no',
                        'description' => __("Show/hide ad banners in employer listings.", "careerfy-frame"),
                        'options' => [
                            'yes' => __('Yes', 'careerfy-frame'),
                            'no' => __('No', 'careerfy-frame'),
                        ],
                    ]
                );
                $this->add_control(
                    'employer_ad_after_list',
                    [
                        'label' => __('Ad after list count', 'careerfy-frame'),
                        'type' => Controls_Manager::TEXT,
                        'default' => '5',
                        'description' => __("Put number. After how many employers list an ad banner will show. You can also add comma seprated numbers i.e. 2,5,7", "careerfy-frame"),
                    ]
                );

                $this->add_control(
                    'employer_ads_group',
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
            'employer_filters',
            [
                'label' => __('Filters', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Employers searching filters switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'employer_filters_count',
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
            'employer_filters_sortby',
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
            'employer_filters_loc',
            [
                'label' => __('Locations', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Employers searching filters 'Location' switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'employer_filters_loc_collapse',
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
            'employer_filters_loc_view',
            [
                'label' => __('Locations Filter Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Locations Filter Collapse", "careerfy-frame"),
                'options' => [
                    'checkboxes' => __('Checkbox List', 'careerfy-frame'),
                    'input' => __('Input Field', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'employer_filters_date',
            [
                'label' => __('Date Posted', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Employers searching filters 'Date Posted' switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'employer_filters_date_collapse',
            [
                'label' => __('Date Posted Collapse', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'employer_filters_sector',
            [
                'label' => __('Sector', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'description' => __("Employers searching filters 'Sector' switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'employer_filters_sector_collapse',
            [
                'label' => __('Sector Filter Collapse', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'employer_filters_team',
            [
                'label' => __('Employers searching filters "Team Size" switch.', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'employer_filters_team_collapse',
            [
                'label' => __('Team Size Filter Collapse', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
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
            'emp_top_map',
            [
                'label' => __('Top map switch', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'description' => __("Employer top map switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'emp_top_map_height',
            [
                'label' => __('Map Height', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'description' => __("Employers top map height.", "careerfy-frame"),
            ]
        );
        $this->add_control(
            'emp_top_map_zoom',
            [
                'label' => __('Map Zoom', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => '8',
                'description' => __("Employers top map zoom.", "careerfy-frame"),
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
            'emp_top_search',
            [
                'label' => __('Employer Search Bar', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => '',
                'description' => __("Employer&apos;s top search bar switch.", "careerfy-frame"),
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'emp_top_search_view',
            [
                'label' => __('Employer&apos;s Search Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'simple',
                'description' => __("Employer&apos;s top search bar switch.", "careerfy-frame"),
                'options' => [
                    'simple' => __('Simple', 'careerfy-frame'),
                    'advance' => __('Advance Search', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'emp_top_search_radius',
            [
                'label' => __('Employer Search Radius', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'simple',
                'description' => __("Enable/Disable top search radius.", "careerfy-frame"),
                'options' => [
                    'simple' => __('Simple', 'careerfy-frame'),
                    'advance' => __('Advance Search', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'emp_top_search_title',
            [
                'label' => __('Employer Title, Keywords, or Phrase', 'careerfy-frame'),
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
            'emp_top_search_location',
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
                'emp_top_search_sector',
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

    }

    public function employer_layout_switcher_fields($atts, $employer_short_counter, $view = '', $frc_view = false)
    {

        $counter = isset($atts['employer_counter']) && $atts['employer_counter'] != '' ? $atts['employer_counter'] : '';
        $transient_view = jobsearch_get_transient_obj('jobsearch_employer_view' . $counter);

        if ($frc_view == true) {
            $view = $view;
        } else {
            if (false === ($view = jobsearch_get_transient_obj('jobsearch_employer_view' . $counter))) {
                $view = isset($atts['employer_view']) ? $atts['employer_view'] : '';
            }
        }
        if ((isset($atts['employer_layout_switcher']) && $atts['employer_layout_switcher'] != 'no')) {

            if (isset($atts['employer_layout_switcher_view']) && !empty($atts['employer_layout_switcher_view'])) {
                $employer_layout_switcher_views = array(
                    'grid' => esc_html__('grid', 'careerfy-frame'),
                    'list' => esc_html__('list', 'careerfy-frame'),
                );
                ?>
                <ul class="employers-views-switcher-holder">
                    <li><?php echo esc_html__('jobsearch_view_employers_by_switcher'); ?></li>
                    <?php
                    $element_employer_layout_switcher_view = explode(',', $atts['employer_layout_switcher_view']);

                    if (!empty($element_employer_layout_switcher_view) && is_array($element_employer_layout_switcher_view)) {
                        $views_counter = 0;
                        foreach ($element_employer_layout_switcher_view as $single_layout_view) {
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
                                    $icon .= esc_html__('grid', 'careerfy-frame');
                                    $view_class = 'grid-view';
                                    break;
                                case 'listed':
                                    $icon = '<i class="icon-th-list"></i> ';
                                    $icon .= esc_html__('list', 'careerfy-frame');
                                    $view_class = 'list-view';
                                    break;
                                case 'grid-medern':
                                    $icon = '<i class="icon-th"></i> ';
                                    $icon .= esc_html__('modern grid', 'careerfy-frame');
                                    $view_class = 'grid-modern-view';
                                    break;
                                case 'grid-classic':
                                    $icon = '<i class="icon-grid_on"></i> ';
                                    $icon .= esc_html__('classic grid', 'careerfy-frame');
                                    $view_class = 'grid-classic-view';
                                    break;
                                case 'grid-default':
                                    $icon = '<i class="icon-menu4"></i> ';
                                    $icon .= esc_html__('default grid', 'careerfy-frame');
                                    $view_class = 'grid-default-view';
                                    break;
                                case 'list-modern':
                                    $icon = '<i class="icon-list5"></i> ';
                                    $icon .= esc_html__('modern list', 'careerfy-frame');
                                    $view_class = 'list-modern-view';
                                    break;
                                default:
                                    $icon = '<i class="icon-th-list"></i> ';
                                    $icon .= esc_html__('list', 'careerfy-frame');
                                    $view_class = 'list-view';
                            }
                            if (empty($view) && $views_counter === 0) {
                                ?>
                                <li><a href="javascript:void(0);" class="active"><i
                                                class="icon-th-list"></i><?php echo esc_html($employer_layout_switcher_views[$single_layout_view]); ?>
                                    </a></li>
                                <?php
                            } else {
                                $view_type = '';
                                ?>
                                <li class="<?php echo esc_html($view_class); ?>"><a
                                            href="javascript:void(0);" <?php if ($view == $single_layout_view) echo 'class="active"'; ?> <?php if ($view != $single_layout_view) { ?> onclick="jobsearch_employer_view_switch('<?php echo esc_html($single_layout_view) ?>', '<?php echo esc_html($employer_short_counter); ?>', '<?php echo esc_html($counter); ?>', '<?php echo esc_html($view_type); ?>');"<?php } ?>><?php echo force_balance_tags($icon); ?></a>
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

    public function jobsearch_employer_pagination_callback($args)
    {
        global $jobsearch_form_fields;
        $total_posts = '';
        $employer_per_page = '5';
        $paging_var = 'employer_page';
        $show_pagination = 'yes';
        $employer_short_counter = '';
        extract($args);
        $view_type = '';

        $ajax_filter = (isset($_REQUEST['ajax_filter']) || isset($_REQUEST['search_type'])) ? 'true' : 'false';

        if ($show_pagination <> 'yes') {
            return;
        } else if ($total_posts <= $employer_per_page) {
            return;
        } else {
            if (!isset($_REQUEST[$paging_var])) {
                $_REQUEST[$paging_var] = '';
            }
            $html = '';
            $dot_pre = '';
            $dot_more = '';
            $total_page = 0;
            if ($total_posts <> 0)
                $total_page = ceil($total_posts / $employer_per_page);
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
                    'cus_id' => $paging_var . '-' . $employer_short_counter,
                    'cus_name' => $paging_var,
                    'std' => '',
                )
            );
            $html .= '<div class="jobsearch-pagination-blog"><ul class="jobsearch-page-numbers">';
            if ($paged_id > 1) {
                $html .= '<li>'
                    . '<a class="prev jobsearch-page-numbers" onclick="jobsearch_employer_pagenation_ajax(\'' . $paging_var . '\', \'' . ($paged_id - 1) . '\', \'' . ($employer_short_counter) . '\', \'' . ($ajax_filter) . '\', \'' . ($view_type) . '\');" href="javascript:void(0);">';
                $html .= '<span><i class="jobsearch-icon jobsearch-arrows4"><i></span>'
                    . '</a>'
                    . '</li>';
            } else {

            }

            if ($paged_id > 3 && $total_page > 5) {
                $html .= '<li><a class="jobsearch-page-numbers" onclick="jobsearch_employer_pagenation_ajax(\'' . $paging_var . '\', \'' . (1) . '\', \'' . ($employer_short_counter) . '\', \'' . ($ajax_filter) . '\', \'' . ($view_type) . '\');" href="javascript:void(0);">';
                $html .= '1</a></li>';
            }
            if ($paged_id > 4 && $total_page > 6) {
                $html .= '<li class="disabled"><span>. . .</span></li>';
            }

            if ($total_page > 1) {

                for ($i = $loop_start; $i <= $loop_end; $i++) {

                    if ($i <> $paged_id) {

                        $html .= '<li><a class="jobsearch-page-numbers" onclick="jobsearch_employer_pagenation_ajax(\'' . $paging_var . '\', \'' . ($i) . '\', \'' . ($employer_short_counter) . '\', \'' . ($ajax_filter) . '\', \'' . ($view_type) . '\');" href="javascript:void(0);">';
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
                $html .= '<li><a class="jobsearch-page-numbers" onclick="jobsearch_employer_pagenation_ajax(\'' . $paging_var . '\', \'' . ($total_page) . '\', \'' . ($employer_short_counter) . '\', \'' . ($ajax_filter) . '\', \'' . ($view_type) . '\');" href="javascript:void(0);">';
                $html .= $total_page . '</a></li>';
            }
            if ($total_posts > 0 && $paged_id < ($total_posts / $employer_per_page)) {
                $html .= '<li>'
                    . '<a class="next jobsearch-page-numbers" onclick="jobsearch_employer_pagenation_ajax(\'' . $paging_var . '\', \'' . ($paged_id + 1) . '\', \'' . ($employer_short_counter) . '\', \'' . ($ajax_filter) . '\', \'' . ($view_type) . '\');" href="javascript:void(0);">';
                $html .= '<span><i class="jobsearch-icon jobsearch-arrows4"></i></span>'
                    . '</a>'
                    . '</li>';
            } else {

            }
            $html .= "</ul></div>";
            echo force_balance_tags($html);
        }
    }

    public function jobsearch_employer_parent_categories($category_id)
    {
        $parent_cate_array = array();
        $category_obj = get_term_by('id', $category_id, 'sector');
        if (isset($category_obj->parent) && $category_obj->parent != '0') {
            $parent_cate_array .= $this->jobsearch_employer_parent_categories($category_obj->parent);
        }
        $parent_cate_array .= isset($category_obj->slug) ? $category_obj->slug . ',' : '';
        return $parent_cate_array;
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

    public function employer_search_sort_fields($atts, $employer_sort_by, $employer_short_counter, $view = '', $employer_totnum = '', $employer_per_page = '')
    {
        global $jobsearch_form_fields;

        $counter = isset($atts['employer_counter']) && $atts['employer_counter'] != '' ? $atts['employer_counter'] : '';
        $transient_view = jobsearch_get_transient_obj('jobsearch_employer_view' . $counter);
        $view = isset($transient_view) && $transient_view != '' ? $transient_view : $view;

        $employer_type_slug = isset($_REQUEST['employer_type']) ? $_REQUEST['employer_type'] : '';
        $employer_type_text = $employer_type_slug;
        if (isset($employer_type_slug) && !empty($employer_type_slug) && $employer_type_slug != 'all') {
            if ($post = get_page_by_path($employer_type_slug, OBJECT, 'employer-type')) {
                $id = $post->ID;
                $employer_type_text = get_the_title($id);
            }
        }

        $view_type = '';

        if ((isset($atts['employer_sort_by']) && $atts['employer_sort_by'] != 'no')) {
            //
            echo '<div class="sortfiltrs-contner">';
            echo apply_filters('jobsearch_emp_listin_before_top_jobfounds_html', '', $employer_totnum, $employer_short_counter, $atts);
            //
            $paging_var = 'employer_page';
            $pagination = isset($atts['employer_pagination']) ? $atts['employer_pagination'] : 'no';
            $paging_args = array(
                'total_posts' => $employer_totnum,
                'employer_per_page' => $employer_per_page,
                'paging_var' => $paging_var,
                'show_pagination' => $pagination,
                'employer_short_counter' => $employer_short_counter,
            );
            echo apply_filters('jobsearch_emp_listin_before_sort_orders', '', $paging_args, $atts);
            ?>
            <div class="jobsearch-filterable jobsearch-filter-sortable">
                <?php
                ob_start();
                ?>
                <h2>
                    <?php
                    echo absint($employer_totnum) . '&nbsp;';
                    if ($employer_totnum > 1) {
                        echo esc_html__('Employers Found', 'wp-jobsearch');
                    } else {
                        echo esc_html__('Employer Found', 'wp-jobsearch');
                    }
                    do_action('jobsearch_emp_listin_sh_after_jobs_found', $employer_totnum, $employer_short_counter, $atts);
                    ?>
                </h2>
                <?php
                $foundemps_html = ob_get_clean();
                echo apply_filters('jobsearch_emp_listin_top_jobfounds_html', $foundemps_html, $employer_totnum, $employer_short_counter, $atts);
                ?>
                <ul class="jobsearch-sort-section">
                    <?php
                    do_action('jobsearch_emp_listin_sh_before_topsort_items', $employer_short_counter, $atts);
                    ?>
                    <li>
                        <i class="jobsearch-icon jobsearch-sort"></i>
                        <div class="jobsearch-filterable-select">
                            <?php
                            $sortby_option = array(
                                'recent' => esc_html__('Most Recent', 'wp-jobsearch'),
                                'alphabetical' => esc_html__('Alphabet Order', 'wp-jobsearch'),
                                'most_viewed' => esc_html__('Most Viewed', 'wp-jobsearch')
                            );
                            $sortby_option = apply_filters('employer_hunt_employers_sort_options', $sortby_option);
                            $cs_opt_array = array(
                                'cus_id' => '',
                                'cus_name' => 'sort-by',
                                'force_std' => $employer_sort_by,
                                'desc' => '',
                                'classes' => 'selectize-select',
                                'ext_attr' => ' onchange="jobsearch_employer_content_load(\'' . esc_js($employer_short_counter) . '\')" placeholder="' . esc_html__('Most Recent', 'wp-jobsearch') . '"',
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
                            $paging_options[""] = '' . esc_html__("Records Per Page", "wp-jobsearch");
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
                                'force_std' => $employer_per_page,
                                'desc' => '',
                                'classes' => 'sort-records-per-page',
                                'ext_attr' => ' onchange="jobsearch_employer_content_load(\'' . esc_js($employer_short_counter) . '\')" placeholder="' . esc_html__('Records Per Page', 'wp-jobsearch') . '"',
                                'options' => apply_filters('jobsearch_emplistin_topsort_paginum_options', $paging_options),
                            );

                            $jobsearch_form_fields->select_field($cs_opt_array);
                            ?>
                        </div>
                    </li>
                </ul>
                <?php
                $this->employer_layout_switcher_fields($atts, $employer_short_counter, $view = '');
                ?>
            </div>
            <!-- filter-moving -->
            <?php
            //
            echo apply_filters('jobsearch_emp_listin_after_sort_orders_html', '', $employer_totnum, $employer_short_counter, $atts);
            //
            echo '</div>';

            $adv_filter_toggle = isset($_REQUEST['adv_filter_toggle']) ? $_REQUEST['adv_filter_toggle'] : 'false';

            $args_more = array(
                'employer_type' => $atts['employer_type'],
                'employer_filters' => $atts['employer_filters'],
                'jobsearch_map_position' => isset($atts['jobsearch_map_position']) && $atts['jobsearch_map_position'] != '' ? ($atts['jobsearch_map_position']) : 'right',
                'employer_short_counter' => $employer_short_counter,
                'employer_sort_by' => $atts['employer_sort_by'],
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

    public function employer_location_filter($all_post_ids)
    {

        global $sitepress;

        $radius = isset($_REQUEST['loc_radius']) ? $_REQUEST['loc_radius'] : '';
        $search_type = isset($_REQUEST['location_location1']) ? $_REQUEST['location_location1'] : '';

        $jobsearch__options = get_option('jobsearch_plugin_options');
        $all_locations_type = isset($jobsearch__options['all_locations_type']) ? $jobsearch__options['all_locations_type'] : '';

        $location_rslt = $all_post_ids;

        if (isset($_REQUEST['location']) && $_REQUEST['location'] != '') {
            if (isset($_POST['action'])) {
                $loc_decod_str = ($_REQUEST['location']);
            } else {
                //$loc_decod_str = urlencode($_REQUEST['location']);
                $loc_decod_str = ($_REQUEST['location']);
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
                'value' => $loc_decod_str,
                'compare' => 'LIKE',
            );
            $location_condition_arr[] = array(
                'key' => 'jobsearch_field_location_location1',
                'value' => $loc_decod_str,
                'compare' => 'LIKE',
            );
            $location_condition_arr[] = array(
                'key' => 'jobsearch_field_location_location2',
                'value' => $loc_decod_str,
                'compare' => 'LIKE',
            );
            $location_condition_arr[] = array(
                'key' => 'jobsearch_field_location_location3',
                'value' => $loc_decod_str,
                'compare' => 'LIKE',
            );
            $location_condition_arr[] = array(
                'key' => 'jobsearch_field_location_location4',
                'value' => $loc_decod_str,
                'compare' => 'LIKE',
            );

            $args_count = array(
                'posts_per_page' => "-1",
                'post_type' => 'employer',
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
                if (empty($location_rslt) && isset($trans_able_options['employer']) && $trans_able_options['employer'] == '2') {
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

            $args_count = array(
                'posts_per_page' => "-1",
                'post_type' => 'employer',
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
                if (empty($location_rslt) && isset($trans_able_options['employer']) && $trans_able_options['employer'] == '2') {
                    $sitepress_def_lang = $sitepress->get_default_language();
                    $sitepress_curr_lang = $sitepress->get_current_language();
                    $sitepress->switch_lang($sitepress_def_lang, true);

                    $location_query = new WP_Query($args_count);
                    wp_reset_postdata();
                    $location_rslt = $location_query->posts;

                    $sitepress->switch_lang($sitepress_curr_lang, true);
                }
            }
            if (empty($location_rslt)) {
                $location_rslt = array(0);
            }
        }
        if ($radius > 0) {
            return $all_post_ids;
        }
        return $location_rslt;
    }

    public function get_employer_id_by_filter($left_filter_arr)
    {
        global $wpdb;
        $meta_post_ids_arr = '';
        $employer_id_condition = '';
        if (isset($left_filter_arr) && !empty($left_filter_arr)) {
            $meta_post_ids_arr = jobsearch_get_query_whereclase_by_array($left_filter_arr);
// if no result found in filtration
            if (empty($meta_post_ids_arr)) {
                $meta_post_ids_arr = array(0);
            }
            if (isset($_REQUEST['loc_polygon_path']) && $_REQUEST['loc_polygon_path'] != '' && $meta_post_ids_arr != '') {
                $meta_post_ids_arr = $this->employer_polygon_filter($_REQUEST['loc_polygon_path'], $meta_post_ids_arr);
                if (empty($meta_post_ids_arr)) {
                    $meta_post_ids_arr = '';
                }
            }
            $ids = $meta_post_ids_arr != '' ? implode(",", $meta_post_ids_arr) : '0';
            $employer_id_condition = " ID in (" . $ids . ") AND ";
        }

        $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $employer_id_condition . " post_type='employer' AND post_status='publish'");

        if (empty($post_ids)) {
            $post_ids = array(0);
        }
        return $post_ids;
    }

    public function employer_polygon_filter($polygon_pathstr, $post_ids, $custom_meta_array = '')
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
        foreach ($post_ids as $employer_id) {
            $qry = "SELECT meta_value FROM $wpdb->postmeta WHERE 1=1 AND post_id='" . $employer_id . "' AND meta_key='jobsearch_field_location_lat'";
            $employer_latitude_arr = $wpdb->get_col($qry);
            $employer_latitude = isset($employer_latitude_arr[0]) ? $employer_latitude_arr[0] : '';

            $qry = "SELECT meta_value FROM $wpdb->postmeta WHERE 1=1 AND post_id='" . $employer_id . "' AND meta_key='jobsearch_field_location_lng'";
            $employer_longitude_arr = $wpdb->get_col($qry);
            $employer_longitude = isset($employer_longitude_arr[0]) ? $employer_longitude_arr[0] : '';

            if ($this->pointInPolygon(array($employer_latitude, $employer_longitude), $polygon_path)) {
                $new_post_ids[] = $employer_id;
            }
            if ($th_counter > 3000) {
                break;
            }
            $th_counter++;
        }
        return $new_post_ids;
    }

    public function jobsearch_employers_content($employer_arg = '')
    {

        global $wpdb, $post, $jobsearch_form_fields, $jobsearch_search_fields, $pagenow, $jobsearch_plugin_options, $sitepress;
        $page_id = isset($post->ID) ? $post->ID : '';
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
            $trans_able_options = $sitepress->get_setting('custom_posts_sync_option', array());
        }

        $def_radius_unit = isset($jobsearch_plugin_options['top_search_radius_unit']) ? $jobsearch_plugin_options['top_search_radius_unit'] : '';

        // getting arg array from ajax

        $all_post_ids = array();
        if (isset($_REQUEST['employer_arg']) && $_REQUEST['employer_arg']) {
            $employer_arg = stripslashes(html_entity_decode($_REQUEST['employer_arg']));
            $employer_arg = json_decode($employer_arg);
            $employer_arg = $this->toArray($employer_arg);
        }
        if (isset($employer_arg) && $employer_arg != '' && !empty($employer_arg)) {
            extract($employer_arg);
        }
        $default_date_time_formate = 'd-m-Y H:i:s';
        // getting if user set it with his choice
        if (false === ($employer_view = jobsearch_get_transient_obj('jobsearch_employer_view' . $employer_short_counter))) {
            $employer_view = isset($atts['employer_view']) ? $atts['employer_view'] : '';
        }

        $employer_order = isset($atts['employer_order']) ? $atts['employer_order'] : '';
        $employer_orderby = isset($atts['employer_orderby']) ? $atts['employer_orderby'] : '';

        $element_employer_sort_by = isset($atts['employer_sort_by']) ? $atts['employer_sort_by'] : 'no';
        $element_employer_topmap = ''; //isset( $atts['employer_topmap'] ) ? $atts['employer_topmap'] : 'no';
        $element_employer_map_position = isset($atts['employer_map_position']) ? $atts['employer_map_position'] : 'full';
        $element_employer_layout_switcher = isset($atts['employer_layout_switcher']) ? $atts['employer_layout_switcher'] : 'no';
        $element_employer_layout_switcher_view = isset($atts['employer_layout_switcher_view']) ? $atts['employer_layout_switcher_view'] : 'grid';
        $element_employer_map_height = isset($atts['employer_map_height']) ? $atts['employer_map_height'] : 400;
        $element_employer_footer = isset($atts['employer_footer']) ? $atts['employer_footer'] : 'no';
        $element_employer_search_keyword = isset($atts['employer_search_keyword']) ? $atts['employer_search_keyword'] : 'no';

        $element_employer_recent_switch = isset($atts['employer_recent_switch']) ? $atts['employer_recent_switch'] : 'no';
        $employer_employer_urgent = isset($atts['employer_urgent']) ? $atts['employer_urgent'] : 'all';
        $employer_type = isset($atts['employer_type']) ? $atts['employer_type'] : 'all';
        $employer_filters_sidebar = isset($atts['employer_filters']) ? $atts['employer_filters'] : '';

        $employer_right_sidebar_content = isset($content) ? $content : '';
        $jobsearch_employer_sidebar = isset($atts['jobsearch_employer_sidebar']) ? $atts['jobsearch_employer_sidebar'] : '';
        $jobsearch_map_position = isset($atts['jobsearch_map_position']) && $atts['jobsearch_map_position'] != '' ? ($atts['jobsearch_map_position']) : 'right';

        $employer_desc = isset($atts['employer_desc']) ? $atts['employer_desc'] : '';
        $employer_cus_fields = isset($atts['employer_cus_fields']) ? $atts['employer_cus_fields'] : 'yes';

        $employer_per_page = '-1';
        $pagination = 'no';
        $employer_per_page = isset($atts['employer_per_page']) ? $atts['employer_per_page'] : '-1';
        $employer_per_page = isset($_REQUEST['per-page']) ? $_REQUEST['per-page'] : $employer_per_page;
        $pagination = isset($atts['employer_pagination']) ? $atts['employer_pagination'] : 'no';
        $filter_arr = array();
        $qryvar_sort_by_column = '';
        $element_filter_arr = array();
        $content_columns = 'jobsearch-column-12 jobsearch-typo-wrap'; // if filteration not true
        $paging_var = 'employer_page';
// Element fields in filter
        if (isset($_REQUEST['employer_type']) && $_REQUEST['employer_type'] != '') {
            $employer_type = $_REQUEST['employer_type'];
        }

        $element_filter_arr[] = array(
            'key' => 'jobsearch_field_employer_approved',
            'value' => 'on',
            'compare' => '=',
        );

        if (function_exists('jobsearch_visibility_query_args')) {
            $element_filter_arr = jobsearch_visibility_query_args($element_filter_arr);
        }

        if (!isset($_REQUEST[$paging_var])) {
            $_REQUEST[$paging_var] = '';
        }

// Get all arguments from getting flters.
        $left_filter_arr = $this->get_filter_arg($employer_short_counter);

        $post_ids = array();
        if (!empty($left_filter_arr)) {
// apply all filters and get ids
            $post_ids = $this->get_employer_id_by_filter($left_filter_arr);
        }

//
        $post_ids = $this->employer_location_filter($post_ids);
//

        $loc_polygon_path = '';
        if (isset($_REQUEST['loc_polygon_path']) && $_REQUEST['loc_polygon_path'] != '') {
            $loc_polygon_path = $_REQUEST['loc_polygon_path'];
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
                $element_filter_arr[] = array(
                    'relation' => 'AND',
                    array(
                        'key' => 'jobsearch_field_location_lat',
                        'value' => array($minLat, $maxLat),
                        'compare' => 'BETWEEN',
                        'type' => 'CHAR'
                    ),
                    array(
                        'key' => 'jobsearch_field_location_lng',
                        'value' => array($minLong, $maxLong),
                        'compare' => 'BETWEEN',
                        'type' => 'DECIMAL'
                    ),
                );
            }
        }

        if (isset($_REQUEST['team_size']) && $_REQUEST['team_size'] != '') {
            $team_size = $_REQUEST['team_size'];
            $team_size_arr = explode('-', $team_size);
            $team_size_fv = isset($team_size_arr[0]) ? esc_html($team_size_arr[0]) : 0;
            $team_size_sv = isset($team_size_arr[1]) ? esc_html($team_size_arr[1]) : 0;
            $element_filter_arr[] = array(
                'key' => 'jobsearch_field_employer_team_size',
                'value' => array($team_size_fv, $team_size_sv),
                'type' => 'numeric',
                'compare' => 'BETWEEN',
            );
        }

        $args_count = array(
            'posts_per_page' => "1",
            'post_type' => 'employer',
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
        } else if (isset($atts['employer_cat']) && $atts['employer_cat'] != '') {
            $args_count['tax_query'][] = array(
                'taxonomy' => 'sector',
                'field' => 'slug',
                'terms' => $atts['employer_cat']
            );
        }
        $employer_sort_by = ''; // default value

        if (isset($_REQUEST['sort-by']) && $_REQUEST['sort-by'] != '') {
            $employer_sort_by = $_REQUEST['sort-by'];
        }
        $meta_key = '';
        $qryvar_employer_sort_type = isset($employer_order) ? $employer_order : 'DESC';
        $qryvar_sort_by_column = isset($employer_orderby) ? $employer_orderby : 'post_date';

        //
        $employer_act_orderby = isset($atts['employer_orderby']) ? $atts['employer_orderby'] : '';
        $employer_act_order = isset($atts['employer_order']) ? $atts['employer_order'] : '';

        $employer_act_orderby = apply_filters('jobsearch_emps_listinsh_args_ordery', $employer_act_orderby);

        if ($employer_act_orderby == 'promote_profile') {

            add_filter('posts_join_paged', array($this, 'edit_join'), 999, 2);
            add_filter('posts_orderby', array($this, 'edit_orderby'), 999, 2);
        }
        //

        $employer_sort_by = apply_filters('jobsearch_emplistin_filter_sortby_str', $employer_sort_by);
        if ($employer_sort_by == 'recent') {
            $qryvar_employer_sort_type = 'DESC';
            $qryvar_sort_by_column = 'post_date';
        } elseif ($employer_sort_by == 'alphabetical') {
            $qryvar_employer_sort_type = 'ASC';
            $qryvar_sort_by_column = 'post_title';
            remove_filter('posts_join_paged', array($this, 'edit_join'), 999, 2);
            remove_filter('posts_orderby', array($this, 'edit_orderby'), 999, 2);
        } elseif ($employer_sort_by == 'most_viewed') {
            $qryvar_employer_sort_type = 'DESC';
            $qryvar_sort_by_column = 'meta_value_num';
            $meta_key = 'jobsearch_employer_views_count';
            remove_filter('posts_join_paged', array($this, 'edit_join'), 999, 2);
            remove_filter('posts_orderby', array($this, 'edit_orderby'), 999, 2);
        }
        $args = array(
            'posts_per_page' => $employer_per_page,
            'paged' => $_REQUEST[$paging_var],
            'post_type' => 'employer',
            'post_status' => 'publish',
            'meta_key' => $meta_key,
            'order' => $qryvar_employer_sort_type,
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
        } else if (isset($atts['employer_cat']) && $atts['employer_cat'] != '') {
            $args['tax_query'][] = array(
                'taxonomy' => 'sector',
                'field' => 'slug',
                'terms' => $atts['employer_cat']
            );
        }

        if (isset($_REQUEST['loc_polygon_path']) && $_REQUEST['loc_polygon_path'] != '') {
            $loc_polygon_path = $_REQUEST['loc_polygon_path'];
            $all_post_ids = $this->employer_polygon_filter($loc_polygon_path, $all_post_ids, $element_filter_arr);
        }

        // recent employer query end
        if (!empty($all_post_ids)) {
            $args_count['post__in'] = $all_post_ids;
            $args['post__in'] = $all_post_ids;
        }

        $args = apply_filters('jobsearch_emp_listing_query_args_array', $args, $atts);
        $args_count = apply_filters('jobsearch_emp_listing_query_argscount_array', $args_count, $atts);

        add_filter('posts_where', 'jobsearch_search_query_results_filter', 10, 2);
        $employer_loop_obj = jobsearch_get_cached_obj('employer_result_cached_loop_obj1', $args, 12, false, 'wp_query');
        remove_filter('posts_where', 'jobsearch_search_query_results_filter', 10);

        $wpml_employer_totnum = $employer_totnum = $employer_loop_obj->found_posts;
        if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') && $wpml_employer_totnum == 0 && isset($trans_able_options['employer']) && $trans_able_options['employer'] == '2') {
            $sitepress_def_lang = $sitepress->get_default_language();
            $sitepress_curr_lang = $sitepress->get_current_language();
            $sitepress->switch_lang($sitepress_def_lang, true);

            $employer_loop_obj = jobsearch_get_cached_obj('employer_result_cached_loop_obj1', $args, 12, false, 'wp_query');
            $employer_totnum = $employer_loop_obj->found_posts;

            //
            $sitepress->switch_lang($sitepress_curr_lang, true);
        }
        remove_filter('posts_join_paged', array($this, 'edit_join'), 999, 2);
        remove_filter('posts_orderby', array($this, 'edit_orderby'), 999, 2);

        $page_container_view = get_post_meta($page_id, 'careerfy_field_page_view', true);
        ?>
        <form id="jobsearch_employer_frm_<?php echo absint($employer_short_counter); ?>">
            <?php
            //
            $emp_top_search = isset($atts['emp_top_search']) ? $atts['emp_top_search'] : '';

            //
            $listing_top_map = isset($atts['emp_top_map']) ? $atts['emp_top_map'] : '';
            $listing_top_map_zoom = isset($atts['emp_top_map_zoom']) && $atts['emp_top_map_zoom'] > 0 ? $atts['emp_top_map_zoom'] : 8;
            $listing_top_map_height = isset($atts['emp_top_map_height']) && $atts['emp_top_map_height'] > 0 ? $atts['emp_top_map_height'] : 450;
            if ($listing_top_map == 'yes') {
                $location_map_type = isset($jobsearch_plugin_options['location_map_type']) ? $jobsearch_plugin_options['location_map_type'] : '';
                if ($location_map_type == 'mapbox') {
                    wp_enqueue_script('jobsearch-mapbox');
                } else {
                    wp_enqueue_script('jobsearch-google-map');
                    wp_enqueue_script('jobsearch-map-infobox');
                    wp_enqueue_script('jobsearch-map-markerclusterer');
                }
                wp_enqueue_script('jobsearch-employer-lists-map');
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

                $map_marker_icon = isset($jobsearch_plugin_options['elistin_map_marker_img']['url']) ? $jobsearch_plugin_options['elistin_map_marker_img']['url'] : '';
                if ($map_marker_icon == '') {
                    $map_marker_icon = jobsearch_plugin_get_url('images/employer_map_marker.png');
                }
                $map_cluster_icon = isset($jobsearch_plugin_options['elistin_map_cluster_img']['url']) ? $jobsearch_plugin_options['elistin_map_cluster_img']['url'] : '';
                if ($map_cluster_icon == '') {
                    $map_cluster_icon = jobsearch_plugin_get_url('images/map_cluster.png');
                }
                //
                $map_list_arr = array();
                $employer_all_posts = $employer_loop_obj->posts;

                foreach ($employer_all_posts as $employer_post) {
                    $listing_latitude = get_post_meta($employer_post, 'jobsearch_field_location_lat', true);
                    $listing_longitude = get_post_meta($employer_post, 'jobsearch_field_location_lng', true);

                    if ($listing_latitude != '' && $listing_longitude != '') {
                        //sectors html
                        $get_pos_sectrs = wp_get_post_terms($employer_post, 'sector');
                        $map_pos_sectrs_html = '';
                        if (!empty($get_pos_sectrs)) {
                            $map_secresult_page = get_permalink();
                            $map_pos_sectrs_html .= ' ' . esc_html__('in', 'wp-jobsearch') . ' ';
                            foreach ($get_pos_sectrs as $get_pos_sectr) {
                                $map_pos_sectrs_html .= '<a href="' . add_query_arg(array('sector_cat' => $get_pos_sectr->slug, 'ajax_filter' => 'true'), $map_secresult_page) . '">' . $get_pos_sectr->name . '</a> ';
                            }
                        }
                        //logo img
                        $map_pos_thum_id = get_post_thumbnail_id($employer_post);
                        $map_pos_thumb_image = wp_get_attachment_image_src($map_pos_thum_id, 'thumbnail');
                        $map_pos_thumb_src = isset($map_pos_thumb_image[0]) && esc_url($map_pos_thumb_image[0]) != '' ? $map_pos_thumb_image[0] : '';
                        $map_pos_thumb_src = $map_pos_thumb_src == '' ? jobsearch_no_image_placeholder() : $map_pos_thumb_src;

                        //address
                        $map_posadres = jobsearch_job_item_address($employer_post);
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
                                    'id' => $employer_post,
                                    'title' => wp_trim_words(get_the_title($employer_post), 5),
                                    'link' => get_permalink($employer_post),
                                    'logo_img_url' => $map_pos_thumb_src,
                                    'address' => $map_posadres,
                                    'sector' => $map_pos_sectrs_html,
                                    'marker' => $map_marker_icon,
                                )
                            );
                        } else {
                            $map_list_arr[] = array(
                                'lat' => $listing_latitude,
                                'long' => $listing_longitude,
                                'id' => $employer_post,
                                'title' => wp_trim_words(get_the_title($employer_post), 5),
                                'link' => get_permalink($employer_post),
                                'logo_img_url' => $map_pos_thumb_src,
                                'address' => $map_posadres,
                                'sector' => $map_pos_sectrs_html,
                                'marker' => $map_marker_icon,
                            );
                        }
                    }
                }
                //
                $listn_map_arr = array(
                    'map_id' => $employer_short_counter,
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
                    if (isset($_REQUEST['ajax_filter']) && $_REQUEST['ajax_filter'] == 'true' && isset($_REQUEST['action']) && $_REQUEST['action'] == 'jobsearch_employers_content') {
                    ?>
                    jobsearch_listing_top_map(jobsearch_listing_dataobj, 'true');
                    <?php
                    }
                    ?>
                    jQuery(document).ready(function () {
                        jobsearch_listing_top_map(jobsearch_listing_dataobj, '');
                    });
                </script>
                <div class="jobsearch-listing-mapcon <?php echo($emp_top_search == 'yes' ? 'with-serch-map-both' : '') ?>">
                    <div id="listings-map-<?php echo absint($employer_short_counter); ?>"
                         class="jobsearch-joblist-map"
                         style="height: <?php echo($listing_top_map_height) ?>px;"></div>
                </div>
                <?php
                $map_html = ob_get_clean();
                echo apply_filters('jobsearch_emps_listin_topmap_html', $map_html, $listn_map_obj, $employer_short_counter, $listing_top_map_height, $atts);

                if ($page_container_view == 'wide') {
                    echo '<div class="container">';
                }
            }
            ?>
            <div style="display:none" id='employer_arg<?php echo absint($employer_short_counter); ?>'>
                <?php
                echo json_encode($employer_arg);
                ?>
            </div>
            <?php
            if ($emp_top_search == 'yes') {

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

                $top_serch_style = isset($atts['emp_top_search_view']) ? $atts['emp_top_search_view'] : '';

                $top_search_title = isset($atts['emp_top_search_title']) && !empty($atts['emp_top_search_title']) ? $atts['emp_top_search_title'] : 'yes';
                $top_search_location = isset($atts['emp_top_search_location']) && !empty($atts['emp_top_search_location']) ? $atts['emp_top_search_location'] : 'yes';
                $top_search_sector = isset($atts['emp_top_search_sector']) && !empty($atts['emp_top_search_sector']) ? $atts['emp_top_search_sector'] : 'yes';
                $vc_top_search_radius = isset($atts['emp_top_search_radius']) && !empty($atts['emp_top_search_radius']) ? $atts['emp_top_search_radius'] : 'yes';
                $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';

                $search_title_val = isset($_REQUEST['search_title']) ? $_REQUEST['search_title'] : '';
                $location_val = isset($_REQUEST['location']) ? ($_REQUEST['location']) : '';
                $cat_sector_val = isset($_REQUEST['sector_cat']) ? urldecode($_REQUEST['sector_cat']) : '';

                $search_title_val = jobsearch_esc_html($search_title_val);
                $location_val = jobsearch_esc_html($location_val);
                $cat_sector_val = jobsearch_esc_html($cat_sector_val);

                $search_main_class = '';
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
                $job_filters_loc = isset($atts['job_filters_loc']) ? $atts['job_filters_loc'] : '';
                $top_search_locsugg = isset($jobsearch_plugin_options['top_search_locsugg']) ? $jobsearch_plugin_options['top_search_locsugg'] : '';
                $top_search_geoloc = isset($jobsearch_plugin_options['top_search_geoloc']) ? $jobsearch_plugin_options['top_search_geoloc'] : '';
                $top_search_radius = isset($jobsearch_plugin_options['top_search_radius']) ? $jobsearch_plugin_options['top_search_radius'] : '';
                $top_search_def_radius = isset($jobsearch_plugin_options['top_search_def_radius']) ? $jobsearch_plugin_options['top_search_def_radius'] : 50;
                $top_search_max_radius = isset($jobsearch_plugin_options['top_search_max_radius']) ? $jobsearch_plugin_options['top_search_max_radius'] : 500;
                ?>
                <div class="jobsearch-top-searchbar jobsearch-typo-wrap <?php echo($search_main_class) ?> <?php echo $adv_search_on ?>">
                    <!-- Sub Header Form -->
                    <div class="jobsearch-subheader-form">
                        <div class="jobsearch-banner-search <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?>">
                            <ul>
                                <?php if ($top_search_title == 'yes') { ?>
                                    <li>
                                        <div class="<?php echo($top_search_autofill != 'no' ? 'jobsearch-sugges-search' : '') ?>">
                                            <input placeholder="<?php esc_html_e('Title, Keywords, or Phrase', 'wp-jobsearch') ?>"
                                                   name="search_title" value="<?php echo($search_title_val) ?>"
                                                   data-type="employer" type="text">
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
                                                       value="<?php echo urldecode($location_val) ?>" type="text">
                                            <?php } else { ?>
                                                <input placeholder="<?php esc_html_e('City, State or ZIP', 'wp-jobsearch') ?>"
                                                       autocomplete="off" class="jobsearch_search_location_field"
                                                       value="<?php echo urldecode($location_val) ?>" type="text">
                                                <input type="hidden"
                                                       class="loc_search_keyword <?php echo($top_search_geoloc != 'no' ? 'srch_autogeo_location' : '') ?>"
                                                       name="location"
                                                       value="<?php echo urldecode($location_val) ?>">
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
                                            <?php
                                        }
                                        ?>
                                    </li>
                                <?php }
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

                                if ($top_serch_style == 'advance') { ?>
                                    <li class="adv-srch-toggler">
                                        <a href="javascript:void(0);" class="adv-srch-toggle-btn">
                                            <span>+</span> <?php esc_html_e('Advance Search', 'wp-jobsearch') ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <li class="jobsearch-banner-submit">
                                    <input type="hidden" name="ajax_filter" value="true">
                                    <input type="submit" value=""> <i class="jobsearch-icon jobsearch-search"></i>
                                </li>
                            </ul>
                            <?php
                            if ($top_serch_style == 'advance') {
                                $sh_atts = isset($employer_arg['atts']) ? $employer_arg['atts'] : '';

                                $top_search_radius = isset($jobsearch_plugin_options['top_search_radius']) ? $jobsearch_plugin_options['top_search_radius'] : '';
                                $top_search_def_radius = isset($jobsearch_plugin_options['top_search_def_radius']) ? $jobsearch_plugin_options['top_search_def_radius'] : 50;
                                $top_search_max_radius = isset($jobsearch_plugin_options['top_search_max_radius']) ? $jobsearch_plugin_options['top_search_max_radius'] : 500;
                                ?>
                                <div class="adv-search-options">
                                    <ul>
                                        <?php
                                        if ($top_search_radius != 'no') {
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
                                        echo apply_filters('jobsearch_employer_top_filter_date_posted_box_html', '', $employer_short_counter, $sh_atts);
                                        echo apply_filters('jobsearch_custom_fields_top_filters_html', '', 'employer', $employer_short_counter);
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
            }
            ?>
            <div class="jobsearch-row">
                <?php
                if (($employer_filters_sidebar == 'yes') || (!empty($jobsearch_employer_sidebar))) {  // if sidebar on from element
                    set_query_var('employer_type', $employer_type);
                    set_query_var('employer_short_counter', $employer_short_counter);
                    set_query_var('employer_arg', $employer_arg);
                    set_query_var('employer_view', $employer_view);
                    set_query_var('args_count', $args_count);
                    set_query_var('employer_right_sidebar_content', $employer_right_sidebar_content);
                    set_query_var('atts', $atts);
                    set_query_var('employer_totnum', $employer_totnum);
                    set_query_var('page_url', $page_url);
                    set_query_var('employer_loop_obj', $employer_loop_obj);
                    set_query_var('global_rand_id', $employer_short_counter);
                    jobsearch_get_template_part('filters', 'employer-template', 'employers');

                    $content_columns = 'jobsearch-column-9 jobsearch-typo-wrap';
                } else {
                    $content_columns = 'jobsearch-column-12 jobsearch-typo-wrap';
                }
                ?>
                <div class="<?php echo esc_html($content_columns); ?>">
                    <div class="wp-jobsearch-employer-content wp-jobsearch-dev-employer-content"
                         id="jobsearch-data-employer-content-<?php echo esc_html($employer_short_counter); ?>"
                         data-id="<?php echo esc_html($employer_short_counter); ?>">
                        <div id="jobsearch-loader-<?php echo esc_html($employer_short_counter); ?>"></div>
                        <?php
                        $employers_title = isset($atts['employers_title']) ? $atts['employers_title'] : '';
                        $employers_subtitle = isset($atts['employers_subtitle']) ? $atts['employers_subtitle'] : '';
                        $employers_title_alignment = isset($atts['employers_title_alignment']) ? $atts['employers_title_alignment'] : '';
                        $employer_element_seperator = isset($atts['jobsearch_employers_seperator_style']) ? $atts['jobsearch_employers_seperator_style'] : '';
                        $jobsearch_employers_element_title_color = isset($atts['jobsearch_employers_element_title_color']) ? $atts['jobsearch_employers_element_title_color'] : '';
                        $jobsearch_employers_element_subtitle_color = isset($atts['jobsearch_employers_element_subtitle_color']) ? $atts['jobsearch_employers_element_subtitle_color'] : '';
                        $element_title_color = '';
                        if (isset($jobsearch_employers_element_title_color) && $jobsearch_employers_element_title_color != '') {
                            $element_title_color = ' style="color:' . $jobsearch_employers_element_title_color . ' ! important"';
                        }
                        $element_subtitle_color = '';
                        if (isset($jobsearch_employers_element_subtitle_color) && $jobsearch_employers_element_subtitle_color != '') {
                            $element_subtitle_color = ' style="color:' . $jobsearch_employers_element_subtitle_color . ' ! important"';
                        }
                        if ($employers_title != '' || $employers_subtitle != '') {
                            ?>
                            <div class="row">
                                <div class="jobsearch-column-12 jobsearch-typo-wrap">
                                    <div class="element-title <?php echo($employers_title_alignment); ?>">
                                        <?php
                                        if ($employers_title != '' || $employers_subtitle != '') {
                                            if ($employers_title != '') { ?>
                                                <h2<?php echo force_balance_tags($element_title_color); ?>><?php echo esc_html($employers_title); ?></h2>
                                                <?php
                                            }
                                            if ($employers_subtitle != '') {
                                                ?>
                                                <p <?php echo force_balance_tags($element_subtitle_color); ?>><?php echo esc_html($employers_subtitle); ?></p>
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
                        if (isset($employer_view)) {
                            // search keywords
                            $search_keyword_html = apply_filters('jobsearch_employer_search_keyword', '', $page_url, $atts);
                            echo force_balance_tags($search_keyword_html);
                            // sorting fields
                            $this->employer_search_sort_fields($atts, $employer_sort_by, $employer_short_counter, $employer_view, $employer_totnum, $employer_per_page);
                        }

                        set_query_var('employer_loop_obj', $employer_loop_obj);
                        set_query_var('employer_view', $employer_view);
                        set_query_var('employer_desc', $employer_desc);
                        set_query_var('employer_cus_fields', $employer_cus_fields);
                        set_query_var('employer_short_counter', $employer_short_counter);
                        set_query_var('atts', $atts);

                        jobsearch_get_template_part($employer_view, 'employer-template', 'employers');

                        echo apply_filters('jobsearch_emps_list_template_after', '', $employer_short_counter, $atts);
                        wp_reset_postdata();
                        ?>
                    </div>
                    <?php
                    // apply paging
                    $paging_args = array('total_posts' => $employer_totnum,
                        'employer_per_page' => $employer_per_page,
                        'paging_var' => $paging_var,
                        'show_pagination' => $pagination,
                        'employer_short_counter' => $employer_short_counter,
                    );

                    $this->jobsearch_employer_pagination_callback($paging_args);
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
                    'classes' => 'employer-counter',
                    'std' => $employer_short_counter,
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

    public function get_filter_arg($employer_short_counter = '', $exclude_meta_key = '')
    {
        global $jobsearch_post_employer_types;
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
        $filter_arr = apply_filters('jobsearch_custom_fields_load_filter_array_html', 'employer', $filter_arr, $exclude_meta_key);
        return $filter_arr;
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();

        if (empty($atts) && !is_array($atts)) {
            $atts = array();
        }
        if (!isset($atts['employer_cat'])) {
            $atts['employer_cat'] = '';
        }
        if (!isset($atts['employer_view'])) {
            $atts['employer_view'] = 'view-default';
        }
        if (!isset($atts['employer_sort_by'])) {
            $atts['employer_sort_by'] = '';
        }
        if (!isset($atts['employer_excerpt'])) {
            $atts['employer_excerpt'] = '20';
        }
        if (!isset($atts['employer_order'])) {
            $atts['employer_order'] = 'DESC';
        }
        if (!isset($atts['employer_orderby'])) {
            $atts['employer_orderby'] = 'date';
        }
        if (!isset($atts['employer_pagination'])) {
            $atts['employer_pagination'] = 'yes';
        }
        if (!isset($atts['employer_per_page'])) {
            $atts['employer_per_page'] = '10';
        }
        if (!isset($atts['employer_type'])) {
            $atts['employer_type'] = '';
        }
        if (!isset($atts['employer_filters'])) {
            $atts['employer_filters'] = 'yes';
        }
        if (!isset($atts['employer_filters_loc'])) {
            $atts['employer_filters_loc'] = 'yes';
        }
        if (!isset($atts['employer_filters_date'])) {
            $atts['employer_filters_date'] = 'yes';
        }
        if (!isset($atts['employer_filters_sector'])) {
            $atts['employer_filters_sector'] = 'yes';
        }
        if (!isset($atts['employer_filters_team'])) {
            $atts['employer_filters_team'] = 'yes';
        }
        if (!isset($atts['employer_loc_listing'])) {
            $atts['employer_loc_listing'] = 'country,city';
        }


        wp_enqueue_style('datetimepicker-style');
        wp_enqueue_script('datetimepicker-script');
        wp_enqueue_script('jquery-ui');
        wp_enqueue_script('jobsearch-employer-functions-script');
        do_action('jobsearch_notes_frontend_modal_popup');
        $employer_short_counter = isset($atts['employer_counter']) && $atts['employer_counter'] != '' ? ($atts['employer_counter']) : rand(123, 9999); // for shortcode counter
        if (false === ($employer_view = jobsearch_get_transient_obj('jobsearch_employer_view' . $employer_short_counter))) {
            $employer_view = isset($atts['employer_view']) ? $atts['employer_view'] : '';
        }
        jobsearch_set_transient_obj('jobsearch_employer_view' . $employer_short_counter, $employer_view);
        $employer_map_counter = rand(10000000, 99999999);
        $element_employer_footer = isset($atts['employer_footer']) ? $atts['employer_footer'] : '';
        $element_employer_map_position = isset($atts['employer_map_position']) ? $atts['employer_map_position'] : '';
        $map_change_class = '';
        if ($employer_view == 'map') {
            if ($element_employer_footer == 'yes') {
                echo '<script>';
                echo 'jQuery(document).ready(function () {'
                    . 'jQuery("footer#footer").hide();'
                    . '});';
                echo '</script>';
            }
        }
        wp_reset_query();
        do_action('employer_checks_enquire_lists_submit');
        do_action('jobsearch_employer_compare_sidebar');
        do_action('jobsearch_employer_enquiries_sidebar');
        $page_url = get_permalink(get_the_ID());
        ob_start();
        ?>
        <div class="wp-dp-employer-content"
             id="wp-dp-employer-content-<?php echo esc_html($employer_short_counter); ?>">
            <div class="dev-map-class-changer<?php echo($map_change_class); ?>">
                <div id="Employer-content-<?php echo esc_html($employer_short_counter); ?>">

                    <?php
                    $content = '';
                    $employer_arg = array(
                        'employer_short_counter' => $employer_short_counter,
                        'atts' => $atts,
                        'content' => $content,
                        'employer_map_counter' => $employer_map_counter,
                        'page_url' => $page_url,
                    );
                    $this->jobsearch_employers_content($employer_arg);
                    ?>
                </div>
            </div>
        </div>
        <?php
        $html = ob_get_clean();
        echo apply_filters('jobsearch_employers_listing_pagehtml', $html);
    }

    public function edit_join($join_paged_statement, $wp_query)
    {
        global $wpdb;
        if (
            !isset($wp_query->query) || $wp_query->is_page || (isset($wp_query->query['post_type']) && $wp_query->query['post_type'] != 'employer')
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

    /**
     * Edit orderby
     *
     * @param string $orderby_statement
     * @param WP_Query $wp_query
     * @return string
     */
    public function edit_orderby($orderby_statement, $wp_query)
    {
        if (
            !isset($wp_query->query) || $wp_query->is_page || (isset($wp_query->query['post_type']) && $wp_query->query['post_type'] != 'employer')
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