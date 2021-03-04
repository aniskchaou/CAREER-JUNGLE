<?php
add_action('admin_footer', 'careerfy_instagram_footer_scripts');

function careerfy_instagram_footer_scripts() {
    ?>
    <script type="text/javascript">
        // instagram getting access token
        jQuery(document).on('click', 'input[type="radio"][id^="instagram-access_token-btn"]', function () {
            var this_parent_div = jQuery(this).parent('div');
            if (this_parent_div.find('.loding-msg-con').length <= 0) {
                this_parent_div.append('&nbsp;<span class="loding-msg-con"></span>');
            }

            var msg_con = this_parent_div.find('.loding-msg-con');
            msg_con.html('<i class="fa fa-refresh fa-spin"></i>');
            var request = jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php') ?>',
                method: "POST",
                data: {
                    'access_token': 'instagram',
                    'action': 'careerfy_get_instagram_redirect_code',
                },
                dataType: "json"
            });

            request.done(function (response) {
                if (typeof response.msg !== 'undefined') {
                    msg_con.html(response.msg);
                } else {
                    msg_con.html('<?php esc_html_e('There is some problem.', 'careerfy-frame') ?>');
                }
                if (typeof response.form_html !== 'undefined') {
                    msg_con.append(response.form_html);
                    msg_con.find('form').submit();
                }
            });

            request.fail(function (jqXHR, textStatus) {
                msg_con.html('<?php esc_html_e('There is some problem.', 'careerfy-frame') ?>');
            });
        });
    </script>
    <?php
}

// instagram access token hook
add_action('wp_ajax_careerfy_get_instagram_redirect_code', 'careerfy_get_instagram_redirect_code');

function careerfy_get_instagram_redirect_code() {
    global $careerfy_framework_options;

    if (isset($_POST['access_token']) && $_POST['access_token'] == 'instagram') {
        $client_id = isset($careerfy_framework_options['instagram-client-id']) ? $careerfy_framework_options['instagram-client-id'] : '';
        $client_secret = isset($careerfy_framework_options['instagram-client-secret']) ? $careerfy_framework_options['instagram-client-secret'] : '';
        $redirect_uri = isset($careerfy_framework_options['instagram-redirect-uri']) && $careerfy_framework_options['instagram-redirect-uri'] != '' ? $careerfy_framework_options['instagram-redirect-uri'] : home_url('/');

        if ($client_id == '' || $client_secret == '') {
            echo json_encode(array('msg' => esc_html__('Please enter your client id and secret for Instagram.', 'careerfy-frame')));
            die;
        }

        ob_start();
        ?>
        <form method="get" action="https://api.instagram.com/oauth/authorize/">
            <input type="hidden" name="client_id" value="<?php echo ($client_id) ?>">
            <input type="hidden" name="redirect_uri" value="<?php echo add_query_arg(array('redirect_from' => 'instagram_code'), $redirect_uri) ?>">
            <input type="hidden" name="response_type" value="code">
        </form>
        <?php
        $form_html = ob_get_clean();
        echo json_encode(array('msg' => esc_html__('redirecting... Please wait.', 'careerfy-frame'), 'form_html' => $form_html));
        die;
    }
    echo json_encode(array('msg' => esc_html__('There is some problem.', 'careerfy-frame')));
    die;
}

if (isset($_GET['redirect_from']) && $_GET['redirect_from'] == 'instagram_code' && isset($_GET['code'])) {
    global $careerfy_framework_options, $CareerfyFrameReduxFramework;

    $client_id = isset($careerfy_framework_options['instagram-client-id']) ? $careerfy_framework_options['instagram-client-id'] : '';
    $client_secret = isset($careerfy_framework_options['instagram-client-secret']) ? $careerfy_framework_options['instagram-client-secret'] : '';
    $instagram_redirect_uri = isset($careerfy_framework_options['instagram-redirect-uri']) && $careerfy_framework_options['instagram-redirect-uri'] != '' ? $careerfy_framework_options['instagram-redirect-uri'] : home_url('/');

    $instagram_redirect_uri = add_query_arg(array('redirect_from' => 'instagram_code'), $instagram_redirect_uri);
    
    $instagram_code = $_GET['code'];
    $redirect_url = admin_url('admin.php?page=theme_options&tab=8');

    if ($instagram_code != '') {
        $instagram_uri = 'https://api.instagram.com/oauth/access_token';
        $data = array(
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $instagram_redirect_uri,
            'code' => $instagram_code
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $instagram_uri); // uri
        curl_setopt($ch, CURLOPT_POST, true); // POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // POST DATA
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // RETURN RESULT true
        curl_setopt($ch, CURLOPT_HEADER, 0); // RETURN HEADER false
        curl_setopt($ch, CURLOPT_NOBODY, 0); // NO RETURN BODY false / we need the body to return
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // VERIFY SSL HOST false
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // VERIFY SSL PEER false
        $instagram_result = curl_exec($ch);
        $instagram_result = json_decode($instagram_result, true);
        if (isset($instagram_result['access_token']) && $instagram_result['access_token'] != '') {
            //$CareerfyFrameReduxFramework->ReduxFramework->set('instagram-access-token', $instagram_result['access_token']);
        }
        if (isset($instagram_result['user']['id']) && $instagram_result['user']['id'] != '') {
            //$CareerfyFrameReduxFramework->ReduxFramework->set('instagram-user-id', $instagram_result['user']['id']);
        }
    }

    header("Location: " . $redirect_url, true);
    exit();
}