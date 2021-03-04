<?php
/**
 * Section Heading Shortcode
 * @return html
 */
add_shortcode('careerfy_app_promo', 'careerfy_app_promo_shortcode');

function careerfy_app_promo_shortcode($atts)
{
    extract(shortcode_atts(array(
        'h_title' => '',
        'hc_title_clr' => '',
        'h_desc' => '',
        'desc_clr' => '',
        'link_text' => '',
        'link_text_url' => '',
        'careerfy_browse_img_1' => '',
        'first_img_link' => '',
        'careerfy_browse_img_2' => '',
        'second_img_link' => '',
        'careerfy_browse_img_3' => '',
        'third_img_link' => '',
        'careerfy_browse_img_4' => '',
        'fourth_img_link' => '',
        'pckg_features' => '',
        'app_promo_view' => 'view1'
    ), $atts));

    ob_start();

    $h_title = $h_title != '' ? $h_title : "";
    $hc_title_clr = $hc_title_clr != "" ? 'style="color: ' . $hc_title_clr . '"' : "";
    $h_desc = $h_desc != '' ? $h_desc : "";
    $link_text = $link_text != '' ? $link_text : "";
    $link_text_url = $link_text_url != '' ? $link_text_url : "#";
    $desc_clr = $desc_clr != "" ? 'style="color: ' . $desc_clr . '"' : "";
    $careerfy_browse_img_1 = $careerfy_browse_img_1 != "" ? '<img src="' . $careerfy_browse_img_1 . '" >' : "";
    $first_img_link = $first_img_link != '' ? $first_img_link : "#";
    $careerfy_browse_img_2 = $careerfy_browse_img_2 != "" ? '<img src="' . $careerfy_browse_img_2 . '" >' : "";
    $second_img_link = $second_img_link != '' ? $second_img_link : "#";
    $careerfy_browse_img_3 = $careerfy_browse_img_3 != "" ? '<img src="' . $careerfy_browse_img_3 . '" >' : "";
    $third_img_link = $third_img_link != '' ? $third_img_link : "#";

    if ($app_promo_view == 'view3') { ?>
        <div class="careerfy-searchjob-text">
            <h2 <?php echo $hc_title_clr ?>><?php echo $h_title ?></h2>
            <?php
        if (function_exists('vc_param_group_parse_atts')) {
            $pckg_features = vc_param_group_parse_atts($pckg_features);
        }
            if (!empty($pckg_features)) { ?>
                <ul>
                    <?php
                    foreach ($pckg_features as $pckg_feature) {
                        $pckg_feat_name = isset($pckg_feature['feat_name']) ? $pckg_feature['feat_name'] : '';
                        ?>
                        <li><img src="<?php echo get_template_directory_uri() ?>/images/search-text-icon.png"
                                 alt=""><?php echo($pckg_feat_name) ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>
            <span <?php echo $desc_clr ?>><?php echo $h_desc ?></span>
            <div class="careerfy-featured-rating"><span class="careerfy-featured-rating-box"
                                                        style="width: 100%;"></span></div>
            <strong><?php echo esc_html__('Download the job app now', 'careerfy-frame') ?></strong>
            <a href="<?php echo $first_img_link ?>"
               class="careerfy-searchjob-app"><?php echo $careerfy_browse_img_1 ?></a>
            <a href="<?php echo $second_img_link ?>"
               class="careerfy-searchjob-app"><?php echo $careerfy_browse_img_2 ?></a>
        </div>
    <?php } else if ($app_promo_view == 'view1') { ?>

        <div class="careerfy-getapp">
            <h2 <?php echo $hc_title_clr ?>><?php echo $h_title ?></h2>
            <p <?php echo $desc_clr ?>><?php echo $h_desc ?></p>
            <div class="clearfix"></div>
            <a href="<?php echo $first_img_link ?>" class="careerfy-getapp-btn"><?php echo $careerfy_browse_img_1 ?></a>

            <a href="<?php echo $second_img_link ?>"
               class="careerfy-getapp-btn"><?php echo $careerfy_browse_img_2 ?></a>
        </div>

    <?php } else { ?>
        <div class="careerfy-autojobs-mobile-text">
            <h2 <?php echo $hc_title_clr ?>><?php echo $h_title ?></h2>
            <p <?php echo $desc_clr ?>><?php echo $h_desc ?></p>
            <a <?php echo $hc_title_clr ?> href="<?php echo $link_text_url ?>"
                                           class="careerfy-autojobs-mobile-btn"><?php echo $link_text ?></a>
            <div class="clearfix"></div>
            <a href="<?php echo $first_img_link ?>"
               class="careerfy-autojobs-mobile-thumb"><?php echo $careerfy_browse_img_1 ?></a>
            <a href="<?php echo $second_img_link ?>"
               class="careerfy-autojobs-mobile-thumb"><?php echo $careerfy_browse_img_2 ?></a>
            <a href="<?php echo $third_img_link ?>"
               class="careerfy-autojobs-mobile-thumb"><?php echo $careerfy_browse_img_3 ?></a>
        </div>
    <?php }

    $html = ob_get_clean();

    return $html;
}
