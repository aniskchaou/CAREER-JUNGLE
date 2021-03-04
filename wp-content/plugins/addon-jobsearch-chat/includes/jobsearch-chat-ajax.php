<?php
$absolute_path = __FILE__;
$path_to_file = explode('wp-content', $absolute_path);
$path_to_wp = $path_to_file[0];
require_once($path_to_wp . '/wp-load.php');
global $wpdb, $jobsearch_chat_settings, $jobsearch_plugin_options, $jobsearch_chat_share_files, $jobsearch_download_locations;
$current_user_id = get_current_user_id();
//
$jobsearch_chat_share_files = true;
add_filter('upload_dir', 'jobsearch_chat_files_upload_dir', 10, 1);
$wp_upload_dir = wp_upload_dir();
define('JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL', $wp_upload_dir['url'] . "/");
define('JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_PATH', $wp_upload_dir['path'] . "/");
remove_filter('upload_dir', 'jobsearch_chat_files_upload_dir', 10, 1);
$jobsearch_chat_share_files = false;

$jobsearch_download_locations = false;
//
/*
 * File extensions
 * */
$allFiles = array('unknown', 'rar', 'zip', 'mp3', 'mp4', 'mov', 'flv', 'wmv', 'avi', 'doc', 'docx', 'pdf', 'xls', 'xlsx', 'zip', 'rar', 'txt');
$docFiles = 'unknown|rar|zip|mp3|mp4|mov|flv|wmv|avi|doc|docx|pdf|xls|xlsx|zip|rar|txt|php|html|css';

$supported_image = array(
    'gif',
    'jpg',
    'jpeg',
    'png',
    'swf',
    'JPG'
);
$supported_image_full_formate = array(
    'image/gif',
    'image/jpg',
    'image/jpeg',
    'image/png',
    'image/swf',
    'image/JPG'
);
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

if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_chat_delete_user_friend') {

    global $wpdb;
    $wpdb->query('DELETE  FROM ' . $wpdb->prefix . 'chatfriendlist WHERE f_id = "' . $_POST['f_user_id'] . '"');
    $data = array(
        'friend_id' => get_current_user_id(),
        'deleted_id' => $_POST['user_real_id'],
    );
    $jobsearch_chat_settings->pusher->trigger('presence-admin-chat', 'del-user-friend', $data);
}

if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_chat_delete_message') {
    global $wpdb;
    $res = $wpdb->query('DELETE  FROM ' . $wpdb->prefix . 'chatmessages WHERE chat_id = "' . $_POST['chat_id'] . '"');
    $data = array(
        'delete_msg' => $res,
        'chat_id' => $_POST['chat_id'],
    );
    $pusher->trigger('presence-admin-chat', 'del-message', $data);
    echo $res;
}
if (isset($_POST['action']) && $_POST['action'] == 'uploadFile') {

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    //
    $current_user = wp_get_current_user();
    $file_name_filtered = preg_replace('/[^A-Za-z0-9\-\(\) ]/', '.', basename($_FILES['userfile']['name']));
    $file_name = str_replace(' ', '-', trim(basename($file_name_filtered)));
    $target_path = JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_PATH;
    //
    $jobsearch_chat_share_files = true;
    add_filter('upload_dir', 'jobsearch_chat_files_upload_dir', 10, 1);
    $wp_upload_dir = wp_upload_dir();
    $upload_file_path = $wp_upload_dir['path'];

    if (!file_exists($wp_upload_dir['path'] . "/" . $file_name)) {
        $status_upload = wp_handle_upload($_FILES['userfile'], array('test_form' => false));
    }
    remove_filter('upload_dir', 'jobsearch_chat_files_upload_dir', 10, 1);
    $jobsearch_chat_share_files = false;

    $attach_mime = $_FILES['userfile']['type'];
    if ($attach_mime == 'application/pdf') {
        $attach_icon = 'fa fa-file-pdf-o';
    } else if ($attach_mime == 'application/msword' || $attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
        $attach_icon = 'fa fa-file-word-o';
    } else if ($attach_mime == 'text/plain') {
        $attach_icon = 'fa fa-file-text-o';
    } else if ($attach_mime == 'application/vnd.ms-excel' || $attach_mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
        $attach_icon = 'fa fa-file-excel-o';
    } else if ($attach_mime == 'image/jpeg' || $attach_mime == 'image/png') {
        $attach_icon = 'fa fa-file-image-o';
    } else {
        $attach_icon = 'fa fa-file-word-o';
    }

    if (isset($status_upload['file']) || file_exists($wp_upload_dir['path'] . "/" . $file_name)) {
        //
        $chat_message_ext = $attach_mime;
        $user_meta = get_userdata($_POST['sender_id']);

        $wpdb->insert("{$wpdb->prefix}chatmessages", array(
            'sender_id' => $_POST['sender_id'],
            'reciever_id' => $_POST['reciever_id'],
            'message' => $file_name,
            'viewed' => 0,
            'is_deleted' => 0,
        ));

        $unread_messages_reciver = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}chatmessages WHERE sender_id = {$_POST['sender_id']} AND reciever_id = {$_POST['reciever_id']} and viewed = 0");

        $data = array(
            'message' => $file_name,
            'sender_id' => $_POST['sender_id'],
            'reciever_id' => $_POST['reciever_id'],
            'logged_in_user' => $current_user->ID,
            'user_role' => $user_meta->roles[0],
            'file_path' => JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL,
            'file_icon' => $attach_icon,
            'file_size' => filesize(JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_PATH . $file_name),
            'file_type' => !in_array($chat_message_ext, $supported_image_full_formate) ? 'file' : 'image',
            'sender_image' => $_POST['sender_image'],
            'receiver_image' => $_POST['receiver_image'],
            'unread_messages_reciver' => count($unread_messages_reciver),
            'last_id' => $wpdb->insert_id,
            'last_msg_time' => date('g:ia')
        );

        $pusher->trigger('presence-admin-chat', 'admin-file-share', $data);
    }
}

