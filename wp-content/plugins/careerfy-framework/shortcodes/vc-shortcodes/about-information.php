<?php
/**
 * About Info Shortcode
 * @return html
 */
add_shortcode('careerfy_about_info', 'careerfy_about_info_shortcode');

function careerfy_about_info_shortcode($atts)
{
    extract(shortcode_atts(array(
        'abt_info_title' => '',
        'abt_sub_title' => '',
        'abt_name' => '',
        'abt_experi' => '',
        'abt_desc' => '',
        'abt_social_links' => '',
    ), $atts));

    ob_start();
    ?>
    <div class="careerfy-team-parallex">
        <?php
        if ($abt_sub_title != '') {
            ?>
            <span><?php echo($abt_sub_title) ?></span>
            <?php
        }
        if ($abt_info_title != '') {
            ?>
            <h2><?php echo($abt_info_title) ?></h2>
            <?php
        }
        if ($abt_name != '' || $abt_experi != '') {
            ?>
            <h3><?php echo($abt_name) ?>
                <small><?php echo($abt_experi) ?></small>
            </h3>
            <?php
        }
        if ($abt_desc != '') {
            ?>
            <p><?php echo($abt_desc) ?></p>
            <?php
        }
        ?>
        <?php
        if (function_exists('vc_param_group_parse_atts')) {
            $abt_social_links = vc_param_group_parse_atts($abt_social_links);
        }
        if (!empty($abt_social_links)) {
            ?>
            <ul>
                <?php
                foreach ($abt_social_links as $social_link) {
                    $abt_soc_icon = isset($social_link['abt_soc_icon']) ? $social_link['abt_soc_icon'] : '';
                    $abt_soc_link = isset($social_link['abt_soc_link']) ? $social_link['abt_soc_link'] : '';

                    if ($abt_soc_icon != '') {
                        ?>
                        <li>
                            <a href="<?php echo($abt_soc_link) ?>" class="<?php echo($abt_soc_icon) ?>"></a>
                        </li>
                        <?php
                    }
                }
                ?>
            </ul>
            <?php
        }
        ?>
    </div>
    <?php
    $html = ob_get_clean();

    return $html;
}
