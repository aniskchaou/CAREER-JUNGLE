<?php
/**
 * Call to action Shortcode
 * @return html
 */
add_shortcode('careerfy_call_to_action', 'careerfy_call_to_action_shortcode');

function careerfy_call_to_action_shortcode($atts)
{
    extract(shortcode_atts(array(
        'view' => '',
        'cta_img' => '',
        'cta_title1' => '',
        'cta_title2' => '',
        'cta_title_small' => '',
        'cta_desc' => '',
        'btn_txt' => '',
        'btn_url' => '',
        'btn_txt_2' => '',
        'btn_url_2' => '',
    ), $atts));

    ob_start();

    $cont_col = 'col-md-12';
    if ($cta_img != '') {
        $cont_col = 'col-md-6';
    }
    $cta_title_small = $cta_title_small != "" ? $cta_title_small : "";
    if ($view == 'view-5') { ?>
        <div class="careerfy-sixteen-parallex careerfy-sixteen-parallex-two">
            <h2><?php echo $cta_title1 ?></h2>
            <span><?php echo $cta_desc ?></span>
            <a href="<?php echo $btn_url ?>"><?php echo $btn_txt ?></a>
            <a href="<?php echo $btn_url_2 ?>"><?php echo $btn_txt_2 ?></a>
        </div>
    <?php } else if ($view == 'view-4') { ?>
        <div class="careerfy-action-style11">
            <h2><?php echo $cta_title1 ?></h2>
            <p><?php echo $cta_title2 ?></p>
            <a href="<?php echo $btn_url ?>"><?php echo $btn_txt ?></a>
        </div>
    <?php } else if ($view == 'view-3') { ?>
        <div class="careerfy-build-action">
            <h2><?php echo $cta_title1 ?>
                <small><?php echo $cta_title_small ?></small>
            </h2>
            <a href="<?php echo $btn_url ?>"><?php echo $btn_txt ?></a>
        </div>
        <?php
    } else { ?>
        <div class="row">
            <aside class="<?php echo($cont_col) ?> careerfy-typo-wrap">
                <div class="careerfy-parallex-text <?php echo($view == 'view-2' ? 'careerfy-logo-text' : '') ?>">
                    <h2><?php echo($cta_title1) ?><?php echo($cta_title2 != '' ? '<br> ' . $cta_title2 : '') ?></h2>
                    <?php echo($cta_desc != '' ? '<p>' . $cta_desc . '</p>' : '') ?>
                    <?php echo($btn_txt != '' ? '<a href="' . $btn_url . '" class="careerfy-static-btn careerfy-bgcolor"><span>' . $btn_txt . '</span></a>' : '') ?>
                </div>
            </aside>
            <?php
            if ($cta_img != '') {
                ?>
                <aside class="col-md-6 careerfy-typo-wrap">
                    <div class="<?php echo($view == 'view-2' ? 'careerfy-logo-thumb' : 'careerfy-right') ?>"><img
                                src="<?php echo($cta_img) ?>" alt=""></div>
                </aside>
                <!--ends here-->
            <?php } ?>
        </div>
    <?php }
    $html = ob_get_clean();

    return $html;
}
