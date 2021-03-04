<?php

/**
 * Testimonials Shortcode
 * @return html
 */
add_shortcode('careerfy_testimonials', 'careerfy_testimonials_shortcode');

function careerfy_testimonials_shortcode($atts, $content = '')
{
    global $testi_view, $imgs_html;
    extract(shortcode_atts(array(
        'testi_view' => '',
        'img' => '',
    ), $atts));
    $rand_num = rand(1000, 9999);
    wp_enqueue_script('careerfy-slick-slider');
    if ($testi_view == 'view15') {
        $html = '
        <div class="careerfy-twentytwo-testimonial">
            ' . do_shortcode($content) . '
        </div>
                <script type="text/javascript">
                var $ = jQuery;
            $(document).ready(function() {
                $(\'.careerfy-twentytwo-testimonial\').slick({
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    infinite: true,
                    dots: true,
                    arrows: false,
                    centerMode: true,
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
                  })
            })
                </script>     
        ' . "\n";
    }
    else if ($testi_view == 'view14') {
        $html = '
        <div class="careerfy-twentyone-testimonial">
            ' . do_shortcode($content) . '
        </div>
                <script type="text/javascript">
                var $ = jQuery;
            $(document).ready(function() {
                $(\'.careerfy-twentyone-testimonial\').slick({
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    infinite: true,
                    dots: false,
                    arrows: false,
                    centerMode: false,
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
                  })
            })
                </script>     
        ' . "\n";
    }
    else if ($testi_view == 'view13') {

        $imgs_html = '';

        $html = '
    <div class="careerfy-twenty-testimonial-wrapper">';

        $view_13_testimonial = '
        <div class="careerfy-twenty-testimonial">
            ' . do_shortcode($content) . '
        </div>';

        $html .= '<div class="careerfy-twenty-testimonial-for">
            ' . $imgs_html . '
        </div>';

        $html .= $view_13_testimonial;

        $html .= '</div> 
    
        <script type="text/javascript">
       
            jQuery(document).ready(function() {
                
                jQuery(\'.careerfy-twenty-testimonial\').slick({
                      slidesToShow: 1,
                      slidesToScroll: 1,
                      arrows: false,
                      fade: true,
                      dots: true,
                      asNavFor: \'.careerfy-twenty-testimonial-for\',
                 });
                
                jQuery(\'.careerfy-twenty-testimonial-for\').slick({
                      slidesToShow: 3,
                      slidesToScroll: 1,
                      asNavFor: \'.careerfy-twenty-testimonial\',
                      dots: false,
                      centerMode: true,
                      focusOnSelect: true,
                      prevArrow: false,
                      nextArrow: false,
                });
                });
        </script>
        ' . "\n";
    }
    else if ($testi_view == 'view12') {
        $imgs_html = '';
        $html = '
<div class="careerfy-nineteen-testimonial-wrapper">
        <div class="careerfy-nineteen-testimonial">
            ' . do_shortcode($content) . '
        </div>
        
        <div class="careerfy-nineteen-testimonial-for">
            ' . $imgs_html . '
        </div>
     </div> 
    
        <script type="text/javascript">
       
            jQuery(document).ready(function() {
                jQuery(\'.careerfy-nineteen-testimonial\').slick({
                      slidesToShow: 1,
                      slidesToScroll: 1,
                      arrows: false,
                      fade: true,
                      asNavFor: \'.careerfy-nineteen-testimonial-for\',
                 });
                jQuery(\'.careerfy-nineteen-testimonial-for\').slick({
                      slidesToShow: 3,
                      slidesToScroll: 1,
                      asNavFor: \'.careerfy-nineteen-testimonial\',
                      dots: false,
                      centerMode: true,
                      focusOnSelect: true,
                      prevArrow: false,
                      nextArrow: false,
                });
                });
        </script>
        
        ' . "\n";
    } else if ($testi_view == 'view11') {
        $html = '
        <div class="careerfy-seventeen-testimonial-full">
            ' . do_shortcode($content) . '
        </div>' . "\n";
    } else if ($testi_view == 'view10') {
        $html = '
        <div class="careerfy-testimonial-style14-slider">
            ' . do_shortcode($content) . '
        </div>
                <script type="text/javascript">
                var $ = jQuery;
            $(document).ready(function() {
                $(\'.careerfy-testimonial-style14-slider\').slick({
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    infinite: true,
                    dots: false,
                    arrows: false,
                    centerMode: true,
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
                  })
            })
                </script>     
        ' . "\n";
    } else if ($testi_view == 'view9') {

        $html = '<div class="careerfy-testimonails-thirteen" id="testimonails-thirteen-' . $rand_num . '">
                    ' . do_shortcode($content) . '
                </div>
            
                <script type="text/javascript">
            var $ = jQuery;
        $(document).ready(function() { 
            jQuery("#testimonails-thirteen-' . $rand_num . '").slick({
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 5000,
                    infinite: true,
                    dots: false,
                    arrows: "false",
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
                ' . "\n";
    } else if ($testi_view == 'view8') {

        $html = '
        <div class="careerfy-testimonial-twelve">
            <ul class="row">
            ' . do_shortcode($content) . '
            </ul>
        </div>   
        ' . "\n";

    } else if ($testi_view == 'view7') {
        $html = '
        <div class="careerfy-testimonial-style11-slider">
            ' . do_shortcode($content) . '
        </div>
        <script type="text/javascript">
        var $ = jQuery;
        $(document).ready(function() {
            $(\'.careerfy-testimonial-style11-slider\').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 5000,
        infinite: true,
        dots: false,
        prevArrow: "<span class=\'slick-arrow-left\'><i class=\'careerfy-icon careerfy-next-1\'></i></span>",
        nextArrow: "<span class=\'slick-arrow-right\'><i class=\'careerfy-icon careerfy-next-1\'></i></span>",
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
              infinite: true,
            }
          },
          {
            breakpoint: 800,
            settings: {
              slidesToShow: 1,
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
        })
        </script>
        ' . "\n";

    } else if ($testi_view == 'view6') {
        $html = '
        <div class="careerfy-testimonial-style10-slider">
            ' . do_shortcode($content) . '
        </div>
        <script type="text/javascript">
        var $ = jQuery;
        $(document).ready(function() {
            $(\'.careerfy-testimonial-style10-slider\').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 5000,
        infinite: true,
        dots: false,
        prevArrow: "<span class=\'slick-arrow-left\'><i class=\'careerfy-icon careerfy-next-1\'></i></span>",
        nextArrow: "<span class=\'slick-arrow-right\'><i class=\'careerfy-icon careerfy-next-1\'></i></span>",
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
              infinite: true,
            }
          },
          {
            breakpoint: 800,
            settings: {
              slidesToShow: 1,
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
        })
        </script>
        ' . "\n";
    } else if ($testi_view == 'view5') {
        $html = '
        <div class="careerfy-testimonial-slider-classic">
            ' . do_shortcode($content) . '
        </div>
        <script type="text/javascript">
        var $ = jQuery;
        $(document).ready(function() {
            
          $(\'.careerfy-testimonial-slider-classic\').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 5000,
        infinite: true,
        dots: false,
        prevArrow: "<span class=\'slick-arrow-left\'><i class=\'careerfy-icon careerfy-arrow-right-light\'></i></span>",
        nextArrow: "<span class=\'slick-arrow-right\'><i class=\'careerfy-icon careerfy-arrow-right-light\'></i></span>",
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1,
              infinite: true
            }
          },
          {
            breakpoint: 800,
            settings: {
              slidesToShow: 1,
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
        })
</script>
        ' . "\n";
    } else if ($testi_view == 'view4') {
        $html = '
        <div class="careerfy-testimonial-style4">
            ' . do_shortcode($content) . '
        </div>' . "\n";
    } else if ($testi_view == 'view3') {
        $html = '
        <div class="container-fluid">
            <div class="row">
                <div class="careerfy-testimonial-slider-style3-wrap">
                    ' . ($img != '' ? '<div class="careerfy-plan-thumb"><img src="' . $img . '" alt=""></div>' : '') . '
                    <div class="careerfy-testimonial-slider-style3">
                        ' . do_shortcode($content) . '
                    </div>
                    <ul class="careerfy-testimonial-nav">
                        <li class="careerfy-prev"><i class="careerfy-icon careerfy-right-arrow-long"></i></li>
                        <li class="careerfy-next"><i class="careerfy-icon careerfy-right-arrow-long"></i></li>
                    </ul>
                </div>
            </div>
        </div>' . "\n";
    } else if ($testi_view == 'view2') {
        $html = '
        <div class="careerfy-testimonial-styletwo">
            ' . do_shortcode($content) . '
        </div>' . "\n";
    } else {

        $html = '
        <div class="careerfy-testimonial-section">
            <div class="row">
                ' . ($img != '' ? '<aside class="col-md-5" style="background:url('.$img.'); background-repeat: no-repeat; background-size:cover; background-position: center;"></aside>' : '') . '
                <aside class="col-md-7">
                    <div class="careerfy-testimonial-slider">
                        ' . do_shortcode($content) . '
                    </div>
                </aside>
            </div>
        </div>' . "\n";
    }
    return $html;
}

add_shortcode('careerfy_testimonial_item', 'careerfy_testimonial_item_shortcode');
function careerfy_testimonial_item_shortcode($atts)
{
    global $testi_view, $imgs_html;
    extract(shortcode_atts(array(
        'img' => '',
        'desc' => '',
        'title' => '',
        'position' => '',
        'location' => '',
        'bg_color' => '',
        'fb_url' => '',
        'twitter_url' => '',
        'linkedin_url' => '',
        'link_btn_txt' => '',
        'link_btn_url' => '',
        'date_txt' => '',
        'testimonial_url' => '',
    ), $atts));


    $bg_color = $bg_color != "" ? "style='background-color: $bg_color'" : "";

    if ($testi_view == 'view15') {

        $imag_id = attachment_url_to_postid($img);
        $thumbnail_image = wp_get_attachment_image_src($imag_id, 'careerfy-posts-msmal');
        $no_placeholder_img = '';
        if (function_exists('jobsearch_no_image_placeholder')) {
            $no_placeholder_img = jobsearch_no_image_placeholder();
        }
        $thumbnail_src = isset($thumbnail_image[0]) && esc_url($thumbnail_image[0]) != '' ? $thumbnail_image[0] : $no_placeholder_img;


        $html = '<div class="careerfy-testimonial-twentytwo-description">
                <div class="careerfy-testimonial-twentytwo-inner">
                    <p><i class="careerfy-icon careerfy-left-quote"></i>' . ($desc) . '</p>
                <img src="' . $thumbnail_src . '" />
                    <h2>' . ($title) . '</h2>
                    <span>' . ($position) . '</span>
                </div></div>';


    } else if ($testi_view == 'view13') {

        $imag_id = attachment_url_to_postid($img);
        $thumbnail_image = wp_get_attachment_image_src($imag_id, 'careerfy-testimonial-thumb');
        $no_placeholder_img = '';
        if (function_exists('jobsearch_no_image_placeholder')) {
            $no_placeholder_img = jobsearch_no_image_placeholder();
        }
        $thumbnail_src = isset($thumbnail_image[0]) && esc_url($thumbnail_image[0]) != '' ? $thumbnail_image[0] : $no_placeholder_img;

        $imgs_html .= '<div>
                        <a href="javascript:void(0)"><img src="' . ($thumbnail_src) . '" alt=""></a>
                     </div>';

        $html = '<div class="careerfy-testimonial-description">
                    <p>' . ($desc) . '</p>
                    <h2>' . ($title) . '</h2>
                    <span>' . ($position) . '</span>
                </div>';


    } else if ($testi_view == 'view14') {

        $imag_id = attachment_url_to_postid($img);
        $thumbnail_image = wp_get_attachment_image_src($imag_id, 'careerfy-testimonial-thumb');
        $no_placeholder_img = '';
        if (function_exists('jobsearch_no_image_placeholder')) {
            $no_placeholder_img = jobsearch_no_image_placeholder();
        }
        $thumbnail_src = isset($thumbnail_image[0]) && esc_url($thumbnail_image[0]) != '' ? $thumbnail_image[0] : $no_placeholder_img;

        $html = '<div class="careerfy-testimonial-twentyone-layers">
          <div class="careerfy-testimonial-twentyone-inner">
                <a href="javascript:void(0)" tabindex="0">
                    <img src="' . ($thumbnail_src) . '" alt="">
                </a>
                <div class="careerfy-testimonialone-description">
                    <p>' . ($desc) . '</p>
                    <h2>' . ($title) . '</h2>
                    <span>' . ($position) . '</span>
                </div></div>
                </div>';


    } else if ($testi_view == 'view12') {

        $imag_id = attachment_url_to_postid($img);
        $thumbnail_image = wp_get_attachment_image_src($imag_id, 'thumbnail');
        $no_placeholder_img = '';
        if (function_exists('jobsearch_no_image_placeholder')) {
            $no_placeholder_img = jobsearch_no_image_placeholder();
        }
        $thumbnail_src = isset($thumbnail_image[0]) && esc_url($thumbnail_image[0]) != '' ? $thumbnail_image[0] : $no_placeholder_img;

        $html = '
            <div class="careerfy-testimonial-description">
                    <p>' . ($desc) . '</p>
                    <h2>' . ($title) . '</h2>
                </div>';
        $imag_id = attachment_url_to_postid($img);
        $thumbnail_image = wp_get_attachment_image_src($imag_id, 'thumbnail');
        $no_placeholder_img = '';
        if (function_exists('jobsearch_no_image_placeholder')) {
            $no_placeholder_img = jobsearch_no_image_placeholder();
        }
        $thumbnail_src = isset($thumbnail_image[0]) && esc_url($thumbnail_image[0]) != '' ? $thumbnail_image[0] : $no_placeholder_img;
        $imgs_html .= '<div>
                        <a href="javascript:void(0)"><img src="' . ($thumbnail_src) . '" alt=""></a>
                     </div>';


    } else if ($testi_view == 'view11') {
        $html = '<div class="col-md-6">
                   <div class="careerfy-seventeen-testimonial">
                   <figure>
                   <a href="' . $testimonial_url . '"><img src="' . ($img) . '" alt=""> <strong></strong> </a>
                   <figcaption>
                   <h2><a href="' . $testimonial_url . '">' . ($title) . '</a></h2>
                <span>' . $position . '</span>';
        if ($date_txt != "") {
            $html .= '<small>' . $date_txt . '</small>';
        }
        $html .= '</figcaption>
                                </figure>
                                <p>' . ($desc) . '</p>
                            </div>
                        </div>';

    } else if ($testi_view == 'view10') {
        $html = '<div class="careerfy-testimonial-style14-layer">
                                <div class="careerfy-testimonial-style14-inner">
                                    <img src="' . ($img) . '" alt="">
                                    <h2>' . ($title) . '</h2>
                                    <span>' . $position . '</span>
                                    <p>' . ($desc) . '</p>
                                    <a href="' . $link_btn_url . '" class="careerfy-testimonial-style14-btn">' . $link_btn_txt . '</a>
                                </div>
                            </div>';
    } else if ($testi_view == 'view9') {

        $html = '<div class="careerfy-testimonails-thirteen-layer">
                                    <div class="careerfy-testimonails-thirteen-inner">
                                        <i class="careerfy-icon careerfy-phrase"></i>
                                        <div class="clearfix"></div>
                                        <p>' . ($desc) . '</p>
                                        <img src="' . ($img) . '" alt="">
                                        <div class="careerfy-testimonails-thirteen-text">
                                            <h2>' . ($title) . '</h2>
                                            <div class="clearfix"></div>';

        if ($fb_url != '') {
            $html .= '<a href="' . $fb_url . '" class="fa fa-facebook"></a>';
        }

        if ($twitter_url != '') {
            $html .= '<a href="' . $twitter_url . '" class="fa fa-twitter"></a>';
        }

        if ($linkedin_url != '') {
            $html .= '<a href="' . $linkedin_url . '" class="fa fa-linkedin"></a>';
        }

        $html .= '</div>
                       </div>
                          </div>';

    } else if ($testi_view == 'view8') {
        $html = '<li class="col-md-4">
                   <div class="careerfy-testimonial-twelve-inner">
                     <i class="careerfy-icon careerfy-quote"></i>
                          <p>' . ($desc) . '</p>
                           <figure>
                           <img src="' . ($img) . '" alt="">
                           <figcaption>
                           <h2>' . ($title) . '</h2>
                           <span>' . ($position) . '</span>
                          </figcaption>
                     </figure>
                    </div>
                  </li>';
    } else if ($testi_view == 'view7') {
        $html = '<div class="careerfy-testimonial-style11-slider-layer" ' . $bg_color . '>
                    <i class="careerfy-icon careerfy-phrase"></i>
                    <p>' . ($desc) . '</p>
                     <figure>
                      <a href="#"><img src="' . ($img) . '" alt=""></a>
                      <figcaption>
                       <h2>' . ($title) . '</h2>
                       <span>' . $location . '</span>
                      </figcaption>
                    </figure>
                  </div>';
    } else if ($testi_view == 'view6') {
        $html = '<div class="careerfy-testimonial-style10-slider-layer">
                  <figure>';

        $img_headers = @get_headers($img);

        if (strpos($img_headers[0], '200') !== false) {
            $html .= '<a href="#"><img src="' . ($img) . '" alt=""></a>';
        }
        $html .= '<figcaption>
                     <h2><a href="#">' . ($title) . '</a></h2>
                       <span>' . ($position) . '</span>
                     </figcaption>
                    </figure>
                    <p>' . ($desc) . '</p>
                    <i class="careerfy-icon careerfy-quote quote-icon-style"></i>
                </div>';
    } else if ($testi_view == 'view5') {
        $html = '
        <div class="careerfy-testimonial-slider-classic-layer">
          <div class="careerfy-testimonial-slider-classic-pera">
            <p> <i class="careerfy-icon careerfy-left-quote"></i>' . ($desc) . '</p>
         </div>
         <div class="careerfy-testimonial-slider-classic-text">
          <img src="' . ($img) . '" alt="">
           <h2>' . ($title) . '</h2>
           <span>' . ($position) . '</span>
           </div>
        </div>
        
        ';
    } else if ($testi_view == 'view4') {
        $html = '
        <div class="careerfy-testimonial-style4-layer">
            <img src="' . ($img) . '" alt="">
            <p>' . ($desc) . '</p>
            <span>' . ($title) . ' <small>' . ($position) . '</small> </span>
        </div>';
    } else if ($testi_view == 'view3') {
        $html = '
        <div class="careerfy-testimonial-slider-style3-layer">
            <div class="testimonial-slider-style3-text">
                <p>' . ($desc) . '</p>
                <span><i class="careerfy-icon careerfy-left-quote"></i> ' . ($position != '' ? '<small>' . $title . ',</small>' : '') . ' ' . ($position) . '</span>
            </div>
        </div>';
    } else if ($testi_view == 'view2') {
        $html = '
        <div class="careerfy-testimonial-styletwo-layer">
            <img src="' . ($img) . '" alt="">
            <p>' . ($desc) . '</p>
            <span>' . ($title) . '</span>
            <small>' . ($position) . '</small>
        </div>';
    } else {
        $html = '
        <div class="careerfy-testimonial-slide-layer">
            <div class="careerfy-testimonial-wrap">
                <p>' . ($desc) . '</p>
                <div class="careerfy-testimonial-text">
                    <h2>' . ($title) . '</h2>
                    <span>' . ($position) . '</span>
                </div>
            </div>
        </div>';
    }
    return $html;
}