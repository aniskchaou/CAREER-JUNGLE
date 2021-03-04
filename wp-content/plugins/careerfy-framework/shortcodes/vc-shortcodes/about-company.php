<?php
/**
 * About Company Shortcode
 * @return html
 */
add_shortcode('careerfy_about_company', 'careerfy_about_company_shortcode');

function careerfy_about_company_shortcode($atts, $content = '')
{
    $about_param = array(
        'ab_view' => '',
        'title' => '',
        'bold_txt' => '',
        'about_img' => '',
        'title_color' => '',
        'desc_color' => '',
        'btn_txt' => '',
        'btn_url' => '',
    );

    $about_param = apply_filters('careerfy_about_company_fields_param', $about_param);

    extract(shortcode_atts($about_param, $atts));

    ob_start();

    if ($ab_view == 'view2') {
        $cont_col = 'col-md-12';
        if ($about_img != '') {
            $cont_col = 'col-md-7';
        }
        ?>
        <div class="row">
            <div class="<?php echo($cont_col) ?> careerfy-parallax-style4">
                <?php
                if ($title != '') { ?>
                    <span <?php echo($title_color != '' ? 'style="color: ' . $title_color . ';"' : '') ?>><?php echo($title) ?></span>
                    <?php
                }
                if ($bold_txt != '') { ?>
                    <h2 <?php echo($title_color != '' ? 'style="color: ' . $title_color . ';"' : '') ?>><?php echo($bold_txt) ?></h2>
                    <?php
                }
                ?>
                <p <?php echo($desc_color != '' ? 'style="color: ' . $desc_color . ';"' : '') ?>><?php echo($content) ?></p>
                <?php
                if ($btn_txt != '') { ?>
                    <a href="<?php echo($btn_url) ?>" class="careerfy-parallax-style4-btn"><?php echo($btn_txt) ?></a>
                <?php } ?>
            </div>
            <?php
            if ($about_img != '') { ?>
                <div class="col-md-5"><img src="<?php echo($about_img) ?>" alt=""></div>
            <?php } ?>
        </div>
        <?php
    } else {
        $cont_col = 'col-md-12';
        if ($about_img != '') {
            $cont_col = 'col-md-6';
        }
        ?>
        <div class="row">
            <div class="<?php echo($cont_col) ?> careerfy-typo-wrap">
                <div class="careerfy-about-text">
                    <?php
                    if ($title != '') { ?>
                        <h2 <?php echo($title_color != '' ? 'style="color: ' . $title_color . ';"' : '') ?>><?php echo($title) ?></h2>
                        <?php
                    }
                    if ($bold_txt != '') { ?>
                        <span class="careerfy-about-sub" <?php echo($title_color != '' ? 'style="color: ' . $title_color . ';"' : '') ?>><?php echo($bold_txt) ?></span>
                    <?php } ?>
                    <p <?php echo($desc_color != '' ? 'style="color: ' . $desc_color . ';"' : '') ?>><?php echo($content) ?></p>
                    <?php
                    if ($btn_txt != '') { ?>
                        <a href="<?php echo($btn_url) ?>"
                           class="careerfy-static-btn careerfy-bgcolor"><span><?php echo($btn_txt) ?></span></a>
                        <?php
                    }

                    do_action('careerfy_about_company_extra_button', $atts);

                    ?>
                </div>
            </div>
            <?php
            if ($about_img != '') {
                ?>
                <div class="col-md-6 careerfy-typo-wrap">
                    <div class="careerfy-about-thumb"><img src="<?php echo($about_img) ?>" alt=""></div>
                </div>
                <!--ends here-->
            <?php } ?>
        </div>
        <?php
    }
    $html = ob_get_clean();
    return $html;
}
    