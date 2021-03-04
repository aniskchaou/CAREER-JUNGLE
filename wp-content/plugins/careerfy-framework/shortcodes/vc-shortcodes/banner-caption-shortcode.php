<?php
/**
 * Call to action Shortcode
 * @return html
 */

add_shortcode('careerfy_banner_caption', 'careerfy_banner_caption_shortcode');

function careerfy_banner_caption_shortcode($atts)
{
    extract(shortcode_atts(array(
        'banner_title' => '',
        'banner_desc' => '',
        'btn_heading' => '',
        'banner_btn' => '',

    ), $atts));

    ob_start(); ?>
    <div class="careerfy-thirteen-banner-caption">
        <div class="container">
            <?php if (!empty($banner_title)) { ?>
                <h1><?php echo $banner_title ?></h1>
            <?php } ?>
            <br>
            <?php if (!empty($banner_desc)) { ?>
                <div class="banner-desc">
                    <?php echo $banner_desc ?>
                </div>
            <?php } ?>
            <?php if (!empty($btn_heading)) { ?>
                <h2><?php echo $btn_heading ?></h2>
            <?php } ?>
            <div class="clearfix"></div>
            <div class="careerfy-thirteen-banner-btn">
                <?php
                if (function_exists('vc_param_group_parse_atts')) {
                    $banner_btn = vc_param_group_parse_atts($banner_btn);
                }
                if (!empty($banner_btn) || !empty($banner_btn)) {
                    foreach ($banner_btn as $banner_btns) {

                        ?>
                        <a href="<?php echo $banner_btns['btn_link'] ?>"><?php echo($banner_btns['btn_txt']) ?></a>
                        <?php
                    }

                } ?>

            </div>
        </div>
    </div>

    <?php $html = ob_get_clean();

    return $html;
}
