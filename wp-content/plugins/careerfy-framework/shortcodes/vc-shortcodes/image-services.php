<?php

/**
 * Services Shortcode
 * @return html
 */
add_shortcode('careerfy_image_services', 'careerfy_image_services_shortcode');

function careerfy_image_services_shortcode($atts, $content = '') {
    global $service_shortcode_counter, $service_view;
    extract(shortcode_atts(array(
        'service_view' => '',
                    ), $atts));

    $service_shortcode_counter = 1;

    $serv_class = 'careerfy-services careerfy-modren-services';
    if ($service_view == 'view2') {
        $serv_class = 'careerfy-services careerfy-services-stylethree';
    }

    $html = '
    <div class="' . $serv_class . '">
        <ul class="row">
        ' . do_shortcode($content) . '
        </ul>
    </div>' . "\n";

    return $html;
}

add_shortcode('careerfy_image_services_item', 'careerfy_image_services_item_shortcode');

function careerfy_image_services_item_shortcode($atts) {
    global $service_shortcode_counter, $service_view;
    extract(shortcode_atts(array(
        'service_img' => '',
        'service_title' => '',
        'service_desc' => '',
        'service_link' => '',
                    ), $atts));

    if ($service_view == 'view2') {
        $html = '
        <li class="col-md-4">
            <div class="careerfy-services-stylethree-wrap">
                <img src="' . $service_img . '" alt="">
                <h2>' . ($service_link != '' ? '<a href="' . $service_link . '">' : '') . $service_title . ($service_link != '' ? '</a>' : '') . '</h2>
                <p>' . $service_desc . '</p>
                <span>' . $service_shortcode_counter . '</span>
            </div>
        </li>';
    } else {
        $html = '
        <li class="col-md-3">
            <div class="careerfy-modren-services-wrap">
                <img src="' . $service_img . '" alt="">
                <p>' . $service_desc . '</p>
                <span>' . $service_title . '</span>
                <a href="' . $service_link . '" class="careerfy-modren-service-link"><i class="careerfy-icon careerfy-right-arrow-long"></i></a>
            </div>
        </li>';
    }
    $service_shortcode_counter++;
    return $html;
}
