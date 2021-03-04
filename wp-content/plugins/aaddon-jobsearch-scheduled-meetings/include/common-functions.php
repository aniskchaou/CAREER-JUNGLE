<?php
use WP_Jobsearch\Package_Limits;

// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}

// functions class
class JobSearch_Sched_Meets_Functions {

    public function __construct() {
        
        add_filter('redux/options/jobsearch_plugin_options/sections', array($this, 'plugin_option_fields'));
        
        add_filter('employer_dash_apps_acts_list_after_download_link', array($this, 'dash_apps_acts_create_meeting'), 15, 4);
        add_filter('employer_dash_apps_acts_grid_after_btns', array($this, 'dash_apps_acts_create_meeting_grid'), 15, 3);

        add_filter('jobsearch_cand_dash_menu_in_opts', array($this, 'canddash_menu_items_inopts_arr'), 10, 1);
        add_filter('jobsearch_cand_dash_menu_in_opts_swch', array($this, 'canddash_menu_items_inopts_swch_arr'), 10, 1);
        add_filter('jobsearch_cand_menudash_link_my_meetings_item', array($this, 'canddash_menu_items_in_fmenu'), 10, 5);

        add_filter('jobsearch_emp_dash_menu_in_opts', array($this, 'empdash_menu_items_inopts_arr'), 10, 1);
        add_filter('jobsearch_emp_dash_menu_in_opts_swch', array($this, 'empdash_menu_items_inopts_swch_arr'), 10, 1);
        add_filter('jobsearch_emp_menudash_link_my_meetings_item', array($this, 'empdash_menu_items_in_fmenu'), 10, 5);

        add_filter('jobsearch_dashboard_tab_content_ext', array($this, 'dashboard_tab_content_add'), 10, 2);

        add_filter('wp_footer', array($this, 'dashboard_footer'));
    }
    
