<?php

namespace WP_JobsearchCandElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use WP_Jobsearch\Candidate_Profile_Restriction;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleCandidateLogo extends Widget_Base {

    /**
     * Retrieve the widget name.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'single-candidate-logo';
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
    public function get_title() {
        return __('Single Candidate Logo', 'wp-jobsearch');
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
    public function get_icon() {
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
    public function get_categories() {
        return ['jobsearch-cand-single'];
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
    protected function _register_controls() {
        $this->start_controls_section(
                'content_section', [
            'label' => __('Candidate Logo Settings', 'wp-jobsearch'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );

        $this->add_control(
                'promote_tag', [
            'label' => __('Promote Profile Tag', 'wp-jobsearch'),
            'type' => Controls_Manager::SELECT2,
            'default' => 'yes',
            'options' => [
                'yes' => __('Yes', 'wp-jobsearch'),
                'no' => __('No', 'wp-jobsearch'),
            ],
                ]
        );
        $this->add_control(
                'urgent_tag', [
            'label' => __('Urgent Tag', 'wp-jobsearch'),
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

    protected function render() {
        global $post, $jobsearch_plugin_options;
        $candidate_id = is_admin() ? jobsearch_candidate_id_elementor() : $post->ID;
        $cand_profile_restrict = new Candidate_Profile_Restriction;

        $atts = $this->get_settings_for_display();

        extract(shortcode_atts(array(
            'promote_tag' => '',
            'urgent_tag' => '',
                        ), $atts));

        $user_def_avatar_url = jobsearch_candidate_img_url_comn($candidate_id);

        ob_start();
        ?>
        <div class="elementor-candsingle-logo">
            <?php
            if ($promote_tag = 'yes') {
                echo jobsearch_member_promote_profile_iconlab($candidate_id);
            }
            if ($urgent_tag = 'yes') {
                ob_start();
                echo jobsearch_cand_urgent_pkg_iconlab($candidate_id, 'cand_listv2');
                $urgnt_html = ob_get_clean();
                echo apply_filters('jobsearch_cand_urgent_pkg_iconlab_html', $urgnt_html, $candidate_id, 'cand_listv2');
            }
            if (!$cand_profile_restrict::cand_field_is_locked('profile_fields|profile_img', 'detail_page')) {?>
                <figure><img src="<?php echo ($user_def_avatar_url) ?>" alt=""></figure>
                <?php
            }
            ?>
        </div>
        <?php
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template() {
        
    }

}
