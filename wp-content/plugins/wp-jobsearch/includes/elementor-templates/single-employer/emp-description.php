<?php

namespace Wp_JobsearchElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH'))
    exit;

/**
 * @since 1.1.0
 */
class SingleEmpDescription extends Widget_Base
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
        return 'single-emp-description';
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
        return __('Single Employer Description', 'wp-jobsearch');
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
            'section_style',
            [
                'label' => __('Style', 'elementor-pro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => __('Alignment', 'elementor-pro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'elementor-pro'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'elementor-pro'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'elementor-pro'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'elementor-pro'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'elementor-pro'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => 'color: {{VALUE}};',
                ],
                'global' => [
                    'default' => Global_Colors::COLOR_TEXT,
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        global $post;
        $employer_id = is_admin() ? jobsearch_employer_id_elementor() : $post->ID;
        $emp_obj = get_post($employer_id);
        $emp_content = isset($emp_obj->post_content) ? $emp_obj->post_content : '';

        if (!is_admin()) {
            $emp_content = apply_filters('the_content', $emp_content);
        }
        ob_start();
        if ($emp_content != '') { ?>
            <div class="jobsearch-description">
                <?php echo jobsearch_esc_wp_editor($emp_content); ?>
            </div>
            <?php
        }
        $html = ob_get_clean();
        echo $html;
    }

    protected function _content_template()
    { ?>
        <div class="jobsearch-description">
            <h4><strong>His room, a proper human room although a little too small, lay peacefully between its four
                    familiar walls.</strong></h4>
            <p>One morning, when Gregor Samsa woke from troubled dreams, he found himself transformed in his bed into a
                horrible vermin. He lay on his armour-like back, and if he lifted his head a little he could see his
                brown belly, slightly domed and divided by arches into stiff sections.</p>
            <p>The bedding was hardly able to cover it and seemed ready to slide off any moment. His many legs,
                pitifully thin compared with the size of the rest of him, waved about helplessly as he looked. “What’s
                happened to me? ” he thought. It wasn’t a dream.</p>
            <p>collection of textile samples lay spread out on the table – Samsa was a travelling salesman – and above
                it there hung a picture that he had recently cut out of an illustrated magazine and housed in a nice,
                gilded frame. It showed a lady fitted out with a fur hat and fur boa who sat upright, raising a heavy
                fur muff that covered the whole of her lower arm towards the viewer. Gregor then turned to look out the
                window at the dull weather.</p>
            <blockquote><p>Hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla
                    facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zril
                    delenit augue duis dolore te feugait nulla facilisi. Nam liber tempor cum soluta.</p></blockquote>
            <p>Diplomatic far the indubitable hey much one wept lynx much scowled but interminable via jeeringly this
                eclectic overpaid after much in a much darn until shed disconsolately gosh and this saucily hence and
                wildebeest some astride the excepting more tentative past to in nosy raffishly incongruously ouch yikes
                the more. Clapped panda absolutely parrot then crab rode while smartly much darkly in capable piously
                more misheard excluding along that far a wherever grizzly</p>
            <p>Scurrilously much wow bore ravingly and darn as goodness much fox rueful gosh swore labrador bald gull
                grew some but the in strict rueful rosily wow this baneful well hotly that. Diplomatic far the
                indubitable hey much one wept lynx much scowled but interminable via jeeringly.</p>
        </div>
    <?php }

}
