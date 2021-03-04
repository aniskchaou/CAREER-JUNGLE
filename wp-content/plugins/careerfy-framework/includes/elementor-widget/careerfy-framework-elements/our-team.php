<?php

namespace CareerfyElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
if (!defined('ABSPATH')) exit;

/**
 * @since 1.1.0
 */
class OurTeam extends Widget_Base
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
        return 'our-team';
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
        return __('Our Team', 'careerfy-frame');
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
        return 'fa fa-users';
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
                'label' => __('Our Team Settings', 'careerfy-frame'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'team_style',
            [
                'label' => __('Style', 'careerfy-frame'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'style1',
                'options' => [
                    'style1' => __('Style 1', 'careerfy-frame'),
                    'style2' => __('Style 2', 'careerfy-frame'),
                ],
            ]
        );

        $this->add_control(
            'partner_title', [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'team_img', [
                'label' => __('Image', 'careerfy-frame'),
                'type' => Controls_Manager::MEDIA,
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'team_title', [
                'label' => __('Title', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $repeater->add_control(
            'team_pos', [
                'label' => __('Position', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $repeater->add_control(
            'team_experience', [
                'label' => __('Experience', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                "description" => __("Add experience in years without any year/month string. (Like:5.) Note: This Field depends on Team Style 2", "careerfy-frame"),
            ]
        );

        $repeater->add_control(
            'team_biography', [
                'label' => __('Biography', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                "description" => __("Note: This Field depends on Team Style 2", "careerfy-frame"),
            ]
        );

        $repeater->add_control(
            'team_fb', [
                'label' => __('Facebook', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                "description" => __("Note: This Field depends on Team Style 2", "careerfy-frame"),
            ]
        );

        $repeater->add_control(
            'team_google', [
                'label' => __('Google +', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                "description" => __("Note: This Field depends on Team Style 2", "careerfy-frame"),
            ]
        );

        $repeater->add_control(
            'team_twitter', [
                'label' => __('Twitter', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                "description" => __("Note: This Field depends on Team Style 2", "careerfy-frame"),
            ]
        );

        $repeater->add_control(
            'team_linkedin', [
                'label' => __('LinkedIn', 'careerfy-frame'),
                'type' => Controls_Manager::TEXT,
                "description" => __("Note: This Field depends on Team Style 2", "careerfy-frame"),
            ]
        );

        $this->add_control(
            'careerfy_our_team_item',
            [
                'label' => __('Our partner item', 'careerfy-frame'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => 'Item 1',
            ]
        );
        $this->end_controls_section();
    }

    protected function careerfy_our_team_item_shortcode()
    {
        $atts = $this->get_settings_for_display();
        global $team_style;

        $html = '';
        foreach ($atts['careerfy_our_team_item'] as $info) {
            $team_img = $info['team_img'] !='' ? $info['team_img']['url'] : '';
            $team_title = $info['team_title'] ;
            $team_pos = $info['team_pos'] ;
            $team_experience = $info['team_experience'] ;
            $team_biography = $info['team_biography'] ;
            $team_fb = $info['team_fb'] ;
            $team_google = $info['team_google'] ;
            $team_twitter = $info['team_twitter'] ;
            $team_linkedin = $info['team_linkedin'] ;

            if ($team_style == 'style2') {
                $rand_id = rand(1046, 78999);
                ob_start();
                ?>
                <li class="jobsearch-column-3">

                    <figure>
                        <a id="fancybox_notes<?php echo($rand_id); ?>" href="#notes<?php echo($rand_id); ?>"
                           class="jobsearch-candidate-grid-thumb">
                            <img src="<?php echo($team_img); ?>" alt="">
                        </a>
                        <figcaption>
                            <h2><a id="fancybox_notes_txt<?php echo($rand_id); ?>"
                                   href="#notes<?php echo($rand_id); ?>"><?php echo($team_title); ?></a></h2>
                            <?php if (isset($team_pos) && !empty($team_pos)) { ?>
                                <p><?php echo($team_pos); ?></p>
                                <?php
                            }
                            if (isset($team_experience) && !empty($team_experience)) {
                                ?>
                                <span><?php printf(esc_html__('Experience: %s Years', 'careerfy-frame'), $team_experience); ?></span>
                                <?php
                            }
                            ?>
                        </figcaption>
                    </figure>
                    <?php
                    if (isset($team_biography) && !empty($team_biography)) {
                        ?>
                        <div id="notes<?php echo($rand_id); ?>"
                             style="display: none;"><?php echo $team_biography ?></div>
                        <?php
                    }
                    if (!empty($team_fb) || !empty($team_google) || !empty($team_twitter) || !empty($team_linkedin)) {
                        ?>
                        <ul class="jobsearch-social-icons">
                            <?php
                            if (isset($team_fb) && !empty($team_fb)) {
                                ?>
                                <li><a href="<?php echo $team_fb ?>" data-original-title="facebook"
                                       class="jobsearch-icon jobsearch-facebook-logo"></a></li>
                                <?php
                            }
                            if (isset($team_google) && !empty($team_google)) {
                                ?>
                                <li><a href="<?php echo $team_google ?>" data-original-title="google-plus"
                                       class="jobsearch-icon jobsearch-google-plus-logo-button"></a></li>
                                <?php
                            }
                            if (isset($team_twitter) && !empty($team_twitter)) {
                                ?>
                                <li><a href="<?php echo $team_twitter ?>" data-original-title="twitter"
                                       class="jobsearch-icon jobsearch-twitter-logo"></a></li>
                                <?php
                            }
                            if (isset($team_linkedin) && !empty($team_linkedin)) {
                                ?>
                                <li><a href="<?php echo $team_linkedin ?>" data-original-title="linkedin"
                                       class="jobsearch-icon jobsearch-linkedin-button"></a></li>
                                <?php
                            }
                            ?>
                        </ul>
                    <?php }
                    ?>
                </li>
                <?php
                $html .= ob_get_contents();
                ob_end_clean();
            } else {
                $html .= '<div class="careerfy-service-slider-layer">';
                $html .= '<a><img src="' . $team_img . '" alt=""></a>';
                $html .= '<span>' . $team_title . ' <small>' . $team_pos . '</small></span>';
                $html .= '</div>';
            }
        }
        echo $html;
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        global $team_style;

        extract(shortcode_atts(array(
            'team_style' => '',
        ), $atts));

        wp_enqueue_script('careerfy-slick-slider');
        ob_start();
        if ($team_style == 'style2') { ?>
            <script>
                jQuery(document).ready(function () {
                    jQuery('a[id^="fancybox_notes"]').fancybox({
                        'titlePosition': 'inside',
                        'transitionIn': 'elastic',
                        'transitionOut': 'elastic',
                        'width': 400,
                        'height': 250,
                        'padding': 40,
                        'autoSize': false
                    });
                });
            </script>
            <div class="jobsearch-candidate jobsearch-candidate-grid team-style-two">
                <ul class="jobsearch-row">
                    <?php echo $this->careerfy_our_team_item_shortcode() ?>
                </ul>
            </div>

        <?php } else { ?>

            <div class="careerfy-service-slider">
                <?php echo $this->careerfy_our_team_item_shortcode() ?>
            </div>
        <?php }
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    {
    }
}