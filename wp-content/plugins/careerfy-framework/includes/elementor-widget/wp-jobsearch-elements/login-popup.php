<?php

namespace CareerfyElementor\Widgets;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use WP_Jobsearch\Package_Limits;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

/**
 * Elementor button widget.
 *
 * Elementor widget that displays a button with the ability to control every
 * aspect of the button design.
 *
 * @since 1.0.0
 */
class LoginPopup extends Widget_Base
{

    /**
     * Get widget name.
     *
     * Retrieve button widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'login-popup';
    }

    /**
     * Get widget title.
     *
     * Retrieve button widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return __('Login Popup Buttons', 'careerfy-frame');
    }

    /**
     * Get widget icon.
     *
     * Retrieve button widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'far fa-user-circle';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the button widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * @since 2.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories()
    {
        return ['careerfy'];
    }

    /**
     * Get button sizes.
     *
     * Retrieve an array of button sizes for the button widget.
     *
     * @since 1.0.0
     * @access public
     * @static
     *
     * @return array An array containing button sizes.
     */
    public static function get_button_sizes()
    {
        return [
            'xs' => __('Extra Small', 'careerfy-frame'),
            'sm' => __('Small', 'careerfy-frame'),
            'md' => __('Medium', 'careerfy-frame'),
            'lg' => __('Large', 'careerfy-frame'),
            'xl' => __('Extra Large', 'careerfy-frame'),
        ];
    }

