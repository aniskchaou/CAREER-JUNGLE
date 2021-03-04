<?php

add_action('wp_footer', function() {
    ob_start();
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function () {
        'use strict';

        // Target your .container, .wrapper, .post, etc.
        jQuery(".careerfy-wrapper").fitVids();

        if (careerfy_framework_vars.is_sticky == 'on') {
            var scrolDifrPixel = 170;
            var is_front_page = careerfy_framework_vars.is_front_page;
            if (jQuery('#careerfy-header').hasClass('careerfy-header-twelve')) {
                if (is_front_page == 'true') {
                    scrolDifrPixel = 650;
                } else {
                    scrolDifrPixel = 230;
                }
            }
            jQuery(window).scroll(function () {
                if (jQuery(this).scrollTop() > scrolDifrPixel) {
                    jQuery('body').addClass("careerfy-sticky-header");
                } else {
                    jQuery('body').removeClass("careerfy-sticky-header");
                }
            });
        }

        if (jQuery('.word-counter').length > 0) {
            jQuery('.word-counter').countUp({
                delay: 190,
                time: 3000,
            });
        }

        if (jQuery('.careerfy_twitter_widget_wrap').length > 0) {
            jQuery('.careerfy_twitter_widget_wrap').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 5000,
                infinite: true,
                dots: false,
                prevArrow: "",
                nextArrow: "",
            });
        }

        //*** Function Banner
        if (jQuery('.careerfy-testimonial-slider').length > 0) {
            jQuery('.careerfy-testimonial-slider').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 5000,
                infinite: true,
                dots: false,
                prevArrow: "<span class='slick-arrow-left'><i class='careerfy-icon careerfy-arrow-right-bold'></i></span>",
                nextArrow: "<span class='slick-arrow-right'><i class='careerfy-icon careerfy-arrow-right-bold'></i></span>",
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
        }

        //*** Function Services Slider
        if (jQuery('.careerfy-service-slider').length > 0) {
            jQuery('.careerfy-service-slider').slick({
                slidesToShow: 5,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 5000,
                infinite: true,
                dots: false,
                centerMode: true,
                centerPadding: '0px',
                prevArrow: "<span class='slick-arrow-left'><i class='careerfy-icon careerfy-arrow-right-bold'></i></span>",
                nextArrow: "<span class='slick-arrow-right'><i class='careerfy-icon careerfy-arrow-right-bold'></i></span>",
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
                            slidesToShow: 3,
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
        }

        //*** Function Partner Slider
        if (jQuery('.careerfy-partner-slider').length > 0) {
            jQuery('.careerfy-partner-slider').slick({
                slidesToShow: 6,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 5000,
                infinite: true,
                dots: false,
                centerMode: true,
                centerPadding: '0px',
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
        }

        if (jQuery('.careerfy-partnertwo-slider').length > 0) {
            jQuery('.careerfy-partnertwo-slider').slick({
                slidesToShow: 6,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 5000,
                infinite: true,
                dots: false,
                prevArrow: "<span class='slick-arrow-left'><i class='careerfy-icon careerfy-arrow-pointing-to-left'></i></span>",
                nextArrow: "<span class='slick-arrow-right'><i class='careerfy-icon careerfy-arrow-pointing-to-right'></i></span>",
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 1,
                            infinite: true,
                        }
                    },
                    {
                        breakpoint: 1250,
                        settings: {
                            slidesToShow: 4,
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
        }

        if (jQuery('.careerfy-testimonial-styletwo').length > 0) {
            jQuery('.careerfy-testimonial-styletwo').slick({
                slidesToShow: 2,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 5000,
                infinite: true,
                dots: true,
                prevArrow: "<span class='slick-arrow-left'><i class='careerfy-icon careerfy-right-arrow-long'></i></span>",
                nextArrow: "<span class='slick-arrow-right'><i class='careerfy-icon careerfy-right-arrow-long'></i></span>",
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
        }

        if (jQuery('.careerfy-testimonial-slider-style3').length > 0) {
            jQuery('.careerfy-testimonial-slider-style3').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 5000,
                infinite: true,
                dots: true,
                fade: true,
                adaptiveHeight: true,
                prevArrow: jQuery('.careerfy-prev'),
                nextArrow: jQuery('.careerfy-next'),
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
        }

        if (jQuery('.careerfy-testimonial-style4').length > 0) {
            jQuery('.careerfy-testimonial-style4').slick({
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
        }

        if (jQuery('.careerfy-partner-style3').length > 0) {
            jQuery('.careerfy-partner-style3').slick({
                slidesToShow: 6,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 5000,
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
        }

        jQuery(".careerfy-loading-section").fadeOut("slow");
    });
    </script>
    <?php
    $html = ob_get_clean();
    
    echo ($html);
}, 999);