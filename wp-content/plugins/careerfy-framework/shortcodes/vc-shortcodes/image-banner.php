<?php
/**
 * Image Banner Shortcode
 * @return html
 */
add_shortcode('careerfy_image_banner', 'careerfy_image_banner_shortcode');

function careerfy_image_banner_shortcode($atts) {
    extract(shortcode_atts(array(
        'b_bgimg' => '',
        'b_title' => '',
        'b_subtitle' => '',
        'b_desc' => '',
        'btn1_txt' => '',
        'btn1_url' => '',
        'btn2_txt' => '',
        'btn2_url' => '',
                    ), $atts));

    ob_start();
    ?>
    <div class="careerfy-banner-four careerfy-typo-wrap" <?php echo ($b_bgimg != '' ? 'style="background-image: url(\'' . $b_bgimg . '\');"' : '') ?>>
        <div class="container">
            <div class="careerfy-bannerfour-caption">
                <h1><span><?php echo ($b_title) ?></span> <?php echo ($b_subtitle) ?></h1>
                <?php
                if ($b_desc != '') {
                    echo '<p>' . $b_desc . '</p>';
                }
                if ($btn1_txt != '' || $btn2_txt != '') { ?>
                    <ul>
                        <?php
                        if ($btn1_txt != '') {
                            ?>
                            <li><a href="<?php echo ($btn1_url) ?>" class="banner-four-btn"><?php echo ($btn1_txt) ?> <i class="careerfy-icon careerfy-arrow-pointing-to-right"></i></a></li>
                                    <?php
                                }
                                if ($btn1_txt != '' && $btn2_txt != '') {
                                    ?>
                            <li><?php esc_html_e("OR", "careerfy-frame") ?></li>
                            <?php
                        }
                        if ($btn2_txt != '') {
                            ?>
                            <li><a href="<?php echo ($btn2_url) ?>"><?php echo ($btn2_txt) ?></a></li>
                            <?php
                        }
                        ?>
                    </ul>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <?php
    $html = ob_get_clean();

    return $html;
}
