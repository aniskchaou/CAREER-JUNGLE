<?php
/**
 * Simple Jobs Listing Shortcode
 * @return html
 */
if (!defined('ABSPATH')) {
    die;
}

// main plugin class
class JobSearch_Careerfy_How_It_Works
{
    public function __construct()
    {
        add_shortcode('jobsearch_how_it_works_shortcode', array($this, 'how_it_works_shortcode'));
    }

    public function how_it_works_shortcode($atts)
    {
        global $view;
        ob_start();
        $parent_class = 'careerfy-howit-works-list';
        ?>
        <div class="<?php echo $parent_class ?>">
            <ul class="row">
                <?php echo self::HowItWorksList($atts); ?>
            </ul>
        </div>

        <?php $html = ob_get_clean();
        return $html;
    }

    private static function HowItWorksList($atts)
    {
        global $view;
        extract(shortcode_atts(array(
            'step_1_image' => '',
            'step_1_image_desc' => '',
            'step_1_title' => '',
            'step_1_desc' => '',
            'step_2_image' => '',
            'step_2_icon' => '',
            'step_2_image_desc' => '',
            'step_2_title' => '',
            'step_2_desc' => '',
            'step_2_icon_color' => '',
            'step_3_opts' => '',
            'step_3_title' => '',
            'step_3_desc' => '',
            'step_4_image' => '',
            'step_4_title' => '',
            'step_4_desc' => '',
            'step_4_icon' => '',
            'step_4_icon_color' => '',
        ), $atts));
        $step_1_image = $step_1_image != "" ? $step_1_image : jobsearch_employer_image_placeholder();
        $step_2_image = $step_2_image != "" ? $step_2_image : jobsearch_employer_image_placeholder();
        $step_4_image = $step_4_image != "" ? $step_4_image : jobsearch_employer_image_placeholder();

        $carrerfy_step_2_icon_color = '';
        if ($step_2_icon_color != "") {
            $carrerfy_step_2_icon_color = 'style="color: ' . $step_2_icon_color . '"';
        }

        ob_start();

        if (!empty($step_1_title)) {
            ?>
            <li class="col-md-12">
                <figure>
                    <a href="#"><img src="<?php echo esc_url($step_1_image) ?>" alt=""></a>
                    <figcaption>
                        <span><?php echo $step_1_image_desc ?></span>
                    </figcaption>
                </figure>
                <div class="careerfy-howit-works-text">
                    <small><?php echo esc_html__('01', 'careerfy-frame') ?></small>
                    <span><?php echo $step_1_title ?></span>
                    <p><?php echo $step_1_desc ?></p>
                </div>
            </li>
        <?php } ?>
        <?php if ($step_2_title != "") { ?>
        <li class="col-md-12 flip-list">
            <figure>
                <a href="#"><img src="<?php echo $step_2_image ?>" alt=""></a>
                <figcaption>
                    <span><i class="careerfy-icon <?php echo $step_2_icon ?>" <?php echo $carrerfy_step_2_icon_color ?>></i><?php echo esc_html__($step_2_image_desc) ?></span>
                </figcaption>
            </figure>
            <div class="careerfy-howit-works-text">
                <small><?php echo esc_html__('02', 'careerfy-frame') ?></small>
                <span><?php echo $step_2_title ?></span>
                <p><?php echo $step_2_desc ?></p>
            </div>
        </li>
    <?php } ?>
        <?php if ($step_3_title != "") { ?>
        <li class="col-md-12 careerfy-spam-list">
            <figure>
                <figcaption>
                    <?php
        if (function_exists('vc_param_group_parse_atts')) {
            $step_3_options = vc_param_group_parse_atts($step_3_opts);
        }
                    foreach ($step_3_options as $options) {
                        $icon_color = isset($options['step_3_icon_color']) && $options['step_3_icon_color'] != "" ? 'style="color: ' . $options['step_3_icon_color'] . '"  ' : '';
                        $option_status = isset($options['step_3_checked_1']) && $options['step_3_checked_1'] == "yes" ? '<em class="careerfy-icon careerfy-checked"></em>' : '<em class="fa fa-times"></em>';
                        ?>
                        <span><i class="careerfy-icon-ten <?php echo $options['step_3_icon'] ?>"  <?php echo $icon_color ?>></i> <?php echo $options['step_3_title'] ?> <?php echo $option_status ?></span><br/>
                    <?php } ?>
                </figcaption>
            </figure>
            <div class="careerfy-howit-works-text">
                <small><?php echo esc_html__('03', 'careerfy-frame') ?></small>
                <?php if ($step_3_title != "") { ?>
                    <span><?php echo $step_3_title ?></span>
                <?php } ?>
                <?php if ($step_3_desc != "") { ?>
                    <p><?php echo $step_3_desc ?></p>
                <?php } ?>
            </div>
        </li>
    <?php } ?>
        <?php if ($step_4_title != "") { ?>
        <li class="col-md-12 flip-list">
            <figure>
                <a href="#"><img src="<?php echo $step_4_image ?>" alt=""></a>
            </figure>
            <div class="careerfy-howit-works-text">
                <small><?php echo esc_html__('04', 'careerfy-frame'); ?></small>
                <span><?php echo $step_4_title ?></span>
                <p><?php echo $step_4_desc ?></p>
            </div>
        </li>
    <?php } ?>
        <?php
        $html = ob_get_clean();
        return $html;
    }
}
return new JobSearch_Careerfy_How_It_Works();