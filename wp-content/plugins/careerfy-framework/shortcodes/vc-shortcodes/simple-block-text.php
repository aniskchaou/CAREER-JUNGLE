<?php
/**
 * Simple Block Text Shortcode
 * @return html
 */
add_shortcode('careerfy_simple_block_text', 'careerfy_simple_block_text_shortcode');

function careerfy_simple_block_text_shortcode($atts, $content = '') {
    extract(shortcode_atts(array(
        'title' => '',
        'title_color' => '',
        'desc_color' => '',
        'btn_txt' => '',
        'btn_url' => '',
        'btn2_txt' => '',
        'btn2_url' => '',
                    ), $atts));

    ob_start();
    ?>
    <div class="careerfy-parallax-text-box">
        <h2<?php echo ($title_color != '' ? ' style="color: ' . $title_color . ';"' : '') ?>><?php echo ($title) ?></h2>
        <?php
        if ($content != '') {
            ?>
            <p<?php echo ($desc_color != '' ? ' style="color: ' . $desc_color . ';"' : '') ?>><?php echo ($content) ?></p>
            <?php
        }
        if ($btn_txt != '') {
            ?>
            <a href="<?php echo ($btn_url) ?>" class="careerfy-parallax-text-btn"><?php echo ($btn_txt) ?></a>
            <?php
        }
        if ($btn2_txt != '') {
            ?>
            <a href="<?php echo ($btn2_url) ?>" class="careerfy-parallax-text-btn"><?php echo ($btn2_txt) ?></a>
            <?php
        }
        ?>
    </div>
    <?php
    $html = ob_get_clean();

    return $html;
}
