<?php

/**
 * Counters Shortcode
 * @return html
 */
add_shortcode('careerfy_counters', 'careerfy_counters_shortcode');

function careerfy_counters_shortcode($atts, $content = '')
{
    global $view, $counter_icon_color, $counter_number_color, $counter_title_color;
    extract(shortcode_atts(array(
        'view' => '',
        'counter_icon_color' => '',
        'counter_number_color' => '',
        'counter_title_color' => '',
    ), $atts));
    wp_enqueue_script('careerfy-counters');

    $counter_class = 'careerfy-counter';
    if ($view == 'view-2') {
        $counter_class = 'careerfy-modren-counter';
    } else if ($view == 'view-3') {
        $counter_class = 'careerfy-counter careerfy-counter-styletwo';
    } else if ($view == 'view-4') {
        $counter_class = 'careerfy-counter-nineview';
    } else if ($view == 'view-5') {
        $counter_class = 'careerfy-counter-style10';
    } else if ($view == 'view-6') {
        $counter_class = 'careerfy-counter-elevenview';
    } else if ($view == 'view-7') {
        $counter_class = 'careerfy-counter-twelveview';
    }
    $html = '
    <div class="' . $counter_class . '">
        <ul class="row">
        ' . do_shortcode($content) . '
        </ul>
    </div>' . "\n";
    if ($view == 'view-5') {
        $html .= '<style>
                .careerfy-counter-style10 span:before {
                    background-color: ' . $counter_title_color . ';
                }
                </style>';
    }
    return $html;
}

add_shortcode('careerfy_counters_item', 'careerfy_counters_item_shortcode');
function careerfy_counters_item_shortcode($atts)
{
    global $view, $counter_icon_color, $counter_number_color, $counter_title_color;
    extract(shortcode_atts(array(
        'count_icon' => '',
        'count_number' => '',
        'count_title' => '',
    ), $atts));
    $count_icon_colr = $counter_icon_color != '' ? "style=color:$counter_icon_color" : '';
    $count_nmbr_colr = $counter_number_color != '' ? "style=color:$counter_number_color" : '';
    $count_title_colr = $counter_title_color != '' ? "style=color:$counter_title_color" : '';

    $counter_class = 'col-md-4';
    if ($view == 'view-3' or $view == 'view-4' or $view == 'view-5' or $view == 'view-6' or $view == 'view-7') {
        $counter_class = 'col-md-3';
    }

    if ($view == 'view-7') {

        $html ='<li class="' . $counter_class . '">
        ' . ($count_icon != '' ? '<i ' . $count_icon_colr . ' class="' . $count_icon . '"></i>' : '') . '
                   <h2 ' . $count_title_colr . '>' . $count_title . '</h2>
                   <span ' . $count_nmbr_colr . ' class="word-counter">' . ($count_number) . '</span>
                </' . ($count_number) . '>';

    } else if ($view == 'view-6') {

        $html = '<li class="' . $counter_class . '">
                   <i class="' . $count_icon . '" ' . $count_icon_colr . '></i>
                   <h2 ' . $count_title_colr . '>' . $count_title . '</h2>
                   <span ' . $count_nmbr_colr . ' class="word-counter">' . ($count_number) . '</span>
                 </li>';

    } else if ($view == 'view-5') {
        $html = '<li class="' . $counter_class . '">
                ' . ($count_icon != '' ? '<i ' . $count_icon_colr . ' class="' . $count_icon . '"></i>' : '') . '
                     <span ' . $count_title_colr . '>' . $count_title . '</span>
                     <small ' . $count_nmbr_colr . ' class="word-counter">' . ($count_number) . '</small>
                 </li>';
    } else if ($view == 'view-4') {
        $html = '<li class="' . $counter_class . '">
                 <span ' . $count_nmbr_colr . ' class="word-counter">' . ($count_number) . '</span>
                  <small ' . $count_title_colr . '>' . $count_title . '</small>
                </li>';
    } else {
        $html = '
    <li class="' . $counter_class . '">
        ' . ($count_icon != '' ? '<i ' . $count_icon_colr . ' class="' . $count_icon . ' careerfy-color"></i>' : '') . '
        <span ' . $count_nmbr_colr . ' class="word-counter">' . ($count_number) . '</span>
        <small ' . $count_title_colr . '>' . $count_title . '</small>
    </li>';
    }


    return $html;
}