if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_chat_send_message') {

    $current_user = wp_get_current_user();
    //
    if (isset($_POST['typing']) && $_POST['typing'] != '') {

        $data_to_send = array(
            'typing' => $_POST['typing'],
            'sender_id' => $_POST['sender_id'],
            'reciever_id' => $_POST['reciever_id']
        );
        $pusher->trigger('presence-admin-chat', 'typing-event', $data_to_send);

    } else {

        $wpdb->insert("{$wpdb->prefix}chatmessages", array(
            'sender_id' => $_POST['sender_id'],
            'reciever_id' => $_POST['reciever_id'],
            'message' => trim(strip_tags($_POST['message'])),
            'viewed' => 0,
            'is_deleted' => 0,
        ));
        //
        $data_to_send = array();
        $data_to_send[] = array(
            'message' => trim(strip_tags(convert_smilies($_POST['message']))),
            'sender_id' => $_POST['sender_id'],
            'reciever_id' => $_POST['reciever_id'],
            'datetime' => date('g:ia l,F Y'),
            'logged_in_user' => $current_user->ID,
            'sender_image' => $_POST['sender_image'],
            'receiver_image' => $_POST['receiver_image'],
            'last_id' => $wpdb->insert_id,
            'last_msg_time' => date('g:ia'),
            'small_text' => $jobsearch_chat_settings->limit_text(trim(strip_tags(convert_smilies($_POST['message']))), 5)
        );
        //
        if (strpos(trim(strip_tags($_POST['message'])), 'youtube.com/watch?') != '' || strpos(trim(strip_tags($_POST['message'])), 'vimeo') != '' || strpos(trim(strip_tags($_POST['message'])), 'dailymotion.com/video/') != '') {
            $data_to_send[0]['is_video'] = wp_oembed_get($_POST['message'], array('height' => 200));
        }

        echo json_encode(array('last_id' => $data_to_send[0]['last_id'], 'is_video' => isset($data_to_send[0]['is_video']) ? $data_to_send[0]['is_video'] : ''));
        $unread_messages_reciver = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}chatmessages WHERE sender_id = {$current_user->ID} AND reciever_id = {$_POST['reciever_id']} and viewed = 0");
        $pusher->trigger('presence-admin-chat', 'send-event', array('chat_msg' => $data_to_send, 'totl_receiver_msgs' => count($unread_messages_reciver)));
    }
}

if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_chat_enable_disable_chat') {
    $current_user = wp_get_current_user();
    update_option('jobsearch_chat_chat_status_'.$current_user->ID, $_POST['val']);
    echo json_encode(array('response' => $_POST['val']));
}

