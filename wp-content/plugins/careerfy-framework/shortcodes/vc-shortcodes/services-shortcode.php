<?php

/**
 * Services Shortcode
 * @return html
 */
add_shortcode('careerfy_services', 'careerfy_services_shortcode');
function careerfy_services_shortcode($atts, $content = '')
{
    global $service_shortcode_counter, $view, $service_text_color, $service_icon_color, $service_title_color, $small_service_title, $rand_id;
    extract(shortcode_atts(array(
        'view' => '',
        'service_title_color' => '',
        'service_text_color' => '',
        'service_icon_color' => '',
        'small_service_title' => '',
    ), $atts));
    $rand_id = rand(100000, 999999);
    $service_shortcode_counter = 1;

    $row_class = '';
    $service_class = 'careerfy-classic-services';
    if ($view == 'view-2') {
        $service_class = 'careerfy-services-classic';
    }
    if ($view == 'view-3') {
        $service_class = 'careerfy-plain-services';
    }
    if ($view == 'view-4') {
        $service_class = 'careerfy-services careerfy-services-stylefour';
    }
    if ($view == 'view-5') {
        $service_class = 'careerfy-services careerfy-services-stylefive';
    }
    if ($view == 'view-6') {
        $service_class = 'careerfy-services careerfy-services-style11';
    }
    if ($view == 'view-7') {
        $service_class = 'careerfy-seventeen-services';
    }
    if ($view == 'view-8') {
        $service_class = 'careerfy-services-eighteen';
    }
    if ($view == 'view-9') {
        $service_class = 'careerfy-services careerfy-services-nineteen';
    }
    if ($view == 'view-10') {
        $service_class = 'careerfy-services careerfy-services-nineteen-style2';
    }
    if ($view == 'view-11') {
        $service_class = 'careerfy-services careerfy-services-nineteen-style3';
    }
    if ($view == 'view-12') {
        $service_class = 'careerfy-services careerfy-services-twenty';
    }
    if ($view == 'view-13') {
        $service_class = 'careerfy-services careerfy-services-twenty-style2';
    }
    if ($view == 'view-14') {
        $service_class = 'careerfy-services careerfy-services-twenty-style3';
    }
    if ($view == 'view-15') {
        $service_class = 'careerfy-services careerfy-services-twenty-style4';
    }
    if ($view == 'view-16') {
        $service_class = 'careerfy-services careerfy-services-twentyone';
    }
    if ($view == 'view-17') {
        $service_class = 'careerfy-services careerfy-services-twentyone-style2';
    }
    if ($view == 'view-18') {
        $service_class = 'careerfy-services careerfy-services-twentyone-style3';
    }
    if ($view == 'view-19') {
        $service_class = 'careerfy-services careerfy-services-twentyone-style4';
    }
    if ($view == 'view-20') {
        $service_class = 'careerfy-services careerfy-services-twentytwo';
    }

    if ($view == 'view-22') {
        $service_class = 'careerfy-services careerfy-services-twentytwo-style3';
    }

    if ($view != 'view-21') {
        $html = '<div class="' . $service_class . '">';
    }

    if ($view == 'view-21') {
        $html = '<div class="row">
        
        ' . do_shortcode($content) . '
        </div>';
    } else {
        $html .= '<ul class="row">
        ' . do_shortcode($content) . '
        </ul>';
    }

    if ($view != 'view-21') {
        $html .= '</div>' . "\n";
    }
    return $html;
}

add_shortcode('careerfy_services_item', 'careerfy_services_item_shortcode');

