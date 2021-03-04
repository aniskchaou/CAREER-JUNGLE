<?php
/**
 * Slider Shortcode
 * @return html
 */
add_shortcode('careerfy_slider', 'careerfy_slider_shortcode');

function careerfy_slider_shortcode($atts, $content = '')
{
    global $slider_shortcode_counter, $slider_view, $careerfy_framework_options;
    extract(shortcode_atts(array(
        'slider_view' => 'view1',
    ), $atts));
    $rand_num = rand(1000000, 9999999);
    $slider_shortcode_counter = 1;
    
    $careerfy_site_loader = isset($careerfy_framework_options['careerfy-site-loader']) ? $careerfy_framework_options['careerfy-site-loader'] : '';

    if ($slider_view == 'view1') {

        $slider_class = 'careerfy-banner-nine careerfy-banner-' . $rand_num;

    } else {

        $slider_class = 'careerfy-seventeen-banner careerfy-banner-' . $rand_num;
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
        <div class="<?php echo($slider_class) ?>">
            <?php echo do_shortcode($content) ?>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                //*** Function Banner

                jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').css({'height': 'auto'});
                jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').find('.careerfy-bannernine-layer').css({'display': 'inline-block'});
                <?php if($slider_view == 'view1'){ ?>
                jQuery('.careerfy-banner-<?php echo($rand_num) ?>').slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 5000,
                    infinite: true,
                    dots: false,
                    arrows: false,
                    fade: true,
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
                <?php } else { ?>
                jQuery('.careerfy-banner-<?php echo($rand_num) ?>').slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    infinite: true,
                    dots: false,
                    arrows: false,
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
                <?php } ?>
                var remSlidrLodrInt<?php echo($rand_num) ?> = setInterval(function () {
                    jQuery('#careerfy-slidloder-<?php echo($rand_num) ?>').remove();
                    clearInterval(remSlidrLodrInt<?php echo($rand_num) ?>);
                }, 1500);

                var slidrHightInt<?php echo($rand_num) ?> = setInterval(function () {
                    jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').css({'height': 'auto'});
                    jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').find('.careerfy-bannernine-layer').css({'display': 'inline-block'});
                    var slider_act_height_<?php echo($rand_num) ?> = jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').height();

                    var filtr_cname_<?php echo($rand_num) ?> = 'careerfy_main_slider_lheight';
                    var c_date_<?php echo($rand_num) ?> = new Date();
                    c_date_<?php echo($rand_num) ?>.setTime(c_date_<?php echo($rand_num) ?>.getTime() + (60 * 60 * 1000));
                    var c_expires_<?php echo($rand_num) ?> = "; c_expires=" + c_date_<?php echo($rand_num) ?>.toGMTString();
                    document.cookie = filtr_cname_<?php echo($rand_num) ?> + "=" + slider_act_height_<?php echo($rand_num) ?> + c_expires_<?php echo($rand_num) ?> + "; path=/";
                    clearInterval(slidrHightInt<?php echo($rand_num) ?>);
                }, 2000);
            });
            jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').find('.careerfy-bannernine-layer').css({'display': 'none'});

            var slider_height_<?php echo($rand_num) ?> = '<?php echo(isset($_COOKIE['careerfy_main_slider_lheight']) && $_COOKIE['careerfy_main_slider_lheight'] != '' ? $_COOKIE['careerfy_main_slider_lheight'] . 'px' : '300px') ?>';
            jQuery('#careerfy-slidmaintop-<?php echo($rand_num) ?>').css({'height': slider_height_<?php echo($rand_num) ?>});
        </script>
    </div>
    <?php

    $html = ob_get_clean();
    return $html;
}

add_shortcode('careerfy_slider_item', 'careerfy_slider_item_shortcode');

function careerfy_slider_item_shortcode($atts)
{
    global $slider_shortcode_counter, $slider_view;
    extract(shortcode_atts(array(
        'slider_img' => '',
        'tiny_title' => '',
        'small_title' => '',
        'big_title' => '',
        'slider_desc' => '',
        'button_bg_color' => '',
        'button_title' => '',
        'button_url' => '',
        'button_title_color' => '',
    ), $atts));

    $slider_img = $slider_img != "" ? $slider_img : '';
    $tiny_title = $tiny_title != "" ? $tiny_title : '';
    $small_title = $small_title != "" ? $small_title : '';
    $big_title = $big_title != "" ? $big_title : '';
    $slider_desc = $slider_desc != "" ? $slider_desc : '';
    $button_bg_color = $button_bg_color != "" ? $button_bg_color : '';
    $button_title = $button_title != "" ? $button_title : '';
    $button_url = $button_url != "" ? $button_url : '';
    $button_title_color = $button_title_color != "" ? $button_title_color : '';

    if ($slider_view == 'view1') {
        $html = '
    <div class="careerfy-bannernine-layer">
        <div class="careerfy-bannernine-caption">
            <div class="container">
                <div class="careerfy-bannernine-caption-inner">
                    <span>' . $tiny_title . '</span>
                    <h1>' . $small_title . '</h1>
                    <h2>' . $big_title . '</h2>
                    <p>' . $slider_desc . '</p>
                    <a href="' . $button_url . '" style="background-color: ' . $button_bg_color . '; color: ' . $button_title_color . '">' . $button_title . '</a>
                </div>
            </div>
        </div>
        <div class="careerfy-bannernine-thumb"> <img src="' . $slider_img . '" alt=""> </div>
    </div>';
    } else {
        $html = '<div class="careerfy-seventeen-banner-layer">
                    <img src="' . $slider_img . '" alt="">
                    <div class="careerfy-seventeen-banner-caption">
                    <div class="container">
                        <div class="careerfy-seventeen-banner-caption-inner">
                            <h1>' . $big_title . '</h1>
                            <p>' . $slider_desc . '</p>
                            <a href="' . $button_url . '" style="background-color: ' . $button_bg_color . '; color: ' . $button_title_color . '" class="careerfy-seventeen-banner-btn">' . $button_title . '</a>
                        </div>
                    </div>
                </div>
            </div>';
    }

    $slider_shortcode_counter++;
    return $html;
}
