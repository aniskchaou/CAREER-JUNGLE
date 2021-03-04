<?php

if (!defined('ABSPATH')) {
    die;
}

if (!class_exists('Jobsearch_Dashboard_Notifications')) {

    class Jobsearch_Dashboard_Notifications {

        // hook things up
        public function __construct() {
            add_filter('jobsearch_candash_stats_before_start', array($this, 'cand_dash_notifications'), 11, 2);
            //
            add_filter('jobsearch_empdash_stats_before_start', array($this, 'emp_dash_notifications'), 11, 2);
            //
            add_action('jobsearch_newjob_posted_at_frontend', array($this, 'on_front_job_post_notifics'), 11);
            //
            add_action('jobsearch_user_shortlist_for_interview', array($this, 'on_candidate_shortlisted_for_interview'), 11, 3);
            //
            add_action('jobsearch_user_rejected_for_interview', array($this, 'on_candidate_rejected_for_interview'), 11, 3);
            //
            add_action('jobsearch_job_applying_save_action', array($this, 'on_candidate_apply_for_job'), 11, 2);

            add_filter('jobsearch_dashmenu_account_btns_before_items', array($this, 'dashmenu_notifics_btn'), 11, 2);
            //
            add_action('wp_ajax_jobsearch_userdash_notific_readmark_act', array($this, 'userdash_notific_readmark_act'));

            add_action('wp_ajax_jobsearch_close_notific_item_check', array($this, 'close_notific_item_check'));

            //
            add_action('wp_ajax_jobsearch_load_more_userdash_notifics', array($this, 'load_more_userdash_notifics'));

            //
            add_action('wp_ajax_jobsearch_chekunchk_notific_setin', array($this, 'chekunchk_notific_setin'));
            
            //
            add_action('jobsearch_member_after_making_cand_or_emp', array($this, 'make_settings_after_register'), 15, 2);
            
            add_filter('careerfy_mobile_hderstrip_btns_after_html', array($this, 'mobile_hder_notifics_btn'), 5);
            
            add_filter('careerfy_mobile_navigation_after', array($this, 'dashnotifics_mobile_navigation_after'), 15);
        }

        public function userdash_notific_readmark_act() {
            $item_id = isset($_POST['item_id']) ? $_POST['item_id'] : '';
            $readm_type = isset($_POST['readm_type']) ? $_POST['readm_type'] : '';
            $user_id = get_current_user_id();
            $user_is_candidate = jobsearch_user_is_candidate($user_id);
            $user_is_employer = jobsearch_user_is_employer($user_id);

            $anchor_allowed_tags = array(
                'a' => array(
                    'href' => array(),
                    'title' => array(),
                )
            );
            
            $notific_msg = '';
            
            if ($user_is_candidate) {
                $candidate_id = jobsearch_get_user_candidate_id($user_id);
                $notifics_list = $this->get_cand_notifics($candidate_id);
            }
            if ($user_is_employer) {
                $employer_id = jobsearch_get_user_employer_id($user_id);
                $notifics_list = $this->get_emp_notifics($employer_id);
            }
            if (!empty($notifics_list)) {
                $noti_counter = 0;
                foreach ($notifics_list as $notific_item) {
                    $notific_unique_id = isset($notific_item['unique_id']) ? $notific_item['unique_id'] : '';
                    $notific_type = isset($notific_item['type']) ? $notific_item['type'] : '';
                    $notific_time = isset($notific_item['time']) ? $notific_item['time'] : '';
                    $notific_job_id = isset($notific_item['job_id']) ? $notific_item['job_id'] : '';
                    $notific_employer_id = isset($notific_item['employer_id']) ? $notific_item['employer_id'] : '';
                    $notific_cand_id = isset($notific_item['candidate_id']) ? $notific_item['candidate_id'] : '';
                    $notific_viewed = isset($notific_item['viewed']) ? $notific_item['viewed'] : '';
                    if ($notific_unique_id == $item_id) {
                        $notifics_list[$noti_counter]['viewed'] = 1;
                        $notimsg_html = '';
                        if ($notific_type == 'post_new_job') {
                            $notimsg_html = sprintf(wp_kses(__('A new job \'<a href="%s">%s</a>\' is posted by \'<a href="%s">%s</a>\'.', 'wp-jobsearch'), $anchor_allowed_tags), get_permalink($notific_job_id), get_the_title($notific_job_id), get_permalink($notific_employer_id), get_the_title($notific_employer_id));
                        }
                        if ($notific_type == 'shortlist_for_intrview') {
                            $notimsg_html = sprintf(wp_kses(__('You are shortlisted for interview the job \'<a href="%s">%s</a>\' by \'<a href="%s">%s</a>\' you applied.', 'wp-jobsearch'), $anchor_allowed_tags), get_permalink($notific_job_id), get_the_title($notific_job_id), get_permalink($notific_employer_id), get_the_title($notific_employer_id));
                        }
                        if ($notific_type == 'reject_for_intrview') {
                            $notimsg_html = sprintf(wp_kses(__('You are rejected for interview the job \'<a href="%s">%s</a>\' by \'<a href="%s">%s</a>\' you applied.', 'wp-jobsearch'), $anchor_allowed_tags), get_permalink($notific_job_id), get_the_title($notific_job_id), get_permalink($notific_employer_id), get_the_title($notific_employer_id));
                        }
                        if ($notific_type == 'on_apply_job') {
                            $notimsg_html = sprintf(wp_kses(__('A new application is submitted on your job \'<a href="%s">%s</a>\' by \'<a href="%s">%s</a>\'.', 'wp-jobsearch'), $anchor_allowed_tags), get_permalink($notific_job_id), get_the_title($notific_job_id), get_permalink($notific_cand_id), get_the_title($notific_cand_id));
                        }
                        $notimsg_html = apply_filters('jobsearch_user_notifics_list_item_html', $notimsg_html, $notific_item);
                        if (isset($notimsg_html) && $notimsg_html != '') {
                            if ($readm_type == 'readin_less') {
                                $notific_msg = wp_trim_words($notimsg_html, 6);
                            } else {
                                $notific_msg = $notimsg_html;
                            }
                        }
                    }
                    $noti_counter++;
                }
                if ($user_is_employer) {
                    update_post_meta($employer_id, 'jobsearch_emp_notifics_list', $notifics_list);
                } else {
                    update_post_meta($candidate_id, 'jobsearch_cand_notifics_list', $notifics_list);
                }
                
                $notifics_list_arr = self::get_total_notifics_arr($user_id, true);

                $notifics_count = !empty($notifics_list_arr) && is_array($notifics_list_arr) ? count($notifics_list_arr) : 0;

                echo json_encode(array('success' => '1', 'msg' => $notific_msg, 'count' => $notifics_count));
                die;
            }
            echo json_encode(array('success' => '0', 'msg' => $notific_msg, 'count' => ''));
            die;
        }

        public static function seting_onoff_btn($id, $txt_type, $is_selected, $title) {
            $notific_option_selected = $is_selected == 'yes' ? 'checked' : '';
            ?>
            <div class="jobsearch-onoffswitch-outer">
                <div class="jobsearch-dashbord-onoffswitch">
                    <input id="<?php echo ($id) ?>" type="checkbox" class="opt_notific_setcheckbtn" data-type="<?php echo ($txt_type) ?>" <?php echo ($notific_option_selected) ?>>
                    <label class="jobsearch-optnotific-label" for="<?php echo ($id) ?>">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
                <span class="jobsearch-onoffswitch-title"><?php echo ($title) ?> <strong class="opt-notific-lodr"></strong></span>
            </div>
            <?php
        }
        
        public function cand_dash_notifications($html, $candidate_id) {
            global $jobsearch_plugin_options;
            
            ob_start();
            $dash_notifics_switch = isset($jobsearch_plugin_options['dash_notifics_switch']) ? $jobsearch_plugin_options['dash_notifics_switch'] : '';
            $add_notifics_for_cands = isset($jobsearch_plugin_options['add_notifics_for_cands']) ? $jobsearch_plugin_options['add_notifics_for_cands'] : '';
            
            $ismeta_newjobpost_exist = metadata_exists('post', $candidate_id, 'jobsearch_field_notific_newjobpost');
            if (!$ismeta_newjobpost_exist) {
                update_post_meta($candidate_id, 'jobsearch_field_notific_newjobpost', 'yes');
            }
            $ismeta_shortforinter_exist = metadata_exists('post', $candidate_id, 'jobsearch_field_notific_shortforinter');
            if (!$ismeta_shortforinter_exist) {
                update_post_meta($candidate_id, 'jobsearch_field_notific_shortforinter', 'yes');
            }
            $ismeta_rejctforinter_exist = metadata_exists('post', $candidate_id, 'jobsearch_field_notific_rejctforinter');
            if (!$ismeta_rejctforinter_exist) {
                update_post_meta($candidate_id, 'jobsearch_field_notific_rejctforinter', 'yes');
            }
            
            if ($dash_notifics_switch == 'on' && $add_notifics_for_cands == 'on') {
                $user_id = get_current_user_id();
                $per_page = 5;
                $sh_typedf = isset($_GET['notifications-tab']) ? $_GET['notifications-tab'] : '';
                ?>
                <div id="jobsearch-notification-section" class="jobsearch-employer-box-section jobsearch-dashnotifics-bars">
                    <div class="jobsearch-profile-title">
                        <h2><?php esc_html_e('Notifications', 'wp-jobsearch') ?></h2>
                        <a href="javascript:void(0);" class="dash-hdtabchng-btn notifics-showlist-tobtn" style="display: <?php echo ($sh_typedf == 'settings' ? 'inline-block' : 'none') ?>;"><?php esc_html_e('Notifications List', 'wp-jobsearch') ?></a>
                        <a href="javascript:void(0);" class="dash-hdtabchng-btn notifics-showsetings-tobtn" style="display: <?php echo ($sh_typedf == 'settings' ? 'none' : 'inline-block') ?>;"><?php esc_html_e('Settings', 'wp-jobsearch') ?></a>
                    </div>
                    <?php
                    $notifics_candto_newjob = isset($jobsearch_plugin_options['notifics_candto_newjob']) ? $jobsearch_plugin_options['notifics_candto_newjob'] : '';
                    $notifics_cand_shrtforinter = isset($jobsearch_plugin_options['notifics_cand_shrtforinter']) ? $jobsearch_plugin_options['notifics_cand_shrtforinter'] : '';
                    $notifics_cand_rejctforinter = isset($jobsearch_plugin_options['notifics_cand_rejctforinter']) ? $jobsearch_plugin_options['notifics_cand_rejctforinter'] : '';
                    
                    $notifics_me_newjob = get_post_meta($candidate_id, 'jobsearch_field_notific_newjobpost', true);
                    $notifics_me_shrtforinter = get_post_meta($candidate_id, 'jobsearch_field_notific_shortforinter', true);
                    $notifics_me_rejctforinter = get_post_meta($candidate_id, 'jobsearch_field_notific_rejctforinter', true);
                    
                    //
                    if ($notifics_candto_newjob == 'on' || $notifics_cand_shrtforinter == 'on' || $notifics_cand_rejctforinter == 'on') {
                        ?>
                        <div class="jobsearch-notifics-setopts" style="display: <?php echo ($sh_typedf == 'settings' ? 'inline-block' : 'none') ?>;">
                            <ul class="jobsearch-row jobsearch-employer-profile-form">
                                <?php
                                if ($notifics_candto_newjob == 'on') {
                                    ?>
                                    <li class="jobsearch-column-6">
                                        <?php self::seting_onoff_btn('opt-notific-newjobpost', 'notific_newjobpost', $notifics_me_newjob, esc_html__('Notify me on new job post', 'wp-jobsearch')) ?>
                                    </li>
                                    <?php
                                }
                                //
                                if ($notifics_cand_shrtforinter == 'on') {
                                    ?>
                                    <li class="jobsearch-column-6">
                                        <?php self::seting_onoff_btn('opt-notific-shortforinter', 'notific_shortforinter', $notifics_me_shrtforinter, esc_html__('Notify me when Shortlist for Interview', 'wp-jobsearch')) ?>
                                    </li>
                                    <?php
                                }
                                //
                                if ($notifics_cand_rejctforinter == 'on') {
                                    ?>
                                    <li class="jobsearch-column-6">
                                        <?php self::seting_onoff_btn('opt-notific-rejctforinter', 'notific_rejctforinter', $notifics_me_rejctforinter, esc_html__('Notify me when Reject for Interview', 'wp-jobsearch')) ?>
                                    </li>
                                    <?php
                                }
                                do_action('jobsearch_candash_notific_settins_btns_after', $candidate_id);
                                ?>
                            </ul>
                        </div>
                        <?php
                    }
                    //

                    ?>
                    <div class="jobsearch-notifics-loistitms" style="display: <?php echo ($sh_typedf == 'settings' ? 'none' : 'inline-block') ?>;">
                        <?php
                        $notifics_list_arr = self::get_total_notifics_arr($user_id);

                        if (!empty($notifics_list_arr)) {
                            $total_notifics = count($notifics_list_arr);
                            krsort($notifics_list_arr);

                            $start = 0;
                            $offset = $per_page;

                            $notifics_list_arr = array_slice($notifics_list_arr, $start, $offset);
                            ?>
                            <div class="jobsearch-notifics-listcon">
                                <ul class="jobsearch-dashnotifics-list">
                                    <?php
                                    self::user_notifics_list_html($notifics_list_arr);
                                    ?>
                                </ul>
                                <?php
                                if ($total_notifics > $per_page) {
                                    $total_pages = ceil($total_notifics / $per_page);
                                    ?>
                                    <div class="lodmore-notifics-btnsec">
                                        <a href="javascript:void(0);" class="lodmore-notific-btn" data-tpages="<?php echo ($total_pages) ?>" data-gtopage="2"><?php esc_html_e('Load More', 'wp-jobsearch') ?></a>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                        } else {
                            ?>
                            <p class="dash-notifics-nofound"><?php esc_html_e('You have no notifications.', 'wp-jobsearch') ?></p>
                            <?php
                        }
                        //
                        ?>
                    </div>
                </div>
                <?php
            }
            $html = ob_get_clean();
            
            return $html;
        }

        public function emp_dash_notifications($html, $employer_id) {
            global $jobsearch_plugin_options;
            
            ob_start();
            $dash_notifics_switch = isset($jobsearch_plugin_options['dash_notifics_switch']) ? $jobsearch_plugin_options['dash_notifics_switch'] : '';
            $add_notifics_for_emps = isset($jobsearch_plugin_options['add_notifics_for_emps']) ? $jobsearch_plugin_options['add_notifics_for_emps'] : '';
            if ($dash_notifics_switch == 'on' && $add_notifics_for_emps == 'on') {
                $user_id = get_current_user_id();
                $per_page = 5;
                $sh_typedf = isset($_GET['notifications-tab']) ? $_GET['notifications-tab'] : '';
                
                $ismeta_onaplyjob_exist = metadata_exists('post', $employer_id, 'jobsearch_field_notific_onaplyjob');
                if (!$ismeta_onaplyjob_exist) {
                    update_post_meta($employer_id, 'jobsearch_field_notific_onaplyjob', 'yes');
                }
                ?>
                <div id="jobsearch-notification-section" class="jobsearch-employer-box-section jobsearch-dashnotifics-bars">
                    <div class="jobsearch-profile-title">
                        <h2><?php esc_html_e('Notifications', 'wp-jobsearch') ?></h2>
                        <a href="javascript:void(0);" class="dash-hdtabchng-btn notifics-showlist-tobtn" style="display: <?php echo ($sh_typedf == 'settings' ? 'inline-block' : 'none') ?>;"><?php esc_html_e('Notifications List', 'wp-jobsearch') ?></a>
                        <a href="javascript:void(0);" class="dash-hdtabchng-btn notifics-showsetings-tobtn" style="display: <?php echo ($sh_typedf == 'settings' ? 'none' : 'inline-block') ?>;"><?php esc_html_e('Settings', 'wp-jobsearch') ?></a>
                    </div>
                    <?php
                    $notifics_empto_applyjob = isset($jobsearch_plugin_options['notifics_empto_applyjob']) ? $jobsearch_plugin_options['notifics_empto_applyjob'] : '';
                    
                    $notifics_me_onaplyjob = get_post_meta($employer_id, 'jobsearch_field_notific_onaplyjob', true);
                    
                    //
                    if ($notifics_empto_applyjob == 'on') {
                        ?>
                        <div class="jobsearch-notifics-setopts" style="display: <?php echo ($sh_typedf == 'settings' ? 'inline-block' : 'none') ?>;">
                            <ul class="jobsearch-row jobsearch-employer-profile-form">
                                <?php
                                if ($notifics_empto_applyjob == 'on') {
                                    $notific_option_selected = $notifics_me_onaplyjob == 'yes' ? 'checked' : '';
                                    ?>
                                    <li class="jobsearch-column-6">
                                        <?php self::seting_onoff_btn('opt-notific-applyjob', 'notific_onaplyjob', $notifics_me_onaplyjob, esc_html__('Notify me on apply job', 'wp-jobsearch')) ?>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>
                        <?php
                    }
                    //

                    ?>
                    <div class="jobsearch-notifics-loistitms" style="display: <?php echo ($sh_typedf == 'settings' ? 'none' : 'inline-block') ?>;">
                        <?php
                        $notifics_list_arr = self::get_total_notifics_arr($user_id);

                        if (!empty($notifics_list_arr)) {
                            $total_notifics = count($notifics_list_arr);
                            krsort($notifics_list_arr);

                            $start = 0;
                            $offset = $per_page;

                            $notifics_list_arr = array_slice($notifics_list_arr, $start, $offset);
                            ?>
                            <div class="jobsearch-notifics-listcon">
                                <ul class="jobsearch-dashnotifics-list">
                                    <?php
                                    self::user_notifics_list_html($notifics_list_arr);
                                    ?>
                                </ul>
                                <?php
                                if ($total_notifics > $per_page) {
                                    $total_pages = ceil($total_notifics / $per_page);
                                    ?>
                                    <div class="lodmore-notifics-btnsec">
                                        <a href="javascript:void(0);" class="lodmore-notific-btn" data-tpages="<?php echo ($total_pages) ?>" data-gtopage="2"><?php esc_html_e('Load More', 'wp-jobsearch') ?></a>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                        } else {
                            ?>
                            <p class="dash-notifics-nofound"><?php esc_html_e('You have no notifications.', 'wp-jobsearch') ?></p>
                            <?php
                        }
                        //
                        ?>
                    </div>
                </div>
                <?php
            }
            $html = ob_get_clean();
            
            return $html;
        }
        
        public function load_more_userdash_notifics() {
            global $jobsearch_plugin_options;
            
            $page_num = absint($_POST['page_num']);
            
            ob_start();
            $dash_notifics_switch = isset($jobsearch_plugin_options['dash_notifics_switch']) ? $jobsearch_plugin_options['dash_notifics_switch'] : '';
            if ($dash_notifics_switch == 'on') {
                $user_id = get_current_user_id();
                
                $per_page = 5;
                $notifics_list_arr = self::get_total_notifics_arr($user_id);
                    
                if (!empty($notifics_list_arr)) {
                    $total_notifics = count($notifics_list_arr);
                    krsort($notifics_list_arr);

                    $start = ($page_num - 1) * ($per_page);
                    $offset = $per_page;

                    $notifics_list_arr = array_slice($notifics_list_arr, $start, $offset);
                    self::user_notifics_list_html($notifics_list_arr);
                }
            }
            
            $html = ob_get_clean();
            echo json_encode(array('html' => $html));
            die;
        }

        public static function user_notifics_list_html($notifics_list_arr) {
            foreach ($notifics_list_arr as $notific_item) {
                $notific_unique_id = isset($notific_item['unique_id']) ? $notific_item['unique_id'] : '';
                $notific_type = isset($notific_item['type']) ? $notific_item['type'] : '';
                $notific_time = isset($notific_item['time']) ? $notific_item['time'] : '';
                $notific_job_id = isset($notific_item['job_id']) ? $notific_item['job_id'] : '';
                $notific_employer_id = isset($notific_item['employer_id']) ? $notific_item['employer_id'] : '';
                $notific_cand_id = isset($notific_item['candidate_id']) ? $notific_item['candidate_id'] : '';
                $notific_viewed = isset($notific_item['viewed']) ? $notific_item['viewed'] : '';

                $anchor_allowed_tags = array(
                    'a' => array(
                        'href' => array(),
                        'title' => array(),
                    )
                );
                $markread_btn_html = '';
                if ($notific_viewed == 0) {
                    $markread_btn_html = '<a href="javascript:void(0);" class="readmore-notific-btn btn-readmore-mode" data-id="' . ($notific_unique_id) . '" data-readl="' . esc_html__('Read less', 'wp-jobsearch') . '" data-readm="' . esc_html__('Read more', 'wp-jobsearch') . '">' . esc_html__('Read more', 'wp-jobsearch') . '</a>';
                }
                $item_html = '';
                $notif_type_class = 'shrtlist-for-jobnoti';
                $notif_type_icon = '<i class="fa fa-info"></i>';
                if ($notific_type == 'post_new_job') {
                    $notif_type_class = 'posted-new-jobnoti';
                    $notif_type_icon = '<i class="fa fa-check"></i>';
                    $item_html = sprintf(wp_kses(__('A new job \'<a href="%s">%s</a>\' is posted by \'<a href="%s">%s</a>\'.', 'wp-jobsearch'), $anchor_allowed_tags), get_permalink($notific_job_id), get_the_title($notific_job_id), get_permalink($notific_employer_id), get_the_title($notific_employer_id));
                }
                if ($notific_type == 'shortlist_for_intrview') {
                    $notif_type_class = 'shrtlist-for-jobnoti';
                    $notif_type_icon = '<i class="fa fa-info"></i>';
                    $item_html = sprintf(wp_kses(__('You are shortlisted for interview the job \'<a href="%s">%s</a>\' by \'<a href="%s">%s</a>\' you applied.', 'wp-jobsearch'), $anchor_allowed_tags), get_permalink($notific_job_id), get_the_title($notific_job_id), get_permalink($notific_employer_id), get_the_title($notific_employer_id));
                }
                if ($notific_type == 'reject_for_intrview') {
                    $notif_type_class = 'reject-for-jobnoti';
                    $notif_type_icon = '<i class="fa fa-times"></i>';
                    $item_html = sprintf(wp_kses(__('You are rejected for interview the job \'<a href="%s">%s</a>\' by \'<a href="%s">%s</a>\' you applied.', 'wp-jobsearch'), $anchor_allowed_tags), get_permalink($notific_job_id), get_the_title($notific_job_id), get_permalink($notific_employer_id), get_the_title($notific_employer_id));
                }
                if ($notific_type == 'on_apply_job') {
                    $notif_type_class = 'candapply-for-jobnoti';
                    $notif_type_icon = '<i class="fa fa-check"></i>';
                    $item_html = sprintf(wp_kses(__('A new application is submitted on your job \'<a href="%s">%s</a>\' by \'<a href="%s">%s</a>\'.', 'wp-jobsearch'), $anchor_allowed_tags), get_permalink($notific_job_id), get_the_title($notific_job_id), get_permalink($notific_cand_id), get_the_title($notific_cand_id));
                }
                $item_html = apply_filters('jobsearch_user_notifics_list_item_html', $item_html, $notific_item);
                ?>
                <li class="jobsearch-notification-item <?php echo ($notific_viewed == 1 ? 'read-notific' : 'unread-notific') ?> <?php echo ($notif_type_class) ?>">
                    <?php
                    if (isset($item_html) && $item_html != '') {
                        ?>
                        <div class="notificate-item-inner">
                            <span class="notific-check-icon"><?php echo ($notif_type_icon) ?></span>
                            <strong><span class="notific-onlmsg-con"><?php echo ($notific_viewed == 1 ? $item_html : wp_trim_words($item_html, 6)) ?></span> <span class="notific-item-datetime"><?php echo date_i18n(get_option('date_format'), $notific_time) ?></span> <?php echo ($markread_btn_html) ?></strong>
                            <a href="javascript:void(0);" class="close-notific-item" data-id="<?php echo ($notific_unique_id) ?>"><i class="fa fa-close"></i></a>
                        </div>
                        <?php
                    }
                    ?>
                </li>
                <?php
            }
        }

        public function get_cand_notifics($candidate_id) {
            $notifics_list = get_post_meta($candidate_id, 'jobsearch_cand_notifics_list', true);
            return $notifics_list;
        }

        public function get_emp_notifics($employer_id) {
            $notifics_list = get_post_meta($employer_id, 'jobsearch_emp_notifics_list', true);
            return $notifics_list;
        }

        public function on_front_job_post_notifics($job_id) {
            global $jobsearch_plugin_options;
            $notifics_candto_newjob = isset($jobsearch_plugin_options['notifics_candto_newjob']) ? $jobsearch_plugin_options['notifics_candto_newjob'] : '';
            $dash_notifics_switch = isset($jobsearch_plugin_options['dash_notifics_switch']) ? $jobsearch_plugin_options['dash_notifics_switch'] : '';
            $add_notifics_for_cands = isset($jobsearch_plugin_options['add_notifics_for_cands']) ? $jobsearch_plugin_options['add_notifics_for_cands'] : '';
            
            $employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
            $emp_user_id = jobsearch_get_employer_user_id($employer_id);
            $employer_follows_list = get_user_meta($emp_user_id, 'jobsearch-user-followins-list', true);
            
            if (!empty($employer_follows_list) && $dash_notifics_switch == 'on' && $add_notifics_for_cands == 'on' && $notifics_candto_newjob == 'on') {
                foreach ($employer_follows_list as $folow_item) {
                    $cand_id = isset($folow_item['post_id']) ? $folow_item['post_id'] : '';
                    $notifics_me_newjob = get_post_meta($cand_id, 'jobsearch_field_notific_newjobpost', true);
                    if ($notifics_me_newjob == 'yes') {
                        $notifics_list = $this->get_cand_notifics($cand_id);
                        $notifics_list = !empty($notifics_list) ? $notifics_list : array();
                        //
                        $notific_data = array(
                            'unique_id' => uniqid(),
                            'type' => 'post_new_job',
                            'time' => current_time('timestamp'),
                            'job_id' => $job_id,
                            'employer_id' => $employer_id,
                            'viewed' => 0,
                        );
                        $notifics_list[] = $notific_data;
                        //var_dump($notifics_list);
                        update_post_meta($cand_id, 'jobsearch_cand_notifics_list', $notifics_list);
                    }
                    //
                }
            }
        }
        
        public function on_candidate_shortlisted_for_interview($emp_user_obj, $job_id, $candidate_id) {
            global $jobsearch_plugin_options;
            $notifics_cand_shrtforinter = isset($jobsearch_plugin_options['notifics_cand_shrtforinter']) ? $jobsearch_plugin_options['notifics_cand_shrtforinter'] : '';
            $dash_notifics_switch = isset($jobsearch_plugin_options['dash_notifics_switch']) ? $jobsearch_plugin_options['dash_notifics_switch'] : '';
            $add_notifics_for_cands = isset($jobsearch_plugin_options['add_notifics_for_cands']) ? $jobsearch_plugin_options['add_notifics_for_cands'] : '';
            
            $employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
            $emp_user_id = isset($emp_user_obj->ID) ? $emp_user_obj->ID : '';
            $employer_follows_list = get_user_meta($emp_user_id, 'jobsearch-user-followins-list', true);
            if ($dash_notifics_switch == 'on' && $add_notifics_for_cands == 'on' && $notifics_cand_shrtforinter == 'on') {
                $is_follower = false;
                if (!empty($employer_follows_list)) {
                    foreach ($employer_follows_list as $folow_item) {
                        $cand_id = isset($folow_item['post_id']) ? $folow_item['post_id'] : '';
                        if ($cand_id == $candidate_id) {
                            $is_follower = true;
                            break;
                        }
                    }
                }
                $is_follower = true;
                if ($is_follower) {
                    $notifics_me_shrtforinter = get_post_meta($candidate_id, 'jobsearch_field_notific_shortforinter', true);
                    if ($notifics_me_shrtforinter == 'yes') {
                        $notifics_list = $this->get_cand_notifics($candidate_id);
                        $notifics_list = !empty($notifics_list) ? $notifics_list : array();
                        //
                        $notific_data = array(
                            'unique_id' => uniqid(),
                            'type' => 'shortlist_for_intrview',
                            'time' => current_time('timestamp'),
                            'job_id' => $job_id,
                            'employer_id' => $employer_id,
                            'viewed' => 0,
                        );
                        $notifics_list[] = $notific_data;
                        //var_dump($notifics_list);
                        update_post_meta($candidate_id, 'jobsearch_cand_notifics_list', $notifics_list);
                    }
                }
            }
            //
        }
        
        public function on_candidate_rejected_for_interview($emp_user_obj, $job_id, $candidate_id) {
            global $jobsearch_plugin_options;

            $dash_notifics_switch = isset($jobsearch_plugin_options['dash_notifics_switch']) ? $jobsearch_plugin_options['dash_notifics_switch'] : '';
            
            $add_notifics_for_cands = isset($jobsearch_plugin_options['add_notifics_for_cands']) ? $jobsearch_plugin_options['add_notifics_for_cands'] : '';
            $notifics_cand_rejctforinter = isset($jobsearch_plugin_options['notifics_cand_rejctforinter']) ? $jobsearch_plugin_options['notifics_cand_rejctforinter'] : '';
            
            $employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
            $emp_user_id = isset($emp_user_obj->ID) ? $emp_user_obj->ID : '';
            $employer_follows_list = get_user_meta($emp_user_id, 'jobsearch-user-followins-list', true);
            if ($dash_notifics_switch == 'on' && $add_notifics_for_cands == 'on' && $notifics_cand_rejctforinter == 'on') {
                $is_follower = false;
                if (!empty($employer_follows_list)) {
                    foreach ($employer_follows_list as $folow_item) {
                        $cand_id = isset($folow_item['post_id']) ? $folow_item['post_id'] : '';
                        if ($cand_id == $candidate_id) {
                            $is_follower = true;
                            break;
                        }
                    }
                }
                $is_follower = true;
                if ($is_follower) {
                    $notifics_me_rejctforinter = get_post_meta($candidate_id, 'jobsearch_field_notific_rejctforinter', true);
                    if ($notifics_me_rejctforinter == 'yes') {
                        $notifics_list = $this->get_cand_notifics($candidate_id);
                        $notifics_list = !empty($notifics_list) ? $notifics_list : array();
                        //
                        $notific_data = array(
                            'unique_id' => uniqid(),
                            'type' => 'reject_for_intrview',
                            'time' => current_time('timestamp'),
                            'job_id' => $job_id,
                            'employer_id' => $employer_id,
                            'viewed' => 0,
                        );
                        $notifics_list[] = $notific_data;
                        update_post_meta($candidate_id, 'jobsearch_cand_notifics_list', $notifics_list);
                    }
                }
            }
            //
        }
        
        public function on_candidate_apply_for_job($candidate_id, $job_id) {
            global $jobsearch_plugin_options;

            $dash_notifics_switch = isset($jobsearch_plugin_options['dash_notifics_switch']) ? $jobsearch_plugin_options['dash_notifics_switch'] : '';
            
            $add_notifics_for_emps = isset($jobsearch_plugin_options['add_notifics_for_emps']) ? $jobsearch_plugin_options['add_notifics_for_emps'] : '';
            $notifics_empto_applyjob = isset($jobsearch_plugin_options['notifics_empto_applyjob']) ? $jobsearch_plugin_options['notifics_empto_applyjob'] : '';
            
            $employer_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);
            if ($dash_notifics_switch == 'on' && $add_notifics_for_emps == 'on' && $notifics_empto_applyjob == 'on') {
                
                $notifics_me_onaplyjob = get_post_meta($employer_id, 'jobsearch_field_notific_onaplyjob', true);
                if ($notifics_me_onaplyjob == 'yes') {
                    $notifics_list = $this->get_emp_notifics($employer_id);
                    $notifics_list = !empty($notifics_list) ? $notifics_list : array();
                    //
                    $notific_data = array(
                        'unique_id' => uniqid(),
                        'type' => 'on_apply_job',
                        'time' => current_time('timestamp'),
                        'job_id' => $job_id,
                        'candidate_id' => $candidate_id,
                        'viewed' => 0,
                    );
                    $notifics_list[] = $notific_data;
                    //var_dump($notifics_list);
                    update_post_meta($employer_id, 'jobsearch_emp_notifics_list', $notifics_list);
                }
            }
            //
        }
        
        public function mobile_hder_notifics_btn($html) {
            global $jobsearch_plugin_options;

            $dash_notifics_switch = isset($jobsearch_plugin_options['dash_notifics_switch']) ? $jobsearch_plugin_options['dash_notifics_switch'] : '';

            $header_notifics_btn = isset($jobsearch_plugin_options['header_notifics_btn']) ? $jobsearch_plugin_options['header_notifics_btn'] : '';
            
            ob_start();
            if ($dash_notifics_switch == 'on' && $header_notifics_btn != 'off') {
                if (is_user_logged_in()) {
                    $user_id = get_current_user_id();
                    $user_is_candidate = jobsearch_user_is_candidate($user_id);
                    $user_is_employer = jobsearch_user_is_employer($user_id);

                    $show_notifics_btn = false;
                    if ($header_notifics_btn == 'public') {
                        if ($user_is_candidate || $user_is_employer) {
                            $show_notifics_btn = true;
                        }
                    } else if ($header_notifics_btn == 'for_cand' && $user_is_candidate) {
                        $show_notifics_btn = true;
                    } else if ($header_notifics_btn == 'for_emp' && $user_is_employer) {
                        $show_notifics_btn = true;
                    } else if ($header_notifics_btn == 'for_both' && ($user_is_candidate || $user_is_employer)) {
                        $show_notifics_btn = true;
                    }

                    if ($show_notifics_btn) {

                        $total_notifics = 0;

                        $notifics_list_arr = self::get_total_notifics_arr($user_id, true);
                        if (!empty($notifics_list_arr)) {
                            krsort($notifics_list_arr);
                        }
                        $unviewd_notifics_arr = $notifics_list_arr;

                        // for read notifications
                        if (empty($notifics_list_arr) || (!empty($notifics_list_arr) && count($notifics_list_arr) < 5)) {
                            $notifics_readlist_arr = self::get_total_notifics_arr($user_id, 'viewed_only');

                            if (!empty($notifics_readlist_arr)) {
                                krsort($notifics_readlist_arr);

                                $unreadlist_offset = !empty($notifics_list_arr) ? (5 - (count($notifics_list_arr))) : 5;

                                $notifics_readlist_arr = array_slice($notifics_readlist_arr, 0, $unreadlist_offset);

                                $notifics_list_arr = array_merge($notifics_list_arr, $notifics_readlist_arr);
                            }
                        }
                        //

                        if (!empty($notifics_list_arr)) {
                            $total_notifics = !empty($unviewd_notifics_arr) ? count($unviewd_notifics_arr) : 0;
                            //
                        }
                        ?>
                        <a href="javascript:void(0);" class="mobile-usernotifics-btn"><i class="fa fa-bell"></i><span class="hderbell-notifics-count"><?php echo ($total_notifics) ?></span></a>
                        <?php
                    }
                } else {
                    if ($header_notifics_btn == 'public') {
                        ?>
                        <a href="javascript:void(0);" class="mobile-usernotifics-btn"><i class="fa fa-bell"></i><span class="hderbell-notifics-count">0</span></a>
                        <?php
                    }
                }
            }
            $html = ob_get_clean();
            
            return $html;
        }
        
        function dashnotifics_mobile_navigation_after() {
            global $jobsearch_plugin_options;
            
            $dash_notifics_switch = isset($jobsearch_plugin_options['dash_notifics_switch']) ? $jobsearch_plugin_options['dash_notifics_switch'] : '';

            $header_notifics_btn = isset($jobsearch_plugin_options['header_notifics_btn']) ? $jobsearch_plugin_options['header_notifics_btn'] : '';
            if ($dash_notifics_switch == 'on' && $header_notifics_btn != 'off') {
                ?>
                <div class="jobsearch-mobile-notificsdet careerfy-inmobile-itemsgen" style="display: none;">
                    <ul>
                        <?php
                        if (is_user_logged_in()) {
                            
                            $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
                            $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');

                            $page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
                            $page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');

                            $dash_page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page');

                            $user_id = get_current_user_id();
                            $user_is_candidate = jobsearch_user_is_candidate($user_id);
                            $user_is_employer = jobsearch_user_is_employer($user_id);

                            $show_notifics_btn = false;
                            if ($header_notifics_btn == 'public') {
                                if ($user_is_candidate || $user_is_employer) {
                                    $show_notifics_btn = true;
                                }
                            } else if ($header_notifics_btn == 'for_cand' && $user_is_candidate) {
                                $show_notifics_btn = true;
                            } else if ($header_notifics_btn == 'for_emp' && $user_is_employer) {
                                $show_notifics_btn = true;
                            } else if ($header_notifics_btn == 'for_both' && ($user_is_candidate || $user_is_employer)) {
                                $show_notifics_btn = true;
                            }

                            if ($show_notifics_btn) {

                                $notifics_items_html = '';

                                $total_notifics = 0;

                                $notifics_list_arr = self::get_total_notifics_arr($user_id, true);
                                if (!empty($notifics_list_arr)) {
                                    krsort($notifics_list_arr);
                                }
                                $unviewd_notifics_arr = $notifics_list_arr;

                                // for read notifications
                                if (empty($notifics_list_arr) || (!empty($notifics_list_arr) && count($notifics_list_arr) < 5)) {
                                    $notifics_readlist_arr = self::get_total_notifics_arr($user_id, 'viewed_only');

                                    if (!empty($notifics_readlist_arr)) {
                                        krsort($notifics_readlist_arr);

                                        $unreadlist_offset = !empty($notifics_list_arr) ? (5 - (count($notifics_list_arr))) : 5;

                                        $notifics_readlist_arr = array_slice($notifics_readlist_arr, 0, $unreadlist_offset);

                                        $notifics_list_arr = array_merge($notifics_list_arr, $notifics_readlist_arr);
                                    }
                                }
                                //

                                if (!empty($notifics_list_arr)) {
                                    $total_notifics = !empty($unviewd_notifics_arr) ? count($unviewd_notifics_arr) : 0;
                                    //

                                    $start = 0;
                                    $offset = 5;

                                    $notifics_list_arr = array_slice($notifics_list_arr, $start, $offset);

                                    $anchor_allowed_tags = array(
                                        'a' => array(
                                            'href' => array(),
                                            'title' => array(),
                                        )
                                    );
                                    ob_start();
                                    foreach ($notifics_list_arr as $notific_item) {
                                        $notific_unique_id = isset($notific_item['unique_id']) ? $notific_item['unique_id'] : '';
                                        $notific_type = isset($notific_item['type']) ? $notific_item['type'] : '';
                                        $notific_time = isset($notific_item['time']) ? $notific_item['time'] : '';
                                        $notific_job_id = isset($notific_item['job_id']) ? $notific_item['job_id'] : '';
                                        $notific_employer_id = isset($notific_item['employer_id']) ? $notific_item['employer_id'] : '';
                                        $notific_cand_id = isset($notific_item['candidate_id']) ? $notific_item['candidate_id'] : '';
                                        $notific_viewed = isset($notific_item['viewed']) ? $notific_item['viewed'] : '';

                                        ?>
                                        <li class="jobsearch-notification-item">
                                            <?php
                                            $item_html = '';
                                            if ($notific_type == 'post_new_job') {
                                                $item_html = sprintf(wp_kses(__('A new job \'<a href="%s">%s</a>\' is posted by \'<a href="%s">%s</a>\'.', 'wp-jobsearch'), $anchor_allowed_tags), get_permalink($notific_job_id), get_the_title($notific_job_id), get_permalink($notific_employer_id), get_the_title($notific_employer_id));
                                            }
                                            if ($notific_type == 'shortlist_for_intrview') {
                                                $item_html = sprintf(wp_kses(__('You are shortlisted for interview the job \'<a href="%s">%s</a>\' by \'<a href="%s">%s</a>\' you applied.', 'wp-jobsearch'), $anchor_allowed_tags), get_permalink($notific_job_id), get_the_title($notific_job_id), get_permalink($notific_employer_id), get_the_title($notific_employer_id));
                                            }
                                            if ($notific_type == 'reject_for_intrview') {
                                                $item_html = sprintf(wp_kses(__('You are rejected for interview the job \'<a href="%s">%s</a>\' by \'<a href="%s">%s</a>\' you applied.', 'wp-jobsearch'), $anchor_allowed_tags), get_permalink($notific_job_id), get_the_title($notific_job_id), get_permalink($notific_employer_id), get_the_title($notific_employer_id));
                                            }
                                            if ($notific_type == 'on_apply_job') {
                                                $item_html = sprintf(wp_kses(__('A new application is submitted on your job \'<a href="%s">%s</a>\' by \'<a href="%s">%s</a>\'.', 'wp-jobsearch'), $anchor_allowed_tags), get_permalink($notific_job_id), get_the_title($notific_job_id), get_permalink($notific_cand_id), get_the_title($notific_cand_id));
                                            }
                                            $item_html = apply_filters('jobsearch_user_notifics_list_item_html', $item_html, $notific_item);
                                            if (isset($item_html) && $item_html != '') {
                                                ?>
                                                <div class="notificate-item-inner">
                                                    <strong><span class="notific-onlmsg-con"><?php echo wp_trim_words($item_html, 6) ?></span></strong>
                                                    <span class="notific-item-datetime"><?php echo date_i18n(get_option('date_format'), $notific_time) ?></span>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </li>
                                        <?php
                                    }
                                    $notifics_items_html = ob_get_clean();
                                }
                                ?>
                                <li class="jobsearch-usernotifics-menubtn">
                                    <div class="jobsearch-hdernotifics-listitms">
                                        <div class="hdernotifics-title-con">
                                            <span class="hder-notifics-title"><?php esc_html_e('Notifications', 'wp-jobsearch') ?></span>
                                            <span class="hder-notifics-count"><?php printf(wp_kses(__('<small>%s</small> new', 'wp-jobsearch'), array('small' => array())), $total_notifics) ?></span>
                                        </div>
                                        <?php
                                        if ($notifics_items_html != '') {
                                            ?>
                                            <ul class="jobsearch-hdrnotifics-list"><?php echo ($notifics_items_html) ?></ul>
                                            <div class="hdernotifics-after-con">
                                                <a href="<?php echo add_query_arg(array('notifications-tab' => 'settings'), $dash_page_url) ?>#jobsearch-notification-section" class="hdernotifics-settin-btn"><?php esc_html_e('Notification Settings', 'wp-jobsearch') ?></a>
                                                <a href="<?php echo ($dash_page_url) ?>#jobsearch-notification-section" class="hdernotifics-viewall-btn jobsearch-color"><?php esc_html_e('View All', 'wp-jobsearch') ?></a>
                                            </div>
                                            <?php
                                        } else {
                                            ?>
                                            <span class="hder-notifics-nofound"><?php esc_html_e('You have no notifications.', 'wp-jobsearch') ?></span>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </li>
                                <?php
                            }
                        } else {
                            if ($header_notifics_btn == 'public') {
                                ?>
                                <li class="jobsearch-usernotifics-menubtn">
                                    <div class="jobsearch-hdernotifics-listitms">
                                        <div class="hdernotifics-title-con">
                                            <span class="hder-notifics-title"><?php esc_html_e('Notifications', 'wp-jobsearch') ?></span>
                                            <span class="hder-notifics-count"><?php printf(wp_kses(__('<small>%s</small> new', 'wp-jobsearch'), array('small' => array())), 0) ?></span>
                                        </div>
                                        <span class="hder-notifics-nofound"><?php esc_html_e('You have no notifications.', 'wp-jobsearch') ?></span>
                                    </div>
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                </div>
                <?php
            }
        }
        
        //
        public function dashmenu_notifics_btn($html, $args = array()) {
            global $jobsearch_plugin_options;


            ob_start();
            $dash_notifics_switch = isset($jobsearch_plugin_options['dash_notifics_switch']) ? $jobsearch_plugin_options['dash_notifics_switch'] : '';
            $is_elementor_class = isset($args['is_elementor']) && $args['is_elementor'] == true ? 'jobsearch-usernotifics-elementor' : '';

            $header_notifics_btn = isset($jobsearch_plugin_options['header_notifics_btn']) ? $jobsearch_plugin_options['header_notifics_btn'] : '';
            if ($dash_notifics_switch == 'on' && $header_notifics_btn != 'off') {
                ob_start();
                ?>
                <li class="jobsearch-usernotifics-menubtn menu-item menu-item-type-custom menu-item-object-custom <?php echo ($is_elementor_class) ?>">
                    <a href="javascript:void(0);" class="elementor-item elementor-item-anchor"><i class="fa fa-bell-o"><span class="hderbell-notifics-count">0</span></i></a>
                    <div class="jobsearch-hdernotifics-listitms">
                        <div class="hdernotifics-title-con">
                            <span class="hder-notifics-title"><?php esc_html_e('Notifications', 'wp-jobsearch') ?></span>
                            <span class="hder-notifics-count"><?php printf(wp_kses(__('<small>%s</small> new', 'wp-jobsearch'), array('small' => array())), 0) ?></span>
                        </div>
                        <span class="hder-notifics-nofound"><?php esc_html_e('You have no notifications.', 'wp-jobsearch') ?></span>
                    </div>
                </li>
                <?php
                $public_btn_html = ob_get_clean();
                if (is_user_logged_in()) {
                    $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
                    $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');

                    $page_id = $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
                    $page_id = $user_dashboard_page = jobsearch__get_post_id($user_dashboard_page, 'page');

                    $dash_page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page');
                    
                    $user_id = get_current_user_id();
                    $user_is_candidate = jobsearch_user_is_candidate($user_id);
                    $user_is_employer = jobsearch_user_is_employer($user_id);
                    
                    $show_notifics_btn = false;
                    if ($header_notifics_btn == 'public') {
                        if ($user_is_candidate || $user_is_employer) {
                            $show_notifics_btn = true;
                        } else {
                            echo ($public_btn_html);
                        }
                    } else if ($header_notifics_btn == 'for_cand' && $user_is_candidate) {
                        $show_notifics_btn = true;
                    } else if ($header_notifics_btn == 'for_emp' && $user_is_employer) {
                        $show_notifics_btn = true;
                    } else if ($header_notifics_btn == 'for_both' && ($user_is_candidate || $user_is_employer)) {
                        $show_notifics_btn = true;
                    }
                    
                    if ($show_notifics_btn) {

                        $notifics_items_html = '';

                        $total_notifics = 0;

                        $notifics_list_arr = self::get_total_notifics_arr($user_id, true);
                        if (!empty($notifics_list_arr)) {
                            krsort($notifics_list_arr);
                        }
                        $unviewd_notifics_arr = $notifics_list_arr;

                        // for read notifications
                        if (empty($notifics_list_arr) || (!empty($notifics_list_arr) && count($notifics_list_arr) < 5)) {
                            $notifics_readlist_arr = self::get_total_notifics_arr($user_id, 'viewed_only');
                            
                            if (!empty($notifics_readlist_arr)) {
                                krsort($notifics_readlist_arr);

                                $unreadlist_offset = !empty($notifics_list_arr) ? (5 - (count($notifics_list_arr))) : 5;

                                $notifics_readlist_arr = array_slice($notifics_readlist_arr, 0, $unreadlist_offset);
                                
                                $notifics_list_arr = array_merge($notifics_list_arr, $notifics_readlist_arr);
                            }
                        }
                        //
                        if (!empty($notifics_list_arr)) {
                            $total_notifics = !empty($unviewd_notifics_arr) ? count($unviewd_notifics_arr) : 0;
                            //

                            $start = 0;
                            $offset = 5;

                            $notifics_list_arr = array_slice($notifics_list_arr, $start, $offset);

                            $anchor_allowed_tags = array(
                                'a' => array(
                                    'href' => array(),
                                    'title' => array(),
                                )
                            );
                            ob_start();
                            foreach ($notifics_list_arr as $notific_item) {
                                $notific_unique_id = isset($notific_item['unique_id']) ? $notific_item['unique_id'] : '';
                                $notific_type = isset($notific_item['type']) ? $notific_item['type'] : '';
                                $notific_time = isset($notific_item['time']) ? $notific_item['time'] : '';
                                $notific_job_id = isset($notific_item['job_id']) ? $notific_item['job_id'] : '';
                                $notific_employer_id = isset($notific_item['employer_id']) ? $notific_item['employer_id'] : '';
                                $notific_cand_id = isset($notific_item['candidate_id']) ? $notific_item['candidate_id'] : '';
                                $notific_viewed = isset($notific_item['viewed']) ? $notific_item['viewed'] : '';

                                ?>
                                <div class="jobsearch-notification-item">
                                    <?php
                                    $item_html = '';
                                    if ($notific_type == 'post_new_job') {
                                        $item_html = sprintf(wp_kses(__('A new job \'<a href="%s">%s</a>\' is posted by \'<a href="%s">%s</a>\'.', 'wp-jobsearch'), $anchor_allowed_tags), get_permalink($notific_job_id), get_the_title($notific_job_id), get_permalink($notific_employer_id), get_the_title($notific_employer_id));
                                    }
                                    if ($notific_type == 'shortlist_for_intrview') {
                                        $item_html = sprintf(wp_kses(__('You are shortlisted for interview the job \'<a href="%s">%s</a>\' by \'<a href="%s">%s</a>\' you applied.', 'wp-jobsearch'), $anchor_allowed_tags), get_permalink($notific_job_id), get_the_title($notific_job_id), get_permalink($notific_employer_id), get_the_title($notific_employer_id));
                                    }
                                    if ($notific_type == 'reject_for_intrview') {
                                        $item_html = sprintf(wp_kses(__('You are rejected for interview the job \'<a href="%s">%s</a>\' by \'<a href="%s">%s</a>\' you applied.', 'wp-jobsearch'), $anchor_allowed_tags), get_permalink($notific_job_id), get_the_title($notific_job_id), get_permalink($notific_employer_id), get_the_title($notific_employer_id));
                                    }
                                    if ($notific_type == 'on_apply_job') {
                                        $item_html = sprintf(wp_kses(__('A new application is submitted on your job \'<a href="%s">%s</a>\' by \'<a href="%s">%s</a>\'.', 'wp-jobsearch'), $anchor_allowed_tags), get_permalink($notific_job_id), get_the_title($notific_job_id), get_permalink($notific_cand_id), get_the_title($notific_cand_id));
                                    }
                                    $item_html = apply_filters('jobsearch_user_notifics_list_item_html', $item_html, $notific_item);
                                    if (isset($item_html) && $item_html != '') {
                                        ?>
                                        <div class="notificate-item-inner">
                                            <strong><span class="notific-onlmsg-con"><?php echo wp_trim_words($item_html, 6) ?></span></strong>
                                            <span class="notific-item-datetime"><?php echo date_i18n(get_option('date_format'), $notific_time) ?></span>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            $notifics_items_html = ob_get_clean();
                        }
                        ?>
                        <li class="jobsearch-usernotifics-menubtn menu-item menu-item-type-custom menu-item-object-custom <?php echo ($is_elementor_class) ?>">
                            <a href="javascript:void(0);" class="elementor-item elementor-item-anchor"><i class="fa fa-bell-o"><span class="hderbell-notifics-count"><?php echo ($total_notifics) ?></span></i></a>
                            <div class="jobsearch-hdernotifics-listitms">
                                <div class="hdernotifics-title-con">
                                    <span class="hder-notifics-title"><?php esc_html_e('Notifications', 'wp-jobsearch') ?></span>
                                    <span class="hder-notifics-count"><?php printf(wp_kses(__('<small>%s</small> new', 'wp-jobsearch'), array('small' => array())), $total_notifics) ?></span>
                                </div>
                                <?php
                                if ($notifics_items_html != '') {
                                    ?>
                                    <div class="jobsearch-hdrnotifics-list"><?php echo ($notifics_items_html) ?></div>
                                    <div class="hdernotifics-after-con">
                                        <a href="<?php echo add_query_arg(array('notifications-tab' => 'settings'), $dash_page_url) ?>#jobsearch-notification-section" class="hdernotifics-settin-btn"><?php esc_html_e('Notification Settings', 'wp-jobsearch') ?></a>
                                        <a href="<?php echo ($dash_page_url) ?>#jobsearch-notification-section" class="hdernotifics-viewall-btn jobsearch-color"><?php esc_html_e('View All', 'wp-jobsearch') ?></a>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <span class="hder-notifics-nofound"><?php esc_html_e('You have no notifications.', 'wp-jobsearch') ?></span>
                                    <?php
                                }
                                ?>
                            </div>
                        </li>
                        <?php
                    }
                } else {
                    if ($header_notifics_btn == 'public') {
                        echo ($public_btn_html);
                    }
                }
                //
            }
            $html = ob_get_clean();
            
            return $html;
        }
        //
        
        public static function get_total_notifics_arr($user_id, $viewed_only = false) {
            global $jobsearch_plugin_options;
            $user_is_candidate = jobsearch_user_is_candidate($user_id);
            $user_is_employer = jobsearch_user_is_employer($user_id);
            if ($user_is_candidate) {
                $candidate_id = jobsearch_get_user_candidate_id($user_id);

                $notifics_candto_newjob = isset($jobsearch_plugin_options['notifics_candto_newjob']) ? $jobsearch_plugin_options['notifics_candto_newjob'] : '';
                $notifics_cand_shrtforinter = isset($jobsearch_plugin_options['notifics_cand_shrtforinter']) ? $jobsearch_plugin_options['notifics_cand_shrtforinter'] : '';
                $notifics_cand_rejctforinter = isset($jobsearch_plugin_options['notifics_cand_rejctforinter']) ? $jobsearch_plugin_options['notifics_cand_rejctforinter'] : '';

                $notifics_me_newjob = get_post_meta($candidate_id, 'jobsearch_field_notific_newjobpost', true);
                $notifics_me_shrtforinter = get_post_meta($candidate_id, 'jobsearch_field_notific_shortforinter', true);
                $notifics_me_rejctforinter = get_post_meta($candidate_id, 'jobsearch_field_notific_rejctforinter', true);

                $rem_newpost_notifics = false;
                if ($notifics_candto_newjob == 'on' && $notifics_me_newjob == 'yes') {
                    $rem_newpost_notifics = true;
                }
                $rem_shrtforjob_notifics = false;
                if ($notifics_cand_shrtforinter == 'on' && $notifics_me_shrtforinter == 'yes') {
                    $rem_shrtforjob_notifics = true;
                }
                $rem_rejctforjob_notifics = false;
                if ($notifics_cand_rejctforinter == 'on' && $notifics_me_rejctforinter == 'yes') {
                    $rem_rejctforjob_notifics = true;
                }
                
                $this_clas_obj = new Jobsearch_Dashboard_Notifications();
                $notifics_list = $this_clas_obj->get_cand_notifics($candidate_id);
                
                if (!empty($notifics_list)) {
                    $notifics_list_arr = array();
                    foreach ($notifics_list as $notifics_item) {
                        $is_viewed = isset($notifics_item['viewed']) ? $notifics_item['viewed'] : '';
                        if ($is_viewed != 0 && $viewed_only === true) {
                            continue;
                        } else if ($is_viewed == 0 && $viewed_only === 'viewed_only') {
                            continue;
                        } else if (isset($notifics_item['type']) && $notifics_item['type'] == 'post_new_job' && $rem_newpost_notifics === false) {
                            continue;
                        } else if (isset($notifics_item['type']) && $notifics_item['type'] == 'shortlist_for_intrview' && $rem_shrtforjob_notifics === false) {
                            continue;
                        } else if (isset($notifics_item['type']) && $notifics_item['type'] == 'reject_for_intrview' && $rem_rejctforjob_notifics === false) {
                            continue;
                        } else {
                            $notifics_list_arr[] = $notifics_item;
                        }
                    }
                    usort($notifics_list_arr, function (array $a, array $b) {
                        return $b["viewed"] - $a["viewed"];
                    });
                    return $notifics_list_arr;
                }
            } else if ($user_is_employer) {
                $employer_id = jobsearch_get_user_employer_id($user_id);

                $notifics_empto_applyjob = isset($jobsearch_plugin_options['notifics_empto_applyjob']) ? $jobsearch_plugin_options['notifics_empto_applyjob'] : '';

                $notifics_me_applyjob = get_post_meta($employer_id, 'jobsearch_field_notific_onaplyjob', true);

                $rem_aplyjob_notifics = false;
                if ($notifics_empto_applyjob == 'on' && $notifics_me_applyjob == 'yes') {
                    $rem_aplyjob_notifics = true;
                }
                
                $this_clas_obj = new Jobsearch_Dashboard_Notifications();
                $notifics_list = $this_clas_obj->get_emp_notifics($employer_id);
                if (!empty($notifics_list)) {
                    $notifics_list_arr = array();
                    foreach ($notifics_list as $notifics_item) {
                        $is_viewed = isset($notifics_item['viewed']) ? $notifics_item['viewed'] : '';
                        if ($is_viewed != 0 && $viewed_only === true) {
                            continue;
                        } else if ($is_viewed == 0 && $viewed_only === 'viewed_only') {
                            continue;
                        } else if (isset($notifics_item['type']) && $notifics_item['type'] == 'on_apply_job' && $rem_aplyjob_notifics === false) {
                            continue;
                        } else {
                            $notifics_list_arr[] = $notifics_item;
                        }
                    }
                    usort($notifics_list_arr, function (array $a, array $b) {
                        return $b["viewed"] - $a["viewed"];
                    });
                    return $notifics_list_arr;
                }
            }
        }

        public function close_notific_item_check() {
            $item_id = isset($_POST['notific_id']) ? $_POST['notific_id'] : '';
            $user_id = get_current_user_id();
            $user_is_candidate = jobsearch_user_is_candidate($user_id);
            $user_is_employer = jobsearch_user_is_employer($user_id);
            
            if ($user_is_candidate) {
                $candidate_id = jobsearch_get_user_candidate_id($user_id);
                $notifics_list = $this->get_cand_notifics($candidate_id);
            }
            if ($user_is_employer) {
                $employer_id = jobsearch_get_user_employer_id($user_id);
                $notifics_list = $this->get_emp_notifics($employer_id);
            }
            if (!empty($notifics_list)) {
                $noti_new_list = array();
                foreach ($notifics_list as $notific_item) {
                    $notific_unique_id = isset($notific_item['unique_id']) ? $notific_item['unique_id'] : '';
                    if ($notific_unique_id == $item_id) {
                        continue;
                    } else {
                        $noti_new_list[] = $notific_item;
                    }
                }
                if ($user_is_employer) {
                    update_post_meta($employer_id, 'jobsearch_emp_notifics_list', $noti_new_list);
                } else {
                    update_post_meta($candidate_id, 'jobsearch_cand_notifics_list', $noti_new_list);
                }
                $notifics_list_arr = self::get_total_notifics_arr($user_id, true);

                $notifics_count = !empty($notifics_list_arr) && is_array($notifics_list_arr) ? count($notifics_list_arr) : 0;

                echo json_encode(array('close' => '1', 'count' => $notifics_count));
                die;
            }
            echo json_encode(array('close' => '0', 'count' => ''));
            die;
        }

        public function chekunchk_notific_setin() {
            $notific_val = isset($_POST['notific_val']) ? $_POST['notific_val'] : '';
            $notific_type = isset($_POST['notific_type']) ? $_POST['notific_type'] : '';
            $user_id = get_current_user_id();
            $user_is_employer = jobsearch_user_is_employer($user_id);
            
            if ($user_is_employer) {
                $member_id = jobsearch_get_user_employer_id($user_id);
            } else {
                $member_id = jobsearch_get_user_candidate_id($user_id);
            }
            if ($notific_type != '') {
                $meta_key = 'jobsearch_field_' . $notific_type;
                update_post_meta($member_id, $meta_key, $notific_val);
            }
            
            echo json_encode(array('update' => 1));
            die;
        }
        
        public function make_settings_after_register($user_id, $user_role) {
            if ($user_role == 'jobsearch_employer') {
                $employer_id = jobsearch_get_user_employer_id($user_id);
                update_post_meta($employer_id, 'jobsearch_field_notific_onaplyjob', 'yes');
            } else if ($user_role == 'jobsearch_candidate') {
                $candidate_id = jobsearch_get_user_candidate_id($user_id);
                update_post_meta($candidate_id, 'jobsearch_field_notific_newjobpost', 'yes');
                update_post_meta($candidate_id, 'jobsearch_field_notific_shortforinter', 'yes');
                update_post_meta($candidate_id, 'jobsearch_field_notific_rejctforinter', 'yes');
            }
        }
    }

    return new Jobsearch_Dashboard_Notifications();
}
