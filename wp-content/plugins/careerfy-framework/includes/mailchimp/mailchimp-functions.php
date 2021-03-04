<?php
use MailchimpAPI\Mailchimp;

if (!function_exists('careerfy_framework_mailchimp_list')) {

    /**
     * Mailchimp list.
     *
     * @param string $apikey mailchimp shortcode api key.
     */
    function careerfy_framework_mailchimp_list($apikey)
    {
        $ret_mailchimp_list = array();
        $apikey = trim(str_replace(array(' '), array(''), $apikey));
        $is_valid_api = false;
        if ($apikey != '') {
            $apikey_parts = explode('-', $apikey);
            if (isset($apikey_parts[0]) && strlen($apikey_parts[0]) >= 10 && isset($apikey_parts[1])) {
                $is_valid_api = true;
            }
        }
        if (function_exists('curl_init') && $is_valid_api) {
            $MailChimp = new Mailchimp($apikey);
            $mailchimp_list = $MailChimp->lists()->get();
            if ($mailchimp_list) {
                $mailchimp_list = $mailchimp_list->getBody();
                $mailchimp_list = json_decode($mailchimp_list, true);
                if (isset($mailchimp_list['lists']) && is_array($mailchimp_list['lists']) && sizeof($mailchimp_list['lists']) > 0) {
                    foreach ($mailchimp_list['lists'] as $m_list) {
                        $ret_mailchimp_list['data'][] = array(
                            'id' => $m_list['id'],
                            'name' => $m_list['name'],
                        );
                    }
                }
            }
        }
        return $ret_mailchimp_list;
    }

    function careerfy_framework_mailchimp_list_subscribe($apikey, $list_id, $args)
    {
        if (!function_exists('curl_init')) {
            return false;
        }
        $MailChimp = new Mailchimp($apikey);
        $merge_values = [
            "FNAME" => isset($args['fname']) ? $args['fname'] : '',
            "LNAME" => isset($args['lname']) ? $args['lname'] : ''
        ];

        $post_params = [
            "email_address" => isset($args['email']) ? $args['email'] : '',
            "status" => "subscribed",
            "email_type" => "html",
            "merge_fields" => $merge_values
        ];

        $result = $MailChimp->lists($list_id)->members()->post($post_params);

        $mailchimp_result = $result->getBody();
        $mailchimp_result = json_decode($mailchimp_result, true);
        return $mailchimp_result;
    }

    /**
     * Mailchimp list.
     */
    if (!function_exists('careerfy_mailchimp')) {

        add_action('wp_ajax_nopriv_careerfy_mailchimp', 'careerfy_mailchimp');
        add_action('wp_ajax_careerfy_mailchimp', 'careerfy_mailchimp');

        /**
         * Mailchimp.
         */
        function careerfy_mailchimp()
        {
            global $careerfy_framework_options, $counter;
            $msg = array();

            $mailchimp_key = '';
            $careerfy_list_id = '';
            if (isset($careerfy_framework_options['careerfy-mailchimp-api-key'])) {
                $mailchimp_key = $careerfy_framework_options['careerfy-mailchimp-api-key'];
            }
            if (isset($careerfy_framework_options['careerfy-mailchimp-list'])) {
                $careerfy_list_id = $careerfy_framework_options['careerfy-mailchimp-list'];
            }

            $careerfy_mailchimp_list = array();

            $mc_list = careerfy_framework_mailchimp_list($mailchimp_key);
            if (is_array($mc_list) && isset($mc_list['data'])) {
                foreach ($mc_list['data'] as $list) {
                    $careerfy_mailchimp_list[] = $list['id'];
                }
            }

            $cp_email = isset($_POST['cp_email']) ? $_POST['cp_email'] : '';
            $cp_fname = isset($_POST['cp_fname']) ? $_POST['cp_fname'] : '';
            $cp_lname = isset($_POST['cp_lname']) ? $_POST['cp_lname'] : '';

            $mc_lists = isset($_POST['mc_lists']) ? $_POST['mc_lists'] : '';

            if (isset($cp_email) && !empty($careerfy_list_id) && '' !== $mailchimp_key) {

                $email = $cp_email;
                $list_id = $careerfy_list_id;

                if (!empty($careerfy_mailchimp_list) && !empty($mc_lists) && sizeof($mc_lists) > 0) {
                    foreach ($careerfy_mailchimp_list as $careerfy_mailchimp_lis) {
                        $request_arr = array(
                            'email' => $email,
                            'fname' => $cp_fname,
                            'lname' => $cp_lname,
                        );
                        $result = careerfy_framework_mailchimp_list_subscribe($mailchimp_key, $careerfy_mailchimp_lis, $request_arr);
                    }
                } else {
                    $request_arr = array(
                        'email' => $email,
                        'fname' => $cp_fname,
                        'lname' => $cp_lname,
                    );
                    $result = careerfy_framework_mailchimp_list_subscribe($mailchimp_key, $careerfy_list_id, $request_arr);
                }

                if ('' !== $result) {
                    if (isset($result['status']) && 400 == $result['status']) {
                        $msg['type'] = 'error';
                        $msg['msg'] = $result['detail'];
                    } else {
                        $msg['type'] = 'success';
                        $msg['msg'] = __('Subscribed Successfully.', 'careerfy-frame');
                    }
                }
            } else {
                $msg['type'] = 'error';
                $msg['msg'] = __('Please enter a valid API key.', 'careerfy-frame');
            }
            echo json_encode($msg);
            die();
        }

    }
}

