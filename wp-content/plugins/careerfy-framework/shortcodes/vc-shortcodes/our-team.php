<?php
/**
 * Our Team Shortcode
 * @return html
 */
add_shortcode('careerfy_our_team', 'careerfy_our_team_shortcode');

function careerfy_our_team_shortcode($atts, $content = '') {
    global $team_style;

    extract(shortcode_atts(array(
        'team_style' => '',
                    ), $atts));

    wp_enqueue_script('careerfy-slick-slider');

    $html = '';
    if ($team_style == 'style2') {

        //$html .='<div class="jobsearch-employer-wrap-section">';
        $html .= '<div class="jobsearch-candidate jobsearch-candidate-grid team-style-two">';
        $html .= '<ul class="jobsearch-row">';
        $html .= do_shortcode($content);
        $html .= '</ul>';
        $html .= '</div>';
        // $html .= '</div>';
    } else {
        $html .= '<div class="careerfy-service-slider">';
        $html .= do_shortcode($content);
        $html .= '</div>';
    }

    return $html;
}

add_shortcode('careerfy_our_team_item', 'careerfy_our_team_item_shortcode');

function careerfy_our_team_item_shortcode($atts) {
    global $team_style;
    extract(shortcode_atts(array(
        'team_img' => '',
        'team_title' => '',
        'team_pos' => '',
        'team_experience' => '',
        'team_biography' => '',
        'team_fb' => '',
        'team_google' => '',
        'team_twitter' => '',
        'team_linkedin' => '',
                    ), $atts));

    $html = '';
    if ($team_style == 'style2') {
        $rand_id = rand(1046, 78999);
        ob_start();
        ?>
        <li class="jobsearch-column-3">
            <script>
                jQuery(document).ready(function () {
                    jQuery('a[id^="fancybox_notes"]').fancybox({
                        'titlePosition': 'inside',
                        'transitionIn': 'elastic',
                        'transitionOut': 'elastic',
                        'width': 400,
                        'height': 250,
                        'padding': 40,
                        'autoSize': false
                    });
                });
            </script>
            <figure>
                <a id="fancybox_notes<?php echo ($rand_id); ?>" href="#notes<?php echo ($rand_id); ?>" class="jobsearch-candidate-grid-thumb">
                    <img src="<?php echo ($team_img); ?>" alt=""> 
                </a>
                <figcaption>
                    <h2><a id="fancybox_notes_txt<?php echo ($rand_id); ?>" href="#notes<?php echo ($rand_id); ?>"><?php echo ($team_title); ?></a></h2>
                    <?php if (isset($team_pos) && !empty($team_pos)) { ?>
                        <p><?php echo ($team_pos); ?></p>
                        <?php
                    }
                    if (isset($team_experience) && !empty($team_experience)) {
                        ?>
                        <span><?php printf(esc_html__('Experience: %s Years','careerfy-frame'),$team_experience );?></span>
                        <?php
                    }
                    ?>
                </figcaption>
            </figure>
            <?php
            if (isset($team_biography) && !empty($team_biography)) {
                ?>
                <div id="notes<?php echo ($rand_id); ?>" style="display: none;"><?php echo $team_biography ?></div>
                <?php
            }
            if (!empty($team_fb) || !empty($team_google) || !empty($team_twitter) || !empty($team_linkedin)) {
                ?>
                <ul class="jobsearch-social-icons">
                    <?php
                    if (isset($team_fb) && !empty($team_fb)) {
                        ?>
                        <li><a href="<?php echo $team_fb ?>" data-original-title="facebook" class="jobsearch-icon jobsearch-facebook-logo"></a></li>
                        <?php
                    }
                    if (isset($team_google) && !empty($team_google)) {
                        ?>
                        <li><a href="<?php echo $team_google ?>" data-original-title="google-plus" class="jobsearch-icon jobsearch-google-plus-logo-button"></a></li>
                        <?php
                    }
                    if (isset($team_twitter) && !empty($team_twitter)) {
                        ?>
                        <li><a href="<?php echo $team_twitter ?>" data-original-title="twitter" class="jobsearch-icon jobsearch-twitter-logo"></a></li>
                        <?php
                    }
                    if (isset($team_linkedin) && !empty($team_linkedin)) {
                        ?>
                        <li><a href="<?php echo $team_linkedin ?>" data-original-title="linkedin" class="jobsearch-icon jobsearch-linkedin-button"></a></li>
                            <?php
                        }
                        ?>
                </ul>
            <?php }
            ?>    
        </li>
        <?php
        $html .= ob_get_contents();
        ob_end_clean();
    } else {
        $html .= '<div class="careerfy-service-slider-layer">';
        $html .= '<a><img src="' . $team_img . '" alt=""></a>';
        $html .= '<span>' . $team_title . ' <small>' . $team_pos . '</small></span>';
        $html .= '</div>';
    }
    return $html;
}
