<?php

namespace Wp_JobsearchElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleEmpLogo extends Widget_Base
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
        return 'single-emp-logo';
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
        return __('Single Employer Logo', 'wp-jobsearch');
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
        return 'fa fa-link';
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
        return ['jobsearch-emp-single'];
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
            'content_section', [
                'label' => __('Employer Logo Settings', 'wp-jobsearch'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'featured_tag', [
                'label' => __('Featured Tag', 'wp-jobsearch'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes', 'wp-jobsearch'),
                    'no' => __('No', 'wp-jobsearch'),
                ],
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        global $post;
        $employer_id = is_admin() ? jobsearch_employer_id_elementor() : $post->ID;
        $atts = $this->get_settings_for_display();
        extract(shortcode_atts(array(
            'featured_tag' => '',

        ), $atts));
        $user_id = jobsearch_get_employer_user_id($employer_id);
        $user_def_avatar_url = get_avatar_url($user_id, array('size' => 140));
        $user_avatar_id = get_post_thumbnail_id($employer_id);
        if ($user_avatar_id > 0) {
            $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
            $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
        } else {
            $user_def_avatar_url = jobsearch_employer_image_placeholder();
        }

        ob_start();
        ?>
        <div class="elementor-sec-emplogo">
              <span class="">
                            <?php
                            if ($featured_tag == 'yes') {
                                echo jobsearch_member_promote_profile_iconlab($employer_id, 'employer_detv1');
                            }
                            ?>
                            <img src="<?php echo jobsearch_esc_html($user_def_avatar_url) ?>" alt="">
                        </span>
        </div>
        <?php
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    {

    }

}
