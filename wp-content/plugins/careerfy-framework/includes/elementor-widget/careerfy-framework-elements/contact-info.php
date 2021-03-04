<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;


/**
 * @since 1.1.0
 */
class ContactInfo extends Widget_Base
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
        return 'contact-info';
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
        return __('Contact Info', 'careerfy-frame');
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
        return 'fa fa-address-book';
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
        $cf7_posts = get_posts(array(
            'post_type' => 'wpcf7_contact_form',
            'numberposts' => -1
        ));
        $cf7_arr = array(
            esc_html__("Select Form", "careerfy-frame") => ''
        );
        if (!empty($cf7_posts)) {
            foreach ($cf7_posts as $p) {
                $cf7_arr[$p->post_name] = $p->post_title;
            }
        }
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Contact Info Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'con_info_title',
            [
                'label' => __('Contact Info title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'con_form_title',
            [
                'label' => __('Contact form title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        if (class_exists('WPCF7_ContactForm')) {
            $this->add_control(
                'search_box',
                [
                    'label' => __('Select Contact Form 7', 'careerfy-frame'),
                    'type' => Controls_Manager::SELECT2,
                    'options' => $cf7_arr,
                ]
            );
        }

        $this->add_control(
            'con_desc',
            [
                'label' => __('Description', 'careerfy-frame'),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );

        $this->add_control(
            'con_address',
            [
                'label' => __('Address', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'con_email',
            [
                'label' => __('Email', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'con_phone',
            [
                'label' => __('Phone', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'con_fax',
            [
                'label' => __('Fax', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'soc_icon', [
                'label' => __('Social Icon', 'careerfy-frame'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'soc_link', [
                'label' => __('Social Link', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                'default' => __(get_site_url(), 'careerfy-frame'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'social_links',
            [
                'label' => __('Add Social Links', 'careerfy-frame'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ soc_icon["value"] }}}',
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();

        extract(shortcode_atts(array(
            'con_info_title' => '',
            'con_form_title' => '',
            'con_form_7' => '',
            'con_desc' => '',
            'con_address' => '',
            'con_email' => '',
            'con_phone' => '',
            'con_fax' => '',
            'search_box' => '',
        ), $atts));

        ob_start();
        ?>
        <div class="careerfy-contact-info-sec">
            <?php if ($con_info_title != '') { ?>
                <h2><?php echo($con_info_title) ?></h2>
                <?php
            }
            if ($con_desc != '') { ?>
                <p><?php echo($con_desc) ?></p>
            <?php } ?>
            <ul class="careerfy-contact-info-list">
                <?php if ($con_address != '') { ?>
                    <li><i class="careerfy-icon careerfy-map-marker"></i> <?php echo($con_address) ?></li>
                    <?php
                }
                if ($con_email != '') { ?>
                    <li><i class="careerfy-icon careerfy-envelope"></i> <a
                                href="mailto:<?php echo($con_email) ?>"><?php printf(esc_html__('Email: %s', 'careerfy-frame'), $con_email) ?></a>
                    </li>
                    <?php
                }
                if ($con_phone != '') { ?>
                    <li>
                        <i class="careerfy-icon careerfy-technology"></i> <?php printf(esc_html__('Call: %s', 'careerfy-frame'), $con_phone) ?>
                    </li>
                    <?php
                }
                if ($con_fax != '') { ?>
                    <li>
                        <i class="careerfy-icon careerfy-fax"></i> <?php printf(esc_html__('Fax: %s', 'careerfy-frame'), $con_fax) ?>
                    </li>
                <?php } ?>
            </ul>

            <?php
            $social_links = isset($atts['social_links']) ? $atts['social_links'] : '';
            if (!empty($social_links)) { ?>
                <div class="careerfy-contact-media">
                    <?php
                    foreach ($social_links as $social_link) {
                        $soc_icon = isset($social_link['soc_icon']) ? $social_link['soc_icon']['value'] : '';
                        $soc_link = isset($social_link['soc_link']) ? $social_link['soc_link'] : '';
                        if ($soc_icon != '') { ?>
                            <a href="<?php echo($soc_link) ?>">
                                <i class="<?php echo($soc_icon) ?>"></i>
                            </a>
                            <?php
                        }
                    } ?>
                </div>
            <?php } ?>
        </div>
        <div class="careerfy-contact-form">
            <?php
            if ($con_form_title != '') { ?>
                <h2><?php echo($con_form_title) ?></h2>
                <?php
            }
            $cnt_counter = rand(1000000, 99999999);
            if (class_exists('WPCF7_ContactForm') && $search_box != '') {
                $con_form_7_id = careerfy__get_post_id($search_box, 'wpcf7_contact_form');
                echo do_shortcode('[contact-form-7 id="' . $con_form_7_id . '" title="' . get_the_title($con_form_7_id) . '"]');
            } else {
                ob_start();
                ?>
                <form id="ct-form-<?php echo absint($cnt_counter) ?>"
                      data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')) ?>" method="post">
                    <ul>
                        <li>
                            <input type="text" name="u_name"
                                   placeholder="<?php esc_html_e('Enter Your Name', 'careerfy-frame') ?>">
                            <i class="careerfy-icon careerfy-user"></i>
                        </li>
                        <li>
                            <input placeholder="<?php esc_html_e('Subject', 'careerfy-frame') ?>" type="text"
                                   name="u_subject">
                            <i class="careerfy-icon careerfy-user"></i>
                        </li>
                        <li>
                            <input placeholder="<?php esc_html_e('Enter Your Email Address', 'careerfy-frame') ?>"
                                   type="text" name="u_email">
                            <i class="careerfy-icon careerfy-mail"></i>
                        </li>
                        <li>
                            <input placeholder="<?php esc_html_e('Enter Your Phone Number', 'careerfy-frame') ?>"
                                   type="text" name="u_number">
                            <i class="careerfy-icon careerfy-technology"></i>
                        </li>
                        <li class="careerfy-contact-form-full">
                            <textarea name="u_msg"
                                      placeholder="<?php esc_html_e('Enter Your Message', 'careerfy-frame') ?>">
                            </textarea>
                        </li>
                        <li>
                            <input type="submit" class="careerfy-ct-form" data-id="<?php echo absint($cnt_counter) ?>"
                                   value="<?php esc_html_e('Submit', 'careerfy-frame') ?>">
                            <span class="careerfy-bt-msg careerfy-ct-msg"></span>
                            <input type="hidden" name="u_type" value="content"/>
                        </li>
                    </ul>
                </form>
                <?php
                $contct_html = ob_get_clean();
                echo apply_filters('careerfy_contactinf_sh_form_html', $contct_html);
            }
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