<?php
/**
 * Blog Shortcode
 * @return html
 */
add_shortcode('news_letter_shortcode', 'news_letter_shortcode');
function news_letter_shortcode($atts, $content)
{
    global $careerfy_framework_options;
    extract(shortcode_atts(array(
        'view' => 'style1',
        'news_letter_title' => '',
        'news_letter_desc' => '',
        'news_letter_list' => '',
        'news_letter_list_multi' => '',
    ), $atts));

    $counter = rand(3333, 322342);

    $mailchimp_list = array();
    if (isset($careerfy_framework_options['careerfy-mailchimp-list'])) {
        $mailchimp_list = $careerfy_framework_options['careerfy-mailchimp-list'];
    }
    if (isset($careerfy_framework_options['careerfy-mailchimp-api-key'])) {
        $mailchimp_api_key = $careerfy_framework_options['careerfy-mailchimp-api-key'];
    }
    $mailchimp_lists = careerfy_framework_mailchimp_list($mailchimp_api_key);

    ob_start();
    if ($view == 'style1') { ?>
        <div class="careerfy-eighteen-newslatter">
            <form action="javascript:careerfy_mailchimp_submit<?php echo intval($counter); ?>('<?php echo esc_js($counter); ?>','<?php echo admin_url('admin-ajax.php'); ?>')"
                  id="mcform_<?php echo intval($counter); ?>" method="post">
                <div class="careerfy-eighteen-newslatter-left">
                    <?php if (!empty($news_letter_title)) { ?>
                        <label><?php echo $news_letter_title ?></label>
                    <?php }
                    if (!empty($news_letter_desc)) { ?>
                        <span><?php echo $news_letter_desc ?></span>
                    <?php } ?>
                </div>
                <div class="careerfy-eighteen-newslatter-right">
                    <input name="mc_fname" id="mc_fname<?php echo intval($counter); ?>" value=""
                           placeholder="<?php echo esc_html__('First Name', 'careerfy-frame'); ?>" type="hidden">
                    <input name="mc_lname" id="mc_lname<?php echo intval($counter); ?>" value=""
                           placeholder="<?php echo esc_html__('Last Name', 'careerfy-frame'); ?>" type="hidden">
                    <input name="mc_lists[]" id="mc_lists<?php echo intval($counter); ?>"
                           value="<?php echo $news_letter_list ?>"
                           placeholder="<?php echo esc_html__('MC Lists', 'careerfy-frame'); ?>" type="hidden">
                    <input value="<?php echo esc_html__('Please enter your email...', 'careerfy-frame'); ?>"
                           id="mc_email<?php echo intval($counter); ?>"
                           onblur="if(this.value == '') { this.value ='Please enter your email...'; }"
                           onfocus="if(this.value =='Please enter your email...') { this.value = ''; }" type="text">
                    <input type="submit" id="btn_newsletter_<?php echo intval($counter); ?>"
                           value="<?php esc_html_e('Submit', 'careerfy-frame') ?>"><i
                            class="hidden ajax-loader-news-letter fa fa-refresh fa-spin"></i>
                </div>
                <div id="process_<?php echo intval($counter); ?>" class="status status-message"
                     style="display:none"></div>
            </form>
            <div id="newsletter_error_div_<?php echo intval($counter); ?>" style="display:none"
                 class="alert alert-danger">
                <button class="close" type="button"
                        onclick="hide_div('newsletter_error_div_<?php echo intval($counter); ?>')" aria-hidden="true">×
                </button>
                <p>
                    <i class="icon-warning"></i>
                    <span id="newsletter_mess_error_<?php echo intval($counter); ?>"></span>
                </p>
            </div>
            <div id="newsletter_success_div_<?php echo intval($counter); ?>" style="display:none"
                 class="alert alert-success">
                <button class="close" type="button"
                        onclick="hide_div('newsletter_success_div_<?php echo intval($counter); ?>')" aria-hidden="true">
                    ×
                </button>
                <p><i class="icon-checkmark"></i><span
                            id="newsletter_mess_success_<?php echo intval($counter); ?>"></span></p>
            </div>
        </div>

    <?php } else if ($view == 'style2') { ?>
        <div class="careerfy-twenty-signup">
            <div class="col-md-5">
                <div class="careerfy-twenty-signup-content">
                    <?php if ($content != '') { ?>
                        <h1><?php echo($content) ?>
                            <img src="<?php echo trailingslashit(get_template_directory_uri()) . 'images/title-arrow.png'; ?>">
                        </h1>
                    <?php }
                    if ($news_letter_desc != '') { ?>
                        <span class="careerfy-twenty-search-description"><?php echo($news_letter_desc) ?></span>
                    <?php } ?>
                </div>
            </div>
            <div class="col-md-7">
                <div class="careerfy-twenty-search-tabs">
                    <ul class="careerfy-search-twenty-tabs-nav">
                        <?php
                        $mailchimp_lists_items = explode(',', $news_letter_list_multi);

                        if (count($mailchimp_lists) > 0) {
                        foreach ($mailchimp_lists['data'] as $index => $mail_list) {
                            if (in_array($mail_list['id'], $mailchimp_lists_items)) {


                                $active = $index + 1 == 1 ? 'active' : ''; ?>
                                <li class="<?php echo $active; ?>">
                                    <a data-toggle="tab" data-type="jobsearch_employer"
                                       data-id="<?php echo($mail_list['id']) ?>"
                                       href="#home"
                                       class="user-type-btn-sign-up change-mailchimp-list-item">
                                        <span><?php echo esc_html__($mail_list['name'], 'careerfy-frame') ?></span>
                                    </a>
                                </li>
                            <?php }
                        }

                        ?>
                    </ul>
                    <div class="tab-content">
                        <form method="post" id="mcform_<?php echo intval($counter); ?>"
                              action="javascript:careerfy_mailchimp_submit<?php echo intval($counter); ?>('<?php echo esc_js($counter); ?>','<?php echo admin_url('admin-ajax.php'); ?>')"
                              class="careerfy-twenty-loc-search-style2">

                            <div class="signup-hidden-inputs">
                                <input name="mc_lname" id="mc_lname<?php echo intval($counter); ?>" value=""
                                       placeholder="<?php echo esc_html__('Last Name', 'careerfy-frame'); ?>"
                                       type="hidden">
                                <input name="mc_lists[]" id="mc_lists<?php echo intval($counter); ?>"
                                       value="<?php echo !empty($mailchimp_lists['data']) ? ($mailchimp_lists['data'][0]['id']) : ''; ?>"
                                       placeholder="<?php echo esc_html__('MC Lists', 'careerfy-frame'); ?>"
                                       type="hidden">
                            </div>
                            <ul>
                                <li>
                                    <input name="mc_fname" id="mc_fname<?php echo intval($counter); ?>"
                                           placeholder="<?php echo esc_html__('Name', 'careerfy-frame'); ?>"
                                           type="text">
                                </li>
                                <li>
                                    <input id="mc_email<?php echo intval($counter); ?>"
                                           placeholder="<?php echo esc_html__('Please enter your email...', 'careerfy-frame'); ?>"
                                           type="text">
                                </li>


                                <li class="careerfy-twenty-signup-submit">
                                    <input type="submit" id="btn_newsletter_<?php echo intval($counter); ?>"
                                           value="<?php esc_html_e('Submit', 'careerfy-frame') ?>">

                                </li>
                                <div class="form-loader" style="display: none;"></div>
                            </ul>
                            <div id="process_<?php echo intval($counter); ?>" class="status status-message"
                                 style="display:none"></div>
                        </form>
                        <div id="newsletter_error_div_<?php echo intval($counter); ?>" style="display:none"
                             class="alert alert-danger">
                            <button class="close" type="button"
                                    onclick="hide_div('newsletter_error_div_<?php echo intval($counter); ?>')"
                                    aria-hidden="true">×
                            </button>
                            <p>
                                <i class="icon-warning"></i>
                                <span id="newsletter_mess_error_<?php echo intval($counter); ?>"></span>
                            </p>
                        </div>
                        <div id="newsletter_success_div_<?php echo intval($counter); ?>" style="display:none"
                             class="alert alert-success">
                            <button class="close" type="button"
                                    onclick="hide_div('newsletter_success_div_<?php echo intval($counter); ?>')"
                                    aria-hidden="true">
                                ×
                            </button>
                            <p><i class="icon-checkmark"></i><span
                                        id="newsletter_mess_success_<?php echo intval($counter); ?>"></span></p>
                        </div>
                    </div>
                    <?php } else { ?>
                        <h2><?php echo esc_html__("Please contact to administrator to set settings for Newsletter API", 'careerfy-frame'); ?></h2>
                    <?php } ?>
                </div>
            </div>
        </div>

    <?php } else if ($view == 'style3') { ?>
        <div class="careerfy-twentyone-signup">
            <div class="col-md-5">
                <div class="careerfy-twentyone-signup-content">
                    <?php if ($content != '') { ?>
                        <h1><?php echo($content) ?></h1>
                    <?php }
                    if ($news_letter_desc != '') { ?>
                        <span class="careerfy-twentyone-search-description"><?php echo($news_letter_desc) ?> <img
                                    src="<?php echo trailingslashit(get_template_directory_uri()) . 'images/title-arrow.png'; ?>"></span>
                    <?php } ?>
                </div>
            </div>
            <?php
            $mailchimp_lists_items = explode(',', $news_letter_list_multi);
            if (count($mailchimp_lists) > 0) { ?>
                <div class="col-md-7">
                    <div class="careerfy-twentyone-search-tabs">
                        <ul class="careerfy-search-twentyone-tabs-nav">
                            <?php

                            if (count($mailchimp_lists) > 0) {
                            foreach ($mailchimp_lists['data'] as $index => $mail_list) {
                                if (in_array($mail_list['id'], $mailchimp_lists_items)) {
                                    $active = $index + 1 == 1 ? 'active' : ''; ?>
                                    <li class="<?php echo $active; ?>">
                                        <a data-toggle="tab"
                                           data-id="<?php echo($mail_list['id']) ?>"
                                           href="#home" class="change-mailchimp-list-item"><i
                                                    class="fa fa-black-tie"></i><span><?php echo esc_html__($mail_list['name'], 'careerfy-frame') ?></span></a>
                                    </li>
                                <?php }
                            }
                            ?>
                        </ul>
                        <div class="tab-content">
                            <div id="home" class="tab-pane fade in active">
                                <form method="post" id="mcform_<?php echo intval($counter); ?>"
                                      action="javascript:careerfy_mailchimp_submit<?php echo intval($counter); ?>('<?php echo esc_js($counter); ?>','<?php echo admin_url('admin-ajax.php'); ?>')"
                                      class="careerfy-twentyone-loc-search">
                                    <div class="signup-hidden-inputs">
                                        <input name="mc_fname" id="mc_fname<?php echo intval($counter); ?>"
                                               placeholder="<?php echo esc_html__('First Name', 'careerfy-frame'); ?>"
                                               type="hidden">
                                        <input name="mc_lname" id="mc_lname<?php echo intval($counter); ?>" value=""
                                               placeholder="<?php echo esc_html__('Last Name', 'careerfy-frame'); ?>"
                                               type="hidden">
                                        <input name="mc_lists[]" id="mc_lists<?php echo intval($counter); ?>"
                                               value="<?php echo !empty($mailchimp_lists['data']) ? ($mailchimp_lists['data'][0]['id']) : ''; ?>"
                                               placeholder="<?php echo esc_html__('MC Lists', 'careerfy-frame'); ?>"
                                               type="hidden">
                                    </div>

                                    <div class="jobsearch_searchloc_div">
                                        <input placeholder="<?php echo esc_html__('Email', 'careerfy-frame'); ?>"
                                               id="mc_email<?php echo intval($counter); ?>"
                                               type="text">
                                    </div>

                                    <input type="submit" value="<?php esc_html_e("Submit", 'careerfy-frame') ?>">

                                </form>
                                <div id="newsletter_error_div_<?php echo intval($counter); ?>" style="display:none"
                                     class="alert alert-danger">
                                    <button class="close" type="button"
                                            onclick="hide_div('newsletter_error_div_<?php echo intval($counter); ?>')"
                                            aria-hidden="true">×
                                    </button>
                                    <p>
                                        <i class="icon-warning"></i>
                                        <span id="newsletter_mess_error_<?php echo intval($counter); ?>"></span>
                                    </p>
                                </div>
                                <div id="newsletter_success_div_<?php echo intval($counter); ?>" style="display:none"
                                     class="alert alert-success">
                                    <button class="close" type="button"
                                            onclick="hide_div('newsletter_success_div_<?php echo intval($counter); ?>')"
                                            aria-hidden="true">
                                        ×
                                    </button>
                                    <p><i class="icon-checkmark"></i><span
                                                id="newsletter_mess_success_<?php echo intval($counter); ?>"></span></p>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } else { ?>
                <h2><?php echo esc_html__("Please contact to administrator to set settings for Newsletter API", 'careerfy-frame'); ?></h2>
            <?php } ?>
        </div>

    <?php } else { ?>
        <div class="careerfy-twentytwo-search-tabs">
            <ul class="careerfy-search-twentytwo-tabs-nav">
                <?php
                $mailchimp_lists_items = explode(',', $news_letter_list_multi);
                if (count($mailchimp_lists) > 0) {
                    foreach ($mailchimp_lists['data'] as $index => $mail_list) {
                        if (in_array($mail_list['id'], $mailchimp_lists_items)) {
                            $active = $index + 1 == 1 ? 'active' : ''; ?>
                            <li class="<?php echo $active; ?>">
                                <a data-toggle="tab"
                                   data-id="<?php echo($mail_list['id']) ?>"
                                   href="#home" class="change-mailchimp-list-item">
                                    <i class="fa fa-black-tie"></i><span><?php echo esc_html__($mail_list['name'], 'careerfy-frame') ?></span></a>
                            </li>
                        <?php }
                    }
                }
                ?>
            </ul>

            <div class="tab-content">
                <div id="home" class="tab-pane fade in active">
                    <form method="post" id="mcform_<?php echo intval($counter); ?>"
                          action="javascript:careerfy_mailchimp_submit<?php echo intval($counter); ?>('<?php echo esc_js($counter); ?>','<?php echo admin_url('admin-ajax.php'); ?>')"
                          class="careerfy-twentytwo-loc-search-newsletter">
                        <div class="signup-hidden-inputs">

                            <input name="mc_lname" id="mc_lname<?php echo intval($counter); ?>" value=""
                                   placeholder="<?php echo esc_html__('Last Name', 'careerfy-frame'); ?>"
                                   type="hidden">
                            <input name="mc_lists[]" id="mc_lists<?php echo intval($counter); ?>"
                                   value="<?php echo !empty($mailchimp_lists['data']) ? ($mailchimp_lists['data'][0]['id']) : ''; ?>"
                                   placeholder="<?php echo esc_html__('MC Lists', 'careerfy-frame'); ?>"
                                   type="hidden">
                        </div>
                        <ul>
                            <li>
                                <input name="mc_fname" id="mc_fname<?php echo intval($counter); ?>"
                                       placeholder="<?php echo esc_html__('Name', 'careerfy-frame'); ?>"
                                       type="text">
                                <i class="careerfy-icon careerfy-user-1"></i>
                            </li>

                            <li>
                                <input placeholder="<?php echo esc_html__('Email', 'careerfy-frame'); ?>"
                                       id="mc_email<?php echo intval($counter); ?>"
                                       type="text">
                                <i class="careerfy-icon careerfy-envelope"></i>
                            </li>
                            <li class="careerfy-twentytwo-loc-search-submit">
                                <input type="submit" value="<?php esc_html_e("Subscribe", 'careerfy-frame') ?>">
                            </li>
                        </ul>
                    </form>
                    <div id="newsletter_error_div_<?php echo intval($counter); ?>" style="display:none"
                         class="alert alert-danger">
                        <button class="close" type="button"
                                onclick="hide_div('newsletter_error_div_<?php echo intval($counter); ?>')"
                                aria-hidden="true">×
                        </button>
                        <p>
                            <i class="icon-warning"></i>
                            <span id="newsletter_mess_error_<?php echo intval($counter); ?>"></span>
                        </p>
                    </div>
                    <div id="newsletter_success_div_<?php echo intval($counter); ?>" style="display:none"
                         class="alert alert-success">
                        <button class="close" type="button"
                                onclick="hide_div('newsletter_success_div_<?php echo intval($counter); ?>')"
                                aria-hidden="true">
                            ×
                        </button>
                        <p><i class="icon-checkmark"></i><span
                                    id="newsletter_mess_success_<?php echo intval($counter); ?>"></span></p>
                    </div>
                </div>
            </div>
        </div>
    <?php }

    if (!empty($mailchimp_list)) { ?>
        <script type="text/javascript">
            function careerfy_mailchimp_submit<?php echo intval($counter); ?>(counter, admin_url) {
                'use strict';
                var $ = jQuery;
                $('#newsletter_error_div_' + counter).fadeOut();
                $('#newsletter_success_div_' + counter).fadeOut();
                $('#process_' + counter).show();
                $('.ajax-loader-news-letter').removeClass('hidden');
                $.ajax({
                    type: 'POST',
                    url: admin_url,
                    data: "mc_lists=" + $('#mc_lists' + counter) + "&cp_email=" + $('#mc_email' + counter).val() + "&cp_fname=" + $('#mc_fname' + counter).val() + "&cp_lname=" + $('#mc_lname' + counter).val() + '&' + $('#mcform_' + counter).serialize() + '&action=careerfy_mailchimp',
                    dataType: "json",
                    success: function (response) {
                        $('.ajax-loader-news-letter').addClass('hidden');
                        $('#mcform_' + counter).get(0).reset();
                        if (response.type === 'error') {
                            $('#process_' + counter).hide();
                            $('#newsletter_mess_error_' + counter).html(response.msg);
                            $('#newsletter_error_div_' + counter).fadeIn();
                        } else {
                            $('#process_' + counter).hide();
                            $('#newsletter_mess_success_' + counter).html(response.msg);
                            $('#newsletter_success_div_' + counter).fadeIn();
                        }
                        $('#newsletter_mess_' + counter).fadeIn(600);
                        $('#newsletter_mess_' + counter).html(response);
                        $('#process_' + counter).html('');
                    }
                });
            }

            function hide_div(div_hide) {
                jQuery('#' + div_hide).hide();
            }

        </script>
        <?php
    } else {
        echo '<p class="error-api">' . esc_html__('Please contact to administrator to set settings for Newsletter API', 'careerfy-frame') . '</p>';
    }
    

    if (count($mailchimp_lists) > 0 && ($view == 'style2' || $view == 'style3' || $view == 'style4')) { ?>
        <script type="text/javascript">
            jQuery(document).on('click', '.change-mailchimp-list-item', function () {
                var _mailchimp_id = jQuery(this).attr('data-id'),
                    _mc_lists = jQuery('#mc_lists<?php echo intval($counter) ?>');
                _mc_lists.val(_mailchimp_id);
            });
        </script>
    <?php }

    $html = ob_get_clean();
    return $html;
}
