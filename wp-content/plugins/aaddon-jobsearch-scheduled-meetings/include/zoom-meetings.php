<?php
// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// functions class
class JobSearch_Sched_ZoomMeets_Functions {

    public function __construct() {
        
        add_action('wp_ajax_jobsearch_zoom_meetin_onoff_callback', array($this, 'zoom_meetins_onoff'));
        
        add_action('wp_ajax_jobsearch_zoom_auth_clientid_callback', array($this, 'zoom_client_auth_callback'));
        
        add_action('wp', array($this, 'get_access_token_zoom'));
        
        add_action('wp', array($this, 'user_reset_zoom_access_token'), 20);
    }
    
    public function zoom_meetins_onoff() {
        $user_id = get_current_user_id();
        $employer_id = jobsearch_get_user_employer_id($user_id);
        
        $switch_val = ($_POST['btn_val'] == 'on' ? 'on' : '');
        
        update_post_meta($employer_id, 'jobsearch_zoom_meetins_switch', $switch_val);
        
        wp_send_json(array('val' => $switch_val));
    }
    
    public function zoom_client_auth_callback() {
        $user_id = get_current_user_id();
        $employer_id = jobsearch_get_user_employer_id($user_id);
        
        $client_id = $_POST['client_id'];
        $client_secret = $_POST['client_secret'];
        $zoom_email = $_POST['zoom_email'];
        
        update_post_meta($employer_id, 'jobsearch_zoom_auth_client_id', $client_id);
        update_post_meta($employer_id, 'jobsearch_zoom_auth_client_secret', $client_secret);
        update_post_meta($employer_id, 'jobsearch_zoom_user_email_address', $zoom_email);
        
        $state = base64_encode('zoom_auth_state');
        $redirect_uri = home_url('/');
        
        $html = '';
        if ($client_id != '') {
            ob_start();
            ?>
            <script>
                var zoom_auth_win = window.open('https://zoom.us/oauth/authorize?response_type=code&state=<?php echo ($state) ?>&client_id=<?php echo ($client_id) ?>&redirect_uri=<?php echo ($redirect_uri) ?>',
                        '', 'scrollbars=no,menubar=no,resizable=yes,toolbar=no,status=no,width=800, height=400');
                var auth_window_timer = setInterval(function () {
                    if (zoom_auth_win.closed) {
                        clearInterval(auth_window_timer);
                        window.location.reload();
                    }
                }, 500);
            </script>
            <?php
            $html = ob_get_clean();
        }
        
        wp_send_json(array('html' => $html));
    }
    
    private function access_token_code_curl($code) {

        $user_id = get_current_user_id();
        $employer_id = jobsearch_get_user_employer_id($user_id);
        
        $client_id = get_post_meta($employer_id, 'jobsearch_zoom_auth_client_id', true);
        $client_secret = get_post_meta($employer_id, 'jobsearch_zoom_auth_client_secret', true);

        $data = array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => home_url('/'),
        );
        $data_str = http_build_query($data);

