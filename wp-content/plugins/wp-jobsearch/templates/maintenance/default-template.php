<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package JobSearch
 */
if (wp_is_mobile()) {
    get_header('mobile');
} else {
    get_header();
}
global $jobsearch_plugin_options;
$maintenance_mode_text_small_title = isset($jobsearch_plugin_options['maintenance-mode-text-small-title']) ? $jobsearch_plugin_options['maintenance-mode-text-small-title'] : '';
$maintenance_mode_text_large_title = isset($jobsearch_plugin_options['maintenance-mode-text-large-title']) ? $jobsearch_plugin_options['maintenance-mode-text-large-title'] : '';
$maintenance_mode_text_content = isset($jobsearch_plugin_options['maintenance-mode-text-content']) ? $jobsearch_plugin_options['maintenance-mode-text-content'] : '';
$maintenance_mode_date = isset($jobsearch_plugin_options['maintenance-mode-date']) ? $jobsearch_plugin_options['maintenance-mode-date'] : '';
$maintenance_mode_time = isset($jobsearch_plugin_options['maintenance-mode-time']) ? $jobsearch_plugin_options['maintenance-mode-time'] : '';
$maintenance_background = isset($jobsearch_plugin_options['maintenance-background']['url']) && $jobsearch_plugin_options['maintenance-background']['url'] != '' ? $jobsearch_plugin_options['maintenance-background']['url'] : '';
$maintenance_background_color = isset($jobsearch_plugin_options['maintenance-background-color']) ? $jobsearch_plugin_options['maintenance-background-color'] : '';
$maintenance_background_color_str = '';
$maintenance_style_str = '';
if (isset($maintenance_background_color['rgba'])) {
    $maintenance_background_color = $maintenance_background_color['rgba'];
}
if ($maintenance_background_color != '') {
    $maintenance_background_color_str .= 'background-color: ' . $maintenance_background_color . ' !important;';
}
$maintenance_background_str = '';

if ($maintenance_background != '') {
    $maintenance_background_str = ' background-image: url(\'' . $maintenance_background . '\');';
}
if ($maintenance_background != '' || $maintenance_background_color_str != '') {
    $maintenance_style_str = 'style="' . $maintenance_background_str . ' ' . $maintenance_background_color_str . '"';
}
$maintenance_mode_datetime = '0000-00-00 00:00';
if (isset($maintenance_mode_date) && $maintenance_mode_date != '') {
    $maintenance_mode_datetime = date("Y-m-d", strtotime($maintenance_mode_date));
    if (isset($maintenance_mode_time) && $maintenance_mode_time != '') {
        $maintenance_mode_datetime = $maintenance_mode_datetime . ' ' . date("H:i", strtotime($maintenance_mode_time));
    } else {
        $maintenance_mode_datetime = $maintenance_mode_datetime . ' 00:00';
    }
}
$rand_num = rand(1000000, 99999999);
?>
<div class="jobsearch-plugin-section jobsearch-coming-soon" <?php echo force_balance_tags($maintenance_style_str); ?>>
    <span class="jobsearch-transparent"></span>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="jobsearch-comingsoon-text">
                    <?php
                    if ($maintenance_mode_text_small_title != '' || $maintenance_mode_text_large_title != '') {
                        ?><h2><?php if ($maintenance_mode_text_small_title != '') { ?><span><?php echo esc_html($maintenance_mode_text_small_title); ?></span> <?php
                            }
                            if ($maintenance_mode_text_large_title != '') {
                                echo esc_html($maintenance_mode_text_large_title);
                                ?></h2>
                            <?php
                        }
                    }
                    if ($maintenance_mode_text_content != '') {
                        echo force_balance_tags($maintenance_mode_text_content);
                    }
                    jobsearch_custom_mailchimp(); ?> 
                    <div id="jobsearch-comingsoon-<?php echo absint($rand_num) ?>" class="jobsearch-comingsoon-countdown"></div>
                    <?php
                    if ($maintenance_mode_datetime != '') {
                        ?>
                        <script>
                            jQuery(document).ready(function ($) {
                                jQuery(function () {
                                    var austDay = new Date(<?php echo date_i18n('Y', strtotime($maintenance_mode_datetime)) ?>, <?php echo date_i18n('m', strtotime($maintenance_mode_datetime)) ?> - 1, <?php echo date_i18n('d', strtotime($maintenance_mode_datetime)) ?>, <?php echo date_i18n('H', strtotime($maintenance_mode_datetime)) ?>, <?php echo date_i18n('i', strtotime($maintenance_mode_datetime)) ?>, <?php echo date_i18n('s', strtotime($maintenance_mode_datetime)) ?>);
                                    jQuery('#jobsearch-comingsoon-<?php echo absint($rand_num) ?>').countdown({
                                        until: austDay
                                    });
                                });
                            });
                        </script>
                        <?php
                    }
                    ?>  
                </div>     
            </div>
        </div>
    </div>
</div> 
<?php
get_footer();
