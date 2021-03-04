<?php
/**
 * Contact Info Shortcode
 * @return html
 */
add_shortcode('careerfy_contact_info', 'careerfy_contact_info_shortcode');
function careerfy_contact_info_shortcode($atts)
{
    extract(shortcode_atts(array(
        'con_info_title' => '',
        'con_form_title' => '',
        'con_form_7' => '',
        'con_desc' => '',
        'con_address' => '',
        'con_email' => '',
        'con_phone' => '',
        'con_fax' => '',
        'social_links' => '',
    ), $atts));

    ob_start();
    ?>
    <div class="careerfy-contact-info-sec">
        <?php
        if ($con_info_title != '') {
            ?>
            <h2><?php echo($con_info_title) ?></h2>
            <?php
        }
        if ($con_desc != '') {
            ?>
            <p><?php echo($con_desc) ?></p>
            <?php
        }
        ?>
        <ul class="careerfy-contact-info-list">
            <?php
            if ($con_address != '') {
                ?>
                <li><i class="careerfy-icon careerfy-map-marker"></i> <?php echo esc_html($con_address) ?></li>
                <?php
            }
            if ($con_email != '') {
                ?>
                <li><i class="careerfy-icon careerfy-envelope"></i> <a
                            href="mailto:<?php echo($con_email) ?>"><?php echo esc_html__('Email: ', 'careerfy-frame') . $con_email ?></a>
                </li>
                <?php
            }
            if ($con_phone != '') { ?>
                <li>
                    <i class="careerfy-icon careerfy-technology"></i> <?php echo esc_html__('Call: ', 'careerfy-frame').$con_phone ?>
                </li>
                <?php
            }
            if ($con_fax != '') {
                ?>
                <li>
                    <i class="careerfy-icon careerfy-fax"></i> <?php echo esc_html__('Fax: ', 'careerfy-frame') . $con_fax ?>
                </li>
                <?php
            }
            ?>
        </ul>
        <?php

        if (function_exists('vc_param_group_parse_atts')) {
            $social_links = vc_param_group_parse_atts($social_links);
        }

        if (!empty($social_links)) { ?>
            <div class="careerfy-contact-media">
                <?php
                foreach ($social_links as $social_link) {
                    $soc_icon = isset($social_link['soc_icon']) ? $social_link['soc_icon'] : '';
                    $soc_link = isset($social_link['soc_link']) ? $social_link['soc_link'] : '';

                    if ($soc_icon != '') {
                        ?>
                        <a href="<?php echo($soc_link) ?>">
                            <i class="<?php echo($soc_icon) ?>"></i>
                        </a>
                        <?php
                    }
                }
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="careerfy-contact-form">
        <?php
        if ($con_form_title != '') {
            ?>
            <h2><?php echo($con_form_title) ?></h2>
            <?php
        }
        $cnt_counter = rand(1000000, 99999999);
        if (class_exists('WPCF7_ContactForm') && $con_form_7 != '') {
            $con_form_7_id = careerfy__get_post_id($con_form_7, 'wpcf7_contact_form');

            echo do_shortcode('[contact-form-7 id="' . $con_form_7_id . '" title="' . get_the_title($con_form_7_id) . '"]');
        } else {
            ob_start();
            ?>
            <form id="ct-form-<?php echo absint($cnt_counter) ?>"
                  data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')) ?>" method="post">
                <ul>
                    <li>
                        <input type="text" name="u_name"
                               placeholder="<?php esc_html_e('Enter Your Name', 'careerfy-frame') ?>">
                        <i class="careerfy-icon careerfy-user"></i>
                    </li>
                    <li>
                        <input placeholder="<?php esc_html_e('Subject', 'careerfy-frame') ?>" type="text"
                               name="u_subject">
                        <i class="careerfy-icon careerfy-user"></i>
                    </li>
                    <li>
                        <input placeholder="<?php esc_html_e('Enter Your Email Address', 'careerfy-frame') ?>"
                               type="text" name="u_email">
                        <i class="careerfy-icon careerfy-mail"></i>
                    </li>
                    <li>
                        <input placeholder="<?php esc_html_e('Enter Your Phone Number', 'careerfy-frame') ?>"
                               type="text" name="u_number">
                        <i class="careerfy-icon careerfy-technology"></i>
                    </li>
                    <li class="careerfy-contact-form-full">
                        <textarea name="u_msg"
                                  placeholder="<?php esc_html_e('Enter Your Message', 'careerfy-frame') ?>"></textarea>
                    </li>
                    <li>
                        <input type="submit" class="careerfy-ct-form" data-id="<?php echo absint($cnt_counter) ?>"
                               value="<?php esc_html_e('Submit', 'careerfy-frame') ?>">
                        <span class="careerfy-bt-msg careerfy-ct-msg"></span>
                        <input type="hidden" name="u_type" value="content"/>
                    </li>
                </ul>
            </form>
            <?php
            $contct_html = ob_get_clean();
            echo apply_filters('careerfy_contactinf_sh_form_html', $contct_html);
        }
        ?>
    </div>
    <?php
    $html = ob_get_clean();
    return $html;
}