if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_chat_get_users_by_key') {
    $user_type = isset($_POST['user_type']) && $_POST['user_type'] == 'emp' ? 'jobsearch_employer' : 'jobsearch_candidate';
    $get_all_users = isset($_POST['get_all_users']) ? $_POST['get_all_users'] : '';
    //
    if ($get_all_users == 'false') {
        $users = new WP_User_Query(array(
            'search' => '*' . esc_attr($_POST['string']) . '*',
            'role' => $user_type,
            'number' => 50,
            'orderby' => 'user_nicename',
            'role__not_in' => 'admin',
            'order' => 'ASC',
            'search_columns' => array(
                'display_name',
            ),
        ));
        $users_found = $users->get_results();
        //
    } else {
        $args = array(
            'role' => $user_type,
            'orderby' => 'user_nicename',
            'role__not_in' => 'admin',
            'order' => 'ASC',
            'number' => 50,
        );
        $users_found = get_users($args);
    }

    foreach ($users_found as $info_user) {
        $total_msgs = $jobsearch_chat_settings->get_unviewed_messages($info_user->ID);
        $user_img = get_avatar_url($info_user->ID);
        $user_msgs = $jobsearch_chat_settings->get_user_last_msg_info($info_user->ID);
        $last_msg = end($user_msgs);
        $user_login_status = get_post_meta($info_user->ID, 'jobsearch_chat_login_status', true);
        ob_start();
        ?>
        <li class="jobsearch-chat-user-<?php echo($info_user->ID) ?> jobsearch-load-user-chat"
            data-user-chat="<?php echo($info_user->ID) ?>">
            <img src="<?php echo($user_img) ?>">
            <span class="status status-with-thumb <?php echo $user_login_status == 0 || empty($user_login_status) ? 'orange' : 'green'; ?>"></span>
            <div>
                <h2><?php echo($info_user->display_name) ?>
                    <small><?php echo $last_msg != '' ? date("g:ia", strtotime($last_msg->time_sent)) : '' ?></small>
                </h2>
                <p><?php echo $last_msg != '' ? $jobsearch_chat_settings->limit_text($last_msg->message, 5) : '' ?>
                    <span class="jobsearch-chat-unread-message <?php echo count($total_msgs) == 0 ? 'hidden' : ''; ?>"><?php echo count($total_msgs) ?></span>
                </p>
            </div>
        </li>
    <?php }
    $html = ob_get_clean();
    echo $html;
}

