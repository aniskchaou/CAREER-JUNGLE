<?php
global $jobsearch_plugin_options;
$absolute_path = __FILE__;
$path_to_file = explode('wp-content', $absolute_path);
$path_to_wp = $path_to_file[0];
require_once( $path_to_wp . '/wp-load.php' );
require_once plugin_dir_path(__DIR__) . '/vendor/autoload.php';


$php_pusher_auth = isset($jobsearch_plugin_options['jobsearch-php-pusher-auth-key']) ? $jobsearch_plugin_options['jobsearch-php-pusher-auth-key'] : '';
$php_pusher_secret = isset($jobsearch_plugin_options['jobsearch-php-pusher-auth-secret']) ? $jobsearch_plugin_options['jobsearch-php-pusher-auth-secret'] : '';
$php_pusher_app_id = isset($jobsearch_plugin_options['jobsearch-php-pusher-app-id']) ? $jobsearch_plugin_options['jobsearch-php-pusher-app-id'] : '';
$php_pusher_cluster = isset($jobsearch_plugin_options['jobsearch-php-pusher-auth-cluster']) ? $jobsearch_plugin_options['jobsearch-php-pusher-auth-cluster'] : '';

$options = array(
    'cluster' => $php_pusher_cluster,
    'useTLS' => true
);
$pusher = new Pusher\Pusher(
    $php_pusher_auth,
    $php_pusher_secret,
    $php_pusher_app_id,
    $options
);
$justLoggedIn = false;
$current_user = wp_get_current_user();
if ($current_user->ID != 0) {
    $justLoggedIn = true;
}
//
$presence_data = array(
    'name' => $current_user->display_name,
    'justLoggedIn' => $justLoggedIn
);
//

$channel_name = $_POST["channel_name"];
//check user has access to $channel_name
echo $pusher->presence_auth($channel_name, $_POST["socket_id"], $current_user->ID, $presence_data);