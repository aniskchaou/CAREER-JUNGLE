<?php
/**
 * Video Testimonial Shortcode
 * @return html
 */
add_shortcode('careerfy_video_testimonial', 'careerfy_video_testimonial_shortcode');

function careerfy_video_testimonial_shortcode($atts, $content = '')
{
    global $vid_testimonial_shortcode_counter, $vid_testimonial_view;

    extract(shortcode_atts(array(
        'vid_testimonial_view' => 'view1'), $atts));
    $rand_num = rand(1000000, 9999999);

    $vid_testimonial_shortcode_counter = 1;
    if ($vid_testimonial_view == 'view1') {
        $vid_testimonial_class = 'careerfy-services-video careerfy-banner-' . $rand_num;
    } else if($vid_testimonial_view == 'view2') {
        $vid_testimonial_class = 'careerfy-services-video careerfy-services-video-two careerfy-banner-' . $rand_num;
    } else {
        $vid_testimonial_class = 'careerfy-video-testimonial-slider testimonial-slider-'. $rand_num;

    }

    ob_start();

    ?>
    <div id="careerfy-slidmaintop-<?php echo($rand_num) ?>" style="position: relative; float: left; width: 100%;">
        <div id="careerfy-slidloder-<?php echo($rand_num) ?>" class="careerfy-slidloder-section">
            <div class="ball-scale-multiple">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>

        <div class="<?php echo($vid_testimonial_class) ?>">
            <?php echo do_shortcode($content) ?>
        </div>
        <script type="text/javascript">
            var $ = jQuery;
            $(document).ready(function () {
                jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').css({'height': 'auto'});
                jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').find('.careerfy-services-video-layer').css({'display': 'inline-block'});

                <?php if($vid_testimonial_view == 'view3'){ ?>
                $('.testimonial-slider-<?php echo($rand_num) ?>').slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 5000,
                    infinite: true,
                    dots: true,
                    arrows: false,
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 1,
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
                <?php } else { ?>
                $('.careerfy-banner-<?php echo($rand_num) ?>').slick({
                    slidesToShow: 4,
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
                                slidesToShow: 3,
                                slidesToScroll: 1,
                                infinite: true
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
                <?php } ?>
                var remSlidrLodrInt<?php echo($rand_num) ?> = setInterval(function () {
                    jQuery('#careerfy-slidloder-<?php echo($rand_num) ?>').remove();
                    clearInterval(remSlidrLodrInt<?php echo($rand_num) ?>);
                }, 1500);
                //

                var slidrHightInt<?php echo($rand_num) ?> = setInterval(function () {
                    jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').css({'height': 'auto'});
                    jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').find('.careerfy-services-video-layer').css({'display': 'inline-block'});

                    var slider_act_height_<?php echo($rand_num) ?> = jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').height();

                    var filtr_cname_<?php echo($rand_num) ?> = 'careerfy_vidtests_slidr_lheight';
                    var c_date_<?php echo($rand_num) ?> = new Date();
                    c_date_<?php echo($rand_num) ?>.setTime(c_date_<?php echo($rand_num) ?>.getTime() + (60 * 60 * 1000));
                    var c_expires_<?php echo($rand_num) ?> = "; c_expires=" + c_date_<?php echo($rand_num) ?>.toGMTString();
                    document.cookie = filtr_cname_<?php echo($rand_num) ?> + "=" + slider_act_height_<?php echo($rand_num) ?> + c_expires_<?php echo($rand_num) ?> + "; path=/";

                    clearInterval(slidrHightInt<?php echo($rand_num) ?>);
                }, 2000);
            });
            jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').find('.careerfy-services-video-layer').css({'display': 'none'});

            var slider_height_<?php echo($rand_num) ?> = '<?php echo(isset($_COOKIE['careerfy_vidtests_slidr_lheight']) && $_COOKIE['careerfy_vidtests_slidr_lheight'] != '' ? $_COOKIE['careerfy_vidtests_slidr_lheight'] . 'px' : '300px') ?>';
            jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').css({'height': slider_height_<?php echo($rand_num) ?>});
        </script>
    </div>
    <?php
    $html = ob_get_clean();

    return $html;
}

add_shortcode('careerfy_video_testimonial_item', 'careerfy_video_testimonial_item_shortcode');

function careerfy_video_testimonial_item_shortcode($atts)
{
    global $vid_testimonial_shortcode_counter, $vid_testimonial_img, $vid_testimonial_view ;

    extract(shortcode_atts(array(
        'vid_testimonial_img' => '',
        'client_title' => '',
        'rank_title' => '',
        'vid_url' => '',
        'company_title' => '',
        'client_location' => '',
        'testimonial_desc' => '',
    ), $atts));

    $vid_testimonial_img = $vid_testimonial_img != '' ? $vid_testimonial_img : '';
    $client_title = $client_title != '' ? $client_title : '';
    $rank_title = $rank_title != '' ? $rank_title : '';

    $gal_video_url = $vid_url;
    if ($gal_video_url != '') {
        if (strpos($gal_video_url, 'watch?v=') !== false) {
            $gal_video_url = str_replace('watch?v=', 'embed/', $gal_video_url);
        }
        if (strpos($gal_video_url, '?') !== false) {
            $gal_video_url .= '&autoplay=1';
        } else {
            $gal_video_url .= '?autoplay=1';
        }
    }

    if ($vid_testimonial_view == 'view2') {
        $html = ' <div class="careerfy-services-video-layer">
                     <figure><a href="' . ($gal_video_url != '' ? $gal_video_url : 'javascript:void(0);') . '" class="' . ($gal_video_url != '' ? 'fancybox-video' : 'vid-testimonial') . '"' . ($gal_video_url != '' ? ' data-fancybox-type="iframe" data-fancybox-group="group"' : '') . '><img src="' . $vid_testimonial_img . '" alt=""><span><i class="fa fa-play-circle"></i></span></a></figure>
                     <h2><a href="#">' . $client_title . '</a></h2>
                     <p>' . $rank_title . '<br> ' . $company_title . '</p>
                  </div>';
    } else if ($vid_testimonial_view == 'view1') {
        $html = '
            <div class="careerfy-services-video-layer">
                <figure><a href="' . ($gal_video_url != '' ? $gal_video_url : 'javascript:void(0);') . '" class="' . ($gal_video_url != '' ? 'fancybox-video' : 'vid-testimonial') . '"' . ($gal_video_url != '' ? ' data-fancybox-type="iframe" data-fancybox-group="group"' : '') . '><img src="' . $vid_testimonial_img . '" alt=""><span><i class="fa fa-play-circle"></i></span></a></figure>
                <h2><a href="#">' . $client_title . '</a></h2>
                <p>' . $rank_title . '</p>
            </div>';
    } else {
        $html = '<div class="careerfy-testimonial-slider-layer">
                    <div class="careerfy-testimonial-style11-slider">
                    <i class="careerfy-icon careerfy-phrase"></i>
                    <p>'.$testimonial_desc.'</p>
                     <figure>
                      <a href="#" tabindex="-1"><img src="'.$vid_testimonial_img.'" alt=""></a>
                      <figcaption>
                       <h2>'.$client_title.'</h2>
                       <span>'.$client_location.'</span>
                      </figcaption>
                    </figure>
                   </div>
                   
                <div class="careerfy-testimonial-video-wrap">
                <a href="' . ($gal_video_url != '' ? $gal_video_url : 'javascript:void(0);') . '" class="' . ($gal_video_url != '' ? 'fancybox-video' : 'vid-testimonial') . '"' . ($gal_video_url != '' ? ' data-fancybox-type="iframe" data-fancybox-group="group"' : '') . '><img src="' . $vid_testimonial_img . '" alt=""><span class="vid-testimonial-icon"><i class="fa fa-play-circle"></i></span></a>
                </div>
                
            </div>';
    }
    $vid_testimonial_shortcode_counter++;
    return $html;
}
