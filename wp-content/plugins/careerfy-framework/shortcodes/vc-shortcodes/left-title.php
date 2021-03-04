<?php
/**
 * Left Title Shortcode
 * @return html
 */
add_shortcode('careerfy_left_title', 'careerfy_left_title_shortcode');

function careerfy_left_title_shortcode($atts) {
    extract(shortcode_atts(array(
        'h_title' => '',
        'btn_txt' => '',
        'btn_url' => '',
                    ), $atts));

    ob_start();
    ?>
    <div class="careerfy-fancy-left-title"> <h2><?php echo ($h_title) ?></h2> <?php echo ($btn_txt != '' ? '<a href="' . $btn_url . '">' . $btn_txt . '</a>' : '') ?> </div>
    <?php
    $html = ob_get_clean();
    return $html;
}
