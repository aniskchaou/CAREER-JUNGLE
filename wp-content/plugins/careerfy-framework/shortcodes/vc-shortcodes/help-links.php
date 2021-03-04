<?php

/**
 * Help Links Shortcode
 * @return html
 */
add_shortcode('careerfy_help_links', 'careerfy_help_links_shortcode');

function careerfy_help_links_shortcode($atts, $content = '') {

    $html = '
    <div class="contact-service">
        <ul class="row">
        ' . do_shortcode($content) . '
        </ul>
    </div>' . "\n";
    return $html;
}

add_shortcode('careerfy_help_links_item', 'careerfy_help_links_item_shortcode');

function careerfy_help_links_item_shortcode($atts) {

    extract(shortcode_atts(array(
        'help_icon' => '',
        'help_title' => '',
        'btn_txt' => '',
        'btn_url' => '',
                    ), $atts));

    $html = '
    <li class="col-md-4">
        <h2>' . $help_title . '</h2>
        <i class="' . $help_icon . '"></i>
        <a href="' . $btn_url . '">' . $btn_txt . '</a>
    </li>';

    return $html;
}