        $url = 'https://zoom.us/oauth/token';
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, 1);
        // make sure we are POSTing
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_str);
        // allow us to use the returned data from the request
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //we are sending json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic ' . base64_encode($client_id . ':' . $client_secret),
        ));

        $result_token = curl_exec($ch);
        curl_close($ch);

        return $result_token;
    }

    private function refresh_token_code_curl($refresh_token) {

        $user_id = get_current_user_id();
        $employer_id = jobsearch_get_user_employer_id($user_id);
        
        $client_id = get_post_meta($employer_id, 'jobsearch_zoom_auth_client_id', true);
        $client_secret = get_post_meta($employer_id, 'jobsearch_zoom_auth_client_secret', true);

        $data = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token,
            'redirect_uri' => home_url('/'),
        );
        $data_str = http_build_query($data);

        $url = 'https://zoom.us/oauth/token';
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, 1);
        // make sure we are POSTing
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_str);
        // allow us to use the returned data from the request
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //we are sending json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic ' . base64_encode($client_id . ':' . $client_secret),
        ));

        $result_token = curl_exec($ch);
        curl_close($ch);

        return $result_token;
    }
    
    public function get_access_token_zoom() {
        $state = base64_encode('zoom_auth_state');
        if (isset($_GET['state']) && $_GET['state'] == $state && isset($_GET['code']) && $_GET['code'] != '') {

            $user_id = get_current_user_id();
            $employer_id = jobsearch_get_user_employer_id($user_id);

            $code = $_GET['code'];

            $result_token = $this->access_token_code_curl($code);

            $result_token = json_decode($result_token, true);

            if (isset($result_token['access_token']) && $result_token['access_token'] != '') {
                $refresh_token = isset($result_token['refresh_token']) ? $result_token['refresh_token'] : '';
                update_post_meta($employer_id, 'jobsearch_zoom_refresh_token', $refresh_token);
                $access_token = $result_token['access_token'];
                set_transient('jobsearch_zoom_access_token_' . $user_id, $access_token, 900);
                echo '<script>window.close();</script>';
                die;
            }
        }
    }
    
    public function user_zoom_access_token($user_id) {

        $check_transient = get_transient('jobsearch_zoom_access_token_' . $user_id);
        if (!empty($check_transient)) {
            $access_token = $check_transient;
            return $access_token;
        }
    }
    
    public function user_reset_zoom_access_token() {
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $employer_id = jobsearch_get_user_employer_id($user_id);
            
            if ($employer_id > 0) {
                $user_refresh_token = get_post_meta($employer_id, 'jobsearch_zoom_refresh_token', true);
                if ($user_refresh_token != '') {
                    $result_token = $this->refresh_token_code_curl($user_refresh_token);
                    $result_token = json_decode($result_token, true);
                    if (isset($result_token['access_token']) && $result_token['access_token'] != '') {
                        $refresh_token = isset($result_token['refresh_token']) ? $result_token['refresh_token'] : '';
                        update_post_meta($employer_id, 'jobsearch_zoom_refresh_token', $refresh_token);
                        $access_token = $result_token['access_token'];
                        set_transient('jobsearch_zoom_access_token_' . $user_id, $access_token, 900);
                        return $access_token;
                    } else {
                        update_post_meta($employer_id, 'jobsearch_zoom_refresh_token', '');
                    }
                }
            }
        }
    }
    
    public function reset_zoom_access_token_byid($user_id) {
        
        $employer_id = jobsearch_get_user_employer_id($user_id);
        $zoom_meetins_switch = get_post_meta($employer_id, 'jobsearch_zoom_meetins_switch', true);
        if ($zoom_meetins_switch == 'on') {
            $access_token = $this->user_zoom_access_token($user_id);
            if (!$access_token) {

                if ($employer_id > 0) {
                    $user_refresh_token = get_post_meta($employer_id, 'jobsearch_zoom_refresh_token', true);
                    if ($user_refresh_token != '') {
                        $result_token = $this->refresh_token_code_curl($user_refresh_token);
                        $result_token = json_decode($result_token, true);
                        if (isset($result_token['access_token']) && $result_token['access_token'] != '') {
                            $refresh_token = isset($result_token['refresh_token']) ? $result_token['refresh_token'] : '';
                            update_post_meta($employer_id, 'jobsearch_zoom_refresh_token', $refresh_token);
                            $access_token = $result_token['access_token'];
                            set_transient('jobsearch_zoom_access_token_' . $user_id, $access_token, 900);
                            return $access_token;
                        } else {
                            update_post_meta($employer_id, 'jobsearch_zoom_refresh_token', '');
                        }
                    }
                }
            }
        }
    }

}
global $JobSearch_Sched_ZoomMeets;
$JobSearch_Sched_ZoomMeets = new JobSearch_Sched_ZoomMeets_Functions();
