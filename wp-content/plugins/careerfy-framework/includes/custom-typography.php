<?php

add_action('wp_head', 'careerfy_custom_typo_style', 98);
function careerfy_custom_typo_style() {
    global $careerfy_theme_options;
    $fancy_title_typo = isset($careerfy_theme_options['fancy-title-typo']) ? $careerfy_theme_options['fancy-title-typo'] : '';

    ?>
    <style>
    <?php
    if (isset($fancy_title_typo['color']) && $fancy_title_typo['color'] != '') { ?>
            .careerfy-fancy-title .after-border:before {
                background-color: <?php echo ($fancy_title_typo['color']) ?>;
            }
        <?php
    }
    ?>
    </style>
    <?php
}