if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_chat_load_current_user_chat') {

    $reciever_id = $_POST['reciever_id'];
    $limit = $_POST['limit'];
    $current_user = wp_get_current_user();
    $query = "SELECT * FROM {$wpdb->prefix}chatmessages WHERE sender_id = {$current_user->ID} AND reciever_id = {$reciever_id} or sender_id = {$reciever_id}  AND reciever_id = {$current_user->ID} order by chat_id DESC LIMIT {$limit}, 10";
    $messages = $wpdb->get_results($query);
    //
    $pusher->trigger('presence-admin-chat', 'updated-messages', $messages);
    $first_msg_date = '';
    if (count($messages)) {

        if (jobsearch_user_is_employer($current_user->ID)) {
            $employer_user_id = $current_user->ID;
            $employer_id = jobsearch_get_user_employer_id($employer_user_id);
            $sender_user_def_avatar_url = get_avatar_url($employer_user_id, array('size' => 140));
            $user_avatar_id = get_post_thumbnail_id($employer_id);
            if ($user_avatar_id > 0) {
                $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
                $sender_user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
            } else {
                $sender_user_def_avatar_url = jobsearch_employer_image_placeholder();
            }
        } else if (jobsearch_user_is_candidate($current_user->ID)) {
            $cand_user_id = $current_user->ID;
            $candidate_user_id = jobsearch_get_user_candidate_id($cand_user_id);
            $sender_user_def_avatar_url = $jobsearch_chat_settings->jobsearch_cand_img_url_wout_encode($candidate_user_id);
        } else {
            $sender_user_def_avatar_url = get_avatar_url($current_user->ID);
        }
        //
        if (jobsearch_user_is_employer($reciever_id)) {
            $employer_user_id = $reciever_id;
            $employer_id = jobsearch_get_user_employer_id($employer_user_id);
            $rec_user_def_avatar_url = get_avatar_url($employer_user_id, array('size' => 140));
            $user_avatar_id = get_post_thumbnail_id($employer_id);
            if ($user_avatar_id > 0) {
                $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
                $rec_user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
            } else {
                $rec_user_def_avatar_url = jobsearch_employer_image_placeholder();
            }

        } else if (jobsearch_user_is_candidate($reciever_id)) {
            $cand_user_id = $reciever_id;
            $candidate_user_id = jobsearch_get_user_candidate_id($cand_user_id);
            $rec_user_def_avatar_url = $jobsearch_chat_settings->jobsearch_cand_img_url_wout_encode($candidate_user_id);
        } else {
            $rec_user_def_avatar_url = get_avatar_url($reciever_id);
        }

        ob_start();
        foreach (array_reverse($messages) as $chat_info) {
            $wpdb->query("UPDATE {$wpdb->prefix}chatmessages SET viewed = 1 WHERE sender_id={$reciever_id} and reciever_id = {$current_user->ID} and chat_id = {$chat_info->chat_id}");

            $chat_message = $chat_info->message;
            $chat_message_ext = explode('.', $chat_message);
            ?>
            <li class="<?php echo $chat_info->sender_id == $current_user->ID ? 'you' : 'me' ?> ">
                <?php if ($chat_info->sender_id == $current_user->ID) { ?>
                    <img src="<?php echo $chat_info->sender_id == $current_user->ID ? $sender_user_def_avatar_url : $rec_user_def_avatar_url ?>">
                    <div class="jobsearch-chat-user-events">
                        <a href="javascript:void(0)" class="jobsearch-chat-del-message"
                           data-chat-id="<?php echo($chat_info->chat_id) ?>">
                            <i class="chat-icon chat-trash-fill"></i>
                        </a>
                    </div>
                <?php } ?>
                <div class="jobsearch-chat-entete-wrapper chat-<?php echo($chat_info->chat_id) ?>">
                    <?php if (!empty($chat_message_ext)) {
                        $end_extension = end($chat_message_ext);
                        if (in_array($end_extension, $supported_image)) { ?>
                            <a target="_blank"
                               href="<?php echo JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL . $chat_info->message ?>">
                                <img class="prchat_convertedImage"
                                     src="<?php echo JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL . $chat_info->message ?>">

                            </a>
                        <?php } else if (in_array($end_extension, $allFiles)) {
                            $filename = basename(JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL . $chat_info->message);
                            $filetype = wp_check_filetype($filename, null);
                            $file_size = filesize(JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_PATH . $filename);
                            $attach_mime = isset($filetype['type']) ? $filetype['type'] : '';
                            if ($attach_mime == 'application/pdf') {
                                $attach_icon = 'fa fa-file-pdf-o';
                            } else if ($attach_mime == 'application/msword' || $attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                $attach_icon = 'fa fa-file-word-o';
                            } else if ($attach_mime == 'text/plain') {
                                $attach_icon = 'fa fa-file-text-o';
                            } else if ($attach_mime == 'application/vnd.ms-excel' || $attach_mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                $attach_icon = 'fa fa-file-excel-o';
                            } else if ($attach_mime == 'image/jpeg' || $attach_mime == 'image/png') {
                                $attach_icon = 'fa fa-file-image-o';
                            } else {
                                $attach_icon = 'fa fa-file-word-o';
                            }
                            ?>
                            <p><a target="_blank"
                                  href="<?php echo JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL . $chat_info->message ?>">
                                    <i class="<?php echo($attach_icon) ?>"></i>
                                    <span><?php echo($chat_info->message) ?><br><small><?php echo ($file_size) . " KB" ?></small></span>
                                </a>
                            </p>
                        <?php } else if (strpos($chat_info->message, 'youtube.com/watch?') != '' || strpos($chat_info->message, 'vimeo') != '' || strpos($chat_info->message, 'dailymotion.com/video/') != '') { ?>
                            <p><?php echo wp_oembed_get($chat_info->message, array('height' => 200)); ?></p>
                        <?php } else { ?>
                            <p>
                                <?php echo convert_smilies($chat_info->message) ?>
                            </p>
                        <?php }
                    } ?>
                    <div class="jobsearch-chat-entete">
                        <h3><?php echo $jobsearch_chat_settings->get_day_name_and_time($chat_info->time_sent) ?></h3>
                        <a href="javascript:void(0)"
                           class="jobsearch-color jobsearch-chat-seen"><?php echo esc_html__('Seen', 'jobsearch-ajchat') ?></a>
                    </div>
                </div>
                <?php if ($chat_info->sender_id != $current_user->ID) { ?>
                    <img src="<?php echo $chat_info->sender_id == $current_user->ID ? $sender_user_def_avatar_url : $rec_user_def_avatar_url ?>">
                    <div class="jobsearch-chat-user-events">
                        <a href="javascript:void(0)" class="jobsearch-chat-del-message"
                           data-chat-id="<?php echo($chat_info->chat_id) ?>">
                            <i class="chat-icon chat-trash-fill"></i>
                        </a>
                    </div>
                <?php } ?>
            </li>
            <?php
        }
    } else {
        //
        $query = "SELECT * FROM {$wpdb->prefix}chatmessages WHERE sender_id = {$current_user->ID} AND reciever_id = {$reciever_id} or sender_id = {$reciever_id}  AND reciever_id = {$current_user->ID} order by chat_id ASC";
        $messages = $wpdb->get_results($query);
        if (count($messages) > 0) {
            $first_msg_date = esc_html__('Started from ', 'jobsearch-ajchat') . $jobsearch_chat_settings->get_day_name_and_time($messages[0]->time_sent);
        }
    }
    $unread_messages = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}chatmessages WHERE sender_id = {$reciever_id} AND reciever_id = {$current_user->ID} and viewed = 0");
    $html = ob_get_clean();
    echo json_encode(array('html' => $html, 'unread_messages' => count($unread_messages), 'reciever_id' => $reciever_id, 'first_msg_date' => $first_msg_date));
}

