<?php

global $jobsearch_chat_settings, $jobsearch_plugin_options;
$this->args = array(
    'role' => 'jobsearch_candidate',
    'orderby' => 'user_nicename',
    'role__not_in' => 'admin',
    'order' => 'ASC',
    'number' => 50,
);
$this->jobsearch_candidate = get_users($this->args);
//
$this->args = array(
    'role' => 'jobsearch_employer',
    'orderby' => 'user_nicename',
    'role__not_in' => 'admin',
    'order' => 'ASC',
    'number' => 50,
);
$this->jobsearch_employer = get_users($this->args);
$php_pusher_auth = isset($jobsearch_plugin_options['jobsearch-php-pusher-auth-key']) ? $jobsearch_plugin_options['jobsearch-php-pusher-auth-key'] : '';
$php_pusher_secret = isset($jobsearch_plugin_options['jobsearch-php-pusher-auth-secret']) ? $jobsearch_plugin_options['jobsearch-php-pusher-auth-secret'] : '';
$php_pusher_app_id = isset($jobsearch_plugin_options['jobsearch-php-pusher-app-id']) ? $jobsearch_plugin_options['jobsearch-php-pusher-app-id'] : '';

$current_user = get_current_user_id();
//
if (jobsearch_user_is_employer($current_user)) {
    $employer_user_id = $user_id;
    $employer_id = jobsearch_get_user_employer_id($employer_user_id);
    $user_def_avatar_url = get_avatar_url($employer_user_id, array('size' => 140));
    $user_avatar_id = get_post_thumbnail_id($employer_id);
    if ($user_avatar_id > 0) {
        $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
        $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
    } else {
        $user_def_avatar_url = jobsearch_employer_image_placeholder();
    }
} else {
    $cand_user_id = $current_user;
    $candidate_user_id = jobsearch_get_user_candidate_id($cand_user_id);
    $user_def_avatar_url = $jobsearch_chat_settings->jobsearch_cand_img_url_wout_encode($candidate_user_id);
}
$emojis = _wp_emoji_list();
?>
<div id="jobsearch-chat-container" class="jobsearch-chat-main-container">
    <div class="jobsearch-profile-title jobsearch-chat-heading">
        <?php if (empty($php_pusher_auth) || empty($php_pusher_secret) || empty($php_pusher_app_id)) { ?>
            <h2><?php esc_html_e('Please add API credentials in Jobsearch options, API settings in order to use chat.', 'wp-jobsearch') ?></h2>
            <?php return;
        } ?>
        <h2><?php esc_html_e('Messages', 'wp-jobsearch') ?></h2>
    </div>
    <aside>
        <ul class="nav nav-tabs jobsearch-chat-nav">
            <li class="active">
                <a href="javascript:void(0)" data-list="jobsearch-chat-user-employer"
                   class="jobsearch-chat-list"><?php echo esc_html__('Employer', 'jobsearch-ajchat') ?></a>
            </li>
            <li>
                <a href="javascript:void(0)" data-list="jobsearch-chat-user-candidate"
                   class="jobsearch-chat-list"><?php echo esc_html__('Candidate', 'jobsearch-ajchat') ?></a>
            </li>
            <li>
                <a href="javascript:void(0)" data-list="jobsearch-chat-user-groups"
                   class="jobsearch-chat-list"><?php echo esc_html__('Groups', 'jobsearch-ajchat') ?></a>
            </li>
        </ul>
        <div class="jobsearch-chat-sort-list">
            <div class="jobsearch-chat-filter-wrapper">
                <div class="jobsearch-chat-filter-input">
                    <a href="javascript:void(0)" class="jobsearch-chat-filter-toggle"><i
                                class="chat-icon chat-search"></i></a>
                </div>
                <div class="jobsearch-chat-filter-input-field">
                    <input type="text" name="filter-name" id="search_field" data-user-type="emp" placeholder="Search">
                    <i class="jobsearch-chat-filter-toggle jobsearch-chat-all-users chat-icon chat-close"></i>
                </div>
            </div>
        </div>
        <div class="jobsearch-chat-user-list">
            <input type="hidden" name="employer-user-offset" value="50">
            <input type="hidden" name="candidate-user-offset" value="50">
            <input type="hidden" name="scrollTop-emp" value="600">
            <input type="hidden" name="scrollTop-cand" value="600">
            <div class="jobsearch-chat-users-list jobsearch-chat-user-employer" onscroll="loadUsers(this)">
                <ul>
                    <?php foreach ($this->jobsearch_employer as $info_emp) {
                        //
                        $employer_id = jobsearch_get_user_employer_id($info_emp->ID);
                        $user_def_avatar_url = get_avatar_url($info_emp->ID, array('size' => 140));
                        $user_avatar_id = get_post_thumbnail_id($employer_id);
                        if ($user_avatar_id > 0) {
                            $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
                            $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
                        } else {
                            $user_def_avatar_url = jobsearch_employer_image_placeholder();
                        }
                        $total_msgs = $jobsearch_chat_settings->get_unviewed_messages($info_emp->ID);
                        //$user_img = get_avatar_url($info_emp->ID);
                        $user_msgs = $jobsearch_chat_settings->get_user_last_msg_info($info_emp->ID);
                        $last_msg = end($user_msgs);
                        ?>
                        <li class="jobsearch-chat-user-<?php echo($info_emp->ID) ?> jobsearch-load-user-chat"
                            data-user-chat="<?php echo($info_emp->ID) ?>">
                            <img src="<?php echo($user_def_avatar_url) ?>">
                            <span class="status status-with-thumb orange"></span>
                            <div>
                                <h2><?php echo($info_emp->display_name) ?>
                                    <small><?php echo $last_msg != '' ? date("g:ia", strtotime($last_msg->time_sent)) : '' ?></small>
                                </h2>
                                <p><?php echo $last_msg != '' ? $jobsearch_chat_settings->limit_text($last_msg->message, 5) : '' ?>
                                    <span class="jobsearch-chat-unread-message <?php echo count($total_msgs) == 0 ? 'hidden' : ''; ?>"><?php echo count($total_msgs) ?></span>
                                </p>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="jobsearch-chat-users-list jobsearch-chat-user-candidate hidden" onscroll="loadUsers(this)">
                <ul>
                    <?php foreach ($this->jobsearch_candidate as $info_cand) {
                        //
                        $candidate_user_id = jobsearch_get_user_candidate_id($info_cand->ID);
                        $user_img = $jobsearch_chat_settings->jobsearch_cand_img_url_wout_encode($candidate_user_id);
                        $total_msgs = $jobsearch_chat_settings->get_unviewed_messages($info_cand->ID);
                        $user_msgs = $jobsearch_chat_settings->get_user_last_msg_info($info_cand->ID);
                        $last_msg = end($user_msgs);
                        //
                        $user_data = get_user_by('ID', $info_cand->ID);
                        $user_displayname = isset($user_data->display_name) ? $user_data->display_name : '';
                        $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_data);
                        //

                        ?>
                        <li class="jobsearch-chat-user-<?php echo($info_cand->ID) ?> jobsearch-load-user-chat"
                            data-user-chat="<?php echo($info_cand->ID) ?>">
                            <img src="<?php echo($user_img) ?>">
                            <span class="status status-with-thumb orange"></span>
                            <div>
                                <h2><?php echo($user_displayname) ?>
                                    <small><?php echo $last_msg != '' ? date("g:ia", strtotime($last_msg->time_sent)) : '' ?></small>
                                </h2>
                                <p><?php echo $last_msg != '' ? $jobsearch_chat_settings->limit_text($last_msg->message, 5) : '' ?>
                                    <span class="jobsearch-chat-unread-message <?php echo count($total_msgs) == 0 ? 'hidden' : ''; ?>"><?php echo count($total_msgs) ?></span>
                                </p>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="jobsearch-chat-users-list jobsearch-chat-user-groups hidden">
                <ul>
                    <li class="feature-coming-soon">
                        <span class="jobsearch-chat-group-title"><?php echo esc_html__('Group Conversation', 'jobsearch-ajchat') ?></span>
                        <a href="javascript:void(0)">
                            <span><img src="<?php echo plugin_dir_url(__DIR__) . '../img/group-icon.png'; ?>"></span>
                            <small><?php echo esc_html__('Create New Group', 'jobsearch-ajchat') ?></small>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </aside>
    <div class="jobsearch-user-chat-content jobsearch-user-chat-messages hidden">
        <div class="jobsearch-user-chat-header">
            <div class="jobsearch-user-detail">
                <img src="#" alt="">
                <span class="status status-with-thumb orange"></span>
                <div class="jobsearch-user-status-wrapper">
                    <h2></h2>
                    <span><?php echo esc_html__('Offline', 'jobsearch-ajchat') ?></span>
                </div>
            </div>
            <div class="jobsearch-user-file-content">
                <ul>
                    <li><a href="javascript:void(0)" class="feature-coming-soon"><i class="chat-icon chat-add"></i></a>
                    </li>
                    <li><a href="javascript:void(0)" class="feature-coming-soon"><i class="chat-icon chat-dott"></i></a>
                    </li>
                </ul>
            </div>
        </div>

        <ul id="chat" class="jobsearch-chat-messages-list" onscroll="loadMessages(this)"></ul>

        <div class="jobsearch-chat-form-wrapper">
            <form class="jobsearch-chat-form" method="post" enctype="multipart/form-data">
                <textarea placeholder="Type your message" id="input-custom-size" class="emojiable-option"
                          name="message"></textarea>
                <input type="hidden" name="sender_id" value="<?php echo($current_user) ?>">
                <input type="hidden" name="reciever_id" value="">
                <input type="hidden" name="sender_image" value="<?php echo get_avatar_url($current_user) ?>">
                <input type="hidden" name="receiver_image" value="">
                <input type="hidden" class="typing" name="typing" value="false">
                <input type="file" class="jobsearch-chat-share-file hidden" value="sharefile">
                <div class="jobsearch-chat-share-file-wrapper">
                    <div class="jobsearch-chat-emojis-box">
                        <?php foreach ($emojis as $key => $info) {
                            if ($key >= 1777 && $key <= 1821) {
                                echo '<span class="jobsearch-emoji" data-val="' . $info . '">' . $info . '</span>';
                            }
                        } ?>
                    </div>
                    <div class="jobsearch-chat-share-file">
                        <a href="javascript:void(0)" class="jobsearch-tooltipcon"
                           title="<?php echo esc_html__('Upload File', 'jobsearch-ajchat') ?>"
                           onclick="triggerFile()"><i class="chat-icon chat-link"></i></a>

                        <a href="javascript:void(0)" class="jobsearch-chat-emoji-picker-select-full-view"><i
                                    class="chat-icon chat-smile"></i></a>

                    </div>
                    <div class="jobsearch-chat-typing-wrapper">
                                    <span class="jobsearch-chat-user-typing bounce">
                                         <img src="<?php echo plugin_dir_url(__DIR__) . '../img/userIsTyping.gif'; ?>"/>
                                    </span>
                        <input type="submit" class="jobsearch-chat-send-message"
                               value="<?php echo esc_html__('Send', 'jobsearch-ajchat') ?>">
                    </div>
                </div>
            </form>
            <form hidden enctype="multipart/form-data" name="fileForm" method="post"
                  onsubmit="uploadFileForm(this);return false;">
                <input type="file" class="file" name="userfile" required/>
                <input type="hidden" name="reciever_id" value="">
                <input type="hidden" name="sender_id" value="<?php echo($current_user) ?>">
                <input type="hidden" name="sender_image" value="<?php echo get_avatar_url($current_user) ?>">
                <input type="hidden" name="receiver_image" value="">
                <input type="hidden" name="action" value="uploadFile">
                <input type="submit" name="submit" class="jobsearch-chat-save" value="save"/>
            </form>
        </div>
    </div>
</div>