<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Careerfy
 */
if (wp_is_mobile()) {
    get_header('mobile');
} else {
    get_header();
}
global $careerfy_framework_options;
$page_id = get_the_ID();
$maintenance_mode_text_small_title = isset($careerfy_framework_options['maintenance-mode-text-small-title']) ? $careerfy_framework_options['maintenance-mode-text-small-title'] : '';
$maintenance_mode_text_large_title = isset($careerfy_framework_options['maintenance-mode-text-large-title']) ? $careerfy_framework_options['maintenance-mode-text-large-title'] : '';
$maintenance_mode_text_content = isset($careerfy_framework_options['maintenance-mode-text-content']) ? $careerfy_framework_options['maintenance-mode-text-content'] : '';
$maintenance_mode_date = isset($careerfy_framework_options['maintenance-mode-date']) ? $careerfy_framework_options['maintenance-mode-date'] : '';
$maintenance_mode_time = isset($careerfy_framework_options['maintenance-mode-time']) ? $careerfy_framework_options['maintenance-mode-time'] : '';

$maintenance_social_network = isset($careerfy_framework_options['maintenance-social-icons']) ? $careerfy_framework_options['maintenance-social-icons'] : '';

$maintenance_mode_datetime = '0000-00-00 00:00';
if (isset($maintenance_mode_date) && $maintenance_mode_date != '') {
    $maintenance_mode_datetime = date("Y-m-d", strtotime($maintenance_mode_date));
    if (isset($maintenance_mode_time) && $maintenance_mode_time != '') {
        $maintenance_mode_datetime = $maintenance_mode_datetime . ' ' . date("H:i", strtotime($maintenance_mode_time));
    } else {
        $maintenance_mode_datetime = $maintenance_mode_datetime . ' 00:00';
    }
}

//
$maintenance_mode_pagemeta_switch = isset($careerfy_framework_options['maintenance-mode-pagemeta-switch']) ? $careerfy_framework_options['maintenance-mode-pagemeta-switch'] : '';
if ($maintenance_mode_pagemeta_switch && $page_id != '') {
    $page_comming_soon_switch = get_post_meta($page_id, 'careerfy_field_maintenance_mode_comming_soon', true);
    if ($page_comming_soon_switch == 'on') {
        $maintenance_mode_text_small_title = get_post_meta($page_id, 'careerfy_field_maintnanace_smalltitle', true);
        $maintenance_mode_text_large_title = get_post_meta($page_id, 'careerfy_field_maintnanace_largtitle', true);
        $maintenance_mode_text_content = get_post_meta($page_id, 'careerfy_field_maintnanace_content', true);
        $maintenance_mode_datetime = get_post_meta($page_id, 'careerfy_field_maintnanace_endtime', true);
        $maintenance_social_network = get_post_meta($page_id, 'careerfy_field_maintnanace_social_ntwork', true);
    }
}
//

$rand_num = rand(1000000, 99999999);
wp_enqueue_script('careerfy-countdown');
?>
<link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,400i,700,700i,900,900i" rel="stylesheet">
<div class="careerfy-main-section careerfy-coming-soon">
    <div class="container">
        <div class="careerfy-comingsoon-wrap">
            <div class="careerfy-comingsoon-text">
                <?php
                if ($maintenance_mode_text_small_title != '' || $maintenance_mode_text_large_title != '') {?>
                    <h2>
                        <?php
                        if ($maintenance_mode_text_small_title != '') { ?><span><?php echo esc_html($maintenance_mode_text_small_title); ?></span> <?php }
                        if ($maintenance_mode_text_large_title != '') {
                            echo esc_html($maintenance_mode_text_large_title);
                        }
                        ?>
                    </h2>
                    <?php
                }
                if ($maintenance_mode_text_content != '') {
                    ?>
                    <p><?php echo force_balance_tags($maintenance_mode_text_content); ?></p>
                    <div class="clearfix"></div>
                    <?php
                }
                if ($maintenance_mode_datetime != '') {
                    ?>
                    <div id="careerfy-comingsoon-<?php echo absint($rand_num) ?>" class="careerfy-comingsoon-countdown"></div>
                    <div class="clearfix"></div>
                    <?php
                }
                careerfy_custom_mailchimp(); ?>
                <?php
                if ($maintenance_mode_datetime != '') { ?>
                    <script>
                        jQuery(document).ready(function ($) {
                            jQuery(function () {
                                var austDay = new Date(<?php echo date_i18n('Y', strtotime($maintenance_mode_datetime)) ?>, <?php echo date_i18n('m', strtotime($maintenance_mode_datetime)) ?> - 1, <?php echo date_i18n('d', strtotime($maintenance_mode_datetime)) ?>, <?php echo date_i18n('H', strtotime($maintenance_mode_datetime)) ?>, <?php echo date_i18n('i', strtotime($maintenance_mode_datetime)) ?>, <?php echo date_i18n('s', strtotime($maintenance_mode_datetime)) ?>);
                                jQuery('#careerfy-comingsoon-<?php echo absint($rand_num) ?>').countdown({
                                    until: austDay
                                });
                            });
                        });
                    </script>
                    <?php
                }
                ?>
                <div class="clearfix"></div>
                <?php
                if ($maintenance_social_network == 'on') {
                    do_action('careerfy_social_icons', '', '');
                }
                ?>
            </div>
        </div>
    </div>
</div> 
<?php
get_footer();