if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_chat_load_current_user_chat_front') {

    $reciever_id = $_POST['reciever_id'];

    $limit = $_POST['limit'];
    $current_user = wp_get_current_user();
    $messages = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}chatmessages WHERE sender_id = {$current_user->ID} AND reciever_id = {$reciever_id} or sender_id = {$reciever_id}  AND reciever_id = {$current_user->ID} order by chat_id DESC LIMIT {$limit}, 10");
    $user_image_sender = '';
    $user_img = '';
    if (jobsearch_user_is_employer($current_user->ID)) {
        $employer_user_id = $current_user->ID;
        $employer_id = jobsearch_get_user_employer_id($employer_user_id);
        $sender_user_def_avatar_url = get_avatar_url($employer_user_id, array('size' => 140));
        $user_avatar_id = get_post_thumbnail_id($employer_id);
        if ($user_avatar_id > 0) {
            $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
            $sender_user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
        } else {
            $sender_user_def_avatar_url = jobsearch_employer_image_placeholder();
        }
    } else if (jobsearch_user_is_candidate($current_user->ID)) {
        $cand_user_id = $current_user->ID;
        $candidate_user_id = jobsearch_get_user_candidate_id($cand_user_id);
        $sender_user_def_avatar_url = $jobsearch_chat_settings->jobsearch_cand_img_url_wout_encode($candidate_user_id);
    } else {
        $sender_user_def_avatar_url = get_avatar_url($current_user->ID);
    }
    //
    if (jobsearch_user_is_employer($reciever_id)) {
        $employer_user_id = $reciever_id;
        $employer_id = jobsearch_get_user_employer_id($employer_user_id);
        $rec_user_def_avatar_url = get_avatar_url($employer_user_id, array('size' => 140));
        $user_avatar_id = get_post_thumbnail_id($employer_id);
        if ($user_avatar_id > 0) {
            $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
            $rec_user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
        } else {
            $rec_user_def_avatar_url = jobsearch_employer_image_placeholder();
        }

    } else if (jobsearch_user_is_candidate($reciever_id)) {
        $cand_user_id = $reciever_id;
        $candidate_user_id = jobsearch_get_user_candidate_id($cand_user_id);
        $rec_user_def_avatar_url = $jobsearch_chat_settings->jobsearch_cand_img_url_wout_encode($candidate_user_id);
    } else {
        $rec_user_def_avatar_url = get_avatar_url($reciever_id);
    }
    $first_msg_date = '';
    if (count($messages)) {
        ob_start();
        foreach (array_reverse($messages) as $chat_info) {
            $user_meta = get_userdata($chat_info->sender_id);
            $chat_message = $chat_info->message;
            $chat_message_ext = explode('.', $chat_message);
            $user_is_employer = jobsearch_user_is_employer($chat_info->sender_id);
            ?>
            <li class="<?php echo $chat_info->sender_id == $current_user->ID ? 'conversation-me' : 'conversation-from' ?>">
                <span>
                    <img src="<?php echo $chat_info->sender_id == $current_user->ID ? $sender_user_def_avatar_url : $rec_user_def_avatar_url ?>">
                </span>
                <div class="jobsearch-chat-user-events">
                    <a href="javascript:void(0)" class="jobsearch-chat-del-message"
                       data-chat-id="<?php echo($chat_info->chat_id) ?>">
                        <i class="chat-icon chat-trash-fill"></i>
                    </a>
                </div>
                <?php if (!empty($chat_message_ext)) {
                    $end_extension = end($chat_message_ext);
                    if (in_array($end_extension, $supported_image)) { ?>
                        <div class="jobsearch-chat-list-thumb chat-<?php echo($chat_info->chat_id) ?>">
                            <a href="<?php echo JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL . $chat_info->message ?>">
                                <img class="prchat_convertedImage"
                                     src="<?php echo JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL . $chat_info->message ?>">
                            </a>
                            <br>
                            <small class="jobsearch-chat-msg-time"><?php echo $jobsearch_chat_settings->get_day_name_and_time($chat_info->time_sent) ?></small>
                        </div>
                    <?php } else if (in_array($end_extension, $allFiles)) {
                        $filename = basename(JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL . $chat_info->message);
                        $filetype = wp_check_filetype($filename, null);
                        $attach_mime = isset($filetype['type']) ? $filetype['type'] : '';
                        if ($attach_mime == 'application/pdf') {
                            $attach_icon = 'fa fa-file-pdf-o';
                        } else if ($attach_mime == 'application/msword' || $attach_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                            $attach_icon = 'fa fa-file-word-o';
                        } else if ($attach_mime == 'text/plain') {
                            $attach_icon = 'fa fa-file-text-o';
                        } else if ($attach_mime == 'application/vnd.ms-excel' || $attach_mime == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                            $attach_icon = 'fa fa-file-excel-o';
                        } else if ($attach_mime == 'image/jpeg' || $attach_mime == 'image/png') {
                            $attach_icon = 'fa fa-file-image-o';
                        } else {
                            $attach_icon = 'fa fa-file-word-o';
                        }
                        ?>
                        <div class="jobsearch-chat-list-thumb chat-<?php echo($chat_info->chat_id) ?>">
                            <p><a href="<?php echo JOBSEARCH_CHAT_MODULE_UPLOAD_FOLDER_URL . $chat_info->message ?>">
                                    <i class="<?php echo($attach_icon) ?>"></i>
                                    <?php echo($chat_info->message) ?>
                                </a></p>
                            <br>
                            <small class="jobsearch-chat-msg-time"><?php echo $jobsearch_chat_settings->get_day_name_and_time($chat_info->time_sent) ?>

                            </small>
                        </div>
                    <?php } else if (strpos($chat_info->message, 'youtube.com/watch?') != '' || strpos($chat_info->message, 'vimeo') != '' || strpos($chat_info->message, 'dailymotion.com/video/') != '') { ?>
                        <div class="jobsearch-chat-list-thumb chat-<?php echo($chat_info->chat_id) ?>"><?php echo wp_oembed_get($chat_info->message, array('height' => 100)); ?></p>
                            <br>
                            <small class="jobsearch-chat-msg-time"><?php echo $jobsearch_chat_settings->get_day_name_and_time($chat_info->time_sent) ?></small>
                        </div>
                    <?php } else { ?>
                        <div class="jobsearch-chat-list-thumb chat-<?php echo($chat_info->chat_id) ?>">
                            <p><?php echo convert_smilies($chat_info->message) ?></p>
                            <br>
                            <small class="jobsearch-chat-msg-time"><?php echo $jobsearch_chat_settings->get_day_name_and_time($chat_info->time_sent) ?></small>
                        </div>
                    <?php } ?>
                <?php } ?>
            </li>
            <?php
        }
    } else {
        //
        $query = "SELECT * FROM {$wpdb->prefix}chatmessages WHERE sender_id = {$current_user->ID} AND reciever_id = {$reciever_id} or sender_id = {$reciever_id}  AND reciever_id = {$current_user->ID} order by chat_id ASC";
        $messages = $wpdb->get_results($query);
        if (count($messages) > 0) {
            $first_msg_date = esc_html__('Started from ', 'jobsearch-ajchat') . $jobsearch_chat_settings->get_day_name_and_time($messages[0]->time_sent);
        }
    }
    $unread_messages = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}chatmessages WHERE sender_id = {$reciever_id} AND reciever_id = {$current_user->ID} and viewed = 0");
    $html = ob_get_clean();
    echo json_encode(array('html' => $html, 'unread_messages' => count($unread_messages), 'reciever_id' => $reciever_id, 'last_msg_time' => date('g:ia'), 'first_msg_date' => $first_msg_date));
}