    /**
     * Register button widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function _register_controls()
    {
        $this->start_controls_section(
            'section_button',
            [
                'label' => __('Login Button Style', 'careerfy-frame'),
            ]
        );

        $this->add_control(
            'button_type',
            [
                'label' => __('Type', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'login_only',
                'options' => [
                    'login_only' => __('login Only', 'careerfy-frame'),
                    'register_only' => __('Register Only', 'careerfy-frame'),
                    'register_login_both' => __('Login and Register', 'careerfy-frame'),
                    'login_notification' => __('Login with notification', 'careerfy-frame'),
                    'register_notification' => __('Register with notification', 'careerfy-frame'),
                    'register_login_notification' => __('Login and Register with notification', 'careerfy-frame'),
                ],
                'description' => __('Also check the notification settings from  Jobsearch Options => dashboard settings.', 'careerfy-frame'),
            ]
        );

        $this->add_control(
            'login_btn_title',
            [
                'label' => __('Login Button Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Login', 'careerfy-frame'),
                'placeholder' => __('Login', 'careerfy-frame'),
                'condition' => [
                    'button_type' => ['login_only', 'login_notification', 'register_login_both', 'register_login_notification'],
                ],
            ]
        );

        $this->add_control(
            'register_btn_title',
            [
                'label' => __('Register Button Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Register', 'careerfy-frame'),
                'placeholder' => __('Register', 'careerfy-frame'),
                'condition' => [
                    'button_type' => ['register_only', 'register_notification', 'register_login_both', 'register_login_notification'],
                ],
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => __('Alignment', 'careerfy-frame'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'careerfy-frame'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'careerfy-frame'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'careerfy-frame'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'careerfy-frame'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'prefix_class' => 'elementor%s-align-',
                'default' => 'right',
            ]
        );

        $this->add_control(
            'size',
            [
                'label' => __('Size', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT,
                'default' => 'sm',
                'options' => self::get_button_sizes(),
                'style_transfer' => true,
            ]
        );

        $this->add_control(
            'selected_icon',
            [
                'label' => __('Login Icon', 'careerfy-frame'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'condition' => [
                    'button_type' => ['login_only', 'login_notification', 'register_login_both', 'register_login_notification'],
                ],
            ]
        );

        $this->add_control(
            'selected_icon_register',
            [
                'label' => __('Register Icon', 'careerfy-frame'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'condition' => [
                    'button_type' => ['register_only', 'register_notification', 'register_login_both', 'register_login_notification'],
                ],
            ]
        );

        $this->add_control(
            'icon_align',
            [
                'label' => __('Icon Position', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT,
                'default' => 'left',
                'options' => [
                    'left' => __('Before', 'careerfy-frame'),
                    'right' => __('After', 'careerfy-frame'),
                ],
                'condition' => [
                    'selected_icon[value]!' => '',
                ],
            ]
        );

        $this->add_control(
            'icon_indent',
            [
                'label' => __('Icon Spacing', 'careerfy-frame'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'dual_button_gap',
            [
                'label' => __('Button Gap', 'careerfy-frame'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 5,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.login-button' => 'margin-right: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_control(
            'view',
            [
                'label' => __('View', 'careerfy-frame'),
                'type' => Controls_Manager::HIDDEN,
                'default' => 'traditional',
            ]
        );

        $this->add_control(
            'button_css_id',
            [
                'label' => __('Button ID', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => '',
                'title' => __('Add your custom id WITHOUT the Pound key. e.g: my-id', 'careerfy-frame'),
                'description' => __('Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'careerfy-frame'),
                'separator' => 'before',

            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_my_account_button',
            [
                'label' => __('My Account Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'dropdown_description',
            [
                'raw' => __('My Account button can be seen after login and all these styles for my account as well.', 'careerfy-frame'),
                'type' => Controls_Manager::RAW_HTML,
                'content_classes' => 'elementor-descriptor',
            ]
        );

        $this->add_control(
            'account_btn_title',
            [
                'label' => __('Account Link Button Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('My Account', 'careerfy-frame'),
                'placeholder' => __('My Account', 'careerfy-frame'),
            ]
        );
        /*
         * Account Profile Settings
         * */
        $this->add_control(
            'profile_username_switch',
            [
                'label' => __('Profile Username On/Off', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'off',
                'options' => [
                    'on' => __('On', 'careerfy-frame'),
                    'off' => __('Off', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'profile_image_switch',
            [
                'label' => __('Profile Image On/Off', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'off',
                'options' => [
                    'on' => __('On', 'careerfy-frame'),
                    'off' => __('Off', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'image_spacing',
            [
                'label' => __('Image Spacing', 'careerfy-frame'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button-wrapper img' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'profile_image_switch' => ['on'],
                ],
            ]
        );

        $this->add_control(
            'profile_image_width',
            [
                'label' => __('Image Width', 'careerfy-frame'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'size' => 20,
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button-wrapper img' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'profile_image_switch' => ['on'],
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .elementor-button-wrapper img',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label' => __('Border Radius', 'careerfy-frame'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        /*
         * End Account Profile Settings
         * */

        $this->add_control(
            'view_account_link',
            [
                'label' => __('View', 'careerfy-frame'),
                'type' => Controls_Manager::HIDDEN,
                'default' => 'traditional',
            ]
        );

        $this->add_control(
            'button_css_id_account_link',
            [
                'label' => __('Button ID', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => '',
                'title' => __('Add your custom id WITHOUT the Pound key. e.g: my-id', 'careerfy-frame'),
                'description' => __('Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'careerfy-frame'),
                'separator' => 'before',

            ]
        );

        $this->end_controls_section();
        /*
         * login button styles
         */

        /*
         * Register button styles
         */
        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Login Button Style', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .elementor-button.login-button',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .elementor-button.login-button',
            ]
        );

        $this->start_controls_tabs('tabs_button_style');

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => __('Normal', 'careerfy-frame'),
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => __('Text Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.login-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label' => __('Background Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_ACCENT,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.login-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => __('Hover', 'careerfy-frame'),
            ]
        );

        $this->add_control(
            'hover_color',
            [
                'label' => __('Text Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.login-button:hover, {{WRAPPER}} .elementor-button.login-button:focus' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementor-button.login-button:hover svg, {{WRAPPER}} .elementor-button.login-button:focus svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_hover_color',
            [
                'label' => __('Background Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.login-button:hover, {{WRAPPER}} .elementor-button.login-button:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label' => __('Border Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.login-button:hover, {{WRAPPER}} .elementor-button.login-button:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hover_animation',
            [
                'label' => __('Hover Animation', 'careerfy-frame'),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} .elementor-button.login-button',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => __('Border Radius', 'careerfy-frame'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.login-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .elementor-button.login-button',
            ]
        );

        $this->add_responsive_control(
            'text_padding',
            [
                'label' => __('Padding', 'careerfy-frame'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.login-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        /*
         * End login button styles
         */

        /*
         *  Register button styles
         */

        $this->start_controls_section(
            'section_style_register',
            [
                'label' => __('Register Button Styles', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_register',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .elementor-button.register-button',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow_register',
                'selector' => '{{WRAPPER}} .elementor-button.register-button',
            ]
        );

        $this->start_controls_tabs('tabs_button_style_register');

        $this->start_controls_tab(
            'tab_button_normal_register',
            [
                'label' => __('Normal', 'careerfy-frame'),
            ]
        );

        $this->add_control(
            'button_text_color_register',
            [
                'label' => __('Text Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.register-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_color_register',
            [
                'label' => __('Background Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_ACCENT,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.register-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover_register',
            [
                'label' => __('Hover', 'careerfy-frame'),
            ]
        );

        $this->add_control(
            'hover_color_register',
            [
                'label' => __('Text Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.register-button:hover, {{WRAPPER}} .elementor-button.register-button:focus' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementor-button.register-button:hover svg, {{WRAPPER}} .elementor-button.register-button:focus svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_hover_color_register',
            [
                'label' => __('Background Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.register-button:hover, {{WRAPPER}} .elementor-button.register-button:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_border_color_register',
            [
                'label' => __('Border Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.register-button:hover, {{WRAPPER}} .elementor-button.register-button:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hover_animation_register',
            [
                'label' => __('Hover Animation', 'careerfy-frame'),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border_register',
                'selector' => '{{WRAPPER}} .elementor-button.register-button',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'border_radius_register',
            [
                'label' => __('Border Radius', 'careerfy-frame'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.register-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow_register',
                'selector' => '{{WRAPPER}} .elementor-button.register-button',
            ]
        );

        $this->add_responsive_control(
            'text_padding_register',
            [
                'label' => __('Padding', 'careerfy-frame'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.register-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();
        /*
         * Notification Bell Style
         * */
        $this->start_controls_section(
            'notification_style',
            [
                'label' => __('Notification Bell Style', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'bell_notification_bubble_color',
            [
                'label' => __('Icon Bubble Background Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .careerfy-btns-con-elementor  .elementor-item-anchor .hderbell-notifics-count' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'bell_notification_bubble_txt_color',
            [
                'label' => __('Icon Bubble Text Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .careerfy-btns-con-elementor  .elementor-item-anchor .hderbell-notifics-count' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'bell_notification_color',
            [
                'label' => __('Icon Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .careerfy-btns-con-elementor  .elementor-item-anchor' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'bell_notification_color_hover',
            [
                'label' => __('Icon Hover Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .careerfy-btns-con-elementor  .elementor-item-anchor:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'bell_notification_background_color',
            [
                'label' => __('Icon Background Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .careerfy-btns-con-elementor  .elementor-item-anchor' => 'background-color: {{VALUE}};',],
            ]
        );
        $this->add_control(
            'bell_notification_background_color_hover',
            [
                'label' => __('Icon Background Color Hover', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .careerfy-btns-con-elementor  .elementor-item-anchor:hover' => 'background-color: {{VALUE}};',],
            ]
        );

        $this->add_control(
            'notification_button_gap',
            [
                'label' => __('Icon Gap', 'careerfy-frame'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 5,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .careerfy-btns-con-elementor' => 'margin-right: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_control(
            'notification_icon_size',
            [
                'label' => __('Icon Size', 'careerfy-frame'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 24,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .careerfy-btns-con-elementor a i' => 'font-size: {{SIZE}}px;',
                ],
            ]
        );

        $this->end_controls_section();
        /*
         * End Notification Bell Style
         * */
        /*
         * My Account button settings
         * */

        $this->start_controls_section(
            'my_account',
            [
                'label' => __('My Account Button Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_account_link',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .elementor-button.elementor-button-account-link',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow_account_link',
                'selector' => '{{WRAPPER}} .elementor-button.elementor-button-account-link',
            ]
        );

        $this->start_controls_tabs('tabs_button_style_account_link');

        $this->start_controls_tab(
            'tab_button_normal_account_link',
            [
                'label' => __('Normal', 'careerfy-frame'),
            ]
        );

        $this->add_control(
            'button_text_color_account_link',
            [
                'label' => __('Text Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.elementor-button-account-link' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_color_account_link',
            [
                'label' => __('Background Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_ACCENT,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.elementor-button-account-link' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover_account_link',
            [
                'label' => __('Hover', 'careerfy-frame'),
            ]
        );

        $this->add_control(
            'hover_color_account_link',
            [
                'label' => __('Text Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.elementor-button-account-link:hover, {{WRAPPER}} .elementor-button.elementor-button-account-link:focus' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementor-button.elementor-button-account-link:hover svg, {{WRAPPER}} .elementor-button.elementor-button-account-link:focus svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_hover_color_account_link',
            [
                'label' => __('Background Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.elementor-button-account-link:hover, {{WRAPPER}} .elementor-button.elementor-button-account-link:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_border_color_account_link',
            [
                'label' => __('Border Color', 'careerfy-frame'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.elementor-button-account-link:hover, {{WRAPPER}} .elementor-button.elementor-button-account-link:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hover_animation_account_link',
            [
                'label' => __('Hover Animation', 'careerfy-frame'),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border_account_link',
                'selector' => '{{WRAPPER}} .elementor-button.elementor-button-account-link',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'border_radius_account_link',
            [
                'label' => __('Border Radius', 'careerfy-frame'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.elementor-button-account-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow_account_link',
                'selector' => '{{WRAPPER}} .elementor-button.elementor-button-account-link',
            ]
        );

        $this->add_responsive_control(
            'text_padding_account_link',
            [
                'label' => __('Padding', 'careerfy-frame'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-button.elementor-button-account-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render button widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render()
    {
        global $jobsearch_plugin_options;
        $settings = $this->get_settings_for_display();
        extract(shortcode_atts(array(
            'button_type' => '',
            'login_btn_title' => '',
            'register_btn_title' => '',
        ), $settings));


        $user_pkg_limits = new Package_Limits;

        $this->add_render_attribute('wrapper', 'class', 'elementor-button-wrapper jobsearch-useraccount-linksbtn');

        /*
         * Login Button settings
         * */
        $this->add_render_attribute('button_login', 'class', 'elementor-button login-button jobsearch-user-loginbtn jobsearch-open-signin-tab');
        $this->add_render_attribute('button_login', 'role', 'button_login');

        if (!empty($settings['button_css_id'])) {
            $this->add_render_attribute('button_login', 'id', $settings['button_css_id']);
        }

        if (!empty($settings['size'])) {
            $this->add_render_attribute('button_login', 'class', 'elementor-size-' . $settings['size']);
        }

        if ($settings['hover_animation']) {
            $this->add_render_attribute('button_login', 'class', 'elementor-animation-' . $settings['hover_animation']);
        }

        /*
         * Register Button settings
         * */

        $this->add_render_attribute('button_register', 'class', 'elementor-button register-button jobsearch-user-loginbtn jobsearch-open-register-tab');
        $this->add_render_attribute('button_register', 'role', 'button_register');

        if (!empty($settings['button_css_id'])) {
            $this->add_render_attribute('button_register', 'id', $settings['button_css_id']);
        }

        if (!empty($settings['size'])) {
            $this->add_render_attribute('button_register', 'class', 'elementor-size-' . $settings['size']);
        }

        if ($settings['hover_animation']) {
            $this->add_render_attribute('button_register', 'class', 'elementor-animation-' . $settings['hover_animation']);
        }

        /*
         * Logged In Menu settings
         * */

        $this->add_render_attribute('account_links_button', 'class', 'elementor-button elementor-button-account-link jobsearch-user-loginbtn elementor-item has-submenu');
        $this->add_render_attribute('account_links_button', 'role', 'account_links_button');

        if (!empty($settings['button_css_id_account_link'])) {
            $this->add_render_attribute('account_links_button', 'id', $settings['button_css_id_account_link']);
        }

        if (!empty($settings['size'])) {
            $this->add_render_attribute('account_links_button', 'class', 'elementor-size-' . $settings['size']);
        }

        if ($settings['hover_animation_account_link']) {
            $this->add_render_attribute('account_links_button', 'class', 'elementor-animation-' . $settings['hover_animation_account_link']);
        }
        $dash_notifics_switch = isset($jobsearch_plugin_options['dash_notifics_switch']) ? $jobsearch_plugin_options['dash_notifics_switch'] : '';

        ob_start();
        $current_user = wp_get_current_user();

        if (is_user_logged_in() && $current_user->roles[0] != 'administrator' && !isset($_GET['action'])) {
            $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
            $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
            $page_id = jobsearch__get_post_id($user_dashboard_page, 'page');
            $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page');
            $profile_page_url = $page_url;
            if (wp_is_mobile()) {
                $profile_page_url = 'javascript:void(0);';
            }
            $user_displayname = '';
            $get_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';
            $profile_image_switch = $settings['profile_image_switch'];

            if ($settings['profile_username_switch'] == 'on') {
                $user_id = $current_user->ID;
                $user_obj = get_user_by('ID', $user_id);
                $user_displayname = isset($user_obj->display_name) ? $user_obj->display_name : '';
                $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_obj);
            }
            $user_def_avatar_url = '';
            if ($profile_image_switch == 'on') {
                if (jobsearch_user_is_employer($current_user->ID)) {
                    $employer_user_id = $current_user->ID;
                    $employer_id = jobsearch_get_user_employer_id($employer_user_id);
                    $user_def_avatar_url = get_avatar_url($employer_user_id, array('size' => 140));
                    $user_avatar_id = get_post_thumbnail_id($employer_id);
                    if ($user_avatar_id > 0) {
                        $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
                        $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
                    } else {
                        $user_def_avatar_url = jobsearch_employer_image_placeholder();
                    }
                } else if (jobsearch_user_is_candidate($current_user->ID)) {
                    $cand_user_id = $current_user->ID;
                    $candidate_user_id = jobsearch_get_user_candidate_id($cand_user_id);
                    $user_def_avatar_url = jobsearch_candidate_img_url_comn($candidate_user_id);
                } else {
                    $user_def_avatar_url = get_avatar_url($current_user->ID);
                }
            }
            ?>

            <div <?php echo $this->get_render_attribute_string('wrapper'); ?>>

            <?php
            if ($dash_notifics_switch == 'on') {
                $args = array(
                    'is_popup' => true,
                    'is_elementor' => true,
                );
                echo apply_filters('jobsearch_dashmenu_account_btns_before_items', '', $args);
            }
            if (!empty($user_def_avatar_url)) { ?>
                <img src="<?php echo($user_def_avatar_url) ?>">
            <?php } ?>

            <a href="<?php echo($profile_page_url) ?>"
                <?php echo $this->get_render_attribute_string('account_links_button'); ?>><?php echo $this->render_text_my_account($user_displayname); ?></a>
            <ul <?php echo apply_filters('jobsearch_hdr_menu_accountlinks_ul_atts', 'class="nav-item-children sub-menu"') ?>>
                <?php jobsearch_user_account_linkitems($user_pkg_limits, $page_url, $get_tab); ?>
            </ul>

        <?php } else { ?>
            <?php


            if ($button_type == 'login_notification' || $button_type == 'register_notification' || $button_type == 'register_login_notification') {
                if ($dash_notifics_switch == 'on') {
                    ?>
                    <div class="careerfy-btns-con careerfy-btns-con-elementor">
                    <ul class="careerfy-user-section careerfy-user-section-elementor">
                    <?php

                    $args = array(
                        'is_popup' => true,
                        'is_elementor' => true,
                    );
                    echo apply_filters('jobsearch_dashmenu_account_btns_before_items', '', $args);
                ?>
                </ul>
                </div>
            <?php } ?>
        <?php } ?>
        <?php

        if ($button_type == 'login_only' || $button_type == 'register_login_both' || $button_type == 'register_login_notification' || $button_type == 'login_notification') { ?>
            <a href="javascript:void(0);"
                <?php echo $this->get_render_attribute_string('button_login'); ?>><?php echo $this->render_text_login() ?></a>
        <?php } ?>
        <?php if ($button_type == 'register_only' || $button_type == 'register_login_both' || $button_type == 'register_login_notification' || $button_type == 'register_notification') { ?>
        <a href="javascript:void(0);"
            <?php echo $this->get_render_attribute_string('button_register'); ?>><?php echo $this->render_text_register() ?></a>
        <?php
    }
    } ?>
    </div>
<?php
$html = ob_get_clean();
echo $html;
}

/**
 * Render button widget output in the editor.
 *
 * Written as a Backbone JavaScript template and used to generate the live preview.
 *
 * @since 2.9.0
 * @access protected
 */
protected
function content_template()
{ ?>
    <#
    view.addRenderAttribute( 'text', 'class', 'elementor-button-text' );
    view.addInlineEditingAttributes( 'text', 'none' );
    var iconHTML = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' );
    var iconHTMLRegister = elementor.helpers.renderIcon( view, settings.selected_icon_register, { 'aria-hidden': true }, 'i' , 'object' );
    migrated = elementor.helpers.isIconMigrated( settings, 'selected_icon' );
    #>
    <div class="jobsearch-useraccount-linksbtn">
        <# if(settings.button_type == 'login_notification' || settings.button_type == 'register_notification' ||
        settings.button_type == 'register_login_notification'){ #>
        <div class="careerfy-btns-con careerfy-btns-con-elementor">
            <ul class="careerfy-user-section careerfy-user-section-elementor">
                <li class="jobsearch-usernotifics-menubtn jobsearch-usernotifics-elementor menu-item menu-item-type-custom menu-item-object-custom">
                    <a href="javascript:void(0);" class="elementor-item elementor-item-anchor"><i
                                class="fa fa-bell-o"><span class="hderbell-notifics-count">0</span></i></a>
                    <div class="jobsearch-hdernotifics-listitms">
                        <div class="hdernotifics-title-con">
                            <span class="hder-notifics-title">Notifications</span>
                            <span class="hder-notifics-count"><small>0</small> new</span>
                        </div>
                        <span class="hder-notifics-nofound">Notifications will show on front end.</span>
                    </div>
                </li>
            </ul>
        </div>
        <# } #>

        <# if(settings.button_type == 'login_only' || settings.button_type == 'login_notification' ||
        settings.button_type == 'register_login_both' || settings.button_type == 'register_login_notification'){ #>
        <a id="{{ settings.button_css_id }}"
           class="elementor-button login-button elementor-size-{{ settings.size }} elementor-animation-{{ settings.hover_animation }}"
           href="#" role="button">
				<span class="elementor-button-content-wrapper">
					<# if ( settings.icon || settings.selected_icon ) { #>
					<span class="elementor-button-icon elementor-align-icon-{{ settings.icon_align }}">
						<# if ( ( migrated || ! settings.icon ) && iconHTML.rendered ) { #>
							{{{ iconHTML.value }}}
						<# } else { #>
							<i class="{{settings.icon}}" aria-hidden="true"></i>
						<# } #>
					</span>
					<# } #>
					<span {{{ view.getRenderAttributeString( 'text' ) }}}>{{{ settings.login_btn_title }}}</span></span>
        </a>
        <# } #>

        <# if(settings.button_type == 'register_only' || settings.button_type == 'register_notification' ||
        settings.button_type == 'register_login_both' || settings.button_type == 'register_login_notification'){ #>
        <a id="{{ settings.button_css_id }}"
           class="elementor-button register-button elementor-size-{{ settings.size }} elementor-animation-{{ settings.hover_animation }}"
           href="#" role="button">
				<span class="elementor-button-content-wrapper">
					<# if ( settings.icon || settings.selected_icon_register ) { #>
					<span class="elementor-button-icon elementor-align-icon-{{ settings.icon_align }}">
						<# if ( ( migrated || ! settings.icon ) && iconHTMLRegister.rendered ) { #>
							{{{ iconHTMLRegister.value }}}
						<# } else { #>
							<i class="{{ settings.icon }}" aria-hidden="true"></i>
						<# } #>
					</span>
					<# } #>
					<span {{{view.getRenderAttributeString( 'text' ) }}}>{{{ settings.register_btn_title }}}</span>
            </span>
        </a>
        <# } #>
    </div>


<?php }

/**
 * Render button text.
 *
 * Render button widget text.
 *
 * @since 1.5.0
 * @access protected
 */
protected
function render_text_my_account($user_displayname)
{
    global $sitepress;
    $lang_code = '';
    if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
        $lang_code = $sitepress->get_current_language();
    }
    $settings = $this->get_settings_for_display();

    $migrated = isset($settings['__fa4_migrated']['selected_icon_account_link']);
    $is_new = empty($settings['icon']) && Icons_Manager::is_migration_allowed();

    $this->add_render_attribute([
        'my-account-content-wrapper' => [
            'class' => 'elementor-button-account-link-wrapper',
        ],
        'icon-align-account-link' => [
            'class' => [
                'elementor-button-icon',

            ],
        ],
        'text' => [
            'class' => 'elementor-button-text',
        ],
    ]);

    $this->add_inline_editing_attributes('text', 'none');
    ?>
    <span <?php echo $this->get_render_attribute_string('my-account-content-wrapper'); ?>>
        <span <?php echo $this->get_render_attribute_string('account_btn_title'); ?>><?php echo apply_filters('wpml_translate_single_string', $settings['account_btn_title'], 'Jobsearch Shortcode', 'Login account register text', $lang_code) ?>&nbsp;<?php echo $user_displayname ?></span>
        </span>
    <?php
}

protected
function render_text_login()
{
    global $sitepress;
    $lang_code = '';
    if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
        $lang_code = $sitepress->get_current_language();
    }
    $settings = $this->get_settings_for_display();

    $migrated = isset($settings['__fa4_migrated']['selected_icon']);
    $is_new = empty($settings['icon']) && Icons_Manager::is_migration_allowed();

    if (!$is_new && empty($settings['icon_align'])) {

        $settings['icon_align'] = $this->get_settings('icon_align');
    }

    $this->add_render_attribute([
        'login-content-wrapper' => [
            'class' => 'elementor-button-content-wrapper',
        ],
        'icon-align' => [
            'class' => [
                'elementor-button-icon',
                'elementor-align-icon-' . $settings['icon_align'],
            ],
        ],
        'text' => [
            'class' => 'elementor-button-text',
        ],
    ]);


    $this->add_inline_editing_attributes('text', 'none');

    ?>
    <span <?php echo $this->get_render_attribute_string('login-content-wrapper'); ?>>
			<?php if (!empty($settings['icon']) || !empty($settings['selected_icon']['value'])) : ?>
                <span <?php echo $this->get_render_attribute_string('icon-align'); ?>>
				<?php if ($is_new || $migrated) :
                    Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']);
                else : ?>
                    <i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
                <?php endif; ?>
			</span>
            <?php endif; ?>
			<span <?php echo $this->get_render_attribute_string('text'); ?>><?php echo apply_filters('wpml_translate_single_string', $settings['login_btn_title'], 'Jobsearch Shortcode', 'Login account register text', $lang_code) ?></span>
		</span>
    <?php
}

protected
function render_text_register()
{
    global $sitepress;
    $lang_code = '';
    if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
        $lang_code = $sitepress->get_current_language();
    }
    $settings = $this->get_settings_for_display();

    $migrated = isset($settings['__fa4_migrated']['selected_icon_register']);
    $is_new = empty($settings['icon']) && Icons_Manager::is_migration_allowed();


    if (!$is_new && empty($settings['icon_align'])) {
        // @todo: remove when deprecated
        // added as bc in 2.6
        //old default
        $settings['icon_align'] = $this->get_settings('icon_align');
    }

    $this->add_render_attribute([
        'register-content-wrapper' => [
            'class' => 'elementor-button-content-wrapper',
        ],
        'icon-align' => [
            'class' => [
                'elementor-button-icon',
                'elementor-align-icon-' . $settings['icon_align'],
            ],
        ],
        'text' => [
            'class' => 'elementor-button-text',
        ],
    ]);

    $this->add_inline_editing_attributes('text', 'none');
    ?>
    <span <?php echo $this->get_render_attribute_string('register-content-wrapper'); ?>>
			<?php if (!empty($settings['icon']) || !empty($settings['selected_icon_register']['value'])) : ?>
                <span <?php echo $this->get_render_attribute_string('icon-align'); ?>>
				<?php if ($is_new || $migrated) :
                    Icons_Manager::render_icon($settings['selected_icon_register'], ['aria-hidden' => 'true']);
                else : ?>
                    <i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
                <?php endif; ?>
			</span>
            <?php endif; ?>
			<span <?php echo $this->get_render_attribute_string('text'); ?>><?php echo apply_filters('wpml_translate_single_string', $settings['register_btn_title'], 'Jobsearch Shortcode', 'Login account register text', $lang_code) ?></span>
		</span>
    <?php
}

public
function on_import($element)
{
    return Icons_Manager::on_import_migration($element, 'icon', 'selected_icon');
}
}