function careerfy_services_item_shortcode($atts)
{
    global $service_shortcode_counter, $view, $service_text_color, $service_icon_color, $service_title_color, $small_service_title;
    extract(shortcode_atts(array(
        'service_icon' => '',
        'service_img' => '',
        'service_title' => '',
        'service_link' => '',
        'service_bg' => '',
        'service_desc' => '',
        'btn_txt' => '',
        'btn_color' => '',
    ), $atts));

    $icon_color = '';
    if (isset($service_icon_color) && !empty($service_icon_color)) {
        $icon_color = ' style="color:' . $service_icon_color . ' !important"';
    }

    $text_color = '';
    $title_color = '';
    $text_color_a = '';
    $text_color_h2 = '';
    if (isset($service_text_color) && !empty($service_text_color)) {
        $text_color = ' style="color:' . $service_text_color . ' !important"';
    }
    if (isset($service_title_color) && !empty($service_title_color)) {
        $title_color = ' style="color:' . $service_title_color . ' !important"';
    }

    if (isset($service_link) && !empty($service_link)) {
        $text_color_a = $title_color;
    }
    if (isset($text_color_a) && !empty($text_color_a)) {
        $text_color_h2 = '';
    } else {
        $text_color_h2 = $title_color;
    }

    $rand_num = rand(10000000, 99909999);
    $btn_txt = $btn_txt != "" ? $btn_txt : esc_html__("Upload Resume", "careerfy-frame");
    $btn_color = $btn_color != "" ? $btn_color : "#04d16a";

    if ($view == 'view-2') {
        $html = '
        <li class="col-md-4">
            <span><i class="' . $service_icon . '"' . $icon_color . '></i></span>
            <h2' . $text_color_h2 . '>' . ($service_link != '' ? '<a href="' . $service_link . '"' . $text_color_a . '>' : '') . $service_title . ($service_link != '' ? '</a>' : '') . '</h2>
            <p' . $text_color . '>' . $service_desc . '</p>
        </li>';
    } else if ($view == 'view-3' || $view == 'view-4') {
        $html = '
        <li class="col-md-4">
            ' . ($view == 'view-4' ? '<div class="careerfy-services-stylefour-wrap ' . ($service_shortcode_counter == 2 ? 'active' : '') . '"' . ($service_bg != '' ? ' style="background-color: ' . $service_bg . ';"' : '') . '>' : '') . ' 
            <i class="' . $service_icon . '"' . $icon_color . '></i>
            <h2' . $text_color_h2 . '>' . ($service_link != '' ? '<a href="' . $service_link . '"' . $text_color_a . '>' : '') . $service_title . ($service_link != '' ? '</a>' : '') . '</h2>
            <p' . $text_color . '>' . $service_desc . '</p>
            ' . ($view == 'view-4' && $service_shortcode_counter == 2 ? '<a ' . ($service_link != '' ? 'href="' . $service_link . '"' : '') . ' class="careerfy-services-stylefour-btn"><small class="careerfy-icon careerfy-right-arrow"></small></a>' : '') . '
            ' . ($view == 'view-4' ? '</div>' : '') . ' 
        </li>';
    } else if ($view == 'view-5') {
        $html = '
        <li class="col-md-4">
			<a href="' . $service_link . '"><img src="' . $service_img . '" alt=""></a>
            <h2' . $text_color_h2 . '>' . ($service_link != '' ? '<a href="' . $service_link . '"' . $text_color_a . '>' : '') . $service_title . ($service_link != '' ? '</a>' : '') . '</h2>
            <p' . $text_color . '>' . $service_desc . '</p>
        </li>';
    } else if ($view == 'view-6') {
        $html = '
        <style>
         .careerfy-action-style11  .careerfy-services-style11-btn-' . $rand_num . ' {
            border-color: ' . $btn_color . ';
                }
                li:hover .careerfy-services-style11-btn-' . $rand_num . ' {
                background-color: ' . $btn_color . ';
                }
        </style>
        <li class="col-md-4">';
                if(!empty($service_img)) {
                    $html .= '<img src="' . $service_img . '" alt="">';
                }
            $html .='<h2 ' . $text_color_h2 . '>' . ($service_link != '' ? '<a href="' . $service_link . '"' . $text_color_a . '>' : '') . $service_title . ($service_link != '' ? '</a>' : '') . '</h2>
            <p ' . $text_color . '>' . $service_desc . '</p >
            <a  href = "' . $service_link . '" class="careerfy-services-style11-btn" > ' . $btn_txt . '</a >
        </li >';

    } else if ($view == 'view-7') {
        $html = '<li class="col-md-3">
                     <i class="' . $service_icon . '"' . $icon_color . '></i>
                     <h2 ' . $text_color_h2 . '>' . ($service_link != '' ? '<a href="' . $service_link . '"' . $text_color_a . '>' : '') . $service_title . ($service_link != '' ? '</a>' : '') . '</h2>
                     <p ' . $text_color . '>' . $service_desc . '</p>
                 </li>';
    } else if ($view == 'view-8') {
        $html = '
        <style>
         .careerfy-services-eighteen  .careerfy-action-button-' . $rand_num . ' {
            border-color: ' . $btn_color . ';
            color: ' . $btn_color . ';
                }
                
        </style>
        
       <div class="col-md-3">
          <i class="' . $service_icon . '" ' . $icon_color . '></i>
          <span ' . $title_color . '>' . $service_title . '</span>
          <p ' . $text_color . '>' . $service_desc . '</p >
          <a href="' . $service_link . '" class="careerfy-action-button-' . $rand_num . '">' . $btn_txt . '</a>
        </div>';

    } else if ($view == 'view-9') {
        $html = '
        
       <li class="col-md-6">
       <a href="' . $service_link . '">
          <small><i class="' . $service_icon . '" ' . $icon_color . '></i></small>
          <span ' . $title_color . '>' . $service_title . '</span>
          <p ' . $text_color . '>' . $service_desc . '</p >
         </a>
        </li>';

    } else if ($view == 'view-10') {

        $html = '
       <li class="col-md-12">
       <a href="' . $service_link . '">
          <small><i class="' . $service_icon . '" ' . $icon_color . '></i></small>
          <span ' . $title_color . '>' . $service_title . '</span>
          <p ' . $text_color . '>' . $service_desc . '</p >
         </a>
        </li>';

    } else if ($view == 'view-11') {

        $imag_id = attachment_url_to_postid($service_img);
        $thumbnail_image = wp_get_attachment_image_src($imag_id, 'thumbnail');
        $no_placeholder_img = '';
        if (function_exists('jobsearch_no_image_placeholder')) {
            $no_placeholder_img = jobsearch_no_image_placeholder();
        }
        $thumbnail_src = isset($thumbnail_image[0]) && esc_url($thumbnail_image[0]) != '' ? $thumbnail_image[0] : $no_placeholder_img;
        $html = '
       <li class="col-md-6">
       <a href="' . $service_link . '">
        <img src="' . $thumbnail_src . '" alt="">
          <p>
              <small ' . $title_color . '>' . $service_title . '</small>
              <span ' . $text_color . '>' . $service_desc . '</span>
              <strong>' . $btn_txt . '</strong>
          </p >
         </a>
          
        </li>';

    } else if ($view == 'view-12') {

        $html = '
       <li class="col-md-3">
       <a href="' . $service_link . '">
        <i class="top-icon ' . $service_icon . '" ' . $icon_color . '></i>
              <span ' . $title_color . '>' . $service_title . '</span>
              <p ' . $text_color . '>' . $service_desc . '</p>
              <strong >' . $btn_txt . '</strong>
              <i class="bottom-icon ' . $service_icon . '"></i>
         </a>
        </li>';

    } else if ($view == 'view-13') {

        $html = '
       <li class="col-md-6">
       <a href="' . $service_link . '">
       <div class="careerfy-services-twenty-style2-counter">
       
       <i class="' . $service_icon . '" ' . $icon_color . '></i>
        </div>
        <div class="careerfy-services-twenty-style2-content">
        <small>' . $service_shortcode_counter . '</small>
              <span ' . $title_color . '>' . $service_title . '</span>
              <p ' . $text_color . '>' . $service_desc . '</p>
              </div>
         </a>
        </li>';

    } else if ($view == 'view-14') {

        $html = '

       <li class="col-md-3">
       <a href="' . $service_link . '">
       <i class="' . $service_icon . '" ' . $icon_color . '></i>
         <span ' . $title_color . '>' . $service_title . '</span>
         <p ' . $text_color . '>' . $service_desc . '</p>
         </a>
        </li>';

    } else if ($view == 'view-15') {

        $imag_id = attachment_url_to_postid($service_img);
        $thumbnail_image = wp_get_attachment_image_src($imag_id, 'careerfy-service');
        $no_placeholder_img = '';
        if (function_exists('jobsearch_no_image_placeholder')) {
            $no_placeholder_img = jobsearch_no_image_placeholder();
        }
        $thumbnail_src = isset($thumbnail_image[0]) && esc_url($thumbnail_image[0]) != '' ? $thumbnail_image[0] : $no_placeholder_img;
        $html = '
       <li class="col-md-3">
       <a href="' . $service_link . '">
       <div class="careerfy-services-twenty-img">
            <img src="' . $thumbnail_src . '" alt=""><small></small>
       </div>
         <span ' . $title_color . '>' . $service_title . '</span>
         <p ' . $text_color . '>' . $service_desc . '</p>
         
         </a>
        </li>';

    } else if ($view == 'view-16') {

        $html = '
       <li class="col-md-3">
       <a href="' . $service_link . '">
         <span ' . $title_color . '>0' . $service_shortcode_counter . '. ' . $service_title . '</span>
         <p ' . $text_color . '>' . $service_desc . '</p>
         <i class="' . $service_icon . '"></i>
         </a>
        </li>';

    } else if ($view == 'view-17') {

        $imag_id = attachment_url_to_postid($service_img);
        $thumbnail_image = wp_get_attachment_image_src($imag_id, 'thumbnail');
        $no_placeholder_img = '';
        if (function_exists('jobsearch_no_image_placeholder')) {
            $no_placeholder_img = jobsearch_no_image_placeholder();
        }
        $thumbnail_src = isset($thumbnail_image[0]) && esc_url($thumbnail_image[0]) != '' ? $thumbnail_image[0] : $no_placeholder_img;

        $html = '<li class="col-md-12">
       <a href="' . $service_link . '">
          <img src="' . $thumbnail_src . '" alt="">
          <div class="careerfy-services-twentyone-content">
          <span ' . $title_color . '>' . $service_title . '</span>
          <p ' . $text_color . '>' . $service_desc . '</p >
          </div>
         </a>
        </li>';

    } else if ($view == 'view-18') {
        $html = '
       <li class="col-md-3">
       <a href="' . $service_link . '">
       <small><i class="' . $service_icon . '"></i></small>
         <span ' . $title_color . '>' . $service_title . '</span>
         <p ' . $text_color . '>' . $service_desc . '</p>
         </a>
        </li>';
    } else if ($view == 'view-19') {

        $service_bg_img = !empty($service_img) ? "style='background-image: url($service_img)'" : '';

        $html = '
       <li class="col-md-3">
       <a href="' . $service_link . '"  ' . $service_bg_img . '>
         <span ' . $title_color . '>' . $service_title . '</span>
         <p ' . $text_color . '>' . $service_desc . '</p>
         <small>0' . $service_shortcode_counter . '</small>
         </a>
        </li>';
    } else if ($view == 'view-20') {
        $html = '
       <li class="col-md-3">
       <div class="careerfy-services-twentytwo-inner">
       <a href="' . $service_link . '">
       <i class="' . $service_icon . '"></i>
         <span ' . $title_color . '>' . $service_title . '</span>
         <p ' . $text_color . '>' . $service_desc . '</p>
         </a>
         <a href="' . $service_link . '" class="careerfy-services-twentytwo-btn" >' . $btn_txt . '</a>
         </div>
        </li>';

    } else if ($view == 'view-21') {

        $imag_id = attachment_url_to_postid($service_img);
        $thumbnail_image = wp_get_attachment_image_src($imag_id);
        $no_placeholder_img = '';
        if (function_exists('jobsearch_no_image_placeholder')) {
            $no_placeholder_img = jobsearch_no_image_placeholder();
        }
        $thumbnail_src = isset($thumbnail_image[0]) && esc_url($thumbnail_image[0]) != '' ? $thumbnail_image[0] : $no_placeholder_img;
        $html = '';
        if ($service_shortcode_counter == 1) {

            $html = '<div class="col-md-6">
                    <div class="careerfy-services-twentytwo-style2 careerfy-services-big">
                        <a href="' . $service_link . '">
                        <div class="careerfy-services-twentytwo-style2-img">
                        <img src="' . $thumbnail_src . '" >
                        </div>
                    <div class="careerfy-services-twentytwo-style2-content">';

            if ($service_shortcode_counter == 1) {
                $html .= '<small > ' . $small_service_title . '</small >';
            }

            $html .= '
                <span ' . $title_color . '>' . $service_title . '</span>
                         <p ' . $text_color . '>' . $service_desc . '</p>
                         <strong class="careerfy-services-twentytwo-style2-btn">' . $btn_txt . '</strong>
                </div>
                
                </a>
                
                </div>
            </div>';
        }

        if ($service_shortcode_counter == 2) {
            $html .= '<div class="col-md-6">
                    <div class="careerfy-services-twentytwo-style2-wrapper">';
        }
        if ($service_shortcode_counter != 1) {
            $html .= '<div class="careerfy-services-twentytwo-style2">
                    <a href="' . $service_link . '" >
                    <div class="careerfy-services-twentytwo-style2-img">
                     <img src="' . $thumbnail_src . '" >
                    </div>
                    <div class="careerfy-services-twentytwo-style2-content">
        <h2 ' . $title_color . '>' . $service_title . '</h2>
                  <p ' . $text_color . '>' . $service_desc . '</p>
                </div>
                </a>
                </div>';
        }
        if ($service_shortcode_counter == 5) {
            $html .= '</div></div>';
        }

    } else if ($view == 'view-22') {

        $imag_id = attachment_url_to_postid($service_img);
        $thumbnail_image = wp_get_attachment_image_src($imag_id, 'careerfy-testimonial-thumb');
        $no_placeholder_img = '';
        if (function_exists('jobsearch_no_image_placeholder')) {
            $no_placeholder_img = jobsearch_no_image_placeholder();
        }
        $thumbnail_src = isset($thumbnail_image[0]) && esc_url($thumbnail_image[0]) != '' ? $thumbnail_image[0] : $no_placeholder_img;

        $html = '
       <li class="col-md-3 ">
       <a href="' . $service_link . '">
       <img src="' . $thumbnail_src . '" alt="">
         <span ' . $title_color . '>' . $service_title . '</span>
         <p ' . $text_color . '>' . $service_desc . '</p>
         </a>
        </li>';
    } else {
        $html = '
        <li class="col-md-4">
            <span>' . $service_shortcode_counter . '</span>
            <i class="' . $service_icon . '"' . $icon_color . '></i>
            <h2' . $text_color_h2 . '>' . ($service_link != '' ? '<a href="' . $service_link . '"' . $text_color_a . '>' : '') . $service_title . ($service_link != '' ? '</a>' : '') . '</h2>
            <p' . $text_color . '>' . $service_desc . '</p>
        </li>';
    }

    $service_shortcode_counter++;
    return $html;
}