    public function plugin_option_fields($sections) {

        $sections[] = array(
            'title' => __('Meetings Settings', 'jobsearch-shmeets'),
            'id' => 'sched-meetings-settings',
            'desc' => '',
            'icon' => 'el el-facetime-video',
            'fields' => apply_filters('jobsearch_schedmeets_settoptions_fields', array(
                array(
                    'id' => 'dashmenu_meetings_switch',
                    'type' => 'button_set',
                    'title' => __('Meetings', 'jobsearch-shmeets'),
                    'subtitle' => __('Switch On/Off Schedule Meetings.', 'jobsearch-shmeets'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'jobsearch-shmeets'),
                        'off' => __('Off', 'jobsearch-shmeets'),
                    ),
                    'default' => 'on',
                ),
                array(
                    'id' => 'zoom_meetings_switch',
                    'type' => 'button_set',
                    'title' => __('Zoom Meetings', 'jobsearch-shmeets'),
                    'subtitle' => __('Switch On/Off Zoom Meetings.', 'jobsearch-shmeets'),
                    'desc' => '',
                    'options' => array(
                        'on' => __('On', 'jobsearch-shmeets'),
                        'off' => __('Off', 'jobsearch-shmeets'),
                    ),
                    'default' => 'on',
                ),
            )),
        );
        return $sections;
    }

    public function canddash_menu_items_inopts_arr($opts_arr = array()) {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $my_meetings_switch = isset($jobsearch__options['dashmenu_meetings_switch']) ? $jobsearch__options['dashmenu_meetings_switch'] : '';

        if ($my_meetings_switch == 'on') {
            $opts_arr['my_meetings'] = __('Meetings', 'jobsearch-shmeets');
        }

        return $opts_arr;
    }

    public function canddash_menu_items_inopts_swch_arr($opts_arr = array()) {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $my_meetings_switch = isset($jobsearch__options['dashmenu_meetings_switch']) ? $jobsearch__options['dashmenu_meetings_switch'] : '';

        if ($my_meetings_switch == 'on') {
            $opts_arr['my_meetings'] = true;
        }

        return $opts_arr;
    }

    public function canddash_menu_items_in_fmenu($opts_item = '', $cand_menu_item, $get_tab, $page_url, $candidate_id) {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $my_meetings_switch = isset($jobsearch__options['dashmenu_meetings_switch']) ? $jobsearch__options['dashmenu_meetings_switch'] : '';
        
        $user_pkg_limits = new Package_Limits;

        if ($my_meetings_switch == 'on') {
            $dashmenu_links_cand = isset($jobsearch__options['cand_dashbord_menu']) ? $jobsearch__options['cand_dashbord_menu'] : '';
            $dashmenu_links_cand = apply_filters('jobsearch_cand_dashbord_menu_items_arr', $dashmenu_links_cand);
            ob_start();
            $link_item_switch = isset($dashmenu_links_cand['my_meetings']) ? $dashmenu_links_cand['my_meetings'] : '';
            if ($cand_menu_item == 'my_meetings' && $link_item_switch == '1') {
                ?>
                <li<?php echo($get_tab == 'meetings' ? ' class="active"' : '') ?>>
                    <?php
                    if ($user_pkg_limits::cand_field_is_locked('dashtab_fields|my_meetings')) {
                        echo($user_pkg_limits::dashtab_locked_html('meetings', 'jobsearch-icon jobsearch-alarm', esc_html__('Meetings', 'wp-jobsearch')));
                    } else {
                        ?>
                        <a href="<?php echo add_query_arg(array('tab' => 'meetings'), $page_url) ?>">
                            <i class="jobsearch-icon jobsearch-alarm"></i>
                            <?php esc_html_e('Meetings', 'jobsearch-shmeets') ?>
                        </a>
                        <?php
                    }
                    ?>
                </li>
                <?php
            }
            $opts_item .= ob_get_clean();
        }

        return $opts_item;
    }

    public function empdash_menu_items_inopts_arr($opts_arr = array()) {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $my_meetings_switch = isset($jobsearch__options['dashmenu_meetings_switch']) ? $jobsearch__options['dashmenu_meetings_switch'] : '';

        if ($my_meetings_switch == 'on') {
            $opts_arr['my_meetings'] = __('Meetings', 'jobsearch-shmeets');
        }

        return $opts_arr;
    }

    public function empdash_menu_items_inopts_swch_arr($opts_arr = array()) {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $my_meetings_switch = isset($jobsearch__options['dashmenu_meetings_switch']) ? $jobsearch__options['dashmenu_meetings_switch'] : '';

        if ($my_meetings_switch == 'on') {
            $opts_arr['my_meetings'] = true;
        }

        return $opts_arr;
    }

    public function empdash_menu_items_in_fmenu($opts_item = '', $emp_menu_item, $get_tab, $page_url, $employer_id) {
        $jobsearch__options = get_option('jobsearch_plugin_options');
        $my_meetings_switch = isset($jobsearch__options['dashmenu_meetings_switch']) ? $jobsearch__options['dashmenu_meetings_switch'] : '';
        
        $user_pkg_limits = new Package_Limits;

        if ($my_meetings_switch == 'on') {
            $dashmenu_links_emp = isset($jobsearch__options['emp_dashbord_menu']) ? $jobsearch__options['emp_dashbord_menu'] : '';
            $dashmenu_links_emp = apply_filters('jobsearch_emp_dashbord_menu_items_arr', $dashmenu_links_emp);
            ob_start();
            $link_item_switch = isset($dashmenu_links_emp['my_meetings']) ? $dashmenu_links_emp['my_meetings'] : '';
            if ($emp_menu_item == 'my_meetings' && $link_item_switch == '1') {
                ?>
                <li<?php echo($get_tab == 'meetings' ? ' class="active"' : '') ?>>
                    <?php
                    if ($user_pkg_limits::emp_field_is_locked('dashtab_fields|my_meetings')) {
                        echo($user_pkg_limits::dashtab_locked_html('meetings', 'jobsearch-icon jobsearch-alarm', esc_html__('Meetings', 'wp-jobsearch')));
                    } else {
                        ?>
                        <a href="<?php echo add_query_arg(array('tab' => 'meetings'), $page_url) ?>">
                            <i class="jobsearch-icon jobsearch-alarm"></i>
                            <?php esc_html_e('Meetings', 'jobsearch-shmeets') ?>
                        </a>
                        <?php
                    }
                    ?>
                </li>
                <?php
            }
            $opts_item .= ob_get_clean();
        }

        return $opts_item;
    }

    public function dashboard_tab_content_add($html = '', $get_tab = '') {
        global $jobsearch_plugin_options;
        $my_meetings_switch = isset($jobsearch_plugin_options['dashmenu_meetings_switch']) ? $jobsearch_plugin_options['dashmenu_meetings_switch'] : '';
        
        $user_dashboard_page = isset($jobsearch_plugin_options['user-dashboard-template-page']) ? $jobsearch_plugin_options['user-dashboard-template-page'] : '';
        $page_id = jobsearch__get_post_id($user_dashboard_page, 'page');
        $page_url = jobsearch_wpml_lang_page_permalink($page_id, 'page');
        
        $user_pkg_limits = new Package_Limits;

        $user_id = get_current_user_id();
        $is_employer = jobsearch_user_is_employer($user_id);
        $is_candidate = jobsearch_user_is_candidate($user_id);

        $dashmenu_links_cand = isset($jobsearch_plugin_options['cand_dashbord_menu']) ? $jobsearch_plugin_options['cand_dashbord_menu'] : '';
        $dashmenu_links_emp = isset($jobsearch_plugin_options['emp_dashbord_menu']) ? $jobsearch_plugin_options['emp_dashbord_menu'] : '';

        $meetings_view = isset($_GET['meetings_view']) && $_GET['meetings_view'] != '' ? $_GET['meetings_view'] : 'list';
        
        if ($my_meetings_switch == 'on' && $get_tab == 'meetings' && $is_candidate && isset($dashmenu_links_cand['my_meetings']) && $dashmenu_links_cand['my_meetings'] == '1' && !$user_pkg_limits::cand_field_is_locked('dashtab_fields|my_meetings')) {
            $candidate_id = jobsearch_get_user_candidate_id($user_id);
            if (isset($_GET['meetings_view']) && $_GET['meetings_view'] == 'calendar') {
                update_post_meta($candidate_id, 'jobsearch_user_meetings_view', 'calendar');
            } else if (isset($_GET['meetings_view'])) {
                update_post_meta($candidate_id, 'jobsearch_user_meetings_view', 'list');
            }
            $saved_meets_view = get_post_meta($candidate_id, 'jobsearch_user_meetings_view', true);
            if ($saved_meets_view != '') {
                $meetings_view = $saved_meets_view;
            }
            ob_start();
            ?>
            <div class="jobsearch-employer-box-section">
                <div class="jobsearch-profile-title jobsearch-meeting-tabtitle">
                    <h2><?php esc_html_e('Interview Schedule Meetings', 'jobsearch-shmeets') ?></h2>
                    <div class="meetview-icons-con">
                        <a href="<?php echo add_query_arg(array('tab' => 'meetings', 'meetings_view' => 'list'), $page_url) ?>" class="meetview-list<?php echo ($meetings_view == 'list' ? ' active-view' : '') ?>"><i class="fa fa-list"></i></a>
                        <a href="<?php echo add_query_arg(array('tab' => 'meetings', 'meetings_view' => 'calendar'), $page_url) ?>" class="meetview-calendar<?php echo ($meetings_view == 'calendar' ? ' active-view' : '') ?>"><i class="fa fa-calendar"></i></a>
                    </div>
                </div>
                <?php do_action('jobsearch_shmeets_cand_dash_tab_lists') ?>
            </div>
            <?php
            $html .= ob_get_clean();
        }
        if ($my_meetings_switch == 'on' && $get_tab == 'meetings' && $is_employer && isset($dashmenu_links_emp['my_meetings']) && $dashmenu_links_emp['my_meetings'] == '1' && !$user_pkg_limits::emp_field_is_locked('dashtab_fields|my_meetings')) {
            $employer_id = jobsearch_get_user_employer_id($user_id);
            if (isset($_GET['meetings_view']) && $_GET['meetings_view'] == 'calendar') {
                update_post_meta($employer_id, 'jobsearch_user_meetings_view', 'calendar');
            } else if (isset($_GET['meetings_view'])) {
                update_post_meta($employer_id, 'jobsearch_user_meetings_view', 'list');
            }
            $saved_meets_view = get_post_meta($employer_id, 'jobsearch_user_meetings_view', true);
            if ($saved_meets_view != '') {
                $meetings_view = $saved_meets_view;
            }
            ob_start();
            ?>
            <div class="jobsearch-employer-box-section">
                <div class="jobsearch-profile-title jobsearch-meeting-tabtitle">
                    <h2><?php esc_html_e('Meetings', 'jobsearch-shmeets') ?></h2>
                    <div class="meetview-icons-con">
                        <a href="<?php echo add_query_arg(array('tab' => 'meetings', 'meetings_view' => 'list'), $page_url) ?>" class="meetview-list<?php echo ($meetings_view == 'list' ? ' active-view' : '') ?>"><i class="fa fa-list"></i></a>
                        <a href="<?php echo add_query_arg(array('tab' => 'meetings', 'meetings_view' => 'calendar'), $page_url) ?>" class="meetview-calendar<?php echo ($meetings_view == 'calendar' ? ' active-view' : '') ?>"><i class="fa fa-calendar"></i></a>
                    </div>
                    <div class="meetins-settinbtn-con">
                        <a href="javascript:void(0);" class="meetview-settinbtn meetview-tosettinsbtn"><?php esc_html_e('Meeting Settings', 'jobsearch-shmeets') ?></a>
                        <a href="javascript:void(0);" class="meetview-settinbtn meetview-backlistsbtn" style="display: none;"><?php esc_html_e('Meetings List', 'jobsearch-shmeets') ?></a>
                    </div>
                </div>
                <?php do_action('jobsearch_shmeets_emp_dash_tab_lists') ?>
            </div>
            <?php
            $html .= ob_get_clean();
        }
        echo $html;
    }
    
    public function dash_apps_acts_create_meeting($html, $candidate_id, $job_id, $view = 'list') {
        if ($view == 'list') {
            global $jobsearch_plugin_options;
            $my_meetings_switch = isset($jobsearch_plugin_options['dashmenu_meetings_switch']) ? $jobsearch_plugin_options['dashmenu_meetings_switch'] : '';
            $get_tab = isset($_GET['tab']) ? $_GET['tab'] : '';
            if ($my_meetings_switch == 'on' && ($get_tab == 'manage-jobs' || $get_tab == 'all-applicants')) {
                ob_start();
                ?>
                <li><a href="javascript:void(0);" class="jobsearch-creatmeetin-btn" data-id="<?php echo ($job_id) ?>" data-cand="<?php echo ($candidate_id) ?>"><?php esc_html_e('Create Meeting', 'jobsearch-shmeets') ?></a></li>
                <?php
                $html .= ob_get_clean();
            }
        }
        return $html;
    }
    
    public function dash_apps_acts_create_meeting_grid($html, $candidate_id, $job_id) {
        global $jobsearch_plugin_options;
        $my_meetings_switch = isset($jobsearch_plugin_options['dashmenu_meetings_switch']) ? $jobsearch_plugin_options['dashmenu_meetings_switch'] : '';
        $get_tab = isset($_GET['tab']) ? $_GET['tab'] : '';
        if ($my_meetings_switch == 'on' && ($get_tab == 'manage-jobs' || $get_tab == 'all-applicants')) {
            ob_start();
            ?>
            <li class="create-meetin-li"><a href="javascript:void(0);" class="jobsearch-creatmeetin-btn" data-id="<?php echo ($job_id) ?>" data-cand="<?php echo ($candidate_id) ?>"><?php esc_html_e('Create Meeting', 'jobsearch-shmeets') ?></a></li>
            <?php
            $html .= ob_get_clean();
        }
        return $html;
    }
    
    public function dashboard_footer() {
        global $jobsearch_plugin_options;
        $my_meetings_switch = isset($jobsearch_plugin_options['dashmenu_meetings_switch']) ? $jobsearch_plugin_options['dashmenu_meetings_switch'] : '';
        $zoom_meetings_switch = isset($jobsearch_plugin_options['zoom_meetings_switch']) ? $jobsearch_plugin_options['zoom_meetings_switch'] : '';
        
        $user_id = get_current_user_id();
        $is_employer = jobsearch_user_is_employer($user_id);
        $is_candidate = jobsearch_user_is_candidate($user_id);
        $get_tab = isset($_GET['tab']) ? $_GET['tab'] : '';
        
        $current_date = strtotime(current_time('d-m-Y'));
        
        if ($my_meetings_switch == 'on' && $is_employer && ($get_tab == 'manage-jobs' || $get_tab == 'all-applicants')) {
            wp_enqueue_script('jobsearch-shmeets-scripts');
            wp_enqueue_script('shmeets-angular-js');
            wp_enqueue_script('shmeets-moment-with-locales');
            wp_enqueue_script('shmeets-moment-timezone');
            wp_enqueue_script('shmeets-dateselecter-js');
            
            $employer_id = jobsearch_get_user_employer_id($user_id);
            ?>
            <div class="jobsearch-modal schedd-creatmeet-popup fade" id="JobSearchSchedMeetingCreate">
                <div class="modal-inner-area">&nbsp;</div>
                <div class="modal-content-area">
                    <div class="modal-box-area">
                        <span class="modal-close"><i class="fa fa-times"></i></span>
                        <div class="jobsearch-creatmeet-form">
                            <form id="sched-creatmeetin-form" data-eid="">
                                <div id="meetcalendr-shedule-desct" class="jobsearch-meetinsched-calendr">
                                    <div ng-app="jobsearchShMeetingApp" class="jobsearch-shcedule-meetcal-section">
                                        <div ng-controller="jobsearchShMeetingController" class="calender-availdate-holdr">
                                            <multiple-date-picker ng-model="availArrayOfDates" moment="moment"></multiple-date-picker>
                                        </div>
                                    </div>
                                    <input type="hidden" name="meeting_date" class="jobsearch-meetime-datefield" value="">
                                </div>
                                <div class="jobsearch-user-form">
                                    <ul class="meet-fields-ulcon">
                                        <li>
                                            <label><?php esc_html_e('Time Duration', 'jobsearch-shmeets') ?></label>
                                            <input type="text" name="meeting_duration" class="meetime-slot-duration" value="30">
                                            <em><?php esc_html_e('Put meeting time duration here (in minutes)', 'jobsearch-shmeets') ?></em>
                                        </li>
                                        <li>
                                            <label><?php esc_html_e('Select Timing', 'jobsearch-shmeets') ?></label>
                                            <div class="jobsearch-meetime-slotscon jobsearch-profile-select">
                                                <select name="meeting_time" class="selectize-select">
                                                    <option value=""><?php esc_html_e('Timing', 'jobsearch-shmeets') ?></option>
                                                    <?php //do_action('jobsearch_meetin_avail_time_slots', $current_date, 30) ?>
                                                </select>
                                            </div>
                                        </li>
                                        <li>
                                            <label><?php esc_html_e('Schedule Message/Notes', 'jobsearch-shmeets') ?></label>
                                            <div class="form-textarea">
                                                <textarea name="meeting_note"></textarea>
                                            </div>
                                        </li>
                                        <li class="form-submitbtn-con">
                                            <input type="hidden" name="meeting_with_cand" value="">
                                            <input type="hidden" name="meeting_obj_id" value="">
                                            <input type="hidden" name="action" value="jobsearch_create_new_job_meeting_sched">
                                            <a href="javascript:void(0);" class="create-meetin-callbtn"><?php esc_html_e('Create Meeting', 'jobsearch-shmeets') ?></a>
                                            <?php
                                            $zoom_meetins_switch = get_post_meta($employer_id, 'jobsearch_zoom_meetins_switch', true);
                                            $zoom_refresh_token = get_post_meta($employer_id, 'jobsearch_zoom_refresh_token', true);
                                            if ($zoom_meetings_switch == 'on' && $zoom_meetins_switch == 'on' && $zoom_refresh_token != '') {
                                                ?>
                                                <a href="javascript:void(0);" class="create-meetin-callbtn create-zoomeetin-btn"><?php esc_html_e('Create Zoom Meeting', 'jobsearch-shmeets') ?></a>
                                                <?php
                                            }
                                            ?>
                                        </li>
                                    </ul>
                                    <div class="meet-sched-msg" style="display: none;"></div>
                                </div>
                                <div class="meet-sched-loder" style="display: none;"><i class="fa fa-refresh fa-spin"></i></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        if ($my_meetings_switch == 'on' && ($is_employer || $is_candidate) && ($get_tab == 'meetings')) {
            wp_enqueue_script('jobsearch-shmeets-scripts');
            wp_enqueue_script('shmeets-angular-js');
            wp_enqueue_script('shmeets-moment-with-locales');
            wp_enqueue_script('shmeets-moment-timezone');
            wp_enqueue_script('shmeets-dateselecter-js');
            ?>
            <div class="jobsearch-modal schedd-creatmeet-popup fade" id="JobSearchSchedMeetingReSched">
                <div class="modal-inner-area">&nbsp;</div>
                <div class="modal-content-area">
                    <div class="modal-box-area">
                        <span class="modal-close"><i class="fa fa-times"></i></span>
                        <div class="jobsearch-creatmeet-form">
                            <form id="sched-updatmeetin-form" data-eid="">
                                <div id="meetcalendr-reshedule-desct" class="jobsearch-meetinsched-calendr">
                                    <div ng-app="jobsearchReShMeetingApp" class="jobsearch-shcedule-meetcal-section">
                                        <div ng-controller="jobsearchReShMeetingController" class="calender-availdate-holdr">
                                            <multiple-date-picker ng-model="availArrayOfDates" moment="moment"></multiple-date-picker>
                                        </div>
                                    </div>
                                    <input type="hidden" name="meeting_date" class="jobsearch-meetime-datefield" value="">
                                </div>
                                <div class="jobsearch-user-form">
                                    <ul class="meet-fields-ulcon">
                                        <li>
                                            <label><?php esc_html_e('Time Duration', 'jobsearch-shmeets') ?></label>
                                            <input type="text" name="meeting_duration" class="meetime-slot-duration" value="30">
                                            <em><?php esc_html_e('Put meeting time duration here (in minutes)', 'jobsearch-shmeets') ?></em>
                                        </li>
                                        <li>
                                            <label><?php esc_html_e('Select Timing', 'jobsearch-shmeets') ?></label>
                                            <div class="jobsearch-meetime-slotscon jobsearch-profile-select">
                                                <select name="meeting_time" class="selectize-select">
                                                    <option value=""><?php esc_html_e('Timing', 'jobsearch-shmeets') ?></option>
                                                    <?php 
                                                    if ($is_employer) {
                                                        //do_action('jobsearch_meetin_avail_time_slots', $current_date, 30);
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </li>
                                        <li>
                                            <label><?php esc_html_e('Re-Schedule Message/Notes', 'jobsearch-shmeets') ?></label>
                                            <div class="form-textarea">
                                                <textarea name="meeting_note"></textarea>
                                            </div>
                                        </li>
                                        <li class="form-submitbtn-con">
                                            <?php
                                            if ($is_employer) {
                                                ?>
                                                <input type="hidden" name="meeting_with_cand" value="">
                                                <?php
                                            } else {
                                                ?>
                                                <input type="hidden" name="meeting_with_emp" value="">
                                                <?php
                                            }
                                            ?>
                                            <input type="hidden" name="meeting_obj_id" value="">
                                            <input type="hidden" name="meeting_id" value="">
                                            <input type="hidden" name="action" value="jobsearch_resched_new_job_meeting_sched">
                                            <a href="javascript:void(0);" class="create-meetin-callbtn"><?php esc_html_e('Re-Schedule Meeting', 'jobsearch-shmeets') ?></a>
                                        </li>
                                    </ul>
                                    <div class="meet-sched-msg" style="display: none;"></div>
                                </div>
                                <div class="meet-sched-loder" style="display: none;"><i class="fa fa-refresh fa-spin"></i></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }

}

return new JobSearch_Sched_Meets_Functions();
