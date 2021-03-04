<?php
/**
 * The template for displaying the footer.
 *
 * @package Careerfy
 */
$careerfy__options = careerfy_framework_options();

$footer_style = isset($careerfy__options['footer-style']) ? $careerfy__options['footer-style'] : '';

if (!(class_exists('Careerfy_MMC') && true == Careerfy_MMC::is_construction_mode_enabled(false))) {
    ?>
    <!--// Footer \\-->
    <footer id="careerfy-footer" class="<?php echo careerfy_footer_class() ?>">
        <?php

        if ($footer_style != 'style5' && $footer_style != 'style17') { ?>
        <div class="container">
            <?php
            }
            do_action('careerfy_footer_top_secion');

            // widget area
            do_action('careerfy_footer_upper_sec');
            // copyright 
            if ($footer_style != 'style5' && $footer_style != 'style7' && $footer_style != 'style8' && $footer_style != 'style9' && $footer_style != 'style10' && $footer_style != 'style12' && $footer_style != 'style15') {
                do_action('careerfy_footer_bottom_sec');
            }

            if ($footer_style != 'style5' && $footer_style != 'style17') { ?>
        </div>
    <?php } ?>

        <?php
        if ($footer_style == 'style5' || $footer_style == 'style7' || $footer_style == 'style8' || $footer_style == 'style9' || $footer_style == 'style10' || $footer_style == 'style12' || $footer_style == 'style15') {
            do_action('careerfy_footer_bottom_sec');
        }
        ?>
    </footer>
    <!--// Footer \\-->
    <div class="clearfix"></div>
<?php } ?>
</div>
<!--// Main Wrapper \\-->
<?php
do_action('careerfy_footer_extra_content');
wp_footer();
?>
</body>
</html>
