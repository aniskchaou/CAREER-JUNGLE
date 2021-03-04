<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

if (!defined('ABSPATH')) exit;

/**
 * @since 1.1.0
 */
class AdvanceSearch extends Widget_Base
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
        return 'advance-search';
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
        return __('Advance Search', 'careerfy-frame');
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
        return 'fa fa-gear';
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
        global $jobsearch_plugin_options;
        $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : 500;

        $all_page = array();
        $args = array(
            'sort_order' => 'asc',
            'sort_column' => 'post_title',
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'meta_key' => '',
            'meta_value' => '',
            'authors' => '',
            'child_of' => 0,
            'parent' => -1,
            'exclude_tree' => '',
            'number' => '',
            'offset' => 0,
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $pages = get_pages($args);
        if (!empty($pages)) {
            foreach ($pages as $page) {
                $all_page[$page->ID] = $page->post_title;
            }
        }
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Advance Search Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'view',
            [
                'label' => __('Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'view1',
                'options' => [
                    'view1' => __('Style 1', 'careerfy-frame'),
                    'view2' => __('Style 2', 'careerfy-frame'),
                    'view3' => __('Style 3', 'careerfy-frame'),
                    'view4' => __('Style 4', 'careerfy-frame'),
                    'view5' => __('Style 5', 'careerfy-frame'),
                    'view6' => __('Style 6', 'careerfy-frame'),
                    'view7' => __('Style 7', 'careerfy-frame'),
                    'view8' => __('Style 8', 'careerfy-frame'),
                    'view9' => __('Style 9', 'careerfy-frame'),
                    'view10' => __('Style 10', 'careerfy-frame'),
                    'view11' => __('Style 11', 'careerfy-frame'),
                    'view12' => __('Style 12', 'careerfy-frame'),
                    'view13' => __('Style 13', 'careerfy-frame'),
                    'view14' => __('Style 14', 'careerfy-frame'),
                    'view15' => __('Style 15', 'careerfy-frame'),
                    'view16' => __('Style 16', 'careerfy-frame'),
                    'view17' => __('Style 17', 'careerfy-frame'),
                    'view18' => __('Style 18', 'careerfy-frame'),
                    'view19' => __('Style 19', 'careerfy-frame'),
                    'view20' => __('Style 20', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'bg_img',
            [
                'label' => __('Background Image', 'careerfy-frame'),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'view' => 'view12'
                ]
            ]
        );
        $this->add_control(
            'small_search_title',
            [
                'label' => __('Small Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'view' => array('view18', 'view19', 'view20')
                ]
            ]
        );
        $this->add_control(
            'srch_title',
            [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'view' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7', 'view9', 'view11', 'view12', 'view13', 'view15', 'view16', 'view17')
                ]
            ]
        );
        $this->add_control(
            'no_total_jobtypes',
            [
                'label' => __('Total Number of Job Types', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'view' => array('view14', 'view15')
                ]
            ]
        );
        $this->add_control(
            'srch_desc',
            [
                'label' => __('Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
                'condition' => [
                    'view' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7', 'view9', 'view11', 'view12', 'view13', 'view15', 'view16', 'view18', 'view17')
                ]
            ]
        );
        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'banner_img',
            [
                'label' => __('Image', 'careerfy-frame'),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'description' => __('Will effect on style 11', 'careerfy-frame'),

            ]
        );
        $repeater->add_control(
            'img_link',
            [
                'label' => __('Image Link', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'description' => __('Will effect on style 11', 'careerfy-frame'),
            ]
        );

        $this->add_control(
            'adv_banner_images',
            [
                'label' => __('Advance Search Banner Will effect on style 11', 'careerfy-frame'),
                'type' => Controls_Manager::REPEATER,
                'description' => __('Will effect on style 11', 'careerfy-frame'),
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'img_link' => __(get_site_url(), 'careerfy-frame'),
                    ]
                ],
                'title_field' => '{{{ img_link }}}',
            ]
        );

        $this->add_control(
            'txt_below_forms_1',
            [
                'label' => __('Text Below Form 1', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'view' => 'view9'
                ]
            ]
        );

        $this->add_control(
            'result_page',
            [
                'label' => __('Search Result Page by Jobs', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'options' => $all_page,
                'condition' => [
                    'view' => array('view1', 'view2', 'view3', 'view4', 'view5', 'view6', 'view7', 'view8', 'view9', 'view11', 'view10', 'view12', 'view14', 'view15', 'view16')
                ]
            ]
        );

        $this->add_control(
            'result_page_2',
            [
                'label' => __('Search Result Page by Employer', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'options' => $all_page,
                'condition' => [
                    'view' => array('view1', 'view2', 'view3', 'view4', 'view5', 'view6', 'view7', 'view8', 'view9', 'view11', 'view10', 'view12', 'view14', 'view15', 'view16')
                ]
            ]
        );

        $this->add_control(
            'txt_below_forms_2',
            [
                'label' => __('Text Below Form 2', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'view' => 'view9'
                ]
            ]
        );
        $this->add_control(
            'result_page_3',
            [
                'label' => __('Search Result Page by Candidate', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'options' => $all_page,
                'condition' => [
                    'view' => array('view9', 'view12')
                ]
            ]
        );

        $this->add_control(
            'txt_below_forms_3',
            [
                'label' => __('Text Below Form 3', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'view' => 'view9'
                ]
            ]
        );
        $this->add_control(
            'btn1_txt',
            [
                'label' => __('Button 1 Text', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'description' => __("This will not show in Search Style 4.", "careerfy-frame"),
                'condition' => [
                    'view' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7')
                ]
            ]
        );
        $this->add_control(
            'btn1_url',
            [
                'label' => __('Button 1 URL', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'description' => __("This will not show in Search Style 4.", "careerfy-frame"),
                'condition' => [
                    'view' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7')
                ]
            ]
        );
        $this->add_control(
            'btn_1_icon',
            [
                'label' => __('Button 1 Icon', 'careerfy-frame'),
                'type' => Controls_Manager::ICONS,
                'description' => __("", "careerfy-frame"),
                'condition' => [
                    'view' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7')
                ]
            ]
        );
        $this->add_control(
            'btn2_txt',
            [
                'label' => __('Button 2 Text', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'description' => __("This will only show in Search Style 1.", "careerfy-frame"),
                'condition' => [
                    'view' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7')
                ]
            ]
        );
        $this->add_control(
            'btn2_url',
            [
                'label' => __('Button 2 URL', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'description' => __("This will only show in Search Style 1.", "careerfy-frame"),
                'condition' => [
                    'view' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7')
                ]
            ]
        );
        $this->add_control(
            'btn_2_icon',
            [
                'label' => __('Button 2 Icon', 'careerfy-frame'),
                'type' => Controls_Manager::ICONS,
                'description' => __("This will only show in Search Style 1.", "careerfy-frame"),
                'condition' => [
                    'view' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7')
                ]
            ]
        );
        $this->add_control(
            'content',
            [
                'label' => __('First Description', 'careerfy-frame'),
                'type' => Controls_Manager::WYSIWYG,
                'description' => __("", "careerfy-frame"),
                'condition' => [
                    'view' => array('view18', 'view19', 'view20')
                ]
            ]
        );


        $this->end_controls_section();


        $this->start_controls_section(
            'field_settings',
            [
                'label' => __('Fields Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'keyword_field',
            [
                'label' => __('Keyword Field', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'show',
                'options' => [
                    'show' => __('Show', 'careerfy-frame'),
                    'hide' => __('Hide', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'autofill_keyword',
            [
                'label' => __('Keyword Suggestions', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'options' => [
                    'no' => __('No', 'careerfy-frame'),
                    'yes' => __('Yes', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'location_field',
            [
                'label' => __('Location Field', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'show',
                'options' => [
                    'show' => __('Show', 'careerfy-frame'),
                    'hide' => __('Hide', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'radius_field',
            [
                'label' => __('Location Radius', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'show',
                'options' => [
                    'show' => __('Show', 'careerfy-frame'),
                    'hide' => __('Hide', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'autofill_location',
            [
                'label' => __('Location Suggestions', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'options' => [
                    'no' => __('No', 'careerfy-frame'),
                    'yes' => __('Yes', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'auto_geo_location',
            [
                'label' => __('AutoFill Geo Location', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'no',
                'options' => [
                    'no' => __('No', 'careerfy-frame'),
                    'yes' => __('Yes', 'careerfy-frame'),
                ],
            ]
        );
        if ($sectors_enable_switch == 'on') {
            $this->add_control(
                'category_field',
                [
                    'label' => __('Sector Field', 'careerfy-frame'),
                    'type' => Controls_Manager::SELECT2,
                    'default' => 'show',
                    'options' => [
                        'show' => __('Show', 'careerfy-frame'),
                        'hide' => __('Hide', 'careerfy-frame'),
                    ],
                ]
            );
        }
        $this->end_controls_section();
        $this->start_controls_section(
            'color_settings',
            [
                'label' => __('Color Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'search_title_color',
            [
                'label' => __('Title Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'view' => array('view1', 'view2', 'view3', 'view5', 'view6', 'view7', 'view9', 'view11', 'view12', 'view13', 'view15', 'view16', 'view17')
                ]
            ]
        );
        $this->add_control(
            'search_paragraph_color',
            [
                'label' => __('Paragraph Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
            ]
        );
        $this->add_control(
            'search_link_color',
            [
                'label' => __('Link Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
            ]
        );
        $this->add_control(
            'search_btn_bg_color',
            [
                'label' => __('Button Background Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
            ]
        );
        $this->add_control(
            'search_btn_txt_color',
            [
                'label' => __('Button Text Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        global $wpdb, $jobsearch_plugin_options;
        $atts = $this->get_settings_for_display();

        extract(shortcode_atts(array(
            'view' => 'view1',
            'bg_img' => '',
            'srch_title' => '',
            'srch_desc' => '',
            'srch_bg_img' => '',
            'result_page' => '',
            'result_page_2' => '',
            'result_page_3' => '',
            'txt_below_forms_1' => '',
            'txt_below_forms_2' => '',
            'txt_below_forms_3' => '',
            'radius_field' => 'show',
            'btn1_txt' => '',
            'btn1_url' => '',
            'btn2_txt' => '',
            'btn2_url' => '',
            'btn_1_icon' => '',
            'btn_2_icon' => '',
            'search_title_color' => '',
            'search_paragraph_color' => '',
            'search_link_color' => '',
            'search_btn_txt_color' => '',
            'search_btn_bg_color' => '',
            'search_bg_color' => '',
            'keyword_field' => 'show',
            'location_field' => 'show',
            'category_field' => 'show',
            'autofill_keyword' => 'no',
            'autofill_location' => 'no',
            'auto_geo_location' => 'no',
            'no_total_jobtypes' => '',
            'adv_banner_images' => '',
            'first_srch_desc' => '',
            'small_search_title' => '',
            'content' => '',
        ), $atts));


        $rand_num = rand();

        $design_css_class = '';

        $transparent_bg_color = '';

        // search title color
        $adv_search_title_color = '';
        if (isset($search_title_color) && !empty($search_title_color)) {
            $adv_search_title_color = ' style="color:' . $search_title_color . ' !important"';
        }
        // search paragraph color
        $adv_search_paragraph_color = '';
        if (isset($search_paragraph_color) && !empty($search_paragraph_color)) {
            $adv_search_paragraph_color = ' style="color:' . $search_paragraph_color . ' !important"';
        }
        // search link color
        $adv_search_link_color = '';
        if (isset($search_link_color) && !empty($search_link_color)) {
            $adv_search_link_color = ' style="color:' . $search_link_color . ' !important"';
        }
        // search buuton text color
        $adv_search_btn_txt_color = '';
        if (isset($search_btn_txt_color) && !empty($search_btn_txt_color)) {
            $adv_search_btn_txt_color = ' color:' . $search_btn_txt_color . ' !important;';
        }
        // search button backgroung color
        $adv_search_btn_bg_color = '';
        if (isset($search_btn_bg_color) && !empty($search_btn_bg_color)) {
            $adv_search_btn_bg_color = ' background-color:' . $search_btn_bg_color . ' !important;';
        }
        $button_style = '';
        if (!empty($adv_search_btn_txt_color) || !empty($adv_search_btn_bg_color)) {
            $button_style = ' style="' . $adv_search_btn_txt_color . $adv_search_btn_bg_color . '"';
        }

        $job_types = '';
        if ($view == 'view14' || $view == 'view15' || $view == 'view16') {
            $job_types = $no_total_jobtypes != "" ? get_terms('jobtype', array('number' => $no_total_jobtypes)) : get_terms('jobtype');
        }

        $location_map_type = isset($jobsearch_plugin_options['location_map_type']) ? $jobsearch_plugin_options['location_map_type'] : '';
        $top_search_radius = isset($jobsearch_plugin_options['top_search_radius']) ? $jobsearch_plugin_options['top_search_radius'] : '';
        $sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : 500;

        ob_start();
        if (class_exists('JobSearch_plugin')) {
            wp_enqueue_script('datetimepicker-script');
            if ($view == 'view20') {
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }
                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));
                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }
                $all_fields_class = '';
                if ($location_field == 'show' && (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') && $keyword_field == 'show') {
                    $all_fields_class = 'all-searches-on';
                }
                ?>
                <div class="careerfy-twentytwo-search <?php echo($all_fields_class) ?> <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?>">

                    <?php if (!empty($small_search_title)) { ?>
                        <small class="careerfy-twentytwo-search-tag" <?php echo($adv_search_title_color) ?>><?php echo($small_search_title) ?>
                            <img src="<?php echo trailingslashit(get_template_directory_uri()) . 'images/arrow-plane.png'; ?>"/>
                        </small>
                    <?php }

                    if ($content != '') {
                        echo force_balance_tags($content);
                    }

                    if ($srch_desc != '') { ?>
                        <span class="careerfy-twentytwo-search-description" <?php echo($adv_search_paragraph_color) ?>><?php echo($srch_desc) ?></span>
                    <?php } ?>
                    <div class="careerfy-twentytwo-search-tabs">
                        <ul class="careerfy-search-twentytwo-tabs-nav">
                            <li class="active">
                                <a data-toggle="tab"
                                   href="#home"><i
                                            class="fa fa-black-tie"></i><span><?php echo esc_html__("I am looking tutoring", "careerfy-frame") ?></span></a>
                            </li>

                            <li><a data-toggle="tab"
                                   href="#menu1"><i
                                            class="fa fa-eye"></i><span><?php echo esc_html__("I am looking jobs", "careerfy-frame") ?></span></a>
                            </li>

                        </ul>
                        <div class="tab-content">
                            <div id="home" class="tab-pane fade in active">
                                <form method="get" action="<?php echo(get_permalink($result_page)); ?>"
                                      class="careerfy-twentytwo-loc-search">
                                    <ul>
                                        <?php
                                        if ($keyword_field == 'show') {
                                            if ($autofill_keyword == 'yes') {
                                                wp_enqueue_script('jobsearch-search-box-sugg');
                                            } ?>
                                            <li>
                                                <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                    <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                                           name="search_title" data-type="job" type="text">
                                                    <span class="sugg-search-loader"></span>
                                                </div>
                                            </li>
                                        <?php }
                                        if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                            <li>
                                                <div class="careerfy-select-style">
                                                    <select name="sector_cat" class="selectize-select">
                                                        <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                                        <?php
                                                        foreach ($all_sectors as $term_sector) { ?>
                                                            <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </li>
                                        <?php }
                                        if ($location_field == 'show') {
                                            if ($autofill_location == 'yes') {
                                                if ($location_map_type == 'mapbox') {
                                                    wp_enqueue_script('jobsearch-mapbox');
                                                    wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                    wp_enqueue_script('mapbox-geocoder-polyfill');
                                                    wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                                } else {
                                                    wp_enqueue_script('jobsearch-google-map');
                                                }
                                                wp_enqueue_script('jobsearch-location-autocomplete');
                                            }
                                            ob_start();
                                            ?>
                                            <li>
                                                <div class="jobsearch_searchloc_div">
                                                    <?php
                                                    if ($autofill_location == 'yes') { ?>
                                                        <span class="loc-loader"></span>
                                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                               autocomplete="off"
                                                               class="jobsearch_search_location_field"
                                                               type="text">
                                                        <input type="hidden"
                                                               class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location">
                                                    <?php } else { ?>
                                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                               class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location" type="text">
                                                        <?php
                                                    }
                                                    if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                        echo get_radius_tooltip();
                                                    }
                                                    //
                                                    if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn"><i
                                                                    class="careerfy-icon careerfy-location"></i></a>
                                                    <?php }
                                                    if ($auto_geo_location == 'yes') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn"
                                                           onclick="JobsearchGetClientLocation()"><i
                                                                    class="careerfy-icon careerfy-location"></i></a>
                                                    <?php } ?>
                                                </div>
                                            </li>
                                            <?php
                                            $srchfield_html = ob_get_clean();
                                            echo apply_filters('jobsearch_careerfy_advance_search_sh_frmloc', $srchfield_html);
                                        } ?>
                                        <li><input type="submit" value="<?php esc_html_e("", 'careerfy-frame') ?>">
                                            <i class="careerfy-icon careerfy-search-o"></i></li>
                                    </ul>


                                </form>
                            </div>
                            <div id="menu1" class="tab-pane fade">
                                <form method="get" action="<?php echo(get_permalink($result_page_3)); ?>"
                                      class="careerfy-twentytwo-loc-search">
                                    <ul>
                                        <?php
                                        if ($keyword_field == 'show') {
                                            if ($autofill_keyword == 'yes') {
                                                wp_enqueue_script('jobsearch-search-box-sugg');
                                            } ?>
                                            <li>
                                                <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                    <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                                           name="search_title" data-type="job" type="text">
                                                    <span class="sugg-search-loader"></span>
                                                </div>
                                            </li>
                                        <?php }
                                        if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                            <li>
                                                <div class="careerfy-select-style">
                                                    <select name="sector_cat" class="selectize-select">
                                                        <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                                        <?php
                                                        foreach ($all_sectors as $term_sector) { ?>
                                                            <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </li>
                                        <?php }
                                        if ($location_field == 'show') {
                                            if ($autofill_location == 'yes') {
                                                if ($location_map_type == 'mapbox') {
                                                    wp_enqueue_script('jobsearch-mapbox');
                                                    wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                    wp_enqueue_script('mapbox-geocoder-polyfill');
                                                    wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                                } else {
                                                    wp_enqueue_script('jobsearch-google-map');
                                                }
                                                wp_enqueue_script('jobsearch-location-autocomplete');
                                            }
                                            ob_start();
                                            ?>
                                            <li>
                                                <div class="jobsearch_searchloc_div">
                                                    <?php if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                        echo get_radius_tooltip();
                                                    }
                                                    if ($autofill_location == 'yes') { ?>
                                                        <span class="loc-loader"></span>
                                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                               autocomplete="off"
                                                               class="jobsearch_search_location_field"
                                                               type="text">
                                                        <input type="hidden"
                                                               class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location">
                                                    <?php } else { ?>
                                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                               class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location" type="text">
                                                        <?php
                                                    }
                                                    if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                        echo get_radius_tooltip();
                                                    }
                                                    //
                                                    if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn"><i
                                                                    class="careerfy-icon careerfy-location"></i></a>
                                                    <?php }
                                                    if ($auto_geo_location == 'yes') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn"
                                                           onclick="JobsearchGetClientLocation()"><i
                                                                    class="careerfy-icon careerfy-location"></i></a>
                                                    <?php } ?>
                                                </div>
                                            </li>
                                            <?php
                                            $srchfield_html = ob_get_clean();
                                            echo apply_filters('jobsearch_careerfy_advance_search_sh_frmloc', $srchfield_html);
                                        } ?>
                                        <li><input type="submit" value="<?php esc_html_e("", 'careerfy-frame') ?>">
                                            <i class="careerfy-icon careerfy-search-o"></i></li>
                                    </ul>


                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } else if ($view == 'view19') {
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }
                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));
                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }
                $all_fields_class = '';
                if ($location_field == 'show' && (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') && $keyword_field == 'show') {
                    $all_fields_class = 'all-searches-on';
                }
                ?>

                <div class="careerfy-twentyone-search <?php echo($all_fields_class) ?> <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?>">
                    <div class="careerfy-twentyone-search-inner">
                        <?php if (!empty($small_search_title)) { ?>
                            <small class="careerfy-twentyone-search-tag" <?php echo($adv_search_title_color) ?>><?php echo($small_search_title) ?></small>
                        <?php } ?>
                        <?php if ($content != '') { ?>
                            <h1 <?php echo($adv_search_title_color) ?>><?php echo($content) ?> <img
                                        src="<?php echo trailingslashit(get_template_directory_uri()) . 'images/text-arrow.png'; ?>">
                            </h1>
                        <?php }
                        if ($srch_desc != '') { ?>
                            <span class="careerfy-twentyone-search-description" <?php echo($adv_search_paragraph_color) ?>><?php echo($srch_desc) ?></span>
                        <?php } ?>
                        <div class="careerfy-twentyone-search-tabs">
                            <ul class="careerfy-search-twentyone-tabs-nav">
                                <li class="active">
                                    <a data-toggle="tab"
                                       href="#home"><i
                                                class="fa fa-black-tie"></i><span><?php echo esc_html__("Find Help", "careerfy-frame") ?></span></a>
                                </li>
                                <?php
                                ob_start();
                                ?>
                                <li><a data-toggle="tab"
                                       href="#menu1"><i
                                                class="fa fa-eye"></i><span><?php echo esc_html__("Looking job", "careerfy-frame") ?></span></a>
                                </li>
                                <?php
                                $html = ob_get_clean();
                                echo apply_filters('careerfy_adv_srch_sh_view12_findcand_tab', $html);
                                ?>
                            </ul>
                            <div class="tab-content">
                                <div id="home" class="tab-pane fade in active">
                                    <form method="get" action="<?php echo(get_permalink($result_page)); ?>"
                                          class="careerfy-twentyone-loc-search">
                                        <ul>
                                            <?php if ($keyword_field == 'show') {
                                                if ($autofill_keyword == 'yes') {
                                                    wp_enqueue_script('jobsearch-search-box-sugg');
                                                }
                                                ?>
                                                <li>
                                                    <i class="careerfy-icon careerfy-search-o"></i>
                                                    <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                        <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                                               name="search_title" data-type="job" type="text">
                                                        <span class="sugg-search-loader"></span>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                            <?php if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                                <li>
                                                    <div class="careerfy-select-style">
                                                        <select name="sector_cat" class="selectize-select">
                                                            <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                                            <?php
                                                            foreach ($all_sectors as $term_sector) { ?>
                                                                <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                            <?php if ($location_field == 'show') {
                                                if ($autofill_location == 'yes') {
                                                    if ($location_map_type == 'mapbox') {
                                                        wp_enqueue_script('jobsearch-mapbox');
                                                        wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                        wp_enqueue_script('mapbox-geocoder-polyfill');
                                                        wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                                    } else {
                                                        wp_enqueue_script('jobsearch-google-map');
                                                    }
                                                    wp_enqueue_script('jobsearch-location-autocomplete');
                                                }
                                                ob_start();
                                                ?>
                                                <li>
                                                    <div class="jobsearch_searchloc_div">
                                                        <?php
                                                        if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                            echo get_radius_tooltip();
                                                        }
                                                        if ($autofill_location == 'yes') {
                                                            ?>
                                                            <span class="loc-loader"></span>
                                                            <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                                   autocomplete="off"
                                                                   class="jobsearch_search_location_field"
                                                                   type="text">
                                                            <input type="hidden"
                                                                   class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                                   name="location">
                                                        <?php } else {
                                                            if ($auto_geo_location == 'no') { ?>
                                                                <i class="careerfy-icon careerfy-pin-line"></i>
                                                            <?php } ?>
                                                            <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                                   class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                                   name="location" type="text">
                                                            <?php
                                                        }
                                                        //

                                                        if ($auto_geo_location == 'yes') {
                                                            ?>
                                                            <a href="javascript:void(0);" class="geolction-btn"
                                                               onclick="JobsearchGetClientLocation()"><i
                                                                        class="careerfy-icon careerfy-location"></i></a>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>

                                                </li>
                                                <?php
                                                $srchfield_html = ob_get_clean();
                                                echo apply_filters('jobsearch_careerfy_advance_search_sh_frmloc', $srchfield_html);
                                            } ?>
                                        </ul>

                                        <input type="submit" value="<?php esc_html_e("Search", 'careerfy-frame') ?>">
                                    </form>
                                </div>
                                <div id="menu1" class="tab-pane fade">
                                    <form method="get" action="<?php echo(get_permalink($result_page_3)); ?>"
                                          class="careerfy-twentyone-loc-search">
                                        <ul>
                                            <?php if ($keyword_field == 'show') {
                                                if ($autofill_keyword == 'yes') {
                                                    wp_enqueue_script('jobsearch-search-box-sugg');
                                                }
                                                ?>
                                                <li>
                                                    <i class="careerfy-icon careerfy-search-o"></i>
                                                    <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                        <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                                               name="search_title" data-type="job" type="text">
                                                        <span class="sugg-search-loader"></span>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                            <?php if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                                <li>
                                                    <div class="careerfy-select-style">
                                                        <select name="sector_cat" class="selectize-select">
                                                            <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                                            <?php
                                                            foreach ($all_sectors as $term_sector) { ?>
                                                                <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                            <?php if ($location_field == 'show') {
                                                if ($autofill_location == 'yes') {
                                                    if ($location_map_type == 'mapbox') {
                                                        wp_enqueue_script('jobsearch-mapbox');
                                                        wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                        wp_enqueue_script('mapbox-geocoder-polyfill');
                                                        wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                                    } else {
                                                        wp_enqueue_script('jobsearch-google-map');
                                                    }
                                                    wp_enqueue_script('jobsearch-location-autocomplete');
                                                }
                                                ob_start();
                                                ?>
                                                <li>
                                                    <div class="jobsearch_searchloc_div">
                                                        <?php
                                                        if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                            echo get_radius_tooltip();
                                                        }
                                                        if ($autofill_location == 'yes') { ?>
                                                            <span class="loc-loader"></span>
                                                            <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                                   autocomplete="off"
                                                                   class="jobsearch_search_location_field"
                                                                   type="text">
                                                            <input type="hidden"
                                                                   class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                                   name="location">
                                                        <?php } else {
                                                            if ($auto_geo_location == 'no') { ?>
                                                                <i class="careerfy-icon careerfy-pin-line"></i>
                                                            <?php } ?>
                                                            <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                                   class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                                   name="location" type="text">
                                                            <?php
                                                        }
                                                        //

                                                        if ($auto_geo_location == 'yes') {
                                                            ?>
                                                            <a href="javascript:void(0);" class="geolction-btn"
                                                               onclick="JobsearchGetClientLocation()"><i
                                                                        class="careerfy-icon careerfy-location"></i></a>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </li>
                                                <?php
                                                $srchfield_html = ob_get_clean();
                                                echo apply_filters('jobsearch_careerfy_advance_search_sh_frmloc', $srchfield_html);
                                            } ?>
                                        </ul>
                                        <input type="submit" value="<?php esc_html_e("Search", 'careerfy-frame') ?>">

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } else if ($view == 'view18') {
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }

                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));
                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }
                $all_fields_class = '';
                if ($location_field == 'show' && (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') && $keyword_field == 'show') {
                    $all_fields_class = 'all-searches-on';
                }
                ?>
                <div class="careerfy-twenty-search <?php echo($all_fields_class) ?> <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?>">
                    <?php if (!empty($small_search_title)) { ?>
                        <small class="careerfy-twenty-search-tag"><?php echo($small_search_title) ?></small>
                    <?php } ?>
                    <?php if ($content != '') { ?>
                        <h1 <?php echo($adv_search_title_color) ?>><?php echo($content) ?> <img
                                    src="<?php echo trailingslashit(get_template_directory_uri()) . 'images/title-arrow.png'; ?>">
                        </h1>
                    <?php }
                    if ($srch_desc != '') { ?>
                        <span class="careerfy-twenty-search-description" <?php echo($adv_search_paragraph_color) ?>><?php echo($srch_desc) ?></span>
                    <?php } ?>
                    <div class="careerfy-twenty-search-tabs">
                        <ul class="careerfy-search-twenty-tabs-nav">
                            <li class="active">
                                <a data-toggle="tab"
                                   href="#home"><span><?php echo esc_html__("looking for animal care", "careerfy-frame") ?></span></a>
                            </li>

                            <li><a data-toggle="tab"
                                   href="#menu1"><span><?php echo esc_html__("looking for a job", "careerfy-frame") ?></span></a>
                            </li>

                        </ul>
                        <div class="tab-content">
                            <div id="home" class="tab-pane fade in active">
                                <form method="get" action="<?php echo(get_permalink($result_page)); ?>"
                                      class="careerfy-twenty-loc-search">
                                    <ul>
                                        <?php
                                        if ($keyword_field == 'show') {
                                            if ($autofill_keyword == 'yes') {
                                                wp_enqueue_script('jobsearch-search-box-sugg');
                                            }
                                            ?>
                                            <li><i class="careerfy-icon careerfy-search-o"></i>
                                                <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                    <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                                           name="search_title" data-type="job" type="text">
                                                    <span class="sugg-search-loader"></span>
                                                </div>
                                            </li>
                                        <?php }

                                        if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                            <li>
                                                <div class="careerfy-select-style">
                                                    <select name="sector_cat" class="selectize-select">
                                                        <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                                        <?php
                                                        foreach ($all_sectors as $term_sector) { ?>
                                                            <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                            </li>
                                        <?php } ?>
                                        <?php if ($location_field == 'show') {
                                            if ($autofill_location == 'yes') {
                                                if ($location_map_type == 'mapbox') {
                                                    wp_enqueue_script('jobsearch-mapbox');
                                                    wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                    wp_enqueue_script('mapbox-geocoder-polyfill');
                                                    wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                                } else {
                                                    wp_enqueue_script('jobsearch-google-map');
                                                }
                                                wp_enqueue_script('jobsearch-location-autocomplete');
                                            }
                                            ob_start();
                                            ?>
                                            <li>
                                                <div class="jobsearch_searchloc_div">
                                                    <?php
                                                    if ($autofill_location == 'yes') { ?>

                                                        <span class="loc-loader"></span>
                                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                               autocomplete="off"
                                                               class="jobsearch_search_location_field"
                                                               type="text">
                                                        <?php if ($auto_geo_location == 'no') { ?>
                                                            <i class="careerfy-icon careerfy-pin-line"></i>
                                                        <?php } ?>
                                                        <input type="hidden"
                                                               class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location">
                                                    <?php } else { ?>
                                                        <?php if ($auto_geo_location == 'no') { ?>
                                                            <i class="careerfy-icon careerfy-pin-line"></i>
                                                        <?php } ?>
                                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                               class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location" type="text">
                                                        <?php
                                                    }
                                                    if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                        echo get_radius_tooltip();
                                                    }
                                                    //

                                                    if ($auto_geo_location == 'yes') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn"
                                                           onclick="JobsearchGetClientLocation()"><i
                                                                    class="careerfy-icon careerfy-location"></i></a>
                                                    <?php } ?>
                                                </div>
                                            </li>
                                            <?php
                                            $srchfield_html = ob_get_clean();
                                            echo apply_filters('jobsearch_careerfy_advance_search_sh_frmloc', $srchfield_html);
                                        } ?>

                                        <li class="careerfy-twenty-loc-submit"><input type="submit"
                                                                                      value="<?php esc_html_e("", 'careerfy-frame') ?>">
                                            <i class="careerfy-icon careerfy-search-o"></i>
                                        </li>
                                    </ul>
                                </form>
                            </div>
                            <div id="menu1" class="tab-pane fade">
                                <form method="get" action="<?php echo(get_permalink($result_page_3)); ?>"
                                      class="careerfy-twenty-loc-search">
                                    <ul>
                                        <?php if ($keyword_field == 'show') { ?>
                                            <li>

                                                <?php if ($autofill_keyword == 'yes') {
                                                    wp_enqueue_script('jobsearch-search-box-sugg');
                                                } ?>
                                                <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                    <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                                           name="search_title" data-type="job" type="text">
                                                    <span class="sugg-search-loader"></span>
                                                </div>

                                            </li>
                                        <?php } ?>
                                        <?php

                                        if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                            <li>
                                                <div class="careerfy-select-style">
                                                    <select name="sector_cat" class="selectize-select">
                                                        <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                                        <?php
                                                        foreach ($all_sectors as $term_sector) { ?>
                                                            <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                            </li>
                                        <?php }
                                        if ($location_field == 'show') {
                                            if ($autofill_location == 'yes') {
                                                if ($location_map_type == 'mapbox') {
                                                    wp_enqueue_script('jobsearch-mapbox');
                                                    wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                    wp_enqueue_script('mapbox-geocoder-polyfill');
                                                    wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                                } else {
                                                    wp_enqueue_script('jobsearch-google-map');
                                                }
                                                wp_enqueue_script('jobsearch-location-autocomplete');
                                            }

                                            ob_start();
                                            ?>
                                            <li>
                                                <div class="jobsearch_searchloc_div">
                                                    <?php
                                                    if ($autofill_location == 'yes') { ?>

                                                        <span class="loc-loader"></span>
                                                        <?php if ($auto_geo_location == 'no') { ?>
                                                            <i class="careerfy-icon careerfy-pin-line"></i>
                                                        <?php } ?>
                                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                               autocomplete="off"
                                                               class="jobsearch_search_location_field"
                                                               type="text">
                                                        <input type="hidden"
                                                               class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location">
                                                    <?php } else { ?>
                                                        <?php if ($auto_geo_location == 'no') { ?>
                                                            <i class="careerfy-icon careerfy-pin-line"></i>
                                                        <?php } ?>
                                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                               class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location" type="text">
                                                        <?php
                                                    }
                                                    //
                                                    if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                        echo get_radius_tooltip();
                                                    }

                                                    if ($auto_geo_location == 'yes') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn"
                                                           onclick="JobsearchGetClientLocation()"><i
                                                                    class="careerfy-icon careerfy-location"></i></a>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </li>
                                            <?php
                                            $srchfield_html = ob_get_clean();
                                            echo apply_filters('jobsearch_careerfy_advance_search_sh_frmloc', $srchfield_html);
                                        } ?>
                                        <li class="careerfy-twenty-loc-submit">
                                            <input type="submit" value="<?php esc_html_e("", 'careerfy-frame') ?>">
                                            <i class="careerfy-icon careerfy-search-o"></i>
                                        </li>

                                    </ul>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } else if ($view == 'view17') {
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }
                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));
                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }
                $all_fields_class = '';
                if ($location_field == 'show' && (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') && $keyword_field == 'show') {
                    $all_fields_class = 'all-searches-on';
                }
                ?>

                <div class="careerfy-nineteen-search <?php echo($all_fields_class) ?> <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?>">
                    <?php if ($srch_title != '') { ?>
                        <h1 <?php echo($adv_search_title_color) ?>><?php echo($srch_title) ?></h1>
                    <?php }
                    if ($srch_desc != '') { ?>
                        <span class="careerfy-nineteen-search-description" <?php echo($adv_search_paragraph_color) ?>><?php echo($srch_desc) ?></span>
                    <?php } ?>
                    <div class="careerfy-nineteen-search-tabs">
                        <ul class="careerfy-search-nineteen-tabs-nav">
                            <li class="active">
                                <a data-toggle="tab"
                                   href="#home"><i
                                            class="fa fa-black-tie"></i><span><?php echo esc_html__('Find Help', 'careerfy-frame') ?></span></a>
                            </li>

                            <li><a data-toggle="tab"
                                   href="#menu1"><i
                                            class="fa fa-eye"></i><span><?php echo esc_html__('Looking Job', 'careerfy-frame') ?></span></a>
                            </li>

                        </ul>
                        <div class="tab-content">
                            <div id="home" class="tab-pane fade in active">
                                <form method="get" action="<?php echo(get_permalink($result_page)); ?>"
                                      class="careerfy-nineteen-loc-search">
                                    <ul>
                                        <?php
                                        if ($keyword_field == 'show') {
                                            if ($autofill_keyword == 'yes') {
                                                wp_enqueue_script('jobsearch-search-box-sugg');
                                            }
                                            ?>
                                            <li><i class="careerfy-icon careerfy-search-o"></i>
                                                <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                    <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                                           name="search_title" data-type="job" type="text">
                                                    <span class="sugg-search-loader"></span>
                                                </div>
                                            </li>
                                        <?php }
                                        if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                            <li>

                                                <div class="careerfy-select-style">
                                                    <select name="sector_cat" class="selectize-select">
                                                        <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                                        <?php
                                                        foreach ($all_sectors as $term_sector) { ?>
                                                            <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                            </li>
                                        <?php }
                                        if ($location_field == 'show') {
                                            ob_start();
                                            ?>
                                            <li>
                                                <div class="jobsearch_searchloc_div">
                                                    <?php
                                                    if ($autofill_location == 'yes') {
                                                        if ($autofill_location == 'yes') {
                                                            if ($location_map_type == 'mapbox') {
                                                                wp_enqueue_script('jobsearch-mapbox');
                                                                wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                                wp_enqueue_script('mapbox-geocoder-polyfill');
                                                                wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                                            } else {
                                                                wp_enqueue_script('jobsearch-google-map');
                                                            }
                                                            wp_enqueue_script('jobsearch-location-autocomplete');
                                                        } ?>


                                                        <span class="loc-loader"></span>
                                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                               autocomplete="off"
                                                               class="jobsearch_search_location_field"
                                                               type="text">
                                                        <input type="hidden"
                                                               class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location">
                                                    <?php } else { ?>
                                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                               class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location" type="text">
                                                        <?php
                                                    }
                                                    if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                        echo get_radius_tooltip();
                                                    }
                                                    if ($top_search_radius == 'yes' && $radius_field == 'show') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn">
                                                            <i class="careerfy-icon careerfy-location"></i></a>
                                                    <?php }
                                                    if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn"><i
                                                                    class="careerfy-icon careerfy-location"></i></a>
                                                    <?php }
                                                    if ($auto_geo_location == 'yes') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn"
                                                           onclick="JobsearchGetClientLocation()"><i
                                                                    class="careerfy-icon careerfy-location"></i></a>
                                                    <?php } ?>
                                                </div>
                                            </li>
                                            <?php
                                            $srchfield_html = ob_get_clean();
                                            echo apply_filters('jobsearch_careerfy_advance_search_sh_frmloc', $srchfield_html);
                                        } ?>

                                        <li><input type="submit"
                                                   value="<?php esc_html_e("Search Now", 'careerfy-frame') ?>">
                                        </li>
                                    </ul>
                                </form>
                            </div>
                            <div id="menu1" class="tab-pane fade">
                                <form method="get" action="<?php echo(get_permalink($result_page_3)); ?>"
                                      class="careerfy-nineteen-loc-search">
                                    <ul>
                                        <?php if ($keyword_field == 'show') { ?>
                                            <li>
                                                <?php if ($autofill_keyword == 'yes') {
                                                    wp_enqueue_script('jobsearch-search-box-sugg');
                                                } ?>
                                                <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                    <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                                           name="search_title" data-type="job" type="text">
                                                    <span class="sugg-search-loader"></span>
                                                </div>

                                            </li>
                                        <?php } ?>
                                        <?php

                                        if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                            <li>
                                                <div class="careerfy-select-style">
                                                    <select name="sector_cat" class="selectize-select">
                                                        <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                                        <?php
                                                        foreach ($all_sectors as $term_sector) { ?>
                                                            <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                            </li>
                                        <?php } ?>
                                        <?php if ($location_field == 'show') {
                                            ob_start();
                                            ?>
                                            <li>
                                                <div class="jobsearch_searchloc_div">
                                                    <?php
                                                    if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                        echo get_radius_tooltip();
                                                    }
                                                    if ($autofill_location == 'yes') {
                                                        if ($autofill_location == 'yes') {
                                                            if ($location_map_type == 'mapbox') {
                                                                wp_enqueue_script('jobsearch-mapbox');
                                                                wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                                wp_enqueue_script('mapbox-geocoder-polyfill');
                                                                wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                                            } else {
                                                                wp_enqueue_script('jobsearch-google-map');
                                                            }
                                                            wp_enqueue_script('jobsearch-location-autocomplete');
                                                        } ?>

                                                        <span class="loc-loader"></span>
                                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                               autocomplete="off"
                                                               class="jobsearch_search_location_field"
                                                               type="text">
                                                        <input type="hidden"
                                                               class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location">
                                                    <?php } else { ?>
                                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                               class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location" type="text">
                                                        <?php
                                                    }

                                                    //
                                                    if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn"><i
                                                                    class="careerfy-icon careerfy-location"></i></a>
                                                    <?php }
                                                    if ($auto_geo_location == 'yes') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn"
                                                           onclick="JobsearchGetClientLocation()"><i
                                                                    class="careerfy-icon careerfy-location"></i></a>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </li>
                                            <?php
                                            $srchfield_html = ob_get_clean();
                                            echo apply_filters('jobsearch_careerfy_advance_search_sh_frmloc', $srchfield_html);
                                        } ?>
                                        <li><input type="submit"
                                                   value="<?php esc_html_e("Search Now", 'careerfy-frame') ?>"></li>
                                    </ul>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="careerfy-nineteen-category-list">
                        <?php
                        $to_result_page = $result_page;
                        $top_sectors = $wpdb->get_col($wpdb->prepare("SELECT terms.term_id FROM $wpdb->terms AS terms"
                            . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                            . " LEFT JOIN $wpdb->termmeta AS term_meta ON(terms.term_id = term_meta.term_id) "
                            . " WHERE term_tax.taxonomy=%s AND term_meta.meta_key=%s"
                            . " ORDER BY cast(term_meta.meta_value as unsigned) DESC LIMIT 4", 'sector', 'active_jobs_count'));


                        if (!empty($top_sectors) && !is_wp_error($top_sectors)) { ?>
                            <ul>
                                <?php
                                foreach ($top_sectors as $term_id) {

                                    $term_sector = get_term_by('id', $term_id, 'sector');
                                    $term_fields = get_term_meta($term_sector->term_id, 'careerfy_frame_cat_fields', true);
                                    $term_icon = isset($term_fields['icon']) ? $term_fields['icon'] : '';
                                    $term_color = isset($term_fields['color']) ? $term_fields['color'] : '';
                                    $term_image = isset($term_fields['image']) ? $term_fields['image'] : '';

                                    $cat_goto_link = add_query_arg(array('sector_cat' => $term_sector->slug), get_permalink($to_result_page));
                                    $cat_goto_link = apply_filters('jobsearch_job_sector_cat_result_link', $cat_goto_link, $term_sector->slug);

                                    ?>
                                    <li>
                                        <a href="<?php echo($cat_goto_link) ?>"><i
                                                    class="<?php echo($term_icon) ?>"<?php echo($term_color != '' ? ' style="color: ' . $term_color . ';"' : '') ?>></i><span><?php echo($term_sector->name) ?></span>
                                        </a>

                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                        <?php } ?>
                    </div>
                </div>


            <?php } else if ($view == 'view16') {
                $rand = rand(99, 100);
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }
                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));
                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }
                $all_fields_class = '';
                if ($location_field == 'show' && (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') && $keyword_field == 'show') {
                    $all_fields_class = 'all-searches-on';
                }
                ?>
                <!-- Banner -->
                <h1 <?php echo($adv_search_title_color) ?>><?php echo $srch_title ?></h1>
                <br>
                <p <?php echo($adv_search_paragraph_color) ?>><?php echo $srch_desc ?></p>
                <br>
                <form class="careerfy-banner-twelve-search <?php echo($all_fields_class) ?> <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?>"
                      method="get"
                      action="<?php echo(get_permalink($result_page)); ?>">

                    <ul class="careerfy-twelve-fields">
                        <?php if ($keyword_field == 'show') { ?>
                            <li>
                                <div class="careerfy-twelve-search-wrapper <?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">

                                    <?php if ($autofill_keyword == 'yes') {
                                        wp_enqueue_script('jobsearch-search-box-sugg');
                                    }
                                    ?>
                                    <i class="careerfy-icon careerfy-search-o"></i>
                                    <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                           name="search_title" data-type="job" type="text">
                                    <span class="sugg-search-loader"></span>
                                </div>
                            </li>
                        <?php }
                        if ($location_field == 'show') { ?>
                            <li>
                                <?php
                                if ($autofill_location == 'yes') {
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
                                    <div class="careerfy-tooltip-radius-wrapper jobsearch_searchloc_div">
                                        <?php if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                            echo get_radius_tooltip();
                                        } ?>
                                        <i class="careerfy-icon careerfy-pin-line"></i>
                                        <span class="loc-loader"></span>
                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                               autocomplete="off"
                                               class="jobsearch_search_location_field"
                                               type="text">
                                        <input type="hidden"
                                               class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                               name="location">
                                        <?php if ($auto_geo_location == 'yes') { ?>
                                            <a href="javascript:void(0);" class="geolction-btn"
                                               onclick="JobsearchGetClientLocation()"><i
                                                        class="careerfy-icon careerfy-location"></i></a>
                                        <?php } ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="careerfy-tooltip-radius-wrapper jobsearch_searchloc_div">
                                        <?php if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                            echo get_radius_tooltip();
                                        } ?>
                                        <i class="careerfy-icon careerfy-pin-line"></i>
                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                               class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                               name="location" type="text">
                                        <?php
                                        if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                            <a href="javascript:void(0);" class="geolction-btn"><i
                                                        class="careerfy-icon careerfy-location"></i></a>
                                        <?php }
                                        if ($auto_geo_location == 'yes') { ?>
                                            <a href="javascript:void(0);" class="geolction-btn"
                                               onclick="JobsearchGetClientLocation()"><i
                                                        class="careerfy-icon careerfy-location"></i></a>
                                        <?php } ?>
                                    </div>

                                <?php } ?>
                            </li>
                        <?php }
                        ?>
                        <?php
                        if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                            <li>
                                <div class="careerfy-select-style">
                                    <select name="sector_cat" class="selectize-select">
                                        <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                        <?php
                                        foreach ($all_sectors as $term_sector) { ?>
                                            <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </li>
                        <?php } ?>
                        <li><input type="submit" value="<?php echo esc_html__('Find Jobs', 'careerfy-frame') ?>"></li>
                    </ul>

                    <div class="clearfix"></div>
                    <?php
                    if (count($job_types) > 0) {
                        foreach ($job_types as $key => $job_types_info) { ?>
                            <div class="careerfy-eighteen-search-radio">
                                <input type="radio" name="job_type"
                                       value="<?php echo esc_html__($job_types_info->slug, 'careerfy-frame') ?>"
                                       id="radio-<?php echo($job_types_info->slug) ?>-<?php echo($rand) ?>"
                                       class="form-radio"
                                       checked="">
                                <label for="radio-<?php echo($job_types_info->slug) ?>-<?php echo($rand) ?>" <?php echo $adv_search_link_color ?> ><?php echo esc_html__($job_types_info->name, 'careerfy-frame') ?></label>
                            </div>
                        <?php }
                    } ?>

                </form>


                <!-- Banner -->
            <?php } else if ($view == 'view15') {
                $rand = rand(99, 100);
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }
                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));
                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }
                $all_fields_class = '';
                if ($location_field == 'show' && (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') && $keyword_field == 'show') {
                    $all_fields_class = 'all-searches-on';
                }
                ?>
                <div class="careerfy-eighteen-banner <?php echo($all_fields_class) ?> <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?>">
                    <span class="careerfy-eighteen-banner-transparent"></span>
                    <h1 <?php echo($adv_search_title_color) ?>><?php echo $srch_title ?></h1>
                    <span <?php echo($adv_search_paragraph_color) ?>><?php echo $srch_desc ?></span>
                    <form method="get" action="<?php echo(get_permalink($result_page)); ?>">
                        <ul class="careerfy-eighteen-fields">
                            <?php if ($keyword_field == 'show') { ?>
                                <li>
                                    <div class="careerfy-eighteen-banner-title <?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                        <?php
                                        if ($autofill_keyword == 'yes') {
                                            wp_enqueue_script('jobsearch-search-box-sugg');
                                        }
                                        ?>
                                        <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                               name="search_title" data-type="job" type="text">
                                        <span class="sugg-search-loader"></span>
                                    </div>
                                </li>
                            <?php }

                            if ($location_field == 'show') { ?>
                                <li>
                                    <?php if ($autofill_location == 'yes') {
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
                                        <div class="careerfy-tooltip-radius-wrapper jobsearch_searchloc_div">
                                            <?php if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                echo get_radius_tooltip();
                                            } ?>

                                            <span class="loc-loader"></span>
                                            <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                   autocomplete="off"
                                                   class="jobsearch_search_location_field"
                                                   type="text">
                                            <input type="hidden"
                                                   class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                   name="location">
                                            <?php if ($auto_geo_location == 'yes') { ?>
                                                <a href="javascript:void(0);" class="geolction-btn"
                                                   onclick="JobsearchGetClientLocation()"><i
                                                            class="careerfy-icon careerfy-location"></i></a>
                                            <?php } ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="careerfy-tooltip-radius-wrapper">
                                            <?php if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                echo get_radius_tooltip();
                                            } ?>

                                            <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                   class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                   name="location" type="text">
                                            <?php
                                            if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                <a href="javascript:void(0);" class="geolction-btn"><i
                                                            class="careerfy-icon careerfy-location"></i></a>
                                            <?php }
                                            if ($auto_geo_location == 'yes') { ?>
                                                <a href="javascript:void(0);" class="geolction-btn"
                                                   onclick="JobsearchGetClientLocation()"><i
                                                            class="careerfy-icon careerfy-location"></i></a>
                                            <?php } ?>
                                        </div>

                                    <?php } ?>
                                </li>
                            <?php } ?>
                            <?php
                            if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                <li>
                                    <div class="careerfy-select-style">
                                        <select name="sector_cat" class="selectize-select">
                                            <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                            <?php
                                            foreach ($all_sectors as $term_sector) { ?>
                                                <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </li>
                            <?php } ?>
                            <li><input type="submit" value="<?php echo esc_html__('Search', 'careerfy-frame') ?>"></li>
                        </ul>
                        <div class="clearfix"></div>
                        <?php
                        if (count($job_types) > 0) {
                            foreach ($job_types as $key => $job_types_info) { ?>
                                <div class="careerfy-eighteen-search-radio">
                                    <input type="radio" name="job_type"
                                           value="<?php echo esc_html__($job_types_info->slug, 'careerfy-frame') ?>"
                                           id="radio-<?php echo($job_types_info->slug) ?>-<?php echo($rand) ?>"
                                           class="form-radio"
                                           checked="">
                                    <label for="radio-<?php echo($job_types_info->slug) ?>-<?php echo($rand) ?>" <?php echo $adv_search_link_color ?> ><?php echo esc_html__($job_types_info->name, 'careerfy-frame') ?></label>
                                </div>
                            <?php }
                        } ?>

                    </form>
                </div>
            <?php } else if ($view == 'view14') {
                $rand = rand(99, 100);
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }
                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));

                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }
                $all_fields_class = '';
                if ($location_field == 'show' && (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') && $keyword_field == 'show') {
                    $all_fields_class = 'all-searches-on';
                }
                ?>
                <div class="careerfy-seventeen-search <?php echo($all_fields_class) ?> <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?>">
                    <form method="get" action="<?php echo(get_permalink($result_page)); ?>">
                        <ul>
                            <li>
                                <ul class="careerfy-seventeen-search-list">
                                    <?php
                                    if ($keyword_field == 'show') {
                                        if ($autofill_keyword == 'yes') {
                                            wp_enqueue_script('jobsearch-search-box-sugg');
                                        } ?>
                                        <li>
                                            <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                                       name="search_title" data-type="job" type="text">
                                                <span class="sugg-search-loader"></span>
                                            </div>
                                        </li>
                                    <?php }

                                    if ($location_field == 'show') { ?>
                                        <li>
                                            <div class="jobsearch_searchloc_div">
                                                <?php if ($autofill_location == 'yes') {
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
                                                    <span class="loc-loader"></span>
                                                    <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                           autocomplete="off"
                                                           class="jobsearch_search_location_field"
                                                           type="text">
                                                    <input type="hidden"
                                                           class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                           name="location">
                                                <?php } else { ?>
                                                    <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                           class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                           name="location" type="text">
                                                <?php }
                                                if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                    echo get_radius_tooltip();
                                                }
                                                if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                <?php }
                                                if ($auto_geo_location == 'yes') { ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"
                                                       onclick="JobsearchGetClientLocation()"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                <?php } ?>

                                            </div>
                                        </li>
                                    <?php } ?>
                                    <?php
                                    if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                        <li>
                                            <div class="careerfy-select-style">
                                                <select name="sector_cat" class="selectize-select">
                                                    <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                                    <?php
                                                    foreach ($all_sectors as $term_sector) { ?>
                                                        <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <li>
                                <?php foreach ($job_types as $job_types_info) { ?>
                                    <div class="careerfy-seventeen-search-radio">
                                        <input type="radio" name="job_type"
                                               value="<?php echo esc_html__($job_types_info->slug, 'careerfy-frame') ?>"
                                               id="radio-<?php echo($job_types_info->slug) ?>-<?php echo($rand) ?>"
                                               class="form-radio" <?php echo $adv_search_link_color ?>>
                                        <label for="radio-<?php echo($job_types_info->slug) ?>-<?php echo($rand) ?>"><?php echo esc_html__($job_types_info->name, 'careerfy-frame') ?></label>
                                    </div>
                                <?php } ?>
                                <input type="submit" value="<?php echo esc_html__('Search', 'careerfy-frame') ?>">
                            </li>
                        </ul>
                    </form>
                </div>
            <?php } else if ($view == 'view13') {
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }

                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                    'number' => 3,
                ));
                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }
                $all_fields_class = '';
                if ($location_field == 'show' && (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') && $keyword_field == 'show') {
                    $all_fields_class = 'all-searches-on';
                }
                ?>

                <div class="careerfy-sixteen-banner <?php echo($all_fields_class) ?> <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?>">
                    <?php if ($srch_title != '') { ?>
                        <h1 <?php echo($adv_search_title_color) ?>><?php echo($srch_title) ?></h1>
                    <?php }
                    if ($srch_desc != '') { ?>
                        <span <?php echo($adv_search_paragraph_color) ?>><?php echo($srch_desc) ?></span>
                    <?php } ?>
                    <form method="get" action="<?php echo(get_permalink($result_page)); ?>">
                        <ul>
                            <?php if ($keyword_field == 'show') { ?>
                                <li>
                                    <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                        <?php
                                        if ($autofill_keyword == 'yes') {
                                            wp_enqueue_script('jobsearch-search-box-sugg');
                                        } ?>
                                        <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                               name="search_title" data-type="job" type="text">
                                        <span class="sugg-search-loader"></span>
                                    </div>
                                </li>
                            <?php } ?>
                            <?php if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                <li>
                                    <div class="careerfy-select-style">
                                        <select name="sector_cat" class="selectize-select">
                                            <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                            <?php
                                            foreach ($all_sectors as $term_sector) { ?>
                                                <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </li>
                            <?php } ?>
                            <?php if ($location_field == 'show') { ?>
                                <li>
                                    <?php if ($autofill_location == 'yes') {
                                        if ($location_map_type == 'mapbox') {
                                            wp_enqueue_script('jobsearch-mapbox');
                                            wp_enqueue_script('jobsearch-mapbox-geocoder');
                                            wp_enqueue_script('mapbox-geocoder-polyfill');
                                            wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                        } else {
                                            wp_enqueue_script('jobsearch-google-map');
                                        }
                                        wp_enqueue_script('jobsearch-location-autocomplete'); ?>
                                        <div class="careerfy-tooltip-radius-wrapper jobsearch_searchloc_div">
                                            <?php if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                echo get_radius_tooltip();
                                            } ?>

                                            <span class="loc-loader"></span>
                                            <input placeholder="<?php esc_html_e('City State or zip', 'careerfy-frame') ?>"
                                                   autocomplete="off"
                                                   class="jobsearch_search_location_field"
                                                   type="text">
                                            <input type="hidden"
                                                   class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                   name="location">
                                            <?php if ($auto_geo_location == 'yes') { ?>
                                                <a href="javascript:void(0);" class="geolction-btn"
                                                   onclick="JobsearchGetClientLocation()"><i
                                                            class="careerfy-icon careerfy-location"></i></a>
                                            <?php } ?>
                                        </div>

                                    <?php } else { ?>
                                        <div class="careerfy-tooltip-radius-wrapper jobsearch_searchloc_div">
                                            <?php if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                echo get_radius_tooltip();
                                            } ?>

                                            <input placeholder="<?php esc_html_e('City State or zip', 'careerfy-frame') ?>"
                                                   class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                   name="location" type="text">
                                            <i class="careerfy-sixteen-banner-search-icon careerfy-icon careerfy-gps"></i>
                                            <?php if ($auto_geo_location == 'yes') { ?>
                                                <a href="javascript:void(0);" class="geolction-btn"
                                                   onclick="JobsearchGetClientLocation()"><i
                                                            class="careerfy-icon careerfy-location"></i></a>
                                            <?php } ?>
                                        </div>

                                    <?php } ?>
                                </li>
                            <?php } ?>
                            <li><input type="submit" value="<?php esc_html_e("Find Jobs", 'careerfy-frame') ?>"></li>
                        </ul>
                    </form>
                    <div class="clearfix"></div>
                    <?php
                    $to_result_page = $result_page;
                    if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                        <div class="careerfy-sixteen-banner-tags">
                            <small><?php echo esc_html__('Popular Keywords', 'careerfy-frame') ?></small>
                            <?php
                            ob_start();
                            foreach ($all_sectors as $term_sector) {
                                $term_fields = get_term_meta($term_sector->term_id, 'careerfy_frame_cat_fields', true);
                                $term_icon = isset($term_fields['icon']) ? $term_fields['icon'] : '';
                                $term_color = isset($term_fields['color']) ? $term_fields['color'] : '';
                                $term_image = isset($term_fields['image']) ? $term_fields['image'] : '';

                                $cat_goto_link = add_query_arg(array('sector_cat' => $term_sector->slug), get_permalink($to_result_page));
                                $cat_goto_link = apply_filters('jobsearch_job_sector_cat_result_link', $cat_goto_link, $term_sector->slug);
                                ?>
                                <a href="<?php echo($cat_goto_link) ?>"><?php echo($term_sector->name) ?></a>
                            <?php }
                            $srchfield_html = ob_get_clean();
                            echo apply_filters('jobsearch_careerfy_advance_search_sh_frmcat', $srchfield_html, $all_sectors);
                            ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } else if ($view == 'view12') {
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }
                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));
                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }
                ?>
                <style>
                    .banner-bg-img-<?php echo $rand_num ?> {
                        background: url("<?php echo $bg_img ?>") no-repeat;
                    }

                </style>
                <div class="careerfy-fifteen-banner <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?>">
                    <div class="careerfy-fifteen-banner-inner banner-bg-img-<?php echo $rand_num ?>">
                        <?php if ($srch_title != '') { ?>
                            <h1<?php echo($adv_search_title_color) ?>><?php echo($srch_title) ?></h1>
                        <?php }
                        if ($srch_desc != '') { ?>
                            <span class="careerfy-fifteen-banner-description" <?php echo($adv_search_paragraph_color) ?>><?php echo($srch_desc) ?></span>
                        <?php } ?>
                        <div class="careerfy-fifteen-banner-tabs">
                            <ul class="careerfy-banner-eleven-tabs-nav">
                                <li class="active"><a data-toggle="tab"
                                                      href="#home"><?php echo esc_html__('Find a Job', 'careerfy-frame') ?></a>
                                </li>
                                <?php
                                ob_start();
                                ?>
                                <li><a data-toggle="tab"
                                       href="#menu1"><?php echo esc_html__('Find a Candidate', 'careerfy-frame') ?></a>
                                </li>
                                <?php
                                $html = ob_get_clean();
                                echo apply_filters('careerfy_adv_srch_sh_view12_findcand_tab', $html);
                                ?>
                            </ul>
                            <div class="tab-content">
                                <div id="home" class="tab-pane fade in active">
                                    <form method="get" action="<?php echo(get_permalink($result_page)); ?>"
                                          class="careerfy-fifteen-banner-search">
                                        <ul>
                                            <?php if ($keyword_field == 'show') { ?>
                                                <li>
                                                    <?php
                                                    if ($autofill_keyword == 'yes') {
                                                        wp_enqueue_script('jobsearch-search-box-sugg');
                                                    }
                                                    ?>
                                                    <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                        <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                                               name="search_title" data-type="job" type="text">
                                                        <span class="sugg-search-loader"></span>
                                                    </div
                                                </li>
                                            <?php }
                                            if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                                <li>
                                                    <div class="">
                                                        <select name="sector_cat" class="selectize-select">
                                                            <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                                            <?php
                                                            foreach ($all_sectors as $term_sector) { ?>
                                                                <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </li>
                                            <?php }
                                            if ($location_field == 'show') { ?>
                                                <li>
                                                    <?php
                                                    if ($top_search_radius == 'yes' && $radius_field == 'show') {

                                                        echo get_radius_tooltip();
                                                    }

                                                    if ($autofill_location == 'yes') {
                                                        if ($location_map_type == 'mapbox') {
                                                            wp_enqueue_script('jobsearch-mapbox');
                                                            wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                            wp_enqueue_script('mapbox-geocoder-polyfill');
                                                            wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                                        } else {
                                                            wp_enqueue_script('jobsearch-google-map');
                                                        }
                                                        wp_enqueue_script('jobsearch-location-autocomplete'); ?>

                                                        <span class="loc-loader"></span>
                                                        <input placeholder="<?php esc_html_e('City State or zip', 'careerfy-frame') ?>"
                                                               autocomplete="off"
                                                               class="jobsearch_search_location_field"
                                                               type="text">
                                                        <input type="hidden"
                                                               class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location">
                                                    <?php } else { ?>
                                                        <input placeholder="<?php esc_html_e('City State or zip', 'careerfy-frame') ?>"
                                                               class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location" type="text">
                                                    <?php }
                                                    if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn"><i
                                                                    class="careerfy-icon careerfy-location"></i></a>
                                                    <?php }
                                                    if ($auto_geo_location == 'yes') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn"
                                                           onclick="JobsearchGetClientLocation()"><i
                                                                    class="careerfy-icon careerfy-location"></i></a>
                                                    <?php } ?>
                                                </li>
                                            <?php } ?>
                                            <li><input type="submit"
                                                       value="<?php esc_html_e("Find Jobs", 'careerfy-frame') ?>">
                                            </li>
                                        </ul>
                                    </form>
                                </div>
                                <div id="menu1" class="tab-pane fade">
                                    <form method="get" action="<?php echo(get_permalink($result_page_3)); ?>"
                                          class="careerfy-fifteen-banner-search">
                                        <ul>
                                            <?php if ($keyword_field == 'show') { ?>
                                                <li>
                                                    <?php if ($autofill_keyword == 'yes') {
                                                        wp_enqueue_script('jobsearch-search-box-sugg');
                                                    } ?>
                                                    <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                        <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                                               name="search_title" data-type="job" type="text">
                                                        <span class="sugg-search-loader"></span>
                                                    </div>
                                                </li>
                                            <?php }

                                            if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                                <li>
                                                    <div class="">
                                                        <select name="sector_cat" class="selectize-select">
                                                            <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                                            <?php
                                                            foreach ($all_sectors as $term_sector) { ?>
                                                                <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </li>
                                            <?php }
                                            if ($location_field == 'show') { ?>
                                                <li>
                                                    <?php if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                        echo get_radius_tooltip();
                                                    }

                                                    if ($autofill_location == 'yes') {
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
                                                        <span class="loc-loader"></span>
                                                        <input placeholder="<?php esc_html_e('City State or zip', 'careerfy-frame') ?>"
                                                               autocomplete="off"
                                                               class="jobsearch_search_location_field"
                                                               type="text">
                                                        <input type="hidden"
                                                               class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location">
                                                    <?php } else { ?>
                                                        <input placeholder="<?php esc_html_e('City State or zip', 'careerfy-frame') ?>"
                                                               class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location" type="text">

                                                    <?php }
                                                    if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn"><i
                                                                    class="careerfy-icon careerfy-location"></i></a>
                                                    <?php }
                                                    if ($auto_geo_location == 'yes') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn"
                                                           onclick="JobsearchGetClientLocation()"><i
                                                                    class="careerfy-icon careerfy-location"></i></a>
                                                    <?php } ?>
                                                </li>
                                            <?php } ?>
                                            <li><input type="submit"
                                                       value="<?php esc_html_e("Find Job", 'careerfy-frame') ?>">
                                            </li>
                                        </ul>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } else if ($view == 'view11') {
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }
                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));
                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }

                $all_fields_class = '';
                if ($location_field == 'show' && (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') && $keyword_field == 'show') {
                    $all_fields_class = 'all-searches-on';
                }

                ?>
                <!-- Banner -->
                <div class="careerfy-fourteen-banner <?php echo($all_fields_class) ?> <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?>">
                    <span class="careerfy-fourteen-banner-transparent"></span>
                    <div class="careerfy-fourteen-caption">
                        <?php if ($srch_title != '') { ?>
                            <h1<?php echo($adv_search_title_color) ?>><?php echo($srch_title) ?></h1>
                        <?php }
                        if ($srch_desc != '') { ?>
                            <p<?php echo($adv_search_paragraph_color) ?>><?php echo($srch_desc) ?></p>
                        <?php } ?>
                        <form method="get" action="<?php echo(get_permalink($result_page)); ?>">
                            <ul class="careerfy-fourteen-fields">
                                <?php if ($keyword_field == 'show') { ?>
                                    <li>
                                        <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                            <?php
                                            if ($autofill_keyword == 'yes') {
                                                wp_enqueue_script('jobsearch-search-box-sugg');
                                            } ?>
                                            <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                                   name="search_title" data-type="job" type="text">
                                            <span class="sugg-search-loader"></span>
                                        </div>
                                    </li>
                                <?php }
                                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                    <li>
                                        <div class="careerfy-select-style">
                                            <select name="sector_cat" class="selectize-select">
                                                <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                                <?php
                                                foreach ($all_sectors as $term_sector) { ?>
                                                    <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </li>
                                <?php }
                                if ($location_field == 'show') { ?>
                                    <li>
                                        <?php
                                        if ($autofill_location == 'yes') {
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
                                            <div class="careerfy-tooltip-radius-wrapper">
                                                <?php if ($top_search_radius == 'yes' && $radius_field == 'show') {

                                                    echo get_radius_tooltip();
                                                } ?>

                                                <span class="loc-loader"></span>
                                                <input placeholder="<?php esc_html_e('City State or zip', 'careerfy-frame') ?>"
                                                       autocomplete="off"
                                                       class="jobsearch_search_location_field"
                                                       type="text">
                                                <input type="hidden"
                                                       class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                       name="location">
                                                <?php
                                                if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                <?php }
                                                if ($auto_geo_location == 'yes') { ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"
                                                       onclick="JobsearchGetClientLocation()"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                <?php } ?>
                                            </div>
                                        <?php } else { ?>

                                            <div class="careerfy-tooltip-radius-wrapper">
                                                <?php if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                    echo get_radius_tooltip();
                                                } ?>
                                                <span class="loc-loader"></span>
                                                <input placeholder="<?php esc_html_e('City State or zip', 'careerfy-frame') ?>"
                                                       class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                       name="location" type="text">

                                                <?php
                                                if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                <?php }
                                                if ($auto_geo_location == 'yes') { ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"
                                                       onclick="JobsearchGetClientLocation()"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                <?php } ?>
                                            </div>

                                        <?php } ?>
                                    </li>
                                <?php } ?>
                                <li><input type="submit"
                                           value="<?php echo esc_html_e('Find Jobs', 'careerfy-frame') ?>">
                                </li>
                            </ul>
                            <a href="<?php echo get_permalink($result_page) ?>"
                               class="careerfy-fourteen-caption-btn"><?php esc_html_e('+ Advance Search', 'careerfy-frame') ?></a>

                        </form>

                        <?php

                        if (count($adv_banner_images) > 1) { ?>
                            <ul>
                                <?php
                                $_exf_counter = 0;
                                foreach ($adv_banner_images as $adv_banner_image) { ?>
                                    <li><a href="<?php echo $adv_banner_image['img_link'] ?>"><img
                                                    src="<?php echo $adv_banner_image['banner_img']['url'] ?>"
                                                    alt=""></a>
                                    </li>
                                    <?php
                                    $_exf_counter++;
                                }
                                ?>
                            </ul>
                        <?php } ?>

                    </div>
                </div>
                <!-- Banner -->
            <?php } else if ($view == 'view10') {
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }
                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));
                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }
                ?>
                <!-- Banner -->
                <div class="careerfy-thirteen-banner">
                    <div class="careerfy-thirteen-banner-search <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?>">
                        <form method="get" action="<?php echo(get_permalink($result_page)); ?>">
                            <ul>
                                <?php
                                if ($keyword_field == 'show') {
                                    if ($autofill_keyword == 'yes') {
                                        wp_enqueue_script('jobsearch-search-box-sugg');
                                    } ?>
                                    <li><i class="careerfy-icon careerfy-search-o"></i>
                                        <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                            <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                                   name="search_title" data-type="job" type="text">
                                            <span class="sugg-search-loader"></span>
                                        </div>
                                    </li>
                                <?php }

                                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                    <li>
                                        <div class="careerfy-select-style">
                                            <select name="sector_cat" class="selectize-select">
                                                <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                                <?php
                                                foreach ($all_sectors as $term_sector) { ?>
                                                    <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </li>
                                <?php } ?>
                                <?php if ($location_field == 'show') { ?>
                                    <li>
                                        <?php
                                        if ($autofill_location == 'yes') {

                                            if ($autofill_location == 'yes') {
                                                if ($location_map_type == 'mapbox') {
                                                    wp_enqueue_script('jobsearch-mapbox');
                                                    wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                    wp_enqueue_script('mapbox-geocoder-polyfill');
                                                    wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                                } else {
                                                    wp_enqueue_script('jobsearch-google-map');
                                                }
                                                wp_enqueue_script('jobsearch-location-autocomplete');
                                            } ?>


                                            <i class="careerfy-icon careerfy-pin-line"></i>
                                            <span class="loc-loader"></span>
                                            <input placeholder="<?php esc_html_e('City State or zip', 'careerfy-frame') ?>"
                                                   autocomplete="off"
                                                   class="jobsearch_search_location_field"
                                                   type="text">
                                            <input type="hidden"
                                                   class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                   name="location">
                                        <?php } else { ?>
                                            <i class="careerfy-icon careerfy-pin-line"></i>
                                            <input placeholder="<?php esc_html_e('City State or zip', 'careerfy-frame') ?>"
                                                   class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                   name="location" type="text">

                                        <?php } ?>

                                    </li>
                                <?php } ?>
                                <?php if ($top_search_radius == 'yes' && $radius_field == 'show') { ?>
                                    <li>
                                        <input name="loc_radius"
                                               placeholder="<?php esc_html_e('Radius', 'careerfy-frame') ?>" value=""
                                               type="text">
                                        <i class="careerfy-icon careerfy-gps-o"></i>
                                    </li>
                                <?php } ?>
                                <li><input type="submit" value="<?php esc_html_e("Search Job", 'careerfy-frame') ?>">
                                    <a href="<?php echo(get_permalink($result_page)); ?>"><?php esc_html_e("+ Advance Search", 'careerfy-frame') ?></a>
                                </li>
                            </ul>
                        </form>
                    </div>
                </div>
                <!-- Banner -->
            <?php } else if ($view == 'view9') {
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }
                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));
                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }
                ?>
                <!-- Banner -->
                <div class="careerfy-banner-eleven">
                    <div class="careerfy-banner-eleven-tabs">
                        <?php
                        if ($srch_title != '') {
                            ?>
                            <h1<?php echo($adv_search_title_color) ?>><?php echo($srch_title) ?></h1>
                            <?php
                        }
                        if ($srch_desc != '') {
                            ?>
                            <p<?php echo($adv_search_paragraph_color) ?>><?php echo($srch_desc) ?></p>
                            <?php
                        }
                        ?>
                        <ul class="careerfy-banner-eleven-tabs-nav">
                            <li class="active"><a data-toggle="tab"
                                                  href="#home"><?php esc_html_e("Jobs", 'careerfy-frame') ?></a>
                            </li>
                            <li><a data-toggle="tab"
                                   href="#menu1"><?php esc_html_e("Employers", 'careerfy-frame') ?></a></li>
                            <li><a data-toggle="tab"
                                   href="#menu2"><?php esc_html_e("Candidates", 'careerfy-frame') ?></a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="home" class="tab-pane fade in active">
                                <div class="careerfy-banner-eleven-search <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?> <?php echo($design_css_class) ?> ">
                                    <form method="get"
                                          action="<?php echo(get_permalink($result_page)); ?>">
                                        <ul>
                                            <?php
                                            if ($keyword_field == 'show') {
                                                if ($autofill_keyword == 'yes') {
                                                    wp_enqueue_script('jobsearch-search-box-sugg');
                                                }
                                                ?>
                                                <li><i class="careerfy-icon careerfy-search-o"></i>
                                                    <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                        <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                                               name="search_title" data-type="job" type="text">
                                                        <span class="sugg-search-loader"></span>
                                                    </div>
                                                </li>
                                            <?php }

                                            if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                                <li>

                                                    <div class="">
                                                        <select name="sector_cat" class="selectize-select">
                                                            <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                                            <?php foreach ($all_sectors as $term_sector) { ?>
                                                                <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </li>
                                                <?php
                                            }
                                            if ($location_field == 'show') {
                                                if ($autofill_location == 'yes') {
                                                    if ($location_map_type == 'mapbox') {
                                                        wp_enqueue_script('jobsearch-mapbox');
                                                        wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                        wp_enqueue_script('mapbox-geocoder-polyfill');
                                                        wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                                    } else {
                                                        wp_enqueue_script('jobsearch-google-map');
                                                    }
                                                    wp_enqueue_script('jobsearch-location-autocomplete');
                                                }
                                                ?>
                                                <li>
                                                    <?php
                                                    if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                        echo get_radius_tooltip();
                                                    }
                                                    if ($autofill_location == 'yes') { ?>

                                                        <span class="loc-loader"></span>
                                                        <i class="careerfy-icon careerfy-pin-line"></i>
                                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                               autocomplete="off"
                                                               class="jobsearch_search_location_field"
                                                               type="text">
                                                        <input type="hidden"
                                                               class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location">
                                                    <?php } else { ?>
                                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                               class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location" type="text">
                                                        <i class="careerfy-icon careerfy-location"></i>
                                                        <?php
                                                    }
                                                    //

                                                    if ($auto_geo_location == 'yes') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn"
                                                           onclick="JobsearchGetClientLocation()"><i
                                                                    class="careerfy-icon careerfy-location"></i></a>
                                                    <?php } ?>
                                                </li>
                                            <?php } ?>
                                            <li><input type="submit"
                                                       value="<?php esc_html_e("Find Jobs", 'careerfy-frame') ?>">
                                            </li>
                                        </ul>
                                    </form>
                                </div>
                                <?php if ($txt_below_forms_1 != '') { ?>
                                    <div class="careerfy-fileupload-banner">
                                        <span><i class="careerfy-icon careerfy-upload"></i><?php echo $txt_below_forms_1 ?> </span>
                                        <input class="careerfy-upload">
                                    </div>
                                <?php } ?>
                            </div>
                            <div id="menu1" class="tab-pane fade">
                                <div class="careerfy-banner-eleven-search">
                                    <form method="get"
                                          action="<?php echo(get_permalink($result_page_2)); ?>">
                                        <ul>
                                            <?php
                                            if ($keyword_field == 'show') {
                                                if ($autofill_keyword == 'yes') {
                                                    wp_enqueue_script('jobsearch-search-box-sugg');
                                                }
                                                ?>
                                                <li><i class="careerfy-icon careerfy-search-o"></i>
                                                    <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                        <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                                               name="search_title" data-type="job" type="text">
                                                        <span class="sugg-search-loader"></span>
                                                    </div>
                                                </li>
                                            <?php }
                                            $all_sectors = get_terms(array(
                                                'taxonomy' => 'sector',
                                                'hide_empty' => false,
                                            ));

                                            if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                                <li>
                                                    <div>
                                                        <select name="sector_cat" class="selectize-select">
                                                            <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                                            <?php foreach ($all_sectors as $term_sector) { ?>
                                                                <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                            <?php if ($location_field == 'show') { ?>
                                                <li>
                                                    <?php
                                                    if ($autofill_location == 'yes') {
                                                        if ($autofill_location == 'yes') {
                                                            if ($location_map_type == 'mapbox') {
                                                                wp_enqueue_script('jobsearch-mapbox');
                                                                wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                                wp_enqueue_script('mapbox-geocoder-polyfill');
                                                                wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                                            } else {
                                                                wp_enqueue_script('jobsearch-google-map');
                                                            }
                                                            wp_enqueue_script('jobsearch-location-autocomplete');
                                                        }
                                                        if ($top_search_radius == 'yes') {
                                                            echo get_radius_tooltip();
                                                        } ?>
                                                        <span class="loc-loader"></span>
                                                        <i class="careerfy-icon careerfy-pin-line"></i>
                                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                               autocomplete="off"
                                                               class="jobsearch_search_location_field"
                                                               type="text">
                                                        <input type="hidden"
                                                               class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location">
                                                    <?php } else { ?>
                                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                               class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location" type="text">
                                                        <i class="careerfy-icon careerfy-location"></i>
                                                        <?php
                                                    }
                                                    //

                                                    if ($auto_geo_location == 'yes') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn"
                                                           onclick="JobsearchGetClientLocation()"><i
                                                                    class="careerfy-icon careerfy-location"></i></a>
                                                    <?php } ?>
                                                </li>
                                            <?php } ?>
                                            <li><input type="submit"
                                                       value="<?php esc_html_e("Find Jobs", 'careerfy-frame') ?>">
                                            </li>
                                        </ul>
                                    </form>
                                </div>
                                <?php if ($txt_below_forms_2 != '') { ?>
                                    <div class="careerfy-fileupload-banner">
                                        <span><i class="careerfy-icon careerfy-upload"></i><?php echo $txt_below_forms_2 ?> </span>
                                        <input class="careerfy-upload">
                                    </div>
                                <?php } ?>
                            </div>
                            <div id="menu2" class="tab-pane fade">
                                <div class="careerfy-banner-eleven-search">
                                    <form method="get"
                                          action="<?php echo(get_permalink($result_page_3)); ?>">
                                        <ul>
                                            <?php
                                            if ($keyword_field == 'show') {
                                                if ($autofill_keyword == 'yes') {
                                                    wp_enqueue_script('jobsearch-search-box-sugg');
                                                }
                                                ?>
                                                <li>
                                                    <i class="careerfy-icon careerfy-search-o"></i>
                                                    <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                        <input placeholder="<?php esc_html_e('Keywords or Title', 'careerfy-frame') ?>"
                                                               name="search_title" data-type="job" type="text">
                                                        <span class="sugg-search-loader"></span>
                                                    </div>
                                                </li>
                                            <?php }
                                            $all_sectors = get_terms(array(
                                                'taxonomy' => 'sector',
                                                'hide_empty' => false,
                                            ));

                                            if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                                <li>
                                                    <div class="careerfy-select-style">
                                                        <select name="sector_cat" class="selectize-select">
                                                            <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                                            <?php foreach ($all_sectors as $term_sector) { ?>
                                                                <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                            <?php if ($location_field == 'show') {
                                                if ($autofill_location == 'yes') {
                                                    if ($location_map_type == 'mapbox') {
                                                        wp_enqueue_script('jobsearch-mapbox');
                                                        wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                        wp_enqueue_script('mapbox-geocoder-polyfill');
                                                        wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                                    } else {
                                                        wp_enqueue_script('jobsearch-google-map');
                                                    }
                                                    wp_enqueue_script('jobsearch-location-autocomplete');
                                                }
                                                ?>
                                                <li>
                                                    <?php
                                                    if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                        echo get_radius_tooltip();
                                                    }
                                                    if ($autofill_location == 'yes') { ?>

                                                        <span class="loc-loader"></span>
                                                        <i class="careerfy-icon careerfy-pin-line"></i>
                                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                               autocomplete="off"
                                                               class="jobsearch_search_location_field"
                                                               type="text">
                                                        <input type="hidden"
                                                               class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location">
                                                    <?php } else { ?>
                                                        <input placeholder="<?php esc_html_e('Location', 'careerfy-frame') ?>"
                                                               class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                               name="location" type="text">
                                                        <?php
                                                    }
                                                    //
                                                    if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn"><i
                                                                    class="careerfy-icon careerfy-location"></i></a>
                                                    <?php }
                                                    if ($auto_geo_location == 'yes') { ?>
                                                        <a href="javascript:void(0);" class="geolction-btn"
                                                           onclick="JobsearchGetClientLocation()"><i
                                                                    class="careerfy-icon careerfy-location"></i></a>
                                                    <?php } ?>
                                                </li>
                                            <?php } ?>
                                            <li><input type="submit"
                                                       value="<?php esc_html_e("Find Jobs", 'careerfy-frame') ?>">
                                            </li>
                                        </ul>
                                    </form>
                                </div>
                                <?php if ($txt_below_forms_3 != '') { ?>
                                    <div class="careerfy-fileupload-banner">
                                        <span><i class="careerfy-icon careerfy-upload"></i><?php echo $txt_below_forms_3 ?> </span>
                                        <input class="careerfy-upload">
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Banner -->
            <?php } else if ($view == 'view8') {
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }
                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));
                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }
                ?>
                <!-- Main Section -->
                <div class="careerfy-main-section careerfy-search-ten-full">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="careerfy-search-ten">
                                <?php
                                if ($keyword_field == 'show' || $location_field == 'show' || $category_field == 'show' && $sectors_enable_switch == 'on') {
                                    ?>
                                    <form class="careerfy-banner-search-ten <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?> <?php echo($design_css_class) ?>"
                                          method="get"
                                          action="<?php echo(get_permalink($result_page)); ?>">
                                        <ul class="careerfy-search-ten-grid">
                                            <?php
                                            if ($keyword_field == 'show') {
                                                if ($autofill_keyword == 'yes') {
                                                    wp_enqueue_script('jobsearch-search-box-sugg');
                                                }
                                                ?>
                                                <li>
                                                    <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                        <input placeholder="<?php esc_html_e('Job Title, Keywords, or Phrase', 'careerfy-frame') ?>"
                                                               name="search_title" data-type="job" type="text">

                                                        <span class="sugg-search-loader"></span>
                                                    </div>
                                                </li>
                                                <?php
                                            }
                                            if ($location_field == 'show') {
                                                if ($autofill_location == 'yes') {
                                                    if ($location_map_type == 'mapbox') {
                                                        wp_enqueue_script('jobsearch-mapbox');
                                                        wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                        wp_enqueue_script('mapbox-geocoder-polyfill');
                                                        wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                                    } else {
                                                        wp_enqueue_script('jobsearch-google-map');
                                                    }
                                                    wp_enqueue_script('jobsearch-location-autocomplete');
                                                }
                                                ?>
                                                <li>
                                                    <div class="jobsearch_searchloc_div">
                                                        <?php
                                                        if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                            echo get_radius_tooltip();
                                                        }
                                                        if ($autofill_location == 'yes') {
                                                            ?>
                                                            <span class="loc-loader"></span>
                                                            <input placeholder="<?php esc_html_e('City, State or ZIP', 'careerfy-frame') ?>"
                                                                   autocomplete="off"
                                                                   class="jobsearch_search_location_field"
                                                                   type="text">
                                                            <input type="hidden"
                                                                   class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                                   name="location">
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <input placeholder="<?php esc_html_e('City, State or ZIP', 'careerfy-frame') ?>"
                                                                   class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                                   name="location" type="text">
                                                            <i class="careerfy-icon careerfy-location"></i>
                                                            <?php
                                                        }
                                                        //
                                                        if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                            <a href="javascript:void(0);" class="geolction-btn"><i
                                                                        class="careerfy-icon careerfy-location"></i></a>
                                                        <?php }
                                                        if ($auto_geo_location == 'yes') {
                                                            ?>
                                                            <a href="javascript:void(0);" class="geolction-btn"
                                                               onclick="JobsearchGetClientLocation()"><i
                                                                        class="careerfy-icon careerfy-location"></i></a>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </li>
                                                <?php
                                            }


                                            if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                                                ?>
                                                <li>
                                                    <div>
                                                        <select name="sector_cat" class="selectize-select">
                                                            <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                                            <?php foreach ($all_sectors as $term_sector) { ?>
                                                                <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </li>
                                                <?php
                                            } ?>

                                            <li>
                                                <label>
                                                    <i class="careerfy-icon careerfy-search-o"></i>
                                                    <input type="submit"
                                                           value="<?php esc_html_e("Search Job", 'careerfy-frame') ?>">
                                                </label>
                                            </li>
                                        </ul>
                                        <div class="clearfix"></div>
                                        <?php
                                        $top_sectors = $wpdb->get_col($wpdb->prepare("SELECT terms.term_id FROM $wpdb->terms AS terms"
                                            . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                                            . " LEFT JOIN $wpdb->termmeta AS term_meta ON(terms.term_id = term_meta.term_id) "
                                            . " WHERE term_tax.taxonomy=%s AND term_meta.meta_key=%s"
                                            . " ORDER BY cast(term_meta.meta_value as unsigned) DESC LIMIT 4", 'sector', 'active_jobs_count'));

                                        if (!empty($top_sectors) && !is_wp_error($top_sectors)) {
                                            ?>
                                            <ul class="careerfy-search-ten-list">
                                                <li<?php echo($adv_search_paragraph_color) ?>><?php esc_html_e('Top Sectors :', 'careerfy-frame') ?></li>
                                                <?php
                                                foreach ($top_sectors as $term_id) {
                                                    $term_sector = get_term_by('id', $term_id, 'sector');
                                                    ?>
                                                    <li>
                                                        <a href="<?php echo add_query_arg(array('sector' => $term_sector->slug), get_permalink($result_page)); ?>"<?php echo($adv_search_link_color) ?>><?php echo($term_sector->name) ?></a>
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        <?php } ?>
                                        <a href="<?php echo(get_permalink($result_page)); ?>"
                                           class="careerfy-search-ten-list-btn"><?php echo esc_html__('Advance Search', 'careerfy-frame') ?></a>
                                    </form>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Main Section -->

            <?php } else if ($view == 'view7') {
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }
                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));
                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }
                $all_fields_class = '';
                if ($location_field == 'show' && (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') && $keyword_field == 'show') {
                    $all_fields_class = 'all-searches-on';
                }
                ?>
                <div class="careerfy-search-eight-wrap  <?php echo($design_css_class) ?>">
                    <div class="careerfy-adv-wrap">
                        <?php
                        if ($srch_title != '') {
                            ?>
                            <h2<?php echo($adv_search_title_color) ?>><?php echo($srch_title) ?></h2>
                            <?php
                        }
                        if ($srch_desc != '') {
                            ?>
                            <p<?php echo($adv_search_paragraph_color) ?>><?php echo($srch_desc) ?></p>
                            <?php
                        }
                        ?>
                        <div class="careerfy-banner-btn">
                            <?php
                            if ($btn1_txt != '') {
                                ?>
                                <a href="<?php echo($btn1_url) ?>"
                                   class="careerfy-bgcolorhover"<?php echo($button_style) ?>><?php echo($btn_1_icon != '' ? '<i class="' . $btn_1_icon['value'] . '"></i>' : '') ?><?php echo($btn1_txt) ?></a>
                                <?php
                            }
                            if ($btn2_txt != '') {
                                ?>
                                <a href="<?php echo($btn2_url) ?>"
                                   class="careerfy-bgcolorhover"<?php echo($button_style) ?><?php echo($adv_search_btn_bg_color) ?>><?php echo($btn_2_icon != '' ? '<i class="' . $btn_2_icon['value'] . '"></i>' : '') ?><?php echo($btn2_txt) ?></a>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                        if ($keyword_field == 'show' || $location_field == 'show' || $category_field == 'show' && $sectors_enable_switch == 'on') {
                            ?>

                            <form class="careerfy-banner-search-eight <?php echo($all_fields_class) ?> <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?> <?php echo($design_css_class) ?>"
                                  method="get"
                                  action="<?php echo(get_permalink($result_page)); ?>">
                                <ul>
                                    <?php
                                    if ($keyword_field == 'show') {
                                        if ($autofill_keyword == 'yes') {
                                            wp_enqueue_script('jobsearch-search-box-sugg');
                                        } ?>
                                        <li>
                                            <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                <input placeholder="<?php esc_html_e('Job Title, Keywords, or Phrase', 'careerfy-frame') ?>"
                                                       name="search_title" data-type="job" type="text">
                                                <span class="sugg-search-loader"></span>
                                            </div>
                                        </li>
                                    <?php }
                                    if ($location_field == 'show') {
                                        if ($autofill_location == 'yes') {
                                            if ($location_map_type == 'mapbox') {
                                                wp_enqueue_script('jobsearch-mapbox');
                                                wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                wp_enqueue_script('mapbox-geocoder-polyfill');
                                                wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                            } else {
                                                wp_enqueue_script('jobsearch-google-map');
                                            }
                                            wp_enqueue_script('jobsearch-location-autocomplete');
                                        }
                                        ?>
                                        <li>
                                            <div class="jobsearch_searchloc_div">
                                                <?php
                                                if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                    echo get_radius_tooltip();
                                                }
                                                if ($autofill_location == 'yes') {
                                                    ?>
                                                    <span class="loc-loader"></span>
                                                    <input placeholder="<?php esc_html_e('City, State or ZIP', 'careerfy-frame') ?>"
                                                           autocomplete="off"
                                                           class="jobsearch_search_location_field"
                                                           type="text">
                                                    <input type="hidden"
                                                           class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                           name="location">
                                                <?php } else { ?>
                                                    <input placeholder="<?php esc_html_e('City, State or ZIP', 'careerfy-frame') ?>"
                                                           class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                           name="location" type="text">
                                                    <?php
                                                }
                                                //
                                                if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                <?php }
                                                if ($auto_geo_location == 'yes') {
                                                    ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"
                                                       onclick="JobsearchGetClientLocation()"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </li>
                                    <?php }
                                    if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                                        <li>
                                            <div class="careerfy-select-style">
                                                <select name="sector_cat" class="selectize-select">
                                                    <option value=""><?php esc_html_e('Categories', 'careerfy-frame') ?></option>
                                                    <?php foreach ($all_sectors as $term_sector) { ?>
                                                        <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </li>
                                    <?php } ?>


                                    <li><input type="submit" value="<?php esc_html_e("Let's Go", 'careerfy-frame') ?>">
                                    </li>
                                </ul>
                            </form>
                        <?php } ?>
                    </div>
                </div>
                <?php
            } elseif ($view == 'view6') {
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }
                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));
                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }
                ?>
                <div class="careerfy-search-seven-wrap <?php echo($design_css_class) ?>">

                    <div class="careerfy-adv-wrap">
                        <?php
                        if ($srch_title != '') { ?>
                            <h2<?php echo($adv_search_title_color) ?>><?php echo($srch_title) ?></h2>
                            <?php
                        }
                        if ($srch_desc != '') { ?>
                            <p<?php echo($adv_search_paragraph_color) ?>><?php echo($srch_desc) ?></p>
                        <?php } ?>
                        <?php
                        if ($keyword_field == 'show' || $location_field == 'show' || $category_field == 'show' && $sectors_enable_switch == 'on') { ?>

                            <form class="careerfy-banner-search-seven <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?> <?php echo($design_css_class) ?>" <?php echo $transparent_bg_color; ?>
                                  method="get"
                                  action="<?php echo(get_permalink($result_page)); ?>">
                                <ul>
                                    <?php
                                    if ($keyword_field == 'show') {
                                        if ($autofill_keyword == 'yes') {
                                            wp_enqueue_script('jobsearch-search-box-sugg');
                                        }
                                        ?>
                                        <li>
                                            <i class="careerfy-icon careerfy-search-o"></i>
                                            <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                <input placeholder="<?php esc_html_e('Job Title, Keywords, or Phrase', 'careerfy-frame') ?>"
                                                       name="search_title" data-type="job" type="text">
                                                <span class="sugg-search-loader"></span>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    if ($location_field == 'show') {
                                        if ($autofill_location == 'yes') {
                                            if ($location_map_type == 'mapbox') {
                                                wp_enqueue_script('jobsearch-mapbox');
                                                wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                wp_enqueue_script('mapbox-geocoder-polyfill');
                                                wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                            } else {
                                                wp_enqueue_script('jobsearch-google-map');
                                            }
                                            wp_enqueue_script('jobsearch-location-autocomplete');
                                        }
                                        ?>
                                        <li>
                                            <div class="jobsearch_searchloc_div">
                                                <?php
                                                if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                    echo get_radius_tooltip();
                                                }
                                                if ($autofill_location == 'yes') { ?>
                                                    <i class="careerfy-icon careerfy-pin-line"></i>
                                                    <span class="loc-loader"></span>
                                                    <input placeholder="<?php esc_html_e('City, State or ZIP', 'careerfy-frame') ?>"
                                                           autocomplete="off" class="jobsearch_search_location_field"
                                                           type="text">
                                                    <input type="hidden"
                                                           class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                           name="location">
                                                <?php } else { ?>
                                                    <i class="careerfy-icon careerfy-pin-line"></i>
                                                    <input placeholder="<?php esc_html_e('City, State or ZIP', 'careerfy-frame') ?>"
                                                           class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                           name="location" type="text">
                                                    <?php
                                                }
                                                //
                                                if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                <?php }
                                                if ($auto_geo_location == 'yes') {
                                                    ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"
                                                       onclick="JobsearchGetClientLocation()"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </li>
                                        <?php
                                    }


                                    if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                                        ?>
                                        <li>
                                            <div class="">
                                                <select name="sector_cat" class="selectize-select">
                                                    <option value=""><?php esc_html_e('Select Sector', 'careerfy-frame') ?></option>
                                                    <?php
                                                    foreach ($all_sectors as $term_sector) {
                                                        ?>
                                                        <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                    <li><i class="careerfy-icon careerfy-search-o"></i><input type="submit" value="">
                                    </li>
                                </ul>
                            </form>
                            <?php
                        }
                        ?>

                    </div>
                </div>
                <?php
            } elseif ($view == 'view5') {
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }
                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));
                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }
                ?>
                <div class="careerfy-search-six-wrap <?php echo($design_css_class) ?>">

                    <div class="careerfy-adv-wrap">
                        <?php
                        if ($keyword_field == 'show' || $location_field == 'show' || $category_field == 'show' && $sectors_enable_switch == 'on') {
                            ?>
                            <form class="careerfy-banner-search-six <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?> <?php echo($design_css_class) ?>" <?php echo $transparent_bg_color; ?>
                                  method="get"
                                  action="<?php echo(get_permalink($result_page)); ?>">
                                <?php
                                if ($srch_title != '') {
                                    ?>
                                    <h2<?php echo($adv_search_title_color) ?>><?php echo($srch_title) ?></h2>
                                    <?php
                                }
                                if ($srch_desc != '') {
                                    ?>
                                    <p<?php echo($adv_search_paragraph_color) ?>><?php echo($srch_desc) ?></p>
                                    <?php
                                }
                                ?>
                                <ul>
                                    <?php
                                    if ($keyword_field == 'show') {
                                        if ($autofill_keyword == 'yes') {
                                            wp_enqueue_script('jobsearch-search-box-sugg');
                                        }
                                        ?>
                                        <li>
                                            <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                <input placeholder="<?php esc_html_e('Job Title, Keywords, or Phrase', 'careerfy-frame') ?>"
                                                       name="search_title" data-type="job" type="text">
                                                <span class="sugg-search-loader"></span>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    if ($location_field == 'show') {
                                        if ($autofill_location == 'yes') {
                                            if ($location_map_type == 'mapbox') {
                                                wp_enqueue_script('jobsearch-mapbox');
                                                wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                wp_enqueue_script('mapbox-geocoder-polyfill');
                                                wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                            } else {
                                                wp_enqueue_script('jobsearch-google-map');
                                            }
                                            wp_enqueue_script('jobsearch-location-autocomplete');
                                        }
                                        ?>
                                        <li>
                                            <div class="jobsearch_searchloc_div">
                                                <?php
                                                if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                    echo get_radius_tooltip();
                                                }
                                                if ($autofill_location == 'yes') {
                                                    ?>
                                                    <span class="loc-loader"></span>
                                                    <input placeholder="<?php esc_html_e('City, State or ZIP', 'careerfy-frame') ?>"
                                                           autocomplete="off" class="jobsearch_search_location_field"
                                                           type="text">
                                                    <input type="hidden"
                                                           class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                           name="location">
                                                    <?php
                                                } else {
                                                    ?>
                                                    <input placeholder="<?php esc_html_e('City, State or ZIP', 'careerfy-frame') ?>"
                                                           class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                           name="location" type="text">
                                                    <?php
                                                }
                                                //
                                                if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                <?php }
                                                if ($auto_geo_location == 'yes') {
                                                    ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"
                                                       onclick="JobsearchGetClientLocation()"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </li>
                                        <?php
                                    }


                                    if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                                        ?>
                                        <li>
                                            <div class="">
                                                <select name="sector_cat" class="selectize-select">
                                                    <option value=""><?php esc_html_e('Select Sector', 'careerfy-frame') ?></option>
                                                    <?php
                                                    foreach ($all_sectors as $term_sector) {
                                                        ?>
                                                        <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                    <li><i class="careerfy-icon careerfy-search-o"></i><input type="submit"
                                                                                              value="<?php esc_html_e('Search Jobs', 'careerfy-frame') ?>">
                                    </li>
                                </ul>
                            </form>
                            <?php
                        }
                        ?>

                    </div>


                </div>
                <?php
            } elseif ($view == 'view4') {
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }
                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));
                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }
                ?>
                <div class="careerfy-search-four-wrap <?php echo($design_css_class) ?>">

                    <div class="careerfy-adv-wrap">
                        <?php
                        if ($keyword_field == 'show' || $location_field == 'show' || $category_field == 'show' && $sectors_enable_switch == 'on') {
                            ?>
                            <form class="careerfy-banner-search-four <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?> <?php echo($design_css_class) ?>"
                                  method="get"
                                  action="<?php echo(get_permalink($result_page)); ?>">
                                <?php
                                if ($srch_title != '') {
                                    ?>
                                    <h2<?php echo($adv_search_title_color) ?>><?php echo($srch_title) ?></h2>
                                    <?php
                                }
                                if ($srch_desc != '') {
                                    ?>
                                    <p<?php echo($adv_search_paragraph_color) ?>><?php echo($srch_desc) ?></p>
                                    <?php
                                }
                                ?>
                                <ul>
                                    <?php
                                    if ($keyword_field == 'show') {
                                        if ($autofill_keyword == 'yes') {
                                            wp_enqueue_script('jobsearch-search-box-sugg');
                                        }
                                        ?>
                                        <li>
                                            <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                <input placeholder="<?php esc_html_e('Job Title, Keywords, or Phrase', 'careerfy-frame') ?>"
                                                       name="search_title" data-type="job" type="text">
                                                <span class="sugg-search-loader"></span>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    if ($location_field == 'show') {
                                        if ($autofill_location == 'yes') {
                                            if ($location_map_type == 'mapbox') {
                                                wp_enqueue_script('jobsearch-mapbox');
                                                wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                wp_enqueue_script('mapbox-geocoder-polyfill');
                                                wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                            } else {
                                                wp_enqueue_script('jobsearch-google-map');
                                            }
                                            wp_enqueue_script('jobsearch-location-autocomplete');
                                        }
                                        ?>
                                        <li>
                                            <div class="jobsearch_searchloc_div">
                                                <?php if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                    echo get_radius_tooltip();
                                                }
                                                if ($autofill_location == 'yes') {
                                                    ?>
                                                    <span class="loc-loader"></span>
                                                    <input placeholder="<?php esc_html_e('City, State or ZIP', 'careerfy-frame') ?>"
                                                           autocomplete="off" class="jobsearch_search_location_field"
                                                           type="text">
                                                    <input type="hidden"
                                                           class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                           name="location">
                                                    <?php
                                                } else {
                                                    ?>
                                                    <input placeholder="<?php esc_html_e('City, State or ZIP', 'careerfy-frame') ?>"
                                                           class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                           name="location" type="text">
                                                    <?php
                                                }
                                                //
                                                if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                <?php }
                                                if ($auto_geo_location == 'yes') {
                                                    ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"
                                                       onclick="JobsearchGetClientLocation()"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </li>
                                        <?php
                                    }


                                    if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                                        ?>
                                        <li>
                                            <div class="">
                                                <select name="sector_cat" class="selectize-select">
                                                    <option value=""><?php esc_html_e('Select Sector', 'careerfy-frame') ?></option>
                                                    <?php
                                                    foreach ($all_sectors as $term_sector) {
                                                        ?>
                                                        <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                    <li><input type="submit"
                                               value="<?php esc_html_e('Search Jobs', 'careerfy-frame') ?>">
                                    </li>
                                </ul>
                            </form>
                            <?php
                        }
                        $top_sectors = $wpdb->get_col($wpdb->prepare("SELECT terms.term_id FROM $wpdb->terms AS terms"
                            . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                            . " LEFT JOIN $wpdb->termmeta AS term_meta ON(terms.term_id = term_meta.term_id) "
                            . " WHERE term_tax.taxonomy=%s AND term_meta.meta_key=%s"
                            . " ORDER BY cast(term_meta.meta_value as unsigned) DESC LIMIT 4", 'sector', 'active_jobs_count'));

                        if (!empty($top_sectors) && !is_wp_error($top_sectors)) {
                            ?>
                            <ul class="careerfy-search-categories">
                                <li<?php echo($adv_search_paragraph_color) ?>><?php esc_html_e('Top Sectors :', 'careerfy-frame') ?></li>
                                <?php
                                foreach ($top_sectors as $term_id) {
                                    $term_sector = get_term_by('id', $term_id, 'sector');
                                    ?>
                                    <li>
                                        <a href="<?php echo add_query_arg(array('sector' => $term_sector->slug), get_permalink($result_page)); ?>"<?php echo($adv_search_link_color) ?>><?php echo($term_sector->name) ?></a>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                            <?php
                        }
                        ?>

                    </div>


                </div>
                <?php
            } else if ($view == 'view3') {
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }
                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));
                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }
                ?>
                <div class="careerfy-banner-three careerfy-typo-wrap <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?> <?php echo($design_css_class) ?>">
                    <div class="careerfy-bannerthree-caption">
                        <?php
                        if ($srch_title != '') {
                            ?>
                            <h1<?php echo($adv_search_title_color) ?>><?php echo($srch_title) ?></h1>
                            <?php
                        }
                        if ($srch_desc != '') {
                            ?>
                            <p<?php echo($adv_search_paragraph_color) ?>><?php echo($srch_desc) ?></p>
                            <?php
                        }
                        ?>
                        <div class="clearfix"></div>
                        <?php
                        if ($keyword_field == 'show' || $location_field == 'show' || $category_field == 'show' && $sectors_enable_switch == 'on') {
                            ?>
                            <form class="careerfy-banner-search-three" method="get"
                                  action="<?php echo(get_permalink($result_page)); ?>">
                                <ul>
                                    <?php
                                    if ($keyword_field == 'show') {
                                        if ($autofill_keyword == 'yes') {
                                            wp_enqueue_script('jobsearch-search-box-sugg');
                                        }
                                        ?>
                                        <li>
                                            <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                <input placeholder="<?php esc_html_e('Job Title, Keywords, or Phrase', 'careerfy-frame') ?>"
                                                       name="search_title" data-type="job" type="text">
                                                <span class="sugg-search-loader"></span>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    if ($location_field == 'show') {
                                        if ($autofill_location == 'yes') {
                                            if ($location_map_type == 'mapbox') {
                                                wp_enqueue_script('jobsearch-mapbox');
                                                wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                wp_enqueue_script('mapbox-geocoder-polyfill');
                                                wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                            } else {
                                                wp_enqueue_script('jobsearch-google-map');
                                            }
                                            wp_enqueue_script('jobsearch-location-autocomplete');
                                        }
                                        ?>
                                        <li>
                                            <div class="jobsearch_searchloc_div">
                                                <?php if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                    echo get_radius_tooltip();
                                                }

                                                if ($autofill_location == 'yes') { ?>
                                                    <span class="loc-loader"></span>
                                                    <input placeholder="<?php esc_html_e('City, State or ZIP', 'careerfy-frame') ?>"
                                                           autocomplete="off" class="jobsearch_search_location_field"
                                                           type="text">
                                                    <input type="hidden"
                                                           class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                           name="location">
                                                <?php } else { ?>
                                                    <input placeholder="<?php esc_html_e('City, State or ZIP', 'careerfy-frame') ?>"
                                                           class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                           name="location" type="text">
                                                    <?php
                                                }
                                                //
                                                if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                <?php }
                                                if ($auto_geo_location == 'yes') {
                                                    ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"
                                                       onclick="JobsearchGetClientLocation()"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </li>
                                        <?php
                                    }


                                    if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                                        ?>
                                        <li>
                                            <div class="">
                                                <select name="sector_cat" class="selectize-select">
                                                    <option value=""><?php esc_html_e('Select Sector', 'careerfy-frame') ?></option>
                                                    <?php
                                                    foreach ($all_sectors as $term_sector) {
                                                        ?>
                                                        <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                    <li><i class="careerfy-icon careerfy-search-o"></i> <input type="submit"
                                                                                               value="<?php esc_html_e('Find', 'careerfy-frame') ?>">
                                    </li>
                                </ul>
                            </form>
                            <?php
                        }
                        if ($btn1_txt != '') {
                            ?>
                            <a href="<?php echo($btn1_url); ?>"
                               class="careerfy-upload-cvbtn"<?php echo($button_style) ?>><?php echo($btn_1_icon != '' ? '<i class="' . $btn_1_icon['value'] . '"></i>' : '') ?><?php echo($btn1_txt); ?></a>
                            <?php
                        }
                        $top_sectors = $wpdb->get_col($wpdb->prepare("SELECT terms.term_id FROM $wpdb->terms AS terms"
                            . " LEFT JOIN $wpdb->term_taxonomy AS term_tax ON(terms.term_id = term_tax.term_id) "
                            . " LEFT JOIN $wpdb->termmeta AS term_meta ON(terms.term_id = term_meta.term_id) "
                            . " WHERE term_tax.taxonomy=%s AND term_meta.meta_key=%s"
                            . " ORDER BY cast(term_meta.meta_value as unsigned) DESC LIMIT 4", 'sector', 'active_jobs_count'));

                        if (!empty($top_sectors) && !is_wp_error($top_sectors)) {
                            ?>
                            <ul class="careerfy-search-categories">
                                <li<?php echo($adv_search_paragraph_color) ?>><?php esc_html_e('Top Sectors :', 'careerfy-frame') ?></li>
                                <?php
                                foreach ($top_sectors as $term_id) {
                                    $term_sector = get_term_by('id', $term_id, 'sector');
                                    ?>
                                    <li>
                                        <a href="<?php echo add_query_arg(array('sector_cat' => $term_sector->slug), get_permalink($result_page)); ?>"<?php echo($adv_search_link_color) ?>><?php echo($term_sector->name) ?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
            } else if ($view == 'view2') {
                $without_loc_class = 'search-loc-off';
                if ($location_field == 'show') {
                    $without_loc_class = '';
                }
                $all_sectors = get_terms(array(
                    'taxonomy' => 'sector',
                    'hide_empty' => false,
                ));

                $without_sectr_class = 'search-cat-off';
                if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                    $without_sectr_class = '';
                }
                $without_keyword_class = 'search-keyword-off';
                if ($keyword_field == 'show') {
                    $without_keyword_class = '';
                }

                ?>
                <div class="careerfy-banner-two careerfy-typo-wrap <?php echo($design_css_class) ?>">
                    <div class="careerfy-banner-caption">
                        <?php
                        if ($srch_title != '') {
                            ?>
                            <h1<?php echo($adv_search_title_color) ?>><?php echo($srch_title) ?></h1>
                            <?php
                        }
                        if ($srch_desc != '') {
                            ?>
                            <p<?php echo($adv_search_paragraph_color) ?>><?php echo($srch_desc) ?></p>
                            <?php
                        }
                        //
                        if ($btn1_txt != '') { ?>
                            <div class="clearfix"></div>
                            <a href="<?php echo($btn1_url); ?>"
                               class="careerfy-banner-two-btn"<?php echo($button_style) ?>><?php echo($btn_1_icon != '' ? '<i class="' . $btn_1_icon['value'] . '"></i>' : '') ?><?php echo($btn1_txt); ?></a>
                            <?php
                        }
                        if ($keyword_field == 'show' || $location_field == 'show' || $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                            <div class="clearfix"></div>
                            <form class="careerfy-banner-search-two <?php echo($without_keyword_class) ?> <?php echo($without_sectr_class) ?> <?php echo($without_loc_class) ?>"
                                  method="get"
                                  action="<?php echo(get_permalink($result_page)); ?>">
                                <ul>
                                    <?php
                                    if ($keyword_field == 'show') {
                                        if ($autofill_keyword == 'yes') {
                                            wp_enqueue_script('jobsearch-search-box-sugg');
                                        }
                                        ?>
                                        <li>
                                            <i class="careerfy-icon careerfy-search-o"></i>
                                            <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                <input placeholder="<?php esc_html_e('Job Title, Keywords, or Phrase', 'careerfy-frame') ?>"
                                                       name="search_title" data-type="job" type="text">
                                                <span class="sugg-search-loader"></span>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    if ($location_field == 'show') {
                                        if ($autofill_location == 'yes') {
                                            if ($location_map_type == 'mapbox') {
                                                wp_enqueue_script('jobsearch-mapbox');
                                                wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                wp_enqueue_script('mapbox-geocoder-polyfill');
                                                wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                            } else {
                                                wp_enqueue_script('jobsearch-google-map');
                                            }
                                            wp_enqueue_script('jobsearch-location-autocomplete');
                                        }
                                        ?>
                                        <li>
                                            <div class="jobsearch_searchloc_div">
                                                <?php
                                                if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                    echo get_radius_tooltip();
                                                }
                                                if ($autofill_location == 'yes') { ?>
                                                    <i class="careerfy-icon careerfy-pin-line"></i>
                                                    <span class="loc-loader"></span>
                                                    <input placeholder="<?php esc_html_e('City, State or ZIP', 'careerfy-frame') ?>"
                                                           autocomplete="off" class="jobsearch_search_location_field"
                                                           type="text">
                                                    <input type="hidden"
                                                           class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                           name="location">
                                                <?php } else { ?>
                                                    <i class="careerfy-icon careerfy-pin-line"></i>
                                                    <input placeholder="<?php esc_html_e('City, State or ZIP', 'careerfy-frame') ?>"
                                                           class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                           name="location" type="text">
                                                    <?php
                                                }
                                                //
                                                if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                <?php }
                                                if ($auto_geo_location == 'yes') {
                                                    ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"
                                                       onclick="JobsearchGetClientLocation()"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                <?php } ?>
                                            </div>
                                        </li>
                                        <?php
                                    }

                                    if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                                        ?>
                                        <li>
                                            <i class="careerfy-icon careerfy-folder"></i>
                                            <div class="">
                                                <select name="sector_cat" class="selectize-select">
                                                    <option value=""><?php esc_html_e('Select Sector', 'careerfy-frame') ?></option>
                                                    <?php
                                                    foreach ($all_sectors as $term_sector) { ?>
                                                        <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </li>
                                    <?php } ?>
                                    <li><input type="submit"
                                               value="<?php esc_html_e('Search Jobs', 'careerfy-frame') ?>">
                                    </li>
                                </ul>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            <?php } else { ?>
                <div class="careerfy-typo-wrap <?php echo($design_css_class) ?>">
                    <div class="careerfy-banner-caption">
                        <?php
                        if ($srch_title != '') { ?>
                            <h1<?php echo($adv_search_title_color) ?>><?php echo($srch_title) ?></h1>
                            <?php
                        }
                        if ($srch_desc != '') { ?>
                            <p<?php echo($adv_search_paragraph_color) ?>><?php echo($srch_desc) ?></p>
                            <?php
                        }
                        if ($keyword_field == 'show' || $location_field == 'show' || $category_field == 'show' && $sectors_enable_switch == 'on') { ?>
                            <form class="careerfy-banner-search" method="get"
                                  action="<?php echo(get_permalink($result_page)); ?>">
                                <ul class="<?php echo apply_filters('careerfy_adv_srch_view1_ul_class', 'careerfy-jobs-srchul', $atts) ?>">
                                    <?php
                                    if ($keyword_field == 'show') {
                                        if ($autofill_keyword == 'yes') {
                                            wp_enqueue_script('jobsearch-search-box-sugg');
                                        }
                                        ob_start();
                                        ?>
                                        <li>
                                            <div class="<?php echo($autofill_keyword == 'yes' ? 'jobsearch-sugges-search' : '') ?>">
                                                <input placeholder="<?php esc_html_e('Job Title, Keywords, or Phrase', 'careerfy-frame') ?>"
                                                       name="search_title" data-type="job" type="text">
                                                <span class="sugg-search-loader"></span>
                                            </div>
                                        </li>
                                        <?php
                                        $srchfield_html = ob_get_clean();
                                        echo apply_filters('jobsearch_careerfy_advance_search_sh_frmtitle', $srchfield_html);
                                    }
                                    if ($location_field == 'show') {
                                        if ($autofill_location == 'yes') {
                                            if ($location_map_type == 'mapbox') {
                                                wp_enqueue_script('jobsearch-mapbox');
                                                wp_enqueue_script('jobsearch-mapbox-geocoder');
                                                wp_enqueue_script('mapbox-geocoder-polyfill');
                                                wp_enqueue_script('mapbox-geocoder-polyfillauto');
                                            } else {
                                                wp_enqueue_script('jobsearch-google-map');
                                            }
                                            wp_enqueue_script('jobsearch-location-autocomplete');
                                        }
                                        ob_start();
                                        ?>
                                        <li>
                                            <div class="jobsearch_searchloc_div">
                                                <?php if ($autofill_location == 'yes') { ?>
                                                    <span class="loc-loader"></span>
                                                    <input placeholder="<?php esc_html_e('City, State or ZIP', 'careerfy-frame') ?>"
                                                           autocomplete="off" class="jobsearch_search_location_field"
                                                           type="text">
                                                    <input type="hidden"
                                                           class="loc_search_keyword <?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                           name="location">
                                                <?php } else { ?>
                                                    <input placeholder="<?php esc_html_e('City, State or ZIP', 'careerfy-frame') ?>"
                                                           class="<?php echo($auto_geo_location == 'yes' ? 'srch_autogeo_location' : '') ?>"
                                                           name="location" type="text">
                                                    <?php
                                                }
                                                //

                                                if ($top_search_radius == 'yes' && $radius_field == 'show') {
                                                    echo get_radius_tooltip();
                                                }
                                                if ($top_search_radius == 'yes' && $radius_field == 'show' && $auto_geo_location == 'no') { ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                <?php }
                                                if ($auto_geo_location == 'yes') { ?>
                                                    <a href="javascript:void(0);" class="geolction-btn"
                                                       onclick="JobsearchGetClientLocation()"><i
                                                                class="careerfy-icon careerfy-location"></i></a>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </li>
                                        <?php
                                        $srchfield_html = ob_get_clean();
                                        echo apply_filters('jobsearch_careerfy_advance_search_sh_frmloc', $srchfield_html);
                                    }
                                    $all_sectors = get_terms(array(
                                        'taxonomy' => 'sector',
                                        'hide_empty' => false,
                                    ));

                                    if (!empty($all_sectors) && !is_wp_error($all_sectors) && $category_field == 'show' && $sectors_enable_switch == 'on') {
                                        ob_start(); ?>
                                        <li>
                                            <div class="careerfy-select-style">
                                                <select name="sector_cat" class="selectize-select">
                                                    <option value=""><?php esc_html_e('Select Sector', 'careerfy-frame') ?></option>
                                                    <?php
                                                    foreach ($all_sectors as $term_sector) { ?>
                                                        <option value="<?php echo urldecode($term_sector->slug) ?>"><?php echo($term_sector->name) ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </li>
                                        <?php
                                        $srchfield_html = ob_get_clean();
                                        echo apply_filters('jobsearch_careerfy_advance_search_sh_frmcat', $srchfield_html, $all_sectors);
                                    }
                                    ?>
                                    <li class="careerfy-banner-submit"><input type="submit" value=""> <i
                                                class="careerfy-icon careerfy-search-o"></i></li>
                                </ul>
                            </form>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <div class="careerfy-banner-btn">
                            <?php
                            ob_start();
                            if ($btn1_txt != '') { ?>
                                <a href="<?php echo($btn1_url) ?>"
                                   class="careerfy-bgcolorhover"<?php echo($button_style) ?>><?php echo($btn_1_icon['value'] != '' ? '<i class="' . $btn_1_icon['value'] . '"></i>' : '') ?><?php echo($btn1_txt) ?></a>
                                <?php
                            }
                            if ($btn2_txt != '') { ?>
                                <a href="<?php echo($btn2_url) ?>"
                                   class="careerfy-bgcolorhover"<?php echo($button_style) ?><?php echo($adv_search_btn_bg_color) ?>><?php echo($btn_2_icon != '' ? '<i class="' . $btn_2_icon['value'] . '"></i>' : '') ?><?php echo($btn2_txt) ?></a>
                                <?php
                            }
                            $btns_html = ob_get_clean();
                            echo apply_filters('jobsearch_advance_search_actbtns_html', $btns_html, $btn1_txt, $btn2_txt, $button_style, $adv_search_btn_bg_color);
                            ?>
                        </div>
                    </div>
                </div>

                <?php
            }
        }
        $html = ob_get_clean();
        echo $html;
    }

    private static function get_radius_tooltip()
    {
        global $jobsearch_plugin_options;
        $top_search_def_radius = isset($jobsearch_plugin_options['top_search_def_radius']) ? $jobsearch_plugin_options['top_search_def_radius'] : 50;
        $top_search_max_radius = isset($jobsearch_plugin_options['top_search_max_radius']) ? $jobsearch_plugin_options['top_search_max_radius'] : 500;
        $def_radius_unit = isset($jobsearch_plugin_options['top_search_radius_unit']) ? $jobsearch_plugin_options['top_search_radius_unit'] : '';
        ob_start(); ?>
        <div class="careerfy-radius-tooltip">
            <label><?php echo esc_html__('Radius', 'careerfy-frame') ?>
                ( <?php echo esc_html__($def_radius_unit, 'careerfy-frame') ?>
                )</label><input
                    type="number" name="loc_radius"
                    value="<?php echo($top_search_def_radius) ?>"
                    max="<?php echo($top_search_max_radius) ?>"></div>
        <?php
        $html = ob_get_clean();
        return $html;
    }

    protected function _content_template()
    {

    }
}