if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_chat_update_unread_messages') {

    $wpdb->query("UPDATE {$wpdb->prefix}chatmessages SET viewed = 1 WHERE sender_id={$_POST['reciever_id']} and reciever_id = {$_POST['sender_id']}");
    $seen_messages_id = $wpdb->get_results("SELECT chat_id FROM {$wpdb->prefix}chatmessages WHERE sender_id={$_POST['reciever_id']} and reciever_id = {$_POST['sender_id']} and viewed = 1");
    $pusher->trigger('presence-admin-chat', 'updated-messages', $seen_messages_id);
}
//
if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_chat_load_next_employers') {
    $args = array(
        'role' => 'jobsearch_employer',
        'orderby' => 'user_nicename',
        'role__not_in' => 'admin',
        'order' => 'ASC',
        'number' => 50,
        'offset' => $_POST['offset']
    );
    $jobsearch_employer = get_users($args);
    foreach ($jobsearch_employer as $info_emp) {
        $total_msgs = $jobsearch_chat_settings->get_unviewed_messages($info_emp->ID);
        $user_msgs = $jobsearch_chat_settings->get_user_last_msg_info($info_emp->ID);
        $last_msg = end($user_msgs);
        $user_img = get_avatar_url($info_emp->ID);
        $emp_login_status = get_post_meta($info_emp->ID, 'jobsearch_chat_login_status', true);
        ob_start();
        ?>
        <li class="jobsearch-chat-user-<?php echo($info_emp->ID) ?> jobsearch-load-user-chat"
            data-user-chat="<?php echo($info_emp->ID) ?>">
            <img src="<?php echo($user_img) ?>">
            <span class="status status-with-thumb <?php echo $emp_login_status == 0 || empty($emp_login_status) ? 'orange' : 'green'; ?>"></span>
            <div>
                <h2 class="name"><?php echo($info_emp->display_name) ?>
                    <small><?php echo $last_msg != '' ? date("g:ia", strtotime($last_msg->time_sent)) : '' ?></small>
                </h2>
                <p><?php echo $last_msg != '' ? $jobsearch_chat_settings->limit_text($last_msg->message, 5) : '' ?>
                    <span class="jobsearch-chat-unread-message <?php echo count($total_msgs) == 0 ? 'hidden' : ''; ?>"><?php echo count($total_msgs) ?></span>
                </p>
            </div>
        </li>

    <?php }
    $html = ob_get_clean();
    echo $html;

}
if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_chat_load_next_candidate') {

    $args = array(
        'role' => 'jobsearch_candidate',
        'orderby' => 'user_nicename',
        'role__not_in' => 'admin',
        'order' => 'ASC',
        'number' => 50,
        'offset' => $_POST['offset']
    );

    $jobsearch_candidate = get_users($args);
    foreach ($jobsearch_candidate as $info_cand) {
        $total_msgs = $jobsearch_chat_settings->get_unviewed_messages($info_cand->ID);
        $user_msgs = $jobsearch_chat_settings->get_user_last_msg_info($info_cand->ID);
        $user_img = get_avatar_url($info_cand->ID);
        $last_msg = end($user_msgs);
        $cand_login_status = get_post_meta($info_cand->ID, 'jobsearch_chat_login_status', true);
        ob_start();
        ?>
        <li class="jobsearch-chat-user-<?php echo($info_cand->ID) ?> jobsearch-load-user-chat"
            data-user-chat="<?php echo($info_cand->ID) ?>">
            <img src="<?php echo($user_img) ?>">
            <span class="status status-with-thumb <?php echo $cand_login_status == 0 || empty($cand_login_status) ? 'orange' : 'green'; ?>"></span>
            <div>
                <h2><?php echo($info_cand->display_name) ?>
                    <small><?php echo $last_msg != '' ? date("g:ia", strtotime($last_msg->time_sent)) : '' ?></small>
                </h2>
                <p><?php echo $last_msg != '' ? $jobsearch_chat_settings->limit_text($last_msg->message, 5) : '' ?>
                    <span class="jobsearch-chat-unread-message <?php echo count($total_msgs) == 0 ? 'hidden' : ''; ?>"><?php echo count($total_msgs) ?></span>
                </p>
            </div>
        </li>

    <?php }
    $html = ob_get_clean();
    echo $html;
}
//
if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_chat_sort_contact') {
    $ids = [];
    $sort_by = $_POST['sort_by'];

    if ($sort_by == 'sort_by_recent') {
        $result = $wpdb->get_results("SELECT reciever_id FROM {$wpdb->prefix}chatmessages WHERE sender_id = {$_POST['current_user_id']} group by reciever_id order by time_sent DESC ");
    } else {
        $result = $wpdb->get_results("SELECT reciever_id FROM {$wpdb->prefix}chatmessages WHERE sender_id = {$_POST['current_user_id']} and viewed = 0 group by reciever_id order by time_sent DESC ");
    }
    foreach ($result as $user_info) {
        $ids[] = $user_info->reciever_id;
    }

    if (jobsearch_user_is_candidate($current_user_id)) {
        $total_contacts = $wpdb->get_results("SELECT employer_id FROM {$wpdb->prefix}chatfriendlist group by employer_id ORDER BY FIELD(f_id,2) DESC");
    } else {
        $total_contacts = $wpdb->get_results("SELECT candidate_id FROM {$wpdb->prefix}chatfriendlist group by candidate_id  ORDER BY FIELD(f_id,2) DESC");
    }
}

