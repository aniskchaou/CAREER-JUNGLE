<?php
/**
 * Login Shortcode
 * @return html
 */
add_shortcode('jobsearch_login_registration', 'jobsearch_login_registration_shortcode');

function jobsearch_login_registration_shortcode($atts, $content = '') {
    extract(shortcode_atts(array(
        'login_registration_title' => '',
        'login_register_form' => '',
        'login_candidate_register' => '',
        'login_employer_register' => '',
                    ), $atts));

    $rand_numb = rand(1000000, 9999999);

    ob_start();
    $terms = get_terms('prayer-prayfor', array(
        'hide_empty' => false,
    ));
    ?> 
    <div class="jobsearch-form jobsearch-typo-wrap jobsearch-login-registration-form">
        <?php if ($login_registration_title != '') { ?>
            <div class="jobsearch-contact-title"><h2><?php echo esc_html($login_registration_title); ?></h2></div> 
            <!--title end-->
            <?php
        }
        do_action('login_registration_form_html', $atts);
        ?>
    </div> 
    <?php
    $html = ob_get_clean();
    return $html;
}
