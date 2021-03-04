<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;


/**
 * @since 1.1.0
 */
class AboutInfo extends Widget_Base
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
        return 'about-info';
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
        return __('About Info', 'careerfy-frame');
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
        return 'fa fa-address-card';
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
        return ['careerfy'];
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
            'content_section',
            [
                'label' => __('About Info Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'abt_info_title',
            [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'abt_sub_title',
            [
                'label' => __('Sub Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'abt_desc',
            [
                'label' => __('User Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );
        $this->add_control(
            'abt_name',
            [
                'label' => __('User Name', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'abt_experi',
            [
                'label' => __('User Experience', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'abt_soc_icon', [
                'label' => __('Social Icon', 'careerfy-frame'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'abt_soc_link', [
                'label' => __('Social Link', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => __(get_site_url(), 'careerfy-frame'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'abt_social_links',
            [
                'label' => __('Add Icons', 'careerfy-frame'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ abt_soc_icon.value }}}',
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        extract(shortcode_atts(array(
            'abt_info_title' => '',
            'abt_sub_title' => '',
            'abt_name' => '',
            'abt_experi' => '',
            'abt_desc' => '',
            'abt_social_links' => '',
        ), $atts));
        ob_start(); ?>
        <div class="careerfy-team-parallex">
            <?php
            if ($abt_sub_title != '') {
                ?>
                <span><?php echo($abt_sub_title) ?></span>
                <?php
            }
            if ($abt_info_title != '') {
                ?>
                <h2><?php echo($abt_info_title) ?></h2>
                <?php
            }
            if ($abt_name != '' || $abt_experi != '') {
                ?>
                <h3><?php echo($abt_name) ?>
                    <small><?php echo($abt_experi) ?></small>
                </h3>
                <?php
            }
            if ($abt_desc != '') {
                ?>
                <p><?php echo($abt_desc) ?></p>
                <?php
            }

             if (!empty($abt_social_links)) { ?>
                <ul>
                    <?php foreach ($abt_social_links as $social_link) {
                        $abt_soc_icon = isset($social_link['abt_soc_icon']) ? $social_link['abt_soc_icon']['value'] : '';
                        $abt_soc_link = isset($social_link['abt_soc_link']) ? $social_link['abt_soc_link'] : '';

                        if ($abt_soc_icon != '') { ?>
                            <li>
                                <a href="<?php echo($abt_soc_link) ?>" class="<?php echo($abt_soc_icon) ?>"></a>
                            </li>
                            <?php
                        }
                    } ?>
                </ul>
            <?php } ?>
        </div>
        <?php $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    { ?>
        <#
        var abt_info_title = settings.abt_info_title;
        var abt_sub_title = settings.abt_sub_title;
        var abt_name = settings.abt_name;
        var abt_experi = settings.abt_experi;
        var abt_social_links  = settings.abt_social_links ;
        var abt_desc = settings.abt_desc;
        #>
        <div class="careerfy-team-parallex">
            <# if(abt_sub_title != ''){ #>
            <span>{{{abt_sub_title}}}</span>
            <# } #>

            <# if(abt_info_title != ''){ #>
            <h2>{{{abt_info_title}}}</h2>
            <# } #>

            <# if(abt_name != '' || abt_experi != ''){ #>
            <h3>{{{abt_name}}}
                <small>{{{abt_experi}}}</small>
            </h3>
            <# } #>

            <# if(abt_desc != ''){ #>
            <p>{{{abt_desc}}}</p>
            <# } #>

            <# if(abt_social_links != ''){ #>
            <ul>
                <# _.each(settings.abt_social_links, function(item,index) { #>
                <# if(item.abt_soc_icon != ''){ #>
                    <li>
                        <a href="#" class="{{{item.abt_soc_icon.value}}}"></a>
                    </li>
                <# } #>
                <# }) #>
            </ul>
            <# } #>

        </div>
    <?php }
}