<?php
global $jobsearch_chat_settings, $wpdb, $jobsearch_plugin_options;
$current_user = wp_get_current_user();

$args = array(
    'role' => 'administrator',
    'orderby' => 'user_nicename',
    'order' => 'ASC',
);
$jobsearch_admin = get_users($args);

$args = array(
    'role' => 'jobsearch_chat_support',
    'orderby' => 'user_nicename',
    'order' => 'ASC',
);
$jobsearch_chat_support = get_users($args);

$get_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';
$page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
$page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');
$page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page'); //get_permalink($page_id);

$user_id = get_current_user_id();
if (jobsearch_user_is_employer($user_id)) {
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
    $cand_user_id = $user_id;
    $candidate_user_id = jobsearch_get_user_candidate_id($cand_user_id);
    $user_def_avatar_url = $jobsearch_chat_settings->jobsearch_cand_img_url_wout_encode($candidate_user_id);
}
//
$emojis = _wp_emoji_list();
$chat_enable_disable_status = get_option('jobsearch_chat_chat_status_' . $current_user->ID);
?>
<div class="jobsearch-chat-wrapper-main jobsearch-chat-frontend">
    <div class="jobsearch-chat-floating-chat hidden">
        <i class="jobsearch-chat-floating-icon1 chat-icon chat-bubble-o"></i>
        <i class="jobsearch-chat-floating-icon2 chat-icon chat-bubble"></i>
        <span class="jobsearch-chat-floating-title"><?php echo esc_html__('Live Chat', 'jobsearch-ajchat') ?></span>
    </div>
    <div class="jobsearch-chat-tabs-wrapper">
        <?php
        if (count($jobsearch_chat_support) > 0) {
            foreach ($jobsearch_chat_support as $admin) { ?>
                <div class="jobsearch-chat-wrapper ofline jobsearch-chat-tab2 hidden user-<?php echo($admin->ID) ?>">
                    <div class="jobsearch-chat-top">
                        <span class="jobsearch-chat-title" onclick="slidechat(<?php echo($admin->ID) ?>)"></span>
                        <input type="hidden" name="user_image" value="">
                        <a href="javascript:void(0)"
                           class="jobsearch-tooltipcon jobsearch-chat-boxslide jobsearch-chat-close"
                           data-user-id="<?php echo($admin->ID) ?>"
                           title="<?php echo esc_html__('Close', 'jobsearch-ajchat') ?>">
                            <i class="chat-icon chat-close"></i>
                        </a>

                        <a href="javascript:void(0)" class="jobsearch-tooltipcon"
                           title="<?php echo esc_html__('Settings', 'jobsearch-ajchat') ?>"><i
                                    class="chat-icon chat-settings"></i></a>
                        <div class="notification-box hidden">
                            <span class="notification-count ">0</span>
                            <div class="notification-bell">
                                <span class="bell-top"></span>
                                <span class="bell-middle"></span>
                                <span class="bell-bottom"></span>
                                <span class="bell-rad"></span>
                            </div>
                        </div>
                    </div>
                    <div class="jobsearch-chat-floating-chat-list">
                        <div class="jobsearch-chat-list jobsearch-chat-scroll"
                             onscroll="loadFloatMessages(this,<?php echo($admin->ID) ?>)" data-msgs-end="false">
                            <ul id="float-chat"></ul>
                        </div>

                        <div class="jobsearch-chat-input">
                            <form enctype="multipart/form-data" class="messageForm" autocomplete="off" method="post">
                                <input type="text" name="message" placeholder="<?php echo esc_html__('Type a Message','jobsearch-ajchat') ?>" autocomplete="off">
                                <input type="hidden" name="sender_id" value="<?php echo($user_id) ?>">
                                <input type="hidden" name="reciever_id" value="">
                                <input type="hidden" name="sender_image" value="<?php echo($user_def_avatar_url); ?>">
                                <input type="hidden" name="receiver_image"
                                       value="<?php echo get_avatar_url($user_id); ?>">
                                <input type="hidden" class="typing" name="typing" value="false">
                                <input type="file" class="jobsearch-chat-share-file hidden" value="sharefile">
                                <div class="jobsearch-chat-input-place">
                                    <div class="jobsearch-chat-emojis-box jobsearch-chat-user-emoji-<?php echo($admin->ID) ?>">
                                        <?php foreach ($emojis as $key => $info) {
                                            if ($key >= 1777 && $key <= 1821) {
                                                echo '<span class="jobsearch-emoji" data-user-id="' . $admin->ID . '" data-val="' . $info . '">' . $info . '</span>';
                                            }
                                        } ?>
                                    </div>
                                    <a href="javascript:void(0)" class="jobsearch-chat-front-send-message"
                                       data-user-id="<?php echo($admin->ID) ?>">
                                        <i class="chat-icon chat-send"></i></a>
                                    <a href="javascript:void(0)" data-user-id="<?php echo($admin->ID) ?>"
                                       class="jobsearch-chat-emoji-picker-for-id jobsearch-chat-emoji-picker-select "><i
                                                class="chat-icon chat-smile"></i></a>
                                    <a href="javascript:void(0)"
                                       onclick="FrontChattriggerFile(<?php echo($admin->ID) ?>)"><i
                                                class="chat-icon chat-link"></i></a>

                                </div>

                                <span class="jobsearch-chat-user-typing">
                                        <img src="<?php echo plugin_dir_url(__DIR__) . '../img/userIsTyping.gif'; ?>"/>
                                    </span>
                            </form>

                            <form hidden enctype="multipart/form-data" name="fileForm" method="post"
                                  onsubmit="FrontChatuploadFileForm(this);return false;">
                                <input type="file" class="file" name="userfile" required/>
                                <input type="hidden" name="reciever_id" value="">
                                <input type="hidden" name="sender_id" value="<?php echo($user_id) ?>">
                                <input type="hidden" name="sender_image" value="<?php echo($user_def_avatar_url); ?>">
                                <input type="hidden" name="receiver_image"
                                       value="<?php echo get_avatar_url($user_id); ?>">
                                <input type="hidden" name="action" value="uploadFile">
                                <input type="submit" name="submit" class="jobsearch-chat-save" value="save"/>
                            </form>
                        </div>

                    </div>
                </div>
                <?php
            }
        }
        if (count($jobsearch_admin) > 0) {
            foreach ($jobsearch_admin as $admin) { ?>
                <div class="jobsearch-chat-wrapper ofline jobsearch-chat-tab2 hidden user-<?php echo($admin->ID) ?>">
                    <div class="jobsearch-chat-top">
                        <span class="jobsearch-chat-title" onclick="slidechat(<?php echo($admin->ID) ?>)"></span>
                        <input type="hidden" name="user_image" value="">
                        <a href="javascript:void(0)"
                           class="jobsearch-tooltipcon jobsearch-chat-boxslide jobsearch-chat-close"
                           data-user-id="<?php echo($admin->ID) ?>"
                           title="<?php echo esc_html__('Close', 'jobsearch-ajchat') ?>">
                            <i class="chat-icon chat-close"></i>
                        </a>

                        <a href="javascript:void(0)" class="jobsearch-tooltipcon"
                           title="<?php echo esc_html__('Settings', 'jobsearch-ajchat') ?>"><i
                                    class="chat-icon chat-settings"></i></a>
                        <div class="notification-box hidden">
                            <span class="notification-count ">0</span>
                            <div class="notification-bell">
                                <span class="bell-top"></span>
                                <span class="bell-middle"></span>
                                <span class="bell-bottom"></span>
                                <span class="bell-rad"></span>
                            </div>
                        </div>
                    </div>
                    <div class="jobsearch-chat-floating-chat-list">
                        <div class="jobsearch-chat-list jobsearch-chat-scroll"
                             onscroll="loadFloatMessages(this,<?php echo($admin->ID) ?>)" data-msgs-end="false">
                            <ul id="float-chat"></ul>
                        </div>

                        <div class="jobsearch-chat-input">
                            <form enctype="multipart/form-data" class="messageForm" autocomplete="off" method="post">
                                <input type="text" name="message" placeholder="<?php echo esc_html__('Type a Message','jobsearch-ajchat') ?>" autocomplete="off">
                                <input type="hidden" name="sender_id" value="<?php echo($user_id) ?>">
                                <input type="hidden" name="reciever_id" value="">
                                <input type="hidden" name="sender_image" value="<?php echo($user_def_avatar_url); ?>">
                                <input type="hidden" name="receiver_image"
                                       value="<?php echo get_avatar_url($user_id); ?>">
                                <input type="hidden" class="typing" name="typing" value="false">
                                <input type="file" class="jobsearch-chat-share-file hidden" value="sharefile">
                                <div class="jobsearch-chat-input-place">
                                    <div class="jobsearch-chat-emojis-box jobsearch-chat-user-emoji-<?php echo($admin->ID) ?>">
                                        <?php foreach ($emojis as $key => $info) {
                                            if ($key >= 1777 && $key <= 1821) {
                                                echo '<span class="jobsearch-emoji" data-user-id="' . $admin->ID . '" data-val="' . $info . '">' . $info . '</span>';
                                            }
                                        } ?>
                                    </div>
                                    <a href="javascript:void(0)" class="jobsearch-chat-front-send-message"
                                       data-user-id="<?php echo($admin->ID) ?>">
                                        <i class="chat-icon chat-send"></i></a>
                                    <a href="javascript:void(0)" data-user-id="<?php echo($admin->ID) ?>"
                                       class="jobsearch-chat-emoji-picker-for-id jobsearch-chat-emoji-picker-select "><i
                                                class="chat-icon chat-smile"></i></a>
                                    <a href="javascript:void(0)"
                                       onclick="FrontChattriggerFile(<?php echo($admin->ID) ?>)"><i
                                                class="chat-icon chat-link"></i></a>

                                </div>

                                <span class="jobsearch-chat-user-typing">
                                        <img src="<?php echo plugin_dir_url(__DIR__) . '../img/userIsTyping.gif'; ?>"/>
                                    </span>
                            </form>

                            <form hidden enctype="multipart/form-data" name="fileForm" method="post"
                                  onsubmit="FrontChatuploadFileForm(this);return false;">
                                <input type="file" class="file" name="userfile" required/>
                                <input type="hidden" name="reciever_id" value="">
                                <input type="hidden" name="sender_id" value="<?php echo($user_id) ?>">
                                <input type="hidden" name="sender_image" value="<?php echo($user_def_avatar_url); ?>">
                                <input type="hidden" name="receiver_image"
                                       value="<?php echo get_avatar_url($user_id); ?>">
                                <input type="hidden" name="action" value="uploadFile">
                                <input type="submit" name="submit" class="jobsearch-chat-save" value="save"/>
                            </form>
                        </div>

                    </div>
                </div>
                <?php
            }
        }

        if (!empty($jobsearch_chat_settings->get_fiends_list()) && count($jobsearch_chat_settings->get_fiends_list())) {
            foreach ($jobsearch_chat_settings->get_fiends_list() as $friends) {
                $user_data = isset($friends->candidate_id) ? get_userdata($friends->candidate_id) : get_userdata($friends->employer_id);
                if (isset($friends->employer_id)) {
                    $employer_user_id = $friends->employer_id;
                    $employer_id = jobsearch_get_user_employer_id($employer_user_id);
                    $rec_user_def_avatar_url = get_avatar_url($employer_user_id, array('size' => 140));
                    $user_avatar_id = get_post_thumbnail_id($employer_id);
                    if ($user_avatar_id > 0) {
                        $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
                        $rec_user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
                    } else {
                        $rec_user_def_avatar_url = jobsearch_employer_image_placeholder();
                    }
                } else {
                    $cand_user_id = $friends->candidate_id;
                    $candidate_user_id = jobsearch_get_user_candidate_id($cand_user_id);
                    $rec_user_def_avatar_url = $jobsearch_chat_settings->jobsearch_cand_img_url_wout_encode($candidate_user_id);
                }
                ?>
                <div class="jobsearch-chat-wrapper ofline jobsearch-chat-tab2 hidden user-<?php echo($user_data->ID) ?>">
                    <div class="jobsearch-chat-top">
                    <span class="jobsearch-chat-title"
                          onclick="slidechat(<?php echo($user_data->ID) ?>)">Test Jack</span>
                        <input type="hidden" name="user_image" value="">
                        <a href="javascript:void(0)"
                           class="jobsearch-tooltipcon jobsearch-chat-boxslide jobsearch-chat-close"
                           data-user-id="<?php echo($user_data->ID) ?>"
                           title="<?php echo esc_html__('Close', 'jobsearch-ajchat') ?>"><i
                                    class="chat-icon chat-close"></i></a>

                        <a href="javascript:void(0)" class="jobsearch-tooltipcon feature-coming-soon"
                           title="<?php echo esc_html__('Settings', 'jobsearch-ajchat') ?>"><i
                                    class="chat-icon chat-settings"></i></a>

                        <div class="notification-box hidden">
                            <span class="notification-count ">0</span>
                            <div class="notification-bell">
                                <span class="bell-top"></span>
                                <span class="bell-middle"></span>
                                <span class="bell-bottom"></span>
                                <span class="bell-rad"></span>
                            </div>
                        </div>
                    </div>
                    <div class="jobsearch-chat-floating-chat-list">
                        <div class="jobsearch-chat-list jobsearch-chat-scroll"
                             onscroll="loadFloatMessages(this,<?php echo($user_data->ID) ?>)" data-msgs-end="false">
                            <ul id="float-chat"></ul>
                        </div>
                        <div class="jobsearch-chat-input">
                            <form enctype="multipart/form-data" class="messageForm" method="post">
                                <input type="text" name="message" placeholder="<?php echo esc_html__('Type a Message','jobsearch-ajchat') ?>" autocomplete="off">
                                <input type="hidden" name="sender_id" value="<?php echo($user_id) ?>">
                                <input type="hidden" name="reciever_id" value="">
                                <input type="hidden" name="sender_image" value="<?php echo($user_def_avatar_url) ?>">
                                <input type="hidden" name="receiver_image"
                                       value="<?php echo($rec_user_def_avatar_url) ?>">
                                <input type="hidden" class="typing" name="typing" value="false">
                                <input type="file" class="jobsearch-chat-share-file hidden" value="sharefile">
                                <div class="jobsearch-chat-input-place ">
                                    <div class="jobsearch-chat-emojis-box jobsearch-chat-user-emoji-<?php echo($user_data->ID) ?>">
                                        <?php foreach ($emojis as $key => $info) {
                                            if ($key >= 1777 && $key <= 1821) {
                                                echo '<span class="jobsearch-emoji" data-user-id="' . $user_data->ID . '" data-val="' . $info . '">' . $info . '</span>';
                                            }

                                        } ?>
                                    </div>
                                    <a href="javascript:void(0)" class="jobsearch-chat-front-send-message"
                                       data-user-id="<?php echo($user_data->ID) ?>">
                                        <i class="chat-icon chat-send"></i></a>
                                    <a href="javascript:void(0)" data-user-id="<?php echo($user_data->ID) ?>"
                                       class="jobsearch-chat-emoji-picker-for-id jobsearch-chat-emoji-picker-select"><i
                                                class="chat-icon chat-smile"></i></a>
                                    <a href="javascript:void(0)"
                                       onclick="FrontChattriggerFile(<?php echo($user_data->ID) ?>)">
                                        <i class="chat-icon chat-link"></i></a>

                                </div>
                                <span class="jobsearch-chat-user-typing"><img
                                            src="<?php echo plugin_dir_url(__DIR__) . '../img/userIsTyping.gif'; ?>"/></span>
                            </form>
                            <form hidden enctype="multipart/form-data" name="fileForm" method="post"
                                  onsubmit="FrontChatuploadFileForm(this);return false;">
                                <input type="file" class="file" name="userfile" required/>
                                <input type="hidden" name="reciever_id" value="">
                                <input type="hidden" name="sender_image" value="<?php echo($user_def_avatar_url) ?>">
                                <input type="hidden" name="receiver_image"
                                       value="<?php echo($rec_user_def_avatar_url) ?>">
                                <input type="hidden" name="sender_id" value="<?php echo($user_id) ?>">
                                <input type="hidden" name="action" value="uploadFile">
                                <input type="submit" name="submit" class="jobsearch-chat-save" value="save"/>
                            </form>
                        </div>
                    </div>
                </div>
            <?php }
        }
        ?>

        <div class="jobsearch-chat-wrapper jobsearch-chat-member-list-wrapper"
             style="opacity: 1; visibility: hidden;">
            <div class="jobsearch-connection-field">
                <i class="fa fa-wifi blink"></i>
            </div>
            <div class="jobsearch-chat-top">
                <h2><?php echo esc_html__('Chat', 'jobsearch-ajchat') ?></h2>
                <a href="javascript:void(0)" class="jobsearch-tooltipcon jobsearch-chat-toggle-chat"
                   title="<?php echo esc_html__('Toggle', 'jobsearch-ajchat') ?>"><i
                            class="careerfy-icon careerfy-down-arrow-line "></i></a>
                <a href="<?php echo add_query_arg(array('tab' => jobsearch_user_is_employer($user_id) == true ? 'emp-chat' : 'cand-chat'), $page_url) ?>"
                   class="jobsearch-tooltipcon"
                   title="<?php echo esc_html__('Full View', 'jobsearch-ajchat') ?>">
                    <i class="chat-icon chat-menu"></i></a>
                <a href="#" class="feature-coming-soon"><i class="chat-icon chat-user-group"></i></a>
                <a href="#" class="jobsearch-tooltipcon jobsearch-chat-user-settings-btn"
                   title="<?php echo esc_html__('Click on settings', 'jobsearch-ajchat') ?>">
                    <i class="chat-icon chat-settings"></i></a>
                <a href="javascript:void(0)" class="jobsearch-tooltipcon"
                   title="<?php echo esc_html__('Sound Notifications', 'jobsearch-ajchat') ?>"><i
                            class="chat-icon chat-speaker disableSound"></i></a>
            </div>
            <div class="jobsearch-chat-list jobsearch-chat-list-toggle-view">
                <div class="jobsearch-chat-user-settings-con">
                    <div class="jobsearch-chat-close-main">
                        <a href="javascript:void(0)" class="jobsearch-chat-close-settings"><i
                                    class="fa fa-times"></i></a>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="jobsearch-chat-user-settings-title">
                                <h2><?php echo esc_html__('Disable Chat', 'jobsearch-ajchat') ?></h2>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <input type="checkbox" class="jobsearch-chat-enble-disble-chat"
                                   onclick="chat_enble_disble_chat(this)" <?php echo ($chat_enable_disable_status) == 1 ? 'checked' : '' ?> >
                        </div>
                    </div>
                    <div class="jobsearch-chat-user-result-msg"></div>
                </div>
                <?php
                $php_pusher_auth = isset($jobsearch_plugin_options['jobsearch-php-pusher-auth-key']) ? $jobsearch_plugin_options['jobsearch-php-pusher-auth-key'] : '';
                $php_pusher_secret = isset($jobsearch_plugin_options['jobsearch-php-pusher-auth-secret']) ? $jobsearch_plugin_options['jobsearch-php-pusher-auth-secret'] : '';
                $php_pusher_app_id = isset($jobsearch_plugin_options['jobsearch-php-pusher-app-id']) ? $jobsearch_plugin_options['jobsearch-php-pusher-app-id'] : '';
                if (empty($php_pusher_auth) || empty($php_pusher_secret) || empty($php_pusher_app_id)) { ?>
                    <h2 class="jobsearch-chat-contact-title"><?php esc_html_e('Please add API credentials in Jobsearch options, API settings in order to use chat.', 'wp-jobsearch') ?></h2>
                <?php } else { ?>
                    <h2 class="jobsearch-chat-contact-title"><?php echo esc_html__('Contacts', 'jobsearch-ajchat') ?></h2>
                    <ul>

                        <?php if (count($jobsearch_chat_support) > 0) {
                            foreach ($jobsearch_chat_support as $admin) {
                                $user_img = get_avatar_url($admin->ID);
                                $total_msgs = $jobsearch_chat_settings->get_unviewed_messages($admin->ID);
                                ?>
                                <li class="ofline jobsearch-chat-front-user-<?php echo($admin->ID) ?> load-chat-box"
                                    data-user-id="<?php echo($admin->ID) ?>">
                                    <span>
                                        <img src="<?php echo($user_img) ?>" alt="">

                                    </span>
                                    <div class="jobsearch-chat-list-thumb user-info">
                                        <a href="javascript:void(0)" class="name"><?php echo($admin->display_name) ?>
                                            (<?php echo esc_html__('Support', 'jobsearch-ajchat') ?>)
                                            <small class="<?php echo count($total_msgs) == 0 ? 'hidden' : ''; ?>"><?php echo count($total_msgs) ?></small>
                                        </a>
                                    </div>
                                </li>
                            <?php }
                        } ?>
                        <?php if (count($jobsearch_admin) > 0) {
                            foreach ($jobsearch_admin as $admin) {
                                $user_img = get_avatar_url($admin->ID);
                                $total_msgs = $jobsearch_chat_settings->get_unviewed_messages($admin->ID);
                                ?>
                                <li class="ofline jobsearch-chat-front-user-<?php echo($admin->ID) ?> load-chat-box"
                                    data-user-id="<?php echo($admin->ID) ?>">
                                    <span>
                                        <img src="<?php echo($user_img) ?>" alt="">

                                    </span>
                                    <div class="jobsearch-chat-list-thumb user-info">
                                        <a href="javascript:void(0)" class="name"><?php echo($admin->display_name) ?>
                                            <small class="<?php echo count($total_msgs) == 0 ? 'hidden' : ''; ?>"><?php echo count($total_msgs) ?></small>
                                        </a>
                                    </div>
                                </li>
                            <?php }
                        }
                        if (!empty($jobsearch_chat_settings->get_fiends_list()) && count($jobsearch_chat_settings->get_fiends_list())) {
                                foreach ($jobsearch_chat_settings->get_fiends_list() as $friends) {
                                    $user_data = isset($friends->candidate_id) ? get_user_by('ID', $friends->candidate_id) : get_user_by('ID', $friends->employer_id);
                                    if (isset($friends->employer_id)) {
                                        $employer_user_id = $friends->employer_id;
                                        $employer_id = jobsearch_get_user_employer_id($employer_user_id);
                                        $user_def_avatar_url = get_avatar_url($employer_user_id, array('size' => 140));
                                        $user_avatar_id = get_post_thumbnail_id($employer_id);
                                        if ($user_avatar_id > 0) {
                                            $user_thumbnail_image = wp_get_attachment_image_src($user_avatar_id, 'thumbnail');
                                            $user_def_avatar_url = isset($user_thumbnail_image[0]) && esc_url($user_thumbnail_image[0]) != '' ? $user_thumbnail_image[0] : '';
                                        } else {
                                            $user_def_avatar_url = jobsearch_employer_image_placeholder();
                                        }
                                        $user_displayname = !empty($user_data) ? $user_data->data->display_name : '';

                                    } else {
                                        //
                                        $user_displayname = isset($user_data->display_name) ? $user_data->display_name : '';
                                        $user_displayname = apply_filters('jobsearch_user_display_name', $user_displayname, $user_data);
                                        //
                                        $cand_user_id = $friends->candidate_id;
                                        $candidate_user_id = jobsearch_get_user_candidate_id($cand_user_id);
                                        $user_def_avatar_url = $jobsearch_chat_settings->jobsearch_cand_img_url_wout_encode($candidate_user_id);
                                    }

                                    $total_msgs = $jobsearch_chat_settings->get_unviewed_messages($user_data->ID);
                                    ?>
                                    <li class="ofline jobsearch-chat-front-user-<?php echo($user_data->ID) ?> load-chat-box"
                                        data-user-id="<?php echo($user_data->ID) ?>">
                            <span>
                                <img src="<?php echo($user_def_avatar_url) ?>" alt="">

                            </span>
                                        <div class="jobsearch-chat-list-thumb user-info">
                                            <a href="javascript:void(0)"
                                               class="name"><?php echo $user_displayname; ?>
                                                <small class="<?php echo count($total_msgs) == 0 ? 'hidden' : ''; ?>"><?php echo count($total_msgs) ?></small>
                                            </a>
                                        </div>
                                    </li>
                                <?php }
                            }

                        ?>

                    </ul>
                    <span class="jobsearch-chat-group-title"><?php echo esc_html__('Group Conversation', 'jobsearch-ajchat') ?></span>
                    <a href="javascript:void(0)" class="jobsearch-chat-create-group feature-coming-soon">
                        <span><img src="<?php echo plugin_dir_url(__DIR__) . '../img/group-icon.png'; ?>"></span>
                        <small><?php echo esc_html__('Create New Group', 'jobsearch-ajchat') ?></small>
                    </a>
                    <div class="jobsearch-chat-bottom">
                        <i class="chat-icon chat-search"></i>
                        <input type="text" id="search_field_toggle" placeholder="Search" autocomplete="off">
                    </div>
                <?php } ?>
            </div>

        </div>
        <script>
            var data_user_id;
            jQuery(document).on('click', '.jobsearch-chat-emoji-picker-for-id', function () {
                data_user_id = jQuery(this).attr('data-user-id');
            })
        </script>
    </div>
</div>