/**
 * Mailchimp frontend form.
 */
if (!function_exists('careerfy_custom_mailchimp')) {

    /**
     * Mailchimp frontend form.
     *
     * @param bolean $under_construction checking under construction.
     */
    function careerfy_custom_mailchimp($newsletter_place = '')
    {
        global $careerfy_framework_options, $counter;
        $careerfy_email_address_str = __('Email Address', 'careerfy-frame');
        if ($newsletter_place == 'footer9') {
            $careerfy_email_address_str = __('Enter your Email Address', 'careerfy-frame');
        }
        $counter++;
        ?>

        <script>
            function careerfy_mailchimp_submit(counter, admin_url) {
                'use strict';
                var $ = jQuery;
                $('#newsletter_error_div_' + counter).fadeOut();
                $('#newsletter_success_div_' + counter).fadeOut();
                $('#process_' + counter).show();
                $('#process_' + counter).html('<i class="fa fa-refresh fa-spin"></i>');
                $.ajax({
                    type: 'POST',
                    url: admin_url,
                    data: $('#mcform_' + counter).serialize() + '&action=careerfy_mailchimp',
                    dataType: "json",
                    success: function (response) {
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
        <div class="careerfy-newsletter" id="process_newsletter_<?php echo intval($counter); ?>">
            <form action="javascript:careerfy_mailchimp_submit('<?php echo esc_js($counter); ?>','<?php echo admin_url('admin-ajax.php'); ?>')"
                  id="mcform_<?php echo intval($counter); ?>" method="post">
                <?php
                $form_inner_inpholdr = 'input-holder';
                if ($newsletter_place == 'footer9') {
                    ?>
                    <label><?php esc_html_e('Subscribe Now', 'careerfy-frame') ?></label>
                    <?php
                    $form_inner_inpholdr = 'careerfy-footernine-newslatter-inner';
                }
                ?>
                <div class="<?php echo($form_inner_inpholdr) ?>">
                    <input id="careerfy_list_id<?php echo intval($counter); ?>" type="hidden" name="careerfy_list_id"
                           value="<?php
                           if (isset($careerfy_framework_options['careerfy-mailchimp-list'])) {
                               echo esc_attr($careerfy_framework_options['careerfy-mailchimp-list']);
                           }
                           ?>"/>
                    <input type="text" id="cp_email<?php echo intval($counter); ?>" name="cp_email"
                           placeholder=" <?php echo esc_html($careerfy_email_address_str); ?>">
                    <?php
                    if ($newsletter_place == 'widget') {
                        ?>
                        <input id="btn_newsletter_<?php echo intval($counter); ?>" type="submit" value=""><i
                                class="fa fa-paper-plane"></i>
                        <?php
                    } else if ($newsletter_place == 'footer9') {
                        ?>
                        <input id="btn_newsletter_<?php echo intval($counter); ?>" type="submit"
                               value="<?php esc_html_e('submit now', 'careerfy-frame') ?>">
                        <?php
                    } else {
                        ?>
                        <label><input class="careerfy-bgcolor" id="btn_newsletter_<?php echo intval($counter); ?>"
                                      type="submit" value="<?php esc_html_e('GET NOTIFIED', 'careerfy-frame') ?>"><i
                                    class="fa fa-paper-plane"></i></label>
                        <?php
                    }
                    ?>
                </div>
                <div id="process_<?php echo intval($counter); ?>" class="status status-message"
                     style="display:none"></div>
            </form>
            <div id="newsletter_error_div_<?php echo intval($counter); ?>" style="display:none"
                 class="alert alert-danger">
                <button class="close" type="button"
                        onclick="hide_div('newsletter_error_div_<?php echo intval($counter); ?>')" aria-hidden="true">×
                </button>
                <p><i class="icon-warning"></i>
                    <span id="newsletter_mess_error_<?php echo intval($counter); ?>"></span></p>
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
        <?php
    }
}