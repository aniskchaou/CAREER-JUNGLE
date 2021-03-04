<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

/**
 * @since 1.1.0
 */
class LoginRegisteration extends Widget_Base
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
        return 'login-registeration-form';
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
        return __('Login Registration Form', 'careerfy-frame');
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
        return 'fa fa-wpforms';
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
        global $rand_num;
        $rand_num = rand(10000000, 99909999);
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Login Registration', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'login_registration_title',
            [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'login_register_form',
            [
                'label' => __('Enable Register', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'on',
                'options' => [
                    'on' => __('Yes', 'careerfy-frame'),
                    'off' => __('No', 'careerfy-frame'),

                ],
            ]
        );

        $this->add_control(
            'logreg_form_type',
            [
                'label' => __('Form Type', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'both',
                'options' => [
                    'both' => __('Both Forms', 'careerfy-frame'),
                    'reg_only' => __('Register Form Only', 'careerfy-frame'),
                    'login_only' => __('Login Form Only', 'careerfy-frame'),

                ],
            ]
        );
        $this->add_control(
            'login_candidate_register',
            [
                'label' => __('Enable Candidate Registration', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );
        $this->add_control(
            'login_employer_register',
            [
                'label' => __('Enable Employer Registration', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'careerfy-frame'),
                    'no' => __('No', 'careerfy-frame'),
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        ob_start(); ?>
        <div class="jobsearch-form jobsearch-login-registration-form">
            <?php if ($atts['login_registration_title'] != '') { ?>
                <div class="jobsearch-contact-title"><h2><?php echo esc_html($atts['login_registration_title']); ?></h2>
                </div>
                <!--title end-->
                <?php
            }
            do_action('login_registration_form_html', $atts);
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