if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_chat_add_to_chat_cand') {

    $current_user = get_current_user_id();
    $u_candidate_id = jobsearch_get_candidate_user_id($_POST['cand_id']);
    $wpdb->insert("{$wpdb->prefix}chatfriendlist", array(
        'employer_id' => $current_user,
        'candidate_id' => $u_candidate_id,
    ));
    $user_data = get_userdata($current_user);
    $user_img = get_avatar_url($user_data->ID);
    $total_msgs = $jobsearch_chat_settings->get_unviewed_messages($user_data->ID);
    ob_start(); ?>
    <li class="ofline jobsearch-chat-front-user-<?php echo($user_data->ID) ?> load-chat-box"
        data-user-id="<?php echo($user_data->ID) ?>">
                                    <span>
                                        <img src="<?php echo($user_img) ?>" alt="">
                                    </span>
        <div class="jobsearch-chat-list-thumb user-info">
            <a href="javascript:void(0)" class="name"><?php echo($user_data->display_name) ?>
                <small class="<?php echo count($total_msgs) == 0 ? 'hidden' : ''; ?>"><?php echo count($total_msgs) ?></small>
            </a>
        </div>
    </li>
    <?php
    $html = ob_get_clean();
    echo json_encode(array('res' => 'candidate_added'));
    $pusher->trigger('presence-admin-chat', 'add-candidate', array('employer_id' => $current_user, 'candidate_id' => $u_candidate_id, 'html' => $html));
}

