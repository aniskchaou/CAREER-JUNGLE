<?php
/**
 * Block Text Box Shortcode
 * @return html
 */
add_shortcode('careerfy_block_text_box', 'careerfy_block_text_box_shortcode');

function careerfy_block_text_box_shortcode($atts, $content = '')
{

    extract(shortcode_atts(array(
        'title' => '',
        'bg_img' => '',
        'bg_color' => '',
        'btn_txt' => '',
        'btn_url' => '',
        'video_url' => '',
        'poster_img' => '',
    ), $atts));

    ob_start();

    $style_str = '';
    if ($bg_img != '') {
        $style_str .= 'background-image: url(' . $bg_img . ');';
    }
    if ($bg_color != '') {
        $style_str .= 'background-color: ' . $bg_color . ';';
    }
    ?>
    <div class="row">
        <div class="col-md-6 careerfy-parallex-box" <?php echo($style_str != '' ? 'style="' . $style_str . '"' : '') ?>>
            <div class="careerfy-parallex-box-wrap">
                <h2><?php echo($title) ?></h2>
                <?php
                if ($content != '') {
                    ?>
                    <p><?php echo($content) ?></p>
                    <?php
                }
                if ($btn_txt != '') {
                    ?>
                    <a href="<?php echo($btn_url) ?>" class="careerfy-parallex-box-btn"><?php echo($btn_txt) ?></a>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php if ($video_url != '') {
            wp_enqueue_script('careerfy-mediaelement');
            ?>
            <div class="col-md-6 careerfy-media-player">
                <video src="<?php echo($video_url) ?>" poster="<?php echo($poster_img) ?>" controls="controls"
                       preload=""></video>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('video').mediaelementplayer({
                        success: function (player, node) {
                            jQuery('#' + node.id + '-mode').html('mode: ' + player.pluginType);
                        }
                    });
                });
            </script>
        <?php } ?>
    </div>
    <?php

    $html = ob_get_clean();
    return $html;
}
