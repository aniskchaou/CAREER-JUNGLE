<?php
namespace CareerfyElementor\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;
/**
 * @since 1.1.0
 */
class BannerAdvertisement extends Widget_Base
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
        return 'banner-advertisement';
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
        return __('Banner Advertisement', 'careerfy-frame');
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
        return 'fa fa-ad';
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
        $groups_value = isset($jobsearch_plugin_options['ad_banner_groups']) ? $jobsearch_plugin_options['ad_banner_groups'] : '';
        $sinle_value = isset($jobsearch_plugin_options['ad_banners_list']) ? $jobsearch_plugin_options['ad_banners_list'] : '';

        $group_add_arr = array(esc_html__('Select banner', 'careerfy-frame') => '');
        if (isset($groups_value) && !empty($groups_value) && is_array($groups_value)) {
            for ($ad = 0; $ad < count($groups_value['group_title']); $ad++) {
                $ad_title = $groups_value['group_title'][$ad];
                $ad_code = $groups_value['group_code'][$ad];
                $group_add_arr[$ad_code] = $ad_title;
            }
        }
        $single_add_arr = array(esc_html__('Select banner', 'careerfy-frame') => '');
        if (isset($sinle_value) && !empty($sinle_value) && is_array($sinle_value)) {
            for ($ad = 0; $ad < count($sinle_value['banner_title']); $ad++) {
                $ad_title = $sinle_value['banner_title'][$ad];
                $ad_code = $sinle_value['banner_code'][$ad];
                $single_add_arr[$ad_code] = $ad_title;
            }
        }
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Banner Advertisement Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'banner_style',
            [
                'label' => __('Banner Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'group_banner',
                'options' => [
                    'single_banner' => __('Single Banner', 'careerfy-frame'),
                    'group_banner' => __('Group Banner', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'banner_sinle_style',
            [
                'label' => __('Single Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'options' => $single_add_arr,
                'condition' => [
                    'banner_style' => 'single_banner'
                ]
            ]
        );

        $this->add_control(
            'banner_group_style',
            [
                'label' => __('Group Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'options' => $group_add_arr,
                'condition' => [
                    'banner_style' => 'group_banner'
                ]
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        global $jobsearch_plugin_options;
        $atts = $this->get_settings_for_display();
        extract(shortcode_atts(array(
            'banner_style' => '',
            'banner_sinle_style' => '',
            'banner_group_style' => '',
        ), $atts));
        $banner_style = $atts['banner_style'];
        $banner_sinle_style = $atts['banner_sinle_style'];
        $banner_group_style = $atts['banner_group_style'];

        $shortcode_html = '';
        if (isset($banner_style) && $banner_style == 'group_banner') {
            $shortcode_html = '[jobsearch_ads_group code="' . $banner_group_style . '"]';
        } else {
            $shortcode_html = '[jobsearch_ad code="' . $banner_sinle_style . '"]';
        }
        ob_start();
        echo do_shortcode($shortcode_html);
        $ad_html = ob_get_clean();
        echo $ad_html;
    }

    protected function _content_template()
    {

    }
}