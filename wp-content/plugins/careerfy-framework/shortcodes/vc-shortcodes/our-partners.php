<?php

/**
 * Our Partners Shortcode
 * @return html
 */
add_shortcode('careerfy_our_partners', 'careerfy_our_partners_shortcode');

function careerfy_our_partners_shortcode($atts, $content = '')
{
    global $partner_view;
    extract(shortcode_atts(array(
        'partner_view' => '',
        'partner_title' => '',
    ), $atts));
    wp_enqueue_script('careerfy-slick-slider');

    $patr_class = 'careerfy-partner-slider';

    if ($partner_view == 'view2') {
        $patr_class = 'careerfy-partnertwo-slider';
    }
    if ($partner_view == 'view3') {
        $patr_class = 'careerfy-partner-style3';
    }
    if ($partner_view == 'view4') {
        $patr_class = 'careerfy-partner-style4';
        $html = '
			<div class="' . $patr_class . '"><ul class="row">
				' . do_shortcode($content) . '
			</ul></div>';
    } else if ($partner_view == 'view5') {
        $patr_class = 'careerfy-partner-twelve-slider';
        $html = '
			<div class="' . $patr_class . '">
				' . do_shortcode($content) . '
			</div>
			<script>
			var $ = jQuery;
			$(document).ready(function() {
			  $(\'.careerfy-partner-twelve-slider\').slick({
        slidesToShow: 6,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 5000,
        infinite: true,
        dots: false,
        prevArrow: "<span class=\'slick-arrow-left\'><i class=\'fa fa-arrow-left\'></i></span>",
        nextArrow: "<span class=\'slick-arrow-right\'><i class=\'fa fa-arrow-right\'></i></span>",
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1,
              infinite: true,
            }
          },
          {
            breakpoint: 800,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1
            }
          },
          {
            breakpoint: 400,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
      });
			});
			
        </script>
			';
    } else {

        $html = '
		' . ($partner_view == 'view3' ? '<div class="careerfy-partner-style3-wrap">' : '') . '
		' . ($partner_title != '' ? '<span class="careerfy-partner-title">' . $partner_title . '</span>' : '') . '
		<div class="' . $patr_class . '">
			' . do_shortcode($content) . '
		</div>
		' . ($partner_view == 'view3' ? '</div>' : '') . "\n";
    }

    return $html;
}

add_shortcode('careerfy_our_partners_item', 'careerfy_our_partners_item_shortcode');

function careerfy_our_partners_item_shortcode($atts)
{
    global $partner_view;
    extract(shortcode_atts(array(
        'partner_img' => '',
        'partner_url' => '',
    ), $atts));

    $patr_class = 'careerfy-partner-slider-layer';
    if ($partner_view == 'view2') {
        $patr_class = 'careerfy-partnertwo-slider-layer';
    }
    if ($partner_view == 'view3') {
        $patr_class = 'careerfy-partner-style3-layer';
    }
    if ($partner_view == 'view4') {
        $html = '
			<li class="col-md-3">
				<a href="' . JobsearchAddHTTP($partner_url) . '" target="_blank"><img src="' . $partner_img . '" alt=""></a>
			</li>';
    } else if ($partner_view == 'view5') {
        $html = '<div class="careerfy-partner-twelve-layer">
                <a href="' . JobsearchAddHTTP($partner_url) . '" target="_blank"><img src="' . $partner_img . '" alt=""></a></div>';
    } else {
        $html = '
    <div class="' . $patr_class . '">
        <a href="' . JobsearchAddHTTP($partner_url) . '" target="_blank"><img src="' . $partner_img . '" alt=""></a>
    </div>';
    }
    return $html;
}