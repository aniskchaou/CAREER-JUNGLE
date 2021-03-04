<?php
/**
 * Section Heading Shortcode
 * @return html
 */
add_shortcode('careerfy_section_heading', 'careerfy_section_heading_shortcode');

function careerfy_section_heading_shortcode($atts)
{
    extract(shortcode_atts(array(
        'view' => '',
        'h_fancy_title' => '',
        'h_title' => '',
        'hc_title' => '',
        'hc_title_clr' => '',
        'hc_icon' => '',
        'h_desc' => '',
        'hc_dcolor' => '',
        's_title' => '',
        'num_title' => '',
        's_title_clr' => '',
        'desc_clr' => '',
        'proc_num_clr' => '',
        'css' => '',
        'heading_img' => '',
        'text_align' => '',
    ), $atts));

    ob_start();

    $design_css_class = '';
    if (function_exists('vc_shortcode_custom_css_class')) {
        $design_css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($css, ' '), '', $atts);
    }

    $title_colr_style = '';
    if ($hc_title_clr != '') {
        $title_colr_style = ' style="color: ' . $hc_title_clr . ';"';
    }
    $random_id = rand();
    $desc_colr_style = '';
    if ($hc_dcolor != '') {
        $desc_colr_style = ' style="color: ' . $hc_dcolor . ';"';
    }

    $hdng_con = 'section';
    $hdng_class = 'careerfy-fancy-title';
    if ($view == 'view2') {
        $hdng_class = 'careerfy-fancy-title careerfy-fancy-title-two';
        $hdng_con = 'div';
    } else if ($view == 'view3') {
        $hdng_class = 'careerfy-fancy-title careerfy-fancy-title-three';
        $hdng_con = 'div';
    } else if ($view == 'view4') {
        $hdng_class = 'careerfy-fancy-title careerfy-fancy-title-four';
        $hdng_con = 'div';
    } else if ($view == 'view5') {
        $hdng_class = 'careerfy-fancy-title careerfy-fancy-title-six';
        $hdng_con = 'div';
    } else if ($view == 'view6') {
        $hdng_class = 'careerfy-fancy-title-nine';
        $s_title_clr = $s_title_clr != "" ? $s_title_clr : '';
        $num_title = $num_title != "" ? $num_title : '';
        $h_title = $h_title != "" ? $h_title : '';
        $h_desc = $h_desc != "" ? $h_desc : '';
        $s_title = $s_title != "" ? $s_title : '';
        $proc_num_clr = $proc_num_clr != "" ? $proc_num_clr : '';
        $hc_title_clr = $hc_title_clr != "" ? 'style="color: ' . $hc_title_clr . '"' : '';
        $desc_clr = $desc_clr != "" ? 'style="color: ' . $desc_clr . '"' : '';

    } else if ($view == 'view7') {
        $hdng_class = 'careerfy-fancy-title-ten';
        $hdng_con = 'div';
        $text_align = $text_align != "" ? $text_align : "center";
        $content_align = "";
        if ($text_align == "left") {
            $content_align = 'careerfy-fancy-title-ten-left';
        }
    } else if ($view == 'view8') {
        $hdng_class = 'careerfy-fancy-title-eleven';
        $hdng_con = 'div';
        $text_align = $text_align != "" ? $text_align : "center";
        $content_align = "";
        if ($text_align == "left") {
            $content_align = 'careerfy-fancy-title-eleven-left';
        }
        $heading_img = $heading_img != "" ? '<img src="' . $heading_img . '">' : "";
    } else if ($view == 'view9') {
        $hdng_class = 'careerfy-fancy-title-twelve';
    } else if ($view == 'view10') {
        $hdng_class = 'careerfy-fancy-title-thirteen';
    } else if ($view == 'view11') {
        $hdng_class = 'careerfy-fancy-title-fourteen';
    } else if ($view == 'view12') {
        $hdng_class = 'careerfy-fancy-title-fifteen';
    } else if ($view == 'view13') {
        $hdng_class = 'careerfy-fancy-title-sixteen';
    } else if ($view == 'view14') {
        $hdng_class = 'careerfy-fancy-title-seventeen';
    } else if ($view == 'view15') {
        $hdng_class = 'careerfy-fancy-title-eighteen';
        $heading_img = $heading_img != "" ? '<img src="' . $heading_img . '">' : "";
    } else if ($view == 'view16') {
        $desc_clr = $desc_clr != "" ? 'style="color: ' . $desc_clr . '"' : '';
        $hdng_class = 'careerfy-fancy-title-nineteen';
    } else if ($view == 'view17') {
        $hdng_class = 'careerfy-fancy-title-twenty';
        $align_heading_class = $text_align != '' && $text_align == 'left' ? 'text-align-left' : '';
    } else if ($view == 'view18') {
        $hdng_class = 'careerfy-fancy-title-twentyone';
        $align_heading_class = $text_align != '' && $text_align == 'left' ? 'text-align-left' : '';
        $s_title_clr = $s_title_clr != "" ? 'style="color: ' . $s_title_clr . '"'  : '';
    }
    if ($view == 'view18') { ?>

        <div class="<?php echo $hdng_class ?> <?php echo $align_heading_class ?><?php echo($design_css_class) ?>">
            <small <?php echo $s_title_clr ?>><?php echo $s_title ?></small>
            <h2 <?php echo $title_colr_style ?>><?php echo $h_title ?></h2>
            <span <?php echo $desc_colr_style ?>><?php echo $h_desc ?></span>
        </div>
    <?php } else if ($view == 'view17') { ?>

        <div class="<?php echo $hdng_class ?> <?php echo $align_heading_class ?><?php echo($design_css_class) ?>">
            <h2 <?php echo $title_colr_style ?>><?php echo $h_title ?></h2>
            <span <?php echo $desc_colr_style ?>><?php echo $h_desc ?></span>
        </div>
    <?php } else if ($view == 'view16') { ?>
        <style>
            .careerfy-fancy-title-nineteen h2:before {
                background-color: <?php echo $hc_title_clr ?>;
            }
        </style>
        <div class="<?php echo $hdng_class ?><?php echo($design_css_class) ?>">
            <span <?php echo $desc_colr_style ?>><?php echo $h_desc ?></span>
            <h2 <?php echo $title_colr_style ?>><?php echo $h_title ?></h2>
        </div>
    <?php } else if ($view == 'view15') { ?>
        <div class="<?php echo $hdng_class ?><?php echo($design_css_class) ?>">
            <?php echo $heading_img ?>
            <h2 <?php echo $title_colr_style ?>><?php echo $h_title ?></h2>
            <span <?php echo $desc_colr_style ?>><?php echo $h_desc ?></span>
        </div>
    <?php } else if ($view == 'view14') { ?>
        <div class="<?php echo $hdng_class ?><?php echo($design_css_class) ?>">
            <h2 <?php echo $title_colr_style ?>><?php echo $h_title ?></h2>
            <span <?php echo $desc_colr_style ?>><?php echo $h_desc ?></span>
            <small class="active"></small>
            <small></small>
        </div>
    <?php } else if ($view == 'view13') { ?>
        <div class="<?php echo $hdng_class ?><?php echo($design_css_class) ?>">
            <h2 <?php echo $title_colr_style ?>><?php echo $h_title ?></h2>
            <span <?php echo $desc_colr_style ?>><?php echo $h_desc ?></span>
        </div>
    <?php } else if ($view == 'view12') { ?>
        <div class="<?php echo $hdng_class ?> style-heading-<?php echo $random_id ?><?php echo($design_css_class) ?>">
            <h2 <?php echo $title_colr_style ?>><?php echo $h_title ?></h2>
            <span <?php echo $desc_colr_style ?>><?php echo $h_desc ?></span>
        </div>
        <style>
            .style-heading-<?php echo $random_id ?>::before {
                background-color: #5dce7d;
            }
        </style>
    <?php } else if ($view == 'view11') { ?>
        <div class="<?php echo $hdng_class ?><?php echo($design_css_class) ?>">
            <h2 <?php echo $title_colr_style ?>><?php echo $h_title ?></h2>
            <span <?php echo $desc_colr_style ?>><?php echo $h_desc ?></span>
        </div>

    <?php } else if ($view == 'view10') { ?>
        <div class="<?php echo $hdng_class ?><?php echo($design_css_class) ?>">
            <h2 <?php echo $title_colr_style ?>><?php echo $h_title ?></h2>
            <span <?php echo $desc_colr_style ?>><?php echo $h_desc ?></span>
        </div>
    <?php } else if ($view == 'view9') { ?>
        <!-- Fancy Title -->
        <div class="<?php echo $hdng_class ?><?php echo($design_css_class) ?>">
            <span <?php echo $desc_colr_style ?>><?php echo $h_desc ?></span>
            <h2 <?php echo $title_colr_style ?>><?php echo $h_title ?></h2>
        </div>

    <?php } else if ($view == 'view8') { ?>
        <!-- Fancy Title -->
        <<?php echo $hdng_con ?> class="<?php echo $hdng_class ?><?php echo $content_align ?><?php echo($design_css_class) ?>">
        <?php echo $heading_img ?>
        <h2 <?php echo $title_colr_style ?>><?php echo $h_title ?></h2>
        <span <?php echo $desc_colr_style ?>><?php echo $h_desc ?></span>
        </div>

    <?php } else if ($view == 'view7') { ?>
        <!-- Fancy Title Ten -->
        <<?php echo $hdng_con ?> class="<?php echo($hdng_class) ?><?php echo $content_align ?><?php echo($design_css_class) ?>">
        <h2 <?php echo $title_colr_style ?>><?php echo $h_title ?></h2>
        <span <?php echo $desc_colr_style ?>><?php echo $h_desc ?></span>
        </div>
        <!-- Fancy Title Ten -->
    <?php } else if ($view != 'view6') { ?>
        <<?php echo($hdng_con) ?> class="<?php echo($hdng_class) ?><?php echo($design_css_class) ?>">
        <?php echo($hc_icon != '' && $view == 'view3' ? '<i class="' . $hc_icon . '"></i>' : '') ?>
        <?php echo($h_fancy_title != '' && $view == 'view5' ? '<span>' . $h_fancy_title . '</span>' : '') ?>

        <h2><?php echo($h_title) ?><?php echo($hc_title != '' ? '<span' . $title_colr_style . '>' . $hc_title . '</span>' : '') ?></h2>
        <?php
        if ($h_desc != '') {
            ?>
            <p<?php echo($desc_colr_style) ?>><?php echo($h_desc) ?></p>
            <?php
        }
        echo($view == 'view4' ? '<span> <i class="fa fa-circle"' . $desc_colr_style . '></i> <i class="fa fa-circle circle-two-size"' . $desc_colr_style . '></i> <i class="fa fa-circle circle-three-size"' . $desc_colr_style . '></i> </span>' : '');
        ?>
        </<?php echo($hdng_con) ?>>

        <?php
    } else { ?>
        <style>
            .careerfy-fancy-title-nine .small-<?php echo $random_id ?>::before {
                background-color: <?php echo $s_title_clr ?>;
            }

            .careerfy-fancy-title-nine .small-<?php echo $random_id ?> {
                color: <?php echo $proc_num_clr ?>;
            }

        </style>
        <div class="<?php echo $hdng_class ?><?php echo($design_css_class) ?>">
            <small class="small-<?php echo $random_id ?>"><?php echo $num_title ?>. <strong
                        style="color: <?php echo $s_title_clr ?>"><?php echo $s_title ?></strong></small>
            <h2 <?php echo $hc_title_clr ?>><?php echo $h_title ?></h2>
            <p <?php echo $desc_clr ?>><?php echo $h_desc ?></p>
        </div>
    <?php }
    $html = ob_get_clean();

    return $html;
}
