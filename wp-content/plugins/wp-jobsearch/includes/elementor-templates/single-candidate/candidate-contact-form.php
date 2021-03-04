<?php

namespace WP_JobsearchCandElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use WP_Jobsearch\Candidate_Profile_Restriction;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;


if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleCandidateContact extends Widget_Base
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
        return 'single-candidate-contact';
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
        return __('Single Candidate Contact', 'wp-jobsearch');
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
    protected function _register_controls()
    {
        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Candidate Form Styles', 'wp-jobsearch'),
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
                'selector' => '{{WRAPPER}} .jobsearch_box_contact_form ul li label',
            ]
        );

        $this->add_control(
            'heading_text_color',
            [
                'label' => __('Heading Color', 'wp-jobsearch'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .jobsearch_box_contact_form ul li label' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __('Icon Color', 'wp-jobsearch'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .jobsearch_box_contact_form ul li i' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        global $post, $jobsearch_plugin_options;
        $candidate_id = is_admin() ? jobsearch_candidate_id_elementor() : $post->ID;
        $cand_profile_restrict = new Candidate_Profile_Restriction;
        $user_id = jobsearch_get_candidate_user_id($candidate_id);
        ob_start();
        $cand_det_contact_form = isset($jobsearch_plugin_options['cand_det_contact_form']) ? $jobsearch_plugin_options['cand_det_contact_form'] : '';
        if ($cand_det_contact_form == 'on') {
            if (!$cand_profile_restrict::cand_field_is_locked('contactfrm_defields', 'detail_page')) {
                $captcha_switch = isset($jobsearch_plugin_options['captcha_switch']) ? $jobsearch_plugin_options['captcha_switch'] : '';
                $jobsearch_sitekey = isset($jobsearch_plugin_options['captcha_sitekey']) ? $jobsearch_plugin_options['captcha_sitekey'] : '';
                ob_start();
                ?>
                <div class="jobsearch_side_box jobsearch_box_contact_form">
                    <?php
                    $cnt_counter = rand(1000000, 9999999);

                    $cnt__cand_wout_log = isset($jobsearch_plugin_options['cand_cntct_wout_login']) ? $jobsearch_plugin_options['cand_cntct_wout_login'] : '';

                    $cur_user_name = '';
                    $cur_user_email = '';
                    $field_readonly = false;
                    if (is_user_logged_in()) {
                        if ($cnt__cand_wout_log != 'on') {
                            $field_readonly = true;
                        }
                        $cur_user_id = get_current_user_id();
                        $cur_user_obj = wp_get_current_user();
                        $cur_user_name = isset($cur_user_obj->display_name) ? $cur_user_obj->display_name : '';
                        $cur_user_email = isset($cur_user_obj->user_email) ? $cur_user_obj->user_email : '';
                        if (jobsearch_user_is_employer($cur_user_id)) {
                            $cnt_emp_id = jobsearch_get_user_employer_id($cur_user_id);
                            $cur_user_name = get_the_title($cnt_emp_id);
                        }
                    }
                    ?>

                    <form id="ct-form-<?php echo absint($cnt_counter) ?>"
                          data-uid="<?php echo absint($user_id) ?>" method="post">
                        <ul>
                            <li>
                                <label><?php esc_html_e('Name:', 'wp-jobsearch') ?></label>
                                <input name="u_name"
                                       placeholder="<?php esc_html_e('Enter Your Name', 'wp-jobsearch') ?>"
                                       type="text" <?php echo($field_readonly ? 'readonly' : '') ?>
                                       value="<?php echo($cur_user_name) ?>">
                                <i class="jobsearch-icon jobsearch-user"></i>
                            </li>
                            <li>
                                <label><?php esc_html_e('Email Address:', 'wp-jobsearch') ?></label>
                                <input name="u_email"
                                       placeholder="<?php esc_html_e('Enter Your Email Address', 'wp-jobsearch') ?>"
                                       type="text" <?php echo($field_readonly ? 'readonly' : '') ?>
                                       value="<?php echo($cur_user_email) ?>">
                                <i class="jobsearch-icon jobsearch-mail"></i>
                            </li>
                            <li>
                                <label><?php esc_html_e('Phone Number:', 'wp-jobsearch') ?></label>
                                <input name="u_number"
                                       placeholder="<?php esc_html_e('Enter Your Phone Number', 'wp-jobsearch') ?>"
                                       type="text">
                                <i class="jobsearch-icon jobsearch-technology"></i>
                            </li>
                            <li>
                                <label><?php esc_html_e('Message:', 'wp-jobsearch') ?></label>
                                <textarea name="u_msg"
                                          placeholder="<?php esc_html_e('Type Your Message here', 'wp-jobsearch') ?>"></textarea>
                            </li>
                            <?php
                            if ($captcha_switch == 'on') {
                                wp_enqueue_script('jobsearch_google_recaptcha');
                                ?>
                                <li>
                                    <script>
                                        var recaptcha_cand_contact;
                                        var jobsearch_multicap = function () {
                                            //Render the recaptcha_cand_contact on the element with ID "recaptcha1"
                                            recaptcha_cand_contact = grecaptcha.render('recaptcha_cand_contact', {
                                                'sitekey': '<?php echo($jobsearch_sitekey); ?>', //Replace this with your Site key
                                                'theme': 'light'
                                            });
                                        };
                                        jQuery(document).ready(function () {
                                            jQuery('.recaptcha-reload-a').click();
                                        });
                                    </script>
                                    <div class="recaptcha-reload" id="recaptcha_cand_contact_div">
                                        <?php echo jobsearch_recaptcha('recaptcha_cand_contact'); ?>
                                    </div>
                                </li>
                            <?php } ?>
                            <li>
                                <?php
                                jobsearch_terms_and_con_link_txt();
                                ?>
                                <input type="submit" class="jobsearch-candidate-ct-form"
                                       data-id="<?php echo absint($cnt_counter) ?>"
                                       value="<?php esc_html_e('Send now', 'wp-jobsearch') ?>">
                                <?php
                                if (!is_user_logged_in() && $cnt__cand_wout_log != 'on') {
                                    ?>
                                    <a class="jobsearch-open-signin-tab"
                                       style="display: none;"><?php esc_html_e('login', 'wp-jobsearch') ?></a>
                                    <?php
                                }
                                ?>
                            </li>
                        </ul>
                        <span class="jobsearch-ct-msg"></span>
                    </form>
                </div>
                <?php
                $cand_cntct_form = ob_get_clean();
                echo apply_filters('jobsearch_candidate_detail_cntct_frm_html', $cand_cntct_form, $candidate_id);
            }
        }
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    { ?>
        <div class="jobsearch_side_box jobsearch_box_contact_form">
            <form id="ct-form-8225506" method="post">
                <ul>
                    <li>
                        <label><?php echo esc_html__('Name:','wp-jobsearch') ?></label>
                        <input name="u_name" placeholder="<?php echo esc_html__('Enter Your Name','wp-jobsearch') ?>" type="text" value="">
                        <i class="jobsearch-icon jobsearch-user"></i>
                    </li>
                    <li>
                        <label><?php echo esc_html__('Email Address:','wp-jobsearch') ?></label>
                        <input name="u_email" placeholder="<?php echo esc_html__('Enter Your Email Address','wp-jobsearch') ?>" type="text" value="">
                        <i class="jobsearch-icon jobsearch-mail"></i>
                    </li>
                    <li>
                        <label><?php echo esc_html__('Phone Number:','wp-jobsearch') ?></label>
                        <input name="u_number" placeholder="<?php echo esc_html__('Enter Your Phone Number','wp-jobsearch') ?>" type="text">
                        <i class="jobsearch-icon jobsearch-technology"></i>
                    </li>
                    <li>
                        <label><?php echo esc_html__('Message:','wp-jobsearch') ?></label>
                        <textarea name="u_msg" placeholder="<?php echo esc_html__('Type Your Message here','wp-jobsearch') ?>"></textarea>
                    </li>
                    <li>
                        <div class="terms-priv-chek-con">
                            <p><input type="checkbox" name="terms_cond_check"><?php echo esc_html__('By clicking checkbox, you agree to our','wp-jobsearch') ?><a
                                        href="http://dev.com/careerfy/terms-and-conditions/"><?php echo esc_html__('Terms and Conditions','wp-jobsearch') ?></a>
                                <?php echo esc_html__('and','wp-jobsearch') ?> <a href="http://dev.com/careerfy/privacy-policy/"><?php echo esc_html__('Privacy Policy','wp-jobsearch') ?></a></p>
                        </div>
                        <input type="submit" class="jobsearch-candidate-ct-form" data-id="8225506" value="<?php echo esc_html__('Send now','wp-jobsearch') ?>">

                    </li>
                </ul>
                <span class="jobsearch-ct-msg"></span>
            </form>
        </div>
    <?php
    }

}
