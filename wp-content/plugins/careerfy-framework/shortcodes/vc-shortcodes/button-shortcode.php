<?php
/**
 * Button Shortcode
 * @return html
 */

add_shortcode('careerfy_button', 'careerfy_button_shortcode');

function careerfy_button_shortcode($atts) {

    extract(shortcode_atts(array(
		'btn_styl' => '',
        'btn_txt' => '',
        'btn_url' => '',
                    ), $atts));

    ob_start();

	$btn_class = 'careerfy-modren-btn';

	if ($btn_styl == 'view2') {
		$btn_class = 'careerfy-more-view4-btn';
	}

    if ($btn_txt != '') { ?>
			<div class="<?php echo ($btn_class) ?>"><a href="<?php echo ($btn_url) ?>"><?php echo ($btn_txt) ?></a></div>
		 <?php
    }

    $html = ob_get_clean();
    return $html;
}
