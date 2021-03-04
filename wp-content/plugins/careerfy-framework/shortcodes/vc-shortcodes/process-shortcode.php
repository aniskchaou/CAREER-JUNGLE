<?php

/**
 * Recruitment Process Shortcode
 * @return html
 */
add_shortcode('careerfy_process', 'careerfy_process_shortcode');

function careerfy_process_shortcode($atts, $content = '')
{
    global $process_shortcode_counter , $icon_color;

    $process_shortcode_counter = 1;
    $process_class = 'careerfy-services-nineview';
    $html = '
    <div class="' . $process_class . '">
        <ul class="row"">
        ' . do_shortcode($content) . '
        </ul>
    </div>
    <style>
        .careerfy-services-nineview i {
            color: '.$icon_color.';
        }
    </style>' . "\n";

    return $html;
}

add_shortcode('careerfy_process_item', 'careerfy_process_item_shortcode');
function careerfy_process_item_shortcode($atts)
{
    global $process_shortcode_counter , $icon_color;

    extract(shortcode_atts(array(
        'process_icon' => '',
        'icon_color' => '',
        'process_title' => '',
        'process_desc' => '',
    ), $atts));

    $process_icon = $process_icon !="" ? '<i class="'.$process_icon.'" ></i>' : '';
    
    $html = '<li class="col-md-4">
               '.$process_icon.'
               <h2>'.$process_title.'</h2>
               <p>'.$process_desc.'</p>
            </li>';

    $process_shortcode_counter++;
    return $html;
}
