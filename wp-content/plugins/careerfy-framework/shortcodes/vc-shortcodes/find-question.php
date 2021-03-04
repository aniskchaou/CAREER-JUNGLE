<?php
/**
 * Find Question Shortcode
 * @return html
 */
add_shortcode('careerfy_find_question', 'careerfy_find_question_shortcode');
function careerfy_find_question_shortcode($atts)
{
    extract(shortcode_atts(array(
        'search_box' => 'show',
        'search_title' => '',
        'search_desc' => '',
        'btn_txt' => '',
        'btn_url' => '',
    ), $atts));

    ob_start();

    $s_keyword = isset($_GET['keyword']) && $_GET['keyword'] != '' ? sanitize_text_field($_GET['keyword']) : '';
    if ($search_box == 'show') { ?>
        <div class="widget careerfy-search-form-widget">
            <form method="get">
                <label><?php esc_html_e("Find Your Question:", "careerfy-frame") ?></label>
                <input placeholder="<?php esc_html_e("Keyword", "careerfy-frame") ?>" type="text" value="<?php echo ($s_keyword) ?>" name="keyword">
                <input type="submit" value="">
                <i class="fa fa-search"></i>
            </form>
        </div>
    <?php } ?>
    <div class="widget widget-text-info">
        <h2 class="careerfy-slash-title"><?php echo($search_title) ?></h2>
        <p><?php echo($search_desc) ?></p>
        <a href="<?php echo($btn_url) ?>" class="careerfy-text-btn careerfy-bgcolor"><?php echo($btn_txt) ?></a>
    </div>
    <?php
    $html = ob_get_clean();
    return $html;
}