if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_chat_add_to_chat_emp') {

    $current_user = get_current_user_id();
    $u_employer_id = jobsearch_get_employer_user_id($_POST['emp_id']);
    //
    $wpdb->insert("{$wpdb->prefix}chatfriendlist", array(
        'employer_id' => $u_employer_id,
        'candidate_id' => $current_user,
    ));
    //
    $user_data = get_userdata($current_user);
    $user_img = get_avatar_url($user_data->ID);
    $total_msgs = $jobsearch_chat_settings->get_unviewed_messages($user_data->ID);
    ob_start(); ?>
    <li class="ofline jobsearch-chat-front-user-<?php echo($user_data->ID) ?> load-chat-box"
        data-user-id="<?php echo($user_data->ID) ?>">
                                    <span>
                                        <img src="<?php echo($user_img) ?>" alt="">
                                    </span>
        <div class="jobsearch-chat-list-thumb user-info">
            <a href="javascript:void(0)" class="name"><?php echo($user_data->display_name) ?>
                <small class="<?php echo count($total_msgs) == 0 ? 'hidden' : ''; ?>"><?php echo count($total_msgs) ?></small>
            </a>
        </div>
    </li>
    <?php
    $html = ob_get_clean();
    echo json_encode(array('res' => 'employer_added'));
    $pusher->trigger('presence-admin-chat', 'add-employer', array('employer_id' => $u_employer_id, 'candidate_id' => $current_user, 'html' => $html));
}

if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_chat_user_is_login') {
    echo json_encode(array('res' => $_POST));
    update_post_meta($_POST['current_user_id'], 'jobsearch_chat_login_status', 1);
}
if (isset($_POST['action']) && $_POST['action'] == 'jobsearch_chat_user_is_log_out') {
    update_post_meta($_POST['current_user_id'], 'jobsearch_chat_login_status', 0);
    echo json_encode(array('res' => $_POST));
}