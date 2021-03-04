<?php

/**
 * Recent Questions Shortcode
 * @return html
 */
add_shortcode('careerfy_recent_questions', 'careerfy_recent_questions_shortcode');

function careerfy_recent_questions_shortcode($atts, $content = '') {

    extract(shortcode_atts(array(
        'ques_title' => '',
                    ), $atts));
    $html = '
    <div class="widget widget_faq">
        ' . ($ques_title != '' ? '<h2 class="careerfy-slash-title">' . $ques_title . '</h2>' : '') . '
        <ul>
        ' . do_shortcode($content) . '
        </ul>
    </div>' . "\n";

    return $html;
}

add_shortcode('careerfy_recent_questions_item', 'careerfy_recent_questions_item_shortcode');

function careerfy_recent_questions_item_shortcode($atts) {

    extract(shortcode_atts(array(
        'q_question' => '',
        'q_url' => '',
                    ), $atts));

    $html = '<li><a href="' . $q_url . '">' . $q_question . '</a></li>';

    return $html;
}
